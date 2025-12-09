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
        };
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

    getWatchers() {
        return [
            {watch: `state:updated`, handler: this.reloadContainerComponent},
        ];
    }

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
                input: this.createDataElement(true, scope, 'additional_scope', scope)
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
                value: JSON.stringify(state, this.replacer, 4),
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

        await this.renderComponent(metadataContainer, 'qtype_stack/metadatacontent', data);
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
        this.addEventListener(
            this.getElement(this.selectors.UPDATEINPUTS),
            'click',
            this.updateInputs
        );
    }

    replacer(key, value) {
        const languages = [];
        switch(key) {
            case 'id':
                return undefined;
            case 'language':
                for (const lang of value) {
                    languages.push(lang.value);
                }
                return languages;
            case 'license':
            case 'isPartOf':
                return value.value;
            default:
                return value;
        }

    }

    reviver(key, value) {
        const holder = [];
        let id = 1;
        switch(key) {
            case 'contributor':
            case 'additional':
                for (const current of value) {
                    current.id = id;
                    holder.push(current);
                    id++;
                }
                return holder;
            case 'language':
                for (const lang of value) {
                    holder.push({id: id, value: lang});
                    id++;
                }
                return holder;
            case 'license':
            case 'isPartOf':
                return {value: value};
            default:
                return value;
        }

    }

    /**
     * Our submit handler.
     *
     */
    update() {
        const requiredElements = this.getElements('#qtype-stack-metadata-content input[aria-required="true"]');
        let isError = false;
        for (const element of requiredElements) {
            if (element.value === '') {
                notifyFieldValidationFailure(element, 'Required');
                isError = true;
            } else if (element.classList.contains('is-invalid')) {
                notifyFieldValidationFailure(element, '');
            }
        }
        if (isError) {
            return;
        }
        let inputElements = this.getElements('#qtype-stack-metadata-content input[id^="smdi"]');
        inputElements = Array.from(inputElements).map((el) => [el.id, el.value]);
        this.reactive.dispatch('updateAll', inputElements);
    }

    addItem(event) {
        const parts = event.target.id.split('_');
        this.reactive.dispatch('addItem', parts[1], parts[2]);
    }

    updateInputs() {
        const data = JSON.parse(this.getElement('#id_metadata_json').value, this.reviver);
        this.reactive.dispatch('updateFromJson', data);
    }
}