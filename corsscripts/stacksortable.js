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
 * Preprocess and validate proof steps, block user options, and sortable user options ready for use in `stack_sortable` class.
 *
 * The function takes proof steps in the form of a Parson's JSON or Maxima string variable, along with block user options
 * and sortable user options. It performs the following tasks:
 * 1. If `proofSteps` is a Maxima string of expected format, it converts it to an object using `_stackstring_objectify`.
 * 2. It validates the structure of the Parson's JSON using `_validate_parsons_JSON`.
 * 3. If the Parsons JSON contains "steps" and "options," it separates them.
 *    - If "header" is present in options, it separates this away from Sortable options into `blockUserOpts`.
 *    - It splits Sortable options into "used" and "available" and passes to `sortableUserOpts`.
 * 4. If `proofSteps` is a Maxima string (after separation), it converts it to an object.
 *
 * @param {string|Object} proofSteps - The proof steps to be preprocessed. Either a JSON of expected format
 * or
 * @param {Object} blockUserOpts - Block user options for the 'header' setting, should be passed as an empty Object.
 * @param {Object} sortableUserOpts - Sortable user options split into used and available, should be passed as an empty Object.
 * @returns {Array} - An array containing preprocessed proof steps, block user options,
 * sortable user options, and a boolean indicating the validity of the proof steps structure.
 *
 * @example
 * // Returns [
 * //   { step1: "Proof step 1", step2: "Proof step 2" },
 * //   { used: { header: "Header 1" }, available: { header: "Header 2" } },
 * //   { used: { option1: "Value 1" }, available: { option2: "Value 2" } },
 * //   true
 * // ]
 * preprocess_steps({
 *   steps: {
 *     step1: "Proof step 1",
 *     step2: "Proof step 2"
 *   },
 *   options: {
 *     header: ["Header 1", "Header 2"],
 *     option1: "Value 1",
 *     option2: "Value 2"
 *   }
 * }, {}, {});
 */
export function preprocess_steps(proofSteps, sortableUserOpts, headers, available_header, index) {
    // Check if proofSteps is a string and convert it to an object
    // (this occurs when proof steps are a flat list coming from a Maxima variable)
    if (typeof proofSteps === "string") {
        proofSteps = _stackstring_objectify(proofSteps);
    }

    // Validate the object
    var valid = _validate_parsons_JSON(proofSteps);

    // At this point, we know proofSteps is either a flat JSON, or it's top-level keys are a subset of 
    // ["steps", "options", "headers", ""index"], and contains at least "steps". Separate these if they are present
    if (_validate_top_level_keys_JSON(proofSteps, ["steps", "options", "headers", "index", "available_header"], ["steps"])) {
        var sortableUserOpts = proofSteps["options"];
        // only want to replace defaults for headers if they have been provided
        if ("headers" in proofSteps) {
            headers = proofSteps["headers"];
        }
        if ("available_header" in proofSteps) {
            available_header = proofSteps["available_header"];
        }
        index = proofSteps["index"];
        proofSteps = proofSteps["steps"];
    }

    // Convert proofSteps to an object if it is still a string (occurs when the proof steps comes from a Maxima variable)
    if (typeof proofSteps === "string") {
        proofSteps = _stackstring_objectify(proofSteps);
    }

    return [proofSteps, sortableUserOpts, headers, available_header, index, valid];
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
 * Validate the structure of Parson's JSON for proof steps.
 *
 * The function checks the structure of the provided Parson's JSON (`proofSteps`)
 * to ensure it follows specific patterns:
 * 1. If the JSON has depth 1, it should be a valid proofStep JSON (i.e., should have string values).
 * 2. If the JSON has depth 2, the top-level keys should be ["steps", "options"], and the value for "steps"
 *    should be a valid proofStep JSON. Options are not validated here.
 *
 * @param {Object} proofSteps - The Parson's JSON to be validated.
 * @returns {boolean} - Returns true if the provided Parsons JSON follows the expected structure, false otherwise.
 *
 * @example
 * // Returns true
 * _validate_parsons_JSON({
 *   "step1": "proof step 1",
 *   "step2": "proof step 2"
 * });
 *
 * @example
 * // Returns true
 * _validate_parsons_JSON({
 *   "steps": {
 *     "step1": "proof step 1",
 *     "step2": "proof step 2"
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
 *     "step1": "proof step 1",
 *     "step2": "proof step 2"
 *   }
 * });
 */
function _validate_parsons_JSON(proofSteps) {
    // If the JSON has depth 1 then it should be a valid proofStep JSON (i.e., should have string values)
    if (Object.values(proofSteps).every((val) => !(typeof(val) == 'object'))) {
        return _validate_proof_steps(proofSteps);
    }
    // Else the top-level of the JSON should have keys ["steps", "options"].
    // The value for "keys" should be a valid proofStep JSON
    // We do not validate options here
    if (Object.values(proofSteps).some((val) => typeof(val) == "object")) {
        if (JSON.stringify(Object.keys(proofSteps)) !== JSON.stringify(["steps", "options"])) {
            return false;
        }
        if (!_validate_proof_steps(proofSteps["steps"])) {
            return false;
        }
        return true;
    }
    // TO-DO : we are missing one case here in depth 2 case and unclear how to catch it:
    // if an author writes {"any string" : {#stackjson_stringify(proof_steps)#}},
    // then this should throw an error
}

/**
 * Validate the structure of proof steps.
 *
 * The function checks the structure of the provided proof steps (`proofSteps`)
 * to ensure that all values are strings.
 *
 * If the proof steps are provided as a Maxima variable (string of form '[["key", "value"], ...]'), they are converted
 * to a JSON object using the `_stackstring_objectify` function before validation.
 *
 * @param {string|Object} proofSteps - The proof steps to be validated. If a string,
 * it is assumed to be a Maxima variable and will be converted to a JSON object.
 * @returns {boolean} - Returns true if all values in the proof steps are strings, false otherwise.
 *
 * @example
 * // Returns true
 * _validate_proof_steps({
 *   step1: "Proof step 1",
 *   step2: "Proof step 2"
 * });
 *
 * @example
 * // Returns true
 * _validate_proof_steps('["step1", "Proof step 1"], ["step2", "Proof step 2"]]');
 *
 * @example
 * // Returns false
 * _validate_proof_steps({
 *   step1: "Proof step 1",
 *   step2: 123 // Not a string
 * });
 */
function _validate_proof_steps(proofSteps) {
    // Case when proof steps are coming from a Maxima variable: convert to a JSON
    if (typeof(proofSteps) == 'string') {
        proofSteps = _stackstring_objectify(proofSteps);
    }
    return Object.values(proofSteps).every((val) => typeof(val) == 'string');
}

function _validate_top_level_keys_JSON(JSON, validKeys, requiredKeys) {
    const keys = Object.keys(JSON);
    const missingRequiredKeys = requiredKeys.filter(key => !keys.includes(key));
    const invalidKeys = keys.filter(key => !validKeys.includes(key));
    return invalidKeys.length === 0 && missingRequiredKeys.length === 0;
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
    var newClass = usedList.className == 'list-group row' ? 'list-group col' : 'list-group row';
    usedList.setAttribute('class', newClass);
    availableList.setAttribute('class', newClass);
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

/**
 * Get the current height of the iframe's content document.
 *
 * @returns {number} - The height of the iframe's content document.
 */
export function get_iframe_height() {
    return document.documentElement.offsetHeight;
}

function _is_empty_li(li) {
    return li.textContent.trim() === '' && li.children.length === 0;
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
 * @param {boolean} clone - Flag indicating whether to clone elements during drag-and-drop.
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
     * @param {Object} proofSteps - Object containing proof steps.
     * @param {string} availableId - ID of the available list element.
     * @param {string} usedId - ID of the used list element.
     * @param {string|null} inputId - ID of the input element for storing state (optional).
     * @param {Object|null} options - Custom options for sortable lists
     *                                of form {used: UsedOptions, available: AvailableOptions} (optional).
     * @param {boolean} clone - Flag indicating whether to clone elements during sorting.
     */

    // TODO : add containerId as param
    // TODO : be careful with default parameters, these should all be strings
    constructor(proofSteps, inputId = null, options = null, clone = false, columns = 1, rows = null, orientation = "col", index = "", grid = false) {
        this.proofSteps = proofSteps;
        this.inputId = inputId;
        this.orientation = orientation;
        this.columns = (this.orientation === "col") ? columns : rows;
        this.rows = (this.orientation === "col") ? rows : columns;
        this.index = index;
        this.use_index = this.index !== "";
        this.grid = grid;
        this.itemCSS = this.grid ? 
            (this.orientation === "row" ? "grid-item-rigid" : "grid-item") : "list-group-item";
        // non-null InputId causes issues in grid version, I think this is required for saving state? 
        this.state = this._generate_state(this.proofSteps, null, Number(this.columns));
        if (inputId !== null) {
            this.input = document.getElementById(this.inputId);
            this.submitted = this.input.getAttribute("readonly") === "readonly"
        }
        this.ids = this._create_ids(this.rows, this.columns);
        this.availableId = this.ids.available;
        //this.available = document.getElementById(this.availableId);
        this.usedId = this.ids.used;
        //this.used = this.usedId.map(idList => idList.map(id => document.getElementById(id)));
        //this.used = document.getElementById(this.usedId);
        this.clone = clone;
        
        // TODO : additional default options?
        this.defaultOptions = {used: {animation: 50, cancel: ".header"}, available: {animation: 50, cancel: ".header"}};
        // Merges user options and default, overwriting default with user options if they clash
        this.userOptions = this._set_user_options(options);

        // Create overall options from this.userOptions by setting ghostClass and group to required options
        // and overwriting them if they appear in userOptions. This also disables the list if they have been 
        // submitted.
        this.options = this._set_ghostClass_group_and_disabled_options();
    }

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

    _flip_orientation() {
        var addClass = (this.orientation === "row") ? ["list-group", "col"] : ["row"];
        if (this.grid) {
            var removeClass = (this.orientation === "row") ? ["list-group", "row"] : ["list-group", "col"];
            var gridItems = document.querySelectorAll(".grid-item");
            gridItems.forEach((item) => {
                item.classList.remove("grid-item");
                item.classList.add("grid-item-rigid");
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
        
        /*var usedList = document.getElementById(usedId);
        var availableList = document.getElementById(availableId);
        var newClass = usedList.className == 'list-group row' ? 'list-group col' : 'list-group row';
        usedList.setAttribute('class', newClass);
        availableList.setAttribute('class', newClass);*/
    }

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
    /*if (orientation === "row") {
        var availableEl = document.getElementById("availableList");
        availableEl.classList.remove("list-group", "col");
        availableEl.classList.add(orientation);
        availableEl.parentNode.insertBefore(availableEl, availableList.parentNode.firstChild);
        //document.getElementById("availableList").classList.add(orientation);
        ids.used.forEach((idList) => {
            document.getElementById(idList[0]).classList.remove("list-group", "col");
            document.getElementById(idList[0]).classList.add(orientation);
        })
        if (use_index) {
            var indexEl = document.getElementById("index");
            indexEl.classList.remove("list-group", "col");
            indexEl.classList.add("row");
        }
    };*/

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
     * Generates the available list based on the current state.
     *
     * @method
     * @returns {void}
     */
    generate_available() {
        this.state.available.forEach(key => this.available.append(this._create_li(key)));
    }

    _add_index(index, indexDOM) {
        for (const [i, value] of index.entries()) {
            indexDOM.append(this._create_index(value, `usedIndex${i}`));
        }
    }

    /**
     * Generates the used list based on the current state.
     *
     * @method
     * @returns {void}
     */
    generate_used() {
        for (const [i, value] of this.used.entries()) {
            /*if (i === 0 && this.index !== null) {
                this._add_index(this.index, value[0]);        
            }*/
            this.state.used[i].forEach(key => value.append(this._create_li(key)));
        }
    }

    add_index(index) {
        for (const [i, value] of index.entries()) {
            var idx = this._create_index(value, `usedIndex_${i}`);
            if (i === 0) {
                var addClass = this.orientation === "col" ? "header" : "index";
                idx.classList.add(addClass);
            }
            document.getElementById("index").append(idx);
        }
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
            var header = this._create_header(value, `usedHeader_${i}`);
            parentEl.insertBefore(header, parentEl.firstChild);
        }
        var parentEl = document.getElementById("availableList");
        parentEl.insertBefore(this._create_header(available_header, "availableHeader"), parentEl.firstChild);
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
     * Adds double-click listeners to move items upon double-click and updates the state accordingly.
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
                this.used.append(li);
                this.update_state(newUsed, newAvailable);
            }
        });
        this.used.addEventListener('dblclick', (e) => {
            if (this._double_clickable(e.target)) {
                // get highest-level parent
                var li = this._get_moveable_parent_li(e.target);
                this.used.removeChild(li);
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
     * Generates the initial state of used and available items based on the provided proof steps and input ID.
     *
     * @method
     * @private
     * @param {Object} proofSteps - Object containing proof steps.
     * @param {string} inputId - ID of the input element for storing state.
     * @returns {Object} The initial state object with used and available lists.
     */
    _generate_state(proofSteps, inputId, columns) {
        let stateStore = document.getElementById(inputId);
        if (stateStore === null) {
            return {used: Array(columns).fill().map(() => []), available: [...Object.keys(proofSteps)]};
        }
        return (stateStore.value && stateStore.value != "") ?
            JSON.parse(stateStore.value) :
            {used: [], available: [...Object.keys(proofSteps)]};
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
     * Create an HTML list item element based on keys in `this.proofSteps`.
     *
     * @method
     * @private
     * @param {string} proofKey - The key associated with the proof content in 'proofSteps'.
     * @returns {HTMLElement} - The created list item element.
     */
    _create_li(proofKey) {
        let li = document.createElement("li");
        li.innerHTML = this.proofSteps[proofKey];
        li.setAttribute("data-id", proofKey);
        li.className = this.itemCSS;
        return li;
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
        var addClass = (this.orientation === "col") ? 
            [this.itemCSS, 'header'] : [this.itemCSS, 'index'];
        i.classList.add(...addClass);
        i.setAttribute("id", id);
        return i;
    }

    _create_index(innerHTML, id) {
        let i = document.createElement("i");
        i.innerHTML = innerHTML;
        var addClass = (this.orientation === "col") ? 
            [this.itemCSS, 'index'] : [this.itemCSS, 'header'];
        i.classList.add(...addClass);
        i.setAttribute("id", id);
        return i;
    }

    /**
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

    _deletable_li(li) {
        return !li.matches(".header") && !li.matches(".index") && !_is_empty_li(li);
    }

    _delete_li(li) {
        li.parentNode.removeChild(li);
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

};

export default {stack_sortable};
