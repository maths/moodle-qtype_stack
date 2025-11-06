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
 * This page presents the user with a selection of questions to try.
 *
 * @package    qtype_stack
 * @copyright  2025 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require_once('../config.php');
require_once(__DIR__ . '../../emulation/MoodleEmulation.php');
require_once(__DIR__ . '/../../stack/questionlibrary.class.php');
// Required to pass Moodle code check. Uses emulation stub.
require_login();
$files = stack_question_library::get_file_list('../../samplequestions/stackdemo/*');

$questions = [];
foreach ($files->children as $file) {
    if (!$file->isdirectory) {
        $question = new StdClass();
        $questiondata = file_get_contents('../../samplequestions/' . $file->path);
        $questionobject = simplexml_load_string($questiondata)->question;
        $question->definition = $questiondata;
        $question->name = (string) $questionobject->name->text;
        $question->seeds = $questionobject->deployedseed;
        $questions[] = $question;
    }
}
// @codingStandardsIgnoreStart
?>
<html>
  <head>
    <? readfile(__DIR__ . '/stackhead.html') ?>
  </head>
  <body>
    <script src="stackshared.js"></script>
    <script>
    let questions = [];
    let page = 0;
    let seed = null;

    $(document).ready(function () {
        goToPage(page);
    });

    /**
     * Go to internal page and update sidebar, etc.
     *
     * @param page - string - name of page
     */
    function goToPage(targetPage) {
        displayType = SAMPLEDISPLAY;
        page = targetPage;
        if (Object.keys(questions[page].seeds).length > 1) {
          seed = 0;
          document.getElementById('stackapi_variant').style.display = 'inline';
        } else {
          seed = null;
          document.getElementById('stackapi_variant').style.display = 'none';
        }
        send();
        $('.sidebar .active').removeClass('active');
        $('.sidebar #' + page + '-stackapi-nav').addClass('active');
    }

    // Create data for call to API.
    function collectData() {
        const res = {
            questionDefinition: questions[page].definition,
            answers: collectAnswer(),
            seed: (seed !== null) ? Number(questions[page].seeds[seed]) : null,
            renderInputs: inputPrefix,
        };
        return res;
    }

    function advanceVariant() {
      seed++;
      if (seed >= Object.keys(questions[page].seeds).length) {
        seed = 0;
      }
      send();
    }

    function toggleAnswer(button) {
      const element = document.getElementById('stackapi_correct')
      const status = element.style.display;
      if (status === 'block') {
        element.style.display = 'none';
        button.value = 'Display Correct Answers';
      } else {
        element.style.display = 'block';
        button.value = 'Hide Correct Answers';
      }
    }

    </script>
    <div class="container-fluid que stack">
      <div>
        <div>
          <a href="https://stack-assessment.org/" class="nav-link">
            <span style="display: flex; align-items: center; font-size: 20px">
              <span style="display: flex; align-items: center;">
                <img src="logo_large.png" style="height: 50px;">
                <span style="font-size: 50px;"><b>STACK API demonstration</b></span>
              </span>
              &nbsp;| Online assessment
            </span>
          </a>
        </div>
        <br>
        <div class="col-lg-9">
          <p>
            STACK is the world-leading open-source online assessment system for mathematics and STEM.
          </p>
          <p>
            This page allows you to try some STACK questions. Click on the name of a question in the menu to view it.
          </p>
          <p>
            Answers will be validated as you enter them. Click 'Submit Answers' to have them assessed.
          </p>
          <p>
            STACK questions can have random variants. If these are available for a question, you can click 'Next Variant' to see another.
          </p>
          <p>
            STACK is also available for direct integration in Moodle, ILIAS and through LTI.
            For more information visit <a href="https://stack-assessment.org/">the STACK community page</a>.
          </p>
          <p>
            There is also a <a href="/stack.php">library of STACK questions</a> on this demo site.
          </p>
          <hr>
        </div>
        <div>
          <div class='sidebar'>
            <? foreach($questions as $key => $question): ?>
              <a id='<?=$key?>-stackapi-nav' href='#<?=$key?>' onclick="event.preventDefault();goToPage(<?=$key?>, null)"><?=$question->name?></a>
            <? endforeach ?>
          </div>
          <div class="main-content">
            <br>
            <div class="col-lg-8">
              <div id='errors'></div>
              <h1 id="stackapi_name"></h1>
              <br>
              <div id="stackapi_qtext">
                <h1 id="stackapi_name"></h1>
                <br>
                <div id="output" class="formulation"></div>
                <br>
                <input type="button" onclick="answer()" class="btn btn-primary noninfo" value="Submit Answers"/>
                <input type="button" onclick="toggleAnswer(this)" class="btn btn-primary noninfo" value="Display Correct Answers"/>
                <input id="stackapi_variant" type="button" onclick="advanceVariant()" class="btn btn-primary" value="Next Variant"/>
                <span id="stackapi_spinner" class="spinner-border text-primary align-middle" role="status" style="margin-left: 10px;">
                  <span class="sr-only">Loading...</span>
                </span>
                <div id="stackapi_validity" style="color:darkred"></div>
              </div>
              <br>
              <div id="stackapi_combinedfeedback" class="feedback col-lg-8" style="display: none">
                <div id="specificfeedback"></div>
                <div id="generalfeedback"></div>
              </div>
              <div id="stackapi_correct" style="display: none">
                <div class="noninfo">
                  <h2>Correct answers:</h2>
                  <div id="formatcorrectresponse" class="feedback"></div>
                </div>
              </div>
            </div>
            <div class="col-lg-9">
              <hr />
              <p style="font-size: 0.875em;color:gray;">
                The STACK source code, including this API, is Licensed under the GNU General Public, License Version 3.
                Documentation, sample questions and materials, are licensed under Creative Commons Attribution-ShareAlike 4.0 International.
                See the <a href="https://docs.stack-assessment.org/en/About/License/">STACK licence</a> page for full details.
              </p>
              <? readfile(__DIR__ . '/stackfooter.html') ?>
            </div>
          </div>
        </div>
        <br>
      </div>
    </div>

  </body>
  <script type="text/javascript">
    <?php
        // Create JSON array of questions.
        if(!empty($questions)) {
            echo "questions=".json_encode($questions).";";
        }
    ?>
  </script>
</html>

