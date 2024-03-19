$identify("org/mathdox/formulaeditor/modules/relation1/IntervalNotation.js");

$require("org/mathdox/formulaeditor/modules/logic1/and.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");

$main(function(){
  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
    function(context) { 
      return {
	infix_Update: function(expr) {
	  var parent = arguments.callee.parent;
          var semantics = org.mathdox.formulaeditor.semantics;
          var result;
          var arg1, arg2;

          if ((expr instanceof semantics.Relation1Lt || expr instanceof semantics.Relation1Leq) &&
              (expr.operands[0] instanceof semantics.Relation1Lt || expr.operands[0] instanceof semantics.Relation1Leq)) {
            arg1 = expr.operands[0];
            console.log("1<x<2");

            if (expr instanceof semantics.Relation1Lt) {
              arg2 = new semantics.Relation1Lt(arg1.operands[1], expr.operands[1]);
            } else { //Leq
              arg2 = new semantics.Relation1Leq(arg1.operands[1], expr.operands[1]);
            }

            result = new semantics.Logic1And(arg1, arg2);
          } else if ((expr instanceof semantics.Relation1Gt || expr instanceof semantics.Relation1Geq) &&
              (expr.operands[0] instanceof semantics.Relation1Gt || expr.operands[0] instanceof semantics.Relation1Geq)) {
            arg1 = expr.operands[0];
            console.log("1>x>2");

            if (expr instanceof semantics.Relation1Gt) {
              arg2 = new semantics.Relation1Gt(arg1.operands[1], expr.operands[1]);
            } else { //Geq
              arg2 = new semantics.Relation1Geq(arg1.operands[1], expr.operands[1]);
            }

            result = new semantics.Logic1And(arg1, arg2);
          } else { // change nothing
            result = expr;
          }

          return parent.infix_Update(result);
	}
      };
    }
  );
});
