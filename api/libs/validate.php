<?php

function validateData(array $data)
{
    if (!array_key_exists('question', $data)) {
        printError('No question');
    }
    if (!array_key_exists('seed', $data)) {
        printError('Seed is required');
    }

  // default values
    $data['prefix'] = (array_key_exists('prefix', $data)) ? $data['prefix'] : '';
    $data['readOnly'] = (array_key_exists('readOnly', $data)) ? $data['readOnly'] : false;
    $data['feedback'] = (array_key_exists('feedback', $data)) ? $data['feedback'] : false;
    $data['score'] = (array_key_exists('score', $data)) ? $data['score'] : false;
    $data['answer'] = (array_key_exists('answer', $data)) ? $data['answer'] : [];
    $data['plots_protocol'] = (array_key_exists('plots_protocol', $data)) ? $data['plots_protocol'] : 'https';

    $GLOBALS['DOMAIN'] = $data['plots_protocol'] . '://' . $_SERVER['HTTP_HOST'];

    $answers = [];
    $prefix_length = strlen($data['prefix']);
    if ($prefix_length > 0) {
        foreach ($data['answer'] as $key => $value) {
            $no_prefix_key = substr($key, $prefix_length);
            $answers[$no_prefix_key] = $value;
        }
        $data['answer'] = $answers;
    }
    return $data;
}
