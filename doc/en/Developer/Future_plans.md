# Future plans

The following features are in approximate priority order.  How to report bugs and make suggestions is described on the [community](../About/Community.md) page.


## Features to add - round two! ##

### Input elements in equations ###

It is very useful to be able to embed input elements in equations, and this was
working in STACK 2.0. However is it possible with MathJax or other Moodle maths
filters?

### Improve the editing form ###

* A button to remove a given PRT or input, without having to guess that the way to do it is to delete the placeholders from the question text.
* A button to remove a new PRT or input, without having to guess that the way to do it is to add the placeholders to the question text.
* A way to rename a given PRT or input. (The only way to do this might be a separate bit of UI, perhaps linked to from the questiontestrun.php script).
* Feature in the edit forms to expand/fold away each PRT and input. This would be a Moodle core change. See https://tracker.moodle.org/browse/MDL-30637.
* A button to save the current definition and continue editing. This would be a Moodle core change. See https://tracker.moodle.org/browse/MDL-33653.

### Other ideas ###

* Expand the CASText format to enable us to embed the _value_ of a variable in CASText, not just the displayed form.  This will be needed for various other things.
* Implement "CommaError" checking for CAS strings.
* Geogebra input.
* Enable individual questions to load Maxima libraries.
* It would be very useful to have finer control over the validation feedback. For example, if we have a polynomial with answer boxes for the coefficients, then we should be able to echo back "Your last answer was..." with the whole polynomial, not just the numbers.
* Better options for automatically generated plots.  (Aalto use of tikzpicture?)
* Make the mark and penalty fields accept arbitrary maxima statements.
* Decimal separator, both input and output.
* Multi-lingual support for questions.  See [languages](Languages.md).  Also known as localisation of questions.  In particular to enable a single STACK question to carry around different versions for each of the text-based fields, including feedback.  Each field might have a new "tab".  The obvious use is for different languages, but it might also be use for different notations, e.g. engineering, physics, maths.


## Features that might be attempted in the future - possible self contained projects ##

* Investigate how a whole PRT might make only one CAS call.
* Ensure the conditionals in CASText adaptive blocks code makes it into version 3. (Aalto) See [question blocks](../Authoring/Question_blocks.md)
* Read other file formats into STACK.  In particular
  * AIM
  * WebWork
  * MapleTA
* Possible Maxima packages:
 * Better support for rational expressions, in particular really firm up the PartFrac and SingleFrac functions with better support.
 * Package for scientific [units](../Authoring/Units.md), and a science answer test
 * Support for inequalities.  This includes real intervals and sets of real numbers.

## More speculative long terms plans ##

* Steps in the working. In particular, variable numbers of input boxes.
