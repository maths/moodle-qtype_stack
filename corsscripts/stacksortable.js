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
 * NB: there is a rare case in which this causes issues: if the author happens to only use a subset of 
 * ["steps", "options", "headers", "available_header", "index"] as the keys inside their `steps` Question variable.
 * This will cause improper validation in the call of `_validate_parsons_JSON` but it will also cause
 * functional issues in the question because we will extract the values of those keys from the object.
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
        var sortableUserOpts = {used: steps["options"], available: steps["options"]};
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
 * NB: The separation of depth 1 and depth 2 cases only really works when raw JSON are written by the author. 
 * This does not work in cases where they are pulled through from Maxima variables using `{# ... #}`. This is 
 * because we check JSON depth by checking if any of the values is an object, and in the Maxima case, this isn't true as the
 * item causing "depth 2" is now a string containing a two-dimensional array. Not clear how to circumvent this, 
 * it just means there's a gap in validation currently but does not break any functionality.
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
    if (Object.values(steps).every((val) => !(typeof(val) == "object"))) {
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
    if (typeof(steps) == "string") {
        steps = _stackstring_objectify(steps);
    }

    // Check whether all numeric keys are passed. Since keys in Maxima arrays are hashed, this only invalidates 
    // the case where JSON's are authored with numeric keys directly inside the parson's block.
    // Numeric keys are a problem because they are ordered by JS objects
    if (Object.keys(steps).every((key) => !isNaN(parseInt(key)))) {
        return false
    }
    return Object.values(steps).every((val) => typeof(val) == "string");
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
    const missingRequiredKeys = requiredKeys.filter((key) => !keys.includes(key));
    const invalidKeys = keys.filter((key) => !validKeys.includes(key));
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
 * @param {Object} steps - The flat steps object.
 * @param {string|null} inputId - The ID of the input element storing the state data.
 * @param {Object|null} options - Options for the Sortable lists.
 * @param {boolean} clone - Indicates whether clone mode is being used in the `parsons` block.
 * @param {number} columns - The number of columns being used, default is 1.
 * @param {number|null} rows - The number of rows.
 * @param {string} orientation - The orientation of the Parsons object. Default is "col".
 * @param {string} index - The index for the proof steps. Default is an empty string.
 * @param {boolean} grid - False if the `parsons` block is being used for proof, true if for grouping and matching.
 *                         Affects styling.
 * @param {string|null} item_height - The height of each item in the sortable lists (including headers and indexes).
 * @param {string|null} item_width - The width of each item in the sortable lists (including headers and indexes).
 *
 * @property {Object} steps - Object containing all steps.
 * @property {string} inputId - ID of the input element for storing state (optional).
 * @property {Array} state - Current state of used and available items of the form [[{"used": [...], "available": [...]}, <timestamp>]].
 * @property {Array} history - All states up to the current state.
 * @property {Object} userOptions - User-defined options merged with default options.
 * @property {boolean} clone - Flag indicating whether to clone elements during sorting.
 * @property {Object} options - Final options for sortable lists.
 * @property {String} columns - Number of columns being used.
 * @property {String} rows - Number of rows being used.
 * @property {Array} index - List of index items.
 * @property {boolean} use_index - Whether an index has been passed to the constructor or not.
 * @property {boolean} grid - Whether grid styling is to be applied (i.e., false if using for proof).
 * @property {string} item_class - The style class to use, different for proof vs. matching.
 * @property {boolean} override_item_height - Whether a custom item height has been passed.
 * @property {boolean} override_item_width - Whether a custom item width has been passed.
 * @property {Object} item_height_width - If item_height and item_width are passed to the constructor, object containing them.
 * @property {Object} container_height_width - Add padding to this.item_height_width to allow custom heights/widths to work.
 * @property {Object} ids - Contains DOM ids for used and available lists.
 * @property {Array} usedId - Two-dimensional array containing used list DOM ids.
 * @property {string} availableId - String containing the available list DOM id.
 * @property {Object} defaultOptions - Default sortable options.
 *
 * @method add_dblclick_listeners - Add listeners that moves items on double-click and updates state for proofs only.
 * @method add_delete_all_listener - Adds a listener that deletes all from the used list and updates state.
 * @method add_headers - Adds header elements to the used and available lists.
 * @method add_index - Add an index column if passed to the constructor.
 * @method add_reorientation_button - Adds a button that allows user to flip between orientations on question page.
 * @method create_row_col_divs - Generates the HTML row and column divs according to how columns and rows are passed to constructor.
 * @method generate_available - Generates the available list based on the current state.
 * @method generate_used - Generates the used list based on the current state.
 * @method update_state - Updates the state based on changes in the used and available lists.
 * @method update_grid_empty_css - Updates the CSS styling of grid-items.
 * @method resize_grid_items - Auto-resizes the height and widths of elements with grid-item and grid-item-rigids. 
 * @method validate_options - Validates the sortable user options.
 *
 * @example
 * // Creating a basic StackSortable instance for proof:
 * const sortable = new stack_sortable({
 *   "step1": "Step 1",
 *   "step2": "Step 2",
 *   // ...
 * }, "ans1", { used: { animation: 100 }, available: { animation: 100 } }, false);
 *
 * // Generating lists and adding headers:
 * sortable.generate_available();
 * sortable.generate_used();
 * sortable.add_headers();
 *
 * // Create the Sortable answer lists.
 * var sortableUsed =
 *       stackSortable.ids.used.map((idList) =>
 *           idList.map((usedId) => Sortable.create(document.getElementById(usedId), stackSortable.options.used)));
 *
 * // Create the Sortable available list.
 * var sortableAvailable = Sortable.create(availableList, stackSortable.options.available);
 *
 * // Add the state callback function for all the created sortables.
 * sortableUsed.forEach((sortableList) =>
 *           sortableList.forEach((sortable) =>
 *               sortable.option("onSort", () => {
 *                   stackSortable.update_state(sortableUsed, sortableAvailable);})
 *           )
 *       );
 * sortableAvailable.option("onSort", () => {stackSortable.update_state(sortableUsed, sortableAvailable);});
 *
 * @exports stack_sortable
 */
export const stack_sortable = class stack_sortable {
    /**
     * Constructor for the StackSortable class.
     *
     * @constructor
     * @param {Object} steps - The flat steps object.
     * @param {string|null} inputId - The ID of the input element storing the state data.
     * @param {Object|null} options - Options for the Sortable lists.
     * @param {boolean} clone - Indicates whether clone mode is being used in the `parsons` block.
     * @param {number} columns - The number of columns being used, default is 1.
     * @param {number|null} rows - The number of rows.
     * @param {string} orientation - The orientation of the Parsons object. Default is "col".
     * @param {string} index - The index for the proof steps. Default is an empty string.
     * @param {boolean} grid - False if the `parsons` block is being used for proof, true if for grouping and matching.
     *                         Affects styling.
     * @param {string|null} item_height - The height of each item in the sortable lists (including headers and indexes).
     * @param {string|null} item_width - The width of each item in the sortable lists (including headers and indexes).
     * @param {string|'false'} log - Whether to use the full history or current state as input store.
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
            item_width = null,
            log = "false") {
        this.steps = steps;
        this.inputId = inputId;
        this.orientation = orientation;
        this.columns = ((this.orientation === "col") ? columns : rows);
        this.rows = ((this.orientation === "col") ? rows : columns);
        this.index = index;
        this.use_index = this.index !== "";
        this.grid = grid;
        this.item_class = (
            this.grid ? (this.orientation === "row" ? "grid-item-rigid" : "grid-item") : "list-group-item"
        );
        this.override_item_height = (item_height !== "");
        this.override_item_width = (item_width !== "");
        this.item_height_width = {"style" : ""};
        if (this.override_item_height) {this.item_height_width["style"] += `height:${item_height}px;`}
        if (this.override_item_width) {this.item_height_width["style"] += `width:${item_width}px;`}
        this.item_height_width = (this.item_height_width["style"] === "") ? {} : this.item_height_width;
        this.container_height_width = (Object.keys(this.item_height_width).length !== 0) ?
            {"style" : this.item_height_width["style"]} : {};
        this.state = this._generate_state(this.steps, inputId, Number(this.columns), Number(this.rows));
        // this.history logs all states in an attempt of the format [[<latest state>, <timestamp>], ..., [<initial state>, <timestamp>]]
        this.history = this.state;
        this.log = log;
        if (inputId !== null) {
            this.input = document.getElementById(this.inputId);
            this.submitted = this.input.getAttribute("readonly") === "readonly"
        }
        this.ids = this._create_ids(this.rows, this.columns);
        this.availableId = this.ids.available;
        this.usedId = this.ids.used;
        this.clone = clone;
        this.defaultOptions = {used: {animation: 50}, available: {animation: 50}};
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
     *
     * @method
     * @param {Object} newUsed - Updated used list.
     * @param {Object} newAvailable - Updated available list.
     * @returns {void}
     */
    add_dblclick_listeners(newUsed, newAvailable) {
        this.available.addEventListener("dblclick", (e) => {
            if (this._double_clickable(e.target)) {
                // get highest-level parent
                var li = this._get_moveable_parent_li(e.target);
                li = (this.clone === "true") ? li.cloneNode(true) : this.available.removeChild(li);
                this.used[0][0].append(li);
                this.update_state(newUsed, newAvailable);
            }
        });
        this.used[0][0].addEventListener("dblclick", (e) => {
            if (this._double_clickable(e.target)) {
                // get highest-level parent
                var li = this._get_moveable_parent_li(e.target);
                this.used[0][0].removeChild(li);
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
        button.addEventListener("click", () => {
            this._delete_all_from_used(); this.update_state(newUsed, newAvailable); this.update_grid_empty_css();});
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
        parentEl.insertBefore(
            this._create_header(available_header, "availableHeader", this.item_height_width), parentEl.firstChild);
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
            (["list-group", this.orientation, "usedList"]) :
            ([this.orientation, "usedList"]);
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
                // In matching mode, we need to add rigid styles to colDivs if orientation === "row"
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
                    // In matching mode, we need to add rigid styles to colDivs if orientation === "row"
                    if (this.orientation === "row") {
                        divRowCol.classList.add(...itemClassList, ...["empty", "col-rigid"]);
                    } else {
                        divRowCol.classList.add(...itemClassList, ...["empty"]);
                    }
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
        this.state[0][0].available.forEach(key => this.available.append(this._create_li(key, this.item_height_width)));
    }

    /**
     * Generates the used list based on the current state.
     *
     * @method
     * @returns {void}
     */
    generate_used() {
        for (const [i, value] of this.state[0][0].used.entries()) {
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
     * Resizes the heights and widths of all grid-item and grid-item-rigid elements according to the maximum content height across all such items.
     * Only resizes widths if in "row" orientation, where we have to fix widths with the grid-item-rigid class.
     * Otherwise widths are already automatically resized.
     * Avoids affecting proof mode by virtue of that mode avoiding grid-item classes entirely.
     * 
     * If `item_height` parameter has been passed, then heights will not be autoresized.
     * Likewise, if `item_width` parameter has been passed, then widths will not be autoresized.
     *
     * @method
     * @returns {void}
     */
    resize_grid_items() {
            const maxHeight = this._resize_compute_max_height('.grid-item, .grid-item-rigid');
            const maxWidth = this._resize_compute_max_width('.grid-item, .grid-item-rigid');

            // Resize the heights for both grid-item and grid-item-rigid
            this._resize_heights('.grid-item, .grid-item-rigid', maxHeight);

            // Additionally resize the width of grid-item-rigid
            this._resize_widths('.grid-item-rigid', maxWidth);
            this._resize_grid_container_heights(maxHeight);
            this._resize_grid_container_widths(maxWidth);
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
        var newState = [[{used: newUsed.map((usedList) => usedList.map((used) => used.toArray())), available: newAvailable.toArray()}, this._get_current_seconds()]];
        this.state = newState;
        // Only log genuinely different states; this is because update_state is called once for each list (at least twice) on each update
        if (JSON.stringify(newState[0][0]) !== JSON.stringify(this.history[0][0])) {
            this.history.unshift(newState[0]);
        }
        if (this.inputId !== null) {
            this.input.value = (this.log === 'false') ? JSON.stringify(this.state) : JSON.stringify(this.history);
            this.input.dispatchEvent(new Event("change"));
        }
    }

    /**
     * Updates empty class elements according as whether they are no longer empty or become empty.
     * This is not used by proof or grouping mode because the lists are never empty (they contain headers).
     * This only affects the case in grid mode, where there are empty placeholders.
     * This should be passed to `onSort` option of sortables so that every time an element is dragged, 
     * the CSS updates accordingly.
     *
     * @method
     * @returns {void}
     */
    update_grid_empty_css() {
        // Remove empty class if no longer empty.
        const empties = document.querySelectorAll('.empty');
        empties.forEach((el) => {if (!this._is_empty(el)) {
            el.classList.remove('empty');
            el.style.height = '';
            el.style.margin = '';
        }})

        // re-assign empty class if empty.
        const usedLists = document.querySelectorAll('.usedList');
        usedLists.forEach((el) => {if (this._is_empty(el)) {
            el.classList.add('empty');
            // We need to auto-resize the height again in this case. 
            this._resize_set_height(el, this._resize_compute_max_height('.grid-item, .grid-item-rigid') + 12);
        }})
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
        var err = "";
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
        var addClass = (this.grid && this.orientation !== "col") ?
            [this.item_class, "index"] : [this.item_class, "header"];
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
            [this.item_class, "index"] : [this.item_class, "header"];
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
        return !li.matches(".header") && !li.matches(".index") && !this._is_empty(li);
    }

    /**
     * Delete all non-header items from the "used" list.
     *
     * @method
     * @private
     * @returns {void}
     */
    _delete_all_from_used() {
        const lis = document.querySelectorAll(".usedList li[data-id]");
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
        document.body.insertBefore(warning, document.getElementById("containerRow"));
    }

    /**
     * Check if an HTML element is double-clickable (i.e., it is not a header or index element).
     *
     * This private method is called on items inside the used or available list.
     *
     * @method
     * @private
     * @param {HTMLElement} item - The HTML element to check for double-clickability.
     * @returns {boolean} - Returns true if the element is double-clickable, false otherwise.
     */
    _double_clickable(item) {
        return !item.matches(".header") && !item.matches(".index");
    }

    /**
     * Flips the question between vertical and horizontal orientations.
     *
     * @method
     * @returns {void}
     */
    _flip_orientation() {
        // Define CSS classes based on orientation and whether using for proof or grouping or matching.
        var addClass = (this.orientation === "row") ? ["list-group", "col"] : ["row"];
        if (this.grid) {
            // Current classes we need to remove.
            var removeClass = (this.orientation === "row") ? ["list-group", "row"] : ["list-group", "col"];
            // Current grid class being used.
            var currGridClass = (this.orientation === "row") ? "grid-item-rigid" : "grid-item";
            // Grid class to add.
            var gridAddClass = (this.orientation === "row") ? "grid-item" : "grid-item-rigid"
            // Get all grid items and replace their classes
            var gridItems = document.querySelectorAll(`.${currGridClass}`);
            gridItems.forEach((item) => {
                item.classList.remove(currGridClass);
                item.classList.add(gridAddClass);
            })

            // In matching mode, we need to add rigid styles to columns as well as items.
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
            // In proof mode just switch between row and col.
            var removeClass = (this.orientation === "row") ? ["row"] : ["col"];
        }

        // Now classes have been defined appropriately according to case, we replace classes in answer lists.
        this.colIds.forEach((colId) => {
                var ul = document.getElementById(colId);
                ul.classList.remove(...removeClass);
                ul.classList.add(...addClass);
            }
        );

        // Do the same for the available list.
        this.available.classList.remove(...removeClass);
        this.available.classList.add(...addClass);

        // Move position of available list to above in horizontal, or to the right in vertical.
        if (this.orientation === "col") {
            this.available.parentNode.insertBefore(this.available, this.available.parentNode.firstChild);
        } else {
            this.available.parentNode.append(this.available);
        }

        // In grid mode (either matching or grouping) headers become indices and vice versa.
        if (this.grid) {
            // Headers to index
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

            // Index to headers (if index is being used).
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
                    document.querySelectorAll("#index > .header").forEach((header) => {
                        if (!header.classList.contains("index")) {
                            header.classList.remove("header");
                            header.classList.add("index");
                        }
                    })
                }
            }
        };

        // Keep track of current orientation.
        this.orientation = (this.orientation === "row") ? "col" : "row";

        // CSS resizing of grid-items
        // --------------------------
        // Reset heights and widths of grid items.
        if (!this.override_item_width) {
            document.querySelectorAll('.grid-item, .grid-item-rigid').forEach((item) => item.style.width = '');
        }
        if (!this.override_item_width) {
            document.querySelectorAll('.grid-item, .grid-item-rigid').forEach((item) => item.style.height = '');
        }
        // Then update the CSS accordingly.
        this.resize_grid_items();
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
            return [[{used: usedState, available: [...Object.keys(steps)]}, this._get_current_seconds()]];
        }
        return (stateStore.value && stateStore.value != "") ?
            JSON.parse(stateStore.value) :
            [[{used: usedState, available: [...Object.keys(steps)]}, this._get_current_seconds()]];
    }

    /**
     * Gets the current second elapsed since the start of the Unix epoch (1st January, 1970, 00:00 GMT)
     * 
     * @returns {number} Number of elapsed seconds
     */
    _get_current_seconds() {
        return Math.floor(Date.now() / 1000);
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
     * Checks if an element is empty.
     *
     * @param {HTMLElement} el - The element to check.
     * @returns {boolean} - True if the element is empty, otherwise false.
     */
    _is_empty(el) {
        return el.textContent.trim() === "" && el.children.length === 0;
    }

    /**
     * Computes the maximum height of all items as specified by the CSS selector.
     * 
     * @method
     * @returns {void}
     */
    _resize_compute_max_height(selector) {
        const selectedItems = document.querySelectorAll(selector);
        return Math.max(...[...selectedItems].map((item) => item.scrollHeight));
    }

    /**
     * Computes the maximum width of all items as specified by the CSS selector.
     * 
     * @method
     * @returns {void}
     */
    _resize_compute_max_width(selector) {
        const selectedItems = document.querySelectorAll(selector);
        return Math.max(...[...selectedItems].map((item) => item.scrollWidth));
    }

    /**
     * Resizes the height of an element in `px`.
     * 
     * If `item_height` parameter has been passed, then height will be overriden by the value in that parameter.
     *
     * @method
     * @returns {void}
     */
    _resize_set_height(el, height) {
        el.style.height = (this.override_item_height) ? this.item_height_width['style']['height'] : `${height}px`;
    }

    /**
     * Resizes the width of an element in `px`.
     * 
     * If `item_width` parameter has been passed, then width will be overriden by the value in that parameter.
     *
     * @method
     * @returns {void}
     */
    _resize_set_width(el, width) {
        el.style.width = (this.override_item_width) ? this.item_height_width['style']['width'] : `${width}px`;
    }

    /**
     * Resizes the heights of items specified by the CSS selector.
     * 
     * If `item_height` parameter has been passed, then heights will be overriden by the value in that parameter.
     *
     * @method
     * @returns {void}
     */
    _resize_heights(selector, height) {
        document.querySelectorAll(selector).forEach((item) => this._resize_set_height(item, height));
    }

    /**
     * Resizes the widths of items specified by the CSS selector.
     * 
     * If `item_width` parameter has been passed, then widths will be overriden by the value in that parameter.
     *
     * @method
     * @returns {void}
     */
    _resize_widths(selector, width) {
        document.querySelectorAll(selector).forEach((item) => this._resize_set_width(item, width));
    }

    /**
     * Resizes the heights of all containers containing placeholder lists in grid mode. Adds some extra to account for margin.
     * 
     * If `item_height` parameter has been passed, then the heights will be overriden by the value in that parameter.
     *
     * @method
     * @returns {void}
     */
    _resize_grid_container_heights(height) {
        if (this.rows !== "" && this.columns !== "") {
            for (const [i, value] of this.state[0][0].used.entries()) 
                for (const [j, _] of value.entries()) {
                    this._resize_set_height(this.used[i][j], height + 12);
            }
        }
    }

    /**
     * Resizes the widths of all containers containing placeholder lists in grid mode. Adds some extra to account for margin.
     * 
     * If `item_width` parameter has been passed, then the widths will be overriden by the value in that parameter.
     *
     * @method
     * @returns {void}
     */
    _resize_grid_container_widths(width) {
        if (this.rows !== "" && this.columns !== "") {
            for (const [i, value] of this.state[0][0].used.entries()) 
                for (const [j, _] of value.entries()) {
                    // Adjust the width if in horizontal orientation.
                    if (this.orientation === "row") {
                        this._resize_set_width(this.used[i][j], width + 12);
                    } else {
                        // Else, set to inherit or override.
                        this.used[i][j].style.width = (this.override_item_width) ? this.item_height_width['style']['width'] : '';
                    }
            }
        }
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
