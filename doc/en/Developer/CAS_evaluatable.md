# The family tree of cas_evaluatables.

As of 4.3 the CAS evaluation engine has been replaced by a more modular
version. Basically the old "cassession" class was replaced with a version
that takes in objects that implement the cas_evaluatable interface, and 
evaluates them. However, it does not extract values from the CAS unless
those objects implement additional interfaces that receive those values.

# The core interfaces

## cas_evaluatable

Anything that can generate a CAS-statement string, responds to a question
about its validity, can tell some backreference information to be displayed
in potential error messages, and provides a function that receives
information about the success or failure of the statement.

In general if one is only loading values into the CAS and does not need to
extract those particular values then something implementing cas_evaluatable
is probably the thing to use. And one can always add additional items to
the session and those can then extract values if need be.

In the old terms keyvals are closest to collections of cas_evaluatables
as they are rarely extracted on their own.

## cas_value_extractor and cas_latex_extractor

These two expand the cas_evaluatable interface by requiring that the object
provides means of receiving the value or its LaTeX representation. 
The obvious use for these are any items one needs to bring back from CAS,
note that while one can easily push every statement to CAS in containers
implementing these it is wasteful to do so unless you actually use those 
extracted values. What is wasted is bandwidth, processing power, and space
in the cache all of which directly affect response time and the upkeep
costs of the system.

From the old model the things that best match these are CASText, getting
the results of PRTs, and generating the LaTeX values for validation messages.

# The new class hierarchy of base level objects

As we rarely create new classes that implement those interfaces it is better
to list the existing ones so that one can seek for a ready made solution.
Note that not all of these will be in active use as other parts of the system
are brought forward.

## By source

In general cas_evaluatables are backed with an AST that the statement parser
has generated from question code or student input and has been processed by
multiple AST-filters and validators. But things are not always so, one might
have large blocks of code that have been prevalidated and cached as raw 
strings that one wants to directly load to the session, for example logic
code that evaluates a whole PRT or a forest of them. For the latter task there
exists a special class (`stack_secure_loader`) that does just that.

When dealing with student sourced material one tends to use 
the `stack_ast_container` class which provides complex maker functions that
ensure that a raw string gets parsed correctly and goes through key filters
in addition to whatever filter you may want to apply in the context of a particular input.
In some cases one may also use that class for teacher sourced material but in general 
`stack_ast_container_silent` is the better choice for such material as it
does not generate return values and typically teacher sourced content is used
in logic, and logic is not something that one needs to always show.

## Special cases

The old system had the concept of conditional casstrings in the block system
of CASText, as CASText is one of those things that will be redone, and in 
a way that it no longer needs conditional casstrings, we provide a separate
type of cas_evaluatable for this task thus freeing others from having to check
for conditions.

## The makers

There exists three different static maker functions in classes 
`stack_ast_container_silent` and `stack_ast_container`. The logic is identical
as they are only defined in former, but the result is different as they create 
objects of classes type. So call they through the class you wish to use.