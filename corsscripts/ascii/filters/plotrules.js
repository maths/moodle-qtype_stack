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
import { asciiString } from '../asciihelper.js';

// Student syntax is intentionally tiny:
//   x: -5..5
//   y: -3..10
//   y=x^2-1
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
    curves: []
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
    ]),

    symbols: new Set([
        'x',
        'pi',
        'e'
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
                    name: '',
                    withLabel: false
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
        curves: []
    };

    const lines = code.split(/\r?\n/)
        .map(line => line.trim())
        .filter(line => line !== '' && !line.startsWith('#'));

    lines.forEach((line) => parseLine(line, config));

    // Validate the complete config after all lines are read so ranges can be
    // given before or after curves.
    if (config.xmin >= config.xmax) {
        throw plotError('asciistringplotxrange');
    }
    if (config.ymin >= config.ymax) {
        throw plotError('asciistringplotyrange');
    }
    if (config.curves.length === 0) {
        throw plotError('asciistringplotempty');
    }

    return config;
}

/**
 * Parse one non-empty plot instruction line into the shared config.
 *
 * @param {string} line trimmed instruction line.
 * @param {Object} config plot configuration being built.
 */
function parseLine(line, config) {
    const range = line.match(/^([xy])\s*:\s*(-?\d+(?:\.\d+)?)\s*\.\.\s*(-?\d+(?:\.\d+)?)$/i);
    if (range) {
        const axis = range[1].toLowerCase();
        config[axis + 'min'] = parseFloat(range[2]);
        config[axis + 'max'] = parseFloat(range[3]);
        return;
    }

    const curve = line.match(/^y\s*=\s*(.+)$/i);
    if (curve) {
        if (config.curves.length > 0) {
            throw plotError('asciistringplotmultiple');
        }
        addCurve(curve[1], config);
        return;
    }

    throw plotError('asciistringplotunknown', line);
}

/**
 * Add a student-entered expression curve to the plot config.
 *
 * @param {string} expression curve expression.
 * @param {Object} config plot configuration being built.
 */
function addCurve(expression, config) {
    const raw = expression.trim();

    try {
        const node = math.parse(raw);
        validate(node);
        // Store the compiled expression once; JSXGraph can then evaluate it
        // repeatedly while sampling the curve.
        config.curves.push({
            expression: raw,
            compiled: node.compile()
        });
    } catch (error) {
        if (error && error.stackPlotError) {
            throw error;
        }
        throw plotError('asciistringplotinvalidexpression', raw);
    }
}

/**
 * Validate a mathjs parse tree against the allowed plotting syntax.
 *
 * @param {Object} node mathjs node.
 */
function validate(node) {
    node.traverse((n, path, parent) => {
        switch (n.type) {
            case 'ParenthesisNode':
                break;
            case 'SymbolNode':
                if (parent && parent.type === 'FunctionNode' &&
                        parent.fn === n && allowed.functions.has(n.name)) {
                    break;
                }
                if (!allowed.symbols.has(n.name)) {
                    throw plotError('asciistringplotsymbolforbidden', n.name);
                }
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
    return asciiString(key, detail);
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
