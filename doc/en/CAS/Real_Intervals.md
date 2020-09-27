# Real intervals and sets of real numbers

STACK has a simple system for representing and dealing with real intervals and sets of real numbers.

Simple real intervals may be represented by the inert functions `oo(a,b)`, `oc(a,b)`, `co(a,b)`, and `cc(a,b)`.  Here the character `o` stands for open end point, and `c` for a closed end point.  So `oc(-1,3)` is the interval \( \{ x\in\mathbb{R} | -1 < x \mbox{ and } x\leq 3.\} \), and is displayed as \( (-1,3] \) with mismatching brackets in the tradition of UK mathematics.

The Maxima function `union` requires its arguments to be sets, and intervals are not sets.  You must use the `%union` function (from the package `to_poly_solve`) to join simple intervals and combine them with discrete sets. E.g. `%union(oo(-2,-1),oo(1,2))`

Note that the `%union` function sorts its arguments (unless you have `simp:false`), and sort puts simple intervals of the form `oo(-inf,a)` out of order at the right hand end. So, some sorting functions return lists of intervals, not `%union` as you might expect, to preserve the order.

As arguments, the `%union` command can take both simple intervals and sets of discrete real numbers, e.g.

    %union(oo(-inf,0),{1},oo(2,3));

Similarly, STACK provides `%intersection` to represent an intersection of intervals (which the package `to_poly_solve` does not have). 

Predicate functions

1. `intervalp(ex)` returns true if `ex` is a single simple interval.  Does not check `ex` is variable free, so `oo(a,b)` is a simple interval.   `{}`, `none`, `all` and singleton sets are not considered "intervals" by this predicate, use `realsetp` instead.  The primary purpose of this predicate is to detect intervals `oo`, `oc` etc within code.
2. `inintervalp(x, I)`  returns true if `x` is an element of `I` and false otherwise.  `x` must be a real number.  `I` must be a set of numbers or a simple interval of the form `oo(a,b)` etc.
3. `trivialintervalp(ex)` returns true if `ex` is a trivial interval such as \((a,a)\).
4. `unionp(ex)` is the operator a union?
5. `intersectionp(ex)` is the operator an intersection?
6. `realsetp(ex)` return true if `ex` represents a definite set of real numbers, e.g. a union of intervals.  All end points and set elements must be real numbers, so `oo(a,b)` is not a `realset`.  If you want to permit variables in sets and as endpoints use `realset_soft_p` instead.
7. `interval_disjointp(I1, I2)` establishes if two simple intervals are disjoint.
8. `interval_subsetp(S1, S2)` is the real set `S1` contained within the real set `S2`?
9. `interval_containsp(I1, S2)` is the simple interval `I1` an explicit sub-interval within the real set `S2`?  No proper subsets here, but this is useful for checking which intervals a student has.

Basic manipulation of intervals.

1. `interval_simple_union(I1, I2)` join two simple intervals.
2. `interval_sort(I)` takes a list of intervals and sorts them into ascending order by their left hand ends.  Returns a list.
3. `interval_connect(S)` Given a `%union` of intervals, checks whether any intervals are connected, and if so, joins them up and returns the ammended union.
4. `interval_tidy(S)`  Given a union of sets, returns the "canonical form" of this union.
5. `interval_intersect(S1, S2)` intersect two two simple intervals or two real sets, e.g. `%union` sets.
6. `interval_intersect_list(ex)` intersect a list of real sets.
7. `interval_complement(ex)` take a `%union` of intervals and return its complement.
8. `interval_set_complement(ex)` Take a set of real numbers, and return the `%union` of intervals not containing these numbers.
9. `interval_count_components(ex)` Take a set of real numbers, and return the number of separate connected components in the whole expression.  Simple intervals count as one, and sets count as number number of distinct points in the set.  Trivial intervals, such as the empty set, count for 0.  No simplification is done, so you might need to use `interval_tidy(ex)` first if you don't want to count just the representation.

## Natural domains, and real sets with a variable.

The function `natural_domain(ex)` returns the natural domain of a function represented by the expression `ex`, in the form of the inert function `realset`.  For example `natural_domain(1/x)` gives

    realset(x,%union(oo(0,inf),oo(−inf,0)));

The inert function `realset` allows a variable to be passed with a set of numbers.  This is mostly for displaying natural domains in a sensible way.  For example, where the complement of the intervals is a discrete set, the `realset` is displayed as \(x\not\in \cdots\) rather than \(x \in \cdots\) which is normally much easier to read and understand.

    realset(x,%union(oo(0,inf),oo(-inf,0)));

is displayed as \(x \not\in\{0\}\).

## Validation of students' answers

Students must simply type `union` (not `%union`) etc.

Validation of students' answer has a very loose sense of "type".  When we are checking the "type" of answer, if the teacher's answer is a "set" then the student's answer should also be a "set" (see `setp`).  If the teacher's answer is acually a set in the context where an interval should be considered valid, then the teacher's answer should be the inert function `%union`, e.g. `%union({1,2,3})`, to bump the type of the teacher's answer away from set and into `realset`.

Validation does some simple checks, so that mal-formed intervals such as `oo(1)` and `oo(4,3)` are rejected as invalid.

## Assessment of students' answers

The algebraic equivalence answer test will apply `interval_tidy` as needed and compare the results. Currently the feedback in this situation provided by this answer test is minimal.
