// Shared helpers for ASCII filters and extractors.

let asciiStrings = {};

export function setAsciiStrings(strings = {}) {
    asciiStrings = {
        ...strings
    };
}

export function asciiString(key, detail = '') {
    let message = asciiStrings[key] || key;
    if (detail !== '') {
        message = message + ' ' + String(detail);
    }
    return message;
}

export function extractorResult(result) {
    return {
        result: result
    };
}

export function extractorError(key, detail = '') {
    return {
        error: asciiString(key, detail)
    };
}
