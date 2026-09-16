# STACK Web Library Examples

This directory contains standalone browser examples for `StackAsciiDisplay`, a
standalone version of STACK ASCII display blocks for use in HTML pages.

## Users

Use `stack-web` when you want a normal webpage to render free-text inputs and
their corresponding display blocks. You can also use extractors to copy useful
values from a response into normal form fields.

### Try The Examples

Run a simple local web server from `corsscripts`:

```bash
cd corsscripts
python3 -m http.server 8000
```

Then open one of these pages:

- `http://localhost:8000/stack-web/examples/basic-container.html`
- `http://localhost:8000/stack-web/examples/existing-elements.html`
- `http://localhost:8000/stack-web/examples/initial-text.html`
- `http://localhost:8000/stack-web/examples/multiple-containers.html`
- `http://localhost:8000/stack-web/examples/supplied-text.html`
- `http://localhost:8000/stack-web/examples/demo.html`

The examples cover the common setup patterns:

- `basic-container.html` shows the smallest generated container-mode setup.
- `existing-elements.html` wires existing input and output containers and
  extracts a final answer into a separate input.
- `initial-text.html` renders static source text passed through `initialText`.
- `multiple-containers.html` creates two independent generated containers on
  the same page.
- `supplied-text.html` renders static source text without creating a textarea.
- `demo.html` combines several filters and extractors in one larger workbench.

### Create A Page

Add the bundle and MathJax to your page:

```html
<script src="stack-web.bundle.js"></script>
<script async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
```

Create a mount element:

```html
<div id="ascii-block"></div>
```

Place your initialisation script after the mount element and use
`window.StackWeb.ready(...)` to wait for MathJax:

```javascript
window.StackWeb.ready(() => {
    new window.StackWeb.StackAsciiDisplay({
        containerId: 'ascii-block',
        initialWidth: '100%',
        initialHeight: 260,
        initialText: [
            'A short worked example.',
            '',
            '`',
            'x^2 - 5x + 6 = 0',
            '(x - 2)(x - 3) = 0',
            'x = 2 or x = 3',
            '`'
        ].join('\n'),
        operations: [
            {
                operation: 'filter',
                type: 'markdown',
                transforms: 'asciimath,aligneq,minwrap',
                display: 'true'
            }
        ]
    });
});
```

### Choose A Mode

Use `containerId` for the simplest setup. `StackAsciiDisplay` creates a textarea
and matching output pane inside the container.

Use `inputElementId` with `outputElementId` when your page already has separate
mount elements for live editing and rendered output. Style those mount elements
to control their height, width, and page layout.

Use `outputElementId` with `initialText` and no `inputElementId` for a static,
read-only display. Static displays do not support extractors because there is no
live input.

### Use Extractors

Extractors read from the live text input and write matching values into ordinary
form fields. Add the target field to your page:

```html
<input id="answer-output" type="text" readonly>
```

Then add an extractor operation. The `targetinput` value must match the target
field's id:

```javascript
window.StackWeb.ready(() => {
    new window.StackWeb.StackAsciiDisplay({
        containerId: 'ascii-block',
        initialText: [
            'Step 1 = `3^2 + 4^2 = 25`',
            '',
            'Answer: `z = 5`',
            '',
            'Calculated hypotenuse: {@sqrt(3^2 + 4^2)@}'
        ].join('\n'),
        operations: [
            { operation: 'filter', type: 'calculation' },
            {
                operation: 'filter',
                type: 'markdown',
                transforms: 'asciimath,aligneq,minwrap',
                display: 'true'
            },
            {
                operation: 'extractor',
                type: 'laststringremainderwhitespace',
                targetinput: 'answer-output',
                search: 'Answer:'
            }
        ]
    });
});
```

Common extractor types include:

- `laststringremainderwhitespace`, which reads the text after the last matching
  marker such as `Answer:`.
- `lastcalc`, which reads the result of the last calculation block.
- `allregexremainder`, which collects every line matching a regular expression
  and returns the remainders as JSON.

Extractor target fields can sit anywhere on the page. When using multiple
`StackAsciiDisplay` instances, give each container and extractor target its own
unique id.

### Useful Options

Container mode accepts `initialWidth`, `initialHeight`, `minWidth`, `minHeight`,
`maxWidth`, and `maxHeight`. These options are applied to the generated display
container. Number values are treated as pixels; string values are passed through
as CSS lengths such as `100%`, `30rem`, or `60vh`.

The bundled build includes the English `asciistring*` messages from
`lang/en/qtype_stack.php`. Override any message with the `asciistrings` option:

```javascript
window.StackWeb.ready(() => {
    new window.StackWeb.StackAsciiDisplay({
        containerId: 'ascii-block',
        asciistrings: {
            asciistringextractorregexnotfound: 'No matching line was found:'
        },
        operations: [
            { operation: 'filter', type: 'markdown', transforms: 'asciimath' }
        ]
    });
});
```

### Writing Content

Wrap inline ASCIIMath in backticks:

```text
`x^2`
`a_n`
`(a+b)/(c+d)`
`sqrt(x)`
`sum_(i=1)^n i`
```

Use one backtick on a line by itself to create displayed, multiline ASCIIMath:

```text
`
log_3(x+17) - 2 = log_3(2x)
log_3((x+17)/(2x)) = 2
(x+17)/(2x) = 9
x = 1
`
```

Markdown text can be mixed with ASCIIMath. Calculation blocks use `{@...@}`:

```text
Step 1 = `3^2 + 4^2 = 25`

Answer: `z = 5`

Calculated hypotenuse: {@sqrt(3^2 + 4^2)@}
```

The larger demo shows extractors for answer lines, calculation results, repeated
step lines, and named coefficient lines.

## Developers

The examples use the built bundle from `../dist/stack-web.bundle.js`. Source
changes in `corsscripts/stack-web/src` are not visible in these browser examples
until the bundle is rebuilt.

### Build

```bash
cd corscripts/stack-web
npm run build
```

This writes the distributable bundle in `corsscripts/stack-web/dist`. Automated
agents should not rebuild generated bundles; maintainers can run the build after
reviewing source changes.

### Test

The focused Jest coverage for `StackAsciiDisplay` lives in
`tests/jest/ascii.stackasciidisplay.test.js`:

```bash
cd tests/jest
npm test -- ascii.stackasciidisplay.test.js
```

### Code Structure

- `src/StackAsciiDisplay.js` defines the public wrapper class and
  `window.StackWeb.ready(...)`, creates generated input/output elements, wires
  operations into `ascii/stackascii.js`, and keeps generated output sizing in
  sync with textarea resizing.
- `src/stack-web.css` contains the generated display layout, existing-element
  mount styles, output styling, and the mobile layout rules.
- `webpack.config.js` builds the browser bundle and injects the default STACK
  ASCII language strings.
- `examples/*.html` are small standalone pages that exercise the supported
  setup modes.
