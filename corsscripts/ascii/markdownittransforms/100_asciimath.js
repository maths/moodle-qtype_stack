/**
 * Transform: convert each AsciiMath line to LaTeX.
 *
 * @param {string[]} lines - array of AsciiMath strings.
 * @returns {string[]} array of LaTeX strings.
 */
export default function asciimath(lines, rule) {
    switch (rule) {
        case 'asciimath_block':
        case 'code_inline':
            if (isLaTeX(lines)) {
                return lines;
            } else {
                return lines.map(line => window.AMparseMath(line, true));
            }
            break;
        case 'math_block':
        case 'math_inline':
        default:
            return lines;
    }
}

function isLaTeX(lines) {
    const code = lines.join();
    // Do not attempt to apply ASCIIMath to blocks which are already LaTeX.
    const islatex = [
        '^{',
        '_{',
        '\\left',
        '\\right',
        '\\begin',
        ];
    if (islatex.some(s => code.includes(s))) {
        return true;
    };
    // Use of a general control code.
    // (Yes, this duplicates \left, \right, \begin above...)
    const regex = /\\[a-zA-Z]+/;
    return regex.test(code);
};
