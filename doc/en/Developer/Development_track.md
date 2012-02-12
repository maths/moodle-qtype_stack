# Development track

## Milestone 1. ## 

Have basic CAS functionality working and incorported into Moodle.  Supply basic data

1. CJS: Refactor connection to the CAS
   1. castext.
   2. answer tests.
   3. unit tests for answer tests which need a CAS connection.
2. MATTI: Importer for STACK 2 questions.
3. Move STACK_StringUtil to be a static class.  Search project for uses of this.
   (Probably a good idea to do this before the project gets to big!)
4. Add "healthcheck pages", installation instructions and confirm configuration settings.
   Links from the settings page.
5. Tim: Question stub ready for further development.

Gradually change variable and function names to conform to Maxima coding standards.

## Milestone 2. ## 

Basic skelaton import of STACK 2 questions and storage in the database.
Teacher can create a question version and attempt this from the database, with no caching.  

## Milestone 3. ## 

Ininstate the dynamic cache and make it work with one Moodle behaviour.

## Milestone 4. ## 

Editing forms in Moodle to allow creation of questions.
Import and export of STACK 3 questions in Moodle's format.

## Milestone 5. ## 

Add reporting functionality.
Add user documentation.
Ensure all fields are imported by the question importer.
Installation documentation, and reporting.
Make it work with all Moodle behaviours.

##########################################################################################

## Maxima

1. Update the list of forbidden keywords....
2. Check out better ways of connecting to Maxima.
  1. https://code.google.com/p/remote-maxima/
  2. http://www.lon-capa.org/maximaasserver.html
  
## Documentation system

1. 404 error does not add an entry to the log.  See line 79 of 
2. What happened to `docMaintenance.php`?  This hasn't been incorporated yet.  We need the ReportWidgets to be included for this to function.  Ben?
3. Update the file, and link this to the documentation system (or just abandon it and use .md!) :

     \stack\www\lib\maxima\stackfun.php

4. Update the file

     \stack\www\lib\maxima\maximafun.php

## Other longer term jobs

1. Refactor the answer tests to remove duplicate code.


#######################################################################################

### Version 3.0 ###

_Not yet released_.  Target, September 2012.

Major re-engineering of the code by  the Open University.  Reporting and documentation added by Ben Holmes.

This round of development does not plan to introduce major new features, or to make major changes to
the core functionality. An explicit aim is that "old questions will still work".  

Key features

* Integration into the quiz of Moodle 2.3.
* Support for Maxima up to 5.26.0.
* Documentation moved from the wiki to within the code base.
* Move from CVS to GIT.
* Language support added: nl, de.

## Future plans ##

We have a dedicated page for [future plans](Future_plans).

## Past versions and History ##

STACK is a direct development of the CABLE project which ran at the University of Birmingham.
CABLE was a development of the AiM computer aided assessment system.

### Version 2.2 ###

Released: October 2010 session.

* Enhanced reporting features.
* Enhanced question management features in Moodle.  E.g. [import multiple questions](https://sourceforge.net/tracker/?func=detail&aid=2930512&group_id=119224&atid=683351)
  from AiM/Maple TA at once, assign multiple questions to Moodle question banks.
* Slider interaction elements.


### Version 2.1 ###

Developed by Chris Sangwin and Simon Hammond at the University of Birmingham.
Released: Easter 2010 session.

Key features

* [Precision](../Authoring/Answer_tests#Precision) answer test added to allow significant to be checked.
* [Form](../Authoring/Answer_tests#Form) answer test added to test if an expression is in completed square form.
* List interaction element expanded to include checkboxes.  See [List](../Authoring/Interaction_elements#List).
* Move to Maxima's `random()` function, rather then generate our own pseudo random numbers
* [Conditionals in CASText](https://sourceforge.net/tracker/?func=detail&aid=2888054&group_id=119224&atid=683351)
* Support for Maxima 5.20.1
* New option added: OptWorkedSol.  This allows the teacher to decide whether the tick box to request the worked solution is available.
* Sample resources included as part of the [FETLAR](http://www.fetlar.bham.ac.uk) project.


### Version 2.0 ###

Released, September 2007.  Developed by Jonathan Hart and Chris Sangwin at the University of Birmingham. 

Key features 

* Display of mathematics now taken care of by [JSMath](../Components/JSMath). 
* Integrated into Moodle. 
* Variety of interaction elements. 
* Multi-part questions.
* Cache. 
* Item tests. 

### Version 1.0 ### 

Released, 2005.  Developed by Chris Sangwin at the University of Birmingham.


