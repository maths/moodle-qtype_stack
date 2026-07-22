import { extractorError, extractorResult } from './extractorhelper.js';

// Extractor: allregexremainder
// [[extractor targetinput="ans2" type="allregexremainder" regex="^f\\(x\\)\\s*=\\s*" /]]
// Searches the entire raw input for all lines matching operation.regex and returns
// a JSON object of the form {"matches":[...]} set as answerEl.value. The regex itself is removed
// from the matches.
export default function allregexremainder(raw, blocks, operation) {
    if (!operation || !operation.regex) {
        return extractorError('asciistringextractorregexrequired');
    }
    const pattern = new RegExp(operation.regex);
    const matches = [];

    for (const line of raw.split('\n')) {
        const trimmed = line.trim();
        if (trimmed && pattern.test(trimmed)) {
            matches.push(trimmed.replace(pattern, ''));
        }
    }

    if (matches.length === 0) {
        return extractorError('asciistringextractorregexnotfound', operation.regex);
    }
    return extractorResult(JSON.stringify({ matches }));
}
