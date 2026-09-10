/** @jest-environment jsdom */

/**
 * Tests for standalone web mode in stackascii.js
 * Verifies that scroll sync is skipped when FRAME_ID is not set.
 */

import init from '../../corsscripts/ascii/stackascii.js';

describe('stackascii standalone mode', () => {
    let mockMarkdown = jest.fn();
    let getElementByIdSpy = null;

    function createElement(id, value = '') {
        const listeners = {};
        const classes = new Set();
        return {
            id,
            value,
            innerHTML: '',
            scrollTop: 0,
            scrollHeight: 0,
            clientHeight: 0,
            style: { scrollBehavior: '' },
            listeners,
            classList: {
                add: jest.fn((className) => classes.add(className)),
                remove: jest.fn((className) => classes.delete(className)),
                contains: jest.fn((className) => classes.has(className))
            },
            addEventListener: jest.fn((eventName, handler) => {
                listeners[eventName] = handler;
            }),
            dispatchEvent: jest.fn()
        };
    }

    function setupEnvironment(inputValue = '') {
        const markdownInput = createElement('markdownInput', inputValue);
        const output = createElement('asciiContainerRow');
        const supplied = createElement('asciiSuppliedText');

        const elements = {
            markdownInput,
            asciiContainerRow: output,
            asciiSuppliedText: supplied
        };

        getElementByIdSpy = jest.spyOn(document, 'getElementById').mockImplementation((id) => elements[id] || null);
        
        global.MathJax = {
            typesetPromise: jest.fn(() => Promise.resolve()),
            Hub: { Queue: jest.fn() }
        };
        global.Event = function Event(type) {
            return { type };
        };
        global.setTimeout = jest.fn((callback) => {
            callback();
            return 123;
        });
        global.clearTimeout = jest.fn();

        return { markdownInput, output, elements };
    }

    beforeEach(() => {
        mockMarkdown.mockReset();
        
        // Mock the markdown filter
        jest.mock('../../corsscripts/ascii/filters/markdown.js', () => ({
            __esModule: true,
            default: (...args) => mockMarkdown(...args)
        }));

        // Ensure FRAME_ID is not set for standalone mode tests
        delete global.FRAME_ID;
    });

    afterEach(() => {
        if (getElementByIdSpy) {
            getElementByIdSpy.mockRestore();
            getElementByIdSpy = null;
        }
        delete global.MathJax;
        delete global.Event;
        delete global.setTimeout;
        delete global.clearTimeout;
        delete global.FRAME_ID;
    });

    test('works in standalone mode without FRAME_ID', () => {
        const env = setupEnvironment('test');
        const operations = [
            { operation: 'filter', type: 'markdown' }
        ];

        // Should work without errors in standalone mode
        init(['markdownInput'], operations);

        expect(env.output.innerHTML).toBeTruthy();
        // MathJax should still run
        expect(global.MathJax.typesetPromise).toHaveBeenCalledWith([env.output]);
    });

    test('standalone mode: skips scroll sync when FRAME_ID not set', () => {
        const env = setupEnvironment('test');
        const operations = [];
        
        // Ensure no FRAME_ID
        delete global.FRAME_ID;
        const postMessageSpy = jest.spyOn(window.parent, 'postMessage');
        
        init(['markdownInput'], operations);
        
        // Should not register for scroll sync in standalone mode
        expect(postMessageSpy).not.toHaveBeenCalled();
    });

    test('standalone mode: supports multiple custom output elements', () => {
        const env = setupEnvironment();
        const inputA = createElement('inputA', 'first');
        const outputA = createElement('outputA');
        const inputB = createElement('inputB', 'second');
        const outputB = createElement('outputB');

        Object.assign(env.elements, {
            inputA,
            outputA,
            inputB,
            outputB
        });

        init(['inputA'], [], { outputElementId: 'outputA' });
        init(['inputB'], [], { outputElementId: 'outputB' });

        expect(outputA.innerHTML).toBe('first');
        expect(outputB.innerHTML).toBe('second');

        inputA.value = 'updated first';
        inputA.listeners.input();

        expect(outputA.innerHTML).toBe('updated first');
        expect(outputB.innerHTML).toBe('second');
    });

    test('standalone mode: renders supplied text when there is no input element', () => {
        const env = setupEnvironment();
        const supplied = createElement('suppliedA');
        const output = createElement('outputA');

        supplied.innerHTML = 'pre-supplied text';
        Object.assign(env.elements, {
            suppliedA: supplied,
            outputA: output
        });

        init([], [], {
            outputElementId: 'outputA',
            suppliedTextElementId: 'suppliedA'
        });

        expect(output.innerHTML).toBe('pre-supplied text');
    });

    test('standalone mode: does not run extractors without a live input element', () => {
        const env = setupEnvironment();
        const supplied = createElement('suppliedA');
        const output = createElement('outputA');
        const answer = createElement('answerA');

        supplied.innerHTML = 'first line\nextracted answer';
        Object.assign(env.elements, {
            suppliedA: supplied,
            outputA: output,
            answerA: answer
        });

        init(['answerA'], [{ operation: 'extractor', type: 'lastexpr' }], {
            outputElementId: 'outputA',
            suppliedTextElementId: 'suppliedA'
        });

        expect(output.innerHTML).toBe('first line\nextracted answer');
        expect(answer.value).toBe('');
        expect(answer.dispatchEvent).not.toHaveBeenCalled();
    });

    test('iframe mode: enables scroll sync when FRAME_ID is set', () => {
        const env = setupEnvironment('test');
        const operations = [];
        
        // Set FRAME_ID to simulate iframe mode
        global.FRAME_ID = 'test-frame';
        const postMessageSpy = jest.spyOn(window.parent, 'postMessage');
        
        init(['markdownInput'], operations);
        
        // Should register for scroll sync in iframe mode
        expect(postMessageSpy).toHaveBeenCalled();
        const callArgs = postMessageSpy.mock.calls[0];
        expect(callArgs[0]).toContain('"type":"track-input-scroll"');
    });

    test('iframe mode: always uses 100ms debounce', () => {
        const env = setupEnvironment('test');
        const operations = [
            { operation: 'filter', type: 'markdown' }
        ];

        init(['markdownInput'], operations);

        // Trigger input change
        env.markdownInput.listeners.change();
        
        expect(global.setTimeout).toHaveBeenCalledWith(expect.any(Function), 100);
    });
});
