$identify("org/mathdox/formulaeditor/modules/limit1/limit.js");

$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/presentation/Row.js");
$require("org/mathdox/formulaeditor/presentation/Column.js");
$require("org/mathdox/formulaeditor/presentation/Symbol.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/modules/relation1/eq.js");
$require("org/mathdox/formulaeditor/modules/interval1/integer_interval.js");
$require("org/mathdox/formulaeditor/modules/fns1/lambda.js");

$main(function(){

  /**
   * Defines a semantic tree node that represents a sum.
   */
  org.mathdox.formulaeditor.semantics.Limit =
    $extend(org.mathdox.formulaeditor.semantics.MultaryOperation, {

      // operand 0 : limiting value
      // NOT USED YET operand 1 : method of approach
      // operand 2 : function
    
      getPresentation : function(context) {
      
        var presentation = org.mathdox.formulaeditor.presentation;
       
        return new presentation.Row(
          new presentation.Limit(
            new presentation.Row(
              this.operands[2].variables[0].getPresentation(context),
              // U+2192 rightwards arrow
              new presentation.Symbol("→"),
              this.operands[0].getPresentation(context)
            )
          ),
          new presentation.Symbol("("),
          this.operands[2].expression.getPresentation(context),
          new presentation.Symbol(")")
        );
      
      },
      
      getOpenMath : function() {
      
        return "<OMA>" +
          "<OMS cd='limit1' name='limit'/>" +
          this.operands[0].getOpenMath() +
          this.operands[1].getOpenMath() +
          this.operands[2].getOpenMath() +
        "</OMA>";
      
      },

      getMathML : function() {
        return "<mrow>"+
          "<munder>"+
            "<mo>lim</mo>"+
            "<mrow>"+
              this.operands[2].variables[0].getMathML() +
              // U+2192 rightwards arrow
              "<mo>→</mo>"+
              this.operands[0].getMathML() +
            "</mrow>" +
          "</munder>" +
          this.operands[2].expression.getMathML() +
        "</mrow>";
      }
    
    });

  /**
   * Defines an on-screen limit.
   */
  org.mathdox.formulaeditor.presentation.Limit =
    $extend(org.mathdox.formulaeditor.presentation.Column, {

    /**
     * top and bottom rows are smaller
     */
    fontSizeModifierArray : [0,-1],

    /**
     * use top row as baseline
     */
    baselineIndex: 0,

    initialize : function(below) {

      var parent = arguments.callee.parent;
      // U+03A3 greek capital letter sigma
      
      var lim  = new org.mathdox.formulaeditor.semantics.Keyword("limit1","limit",{onscreen:"lim"},"function").getPresentation();
      return parent.initialize.call(this, lim, below);
    },

    getSemantics : function(context) {

      var semantics = org.mathdox.formulaeditor.semantics;

      var below = this.children[1].getSemantics(context, null, null, "approach").value;

      if (below !== null) {
        return {
          value : below,
          rule  : "limit"
        };

      }
      else {
        return null;
      }
    }
  });

  /**
   * Extend the OpenMathParser object with parsing code for limit1.limit.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

      /**
       * Returns a Limit object based on the OpenMath node.
       */
      handleLimit1Limit : function(node) {


        var children = node.childNodes;
        var value = this.handle(children.item(1));
        var method = this.handle(children.item(2));
        var lambda   = this.handle(children.item(3));

        if (lambda.variables.length === 0) {
          alert("limit1.limit needs a nonempty OMBVAR");
          return null;
        }

        return new org.mathdox.formulaeditor.semantics.Limit(value, method, lambda);

      }

    });


  /**
   * Add the parsing code for limits.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
    function(context) { return {

        // expression150 = limit expression150 | super.expression150
        expression150 : function() {
          var parent = arguments.callee.parent;
          pG.alternation(
            pG.transform(
              pG.concatenation(
                pG.rule("limit"),
                pG.rule("expression150")
              ),
              function(result) {

                return new semantics.Limit(
                  result[0][2], 
                  new semantics.Keyword("limit1", "null", null, "constant"),
                  new semantics.Lambda([result[0][0]], result[1])
                );

              }
            ),
            parent.expression150).apply(this, arguments);
        },

        approach : function() {
          var parent = arguments.callee.parent;
          pG.concatenation(
            pG.rule("variable"),
            pG.rule("rightarrow"),
            pG.rule("expression")
          ).apply(this, arguments);
        },
        
        rightarrow: function() {
          var parent = arguments.callee.parent;
          pG.transform(
            pG.alternation(
              pG.concatenation(pG.literal("-"), pG.literal(">")),
              // U+2192 rightwards arrow
              pG.literal("→")
            ),
            function(result){
              // U+2192 rightwards arrow
              return "→";
            }
          ).apply(this, arguments);
        },

        // limit = never
        limit : pG.never
      };
    });

});
