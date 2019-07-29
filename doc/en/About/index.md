# About the STACK Project

STACK is an assessment system for mathematics, science and related disciplines, designed to enable students to answer questions with a mathematical expression, such as a polynomial. Students are not limited to multiple choice.

Below is a typical STACK question. Note that the student's answer can be any algebraic expression, and that their answer is interpreted by the system before it is marked. This allows the student to confirm the answer is what they intended before being marked. Notice also the automatically generated feedback.

![STACK screenshot](%CONTENT/STACK-screenshot.png)

STACK uses a [Computer Algebra System](../CAS/index.md) to power the assessment system. The built-in knowledge of mathematics that the computer algebra system provides opens up entirely new possibilities to computer-aided assessment.

Primarily, STACK provides a question type for the Moodle quiz.  STACK has also been ported to the ILIAS learning environment, as an [ILIAS question type](https://github.com/ilifau/assStackQuestion/). STACK can be integrated into other systems using [LTI](../Installation/LTI.md).

More about what we are trying to achieve can be found under [the philosophy of STACK](The_philosophy_of_STACK.md).

A demonstration server is also available:  [https://stack.maths.ed.ac.uk/demo](https://stack.maths.ed.ac.uk/demo)

# Main STACK Features

STACK has many features. Here are some of the most important ones.

### Equivalent answers of the right form ###

STACK can mark questions where the correct answer can be expressed in many different forms. In mathematics, this is the rule rather than the exception because of algebraic equivalence between expressions, for example \((x+1)^2 = x^2+2x+1\). STACK can identify these equivalences. STACK can also establish the form of an answer, for example if it is factorised. These two properties are independent.

### Ask for examples ###

The system can mark questions that ask the student to provide an example. Here is a simple example of such a question:

    Give an example of a function f(x) with a minimum at x=0 and a maximum at x=2.

There are many such functions. Rather than comparing the student's answer to the teacher's answer, STACK checks that the answer has the required properties.  Giving examples is a higher-order skill that is impossible to assess with conventional Computer Aided Assessment (CAA) systems.

### Intelligent randomisation ###

Randomising questions is invaluable in ensuring students do not share answers. STACK can randomise questions in such a way that the level of difficulty is kept constant. For example, if a question asks the student to 'diagonalize' a 2 by 2 matrix, then the system can randomise this problem in a way that guarantees that the answer only contains integers. The trick is to reverse-engineer the randomised question from a randomised answer. Computer algebra is invaluable to support this process.

### Give feedback and partial credit ###

Consider a question like:
Give an example of a cubic polynomial with the following properties:

* \(p(0)=1\),
* \(p(x)=0\) at \(x=2\) and at \(x=3\).

Here, STACK can check each condition separately on the student's answer and assign partial credit accordingly. If the student gave the answer \(p(x) = x^2-5x+6\) for example, then STACK could reply: _Your answer does have zeros at the required points but its value at zero is not equal to 1. You received 2 out of 3 points. Please try again._ There are a variety of ways in which [feedback](../Authoring/Feedback.md) can be given, including [plotting](../CAS/Plots.md) the students' answer against the teacher's answer.

### Multipart questions

STACK supports multipart questions, like the following:

(a) differentiate \(x^2+5*x\) with respect to x.

(b) substitute \(x=5\) into your answer.

You can have follow-through marking for situations where, for example, the student enters a wrong expression to part (a), but correctly substitutes in values into their expression in part (b). Here, STACK can recognise the work the student put into part (b), even though their answer is different from the "correct" answer.

### Support for many types of questions

STACK has a large number of [inputs](../Authoring/Inputs.md) and [answer tests](../Authoring/Answer_tests.md) to support the diverse needs of users across mathematics and science. This includes support for questions about [numerical accuracy](../Authoring/Answer_tests_numerical.md), [significant figures](../Authoring/Answer_tests_numerical.md#Significant_figure_testing) and [scientific units](../Authoring/Units.md). You can also assess students' ability to reason line-by-line with [equivalence reasoning](../Authoring/Equivalence_reasoning.md). 

# Further information

* [The philosophy of STACK](The_philosophy_of_STACK.md).
* A [demonstration server](https://stack.maths.ed.ac.uk/demo).
* [Associated publications](Publications.md).
* [STACK Community](Community.md).
* The mathematics behind [the STACK logo](Logo.md).
* [Credits](Credits.md).
