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
//   point (2,3) A
// Each !!plot block is parsed into one of these config objects, then rendered
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
        'abs', 'floor', 'ceil', 'ceiling', 'round',
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

let nextPlotId = 1;
const pendingPlots = new Map();

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
                // mathjs compiled expressions are evaluated with only x in scope.
                board.create('functiongraph', [
                    function(x) {
                        return curve.compiled.evaluate({ x });
                    },
                    config.xmin,
                    config.xmax
                ], {
                    name: curve.label,
                    withLabel: Boolean(curve.label)
                });
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

    const lines = code.split(/\r?\n/)
        .map(line => line.trim())
        .filter(line => line !== '' && !line.startsWith('#'));

    lines.forEach((line) => parseLine(line, config));

    // Validate the complete config after all lines are read so ranges can be
    // given before or after curves.
    if (config.xmin >= config.xmax) {
        throw new Error('Plot x range must increase.');
    }
    if (config.ymin >= config.ymax) {
        throw new Error('Plot y range must increase.');
    }
    if (config.curves.length === 0 && config.points.length === 0) {
        throw new Error('Plot block needs at least one curve or point.');
    }

    return config;
}

function parseLine(line, config) {
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
        return;
    }

    const dimension = line.match(/^(width|height)\s*:\s*(\d+)$/i);
    if (dimension) {
        config[dimension[1].toLowerCase()] = clamp(parseInt(dimension[2], 10), 100, 1200);
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

    const curve = line.match(/^(?:plot\s+)?(?:y\s*=\s*|f\s*\(\s*x\s*\)\s*=\s*)?(.+)$/i);
    if (curve) {
        // A bare expression is accepted as shorthand for "plot y=...".
        addCurve(curve[1], config);
        return;
    }

    throw new Error('Unknown plot instruction: ' + line);
}

function addCurve(expression, config) {
    const labelMatch = expression.match(/^(.*?)\s+as\s+(.+)$/i);
    const raw = labelMatch ? labelMatch[1].trim() : expression.trim();
    const label = labelMatch ? labelMatch[2].trim() : '';

    try {
        const node = math.parse(raw);
        validate(node);
        // Store the compiled expression once; JSXGraph can then evaluate it
        // repeatedly while sampling the curve.
        config.curves.push({
            expression: raw,
            compiled: node.compile(),
            label
        });
    } catch (error) {
        throw new Error('Invalid plot expression "' + raw + '".');
    }
}

function validate(node) {
    node.traverse((n) => {
        switch (n.type) {
            case 'ParenthesisNode':
                break;
            case 'SymbolNode':
                break;
            case 'FunctionNode':
                if (!allowed.functions.has(n.fn.name)) {
                    throw new Error('Function not allowed: ' + n.fn.name);
                }
                break;
            case 'OperatorNode':
                if (!allowed.operators.has(n.fn)) {
                    throw new Error('Operator not allowed: ' + n.fn);
                }
                break;
            default:
                if (!allowed.nodetypes.has(n.type)) {
                    throw new Error('Node type not allowed: ' + n.type);
                }
        }
    });
}

function clamp(value, min, max) {
    return Math.max(min, Math.min(max, value));
}

function escapeHTML(text) {
    return String(text)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
