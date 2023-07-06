# Maxima and computer algebra use in STACK

STACK uses the computer algebra system (CAS) [Maxima](Maxima.md) and a graphical desktop interface like [wxMaxima](http://andrejv.github.com/wxmaxima/) can be helpful for ofline editing, including a [STACK-Maxima sandbox](STACK-Maxima_sandbox.md) for testing question code on the desktop.

Computer algebra systems are most often designed for either the research mathematician, or the student _to do calculations_. For the purposes of assessment our calculation _establish some relevant properties_ of the students' answers.  Establishing properties in this way, and on the basis of this creating outcomes such as feedback, is something particular to assessment systems. Such properties include things like

* is the expression algebraically equivalent to the correct answer?
* is the expression fully simplified?
* is the expression written a particular conventional form, (e.g. factored, partial fraction)?
* are all the numbers in the expression written as fractions in the lowest terms?

## Maxima in STACK {#reference}

* [Predicate functions](Predicate_functions.md), which are useful to test expressions.
* [Numbers](Numbers.md), including floating point and complex numbers.
* [Simplification](Simplification.md) can be switched on and off in Maxima.
* [Inequalities](Inequalities.md).
* [Matrices and vectors](Matrix.md).
* [Statistics](Statistics.md).
* [Randomly generated objects](Random.md).
* [Plots](Plots.md) and graphics.
* [Buggy rules](Buggy_rules.md) implements rules which are not always valid!

## Developer information, and other topics {#developer}

* Setting up a [STACK-Maxima sandbox](STACK-Maxima_sandbox.md) for testing code on the desktop.
* [Optimising Maxima](../Installation/Optimising_Maxima.md).
