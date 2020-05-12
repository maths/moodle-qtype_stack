# Tables

STACK provides an intert function `table` for typesetting mathematical tables, provided originally to typeset truth tables in [Propositional Logic](../CAS/Propositional_Logic.md).

You can create a table directly.  Notice the first row is a heading row.

    T0:table([x,x^3],[-1,-1],[0,0],[1,1],[2,8],[3,27]);

Really, the `table` operator does nothing (very much like a matrix).  Like a matrix, the arguments must be identical length lists.
However, there are some special rules for the tex display.

1. The first row is considered to be a heading, and a horizontal line is printed after the first row.
2. There are vertical bars between internal columns of the table.
3. There are currently no options for customising printing of borders, etc.
4. You can highlight entries in the table using the command `texcolor("col", ex)`.  Note however, this will also underline any entries (as colour alone is poor accessibility practice.).

The table `T0` is displayed as \[ {\begin{array}{c|c} x & x^3\\ \hline -1 & -1 \\ 0 & 0 \\ 1 & 1 \\ 2 & 8 \\ 3 & 27\end{array}} \].

`table_bool_abbreviate:true` is a boolean variable.  If set to `true` (the default) boolean entries `true/false` will be abbreviated to `T/F` respectively when creating the LaTeX for display, to keep the table small and tidy looking.  All other entries in the table are typeset normally.  This only affects the LaTeX display, and table entries remain boolean.

`table_zip_with(fn, T1, T2)` combines two tables, using the binary function `fn` (much as `zip_with` combines two lists).

It is instructive to look at the code for `table_difference` which colours entries which differ in red.

    table_difference(T1, T2) := table_zip_with(lambda([ex1,ex2], if ex1=ex2 then ex1 else texcolor("red", ex1)), T1, T2)$

This shows which elements of `T1` differ from the corresponding elements of `T2` by returning elements of `T1` coloured in red.

If you want to identify which entries really are different then you could do something like the following.

    table_zip_with(lambda([ex1,ex2], is(ex1=ex2)), T1, T2)

If you find yourself manipulating tables, the above funtion provides a starting point.  Please ask the developers to add anything you use regularly.

## Examples.

You can create a table via some code such as the following.  Notice the use of the list constructor function `"["` within the `zip_with` command.

    vals:[-1,0,1,2,3];
    fn(ex):=ex^3;
    T0:apply(table, append([[x,fn(x)]], zip_with("[", vals, maplist(fn, vals))));

As a question variable, or directly within CAStext.

    T1:truth_table(a implies b);
    T2:table_difference(truth_table(a xor b), truth_table(a implies b));

Here we have two tables `T1` is displayed as

\[ {\begin{array}{c|c|c} a & b & a\rightarrow b\\ \hline \mathbf{F} & \mathbf{F} & \mathbf{T} \\ \mathbf{F} & \mathbf{T} & \mathbf{T} \\ \mathbf{T} & \mathbf{F} & \mathbf{F} \\ \mathbf{T} & \mathbf{T} & \mathbf{T} \end{array}} \]
and `T2` gives
\[ {\begin{array}{c|c|c} a & b & \color{red}{\underline{a\oplus b}}\\ \hline \mathbf{F} & \mathbf{F} & \color{red}{\underline{\mathbf{F} }} \\ \mathbf{F} & \mathbf{T} & \mathbf{T} \\ \mathbf{T} & \mathbf{F} & \color{red}{\underline{\mathbf{T} }} \\ \mathbf{T} & \mathbf{T} & \color{red}{\underline{\mathbf{F} }}\end{array}} \]
Notice in both the effect of `table_bool_abbreviate:true`.