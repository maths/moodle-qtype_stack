# Question blocks

_This feature is currently under development for STACK 3.2._  We envisage expansion of these features in future developments.

## Introduction ##

Question blocks add flexibility to STACK questions by adding functional structures.

For maximum flexibility, blocks can be nested and conditionally evaluated.
A body of CAStext is then repeatedly processed until all blocks have been interpreted into CAStext.
This is should be applied to all CAStext parts of the question.

Note:  The parameters to blocks in the question body may **NOT** depend on the student's answers. This means that 
you cannot reveal an input block based on student input. But you may still adapt PRT-feedback as much as you want.

## General Syntax ##

To avoid issues with the rich text editors used in Moodle we use a simple syntax not too 
different from the syntax used in input and output components:

    [[ block_type param1="value1" param2='value2' ... paramN="valueN" ]]
    Some content.
    [[/ block_type ]]

The syntax is quite similar to XML and includes [[ emptyblocks /]].

## Conditional blocks ##

The common **if** statement is written as:

    [[ if test="some_CAS_expression_evaluating_to_true_or_false" ]]
    The expression seems to be true.
    [[/ if ]]

For example,

    [[ if test='oddp(rand(5))' ]]
    This is an odd block!
    [[/ if]]

There is no else or else-if functionality as they would make the syntax rather difficult to evaluate.
    
## Foreach loop ##

Foreach blocks iterate over lists or sets and repeat their content redefining variables for each repetition.

    [[ foreach x="[1,2,3]" ]]{#x#} [[/ foreach ]]

You may have multiple variables and they will be iterated over in sync and the variables may also come from Maxima.
Should one of the lists or set be shorter/smaller the iteration will stop when the first one reaches end.

    [[ foreach x="[1,2,3]" y="makelist(x^2,x,4)" ]] ({#x#},{#y#}) [[/ foreach ]]

Because the foreach block needs to evaluate the lists/sets before it can do the iteration, using foreach blocks 
will require one additional cas evaluation for each level of foreach blocks.

## Define block ##

The define block is a core component of the foreach block, but it may also be used elsewhere. Its function
is to change the value of a cas variable in the middle of castex. For example:

    [[ define x='1' /]] {#x#}, [[ define x='x+1' /]] {#x#}, [[ define x='x+1' /]] {#x#}

should print "1, 2, 3". You may define multiple variables in the same block and the order of define 
operations is from left to right so "[[ define a='1' b='a+1' c='a+b' /]] {#a#}, {#b#}, {#c#}" should
generate the same output.


## External block ##

The External block is a special block sending its contents to external tools for evaluation, typically, generating
images. All external blocks must define their type and may define additional parameters. For example the following
would produce an image of an equation:

    [[ external type="latex" template="basic" ]]\[\frac12\sin{{@f@}}\][[/ external ]]

While working with source-code for various tools you'll probably want to turn of the rich text editor and use 
plain text instead and make sure that you do not load and save the document in the rich text editor as that will
reformat it and add various line-breaks and paragraphs in all the wrong places.

Note: For various technical and security reasons external-blocks have been disabled by default. Should you want
to use them you will need to activate them in the settings on type by type basis and provide all the additional
software-requirements and configuration parameters they may need.


