import lastregexremainder from '../../corsscripts/ascii/extractors/lastregexremainder.js';
import { setAsciiStrings } from '../../corsscripts/ascii/asciihelper.js';
import { stackStrings, stackStringWithDetail } from './ascii.teststrings.js';

const strings = stackStrings([
    'asciistringextractorregexrequired',
    'asciistringextractorregexnotfound'
]);

describe('lastregexremainder extractor', () => {
    beforeEach(() => {
        setAsciiStrings(strings);
    });

    describe('guard clauses', () => {
        test('returns translated error when operation is undefined', () => {
            expect(lastregexremainder('any raw', [], undefined)).toEqual({
                error: strings.asciistringextractorregexrequired
            });
        });

        test('returns translated error when operation is null', () => {
            expect(lastregexremainder('any raw', [], null)).toEqual({
                error: strings.asciistringextractorregexrequired
            });
        });

        test('returns translated error when operation.regex is missing', () => {
            expect(lastregexremainder('any raw', [], { type: 'lastregexremainder' })).toEqual({
                error: stackStringWithDetail('asciistringextractorregexrequired', 'lastregexremainder')
            });
        });

        test('returns translated error when operation.regex is empty', () => {
            expect(lastregexremainder('any raw', [], { type: 'lastregexremainder', regex: '' })).toEqual({
                error: stackStringWithDetail('asciistringextractorregexrequired', 'lastregexremainder')
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
            error: stackStringWithDetail('asciistringextractorregexnotfound', operation.regex)
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
