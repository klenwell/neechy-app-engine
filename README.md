# Neechy
[![Build Status](https://travis-ci.org/klenwell/neechy.svg)](https://travis-ci.org/klenwell/neechy)

Lightweight wiki platform written in PHP aimed at individuals and small groups


## Installation
Using git and the command line installation script, you can get set up quickly:

1. Clone repository:

    git clone https://github.com/klenwell/neechy.git neechy

2. Create a mysql user (with appropriate password):

    mysql -uroot -p -e "
     CREATE USER 'neechy'@'localhost' IDENTIFIED BY '<PASSWORD>';
     GRANT ALL PRIVILEGES ON * . * TO 'neechy'@'localhost';
     FLUSH PRIVILEGES;
    "

3. Run the console install script:

    cd neechy
    php console/run.php install

4. Once installed, you can test the site locally by running the PHP development server:

    php -S localhost:3000 -t public/


## Tests
Neechy tests require PhpUnit to be installed. For installation instructions, see [the PhpUnit docs](http://phpunit.de/manual/3.7/en/installation.html). I found the PEAR method simple and straightforward.

Tests are run from the command line using `phpunit`. From the root `neechy` directory, run the following command:

    phpunit --bootstrap test/bootstrap.php --colors .

For more information on tests, see the README in the test directory.


## Development

### Models

### Handlers

### Tasks

### Themes

### Capsules
