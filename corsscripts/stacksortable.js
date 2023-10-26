/**
 * This is a library for SortableJS functionality used to generate STACK Parsons blocks.
 * 
 * @copyright  2023 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const stack_sortable = {
    generate_available: function (proofSteps, state, availableId) {
        let availableList = document.getElementById(availableId);
        for (const key in state.available) {
            let li = document.createElement("li");
            li.innerText = proofSteps[state.available[key]];
            li.setAttribute("data-id", key);
            li.className = "list-group-item";
            availableList.append(li);
        };
    },

    update_state: function (newUsed, newAvailable) {
        var newState = {used: [], available: []};
        newState.used = newUsed.toArray();
        newState.available = newAvailable.toArray();
        state = newState;
    },

    options: {
            animation: 50,
            ghostClass: "list-group-item-info",
            group: "shared",
        },
    /*create: function(usedList, availableList) {
		var sortableUsed = Sortable.create(usedList, {
			animation: 50,
			ghostClass: 'list-group-item-info',
			group: 'shared', 
			onSort: () => { 
				this.update_state(sortableUsed, sortableAvailable);  
			},
		});
		var sortableAvailable = Sortable.create(availableList, {
			animation: 50,
			ghostClass: 'list-group-item-info',
			group: 'shared',
			onSort: () => { 
				this.update_state(sortableUsed, sortableAvailable);
			},
		});
    },*/
};

export default {stack_sortable};
