const {mutations} = require('../../amd/src/metadata/mutations.js');

/**
 * Minimal mock of a Moodle reactive state collection.
 * Supports the same API used by the mutations: get, forEach, delete, add,
 * and Array.from() (which delegates to the underlying Map iterator).
 */
class MockStateCollection {
    constructor(items = []) {
        this._map = new Map(items.map(item => [String(item.id), item]));
    }
    get(id)             { return this._map.get(String(id)); }
    forEach(callback)   { for (const item of this._map.values()) callback(item); }
    delete(id)          { this._map.delete(String(id)); }
    add(item)           { this._map.set(String(item.id), item); }
    [Symbol.iterator]() { return this._map[Symbol.iterator](); }
}

function makeState(overrides = {}) {
    return {
        author:          new MockStateCollection(),
        language:        new MockStateCollection(),
        license:         {id: '', value: ''},
        isPartOf:        {id: '', value: ''},
        additional:      new MockStateCollection(),
        freeform:        {id: '', value: ''},
        metadataTicker:  {value: 1},
        ...overrides,
    };
}

function makeStateManager(state) {
    return {state, setReadOnly: jest.fn()};
}

// ── updateFromJson ─────────────────────────────────────────────────────────────

describe('updateFromJson', () => {
    test('copies each supplied property onto state', () => {
        const state = makeState();
        const stateManager = makeStateManager(state);
        mutations.updateFromJson(stateManager, {
            author: [{firstName: 'Alice'}],
            license: {id: '', value: 'MIT'},
        });
        expect(state.author).toEqual([{firstName: 'Alice'}]);
        expect(state.license).toEqual({id: '', value: 'MIT'});
    });

    test('increments metadataTicker', () => {
        const state = makeState();
        mutations.updateFromJson(makeStateManager(state), {});
        expect(state.metadataTicker.value).toBe(2);
    });

    test('opens and closes read-only around the update', () => {
        const stateManager = makeStateManager(makeState());
        mutations.updateFromJson(stateManager, {});
        expect(stateManager.setReadOnly).toHaveBeenCalledWith(false);
        expect(stateManager.setReadOnly).toHaveBeenCalledWith(true);
    });
});

// ── deleteRow ──────────────────────────────────────────────────────────────────

describe('deleteRow', () => {
    test('removes the specified author entry and leaves others intact', () => {
        const author = new MockStateCollection([
            {id: 1, firstName: 'Alice'},
            {id: 2, firstName: 'Bob'},
        ]);
        const stateManager = makeStateManager(makeState({author}));
        mutations.deleteRow(stateManager, 'author', '1');
        expect(stateManager.state.author.get('1')).toBeUndefined();
        expect(stateManager.state.author.get('2')).toBeDefined();
    });

    test('removes the specified language entry and leaves others intact', () => {
        const language = new MockStateCollection([
            {id: 1, value: 'en'},
            {id: 2, value: 'fr'},
        ]);
        const stateManager = makeStateManager(makeState({language}));
        mutations.deleteRow(stateManager, 'language', '2');
        expect(stateManager.state.language.get('2')).toBeUndefined();
        expect(stateManager.state.language.get('1')).toBeDefined();
    });

    test('scope: removes all additional entries sharing that scope', () => {
        const additional = new MockStateCollection([
            {id: 1, scope: 'dc', property: 'subject', qualifier: '', value: 'math'},
            {id: 2, scope: 'dc', property: 'title',   qualifier: '', value: 'My Title'},
            {id: 3, scope: 'lrmi', property: 'level', qualifier: '', value: 'HE'},
        ]);
        const stateManager = makeStateManager(makeState({additional}));
        mutations.deleteRow(stateManager, 'scope', '1');
        expect(stateManager.state.additional.get('1')).toBeUndefined();
        expect(stateManager.state.additional.get('2')).toBeUndefined();
    });

    test('scope: entries with a different scope are not removed', () => {
        const additional = new MockStateCollection([
            {id: 1, scope: 'dc',   property: 'subject', qualifier: '', value: 'math'},
            {id: 2, scope: 'lrmi', property: 'level',   qualifier: '', value: 'HE'},
        ]);
        const stateManager = makeStateManager(makeState({additional}));
        mutations.deleteRow(stateManager, 'scope', '1');
        expect(stateManager.state.additional.get('2')).toBeDefined();
    });

    test('opens and closes read-only around the delete', () => {
        const stateManager = makeStateManager(makeState({
            language: new MockStateCollection([{id: 1, value: 'en'}]),
        }));
        mutations.deleteRow(stateManager, 'language', '1');
        expect(stateManager.setReadOnly).toHaveBeenCalledWith(false);
        expect(stateManager.setReadOnly).toHaveBeenCalledWith(true);
    });
});

// ── addItem ────────────────────────────────────────────────────────────────────

describe('addItem', () => {
    test('language: adds a blank entry with id 1 to an empty collection', () => {
        const language = new MockStateCollection();
        const stateManager = makeStateManager(makeState({language}));
        mutations.addItem(stateManager, 'language');
        expect(stateManager.state.language.get('1')).toEqual({value: '', id: 1});
    });

    test('language: assigns next sequential id when collection is non-empty', () => {
        const language = new MockStateCollection([{id: 1, value: 'en'}]);
        const stateManager = makeStateManager(makeState({language}));
        mutations.addItem(stateManager, 'language');
        expect(stateManager.state.language.get('2')).toMatchObject({value: '', id: 2});
    });

    test('author: adds a blank entry when id is not "user"', () => {
        const author = new MockStateCollection();
        const stateManager = makeStateManager(makeState({author}));
        mutations.addItem(stateManager, 'author', 'blank');
        const item = stateManager.state.author.get('1');
        expect(item.firstName).toBe('');
        expect(item.lastName).toBe('');
        expect(item.institution).toBe('');
        expect(item.year).toBe(String(new Date().getFullYear()));
    });

    test('author: prefills user data when id is "user"', () => {
        const author = new MockStateCollection();
        const stateManager = makeStateManager(makeState({author}));
        mutations.addItem(stateManager, 'author', 'user');
        const item = stateManager.state.author.get('1');
        expect(item.firstName).toBe('Jane');
        expect(item.lastName).toBe('Doe');
        expect(item.institution).toBe('Test University');
    });

    test('author: does not add a second blank entry while one already exists', () => {
        const author = new MockStateCollection([
            {id: 1, firstName: 'Alice', lastName: 'Author', institution: 'Uni', year: '2024'},
            {id: 2, firstName: ' ', lastName: '', institution: '', year: '2025'},
        ]);
        const stateManager = makeStateManager(makeState({author}));
        mutations.addItem(stateManager, 'author', 'blank');
        expect(stateManager.state.author.get('2')).toEqual({
            id: 2,
            firstName: ' ',
            lastName: '',
            institution: '',
            year: '2025',
        });
        expect(stateManager.state.author.get('3')).toBeUndefined();
    });

    test('author: reuses a blank entry for "user" instead of adding a new one', () => {
        const author = new MockStateCollection([
            {id: 1, firstName: ' ', lastName: '', institution: '', year: '2023'},
        ]);
        const stateManager = makeStateManager(makeState({author}));
        mutations.addItem(stateManager, 'author', 'user');
        expect(stateManager.state.author.get('1')).toEqual({
            id: 1,
            firstName: 'Jane',
            lastName: 'Doe',
            institution: 'Test University',
            year: String(new Date().getFullYear()),
        });
        expect(stateManager.state.author.get('2')).toBeUndefined();
    });

    test('scope: adds a blank additional entry with all empty fields', () => {
        const additional = new MockStateCollection();
        const stateManager = makeStateManager(makeState({additional}));
        mutations.addItem(stateManager, 'scope');
        expect(stateManager.state.additional.get('1')).toEqual(
            {scope: '', property: '', qualifier: '', value: '', id: 1}
        );
    });

    test('property: inherits scope from the referenced additional entry', () => {
        const additional = new MockStateCollection([
            {id: 1, scope: 'dc', property: 'subject', qualifier: '', value: 'math'},
        ]);
        const stateManager = makeStateManager(makeState({additional}));
        mutations.addItem(stateManager, 'property', '1');
        const newItem = stateManager.state.additional.get('2');
        expect(newItem.scope).toBe('dc');
        expect(newItem.property).toBe('');
        expect(newItem.qualifier).toBe('');
        expect(newItem.value).toBe('');
    });

    test('opens and closes read-only around the add', () => {
        const stateManager = makeStateManager(makeState({
            language: new MockStateCollection(),
        }));
        mutations.addItem(stateManager, 'language');
        expect(stateManager.setReadOnly).toHaveBeenCalledWith(false);
        expect(stateManager.setReadOnly).toHaveBeenCalledWith(true);
    });
});

// ── updateAll ──────────────────────────────────────────────────────────────────

describe('updateAll', () => {
    test('updates a scalar field (id=0) directly on the state property', async () => {
        const state = makeState();
        await mutations.updateAll(makeStateManager(state), [['smdi_0_license_value', 'MIT']]);
        expect(state.license.value).toBe('MIT');
    });

    test('updates a collection item by id', async () => {
        const language = new MockStateCollection([{id: 1, value: 'en'}]);
        const state = makeState({language});
        await mutations.updateAll(makeStateManager(state), [['smdi_1_language_value', 'fr']]);
        expect(state.language.get('1').value).toBe('fr');
    });

    test('updates an additional item field', async () => {
        const additional = new MockStateCollection([
            {id: 1, scope: 'dc', property: 'subject', qualifier: '', value: 'math'},
        ]);
        const state = makeState({additional});
        await mutations.updateAll(makeStateManager(state), [['smdi_1_additional_property', 'title']]);
        expect(state.additional.get('1').property).toBe('title');
    });

    test('scope update propagates to all additional entries sharing that scope', async () => {
        const additional = new MockStateCollection([
            {id: 1, scope: 'dc', property: 'subject', qualifier: '', value: 'math'},
            {id: 2, scope: 'dc', property: 'title',   qualifier: '', value: 'My Title'},
        ]);
        const state = makeState({additional});
        await mutations.updateAll(makeStateManager(state), [['smdi_1_additional_scope', 'lrmi']]);
        expect(state.additional.get('1').scope).toBe('lrmi');
        expect(state.additional.get('2').scope).toBe('lrmi');
    });

    test('scope update does not affect entries with a different scope', async () => {
        const additional = new MockStateCollection([
            {id: 1, scope: 'dc',   property: 'subject', qualifier: '', value: 'math'},
            {id: 2, scope: 'lrmi', property: 'level',   qualifier: '', value: 'HE'},
        ]);
        const state = makeState({additional});
        await mutations.updateAll(makeStateManager(state), [['smdi_1_additional_scope', 'schema']]);
        expect(state.additional.get('2').scope).toBe('lrmi');
    });

    test('rejects when a qualifier-less row has the same scope+property as a qualified row', async () => {
        // This shouldn't really happen anyway but hey.
        const additional = new MockStateCollection([
            {id: 1, scope: 'dc', property: 'subject', qualifier: '',   value: 'math'},
            {id: 2, scope: 'dc', property: 'subject', qualifier: 'en', value: 'physics'},
        ]);
        const state = makeState({additional});
        await expect(mutations.updateAll(makeStateManager(state), [])).rejects.toBe('1');
    });

    test('rejects when inputArray introduces a qualifier that conflicts with an existing qualifier-less row', async () => {
        const additional = new MockStateCollection([
            {id: 1, scope: 'dc', property: 'subject', qualifier: '', value: 'math'},
            {id: 2, scope: 'dc', property: 'subject', qualifier: '', value: 'physics'},
        ]);
        const state = makeState({additional});
        // Setting a qualifier on row 2 leaves row 1 qualifier-less with the same scope+property.
        await expect(
            mutations.updateAll(makeStateManager(state), [['smdi_2_additional_qualifier', 'en']])
        ).rejects.toBe('1');
    });

    test('rejects when inputArray introduces a qualifier-less row that conflicts with an existing qualified row', async () => {
        const additional = new MockStateCollection([
            {id: 1, scope: 'dc', property: 'subject', qualifier: 'bob', value: 'math'},
            {id: 2, scope: 'dc', property: 'subject', qualifier: 'bob', value: 'physics'},
        ]);
        const state = makeState({additional});
        // Setting a qualifier on row 2 leaves row 1 qualifier-less with the same scope+property.
        await expect(
            mutations.updateAll(makeStateManager(state), [['smdi_1_additional_qualifier', '']])
        ).rejects.toBe('1');
    });

    test('applies concurrent scope, property and qualifier updates across multiple rows', async () => {
        // State: three additional rows across two scopes.
        //   row 1 – dc / subject / en  = 'Algebra'
        //   row 2 – dc / level   / en  = 'HE'
        //   row 3 – lrmi / topic / ''  = 'STEM'
        const additional = new MockStateCollection([
            {id: 1, scope: 'dc',   property: 'subject', qualifier: 'en', value: 'Algebra'},
            {id: 2, scope: 'dc',   property: 'level',   qualifier: 'en', value: 'HE'},
            {id: 3, scope: 'lrmi', property: 'topic',   qualifier: '',   value: 'STEM'},
        ]);
        const state = makeState({additional});

        await mutations.updateAll(makeStateManager(state), [
            // Rename the entire 'dc' scope to 'schema' (rows 1 and 2 are affected).
            ['smdi_1_additional_scope',     'schema'],
            // Change the property of row 1 from 'subject' to 'name'.
            ['smdi_1_additional_property',  'name'],
            // Change the qualifier of row 2 from 'en' to 'fr'.
            ['smdi_2_additional_qualifier', 'fr'],
            // Change the value of row 3.
            ['smdi_3_additional_value',     'Science'],
        ]);

        // Both dc rows should now have scope 'schema'.
        expect(state.additional.get('1').scope).toBe('schema');
        expect(state.additional.get('2').scope).toBe('schema');
        // Row 3 scope is untouched.
        expect(state.additional.get('3').scope).toBe('lrmi');

        // Row 1 property updated.
        expect(state.additional.get('1').property).toBe('name');
        // Row 2 property untouched.
        expect(state.additional.get('2').property).toBe('level');

        // Row 2 qualifier updated.
        expect(state.additional.get('2').qualifier).toBe('fr');
        // Row 1 qualifier untouched.
        expect(state.additional.get('1').qualifier).toBe('en');

        // Row 3 value updated.
        expect(state.additional.get('3').value).toBe('Science');
    });

    test('resolves with "Success" when there are no conflicts', async () => {
        const state = makeState();
        await expect(mutations.updateAll(makeStateManager(state), [])).resolves.toBe('Success');
    });

    test('increments metadataTicker on success', async () => {
        const state = makeState();
        await mutations.updateAll(makeStateManager(state), []);
        expect(state.metadataTicker.value).toBe(2);
    });

    test('processes multiple fields in a single call', async () => {
        const author = new MockStateCollection([{id: 1, firstName: '', lastName: '', institution: '', year: ''}]);
        const state = makeState({author});
        await mutations.updateAll(makeStateManager(state), [
            ['smdi_1_author_firstName', 'Alice'],
            ['smdi_1_author_lastName',  'Smith'],
        ]);
        expect(state.author.get('1').firstName).toBe('Alice');
        expect(state.author.get('1').lastName).toBe('Smith');
    });

    test('opens and closes read-only around the update', async () => {
        const stateManager = makeStateManager(makeState());
        await mutations.updateAll(stateManager, []);
        expect(stateManager.setReadOnly).toHaveBeenCalledWith(false);
        expect(stateManager.setReadOnly).toHaveBeenCalledWith(true);
    });

    test('does not mutate state when promise is rejected', async () => {
        const additional = new MockStateCollection([
            {id: 1, scope: 'dc', property: 'subject', qualifier: '',   value: 'math'},
            {id: 2, scope: 'dc', property: 'subject', qualifier: 'en', value: 'physics'},
        ]);
        const state = makeState({additional});
        const stateManager = makeStateManager(state);
        await mutations.updateAll(stateManager, []).catch(() => {});
        // State mutation only happens after the conflict check passes.
        expect(stateManager.setReadOnly).not.toHaveBeenCalled();
    });
});
