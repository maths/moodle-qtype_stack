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

## Naming parser rules

We give each parser rule a name, numbered in the approximate appropriate order in which they need to be applied.

* 0-10 the mandatory stars for "f(x)(y) => f(x)*(y)" and syntactical candy like the logarithms
* 10-20 rules forbidding certain usages.
* 20-30 identification of typical syntax mistakes like "sin^2(x)".
* 30-40 rules forbidding certain usages, like calling functions at all.
* 40-50 various rules adding stars, insert stars in function calls and split variables and whatnot.
* 60-70 rules forbidding certain usages, e.g. forbid floats.
* 80-90 logic to check for things that have been done and deciding whether what was done was ok i.e. stars were inserted but not allowed in "syntax=true".

## Debugging the parser rules

    echo "\n" . $ast->debugPrint($ast->toString() . '     ') . "\n";
    while ($ast->callbackRecurse($process, true) !== true) {
        echo "\n" . $ast->debugPrint($ast->toString() . '     ') . "\n";
    }
    echo "\n" . $ast->debugPrint($ast->toString() . '     ') . "\n";

