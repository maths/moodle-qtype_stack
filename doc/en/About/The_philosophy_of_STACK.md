# Philosophy of STACK

The STACK system is a computer aided assessment package for mathematics.  In computer aided assessment (CAA), there are two classes of question types.

*  *Teacher-provided answer questions*

   >  In these questions, a student makes a selection from, or interacts with, potential answers which the teacher has selected.
      Examples include multiple choice, multiple response and so on.

*  *Student-provided answer questions*

   >  In these questions the student's answer contains the content.
      It is not a selection. Examples of these are numeric questions.

There are a number of limitations of teacher-provided answer questions. Some of these are:

1. Only finite answers are possible. The teacher can put in "dummy responses" for common mistakes and give feedback based on those mistakes, but it is impossible to give mistakes on student errors outside the given examples.
2. You can only assess "lower-order" skills. "Give an example of..." type questions are impossible.
3. It encourages "strategic learning", i.e. instead of solving the problem, students will think about how to "trick the system" and find the right answer.
4. Question distortion, especially problematic in mathematics. You may ask students to integrate a complicated function, and then give a list of potential answers. Differentiating the answers to get the original expression may be a much easier process, and not what we wanted the student to practice anyhow.

STACK concentrates on student-provided answers which are mathematical expressions.
For example, a student might respond to a question with a polynomial or matrix.
Essentially STACK asks for mathematical expressions and evaluates these using computer algebra.
The prototype test is the following pseudo-code.

    If
     simplify(student_answer-teacher_answer) = 0
    then
      mark = 1,
    else
      mark = 0.

STACK uses a *computer algebra system*, (CAS) to implement these mathematical functions.
A CAS provides a library of functions with which to manipulate students' answers and generate outcomes such as providing feedback.
Establishing algebraic equivalence with a correct answer is only one kind of manipulation which is possible.

Using CAS can also help generate random yet structured problems, and corresponding worked solutions.
This system is the brain child of [Chris Sangwin](mailto:C.J.Sangwin@ed.ac.uk).

In STACK a lot of attention has been paid to allowing teachers to author and manage their own questions. The following are the key features.

* Question variants are randomly generated within structured templates.
* There are many different kinds of inputs. These are, for example, where the student enters a mathematical expression, or makes a true/false selection.
* Mathematical properties of students' answers are established using answer tests within the CAS Maxima.
* Feedback is assigned on the basis of these properties using a potential response tree. This feedback includes:
    1. Textual comments for the student.
    2. A numerical mark.
    3. Answer notes from which statistics for the teacher are compiled.

These broadly correspond to formative, summative and evaluative functions of assessment.  Which of these outcomes is available to the student, and when, is under the control of the teacher.

* Partial credit is possible when an expression only satisfies some of the required properties.
* Plots can be dynamically generated and included within any part of the question, including feedback in the form of a plot of the student's expression.
* [Multi-part mathematical questions](../Authoring/Authoring_quick_start_2.md) are possible:
  each question may have any number of inputs and any number of potential response trees.
  There need not be a one-to-one correspondence between these.
* Students can work line by line [reasoning by equivalence](../CAS/Equivalence_reasoning.md) until they have a final answer in the correct form.  STACK can automatically assess this particular form of method.

STACK provides a question type for Moodle, ILIAS, and via LTI.
