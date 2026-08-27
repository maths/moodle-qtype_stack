// Extractor: lastregexmatch
// [[extractor targetinput="ans2" type="lastregexmatch" regex="^f\\(x\\)\\s*=\\s*" /]]
// Note the escaped backslashes. Searches for a trimmed line matching the given expression.
// Returns the whole trimmed line.
// Scans lines in reverse order.
export default function lastregexmatch(raw, blocks, operation) {
    if (!operation || !operation.regex) {
        return 'ERROR';
    }
    const pattern = new RegExp(operation.regex);

    const lines = raw.split('\n');
    lines.reverse();
    for (const line of lines) {
        const trimmed = line.trim();
        if (pattern.test(trimmed)) {
            return trimmed;
        }
    }
    return 'ERROR';
}
