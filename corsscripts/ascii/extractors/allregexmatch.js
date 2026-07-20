// Extractor: allregexmatch
// [[extractor targetinput="ans2" type="allregexmatch" regex="^f\\(x\\)\\s*=\\s*" /]]
// Searches the entire raw input for all lines matching operation.regex and returns
// a JSON object of the form {"matches":[...]} set as answerEl.value.
export default function allregexmatch(raw, blocks, operation) {
    if (!operation || !operation.regex) {
        return 'ERROR';
    }
    const pattern = new RegExp(operation.regex);
    const matches = [];

    for (const line of raw.split('\n')) {
        const trimmed = line.trim();
        if (trimmed && pattern.test(trimmed)) {
            matches.push(trimmed);
        }
    }

    if (matches.length === 0) {
        return 'ERROR';
    }
    return JSON.stringify({ matches });
}
