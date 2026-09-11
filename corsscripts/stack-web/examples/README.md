# STACK Web Library Examples

This directory contains a standalone browser demo for `StackAsciiDisplay`.
It uses the bundled library from `../dist/stack-web.bundle.js`, so rebuild
`corsscripts/stack-web` after changing source files.

## Quick Test

```bash
cd corsscripts
python3 -m http.server 8000
```

Open `http://localhost:8000/stack-web/examples/demo.html`.

The demo creates a generated textarea and output area inside `demoContainer`.
The input and output start side-by-side. Drag the resize grip in the lower-right
corner of the input to stretch both panes. If `maxWidth` is omitted, horizontal
growth is unbounded and the container can overflow the page visibly.

The example buttons are based on the free-text documentation questions in
`samplequestions/stacklibrary/Doc-Examples/Specialist-Tools-Docs/Free-text-input`.
Some examples are display-only; some use one extractor; others populate several
extractor outputs.

## Build Command

```bash
cd corsscripts/stack-web
npm run build
```

Agents should not rebuild generated bundles. Maintainers can run this command
after reviewing source changes.

## Container Mode

```javascript
const display = new StackAsciiDisplay({
    containerId: 'ascii-block',
    initialWidth: '100%',
    minWidth: 520,
    minHeight: 220,
    maxHeight: 520,
    operations: [
        { operation: 'filter', type: 'calculation' },
        {
            operation: 'filter',
            type: 'markdown',
            transforms: 'asciimath,aligneq,minwrap',
            display: 'true'
        },
        { operation: 'filter', type: 'calculation', reset: 'true' },
        {
            operation: 'extractor',
            type: 'laststringremainderwhitespace',
            targetinput: 'answer-expression',
            search: 'Answer ='
        },
        {
            operation: 'extractor',
            type: 'lastcalc',
            targetinput: 'answer-calculation'
        },
        {
            operation: 'extractor',
            type: 'allregexremainder',
            targetinput: 'answer-steps',
            regex: '^Step\\s+\\d+\\s*=\\s*'
        },
        {
            operation: 'extractor',
            type: 'laststringremainderwhitespace',
            targetinput: 'answer-a',
            search: 'a ='
        },
        {
            operation: 'extractor',
            type: 'laststringremainderwhitespace',
            targetinput: 'answer-b',
            search: 'b ='
        }
    ]
});
```

Container mode accepts `initialWidth`, `initialHeight`, `minWidth`, `minHeight`,
`maxWidth`, and `maxHeight`. Number values are treated as pixels. Omit
`maxWidth` for unlimited horizontal resizing, or set it to a pixel, percent,
`rem`, `vw`, or `vh` length to cap the generated block.

To use existing elements instead, provide `outputElementId` plus exactly one of
`inputElementId` or `suppliedTextElementId`. The supplied-text option is for
display-only blocks and does not support extractors.

## ASCIIMath Syntax

Wrap inline ASCIIMath in backticks.

```text
`x^2`
`a_n`
`(a+b)/(c+d)`
`sqrt(x)`
`sin(theta)`
`sum_(i=1)^n i`
`int_0^1 x^2 dx`
`[[a,b],[c,d]]`
```

Markdown text can be mixed with ASCIIMath. Calculation blocks use
`{@...@}` and are evaluated by the calculation filter:

```text
Step 1 = `3^2 + 4^2 = 25`

Answer = `z = 5`

Calculated hypotenuse: {@sqrt(3^2 + 4^2)@}
```

Markdown needs a blank line between paragraphs or displayed blocks. For
displayed, multiline ASCIIMath, put one backtick on a line by itself at the
start and end of the block:

```text
`
log_3(x+17) - 2 = log_3(2x)
log_3((x+17)/(2x)) = 2
(x+17)/(2x) = 9
x = 1
`
```

The calculation filter supports basic scientific calculations in radians,
including functions such as `sin`, `cos`, `tan`, `sqrt`, `log`, `mean`, `sum`,
and `prod`.

## Demo Extractors

The demo shows three extractor styles at once:

- `laststringremainderwhitespace` reads the last line beginning with `Answer =`.
- `lastcalc` reads the rendered result of the last calculation block.
- `allregexremainder` collects every line beginning with `Step n =` and returns
  the remainders as JSON.
- Additional `laststringremainderwhitespace` extractors read coefficient lines
  beginning with `a =` and `b =`.

Examples that do not contain the matching marker simply leave that extractor
output blank.
