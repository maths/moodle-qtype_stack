<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Private STACK API demo frontend.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

require_once(__DIR__ . '/../lib.php');

$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

if ($path === 'demo/questions' && $method === 'GET') {
    stack_private_demo_json(stack_private_demo_catalogue());
}

if ($path === 'cors.php' || $path === 'demo/cors.php') {
    stack_private_demo_cors_asset($_GET['name'] ?? '');
}

if (preg_match('#^demo/plot\.php/(.+)$#', $path, $matches) && $method === 'GET') {
    stack_private_demo_proxy_plot($matches[1]);
}

if (preg_match('#^demo/(render|validate|grade|download|diff)$#', $path, $matches) && $method === 'POST') {
    $route = $matches[1];
    $data = stack_private_demo_request_json();
    $payload = stack_private_demo_api_payload($data, $route);
    if ($route === 'diff') {
        $payload = ['questionDefinition' => $payload['questionDefinition']];
    }
    stack_private_demo_proxy_json($route, $payload);
}

if ($path === 'embed' && $method === 'GET') {
    require(__DIR__ . '/embed.php');
    die();
}

if ($path !== '' && $path !== 'index.php') {
    http_response_code(404);
    echo 'Not found';
    die();
}
?>
<!doctype html>
<html>
  <head>
    <meta charset="utf-8"/>
    <title>STACK Private API Demo</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="assets/api.css" />
    <style>
      .question-browser {
        max-width: 48rem;
        margin-bottom: 1rem;
      }
      .question-browser input,
      .question-browser select {
        width: 100%;
      }
      .question-browser select {
        min-height: 16rem;
      }
      .question-meta {
        color: #555;
        font-size: 0.875rem;
        margin-top: 0.35rem;
      }
      .private-demo-content {
        margin-left: 0;
        padding: 1px 16px;
      }
      .question-frame {
        border: 0;
        min-height: 46rem;
        width: 100%;
      }
    </style>
    <script src="private-demo.js"></script>
  </head>
  <body>
    <div class="container-fluid que stack">
      <div>
        <a href="https://stack-assessment.org/" class="nav-link">
          <span style="display: flex; align-items: center; font-size: 20px">
            <span style="display: flex; align-items: center;">
              <img src="assets/logo_large.png" style="height: 50px;" alt="STACK">
              <span style="font-size: 50px;"><b>STACK</b></span>
            </span>
            &nbsp;| Online assessment
          </span>
        </a>
      </div>
      <h2>Private API Demonstration</h2>
      <div class="col-lg-9">
        <p>
          This demo serves questions from the STACK library without sending question XML to the browser.
        </p>
        <hr>
      </div>
      <div id="errors"></div>
      <div class="question-browser col-lg-9">
        <input
          id="question-search"
          class="form-control"
          type="search"
          placeholder="Search by question name or filename"
          autocomplete="off"
        />
        <select id="question-list" class="form-control" size="12"></select>
        <div id="question-count" class="question-meta"></div>
      </div>
      <div>
        <div class="main-content private-demo-content">
          <br>
          <div class="col-lg-10">
            <iframe
              id="question-frame"
              class="question-frame"
              title="Selected STACK question"
            ></iframe>
          </div>
          <div class="col-lg-9">
            <hr />
            <p style="font-size: 0.875em;color:gray;">
              The STACK source code, including this API, is licensed under the GNU General Public, License Version 3.
              Documentation, sample questions and materials, are licensed under Creative Commons Attribution-ShareAlike 4.0 International.
            </p>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
