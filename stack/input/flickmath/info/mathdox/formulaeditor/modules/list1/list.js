$identify("org/mathdox/formulaeditor/modules/list1/list.js");

$require("org/mathdox/formulaeditor/semantics/MultaryListOperation.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");

$main(function(){

  /**
   * Defines a semantic tree node that represents an absolute value.
   */
  org.mathdox.formulaeditor.semantics.List1List =
    $extend(org.mathdox.formulaeditor.semantics.MultaryListOperation, {

      symbol : {

        mathml   : ["<mo>{</mo>","<mo>,</mo>","<mo>}</mo>"],
        onscreen : ["{",",","}"],
        openmath : "<OMS cd='list1' name='list'/>"

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
      handleList1List : function(node) {

        // parse the children of the OMA
        var children = node.childNodes;
        var operands = [];
        for (var i=1; i<children.length; i++) {
          operands.push(this.handle(children.item(i)));
        }

        // construct a List1List object
        var result = new org.mathdox.formulaeditor.semantics.List1List();
        result.initialize.apply(result, operands);
        return result;

      }

    });

  /**
   * Add the parsing code for lists.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
    function(context) { return {

      // expression160 = list | super.expression160
      expression160 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("list"),
          parent.expression160).apply(this, arguments);
      },

      // abs = "{" expression "}"
      list :
        pG.transform(
          pG.concatenation(
            pG.literal("{"),
            pG.rule("expression"),
            pG.repetition(
              pG.concatenation(
                pG.literal(context.listSeparator),
                pG.rule("expression")
              )
            ),
            pG.literal("}")
          ),
          function(result) {
            var array = [];
            for (var i=1; i+1<result.length; i=i+2) {
              array.push(result[i]);
            }
            var list = new semantics.List1List();
            list.initialize.apply(list, array);
            return list;
          }
        )
      };
    });

});
