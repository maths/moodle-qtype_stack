import Modal from 'core/modal';
import ModalRegistry from 'core/modal_registry';

export default class MetadataModal extends Modal {
    static TYPE = "qtype_stack/metadatamodal";
    static TEMPLATE = "qtype_stack/metadatamodal";
}

let registered = false;
if (!registered) {
    ModalRegistry.register(MetadataModal.TYPE, MetadataModal, MetadataModal.TEMPLATE);
    registered = true;
}