# Future plans

The following features are in approximate priority order.  How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Features to add ##

### Inputs ###

* Add back remaining input types
 1. Dragmath (actually, probably use javascript from NUMBAS instead here).
 2. Sliders.
 3. Dropdown/MCQ input type.
* It is very useful to be able to embed input elements in equations, and this was working in STACK 2.0. However is it possible with MathJax or other Moodle maths filters?
* Geogebra input.
* Reasoning by equivalence input type.
* Inputs which enable student to input steps in the working. In particular, variable numbers of input boxes.
* Add a "scratch working" area in which students can record their thinking etc. alongside the final answer.

### Improve the editing form ###

* A button to remove a given PRT or input, without having to guess that the way to do it is to delete the placeholders from the question text.
* A button to add a new PRT or input, without having to guess that the way to do it is to add the placeholders to the question text.
* A button to save the current definition and continue editing. This would be a Moodle core change. See https://tracker.moodle.org/browse/MDL-33653.

### Other ideas ###

* Implement "CommaError" checking for CAS strings.  Make comma an option for the decimal separator.
* Enable individual questions to load Maxima libraries.
* It would be very useful to have finer control over the validation feedback. For example, if we have a polynomial with answer boxes for the coefficients, then we should be able to echo back "Your last answer was..." with the whole polynomial, not just the numbers.
* Better options for automatically generated plots.  (Aalto use of tikzpicture?)  (Draw package?)
* Make the mark and penalty fields accept arbitrary maxima statements.
* Decimal separator, both input and output.
* Multi-lingual support for questions.  See [languages](Languages.md).  Also known as localisation of questions.  In particular to enable a single STACK question to carry around different versions for each of the text-based fields, including feedback.  Each field might have a new "tab".  The obvious use is for different languages, but it might also be use for different notations, e.g. engineering, physics, maths.
* Check CAS/maxima literature on -inf=minf.
* Introduce a variable so the maxima code "knows the attempt number". [Note to self: check how this changes reporting]
* Facility to import test-cases in-bulk as CSV (or something). Likewise export.
* Refactor answer tests.
 1. They should be like inputs. We should return an answer test object, not a controller object.
 2. at->get_at_mark() really ought to be at->matches(), since that is how it is used.

## Features that might be attempted in the future - possible self contained projects ##

* Investigate how a whole PRT might make only one CAS call.
* Provide an alternative way to edit PRTs in a form of computer code, rather than lots of form fields. For example using http://zaach.github.com/jison/ or https://github.com/hafriedlander/php-peg. 
* Read other file formats into STACK.  In particular
  * AIM
  * WebWork
  * MapleTA
* Possible Maxima packages:
 * Better support for rational expressions, in particular really firm up the PartFrac and SingleFrac functions with better support.
 * Package for scientific [units](../Authoring/Units.md), and a science answer test.
 * Support for inequalities.  This includes real intervals and sets of real numbers.
 * Support for the "draw" package.
* Add support for qtype_stack in Moodle's lesson module.
