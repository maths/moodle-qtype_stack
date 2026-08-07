import laststringremainderwhitespace from '../../corsscripts/ascii/extractors/laststringremainderwhitespace.js';
import { setExtractorStrings } from '../../corsscripts/ascii/extractors/extractorhelper.js';

describe('laststringremainderwhitespace extractor', () => {
    beforeEach(() => {
        setExtractorStrings({
            asciistringextractorsearchrequired: 'This extractor requires a search parameter.',
            asciistringextractorsearchnotfound: 'No line matched the requested search text.'
        });
    });

    describe('guard clauses', () => {
        test('returns translated error when operation is undefined', () => {
            expect(laststringremainderwhitespace('any raw', null, undefined)).toEqual({
                error: 'This extractor requires a search parameter.'
            });
        });

        test('returns translated error when operation is null', () => {
            expect(laststringremainderwhitespace('any raw', null, null)).toEqual({
                error: 'This extractor requires a search parameter.'
            });
        });

        test('returns translated error when operation.search is missing', () => {
            expect(laststringremainderwhitespace('any raw', null, {
                type: 'laststringremainderwhitespace'
            })).toEqual({
                error: 'This extractor requires a search parameter. laststringremainderwhitespace'
            });
        });

        test('returns translated error when operation.search is empty', () => {
            expect(laststringremainderwhitespace('any raw', null, {
                type: 'laststringremainderwhitespace',
                search: ''
            })).toEqual({
                error: 'This extractor requires a search parameter. laststringremainderwhitespace'
            });
        });
    });

    test('returns translated error when there is no match', () => {
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace('a = 1\nb = 2', null, operation)).toEqual({
            error: 'No line matched the requested search text. f(x) ='
        });
    });

    test('returns suffix of the last matching line', () => {
        const raw = 'f(x) = first\nother\n f(x) = last ';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({ result: 'last' });
    });

    test('returns basic match', () => {
        const raw = ' f(x) = x^2';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({ result: 'x^2' });
    });

    test('returns basic match no whitespace', () => {
        const raw = ' f(x) =x^2';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({ result: 'x^2' });
    });

    test('returns basic match backticks', () => {
        const raw = ' f(x) = `x^2`  ';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({ result: 'x^2' });
    });

    test('returns basic match backticks internal whitespace', () => {
        const raw = ' f(x) = ` x^7 `  ';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({ result: 'x^7' });
    });

    test('returns basic match no whitespace', () => {
        const raw = 'f(x)=x^2';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({ result: 'x^2' });
    });

    test('returns external match backticks internal whitespace', () => {
        const raw = '`f(x)=x^2`';
        const operation = { search: 'f(x)=' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({ result: 'x^2' });
    });

    test('returns external match backticks lots of whitespace', () => {
        const raw = '` f(x) =x^2  `';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({ result: 'x^2' });
    });

    test('returns external match backticks lots of whitespace, full stop', () => {
        const raw = '` f(x) =x^3+1  ` .  ';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({ result: 'x^3+1' });
    });

    test('fails to match because of previous text', () => {
        const raw = 'hence `f(x)=x^2`.';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({
            error: 'No line matched the requested search text. f(x) ='
        });
    });

    test('returns basic match backticks internal whitespace', () => {
        const raw = ' f(x)= x^2  ';
        const operation = { search: 'f(x)=' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({ result: 'x^2' });
    });

    test('returns basic match backticks internal whitespace, full stop', () => {
        const raw = ' f(x)= x^2.  ';
        const operation = { search: 'f(x)=' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({ result: 'x^2' });
    });

    test('returns translated error because of internal whitespace in line but not search string', () => {
        const raw = ' f(x) = x^2  ';
        const operation = { search: 'f(x)=' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({
            error: 'No line matched the requested search text. f(x)='
        });
    });

    test('returns last match', () => {
        const raw = ' a=1\n a = 2';
        const operation = { search: 'a =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toEqual({ result: '2' });
    });
});
