# Entry of numbers in STACK

___This is a proposal for discussion as of 10 July 2023.___

This document discusses entry of numbers into STACK.  This discussion will also be relevant to other online assessment systems and technology more generally.  When we type in a string of symbols into a computer there is a context and assumptions which arise from that context.  For example is `e` the base of the natural logarithms or is `e` to be interpreted as part of a floating point number, e.g. `6.6263−34`? There are two related issues.

* Which symbol to use as the decimal separator, '`,`' or '`.`'?
* Support for number bases (other than decimal).

We start by designing the input mechanism for decimal separators.

## Standards

[ISO 80000-1:2022](https://www.iso.org/standard/76921.html) _Quantities and units — Part 1: General_ gives "general information and definitions concerning quantities, systems of quantities, units, quantity and unit symbols, and coherent unit systems, especially the International System of Quantities (ISQ)."  Section 7.2.2 covers the decimal sign: "The decimal sign is either a comma or a point on the line. The same decimal sign should be used consistently within a document."

Further, in section _7.2 Numbers_:  "To facilitate the reading of numbers with many digits, these may be separated into groups of three, counting from the decimal sign towards the left and the right. No group shall contain more than three digits. Where such separation into groups of three is used, the groups shall be separated by a small space and not by a point or a comma or by any other means."

It goes on to say "The General Conference on Weights and Measures (Fr: Conférence Générale des Poids et Mesures) at
its meeting in 2003 passed unanimously the following resolution: _"The decimal marker shall be either a point on the line or a comma on the line."_  In practice, the choice between these alternatives depends on customary use in the language concerned".

The older [ISO 6093:1985](https://www.iso.org/standard/12285.html) _Specification for Representation of numerical values in character strings for information interchange_
also allows for either a comma or point for the decimal separator.

These standards to not provide advice on how to separate items, e.g. in lists, and so how to interpret expressions such as `{1,2}`. There are two options for interpreting `{1,2}`:

1. A set containing the single number six fifths, \(\frac{6}{5}\).
2. A set containing the two integers one and two.

## Design of syntax for decimal separators

The only opportunity for ambiguity arises in the use of a comma '`,`' in an expression, which could be a decimal separator or a list separator.

1. We assume we are only parsing a single expression.  Hence expressions are _not_ separated by a semicolon '`;`'.  However, a single expression might contain more than one number, e.g. coefficients in a polynomial, members of a set/list, and arguments to a function (e.g. \(\max(a, b)\) or \(\max(a; b)\)).
2. The symbol '`.`' must be a decimal separator.
3. The symbol '`;`' must separate items in a list, set, function, etc.

It is reasonable to expect students to be consistent in their use of the '`,`' within a particular expression.  This follows the advice in ISO 80000-1:2022.
Therefore students cannot use all of '`.`', '`,`' and '`;`' in a single expression without inconsistency.

In the current STACK design student input of `1,23` would be invalid and generate an error: "A comma in your expression appears in a strange way."  Many users will wish to retain this behaviour.  Therefore although this expression is not ambiguous, in a British context it does not follow common usage and could well indicate a misunderstanding about how to type in sets, lists, coordinates functions etc.
A similar problem occurs in a continental context where `1;23` contains an unencapsulated list separation. This expression is not ambiguous and a similar error message such as "A in your expression appears in a strange way." would be similarly helpful.

Examples.

| Typed expression | '`.`' | '`,`' | '`;`' | Ambiguity? | Comments                                                    |
|------------------|-------|-------|-------|------------|-------------------------------------------------------------|
|`123`             |  .    |  .    |  .    | No         |                                                             |
|`1.23`            |  Y    |  .    |  .    | No         | Single decimal number.                                      |
|`1,23`            |  .    |  Y    |  .    | No/error   | Single decimal number or an unencapsulated list.            |
|`1.2+2,3*x`       |  Y    |  Y    |  .    | Error      | Inconsistent decimal separators used.                       |
|`1;23`            |  .    |  .    |  Y    | Error      | This expression contains an unencapsulated list.            |
|`{123}`           |  .    |  .    |  .    | No         | Set of one integer.                                         |
|`{1.23}`          |  Y    |  .    |  .    | No         | Set of one float.                                           |
|`{1,23}`          |  .    |  Y    |  .    | Yes        | Option needed to interpret the role of '`,`'.               |
|`{1.2,3}`         |  Y    |  Y    |  .    | No         | '`.`' used, '`;`' not used, so '`,`' must separate lists.   |
|`{1;23}`          |  .    |  .    |  Y    | No         | Set of two integers.                                        |
|`{1.2;3}`         |  Y    |  .    |  Y    | No         | '`.`' used, and no '`,`'                                    |
|`{1,2;3}`         |  .    |  Y    |  Y    | No         | '`;`' used, no '`.`', so '`,`' is a decimal separator.      |
|`{1,2;3;4.1}`     |  Y    |  Y    |  Y    | Error      | Inconsistent decimal/list separators used.                  |


## Proposal for options in STACK

We need a new question-level option in STACK for decimal separators.  This option distinguishes between "British" '`.`' and "contiential" '`,`' decimal separators.  Output, e.g. LaTeX generated by Maxima, will respect this useage throughout the question. Hence the need for a question-level option.

1. Strict continential.  Reject any use of '`.`' as a decimal separator.  I.e. reject any use of '`.`'.
2. Strict British.  Reject any use of '`,`' as decimal separator.  Warn for unencapsulated lists with '`,`' and reject any use of '`;`'.  (Current STACK behaviour)
3. Weak continential.  When ambiguity arises, assume '`,`' should be a decimal separator.
4. Weak British.  When ambiguity arises, assume '`,`' should be a list separator.

Wherever '`;`' is permitted (all but Strict British) we should warn for unencapsulated lists with '`;`' as we currently do for '`,`' in STACK.

We have always worked on the basis of being as forgiving as possible, and accepting expessions where no ambiguity arises. E.g. `2x` must always mean `2*x` under any reasonable interpretation, and if we choose to reject it in STACK we do so with explicit validation feedback explaining where to put the `*` symbol. Therefore, we should try to do the same when supporting input sytax for decimal seprators.  Forgiving inference rules

1. If a student's expression contains neither dots '`.`' or semicolon '`;`' then a question-level (continential/British) option is used to determine the meaning of the '`,`'.
2. If the student's expression contains a '`;`' then any commas are interpreted as decimal separators.
3. If the student's expression contains a '`.`' then any commas are interpreted as list separators.
4. If a student's expression contains both dots '`.`' and semicolon '`;`' then a student cannot use '`,`' without ambiguity.  A question-level option is needed to determine the probable meaning.

To be decided.

* To what extent do continental teachers accept use of '`.`' as a decimal separator?  If _never_ then we probably don't need the "weak" options proposed above.
* Can teachers set this option at the question level or should it respect a site-wide option?  (It's a shame Moodle can't set plugin options at the course level, otherwise we'd return to the cascading options which were available in STACK 1.0 some twenty years ago...)
* How can we _easily_ allow teachers to set/override this option for imported materials?

## Practical implementation in STACK

Students do not type in expression termination symbols `;`, freeing up this symbol for use in students' input for separating list items.

Internally, we retain strict Maxima syntax.  _Teachers must use strict Maxima syntax, so that numbers are typed in base 10, and the decimal point (`.`) must be used by teachers as the decimal separator._   This simplifies the problem considerably, as input parsing is only required for students' answers.

1. Mechanism for Maxima to output LaTeX. (Done - but more work needed on testing and question-wide options)
2. Mechanism to output expressions as they should be typed.  E.g. "The teacher's answer is \(???\) which can be typed as `???`".
3. Input parsing mechanism for _students' answers only_.

(Note to self, strings may contain harmless punctuation characters which should not be changed...)

