# Neechy for App Engine

[Neechy wiki platform](https://github.com/klenwell/neechy-app-engine) tailored for [Google PHP App Engine runtime environment](https://cloud.google.com/appengine/docs/php/).

A demo version of the site is available here:

- https://neechy-demo.appspot.com/

But, be advised, this project is still very early in its development.


## Installation

Using git and the command line installation script, you can get set up quickly:

1. Clone repository:

    ```
    git clone https://github.com/klenwell/neechy-app-engine.git neechy-app-engine
    ```

2. Create a mysql user (with appropriate password) for local dev server:

    ```
    mysql -uroot -p -e "
     CREATE USER 'neechy'@'localhost' IDENTIFIED BY '<PASSWORD>';
     GRANT ALL PRIVILEGES ON * . * TO 'neechy'@'localhost';
     FLUSH PRIVILEGES;
    "
    ```

3. Prepare the app configuration file to run dev server:

    ```
    cd neechy
    cp -v config/app.conf.php{-dist,}
    ```

    Update the database user and password settings under the `default` key.

4. Run the local Google App Engine development server at port 3333 from the project root directory:

    ```
    cd ~/projects/neechy-app-engine
    dev_appserver.py --port=3333 --admin_port=3334 --api_port=3335 \
      --php_executable_path=/usr/bin/php-cgi \
      ./
    ```

    On Linux, specify the PHP executable path. To locate:

        which php

You should now be able to access a development version of the site at:

- http://localhost:3333/

Log in as an admin user to access the admin interface:

- http://localhost:3333/admin


## Tests
Neechy tests require PhpUnit to be installed. For installation instructions, see [the PhpUnit docs](http://phpunit.de/manual/3.7/en/installation.html). I found the PEAR method simple and straightforward.

Prepare the test configuration file:

    cd neechy
    cp -v config/test.conf.php{-dist,}

Tests are run from the command line using `phpunit`. From the root `neechy` directory, run the following command:

    phpunit --bootstrap test/bootstrap.php --colors .

For more information on tests, see the README in the test directory.


## Deployment
You'll need to first create an [App Engine PHP project](https://cloud.google.com/appengine/docs/php/) and set up a [Cloud SQL instance](https://cloud.google.com/sql/docs/getting-started) with a database. Update the settings in the `cloud` section of your `app.conf.php` config file.

To deploy:

    appcfg.py -A <PROJECT-ID> --no_cookies --noauth_local_webserver -e <USER-NAME> update app.yaml

Visit the admin page to install the database tables:

- http://<MY-SITE>.appspot.com/admin
