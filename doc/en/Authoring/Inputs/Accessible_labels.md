# Connecting question text to an input

Mark the visible text identifying an answer with `data-stack-input-label`, using that input's name:

```html
<p data-stack-input-label="ans1">Projection matrix \(P\)</p>
[[input:ans1]] [[validation:ans1]]

<p data-stack-input-label="ans2">Number of solutions</p>
[[input:ans2]] [[validation:ans2]]
```

The Moodle renderer connects this text to the corresponding fixed matrix, dropdown, text or textarea
input. Fixed matrix cells also include their one-based row and column. Mathematical markup is retained
through `aria-labelledby`; do not replace a formula with an arbitrary speech transcription. The marked
text is ordinary question text, so existing CASText and language filters continue to apply.

Use a short, meaningful label that distinguishes this answer from the others. Internal names such as
`ans1` are identifiers, not suitable learner-facing descriptions. Existing explicit control labels and
radio/checkbox option labels are preserved. Connections are scoped to the individual question attempt
and are also initialized when instant validation is off or the attempt is read-only.

This mechanism does not infer labels from arbitrary surrounding prose, or label variable-size matrix
controls. Contextual connections require JavaScript; the fixed matrix's row/column names are generated
by PHP. Standalone API clients need to initialize the label module in their own host page.
