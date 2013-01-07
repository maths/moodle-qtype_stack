# Installation instructions.

STACK 3.0 beta is still under active testing.

## 1. Set up moodle.

* Please ensure you have [installed moodle](http://docs.moodle.org/23/en/Installing_Moodle).  You must use moodle 2.3.2 or later, [e.g. here.](https://github.com/moodle/moodle)  We strongly recommend using the latest version from your stable branch.
* Please ensure LaTeX can be displayed.  We currently support [MathJax](../Developer/Mathjax.md).

## 2. Install GNUPlot and Maxima

Ensure GNUPlot and [Maxima](http://maxima.sourceforge.net) are installed on your server.  Currently Maxima 5.25.1 to 5.28.0 are supported.  Please contact the developers to request support for other versions.

Maxima can be [downloaded](http://maxima.sourceforge.net/download.html) as a self-contained
installer program for Windows, RPMs for Linux or as source for all platforms.  Maxima and
GNUPlot will install themselves in suitable directories.

## 3. Add some additional question behaviours

STACK requries these.

1. Obtain Deferred feedback with explicit validation behaviour code. Either [download the zip file](https://github.com/maths/moodle-qbehaviour_dfexplicitvaildate/zipball/master), unzip it, and place it in the directory `moodle\question\behaviour\dfexplicitvaildate`. (You will need to rename the directory `moodle-qbehaviour_dfexplicitvaildate -> dfexplicitvaildate`.) Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: `git clone git://github.com/maths/moodle-qbehaviour_dfexplicitvaildate.git question/behaviour/dfexplicitvaildate`.
2. Obtain Deferred feedback with CBM and explicit validation behaviour code. Either [download the zip file](https://github.com/maths/moodle-qbehaviour_dfcbmexplicitvaildate/zipball/master), unzip it, and place it in the directory `moodle\question\behaviour\dfcbmexplicitvaildate`. (You will need to rename the directory `moodle-qbehaviour_dfcbmexplicitvaildate -> dfcbmexplicitvaildate`.) Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: `git clone git://github.com/maths/moodle-qbehaviour_dfcbmexplicitvaildate.git question/behaviour/dfcbmexplicitvaildate`.
2. Obtain adaptivemutlipart behaviour code. Either [download the zip file](https://github.com/maths/moodle-qbehaviour_adaptivemultipart/zipball/master), unzip it, and place it in the directory `moodle\question\behaviour\adaptivemultipart`. (You will need to rename the directory `moodle-qbehaviour_adaptivemultipart  -> adaptivemultipart`.) Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: `git clone git://github.com/maths/moodle-qbehaviour_adaptivemultipart.git question/behaviour/adaptivemultipart`.
3. Login to Moodle as the admin user and click on Notifications in the Site Administration panel.

## 4. Add the STACK question type

STACK is a question type for the moodle quiz.

1. Obtain the code. Either [download the zip file](https://github.com/maths/moodle-qtype_stack/zipball/master), unzip it, and place it in the directory `moodle\question\type\stack`. (You will need to rename the directory `moodle-qtype_stack -> stack`.) Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: `git clone git://github.com/maths/moodle-qtype_stack.git question/type/stack`.
2. Login to Moodle as the admin user and click on Notifications in the Site Administration panel.
3. As the admin user, navigate to `Home > Site administration > Plugins > Question types > Stack`.  Please choose and save the appropriate options.
4. On the same page, click on the link to the healthcheck script.  This writes local configuration files and then helps you verify that all aspects of STACK are working properly.

You must be able to connect to the CAS, and for the CAS to successfully create plots, before you can use STACK. You might want to try [optimising Maxima](../CAS/Optimising_Maxima.md) access times.

You should now have a question type available to the moodle quiz.

## 5. Add the STACK quiz report

If you wish to take advantage of bespoke reports on attempts at an individual STACK question you will need to install the STACK quiz report format separately.  
This is distributed as `quiz_stack`.  

1. Obtain the code. Either [download the zip file](https://github.com/maths/quiz_stack/zipball/master), unzip it, and place it in the directory `moodle\mod\quiz\report\stack`. (You will need to rename the directory `quiz_stack -> stack`.) Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: `git clone git://github.com/maths/quiz_stack.git mod/quiz/report/stack`.
2. Login to Moodle as the admin user and click on Notifications in the Site Administration panel.

## 6. Add the STACK question format

If you wish to import STACK 2 questions into STACK 3 you will need to install the STACK question format separately.  This is distributed as `qformat_stack`.  It provides a different _question format_ for the Moodle quiz importer.

1. Obtain the code. Either [download the zip file](https://github.com/maths/moodle-qformat_stack/zipball/master), unzip it, and place it in the directory `moodle\question\format\stack`. (You will need to rename the directory `moodle-qformat_stack -> stack`.) Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: `git clone git://github.com/maths/moodle-qformat_stack.git question/format/stack`.
2. Login to Moodle as the admin user and click on Notifications in the Site Administration panel.

There have been a number of changes between STACK 2 and STACK 3.  Please read the [notes on the importer](../Authoring/ImportExport.md) before using it.
