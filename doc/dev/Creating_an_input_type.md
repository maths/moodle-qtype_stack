# Creating a new Input type

## Background ##

To allow questions to be as flexible as possible, the types of input that can be
used to build questions are a type of plugin. You can make a new one and add
it to STACK if you wish. This document explains how.

## File layout ##

Your new input type will be a folder inside question/type/stack/stack/input/.
For the purpose of this example, we will suppose we are making an input type
called myinput. So, the folder will be question/type/stack/stack/input/myinput.

Inside there, you should have two files:

* myinput.class.php
* tests/myinput_test.php - not strictly requried, but of course you
want to write unit tests for your new code.

## myinput.class.php ##

In this file, you need to define a subclass of stack_input called
stack_myinput_input.

The methods you need to implement are well described by the PHPdoc comments on
the base class, so go and read those now. The key methods are:

* get_parameters_defaults - used by the question editing form.
* render - displays the input element as HTML.

## Finally ##

The best way to see how all this works is to have a look at how the inputs
that come with stack as standard are implemented. It should be easy to learn
by example.
