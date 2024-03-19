$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/Vector.js");

$require("org/mathdox/formulaeditor/presentation/Bracket.js");
$require("org/mathdox/formulaeditor/presentation/Bracketed.js");
$require("org/mathdox/formulaeditor/presentation/PArray.js");

$main(function(){

  /**
   * Representation of a column of mathematical expressions in the presentation
   * tree.
   */
  org.mathdox.formulaeditor.presentation.Vector =
    $extend(org.mathdox.formulaeditor.presentation.Bracketed, {
      // variable to store the array to get the semantics
      entries : null,

      initialize : function () {
        var presentation = org.mathdox.formulaeditor.presentation;
        var leftBracket = new presentation.Bracket('(');
        var rightBracket = new presentation.Bracket(')');

        this.middle = new presentation.Column();
        this.middle.initialize.apply(this.middle,arguments);
        this.middle.margin = 10.0;

        arguments.callee.parent.initialize.call(this, leftBracket, 
          this.middle, rightBracket);
      },

      /**
       * Returns a copy of this presentation object, without index information
       * To be used for copy/paste or undo. See also presentation/Node.js
       */
      copy : function() {
        return this.clone.apply(this, this.copyArray(this.middle.children));
      },

      getSemantics : function(context) {
        var semanticEntries;
        var vector;

        var semantics = org.mathdox.formulaeditor.semantics;
        semanticEntries = [];
        for (var i=0;i<this.middle.children.length;i++) {
          semanticEntries.push(this.middle.children[i].getSemantics(context).value);
        }
        vector = new semantics.Linalg2Vector();
        vector.initialize.apply(vector, semanticEntries);

        return {
          value : vector,
          rule  : "braces"
        };
      }

    });

});
