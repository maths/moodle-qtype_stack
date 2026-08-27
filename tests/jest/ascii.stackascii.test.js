/** @jest-environment jsdom */

const mockMarkdown = jest.fn();
const mockCalculation = jest.fn();
const mockCas = jest.fn();
const mockLastexpr = jest.fn();
const mockLastblock = jest.fn();
const mockLastcalc = jest.fn();
const mockLastregexmatch = jest.fn();
const mockLastregexremainder = jest.fn();
const mockLaststringremainder = jest.fn();
const mockRegexallmatch = jest.fn();
const mockRegexallremainder = jest.fn();

jest.mock('../../corsscripts/ascii/filters/markdown.js', () => ({
    __esModule: true,
    default: (...args) => mockMarkdown(...args)
}));

jest.mock('../../corsscripts/ascii/filters/calculation.js', () => ({
    __esModule: true,
    default: (...args) => mockCalculation(...args)
}));

jest.mock('../../corsscripts/ascii/filters/cas.js', () => ({
    __esModule: true,
    default: (...args) => mockCas(...args)
}));

jest.mock('../../corsscripts/ascii/extractors/lastblock.js', () => ({
    __esModule: true,
    default: (...args) => mockLastblock(...args)
}));

jest.mock('../../corsscripts/ascii/extractors/lastcalc.js', () => ({
    __esModule: true,
    default: (...args) => mockLastcalc(...args)
}));

jest.mock('../../corsscripts/ascii/extractors/lastexpr.js', () => ({
    __esModule: true,
    default: (...args) => mockLastexpr(...args)
}));

jest.mock('../../corsscripts/ascii/extractors/lastregexmatch.js', () => ({
    __esModule: true,
    default: (...args) => mockLastregexmatch(...args)
}));

jest.mock('../../corsscripts/ascii/extractors/lastregexremainder.js', () => ({
    __esModule: true,
    default: (...args) => mockLastregexremainder(...args)
}));

jest.mock('../../corsscripts/ascii/extractors/laststringremainder.js', () => ({
    __esModule: true,
    default: (...args) => mockLaststringremainder(...args)
}));

jest.mock('../../corsscripts/ascii/extractors/allregexmatch.js', () => ({
    __esModule: true,
    default: (...args) => mockRegexallmatch(...args)
}));

jest.mock('../../corsscripts/ascii/extractors/allregexremainder.js', () => ({
    __esModule: true,
    default: (...args) => mockRegexallremainder(...args)
}));

import init from '../../corsscripts/ascii/stackascii.js';

describe('stackascii init', () => {
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
            style: {
                scrollBehavior: ''
            },
            listeners,
            classList: {
                add: jest.fn((className) => {
                    classes.add(className);
                }),
                remove: jest.fn((className) => {
                    classes.delete(className);
                }),
                contains: jest.fn((className) => classes.has(className))
            },
            addEventListener: jest.fn((eventName, handler) => {
                listeners[eventName] = handler;
            }),
            dispatchEvent: jest.fn()
        };
    }

    function setupEnvironment(inputValue, answerCount = 1) {
        const markdownInput = createElement('markdownInput', inputValue);
        const output = createElement('asciiContainerRow');
        const supplied = createElement('asciiSuppliedText');
        const answers = Array.from({ length: answerCount }, (_, index) => createElement(`answer${index + 1}`));

        const elements = {
            markdownInput,
            asciiContainerRow: output,
            asciiSuppliedText: supplied
        };
        for (const answer of answers) {
            elements[answer.id] = answer;
        }

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

        return { markdownInput, output, answers, elements };
    }

    beforeEach(() => {
        mockMarkdown.mockReset();
        mockCalculation.mockReset();
        mockCas.mockReset();
        mockLastexpr.mockReset();
        mockLastblock.mockReset();
        mockLastcalc.mockReset();
        mockLastregexmatch.mockReset();
        mockLastregexremainder.mockReset();
        mockLaststringremainder.mockReset();
        mockRegexallmatch.mockReset();
        mockRegexallremainder.mockReset();
        Object.defineProperty(window, 'parent', {
            value: {postMessage: jest.fn()},
            configurable: true
        });
        global.FRAME_ID = 'frame-1';
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

    test('runs filters and extractors, updates output, and dispatches change', () => {
        const env = setupEnvironment('  alpha  ', 1);

        mockMarkdown.mockImplementation((text, blockCollector, op) => {
            blockCollector.blocks.push({ type: 'markdown', raw: text });
            return `MD:${text}`;
        });
        mockCalculation.mockImplementation((text, blockCollector, op) => {
            blockCollector.blocks.push({ type: 'calculation', raw: text });
            return `CALC:${text}`;
        });
        mockLastexpr.mockImplementation((raw, blocks, op) => `EXTRACT:${raw}:${blocks.map((block) => block.type).join('|')}`);

        const operations = [
            { operation: 'filter', type: 'markdown', reset: 'false', display: 'true' },
            { operation: 'filter', type: 'calculation', reset: 'true'},
            { operation: 'extractor', type: 'unknown' }
        ];

        init(['markdownInput', 'answer1', 'answer2'], operations);

        expect(mockMarkdown).toHaveBeenCalledWith('  alpha  ', expect.any(Object), operations[0]);
        expect(mockCalculation).toHaveBeenCalledWith('  alpha  ', expect.any(Object), operations[1]);
        expect(mockLastexpr).toHaveBeenCalledWith('  alpha  ', [
            { type: 'markdown', raw: '  alpha  ' },
            { type: 'calculation', raw: '  alpha  ' }
        ], operations[2]);
        expect(env.output.innerHTML).toBe('MD:  alpha  ');
        expect(env.answers[0].value).toBe('EXTRACT:  alpha  :markdown|calculation');
        expect(env.answers[0].dispatchEvent).toHaveBeenCalledWith({ type: 'change' });
        expect(global.MathJax.typesetPromise).toHaveBeenCalledWith([env.output]);
    });

    test('runs filters and extractors, updates output, and dispatches change, no reset or display', () => {
        const env = setupEnvironment('  alpha  ', 2);

        mockMarkdown.mockImplementation((text, blockCollector, op) => {
            blockCollector.blocks = [{ type: 'markdown', raw: text }];
            return `MD:${text}`;
        });
        mockCalculation.mockImplementation((text, blockCollector, op) => {
            blockCollector.blocks = [{ type: 'calculation', raw: text }];
            return `CALC:${text}`;
        });
        mockLastexpr.mockImplementation((raw, blocks, op) => `EXTRACT:${raw}:${blocks.map((block) => block.type).join('|')}`);
        mockLastcalc.mockImplementation((raw, blocks, op) => `EXTRACTCALC:${raw}:${blocks.map((block) => block.type).join('|')}`);

        const operations = [
            { operation: 'filter', type: 'markdown' },
            { operation: 'extractor', type: 'lastexpr' },
            { operation: 'filter', type: 'calculation' },
            { operation: 'extractor', type: 'lastcalc' }
        ];

        init(['markdownInput', 'answer1', 'answer2'], operations);

        expect(mockMarkdown).toHaveBeenCalledWith('  alpha  ', expect.any(Object), operations[0]);
        expect(mockLastexpr).toHaveBeenCalledWith('  alpha  ', [
            { type: 'markdown', raw: '  alpha  ' }
        ], operations[1]);
        expect(mockCalculation).toHaveBeenCalledWith('MD:  alpha  ', expect.any(Object), operations[2]);
        expect(mockLastcalc).toHaveBeenCalledWith('  alpha  ', [
            { type: 'calculation', raw: 'MD:  alpha  ' }
        ], operations[3]);
        expect(env.output.innerHTML).toBe('CALC:MD:  alpha  ');
        expect(env.answers[0].value).toBe('EXTRACT:  alpha  :markdown');
        expect(env.answers[0].dispatchEvent).toHaveBeenCalledWith({ type: 'change' });
        expect(env.answers[1].value).toBe('EXTRACTCALC:  alpha  :calculation');
        expect(env.answers[1].dispatchEvent).toHaveBeenCalledWith({ type: 'change' });
        expect(global.MathJax.typesetPromise).toHaveBeenCalledWith([env.output]);
    });

    test('clears the answer input when an extractor returns ERROR', () => {
        const env = setupEnvironment('beta', 1);

        mockLaststringremainder.mockReturnValue('ERROR');

        const operations = [{ operation: 'extractor', type: 'laststringremainder' }];

        init(['markdownInput', 'answer1'], operations);

        expect(mockLaststringremainder).toHaveBeenCalledWith('beta', [], operations[0]);
        expect(env.answers[0].value).toBe('');
        expect(env.answers[0].dispatchEvent).not.toHaveBeenCalled();
        expect(env.output.innerHTML).toBe('beta');
    });

    test('debounces and rerenders when the input changes', () => {
        const env = setupEnvironment('gamma', 0);

        mockMarkdown.mockImplementation((text) => `MD:${text}`);

        const operations = [{ operation: 'filter', type: 'markdown', reset: 'false', display: 'false' }];

        init(['markdownInput'], operations);

        expect(env.markdownInput.addEventListener).toHaveBeenCalledWith('change', expect.any(Function));
        expect(mockMarkdown).toHaveBeenCalledTimes(1);

        env.markdownInput.listeners.change();

        expect(global.clearTimeout).toHaveBeenCalled();
        expect(global.setTimeout).toHaveBeenCalledWith(expect.any(Function), 100);
        expect(mockMarkdown).toHaveBeenCalledTimes(2);
        expect(env.output.innerHTML).toBe('MD:gamma');
    });

    test('registers one-way scroll sync and applies inbound scroll positions', () => {
        const env = setupEnvironment('delta', 0);
        env.output.scrollHeight = 300;
        env.output.clientHeight = 100;

        init(['markdownInput'], []);

        expect(window.parent.postMessage).toHaveBeenCalledWith(JSON.stringify({
            version: 'STACK-JS:1.6.0',
            type: 'track-input-scroll',
            name: 'markdownInput',
            'limit-to-question': true,
            src: 'frame-1'
        }), '*');

        window.dispatchEvent(new MessageEvent('message', {
            data: JSON.stringify({
                version: 'STACK-JS:1.6.0',
                type: 'input-scroll-position',
                name: 'markdownInput',
                position: 0.5,
                tgt: 'frame-1'
            })
        }));

        expect(env.output.scrollTop).toBe(100);

        window.parent.postMessage.mockClear();
        expect(env.output.listeners.scroll).toBeUndefined();
        env.output.scrollTop = 150;

        expect(window.parent.postMessage).not.toHaveBeenCalled();
        expect(env.output.scrollTop).toBe(150);

        window.dispatchEvent(new MessageEvent('message', {
            data: JSON.stringify({
                version: 'STACK-JS:1.6.0',
                type: 'input-scroll-position',
                name: 'markdownInput',
                position: 0.25,
                tgt: 'frame-1'
            })
        }));

        expect(env.output.scrollTop).toBe(50);
    });

    test('reapplies synced scroll position after MathJax 2 queue typesetting changes height', () => {
        const env = setupEnvironment('epsilon', 0);
        env.output.scrollHeight = 200;
        env.output.clientHeight = 100;
        delete global.MathJax.typesetPromise;
        global.MathJax.Hub.Queue.mockImplementation((entry) => {
            if (Array.isArray(entry)) {
                env.output.scrollHeight = 300;
                return;
            }
            if (typeof entry === 'function') {
                entry();
            }
        });

        init(['markdownInput'], []);

        window.dispatchEvent(new MessageEvent('message', {
            data: JSON.stringify({
                version: 'STACK-JS:1.6.0',
                type: 'input-scroll-position',
                name: 'markdownInput',
                position: 0.5,
                tgt: 'frame-1'
            })
        }));
        expect(env.output.scrollTop).toBe(100);

        env.output.scrollHeight = 200;
        env.markdownInput.listeners.change();

        expect(global.MathJax.Hub.Queue).toHaveBeenCalledWith(["Typeset", global.MathJax.Hub, 'asciiContainerRow']);
        expect(env.output.scrollTop).toBe(100);
    });
});
