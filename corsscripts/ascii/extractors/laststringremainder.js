import { extractorError, extractorResult } from './extractorhelper.js';

// Extractor: laststringremainder
// [[extractor targetinput="ans2" type="laststringremainder" string="Answer =" /]]
// Searches for a trimmed line (with or without backslashes) matching the given string.
// Returns the remainder of the line stripped of backslashes and leading/trailing spaces.
// Scans lines in reverse order.
export default function laststringremainder(raw, blocks, operation) {
    if (!operation || !operation.search) {
        return extractorError('asciistringextractorsearchrequired');
    }

    const lines = raw.split('\n');
    lines.reverse();
    for (const line of lines) {
        let trimmed = line.replace(/^[\s`]+|[\s`]+$/g, '');
        if (trimmed.includes(operation.search)) {
            trimmed = trimmed.replace(operation.search, '');
            return extractorResult(trimmed.replace(/^[\s`]+|[\s`]+$/g, ''));
        }
    }
    return extractorError('asciistringextractorsearchnotfound', operation.search);
}
