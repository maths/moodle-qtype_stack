$identify("org/mathdox/formulaeditor/modules/arithmetic/root.js");

$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/KeywordList.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/presentation/Root.js");
$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");

$main(function(){

  var semantics = org.mathdox.formulaeditor.semantics;

  /**
   * Defines a semantic tree node that represents a root.
   */
  semantics.Arith1Root =
    $extend(semantics.MultaryOperation, {

      argcount : 2,

      symbol : {

        openmath : "<OMS cd='arith1' name='root'/>"

      },

      precedence : 160,

      getPresentation : function(context) {

        var presentation = org.mathdox.formulaeditor.presentation;

        return new presentation.Root(
          this.operands[0].getPresentation(context),
          this.operands[1].getPresentation(context)
        );
      },

      getMathML : function() {
	var base = this.operands[0];
	var index = this.operands[1];

	// TODO: also generate msqrt if index is integer, value 2
	if (index instanceof semantics.Integer && index && index.value == 2) {
	  // note: inferred mrow, but we might produce one
          return "<msqrt>" + base.getMathML() + "</msqrt>";
	} else {
          return "<mroot>" + base.getMathML() + index.getMathML() + "</mroot>";
	}
      }

  });

  /**
  * Extend the OpenMathParser object with parsing code for arith1.divide.
  */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

      /**
      * Returns a Root object based on the OpenMath node.
      */
      handleArith1Root : function(node) {

        // parse the left and right operands
        var children = node.childNodes;
        var middle  = this.handle(children.item(1));
        var base  = this.handle(children.item(2));

        // construct a root object
        return new semantics.Arith1Root(middle, base);

      }

  });

  /**
   * Add the parsing code for division.
   */
  var pG = new org.mathdox.parsing.ParserGenerator();

  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
    function(context) { return {

      // expression160 = root | super.expression160
      expression160 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("root"),
          parent.expression160).apply(this, arguments);
      },

      // root = never
      root : pG.never
    };
  });

  org.mathdox.formulaeditor.parsing.expression.KeywordList.rt = {
    parseResultFun : function(oper, array) {
      var semantics = org.mathdox.formulaeditor.semantics;
      var root = new semantics.Arith1Root();
      root.initialize.apply(root, array);

      return root;
    }
  };

  org.mathdox.formulaeditor.parsing.expression.KeywordList.sqrt = {
    parseResultFun : function(oper, array) {
      var semantics = org.mathdox.formulaeditor.semantics;
      var root = new semantics.Arith1Root();
      array.push(new semantics.Integer(2));

      root.initialize.apply(root, array);

      return root;
    }
  };

    org.mathdox.formulaeditor.parsing.expression.KeywordList.rt;


});
