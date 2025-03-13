# CASText2 life-cycle

This document describes the life-cycle of CASText2 evaluation. Use this when
you need to decide what to do and when and want to use CASText to do it.

## Raw CASText

In the first phase of the life-cycle CASText is just a string that may or may
not contain CASText specific syntax, like blocks or injections. This is
the format authors use when writing question-text or feedback or even inline
CASText. If you are a developer of the STACK core you are probably only focused
on turning this to the next forms, or maybe you are using inline CASText to
build something and leave processing it further to tools that do it.

### Inline CASText

Inline CASText is not actually a part of the life-cycle it is just a way of
injecting raw CASText into places and it relies on compile time processing to
turn it to usable compiled CASText.

## Compiled CASText

The second phase of the life-cycle is the compiled form of the CASText, this is
the result of raw CASText being parsed and then turned into a CAS statement.
This happens by asking all the blocks present in the CASText to convert
the parts of the parse tree related to them into statements and joining those
together. Caching the compilation result is something we really want to do as
recompiling is pointless and for large documents can take quite a lot of time,
as the compilation result will always be the same for a given raw CASText in
the same context (context variance does not yet exist, but may become a thing)
i.e. same place in the question after same keyvals and so on, this caching does
not depend on the question variant and can be shared by all variants.

### Simplification and other pre-processing

The compiled CASText statement is then further processed as far as it can be
without actually evaluating it in CAS. This processing can lead into
a situation where the statement simplifies to a single string which
essentially means that the value of evaluating that statement is going to be
the same as the statement itself, in this special case it is unnecessary to
send that statement to the CAS for evaluation.

During this pre-processing it is also possible to extract long static string
values that do not take part in the logic from the statement and replace them
with shorter placeholders. These static strings are then stored separately 
and can be returned to their places after evaluation.

## Evaluated CASText

By evaluating the compiled CASText statement in the CAS we get evaluated
CASText which is essentially either a raw string value or a nested list
describing which blocks need to post-process things and which parameter values
they should use when doing that. This intermediate result is something that
the CAS cache can hold onto as an usable value for the next step.

## Rendered CASText

After the CASText has been evaluated and the evaluated CASText has had any
static strings restored to their places we ask all the blocks that are
declared in the evaluated value to do their post-processing. They turn
the nested list structure into a flat string which we may then present to
the user if need be. It is important to note that this post-processing may
not be side effect free and some blocks may for example inject scripts into
the page, so simply caching the end result of this rendering is not a viable
solution as it might miss those side effects. In general, it is best to apply
lazy evaluation to this and only post-process evaluated CASText into rendered
CASText if the rendering actually needs to be displayed, while the rendering
is often the cheapest operations of this whole life cycle it will always save
some resources.


# Specific notes

 1. If you are not going to render something, why evaluate it at all? This
    might be something that comes to mind, especially related to PRTs,
    the primary reason being that the CAS-cache can efficiently hold onto
    those evaluated CASText segments and if the only difference between
    the evaluated CASSessions is it necessary to generate two different
    sessions to cache? Note that that caching would happen both at
    CAS-cache database table and in the parser cache for the result. Also
    note that at the point where you would need to know whether you are to
    actually render things to decide whether to evaluate them it might not
    be easily knowable whether to do so, and minimising branching is always
    a good idea.

 2. Some blocks need additional context information to be evaluated e.g. 
    the `[[lang]]`-block needs a special variable to be set to the language
    selected for presentation. Do not forget to set such things if you use
    such blocks.
