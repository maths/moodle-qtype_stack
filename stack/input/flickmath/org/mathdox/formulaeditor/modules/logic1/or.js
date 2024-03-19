
$identify("org/mathdox/formulaeditor/modules/logic1/or.js");

$require("org/mathdox/parsing/ParserGenerator.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/semantics/Keyword.js");

$main(function(){

  var symbol = {
    onscreen         : "∨",
    openmath         : null, // use default with model:cd and model:name
    mathml           : "<mo>∨</mo>",
    mathml_invisible : ( "" != "" ? "<mo></mo>" : null )
  };

  /**
   * Define a semantic tree node that represents logic1.or.
   */
  org.mathdox.formulaeditor.semantics.Logic1Or =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      symbol : {

        onscreen         : symbol.onscreen,
        openmath         : "<OMS cd='logic1' name='or'/>",
        mathml           : symbol.mathml,
        mathml_invisible : symbol.mathml_invisible

      },

      precedence : 90

    });
  
  /**
   * Extend the OpenMathParser object with parsing code for logic1.or.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

    /**
     * Returns an equality object based on the OpenMath node.
     */
    handleLogic1Or : function(node, style) {

      // parse the children of the OMA
      var children = node.childNodes;
      var operands = [];
      for (var i=1; i<children.length; i++) {
        operands.push(this.handle(children.item(i)));
      }

      // construct the corresponding object
      var result = new org.mathdox.formulaeditor.semantics.Logic1Or();
      result.initialize.apply(result, operands);

      if (style == "invisible") {
        result.style = style;
      }

      return result;
    }

  });

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["logic1__or"] = new org.mathdox.formulaeditor.semantics.Keyword("logic1", "or", symbol, "infix");

  /**
   * Add the parsing code for an infix sign.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  if ( "||" == "∨" ) {
    // only one expression, same on screen
  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
    function(context) { return {

      // expression90 = or | super.expression90
      expression90 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("logic1or"),
          parent.expression90).apply(this, arguments);
      },

      // logic1or = 
      //    expression90 "||" expression100
      logic1or :
        pG.transform(
          pG.concatenation(
            pG.rule("expression90"),
            pG.literal("||"),
            pG.rule("expression100"),
            pG.repetition(
              pG.concatenation(
                pG.literal("||"),
                pG.rule("expression100")
              )
            )
          ),
          function(result) {
            var retval = new semantics.Logic1Or();
            var operands = [];
            var i;

	    // if the operator is the same rewrite it
	    // except if the style is invisible 
            if (result[0] instanceof semantics.Logic1Or && result[0].style!="invisible" && result[0].inside_braces !== true ) {
              for (i=0; i<result[0].operands.length;i++) {
	        operands.push(result[0].operands[i]);
              }
	    } else {
	      operands.push(result[0]);
            }

	    for (i=1; 2*i<result.length; i++) {
              operands.push(result[2*i]);
            }
            retval.operands = operands;

            return retval;
          }
        )
      };
    });
  } else { // allow alternative as displayed on the screen
  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
    function(context) { return {

      // expression90 = logic1or | 
      //   logic1oralt | super.expression90
      expression90 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("logic1or"),
          parent.expression90).apply(this, arguments);
      },

      // logic1or = 
      //    expression90 "||" expression100
      logic1or :
        pG.transform(
          pG.concatenation(
            pG.rule("expression90"),
	    pG.alternation(
	      pG.literal("||"),
	      pG.literal("∨")
	    ),
            pG.rule("expression100"),
            pG.repetition(
              pG.concatenation(
	        pG.alternation(
	          pG.literal("||"),
	          pG.literal("∨")
	        ),
                pG.rule("expression100")
              )
            )
          ),
          function(result) {
            var retval = new semantics.Logic1Or();
            var operands = [];
            var i;

            if (result[0] instanceof semantics.Logic1Or) {
              for (i=0; i<result[0].operands.length;i++) {
	        operands.push(result[0].operands[i]);
              }
	    } else {
	      operands.push(result[0]);
            }

	    for (i=1; 2*i<result.length; i++) {
              operands.push(result[2*i]);
            }
            retval.operands = operands;

            return retval;
          }
        )
      };
    });
  }
});
