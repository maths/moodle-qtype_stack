# Optimising Maxima

There are several ways to reduce the access and execution time of this CAS which can prove useful for scaling. They have the potential to greatly speed up STACK, and are widely used.  It is particularly important on a Unix system to compile the Maxima code. Please let us know if you try them.

The instructions for both CLISP and SBCL have been tested and work in STACK 3.  As of November 2015, these are working with Maxima 5.36.1, but some versions of Maxima do have problems.  We now have some code to attempt to automatically generate the LISP images described below.  Beware, however, that when regenerating the image you may have to manually delete the old image which may be write-protected.

## Terminating runaway LISPS ##

It is relatively easy for students to inadvertently generate an answer which takes Maxima a very long time to evaluate.  Typically this arises from where Maxima needs to expand out the brackets by comparing `(x-a)^59999` with a similar expression.  It is very hard to ensure this kind of calculation is impossible so in general this situation will arise from time to time.  The PHP scripts have a timeout, but on linux systems you can also ensure the underlying LISP process is killed off using `timeout` command in linux.  This is particularly valuable for production systems where stability is essential.

1. Check that your linux has the `timeout` command.  Because this is not standard we have not included this mechanism by default.
2. Make sure STACK is working.
3. Set the CAS connection timeout variable as normal in the STACK settings.  E.g. you might choose 5 seconds
4. Use the following Maxima command

    timeout --kill-after=6s 6s maxima

It is important that the timeout time is *longer* than the CAS connection timeout.  That way, PHP gives up first and degrates gracefully.  The OS then kills the process later.  If you choose the timeout to be the same or less, PHP may not have gathered enough data to degrade gracefully.  

The above can be used with either a direct maxima connection, or with the image created as described below.

## Compiled Lisp ##

[Maxima](../CAS/Maxima.md) can be run with a number of different [lisp implementations](http://maxima.sourceforge.net/lisp.html).
Although CLISP is the most portable - due to being interpreted - other lisps can give faster execution.


## Preloading ##

Lisp is able to save a snapshot of its current state to a file. This file can then be used to re-start Lisp, and hence Maxima, in exactly that state. This optimisation involves creating a snap-shot of Lisp with Maxima and all the STACK code loaded, which can speed up launch times be an order of magnitude on Linux. This tip was originally provided Niels Walet.

The principle is to save an image of Maxima running with STACK libraries already loaded then run this directly.  It is fairly straightforward with the following steps.

* Check your Maxima Lisp with **maxima --list-avail** to see what Lisps you have to run Maxima.
* Load Maxima, using switches for a particular version if you need, e.g. `maxima -l CLISP -u 5.19.2`.
* Load relevant packages, including the stackmaxima.mac package
* Save image

### GCL ###

This is the default lisp used by most of the binary distributions, and therefore the lisp which you are most likely to have.

* Get STACK working with Platform type set to 'Linux'. Run the health-check. It is important to do this every time you upgrade your version.

~~~~
    load("<path>/maximalocal.mac");
    load("<path>/stackmaxima.mac");
    load(stats);
    load(distrib);
    load(descriptive);
    :lisp (si::save-system "/path/to/moodledata/stack/maxima-optimised")  
    quit();
~~~~

* Go into the STACK settings and set Platform type to 'Linux (Optimised)'.
* Set Maxima command to.

    /path/to/moodledata/stack/maxima-optimised  -eval '(cl-user::run)'


### CLISP ###

[Save an image in CLISP](http://clisp.cons.org/impnotes/image.html).

* Get STACK working with Platform type set to 'Linux'. Run the health-check. It is important to do this every time you upgrade your version.
* We assume you have CLISP. Type **locate lisp.run** to find the path(s) for the next step. You might need to run the command as root, and if you get no results try doing a **sudo updatedb**.
* Within Maxima, type the following lines to create an image and exit.

~~~~
    load("<path>/maximalocal.mac");
    load("<path>/stackmaxima.mac");
    load(stats);
    load(distrib);
    load(descriptive);
    :lisp (ext:saveinitmem "/path/to/moodledata/stack/maxima-optimised.mem" :init-function #'user::run)
    quit();
~~~~

* Go into the STACK settings and set Platform type to 'Linux (Optimised)'.
* Set Maxima command to.
~~~~~~
    <path>/lisp.run -q -M <path>/maxima-optimised.mem
~~~~~~

* Click Save changes at the bottom of the settings page.
* Visit the healthcheck page, and clear the cache (if applicable), to make sure everything is still working.

Access speed increases of between 2 and 9.5 times have been reported over the standard CLISP configurations.

### SBCL ###

If you are using stack with sbcl (if you are using CentOS/sl5/RHEL with maxima from epel),
use the following to generate a stand alone executable:

* Get STACK working with Platform type set to 'Linux'. Run the health-check. It is important to do this everytime you upgrade your version.
* Go into your moodledata/stack folder as the current directory, and run Maxima.
* In Maxima, type the commands:
~~~~
    load("maximalocal.mac");
    load("<path>/stackmaxima.mac");
    load(stats);
    load(distrib);
    load(descriptive);
    :lisp (sb-ext:save-lisp-and-die "maxima-optimised" :toplevel #'run :executable t)
~~~~

* Go into the STACK settings and set the Platform to 'Linux (Optimised)'.
* You should be able to leave Maxima command blank.
* Click save changes at the bottom of the settings page.
* Visit the healthcheck page, and clear the cache (if applicable), to make sure everything is still working.

### Other Lisps ###

The following web pages have more information for a few types of lisp: <http://stackoverflow.com/questions/25046/lisp-executable> and
<http://code.google.com/p/lispbuilder/wiki/StandAloneExecutables#Defining_a_Startup_Function>

## Putting Maxima on other servers ##

Running Maxima on a separate server dedicated to the task is more secure. We believe it also
improves performance because the server can start up Maxima processes in advance
so they are all ready and waiting to compute some CAS with zero delay.

See a [Maxima pool](http://github.com/maths/stack_util_maximapool) has been implemented to do this.  See <https://github.com/maths/stack_util_maximapool/blob/master/README.md>

## Optimisation results ##

The following data was gathered by CJS on 23/9/2012 using Maxima 5.28.0 with CLISP 2.49 (2010-07-07) on a linux server.

Running the PHP testing suites we have the following data, where all times are in seconds. The second line, in italics, is time per test.

CAS setting       | Answertest (460 tests) | Inputs (257 tests)
----------------- | ---------------------- | -------------------
Linux             | 517.8672               | 208.85655
                  | _1.1258_               | _0.81267_
Mature cache      | 0.92644                | 13.9798
(with linux)      | _0.00201_              | _0.0544_
Linux (optimised) | 95.16954               | 20.89807
                  | _0.20689_              | _0.08132_
Mature cache      | 0.90839                | 1.48648
(when optimised)  | _0.00197_              | _0.00578_

However, not all tests result in a CAS call.  So, to estimate this we subtract the overhead time for a mature cache (which is essentially time for database/PHP processing) from the raw time and divide by the number of CAS calls.  We have the following time per CAS call estimates.

CAS setting       | Answertest (438 calls) | Inputs (204 calls)
----------------- | ---------------------- | -------------------
Linux             | 1.180                  | 0.955
Linux (optimised) | 0.215                  | 0.095

The optimised version saves a considerable amount of time, representing a significant performance improvement in the critical step of just under a second per call.  Well worth the effort (& maintenance) to reward ratio.  It is likely that using a compiled version of LISP would result in further considerable savings.

However, it isn't entirely clear to me at this point why the input tests with a mature cache using the default linux connection takes over 13 seconds.  This seems anomalous.

The following data was gathered by CJS on 10/10/2012 using Maxima 5.28.0 with CLISP 2.49 and SBCL 1.0.58-1.el6, both on the same linux server.  The table gives time in seconds to run the answer tests (462 tests).

Maxima version    | Linux    | Linux optimised
----------------- | -------- | -------------------
CLISP 2.49        | 652.3    | 117.76314
SBCL 1.0.58-1.el6 | 1570.6   | 118.39215

With both lisp versions, the optimisation gives a significant performance gain and there is very little difference between the times of the optimised versions.

## CAS on linux ##

The tests above use Maxima through the PHP interface.  To gauge the overhead from the CAS itself we ran the following tests on the same server using Maxima 5.28.0 with CLISP 2.49.

    model name: Intel(R) Xeon(R) CPU, E3113  @ 3.00GHz

Linux                 | Time for 100 cycles (s)
----------------------| -----------------------
Start Maxima and quit | 43
Start STACK  and quit | 124
Process AtAlgEquiv    | 133

Without optimising linux, compared to processing one AtAlgEquiv, there is approximately 93% overhead in starting the maxima process.

Optimsed Linux         | Time for 100 cycles (s)
-----------------------| -----------------------
Start STACK  and quit  | 12
Process AtAlgEquiv     | 16
Process 100 AtAlgEquiv | 104
Process 100 plot       | 117

The PHP processing time is almost insignificant against the time it takes to initiate and use the CAS.

With the optimised linux we have reduced the loading time, and the loading overhead considerably.  A single ATAlgEquiv request takes about 0.04s.  Asking for 100 ATAlgEquiv requests in a single session take 0.0092s per request.  Asking for 100 plot commands takes 1.05s per plot - which is rather slow (plots undertake a large number of floating point calculations).
The overhead times for loading maxima might be reduced, and smoothed out by using the maxima pool, see <http://github.com/maths/stack_util_maximapool> for an implementation of this.  The computation times are difficult to reduce.

Memory appears to be modest: the optimised linux takes about 15Mb of memory per process.

# Compiling a Maxima image

The following was tested in March 2016 on CENTOS.  It is for compiling a Maxima image.  Really this is mostly for developers.

    sudo yum install sbcl texinfo rpm-build
    cd ~
    wget http://dl.fedoraproject.org/pub/fedora/linux/updates/22/SRPMS/m/maxima-5.36.1-2.fc22.src.rpm
    rpm -i maxima-5.36.1-2.fc22.src.rpm
    cd rpmbuild
    rpmbuild -ba SPECS/maxima.spec
    cd RPMS/x86_64
    sudo yum remove maxima
    sudo yum install maxima-runtime-sbcl-5.36.1-2.el6.x86_64.rpm maxima-5.36.1-2.el6.x86_64.rpm

The following was tested in March 2016 on Ubuntu 14.04.4 LTS (GNU/Linux 3.13.0-79-generic x86_64) (Trusty). Install dependencies:

    sudo apt-get -y install clisp texinfo

Then run:

    cd ~
    wget -O maxima_source.tar.gz http://sourceforge.net/projects/maxima/files/Maxima-source/5.36.1-source/maxima-5.36.1.tar.gz/download
    tar zxvf maxima_source.tar.gz
    cd maxima-5.36.1
    ./configure --with-clisp
    make --silent
    sudo make install --silent
    maxima --list-avail
