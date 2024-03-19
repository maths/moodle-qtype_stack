$package("org.mathdox.formulaeditor");

$identify("org/mathdox/formulaeditor/Cursor.js");

$main(function(){

  /**
  * Represents the keyboard cursor.
  */
  org.mathdox.formulaeditor.Cursor = $extend(Object, {

    position : null,

    visible : false,

    initialize : function(position) {
      this.position = position;
    },

    /**
     * Handles an onkeydown event from the browser. Returns false when the event
     * has been handled and should not be handled by the browser, returns true
     * otherwise.
     */
    onkeydown : function(event, editor) {

      // only handle keypresses where alt, ctrl and shift are not held
      if (!event.altKey && !event.ctrlKey && !event.shiftKey) {

        // handle arrow keys, home and end
        switch(event.keyCode) {

          case 35: // end
            this.moveLast();
            editor.redraw();
            return false;

          case 36: // home
            this.moveFirst();
            editor.redraw();
            return false;

          case 37: // left arrow
            this.moveLeft();
            editor.redraw();
            return false;

          case 38: // up arrow
            this.moveUp();
            editor.redraw();
            return false;

          case 39: // right arrow
            this.moveRight();
            editor.redraw();
            return false;

          case 40: // down arrow
            this.moveDown();
            editor.redraw();
            return false;
	  
        }

      }

      // pass the event to the presentation tree
      return this.position.row.onkeydown(event, editor);

    },

    /**
     * Handles an onkeypress event from the browser. Returns false when the
     * event has been handled and should not be handled by the browser, returns
     * true otherwise.
     */
    onkeypress : function(event, editor) {

      // pass the event to the presentation tree
      return this.position.row.onkeypress(event, editor);

    },

    /**
     * Handles an onmousedown event from the browser. Returns false when the
     * event has been handled and should not be handled by the browser, returns
     * true otherwise.
     */
    onmousedown : function(event, editor, x, y) {
      var position = editor.presentation.getCursorPosition(x,y);

      if (position) {
	this.position.row.setHighlight(false);
	this.position = position;
      } else {
	editor.presentation.onmousedown(event, editor, x, y);
      }
      if (this.position === null || this.position === undefined) {
	this.position = editor.presentation.getFollowingCursorPosition();
      }

      editor.redraw();
    },

    moveRight : function() {

      var row   = this.position.row;
      var index = this.position.index;
      
      var newPosition = row.getFollowingCursorPosition(index);
      if (newPosition !== null) {
        this.position = newPosition;
      }

      row.setHighlight(false);
    },

    moveLeft : function() {

      var row   = this.position.row;
      var index = this.position.index;

      var newPosition = row.getPrecedingCursorPosition(index);
      if (newPosition !== null) {
        this.position = newPosition;
      }

      row.setHighlight(false);
    },

    moveDown : function() {

      var row   = this.position.row;
      var index = this.position.index;

      var newPosition = row.getLowerCursorPosition(index, this.getX());
      if (newPosition !== null) {
        this.position = newPosition;
      }

      row.setHighlight(false);
    },

    moveUp : function() {

      var row   = this.position.row;
      var index = this.position.index;

      var newPosition = row.getHigherCursorPosition(index, this.getX());
      if (newPosition !== null) {
        this.position = newPosition;
      }

      row.setHighlight(false);
    },

    moveFirst : function() {

      var row   = this.position.row;
      var index = this.position.index;

      var newPosition = row.getFirstCursorPosition(index);
      if (newPosition !== null) {
        this.position = newPosition;
      }

      row.setHighlight(false);
    },

    moveLast : function() {

      var row   = this.position.row;
      var index = this.position.index;

      var newPosition = row.getLastCursorPosition(index);
      if (newPosition !== null) {
        this.position = newPosition;
      }

      row.setHighlight(false);
    },

    getX : function() {

      // TODO: use this in draw, rename
      var row   = this.position.row;
      var index = this.position.index;

      var dim0 = row.children[index-1] ? row.children[index - 1].dimensions : null;
      var dim1 = row.children[index]   ? row.children[index].dimensions     : null;

      if (dim0 === null && dim1 === null) {
        dim1 = row.dimensions;
      }
      if (dim0 === null && dim1 !== null) {
        dim0 = { left: row.dimensions.left, top: dim1.top, width:0, height: dim1.height };
      }
      if (dim1 === null && dim0 !== null) {
        dim1 = { left: dim0.left + dim0.width, top: dim0.top, width:0, height: dim0.height };
      }

      return Math.round(dim0.left + dim0.width + ((dim1.left - (dim0.left + dim0.width))/2));

    },

    draw : function(canvas, context) {

      if (this.visible) {

        var lineWidth = 2.0;
        var margin    = 2.0;
        var color     = "#66C";

        var row   = this.position.row;
        var index = this.position.index;

        var dim0 = row.children[index-1] ? row.children[index - 1].dimensions : null;
        var dim1 = row.children[index]   ? row.children[index].dimensions     : null;

        if (dim0 === null && dim1 === null) {
          dim1 = row.dimensions;
        }
        if (dim0 === null && dim1 !== null) {
          dim0 = { left: row.dimensions.left, top: dim1.top, width:0, height: dim1.height };
        }
        if (dim1 === null && dim0 !== null) {
          dim1 = { left: dim0.left + dim0.width, top: dim0.top, width:0, height: dim0.height };
        }

        var x      = Math.round(dim0.left + dim0.width + ((dim1.left - (dim0.left + dim0.width))/2));
        var top    = Math.round(Math.min(dim0.top, dim1.top) - margin);
        var bottom = Math.round(Math.max(dim0.top + dim0.height, dim1.top + dim1.height) + margin);

        // ensure that size of the cursor is at least height of the symbol 'f'
        var Symbol = org.mathdox.formulaeditor.presentation.Symbol;
        var fHeight = new Symbol("f").draw(canvas, context, 0, 0, true).height;
        fHeight = fHeight + 2 * margin;
        if (bottom - top < fHeight) {
          var delta = fHeight - (bottom - top);
          top = top - delta/2;
          bottom = bottom + delta/2;
        }

        var canvasContext = canvas.getContext();

        canvasContext.save();
        canvasContext.lineWidth = lineWidth;
        canvasContext.strokeStyle = color;
        canvasContext.beginPath();
        canvasContext.moveTo(x, top);
        canvasContext.lineTo(x, bottom);
        canvasContext.stroke();
        canvasContext.closePath();
        canvasContext.restore();

      }

    }

  });

});
