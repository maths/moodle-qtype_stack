$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/Symbol.js");

$require("org/mathdox/formulaeditor/presentation/Node.js");
$require("org/mathdox/formulaeditor/presentation/SymbolAliases.js");

$main(function(){

  /**
   * Representation of a mathematical symbol (number, letter, operator) in the
   * presentation tree.
   */
  org.mathdox.formulaeditor.presentation.Symbol =
    $extend(org.mathdox.formulaeditor.presentation.Node, {

      /**
       * A string representation of the symbol.
       */
      value : null,
      /**
       * A string representation of the symbol for on the screen
       */
      onscreen : null,
      /**
       * The typeface (currently supported: math, which means display as
       * slanted if possible)
       */
      typeface : null,

      /**
       * Initializes a Symbol node in the presentation tree using the specified
       * string representation of a symbol.
       */
      initialize : function() {
        
        if (arguments.length > 0) {
          if (arguments[0] instanceof Array) {
            this.value = arguments[0][0];
            if (arguments[0].length > 1) {
              this.onscreen = arguments[0][1];
            }
          } else {
            this.value = arguments[0];
          }
	  var aliases = org.mathdox.formulaeditor.presentation.SymbolAliases;

	  if (aliases[this.value] !== undefined && aliases[this.value] !== null) {
            this.value = aliases[this.value];
	  }
        }
        if (arguments.length > 1) {
          this.typeface = arguments[1];
        }
      },

      /**
       * Returns a copy of this presentation object, without index information
       * To be used for copy/paste or undo. See also presentation/Node.js
       */
      copy : function() {
        var result;
        var arg0;

        if (this.onscreen !== null) {
          arg0 = [ this.value, this.onscreen ];
        } else {
          arg0 = this.value;
        }

        if (this.typeface !== null) {
          result = this.clone(arg0, this.typeface);
        } else {
          result = this.clone(arg0);
        }
  
        return result;
      },

      /**
       * Draws the symbol to the canvas.
       *
       * See also: org.mathdox.formulaeditor.presentation.Node.draw
       */
      draw : function(canvas, context, x, y, invisible) {
        var symbol = this.value;
        if (this.onscreen !== null) {
          symbol = this.onscreen;
        }
       
        var fontSizeModifier = 0;
        if (context.fontSizeModifier!== undefined && context.fontSizeModifier !== null) {
          fontSizeModifier = context.fontSizeModifier;
        }

        this.dimensions = canvas.drawSymbol(
          symbol, Math.round(x), Math.round(y), invisible, this.typeface, 
          fontSizeModifier);

        return this.dimensions;

      }

    });

});
