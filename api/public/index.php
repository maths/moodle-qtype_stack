<?php

use api\controller\GradingController;
use api\controller\RenderController;
use api\controller\ValidationController;
use api\util\ErrorRenderer;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(0);

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(false, true, true);
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->registerErrorRenderer('application/json', ErrorRenderer::class);

$app->post('/render', RenderController::class);
$app->post('/grade', GradingController::class);
$app->post('/validate', ValidationController::class);

$app->run();
