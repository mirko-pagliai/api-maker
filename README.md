# php-doc-maker

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://api.travis-ci.org/mirko-pagliai/php-doc-maker.svg?branch=master)](https://travis-ci.org/mirko-pagliai/php-doc-maker)
[![Build status](https://ci.appveyor.com/api/projects/status/o4pygqsu130vm5m2?svg=true)](https://ci.appveyor.com/project/mirko-pagliai/php-doc-maker)
[![codecov](https://codecov.io/gh/mirko-pagliai/php-doc-maker/branch/master/graph/badge.svg)](https://codecov.io/gh/mirko-pagliai/php-doc-maker)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/be8535f48eff4ed9913545a990c25d86)](https://www.codacy.com/manual/mirko.pagliai/php-doc-maker?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=mirko-pagliai/php-doc-maker&amp;utm_campaign=Badge_Grade)

*php-doc-maker* is a command line tool that automatically generates the documentation
of your PHP project.

![enter image description here](https://github.com/mirko-pagliai/php-doc-maker/raw/master/docs/screenshot.jpg)

It requires at least PHP 7.1 and `phpunit` 7 or 8.

Yes, the documentation for *php-doc-maker* was also generated with the same and you can find it [here](//mirko-pagliai.github.io/php-doc-maker).

Did you like this library? Its development requires a lot of time for me.
Please consider the possibility of making [a donation](//paypal.me/mirkopagliai):
even a coffee is enough! Thank you.

[![Make a donation](https://www.paypalobjects.com/webstatic/mktg/logo-center/logo_paypal_carte.jpg)](//paypal.me/mirkopagliai)

## How it work
You can install the package via Composer (if you want to install the package globally, see below)

```bash
$ composer require --dev --prefer-dist mirko-pagliai/php-doc-maker
```

Then you can run the command (the package has been installed in `vendor/mirko-pagliai/php-doc-maker`):

```bash
vendor/mirko-pagliai/php-doc-maker/bin/php-doc-maker make
```

This is an example of output:
```bash
$ vendor/mirko-pagliai/php-doc-maker/bin/php-doc-maker make --title "My project" -t docs

Sources directory: /home/mirko/Libs/php-doc-maker/tests/test_app
Target directory: docs
====================================================================================================================================================
Founded 13 classes
Founded 6 functions
Rendered menu element
Rendered index page
Rendered functions page
Rendered class page for App\Animals\Animal
Rendered class page for App\Animals\Cat
Rendered class page for App\Animals\Dog
Rendered class page for App\Animals\Horse
Rendered class page for App\Animals\Traits\ColorsTrait
Rendered class page for App\Animals\Traits\PositionTrait
Rendered class page for App\ArrayExample
Rendered class page for App\DeprecatedClassExample
Rendered class page for App\FileArrayExample
Rendered class page for App\Vehicles\Car
Rendered class page for App\Vehicles\MotorVehicle
Rendered class page for App\Vehicles\Vehicle
Elapsed time: 4.85 seconds
```

Look for example at the [`php-doc-maker` documentation](//mirko-pagliai.github.io/php-doc-maker), built by itself.

You can see all the available options using the `--help` option (or `-h`):
```bash
$ vendor/mirko-pagliai/php-doc-maker/bin/php-doc-maker make -h
Usage:
make [options] [--] [<source>]

Arguments:
source Path from which to read the sources. If not specified, the current directory will be used

Options:
--debug Enables debug
--no-cache Disables cache
-t, --target=TARGET Target directory where to generate the documentation. If not specified, the `output` directory will be created
--title=TITLE Title of the project. If not specified, the title will be self-determined using the name of the source directory
-h, --help Display this help message
-q, --quiet Do not output any message
-V, --version Display this application version
--ansi Force ANSI output
--no-ansi Disable ANSI output
-n, --no-interaction Do not ask any interactive question
-v|vv|vvv, --verbose Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug
```

### Global installation
Suppose you want to use *php-doc-maker* for multiple projects and libraries, and therefore don't install it for each of them.

In this case, you can clone the git repository (e.g. in your user home, or elsewhere):
```bash
$ git clone git@github.com:mirko-pagliai/php-doc-maker.git php-doc-maker
Cloning into 'php-doc-maker'...
remote: Enumerating objects: 1373, done.
remote: Counting objects: 100% (1373/1373), done.
remote: Compressing objects: 100% (637/637), done.
remote: Total 1373 (delta 817), reused 1177 (delta 621), pack-reused 0
Ricezione degli oggetti: 100% (1373/1373), 598.74 KiB | 1.58 MiB/s, done.
Risoluzione dei delta: 100% (817/817), done.
```
And then install the dependencies with Composer:
```bash
$ cd php-doc-maker
$ composer install
```

Finally, edit the `.bash_aliases` file (in your user home), adding an alias:
```bash
alias php-doc-maker='/home/mirko/php-doc-maker/bin/php-doc-maker make'
```

Now you can run the `php-doc-maker` command anywhere.

## Versioning
For transparency and insight into our release cycle and to maintain backward
compatibility, *php-doc-maker* will be maintained under the
[Semantic Versioning guidelines](http://semver.org).
