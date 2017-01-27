# Reasoning by Equivalence

Sometimes STACK allows you to work line by line to solve an equation or inequality.  You must work in such a way that adjacent lines are equivalent to each other.  See below for more information about this.

Start by typing in the equation in the question. Then work line by line in the text area until you have solved the problem.  Normally some feedback will be available as you type.  You can copy and paste from one line to the next and modify the line if this helps.

For example, to solve the quadratic equation \( x^2-4x-5=0\) you might reason in the following way.

    x^2-4*x-5=0
    (x+1)*(x-5)=0
    x=-1 or x=5

This might be displayed as follows

\[ \begin{array}{ccc} \  & x^2-4\cdot x-5=0 & \mbox{ } \\ \color{green}{\Leftrightarrow} & \left(x+1\right)\cdot \left(x-5\right)=0 & \mbox{ } \\ \color{green}{\Leftrightarrow} & x=-1\lor x=5 & \mbox{ } \end{array} \]

Whether this feedback is available to you or not immediately depends on your question.  This feedback indicates that STACK considers that each line is equivalent to the previous line.  The last line of working will constitute your final answer, although the whole argument is sometimes considered as well.

1. You should use the normal syntax for [answer input](Answer_input.md) in STACK, including brackets and `*` symbols for multiplication.
2. Sets, lists and matrices are not permitted when reasoning by equivalence.
3. Do not enter your answer as a list or set of numbers, use logical notation such as `x=-1 or x=5`.

## What is "equivalence"?

Two equations are *equivalent* if they have the same solutions with the same multiplicities.

Some general advice for solving equations when reasoning by equivalence is

1. Factorise expressions, then use \( AB=0 \color{green}{\Leftrightarrow} A=0 \lor B=0\).  
2. Use the difference of two squares, \(a^2-b^2=(a-b)(a+b)\).
3. Complete the square:  e.g. \( (x-a)^2+b=0\).

## Avoid taking roots.

Do not take the square root of both sides of an equation.
\[ \mbox{ If } a=b \mbox{ then } a^2=b^2.\]
However, 
\[ \mbox{ if } a^2=b^2 \mbox{ then } a=b \mbox{ or } a=-b.\]
So, if you take the square root of both sides of an equation to tansform \(a^2=b^2\) into \(a=b\) you might miss a root!

To avoid this problem use the *difference of two squares* in the following way.

    a^2=b^2
    a^2-b^2=0
    (a-b)*(a+b)=0
    a=b or a=-b

\[ \begin{array}{ccc} \  & a^2=b^2 & \mbox{ } \\ \color{green}{\Leftrightarrow} & a^2-b^2=0 & \mbox{ } \\ \color{green}{\Leftrightarrow} & \left(a-b\right)\cdot \left(a+b\right)=0 & \mbox{ } \\ \color{green}{\Leftrightarrow} & a=b\lor a=-b & \mbox{ } \end{array} \]

## Avoid the \(\pm\) operator

Try to avoid using the \( \pm \) operator as it can be abiguious, especially when it appears more than once.  The \(\pm\) operator is normally used when taking square roots, to indicate two roots exist.  Instead of using \(\pm\) just write both possibilities explicitly.

For example, do not write \(x-5=\pm 2\).  Instead write \[ x-5=2 \lor x-5=-2\]
and work from there.  This looks like more writing, but it reduces opportunites for error.

Final answers with the \(\pm\) operator will be rejected as invalid.