# About the STACK Project

STACK provides a question type for the Moodle quiz which is specifically designed to enable sophisticated computer-aided assessment in Mathematics and related disciplines, with emphasis on formative assessment.

STACK has been ported to the ILIAS learning evironment.  For details of that integration see [here](https://github.com/ilifau/assStackQuestion/).

Conventional on-line assessment systems are often very limited in the type of questions that can be set.  Accepting numeric answers, or providing multiple choice questions is relatively common. In mathematics there are many questions where checking an answer is significantly easier than solving the problem itself.  It is often invalid to use multiple choice questions here because (sensible) students do not solve the problem directly, but merely check each suggested answer.   It is impossible to set many questions as multiple choice items without giving the game away.

However it is exactly this type of question that we use a lot in mathematics and science teaching. Really, the student should provide their answer in the form of a mathematical expression and the system should evaluate its properties.

We cannot use a simple string match because such questions usually have a large variety of correct answers.  Sometimes the correct answer can be given in many different forms.
The solution lies in using a [Computer Algebra System](../CAS/index.md) to power the assessment system.
The built-in knowledge of mathematics that the computer algebra system provides opens up entirely new possibilities to computer-aided assessment, of which we will now present a few examples:

More about what we are trying to achieve can be found under [the philosophy of STACK](The_philosophy_of_STACK.md).

## Equivalent answers of the right form ##

STACK can mark questions where the correct answer can be expressed in many different forms. In mathematics this is the rule rather than the exception because of algebraic equivalence between expressions, for example \((x+1)^2 = x^2+2x+1\). STACK can identify these equivalences.  STACK can also establish the form of an answer, independent of equivalence to the teacher's.  These two properties are independent.

## Ask for examples ##

The system can mark questions that ask the student to provide an example. Here is a simple example of such a question:

    Give an example of a function f(x) with a minimum at x=0 and a maximum at x=2.

There are many such functions. Rather than comparing the student's answer to the teacher's answer STACK checks that the answer has the required properties.  Giving examples is a higher-order skill that was impossible to assess with conventional CAA systems.

## Intelligent randomisation ##

STACK can randomise problems in such a way that the level of difficulty is kept constant. For example, if a question asks the student to 'diagonalise' a 2 by 2 matrix, then the system can randomise this problem in a way that guarantees that the answer always contains only integers. The trick is to reverse-engineer the randomised question from a randomised answer.  Computer algebra is invaluable to support this process.

## Give feedback and partial credit ##

For example, in a question like:
Give an example of a cubic polynomial with the following properties:

* \(p(0)=1\),
* \(p(x)=0\) at \(x=2\) and at \(x=3\).

STACK can check each condition separately on the student's answer and assign partial credit accordingly.
If the student gave the answer \(p(x) = x^2-5x+6\) for example, then STACK could reply: _Your answer does have zeros at the required points but its value at zero is not equal to 1. You received 2 out of 3 points. Please try again._
There are a variety of ways in which [feedback](../Authoring/Feedback.md) can be given.

STACK is a direct development of the AIM system, using the open source computer algebra system [Maxima](../CAS/Maxima.md).

## Further information  ##

* [Associated publications](Publications.md)
* [The STACK logo](Logo.md).


