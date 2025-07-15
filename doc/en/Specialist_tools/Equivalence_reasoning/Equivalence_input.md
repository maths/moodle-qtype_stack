# Equivalence reasoning input

This is reference documentation for the equivalence reasoning input.

##  What is reasoning by equivalence and this input type?

This input type enables us to capture and evaluate student's line-by-line reasoning.

Reasoning by equivalence is is an iterative formal symbolic procedure where algebraic expressions, or terms within an expression, are replaced by an equivalent until a "solved" form is reached.  Reasoning by equivalence is very common in elementary mathematics.  It is either the entire task (such as when solving a quadratic) or it is an important part of a bigger problem.  E.g. proving the induction step is often achieved by reasoning by equivalence.

There are two modes:  (i) solving equations, and (ii) re-writing equivalent expressions.

An example of solving a quadratic equation is shown below.
\[\begin{array}{cc} \  & x^2-x=30 & \\
\color{green}{\Leftrightarrow} & x^2-x-30=0 & \\
\color{green}{\Leftrightarrow} & \left(x-6\right)\cdot \left(x+5\right)=0 \\
\color{green}{\Leftrightarrow} & x-6=0\lor x+5=0 \\
\color{green}{\Leftrightarrow} & x=6\lor x=-5
\end{array}\]

An example of working with equivalent expressions is shown below.
\[\begin{array}{lll} & x^2-x-30& \\
\color{green}{\checkmark} & =x^2-2\cdot \left(\frac{1}{2}\right)\cdot x-30& \\
\color{green}{\checkmark} & =x^2-2\cdot \left(\frac{1}{2}\right)\cdot x+{\left(\frac{1}{2}\right)}^2-{\left(\frac{1}{2}\right)}^2-30 & \\
\color{green}{\checkmark} & ={\left(x-\frac{1}{2}\right)}^2-{\left(\frac{11}{2}\right)}^2& \\
\color{green}{\checkmark}&=\left(x-6\right)\cdot \left(x+5\right)
\end{array}\]

## How do students use this input?

[Instructions for students](../../Students/Equivalence_reasoning.md).

In traditional practice students work line by line rewriting an equation until it is solved.  
This input type is designed to capture this kind of working and evaluate it based on the assumption that each line should be equivalent to the previous one.  
We have [instructions for students](../../Students/Equivalence_reasoning.md).  Some common observations about this form of reasoning are

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

### Validation symbols and their meaning

Validation typically gives immediate feedback using symbols.

* \(\color{green}{\Leftrightarrow}\) is used to indicate the following _equation_ is equivalent to the previous one.
  E.g. \[\begin{array}{cc} \  & x^2-x-30=0 & \\ \color{green}{\Leftrightarrow} & \left(x-6\right)\cdot \left(x+5\right)=0\end{array}\]
* \(\color{green}{\checkmark}\) is use to indicate the following _expression_ is equivalent to the previous one.
  E.g. \[\begin{array}{lll} & x^2-x-30& \\ \color{green}{\checkmark}&=\left(x-6\right)\cdot \left(x+5\right)\end{array}\]
* \(\color{red}{?}\) is used to indicate one expression is not equivalent to the next (equation or expression).
* \(\color{green}{\text{(Same roots)}}\) is used to indicate the same set of roots, without multiplicity.  There is no easy way to deal with multiplicity of roots.
  E.g. \[{\begin{array}{lll} &x^2-6\,x=-9& \cr \color{green}{\Leftrightarrow}&{\left(x-3\right)}^2=0& \cr \color{green}{\text{(Same roots)}}&x-3=0& \cr \color{green}{\Leftrightarrow}&x=3& \cr \end{array}}\]
* \(\color{red}{\Rightarrow}\) and \(\color{red}{\Leftarrow}\) are used when the solution sets of expressions are subsets. Let \(P\) be the solution set of \(p(x)=0\) and \(Q\) be the solution set of \(q(x)=0\) and \(P\subset Q\) then we write \(p(x) \color{red}{\Rightarrow} q(x)\).
  E.g. \[{\begin{array}{lll} &x+1& \cr \color{red}{\Rightarrow}&={\left(x+1\right)}^2& \cr \color{red}{\Rightarrow}&={\left(x+1\right)}^3& \cr \color{red}{\Leftarrow}&=x+1& \cr \end{array}}\]
* \(\color{green}{\Leftrightarrow}\, \color{blue}{(\mathbb{R})}\) is used when solving over the reals (see the option `assume_real`).
  E.g. \[{\begin{array}{lll}\color{blue}{(\mathbb{R})}&x+1& \cr \color{green}{\Leftrightarrow}\, \color{blue}{(\mathbb{R})}&={\left(x+1\right)}^2& \cr \color{green}{\Leftrightarrow}\, \color{blue}{(\mathbb{R})}&={\left(x+1\right)}^3& \cr \color{green}{\Leftrightarrow}\, \color{blue}{(\mathbb{R})}&=x+1& \cr \end{array}}\]
* \(\color{blue}{\text{Assume +ve vars}}\) is used when solving over the positive reals (see the option `assume_pos`).
  E.g. \[\begin{array}{lll}\color{blue}{\text{Assume +ve vars}}&\left(x-7\right)\cdot \left(x+1\right)=0& \cr \color{green}{\Leftrightarrow}&x=7\,{\text{ or }}\, x=-1& \cr \color{green}{\Leftrightarrow}&x=7& \cr \end{array}\]
* \( \color{green}{\log(?)} \) is used when equivalence is established using the rule \( A=B \Leftrightarrow e^A=e^B\).
  E.g. \[ \begin{array}{lll} &\log_{3}\left(\frac{x+17}{2\cdot x}\right)=2& \cr \color{green}{\log(?)}&\frac{x+17}{2\cdot x}=3^2&{\color{blue}{{x \not\in {\left \{0 \right \}}}}}\cr \color{green}{\Leftrightarrow}&x=1& \cr \end{array}\]

Other symbols are used to give feedback of various kinds.

* \(\color{red}{\text{Missing assignments}}\) is used when students forget to write a variable.
  E.g. \[{\begin{array}{lll} &x=1\,{\text{ or }}\, x=2& \cr \color{red}{\text{Missing assignments}}&x=1\,{\text{ or }}\, 2& \cr \end{array}}\]
* \(\color{red}{\text{and/or confusion!}}\) is used when students use and/or incorrectly.
  E.g. \[{\begin{array}{lll} &x=1\,{\text{ or }}\, x=2& \cr \color{red}{\text{and/or confusion!}}&\left\{\begin{array}{l}x=1\cr x=2\cr \end{array}\right.& \cr \end{array}}\]
* \(\color{blue}{\int\ldots\mathrm{d}x}\) and \(\) are used when we infer students have performed calculus operations (see the option `calculus`).
  E.g. \[{\begin{array}{lll} &x^2+1& \cr \color{blue}{\int\ldots\mathrm{d}x}&\frac{x^3}{3}+x& \cr \color{blue}{\frac{\mathrm{d}}{\mathrm{d}x}\ldots}&x^2+1& \cr \color{blue}{\int\ldots\mathrm{d}x}&\frac{x^3}{3}+x+c& \cr \end{array}}\]


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

Teachers must explicitly use the `nounor` and `nounand` commands, not the `and` and `or` logic.  For more details see the section on [simplification](../../CAS/Simplification.md).  For example, a worked solution might be

    ta:[p=0,(v-n1)*(v-n2)=0,v-n1=0 nounor v-n2=0,v=n1 nounor v=n2]

## Syntax hints

The result of evaluating the syntax hint castext must be a list.  This will be re-interpreted by Maxima, and if you just pass in an expression strange behaviour may result.

The castext may be used to fine-tune the syntax hint. In particular, if you want to remove `*`s from the first line, then this is one (rare) situation where it's best to work at the _display_ level, not at the Maxima expression level.  Normally, especially with PRTs, it's best to work at the maxima level to establish the mathematical meaning.  However, here we need to fine tune how an expression is displayed.  There are currently two options for creating a clean string representation of an expression.

1. `sh:stack_disp(unary_minus_sort(p), "")` will provide the LaTeX output (fine-tuned by STACK).  The problems with using this format here, outside the LaTeX maths environment, are things like (i) use of `\left(...\right)`, use of `\frac{}{}` for division etc.
2. `sh:sremove("*", string(unary_minus_sort(p)))` will provide the string output.  The problems with this format are things like (i) too many brackets, (ii) nounforms are not converted (`nounand`).  This Maxima function has not been fine-tuned by STACK.

To solve this problem in general an outstanding developer task is to write an output format function, like Maxima's `tex` command.  However, this is a lot of work.


Here is one specific example of question variables, sorting out unary minus problems.

```
p:-8*a*d-3*b*c+6*a*c+4*b*d;
make_multsgn("space");
sh:stack_disp([unary_minus_sort(p)],"");
ta:[p,ev(p,simp)];
````

Then use `[{@sh@}]` in the syntax hint.


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

### "let" ###

Students can assigne a value to a variable by typing `let v=a`.  This value will be used in all subsequent working.

For example, try the following in the equiv-reasoning input.

    x^2=a^2
    let a=2
    (x-2)*(x+2)=0

Internally there is a special function `stacklet(v,a)` which is used to indicate the variable `v` should have the value `a` within equivalence reasoning.  Note, this only assigns a value to a variable, and by design is not intended for definition of functions (sorry).

To find all assignments in a student's answer, such as `ans1`, you can filter on the `stacklet` function within the feedback variables.  Note, the student's answer will be a list..

    L1:sublist(ans1,lambda([ex],safe_op(ex)="stacklet"));

Then `L1` will be a list of the assignments.  If you want to turn this into a list of equations then

    L1:ev(L1,stacklet="=");

