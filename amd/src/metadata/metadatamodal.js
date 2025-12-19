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
 * STACK metadata modal setup
 *
 * @module     qtype_stack/metadata
 * @copyright  2025 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

import Modal from 'core/modal';
import ModalRegistry from 'core/modal_registry';
import ModalFactory from 'core/modal_factory';
import {metadata} from 'qtype_stack/metadata/metadata';

export class MetadataModal extends Modal {
    static TYPE = "qtype_stack/metadatamodal";
    static TEMPLATE = "qtype_stack/metadata/metadatamodal";

    /**
     * Override the default hide function to validate and update metadata JSON.
     * On success, stores new JSON to hidden edit form field and closed modal.
     */
    async hide() {
        const result = await metadata.container.update(true);
        if (result) {
            document.querySelector('input[name="metadata"]').value = JSON.stringify(metadata.state, metadata.replacer);
            super.hide();
        }
    }

    /**
     * Cancel button needs to close the modal without updating form.
     */
    cancel() {
        super.hide();
    }
}

let registered = false;
if (!registered) {
    ModalRegistry.register(MetadataModal.TYPE, MetadataModal, MetadataModal.TEMPLATE);
    registered = true;
}

let modal = null;

// Prepare for modal creation.
export const setup = () => {
   document.querySelector('#id_metadatamodal')?.addEventListener('click', openModal);
   metadata.loadState();
};

// Need to pass appropriate 'this' to cancel function.
function closeModal() {
    modal.cancel.call(modal);
}

/**
 * Open the metadata modal.
 * Only create modal and add listener once.
 */
async function openModal() {
    let addListener = false;
    if (!modal) {
        if (typeof MetadataModal.create === "function") {
            modal = await MetadataModal.create();
        } else {
            // Pre Moodle 4.3 code.
            modal = await ModalFactory.create({
                type: MetadataModal.TYPE,
            });
        }
        addListener = true;
    }
    modal.show();
    if (addListener) {
        document.querySelector('#stackmetadata_cancel').addEventListener('click', closeModal);
    }
}

