$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/Bracket.js");

$require("org/mathdox/formulaeditor/presentation/Node.js");

$main(function(){

  /**
   * Representation of a mathematical bracket (with minimum height) in the
   * presentation tree.
   */
  org.mathdox.formulaeditor.presentation.Bracket =
    $extend(org.mathdox.formulaeditor.presentation.Symbol, {

      /**
       * A string representation of the symbol.
       */
      value : null,
      /**
       * A string representation of the symbol for on the screen
       */
      onscreen : null,

      /*
       * Minimum desired height
       */
      minimumHeight : 1,

      /**
       * Initializes a Symbol node in the presentation tree using the specified
       * string representation of a symbol.
       */
      initialize : function() {

        if (arguments.length > 0) {
          this.value = arguments[0];
        }
        if (arguments.length > 1) {
          this.minimumHeight = arguments[1];
        }
        if (arguments.length > 2) {
          this.onscreen = arguments[2];
        }

      },

      /**
       * Returns a copy of this presentation object, without index information
       * To be used for copy/paste or undo. See also presentation/Node.js
       */
      copy : function() {
        return this.clone(this.value, this.minimumHeight, this.onscreen);
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

        this.dimensions = canvas.drawBracket(
          symbol, Math.round(x), Math.round(y), this.minimumHeight, invisible);

        return this.dimensions;

      }

    });

});
