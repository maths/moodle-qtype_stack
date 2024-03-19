
$identify("org/mathdox/formulaeditor/modules/set1/in.js");

$require("org/mathdox/parsing/ParserGenerator.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/semantics/Keyword.js");
$require("org/mathdox/formulaeditor/parsing/openmath/KeywordList.js");

$main(function(){

  var symbol = {
    onscreen : "∈",
    openmath : null, // use default with model:cd and model:name
    mathml   : "<mo>∈</mo>"
  };

  /**
   * Define a semantic tree node that represents set1.in.
   */
  org.mathdox.formulaeditor.semantics.Set1In =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      symbol : {

        onscreen : symbol.onscreen,
        openmath : "<OMS cd='set1' name='in'/>",
        mathml   : symbol.mathml

      },

      associative : true,
      precedence : 110

    });
  
  /**
   * Extend the OpenMathParser object with parsing code for set1.in.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

    /**
     * Returns an equality object based on the OpenMath node.
     */
    handleSet1In : function(node) {

      // parse the children of the OMA
      var children = node.childNodes;
      var operands = [];
      for (var i=1; i<children.length; i++) {
        operands.push(this.handle(children.item(i)));
      }

      // construct the corresponding object
      var result = new org.mathdox.formulaeditor.semantics.Set1In();
      result.initialize.apply(result, operands);

      return result;

    }

  });

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["set1__in"] = new org.mathdox.formulaeditor.semantics.Keyword("set1", "in", symbol, "infix");

  /**
   * Add the parsing code for an infix-once symbol.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  if ( "∈" == "∈" ) {
    // only one expression, same on screen
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

      // expression110 = in | super.expression110
      expression110 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("set1in"),
          parent.expression110).apply(this, arguments);
      },

      // set1in = 
      //    expression110 "∈" expression120
      set1in : function() {
          var parent = arguments.callee.parent;
          return pG.transform(
            pG.concatenation(
              pG.rule("expression110"),
              pG.literal("∈"),
              pG.rule("expression120")
            ),
            function(result) {
              return parent.infix_Update(new semantics.Set1In(result[0], result[2]));
            }
          ).apply(this, arguments);
        }
      };
    });
  } else { // allow alternative as displayed on the screen
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

      // expression110 = set1in | 
      //   super.expression110
      expression110 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("set1in"),
          parent.expression110).apply(this, arguments);
      },

      // set1in = 
      //    expression110 ("∈"|"∈") expression120
      set1in : function() {
          var parent = arguments.callee.parent;
          return pG.transform(
            pG.concatenation(
              pG.rule("expression110"),
  	    pG.alternation(
  	      pG.literal("∈"),
  	      pG.literal("∈")
  	    ),
              pG.rule("expression120")
            ),
            function(result) {
              return parent.infix_Update(new semantics.Set1In(result[0], result[2]));
            }
          ).apply(this, arguments);
        }
      };
    });
  }
});
