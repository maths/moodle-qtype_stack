const {MetadataModal, setup} = require('../../amd/src/metadata/metadatamodal.js');
const Modal = require('core/modal').default;
const {metadata} = require('qtype_stack/metadata/metadata');

// ── DOM mock ───────────────────────────────────────────────────────────────────
// metadatamodal.js accesses the DOM only inside function calls (not at module
// load time), so a global mock set up before the test run is sufficient.

let mockElements = {};
const mockQuerySelector = jest.fn();

beforeAll(() => {
    global.document = {querySelector: mockQuerySelector};
});

beforeEach(() => {
    // resetAllMocks clears call history AND return-value implementations.
    jest.resetAllMocks();
    mockElements = {};
    // Re-apply implementation after reset.
    mockQuerySelector.mockImplementation(sel => mockElements[sel] ?? null);
});

// ── cancel ─────────────────────────────────────────────────────────────────────

describe('cancel', () => {
    test('reverts metadata before delegating to super.hide()', () => {
        const modal = new MetadataModal();
        modal.cancel();
        expect(metadata.container.revert).toHaveBeenCalledTimes(1);
        expect(Modal.prototype.hide).toHaveBeenCalledTimes(1);
        expect(metadata.container.revert.mock.invocationCallOrder[0]).toBeLessThan(
            Modal.prototype.hide.mock.invocationCallOrder[0]
        );
    });
});

// ── hide ───────────────────────────────────────────────────────────────────────

describe('hide', () => {
    test('does not close the modal when update() resolves falsy', async () => {
        metadata.container.update.mockResolvedValue(false);
        const modal = new MetadataModal();
        await modal.hide();
        expect(Modal.prototype.hide).not.toHaveBeenCalled();
    });

    test('closes the modal when update() resolves truthy', async () => {
        metadata.container.update.mockResolvedValue(true);
        metadata.jsonStringify.mockReturnValue('{"same":true}');
        mockElements['input[name="metadata"]'] = {value: '{"same":true}'};
        const modal = new MetadataModal();
        await modal.hide();
        expect(Modal.prototype.hide).toHaveBeenCalledTimes(1);
    });

    test('does not overwrite the hidden field when the JSON value is unchanged', async () => {
        metadata.container.update.mockResolvedValue(true);
        metadata.jsonStringify.mockReturnValue('{"same":true}');
        mockElements['input[name="metadata"]'] = {value: '{"same":true}'};
        const modal = new MetadataModal();
        await modal.hide();
        // The write path calls querySelector a second time; only one call means no write occurred.
        const inputCalls = mockQuerySelector.mock.calls.filter(
            ([sel]) => sel === 'input[name="metadata"]'
        );
        expect(inputCalls).toHaveLength(1);
    });

    test('updates the hidden metadata field when the JSON value changes', async () => {
        metadata.container.update.mockResolvedValue(true);
        metadata.jsonStringify.mockReturnValue('{"new":true}');
        const metaInput = {value: '{"old":true}'};
        mockElements['input[name="metadata"]'] = metaInput;
        const modal = new MetadataModal();
        await modal.hide();
        expect(metaInput.value).toBe('{"new":true}');
        expect(Modal.prototype.hide).toHaveBeenCalledTimes(1);
    });

    test('updates the change-indicator textContent when the element exists', async () => {
        metadata.container.update.mockResolvedValue(true);
        metadata.jsonStringify.mockReturnValue('{"new":true}');
        mockElements['input[name="metadata"]'] = {value: '{"old":true}'};
        const textEl = {textContent: ''};
        mockElements['[data-name="metadata_text"]'] = textEl;
        mockElements['#id_stack_metadata'] = {
            getAttribute: jest.fn().mockReturnValue('Form has been changed'),
        };
        const modal = new MetadataModal();
        await modal.hide();
        expect(textEl.textContent).toBe('Form has been changed');
    });

    test('still closes and does not throw when change-indicator element is absent (Moodle 4.2)', async () => {
        metadata.container.update.mockResolvedValue(true);
        metadata.jsonStringify.mockReturnValue('{"new":true}');
        mockElements['input[name="metadata"]'] = {value: '{"old":true}'};
        // No metadata_text element → querySelector returns null → caught by try/catch in hide().
        const modal = new MetadataModal();
        await expect(modal.hide()).resolves.toBeUndefined();
        expect(Modal.prototype.hide).toHaveBeenCalledTimes(1);
    });
});

// ── setup ──────────────────────────────────────────────────────────────────────

describe('setup', () => {
    test('calls metadata.loadState()', () => {
        setup();
        expect(metadata.loadState).toHaveBeenCalledTimes(1);
    });

    test('adds a click listener to the modal button when it exists', () => {
        const addListener = jest.fn();
        mockElements['#id_metadatamodal'] = {addEventListener: addListener};
        setup();
        expect(addListener).toHaveBeenCalledWith('click', expect.any(Function));
    });

    test('does not throw when the modal button element is absent', () => {
        // querySelector returns null → optional chaining skips addEventListener.
        expect(() => setup()).not.toThrow();
    });
});
