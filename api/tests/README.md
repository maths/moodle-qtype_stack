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