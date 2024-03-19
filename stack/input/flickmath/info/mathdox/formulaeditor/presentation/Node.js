$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/Node.js");

$main(function(){

  /**
   * Representation of a node in the presentation tree.
   */
  org.mathdox.formulaeditor.presentation.Node = $extend(Object, {

    /**
     * The parent node of this node.
     */
    parent : null,

    /**
     * The amount of siblings preceding this node.
     */
    index : null,

    /**
     * The child nodes of this node.
     */
    children : [],

    /**
     * The position and dimensions of the node when it was last rendered to a
     * canvas.
     */
    dimensions : null,

    /**
     * Indicate if this should be drawn with a highlight. Note: not all items
     * might support highlights.
     */
    highlight: false,

    /**
     * Indicate if this should not be deleted directly. The item will be
     * highlighted instead.
     */
    slowDelete: false,

    /**
     * Indicate this is on the baseline (for superscript/subscript drawing)
     */
    onBaseline: true,

    /**
     * The arguments to the constructor are the children of this node.
     */
    initialize : function() {

      this.children = Array.prototype.slice.call(arguments);
      this.updateChildren();

    },

    /*
      if ( typeof Object.getPrototypeOf !== "function" ) {
        if ( typeof "test".__proto__ === "object" ) {
          Object.getPrototypeOf = function(object){
            return object.__proto__;
          };
        } else {
          Object.getPrototypeOf = function(object){
            // May break if the constructor has been tampered with
            return object.constructor.prototype;
          };
        }
      }
    */

    /**
     * Returns a copy of an object initialized with arguments args, which
     * should be an array.
     */
    clone: function() {
      var constructor = function(){};
      constructor.prototype = Object.getPrototypeOf(this);

      var result = new constructor();
      result.initialize.apply(result, arguments);
      
      return result;
    },

    /**
     * Returns a copy of this presentation object, without index information
     * To be used for copy/paste or undo. See also presentation/Node.js
     */
    copy : function() {
      return this.clone.apply(this, this.copyArray(this.children));
    },

    /**
     * Returns a copy of an array of presentation objects, without index
     * information. To be used for copy/paste or undo.
     */
    copyArray: function(arr) {
      var result = [];

      for (var i=0;i<arr.length;i++) {
        if (arr[i] instanceof Array) {
          result.push(this.copyArray(arr[i]));
	} else {
          result.push(arr[i].copy());
        }
      }
      return result;
    },

    /**
     * Test to see if we can delete this item.
     *
     * Result: 
     *   Returns true if this can be deleted. Returns false if this should not
     *   be deleted yet. This allows highlighting an item before deleting it.
     */
    deleteItem: function() {
      if (this.slowDelete === true) {
        if (this.highlight === true) {
          return true;
        } else {
          this.setHighlight(true);

          return false;
        }
      } else {
        return true;
      }
    },

    /**
     * Draws the node on the specified canvas context. This is an abstract
     * method, so it is expected that subclasses will override this method.
     *
     * Parameter canvas: The 2d context of the canvas upon which this node is
     *   expected to draw itself
     * Parameter context: The draw context (subscript font is smaller; etc)
     * Parameters x,y: The (x,y) coordinate indicates where the left of the
     *   baseline of the node will appear.
     * Parameter invisible: This is an optional boolean parameter that indicates
     *   whether or not the node should be drawn to the canvas. It defaults to
     *  'false'. Setting this parameter to 'true' can be used to obtain
     *   information about the dimensions of the node, without actually
     *   drawing on the canvas.
     * Result : an object containing the values { left, top, width, height }
     *   that indicate the position and dimensions of the bounding rectangle
     *   of the node.
     */
    draw : function(canvas, context, x, y, invisible) {

      throw new Error("abstract method called");

    },

    /**
     * draws the background when highlighted
     *
     * Parameter dimensions: the size of the object
     */
    drawHighlight : function(canvas, invisible) {
      var dimensions = this.dimensions;
      if ( (invisible === undefined || invisible === null || invisible === false ) && this.highlight === true) {
        canvas.drawBox(dimensions, "#66C", "rgba(160,160,255,0.5)");
        //canvas.drawBox(dimensions, "#66C", "rgba(255,0,0,0.5)");
      }
    },

    /**
     * Returns the MathML presentation of the node. This is an abstract method,
     * so it is expected that subclasses will override this method.
     */
    getMathML : function(mrow) {
      throw new Error("abstract method called");
    },

    /**
     * Method which is called whenever this node changes. Calls the parent's
     * onchange method by default.
     */
    onchange : function(node) {

      if (this.parent !== null) {
        this.parent.onchange(this);
      }

    },

    /**
     * Called whenever a keypress has been detected when the cursor was at the
     * specified index. Does nothing by default, should be overridden to do
     * something usefull.
     */
    onkeypress : function(index, event) {

      // skip

    },

    /**
     * Called whenever a mouseclick has been detected at position (x,y), and no
     * new cursor position could be found. Put other actions here.
     * If the event is handled, return false.
     */
    onmousedown : function(event, editor, x, y) {
      // skip
      return true;

    },

    /**
     * Flattens the tree, meaning that all rows inside rows will be moved into
     * one row.
     */
    flatten : function() {

      // flatten the child nodes
      for (var i=0; i<this.children.length; i++) {
        if (! this.children[i]) {
          alert("no child at :"+i);
        } else if (! this.children[i].flatten) {
          alert("no flatten in : "+i+".");
        } else {
          this.children[i].flatten();
        }
      }

    },

    /**
     * Re-calculates each child's index value, and sets each child's parent
     * value. This method should be called after a change in the tree. When the
     * 'begin' argument is specified, only the children at index >= begin will
     * be updated.
     */
    updateChildren : function(begin) {

      if ((begin === null) || (begin === undefined)) {
        begin = 0;
      }
      for (var i=begin; i<this.children.length; i++) {
        if (! this.children[i]) {
          alert("empty child : "+i+".");
        } else {
          this.children[i].parent = this;
          this.children[i].index = i;
        }
      }

    },

    /**
     * Returns a keyboard cursor position closest to the specified screen
     * coordinates.
     */
    getCursorPosition : function(x, y) {

      if (this.parent !== null) {
        if (x < this.dimensions.left + this.dimensions.width / 2) {
          return this.parent.getPrecedingCursorPosition(this.index+1,false);
        }
        else {
          return this.parent.getFollowingCursorPosition(this.index,false);
        }
      }
      else {
        return null;
      }

    },

    getFirstCursorPosition : function(index) {
      if (this.parent !== null) {
        return this.parent.getFirstCursorPosition();
      }
      else {
        return null;
      }
    },

    getLastCursorPosition : function(index) {
      if (this.parent !== null) {
        return this.parent.getLastCursorPosition();
      }
      else {
        return null;
      }
    },

    /**
     * Returns the cursor position following the cursor position at the
     * specified index. When no cursor position can be provided, null is
     * returned. By default this method always returns null, override it to do
     * something useful.
     */
    getFollowingCursorPosition : function(index) {

      return null;

    },

    getPrecedingCursorPosition : function(index) {

      return null;

    },

    getLowerCursorPosition : function(index, x) {

      var presentation = org.mathdox.formulaeditor.presentation;

      if (this.parent !== null) {
        if ((index === null || index === undefined) && 
            this.parent instanceof presentation.Row) {
          return { row: this.parent, index: this.index };
        }
        else {
          return this.parent.getLowerCursorPosition(this.index, x);
        }
      }
      else {
        return null;
      }

    },

    getHigherCursorPosition : function(index, x) {

      var presentation = org.mathdox.formulaeditor.presentation;

      if (this.parent !== null) {
        if ((index === null || index === undefined) && 
            this.parent instanceof presentation.Row) {
          return { row: this.parent, index: this.index };
        } else {
          return this.parent.getHigherCursorPosition(this.index, x);
        }
      } else {
        return null;
      }

    },
    toString : function() {
      if (this.value) {
        return this.value;
      } else if (this.children) {
        var str = "[ ";
        for (var i=0; i<this.children.length; i++) {
          str+=this.children[i];
          if (i<this.children.length-1) {
            str +=", ";
          }
        }
        str+=" ]";
        return str;
      }
    },
    maxDimensions: function(x,y,arr) {
      var i;
      var maxdim={ top:y, left:x, width:0, height:0 };
      var top, bottom, left, right;

      for (i=0; i< arr.length;i++) {
        top = Math.min(maxdim.top,arr[i].top);
        bottom = Math.max(maxdim.top+maxdim.height, 
          arr[i].top+arr[i].height);
        left = Math.min(maxdim.left,arr[i].left);
        right = Math.max(maxdim.left+maxdim.width, 
          arr[i].left+arr[i].width);
        maxdim = { 
          top:top, 
          left:left, 
          width: right-left, 
          height: bottom - top
        };
      }
      return maxdim;
    },

    /**
     * Parameter highlight:
     *   true if this should be highlighted.
     */
    setHighlight: function(highlight) {
      if (highlight === true || highlight === false) {
        this.highlight = highlight;
      } else {
        console.log('presentation.Node.setHighlight: invalid argument '+highlight+', was expecting true or false.');
      }
      if (highlight === false && this.children.length>0) {
        var i;
        // NOTE: going through all children, could be done cheaper (but the
        // assumption is a redraw is done anyway, so this shouldn't matter)
        for (i=0;i<this.children.length;i++) {
          this.children[i].setHighlight(false);
        }
      }
    }

  });

});
