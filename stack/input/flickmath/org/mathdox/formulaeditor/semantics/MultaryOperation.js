$package("org.mathdox.formulaeditor.semantics");

$identify("org/mathdox/formulaeditor/semantics/MultaryOperation.js");

$require("org/mathdox/formulaeditor/semantics/Node.js");
$require("org/mathdox/formulaeditor/presentation/Row.js");
$require("org/mathdox/formulaeditor/presentation/Symbol.js");

$main(function(){

  /**
   * Representation of an n-ary infix operation.
   */
  org.mathdox.formulaeditor.semantics.MultaryOperation =
    $extend(org.mathdox.formulaeditor.semantics.Node, {

      /**
       * The operands of the operation.
       */
      operands: null,

      /**
       * Information about the symbol that is used to represent this operation.
       */
      symbol : {

        /**
         * The symbol that is used for rendering the operation to the screen.
         */
        onscreen         : null,

        /**
         * The OpenMath symbol that is associated with this operation.
         */
        openmath         : null,

        /**
         * The MathML representation of this operation.
         */
        mathml           : null,

        /**
         * The MathML invisible representation of this operation (if any)
         */
        mathml_invisible : null

      },

      getSymbolMathML : function(context) {
        return this.symbol.mathml;
      },

      getSymbolOnscreen : function(context) {
        return this.symbol.onscreen;
      },

      getSymbolOpenMath : function(context) {
        return this.symbol.openmath;
      },

      /**
       * The precedence level of the operator.
       */
      precedence : 0,

      getPrecedence : function(context) {
	return this.precedence;
      },
      getInnerPrecedence : function(context) {
	return this.getPrecedence(context);
      },
 
      /**
       * Is the operator associative
       *
       * if false: put brackets around the second argument also if it has an
       * operator with the same precedence. Example: a-(b-c)
       */
      associative : true,

      /**
       * style if any (like "invisible")
       */ 
      style:null,

      /**
       * Initializes the operation using the specified arguments as operands.
       */
      initialize : function() {
        this.operands = arguments;
      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getPresentation(context)
       */
      getPresentation : function(context) {

        var presentation = org.mathdox.formulaeditor.presentation;

        // construct an array of the presentation of operand nodes interleaved
        // with operator symbols
        var array = [];
        var i;
        var symbolOnscreen = this.getSymbolOnscreen(context);
        if (this.style != "invisible" && symbolOnscreen instanceof Array) {
          if (symbolOnscreen[0]!=="") {
            array.push(new presentation.Row(symbolOnscreen[0]));
          }
        }
        for (i=0; i<this.operands.length; i++) {
          var operand = this.operands[i];
          if (i>0 && this.style != "invisible" ) {
            if (symbolOnscreen instanceof Array) {
              if (symbolOnscreen[1]!=="") {
                array.push(new presentation.Row(symbolOnscreen[1]));
              }
            }
            else {
              array.push(new presentation.Row(symbolOnscreen));
            }
          }
          //if (operand.precedence && ((operand.precedence < this.precedence) || ((this.associative==false) && i>0 && operand.precedence <= this.precedence))) {
          if (operand.getPrecedence && operand.getPrecedence(context) != 0 && ((operand.getPrecedence(context) < this.getInnerPrecedence(context)) || 
	     (operand.getPrecedence(context) == this.getInnerPrecedence(context) && 
	       (i>0 || (this.associative==true && this.symbol.openmath == operand.symbol.openmath) ||
		(this.operands.length == 1)
		)) 
	     )) {
            array.push(new presentation.Symbol("("));
            array.push(operand.getPresentation(context));
            array.push(new presentation.Symbol(")"));
          }
          else {
            array.push(operand.getPresentation(context));
          }
        }
        if (this.style != "invisible" && symbolOnscreen instanceof Array) {
          if (symbolOnscreen[2]!=="") {
            array.push(new presentation.Row(symbolOnscreen[2]));
          }
        }

        // create and return new presentation row using the constructed array
        var result = new presentation.Row();
        result.initialize.apply(result, array);
        return result;

      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getOpenMath()
       */
      getOpenMath : function() {
        var semantics = org.mathdox.formulaeditor.semantics;
	var result;

        var argtest = this.checkArguments(this.operands);

        if (typeof argtest === "string") {
          result = "<OME><OMS cd='moreerrors' name='encodingError'/>";
          result += "<OMSTR>invalid expression entered: "+ argtest+"</OMSTR>";
          result += "</OME>";
          return result;
        }

        var result = "<OMA";

        // add style (like invisible) if present
        if (this.style) {
          result = result + " style='" + this.style + "'";
        }

        result = result + ">" + this.getSymbolOpenMath();
        for (var i=0; i<this.operands.length; i++) {
          result = result + this.operands[i].getOpenMath();
        }
        result = result + "</OMA>";
        return result;

      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getMathML()
       */
      getMathML : function() {

        var result = "<mrow>";
        var symbol_mathml = this.getSymbolMathML();

	if (this.style == "invisible" && (this.symbol.mathml_invisible !== undefined && this.symbol.mathml_invisible !== null)) {
          symbol_mathml = this.symbol.mathml_invisible;
	}

        if (symbol_mathml instanceof Array) {
          result = result + symbol_mathml[0];
        }

        for (var i=0; i<this.operands.length; i++) {
          var operand = this.operands[i];
          if (i>0) {
            if (symbol_mathml instanceof Array) {
              result = result + symbol_mathml[1];
            } else {
              result = result + symbol_mathml;
            }
          }
          if (operand.precedence && ((operand.precedence < this.precedence) || ((this.associative==false) && i>0 && operand.precedence <= this.precedence))) {
            result = result + "<mfenced>";
            result = result + this.operands[i].getMathML();
            result = result + "</mfenced>";
          }
          else {
            result = result + this.operands[i].getMathML();
          }
        }

        if (symbol_mathml instanceof Array) {
          result = result + symbol_mathml[2];
        }

        result = result + "</mrow>";
        return result;

      }

    });

});
