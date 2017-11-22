<?php

function parseinput() {
    $data = file_get_contents("php://input");
    $parsed = json_decode($data, true);
    if ($parsed === null) {
        printerror('no valid json');
    }
    return $parsed;
}

function printdata($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
}

function printsuccess($data) {
    printdata([
        "error" => false,
        "message" => $data
    ]);
}

function printerror($message) {
    header("HTTP/1.0 500 Error");
    printdata([
        "error" => true,
        "message" => $message
    ]);
    die();
}

function replace_plots($text) {
    return str_replace('!ploturl!', $GLOBALS['DOMAIN'] . '/plots/', $text);
}
