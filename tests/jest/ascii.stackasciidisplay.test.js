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
    beforeEach(() => {
        document.body.innerHTML = '';
        mockInitAscii.mockClear();
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
