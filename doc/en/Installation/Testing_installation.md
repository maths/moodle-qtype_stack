# Testing Installation

It is important to confirm that STACK has been installed correctly, and that it is connecting to the CAS.

## STACK configuration page

STACK provides a number of options.  To set these you must login as the Moodle site Administrator.  Navigate to 

    Site administration -> 
    Plugins ->
    Question Types ->
    STACK
    
## Health-check script

To confirm if the PHP scripts are connecting to Maxima navigate to the `STACK configuration page`.  Choose the link to the healthcheck script.

The CAS-debug option in the STACK settings will provide a very verbose output which is indispensable at this stage.  Turn this off for production servers, as it is wasteful of storage, particularly when caching results.

The healthcheck script checks the following. 

* Check LaTeX is being converted correctly?  Check [MathJax](../Developer/Mathjax.md) or other LaTeX converter.
* Can PHP call external applications?  No, then change PHP settings. 
* Can PHP call Maxima? No, then see below.
* Graph plotting. Are auto-generated plots being created correctly?  There should be two different graphs.  If not, check the gnuplot settings, and directory permissions.

If PHP does not connect to Maxima then this checklist might help.

1. Maxima version.  If you have installed more than one version of Maxima on your machine you will probably need to choose one explicitly.
2. If you get the following error `loadfile: failed to load /usr/share/maxima/5.32.1/share/draw/draw.lisp` then remove the optional libraries from `Load optional Maxima libraries:`.  Set this to blank and re-try the healthceck.

## Maxima optional packages

Maxima has a wide range of optional libraries.  Which are loaded on your server is set with the option `qtype_stack | maximalibraries` from the STACK question type settings page.
Currently the defualt setting is to load the following optional Maxima packages whenever Maxima is used.

    stats, distrib, descriptive

We need to support STACK in a wide range of situations. In production environments system admins have asked us to check packages do not write files to the server, or have other server side effects.  E.g. the plot2d command executes a `gnuplot` process on the server for example.  For this reason only some optional packages can be loaded into STACK.  Currently the only supported packages are

    stats, distrib, descriptive

If you wish to subvert this process you will need to alter the source code of STACK.  If you have authority on your server to modify the source code you already have some level of responsibility and trust on the server!  In the file `/stack/cas/installhelper.class.php`, there is a static class `$maximalibraries` which contains the list of permitted libraries.

# Caching CAS output

By default, the interactions with the CAS are cached.  You can connect freshly to the CAS each time, which is useful for  debugging, and this option is available on the STACK configuration page.  To clear the cache, click the button on the bottom of the Health-check script. 

## Optimizing Maxima 

Maxima is the rate-determining step in performance to STACK. Once you have the STACK question type working with a direct connection to the CAS, then you should consider optimizing the  performance of Maxima.  See the page dedicated to [optimizing Maxima](../CAS/Optimising_Maxima.md) 

## CAS Chat

At any stage you can evaluate a fragment of CASText by using the CASChat script.  There is a link from the STACK configuration page. 

# Test results

As of 2015-9-17, STACK has been tested on the following versions of Maxima.

* 5.31.3 = PASS
* branch_5_35_base_87_g454731b = PASS (compiled from source)
* 5.36.1 = PASS
