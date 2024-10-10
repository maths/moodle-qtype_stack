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
require_once(__DIR__ . '/../../stack/questionlibrary.class.php');
// Required to pass Moodle code check. Uses emulation stub.
require_login();
?>
<html>
  <head>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/codemirror.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/addon/lint/lint.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="cors.php?name=sortable.min.css" />
    <style>
      .CodeMirror { border: 1px solid #ddd;}
      .CodeMirror {
        width: 100%;
        float: left;
        height: 400px;
      }
      .feedback {
        color: #7d5a29;
        background-color: #fcf2d4;
        border-radius: 4px;
        border: 1px solid #7d5a2933;
        display: inline-block;
        padding: 5px;
      }
      .validation {
        border-radius: 4px;
        border: 1px solid darkgrey;
        padding: 5px;
        display: inline-block;
      }
      .correct-answer {
        background-color: white;
        border-radius: 4px;
        border: 1px solid darkgrey;
        padding: 0px 5px 0px;
        display: inline-block;
      }
      a.nav-link:link, a.nav-link:visited, a.nav-link:hover, a.nav-link:active {
        color:black;
        text-decoration:none;
      }
      .stack-library-file-list {
    padding: 20px 0px 20px 0px;
    height: 400px;
    overflow-y: scroll;
    border: 1px solid lightblue;
    border-radius: 10px;
}
    </style>
    <script type="text/x-mathjax-config">
      MathJax.Hub.Config({
        tex2jax: {
          inlineMath: [['$', '$'], ['\\[','\\]'], ['\\(','\\)']],
          displayMath: [['$$', '$$']],
          processEscapes: true,
          skipTags: ["script","noscript","style","textarea","pre","code","button"]
        },
        showMathMenu: false
      })
    </script>
    <script
      type="text/javascript"
      src="//cdnjs.cloudflare.com/ajax/libs/mathjax/2.7.9/MathJax.js?config=TeX-AMS_HTML"
    >
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/js-yaml/3.10.0/js-yaml.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/codemirror.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/addon/lint/lint.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/addon/lint/yaml-lint.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.31.0/mode/yaml/yaml.js"></script>
    <script src="stackjsvle.js"></script>
  </head>
  <body>
    <script>
      const timeOutHandler = new Object();
      const inputPrefix = 'stackapi_input_';
      const feedbackPrefix = 'stackapi_fb_';
      const validationPrefix = 'stackapi_val_';

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

      // Get the different input elements by tag and return object with values.
      function collectAnswer() {
        const inputs = document.getElementsByTagName('input');
        const textareas = document.getElementsByTagName('textarea');
        const selects = document.getElementsByTagName('select');
        let res = {};
        res = processNodes(res, inputs);
        res = processNodes(res, textareas);
        res = processNodes(res, selects);
        return res;
      }

      // Return object of values of valid entries in an HTMLCollection.
      function processNodes(res, nodes) {
        for (let i = 0; i < nodes.length; i++) {
          const element = nodes[i];
          if (element.name.indexOf(inputPrefix) === 0 && element.name.indexOf('_val') === -1) {
            if (element.type === 'checkbox' || element.type === 'radio') {
              if (element.checked) {
                res[element.name.slice(inputPrefix.length)] = element.value;
              }
            } else {
              res[element.name.slice(inputPrefix.length)] = element.value;
            }
          }
        }
        return res;
      }

      // Display rendered question and solution.
      function send() {
        const http = new XMLHttpRequest();
        const url = window.location.origin + '/render';
        http.open("POST", url, true);
        http.setRequestHeader('Content-Type', 'application/json');
        http.onreadystatechange = function() {
          if(http.readyState == 4) {
            try {
              const json = JSON.parse(http.responseText);
              if (json.message) {
                document.getElementById('errors').innerText = json.message;
                return;
              } else {
                document.getElementById('errors').innerText = '';
              }
              renameIframeHolders();
              let question = json.questionrender;
              const inputs = json.questioninputs;
              let correctAnswers = '';
              // Show correct answers.
              for (const [name, input] of Object.entries(inputs)) {
                question = question.replace(`[[input:${name}]]`, input.render);
                question = question.replace(`[[validation:${name}]]`, `<span name='${validationPrefix + name}'></span>`);
                if (input.samplesolutionrender && name !== 'remember') {
                  // Display render of answer and matching user input to produce the answer.
                  correctAnswers += `<p>
                    <?php echo stack_string('teacheranswershow_mcq', '')?> \\[{${input.samplesolutionrender}}\\],
                    <?php echo stack_string('api_which_typed')?>: `;
                  for (const [name, solution] of Object.entries(input.samplesolution)) {
                    if (name.indexOf('_val') === -1) {
                      correctAnswers += `<span class='correct-answer'>${solution}</span>`;
                    }
                  }
                  correctAnswers += '.</p>';
                } else if (name !== 'remember') {
                  // For dropdowns, radio buttons, etc, only the correct option is displayed.
                  for (const solution of Object.values(input.samplesolution)) {
                    if (input.configuration.options) {
                      correctAnswers += `<p class='correct-answer'>${input.configuration.options[solution]}</p>`;
                    }
                  }
                }
              }
              // Convert Moodle plot filenames to API filenames.
              for (const [name, file] of Object.entries(json.questionassets)) {
                question = question.replace(name, `plots/${file}`);
                json.questionsamplesolutiontext = json.questionsamplesolutiontext.replace(name, `plots/${file}`);
                correctAnswers = correctAnswers.replace(name, `plots/${file}`);
              }
              question = replaceFeedbackTags(question);

              document.getElementById('output').innerHTML = question;
              // Only display results sections once question retrieved.
              document.getElementById('stackapi_qtext').style.display = 'block';
              document.getElementById('stackapi_correct').style.display = 'block';

              // Setup a validation call on inputs. Timeout length is reset if the input is updated
              // before the validation call is made.
              for (const inputName of Object.keys(inputs)) {
                const inputElements = document.querySelectorAll(`[name^=${inputPrefix + inputName}]`);
                for (const inputElement of Object.values(inputElements)) {
                  inputElement.oninput = (event) => {
                    const currentTimeout = timeOutHandler[event.target.id];
                    if (currentTimeout) {
                      window.clearTimeout(currentTimeout);
                    }
                    timeOutHandler[event.target.id] = window.setTimeout(validate.bind(null, event.target), 1000);
                  };
                }
              }
              let sampleText = json.questionsamplesolutiontext;
              if (sampleText) {
                sampleText = replaceFeedbackTags(sampleText);
                document.getElementById('generalfeedback').innerHTML = sampleText;
                document.getElementById('stackapi_generalfeedback').style.display = 'block';
              } else {
                // If the question is updated, there may no longer be general feedback.
                document.getElementById('stackapi_generalfeedback').style.display = 'none';
              }
              document.getElementById('stackapi_score').style.display = 'none';
              document.getElementById('stackapi_validity').innerText = '';
              const innerFeedback = document.getElementById('specificfeedback');
              innerFeedback.innerHTML = '';
              innerFeedback.classList.remove('feedback');
              document.getElementById('formatcorrectresponse').innerHTML = correctAnswers;
              createIframes(json.iframes);
              MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
            }
            catch(e) {
              document.getElementById('errors').innerText = http.responseText;
              return;
            }
          }
        };
        http.send(JSON.stringify(collectData()));
      }

      // Validate an input. Called a set amount of time after an input is last updated.
      function validate(element) {
        const http = new XMLHttpRequest();
        const url = window.location.origin + '/validate';
        http.open("POST", url, true);
        // Remove API prefix and subanswer id.
        const answerName = element.name.slice(15).split('_', 1)[0];
        http.setRequestHeader('Content-Type', 'application/json');
        http.onreadystatechange = function() {
          if(http.readyState == 4) {
            try {
              const json = JSON.parse(http.responseText);
              if (json.message) {
                document.getElementById('errors').innerText = json.message;
                return;
              } else {
                document.getElementById('errors').innerText = '';
              }
              renameIframeHolders();
              const validationHTML = json.validation;
              const element = document.getElementsByName(`${validationPrefix + answerName}`)[0];
              element.innerHTML = validationHTML;
              if (validationHTML) {
                element.classList.add('validation');
              } else {
                element.classList.remove('validation');
              }
              createIframes(json.iframes);
              MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
            }
            catch(e) {
              document.getElementById('errors').innerText = http.responseText;
              return;
            }
          }
        };

        const data = collectData();
        data.inputName = answerName;
        http.send(JSON.stringify(data));
      }

      // Submit answers.
      function answer() {
        const http = new XMLHttpRequest();
        const url = window.location.origin + '/grade';
        http.open("POST", url, true);

        if (!document.getElementById('output').innerText) {
          return;
        }

        http.setRequestHeader('Content-Type', 'application/json');
        http.onreadystatechange = function() {
          if(http.readyState == 4) {
            try {
              const json = JSON.parse(http.responseText);
              if (json.message) {
                document.getElementById('errors').innerText = json.message;
                return;
              } else {
                document.getElementById('errors').innerText = '';
              }
              if (!json.isgradable) {
                document.getElementById('stackapi_validity').innerText
                  = ' <?php echo stack_string('api_valid_all_parts')?>';
                return;
              }
              renameIframeHolders();
              document.getElementById('score').innerText
                = (json.score * json.scoreweights.total).toFixed(2) +
                ' <?php echo stack_string('api_out_of')?> ' + json.scoreweights.total;
              document.getElementById('stackapi_score').style.display = 'block';
              document.getElementById('response_summary').innerText = json.responsesummary;
              document.getElementById('stackapi_summary').style.display = 'block';
              const feedback = json.prts;
              const specificFeedbackElement = document.getElementById('specificfeedback');
              // Replace tags and plots in specific feedback and then display.
              if (json.specificfeedback) {
                for (const [name, file] of Object.entries(json.gradingassets)) {
                  json.specificfeedback = json.specificfeedback.replace(name, `plots/${file}`);
                }
                json.specificfeedback = replaceFeedbackTags(json.specificfeedback);
                specificFeedbackElement.innerHTML = json.specificfeedback;
                specificFeedbackElement.classList.add('feedback');
              } else {
                specificFeedbackElement.classList.remove('feedback');
              }
              // Replace plots in tagged feedback and then display.
              for (let [name, fb] of Object.entries(feedback)) {
                for (const [name, file] of Object.entries(json.gradingassets)) {
                  fb = fb.replace(name, `plots/${file}`);
                }
                const elements = document.getElementsByName(`${feedbackPrefix + name}`);
                if (elements.length > 0) {
                  const element = elements[0];
                  if (json.scores[name] !== undefined) {
                    fb = fb + `<div><?php echo stack_string('api_marks_sub')?>:
                      ${(json.scores[name] * json.scoreweights[name] * json.scoreweights.total).toFixed(2)}
                        / ${(json.scoreweights[name] * json.scoreweights.total).toFixed(2)}.</div>`;
                  }
                  element.innerHTML = fb;
                  if (fb) {
                    element.classList.add('feedback');
                  } else {
                    element.classList.remove('feedback');
                  }
                }
              }
              createIframes(json.iframes);
              MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
            }
            catch(e) {
              document.getElementById('errors').innerText = http.responseText;
              return;
            }
          }
        };
        // Clear previous answers and score.
        const specificFeedbackElement = document.getElementById('specificfeedback');
        specificFeedbackElement.innerHTML = "";
        specificFeedbackElement.classList.remove('feedback');
        document.getElementById('response_summary').innerText = "";
        document.getElementById('stackapi_summary').style.display = 'none';
        const inputElements = document.querySelectorAll(`[name^=${feedbackPrefix}]`);
        for (const inputElement of Object.values(inputElements)) {
          inputElement.innerHTML = "";
          inputElement.classList.remove('feedback');
        }
        document.getElementById('stackapi_score').style.display = 'none';
        document.getElementById('stackapi_validity').innerText = '';
        http.send(JSON.stringify(collectData()));
      }

      function download(filename, fileid) {
        const http = new XMLHttpRequest();
        const url = window.location.origin + '/download';
        http.open("POST", url, true);
        http.setRequestHeader('Content-Type', 'application/json');
        // Something funky going on with closures and callbacks. This seems
        // to be the easiest way to pass through the file details.
        http.filename = filename;
        http.fileid = fileid;
        http.onreadystatechange = function() {
          if(http.readyState == 4) {
            try {
              // Only download the file once. Replace call to download controller with link
              // to downloaded file.
              const blob = new Blob([http.responseText], {type: 'application/octet-binary', endings: 'native'});
              const selector = CSS.escape(`javascript\:download\(\'${http.filename}\'\, ${http.fileid}\)`)
              const linkElements = document.querySelectorAll(`a[href^=${selector}]`);

              const link = linkElements[0];
              link.setAttribute('href', URL.createObjectURL(blob));
              link.setAttribute('download', filename);
              link.click();
            }
            catch(e) {
              document.getElementById('errors').innerText = http.responseText;
              return;
            }
          }
        };
        const data = collectData();
        data.filename = filename;
        data.fileid = fileid;
        http.send(JSON.stringify(data));
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

      function renameIframeHolders() {
        // Each call to STACK restarts numbering of iframe holders so we need to rename
        // any old ones to make sure new iframes end up in the correct place.
        for (const iframe of document.querySelectorAll(`[id^=stack-iframe-holder]:not([id$=old]`)) {
          iframe.id = iframe.id + '_old';
        }
      }

      function createIframes (iframes) {
        for (const iframe of iframes) {
          create_iframe(...iframe);
        }
      }

      // Replace feedback tags in some text with an approproately named HTML div.
      function replaceFeedbackTags(text) {
        let result = text;
        const feedbackTags = text.match(/\[\[feedback:.*\]\]/g);
        if (feedbackTags) {
          for (const tag of feedbackTags) {
            // Part name is between '[[feedback:' and ']]'.
            result = result.replace(tag, `<div name='${feedbackPrefix + tag.slice(11, -2)}'></div>`);
          }
        }
        return result;
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
        holder.innerHTML = "<?php echo stack_string('api_q_select')?>: ";
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
        <?php echo stack_string('api_choose_q')?>:
        <?php
        $files = stack_question_library::get_file_list('../../samplequestions/*');
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
        <?php echo stack_string('api_local_file')?>:
        <input type="file" id="local-file" name="local-file" accept=".xml" onchange="getLocalQuestionFile(this.files[0])"/>
        <div id="stackapi_question_select_holder"></div>
        <h2><?php echo stack_string('api_q_xml')?></h2>
        <textarea id="xml" cols="100" rows="10"></textarea>
        <h2><?php echo stack_string('seedx', '')?> <input id="seed" type="number"></h2>
        <div>
          <input type="button" onclick="send()" class="btn btn-primary" value="<?php echo stack_string('api_display')?>"/>
          <input type="checkbox" id="readOnly" style="margin-left: 10px"/> <?php echo stack_string('api_read_only')?>
        </div>
        <div id='errors'></div>
        <div id="stackapi_qtext" class="col-lg-8" style="display: none">
          <h2><?php echo stack_string('questiontext')?>:</h2>
          <div id="output" class="formulation"></div>
          <div id="specificfeedback"></div>
          <br>
          <input type="button" onclick="answer()" class="btn btn-primary" value="<?php echo stack_string('api_submit')?>"/>
          <span id="stackapi_validity" style="color:darkred"></span>
        </div>
        <div id="stackapi_generalfeedback" class="col-lg-8" style="display: none">
          <h2><?php echo stack_string('generalfeedback')?>:</h2>
          <div id="generalfeedback" class="feedback"></div>
        </div>
        <h2 id="stackapi_score" style="display: none"><?php echo stack_string('score')?>: <span id="score"></span></h2>
        <div id="stackapi_summary" class="col-lg-10" style="display: none">
          <h2><?php echo stack_string('api_response')?>:</h2>
          <div id="response_summary" class="feedback"></div>
        </div>
        <div id="stackapi_correct" class="col-lg-10" style="display: none">
          <h2><?php echo stack_string('api_correct')?>:</h2>
          <div id="formatcorrectresponse" class="feedback"></div>
        </div>
      </div>
    </div>
    <br>

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

