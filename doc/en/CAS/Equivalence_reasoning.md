# Reasoning by equivalence

__NOTE this is experimental code and the features and behaviour are very likely to change significantly in the near future.__

##  What is reasoning by equivalence and this input type?

Reasoning by Equivalence is a particularly important activity in elementary algebra.  It is an iterative formal symbolic procedure where algebraic expressions, or terms within an expression, are replaced by an equivalent until a ``solved" form is reached.
The point is that replacing an expression or a sub-expression in a problem by an equivalent expression provides a new problem having the same solutions.
This input type enables us to capture and evaluate student's line by line reasoning, i.e. their steps in working, during this kind of activity.

Note, the teacher's answer and any syntax hint must be a list!  If you just pass in an expression strange behaviour may result.

## How do students use this input?

In traditional practice students work line by line rewriting an equation until it is solved.  This input type is designed to capture this kind of working and evaluate it based on the assumption that each line should be equivalent to the previous one.  Instructions for students are [here](../Students/Equivalence_reasoning.md).

1. Students often use no logical connectives between lines.
2. Students ignore the natural domain of an expression, e.g. in \(\frac{1}{x}\) the value \(x=0\) is excluded from the domain of definition.

This input type mirrors that practice and does not expect students to indicate either logic or domains.  The input type itself will give students feedback on these issues.  Quite how it does this, and the options available to the teacher is what is most likely to change!  In the future students might also be expected to do more.  E.g. they might say what they are doing, e.g. ``add \(a\) to both sides", as well as just do it

The input type works very much like the text area input type.  Internally, the student's lines are turned into a list.  If you want to use the "final answer" then use code such as 

    last(ans1)

in your potential response tree.

## Features of this input type

* If students type in an expression rather than an equation, the system will assume they forgot to add \(=0\) at the end and act accordingly.  This is displayed to the student.
* We assume this is working over the real numbers.


## Examples of arguments

The following are examples of algebraic reasoning which this input type is designed to capture.

### Solving a linear equation

    3*x-7=8
    3*x=15
    x=5

### Solving a quadratic inequality

    2*x^2 + x >= 6 
    2*x^2 + x - 6 >= 0 
    (2*x-3)*(x+2) >= 0 
    ((2*x-3)>= 0 and (x+2)>= 0) or ((2*x-3)<= 0 and(x+2)<= 0)
    (x>= 3/2 and x >= -2 ) or ( x <= 3/2 and x <= -2) 
    x>= 3/2 or x <= -2

    [2*x^2 + x >= 6,  2*x^2 + x - 6 >= 0, (2*x-3)*(x+2) >= 0,((2*x-3)>= 0 and (x+2)>= 0) or ((2*x-3)<= 0 and (x+2)<= 0),(x>= 3/2 and x >= -2 ) or ( x <= 3/2 and x <= -2), x>= 3/2 or x <= -2];

### Solving quadratic equations

    x^2-2*p*x-q=0
    x^2-2*p*x=q
    x^2-2*p*x+p^2=q+p^2
    (x-p)^2=q+p^2
    x-p=+-(q+p^2)
    x-p = (q+p^2) or x-p = -(q+p^2)
    x = p+(q+p^2) or x=p-(q+p^2)

    [x^2-2*p*x-q=0,x^2-2*p*x=q,x^2-2*p*x+p^2=q+p^2,(x-p)^2=q+p^2,x-p=+-(q+p^2),x-p=(q+p^2) or x-p=-(q+p^2),x=p+(q+p^2) or x=p-(q+p^2)];

### Solving rational expressions (erroneous argument)

    (x+5)/(x-7)-5= (4*x-40)/(13-x)
    (x+5-5*(x-7))/(x-7)= (4*x-40)/(13-x)
    (4*x-40)/(7-x)= (4*x-40)/(13-x)
    7-x= 13-x
    7= 13.

    [(x+5)/(x-7)-5= (4*x-40)/(13-x),(x+5-5*(x-7))/(x-7)= (4*x-40)/(13-x), (4*x-40)/(7-x)= (4*x-40)/(13-x),7-x= 13-x,7= 13]

### Solving inequalities with the absolute value function

    abs(x)>5
    x>5 or x<-5

### Solving equations with surds (erroneous argument)

    sqrt(3*x+4) = 2+sqrt(x+2)
    3*x+4       = 4+4*sqrt(x+2)+(x+2)
    x-1         = 2*sqrt(x+2)
    x^2-2*x+1   = 4*x+8
    x^2-6*x-7   = 0
    (x-7)*(x+1) = 0
    x=7 or x=-1

    [sqrt(3*x+4) = 2+sqrt(x+2), 3*x+4=4+4*sqrt(x+2)+(x+2),x-1=2*sqrt(x+2),x^2-2*x+1 = 4*x+8,x^2-6*x-7 = 0,(x-7)*(x+1) = 0,x=7 or x=-1]


### Example of absolute value function (involves removal of redundant inequalities)

    2*x/abs(x-1)  < 1 
    2*x < abs(x-1)
    (x >=1 and 2*x<x-1) or (x<1 and 2*x<-x+1) 
    (x >= 1 and x<-1 ) or (x<1 and 3*x<1)
    x<1/3 

    [2*x/abs(x-1) < 1,2*x < abs(x-1),(x >=1 and 2*x<x-1) or (x<1 and 2*x<-x+1),(x >= 1 and x<-1 ) or (x<1 and 3*x<1),x<1/3]

### Simultaneous equations (must use `and` to join them).

    x^2+y^2=8 and x=y
    2*x^2=8 and y=x
    x^2=4 and y=x
    x= +-2 and y=x
    (x=2 and y=x) or (x=-2 and y=x)
    (x=2 and y=2) or (x=-2 and y=-2)

    [x^2+y^2=8 and x=y, 2*x^2=8 and y=x, x^2=4 and y=x, x= +-2 and y=x, (x= 2 and y=x) or (x=-2 and y=x), (x=2 and y=2) or (x=-2 and y=-2)];

## TODO

1. Document only specific types of problems which we support reasoning by equivalence with.
2. Provide better feedback to students about what goes wrong.
3. Reject equations containing trig functions (for the moment) as invalid.
4. Track down Maxima's internal <= commands.  When did these appear?!  Refactor and remove STACK version.
5. Define \(x\neq a\) operator.  Needed to exclude single numbers from the domain.
5. Define \(x\pm a\) as an infix and prefix operator.  
6. Removal of redundant inequalities from conjunctive and disjunctive expressions.  Deal with end points, e.g. this includes expressions like x<3 or x=3 which come from to_poly_solver.
7. Decide implication direction as well as establishing equivalence.  E.g. if a student squares both sides.
8. Calculate the natural domain, and use this information.  Auditing.
10. Equivalence using a substitution of one variable for another.  See simultaneous equation example.


## Longer term plans

The longer term goal is to implement the ideas in the paper 

* Sangwin, C.J. __An Audited Elementary Algebra__ The Mathematical Gazette, July 2015.
