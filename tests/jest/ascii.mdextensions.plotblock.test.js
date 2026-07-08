import plotBlock from '../../corsscripts/ascii/markdownitextensions/plotblock.js';

describe('plotBlock markdown-it extension', () => {
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

    test('registers the plot_block rule before paragraph', () => {
        const mdit = makeFakeMdit();

        plotBlock(mdit);

        expect(mdit.block.ruler.before).toHaveBeenCalledWith(
            'paragraph',
            'plot_block',
            expect.any(Function),
            { alt: ['paragraph', 'reference', 'blockquote', 'list'] }
        );
    });

    test('parses a plot block and emits content', () => {
        const mdit = makeFakeMdit();
        plotBlock(mdit);
        const rule = mdit.block.ruler.before.mock.calls[0][2];
        const state = makeState(['!!plot', 'x: -5..5', 'plot y=x^2', '!!plot', 'after']);

        const matched = rule(state, 0, state.bMarks.length, false);

        expect(matched).toBe(true);
        expect(state.line).toBe(4);
        expect(state.pushed).toHaveLength(1);
        expect(state.pushed[0]).toMatchObject({ type: 'plot_block', tag: '', nesting: 0 });
        expect(state.pushed[0].content).toBe('x: -5..5\nplot y=x^2');
        expect(state.pushed[0].map).toEqual([0, 4]);
        expect(state.pushed[0].markup).toBe('!!plot');
    });

    test('silent mode probes without consuming input', () => {
        const mdit = makeFakeMdit();
        plotBlock(mdit);
        const rule = mdit.block.ruler.before.mock.calls[0][2];
        const state = makeState(['!!plot', 'plot y=x', '!!plot']);

        const matched = rule(state, 0, state.bMarks.length, true);

        expect(matched).toBe(true);
        expect(state.line).toBe(0);
        expect(state.pushed).toHaveLength(0);
    });

    test('requires a closing marker', () => {
        const mdit = makeFakeMdit();
        plotBlock(mdit);
        const rule = mdit.block.ruler.before.mock.calls[0][2];
        const state = makeState(['!!plot', 'plot y=x']);

        expect(rule(state, 0, state.bMarks.length, false)).toBe(false);
        expect(state.pushed).toHaveLength(0);
        expect(state.line).toBe(0);
    });

    test('does not match marker text with extra content', () => {
        const mdit = makeFakeMdit();
        plotBlock(mdit);
        const rule = mdit.block.ruler.before.mock.calls[0][2];
        const state = makeState(['!!plot now', 'plot y=x', '!!plot']);

        expect(rule(state, 0, state.bMarks.length, false)).toBe(false);
        expect(state.pushed).toHaveLength(0);
    });
});
