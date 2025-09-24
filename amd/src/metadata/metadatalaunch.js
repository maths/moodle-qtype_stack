import ModalFactory from 'core/modal_factory';
import MetadataModal from './metadatamodal';

export const setup = () => {
   document.querySelector('#id_metadatamodal')?.addEventListener('click', openModal);
};

/**
 * Open the metadata modal.
 */
async function openModal() {
    // ...
    const modal = await ModalFactory.create({
        type: MetadataModal.TYPE,
    });

    modal.show();
}