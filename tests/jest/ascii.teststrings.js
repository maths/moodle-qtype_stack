import fs from 'fs';
import path from 'path';

const langFile = path.resolve(__dirname, '../../lang/en/qtype_stack.php');
const langSource = fs.readFileSync(langFile, 'utf8');

export function stackString(key) {
    const pattern = new RegExp("\\$string\\['" + escapeRegExp(key) + "'\\]\\s*=\\s*'((?:\\\\.|[^'])*)';");
    const match = langSource.match(pattern);
    if (!match) {
        throw new Error(`Unable to find qtype_stack string: ${key}`);
    }
    return decodePhpSingleQuotedString(match[1]);
}

export function stackStrings(keys) {
    return Object.fromEntries(keys.map((key) => [key, stackString(key)]));
}

export function stackStringWithDetail(key, detail) {
    return stackString(key) + ' ' + String(detail);
}

function escapeRegExp(value) {
    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function decodePhpSingleQuotedString(value) {
    return value
        .replace(/\\\\/g, '\\')
        .replace(/\\'/g, "'");
}
