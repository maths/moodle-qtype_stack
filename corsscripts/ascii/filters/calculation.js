// Filter: calculation - basic scientific calcuator, in radians for school mathematics.
// Finds text enclosed in {@...@} on a single line and evaluates the expression.
// e.g. "The answer is {@2^2 + 1@} here" → "The answer is 5 here"
import math from '../mathjs.min.js';

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
        try {
            const node = math.parse(raw);
            validate(node, allowed);
            rendered = String(node.evaluate());
        } catch (error) {
            rendered = raw;
        }
        if (blockCollector) {
            blockCollector.blocks.push({ type: 'calculation', raw, rendered });
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
                    throw new Error(`Function not allowed: ${n.fn.name}`);
                }
                break;
            case 'OperatorNode':
                if (!allowed.operators.has(n.fn)) {
                    throw new Error(`Operator not allowed: ${n.fn}`);
                }
                break;
            default:
                if (!allowed.nodetypes.has(n.type)) {
                    throw new Error(`Node type not allowed: ${n.type}`);
                }
    }
  });
}
