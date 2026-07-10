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
// along with STACK.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/questionlib.php');
require_once($CFG->dirroot . '/question/format/xml/format.php');

/**
 * Loads and manipulates data for the question XML comparison page.
 *
 * @package   qtype_stack
 * @copyright 2026 University of Edinburgh.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */
class stack_question_xml_compare {
    /**
     * Export one question version as Moodle XML.
     *
     * @param stdClass $questiondata the question data to export.
     * @return string|false the XML string, or false if it cannot be exported.
     */
    public static function export_question_version_xml($questiondata) {
        global $COURSE;

        $question = question_bank::load_question($questiondata->id);
        $questiondata = clone($questiondata);
        $questiondata->contextid = $question->contextid;
        $questiondata->idnumber = $question->idnumber;

        $qformat = new qformat_xml();
        $qformat->setCattofile(false);
        $qformat->setContexttofile(false);
        $qformat->setContextfromfile(false);
        $qformat->setStoponerror(true);
        $qformat->setCourse($COURSE);

        if (!$qformat->exportpreprocess()) {
            return false;
        }

        $xml = $qformat->writequestion($questiondata);
        if (!$xml) {
            return false;
        }

        return '<?xml version="1.0" encoding="UTF-8"?>
<quiz>
' . $xml . '</quiz>';
    }

    /**
     * Select the version to compare with the current one.
     *
     * @param int|null $requestedcompareversion requested comparison version.
     * @param array $versions version number => question id.
     * @param int $currentversion current/latest version number.
     * @return int selected comparison version.
     */
    public static function get_compare_version($requestedcompareversion, array $versions, int $currentversion): int {
        $versionnumbers = array_keys($versions);
        $defaultcompareversion = $versionnumbers[1] ?? $currentversion;
        if ($requestedcompareversion === null) {
            return $defaultcompareversion;
        }
        if (!array_key_exists($requestedcompareversion, $versions)) {
            throw new invalid_parameter_exception('compareversion');
        }

        return $requestedcompareversion;
    }

    /**
     * Build template data for the version dropdown.
     *
     * @param array $versions version number => question id.
     * @param int $compareversion selected comparison version.
     * @return stdClass[] template options.
     */
    public static function version_options(array $versions, int $compareversion): array {
        $options = [];
        foreach (array_keys($versions) as $version) {
            $option = new stdClass();
            $option->version = $version;
            $option->label = stack_string('version') . ' ' . $version;
            $option->selected = ($version === $compareversion);
            $options[] = $option;
        }

        return $options;
    }

    /**
     * Build template data for hidden form parameters.
     *
     * @param array $params URL parameters to preserve.
     * @return stdClass[] template hidden input data.
     */
    public static function form_params(array $params): array {
        $formparams = [];
        foreach ($params as $name => $value) {
            if (is_array($value)) {
                continue;
            }
            $param = new stdClass();
            $param->name = $name;
            $param->value = $value;
            $formparams[] = $param;
        }

        return $formparams;
    }

    /**
     * Build template data for the comparison page.
     *
     * @param object $question current question.
     * @param int $currentversion current/latest version number.
     * @param int $compareversion selected comparison version.
     * @param array $versions version number => question id.
     * @param string $currentxml current version XML.
     * @param string $comparexml selected version XML.
     * @param string $notices notice text.
     * @param stdClass $general general URL data.
     * @return stdClass template data.
     */
    public static function output_data(
        object $question,
        int $currentversion,
        int $compareversion,
        array $versions,
        string $currentxml,
        string $comparexml,
        string $notices,
        stdClass $general
    ): stdClass {
        $output = new stdClass();
        $output->question = new stdClass();
        $output->question->name = $question->name;
        $output->question->currentversion = $currentversion;
        $output->question->compareversion = $compareversion;
        $output->question->currentversionlabel = stack_string('comparexmlcurrentversion', $currentversion);
        $output->question->compareversionlabel = stack_string('comparexmlselectedversion', $compareversion);

        $output->general = $general;
        $output->hasnotices = $notices !== '';
        $output->notices = $notices;
        $output->nootherversions = count($versions) < 2;
        $output->versions = self::version_options($versions, $compareversion);
        $output->rows = self::diff_rows($currentxml, $comparexml);

        return $output;
    }

    /**
     * Split XML into lines for comparison.
     *
     * @param string $xml the XML to split.
     * @return string[] the lines.
     */
    public static function lines(string $xml): array {
        return explode("\n", str_replace(["\r\n", "\r"], "\n", $xml));
    }

    /**
     * Build raw line-based diff rows.
     *
     * @param string $currentxml the current version XML.
     * @param string $comparexml the selected version XML.
     * @return stdClass[] raw rows.
     */
    public static function compare_rows(string $currentxml, string $comparexml): array {
        $current = self::lines($currentxml);
        $compare = self::lines($comparexml);
        $currentcount = count($current);
        $comparecount = count($compare);
        $lcs = array_fill(0, $currentcount + 1, array_fill(0, $comparecount + 1, 0));

        for ($i = $currentcount - 1; $i >= 0; $i--) {
            for ($j = $comparecount - 1; $j >= 0; $j--) {
                if ($current[$i] === $compare[$j]) {
                    $lcs[$i][$j] = $lcs[$i + 1][$j + 1] + 1;
                } else {
                    $lcs[$i][$j] = max($lcs[$i + 1][$j], $lcs[$i][$j + 1]);
                }
            }
        }

        $ops = [];
        $i = 0;
        $j = 0;
        while ($i < $currentcount && $j < $comparecount) {
            if ($current[$i] === $compare[$j]) {
                $ops[] = self::op('same', $i + 1, $j + 1, $current[$i], $compare[$j]);
                $i++;
                $j++;
            } else if ($lcs[$i + 1][$j] >= $lcs[$i][$j + 1]) {
                $ops[] = self::op('delete', $i + 1, null, $current[$i], null);
                $i++;
            } else {
                $ops[] = self::op('add', null, $j + 1, null, $compare[$j]);
                $j++;
            }
        }
        while ($i < $currentcount) {
            $ops[] = self::op('delete', $i + 1, null, $current[$i], null);
            $i++;
        }
        while ($j < $comparecount) {
            $ops[] = self::op('add', null, $j + 1, null, $compare[$j]);
            $j++;
        }

        return self::pair_changed_blocks($ops);
    }

    /**
     * Build an operation row.
     *
     * @param string $type operation type.
     * @param int|null $currentline current line number.
     * @param int|null $compareline compared line number.
     * @param string|null $currenttext current text.
     * @param string|null $comparetext compared text.
     * @return array operation row.
     */
    protected static function op($type, $currentline, $compareline, $currenttext, $comparetext): array {
        return [
            'type' => $type,
            'currentline' => $currentline,
            'compareline' => $compareline,
            'currenttext' => $currenttext,
            'comparetext' => $comparetext,
        ];
    }

    /**
     * Pair line insertions/deletions into changed rows where possible.
     *
     * @param array $ops raw operations.
     * @return stdClass[] raw rows.
     */
    protected static function pair_changed_blocks(array $ops): array {
        $rows = [];
        $opcount = count($ops);
        for ($i = 0; $i < $opcount; $i++) {
            if ($ops[$i]['type'] === 'same') {
                $rows[] = (object) $ops[$i];
                continue;
            }

            $currentblock = [];
            $compareblock = [];
            while ($i < $opcount && $ops[$i]['type'] !== 'same') {
                if ($ops[$i]['type'] === 'delete') {
                    $currentblock[] = $ops[$i];
                } else {
                    $compareblock[] = $ops[$i];
                }
                $i++;
            }
            $i--;

            $blockrows = max(count($currentblock), count($compareblock));
            for ($j = 0; $j < $blockrows; $j++) {
                $currentop = $currentblock[$j] ?? null;
                $compareop = $compareblock[$j] ?? null;
                if ($currentop && $compareop) {
                    $type = 'changed';
                } else if ($currentop) {
                    $type = 'added';
                } else {
                    $type = 'deleted';
                }
                $rows[] = (object) [
                    'type' => $type,
                    'currentline' => $currentop['currentline'] ?? null,
                    'compareline' => $compareop['compareline'] ?? null,
                    'currenttext' => $currentop['currenttext'] ?? null,
                    'comparetext' => $compareop['comparetext'] ?? null,
                ];
            }
        }

        return $rows;
    }

    /**
     * Build template-ready diff rows.
     *
     * @param string $currentxml the current version XML.
     * @param string $comparexml the selected version XML.
     * @return stdClass[] template rows.
     */
    public static function diff_rows(string $currentxml, string $comparexml): array {
        $rows = [];
        foreach (self::compare_rows($currentxml, $comparexml) as $row) {
            $currenthtml = null;
            $comparehtml = null;
            if ($row->type === 'changed') {
                [$currenthtml, $comparehtml] = self::inline_changed($row->currenttext, $row->comparetext);
            } else if ($row->type === 'deleted') {
                $currenthtml = html_writer::tag(
                    'span',
                    s($row->comparetext),
                    ['class' => 'stack-xml-compare-inline-deleted stack-xml-compare-inline-missing']
                );
            }

            $template = new stdClass();
            $template->type = $row->type;
            $template->currentline = $row->currentline ?? '';
            $template->compareline = $row->compareline ?? '';
            $template->currentclass = self::cell_class('current', $row->currenttext === null && $currenthtml === null);
            $template->compareclass = self::cell_class('compared', $row->comparetext === null && $comparehtml === null);
            $template->currenthtml = self::cell_html($row->currenttext, $currenthtml);
            $template->comparehtml = self::cell_html($row->comparetext, $comparehtml);
            $rows[] = $template;
        }

        return $rows;
    }

    /**
     * Build a cell class.
     *
     * @param string $side cell side.
     * @param bool $empty whether the cell is empty.
     * @return string cell classes.
     */
    protected static function cell_class(string $side, bool $empty): string {
        $classes = 'stack-xml-compare-code stack-xml-compare-code-' . $side;
        if ($empty) {
            $classes .= ' stack-xml-compare-empty';
        }

        return $classes;
    }

    /**
     * Build cell HTML.
     *
     * @param string|null $text plain text.
     * @param string|null $html explicit HTML.
     * @return string cell HTML.
     */
    protected static function cell_html($text, $html): string {
        if ($html !== null) {
            return $html;
        }
        if ($text === null) {
            return '';
        }

        return s($text);
    }

    /**
     * Split a line into characters.
     *
     * @param string $text text to split.
     * @return string[] characters.
     */
    public static function chars(string $text): array {
        $chars = preg_split('//u', $text, -1, PREG_SPLIT_NO_EMPTY);
        if ($chars === false) {
            $chars = str_split($text);
        }

        return $chars;
    }

    /**
     * Render an inline character diff for two changed lines.
     *
     * @param string $currenttext current version line.
     * @param string $comparetext selected version line.
     * @return string[] current and compared HTML.
     */
    public static function inline_changed(string $currenttext, string $comparetext): array {
        $current = self::chars($currenttext);
        $compare = self::chars($comparetext);
        $currentcount = count($current);
        $comparecount = count($compare);

        if ($currentcount * $comparecount > 40000) {
            return self::inline_changed_region($current, $compare);
        }

        $lcs = array_fill(0, $currentcount + 1, array_fill(0, $comparecount + 1, 0));
        for ($i = $currentcount - 1; $i >= 0; $i--) {
            for ($j = $comparecount - 1; $j >= 0; $j--) {
                if ($current[$i] === $compare[$j]) {
                    $lcs[$i][$j] = $lcs[$i + 1][$j + 1] + 1;
                } else {
                    $lcs[$i][$j] = max($lcs[$i + 1][$j], $lcs[$i][$j + 1]);
                }
            }
        }

        $currenthtml = '';
        $comparehtml = '';
        $i = 0;
        $j = 0;
        while ($i < $currentcount && $j < $comparecount) {
            if ($current[$i] === $compare[$j]) {
                $currenthtml .= s($current[$i]);
                $comparehtml .= s($compare[$j]);
                $i++;
                $j++;
            } else if ($lcs[$i + 1][$j] > $lcs[$i][$j + 1]) {
                $currenthtml .= html_writer::tag(
                    'span',
                    s($current[$i]),
                    ['class' => 'stack-xml-compare-inline-added']
                );
                $i++;
            } else {
                $currenthtml .= self::missing_html($compare[$j]);
                $comparehtml .= html_writer::tag(
                    'span',
                    s($compare[$j]),
                    ['class' => 'stack-xml-compare-inline-deleted']
                );
                $j++;
            }
        }
        while ($i < $currentcount) {
            $currenthtml .= html_writer::tag('span', s($current[$i]), ['class' => 'stack-xml-compare-inline-added']);
            $i++;
        }
        while ($j < $comparecount) {
            $currenthtml .= self::missing_html($compare[$j]);
            $comparehtml .= html_writer::tag('span', s($compare[$j]), ['class' => 'stack-xml-compare-inline-deleted']);
            $j++;
        }

        return [$currenthtml, $comparehtml];
    }

    /**
     * Render inline changed regions for long lines without building an LCS matrix.
     *
     * @param string[] $current current version characters.
     * @param string[] $compare selected version characters.
     * @return string[] current and compared HTML.
     */
    public static function inline_changed_region(array $current, array $compare): array {
        $currentcount = count($current);
        $comparecount = count($compare);
        $prefix = 0;
        while ($prefix < $currentcount && $prefix < $comparecount && $current[$prefix] === $compare[$prefix]) {
            $prefix++;
        }

        $suffix = 0;
        while (
            $suffix < $currentcount - $prefix &&
            $suffix < $comparecount - $prefix &&
            $current[$currentcount - $suffix - 1] === $compare[$comparecount - $suffix - 1]
        ) {
            $suffix++;
        }

        $currenthtml = s(implode('', array_slice($current, 0, $prefix)));
        $comparehtml = s(implode('', array_slice($compare, 0, $prefix)));
        $currentmiddle = implode('', array_slice($current, $prefix, $currentcount - $prefix - $suffix));
        $comparemiddle = implode('', array_slice($compare, $prefix, $comparecount - $prefix - $suffix));
        if ($currentmiddle !== '') {
            $currenthtml .= html_writer::tag('span', s($currentmiddle), ['class' => 'stack-xml-compare-inline-added']);
        }
        if ($comparemiddle !== '') {
            $currenthtml .= self::missing_html($comparemiddle);
            $comparehtml .= html_writer::tag(
                'span',
                s($comparemiddle),
                ['class' => 'stack-xml-compare-inline-deleted']
            );
        }
        $currenthtml .= s(implode('', array_slice($current, $currentcount - $suffix)));
        $comparehtml .= s(implode('', array_slice($compare, $comparecount - $suffix)));

        return [$currenthtml, $comparehtml];
    }

    /**
     * Render text missing from current line.
     *
     * @param string $text deleted text.
     * @return string HTML.
     */
    protected static function missing_html(string $text): string {
        return html_writer::tag(
            'span',
            s($text),
            ['class' => 'stack-xml-compare-inline-deleted stack-xml-compare-inline-missing']
        );
    }
}
