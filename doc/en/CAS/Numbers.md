# Numbers in STACK #

## Constants ##

In maxima the special constants are defined to be

    %i, %e, %pi

etc.   STACK also uses single letters, e.g.

    e: %e
    pi: %pi

Optinally, depending on the question settings, you have

    i: %i
    j: %i

Sometimes you need to use \(e\) as an abstract symbol not a number.
The Maxima solution is to use the `kill()` command, but for security reasons users of STACK are not permitted to use this function. Instead use `stack_reset_vars(true)` in the question variables.

This resets all the special constants defined by STACK so the symbols can be redefined in a STACK question.

The following commands which are relevant to manipulation of numbers are defined by STACK.

| Command                         | Description
| ------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| `decimalplaces(x,n)`            | Truncate \(x\) to \(n\) decimal places.
| `dispdp(x,n)`                   | Truncate \(x\) to \(n\) decimal places and display with trailing digits.
| `significantfigures(x,n)`       | Truncate \(x\) to \(n\) significant figures.
| `scientific_notation(x)`        | Write \(x\) in the form \(m10^e\).   Only works with `simp:false`.
| `commonfaclist(l)`              | Returns the hcf of a list of numbers.
| `list_expression_numbers(ex)`   | Create a list with all parts for which `numberp(ex)=true`.
| `coeff_list(ex,v)`              | This function takes an expression \(ex\) and returns a list of coefficients of \(v\).
| `coeff_list_nz(ex,v)`           | This function takes an expression \(ex\) and returns a list of nonzero coefficients of \(v\).
| `numabsolutep(sa,ta,tol)`       | Is \(sa\) within \(tol\) of \(ta\)? I.e. \( |sa-ta|<tol \)  
| `numrelativep(sa,ta,tol)`       | Is \(sa\) within \(tol\times ta\) of \(ta\)? I.e. \( |sa-ta|<tol\times ta \).  

## Notes about numerical rounding ##

There are two ways to round numbers ending in a digit \(5\).  
* Always round up, so that \(0.5\rightarrow 1\), \(1.5 \rightarrow 2\), \(2.5 \rightarrow 3\) etc.
* Another common system is to use ``Bankers' Rounding". Bankers Rounding is an algorithm for rounding quantities to integers, in which numbers which are equidistant from the two nearest integers are rounded to the nearest even integer. \(0.5\rightarrow 0\), \(1.5 \rightarrow 2\), \(2.5 \rightarrow 2\) etc.  The supposed advantage to bankers rounding is that in the limit it is unbiased, and so produces better results with some statistical processes that involve rounding.

Maxima's `round(ex)` command rounds multiples of 1/2 to the nearest even integer, i.e. Maxima implements Bankers' Rounding.

STACK has defined the function `significantfigures(x,n)` to conform to convention of rounding up.

## STACK numerical predicates ##

| Function                  | Predicate
| ------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| `simp_numberp(ex)`        | Fixes `numberp(ex)` for `simp:false`.
| `real_numberp(ex)`        | Determines if \(ex\) is a real number.  This includes surds and symbolic numbers such as \(\pi\).
| `lowesttermsp(ex)`        | Is the rational expression in its lowest terms?
| `anyfloatex(ex)`          | Decides if any floats are in the expression.


## Floating point numbers ## {#Floats}

The variable \(e\) has been defined as `e:exp(1)`.  This now potentially conflicts with scientific notation `2e3` which means `2*10^3`.    

If you expect students to use scientific notation for numbers, e.g. `3e4` (which means \(3\times 10^{4}\) ), then you must use the [option for strict syntax](../Authoring/Inputs.md#Strict_Syntax).  Otherwise STACK will try to insert star characters for you and `3e4` will be interprted as `3*e*4`.

## Displaying a float with trailing zeros ##

By default in Maxima all trailing zeros are suppressed.  Therefore, you can't display \(3.00\) for scientific work easily.  To overcome this, STACK provides a function `dispdp(x, n)`.  Here `x` is a number, and `n` is the number of decimal digits to display.  This function does perform rounding, and adds trailing digits to the display.  If you want to do further calculationw with the value don't use this funtion, instead round with `decimalplaces(x,n)` and display only at the last moment.

## Surds ##

The option [Surd for Square Root](../Authoring/Options.md#surd) enables the question author to alter the way surds are displayed in STACK.


## Complex numbers ##

The input and display of complex numbers is difficult, since differences exist between mathematics, physics and engineering about which symbols to use.
The option [sqrt(-1)](../Authoring/Options.md#sqrt_minus_one) is set in each question to sort out meaning and display.

## See also

[Maxima reference topics](index.md#reference)
