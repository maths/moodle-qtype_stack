<?php
// This is a temporary file.  At some point this functionality will be merged into atdecplaces.class.php.

/* This function counts the number of significant digits in a string.
 * 
 * The significant figures of a number are digits that carry meaning contributing to its measurement resolution.
 * This includes all digits except:
 *     All leading zeros;
 *     Trailing zeros when they are merely placeholders to indicate the scale of the number.
 * 
 * We can only give upper and lower bounds, because in some cases we can't tell exactly what is and
 * is not significant.
 */
require_once(__DIR__ . '/../utils.class.php');

// In this text digits are 1-9 and 0 is not a digit.
// array("string", lower, upper)
   $tests = array(
        array("0", 1, 1),  // Decision: zero has one significant digit.
        array("0.0", 1, 1), // Decision: 0.0 has one significant digit.
        array("0.00", 2, 2),
        array("00.00", 2, 2),
        array("0.000", 3, 3),
        array("0.0001", 1, 1), // Leading zeros are insignificant.
        array("0.0010", 2, 2), 
        array("100.0", 4, 4), // Existence of a significant zero (or digit) changes
                           // all insignificant zeros between it and the previous digit to significant.
        array("100.", 3, 3),
        array("00120", 2, 3),
        array("00.120", 3, 3),
        array("1.001", 4, 4),
        array("2.000", 4, 4),
        array("1234", 4, 4),
        array("123.4", 4, 4),
        array("2000", 1, 4),
        array("10000", 1, 5),
        array("2001", 4, 4),
        array("0.01030", 4, 4),
        // Scientific notation.
        array("4.320e-3", 4, 4), // After a digit, zeros after the decimal separator are always significant.
        array("0.020e3", 2, 2), // If no digits before a zero that zero is not significant even after the decimal separator.
        array("1.00e3", 3, 3),
        array("10.0e1", 3, 3),
        // Unary signs.
        array("+334.3", 4, 4),
        array("-0.00", 2, 2),
        array("-12.00", 4, 4),
        array(" -121000", 3, 6),
        array("-303.30003", 8, 8),
        // We insist the input only has one numerical multiplier that we act on and that is the first thing in the string.
        array("52435*mg", 5, 5),
        array("1030*m/s", 3, 4), // Here we know that there are 3 significant figures but can't be sure about that trailing zero.
        array("1.23*4", 3, 3),
        array("4*3.21", 1, 1),
        array("50*3.21", 1, 2),
        array("3434...34*34", 4, 4),
      );

echo "<pre>";
foreach ($tests as $t) {
    $r = stack_utils::decimal_digits($t[0]);
    $passed = true;
    $message = '';
    if ($r['lowerbound'] != $t[1]) {
        $passed = false;
        $message .= 'Expected lower bound: '. $t[1] . '. ';
    }
    if ($r['upperbound'] != $t[2]) {
        $passed = false;
        $message .= 'Expected upper bound: '. $t[2] . '. ';
    }
    echo $t[0];
    echo ", {$r['lowerbound']}, {$r['upperbound']}";
    if (!$passed) { echo ' | '.$message; }
    echo "<br>";
}

echo "<hr>";
print_r(stack_utils::decimal_digits("00.00"));

echo "</pre>";

