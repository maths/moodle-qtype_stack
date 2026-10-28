const {metadata} = require('../../amd/src/metadata/metadata.js');

// ── reviver ────────────────────────────────────────────────────────────────────

describe('reviver', () => {
    test('author: assigns sequential ids to each item', () => {
        const input = [{firstName: 'Alice'}, {firstName: 'Bob'}];
        expect(metadata.reviver('author', input)).toEqual([
            {firstName: 'Alice', id: 1},
            {firstName: 'Bob', id: 2},
        ]);
    });

    test('language: converts array of strings to [{id, value}] objects', () => {
        expect(metadata.reviver('language', ['en', 'fr'])).toEqual([
            {id: 1, value: 'en'},
            {id: 2, value: 'fr'},
        ]);
    });

    test('language: returns empty array for empty input', () => {
        expect(metadata.reviver('language', [])).toEqual([]);
    });

    test('license: wraps value in {value: ...}', () => {
        expect(metadata.reviver('license', 'MIT')).toEqual({value: 'MIT'});
    });

    test('isPartOf: wraps value in {value: ...}', () => {
        expect(metadata.reviver('isPartOf', 27)).toEqual({value: 27});
    });

    test('freeform: JSON-stringifies the value into {value: ...}', () => {
        expect(metadata.reviver('freeform', {a: 1})).toEqual({value: '{"a":1}'});
    });

    test('freeform: handles empty object', () => {
        expect(metadata.reviver('freeform', {})).toEqual({value: '{}'});
    });

    test('default: returns value unchanged', () => {
        expect(metadata.reviver('title', 'Hello')).toBe('Hello');
        expect(metadata.reviver('creator', {firstName: 'Alice'})).toEqual({firstName: 'Alice'});
    });
});

// ── replacer ───────────────────────────────────────────────────────────────────

describe('replacer', () => {
    test('metadataTicker: returns undefined', () => {
        expect(metadata.replacer('metadataTicker', 'any')).toBeUndefined();
    });

    test('id: returns undefined', () => {
        expect(metadata.replacer('id', 5)).toBeUndefined();
    });

    test('language: extracts .value from each item', () => {
        expect(metadata.replacer('language', [{value: 'en'}, {value: 'fr'}])).toEqual(['en', 'fr']);
    });

    test('language: returns empty array for empty input', () => {
        expect(metadata.replacer('language', [])).toEqual([]);
    });

    test('license: returns the value property', () => {
        expect(metadata.replacer('license', {value: 'MIT'})).toBe('MIT');
    });

    test('isPartOf: returns the value property', () => {
        expect(metadata.replacer('isPartOf', {value: 'course1'})).toBe('course1');
    });

    test('freeform: returns the value string', () => {
        expect(metadata.replacer('freeform', {value: '{"a":1}'})).toBe('{"a":1}');
    });

    test('freeform: returns "{}" when value is empty string', () => {
        expect(metadata.replacer('freeform', {value: ''})).toBe('{}');
    });

    test('freeform: returns "{}" when value is null', () => {
        expect(metadata.replacer('freeform', {value: null})).toBe('{}');
    });

    test('additional: empty array returns JSON-stringified empty object', () => {
        expect(metadata.replacer('additional', [])).toBe('{}');
    });

    test('additional: unqualified item produces flat nested object', () => {
        const input = [{id: 1, scope: 'dc', property: 'subject', qualifier: '', value: 'math'}];
        expect(metadata.replacer('additional', input)).toBe('{"dc":{"subject":"math"}}');
    });

    test('additional: qualified item produces doubly-nested object', () => {
        const input = [{id: 1, scope: 'dc', property: 'subject', qualifier: 'en', value: 'math'}];
        expect(metadata.replacer('additional', input)).toBe('{"dc":{"subject":{"en":"math"}}}');
    });

    test('additional: two items with same scope/property are combined into an array', () => {
        const input = [
            {id: 1, scope: 'dc', property: 'subject', qualifier: '', value: 'math'},
            {id: 2, scope: 'dc', property: 'subject', qualifier: '', value: 'physics'},
        ];
        expect(metadata.replacer('additional', input)).toBe('{"dc":{"subject":["math","physics"]}}');
    });

    test('additional: two items with same scope/property/qualifier are combined into an array', () => {
        const input = [
            {id: 1, scope: 'dc', property: 'subject', qualifier: 'bob', value: 'math'},
            {id: 2, scope: 'dc', property: 'subject', qualifier: 'bob', value: 'physics'},
        ];
        expect(metadata.replacer('additional', input)).toBe('{"dc":{"subject":{"bob":["math","physics"]}}}');
    });

    test('additional: items from different scopes stay separate', () => {
        const input = [
            {id: 1, scope: 'dc', property: 'subject', qualifier: '', value: 'math'},
            {id: 2, scope: 'lrmi', property: 'subject', qualifier: '', value: 'HE'},
        ];
        const result = JSON.parse(metadata.replacer('additional', input));
        expect(result).toEqual({dc: {subject: 'math'}, lrmi: {subject: 'HE'}});
    });

    test('default: returns value unchanged', () => {
        expect(metadata.replacer('title', 'Hello World')).toBe('Hello World');
        expect(metadata.replacer('firstName', 'Alice')).toBe('Alice');
    });
});

// ── stripFields ────────────────────────────────────────────────────────────────

describe('stripFields', () => {
    test('keeps only the specified fields', () => {
        expect(metadata.stripFields({a: 1, b: 2, c: 3}, ['a', 'c'])).toEqual({a: 1, c: 3});
    });

    test('returns empty object when no fields match', () => {
        expect(metadata.stripFields({a: 1}, ['b'])).toEqual({});
    });

    test('returns empty object for empty input object', () => {
        expect(metadata.stripFields({}, ['a', 'b'])).toEqual({});
    });

    test('returns empty object when fields list is empty', () => {
        expect(metadata.stripFields({a: 1, b: 2}, [])).toEqual({});
    });
});

// ── addFields ──────────────────────────────────────────────────────────────────

describe('addFields', () => {
    test('adds missing fields as empty string', () => {
        expect(metadata.addFields({a: 'hello'}, ['a', 'b'])).toEqual({a: 'hello', b: ''});
    });

    test('converts existing values to strings', () => {
        expect(metadata.addFields({a: 42, b: true}, ['a', 'b'])).toEqual({a: '42', b: 'true'});
    });

    test('populates all fields as empty string for empty object', () => {
        expect(metadata.addFields({}, ['x', 'y'])).toEqual({x: '', y: ''});
    });
});

// ── tidyObject ─────────────────────────────────────────────────────────────────

describe('tidyObject', () => {
    test('strips unlisted fields and adds missing ones as empty string', () => {
        expect(metadata.tidyObject({a: 1, b: 2, c: 'hello'}, ['a', 'c', 'd']))
            .toEqual({a: '1', c: 'hello', d: ''});
    });

    test('handles null input by returning all fields as empty string', () => {
        expect(metadata.tidyObject(null, ['a', 'b'])).toEqual({a: '', b: ''});
    });

    test('handles non-object input by returning all fields as empty string', () => {
        expect(metadata.tidyObject('not an object', ['a'])).toEqual({a: ''});
    });

    test('handles undefined input', () => {
        expect(metadata.tidyObject(undefined, ['a'])).toEqual({a: ''});
    });
});

// ── jsonToState ────────────────────────────────────────────────────────────────

describe('jsonToState', () => {
    test('empty JSON produces a fully-defaulted state', () => {
        expect(metadata.jsonToState('{}')).toEqual({
            author:     [],
            language:   [],
            license:    {id: '', value: ''},
            isPartOf:   {id: '', value: ''},
            additional: [],
            freeform:   {id: '', value: ''},
        });
    });

    test('parses author fields and coerces values to strings', () => {
        const input = JSON.stringify({
            author: [{firstName: 'Alice', lastName: 'Smith', institution: 'Uni', year: 2025}],
        });
        expect(metadata.jsonToState(input).author)
            .toEqual([{id: '1', firstName: 'Alice', lastName: 'Smith', institution: 'Uni', year: '2025'}]);
    });

    test('strips unrecognised author fields', () => {
        const input = JSON.stringify({author: [{firstName: 'Alice', unknownField: 'ignored'}]});
        expect(metadata.jsonToState(input).author[0]).not.toHaveProperty('unknownField');
    });

    test('parses language strings into [{id, value}] objects', () => {
        const input = JSON.stringify({language: ['en', 'fr']});
        expect(metadata.jsonToState(input).language)
            .toEqual([{id: '1', value: 'en'}, {id: '2', value: 'fr'}]);
    });

    test('parses license string into {id, value}', () => {
        const input = JSON.stringify({license: 'MIT'});
        expect(metadata.jsonToState(input).license).toEqual({id: '', value: 'MIT'});
    });

    test('parses isPartOf string into {id, value}', () => {
        const input = JSON.stringify({isPartOf: 'course1'});
        expect(metadata.jsonToState(input).isPartOf).toEqual({id: '', value: 'course1'});
    });

    test('strips top-level unrecognised fields', () => {
        const input = JSON.stringify({unknownField: 'should be removed'});
        expect(metadata.jsonToState(input)).not.toHaveProperty('unknownField');
    });

    test('parses additional unqualified entries in array form', () => {
        const input = JSON.stringify({additional: {dc: {subject: ['math', 'physics']}}});
        expect(metadata.jsonToState(input).additional)
            .toEqual([
                {id: 1, scope: 'dc', property: 'subject', qualifier: '', value: 'math'},
                {id: 2, scope: 'dc', property: 'subject', qualifier: '', value: 'physics'},
            ]);
    });

    test('parses additional qualified entries in array form', () => {
        const input = JSON.stringify({additional: {dc: {subject: {qual: ['math', 'physics']}}}});
        expect(metadata.jsonToState(input).additional)
            .toEqual([
                {id: 1, scope: 'dc', property: 'subject', qualifier: 'qual', value: 'math'},
                {id: 2, scope: 'dc', property: 'subject', qualifier: 'qual', value: 'physics'},
            ]);
    });

    test('parses additional unqualified entry', () => {
        const input = JSON.stringify({additional: {dc: {subject: 'math'}}});
        expect(metadata.jsonToState(input).additional)
            .toEqual([{id: 1, scope: 'dc', property: 'subject', qualifier: '', value: 'math'}]);
    });

    test('parses additional qualified entry', () => {
        const input = JSON.stringify({additional: {dc: {subject: {en: 'math'}}}});
        expect(metadata.jsonToState(input).additional)
            .toEqual([{id: 1, scope: 'dc', property: 'subject', qualifier: 'en', value: 'math'}]);
    });

    test('parses additional qualified entry containing key words', () => {
        const input = JSON.stringify({additional: {dc: {additional: {license: 'math'}}}});
        expect(metadata.jsonToState(input).additional)
            .toEqual([{id: 1, scope: 'dc', property: 'additional', qualifier: 'license', value: 'math'}]);
    });

    test('parses freeform object into {id, value} where value is JSON string', () => {
        const input = JSON.stringify({freeform: {custom: 'data'}});
        expect(metadata.jsonToState(input).freeform)
            .toEqual({id: '', value: '{"custom":"data"}'});
    });

    test('parses freeform object containing key words into {id, value}', () => {
        const input = JSON.stringify({freeform: {additional: 'data'}});
        expect(metadata.jsonToState(input).freeform)
            .toEqual({id: '', value: '{"additional":"data"}'});
    });
});

// ── jsonStringify ──────────────────────────────────────────────────────────────

describe('jsonStringify', () => {
    const baseState = {
        author: [
            {id: '1', firstName: 'Alice', lastName: 'Smith', institution: 'Uni', year: '2025'},
            {id: '2', firstName: 'Bob', lastName: 'Jones', institution: 'Uni2', year: '2026'},
        ],
        language:    [{id: 1, value: 'en'}, {id: 2, value: 'fr'}],
        license:     {id: 0, value: 'MIT'},
        isPartOf:    {id: 0, value: 'course1'},
        additional:  [
            {id: 1, scope: 'dc', property: 'subject', qualifier: 'bob', value: 'math'},
            {id: 2, scope: 'dc', property: 'subject', qualifier: 'bob', value: 'physics'},
        ],
        freeform:    {id: '', value: '{"times":[1,2,3]}'},
    };

    test('round-trips author fields unchanged and strips ids', () => {
        const result = JSON.parse(metadata.jsonStringify(baseState));
        expect(result.author).toEqual([
            {firstName: 'Alice', lastName: 'Smith', institution: 'Uni', year: '2025'},
            {firstName: 'Bob', lastName: 'Jones', institution: 'Uni2', year: '2026'},
        ]);
    });

    test('converts language objects back to string array', () => {
        const result = JSON.parse(metadata.jsonStringify(baseState));
        expect(result.language).toEqual(['en', 'fr']);
    });

    test('converts license object back to plain value', () => {
        const result = JSON.parse(metadata.jsonStringify(baseState));
        expect(result.license).toBe('MIT');
    });

    test('converts isPartOf object back to plain value', () => {
        const result = JSON.parse(metadata.jsonStringify(baseState));
        expect(result.isPartOf).toBe('course1');
    });

    test('removes metadataTicker field', () => {
        const state = {...baseState, metadataTicker: {value: 5}};
        const result = JSON.parse(metadata.jsonStringify(state));
        expect(result).not.toHaveProperty('metadataTicker');
    });

    test('converts additional qualified items into nested object', () => {
        const result = JSON.parse(metadata.jsonStringify(baseState));
        expect(result.additional).toEqual({dc: {subject: {bob: ['math', 'physics']}}});
    });

    test('converts additional array items back to nested object', () => {
        const state = {
            ...baseState,
            additional: [{id: 1, scope: 'dc', property: 'subject', qualifier: '', value: 'math'}],
        };
        const result = JSON.parse(metadata.jsonStringify(state));
        expect(result.additional).toEqual({dc: {subject: 'math'}});
    });

    test('converts freeform value string back to parsed object', () => {
        const result = JSON.parse(metadata.jsonStringify(baseState));
        expect(result.freeform).toEqual({times: [1, 2, 3]});
    });

    test('respects spacing parameter for pretty-printing', () => {
        const result = metadata.jsonStringify(baseState, 2);
        expect(result).toContain('\n');
        expect(result).toContain('\n  "author"');
    });

    test('produces compact output with no spacing', () => {
        const result = metadata.jsonStringify(baseState);
        expect(result).not.toContain('\n');
    });
});
