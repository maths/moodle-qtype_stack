$package("org.mathdox.formulaeditor.options");

$identify("org/mathdox/formulaeditor/Options.js");

// currently only org.mathdox.formulaeditor.options should be created
// further functions could be placed in an org.mathdox.formulaeditor.options
// record.

// ancientOrbeon: if set to true: do not warn about old orbeon
// contextParsingExpression: set to an object describing the context for the ExpressionParser
// debug: is debug enabled ?
// decimalMark: character for decimal mark
// - '.' : use period as in US
// - ',' : use comma as in Europe
// dragPalette: if set to true: enable draggable Palette
// fontSize: set font size 
// paletteHighlight: highlight Palette
// - true (default): highlight Palette onmouseover
// - false: do not highlight Palette
// indentXML: indent created XML
// inputStyle: set default style for Editor Canvases
// modeArith1Divide: set mode for handling arith1.divide
// - normal: automatically put unary minus and times expressions as enumerators
// - restricted: only parse power and higher priority (default)
// optionArith1PowerInversePrefix
// - true : allow sin^-1(x) -> arcsin(x)
// - false : (default)
// optionArith1PowerPrefix
// - true : allow sin^2(x)
// - false : (default)
// optionArith1UnaryMinusBrackets
// - true: lower priority unary_minus, e.g. 1 + (-2)
// - false: "normal" priority unary_minus e.g. 1 + -2 (default)
// optionInterval1Brackets
// - object with 4 strings
//   lo: left symbol for left-open interval
//   lc: left symbol for left-closed interval
//   ro: right symbol for right-open interval
//   rc: right symbol for right-closed interval
//   default: { lo: "(", lc: "[", ro: ")", rc: "]" }
// optionListSeparatorFixed
// - list separator for non-editable lists
//   default : same as getListSeparator()
// optionResizeBrackets
// - true : use Bracketed when loading openmath
// - false : do not use Bracketed
//   NOTE: Bracketed can only be deleted completely.
// optionVerboseStyle
// - "true": add style to divide and times
// - "false": (default)
// onloadFocus: set focus on load 
// - true gives focus to the first formulaeditor
// - <id> as string gives focus to the formulaeditor corresponding to the
//   textarea with id <id>
// paletteShow : default behaviour when showing palettes, choices : 
// - "all" gives a palette if not specified by class
// - "none" gives no palette if not specified by class
// - "one" (default) gives a palette if not specified by class when there
//   is none in the page yet, 
// paletteStyle: set default style for Palette Canvases
// paletteURL: url for palette
// styleArith1Times: behavior for times symbol
// - "dot" show a middle dot (default)
// - "cross" show a cross
// - "star" show an asterisk
// styleTransc1Log: behavior for logarithm symbol
// - "function" log(10, x)
// - "prefix"   ^10 log (x)
// - "postfix"  log_10(x)
// undo: whether to enable experimental undo
// - true (default): enable undo
// - false: disable undo
// useBar : enable Bar to turn palette on/off
// - true (default): enable bar
// - false: disable bar

$main(function() {
  org.mathdox.formulaeditor.Options = $extend(Object, {
    defaultOptions : {
      debug: false,
      decimalMark: '.',
      featureUndo: true,
      modeArith1Divide: 'restricted',
      optionVerboseStyle: 'false',
      optionArith1UnaryMinusBrackets : 'false',
      optionInterval1Brackets : {lo: '(', lc: '[', ro: ')', rc:']'},
      optionResizeBrackets: true,
      styleArith1Divide: 'mfrac',
      styleArith1Times: 'dot',
      styleTransc1Log: 'function',
      symbolArith1Times: '·' // U+00B7 Middle dot
    },
    getOption : function(name) {
      if (org.mathdox.formulaeditor.options[name] !== undefined) {
        return org.mathdox.formulaeditor.options[name];
      } else if (this.defaultOptions[name] !== undefined) {
        return this.defaultOptions[name];
      } else {
	return null;
      }
    },
    getArith1DivideMode : function () {
      var option = this.getOption("modeArith1Divide");

      if (option == 'normal' || option == 'restricted' || option == 'inline') {
      	return option;
      } else {
        return "restricted";
      }
    },
    /*
    getArith1DivideSymbol : function () {
      if (option == 'colon') {
        return ':'; // normal colon, 
        // NOTE: it might be better to return U+2236 ratio, but that would be
        // confusing to the user
      } else if (option == 'div') {
        return '÷'; // U+00F7 is division sign 
      } else if (option == 'slash') {
        return '∕'; // U+2215 is division slash
      } else {
        return '∕'; // U+2215 is division slash
      }
    }, */
    getArith1PowerOptionInversePrefix : function () {
      var option = this.getOption("optionArith1PowerInversePrefix");

      if (option == 'true') {
      	return "true";
      } else {
        return "false";
      }
    },
    getArith1PowerOptionPrefix : function () {
      var option = this.getOption("optionArith1PowerPrefix");

      if (option == 'true') {
      	return "true";
      } else {
        return "false";
      }
    },
    getArith1TimesStyle : function () {
      var option = this.getOption("styleArith1Times");

      if (option == 'dot' || option == 'cross' || option == 'star') {
	return option;
      }

      return this.defaultOptions.styleArith1Times;
    },
    getArith1TimesSymbol : function () {
      var option = this.getOption("styleArith1Times");

      if (option == 'dot') {
        return '·'; // U+00B7 Middle dot
      } else if (option == 'cross') {
        return '×'; // U+00D7 is cross
      } else if (option == 'star') {
        return '*';
      }
      return this.defaultOptions.symbolArith1Times;
    },
    getArith1UnaryMinusOptionBrackets : function() {
     var option = this.getOption("optionArith1UnaryMinusBrackets");

      if (option == 'true') {
      	return "true";
      } else {
        return "false";
      }
    },
    getDecimalMark: function() {
      var mark = this.getOption("decimalMark");
      if (mark === '.' || mark === ',') {
        return mark;
      } else { 
        // use default 
        return this.defaultOptions.decimalMark;
      }
    },
    getInterval1BracketsOption: function() {
     var option = this.getOption("optionInterval1Brackets");

      if (typeof option === "object" && typeof option.lo === "string" && typeof option.lc === "string" && typeof option.ro === "string" && typeof option.rc === "string") {
      	return option;
      } else {
        console.log("ERROR: invalid option for Interval1Brackets: "+option);
	return this.defaultOptions.optionInterval1Brackets;
      }
    },
    getListSeparator : function() {
      var mark = this.getDecimalMark();
      
      if (mark === '.') {
        return ',';
      } else if (mark === ',') {
        return ';';
      } else { // should not happen
        alert("Options: unable to get listseparator.");
        return null;
      }
    },
    getListSeparatorFixed : function() {
      var option = this.getOption("optionListSeparatorFixed");
      var sep = this.getListSeparator();
      
      if (option !== null) {
	return option;
      } else {
	return sep;
      }
    },
    getResizeBracketsOption : function() {
      var option = this.getOption("optionResizeBrackets");

      if (option == true || option == false) {
        return option;
      } 

      return this.defaultOptions.optionResizeBrackets;
    },

    getTransc1LogStyle : function () {
      var option = this.getOption("styleTransc1Log");

      if (option == 'prefix' || option == 'postfix' || option == 'function') {
        return option;
      } 

      return this.defaultOptions.styleTransc1Log;
    },
    getVerboseStyleOption : function() {
      var option = this.getOption("optionVerboseStyle");

      if (option == 'true' || option == 'false') {
        return option;
      } 

      return this.defaultOptions.optionVerboseStyle;
    },

    getExpressionParsingContext: function() {
      return {
        decimalMark                    : this.getDecimalMark(),
        listSeparator                  : this.getListSeparator(),
        optionArith1DivideMode         : this.getArith1DivideMode(),
        optionArith1PowerInversePrefix : this.getArith1PowerOptionInversePrefix(),
        optionArith1PowerPrefix        : this.getArith1PowerOptionPrefix(),
        optionArith1UnaryMinusBrackets : this.getArith1UnaryMinusOptionBrackets(),
        styleTransc1Log                : this.getTransc1LogStyle(),
        symbolArith1Times              : this.getArith1TimesSymbol()
      };
    },

    getPresentationContext: function() {
      return {
        decimalMark                    : this.getDecimalMark(),
        listSeparator                  : this.getListSeparator(),
        listSeparatorFixed             : this.getListSeparatorFixed(),
        modeArith1Divide               : this.getArith1DivideMode(),
        optionArith1UnaryMinusBrackets : this.getArith1UnaryMinusOptionBrackets(),
        optionInterval1Brackets        : this.getInterval1BracketsOption(),
        optionResizeBrackets           : this.getResizeBracketsOption(),
        styleTransc1Log                : this.getTransc1LogStyle(),
        symbolArith1Times              : this.getArith1TimesSymbol()
      };
    }
  });
});

