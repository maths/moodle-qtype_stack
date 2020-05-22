# Assessment of proof

In STACK the basic assumption is that a student's answer will be a mathematical expression, e.g. a polynomial or an equation.  The facilities for assessing a student's free-form proof is limited.

Colleagues assessing proof might also consider [semi-automatic marking](Semi-automatic_Marking.md).

## Styles

From STACK 4.3 we support the following CSS styles to enable consistent display of mathematical proof, and arguments in general.

### `<div class="proof">`

This class is a general high level container.

<div style="color: #2f6473; background-color: #def2f8; border-color: #d1edf6;">
This is typical question style, from the Moodle theme, containing the following proof.
<div class="proof">
<p>Let P(n) be the statement [...] </p>
<p>From which we see that.</p>
</div>
Back to the typical Moodle style.
</div>

Note, the proof container is minimal, and subtle and does not intrude too much but contains a proof as a distinct entity.

### `<div class="proof-block">`

This class allows the teacher to highlight sub-components of a proof.  It is intended to be a nested sub-proof block of a main proof.

<div style="color: #2f6473; background-color: #def2f8; border-color: #d1edf6;">
This is typical question style, from the Moodle theme, containing the following proof.
<div class="proof">
This proof has two cases.
<div class="proof-block">
<p>If n is odd then we have</p>
<p>[...]</p>
<p>and so ...</p>
</div>
<div class="proof-block">
<p>If n is even then we have</p>
<div class="proof-block">
<p>a. even more subcases.</p>
</div>
<div class="proof-block">
<p>b. even more subcases.</p>
</div>
<p>and so ...</p>
</div>
<p>From which we see that in all cases the proof holds.</p>
</div>
Back to the typical Moodle style.
</div>

### `<div class="proof-line">`, `<div class="proof-num">`, `<div class="proof-step">`, `<div class="proof-comment">`

Greater typeset structure can be gained by marking up the proof in more detail, if so desired.

A typical proof consists of individual lines. Each line is numbered, so we can refer to them later.  The main "meat" of the line is the "proof step" and there is an optional comment.  The proof comment is designed for a comment, to allow an input to ask a question or to act as the second column in a two column proof.

<div style="color: #2f6473; background-color: #def2f8; border-color: #d1edf6;">
Consider the following proof by induction. \(P(n)\)
<div class="proof">
<div class="proof-block">
<div class="proof-line">
 <div class="proof-num">1.</div>
 <div class="proof-step">Let \(P(n)\) be the statement</div>
 <div class="proof-comment">The first block of an induction proof must be a clear statement of the "induction hypothesis".</div>
</div>
<div class="proof-line">
 <div class="proof-num">2.</div>
 <div class="proof-step">\({\sum_{k=1}^{n}{k^2}=\frac{n\cdot \left(n+1\right)\cdot \left(2\cdot n+1\right)}{6}}\)</div>
 <div class="proof-comment">Notice, in this case, we have a purely algebraic induction hypothesis.</div>
</div>
</div>
<div class="proof-block">
<div class="proof-line">
 <div class="proof-num">3.</div>
 <div class="proof-step">Since</div>
 <div class="proof-comment">This block is known as the "base case".</div>
</div>
<div class="proof-line">
 <div class="proof-num">4.</div>
 <div class="proof-step">\( {1^2} = {1}\)</span> and \( {\frac{1\cdot \left(1+1\right)\cdot \left(2\cdot 1+1\right)}{6}} = {1} \)</div>
</div>
<div class="proof-line">
 <div class="proof-num">5.</div>
 <div class="proof-step">it follows that \(P(1)\) is true.</div>
</div>
</div>
<div class="proof-block">
<div class="proof-line">
 <div class="proof-num">6.</div>
 <div class="proof-step">Assume that \(P(n)\) is true.</div>
 <div class="proof-comment">This block is known as the "induction step".</div>
</div>
<div class="proof-line">
 <div class="proof-num">7.</div>
 <div class="proof-step">\({\sum_{k=1}^{n+1}{k^2}} = {\sum_{k=1}^{n}{k^2}} + {\left(n+1\right)^2}\)</div>
 <div class="proof-comment">We just consider the sum which occurs in \(P(n+1)\) and start to rearrange this.</div>
</div>
<div class="proof-line">
 <div class="proof-num">8.</div>
 <div class="proof-step">\(= {\frac{n\cdot \left(n+1\right)\cdot \left(2\cdot n+1\right)}{6}} + {\left(n+1\right)^2}\)</div>
 <div class="proof-comment">We are assuming \(P(n)\) is true, and here we use this fact.</div>
</div>
<div class="proof-line">
 <div class="proof-num">9.</div>
 <div class="proof-step">\(= {\frac{\left(n+2\right)\cdot \left(2\cdot n+3\right)\cdot \left(n+1\right)}{6}}\)</div>
</div>
<div class="proof-line">
 <div class="proof-num">10.</div>
 <div class="proof-step">\(= {\frac{\left(n+1\right)\cdot \left(n+1+1\right)\cdot \left(2\cdot \left(n+1\right)+1\right)}{6}}\)</div>
 <div class="proof-comment">Notice we have rearranged the algebra to give us the right hand side of \(P(n+1)\).</div>
</div>
<div class="proof-line">
 <div class="proof-num">11.</div>
 <div class="proof-step">Hence \(P(n+1)\) is true.</div>
 <div class="proof-comment">This block is the conclusion of the proof.</div>
</div>
</div>
<div class="proof-block">
<div class="proof-line">
 <div class="proof-num">12.</div>
 <div class="proof-step">Since \(P(1)\)</span> and \(P(n)\Rightarrow P(n+1)\) it follows that \(P(n)\) is true for all \(n\in\mathbb{N}\) by the principal of mathematical induction.</div>
</div>
</div>
</div>
</div>

### `<div class="proof-column">`, `<div class="proof-column-2">`

Alternatively, you can use columns, which are fixed at a width of 48%.  The design assumes two columns and line numbers.

Additionally, `proof-column-2` has a different visual style for emphasis.


<div style="color: #2f6473; background-color: #def2f8; border-color: #d1edf6;">
Here is a proof of the great and wonderful theorem.
<div class="proof">
The proof has two cases.
<div class="proof-block">
<div class="proof-line">
 <div class="proof-num">1.</div>
 <div class="proof-column">The proof itself is in the first column.</div>
 <div class="proof-column-2">The second column can contain comments, here in <code>proof-column-2</code> style. </div>
</div>
<div class="proof-line">
 <div class="proof-num">2.</div>
 <div class="proof-column">The proof continues.</div>
 <div class="proof-column-2">With further comments.</div>
</div>
</div>
<div class="proof-block">
<div class="proof-line">
 <div class="proof-num">3.</div>
 <div class="proof-column">The second case of the proof.</div>
 <div class="proof-column">The second column can contain comments, here in <code>proof-column</code> style. </div>
</div>
<div class="proof-line">
 <div class="proof-num">4.</div>
 <div class="proof-column">The proof continues.</div>
 <div class="proof-column">With further comments.</div>
</div>
<div class="proof-block">
<div class="proof-line">
 <div class="proof-num">5.</div>
 <div class="proof-column">The proof continues.</div>
 <div class="proof-column">With further comments.</div>
</div>
<div class="proof-line">
 <div class="proof-num">6.</div>
 <div class="proof-column">The proof continues, without comment.</div>
</div>
</div>
</div>
</div>
<div class="proof-line">
 <div class="proof-column">Lines don't need to have <code>proof-num</code> or <code>proof-block</code></div>
 <div class="proof-column">With further comments.</div>
</div>
<div class="proof-line">
 <div class="proof-column">The proof continues.</div>
 <div class="proof-column-2">With further comments.</div>
</div>
<div class="proof-line">
 <div class="proof-column">This concludes the proof.</div>
</div>
</div>

It is possible to use `proof-column-2` style in the first column, but this looks odd and is not recommended.  The `proof-column-2` style is intended for only the second column.  The `proof-column` style is intended to be used in both columns.

Here is a proof by induction.

<div style="color: #2f6473; background-color: #def2f8; border-color: #d1edf6;">
Consider the following proof by induction. \(P(n)\)
<div class="proof">
<div class="proof-block">
<div class="proof-line">
 <div class="proof-num">1.</div>
 <div class="proof-column">Let \(P(n)\) be the statement</div>
 <div class="proof-column-2">The first block of an induction proof must be a clear stement of the "induction hypothesis".</div>
</div>
<div class="proof-line">
 <div class="proof-num">2.</div>
 <div class="proof-column">\({\sum_{k=1}^{n}{k^2}=\frac{n\cdot \left(n+1\right)\cdot \left(2\cdot n+1\right)}{6}}\)</div>
 <div class="proof-column-2">Notice, in this case, we have a purely algebraic induction hypothesis.</div>
</div>
</div>
<div class="proof-block">
<div class="proof-line">
 <div class="proof-num">3.</div>
 <div class="proof-column">Since</div>
 <div class="proof-column-2">This block is known as the "base case".</div>
</div>
<div class="proof-line">
 <div class="proof-num">4.</div>
 <div class="proof-column">\( {1^2} = {1}\)</span> and \( {\frac{1\cdot \left(1+1\right)\cdot \left(2\cdot 1+1\right)}{6}} = {1} \)</div>
</div>
<div class="proof-line">
 <div class="proof-num">5.</div>
 <div class="proof-column">it follows that \(P(1)\) is true.</div>
</div>
</div>
<div class="proof-block">
<div class="proof-line">
 <div class="proof-num">6.</div>
 <div class="proof-column">Assume that \(P(n)\) is true.</div>
 <div class="proof-column-2">This block is known as the "induction step".</div>
</div>
<div class="proof-line">
 <div class="proof-num">7.</div>
 <div class="proof-column">\({\sum_{k=1}^{n+1}{k^2}} = {\sum_{k=1}^{n}{k^2}} + {\left(n+1\right)^2}\)</div>
 <div class="proof-column-2">We just consider the sum which occurs in \(P(n+1)\) and start to rearrange this.</div>
</div>
<div class="proof-line">
 <div class="proof-num">8.</div>
 <div class="proof-column">\(= {\frac{n\cdot \left(n+1\right)\cdot \left(2\cdot n+1\right)}{6}} + {\left(n+1\right)^2}\)</div>
 <div class="proof-column-2">We are assuming \(P(n)\) is true, and here we use this fact.</div>
</div>
<div class="proof-line">
 <div class="proof-num">9.</div>
 <div class="proof-column">\(= {\frac{\left(n+2\right)\cdot \left(2\cdot n+3\right)\cdot \left(n+1\right)}{6}}\)</div>
</div>
<div class="proof-line">
 <div class="proof-num">10.</div>
 <div class="proof-column">\(= {\frac{\left(n+1\right)\cdot \left(n+1+1\right)\cdot \left(2\cdot \left(n+1\right)+1\right)}{6}}\)</div>
 <div class="proof-column-2">Notice we have rearranged the algebra to give us the right hand side of \(P(n+1)\).</div>
</div>
<div class="proof-line">
 <div class="proof-num">11.</div>
 <div class="proof-column">Hence \(P(n+1)\) is true.</div>
 <div class="proof-column-2">This block is the conclusion of the proof.</div>
</div>
</div>
<div class="proof-block">
<div class="proof-line">
 <div class="proof-num">12.</div>
 <div class="proof-column">Since \(P(1)\)</span> and \(P(n)\Rightarrow P(n+1)\) it follows that \(P(n)\) is true for all \(n\in\mathbb{N}\) by the principal of mathematical induction.</div>
</div>
</div>
</div>
</div>