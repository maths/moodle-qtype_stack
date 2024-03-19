$identify("org/mathdox/formulaeditor/modules/interval1/interval.js");

$require("org/mathdox/formulaeditor/semantics/MultaryListOperation.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");

$main(function(){

  /**
   * Defines a semantic tree node that represents an interval.
   */
  org.mathdox.formulaeditor.semantics.Interval =
    $extend(org.mathdox.formulaeditor.semantics.MultaryListOperation, {

      // operand 0 is lower bound
      // operand 1 is upper bound

      symbol : {

        onscreen : ["[",",","]"],
        openmath : "<OMS cd='interval1' name='interval'/>"

      }

    });

  /**
   * Extend the OpenMathParser object with parsing code for
   * interval1.interval
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

      /**
       * Returns an Interval object based on the OpenMath node.
       */
      handleInterval1Interval : function(node) {

        var children = node.childNodes;
        var lower = this.handle(children.item(1));
        var upper = this.handle(children.item(2));

        return new org.mathdox.formulaeditor.semantics.Interval(lower, upper);

      }

    });
  

});
