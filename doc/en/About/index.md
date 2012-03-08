# About the STACK Project

STACK is an open-source system for computer-aided assessment in Mathematics and related disciplines, with emphasis on formative assessment.

Conventional on-line assessment systems are very limited in the type of questions that can be set.
Questions where there is a large variety of correct answers or where the correct answer can be given in many different forms can not be marked with conventional assessment systems.

However it is exactly this type of question that we use a lot in science teaching. There are usually many ways to write a formula, for example.
Furthermore, in mathematics it is impossible to set many questions as multiple choice items without giving the game away.

Really, the student should provide their answer in the form of a mathematical expression and the system should evaluate its properties.

The solution lies in using a [Computer Algebra System](../CAS/) to power the assessment system.
The built-in knowledge of mathematics that the computer algebra system provides opens up entirely new possibilities to computer-aided assessment, of which we will now present a few examples:

More about what we are trying to achieve can be found under [the philosophy of STACK](The_philosophy_of_STACK.md).

## Equivalent answers ##

STACK can mark questions where the correct answer can be expressed in many different forms.
In mathematics this is the rule rather than the exception because of algebraic equivalence between expressions, for example $(x+1)^2 = x^2+2x+1$.
STACK can identify these equivalences.

## Ask for examples ##

The system can mark questions that ask the student to provide an example.
Giving examples is a higher-order skill that was impossible to assess with conventional CAA systems.
Here is a simple example of such a question:

    Give an example of a function f(x) with a minimum at x=0 and a maximum at x=2.

There are many such functions. Rather than comparing the students answer to the teacher's answer STACK checks that the answer has the required properties.

## Intelligent randomisation ##

STACK can randomise problems in such a way that the level of difficulty is kept as a constant.
For example, if a question asks the student to 'diagonalise' a 2 by 2 matrix, then the system can randomise this problem in a way that guarantees that the answer always contains only integers.
The trick is to reverse-engineer the randomised question from a randomised answer.

## Give feedback and partial credit ##

For example, in a question like:
Give an example of a cubic polynomial with the following properties:

* $p(0)=1$,
* $p(x)=0$ at $x=2$ and at $x=3$.

STACK can check each condition separately on the student's answer and assign partial credit accordingly.
If the student gave the answer \(p(x) = x^2-5x+6\) for example, then STACK could reply: _Your answer does have zeros at the required points but its value at zero is not equal to 1. You received 2 out of 3 points. Please try again._ 
There are a variety of ways in which [feedback](../Authoring/Feedback.md) can be given.

STACK is a direct development of the [AIM](../Related_projects/AIM.md) system, using the open source computer algebra system [Maxima](../CAS/Maxima.md).

## Further information  ##

* [Associated publications](Publications.md)
* [The STACK logo](Logo.md).
