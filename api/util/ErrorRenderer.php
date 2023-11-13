<?php

namespace api\util;

use Slim\Interfaces\ErrorRendererInterface;

class ErrorRenderer implements ErrorRendererInterface
{
    public function __invoke(\Throwable $exception, bool $displayErrorDetails): string
    {
        $message = $exception instanceof \stack_exception ? $exception->getMessage() : "An Error occured while processing the question";
        return json_encode(array(
            'message' => $message
        ));
    }

}
