$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/Subscript.js");

$require("org/mathdox/formulaeditor/presentation/Node.js");

$main(function(){

  /**
   * Represents a subscript expression.
   */
  org.mathdox.formulaeditor.presentation.Subscript =
    $extend(org.mathdox.formulaeditor.presentation.Node, {
      onBaseline : false,

      draw : function(canvas, context, x, y, invisible) {

        var subscript = this.children[0];

        var dim0;
        var presentation = org.mathdox.formulaeditor.presentation;

        var modifiedContext = { fontSizeModifier : 0 };
        for (var name in context) {
          modifiedContext[name] = context[name];
        }
        modifiedContext.fontSizeModifier = modifiedContext.fontSizeModifier - 1;

        if (this.parent instanceof presentation.Row && this.index > 0) {
          dim0 = this.parent.children[this.index - 1].dimensions;
        }
        else {
          dim0 = new presentation.Symbol("x").draw(canvas,modifiedContext,x,y,true);
          dim0.left = x - dim0.width;
        }

        var tmp = subscript.draw(canvas,modifiedContext,0,0,true);

        var dim1 = subscript.draw(
          canvas, modifiedContext,
          dim0.left + dim0.width,
          dim0.top + dim0.height - tmp.top,
          invisible);

        var left   = dim1.left;
        var top    = Math.min(dim0.top,  dim1.top );
        var right  = dim1.left + dim1.width;
        var bottom = Math.max(dim0.top  + dim0.height, dim1.top  + dim1.height);

        this.dimensions = {
          left   : left,
          top    : top,
          width  : right - left,
          height : bottom - top
        };

        return this.dimensions;

      },

      getCursorPosition : function(x, y) {

        return this.children[0].getCursorPosition(x,y);

      },

      getFollowingCursorPosition : function(index) {
        if (index === null || index === undefined) {
          return this.children[0].getFollowingCursorPosition();
        }
        else {
          if (this.parent !== null) {
            return { row : this.parent, index: this.index + 1 };
          }
          else {
            return null;
          }
        }
      },

      getPrecedingCursorPosition : function(index) {
        if (index === null || index === undefined) {
          return this.children[0].getPrecedingCursorPosition();
        }
        else {
          if (this.parent !== null) {
            return { row : this.parent, index: this.index };
          }
          else {
            return null;
          }
        }
      },

      getSemantics : function(context) {
        return {
          value : this.children[0].getSemantics(context).value,
          rule  : "subscript"
        };
      }

    });

});
