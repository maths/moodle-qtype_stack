<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

// This script handles the various deploy/undeploy actions from questiontestrun.php.
//
// @copyright  2023 University of Edinburgh
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
require_once('../config.php');
require_once(__DIR__ . '../../emulation/MoodleEmulation.php');
// Required to pass Moodle code check. Uses emulation stub.
require_login();
?>
<html>
  <head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <style>
      .feedback {
        color: black;
        background-color: #fcf2d4;
        border-radius: 4px;
        border: 1px solid #7d5a2933;
        padding: 5px;
        margin-left: 10px;
        width: fit-content;
      }
      .passed {
        background-color: lightgreen;
      }
      .failed {
        background-color: pink;
      }
      .seed {
        color: darkblue;
      }
      .question-title {
        margin-top: 20px;
      }
      .path-title {
        color: grey;
      }
      a.nav-link:link, a.nav-link:visited, a.nav-link:hover, a.nav-link:active {
        color:black;
        text-decoration:none;
      }
    </style>
  </head>
  <body>
    <script>
      // Keep track of all requests sent to the server so we can check they're all complete.
      var requests = [];
      var filesToProcess = [];
      // Keep an array for which questions have failed for each reason for easy display of summary.
      var noFeedbackArray = [];
      var noTestsArray = [];
      var upgradeIssueArray = [];
      var noDeployedSeedsArray = [];
      var failedTestsArray = [];
      var generalErrorArray = [];

      /**
      * Send a question to the server for testing. Filepath required
      * for ordering the output by folder.
      */
      function send(filepath, questionxml) {
        const http = new XMLHttpRequest();
        requests.push(http);
        document.getElementById('bulktest-button').setAttribute('disabled', true);
        document.getElementById('bulktest-spinner').removeAttribute('hidden');
        const url = window.location.origin + '/test';
        http.open("POST", url, true);
        http.setRequestHeader('Content-Type', 'application/json');

        // Create nested <div>s with ids and titles representing the file structure in
        // preparation for displaying results.
        // Files being sent in sorted order so we don't need to sort again.
        const pathArray = filepath.split('/');
        let currentDiv = '';
        for (const part of pathArray) {
          let prevDiv = currentDiv;
          currentDiv += (currentDiv) ? '/' : '';
          currentDiv += part;
          if (!document.getElementById(currentDiv)) {
            const newDivEl = document.createElement("div");
            newDivEl.setAttribute('id', currentDiv);
            newDivEl.innerHTML = '<h5 class="path-title">' + currentDiv + '</h5>';
            if (prevDiv) {
              const prevDivEl = document.getElementById(prevDiv);
              newDivEl.setAttribute('style', 'margin-left: 10px;');
              prevDivEl.appendChild(newDivEl);
            } else {
              document.getElementById('output').appendChild(newDivEl);
            }
          }
        }
        http.onreadystatechange = function() {
          if(http.readyState == 4) {
            // Create div to show results of an individual question.
            const resultDiv = document.createElement("div");
            resultDiv.setAttribute('style', 'margin-left: 10px;');
            try {
              // If there's a JSON error, display output then this whole question has a problem somewhere.
              // Add to general erros and give up.
              const json = JSON.parse(http.responseText);
              if (json.message) {
                resultDiv.innerHTML = '<p class="feedback failed">' + json.error + ' - JSON: ' + http.responseText + '</p>';
                const parentDivEl = document.getElementById(filepath);
                parentDivEl.appendChild(resultDiv);
                generalErrorArray.push(filepath);
              } else {
                let resultHtml = '<h3 class="question-title">' + json.name + '</h3>';
                resultDiv.setAttribute('id', json.name);

                // Display issues based on returned flags.
                resultHtml += (json.isgeneralfeedback) ?
                    '' : '<p class="feedback"><?php echo stack_string('bulktestnogeneralfeedback')?></p>';
                resultHtml += (json.istests) ? '' : '<p class="feedback"><?php echo stack_string('bulktestnotests')?></p>';
                resultHtml += (json.israndomvariants && !json.isdeployedseeds) ?
                    '<p class="feedback"><?php echo stack_string('bulktestnodeployedseeds')?></p>' : '';
                resultHtml += (json.israndomvariants && json.isdeployedseeds) ?
                    '<div style="margin-left: 20px">' : ''; // Open div for seeds.
                for (seed in json.results) {
                  // If there are no random variants, there should be one result indexed as 'noseed'.
                  if (seed !== 'noseed') {
                    resultHtml += '<h5 class="seed"><?php echo stack_string('seedx', '')?>' + seed + '</h5>';
                  }
                  if (json.istests && json.results[seed].passes !== null) {
                    // If tests have been run, displays number of passes and fails.
                    resultHtml += '<p class="feedback' + ((json.results[seed].fails === 0) ? ' passed' :' failed') +  '">'
                        + json.results[seed].passes + ' <?php echo stack_string('api_passes')?>, '
                        + json.results[seed].fails + ' <?php echo stack_string('api_failures')?>.</p>';
                    if ((json.results[seed].fails !== 0) || json.results[seed].messages) {
                      failedTestsArray.push({'name': json.name, 'seed': seed, 'filepath': filepath,
                          'passes': json.results[seed].passes, 'fails': json.results[seed].fails,
                          'message': json.results[seed].messages});
                      for (const testname in json.results[seed].outcomes) {
                        const outcome = json.results[seed].outcomes[testname];
                        // Display outcomes of failed tests if available. There should be a reason if not.
                        // Reason will be displayed as part of messages.
                        if (!outcome.passed) {
                          if (!outcome.reason) {
                            resultHtml += '<p>' + testname + ' : ' + JSON.stringify(outcome.inputs) + '</p>';
                            resultHtml += '<p>' + testname + ' : ' + JSON.stringify(outcome.outcomes) + '</p>';
                          }
                        }
                      }
                    }
                  }
                  if (!json.istests && json.results[seed].passes == 1) {
                    resultHtml += '<p class="feedback passed">' + '<?php echo stack_string('defaulttestpass')?>' + '</p>';
                  }
                  // Display seed level messages - will be exceptions.
                  if (json.results[seed].messages) {
                    resultHtml += '<p class="feedback failed">' + json.results[seed].messages + '</p>';
                  }
                  // If we've got no tests but a failed test, then our fallback test of using the teacher's
                  // answer and expecting a score of 1 has failed.
                  if (!json.istests && json.results[seed].fails) {
                    failedTestsArray.push({'name': json.name, 'seed': seed, 'filepath': filepath,
                          'message': json.results[seed].messages});
                  }
                }
                resultHtml += (json.israndomvariants && json.isdeployedseeds) ? '</div>' : '';  // Close div for seeds.
                // Display question level messages. (Upgrade errors).
                resultHtml += (json.messages) ? '<p class="feedback failed">' + json.messages + '</p>' : '';
                resultDiv.innerHTML = resultHtml;
                // Append result to correct div then sort questions by name. (Calls are async so we may not
                // get the results back in the correct order).
                const parentDivEl = document.getElementById(filepath);
                parentDivEl.appendChild(resultDiv);
                parentDivEl.replaceChildren(...Array.from(parentDivEl.children).sort((a,b) => a.id.localeCompare(b.id)));

                // Update the lists of failed tests in each category.
                const overallUpdate = [
                          [!json.istests, noTestsArray],
                          [!json.isgeneralfeedback, noFeedbackArray],
                          [json.israndomvariants && !json.isdeployedseeds, noDeployedSeedsArray],
                          [json.isupgradeerror, upgradeIssueArray],
                          [json.message && !json.isupgradeerror, generalErrorArray],
                ]
                for (const update of overallUpdate) {
                  if (update[0] === true) {
                    update[1].push({'name': json.name, 'filepath': filepath})
                  }
                }
              }
            } catch(e) {
              // Something has gone very wrong.
              resultDiv.innerText = e.message + ' - JSON: ' + http.responseText;
              resultDiv.innerHTML += '<br><br>';
              document.getElementById('errors').appendChild(resultDiv);
              document.getElementById('errors').removeAttribute('hidden');
              generalErrorArray.push(filepath);
            }

            // Remove current request from pending array
            requests = requests.filter(req => req !== http);
            if (requests.length === 0 && filesToProcess.length === 0) {
              displayOverallResults();
            }
          }
        };
        http.send(JSON.stringify({'questionDefinition': questionxml}));
      }

      /**
       * Display the overall pass/fail message and the collections of failed
       * tests by category of failure. We need to have processed all the files
       * and got a response to all the requests before running this.
       *
       * @return void
       */
      function displayOverallResults() {
        // All done. Display final lists of failed questions by category.
        let overallPass = true;
        document.getElementById('bulktest-button').removeAttribute('disabled');
        document.getElementById('bulktest-spinner').setAttribute('hidden', true);
        document.getElementById('overall-results').removeAttribute('hidden');
        const displayUpdate = [
                [noTestsArray, 'no-tests'],
                [noFeedbackArray, 'no-feedback'],
                [upgradeIssueArray, 'upgrade-fail'],
                [noDeployedSeedsArray, 'no-deployed-seeds'],
                [failedTestsArray, 'failed-tests'],
                [generalErrorArray, 'general-error'],
        ]
        for (const update of displayUpdate) {
          const targetTitle = document.getElementById(update[1] + '-title')
          if (update[0].length === 0) {
            targetTitle.setAttribute('hidden', true);
            continue;
          }
          overallPass = false;
          targetTitle.removeAttribute('hidden');
          const targetDiv = document.getElementById(update[1]);
          targetDiv.innerHTML = '';
          const listEl = document.createElement('ul');
          for (const issue of update[0]) {
            const itemEl = document.createElement('li');
            itemEl.innerHTML = issue.filepath
            itemEl.innerHTML += (issue.name !== undefined) ? ' : ' + issue.name : '';
            itemEl.innerHTML += (issue.seed !== undefined && issue.seed !== 'noseed') ?
                ' : <?php echo stack_string('seedx', '')?>' + issue.seed : '';
            itemEl.innerHTML += (issue.passes !== undefined) ?
                ' - (' + issue.passes + ' <?php echo stack_string('api_passes')?>, ' +
                issue.fails + ' <?php echo stack_string('api_failures')?>)' : '';
            itemEl.innerHTML += (issue.message !== undefined) ? '<br>' + issue.message : '';
            listEl.appendChild(itemEl);
          }
          listEl.replaceChildren(...Array.from(listEl.children).sort((a,b) => a.innerHTML.localeCompare(b.innerHTML)));
          targetDiv.appendChild(listEl);
          document.getElementById('overall-result').innerHTML = (overallPass) ?
              '<div class="feedback passed"><?php echo stack_string('stackInstall_testsuite_pass')?></div><br>' :
              '<div class="feedback failed"><?php echo stack_string('stackInstall_testsuite_fail')?></div><br>';
        }
      }

      /**
       * Initialise output and sort selected files.
       * Keeps track of files processed so far to avoid race condition possibility
       * between the calls to the server and the reading/parsing of a long file.
       *
       * @return void
       */
      function testFolder() {
        let files = document.getElementById('local-folder').files;
        if (!files.length) {
          return;
        }
        document.getElementById('bulktest-button').setAttribute('disabled', true);
        document.getElementById('bulktest-spinner').removeAttribute('hidden');
        document.getElementById('overall-results').setAttribute('hidden', true);
        document.getElementById('errors').setAttribute('hidden', true);
        document.getElementById('errors').innerHTML = '<h1><?php echo stack_string('api_errors')?></h1><br>';
        document.getElementById('output').innerHTML = '';
        requests = [];
        filesToProcess = [];
        noFeedbackArray = [];
        noTestsArray = [];
        upgradeIssueArray = [];
        noDeployedSeedsArray = [];
        failedTestsArray = [];
        generalErrorArray = [];
        readingFile = true;
        files = Array.from(files).sort((a,b) => a.webkitRelativePath.localeCompare(b.webkitRelativePath));
        for (const file of files) {
          filesToProcess.push(file.webkitRelativePath);
        }
        for (const file of files) {
          if (file.type === 'application/xml' || file.type === 'text/xml') {
            getLocalQuestionFile(file);
          } else {
            filesToProcess = filesToProcess.filter(item => item !== file.webkitRelativePath);
          }
        }
        if (requests.length === 0 && filesToProcess.length === 0) {
          document.getElementById('bulktest-button').removeAttribute('disabled');
          document.getElementById('bulktest-spinner').setAttribute('hidden', true);
        }
      }

      /**
       * Read a file
       */
      function getLocalQuestionFile(file) {
        if (file) {
          const reader = new FileReader();
          reader.readAsText(file, "UTF-8");
          reader.onload = function (evt) {
            sendQuestionsFromFile(file.webkitRelativePath, evt.target.result);
            filesToProcess = filesToProcess.filter(item => item !== file.webkitRelativePath);
            // Maybe we're done. Check if so and display the results if appropriate.
            // This is unlikely but maybe we finished with a large file with no
            // STACK questions and so all the requests are already complete.
            if (requests.length === 0 && filesToProcess.length === 0) {
              displayOverallResults();
            }
          }
        }
      }

      /**
       * Parse an XML file, chop into stack questions and send test requests for each one.
       */
      function sendQuestionsFromFile(filepath, fileContents) {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(fileContents, "text/xml");
        const errorNode = xmlDoc.querySelector("parsererror");
        if (errorNode) {
          const resultDiv = document.createElement("div");
          resultDiv.innerHTML += '<p class="feedback failed">' + filepath + ': ' + errorNode.innerHTML + '</p><br><br>';
          document.getElementById('errors').appendChild(resultDiv);
          document.getElementById('errors').removeAttribute('hidden');
          generalErrorArray.push(filepath);
          return;
        }
        let questions = xmlDoc.getElementsByTagName("question");
        for (const question of questions) {
          if (question.getAttribute('type').toLowerCase() === 'stack') {
            send(filepath, '<quiz>\n' + question.outerHTML + '\n</quiz>');
          }
        }
      }

    </script>

    <div class="container-fluid que stack">
      <div class="vstack gap-3 ms-3 col-lg-8">
        <div>
          <a href="https://stack-assessment.org/" class="nav-link">
            <span style="display: flex; align-items: center; font-size: 20px">
              <span style="display: flex; align-items: center;">
                <img src="logo_large.png" style="height: 50px;">
                <span style="font-size: 50px;"><b>STACK </b></span>
              </span>
              &nbsp;| Online assessment
            </span>
          </a>
        </div>
          <?php echo stack_string('api_choose_folder')?>:
        <input type="file" id="local-folder" accept=".xml" name="local-folder" webkitdirectory directory multiple/>
        <div>
          <button id="bulktest-button" onclick="testFolder()"  class="btn btn-primary" type="button">
            <span id="bulktest-spinner" class="spinner-border spinner-border-sm" role="status" aria-hidden="true" hidden></span>
            Test
          </button>
        </div>
      </div>
      <br>
      <div id='output'>
      </div>
      <div id='errors' hidden>
      </div>
      <br><br>
      <div id="overall-results" hidden>
        <h1>Overall Results</h1>
        <div id="overall-result">
        </div>
        <h3 id="failed-tests-title"><?php echo stack_string('stackInstall_testsuite_failingtests')?></h3>
        <div id="failed-tests">
        </div>
        <h3 id="upgrade-fail-title"><?php echo stack_string('stackInstall_testsuite_failingupgrades')?></h3>
        <div id="upgrade-fail">
        </div>
        <h3 id="no-tests-title"><?php echo stack_string('stackInstall_testsuite_notests')?></h3>
        <div id="no-tests">
        </div>
        <h3 id="no-feedback-title"><?php echo stack_string('stackInstall_testsuite_nogeneralfeedback')?></h3>
        <div id="no-feedback">
        </div>
        <h3 id="no-deployed-seeds-title"><?php echo stack_string('stackInstall_testsuite_nodeployedseeds')?></h3>
        <div id="no-deployed-seeds">
        </div>
        <h3 id="general-error-title"><?php echo stack_string('api_general_errors')?></h3>
        <div id="general-error">
        </div>
      </div>
    <br>

  </body>
</html>

