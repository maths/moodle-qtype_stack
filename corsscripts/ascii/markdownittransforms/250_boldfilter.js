// Must be applied after aligneq (use filters: "aligneq,boldfilter").
// aligneq inserts & alignment tokens by scanning for = etc. at brace depth 0;
// any bold wrapper applied before it hides those tokens inside {}, breaking alignment.
// \boldsymbol is used (not \textbf) because \textbf is text-mode and breaks on
// math content such as \displaystyle which AMparseMath inserts.
export default function boldfilter(lines, rule) {
    switch (rule) {
        case 'asciimath_block':
        case 'math_block':
        case 'code_inline':
        case 'math_inline':
        default:
            break;
    }
    // lines[0] = '\[\begin{align*}', lines[-1] = '\end{align*}\]'
    // aligneq appends \\ (the LaTeX row-break) directly onto the last content
    // column (e.g. "after\\"), so splitting on & gives ["", " ", " before", "after\\"].
    // We must strip the trailing \\ before wrapping in \boldsymbol, then restore it,
    // otherwise \boldsymbol{after\\} swallows the row-break and output collapses to one line.
    const rowBreak = '\\\\'; // two actual backslash chars = LaTeX \\
    return lines.map((line, i) => {
        if (i === 0 || i === lines.length - 1) return line;
        return splitTopLevelAmpersands(line).map(col => {
            let trimmed = col.trim();
            if (trimmed === '') return col;
            // Strip trailing \\ so it is never inside \boldsymbol{}.
            let trailingBreak = '';
            if (trimmed.endsWith(rowBreak)) {
                trailingBreak = rowBreak;
                trimmed = trimmed.slice(0, -rowBreak.length).trim();
            }
            if (trimmed === '') return col; // column was only \\
            // \displaystyle is a math-mode declaration and must stay outside \boldsymbol.
            const displayPrefix = '\\displaystyle';
            let bold;
            if (trimmed.startsWith(displayPrefix)) {
                const rest = trimmed.slice(displayPrefix.length).trim();
                bold = rest ? `${displayPrefix}\\boldsymbol{${rest}}` : displayPrefix;
            } else {
                bold = `\\boldsymbol{${trimmed}}`;
            }
            return bold + trailingBreak;
        }).join('&');
    });
}

/**
 * Split a LaTeX align row by top-level '&' only.
 * This avoids breaking brace groups such as \text{A & B} into invalid fragments.
 *
 * @param {string} line
 * @returns {string[]}
 */
function splitTopLevelAmpersands(line) {
    const cols = [];
    let start = 0;
    let depth = 0;
    const envStack = [];
    for (let i = 0; i < line.length; i++) {
        if (line.startsWith('\\begin{', i)) {
            const end = line.indexOf('}', i + '\\begin{'.length);
            if (end !== -1) {
                envStack.push(line.slice(i + '\\begin{'.length, end));
                i = end;
                continue;
            }
        }
        if (line.startsWith('\\end{', i)) {
            const end = line.indexOf('}', i + '\\end{'.length);
            if (end !== -1) {
                const envName = line.slice(i + '\\end{'.length, end);
                const top = envStack[envStack.length - 1];
                if (top === envName) {
                    envStack.pop();
                } else {
                    const idx = envStack.lastIndexOf(envName);
                    if (idx !== -1) {
                        envStack.splice(idx, 1);
                    }
                }
                i = end;
                continue;
            }
        }
        const ch = line[i];
        const prev = i > 0 ? line[i - 1] : '';
        if (ch === '{' && prev !== '\\') {
            depth++;
            continue;
        }
        if (ch === '}' && prev !== '\\') {
            depth = Math.max(0, depth - 1);
            continue;
        }
        if (ch === '&' && prev !== '\\' && depth === 0 && envStack.length === 0) {
            cols.push(line.slice(start, i));
            start = i + 1;
        }
    }
    cols.push(line.slice(start));
    return cols;
}
