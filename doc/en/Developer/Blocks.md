# Creating blocks and extensions to them

## Background ##

To allow castext to adapt to various situations based on the current state of the cas-variables we 
needed to add someway of conditionally rendering parts of the castext this led to development of
the castext-blocks. At the same time the need for displaying raw maxima syntax of variables meant 
that a new syntax element alongside the old "@var@" syntax was required. Also there was some 
problems in the old "@var@"-syntax as there was no way of knowing which @ was the start and which 
the end and a single misplaced @ could make a mess.

So a new syntax "{@var@}" and "{#var#}" was designed and [blocks](../Authoring/Question_blocks.md) 
added to it and for that syntax a new PEG-parser was built with 
[PHP-PEG](https://github.com/hafriedlander/php-peg). As the new parser generates a DOM-like tree
of the castext we now have simpler ways for operating on the castext and may build "blocks" that
do various things on the castext.

For now there only exists six blocks:

* latex-block handles the traditional "{@var@}" constructs.
* raw-block handles the new "{#var#}" constructs.
* the rest of the blocks use the new block syntax and can actually have child-nodes in the tree.
 * if-block modifies the blocks inside itself so that things they send to the cas only get 
   evaluated if the if-blocks condition is true.
 * define-block allows modification of cas variables "inline" in the midle of the castext.
 * foreach-block does iteration over lists/sets/collections and blocks the evaluation of its own
   contents before it gets to evaluate the things it needs to iterate on.
 * external-block ensures that its own contents are fully evaluated before it sends them to 
   a handler class to be processesm typically in external tools.

The latex- and raw-blocks are special in the sense that they are tightly coupled to the parser, so
adding new blocks that have similar syntactical elements would be difficult and require a large 
amount of work. On the other hand generic blocks following the block-syntax may be added freely
just by modifying couple of switch statements in the castext class. Similarilly handlers for 
the external-block may be added by modifying the external-block to include the new handler.


## How to build a new block ##

For many good reasons there does not exist a block that would act as a while-loop, but for 
educational purposes we will now build one. Suppose we want this to do whatever we would expect:

    [[ define i='0' /]]
    [[ do while='is(i<10)' ]]
     {#i#}
     [[ define i='i+1' /]]
    [[/ do ]]

What we need to do:

 1. Create an new class based on block.interface.php for this just copy foreach.class.php or
    if.class.php
 2. Extract and validate that "while"-attribute in functions 'extract_attributes', 
    'validate_extract_attributes' and 'validate'
 3. As we want to repeat the contents of this block as they are untill the condition fails we
    must block the evaluation of those contents. For this reason 'content_evaluation_context'
    must return false.
 4. The interesting actions happen in 'process_content':
    * Get your while attribute from the evaluated context and check it.
    * If false destroy this node by calling '$this->get_node()->destroy_node()' and return false
      to signal that all is done.
    * If true you want to extract the contents of this node to text in similar ways as the 
      foreach block does it and append '$this->get_node()->to_string()' to that. Then just
      replace the whole node with that text and return true signaling that we need a new round 
      of parsing and evaluation
         * So we copied the contents of this block infront of this block and turned the whole mess 
           to a text node that will not see anymore evaluation during this evaluation pass.
         * But we also requested a new pass that will parse that set of nodes and process them again.
         * Note that 'defines' do not dissappear before the last evaluation that is because of 
           their 'clear'-function. 
 5. Then just add your new block class to castext.class.php in three places:
    1. You will need to 'require_once' it.
    2. 'validation_recursion' will need to have a case for "do"
    3. And same for 'first_pass_recursion'
 6. And that is it, feel free to test this but note how you cause an maxima-evaluation during
    every iteration.


## How to build a handler for the external-block ##

TODO...
