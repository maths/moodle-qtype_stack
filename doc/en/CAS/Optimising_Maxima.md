# Optimising Maxima

***NOT TESTED IN VERSION 3.0***

There are several ways to reduce the access and execution time of this CAS which can prove useful for scaling.

## Compiled Lisp ##

[Maxima](../CAS/Maxima.md) can be run with a number of Lisp implementations.
Although CLISP is the most portable -- due to being interpreted -- other lisps can give faster execution.

[Maxima](../CAS/Maxima.md) can be compiled with one or more of 4 LISP implementations;
[CLISP](http://en.wikipedia.org/wiki/CLISP) , [CMUCL](http://en.wikipedia.org/wiki/CMU_Common_Lisp),
GCL and SBCL. Of the four, CLISP is the most portable, but is also by far the slowest due to its being
the only LISP interpreter. Using [Maxima](../CAS/Maxima.md) compiled with CMUCL, GCL or SBCL will generally
give much better performance.

## Preloading ##

In STACK version 2, it was possible to decrease CAS access times by a significant factor via a [tip originally provided Niels 
Walet](http://stack.bham.ac.uk/live/mod/forum/discuss.php?d=134) by preloading the STACK files.

### CLISP ###

The principle is to [save an image](http://clisp.cons.org/impnotes/image.html) of Maxima running with STACK libraries already loaded then run this directly.  It 
is fairly straightforward with the following steps.

* Check your Maxima Lisp with **maxima --list-avail** to see what Lisps you have to run Maxima.  We assume you have CLISP. Type **locate lisp.run** to find the 
path(s) for the next step.
* Load Maxima, using switches for a particular version if you need, e.g. **maxima -l CLISP -u 5.19.2**.
* Within Maxima, type the following lines to create an image and exit.

~~~~
	load("<path>/maximalocal.mac");
	:lisp (ext:saveinitmem "maxima-clisp.mem" :init-function #'user::run)
	quit();
~~~~

* Modify configured CAS command in **config.php** as below (checking you are editing casArray["**command**"] and not casArray["**cas**"]):

~~~~
	$this->casArray["command"] = "lisp.run -q -M <path>/maxima-clisp.mem"; // was "maxima"
~~~~

* Comment out the last line of `moodledata/stack/maximalocal.mac`, i.e. to:

~~~~~
	/* load("stackmaxima.mac")$ */
~~~~~

Access speed increases of between 2 and 9.5 times have been reported over the standard CLISP configurations.
Applying this to compiled Lisp versions - such as CMUCL - is being investigated.

### SBCL ###

If you are using stack with sbcl (if you are using centos5/sl5/rhel5 with maxima from epel), use the following to generate a stand alone executable:

	load("<path>/maximalocal.mac");
	:lisp (sb-ext:save-lisp-and-die "maxima-sbcl" :toplevel #'run :executable t)

* Modify configured CAS command in the moodle settings page to 
~~~~~~
	"maxima-sbcl"; // was "maxima"
~~~~~~
* Comment out the last line of `moodledata/stack/maximalocal.mac`, i.e. to:
~~~~~
	/* load("stackmaxima.mac")$ */
~~~~~

Note that 

* the above commands should be typed one line at a time, without spaces.
* running the healthcheck script over writes `maximalocal.mac`

### Other Lisps ###

The following web pages have more information for a few types of lisp: <http://stackoverflow.com/questions/25046/lisp-executable> and 
<http://code.google.com/p/lispbuilder/wiki/StandAloneExecutables#Defining_a_Startup_Function>

## Putting Maxima on other servers ##

It may be possible to place Maxima on a separate and dedicated server.  This may be desirable for many reasons, and may be combined with the optimization steps 
above.  This is untested with STACK.

  *  <http://code.google.com/p/remote-maxima/>
  *  <http://www.lon-capa.org/maximaasserver.html>

