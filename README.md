CMS Project in Symfony2
=========================

What's inside?
--------------

This project comes pre-configured with the following bundles:

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


## Installation

Install composer:

```
curl -s https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin
```

Go into the root folder of the project and run:

```
php app/check.php
```

Fix any errors that you may encounter there. When that's finished, run:

```
composer.phar install
```

That will install all the external libraries.

Configure the ```parameters.yml``` file before going creating the configs for other envs and going ahead with the database.

```
cp app/config/parameters.yml.dist app/config/parameters.yml
```

Then edit the ```app/config/parameters.yml``` file.


## Database initialization

At this point, the ```app/console``` command should work with no issues. We should use **doctrine migrations** everytime we want to **update** or **upgrade** our database, it is much better due to we can prevent several errors. In this case the migration is already configured so we do not need to run a ```diff``` command, so just run:

```
php app/console doctrine:migrations:migrate
```

## Assets Installation

Your frontend still looking weird because bundle assets are not installed. Run the following command to install assets for all active bundles under public directory.

```
php app/console assets:install --symlink web
```

## Sonata Page Bundle

By default the Sonata Page bundle is activated, so you need to starts 2 commands before going further.

```
php app/console sonata:page:create-site --enabled=true --name=localhost --host=localhost --relativePath=/ --enabledFrom=now --enabledTo="+10 years" --default=1
php app/console sonata:page:update-core-routes --site=all
php app/console sonata:page:create-snapshots --site=all
```

> **Note**:
> 
> In the previous version the parameter ```--default``` was passed with a ```true``` value. Right now its value has been replaced by an integer value, that's why the last parameter of the command was modified to ```--default=1```.
> 
>The ```update-core-routes``` populates the database with ```page``` from the routing information.
> 
> The ```create-snapshots``` create a snapshot (a public page version) from the created pages.

## Fixtures

To have some actual data in the DB, we should load the fixtures by running:

```
php app/console doctrine:fixtures:load
```


**Done!**