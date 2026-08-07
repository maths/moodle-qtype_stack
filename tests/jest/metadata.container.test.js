const ContainerClass = require('../../amd/src/metadata/container.js').default;
const {metadata} = require('qtype_stack/metadata/metadata');
const {notifyFieldValidationFailure} = require('core_form/events');
const {BaseComponent} = require('core/reactive');

// ── helpers ────────────────────────────────────────────────────────────────────

/**
 * Build a testable container instance without going through the BaseComponent
 * constructor (which needs real DOM elements and a reactive instance).
 * We set required properties manually then call create() so the container's
 * own initialisation runs.
 */
function makeInstance(reactiveOverrides = {}) {
    const reactive = {dispatch: jest.fn(), ...reactiveOverrides};
    const instance = Object.create(ContainerClass.prototype);
    // Provide the BaseComponent API that container methods rely on.
    instance.element = null;
    instance.reactive = reactive;
    instance.getElement = jest.fn();
    instance.getElements = jest.fn(() => []);
    instance.addEventListener = jest.fn();
    instance.renderComponent = jest.fn().mockResolvedValue(undefined);
    // Runs the subclass create() hook which sets this.name, this.selectors, etc.
    instance.create();
    return {instance, reactive};
}

// DOM mock - document.querySelector is used directly in revert() and reloadContainerComponent().
const mockQuerySelector = jest.fn();

beforeAll(() => {
    global.document = {querySelector: mockQuerySelector};
});

beforeEach(() => {
    jest.resetAllMocks();
    mockQuerySelector.mockReturnValue(null);
    // After resetAllMocks the prototype stubs need their default implementations restored.
    BaseComponent.prototype.getElements.mockReturnValue([]);
    BaseComponent.prototype.renderComponent.mockResolvedValue(undefined);
    // Prevent brokenMetadata state leaking between tests.
    delete metadata.lib.brokenMetadata;
});

// ── createDataElement ──────────────────────────────────────────────────────────

describe('createDataElement', () => {
    test('returns the required flag as-is', () => {
        const {instance} = makeInstance();
        expect(instance.createDataElement(true, 1, 'language_value', 'en').required).toBe(true);
        expect(instance.createDataElement(false, 1, 'language_value', 'en').required).toBe(false);
    });

    test('element.value matches the supplied value', () => {
        const {instance} = makeInstance();
        const el = instance.createDataElement(true, 0, 'license_value', 'MIT');
        expect(el.element.value).toBe('MIT');
    });

    test('element IDs are composed from id and tag', () => {
        const {instance} = makeInstance();
        const el = instance.createDataElement(false, 3, 'author_lastName', 'Smith');
        expect(el.element.id).toBe('smdi_3_author_lastName');
        expect(el.element.name).toBe('smdi_3_author_lastName');
        expect(el.element.wrapperid).toBe('fitem_smdi_3_author_lastName');
        expect(el.element.iderror).toBe('smde_3_author_lastName_error');
    });

    test('works with id=0 for single-instance elements', () => {
        const {instance} = makeInstance();
        const el = instance.createDataElement(true, 0, 'license_value', 'MIT');
        expect(el.element.id).toBe('smdi_0_license_value');
    });
});

// ── update ─────────────────────────────────────────────────────────────────────

describe('update', () => {
    function makeInputEl(id, value, classes = []) {
        return {
            id,
            value,
            classList: {contains: (c) => classes.includes(c)},
        };
    }

    test('skips validation and dispatches updateAll when mustValidate=false', async () => {
        const {instance, reactive} = makeInstance();
        const allInputs = [makeInputEl('smdi_0_license_value', 'MIT')];
        instance.getElements.mockImplementation(sel => {
            if (sel === instance.selectors.ALLINPUTS) {
                return allInputs;
            }
            return [];
        });
        reactive.dispatch.mockResolvedValue(undefined);
        const result = await instance.update(false);
        expect(reactive.dispatch).toHaveBeenCalledWith('updateAll', [['smdi_0_license_value', 'MIT']]);
        expect(result).toBe(true);
    });

    test('returns false and notifies when a required field is empty', async () => {
        const {instance, reactive} = makeInstance();
        const emptyEl = makeInputEl('smdi_1_language_value', '');
        instance.getElements.mockImplementation(sel => {
            if (sel === instance.selectors.REQUIREDINPUTS) {
                return [emptyEl];
            }
            return [];
        });
        const result = await instance.update(true);
        expect(notifyFieldValidationFailure).toHaveBeenCalledWith(emptyEl, 'Required');
        expect(reactive.dispatch).not.toHaveBeenCalled();
        expect(result).toBe(false);
    });

    test('clears error notification for an is-invalid field that now has a value', async () => {
        const {instance, reactive} = makeInstance();
        const fixedEl = makeInputEl('smdi_1_language_value', 'en', ['is-invalid']);
        instance.getElements.mockImplementation(sel => {
            if (sel === instance.selectors.REQUIREDINPUTS) {
                return [fixedEl];
            }
            if (sel === instance.selectors.ALLINPUTS) {
                return [fixedEl];
            }
            return [];
        });
        reactive.dispatch.mockResolvedValue(undefined);
        await instance.update(true);
        expect(notifyFieldValidationFailure).toHaveBeenCalledWith(fixedEl, '');
    });

    test('returns false and notifies on invalid freeform JSON', async () => {
        const {instance, reactive} = makeInstance();
        const freeformEl = {value: '{bad json', classList: {contains: () => false}};
        instance.getElements.mockReturnValue([]);
        instance.getElement.mockImplementation(sel => {
            if (sel === '#smdi_0_freeform_value') {
                return freeformEl;
            }
            return null;
        });
        const result = await instance.update(true);
        expect(notifyFieldValidationFailure).toHaveBeenCalledWith(freeformEl, expect.any(String));
        expect(reactive.dispatch).not.toHaveBeenCalled();
        expect(result).toBe(false);
    });

    test('clears freeform error and dispatches when freeform JSON is valid', async () => {
        const {instance, reactive} = makeInstance();
        const freeformEl = {value: '{"ok":true}', classList: {contains: () => false}};
        const authorEl = makeInputEl('smdi_1_author_firstName', 'Alice');
        instance.getElements.mockImplementation(sel => {
            if (sel === instance.selectors.REQUIREDINPUTS) {
                return [];
            }
            if (sel === instance.selectors.ALLINPUTS) {
                return [authorEl];
            }
            return [];
        });
        instance.getElement.mockImplementation(sel => {
            if (sel === '#smdi_0_freeform_value') {
                return freeformEl;
            }
            return null;
        });
        reactive.dispatch.mockResolvedValue(undefined);
        const result = await instance.update(true);
        expect(notifyFieldValidationFailure).toHaveBeenCalledWith(freeformEl, '');
        expect(result).toBe(true);
    });

    test('returns false and notifies per-element when dispatch throws comma-separated IDs', async () => {
        const {instance, reactive} = makeInstance();
        instance.getElements.mockReturnValue([]);
        reactive.dispatch.mockRejectedValue('2,5');
        const errorEl = {value: ''};
        instance.getElement.mockImplementation(sel => {
            if (sel.includes('smdi_2_additional_qualifier') || sel.includes('smdi_5_additional_qualifier')) {
                return errorEl;
            }
            return null;
        });
        const result = await instance.update(false);
        expect(notifyFieldValidationFailure).toHaveBeenCalledWith(errorEl, 'Required');
        expect(result).toBe(false);
    });
});

// ── addItem ────────────────────────────────────────────────────────────────────

describe('addItem', () => {
    test('does not dispatch when update() returns false', async () => {
        const {instance, reactive} = makeInstance();
        jest.spyOn(instance, 'update').mockResolvedValue(false);
        await instance.addItem({currentTarget: {id: 'smd_language_0_add'}});
        expect(reactive.dispatch).not.toHaveBeenCalled();
    });

    test('dispatches addItem with type derived from event.currentTarget.id when update succeeds', async () => {
        const {instance, reactive} = makeInstance();
        jest.spyOn(instance, 'update').mockResolvedValue(true);
        reactive.dispatch.mockResolvedValue(undefined);
        await instance.addItem({currentTarget: {id: 'smd_author_0_add'}});
        expect(reactive.dispatch).toHaveBeenCalledWith('addItem', 'author', '0');
    });
});

// ── deleteItem ─────────────────────────────────────────────────────────────────

describe('deleteItem', () => {
    test('does not dispatch when update() returns false', async () => {
        const {instance, reactive} = makeInstance();
        jest.spyOn(instance, 'update').mockResolvedValue(false);
        await instance.deleteItem({currentTarget: {id: 'smd_author_1_delete'}});
        expect(reactive.dispatch).not.toHaveBeenCalled();
    });

    test('dispatches deleteRow with id parts from event.currentTarget.id when update succeeds', async () => {
        const {instance, reactive} = makeInstance();
        jest.spyOn(instance, 'update').mockResolvedValue(true);
        reactive.dispatch.mockResolvedValue(undefined);
        await instance.deleteItem({currentTarget: {id: 'smd_author_1_delete'}});
        expect(reactive.dispatch).toHaveBeenCalledWith('deleteRow', 'author', '1');
    });
});

// ── focus handling ─────────────────────────────────────────────────────────────

describe('focus handling', () => {
    test('restorePendingFocus moves focus to the first input in the last added row', () => {
        const {instance} = makeInstance();
        const oldInput = {
            id: 'smdi_1_language_value',
            focus: jest.fn(),
            select: jest.fn(),
        };
        const newInput = {
            id: 'smdi_2_language_value',
            focus: jest.fn(),
            select: jest.fn(),
        };
        instance.getElements.mockImplementation(sel => {
            if (sel === instance.selectors.ADDLANGUAGE) {
                return [oldInput, newInput];
            }
            return [];
        });

        instance.queueFocus('add', 'language', null);
        instance.restorePendingFocus();

        expect(newInput.focus).toHaveBeenCalled();
        expect(newInput.select).toHaveBeenCalled();
        expect(oldInput.focus).not.toHaveBeenCalled();
        expect(oldInput.select).not.toHaveBeenCalled();
    });

    test('restorePendingFocus moves focus to the corresponding add button after deletion', () => {
        const {instance} = makeInstance();
        const add = {
            id: 'smd_language_0_add',
            focus: jest.fn(),
            matches: jest.fn(sel => sel === instance.selectors.ADDBUTTONS),
        };
        instance.getElement.mockImplementation(sel => {
            if (sel === instance.selectors.DELETELANGUAGE) {
                return add;
            }
            return null;
        });

        instance.queueFocus('delete', 'language', {id: 'smd_language_1_delete'});
        instance.restorePendingFocus();

        expect(add.focus).toHaveBeenCalledWith({focusVisible: true});
    });

    test('restorePendingFocus does not override validation errors', () => {
        const {instance} = makeInstance();
        const add = {
            id: 'smd_language_0_add',
            focus: jest.fn(),
        };
        instance.getElements.mockImplementation(sel => {
            if (sel === instance.selectors.VALIDATIONERRORS) {
                return [{id: 'smdi_1_language_value'}];
            }
            return [];
        });
        instance.getElement.mockImplementation(sel => {
            if (sel === instance.selectors.DELETELANGUAGE) {
                return add;
            }
            return null;
        });

        instance.queueFocus('delete', 'language', {id: 'smd_language_1_delete'});
        instance.restorePendingFocus();

        expect(add.focus).not.toHaveBeenCalled();
    });

    test('restorePendingFocus uses the property add button after deleting additional metadata', () => {
        const {instance} = makeInstance();
        const add = {id: 'smd_property_1_add', focus: jest.fn()};
        const sourceScopeCard = {
            querySelector: jest.fn(sel => {
                if (sel === instance.selectors.ADDSCOPE) {
                    return {value: 'dc'};
                }
                return null;
            }),
        };
        const renderedScopeCard = {
            id: 'qtype-stack-metadata-scope-2',
            querySelector: jest.fn(sel => {
                if (sel === instance.selectors.ADDSCOPE) {
                    return {value: 'dc'};
                }
                if (sel === instance.selectors.DELETEADDITIONAL) {
                    return add;
                }
                return null;
            }),
        };
        const deletedPropertyButton = {
            closest: jest.fn(() => sourceScopeCard),
        };
        instance.getElements.mockImplementation(sel => {
            if (sel === instance.selectors.SCOPECARD) {
                return [renderedScopeCard];
            }
            return [];
        });

        instance.queueFocus('delete', 'additional', deletedPropertyButton);
        instance.restorePendingFocus();

        expect(add.focus).toHaveBeenCalled();
    });

    test('restorePendingFocus moves focus to the add property button within the same scope', () => {
        const {instance} = makeInstance();
        const wrongScopeAdd = {
            id: 'smd_property_1_add',
            focus: jest.fn(),
            matches: jest.fn(sel => sel === instance.selectors.ADDBUTTONS),
        };
        const matchingScopeAdd = {
            id: 'smd_property_3_add',
            focus: jest.fn(),
            matches: jest.fn(sel => sel === instance.selectors.ADDBUTTONS),
        };
        const wrongScopeCard = {
            querySelector: jest.fn(sel => {
                if (sel === instance.selectors.ADDSCOPE) {
                    return {value: 'dc'};
                }
                if (sel === instance.selectors.DELETEADDITIONAL) {
                    return wrongScopeAdd;
                }
                return null;
            }),
        };
        const matchingScopeCard = {
            querySelector: jest.fn(sel => {
                if (sel === instance.selectors.ADDSCOPE) {
                    return {value: 'lom'};
                }
                if (sel === instance.selectors.DELETEADDITIONAL) {
                    return matchingScopeAdd;
                }
                return null;
            }),
        };
        const deletedPropertyButton = {
            closest: jest.fn(() => matchingScopeCard),
        };
        instance.getElements.mockImplementation(sel => {
            if (sel === instance.selectors.SCOPECARD) {
                return [wrongScopeCard, matchingScopeCard];
            }
            return [];
        });

        instance.queueFocus('delete', 'additional', deletedPropertyButton);
        instance.restorePendingFocus();

        expect(matchingScopeAdd.focus).toHaveBeenCalledWith({focusVisible: true});
        expect(wrongScopeAdd.focus).not.toHaveBeenCalled();
    });

    test('setDetailsState records scope open state', () => {
        const {instance} = makeInstance();
        const detail = {
            id: 'qtype-stack-metadata-scope-1',
            open: false,
        };

        instance.setDetailsState({currentTarget: detail});

        expect(instance.detailsOpen['qtype-stack-metadata-scope-1']).toBe(false);
    });
});

// ── toggleEditing ───────────────────────────────────────────────────────────────

describe('toggleEditing', () => {
    test('enables editing when the switch is checked', async () => {
        const {instance} = makeInstance();
        const event = {
            preventDefault: jest.fn(),
            currentTarget: {checked: true},
        };
        jest.spyOn(instance, 'reloadContainerComponent').mockResolvedValue(undefined);
        await instance.toggleEditing(event);
        expect(instance.isEditing).toBe(true);
        expect(instance.reloadContainerComponent).toHaveBeenCalledWith({state: undefined});
    });

    test('validates and updates in read mode when the switch is unchecked', async () => {
        const {instance} = makeInstance();
        const event = {currentTarget: {checked: false}};
        instance.isEditing = true;
        jest.spyOn(instance, 'validateInputs').mockReturnValue(true);
        jest.spyOn(instance, 'update').mockResolvedValue(true);
        await instance.toggleEditing(event);
        expect(instance.validateInputs).toHaveBeenCalled();
        expect(instance.update).toHaveBeenCalledWith(false);
        expect(instance.isEditing).toBe(false);
        expect(event.currentTarget.checked).toBe(false);
    });

    test('keeps edit mode switched on when validation fails', async () => {
        const {instance} = makeInstance();
        const event = {currentTarget: {checked: false}};
        jest.spyOn(instance, 'validateInputs').mockReturnValue(false);
        jest.spyOn(instance, 'update').mockResolvedValue(true);
        await instance.toggleEditing(event);
        expect(instance.update).not.toHaveBeenCalled();
        expect(event.currentTarget.checked).toBe(true);
    });

    test('keeps edit mode switched on when update fails after validation', async () => {
        const {instance} = makeInstance();
        const event = {currentTarget: {checked: false}};
        instance.isEditing = true;
        jest.spyOn(instance, 'validateInputs').mockReturnValue(true);
        jest.spyOn(instance, 'update').mockResolvedValue(false);
        jest.spyOn(instance, 'reloadContainerComponent').mockResolvedValue(undefined);
        await instance.toggleEditing(event);
        expect(instance.isEditing).toBe(true);
        expect(event.currentTarget.checked).toBe(true);
        expect(instance.reloadContainerComponent).toHaveBeenCalledWith({state: undefined});
    });
});

// ── makeAuthor ────────────────────────────────────────────────────────────────

describe('makeAuthor', () => {
    test('does not dispatch when update() returns false', async () => {
        const {instance, reactive} = makeInstance();
        jest.spyOn(instance, 'update').mockResolvedValue(false);
        await instance.makeAuthor();
        expect(reactive.dispatch).not.toHaveBeenCalled();
    });

    test('dispatches addItem for author/user when update succeeds', async () => {
        const {instance, reactive} = makeInstance();
        jest.spyOn(instance, 'update').mockResolvedValue(true);
        reactive.dispatch.mockResolvedValue(undefined);
        await instance.makeAuthor();
        expect(reactive.dispatch).toHaveBeenCalledWith('addItem', 'author', 'user');
    });
});

// ── updateInputs ───────────────────────────────────────────────────────────────

describe('updateInputs', () => {
    test('notifies validation failure and returns early when JSON is invalid', async () => {
        const {instance, reactive} = makeInstance();
        const jsonEl = {value: '{invalid json'};
        instance.getElement.mockReturnValue(jsonEl);
        metadata.jsonToState.mockImplementation(() => { throw new SyntaxError('Unexpected token'); });
        await instance.updateInputs();
        expect(notifyFieldValidationFailure).toHaveBeenCalledWith(jsonEl, expect.any(String));
        expect(reactive.dispatch).not.toHaveBeenCalled();
    });

    test('clears error, updates element value and dispatches updateFromJson on valid JSON', async () => {
        const {instance, reactive} = makeInstance();
        const jsonEl = {value: '{"language":[{"id":1,"value":"en"}]}'};
        instance.getElement.mockReturnValue(jsonEl);
        const parsed = {language: [{id: 1, value: 'en'}]};
        metadata.jsonToState.mockReturnValue(parsed);
        metadata.jsonStringify.mockReturnValue('prettified');
        reactive.dispatch.mockResolvedValue(undefined);
        await instance.updateInputs();
        expect(notifyFieldValidationFailure).toHaveBeenCalledWith(jsonEl, '');
        expect(jsonEl.value).toBe('prettified');
        expect(reactive.dispatch).toHaveBeenCalledWith('updateFromJson', parsed);
    });
});

// ── revert ─────────────────────────────────────────────────────────────────────

describe('revert', () => {
    test('restores JSON and dispatches updateFromJson when saved value is valid', async () => {
        const {instance, reactive} = makeInstance();
        const jsonEl = {value: ''};
        instance.getElement.mockReturnValue(jsonEl);
        const formInput = {value: '{"language":[]}'};
        mockQuerySelector.mockReturnValue(formInput);
        const parsed = {language: []};
        metadata.jsonToState.mockReturnValue(parsed);
        metadata.jsonStringify.mockReturnValue('pretty');
        reactive.dispatch.mockResolvedValue(undefined);
        await instance.revert();
        expect(jsonEl.value).toBe('pretty');
        expect(reactive.dispatch).toHaveBeenCalledWith('updateFromJson', parsed);
    });

    test('notifies validation failure and restores broken JSON when saved value is invalid', async () => {
        const {instance, reactive} = makeInstance();
        const jsonEl = {value: ''};
        instance.getElement.mockReturnValue(jsonEl);
        mockQuerySelector.mockReturnValue({value: '{broken'});
        const parseError = new SyntaxError('Unexpected token');
        metadata.jsonToState.mockImplementation(() => {
            throw parseError;
        });
        reactive.dispatch.mockResolvedValue(undefined);
        await instance.revert();
        expect(notifyFieldValidationFailure).toHaveBeenCalledWith(jsonEl, parseError.message);
        expect(jsonEl.value).toBe('{broken');
        expect(metadata.lib.brokenMetadata).toBe(parseError.message);
        expect(reactive.dispatch).not.toHaveBeenCalled();
    });
});

// ── reloadContainerComponent ───────────────────────────────────────────────────

describe('reloadContainerComponent', () => {
    // Build a state object; any top-level key can be overridden.
    function makeState(overrides = {}) {
        const items = (arr) => ({forEach: (cb) => arr.forEach(cb)});
        return {
            author: items([{id: 1, firstName: 'Alice', lastName: 'Smith', institution: 'Uni', year: '2025'}]),
            language: items([{id: 1, value: 'en'}]),
            additional: items([]),
            license: {value: 'cc-by'},
            isPartOf: {value: 'course1'},
            freeform: {value: '{}'},
            ...overrides,
        };
    }

    // Convenience: a forEach-able collection from a plain array.
    const rows = (arr) => ({forEach: (cb) => arr.forEach(cb)});

    // Wire up the instance so reloadContainerComponent can run end-to-end.
    function setupForRender(instance) {
        const fakeContainer = {};
        instance.getElement.mockReturnValue(fakeContainer);
        instance.getElements.mockReturnValue([]);
        mockQuerySelector.mockReturnValue({value: '{}'});
        metadata.jsonStringify.mockReturnValue('prettified-json');
        return fakeContainer;
    }

    // Run reloadContainerComponent and capture the data argument passed to renderComponent.
    async function captureData(instance, state) {
        let captured = null;
        instance.renderComponent.mockImplementation(async (_el, _tmpl, data) => { captured = data; });
        await instance.reloadContainerComponent({state});
        return captured;
    }

    // ── existing structural tests ────────────────────────────────────────────────

    test('calls renderComponent with the metadatacontent template', async () => {
        const {instance} = makeInstance();
        const state = makeState();
        metadata.jsonStringify.mockReturnValue('{}');
        const fakeContainer = {};
        mockQuerySelector.mockReturnValue({value: '{}'});
        instance.getElement.mockReturnValue(fakeContainer);
        instance.getElements.mockReturnValue([]);
        await instance.reloadContainerComponent({state});
        expect(instance.renderComponent).toHaveBeenCalledWith(
            fakeContainer,
            'qtype_stack/metadata/metadatacontent',
            expect.any(Object)
        );
    });

    test('throws when the metadata container DOM element is not found', async () => {
        const {instance} = makeInstance();
        const state = makeState();
        metadata.jsonStringify.mockReturnValue('{}');
        instance.getElement.mockReturnValue(null);
        await expect(instance.reloadContainerComponent({state})).rejects.toThrow('Missing metadata container');
    });

    test('registers change listener for edit switch and click listeners for add and delete buttons after render', async () => {
        const {instance} = makeInstance();
        const state = makeState();
        metadata.jsonStringify.mockReturnValue('{}');
        const fakeContainer = {};
        const fakeButton = {};
        const fakeToggle = {};
        mockQuerySelector.mockImplementation(sel => {
            if (sel === instance.selectors.EDITTOGGLE) {
                return fakeToggle;
            }
            return {value: '{}'};
        });
        instance.getElement.mockImplementation(sel => {
            if (sel === instance.selectors.METADATACONTAINER) {
                return fakeContainer;
            }
            return null;
        });
        instance.getElements.mockImplementation(sel => {
            if (sel === instance.selectors.ADDITEM || sel === instance.selectors.DELETEITEM) {
                return [fakeButton];
            }
            return [];
        });
        await instance.reloadContainerComponent({state});
        expect(instance.addEventListener).toHaveBeenCalledWith(fakeToggle, 'change', instance.toggleEditing);
        expect(instance.addEventListener).toHaveBeenCalledWith(fakeButton, 'click', instance.addItem);
        expect(instance.addEventListener).toHaveBeenCalledWith(fakeButton, 'click', instance.deleteItem);
    });

    test('update inputs button queues focus and awaits updateInputs', async () => {
        const {instance} = makeInstance();
        const state = makeState();
        metadata.jsonStringify.mockReturnValue('{}');
        const fakeContainer = {};
        const fakeUpdateInputs = {};
        let resolveUpdateInputs = null;
        jest.spyOn(instance, 'updateInputs').mockImplementation(() => new Promise(resolve => {
            resolveUpdateInputs = resolve;
        }));
        mockQuerySelector.mockReturnValue({value: '{}'});
        instance.getElement.mockImplementation(sel => {
            if (sel === instance.selectors.METADATACONTAINER) {
                return fakeContainer;
            }
            if (sel === instance.selectors.UPDATEINPUTS) {
                return fakeUpdateInputs;
            }
            return null;
        });
        instance.getElements.mockReturnValue([]);

        await instance.reloadContainerComponent({state});

        const updateInputsListener = instance.addEventListener.mock.calls.find(
            call => call[0] === fakeUpdateInputs && call[1] === 'click'
        )[2];
        let completed = false;
        const listenerPromise = updateInputsListener().then(() => {
            completed = true;
        });

        expect(instance.pendingFocus).toEqual({selector: instance.selectors.UPDATEINPUTS});
        expect(instance.updateInputs).toHaveBeenCalled();
        await Promise.resolve();
        expect(completed).toBe(false);
        resolveUpdateInputs();
        await listenerPromise;
        expect(completed).toBe(true);
    });

    test('passes the current edit state to the template data', async () => {
        const {instance} = makeInstance();
        setupForRender(instance);
        let data = await captureData(instance, makeState());
        expect(data.isEditing).toBe(false);
        instance.isEditing = true;
        data = await captureData(instance, makeState());
        expect(data.isEditing).toBe(true);
    });

    // ── data.author ─────────────────────────────────────────────────────────────

    describe('data.author', () => {
        test('maps all four author fields with correct values', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState());
            expect(data.author[0].firstname.element.value).toBe('Alice');
            expect(data.author[0].lastname.element.value).toBe('Smith');
            expect(data.author[0].institution.element.value).toBe('Uni');
            expect(data.author[0].year.element.value).toBe('2025');
        });

        test('lastname is not required; firstname, institution and year are not', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState());
            expect(data.author[0].lastname.required).toBe(false);
            expect(data.author[0].firstname.required).toBe(false);
            expect(data.author[0].institution.required).toBe(false);
            expect(data.author[0].year.required).toBe(false);
        });

        test('element IDs follow the author_* naming convention', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState());
            expect(data.author[0].firstname.element.id).toBe('smdi_1_author_firstName');
            expect(data.author[0].lastname.element.id).toBe('smdi_1_author_lastName');
            expect(data.author[0].institution.element.id).toBe('smdi_1_author_institution');
            expect(data.author[0].year.element.id).toBe('smdi_1_author_year');
        });

        test('multiple authors produce multiple entries', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const state = makeState({
                author: rows([
                    {id: 1, firstName: 'Alice', lastName: 'Smith', institution: 'Uni', year: '2025'},
                    {id: 2, firstName: 'Bob', lastName: 'Jones', institution: 'Uni2', year: '2026'},
                ]),
            });
            const data = await captureData(instance, state);
            expect(data.author).toHaveLength(2);
            expect(data.author[1].firstname.element.value).toBe('Bob');
        });

        test('empty author list produces an empty array', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState({author: rows([])}));
            expect(data.author).toEqual([]);
        });
    });

    // ── data.language ─────────────────────────────────────────────────────────────

    describe('data.language', () => {
        test('empty language list produces an empty array', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState({language: rows([])}));
            expect(data.language).toEqual([]);
        });

        test('two languages produce two entries with correct ids and values', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const state = makeState({language: rows([{id: 1, value: 'en'}, {id: 2, value: 'fr'}])});
            const data = await captureData(instance, state);
            expect(data.language).toHaveLength(2);
            expect(data.language[0].id).toBe(1);
            expect(data.language[0].lang.element.value).toBe('en');
            expect(data.language[1].id).toBe(2);
            expect(data.language[1].lang.element.value).toBe('fr');
        });

        test('language element IDs embed the language id', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState({language: rows([{id: 3, value: 'de'}])}));
            expect(data.language[0].lang.element.id).toBe('smdi_3_language_value');
        });
    });

    // ── data.license ──────────────────────────────────────────────────────────────

    describe('data.license', () => {
        test('matching license option is marked as selected', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState({license: {value: 'cc-by'}}));
            const selected = data.license.element.options.find(o => o.selected);
            expect(selected).toBeDefined();
            expect(selected.value).toBe('cc-by');
        });

        test('original metadata.lib.licenses array is not mutated', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            await captureData(instance, makeState({license: {value: 'cc-by'}}));
            expect(metadata.lib.licenses.some(o => o.selected)).toBe(false);
        });

        test('unknown license value is appended as a new selected option', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState({license: {value: 'custom-license'}}));
            const added = data.license.element.options.find(o => o.value === 'custom-license');
            expect(added).toBeDefined();
            expect(added.selected).toBe(true);
        });

        test('autocomplete properties are set on the license element', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState());
            expect(data.license.element.tags).toBe('[]');
            expect(data.license.element.ajax).toBe('');
            expect(data.license.element.placeholder).toBe(metadata.lib.placeholder);
            expect(data.license.element.noselectionstring).toBe('');
            expect(data.license.element.showsuggestions).toBe('true');
            expect(data.license.element.casesensitive).toBe('false');
        });
    });

    // ── data.isPartOf ─────────────────────────────────────────────────────────────

    describe('data.isPartOf', () => {
        test('value is passed through and the field is not required', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState({isPartOf: {value: 'quiz42'}}));
            expect(data.isPartOf.element.value).toBe('quiz42');
            expect(data.isPartOf.required).toBe(false);
        });
    });

    // ── data.freeform ─────────────────────────────────────────────────────────────

    describe('data.freeform', () => {
        test('non-empty freeform value is passed through unchanged', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const state = makeState({freeform: {value: '{"times":[1,2,3]}'}});
            const data = await captureData(instance, state);
            expect(data.freeform.element.value).toBe('{"times":[1,2,3]}');
        });

        test('empty string freeform value falls back to empty object literal', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState({freeform: {value: ''}}));
            expect(data.freeform.element.value).toBe('{}');
        });

        test('falsy freeform value (null) falls back to empty object literal', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState({freeform: {value: null}}));
            expect(data.freeform.element.value).toBe('{}');
        });

        test('freeform field is not required', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState());
            expect(data.freeform.required).toBe(false);
        });
    });

    // ── data.scope (additional metadata) ──────────────────────────────────────────

    describe('data.scope (additional metadata)', () => {
        test('no additional items produces an empty scope array', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState({additional: rows([])}));
            expect(data.scope).toEqual([]);
        });

        test('single item creates one scope entry with one property', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const state = makeState({
                additional: rows([
                    {id: 1, scope: 'dc', property: 'subject', qualifier: 'q1', value: 'math'},
                ]),
            });
            const data = await captureData(instance, state);
            expect(data.scope).toHaveLength(1);
            expect(data.scope[0].name).toBe('dc');
            expect(data.scope[0].properties).toHaveLength(1);
        });

        test('property, qualifier and value elements are mapped from the additional item', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const state = makeState({
                additional: rows([
                    {id: 1, scope: 'dc', property: 'subject', qualifier: 'narrow', value: 'math'},
                ]),
            });
            const data = await captureData(instance, state);
            const prop = data.scope[0].properties[0];
            expect(prop.property.element.value).toBe('subject');
            expect(prop.qualifier.element.value).toBe('narrow');
            expect(prop.value.element.value).toBe('math');
        });

        test('two items with the same scope are grouped into one scope entry', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const state = makeState({
                additional: rows([
                    {id: 1, scope: 'dc', property: 'subject', qualifier: 'q1', value: 'math'},
                    {id: 2, scope: 'dc', property: 'subject', qualifier: 'q2', value: 'physics'},
                ]),
            });
            const data = await captureData(instance, state);
            expect(data.scope).toHaveLength(1);
            expect(data.scope[0].properties).toHaveLength(2);
            expect(data.scope[0].properties[1].qualifier.element.value).toBe('q2');
        });

        test('items with different scopes produce separate scope entries', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const state = makeState({
                additional: rows([
                    {id: 1, scope: 'dc', property: 'subject', qualifier: '', value: 'math'},
                    {id: 2, scope: 'lom', property: 'keyword', qualifier: '', value: 'algebra'},
                ]),
            });
            const data = await captureData(instance, state);
            expect(data.scope).toHaveLength(2);
            expect(data.scope.map(s => s.name)).toEqual(expect.arrayContaining(['dc', 'lom']));
        });

        test('three items across two scopes: each scope groups its own correctly', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const state = makeState({
                additional: rows([
                    {id: 1, scope: 'dc', property: 'subject', qualifier: 'q1', value: 'math'},
                    {id: 2, scope: 'lom', property: 'keyword', qualifier: 'q2', value: 'algebra'},
                    {id: 3, scope: 'dc', property: 'title', qualifier: 'q3', value: 'calculus'},
                ]),
            });
            const data = await captureData(instance, state);
            const dc = data.scope.find(s => s.name === 'dc');
            const lom = data.scope.find(s => s.name === 'lom');
            expect(dc.properties).toHaveLength(2);
            expect(lom.properties).toHaveLength(1);
            expect(lom.properties[0].value.element.value).toBe('algebra');
        });

        test('firstProp is the id of the first item in the scope', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const state = makeState({
                additional: rows([
                    {id: 5, scope: 'dc', property: 'subject', qualifier: '', value: 'math'},
                    {id: 6, scope: 'dc', property: 'subject', qualifier: '', value: 'physics'},
                ]),
            });
            const data = await captureData(instance, state);
            expect(data.scope[0].firstProp).toBe(5);
        });

        test('scope input element has the scope name as its value and is required', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const state = makeState({
                additional: rows([
                    {id: 3, scope: 'lom', property: 'keyword', qualifier: '', value: 'algebra'},
                ]),
            });
            const data = await captureData(instance, state);
            expect(data.scope[0].input.element.value).toBe('lom');
            expect(data.scope[0].input.required).toBe(true);
        });

        test('additional element IDs embed the item id', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const state = makeState({
                additional: rows([
                    {id: 7, scope: 'dc', property: 'subject', qualifier: 'q', value: 'v'},
                ]),
            });
            const data = await captureData(instance, state);
            const prop = data.scope[0].properties[0];
            expect(prop.property.element.id).toBe('smdi_7_additional_property');
            expect(prop.qualifier.element.id).toBe('smdi_7_additional_qualifier');
            expect(prop.value.element.id).toBe('smdi_7_additional_value');
        });
    });

    // ── data.json ─────────────────────────────────────────────────────────────────

    describe('data.json', () => {
        test('calls jsonStringify with the state and spacing=4', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const state = makeState();
            await captureData(instance, state);
            expect(metadata.jsonStringify).toHaveBeenCalledWith(state, 4);
        });

        test('jsonStringify return value appears in data.json.element.value', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            metadata.jsonStringify.mockReturnValue('{"pretty":true}');
            const data = await captureData(instance, makeState());
            expect(data.json.element.value).toBe('{"pretty":true}');
        });

        test('fixed structure fields are always set correctly', async () => {
            const {instance} = makeInstance();
            setupForRender(instance);
            const data = await captureData(instance, makeState());
            expect(data.json.required).toBe(true);
            expect(data.json.element.attributes).toBe('rows="10"');
            expect(data.json.element.id).toBe('id_metadata_json');
            expect(data.json.element.name).toBe('metadata_json');
            expect(data.json.element.wrapperid).toBe('fitem_metadata_json');
        });
    });

    // ── brokenMetadata branch ─────────────────────────────────────────────────────

    describe('brokenMetadata branch', () => {
        test('loads the FORMJSON value into the JSONINPUT element and shows the error', async () => {
            const {instance} = makeInstance();
            metadata.lib.brokenMetadata = 'JSON parse error';
            metadata.jsonStringify.mockReturnValue('{}');
            const fakeContainer = {};
            const jsonInputEl = {value: ''};
            instance.renderComponent.mockResolvedValue(undefined);
            instance.getElement.mockImplementation(sel => {
                if (sel === instance.selectors.JSONINPUT) {
                    return jsonInputEl;
                }
                return fakeContainer;
            });
            instance.getElements.mockReturnValue([]);
            mockQuerySelector.mockReturnValue({value: '{"saved":true}'});
            await instance.reloadContainerComponent({state: makeState()});
            expect(jsonInputEl.value).toBe('{"saved":true}');
            expect(notifyFieldValidationFailure).toHaveBeenCalledWith(jsonInputEl, 'JSON parse error');
        });

        test('deletes brokenMetadata from metadata.lib after displaying the error', async () => {
            const {instance} = makeInstance();
            metadata.lib.brokenMetadata = 'JSON parse error';
            metadata.jsonStringify.mockReturnValue('{}');
            const fakeContainer = {};
            instance.getElement.mockReturnValue(fakeContainer);
            instance.getElements.mockReturnValue([]);
            mockQuerySelector.mockReturnValue({value: '{}'});
            await instance.reloadContainerComponent({state: makeState()});
            expect(metadata.lib.brokenMetadata).toBeUndefined();
        });
    });
});
