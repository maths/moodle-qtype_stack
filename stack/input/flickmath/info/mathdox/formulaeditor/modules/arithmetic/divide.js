$identify("org/mathdox/formulaeditor/modules/arithmetic/divide.js");

$require("org/mathdox/formulaeditor/modules/arith1/plus.js");
$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/presentation/Fraction.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");

$main(function(){

  /**
   * Defines a semantic tree node that represents a division.
   */
  org.mathdox.formulaeditor.semantics.Divide =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      symbol : {
        openmath : "<OMS cd='arith1' name='divide'/>"
      },

      style: "mfrac",

      //precedence : 160,
      precedence : 170,

      getPresentation : function(context) {
        var presentation = org.mathdox.formulaeditor.presentation;

        return new presentation.Fraction(
          new presentation.Row(this.operands[0].getPresentation(context)),
          new presentation.Row(this.operands[1].getPresentation(context))
        );

      },

      getSymbolOpenMath : function() {
        var options = new org.mathdox.formulaeditor.Options();
        var result;
        if (options.getVerboseStyleOption() == "true") {
          var arr = this.symbol.openmath.split("/");
          result = arr.join(" style='" + this.style  + "'/");
        } else {
          result = this.symbol.openmath;
        }
        return result;
      },

      getMathML : function() {
        return "<mfrac>" +
          this.operands[0].getMathML() +
          this.operands[1].getMathML() +
          "</mfrac>";
      }

  });
  
  org.mathdox.formulaeditor.semantics.DivideInline =
    $extend(org.mathdox.formulaeditor.semantics.Divide, {
      //precedence : 130,
      precedence : 140,

      style: "colon",
      
      symbol : {
        mathml: "<mo>:</mo>",
	onscreen: ":",
        openmath : "<OMS cd='arith1' name='divide'/>"
      },

      getMathML : function() {
	// parent = Divide, parent.parent is MultaryOperation
	// use the default MultaryOperation method
        return arguments.callee.parent.getMathML.parent.getMathML.call(this);
      },

      getPresentation : function() {
	// parent = Divide, parent.parent is MultaryOperation
	// use the default MultaryOperation method
        return arguments.callee.parent.getPresentation.parent.getPresentation.call(this, arguments);
      }

  });


  /**
  * Extend the OpenMathParser object with parsing code for arith1.divide.
  */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

      /**
      * Returns a Divide object based on the OpenMath node.
      */
      handleArith1Divide : function(node) {

        // parse the left and right operands
        var children = node.childNodes;
        var left  = this.handle(children.item(1));
        var right = this.handle(children.item(2));

        // construct a divide object
	var result;

	if (node.getAttribute("style") == "colon") {
          result = new org.mathdox.formulaeditor.semantics.DivideInline(left, right);
	} else {
          result = new org.mathdox.formulaeditor.semantics.Divide(left, right);
	}

        return result;
      }
  });

  /**
   * Add the parsing code for division.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( function(context) { 
    return {

      // expression130 = divide_inline | super.expression130
      expression130 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("divide_inline"),
          parent.expression130).apply(this, arguments);
      },

      // expression160 = divide | divide_silent_addition | super.expression160
      expression160 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("divide"),
          pG.rule("divide_silent_addition"),
          parent.expression160).apply(this, arguments);
      },

      // divide = never
      divide : pG.never,

      // divide_inline = expression130 ":" expression140
      divide_inline :
        pG.transform(
          pG.concatenation(
            pG.rule("expression130"),
            pG.literal(':'),
            pG.rule("expression140")
          ),
          function(result) {
            return new semantics.DivideInline(result[0], result[2]);
          }
        ),

      divide_silent_addition : 
        pG.transform(
          pG.concatenation(
            pG.rule("integer"),
            pG.rule("divide")),
          function(result) {
            var semantics = org.mathdox.formulaeditor.semantics; 
            var plus = new semantics.Arith1Plus(result[0],result[1]);
            plus.style="invisible";
            return plus;
          }
        ),

      // parseNumber = divide | divide_silent_addition | parseNumber
      parseNumber : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("divide"),
          pG.rule("divide_silent_addition"),
          parent.parseNumber).apply(this, arguments);
      }
    };
  });


  /**
   * Add a key handler for the '/' and '%' keys.
   */
  org.mathdox.formulaeditor.presentation.Row =
    $extend(org.mathdox.formulaeditor.presentation.Row, {

      /**
       * Override the onkeypress method to handle the '/' key.
       */
      onkeypress : function(event, editor) {

        // only handle keypresses where alt and ctrl are not held
        if (!event.altKey && !event.ctrlKey) {

          // check whether the '/' key has been pressed
          if (String.fromCharCode(event.charCode) == "/") {

            var Fraction = org.mathdox.formulaeditor.presentation.Fraction;
            var index    = editor.cursor.position.index;
            var length   = this.children.length;

	    // search for an expression of precedence level 130 (or 150 if
	    // restricted) to the left of the cursor, and of level 150 to the
	    // right of the cursor
	    var leftexpr;

	    if (editor.getExpressionParsingContext().optionArith1DivideMode == 'restricted') {
	      leftexpr = "expression150";
	    } else { // 'normal'
	      leftexpr = "expression130";
	    }
	    var parsedleft = this.getSemantics(editor.getExpressionParsingContext(), 0, index, leftexpr, true);
            var parsedright = this.getSemantics(editor.getExpressionParsingContext(), index, length, "expression150");

            // create the left and right operands of the fraction
            var right = this.remove(index, parsedright.index);
            var left  = this.remove(parsedleft.index,  index);

            // insert the fraction into the row
            this.insert(parsedleft.index, new Fraction(left, right));
            editor.cursor.position = right.getFollowingCursorPosition();

            // update the editor state
            editor.redraw();
            editor.save();
            return false;

          } else if (String.fromCharCode(event.charCode) == "%") {
            var presentation = org.mathdox.formulaeditor.presentation;
            var index    = editor.cursor.position.index;

            // create the fraction
            var fraction = new presentation.Fraction(
              new presentation.Row(new presentation.BlockSymbol()),
              new presentation.Row(new presentation.BlockSymbol()));

            // insert the fraction into the row
            this.insert(index, fraction);

            // move the cursor into the fraction
            editor.cursor.moveRight();
            
            // update the editor state
            editor.redraw();
            editor.save();
            return false;
          }

        }

        // call the overridden method
        return arguments.callee.parent.onkeypress.call(this, event, editor);

      }

    });
    
});
