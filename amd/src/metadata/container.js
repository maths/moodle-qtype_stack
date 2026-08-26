// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Main STACK metadata component
 *
 * @module     qtype_stack/metadata
 * @copyright  2025 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

import {BaseComponent} from 'core/reactive';
import {metadata} from 'qtype_stack/metadata/metadata';
import {notifyFieldValidationFailure} from 'core_form/events';

export default class extends BaseComponent {
    create() {
        this.name = 'stack-metadata-container';
        this.selectors = {
            METADATACONTAINER: `[data-for='qtype-stack-metadata']`,
            UPDATEJSON: `#stack-metadata-update`,
            UPDATEINPUTS: `#stack-metadata-update-inputs`,
            EDITTOGGLE: `#stack-metadata-edit-switch`,
            ADDITEM: `[name="smd_add"]`,
            DELETEITEM: `[name="smd_delete"]`,
            MAKEAUTHOR: `#stack-metadata-make-author`,
            REVERT: `#stack-metadata-revert`,
            FORMJSON: 'input[name="metadata"]',
            FORM: '#qtype-stack-metadata-content',
            JSONINPUT: '#id_metadata_json',
            REQUIREDINPUTS: '#qtype-stack-metadata-content input[aria-required="true"]',
            VALIDATIONERRORS: '#qtype-stack-metadata-content .is-invalid',
            ALLINPUTS: '#qtype-stack-metadata-content [id^="smdi"]',
            DETAILS: '#qtype-stack-metadata-content details[id]',
            ADDAUTHOR: '[id^="smdi_"][id$="_author_firstName"]',
            ADDLANGUAGE: '[id^="smdi_"][id$="_language_value"]',
            ADDPROPERTY: '[id^="smdi_"][id$="_additional_property"]',
            ADDSCOPE: '[id^="smdi_"][id$="_additional_scope"]',
            ADDBUTTONS: '[name="smd_add"], #stack-metadata-make-author',
            SCOPECARD: '.smd-scope-card',
            DELETEADDITIONAL: '[id^="smd_property_"][id$="_add"]',
            DELETEAUTHOR: '#smd_author_0_add',
            DELETELANGUAGE: '#smd_language_0_add',
            DELETESCOPE: '#smd_scope_0_add',
        };
        this.isEditing = false;
        this.detailsOpen = {};
        this.pendingFocus = null;
        metadata.container = this;
    }

    /**
     * Static method to create a component instance form the mustache template.
     *
     * @param {string} target the DOM main element or its ID
     * @param {object} selectors optional css selector overrides
     * @return {Component}
     */
    static init(target, selectors) {
        return new this({
            element: document.querySelector(target),
            reactive: metadata,
            selectors,
        });
    }

    /**
     * Initial state ready method.
     *
     * @param {object} state the initial state
     */
    async stateReady(state) {
        await this.reloadContainerComponent({state});
    }

    /**
     * Set to refresh display on any state change.
     *
     * @returns {object} watchers
     */
    getWatchers() {
        return [
            {watch: `state:updated`, handler: this.reloadContainerComponent},
        ];
    }

    /**
     * Converts field information into element suitable for feeding into Mustache templates.
     *
     * @param {bool} required
     * @param {mixed} id to link to state
     * @param {string} tag type of field
     * @param {mixed} value of element
     * @returns {object}
     */
    createDataElement(required, id, tag, value) {
        return {
            required: required,
            element: {
                value: value,
                wrapperid: 'fitem_smdi_' + id + '_' + tag,
                id: 'smdi_' + id + '_' + tag,
                name: 'smdi_' + id + '_' + tag,
                iderror: 'smde_' + id + '_' + tag + '_error'
            }
        };
    }

    async reloadContainerComponent({state}) {
        const details = this.getElements(this.selectors.DETAILS);
        for (const detail of details) {
            this.detailsOpen[detail.id] = detail.open;
        }

        // Mustache data is not fully compatible with state object so we need to convert it
        // into a plain object.
        const data = {
            author: [],
            language: [],
            license: this.createDataElement(true, 0, 'license_value', state.license.value),
            isPartOf: this.createDataElement(false, 0, 'isPartOf_value', state.isPartOf.value),
            scope: [],
            freeform: this.createDataElement(false, 0, 'freeform_value', state.freeform.value || '{}'),
            isEditing: this.isEditing,
            isJsonOpen: this.detailsOpen['qtype-stack-metadata-json-section'] ?? false,
        };

        // Need to copy licenses list as we modify to mark as selected.
        data.license.element.options = JSON.parse(JSON.stringify(metadata.lib.licenses));
        const selectedLicense = state.license.value;
        let selectedOption = data.license.element.options.find((op) => op.value === selectedLicense);
        if (selectedOption) {
            selectedOption.selected = true;
        } else {
            data.license.element.options.push({value: state.license.value, text: state.license.value, selected: true});
        }
        data.license.element.tags = '[]';
        data.license.element.ajax = '';
        data.license.element.placeholder = metadata.lib.placeholder;
        data.license.element.noselectionstring = '';
        data.license.element.showsuggestions = 'true';
        data.license.element.casesensitive = 'false';

        state.language.forEach(language => {
            const element = { id: language.id, lang: this.createDataElement(true, language.id, 'language_value', language.value) };
            data.language.push({...element});
        });

        state.author.forEach(author => {
             const element = {
                firstname: this.createDataElement(false, author.id, 'author_firstName', author.firstName),
                lastname: this.createDataElement(false, author.id, 'author_lastName', author.lastName),
                institution: this.createDataElement(false, author.id, 'author_institution', author.institution),
                year: this.createDataElement(false, author.id, 'author_year', author.year),
                id: author.id,
            };
            data.author.push({...element});
        });

        const scopeHolder = {};
        // Rearrange additional metadata by scope.
        state.additional.forEach(additional => {
            const element = {
                property: this.createDataElement(true, additional.id, 'additional_property', additional.property),
                qualifier: this.createDataElement(false, additional.id, 'additional_qualifier', additional.qualifier),
                value: this.createDataElement(false, additional.id, 'additional_value', additional.value),
                id: additional.id,
            };
            if (!scopeHolder[additional.scope]) {
                scopeHolder[additional.scope] = [];
            }
            scopeHolder[additional.scope].push(element);
        });
        for (const scope in scopeHolder) {
            const scopeId = 'qtype-stack-metadata-scope-' + scopeHolder[scope][0].id;
            const current = {
                id: scopeId,
                name: scope,
                firstProp: scopeHolder[scope][0].id,
                properties: scopeHolder[scope],
                input: this.createDataElement(true, scopeHolder[scope][0].id, 'additional_scope', scope),
                isOpen: this.detailsOpen[scopeId] ?? true,
            };
            data.scope.push(current);
        }

        data.json = {
            required: true,
            element: {
                value: metadata.jsonStringify(state, 4),
                attributes: 'rows="10"',
                wrapperid: 'fitem_metadata_json',
                id: 'id_metadata_json',
                name: 'metadata_json',
            }
        };

        // To render a child component we need a container.
        const metadataContainer = this.getElement(this.selectors.METADATACONTAINER);
        if (!metadataContainer) {
            throw new Error('Missing metadata container.');
        }

        await this.renderComponent(metadataContainer, 'qtype_stack/metadata/metadatacontent', data);

        // Add all the event listeners as all elements have been destroyed and rebuilt.
        const detailSections = this.getElements(this.selectors.DETAILS);
        for (const detailSection of detailSections) {
            this.addEventListener(
                detailSection,
                'toggle',
                this.setDetailsState
            );
        }
        this.addEventListener(
            document.querySelector(this.selectors.EDITTOGGLE),
            'change',
            this.toggleEditing
        );
        this.addEventListener(
            this.getElement(this.selectors.UPDATEJSON),
            'click',
            async() => {
                this.pendingFocus = {selector: this.selectors.UPDATEJSON};
                await this.update();
            }
        );
        const addButtons = this.getElements(this.selectors.ADDITEM);
        for (const addButton of addButtons) {
            this.addEventListener(
                addButton,
                'click',
                this.addItem
            );
        }
        const deleteButtons = this.getElements(this.selectors.DELETEITEM);
        for (const deleteButton of deleteButtons) {
            this.addEventListener(
                deleteButton,
                'click',
                this.deleteItem
            );
        }
        this.addEventListener(
            this.getElement(this.selectors.UPDATEINPUTS),
            'click',
            async() => {
                this.pendingFocus = {selector: this.selectors.UPDATEINPUTS};
                await this.updateInputs();
            }
        );
        this.addEventListener(
            this.getElement(this.selectors.MAKEAUTHOR),
            'click',
            this.makeAuthor
        );
        this.addEventListener(
            this.getElement(this.selectors.REVERT),
            'click',
            async() => {
                this.pendingFocus = {selector: this.selectors.REVERT};
                await this.revert();
            }
        );

        // Deal with case of brkon JSON in saved question. The errormessage is saved on initial setup.
        // We load in the original un-prettified JSON and display error message, giving user chance to edit.
        // After this, though, they'll need to sort it out - if we're back here again then we'll use
        // JSON created from current content of state.
        if (metadata.lib.brokenMetadata) {
            const jsonElement = this.getElement(this.selectors.JSONINPUT);
            jsonElement.value = document.querySelector(this.selectors.FORMJSON).value ?? '';
            notifyFieldValidationFailure(jsonElement, metadata.lib.brokenMetadata);
            delete metadata.lib.brokenMetadata;
        }

        this.restorePendingFocus();
    }

    /**
     * Toggle edit mode. Turning edit mode off validates and updates the state first.
     *
     * @param {Event} event
     */
    async toggleEditing(event) {
        if (event.currentTarget.checked) {
            this.isEditing = true;
            await this.reloadContainerComponent({state: this.reactive.state});
            return;
        }
        if (!this.validateInputs()) {
            event.currentTarget.checked = true;
            return;
        }
        this.isEditing = false;
        const result = await this.update(false);
        if (!result) {
            this.isEditing = true;
            event.currentTarget.checked = true;
            await this.reloadContainerComponent({state: this.reactive.state});
        }
    }

    /**
     * Validate visible metadata inputs without dispatching state changes.
     *
     * @returns {bool} Returns false on validation error.
     */
    validateInputs() {
        const requiredElements = this.getElements(this.selectors.REQUIREDINPUTS);
        let isError = false;
        for (const element of requiredElements) {
            if (element.value === '') {
                isError = true;
                notifyFieldValidationFailure(element, 'Required');
            } else if (element.classList.contains('is-invalid')) {
                // Reset warning as field no longer empty.
                notifyFieldValidationFailure(element, '');
            }
        }
        // Validate freeform JSON if non-empty.
        const freeformElement = this.getElement('#smdi_0_freeform_value');
        if (freeformElement && freeformElement.value.trim() !== '') {
            try {
                JSON.parse(freeformElement.value);
                notifyFieldValidationFailure(freeformElement, '');
            } catch(e) {
                notifyFieldValidationFailure(freeformElement, e.message);
                isError = true;
            }
        }
        return !isError;
    }

    /**
     * Updates state based on contents of inputs.
     *
     * @param {bool} mustValidate Do we want validation to occur?
     * We check when explicitly asked for and when attempting to close the modal other than by cancel.
     * We don't check when e.g. adding an author. This means state can be invalid but we only
     * update the edit form entry after successful validation on modal close.
     * @returns {bool} Returns false on validation error.
     */
    async update(mustValidate = true) {
        if (mustValidate && !this.validateInputs()) {
            return false;
        }
        // Elements have ids in form smdi_id_category_field e.g. smdi_1_author_year.
        // id is category entry id in state. 0 is used for single elements e.g. license.
        // Multi-elements begin counting from 1.
        let inputElements = this.getElements(this.selectors.ALLINPUTS);
        inputElements = Array.from(inputElements).map((el) => [el.id, el.value]);
        try {
            await this.reactive.dispatch('updateAll', inputElements);
        } catch (e) {
            const addIds = e.split(',');
            for (const id of addIds) {
                const element = this.getElement('#qtype-stack-metadata-content [id="smdi_' + id + '_additional_qualifier"]');
                notifyFieldValidationFailure(element, 'Required');
            }
            return false;
        }
        return true;
    }

    /**
     * Add a new row to modal form.
     * We have to update state from the input fields first or any changes will
     * be wiped when we refresh the display to show the new row.
     *
     * @param {*} event
     */
    async addItem(event) {
        const result = await this.update(true);
        if (result) {
            const parts = event.currentTarget.id.split('_');
            this.queueFocus('add', parts[1], event.currentTarget);
            await this.reactive.dispatch('addItem', parts[1], parts[2]);
        }
    }
    /**
     * Delete a row from modal form.
     * We have to update state from the input fields first or any changes will
     * be wiped when we refresh the display toremove the row
     *
     * @param {*} event
     */
    async deleteItem(event) {
        const result = await this.update(false);
        if (result) {
            const parts = event.currentTarget.id.split('_');
            this.queueFocus('delete', parts[1], event.currentTarget);
            await this.reactive.dispatch('deleteRow', parts[1], parts[2]);
        }
    }

    /**
     * Update state from the currently entered JSON if JSON is valid.
     */
    async updateInputs() {
        const jsonElement = this.getElement(this.selectors.JSONINPUT);
        let data = null;
        try {
            data = metadata.jsonToState(jsonElement.value);
            notifyFieldValidationFailure(jsonElement, '');
        } catch (e) {
            notifyFieldValidationFailure(jsonElement, e.message);
            return;
        }
        jsonElement.value = metadata.jsonStringify(data, 4);
        await this.reactive.dispatch('updateFromJson', data);
    }

    /**
     * Add the current user as an author.
     */
    async makeAuthor() {
        const result = await this.update(false);
        if (result) {
            this.queueFocus('add', 'author', this.getElement(this.selectors.MAKEAUTHOR));
            await this.reactive.dispatch('addItem', 'author', 'user');
        }
    }

    /**
     * Return JSON to the current version on the edit form. This will be either the saved
     * version from the question or the update from a previous close and validate of the metadata modal.
     * If the JSON is valid, update the state so the inputs match. If invalid, setup as on initial failure
     * in metadata.js.
     */
    async revert() {
        const jsonElement = this.getElement(this.selectors.JSONINPUT);
        let previousdataJSON = document.querySelector(this.selectors.FORMJSON).value ?? null;
        let previousdata = null;
        try {
            previousdata = metadata.jsonToState(previousdataJSON);
            notifyFieldValidationFailure(jsonElement, '');
        } catch (e) {
            notifyFieldValidationFailure(jsonElement, e.message);
            jsonElement.value = previousdataJSON;
            metadata.lib.brokenMetadata = e.message;
            return;
        }
        jsonElement.value = metadata.jsonStringify(previousdata, 4);
        await this.reactive.dispatch('updateFromJson', previousdata);
    }

    /**
     * Update stored state when a collapsible section is toggled.
     *
     * @param {Event} event
     */
    setDetailsState(event) {
        const detail = event.currentTarget;
        this.detailsOpen[detail.id] = detail.open;
    }

    /**
     * Remember where focus should move after adding or deleting an item.
     *
     * @param {string} action
     * @param {string} category
     * @param {HTMLElement} target
     */
    queueFocus(action, category, target) {
        this.pendingFocus = null;
        const selectorName = (action + category).toUpperCase();
        const selector = this.selectors[selectorName];
        if (!selector) {
            return;
        }

        switch (action) {
            case 'add':
                this.pendingFocus = {selector, selection: 'last'};
                break;
            case 'delete':
                this.pendingFocus = {selector, fallbackSelector: selectorName === 'DELETEADDITIONAL' ?
                    this.selectors.DELETESCOPE : null, selection: 'scope'};
                break;
        }
        if (selectorName === 'ADDPROPERTY' || selectorName === 'DELETEADDITIONAL') {
            const scopeCard = target?.closest?.(this.selectors.SCOPECARD);
            this.pendingFocus.scopeName = scopeCard?.querySelector?.(this.selectors.ADDSCOPE)?.value ?? null;
        }
    }

    /**
     * Restore focus after reactive re-rendering.
     */
    restorePendingFocus() {
        if (!this.pendingFocus) {
            return;
        }

        const pendingFocus = this.pendingFocus;
        this.pendingFocus = null;
        if (this.getElements(this.selectors.VALIDATIONERRORS).length) {
            return;
        }

        // Defer in the browser to deal with Moodle 4.2 issue.
        if (typeof window !== 'undefined' && window.setTimeout) {
            window.setTimeout(() => {
                this.focusPendingTarget(pendingFocus);
            }, 0);
            return;
        }

        // JEST test ends up here.
        this.focusPendingTarget(pendingFocus);
    }

    /**
     * Find and focus the element requested by a stored focus instruction.
     *
     * @param {object} pendingFocus
     */
    focusPendingTarget(pendingFocus) {
        let focusTarget = null;

        const scopeCard = pendingFocus.scopeName ? this.getScopeCard(pendingFocus.scopeName) : null;
        if (pendingFocus.selection === 'last') {
            const elements = Array.from(
                scopeCard?.querySelectorAll?.(pendingFocus.selector) || this.getElements(pendingFocus.selector)
            );
            focusTarget = elements[elements.length - 1] || null;
        } else if (pendingFocus.selection === 'scope') {
            focusTarget = scopeCard?.querySelector?.(pendingFocus.selector) ||
                this.getElement(pendingFocus.fallbackSelector || pendingFocus.selector);
        } else {
            focusTarget = this.getElements(pendingFocus.selector)[0];
        }

        if (focusTarget?.focus) {
            if (!focusTarget.matches?.(this.selectors.ADDBUTTONS)) {
                focusTarget.focus();
            } else {
                try {
                    focusTarget.focus({focusVisible: true});
                } catch {
                    focusTarget.focus();
                }
            }
        }
        if (focusTarget?.select) {
            focusTarget.select();
        }
    }

    /**
     * Get a scope card from its scope name.
     *
     * @param {string} scopeName
     * @returns {HTMLElement|null}
     */
    getScopeCard(scopeName) {
        return Array.from(this.getElements(this.selectors.SCOPECARD)).find((scopeCard) => {
            return scopeCard.querySelector?.(this.selectors.ADDSCOPE)?.value === scopeName;
        }) ?? null;
    }
}
