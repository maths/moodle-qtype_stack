<?php

class qtype_stack_renderer {
    public function fact_sheet($name, $fact) {
        $name = html_writer::tag('h5', $name);
        return html_writer::tag('div', $name.$fact, array('class' => 'factsheet'));
    }
}
