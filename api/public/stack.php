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

/**
 * Sample API usage showing loading of questions from directory, display and validation/grading.
 *
 * @package    qtype_stack
 * @copyright  2023 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require_once('../config.php');
require_once(__DIR__ . '../../emulation/MoodleEmulation.php');
require_once(__DIR__ . '/../../stack/questionlibrary.class.php');
// Required to pass Moodle code check. Uses emulation stub.
require_login();
// @codingStandardsIgnoreStart
?>
<html>
  <head>
    <? readfile(__DIR__ . '/stackhead.html') ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/codemirror.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/addon/lint/lint.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-yaml/3.10.0/js-yaml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/codemirror.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/addon/lint/lint.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/addon/lint/yaml-lint.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/mode/yaml/yaml.js"></script>
  </head>
  <body>
    <script type="text/javascript" src="stackshared.js"></script>
    <script>
      // Create data for call to API.
      function collectData() {
        const res = {
          questionDefinition: yamlEditor.getDoc().getValue(),
          answers: collectAnswer(),
          seed: parseInt(document.getElementById('seed').value),
          renderInputs : inputPrefix,
          readOnly: document.getElementById('readOnly').checked,
        };
        return res;
      }

      // Save contents of question editor locally.
      function saveState(key, value) {
        if (typeof(Storage) !== "undefined") {
          localStorage.setItem(key, value);
        }
      }

      // Load locally stored question on page refresh.
      function loadState(key) {
        if (typeof(Storage) !== "undefined") {
          return localStorage.getItem(key) || '';
        }
        return '';
      }

      function getQuestionFile(questionURL) {
        if (questionURL) {
          fetch(questionURL)
            .then(result => result.text())
            .then((result) => {
              createQuestionList(result);
            });
        }
      }

      function getLocalQuestionFile(filepath) {
        if (filepath) {
          var reader = new FileReader();
          reader.readAsText(filepath, "UTF-8");
          reader.onload = function (evt) {
            createQuestionList(evt.target.result);
          }
        }
      }

      function createQuestionList(fileContents) {
        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(fileContents, "text/xml");
        const selectQuestion = document.createElement("select");
        selectQuestion.setAttribute("onchange", "setQuestion(this.value)");
        selectQuestion.id = "stackapi_question_select";
        const holder = document.getElementById("stackapi_question_select_holder");
        holder.innerHTML = "Select a question: ";
        holder.appendChild(selectQuestion);
        let firstquestion = null
        for (const question of xmlDoc.getElementsByTagName("question")) {
          if (question.getAttribute('type').toLowerCase() === 'stack') {
            firstquestion = (firstquestion) ? firstquestion : question.outerHTML;
            const option = document.createElement("option");
            option.value = question.outerHTML;
            option.text = question.getElementsByTagName("name")[0].getElementsByTagName("text")[0].innerHTML;

            selectQuestion.appendChild(option);
          }
        }
        setQuestion(firstquestion);
      }

      function setQuestion(question) {
        yamlEditor.getDoc().setValue('<quiz>\n' + question + '\n</quiz>');
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
        Choose a STACK sample file:
        <?php
        $files = stack_question_library::get_file_list('../../samplequestions/stacklibrary/*');
        function render_directory($dirdetails) {
            echo '<div style="margin-left: 30px;">';
            foreach ($dirdetails as $file) {
                if (!$file->isdirectory) {
                    echo '<button class="btn btn-link library-file-link" type="button" onclick="getQuestionFile(\'cors.php?name='
                        . $file->path . '&question=true\')">' .
                        $file->label . '</button><br>';
                } else {
                    echo '<button class="btn btn-link" type="button" data-toggle="collapse" ' .
                      'data-target="#' . $file->divid . '" aria-expanded="false" aria-controls="' . $file->divid . '">' .
                      $file->label . '</button><br><div class="collapse" id="' . $file->divid . '">';
                    render_directory($file->children);
                    echo '</div>';
                }
            }
            echo '</div>';
        }
        echo '<div class="stack-library-file-list">';
        render_directory($files->children);
        echo '</div>';
        ?>
        Or select a file of your own:
        <input type="file" id="local-file" name="local-file" accept=".xml" onchange="getLocalQuestionFile(this.files[0])"/>
        <div id="stackapi_question_select_holder"></div>
        <h2>Question XML</h2>
        <textarea id="xml" cols="100" rows="10"></textarea>
        <h2>Seed <input id="seed" type="number"></h2>
        <div>
          <input type="button" onclick="send(); diff();" class="btn btn-primary" value="Display Question"/>
          <input type="checkbox" id="readOnly" style="margin-left: 10px"/> Read Only
        </div>
        <div id='errors'></div>
        <div id="stackapi_qtext" class="col-lg-8" style="display: none">
          <h2>Question text:</h2>
          <div id="output" class="formulation"></div>
          <div id="specificfeedback"></div>
          <br>
          <br>
          <input type="button" onclick="answer()" class="btn btn-primary" value="Submit Answers"/>
          <span id="stackapi_validity" style="color:darkred"></span>
        </div>
        <div id="stackapi_generalfeedback" class="col-lg-8" style="display: none">
          <h2>General feedback:</h2>
          <div id="generalfeedback" class="feedback"></div>
        </div>
        <h2 id="stackapi_score" style="display: none">Score: <span id="score"></span></h2>
        <div id="stackapi_summary" class="col-lg-10" style="display: none">
          <h2><?php echo stack_string('api_response')?>:</h2>
          <div id="response_summary" class="feedback"></div>
        </div>
        <div id="stackapi_correct" class="col-lg-10" style="display: none">
          <div class="noninfo">
            <h2>Response summary:</h2>
            <div id="formatcorrectresponse" class="feedback"></div>
          </div>
        </div>
        <div id="stackapi_difference" class="col-lg-10" style="display: none">
            <h2>YAML representation based on API defaults:</h2>
            <div id="difference" class="feedback" style="white-space: pre;"></div>
          </div>
        </div>
      </div>
    </div>
    <br>
    <? readfile(__DIR__ . '/stackfooter.html') ?>
  </body>
  <script>
    const yamlEditor = CodeMirror.fromTextArea(document.getElementById("xml"),
      {
        lineNumbers: true,
        mode: "xml",
        gutters: ["CodeMirror-lint-markers"],
        lint: true
      });
    yamlEditor.getDoc().on('change', function (cm) {
      saveState('xml', cm.getValue());
    });
    yamlEditor.getDoc().setValue(loadState('xml'));
  </script>
</html>

