// Shared helpers for ASCII extractors.

let extractorStrings = {};

export function setExtractorStrings(strings = {}) {
    extractorStrings = {
        ...strings
    };
}

export function extractorResult(result) {
    return {
        result: result
    };
}

export function extractorError(key, detail = '') {
    let message = extractorStrings[key] || key;
    if (detail !== '') {
        message = message + ' ' + String(detail);
    }
    return {
        error: message
    };
}

