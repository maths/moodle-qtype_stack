import { extractorError, extractorResult } from './extractorhelper.js';

// Extractor: laststringremainder
// [[extractor targetinput="ans2" type="laststringremainder" search="Answer =" /]]
// Searches for a trimmed line (with or without backslashes) matching the given string.
// Returns the remainder of the line stripped of backslashes and leading/trailing spaces.
// Scans lines in reverse order.
export default function laststringremainder(raw, blocks, operation) {
    // Original release had 'string' rather than 'search'. This is corrected here
    // but string option retained for back compat.
    if (!operation || (!operation.search && !operation.string)) {
        return extractorError('asciistringextractorsearchrequired');
    }

    const lines = raw.split('\n');
    lines.reverse();
    const searchstring = operation.search ?? operation.string;
    for (const line of lines) {
        let trimmed = line.replace(/^[\s`]+|[\s`]+$/g, '');
        if (trimmed.includes(searchstring)) {
            trimmed = trimmed.replace(searchstring, '');
            return extractorResult(trimmed.replace(/^[\s`]+|[\s`]+$/g, ''));
        }
    }
    return extractorError('asciistringextractorsearchnotfound', searchstring);
}
