import lastregexremainder from '../../corsscripts/ascii/extractors/lastregexremainder.js';
import { setExtractorStrings } from '../../corsscripts/ascii/extractors/extractorhelper.js';

describe('lastregexremainder extractor', () => {
    beforeEach(() => {
        setExtractorStrings({
            asciistringextractorregexrequired: 'This extractor requires a regular expression.',
            asciistringextractorregexnotfound: 'No line matched the requested regular expression.'
        });
    });

    describe('guard clauses', () => {
        test('returns translated error when operation is undefined', () => {
            expect(lastregexremainder('any raw', [], undefined)).toEqual({
                error: 'This extractor requires a regular expression.'
            });
        });

        test('returns translated error when operation is null', () => {
            expect(lastregexremainder('any raw', [], null)).toEqual({
                error: 'This extractor requires a regular expression.'
            });
        });

        test('returns translated error when operation.regex is missing', () => {
            expect(lastregexremainder('any raw', [], { type: 'lastregexremainder' })).toEqual({
                error: 'This extractor requires a regular expression. lastregexremainder'
            });
        });

        test('returns translated error when operation.regex is empty', () => {
            expect(lastregexremainder('any raw', [], { type: 'lastregexremainder', regex: '' })).toEqual({
                error: 'This extractor requires a regular expression. lastregexremainder'
            });
        });
    });

    test('returns empty string when regex consumes whole matching line', () => {
        const raw = 'abc\n42\n99';
        expect(lastregexremainder(raw, null, { regex: '^\\d+$' })).toEqual({ result: '' });
    });

    test('returns translated error when there is no match', () => {
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexremainder('a = 1\nb = 2', null, operation)).toEqual({
            error: 'No line matched the requested regular expression. ^f\\(x\\)\\s*=\\s*'
        });
    });

    test('returns suffix of the last matching line', () => {
        const raw = 'f(x) = first\nother\n f(x) = last ';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexremainder(raw, null, operation)).toEqual({ result: 'last' });
    });

    test('returns basic match', () => {
        const raw = ' f(x) = x^2';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexremainder(raw, null, operation)).toEqual({ result: 'x^2' });
    });

    test('returns basic match no whitespace', () => {
        const raw = ' f(x)=x^2';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexremainder(raw, null, operation)).toEqual({ result: 'x^2' });
    });

    test('returns basic match backticks', () => {
        const raw = ' f(x)= `x^2`  ';
        const operation = { regex: '^f\\(x\\)\\s*=\\s*' };
        expect(lastregexremainder(raw, null, operation)).toEqual({ result: '`x^2`' });
    });
});
