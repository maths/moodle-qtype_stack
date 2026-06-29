# Using the `calculation` filter with free-text inputs

The free-text input allows students to embed calculations within their answer.  Rather than reaching for a separate calculator, students can request the result of a calculation client-side using Javascript.

This document gives an example question using this feature.  See the [filter calculations](../../Authoring/Question_blocks/Filter_calculations.md) reference documentation for the various calculation options.

### Question variables

Notice in the teacher's answer `ta1` they use calculations.  Of course, this is simple, but it is just an example.

```
ta1:castext("The consecutive even numbers are `n` and `n+2`.  Their difference is 2
`
n + (n+2) = 45*2
2n + 2 = {#45*2#}
2n = {#(45*2)-2#}
n = {#((45*2)-2)/2#}
`
Note this is an even number.
");
ta2:(45*2-2)/2;
```

### Question text

```
<p>Find the smaller of two consecutive even numbers such that their sum is \(45\) times there difference.</p>
<p>Work line by line below, justifying your answer fully.</p>
<div class="free-text-container">
[[input:ans1]] [[validation:ans1]]
[[ascii input="ans1"]]
  [[filter type="calculation" /]]
  [[filter type="markdown-math" transforms="aligneq" /]]
  [[extractor targetinput="ans2" type="lastexpr"/]]
[[/ascii]]
</div>
<p>[[commonstring key="free_text_calc"/]]</p>
<p style="display:none">[[input:ans2]]</p>
<p>[[validation:ans2]]</p>
```

The `[[filter]]` blocks specify how the student's input is processed. 

The first `type="calculation"` extracts `{@...@}` as requested calculations and replaces this with the answer.

The second is `type="markdown-math"` which processes the (updated) text as Markdown with AsciiMath support.  This explicit block is needed because the default `markdown-math` filter is only provided when _no filters_ are explicitly defined in the block.  The filter order matters, and here the question author needs to specify that calculation comes before markdown-math.

Lastly, the final expression is sent to a numerical input.

### Inputs

Input `ans1` holds the free-text.

* Change input `ans1` from the algebraic (default) to [free-text](../../Authoring/Inputs/Text_input.md).
* Change the input "Input box size" to 80 or 100, to give more space to type.
* Change "Student must verify" to no, and "Show the validation" to no.
* Extra options: `monospace:true, manualgraded:false`

Input `ans2` holds the final answer.

* No special changes for `ans2`.

### PRT

Students might enter a number, e.g. `44` or an equation `n=44`, so to accommodate both define the following in the feedback variables.

    sa2:if equationp(ans2) then rhs(ans2) else ans2

Set up the PRT to compare `sa2` with `ta2`.  Algebraic equivalence is fine in this simple example.


The PRT could be improved to check for common mistakes, and add feedback.

### General feedback

You can use the value of `ta1` to create a worked solution in the general feedback, with a static version of the `[[ascii]]` block.

    [[ascii]]{@ta1@}[[/ascii]]

This question can also be loaded from 

    Doc-Examples/Specialist-Tools-Docs/Free-text-input/Free-text_with_calculation.xml

