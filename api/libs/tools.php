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

function replace_plots($text, $ploturl) {
    return str_replace('!ploturl!', $ploturl , $text);
}
