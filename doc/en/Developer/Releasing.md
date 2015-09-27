# STACK release process notes.

Note, these notes are designed for developers releasing a new version through the Moodle plugin database.  They are probably not useful for anyone else.

Release needs to be done for the whole "set".  Do we increment the numbers of all components, e.g. behaviours, or just the qtype_stack?

* for key releases, we do them all.
* for bug fixes just the question type.


## 1. Pre-release checks.

Check 

* Readme.md
* version.php
 * check both the Moodle versions, and the required number. (https://docs.moodle.org/dev/Releases)
* check version numbers on stackmaxima.mac.
* check docs

* Commit all changes to git, e.g. "Update version number for the 3.4 release."

## 2. Run all unit tests.

## 3. Create new tag with version name.

E.g. "v3.4".

* push to github.
* push tags to github 
 * Tortoise git: pulldown from push
 * unix: `git push --tags`

## Moodle plugins database entry for the plugin.

Add a new version to the Moodle plugins database entry for the plugin.

* If version number does not appear in the dropdown, then upload it from github.
 
* Version information
* Upload zipfile
* Github
* Username = maths
* Choose appropriate plugin
* Choose tags
* Choose tag number
* Rename root directory +
* Fix README filename +
* Choose supported Moodle.

Then check updated information on the form.

(don't add "master" to branch info)
