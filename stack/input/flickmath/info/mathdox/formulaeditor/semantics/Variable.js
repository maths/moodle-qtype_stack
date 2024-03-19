$package("org.mathdox.formulaeditor.semantics");

$identify("org/mathdox/formulaeditor/semantics/Variable.js");

$require("org/mathdox/formulaeditor/presentation/Symbol.js");
$require("org/mathdox/formulaeditor/semantics/Node.js");

$main(function(){

  /**
   * Representation of a variable in the semantic tree.
   */
  org.mathdox.formulaeditor.semantics.Variable =
    $extend(org.mathdox.formulaeditor.semantics.Node, {

      /**
       * The variable name.
       */
      name : null,

     /**
       * Information about the variable that is used to represent it.
       */
      symbol : {

        /**
         * The symbol(s) that is/are used for rendering the variable to the
         * screen.
         */
        onscreen : null,

        /**
         * The MathML representation of this variable.
         */
        mathml   : null

      },


      /**
       * Initializes a semantic tree node to represent the variable with the
       * specified name.
       */
      initialize : function(name, symbol) {
        this.name = name;

        if (symbol) {
          this.symbol = {};
          if (symbol.onscreen) {
            this.symbol.onscreen = symbol.onscreen;
          }
          if (symbol.mathml) {
            this.symbol.mathml = symbol.mathml;
          }
        }
      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getPresentation
       */
      getPresentation : function(context) {
        var presentation = org.mathdox.formulaeditor.presentation;

        var str;
        if (this.symbol.onscreen !== null) {
          str = this.symbol.onscreen.toString();
        } else { 
          str = this.name.toString();
        }
        var symbols = [];

        for (var i=0; i<str.length; i++) {
          symbols[i] = new presentation.Symbol(str.charAt(i), "math");
        }

        var result = new presentation.Row();
        result.initialize.apply(result, symbols);
        return result;

      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getOpenMath
       */
      getOpenMath : function() {
        return "<OMV name='" + this.name + "'/>";
      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getMathML
       */
      getMathML : function() {
        if (this.symbol.mathml !== null) {
          return this.symbol.mathml.toString();
        } else {
          return "<mi>" + this.name + "</mi>";
        }
      }

    });

});
