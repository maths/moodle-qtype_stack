$package("org.mathdox.formulaeditor.modules.calculus1");

$identify("org/mathdox/formulaeditor/modules/calculus1/defint.js");

$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/presentation/Row.js");
$require("org/mathdox/formulaeditor/presentation/Column.js");
$require("org/mathdox/formulaeditor/presentation/Symbol.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/modules/relation1/eq.js");
$require("org/mathdox/formulaeditor/modules/interval1/interval.js");
$require("org/mathdox/formulaeditor/modules/fns1/lambda.js");

$main(function(){

  /**
   * Defines a semantic tree node that represents a definite integration.
   */
  org.mathdox.formulaeditor.semantics.Defint =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      // operand 0 : interval
      // operand 1 : lambda expression
    
      getPresentation : function(context) {
      
        var presentation = org.mathdox.formulaeditor.presentation;
        
        return new presentation.Row(
          new presentation.Defint(
            new presentation.Row(
              this.operands[0].operands[0].getPresentation(context)),
            new presentation.Row(
              this.operands[0].operands[1].getPresentation(context))
          ),
          this.operands[1].expression.getPresentation(context),
          // U+2146 Differential d
          new presentation.Symbol("ⅆ"),
          this.operands[1].variables[0].getPresentation(context)
        );
      
      },
      
      getMathML : function() {
      	// U+222B integral 
        return "<mrow><msubsup><mo>∫</mo>" +
          // below: lower boundry
          this.operands[0].operands[0].getMathML() +
          // above: higher boundry
          this.operands[0].operands[1].getMathML() +
          "</msubsup>"+
          this.operands[1].expression.getMathML() +
          "<mo>ⅆ</mo>"+
          this.operands[1].variables[0].getMathML() +
	  "</mrow>";
      },

      getOpenMath : function() {
      
        return "<OMA>" +
          "<OMS cd='calculus1' name='defint'/>" +
          this.operands[0].getOpenMath() +
          this.operands[1].getOpenMath() +
        "</OMA>";
      
      }
    
    });

  /**
   * Defines an on-screen (definite) integral.
   */
  org.mathdox.formulaeditor.presentation.Defint =
    $extend(org.mathdox.formulaeditor.presentation.Column, {

      /**
       * top and bottom rows are smaller
       */
      fontSizeModifierArray : [-1,0,-1],

      initialize : function(below, above) {

        var parent = arguments.callee.parent;
        // U+222B integral
        var defint  = new org.mathdox.formulaeditor.presentation.Symbol("∫");
        return parent.initialize.call(this, above, defint, below);

      },

      copy : function() {
        var above = this.children[0];
        var below = this.children[2];
        return this.clone(below.copy(), above.copy());
      },

      getSemantics : function(context) {

        var above = this.children[0].getSemantics(context).value;
        var below = this.children[2].getSemantics(context).value;

        return {
          value : [below, above],
          rule  : "defint"
        };

      }

  });

  /**
   * Extend the OpenMathParser object with parsing code for calculus1.defint.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

      /**
       * Returns a Sum object based on the OpenMath node.
       */
      handleCalculus1Defint : function(node) {

        var children = node.childNodes;
        var interval = this.handle(children.item(1));
        var lambda   = this.handle(children.item(2));

	if (lambda === null || lambda.variables.length === 0) {
	  alert("calculus1.defint needs a nonempty OMBVAR");
	  return null;
	}

        return new org.mathdox.formulaeditor.semantics.Defint(interval, lambda);

      }

    });


  /**
   * Add the parsing code for definite integrals.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

      // expression150 = defint expression 'd' variable | super.expression150
      expression150 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.transform(
            pG.concatenation(
              pG.rule("defint"),
              pG.rule("expression"),
	      // U+2146 differential d
              pG.literal("ⅆ"),
              pG.rule("variable")
            ),
            function(result) {

              return new semantics.Defint(
                new semantics.Interval(result[0][0], result[0][1]),
                new semantics.Lambda(result[3], result[1])
              );

            }
          ),
          parent.expression150).apply(this, arguments);
      },

      // defint = never
      defint : pG.never,
      calculus1defint_partial: 
        pG.transform(
          pG.concatenation(
            pG.rule("defint"),
            pG.rule("expression")
          ),
          function(result) {
            // just return the expression
            // return value should probably not be used anyway
            return result[0];
          }
        )
    };
  });

  /**
   * Add a key handler for the 'd' key.
   */
  org.mathdox.formulaeditor.presentation.Row =
    $extend(org.mathdox.formulaeditor.presentation.Row, {

      /**
       * Override the onkeypress method to handle the 'd' key.
       */
      onkeypress : function(event, editor) {

        // only handle keypresses where alt and ctrl are not held
        if (!event.altKey && !event.ctrlKey) {

          // check whether the 'd' key has been pressed
          if (String.fromCharCode(event.charCode) == "d") {

            // search for a partial integral expression to the left of
            // the cursor
            var index = editor.cursor.position.index;
            var parsedleft = this.getSemantics(editor.getExpressionParsingContext(), 0, index, 
              "calculus1defint_partial", true);

            if (parsedleft.value !== null || parsedleft.index > 0) {
              // found a partial calculus1int expression
              // U+2146 differential d
              var presentation = org.mathdox.formulaeditor.presentation;
              this.insert(index, new presentation.Symbol("ⅆ"));
              editor.cursor.moveRight();
            
              // update the editor state
              editor.redraw();
              editor.save();
              return false;
            }
          }

        }

        // call the overridden method
        return arguments.callee.parent.onkeypress.call(this, event, editor);

      }

    });


});
