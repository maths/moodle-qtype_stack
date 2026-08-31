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

// Use StackAsciiDisplay with ID-based options (required format)
const display = new StackAsciiDisplay({
    containerId: 'ascii-block',
    inputElementId: 'input',
    operations: [
        { operation: 'filter', type: 'markdown', transforms: 'asciimath' },
        { operation: 'extractor', type: 'lastexpr', targetinput: 'answer1' }
    ]
});

```

