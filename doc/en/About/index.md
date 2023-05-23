# About the STACK Project

STACK is an assessment system for mathematics, science and related disciplines, designed to enable students to answer questions with a mathematical expression, such as a polynomial.  STACK uses a Computer Algebra System and students are not limited to multiple choice.

There is a large, and growing, community of STACK users in many languages.

* The main community website for STACK is [https://stack-assessment.org/](https://stack-assessment.org/)
* General community discussion takes place on [https://stack-assessment.zulipchat.com/](https://stack-assessment.zulipchat.com/)
* For specific platforms
  * ILIAS users see [https://docu.ilias.de/goto_docu_cat_4119.html](https://docu.ilias.de/goto_docu_cat_4119.html).
  * Moodle users see [the Mathematics tools](https://moodle.org/mod/forum/view.php?id=752) forum.
* A demonstration server is available in Edinburgh:  [https://stack-demo.maths.ed.ac.uk/demo/](https://stack-demo.maths.ed.ac.uk/demo/)

The source code, and development discussion, is on [github](http://github.com/maths/moodle-qtype_stack/issues), with an additional [ILIAS](https://github.com/ilifau/assStackQuestion/) site.

# Main STACK Features

### Equivalent answers of the right form ###

STACK can accept equivalent expressions, for example \((x+1)^2 = x^2+2x+1\). STACK can also establish the form of an answer, for example if it is factorised.  STACK is designed to let teachers specify independent properties required in an answer.

### Ask for examples ###

The system can mark questions that ask the student to provide an example.

    Give an example of a function f(x) with a minimum at x=0 and a maximum at x=2.

Rather than comparing the student's answer to the teacher's answer, STACK checks that the answer has the required properties.  Giving examples is a higher-order skill that is impossible to assess with conventional Computer Aided Assessment (CAA) systems.

### Intelligent randomisation ###

Randomising questions is invaluable in ensuring students can practice and reducing sharing of answers. The trick is to reverse-engineer the randomised question from a randomised answer. Computer algebra is invaluable to support this process.

### Give feedback and partial credit ###

Consider a question like:
Give an example of a cubic polynomial with the following properties:

* \(p(0)=1\),
* \(p(x)=0\) at \(x=2\) and at \(x=3\).

Here, STACK can check each condition separately on the student's answer and assign partial credit accordingly. If the student gave the answer \(p(x) = x^2-5x+6\) for example, then STACK could reply: _Your answer does have zeros at the required points but its value at zero is not equal to 1. You received 2 out of 3 points. Please try again._ There are a variety of ways in which [feedback](../Authoring/Feedback.md) can be given, including [plotting](../CAS/Plots.md) the students' answer against the teacher's answer.

### Multipart questions

STACK supports multipart questions, like the following:

(a) differentiate \(x^2+5x\) with respect to x.

(b) substitute \(x=5\) into your answer.

You can have follow-through marking for situations where, for example, the student enters a wrong expression to part (a), but correctly substitutes in values into their expression in part (b). Here, STACK can recognise the work the student put into part (b), even though their answer is different from the "correct" answer.

### Support for many types of questions

STACK has a large number of [inputs](../Authoring/Inputs.md) and [answer tests](../Authoring/Answer_Tests/index.md) to support the diverse needs of users across mathematics and science. This includes support for questions about [numerical accuracy](../Authoring/Answer_Tests/Numerical.md), [significant figures](../Authoring/Answer_Tests/Numerical.md#Significant_figure_testing) and [scientific units](../Topics/Units.md). You can also assess students' ability to reason line-by-line with [equivalence reasoning](../Authoring/Equivalence_reasoning.md). 

# Further information

* The main community website [https://stack-assessment.org/](https://stack-assessment.org/)
* [The philosophy of STACK](The_philosophy_of_STACK.md).
* [Associated publications](Publications.md).
* The mathematics behind [the STACK logo](Logo.md).

