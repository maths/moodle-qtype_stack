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

// This script handles the various deploy/undeploy actions from questiontestrun.php.
//
// @copyright  2023 RWTH Aachen
// @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.

namespace api\util;

class StackSeedHelper {
    public static function initialize_seed($question, $seed) {
        if ($question->has_random_variants()) {
            // We require the xml to include deployed variants.
            if (count($question->deployedseeds) === 0) {
                throw new \stack_exception(get_string('api_no_deployed_variants', null));
            }

            // If no seed has been specified, use the first deployed variant.
            if (!$seed) {
                $seed = $question->deployedseeds[0];
            }

            if (!in_array($seed, $question->deployedseeds)) {
                throw new \stack_exception(get_string('api_seed_not_in_variants', null));
            }

            $question->seed = $seed;
        } else {
            // We just set any seed here.
            $question->seed = -1;
        }
    }
}
