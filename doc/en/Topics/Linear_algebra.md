# Setting linear algebra questions in STACK

Linear algebra, next to calculus, is one of the pillars of modern mathematics and an important application in STACK is testing linear algebra questions.

Often there will be many equivalent answers, especially when asking for students to find the basis for a subspace, etc.

This document provides some tips and hints for writing STACK questions.

Please see also the file on [matrix manipulations](../CAS/Matrix.md).

## Random questions

The paper [Setting linear algebra problems](https://www.researchgate.net/publication/228855146_Setting_linear_algebra_problems) by John Steele (2003) is very useful.

## Lines and planes

Problem: the student has been asked to represent a subspace in parametric form; we want to decide if these spaces are equivalent.

## Converting from parametric form to vector form

Let us start with an answer such as

    ta:transpose(matrix([s+2*t,3+t,w]));
    ta:expand(ta);
    lv:listofvars(ta);

    /* Sanity check: make sure the student's answer is linear in its parameters. */
    deg:apply(max, maplist(lambda([ex], hipow(ta, ex)), lv));

The first thing is to note Maxima's 

    cm:coefmatrix(flatten(args(ta)), lv);
    am:augcoefmatrix(flatten(args(ta)), lv);

We will turn this into vector form explicitly as follows.

    /* Remove any constant vectors (setting all parameters to zero). */
     cv:ev(ta, maplist(lambda([ex], ex=0), lv), simp);

    /* Calculate the direction vectors. */
    dvs:maplist(lambda([ex], col(cm,ex)), makelist(k,k,1,length(lv)));

    /* Create vector form. */
    simp:false;
    vf:apply("+", zip_with("*", lv, dvs))+cv;

Note, this last line assumes `simp:false` (otherwise we simplify back to the original `ta`!), and uses STACK's `zip_with` function which is not a core part of Maxima.

Now we have the direction vectors we can answer a number of questions.

Turn the direction vectors into a single matrix.

    simp:true;
    cm:transpose(apply(matrix, maplist(lambda([ex], first(transpose(ex))), dvs)));
    /* Takes us back to ta.... */
    cm.transpose(matrix(lv))+cv;

Does `ta` pass through the origin?  This amounts to solving 

    solve(flatten(args(ta)), lv);

But, solve can throw errors when we have dependent equations (as we might well do...).

## Solving systems of linear equations

Using solve can throw errors, so use `linsolve` instead.  For example.

    /* Decide if a vector is in W */
    point_in_space(W, wx):= linsolve(flatten(args(W))-first(args(transpose(wx))), listofvars(W));

If the above is the empty list, there is no solution.  Otherwise a solution is returned.

    /* Calculate the canonical form of a column space of a system. */
    cspace(ex):= block([M],
      M: coefmatrix(flatten(args(ex)), listofvars(ex)),
      ev(transpose(rref(transpose(M))), simp)
    );