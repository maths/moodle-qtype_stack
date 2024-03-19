$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/PTabContainer.js");

$require("org/mathdox/formulaeditor/presentation/Node.js");
$require("org/mathdox/formulaeditor/presentation/PArray.js");

$main(function(){

  /**
   * Representation of several tabs, [including a method to switch between
   * them]
   */
  org.mathdox.formulaeditor.presentation.PTabContainer =
    $extend(org.mathdox.formulaeditor.presentation.Node, {
    /*
     * index of the current tab
     */
    currenttab: null,

    tabBarSize : 20,

    margin: 2.0,

    /**
     * Draws the current tab to the canvas.
     *
     * See also: org.mathdox.formulaeditor.presentation.Node.draw
     */
    draw : function(canvas, context, x, y, invisible) {

      if (this.current === null) {
        this.dimensions = { top:y, left:x, width:0, height:0 };
        return this.dimensions;
      }

      if ((this.children[this.current] === undefined) || (this.children[this.current] === null)) {
        this.dimensions = { top:y, left:x, width:0, height:0 };
        return this.dimensions;
      }

      /* calculate maximum dimensions */
      var dimArray = [];
      var maxDim;
      var boxDim;
      var tabBoxDim;
      var i;

      if (this.showTabBar()) {
        for (i=0; i< this.children.length; i++) {
          dimArray.push(this.children[i].draw(canvas,context,x,y+this.tabBarSize+this.margin,true));
        }
        maxDim = this.maxDimensions(x,y+this.tabBarSize+this.margin,dimArray);

        boxDim = { 
          top: maxDim.top - this.tabBarSize - this.margin, 
          left: maxDim.left, 
          width: maxDim.width, 
          height: this.tabBarSize
        };


        for (i=0; i< this.children.length; i++) {
          tabBoxDim = {
            top: boxDim.top,
            left: boxDim.left + (i/this.children.length)*boxDim.width,
            width: boxDim.width/this.children.length,
            height: boxDim.height
          };

          if (i == this.current) {
            if (!invisible) { 
              canvas.drawBox(tabBoxDim, "#00F", "#AAF"); 
            }
          } else {
            if (!invisible) { 
              canvas.drawBox(tabBoxDim, "#00F", "#DDF");
            }
          }
        }
        
        if (!invisible) { 
          canvas.drawBox(boxDim, "#00F");
        }
  
        this.children[this.current].draw(canvas, context, x, y + this.tabBarSize + this.margin, 
          invisible);
    
        this.dimensions = {
          top: maxDim.top - this.tabBarSize - this.margin,
          left: maxDim.left, 
          width: maxDim.width, 
          height: maxDim.height+this.tabBarSize + this.margin
        };
      } else { /* only 1 child, don't draw a bar */
        this.dimensions = this.children[0].draw(canvas, context, x, y, invisible);
      }

      return this.dimensions;
    },

    handleMouseClick : function (x,y,redraw) {
      var palcoords ;
      var index;

      if (this.showTabBar()) {
        if (y < this.dimensions.top + this.tabBarSize) {
          /* inside tabbar, insert nothing */
          index = Math.floor((x - this.dimensions.left) / 
            (this.dimensions.width) * this.children.length);
          
          this.current = index;

          redraw();

          return null;
        }
      }

      palcoords = this.children[this.current].getCoordinatesFromPosition(x,y);

      return {
        tab: this.current,
        row: palcoords.row,
        col: palcoords.col
      };
    },

    handleMouseMove : function (x,y,redraw) {
      if(x==null || y==null) {
	if (this.children[this.current].highlight) {
          this.children[this.current].highlight = null;

	  redraw();
	}

	return;
      }

      if (this.showTabBar()) {
        if (y < this.dimensions.top + this.tabBarSize) {

          if(this.children[this.current].highlight) {
            this.children[this.current].highlight = null;

	    redraw();
          }

          return;
        }
      }

      var oldhighlight = this.children[this.current].highlight;
      this.children[this.current].highlight = 
          this.children[this.current].getCoordinatesFromPosition(x,y);

      if ((!oldhighlight || !this.children[this.current].highlight) ||  
        (oldhighlight.col != this.children[this.current].highlight.col ||
          oldhighlight.row != this.children[this.current].highlight.row)) {

	redraw();
      } 

    },

    initialize : function() {
      if (arguments.length >0) {
        this.children = Array.prototype.slice.call(arguments);
        this.current = 0;
      }
      this.updateChildren();
    },

    /**
     * Returns a copy of this presentation object, without index information
     * To be used for copy/paste or undo. See also presentation/Node.js
     */
    copy : function() {
      return this.clone.apply(this, this.copyArray(this.children));
    },

    showTabBar : function() {
      return (this.children.length>1);
    }
  });
});
