# Neechy
[![Build Status](https://travis-ci.org/klenwell/neechy.svg)](https://travis-ci.org/klenwell/neechy)

Lightweight wiki platform written in PHP aimed at developers and small groups.


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

3. Prepare the test configuration file:

    cd neechy
    cp -v config/test.conf.php{-dist,}

Update the database user and password settings.

4. Run the developer server

    php -S localhost:3000 -t public/

You should now be able to access a development version of the site at:

- http://localhost:3000/

For a full installation, repeat steps 1 and 2 above, then run the console install script:

    php console/run.php install


## Tests
Neechy tests require PhpUnit to be installed. For installation instructions, see [the PhpUnit docs](http://phpunit.de/manual/3.7/en/installation.html). I found the PEAR method simple and straightforward.

Tests are run from the command line using `phpunit`. From the root `neechy` directory, run the following command:

    phpunit --bootstrap test/bootstrap.php --colors .

For more information on tests, see the README in the test directory.


## Development

### Handlers
Neechy is organized around handler. These are pluggable libraries or packages that provide a coherent set of features, much like applications in Django. User requests are mapped to handlers through the URL as follows:

- http://my-domain.com/handler/action

Thus, **http://my-domain.com/page/home** would route a request to the core **PageHandler** which is responsible for simply displaying the **home** page to the viewer.

Neechy includes a core set of handlers. Use these as examples for your own handler. If you wish to add new features for your application, it is recommended that you add a handler directory to your app directory. Each handler must have a unique name.
