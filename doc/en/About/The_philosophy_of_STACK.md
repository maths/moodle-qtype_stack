# Philosophy of STACK

This page will describe the main design philosophy of STACK.

## Motivation

Computer Aided Assessment (CAA) has many advantages over traditional hand-marking. One of the most key advantages is that, if done correctly, it can effectively assess and give feedback to a large number of students. This frees up marking time and resources that would normally have to be spent by several tutors. Additionally, it can be frustrating for students when their tutors are not consistent in how they mark.

The traditional form of CAA is *teacher-provided answer questions*. In these questions, a student makes a selection from, or interacts with, potential answers which the teacher has selected. Examples include multiple choice questions and multiple response questions. This type of questions has a number of limitations, including:

1. Only finite answers are possible. The teacher can put in "dummy responses" for common mistakes and give feedback based on those mistakes, but it is impossible to give feedback on student errors outside the given examples.
2. You can only assess "lower-order" skills. "Give an example of..." type questions are impossible.
3. It encourages "strategic learning", i.e. instead of solving the problem, students will think about how to "trick the system" and find the right answer.
4. "Question distortion", especially problematic in mathematics. You may ask students to integrate a complicated function, and then give a list of potential answers. Differentiating the answers to get the original expression is a much easier process, and not what we wanted the student to practice anyhow.

The alternative is to focus on *student-provided answer questions*. In these questions, the student's answer contains the content. For example, a student could be asked to input an algebraic expression, like a polynomial. This system is harder to implement, but responds to all the shortcomings of teacher-provided answer questions listed above. STACK was designed to be such a system.

## Design choices

STACK is not the first CAA system to focus on student-provided answer questions. However, STACK has a number of key design choices that make it stand out.

#### Teachers should be able to write their own questions, with minimal coding skill

Some CAA systems do not allow teachers to write their own questions, and instead provide a large database of questions. The advantage of this is quality control, however there is a large disadvantage in the lack of flexibility. Therefore, many systems allow teachers to write their own questions. However, often this requires teachers to learn a coding language and assess students' answers by writing code such as :

```
If
 simplify(student_answer-teacher_answer) = 0
then
  mark = 1,
else
  mark = 0.
```

When designing STACK, it was important to give teachers the freedom to write their own questions, while reducing the barrier of entry. When assessing students' answers, STACK asks teachers to focus on the properties of students answers, such as "algebraically equivalent to the teacher's answer", "factorised", etc. Teachers can then pick suitable [answer tests](../Authoring/Answer_tests.md) in a [potential response tree](../Authoring/Potential_response_trees.md) and give marks and feedback accordingly.

#### STACK should be open source

There are many quality CAA systems out there, but many are costly. The developers of STACK wanted to make sure students did not have to pay for access codes to online assessment at Universities. At the same time, they wanted a system that encourages other developers to collaborate on improving STACK. This is exactly what is achieved by an open source license. Many design decisions for STACK are a direct result of this license, for example the choice of Maxima as its Computer Algebra System (CAS), Moodle to take care of identity management, LaTeX for mathematics typesetting and MathJax to filter and display math. All these programs function well with STACK's open source license.

#### Students should not be penalised for poor computer skills

Penalising a student for answering `sinx` instead of `sin(x)` is not fair. Online assessment should assess mathematics skills, not how well students know the specific CAS' syntax. Besides, this is likely an error a tutor would ignore if marking by hand.

To ensure that students are marked for *mathematical skills* instead of *computer skills*, STACK separates "validity" and "correctness". When a student types an answer, it is interpreted by the CAS and a "validation box" is shown displaying how the student's answer is interpreted. This gives the student a chance to fix any syntax errors before their answer is marked. If the student typed `sinx`, the system lets them know "This answer is invalid", giving them a chance to fix their syntax error.

#### Multipart questions should be possible

Multipart questions can be very helpful for students, for example to help guide a student through a new topic. It was hence important for the STACK developers that multipart questions were supported. For this, it was decided to completely separate input and assessment. A question can have unlimited input boxes, and unlimited potential response trees to handle the assessment. Each tree is not limited to a particular input, but instead has access to all the student's inputs. Hence, a tree assessing the correctness of part (b) of a question can use the student's answer to part (a) in its algorithm. This allows for follow-through marking, where a student be penalised for a wrong expression in part (a) but given credit for correctly substituting in values in part (b).

#### STACK should be rich in features

STACK should be designed to cover the needs of a large variety of users across mathematics and science. The main features of STACK are outlined in [About the STACK Project](index.md), but here is a summary:

* Questions can be [randomised](../CAS/Random.md) to ensure different students see different variants of a question.
* There are many different kinds of [inputs](../Authoring/Inputs.md). These are, for example, where the student enters a mathematical expression, or makes a true/false selection.
* Partial credit is possible when an expression only satisfies some of the required properties.
* [Plots](../CAS/Plots.md) can be dynamically generated and included within any part of the question, including feedback in the form of a plot of the student's expression.
* Students can work line by line [reasoning by equivalence](../CAS/Equivalence_reasoning.md) until they have a final answer in the correct form. 
* STACK supports working with [significant figures](../Authoring/Answer_tests_numerical.md) and [scientific units](../Authoring/Units.md).