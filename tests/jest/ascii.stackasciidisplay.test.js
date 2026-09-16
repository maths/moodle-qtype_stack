/** @jest-environment jsdom */

const mockInitAscii = jest.fn();

jest.mock('../../corsscripts/ascii/stackascii.js', () => ({
    __esModule: true,
    default: (...args) => mockInitAscii(...args)
}));

jest.mock('../../corsscripts/stack-web/src/stack-web.css', () => ({}));

import StackAsciiDisplay, {
    StackAsciiDisplay as NamedStackAsciiDisplay,
    ready
} from '../../corsscripts/stack-web/src/StackAsciiDisplay.js';

function setInputRect(display, rect) {
    display.inputElement.getBoundingClientRect = jest.fn(() => ({
        bottom: rect.bottom ?? rect.height,
        height: rect.height,
        right: rect.right ?? rect.width,
        width: rect.width
    }));
}

function mockResizeObserver() {
    let resizeCallback = null;
    const observer = {
        observe: jest.fn(),
        disconnect: jest.fn()
    };

    global.ResizeObserver = jest.fn().mockImplementation((callback) => {
        resizeCallback = callback;
        return observer;
    });

    return {
        observer,
        trigger: () => resizeCallback && resizeCallback()
    };
}

function resizeObservedInput(display, resizeObserver, rect) {
    setInputRect(display, rect);
    resizeObserver.trigger();
}

describe('StackAsciiDisplay', () => {
    let originalMathJax;
    let originalResizeObserver;

    beforeEach(() => {
        document.body.innerHTML = '';
        mockInitAscii.mockClear();
        originalMathJax = window.MathJax;
        originalResizeObserver = global.ResizeObserver;
        mockResizeObserver();
    });

    afterEach(() => {
        window.MathJax = originalMathJax;
        global.ResizeObserver = originalResizeObserver;
        jest.useRealTimers();
    });

    test('ready rejects missing callbacks', () => {
        expect(() => ready()).toThrow('StackWeb.ready: callback is required');
    });

    test('exports StackAsciiDisplay as the default and a named class', () => {
        expect(NamedStackAsciiDisplay).toBe(StackAsciiDisplay);
    });

    test('ready calls the callback when MathJax is available', () => {
        window.MathJax = {
            typesetPromise: jest.fn()
        };
        const callback = jest.fn();

        ready(callback);

        expect(callback).toHaveBeenCalledWith();
    });

    test('ready waits for MathJax before calling the callback', () => {
        jest.useFakeTimers();
        window.MathJax = undefined;
        const callback = jest.fn();

        ready(callback);
        expect(callback).not.toHaveBeenCalled();

        window.MathJax = {
            typesetPromise: jest.fn()
        };
        jest.advanceTimersByTime(100);

        expect(callback).toHaveBeenCalledWith();
    });

    test('container mode creates generated input and output elements', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div><input id="answer1">';

        const display = new StackAsciiDisplay({
            containerId: 'asciiBlock',
            initialText: '`x^2`',
            placeholder: 'Type here',
            operations: [
                { operation: 'filter', type: 'markdown', transforms: 'asciimath' },
                { operation: 'extractor', type: 'lastexpr', targetinput: 'answer1' }
            ]
        });

        expect(display.inputElement.tagName).toBe('TEXTAREA');
        expect(display.inputElement.value).toBe('`x^2`');
        expect(display.inputElement.placeholder).toBe('Type here');
        expect(display.outputElement.classList.contains('stack-ascii-output')).toBe(true);
        expect(display.outputElement.contains(display.renderedOutputElement)).toBe(true);
        expect(display.outputElement.contains(display.errorOutputElement)).toBe(true);
        expect(document.getElementById('asciiBlock').contains(display.inputElement)).toBe(true);
        expect(document.getElementById('asciiBlock').contains(display.outputElement)).toBe(true);
        expect(display.inputElement.id).not.toBe(display.outputElement.id);
        expect(mockInitAscii).toHaveBeenCalledWith(
            [display.inputElement.id, 'answer1'],
            expect.any(Array),
            {
                outputElementId: display.outputElement.id,
                shellElementId: display.outputElement.id,
                renderedOutputElementId: display.renderedOutputElement.id,
                errorOutputElementId: display.errorOutputElement.id,
                asciistrings: {}
            }
        );
    });

    test('imports bundled ASCIIMath parser globals', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        new StackAsciiDisplay({ containerId: 'asciiBlock' });

        expect(typeof window.AMparseMath).toBe('function');
        expect(typeof window.AMTparseAMtoTeX).toBe('function');
        expect(window.AMparseMath('x^2', true)).toContain('{x}^{{2}}');
    });

    test('container mode creates unique ids for multiple containers', () => {
        document.body.innerHTML = '<div id="asciiBlockA"></div><div id="asciiBlockB"></div>';

        const first = new StackAsciiDisplay({ containerId: 'asciiBlockA' });
        const second = new StackAsciiDisplay({ containerId: 'asciiBlockB' });

        expect(first.inputElement.id).not.toBe(second.inputElement.id);
        expect(first.outputElement.id).not.toBe(second.outputElement.id);
        expect(first.renderedOutputElement.id).not.toBe(second.renderedOutputElement.id);
        expect(first.errorOutputElement.id).not.toBe(second.errorOutputElement.id);
    });

    test('container mode applies configured container dimensions', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        const display = new StackAsciiDisplay({
            containerId: 'asciiBlock',
            initialWidth: '80%',
            initialHeight: 240,
            minWidth: 300,
            minHeight: '10rem',
            maxWidth: 900,
            maxHeight: '60vh'
        });

        expect(display.container.style.width).toBe('80%');
        expect(display.container.style.height).toBe('240px');
        expect(display.container.style.minWidth).toBe('300px');
        expect(display.container.style.minHeight).toBe('10rem');
        expect(display.container.style.maxWidth).toBe('900px');
        expect(display.container.style.maxHeight).toBe('60vh');
    });

    test('container mode does not mark the display as resized before textarea size changes', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';
        const resizeObserver = mockResizeObserver();

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });
        setInputRect(display, {
            height: 150,
            width: 300
        });

        display.inputElement.dispatchEvent(new MouseEvent('pointerdown', { bubbles: true }));

        expect(display.container.classList.contains('stack-ascii-display-resized')).toBe(false);
        expect(display.container.style.width).toBe('');
        expect(display.outputElement.style.width).toBe('');
        expect(display.outputElement.style.height).toBe('');
        expect(display.inputPane.style.width).toBe('');
        expect(display.outputPane.style.width).toBe('');
        expect(resizeObserver.observer.observe).toHaveBeenCalledWith(display.inputElement);
    });

    test('container mode ignores observed input size when unchanged from the baseline', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';
        const resizeObserver = mockResizeObserver();

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        resizeObservedInput(display, resizeObserver, {
            height: 0,
            width: 0
        });

        expect(display.container.classList.contains('stack-ascii-display-resized')).toBe(false);
        expect(display.outputElement.style.width).toBe('');
        expect(display.outputElement.style.height).toBe('');
        expect(display.inputPane.style.width).toBe('');
        expect(display.outputPane.style.width).toBe('');
    });

    test('container mode marks the display as resized when observed input size changes', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';
        const resizeObserver = mockResizeObserver();

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        resizeObservedInput(display, resizeObserver, {
            height: 210,
            width: 480
        });

        expect(display.container.classList.contains('stack-ascii-display-resized')).toBe(true);
        expect(display.container.style.width).toBe('max-content');
    });

    test('container mode syncs output dimensions to observed input resize', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';
        const resizeObserver = mockResizeObserver();

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        resizeObservedInput(display, resizeObserver, {
            height: 202,
            width: 422
        });

        expect(display.inputElement.style.width).toBe('422px');
        expect(display.inputPane.style.width).toBe('422px');
        expect(display.outputElement.style.width).toBe('422px');
        expect(display.outputPane.style.width).toBe('422px');
        expect(display.outputElement.style.height).toBe('202px');
        expect(display.outputPane.style.height).toBe('202px');
    });

    test('container mode allows uncapped observed horizontal expansion when maxWidth is omitted', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';
        const resizeObserver = mockResizeObserver();

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        resizeObservedInput(display, resizeObserver, {
            height: 160,
            width: 1520
        });

        expect(display.inputElement.style.width).toBe('1520px');
        expect(display.inputPane.style.width).toBe('1520px');
        expect(display.outputElement.style.width).toBe('1520px');
        expect(display.outputPane.style.width).toBe('1520px');
        expect(display.container.style.width).toBe('max-content');
    });

    test('container mode preserves configured maximums while mirroring observed size', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';
        const resizeObserver = mockResizeObserver();

        const display = new StackAsciiDisplay({
            containerId: 'asciiBlock',
            maxWidth: 700,
            maxHeight: 300
        });

        resizeObservedInput(display, resizeObserver, {
            height: 600,
            width: 700
        });

        expect(display.inputElement.style.width).toBe('700px');
        expect(display.inputElement.style.height).toBe('600px');
        expect(display.inputPane.style.width).toBe('700px');
        expect(display.inputPane.style.height).toBe('600px');
        expect(display.outputElement.style.width).toBe('700px');
        expect(display.outputElement.style.height).toBe('600px');
        expect(display.outputPane.style.width).toBe('700px');
        expect(display.outputPane.style.height).toBe('600px');
        expect(display.container.style.width).toBe('max-content');
        expect(display.container.style.maxWidth).toBe('700px');
        expect(display.container.style.maxHeight).toBe('300px');
        expect(display.container.style.height).toBe('fit-content');
    });

    test('container mode syncs observed input size changes after a user resize', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';
        const resizeObserver = mockResizeObserver();

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        resizeObservedInput(display, resizeObserver, {
            height: 210,
            width: 480
        });
        resizeObservedInput(display, resizeObserver, {
            width: 360,
            height: 210
        });

        expect(display.inputElement.style.width).toBe('360px');
        expect(display.inputPane.style.width).toBe('360px');
        expect(display.outputElement.style.width).toBe('360px');
        expect(display.outputPane.style.width).toBe('360px');
    });

    test('requires exactly one of containerId or outputElementId', () => {
        expect(() => new StackAsciiDisplay({})).toThrow(
            'StackAsciiDisplay: specify exactly one of containerId or outputElementId'
        );

        document.body.innerHTML = '<div id="container"></div><div id="output"></div>';
        expect(() => new StackAsciiDisplay({
            containerId: 'container',
            outputElementId: 'output'
        })).toThrow('StackAsciiDisplay: specify exactly one of containerId or outputElementId');
    });

    test('container mode rejects explicit source ids', () => {
        document.body.innerHTML = '<div id="container"></div><textarea id="input"></textarea>';

        expect(() => new StackAsciiDisplay({
            containerId: 'container',
            inputElementId: 'input'
        })).toThrow('StackAsciiDisplay: containerId cannot be used with inputElementId');
    });

    test('existing output mode creates textarea inside existing input mount', () => {
        document.body.innerHTML = '<div id="input">replace me</div><div id="output"></div>';

        const display = new StackAsciiDisplay({
            outputElementId: 'output',
            inputElementId: 'input',
            initialText: '`x^2`',
            placeholder: 'Type here'
        });

        expect(display.inputMountElement.id).toBe('input');
        expect(display.inputMountElement.classList.contains('stack-ascii-input-mount')).toBe(true);
        expect(display.inputElement.tagName).toBe('TEXTAREA');
        expect(display.inputElement.id).not.toBe('input');
        expect(display.inputElement.value).toBe('`x^2`');
        expect(display.inputElement.placeholder).toBe('Type here');
        expect(document.getElementById('input').contains(display.inputElement)).toBe(true);
        expect(display.suppliedTextElement).toBe(null);
        expect(display.outputElement.id).toBe('output');
        expect(display.outputElement.classList.contains('stack-ascii-output-mount')).toBe(true);
        expect(display.shellElement.classList.contains('stack-ascii-output')).toBe(true);
        expect(display.shellElement.contains(display.renderedOutputElement)).toBe(true);
        expect(display.shellElement.contains(display.errorOutputElement)).toBe(true);
        expect(document.getElementById('output').contains(display.shellElement)).toBe(true);
        expect(mockInitAscii).toHaveBeenCalledWith(
            [display.inputElement.id],
            [],
            {
                outputElementId: 'output',
                shellElementId: display.shellElement.id,
                renderedOutputElementId: display.renderedOutputElement.id,
                errorOutputElementId: display.errorOutputElement.id,
                asciistrings: {}
            }
        );
    });

    test('existing output mode creates hidden supplied text element from initialText', () => {
        document.body.innerHTML = '<section id="wrapper"><div id="output"></div></section>';

        const display = new StackAsciiDisplay({
            outputElementId: 'output',
            initialText: 'pre-supplied `x^2`',
            operations: [{ operation: 'filter', type: 'markdown', transforms: 'asciimath' }]
        });

        expect(display.inputElement).toBe(null);
        expect(display.outputElement.id).toBe('output');
        expect(display.outputElement.classList.contains('stack-ascii-output-mount')).toBe(true);
        expect(display.shellElement.classList.contains('stack-ascii-output')).toBe(true);
        expect(display.shellElement.contains(display.renderedOutputElement)).toBe(true);
        expect(display.shellElement.contains(display.errorOutputElement)).toBe(true);
        expect(document.getElementById('output').contains(display.shellElement)).toBe(true);
        expect(display.suppliedTextElement.hidden).toBe(true);
        expect(display.suppliedTextElement.innerHTML).toBe('pre-supplied `x^2`');
        expect(display.suppliedTextElement.id).toMatch(/^stack-ascii-output-\d+-supplied-text$/);
        expect(document.getElementById('wrapper').contains(display.suppliedTextElement)).toBe(true);
        expect(mockInitAscii).toHaveBeenCalledWith(
            [],
            expect.any(Array),
            {
                outputElementId: 'output',
                shellElementId: display.shellElement.id,
                renderedOutputElementId: display.renderedOutputElement.id,
                errorOutputElementId: display.errorOutputElement.id,
                suppliedTextElementId: display.suppliedTextElement.id,
                asciistrings: {}
            }
        );
    });

    test('existing output mode creates unique supplied text ids for static displays', () => {
        document.body.innerHTML = '<div id="outputA"></div><div id="outputB"></div>';

        const first = new StackAsciiDisplay({
            outputElementId: 'outputA',
            initialText: 'first'
        });
        const second = new StackAsciiDisplay({
            outputElementId: 'outputB',
            initialText: 'second'
        });

        expect(first.suppliedTextElement.id).not.toBe(second.suppliedTextElement.id);
        expect(first.suppliedTextElement.innerHTML).toBe('first');
        expect(second.suppliedTextElement.innerHTML).toBe('second');
    });

    test('existing static mode only uses initialText as the supplied source', () => {
        document.body.innerHTML = '<div id="supplied"></div><div id="output"></div>';
        document.getElementById('supplied').innerHTML = 'ignored';

        const display = new StackAsciiDisplay({
            outputElementId: 'output',
            initialText: 'used'
        });

        expect(display.suppliedTextElement.id).not.toBe('supplied');
        expect(display.suppliedTextElement.innerHTML).toBe('used');
        expect(mockInitAscii).toHaveBeenCalledWith(
            [],
            [],
            {
                outputElementId: 'output',
                shellElementId: display.shellElement.id,
                renderedOutputElementId: display.renderedOutputElement.id,
                errorOutputElementId: display.errorOutputElement.id,
                suppliedTextElementId: display.suppliedTextElement.id,
                asciistrings: {}
            }
        );
    });

    test('existing static initialText mode rejects extractors', () => {
        document.body.innerHTML = '<input id="answer1"><div id="output"></div>';

        expect(() => new StackAsciiDisplay({
            outputElementId: 'output',
            initialText: 'extract me',
            operations: [{ operation: 'extractor', type: 'lastexpr', targetinput: 'answer1' }]
        })).toThrow('StackAsciiDisplay: extractors require inputElementId');
    });
});
