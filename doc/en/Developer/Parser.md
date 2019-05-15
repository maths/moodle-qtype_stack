# Building the parser


## Install node

We need to use node:

    apt-get install node npm

Inside `stack/stack/maximaparser` (or anywhere else), and not as root

    npm install pegjs 
    npm install phpegjs ts-pegjs

## Compile the parser

To compile the parser

    node gen.js

This should return `null null null` and create new parsers for STACK.

## Debugging the parser rules

    echo "\n" . $ast->debugPrint($ast->toString() . '     ') . "\n";
    while ($ast->callbackRecurse($process, true) !== true) {
        echo "\n" . $ast->debugPrint($ast->toString() . '     ') . "\n";
    }
    echo "\n" . $ast->debugPrint($ast->toString() . '     ') . "\n";
