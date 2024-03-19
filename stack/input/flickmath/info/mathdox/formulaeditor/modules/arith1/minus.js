
$identify("org/mathdox/formulaeditor/modules/arith1/minus.js");

$require("org/mathdox/parsing/ParserGenerator.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/semantics/Keyword.js");
$require("org/mathdox/formulaeditor/parsing/openmath/KeywordList.js");

$main(function(){

  var symbol = {
    onscreen : "-",
    openmath : null, // use default with model:cd and model:name
    mathml   : "<mo>-</mo>"
  };

  /**
   * Define a semantic tree node that represents arith1.minus.
   */
  org.mathdox.formulaeditor.semantics.Arith1Minus =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      symbol : {

        onscreen : symbol.onscreen,
        openmath : "<OMS cd='arith1' name='minus'/>",
        mathml   : symbol.mathml

      },

      associative : false,
      precedence : 120

    });
  
  /**
   * Extend the OpenMathParser object with parsing code for arith1.minus.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

    /**
     * Returns an equality object based on the OpenMath node.
     */
    handleArith1Minus : function(node) {

      // parse the children of the OMA
      var children = node.childNodes;
      var operands = [];
      for (var i=1; i<children.length; i++) {
        operands.push(this.handle(children.item(i)));
      }

      // construct the corresponding object
      var result = new org.mathdox.formulaeditor.semantics.Arith1Minus();
      result.initialize.apply(result, operands);

      return result;

    }

  });

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["arith1__minus"] = new org.mathdox.formulaeditor.semantics.Keyword("arith1", "minus", symbol, "infix");

  /**
   * Add the parsing code for an infix-once symbol.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  if ( "-" == "-" ) {
    // only one expression, same on screen
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

      // expression120 = minus | super.expression120
      expression120 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("arith1minus"),
          parent.expression120).apply(this, arguments);
      },

      // arith1minus = 
      //    expression120 "-" expression130
      arith1minus : function() {
          var parent = arguments.callee.parent;
          return pG.transform(
            pG.concatenation(
              pG.rule("expression120"),
              pG.literal("-"),
              pG.rule("expression130")
            ),
            function(result) {
              return parent.infix_Update(new semantics.Arith1Minus(result[0], result[2]));
            }
          ).apply(this, arguments);
        }
      };
    });
  } else { // allow alternative as displayed on the screen
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

      // expression120 = arith1minus | 
      //   super.expression120
      expression120 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("arith1minus"),
          parent.expression120).apply(this, arguments);
      },

      // arith1minus = 
      //    expression120 ("-"|"-") expression130
      arith1minus : function() {
          var parent = arguments.callee.parent;
          return pG.transform(
            pG.concatenation(
              pG.rule("expression120"),
  	    pG.alternation(
  	      pG.literal("-"),
  	      pG.literal("-")
  	    ),
              pG.rule("expression130")
            ),
            function(result) {
              return parent.infix_Update(new semantics.Arith1Minus(result[0], result[2]));
            }
          ).apply(this, arguments);
        }
      };
    });
  }
});
