
$identify("org/mathdox/formulaeditor/modules/relation1/leq.js");

$require("org/mathdox/parsing/ParserGenerator.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/semantics/Keyword.js");
$require("org/mathdox/formulaeditor/parsing/openmath/KeywordList.js");

$main(function(){

  var symbol = {
    onscreen : "≤",
    openmath : null, // use default with model:cd and model:name
    mathml   : "<mo>≤</mo>"
  };

  /**
   * Define a semantic tree node that represents relation1.leq.
   */
  org.mathdox.formulaeditor.semantics.Relation1Leq =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      symbol : {

        onscreen : symbol.onscreen,
        openmath : "<OMS cd='relation1' name='leq'/>",
        mathml   : symbol.mathml

      },

      associative : true,
      precedence : 110

    });
  
  /**
   * Extend the OpenMathParser object with parsing code for relation1.leq.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

    /**
     * Returns an equality object based on the OpenMath node.
     */
    handleRelation1Leq : function(node) {

      // parse the children of the OMA
      var children = node.childNodes;
      var operands = [];
      for (var i=1; i<children.length; i++) {
        operands.push(this.handle(children.item(i)));
      }

      // construct the corresponding object
      var result = new org.mathdox.formulaeditor.semantics.Relation1Leq();
      result.initialize.apply(result, operands);

      return result;

    }

  });

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["relation1__leq"] = new org.mathdox.formulaeditor.semantics.Keyword("relation1", "leq", symbol, "infix");

  /**
   * Add the parsing code for an infix-once symbol.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  if ( "<=" == "≤" ) {
    // only one expression, same on screen
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

      // expression110 = leq | super.expression110
      expression110 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("relation1leq"),
          parent.expression110).apply(this, arguments);
      },

      // relation1leq = 
      //    expression110 "<=" expression120
      relation1leq : function() {
          var parent = arguments.callee.parent;
          return pG.transform(
            pG.concatenation(
              pG.rule("expression110"),
              pG.literal("<="),
              pG.rule("expression120")
            ),
            function(result) {
              return parent.infix_Update(new semantics.Relation1Leq(result[0], result[2]));
            }
          ).apply(this, arguments);
        }
      };
    });
  } else { // allow alternative as displayed on the screen
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

      // expression110 = relation1leq | 
      //   super.expression110
      expression110 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("relation1leq"),
          parent.expression110).apply(this, arguments);
      },

      // relation1leq = 
      //    expression110 ("<="|"≤") expression120
      relation1leq : function() {
          var parent = arguments.callee.parent;
          return pG.transform(
            pG.concatenation(
              pG.rule("expression110"),
  	    pG.alternation(
  	      pG.literal("<="),
  	      pG.literal("≤")
  	    ),
              pG.rule("expression120")
            ),
            function(result) {
              return parent.infix_Update(new semantics.Relation1Leq(result[0], result[2]));
            }
          ).apply(this, arguments);
        }
      };
    });
  }
});
