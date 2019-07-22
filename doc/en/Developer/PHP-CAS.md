# PHP interface with the CAS

This document describes the design of the PHP interface to the CAS.  While this interface was developed specifically to connect STACK to Maxima, it should be possible to factor this into other projects.

# High level objects

## CAS text

CAS text is literally "computer algebra active text".  This is documented [here](../Authoring/CASText.md).  It should be, in principle, possible to develop a "CASText filter" for Moodle.

Note, that when constructing CAS text it must be able to take a CAS session as an argument to the constructor.  In this way we can use lists of variables, such as [question variables](../Authoring/Variables.md) to provide values.

E.g. we might have :

    n:rand(5)+2;

in the question variables field.  The question is instantiated,  and then later we need to create some CAS text, e.g. feedback, in which we have :

    You were asked to find the integral of \((x+2)^{@n@}\) with respect to \(x\).  In fact ....

Here, we need the CAS text to be able to construct the text, with the previously evaluated value of `n`.  This need is also why the CAS session returns the *value* of the variables as well as a displayed form.  It is used here.

## CAS session

This class actually calls the CAS itself.

This class takes a list of CAS strings, including assignments of the form.

    key:rawvalue

and executes this list in a single Maxima session.  The results of this are then captures and fed back into the CAS strings so we have data in the form:

    key =>
        value
        display
        error

The value is the result of Maxima's "string" command, and this should be sufficient to renter the expression into Maxima later.  Notice the difference between the rawvalue and the value.

The display field contains the LaTeX displayed form of the variable.

An important point here is that expressions (values) can refer to previous keys. This is one reason why we can't tie teachers down to a list of allowed functions.  They will be defining variable and function names.

We have implemented a lazy approach where the connection to the CAS is only made, and automatically made, when we ask for the values, display form or error terms for a variable.


## Answer tests

The answer tests essentially compare two expressions.  These may accept an option, e.g. the number of significant figures.
Details of the current answer tests are available [elsewhere](../Authoring/Answer_tests.md).  The result of an answer test should be

1. Boolean outcome, true or false,
2. errors,
3. feedback,
4. note.


## CAS string

This is the most basic object.  The purpose of this class is to maximise system stability.  Validation and basic security checks takes place at this level.

# Other concepts

## Validity

There are a number of reasons why a CAS expression needs to be "valid".   These are

1. security,
2. stability,
3. error trapping,
4. pedagogic reasons, specific to a particular question.

### Security checks

It is important that students do not evaluate expressions such as the following with the CAS :

    system("rm /*");

for somewhat obvious reasons!  Hence, we need to restrict the availability of certain functions to users.

STACK "trusts" the teacher.  Therefore there are three levels of Maxima functions.

1. Forbidden.  No string containing anything which might look like this should ever get near the CAS.
2. Teacher.  Teacher's answers cannot contain these, but they are not a security risk.  Usually these have side effects within the Maxima code, e.g. setting a global variable which might cause problems later/elsewhere.
3. Student.  Student's expressions can only contain these.

### Stability

It is important to try to prevent generating Maxima errors, particularly syntax errors.  These cause havoc, because we don't have a proper API to Maxima.

However, students are apt to put in ill-formed expressions, e.g. `2x`, and it is obvious what they mean.  So, STACK has options such as "insert stars" which try to patch these things up.

### Pedagogic reasons

Some expressions will be rendered "invalid" because the student has done something which is not mathematically appropriate, i.e. permitted.  For example, they may have used floating-point numbers in an expression when the teacher wanted rational numbers.    See the section below on options.

## STACK Options

We need to ensure the current options are respected in the new CAS setup.  See [options](../Authoring/Options.md).

Note that some of the inputs enable options to be set there.  We might have a multi-part question in which part one forbids floating-point numbers, whereas the next part allows them.  Validity is a concept tied to the input.

# Code layout

## Install Options

The most difficult part of configuring the CAS is enabling Maxima to plot graphs.   At install time, the relevant local setting for this installation are transferred to the file :

    stacklocal/maximalocal.mac

The PHP code which creates this file sorts out backslashes and other vagaries...

## Efficiency

The connection to Maxima is slow, and is certainly the most important issue when trying to scale up the STACK.  The issue is that Maxima is quite slow to start.  The University of Manchester provided a mechanism to compile the Maxima code and this leads to increased server response.  See [Optimising Maxima](../CAS/Optimising_Maxima.md).

