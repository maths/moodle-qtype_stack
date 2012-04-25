# Future plans

The following features are in approximate priority order.  Please contact the developers if you have suggestions for other features.

## Minor features to add immediately on completion of 3.0 ##

* Introduce a variable so the maxima code "knows the attempt number".
* Ensure the conditionals in CASText adaptive blocks code makes it into version 3. (Aalto)
* Add a feature to display the Maxima form of an internal variable - useful for helping students.  (Aalto: contact Matti).
* Move sample_questions into the language directory.

### Existing bugs to confirm ###

* What is the status of inputs in equations, particularly with MathJax.  Confirm what happens when these are locked once a worked solution has been called for.

### Consolidation of mailing lists, forum, wiki etc. ###

We need to consolidate all of these things.

* Remove old wiki.
* Upate forum.
* Announcements on Moodle mailing lists.
* Re-install demonstration servers.

## Features to add - round two! ##

* Expand the CASText format to enable us to embed the _value_ of a variable, not just the displayed form.  This will be needed for various other things.
* Geogebra input.
* Enable individual questions to load Maxima libraries.
* It would be very useful to have finer control over the validation feedback. For example, if we have a polynomial with answer boxes for the coefficients, then we should be able to echo back "Your last answer was..." with the whole polynomial, not just the numbers.
* Better options for automatically generated plots.  (Aalto use of tikzpicture?)
* Make the mark and penalty fields accept arbitrary maxima statements.
* Decimal separator, both input and output.

## Features to add - possible self contained projects ##

* [Question blocks](../Authoring/Question_blocks.md)
* Read other file formats into STACK.  In particular
  * AIM
  * WebWork
  * MapleTA
* Possible Maxima packages:
 * Better support for rational expressions, in particular really firm up the PartFrac and SingleFrac functions with better support.
 * Package for scientific [units](../Authoring/Units.md), and a science answer test
 * Support for inequalities.  This includes real intervals and sets of real numbers.

* Localisation of questions.  In particular to enable a single STACK question to carry around different versions for each of the text-based fields, including feedback.  Each field might have a new "tab".  The obvious use is for different languages, but it might also be use for different notations, e.g. engineering, physics, maths.

## More speculative long terms plans ##

* Steps in the working. In particular, variable numbers of input boxes.
* Hints.
