$identify("org/mathdox/formulaeditor/modules/permutation1/permutation.js");

$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/semantics/MultaryListOperation.js");

$main(function(){

  /**
   * Defines a semantic tree node that represents an absolute value.
   */
  org.mathdox.formulaeditor.semantics.Cycle =
    $extend(org.mathdox.formulaeditor.semantics.MultaryListOperation, {

      symbol : {

        mathml   : ["<mo>(</mo>","<mo>,</mo>","<mo>)</mo>"],
        onscreen : ["(",",",")"],
        openmath : "<OMS cd='permutation1' name='cycle'/>"

      },

      precedence : 0

    });

  /**
   * Defines a semantic tree node that represents an absolute value.
   */
  org.mathdox.formulaeditor.semantics.Permutation =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      symbol : {

        mathml   : ["","",""],
        onscreen : ["","",""],
        openmath : "<OMS cd='permutation1' name='permutation'/>"

      },

      precedence : 0

    });

  /**
   * Extend the OpenMathParser object with parsing code for arith1.abs.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

      /**
      * Returns a cycle object based on the OpenMath node.
      */
      handlePermutation1Cycle : function(node) {
        var operands = [];
        var result;
        var i;

        for (i=1; i<node.childNodes.length; i++) {
          operands.push(this.handle(node.childNodes.item(i)));
        }
        result = new org.mathdox.formulaeditor.semantics.Cycle();
        result.initialize.apply(result, operands);

        return result;
      }, 
      
      /**
      * Returns a permutation object based on the OpenMath node.
      */
      handlePermutation1Permutation : function(node) {
        var operands = [];
        var result;
        var i;

        for (i=1; i<node.childNodes.length; i++) {
          operands.push(this.handle(node.childNodes.item(i)));
        }
        result = new org.mathdox.formulaeditor.semantics.Permutation();
        result.initialize.apply(result, operands);

        return result;

      }

    });

  /**
   * Add the parsing code for permutations.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
    function(context) { return {

        // expression160 = permutation | super.expression160
        expression160 : function() {
          var parent = arguments.callee.parent;
          pG.alternation(
            pG.rule("permutation"),
            parent.expression160).apply(this, arguments);
        },

        // permutation = "(" (omString | integer) ("," (omString | integer))+ ")"
        permutation :
          pG.transform(
            pG.repetitionplus(
              pG.concatenation(
                pG.literal("("),
                pG.alternation(
                  pG.rule("integer"),
                  pG.rule("omString")
                ),
                pG.repetitionplus(
                  pG.concatenation(
                    pG.literal(context.listSeparator),
                    pG.alternation(
                      pG.rule("integer"),
                      pG.rule("omString")
                    )
                  )
                ),
                pG.literal(")")
              )
            ),
            function(result) {
              var cycles = [];
              var entries;
              var i=0;
              var perm;
              while (i<result.length) {
                // result[i] = '('
                i++;

                entries = [];
                while (i<result.length && result[i] != ')') {
                  entries.push(result[i]);
                  i++;
                  // result[i] = ')' or ','
                  if (i<result.length && result[i] == context.listSeparator) {
                    i++;
                  }
                  // result[i] = ')' or result[i] = <entry>
                }
                // result[i] == ')' 
                i++;
                
                var cycle = new semantics.Cycle();
                cycle.initialize.apply(cycle, entries);
                cycles.push(cycle);
              }

              perm = new semantics.Permutation();
              perm.initialize.apply(perm, cycles);
              return perm;
            }
          )
        };
      });

});
