# Maxima identifier census

The Maxima identifier census is a new part of STACK starting from version 4.3
the point of this census is to keep track of new Maxima features and their
presence in versions of Maxima. Basically, we can use some function and flag if
and only if its present in a reasonable number of recent Maxima versions and
we drop STACKs support of older versions.

In addition to helping developers identify features that are not necessarily
available in relevant Maxima versions the census data is also used to build
the security-map i.e. a list of identifiers that should not be modified,
accessed or called by students or teachers. The same list also describes safe
identifiers so that we can identify situations where function names are used as
variables or common constants are being redefined.


## The census and security files

Currently, the data can be found from JSON files under `/stack/cas/` the census
in its raw form is in `base-identifier-map.json` and the [security-map](Security_map.md) is In
`security-map.json`. The census file lists identifiers and their usage types
and the versions of Maxima those usages have been present in the documentation.
The security file gives meta-data about selected identifiers, that meta-data
typically consists of keys like `function`, `operator`, or `variable` that have
values of `s`, `f`, and `?` first one meaning that students can use that
identifier in that way and the second one meaning that no one can use that in
that way the last one signals that the identifier is not yet forbidden and that
teachers may allow students to use it if they so wish. Other meta-data tends to
focus on classification e.g. functions that can be used to apply or map other
function on values have their own meta-data flag `mapfunction`.

## Updating the files

The process of updating the census file is as follows:

 1. Acquire the source code of the new Maxima version you want to include in
    the census. For example 'maxima-5.42.0.tar.gz' and unpack it somewhere.

     ```
     cd /tmp/
     mkdir maximatemp
     cd maximatemp
     tar xvf ~/Downloads/maxima-5.42.0.tar.gz
     ```

 2. Extract the identifiers from the documentation that comes with that source,
    and store them to a file.

     ```
     cd /tmp/maximatemp/maxima-5.42.0/doc/info
     grep '^ \-\- .*: .*' -shI * | grep -v 'an error. To debug' > /tmp/maximatemp/identifiers-5.42.0.txt
     ```

 3. Use STACKs update script to read in that file. Make sure that you give
    the correct version number.

     ```
     cd $MOODLE
     php question/type/stack/cli/maximaidentifierupdater.php --version=5.42.0 --data=/tmp/maximatemp/identifiers-5.42.0.txt
     ```

 4. Also call the update script for the security map. The script will read
    the data updated in the previous step and include any new identifiers to
    the security map. Calling, this script should also be done if you manually
    modify the security-map as the script ensure that the map is in alphabetical
    order and indented in the expected way.
     
     ```
     cd $MOODLE
     php question/type/stack/cli/maximasecuritymapupdater.php
     ```

 5. Remember to commit the changes to those JSON files to the GIT.
