# Scientific Units

It is quite common in science subjects to need to accept an answer which has _units_, for example using \(m/s\).

Currently STACK only support SI units.   See International Bureau of Weights and Measures (2006), (The International System of Units (SI)) [PDF](http://www.bipm.org/utils/common/pdf/si_brochure_8_en.pdf) (8th ed.), ISBN 92-822-2213-6.

## Maxima packages for the support of scientific units  ##

In Maxima there are a number of separate packages which enable a user to manipulate physical units.

1. `load(unit);` the code is in `...\share\contrib\unit\unit.mac`
2. `load(units);` the code is in `...\share\physics\units.mac`
3. `load(ezunits);` the code is in `...\share\ezunits\ezunits.mac`

**WE DO NOT USE THESE PACKAGES** as they are too slow to load and have a variety of side effects.  Instead we have a lightweight package of our own.

By default in Maxima, letters such as `k` are unbound variables.  If you would like to give these prefix values \(10^3\) so that `km` literally means `10^3*m` then you will need to add the following to the question variables field.

    stack_unit_si_declare(true);

The units input and units answer test automatically execute this command.  More details are given below.

Internally numbers can be separated from units using the following inert Maxima function.

    stackunits(num, units)

where `num` is the part interpreted to be the numerical portion, and `units` is the portion considered to be the units.  For example, in the expression `10*m/s` the internal value from this input will be `stackunits(10,m/s)`.  Essentially the function `stackunits` is inert, but does subtly modify the display.  However, having STACK split the student's answer this way is more reliable than teachers trying to find the "numerical part" themselves on a question by question basis.  It essentially creates a single object representing a dimensional numerical quantity.  If you are using the units answer tests then you need not worry about these internals.

## Examples  ##

### Example 1  ###

Let us assume that the correct answer is `12.1*m/s^2`.

1. Students type this value into STACK exactly as it is above. Note the multiplication sign between
   the number and units. There are options to condone a missing star, or to accept a space.
2. In entry, the numerical part is bound to the units part with multiplication.  Using multiplication in this way is ambiguous.  To create, and to disambiguate, a dimensional numerical quantity from a number multiplied by units (a subtle distinction at best) STACK has a mostly inert function `stackunits(12.1,m/s^2)`.  Students do not need to use this, but teachers can use it in question variables etc.
3. STACK converts the student's answer to SI base units only.
   This function also handles the number coefficients automatically (e.g. `1*km = 1000*m` etc.).
4. STACK has answer tests which compare dimensional numbers. These tests use (share code with) one of the existing numerical answer tests, such as `NumSigFigs`.


## Input type ## {#Input_type}

Stack provides an input type to enable teachers to support students in entering answers with scientific units.

This input type is built closely on the algebraic input type with the following differences.

1. The input type will check both the teacher's answer and the student's answer for units.  The input will require the student's answer to have units if and only if the teacher's answer also has units.  This normally forces the student to use units.  Also, students sometimes add units to dimensionless quantities (such as pH) and this input type will also enable a teacher to reject such input as invalid when the teacher does not use units.
2. This input type *always accepts floating-point numbers*, regardless of the option set on the edit form.  The input type should display the same number of significant figures as typed in by the student.  Note that all other input types truncate the display of unnecessary trailing zeros in floating point numbers, loosing information about significant figures.  If you want to specifically test for significant figures, use this input type, with the teacher's answer having no units.
3. The student must type a number of some kind.  Entering units on their own will be invalid.  If you want to ask a student for units, then use the algebraic input type.  Units on their own are a not valid expression for this input.
4. If the teacher shows the validation, "with variable list" this will be displayed as "the units found in your answer are"...
5. The student is permitted to use variable names in this input type.
6. The "insert stars" option is unchanged.  You may or may not want your students to type a `*` or space between the numbers and units for implied multiplication.
7. You may want the single letter variable names options here.  Note that since `km` literally means `k*m=1000*m` this is not a problem with most units.
8. The input type checks for units in a case sensitive way.  If there is more than one option then STACK suggests a list.  E.g. if the student types `mhz` then STACK suggests `MHz` or `mHz`.
9. You can require numerical accuracy at validation by using the `mindp`, `maxdp`, `minsf` and `maxsf` extra options, as documented in the [numerical input](Numerical_input.md).

There are surprisingly few ambiguities in the units set up, but there will be some that the developers have missed (correctly dealing with ambiguous input is by definition an impossible problem!).  Please contact us with suggestions for improvements.

Note, the input does not currently support a situation where you want to accept as valid a purely numerical quantity and then use the PRT to deduct marks for failing to use units, rather than rejecting it as invalid.

### Extra Options ###

The extra options to the input should be a comma separated list of tags.  This input type makes use of the additional options in two ways:

1. Units can be displayed using inline fractions \(m/s\) or negative powers \(m\,s^{-1}\).  Add `negpow` to the Extra Options field to use negative powers.

## Answer tests  ##

STACK provides a number of answer tests for dealing with units.  These are designed to accept an answer which is a dimensional numerical quantity, that is a floating-point number with units such as `12.3*m/s`.  This will not work with sets, lists, matrices, equations, etc.

The answer tests *require* the teacher's answer (second argument to the function) to have units.  If the teacher does not specify units then the test will fail.  This decision is to help question authors write robust questions e.g. just specifying a number would be problematic.  The input will accept an answer as valid if and only if the teacher's answer has units, so you should know in advance if you have units.  If you want to compare numbers (i.e. no units), just use the numerical test.

The units answer tests will happily accept a `stackunits` expression.  Otherwise, the answer test splits up both arguments into this form first.

There are three decisions to be made:

1. Does the written precision matter?  I.e. should the student use certain significant figures?  If so, should we take a strict interpretation of significant figures (\(100\) has 1 sig fig, \(1.00e2\) has 3) or not (\(100\) has somewhere between 1 and 3 sig figs)?  See the `NumSigFigs` and `SigFigsStrict` answer tests.
2. How does numerical precision matter?  A number might be written using significant figures, but is it the right number?  See the `NumSigFigs`, `NumRelative` and `NumAbsolute` answer tests.
3. Do we convert to compatible units, or require strict units which match those given by the teacher exactly?

Essentially, the teacher has to make three decisions.  These could always be done in a potential response tree with three nodes, but this is a common task.  For legacy reasons, some of the answer tests (e.g. `NumSigFigs`, `UnitsSigFigs`) combine answering two or more of these questions in one answer test.

For scientific units (Q3.) there are two families of answer tests.

1. `Units[...]` gives feedback if the student has the wrong units, but number is equivalent on conversion,
2. `UnitsStrict[...]` expects the student's answer to use exactly the units which appear in the teacher's answer.  There is no conversion here.  However, answer notes will record whether the conversion would have worked.

The two issues related to the numerical part are tested with one of the [numerical answer tests](Answer_tests_numerical.md) which are documented elsewhere. Units answer tests share code with these functions.  Use the appropriate options for the chosen test.


__Notes__

1. All variables are considered to be units.
2. The numerical part is compared using the one of the three numerical answer tests.  Each *requires* various options, e.g. the number of significant figures, or the numerical accuracy.  These answer tests use identical options to the numerical tests.
3. The units system accepts both `l` and `L` for litres, and the display respects the way they are typed in.
4. Only abbreviations are accepted, not full names.  I.e. students may not use `meter`, instead they must use `m`.
5. Currently there is no localisation (i.e. language support) for unit names/spellings.
6. The letter `u` is the prefix for micro, and is displayed as \(\mu\) when the student validates.
7. The string `xi` is the Greek letter \(\xi\).  If you assume single variable letter names this might clash with `x*i` which is a relatively common pattern.

## Conversion to base units and numerical accuracy  ##

This only applies to the "non-strict" versions of the tests.  If the units in the student's answer do not match those of the teacher, then both the student's and teacher's answer is converted to base scientific units and the numerical test applied again.  Note, the student's answer is *not* converted to the units used by the teacher.

For example, in order to make a numerical comparison between `1.1*Mg/10^6` and `1.2*kN*ns/(mm*Hz)` both expressions are converted to base units of `kg`. The numerical test is then applied.

For the `NumRelative` test the option gives the required percentage tolerance within which a student's answer should be.  Literally we test the following `|sa-ta| < |ta*tol|`.  Here `sa` and `ta` are the numerical value of the student's and teacher's answer respectively.  The same `tol` is used both when the units match and *once they have been converted to base units*.

Similarly, for `NumAbsolute` the option is an absolute difference, (expressed in units).  Literally we test
`|sa-ta| < |tol|`. Here `sa` and `ta` are the numerical value of the student's and teacher's answer respectively *once they have been converted to base units*.  If `tol` has no units, then STACK will use the units supplied by the teacher.

If the teacher uses units in the option then the option units must be identical to the units in the teacher's answer. 

## Dealing with units in Maxima functions, e.g. question variables and PRTs  ##

STACK uses an inert function to represent dimensional numerical quantities.

    stackunits(num, units)

In particular, if we just represented scientific units as a product there would be no way to distinguish between `stackunits(0, m)` and `stackunits(0, s)`.  As a product both would evaluate to `0`, which would appear dimensionless.  A teacher is still likely to want to make comments on units when the numerical part is zero.

The function `stack_unit_si_declare(true)` declares variables as units.  (Note the argument to this function is not used.)  For example, this changes the TeX output of `m` to Roman \(\mathrm{m}\) and not the normal \(m\).  (That units are displayed in Roman is lost to most students!).  Note that the symbols are *only* declared to be units by using `stack_unit_si_declare(true)` first somewhere else in the question, or feedback variables.

* `unitsp(ex)` is a predicate which decides if STACK considers the expression to represent a dimensional numerical quantity `stackunits`.  
* `listofnonunits(ex)` lists all variables in the expression `ex` considered not to be units however they appear.  Use of this function auto-loads `stack_unit_si_declare(true)`.
* `listofunits(ex)` lists all variables in the expression `ex` considered to be units however they appear. Use of this function auto-loads `stack_unit_si_declare(true)`.
*  If you do not declare `stack_unit_si_declare(true)` in the question variables you may need to do so in the feedback text itself.

The function `stackunits_make(ex)` takes the expression `ex` and, if this is a product of numbers and units, it returns an inert function `stackunits` with arguments `stackunits(numbers, symbols)`.  Note, symbols will include a mix of variables, and symbols which are considered to be units. Use of this function autoloads `stack_unit_si_declare(true)`.

The function `stackunits_to_product(ex)` turns a `stackunits` object into a product of number and units.

It might be helpful in the feedback variables field to separate units from numerical parts prior to building your own potential response tree.  However, do not do the following.

    n:float(100+rand(300)/10);
    u:m/s
    ta:stackunits_make(n*u)

Instead just call `stackunits` directly

    n:float(100+rand(300)/10);
    u:m/s
    ta:stackunits(n,u)

If you regularly find yourself building a particular tree to test for some property please contact the developers who will consider adding this functionality to the core.  

The functions

    stack_units_units(ex);
    stack_units_nums(ex);

try to split the expression into units and numbers, and the return the units and numbers found.  If there are no numbers, `stack_units_nums(ex)` returns `NULLNUM`. If there are no numbers, `stack_units_units(ex)` returns `NULLUNITS`.  These are special tags, but note they are displayed by LaTeX as empty strings.  (You could also use `first(args(ans1))` or `second(args(ans1))` respectively to access the numerical and units parts.)

The function `stack_units_split` is deprecated.  DO NOT USE.

## Custom units ##

The teacher may want to use their own units. For example, the core `unit` package in Maxima does not include `mm` (millimetre), it is defined there as a word `millimetre`.  This is one reason for creating our own custom units package.

___For advanced users and developers only___

Currently there is no way to create custom sets of units.  This feature may be added in the future.  The following may be helpful for now, but this does not fully work and the mechanism will change in the future when proper support is added for custom units.

Add the following to the question variables and feedback variables.

    stack_unit_si_declare(true);
    declare(diamonds, units);
    texput(diamonds, "\\diamond");

The symbol `diamonds` will then be treated as units in code such as `unitsp(ex)` and displayed with the TeX \(\diamond\) symbol.

You will need to put `diamonds` in the allow words of the input in the question.

## Tips for dealing with units in STACK ##

CAS variables are tricky.

*  When creating worked solutions etc. try `12.3m*s^(-1)` not `12.3m/s`.  The display is better.  The answer test accepts either as equivalent.
*  Which units are supported?  It is probably best to look at the code.  This is contained in `qtype_stack\stack\cas\casstring_units_class.php`.  Comments and additions are welcome.
