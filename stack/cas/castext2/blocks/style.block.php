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
//
defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../block.interface.php');
require_once(__DIR__ . '/../../../utils.class.php');

/**
 * A block for dealing with style in IFRAME blocks.
 *
 * This is meant to act as a replacement for <style> and combines with
 * the [[iframe]]-family of blocks so the content gets positioned into
 * the <head> instead of the <body>.
 */
class stack_cas_castext2_style extends stack_cas_castext2_block {

    public function compile($format, $options): ?MP_Node {
        $r = new MP_List([
            new MP_String('style'),
            new MP_String(json_encode($this->params))
        ]);

        if (!isset($options['in iframe'])) {
            return new MP_String(' ERROR [[style]] blocks must be within iframes. ');
        }

        // All formatting assumed to be raw HTML here.
        $frmt = castext2_parser_utils::RAWFORMAT;

        foreach ($this->children as $child) {
            $c = $child->compile(castext2_parser_utils::RAWFORMAT, $options);
            if ($c !== null) {
                $r->items[] = $c;
            }
        }

        return $r;
    }

    public function is_flat(): bool {
        // These are never flat.
        return false;
    }

    public function validate_extract_attributes(): array {
        // No CAS arguments.
        return [];
    }

    public function postprocess(array $params, castext2_processor $processor): string {

        $parameters = json_decode($params[1], true);
        $content    = '';
        for ($i = 2; $i < count($params); $i++) {
            if (is_array($params[$i])) {
                $content .= $processor->process($params[$i][0], $params[$i]);
            } else {
                $content .= $params[$i];
            }
        }

        $attributes = [];

        foreach (['href', 'media', 'blocking', 'title', 'nonce', 'type'] as $attr) {
            if (isset($parameters[$attr])) {
                $attributes[$attr] = $parameters[$attr];
            }
        }

        // No need if nothing to do.
        if (!isset($attributes['href']) && trim($content) === '') {
            return '';
        }

        // Provide a way to reference content served out through the CORS directory.
        if (isset($attributes['href']) && strpos($attributes['href'], 'cors://') === 0) {
            $attributes['href'] = stack_cors_link(substr($attributes['href'], 7));
        }

        if (isset($attributes['href'])) {
            $attributes['rel'] = 'stylesheet';
            return html_writer::tag('link', '', $attributes);
        } else {
            unset($attributes['href']);
            return html_writer::tag('style', $content, $attributes);
        }
    }
}
