# Philosophy of STACK

One of the key advantages over traditional hand-marking is that, when done correctly, online assessment can effectively assess and give feedback to a large number of students. This frees up marking time and resources that would normally have to be spent by several tutors. Additionally, it can be frustrating for students when their tutors are not consistent in how they mark.

The traditional form of CAA is *teacher-provided answer questions*. In these questions, a student makes a selection from, or interacts with, potential answers which the teacher has selected. Examples include multiple choice questions and multiple response questions. These types of questions have a number of limitations, including:

1. Only finite answers are possible. The teacher can put in "dummy responses" for common mistakes and give feedback based on those mistakes, but it is impossible to give feedback on student errors outside the given examples.
2. You can only assess lower-order skills. "Give an example of..." type questions are impossible.
3. It encourages strategic learning, i.e. instead of solving the problem, students will think about how to "trick the system" and find the right answer.
4. Question distortion is especially problematic in mathematics. You may ask students to integrate a complicated function, and then give a list of potential answers. Differentiating the answers to get the original expression is a much easier process, and sensible students are likely to take the easier route, which is not what we wanted the student to practice.

The alternative is to focus on *student-provided answer questions*. In these questions, the student's answer contains the content. For example, a student could be asked to input an algebraic expression, like a polynomial. This system is harder to implement, but responds to all the shortcomings of teacher-provided answer questions listed above. STACK was designed to be such a system.

## Design choices

STACK is not the first CAA system to focus on student-provided answer questions. However, STACK has a number of key design choices that make it stand out.

#### Teachers should be able to write their own questions, with minimal coding skill

Teachers should take responsibility for their assessments. When assessing students' answers, STACK asks teachers to focus on the properties of students' answers, such as "algebraically equivalent to the teacher's answer", "factorised", etc. 

* _Focusing on mathematical properties, such as equivalence, is a unique design feature of STACK._

Teachers can then pick suitable [answer tests](../Authoring/Answer_Tests/index.md) in a [potential response tree](../Authoring/Potential_response_trees.md) and give marks and feedback accordingly.  STACK does not require teachers to learn a coding language and assess students' answers by writing code such as :

```
If
 simplify(student_answer - teacher_answer) = 0
then
  mark = 1,
else
  mark = 0.
```

#### Students should not be penalised for poor computer skills

Online assessment should assess mathematics skills, not how well students know the specific syntax. For example, penalising a student for answering `sinx` instead of `sin(x)` is not fair.

* _Separating validity from assessment is a key design feature pioneered by STACK._

To ensure that students are marked for *mathematical skills* instead of *computer skills*, STACK separates "validity" and "correctness". When a student types an answer, it is interpreted by the CAS and a "validation box" is shown displaying how the student's answer is interpreted. This gives the student a chance to fix any syntax errors before their answer is marked.


#### Multipart questions should be possible

Multipart questions can be very helpful for students, for example to help guide a student through a new topic.

* _STACK completely separates input and assessment with a unique and flexible design._

A question can have unlimited input boxes, and unlimited potential response trees to handle the assessment. Each tree is not limited to a particular input, but instead has access to all the student's inputs. Hence, a tree assessing the correctness of part (b) of a question can use the student's answer to part (a) in its algorithm. This allows for follow-through marking, where a student be penalised for a wrong expression in part (a) but given credit for correctly substituting in values in part (b).

#### STACK is rich in features

STACK is designed to cover the needs of a large variety of users across mathematics and science.

* Questions can be [randomised](../CAS/Random.md) to ensure different students see different variants of a question.
* There are many different kinds of [inputs](../Authoring/Inputs.md). These are, for example, where the student enters a mathematical expression, or makes a true/false selection.
* Partial credit is possible when an expression only satisfies some of the required properties.
* [Plots](../CAS/Plots.md) can be dynamically generated and included within any part of the question, including feedback in the form of a plot of the student's expression.
* Students can work line by line [reasoning by equivalence](../CAS/Equivalence_reasoning.md) until they have a final answer in the correct form. 
* STACK supports working with [significant figures](../Authoring/Answer_Tests/Numerical.md) and [scientific units](../Topics/Units.md).

#### STACK is open source

The problems faced by teachers, particularly in University, are the same the world over.  The developers of STACK wanted to make sure students do not have to pay for access codes to published books and that institutions are not locked into an expensive system.  At the same time, they wanted a system that encourages other developers to collaborate on improving STACK.  

* _STACK is the leading open souce online assessment system for mathematics and STEM._

The business model for STACK relies on institutions collaborating on the infrustricture, remaining free to use the resulting system as the please, including in commercially as needed.  Many design decisions for STACK are a direct result of choosing the GPL license for the codebase, for example the choice of Maxima as its Computer Algebra System (CAS), Moodle and ILIAS to take care of identity management, LaTeX/MathJax to filter and display mathematics.  The documentation for STACK is available under the  <a rel="license" href="http://creativecommons.org/licenses/by-sa/4.0/">Creative Commons Attribution-ShareAlike 4.0 International License</a> 
![Creative Commons License](../../content/by-sa-88.png).
