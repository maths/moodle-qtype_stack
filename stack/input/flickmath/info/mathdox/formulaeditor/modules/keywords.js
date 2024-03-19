
$identify("org/mathdox/formulaeditor/modules/keywords.js");

$require("org/mathdox/formulaeditor/semantics/Keyword.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/KeywordList.js");
$require("org/mathdox/formulaeditor/parsing/openmath/KeywordList.js");
$require("org/mathdox/parsing/ParserGenerator.js");

$main(function(){

  var semantics = org.mathdox.formulaeditor.semantics;
  var cd;
  var name;
  var symbol;
  var newvars = [];
  var regex = /^[A-Za-z]*$/;

  var hasOnlyLetters = function(s) {
    return regex.test(s);
  };

  
  /**
   * Define the arith1.gcd keyword.
   */
  cd = "arith1";
  name = "gcd";
  symbol = { 
    onscreen: "gcd", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>gcd</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["gcd"] = new semantics.Keyword(cd, name, symbol, "function", null);

  if ( "gcd" !== "gcd" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["gcd"] = new semantics.Keyword(cd, name, symbol, "function", null);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["arith1__gcd"] = new semantics.Keyword(cd, name, symbol, "function", null);

  if ( ! hasOnlyLetters("gcd") ) {
    newvars.push( "gcd" );
  }
  
  /**
   * Define the arith1.lcm keyword.
   */
  cd = "arith1";
  name = "lcm";
  symbol = { 
    onscreen: "lcm", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>lcm</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["lcm"] = new semantics.Keyword(cd, name, symbol, "function", null);

  if ( "lcm" !== "lcm" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["lcm"] = new semantics.Keyword(cd, name, symbol, "function", null);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["arith1__lcm"] = new semantics.Keyword(cd, name, symbol, "function", null);

  if ( ! hasOnlyLetters("lcm") ) {
    newvars.push( "lcm" );
  }
  
  /**
   * Define the editor1.input_box keyword.
   */
  cd = "editor1";
  name = "input_box";
  symbol = { 
    onscreen: "□", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>□</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["□"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "□" !== "□" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["□"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["editor1__input_box"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("□") ) {
    newvars.push( "□" );
  }
  
  /**
   * Define the editor1.palette_whitespace keyword.
   */
  cd = "editor1";
  name = "palette_whitespace";
  symbol = { 
    onscreen: " ", 
    openmath : null, // use default with model:cd and model:name
    mathml : ""
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList[""] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "" !== " " ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList[" "] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["editor1__palette_whitespace"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters(" ") ) {
    newvars.push( " " );
  }
  
  /**
   * Define the linalg1.determinant keyword.
   */
  cd = "linalg1";
  name = "determinant";
  symbol = { 
    onscreen: "det", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>det</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["det"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "det" !== "det" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["det"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["linalg1__determinant"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("det") ) {
    newvars.push( "det" );
  }
  
  /**
   * Define the logic1.false keyword.
   */
  cd = "logic1";
  name = "false";
  symbol = { 
    onscreen: "false", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>false</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["false"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "false" !== "false" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["false"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["logic1__false"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("false") ) {
    newvars.push( "false" );
  }
  
  /**
   * Define the logic1.true keyword.
   */
  cd = "logic1";
  name = "true";
  symbol = { 
    onscreen: "true", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>true</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["true"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "true" !== "true" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["true"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["logic1__true"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("true") ) {
    newvars.push( "true" );
  }
  
  /**
   * Define the nums1.e keyword.
   */
  cd = "nums1";
  name = "e";
  symbol = { 
    onscreen: "e", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>e</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["e"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "e" !== "e" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["e"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["nums1__e"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("e") ) {
    newvars.push( "e" );
  }
  
  /**
   * Define the nums1.i keyword.
   */
  cd = "nums1";
  name = "i";
  symbol = { 
    onscreen: "i", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>i</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["i"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "i" !== "i" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["i"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["nums1__i"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("i") ) {
    newvars.push( "i" );
  }
  
  /**
   * Define the nums1.infinity keyword.
   */
  cd = "nums1";
  name = "infinity";
  symbol = { 
    onscreen: "∞", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>∞</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["infinity"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "infinity" !== "∞" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["∞"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["nums1__infinity"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("∞") ) {
    newvars.push( "∞" );
  }
  
  /**
   * Define the nums1.pi keyword.
   */
  cd = "nums1";
  name = "pi";
  symbol = { 
    onscreen: "π", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>π</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["pi"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "pi" !== "π" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["π"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["nums1__pi"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("π") ) {
    newvars.push( "π" );
  }
  
  /**
   * Define the permutation1.sign keyword.
   */
  cd = "permutation1";
  name = "sign";
  symbol = { 
    onscreen: "sgn", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>sgn</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["sgn"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "sgn" !== "sgn" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["sgn"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["permutation1__sign"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("sgn") ) {
    newvars.push( "sgn" );
  }
  
  /**
   * Define the plangeo7.triangle keyword.
   */
  cd = "plangeo7";
  name = "triangle";
  symbol = { 
    onscreen: "△", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mo>△</mo>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["triangle"] = new semantics.Keyword(cd, name, symbol, "function", 3);

  if ( "triangle" !== "△" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["△"] = new semantics.Keyword(cd, name, symbol, "function", 3);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["plangeo7__triangle"] = new semantics.Keyword(cd, name, symbol, "function", 3);

  if ( ! hasOnlyLetters("△") ) {
    newvars.push( "△" );
  }
  
  /**
   * Define the setname1.C keyword.
   */
  cd = "setname1";
  name = "C";
  symbol = { 
    onscreen: "ℂ", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>ℂ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["CC"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "CC" !== "ℂ" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["ℂ"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["setname1__C"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("ℂ") ) {
    newvars.push( "ℂ" );
  }
  
  /**
   * Define the setname1.N keyword.
   */
  cd = "setname1";
  name = "N";
  symbol = { 
    onscreen: "ℕ", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>ℕ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["NN"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "NN" !== "ℕ" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["ℕ"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["setname1__N"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("ℕ") ) {
    newvars.push( "ℕ" );
  }
  
  /**
   * Define the setname1.P keyword.
   */
  cd = "setname1";
  name = "P";
  symbol = { 
    onscreen: "ℙ", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>ℙ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["PP"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "PP" !== "ℙ" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["ℙ"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["setname1__P"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("ℙ") ) {
    newvars.push( "ℙ" );
  }
  
  /**
   * Define the setname1.Q keyword.
   */
  cd = "setname1";
  name = "Q";
  symbol = { 
    onscreen: "ℚ", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>ℚ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["QQ"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "QQ" !== "ℚ" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["ℚ"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["setname1__Q"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("ℚ") ) {
    newvars.push( "ℚ" );
  }
  
  /**
   * Define the setname1.R keyword.
   */
  cd = "setname1";
  name = "R";
  symbol = { 
    onscreen: "ℝ", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>ℝ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["RR"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "RR" !== "ℝ" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["ℝ"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["setname1__R"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("ℝ") ) {
    newvars.push( "ℝ" );
  }
  
  /**
   * Define the setname1.Z keyword.
   */
  cd = "setname1";
  name = "Z";
  symbol = { 
    onscreen: "ℤ", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>ℤ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["ZZ"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "ZZ" !== "ℤ" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["ℤ"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["setname1__Z"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("ℤ") ) {
    newvars.push( "ℤ" );
  }
  
  /**
   * Define the set1.emptyset keyword.
   */
  cd = "set1";
  name = "emptyset";
  symbol = { 
    onscreen: "∅", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>∅</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["emptyset"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( "emptyset" !== "∅" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["∅"] = new semantics.Keyword(cd, name, symbol, "constant", 0);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["set1__emptyset"] = new semantics.Keyword(cd, name, symbol, "constant", 0);

  if ( ! hasOnlyLetters("∅") ) {
    newvars.push( "∅" );
  }
  
  /**
   * Define the transc1.arccos keyword.
   */
  cd = "transc1";
  name = "arccos";
  symbol = { 
    onscreen: "arccos", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>arccos</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["arccos"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "arccos" !== "arccos" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["arccos"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__arccos"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("arccos") ) {
    newvars.push( "arccos" );
  }
  
  /**
   * Define the transc1.arccosh keyword.
   */
  cd = "transc1";
  name = "arccosh";
  symbol = { 
    onscreen: "arccosh", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>arccosh</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["arccosh"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "arccosh" !== "arccosh" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["arccosh"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__arccosh"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("arccosh") ) {
    newvars.push( "arccosh" );
  }
  
  /**
   * Define the transc1.arccot keyword.
   */
  cd = "transc1";
  name = "arccot";
  symbol = { 
    onscreen: "arccot", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>arccot</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["arccot"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "arccot" !== "arccot" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["arccot"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__arccot"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("arccot") ) {
    newvars.push( "arccot" );
  }
  
  /**
   * Define the transc1.arccoth keyword.
   */
  cd = "transc1";
  name = "arccoth";
  symbol = { 
    onscreen: "arccoth", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>arccoth</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["arccoth"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "arccoth" !== "arccoth" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["arccoth"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__arccoth"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("arccoth") ) {
    newvars.push( "arccoth" );
  }
  
  /**
   * Define the transc1.arccsc keyword.
   */
  cd = "transc1";
  name = "arccsc";
  symbol = { 
    onscreen: "arccsc", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>arccsc</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["arccsc"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "arccsc" !== "arccsc" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["arccsc"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__arccsc"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("arccsc") ) {
    newvars.push( "arccsc" );
  }
  
  /**
   * Define the transc1.arccsch keyword.
   */
  cd = "transc1";
  name = "arccsch";
  symbol = { 
    onscreen: "arccsch", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>arccsch</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["arccsch"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "arccsch" !== "arccsch" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["arccsch"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__arccsch"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("arccsch") ) {
    newvars.push( "arccsch" );
  }
  
  /**
   * Define the transc1.arcsec keyword.
   */
  cd = "transc1";
  name = "arcsec";
  symbol = { 
    onscreen: "arcsec", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>arcsec</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["arcsec"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "arcsec" !== "arcsec" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["arcsec"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__arcsec"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("arcsec") ) {
    newvars.push( "arcsec" );
  }
  
  /**
   * Define the transc1.arcsech keyword.
   */
  cd = "transc1";
  name = "arcsech";
  symbol = { 
    onscreen: "arcsech", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>arcsech</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["arcsech"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "arcsech" !== "arcsech" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["arcsech"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__arcsech"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("arcsech") ) {
    newvars.push( "arcsech" );
  }
  
  /**
   * Define the transc1.arcsin keyword.
   */
  cd = "transc1";
  name = "arcsin";
  symbol = { 
    onscreen: "arcsin", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>arcsin</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["arcsin"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "arcsin" !== "arcsin" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["arcsin"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__arcsin"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("arcsin") ) {
    newvars.push( "arcsin" );
  }
  
  /**
   * Define the transc1.arcsinh keyword.
   */
  cd = "transc1";
  name = "arcsinh";
  symbol = { 
    onscreen: "arcsinh", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>arcsinh</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["arcsinh"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "arcsinh" !== "arcsinh" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["arcsinh"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__arcsinh"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("arcsinh") ) {
    newvars.push( "arcsinh" );
  }
  
  /**
   * Define the transc1.arctan keyword.
   */
  cd = "transc1";
  name = "arctan";
  symbol = { 
    onscreen: "arctan", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>arctan</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["arctan"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "arctan" !== "arctan" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["arctan"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__arctan"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("arctan") ) {
    newvars.push( "arctan" );
  }
  
  /**
   * Define the transc1.arctanh keyword.
   */
  cd = "transc1";
  name = "arctanh";
  symbol = { 
    onscreen: "arctanh", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>arctanh</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["arctanh"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "arctanh" !== "arctanh" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["arctanh"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__arctanh"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("arctanh") ) {
    newvars.push( "arctanh" );
  }
  
  /**
   * Define the transc1.cos keyword.
   */
  cd = "transc1";
  name = "cos";
  symbol = { 
    onscreen: "cos", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>cos</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["cos"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "cos" !== "cos" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["cos"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__cos"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("cos") ) {
    newvars.push( "cos" );
  }
  
  /**
   * Define the transc1.cosh keyword.
   */
  cd = "transc1";
  name = "cosh";
  symbol = { 
    onscreen: "cosh", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>cosh</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["cosh"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "cosh" !== "cosh" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["cosh"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__cosh"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("cosh") ) {
    newvars.push( "cosh" );
  }
  
  /**
   * Define the transc1.cot keyword.
   */
  cd = "transc1";
  name = "cot";
  symbol = { 
    onscreen: "cot", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>cot</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["cot"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "cot" !== "cot" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["cot"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__cot"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("cot") ) {
    newvars.push( "cot" );
  }
  
  /**
   * Define the transc1.coth keyword.
   */
  cd = "transc1";
  name = "coth";
  symbol = { 
    onscreen: "coth", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>coth</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["coth"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "coth" !== "coth" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["coth"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__coth"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("coth") ) {
    newvars.push( "coth" );
  }
  
  /**
   * Define the transc1.csc keyword.
   */
  cd = "transc1";
  name = "csc";
  symbol = { 
    onscreen: "csc", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>csc</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["csc"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "csc" !== "csc" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["csc"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__csc"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("csc") ) {
    newvars.push( "csc" );
  }
  
  /**
   * Define the transc1.csch keyword.
   */
  cd = "transc1";
  name = "csch";
  symbol = { 
    onscreen: "csch", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>csch</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["csch"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "csch" !== "csch" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["csch"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__csch"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("csch") ) {
    newvars.push( "csch" );
  }
  
  /**
   * Define the transc1.exp keyword.
   */
  cd = "transc1";
  name = "exp";
  symbol = { 
    onscreen: "exp", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>exp</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["exp"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "exp" !== "exp" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["exp"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__exp"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("exp") ) {
    newvars.push( "exp" );
  }
  
  /**
   * Define the transc1.ln keyword.
   */
  cd = "transc1";
  name = "ln";
  symbol = { 
    onscreen: "ln", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>ln</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["ln"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "ln" !== "ln" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["ln"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__ln"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("ln") ) {
    newvars.push( "ln" );
  }
  
  /**
   * Define the transc1.log keyword.
   */
  cd = "transc1";
  name = "log";
  symbol = { 
    onscreen: "log", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>log</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["log"] = new semantics.Keyword(cd, name, symbol, "function", 2);

  if ( "log" !== "log" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["log"] = new semantics.Keyword(cd, name, symbol, "function", 2);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__log"] = new semantics.Keyword(cd, name, symbol, "function", 2);

  if ( ! hasOnlyLetters("log") ) {
    newvars.push( "log" );
  }
  
  /**
   * Define the transc1.sec keyword.
   */
  cd = "transc1";
  name = "sec";
  symbol = { 
    onscreen: "sec", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>sec</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["sec"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "sec" !== "sec" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["sec"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__sec"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("sec") ) {
    newvars.push( "sec" );
  }
  
  /**
   * Define the transc1.sech keyword.
   */
  cd = "transc1";
  name = "sech";
  symbol = { 
    onscreen: "sech", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>sech</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["sech"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "sech" !== "sech" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["sech"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__sech"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("sech") ) {
    newvars.push( "sech" );
  }
  
  /**
   * Define the transc1.sin keyword.
   */
  cd = "transc1";
  name = "sin";
  symbol = { 
    onscreen: "sin", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>sin</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["sin"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "sin" !== "sin" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["sin"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__sin"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("sin") ) {
    newvars.push( "sin" );
  }
  
  /**
   * Define the transc1.sinh keyword.
   */
  cd = "transc1";
  name = "sinh";
  symbol = { 
    onscreen: "sinh", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>sinh</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["sinh"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "sinh" !== "sinh" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["sinh"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__sinh"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("sinh") ) {
    newvars.push( "sinh" );
  }
  
  /**
   * Define the transc1.tan keyword.
   */
  cd = "transc1";
  name = "tan";
  symbol = { 
    onscreen: "tan", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>tan</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["tan"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "tan" !== "tan" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["tan"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__tan"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("tan") ) {
    newvars.push( "tan" );
  }
  
  /**
   * Define the transc1.tanh keyword.
   */
  cd = "transc1";
  name = "tanh";
  symbol = { 
    onscreen: "tanh", 
    openmath : null, // use default with model:cd and model:name
    mathml : "<mi>tanh</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.KeywordList["tanh"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( "tanh" !== "tanh" ) {
    org.mathdox.formulaeditor.parsing.expression.KeywordList["tanh"] = new semantics.Keyword(cd, name, symbol, "function", 1);
  }

  org.mathdox.formulaeditor.parsing.openmath.KeywordList["transc1__tanh"] = new semantics.Keyword(cd, name, symbol, "function", 1);

  if ( ! hasOnlyLetters("tanh") ) {
    newvars.push( "tanh" );
  }
  
  var pG = new org.mathdox.parsing.ParserGenerator();
  if(newvars.length > 0) {
    var args = [];
    for (var i=0;i < newvars.length; i++) {
      args.push(pG.literal(newvars[i]));
    }
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction( 
      function(context) { return {
        variable : function() {
          var parent = arguments.callee.parent;
          pG.alternation(
            pG.transform(
              pG.alternation.apply(this, args),
              function(result) {
                var result_joined = result.join("");

                // this should be in the keywordlist
                return org.mathdox.formulaeditor.parsing.expression.KeywordList[result_joined];
              }
            ),
            parent.variable).apply(this, arguments);
        }
      };
    });
  }
});
