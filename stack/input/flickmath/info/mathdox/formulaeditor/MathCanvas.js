$package("org.mathdox.formulaeditor");

$identify("org/mathdox/formulaeditor/MathCanvas.js");

$require("org/mathdox/formulaeditor/Options.js");

$main(function(){

  /**
   * Adds mathematical symbol and string rendering to an HTML Canvas.
   */
  org.mathdox.formulaeditor.MathCanvas = $extend(Object, {

    /**
     * The HTML Canvas that is used for drawing mathematics.
     */
    canvas : null,

    /**
     * The name of the font that is used for drawing symbols and strings.
     */
    fontName : "cmr",

    /**
     * The font size that is used for drawing symbols and strings.
     */
    fontSizes : [ 50, 60, 70, 85, 100, 120, 144, 173, 207, 249, 298, 358, 430],
    fontSizeIdx : 7,

    /**
     * Contains previously loaded images.
     */
    imageCache : null,

    /**
     * Constructor which takes as parameter the HTML Canvas that will be used to
     * draw mathematics on. */ 
    initialize : function(canvas) {
      this.canvas = canvas;
      this.imageCache = {};
      if (org.mathdox.formulaeditor.options.fontSize) {
        var i = 0;
        while (i<this.fontSizes.length - 1 && 
          this.fontSizes[i]<org.mathdox.formulaeditor.options.fontSize) {
          i++;
        }
        this.fontSizeIdx = i;
      }
    },

    /**
     * Returns the CanvasRenderingContext2D object associated with the HTML
     * canvas.
     */
    getContext : function() {
      return this.canvas.getContext("2d");
    },

    /**
     * Draws the specified bracket (character) on the canvas, at the specified
     * position, using the current fontName and fontSize. The (x,y) coordinate
     * indicates where the left of the baseline of the symbol will appear.
     * The result of this method is one object containing the values
     * { left, top, width, height } that indicate the position and dimensions of
     * the bounding rectangle of the drawn symbol.
     * The optional parameter 'invisible' determines whether or not the symbol
     * should be drawn to the canvas. By default this parameter is 'false'.
     * Setting this parameter to 'true' can be used to obtain information about
     * the dimensions of the drawn symbol, without actually drawing on the
     * canvas.
     *
     * This function is like the drawSymbol function, except it has an extra
     * parameter minimumSize, which is used to determine the size of the
     * brackets. A bracket symbol will be used of at least that size. If no
     * symbol of that size is known it will be tried to construct it, if that
     * fails a smaller symbol will be used. See also drawSymbol.
     */
    drawBracket : function(bracket, x, y, minimumHeight, invisible, fontSizeModifier, center) {

      // retrieve font and symbol data
      var symbolData;
      var ex = this.getFontUnitEx(fontSizeModifier);

      // see if a standard symbol can be used
      for (var i=4 ; i>=1; i--) {
        var tmpData = this.getSymbolDataByPosition(bracket + i, fontSizeModifier);
        if (tmpData.height >= minimumHeight) {
          symbolData = tmpData;
        }
      }
 
      // variables used in both if and else clauses, declare them before the if
      var left, height, top, width, font, canvas, cache, drawImage, image;

      if (symbolData) {
        // draw a symbol 
        
        // calculate the position of the topleft corner, the width and the height
        if (symbolData.margin) {
          symbolData = this.extendObject(symbolData, {
            x: symbolData.x - symbolData.margin,
            width: symbolData.width + 2*symbolData.margin
          });
        }
        left   = x;
        top    = y - symbolData.height + symbolData.yadjust;
        width  = symbolData.width;
        height = symbolData.height;
        font = symbolData.font;
  
        // draw that part of the font image which contains the symbol
        if (!invisible) {
  
          canvas = this.canvas;
          cache  = this.imageCache;
  
          drawImage = function() {
            canvas.getContext("2d").drawImage(
              cache[font.image],
              symbolData.x, symbolData.y, symbolData.width, symbolData.height,
              left, top, width, height);
          };

          /* warning code used in both drawSymbol and drawBracket */
          if (cache[font.image] === null || cache[font.image] === undefined) {
            image = new Image();
            image.onload = function() {
              if (cache[font.image] instanceof Array) {
                var todo = cache[font.image];
  
                cache[font.image] = image;
  
                for (var i=0; i<todo.length; i++) {
                  todo[i](); // call stored drawImage functions
                }
              }
            };
            cache[font.image] = [];
  
            cache[font.image].push(drawImage);
  
            image.src = font.image;
          } else if (cache[font.image] instanceof Array) {
            cache[font.image].push(drawImage);
          } else {
            drawImage();
          }
 
        }
  
        // return the coordinates of the topleft corner, the width and height
        return {
          left:   left,
          top:    top,
          width:  width,
          height: height
        };
      } else {
        // construct a symbol
        var topSymbol = this.getSymbolDataByPosition(bracket+"u", fontSizeModifier);
        var bottomSymbol = this.getSymbolDataByPosition(bracket+"l", fontSizeModifier);
        var connection = this.getSymbolDataByPosition(bracket+"m", fontSizeModifier);

        if (!topSymbol.adjusted) {
          // get rid of aliased top/bottom
          // for top part
          topSymbol.adjusted = true;
          topSymbol.height -= 1;

          // for middle part
          if (connection.height > 2) {
            // get rid of aliased top/bottom
            connection.height -= 2;
            connection.y += 1;
          }

          // for bottom part
          bottomSymbol.height -= 1;
          bottomSymbol.y += 1;
        }

        left = x;
        height = Math.max(minimumHeight,
          topSymbol.height + bottomSymbol.height);
        top = y - height + bottomSymbol.yadjust;
        width = Math.max(topSymbol.width, connection.width,
          bottomSymbol.width);

        font = topSymbol.font;

        if (!invisible) {
  
          canvas = this.canvas;
          cache  = this.imageCache;
  
          drawImage = function() {
            var minXadjust = Math.min(topSymbol.xadjust,
              bottomSymbol.xadjust, connection.xadjust);
            var topPos = { 
              left: left + topSymbol.xadjust - minXadjust,
              top: top,
              width: topSymbol.width,
              height: topSymbol.height
            };
            var bottomPos = { 
              left: left + bottomSymbol.xadjust - minXadjust,
              top: top + height - bottomSymbol.height ,
              width: bottomSymbol.width,
              height: bottomSymbol.height
            };
            var connPos = { 
              left: left + connection.xadjust - minXadjust,
              top: topPos.top + topPos.height,
              width: connection.width,
              height: height - topPos.height - bottomPos.height
            };
            //alert("top: ("+topPos.left+", "+topPos.top+", "+topPos.width+", "+topPos.height+")\n"+"conn: ("+connPos.left+", "+connPos.top+", "+connPos.width+", "+connPos.height+")\n"+"bottom: ("+bottomPos.left+", "+bottomPos.top+", "+bottomPos.width+", "+bottomPos.height+")");
            //alert("font.image:"+font.image);
            canvas.getContext("2d").drawImage(
              cache[font.image],
              topSymbol.x, topSymbol.y, topSymbol.width, topSymbol.height,
              topPos.left, topPos.top, topPos.width, 
              topPos.height);
            if (connPos.height>0) {
              canvas.getContext("2d").drawImage(
                cache[font.image],
                connection.x, connection.y, connection.width, connection.height,
                connPos.left, connPos.top, 
                connPos.width, connPos.height);
            }
            canvas.getContext("2d").drawImage(
              cache[font.image],
              bottomSymbol.x, bottomSymbol.y, bottomSymbol.width, 
              bottomSymbol.height, bottomPos.left, 
              bottomPos.top, bottomPos.width, bottomPos.height);
          };
          
          /* warning code used in both drawSymbol and drawBracket */
          if (cache[font.image] === null || cache[font.image] === undefined) {
            image = new Image();
            image.onload = function() {
              if (cache[font.image] instanceof Array) {
                var todo = cache[font.image];
  
                cache[font.image] = image;
  
                for (var i=0; i<todo.length; i++) {
                  todo[i](); // call stored drawImage functions
                }
              }
            };
            cache[font.image] = [];
  
            cache[font.image].push(drawImage);
  
            image.src = font.image;
          } else if (cache[font.image] instanceof Array) {
            cache[font.image].push(drawImage);
          } else {
            drawImage();
          }
  
        }
        //alert("total: ("+left+", "+top+", "+width+", "+height+")");
         // return the coordinates of the topleft corner, the width and height
        return {
          left:   left,
          top:    top,
          width:  width,
          height: height
        };
      }
    },

    /**
     * Draws a grey/black box around on the edge of an element, depending on
     * the dimensions. It overwrites the character (that is stays inside its
     * dimensions). The box will be drawn around
     * (dimensions.left,dimensions.top) (upper left) and
     * (dimensions.left+dimensions.width - 1, dimensions.top+dimensions.height
     * - 1) (lower right). 
     *
     * if strokeStyle is defined, that style will be used to draw the
     * border of the box.
     *
     * if fillStyle is defined, the box is filled using that style
     * 
     * if both stokeStyle and fillStyle are defined, then a filled box is drawn
     * first, followed by a border.
     */
    drawBox: function(dimensions, strokeStyle, fillStyle) {
      var canvas = this.canvas;
      var context = this.getContext();

      context.save();
      
      // set styles
      if (fillStyle !== undefined) {
        context.fillStyle = fillStyle;
      }
      if (strokeStyle !== undefined) {
        context.strokeStyle = strokeStyle;
      }

      // draw a filled box
      if (fillStyle) {
        context.fillRect(dimensions.left, dimensions.top, dimensions.width, 
          dimensions.height);
      }

      // draw the box border
      if (!fillStyle || (fillStyle && strokeStyle)) {
        context.lineWidth = 1.0;
        context.strokeRect(dimensions.left, dimensions.top, 
          dimensions.width - 1 , dimensions.height - 1);
      } 

      context.restore();
    },
   
    /**
     * This function draws a box with based on dimensions, with border style
     * strokeStyle and fill style fillStyle.
     * 
     * if y is defined a baseline will be drawn as well (in the strokeStyle)
     * 
     * see also: drawBox
     */
    drawBoxWithBaseline: function(dimensions, y, strokeStyle, fillStyle) {
      this.drawBox(dimensions, fillStyle, strokeStyle);

      var canvas = this.canvas;
      var context = this.getContext();

      context.save();
      if (y) {
        if (strokeStyle !== undefined) {
          context.strokeStyle = strokeStyle;
        }
        context.beginPath();
        context.moveTo(dimensions.left, y);
        context.lineTo(dimensions.left + dimensions.width - 1, y);
        context.stroke();
        context.closePath();
      }
      context.restore();
    },

    // draw a box the size of the symbol of the letter 'f' 
    drawFBox : function(x, y, invisible, letter, typeface, fontSizeModifier) {
      var dim;
      if (letter === null || letter === undefined) {
        letter = "f";
      }
      var presentation = org.mathdox.formulaeditor.presentation;

      dim = this.drawSymbol(letter,x,y,true, typeface, fontSizeModifier);

      if (!invisible) {
        var context = this.getContext();
        context.save();
        context.fillStyle = "rgba(160,160,255,0.5)";
        context.fillRect(dim.left, dim.top, dim.width, dim.height);
        context.restore();
      }

      return dim;

    },


    /**
     * Draws the specified symbol (character) on the canvas, at the specified
     * position, using the current fontName and fontSize. The (x,y) coordinate
     * indicates where the left of the baseline of the symbol will appear.
     * The result of this method is one object containing the values
     * { left, top, width, height } that indicate the position and dimensions of
     * the bounding rectangle of the drawn symbol.
     * The optional parameter 'invisible' determines whether or not the symbol
     * should be drawn to the canvas. By default this parameter is 'false'.
     * Setting this parameter to 'true' can be used to obtain information about
     * the dimensions of the drawn symbol, without actually drawing on the
     * canvas.
     * If the typeface is "math" a slanted/italic symbol will be drawn if
     * possible.
     */
    drawSymbol : function(symbol, x, y, invisible, typeface, fontSizeModifier) {
      var mathCanvas = org.mathdox.formulaeditor.MathCanvas;
      if (mathCanvas.specialSymbols[symbol]!== undefined) {
        // special case combined symbol: 
        // draw all subsymbols and return maximum dimensions

        var dim = {
          top:    y,
          left:   x,
          width:  0,
          height: 0
        };
        var olddim;
        var i;
        var symbols = mathCanvas.specialSymbols[symbol];

        for (i=0; i< symbols.length; i++) {
          olddim = dim;
          dim = this.drawSymbol(symbols[i], x, y, invisible, typeface, fontSizeModifier);

          dim = {
            top: Math.min(olddim.top, dim.top),
            height: Math.max(olddim.top+olddim.height, dim.top+dim.height) - 
              Math.min(olddim.top, dim.top),
            left: Math.min(olddim.left, dim.left),
            width: Math.max(olddim.left+olddim.width, dim.left+dim.width) - 
              Math.min(olddim.left, dim.left)
          };
        }

        return dim;
      }

      // retrieve font and symbol data
      var symbolData = this.getSymbolData(symbol, typeface, fontSizeModifier);

      if (symbolData === null) {
	// draw an invisible box instead. Another option would be a red box
	var box =  this.drawFBox(x, y, true, null, typeface, fontSizeModifier);
	if (invisible !== true) {
	  this.drawBox(box, "rgba(255,0,0, 1.0)");
	}
	return box;
      }

      var font = symbolData.font;

      // calculate the position of the topleft corner, the width and the height
      var left   = x;
      var top    = y - symbolData.height + symbolData.yadjust;
      var width  = symbolData.width;
      var height = symbolData.height;

      // draw that part of the font image which contains the symbol
      if (!invisible) {

        var canvas = this.canvas;
        var cache  = this.imageCache;

        var drawImage = function() {
          canvas.getContext("2d").drawImage(
            cache[font.image],
            symbolData.x, symbolData.y, symbolData.width, symbolData.height,
            left, top, width, height);
        };

        /* warning code used in both drawSymbol and drawBracket */
        if (cache[font.image] === null || cache[font.image] === undefined) {
          var image = new Image();
          image.onload = function() {
            if (cache[font.image] instanceof Array) {
              var todo = cache[font.image];

              cache[font.image] = image;

              for (var i=0; i<todo.length; i++) {
                todo[i](); // call stored drawImage functions
              }
            }
          };
          cache[font.image] = [];

          cache[font.image].push(drawImage);

          image.src = font.image;
        } else if (cache[font.image] instanceof Array) {
          cache[font.image].push(drawImage);
        } else {
          drawImage();
        }
      }

      // return the coordinates of the topleft corner, the width and height
      return {
        left:   left,
        top:    top,
        width:  width,
        height: height
      };

    },

    /**
     * copy an old object, replacing only a few attributes
     */
    extendObject: function(oldObj, replace) {
      var newObj = {};
      var name; // index variable
      for (name in oldObj) {
        newObj[name] = oldObj[name];
      }
      for (name in replace) {
        newObj[name] = replace[name];
      }

      return newObj;
    },

    getSymbolData : function(symbol, typeface, fontSizeModifier) {
      // retrieve font and symbol data
      var symbolData;

      /* some special cases */
      if (symbol==' ') {
        symbolData = this.getSymbolDataByPosition(',', fontSizeModifier);
        if (symbolData) {
          symbolData = this.extendObject(symbolData, {
            x:symbolData.x+symbolData.width+1
          });
        }
      } else if (symbol == '_' ) { 
        symbolData = this.getSymbolDataByPosition('-', fontSizeModifier);
        if (symbolData) {
          symbolData = this.extendObject(symbolData, { 
            yadjust: 0 + symbolData.height 
          });
        }
      } else if (typeface == 'math') {
        symbolData = this.getSymbolDataByPosition("m"+ symbol, fontSizeModifier);
        if (! symbolData) {
          symbolData = this.getSymbolDataByPosition(symbol, fontSizeModifier);
        }
      } else {
        /* generic case */
        symbolData = this.getSymbolDataByPosition(symbol, fontSizeModifier);
      }

      /* some margins, '-', '+', middle dot */
      if (symbol == '-') {
        symbolData = this.extendObject(symbolData, { 
          margin: 2
        });
      } else if (symbol == '+') { 
        symbolData = this.extendObject(symbolData, { 
          margin: 2
        });
      } else if (symbol == '·') { // U+00B7 middle dot
        symbolData = this.extendObject(symbolData, { 
          margin: 2
        });
      }

      if (!symbolData) {
        if ((!symbol) || (symbol === '') || (symbol.charCodeAt(0) === 0)) {
          return null;
        }
      }

      if (symbolData) {
        if (symbolData.margin) {
          symbolData = this.extendObject(symbolData, {
            x: symbolData.x - symbolData.margin,
            width: symbolData.width + 2*symbolData.margin
          });
        }

        // return symboldata
        return symbolData;
      }
        
      if (!symbolData) {
        // should not happen any more
        console.log("ALERT: unsupported symbol '"+symbol+"' cannot be gotten by position");
      }

      // no symbol data found, return null
      return null;
    },

    getSymbolDataByPosition: function(symbol, fontSizeModifier) {
      var positionInfo = org.mathdox.formulaeditor.MathCanvas.symbolPositions[
        symbol];
      var fBP = org.mathdox.formulaeditor.MathCanvas.fontsByPosition;
      var fontSize = this.fontSizes[this.fontSizeIdx];
      var newFontSizeIndex;
      if (fontSizeModifier!== undefined && fontSizeModifier !== null) {
	newFontSizeIndex = this.fontSizeIdx + fontSizeModifier;
	if (0<=newFontSizeIndex && newFontSizeIndex < this.fontSizes.length) {
	  fontSize = this.fontSizes[newFontSizeIndex];
	} else if (newFontSizeIndex<0) {
	  fontSize = this.fontSizes[0];
	} else if (newFontSizeIndex>this.fontSizes.length) {
	  fontSize = this.fontSizes[this.fontSizes.length-1];
	}
      }

      if (!positionInfo) {
        //alert("no positioninfo : "+symbol);
        return null;
      }
      
      if (!fBP[positionInfo.font]) {
        alert("no metrics for this font");
        return null;
      }

      if (!fBP[positionInfo.font][fontSize]) {
        alert("no metrics for this fontsize: "+fontSize);
        return null;
      }
      
      if (positionInfo.row*16+positionInfo.col >=
        fBP[positionInfo.font][fontSize].length) {
        alert("positionInfo row: "+positionInfo.row+" col: "+positionInfo.col);
        alert("no metrics for this symbol: "+(positionInfo.row*16+positionInfo.col)+"/"+fBP[positionInfo.font][fontSize].length);
        return null;
      }

      return fBP[positionInfo.font][fontSize][ positionInfo.row * 16 +
        positionInfo.col ];
    },

    getFontUnitEm: function (fontSizeModifier) {
      var data = this.getSymbolDataByPosition("M", fontSizeModifier);

      return data.width;
    },

    getFontUnitEx: function (fontSizeModifier) {
      var data = this.getSymbolDataByPosition("x", fontSizeModifier);

      return data.height;
    },

    /**
     * Clears the canvas.
     */
    clear : function() {
      var canvas = this.canvas;
      var width  = canvas.getAttribute("width");
      var height = canvas.getAttribute("height");
      canvas.getContext("2d").clearRect(0, 0, width, height);
    },

    decreaseSize: function() {
      if ( this.fontSizeIdx>0) {
        this.fontSizeIdx = this.fontSizeIdx - 1;
      }
    },

    increaseSize: function() {
      if ( this.fontSizeIdx<this.fontSizes.length-1) {
        this.fontSizeIdx = this.fontSizeIdx + 1;
      }
    }

  });

  /**
   * Static function addFont to add fonts to the static MathCanvas in
   * fontsByPosition. Arguments are like jsMath.Img.AddFont:
   * - size: the size (in pt?) of the font
   * - data: a record with fontname: array
   *   where array contains 256 entries (relative width, relative height,
   *   yadjust, optional xadjust)
   *   followed by a list of horizontal positions, a list of vertical
   *   positions and the lower right coordinates of the picture
   */
  org.mathdox.formulaeditor.MathCanvas.addFont = function(size,data) {
    var fontSize = ""+size;

    if (!org.mathdox.formulaeditor.MathCanvas.fontsByPosition) {
      org.mathdox.formulaeditor.MathCanvas.fontsByPosition = {};
    }
    var fBP = org.mathdox.formulaeditor.MathCanvas.fontsByPosition;

    for (var fontName in data) {
      if (!fBP[fontName]) {
        fBP[fontName] = {};
      }
      var fBPN = fBP[fontName];
      var fontInput = data[fontName];
      var font = { image : $baseurl + "org/mathdox/formulaeditor/fonts/" +
          fontName + "/" + fontSize + ".png"};

      if (!fBPN[fontSize]) {
        fBPN[fontSize] = [];
      }
      var fontInfo = fBPN[fontSize];
      var length = fontInput.length;

      for (var row = 0; row < 8; row++) {
        for (var col = 0; col < 16; col ++) {
          var pos = row*16 + col;

          if (pos<length-3) {
            var xadjust = 0;
            var width = fontInput[pos][0];
            var height = fontInput[pos][1];
            var yadjust = fontInput[pos][2];
            
            if (fontInput[pos][3]) {
              xadjust = fontInput[pos][3];
            }
            var outputCharInfo = {
              x: fontInput[ length - 3 ][col] - xadjust,
              y: fontInput[ length - 2 ][row] - height + yadjust,
              width: width,
              height: height,
              xadjust: - xadjust,
              yadjust: yadjust, // XXX check the sign
              font:font
            };
          }
          
          fontInfo.push(outputCharInfo);
        }
      }
    }
  };

  // XXX: how to multiple alternatives for symbols (capital pi vs product,
  // emptyset vs o with stroke) ?
  // XXX: which symbols to choose for ',`,"
  org.mathdox.formulaeditor.MathCanvas.symbolsInFont = {
    bbold10: [
        // U+213E double-struck capital gamma
        // U+213F double-struck capital pi
        // U+2140 double-struck n-ary summation
      [  'ℾ', null, null, null, null,  'ℿ',  '⅀', null,
        // U+213D double-struck small gamma
        null, null, null, null, null,  'ℽ', null, null],
      [ null, null, null, null, null, null, null, null,
        // U+213C double-struck small pi
        null,  'ℼ', null, null, null, null, null, null],
      [ null, null, null, null, null, null, null, null,
        null, null, null, null, null, null, null, null],
      [ null, null, null, null, null, null, null, null,
        null, null, null, null, null, null, null, null],
        // U+2102 double-struck capital c
        // U+210D double-struck capital h
      [ null, null, null,  'ℂ', null, null, null,  'ℍ',
        // U+2115 double-struck capital n
        null, null, null, null, null, null,  'ℕ', null],
        // U+2119 double-struck capital p
        // U+211A double-struck capital q
        // U+211D double-struck capital r
      [  'ℙ',  'ℚ',  'ℝ', null, null, null, null, null,
        // U+2124 double-struck capital z
        null, null,  'ℤ', null, null, null, null, null],
      [ null, null, null, null, null, null, null, null,
        null, null, null, null, null, null, null, null],
      [ null, null, null, null, null, null, null, null,
        null, null, null, null, null, null, null, null]
    ],
    cmr10: [
      // U+0393 Greek capital letter gamma
      // U+0349 Greek capital letter delta
      // U+0398 Greek capital letter theta
      // U+039E Greek capital letter xi
      // U+039B Greek capital letter lamda
      // U+03A0 Greek capital letter pi
      // U+03A3 Greek capital letter sigma
      // U+03D2 Greek upsilon with hook symbol
      [  'Γ',  'Δ',  'Θ',  'Ξ',  'Λ',  'Π',  'Σ',  'ϒ',
      // U+03A6 Greek capital letter phi
      // U+03A8 Greek capital letter psi
      // U+03A9 Greek capital letter omega
         'Φ',  'Ψ',  'Ω', 'ff', 'fi', 'fl','ffi','ffl'],
      // U+00B4 Acute accent (spacing)
      // U+00B0 Degree sign
      [ null, null,  '`',  '´', null, null, null,  '°', 
      // U+00B8 Cedilla (spacing)
      // U+00DF Latin small letter sharp s
      // U+00E6 Latin small letter ae
      // U+0152 Latin small ligature oe
      // U+00F8 Latin small letter o with stroke
      // U+00C6 Latin capital ae
      // U+0152 Latin capital ligature oe
      // U+00D8 Latin capital letter o with stroke
         '¸',  'ß',  'æ',  'œ',  'ø',  'Æ',  'Œ',  'Ø'],
      [ null,  '!',  '"',  '#',  '$',  '%',  '&', '\'',
         '(',  ')',  '*',  '+',  ',', null,  '.',  '/'],
      [  '0',  '1',  '2',  '3',  '4',  '5',  '6',  '7',
      // U+00A1 inverted exclamation mark
      // U+00BF inverted question mark
         '8',  '9',  ':',  ';',  '¡',  '=',  '¿',  '?'],
      [  '@',  'A',  'B',  'C',  'D',  'E',  'F',  'G',
         'H',  'I',  'J',  'K',  'L',  'M',  'N',  'O'],
      [  'P',  'Q',  'R',  'S',  'T',  'U',  'V',  'W',
         'X',  'Y',  'Z',  '[', '``',  ']',  '^', '^.'],
      [ null,  'a',  'b',  'c',  'd',  'e',  'f',  'g',
         'h',  'i',  'j',  'k',  'l',  'm',  'n',  'o'],
      [  'p',  'q',  'r',  's',  't',  'u',  'v',  'w',
      // U+00A8 diaeresis (spacing)
         'x',  'y',  'z', null, null, null,  '~',  '¨']
    ],
    cmbx10: [
      // U+0393 Greek capital letter gamma
      // U+0349 Greek capital letter delta
      // U+0398 Greek capital letter theta
      // U+039B Greek capital letter lamda
      // U+039E Greek capital letter xi
      // U+03A0 Greek capital letter pi
      // U+03A3 Greek capital letter sigma
      // U+03D2 Greek upsilon with hook symbol
      [ 'bΓ', 'bΔ', 'bΘ', 'bΛ', 'bΞ', 'bΠ', 'bΣ', 'bϒ', 
      // U+03A6 Greek capital letter phi
      // U+03A8 Greek capital letter psi
      // U+03A9 Greek capital letter omega
        'bΦ', 'bΨ', 'bΩ','bff','bfi','bfl','bffi','bffl'],
      // U+00B4 Acute accent (spacing)
      // U+00B0 Degree sign
      [ null, null, 'b`', 'b´', null, null, null, 'b°',
      // U+00B8 Cedilla (spacing)
      // U+00DF Latin small letter sharp s
      // U+00E6 Latin small letter ae
      // U+0152 Latin small ligature oe
      // U+00F8 Latin small letter o with stroke
      // U+00C6 Latin capital ae
      // U+0152 Latin capital ligature oe
      // U+00D8 Latin capital letter o with stroke
        'b¸', 'bß', 'bæ', 'bœ', 'bø', 'bÆ', 'bŒ', 'bØ'],
      [ null, 'b!', 'b"', 'b#', 'b$', 'b%', 'b&','b\'',
        'b(', 'b)', 'b*', 'b+', 'b,', 'b-', 'b.', 'b/'],
      [ 'b0', 'b1', 'b2', 'b3', 'b4', 'b5', 'b6', 'b7',
      // U+00A1 inverted exclamation mark
      // U+00BF inverted question mark
        'b8', 'b9', 'b:', 'b;', 'b¡', 'b=', 'b¿', 'b?'],
      [ 'b@', 'bA', 'bB', 'bC', 'bD', 'bE', 'bF', 'bG',
        'bH', 'bI', 'bJ', 'bK', 'bL', 'bM', 'bN', 'bO'],
      [ 'bP', 'bQ', 'bR', 'bS', 'bT', 'bU', 'bV', 'bW',
        'bX', 'bY', 'bZ', 'b[','b``', 'b]', 'b^','b^.'],
      [ null, 'ba', 'bb', 'bc', 'bd', 'be', 'bf', 'bg',
        'bh', 'bi', 'bj', 'bk', 'bl', 'bm', 'bn', 'bo'],
      [ 'bp', 'bq', 'br', 'bs', 'bt', 'bu', 'bv', 'bw',
      // U+00A8 diaeresis (spacing)
        'bx', 'by', 'bz', null, null, null, 'b~', 'b¨']
    ],
    cmex10: [
      // U+230A left floor
      // U+230B right floor
      // U+2308 right ceiling
      // U+2309 right ceiling
      [ '(1', ')1', '[1', ']1', '⌊1', '⌋1', '⌈1', '⌉1',
        '{1', '}1', '<1', '>1', null, null, '/1','\\1'],
      [ '(2', ')2', '(3', ')3', '[3', ']3', '⌊3', '⌋3',
        '⌈3', '⌉3', '{3', '}3', '<3', '>3', '/3','\\3'],
      [ '(4', ')4', '[4', ']4', '⌊4', '⌋4', '⌈4', '⌉4',
        '{4', '}4', '<4', '>4', '/4','\\4', '/2','\\2'],
      [ '(u', ')u', '[u', ']u', '[l', ']l', '[m', ']m',
        '{u', '}u', '{l', '}l', '{m', '}m', null, null],
      [ '(l', ')l', '(m', ')m', '<2', '>2', null, null,
        null, null, null, null, null, null, null, null],
      [ null, null, null, null, null, null, null, null,
        null, null, null, null, null, null, null, null],
      [ null, null, null, null, null, null, null, null,
        '[2', ']2', '⌊2', '⌋2', '⌈2', '⌉2', '{2', '}2'],
      [ 'v1', 'v2', 'v3', 'v4', 'vl', 'vm', 'vu', null,
        null, null, null, null, null, null, null, null]
    ],
    cmmi10: [
      // U+0393 Greek capital letter gamma
      // U+0349 Greek capital letter delta
      // U+0398 Greek capital letter theta
      // U+039E Greek capital letter xi
      // U+039B Greek capital letter lamda
      // U+03A0 Greek capital letter pi
      // U+03A3 Greek capital letter sigma
      // U+03D2 Greek upsilon with hook symbol
      [ 'mΓ', 'mΔ', 'mΘ', 'mΛ', 'mΞ', 'mΠ', 'mΣ', 'mϒ',
      // U+03A6 Greek capital letter phi
      // U+03A8 Greek capital letter psi
      // U+03A9 Greek capital letter omega
      // U+03B1 Greek small letter alpha
      // U+03B2 Greek small letter beta
      // U+03B3 Greek small letter gamma
      // U+03B4 Greek small letter delta
      // U+03F5 Greek lunate epsilon symbol
        'mΦ', 'mΨ', 'mΩ', 'mα', 'mβ', 'mγ', 'mδ', 'mϵ'],
      // U+03B6 Greek small letter zeta
      // U+03B7 Greek small letter eta
      // U+03B8 Greek small letter theta
      // U+03B9 Greek small letter iota
      // U+03BA Greek small letter kappa
      // U+03BB Greek small letter lamda
      // U+03BC Greek small letter mu
      // U+03BD Greek small letter nu
      [ 'mζ', 'mη', 'mθ', 'mι', 'mκ', 'λ', 'mμ', 'mν',
      // U+03BE Greek small letter xi
      // U+03C0 Greek small letter pi
      // U+03C1 Greek small letter rho
      // U+03C3 Greek small letter sigma
      // U+03C4 Greek small letter tau
      // U+03C5 Greek small letter upsilon
      // U+03D5 Greek phi symbol
      // U+03C7 Greek small letter chi
        'mξ', 'π', 'mρ', 'mσ', 'mτ', 'mυ', 'mϕ', 'mχ'],
      // U+03C8 Greek small letter psi
      // U+03C9 Greek small letter omega
      // U+03B5 Greek small letter epsilon
      // U+03D1 Greek theta symbol
      // U+03D6 Greek pi symbol
      // U+03F1 Greek rho symbol
      // U+03C2 Greek small letter final sigma
      // U+03C6 Greek small letter phi
      [ 'mψ', 'mω', 'mε', 'mϑ', 'mϖ', 'mϱ', 'mς', 'mφ',
        null, null, null, null, null, null, null, null],
      [ 'm0', 'm1', 'm2', 'm3', 'm4', 'm5', 'm6', 'm7',
        'm8', 'm9', 'm.', 'm,',  '<', 'm/',  '>', 'm*'],
      // U+2202 Partial differential
      [  '∂', 'mA', 'mB', 'mC', 'mD', 'mE', 'mF', 'mG',
        'mH', 'mI', 'mJ', 'mK', 'mL', 'mM', 'mN', 'mO'],
      [ 'mP', 'mQ', 'mR', 'mS', 'mT', 'mU', 'mV', 'mW',
        'mX', 'mY', 'mZ', null, null, null, null, null],
      [ null, 'ma', 'mb', 'mc', 'md', 'me', 'mf', 'mg',
        'mh', 'mi', 'mj', 'mk', 'ml', 'mm', 'mn', 'mo'],
      [ 'mp', 'mq', 'mr', 'ms', 'mt', 'mu', 'mv', 'mw',
        'mx', 'my', 'mz', null, null, null, null, null]
    ],
    cmsy10: [
      // U+00B7 middle dot
      // U+00D7 multiplication sign
      // U+204E low asterisk
      // U+00F7 division sign
      // U+22C4 diamond operator 
      // U+00B1 plus-minus sign
      // U+2213 minus-or-plus sign
      [  '-',  '·',  '×',  '⁎',  '÷',  '⋄',  '±',  '∓',
      // U+2295 circled plus
      // U+2296 circled minus
      // U+2297 circled times
      // U+2298 circled division slash
      // U+2299 circled dot operator
      // U+25CB white circle
      // U+2218 ring operator
      // U+2219 bullet operator
         '⊕',  '⊖',  '⊗',  '⊘',  '⊙',  '○',  '∘',  '∙' ],
      // U+224D equivalent to
      // U+2261 identical to
      // U+2286 subset of or equal to
      // U+2287 superset of or equal to
      // U+2264 less than or equal to
      // U+2265 greater than or equal to
      // U+227C precedes or equal to
      // U+227D succeeds or equal to
      [  '≍',  '≡',  '⊆',  '⊇',  '≤',  '≥',  '≼',  '≽',
      // U+223C tilde operator
      // U+2248 almost equal to
      // U+2282 subset of
      // U+2283 superset of
      // U+226A much less-than
      // U+226B much greater-than
      // U+227A precedes
      // U+227B succeeds
         '∼',  '≈',  '⊂',  '⊃',  '≪',  '≫',  '≺',  '≻' ],
      // U+2190 leftwards arrow
      // U+2192 rightwards arrow
      // U+2191 upwards arrow
      // U+2193 downwards arrow
      // U+2194 left right arrow
      // U+2197 north east arrow
      // U+2198 south east arrow
      // U+2243 asymptotically equal to
      [  '←',  '→',  '↑',  '↓',  '↔',  '↗',  '↘',  '≃',
      // U+21D0 leftwards double arrow
      // U+21D2 rightwards double arrow
      // U+21D1 upwards double arrow
      // U+21D3 downwards double arrow
      // U+21D4 left right double arrow
      // U+2196 north west arrow
      // U+2199 south west arrow
      // U+221D proportional to
         '⇐',  '⇒',  '⇑',  '⇓',  '⇔',  '↖',  '↙',  '∝' ],
      // U+2032 [superscript] prime
      // U+221E infinity
      // U+2208 element of
      // U+220B contains as member
      // U+25B3 white up-pointing triangle
      // U+25BD white down-pointing triangle
      // U+2215 division slash
      [  '′',  '∞',  '∈',  '∋',  '△',  '▽',  '∕', null,
      // U+2200 for all
      // U+2203 there exists
      // U+00AC not sign
      // U+2205 empty set
      // U+211C black-letter capital r
      // U+2111 black-letter capital i
      // U+22A4 down tack
      // U+22A5 up tack
         '∀',  '∃',  '¬',  '∅',  'ℜ',  'ℑ',  '⊤', '⊥' ],
      // U+2135 alef symbol
      [  'ℵ', null, null, null, null, null, null, null,
        null, null, null, null, null, null, null, null ],
      [ null, null, null, null, null, null, null, null,
      // U+222A union
      // U+2229 intersection
      // U+2227 logical and
      // U+2228 logical or
      // U+228E multiset union
        null, null, null,  '∪',  '∩',  '⊎',  '∧',  '∨' ],
      // U+22A2 right tack
      // U+22A3 left tack
      // U+230A left floor
      // U+230B right floor
      [  '⊢',  '⊣',  '⌊',  '⌋',  '⌈',  '⌉',  '{',  '}',
      // U+27E8 mathematical left angle bracket
      // U+27E9 mathematical right angle bracket
      // U+2225 parallel to
      // U+2195 up down arrow
      // U+21D5 up down double arrow
      // U+2240 wreath product
         '⟨',  '⟩',  '|',  '∥',  '↕',  '⇕', '\\',  '≀' ],
      // U+221A square root
      // U+2210 n-ary coproduct
      // U+2207 nabla
      // U+222B integral
      // U+2294 square cup
      // U+2293 square cap
      // U+2291 square image of or equal to
      // U+2292 square original of or equal to
      [  '√',  '∐',  '∇',  '∫',  '⊔',  '⊓',  '⊑',  '⊒',
      // U+00A7 section sign
      // U+2020 dagger
      // U+2021 double dagger
      // U+00B6 pilcrow sign
      // U+2663 black club suit
      // U+2662 white diamond suit
      // U+2661 white heart suit
      // U+2660 black spade suit
         '§',  '†',  '‡',  '¶',  '♣',  '♢',  '♡',  '♠' ]
    ]
  };

  org.mathdox.formulaeditor.MathCanvas.specialSymbols = {
    // U+2146 differential D
    'ⅆ': [ 'd' ],
    // U+2260 not equal to
    // U+2215 division slash
    '≠': [ '=', '∕' ]
  };

  org.mathdox.formulaeditor.MathCanvas.fillSymbolPositions = function() {
    var sp,sif;
    if (!org.mathdox.formulaeditor.MathCanvas.symbolPositions) {
      org.mathdox.formulaeditor.MathCanvas.symbolPositions = {};
    }
    sp = org.mathdox.formulaeditor.MathCanvas.symbolPositions;
    sif = org.mathdox.formulaeditor.MathCanvas.symbolsInFont;

    for (var font in sif) {
      var symbolsArray = sif[font];
      for (var row = 0; row<symbolsArray.length; row++) {
        for (var col = 0; col<symbolsArray[row].length; col++) {
          var symbol = symbolsArray[row][col];
          if (symbol !== null && symbol !== undefined) {
            if (symbol in sp) {
              alert("duplicate entry for \""+symbol+"\"\n"+sp[symbol].font+
                ": ("+sp[symbol].row+", "+sp[symbol].col+")\n"+
                font+": ("+row+", "+col+")\n");
            } else {
              sp[symbol] = {
                font: font,
                row: row,
                col: col
              };
            }
          }
        }
      }
    }
  };

  org.mathdox.formulaeditor.MathCanvas.fillSymbolPositions();

});
