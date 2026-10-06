<?php
// This file is part of STACK
//
// STACK is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// STACK is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stateful.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This class adds in the interactive repeat button blocks to castext.
 * @package    qtype_stack
 * @copyright  2025 University of Edinburgh.
 * @copyright  2025 Ruhr University Bochum.
 * @copyright  2025 ETH Zürich.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/repeat.block.php');
// Register a counter.
require_once(__DIR__ . '/iframe.block.php');
stack_cas_castext2_iframe::register_counter('///REPEATBUTTON_COUNT///');

/**
 * This class adds in the repeat button blocks to castext.
 */
class stack_cas_castext2_repeatbutton extends stack_cas_castext2_block {

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {

        // All reveals need unique (at request level) identifiers, we use running numbering.
        static $count = 0;

        $body = new MP_List([new MP_String('%root')]);

        // This should have enough randomness to avoid collisions.
        $uid = '' . rand(100, 999) . time() . '_' . $count;
        $count = $count + 1;

        $buttonid = 'stack-repeatbutton-' . $uid;
        $body->items[] = new MP_String('<button type="button" class="btn btn-secondary" id="' .
            $buttonid . '">' . $this->params['title'] . '</button>');

        $list = [];
        $list[] = new MP_String('script');
        $list[] = new MP_String(json_encode(['type' => 'module']));

        $stackjsurl = json_encode(stack_cors_link('stackjsiframe.min.js'), JSON_UNESCAPED_SLASHES);
        $savestate = json_encode($this->params['save_state']);
        $buttonid = json_encode($buttonid);

        $list[] = new MP_String("import {stack_js} from {$stackjsurl};\n" .
            "const SAVE_STATE = {$savestate};\n" .
            "const BUTTON_ID = {$buttonid};\n" .
            "const REPEATINDEX = " . json_encode(stack_cas_castext2_repeat::REPEATINDEX) . ";\n" .
            "// One entry per [[repeat]] block controlled by this button.\n" .
            "const REPEATS = [\n");
        foreach ($this->get_repeat_ids() as $id) {
            $list[] = new MP_String("{template: '");
            $list[] = new MP_List([new MP_String('quid'), new MP_String('repeat_' . $id)]);
            $list[] = new MP_String("', container: '");
            $list[] = new MP_List([new MP_String('quid'), new MP_String('repeatcontainer_' . $id)]);
            $list[] = new MP_String("', suffix: " . json_encode('_repeat_' . $id . '_') . "},\n");
        }
        $list[] = new MP_String("];\n");

        $list[] = new MP_String(<<<'JS'
// The (iframe side mirror of the) input holding the JSON state.
let state_input = null;
// The id of the validation element of that input, where the per-field feedback arrives.
let state_val_id = null;
// Element id => {cls, html} currently shown in that per-field validation element.
const shown_feedback = {};

function feedback_html(fb, waiting) {
    if (fb.html === '') {
        return '';
    }
    return '<div class="' + fb.cls + (waiting ? ' waiting' : '') + '">' + fb.html + '</div>';
}

// While the state is being validated, grey out the feedback, as for normal inputs.
function mark_feedback_waiting() {
    Object.keys(shown_feedback).forEach((id) => {
        if (shown_feedback[id].html !== '') {
            stack_js.switch_content(id, feedback_html(shown_feedback[id], true));
        }
    });
}

function read_state() {
    return JSON.parse(state_input.value);
}

function write_state(state) {
    state_input.value = JSON.stringify(state);
    state_input.dispatchEvent(new Event('change'));
}

// VLE ids look like "<prefix>_<inputname>" and the prefix contains no "_",
// so everything after the first "_" is the input name (which may contain "_").
function input_name(vleid) {
    return vleid.substring(vleid.indexOf('_') + 1);
}

// Inputs consisting of a single element can be repeated: text boxes, dropdowns and text areas.
function is_repeated_input(el) {
    if (el.tagName === 'INPUT') {
        return !['hidden', 'radio', 'checkbox', 'button', 'submit'].includes(el.type);
    }
    return el.tagName === 'SELECT' || el.tagName === 'TEXTAREA';
}

// Set the initial value of a copy of an input, in the markup (not just the live value).
function set_initial_value(el, value) {
    if (el.tagName === 'SELECT') {
        el.querySelectorAll('option').forEach((option) => {
            if (option.value === value) {
                option.setAttribute('selected', 'selected');
            } else {
                option.removeAttribute('selected');
            }
        });
    } else if (el.tagName === 'TEXTAREA') {
        el.textContent = value;
    } else {
        el.setAttribute('value', value);
    }
}

// Rows are never re-rendered once shown: the VLE binds its change listeners to
// the actual elements, so replacing an existing row would silently disconnect
// it.  Instead each row is written into an empty "slot" element, and brings
// along the empty slot for the next row.  Row 1 goes into the container itself.
function slot_id(r, n) {
    return n === 1 ? r.container : r.container + '_slot_' + n;
}

// Build the HTML for row n of repeat block r from its template.  Every id (and
// input name) gets a row specific suffix to keep them unique on the page.
function make_row(r, n, state) {
    const tmp = document.createElement('div');
    tmp.innerHTML = r.html.split(REPEATINDEX).join(String(n));
    const added = [];
    const valids = Object.values(r.valids);
    tmp.querySelectorAll('[id]').forEach((el) => {
        if (valids.includes(el.id)) {
            // Validation of the repeated fields is filled in by show_feedback(), inside this
            // element, which is just a neutral container (so that it is never hidden as "empty").
            el.className = '';
            el.innerHTML = '';
        }
        if (is_repeated_input(el)) {
            const name = input_name(el.id);
            const values = state.data[name] || [];
            set_initial_value(el, values[n - 1] !== undefined ? String(values[n - 1]) : '');
            el.name = el.name + r.suffix + n;
            added.push({vlename: name + r.suffix + n, name: name, idx: n - 1});
        }
        el.id = el.id + r.suffix + n;
    });
    // The template is not typeset, so let the VLE's maths filter typeset the copy.
    return {html: '<div class="filter_mathjaxloader_equation">' + tmp.innerHTML + '</div>', added: added};
}

// Write a single repeated input back into its slot of the state, whenever it changes.
function connect_input(entry) {
    stack_js.request_access_to_input(entry.vlename, true).then((id) => {
        const input = document.getElementById(id);
        const writeback = () => {
            // Re-read the state, other inputs may have changed it in the meantime.
            const state = read_state();
            if (!state.data || !Array.isArray(state.data[entry.name]) ||
                    state.data[entry.name][entry.idx] === input.value) {
                return;
            }
            state.data[entry.name][entry.idx] = input.value;
            write_state(state);
        };
        input.addEventListener('change', writeback);
        // Registration is asynchronous, so the student may already have typed something.
        writeback();
    });
}

// Number of rows currently recorded in the state for block r.
function row_count(r, state) {
    let count = 0;
    r.inputs.forEach((name) => {
        if (state.data && Array.isArray(state.data[name])) {
            count = Math.max(count, state.data[name].length);
        }
    });
    return count;
}

// Add one row to every block controlled by this button.
function add_repeat() {
    if (state_input.hasAttribute('readonly')) {
        return;
    }
    const state = read_state();
    const rows = REPEATS.map((r) => {
        const n = row_count(r, state) + 1;
        r.inputs.forEach((name) => {
            state.data[name] = state.data[name] || [];
            while (state.data[name].length < n) {
                state.data[name].push('');
            }
        });
        r.rows = n;
        return [r, n];
    });
    write_state(state);
    rows.forEach(([r, n]) => {
        const row = make_row(r, n, state);
        stack_js.switch_content(slot_id(r, n), row.html + '<div id="' + slot_id(r, n + 1) + '"></div>');
        row.added.forEach(connect_input);
    });
}

// Rebuild the rows recorded in an existing state, e.g. after a page reload.
function construct_repeat() {
    const state = read_state();
    REPEATS.forEach((r) => {
        const count = row_count(r, state);
        r.rows = count;
        if (count === 0) {
            return;
        }
        let html = '';
        let added = [];
        for (let n = 1; n <= count; n++) {
            const row = make_row(r, n, state);
            html += row.html;
            added = added.concat(row.added);
        }
        stack_js.switch_content(r.container, html + '<div id="' + slot_id(r, count + 1) + '"></div>');
        added.forEach(connect_input);
    });
}

// Copy the validation feedback for each repeated field, which the repeat input sends
// along with its own validation, next to the corresponding field.
function show_feedback() {
    if (state_val_id === null) {
        return Promise.resolve();
    }
    return stack_js.get_content(state_val_id).then((content) => {
        const tmp = document.createElement('div');
        tmp.innerHTML = content || '';
        const el = tmp.querySelector('.stack-repeat-feedback');
        let feedback = {};
        if (el) {
            if (el.dataset.state !== state_input.value) {
                // A late response for an older state, the current one is still on its way.
                return;
            }
            feedback = JSON.parse(el.dataset.feedback || '{}');
        }
        REPEATS.forEach((r) => {
            for (let n = 1; n <= r.rows; n++) {
                r.inputs.forEach((name) => {
                    if (!(name in r.valids)) {
                        return;
                    }
                    const id = r.valids[name] + r.suffix + n;
                    const fb = {
                        cls: r.valclass[name],
                        html: feedback[name] && feedback[name][n] ? feedback[name][n] : '',
                    };
                    // Always rewrite shown feedback, it may have been greyed out meanwhile.
                    if (fb.html !== '' || (shown_feedback[id] && shown_feedback[id].html !== '')) {
                        shown_feedback[id] = fb;
                        stack_js.switch_content(id, feedback_html(fb, false));
                    }
                });
            }
        });
    });
}

// Set up an empty state with one list per repeated input, and show the first row.
function init_repeat() {
    const data = {};
    REPEATS.forEach((r) => {
        r.inputs.forEach((name) => {
            data[name] = [];
        });
    });
    write_state({data: data});
    add_repeat();
}

const ready = Promise.all([
    stack_js.request_access_to_input(SAVE_STATE, true).then((id) => {
        state_input = document.getElementById(id);
    }),
    ...REPEATS.map((r) => stack_js.get_content(r.template).then((html) => {
        r.html = html;
        const tmp = document.createElement('div');
        tmp.innerHTML = html;
        const elements = [...tmp.querySelectorAll('[id]')];
        r.inputs = [];
        r.valids = {};
        r.valclass = {};
        r.rows = 0;
        elements.filter(is_repeated_input).forEach((el) => {
            const name = input_name(el.id);
            let prefix = el.id.substring(0, el.id.length - name.length);
            if (el.tagName === 'SELECT' && prefix.startsWith('menu')) {
                // Moodle prefixes the field name by "menu" to make the id of a select.
                prefix = prefix.substring(4);
            }
            r.inputs.push(name);
            state_val_id = prefix + SAVE_STATE + '_val';
            // The [[validation:name]] element of this input, if the template has one.
            const val = elements.find((v) => v.id === prefix + name + '_val');
            if (val) {
                r.valids[name] = val.id;
                r.valclass[name] = [...val.classList].filter(
                    (c) => !['empty', 'waiting', 'loading', 'error'].includes(c)).join(' ');
            }
        });
    })),
]).then(() => {
    if (state_input.value === '') {
        init_repeat();
    } else {
        construct_repeat();
    }
});

// Serialise additions, so that fast repeated clicks cannot target the same slot.
let queue = ready;

// Whenever validation of the state completes, update the feedback next to the fields.
stack_js.register_validation_state_listener(SAVE_STATE, (completed) => {
    if (completed) {
        queue = queue.then(show_feedback).catch((e) => console.error('STACK repeatbutton:', e));
    } else {
        mark_feedback_waiting();
    }
});
// After a page reload the validation is already there.
queue = queue.then(show_feedback);
stack_js.register_external_button_listener(BUTTON_ID, () => {
    queue = queue.then(add_repeat).catch((e) => console.error('STACK repeatbutton:', e));
});

JS);

        // Now add a hidden [[iframe]] with suitable scripts.
        $body->items[] = new MP_List([
            new MP_String('iframe'),
            new MP_String(json_encode([
                'hidden' => true,
                'title' => 'Logic container for a repeatbutton  ///REPEATBUTTON_COUNT///.',
            ])),
            new MP_List($list),
        ]);

        return $body;
    }

    /**
     * The ids of the repeat blocks this button controls.
     * @return string[]
     */
    public function get_repeat_ids(): array {
        if (!isset($this->params['repeat_ids'])) {
            return [];
        }
        return preg_split('/[\s;]+/', trim($this->params['repeat_ids']), -1, PREG_SPLIT_NO_EMPTY);
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function is_flat(): bool {
        return true;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function postprocess(array $params, castext2_processor $processor, castext2_placeholder_holder $holder): string {
        return 'Post processing of repeatbutton blocks never happens, this block is handled through [[iframe]].';
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate_extract_attributes(): array {
        $r = [];
        if (!isset($this->params['title'])) {
            return $r;
        }
        if (!isset($this->params['repeat_ids'])) {
            return $r;
        }
        if (!isset($this->params['save_state'])) {
            return $r;
        }
        return $r;
    }

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function validate(&$errors=[], $options=[]): bool {
        if (!array_key_exists('title', $this->params) || trim($this->params['title']) === '') {
            $errors[] = new $options['errclass']('Repeatbutton block requires a non-empty title parameter.', $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        if (!array_key_exists('repeat_ids', $this->params) || trim($this->params['repeat_ids']) === '') {
            $errors[] = new $options['errclass']('Repeatbutton block requires a non-empty repeat_ids parameter.',
                $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        if (!array_key_exists('save_state', $this->params) || trim($this->params['save_state']) === '') {
            $errors[] = new $options['errclass']('Repeatbutton block requires a non-empty save_state parameter.',
                $options['context'] . '/' .
                $this->position['start'] . '-' . $this->position['end']);
            return false;
        }
        return true;
    }

    /**
     * Is this an interactive block?
     * If true, we can't generate a static version.
     * @return bool
     */
    public function is_interactive(): bool {
        return true;
    }
}
