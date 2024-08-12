
# Extracting questions to make into PHP/BEHAT unit test questions

It's easier to create a question within Moodle and export XML than create a question within a test. The question then
needs to be converted into a format useable by the tests, however, and put in the helper.php file.

Insert the following code in question/bank/editquestion/question.php. Just inside the

```
} else if ($fromform = $mform->get_data()) {
```

and then attempt to save a question you are editing:

### Method 1 - Form data (assumes inputs in form ansX) used for behat tests:

```
echo "<br>
\$formform = new stdClass();<br>

\$formform->stackversion = '{$fromform->stackversion}';<br>
\$formform->name = '{$fromform->name}';<br>
\$formform->questionvariables = '{$fromform->questionvariables}';<br>
\$formform->questiontext = [<br>
    'text' => '";
echo htmlentities($fromform->questiontext['text']);
echo "',<br>
    'format' => '{$fromform->questiontext['format']}',<br>
    'itemid' => 0,<br>
];<br>
\$formform->questiondescription = [<br>
    'text' => '";
echo htmlentities($fromform->questiondescription['text']);
echo "',<br>
    'format' => '{$fromform->questiondescription['format']}',<br>
    'itemid' => 0,<br>
];<br>
\$formform->specificfeedback =  [<br>
    'text' => '";
echo htmlentities($fromform->specificfeedback['text']);
echo "',<br>
    'format' => '{$fromform->specificfeedback['format']}',<br>
    'itemid' => 0,<br>
];<br>
\$formform->generalfeedback =  [<br>
    'text' => '";
echo htmlentities($fromform->generalfeedback['text']);
echo "',<br>
    'format' => '{$fromform->generalfeedback['format']}',<br>
    'itemid' => 0,<br>
];<br>
\$formform->questionnote =  [<br>
    'text' => '";
echo htmlentities($fromform->questionnote['text']);
echo "',<br>
    'format' => '{$fromform->questionnote['format']}',<br>
    'itemid' => 0,<br>
];<br>
\$formform->penalty = {$fromform->penalty};<br>
\$formform->variantsselectionseed = '';<br>
\$formform->defaultmark = '{$fromform->defaultmark}';<br>";

// Create a version of this loop for each anser name variant e.g. ansX, elementX
// changing ans prefix to other names.
for ($i=1; $i <= 30; $i++) {
    if (!isset($fromform->{"ans" . $i . "type"})) {
        continue;
    }
    echo "
    \$formform->ans{$i}type = '" . $fromform->{"ans" . $i . "type"} . "';<br>
    \$formform->ans{$i}modelans = '" . $fromform->{"ans" . $i . "modelans"} . "';<br>
    \$formform->ans{$i}boxsize = '" . $fromform->{"ans" . $i . "boxsize"} . "';<br>
    \$formform->ans{$i}strictsyntax = '" . $fromform->{"ans" . $i . "strictsyntax"} . "';<br>
    \$formform->ans{$i}insertstars = '" . $fromform->{"ans" . $i . "insertstars"} . "';<br>
    \$formform->ans{$i}syntaxhint = '" . $fromform->{"ans" . $i . "syntaxhint"} . "';<br>
    \$formform->ans{$i}syntaxattribute = '" . $fromform->{"ans" . $i . "syntaxattribute"} . "';<br>
    \$formform->ans{$i}forbidwords = '" . $fromform->{"ans" . $i . "forbidwords"} . "';<br>
    \$formform->ans{$i}allowwords = '" . $fromform->{"ans" . $i . "allowwords"} . "';<br>
    \$formform->ans{$i}forbidfloat = '" . $fromform->{"ans" . $i . "forbidfloat"} . "';<br>
    \$formform->ans{$i}requirelowestterms = '" . $fromform->{"ans" . $i . "requirelowestterms"} . "';<br>
    \$formform->ans{$i}checkanswertype = '" . $fromform->{"ans" . $i . "checkanswertype"} . "';<br>
    \$formform->ans{$i}mustverify = '" . $fromform->{"ans" . $i . "mustverify"} . "';<br>
    \$formform->ans{$i}showvalidation = '" . $fromform->{"ans" . $i . "showvalidation"} . "';<br>
    \$formform->ans{$i}options = '" . $fromform->{"ans" . $i . "options"} . "';<br>";
}

echo"
\$formform->questionsimplify = '{$fromform->questionsimplify}';<br>
\$formform->assumepositive = '{$fromform->decimassumepositiveals}';<br>
\$formform->assumereal = '{$fromform->assumereal}';<br>
\$formform->prtcorrect = [<br>
    'text' => '";
echo htmlentities($fromform->prtcorrect['text']);
echo "',<br>
    'format' => '{$fromform->prtcorrect['format']}',<br>
    'itemid' => 0,<br>
];<br>
\$formform->prtpartiallycorrect = [<br>
    'text' => '";
echo htmlentities($fromform->prtpartiallycorrect['text']);
echo "',<br>
    'format' => '{$fromform->prtpartiallycorrect['format']}',<br>
    'itemid' => 0,<br>
];<br>
\$formform->prtincorrect = [<br>
    'text' => '";
echo htmlentities($fromform->prtincorrect['text']);
echo "',<br>
    'format' => '{$fromform->prtincorrect['format']}',<br>
    'itemid' => 0,<br>
];<br>
\$formform->decimals = '{$fromform->decimals}';<br>
\$formform->scientificnotation = '{$fromform->scientificnotation}';<br>
\$formform->multiplicationsign = '{$fromform->multiplicationsign}';<br>
\$formform->sqrtsign = '{$fromform->sqrtsign}';<br>
\$formform->complexno = '{$fromform->complexno}';<br>
\$formform->inversetrig = '{$fromform->inversetrig}';<br>
\$formform->logicsymbol = '{$fromform->logicsymbol}';<br>
\$formform->matrixparens = '{$fromform->matrixparens}';<br>
\$formform->qtype = 'stack';<br>
\$formform->numhints = {$fromform->numhints};<br>
\$formform->hint = [<br>";
foreach ($fromform->hint as $hint) {
    echo "['text' => '";
    echo htmlentities($hint['text']);
    echo "',<br>
    'format' => '{$hint['format']}'],<br>";
}
echo "];<br>";
for ($i=1; $i <= 30; $i++) {
    if (!isset($fromform->{"prt" . $i . "value"})) {
        continue;
    }
    echo "
    \$formform->prt{$i}value             = " . $fromform->{"prt" . $i . "value"} . ";<br>
    \$formform->prt{$i}feedbackstyle     = '" . $fromform->{"prt" . $i . "feedbackstyle"} . "';<br>
    \$formform->prt{$i}feedbackvariables = '" . $fromform->{"prt" . $i . "feedbackvariables"} . "';<br>
    \$formform->prt{$i}autosimplify      = '" . $fromform->{"prt" . $i . "autosimplify"} . "';<br>";

    for ($k=0; $k < count($fromform->{"prt" . $i . "answertest"}); $k++) {
        echo "
            \$formform->prt{$i}description[{$k}]         = '" . $fromform->{"prt" . $i . "description"}[$k] . "';<br>
            \$formform->prt{$i}sans[{$k}]                = '" . $fromform->{"prt" . $i . "sans"}[$k] . "';<br>
            \$formform->prt{$i}tans[{$k}]                = '" . $fromform->{"prt" . $i . "tans"}[$k] . "';<br>
            \$formform->prt{$i}answertest[{$k}]          = '" . $fromform->{"prt" . $i . "answertest"}[$k] . "';<br>
            \$formform->prt{$i}testoptions[{$k}]         = '" . $fromform->{"prt" . $i . "testoptions"}[$k] . "';<br>
            \$formform->prt{$i}quiet[{$k}]               = '" . $fromform->{"prt" . $i . "quiet"}[$k] . "';<br>
            \$formform->prt{$i}falsescore[{$k}]          = '" . $fromform->{"prt" . $i . "falsescore"}[$k] . "';<br>
            \$formform->prt{$i}falsescoremode[{$k}]      = '" . $fromform->{"prt" . $i . "falsescoremode"}[$k] . "';<br>
            \$formform->prt{$i}falsepenalty[{$k}]        = '" . $fromform->{"prt" . $i . "falsepenalty"}[$k] . "';<br>
            \$formform->prt{$i}falsefeedback[{$k}]       = [<br>
                'text' => '";
            echo htmlentities($fromform->{"prt" . $i . "falsefeedback"}[$k]['text']);
            echo "',<br>
                'format' => '" . $fromform->{"prt" . $i . "falsefeedback"}[$k]['format'] . "',<br>
                'itemid' => 0,<br>
            ];<br>
            \$formform->prt{$i}falseanswernote[{$k}]     = '" . $fromform->{"prt" . $i . "falseanswernote"}[$k] . "';<br>
            \$formform->prt{$i}falsenextnode[{$k}]       = '" . $fromform->{"prt" . $i . "falsenextnode"}[$k] . "';<br>
            \$formform->prt{$i}truescore[{$k}]           = '" . $fromform->{"prt" . $i . "truescore"}[$k] . "';<br>
            \$formform->prt{$i}truescoremode[{$k}]       = '" . $fromform->{"prt" . $i . "truescoremode"}[$k] . "';<br>
            \$formform->prt{$i}truepenalty[{$k}]         = '" . $fromform->{"prt" . $i . "truepenalty"}[$k] . "';<br>
            \$formform->prt{$i}truefeedback[{$k}]        = [<br>
                'text' => '";
            echo htmlentities($fromform->{"prt" . $i . "truefeedback"}[$k]['text']);
            echo "',<br>
                'format' => '" . $fromform->{"prt" . $i . "truefeedback"}[$k]['format'] . "',<br>
                'itemid' => 0,<br>
            ];<br>
            \$formform->prt{$i}trueanswernote[{$k}]      = '" . $fromform->{"prt" . $i . "trueanswernote"}[$k] . "';<br>
            \$formform->prt{$i}truenextnode[{$k}]        = '" . $fromform->{"prt" . $i . "truenextnode"}[$k] . "';<br>
        ";
    }
}
echo "return \$formform;<br>";
die;
```

### Method 2 - Question propeerties (assumes inputs in form ansX) used in unit tests:
(Not heavily tested.)

```
echo "<br>
    \$$formform = new stdClass();<br>

        \$q->stackversion = '{$fromform->stackversion}';<br>
        \$q->name = '{$fromform->name}';<br>
        \$q->questionvariables = '{$fromform->questionvariables}';<br>
        \$q->questiontext = '";
        echo htmlentities($fromform->questiontext['text']);
        echo "';<br>
        \$q->questiondescription = '";
        echo htmlentities($fromform->questiondescription['text']);
        echo "';<br>
        \$q->generalfeedback = '";
        echo htmlentities($fromform->generalfeedback['text']);
        echo "';<br>

        \$q->specificfeedback = '";
        echo htmlentities($fromform->specificfeedback['text']);
        echo "';<br>
        \$q->penalty = {$fromform->penalty};<br>";

        for ($i=1; $i <= 10; $i++) {
            if (!isset($fromform->{"ans" . $i . "type"})) {
                break;
            }
            echo "
            \$q->inputs['ans{$i}'] = stack_input_factory::make(<br>
                    '" . $fromform->{"ans" . $i . "type"} . "', 'ans{$i}', '{$fromform->ans1modelans}', null,<br>
                    array(<br>
                        'sameType'           => '" . $fromform->{"ans" . $i . "checkanswertype"} . "',<br>
                        'mustVerify'         => '" . $fromform->{"ans" . $i . "mustverify"} . "',<br>
                        'showValidation'     => '" . $fromform->{"ans" . $i . "showvalidation"} . "',<br>
                        'boxWidth'           => '" . $fromform->{"ans" . $i . "boxsize"} . "',<br>
                        'strictSyntax'       => '" . $fromform->{"ans" . $i . "strictsyntax"} . "',<br>
                        'syntaxAttribute'    => '" . $fromform->{"ans" . $i . "syntaxattribute"} . "',<br>
                        'insertStars'        => '" . $fromform->{"ans" . $i . "insertstars"} . "',<br>
                        'syntaxHint'         => '" . $fromform->{"ans" . $i . "syntaxhint"} . "',<br>
                        'forbidWords'        => '" . $fromform->{"ans" . $i . "forbidwords"} . "',<br>
                        'allowWords'         => '" . $fromform->{"ans" . $i . "allowwords"} . "',<br>
                        'forbidFloats'       => '" . $fromform->{"ans" . $i . "forbidfloat"} . "',<br>
                        'lowestTerms'        => '" . $fromform->{"ans" . $i . "requirelowestterms"} . "',<br>
                        'options'            => '" . $fromform->{"ans" . $i . "options"} . "',<br>
                ));<br>";
        }
        echo "\$q->options->set_option('decimals', '{$fromform->decimals}');<br>
        \$q->options->set_option('scientificnotation', '{$fromform->scientificnotation}');<br>
        \$q->options->set_option('multiplicationsign', '{$fromform->multiplicationsign}');<br>
        \$q->options->set_option('complexno', '{$fromform->complexno}');<br>
        \$q->options->set_option('inversetrig', '{$fromform->inversetrig}');<br>
        \$q->options->set_option('logicsymbol', '{$fromform->logicsymbol}');<br>
        \$q->options->set_option('matrixparens', '{$fromform->matrixparens}');<br>
        \$q->options->set_option('sqrtsign', (bool) {$fromform->sqrtsign});<br>
        \$q->options->set_option('simplify', (bool) {$fromform->questionsimplify});<br>
        \$q->options->set_option('assumepos', (bool) {$fromform->assumepositive});<br>
        \$q->options->set_option('assumereal', (bool) {$fromform->assumereal});<br>

        \$q->numhints = {$fromform->numhints};<br>
        \$q->hint = [<br>";
        foreach ($fromform->hint as $hint) {
            echo "['text' => '";
            echo htmlentities($hint['text']);
            echo "',<br>
            'format' => '{$hint['format']}'],<br>";
        }
        echo "];<br>";

        for ($i=1; $i <= 10; $i++) {
            if (!isset($fromform->{"prt" . $i . "value"})) {
                break;
            }
            echo "
            \$prt{$i} = new stdClass;<br>
            \$prt{$i}->name              = '" . ($i - 1) . "';<br>
            \$prt{$i}->id                = " . ($i - 1) . ";<br>
            \$prt{$i}->value             = " . $fromform->{"prt" . $i . "value"} . ";<br>
            \$prt{$i}->feedbackstyle     = '" . $fromform->{"prt" . $i . "feedbackstyle"} . "';<br>
            \$prt{$i}->feedbackvariables = '" . $fromform->{"prt" . $i . "feedbackvariables"} . "';<br>
            \$prt{$i}->firstnodename     = '" . $fromform->{"prt" . $i . "firstnodename"} . "';<br>
            \$prt{$i}->nodes             = [];<br>
            \$prt{$i}->autosimplify      = '" . $fromform->{"prt" . $i . "autosimplify"} . "';<br>";

            for ($k=0; $k < count($fromform->{"prt" . $i . "value"}); $k++) {
                echo "
                    \$node{$k} = new stdClass;<br>
                    \$node{$k}->id                  = '{$k}';<br>
                    \$node{$k}->nodename            = '{$k}';<br>
                    \$node{$k}->description         = '" . $fromform->{"prt" . $i . "description"}[$k] . "';<br>
                    \$node{$k}->sans                = '" . $fromform->{"prt" . $i . "sans"}[$k] . "';<br>
                    \$node{$k}->tans                = '" . $fromform->{"prt" . $i . "tans"}[$k] . "';<br>
                    \$node{$k}->answertest          = '" . $fromform->{"prt" . $i . "answertest"}[$k] . "';<br>
                    \$node{$k}->testoptions         = '" . $fromform->{"prt" . $i . "testoptions"}[$k] . "';<br>
                    \$node{$k}->quiet               = (bool) " . $fromform->{"prt" . $i . "quiet"}[$k] . ";<br>
                    \$node{$k}->falsescore          = '" . $fromform->{"prt" . $i . "falsescore"}[$k] . "';<br>
                    \$node{$k}->falsescoremode      = '" . $fromform->{"prt" . $i . "falsescoremode"}[$k] . "';<br>
                    \$node{$k}->falsepenalty        = '" . $fromform->{"prt" . $i . "falsepenalty"}[$k] . "';<br>
                    \$node{$k}->falsefeedback        = '";
                    echo htmlentities( $fromform->{"prt" . $i . "falsefeedback"}[$k]['text']);
                    echo "';<br>
                    \$node{$k}->falsefeedbackformat = '" . $fromform->{"prt" . $i . "falsefeedback"}[$k]['format'] . "';<br>
                    \$node{$k}->falseanswernote     = '" . $fromform->{"prt" . $i . "falseanswernote"}[$k] . "';<br>
                    \$node{$k}->falsenextnode       = '" . $fromform->{"prt" . $i . "falsenextnode"}[$k] . "';<br>
                    \$node{$k}->truescore           = '" . $fromform->{"prt" . $i . "truescore"}[$k] . "';<br>
                    \$node{$k}->truescoremode       = '" . $fromform->{"prt" . $i . "truescoremode"}[$k] . "';<br>
                    \$node{$k}->truepenalty         = '" . $fromform->{"prt" . $i . "truepenalty"}[$k] . "';<br>
                    \$node{$k}->truefeedback         = '";
                    echo htmlentities($fromform->{"prt" . $i . "truefeedback"}[$k]['text']);
                    echo "';<br>
                    \$node{$k}->truefeedbackformat  = '" . $fromform->{"prt" . $i . "truefeedback"}[$k]['format'] . "';<br>
                    \$node{$k}->trueanswernote      = '" . $fromform->{"prt" . $i . "trueanswernote"}[$k] . "';<br>
                    \$node{$k}->truenextnode        = '" . $fromform->{"prt" . $i . "truenextnode"}[$k] . "';<br>
                    \$prt{$i}->nodes[] = \$node{$k};<br>
                ";
            }
            echo "\$q->prts[\$prt{$i}->name] = new stack_potentialresponse_tree_lite(\$prt{$i}, \$prt{$i}->value, \$q);<br>";
        }
        echo "return \$q;<br>";
        die;
```
