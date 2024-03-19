$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/Bracketed.js");

$require("org/mathdox/formulaeditor/presentation/Node.js");
$require("org/mathdox/formulaeditor/presentation/Row.js");

$main(function(){

  /**
   * Representation of a column of mathematical expressions in the presentation
   * tree.
   */
  org.mathdox.formulaeditor.presentation.Bracketed =
    $extend(org.mathdox.formulaeditor.presentation.Node, {
      // array
      middle : null,
      // left bracket
      leftBracket : null,
      // right bracket
      rightBracket : null,
      // should we draw boxes ?
      drawBox : false,
      // enable slow deleting
      slowDelete : true,

      /**
       * Draws the matrix to the canvas.
       *
       * See also: org.mathdox.formulaeditor.presentation.Node.draw
       */
      draw : function(canvas, context, x, y, invisible) {
        var height;

        // invisible drawing of array to set dimensions
        
        this.middle.draw(canvas, context, 0, 0, true);

        // if the left and right symbols are brackets set the height
        // XXX check if they are brackets
        this.leftBracket.minimumHeight = 
          this.middle.dimensions.height;
        this.rightBracket.minimumHeight = 
          this.middle.dimensions.height;

        // invisible drawing of brackets to set dimensions
        this.leftBracket.draw(canvas, context, 0, 0, true);
        this.rightBracket.draw(canvas, context, 0, 0, true);

        height = Math.max(
            this.leftBracket.dimensions.height,
            this.middle.dimensions.height,
            this.rightBracket.dimensions.height);

        var yAdjust = 0;
        var yAdjustBrackets = 0;
        
        // brackets are higher than the array
        if (height>this.middle.dimensions.height) {
          yAdjust = (height - this.middle.dimensions.height)/2;
        }

        // brackets are smaller than the array
        // assuming right bracket has the same size as the left bracket
        if (this.leftBracket.dimensions.height<height) {
          yAdjustBrackets = (height - this.leftBracket.dimensions.height)/2;
        }

        this.dimensions = { 
          height : height,
          width : 
            this.leftBracket.dimensions.width +
            this.middle.dimensions.width +
            this.rightBracket.dimensions.width,
          left : x,
          top : y + this.middle.dimensions.top - yAdjust
        };
        
	this.drawHighlight(canvas, invisible);

        this.leftBracket.minimumHeight = this.middle.dimensions.height;
        this.leftBracket.draw(canvas, context,  
          x - this.leftBracket.dimensions.left, 
          this.dimensions.top + yAdjustBrackets - 
          this.leftBracket.dimensions.top, 
          invisible);

        this.middle.draw(canvas, context,  
          x + this.leftBracket.dimensions.width - this.middle.dimensions.left, 
          y, invisible);

        this.rightBracket.minimumHeight = this.middle.dimensions.height;
        this.rightBracket.draw(canvas, context, 
          x + this.rightBracket.dimensions.width + 
            this.middle.dimensions.width - this.rightBracket.dimensions.left,
          this.dimensions.top + yAdjustBrackets - 
          this.rightBracket.dimensions.top, 
          invisible);
        
        if ((!invisible) &&this.drawBox) {
          canvas.drawBox(this.middle.dimensions);
          canvas.drawBoxWithBaseline(this.leftBracket.dimensions, this.dimensions.top + this.dimensions.height - yAdjustBrackets);
          canvas.drawBoxWithBaseline(this.rightBracket.dimensions, this.dimensions.top + this.dimensions.height - yAdjustBrackets);
          canvas.drawBoxWithBaseline(this.dimensions,y);
        }

        return this.dimensions;
      },
      functionsFromRow : [ "getFirstCursorPosition",
        "getLastCursorPosition", "getLowerCursorPosition",
        "getHigherCursorPosition" ],
      getCursorPosition: function(x,y) {
        var dimensions;

        dimensions = this.leftBracket.dimensions;
        if (x < dimensions.left + dimensions.width) {
          if (this.parent !== null) {
            return { row: this.parent, index: this.index };
          } else {
            return null;
          }
          return this.getFollowingCursorPosition();
        }
        dimensions = this.middle.dimensions;
        if (x < dimensions.left + dimensions.width) {
          return this.middle.getCursorPosition(x,y);
        }
        if (this.parent !== null) {
          return { row: this.parent, index: this.index+1 };
        } else {
          return this.getPrecedingCursorPosition();
        }
      },
      getFollowingCursorPosition : function(index, descend) {

        // default value for descend
        if (descend === null || descend === undefined) {
          descend = true;
        }

        // when index is not specified, return the first position in the array
        if (index === null || index === undefined) {
          return this.middle.getFollowingCursorPosition();
        }
        
        var result = null;

        if (index === 0) {
          if (descend) {
            result = this.middle.getFollowingCursorPosition();
          }
        }

        if (result === null) {
          // when we're at the end of the matrix, ask the parent of the matrix
          // for the position following this matrix
          if (this.parent !== null) {
            return this.parent.getFollowingCursorPosition(this.index, false);
          }
        }
        
        return result;
      },
      getPrecedingCursorPosition : function(index, descend) {

        // default value for descend
        if (descend === null || descend === undefined) {
          descend = true;
        }

        // when index is not specified, return the first position in the array
        if (index === null || index === undefined) {
          return this.middle.getPrecedingCursorPosition();
        }
        
        var result = null;

        if (index == 1) {
          if (descend) {
            result = this.middle.getPrecedingCursorPosition();
          }
        }

        if (result === null) {
          // when we're at the beginning of the matrix, ask the parent of the
          // matrix for the position before this matrix
          if (this.parent !== null) {
            return this.parent.getPrecedingCursorPosition(this.index+1, false);
          }
        }
        
        return result;
      },
      initialize : function () {
        if (arguments.length>0) {
          this.leftBracket = arguments[0];
          this.middle = arguments[1];
          this.rightBracket = arguments[2];
          this.children = [];
          this.children.push(this.middle);
        } else {
          this.children = [];
        }

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
      /**
       * Returns a copy of this presentation object, without index information
       * To be used for copy/paste or undo. See also Presentation/Node.js
       */
      copy : function () {
        return this.clone(this.leftBracket.copy(), this.children[0].copy(), this.rightBracket.copy());
      },
      getSemantics: function(context) {
	var sem = this.middle.getSemantics(context, null, null, "functionArguments", null);
	var value = sem.value;

	if (!(value instanceof Array)) {
	  return {
            rule: "braces",
            value: value
          };
	} else if (value.length === 1) {
	  // NOTE: probably should not occur
	  return {
            rule: "braces",
            value: value[0]
          };
	} else {
          return {
	    rule: "bracesWithSeparatedArguments",
	    value: value
          };
        }
      }
    });
});
