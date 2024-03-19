$identify("org/mathdox/formulaeditor/modules/nums1/rational.js");

$require("org/mathdox/formulaeditor/modules/arithmetic/divide.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");

$main(function(){

  /**
  * Extend the OpenMathParser object with parsing code for arith1.divide.
  */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

      /**
      * Returns a Divide object based on the OpenMath node.
      */
      handleNums1Rational : function(node) {
	return this.handleArith1Divide(node);
      }
  });

});
