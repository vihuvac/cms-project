Víctor Hugo Valle Website
=========================

What's inside?
--------------

The website comes pre-configured with the following bundles:

* Bundles from Symfony Standard distribution
* SonataAdminBundle - The missing Symfony2 Admin Generator
* SonataMediaBundle
* SonataPageBundle
* SonataUserBundle
* SonataEasyExtendsBundle
* SonataIntlBundle
* SonataNewsBundle
* SonatajQueryBundle
* FOSUserBundle


Installation
------------

Install composer::

    curl -s https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin

Go into the root folder of the project and run::

    php app/check.php

Fix any errors that you may encounter there. When that's finished, run::

    composer.phar install

That will install all the external libraries.

Configure the ``parameters.yml`` file before going creating the configs for other envs and going ahead with the database.
::
    cp app/config/parameters.yml.sample app/config/parameters.yml
    edit the app/config/parameters.yml file

Once configured the ``parameters.yml`` file just run::

    cp app/config/parameters.yml app/config/production_parameters.yml
    cp app/config/parameters.yml app/config/validation_parameters.yml

Database initialization
~~~~~~~~~~~~~~~~~~~~~~~

At this point, the ``app/console`` command should start with no issues.

Then runs the commands::

    php app/console doctrine:database:create
    php app/console doctrine:schema:update --dump-sql
    php app/console doctrine:schema:update --force

Assets Installation
~~~~~~~~~~~~~~~~~~~

Your frontend still looking weird because bundle assets are not installed. Run the following command to install assets for all active bundles under public directory::

    php app/console assets:install --symlink web

Fixtures
~~~~~~~~

To have some actual data in the DB, we should load the fixtures by running::

    php app/console doctrine:fixtures:load

Sonata Page Bundle
~~~~~~~~~~~~~~~~~~

By default the Sonata Page bundle is activated, so you need to starts 2 commands before going further::

    php app/console sonata:page:create-site --enabled=true --name=localhost --host=localhost --relativePath=/ --enabledFrom=now --enabledTo="+10 years" --default=1
    php app/console sonata:page:update-core-routes --site=all
    php app/console sonata:page:create-snapshots --site=all

Note:

In the previous version the parameter ``--default`` was passed with a ``true`` value, then it was ``--default=true``. Right now its value has been replaced by an integer value, that's why the last parameter of the command was modified to ``--default=1``.
The ``update-core-routes`` populates the database with ``page`` from the routing information.
The ``create-snapshots`` create a snapshot (a public page version) from the created pages.


Unit Testing
------------

Automatic Unit Testing with ``watchr``::

    gem install watchr
    cd /path/to/symfony-project
    watchr phpunit.watchr


reference : https://gist.github.com/1151531

Done!