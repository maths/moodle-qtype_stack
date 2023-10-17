# Release notes for STACK 4.4.x

STACK version 4.4.0 was released in July 2022.  This was a major rewrite of the PRT and CASText systems, with a focus on performance and limitations of the previous systems.  This release has changed/tightened up some question authoring causing some problems with existing questions.  For this reason we have written these dedicated release notes.

### Issue: `[[ foreach ]]` blocks over zero length lists.

`[[ foreach ]]` blocks over a list that happens to have length zero causes "text rendering error" if reference to the non-existent element is made, even though the loop is not run: e.g.

    [[ foreach item="[]" ]]  {@item@}   [[/ foreach ]].

(Note without the `{@item@}` it runs fine.)

Solution Protect loop with an `[[ if  ]]` block on the length of the list.

### Issue: Bad interaction between `ordergreat` and `exdowncase`

This is a more serious problem, see [https://github.com/maths/moodle-qtype_stack/issues/887](https://github.com/maths/moodle-qtype_stack/issues/887) for updates.

One solution is to test for `exdowncase(sans)=exdowncase(tans)` [not just `tans`].

### Issue: Automatically calculated numerical teachers answer might now include brackets

Automatically calculated numerical teachers answer might now include brackets: ( -(3/8)), which is a problem if the input forbade brackets!, causing Question Test failure.

Solution: Don't forbid input of brackets!

### Issue: when selecting function names from a list.

When using something like 

    func:rand([sin,cos,exp,ln]);
    is(equal(func,ln));

now returns unknown. Used to return true/false.

Solution: First generate a random integer. Set `func` based on that, and test the value of the integer. (Not as neat!)

### Issue: MathJax 

Mathjax no longer likes   `\begin{pmatrix}{@xx@}\\{@yy@}\end{pmatrix}` in castext.

Solution: make sure there is a space between `\\` and `{@yy@}`.  (Better!) Define the vector to be a maxima object and display that instead.

### Issue: CAS text comments delineated with /* ... */ are rendered

Solution:  Use  `[[ comment ]] ... [ [/ comment ]]`

### Issue: Fatal error causing an exception and then displaying nothing.

Solution: "tans" PRT node entry ended with semicolon: Delete the semi-colon!  We now have error trapping for this problem.

### Issue: `taylor` now returns a maxima taylor series object

`taylor` now returns a maxima taylor series object (as it should!) rather than a normal algebraic expression (as it did!) Solution use `expand(taylor( ... ) )` to get a normal algebraic object.
(May then also need to set `powerdisp:true` to get the usual ordering of terms)

This is one consequence of the single call to Maxima.  Previously there was some string input/output which lost the data type information.

### Issue: rounding of numerical quantities.

The answer test `AlgEquiv` now does not think floats are equivalent, even though they look identical when displayed in decimal.

Solution: don't use the answer test `AlgEquiv` for floats, use a [numerical answertest](../Authoring/Answer_Tests/Numerical.md) instead.

This is one consequence of the single call to Maxima.  Previously there was some string input/output which created numerical rounding. This rounding no longer happens, causing the apparent problem.  Examples are given in the documentation on [numbers](../CAS/Numbers.md) instead.

