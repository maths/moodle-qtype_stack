// This file is part of Stack - https://stack.maths.ed.ac.uk
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This is part of the free text input/ ASCII display block.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import math from '../mathjs.min.js';

// Student syntax is intentionally small:
//   x: -5..5
//   y: -3..10
//   plot y=x^2-1
//   plot x=y^2
//   point (2,3) A
//   fit line (1,2), (2,3), (3,5) as trend
// Each !!p block is parsed into one of these config objects, then rendered
// by JSXGraph after markdown has inserted the placeholder HTML into the page.
const defaultConfig = {
    xmin: -10,
    xmax: 10,
    ymin: -10,
    ymax: 10,
    width: 500,
    height: 350,
    axes: true,
    grid: true,
    curves: [],
    points: []
};

const maxPolynomialDegree = 6;

// Keep plotting expressions in the same spirit as the calculation filter:
// allow ordinary school-level functions/operators, but reject assignments,
// strings, object access, indexing, and other general JavaScript-like syntax.
const allowed = {
    functions: new Set([
        'sin', 'cos', 'tan',
        'asin', 'acos', 'atan',
        'sqrt',
        'log', 'log10',
        'exp',
        'abs', 'floor', 'ceil', 'round',
        'mod', 'min', 'max'
    ]),

    operators: new Set([
        'add',
        'subtract',
        'multiply',
        'divide',
        'pow',
        'unaryMinus',
        'unaryPlus',
        'mod'
    ]),

    nodetypes: new Set([
        'ConstantNode',
        'ParenthesisNode',
        'OperatorNode',
        'FunctionNode',
        'SymbolNode'
    ])
};

let plotStrings = {};
let nextPlotId = 1;
const pendingPlots = new Map();

/**
 * Install translated plot strings supplied by PHP.
 *
 * @param {Object} strings translated message templates.
 */
export function setPlotStrings(strings = {}) {
    plotStrings = { ...strings };
}

/**
 * Render a markdown plot token to a placeholder. The board is initialised after
 * stackascii.js has inserted the rendered HTML into the document.
 *
 * @param {string} code raw plot block content.
 * @returns {string} HTML placeholder.
 */
export function renderPlotPlaceholder(code) {
    let config;
    try {
        config = parsePlot(code);
    } catch (error) {
        return '<pre class="stack-plot-error">' + escapeHTML(error.message) + '</pre>';
    }

    const id = 'stack-plot-' + nextPlotId++;
    pendingPlots.set(id, config);

    // Scripts inserted via innerHTML do not run, so markdown rendering only
    // creates a stable placeholder. stackascii.js calls renderPlots() after
    // output.innerHTML is set, and that function creates the JSXGraph board.
    return '<div class="stack-plot" data-stack-plot-id="' + id + '">' +
        '<div id="' + id + '" class="jxgbox stack-plot-board" style="' +
        'width:' + config.width + 'px;height:' + config.height + 'px;"></div>' +
        '</div>';
}

/**
 * Initialise any plot placeholders under a rendered ASCII block.
 *
 * @param {HTMLElement} container rendered ASCII output container.
 */
export function renderPlots(container) {
    if (!container || typeof JXG === 'undefined') {
        return;
    }

    const placeholders = container.querySelectorAll('.stack-plot[data-stack-plot-id]');
    placeholders.forEach((placeholder) => {
        const id = placeholder.dataset.stackPlotId;
        const config = pendingPlots.get(id);
        pendingPlots.delete(id);

        if (!config) {
            return;
        }

        try {
            // JSXGraph uses [left, top, right, bottom] for the bounding box.
            const board = JXG.JSXGraph.initBoard(id, {
                boundingbox: [config.xmin, config.ymax, config.xmax, config.ymin],
                axis: config.axes,
                grid: config.grid,
                showCopyright: false,
                showNavigation: false
            });

            config.curves.forEach((curve) => {
                if (curve.axis === 'x') {
                    // JSXGraph's functiongraph is always y=f(x), so x=f(y)
                    // is drawn as a parametric curve: [x(y), y].
                    board.create('curve', [
                        function(y) {
                            return evaluateCurve(curve, 'y', y);
                        },
                        function(y) {
                            return y;
                        },
                        config.ymin,
                        config.ymax
                    ], {
                        name: '',
                        withLabel: false
                    });
                    createCurveLabel(board, curve, config);
                    return;
                }

                // mathjs compiled expressions are evaluated with only x in scope.
                board.create('functiongraph', [
                    function(x) {
                        return evaluateCurve(curve, 'x', x);
                    },
                    config.xmin,
                    config.xmax
                ], {
                    name: '',
                    withLabel: false
                });
                createCurveLabel(board, curve, config);
            });

            config.points.forEach((point) => {
                board.create('point', [point.x, point.y], {
                    name: point.label || '',
                    fixed: true,
                    withLabel: Boolean(point.label)
                });
            });
        } catch (error) {
            placeholder.innerHTML = '<pre class="stack-plot-error">' + escapeHTML(error.message) + '</pre>';
        }
    });
}

/**
 * Parse a concise plot instruction block.
 *
 * @param {string} code raw plot block content.
 * @returns {Object} plot configuration.
 */
export function parsePlot(code) {
    const config = {
        ...defaultConfig,
        curves: [],
        points: []
    };
    // Parser-only state is kept out of the returned config. It lets fitted
    // data expand default ranges without leaking implementation flags.
    const state = {
        fitRangePoints: [],
        xRangeSet: false,
        yRangeSet: false
    };

    const lines = code.split(/\r?\n/)
        .map(line => line.trim())
        .filter(line => line !== '' && !line.startsWith('#'));

    lines.forEach((line) => parseLine(line, config, state));
    expandFitRanges(config, state);

    // Validate the complete config after all lines are read so ranges can be
    // given before or after curves.
    if (config.xmin >= config.xmax) {
        throw plotError('asciistringplotxrange');
    }
    if (config.ymin >= config.ymax) {
        throw plotError('asciistringplotyrange');
    }
    if (config.curves.length === 0 && config.points.length === 0) {
        throw plotError('asciistringplotempty');
    }

    return config;
}

/**
 * Parse one non-empty plot instruction line into the shared config.
 *
 * @param {string} line trimmed instruction line.
 * @param {Object} config plot configuration being built.
 * @param {Object} state parser-only state for explicit ranges and fitted data.
 */
function parseLine(line, config, state) {
    if (/^axes?$/i.test(line)) {
        config.axes = true;
        return;
    }
    if (/^no\s+axes?$/i.test(line)) {
        config.axes = false;
        return;
    }
    if (/^grid$/i.test(line)) {
        config.grid = true;
        return;
    }
    if (/^no\s+grid$/i.test(line)) {
        config.grid = false;
        return;
    }

    const range = line.match(/^([xy])\s*:\s*(-?\d+(?:\.\d+)?)\s*\.\.\s*(-?\d+(?:\.\d+)?)$/i);
    if (range) {
        const axis = range[1].toLowerCase();
        config[axis + 'min'] = parseFloat(range[2]);
        config[axis + 'max'] = parseFloat(range[3]);
        // Explicit ranges win over any automatic range expansion from data.
        state[axis + 'RangeSet'] = true;
        return;
    }

    const dimension = line.match(/^(width|height)\s*:\s*(\d+)$/i);
    if (dimension) {
        const value = parseInt(dimension[2], 10);
        config[dimension[1].toLowerCase()] = Math.max(100, Math.min(1200, value));
        return;
    }

    const lineFit = line.match(/^(?:fit\s+line|fitline|linefit)\s+(.+)$/i);
    if (lineFit) {
        addPolynomialFit(lineFit[1], config, state, 1, 'fit line');
        return;
    }

    const namedPolynomialFit = line.match(/^fit\s+(quadratic|cubic)\s+(.+)$/i);
    if (namedPolynomialFit) {
        const degree = namedPolynomialFit[1].toLowerCase() === 'quadratic' ? 2 : 3;
        addPolynomialFit(
            namedPolynomialFit[2],
            config,
            state,
            degree,
            'fit ' + namedPolynomialFit[1].toLowerCase()
        );
        return;
    }

    const polynomialFit = line.match(/^(?:fit\s+polynomial|fitpoly|polyfit)\s+(\d+)\s+(.+)$/i);
    if (polynomialFit) {
        addPolynomialFit(
            polynomialFit[2],
            config,
            state,
            parseInt(polynomialFit[1], 10),
            'fit polynomial'
        );
        return;
    }

    const point = line.match(/^point\s*\(\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)(?:\s+(.+))?$/i);
    if (point) {
        config.points.push({
            x: parseFloat(point[1]),
            y: parseFloat(point[2]),
            label: point[3] || ''
        });
        return;
    }

    const curve = line.match(/^(?:plot\s+)?(?:(x|y)\s*=\s*|f\s*\(\s*x\s*\)\s*=\s*)?(.+)$/i);
    if (curve) {
        // A bare expression is accepted as shorthand for "plot y=...".
        addCurve(curve[2], config, curve[1] ? curve[1].toLowerCase() : 'y');
        return;
    }

    throw plotError('asciistringplotunknown', line);
}

/**
 * Add a student-entered expression curve to the plot config.
 *
 * @param {string} expression curve expression, possibly followed by "as label".
 * @param {Object} config plot configuration being built.
 * @param {string} axis dependent axis, either "x" or "y".
 */
function addCurve(expression, config, axis = 'y') {
    const { raw, label } = splitLabel(expression);

    try {
        const node = math.parse(raw);
        validate(node);
        // Store the compiled expression once; JSXGraph can then evaluate it
        // repeatedly while sampling the curve.
        config.curves.push({
            axis,
            expression: raw,
            compiled: node.compile(),
            label
        });
    } catch (error) {
        if (error && error.stackPlotError) {
            throw error;
        }
        throw plotError('asciistringplotinvalidexpression', raw);
    }
}

/**
 * Add data points and a polynomial least-squares fit to the plot config.
 *
 * @param {string} expression point list, possibly followed by "as label".
 * @param {Object} config plot configuration being built.
 * @param {Object} state parser-only state for range expansion.
 * @param {number} degree polynomial degree to fit.
 * @param {string} fitType display/debug name for the fitted curve.
 */
function addPolynomialFit(expression, config, state, degree, fitType) {
    const { raw, label } = splitLabel(expression);
    const points = parsePointList(raw);

    if (degree < 1 || degree > maxPolynomialDegree) {
        throw plotError('asciistringplotfitdegree');
    }
    if (points.length < degree + 1) {
        throw plotError('asciistringplotfitpoints');
    }
    if (degree === 1 && points.every((point) => point.x === points[0].x)) {
        throw plotError('asciistringplotfitvertical');
    }

    const coefficients = fitPolynomial(points, degree);

    // A fit command draws both the original data points and the fitted curve.
    config.points.push(...points.map((point) => ({
        x: point.x,
        y: point.y,
        label: ''
    })));
    state.fitRangePoints.push(...points);
    config.curves.push({
        axis: 'y',
        expression: fitType,
        coefficients,
        label
    });
}

/**
 * Calculate polynomial least-squares coefficients for a point set.
 *
 * @param {Object[]} points data points with x and y properties.
 * @param {number} degree polynomial degree to fit.
 * @returns {number[]} coefficients in ascending power order.
 */
function fitPolynomial(points, degree) {
    const matrix = [];
    const rhs = [];

    // Build the normal equations for least-squares polynomial regression.
    // coefficients[0] is the constant term, coefficients[1] multiplies x, etc.
    for (let row = 0; row <= degree; row++) {
        matrix[row] = [];
        for (let col = 0; col <= degree; col++) {
            matrix[row][col] = points.reduce((sum, point) => sum + Math.pow(point.x, row + col), 0);
        }
        rhs[row] = points.reduce((sum, point) => sum + point.y * Math.pow(point.x, row), 0);
    }

    return solveLinearSystem(matrix, rhs);
}

/**
 * Solve a small dense linear system using Gaussian elimination.
 *
 * @param {number[][]} matrix square coefficient matrix.
 * @param {number[]} rhs right-hand side vector.
 * @returns {number[]} solution vector.
 */
function solveLinearSystem(matrix, rhs) {
    const size = rhs.length;
    const augmented = matrix.map((row, index) => [...row, rhs[index]]);

    for (let pivot = 0; pivot < size; pivot++) {
        // Partial pivoting keeps the small systems used here reasonably stable.
        let pivotRow = pivot;
        for (let row = pivot + 1; row < size; row++) {
            if (Math.abs(augmented[row][pivot]) > Math.abs(augmented[pivotRow][pivot])) {
                pivotRow = row;
            }
        }

        if (Math.abs(augmented[pivotRow][pivot]) < 1e-12) {
            throw plotError('asciistringplotfitsingular');
        }

        [augmented[pivot], augmented[pivotRow]] = [augmented[pivotRow], augmented[pivot]];

        for (let row = pivot + 1; row < size; row++) {
            const factor = augmented[row][pivot] / augmented[pivot][pivot];
            for (let col = pivot; col <= size; col++) {
                augmented[row][col] -= factor * augmented[pivot][col];
            }
        }
    }

    const solution = new Array(size);
    for (let row = size - 1; row >= 0; row--) {
        let value = augmented[row][size];
        for (let col = row + 1; col < size; col++) {
            value -= augmented[row][col] * solution[col];
        }
        solution[row] = value / augmented[row][row];
    }

    return solution;
}

/**
 * Expand default axes so fitted data points are visible.
 *
 * @param {Object} config plot configuration being built.
 * @param {Object} state parser-only state for explicit ranges and fitted data.
 */
function expandFitRanges(config, state) {
    if (state.fitRangePoints.length === 0) {
        return;
    }

    // Only axes left at their defaults are expanded to include fitted data.
    if (!state.xRangeSet) {
        expandAxisRange(config, 'x', state.fitRangePoints.map((point) => point.x));
    }
    if (!state.yRangeSet) {
        expandAxisRange(config, 'y', state.fitRangePoints.map((point) => point.y));
    }
}

/**
 * Expand one axis if the supplied values exceed the current visible range.
 *
 * @param {Object} config plot configuration being built.
 * @param {string} axis axis name, either "x" or "y".
 * @param {number[]} values values that should be visible on this axis.
 */
function expandAxisRange(config, axis, values) {
    const min = Math.min(...values);
    const max = Math.max(...values);

    if (min >= config[axis + 'min'] && max <= config[axis + 'max']) {
        return;
    }

    const padding = getRangePadding(min, max);
    config[axis + 'min'] = Math.min(config[axis + 'min'], min - padding);
    config[axis + 'max'] = Math.max(config[axis + 'max'], max + padding);
}

/**
 * Calculate a small margin around automatically expanded data ranges.
 *
 * @param {number} min minimum data value.
 * @param {number} max maximum data value.
 * @returns {number} padding to add at both ends.
 */
function getRangePadding(min, max) {
    const span = max - min;
    if (span > 0) {
        return span * 0.1;
    }
    return Math.max(1, Math.abs(min) * 0.1);
}

/**
 * Split a command payload into raw data/expression text and an optional label.
 *
 * @param {string} text command payload.
 * @returns {Object} object containing raw and label strings.
 */
function splitLabel(text) {
    const labelMatch = text.match(/^(.*?)\s+as\s+(.+)$/i);
    return {
        raw: labelMatch ? labelMatch[1].trim() : text.trim(),
        label: labelMatch ? labelMatch[2].trim() : ''
    };
}

/**
 * Parse a comma-separated list of "(x,y)" data points.
 *
 * @param {string} text point list text.
 * @returns {Object[]} parsed points with x and y properties.
 */
function parsePointList(text) {
    const points = [];
    const pointPattern = /\(\s*(-?\d+(?:\.\d+)?)\s*,\s*(-?\d+(?:\.\d+)?)\s*\)/g;
    const remaining = text.replace(pointPattern, (match, x, y) => {
        points.push({
            x: parseFloat(x),
            y: parseFloat(y)
        });
        return '';
    });

    if (remaining.replace(/[,\s]/g, '') !== '') {
        throw plotError('asciistringplotfitformat');
    }

    return points;
}

/**
 * Evaluate either a mathjs expression curve or a fitted polynomial curve.
 *
 * @param {Object} curve curve configuration.
 * @param {string} variable input variable, either "x" or "y".
 * @param {number} value input value.
 * @returns {number} curve output value.
 */
function evaluateCurve(curve, variable, value) {
    if (curve.coefficients) {
        // Fitted curves store polynomial coefficients instead of a mathjs node.
        return curve.coefficients.reduce((sum, coefficient, index) => {
            return sum + coefficient * Math.pow(value, index);
        }, 0);
    }
    return curve.compiled.evaluate({ [variable]: value });
}

/**
 * Create a visible JSXGraph text label for a curve when it has a label.
 *
 * @param {Object} board JSXGraph board.
 * @param {Object} curve curve configuration.
 * @param {Object} config plot configuration.
 */
function createCurveLabel(board, curve, config) {
    if (!curve.label) {
        return;
    }

    // JSXGraph's automatic curve labels can be placed outside the board.
    // We create our own text label on a visible sample point instead.
    const position = getCurveLabelPosition(curve, config);
    if (!position) {
        return;
    }

    board.create('text', [position.x, position.y, curve.label], {
        fixed: true,
        anchorX: 'left',
        anchorY: 'bottom'
    });
}

/**
 * Find where a curve label should be placed on the visible board.
 *
 * @param {Object} curve curve configuration.
 * @param {Object} config plot configuration.
 * @returns {Object|null} visible point for the label, or null if none exists.
 */
function getCurveLabelPosition(curve, config) {
    // Labels sit at the left-hand end of the visible part of the curve.
    return getVisibleCurvePoints(curve, config).reduce((leftmost, point) => {
        if (!leftmost || point.x < leftmost.x || (point.x === leftmost.x && point.y < leftmost.y)) {
            return point;
        }
        return leftmost;
    }, null);
}

/**
 * Sample a curve and return only points inside the current board range.
 *
 * @param {Object} curve curve configuration.
 * @param {Object} config plot configuration.
 * @returns {Object[]} visible sampled points.
 */
function getVisibleCurvePoints(curve, config) {
    const samples = 60;
    // y=f(x) is sampled across x. x=f(y) is sampled across y.
    const axis = curve.axis === 'x' ? 'y' : 'x';
    const min = config[axis + 'min'];
    const span = config[axis + 'max'] - min;
    const points = [];

    for (let index = 0; index <= samples; index++) {
        const input = min + (index / samples) * span;
        let point;

        try {
            const output = evaluateCurve(curve, axis, input);
            point = curve.axis === 'x' ? { x: output, y: input } : { x: input, y: output };
        } catch (error) {
            continue;
        }

        if (isVisiblePoint(point.x, point.y, config)) {
            points.push(point);
        }
    }

    return points;
}

/**
 * Check whether a point is finite and inside the configured plot range.
 *
 * @param {number} x x-coordinate.
 * @param {number} y y-coordinate.
 * @param {Object} config plot configuration.
 * @returns {boolean} whether the point can be shown on the board.
 */
function isVisiblePoint(x, y, config) {
    return Number.isFinite(x) && Number.isFinite(y) &&
        x >= config.xmin && x <= config.xmax &&
        y >= config.ymin && y <= config.ymax;
}

/**
 * Validate a mathjs parse tree against the allowed plotting syntax.
 *
 * @param {Object} node mathjs node.
 */
function validate(node) {
    node.traverse((n) => {
        switch (n.type) {
            case 'ParenthesisNode':
                break;
            case 'SymbolNode':
                break;
            case 'FunctionNode':
                if (!allowed.functions.has(n.fn.name)) {
                    throw plotError('asciistringplotfunctionforbidden', n.fn.name);
                }
                break;
            case 'OperatorNode':
                if (!allowed.operators.has(n.fn)) {
                    throw plotError('asciistringplotoperatorforbidden', n.fn);
                }
                break;
            default:
                if (!allowed.nodetypes.has(n.type)) {
                    throw plotError('asciistringplotnodetypeforbidden', n.type);
                }
        }
    });
}

/**
 * Create a plot error whose message is resolved through translated strings.
 *
 * @param {string} key translation key.
 * @param {string} detail optional detail appended to the message.
 * @returns {Error} tagged plot error.
 */
function plotError(key, detail = '') {
    const error = new Error(plotString(key, detail));
    error.stackPlotError = true;
    return error;
}

/**
 * Resolve a translated plot string and append optional detail.
 *
 * @param {string} key translation key.
 * @param {string} detail optional detail appended to the message.
 * @returns {string} resolved message.
 */
function plotString(key, detail = '') {
    const message = plotStrings[key] || key;
    if (detail !== '') {
        return message + ' ' + String(detail);
    }
    return message;
}

/**
 * Escape text for safe display inside generated HTML.
 *
 * @param {string} text raw text.
 * @returns {string} escaped text.
 */
function escapeHTML(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
