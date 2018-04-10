# Unit testing the STACK API.

Unit testing is carried out through PHPUnit.

1. Install PHPUnit in the directory you have as '$CFG->wwwroot'

    wget https://phar.phpunit.de/phpunit.phar
    chmod +x phpunit.phar
    mv phpunit.phar phpunit

More instructions are given in [https://phar.phpunit.de/phpunit-5.7.phar](https://phar.phpunit.de/phpunit-5.7.phar).

2. To run the unit tests

    php phpunit api/tests/apilib_test.php
    php phpunit api/tests/api_test.php

# Coding standards

The main codebase is part of Moodle.  Hence we conform to Moodle coding standards.  These are described here: [https://docs.moodle.org/dev/Coding_style](https://docs.moodle.org/dev/Coding_style).  Note Moodle has a plugin which automatically checks coding style.  This is the easiest way to ensure code conforms.