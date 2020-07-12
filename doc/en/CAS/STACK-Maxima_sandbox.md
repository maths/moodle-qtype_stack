# STACK Sandbox

It is very useful when authoring questions to be able to test out Maxima code in the same environment in which STACK uses [Maxima](Maxima.md).
That is to say, to run a desktop version of Maxima with the local settings and STACK specific functions loaded.  This is also used in [reporting](../Authoring/Reporting.md) and analysis of students' responses.
To do this you will need to load your local settings, and also the libraries of Maxima functions specific to STACK.

## Setup Maxima and wxMaxima

Install Maxima (http://maxima.sourceforge.net/) and wxMaxima (https://wxmaxima-developers.github.io/wxmaxima/).

## STACK - Maxima sandbox (without access to a server)

If you don't have access to a STACK server then you will need to download the files.   Download all the STACK files from GitHub (git or as a .zip).   E.g. try `https://github.com/maths/moodle-qtype_stack/archive/master.zip`

The only files you need are in

    .../stack/maxima/

In this directory open the file `sandbox.wmx` with wxMaxima and edit it to your needs.

## STACK - Maxima sandbox (with access to a server)

It is very useful when authoring questions to be able to test out Maxima code in the same environment in which STACK uses [Maxima](Maxima.md).
That is to say, to run a desktop version of Maxima with the local settings and STACK specific functions loaded.  This is also used in [reporting](../Authoring/Reporting.md) and analysis of students' responses.
To do this you will need to load your local settings, and also the libraries of Maxima functions specific to STACK.

For example, many of the functions are defined in
~~~~~~~~~
        stack/stack/maxima/stackmaxima.mac
~~~~~~~~~
Hence, on a typical Moodle installation you will find the file at
~~~~~~~~~
        /moodle/question/type/stack/stack/maxima/stackmaxima.mac
~~~~~~~~~

The first part of the instructions work on a Microsoft platform, but instructions for Linux can also be found below. Please note that having a properly set up STACK - Maxima sandbox is <i>not</i> equivalent to running an optimized Maxima.

### Setting Maxima's Path ###

Setting the path in Maxima is a problem on a Microsoft platform.  Maxima does not deal well with spaces in filenames, for example.  The simplest solution is to create a directory

    C:/maxima

and add this to Maxima's path.  Place all Maxima files in this directory, so they will then be seen by Maxima.
For Maxima 5.26.0, edit, or create, the file

    C:/Program Files/Maxima-5.26.0/share/maxima/5.26.0/share/maxima-init.mac

ensure it contains the following lines, possibly modified to reflect the directory you have chosen

    file_search_maxima:append([sconcat("C:/maxima/###.{mac,mc}")],file_search_maxima)$
    file_search_lisp:append([sconcat("C:/maxima/###.{lisp}")],file_search_lisp)$

Other versions of Maxima are similar.

### Loading STACK's functions ###

STACK automatically adjusts Maxima's path and loads a number of files. These define STACK specific functions and reflect your local settings. To do this, STACK loads a file which is automatically created at install time.  This is placed within the `moodledata` directory, typically as

    moodledata/stack/maximalocal.mac

For example, the value might look like

    C:/xampp/data/moodledata/stack/maximalocal.mac

You need to load this file into Maxima to recreate the setup of Maxima as seen by STACK.  Assuming you have created a directory `C:/maxima` as suggested above and added it to Maxima's path, the simplest way to do this is to create a file

    C:/maxima/sm.mac

and into this file add the line

    load("C:/xampp/data/moodledata/stack/maximalocal.mac");

To load this into Maxima simply type

    load(sm);

at Maxima's command line. The time spent setting the path in this way is soon repaid in not having to type the following line each time you want the sandbox.
Your path to `maximalocal.mac` might be significantly longer....!   You will know the file is loaded correctly if you see a message such as the following

    (%i1) load(sm);
    Loading maxima-grobner $Revision: 1.6 $ $Date: 2009-06-02 07:49:49 $
    [Stack-Maxima started V3.0, 13/2/12]
    (%o0) "C:/maxima/sm.mac"

You can test this out by using, for example, the `rand()` function.

    rand(matrix([5,5],[5,5]));

to create a pseudo-random matrix.  If `rand` returns unevaluated, then you have not loaded the libraries correctly.

### Linux instructions ###

In a terminal window, execute the following commands, e.g., in your home folder:

     mkdir stack-maxima
     cd stack-maxima
     pico maxima-init.mac

Put the following three lines into maxima-init.mac:

    file_search_maxima:append([sconcat("<path to your home folder>/stack-maxima/###.{mac,mc}")],file_search_maxima)$
    file_search_lisp:append([sconcat("<path to your home folder>/stack-maxima/###.{lisp}")],file_search_lisp)$
    load("<path to your moodledata>/stack/maximalocal.mac");

Note that the paths above need to be completed. The following command is useful for finding the path to maximalocal.mac:

    locate maximalocal.mac

### Using the answer tests

Please make sure you read the page on [answer tests](../Authoring/Answer_tests.md) first.  Not all the answer tests can be called directly from Maxima.  For example, the string match uses the PHP functions.

Informally, the answer tests have the following syntax

    [Errors, Result, FeedBack, Note] = AnswerTest(StudentAnswer, TeacherAnswer, Opt)

actually the results returned in Maxima are

    [Valid, Result, FeedBack, Note] = AnswerTest(StudentAnswer, TeacherAnswer, Opt)

Errors are echoed to the console, and are trapped by another mechanism.  The valid field is used to render an attempt invalid, not wrong.

To call an answer test directly from Maxima, you need to use the correct function name.   For example, to call the algebraic equivalence (AlgEquiv) answer test you need to use

    ATAlgEquiv(x^2+2,x*(x+1));

The values returned are actually in the form

    [true,false,"",""]

Feedback is returned in the form of a language tag which is translated later. For example,

    (%i1) ATInt(x^2,x*(x+1),x);
    (%o1) [true,false,"ATInt_generic. ",
           "stack_trans('ATInt_generic' , !quot!\\[2\\,x+1\\]!quot!  , !quot!\\(x\\)!quot!  , !quot!\\[2\\,x\\]!quot! ); "]

The chart below shows the answer test, whether it is defined in Maxima or PHP and the options it expects.  Some of the tests are called "hybrid".  These require both significant Maxima and PHP code and cannot be easily reproduced in the sandbox.

| Answer test         | Maxima command name   | Maxima/PHP | Option ?
| ------------------- | --------------------- | ---------- | -------------
| AlgEquiv            | ATAlgEquiv            | Maxima     |
| AlgEquivNouns       | ATAlgEquivNouns       | Maxima     |
| EqualComAss         | ATEqualComAss         | Maxima     |
| CasEqual            | ATCasEqual            | Maxima     |
| SameType            | ATSameType            | Maxima     |
| SubstEquiv          | ATSubstEquiv          | Maxima     |
| SysEquiv            | ATSysEquiv            | Maxima     |
| Sets                | ATSets                | Maxima     |
| Expanded            | ATExpanded            | Maxima     |
| FacForm             | ATFacForm             | Maxima     | Variable
| SingleFrac          | ATSingleFrac          | Maxima     |
| PartFrac            | ATPartFrac            | Maxima     | Variable
| CompSquare          | ATCompSquare          | Maxima     | Variable
| PopLogic            | ATPopLogic            | Maxima     |
| Equiv               | ATEquiv               | Maxima     |
| EquivFirst          | ATEquivFirst          | Maxima     |
| Num-GT              | ATGT                  | Maxima     |
| Num-GTE             | ATGTE                 | Maxima     |
| NumSigFigs          | ATNumSigFigs          | Maxima     | Number sig figs
| NumAbsolute         |                       | Hybrid     |
| NumRelative         |                       | Hybrid     |
| NumDecPlaces        |                       | Hybrid     |
| NumDecPlacesWrong   |                       | Hybrid     |
| LowestTerms         | ATLowestTerms         | Maxima     |
| UnitsSigFigs        | ATUnitsSigFigs        | Maxima     |  Shares code with NumSigFigs
| UnitsSigFigs        | ATUnitsStrictSigFigs  | Maxima     |  Shares code with NumSigFigs
| UnitsAbsolute       | ATUnitsAbsolute       | Maxima     |  Shares code with NumAbsolute
| UnitsAbsolute       | ATUnitsStrictAbsolute | Maxima     |  Shares code with NumAbsolute
| UnitsRelative       | ATUnitsRelative       | Maxima     |  Shares code with NumRelative
| UnitsRelative       | ATUnitsStrictRelative | Maxima     |  Shares code with NumRelative
| Diff                | ATDiff                | Maxima     | Variable
| Int                 | ATInt                 | Maxima     | Variable
| String              |                       | PHP        |
| StringSloppy        |                       | PHP        |
| SRegExp             | ATSRegExp             | Maxima     |


If you just want to decide if two expressions are considered to be algebraically equivalent, then use

    algebraic_equivalence(ex1,ex2);

This is the function the answer test `ATAlgEquiv` uses without all the wrapper of a full answer test.

### Where is the Maxima code?

All the maxima code is kept in

    ...\moodle\question\type\stack\stack\maxima

The bulk of the functions are defined in

    ...\moodle\question\type\stack\stack\maxima\stackmaxima.mac
    ...\moodle\question\type\stack\stack\maxima\assessment.mac

### Useful tips

STACK turns off the traditional two-dimensional display, which we can turn back on with the following command.

    display2d:true;
