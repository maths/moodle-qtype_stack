/**
 * This is a library for SortableJS functionality used to generate STACK Parsons blocks.
 * 
 * @copyright  2023 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export function preprocess_steps(proofSteps, blockUserOpts, sortableUserOpts) {
    if (typeof proofSteps === "string") {
        proofSteps = Object.fromEntries(new Map(Object.values(JSON.parse(proofSteps))));
    };
    if (JSON.stringify(Object.keys(proofSteps)) === JSON.stringify([ "steps", "options" ])) {
        var userOpts = proofSteps["options"];
        proofSteps = proofSteps["steps"];

        // the only block option we currently support is 'header': ["used header", "available header"]
        if (userOpts.header != null) {
            blockUserOpts = {used: {header: userOpts.header[0]}, available: {header: userOpts.header[1]}};
        }
        
        // Sortable options are passed as a flat JSON, we split over used and available to allow different settings to be applied later
        delete userOpts.header;
        sortableUserOpts = {used: userOpts, available: userOpts};
    };
    if (typeof proofSteps === "string") {
        proofSteps = Object.fromEntries(new Map(Object.values(JSON.parse(proofSteps))));
    };

    return [proofSteps, blockUserOpts, sortableUserOpts];
}

function _flip_orientation() {
    var usedList = document.getElementById('usedList');
    var availableList = document.getElementById('availableList');
    var bin = document.getElementById('bin');
    var newClass = usedList.className == 'list-group row' ? 'list-group col' : 'list-group row';
    usedList.setAttribute('class', newClass);
    availableList.setAttribute('class', newClass);
    if (bin != null) {
        bin.setAttribute('class', newClass);
    }
}

export function add_orientation_listener() {
    const button = document.querySelector('button');
    button.addEventListener('click', () => _flip_orientation());
}

export function add_dblclick_listeners() {
    var items = document.getElementsByClassName("list-group-item");
    for (var i; i < items.length; i++) {
        items[i].addEventListener('dblclick');
    }
}

//function _resize_iframe_holder(e) {
//    document.getElementById("stack-iframe-holder-1").height = Number(e.data.height);
//}

function _get_iframe_height() {
    return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);
}

/*export function add_holder_resizer() {
    document.addEventListener('message', _resize_iframe_holder);
}*/

export function add_rescale_height_listener() {
    document.addEventListener('load', () => document.parentNode.postMessage({'height': _get_iframe_height()}));
}

export const stack_sortable = class {

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
        // Preprocessed options look like:
        // options = {used : {// options for the used list}, available : {// options for the available list}}
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

    _generate_state(proofSteps, inputId) {
        let stateStore = document.getElementById(inputId);
        if (stateStore == null) {
            return {used: [], available: [...Object.keys(proofSteps)]}
        }
        return (stateStore.value && stateStore.value != "") ? 
            JSON.parse(stateStore.value) : 
            {used: [], available: [...Object.keys(proofSteps)]};
    }

    generate_available() {
        for (const key in this.state.available) {
            let li = document.createElement("li");
            li.innerHTML = this.proofSteps[this.state.available[key]];
            li.setAttribute("data-id", this.state.available[key]);
            li.className = "list-group-item";
            this.available.append(li);
        };
    }

    generate_used() {
        for (const key in this.state.used) {
            let li = document.createElement("li");
            li.innerHTML = this.proofSteps[this.state.used[key]];
            li.setAttribute("data-id", this.state.used[key]);
            li.className = "list-group-item";
            this.used.append(li);
        };
    }

    _create_header(innerHTML, id) {
        let i = document.createElement("i");
        i.innerHTML = innerHTML;
        i.className = "list-group-item header";
        i.setAttribute("id", id);
        return i;
    }

    add_headers(headers) {
        this.used.append(this._create_header(headers.used.header, "usedHeader"));
        this.available.append(this._create_header(headers.available.header, "availableHeader"));
    }

    update_state(newUsed, newAvailable) {
        var newState = {used: newUsed.toArray(), available: newAvailable.toArray()};
        if (this.inputId != null) {
            this.input.value = JSON.stringify(newState);
            this.input.dispatchEvent(new Event('change'));
        };
        this.state = newState;
    }

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
};

export default {stack_sortable};
