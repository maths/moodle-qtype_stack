/**
 * This is a library for SortableJS functionality used to generate STACK Parsons blocks.
 * 
 * @copyright  2023 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const stack_sortable = class {

    constructor(state, inputid, options = {animation: 50}) {
        this.state = state;
        this.inputid = inputid;
        this.options = {...{ghostClass: "list-group-item-info", group: "shared"}, ...options};
    }
    
    generate_available(proofSteps, availableId) {
        let availableList = document.getElementById(availableId);
        for (const key in this.state.available) {
            let li = document.createElement("li");
            li.innerText = proofSteps[this.state.available[key]];
            li.setAttribute("data-id", key);
            li.className = "list-group-item";
            availableList.append(li);
        };
    }

    update_state(newUsed, newAvailable) {
        var newState = {used: [], available: []};
        newState.used = newUsed.toArray();
        newState.available = newAvailable.toArray();
        let input = document.getElementById(this.inputid);
        input.dispatchEvent(new Event('change'));
        input.value = JSON.stringify(newState);
        this.state = newState;
    }
};

export default {stack_sortable};
