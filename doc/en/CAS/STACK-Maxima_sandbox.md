# STACK - Maxima sandbox

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

These instructions work on a Microsoft platform.

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

You need to load this file into Maxima to recreate the setup of Maxima as seen by STACK.  Assuming you have created a directory `c:/maxima` as suggested above and added it to Maxima's path, the simplest way to do this is to create a file

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

### Useful tips

STACK turns off the traditional two dimensional display, which we can turn back on with the following command.

    display2d:true;
