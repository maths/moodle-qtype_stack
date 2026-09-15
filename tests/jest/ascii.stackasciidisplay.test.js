/** @jest-environment jsdom */

const mockInitAscii = jest.fn();

jest.mock('../../corsscripts/ascii/stackascii.js', () => ({
    __esModule: true,
    default: (...args) => mockInitAscii(...args)
}));

jest.mock('../../corsscripts/stack-web/src/stack-web.css', () => ({}));

import StackAsciiDisplay from '../../corsscripts/stack-web/src/StackAsciiDisplay.js';

function setInputRect(display, rect) {
    display.inputElement.getBoundingClientRect = jest.fn(() => ({
        bottom: rect.bottom ?? rect.height,
        height: rect.height,
        right: rect.right ?? rect.width,
        width: rect.width
    }));
}

function startNativeResize(display, rect) {
    setInputRect(display, rect);
    display.inputElement.dispatchEvent(new MouseEvent('pointerdown', {
        bubbles: true,
        cancelable: true
    }));
}

function finishNativeResize(display, rect) {
    setInputRect(display, rect);
    window.dispatchEvent(new Event('pointerup'));
}

describe('StackAsciiDisplay', () => {
    let originalResizeObserver;

    beforeEach(() => {
        document.body.innerHTML = '';
        mockInitAscii.mockClear();
        originalResizeObserver = global.ResizeObserver;
    });

    afterEach(() => {
        global.ResizeObserver = originalResizeObserver;
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

    test('container mode marks the display as resized when native textarea resize starts', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });
        setInputRect(display, {
            bottom: 150,
            height: 150,
            right: 300,
            width: 300
        });

        display.inputElement.dispatchEvent(new MouseEvent('pointerdown', { bubbles: true }));

        expect(display.container.classList.contains('stack-ascii-display-resized')).toBe(true);
        expect(display.container.style.width).toBe('max-content');
    });

    test('container mode removes resized marker when native resize finishes unchanged', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });
        setInputRect(display, {
            bottom: 150,
            height: 150,
            right: 300,
            width: 300
        });

        display.inputElement.dispatchEvent(new MouseEvent('pointerdown', { bubbles: true }));
        window.dispatchEvent(new Event('pointerup'));

        expect(display.container.classList.contains('stack-ascii-display-resized')).toBe(false);
        expect(display.container.style.width).toBe('');
        expect(display.outputElement.style.width).toBe('');
        expect(display.outputElement.style.height).toBe('');
    });

    test('container mode keeps resized marker when native resize changes size', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        startNativeResize(display, {
            bottom: 150,
            height: 150,
            right: 300,
            width: 300
        });
        finishNativeResize(display, {
            bottom: 210,
            height: 210,
            right: 480,
            width: 480
        });

        expect(display.container.classList.contains('stack-ascii-display-resized')).toBe(true);
        expect(display.container.style.width).toBe('max-content');
    });

    test('container mode syncs output dimensions to resized input', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        startNativeResize(display, {
            bottom: 150,
            height: 150,
            right: 300,
            width: 300
        });
        finishNativeResize(display, {
            bottom: 210,
            height: 210,
            right: 480,
            width: 480
        });

        expect(display.outputElement.style.width).toBe('480px');
        expect(display.outputElement.style.height).toBe('210px');
    });

    test('container mode expands input and output horizontally after native textarea resize', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        startNativeResize(display, {
            bottom: 160,
            height: 160,
            right: 320,
            width: 320
        });
        finishNativeResize(display, {
            bottom: 202,
            height: 202,
            right: 422,
            width: 422
        });

        expect(display.inputElement.style.width).toBe('422px');
        expect(display.outputElement.style.width).toBe('422px');
        expect(display.outputElement.style.height).toBe('202px');
    });

    test('container mode allows uncapped native horizontal expansion when maxWidth is omitted', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        startNativeResize(display, {
            bottom: 160,
            height: 160,
            right: 320,
            width: 320
        });
        finishNativeResize(display, {
            bottom: 160,
            height: 160,
            right: 1520,
            width: 1520
        });

        expect(display.inputElement.style.width).toBe('1520px');
        expect(display.outputElement.style.width).toBe('1520px');
        expect(display.container.style.width).toBe('max-content');
    });

    test('container mode clamps input and output to configured maximums', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        const display = new StackAsciiDisplay({
            containerId: 'asciiBlock',
            maxWidth: 700,
            maxHeight: 300
        });

        startNativeResize(display, {
            bottom: 200,
            height: 200,
            right: 300,
            width: 300
        });
        finishNativeResize(display, {
            bottom: 600,
            height: 600,
            right: 700,
            width: 700
        });

        expect(display.inputElement.style.width).toBe('332px');
        expect(display.inputElement.style.height).toBe('276px');
        expect(display.outputElement.style.width).toBe('332px');
        expect(display.outputElement.style.height).toBe('276px');
        expect(display.container.style.width).toBe('max-content');
        expect(display.container.style.maxWidth).toBe('700px');
        expect(display.container.style.height).toBe('300px');
    });

    test('container mode syncs observed input size changes after a user resize', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';
        let resizeCallback = null;

        global.ResizeObserver = jest.fn().mockImplementation((callback) => {
            resizeCallback = callback;
            return { observe: jest.fn() };
        });

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        startNativeResize(display, {
            bottom: 150,
            height: 150,
            right: 300,
            width: 300
        });
        finishNativeResize(display, {
            bottom: 210,
            height: 210,
            right: 480,
            width: 480
        });

        setInputRect(display, {
            width: 360,
            height: 210
        });
        resizeCallback();

        expect(display.inputElement.style.width).toBe('360px');
        expect(display.outputElement.style.width).toBe('360px');
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
        })).toThrow('StackAsciiDisplay: containerId cannot be used with inputElementId or suppliedTextElementId');
    });

    test('existing output mode requires exactly one source id', () => {
        document.body.innerHTML = '<textarea id="input"></textarea><div id="supplied"></div><div id="output"></div>';

        expect(() => new StackAsciiDisplay({ outputElementId: 'output' })).toThrow(
            'StackAsciiDisplay: specify exactly one of inputElementId or suppliedTextElementId'
        );

        expect(() => new StackAsciiDisplay({
            outputElementId: 'output',
            inputElementId: 'input',
            suppliedTextElementId: 'supplied'
        })).toThrow('StackAsciiDisplay: specify exactly one of inputElementId or suppliedTextElementId');
    });

    test('existing supplied-text mode rejects extractors', () => {
        document.body.innerHTML = '<div id="supplied"></div><div id="output"></div>';

        expect(() => new StackAsciiDisplay({
            outputElementId: 'output',
            suppliedTextElementId: 'supplied',
            operations: [{ operation: 'extractor', type: 'lastexpr', targetinput: 'answer1' }]
        })).toThrow('StackAsciiDisplay: extractors require inputElementId');
    });
});
