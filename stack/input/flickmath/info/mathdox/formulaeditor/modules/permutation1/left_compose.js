
$identify("org/mathdox/formulaeditor/modules/permutation1/left_compose.js");

$require("org/mathdox/parsing/ParserGenerator.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/semantics/Keyword.js");
$require("org/mathdox/formulaeditor/parsing/openmath/KeywordList.js");

$main(function(){

  var symbol = {
    onscreen : "∘",
    openmath : null, // use default with model:cd and model:name
    mathml   : "<mo>∘</mo>"
  };

  /**
   * Define a semantic tree node that represents permutation1.left_compose.
   */
  org.mathdox.formulaeditor.semantics.Permutation1Left_compose =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      symbol : {

        onscreen : symbol.onscreen,
        openmath : "<OMS cd='permutation1' name='left_compose'/>",
        mathml   : symbol.mathml

      },

      associative : true,
      precedence : 130

    });
  
  /**
   * Extend the OpenMathParser object with parsing code for permutation1.left_compose.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

    /**
     * Returns an equality object based on the OpenMath node.
     */
    handlePermutation1Left_compose : function(node) {

      // parse the children of the OMA
      var children = node.childNodes;
      var operands = [];
      for (var i=1; i<children.length; i++) {
        operands.push(this.handle(children.item(i)));
      }

      // construct the corresponding object
      var result = new org.mathdox.formulaeditor.semantics.Permutation1Left_compose();
      result.initialize.apply(result, operands);

      return result;

    }

  });

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["permutation1__left_compose"] = new org.mathdox.formulaeditor.semantics.Keyword("permutation1", "left_compose", symbol, "infix");

  /**
   * Add the parsing code for an infix-once symbol.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  if ( "∘" == "∘" ) {
    // only one expression, same on screen
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

      // expression130 = left_compose | super.expression130
      expression130 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("permutation1left_compose"),
          parent.expression130).apply(this, arguments);
      },

      // permutation1left_compose = 
      //    expression130 "∘" expression140
      permutation1left_compose : function() {
          var parent = arguments.callee.parent;
          return pG.transform(
            pG.concatenation(
              pG.rule("expression130"),
              pG.literal("∘"),
              pG.rule("expression140")
            ),
            function(result) {
              return parent.infix_Update(new semantics.Permutation1Left_compose(result[0], result[2]));
            }
          ).apply(this, arguments);
        }
      };
    });
  } else { // allow alternative as displayed on the screen
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

      // expression130 = permutation1left_compose | 
      //   super.expression130
      expression130 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("permutation1left_compose"),
          parent.expression130).apply(this, arguments);
      },

      // permutation1left_compose = 
      //    expression130 ("∘"|"∘") expression140
      permutation1left_compose : function() {
          var parent = arguments.callee.parent;
          return pG.transform(
            pG.concatenation(
              pG.rule("expression130"),
  	    pG.alternation(
  	      pG.literal("∘"),
  	      pG.literal("∘")
  	    ),
              pG.rule("expression140")
            ),
            function(result) {
              return parent.infix_Update(new semantics.Permutation1Left_compose(result[0], result[2]));
            }
          ).apply(this, arguments);
        }
      };
    });
  }
});
