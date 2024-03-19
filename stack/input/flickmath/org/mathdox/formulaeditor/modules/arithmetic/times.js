$identify("org/mathdox/formulaeditor/modules/arithmetic/times.js");

$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/parsing/openmath/KeywordList.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/semantics/Keyword.js");
$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/modules/arithmetic/power.js");

$main(function(){
  /** 
   * describe how this symbol should be presented
   */
  var symbol = {
    // U+00B7 middle dot
    onscreen         : "·",
    openmath         : null,
    // U+00B7 middle dot
    mathml           : "<mo>·</mo>",
    // U+2062 invisible times
    mathml_invisible : "<mo>⁢</mo>"
  };

  /**
   * Defines a semantic tree node that represents a multiplication.
   */
  org.mathdox.formulaeditor.semantics.KeywordTimes =
    $extend(org.mathdox.formulaeditor.semantics.Keyword, {

      getSymbolOnscreen : function(context) {
        return context.symbolArith1Times;
      }

    });

  /**
   * Defines a semantic tree node that represents a multiplication.
   */
  org.mathdox.formulaeditor.semantics.Times =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      symbol : {

        onscreen         : symbol.onscreen,
        openmath         : "<OMS cd='arith1' name='times'/>",
        mathml           : symbol.mathml,
        mathml_invisible : symbol.mathml_invisible
      },

      getSymbolOnscreen : function(context) {
        return context.symbolArith1Times;
      },

      getSymbolMathML : function() {
        options = new org.mathdox.formulaeditor.Options();
        return "<mo>"+ options.getArith1TimesSymbol() +"</mo>";
      },

      getSymbolOpenMath : function() {
        var options = new org.mathdox.formulaeditor.Options();
	var result;
	if (options.getVerboseStyleOption() == "true") {
	  var arr = this.symbol.openmath.split("/");
          result = arr.join(" style='" + options.getArith1TimesStyle()  + "'/");
	} else {
	  result = this.symbol.openmath;
	}
        return result;
      },

      precedence : 130,
      precedence : 140

    });

  /**
   * Extend the OpenMathParser object with parsing code for arith1.times.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

      /**
       * Returns a Times object based on the OpenMath node.
       */
      handleArith1Times : function(node, style) {

        // parse the children of the OMA
        var children = node.childNodes;
        var operands = [];
        for (var i=1; i<children.length; i++) {
          operands.push(this.handle(children.item(i)));
        }

        // construct a Times object
        var result = new org.mathdox.formulaeditor.semantics.Times();
        result.initialize.apply(result, operands);
	if (style == "invisible") {
          result.style = style;
        }
        return result;

      }

    });

  /**
   * Add the parsing code for multiplication.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
    function(context) { return {

      // expression130 = times | super.expression130
      expression130 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("times"),
          parent.expression130).apply(this, arguments);
      },

      // expression150 = times | super.expression150
      expression150 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("invisibletimes"),
          parent.expression150).apply(this, arguments);
      },

      // invisibletimes = number variable
      invisibletimes:
        pG.transform(
          pG.concatenation(
            pG.rule("parseNumber"),
            pG.alternation(
              pG.rule("restrictedexpression160"),
              pG.rule("restrictedpower")
            )
          ),
          function(result) {
            var times = new semantics.Times(result[0], result[1]);
            times.style = "invisible";
            return times;
          }
        ),

      // times = expression130 "·" expression140
      times :
        pG.transform(
          pG.concatenation(
            pG.rule("expression130"),
            pG.literal(context.symbolArith1Times),
            pG.rule("expression140")
          ),
          function(result) {
            return new semantics.Times(result[0], result[2]);
          }
        )
      };
    });

  /**
   * Add a key handler for the '*' key.
   */
  org.mathdox.formulaeditor.presentation.Row =
    $extend(org.mathdox.formulaeditor.presentation.Row, {

      /**
       * Override the onkeypress method to handle the '*' key.
       */
      onkeypress : function(event, editor) {

        // only handle keypresses where alt and ctrl are not held
        if (!event.altKey && !event.ctrlKey) {

          // check whether the '*' key has been pressed
          if (String.fromCharCode(event.charCode) == "*") {

            // substitute the charCode of "·" for "*".
            var newEvent = {};
            for (var x in event) {
              newEvent[x] = event[x];
            }
            newEvent.charCode = editor.getPresentationContext().symbolArith1Times.charCodeAt(0);
            event = newEvent;

          }

        }

        // call the overridden method
        return arguments.callee.parent.onkeypress.call(this, event, editor);

      }

    });
  
  org.mathdox.formulaeditor.parsing.openmath.KeywordList["arith1__times"] = new org.mathdox.formulaeditor.semantics.KeywordTimes("arith1", "times", symbol, "infix");

});
