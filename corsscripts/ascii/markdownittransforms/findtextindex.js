/**
 * Return the index of the first token from `needle` found at top-level brace depth
 * in `str` (i.e. outside any (), [], {}, \lbrace/\rbrace nesting).
 * Tokens listed in `without` are ignored even when they match a `needle` entry.
 * Returns false if no unexcluded needle token is found at top level.
 *
 * @param {string}   str     - the string to search.
 * @param {string[]} needle  - tokens to search for (matched via String.startsWith).
 * @param {string[]} without - tokens to exclude from matches (default: []).
 * @returns {number|false} index of the first matching token, or false if none found.
 *
 * @example
 * findtextindex('a = {b = c}', ['='])        // returns 2  (inner = is nested)
 * findtextindex('a = b',       ['='], ['='])  // returns false (excluded)
 */
export default function findtextindex(str, needle, without = []) {
    let braceDepth = 0;

    for (let i = 0; i < str.length; i++) {
        const ch = str[i];
        // Track opening delimiters: (, [, { and \lbrace.
        if (ch === '{' || ch === '(' || ch === '[') {
            braceDepth++;
            continue;
        }
        if (str.startsWith('\\lbrace', i)) {
            braceDepth++;
            continue;
        }
        // Track closing delimiters: ), ], } and \rbrace.
        if (ch === '}' || ch === ')' || ch === ']') {
            braceDepth = Math.max(0, braceDepth - 1);
            continue;
        }
        if (str.startsWith('\\rbrace', i)) {
            braceDepth = Math.max(0, braceDepth - 1);
            continue;
        }

        // Only test needle tokens when at top level (braceDepth === 0).
        if (braceDepth === 0) {
            const isIncluded = needle.some(token => str.startsWith(token, i));
            if (!isIncluded) {
                continue;
            }
            const isExcluded = without.some(token => str.startsWith(token, i));
            if (!isExcluded) {
                return i;
            }
        }
    }
    return false;
}
