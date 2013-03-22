# Question blocks

_This is an outline for a feature currently under development for STACK 3.0._

## Introduction ##

Question blocks are a feature that have been strongly requested to add flexibility to STACK
questions by adding functional structures, i.e. conditional inclusion
<http://stack.bham.ac.uk/live/mod/forum/discuss.php?d=153>.

More maximum flexibility, blocks can be nested and conditionally evaluated.
A body of CAStext is then repeatedly processed until all blocks have been interpreted into CAStext.
This is should be applied to all CAStext parts of the question.

## General Syntax ##

To avoid issues with the rich text editors used in Moodle we use a simple syntax not too 
different from the syntax used in input and output components:

    [[ block_type param1='value1' param2="value2" ... paramN="valueN" ]]
    some content
    [[/ block_type ]]

The syntax is quite similar to XML and includes [[ emptyblocks /]].

## Conditional blocks ##

The common **if** statement would be written:

    [[ if test='some_CAS_expression_evaluating_to_true_or_false' ]]
    The expression seems to be true
    [[/ if ]]

There is no else or else-if functionality as they would make the syntax rather difficult to evaluate.

## Foreach loop ##

Foreach blocks iterate over lists or sets and repeat their content redefining variables for each repetition.

    [[ foreach x="[1,2,3]" ]]{#x#} [[/ foreach ]]

You may have multiple variables and they will be iterated over in sync and the variables may also come from Maxima.
Should one of the lists or set be shorter/smaller the iteration will stop when the first one reaches end.

    [[ foreach x="[1,2,3]" y="makelist(x^2,x,4)" ]] ({#x#},{#y#}) [[/ foreach ]]

Because the foreach block needs to evaluate the lists/sets before it may do the iteration, using foreach blocks 
will require one additional cas evaluation for each level of foreach blocks.

## Development ##

### Block development ###

A block will be given access to the castext in form of a tree not unlike a DOM-tree. The block may parse parameters 
from that tree and add them to the CASsession to be evaluated. The block may forbid the evaluation of its contents
at this pass of the CAStext evaluation.

When the CASsession has been evaluated the block may use the evaluated variables and modify the tree as it seems 
fit, after this the block may request that the CAStext be re-evaluated.

Once all the blocks present in the CAStext are happy and do not want to re-evaluate the CAStext again they will get 
a chance to clean up. Thus making it possible for blocks to be visible only in the evalution phase and dissappear
just before the instantiation is complete.


Development is happening at [aharjula/moodle-qtype_stack](https://github.com/aharjula/moodle-qtype_stack/)
