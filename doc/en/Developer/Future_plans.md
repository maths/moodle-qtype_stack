# Future plans

How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

Note, where the feature is listed as "(done)" means we have prototype code in the testing phase.

## Features to add for STACK 3.6 or later ##

### Units Inputs ###

* Convestion from Celsius to Kelvin?  What units to choose for degrees Celsius which don't conflict with Coulomb?
* Support for United States customary units?
* Add an option to validation to require compatible units with the teacher's answer, not just some units.
* Create a mechanism to distinguish between `m/s` and `m*s^-1`, both at validation and answer test levels.
* Create a mechanism to distinguish between `m/s/s` and `m/s^2`, both at validation and answer test levels.

### Inputs ###

* (underway - in equiv branch) Reasoning by equivalence input type.
* Add support for coordinates, so students can type in (x,y).  This should be converted internally to a list.
* Add new input types
 1. (done - in equiv branch) "scratch working" area in which students can record their thinking etc. alongside the final answer.
 2. Dragmath (actually, probably use javascript from NUMBAS instead here, or the MathDox editor).
 3. Sliders.
 4. Geogebra input.
* It is very useful to be able to embed input elements in equations, and this was working in STACK 2.0. However is it possible with MathJax or other Moodle maths filters?
  This might offer one option:  http://stackoverflow.com/questions/23818478/html-input-field-within-a-mathjax-tex-equation
* In the MCQ input type: Add choose N (correct) from M feature (used at Aalto).
* A new MCQ input type with a "none of these" option which uses Javascript to degrade to an algebraic input.


### Improve the editing form ###

* A button to remove a given PRT or input, without having to guess that the way to do it is to delete the placeholders from the question text.
* A button to add a new PRT or input, without having to guess that the way to do it is to add the placeholders to the question text.
* A button to save the current definition and continue editing. This would be a Moodle core change. See https://tracker.moodle.org/browse/MDL-33653.

### Other ideas ###

* Document ways of using JSXGraph  `http://jsxgraph.org` for better support of graphics.
* Better options for automatically generated plots.  (Aalto use of tikzpicture?)  (Draw package?)
* Implement "Banker's rounding" option which applies over a whole question, and for all answer tests.
* Implement "CommaError" checking for CAS strings.  Make comma an option for the decimal separator.
* Implement "BracketError" option for inputs.  This allows the student's answer to have only those types of parentheses which occur in the teacher's answer.  Types are `(`,`[` and `{`.  So, if a teacher's answer doesn't have any `{` then a student's answer with any `{` or `}` will be invalid.
* Enable individual questions to load Maxima libraries.
* It would be very useful to have finer control over the validation feedback. For example, if we have a polynomial with answer boxes for the coefficients, then we should be able to echo back "Your last answer was..." with the whole polynomial, not just the numbers.
* Make the mark and penalty fields accept arbitrary maxima statements.
* Decimal separator, both input and output.
* Check CAS/maxima literature on -inf=minf.
* Introduce a variable so the maxima code "knows the attempt number". [Note to self: check how this changes reporting].  This is now being done with the "state" code in the abacus branch.
* Facility to import test-cases in-bulk as CSV (or something). Likewise export.
* Refactor answer tests.
 1. They should be like inputs. We should return an answer test object, not a controller object.
 2. at->get_at_mark() really ought to be at->matches(), since that is how it is used.
 3. Use `defstruct` in Maxima for the return objects. (Note to self: `@` is the element access operator).
* Make the PRT Score element CAS text, so that a value calculated in the "Feedback variables" could be included here.
* Refactor the STACK return object as a structure. ` ? defstruct`.  Note that `@` is the element access operator.

## Features that might be attempted in the future - possible self contained projects ##

* Investigate how a whole PRT might make only one CAS call.
* Provide an alternative way to edit PRTs in a form of computer code, rather than lots of form fields. For example using http://zaach.github.com/jison/ or https://github.com/hafriedlander/php-peg. 
* Read other file formats into STACK.  In particular
  * AIM
  * WebWork, including the Open Problem Library:  http://webwork.maa.org/wiki/Open_Problem_Library
  * MapleTA
  * Wiris
* Possible Maxima packages:
 * Better support for rational expressions, in particular really firm up the PartFrac and SingleFrac functions with better support.
 * Support for inequalities.  This includes real intervals and sets of real numbers.
 * Support for the "draw" package.
 * Add an ephemeral form for floating point numbers for better support for the numerical tests.  See below.
* Add support for qtype_stack in Moodle's lesson module.
* Improve the way questions are deployed.
 1. Auto deploy.  E.g. if the first variable in the question variables is a single a:rand(n), then loop a=0..(n-1).
 2. Remove many versions at once.
* When validating the editing form, also evaluate the Maxima code in the PRTs, using the teacher's model answers.
* You cannot use one PRT node to guard the evaluation of another, for example Node 1 check x = 0, and only if that is false, Node 2 do 1 / x. We need to change how PRTs do CAS evaluation.


## Ephemeral forms for numbers.

To implement the decinal places test we need an "ephemiral form" for representing numbers at a syntactic level.   This test probably needs to operate at the PHP level on strings, rather then through Maxima.  

Note that in Maxima the `floor()` function gives `floor(0.1667*10^4)` as `1666` not `1667` as expected.  So use `floor(0.1667*10^4)` instead.

Add in support for `printf` to better format numbers.


## STACK custom reports

Basic reports now work.

* Really ensure "attempts" list those with meaningful histories.  I.e. if possible filter out navigation to and from the page etc.
* Add better maxima support functions for off-line analysis.
 * A fully maxima-based representation of the PRT?

## Features we are unlikly to add to STACK! ##

These are some things we are asked for, but which we are unlikley to add in the near future, often because there are other ways to do this.

* Multi-lingual support for questions.  See [languages](Languages.md).  Also known as localisation of questions.  In particular to enable a single STACK question to carry around different versions for each of the text-based fields, including feedback.  Each field might have a new "tab".  The obvious use is for different languages, but it might also be use for different notations and also for applications which appeal to different disciplines, e.g. engineering, physics, maths.
