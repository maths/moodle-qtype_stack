$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/SuperscriptSymbol.js");

$require("org/mathdox/formulaeditor/presentation/Superscript.js");
$require("org/mathdox/formulaeditor/presentation/Symbol.js");

$main(function(){

  var presentation = org.mathdox.formulaeditor.presentation;

  /**
   * Representation of a mathematical symbol that is always displayed as
   * superscript (like prime, U+2032, 
   */
  org.mathdox.formulaeditor.presentation.SuperscriptSymbol =
    $extend(org.mathdox.formulaeditor.presentation.Symbol, {
      onBaseline : false,

      children : null,

      initialize: function() {
        // call parent initilialization
        arguments.callee.parent.initialize.apply(this, arguments);

        // set this.children[0] for Superscript-draw
        if (this.value !== undefined && this.value !== null) {
          this.children = [];
          this.children.push(new presentation.Symbol(this.value));
        }
      },

      // use the draw function from Superscript
      draw: presentation.Superscript.prototype.draw
    });

});

