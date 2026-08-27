# STACK-MP-Lite

`stack_mp_lite` is a collection JavaScript functions and classes for parsing
STACK-Maxima syntax. It is meant for translating such syntax to something else.

The "lite" here means that this version of the parser does not understand
program-flow (`if`s and `do`-loops), nor does it understand multiple
statements or evaluation flags, mostly things one does not expect to output
to students.

The parser does support automatic insertion of multiplication signs and can
operate with localised separators (decimal and list), but it is not meant for
student input processing.

## An example of custom translation.

We shall produce a solution to a differential equation in Maxima and then
translate it to a JavaScript function that can plotted. Parameters in
the solution will be given special overrides to map them to something else.

```
[[jsxgraph]]
/* The parser is not small so it is not loaded by default. */
import * as stack_mp_lite from '[[cors src="stackmplite.min.js"/]]';
/**
 *  First our Maxima syntax expression: 
 *   "%e^-x*(%k1*sin(sqrt(5)*x)+%k2*cos(sqrt(5)*x))" 
 */
let maxima_syntax = "{#rhs(ode2(diff(f(x),x,2)+2*diff(f(x),x)+6*f(x)=0,f(x),x))#}";

/**
 * The default translation to JS already knows how to deal with 'sin',
 * 'cos', 'sqrt' and '%e'. But what to do with '%k1' and '%k2'? We will
 * first create something to target and then define a translation.
 */
let board = JXG.JSXGraph.initBoard(divid, {boundingbox: [-10, 10, 10, -10], axis: true});

let c1 = board.create('slider', [[-9,-8],[-4,-8], [-10,1,10]], {suffixLabel: 'c_1 = ', point1: {frozen: true},
    point2: {frozen: true}})
let c2 = board.create('slider', [[-9,-9],[-4,-9], [-10,1,10]], {suffixLabel: 'c_2 = ', point1: {frozen: true},
    point2: {frozen: true}})

/* Translated code can ignore 'x' but those two map something else. */
let translation_options = {
	variables : {
		"%k1" : "c1.Value()",
		"%k2" : "c2.Value()",
		"x" : "x"
	}
};

/* Now assuming we are running with '.' as our decimal separator... */
let parsed = stack_mp_lite.parse_decimal_dot(maxima_syntax);

/**
 * Then turn that to JS string like this:
 *  "Math.pow(Math.E,- x) * (c1.Value() * Math.sin(Math.sqrt(5) * x) + c2.Value() * Math.cos(Math.sqrt(5) * x))"
 */
let translated = parsed.toJS(translation_options);

/* Now we have string with JavaScript code, lets turn it to a function. */
let f = (x) => eval(translated);

board.create('functiongraph', [f]);

/**
 * Tech note... 
 * If you do not need to reference things like those sliders and your
 * function is pure you can get more performance by defining: 
 *  let f = new Function("x", "return " + translated); 
 */
[[/jsxgraph]]
```


## More about translation

Note that the parser result will contain four translation functions:

 1. The `toString` will produce a Maxima syntax output matching the original
    with some whitespace changes. You can tune it by giving the function
    an option array. The options currently accepted are:
     `{"list separator": ",", "decimal separator": "."}`
    Both accept any string as the value.

 2. The `translate` function can be made to transform subtrees of the parsed
    result as one wishes. The function takes that same set of options and
    additional dictionaries for `function`, `operator` and `variable` mappings.
    Those mappings may simply map an identifier to another like:
      `"cos" -> "Math.cos"`
    Or they may define a function to do the mapping. Se the source of 
    the `toJS`-function for samples of such mappings.

    The translate function will complain to the console should it see something
    that has no defined translation, you may use this to identify when have
    forgotten some variables or even functions.

    You may also redefine the whole `translate`-function for certain types
    of objects in the parse tree. For example, sets don't really have a default
    translation, but you might provide one:
```
 stack_mp_lite.MPSet.prototype.translate = function(opt) {
   return '{' + this.items.map((x)=>x.translate(opt)).join(',') + '}';
 }; 
```
 3. The `toJS` function takes your options list and fills in various JavaScript
    math-library functions and mappings to constant values before calling
    `translate`.

 4. The `toJessieCode` function takes your options and fills in various 
    JSXGraph math-library functions and mappings to constant values before
    calling `toJS`.