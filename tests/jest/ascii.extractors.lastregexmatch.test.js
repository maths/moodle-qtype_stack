import lastregexmatch from '../../corsscripts/ascii/extractors/lastregexmatch.js';
import { setExtractorStrings } from '../../corsscripts/ascii/extractors/extractorresult.js';

describe('lastregexmatch extractor', () => {
    beforeEach(() => {
        setExtractorStrings({
            asciistringextractorregexrequired: 'This extractor requires a regular expression.',
            asciistringextractorregexnotfound: 'No line matched the requested regular expression.'
        });
    });

    describe('guard clauses', () => {
        test('returns translated error when operation is undefined', () => {
            expect(lastregexmatch('any raw', [], undefined)).toEqual({
                error: 'This extractor requires a regular expression.'
            });
        });

        test('returns translated error when operation is null', () => {
            expect(lastregexmatch('any raw', [], null)).toEqual({
                error: 'This extractor requires a regular expression.'
            });
        });

        test('returns translated error when operation.regex is missing', () => {
            expect(lastregexmatch('any raw', [], {})).toEqual({
                error: 'This extractor requires a regular expression.'
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
            error: 'No line matched the requested regular expression.'
        });
    });
});
