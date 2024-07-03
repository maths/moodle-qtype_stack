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
// @copyright  2023 RWTH Aachen
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

require_once('../config.php');
require_once(__DIR__ . '../../emulation/MoodleEmulation.php');
// Required to pass Moodle code check. Uses emulation stub.
require_login();
use api\controller\GradingController;
use api\controller\RenderController;
use api\controller\TestController;
use api\controller\ValidationController;
use api\controller\DownloadController;
use api\util\ErrorRenderer;
use Slim\Factory\AppFactory;

require(__DIR__ . '/../vendor/autoload.php');

error_reporting(0);

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$errormiddleware = $app->addErrorMiddleware(false, true, true);
$errorhandler = $errormiddleware->getDefaultErrorHandler();
$errorhandler->forceContentType("application/json");
$errorhandler->registerErrorRenderer('application/json', ErrorRenderer::class);
$app->post('/render', RenderController::class);
$app->post('/test', TestController::class);
$app->post('/grade', GradingController::class);
$app->post('/validate', ValidationController::class);
$app->post('/download', DownloadController::class);

$app->run();
