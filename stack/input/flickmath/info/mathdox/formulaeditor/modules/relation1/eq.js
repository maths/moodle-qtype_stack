
$identify("org/mathdox/formulaeditor/modules/relation1/eq.js");

$require("org/mathdox/parsing/ParserGenerator.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/semantics/Keyword.js");

$main(function(){

  var symbol = {
    onscreen : "=",
    openmath : null, // use default with cd and name from model
    mathml   : "<mo>=</mo>"
  };

  /**
   * Define a semantic tree node that represents relation1.eq.
   */
  org.mathdox.formulaeditor.semantics.Relation1Eq =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      symbol : {

        onscreen : symbol.onscreen,
        openmath : "<OMS cd='relation1' name='eq'/>",
        mathml   : symbol.mathml

      },

      precedence : 110

    });
 
  /**
   * Define a semantic tree node that represents relation2.eqs.
   */
  org.mathdox.formulaeditor.semantics.Relation2Eqs =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      symbol : {

        onscreen : symbol.onscreen,
        openmath : "<OMS cd='relation2' name='eqs'/>",
        mathml   : symbol.mathml

      },

      precedence : 110

    });
  
  /**
   * Extend the OpenMathParser object with parsing code for 
   * relation1.eq and relation2.eqs.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

    /**
     * Returns an equality object based on the OpenMath node.
     */
    handleRelation1Eq : function(node) {

      // parse the children of the OMA
      var children = node.childNodes;
      var operands = [];
      for (var i=1; i<children.length; i++) {
        operands.push(this.handle(children.item(i)));
      }

      // construct the corresponding object
      var result = new org.mathdox.formulaeditor.semantics.Relation1Eq();
      result.initialize.apply(result, operands);
      return result;

    },
    handleRelation2Eqs : function(node) {

      // parse the children of the OMA
      var children = node.childNodes;
      var operands = [];
      for (var i=1; i<children.length; i++) {
        operands.push(this.handle(children.item(i)));
      }

      // construct the corresponding object
      var result = new org.mathdox.formulaeditor.semantics.Relation2Eqs();
      result.initialize.apply(result, operands);
      return result;

    }


  });

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["relation1__eq"] = new org.mathdox.formulaeditor.semantics.Keyword("relation1", "eq", symbol, "infix");
  org.mathdox.formulaeditor.parsing.openmath.KeywordList["relation2__eqs"] = new org.mathdox.formulaeditor.semantics.Keyword("relation2", "eqs", symbol, "infix");

  /**
   * Add the parsing code for an infix-different symbol.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  if ( "=" == "=" ) {
    // only one expression, same on screen
  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
    function(context) { return {

      // expression110 = relation1eqrelation2eqs | super.expression110
      expression110 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("relation1eqrelation2eqs"),
          parent.expression110).apply(this, arguments);
      },

      // relation1eqrelation2eqs = 
      //    expression110 "=" expression120
      relation1eqrelation2eqs :
        pG.transform(
          pG.concatenation(
            pG.rule("expression110"),
            pG.literal("="),
            pG.rule("expression120"),
            pG.repetition(
              pG.concatenation(
                pG.literal("="),
                pG.rule("expression120")
              )
            )
          ),
          function(result) {
	    var retval;
            var operands = [];
            var i;

            if ((result[0] instanceof semantics.Relation1Eq) ||
                (result[0] instanceof semantics.Relation1Eq)) {

              retval = new semantics.Relation2Eqs();
              for (i=0; i<result[0].operands.length;i++) {
	        operands.push(result[0].operands[i]);
              }
	    } else {
              retval = new semantics.Relation1Eq();
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

      // expression110 = relation1eqrelation2eqs | 
      //   super.expression110
      expression110 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("relation1eqrelation2eqs"),
          parent.expression110).apply(this, arguments);
      },

      // relation1eqrelation2eqs = 
      //    expression110 "=" expression120
      relation1eqrelation2eqs :
        pG.transform(
          pG.concatenation(
            pG.rule("expression110"),
	    pG.alternation(
	      pG.literal("="),
	      pG.literal("=")
	    ),
            pG.rule("expression120"),
            pG.repetition(
              pG.concatenation(
	        pG.alternation(
	          pG.literal("="),
	          pG.literal("=")
	        ),
                pG.rule("expression120")
              )
            )
          ),
          function(result) {
            var retval;
	    if (result.length < 3){
              retval = new semantics.Relation1Eq();
	    } else {
              retval = new semantics.Relation2Eqs();
	    }
            var operands = [];
            var i;

            for (i=0; 2*i<result.length; i++) {
              operands[i] = result[2*i];
            }
            retval.operands = operands;

            return retval;
          }
        )
      };
    });
  }
});
