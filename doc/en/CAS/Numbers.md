# Numbers in STACK

Numerical answer tests are documented in a page dedicated to [numerical answer tests](../Authoring/Answer_tests_numerical.md).

## Precise Constants ##

In Maxima the special constants are defined to be

    %i, %e, %pi

etc.   STACK also uses single letters, e.g.

    e: %e
    pi: %pi

Optionally, depending on the question settings, you have

    i: %i
    j: %i

Sometimes you need to use \(e\), or other constants, as an abstract symbol not a number.  The Maxima solution is to use the `kill()` command, but for security reasons users of STACK are not permitted to use this function. Instead use `stack_reset_vars(true)` in the question variables.  This resets all the special constants defined by STACK so the symbols can be redefined in an individual STACK question.  (On Maxima 5.42.1 (and possibly others) `stack_reset_vars(true)` also resets `ordergreat`, so if you need to use `stack_reset_vars(true)` it must be the first command the question variables.  Since this has been fixed in Maxima 5.44.0, it was probably a bug in Maxima.)

## Modular arithmetic ##

The function `recursemod(ex, n)` recurses over an expression tree, and applies the function `mod(?, n)` to any numbers as identified by `numberp`.  This works on any expression, whereas `polymod` only applies to polynomials.

## Internal representation of numbers ##

Maxima has two data types to represent numbers: integers and floats.  Rational numbers are expressed as a division of two integers not with a dedicated data type, and surds with fractional powers or the `sqrt` function.
The option [Surd for Square Root](../Authoring/Options.md#surd) enables the question author to alter the way surds are displayed in STACK.

Similarly, complex numbers are not represented as a single object, but as a sum of real and imaginary parts, or via the exponential function.
The input and display of complex numbers is difficult, since differences exist between mathematics, physics and engineering about which symbols to use.
The option [sqrt(-1)](../Authoring/Options.md#sqrt_minus_one) is set in each question to sort out meaning and display.

## Floating point numbers ## {#Floats}

* To convert to a float use Maxima's `float(ex)` command.
* To convert a float to an exact representation use `rat(x)` to rationalise the decimal.

The variable \(e\) has been defined as `e:exp(1)`.  This now potentially conflicts with scientific notation `2e3` which means `2*10^3`.    

If you expect students to use scientific notation for numbers, e.g. `3e4` (which means \(3\times 10^{4}\) ), then you may want to use the [option for strict syntax](../Authoring/Inputs.md#Strict_Syntax).  

## Maxima and floats with trailing zeros ##

For its internal representation, Maxima always truncates trailing zeros from a floating point number.  For example, the Maxima expression `0.01000` will be converted internally to `0.01`.  Actually this is a byproduct of the process of converting a decimal input to an internal binary float, and back again.  Similarly, when a number is a "float" datatype, Maxima always prints at least one decimal digit to indicate the number is a float.  For example, the floating point representation of the number ten is \(10.0\).  This does _not_ indicate significant figures, rather it indicates data type.  In situations where the number of significant figures is crucial this is problematic.

Display of numbers in STACK is controlled with LaTeX, and the underlying LISP provides flexible ways to represent numbers.

Note, that apart from the units input, all other input types truncate the display of unnecessary trailing zeros in floating point numbers, loosing information about significant figures.  So, when the student's answer is a floating point number, trailing zeros will not be displayed.  If you want to specifically test for significant figures, use the [units input type](../Authoring/Units.md), with the teacher's answer having no units.  The units input type should display the same number of significant figures as typed in by the student.  

## Display of numbers with LaTeX ##

The display of numbers is controlled by Maxima's `texnumformat` command, which STACK modifies.

Stack provides two variables to control the display of integers and floats respectively.  The default values are

    stackintfmt:"~d";
    stackfltfmt:"~a";

These two variables control the output format of integers (identified by the predicate `integerp`) and floats (identified by the predicate `floatnump`) respectively.  These variables persist, so you need to define their values each time you expect them to change.

These variables must be assigned a string following Maxima's `printf` format.

These variables can be defined in the question variables, for global effect.  They can also be defined inside a Maxima block to control the display on the fly, and for individual expressions.  For example, consider the following CASText.

    The decimal number {@n:73@} is written in base \(2\) as {@(stackintfmt:"~2r",n)@}, in base \(7\) as {@(stackintfmt:"~7r",n)@}, in scientific notation as {@(stackintfmt:"~e",n)@} and in rhetoric as {@(stackintfmt:"~r",n)@}.

The result should be "The decimal number \(73\) is written in base \(2\) as \(1001001\), in base \(7\) as \(133\), in scientific notation as \(7.3E+1\) and in rhetoric as \(seventy-three\)."

To force all floating point numbers to scientific notation use

    stackfltfmt:"~e";

To force all floating point numbers to decimal floating point numbers use

    stackfltfmt:"~f";

You can also force all integers to be displayed as floating point decimals or in scientific notation using `stackintfmt` and the appropriate template.  This function calls the LISP `format` function, which is complex and more example are available [online](http://www.gigamonkeys.com/book/a-few-format-recipes.html) elsewhere.

| Template    | Input       |  TeX Output      |  Description/notes
| ----------- | ----------- | ---------------- | ----------------------------------------------------------------------------------------------
| `"~,4f"`    | `0.12349`   | \(0.1235\)       |  Output four decimal places: floating point.
|             | `0.12345`   | \(0.1234\)       |  Note the rounding.
|             | `0.12`      | \(0.1200\)       |  
| `"~,5e"`    | `100.34`    | \(1.00340e+2\)   |  Output five decimal places: scientific notation.
| `"~:d"`     | `10000000`  | \(10,000,000\)   |  Separate decimal groups of three digits with commas.
| `~r`        | `9`         | \(\mbox{nine}\)  |  Rhetoric.
| `~:r`       | `9`         | \(\mbox{ninth}\) |  Ordinal rhetoric.
| `~7r`       | `9`         | \(12\)           |  Base 7.
| `~@r`       | `9`         | \(IX\)           |  Roman numerals.
| `~:@r`      | `9`         | \(VIIII\)        |  Old style Roman numerals.

There are many other options within the LISP format command. Please note with the rhetoric and Roman numerals that the numbers will be in LaTeX mathematics environments.

Maxima has a separate system for controlling the number of decimal digits used in calculations and when printing the _value_ of computed results.  Trailing zeros will not be printed with the value.  This is controlled by Maxima's `fpprec` and `fpprintprec` variables.  The default for STACK is

    fpprec:20,          /* Work with 20 digits. */
    fpprintprec:12,     /* Print only 12 digits. */

## Notes about numerical rounding ##

There are two ways to round numbers ending in a digit \(5\).  

* Always round up, so that \(0.5\rightarrow 1\), \(1.5 \rightarrow 2\), \(2.5 \rightarrow 3\) etc.
* Another common system is to use ``Bankers' Rounding". Bankers Rounding is an algorithm for rounding quantities to integers, in which numbers which are equidistant from the two nearest integers are rounded to the nearest even integer. \(0.5\rightarrow 0\), \(1.5 \rightarrow 2\), \(2.5 \rightarrow 2\) etc.  The supposed advantage to bankers rounding is that in the limit it is unbiased, and so produces better results with some statistical processes that involve rounding.

Maxima's `round(ex)` command rounds multiples of 1/2 to the nearest even integer, i.e. Maxima implements Bankers' Rounding.

STACK has defined the function `significantfigures(x,n)` to conform to convention of rounding up.

## STACK numerical functions and predicates ##

The following commands which are relevant to manipulation of numbers are defined by STACK.

| Command                         | Description
| ------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| `significantfigures(x,n)`       | Truncate \(x\) to \(n\) significant figures (does perform rounding).
| `decimalplaces(x,n)`            | Truncate \(x\) to \(n\) decimal places  (does perform rounding).
| `commonfaclist(l)`              | Returns the highest common factors of a list of numbers.
| `list_expression_numbers(ex)`   | Create a list with all parts for which `numberp(ex)=true`.
| `coeff_list(ex,v)`              | This function takes an expression \(ex\) and returns a list of coefficients of \(v\).
| `coeff_list_nz(ex,v)`           | This function takes an expression \(ex\) and returns a list of nonzero coefficients of \(v\).
| `numabsolutep(sa,ta,tol)`       | Is \(sa\) within \(tol\) of \(ta\)? I.e. \( |sa-ta|<tol \)  
| `numrelativep(sa,ta,tol)`       | Is \(sa\) within \(tol\times ta\) of \(ta\)? I.e. \( |sa-ta|<tol\times ta \).  

The following commands generate displayed forms of numbers.  These will not be manipulated further automatically, so you will need to use these at the last moment, e.g. only when generating the teacher's answer etc.

| Command                         | Description
| ------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| `dispdp(x,n)`                   | Truncate \(x\) to \(n\) decimal places and display with trailing digits.  Note, this always prints as a float (or integer), and not in scientific notation.
| `dispsf(x,n)`                   | Truncate \(x\) to \(n\) significant figures and display with trailing digits.  Note, this always prints as a float, and not in scientific notation.
| `scientific_notation(x,n)`      | Write \(x\) in the form \(m10^e\).   Only works reliably with `simp:false` (e.g. try 9000).  The optional second argument applies `displaysci(m,n)` to the mantissa to control the display of trailing zeros.
| `displaydp(x,n)`                | An inert internal function to record that \(x\) should be displayed to \(n\) decimal places with trailing digits.  This function does no rounding.
| `displaysci(x,n,expo)`          | An inert internal function to record that \(x\) should be displayed to \(n\) decimal places with trailing digits, in scientific notation.  E.g. \(x\times 10^{expo}\).


| Function                  | Predicate
| ------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| `simp_numberp(ex)`          | Fixes `numberp(ex)` for `simp:false`.
| `real_numberp(ex)`          | Determines if \(ex\) is a real number.  This includes surds and symbolic numbers such as \(\pi\).
| `lowesttermsp(ex)`          | Is the rational expression in its lowest terms?
| `anyfloatex(ex)`            | Decides if any floats are in the expression.
| `scientific_notationp(ex)` | Determines if \(ex\) is written in the form \(a10^n\) where \(a\) is an integer or float, and \(n\) is an integer.

## See also

[Maxima reference topics](index.md#reference)
