/**
 * @jest-environment jsdom
 */

import {
    parsePlot,
    renderPlotPlaceholder,
    renderPlots,
    setPlotStrings
} from '../../corsscripts/ascii/plot/plot.js';

const testStrings = {
    asciistringplotempty: 'Plot block needs at least one curve or point.',
    asciistringplotfunctionforbidden: 'Function not allowed:',
    asciistringplotinvalidexpression: 'Invalid plot expression:',
    asciistringplotnodetypeforbidden: 'Expression syntax not allowed:',
    asciistringplotoperatorforbidden: 'Operator not allowed:',
    asciistringplotunknown: 'Unknown plot instruction:',
    asciistringplotxrange: 'Plot x range must increase.',
    asciistringplotyrange: 'Plot y range must increase.'
};

describe('plot helper', () => {
    beforeEach(() => {
        setPlotStrings(testStrings);
    });

    afterEach(() => {
        delete global.JXG;
        document.body.innerHTML = '';
    });

    test('parses ranges, dimensions, curves and points', () => {
        const config = parsePlot([
            'x: -5..5',
            'y: -3..10',
            'width: 640',
            'height: 300',
            'plot y=x^2-1 as parabola',
            'point (2,3) A'
        ].join('\n'));

        expect(config.xmin).toBe(-5);
        expect(config.xmax).toBe(5);
        expect(config.ymin).toBe(-3);
        expect(config.ymax).toBe(10);
        expect(config.width).toBe(640);
        expect(config.height).toBe(300);
        expect(config.curves).toHaveLength(1);
        expect(config.curves[0].expression).toBe('x^2-1');
        expect(config.curves[0].label).toBe('parabola');
        expect(config.points).toEqual([{ x: 2, y: 3, label: 'A' }]);
    });

    test('throws when no plottable items exist', () => {
        expect(() => parsePlot('x: -1..1')).toThrow(/at least one curve or point/);
    });

    test('uses injected translated error strings', () => {
        setPlotStrings({
            asciistringplotempty: 'TRANSLATED empty plot',
            asciistringplotfunctionforbidden: 'TRANSLATED function:',
            asciistringplotinvalidexpression: 'TRANSLATED expression:'
        });

        expect(() => parsePlot('x: -1..1')).toThrow('TRANSLATED empty plot');
        expect(() => parsePlot('plot y=evil(x)')).toThrow('TRANSLATED function: evil');

        const html = renderPlotPlaceholder('plot y=<script>');
        expect(html).toContain('TRANSLATED expression: &lt;script&gt;');
    });

    test('renders a placeholder and initialises a JSXGraph board', () => {
        const create = jest.fn();
        const initBoard = jest.fn(() => ({ create }));
        global.JXG = { JSXGraph: { initBoard } };

        document.body.innerHTML = renderPlotPlaceholder('x: -2..2\nplot y=x^2\npoint (1,1) A');

        renderPlots(document.body);

        expect(initBoard).toHaveBeenCalledWith(expect.stringMatching(/^stack-plot-/), expect.objectContaining({
            boundingbox: [-2, 10, 2, -10],
            axis: true,
            grid: true,
            showCopyright: false,
            showNavigation: false
        }));
        expect(create).toHaveBeenCalledWith('functiongraph', expect.any(Array), expect.any(Object));
        expect(create).toHaveBeenCalledWith('point', [1, 1], expect.objectContaining({
            name: 'A',
            fixed: true
        }));
    });

    test('renders parser errors as escaped text', () => {
        const html = renderPlotPlaceholder('plot y=<script>');

        expect(html).toContain('stack-plot-error');
        expect(html).not.toContain('<script>');
        expect(html).toContain('&lt;script&gt;');
    });
});
