# Authoring free-text input questions

This page provides two examples of free-text input questions.

1. Manually graded example.
2. Free-text input where the final answer is extracted to an algebraic input.

# 1. Simple manually graded question

The simplest use is to capture students' typed text.  In this respect the free-text input, and linked `[[ascii]]` block, are very like an essay or short answer input.

Start with a blank STACK question, and choose a name.

Question variables should be changed to 

```
ta1:"## Theorem: ∀ n ∈ ℕ, 1+3+5+7+...+(2n−1) = n².

Let P(n) be the statement `sum_(k=1)^n 2k − 1 = n²` ∀ n ∈ ℕ.

Since `sum_(k=1)^1 =1 =1^2`, we see that \(P(1)\) is true.

Assume that \(P(n)\) is true. Then  
`
sum_(k=1)^(n+1) (2k−1) = sum_(k=1)^(n) (2k−1)+ (2(n+1)−1)
                       = n² + 2n + 1
                       = (n+1)².
`
Since `P(1)` is true and `P(n+1)` follows from `P(n)`, we conclude
that `P(n)` is true `forall n in NN` by the principle of mathematical induction";
```

Notice this text contains a mix of markdown and unicode text.

The question text uses pure LaTeX rather than unicode:

```
<p>Prove that \(\forall n \in \mathbb{N}\), \(1+3+5+7+...+(2n−1) = n^2\)</p>
<div class="free-text-container">
[[input:ans1]]
[[validation:ans1]]
[[ascii input="ans1"]]
[[/ascii]]
</div>
```

Note the use of style `<div class="free-text-container">...</div`.  This positions the input and ascii-block preview next to each other.

Reference documentation is available for the linked [`[[ascii]]` block](../../Authoring/Question_blocks/ASCII.md) elsewhere.  This block provides more flexibility for options for free-text input than is available for general inputs.  In particular for (i) processing, (ii) displaying and (in later examples) (iii) extracting parts of the input for use in PRTs.  This example uses the default `markdown-math` filter.  This takes the free-text, processes it as markdown with mathematics and uses the `aligneq` transformation to align multi-line mathematics on the first equals sign.

Adjust the input settings:

* Change input `ans1` from the algebraic (default) to [free-text](../../Authoring/Inputs/Text_input.md).
* Change the input "Input box size" to 80 or 100, to give more space to type.
* Change "Student must verify" to no, and "Show the validation" to no.

Remove `[[feedback:prt1]]` from the feedback variables.  Then save the question and confirm removal of `prt`.  This turns the question into a survey/manually graded item.

The input `ans1` is available to PRTs as a string, but this is not used in this question.

This question will be [manually graded](../../Moodle/Semi-automatic_Marking.md).  

This question can also be loaded from 

    Doc-Examples/Specialist-Tools-Docs/Free-text-input/Free-text_manually_graded_mathematical_proof.xml

# 2. Free-text answer linked to STACK input

This question is an example which links the end of a free-text input to an algebraic input.  This question is _not_ manually graded, rather the last block of mathematics is sent to the equivalence reasoning input and automatically assessed.  The rest of the text is ignored.


Start with a blank STACK question, and choose a name.

The question variables are as follows.

```
eq1:lg(x+17,3)-2=lg(2*x,3);

ta1:"To solve `log_3(x+17)-2 = log_3(2x)` we note that both `x+17>0` and `2x>0`.  Combining this we have the domain `x>0`.
`
log_3(x+17)-2         = log_3(2x)   
log_3(x+17)-log_3(2x) = 2
log_3((x+17)/(2x))    = 2
(x+17)/(2x)           = 3^2 = 9
x+17                  = 18x
x                     = 1
`
This answer satisfies the original domain constraint.
";
ta2:x=1;
```

This contains the original equation to solve `eq1` in Maxima syntax, `ta1` a string containing the whole answer, and `ta2` the final answer (in Maxima syntax).

This question needs two inputs `ans1` and `ans2`, with an `[[ascii]]` block linking `ans1` to `ans2`.

The complete (html) question text is therefore

```
<p> Solve {@eq1@}.</p>
<p>Work line by line below, justifying your answer fully.  Your last line should be your answer \(x =...\).</p>
<div class="free-text-container">
[[input:ans1]] [[validation:ans1]]
[[ascii input="ans1"]]
  [[extractor targetinput="ans2" type="lastexpr"/]]
[[/ascii]]
</div>
<p>[[hint title="Input help"]][[commonstring key="free_text_fact"/]][[/hint]]</p>
<p style="display:none">[[input:ans2]]</p>
<p>[[validation:ans2]]</p>
```

Notice, the `[[ascii]]` block uses the default `markdown-math` filter. The `[[extractor]]` block specifies what to extract and where to send it. Here `type="lastexpr"` looks for the last line of mathematics and sends it to input `ans2`. See [the ASCII block documentation](../../Authoring/Question_blocks/ASCII.md) for the full list of extractor types.

Note the `[[hint]]` block, using a `[[commonstring]]` block to provide standard help to students for this input type.

`ans1`: A freetext input, to hold the student's text answer.

1. Validation not shown
2. Box size: 80 (nice and wide to hold typed text)
3. Extra options: `monospace:true, manualgraded:false`

`ans2`: An algebraic input, to hold the last line of mathematics.

1. Validation shown
2. Insert assuming single-character variables, implied and for spaces.
3. Note the input itself is hidden by HTML `<p style="display:none">` in the question text.
4. (Optional, hide the validation.)

To set up the PRT, a basic check of the final answer of the student's input is sufficient for demonstration: `ATEqualComAss(ans2,ta2)`.  A more complex PRT, and question tests, is provided in the example.

This question can also be loaded from 

    Doc-Examples/Specialist-Tools-Docs/Free-text-input/Free-text_with_algebraic_answer.xml

# 3. Mixing manually graded free-text answers with automatic assessment

For information on how to mix manually graded free-text answers with automatic assessment see the separate docs on [semi-automatic marking](../../Moodle/Semi-automatic_Marking.md).

A manually graded question, which also has automatic assessed PRTs, is given in 

    Features/input-type-sample-questions/Free-text_final_answer.xml

