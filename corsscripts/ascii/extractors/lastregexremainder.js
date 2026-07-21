import { extractorError, extractorResult } from './extractorresult.js';

// Extractor: lastregexremainder
// [[extractor targetinput="ans2" type="lastregexmatch" regex="^f\\(x\\)\\s*=\\s*" /]]
// Note the escaped backslashes. Searches for a trimmed line matching the given expression.
// Returns the whole trimmed line with the regex removed.
// Scans lines in reverse order.
export default function lastregexremainder(raw, blocks, operation) {
    if (!operation || !operation.regex) {
        return extractorError('asciistringextractorregexrequired');
    }
    const pattern = new RegExp(operation.regex);

    const lines = raw.split('\n');
    lines.reverse();
    for (const line of lines) {
        const trimmed = line.trim();
        if (pattern.test(trimmed)) {
            return extractorResult(trimmed.replace(pattern, ''));
        }
    }
    return extractorError('asciistringextractorregexnotfound');
}
