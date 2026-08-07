export default class Modal {
    constructor(..._args) {}
    static create = jest.fn().mockResolvedValue(null);
}

// Prototype methods so all instances share the same spy target.
Modal.prototype.hide = jest.fn();
Modal.prototype.show = jest.fn();
