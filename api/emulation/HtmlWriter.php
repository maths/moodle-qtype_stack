<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Simple html output class
 *
 * @copyright 2009 Tim Hunt, 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package    qtype_stack
 * @category output
 */
class html_writer {

    /**
     * Outputs a tag with attributes and contents
     *
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @param string $contents What goes between the opening and closing tags
     * @param array|null $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    public static function tag($tagname, $contents, ?array $attributes = null) {
        return self::start_tag($tagname, $attributes) . $contents . self::end_tag($tagname);
    }

    /**
     * Outputs an opening tag with attributes
     *
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @param array|null $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    public static function start_tag($tagname, ?array $attributes = null) {
        return '<' . $tagname . self::attributes($attributes) . '>';
    }

    /**
     * Outputs a closing tag
     *
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @return string HTML fragment
     */
    public static function end_tag($tagname) {
        return '</' . $tagname . '>';
    }

    /**
     * Outputs an empty tag with attributes
     *
     * @param string $tagname The name of tag ('input', 'img', 'br' etc.)
     * @param array|null $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    public static function empty_tag($tagname, ?array $attributes = null) {
        return '<' . $tagname . self::attributes($attributes) . ' />';
    }

    /**
     * Outputs a tag, but only if the contents are not empty
     *
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @param string $contents What goes between the opening and closing tags
     * @param array|null $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    public static function nonempty_tag($tagname, $contents, ?array $attributes = null) {
        if ($contents === '' || is_null($contents)) {
            return '';
        }

        return self::tag($tagname, $contents, $attributes);
    }

    /**
     * Outputs a HTML attribute and value
     *
     * @param string $name The name of the attribute ('src', 'href', 'class' etc.)
     * @param string $value The value of the attribute. The value will be escaped with {@link s()}
     * @return string HTML fragment
     */
    public static function attribute($name, $value) {
        if ($value instanceof moodle_url) {
            return ' ' . $name . '="' . $value->out() . '"';
        }

        // Special case, we do not want these in output.
        if ($value === null) {
            return '';
        }

        // No sloppy trimming here!
        return ' ' . $name . '="' . s($value) . '"';
    }


    /**
     * Outputs a list of HTML attributes and values
     *
     * @param array|null $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     *       The values will be escaped with {@link s()}
     * @return string HTML fragment
     */
    public static function attributes(?array $attributes = null) {
        $attributes = (array)$attributes;
        $output = '';
        foreach ($attributes as $name => $value) {
            $output .= self::attribute($name, $value);
        }

        return $output;
    }

    /**
     * Generates random html element id.
     *
     * @param string $base A string fragment that will be included in the random ID.
     * @return string A unique ID
     */
    public static function random_id($base = 'random') {
        static $counter = 0;
        static $uniq;

        if (!isset($uniq)) {
            $uniq = uniqid();
        }

        $counter++;

        return $base . $uniq . $counter;
    }

    /**
     * Generates a simple html link
     *
     * @param string|moodle_url $url The URL
     * @param string $text The text
     * @param array|null $attributes HTML attributes
     * @return string HTML fragment
     */
    public static function link($url, $text, ?array $attributes = null) {
        $attributes = (array)$attributes;
        $attributes['href'] = $url;

        return self::tag('a', $text, $attributes);
    }

    /**
     * Generates a simple checkbox with optional label
     *
     * @param string $name The name of the checkbox
     * @param string $value The value of the checkbox
     * @param bool $checked Whether the checkbox is checked
     * @param string $label The label for the checkbox
     * @param array|null $attributes Any attributes to apply to the checkbox
     * @return string html fragment
     */
    public static function checkbox($name, $value, $checked = true, $label = '', ?array $attributes = null) {
        $attributes = (array)$attributes;
        $output = '';

        if ($label !== '' && !is_null($label)) {
            if (empty($attributes['id'])) {
                $attributes['id'] = self::random_id('checkbox_');
            }
        }
        $attributes['type'] = 'checkbox';
        $attributes['value'] = $value;
        $attributes['name'] = $name;
        $attributes['checked'] = $checked ? 'checked' : null;

        $output .= self::empty_tag('input', $attributes);

        if ($label !== '' && !is_null($label)) {
            $output .= self::tag('label', $label, ['for' => $attributes['id']]);
        }

        return $output;
    }

    /**
     * Generates a simple select yes/no form field
     *
     * @param string $name name of select element
     * @param bool $selected
     * @param array|null $attributes - html select element attributes
     * @return string HTML fragment
     */
    public static function select_yes_no($name, $selected = true, ?array $attributes = null) {
        $options = ['1' => get_string('yes'), '0' => get_string('no')];

        return self::select($options, $name, $selected, null, $attributes);
    }

    /**
     * Generates a simple select form field
     *
     * @param array $options associative array value=>label ex.:
     *                array(1=>'One, 2=>Two)
     *              it is also possible to specify optgroup as complex label array ex.:
     *                array(array('Odd'=>array(1=>'One', 3=>'Three)), array('Even'=>array(2=>'Two')))
     *                array(1=>'One', '--1uniquekey'=>array('More'=>array(2=>'Two', 3=>'Three')))
     * @param string $name name of select element
     * @param string|array $selected value or array of values depending on multiple attribute
     * @param array|bool $nothing add nothing selected option, or false of not added
     * @param array|null $attributes html select element attributes
     * @return string HTML fragment
     */
    public static function select(
        array $options, $name, $selected = '', $nothing = ['' => 'choosedots'], ?array $attributes = null) {
        $attributes = (array)$attributes;
        if (is_array($nothing)) {
            foreach ($nothing as $k => $v) {
                if ($v === 'choose' || $v === 'choosedots') {
                    $nothing[$k] = get_string('choosedots');
                }
            }
            $options = $nothing + $options; // Keep keys, do not override.
        } else {
            if (is_string($nothing) && $nothing !== '') {
                // BC.
                $options = ['' => $nothing] + $options;
            }
        }

        // We may accept more values if multiple attribute specified.
        $selected = (array)$selected;
        foreach ($selected as $k => $v) {
            $selected[$k] = (string)$v;
        }

        if (!isset($attributes['id'])) {
            $id = 'menu' . $name;
            // Name may contaion [], which would make an invalid id. e.g. numeric question type editing form,
            // assignment quickgrading.
            $id = str_replace('[', '', $id);
            $id = str_replace(']', '', $id);
            $attributes['id'] = $id;
        }

        if (!isset($attributes['class'])) {
            $class = 'menu' . $name;
            // Name may contaion [], which would make an invalid class. e.g. numeric question type editing form,
            // assignment quickgrading.
            $class = str_replace('[', '', $class);
            $class = str_replace(']', '', $class);
            $attributes['class'] = $class;
        }
        $attributes['class'] = 'select ' . $attributes['class']; // Add 'select' selector always.

        $attributes['name'] = $name;

        if (!empty($attributes['disabled'])) {
            $attributes['disabled'] = 'disabled';
        } else {
            unset($attributes['disabled']);
        }

        $output = '';
        foreach ($options as $value => $label) {
            if (is_array($label)) {
                // Ignore key, it just has to be unique.
                $output .= self::select_optgroup(key($label), current($label), $selected);
            } else {
                $output .= self::select_option($label, $value, $selected);
            }
        }

        return self::tag('select', $output, $attributes);
    }

    /**
     * Returns HTML to display a select box option.
     *
     * @param string $label The label to display as the option.
     * @param string|int $value The value the option represents
     * @param array $selected An array of selected options
     * @return string HTML fragment
     */
    private static function select_option($label, $value, array $selected) {
        $attributes = [];
        $value = (string)$value;
        if (in_array($value, $selected, true)) {
            $attributes['selected'] = 'selected';
        }
        $attributes['value'] = $value;

        return self::tag('option', $label, $attributes);
    }

    /**
     * Returns HTML to display a select box option group.
     *
     * @param string $groupname The label to use for the group
     * @param array $options The options in the group
     * @param array $selected An array of selected values.
     * @return string HTML fragment.
     */
    private static function select_optgroup($groupname, $options, array $selected) {
        if (empty($options)) {
            return '';
        }
        $attributes = ['label' => $groupname];
        $output = '';
        foreach ($options as $value => $label) {
            $output .= self::select_option($label, $value, $selected);
        }

        return self::tag('optgroup', $output, $attributes);
    }

    /**
     * This is a shortcut for making an hour selector menu.
     *
     * @param string $type The type of selector (years, months, days, hours, minutes)
     * @param string $name fieldname
     * @param int $currenttime A default timestamp in GMT
     * @param int $step minute spacing
     * @param array|null $attributes - html select element attributes
     * @return HTML fragment
     */
    public static function select_time($type, $name, $currenttime = 0, $step = 5, ?array $attributes = null) {
        if (!$currenttime) {
            $currenttime = time();
        }
        $currentdate = usergetdate($currenttime);
        $userdatetype = $type;
        $timeunits = [];

        switch ($type) {
            case 'years':
                for ($i = 1970; $i <= 2020; $i++) {
                    $timeunits[$i] = $i;
                }
                $userdatetype = 'year';
                break;
            case 'months':
                for ($i = 1; $i <= 12; $i++) {
                    $timeunits[$i] = userdate(gmmktime(12, 0, 0, $i, 15, 2000), "%B");
                }
                $userdatetype = 'month';
                $currentdate['month'] = (int)$currentdate['mon'];
                break;
            case 'days':
                for ($i = 1; $i <= 31; $i++) {
                    $timeunits[$i] = $i;
                }
                $userdatetype = 'mday';
                break;
            case 'hours':
                for ($i = 0; $i <= 23; $i++) {
                    $timeunits[$i] = sprintf("%02d", $i);
                }
                break;
            case 'minutes':
                if ($step != 1) {
                    $currentdate['minutes'] = ceil($currentdate['minutes'] / $step) * $step;
                }

                for ($i = 0; $i <= 59; $i += $step) {
                    $timeunits[$i] = sprintf("%02d", $i);
                }
                break;
            default:
                throw new \stack_exception("Time type $type is not supported by html_writer::select_time().");
        }

        if (empty($attributes['id'])) {
            $attributes['id'] = self::random_id('ts_');
        }
        $timerselector = self::select($timeunits, $name, $currentdate[$userdatetype], null, ['id' => $attributes['id']]);
        $label = self::tag('label', get_string(substr($type, 0, -1), 'form'),
            ['for' => $attributes['id'], 'class' => 'accesshide']);

        return $label . $timerselector;
    }

    /**
     * Shortcut for quick making of lists
     *
     * Note: 'list' is a reserved keyword ;-)
     *
     * @param array $items
     * @param array|null $attributes
     * @param string $tag ul or ol
     * @return string
     */
    public static function alist(array $items, ?array $attributes = null, $tag = 'ul') {
        $output = '';

        foreach ($items as $item) {
            $output .= self::start_tag('li') . "\n";
            $output .= $item . "\n";
            $output .= self::end_tag('li') . "\n";
        }

        return self::tag($tag, $output, $attributes);
    }

    /**
     * Returns hidden input fields created from url parameters.
     *
     * @param moodle_url $url
     * @param array|null $exclude list of excluded parameters
     * @return string HTML fragment
     */
    public static function input_hidden_params(moodle_url $url, ?array $exclude = null) {
        $exclude = (array) $exclude;
        $params = $url->params();
        foreach ($exclude as $key) {
            unset($params[$key]);
        }

        $output = '';
        foreach ($params as $key => $value) {
            $attributes = ['type' => 'hidden', 'name' => $key, 'value' => $value];
            $output .= self::empty_tag('input', $attributes) . "\n";
        }

        return $output;
    }

    /**
     * Generate a script tag containing the the specified code.
     *
     * @param string $jscode the JavaScript code
     * @param moodle_url|string $url optional url of the external script, $code ignored if specified
     * @return string HTML, the code wrapped in <script> tags.
     */
    public static function script($jscode, $url = null) {
        if ($jscode) {
            $attributes = ['type' => 'text/javascript'];

            return self::tag('script', "\n//<![CDATA[\n$jscode\n//]]>\n", $attributes) . "\n";
        } else {
            if ($url) {
                $attributes = ['type' => 'text/javascript', 'src' => $url];

                return self::tag('script', '', $attributes) . "\n";
            } else {
                return '';
            }
        }
    }

    /**
     * Renders HTML table
     *
     * This method may modify the passed instance by adding some default properties if they are not set yet.
     * If this is not what you want, you should make a full clone of your data before passing them to this
     * method. In most cases this is not an issue at all so we do not clone by default for performance
     * and memory consumption reasons.
     *
     * @param html_table $table data to be rendered
     * @return string HTML code
     */
    public static function table(html_table $table) {
        // Prepare table data and populate missing properties with reasonable defaults.
        if (!empty($table->align)) {
            foreach ($table->align as $key => $aa) {
                if ($aa) {
                    $table->align[$key] = 'text-align:' . fix_align_rtl($aa) . ';'; // Fix for RTL languages.
                } else {
                    $table->align[$key] = null;
                }
            }
        }
        if (!empty($table->size)) {
            foreach ($table->size as $key => $ss) {
                if ($ss) {
                    $table->size[$key] = 'width:' . $ss . ';';
                } else {
                    $table->size[$key] = null;
                }
            }
        }
        if (!empty($table->wrap)) {
            foreach ($table->wrap as $key => $ww) {
                if ($ww) {
                    $table->wrap[$key] = 'white-space:nowrap;';
                } else {
                    $table->wrap[$key] = '';
                }
            }
        }
        if (!empty($table->head)) {
            foreach ($table->head as $key => $val) {
                if (!isset($table->align[$key])) {
                    $table->align[$key] = null;
                }
                if (!isset($table->size[$key])) {
                    $table->size[$key] = null;
                }
                if (!isset($table->wrap[$key])) {
                    $table->wrap[$key] = null;
                }
            }
        }
        if (empty($table->attributes['class'])) {
            $table->attributes['class'] = 'generaltable';
        }
        if (!empty($table->tablealign)) {
            $table->attributes['class'] .= ' boxalign' . $table->tablealign;
        }

        // Explicitly assigned properties override those defined via $table->attributes.
        $table->attributes['class'] = trim($table->attributes['class']);
        $attributes = array_merge($table->attributes, [
            'id' => $table->id, 'width' => $table->width,
            'summary' => $table->summary, 'cellpadding' => $table->cellpadding, 'cellspacing' => $table->cellspacing,
        ]);
        $output = self::start_tag('table', $attributes) . "\n";

        $countcols = 0;

        if (!empty($table->head)) {
            $countcols = count($table->head);

            $output .= self::start_tag('thead', []) . "\n";
            $output .= self::start_tag('tr', []) . "\n";
            $keys = array_keys($table->head);
            $lastkey = end($keys);

            foreach ($table->head as $key => $heading) {
                // Convert plain string headings into html_table_cell objects.
                if (!($heading instanceof html_table_cell)) {
                    $headingtext = $heading;
                    $heading = new html_table_cell();
                    $heading->text = $headingtext;
                    $heading->header = true;
                }

                if ($heading->header !== false) {
                    $heading->header = true;
                }

                if ($heading->header && empty($heading->scope)) {
                    $heading->scope = 'col';
                }

                $heading->attributes['class'] .= ' header c' . $key;
                if (isset($table->headspan[$key]) && $table->headspan[$key] > 1) {
                    $heading->colspan = $table->headspan[$key];
                    $countcols += $table->headspan[$key] - 1;
                }

                if ($key == $lastkey) {
                    $heading->attributes['class'] .= ' lastcol';
                }
                if (isset($table->colclasses[$key])) {
                    $heading->attributes['class'] .= ' ' . $table->colclasses[$key];
                }
                $heading->attributes['class'] = trim($heading->attributes['class']);
                $attributes = array_merge($heading->attributes, [
                    'style' => $table->align[$key] . $table->size[$key] .
                    $heading->style, 'scope' => $heading->scope, 'colspan' => $heading->colspan,
                ]);

                $tagtype = 'td';
                if ($heading->header === true) {
                    $tagtype = 'th';
                }
                $output .= self::tag($tagtype, $heading->text, $attributes) . "\n";
            }
            $output .= self::end_tag('tr') . "\n";
            $output .= self::end_tag('thead') . "\n";

            if (empty($table->data)) {
                // For valid XHTML strict every table must contain either a valid tr
                // r a valid tbody... both of which must contain a valid td.
                $output .= self::start_tag('tbody', ['class' => 'empty']);
                $output .= self::tag('tr', self::tag('td', '', ['colspan' => count($table->head)]));
                $output .= self::end_tag('tbody');
            }
        }

        if (!empty($table->data)) {
            $oddeven = 1;
            $keys = array_keys($table->data);
            $lastrowkey = end($keys);
            $output .= self::start_tag('tbody', []);

            foreach ($table->data as $key => $row) {
                if (($row === 'hr') && ($countcols)) {
                    $output .= self::tag('td', self::tag('div', '',
                        ['class' => 'tabledivider']), ['colspan' => $countcols]);
                } else {
                    // Convert array rows to html_table_rows and cell strings to html_table_cell objects.
                    if (!($row instanceof html_table_row)) {
                        $newrow = new html_table_row();

                        foreach ($row as $cell) {
                            if (!($cell instanceof html_table_cell)) {
                                $cell = new html_table_cell($cell);
                            }
                            $newrow->cells[] = $cell;
                        }
                        $row = $newrow;
                    }

                    $oddeven = $oddeven ? 0 : 1;
                    if (isset($table->rowclasses[$key])) {
                        $row->attributes['class'] .= ' ' . $table->rowclasses[$key];
                    }

                    $row->attributes['class'] .= ' r' . $oddeven;
                    if ($key == $lastrowkey) {
                        $row->attributes['class'] .= ' lastrow';
                    }

                    $output .= self::start_tag('tr', [
                        'class' => trim($row->attributes['class']),
                        'style' => $row->style, 'id' => $row->id,
                    ]) . "\n";
                    $keys2 = array_keys($row->cells);
                    $lastkey = end($keys2);

                    $gotlastkey = false; // Flag for sanity checking.
                    foreach ($row->cells as $key => $cell) {
                        if ($gotlastkey) {
                            // This should never happen. Why do we have a cell after the last cell?
                            mtrace("A cell with key ($key) was found after the last key ($lastkey)");
                        }

                        if (!($cell instanceof html_table_cell)) {
                            $mycell = new html_table_cell();
                            $mycell->text = $cell;
                            $cell = $mycell;
                        }

                        if (($cell->header === true) && empty($cell->scope)) {
                            $cell->scope = 'row';
                        }

                        if (isset($table->colclasses[$key])) {
                            $cell->attributes['class'] .= ' ' . $table->colclasses[$key];
                        }

                        $cell->attributes['class'] .= ' cell c' . $key;
                        if ($key == $lastkey) {
                            $cell->attributes['class'] .= ' lastcol';
                            $gotlastkey = true;
                        }
                        $tdstyle = '';
                        $tdstyle .= isset($table->align[$key]) ? $table->align[$key] : '';
                        $tdstyle .= isset($table->size[$key]) ? $table->size[$key] : '';
                        $tdstyle .= isset($table->wrap[$key]) ? $table->wrap[$key] : '';
                        $cell->attributes['class'] = trim($cell->attributes['class']);
                        $tdattributes = array_merge($cell->attributes, [
                            'style' => $tdstyle .
                            $cell->style, 'colspan' => $cell->colspan, 'rowspan' => $cell->rowspan,
                            'id' => $cell->id, 'abbr' => $cell->abbr, 'scope' => $cell->scope,
                        ]);
                        $tagtype = 'td';
                        if ($cell->header === true) {
                            $tagtype = 'th';
                        }
                        $output .= self::tag($tagtype, $cell->text, $tdattributes) . "\n";
                    }
                }
                $output .= self::end_tag('tr') . "\n";
            }
            $output .= self::end_tag('tbody') . "\n";
        }
        $output .= self::end_tag('table') . "\n";

        return $output;
    }

    /**
     * Renders form element label
     *
     * By default, the label is suffixed with a label separator defined in the
     * current language pack (colon by default in the English lang pack).
     * Adding the colon can be explicitly disabled if needed. Label separators
     * are put outside the label tag itself so they are not read by
     * screenreaders (accessibility).
     *
     * Parameter $for explicitly associates the label with a form control. When
     * set, the value of this attribute must be the same as the value of
     * the id attribute of the form control in the same document. When null,
     * the label being defined is associated with the control inside the label
     * element.
     *
     * @param string $text content of the label tag
     * @param string|null $for id of the element this label is associated with, null for no association
     * @param bool $colonize add label separator (colon) to the label text, if it is not there yet
     * @param array $attributes to be inserted in the tab, for example array('accesskey' => 'a')
     * @return string HTML of the label element
     */
    public static function label($text, $for, $colonize = true, array $attributes = []) {
        if (!is_null($for)) {
            $attributes = array_merge($attributes, ['for' => $for]);
        }
        $text = trim($text);
        $label = self::tag('label', $text, $attributes);

        return $label;
    }
}
