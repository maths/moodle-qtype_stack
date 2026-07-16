const mockRenderPlotPlaceholder = jest.fn((code) => `<plot>${code}</plot>`);

jest.mock('../../corsscripts/ascii/filters/plotrules.js', () => ({
    __esModule: true,
    renderPlotPlaceholder: (code) => mockRenderPlotPlaceholder(code)
}));

import plot from '../../corsscripts/ascii/filters/plot.js';

describe('plot filter', () => {
    beforeEach(() => {
        mockRenderPlotPlaceholder.mockClear();
    });

    test('replaces paired plot delimiters with placeholders and records blocks', () => {
        const collector = { blocks: [], isHTML: false };
        const text = [
            'before',
            '!!p',
            'x: -5..5',
            'plot y=x^2',
            '!!p',
            'after'
        ].join('\n');

        const rendered = plot(text, collector);

        expect(rendered).toBe([
            'before',
            '<plot>x: -5..5\nplot y=x^2</plot>',
            'after'
        ].join('\n'));
        expect(mockRenderPlotPlaceholder).toHaveBeenCalledWith('x: -5..5\nplot y=x^2');
        expect(collector.isHTML).toBe(true);
        expect(collector.blocks).toEqual([
            {
                type: 'plot_block',
                raw: 'x: -5..5\nplot y=x^2',
                rendered: '<plot>x: -5..5\nplot y=x^2</plot>'
            }
        ]);
    });

    test('allows whitespace around plot markers', () => {
        const rendered = plot('  !!p  \nplot y=x\n  !!p', { blocks: [], isHTML: false });

        expect(rendered).toBe('<plot>plot y=x</plot>');
    });

    test('leaves unmatched markers as ordinary text', () => {
        const collector = { blocks: [{ type: 'old' }], isHTML: true };

        const rendered = plot('before\n!!p\nplot y=x', collector);

        expect(rendered).toBe('before\n!!p\nplot y=x');
        expect(mockRenderPlotPlaceholder).not.toHaveBeenCalled();
        expect(collector.isHTML).toBe(false);
        expect(collector.blocks).toEqual([]);
    });

    test('handles multiple plot blocks', () => {
        const collector = { blocks: [], isHTML: false };

        const rendered = plot('!!p\nplot y=x\n!!p\nmid\n!!p\npoint (1,2)\n!!p', collector);

        expect(rendered).toBe('<plot>plot y=x</plot>\nmid\n<plot>point (1,2)</plot>');
        expect(collector.blocks).toHaveLength(2);
        expect(collector.blocks[0].raw).toBe('plot y=x');
        expect(collector.blocks[1].raw).toBe('point (1,2)');
    });
});
