$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/Fraction.js");

$require("org/mathdox/formulaeditor/presentation/Node.js");

$main(function(){

  /**
   * Representation of a fraction in the presentation tree.
   */
  org.mathdox.formulaeditor.presentation.Fraction =
    $extend(org.mathdox.formulaeditor.presentation.Node, {
      slowDelete : true,

      /**
       * Draws the fraction on the canvas.
       *
       * vertically aligned on middle of previous symbol if present or middle
       * of a letter x (on the baseline) otherwise
       *
       * See also: org.mathdox.formulaeditor.presentation.Node.draw
       */
      draw : function(canvas, context, x, y, invisible) {

        var dim0;
        var presentation = org.mathdox.formulaeditor.presentation;
        if (this.parent instanceof presentation.Row && this.index > 0) {
          dim0 = this.parent.children[this.index - 1].dimensions;
        }
        else {
          dim0 = new presentation.Symbol("x").draw(canvas,context, x,y,true);
        }

        y = dim0.top + (dim0.height/2);

        x = Math.round(x);
        y = Math.round(y);

        var upper = this.children[0];
        var lower = this.children[1];

        var margin = 4.0;
        var lineWidth = 1.0;

        var upperDimensions = upper.draw(canvas, context, 0, 0, true);
        var lowerDimensions = lower.draw(canvas, context, 0, 0, true);

        var left   = x + Math.min(upperDimensions.left, lowerDimensions.left);
        var top    = y - upperDimensions.height - margin;
        var width  = Math.max(upperDimensions.width, lowerDimensions.width) + 
	  2 * margin;
        var height = upperDimensions.height + lowerDimensions.height + 
	  2 * margin + lineWidth;

        this.dimensions = {
          left   : left,
          top    : top,
          width  : width,
          height : height
        };

        this.drawHighlight(canvas, invisible);

        // draw upper part
        upper.draw(
          canvas, context,
          x + (width/2) - (upperDimensions.width/2),
          top - upperDimensions.top,
          invisible);

        if (!invisible) {

          // draw line
          var canvasContext = canvas.getContext();
          canvasContext.save();
          canvasContext.lineWidth = lineWidth;
          canvasContext.beginPath();
          canvasContext.moveTo(x,y);
          canvasContext.lineTo(x+width,y);
          canvasContext.stroke();
          canvasContext.closePath();
          canvasContext.restore();

        }

        // draw lower part
        lower.draw(
          canvas, context,
          x + (width/2) - (lowerDimensions.width/2),
          y + margin - lowerDimensions.top + 1,
          invisible);

        return this.dimensions;

      },

      getCursorPosition : function(x, y) {

        var upper = this.children[0].dimensions;
        var lower = this.children[1].dimensions;

        if (y < ((upper.top + upper.height) + lower.top) / 2) {
          return this.children[0].getCursorPosition(x, y);
        }
        else {
          return this.children[1].getCursorPosition(x, y);
        }

      },

      getFollowingCursorPosition : function(index) {
        if (index === null || index === undefined) {
          return this.children[0].getFollowingCursorPosition();
        }
        else {
          if (this.parent !== null) {
            return { row: this.parent, index: this.index + 1 };
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
            return { row: this.parent, index: this.index };
          }
          else {
            return null;
          }
        }
      },

      // TODO: something fishy when moving through 2/3/4
      getLowerCursorPosition : function(index, x) {
        if (index === null || index === undefined) {
          return this.children[0].getLowerCursorPosition(null, x);
        }
        else {
          if (index === 0) {
            return this.children[1].getLowerCursorPosition(null, x);
          }
          else {
            if (this.parent !== null ) {
              return this.parent.getLowerCursorPosition(this.index, x);
            } else {
              return null;
            }
          }
        }
      },

      getHigherCursorPosition : function(index, x) {
        if (index === null || index === undefined) {
          return this.children[1].getHigherCursorPosition(null, x);
        }
        else {
          if (index == 1) {
            return this.children[0].getHigherCursorPosition(null, x);
          }
          else {
            if (this.parent !== null) {
              return this.parent.getHigherCursorPosition(this.index, x);
            }
            else {
              return null;
            }
          }
        }
      },

      getSemantics : function(context) {
        return {
          value : new org.mathdox.formulaeditor.semantics.Divide(
                    this.children[0].getSemantics(context).value,
                    this.children[1].getSemantics(context).value),
          rule  : "divide"
        };
      }

    });

});
