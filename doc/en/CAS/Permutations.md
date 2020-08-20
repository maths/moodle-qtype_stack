# Writing a permutation as a product of disjoint cycles

Let \[f= \left( \begin{array}{ccccccc} 1 & 2 & 3 & 4 & 5 & 6 & 7 \\ 3 & 1 & 5 & 7 & 2 & 6 & 4 \end{array}\right)\]
 
In pure mathematics we might ask students to write a permutation such as this as a product of disjoint cycles.

One way to do this is to expect students to write their answer as a list, including the one-cycles. e.g. the permutation \((1)(2 \: 3)\) is entered as `[[1],[2, 3]]`.

This list can be turned into a set of lists, so that the order of disjoint cycles is not important.  However, we need to write each cycle in a particular way.  For example, we would want `[2, 3, 4]` and `[3, 4, 2]` to be considered as equivalent.

One way to do this is to make sure the first element in the list is the minumum element in the list, by cycling through the list.  Essentially, we ensure each cycle is re-written in a definite form.  The following code does this for one cycle.  This function can be used in the question variables.

    /* Write a cycle with the smallest element at the front.  Gives a definite order. */
    perm_min_first(ex) := block(
        if length(ex)<2 then return(ex),
        if is(first(ex)<apply(min, rest(ex))) then return(ex),
        return(perm_min_first(append(rest(ex), [first(ex)])))
    );

Assume the student's answer `ans1` is entered as `[[1],[2, 3]]`.  In the feeback variables make sure each list in `ans1` has the smallest element first with the following code.

    sa1:maplist(perm_min_first, ans1);

Then compare `setify(sa1)` with the teacher's answer (which needs to be processed in a similar way) using algebraic equivalence (quiet).

This is a good example of where we do not have a specific data type and corresponding methods for equivalence, but the pre-processing of a student's answer will make sure we can establish the relevant equivalence.