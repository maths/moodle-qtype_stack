<?php

function parseInput()
{
    $data = file_get_contents("php://input");
    $parsed = json_decode($data, true);
    if ($parsed === null) {
        printError('no valid json');
    }
    return $parsed;
}

function printData($data)
{
    header('Content-Type: application/json');
    echo json_encode($data);
}

function printSuccess($data)
{
    printData([
        "error" => false,
        "message" => $data
    ]);
}

function printError($message)
{
    header("HTTP/1.0 500 Error");
    printData([
        "error" => true,
        "message" => $message
    ]);
    die();
}

function replace_plots($text)
{
    return str_replace('!ploturl!', $GLOBALS['DOMAIN'] . '/plots/', $text);
}
