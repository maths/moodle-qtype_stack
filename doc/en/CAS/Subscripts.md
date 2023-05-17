## Atoms, subscripts and fine tuning the LaTeX display

Everything in Maxima is either an _atom_ or an _expression_. Atoms are either an integer number, float, string or a name.  You can use the predicate `atom()` to decide if its argument is an atom.  Expressions have an _operator_ and a list of _arguments_. Note that the underscore symbol is _not_ an operator.  Thus `a_1` is an atom in maxima. Hence, the atoms `a1` and `a_1` are not considered to be algebraically equivalent.  If you would like to consolidate subscripts in students' input see the documentation on [extra options](../Authoring/Inputs.md).  Also note that since the underscore is not an operator, an expression such as `(a_b)_c` is not valid Maxima syntax, but `a_b_c` is a valid name for an atom.

You can change the TeX output for an atom with Maxima's `texput` command.  E.g. `texput(blob, "\\diamond")` will display the atom `blob` as \( \diamond \).  If you place `texput` commands in the question variables, this affects the display everywhere in the question including the inputs.  E.g. if a student types in `blob` then the validation feedback should say something like "your last answer was: \( \diamond \)".

Display with subscripts is a subtle and potentially confusing issue because subscript notation in mathematics has many different uses.  For example,

1. Subscripts denote a function of the natural numbers, e.g. when defining terms in a sequence \(a_1, a_2, a_3\).  That is the subscript denotes function application.  \(a_n = a(n)\).
2. Subscripts denote differentiation, e.g. \( x_t \) is the derivative of \(x \) with respect to \(t\).
3. Subscripts denote coordinates in a vector, in \( \vec{v} = (v_1, v_2, \cdots, v_n)  \).

There are many other possible uses for subscripts, especially in other subjects e.g. in physics or [actuarial studies](../Reference/Actuarial.md).

Because Maxima considers subscripted expressions to be atoms, the default TeX output of an atom `V_alpha` from Maxima is \( {\it V\_alpha} \) (literally `{\it V\_alpha}`) and not \( V_{\alpha} \) as a user might expect.  For this reason STACK intercepts and redefines how atoms with the underscore are displayed.  In particular STACK (but not core Maxima) takes an atom `A_B`, applies the `tex()` command to `A` and `B` separately and concatenates the result using subscripts.  For example, if you define

    texput(A, "{\\mathcal A}");
    texput(B, "\\diamond");

then `A_B` is now displayed as \({{{\mathcal A}}_{\diamond}}\).

Below are some examples.

| Maxima code  | Maxima's `tex()` command (raw output and displayed)       | STACK  (raw output and displayed)                             |
|--------------|-----------------------------------------------------------|---------------------------------------------------------------|
| `A_B`        | `{\it A\_B}` \({\it A\_B}\)                               | `{{A}_{B}}` \( {{A}_{B}} \)                                   |
| `A[1]`       | `A_{1}` \( A_{1}\)                                        | `{A_{1}}` \( {A_{1}} \)                                       |
| `A1`         | `A_{1}` \( A_{1} \)                                       | `{A_{1}}` \( {A_{1}} \)                                       |
| `A_1`        | `A_{1}` \( A_{1} \)                                       | `{A_{1}}` \( {A_{1}} \)                                       |
| `A_x1`       | `{\it A\_x}_{1}` \( {\it A\_x}_{1} \)                     | `{{A}_{x_{1}}}` \( {{A}_{x_1}} \)                             |
| `A_BC`       | `{\it A\_BC}` \( {\it A\_BC} \)                           | `{{A}_{{\it BC}}}` \( {{A}_{{\it BC}}} \)                     |
| `A_alpha`    | `{\it A\_alpha}` \( {\it A\_alpha}\)                      | `{{A}_{\alpha}}` \( {{A}_{\alpha}} \)                         |
| `A_B_C`      | `{\it A\_B\_C}` \( {\it A\_B\_C} \)                       | `{{{A}_{B}}_{C}}` \( {A_B}_C \)                               |
| `x_t(1)`     | `{\it x\_t}\left(1\right)` \( {\it x\_t}\left(1\right) \) | `{{\it x\_t}\left(1\right)}` \( {{\it x\_t}\left(1\right)} \) |
| `A[1,2]`     | `A_{1,2}` \( A_{1,2} \)                                   | `{A_{1,2}}` \( {A_{1,2}} \)                                   |

Notes

1. in the above examples three different expressions (atoms `A1`, `A_1` and the expression `A[1]`) generate the same tex code `A_{1}` \( A_{1}\), and so are indistinguishable at the display level.
2. The expression `x_t(1)` refers to the function `x_t` which is not an atom, and hence STACK's logic for displaying atoms with underscores does not apply (by design).  If you want to display a function name including a subscript you can explicitly use, e.g. `texput(x_t, "x_t");` to control the display, this is just not done automatically.

One situation where this design is not satisfactory is when you want to use both of the atoms `F` and `F_1` but with different display. For example `F` should display as \({\mathcal F}\) but `F_1` should display as \( F_1 \).  Such a situation is not hard to imagine, as it is often considered good style to have things like \( F_1 \in {\mathcal F}\).  The above design always splits up the atom `F_1` into `F` and `1`, so that the atom `F_1` will display as  \({\mathcal F}_1\).  (This is actually what you normally expect, especially with the Greek letter subscripts.)  To avoid this problem the logic which splits up atoms containing an underscore checks the texput properties list. If an entry has been made for a specific atom then STACK's display logic uses the entry, and does not split an atom over the underscore.  In the above example, use the following texput commands.

    texput(F, "{\\mathcal F}");
    texput(F_1, "{F_1}");

With this code `F` displays as \({\mathcal F}\), the atom `F_1` displays as \( F_1 \), and every subscript will display with calligraphic, e.g. `F_alpha` displays as \({\mathcal F}_{\alpha}\).  There is no way to code the reverse logic, i.e. define a special display only for the unique atom `F`.

Note that the [scientific units](../Topics/Units.md) code redefines and then assumes that symbols represent units.  E.g. `F` is assumed to represent Farad, and all units are typeset in Roman type, e.g. \( \mathrm{F} \) rather than the normal \( F \). This is typically the right thing to do, but it does restrict the number of letters which can be used for variable names in a particular question.  To overcome this problem you will have to redefine some atoms with texput.  For example,

    stack_unit_si_declare(true);
    texput(F_a, "F_a");

will display the atom `F_a` as \(F_a\), i.e. not in Roman.  If you `texput(F, "F")` the symbol `F` is no longer written in Roman, as you would expect from units.  This may be sensible if Farad could not possibly appear in context, but students might type a range of subscripted atoms involving `F`.

The use of texput is global to a question. There is no way to display a particular atom differently in different places (except perhaps in the feedback variables, which is currently untested: assume texput is global).

How would you generate the tex like \( A_{1,2} \)?  STACK's `sequence` command (see below) does output its arguments separated by a comma, so `sequence(1,2)` is displayed as \( {1,2} \), however the Maxima command `A_sequence(1,2)` refers to the function `A_sequence`, (since the underscore is not an operator).  Hence STACK's logic for splitting up _atoms_ containing the underscore does not apply.  (In any case, even if the display logic did split up function names we would still have the issue of binding power to sort out, i.e. do we have the atom with parts `A` and `sequence(1,2)` or the function named `A` and `sequence`?)  To create an output like \( A_{1,2} \) you have no option but to work at the level of display.  Teachers can create an inert function which displays using subscripts.

    texsub(a,b)

is typeset as \({a}_{b}\) i.e. `{a}_{b}` in LaTeX.  For example,

* `texsub(A, sequence(1,2))` will display as \({{A}_{1, 2}}\),
* with simplification off, `texsub(F,1-2)` will be displayed as \({F}_{1-2}\).

Note that the process of converting `theta_07` into the intermediate `texsub` form internally results in the `texsub(theta,7)` which removes the leading zero.  This is a known issue, for which a work around is to directly use `texput(theta_07, "{{\\theta}_{07}}")` or `texsub(theta,"07")`.  The second option does not produce optimal LaTeX, since it uses TeX `mbox`, e.g. `{{\theta}_{\mbox{07}}}`.
