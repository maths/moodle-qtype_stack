import laststringremainderwhitespace from '../../corsscripts/ascii/extractors/laststringremainderwhitespace.js';

describe('laststringremainderwhitespace extractor', () => {
    test('returns ERROR when there is no match', () => {
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace('a = 1\nb = 2', null, operation)).toBe('ERROR');
    });

    test('returns suffix of the last matching line', () => {
        const raw = 'f(x) = first\nother\n f(x) = last ';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('last');
    });

    test('returns basic match', () => {
        const raw = ' f(x) = x^2';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns basic match no whitespace', () => {
        const raw = ' f(x) =x^2';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns basic match backticks', () => {
        const raw = ' f(x) = `x^2`  ';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns basic match backticks internal whitespace', () => {
        const raw = ' f(x) = ` x^7 `  ';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^7');
    });

    test('returns basic match no whitespace', () => {
        const raw = 'f(x)=x^2';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns external match backticks internal whitespace', () => {
        const raw = '`f(x)=x^2`';
        const operation = { search: 'f(x)=' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns external match backticks lots of whitespace', () => {
        const raw = '` f(x) =x^2  `';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns external match backticks lots of whitespace, full stop', () => {
        const raw = '` f(x) =x^3+1  ` .  ';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^3+1');
    });

    test('returns basic match with asymmetric configured delimiters', () => {
        const raw = ' f(x) = << x^4 >>  ';
        const operation = { search: 'f(x) =' };
        const blockCollector = { blocks: [], delimiter: '<<', closingdelimiter: '>>' };
        expect(laststringremainderwhitespace(raw, blockCollector, operation)).toBe('x^4');
    });

    test('returns external match with asymmetric configured delimiters', () => {
        const raw = '<< f(x)=x^5 >>';
        const operation = { search: 'f(x)=' };
        const blockCollector = { blocks: [], delimiter: '<<', closingdelimiter: '>>' };
        expect(laststringremainderwhitespace(raw, blockCollector, operation)).toBe('x^5');
    });

    test('returns external match with symmetric configured delimiters', () => {
        const raw = '## f(x)=x^5 ##';
        const operation = { search: 'f(x)=' };
        const blockCollector = { blocks: [], delimiter: '##', closingdelimiter: '##' };
        expect(laststringremainderwhitespace(raw, blockCollector, operation)).toBe('x^5');
    });

    test('returns external match with regex-special configured delimiters', () => {
        const raw = '[ f(x)=x^6 ]';
        const operation = { search: 'f(x)=' };
        const blockCollector = { blocks: [], delimiter: '[', closingdelimiter: ']' };
        expect(laststringremainderwhitespace(raw, blockCollector, operation)).toBe('x^6');
    });

    test('returns match with asymmetric custom delimiters and inner whitespace', () => {
        const raw = ' f(x) = <<  x^4 + 1  >> ';
        const operation = { search: 'f(x) =' };
        const blockCollector = { blocks: [], delimiter: '<<', closingdelimiter: '>>' };
        expect(laststringremainderwhitespace(raw, blockCollector, operation)).toBe('x^4 + 1');
    });

    test('returns external match with symmetric custom delimiters and outer whitespace', () => {
        const raw = '  ##  f(x)=x^5 + 1  ## ';
        const operation = { search: 'f(x)=' };
        const blockCollector = { blocks: [], delimiter: '##', closingdelimiter: '##' };
        expect(laststringremainderwhitespace(raw, blockCollector, operation)).toBe('x^5 + 1');
    });

    test('returns custom-delimited match with trailing full stop and whitespace', () => {
        const raw = ' <<  f(x) = x^6 + 1  >> . ';
        const operation = { search: 'f(x) =' };
        const blockCollector = { blocks: [], delimiter: '<<', closingdelimiter: '>>' };
        expect(laststringremainderwhitespace(raw, blockCollector, operation)).toBe('x^6 + 1');
    });

    test('Fail to match because of previous text', () => {
        const raw = 'hence `f(x)=x^2`.';
        const operation = { search: 'f(x) =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('ERROR');
    });

    test('returns basic match backticks internal whitespace', () => {
        const raw = ' f(x)= x^2  ';
        const operation = { search: 'f(x)=' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns basic match backticks internal whitespace, full stop', () => {
        const raw = ' f(x)= x^2.  ';
        const operation = { search: 'f(x)=' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('x^2');
    });

    test('returns error because of internal whitespace in line but not search string', () => {
        const raw = ' f(x) = x^2  ';
        const operation = { search: 'f(x)=' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('ERROR');
    });

    test('returns last match', () => {
        const raw = ' a=1\n a = 2';
        const operation = { search: 'a =' };
        expect(laststringremainderwhitespace(raw, null, operation)).toBe('2');
    });
});
