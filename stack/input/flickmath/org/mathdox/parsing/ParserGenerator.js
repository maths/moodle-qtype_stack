$package("org.mathdox.parsing");

$identify("org/mathdox/parsing/ParserGenerator.js");

$main(function(){

  /**
   * A recursive descent parser in continuation passing style. This parser allows
   * both forward (left-to-right) and backward (right-to-left) parsing.
   */
  org.mathdox.parsing.ParserGenerator = $extend(Object, {

    alternation : function() {

      var alternatives = arguments;

      return function(context, index, result, continuation) {
        for (var i=0; i<alternatives.length; i++) {
          alternatives[i](context, index, result, continuation);
        }
      };

    },

    begin : function(context, index, result, continuation) {

      if (index == 0) {
        this.empty(context, index, result, continuation);
      }

    },

    concatenation : function() {

      var elements = Array.prototype.slice.call(arguments);
      var generator = this;

      if (elements.length > 0) {

        var concatenation = arguments.callee;

        return function(context, index, result, continuation) {

          var tail = elements.slice();
          var head = context.backward ? tail.pop() : tail.shift();

          head(
            context,
            index,
            result,
            function(newindex, newresult) {
              var tailparser = concatenation.apply(generator, tail);
              tailparser(context, newindex, newresult, continuation);
            }
          );

        };

      }
      else {

        return generator.empty;

      }

    },

    empty : function(context, index, result, continuation) {

      continuation(index, result);

    },

    end : function(context, index, result, continuation) {

      if (index == context.input.length) {
        this.empty(context, index, result, continuation);
      }

    },

    literal : function(string) {

      return function(context, index, result, continuation) {

        var start = context.backward ? index - string.length : index;
        var end   = context.backward ? index : index + string.length;

        if (context.input.substring(start, end) == string) {
          if (context.backward) {
            continuation(start, [string].concat(result));
          }
          else {
            continuation(end, result.concat([string]));
          }
        }

      };

    },

    never : function(context, index, result, continuation) {

      //skip

    },

    range : function(lower, upper) {

      return function(context, index, result, continuation) {

        var character = context.input.charAt(context.backward ? index-1 : index);

        if (lower <= character && character <= upper) {
          if (context.backward) {
            continuation(index - 1, [character].concat(result));
          }
          else {
            continuation(index + 1, result.concat([character]));
          }
        }

      };

    },

    repetition : function(operand) {
      var pG = this;

      return pG.alternation(
        pG.empty,
        pG.repetitionplus(operand)
      );

    },

    repetitionplus : function(operand) {

      var pG = this;

      return function(context, index, result, continuation) {
	// note: repetition is the same forwards and backwards
	// alternative 1: only 1 
        operand(context, index, result, continuation); 

	/* alternative 2: 1 followed by repetitionplus */
        operand(
          context,
          index,
          result,
          function(newindex, newresult) {
            pG.repetitionplus(operand)(context, newindex, newresult, continuation);
          }
        );
      };
    },

    rule : function(name) {

      return function(context, index, result, continuation) {

        context.parser[name](context, index, result, continuation);

      };

    },

    transform : function(operand, transform) {

      return function(context, index, result, continuation) {

        operand(
          context,
          index,
          result,
          function(newindex, newresult) {
	    var sliced;
            if (context.backward) {
              sliced = newresult.slice(0, newresult.length - result.length);
              continuation(newindex,[transform(sliced)].concat(result));
            }
            else {
              sliced = newresult.slice(result.length);
              continuation(newindex, result.concat([transform(sliced)]));
            }
          }
        );

      };

    }
  });


  /**
   * Add string representation to the generated parser parts. This is needed for
   * the memoization algorithm below.
   */
  org.mathdox.parsing.ParserGenerator =
    $extend(org.mathdox.parsing.ParserGenerator, {

      initialize : function() {

        this.begin.asString = "^" ;
        this.empty.asString = "()";
        this.end.asString   = "$" ;
        this.never.asString = "0" ;

      },

      alternation : function() {

        var parent = arguments.callee.parent;
        var result = parent.alternation.apply(this, arguments);

        result.asString = "(";

        for (var i=0; i<arguments.length; i++) {

          if (i>0) {
            result.asString += "|";
          }

          result.asString += arguments[i].asString;

        }

        result.asString += ")";

        return result;

      },

      parentAlternation: function(newrule, oldrule, obj) {
        alert(obj);
        alert(obj[oldrule]);
        var result = function() {
          this.alternation(newrule, obj[oldrule]).apply(this, arguments);
        }
        result.asString = "(" + obj[oldrule].asString + "|" + 
	  newrule.asString + ")";

        return result;
      },

      concatenation : function() {

        var parent = arguments.callee.parent;
        var result = parent.concatenation.apply(this, arguments);

        result.asString = "(";

        for (var i=0; i<arguments.length; i++) {

          if (i>0) {
            result.asString += " ";
          }

          result.asString += arguments[i].asString;

        }

        result.asString += ")";

        return result;

      },

      literal : function(string) {

        var parent = arguments.callee.parent;
        var result = parent.literal.apply(this, arguments);
        result.asString = "\"" + string + "\"";
        return result;

      },

      range : function(lower, upper) {

        var parent = arguments.callee.parent;
        var result = parent.range.apply(this, arguments);
        result.asString = "[" + lower + ".." + upper + "]";
        return result;

      },

      repetition : function(operand) {

        var parent = arguments.callee.parent;
        var result = parent.repetition.apply(this, arguments);
        result.asString = operand.asString + "*";
        return result;

      },

      repetitionplus : function(operand) {

        var parent = arguments.callee.parent;
        var result = parent.repetitionplus.apply(this, arguments);
        result.asString = operand.asString + "+";
        return result;

      },

      rule : function(name) {

        var parent = arguments.callee.parent;
        var result = parent.rule.apply(this, arguments);
        result.asString = name;
        return result;

      },

      transform : function(operand, transform) {

        var parent = arguments.callee.parent;
        var result = parent.transform.apply(this, arguments);
        result.asString = "{" + operand.asString + "}";
        return result;

      }

  });


  /**
   * Add memoization to support left-recursion (and right recursion when parsing
   * backwards), and improve the time complexity from exponential to polynomial.
   */
  org.mathdox.parsing.ParserGenerator =
    $extend(org.mathdox.parsing.ParserGenerator, {

      memoize : function(f) {

        var result = function(context, index, result, continuation) {

          var key = f.asString + "," + index;

          var entry = context.cache[key];
          if (!entry) {

            entry = context.cache[key] = { results : [], continuations : [] };
            entry.continuations.push(continuation);
            f(context, index, result, function(index, result) {
              entry.results.push({index : index, value : result});
              for (var i=0; i<entry.continuations.length; i++) {
                entry.continuations[i](index, result);
              }
            });

          }
          else {

            entry.continuations.push(continuation);
            for (var i=0; i<entry.results.length; i++) {
              var result = entry.results[i];
              continuation(result.index, result.value);
            }

          }

        };

        result.asString = f.asString;

        return result;

      },

      rule : function(name) {

        var parent = arguments.callee.parent;
        return this.memoize(parent.rule.apply(this, arguments));

      },

      repetitionplus : function(name) {

        var parent = arguments.callee.parent;
        return this.memoize(parent.repetitionplus.apply(this,arguments));

      }

    });

});
