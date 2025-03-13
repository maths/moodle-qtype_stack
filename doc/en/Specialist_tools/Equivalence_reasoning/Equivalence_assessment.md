# Equivalence input assessent

This is reference documentation for assessing answers from the [equivalence reasoning input](Equivalence_input.md).

## Answer tests ###

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

## Natural domains ##

The equivalence reasoning input tracks natural domains of expressions.  This is via the STACK's `natural_domain(ex)` function.  Natural domains are shown to students in the validation feedback.
\[ \begin{array}{lll} &\frac{5\,x}{2\,x+1}-\frac{3}{x+1}=1&{\color{blue}{{x \not\in {\left \{-1 , -\frac{1}{2} \right \}}}}}\cr \color{green}{\Leftrightarrow}&5\,x\,\left(x+1\right)-3\,\left(2\,x+1\right)=\left(x+1\right)\,\left(2\,x+1\right)& \end{array} \]
At the moment STACK quietly condones silent domain enlargements such as in the above example.

## Repeated roots ##

There is general ambiguity about how to express multiplicity of roots.  If \((x-1)^2=0\) is not equivalent to \(x=1\) then students need to indicate multiplicity of roots, but there appears to be no consensus on how this should be notated.

The equation \( (x-3)^2 = 0 \) and the expression \( x=3 \text{ or } x=3\) are considered to be equivalent, because they have the same roots with the same multiplicity.
The expressions \( x=3 \text{ or } x=3\) and \( x=3\) have the same variety, but are not identical.
This is, of course, slightly awkward since logical ``or'' is idempotent, and so \( x=3 \text{ or } x=3\) and \( x=3\) would be equivalent at a symbolic level.
For this reason, STACK accepts \(x=3\) as equivalent to \((x-3)^2=0\), but with an acknowledgement.
\[ \begin{array}{lll} &\left(x-3\right)^3=0& \cr \color{green}{\text{(Same roots)}}&x=3& \cr \end{array} \]

## Other functions ##

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

## Finding a step in working ##

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

