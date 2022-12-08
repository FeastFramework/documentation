![FEAST Framework](https://github.com/FeastFramework/framework/blob/master/logos/feast-transparent-small.png?raw=true)

![PHP Version](https://img.shields.io/packagist/php-v/feast/feast/v3.x-dev)
![License](https://img.shields.io/packagist/l/feast/feast.svg)
[![Docs](https://img.shields.io/badge/docs-quickstart-green.svg)](https://docs.feast-framework.com)

This project is a sample project using FEAST Framework. In addition, it serves as the official repository
for [docs.feast-framework.com](https://docs.feast-framework.com). The project makes use of the MVC system of FEAST as
well as migrations. Additionally, an on-disk cache can be used to save database hits to the docs website.

# Getting started

## Configuring the application

In order to use this project locally (which will allow you to self-host the docs), first
copy `configs/config.local.sample.php` to `configs/config.local.php` then override the values with the appropriate
values.

Create a MySQL database with the same name as the config value of `database.default.name` and give access to
the `database.default.user`

## Initializing the application

From the root project of the folder run `php famine feast:migration:run-all` to initialize the database.

## Fetching the doc info

1. Fetch all releases with the following command `php famine release:generate`. This will download all release info from
   Github and populate your database. To see what code this executes,
   open `modules/CLI/Controllers/ReleaseController.php` and read `ReleaseController::generateGet`.

2. Parse all markdown documentation. with the following command `php famine docs:parse`. This will use Github's api to
   parse the markdown to HTML and save it to the database.

## Speeding up the web application

Once you have set up either [Apache](https://docs.feast-framework.com/install.md#running-on-apache2)
or [nginx](https://docs.feast-framework.com/install.md#running-on-nginx), the docs will function at whatever url you
have configured. Each request will run through generating routes, parsing configs, and fetching database info in
addition to making queries when running. Through both FEAST's
built-in [caching](https://docs.feast-framework.com/cli.md#feastcache) and the Documentation projects cache, all of
these steps can be eliminated resulting in a (not very noticeably on most hosts) faster application!

To cache the routes, configs and dbinfo, run the following
commands: `php famine feast:cache:config-generate && php famine feast:cache:routing-generate && php famine feast:cache:dbinfo-generate`
.

To cache the documentation application run `php famine cache:cache-all` or you can cache individual pieces.
See `php famine cache:cache` for more details.

## Browsing the source
The following folders contain files that are used by the documentation application:
1. `configs`
2. `Controllers`
3. `Mapper`
4. `Migrations`
5. `Model`
6. `Modules/Cli/Controllers`
7. `Views/Index`
8. `Views/Partial`