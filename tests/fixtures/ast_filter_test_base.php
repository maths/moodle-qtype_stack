<?php
// This file is part of Stack - https://stack.maths.ed.ac.uk
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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../locallib.php');
require_once(__DIR__ . '/../../stack/maximaparser/corrective_parser.php');
require_once(__DIR__ . '/../../stack/cas/parsingrules/parsingrule.factory.php');
require_once(__DIR__ . '/../../stack/cas/cassecurity.class.php');


/**
 * This is a base-class for automatically generated AST-filter tests
 *
 * There is a CLI-script to generate and update those test. You must
 * not modify those tests by hand and they exist just so that you can
 * see what happened when they were created and that things still work
 * the same.
 *
 * @copyright  2019 Aalto University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class qtype_stack_ast_testcase extends basic_testcase {

    /**
     * @var stack_cas_astfilter The filter being tested.
     */
    public $filter = null;

    /**
     * @var stack_cas_security The security being used.
     */
    public $security = null;

    /**
     * The generic declaration of what is expected.
     *
     * @param string $input
     * @param string $result
     * @param array $notes
     * @param bool $valid
     * @param bool $errors
     */
    public function expect(string $input, string $result, $notes=array(),
                           $valid=true, $errors=false) {
        // We currently ignore these but lets collect them.
        $parsererrors = array();
        $parsernotes = array();

        // Parse it, remember that these tests only act on parseable strings.
        $ast = maxima_corrective_parser::parse($input, $parsererrors, $parsernotes,
                                               array('startRule' => 'Root',
                                               'letToken' => stack_string('equiv_LET')));

        $filtererrors = array();
        $filternotes = array();

        $filtered = $this->filter->filter($ast, $filtererrors,
                                          $filternotes, $this->security);

        // What notes we expect there to be.
        foreach ($notes as $key => $value) {
            $this->assertArrayHasKey($key, $filternotes);
            $this->assertSame($value, $filternotes[$key]);
        }

        // If it is supposed to become invalid.
        if ($valid === true) {
            $this->assert_not_marked_invalid($filtered);
        } else {
            $this->assert_marked_invalid($filtered);
        }

        // If we expect errors to be generated.
        if ($errors === true) {
            $this->assertNotEmpty($filtererrors);
        } else {
            $this->assertEquals([], $filtererrors);
        }

        // Finally, check that the result string is equivalent.
        $this->assertEquals($result, $filtered->toString(array('nosemicolon' => true)));
    }

    /**
     * @param MP_Node $ast
     */
    public function assert_marked_invalid($ast) {
        $hasinvalid = false;
        $findinvalid = function($node) use(&$hasinvalid) {
            if (isset($node->position['invalid']) && $node->position['invalid'] === true) {
                $hasinvalid = true;
                return false;
            }
            return true;
        };
        $ast->callbackRecurse($findinvalid);

        $this->assertTrue($hasinvalid);
    }

    /**
     * @param MP_Node $ast
     */
    public function assert_not_marked_invalid($ast) {
        $hasinvalid = false;
        $findinvalid = function($node) use(&$hasinvalid) {
            if (isset($node->position['invalid']) && $node->position['invalid'] === true) {
                $hasinvalid = true;
                return false;
            }
            return true;
        };
        $ast->callbackRecurse($findinvalid);

        $this->assertFalse($hasinvalid);
    }

    /**
     * Asserts that an array has a specified subset.
     *
     * This assert used to be provided by PHPUnit but they removed it
     * without replacement. Therefore, we have a copy of it here.
     *
     * @param array|ArrayAccess $subset
     * @param array|ArrayAccess $array
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws Exception
     */
    public function assertArraySubset($subset, $array, bool $checkForObjectIdentity = false, string $message = ''): void
    {
        if (!(is_array($subset) || $subset instanceof ArrayAccess)) {
            throw \PHPUnit\Framework\InvalidArgumentException::create(
                1,
                'array or ArrayAccess'
            );
        }
        if (!(is_array($array) || $array instanceof ArrayAccess)) {
            throw \PHPUnit\Framework\InvalidArgumentException::create(
                2,
                'array or ArrayAccess'
            );
        }
        $constraint = new ArraySubset($subset, $checkForObjectIdentity);
        \PHPUnit\Framework\Assert::assertThat($array, $constraint, $message);
    }
}


/**
 * Constraint that asserts that the array it is evaluated for has a specified subset.
 *
 * Uses array_replace_recursive() to check if a key value subset is part of the
 * subject array.
 *
 * PHPUnit removed this, but we need it, so adding it back here.
 */
final class ArraySubset extends PHPUnit\Framework\Constraint\Constraint {
    /**
     * @var iterable
     */
    private $subset;

    /**
     * @var bool
     */
    private $strict;

    public function __construct(iterable $subset, bool $strict = false) {
        $this->strict = $strict;
        $this->subset = $subset;
    }

    /**
     * Evaluates the constraint for parameter $other
     *
     * If $returnResult is set to false (the default), an exception is thrown
     * in case of a failure. null is returned otherwise.
     *
     * If $returnResult is true, the result of the evaluation is returned as
     * a boolean value instead: true in case of success, false in case of a
     * failure.
     *
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function evaluate($other, string $description = '', bool $returnResult = false): ?bool {
        //type cast $other & $this->subset as an array to allow
        //support in standard array functions.
        $other = $this->toArray($other);
        $this->subset = $this->toArray($this->subset);
        $patched = array_replace_recursive($other, $this->subset);
        if ($this->strict) {
            $result = $other === $patched;
        } else {
            $result = $other == $patched;
        }
        if ($returnResult) {
            return $result;
        }
        if (!$result) {
            $f = new \SebastianBergmann\Comparator\ComparisonFailure(
                    $patched,
                    $other,
                    var_export($patched, true),
                    var_export($other, true)
            );
            $this->fail($other, $description, $f);
        }
        return null;
    }

    /**
     * Returns a string representation of the constraint.
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function toString(): string
    {
        return 'has the subset ' . $this->exporter()->export($this->subset);
    }

    /**
     * Returns the description of the failure
     *
     * The beginning of failure messages is "Failed asserting that" in most
     * cases. This method should return the second part of that sentence.
     *
     * @param mixed $other evaluated value or object
     *
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    protected function failureDescription($other): string
    {
        return 'an array ' . $this->toString();
    }

    private function toArray(iterable $other): array
    {
        if (is_array($other)) {
            return $other;
        }
        if ($other instanceof ArrayObject) {
            return $other->getArrayCopy();
        }
        if ($other instanceof Traversable) {
            return iterator_to_array($other);
        }
        // Keep BC even if we know that array would not be the expected one
        return (array) $other;
    }
}
