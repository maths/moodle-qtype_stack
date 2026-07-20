import markdownitrules from '../../corsscripts/ascii/filters/markdownitrules.js';

describe('markdownitrules filter', () => {
    beforeEach(() => {
        global.window = {
            AMparseMath: jest.fn((content) => content)
        };
    });

    afterEach(() => {
        delete global.window;
    });

    // We'll mock the mdit and options objects to test the plugin registration and rule effects.
    function makeFakeMdit() {
        const defaultCodeInline = jest.fn((tokens, idx) => `<code>${tokens[idx].content}</code>`);
        return {
            core: { ruler: { push: jest.fn() } },
            renderer: { rules: { code_inline: defaultCodeInline } },
            render: jest.fn((content) => `R(${content})`),
            renderInline: jest.fn((content) => `RI(${content})`),
            block: { ruler: { before: jest.fn() } }
        };
    }

    function setup(stateOverrides = {}) {
        const mdit = makeFakeMdit();
        const state = {
            transforms: [],
            transformLib: {},
            collector: null,
            ...stateOverrides
        };
        markdownitrules(mdit, { state });
        return { mdit, state };
    }

    test('registers reset_collector and renderer rules', () => {
        const mdit = makeFakeMdit();
        const options = { state: { transforms: [], transformLib: {}, collector: null } };
        markdownitrules(mdit, options);
        expect(typeof mdit.renderer.rules.code_inline).toBe('function');
        expect(typeof mdit.renderer.rules.asciimath_block).toBe('function');
        expect(typeof mdit.renderer.rules.math_inline).toBe('function');
        expect(typeof mdit.renderer.rules.math_block).toBe('function');
        expect(mdit.core.ruler.push).toHaveBeenCalledWith('reset_collector', expect.any(Function));
    });

    test('throws on unknown transform', () => {
        const mdit = makeFakeMdit();
        const options = { state: { transforms: ['notfound'], transformLib: {}, collector: null } };
        markdownitrules(mdit, options);
        // Simulate a call to applyTransforms
        const fn = mdit.renderer.rules.asciimath_block;
        expect(() => fn([{ content: 'x' }], 0)).toThrow(/unknown transform/);
    });

    test('reset_collector clears collector blocks', () => {
        const collector = { blocks: [{ type: 'old', raw: 'x' }] };
        const { mdit } = setup({ collector });

        const resetCollector = mdit.core.ruler.push.mock.calls[0][1];
        resetCollector();

        expect(collector.blocks).toEqual([]);
    });

    test('code_inline delegates to original code renderer and pushes collector block', () => {
        const collector = { blocks: [] };
        const { mdit } = setup({ collector });

        const rendered = mdit.renderer.rules.code_inline([{ content: 'x^2' }], 0, {}, {}, {});

        expect(rendered).toBe('<code>x^2</code>');
        expect(collector.blocks).toEqual([
            { type: 'code_inline', raw: 'x^2', rendered: '<code>x^2</code>' }
        ]);
    });

    test('asciimath_block applies transforms in order and pushes collector block', () => {
        const t1 = jest.fn(lines => lines.map(line => `T1:${line}`));
        const t2 = jest.fn(lines => lines.map(line => `T2:${line}`));
        const collector = { blocks: [] };
        const { mdit } = setup({
            transforms: ['t1', 't2'],
            transformLib: { t1, t2 },
            collector
        });

        const raw = ' a  \n\n  b ';
        const rendered = mdit.renderer.rules.asciimath_block([{ content: raw }], 0);

        expect(t1).toHaveBeenCalledWith(['a', 'b'], 'asciimath_block');
        expect(t2).toHaveBeenCalledWith(['T1:a', 'T1:b'], 'asciimath_block');
        expect(rendered).toBe('T2:T1:a\nT2:T1:b\n');
        expect(collector.blocks).toEqual([
            { type: 'asciimath_block', raw, rendered: 'T2:T1:a\nT2:T1:b\n' }
        ]);
    });

    test('math_inline wraps mdit.renderInline output and pushes collector block', () => {
        const collector = { blocks: [] };
        const { mdit } = setup({ collector });

        const rendered = mdit.renderer.rules.math_inline([{ content: 'x + 1' }], 0);

        expect(rendered).toBe('\\(RI(x + 1)\\)');
        expect(collector.blocks).toEqual([
            { type: 'math_inline', raw: 'x + 1', rendered: '\\(RI(x + 1)\\)' }
        ]);
    });

    test('math_block wraps mdit.render output and pushes collector block', () => {
        const collector = { blocks: [] };
        const { mdit } = setup({ collector });

        const rendered = mdit.renderer.rules.math_block([{ content: 'x + 1' }], 0);

        expect(rendered).toBe('\\[R(x + 1)\\]');
        expect(collector.blocks).toEqual([
            { type: 'math_block', raw: 'x + 1', rendered: '\\[R(x + 1)\\]' }
        ]);
    });

    test('math_block applies transforms and pushes collector block', () => {
        const t1 = jest.fn(lines => lines.map(line => line.toUpperCase()));
        const collector = { blocks: [] };
        const { mdit } = setup({
            transforms: ['t1'],
            transformLib: { t1 },
            collector
        });

        const raw = ' a\n\n b ';
        const rendered = mdit.renderer.rules.math_block([{ content: raw }], 0);

        expect(t1).toHaveBeenCalledWith(['a', 'b'], 'math_block');
        expect(rendered).toBe('A\nB\n');
        expect(collector.blocks).toEqual([
            { type: 'math_block', raw, rendered: 'A\nB\n' }
        ]);
    });

    test('splitBlock trims lines and removes blank lines before transforms', () => {
        const pass = jest.fn(lines => lines);
        const { mdit } = setup({ transforms: ['pass'], transformLib: { pass } });

        const asciiRaw = '  first  \r\n\r\n   second   \n   ';
        const mathRaw = '  left  \n\n   right   \n';

        const asciiRendered = mdit.renderer.rules.asciimath_block([{ content: asciiRaw }], 0);
        const mathRendered = mdit.renderer.rules.math_block([{ content: mathRaw }], 0);

        expect(pass).toHaveBeenNthCalledWith(1, ['first', 'second'], 'asciimath_block');
        expect(pass).toHaveBeenNthCalledWith(2, ['left', 'right'], 'math_block');
        expect(asciiRendered).toBe('first\nsecond\n');
        expect(mathRendered).toBe('left\nright\n');
    });

    test('applyTransforms with no content still returns trailing newline', () => {
        const pass = jest.fn(lines => lines);
        const { mdit } = setup({ transforms: ['pass'], transformLib: { pass } });

        const rendered = mdit.renderer.rules.math_block([{ content: '   \n\n  ' }], 0);

        expect(pass).toHaveBeenCalledWith([], 'math_block');
        expect(rendered).toBe('\n');
    });

    test('code_inline with transforms uses transform pipeline and does not call original renderer', () => {
        const up = jest.fn(lines => lines.map(line => line.toUpperCase()));
        const collector = { blocks: [] };
        const { mdit } = setup({ transforms: ['up'], transformLib: { up }, collector });

        const rendered = mdit.renderer.rules.code_inline([{ content: '\\frac{a}{b}' }], 0);

        expect(up).toHaveBeenCalledWith(['\\frac{a}{b}'], 'code_inline');
        expect(rendered).toBe('\\FRAC{A}{B}\n');
        expect(collector.blocks).toEqual([
            { type: 'code_inline', raw: '\\frac{a}{b}', rendered: '\\FRAC{A}{B}\n' }
        ]);
    });

    test('code_inline with transforms passes rule name to transform', () => {
        const spy = jest.fn(lines => lines);
        const { mdit } = setup({ transforms: ['spy'], transformLib: { spy } });

        mdit.renderer.rules.code_inline([{ content: 'x^{2}' }], 0);

        expect(spy).toHaveBeenCalledWith(['x^{2}'], 'code_inline');
    });

});
