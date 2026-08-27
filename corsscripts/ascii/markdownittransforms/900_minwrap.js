import findtextindex from './findtextindex.js';
/**
 * Transform: add minimal LaTeX wrapping.
 *
 * @param {string[]} lines - array of strings.
 * @returns {string[]} array of LaTeX strings.
 */
export default function minwrap(lines, rule) {
    if (lines && lines.length === 0) {
        return [''];
    }
    switch (rule) {
        case 'asciimath_block':
        case 'math_block':
            return wraplatex(lines);
        case 'code_inline':
        case 'math_inline':
            return [`\\(${lines[0]}\\)`];
        default:
            return lines;
    }
}

function wraplatex(lines) {
    // Do not to wrap displayed LaTeX which already has these environments aligned.
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
    lines.push('\\]');
    lines.unshift('\\[');
    return lines;
}
