$package("org.mathdox.formulaeditor.presentation");

$identify("org/mathdox/formulaeditor/presentation/Row.js");

//$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/presentation/Node.js");
$require("org/mathdox/formulaeditor/presentation/BlockSymbol.js");
$require("org/mathdox/formulaeditor/presentation/Symbol.js");
$require("org/mathdox/formulaeditor/presentation/SuperscriptSymbol.js");

$main(function(){

  /**
   * Representation of a row of mathematical expressions in the presentation
   * tree.
   */
  org.mathdox.formulaeditor.presentation.Row =
    $extend(org.mathdox.formulaeditor.presentation.Node, {

      initialize : function() {

        var SuperscriptSymbol = org.mathdox.formulaeditor.presentation.SuperscriptSymbol;
        var Symbol = org.mathdox.formulaeditor.presentation.Symbol;
        var BlockSymbol = org.mathdox.formulaeditor.presentation.BlockSymbol;
        var i; // counter

        if (arguments.length == 1 && typeof(arguments[0]) == "string") {
          var string = arguments[0];
          var array = [];
          for (i=0; i<string.length; i++) {
            array.push(this.newSymbol(string.charAt(i)));
          }
          return arguments.callee.parent.initialize.apply(this, array);
        }
        else {
          var children = Array.prototype.slice.call(arguments);

          if (children.length === 0) {
            children = [];
            children.push(null);
          }

          for (i=0; i<children.length; i++) {
            // an "empty" box is a symbol with the empty string
            if ((children[i] === null) || (children[i] === undefined)) {
              children[i] = new BlockSymbol();
            }
          }
          return arguments.callee.parent.initialize.apply(this, children);
        }

      },

      /**
       * Draws the row to the canvas.
       *
       * See also: org.mathdox.formulaeditor.presentation.Node.draw
       */
      draw : function(canvas, context, x, y, invisible) {

        if (this.children.length > 0) {

          // use following variables to maintain track of the bounding rectangle
          var left   = x;
          var top    = y;
          var right  = x;
          var bottom = y;


          // go through all child nodes in the row
          for (var i=0; i<this.children.length; i++) {

            var child = this.children[i];

            // draw the current child node
            var dimensions = child.draw(canvas, context, right, y, invisible);

            // update the dimensions of the bounding rectangle
            left   = Math.min(left, dimensions.left);
            top    = Math.min(top, dimensions.top);
            right  = Math.max(right, dimensions.left + dimensions.width);
            bottom = Math.max(bottom, dimensions.top + dimensions.height);

          }

          // return information about the bounding rectangle
          this.dimensions = {
            left:   left,
            top:    top,
            width:  right - left,
            height: bottom - top
          };

          return this.dimensions;

        }
        else {
          var fontSizeModifier = 0;
          if (context.fontSizeModifier!== undefined && context.fontSizeModifier !== null) {
            fontSizeModifier = context.fontSizeModifier;
          }

          this.dimensions = canvas.drawFBox(x, y, true, null, null, fontSizeModifier);

          if (!invisible && this.parent) {
            canvas.drawFBox(x, y, invisible);
          }

          return this.dimensions;

        }

      },

      /**
       * Returns the MathML presentation of the node.
       */
      getMathML: function(mrow) {
        var presentation = org.mathdox.formulaeditor.presentation;
        var result = [];
        var mode = "none";
        var newmode = "none";
        var arg = [];

        /* for all children add the corresponding mathml */
        /* TODO: do tricks for mi/mo/mn */

        var i;

        for (i = 0; i < this.children.length; i++) {
          if (this.children[i] instanceof presentation.Symbol) {
            newmode = "symbol";
          } else {
            newmode = "none";
          }

          if (newmode != mode) {
            if (mode != "none") {
              // TODO: do magic stuff
              // add result to result var
              arg = [];
            }
            if (newmode != "none") {
              arg = [];
            }
          } 
          if (newmode == "none") {
            result.push(this.children[i].getMathML());
          } else { // mode = "symbol"
            arg.push(this.children[i].value);
          }

          mode = newmode;
        }

        /**
         * wrap in mrow when not implicit
         * if length is 1, then skip (already 1 term, no row needed)
         */
        if (mrow !== undefined && mrow !== null && mrow === true && result.length != 1) {
          result = "<mrow>" + result.join("") + "</mrow>";
        } else {
          result = result.join("");
        }
      },

      /**
       * Generates a symbol in the correct style
       */
      newSymbol: function(character) {
	var SuperscriptSymbol = org.mathdox.formulaeditor.presentation.SuperscriptSymbol;
	var Symbol = org.mathdox.formulaeditor.presentation.Symbol;

        if (character == " ") {
          // spaces do not have a value
          return new Symbol([""," "]);
        } else if (((character >= 'a') && (character <='z'))||
                   ((character >= 'A') && (character <='Z'))) {
          return new Symbol(character, "math");
        } else if (character == "'" || character =="′" ) {
          // quote or U+2032 prime
          return new SuperscriptSymbol(character);
        } else {
          return new Symbol(character);
        }
      },

      /**
       * Handles an onkeydown event from the browser. Returns false when the
       * event has been handled and should not be handled by the browser,
       * returns true otherwise.
       */
      onkeydown : function(event, editor) {

        // only handle keypresses where alt, ctrl and shift are not held
        if (!event.altKey && !event.ctrlKey && !event.shiftKey) {

          // handle backspace and delete
          switch(event.keyCode) {

            case  8: // backspace
              var position = editor.cursor.position;
              if (position.index > 0) {
                // test if we should delete
                var del = this.children[position.index - 1].deleteItem();

                if (del) {
                  this.remove(position.index - 1);
                  position.index--;
                  // after deleting the last character, add a new input box
                  if (this.isEmpty()) {
                    this.insert(0);
                  }
                }
                editor.redraw();
                editor.save();
              }
              return false;

            case 46: // delete
              var position = editor.cursor.position;
              if (position.index <this.children.length) {
                // test if we should delete
                var del = this.children[position.index].deleteItem();

                if (del) {
                  this.remove(position.index);
                  // after deleting the last character, add a new input box
                  if (this.isEmpty()) {
                    this.insert(0);
                  }
                }
                editor.redraw();
                editor.save();
              }
              return false;
          }
        }

        // pass the event back to the browser
        return true;

      },

      /**
       * Handles an onkeypress event from the browser. Returns false when the
       * event has been handled and should not be handled by the browser,
       * returns true otherwise.
       */
      onkeypress : function(event, editor) {

        // only handle keypresses where alt and ctrl are not held
        if (!event.altKey && !event.ctrlKey) {

          var canvas    = editor.canvas;
          var fontName  = canvas.fontName;
          var fontSize  = canvas.fontSize;
          var character = String.fromCharCode(event.charCode);

          // XXX enter, fire DOMActivate event in the future
          if (event.charCode == 13) {
            return false;
          }

          // see whether there is a character for pressed key in current font
          if (canvas.getSymbolData(character)) {

            var moveright;

            // insert the character into the row, and move the cursor
	    moveright = this.insert(editor.cursor.position.index,
	      this.newSymbol(character));

            if (moveright) {
              editor.cursor.moveRight();
            }

            editor.redraw();
            editor.save();
            return false;

          }

        }

        // pass the event back to the browser
        return true;

      },

      /**
       * Flattens this row, meaning that all child nodes that are rows
       * themselves will be embedded into this row.
       */
      flatten : function() {

        var Row = org.mathdox.formulaeditor.presentation.Row;

        // call flatten on the child nodes
        arguments.callee.parent.flatten.apply(this);

        // go through all children
        var children = this.children;
        for (var i=0; i<children.length; i++) {
          var child = children[i];

          // check whether the child is a row
          if (child instanceof Row) {
            // insert the child node's children into the list of children
            children.splice.apply(children, [i,1].concat(child.children));
          }

        }
        this.updateChildren();

      },

      getCursorPosition : function(x, y) {

        var count = this.children.length;
        for (var i=0; i<count; i++) {
          var dimensions = this.children[i].dimensions;
          if (x < dimensions.left + dimensions.width || i == count - 1) {
            return this.children[i].getCursorPosition(x,y);
          }
        }

        return { row: this, index: 0 };

      },

      getFirstCursorPosition : function(index) {
        if (index === null || index === undefined || index > 0) {
          return this.getFollowingCursorPosition();
        }
        else {
          if (this.parent !== null) {
            return this.parent.getFirstCursorPosition();
          }
          else {
            return null;
          }
        }
      },

      getLastCursorPosition : function(index) {
        if (index === null || index === undefined ||
            index < this.children.length) {
          return this.getPrecedingCursorPosition();
        }
        if (this.parent !== null) {
          return this.parent.getLastCursorPosition();
        }
        else {
          return null;
        }
      },

      /**
       * Returns the cursor position following the specified index.
       * See also Node.getFollowingCursorPosition(index).
       */
      getFollowingCursorPosition : function(index, descend) {

        // default value for descend
        if (descend === null || descend === undefined) {
          descend = true;
        }

        // when index is not specified, return the first position in this row
        if (index === null || index === undefined) {
          return { row : this, index : 0 };
        }

        // ask the child at the specified index for the cursor position
        if (index < this.children.length) {
          var result = null;
          if (descend) {
            result = this.children[index].getFollowingCursorPosition();
          }
          if (result === null) {
            // when the child can not provide a cursor position, shift the
            // cursor one position in this row
            result = { row : this, index : index + 1 };
          }
          return result;
        }

        // when we're at the end of the row, ask the parent of the row for the
        // position following this row
        if (this.parent !== null) {
          return this.parent.getFollowingCursorPosition(this.index, false);
        }

        // no suitable cursor position could be found
        return null;

      },

      getPrecedingCursorPosition : function(index, descend) {

        // default value for descend
        if (descend === null || descend === undefined) {
          descend = true;
        }

        // when index is not specified, return the last position in this row
        if (index === null || index === undefined) {
          return { row : this, index : this.children.length };
        }

        // ask the child at the specified index for the cursor position
        if (index > 0) {
          var result = null;
          if (descend) {
            result = this.children[index-1].getPrecedingCursorPosition();
          }
          if (result === null) {
            // when the child can not provide a cursor position, shift the
            // cursor one position in this row
            result = { row : this, index : index - 1 };
          }
          return result;
        }

        // when we're at the beginning of the row, ask the parent of the row for
        // the position preceding this row
        if (this.parent !== null) {
          return this.parent.getPrecedingCursorPosition(this.index, false);
        }

        // no suitable cursor position could be found
        return null;

      },

      getLowerCursorPosition : function(index, x) {
        if (index === null || index === undefined) {
          var minimumDistance = null;
          var bestIndex = 0;
          for (var i=0; i<=this.children.length; i++) {
            var left;
            if (i<this.children.length) {
              left = this.children[i].dimensions.left;
            }
            else {
              if (this.children.length > 0) {
                var dimensions = this.children[this.children.length-1].dimensions;
                left = dimensions.left + dimensions.width;
              }
              else {
                left = this.dimensions.left;
              }
            }
            var distance = Math.abs(left-x);
            if (minimumDistance === null || distance < minimumDistance) {
              minimumDistance = distance;
              bestIndex = i;
            }
          }
          if (this.children[bestIndex] !== null &&
            this.children[bestIndex] !== undefined) {
            return this.children[bestIndex].getLowerCursorPosition(null, x);
          }
          else {
            return { row: this, index : bestIndex };
          }
        }
        else {
          return this.parent.getLowerCursorPosition(this.index, x);
        }
      },

      getHigherCursorPosition : function(index, x) {
        if (index === null || index === undefined) {
          var minimumDistance = null;
          var bestIndex = 0;
          for (var i=0; i<=this.children.length; i++) {
            var left;
            if (i<this.children.length) {
              left = this.children[i].dimensions.left;
            }
            else {
              if (this.children.length > 0) {
                var dimensions = this.children[this.children.length-1].dimensions;
                left = dimensions.left + dimensions.width;
              }
              else {
                left = this.dimensions.left;
              }
            }
            var distance = Math.abs(left-x);
            if (minimumDistance === null || distance < minimumDistance) {
              minimumDistance = distance;
              bestIndex = i;
            }
          }
          if (this.children[bestIndex] !== null &&
            this.children[bestIndex] !== undefined) {
            return this.children[bestIndex].getHigherCursorPosition(null, x);
          }
          else {
            return { row: this, index : bestIndex };
          }
        }
        else {
          return this.parent.getHigherCursorPosition(this.index, x);
        }
      },

      isEmpty : function() {
        return (this.children.length === 0);
      },

      insert : function(index, node, fillempty) {
        var BlockSymbol = org.mathdox.formulaeditor.presentation.BlockSymbol;
        var newindex = index;
        var moveright = true;

        if ((fillempty === null) || (fillempty === undefined)) {
          fillempty = true;
        }
        if ((node === null) || (node === undefined)) {
          node = new BlockSymbol();
        }

        if (fillempty && index<=this.children.length &&
          this.children[index] instanceof BlockSymbol) {
          this.children.splice(index, 1, node);
        } else if (fillempty && index-1>=0 &&
            this.children[index-1] instanceof BlockSymbol) {
          this.children.splice(index-1, 1, node);
          newindex = index - 1;
          moveright = false; // do not move right after inserting now
        } else {
          this.children.splice(newindex, 0, node);
        }
        this.updateChildren(newindex);

        //alert("index:"+index+" -> "+newindex+"\nmoveright: "+moveright);
        return moveright;
      },

      replace : function(index, node) {
        this.children.splice(index, 1, node);
        this.updateChildren(index);
      },

      remove : function(begin, end) {
        var result;
        if (end === null || end === undefined) {
          result = this.children[begin];
          this.children.splice(begin, 1);
          this.updateChildren(begin);
          return result;
        }
        else {
          result = new org.mathdox.formulaeditor.presentation.Row();
          result.initialize.apply(result, this.children.splice(begin, end-begin));
          this.updateChildren(begin);
          return result;
        }
      },

      getSemantics : function(context, begin, end, start, backward) {

        // assign default values to parameters
        if (begin    === null || begin    === undefined ) {
          begin    = 0;
        }
        if (end      === null || end      === undefined ) {
          end      = this.children.length;
        }
        if (start    === null || start    === undefined) {
          start    = "start";
        }
        if (backward === null || backward === undefined) {
          backward = false;
        }

        // use the expressionparser to parse the elements of the row
        var ContextParser;
        ContextParser = org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser;

        var Parser;
        Parser = new ContextParser().getParser(context); //for now use empty context

        // create the input for the parser by serializing the row elements
        var input = "";
        // array to adjust for spaces (which have no semantics)
        var originalIndex = new Array();
        // adjust the index for the begin (XXX: shouldn't be necessary)
        originalIndex[begin] = begin;
        // counting spaces so far
        var adjustIndex = 0;

        // go through the row elements
        var children = this.children;
        for (var i=begin; i<end; i++) {

          // start a new variable scope
          (function(){

            // act differently based on the type of the row element
            var child = children[i];
            if (child instanceof org.mathdox.formulaeditor.presentation.Symbol) {

              // if the row element is a symbol, add its value to input string
              input = input + child.value;

              if (child.value == "") {
                // special case: space, count it
                adjustIndex=adjustIndex+1;
              }
            }
            else {

              // record the index of this row element in the parser input string
              var inputindex = input.length;

              // add a dummy to the input string
              input = input + '#';

              // retrieve the semantic tree node that represents the row element
              var semantics = child.getSemantics(context);

              // extend the parser so that it will parse the dummy into the
              // correct semantic tree node
              var extension = {};
              extension[semantics.rule] =
                function(context, index, result, continuation) {

                  var parent = arguments.callee.parent;

                  if (!context.backward && index == inputindex) {
                    continuation(index+1, result.concat([semantics.value]));
                  }
                  else if(context.backward && index - 1 == inputindex) {
                    continuation(index-1, [semantics.value].concat(result));
                  }
                  else {
                    parent[semantics.rule](context, index, result, continuation);
                  }

                };

              Parser = $extend(Parser, extension);

            }

          })();

          // adjust the index for this position
          originalIndex[i] = i + adjustIndex;
        }
        // adjust the index for the end
        originalIndex[end] = end + adjustIndex;

        // use the constructed parser and input to parse the row
        var parsebegin = backward ? input.length : 0           ;
        var parseend   = backward ? 0            : input.length;
        var parsed = new Parser().parse(input, parsebegin, backward, start);

        // return the result of parsing
        return {
          value : parsed.index == parseend ? parsed.value : null,
          index : originalIndex[parsed.index + begin],
          rule  : "braces"
        };

      }

    });

});
