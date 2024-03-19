
$identify("org/mathdox/formulaeditor/modules/logic1/implies.js");

$require("org/mathdox/parsing/ParserGenerator.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/semantics/Keyword.js");
$require("org/mathdox/formulaeditor/parsing/openmath/KeywordList.js");

$main(function(){

  var symbol = {
    onscreen : "⇒",
    openmath : null, // use default with model:cd and model:name
    mathml   : "<mo>⇒</mo>"
  };

  /**
   * Define a semantic tree node that represents logic1.implies.
   */
  org.mathdox.formulaeditor.semantics.Logic1Implies =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      symbol : {

        onscreen : symbol.onscreen,
        openmath : "<OMS cd='logic1' name='implies'/>",
        mathml   : symbol.mathml

      },

      associative : true,
      precedence : 80

    });
  
  /**
   * Extend the OpenMathParser object with parsing code for logic1.implies.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

    /**
     * Returns an equality object based on the OpenMath node.
     */
    handleLogic1Implies : function(node) {

      // parse the children of the OMA
      var children = node.childNodes;
      var operands = [];
      for (var i=1; i<children.length; i++) {
        operands.push(this.handle(children.item(i)));
      }

      // construct the corresponding object
      var result = new org.mathdox.formulaeditor.semantics.Logic1Implies();
      result.initialize.apply(result, operands);

      return result;

    }

  });

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["logic1__implies"] = new org.mathdox.formulaeditor.semantics.Keyword("logic1", "implies", symbol, "infix");

  /**
   * Add the parsing code for an infix-once symbol.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  if ( "=>" == "⇒" ) {
    // only one expression, same on screen
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

      // expression80 = implies | super.expression80
      expression80 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("logic1implies"),
          parent.expression80).apply(this, arguments);
      },

      // logic1implies = 
      //    expression80 "=>" expression90
      logic1implies : function() {
          var parent = arguments.callee.parent;
          return pG.transform(
            pG.concatenation(
              pG.rule("expression80"),
              pG.literal("=>"),
              pG.rule("expression90")
            ),
            function(result) {
              return parent.infix_Update(new semantics.Logic1Implies(result[0], result[2]));
            }
          ).apply(this, arguments);
        }
      };
    });
  } else { // allow alternative as displayed on the screen
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

      // expression80 = logic1implies | 
      //   super.expression80
      expression80 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("logic1implies"),
          parent.expression80).apply(this, arguments);
      },

      // logic1implies = 
      //    expression80 ("=>"|"⇒") expression90
      logic1implies : function() {
          var parent = arguments.callee.parent;
          return pG.transform(
            pG.concatenation(
              pG.rule("expression80"),
  	    pG.alternation(
  	      pG.literal("=>"),
  	      pG.literal("⇒")
  	    ),
              pG.rule("expression90")
            ),
            function(result) {
              return parent.infix_Update(new semantics.Logic1Implies(result[0], result[2]));
            }
          ).apply(this, arguments);
        }
      };
    });
  }
});
