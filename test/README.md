# Neechy Tests

## Overview
The tests here are meant to serve the following goals:

- Avoid regressions and eliminate bugs
- Provide a basis for effective collaboration
- Document new bugs and prove they are fixed
- Signal the correctness of new code
- Improve overall code design and quality


## Setup
Neechy tests require PhpUnit to be installed. For installation instructions, see [the PhpUnit docs](http://phpunit.de/manual/3.7/en/installation.html).

I found the PEAR method simple and straightforward.


## To Run
Tests are run from the command line using `phpunit`. From the root `neechy` directory, run the following command:

    phpunit --bootstrap test/bootstrap.php --colors .

To generate coverage reports, install Xdebug following [installation instructions](http://xdebug.org/docs/install) and run like so:

    phpunit --bootstrap test/bootstrap.php --coverage-html test/reports .

HTML reports will be published to the directory `reports` in `test`.


## Comments / Questions
For information on installing PhpUnit:

- http://phpunit.de/manual/3.7/en/installation.html
- http://book.cakephp.org/2.0/en/development/testing.html#installing-phpunit

Feel free to reach out to me, Tom, on Github at [klenwell@gmail.com](https://github.com/klenwell)
