import asciimathBlock from '../../corsscripts/ascii/markdownitextensions/asciimathblock.js';

describe('asciimathBlock markdown-it extension', () => {
    function makeFakeMdit() {
        return {
            block: { ruler: { before: jest.fn() } }
        };
    }

    function makeState(lines) {
        const src = lines.join('\n');
        const bMarks = [];
        const tShift = [];
        const eMarks = [];

        let offset = 0;
        lines.forEach((line, index) => {
            bMarks[index] = offset;
            tShift[index] = line.trim() === '' ? -1 : 0;
            eMarks[index] = offset + line.length;
            offset += line.length + 1;
        });

        return {
            src,
            bMarks,
            tShift,
            eMarks,
            line: 0,
            pushed: [],
            push(type, tag, nesting) {
                const token = { type, tag, nesting };
                this.pushed.push(token);
                return token;
            }
        };
    }

    beforeEach(() => {
        global.window = global.window || {};
    });

    afterEach(() => {
        delete global.window;
    });

    test('registers the asciimath_block rule before paragraph', () => {
        const mdit = makeFakeMdit();

        asciimathBlock(mdit);

        expect(mdit.block.ruler.before).toHaveBeenCalledWith(
            'paragraph',
            'asciimath_block',
            expect.any(Function),
            { alt: ['paragraph', 'reference', 'blockquote', 'list'] }
        );
    });

    test('parses a block and emits a token with content and map', () => {
        const mdit = makeFakeMdit();
        asciimathBlock(mdit);
        const rule = mdit.block.ruler.before.mock.calls[0][2];
        const state = makeState(['`   ', 'x + 1', 'y + 2', '`', 'after']);

        const matched = rule(state, 0, state.bMarks.length, false);

        expect(matched).toBe(true);
        expect(state.line).toBe(4);
        expect(state.pushed).toHaveLength(1);
        expect(state.pushed[0]).toMatchObject({ type: 'asciimath_block', tag: '', nesting: 0 });
        expect(state.pushed[0].content).toBe('x + 1\ny + 2');
        expect(state.pushed[0].map).toEqual([0, 4]);
        expect(state.pushed[0].markup).toBe('`');
    });

    test('silent mode only probes and does not consume input', () => {
        const mdit = makeFakeMdit();
        asciimathBlock(mdit);
        const rule = mdit.block.ruler.before.mock.calls[0][2];
        const state = makeState(['`', 'x', '`']);

        const matched = rule(state, 0, state.bMarks.length, true);

        expect(matched).toBe(true);
        expect(state.line).toBe(0);
        expect(state.pushed).toHaveLength(0);
    });

    test('returns false for inline code-like backticks and fenced blocks', () => {
        const mdit = makeFakeMdit();
        asciimathBlock(mdit);
        const rule = mdit.block.ruler.before.mock.calls[0][2];

        const inlineState = makeState(['`inline code`']);
        const fencedState = makeState(['``', 'x', '``']);

        expect(rule(inlineState, 0, inlineState.bMarks.length, false)).toBe(false);
        expect(rule(fencedState, 0, fencedState.bMarks.length, false)).toBe(false);
        expect(inlineState.pushed).toHaveLength(0);
        expect(fencedState.pushed).toHaveLength(0);
    });

    test('returns false when closing marker is missing', () => {
        const mdit = makeFakeMdit();
        asciimathBlock(mdit);
        const rule = mdit.block.ruler.before.mock.calls[0][2];
        const state = makeState(['`', 'x + 1', 'y + 2']);

        expect(rule(state, 0, state.bMarks.length, false)).toBe(false);
        expect(state.pushed).toHaveLength(0);
        expect(state.line).toBe(0);
    });

    test('includes blank lines as empty content between markers', () => {
        const mdit = makeFakeMdit();
        asciimathBlock(mdit);
        const rule = mdit.block.ruler.before.mock.calls[0][2];
        const state = makeState(['`', 'x + 1', '', 'y + 2', '`']);

        const matched = rule(state, 0, state.bMarks.length, false);

        expect(matched).toBe(true);
        expect(state.pushed[0].content).toBe('x + 1\n\ny + 2');
    });
});