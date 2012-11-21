# Question blocks

_This is an outline for a feature currently under development for STACK 3.0._

## Introduction ##

Question blocks are a feature that have been strongly requested to add flexibility to STACK
questions by adding functional structures, i.e. conditional inclusion
<http://stack.bham.ac.uk/live/mod/forum/discuss.php?d=153>.

More maximum flexibility, blocks can be nested and conditionally evaluated.
A body of CAStext is then repeatedly processed until all blocks have been interpreted into CAStext.
This is currently applied to the question text and the worked solution.

## General Syntax ##

In anticipation of unforeseen extensions, we favour a generic format inspired by the Django templating system:

    {% block_type var_1 var_2 ... var_n %}
    some content
    {% end block_type %}

## Conditional blocks ##

The common **if** statement would be written:

    {% if @some_CAS_expression@ %}
    The expression seems to be true
    {% end if %}

An **else** sub-block can optionally be included:

    {% if @some_CAS_expression@ %}
    The expression seems to be true
    {% else %}
    not quite true
    {% end if %}

The **else if** construct is also supported, e.g:

    {% if @is(x=2)@ %}
       {% bold %}it is 2{% end bold %}
    {% else if @is(x=3)@ %}
       it is 3
    {% else %}
       {% if @is(x=4)@ %}
          {% bold %}is be 4{% end bold%}
       {% else if @is(x=5)@ %}
          it is 5
       {% else %}
          it is something else
       {% end if %}
    {% end if %}

## Development ##

Question block work is being committed to the STACK 2.1 branch
<http://stack.cvs.sourceforge.net/viewvc/stack/stack-dev/lib/ui/?pathrev=STACK2_1> of CVS. Current focus is on:

* Minimising number of CAS calls where possible, e.g. by evaluating all blocks at same level in one call.
* Integrating with CASText and probably lib/filters.
* Added block handling to potential response feedback.
