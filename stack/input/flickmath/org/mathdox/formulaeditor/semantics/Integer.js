$package("org/mathdox/formulaeditor/semantics");

$identify("org/mathdox/formulaeditor/semantics/Integer.js");

$require("org/mathdox/formulaeditor/semantics/Node.js");
$require("org/mathdox/formulaeditor/presentation/Symbol.js");
$require("org/mathdox/formulaeditor/presentation/Row.js");

$main(function(){

  /**
  * Representation of an integer in the semantic tree.
  */
  org.mathdox.formulaeditor.semantics.Integer =
    $extend(org.mathdox.formulaeditor.semantics.Node, {

      /**
       * The integer value.
       */
      value : null,

      /**
       * Initializes a semantic tree node to represent an integer with the
       * specified value.
       */
      initialize : function(value) {
	// check for allowed types
	// 1: integer
	if (value === undefined || value === null) {
          this.value = 0;
	} else {
	  if (! ((typeof value == "number") || (typeof value == "object" && value.rule == "bigint"))) {
            console.log("MFE WARNING: integer object created with unknown type "+(typeof value));
	  }
	  this.value = value;
	}

      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getPresentation
       */
      getPresentation : function(context) {
        var presentation = org.mathdox.formulaeditor.presentation;

        var string = this.value.toString();
        var symbols = [];

        for (var i=0; i<string.length; i++) {
          symbols[i] = new presentation.Symbol(string.charAt(i));
        }

        var result = new presentation.Row();
        result.initialize.apply(result, symbols);
        return result;

      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getOpenMath
       */
      getOpenMath : function() {
        return "<OMI>" + this.getValueAsString() + "</OMI>";
      },

      /**
       * See org.mathdox.formulaeditor.semantics.Node.getMathML
       */
      getMathML : function() {
        return "<mn>" + this.getValueAsString() + "</mn>";
      },

      getValueAsString: function() {
	if (typeof this.value == "number") {
	  return this.value.toString();
	} else if (typeof this.value == "string") { 
          console.log("MFE WARNING: found integer with unexpected type: string");
	  return this.value;
	} else if (typeof this.value == "object" && this.value.rule == "bigint") {
	  // bigint encoding, value.value is the string representation
	  return this.value.value;
	} else { // unknown type
          console.log("MFE ERROR: integer of unknown type");
	  return this.value.toString();
	}
      }

    });

});
