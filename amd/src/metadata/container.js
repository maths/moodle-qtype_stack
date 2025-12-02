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
        this.addEventListener(
            this.getElement(this.selectors.SUBMIT),
            'click',
            this.update
        );
    }

    getWatchers() {
        return [
            {watch: `contributor:updated`, handler: this.reloadContainerComponent},
        ];
    }

    async reloadContainerComponent({state}) {
        console.log('Refresh');
        // Mustache data is not fully compatible with state object so we need to convert it
        // into a plain object.
        const element = {
            hiddenlabel: "Course full name",
            required: true,
            element: {
                wrapperid: "fitem_id_fullname",
                id: "id_fullname",
                name: "fullname"
            }
        };

        const data = {
            creator: {},
            contributor: [],
            language: [],
            license: state.license,
            isPartOf: state.isPartOf,
            additional: [],
            element: []
        };
        state.contributor.forEach(contributor => {
            const element = {
                firstname: {
                    required: false,
                    element: {
                        hiddenlabel: "Contributor first name",
                        value: contributor.firstName,
                        wrapperid: "fitem_" + contributor.id + "_confirstname",
                        id: 'id_' + contributor.id + "_confirstname",
                        name: contributor.id + "_confirstname",
                    }
                },
                lastname: {
                    required: true,
                    element: {
                        hiddenlabel: "Contributor last name",
                        value: contributor.lastName,
                        wrapperid: "fitem_" + contributor.id + "_conlastname",
                        id: 'id_' + contributor.id + "_conlastname",
                        name: contributor.id + "_conlastname",
                    }
                },
                institution: {
                    required: false,
                    element: {
                        hiddenlabel: "Contributor institution",
                        value: contributor.institution,
                        wrapperid: "fitem_" + contributor.id + "_coninstitution",
                        id: 'id_' + contributor.id + "_coninstitution",
                        name: contributor.id + "_coninstitution",
                    }
                },
                year: {
                    required: false,
                    element: {
                        hiddenlabel: "Contributor year",
                        value: contributor.year,
                        wrapperid: "fitem_" + contributor.id + "_conyear",
                        id: 'id_' + contributor.id + "_conyear",
                        name: contributor.id + "_conyear",
                    }
                }
            };
            data.contributor.push({...element});
        });
        state.language.forEach(language => {
            data.language.push({...language});
        });
        state.additional.forEach(additional => {
            data.additional.push({...additional});
        });
        data.element.push(element);
        data.creator = data.contributor[0];
        data.json = {
            required: true,
            element: {
                hiddenlabel: "JSON metadata",
                value: JSON.stringify(state),
                wrapperid: "fitem_metadata_json",
                id: "id_metadata_json",
                name: "metadata_json",
            }
        };

        // To render a child component we need a container.
        const metadataContainer = this.getElement(this.selectors.METADATACONTAINER);
        if (!metadataContainer) {
            throw new Error('Missing metadata container.');
        }

        await this.renderComponent(metadataContainer, 'qtype_stack/metadatacontent', data);
    }

    /**
     * Our submit handler.
     *
     * @param {Event} event the click event
     */
    update(event) {
        // We don't want to submit the form.
        event.preventDefault();
        const elements = this.getElements('#qtype-stack-metadata-content input[aria-required="true"]');
        for (const element of elements) {
            if (element.value === '') {
                notifyFieldValidationFailure(element, 'Required');
            } else if (element.classList.contains('is-invalid')) {
                notifyFieldValidationFailure(element, '');
            }
        }
        // Get the selected person id.
        const first = this.getElement('#id_2_confirstname').value;
        const last = this.getElement('#id_2_conlastname').value;
        this.reactive.dispatch('updateContributor', 2, first, last);
    }
}