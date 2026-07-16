/**
 * @jest-environment jsdom
 */

import {
    parsePlot,
    renderPlotPlaceholder,
    renderPlots,
    setPlotStrings
} from '../../corsscripts/ascii/filters/plotrules.js';

const testStrings = {
    asciistringplotempty: 'Plot block needs at least one curve or point.',
    asciistringplotfitdegree: 'Polynomial fit degree must be between 1 and 6.',
    asciistringplotfitformat: 'Fit points must use the form (x,y).',
    asciistringplotfitpoints: 'Fit needs more data points.',
    asciistringplotfitsingular: 'Polynomial fit cannot be calculated for these points.',
    asciistringplotfitvertical: 'Line fit needs at least two different x values.',
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
            'plot x=y^2 as sideways',
            'point (2,3) A'
        ].join('\n'));

        expect(config.xmin).toBe(-5);
        expect(config.xmax).toBe(5);
        expect(config.ymin).toBe(-3);
        expect(config.ymax).toBe(10);
        expect(config.width).toBe(640);
        expect(config.height).toBe(300);
        expect(config.curves).toHaveLength(2);
        expect(config.curves[0].axis).toBe('y');
        expect(config.curves[0].expression).toBe('x^2-1');
        expect(config.curves[0].label).toBe('parabola');
        expect(config.curves[1].axis).toBe('x');
        expect(config.curves[1].expression).toBe('y^2');
        expect(config.curves[1].label).toBe('sideways');
        expect(config.points).toEqual([{ x: 2, y: 3, label: 'A' }]);
        expect(config).not.toHaveProperty('fitRangePoints');
        expect(config).not.toHaveProperty('xRangeSet');
        expect(config).not.toHaveProperty('yRangeSet');
    });

    test('parses data points with a fitted line', () => {
        const config = parsePlot('fit line (1,2), (2,4), (3,6) as trend');

        expect(config.points).toEqual([
            { x: 1, y: 2, label: '' },
            { x: 2, y: 4, label: '' },
            { x: 3, y: 6, label: '' }
        ]);
        expect(config.curves).toHaveLength(1);
        expect(config.curves[0]).toEqual(expect.objectContaining({
            axis: 'y',
            expression: 'fit line',
            coefficients: expect.any(Array),
            label: 'trend'
        }));
        expect(config.curves[0].coefficients[0]).toBeCloseTo(0);
        expect(config.curves[0].coefficients[1]).toBeCloseTo(2);
    });

    test('parses polynomial fitted curves', () => {
        const quadratic = parsePlot('fit quadratic (0,1), (1,4), (2,9) as curve');
        const polynomial = parsePlot('fit polynomial 2 (0,1), (1,4), (2,9) as curve');

        expect(quadratic.curves[0]).toEqual(expect.objectContaining({
            axis: 'y',
            expression: 'fit quadratic',
            coefficients: expect.any(Array),
            label: 'curve'
        }));
        expect(quadratic.curves[0].coefficients[0]).toBeCloseTo(1);
        expect(quadratic.curves[0].coefficients[1]).toBeCloseTo(2);
        expect(quadratic.curves[0].coefficients[2]).toBeCloseTo(1);
        expect(polynomial.curves[0].coefficients[2]).toBeCloseTo(1);
    });

    test('expands default ranges to include fitted line data points', () => {
        const config = parsePlot('fit line (20,30), (30,60) as trend');

        expect(config.xmin).toBe(-10);
        expect(config.xmax).toBe(31);
        expect(config.ymin).toBe(-10);
        expect(config.ymax).toBe(63);
    });

    test('does not expand explicitly set ranges for fitted line data points', () => {
        const config = parsePlot([
            'x: -1..1',
            'fit line (20,30), (30,60) as trend',
            'y: -5..5'
        ].join('\n'));

        expect(config.xmin).toBe(-1);
        expect(config.xmax).toBe(1);
        expect(config.ymin).toBe(-5);
        expect(config.ymax).toBe(5);
    });

    test('rejects invalid fitted line data', () => {
        expect(() => parsePlot('fit line (1,2)')).toThrow('Fit needs more data points.');
        expect(() => parsePlot('fit line (1,2), (1,3)')).toThrow('Line fit needs at least two different x values.');
        expect(() => parsePlot('fit line (1,2), bad')).toThrow('Fit points must use the form (x,y).');
        expect(() => parsePlot('fit polynomial 7 (1,2), (2,4)')).toThrow('Polynomial fit degree must be between 1 and 6.');
        expect(() => parsePlot('fit quadratic (1,2), (2,4)')).toThrow('Fit needs more data points.');
        expect(() => parsePlot('fit quadratic (1,2), (1,3), (2,4)'))
            .toThrow('Polynomial fit cannot be calculated for these points.');
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

        document.body.innerHTML = renderPlotPlaceholder(
            'x: -2..2\nplot y=x^2 as parabola\nplot x=y^2 as sideways\npoint (1,1) A'
        );

        renderPlots(document.body);

        expect(initBoard).toHaveBeenCalledWith(expect.stringMatching(/^stack-plot-/), expect.objectContaining({
            boundingbox: [-2, 10, 2, -10],
            axis: true,
            grid: true,
            showCopyright: false,
            showNavigation: false
        }));
        expect(create).toHaveBeenCalledWith('functiongraph', expect.any(Array), expect.objectContaining({
            name: '',
            withLabel: false
        }));
        expect(create).toHaveBeenCalledWith('curve', expect.any(Array), expect.objectContaining({
            name: '',
            withLabel: false
        }));
        const curveArgs = create.mock.calls.find((call) => call[0] === 'curve')[1];
        expect(curveArgs[0](2)).toBe(4);
        expect(curveArgs[1](2)).toBe(2);
        expect(curveArgs[2]).toBe(-10);
        expect(curveArgs[3]).toBe(10);
        expect(create).toHaveBeenCalledWith('text', [-2, 4, 'parabola'], expect.objectContaining({
            fixed: true,
            anchorX: 'left',
            anchorY: 'bottom'
        }));
        expect(create).toHaveBeenCalledWith('text', [0, 0, 'sideways'], expect.objectContaining({
            fixed: true,
            anchorX: 'left',
            anchorY: 'bottom'
        }));
        expect(create).toHaveBeenCalledWith('point', [1, 1], expect.objectContaining({
            name: 'A',
            fixed: true
        }));
    });

    test('renders fitted lines and their data points', () => {
        const create = jest.fn();
        const initBoard = jest.fn(() => ({ create }));
        global.JXG = { JSXGraph: { initBoard } };

        document.body.innerHTML = renderPlotPlaceholder('fitline (1,2), (2,4), (3,6) as trend');

        renderPlots(document.body);

        const functionGraphArgs = create.mock.calls.find((call) => call[0] === 'functiongraph')[1];
        const functionGraphOptions = create.mock.calls.find((call) => call[0] === 'functiongraph')[2];
        expect(functionGraphArgs[0](4)).toBe(8);
        expect(functionGraphOptions).toEqual(expect.objectContaining({
            name: '',
            withLabel: false
        }));
        expect(create).toHaveBeenCalledWith('text', [-5, -10, 'trend'], expect.objectContaining({
            fixed: true,
            anchorX: 'left',
            anchorY: 'bottom'
        }));
        expect(create).toHaveBeenCalledWith('point', [1, 2], expect.objectContaining({ fixed: true }));
        expect(create).toHaveBeenCalledWith('point', [2, 4], expect.objectContaining({ fixed: true }));
        expect(create).toHaveBeenCalledWith('point', [3, 6], expect.objectContaining({ fixed: true }));
    });

    test('renders parser errors as escaped text', () => {
        const html = renderPlotPlaceholder('plot y=<script>');

        expect(html).toContain('stack-plot-error');
        expect(html).not.toContain('<script>');
        expect(html).toContain('&lt;script&gt;');
    });
});
