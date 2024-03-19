$identify("org/mathdox/formulaeditor/modules/arithmetic/abs.js");

$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");

$main(function(){

  /**
   * Defines a semantic tree node that represents an absolute value.
   */
  org.mathdox.formulaeditor.semantics.Abs =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      symbol : {

        mathml   : ["<mo>|</mo>","","<mo>|</mo>"],
        onscreen : ["|","","|"],
        openmath : "<OMS cd='arith1' name='abs'/>"

      },

      precedence : 0

    });

  /**
   * Extend the OpenMathParser object with parsing code for arith1.abs.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

      /**
      * Returns an absolute value object based on the OpenMath node.
      */
      handleArith1Abs : function(node) {

        var operand = this.handle(node.childNodes.item(1));
        return new org.mathdox.formulaeditor.semantics.Abs(operand);

      }

    });

  /**
   * Add parsing code for absolute values.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
    function(context) { return {

        // expression160 = abs | super.expression160
        expression160 : function() {
          var parent = arguments.callee.parent;
          pG.alternation(
            pG.rule("abs"),
            parent.expression160).apply(this, arguments);
        },

        // abs = "|" expression "|"
        abs :
          pG.transform(
            pG.concatenation(
              pG.literal("|"),
              pG.rule("expression"),
              pG.literal("|")
            ),
            function(result) {
              return new semantics.Abs(result[1]);
            }
          )
        };
      });

});
