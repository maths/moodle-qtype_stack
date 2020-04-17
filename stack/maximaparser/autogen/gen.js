#!/usr/bin/env node

'use strict';

let fs = require('fs');
let pegjs = require('pegjs');
let phpegjs = require('phpegjs');
let tspegjs = require('ts-pegjs');


var parserCode = fs.readFile('./parser-grammar.pegjs','utf8',function
(err,data) {

   let PHPparser = pegjs.generate(data, {
       plugins: [phpegjs],
       cache: true,
       allowedStartRules: ["Root", "Equivline"],
       phpegjs: {parserNamespace: '', parserClassName: 'MP_Parser'}
   });

   fs.writeFile('parser.mbstring.php',PHPparser,'utf8',(err) => {console.log(err);});

   let TSparser = pegjs.generate(data, {
       output: 'source',
       cache: true,
       plugins: [tspegjs],
       allowedStartRules: ["Root", "Equivline"],
       tspegjs: {
         customHeader: 'import {MPNode, MPOperation, MPAtom, MPInteger, MPFloat, MPString, MPBoolean, MPIdentifier, MPComment, MPFunctionCall, MPGroup, MPSet, MPList, MPPrefixOp, MPPostfixOp, MPIndexing, MPIf, MPLoop, MPLoopBit, MPEvaluationFlag, MPStatement, MPRoot, MPAnnotation} from \'./MP_classes\';'
       }
   });

   fs.writeFile('maximaParser.ts', TSparser, 'utf8', (err) => {console.log(err);});

});

