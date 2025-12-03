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
            SUBMIT: `#stack-metadata-update`,
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
            license: state.license,
            isPartOf: state.isPartOf,
            additional: []
        };
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
        state.language.forEach(language => {
            data.language.push({...language});
        });
        state.additional.forEach(additional => {
            data.additional.push({...additional});
        });
        data.creator = {
            firstname: this.createDataElement(false, 0, 'creator_firstName', state.creator.firstName),
            lastname: this.createDataElement(true, 0, 'creator_lastName', state.creator.lastName),
            institution: this.createDataElement(false, 0, 'creator_institution', state.creator.institution),
            year: this.createDataElement(false, 0, 'creator_year', state.creator.year),
        };
        data.json = {
            required: true,
            element: {
                value: JSON.stringify(state),
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
            this.getElement(this.selectors.SUBMIT),
            'click',
            this.update
        );
    }

    /**
     * Our submit handler.
     *
     * @param {Event} event the click event
     */
    update(event) {
        // We don't want to submit the form.
        event.preventDefault();
        const requiredElements = this.getElements('#qtype-stack-metadata-content input[aria-required="true"]');
        for (const element of requiredElements) {
            if (element.value === '') {
                notifyFieldValidationFailure(element, 'Required');
            } else if (element.classList.contains('is-invalid')) {
                notifyFieldValidationFailure(element, '');
            }
        }
        let inputElements = this.getElements('#qtype-stack-metadata-content input[id^="smdi"]');
        inputElements = Array.from(inputElements).map((el) => [el.id, el.value]);
        this.reactive.dispatch('updateAll', inputElements);
    }
}