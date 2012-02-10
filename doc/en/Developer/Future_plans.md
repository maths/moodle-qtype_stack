# Furture plans

The following features are in approximate priority order.  Please contact the developers if you have suggestions for other features.

## Minor features to add immediately on completion of 3.0 ##

* Fix MCQs so that the displayed text is full CASText.  
* Better deploy reatures, including "drop all" and "redeploy".
* Systematic deploying of all versions?
* Reinstate dragmath.
* Support for MathJAX.  This speaks mathematics, so will aid [accessibility](../Students/Accessibility).
* Create "worksheets" interface to new code.
* Ensure the conditionals in CASText adaptive blocks code makes it into version 2.3. (Aalto)
* Add a feature to display the Maxima form of an internal variable - useful for helping students.  (Aalto: contact Matti).
* Re-check Maxima functions for security settings.
* Move sample_questions into the languge directory.

### Existing bugs to confirm ###

* What is the status of interaction elements in equations, particularly with MathJAX.  Confirm what happends when these are locked once a worked solution has been called for.


### Consolidation of mailing lists, forum, wiki etc. ###

We need to consolidate all of these things.

* Remove wiki.
* Upate forum.
* Announcements on Moodle mailing lists.
* Re-install demonstration servers.

## Features to add - round two! ##

* Geogebra interaction element.
* Look at the Numbas "see as you type" javascript interface.  Could we include this as an interaction element?
* Enable individual questions to load Maxima libraries.
* It would be very useful to have finer control over the validation feedback. For example, if we have a polynomial with answer boxes for the coefficients, then we should be able to echo back "Your last answer was..." with the whole polynomial, not just the numbers. 
* Better options for automatically generated plots.  (Aalto use of tikzpicture?)

## Features to add - possible self contained projects ##

* Read WebWork files and set these questions in STACK?  
* Possible Maxima packages:
 * Better support for rational expressions, in particular really firm up the PartFrac and SingleFrac functions with better support.
 * Package for scientific units, and a science answer test
 * Support for inequalities.  This includes real intervals and sets of real numbers.
 
* Localisation of questions.  In particular to enable a single STACK question to carry around different versions for each of the text-based fields, including feedback.  Each field might have a new "tab".  The obvious use is for different languages, but it might also be use for different notations, e.g. engineering, physics, maths.

## More speculative long terms plans ##

* Steps in the working. In particular, variable numbers of input boxes.  
* Hints.
