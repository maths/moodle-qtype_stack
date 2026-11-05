/** @jest-environment jsdom */
const path = require('path');
const {loadAmdModule} = require('./loadAmdModule');
const load = () => loadAmdModule(path.resolve(__dirname, '../../amd/src/input_labels.js'), {});
function question(id, prefix) {
    return `<div id="${id}"><span data-stack-input-label="ans">Projection <math>P</math></span>
      <input data-stack-input-type="matrix" name="${prefix}ans_sub_0_1" id="${prefix}ans_sub_0_1"
        aria-label="Row 1, column 2" value="7">
      <span data-stack-input-label="choice">Number of solutions</span>
      <select name="${prefix}choice"><option>One</option></select></div>`;
}
test('matrix context and position; dropdown context; values and formula markup preserved', () => {
    document.body.innerHTML = question('q1','p:1_');
    load().init('q1','p:1_');
    const input = document.querySelector('input');
    const refs = input.getAttribute('aria-labelledby').split(' ').map(id => document.getElementById(id));
    expect(refs.map(el=>el.textContent)).toEqual(['Projection P','Row 1, column 2']);
    expect(refs[0].querySelector('math')).not.toBeNull();
    expect(input.value).toBe('7');
    expect(document.querySelector('select').getAttribute('aria-labelledby')).toBe('p:1_choice_label_1');
});
test('two questions with identical answer names remain isolated and repeated initialization is idempotent', () => {
    document.body.innerHTML = question('q1','p:1_') + question('q2','p:2_');
    const m=load();m.init('q1','p:1_');m.init('q2','p:2_');const html=document.body.innerHTML;
    m.init('q1','p:1_');m.init('q2','p:2_');expect(document.body.innerHTML).toBe(html);
    const ids=[...document.querySelectorAll('[id]')].map(el=>el.id);expect(new Set(ids).size).toBe(ids.length);
});
test('native and explicit labels and radio names are retained', () => {
    document.body.innerHTML = `<div id="q"><span data-stack-input-label="ans">New</span>
      <label for="x">Existing</label><input id="x" name="pans">
      <input name="pans" type="radio" aria-label="Formula">
      <select name="pans" aria-label="Explicit"><option>A</option></select></div>`;
    load().init('q','p');expect(document.querySelectorAll('[data-stack-label-managed]')).toHaveLength(0);
});
test('invalid label targets cannot affect unrelated input names', () => {
    document.body.innerHTML = `<div id="q"><span data-stack-input-label='ans"]'>Bad</span><input name="pans"></div>`;
    expect(()=>load().init('q','p')).not.toThrow();expect(document.querySelector('input').hasAttribute('aria-labelledby')).toBe(false);
});
