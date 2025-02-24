# Vector geometry functions for STACK

STACK has a contributed library for vector geometry functions.  To use this library you must load it into the question variables.

* To use the latest code from github: `stack_include_contrib("vectorgeometry.mac");`
* Loading this library automatically declares `stack_linear_algebra_declare(true);` to provide context.

This page contains reference documentation on functions in this library.

TODO: document `vectorgeometry.mac`.

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