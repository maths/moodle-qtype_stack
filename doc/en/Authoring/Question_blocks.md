# Question blocks

_This feature is currently under development for STACK 3.2._  We envisage expansion of these features in future developments.

## Introduction ##

Question blocks add flexibility to STACK questions by adding functional structures.

For maximum flexibility, blocks can be nested and conditionally evaluated.
A body of CAStext is then repeatedly processed until all blocks have been interpreted into CAStext.
This is should be applied to all CAStext parts of the question.

Note:  The parameters to blocks may **NOT** depend on the student's answers.  This means that you cannot reveal a block based on student input.

## General Syntax ##

To avoid issues with the rich text editors used in Moodle we use a simple syntax not too 
different from the syntax used in input and output components:

    [[ block_type param1="value1" param2="value2" ... paramN="valueN" ]]
    Some content.
    [[/ block_type ]]

The syntax is quite similar to XML and includes [[ emptyblocks /]].

## Conditional blocks ##

The common **if** statement is be written:

    [[ if test="some_CAS_expression_evaluating_to_true_or_false" ]]
    The expression seems to be true.
    [[/ if ]]

For example,

    [[ if test="oddp(rand(5))" ]]
    This is an odd block!
    [[/ if]]

There is no else or else-if functionality as they would make the syntax rather difficult to evaluate.
    
## Foreach loop ##

Foreach blocks iterate over lists or sets and repeat their content redefining variables for each repetition.

    [[ foreach x="[1,2,3]" ]]{#x#} [[/ foreach ]]

You may have multiple variables and they will be iterated over in sync and the variables may also come from Maxima.
Should one of the lists or set be shorter/smaller the iteration will stop when the first one reaches end.

    [[ foreach x="[1,2,3]" y="makelist(x^2,x,4)" ]] ({#x#},{#y#}) [[/ foreach ]]

Because the foreach block needs to evaluate the lists/sets before it may do the iteration, using foreach blocks 
will require one additional cas evaluation for each level of foreach blocks.

## External block ##

The External block is a special block sending its contents to external tools for evaluation, typically, generating
images. All external blocks must define their type and may define additional parameters. For example the following
would produce an image of an equation:

    [[ external type="latex" template="basic" ]]\[\frac12\sin{{@f@}}\][[/ external ]]

In the Moodle rich text editor you may want to wrap your external blocks in pre-tags to avoid reformatting and entity 
conversion.

## Development ##

### Block development ###

A block will be given access to the castext in the form of a tree not unlike a DOM-tree. The block may parse parameters 
from that tree and add them to the CASsession to be evaluated. The block may forbid the evaluation of its contents
at this pass of the CAStext evaluation.

When the CASsession has been evaluated the block may use the evaluated variables and modify the tree as it seems 
fit, after this the block may request that the CAStext be re-evaluated.

Once all the blocks present in the CAStext are happy and do not want to re-evaluate the CAStext again they will get 
a chance to clean up. Thus making it possible for blocks to be visible only in the evalution phase and dissappear
just before the instantiation is complete.


Development is happening at [aharjula/moodle-qtype_stack](https://github.com/aharjula/moodle-qtype_stack/)
