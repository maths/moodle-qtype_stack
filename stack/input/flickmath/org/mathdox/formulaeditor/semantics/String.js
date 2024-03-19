$package("org.mathdox.formulaeditor.semantics");

$identify("org/mathdox/formulaeditor/semantics/String.js");

$require("org/mathdox/formulaeditor/presentation/Symbol.js");
$require("org/mathdox/formulaeditor/semantics/Node.js");

$main(function(){

  /**
   * Representation of a variable in the semantic tree.
   */
  org.mathdox.formulaeditor.semantics.SString =
    $extend(org.mathdox.formulaeditor.semantics.Node, {

      /**
       * The variable name.
       */
      name : null,

      /**
       * Initializes a semantic tree node to represent the variable with the
       * specified name.
       */
      initialize : function(name) {
        this.name = name;
      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getPresentation
       */
      getPresentation : function(context) {
        var presentation = org.mathdox.formulaeditor.presentation;

        var str = this.name; // the string that is presented
        var symbols = []; // array for the symbols
	
	// opening "
	symbols.push(new presentation.Symbol("\""));

	// the actual string
        for (var i=0; i<str.length; i++) {
          symbols.push(new presentation.Symbol(str.charAt(i)));
        }

	// closing "
	symbols.push(new presentation.Symbol("\""));

	// now create a row and initialize it with the list of symbols
        var result = new presentation.Row();
        result.initialize.apply(result, symbols);

        return result;

      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getOpenMath
       */
      getOpenMath : function() {
        return "<OMSTR>" + this.name + "</OMSTR>";
      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getMathML
       */
      getMathML : function() {
        return "<mtext>" + this.name + "</mtext>";
      }

    });

});
