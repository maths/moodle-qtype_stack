/**
 * @jest-environment jsdom
 */

import {
    parsePlot,
    renderPlotPlaceholder,
    renderPlots
} from '../../corsscripts/ascii/filters/plotrules.js';
import { setAsciiStrings } from '../../corsscripts/ascii/asciihelper.js';

const testStrings = {
    asciistringplotempty: 'Plot block needs one equation.',
    asciistringplotfunctionforbidden: 'Function not allowed:',
    asciistringplotinvalidexpression: 'Invalid plot expression:',
    asciistringplotmultiple: 'Plot block can only contain one equation.',
    asciistringplotnodetypeforbidden: 'Expression syntax not allowed:',
    asciistringplotoperatorforbidden: 'Operator not allowed:',
    asciistringplotsymbolforbidden: 'Symbol not allowed:',
    asciistringplotunknown: 'Unknown plot instruction:',
    asciistringplotxrange: 'Plot x range must increase.',
    asciistringplotyrange: 'Plot y range must increase.'
};

describe('plot helper', () => {
    beforeEach(() => {
        setAsciiStrings(testStrings);
    });

    afterEach(() => {
        delete global.JXG;
        document.body.innerHTML = '';
    });

    test('parses ranges and one equation', () => {
        const config = parsePlot([
            '# Graph window.',
            'x: -5..5',
            'y: -3..10',
            'y=x^2-1'
        ].join('\n'));

        expect(config.xmin).toBe(-5);
        expect(config.xmax).toBe(5);
        expect(config.ymin).toBe(-3);
        expect(config.ymax).toBe(10);
        expect(config.curves).toHaveLength(1);
        expect(config.curves[0].expression).toBe('x^2-1');
        expect(config.curves[0].compiled.evaluate({ x: 3 })).toBe(8);
    });

    test('parses allowed functions and constants in equations', () => {
        const config = parsePlot('y=sin(x)+pi');

        expect(config.curves[0].compiled.evaluate({ x: 0 })).toBeCloseTo(Math.PI);
    });

    test('throws when no equation exists', () => {
        expect(() => parsePlot('x: -1..1')).toThrow('Plot block needs one equation.');
    });

    test('validates ranges and expressions', () => {
        expect(() => parsePlot('x: 1..-1\ny=x')).toThrow('Plot x range must increase.');
        expect(() => parsePlot('y: 1..-1\ny=x')).toThrow('Plot y range must increase.');
        expect(() => parsePlot('y=evil(x)')).toThrow('Function not allowed: evil');
        expect(() => parsePlot('y=t^2')).toThrow('Symbol not allowed: t');
    });

    test('uses injected translated error strings', () => {
        setAsciiStrings({
            asciistringplotempty: 'TRANSLATED empty plot',
            asciistringplotfunctionforbidden: 'TRANSLATED function:',
            asciistringplotinvalidexpression: 'TRANSLATED expression:'
        });

        expect(() => parsePlot('x: -1..1')).toThrow('TRANSLATED empty plot');
        expect(() => parsePlot('y=evil(x)')).toThrow('TRANSLATED function: evil');

        const html = renderPlotPlaceholder('y=<script>');
        expect(html).toContain('TRANSLATED expression: &lt;script&gt;');
    });

    test('renders a placeholder and initialises a JSXGraph board', () => {
        const create = jest.fn();
        const initBoard = jest.fn(() => ({ create }));
        global.JXG = { JSXGraph: { initBoard } };

        document.body.innerHTML = renderPlotPlaceholder('x: -2..2\ny=x^2');

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
        const functionGraphArgs = create.mock.calls.find((call) => call[0] === 'functiongraph')[1];
        expect(functionGraphArgs[0](4)).toBe(16);
        expect(functionGraphArgs[1]).toBe(-2);
        expect(functionGraphArgs[2]).toBe(2);
    });

    test('renders parser errors as escaped text', () => {
        const html = renderPlotPlaceholder('y=<script>');

        expect(html).toContain('stack-plot-error');
        expect(html).not.toContain('<script>');
        expect(html).toContain('&lt;script&gt;');
    });
});
