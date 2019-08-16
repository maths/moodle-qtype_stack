# Installation instructions.

STACK is being used live at a number of institutions, including the University of Edinburgh, the UK Open University, Aalto, Loughborough University, and the University of Birmingham in the UK.  

STACK is designed to be used on a Linux-based server.  The Windows/MS option exists to help teachers author questions offline, and for demonstration and development.  However, for demonstration, development and offline use we strongly recommend using VirtualBox instead of the Windows port.  Note also that support for the optimized Maxima image is not available on Windows platforms, which is a substantial performance improvement.

## 1. Set up Moodle.

* Please ensure you have [installed Moodle](http://docs.moodle.org/en/Main_page).  You must use Moodle 2.7.0 or later, [e.g. here.](https://github.com/moodle/moodle)  We recommend using Moodle 3.5, and STACK 4.2 is untested on versions before Moodle 3.0.
* Please ensure LaTeX can be displayed.  We currently support [MathJax](Mathjax.md) through the Moodle MathJax filter.

STACK requires PHP 5.6 or later.

## 2. Install gnuplot and Maxima

Ensure gnuplot and [Maxima](http://maxima.sourceforge.net) are installed on your server.  Currently Maxima 5.34.1 to 5.36.1 then 5.38.0 to 5.42.2 are supported.  Please avoid versions 5.37.x which are known to have a minor bug which affects STACK.  STACK has been used with Maxima 5.25.1 to 5.36.1, but these older versions are not being actively tested. Please contact the developers to request support for other versions.

We currently recommend that you use Maxima 5.38.1.

Please note 

* Please avoid versions 5.37.x which are known to have a minor bug which affects STACK. In particular with `simp:false`, \(s^{-1}\) is transformed into \(1/s\).  This apparently minor change makes it impossible to distinguish between the two forms.  This causes all sorts of problems.  Do not use Maxim 5.37.1 to 5.37.3.
* From version 5.35.1 on Windows, Maxima is very slow in starting, and there is no optimization mechanism.
* Older versions of Maxima:  in particular, Maxima 5.23.2 has some differences which result in \(1/\sqrt{x} \neq \sqrt{1/x}\), and similar problems.  This means that we have an inconsistency between questions between versions of maxima.   Of course, we can argue about which values of \(x\) make \(1/\sqrt{x} = \sqrt{1/x}\), but currently the unit tests and assumption is that these expressions should be considered to be algebraically equivalent!   So, older versions of Maxima are not supported for a reason.  Please test thoroughly if you try to use an older version, and expect some errors in the mathematical parts of the code.
* If you install more than one version of Maxima then you will need to tell STACK which version to use.  Otherwise just use the "default" option.

The documentation on [Installing Maxima](Maxima.md) includes code for compiling Maxima from source.

Maxima can also be [downloaded](http://maxima.sourceforge.net/download.html) as a self-contained installer program for Windows, RPMs for Linux or as source for all platforms.

Instructions for installing a more recent version of Maxima on CentOS 6 are available on the [Moodle forum](https://moodle.org/mod/forum/discuss.php?d=270956)  (Oct 2014).

## 3. Add some additional question behaviours

STACK requires these.

1. Obtain Deferred feedback with explicit validation behaviour code. You can [download the zip file](https://github.com/maths/moodle-qbehaviour_dfexplicitvaildate/zipball/master), unzip it, and place it in the directory `moodle/question/behaviour/dfexplicitvaildate`. (You will need to rename the directory `moodle-qbehaviour_dfexplicitvaildate -> dfexplicitvaildate`.) 

    Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: 

        git clone git://github.com/maths/moodle-qbehaviour_dfexplicitvaildate.git question/behaviour/dfexplicitvaildate

2. Obtain Deferred feedback with CBM and explicit validation behaviour code. You can [download the zip file](https://github.com/maths/moodle-qbehaviour_dfcbmexplicitvaildate/zipball/master), unzip it, and place it in the directory `moodle/question/behaviour/dfcbmexplicitvaildate`. (You will need to rename the directory `moodle-qbehaviour_dfcbmexplicitvaildate -> dfcbmexplicitvaildate`.) 

    Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: 
    
        git clone git://github.com/maths/moodle-qbehaviour_dfcbmexplicitvaildate.git question/behaviour/dfcbmexplicitvaildate
2. Obtain adaptivemutlipart behaviour code. You can [download the zip file](https://github.com/maths/moodle-qbehaviour_adaptivemultipart/zipball/master), unzip it, and place it in the directory `moodle/question/behaviour/adaptivemultipart`. (You will need to rename the directory `moodle-qbehaviour_adaptivemultipart  -> adaptivemultipart`.) 

    Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: 
    
        git clone git://github.com/maths/moodle-qbehaviour_adaptivemultipart.git question/behaviour/adaptivemultipart
3. Login to Moodle as the admin user and click on Notifications in the Site Administration panel.

## 4. Add the STACK question type

STACK is a question type for the Moodle quiz.

1. Obtain the code. You can [download the zip file](https://github.com/maths/moodle-qtype_stack/zipball/master), unzip it, and place it in the directory `moodle/question/type/stack`. (You will need to rename the directory `moodle-qtype_stack -> stack`.) 

    Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: 
    
        git clone git://github.com/maths/moodle-qtype_stack.git question/type/stack
2. Login to Moodle as the admin user and click on Notifications in the Site Administration panel.
3. As the admin user, navigate to `Home > Site administration > Plugins > Question types > STACK`.  Please choose and save the appropriate options.
4. On the same page, click on the link to the healthcheck script.  This writes local configuration files and then helps you verify that all aspects of STACK are working properly.

You must be able to connect to the CAS, and for the CAS to successfully create plots, before you can use STACK. You might want to try [optimising Maxima](../CAS/Optimising_Maxima.md) access times.

You should now have a question type available to the Moodle quiz.

## 5. Post-install confirmation.

It is essential to confirm that the PHP scripts are connecting to the CAS.  

We have special scripts which provide confirmation and trouble-shooting data to [test the installation](Testing_installation.md).

## 6. Optional: Add the STACK quiz report {#Report}

The reports are still in a beta development stage and you can skip this step.   If you wish to take advantage of bespoke reports on attempts at an individual STACK question you will need to install the STACK quiz report format separately.  This is distributed as `quiz_stack`.  

1. Obtain the code. You can [download the zip file](https://github.com/maths/quiz_stack/zipball/master), unzip it, and place it in the directory `moodle/mod/quiz/report/stack`. (You will need to rename the directory `quiz_stack -> stack`.) 

    Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: 
    
        git clone git://github.com/maths/quiz_stack.git mod/quiz/report/stack
2. Login to Moodle as the admin user and click on Notifications in the Site Administration panel.

## 7. Optional: Add the LTI provider plugin

This optional step is for people who wish to use STACK through another interface than Moodle (or ILIAS).  Details are in the [LTI](LTI.md) page.

# Migration from STACK 3.X to STACK 4.0

STACK 4.0 has one important change in the question authoring.  [CAS text](../Authoring/CASText.md) now uses `{@...@}` in include mathematics in the text.  The change from `@...@` to `{@...@}` gives us matching parentheses to parse, which is much better.  The `{..}` will not break LaTeX.

You will need to update all your existing questions which include CAS calculations. This includes all fields, e.g. in the feedback as well.  To help with this process we have an automatic conversion script.  As an admin user navigate to 

    Site administration -> 
    Plugins ->
    Question Types ->
    STACK

Then choose the link "The fix maths delimiters script".  If you have any difficulties with this process please contact the developers.

# Migration from STACK 2.X to STACK 3.0

If you wish to import STACK 2 questions into STACK 3 you will need to install the STACK question format separately.  This is distributed as `qformat_stack`.  It provides a different _question format_ for the Moodle quiz importer.

1. Obtain the code. You can [download the zip file](https://github.com/maths/moodle-qformat_stack/zipball/master), unzip it, and place it in the directory `moodle/question/format/stack`. (You will need to rename the directory `moodle-qformat_stack -> stack`.) 

    Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: 
    
        git clone git://github.com/maths/moodle-qformat_stack.git question/format/stack
2. Login to Moodle as the admin user and click on Notifications in the Site Administration panel.

There have been a number of changes between STACK 2 and STACK 3.  Please read the [notes on the importer](../Authoring/ImportExport.md) before using it.
