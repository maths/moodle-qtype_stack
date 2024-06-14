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
      var requests = [];
      var noFeedbackArray = [];
      var noTestsArray = [];
      var upgradeIssueArray = [];
      var noDeployedSeedsArray = [];
      var failedTestsArray = [];
      var generalErrorArray = [];

      function send(filepath, questionxml) {
        const http = new XMLHttpRequest();
        requests.push(http);
        document.getElementById('bulktest-button').setAttribute('disabled', true);
        document.getElementById('bulktest-spinner').removeAttribute('hidden');
        const url = "http://localhost:3080/test";
        http.open("POST", url, true);
        http.setRequestHeader('Content-Type', 'application/json');
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
            const resultDiv = document.createElement("div");
            resultDiv.setAttribute('style', 'margin-left: 10px;');
            try {
              const json = JSON.parse(http.responseText);
              if (json.error) {
                resultDiv.innerHTML = '<p class="feedback failed">' + json.error + ' - JSON: ' + http.responseText + '</p>';
                const parentDivEl = document.getElementById(filepath);
                parentDivEl.appendChild(resultDiv);
                generalErrorArray.push(filepath);
              } else {
                let resultHtml = '<h3 class="question-title">' + json.name + '</h3>';
                resultDiv.setAttribute('id', json.name);

                resultHtml += (json.isgeneralfeedback) ? '' : '<p class="feedback"><?=stack_string('bulktestnogeneralfeedback')?></p>';
                resultHtml += (json.istests) ? '' : '<p class="feedback"><?=stack_string('bulktestnotests')?></p>';
                resultHtml += (json.israndomvariants && !json.isdeployedseeds) ? '<p class="feedback"><?=stack_string('bulktestnodeployedseeds')?></p>' : '';
                resultHtml += (json.israndomvariants && json.isdeployedseeds) ? '<div style="margin-left: 20px">' : '';
                for (seed in json.results) {
                  if (seed !== 'noseed') {
                    resultHtml += '<h5 class="seed"><?=stack_string('seedx', '')?>' + seed + '</h5>';
                  }
                  if (json.istests && json.results[seed].passes !== null) {
                    resultHtml += '<p class="feedback' + ((json.results[seed].fails === 0) ? ' passed' : ' failed') +  '">' + json.results[seed].passes + ' <?=stack_string('api_passes')?>, ' + json.results[seed].fails + ' <?=stack_string('api_failures')?>.</p>';
                    if ((json.results[seed].fails !== 0)) {
                      failedTestsArray.push({'name': json.name, 'seed': seed, 'filepath': json.filepath, 'passes': json.results[seed].passes, 'fails': json.results[seed].fails});
                      for (const testname in json.results[seed].outcomes) {
                        const outcome = json.results[seed].outcomes[testname];
                        if (!outcome.passed && !outcome.reason) {
                          resultHtml += '<p>' + testname + ' : ' + JSON.stringify(outcome.outcomes) + '</p>';
                        }
                      }
                    }
                  }
                  if (json.results[seed].messages) {
                    resultHtml += '<p class="feedback failed">' + json.results[seed].messages + '</p>';
                  }
                }
                resultHtml += (json.israndomvariants && json.isdeployedseeds) ? '</div>' : '';
                resultHtml += (json.messages) ? '<p class="feedback failed">' + json.messages + '</p>' : '';
                resultDiv.innerHTML = resultHtml;
                const parentDivEl = document.getElementById(json.filepath);
                parentDivEl.appendChild(resultDiv);
                parentDivEl.replaceChildren(...Array.from(parentDivEl.children).sort((a,b) => a.id.localeCompare(b.id)));

                const overallUpdate = [
                                  [!json.istests, noTestsArray],
                                  [!json.isgeneralfeedback, noFeedbackArray],
                                  [json.israndomvariants && !json.isdeployedseeds, noDeployedSeedsArray],
                                  [json.isupgradeerror, upgradeIssueArray],
                                  [json.message && !json.isupgradeerror, generalErrorArray],
                                ]
                for (const update of overallUpdate) {
                  if (update[0] === true) {
                    update[1].push({'name': json.name, 'filepath': json.filepath})
                  }
                }
              }
            } catch(e) {
              resultDiv.innerText = e.message + ' - JSON: ' + http.responseText;
              resultDiv.innerHTML += '<br><br>';
              document.getElementById('errors').appendChild(resultDiv);
              document.getElementById('errors').removeAttribute('hidden');
              generalErrorArray.push(filepath);
            }

            requests = requests.filter(req => req !== http);
            if (requests.length === 0) {
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
                  itemEl.innerHTML += (issue.seed !== undefined && issue.seed !== 'noseed') ? ' : <?=stack_string('seedx', '')?>' + issue.seed : '';
                  itemEl.innerHTML += (issue.passes !== undefined) ? ' - (' + issue.passes + ' <?=stack_string('api_passes')?>, ' + issue.fails + ' <?=stack_string('api_failures')?>)' : '';
                  listEl.appendChild(itemEl);
                }
                listEl.replaceChildren(...Array.from(listEl.children).sort((a,b) => a.innerHTML.localeCompare(b.innerHTML)));
                targetDiv.appendChild(listEl);
                document.getElementById('overall-result').innerHTML = (overallPass) ?
                    '<div class="feedback passed"><?=stack_string('stackInstall_testsuite_pass')?></div><br>' : '<div class="feedback failed"><?=stack_string('stackInstall_testsuite_fail')?></div><br>';
              }
            }
          }
        };
        http.send(JSON.stringify({'questionDefinition': questionxml, 'filepath': filepath}));
      }

      function getLocalQuestionFile(file) {
        if (file) {
          const reader = new FileReader();
          reader.readAsText(file, "UTF-8");
          reader.onload = function (evt) {
            sendQuestionsFromFile(file.webkitRelativePath, evt.target.result);
          }
        }
      }

      function testFolder() {
        document.getElementById('bulktest-button').setAttribute('disabled', true);
        document.getElementById('bulktest-spinner').removeAttribute('hidden');
        document.getElementById('overall-results').setAttribute('hidden', true);
        document.getElementById('errors').setAttribute('hidden', true);
        document.getElementById('errors').innerHTML = '<h1><?=stack_string('api_errors')?></h1><br>';
        document.getElementById('output').innerHTML = '';
        requests = [];
        noFeedbackArray = [];
        noTestsArray = [];
        upgradeIssueArray = [];
        noDeployedSeedsArray = [];
        failedTestsArray = [];
        generalErrorArray = [];
        let files = document.getElementById('local-folder').files;
        files = Array.from(files).sort((a,b) => a.webkitRelativePath.localeCompare(b.webkitRelativePath))
        for (const file of files) {
          if (file.type === 'application/xml' || file.type === 'text/xml')
            getLocalQuestionFile(file);
        }
      }

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
          <?=stack_string('api_choose_folder')?>:
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
        <h3 id="failed-tests-title"><?=stack_string('stackInstall_testsuite_failingtests')?></h3>
        <div id="failed-tests">
        </div>
        <h3 id="upgrade-fail-title"><?=stack_string('stackInstall_testsuite_failingupgrades')?></h3>
        <div id="upgrade-fail">
        </div>
        <h3 id="no-tests-title"><?=stack_string('stackInstall_testsuite_notests')?></h3>
        <div id="no-tests">
        </div>
        <h3 id="no-feedback-title"><?=stack_string('stackInstall_testsuite_nogeneralfeedback')?></h3>
        <div id="no-feedback">
        </div>
        <h3 id="no-deployed-seeds-title"><?=stack_string('stackInstall_testsuite_nodeployedseeds')?></h3>
        <div id="no-deployed-seeds">
        </div>
        <h3 id="general-error-title"><?=stack_string('api_general_errors')?></h3>
        <div id="general-error">
        </div>
      </div>
    <br>

  </body>
</html>

