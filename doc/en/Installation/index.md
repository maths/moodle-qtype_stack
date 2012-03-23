# Installation instructions.

__WARNING: STACK 3.0 is under development.  This code is intended only for use by developers, and early adopters wishing to help test the code.__

## 1. Set up moodle.

* Please ensure you have [installed moodle](http://docs.moodle.org/23/en/Installing_Moodle).  You must use moodle 2.3 or later.  [E.g.](https://github.com/timhunt/moodle)
* Please ensure LaTeX can be displayed.  We currently support [MathJax](../Developer/Mathjax.md).

## 2. Install GNUPlot and Maxima

Ensure GNUPlot and [Maxima](http://maxima.sourceforge.net) are installed on your server.  Currently Maxima 5.21.1 to 5.26.0 are supported.  Please contact the developers to request support for other versions.

Maxima can be [downloaded](http://maxima.sourceforge.net/download.html) as a self-contained
installer program for Windows, RPMs for Linux or as source for all platforms.  Maxima and
GNUPlot will install themselves in suitable directories.

## 3. Add the STACK question type

STACK is a question type for the moodle quiz.

1. Obtain the code. Either [download the zip file](https://github.com/sangwinc/moodle-qtype_stack/zipball/master), unzip it, and place it in the directory `moodle\question\type\stack`. (You will need to rename the directory `moodle-qtype_stack -> stack`.) Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: `git clone git://github.com/sangwinc/moodle-qtype_stack.git question/type/stack`.
2. Login to Moodle as the admin user and click on Notifications in the Site Administration panel.
3. As the admin user, navigate to `Home > Site administration > Plugins > Question types > Stack`.  Please choose and save the appropriate options.
4. On the same page, click on the link to the healthcheck script.  This writes local configuration files and then helps you verify that all aspects of STACK are working properly.

You must be able to connect to the CAS, and for the CAS to successfully create plots, before you can use STACK.

You should now have a question type available to the moodle quiz.

## 4. Add the STACK question format

If you wish to import STACK 2 questions into STACK 3 you will need to install the STACK question format separately.

__This has not been completed yet, and the code is unavailable.__
