<?php

function validatedata(array $data) {
    if (!array_key_exists('question', $data)) {
        printError('No question');
    }
    if (!array_key_exists('seed', $data)) {
        printError('Seed is required');
    }

    // Default values.
    $data['defaults'] = (array_key_exists('defaults', $data)) ? $data['defaults'] : null;
    $data['prefix'] = (array_key_exists('prefix', $data)) ? $data['prefix'] : '';
    $data['readOnly'] = (array_key_exists('readOnly', $data)) ? $data['readOnly'] : false;
    $data['feedback'] = (array_key_exists('feedback', $data)) ? $data['feedback'] : false;
    $data['score'] = (array_key_exists('score', $data)) ? $data['score'] : false;
    $data['answer'] = (array_key_exists('answer', $data)) ? $data['answer'] : [];
    $data['plots_protocol'] = (array_key_exists('plots_protocol', $data)) ? $data['plots_protocol'] : 'https';

    $GLOBALS['DOMAIN'] = $data['plots_protocol'] . '://' . $_SERVER['HTTP_HOST'];

    $answers = [];
    $prefixlength = strlen($data['prefix']);
    if ($prefixlength > 0) {
        foreach ($data['answer'] as $key => $value) {
            $noprefixkey = substr($key, $prefixlength);
            $answers[$noprefixkey] = $value;
        }
        $data['answer'] = $answers;
    }
    return $data;
}
