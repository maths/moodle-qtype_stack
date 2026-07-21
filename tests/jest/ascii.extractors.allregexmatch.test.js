import allregexmatch from '../../corsscripts/ascii/extractors/allregexmatch.js';
import { setExtractorStrings } from '../../corsscripts/ascii/extractors/extractorresult.js';

describe('allregexmatch extractor', () => {
    beforeEach(() => {
        setExtractorStrings({
            asciistringextractorregexrequired: 'This extractor requires a regular expression.',
            asciistringextractorregexnotfound: 'No line matched the requested regular expression.'
        });
    });

    describe('guard clauses', () => {
        test('returns translated error when operation is undefined', () => {
            expect(allregexmatch('any raw', null, undefined)).toEqual({
                error: 'This extractor requires a regular expression.'
            });
        });

        test('returns translated error when operation is null', () => {
            expect(allregexmatch('any raw', null, null)).toEqual({
                error: 'This extractor requires a regular expression.'
            });
        });

        test('returns translated error when operation.regex is missing', () => {
            expect(allregexmatch('any raw', null, {})).toEqual({
                error: 'This extractor requires a regular expression.'
            });
        });

        test('returns translated error when operation.regex is empty', () => {
            expect(allregexmatch('any raw', null, { regex: '' })).toEqual({
                error: 'This extractor requires a regular expression.'
            });
        });
    });

    describe('matching lines', () => {
        test('returns all matching lines as JSON', () => {
            const raw = 'f(x) = x\ny = 3\nf(x) = x^2';
            const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
            const result = allregexmatch(raw, null, operation);
            expect(result).toEqual({
                result: JSON.stringify({ matches: ['f(x) = x', 'f(x) = x^2'] })
            });
        });

        test('trims lines before matching and keeps order', () => {
            const raw = '  f(x) = a  \n\n  f(x) = b';
            const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
            const result = allregexmatch(raw, null, operation);
            expect(result).toEqual({
                result: JSON.stringify({ matches: ['f(x) = a', 'f(x) = b'] })
            });
        });

        test('returns translated error when no lines match', () => {
            const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
            expect(allregexmatch('y = x\na = 1', null, operation)).toEqual({
                error: 'No line matched the requested regular expression.'
            });
        });
    });
});
