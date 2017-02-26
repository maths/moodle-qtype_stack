# Reasoning by equivalence

__NOTE: this is experimental code and the features and behaviour are very likely to change significantly in the near future.__

##  What is reasoning by equivalence and this input type?

Reasoning by Equivalence is a particularly important activity in elementary algebra.  It is an iterative formal symbolic procedure where algebraic expressions, or terms within an expression, are replaced by an equivalent until a "solved" form is reached. 
An example of solving a quadratic equation is shown below.
\[\begin{array}{cc} \  & x^2-x=30 & \\ 
\color{green}{\Leftrightarrow} & x^2-x-30=0 & \\
\color{green}{\Leftrightarrow} & \left(x-6\right)\cdot \left(x+5\right)=0 \\
\color{green}{\Leftrightarrow} & x-6=0\lor x+5=0 \\ 
\color{green}{\Leftrightarrow} & x=6\lor x=-5 
\end{array}\]
The point is that replacing an expression or a sub-expression in a problem by an equivalent expression provides a new problem having the same solutions.
This input type enables us to capture and evaluate student's line by line reasoning, i.e. their steps in working, during this kind of activity.

Reasoning by equivalence is very common in elementary mathematics.  It is either the entire task (such as when solving a quadratic) or it is an important part of a bigger problem.  E.g. proving the inducion step is often achieved by reasoning by equivalence.  

## How do students use this input?

[Instructions for students](../Students/Equivalence_reasoning.md).

In traditional practice students work line by line rewriting an equation until it is solved.  This input type is designed to capture this kind of working and evaluate it based on the assumption that each line should be equivalent to the previous one.  Instructions for students are [here](../Students/Equivalence_reasoning.md).  Some common observations about this form of reasoning are

1. Students often use no logical connectives between lines.
2. Students ignore the natural domain of an expression, e.g. in \(\frac{1}{x}\) the value \(x=0\) is excluded from the domain of definition.
3. Operations which do not result in equivalence are often used, e.g. squaring both sides of an equation.

This input type mirrors current common practice and does not expect students to indicate either logic or domains.  The input type itself will give students feedback on these issues.

Note that students must use correct propositional logic connectives `or` and `and`.  E.g. their answer must be something correct such as `x=1 or x=2`, *not* something sloppy like `x=1 or 2` or `x=1,2`.  It certainly can't be something wrong such as `x=1 and x=2` which is often seen in written answers!

Note that students may not take square roots of both sides of an equation.  This will be rejected because it it not equivalen!  Similarly, students may not cancel terms from both sides which may be zero.  As we require equivalence, students may not *multiply* either.  This will take a bit of geting used to!

But, shouldn't student really use logical connectives?  Yes, I (CJS) believe they should but that to require this from the input type now would be too big a step for students and their teachers.  Students are already being expected to use connectives such as `and` and `or` correctly.  The input type uses these connectives and in the futre options may be added to this input type which require students to be explicit about logical connectives, especially when we add support for implication in addition to equivalence.  As we gain confidence in teaching with equivalence reasoning, so we will add more options to this input type.

__If you have strong views on how this input type should behave, please contact the developers.__

## Validation and correctness

STACK carefully separates out *validation* of a student's answer from the *correctness*.  This idea is encapsulated in the separation of validation feedback via the tags `[[validation]]` which is tied to inputs, from the potential response trees which establish the mathematical properties.

Each line of a student's answer must be a valid expression, just as with the algebraic input type.  However, sets, lists and matrices are not permitted in this input type.  The internal result is a *list* of expressions (equations or inequalities).

## Example use cases for this input type.

1. Reasoning by equivalence is the entire task.  The argument must be correct and the last line is the final answer.

2. In a formative setting, we want immediate feedback on whether the argument consists of equivalent lines.  This feedback can be given
  1. as the student types line by line, or
  2. at the end when they press "check".
  
The ability to give feedback on the equivalence of adjacent lines as the student types their answer somewhat blurs the distinction between validation and correctness, but in a way which is probably very useful to students.

## Notes for question authors

* The validation tags, e.g. `[[validation:ans1]]` are ignored for this input type.  Validation feeback is always displayed next to the textarea into which students type their answer.
* The teacher's answer and any syntax hint must be a list.  If you just pass in an expression strange behaviour may result.
* The input type works very much like the textarea input type.  Internally, the student's lines are turned into a list.  If you want to use the "final answer" then use code such as  the following in your potential response tree.

    last(ans1)
* Mathematically, the code assumes we are working over the real numbers.
* If students type in an expression rather than an equation, the system will assume they forgot to add \(=0\) at the end and act accordingly.  This is displayed to the student.

Teachers must explicitly use the `nounor` and `nounand` commands, not the `and` and `or` logic.  For example, a worked solution might be

    ta:[p=0,(v-n1)*(v-n2)=0,v-n1=0 nounor v-n2=0,v=n1 nounor v=n2]


## Input type options.

To enter options for this input use the "extra options field".   Options should be a comma separated list of values only from the following list.

`hideequiv` does not display whether each line is equivalent to the next at validation time.

`comments` allows students to include comments in their answer.  By default comments are not permitted as it breaks up the argument and stops automatic marking.

`firstline` takes the first line of the teacher's answer and forces the student to have this as the first line of the student's answer.  The test used is equality up to commutativity and associativity (EqualComAss answer test).

`assume_pos` sets the value of Maxima's `assume_pos` variable to be true.  In particular, this also has the effect of condoning squaring or rooting both sides of an equation.  For example \(x^4=2\) will now be equivalent to \(x=2\) (rather than \(x=2 \vee x=-2\).  This is not the default, but is useful in situations where a student is rearranging an equation to a given subject, and all the variables are assume to be positive.  Note, this option is only for the input type. You will also need to set this in the question variables to also affect the answer test.

# Answer tests

There are a number of answer tests which seek to establish whether a student's list of expressions are all equivalent.

In these tests there is no concept of "step size" or any test that a student has worked in a sensible order.  The tests share code with the input type, and feedback from the test will be identical to that from the input when this is shown.

### EquivReasoning ###

This test establishes that all the items in the list are equivalent.

### EquivFirst ###

1. This test establishes that all the items in the list are equivalent.  
2. Test that the first line of the student's answer is equivalent to the first line of the teacher's answer up to commutativity and associativity (using the answer test EqualComAss.)

To test the last line of an argument is in the correct form will require a separate node in the potential response tree.  To add this to the answer test gives too many possibilities.

# Other functions #

The maxima function `stack_disp_arg(ex, showlogic)` can be used to display a list of expressions `ex` in the same form as used in the input and answer tests.  This is useful for displaying the teacher's worked solution in the general feedback.  The boolean variable `showlogic` dertemines whether the equivalence symbols are shown.  For a worked solution you probably need to use the following:

    \[ @stack_disp_arg(ta, true)@ \]

# TODO

1. Document and enforce only specific types of problems which we support reasoning by equivalence with.
2. Reject equations containing trig functions (for the moment) as invalid.
3. Track down Maxima's internal <= commands.  When did these appear?!  Refactor and remove STACK version.
4. Define \(x\neq a\) operator.  Needed to exclude single numbers from the domain.
5. Reject any use of the \(\pm\) operator in normal algebraic input.
6. Removal of redundant inequalities from conjunctive and disjunctive expressions.  Deal with end points, e.g. this includes expressions like x<3 or x=3 which come from to_poly_solver.
8. Calculate the natural domain, and use this information, i.e. auditing.
9. Equivalence using a substitution of one variable for another.  See simultaneous equation example.
10. Assume we solve over the real numbers, but if a student used complex numbers correctly it will not be marked as incorrect.

## Longer term plans

Provide better feedback to students about what what steps they have taken and what goes wrong.

In the future students might also be expected to say what they are doing, e.g. ``add \(a\) to both sides", as well as just do it.  Quite how it does this, and the options available to the teacher is what is most likely to change!  

We would like to introduce the idea of a *model answer*.  STACK will then establish the extent to which the student's answer follows this model solution.

The longer term goal is to implement the ideas in the paper 

* Sangwin, C.J. __An Audited Elementary Algebra__ The Mathematical Gazette, July 2015.

