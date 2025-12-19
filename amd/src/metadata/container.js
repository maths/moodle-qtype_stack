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
            ADDITEM: `[name="smd_add"]`,
            DELETEITEM: `[name="smd_delete"]`,
            MAKECONTRIBUTOR: `#stack-metadata-make-contributor`,
            MAKECREATOR: `#stack-metadata-make-creator`,
            REVERT: `#stack-metadata-revert`,
            FORMJSON: 'input[name="metadata"]',
            JSONINPUT: '#id_metadata_json',
            REQUIREDINPUTS: '#qtype-stack-metadata-content input[aria-required="true"]',
            ALLINPUTS: '#qtype-stack-metadata-content [id^="smdi"]',
        };
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
            }
        };
    }

    async reloadContainerComponent({state}) {
        // Mustache data is not fully compatible with state object so we need to convert it
        // into a plain object.
        const data = {
            creator: {},
            contributor: [],
            language: [],
            license: this.createDataElement(true, 0, 'license_value', state.license.value),
            isPartOf: this.createDataElement(false, 0, 'isPartOf_value', state.isPartOf.value),
            scope: [],
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

        state.contributor.forEach(contributor => {
             const element = {
                firstname: this.createDataElement(false, contributor.id, 'contributor_firstName', contributor.firstName),
                lastname: this.createDataElement(true, contributor.id, 'contributor_lastName', contributor.lastName),
                institution: this.createDataElement(false, contributor.id, 'contributor_institution', contributor.institution),
                year: this.createDataElement(false, contributor.id, 'contributor_year', contributor.year),
                id: contributor.id,
            };
            data.contributor.push({...element});
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
            const current = {
                name: scope,
                firstProp: scopeHolder[scope][0].id,
                properties: scopeHolder[scope],
                input: this.createDataElement(true, scopeHolder[scope][0].id, 'additional_scope', scope)
            };
            data.scope.push(current);
        }

        data.creator = {
            firstname: this.createDataElement(false, 0, 'creator_firstName', state.creator.firstName),
            lastname: this.createDataElement(true, 0, 'creator_lastName', state.creator.lastName),
            institution: this.createDataElement(false, 0, 'creator_institution', state.creator.institution),
            year: this.createDataElement(false, 0, 'creator_year', state.creator.year),
        };

        data.json = {
            required: true,
            element: {
                value: JSON.stringify(state, metadata.replacer, 4),
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
        this.addEventListener(
            this.getElement(this.selectors.UPDATEJSON),
            'click',
            this.update
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
            this.updateInputs
        );
        this.addEventListener(
            this.getElement(this.selectors.MAKECREATOR),
            'click',
            this.makeCreator
        );
        this.addEventListener(
            this.getElement(this.selectors.MAKECONTRIBUTOR),
            'click',
            this.makeContributor
        );
        this.addEventListener(
            this.getElement(this.selectors.REVERT),
            'click',
            this.revert
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
    }

    /**
     * Updates state based on contents of inputs.
     *
     * @param {bool} mustValidate Do we want validation to occur?
     * We check when explicitly asked for and when attempting to close the modal other than by cancel.
     * We don't check when e.g. adding a contributor. This means state can be invalid but we only
     * update the edit form entry after successful validation on modal close.
     * @returns {bool} Returns false on validation error.
     */
    async update(mustValidate = true) {
        if (mustValidate) {
            // TO-DO Do we need other validation and/or different required fields.
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
            if (isError) {
                return false;
            }
        }
        // Elements have ids in form smdi-id-category-field e.g. smdi-1-contributor-year.
        // id is category entry id in state. 0 is used for single elements e.g. license.
        // Multi-elements begin counting from 1.
        let inputElements = this.getElements(this.selectors.ALLINPUTS);
        inputElements = Array.from(inputElements).map((el) => [el.id, el.value]);
        await this.reactive.dispatch('updateAll', inputElements);
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
            const parts = event.target.id.split('_');
            this.reactive.dispatch('addItem', parts[1], parts[2]);
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
            const parts = event.target.id.split('_');
            this.reactive.dispatch('deleteRow', parts[1], parts[2]);
        }
    }

    /**
     * Update state from the currently entered JSON if JSON is valid.
     */
    updateInputs() {
        const jsonElement = this.getElement(this.selectors.JSONINPUT);
        let data = null;
        try {
            data = metadata.jsonToState(jsonElement.value);
            notifyFieldValidationFailure(jsonElement, '');
        } catch (e) {
            notifyFieldValidationFailure(jsonElement, e.message);
            return;
        }
        jsonElement.value = JSON.stringify(data, metadata.replacer, 4);
        this.reactive.dispatch('updateFromJson', data);
    }

    /**
     * Add the current user as a contributor.
     */
    async makeContributor() {
        const result = await this.update(false);
        if (result) {
            this.reactive.dispatch('addItem', 'contributor', 'user');
        }
    }

    /**
     * Make current user the creator.
     */
    makeCreator() {
        this.getElement('#smdi_0_creator_firstName').value = metadata.lib.user.firstname;
        this.getElement('#smdi_0_creator_lastName').value = metadata.lib.user.lastname;
        this.getElement('#smdi_0_creator_institution').value = metadata.lib.user.institution;
        this.getElement('#smdi_0_creator_year').value = new Date().getFullYear();
    }

    /**
     * Return JSON to the current version on the edit form. This will be either the saved
     * version from the question or the update from a previous close and validate of the metadata modal.
     * If the JSON is valid, update the state so the inputs match. If invalid, setup as on initial failure
     * in metadata.js.
     */
    revert() {
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
            this.reactive.dispatch('updateFromJson', metadata.jsonToState('{}'));
            return;
        }
        jsonElement.value = JSON.stringify(previousdata, metadata.replacer, 4);
        this.reactive.dispatch('updateFromJson', previousdata);
    }
}