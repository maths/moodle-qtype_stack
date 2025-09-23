// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Module to export parts of the state and transform them to be used in templates
 * and as draggable data.
 *
 * @module     mod_nosferatu/local/intermediate/exporter
 * @class      mod_nosferatu/local/intermediate/exporter
 * @copyright  2022 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class {

    /**
     * Class constructor.
     * @param {Reactive} reactive the course editor object
     */
    constructor(reactive) {
        this.reactive = reactive;
    }

    /**
     * Generate the people export data from the state.
     *
     * @param {Object} state the current state.
     * @returns {Object}
     */
    metadata(state) {
        // Collect section information from the state.
        const data = {
            // State stores maps of key-values instead of simple arrays.
            people: [],
        };
        state.people.forEach(person => {
            data.people.push({...person});
        });
        data.haspeople = (data.people.length != 0);
        return data;
    }
}