/** @jest-environment jsdom */
import {stack_sortable} from '../../corsscripts/stacksortable.js';
import {stack_accessible_parsons} from '../../corsscripts/stackparsonsaccessible.js';
function setup({readonly=false,clone='false',columns='1',rows='',log='false',initial=''}={}) {
 document.body.innerHTML='<input id="answer"><div id="parsons-accessible-container"></div>';
 const input=document.getElementById('answer');input.value=initial;if(readonly)input.setAttribute('readonly','');
 const editor=new stack_accessible_parsons({a:'Let \\(n=2k+1\\).',b:'Expand \\(n^2\\).',c:'Conclude.'},'answer',clone,columns,rows,'col',[],rows!=='','','',log,['Column A','Column B'],'Available');
 editor.render();return {editor,input};
}
const buttons=()=>[...document.querySelectorAll('.stack-parsons-available button')];
const action=(item,text)=>[...item.querySelectorAll('button')].find(b=>b.textContent===text);
const answerItems=()=>[...document.querySelectorAll('.stack-parsons-used > li')];
test('keyboard add retains focus on the added step',()=>{
 const {input}=setup();buttons()[0].focus();buttons()[0].click();
 expect(document.activeElement.textContent).toBe('Remove');expect(document.activeElement.closest('.stack-parsons-used')).not.toBeNull();
 expect(JSON.parse(input.value)[0][0].used).toEqual([[['a']]]);
});
test('move retains focus at a boundary and boundary action cannot change the answer',()=>{
 const {input}=setup();buttons()[0].click();buttons()[0].click();
 const up=action(answerItems()[1],'Move up');up.focus();up.click();
 expect(document.activeElement.textContent).toBe('Move up');expect(document.activeElement.getAttribute('aria-disabled')).toBe('true');
 const before=input.value;document.activeElement.click();expect(input.value).toBe(before);
});
test('remove returns focus to the returned available step; live region is retained',()=>{
 setup();const live=document.querySelector('[aria-live]');buttons()[0].click();
 action(answerItems()[0],'Remove').click();
 expect(document.activeElement.textContent).toBe('Add');expect(document.activeElement.closest('.stack-parsons-available')).not.toBeNull();
 expect(document.querySelector('[aria-live]')).toBe(live);
});
test('boolean readonly blocks reset and mutations',()=>{
 const initial=JSON.stringify([[{used:[[['a']]],available:['b','c']},1]]);
 const {editor,input}=setup({readonly:true,initial});editor.reset();editor._move_in_list(0,0,0);
 expect(input.value).toBe(initial);expect(document.querySelectorAll('#parsons-accessible-container button')).toHaveLength(0);
});
test('button names remain visible action text and refer to structured card content',()=>{
 setup();const button=buttons()[0];expect(button.getAttribute('aria-label')).toBeNull();
 const content=document.getElementById(button.getAttribute('aria-describedby'));expect(content.innerHTML).toContain('\\(n=2k+1\\)');
});
test('clone and history mode retain repeated cards and return focus after removing one',()=>{
 const {input}=setup({clone:'true',log:'true'});buttons()[0].click();buttons()[0].click();
 expect(JSON.parse(input.value)[0][0].used).toEqual([[['a','a']]]);
 action(answerItems()[0],'Remove').click();expect(document.activeElement.textContent).toBe('Add');
 expect(JSON.parse(input.value).length).toBe(4);
});
test('grid add and remove preserve focus and serialization',()=>{
 const {input}=setup({rows:'2',columns:'2'});buttons()[0].click();
 expect(JSON.parse(input.value)[0][0].used[0][0]).toEqual(['a']);expect(document.activeElement.textContent).toBe('Remove');
 document.activeElement.click();expect(JSON.parse(input.value)[0][0].used[0][0]).toEqual([]);expect(document.activeElement.textContent).toBe('Add');
});

test('drag-mode double click and clone clear cannot edit readonly answers',()=>{
 document.body.innerHTML='<input id="answer" readonly><button id="delete-all"></button><div id="containerRow"></div>';
 const s=new stack_sortable({a:'First',b:'Second'},'answer',{},'true','1','','col',undefined,false,'','','false');
 s.create_row_col_divs();s.add_headers(['Proof'],'Available');s.generate_used();s.generate_available();
 const get=el=>({toArray:()=>[...el.querySelectorAll('li[data-id]')].map(x=>x.dataset.id)});
 const used=[[get(s.used[0][0])]],available=get(s.available);s.add_dblclick_listeners(used,available);s.add_delete_all_listener('delete-all',used,available);
 const initial=document.getElementById('answer').value;s.available.querySelector('li[data-id]').dispatchEvent(new MouseEvent('dblclick',{bubbles:true}));
 expect(document.getElementById('answer').value).toBe(initial);expect(document.getElementById('delete-all').disabled).toBe(true);
});
