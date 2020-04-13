# Reasoning by equivalence

__NOTE: Reasoning by equivalence was introduced in STACK 3.6.  This area of STACK is still under active development, and features and behaviour may change significantly in future versions, subject to trials with students and feedback from colleagues.__

We currently provide support for 

- basic single variable polynomials,
- very simple inequalities,
- simple simultaneous equations.

There is no support for algebraic manipulations involving logarithms and trig functions although individual examples may work.

Wider support is intended for future versions. A simple [getting started with equivalence reasoning guide](../Authoring/Equivalence_reasoning.md) can be found elsewhere.

##  What is reasoning by equivalence and this input type?

Reasoning by equivalence is a particularly important activity in elementary algebra.  
It is an iterative formal symbolic procedure where algebraic expressions, or terms within an expression, 
are replaced by an equivalent until a "solved" form is reached.
An example of solving a quadratic equation is shown below.
\[\begin{array}{cc} \  & x^2-x=30 & \\
\color{green}{\Leftrightarrow} & x^2-x-30=0 & \\
\color{green}{\Leftrightarrow} & \left(x-6\right)\cdot \left(x+5\right)=0 \\
\color{green}{\Leftrightarrow} & x-6=0\lor x+5=0 \\
\color{green}{\Leftrightarrow} & x=6\lor x=-5
\end{array}\]

The point is that replacing an expression or a sub-expression in a problem by an equivalent expression provides a new problem having the same solutions.
This input type enables us to capture and evaluate student's line-by-line reasoning, i.e. their steps in working, during this kind of activity.

Reasoning by equivalence is very common in elementary mathematics.  It is either the entire task (such as when solving a quadratic)
or it is an important part of a bigger problem.  E.g. proving the induction step is often achieved by reasoning by equivalence.  

## How do students use this input?

[Instructions for students](../Students/Equivalence_reasoning.md).

In traditional practice students work line by line rewriting an equation until it is solved.  
This input type is designed to capture this kind of working and evaluate it based on the assumption that each line should be equivalent to the previous one.  
Instructions for students are [here](../Students/Equivalence_reasoning.md).  Some common observations about this form of reasoning are

1. Students often use no logical connectives between lines.
2. Students ignore the natural domain of an expression, e.g. in \(\frac{1}{x}\) the value \(x=0\) is excluded from the domain of definition.
3. Operations which do not result in equivalence are often used, e.g. squaring both sides of an equation.

This input type mirrors current common practice and does not expect students to indicate either logic or domains.  
The input type itself will give students feedback on these issues.

Note that students must use correct propositional logic connectives `or` and `and`.  
E.g. their answer must be something correct such as `x=1 or x=2`, *not* something sloppy like `x=1 or 2` or `x=1,2`.  
It certainly can't be something wrong such as `x=1 and x=2` which is often seen in written answers!

Note that students may not take square roots of both sides of an equation.  This will be rejected because it is not equivalent!  Similarly, students may not cancel terms from both sides which may be zero.  As we require equivalence, students may not *multiply* either.  This will probably not correspond to students' expectations, and may take a bit of getting used to.

But should students really use logical connectives?  Yes, I (CJS) believe they should but to require this from the input type now would be too big a step for students and their teachers. Students are already being expected to use connectives such as `and` and `or` correctly.  The input type uses these connectives and in the future options may be added to this input type which require students to be explicit about logical connectives, especially when we add support for implication in addition to equivalence.  As we gain confidence in teaching with equivalence reasoning, we will add more options to this input type.

__If you have strong views on how this input type should behave, please contact the developers.__

## Validation and correctness

STACK carefully separates out *validation* of a student's answer from the *correctness*.
This idea is encapsulated in the separation of validation feedback via the tags `[[validation]]` 
which is tied to inputs, from the potential response trees which establish the mathematical properties.

Each line of a student's answer must be a valid expression, just as with the algebraic input type.  
However, sets, lists and matrices are not permitted in this input type.  
The internal result is a *list* of expressions (equations or inequalities).

## Example use cases for this input type

1. Reasoning by equivalence is the entire task.  The argument must be correct and the last line is the final answer.

2. In a formative setting, we want immediate feedback on whether the argument consists of equivalent lines.  This feedback can be given
  1. as the student types line by line, or
  2. at the end when they press "check".

The ability to give feedback on the equivalence of adjacent lines as the student types their answer somewhat 
blurs the distinction between validation and correctness, but in a way which is probably very useful to students.

## Notes for question authors

* The position of the validation tags, e.g. `[[validation:ans1]]` are ignored for this input type.  Validation feedback is always displayed next to the TextArea into which students type their answer.
* The teacher's answer and any syntax hint must be a list.  If you just pass in an expression strange behaviour may result.
* The input type works very much like the TextArea input type.  Internally, the student's lines are turned into a list.  If you want to use the "final answer" then use code such as `last(ans1)` in your potential response tree.
* If students type in an expression rather than an equation, the system will assume they forgot to add \(=0\) at the end and act accordingly.  This is displayed to the student.

If the student starts their line with an `=` sign, this is accepted.  Teachers cannot use a prefix `=`.  In a worked solution the teacher must use the prefix function `stackeq`.  For example,

    ta:[(x-1)^2,stackeq(x^2-2*x+1)]

Teachers must explicitly use the `nounor` and `nounand` commands, not the `and` and `or` logic.  For more details see the section on [simplification](Simplification.md).  For example, a worked solution might be

    ta:[p=0,(v-n1)*(v-n2)=0,v-n1=0 nounor v-n2=0,v=n1 nounor v=n2]


## Input type options

To enter options for this input use the "extra options field".   Options should be a comma separated list of values only from the following list.

`hideequiv` does not display whether each line is equivalent to the next at validation time.

`hidedomain` does not display natural domain information.

`comments` allows students to include comments in their answer.  By default comments are not permitted as it breaks up the argument and stops automatic marking.

`firstline` takes the first line of the teacher's answer and forces the student to have this as the first line of the student's answer.  The test used is equality up to commutativity and associativity (EqualComAss answer test).

`assume_pos` sets the value of Maxima's `assume_pos` variable to be true.  
If we `assume_pos` they any negative solutions will be ignored, whether or not they exist.  So \(x=\pm 2\) will now be equivalent to \(x=2\).  
You might not want this equivalence.  In particular, this also has the effect of condoning squaring or rooting both sides of an equation.  
For example \(x^4=2\) will now be equivalent to \(x=2\) (rather than \(x=2 \vee x=-2\)).  
This is not the default, but is useful in situations where a student is rearranging an equation to a given subject, and all the variables are assumed to be positive.  Note, this option is only for the input type. You will also need to set this in the question options to also affect the answer test.

`assume_real` sets an internal flag to work over the real numbers.  If `true` then \(x=1\) will be considered equivalent to \(x^3=1\).  Note, this option is only for the input type. You will also need to set this in the question options to also affect the answer test.

`calculus` allows calculus operations in an argument.  Note, this option is only for the input type. You will also need to set this in the answer test options to also affect the answer test.  This is at a very early stage of development.  
For example, constants of integration are not currently checked by this currently.

If the syntax hint is just the keyword `firstline` then the first line of the *value* of the teacher's answer will appear as a syntax hint.  
This enables a randomly generated syntax hint to appear in the box.

# Answer tests

There are a number of answer tests which seek to establish whether a student's list of expressions are all equivalent.

In these tests there is no concept of "step size" or any test that a student has worked in a sensible order.  
The tests share code with the input type, and feedback from the test will be identical to that from the input when this is shown.

You will also need to set options, as in the input type above, to get the answer tests to reflect the options set.
The options supported are `assume_pos`, `assume_real`, and `calculus`.

### EquivReasoning ###

This test establishes that all the items in the list are equivalent, using algebraic equivalence.

### EquivFirst ###

1. This test establishes that all the items in the list are equivalent, using algebraic equivalence.
2. Test that the first line of the student's answer is equivalent to the first line of the teacher's answer up to commutativity and associativity (using the answer test EqualComAss.)

To test the last line of an argument is in the correct form will require a separate node in the potential response tree.  To add this to the answer test gives too many possibilities.

# Natural domains #

The equivalence reasoning input tracks natural domains of expressions.  This is via the STACK's `natural_domain(ex)` function.  Natural domains are shown to students in the validation feedback.
\[ \begin{array}{lll} &\frac{5\,x}{2\,x+1}-\frac{3}{x+1}=1&{\color{blue}{{x \not\in {\left \{-1 , -\frac{1}{2} \right \}}}}}\cr \color{green}{\Leftrightarrow}&5\,x\,\left(x+1\right)-3\,\left(2\,x+1\right)=\left(x+1\right)\,\left(2\,x+1\right)& \end{array} \]
At the moment STACK quietly condones silent domain enlargements such as in the above example.

# Repeated roots #

There is general ambiguity about how to express multiplicity of roots.  If \((x-1)^2=0\) is not equivalent to \(x=1\) then students need to indicate multiplicity of roots, but there appears to be no consensus on how this should be notated.

The equation \( (x-3)^2 = 0 \) and the expression \( x=3 \mbox{ or } x=3\) are considered to be equivalent, because they have the same roots with the same multiplicity.
The expressions \( x=3 \mbox{ or } x=3\) and \( x=3\) have the same variety, but are not identical.
This is, of course, slightly awkward since logical ``or'' is idempotent, and so \( x=3 \mbox{ or } x=3\) and \( x=3\) would be equivalent at a symbolic level.
For this reason, STACK accepts \(x=3\) as equivalent to \((x-3)^2=0\), but with an acknowledgement.
\[ \begin{array}{lll} &\left(x-3\right)^3=0& \cr \color{green}{\mbox{(Same roots)}}&x=3& \cr \end{array} \]

# Other functions #

The maxima function `stack_disp_arg(ex, [showlogic, showdomain])` can be used to display a list of expressions `ex` in the same form as used in the input and answer tests.  This is useful for displaying the teacher's worked solution in the general feedback.  

The second two arguments are optional.
1. The boolean variable `showlogic` determines whether the equivalence symbols are shown.  For a worked solution you probably need to use the following:
2. The boolean variable `showdomain` determines whether the natural domains are shown.

For a worked solution you probably need to use the function

    \[ {@stack_disp_arg(ta)@} \]

Without the equivalence symbols you use 

    \[ {@stack_disp_arg(ta, false)@} \]

With the equivalence symbols but without natural domains you use 

    \[ {@stack_disp_arg(ta, true, false)@} \]

# Finding a step in working #

It is relatively common to want students to take a "particular step" in their argument.  That is to say, to expect a particular intermediate expression to appear explicitly in their list of answers.

For example, imagine you want students to "simplify" \( \log_5(25) \) to \(2\).  It is important to see evidence of the expression \( \log_5(5^2) \) in their answer.  The teacher's answer is, e.g.

    ta:[lg(25,5),stackeq(lg(5^2,5)),stackeq(2*lg(5,5)),stackeq(2*1),stackeq(2)]

We want to accept 

    sa1:[lg(25,5),stackeq(lg(5^2,5)),stackeq(2)]

But reject

    sa0:[lg(25,5),stackeq(2)]

Both of these are correct reasoning arguments, but the second is missing the desired step.

To facilitate this search we provide the function `stack_equiv_find_step(ex, exl)`.  This looks for expression `ex` in the list `exl` using `ATEqualComAss`.  It returns the list of indices of the position.  If you just want to know if the expression is missing use the predicate `emptyp`.  Note, this function strips off `stackeq` before testing, so this function will find \( \log_5(5^2) \) in both `sa1` and `sa2` below.

    sa2:[lg(25,5),lg(5^2,5),2]


## Longer term plans

1. Define \(x\neq a\) operator.  Needed to exclude single numbers from the domain.
2. Define \(x\in X\) operator, for student use.
3. Provide better tools for dealing with assessment, such as checking for particular steps.
2. Provide better feedback to students about which steps they have taken and what goes wrong.

In the long term, we may fully implement the ideas in the paper Sangwin, C.J. __An Audited Elementary Algebra__ The Mathematical Gazette, July 2015.

In the future students might also be expected to say what they are doing, e.g. ``add \(a\) to both sides", as well as just do it.  Quite how it does this, and the options available to the teacher is what is most likely to change.

We would like to introduce the idea of a *model answer*.  STACK will then establish the extent to which the student's answer follows this model solution.

