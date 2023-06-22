# Unsorted multi-input answers

Quite often we have a number of separate inputs, but we don't know which order the student will choose to enter.

Let's assume we ask a student to break down integration using linearity for example we have
\[ \int x^2+\sin(x) \mathrm{d} x = \int {\color{red}?} \mathrm{d} x + \int {\color{red}?} \mathrm{d} x\]
where the `?` are inputs `ans1` and `ans2`.  We might expect `ans1=x^2` but of course we could also have `ans1=sin(x)`.

### Use the feedback variables

One option is to define `sans:ans1+ans2` in the feedback variables.  Then the PRT can check `sans` is equivalent to the integrand up to commutativity and associativity (or something else if you perefer). The problem with this is the difficulty in awarding partial credit.

### Partial credit for some correct answers

We want to provide some partial credit when student have some, but not all, the inputs correct.

Let's assume we are looking for \(n\) different inputs.  We define \(m\) to be the number of missing, \(w\) to be the number of "not wanted" inputs, then we choose the score to be
\[ s = \max\left(1-\frac{m+w}{2n}, 0\right)\]
E.g., here if a student gets all \(n\) wrong so that \(m=w=n\) then the score is zero.
If the student types in all the required expressions, \(n=0\) and \(n\) wrong ones in addition, then \(s=\frac{1}{2}\).
This function is, of course, a choice of the teacher.

In the above example, put the following in the question variables

    ta1:x^2;
    ta2:sin(x);
    tas:{ta1, ta2};
    p:ta1+ta2;

In the feedback variables put

    sans:{ans1, ans2};
    missing:setdifference(tas, sans);
    notwanted:setdifference(sans, tas);
    score:max(1-(length(missing)+length(notwanted))/(2*length(tas)),0);

Continuing the above example, in the PRT use

1. The answer test `sets` perhaps with the quiet option.
2. `sans` is `{ans1,ans2}`.
3. `tans` is `tas`.
4. Assign the score in _both_ prt branches to be `score`.

### Dealing with duplicate entries ###

How do we decide partial credit when there may be duplicates, e.g. eignevalues with repetion?  If the teacher's answer is `[1,1,2]` then we can't use the above example based on sets.

STACK provides a maxima function `list_cancel(l1,l2)` which removes any common elements from `[l1,l2]`, with duplication.  E.g. use the following in the quesion variables.

    sans:{ans1, ans2};
    [missing, notwanted]:list_cancel([sans, tas]);
    score:max(1-(length(missing)+length(notwanted))/(2*length(tas)),0);

Note that `list_cancel` will not establish algebraic equialence and within this function two expressions are considered the same using maxima's `is(ex1=ex2))`.  Hence, some pre-processing of the lists might be needed, depending on the situation and what you consider is the "same".  For example if we have

    l1:[x^2,x^3,x^2-1,x+x];
    l2:[x^2,x^4,(x-1)*(x+1),2*x];
    list_cancel([l1,l2]);

will return \[\left[ \left[ x^3 , x^2-1 \right]  , \left[ x^4 , \left(x-1\right) \,\left(x+1\right) \right]  \right] \]
Notice the last elements are remove because default simplification takes place but \(x^2-1\) and \((x-1)(x+1)\) are not considered the same by `is`.  In this case `ratsimp` can be applied to the lists first.  In other situations functions like `trigsimp` or `trigrat` might be needed.

