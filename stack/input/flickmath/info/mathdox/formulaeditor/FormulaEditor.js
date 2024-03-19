$package("org.mathdox.formulaeditor");

$identify("org/mathdox/formulaeditor/FormulaEditor.js");

// load make/maker functions
$require("com/oreilly/javascript/tdg/make.js");
// load XMLHttpRequest methods
$require("com/oreilly/javascript/tdg/XMLHttp.js");

var Drag;
$require("net/youngpup/dom-drag/dom-drag.js", function() { return Drag; });

$require("org/mathdox/debug/Debug.js");

$require("org/mathdox/formulaeditor/parsing/expression/KeywordList.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/parsing/openmath/KeywordList.js");
$require("org/mathdox/formulaeditor/parsing/openmath/OpenMathParser.js");
$require("org/mathdox/formulaeditor/parsing/mathml/MathMLParser.js");
$require("org/mathdox/formulaeditor/Canvas.js");
$require("org/mathdox/formulaeditor/MathCanvasFill.js");
$require("org/mathdox/formulaeditor/Cursor.js");
$require("org/mathdox/formulaeditor/EventHandler.js");
$require("org/mathdox/formulaeditor/Services.js");

$require("org/mathdox/formulaeditor/version.js");

$require("org/mathdox/formulaeditor/Options.js");

$require("org/mathdox/formulaeditor/Palettes.js");

$require("org/mathdox/formulaeditor/modules/keywords.js");
$require("org/mathdox/formulaeditor/modules/variables.js");

$require("org/mathdox/formulaeditor/modules/arithmetic/abs.js");
$require("org/mathdox/formulaeditor/modules/arithmetic/divide.js");
$require("org/mathdox/formulaeditor/modules/arith1/minus.js");
$require("org/mathdox/formulaeditor/modules/arith1/plus.js");
$require("org/mathdox/formulaeditor/modules/arithmetic/power.js");
$require("org/mathdox/formulaeditor/modules/arithmetic/product.js");
$require("org/mathdox/formulaeditor/modules/arithmetic/root.js");
$require("org/mathdox/formulaeditor/modules/arithmetic/sum.js");
$require("org/mathdox/formulaeditor/modules/arithmetic/times.js");
$require("org/mathdox/formulaeditor/modules/arithmetic/unary_minus.js");

$require("org/mathdox/formulaeditor/modules/calculus1/defint.js");
$require("org/mathdox/formulaeditor/modules/calculus1/diff.js");
$require("org/mathdox/formulaeditor/modules/calculus1/int.js");

$require("org/mathdox/formulaeditor/modules/complex1/complex_cartesian.js");

$require("org/mathdox/formulaeditor/modules/editor1/palette.js");

$require("org/mathdox/formulaeditor/modules/fns1/lambda.js");

$require("org/mathdox/formulaeditor/modules/integer1/factorial.js");

$require("org/mathdox/formulaeditor/modules/interval1/interval_cc.js");
$require("org/mathdox/formulaeditor/modules/interval1/interval_co.js");
$require("org/mathdox/formulaeditor/modules/interval1/interval_oc.js");
$require("org/mathdox/formulaeditor/modules/interval1/interval_oo.js");

$require("org/mathdox/formulaeditor/modules/linalg/matrix.js");

$require("org/mathdox/formulaeditor/modules/limit1/limit.js");

$require("org/mathdox/formulaeditor/modules/list1/list.js");

$require("org/mathdox/formulaeditor/presentation/Editor.js");
$require("org/mathdox/formulaeditor/presentation/Root.js");

$require("org/mathdox/formulaeditor/modules/logic1/and.js");
$require("org/mathdox/formulaeditor/modules/logic1/equivalent.js");
$require("org/mathdox/formulaeditor/modules/logic1/implies.js");
$require("org/mathdox/formulaeditor/modules/logic1/not.js");
$require("org/mathdox/formulaeditor/modules/logic1/or.js");

$require("org/mathdox/formulaeditor/modules/nums1/rational.js");

$require("org/mathdox/formulaeditor/modules/permutation1/left_compose.js");
$require("org/mathdox/formulaeditor/modules/permutation1/permutation.js");

$require("org/mathdox/formulaeditor/modules/relation1/approx.js");
$require("org/mathdox/formulaeditor/modules/relation1/eq.js");
$require("org/mathdox/formulaeditor/modules/relation1/geq.js");
$require("org/mathdox/formulaeditor/modules/relation1/gt.js");
$require("org/mathdox/formulaeditor/modules/relation1/leq.js");
$require("org/mathdox/formulaeditor/modules/relation1/lt.js");
$require("org/mathdox/formulaeditor/modules/relation1/neq.js");
// for rewriting 1<x<2
$require("org/mathdox/formulaeditor/modules/relation1/IntervalNotation.js");

$require("org/mathdox/formulaeditor/modules/set1/in.js");

$main(function(){

  /**
   * Maintain a list of all formula editors that are initialized.
   */
  var editors = [];

  var palettes;
 
  var debug = null;

  /**
   * Class that represents a formula editor.
   */
  org.mathdox.formulaeditor.FormulaEditor = $extend(Object, {

    /**
     * The textarea that is being replaced.
     */
    textarea : null,

    /**
     * The canvas that will be used for rendering formulae.
     */
    canvas : null,

    /**
     * The current presentation tree.
     */
    presentation : null,

    /**
     * The keyboard cursor.
     */
    cursor : null,

    /**
     * The palette (if any)
     */
    palette : null,

    /**
     * Boolean that indicates whether the palette should be shown
     */
    showPalette : true,

    /**
     * Indicates whether this formula editor has the focus.
     */
    hasFocus : false,

    addPalette : function() {
      //canvas.parentNode.insertBefore(palette, canvas);
      var palcanvas = document.createElement("canvas");

      if (! org.mathdox.formulaeditor.options.ignoreTextareaStyle) {
        // copy style attributes from the textarea to the canvas
        for (x in this.textarea.style) {
          try {
            palcanvas.style[x] = this.textarea.style[x];
          }
          catch(exception) {
            // skip
          }
        }
      }

      // fix for opera
      if ( G_vmlCanvasManager === undefined) {
        if (palcanvas.style.getPropertyValue("height") != "") {
          try {
            palcanvas.style.removeProperty("height");
          } catch(exception) {
            // skip
          }
        }
      }

      // set the style attributes that determine the look of the palette
      if (org.mathdox.formulaeditor.options.paletteStyle) {
        // use paletteStyle option if available
        palcanvas.setAttribute("style",
                org.mathdox.formulaeditor.options.paletteStyle);
      } else {
        // no paletteStyle option available -> set default style
        palcanvas.style.border          = "2px solid #99F";
        palcanvas.style.verticalAlign   = "middle";
        palcanvas.style.cursor          = "pointer";
        palcanvas.style.padding         = "0px";
        palcanvas.style.backgroundColor = "white";
      }

      // clear possible display none
      palcanvas.style.display = "";
      // set a classname so the user can extend the style
      palcanvas.className           = "formulaeditorpalette";

      if (!palettes) {
        palettes = [];
      }
      this.palette = new org.mathdox.formulaeditor.Palette();
      palettes.push(this.palette);

      // special case: draggable canvas TODO
      if (org.mathdox.formulaeditor.options.dragPalette !== undefined &&
          org.mathdox.formulaeditor.options.dragPalette === true) {
        // create root 
        var root = document.createElement("div");
        root.style.left = "150px";
        root.style.top = "0px";
        root.style.position = "relative";

        var subdiv = document.createElement("div");

        // float is a keyword, to change the css float style, use cssFloat
        subdiv.style.cssFloat="right";

        // create handle
        var handle = document.createElement("div");

        handle.style.width = "200px";
        handle.style.marginLeft = "50px";
        handle.style.height = "10px";
        handle.style.cursor = "move";

        subdiv.appendChild(handle);
        subdiv.appendChild(palcanvas);
        root.appendChild(subdiv);

        // add root, handle and palette to the document
        if (this.textarea.parentNode.tagName.toLowerCase() == "p") {
          // NOTE: should not be added inside a para
          var para = this.textarea.parentNode;
          var paraparent = this.textarea.parentNode.parentNode;
          // code to insert after the paragraph
          if (G_vmlCanvasManager) {
            paraparent.replaceChild(root, this.canvas.canvas);
            paraparent.insertBefore(this.canvas.canvas, root);
          } else {
            paraparent.replaceChild(root, para);
            paraparent.insertBefore(para, root);
          }
          // to insert before the paragraph use
          //paraparent.insertBefore(root, para);
        } else {
          this.textarea.parentNode.insertBefore(root, this.textarea);
        }

        // initialize dragging script
        Drag.init(handle, root);

        var borderTopColor = "";
        
        // getting computed style: see also page 380 of J:TDG 5th edition
        if (palcanvas.currentStyle) { // Try simple IE API first
          borderTopColor = palcanvas.currentStyle.borderTopColor;
        } else if (window.getComputedStyle) {  // Otherwise use W3C API
          borderTopColor = 
            window.getComputedStyle(palcanvas, null).borderTopColor;
        }

        if (borderTopColor !== "") {
          handle.style.backgroundColor = borderTopColor;
        } else {
          handle.style.backgroundColor = "red";
        }

          this.palette.htmlelement = root;
      } else {
        // insert palcanvas in the document before the textarea 
        // in case of G_vmlCanvasManager, check if the parent is a p
        // if it is then put the canvas after the paragraph
        if (G_vmlCanvasManager && this.textarea.parentNode.tagName.toLowerCase() == "p") {
          // NOTE: should not be added inside a para
          var para = this.textarea.parentNode;
          var paraparent = this.textarea.parentNode.parentNode;
          // code to insert after the paragraph
          paraparent.replaceChild(palcanvas, para);
          paraparent.insertBefore(para, palcanvas);
          // to insert before the paragraph use
          //paraparent.insertBefore(root, para);
        } else {
          this.textarea.parentNode.insertBefore(palcanvas, this.textarea);
        }
      }
      if (G_vmlCanvasManager) {
        /* reinitialize canvas */
        palcanvas = G_vmlCanvasManager.initElement(palcanvas);
      }

      // Initialize the canvas. This is only needed in Internet Explorer,
      // where Google's Explorer Canvas library handles canvases.
      // NOTE: this should be done after putting the canvas in the DOM tree
      
      this.palette.initialize(palcanvas);
    },
    /**
     * checkClass(classNames, className): function to help check if an HTML
     * element contains a certain class.
     */
    checkClass: function(classNames, className) {
      var words = classNames.split(" ");
      var i;

      for (i=0; i<words.length; i++) {
        if (words[i] == className) {
          return true;
        }
      }
      return false;
    },

    togglePalette:function () {
      if (this.palette) {
        // remove existing palette
        org.mathdox.formulaeditor.Palette.removePalette(this.palette);
      } else {
        // add new palette
        this.addPalette();
      }
    },
    /**
     * Hides the specified textarea and replaces it by a canvas that will be
     * used for rendering formulae.
     */
    initialize : function(textarea, canvas) {
      var x;

      if (textarea) {

        var Cursor    = org.mathdox.formulaeditor.Cursor;
        var MathCanvas = org.mathdox.formulaeditor.MathCanvas;

        // ensure that there isn't already an editor for this textarea
        for (var i=0; i<editors.length; i++) {
          if (editors[i].textarea == textarea) {
            return editors[i];
          }
        }

        // check whether a new canvas needs to be added.
        if (!canvas) {

          // create an HTML canvas
          canvas = document.createElement("canvas");

          // copy style attributes from the textarea to the canvas
          if (! org.mathdox.formulaeditor.options.ignoreTextareaStyle) {
            for (x in textarea.style) {
              try {
                canvas.style[x] = textarea.style[x];
              }
              catch(exception) {
                // skip
              }
            }
          }

          // fix for opera
          if ( G_vmlCanvasManager === undefined) {
  	    if (canvas.style.getPropertyValue("height") != "") {
                try {
                  canvas.style.removeProperty("height");
  	      } catch(exception) {
  	        // skip
  	      }
  	    }
  	  }

          canvas.className = "mathdoxformula";

          // set the style attributes that determine the look of the editor
          if (textarea.getAttribute("style") !== null && 
               textarea.getAttribute("style") !== undefined &&
               textarea.getAttribute("style").value !== undefined) {
            // same style as the textarea
            canvas.setAttribute("style", textarea.getAttribute("style"));
          } else if (org.mathdox.formulaeditor.options.inputStyle) {
            // textarea has no style use inputStyle option if available
            canvas.setAttribute("style",
                    org.mathdox.formulaeditor.options.inputStyle);
          } else {
            // textarea has no style and no inputStyle option available
            // set default style
            canvas.style.border        = "1px solid #99F";
            canvas.style.verticalAlign = "middle";
            canvas.style.cursor        = "text";
            canvas.style.padding       = "0px";
          }

          // insert canvas in the document before the textarea 
          // in case of G_vmlCanvasManager, check if the parent is a p
          // if it is then put the canvas after the paragraph
          
          if (G_vmlCanvasManager && textarea.parentNode.tagName.toLowerCase() == "p") {
            // NOTE: should not be added inside a para
            var para = textarea.parentNode;
            var paraparent = textarea.parentNode.parentNode;
            // code to insert after the paragraph
            paraparent.replaceChild(canvas, para);
            paraparent.insertBefore(para, canvas);
            // to insert before the paragraph use
            //paraparent.insertBefore(root, para);
          } else {
            textarea.parentNode.insertBefore(canvas, textarea);
          }

          // Initialize the canvas. This is only needed in Internet Explorer,
          // where Google's Explorer Canvas library handles canvases.
          if (G_vmlCanvasManager) {
            canvas = G_vmlCanvasManager.initElement(canvas);
          }

        }

        // register the textarea 
        this.textarea = textarea;

        // register a new mathcanvas
        this.canvas   = new MathCanvas(canvas);

        // check whether a palette needs to be added
        if (this.checkClass(textarea.className, "mathdoxpalette")) {
          /* specified: show a palette */
          this.showPalette = this.showPalette && true;
        } else if (this.checkClass(textarea.className, "mathdoxnopalette")) {
          /* specified: don't show a palette */
          this.showPalette = this.showPalette && false;
        } else if (org.mathdox.formulaeditor.options.paletteShow == "all") {
          /* when unspecified, always show a palette */
          this.showPalette = this.showPalette && true;
        } else if (org.mathdox.formulaeditor.options.paletteShow == "none") {
          /* when unspecified, never show a palette */
          this.showPalette = this.showPalette && false;
        } else if (org.mathdox.formulaeditor.options.paletteShow == "once") {
          /* only add a palette if no palette is present on the page yet */
          this.showPalette = this.showPalette && (!palettes);
        } else {
          /* default: show only one palette */
          /* only add a palette if no palette is present on the page yet */
          this.showPalette = this.showPalette && (!palettes);
        }

        this.showPalette = this.showPalette &&
          (this.checkClass(textarea.className, "mathdoxpalette") || 
          (!this.checkClass(textarea.className, "mathdoxnopalette") && 
            !palettes)
          );
        if (this.showPalette) { 
          this.addPalette();
        }

        // hide the textarea XXX
        if (!this.checkClass(textarea.className, "mathdoxvisibletextarea")) {
          textarea.style.display = "none";
        }

        this.load();

        // initialize the cursor, and draw the presentation tree
        this.cursor = new Cursor(this.presentation.getFollowingCursorPosition());
        this.draw();

        // register this editor in the list of editors.
        editors.push(this);

      }

    },

    load : function() {

      var Parser    = org.mathdox.formulaeditor.parsing.openmath.OpenMathParser;
      var Editor    = org.mathdox.formulaeditor.presentation.Editor;
      var Row       = org.mathdox.formulaeditor.presentation.Row;

      // read any OpenMath code that may be present in the textarea
      var paletteEnabled;
      try {
        var parsed = new Parser().parse(this.textarea.value);
        if (org.mathdox.formulaeditor.options.useBar) {
          if (this.palette) {
            paletteEnabled = true;
          } else {
            paletteEnabled = false;
          }
          this.presentation = new Editor(parsed.getPresentation(this.getPresentationContext()), paletteEnabled);
        } else {
          this.presentation = new Row(parsed.getPresentation(this.getPresentationContext()));
          this.presentation.flatten();
        }
      }
      catch(exception) {
        if (org.mathdox.formulaeditor.options.useBar) {
          if (this.palette) {
            paletteEnabled = true;
          } else {
            paletteEnabled = false;
          }
          this.presentation = new Editor(null, paletteEnabled);
        } else {
          this.presentation = new Row();
        }
      }

    },

    loadMathML: function(xmlString) {
      org.mathdox.formulaeditor.FormulaEditor.addDebug("loading MathML");
      var Parser    = org.mathdox.formulaeditor.parsing.mathml.MathMLParser;
      var Editor    = org.mathdox.formulaeditor.presentation.Editor;
      var Row       = org.mathdox.formulaeditor.presentation.Row;

      // read any OpenMath code that may be present in the textarea
      var paletteEnabled;
      //try {
        var parsed = new Parser().parse(xmlString, this.getPresentationContext());

        org.mathdox.formulaeditor.FormulaEditor.addDebug("parsed: "+parsed);
        if (org.mathdox.formulaeditor.options.useBar) {
          if (this.palette) {
            paletteEnabled = true;
          } else {
            paletteEnabled = false;
          }
          this.presentation = new Editor(parsed, paletteEnabled);
        } else {
          this.presentation = new Row(parsed);
          this.presentation.flatten();
        }
      //}
/*      catch(exception) {
        if (org.mathdox.formulaeditor.options.useBar) {
          if (this.palette) {
            paletteEnabled = true;
          } else {
            paletteEnabled = false;
          }
          this.presentation = new Editor(null, paletteEnabled);
        } else {
          this.presentation = new Row();
        }
      }*/
    },

    // TODO : move this to an onchange handler
    save : function() {

      var textarea = this.textarea;
      var openmathInfo = this.getOpenMath(true);

      // update the textarea
      if (org.mathdox.formulaeditor.options.indentXML && 
        org.mathdox.formulaeditor.options.indentXML === true) {
        textarea.value = this.indentXML(openmathInfo.value);
      } else {
        textarea.value = openmathInfo.value;
      }

      return { 
        success: openmathInfo.success,
        errorString: openmathInfo.errorString
      };
    },

    redraw : function() {
      this.canvas.clear();
      this.draw();
    },

    draw : function() {

      // TODO: move this code to a separate presentation node
      //       (equivalent to the DOM Document node)
      var dimensions;
      var drawContext = {};

      if (org.mathdox.formulaeditor.options.useBar) {
        dimensions = this.presentation.draw(this.canvas, drawContext, 0, 0, true);
      } else {
        /* add margin */
        var margin = 4.0;
        var formula_dimensions = this.presentation.draw(this.canvas, drawContext, 0, 0, true);
        dimensions = {
          top:    formula_dimensions.top    - margin,
          left:          formula_dimensions.left   - margin,
          width:  formula_dimensions.width  + 2 * margin,
          height: formula_dimensions.height + 2 * margin
        };
      }
      // XXX
      if (G_vmlCanvasManager) {
        var computedStyle;
        if (this.canvas.canvas.currentStyle !== undefined && this.canvas.canvas.currentStyle!== null) {
          // IE method 
          computedStyle = this.canvas.canvas.currentStyle;
        } else {
          computedStyle = getComputedStyle(this.canvas.canvas, null);
        }
        var dim_extra = { width:0, height:0};

        // adjust size horizontally
        var tmp;
        tmp = [ computedStyle.borderLeftWidth, 
          computedStyle.borderRightWidth, 
          computedStyle.paddingLeft, 
          computedStyle.paddingRight ];

        var i;
        var parsed;
        for (i=0;i<tmp.length;i++) {
          parsed = parseInt(tmp[i]);
          if (isFinite(parsed)) {
            dim_extra.width+=parsed;
          }
        }

        // adjust size vertically
        tmp = [ computedStyle.borderTopWidth, 
          computedStyle.borderBottomWidth, 
          computedStyle.paddingTop, 
          computedStyle.paddingBottom ];

        for (i=0;i<tmp.length;i++) {
          parsed = parseInt(tmp[i]);
          if (isFinite(parsed)) {
            dim_extra.height+=parsed;
          }
        }
        this.canvas.canvas.setAttribute("width", dimensions.width+dim_extra.width);
        this.canvas.canvas.setAttribute("height", dimensions.height+dim_extra.height);
      } else {
        this.canvas.canvas.setAttribute("width", dimensions.width);
        this.canvas.canvas.setAttribute("height", dimensions.height);
      }
      this.presentation.draw(this.canvas, drawContext, - dimensions.left, - dimensions.top);
      this.cursor.draw(this.canvas, drawContext);
    },

    /**
     * Handles an onkeydown event from the browser. Returns false when the event
     * has been handled and should not be handled by the browser, returns true
     * otherwise.
     */
    onkeydown : function(event) {

      // forward the event to the cursor object when we have the focus
      if(this.hasFocus) {
        // handle some events here
        if (event.keyCode == 116) {
          var Cursor    = org.mathdox.formulaeditor.Cursor;

          var saveResult = this.save();
          if (saveResult.success) {
            // formula can be parsed and transformed to OpenMath
            this.load();
            this.cursor = new Cursor(this.presentation.getFollowingCursorPosition());
            this.focus(); // XXX is this necessary ?
            this.redraw();
          } else {
            // formula cannot be parsed and transformed to OpenMath
            alert("The formula could not be interpreted correctly. "+
              "The error message was:\n"+saveResult.errorString);
          }

          return false;
        }
        this.focus(); // TODO: only necessary for crappy blinker implementation
        return this.cursor.onkeydown(event, this);
      }

    }, 
    decreaseSizes : function() {
      var i;
      for (i =0;i<palettes.length;i++) {
        if(palettes[i].canvas) {
          palettes[i].canvas.decreaseSize();
          palettes[i].redraw();
        }
      }
      for (i =0;i<editors.length;i++) {
        if(editors[i].canvas) {
          editors[i].canvas.decreaseSize();
          editors[i].redraw();
        }
      }
      return true;
    },
  
    increaseSizes : function() {
      var i;
      for (i=0;i<editors.length;i++) {
        if(editors[i].canvas) {
          editors[i].canvas.increaseSize();
          editors[i].redraw();
        }
      }
      for (i=0;i<palettes.length;i++) {
        if(palettes[i].canvas) {
          palettes[i].canvas.increaseSize();
          palettes[i].redraw();
        }
      }
      return true;
    },

    /**
     * Handles an onkeypress event from the browser. Returns false when the
     * event has been handled and should not be handled by the browser, returns
     * true otherwise.
     */
    onkeypress : function(event) {

      // forward the event to the cursor object when we have the focus
      if (this.hasFocus) {
        var result = true;
        if (event.ctrlKey) {
          switch(event.charCode) {
            case 43: // '+' larger
              this.increaseSizes();
              result = false;
              break;
            case 45: // '-' smaller
              this.decreaseSizes();
              result = false;
              break;
          }
        }
  
        this.focus(); // TODO: only necessary for crappy blinker implementation
        if (result) {
          result = this.cursor.onkeypress(event, this);
        }

        return result;
      }

    },

    /**
     * Returns info about the mouse event, returning {x, y}, where x and y are
     * the relative positions in the canvas. If the mouseclick was not in the
     * canvas null is returned instead.
     */
    mouseeventinfo : function(event) {  

      // retrieve the screen coordinates of the mouse click
      var mouseX = event.clientX;
      var mouseY = event.clientY;

      // retrieve the page offset, needed to convert screen coordinates to
      // document coordinates
      var pageXOffset = window.pageXOffset;
      var pageYOffset = window.pageYOffset;

      var element;
      // MSIE provides the page offset in a different way *sigh*
      if (pageXOffset === null || pageXOffset === undefined) {
        element = document.documentElement;
        if (!element || !element.scrollLeft) {
          element = document.body;
        }
        pageXOffset = element.scrollLeft;
        pageYOffset = element.scrollTop;
      }

      // calculate the document coordinates of the mouse click
      if (!event.mathdoxnoadjust) {
        mouseX += pageXOffset;
        mouseY += pageYOffset;
      }

      // calculate the document coordinates of the canvas element
      element = this.canvas.canvas;
      var x      = 0;
      var y      = 0;
      var width  = element.offsetWidth;
      var height = element.offsetHeight;

      // check for padding and border
        
      var computedStyle;
      if (element.currentStyle !== undefined && element.currentStyle!== null) {
        // IE method 
        computedStyle = element.currentStyle;
      } else {
        computedStyle = getComputedStyle(element, null);
      }

      var tmpp,tmpb;

      // only add maximum of tmpp, tmpb
      // if only 1 is finite, that is the maximum
      tmpb = parseInt(computedStyle.borderLeftWidth);
      tmpp = parseInt(computedStyle.paddingLeft);

      if ( isFinite(tmpp) && isFinite(tmpb)) {
        if (tmpp>tmpb) {
          x += tmpp;
        } else {
          x += tmpb;
        }
      } else if (isFinite(tmpp)) {
        x += tmpp;
      } else if (isFinite(tmpb)) {
        x += tmpb;
      }

      tmpb = parseInt(computedStyle.borderTopWidth);
      tmpp = parseInt(computedStyle.paddingTop);
      // only add maximum of tmpp, tmpb
      // if only 1 is finite, that is the maximum
      if ( isFinite(tmpp) && isFinite(tmpb)) {
        if (tmpp>tmpb) {
          y += tmpp;
        } else {
          y += tmpb;
        }
      } else if (isFinite(tmpp)) {
        y += tmpp;
      } else if (isFinite(tmpb)) {
        y += tmpb;
      }

      while (element) {
        x += element.offsetLeft;
        y += element.offsetTop;

        element = element.offsetParent;
      }

      // check whether the mouse click falls in the canvas element
      if (x<=mouseX && mouseX<=x+width && y<=mouseY && mouseY<=y+height) {
        // we have focus
        // forward the mouse click to the cursor object
        return { x:mouseX-x, y:mouseY-y };
      }
      else {
        // we do not have focus
        return null;
      }

    },
    /**
     * Handles an onmousedown event from the browser. Returns false when the
     * event has been handled and should not be handled by the browser, returns
     * true otherwise.
     */
    onmousedown : function(event) {
      // check whether the mouse click falls in the canvas element
      var mouseinfo = this.mouseeventinfo(event);


      if (mouseinfo) {
        // we have focus
        this.focus();
        // give focus to the window
        // XXX check if it is the right window
        window.focus();
        // forward the mouse click to the cursor object
        return this.cursor.onmousedown(event, this, mouseinfo.x, mouseinfo.y);
      }
      else {
        // we do not have focus
        this.blur();
        this.redraw();
      }

    },

    // TODO: only necessary for crappy blinker implementation
    blinker : 0,

    focus : function() {

      this.hasFocus = true;
      this.cursor.visible = true;

      // cursor blinking
      // TODO: move to cursor class
      var editor = this;
      var blinker = ++this.blinker;
      var blinkon;
      var blinkoff;
      blinkon = function() {
        if (editor.hasFocus && (blinker == editor.blinker)) {
          editor.cursor.visible = true;
          editor.redraw();
          window.setTimeout(blinkoff, 600);
        }
      };
      blinkoff = function() {
        if (editor.hasFocus && (blinker == editor.blinker)) {
          editor.cursor.visible = false;
          editor.redraw();
          window.setTimeout(blinkon, 400);
        }
      };
      blinkon();

    },

    blur : function() {
      if (this.hasFocus) {
        // on losing focus save changes to ORBEON if ORBEON is around
        if (ORBEON) {
          ORBEON.xforms.Document.setValue(this.textarea.id, 
            this.textarea.value);
        }
	org.mathdox.formulaeditor.FormulaEditor.lastFocused = this;
      }
      this.hasFocus = false;
      this.cursor.visible = false;
    },

    getMathML : function() {
      var mmlstring;
      try {
        mmlstring = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\">"+
          this.presentation.getSemantics(this.getExpressionParsingContext()).value.getMathML()+
          "</math>";
      }
      catch(exception) {
        mmlstring = "<math xmlns=\"http://www.w3.org/1998/Math/MathML\">"+
          "<mtext>"+exception.toString()+"</mtext></math>";
      }

      return mmlstring;
    },

    /**
     * getOpenMath()
     *
     * function to get the OpenMath value of the formula in the formulaeditor
     * returns the contents of the formulaeditor as an OpenMath string.
     * 
     * extended with optional argument returnErrorInfo (boolean). If true
     * an array is returned instead with entries: 
     * - value (the OpenMath string);
     * - success (a boolean, true if no error has occurred);
     * - errorString (the exception converted to a string, which might be shown
     *   to the user).
     *
     * Usually an error occurs when there is an error in the entered formula.
     */
    getOpenMath : function(returnErrorInfo) {
      var omstring;
      var errorInfo;
      var success;

      if (returnErrorInfo === null || returnErrorInfo === undefined) {
        returnErrorInfo = false;
      }

      try {
        var semantics = this.presentation.getSemantics(this.getExpressionParsingContext());

        if (semantics === null || semantics.value === null) {
          omstring = "<OME>" +
	      "<OMS cd='moreerrors' name='encodingError'/>" +
                "<OMSTR>invalid expression entered. Presentation was: " +
                  this.presentation +
	        "</OMSTR>" +
	    "</OME>";

          success = false;
          errorString = null;
        } else {
          omstring = semantics.value.getOpenMath();
          success = true;
          errorString = null;
        }
      }
      catch(exception) {
        // IE doesn't provide a useful .toString for errors, use name and
        // message instead
        // old code: errorString = exception.toString();
        errorString = exception.name + " : "+exception.message;
        omstring =
            "<OME>" +
              "<OMS cd='moreerrors' name='encodingError'/>" +
              "<OMSTR>invalid expression entered" +
                errorString + 
	        ". "+
	        " Presentation was: "+
                this.presentation +
	      "</OMSTR>" +
            "</OME>";
        success = false;
      }

      omstring = "<OMOBJ xmlns='http://www.openmath.org/OpenMath' version='2.0' " + 
        "cdbase='http://www.openmath.org/cd'>" + omstring + "</OMOBJ>";
      
      if (returnErrorInfo) {
        /* return information about whether an error did occur */
        return { 
          errorString : errorString,
          success: success,
          value: omstring
        };
      } else {
        return omstring;
      }
    },
    indentXML : function(str) {
      var buffer=[];     // buffer to prevent slow string concatenation
      var oldpos;        // old position in string (written up till here)
      var pos;                 // current position in string
      var l;                 // length
      var indent=0;         // current indenting
      var indentstr="  ";// indenting done for each step
      var child;         // true if a child tag exists inside this one
                               // set to true if a tag is closed
                         // set to false if a tag is opened

      // help function that does the indenting
      var doIndent = function() {
        var i;

        if (buffer.length>0) {
          buffer.push("\n");
        }
        for (i=indent;i>0;i--) {
          buffer.push(indentstr);
        }
      };

      l=str.length; // store the length in l;

      oldpos=0; // written up to 0

      while ((pos = str.indexOf('<',oldpos))>=0) {
        if (pos>oldpos) {
          /* 
            indenting is not desired for text inside tags, unless after a child
          */
          if (child === true) {
            doIndent();
          }
          buffer.push(str.substr(oldpos,pos-oldpos));
          oldpos=pos;
        }

        pos++;
        c = str.charAt(pos);
        switch(c) {
          case '/': // closing tag
            indent -= 1;
            if (indent<0) {
              // shouldn't happen
              indent=0;
            }
            /*
             * don't indent if the tag only contains text (so no other tags, no
             * comments and no CDATA)
             */
            if (child === true) {
              doIndent();
            }
            pos = str.indexOf('>',pos);
            if (pos<0) {
              //alert("couldn't find closing >");
              return buffer.join("")+str.substr(oldpos);
            }
            pos+=1;
            child = true;
            break;
          case '!': // comment or CDATA
            pos++;
            c = str.charAt(pos);
            switch(c) {
              case '[' : // CDATA 
                child = true;
                pos = str.indexOf(']]>',pos);
                if (pos<0) {
                  //alert("couldn't find closing ]]>");
                  return buffer.join("")+str.substr(oldpos);
                }
                pos+=3;

                doIndent();

                break;
              case '-' : // XML Comment
                child = true;
                pos = str.indexOf('-->',pos);
                if (pos<0) {
                  //alert("couldn't find closing -->");
                  return buffer.join("")+str.substr(oldpos);
                }
                pos+=3;

                doIndent();

                break;
              default: // failure to parse the string
                //alert("couldn't parse");
                return buffer.join("")+str.substr(oldpos);
            }
            break;

          default: // opening tag or directly closed tag
            pos = str.indexOf('>',pos);
            if (pos<0) {
              //alert("couldn't find >, was expecting one though");
              return buffer.join("")+str.substr(oldpos);
            }

            doIndent();
            
            // in case of an opening tag, increase indenting
            if (str.charAt(pos-1) !='/') {
              child = false;
              indent += 1;
            } else {
              child = true;
            }
            pos+=1;
            break;
        }
        buffer.push(str.substr(oldpos,pos-oldpos));
        oldpos = pos;
        
      }
      if (oldpos<str.length) {
        buffer.push(str.substr(oldpos));
      }
      
      return buffer.join("");
    },
    /**
     * get the context for the expression parser
     * in the future this might be dependant on the editor
     * for now it is just an additional layer
     */
    getExpressionParsingContext: function() {
      return org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.getContext();
    },
    /**
     * get the context for the expression parser
     * in the future this might be dependant on the editor
     * for now it is just an additional layer
     */
    getPresentationContext: function() {
      Options = new org.mathdox.formulaeditor.Options();

      return Options.getPresentationContext();
    }
  });

  /**
   * Write debug info if debug option is on
   */
  org.mathdox.formulaeditor.FormulaEditor.addDebug= function(str) {
    if (debug !== undefined && debug!== null) {
      debug.addDebug(str);
    }
    return debug;
  },

  /**
   * Perform several cleanup operations
   * 
   * - Check all Formula Editor objects in editors
   *   + check if the textarea and canvas still exist (and are visible)
   *   + if the canvas doesn't exist anymore; remove the Formula Editor (and
   *     textarea)
   *   + if the textarea doesn't exist anymore, it should be reconstructed (?)
   * - Check all *mathdoxformula* canvases and remove those without Editor
   * - Check all *mathdoxformula* textareas and remove those without Editor
   * - Make sure for all editors that the textarea is placed directly after the
   *   canvas.
   */
  org.mathdox.formulaeditor.FormulaEditor.cleanup = function() {
    this.cleanupEditors();
    this.cleanupCanvases();
    this.cleanupTextareas();
    this.cleanupGroup();
  };

  /**
   * Perform some cleanup operations
   * 
   * - Check all *mathdoxformula* canvases and remove those without Editor
   */
  org.mathdox.formulaeditor.FormulaEditor.cleanupCanvases = function() {
    var canvases = document.getElementsByTagName("canvas");

    for (i=0; i<canvases.length; i++) {
      var canvas = canvases[i];

      // retrieve the class attribute of the textarea
      classattribute = canvas.getAttribute("class");

      // workaround bug in IE
      // see also http://www.doxdesk.com/personal/posts/wd/20020617-dom.html
      if (!classattribute) {
        classattribute = canvas.getAttribute("className");
      }

      // check whether this canvas is of class 'mathdoxformula'
      if (classattribute && classattribute.match(/(^| )mathdoxformula($| )/)) {
        if (!this.getEditorByCanvas(canvas)) {
          /* delete canvas */
          canvas.parentNode.removeChild(canvas);
        }
      }
    }
  };

  /**
   * Perform some cleanup operations
   * 
   * - Check all Formula Editor objects in editors
   *   + check if the textarea and canvas still exist (and are visible)
   *   + if the canvas doesn't exist anymore; remove the Formula Editor (and
   *     textarea)
   *   + if the textarea doesn't exist anymore, remove the Formula Editor (and
   *     canvas) 
   *     in the future : maybe reconstructed it ?
   */
  org.mathdox.formulaeditor.FormulaEditor.cleanupEditors = function() {
    /* check all editors and remove those that cannot be repaired */
    for (var i=editors.length; i>0; i--) {
      var nodeCanvas = editors[i-1].canvas.canvas;
      var nodeTextarea = editors[i-1].textarea;

      // check editor for canvas and textarea
      if (!nodeCanvas || !nodeTextarea) {
        if (nodeCanvas &&nodeCanvas.parentNode) {
          nodeCanvas.parentNode.removeChild(nodeCanvas);
        }

        if (nodeTextarea && nodeTextarea.parentNode) {
          nodeTextarea.parentNode.removeChild(nodeTextarea);
        }

        delete editors[i-1];
        editors.splice(i-1, 1);
        // editor removed
      }
    }
  };

  /**
   * Perform some cleanup operations
   * 
   * - Make sure for all editors that the textarea is placed directly after the
   *   canvas.
   * 
   * returns true if all editors have a textarea and canvas and false otherwise
   */
  org.mathdox.formulaeditor.FormulaEditor.cleanupGroup = function() {
    // check again all editors and place textareas directly after the canvas
    var i;
    var structureCorrect = true;

    for (i=0;i<editors.length;i++) {
      var nodeCanvas = editors[i].canvas.canvas;
      var nodeTextarea = editors[i].textarea;

      // check if editor has a canvas and textarea
      if (nodeCanvas && nodeCanvas.parentNode && nodeTextarea && nodeTextarea.parentNode) {
        if (nodeCanvas.nextSibling && (nodeCanvas.nextSibling==nodeTextarea)) {
          // text area was positioned correctly
        } else {
          // text area is not positioned correctly: fix it
          // create a clone of the textarea 
          var tmpTextarea = nodeTextarea.cloneNode(true);

          // put the clone directly after the canvas
          if (nodeCanvas.nextSibling) {
            nodeCanvas.parentNode.insertBefore(tmpTextarea, nodeCanvas.nextSibling);
          } else {
            nodeCanvas.parentNode.appendChild(tmpTextarea);
          }

          // update the textarea in the editor 
          editors[i].textarea = tmpTextarea;

          // remove original textarea
          textarea.parentNode.removeChild(textarea);
        }
      } else {
        // no canvas or no textarea found
        structureCorrect = false;
      }
    }

    return structureCorrect;
  };

  /**
   * Perform some cleanup operations
   * 
   * - Check all *mathdoxformula* textareas and remove those without Editor
   */
  org.mathdox.formulaeditor.FormulaEditor.cleanupTextareas = function() {
    var textareas = document.getElementsByTagName("textarea");

    for (i=0; i<textareas.length; i++) {
      var textarea = textareas[i];

      // retrieve the class attribute of the textarea
      classattribute = textarea.getAttribute("class");

      // workaround bug in IE
      // see also http://www.doxdesk.com/personal/posts/wd/20020617-dom.html
      if (!classattribute) {
        classattribute = textarea.getAttribute("className");
      }

      // check whether this textarea is of class 'mathdoxformula'
      if (classattribute && classattribute.match(/(^| )mathdoxformula($| )/)) {
        var textareaObject = this.getEditorByTextArea(textarea);
        if (!textareaObject) {
          /* delete textarea */
          textarea.parentNode.removeChild(textarea);
        } else {
          // dirty... but works (forces page to render in some cases)
          // workaround for some bug
          // TODO : is this still necessary
          textarea.innerHTML = textareaObject.textarea.value;
        }
      }
    }  
  };

  /**
   * Add the static deleteEditor(editor) method, that deletes a formula editor
   * object. This can be useful when cleaning, for example after changes in the
   * HTML, like when a textarea has been removed.
   * 
   * editor - an index in the editors array or a FormulaEditor object
   *
   * It returns true if the editor was found and of the right type and false
   * otherwise.
   */
  org.mathdox.formulaeditor.FormulaEditor.deleteEditor = function(editor) {
    var i;

    if (typeof editor == "number") {
      /* editor is an index; check if it is valid */
      i=editor;
      if (i<0 || i>=editors.length) {
        /* wrong index */
        return false;
      }
    } else if (editor instanceof org.mathdox.formuleditor.FormulaEditor) {
      /* editor is a FormulaEditor object; look it up editor in editors array */
      i=0;
      while (i<editors.length && editors[i]!=editor) {
        i++;
      }

      /* if the editor is not found, return false */
      if (i==editors.length) {
        return false;
      }
    } else {
      /* not a number, and not a Formula Editor */
      return false;
    }

    var nodeCanvas = editors[i].canvas.canvas;
    var nodeTextarea = editors[i].textarea;
  
    // remove canvas (if exists)
    if (nodeCanvas &&nodeCanvas.parentNode) {
      nodeCanvas.parentNode.removeChild(nodeCanvas);
    }
    // remove textarea (if exists)
    if (nodeTextarea && nodeTextarea.parentNode) {
      nodeTextarea.parentNode.removeChild(nodeTextarea);
    }

    // remove editor object
    delete editors[i];
    editors.splice(i,1);

    return true;
  };

  /**
   * Add the static getEditorByCanvas(canvas) method, that returns the
   * formula editor corresponding to a certain canvas. 
   * 
   * canvas - a string or an HTMLElement
   *
   * NOTE: might not work in IE if canvas is not an HTMLElement (check)
   *
   * It returns null when none of the editors in the page corresponds to the 
   * canvas given as argument.
   */
  org.mathdox.formulaeditor.FormulaEditor.getEditorByCanvas = function(canvas) {
    var i;

    if (canvas === undefined || canvas === null) {
      /* no argument given, return null */
      return null;
    }
    /* if canvas is a string, it is an id */
    /* NOTE: testing with instanceof String does *not* work */
    if (typeof canvas == "string") {
      for (i=0; i<editors.length; i++) {
        if (canvas == editors[i].canvas.id) {
          return editors[i];
        }
      }
    /**
     * if textarea is an object in the HTML DOM tree, it is the textarea itself
     */
    } else if (canvas instanceof HTMLElement) {
      for (i=0; i<editors.length; i++) {
        if (editors[i].canvas.canvas == canvas) {
          return editors[i];
        }
      }
    }
    /* no editor found */
    return null;
  };

  /**
   * Add the static getEditorByTextArea(textarea) method, that returns the
   * formula editor corresponding to a certain textarea. 
   * 
   * textarea - a string or an HTMLTextAreaElement
   *
   * It returns null when none of the editors in the page corresponds to the 
   * textarea given as argument.
   */
  org.mathdox.formulaeditor.FormulaEditor.getEditorByTextArea = function(textarea) {
    var i;

    if (textarea === undefined || textarea === null) {
      /* no argument given, return null */
      return null;
    }
    /* if textarea is a string, it is an id */
    /* NOTE: testing with instanceof String does *not* work */
    if (typeof textarea == "string") {
      for (i=0; i<editors.length; i++) {
        if (textarea == editors[i].textarea.id) {
          return editors[i];
        }
      }
    /**
     * if textarea is an object in the HTML DOM tree, it is the textarea itself
     */
    } else if (textarea instanceof HTMLElement) {
      for (i=0; i<editors.length; i++) {
        if (editors[i].textarea == textarea) {
          return editors[i];
        }
      }
    }
    /* no editor found */
    return null;
  };

  /**
   * Add the static getFocusedEditor() method, that returns the formula editor
   * that currently has focus. Returns null when none of the editors in the page
   * have focus.
   */
  org.mathdox.formulaeditor.FormulaEditor.getFocusedEditor = function() {

    for (var i=0; i<editors.length; i++) {
      if (editors[i].hasFocus) {
        return editors[i];
      }
    }
    return null;

  };
  
  org.mathdox.formulaeditor.FormulaEditor.lastFocused = null;

  /**
   * Add the static getLastFocusedEditor() method, that returns the formula
   * editor that last had focus. Returns null when none of the editors in
   * the page have had focus.
   */
  org.mathdox.formulaeditor.FormulaEditor.getLastFocusedEditor = function() {
    var current = org.mathdox.formulaeditor.FormulaEditor.getFocusedEditor();

    if (current !== null) {
      return current;
    }
    
    return org.mathdox.formulaeditor.FormulaEditor.lastFocused;
  };
 
  /**
   * Update the editor list, based on the current tree, focusing on which
   * textareas are present.
   *
   * In effect, create a new editor for each *relevant* textarea that has no 
   * corresponding editor. Here relevant means having the class
   * mathdoxformulaeditor.
   * 
   * clean: boolean, if true also do the following:
   *   - remove all editors for which no textarea is present
   *   - remove all *relevant* canvases for which no editor is present
   *     (here relevant means having the class ???)
   */
  org.mathdox.formulaeditor.FormulaEditor.updateByTextAreas = function(clean) {
    /* let textareas be all the textareas in the document */

    var textareas = document.getElementsByTagName("textarea");

    var classattribute;

    /* do cleaning : TODO */
    var i,j;
    if (clean) {
      /* remove all editors for which no textarea is present */

      i=0;
      while (i<editors.length) {
        /* get the index of j, or textareas.length if not found */
        j=0;
        while (j<textareas.length && editors[i].textarea != textareas[j]) {
          j++;
        }
        
        if (j==textareas.length) {
          /* textarea not found, delete the editor */
          this.deleteEditor(i);
        } else {
          i=i+1;
        }
      }

      /* 
        remove all *relevant* canvases for which no editor is present 
        Here relevant means of the class: mathdoxformula
      */
      this.cleanupCanvases();
    }

    /* 
     * add new editors where needed: 
     * for each textarea:
     * - check if the class contains mathdoxformula
     * - check if no editor corresponds to it
     * - create a new editor and add it to the list of editors
     */

    for (i=0; i<textareas.length; i++) {
      var textarea = textareas[i];

      // retrieve the class attribute of the textarea
      classattribute = textarea.getAttribute("class");

      // workaround bug in IE
      // see also http://www.doxdesk.com/personal/posts/wd/20020617-dom.html
      if (!classattribute) {
        classattribute = textarea.getAttribute("className");
      }

      // check whether this textarea is of class 'mathdoxformula'
      if (classattribute && classattribute.match(/(^| )mathdoxformula($| )/)) {
        // create and initialize new editor
        // new should not be used as a statement: use a dummy variable
        var editor = new org.mathdox.formulaeditor.FormulaEditor(textarea);
      }
    }
  };


  /**
   * Class that represents a formula editor palette.
   */
  org.mathdox.formulaeditor.Palette = $extend(
    org.mathdox.formulaeditor.FormulaEditor, {

    // do nothing with a keydown
    onkeydown : function(event) {
      return true; 
    },
    // check for keypress (currently: zoom)
    onkeypress : function(event) {
      var result = true;

      if (event.ctrlKey) {
        switch(event.charCode) {
          case 43: // '+' larger
            this.increaseSizes();
            result = false;
            break;
          case 45: // '-' smaller
            this.decreaseSizes();
            result = false;
            break;
        }
      }

      return result; 
    },
    // handle a mouseclick
    onmousedown: function(event) {
      // check whether the mouse click falls in the canvas element
      var mouseinfo = this.mouseeventinfo(event);
      var noEditorNeeded;

      if (mouseinfo) {
        // we are clicked on
        var editor = org.mathdox.formulaeditor.FormulaEditor.getFocusedEditor();

        noEditorNeeded = 
          this.handleMouseClick(editor, mouseinfo.x, mouseinfo.y);

        if ((noEditorNeeded === false) && (editor === null)) {
          alert("No formulaeditor with focus. Please click on an editor\n"+
                "at the position where the symbol should be entered.");
        }

        return false;
      }
      else {
        // we are not clicked on 
        return true;
      }
    },
    // fake function, do not draw the cursor
    cursor : {
      draw: function() {
        return true;
      }
    },
    /**
     * containing HTMLElement if not the canvas
     * item that should be removed if this palette should be removed
     */
    htmlelement : null,
    // todo onmousedown : function(event) { }
    initialize : function(canvas) {
      if (!canvas) {
        /* first initalization, do nothing yet */
        return null;
      }
     
      var MathCanvas = org.mathdox.formulaeditor.MathCanvas;

      if (arguments.length > 0 ) { 
        this.canvas = new MathCanvas(canvas);
      }

      var highlight = true;
      if (org.mathdox.formulaeditor.options.paletteHighlight == false) {
        highlight = false;
      }

      if (highlight) {
        var palette = this;
        var redrawFunction = function() {
          palette.redraw();
        };
        var HandlerLocal = $extend(org.mathdox.formulaeditor.EventHandlerLocal, {
          // handler for onmousemove
          onmousemove: function(event) {
            var mouseinfo = palette.mouseeventinfo(event);
  
	    if (mouseinfo) {
              var pTabContainer = palette.presentation.children[0];
  
              /* mouse over update function */
              pTabContainer.handleMouseMove(mouseinfo.x,mouseinfo.y,redrawFunction);
 
	    }
            return true;
          },
          onmouseout: function(event) {
            var pTabContainer = palette.presentation.children[0];
  
            /* mouse out update function */
            pTabContainer.handleMouseMove(null,null,redrawFunction);

            return true;
          }
        });

        var handlerLocal = new HandlerLocal(canvas);
      }
 
      // default presentation: empty
      this.presentation = new org.mathdox.formulaeditor.presentation.Row();

      var url;
      if (org.mathdox.formulaeditor.options.paletteURL) {
        url = org.mathdox.formulaeditor.options.paletteURL;
      } else {
        org.mathdox.formulaeditor.Palette.description = 
          org.mathdox.formulaeditor.Palettes.defaultPalette;
        
        this.parseXMLPalette(org.mathdox.formulaeditor.Palette.description);
        this.draw();

        return;
      }

      if (!org.mathdox.formulaeditor.Palette.description) {
        org.mathdox.formulaeditor.Palette.description = "loading";
        var HTTP = com.oreilly.javascript.tdg.XMLHttp;

        var callback = function(responseText) {

          org.mathdox.formulaeditor.Palette.description = responseText;
            
          /* update palettes */
          for (var p=0; p<palettes.length; p++) {
            palettes[p].parseXMLPalette(org.mathdox.formulaeditor.Palette.description);
            palettes[p].draw();
          }
        };

        HTTP.getText(url, callback);
      } else if (org.mathdox.formulaeditor.Palette.description != "loading") {
        // set presentation and semantics
        this.parseXMLPalette(org.mathdox.formulaeditor.Palette.description);
        this.draw();
      }
    },
    handleMouseClick: function(editor,x,y) {
      var pTabContainer = this.presentation.children[0];
      /* wrapper function to be able to redraw after a tab switch */
      var palette = this;
      var redrawFunction = function() {
              palette.redraw();
      };
      var coords = pTabContainer.handleMouseClick(x,y,redrawFunction);

      if (editor === null) {
        if (coords === null) {
        /* nothing to enter, so no editor needed */
          return true; 
        } else {
          return false;
        }
      }

      var position = editor.cursor.position;

      if (coords === null) {
        return false;
      }
      var row = this.semantics.operands[coords.tab].operands[coords.row].operands[coords.col];

      var presentation = org.mathdox.formulaeditor.presentation;
      var rowPresentation = new presentation.Row(row.getPresentation(this.getPresentationContext()));
      rowPresentation.flatten();

      var moveright;
      if (rowPresentation.children) {
        for (var i=0;i<rowPresentation.children.length;i++) {
          //alert("inserting: "+i+" : "+toInsert.children[i]);

          moveright = position.row.insert(position.index, 
            rowPresentation.children[i], (i === 0));
          if (moveright) {
            position.index++;
          }
        }
      } else {
        moveright = position.row.insert(
          position.index, rowPresentation, true);

        if (moveright) {
          position.index++;
        }
      }
      editor.redraw();
      editor.save();
      return false;
    },
    parseXMLPalette : function(XMLstr) {
      var presentation;
      var Parser    = org.mathdox.formulaeditor.parsing.openmath.OpenMathParser;
      var Row       = org.mathdox.formulaeditor.presentation.Row;

      // read any OpenMath code that may be present in the textarea
      //try {
        this.semantics = new Parser().parse(XMLstr);
        presentation = new Row(this.semantics.getPresentation(this.getPresentationContext()));
        presentation.flatten();
        this.presentation = presentation;
        this.presentation.margin = 10.0;
      //}
      //catch(exception) {
      //  presentation = new Row();
      //}

      return presentation;
    }
  });

  /**
   * Remove this palette from the document
   * does three things:
   * - removes this palette from palettes
   * - removes it from the corresponding editor
   * - removes the canvas from the document
   */
  org.mathdox.formulaeditor.Palette.removePalette = function (palette) {
    /* palette should be defined */
    if (palette === null || palette === undefined) {
      return ;
    }

    var i;
    for (i=0; i<palettes.length; i++) {
      if (palettes[i] == palette) {
        // remove palette from the palettes array
        palettes.splice(i,1);
      }
    }
    // remove palette's canvas from the corresponding editor
    for (i=0; i<editors.length; i++) {
      if (editors[i].palette == palette) {
        editors[i].palette = null;
      }
    }

    // remove this palette's canvas from the page
    var palhtml;

    if (palette.htmlelement!== null && palette.htmlelement !== undefined) {
      palhtml = palette.htmlelement;
    } else {
      palhtml = palette.canvas.canvas;
    }

    palhtml.parentNode.removeChild(palhtml);
  };

  /**
   * When the document has finished loading, replace all textarea elements of
   * class 'mathdoxformula' with a formula editor.
   */

  // function that will be called upon loading
  var onload = function() {
    var options = new org.mathdox.formulaeditor.Options();
    if (options.getOption("debug") === true) {
      debug = new org.mathdox.debug.Debug();
      debug.createDebug();
    }


    // replace all textarea's of class 'mathdoxformula' with editors
    var textareas = document.getElementsByTagName("textarea");
    for (var i=0; i<textareas.length; i++) {
      var textarea = textareas[i];

      // retrieve the class attribute of the textarea
      var classattribute = textarea.getAttribute("class");

      // workaround bug in IE
      // see also http://www.doxdesk.com/personal/posts/wd/20020617-dom.html
      if (!classattribute) {
        classattribute = textarea.getAttribute("className");
      }

      // check whether this textarea is of class 'mathdoxformula'
      if (classattribute && classattribute.match(/(^| )mathdoxformula($| )/)) {

        // replace the textarea by a formula editor
        var editor = new org.mathdox.formulaeditor.FormulaEditor(textarea);

      }

    }

    // register key and mouse handlers that forward events to the editors
    var Handler = $extend(org.mathdox.formulaeditor.EventHandler, {

      onkeydown : function(event) {
        var result = true;
        for (var i=0; i<editors.length; i++) {
          var intermediate = editors[i].onkeydown(event);
          if (intermediate !== null && intermediate !== undefined &&
              intermediate === false) {
            result = false;
          }
        }
        return result;
      },

      onkeypress : function(event) {
        var result = true;

        for (var i=0; i<editors.length; i++) {
          var intermediate = editors[i].onkeypress(event);
          if (intermediate !== null && intermediate !== undefined && 
              intermediate === false) {
            result = false;
          }
        }
        return result;
      },

      onmousedown : function(event) {
        var result = true;
        var i; // counter
        if (palettes) {
          for (i=0;i<palettes.length;i++) {
            if (result) {
              result = result && palettes[i].onmousedown(event);
            }
          }
        }
        if (result) {
          // if not handled by palettes, then continue
          for (i=0; i<editors.length; i++) {
            var intermediate = editors[i].onmousedown(event);
            if (intermediate !== null && intermediate !== undefined &&
                intermediate === false) {
              result = false;
            }
          }
          return result;
        }
      }
    });
    var handler = new Handler();
    var Options = new org.mathdox.formulaeditor.Options();

    /* check for onload focus */
    var focus = Options.getOption("onloadFocus");
    if (typeof focus == "string") {
      var onloadTextArea = document.getElementById(focus);
      if (onloadTextArea) {
        var onloadEditor = org.mathdox.formulaeditor.FormulaEditor.getEditorByTextArea( onloadTextArea );
        if (onloadEditor) {
          onloadEditor.focus();
        }
      } 
    } else if (focus == true) {
      editors[0].focus();
    } 
  };

  // register the onload function as an event handler
  if (window.addEventListener) {

    // use the W3C standard way of registering event handlers
    // NOTE: Google Chrome indicates it's already finished loading, so no
    // eventlistener needs to be added.
    if (org.mathdox.formulaeditor.hasLoaded || 
      (document.readyState && document.readyState == "complete")) {
      onload();
    } else {
      window.addEventListener("load", onload, false);
    }
  } else {

    // document.body might not exist yet, if it doesn't call the check function
    // with a 50 ms delay (fixes a bug)
    
    var bodyChecker;

    bodyChecker = function() {
      if (!document.body) {
        setTimeout(bodyChecker,50);
      } else {
        if (document.body.attachEvent) {
          // use the MSIE-only way of registering event handlers
          if (document.readyState == "complete") {
            onload();
          } else {
            document.body.attachEvent("onload", onload);
          }
        } 
      }
    };
    bodyChecker();
  }

});

org.mathdox.formulaeditor.hasLoaded = false;

if (window.addEventListener) {
  var setLoaded = function() {
    org.mathdox.formulaeditor.hasLoaded = true;
  };

  // use the W3C standard way of registering event handlers
  window.addEventListener("load", setLoaded, false);
}
