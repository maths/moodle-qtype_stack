# STACK release process notes

Note, these notes are designed for developers releasing a new version through the Moodle plugin database.  They are probably not useful for anyone else.

Release needs to be done for the whole "set".  Do we increment the numbers of all components, e.g. behaviours, or just the qtype_stack?

* for key releases, we do them all.
* for bug fixes just the question type.

## 0. Code on github

The STACK code is stored on github, e.g. in [https://github.com/maths/moodle-qtype_stack](https://github.com/maths/moodle-qtype_stack). In general the code branches are arranged as follows.

* The `master` branch should always be either (i) an official release, (ii) contain minor but critical bug fixes, or (iii) contain updates to the docs.  The STACK documentation on [https://docs.stack-assessment.org/](https://docs.stack-assessment.org/) auto-builds from the master branch, and so it is normal to update the docs between official releases.
* The `dev` branch normally contains features which will in the next release.  The `dev` branch should normally work out of the box, but expect unfinished features and bugs!  
* Development of new features often takes place in a specific branch named to correspond to an issue in the [github issue tracker](https://github.com/maths/moodle-qtype_stack/issues), normally forked from `dev`.  E.g. if you raise [issue 802](https://github.com/maths/moodle-qtype_stack/issues/802) then code related to this can start life in a branch `iss802`.  If you put `#802` in the commit message then github will pick this up in the issue discussion.  New features can be merged back into `dev` before they are completely finished, but please do add documentation, unit tests etc. first and make sure we at least have consensus on the design!

Unless you want to discuss something confidential with the developers, please do raise issues on github!

## 1. Pre-release checks

### Check

* Readme.md
* Check docs 
 * [development history](Development_history.md) and [development track](Development_track.md) `history/track`.
 * Execute `php cli/answertests_docs.php` to update the static docs about answertests.
 * Execute `doc/maintenance.php` to search for broken links etc.
* Run `php cli/unicode_data_process.php` to update unicode mappings.
* Run `php cli/ast_test_generator.php` to confirm if auto-generated tests have not changed.
* Run Maxima unit tests of contributed packages by re-defining `stacklocation` and running `s_test_case.mac` in the sandbox.  E.g.

    stacklocation:"/var/www/html/m40/question/type/stack"$
    load("s_test_case.mac");

* Run PHP [unit tests](Unit_tests.md).
* Run code checker.
* If needed re-build the minified Javascript (e.g. `npx grunt --root=question/type/stack`).

### Check API

* Spin up the STACK API in Docker and check it still works with a selection of questions, including download and JSXGraph.
* Chances are it won't work because even the latest version of goemaxima is out of date.
* You will need to create a local up-to-date image of goemaxima:
  * Clone the goemaxima repo and create a folder stack/STACKVERSION e.g. `stack/2024072400` matching the latest STACK version.
  * Copy the stack/maxima folder from STACK into this new folder.
  * Create `maximalocal.mac.template` as described in `Adding_new_version.md` in goemaxima docs (or just copy from previous goemaxima version).
  * In `buildimage.sh` set `maximaver` and `sbclver` e.g. `maximaver="5.45.1" sbclver="2.2.6"`. (`maximaver` should match `maximalocal.mac.template`)
  * `./buildweb.sh` (You may need to install `go` first: `sudo snap install go --classic`).
  * `./buildimage.sh 2024072400` (If Docker struggles to fetch metadata `sudo vi ~/.docker/config.json` and change `credsStore` to `credStore`).
  * You should have now created a `goemaxima:2024072400-dev` image locally.
* Temporarily update STACK API locally:
  * Update maxima image in STACK API to `goemaxima:2024072400-dev` in `docker-compose.dev.yml`.
  * Update `stackmaximaversion` and `version` in `config.php` for the API to e.g. 2024072400.
* `docker compose -f docker-compose.dev.yml up`

### Version numbers

 * version.php
 * stackmaxima.mac
 * Update version numbers in the API in anticipation of a new Goemaxima image `api/config_samples.txt`
 * Update Goemaxima docker image versions in `api/docker/docker-compose.dev.yml` and  `api/docker/docker-compose.yml`
   E.g. see `https://hub.docker.com/r/mathinstitut/goemaxima` for latest versions.


 * Run `php cli/getversionstring.php` and add output to `doc/en/Installation/STACK_versions.md`.
 * Change to `MATURITY_STABLE` in version.php
 * Check both the Moodle versions, and the required number. (https://moodledev.io/general/releases)  Update `Installation/index.md`

Commit all changes to git, e.g. "Update version number for the 4.6.0 release."

## 2. Create new tag with version name

E.g. "v4.6.0".

* Push to GitHub.
* Push tags to GitHub 
 * Tortoise git: pulldown from push
 * Linux: `git tag -a v4.6.0 -m "Update version number for the 4.6.0 release."`
 * Linux: `git push`
 * Linux: `git push --tags`

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

### 4. Releasing a new verion of the API to take advantage of the new release

Prompt to update Geomaxima image.

