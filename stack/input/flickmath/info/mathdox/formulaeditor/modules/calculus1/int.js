$package("org.mathdox.formulaeditor.modules.calculus1");

$identify("org/mathdox/formulaeditor/modules/calculus1/int.js");

$require("org/mathdox/formulaeditor/semantics/MultaryOperation.js");
$require("org/mathdox/formulaeditor/presentation/Row.js");
$require("org/mathdox/formulaeditor/presentation/Symbol.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/modules/fns1/lambda.js");

$main(function(){

  /**
   * Defines a semantic tree node that represents an integration.
   */
  org.mathdox.formulaeditor.semantics.Integration =
    $extend(org.mathdox.formulaeditor.semantics.Node, {

      // operand : lambda expression
      lambda: null,
    
      getPresentation : function(context) {
      
        var presentation = org.mathdox.formulaeditor.presentation;
        var result = new presentation.Row();
        var row;

        row = [
          // U+222B integral
          new presentation.Symbol('∫'),
          this.lambda.expression.getPresentation(context),
          // U+2146 differential D
          new presentation.Symbol("ⅆ"),
          this.lambda.variables[0].getPresentation(context)
        ];

        result.initialize.apply(result, row);
        
        return result;
      },
      
      getOpenMath : function() {
      
        return "<OMA>" +
          "<OMS cd='calculus1' name='int'/>" +
          this.lambda.getOpenMath() +
        "</OMA>";
      },

      getMathML : function() {
        // U+222B integral
        return "<mrow><mo>∫</mo>"+
          this.lambda.expression.getMathML() +
          // U+2146 differential D
          "<mo>ⅆ</mo>"+
          this.lambda.variables[0].getMathML()+
          "</mrow>";
      },

      initialize : function() {
        this.lambda = arguments[0];
      }
    
    });

  /**
   * Extend the OpenMathParser object with parsing code for calculus1.int.
   */
  org.mathdox.formulaeditor.parsing.openmath.OpenMathParser =
    $extend(org.mathdox.formulaeditor.parsing.openmath.OpenMathParser, {

      /**
       * Returns a Integration object based on the OpenMath node.
       */
      handleCalculus1Int : function(node) {

        var children = node.childNodes;
        var lambda   = this.handle(children.item(1));

        if (lambda === null || lambda.variables.length === 0) {
          alert("calculus1.int needs a nonempty OMBVAR");
          return null;
        }

        return new org.mathdox.formulaeditor.semantics.Integration(lambda);
      }

    });


  /**
   * Add the parsing code for integrals.
   */
  var semantics = org.mathdox.formulaeditor.semantics;
  var pG = new org.mathdox.parsing.ParserGenerator();

  org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
    function(context) { return {

      // expression150 = calculus1int | super.expression150
      expression150 : function() {
        var parent = arguments.callee.parent;
        pG.alternation(
          pG.rule("calculus1int"),
          parent.expression150).apply(this, arguments);
      },
      // U+222B integral
      // calculus1int '∫' expression 'd' variable 
      calculus1int: 
        pG.transform(
          pG.concatenation(
            // U+222B integral
            pG.literal("∫"),
            pG.rule("expression"),
            pG.literal("ⅆ"),
            pG.rule("variable")
          ),
          function(result) {
            var integration;

            integration =  new semantics.Integration(
              new semantics.Lambda(result[3], result[1])
            );

            return integration;
          }
        ),
      calculus1int_partial: 
        pG.transform(
          pG.concatenation(
            // U+222B integral
            pG.literal("∫"),
            pG.rule("expression")
          ),
          function(result) {
            // just return the expression
            // return value should probably not be used anyway
            return result[1];
          }
        )
    };
  });

  /**
   * Add a key handler for the 'd' key.
   */
  org.mathdox.formulaeditor.presentation.Row =
    $extend(org.mathdox.formulaeditor.presentation.Row, {

      /**
       * Override the onkeypress method to handle the 'd' key.
       */
      onkeypress : function(event, editor) {

        // only handle keypresses where alt and ctrl are not held
        if (!event.altKey && !event.ctrlKey) {

          // check whether the 'd' key has been pressed
          if (String.fromCharCode(event.charCode) == "d") {

            // search for a partial integral expression to the left of
            // the cursor
            var index = editor.cursor.position.index;
            var parsedleft = this.getSemantics(editor.getExpressionParsingContext(), 0, index, 
              "calculus1int_partial", true);
	
            if (parsedleft.value !== null || parsedleft.index > 0) {
              // found a partial calculus1int expression
              // U+2146 differential d
              var presentation = org.mathdox.formulaeditor.presentation;
              this.insert(index, new presentation.Symbol("ⅆ"));
              editor.cursor.moveRight();
            
              // update the editor state
              editor.redraw();
              editor.save();
              return false;
            }
          }

        }

        // call the overridden method
        return arguments.callee.parent.onkeypress.call(this, event, editor);

      }

    });


});
