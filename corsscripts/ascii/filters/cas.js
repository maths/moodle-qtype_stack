// Filter: cas - Full access to math.js, which has lots of computer algebra functions.
// Finds text enclosed in {@...@} on a single line and evaluates the expression.
// e.g. "The answer is {@2^2 + 1@} here" → "The answer is 5 here"
//      {@derivative("sin(2*x^3)", "x")@} → 6*x^2*sin(2*x^3).
import math from '../mathjs.min.js';
export default function cas(text, blockCollector) {
    if (blockCollector) {
        blockCollector.isHTML = false;
        blockCollector.blocks = [];
    }

    return text.replace(/\{@([^\n]+?)@\}/g, (match, raw) => {
        let rendered;
        let errormsg;
        try {
            rendered = math.evaluate(raw);
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
