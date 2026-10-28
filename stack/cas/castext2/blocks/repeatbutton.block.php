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

    /**
     * Set for a button inside a repeat block: its rows are added by the JS of that block's button.
     * @var bool
     */
    public $nested = false;

    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function compile($format, $options): ?MP_Node {
        if ($this->nested) {
            return $this->compile_nested();
        }

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
            $list[] = new MP_String("', id: " . json_encode($id) . ", suffix: " . json_encode('_repeat_' . $id . '_') . "},\n");
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

// VLE field names look like "<prefix>_<inputname>" and the prefix contains no "_",
// so everything after the first "_" is the input name (which may contain "_").
function input_name(fieldname) {
    return fieldname.substring(fieldname.indexOf('_') + 1);
}

// The repeated input an element belongs to, and for an input consisting of several
// elements the key of this element's field: the cells of a matrix are "<name>_sub_<i>_<j>",
// the boxes of a checkbox input "<name>_<key>" with value "<key>".
function input_field(el) {
    const name = input_name(el.name);
    const cell = name.match(/^(.+)_(sub_[0-9]+_[0-9]+)$/);
    if (el.dataset.stackInputType === 'matrix' && cell) {
        return {name: cell[1], key: cell[2]};
    }
    if (el.type === 'checkbox' && name.endsWith('_' + el.value)) {
        return {name: name.substring(0, name.length - el.value.length - 1), key: el.value};
    }
    return {name: name, key: null};
}

// Inputs consisting of a single element can be repeated: text boxes, dropdowns and text areas.
// So can radio buttons: all the buttons with the same name together are one input.  The cells
// of a matrix and the boxes of a checkbox input are fields of one input, see input_field().
function is_repeated_input(el) {
    if (el.tagName === 'INPUT') {
        return !['hidden', 'button', 'submit'].includes(el.type);
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
    } else if (el.type === 'checkbox') {
        // A ticked box has its key as the value of its field.
        if (value !== '') {
            el.setAttribute('checked', 'checked');
        } else {
            el.removeAttribute('checked');
        }
    } else if (el.type === 'radio') {
        if (el.value === value) {
            el.setAttribute('checked', 'checked');
        } else {
            el.removeAttribute('checked');
        }
    } else {
        el.setAttribute('value', value);
    }
}

// Rows are never re-rendered once shown: the VLE binds its change listeners to
// the actual elements, so replacing an existing row would silently disconnect
// it.  Instead each row is written into an empty "slot" element, and brings
// along the empty slot for the next row.  Row 1 goes into the container itself.
function slot_id(container, n) {
    return n === 1 ? container : container + '_slot_' + n;
}

// The value in the state of an input in a row: path holds the row (0-based), and for an input
// in a nested repeat block also the row of that block.
function state_value(state, name, path) {
    let value = state.data[name];
    path.forEach((i) => {
        value = Array.isArray(value) ? value[i] : undefined;
    });
    return value;
}

// The number of rows of nested repeat block q in the row of the outer block given by path.
function nested_count(q, state, path) {
    let count = 0;
    q.inputs.forEach((name) => {
        const rows = state_value(state, name, path);
        if (Array.isArray(rows)) {
            count = Math.max(count, rows.length);
        }
    });
    return count;
}

// Build the HTML for row n of repeat block def from its template.  Every id (and input name)
// gets the row specific suffix to keep them unique on the page.  The rows of the nested repeat
// blocks of def are built into their containers.  Path gives the row in the state, see state_value().
// Indices gives the rows of the enclosing repeat blocks by their ids, for the placeholders of their numbers.
function make_row(def, n, suffix, state, path, indices) {
    indices = Object.assign({}, indices, {[def.id]: n});
    const tmp = document.createElement('div');
    tmp.innerHTML = def.html.split(REPEATINDEX).join(String(n)).replace(/%%REPEATINDEX:([^%]*)%%/g,
        (placeholder, id) => (id in indices ? String(indices[id]) : placeholder));
    const added = [];
    const valids = Object.values(def.valids);
    // Old id => new id, to keep labels attached to their elements.
    const renamed = {};
    tmp.querySelectorAll('[id]').forEach((el) => {
        if (valids.includes(el.id)) {
            // Validation of the repeated fields is filled in by show_feedback(), inside this
            // element, which is just a neutral container (so that it is never hidden as "empty").
            el.className = '';
            el.innerHTML = '';
        }
        let newid = el.id + suffix;
        if (is_repeated_input(el)) {
            const field = input_field(el);
            let value = state_value(state, field.name, path);
            if (field.key !== null) {
                value = (value && typeof value === 'object') ? value[field.key] : undefined;
            }
            set_initial_value(el, value !== undefined ? String(value) : '');
            const vlename = input_name(el.name) + suffix;
            el.name = el.name + suffix;
            if (el.type === 'radio') {
                // The VLE finds a group of radio buttons by the id "<name>_1" of its first button.
                newid = el.name + '_' + el.value;
            }
            if (!added.some((entry) => entry.vlename === vlename)) {
                added.push({vlename: vlename, name: field.name, key: field.key, path: path,
                    checkbox: el.type === 'checkbox'});
            }
        }
        renamed[el.id] = newid;
        el.id = newid;
    });
    tmp.querySelectorAll('label[for]').forEach((label) => {
        if (label.htmlFor in renamed) {
            label.htmlFor = renamed[label.htmlFor];
        }
    });
    const buttons = [];
    def.nested.forEach((q) => {
        const container = tmp.querySelector('[id="' + q.container + suffix + '"]');
        const count = nested_count(q, state, path);
        let html = '';
        for (let m = 1; m <= count; m++) {
            const row = make_row(q, m, suffix + q.suffix + m, state, path.concat([m - 1]), indices);
            html += row.html;
            row.added.forEach((entry) => added.push(entry));
        }
        if (container) {
            container.innerHTML = count === 0 ? '' :
                html + '<div id="' + slot_id(q.container + suffix, count + 1) + '"></div>';
        }
        if (q.button !== null && !buttons.includes(q.button + suffix)) {
            buttons.push(q.button + suffix);
        }
    });
    return {html: tmp.innerHTML, added: added, buttons: buttons};
}

// The template is not typeset, so let the VLE's maths filter typeset the copy.
function typeset(html) {
    return '<div class="filter_mathjaxloader_equation">' + html + '</div>';
}

// Write a single repeated input back into its slot of the state, whenever it changes.
function connect_input(entry) {
    stack_js.request_access_to_input(entry.vlename, true).then((id) => {
        const input = document.getElementById(id);
        const writeback = () => {
            // Re-read the state, other inputs may have changed it in the meantime.
            const state = read_state();
            if (!state.data) {
                return;
            }
            const rows = state_value(state, entry.name, entry.path.slice(0, -1));
            const idx = entry.path[entry.path.length - 1];
            if (!Array.isArray(rows)) {
                return;
            }
            if (entry.key === null) {
                if (rows[idx] === input.value) {
                    return;
                }
                rows[idx] = input.value;
            } else {
                // The fields of an input consisting of several elements, e.g. the cells of a matrix.
                const fields = (rows[idx] && typeof rows[idx] === 'object') ? rows[idx] : {};
                // The VLE reports whether a checkbox is ticked, its field has the key if it is.
                let value = input.value;
                if (entry.checkbox) {
                    value = value === 'true' ? entry.key : '';
                }
                if ((fields[entry.key] || '') === value) {
                    return;
                }
                fields[entry.key] = value;
                rows[idx] = fields;
            }
            write_state(state);
        };
        input.addEventListener('change', writeback);
        // Registration is asynchronous, so the student may already have typed something.
        writeback();
    });
}

// The buttons of the nested repeat blocks, in row n of block r, add rows to those blocks.
function connect_buttons(r, n, buttons) {
    buttons.forEach((button) => {
        stack_js.register_external_button_listener(button, () => {
            const blocks = r.nested.filter((q) => q.button + r.suffix + n === button);
            queue = queue.then(() => add_nested(r, n, blocks)).catch((e) => console.error('STACK repeatbutton:', e));
        });
    });
}

// The inputs of repeat block r, including those of its nested repeat blocks.
function all_inputs(r) {
    let names = r.inputs;
    r.nested.forEach((q) => {
        names = names.concat(q.inputs);
    });
    return names;
}

// Number of rows currently recorded in the state for block r.
function row_count(r, state) {
    let count = 0;
    all_inputs(r).forEach((name) => {
        if (state.data && Array.isArray(state.data[name])) {
            count = Math.max(count, state.data[name].length);
        }
    });
    return count;
}

// Add one row to every block controlled by this button, with one row in each nested block.
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
        r.nested.forEach((q) => {
            q.inputs.forEach((name) => {
                state.data[name] = state.data[name] || [];
                while (state.data[name].length < n) {
                    state.data[name].push(['']);
                }
            });
        });
        r.rows = n;
        return [r, n];
    });
    write_state(state);
    rows.forEach(([r, n]) => {
        const row = make_row(r, n, r.suffix + n, state, [n - 1], {});
        stack_js.switch_content(slot_id(r.container, n),
            typeset(row.html) + '<div id="' + slot_id(r.container, n + 1) + '"></div>');
        row.added.forEach(connect_input);
        connect_buttons(r, n, row.buttons);
    });
}

// Add one row to the nested repeat blocks in row n of block r.
function add_nested(r, n, blocks) {
    if (state_input.hasAttribute('readonly')) {
        return;
    }
    const state = read_state();
    const rows = blocks.map((q) => {
        const m = nested_count(q, state, [n - 1]) + 1;
        q.inputs.forEach((name) => {
            state.data[name] = state.data[name] || [];
            while (state.data[name].length < n) {
                state.data[name].push([]);
            }
            if (!Array.isArray(state.data[name][n - 1])) {
                state.data[name][n - 1] = [];
            }
            while (state.data[name][n - 1].length < m) {
                state.data[name][n - 1].push('');
            }
        });
        return [q, m];
    });
    write_state(state);
    rows.forEach(([q, m]) => {
        const container = q.container + r.suffix + n;
        const row = make_row(q, m, r.suffix + n + q.suffix + m, state, [n - 1, m - 1], {[r.id]: n});
        stack_js.switch_content(slot_id(container, m),
            typeset(row.html) + '<div id="' + slot_id(container, m + 1) + '"></div>');
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
        const rows = [];
        for (let n = 1; n <= count; n++) {
            const row = make_row(r, n, r.suffix + n, state, [n - 1], {});
            html += typeset(row.html);
            rows.push(row);
        }
        stack_js.switch_content(r.container, html + '<div id="' + slot_id(r.container, count + 1) + '"></div>');
        rows.forEach((row, i) => {
            row.added.forEach(connect_input);
            connect_buttons(r, i + 1, row.buttons);
        });
    });
}

// Show the feedback for one field in its validation element.
function update_feedback(id, cls, html) {
    const fb = {cls: cls, html: html || ''};
    // Always rewrite shown feedback, it may have been greyed out meanwhile.
    if (fb.html !== '' || (shown_feedback[id] && shown_feedback[id].html !== '')) {
        shown_feedback[id] = fb;
        stack_js.switch_content(id, feedback_html(fb, false));
    }
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
        const state = read_state();
        REPEATS.forEach((r) => {
            for (let n = 1; n <= r.rows; n++) {
                r.inputs.forEach((name) => {
                    if (name in r.valids) {
                        update_feedback(r.valids[name] + r.suffix + n, r.valclass[name],
                            feedback[name] && feedback[name][n]);
                    }
                });
                r.nested.forEach((q) => {
                    const count = nested_count(q, state, [n - 1]);
                    for (let m = 1; m <= count; m++) {
                        q.inputs.forEach((name) => {
                            if (name in q.valids) {
                                update_feedback(q.valids[name] + r.suffix + n + q.suffix + m, q.valclass[name],
                                    feedback[name] && feedback[name][n] && feedback[name][n][m]);
                            }
                        });
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
        all_inputs(r).forEach((name) => {
            data[name] = [];
        });
    });
    write_state({data: data});
    add_repeat();
}

// Find the repeated inputs of a repeat block, and their validation elements, in its template.
// A nested repeat block is taken out of the template, and described the same way in def.nested,
// with the ids of its container and of its button.
function parse_template(def, html) {
    const tmp = document.createElement('div');
    tmp.innerHTML = html;
    def.nested = [];
    tmp.querySelectorAll('[data-stack-repeat]').forEach((el) => {
        const id = el.dataset.stackRepeat;
        const container = tmp.querySelector('[data-stack-repeat-container="' + id + '"]');
        const button = tmp.querySelector('[data-stack-repeat-button~="' + id + '"]');
        const q = {
            id: id,
            suffix: '_repeat_' + id + '_',
            container: container ? container.id : '',
            button: button ? button.id : null,
        };
        el.remove();
        parse_template(q, el.innerHTML);
        def.nested.push(q);
    });
    def.html = tmp.innerHTML;
    const elements = [...tmp.querySelectorAll('[id]')];
    def.inputs = [];
    def.valids = {};
    def.valclass = {};
    elements.filter(is_repeated_input).forEach((el) => {
        const name = input_field(el).name;
        if (def.inputs.includes(name)) {
            // Another button of the same group of radio buttons, or another cell of a matrix.
            return;
        }
        const prefix = el.name.substring(0, el.name.indexOf('_') + 1);
        def.inputs.push(name);
        state_val_id = prefix + SAVE_STATE + '_val';
        // The [[validation:name]] element of this input, if the template has one.
        const val = elements.find((v) => v.id === prefix + name + '_val');
        if (val) {
            def.valids[name] = val.id;
            def.valclass[name] = [...val.classList].filter(
                (c) => !['empty', 'waiting', 'loading', 'error'].includes(c)).join(' ');
        }
    });
}

const ready = Promise.all([
    stack_js.request_access_to_input(SAVE_STATE, true).then((id) => {
        state_input = document.getElementById(id);
    }),
    ...REPEATS.map((r) => stack_js.get_content(r.template).then((html) => {
        parse_template(r, html);
        r.rows = 0;
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
     * A button inside a repeat block is just a button, copied with each row of that block.  The JS
     * of the outer block's button finds it by data-stack-repeat-button and adds the rows.
     * @return MP_Node
     */
    private function compile_nested(): MP_Node {
        $ids = implode(' ', $this->get_repeat_ids());
        return new MP_List([
            new MP_String('%root'),
            new MP_String('<button type="button" class="btn btn-secondary" data-stack-repeat-button="' .
                htmlspecialchars($ids) . '" id="'),
            new MP_List([new MP_String('quid'), new MP_String('repeatbutton_' . str_replace(' ', '_', $ids))]),
            new MP_String('">' . $this->params['title'] . '</button>'),
        ]);
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
