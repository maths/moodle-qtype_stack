# Installation instructions.

STACK is being used live at many institutions, including the University of Edinburgh, the UK Open University, Aalto, Loughborough University, and the University of Birmingham in the UK.

We appreciate some people prefer hosted services as an alternative to running their own server.  If so, then please contact the developers for more details of current providers.

STACK is designed to be used on a Linux-based server.  For testing and local question development we recommend using virtual box on a Windows/MS machine.  (The windows/MS option exists for legacy reasons and is currently not supported.)

## 0. Set up PHP with mbstring

STACK v4.3 and later require the PHP `mbstring` library.   While this is included in many distros, it is not yet included by default in all.

On an existing Moodle site navigate to

    /admin/environment.php

to confirm before adding this plug-in.

On some Linux distros, you simply need to

    apt-get install php-mbstring

and then re-start the web server.

## 1. Set up Moodle.

Please ensure you have [installed Moodle](http://docs.moodle.org/en/Main_page).

* STACK has been tested on Moodle 4.0 to Moodle 4.4 inclusive.
* We intend to support STACK within the normal Moodle [release cycle](https://docs.moodle.org/dev/Releases).  We intend to support all future Moodle releases. If your version of Moodle is not listed here please contact the developers: we probably simply have not done the testing of future versions yet.  For longer support of older versions of Moodle please contact us, otherwise will will drop them from our list.

Please ensure LaTeX can be displayed.  We currently support [MathJax](Mathjax.md) through the Moodle MathJax filter.

Consider updating the MathJax settings to wrap long equations. In particular, add

    CommonHTML: { linebreaks: { automatic: true } },
    "HTML-CSS": { linebreaks: { automatic: true } },
    SVG: { linebreaks: { automatic: true } }

to `filter_mathjaxloader | mathjaxconfig` in the filter settings: Dashboard > Site administration > Plugins > Filters > MathJax

## 2. Install gnuplot and Maxima

Ensure gcc, gnuplot and [Maxima](http://maxima.sourceforge.net) are installed on your server.  Currently Maxima 5.38.1 to 5.47.0 are supported.  Please contact the developers to request support for other versions.  (Newer versions will be supported, and prompts to test them are welcome.)  We currently recommend that you use any version of Maxima after 5.43.0.

Maxima can be installed via a package manager on most Linux distributions (e.g. `sudo apt-get install maxima` on Debian/Ubuntu), [downloaded](http://maxima.sourceforge.net/download.html), or [compiled from source](Maxima.md).  Please make sure you also have `maxima-share` installed.  (This is automatically installed on some distributions, but not others.)

To check your version of maxima, run `maxima --version`.  If Moodle is set up using Apache, STACK will run maxima through the Apache user (`www-data/apache2`).  To check that this works, run maxima as the apache user (e.g. `sudo -u www-data maxima`).  Later versions of maxima create a cache and thus the executing user needs to have write access to a temporary folder, see [#731](https://github.com/maths/moodle-qtype_stack/issues/731) for more details and troubleshooting.

Alternatively, Maxima can also be run on a separate server via [GoeMaxima](https://github.com/mathinstitut/goemaxima) or [MaximaPool](https://github.com/maths/stack_util_maximapool).

Please note

* Please avoid versions 5.37.x which are known to have a minor bug which affects STACK. In particular with `simp:false`, \(s^{-1}\) is transformed into \(1/s\).  This apparently minor change makes it impossible to distinguish between the two forms.  This causes all sorts of problems.  Do not use Maxima 5.37.1 to 5.37.3.
* Older versions of Maxima:  in particular, Maxima 5.23.2 has some differences which result in \(1/\sqrt{x} \neq \sqrt{1/x}\), and similar problems.  This means that we have an inconsistency between questions between versions of maxima.   Of course, we can argue about which values of \(x\) make \(1/\sqrt{x} = \sqrt{1/x}\), but currently the unit tests and assumption is that these expressions should be considered to be algebraically equivalent!   So, older versions of Maxima are not supported for a reason.  Please test thoroughly if you try to use an older version, and expect some errors in the mathematical parts of the code.
* If you install more than one version of Maxima then you will need to tell STACK which version to use.  Otherwise just use the "default" option.

Instructions for installing a more recent version of Maxima on CentOS 6 are available on the [Moodle forum](https://moodle.org/mod/forum/discuss.php?d=270956)  (Oct 2014).

## 3. Add some additional question behaviours

STACK requires these.

1. Obtain Deferred feedback with explicit validation behaviour code. You can [download the zip file](https://github.com/maths/moodle-qbehaviour_dfexplicitvaildate/zipball/master), unzip it, and place it in the directory `moodle/question/behaviour/dfexplicitvaildate`. (You will need to rename the directory `moodle-qbehaviour_dfexplicitvaildate -> dfexplicitvaildate`.)

Alternatively, get the code using git by running the following command in the top level folder of your Moodle install:

        git clone https://github.com/maths/moodle-qbehaviour_dfexplicitvaildate.git question/behaviour/dfexplicitvaildate

2. Obtain Deferred feedback with CBM and explicit validation behaviour code. You can [download the zip file](https://github.com/maths/moodle-qbehaviour_dfcbmexplicitvaildate/zipball/master), unzip it, and place it in the directory `moodle/question/behaviour/dfcbmexplicitvaildate`. (You will need to rename the directory `moodle-qbehaviour_dfcbmexplicitvaildate -> dfcbmexplicitvaildate`.)

Alternatively, get the code using git by running the following command in the top level folder of your Moodle install:

        git clone https://github.com/maths/moodle-qbehaviour_dfcbmexplicitvaildate.git question/behaviour/dfcbmexplicitvaildate
2. Obtain adaptivemutlipart behaviour code. You can [download the zip file](https://github.com/maths/moodle-qbehaviour_adaptivemultipart/zipball/master), unzip it, and place it in the directory `moodle/question/behaviour/adaptivemultipart`. (You will need to rename the directory `moodle-qbehaviour_adaptivemultipart  -> adaptivemultipart`.)

Alternatively, get the code using git by running the following command in the top level folder of your Moodle install:

        git clone https://github.com/maths/moodle-qbehaviour_adaptivemultipart.git question/behaviour/adaptivemultipart
3. Login to Moodle as the admin user and click on Notifications in the Site Administration panel.

## 4. Add the STACK question type

STACK is a question type for the Moodle quiz.

1. Obtain the code. You can [download the zip file](https://github.com/maths/moodle-qtype_stack/zipball/master), unzip it, and place it in the directory `moodle/question/type/stack`. (You will need to rename the directory `moodle-qtype_stack -> stack`.)

    Alternatively, get the code using git by running the following command in the top level folder of your Moodle install:

        git clone https://github.com/maths/moodle-qtype_stack.git question/type/stack
2. Login to Moodle as the admin user and click on Notifications in the Site Administration panel.
3. As the admin user, navigate to `Home > Site administration > Plugins > Question types > STACK`.  Please choose and save the appropriate options.
4. On the same page, click on the link to the healthcheck script.  This writes local configuration files and then helps you verify that all aspects of STACK are working properly.

You must be able to connect to the CAS, and for the CAS to successfully create plots, before you can use STACK. You might want to try [optimising Maxima](Optimising_Maxima.md) access times.

You should now have a question type available to the Moodle quiz.

## 5. Multi-language support

STACK questions can be localised into [multiple languages](../Authoring/Languages.md).

1. Your site administrator must enable the [Moodle multi-language content filter](http://docs.moodle.org/en/Multi-language_content_filter).
2. The multi-language content filter must be applied before the MathJax filter, otherwise strange results will occur.

## 6. Activity names auto-linking filter

By default the Moodle "Activity names auto-linking" filter is enabled.  The Activity names auto-linking filter is a site Filter that will create links to an activity whenever the name of the activity is written in texts within the same course in which the activity is located. This includes forum postings, pages, labels etc.

This also includes question content.

STACK applies filters mid-way through the question creation process, mostly to provide multi-language support.  However, this means that other filters (including the Activity names auto-linking filter) can disrupt STACK question version generation.

For example, if you create an activity in your course named "feedback", then this filter will link the feedback tags placing potential response trees in your question to that activity.  The filter-generated link will break your STACK questions.

STACK is _not incompatible_ with the Activity names auto-linking filter but it is known to cause some problems in edge cases.

We recommend you disable the Activity names auto-linking filter by default.

## 7. Post-install confirmation.

It is essential to confirm that the PHP scripts are connecting to the CAS.

We have special scripts which provide confirmation and trouble-shooting data to [test the installation](Testing_installation.md).

## 8. Optional (but recommended): Fix DB case sensitivity issue.

Using a database with a case insensitive collation can cause issues; for example MySQL with utf8mb4_unicode_ci. This is a general problem of Moodle, not specific to this plugin. See [Database collation issue](https://docs.moodle.org/dev/Database_collation_issue).

If your DB uses case insensitive collations you must change them to case sensitive ones for the following columns:

* qtype_stacks_inputs -> name

Example command for MySQL 8.0:

```sql
ALTER TABLE mdl_qtype_stack_inputs CHANGE name name VARCHAR(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '';
```

STACK will work without this fix, but input names will not be case sensitive (!) as far as Moodle's database is concerned.  This will throw errors for some questions.

# Upgrading to new versions of STACK

Please check the [release notes](../Developer/Development_history.md) carefully.  Some upgrades inevitably require review/changes to existing STACK questions. We do our best to maintain back compatibility, but some changes are unavoidable.

If STACK is already installed, as described above, it can be updated via git, like this:

1. Go into your moodle-dir and execute:

        cd question/type/stack
        git pull
        cd ..
        cd ..
        cd behaviour/dfcbmexplicitvaildate/
        git pull
        cd ..
        cd dfcbmexplicitvaildate/
        git pull
        cd ..
        cd adaptivemultipart/
        git pull

2. Then login as admin in your moodle and update the database.

3. As admin user, navigate to yourmoodle/admin/settings.php?section=qtypesettingstack

4. Check for the correct maxima version.

5. Click on the link to the healthcheck script.  This writes local configuration files and then helps you verify that all aspects of STACK are working properly.

6. On the same site, you might need to create a new maxima image, by using the button at the end of the page.

It is a good idea to bulk test your materials with the new version.

If you are upgrading from much older versions please look at the [migrations page](Migration.md).
