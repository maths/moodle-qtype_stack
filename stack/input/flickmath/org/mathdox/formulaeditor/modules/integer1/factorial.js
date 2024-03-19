
$identify("org/mathdox/formulaeditor/modules/integer1/factorial.js");

$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/presentation/Superscript.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/semantics/Keyword.js");

$main(function(){

  var mathmlSymbol= [ "", "", ""];
  
  if ("" !== "") {
    mathmlSymbol[0] = "<mo></mo>";
  }
  if ("!" !== "") {
    mathmlSymbol[2] = "<mo>!</mo>";
  }

  var symbol =  {
    onscreen : ["","","!"],
    openmath : "<OMS cd='integer1' name='factorial'/>",
    mathml   : mathmlSymbol
  };

  /**
   * Defines a semantic tree node that represents a unary minus.
   */
  org.mathdox.formulaeditor.semantics.Integer1Factorial =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {
      

      symbol : {
        onscreen : symbol.onscreen,
        openmath : symbol.openmath,
        mathml   : mathmlSymbol
      },

      precedence : 140

    });

  /**
   * Extend the OpenMathParser object with parsing code for arith1.unary_minus.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

      /**
      * Returns a unary minus object based on the OpenMath node.
      */
      handleInteger1Factorial : function(node) {

        var operand = this.handle(node.childNodes.item(1));
        return new org.mathdox.formulaeditor.semantics.Integer1Factorial(operand);

      }

    });

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["integer1__factorial"] = new org.mathdox.formulaeditor.semantics.Keyword("integer1", "factorial", symbol, "unary");

  /**
   * Add the parsing code for unary symbol.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  var rulesEnter = [];
  var positionEnter = 0;
  if ("" !== "") {
    rulesEnter.push(pG.literal(""));
    positionEnter++;
  }
  rulesEnter.push(pG.rule("expression150"));
  if ("!" !== "") {
    rulesEnter.push(pG.literal("!"));
  }

  if (( ""  === ""  ) &&
      ( "!" === "!" )) {
    // only one expression, same on screen
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

        // expression140 = integer1factorial | super.expression140
        expression140 : function() {
          var parent = arguments.callee.parent;
          pG.alternation(
            pG.rule("integer1factorial"),
            parent.expression140).apply(this, arguments);
        },

        // integer1factorial = "" expression150 "!"
        integer1factorial :
          pG.transform(
            pG.concatenation.apply(pG, rulesEnter),
            function(result) {
              return new semantics.Integer1Factorial(result[positionEnter]);
            }
          )
      };
    });
  } else { // allow alternative as displayed on the screen
    var rulesScreen = [];
    var positionScreen = 0;
    if ("" !== "") {
      rulesScreen.push(pG.literal(""));
      positionScreen++;
    }
    rulesScreen.push(pG.rule("expression150"));
    if ("!" !== "") {
      rulesScreen.push(pG.literal("!"));
    }
  
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {

        // expression140 = integer1factorial | super.expression140
        expression140 : function() {
          var parent = arguments.callee.parent;
          pG.alternation(
            pG.rule("integer1factorial"),
            pG.rule("integer1factorialalt"),
            parent.expression140).apply(this, arguments);
        },

        // integer1factorial = "" expression150 "!"
        integer1factorial :
          pG.transform(
            pG.concatenation.apply(pG, rulesEnter),
            function(result) {
              return new semantics.Integer1Factorial(result[positionEnter]);
            }
          ),

        // integer1factorialalt = "" expression150 "!"
        integer1factorialalt :
          pG.transform(
            pG.concatenation.apply(pG, rulesScreen),
            function(result) {
              return new semantics.Integer1Factorial(result[positionScreen]);
            }
          )
       };
     });
   }

});
