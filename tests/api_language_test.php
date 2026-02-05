<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Unit tests for the Stack question type API.
 *
 * @package    qtype_stack
 * @copyright 2023 University of Edinburgh.
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace qtype_stack;

use ApiLanguage;

defined('MOODLE_INTERNAL') || die();
require_once(__DIR__ . '../../api/emulation/Language.php');

/**
 * Allows mocking
 * phpcs:disable PSR1.Classes.ClassDeclaration.MultipleClasses
 */
class fake_api_language extends ApiLanguage {
    /**
     * Override so ignored during testing
     *
     * @param string $requestedlanguage
     * @return bool
     */
    public static function install_language_safe($requestedlanguage) {
        switch ($requestedlanguage) {
            case 'es_mx':
                return false;
            default:
                return true;
        }
    }
}

/**
 * Add description here.
 * @group qtype_stack
 * @covers \qtype_stack
 */
final class api_language_test extends \advanced_testcase {
    /** @var apilanguage mocked apilanguage */
    public ApiLanguage $apilanguage;
    public function setUp(): void {
        parent::setUp();
        $this->resetAfterTest();
    }

    public function test_lang(): void {

        // No setting. German.
        $language = fake_api_language::api_current_language('de');
        $this->assertEquals('de', $language);

        // No setting. English.
        $language = fake_api_language::api_current_language('en');
        $this->assertEquals('en', $language);

        // No setting. Other.
        $language = fake_api_language::api_current_language('pt');
        $this->assertEquals('en', $language);

        // No wildcard.
        \set_config('supportedlanguages', 'en,de', 'qtype_stack');
        $language = fake_api_language::api_current_language('en_us');
        $this->assertEquals('en', $language);

        // Wildcard. Basic language.
        \set_config('supportedlanguages', 'en,de,*', 'qtype_stack');
        $language = fake_api_language::api_current_language('pt');
        $this->assertEquals('pt', $language);

        // Wildcard. Region.
        \set_config('supportedlanguages', 'en,de,*', 'qtype_stack');
        $language = fake_api_language::api_current_language('pt_br');
        $this->assertEquals('pt_br', $language);

        // Wildcard. Variant.
        \set_config('supportedlanguages', 'en,de,*', 'qtype_stack');
        $language = fake_api_language::api_current_language('pt_br_wp');
        $this->assertEquals('pt_br_wp', $language);

        // Wildcard. Variant. Region missing.
        \set_config('supportedlanguages', 'en,de,*', 'qtype_stack');
        $language = fake_api_language::api_current_language('es_mx_wp');
        $this->assertEquals('es_mx_wp', $language);

        // Variant. Region only.
        \set_config('supportedlanguages', 'en,de,pt_br', 'qtype_stack');
        $language = fake_api_language::api_current_language('pt_br_wp');
        $this->assertEquals('pt_br', $language);
    }
}
