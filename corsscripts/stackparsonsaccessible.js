/**
 * Accessible editor for STACK Parsons blocks.
 *
 * This intentionally does not depend on SortableJS. It edits the same JSON
 * state as the drag-and-drop implementation so existing inputs and grading
 * continue to work.
 *
 * @package    qtype_stack
 * @copyright  2026 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

"use strict";

export function preprocess_steps(steps, sortableUserOpts, headers, available_header, index) {
    if (typeof steps === "string") {
        steps = _stackstring_objectify(steps);
    }

    var valid = _validate_parsons_JSON(steps);
    if (_validate_top_level_keys_JSON(steps, ["steps", "options", "headers", "index", "available_header"], ["steps"])) {
        sortableUserOpts = {used: steps["options"], available: steps["options"]};
        if ("headers" in steps) {
            headers = steps["headers"];
        }
        if ("available_header" in steps) {
            available_header = steps["available_header"];
        }
        index = steps["index"];
        steps = steps["steps"];
    }

    if (typeof steps === "string") {
        steps = _stackstring_objectify(steps);
    }

    return [steps, sortableUserOpts, headers, available_header, index, valid];
}

export function get_iframe_height() {
    return document.documentElement.offsetHeight;
}

function _stackstring_objectify(stackjson_array_string) {
    return Object.fromEntries(new Map(Object.values(JSON.parse(stackjson_array_string))));
}

function _validate_parsons_JSON(steps) {
    if (Object.values(steps).every((val) => !(typeof(val) == "object"))) {
        return _validate_flat_steps(steps);
    }
    if (Object.values(steps).some((val) => typeof(val) == "object")) {
        if (!_validate_top_level_keys_JSON(steps, ["steps", "options", "headers", "index", "available_header"], ["steps"])) {
            return false;
        }
        if (!_validate_flat_steps(steps["steps"])) {
            return false;
        }
        return true;
    }
    return false;
}

function _validate_flat_steps(steps) {
    if (typeof(steps) == "string") {
        steps = _stackstring_objectify(steps);
    }
    if (Object.keys(steps).every((key) => !isNaN(parseInt(key)))) {
        return false;
    }
    return Object.values(steps).every((val) => typeof(val) == "string");
}

function _validate_top_level_keys_JSON(JSON, validKeys, requiredKeys) {
    const keys = Object.keys(JSON);
    const missingRequiredKeys = requiredKeys.filter((key) => !keys.includes(key));
    const invalidKeys = keys.filter((key) => !validKeys.includes(key));
    return invalidKeys.length === 0 && missingRequiredKeys.length === 0;
}

export const stack_accessible_parsons = class stack_accessible_parsons {
    constructor(steps,
            inputId = null,
            clone = "false",
            columns = 1,
            rows = null,
            orientation = "col",
            index = "",
            grid = false,
            item_height = "",
            item_width = "",
            log = "false",
            headers = [],
            availableHeader = "",
            labels = {}) {
        this.steps = steps;
        this.inputId = inputId;
        this.input = inputId ? document.getElementById(inputId) : null;
        this.submitted = this.input && this.input.getAttribute("readonly") === "readonly";
        this.clone = clone;
        this.orientation = orientation;
        this.columns = ((this.orientation === "col") ? columns : rows);
        this.rows = ((this.orientation === "col") ? rows : columns);
        this.index = index;
        this.grid = grid === true || grid === "true";
        this.itemHeight = item_height;
        this.itemWidth = item_width;
        this.log = log;
        this.headers = headers;
        this.availableHeader = availableHeader;
        this.labels = Object.assign({
            add: "Add",
            remove: "Remove",
            move: "Move",
            moveup: "Move up",
            movedown: "Move down",
            destination: "Destination",
            available: "Available items",
            answer: "Answer",
            empty: "Empty",
            useaccessible: "Use keyboard accessible version",
            usedrag: "Use drag and drop version",
            added: "Item added.",
            removed: "Item removed.",
            moved: "Item moved.",
            noemptycells: "No empty cells are available.",
            addsection: "Add to your solution",
            ordersection: "Order your solution",
            reset: "Reset",
            additem: "Add: {item}",
            moveitem: "Move: {item}",
            removeitem: "Remove: {item}",
            moveupitem: "Move up: {item}",
            movedownitem: "Move down: {item}",
            destinationitem: "Destination for {item}",
            addeditem: "Added: {item}",
            moveditem: "Moved: {item}",
            moveditemtodestination: "Moved: {item} to {destination}",
            removeditem: "Removed: {item}"
        }, labels);
        this.state = this._generate_state();
        this.history = this.state;
    }

    render() {
        this._load_state_from_input();
        const container = document.getElementById("parsons-accessible-container");
        if (!container) {
            return;
        }
        container.replaceChildren();
        container.classList.add("stack-parsons-accessible");

        const live = document.createElement("div");
        live.id = "parsons-accessible-live";
        live.className = "stack-parsons-sr-only";
        live.setAttribute("aria-live", "polite");
        container.append(live);
        this.liveRegion = live;

        if (this.submitted) {
            container.append(this._render_readonly());
            this._typeset();
            this._notify_rendered();
            return;
        }

        container.append(this._render_available());
        container.append(this._render_answer());
        this._typeset();
        this._notify_rendered();
    }

    reset() {
        this.state = this._generate_state();
        this.history = this.state;
        if (this.input) {
            this.input.value = this.log === "false" ? JSON.stringify(this.state) : JSON.stringify(this.history);
            this.input.dispatchEvent(new Event("change"));
        }
        this.render();
        this.announce(this.labels.reset);
    }

    announce(message) {
        if (this.liveRegion) {
            this.liveRegion.textContent = "";
            window.setTimeout(() => {
                this.liveRegion.textContent = message;
            }, 0);
        }
    }

    _render_available() {
        const section = this._section(this.labels.addsection);
        const list = document.createElement("ol");
        list.className = "stack-parsons-accessible-list stack-parsons-available";

        this._current().available.forEach((key) => {
            const item = document.createElement("li");
            item.className = "stack-parsons-accessible-item";
            item.append(this._item_content(key));
            item.append(this._destination_form(key, null));
            list.append(item);
        });

        if (this._current().available.length === 0) {
            list.append(this._empty_item());
        }
        section.append(list);
        return section;
    }

    _render_answer() {
        const section = this._section(this.labels.ordersection);
        const answer = document.createElement("div");
        answer.className = this.rows === "" ? "stack-parsons-answer-lists" : "stack-parsons-answer-grid";

        for (let col = 0; col < Number(this.columns); col++) {
            const column = document.createElement("section");
            column.className = "stack-parsons-answer-column";
            if (!(this.rows === "" && Number(this.columns) === 1)) {
                const heading = document.createElement("h3");
                heading.innerHTML = this.headers[col] !== undefined ? this.headers[col] : String(col + 1);
                column.append(heading);
            }

            if (this.rows === "") {
                column.append(this._render_answer_list(col));
            } else {
                column.append(this._render_grid_column(col));
            }
            answer.append(column);
        }
        section.append(answer);
        return section;
    }

    _render_answer_list(col) {
        const list = document.createElement("ol");
        list.className = "stack-parsons-accessible-list stack-parsons-used";
        const items = this._current().used[col][0];

        items.forEach((key, row) => {
            const item = document.createElement("li");
            item.className = "stack-parsons-accessible-item";
            item.append(this._item_content(key));
            if (!this.submitted) {
                item.append(this._list_controls(key, col, row));
            }
            list.append(item);
        });

        if (items.length === 0) {
            list.append(this._empty_item());
        }
        return list;
    }

    _render_grid_column(col) {
        const list = document.createElement("ol");
        list.className = "stack-parsons-accessible-list stack-parsons-grid-column";

        for (let row = 0; row < Number(this.rows); row++) {
            const item = document.createElement("li");
            item.className = "stack-parsons-accessible-grid-cell";
            const cellLabel = document.createElement("h4");
            const rowLabel = this.index && this.index[row + 1] !== undefined ? this.index[row + 1] : String(row + 1);
            cellLabel.innerHTML = rowLabel;
            item.append(cellLabel);

            const keys = this._current().used[col][row];
            if (keys.length === 0) {
                item.append(this._empty_cell());
            } else {
                const key = keys[0];
                item.append(this._item_content(key));
                if (!this.submitted) {
                    item.append(this._grid_controls(key, col, row));
                }
            }
            list.append(item);
        }
        return list;
    }

    _destination_form(key, from) {
        const controls = document.createElement("div");
        controls.className = "stack-parsons-accessible-controls";

        const destinations = this._destinations(from);
        const button = document.createElement("button");
        button.type = "button";
        button.textContent = from === null ? this.labels.add : this.labels.move;
        button.setAttribute("aria-label", this._format(from === null ? this.labels.additem : this.labels.moveitem, {
            item: this._item_label(key)
        }));
        button.disabled = destinations.length === 0;

        if (destinations.length === 1) {
            button.addEventListener("click", () => this._place_item(key, from, destinations[0]));
            controls.append(button);
            return controls;
        }

        const select = document.createElement("select");
        select.setAttribute("aria-label", this._format(this.labels.destinationitem, {item: this._item_label(key)}));
        destinations.forEach((destination) => {
            const option = document.createElement("option");
            option.value = JSON.stringify(destination);
            option.textContent = destination.label;
            select.append(option);
        });

        button.addEventListener("click", () => {
            if (destinations.length === 0) {
                this.announce(this.labels.noemptycells);
                return;
            }
            this._place_item(key, from, JSON.parse(select.value));
        });

        controls.append(select, button);
        return controls;
    }

    _list_controls(key, col, row) {
        const controls = document.createElement("div");
        controls.className = "stack-parsons-accessible-controls";

        if (row !== 0) {
            controls.append(this._button(this.labels.moveup, () => this._move_in_list(col, row, row - 1), false,
                this._format(this.labels.moveupitem, {item: this._item_label(key)})));
        }
        if (row !== this._current().used[col][0].length - 1) {
            controls.append(this._button(this.labels.movedown, () => this._move_in_list(col, row, row + 1), false,
                this._format(this.labels.movedownitem, {item: this._item_label(key)})));
        }
        const remove = this._button(this.labels.remove, () => this._remove_from_list(col, row), false,
            this._format(this.labels.removeitem, {item: this._item_label(key)}));

        controls.append(remove);
        return controls;
    }

    _grid_controls(key, col, row) {
        const controls = document.createElement("div");
        controls.className = "stack-parsons-accessible-controls";
        controls.append(this._button(this.labels.remove, () => this._remove_from_grid(col, row), false,
            this._format(this.labels.removeitem, {item: this._item_label(key)})));
        return controls;
    }

    _destinations(from) {
        const destinations = [];
        if (this.rows === "") {
            for (let col = 0; col < Number(this.columns); col++) {
                const header = this._plain_text(this.headers[col] !== undefined ? this.headers[col] : String(col + 1));
                destinations.push({col: col, row: null, label: header});
            }
            return destinations;
        }

        for (let col = 0; col < Number(this.columns); col++) {
            const header = this._plain_text(this.headers[col] !== undefined ? this.headers[col] : String(col + 1));
            for (let row = 0; row < Number(this.rows); row++) {
                if (from && from.col === col && from.row === row) {
                    destinations.push({col: col, row: row, label: `${header}, ${row + 1}`});
                } else if (this._current().used[col][row].length === 0) {
                    destinations.push({col: col, row: row, label: `${header}, ${row + 1}`});
                }
            }
        }
        return destinations;
    }

    _place_item(key, from, destination) {
        const current = this._clone_current();
        const itemLabel = this._item_label(key);
        if (from === null) {
            if (this.clone !== "true") {
                const availableIndex = current.available.indexOf(key);
                if (availableIndex !== -1) {
                    current.available.splice(availableIndex, 1);
                }
            }
        } else if (this.rows === "") {
            current.used[from.col][0].splice(from.row, 1);
        } else {
            current.used[from.col][from.row] = [];
        }

        if (this.rows === "") {
            current.used[destination.col][0].push(key);
        } else {
            current.used[destination.col][destination.row] = [key];
        }
        const destinationLabel = destination && destination.label ? destination.label : "";
        const message = from === null ?
            this._format(this.labels.addeditem, {item: itemLabel, destination: destinationLabel}) :
            this._format(this.labels.moveditemtodestination, {item: itemLabel, destination: destinationLabel});
        this._commit(current, message);
    }

    _move_in_list(col, fromRow, toRow) {
        const current = this._clone_current();
        const list = current.used[col][0];
        const item = list.splice(fromRow, 1)[0];
        list.splice(toRow, 0, item);
        this._commit(current, this._format(this.labels.moveditem, {item: this._item_label(item)}));
    }

    _remove_from_list(col, row) {
        const current = this._clone_current();
        const key = current.used[col][0].splice(row, 1)[0];
        if (this.clone !== "true") {
            current.available.push(key);
        }
        this._commit(current, this._format(this.labels.removeditem, {item: this._item_label(key)}));
    }

    _remove_from_grid(col, row) {
        const current = this._clone_current();
        const key = current.used[col][row][0];
        current.used[col][row] = [];
        if (this.clone !== "true") {
            current.available.push(key);
        }
        this._commit(current, this._format(this.labels.removeditem, {item: this._item_label(key)}));
    }

    _commit(current, message) {
        const newState = [current, this._get_current_seconds()];
        if (JSON.stringify(newState[0]) !== JSON.stringify(this.history[0][0])) {
            this.history.unshift(newState);
        }
        this.state = [this.history[0]];
        if (this.input) {
            this.input.value = this.log === "false" ? JSON.stringify(this.state) : JSON.stringify(this.history);
            this.input.dispatchEvent(new Event("change"));
        }
        this.render();
        this.announce(message);
    }

    _load_state_from_input() {
        if (!this.input) {
            return;
        }
        if (!this.input.value) {
            this.state = this._generate_state();
            this.history = this.state;
            return;
        }
        try {
            this.history = JSON.parse(this.input.value);
            this.state = [this.history[0]];
        } catch (e) {
            this.state = this._generate_state();
            this.history = this.state;
        }
    }

    _generate_state() {
        const columns = Number(this.columns);
        const rows = Number(this.rows);
        const usedState = (rows === 0 || columns === 0) ?
                Array(columns).fill().map(() => [[]]) :
                Array(columns).fill().map(() => Array(rows).fill().map(() => []));
        return [[{used: usedState, available: [...Object.keys(this.steps)]}, this._get_current_seconds()]];
    }

    _current() {
        return this.state[0][0];
    }

    _clone_current() {
        return JSON.parse(JSON.stringify(this._current()));
    }

    _get_current_seconds() {
        return Math.floor(Date.now() / 1000);
    }

    _button(label, callback, disabled = false, ariaLabel = null) {
        const button = document.createElement("button");
        button.type = "button";
        button.textContent = label;
        button.disabled = disabled;
        if (ariaLabel !== null) {
            button.setAttribute("aria-label", ariaLabel);
        }
        button.addEventListener("click", callback);
        return button;
    }

    _section(headingText) {
        const section = document.createElement("section");
        section.className = "stack-parsons-accessible-section";
        const heading = document.createElement("h2");
        heading.innerHTML = headingText;
        section.append(heading);
        return section;
    }

    _item_content(key) {
        const content = document.createElement("div");
        content.className = "stack-parsons-accessible-content";
        content.innerHTML = this.steps[key];
        return content;
    }

    _empty_item() {
        const item = document.createElement("li");
        item.className = "stack-parsons-accessible-empty";
        item.textContent = this.labels.empty;
        return item;
    }

    _empty_cell() {
        const empty = document.createElement("p");
        empty.className = "stack-parsons-accessible-empty";
        empty.textContent = this.labels.empty;
        return empty;
    }

    _render_readonly() {
        const wrapper = document.createElement("div");
        wrapper.className = "stack-parsons-accessible-readonly";
        wrapper.append(this._render_answer());
        return wrapper;
    }

    _plain_text(html) {
        const div = document.createElement("div");
        div.innerHTML = html;
        return div.textContent || div.innerText || "";
    }

    _item_label(key) {
        return this._plain_text(this.steps[key]).replace(/\s+/g, " ").trim();
    }

    _format(template, replacements) {
        return template.replace(/\{([a-z]+)\}/g, (match, key) => {
            return Object.prototype.hasOwnProperty.call(replacements, key) ? replacements[key] : match;
        });
    }

    _typeset() {
        if (typeof MathJax === "undefined") {
            return;
        }
        const container = document.getElementById("parsons-accessible-container");
        if (MathJax.typesetPromise) {
            MathJax.typesetPromise([container]).then(() => this._notify_rendered());
        } else if (MathJax.Hub && MathJax.Hub.Queue) {
            MathJax.Hub.Queue(["Typeset", MathJax.Hub, container]);
            MathJax.Hub.Queue(() => this._notify_rendered());
        }
    }

    _notify_rendered() {
        if (typeof window.CustomEvent === "function") {
            window.dispatchEvent(new CustomEvent("stack-parsons-accessible-rendered"));
        } else {
            window.dispatchEvent(new Event("stack-parsons-accessible-rendered"));
        }
    }
};

export default {stack_accessible_parsons: stack_accessible_parsons};
