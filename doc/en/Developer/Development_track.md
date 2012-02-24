# Development track

## Milestone 1.  

Have basic CAS functionality working and incorporated into Moodle.  Supply basic data

1. CJS: Refactor connection to the CAS
   1. castext.
   2. answer tests.
   3. unit tests for answer tests which need a CAS connection.
2. MP: Importer for STACK 2 questions.
3. Move STACK_StringUtil to be a static class.  Search project for uses of this.
   (Probably a good idea to do this before the project gets to big!)
4. CJS: Add "healthcheck pages", installation instructions and confirm configuration settings.
   Links from the settings page.
5. TH: Question stub ready for further development.

Gradually change variable and function names to conform to Moodle coding standards.

## Milestone 2.  

Basic skeleton import of STACK 2 questions and storage in the database. Teacher can create a question version and attempt this from the database, with no caching.  

## Milestone 3. 

Reinstate the dynamic cache and make it work with one Moodle behaviour. At this point we should be able to include a STACK question into a moodle quiz, for demonstration and testing purposes.  

## Milestone 4.  

* Editing forms in Moodle to allow creation and editing of questions.
* Import and export of STACK 3 questions in Moodle's format.

## Milestone 5.  

* Finish STACK 2 importer: ensure all fields are imported correctly by the question importer.
* Add reporting functionality.
* Add user documentation.
* Installation, documentation, and reporting.
* Make STACK respect all Moodle behaviours.

---
# Other tasks

These tasks also need to be done, but do not block progress towards getting STACK basically working in moodle.

1. Refactor the way STACK surrounds mathematics with LaTeX 
environments.  Really we need a function 

stack_maths($ex,$format = INLINE/DISPLAY)

which takes the castring $ex, and surrounds it by strings 
depending on whether we want an inline or displayed equation.   
Similar to the translator function... 


## Some miscellaneous things Tim wants to do

* Answer tests should be like inputs. We should return an answer test object, not a controller object.
* $at->get_at_mark() really ought to be $at->matches(), since that is how it is used.
* Finish cleaning up stack_utils.


## Languages

* Add in other languages.   Copy over only those strings which are really needed.  NOTE: the new format of the language strings containing parameters.  In particular, strings {$a[0]} need to be changed to {$a->m0}, etc.

## Maxima

1. Update the list of forbidden keywords....
2. Investigate better ways of connecting to Maxima.
  *  <http://code.google.com/p/remote-maxima/>
  *  <http://www.lon-capa.org/maximaasserver.html>
3. Refactor Maxima code to change from $'s and $$'s to \[ \] and \( and \).
  
## Documentation system

1. 404 error does not add an entry to the log.   
2. What happened to `docMaintenance.php`?  This hasn't been incorporated yet.  We need the ReportWidgets to be included for this to function.  Ben?
3. Update the file, and link this to the documentation system (or just abandon it and use .md!) :

     \stack\www\lib\maxima\stackfun.php

4. Update the file

     \stack\www\lib\maxima\maximafun.php

## Other longer term jobs

---
# History of previous versions of STACK

### Version 3.0 

_Not yet released_.  Target, September 2013.

Major re-engineering of the code by the Open University, The 
University of Birmingham and the University of Helsinki.  
Reporting and documentation added by Ben Holmes. 

This round of development does not plan to introduce major new features, or to make major changes to
the core functionality. An explicit aim is that "old questions will still work".  

Key features

* Integration into the quiz of Moodle 2.3.
* Support for Maxima up to 5.26.0.
* Documentation moved from the wiki to within the code base.
* Move from CVS to GIT.
* Language support added: nl, de.

## Changes in features between STACK 2 and STACK 3.

* What used to be called Interaction elements are now known as Inputs.
* Change in the internal name of one answer test `Equal_Com_ASS` changed to `EqualComASS`.
* Feature "allowed words" dropped from interaction elements. 
* Input "Dropdown" list -> should be automatically imported to "list"

## Future plans 

We have a dedicated page for [future plans](Future_plans).

## Past versions and History 

STACK is a direct development of the CABLE project which ran at the University of Birmingham.
CABLE was a development of the AiM computer aided assessment system.

### Version 2.2 

Released: October 2010 session.

* Enhanced reporting features.
* Enhanced question management features in Moodle.  E.g. [import multiple questions](https://sourceforge.net/tracker/?func=detail&aid=2930512&group_id=119224&atid=683351)
  from AiM/Maple TA at once, assign multiple questions to Moodle question banks.
* Slider interaction elements.


### Version 2.1 

Developed by Chris Sangwin and Simon Hammond at the University of Birmingham.
Released: Easter 2010 session.

Key features

* [Precision](../Authoring/Answer_tests#Precision) answer test added to allow significant to be checked.
* [Form](../Authoring/Answer_tests#Form) answer test added to test if an expression is in completed square form.
* List interaction element expanded to include checkboxes.  See [List](../Authoring/Inputs#List).
* Move to Maxima's `random()` function, rather then generate our own pseudo random numbers
* [Conditionals in CASText](https://sourceforge.net/tracker/?func=detail&aid=2888054&group_id=119224&atid=683351)
* Support for Maxima 5.20.1
* New option added: OptWorkedSol.  This allows the teacher to decide whether the tick box to request the worked solution is available.
* Sample resources included as part of the [FETLAR](http://www.fetlar.bham.ac.uk) project.


### Version 2.0 

Released, September 2007.  Developed by Jonathan Hart and Chris Sangwin at the University of Birmingham. 

Key features 

* Display of mathematics now taken care of by [JSMath](../Components/JSMath). 
* Integrated into Moodle. 
* Variety of interaction elements. 
* Multi-part questions.
* Cache. 
* Item tests. 

### Version 1.0 

Released, 2005.  Developed by Chris Sangwin at the University of Birmingham.


