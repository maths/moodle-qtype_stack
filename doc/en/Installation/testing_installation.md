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

This script checks the following. 

* Check LaTeX is being converted correctly?  Check [MathJax](../Developer/Mathjax.md) or other LaTeX converter.
* Can PHP call external applications?  No, then change PHP settings. 
* Can PHP call Maxima? No, check the settings in the STACK plugin, and look carefully at the calls being made.  You may need to explicitly set a path to the Maxima executable if PHP can't find it automatically. Do you have permission to run this as the web server?
* Graph plotting. Are auto-generated plots being created correctly.  There should be two different graphs.  If not, check the gnuplot settings, and directory permissions.

The CAS-debug option in the STACK settings will provide a very verbose output which is indispensable at this stage.  Turn this off for production servers, as it is wasteful of storage, particularly when caching results.

## Caching CAS output

By default, the interactions with the CAS are cached.  You can connect freshly to the CAS each time, which is useful for  debugging, and this option is available on the STACK configuration page.  To clear the cache, click the button on the bottom of the Health-check script. 

## Optimizing Maxima 

Maxima is the rate-determining step in performance to STACK. Once you have the STACK question type working with a direct connection to the CAS, then you should consider optimizing the  performance of Maxima.  See the page dedicated to [optimizing Maxima](../CAS/Optimising_Maxima.md) 

## CAS Chat

At any stage you can evaluate a fragment of CASText by using the CASChat script.  There is a link from the STACK configuration page. 
