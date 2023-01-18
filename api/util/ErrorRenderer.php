<?php

namespace api\util;

use Slim\Interfaces\ErrorRendererInterface;

class ErrorRenderer implements ErrorRendererInterface
{
    public function __invoke(\Throwable $exception, bool $displayErrorDetails): string
    {
        return json_encode(array(
            'message' => $exception->getMessage()
        ));
    }

}
