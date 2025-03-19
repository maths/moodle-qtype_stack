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
 * This script handles the various deploy/undeploy actions from questiontestrun.php.
 *
 * @package    qtype_stack
 * @copyright  2023 RWTH Aachen
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace api\util;

use Slim\Interfaces\ErrorRendererInterface;

// phpcs:ignore moodle.Commenting.MissingDocblock.Class
class ErrorRenderer implements ErrorRendererInterface {
    // phpcs:ignore moodle.Commenting.MissingDocblock.Function
    public function __invoke(\Throwable $exception, bool $displayerrordetails): string {
        $message = $exception instanceof \stack_exception ? $exception->getMessage() :
                                            "An Error occured while processing the question";
        return json_encode([
            'message' => $message,
        ]);
    }

}
