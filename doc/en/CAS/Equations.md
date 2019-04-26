# Asking students to solve equations

It is quite common to ask students to solve an algebraic equation.  The student's answer may be a list (or set) of numbers.  We need to check that this list is

1. Correct: every element of the list satisfies the equation.
2. Complete: every solution of the equation is in the list.

The best way to do (1) is *not* to check algebraic equivalence with the list/set of correct answers!  Instead, substitute the student's answer into the equation and see if it works.

We proceed by example.  Imagine the teacher has asked the student to solve the following equation defined in the "question variables".


    p:2*x^2+11*x-5/4;
    ta:solve(p,x);
    /* Solve gives a list of equations, we want a set of numbers. */
    ta:setify(maplist(rhs,ta));

For solutions we are not interested in order, but we need multiplicity.  Therefore a "bag" is what we need logically.  However, Maxima only has sets and lists.

If the student enters a set or list, the AlgEquiv answer test can be used to compare sets and lists, but it does so element-wise.  We need to do something different.

In the feedback variables we create a new list called "listans" as follows, assuming the student's answer is assigned to `ans1`.

    /* Need a *LIST* from this point on, so we have a definite order. */
    sans:listify(ans1);
    /* Substitute into the equation. */
    listans:maplist(lambda([ex],ev(p,x=ex)), listify(ans1));

The values of `listans` are what we get when we substitute in each of the students' answers into the equation.   We could also simplify this, but it isn't strictly necessary.

    /* "Simplify" the result (not strictly necessary) */
    listans:maplist(fullratsimp, listans);

Now we have a list of numbers.  We need to compare this with something, but the student's list may have a different number of entries than of the teacher's solution!

    /* Generate something to compare this list with. */
    zl:makelist(0,length(listans));
    setify(append(first(ta)+1, rest(ta))

In the potential response tree, compare `listans` with `zl` using the AlgEquiv answer test and the `quiet=yes` option to suppress all feedback.

Now we want to work out which answers are wrong.

    /* To decide which answers in a list are equivalent. */
    /* Pick out the wrong answers. */
    we:sublist(sans, lambda([ex], not(algebraic_equivalence(ev(p,x=ex),0))));

To use this, we could put the following in the `false` branch feedback.

    The following answers you entered do not satisfy the equation
    \[ {@we@}. \]

To check (2) you need to make sure that the length of the student's answer is the same as the length of the teacher's.  
This can be done with the following two tests, one with numerical GE.  
If the student has too many they have repeated solutions.  If they have too few they have missed some.   

    length(fullratsimp(sans))
    length(ta)

## Randomly generated variables

In the above example, we may have created a randomly generated variable.  E.g.

    v:rand([x,y,z,t]);
    p:a*v^2+b*v+c;

In this case, to make the substitution you need to put in an extra evaluation.

    listans:maplist(lambda([ex],ev(p,ev(v=ex))), listify(ans1));

## Repeated roots!

If the teacher asks a student to enter the answer as a set, then by default STACK does not remove duplicates because validation, etc. 
is done with `simp:false`.  If you want the student to enter repeated roots you must set `Auto-simplify` to `no` in the PRT to avoid losing solutions from the student.  You can then check that each answer satisfies the equation and the student has the correct number of answers using

    length(ans1)

being equivalent to the correct number using `EqualComAss` to avoid simplification.  
Note, that if you "simplify" `ans1` you are likely to lose answers as sets automatically lose duplicates.

Alternatively, you may want to simplify the student's answer to make sure they have the right number of *different* solutions.  This is a separate test.

    length(fullratsimp(ans1))

Exact circumstances of the question will dictate what to do, and whether the teacher expects students to enter duplicate roots the right number of times.
