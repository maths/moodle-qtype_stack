$package("org.mathdox.formulaeditor.parsing.expression");

$identify("org/mathdox/formulaeditor/parsing/expression/ExpressionContextParser.js");

$require("org/mathdox/parsing/Parser.js");
$require("org/mathdox/formulaeditor/Options.js");
$require("org/mathdox/formulaeditor/parsing/expression/ExpressionParser.js");

$main(function() {

    var ParsingParser = org.mathdox.parsing.Parser;
    var functions = new Array();

    var cachedContext = null;

    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser =
      $extend(Object, {
        getParser : function(context) {
          var i;

          if (context === null || context === undefined) {
	    context = this.getContext();
	  }

	  if (context.parser === undefined) {

            var parser = ParsingParser;

            for (i=0;i<functions.length;i++) {
              parser = $extend(parser, functions[i](context));
            }

	    context.parser = parser;
	  }

	  return context.parser;
        }
      });

    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.addFunction = function(fun) {
      functions.push(fun);
    };
    
    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.clearCache = function() {
      cachedContext = null;
    }

    org.mathdox.formulaeditor.parsing.expression.ExpressionContextParser.getContext = function() {
      if (cachedContext === null) {

        var Options = new org.mathdox.formulaeditor.Options();

        /* set context options based on options */
	/* XXX move this to options.getCachedContext() */

	cachedContext = Options.getExpressionParsingContext();
      }

      return cachedContext;
    };

    /* initialize with ExpressionParser rules */
    var ExpressionParser = new org.mathdox.formulaeditor.parsing.expression.ExpressionParser();

    functions.push(ExpressionParser.getRules);
}

);
