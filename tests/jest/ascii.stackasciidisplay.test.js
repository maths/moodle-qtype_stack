/** @jest-environment jsdom */

const mockInitAscii = jest.fn();

jest.mock('../../corsscripts/ascii/stackascii.js', () => ({
    __esModule: true,
    default: (...args) => mockInitAscii(...args)
}));

jest.mock('../../corsscripts/stack-web/src/stack-web.css', () => ({}));
jest.mock('../../corsscripts/ascii/ASCIIMathTeXImg.js', () => ({}));

import StackAsciiDisplay from '../../corsscripts/stack-web/src/StackAsciiDisplay.js';

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
        expect(document.getElementById('asciiBlock').contains(display.inputElement)).toBe(true);
        expect(document.getElementById('asciiBlock').contains(display.outputElement)).toBe(true);
        expect(display.inputElement.id).not.toBe(display.outputElement.id);
        expect(mockInitAscii).toHaveBeenCalledWith(
            [display.inputElement.id, 'answer1'],
            expect.any(Array),
            { outputElementId: display.outputElement.id }
        );
    });

    test('container mode creates unique ids for multiple containers', () => {
        document.body.innerHTML = '<div id="asciiBlockA"></div><div id="asciiBlockB"></div>';

        const first = new StackAsciiDisplay({ containerId: 'asciiBlockA' });
        const second = new StackAsciiDisplay({ containerId: 'asciiBlockB' });

        expect(first.inputElement.id).not.toBe(second.inputElement.id);
        expect(first.outputElement.id).not.toBe(second.outputElement.id);
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

    test('container mode syncs output dimensions to resized input', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        display.inputElement.getBoundingClientRect = jest.fn(() => ({
            bottom: 150,
            height: 150,
            right: 300,
            width: 300
        }));
        display.resizeHandle.dispatchEvent(new MouseEvent('pointerdown', {
            bubbles: true,
            cancelable: true,
            clientX: 298,
            clientY: 148
        }));
        window.dispatchEvent(new MouseEvent('pointermove', {
            clientX: 478,
            clientY: 208
        }));

        expect(display.outputElement.style.width).toBe('480px');
        expect(display.outputElement.style.height).toBe('210px');
    });

    test('container mode expands input and output horizontally by dragging the resize handle', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        display.inputElement.getBoundingClientRect = jest.fn(() => ({
            bottom: 160,
            height: 160,
            right: 320,
            width: 320
        }));
        display.resizeHandle.dispatchEvent(new MouseEvent('pointerdown', {
            bubbles: true,
            cancelable: true,
            clientX: 318,
            clientY: 158
        }));
        window.dispatchEvent(new MouseEvent('pointermove', {
            clientX: 420,
            clientY: 200
        }));

        expect(display.inputElement.style.width).toBe('422px');
        expect(display.outputElement.style.width).toBe('422px');
        expect(display.outputElement.style.height).toBe('202px');
    });

    test('container mode allows uncapped horizontal expansion when maxWidth is omitted', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        display.inputElement.getBoundingClientRect = jest.fn(() => ({
            bottom: 160,
            height: 160,
            right: 320,
            width: 320
        }));
        display.resizeHandle.dispatchEvent(new MouseEvent('pointerdown', {
            bubbles: true,
            cancelable: true,
            clientX: 318,
            clientY: 158
        }));
        window.dispatchEvent(new MouseEvent('pointermove', {
            clientX: 1518,
            clientY: 158
        }));

        expect(display.inputElement.style.width).toBe('1520px');
        expect(display.outputElement.style.width).toBe('1520px');
        expect(display.container.style.width).toBe('3076px');
    });

    test('container mode clamps input, output, and container to configured maximums', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';

        const display = new StackAsciiDisplay({
            containerId: 'asciiBlock',
            maxWidth: 700,
            maxHeight: 300
        });

        display.inputElement.getBoundingClientRect = jest.fn(() => ({
            bottom: 200,
            height: 200,
            right: 300,
            width: 300
        }));
        display.resizeHandle.dispatchEvent(new MouseEvent('pointerdown', {
            bubbles: true,
            cancelable: true,
            clientX: 298,
            clientY: 198
        }));
        window.dispatchEvent(new MouseEvent('pointermove', {
            clientX: 700,
            clientY: 600
        }));

        expect(display.inputElement.style.width).toBe('332px');
        expect(display.inputElement.style.height).toBe('276px');
        expect(display.outputElement.style.width).toBe('332px');
        expect(display.outputElement.style.height).toBe('276px');
        expect(display.container.style.width).toBe('700px');
        expect(display.container.style.height).toBe('300px');
    });

    test('container mode ignores layout-only horizontal shrink after a user resize', () => {
        document.body.innerHTML = '<div id="asciiBlock"></div>';
        let resizeCallback = null;

        global.ResizeObserver = jest.fn().mockImplementation((callback) => {
            resizeCallback = callback;
            return { observe: jest.fn() };
        });

        const display = new StackAsciiDisplay({ containerId: 'asciiBlock' });

        display.inputElement.getBoundingClientRect = jest.fn(() => ({
            bottom: 150,
            height: 150,
            right: 300,
            width: 300
        }));
        display.resizeHandle.dispatchEvent(new MouseEvent('pointerdown', {
            bubbles: true,
            cancelable: true,
            clientX: 298,
            clientY: 148
        }));
        window.dispatchEvent(new MouseEvent('pointermove', {
            clientX: 478,
            clientY: 208
        }));
        window.dispatchEvent(new Event('pointerup'));

        display.inputElement.getBoundingClientRect = jest.fn(() => ({
            width: 360,
            height: 210
        }));
        resizeCallback();

        expect(display.inputElement.style.width).toBe('480px');
        expect(display.outputElement.style.width).toBe('480px');
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
