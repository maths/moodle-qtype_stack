// Extractor: laststringremainderwhitespace
// [[extractor targetinput="ans2" type="laststringremainderwhitespace" string="f(x) =" /]]
// Remove the requirement to write a regex.
// Searches for a trimmed line matching the given expression, ignoring whitespace, and backticks.
// Returns the matching group, with the search regex removed.
// Scans lines in reverse order.
export default function laststringremainderwhitespace(raw, blocks, operation) {
    if (!operation || !operation.search) {
        return 'ERROR';
    }

    var match = escaperegex(operation.search);
    match = '^' + match + '\\s*`?([^`]+)`?';
    const pattern = new RegExp(match);

    const lines = raw.split('\n');
    lines.reverse();
    for (const line of lines) {
        // Trim off whitespace.
        var trimmed = line.trim();
        // Trim off full stop, if needed.
        if (trimmed.endsWith('.')) {
          trimmed = trimmed.slice(0, -1);
          trimmed = trimmed.trim();
        }
        // Trim off matching outer backticks, if needed, and trim.
        if (trimmed.startsWith("`") && trimmed.endsWith("`")) {
          trimmed = trimmed.slice(1, -1);
          trimmed = trimmed.trim();
        }
        const matched = trimmed.match(pattern);
        if (matched) {
            const retmatch = matched[1];
            return retmatch.trim();
        }
    }
    return 'ERROR';
}

function escaperegex(str) {
  // 1. Protect special characters in the search pattern.
  const match = str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  // 2. Turn each whitespace character in the search pattern to match to zero or more spaces.
  return match.replace(/\s+/g, "\\s*");
}

