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
 * Sample maths notes page with embedded private-demo STACK questions.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require_once('../config.php');
require_once(__DIR__ . '../../emulation/MoodleEmulation.php');
// Required to pass Moodle code check. Uses emulation stub.
require_login();
$pathquestion = 'Algebra-Refresher/01-Combinations-of-arithmetic-operations/AlgMap-1-1.xml';
$pathquestionseeds = 'all';
$idquestion = 'q_ae2a68e432c6de1b';
$idquestionseeds = '1239251428, 505204816';
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>Sample Maths Notes</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
    <style>
      body {
        background: #f6f7f9;
        color: #222;
      }
      .notes-page {
        max-width: 62rem;
        margin: 0 auto;
        padding: 2rem 1rem 4rem;
      }
      .notes-section {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 1.25rem;
        margin-bottom: 1rem;
      }
      .stack-embed {
        width: 100%;
        min-height: 31rem;
        border: 1px solid #cfd7df;
        border-radius: 6px;
        background: #fff;
      }
      code {
        color: #004f71;
      }
    </style>
  </head>
  <body>
    <main class="notes-page">
      <a href="index.php">Back</a>
      <h1>Integer Arithmetic: Sample Notes</h1>
      <p class="lead">
        These notes illustrate how a resource author can combine explanatory material with embedded STACK questions.
      </p>

      <section class="notes-section">
        <h2>Adding And Subtracting Integers</h2>
        <p>
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer non arcu vel justo tincidunt
          congue. Suspendisse potenti. Curabitur vitae lectus sed lorem fermentum cursus. Donec id
          risus at turpis dictum tincidunt.
        </p>
        <p>
          When working with signed numbers, it is useful to keep track of the operation and the sign of
          each number separately. Praesent commodo, sem at pulvinar laoreet, ipsum neque hendrerit
          lectus, sed posuere risus neque at augue.
        </p>
      </section>

      <section class="notes-section">
        <h2>Embedded By File Location</h2>
        <p>
          This embedded question is selected by a configured question-library-relative file location:
          <code><?=htmlspecialchars($pathquestion)?></code>.
        </p>
        <iframe
          class="stack-embed"
          title="STACK question embedded by file location"
          src="/embed?questionPath=<?=rawurlencode($pathquestion)?>&seeds=<?=rawurlencode($pathquestionseeds)?>"
        ></iframe>
      </section>

      <section class="notes-section">
        <h2>Embedded By Question ID</h2>
        <p>
          This embedded question is selected by the generated manifest id:
          <code><?=htmlspecialchars($idquestion)?></code>.
        </p>
        <iframe
          class="stack-embed"
          title="STACK question embedded by question id"
          src="/embed?questionId=<?=rawurlencode($idquestion)?>&seeds=<?=rawurlencode($idquestionseeds)?>"
        ></iframe>
      </section>

      <section class="notes-section">
        <h2>Further Notes</h2>
        <p>
          Mauris rhoncus, nunc at tincidunt vulputate, augue arcu tempor enim, ac vestibulum nisl
          risus non libero. Sed et massa sed lorem dictum facilisis. Aliquam erat volutpat.
        </p>
      </section>
    </main>
  </body>
</html>
