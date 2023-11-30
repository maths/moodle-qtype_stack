/**
 * This is a library for SortableJS functionality used to generate STACK Parsons blocks.
 * 
 * @copyright  2023 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Preprocesses proof steps and user options to a format expected by stack_sortable.
 *
 * @param {Object|string} proofSteps - The proof steps as an object or a JSON string.
 * @param {Object} blockUserOpts - The block user options.
 * @param {Object} sortableUserOpts - The sortable user options.
 * @returns {[Object, Object, Object]} - An array containing preprocessed proof steps, block user options, and sortable user options.
 * @throws {SyntaxError} - If the proofSteps parameter is a string but cannot be parsed into a valid JSON object.
 */
export function preprocess_steps(proofSteps, blockUserOpts, sortableUserOpts) {
    // Check if proofSteps is a string and convert it to an object
    if (typeof proofSteps === "string") {
        proofSteps = Object.fromEntries(new Map(Object.values(JSON.parse(proofSteps))));
    }

    // Check if proofSteps has the expected structure
    if (JSON.stringify(Object.keys(proofSteps)) === JSON.stringify(["steps", "options"])) {
        var userOpts = proofSteps["options"];
        proofSteps = proofSteps["steps"];

        // Process block user options for the 'header' setting
        if (userOpts.header != null) {
            blockUserOpts = { used: { header: userOpts.header[0] }, available: { header: userOpts.header[1] } };
        }

        // Split Sortable options into used and available
        delete userOpts.header;
        sortableUserOpts = { used: userOpts, available: userOpts };
    }

    // Convert proofSteps to an object if it is still a string
    if (typeof proofSteps === "string") {
        proofSteps = Object.fromEntries(new Map(Object.values(JSON.parse(proofSteps))));
    }

    return [proofSteps, blockUserOpts, sortableUserOpts];
}

/**
 * Flips the orientation of specified used and available lists, and the bin (if present) in the UI.
 * The function toggles between 'list-group row' and 'list-group col' classes for the specified elements.
 * The bin element (if present) is expected to have ID 'bin'.
 *
 * @param {string} usedId - The ID of the used list element.
 * @param {string} availableId - The ID of the available list element.
 * @returns {void}
 *
 * @example
 * // HTML structure:
 * // <div id="usedList" class="list-group row">...</div>
 * // <div id="availableList" class="list-group row">...</div>
 * // <div id="bin" class="list-group row">...</div>
 *
 * // JavaScript usage:
 * _flip_orientation('usedList', 'availableList');
 */
function _flip_orientation(usedId, availableId) {
    var usedList = document.getElementById(usedId);
    var availableList = document.getElementById(availableId);
    var bin = document.getElementById('bin');
    var newClass = usedList.className == 'list-group row' ? 'list-group col' : 'list-group row';
    usedList.setAttribute('class', newClass);
    availableList.setAttribute('class', newClass);
    if (bin != null) {
        bin.setAttribute('class', newClass);
    }
}

/**
 * Adds an event listener to a button element with the specified ID to trigger the flipping
 * of orientation between 'list-group row' and 'list-group col' classes for specified UI elements.
 * This event will also change the orientation of the bin element (if present), which is expected 
 * to have ID 'bin'.
 *
 * @param {string} buttonId - The ID of the button element to which the event listener is added.
 * @param {string} usedId - The ID of the used list element.
 * @param {string} availableId - The ID of the available list element.
 * @returns {void}
 *
 * @example
 * // HTML structure:
 * // <button id="toggleButton">Toggle Orientation</button>
 * // <div id="usedList" class="list-group row">...</div>
 * // <div id="availableList" class="list-group row">...</div>
 *
 * // JavaScript usage:
 * add_orientation_listener('toggleButton', 'usedList', 'availableList');
 */
export function add_orientation_listener(buttonId, usedId, availableId) {
    const button = document.getElementById(buttonId);
    button.addEventListener('click', () => _flip_orientation(usedId, availableId));
}

export function get_iframe_height() {
    return document.documentElement.offsetHeight;
}

/**
 * Class for for managing Sortable lists for Parson's proof questions in STACK.
 *
 * @class
 * @param {Object} proofSteps - Object containing proof steps.
 * @param {string} availableId - ID of the available list element.
 * @param {string} usedId - ID of the used list element.
 * @param {string|null} inputId - ID of the input element for storing state (optional).
 * @param {Object|null} options - Custom options for sortable lists (optional).
 * @param {boolean} clone - Flag indicating whether to clone elements during sorting.
 *
 * @property {Object} proofSteps - Object containing proof steps.
 * @property {string} inputId - ID of the input element for storing state (optional).
 * @property {Object} state - Current state of used and available items.
 * @property {Object} userOptions - User-defined options merged with default options.
 * @property {boolean} clone - Flag indicating whether to clone elements during sorting.
 * @property {Object} options - Final options for sortable lists.
 *
 * @method generate_available - Generates the available list based on the current state.
 * @method generate_used - Generates the used list based on the current state.
 * @method add_headers - Adds header elements to the used and available lists.
 * @method update_state - Updates the state based on changes in the used and available lists.
 * @method update_state_dblclick - Updates the state on double-click events in the lists.
 *
 * @example
 * // Creating a StackSortable instance:
 * const sortable = new stack_sortable({
 *   "step1": "Step 1",
 *   "step2": "Step 2",
 *   // ...
 * }, "availableList", "usedList", "stateInput", { used: { animation: 100 }, available: { animation: 100 } }, false);
 *
 * // Generating lists and adding headers:
 * sortable.generate_available();
 * sortable.generate_used();
 * sortable.add_headers({ used: { header: "Used Header" }, available: { header: "Available Header" } });
 *
 * // Updating state on changes:
 * sortable.update_state(newUsedList, newAvailableList);
 *
 * // Updating state on double-click events:
 * sortable.update_state_dblclick(newUsedList, newAvailableList);
 *
 * @exports stack_sortable
 */
export const stack_sortable = class {
    /**
     * Constructor for the StackSortable class.
     *
     * @constructor
     * @param {Object} proofSteps - Object containing proof steps.
     * @param {string} availableId - ID of the available list element.
     * @param {string} usedId - ID of the used list element.
     * @param {string|null} inputId - ID of the input element for storing state (optional).
     * @param {Object|null} options - Custom options for sortable lists (optional).
     * @param {boolean} clone - Flag indicating whether to clone elements during sorting.
     */
    constructor(proofSteps, availableId, usedId, inputId = null, options = null, clone = false) {
        this.proofSteps = proofSteps;
        this.inputId = inputId;
        this.state = this._generate_state(this.proofSteps, this.inputId);
        if (inputId != null) {
            this.input = document.getElementById(this.inputId);
        };
        this.availableId = availableId;
        this.available = document.getElementById(this.availableId);
        this.usedId = usedId;
        this.used = document.getElementById(this.usedId);

        // TODO : additional default options?
        this.defaultOptions = {used: {animation: 50}, available: {animation: 50}};
        if (options == null) {
            this.userOptions = this.defaultOptions;
        } else {
            this.userOptions = {used: Object.assign(this.defaultOptions.used, options.used), 
                available: Object.assign(this.defaultOptions.available, options.available)};
        };
        // define group correctly based on clone
        this.clone = clone
        var group_val = {used: {name: "sortableUsed", pull: true, put: true}};
        group_val.available = (clone === "true") ? 
            {name: "sortableAvailable", pull: "clone", revertClone: true, put: false} : 
            {name: "sortableAvailable", pull: true, put: true};

        // Do not allow a user to replace ghostClass or group.
        this.options = {used: 
            Object.assign(this.userOptions.used, {ghostClass: "list-group-item-info", group: group_val.used}), 
                        available : 
            Object.assign(this.userOptions.available, {ghostClass: "list-group-item-info", group: group_val.available})
        };
    }

    /**
     * Generates the available list based on the current state.
     *
     * @method
     * @returns {void}
     */
    generate_available() {
        for (const key in this.state.available) {
            let li = document.createElement("li");
            li.innerHTML = this.proofSteps[this.state.available[key]];
            li.setAttribute("data-id", this.state.available[key]);
            li.className = "list-group-item";
            this.available.append(li);
        };
    }

    /**
     * Generates the used list based on the current state.
     *
     * @method
     * @returns {void}
     */
    generate_used() {
        for (const key in this.state.used) {
            let li = document.createElement("li");
            li.innerHTML = this.proofSteps[this.state.used[key]];
            li.setAttribute("data-id", this.state.used[key]);
            li.className = "list-group-item";
            this.used.append(li);
        };
    }

    /**
     * Adds header elements to the used and available lists.
     *
     * @method
     * @param {Object} headers - Object containing header text for used and available lists.
     * @returns {void}
     */
    add_headers(headers) {
        this.used.append(this._create_header(headers.used.header, "usedHeader"));
        this.available.append(this._create_header(headers.available.header, "availableHeader"));
    }

    add_item_to_available(innerHTML) {
        let li = document.createElement("li");
        li.innerHTML = innerHTML;
        li.className = "list-group-item";
        this.available.append(li);
    }

    /**
     * Updates the state based on changes in the used and available lists.
     *
     * @method
     * @param {Object} newUsed - Updated used list.
     * @param {Object} newAvailable - Updated available list.
     * @returns {void}
     */
    update_state(newUsed, newAvailable) {
        var newState = {used: newUsed.toArray(), available: newAvailable.toArray()};
        if (this.inputId != null) {
            this.input.value = JSON.stringify(newState);
            this.input.dispatchEvent(new Event('change'));
        };
        this.state = newState;
    }

    /**
     * Updates the state on double-click events in the lists.
     *
     * @method
     * @param {Object} newUsed - Updated used list.
     * @param {Object} newAvailable - Updated available list.
     * @returns {void}
     */
    update_state_dblclick(newUsed, newAvailable) {
        this.available.addEventListener('dblclick', (e) => {
            if (e.target.matches(".list-group-item")) {
                var li = (this.clone === "true") ? e.target.cloneNode(true) : this.available.removeChild(e.target);
                this.used.append(li);
                this.update_state(newUsed, newAvailable)
            }
        });
        this.used.addEventListener('dblclick', (e) => {
            if (e.target.matches(".list-group-item")) {
                var li = this.used.removeChild(e.target);
                if (this.clone !== "true") {
                    this.available.insertBefore(li, this.available.children[1]);
                }
                this.update_state(newUsed, newAvailable);
            }
        });
    }

    /**
     * Generates the initial state of used and available items based on the provided proof steps and input ID.
     *
     * @method
     * @private
     * @param {Object} proofSteps - Object containing proof steps.
     * @param {string} inputId - ID of the input element for storing state.
     * @returns {Object} The initial state object with used and available lists.
     */
    _generate_state(proofSteps, inputId) {
        let stateStore = document.getElementById(inputId);
        if (stateStore == null) {
            return {used: [], available: [...Object.keys(proofSteps)]}
        }
        return (stateStore.value && stateStore.value != "") ? 
            JSON.parse(stateStore.value) : 
            {used: [], available: [...Object.keys(proofSteps)]};
    }

    /**
     * Creates a header element.
     *
     * @method
     * @private
     * @param {string} innerHTML - Inner HTML content of the header.
     * @param {string} id - ID of the header element.
     * @returns {HTMLElement} The created header element.
     */
    _create_header(innerHTML, id) {
        let i = document.createElement("i");
        i.innerHTML = innerHTML;
        i.className = "list-group-item header";
        i.setAttribute("id", id);
        return i;
    }

};

export default {stack_sortable};
