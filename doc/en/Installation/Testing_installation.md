# Testing Installation

It is important to confirm that STACK has been installed correctly, and that it is connecting to the CAS.

## STACK configuration page

STACK provides a number of options.  To set these you must login as the Moodle site Administrator.  Navigate to 

    Site administration -> Plugins -> Question Types -> STACK

## Healthcheck script

To confirm if the PHP scripts are connecting to Maxima navigate to the _STACK configuration page_ (see above).  Choose the link to the "healthcheck script".

The healthcheck script checks the following. 

* Check LaTeX is being converted correctly?  Check [MathJax](Mathjax.md) or another LaTeX converter.
* Can PHP call external applications?  No, then change PHP settings. 
* Can PHP call Maxima? No, then see below.
* Graph plotting. Are auto-generated plots being created correctly?  There should be two different graphs.  If not, check the gnuplot settings, and directory permissions.

The CAS-debug option in the STACK settings will provide a very verbose output which is indispensable at this stage.  Turn this off for production servers, as it is wasteful of storage, particularly when caching results.

If PHP does not connect to Maxima then please see the "Troubleshooting" section below.

## Maxima optional packages

Maxima has a wide range of optional libraries.  Which are loaded on your server is set with the option `qtype_stack | maximalibraries` from the STACK question type settings page.
Currently the default setting is to load the following optional Maxima packages whenever Maxima is used.

    stats, distrib, descriptive

We need to support STACK in a wide range of situations. In production environments system admins have asked us to check packages do not write files to the server, or have other server-side effects.  E.g. the plot2d command executes a `gnuplot` process on the server for example.  For this reason only some optional packages can be loaded into STACK.  Currently the only supported packages are

    stats, distrib, descriptive

If you wish to subvert this process you will need to alter the source code of STACK.  If you have authority on your server to modify the source code you already have some level of responsibility and trust on the server!  In the file `/stack/cas/installhelper.class.php`, there is a static class `$maximalibraries` which contains the list of permitted libraries.

# Caching CAS output

By default, the interactions with the CAS are cached.  You can connect freshly to the CAS each time, which is useful for  debugging, and this option is available on the STACK configuration page.  To clear the cache, click the button on the bottom of the healthcheck script. 

## Optimizing Maxima 

Maxima is the rate-determining step in performance to STACK. Once you have the STACK question type working with a direct connection to the CAS, then you should consider optimizing the  performance of Maxima.  See the page dedicated to [optimizing Maxima](Optimising_Maxima.md) 

## CAS Chat

At any stage you can evaluate a fragment of CASText by using the CASChat script.  There is a link from the STACK configuration page. 

## Testing your questions when you upgrade

Whenever you upgrade to a new version of the STACK plugin, it is a really good idea to run all
of the [question tests](../Authoring/Testing.md) to be sure that the behaviour of STACK has not
changed in a way that breaks any of your questions. To do this, go to 

    Site administration -> Plugins -> Question types -> STACK 

and follow the "run the question tests in bulk script" link.

It is even possible, with a bit of hacking, to [execute the question tests from
one Moodle site on a different Moodle site](../Developer/Running_question_tests_other_site.md).
For example you may be evaluating the latest release of STACK on a test server, and you would
like to know if the upgrade will break any of your existing questions.
(And you don't want to do a lot of exporting and importing.)

# Troubleshooting an upgrade

When you upgrade, the STACK plugin will try to automatically recreate the optimised Maxima image.  Occasionally this will not work and you will need to troubleshoot why.

### 1. GOAL: maxima works on the server

Check Maxima is installed and working.  E.g. type `maxima` on the command line, and try a non-trivial calculation such as `diff(sin(x^2),x);` to confirm Maxima is working.  Use `quit();` to exit.

### 2. GOAL: STACK works!

Next, check STACK is working without the optimised image, and without caching.  The STACK settings are defined on the plugin page.

    Site administration -> Plugins -> Question Types -> STACK

To set these you must login as the Moodle site Administrator.  Take note of your old settings and save the following settings.

    qtype_stack | platform = Linux
    qtype_stack | maximaversion = default
    qtype_stack | casresultscache = Do not cache
    qtype_stack | castimeout = 100
    qtype_stack | maximacommand =
    qtype_stack | maximacommandopt =
    qtype_stack | maximalibraries = 

Note that the `maximacommand`, `maximacommandopt` and `maximalibraries` should be empty boxes.

The `castimeout` of 100s is excessive. However, the very first time Maxima is called on a server it internally compiles a lot of LISP sourcecode.  This can take a surprisingly long time!

### 3. GOAL: Reduce timeout and check Maxima libraies.

Now we need to back away gently from the above raw confuration, back towards the defaults/production settings.

    qtype_stack | castimeout = 10
    qtype_stack | maximalibraries = stats, distrib, descriptive, simplex

Not all versions of Maxima have the stats libraries, but if you do have them add them back now.  You can check whether you have each library individually on the command line by typing `load("stats");` (etc.)  Save these settings and run the healthcheck script.

### 4. GOAL: choose a specific maxima version (optional).

If you want to choose a specific version of Maxima now is the time to do so by selecting

    qtype_stack | maximaversion

from the dropdown.  Part of the healthcheck script will tell you which version you have on your server or use `maxima --list-avail` on the command line.  If your version does not appear on the dropdown (and it won't if you compiled Maxima from source) then set `maximacommand` to be the command you need to type.  E.g. if you use `maxima --use-version=5.42.1` on the command line then 

    qtype_stack | maximacommand = maxima --use-version=5.42.1

Save these settings and run the healtcheck script.

### 5. Goal: check libraries in Maxima

The setting `qtype_stack | maximalibraries` tries to load some optional Maxima libraries.  Not all versions of Maxima have these libraries, which can be confusing.

Only supported library names can be used.  For example try the following.

    qtype_stack | maximalibraries = stats, distrib, descriptive, simplex

Save these settings and run the healtcheck script.

Note, internally in Maxima this is equivalent to typing `load(stats);` in at the Maxima command line for each library in the list.  You can try this in step 1 above to check each library you want to load by hand.

If you get the following error `loadfile: failed to load /usr/share/maxima/5.32.1/share/draw/draw.lisp` then remove the optional libraries from `qtype_stack | maximalibraries`.  I.e. set this to blank and re-try the healthcheck.  (One of the stats libraries also tries to load the draw library.)

### 6. Goal: create optimised image.

Now press the "Create Maxima Image" button at the bottom of the healthcheck script page to create the optimised image, and read the output of the refreshed healthcheck page.  Note, this page updates some of your settings in the plugin page. In particular, it changes `qtype_stack | platform` to optimised and fills in the value of `qtype_stack | maximacommandopt`.

__Reload the plugin page (but don't save over the top).__

The maxima image is stored in a sub-directory of moodle's `dataroot` directory, specifically in `dataroot/stack`.  This is defined in Moodle's `config.php` as, for example,

    $CFG->dataroot  = '/var/data/moodle311';

The optimised image will therefore be something like

    /var/data/moodle311/stack/maxima_opt_auto'

and the command to execute this (with a timeout) will be

    timeout --kill-after=10s 10s /var/data/moodle311/stack/maxima_opt_auto -eval '(cl-user::run)'

The PHP process must have permission to write to this directory.  If your optimised image was not created please check the file permissions, check the file exists and try to run the command `maximacommandopt` from the server command line.  If the current optimised image was created by a different user (e.g. during command-line install) you may not have permission to replace it.

(The precise name of the Maxima image depends on the LISP version, e.g. `maxima_opt_auto` is generated by GCL.)

### 7. Goal: use the CAS cache.

The very last step is to use the CAS cache.

    qtype_stack | casresultscache = Cache in the database

This should be a working server, using the optimised image.  Please consider using the Maxima Pool for production sites, putting Maxima onto another server competely.