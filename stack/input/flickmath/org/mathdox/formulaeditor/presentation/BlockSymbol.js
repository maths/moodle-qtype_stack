$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/BlockSymbol.js");

$require("org/mathdox/formulaeditor/presentation/Symbol.js");

$main(function(){
  /**
   * Representation of an empty space in a row in the presentation tree.
   */
  org.mathdox.formulaeditor.presentation.BlockSymbol =
    $extend(org.mathdox.formulaeditor.presentation.Symbol, {

    initialize : function() {
      // U+25A1 white square
      this.value = '□';

      if (arguments.length == 1) {
        this.onscreen = arguments[0];
      } else {
        this.onscreen = "f";
      }
    },

    /**
     * Returns a copy of this presentation object, without index information
     * To be used for copy/paste or undo. See also presentation/Node.js
     */
    copy : function() {
      return this.clone(this.onscreen);
    },
    draw : function(canvas, context, x, y, invisible) {
      var fontSizeModifier = 0;
      if (context.fontSizeModifier!== undefined && context.fontSizeModifier !== null) {
        fontSizeModifier = context.fontSizeModifier;
      }

      this.dimensions = canvas.drawFBox(
        Math.round(x), Math.round(y), invisible, this.onscreen, this.typeface, fontSizeModifier);

      return this.dimensions;
    }
  });

});
