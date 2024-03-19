$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/PseudoRow.js");

$require("org/mathdox/formulaeditor/presentation/Node.js");
$require("org/mathdox/formulaeditor/presentation/Row.js");

$main(function(){
  /**
   * Representation of a row, which will not be flattened
   */
  org.mathdox.formulaeditor.presentation.PseudoRow =
    $extend(org.mathdox.formulaeditor.presentation.Node, {
      
      // allow overwriting of abstract draw method
      draw: null,

      functionsFromRow : [ "getFirstCursorPosition", "getFollowingCursorPosition", "getPrecedingCursorPosition",
        "getLastCursorPosition", "getLowerCursorPosition",
        "getHigherCursorPosition", "draw", "isEmpty", "getMathML", "getSemantics", "insert", "replace", "remove" ],

      initialize : function () {
        this.children = Array.prototype.slice.call(arguments);

        var presentation = org.mathdox.formulaeditor.presentation;
        /* copy the cursor/position functions from Row */

        var row = new presentation.Row(); // only an instance has the functions
        
        for (var i=this.functionsFromRow.length - 1; i>=0; i--) {
          if (! this[this.functionsFromRow[i]] ) {
            this[this.functionsFromRow[i]] = 
              row[ this.functionsFromRow[i] ];
          }
        }

        this.updateChildren();
      },
    });
});
