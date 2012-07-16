# Optimising Maxima

The optimisations described here have not been tested much, but they have the
potential to greatly speed up STACK. Please let us know if you try them.

The instructions for both CLISP and SBCL have been tested and work in STACK 3,
but it is not clear how much they actually shorten access times, if at all. SBCL
was tested with Maxima version 5.23.2 and CLISP with some Maxima version. The
instructions do *not* seem to work with Maxma 5.27 as of 28 May 2012.

There are several ways to reduce the access and execution time of this CAS which
can prove useful for scaling.

## Compiled Lisp ##

[Maxima](../CAS/Maxima.md) can be run with a number of Lisp implementations.
Although CLISP is the most portable -- due to being interpreted -- other lisps can give faster execution.

[Maxima](../CAS/Maxima.md) can be compiled with one or more of 4 LISP implementations;
[CLISP](http://en.wikipedia.org/wiki/CLISP) , [CMUCL](http://en.wikipedia.org/wiki/CMU_Common_Lisp),
GCL and SBCL. Of the four, CLISP is the most portable, but is also by far the slowest due to its being
the only LISP interpreter. Using [Maxima](../CAS/Maxima.md) compiled with CMUCL, GCL or SBCL will generally
give much better performance.

## Preloading ##

Lisp is able to save a snapshot of its current state to a file. This file can
then be used to re-start Lisp, and hence Maxima, in exactly that state. This
optimisation involves creating a snap-shot of Lisp with Maxima and all the
STACK code loaded, which can speed up launch times be an order of magnitude on
Linux.This [tip was originally provided Niels
Walet](http://stack.bham.ac.uk/live/mod/forum/discuss.php?d=134).

### CLISP ###

The principle is to [save an image](http://clisp.cons.org/impnotes/image.html)
of Maxima running with STACK libraries already loaded then run this directly.  It
is fairly straightforward with the following steps.

* Get STACK working with Platform type set to 'Linux'. Run the health-check.
* Check your Maxima Lisp with **maxima --list-avail** to see what Lisps you have
to run Maxima.  We assume you have CLISP. Type **locate lisp.run** to find the
path(s) for the next step. You might need to run the command as root, and if you
get no results try doing a **sudo updatedb**.
* Load Maxima, using switches for a particular version if you need, e.g. **maxima -l CLISP -u 5.19.2**.
* Within Maxima, type the following lines to create an image and exit.

~~~~
	load("<path>/maximalocal.mac");
	:lisp (ext:saveinitmem "/path/to/moodledata/stack/maxima-optimised.mem" :init-function #'user::run)
	quit();
~~~~

* Go into the STACK settings and set Platform type to 'Linux (Optimised)'.
* Set Maxima command to.
~~~~~~
	<path>/lisp.run -q -M <path>/maxima-clisp.mem
~~~~~~

* Click Save changes at the bottom of the settings page.
* Visit the healthcheck page, and clear the cache (if applicable), to make sure everything is still working.

Access speed increases of between 2 and 9.5 times have been reported over the standard CLISP configurations.
Applying this to compiled Lisp versions - such as CMUCL - is being investigated.

### SBCL ###

If you are using stack with sbcl (if you are using CentOS/sl5/RHEL with maxima from epel),
use the following to generate a stand alone executable:

* Get STACK working with Platform type set to 'Linux'. Run the health-check.
* Go into your moodledata/stack folder as the current directory, and run Maxima.
* In Maxima, type the commands:
~~~~
	load("maximalocal.mac");
	:lisp (sb-ext:save-lisp-and-die "maxima-optimised" :toplevel #'run :executable t)
~~~~

* Go into the STACK settings and set the Platform to 'Linux (Optimised)'.
* You should be able to leave Maxima command blank.
* Click Save changes at the bottom of the settings page.
* Visit the healthcheck page, and clear the cache (if applicable), to make sure everything is still working.

### Other Lisps ###

The following web pages have more information for a few types of lisp: <http://stackoverflow.com/questions/25046/lisp-executable> and
<http://code.google.com/p/lispbuilder/wiki/StandAloneExecutables#Defining_a_Startup_Function>

## Putting Maxima on other servers ##

See https://github.com/aharjula/maximapool for an implementation of this.

Running Maxima on a separate server dedicated to the task is more secure. It also
improves performance becuase the server can start up Maxima processes in advance
so they are all ready and waiting to compute some CAS with zero delay.
