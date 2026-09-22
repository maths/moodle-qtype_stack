import lastregexmatch from '../../corsscripts/ascii/extractors/lastregexmatch.js';
import { setAsciiStrings } from '../../corsscripts/ascii/asciihelper.js';
import { stackStrings, stackStringWithDetail } from './ascii.teststrings.js';

const strings = stackStrings([
    'asciistringextractorregexrequired',
    'asciistringextractorregexnotfound'
]);

describe('lastregexmatch extractor', () => {
    beforeEach(() => {
        setAsciiStrings(strings);
    });

    describe('guard clauses', () => {
        test('returns translated error when operation is undefined', () => {
            expect(lastregexmatch('any raw', [], undefined)).toEqual({
                error: strings.asciistringextractorregexrequired
            });
        });

        test('returns translated error when operation is null', () => {
            expect(lastregexmatch('any raw', [], null)).toEqual({
                error: strings.asciistringextractorregexrequired
            });
        });

        test('returns translated error when operation.regex is missing', () => {
            expect(lastregexmatch('any raw', [], { type: 'lastregexmatch' })).toEqual({
                error: stackStringWithDetail('asciistringextractorregexrequired', 'lastregexmatch')
            });
        });

        test('returns translated error when operation.regex is empty', () => {
            expect(lastregexmatch('any raw', [], { type: 'lastregexmatch', regex: '' })).toEqual({
                error: stackStringWithDetail('asciistringextractorregexrequired', 'lastregexmatch')
            });
        });
    });

    test('returns full last matching line from raw', () => {
        const raw = 'f(x) = first\nother\nf(x) = last';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexmatch(raw, null, operation)).toEqual({ result: 'f(x) = last' });
    });

    test('trims lines before matching', () => {
        const raw = '  f(x) = expr  ';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexmatch(raw, null, operation)).toEqual({ result: 'f(x) = expr' });
    });

    test('returns translated error when there is no match', () => {
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexmatch('a = 1\nb = 2', null, operation)).toEqual({
            error: stackStringWithDetail('asciistringextractorregexnotfound', operation.regex)
        });
    });
});
