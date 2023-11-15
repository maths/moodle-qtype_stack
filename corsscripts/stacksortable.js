/**
 * This is a library for SortableJS functionality used to generate STACK Parsons blocks.
 * 
 * @copyright  2023 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export function preprocess_steps(proofSteps, userOpts) {
    if (typeof proofSteps === "string") {
        proofSteps = Object.fromEntries(new Map(Object.values(JSON.parse(proofSteps))));
    };
    if (JSON.stringify(Object.keys(proofSteps)) === JSON.stringify([ "steps", "options" ])) {
        userOpts = proofSteps["options"];
        proofSteps = proofSteps["steps"];
    };
    if (typeof proofSteps === "string") {
        proofSteps = Object.fromEntries(new Map(Object.values(JSON.parse(proofSteps))));
    };

    return [proofSteps, userOpts];
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
        // Design for options:
        // options = {used : {// options for the used list}, available : {// options for the available list}}
        // TODO : additional default options?
        this.defaultOptions = {used: {animation: 50}, available: {animation: 50}};
        if (options == null) {
            this.userOptions = this.defaultOptions;
        } else {
            // TODO: if options is flat object assign same to both used available, else index into options.used and options.available
            this.userOptions = {used: Object.assign(this.defaultOptions.used, options.used), available: Object.assign(this.defaultOptions.available, options.available)};
        };
        // define group correctly based on clone
        this.clone = clone
        var group_val = {used: {name: "sortableUsed", pull: true, put: true}};
        group_val.available = (clone === "true") ? {name: "sortableAvailable", pull: "clone", revertClone: true, put: false} : {name: "sortableAvailable", pull: true, put: true};
        // Do not allow a user to replace ghostClass or group.
        this.options = {used: Object.assign(this.userOptions.used, {ghostClass: "list-group-item-info", group: group_val.used}), 
                        available : Object.assign(this.userOptions.available, {ghostClass: "list-group-item-info", group: group_val.available})};
    }

    _generate_state(proofSteps, inputId) {
        let stateStore = document.getElementById(inputId);
        if (stateStore == null) {
            return {used: [], available: [...Object.keys(proofSteps)]}
        }
        return stateStore.value && stateStore.value != "" ? JSON.parse(stateStore.value) : {used: [], available: [...Object.keys(proofSteps)]};
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

    update_state(newUsed, newAvailable) {
        var newState = {used: newUsed.toArray(), available: newAvailable.toArray()};
        if (this.inputId != null) {
            this.input.value = JSON.stringify(newState);
            this.input.dispatchEvent(new Event('change'));
        };
        this.state = newState;
    }

    update_state_dblclick(newUsed, newAvailable) {
        var availableLi = this.available.getElementsByClassName("list-group-item");
        for (var i = 0; i < availableLi.length; i++) {
            availableLi[i].addEventListener('dblclick', (e) => {
                if (e.target.parentNode.id == this.availableId) {
                    if (this.clone === "true") {
                        this.used.append(e.target.cloneNode(true));
                    } else {
                        var li = this.available.removeChild(e.target);
                        this.used.append(li);
                    }
                }
                else if (e.target.parentNode.id == this.usedId) {
                    if (this.clone !== "true") {
                        var li = this.used.removeChild(e.target);
                        this.available.prepend(li);
                    }
                }
                this.update_state(newUsed, newAvailable);
            });
        }
    }
};

export default {stack_sortable};
