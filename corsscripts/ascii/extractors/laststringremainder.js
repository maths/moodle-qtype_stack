// Extractor: laststringremainder
// [[extractor targetinput="ans2" type="laststringremainder" string="Answer =" /]]
// Searches for a trimmed line (with or without backslashes) matching the given string.
// Returns the remainder of the line stripped of backslashes and leading/trailing spaces.
// Scans lines in reverse order.
export default function laststringremainder(raw, blocks, operation) {
    if (!operation || !operation.string) {
        return 'ERROR';
    }

    const lines = raw.split('\n');
    lines.reverse();
    for (const line of lines) {
        let trimmed = line.replace(/^[\s`]+|[\s`]+$/g, '');
        if (trimmed.includes(operation.string)) {
            trimmed = trimmed.replace(operation.string, '');
            return trimmed.replace(/^[\s`]+|[\s`]+$/g, '');
        }
    }
    return 'ERROR';
}
