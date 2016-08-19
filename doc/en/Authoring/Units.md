# Units

It is quite common in science subjects to need to accept an answer which has _units_, for example using \(m/s\).

Currently STACK only support SI units.   See International Bureau of Weights and Measures (2006), (The International System of Units (SI)) [PDF](http://www.bipm.org/utils/common/pdf/si_brochure_8_en.pdf) (8th ed.), ISBN 92-822-2213-6.

## Maxima packages for the support of scientific units  ##

Note that in Maxima are a number of separate packages which enable a user to manipulate physical units.

1. `load(unit);` the code is in `...\share\contrib\unit\unit.mac`
2. `load(units);` the code is in `...\share\physics\units.mac`
3. `load(ezunits);` the code is in `...\share\ezunits\ezunits.mac`

**WE DO NOT USE THESE PACKAGES** as they are too slow to load.  Instead we have a lightweight package of our own.

By default in maxima, letters such as `k` are unbound variables.  If you would like to give these prefix values \(10^3\) so that `km` literally means `10^3*m` then you will need to add the following to the question variables field.

    stack_unit_si_declare(true);

The units input, and units answertest automatically execute this command.  More details are given below.

## Examples  ##

### Example 1  ###

Let us assume that the correct answer is `12.1*m/s^2`.

1. This value is inserted to STACK exactly as it is above. Note the multiplication sign between
   the number and units. Thus only one answer field is needed. We think this is the best solution (see below).
2. The teacher may want to use their own units. For example, the unit package in Maxima does not
   include `mm` (millimetre), it is defined there as a word `millimetre`.
3. STACK converts the student's answer to SI base units only.
   This function also handles the number coefficients automatically (e.g. `1*km = 1000*m` etc.).
4. STACK separtes the number from the units.
5. Finally STACK compares this number to the respective model answer. In this comparison it uses `NumSigFigs`.

## Input type ##

Stack provides an input type to enable teachers to support students in entering answers with scientific units.

IMPORTANT: the internals of this input type actually changes the student's input.  If this input type is used then the value available to a potential response tree is of the form

    stackunits(num, units)

where `num` is the part interpreted to be the numerical portion, and `units` is the portion considered to be the units.  For example, if a student answers `10*m/s` then the internal value from this input will be `stackunits(10,m/s)`.  Essentially the function `stackunits` is inert, but does subtly modify the display.  However, having STACK split the student's answer this way is more reliable than teachers trying to find the "numerical part" themselves on a question by question basis.  If you are using the units answer tests then you need not worry about these internals.  The units answer tests will happily acccept a `stackunits` expression. If you use other answer tests you can access the numerical and units parts with `first(args(ans1))` or `second(args(ans1))` respectively.

If you would like to replace `stackunits` with multiplication automatically in the potential response trees use the add `mul` to the Extra Options field.

This input type is built closely on the algebraic input type with the following differences.

1. The input type checks for units in a case sensitive way.  If there is more than one option then STACK suggests a list.  E.g. if the student types `mhz` then STACK suggests `MHz` or `mHz`.
2. The input type will check both the teacher's answer and the student's answer for units.  The input will require the student's answer to have units if and only if the teacher's answer also has units.  This normally forces the student to use units.  But, students sometimes add units to dimensionless quantities (such as pH) and this input type will also enable a teacher to reject such input as invalid when the teacher does not use units.
3. This input type *always accepts floating point numbers*, regardless of the option set on the edit form.
4. The student must type a number of some kind.  Entering units on their own will be invalid.  Note, if you want to ask a student for units, then use the algebraic input type.  Units on their own are a valid expression.
5. If the teacher shows the validation, "with variable list" this will be displayed as "the units found in your answer are"...
6. The student is permitted to use variable names in this input type.
7. The "insert stars" option is unchanged.  You may or may not want your students to type a `*` between the numbers and units for implied multiplication.  
8. You may want the single letter variable names options here, which is why this option has not been changed for this input type.

### Extra Options ###

The extra options should be a comma separated list of tags.  This input type makes use of the additional options in two ways:

1. Units can be displayed using inline fractions \(m/s\) or negative powers \(m\,s^{-1}\).  Add `negpow` to the Extra Options field to use negative powers.
2. This input type modifies the student's answer to use `stackunits`. If you would like to replace `stackunits` with multiplication automatically in the potential response trees use the add `mul` to the Extra Options field.

## Answer tests  ##

Six units answer tests are provided.  These are designed to accept a single answer with units such as `12.3*m/s`.  This will not work with sets, lists, matrices, equations etc.  Both the teacher and student must answer in this form.

Each answer test splits up the answer into two parts.
  * The numerical part, which is tested with one of the three numerical tests ATNumSigFigs, ATNumRelative or ATNumAbsolute.  Use the appropriate options for this test.
  * The units.  All non-numerical variable names are considered to be units.  Hence, you cannot use other variables with this test.

This answer test establishes if the student has

* correct units, wrong number,
* wrong units, but number is equivalent on conversion,
* wrong class of units, i.e. Imperial not metric is a different problem from using \(m\) vs \(km\).
* dimensional problems

There are two families of answer tests.
1. `Units[...]` gives feedback if the student has the wrong units, but number is equivalent on conversion,
2. `UnitsStrict[...]` expects the student's answer to use exactly the units which appear in the teacher's answer.  There is no conversion here.  However, answernotes will record whether the conversion would have worked.

__Notes__

1. The student may not include any variables in their answer.  All variables are considered to be units.
2. The numerical part is compared using the one of the three numerical answer tests.  Each *requires* various options, e.g. the number of significant figures, or the numerical accuracy.  These answer tests uses identical options to the numerical tests.
3. The units system accepts both `l` and `L` for litres, and the display respects the way they are typed in.
4. Currently there is no localisation (i.e. language support) for unit names/spellings.
5. The letter `u` is the prefix for micro, and is displayed as \(\mu\) when the student validates.

## Conversion to base units and numerical accuracy  ##

This only applys to the "non-strict" versions of the tests.  If the units in the student's answer do not match those of the teacher, then both the student's and teacher's answer is converted to base scientific units and the numerical test applied again.  Note, the student's answer is *not* converted to the units used by the teacher.

For example, in order to make a numerical comparison between `1.1*Mg/10^6` and `1.2*kN*ns/(mm*Hz)` both expressions are converted to base units of `kg`. The numerical test is then applied.

For the `NumRelative` test the option gives the required percentage tollarance within which a student's answer should be.  Literally we test the following `|sa-ta| < |ta*tol|`.  Here `sa` and `ta` are the numerical value of the student's and teacher's answer respectively.  The same `tol` is used both when the units match and *once they have been converted to base units*.

Similarly, for `NumAbsolute` the option is an absolute difference.  Literally we test
`|sa-ta| < |tol|`. Here `sa` and `ta` are the numerical value of the student's and teacher's answer respectively *once they have been converted to base units*.  Note, where the units are compatible, the same `tol` is used before and after converion to base units.  For this reason, the test `UnitsAbsolute` is likely to give strange behaviour for answers where the units are compatible.  *Suggestions from those teaching science, with examples, of improved behaviour here are very welcome!*

## Dealing with units in Maxima functions, e.g. question variables and PRTs  ##

The function `stack_unit_si_declare(true)` declares symbols as units as far as STACK is concerned.  (Note the argument to this function is not used.)  For example, this changes the TeX output of `m` to Roman \(\mathrm{m}\) and not the normal \(m\).  (That units are displayed in Roman is lost to most students!).  Note that the symbols are *only* declared to be units by using `stack_unit_si_declare(true)` first somewhere else in the question, or feedback variables.

* `unitsp(ex)` is a predicate which decides if STACK considers the expression to represent a scientific unit.  
* `listofnonunits(ex)` lists all variables in the expression `ex` considered not to be units however they appear.  Use of this function autoloads `stack_unit_si_declare(true)`.
* `listofunits(ex)` lists all variables in the expression `ex` considered to be units however they appear. Use of this function autoloads `stack_unit_si_declare(true)`.
Also, you will need to use `stack_unit_si_declare(true)` in the feedback text itself.

The function `stackunits_make(ex)` takes the expression `ex` and, if this is a product of numbers and units it returns an inert function `stackunits` with arguments `stackunits(numbers, symbols)`.  This might be helpful in the feedback variables field to separate units from numerical parts prior to building your own potential response tree.  If you regularly find yourself building a particular tree to test for some property please contact the developers who will consider adding this functionality to the core.  Note, sybmbols will include a mix of variables, and symbols which are considered to be units. Use of this function autoloads `stack_unit_si_declare(true)`.

The functions

    stack_units_units(ex);
    stack_units_nums(ex);

try to split the expression into units and numbers, and the return the units and numbers found.  If there are no numbers, `stack_units_nums(ex)` returns `NULNUMS`. If there are no numbers, `stack_units_units(ex)` returns `NULUNITS`.  These are special tags, but note they are displayed by LaTeX as empty strings.

The function `stack_units_split` is deprecated.  DO NOT USE.

## Custom units ##

___For advanced users and developers only___

Currently there is no way to create custom sets of units.  This feature may be added in the future.  The following may be helpful for now, but this does not fully work and the mechanism will change in the future when proper support is added for custom units.

Add the following to the question variables and feedback variables.

    stack_unit_si_declare(true)
    declare(diamonds, units)
    texput(diamonds, "\\diamond")

The symbol `diamonds` will then be treated as units in code such as `unitsp(ex)` and displayed with the TeX \(\diamond\) symbol.

You will need to put `diamonds` in the allow words of the input in the question.  However, the input validation code is independent of the question variables, and hence the student's answer will not be displayed using the TeX \(\diamond\) symbol.  If we add better support for custom units in the future, this may change.  If your units are something like `lb` and you are happy with italic fonts, this might be fine for now.

Note, the feedback created within potential response trees will not respect the above code.

## Tips for dealing with units in STACK ##

CAS vaiables are tricky.

*  When creating worked solutions etc. try `12.3m*s^(-1)` not `12.3m/s`.  The display is better.  The answer test accepts either as equivalent.
*  Which units are supported?  It is probably best to look at the code.  This is contained in `qtype_stack\stack\cas\casstring_units_class.php`.  Comments and additions are welcome.
