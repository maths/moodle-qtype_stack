
$identify("org/mathdox/formulaeditor/modules/variables.js");

$require("org/mathdox/formulaeditor/semantics/Variable.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");
$require("org/mathdox/formulaeditor/parsing/expression/VariableList.js");
$require("org/mathdox/formulaeditor/parsing/openmath/VariableList.js");
$require("org/mathdox/parsing/ParserGenerator.js");

$main(function(){

  var semantics = org.mathdox.formulaeditor.semantics;
  var name;
  var symbol;
  var newvars = [];
  var regex = /^[A-Za-z]*$/;

  var hasOnlyLetters = function(s) {
    return regex.test(s);
  };

  
  /**
   * Define the alpha variable.
   */
  name = "alpha";
  symbol = { 
    onscreen: "α", 
    mathml : "<mi>α</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["alpha"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["α"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["alpha"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("α") ) {
    newvars.push( "α" );
  }
  
  /**
   * Define the beta variable.
   */
  name = "beta";
  symbol = { 
    onscreen: "β", 
    mathml : "<mi>β</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["beta"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["β"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["beta"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("β") ) {
    newvars.push( "β" );
  }
  
  /**
   * Define the gamma variable.
   */
  name = "gamma";
  symbol = { 
    onscreen: "γ", 
    mathml : "<mi>γ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["gamma"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["γ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["gamma"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("γ") ) {
    newvars.push( "γ" );
  }
  
  /**
   * Define the delta variable.
   */
  name = "delta";
  symbol = { 
    onscreen: "δ", 
    mathml : "<mi>δ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["delta"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["δ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["delta"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("δ") ) {
    newvars.push( "δ" );
  }
  
  /**
   * Define the epsilon variable.
   */
  name = "epsilon";
  symbol = { 
    onscreen: "ϵ", 
    mathml : "<mi>ϵ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["epsilon"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["ϵ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["epsilon"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("ϵ") ) {
    newvars.push( "ϵ" );
  }
  
  /**
   * Define the varepsilon variable.
   */
  name = "varepsilon";
  symbol = { 
    onscreen: "φ", 
    mathml : "<mi>φ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["varepsilon"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["φ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["varepsilon"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("φ") ) {
    newvars.push( "φ" );
  }
  
  /**
   * Define the zeta variable.
   */
  name = "zeta";
  symbol = { 
    onscreen: "ζ", 
    mathml : "<mi>ζ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["zeta"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["ζ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["zeta"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("ζ") ) {
    newvars.push( "ζ" );
  }
  
  /**
   * Define the eta variable.
   */
  name = "eta";
  symbol = { 
    onscreen: "η", 
    mathml : "<mi>η</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["eta"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["η"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["eta"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("η") ) {
    newvars.push( "η" );
  }
  
  /**
   * Define the theta variable.
   */
  name = "theta";
  symbol = { 
    onscreen: "θ", 
    mathml : "<mi>θ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["theta"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["θ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["theta"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("θ") ) {
    newvars.push( "θ" );
  }
  
  /**
   * Define the vartheta variable.
   */
  name = "vartheta";
  symbol = { 
    onscreen: "ϑ", 
    mathml : "<mi>ϑ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["vartheta"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["ϑ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["vartheta"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("ϑ") ) {
    newvars.push( "ϑ" );
  }
  
  /**
   * Define the kappa variable.
   */
  name = "kappa";
  symbol = { 
    onscreen: "κ", 
    mathml : "<mi>κ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["kappa"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["κ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["kappa"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("κ") ) {
    newvars.push( "κ" );
  }
  
  /**
   * Define the mu variable.
   */
  name = "mu";
  symbol = { 
    onscreen: "μ", 
    mathml : "<mi>μ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["mu"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["μ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["mu"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("μ") ) {
    newvars.push( "μ" );
  }
  
  /**
   * Define the nu variable.
   */
  name = "nu";
  symbol = { 
    onscreen: "ν", 
    mathml : "<mi>ν</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["nu"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["ν"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["nu"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("ν") ) {
    newvars.push( "ν" );
  }
  
  /**
   * Define the xi variable.
   */
  name = "xi";
  symbol = { 
    onscreen: "ξ", 
    mathml : "<mi>ξ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["xi"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["ξ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["xi"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("ξ") ) {
    newvars.push( "ξ" );
  }
  
  /**
   * Define the varpi variable.
   */
  name = "varpi";
  symbol = { 
    onscreen: "ϖ", 
    mathml : "<mi>ϖ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["varpi"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["ϖ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["varpi"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("ϖ") ) {
    newvars.push( "ϖ" );
  }
  
  /**
   * Define the rho variable.
   */
  name = "rho";
  symbol = { 
    onscreen: "ρ", 
    mathml : "<mi>ρ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["rho"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["ρ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["rho"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("ρ") ) {
    newvars.push( "ρ" );
  }
  
  /**
   * Define the varrho variable.
   */
  name = "varrho";
  symbol = { 
    onscreen: "ϱ", 
    mathml : "<mi>ϱ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["varrho"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["ϱ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["varrho"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("ϱ") ) {
    newvars.push( "ϱ" );
  }
  
  /**
   * Define the sigma variable.
   */
  name = "sigma";
  symbol = { 
    onscreen: "σ", 
    mathml : "<mi>σ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["sigma"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["σ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["sigma"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("σ") ) {
    newvars.push( "σ" );
  }
  
  /**
   * Define the varsigma variable.
   */
  name = "varsigma";
  symbol = { 
    onscreen: "ς", 
    mathml : "<mi>ς</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["varsigma"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["ς"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["varsigma"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("ς") ) {
    newvars.push( "ς" );
  }
  
  /**
   * Define the tau variable.
   */
  name = "tau";
  symbol = { 
    onscreen: "τ", 
    mathml : "<mi>τ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["tau"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["τ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["tau"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("τ") ) {
    newvars.push( "τ" );
  }
  
  /**
   * Define the upsilon variable.
   */
  name = "upsilon";
  symbol = { 
    onscreen: "υ", 
    mathml : "<mi>υ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["upsilon"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["υ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["upsilon"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("υ") ) {
    newvars.push( "υ" );
  }
  
  /**
   * Define the phi variable.
   */
  name = "phi";
  symbol = { 
    onscreen: "ϕ", 
    mathml : "<mi>ϕ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["phi"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["ϕ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["phi"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("ϕ") ) {
    newvars.push( "ϕ" );
  }
  
  /**
   * Define the varphi variable.
   */
  name = "varphi";
  symbol = { 
    onscreen: "φ", 
    mathml : "<mi>φ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["varphi"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["φ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["varphi"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("φ") ) {
    newvars.push( "φ" );
  }
  
  /**
   * Define the chi variable.
   */
  name = "chi";
  symbol = { 
    onscreen: "χ", 
    mathml : "<mi>χ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["chi"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["χ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["chi"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("χ") ) {
    newvars.push( "χ" );
  }
  
  /**
   * Define the psi variable.
   */
  name = "psi";
  symbol = { 
    onscreen: "ψ", 
    mathml : "<mi>ψ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["psi"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["ψ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["psi"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("ψ") ) {
    newvars.push( "ψ" );
  }
  
  /**
   * Define the omega variable.
   */
  name = "omega";
  symbol = { 
    onscreen: "ω", 
    mathml : "<mi>ω</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["omega"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["ω"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["omega"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("ω") ) {
    newvars.push( "ω" );
  }
  
  /**
   * Define the Gamma variable.
   */
  name = "Gamma";
  symbol = { 
    onscreen: "Γ", 
    mathml : "<mi>Γ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["Gamma"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["Γ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["Gamma"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("Γ") ) {
    newvars.push( "Γ" );
  }
  
  /**
   * Define the Delta variable.
   */
  name = "Delta";
  symbol = { 
    onscreen: "Δ", 
    mathml : "<mi>Δ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["Delta"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["Δ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["Delta"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("Δ") ) {
    newvars.push( "Δ" );
  }
  
  /**
   * Define the Theta variable.
   */
  name = "Theta";
  symbol = { 
    onscreen: "Θ", 
    mathml : "<mi>Θ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["Theta"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["Θ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["Theta"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("Θ") ) {
    newvars.push( "Θ" );
  }
  
  /**
   * Define the Lamda variable.
   */
  name = "Lamda";
  symbol = { 
    onscreen: "Λ", 
    mathml : "<mi>Λ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["Lamda"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["Λ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["Lamda"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("Λ") ) {
    newvars.push( "Λ" );
  }
  
  /**
   * Define the Xi variable.
   */
  name = "Xi";
  symbol = { 
    onscreen: "Ξ", 
    mathml : "<mi>Ξ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["Xi"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["Ξ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["Xi"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("Ξ") ) {
    newvars.push( "Ξ" );
  }
  
  /**
   * Define the Phi variable.
   */
  name = "Phi";
  symbol = { 
    onscreen: "Φ", 
    mathml : "<mi>Φ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["Phi"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["Φ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["Phi"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("Φ") ) {
    newvars.push( "Φ" );
  }
  
  /**
   * Define the Psi variable.
   */
  name = "Psi";
  symbol = { 
    onscreen: "Ψ", 
    mathml : "<mi>Ψ</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["Psi"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["Ψ"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["Psi"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("Ψ") ) {
    newvars.push( "Ψ" );
  }
  
  /**
   * Define the Omega variable.
   */
  name = "Omega";
  symbol = { 
    onscreen: "Ω", 
    mathml : "<mi>Ω</mi>"
  };
  
  org.mathdox.formulaeditor.parsing.expression.VariableList["Omega"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.expression.VariableList["Ω"] = new semantics.Variable(name, symbol);

  org.mathdox.formulaeditor.parsing.openmath.VariableList["Omega"] = new semantics.Variable(name, symbol);

  if ( ! hasOnlyLetters("Ω") ) {
    newvars.push( "Ω" );
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
                return org.mathdox.formulaeditor.parsing.expression.VariableList[result_joined];
              }
            ),
            parent.variable).apply(this, arguments);
        }
      };
    });
  }
});
