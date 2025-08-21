# Vector geometry functions for STACK

STACK has a [contributed library](https://github.com/maths/moodle-qtype_stack/blob/master/stack/maxima/contrib/vectorgeometry.mac) for vector geometry functions.  To use this library you must load it into the question variables.

* To use the latest code from github: `stack_include_contrib("vectorgeometry.mac");`
* Loading this library automatically declares `stack_linear_algebra_declare(true);` to provide context.

This page contains reference documentation on functions in this library.

## Lines and planes

Problem: the student has been asked to represent a subspace in parametric form; we want to decide if these spaces are equivalent.

### Student vector input

If we want students to input an answer such as \(\left[\begin{array}{c} 1 \\ 2 \end{array}\right]+t\, \left[\begin{array}{c} 3 \\ 4 \end{array}\right]\) we have some choices. 

One option is to have each component of this answer have its own answer box, with matrix input for the vectors and (perhaps) an algebraic input for the parameter. A disadvantage of this is that it gives the student the exact form of the answer.

Another option is to ask the student to type in their answer using matrix notation. Students tend to find this cumbersome, and the answer boxes need to be quite large to fit the whole answer.

`vectorgeometry.mac` supports `c()` and `r()` notation, which allows a student to type in the above function as `c(1,2) + t*c(3,4)`. Including this library will provide this functionality. See [Vectors](Vectors.md) for more information about post-processing answers of this form. 

## Converting from parametric form to vector form

Now, let us start with an answer that has already been processed into matrix form such as

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

## Functions in `vectorgeometry.mac`

`vectorgeometry.mac` codifies much of this into a set of functions. These functions make certain assumptions of form, but may make the above marking code a bit simpler. 

### Is an expression in the expected form?
`linear_combinationp(ex)` is a predicate function that tests whether a given input `ex` is exactly a linear combination of column vectors with a linear offset.

This function is specifically expecting something of the form \(\underline{\mathbf{r}}_0 + t_1\underline{\mathbf{d}}_1 + t_2\underline{\mathbf{d}}_2 + \dots + t_n\underline{\mathbf{d}}_n\), and can handle either `c` or `matrix` notation (not `r`). It is primarily useful as a sanity check before running the functions below. It will reject any expression with non-linear terms, and respects declarations like `declare(k,constant)`.

* `linear_combinationp(c(1,2) + t*c(3,4))` returns `true`
* `linear_combinationp(c(1,2) + t^2*c(3,4))` returns `false`
* `linear_combinationp(c(1,2) + t*c(k,4))` returns `false`
* `linear_combinationp(c(1,k) + t*c(3,4))` returns `true`, because the expression is interpreted as `c(1,0) + k*c(0,1) + t*c(3,4)`
* `(declare(k, constant), linear_combinationp(c(1,2) + t*c(k,4))` returns `true`.

### Extracting the components of a vector parametric expression

`constant_term(ex)` extracts the constant term(s) of an expression that is linear in all of its parameters. Specifically, it sets all variables to 0 and returns the result, so it will work on polynomial expressions but not on expressions containing terms like `cos(x)` or `%e^x`. If these edge cases are something that you are worried about, then first using `linear_combinationp(ex)` to filter out expressions that are not linear in their parameters might be sensible.

`constant_term(ex,[constant_vars])` will do the same, but assumes that any variable in `[constant_vars]` is constant.
 
For example, `constant_term(c(1,2) + t*c(3,4))` returns `matrix([1],[2])`, and `constant_term(c(1,2) + t*c(3,4),[t])` returns `matrix([3*t+1],[4*t+2])`.

`vector_parametric_parts(ex)` will break down the expression into its constant part, direction vectors, and parameters (variables). This may behave unexpectedly if `linear_combinationp(ex)` returns false, as it assumes that the expression is linear in its parameters. 

It returns a list with three components. The first is the constant term/linear offset (extracted with `constant_term`). The second is a list of column vectors; the direction vectors of this affine subspace. The third is a list of variable names; the parameters of this expression. Some examples:

* `vector_parametric_parts(c(1,2) + t*c(3,4))` gives `[matrix([1],[2]), [matrix([3],[4])], [t]]`.
* `vector_parametric_parts(c(1,2,3) + t*c(1,1,1) + s*c(1,0,1))` gives `[matrix([1],[2],[3]),[matrix([1],[0],[1]),matrix([1],[1],[1])],[s,t]]`
* `vector_parametric_parts(matrix([p+3*q-4],[2+2*p-q],[p+q+1]))` gives `[matrix([-4],[2],[1]),[matrix([1],[2],[1]),matrix([3],[-1],[1])],[p,q]]`

Just like in `constant_term`, adding a list of variable names to the input will instruct the function to treat them as constants. 

* `vector_parametric_parts(c(1,2,3) + t*c(1,1,1) + s*c(1,0,1), [s])` gives `[matrix([s+1],[2],[s+3]),[matrix([1],[1],[1])],[t]]`
* `vector_parametric_parts(matrix([p+3*q-4],[2+2*p-q],[p+q+1]),[p,q])` gives `[matrix([3*q+p-4],[-q+2*p+2],[q+p+1]),[],[]]`

The output of this function can be a bit cumbersome with its nested lists, but the intention is to use the output directly in the functions below. You might like to use code such as the following to more easily handle these variables in a PRT. 

    ta_parts: vector_parametric_parts(ta);
    [ta_cons, ta_dirvecs, ta_vars]: ta_parts;
    check_form: linear_combinationp(ans1);
    if check_form then block(
        sa_parts: vector_parametric_parts(ans1),
        [sa_cons, sa_dirvecs, sa_vars]: sa_parts
    );

### Displaying these expressions

`vector_parametric_display(parts)` will produce a string of the TeX code to display the expression in the expected format. This is nice for showing students how their answer has been processed. 

The function is set up to take exactly the output of `vector_parametric_parts`. That means that `vector_parametric_display(vector_parametric_parts(ex))` will produce a formatted expression for `ex`.

For example, using the above example, `vec: vector_parametric_display(vector_parametric_parts(matrix([p+3*q-4],[2+2*p-q],[p+q+1])))` will produce a string such that `\({@vec@}\)` displays as: 

\[ \left[\begin{array}{c} -4 \\ 2 \\ 1 \end{array}\right]+p\, \left[\begin{array}{c} 1 \\ 2 \\ 1 \end{array}\right]+q\, \left[\begin{array}{c} 3 \\ -1 \\ 1 \end{array}\right] \]

It is worth emphasising that this is just a string, and cannot be interacted with algebraically.

Continuing with the above example, you might like include in the PRT an expression such as: 

"Your answer was interpreted as {@vector_parametric_display(sa_parts)@}"

so that the student can see how their answer has been interpreted. 

### Testing equivalence of answers

Finally, there are two predicate functions provided to help test equivalence of these parametric expressions. 

`point_in_affine_spacep(p, parts)` tests whether a point `p` is in the affine subspace described by the list `parts`. Again, `parts` is exactly the output of `vector_parametric_parts`. `p` must be a matrix with only one column.

The intended use case is testing whether a student's linear offset does lie in the correct plane. Using the above variable names (`sa` for student answers and `ta` for teacher's answers), it may be convenient to check

    point_in_affine_spacep(sa_cons, ta_parts);

`vector_in_spacep(v,dir_vecs)` tests whether a given **non-zero** vector `v` is in the vector subspace spanned by the list of column vectors `dir_vecs`. Like above, this is intended to be used to check whether a student's direction vectors are actually in the affine subspace (accounting for the linear offset). It may be convenient to test

    vector_in_spacep(first(sa_dirvecs), ta_dirvecs);
    vector_in_spacep(second(sa_dirvecs), ta_dirvecs);

and so on for each vector. To test them all at once, `subspace_equivp` from [`vectorspaces.mac`](Vector_space.md) might prove useful.

`vector_in_spacep(v,dir_vecs,true)` will change the behaviour such that the zero vector is considered to be in the space. Without this third argument, zero vectors will always return false. This is not particularly mathematically accurate, as zero vectors will be members of any appropriately-dimensioned vector subspace. However, in the context of vector geometry we certainly do not want to accept the zero vector as a correct direction vector. 

### An example question

This question is included as a [sample question](../../STACK_question_admin/Library/index.md), "Find vector parametric equation for plane given three points".

Find a vector parametric equation for the plane that goes through \(\mathcal{P}_1\), \(\mathcal{P}_2\) and \(\mathcal{P}_3\). Our answer will be \(\mathbf{r} = \mathcal{P}_1 + t\cdot \left(\mathcal{P}_2 - \mathcal{P}_1\right) + s\cdot \left(\mathcal{P}_3 - \mathcal{P}_1\right)\), but the student could very reasonably choose any of the three points as their linear offset (or others!), and can construct alternative direction vectors and use different parameter names. In the question variables we can include: 

    /* Can be deleted if using STACK 4.9.0 or later: */
    stack_include_contrib("linearalgebra_contrib.mac");
    %_stack_preamble_end;
    
    stack_include_contrib("vectorgeometry.mac")
    
    p1: transpose(rand_selection([-4,-3,-2,-1,0,1,2,3,4],3));
    p2: scale_nicely(crossproduct(p1,transpose([1,0,0])));
    p3: scale_nicely(crossproduct(p1,transpose([0,1,1])));
    
    ta: un_vec_convert(p1) + t*un_vec_convert(p2 - p1) + s*un_vec_convert(p3 - p1);

Then in the feedback variables we can include: 

    ta_parts: vector_parametric_parts(vec_convert(ta));
    [ta_cons, ta_dirvecs, ta_vars]: ta_parts;
    check_form: linear_combinationp(ans1);
    if check_form then block(
       sa_parts: vector_parametric_parts(vec_convert(ans1)),
       [sa_cons, sa_dirvecs, sa_vars]: sa_parts,
       check_form: check_form and is(length(sa_dirvecs)=2)
    );

Note that in this case we have included the requirement that the answer has two direction vectors as part of the "form" of the answer. Then each of the PRT nodes will be, in order: 

    1.    ATAlgEquiv(check_form,true)
    2.    ATAlgEquiv(point_in_affine_spacep(sa_cons,ta_parts),true)
    3.    ATAlgEquiv(vector_in_spacep(first(sa_dirvecs), ta_dirvecs),true)
    4.    ATAlgEquiv(vector_in_spacep(second(sa_dirvecs), ta_dirvecs),true)

This is sufficient to determine that the student's plane equation is equivalent to the teacher's. 
