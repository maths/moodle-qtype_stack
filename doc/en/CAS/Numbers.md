# Numbers in STACK #

## Constants ##

In maxima the special constants are defined to be

    %i, %e, %pi

etc.   STACK also uses single letters, e.g.

    i: %i
    j: %i
    e: %e
    pi: %pi

Sometimes you need to use \(e\) as an abstract symbol not a number.
The Maxima solution is to use the `kill()` command, but for security reasons users of STACK are not permitted to use this function.
Instead use `stack_reset_vars(true)` in the question variables.

This resets all the special constants defined by STACK so the symbols can be redefined in a STACK question.

The following commands which are relevant to manipulation of numbers are defined by STACK.

| Command                         | Description
| ------------------------------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| `decimalplaces(x,n)`            | Truncate \(x\) to \(n\) decimal places
| `significantfigures(x,n)`       | Truncate \(x\) to \(n\) significant figures
| `scientific_notation(x)`        | Write \(x\) in the form \(m10^e\).   Only works with `simp:false`.
| `commonfaclist(l)`              | Returns the hcf of a list of numbers
| `list_expression_numbers(ex)`   | Create a list with all parts for which `numberp(ex)=true`.
| `coeff_list(ex,v)`              | This function takes an expression ex and returns a list of coefficients of v
| `coeff_list_nz(ex,v)`           | This function takes an expression ex and returns a list of nonzero coefficients of v

## STACK numerical predicates ##

| Function                  | Predicate
| ------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
| `simp_numberp(ex)`        | Fixes `numberp(ex)` for `simp:false`.
| `real_numberp(ex)`        | Determines if \(ex\) is a real number.  This includes surds and symbolic numbers such as \(\pi\).
| `lowesttermsp(ex)`        | Is the rational expression in its lowest terms?
| `anyfloatex(ex)`          | Decides if any floats are in the expression.


## Rational numbers ##

## Floating point numbers ##

## Surds ##

The option [Surd for Square Root](../Authoring/Options.md#surd) enables the question author to alter the way surds are displayed in STACK.


## Complex numbers ##

The input and display of complex numbers is difficult, since differences exist between mathematics, physics and engineering about which symbols to use.
The option [sqrt(-1)](../Authoring/Options.md#sqrt_minus_one) is set in each question to sort out meaning and display.

## See also

[Maxima reference topics](index.md#reference)
