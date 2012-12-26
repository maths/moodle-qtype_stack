# Future plans

The following features are in approximate priority order.  How to report bugs and make suggestions is described on the [community](../About/Community.md) page.

## Minor features to add immediately on completion of 3.0 ##

* [DONE] Add back all questions from the diagnostic quiz project as further examples.
* Improve the way questions are deployed.
 1. [DONE] Deploy many versions at once.
 2. Auto deploy.  E.g. if the first variable in the question variables is a single a:rand(n), then loop a=0..(n-1).
 3. Remove many versions at once.
* Ensure the conditionals in CASText adaptive blocks code makes it into version 3. (Aalto)
* Facility to import test-cases in-bulk as CSV (or something). Likewise export.
* Improve editing UI for test-cases https://github.com/maths/moodle-qtype_stack/issues/15
* Add back remaining input types
 1. dragmath
 2. NUMBAS
 3. Dropdown/MCQ input type. 
* Refactor answer tests.
 1. They should be like inputs. We should return an answer test object, not a controller object.
 2. at->get_at_mark() really ought to be at->matches(), since that is how it is used.
* When validating the editing form, also evaluate the Maxima code in the PRTs, using the teacher's model answers.
* A way to set defaults for many of the options on the question edit form. There are two ways we could do it. We could make it a system-wide setting, controlled by the admin, just like admins can set defaults for all the quiz settings. Alternatively, we could use user_preferences, so the next time you create a STACK question, it uses the same settings as the previous STACK qusetion you created.
* Introduce a variable so the maxima code "knows the attempt number". [Note to self: check how this changes reporting]

## Features to add - round two! ##

### STACK custom reports ###

* [DONE] Split up the answer notes to report back for each PRT separately.
* Really ensure "attempts" list those with meaningful histories.  I.e. if possible filter out navigations to and from the page etc.
* Introduce "validation notes".
* Rename the "Reviewing" to "Reporting" in the docs.

### Rethink how STACK embed maths ###

[DONE] The goal will be to work more closely with how Moodle handles maths, so that
the option to use the standard Moodle tex filter, or a custom filter like the
OU's is feasible.

We probably need a function for use by CAS text, like
    stack_maths(ex,format = INLINE/DISPLAY)
which takes the castring $ex, and surrounds it by strings depending on whether
we want an inline or displayed equation. However, there is also the issue that
question authors embed maths in the question text using their own conventions
(e.g. $ for inline maths, that Moodle does not recognise by default.) Do we try
to fix question text too?

This may also require changes to the Maxima code, to change from dollars to `\[ and \]` and `\( and \)`.

### Input elements in equations ###

It is very useful to be able to embed input elements in equations, and this was
working in STACK 2.0. However is it possible with MathJax or other Moodle maths
filters?

### Improve the editing form ###

* Feature in the edit forms to expand/fold away each PRT and input.
* A button to remove a given PRT or input, without having to guess that the way to do it is to delete the placeholders from the question text.
* A button to remove a new PRT or input, without having to guess that the way to do it is to add the placeholders to the question text.
* A way to rename a given PRT or input. (The only way to do this might be a separate bit of UI, perhaps linked to from the questiontestrun.php script).
* A button to save the current definition and continue editing.

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

* [Question blocks](../Authoring/Question_blocks.md)
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
* Hints.
