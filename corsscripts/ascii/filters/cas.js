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
        try {
            rendered = String(math.evaluate(raw));
        } catch (error) {
            rendered = raw;
        }
        if (blockCollector) {
            blockCollector.blocks.push({ type: 'calculation', raw, rendered });
        }
        return rendered;
    });
}
