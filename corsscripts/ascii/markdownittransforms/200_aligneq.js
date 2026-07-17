import findtextindex from './findtextindex.js';

/**
 * Transform: wraps an array of LaTeX lines in an `align*` environment formatted
 * as a 3-column (plus optional 4th) aligned layout.
 *
 * Column assignment per line (insertions are applied right-to-left so earlier
 * positions are not shifted by later insertions):
 *   col 1 – leading logical connective (\\Rightarrow, \\therefore, …), if present
 *   col 2 – left-hand side up to (but not including) the relation symbol
 *   col 3 – relation symbol and right-hand side
 *   col 4 – \\text{…} that is not \\text{or}, \\text{and}, or \\text{if}
 *
 * @param {string[]} lines - array of LaTeX strings, one expression per line.
 * @returns {string[]} array whose first element is \[\begin{align*}, last element
 *   is \end{align*}\], and whose interior lines are the formatted content rows.
 */
export default function aligneq(lines, rule) {
    switch (rule) {
        case 'asciimath_block':
        case 'math_block':
            break;
        case 'code_inline':
        case 'math_inline':
            return lines;
        default:
            return lines;
    }
    // Do not attempt to wrap LaTeX which is already aligned.
    const nonest = [
        'align',
        'flalign',
        'alignat',
        'xalignat',
        'xxalignat',
        'gather',
        'multline',
        'equation',
        'split',
        'subequations'
        ];
    const skipenv = nonest.flatMap(token => [`\\begin{${token}}`, `\\begin{${token}*}`]);
    var skip = false;
    for (let str of lines) {
        if (findtextindex(str, skipenv) !== false) {
            skip = true;
        }
    }
    if (skip) {
        return lines;
    }
    const output = [`\\begin{align*}`];
    for (let str of lines) {
        // Remove any display style commands and trim the string.
        str = str.replace(/^\s*(?:\\displaystyle\s*)?/, '');
        // Step 3 (rightmost): find the first \text{ that is not \text{or/and/if}
        // and push it into col 4 by inserting '& &' before it.
        const matchtxt = findtextindex(str, ['\\text{'], [`\\text{or}`, `\\text{and}`, `\\text{if}`]);
        if (matchtxt) {
            str = str.slice(0, matchtxt) + ' & & ' + str.slice(matchtxt);
        }

        // Step 2 (middle): find the first relation symbol at top brace level
        // (=, >, <, \leq, \subset, etc.) and split col 2 from col 3 with '&'.
        // A trailing '&' is appended even when no relation is found so MathJax
        // still aligns the line correctly within the environment.
        const bracest = [
            `in`, `notin`, `subset`, `subseteq`, `supset`, `supseteq`,
            `leq`, `lt`, `le`, `geq`, `gt`, `ge`,
            `preq`, `preqeq`, `succ`, `succeq`,
            `ne`, `neq`, `approx`, `equiv`, `propto`, `cong`,
        ];
        const braces = [`=`, `>`, `<`].concat(bracest.flatMap(token => [`\\${token}{`, `\\${token} `]));
        const matcheq = findtextindex(str, braces);
        // findtextindex returns 0 (falsy) when the match is at position 0, so
        // compare strictly against false rather than using a truthiness check.
        if (matcheq !== false) {
            str = str.slice(0, matcheq) + ' & ' + str.slice(matcheq);
        } else {
            str = str.trim() + ` & `;
        }

        // Step 1 (leftmost): if the line opens with a logical connective
        // (\Rightarrow, \therefore, etc.), move it into col 1.
        const imptxt = [`Rightarrow`, `Leftarrow`, `Leftrightarrow`, `therefore`, `because`];
        const imptxttk = imptxt.flatMap(token => [`\\${token}{`, `\\${token} `, `\\${token}\\`]);
        const matchimp = imptxttk.find(token => str.startsWith(token));
        if (matchimp === undefined) {
            // No connective — leave col 1 empty with leading '& & '.
            str = `& & ` + str.trim();
        } else {
            // Connective found — insert '& &' after it to push the rest into col 2.
            str = str.slice(0, matchimp.length - 1) + ' & & ' + str.slice(matchimp.length - 1);
        }
        str += `\\\\`;   // LaTeX row separator
        output.push(str);
    }
    output.push(`\\end{align*}`);
    return output;
}
