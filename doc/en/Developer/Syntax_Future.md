# Future plans for syntax of answers and STACK.

__Problem we are trying to solve__: students don't use the correct syntax.  We want, as much as possible, to spot common problems and either (1) tell students what they have done wrong, or (2) just condone the problem and fix it quietly.

__Goal__: be as helpful to students as possible.

Separate "spotting problems/setting a context" from "Fixing/condoning problems".

0. Teachers are expected to always use correct Maxima syntax, and use inert functions such as `stackunits`.
1. Use the "Insert stars" option to set the pattern/context for checking expresions.
2. Use the "Strict syntax" option to decide if we can fix/condone a problem for students.
 * "yes" we don't condone any problem, but we do report back error messages such as "you seem to be missing *s", etc.
 * "no" we just interpret and use the expression.  It may still be "invalid".

E.g. if we have "Insert stars" looking for implied multiplication, and "Strict syntax" is "no" then a student's answer `2x` will be changed to `2*x` and used without comment.

Some patterns must always be wrong.  Always check for these, even with teachers (who also need helpful error messages!).

* `)(` must be missing a star.
* Some implied multiplication patterns such as `2x`, or space patterns `2 x`.
* Atoms which appear as both function names and variable names.  E.g.`x(x+1)`.

The issues are as follows:

1. Implied multiplication.
 * `2x` must be `2*x`.
 * `x2` could be the atom `x2`, `x*2`, or `x[2]`.  Only if we look for "implied multiplication" do we suggest `x2->x*2`.
2. Use of spaces as implied multiplication.
 * `2 x` must be `2*x`.
3. Assume single variable character names. In many situations we know that a student will only have single character variable names.  Identify variables in the student's answer made up of more than one character then replace these with the product of the letters.
 * We identify _variables_ and known functions are now protected, e.g. `sin(ax)` will not end up as `s*i*n*(a*x)` but as `sin(a*x)`.
 * Greek letters are always protected. (This might not work, e.g. `pi` might be `p*i`, but this is why we have validation!)
 * `in` is a keyword in Maxima, but we treat this as a special case `in->i*n`.
 * `asin(x)` is a problem, because `asin` is a function.  This is not transformed to `a*sin(x)`.
4. Prevent the use of any user-defined functions? E.g. `f(x+1)` is function application in default Maxima, but `f` us not defined.

## "Insert stars" option.

The value of this option is an integer.  Sum the numbers for each issue you want to include.  I.e. written in binary, each bit acts as a flag for each of the issues above.  This is extensible, as we identify new issues.

* `0`: Expect strict Maxima syntax. I.e. don't insert *s for implied multiplication, or in place of any spaces.
* `1`: Insert `*`s for implied multiplication.  If any patterns are identified as needing `*`s then they will automatically be inserted into the expression quietly.
* `2`: Allow spaces for implied multiplication.
* `4`: Assume single character variable names.
* `8`: Assume single character variable nemes, and assume no Maxima constants.  E.g. `pi` is now `p*i` and not \(\pi\).
* `16`: Assume no user-defined functions. (If `1` is set the insert stars...)

Therefore, a value of `3` both inserts `*`s for implied multiplication and allow spaces to imply multiplication, but we do not assume single character variable names, and we do not prevent user-defined functions.

## Other proposals.

1. Use the "forbid floats" option to deal with patterns like scientific notation.  If "forbid floats" is true we add patterns so that `3E2` or `3e2` (which Maxima interprets as `300.0`) are added to any for insert *s.

## Future developments

Support other issues in context, at the parsing stage.

* Base M numbers
* Allow entry of unicode?
* Spot order of precedence problems:  `x = 1 or 2` (normally used to mean `x=1 or x=2`).
* Bespoke grammers, e.g. add in support for intervals.  `x in (-2,-1] or [1,2)`.
* Student use of control structures, e.g. `if then...`?

## Legacy mapping.

For old questions (those with `stackversion < 2019041600`) we automatically perform the following mapping for the value of the insert stars option.

`old -> new` (Old phrase).

* `0 -> 0` (Don't insert stars)
* `1 -> 1` (Insert stars for implied multiplication only)
* `2 -> 4` (Insert stars assuming single-character variable names)
* `3 -> 2` (Insert stars for spaces only)
* `4 -> 3` (Insert stars for implied multiplication and for spaces)
* `5 -> 7` (Insert stars assuming single-character variables, implied and for spaces)

