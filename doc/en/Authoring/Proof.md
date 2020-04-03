# Assessment of proof

In STACK the basic assumption is that a student's answer will be a mathematical expression, e.g. a polynomial or an equation.  The facilities for assessing a student's free-form proof is limited.

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
