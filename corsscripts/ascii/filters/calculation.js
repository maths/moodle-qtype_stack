// Filter: calculation - basic scientific calcuator, in radians for school mathematics.
// Finds text enclosed in {@...@} on a single line and evaluates the expression.
// e.g. "The answer is {@2^2 + 1@} here" → "The answer is 5 here"
import math from '../mathjs.min.js';
import { asciiString } from '../asciihelper.js';

// This is the allowed set of functions, operators and ast nodes for students.
const allowed = {
  functions: new Set([
      'sin', 'cos', 'tan',
      'asin', 'acos', 'atan',
      'sqrt',
      'log', 'log10',
      'exp',
      'abs', 'floor', 'celing', 'round',
      'mod', 'gcd', 'lcm',
      'factorial',
      'combinations', 'permutations',
      'min', 'max',
      'sum', 'prod',

      // Statistics.
      'mean','median','mode','variance','std'
  ]),

  operators: new Set([
      'add',
      'subtract',
      'multiply',
      'divide',
      'pow',
      'unaryMinus',
      'unaryPlus',
      'factorial',
      'mod',
  ]),

  nodetypes: new Set([
      'ConstantNode',
      'ParenthesisNode',
      'ArrayNode',  // Needed for stats functions.
      'OperatorNode',
      'FunctionNode',
      'SymbolNode'
    ])
};


export default function calculation(text, blockCollector) {
    if (blockCollector) {
        blockCollector.isHTML = false;
        blockCollector.blocks = [];
    }

    return text.replace(/\{@([^\n]+?)@\}/g, (match, raw) => {
        let rendered;
        let errormsg;
        try {
            const node = math.parse(raw);
            validate(node, allowed);
            rendered = node.evaluate();
            if (typeof rendered === 'function') {
                rendered = raw;
            } else {
                rendered = String(rendered);
            }
        } catch (error) {
            errormsg = error.message;
            rendered = raw;
        }
        if (blockCollector) {
            const block = { type: 'calculation', raw, rendered };
            if (errormsg) {
                block.errormsg = errormsg;
            }
            blockCollector.blocks.push(block);
        }
        return rendered;
    });
}

function validate(node, allowed) {
    node.traverse((n) => {
        switch (n.type) {
            case 'ParenthesisNode':
                break;
            case 'SymbolNode':
                // Allow all symbols.
                break;
            case 'FunctionNode':
                if (!allowed.functions.has(n.fn.name)) {
                    throw new Error(asciiString('asciistringfiltercalculationfunctionnotallowed', n.fn.name));
                }
                break;
            case 'OperatorNode':
                if (!allowed.operators.has(n.fn)) {
                    throw new Error(asciiString('asciistringfiltercalculationoperatornotallowed', n.fn));
                }
                break;
            default:
                if (!allowed.nodetypes.has(n.type)) {
                    throw new Error(asciiString('asciistringfiltercalculationnodetypenotallowed', n.type));
                }
    }
  });
}
