# Reasoning by Equivalence

Sometimes STACK allows you to work line by line to solve an equation or inequality.  You must work in such a way that adjacent lines are equivalent to each other.

Start by typing in the equation in the question. Then work line by line in the text area until you have solved the problem.  Normally some feedback will be available as you type.

For example, to solve the quadratic equation \( x^2-4x-5=0\) you might reason in the following way.

    x^2-4*x-5=0
    (x+1)*(x-5)=0
    x=-1 or x=5

You can copy and paste from one line to the next and modify the line if this helps.

This might be displayed as follows

\[ \begin{array}{ccc} \  & x^2-4 x-5=0 & \mbox{ } \\ \color{green}{\Leftrightarrow} & \left(x+1\right) \left(x-5\right)=0 & \mbox{ } \\ \color{green}{\Leftrightarrow} & x=-1\lor x=5 & \mbox{ } \end{array} \]

Whether this feedback is available to you or not immediately depends on your question.  This feedback indicates that STACK considers that each line is equivalent to the previous line.  The last line of working is your final answer, although the whole argument is considered as well.

1. You should use the normal syntax for [answer input](Answer_input.md) in STACK, including brackets and `*` symbols for multiplication.
2. Sets, lists and matrices are not permitted when reasoning by equivalence.
3. Do not enter your answer as a list or set of numbers, use logical notation such as `x=-1 or x=5`.

You can also work line by line with expressions, not equations.  For example, to expand out \( (x-1)(x+4) \) you might reason in the following way.

    (x-1)*(x+4)
    =x^2-x+4*x-4
    =x^2+3*x-4

This might be displayed as follows

\[\begin{array}{ll}\ &\left(x-1\right) \left(x+4\right) \cr \color{green}{\checkmark}&=x^2-x+4 x-4 \cr
\color{green}{\checkmark}&=x^2+3 x-4\end{array}\]

Here, each new line must start with an equals sign `=`.

## What is "equivalence"?

Two *equations* are equivalent if they have the same solutions with the same multiplicities.

Two *expressions* are equivalent if they have the same value when the variables are evaluated.

Some general advice for solving equations when reasoning by equivalence is

1. Factorise expressions, then use \( AB=0 \color{green}{\Leftrightarrow} A=0 \lor B=0\).  
2. Use the difference of two squares, \(a^2-b^2=(a-b)(a+b)\).
3. Complete the square:  e.g. \( (x-a)^2+b=0\).

## Avoid taking roots.

Do not take the square root of both sides of an equation.
\[ \mbox{ If } a=b \mbox{ then } a^2=b^2.\]
However, 
\[ \mbox{ if } a^2=b^2 \mbox{ then } a=b \mbox{ or } a=-b.\]
So, if you take the square root of both sides of an equation to transform \(a^2=b^2\) into \(a=b\) you might miss a root!

To avoid this problem use the *difference of two squares* in the following way.

    a^2=b^2
    a^2-b^2=0
    (a-b)*(a+b)=0
    a=b or a=-b

\[ \begin{array}{ccc} \  & a^2=b^2 & \mbox{ } \\ \color{green}{\Leftrightarrow} & a^2-b^2=0 & \mbox{ } \\ \color{green}{\Leftrightarrow} & \left(a-b\right) \left(a+b\right)=0 & \mbox{ } \\ \color{green}{\Leftrightarrow} & a=b\lor a=-b & \mbox{ } \end{array} \]

## Edge cases

Sometimes you will have an equation with no solutions.  You can express the fact there are no solutions in the following ways

1. `false`.  This is because sometimes you end up with a contradiction such as \(1=2\), since this equation is false you can type that as the final line in your argument.
2. `none`.  The special keyword `none` is used to signify that any values of the variables satisfy the equation.
2. `{}`.  The empty set indicates there are no solutions.

Sometimes you will have an equation in which every number is a solution, such as \(x=x\).  You can express the fact every number is a solution in the following ways

1. `true`.  This is because sometimes you end up with an equation such as \(x=x\), and since this equation is true you can type that as the final line in your argument.
2. `all`.  The special keyword `all` is used to signify that any values of the variables satisfy the equation.

