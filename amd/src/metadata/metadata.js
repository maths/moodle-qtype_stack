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
 * Metadata entry reactive component
 *
 * @module     qtype_stack/metadata
 * @copyright  2025 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

import {Reactive} from 'core/reactive';
import {mutations} from 'qtype_stack/metadata/mutations';
import {eventTypes, notifyQtypeStackStateUpdated} from 'qtype_stack/metadata/events';

class StackMetadata extends Reactive {
    // Default and config data passed through from Moodle.
    lib = {
        languages: ['en'],
        user: {
            firstname: '',
            lastname: '',
            institution: '',
            year: ''
        },
        licenses: [{value: 'unknown', text: 'unknown'}],
        placeholder: ''
    };

    /**
     * Load initial value of state from value on form.
     */
    loadState() {
        let metadata = document.querySelector('input[name="metadata"]');
        const metadataJSON = metadata.value ?? null;
        try {
            this.lib = JSON.parse(metadata.dataset.lib);
            this.lib.user.year = new Date().getFullYear();
            // Weed out duplicates and falsy values.
            let languages = new Set(this.lib.languages);
            this.lib.languages = languages.difference(new Set([null, undefined, ""]));
            this.lib.languages = Array.from(this.lib.languages);
        } catch (e) {
            // Lib will be set to defaults.
        }
        try {
            metadata = this.jsonToState(metadataJSON);
        } catch (e) {
            // If the saved data is broken, show empty inputs and save error message for display in modal.
            this.lib.brokenMetadata = e.message;
            metadata = this.jsonToState('{}');
        }
        metadata.metadataTicker = {value: 1};
        this.setInitialState(metadata);
    }

    /**
     * Replacer function for JSON stringify of state.
     * Removed unwanted properties and converts some objects to plain values.
     *
     * @param {*} key
     * @param {*} value
     * @returns
     */
    replacer(key, value) {
        const languages = [];
        const additional = {};
        switch(key) {
            case 'metadataTicker':
                return undefined;
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
            case 'additional':
                for (const item of value) {
                    if (item.scope in additional === false) {
                        additional[item.scope] = {};
                    }
                    if (item.property in additional[item.scope] === false) {
                        additional[item.scope][item.property] = {};
                    }
                    if (item.qualifier === '') {
                        additional[item.scope][item.property] = item.value;
                    } else {
                        additional[item.scope][item.property][item.qualifier] = item.value;
                    }
                }
                return additional;
            default:
                return value;
        }

    }

    /**
     * Reviver function for JSON parsing to feed into state.
     * Adds id values and converts strings to obj.value.
     *
     * @param {*} key
     * @param {*} value
     * @returns
     */
    reviver(key, value) {
        const holder = [];
        let id = 1;
        switch(key) {
            case 'contributor':
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
     * Convert JSON to state format ready for updateFromJson mutation.
     * Strips out extraneous fields; adds in missing fields with blank values.
     *
     * @param {*} data
     * @returns
     */
    jsonToState(data) {
        data = JSON.parse(data, this.reviver);
        const fields = ['creator', 'contributor', 'language', 'license', 'isPartOf', 'additional'];
        data = this.stripFields(data, fields);
        const creatorFields = ['firstName', 'lastName', 'institution', 'year'];
        const contribFields = ['id', 'firstName', 'lastName', 'institution', 'year'];
        const standardFields = ['id', 'value'];

        data.creator = this.tidyObject(data.creator, creatorFields);
        data.contributor = (Array.isArray(data.contributor)) ? data.contributor : [];
        const contribHolder = [];
        for (let contrib of data.contributor) {
            contrib = this.tidyObject(contrib, contribFields);
            contribHolder.push(contrib);
        }
        data.contributor = contribHolder;
        data.language = (Array.isArray(data.language)) ? data.language : [];
        const langHolder = [];
        for (let lang of data.language) {
            lang = this.tidyObject(lang, standardFields);
            langHolder.push(lang);
        }
        data.language = langHolder;
        data.isPartOf = this.tidyObject(data.isPartOf, standardFields);
        data.license = this.tidyObject(data.license, standardFields);
        const addHolder = [];
        let addId = 1;
        for (const addScope in data.additional) {
            for (const addProperty in data.additional[addScope]) {
                if (typeof data.additional[addScope][addProperty] === 'object') {
                    for (const addQualifier in data.additional[addScope][addProperty]) {
                        const add = {
                            id: addId,
                            scope: addScope,
                            property: addProperty,
                            qualifier: addQualifier,
                            value: data.additional[addScope][addProperty][addQualifier]
                        };
                        addHolder.push(add);
                        addId++;
                    }
                } else {
                    const add = {
                        id: addId,
                        scope: addScope,
                        property: addProperty,
                        qualifier: '',
                        value: data.additional[addScope][addProperty]
                    };
                    addHolder.push(add);
                    addId++;
                }
            }
        }
        data.additional = addHolder;

        return data;
    }

    /**
     * Remove any properties from an object that are not in a supplied array of property names.
     *
     * @param {object} obj
     * @param {array} fields
     * @returns {object}
     */
    stripFields(obj, fields) {
        const result = {};
        for (const suppliedField in obj) {
            if (fields.includes(suppliedField)) {
                result[suppliedField] = obj[suppliedField];
            }
        }
        return result;
    }

    /**
     * Add any missing properties to an object from a supplied array of field names and set to ''.
     *
     * @param {object} obj
     * @param {array} fields
     * @returns
     */
    addFields(obj, fields) {
        for (const field of fields) {
            if (!Object.hasOwn(obj, field)) {
                obj[field] = '';
            } else {
                obj[field] = String(obj[field]);
            }
        }
        return obj;
    }

    /**
     * Set properties of an object to those from a supplied array of field names.
     *
     * @param {object} obj
     * @param {array} fields
     * @returns
     */
    tidyObject(obj, fields) {
        obj = (obj && typeof obj === 'object') ? obj : {};
        obj = this.stripFields(obj, fields);
        obj = this.addFields(obj, fields);
        return obj;
    }
}

/**
 * The metadata state instance.
 */
export const metadata = new StackMetadata({
    name: 'qtype_stack_metadata',
    eventName: eventTypes.qtypeStackStateUpdated,
    eventDispatch: notifyQtypeStackStateUpdated,
    mutations,
});


