$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/Superscript.js");

$require("org/mathdox/formulaeditor/presentation/Node.js");

$main(function(){

  /**
   * Represents a superscript expression.
   */
  org.mathdox.formulaeditor.presentation.Superscript =
    $extend(org.mathdox.formulaeditor.presentation.Node, {
      slowDelete : true,
      onBaseline : false,

      draw : function(canvas, context, x, y, invisible) {

        var superscript = this.children[0];

        var dim0;
        var presentation = org.mathdox.formulaeditor.presentation;

	var modifiedContext = { fontSizeModifier : 0 };
        for (var name in context) {
          modifiedContext[name] = context[name];
        }
        modifiedContext.fontSizeModifier = modifiedContext.fontSizeModifier - 1;

	/*
	 * Find a character earlier in the row on the baseline
	 */
        var index = 0;
	var xdim0, ydim0;
        if (this.parent instanceof presentation.Row) {
          index = this.index -1;
	  if (index>=0) { 
            xdim0 = this.parent.children[index].dimensions;
	  }
          while (index>0 && this.parent.children[index].onBaseline !== true) {
            index--;
	  }
        }

        if (index >= 0) {
	  //console.log("on baseline: "+this.parent.children[index].onBaseline);
          ydim0 = this.parent.children[index].dimensions;
	  dim0 = {
            left: xdim0.left,
            top: ydim0.top,
            width: xdim0.width,
            height: ydim0.height
	  }
        }
        else {
          dim0 = new presentation.Symbol("x").draw(canvas,modifiedContext,x,y,true);
          dim0.left = x - dim0.width;
        }

        var tmp = superscript.draw(canvas,modifiedContext,0,0,true);

        // warning drawing twice, should be possible to combine first dim1 with tmp
        var dim1 = superscript.draw(
          canvas,modifiedContext,
          dim0.left + dim0.width,
          dim0.top - (tmp.height + tmp.top),
          true);

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

        if (invisible === false || invisible === null || invisible === undefined) {
          this.drawHighlight(canvas, invisible);
          // warning drawing twice
          superscript.draw(
            canvas,modifiedContext,
            dim0.left + dim0.width,
            dim0.top - (tmp.height + tmp.top),
            invisible);
        }

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
          rule  : "superscript"
        };
      }

    });

});
