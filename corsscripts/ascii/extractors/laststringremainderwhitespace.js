// Extractor: laststringremainderwhitespace
// [[extractor targetinput="ans2" type="laststringremainderwhitespace" string="f(x) =" /]]
// Remove the requirement to write a regex.
// Searches for a trimmed line matching the given expression, ignoring whitespace, and delimiters.
// Returns the matching group, with the search regex removed.
// Scans lines in reverse order.
export default function laststringremainderwhitespace(raw, blockCollector, operation) {
    if (!operation || !operation.search) {
        return 'ERROR';
    }

    const delimiters = getDelimiters(blockCollector);
    var match = escaperegex(operation.search);
    match = '^' + match + '\\s*(?:' + escapeRegExp(delimiters.open) + ')?([\\s\\S]+?)(?:' +
        escapeRegExp(delimiters.close) + ')?$';
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
        // Trim off matching outer delimiters, if needed, and trim.
        if (trimmed.startsWith(delimiters.open) && trimmed.endsWith(delimiters.close)) {
          trimmed = trimmed.slice(delimiters.open.length, -delimiters.close.length);
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

function getDelimiters(blockCollector) {
  const delimiter = (typeof blockCollector?.delimiter === 'string' && blockCollector.delimiter !== '')
      ? blockCollector.delimiter
      : '`';
  const closingdelimiter = (
      typeof blockCollector?.closingdelimiter === 'string' &&
      blockCollector.closingdelimiter !== ''
  ) ? blockCollector.closingdelimiter : delimiter;

  return { open: delimiter, close: closingdelimiter };
}

function escaperegex(str) {
  // 1. Protect special characters in the search pattern.
  const match = str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  // 2. Turn each whitespace character in the search pattern to match to zero or more spaces.
  return match.replace(/\s+/g, "\\s*");
}

function escapeRegExp(value) {
  // Delimiters are configured by users and can be regex metacharacters.
  // Escape them so the optional delimiter markers are matched literally.
  return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}
