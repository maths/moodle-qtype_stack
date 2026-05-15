export class Reactive {}

export class BaseComponent {
    constructor({element, reactive, selectors} = {}) {
        this.element = element ?? null;
        this.reactive = reactive ?? null;
        if (selectors) {
            this.selectors = selectors;
        }
        if (typeof this.create === 'function') {
            this.create();
        }
    }
}

// Prototype stubs — shared across instances; reset with jest.resetAllMocks() in beforeEach.
BaseComponent.prototype.getElement = jest.fn();
BaseComponent.prototype.getElements = jest.fn(() => []);
BaseComponent.prototype.addEventListener = jest.fn();
BaseComponent.prototype.renderComponent = jest.fn().mockResolvedValue(undefined);

