/**
 * This is a library for SortableJS functionality used to generate STACK Parsons blocks.
 * 
 * @copyright  2023 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const stack_sortable = class {

    constructor(state, inputId, availableId, options = {animation: 50}) {
        this.state = state;
        this.inputId = inputId;
        this.input = document.getElementById(this.inputId);
        this.availableId = availableId;
        this.available = document.getElementById(this.availableId);
        this.options = {...{ghostClass: "list-group-item-info", group: "shared"}, ...options};
    }
    
    generate_available(proofSteps) {
        for (const key in this.state.available) {
            let li = document.createElement("li");
            li.innerText = proofSteps[this.state.available[key]];
            li.setAttribute("data-id", key);
            li.className = "list-group-item";
            this.available.append(li);
        };
        this.input.value = JSON.stringify(this.state);
        this.input.dispatchEvent(new Event("change"));
    }

    update_state(newUsed, newAvailable) {
        var newState = {used: [], available: []};
        newState.used = newUsed.toArray();
        newState.available = newAvailable.toArray();
        this.input.value = JSON.stringify(newState);
        this.input.dispatchEvent(new Event('change'));
        this.state = newState;
    }
};

export default {stack_sortable};
