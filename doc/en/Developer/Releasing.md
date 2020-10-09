# STACK release process notes

Note, these notes are designed for developers releasing a new version through the Moodle plugin database.  They are probably not useful for anyone else.

Release needs to be done for the whole "set".  Do we increment the numbers of all components, e.g. behaviours, or just the qtype_stack?

* for key releases, we do them all.
* for bug fixes just the question type.


## 1. Pre-release checks

Check 

* Readme.md
* Check docs 
 * development `history/track`.
 * Execute `doc/maintenance.php` to search for broken links etc.
* version.php
 * check both the Moodle versions, and the required number. (https://docs.moodle.org/dev/Releases)
 * `MATURITY_STABLE`?
* Check version numbers on stackmaxima.mac.
* Run unit tests.
* Run code checker.
* Commit all changes to git, e.g. "Update version number for the 4.3.6 release."

## 2. Create new tag with version name

E.g. "v4.3.6".

* Push to GitHub.
* Push tags to GitHub 
 * Tortoise git: pulldown from push
 * Unix: `git tag -a v4.3.6 -m "Update version number for the 4.3.6 release."`
 * Unix: `git push`
 * Unix: `git push --tags`

## 3. Moodle plugins database entry for the plugin

Add a new version to the Moodle plugins database entry for the plugin.

* If version number does not appear in the dropdown, then upload it from GitHub.
 
* Version information
* Upload zipfile
* GitHub
* Username = maths
* Choose appropriate plugin
* Choose tags
* Choose tag number
* Rename root directory +
* Fix README filename +
* Choose supported Moodle.

Then check updated information on the form.

(don't add "master" to branch info)
