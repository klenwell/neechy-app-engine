# Neechy
[![Build Status](https://travis-ci.org/klenwell/neechy.svg)](https://travis-ci.org/klenwell/neechy)

Lightweight wiki platform written in PHP aimed at individuals and small groups


## Installation
The command line installation script will walk you through the process. From the root `neechy` directory, run the following command:

    php console/run.php install

You will need to create a MySQL database for your application.

Once installed, you can test the site by running the PHP development server:

    php -S localhost:3000 -t public/


## Tests
Tests are run from the command line using `phpunit`. From the root `neechy` directory, run the following command:

    phpunit --bootstrap test/bootstrap.php --colors .

Neechy tests require PhpUnit to be installed. For installation instructions, see [the PhpUnit docs](http://phpunit.de/manual/3.7/en/installation.html).

I found the PEAR method simple and straightforward.


## Development

### Models

### Handlers

### Tasks

### Themes

### Capsules
