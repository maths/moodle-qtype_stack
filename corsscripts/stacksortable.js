/**
 * This is a library for SortableJS functionality used to generate STACK Parsons blocks.
 *
 * @copyright  2023 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Currently supported Sortable callback functions. This should be modified if updating the version of Sortable.js and
 * new callback functions have been added.
 */
export const SUPPORTED_CALLBACK_FUNCTIONS = [
    "onChoose",
    "onUnchoose",
    "onStart",
    "onEnd",
    "onAdd",
    "onUpdate",
    "onSort",
    "onRemove",
    "onFilter",
    "onMove",
    "onClone",
    "onChange"
];

/**
 * Preprocess and validate steps, sortable user options, headers and indices ready for use in `stack_sortable` class.
 *
 * The function takes steps in the form of a Parson's JSON or Maxima string variable, along with block user options
 * and sortable user options. It performs the following tasks:
 * 1. If `steps` is a Maxima string of expected format, it converts it to an object using `_stackstring_objectify`.
 * 2. It validates the structure of the Parson's JSON using `_validate_parsons_JSON`.
 * 3. If the Parsons JSON is of depth two with a valid set of top-level keys it separates them.
 * 4. If `steps` is a Maxima string (after separation), it converts it to an object.
 *
 * @param {Object|string} steps - The steps object or string representation of steps.
 * @param {Object} sortableUserOpts - Options for the sortable plugin.
 * @param {Array} headers - Headers for the answer lists.
 * @param {Array} available_header - Header for the available list.
 * @param {Array} index - Index column.
 * @returns {Array} - An array containing preprocessed steps, options, headers, available header, and index in that order.
 *
 * @example
 * // Returns [
 * //   { step1: "step 1 text", step2: "step 2 text" },
 * //   { option1: "Value 1", option2: "Value 2" },
 * //   ["header 1", "header 2"],
 * //   ["Drag from here:"],
 * //   null,
 * //   true
 * // ]
 * preprocess_steps({
 *   steps: {
 *     step1: "step 1 text",
 *     step2: "step 2 text"
 *   },
 *   options: {
 *     option1: "Value 1",
 *     option2: "Value 2"
 *   }
 * }, ["header 1", "header 2"], ["Drag from here:"], null);
 */
export function preprocess_steps(steps, sortableUserOpts, headers, available_header, index) {
    // Check if steps is a string and convert it to an object
    // (this occurs when the steps are a flat list coming from a Maxima variable)
    if (typeof steps === "string") {
        steps = _stackstring_objectify(steps);
    }

    // Validate the object
    var valid = _validate_parsons_JSON(steps);

    // At this point, we know steps is either a flat JSON, or it's top-level keys are a subset of 
    // ["steps", "options", "headers", "available_header", "index"], and contains at least "steps". 
    // Separate these if they are present.
    if (_validate_top_level_keys_JSON(steps, ["steps", "options", "headers", "index", "available_header"], ["steps"])) {
        var sortableUserOpts = steps["options"];
        // only want to replace defaults for headers if they have been provided
        if ("headers" in steps) {
            headers = steps["headers"];
        }
        if ("available_header" in steps) {
            available_header = steps["available_header"];
        }
        index = steps["index"];
        steps = steps["steps"];
    }

    // Convert steps to an object if it is still a string (occurs when the steps comes from a Maxima variable)
    if (typeof steps === "string") {
        steps = _stackstring_objectify(steps);
    }

    return [steps, sortableUserOpts, headers, available_header, index, valid];
}

/**
 * Convert a JSON-formatted stack string array of format '[["key", "value"], ..., ["key", "value"]]' into a JavaScript object.
 *
 * @param {string} stackjson_array_string - The JSON-formatted stack string array of format
 * '[["key", "value"], ..., ["key", "value"]]'.
 * @returns {Object} - The JavaScript object created from the stack string.
 *
 * @example
 * // Returns { key1: 'value1', key2: 'value2' }
 * _stackstring_objectify('["key1", "value1"], ["key2", "value2"]]');
 */
function _stackstring_objectify(stackjson_array_string) {
    return Object.fromEntries(new Map(Object.values(JSON.parse(stackjson_array_string))));
}

/**
 * Validate the structure of Parson's JSON `steps`.
 *
 * The function checks the structure of the provided Parson's JSON (`steps`)
 * to ensure it follows specific patterns:
 * 1. If the JSON has depth 1, it should be a valid flat JSON (i.e., should have string values).
 * 2. If the JSON has depth 2, the top-level keys should be a subset of 
 *    `["steps", "options", "headers", "index", "available_header"]`, and must contain `"steps"`. 
 *    The value for "steps" should be a valid flat JSON. Options are not validated here.
 *
 * @param {Object} steps - The Parson's JSON to be validated.
 * @returns {boolean} - Returns true if the provided Parsons JSON follows the expected structure, false otherwise.
 *
 * @example
 * // Returns true
 * _validate_parsons_JSON({
 *   "step1": "step 1 text",
 *   "step2": "step 2 text"
 * });
 *
 * @example
 * // Returns true
 * _validate_parsons_JSON({
 *   "steps": {
 *     "step1": "step 1 text",
 *     "step2": "step 2 text"
 *   },
 *   "options": {
 *     "option1": "value1",
 *     "option2": "value2"
 *   }
 * });
 *
 * @example
 * // Returns false
 * _validate_parsons_JSON({
 *   "invalidKey": {
 *     "step1": "step 1 text",
 *     "step2": "step 2 text"
 *   }
 * });
 */
function _validate_parsons_JSON(steps) {
    // If the JSON has depth 1 then it should be a valid flat JSON (i.e., should have string values).
    if (Object.values(steps).every((val) => !(typeof(val) == 'object'))) {
        return _validate_flat_steps(steps);
    }
    // Else the top-level of the JSON should have keys that are a subset of ["steps", "options", "headers", "index", "available_header"]
    // and a superset of ["steps"].
    // The value for "steps" should be a valid flat JSON.
    // We do not validate options here.
    if (Object.values(steps).some((val) => typeof(val) == "object")) {
        if (!_validate_top_level_keys_JSON(steps, ["steps", "options", "headers", "index", "available_header"], ["steps"])) {
            return false;
        }
        if (!_validate_flat_steps(steps["steps"])) {
            return false;
        }
        return true;
    }
}

/**
 * Validate the structure of a flat steps JSON.
 *
 * The function checks the structure of the provided steps (`steps`)
 * to ensure that all values are strings.
 *
 * If the steps are provided as a Maxima variable (string of form '[["key", "value"], ...]'), they are converted
 * to a JSON object using the `_stackstring_objectify` function before validation.
 *
 * @param {string|Object} steps - The flat JSON to be validated. If a string,
 * it is assumed to be a Maxima variable and will be converted to a JSON object.
 * @returns {boolean} - Returns true if all values in `steps` are strings, false otherwise.
 *
 * @example
 * // Returns true
 * _validate_flat_steps({
 *   step1: "step 1 text",
 *   step2: "step 2 text"
 * });
 *
 * @example
 * // Returns true
 * _validate_flat_steps('["step1", "step 1 text"], ["step2", "step 2 text"]]');
 *
 * @example
 * // Returns false
 * _validate_flat_steps({
 *   step1: "step 1 text",
 *   step2: 123 // Not a string
 * });
 */
function _validate_flat_steps(steps) {
    // Case when steps are coming from a Maxima variable: convert to a JSON
    if (typeof(steps) == 'string') {
        steps = _stackstring_objectify(steps);
    }
    return Object.values(steps).every((val) => typeof(val) == 'string');
}

/**
 * Validates the top-level keys of a JSON object by checking they are a subset of `validKeys` 
 * and a superset of `requiredKeys`.
 * 
 * @param {Object} JSON - The JSON object to validate.
 * @param {Array} validKeys - An array of valid top-level keys.
 * @param {Array} requiredKeys - An array of top-level keys that are required.
 * @returns {boolean} - True if the JSON object passes validation, otherwise false.
 */
function _validate_top_level_keys_JSON(JSON, validKeys, requiredKeys) {
    const keys = Object.keys(JSON);
    const missingRequiredKeys = requiredKeys.filter(key => !keys.includes(key));
    const invalidKeys = keys.filter(key => !validKeys.includes(key));
    return invalidKeys.length === 0 && missingRequiredKeys.length === 0;
}

/**
 * Get the current height of the iframe's content document.
 *
 * @returns {number} - The height of the iframe's content document.
 */
export function get_iframe_height() {
    return document.documentElement.offsetHeight;
}

/**
 * Class for for managing Sortable lists for Parson's block questions in STACK.
 *
 * @class
 * @param {Object} steps - Object containing flat steps JSON.
 * @param {string} availableId - ID of the available list element.
 * @param {string} usedId - ID of the used list element.
 * @param {string|null} inputId - ID of the input element for storing state (optional).
 * @param {Object|null} options - Custom options for sortable lists (optional).
 * @param {boolean} clone - Flag indicating whether to clone elements during drag-and-drop.
 *
 * @property {Object} steps - Object containing all steps.
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
 * @method add_dblclick_listeners - Add listeners that moves items on double-click and updates state.
 * @method add_delete_all_listener - Adds a listener that deletes all from the used list and updates state.
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
     * @param {Object} steps - Object containing the flat steps JSON.
     * @param {string} availableId - ID of the available list element.
     * @param {string} usedId - ID of the used list element.
     * @param {string|null} inputId - ID of the input element for storing state (optional).
     * @param {Object|null} options - Custom options for sortable lists
     *                                of form {used: UsedOptions, available: AvailableOptions} (optional).
     * @param {boolean} clone - Flag indicating whether to clone elements during sorting.
     */
    constructor(steps, 
            inputId = null, 
            options = null, 
            clone = false, 
            columns = 1, 
            rows = null, 
            orientation = "col", 
            index = "", 
            grid = false, 
            item_height = null, 
            item_width = null) {
        this.steps = steps;
        this.inputId = inputId;
        this.orientation = orientation;
        this.columns = (this.orientation === "col") ? columns : rows;
        this.rows = (this.orientation === "col") ? rows : columns;
        this.index = index;
        this.use_index = this.index !== "";
        this.grid = grid;
        this.item_class = this.grid ? 
            (this.orientation === "row" ? "grid-item-rigid" : "grid-item") : "list-group-item";
        this.item_height_width = {'style' : ''};
        for (const [key, val] of [['height', item_height], ['width', item_width]]) {
            if (val !== '') {this.item_height_width['style'] += `${key}:${val}px;`};
        };
        this.item_height_width = (this.item_height_width['style'] === '') ? {} : this.item_height_width;
        this.item_height = (item_height !== '') ? {'style' : `height:${item_height}px;`} : {};
        this.item_width = (item_width !== '') ? {'style' : `width:${item_width}px;`} : {};
        this.container_height_width = (this.item_height_width['style'] !== '') ? {'style' : this.item_height_width['style'] + 'margin: 12px;'} : {};
        this.state = this._generate_state(this.steps, inputId, Number(this.columns), Number(this.rows));
        if (inputId !== null) {
            this.input = document.getElementById(this.inputId);
            this.submitted = this.input.getAttribute("readonly") === "readonly"
        }
        this.ids = this._create_ids(this.rows, this.columns);
        this.availableId = this.ids.available;
        this.usedId = this.ids.used;
        this.clone = clone;
        
        this.defaultOptions = {used: {animation: 50, cancel: ".header"}, available: {animation: 50, cancel: ".header"}};
        // Merges user options and default, overwriting default with user options if they clash
        this.userOptions = this._set_user_options(options);

        // Create overall options from this.userOptions by setting ghostClass and group to required options
        // and overwriting them if they appear in userOptions. This also disables the list if they have been 
        // submitted.
        this.options = this._set_ghostClass_group_and_disabled_options();
    }

    /**
     * Adds double-click listeners to move items upon double-click and updates the state accordingly.
     * Only supported for proofmode
     * TODO : fix this
     *
     * @method
     * @param {Object} newUsed - Updated used list.
     * @param {Object} newAvailable - Updated available list.
     * @returns {void}
     */
    add_dblclick_listeners(newUsed, newAvailable) {
        this.available.addEventListener('dblclick', (e) => {
            if (this._double_clickable(e.target)) {
                // get highest-level parent
                var li = this._get_moveable_parent_li(e.target);
                li = (this.clone === "true") ? li.cloneNode(true) : this.available.removeChild(li);
                this.used[0].append(li);
                this.update_state(newUsed, newAvailable);
            }
        });
        this.used[0].addEventListener('dblclick', (e) => {
            if (this._double_clickable(e.target)) {
                // get highest-level parent
                var li = this._get_moveable_parent_li(e.target);
                this.used[0].removeChild(li);
                if (this.clone !== "true") {
                    this.available.insertBefore(li, this.available.children[1]);
                }
                this.update_state(newUsed, newAvailable);
            }
        });
    }

    /**
     * Add a click event listener to a button to delete all items from the "used" list and
     * updates the state accordingly.
     *
     * @method
     * @param {string} buttonId - ID of the button element to attach the listener.
     * @param {Object} newUsed - Updated "used" list.
     * @param {Object} newAvailable - Updated "available" list.
     * @returns {void}
     */
    add_delete_all_listener(buttonId, newUsed, newAvailable) {
        const button = document.getElementById(buttonId);
        button.addEventListener('click', () => {
            this._delete_all_from_used(); this.update_state(newUsed, newAvailable);});
    }

    /**
     * Adds header elements to the used and available lists.
     *
     * @method
     * @param {Object} headers - Object containing header text for used and available lists.
     * @returns {void}
     */
    add_headers(headers, available_header) {
        for (const [i, value] of headers.entries()) {
            var parentEl = document.getElementById(`usedList_${i}`);
            var header = this._create_header(value, `usedHeader_${i}`, this.item_height_width);
            parentEl.insertBefore(header, parentEl.firstChild);
        }
        var parentEl = document.getElementById("availableList");
        parentEl.insertBefore(this._create_header(available_header, "availableHeader", this.item_height_width), parentEl.firstChild);
    }

    /**
     * Adds index elements to the DOM based on the provided index array.
     * 
     * @param {Array} index - The array containing index values to be added.
     */
    add_index(index) {
        for (const [i, value] of index.entries()) {
            // Deal with the item in both header and index separately
            if (i === 0) {
                var idx = this._create_index(value, `usedIndex_${i}`, this.item_height_width);
                var addClass = this.orientation === "col" ? "header" : "index";
                idx.classList.add(addClass);
            } else {
                var idx = this._create_index(value, `usedIndex_${i}`, this.item_height_width);
            }
            document.getElementById("index").append(idx);
        }
    }

    /**
     * Adds a reorientation button to the document body.
     * 
     * The button allows users to change the orientation of sortable lists between vertical 
     * and horizontal.
     */
    add_reorientation_button() {
        var btn = document.createElement("button");
        btn.id = "orientation";
        btn.setAttribute("class", "parsons-button");
        var icon = document.createElement("i");
        icon.setAttribute("class", "fa fa-refresh");
        btn.append(icon);
        btn.addEventListener("click", () => this._flip_orientation());
        document.body.insertBefore(btn, document.getElementById("containerRow"));
    }

    /**
     * Populates the DOM with row and column div elements to the document based 
     * on how many columns and rows are being passed to the instance.
     * 
     * How this occurs depends on various configurations.
     * - Lists should contain the `"row"` or `"col"` class according to the orientation.
     * - If the class is being used for proof (i.e., `this.grid === false`), then the list class should
     *   also contain `"list-group"`.
     * - Items class depends only on the orientation.
     */
    create_row_col_divs() {
        var usedClassList = (!this.grid || this.orientation === "col") ? 
            ["list-group", this.orientation, "usedList"]:
            [this.orientation, "usedList"];
        var itemClass = (this.orientation === "col") ? "row" : "col";
        var itemClassList = [itemClass, "usedList"];
        var availClassList = (!this.grid || this.orientation === "col") ?
            ["list-group", this.orientation] : 
            [this.orientation];
        var container = document.getElementById("containerRow");

        if (this.use_index) {
            var indexCol = document.createElement("div");
            indexCol.id = "index";
            indexCol.classList.add(...usedClassList);
            container.append(indexCol);
        }
        this.colIds.forEach((id) => 
            {
                var colDiv = document.createElement("ul");
                colDiv.id = id;
                colDiv.classList.add(...usedClassList);
                container.append(colDiv);
            });
        // if rows are specified then add the row divs
        if (this.rows !== "") {
            this.colIds.forEach((colId) => {
                var colDiv = document.getElementById(colId);
                colDiv.classList.add("container");
                this.rowColIds[colId].forEach((rowColId) => {
                    var divRowCol = document.createElement("li");
                    divRowCol.id = rowColId;
                    divRowCol.classList.add(...itemClassList);
                    colDiv.append(divRowCol);
                })
            })
        };

        var availDiv = document.createElement("ul");
        availDiv.id = this.ids.available;
        availDiv.classList.add(...availClassList);
        if (this.orientation === "col") {
            container.append(availDiv);
        } else {
            container.insertBefore(availDiv, container.firstChild);
        }

        this.used = this.usedId.map(idList => idList.map(id => document.getElementById(id)));
        this.available = document.getElementById(this.availableId);
    }

    /**
     * Generates the available list based on the current state.
     *
     * @method
     * @returns {void}
     */
    generate_available() {
        this.state.available.forEach(key => this.available.append(this._create_li(key, this.item_height_width)));
    }

    /**
     * Generates the used list based on the current state.
     *
     * @method
     * @returns {void}
     */
    generate_used() {
        for (const [i, value] of this.state.used.entries()) {
            if (this.rows !== "" && this.columns !== "") {
                for (const [j, val] of value.entries()) {
                    this._apply_attrs(this.used[i][j], this.container_height_width);
                    val.forEach(key => this.used[i][j].append(this._create_li(key, this.item_height_width)));
                }
            } else {
                value[0].forEach(key => this.used[i][0].append(this._create_li(key, this.item_height_width)));
            }
        }
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
        var newState = {used: newUsed.map((usedList) => usedList.map((used) => used.toArray())), available: newAvailable.toArray()};
        if (this.inputId !== null) {
            this.input.value = JSON.stringify(newState);
            this.input.dispatchEvent(new Event('change'));
        }
        this.state = newState;
    }

    /**
     * Validate user options against a list of possible option keys.
     *
     * This method checks the provided user options against a list of possible
     * option keys. It verifies if each key is recognized and throws warnings if
     * there are unknown keys or if certain keys are being overwritten.
     *
     * @method
     * @param {string[]} possibleOptionKeys - List of possible option keys.
     * @param {string} unknownErr - Error message for unknown option keys.
     * @param {string} overwrittenErr - Error message for overwritten option keys.
     * @returns {void}
     *
     * @throws {warningMessage} If there are unknown option keys or if certain keys are being overwritten, a warning
     * will appear on the question page.
     */
    validate_options(possibleOptionKeys, unknownErr, overwrittenErr) {
        var err = '';
        var keysRecognised = true;
        var invalidKeys = [];
        // If option is not recognised warn user
        Object.keys(this.options.used).forEach(key => {
            if (!this._validate_option_key(key, possibleOptionKeys)) {
                keysRecognised = false;
                if (!invalidKeys.includes(key)) {
                    invalidKeys.push(key);
                }
            }
        });
        Object.keys(this.options.available).forEach(key => {
            if (!this._validate_option_key(key, possibleOptionKeys)) {
                keysRecognised = false;
                if (!invalidKeys.includes(key)) {
                    invalidKeys.push(key);
                }
            }
        });
        if (!keysRecognised) {
            err += unknownErr+ invalidKeys.join(", ") + ". ";
        }
        // If option is overwritten warn user (we have to use this.userOptions as this.options will contain these keys)
        var overwrittenKeys = [];
        var keysPreserved = true;
        ["ghostClass", "group", "onSort"].forEach(key =>
            {if (Object.keys(this.userOptions.used).includes(key) || Object.keys(this.userOptions.available).includes(key))
                {
                    keysPreserved = false;
                    overwrittenKeys.push(key);
                }
            }
        );
        if (!keysPreserved) {
            err += overwrittenErr + overwrittenKeys.join(", ") + ".";
        }
        if (!keysRecognised || !keysPreserved) {
            this._display_warning(err);
        }
    }

    /**
     * Applies attributes to an HTML element.
     * 
     * @param {HTMLElement} el - The HTML element to which attributes will be applied.
     * @param {Object} opts - An object containing attribute-value pairs to be applied.
     */
    _apply_attrs(el, opts) {
        for (const [key, value] of Object.entries(opts)) {
            el.setAttribute(key, value);
        }
    }

    /**
     * Creates a header element with specified inner HTML, ID, and other attributes.
     * 
     * @param {string} innerHTML - The inner HTML content of the header element.
     * @param {string} id - The ID attribute of the header element.
     * @param {Object} attrs - An object containing additional attributes for the header element.
     * @returns {HTMLElement} - The created header element.
     */
    _create_header(innerHTML, id, attrs) {
        let i = document.createElement("i");
        i.innerHTML = innerHTML;
        var addClass = (this.orientation === "col") ? 
            [this.item_class, 'header'] : [this.item_class, 'index'];
        i.classList.add(...addClass);
        this._apply_attrs(i, {...{"id" : id}, ...attrs});
        return i;
    }

    /**
     * Creates and organizes identifiers for rows and columns. If only columns are passed, then 
     * the used IDs will just be a flat list `["usedList_0", ..., "usedList_n"]`, where `columns = "n + 1"`.
     * If both rows and columns have non-null values, then this will be a two-dimensional array
     * `[["usedList_00", "usedList_01", ..., "usedList_0n"], ["usedList_10", ...], ...]`. 
     * In the two-dimensional case, a mapping between the column IDs `["usedList_0", ...]` and the 
     * two-dimensional array of item IDs is contained in the object `this.rowColIds`, that is 
     * `this.rowColIds["usedList_0"] = ["usedList_00", "usedList_01", ...]`.
     * 
     * @param {number} rows - The number of rows.
     * @param {number} columns - The number of columns.
     * @returns {Object} - An object containing identifiers for used and available elements.
     */
    _create_ids(rows, columns) {
        var colIdx = Array.from({length: columns}, (_, i) => i);
        var rowIdx = Array.from({length: rows}, (_, j) => j);
        this.colIds = colIdx.map((idx) => `usedList_${idx}`);
        this.rowColIds = {}
        colIdx.forEach((i) => this.rowColIds[this.colIds[i]] = rowIdx.map((j) => `usedList_${j}${i}`));
        var usedIds = (rows === "") ? 
            this.colIds.map((id) => [id]) : 
            Object.values(this.rowColIds);

        return {
            used: usedIds,
            available: "availableList"
        };
    }

    /**
     * Creates an index element with specified inner HTML, ID, and additional attributes.
     * 
     * @param {string} innerHTML - The inner HTML content of the index element.
     * @param {string} id - The ID attribute of the index element.
     * @param {Object} attrs - An object containing additional attributes for the index element.
     * @returns {HTMLElement} - The created index element.
     */
    _create_index(innerHTML, id, attrs) {
        let i = document.createElement("i");
        i.innerHTML = innerHTML;
        var addClass = (this.orientation === "col") ? 
            [this.item_class, 'index'] : [this.item_class, 'header'];
        i.classList.add(...addClass);
        this._apply_attrs(i, {...{"id" : id}, ...attrs});
        return i;
    }

    /**
     * Creates a list item (li) element containing the value of the specified key from `this.steps` and attributes.
     * 
     * @param {string} stepKey - The key whose HTML to get from `this.steps`.
     * @param {Object} attrs - An object containing additional attributes for the list item element.
     * @returns {HTMLElement} - The created list item (li) element.
     */
    _create_li(stepKey, attrs) {
        let li = document.createElement("li");
        li.innerHTML = this.steps[stepKey];
        this._apply_attrs(li, {...{"data-id" : stepKey}, ...attrs});
        li.className = this.item_class;
        return li;
    }

    /**
     * Checks if a list item (li) is deletable.
     * 
     * @param {HTMLElement} li - The list item (li) element to check.
     * @returns {boolean} - True if the list item is deletable, otherwise false.
     */
    _deletable_li(li) {
        return !li.matches(".header") && !li.matches(".index") && !this._is_empty_li(li);
    }

    /**
     * Delete all non-header items from the "used" list.
     *
     * @method
     * @private
     * @returns {void}
     */
    _delete_all_from_used() {
        const lis = document.querySelectorAll('.usedList li[data-id]');
        lis.forEach(li => {if (this._deletable_li(li)) {this._delete_li(li);}});
    }

    /**
     * Deletes a list item (li) from its parent node.
     * 
     * @param {HTMLElement} li - The list item (li) element to delete.
     */
    _delete_li(li) {
        li.parentNode.removeChild(li);
    }

    /**
     * Display a warning message on the question page.
     *
     * @method
     * @private
     * @param {string} msg - The message to be displayed in the warning.
     * @returns {void}
     */
    _display_warning(msg) {
        var warning = document.createElement("div");
        warning.className = "sortable-warning";
        var exclamation = document.createElement("i");
        exclamation.className = "icon fa fa-exclamation-circle text-danger fa-fw";
        warning.append(exclamation);
        var warningMessage = document.createElement("span");
        warningMessage.textContent = msg;
        warning.append(warningMessage);
        document.body.insertBefore(warning, document.getElementById("sortableContainer"));
    }

    /**
     * TODO: fix this, it should not be an index or grid-item or grid-item-rigid
     * Check if an HTML element is double-clickable (i.e., it is not a header element).
     *
     * This private method is called on items inside the used or available list.
     *
     * @method
     * @private
     * @param {HTMLElement} item - The HTML element to check for double-clickability.
     * @returns {boolean} - Returns true if the element is double-clickable, false otherwise.
     */
    _double_clickable(item) {
        return !item.matches(".header");
    }

    /* TODO : simplify this */
    _flip_orientation() {
        var addClass = (this.orientation === "row") ? ["list-group", "col"] : ["row"];
        if (this.grid) {
            var removeClass = (this.orientation === "row") ? ["list-group", "row"] : ["list-group", "col"];
            var currGridClass = (this.orientation === "row") ? "grid-item-rigid" : "grid-item";
            var gridAddClass = (this.orientation === "row") ? "grid-item" : "grid-item-rigid"
            var gridItems = document.querySelectorAll(`.${currGridClass}`);
            gridItems.forEach((item) => {
                item.classList.remove(currGridClass);
                item.classList.add(gridAddClass);
            })

            if (this.rows !== "") {
                [].concat(...this.used).forEach((div) => {
                    if (this.orientation === "col") {
                        div.classList.remove("row");
                        div.classList.add("col", "col-rigid");
                    } else {
                        div.classList.remove("col", "col-rigid");
                        div.classList.add("row");
                    }
                })
            }
        } else {
            var removeClass = (this.orientation === "row") ? ["row"] : ["col"];
        }
        this.colIds.forEach((colId) => {
                var ul = document.getElementById(colId);
                ul.classList.remove(...removeClass);
                ul.classList.add(...addClass);
            }
        );

        this.available.classList.remove(...removeClass);
        this.available.classList.add(...addClass);
        if (this.orientation === "col") {
            this.available.parentNode.insertBefore(this.available, this.available.parentNode.firstChild);
        } else {
            this.available.parentNode.append(this.available);
        }

        if (this.grid) {
            if (this.orientation === "col") {
                document.querySelectorAll(".header").forEach((header) => {
                    if (!header.classList.contains("index")) {
                        header.classList.remove("header");
                        header.classList.add("index");
                    }
                });
            } else {
                document.querySelectorAll(".index").forEach((index) => {
                    if (!index.classList.contains("header")) {
                        index.classList.remove("index");
                        index.classList.add("header");
                    }
                })
            }
        };

        if (this.use_index) {
            var indexDiv = document.getElementById("index");
            indexDiv.classList.remove(...removeClass);
            indexDiv.classList.add(...addClass);
            if (this.orientation === "col") {
                document.querySelectorAll("#index > .index").forEach((idx) => {
                    if (!idx.classList.contains("header")) {
                        idx.classList.remove("index");
                        idx.classList.add("header");
                    }
                })
            } else {
                document.querySelectorAll('#index > .header').forEach((header) => {
                    if (!header.classList.contains("index")) {
                        header.classList.remove("header");
                        header.classList.add("index");
                }
            })
        }
    }
        this.orientation = (this.orientation === "row") ? "col" : "row";
    }

    /**
     * Generates the initial state of used and available items based on the provided steps, input ID,
     * and number of columns and rows used. The shape of the used state will be `(1, 1, ?)` if in proof 
     * mode, `(n, 1, ?)` if `n` columns are specified and `(n, m, 1)` if `n` columns and `m` rows are specified.
     *
     * @method
     * @private
     * @param {Object} steps - Object containing steps.
     * @param {string} inputId - ID of the input element for storing state.
     * @returns {Object} The initial state object with used and available lists.
     */
    _generate_state(steps, inputId, columns, rows) {
        const usedState = (rows === 0 || columns === 0) ? 
                Array(columns).fill().map(() => [[]]) : 
                Array(columns).fill().map(() => Array(rows).fill([]));
        let stateStore = document.getElementById(inputId);
        if (stateStore === null) {
            return {used: usedState, available: [...Object.keys(steps)]};
        }
        return (stateStore.value && stateStore.value != "") ?
            JSON.parse(stateStore.value) :
            {used: usedState, available: [...Object.keys(steps)]};
    }

    /**
     * Get the nearest moveable parent list item for a given HTML element.
     *
     * This private method traverses the DOM hierarchy starting from the provided HTML
     * element and finds the nearest parent list item with the class ".list-group-item".
     * It is useful for identifying the moveable parent when doubling clicking on child
     * elements (for example MathJax display elements) inside list items.
     *
     * @method
     * @private
     * @param {HTMLElement} target - The HTML element for which to find the moveable parent list item.
     * @returns {HTMLElement|null} - The nearest parent list item with class ".list-group-item", or null if not found.
     */
    _get_moveable_parent_li(target) {
        var li = target;
        while (!li.matches(".list-group-item")) {
            li = li.parentNode;
        }
        return li;
    }

    /**
     * Checks if a list item (li) element is empty.
     * 
     * @param {HTMLElement} li - The list item (li) element to check.
     * @returns {boolean} - True if the list item is empty, otherwise false.
     */
    _is_empty_li(li) {
        return li.textContent.trim() === '' && li.children.length === 0;
    }

    /**
     * Set ghostClass, group and disabled-after-submission options for both "used" and "available" lists.
     *
     * This private method sets the ghostClass and group options for both "used" and "available" lists
     * and will overwrite user options for ghostClass and group if they are provided. This is required
     * for the functionality of the Sortable lists. If the question has been submitted, then this also
     * disables the Sortable lists to prevent further (visual) changes.
     *
     * @method
     * @private
     * @returns {Object} - Options containing ghostClass and group settings for both lists.
     */
    _set_ghostClass_group_and_disabled_options() {
        var group_val = {};
        group_val.used = (this.rows === "") ?
            {
                name: "sortableUsed", 
                pull: true, 
                put: true
            } : 
            {
                name: "sortableUsed", 
                pull: true, 
                put: (to) => to.el.children.length < 1 
            };
        
        group_val.available = (this.clone === "true") ?
            {
                name: "sortableAvailable", 
                pull: "clone", 
                revertClone: true, 
                put: false
            } :
            {
                name: "sortableAvailable", 
                put: true
            };
        
        var options_to_assign = this.submitted ?
            {
                used : {
                    ghostClass: "list-group-item-info", 
                    group: group_val.used, 
                    disabled: true
                }, 
                available : {
                    ghostClass: "list-group-item-info", 
                    group: group_val.available, 
                    disabled: true
                }
            } : 
            {
                used : {
                    ghostClass: "list-group-item-info", 
                    group: group_val.used
                }, 
                available : {
                    ghostClass: "list-group-item-info", 
                    group: group_val.available
                }
            }
        var options = {used:
            Object.assign(
                Object.assign({}, this.userOptions.used),
                            options_to_assign.used
                        ),
                        available :
            Object.assign(
                Object.assign({}, this.userOptions.available),
                            options_to_assign.available
                        )
        };

        return options;
    }

    /**
     * Set and merge user-provided options with default options.
     *
     * This private method sets user options for both "used" and "available" lists
     * by merging the provided options with the default options. If no options are
     * provided, it returns the default options.
     *
     * @method
     * @private
     * @param {Object|null} options - Custom options for sortable lists
     *                                of form {used: UsedOptions, available: AvailableOptions} (optional).
     * @returns {Object} - Merged user options for "used" and "available" lists.
     */
    _set_user_options(options) {
        var userOptions;
        if (options === null) {
            userOptions = this.defaultOptions;
        } else {
            userOptions = {used: Object.assign(this.defaultOptions.used, options.used),
                available: Object.assign(this.defaultOptions.available, options.available)};
        }
        return userOptions;
    }

    /**
     * Validate if a given option key is among the possible option keys.
     *
     * @method
     * @private
     * @param {string} key - The option key to validate.
     * @param {string[]} possibleOptionKeys - List of possible option keys.
     * @returns {boolean} - Returns true if the option key is valid, false otherwise.
     */
    _validate_option_key(key, possibleOptionKeys) {
        return possibleOptionKeys.includes(key);
    }
};

export default {stack_sortable};
