# Entry of numbers in STACK

This document discusses entry of numbers into STACK.  This discussion will also be relevant to other online assessment systems and technology more generally.  When we type in a string of symbols into a computer there is a context and assumptions which arise from that context.  For example is `e` the base of the natural logarithms or is `e` to be interpreted as part of a floating point number, e.g. `6.6263e−34`? There are two related issues.

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

There is also a discussion of [number styles in the NUMBAS system](https://docs.numbas.org.uk/en/latest/number-notation.html).

## Design of syntax for decimal separators

The only opportunity for ambiguity arises in the use of a comma '`,`' in an expression, which could be a decimal separator or a list separator.

1. We assume we are only parsing a single expression.  Hence expressions are _not_ separated by a semicolon '`;`'.  However, a single expression might contain more than one number, e.g. coefficients in a polynomial, members of a set/list, and arguments to a function (e.g. \(\max(a, b)\) or \(\max(a; b)\)).
2. The symbol '`.`' must be a decimal separator.
3. The symbol '`;`' must separate items in a list, set, function, etc.

It is reasonable to expect students to be consistent in their use of the '`,`' within a particular expression.  This follows the advice in ISO 80000-1:2022.
Therefore students cannot use all of '`.`', '`,`' and '`;`' in a single expression without inconsistency.

In the current STACK design student input of `1,23` would be invalid and generate an error: "A comma in your expression appears in a strange way."  Many users will wish to retain this behaviour.  Therefore although this expression is not ambiguous, in a British context it does not follow common usage and could well indicate a misunderstanding about how to type in sets, lists, coordinates functions etc.
A similar problem occurs in a continental context where `1;23` contains an unencapsulated list separation. This expression is not ambiguous and a similar error message such as "A semicolon in your expression appears in a strange way." would be similarly helpful.

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

## Practical implementation in STACK

Students do not type in expression termination symbols `;`, freeing up this symbol for use in students' input for separating list items, including lists which are arguments to functions.

Internally, we retain strict Maxima syntax.  _Teachers must use strict Maxima syntax, so that numbers are typed in base 10, and the decimal point (`.`) must be used by teachers as the decimal separator._   This simplifies the problem considerably, as input parsing is only required for students' answers.

1. Currently the only option available is "strict".
2. TO-DO: Allow student input of `matrix([3,1415;2,71]).matrix([1];[2])` this example should be parsed, but currently the system rejects this.

## Practial implementation in other software

1. NUMBAS also uses the semicolon to separate list items, as discussed in [NUMBAS issue 889](https://github.com/numbas/Numbas/issues/889)

### Comments from Peter Mayer:

In the school context, it is almost exclusively common in German-speaking countries to use the '`,`' as a decimal separator. In contrast, I have never encountered a '`.`' as a decimal separator. The '`.`' is usually used as a thousands separator: \(1002 = 1.002\) and can also be used in conjunction with a comma: \(1002,54 = 1.002,54\). A '`;`' is usually used in schools only in geometry as an alternative to \(A(4|5)\): \(A(4;5)\).

As a suggestion, I would like to point out the behavior of Microsoft Excel.  In the German version, the '`,`' and '`.`' are also used according to my comment above. In formulas, the individual arguments are separated by a '`;`'.  If you switch to the English version, however, thousands are separated by '`,`' and decimal numbers by '`.`' as well as the parameters of functions by '`,`'. Maybe it is advisable to approach this behavior, because there could be synnergies. 

***As a silver bullet, however, I would suggest the following:***

In Moodle there is a method (unformat_float lib/moodlelib.php:8880) that converts local numbers entered by the user into a standard-compliant number, which can then also be stored in the DB. Depending on the viewer, this can then be output again in the respective local representation ([format_float; lib/moodlelib.php](https://github.com/moodle/moodle/blob/master/lib/moodlelib.php#L8830)) of the viewer. The advantage would be that thereby no special cases must be considered but, everything can be kept as before. Only the user input and output has to be converted accordingly, and moodle does that itself.

### Comments from Björn Gerß:

I agree with Chris' analyses and Peter's comment.

Weak option:  for my use case, I would like a behavior as described in "Strict continental". I completely understand why the weak options are proposed, but in German-speaking schools only the calculator works in British mode and no one writes it done like that. So the weak option is not needed for school use. This might be different in university use, where writing like in the weak-option is more common.

Where to put the option:  If the option is part of the question, it is possible to mix settings from question to question. I think this is confusing for students. So I would suggest a site wide option.

In my use case, it isn't essential, but we might think of connection the option to the language the student is using. On the last annual meeting, there were many talks about translating question. As a user, I would expect a British behavior when moodle is set to English and a Continental when moodle is set to German. So it could be best to have a sitewide option for a behavior for every installed language. As the most instances of moodle have a clear focus on the type of student, I assume it only rarely the wish to change the setting between different courses.

With the sitewide option, imported material is also no problem anymore

### Comments from Matti Harjula:

A site-level setting is indeed something that would be nice, but for larger multi-locale sites where needs may vary between courses and subjects, we really need to be able to override it at the question and, preferably, course level, think #993. Personally, I would want the following:
 
 1. A site-level setting that allows choosing between traditional CAS syntax (using the separators we have used up to this point) and using locale-specific separators which would match with the active locale that gets chosen by the course or by the student.
 2. That site level setting could then be overridden at lower levels, e.g. course, quiz, question, or even at the input level.
 3. At the input level, the setting would have even more options, e.g., not tying it to the active locale but instead allowing one to override it, maybe even defining a custom separator combination that is not backed by a locale.
 4. When that setting gets overridden and differs from the site level (or course level), we should always provide a note in the user interface describing what the syntax is within this question or even for a given input.

When we eventually get this, and there will be a site-level setting I strongly suggest that the default value for it will be to keep using the current CAS syntax so that no system switches to something unexpected.

And I really want to be able to target this at the input level, even though the use for that would mainly be for teaching cultural syntax differences and would not be used outside those few extra special materials. For that it would be enough to be able to set those options through some magical "extra options" syntax. At the question level, a simple dropdown for selecting between "CAS/locale" should be enough.
