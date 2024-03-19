$identify("org/mathdox/formulaeditor/modules/fns1/lambda.js");

$require("org/mathdox/formulaeditor/semantics/Lambda.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/KeywordList.js");

$main(function(){

  /**
   * Extend the OpenMathParser object with parsing code for fns1.lambda.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

      /**
      * Returns an absolute value object based on the OpenMath node.
      */
      handleFns1Lambda : function(node) {

        // parse the children of the OMBIND
        var children = node.childNodes;

        // children.item(0) is OMS: fns1.lambda
        // children.item(1) is OMBVAR
        // children.item(2) is the expression

        if (children.length < 3) {
          // not enough arguments
          alert("parsing OpenMath fns1.lambda: not enough arguments");
          return null; 
        }
        var ombvarNode = children.item(1);
        if (ombvarNode.nodeType != 1) { // ELEMENT node
          alert("parsing OpenMath fns1.lambda: could not find OMBVAR node ");
          return null;
        }
        var ombvarChildren = ombvarNode.childNodes;
        var variables = [];
        var i; // counter
        for (i=0; i<ombvarChildren.length; i++) {
          variables.push(this.handle(ombvarChildren.item(i)));
        }

        var expressionNode = children.item(2);
        if (children.item(2).nodeType != 1) { // ELEMENT node
          alert("parsing OpenMath fns1.lambda: could not find expression node");
          return null;
        }
        var expression = this.handle(expressionNode);

        // construct a List1List object
        var semantics = org.mathdox.formulaeditor.semantics;
        var lambda = new semantics.Lambda(variables, expression);

        return lambda;
      }

    });


  /**
   * Add the parsing code for lambda functions.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( function(context) {
    return {

      // expression160 = list | super.expression160
      expression160 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("fns1lambda"),
          parent.expression160).apply(this, arguments);
      },

      // U+03BB greek small letter lamda
      // lambda = "λ" ( variable | "(" variable ( "," variable )* ")" ) "."
      // ( variable | "(" expression ")" )
      fns1lambda :
        pG.transform(
          pG.concatenation(
            // U+03BB greek small letter lamda
            pG.literal("λ"),
            pG.alternation(
              pG.rule("variable"),
              pG.concatenation(
                pG.literal("("),
                pG.rule("variable"),
                pG.repetition(
                  pG.concatenation(
                    pG.literal(","),
                    pG.rule("variable")
                  )
                ),
                pG.literal(")")
              )
            ),
            pG.literal("."),
            pG.alternation(
              pG.rule("variable"),
              pG.concatenation(
                pG.literal("("),
                pG.rule("expression"),
                pG.literal(")")
              )
            )
          ),
          function(result) {
            var semantics = org.mathdox.formulaeditor.semantics;

            // result[0] = lambda

            // parse variables
            var variables = [];

            var i = 1;

            if (i<result.length && result[i] != "(") {
              // only one variable
              variables.push(result[i]);
              
              i++;
              // result[i] = "."
            } else {
              // multiple variables, result[i] == "("
              i++;
              // result[i] = ")" or result[i] = variable
              while (i<result.length && result[i] != ")") {
                variables.push(result[i]);
                i++;
                // result[i] = "," or result[i] = ")"
                if (i<result.length && result[i] == ",") {
                  // skip comma
                  i++;
                }
                // result[i] = variable or result[i] = ")"
              }
              // result[i] = ")"
              i++;
              // result[i] = '.'
            }
            // result[i] = '.'
            i++;
            // result[i] = variable or result[i] = "("
            if (i<result.length && result[i] != "(") {
              // only one variable
              expression = result[i];
              i++;
            } else {
              // result[i] = "("
              i++;
              expression = result[i];
              i++;
              // result[i] = ")"
              i++;
            }
            return new semantics.Lambda(variables,expression);
          }
        )
      };
    });

  org.mathdox.formulaeditor.parsing.expression.KeywordList.lambda = {
    parseResultFun : function(oper, array) {
      var semantics = org.mathdox.formulaeditor.semantics;
      var lambda = new semantics.Lambda();
      lambda.initialize.apply(lambda, array);

      return lambda;
    }
  };
});
