$package("org/mathdox/formulaeditor/semantics");

$identify("org/mathdox/formulaeditor/semantics/SemanticFloat.js");

$require("org/mathdox/formulaeditor/semantics/Node.js");
$require("org/mathdox/formulaeditor/presentation/Symbol.js");
$require("org/mathdox/formulaeditor/presentation/Row.js");

$main(function(){

  /**
  * Representation of a float in the semantic tree.
  * it is named SemanticFloat because float is a reserved keyword
  */
  org.mathdox.formulaeditor.semantics.SemanticFloat =
    $extend(org.mathdox.formulaeditor.semantics.Node, {

      /**
       * The float value.
       */
      value : null,

      /**
       * Initializes a semantic tree node to represent an float with the
       * specified value.
       */
      initialize : function(value) {
        this.value = value;
      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getPresentation
       */
      getPresentation : function(context) {
        var presentation = org.mathdox.formulaeditor.presentation;

        var string = this.getValueAsString(context); // getValueAsString returns a string with corrected decimal mark
        var symbols = [];

        for (var i=0; i<string.length; i++) {
          symbols[i] = new presentation.Symbol(string.charAt(i));
        }

        var result = new presentation.Row();
        result.initialize.apply(result, symbols);
        return result;

      },

      /**
       * return the value with the correct decimal mark
       */
      getValueAsString : function(context) {
        var string = this.value.toString();

	if (context === null || context === undefined || context.decimalMark == '.') {
          return string;
	}

	// use context to change the decimalMark

        var result=[];

        for (var i=0; i<string.length; i++) {
          if (string.charAt(i) != '.') {
            result.push(string.charAt(i));
          } else {
            result.push(context.decimalMark);
          }
        }

        return result.join("");
      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getOpenMath
       */
      getOpenMath : function() {
        return "<OMF dec='" + this.value + "'/>";
      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getMathML
       */
      getMathML : function() {
        var string = this.value.toString();
        var result=[];

	result.push("<mn>");

        for (var i=0; i<string.length; i++) {
          if (string.charAt(i)!='.') {
            result.push(string.charAt(i));
          } else {
            result.push((new org.mathdox.formulaeditor.Options).getDecimalMark());
          }
        }
	result.push("</mn>");

        return result.join("");
      },

      toString : function() {
        return this.value.toString();
      }

    });

});
