# PHP interface with the CAS

This document describes the design of the PHP interface to the CAS.  This interface was developed specifically to connect STACK to Maxima.

# High level objects

## CAS text

CAS text is literally "computer algebra active text".  This is documented [here](../Authoring/CASText.md).  Note, that when constructing CAS text it must be able to take a CAS session as an argument to the constructor.  In this way we can use lists of variables, such as [question variables](../Authoring/Variables.md) to provide values.

E.g. we might have :

    n:rand(5)+2;

in the question variables field.  The question is instantiated,  and then later we need to create some CAS text, e.g. feedback, in which we have :

    You were asked to find the integral of \((x+2)^{@n@}\) with respect to \(x\).  In fact ....

Here, we need the CAS text to be able to construct the text, with the previously evaluated value of `n`.  This need is also why the CAS session returns the *value* of the variables as well as a displayed form.  It is used here.

## CAS session

This class actually calls the CAS itself.  The basic ideas is to take a list of maxima commands, including assignments of the form.

    key:rawvalue

and executes this list in a single Maxima session.  The results of this are then captures and fed back into the CAS strings so we essentially have data in the form:

    key =>
        value
        display
        error

An important point here is that expressions (values) can refer to previous keys. This is one reason why we can't tie teachers down to a list of allowed functions.  They will be defining variable and function names.  We have implemented a lazy approach where the connection to the CAS is only made, and automatically made, when we ask for the values, display form or error terms for a variable.  And we don't generate display and value unless they are needed later.  E.g. intermediate values do not create LaTeX.


## Answer tests

The answer tests essentially compare two expressions.  These may accept an option, e.g. the number of significant figures.
Details of the current answer tests are available [elsewhere](../Authoring/Answer_Tests/index.md).  The result of an answer test should be

1. Boolean outcome, true or false,
2. errors,
3. feedback,
4. note.

# Other concepts

## Validity

There are a number of reasons why a CAS expression needs to be "valid".   These are

1. security,
2. stability,
3. error trapping,
4. pedagogic reasons, specific to a particular question.

## Single call PRTs and simplification

As of STACK 4.4 we make a single Maxima call to exectue an entire PRT. 
This reduces significantly the number of separate calls to maxima, which 
is a significant efficienty  boost for more complex questions.

Some answer tests rely on "unsimplified" expressions with a "what you see 
is what you get" approach.

This example illustrates the issue.  The teacher computes the answer to their 
question, e.g. find \(\int_{0}^{1} {\frac{{\left(1-x\right)}^4\cdot x^4}{x^2+1}} \mathrm{d}x\), 
with the Maxima code

    p1:int(x^4*(1-x)^4/(1+x^2),x,0,1);

using simplification (obviously) at the start of the quetion variables, and this simplified expression is used
by the PRT. The answer,  \(\frac{22}{7}-\pi\), is held internally in "simplified" form. 
The Maxima string output is `22/7-%pi` but internally the answer is actually the following 

    ((MPLUS SIMP) ((RAT SIMP) 22 7) ((MTIMES SIMP) -1 $%PI)) 

You can see the internal tree structure of a Maxima expression with the following code.

    (simp:true, p1:22/7-%pi, ?print(p1));

Rather than `22/7-%pi` the internal structure is really closer to this: `rat(22,7)+ (-1*%pi)`.

On the other hand, a student types in the expression `22/7-%pi` and we deal with this without simplification.
The internal Maxima expression is now 

    (simp:false, p1:22/7-%pi, ?print(p1));

which gives a different internal structure

    ((MPLUS) ((MQUOTIENT) 22 7) ((MMINUS) $%PI))

which might best be thought of as `22/7+-(%pi)` where `-` is now a function of a single argument.

In this example, the unsimplified `MQUOTIENT` and simplified `RAT SIMP` are not particularly problematic.  However, the difference between `-%pi` and `-1*%pi` is seriouly problematic.  Indeed, this kind of distinction is exactly what some
answer tests, e.g. `EqualComAss` and `CasEqual`, are designed to establish.  These tests will not work with a mix of simplified and unsimplified expressions, even if to the user they look completely identical!

The solution to this problem is to "rinse" away any maxima internal simplification by using the Maxima `string` function to return the expression to the top level which a user would expect to type.  This process corresponds to what happend in older versions of Maxima in which expressions were routinely passed between Maxima and PHP, with the string representation being used.

Some expressions (lists, matrices) are passed by reference in Maxima, so even if the teacher's answer is created without simplification in the first instance, when it is evaluated by the answer tests there is a risk of it becoming simplified when it is later compared by an answer test function.
