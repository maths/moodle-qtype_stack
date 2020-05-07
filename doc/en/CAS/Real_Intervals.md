# Real intervals and sets of real numbers

STACK has a simple system for representing and dealing with real intervals and sets of real numbers.

Simple real intervals may be represented by the inert functions `oo(a,b)`, `oc(a,b)`, `co(a,b)`, and `cc(a,b)`.  Here the character `o` stands for open end point, and `c` for a closed end point.  So `oc(-1,3)` is the interval \( \{ x\in\mathbb{R} | -1 < x \mbox{ and } x\leq 3.\} \), and is displayed as \( (-1,3] \) with mismatching brackets in the tradition of UK mathematics.

The Maxima function `union` requires its arguments to be sets, and intervals are not sets.  You must use the `%union` function (from the package `to_poly_solve`) to join simple interval and combine them with discrete sets. E.g. `%union(oo(-2,-1),oo(1,2))`

Note that the `%union` function sorts its arguments (unless you have `simp:false`), and sort puts simple intervals of the form `oo(-inf,a)` out of order at the right hand end. So, some sorting functions return lists of intervals, not `%union` as you might expect, to preserve the order.

As arguments, the `%union` command can take both simple intervals and sets of discrete real numbers, e.g.

    %union(oo(-inf,0),{1},oo(2,3));

Predicate functions

1. `intervalp(ex)` returns true if `ex` is a single simple interval.  Does not check `ex` is variable free, so `oo(a,b)` is a simple interval.
2. `inintervalp(p, A)`  returns true if `p` is an element of `A` and false.
3. `trivialintervalp(ex)` returns true if `ex` is a trivial interval such as \( (a,a)\).
4. `unionp(ex)` is the operator a union?
5. `realsetp(ex)` return true if `ex` represents a set of real numbers, e.g. a union of intervals.
6. `interval_disjointp(A, B)` establishes if two simple intervals are disjoint.
7. `interval_subsetp(ex, EX)` is the simple interval `ex` a contained within the real set `EX`?

Basic manipulation of intervals.

1. `interval_simple_union(A, B)` join two simple intervals.
2. `interval_sort(X)` takes a list of intervals and sorts them into ascending order by their left hand ends.  Returns a list.
3. `interval_connect(X)` Given a `%union` of intervals, checks whether any intervals are connected, and if so, joins them up and returns the ammended union.
4. `interval_tidy(X)`  Given a union of sets, returns the "canonical form" of this union.
5. `interval_simple_intersect(A, B)` intersect two simple intervals.
6. `inverval_intersect(A, B)` intersect two real sets, e.g. `%union` sets.
7. `inverval_intersect_list(I)` intersect a list of real sets.
8. `interval_complement(X)` take a `%union` of intervals and return its complement.
9. `interval_set_complement(X)` Take a set of real numbers, and return the `%union` of intervals not containing these numbers.


## Natural domains, and real sets with a variable.

The function `natural_domain(ex)` returns the natural domain of a function represented by the expression `ex`, in the form of the inert function `realset`.  For example `natural_domain(1/x)` gives

    realset(x,%union(oo(0,inf),oo(−inf,0)));

The inert function `realset` allows a variable to be passed with a set of numbers.  This is mostly for displaying natural domains in a sensible way.  For example, where the complement of the intervals is a discrete set, the `realset` is displayed as \(x\not\in \cdots\) rather than \(x \in \cdots\) which is normally much easier to read and understand.

    realset(x,%union(oo(0,inf),oo(-inf,0)));

is displayed as \(x \not\in\{0\}\).