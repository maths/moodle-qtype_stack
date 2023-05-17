# STACK - Maxima Sandbox

It is very useful when authoring questions to be able to test out Maxima code on your local machine in the same environment in which STACK uses [Maxima](Maxima.md) on your server. That is to say, to run a desktop version of Maxima with the local settings and STACK specific functions loaded.  You can copy the Maxima code from the question testing page into the sandbox for offline testing and debugging of a question.  This is also used in [reporting](../Authoring/Reporting.md) and analysis of students' responses. To do this you will need to load the libraries of Maxima functions specific to STACK. You may also want to copy some of your local settings from the server to your local machine to ensure an identical setup, but this is not strictly necessary for most purposes.

The first step is to install wxMaxima on your local machine (http://maxima.sourceforge.net/).

### Getting the STACK libraries

You will need to download the STACK files onto your local machine.  Download all the STACK files from GitHub (git clone or as a .zip).  E.g. try `https://github.com/maths/moodle-qtype_stack/archive/master.zip` 

The only files you need to run the sandbox are contained within the directory

    stack/maxima/

This directory also contains the wxMaxima file `sandbox.wmx` which is the "sandbox" interface file. Your goals are (i) to set Maxima's path so it can find all the files you have downloaded, and (ii) to load the file

    stack/maxima/stackmaxima.mac

Copy `sandbox.wmx` somewhere you can find it later and edit this file to reflect the location of the above file on your local machine.  

On a Microsoft operating system, if you place the all the files (i.e. clone or unzip the download) into 

    c:/tmp/stackroot

the `sandbox.wmx` should work without further adjustment.  

Otherwise open `sandbox.wmx` with wxMaxima and follow the further instructions it contains to setup the path for Maxima.  __Note, the backslash character `\` is a control character so you will need to edit the path to replace the `\` with `/` in wxMaxima.__ Execute the sandbox file with wxMaxima when you have updated the settings with `cell > Evaluate all cells`.  If you see something like the following you have set this up correctly (version numbers will vary).

    [ STACK-Maxima started, library version 2022022300 ] 

You can test this out by using, for example, the `rand()` function.

    rand(matrix([5,5],[5,5]));

to create a pseudo-random matrix.  If `rand` returns unevaluated, then you have not loaded the libraries correctly.

An alternative approach on a Microsoft operating system is to copy the contents of (a working) `sandbox.wmx` file into a

    %USERPROFILE%/Maxima/stacklocal.mac

Using `load("stacklocal")` in any worksheet will load the STACK environement.

### Using the answer tests

Please make sure you read the page on [answer tests](../Authoring/Answer_Tests/index.md) first.

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

If you just want to decide if two expressions are considered to be algebraically equivalent, then use

    algebraic_equivalence(ex1,ex2);

This is the function the answer test `ATAlgEquiv` uses without all the wrapper of a full answer test.

### Useful tips

STACK turns off the traditional two-dimensional display, which we can turn back on with the following command.

    display2d:true;

## Setting Maxima's Global Path (Microsoft) ###

Setting the path in Maxima is a problem on a Microsoft platform.  Maxima does not deal well with spaces in filenames, for example.  The simplest solution is to create a directory

    C:/maxima

and add this to Maxima's path.  Place all Maxima files in this directory, so they will then be seen by Maxima.
For Maxima 5.43.2, edit, or create, the file

    C:/Program Files/maxima-5.43.2/share/maxima/5.43.2/share/maxima-init.mac

ensure it contains the following lines, possibly modified to reflect the directory you have chosen

    file_search_maxima:append([sconcat("C:/maxima/###.{mac,mc}")],file_search_maxima)$
    file_search_lisp:append([sconcat("C:/maxima/###.{lisp}")],file_search_lisp)$

Other versions of Maxima are similar.

## Reflecting the settings on your server

The healtcheck page (Moodle admin access only) displays the contents of the Maxima configuration file which is written to the sever.  This contains Maxima commands to update the path (which you probably don't want to copy) and also the function `STACK_SETUP(ex)` which configures your particular version of STACK.  You may want to replace `STACK_SETUP(ex)` in the sandbox with `STACK_SETUP(ex)` from the Moodle server. For most users this should not be needed, and is most useful for advanced debugging where significant differences between versions matters.

It is more important to match the version of the STACK code you downloaded from github with the version you have on your server.  The STACK documentation page on your server gives the version number of the STACK code at the bottom of the documentation front page.  For example

    https://stack-demo.maths.ed.ac.uk/demo/question/type/stack/doc/doc.php/

shows the version of the STACK code the demo site is running.