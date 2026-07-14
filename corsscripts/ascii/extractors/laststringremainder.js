// Extractor: laststringremainder
// [[extractor targetinput="ans2" type="laststringremainder" string="Answer =" /]]
// Searches for a trimmed line (with or without backslashes) matching the given string.
// Returns the remainder of the line stripped of backslashes and leading/trailing spaces.
// Scans lines in reverse order.
export default function laststringremainder(raw, blockCollector, operation) {
    if (!operation || !operation.string) {
        return 'ERROR';
    }

    const stripper = makeStripper(blockCollector);

    const lines = raw.split('\n');
    lines.reverse();
    for (const line of lines) {
        let trimmed = line.replace(stripper, '');
        if (trimmed.includes(operation.string)) {
            trimmed = trimmed.replace(operation.string, '');
            return trimmed.replace(stripper, '');
        }
    }
    return 'ERROR';
}

function makeStripper(blockCollector) {
    const delimiter = (typeof blockCollector?.delimiter === 'string' && blockCollector.delimiter !== '')
        ? blockCollector.delimiter
        : '`';
    const closingdelimiter = (
        typeof blockCollector?.closingdelimiter === 'string' &&
        blockCollector.closingdelimiter !== ''
    ) ? blockCollector.closingdelimiter : delimiter;
    // Build the list of delimiter markers that may wrap the matched line or the
    // extracted remainder. These must be matched as complete strings, not as
    // individual characters: for example, with <<...>>, stripping one '<' at a
    // time would also strip legitimate single '<' characters from the answer.
    // The duplicate filter keeps the regex smaller for symmetric delimiters
    // such as `...`, where the opening and closing markers are identical.
    const markers = [delimiter, closingdelimiter]
        .filter((value, index, values) => values.indexOf(value) === index)
        .map(escapeRegExp)
        .join('|');

    // Strip only leading and trailing whitespace/delimiter markers, leaving the
    // interior answer untouched. The alternation in markers lets asymmetric
    // delimiters work at either end, and the repeated non-capturing groups allow
    // combinations like "  << value >>  " to be normalised in one replace call.
    return new RegExp(String.raw`^(?:\s|${markers})+|(?:\s|${markers})+$`, 'g');
}

function escapeRegExp(value) {
    // User-configured delimiters are inserted into a RegExp, but delimiters may
    // themselves be regex metacharacters such as [, *, ., or \. Prefixing each
    // metacharacter with a backslash makes the regex engine treat the delimiter
    // as literal text. The replacement string "\\$&" means "the whole matched
    // metacharacter, prefixed by a backslash".
    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}
