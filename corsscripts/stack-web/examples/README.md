# STACK Web Library Examples

## Quick Test

```bash
cd /corsscripts
python3 -m http.server 8000
```

Open: http://localhost:8000/stack-web/examples/demo.html

Type `x^2 + y^2 = z^2` - should render math in real-time.

## Build Commands

```bash
# Build stack-web (standalone wrapper with CSS bundled)
cd /corsscripts/stack-web
npm run build
```

## Usage

```javascript
// Load the bundle
<script src="dist/stack-web.bundle.js"></script>

// Access the classes
const StackAsciiDisplay = window.StackWeb.default;

// Use unique IDs for each ASCII block on the page.
const display = new StackAsciiDisplay({
    containerId: 'ascii-block',
    inputElementId: 'input',
    outputElementId: 'ascii-output',
    operations: [
        { operation: 'filter', type: 'markdown', transforms: 'asciimath' },
        { operation: 'extractor', type: 'lastexpr', targetinput: 'answer1' }
    ]
});

```

For a display-only block with no live input, replace `inputElementId` with
`suppliedTextElementId`. The supplied element's HTML is used as the source text
for the rendered output. Extractors require a live input.
