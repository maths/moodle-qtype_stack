$package("org.mathdox.formulaeditor.semantics");

$identify("org/mathdox/formulaeditor/semantics/Lambda.js");

$require("org/mathdox/formulaeditor/semantics/Node.js");
$require("org/mathdox/formulaeditor/semantics/Variable.js");
$require("org/mathdox/formulaeditor/presentation/Row.js");
$require("org/mathdox/formulaeditor/presentation/Symbol.js");

$main(function(){

  /**
   * Representation of an n-ary function application.
   */
  org.mathdox.formulaeditor.semantics.Lambda =
    $extend(org.mathdox.formulaeditor.semantics.Node, {

      /**
       * The expression 
       */
      expression: null,

      /**
       * The bound variables of the lambda expression
       */
      variables: [],

      /**
       * Initializes the operation using the specified arguments as operands.
       */
      initialize : function() {
        var variables;
        var expression;

        if (arguments.length === 0) {
          // no arguments 
          variables = [];
          expression = null;
        } else if (arguments.length == 1) {
          // one arguments -> only expression
          variables = [];
          expression = arguments[0];
        } else if (arguments.length == 2) {
          // two arguments -> variable [array] and expression
          if (arguments[0] instanceof Array) {
            variables = arguments[0];
          } else {
            variables = [];
            variables.push(arguments[0]);
          }
          expression = arguments[1];
        } else if (arguments.length > 2) {
          // more than two arguments -> variables and expression
          variables = argument.slice(0, arguments.length -2);
          expression = arguments[arguments.length - 1];
        }

        this.variables = variables;
        this.expression = expression;
      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getPresentation(context)
       */
      getPresentation : function(context) {

        var presentation = org.mathdox.formulaeditor.presentation;

        // construct an array of the presentation of operand nodes interleaved
        // WITH Operator symbols
        var array = [];
        var pres;
        var i; // counter

        // U+03BB greek small letter lamda
        array.push(new presentation.Symbol("λ"));
        if (this.variables.length == 1) {
          array.push(this.variables[0].getPresentation(context));
        } else {
          array.push(new presentation.Symbol("("));
          for (i=0; i<this.variables.length; i++) {
            if (i>0) {
              array.push(new presentation.Symbol(","));
            }
            array.push(this.variables[i].getPresentation(context));
          }
          array.push(new presentation.Symbol(")"));
        }

        array.push(new presentation.Symbol("."));
        array.push(new presentation.Symbol("("));
        array.push(this.expression.getPresentation(context));
        array.push(new presentation.Symbol(")"));

        // create and return new presentation row using the constructed array
        var result = new presentation.Row();
        result.initialize.apply(result, array);

        return result;
      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getOpenMath()
       */
      getOpenMath : function() {

        var result = [];
        var i; // counter
        
        result.push("<OMBIND>");
        result.push("<OMS cd='fns1' name='lambda'/>");
        
        // bound variables
        result.push("<OMBVAR>");

        for (i=0; i<this.variables.length; i++) {
          result.push(this.variables[i].getOpenMath());
        }

        result.push("</OMBVAR>");

        // expression
        result.push(this.expression.getOpenMath());

        result.push("</OMBIND>");

        return result.join("");
      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getMathML()
       */
      getMathML : function() {
        var result = [];
        result.push("<mrow>");
        // U+03BB greek small letter lamda
        result.push("<mo>λ</mo>");

        if (this.variables.length == 1) {
          result.push(this.variables[0].getMathML());
        } else {
          result.push("<mo>(</mo>");
          for (i=0; i<this.variables.length; i++) {
            if (i>0) {
              result.push("<mo>,</mo>");
            }
            result.push(this.variables[i].getMathML());
          }
          result.push("<mo>)</mo>");
        }
        result.push("</mrow>");

        return result.join("");

      }

    });

});
