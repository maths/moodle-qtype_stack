# The mathematics of equivalence reasoning

Reasoning by equivalence is is line-by-line algebraic reasoning.

Reasoning by equivalence is is an iterative formal symbolic procedure where algebraic expressions, or terms within an expression, are replaced by an equivalent until a "solved" form is reached.  Reasoning by equivalence is very common in elementary mathematics.  It is either the entire task (such as when solving a quadratic) or it is an important part of a bigger problem.  E.g. proving the induction step is often achieved by reasoning by equivalence.

There are two modes:  (i) re-writing equivalent expressions, and (ii) solving equations.

An example of working with equivalent expressions is shown below.
\[\begin{array}{lll} & x^2-x-30& \\
\color{green}{\checkmark} & =x^2-2\cdot \left(\frac{1}{2}\right)\cdot x-30& \\
\color{green}{\checkmark} & =x^2-2\cdot \left(\frac{1}{2}\right)\cdot x+{\left(\frac{1}{2}\right)}^2-{\left(\frac{1}{2}\right)}^2-30 & \\
\color{green}{\checkmark} & ={\left(x-\frac{1}{2}\right)}^2-{\left(\frac{11}{2}\right)}^2& \\
\color{green}{\checkmark}&=\left(x-6\right)\cdot \left(x+5\right)
\end{array}\]
It is not necessary to write the equal sign at the start of the expression in this form of reasoning.

An example of solving a quadratic equation is shown below.
\[\begin{array}{cc} \  & x^2-x=30 & \\
\color{green}{\Leftrightarrow} & x^2-x-30=0 & \\
\color{green}{\Leftrightarrow} & \left(x-6\right)\cdot \left(x+5\right)=0 \\
\color{green}{\Leftrightarrow} & x-6=0\lor x+5=0 \\
\color{green}{\Leftrightarrow} & x=6\lor x=-5
\end{array}\]

STACK has predicates to determine which form of reasoning is used.  The predicate is applied to the list of expressions.

1. `stack_eval_arg_expression_reasoningp(ex)`
2. `stack_eval_arg_equation_reasoningp(ex)`

## Equivalent expressions

Working with equivalent expressions is relatively straightforward.

In STACK, equivalence of adjacent expressions is established with the `AlgEquiv` answer test.

* \(\color{green}{\checkmark}\) (`CHECKMARK`) is use to indicate the following _expression_ is equivalent to the previous one.
  E.g. \[\begin{array}{lll} & x^2-x-30& \\ \color{green}{\checkmark}&=\left(x-6\right)\cdot \left(x+5\right)\end{array}\]
* \(\color{red}{?}\) (`QMCHAR`) is used to indicate one expression is not equivalent to the next (equation or expression).

## Equivalent equations

Two equations are equivalent if they have the same solutions.

Line-by-line algebraic reasoning with equations involves different types of object:

1. Algebraic equations, which (implicitly) represent the set of solutions:  e.g. \(x^2+6=5x\).
2. Logical expressions giving explicit solutions: e.g. \(x=2 \mbox{ or } x=3\).
3. Sets of numbers representing the solution: e.g. \(\{2,3\}\), [intervals](../../CAS/Real_Intervals.md) such as \([0,\infty)\) (`co(0,inf)` in STACK).
4. Systems of inequalities, e.g. \( x\geq 0\).

Reasoning by equivalence typically means working from a given equation to the explicit solutions.

In deciding whether two equations are "the same" there are a number of choices to be made.

1. Are we working over the real numbers, the complex numbers or something else?
2. What should we do about repeated solutions.  E.g. are \((x-2)^3=0\) and \(x=2\) equivalent equations?

There are some edge cases when reasoning with equations:

* \( \mbox{false} = \{\} = \mbox{none}\) denote situations with empty solutions, such as equations such as \(1=0\).
* \( \mbox{true} = \mbox{all}\) denote trivial equations which are universally true, such as \(x=x\).  Note, the atom `all` is displayed as \(\mathbf{R}\) rather than \(\mathbf{C}\).  (This can be changed with `texput(all, "\\mathbb{R}");`)

These edge cases differ from `AlgEquiv` which does not consider \(\{\}\) to be \( \mbox{false}\).

Note that STACK has no concept of "step size": future plans include measuring the "distance" between two expressions/equations.  Teachers can then use this size to decide on whether a step is too big, or an argument needs more detail (another intermediate step).

* \(\color{green}{\Leftrightarrow}\) (`EQUIVCHAR`) is used to indicate the following _equation_ is equivalent to the previous one.
  E.g. \[\begin{array}{cc} \  & x^2-x-30=0 & \\ \color{green}{\Leftrightarrow} & \left(x-6\right)\cdot \left(x+5\right)=0\end{array}\]
* \(\color{red}{?}\) (`QMCHAR`)is used to indicate one expression is not equivalent to the next (equation or expression).
* \(\color{green}{\text{(Same roots)}}\) (`SAMEROOTS`) is used to indicate the same set of roots, without multiplicity.  
  E.g. \[{\begin{array}{lll} &x^2-6\,x=-9& \cr \color{green}{\Leftrightarrow}&{\left(x-3\right)}^2=0& \cr \color{green}{\text{(Same roots)}}&x-3=0& \cr \color{green}{\Leftrightarrow}&x=3& \cr \end{array}}\]
* \(\color{red}{\Rightarrow}\) (`IMPLIESCHAR`) and \(\color{red}{\Leftarrow}\) (`IMPLIEDCHAR`) are used when the solution sets of expressions are subsets. Let \(P\) be the solution set of \(p(x)=0\) and \(Q\) be the solution set of \(q(x)=0\) and \(P\subset Q\) then we write \(p(x) \color{red}{\Rightarrow} q(x)\).
  E.g. \[{\begin{array}{lll} &x+1=0& \cr \color{red}{\Rightarrow}&{\left(x+1\right)}^2=0& \cr \color{red}{\Rightarrow}&{\left(x+1\right)}^3=0& \cr \color{red}{\Leftarrow}&x+1=0& \cr \end{array}}\]
* \(\color{green}{\Leftrightarrow}\, \color{blue}{(\mathbb{R})}\) is used when solving over the reals (see the option `assume_real`).
  E.g. \[{\begin{array}{lll}\color{blue}{(\mathbb{R})}&x+1& \cr \color{green}{\Leftrightarrow}\, \color{blue}{(\mathbb{R})}&{\left(x+1\right)}^2=0& \cr \color{green}{\Leftrightarrow}\, \color{blue}{(\mathbb{R})}&{\left(x+1\right)}^3=0& \cr \color{green}{\Leftrightarrow}\, \color{blue}{(\mathbb{R})}&x+1=0& \cr \end{array}}\]
* \(\color{blue}{\text{Assume +ve vars}}\) is used when solving over the positive reals (see the option `assume_pos`).
  E.g. \[\begin{array}{lll}\color{blue}{\text{Assume +ve vars}}&\left(x-7\right)\cdot \left(x+1\right)=0& \cr \color{green}{\Leftrightarrow}&x=7\,{\text{ or }}\, x=-1& \cr \color{green}{\Leftrightarrow}&x=7& \cr \end{array}\]
* \( \color{green}{\log(?)} \) is used when equivalence is established using the rule \( A=B \Leftrightarrow e^A=e^B\).
  E.g. \[ \begin{array}{lll} &\log_{3}\left(\frac{x+17}{2\cdot x}\right)=2& \cr \color{green}{\log(?)}&\frac{x+17}{2\cdot x}=3^2&{\color{blue}{{x \not\in {\left \{0 \right \}}}}}\cr \color{green}{\Leftrightarrow}&x=1& \cr \end{array}\]

### Other symbols are used to give feedback of various kinds.

* \(\color{red}{\text{Missing assignments}}\) is used when students forget to write a variable.
  E.g. \[{\begin{array}{lll} &x=1\,{\text{ or }}\, x=2& \cr \color{red}{\text{Missing assignments}}&x=1\,{\text{ or }}\, 2& \cr \end{array}}\]
* \(\color{red}{\text{and/or confusion!}}\) is used when students use and/or incorrectly.
  E.g. \[{\begin{array}{lll} &x=1\,{\text{ or }}\, x=2& \cr \color{red}{\text{and/or confusion!}}&\left\{\begin{array}{l}x=1\cr x=2\cr \end{array}\right.& \cr \end{array}}\]
* \(\color{blue}{\int\ldots\mathrm{d}x}\) and \(\) are used when we infer students have performed calculus operations (see the option `calculus`).
  E.g. \[{\begin{array}{lll} &x^2+1& \cr \color{blue}{\int\ldots\mathrm{d}x}&\frac{x^3}{3}+x& \cr \color{blue}{\frac{\mathrm{d}}{\mathrm{d}x}\ldots}&x^2+1& \cr \color{blue}{\int\ldots\mathrm{d}x}&\frac{x^3}{3}+x+c& \cr \end{array}}\]


