# Migration from STACK 3.X to STACK 4.0

STACK 4.0 has one important change in the question authoring.  [CAS text](../Authoring/CASText.md) now uses `{@...@}` in include mathematics in the text.  The change from `@...@` to `{@...@}` gives us matching parentheses to parse, which is much better.  The `{..}` will not break LaTeX.

You will need to update all your existing questions which include CAS calculations. This includes all fields, e.g. in the feedback as well.  To help with this process we have an automatic conversion script.  As an admin user navigate to 

    Site administration -> 
    Plugins ->
    Question Types ->
    STACK

Then choose the link "The fix maths delimiters script".  If you have any difficulties with this process please contact the developers.

# Migration from STACK 2.X to STACK 3.0

If you wish to import STACK 2 questions into STACK 3 you will need to install the STACK question format separately.  This is distributed as `qformat_stack`.  It provides a different _question format_ for the Moodle quiz importer.

1. Obtain the code. You can [download the zip file](https://github.com/maths/moodle-qformat_stack/zipball/master), unzip it, and place it in the directory `moodle/question/format/stack`. (You will need to rename the directory `moodle-qformat_stack -> stack`.) 

    Alternatively, get the code using git by running the following command in the top level folder of your Moodle install: 
    
        git clone https://github.com/maths/moodle-qformat_stack.git question/format/stack
2. Login to Moodle as the admin user and click on Notifications in the Site Administration panel.

There have been a number of changes between STACK 2 and STACK 3.  This feature has not been tested since STACK 4.0.  If you need to use this please contact the developers.  Also, see the [notes on the importer](../Moodle/Import_Export.md) before using it.
