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
    /** @var int unchanged character gap below which nearby inline changes are joined. */
    protected const INLINE_JOIN_GAP = 6;

    /** @var string unified diff display mode. */
    public const DISPLAY_UNIFIED = 'unified';

    /** @var string split diff display mode. */
    public const DISPLAY_SPLIT = 'split';

    /** @var int show every diff row, including unchanged context lines. */
    public const FILTER_ALL = 0;

    /** @var int show compacted changed blocks with one unchanged context row on either side. */
    public const FILTER_DIFFERENCES = 1;

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
     * @param int $currentversion current selected version number.
     * @return int selected comparison version.
     */
    public static function get_compare_version($requestedcompareversion, array $versions, int $currentversion): int {
        $versionnumbers = array_keys($versions);
        // Here get_latest_question_version() returns versions newest first. The natural default is the
        // entry after the selected current version, falling back to comparing the version with itself.
        $currentindex = array_search($currentversion, $versionnumbers, true);
        $defaultcompareversion = $versionnumbers[$currentindex + 1] ?? $currentversion;
        if ($requestedcompareversion === null) {
            return $defaultcompareversion;
        }
        if (!array_key_exists($requestedcompareversion, $versions)) {
            throw new invalid_parameter_exception('compareversion');
        }

        return $requestedcompareversion;
    }

    /**
     * Select the current/base version to compare from.
     *
     * @param int|null $requestedcurrentversion requested current/base version.
     * @param array $versions version number => question id.
     * @param int $latestversion latest version number.
     * @return int selected current/base version.
     */
    public static function get_current_version($requestedcurrentversion, array $versions, int $latestversion): int {
        if ($requestedcurrentversion === null) {
            return $latestversion;
        }
        if (!array_key_exists($requestedcurrentversion, $versions)) {
            throw new invalid_parameter_exception('currentversion');
        }

        return $requestedcurrentversion;
    }

    /**
     * Select the diff display mode.
     *
     * @param string|null $requesteddisplay requested display mode.
     * @return string selected display mode.
     */
    public static function get_display_mode(?string $requesteddisplay): string {
        if ($requesteddisplay === null || $requesteddisplay === '') {
            return self::DISPLAY_UNIFIED;
        }
        if ($requesteddisplay === self::DISPLAY_UNIFIED || $requesteddisplay === self::DISPLAY_SPLIT) {
            return $requesteddisplay;
        }

        return self::DISPLAY_UNIFIED;
    }

    /**
     * Get the other display mode.
     *
     * @param string $displaymode current display mode.
     * @return string toggled display mode.
     */
    public static function toggle_display_mode(string $displaymode): string {
        if ($displaymode === self::DISPLAY_SPLIT) {
            return self::DISPLAY_UNIFIED;
        }

        return self::DISPLAY_SPLIT;
    }

    /**
     * Select whether unchanged rows should be hidden.
     *
     * @param int|bool|null $requesteddiffonly requested row filter.
     * @return bool true when compacted changed blocks should be shown.
     */
    public static function get_diff_only($requesteddiffonly): bool {
        return (bool) $requesteddiffonly;
    }

    /**
     * Get the other row filter value.
     *
     * @param bool $diffonly current row filter.
     * @return int toggled row filter URL value.
     */
    public static function toggle_diff_only(bool $diffonly): int {
        if ($diffonly) {
            return self::FILTER_ALL;
        }

        return self::FILTER_DIFFERENCES;
    }

    /**
     * Build template data for the version dropdown.
     *
     * @param array $versions version number => question id.
     * @param int $compareversion selected comparison version.
     * @param bool $fileselected whether to add a selected-file option.
     * @return stdClass[] template options.
     */
    public static function version_options(array $versions, int $compareversion, bool $fileselected = false): array {
        $options = [];
        if ($fileselected) {
            $fileoption = new stdClass();
            $fileoption->version = '';
            $fileoption->label = stack_string('comparexmlselectedfile');
            $fileoption->selected = true;
            $fileoption->disabled = true;
            $options[] = $fileoption;
        }
        foreach (array_keys($versions) as $version) {
            $option = new stdClass();
            $option->version = $version;
            $option->label = $version;
            $option->selected = (!$fileselected && $version === $compareversion);
            $options[] = $option;
        }

        return $options;
    }

    /**
     * Build template data for the question selector.
     *
     * @param int $categoryid question category id.
     * @param int $selectedquestionbankentryid selected question bank entry id.
     * @return stdClass[] question selector options.
     */
    public static function questions_in_category(int $categoryid, int $selectedquestionbankentryid): array {
        global $DB;

        $records = $DB->get_records_sql("
            SELECT q.id, q.name, q.qtype, qv.version, qbe.id AS questionbankentryid
              FROM {question} q
              JOIN {question_versions} qv ON qv.questionid = q.id
              JOIN {question_bank_entries} qbe ON qbe.id = qv.questionbankentryid
             WHERE qbe.questioncategoryid = :categoryid
               AND q.parent = 0
               AND qv.version = (
                    SELECT MAX(v.version)
                      FROM {question_versions} v
                     WHERE v.questionbankentryid = qbe.id
               )
          ORDER BY q.name, q.id", ['categoryid' => $categoryid]);

        return self::question_options($records, $selectedquestionbankentryid);
    }

    /**
     * Build template data for the question selector from question records.
     *
     * @param array $questions question records.
     * @param int $selectedquestionbankentryid selected question bank entry id.
     * @return stdClass[] question selector options.
     */
    public static function question_options(array $questions, int $selectedquestionbankentryid): array {
        $options = [];
        foreach ($questions as $question) {
            $option = new stdClass();
            $option->id = (int) $question->id;
            $option->label = $question->name;
            $option->selected = ((int) $question->questionbankentryid === $selectedquestionbankentryid);
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
            // The compare-version select has its own field, but Moodle context fields such as cmid/courseid
            // must survive the auto-submit.
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
     * Build hidden parameters for the question selector.
     *
     * Switching question should always discard uploaded files, so we strip any filepicker draft ids here.
     *
     * @param array $params URL parameters to preserve.
     * @return stdClass[] template hidden input data.
     */
    public static function question_form_params(array $params): array {
        unset($params['currentfile']);
        unset($params['comparefile']);

        return self::form_params($params);
    }

    /**
     * Get a file from the current user's draft file area.
     *
     * @param int|null $draftitemid draft item id from a Moodle filepicker.
     * @return stored_file|null draft file, if present.
     */
    public static function draft_file(?int $draftitemid) {
        global $USER;

        if (empty($draftitemid) || empty($USER->id)) {
            return null;
        }

        $fs = get_file_storage();
        $usercontext = context_user::instance($USER->id);
        $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id DESC', false);
        if (!$files) {
            return null;
        }

        return reset($files) ?: null;
    }

    /**
     * Read XML content from the current user's draft file area.
     *
     * @param int|null $draftitemid draft item id from a Moodle filepicker.
     * @return string|null file content, if present.
     */
    public static function draft_file_content(?int $draftitemid): ?string {
        $file = self::draft_file($draftitemid);

        return $file ? $file->get_content() : null;
    }

    /**
     * Get the display filename for a draft file.
     *
     * @param int|null $draftitemid draft item id from a Moodle filepicker.
     * @return string|null filename, if present.
     */
    public static function draft_file_name(?int $draftitemid): ?string {
        $file = self::draft_file($draftitemid);

        return $file ? $file->get_filename() : null;
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

        // Build a longest-common-subsequence matrix for complete XML lines. Working backwards lets each
        // cell answer: "from these two positions, how many unchanged lines can still be matched?".
        for ($i = $currentcount - 1; $i >= 0; $i--) {
            for ($j = $comparecount - 1; $j >= 0; $j--) {
                if ($current[$i] === $compare[$j]) {
                    $lcs[$i][$j] = $lcs[$i + 1][$j + 1] + 1;
                } else {
                    $lcs[$i][$j] = max($lcs[$i + 1][$j], $lcs[$i][$j + 1]);
                }
            }
        }

        // Walk the matrix from the start of both files to produce a minimal stream of primitive operations.
        // These primitive names are from the matrix perspective:
        // - "delete" means a line exists only in the current/latest XML.
        // - "add" means a line exists only in the compared/older XML.
        // They are translated later into user-facing "added" and "deleted" rows from the perspective of
        // "what changed in the current version compared with the selected version".
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

            // Consecutive non-matching operations form a change block between two unchanged anchors.
            // Pair one current-only line with one compared-only line as a changed line. Any surplus
            // current-only lines are additions; any surplus compared-only lines are deletions.
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
     * @param string $displaymode display mode.
     * @param bool $diffonly whether output should be compacted to changed blocks.
     * @return stdClass[] template rows.
     */
    public static function diff_rows(
        string $currentxml,
        string $comparexml,
        string $displaymode = self::DISPLAY_UNIFIED,
        bool $diffonly = false
    ): array {
        if (self::get_display_mode($displaymode) === self::DISPLAY_SPLIT) {
            return self::filter_diff_rows(self::split_diff_rows($currentxml, $comparexml), $diffonly);
        }

        return self::filter_diff_rows(self::unified_diff_rows($currentxml, $comparexml), $diffonly);
    }

    /**
     * Optionally reduce output to changed blocks plus one unchanged context row on either side.
     *
     * @param stdClass[] $rows template rows.
     * @param bool $diffonly whether unchanged rows should be mostly hidden.
     * @return stdClass[] filtered template rows.
     */
    protected static function filter_diff_rows(array $rows, bool $diffonly): array {
        if (!$diffonly) {
            return $rows;
        }

        $segments = [];
        $rowcount = count($rows);
        for ($index = 0; $index < $rowcount; $index++) {
            $row = $rows[$index];
            if ($row->type === 'same') {
                continue;
            }

            $start = $index;
            $end = $index;
            while ($end + 1 < $rowcount && $rows[$end + 1]->type !== 'same') {
                $end++;
            }
            $index = $end;

            if ($start > 0 && $rows[$start - 1]->type === 'same') {
                $start--;
            }
            if ($end + 1 < $rowcount && $rows[$end + 1]->type === 'same') {
                $end++;
            }

            $lastsegment = array_key_last($segments);
            if ($lastsegment !== null && $start <= $segments[$lastsegment][1]) {
                $segments[$lastsegment][1] = max($segments[$lastsegment][1], $end);
            } else {
                $segments[] = [$start, $end];
            }
        }

        if (!$segments) {
            return [];
        }

        $filtered = [];
        foreach ($segments as $segmentindex => [$start, $end]) {
            if ($segmentindex > 0) {
                $filtered[] = self::separator_row();
            }
            for ($i = $start; $i <= $end; $i++) {
                $filtered[] = $rows[$i];
            }
        }

        return $filtered;
    }

    /**
     * Build a marker row which separates compacted changed blocks.
     *
     * @return stdClass template row.
     */
    protected static function separator_row(): stdClass {
        $row = new stdClass();
        $row->type = 'separator';
        $row->currentline = '';
        $row->compareline = '';
        $row->currentclass = 'stack-xml-compare-code stack-xml-compare-separator-cell';
        $row->compareclass = 'stack-xml-compare-code stack-xml-compare-separator-cell';
        $row->currenthtml = '...';
        $row->comparehtml = '...';

        return $row;
    }

    /**
     * Build template-ready unified diff rows.
     *
     * @param string $currentxml the current version XML.
     * @param string $comparexml the selected version XML.
     * @return stdClass[] template rows.
     */
    protected static function unified_diff_rows(string $currentxml, string $comparexml): array {
        $rows = [];
        foreach (self::compare_rows($currentxml, $comparexml) as $row) {
            if ($row->type === 'changed') {
                [$currenthtml, $deletedhtml] = self::inline_changed_separate($row->currenttext, $row->comparetext);
                $rows[] = self::template_row((object) [
                    'type' => 'deleted',
                    'currentline' => null,
                    'compareline' => $row->compareline,
                    'currenttext' => null,
                    'comparetext' => null,
                ], $deletedhtml, null);
                $rows[] = self::template_row((object) [
                    'type' => 'added',
                    'currentline' => $row->currentline,
                    'compareline' => null,
                    'currenttext' => $row->currenttext,
                    'comparetext' => null,
                ], $currenthtml, null);
                continue;
            } else if ($row->type === 'deleted') {
                $currenthtml = html_writer::tag(
                    'span',
                    s($row->comparetext),
                    ['class' => 'stack-xml-compare-inline-deleted']
                );
                $rows[] = self::template_row((object) [
                    'type' => 'deleted',
                    'currentline' => null,
                    'compareline' => $row->compareline,
                    'currenttext' => null,
                    'comparetext' => null,
                ], $currenthtml, null);
                continue;
            }

            $rows[] = self::template_row($row);
        }

        return $rows;
    }

    /**
     * Build template-ready split diff rows.
     *
     * @param string $currentxml the current version XML.
     * @param string $comparexml the selected version XML.
     * @return stdClass[] template rows.
     */
    protected static function split_diff_rows(string $currentxml, string $comparexml): array {
        $rows = [];
        foreach (self::compare_rows($currentxml, $comparexml) as $row) {
            if ($row->type === 'changed') {
                [$currenthtml, $comparehtml] = self::inline_changed_separate($row->currenttext, $row->comparetext);
                $rows[] = self::template_row($row, $currenthtml, $comparehtml);
                continue;
            } else if ($row->type === 'deleted') {
                $comparehtml = html_writer::tag(
                    'span',
                    s($row->comparetext),
                    ['class' => 'stack-xml-compare-inline-deleted']
                );
                $rows[] = self::template_row($row, null, $comparehtml);
                continue;
            }

            $rows[] = self::template_row($row);
        }

        return $rows;
    }

    /**
     * Build one template-ready diff row.
     *
     * @param stdClass $row raw row.
     * @param string|null $currenthtml explicit current-side HTML.
     * @param string|null $comparehtml explicit compared-side HTML.
     * @return stdClass template row.
     */
    protected static function template_row(
        stdClass $row,
        ?string $currenthtml = null,
        ?string $comparehtml = null
    ): stdClass {
        $template = new stdClass();
        $template->type = $row->type;
        $template->currentline = $row->currentline ?? '';
        $template->compareline = $row->compareline ?? '';
        $template->currentclass = self::cell_class('current', $row->currenttext === null && $currenthtml === null);
        $template->compareclass = self::cell_class('compared', $row->comparetext === null && $comparehtml === null);
        $template->currenthtml = self::cell_html($row->currenttext, $currenthtml);
        $template->comparehtml = self::cell_html($row->comparetext, $comparehtml);

        return $template;
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
     * Split text into inline-diff units, keeping XML special constructs intact.
     *
     * Ordinary text is tokenised as words, whitespace runs, and punctuation runs so
     * insertions like "hello " do not get fragmented into character-level quirks.
     * CDATA sections stay atomic, while comments keep their delimiters but still
     * diff the comment body. Processing instructions, entity references,
     * declarations, simple XML tags, and STACK inline markers stay atomic.
     *
     * @param string $text text to split.
     * @return string[] diff units.
     */
    protected static function inline_units(string $text): array {
        $specialpattern = '/(<!\\[CDATA\\[.*?\\]\\]>|<!--.*?-->|<\\?.*?\\?>|<![^>]*>|&(#[0-9]+|#x[0-9A-Fa-f]+' .
                          '|[A-Za-z][A-Za-z0-9]+);|' .
                          '<\\/?[A-Za-z][A-Za-z0-9:-]*(?:\\s*)?>|\\[\\[\\/?[A-Za-z][^\\]]*\\]\\]|\\{@[^@]*@\\})/us';
        $units = [];
        $offset = 0;
        $length = strlen($text);

        while ($offset < $length && preg_match($specialpattern, $text, $match, PREG_OFFSET_CAPTURE, $offset)) {
            $matchoffset = $match[0][1];
            $matchtext = $match[0][0];
            if ($matchoffset > $offset) {
                $units = array_merge($units, self::inline_text_units(substr($text, $offset, $matchoffset - $offset)));
            }

            if (str_starts_with($matchtext, '<!--')) {
                $units[] = '<!--';
                $units = array_merge($units, self::inline_text_units(substr($matchtext, 4, -3)));
                $units[] = '-->';
            } else {
                $units[] = $matchtext;
            }

            $offset = $matchoffset + strlen($matchtext);
        }
        if ($offset < $length) {
            $units = array_merge($units, self::inline_text_units(substr($text, $offset)));
        }

        return $units;
    }

    /**
     * Split plain text into word, whitespace, and punctuation units.
     *
     * @param string $text text to split.
     * @return string[] text units.
     */
    protected static function inline_text_units(string $text): array {
        if ($text === '') {
            return [];
        }

        $pattern = '/[\\p{L}\\p{N}_\\p{M}]+|\\s+|[^\\p{L}\\p{N}_\\p{M}\\s]+/us';
        $matches = [];
        $count = preg_match_all($pattern, $text, $matches);
        if ($count === false || $count === 0) {
            return self::chars($text);
        }

        return $matches[0];
    }

    /**
     * Render inline character changes for two changed lines shown as separate rows.
     *
     * @param string $currenttext current version line.
     * @param string $comparetext selected version line.
     * @return string[] current-added HTML and compared-deleted HTML.
     */
    public static function inline_changed_separate(string $currenttext, string $comparetext): array {
        $current = self::inline_units($currenttext);
        $compare = self::inline_units($comparetext);
        $currentcount = count($current);
        $comparecount = count($compare);

        if ($currentcount * $comparecount > 40000) {
            return self::inline_changed_separate_region($current, $compare);
        }

        $currenthtml = '';
        $comparehtml = '';
        foreach (self::inline_chunks($current, $compare) as $chunk) {
            if ($chunk['changed']) {
                if ($chunk['currenttext'] !== '') {
                    $currenthtml .= html_writer::tag(
                        'span',
                        s($chunk['currenttext']),
                        ['class' => 'stack-xml-compare-inline-added']
                    );
                }
                if ($chunk['comparetext'] !== '') {
                    $comparehtml .= html_writer::tag(
                        'span',
                        s($chunk['comparetext']),
                        ['class' => 'stack-xml-compare-inline-deleted']
                    );
                }
                continue;
            }

            $currenthtml .= s($chunk['currenttext']);
            $comparehtml .= s($chunk['comparetext']);
        }

        return [$currenthtml, $comparehtml];
    }

    /**
     * Build inline diff chunks and join changes separated by very small unchanged gaps.
     *
     * @param string[] $current current version characters.
     * @param string[] $compare selected version characters.
     * @return array[] chunks with currenttext, comparetext, and changed keys.
     */
    protected static function inline_chunks(array $current, array $compare): array {
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

        $chunks = [];
        $i = 0;
        $j = 0;
        while ($i < $currentcount && $j < $comparecount) {
            if ($current[$i] === $compare[$j]) {
                self::append_inline_chunk($chunks, $current[$i], $compare[$j], false);
                $i++;
                $j++;
            } else if ($lcs[$i + 1][$j] > $lcs[$i][$j + 1]) {
                self::append_inline_chunk($chunks, $current[$i], '', true);
                $i++;
            } else {
                self::append_inline_chunk($chunks, '', $compare[$j], true);
                $j++;
            }
        }
        while ($i < $currentcount) {
            self::append_inline_chunk($chunks, $current[$i], '', true);
            $i++;
        }
        while ($j < $comparecount) {
            self::append_inline_chunk($chunks, '', $compare[$j], true);
            $j++;
        }

        return self::join_inline_change_gaps($chunks);
    }

    /**
     * Append text to the current inline chunk list.
     *
     * @param array[] $chunks inline chunks.
     * @param string $currenttext current-side text.
     * @param string $comparetext compared-side text.
     * @param bool $changed whether the text should be highlighted.
     */
    protected static function append_inline_chunk(
        array &$chunks,
        string $currenttext,
        string $comparetext,
        bool $changed
    ): void {
        $last = array_key_last($chunks);
        if ($last !== null && $chunks[$last]['changed'] === $changed) {
            $chunks[$last]['currenttext'] .= $currenttext;
            $chunks[$last]['comparetext'] .= $comparetext;
            return;
        }

        $chunks[] = [
            'currenttext' => $currenttext,
            'comparetext' => $comparetext,
            'changed' => $changed,
        ];
    }

    /**
     * Join changed chunks separated by fewer than INLINE_JOIN_GAP unchanged characters.
     *
     * @param array[] $chunks inline chunks.
     * @return array[] merged inline chunks.
     */
    protected static function join_inline_change_gaps(array $chunks): array {
        for ($i = 1; $i < count($chunks) - 1; $i++) {
            if (
                !$chunks[$i]['changed'] &&
                $chunks[$i - 1]['changed'] &&
                $chunks[$i + 1]['changed'] &&
                count(self::chars($chunks[$i]['currenttext'])) < self::INLINE_JOIN_GAP
            ) {
                $chunks[$i]['changed'] = true;
            }
        }

        $merged = [];
        foreach ($chunks as $chunk) {
            self::append_inline_chunk(
                $merged,
                $chunk['currenttext'],
                $chunk['comparetext'],
                $chunk['changed']
            );
        }

        return $merged;
    }

    /**
     * Render separate-row inline changed regions for long lines without building an LCS matrix.
     *
     * @param string[] $current current version characters.
     * @param string[] $compare selected version characters.
     * @return string[] current-added HTML and compared-deleted HTML.
     */
    public static function inline_changed_separate_region(array $current, array $compare): array {
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
}
