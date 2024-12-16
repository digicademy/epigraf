---
title: Servers
nav_order: 10
---

# {{ page.title }}

## Installation
Epigraf needs a web server, a relational database management system and a cache server.
You find prepared docker setups in the docker folder of the repository.
The setups use Apache, MariaDB, and Redis. See `docker/readme.md` for further guidance.

Provided we've done everything right, you don't need to read any further here,
but can pull the setups straight off the shelf.
We probably haven't done everything right. Or the world has moved on.
In that case, the following notes may well come in handy.

A typical installation process is as follows:
- Clone the repository.
- Run `composer install --no-dev` which also runs `npm run build`.
- Configure the database and cache server connections in `config/app.php`.
- Initialize the database with `bin/cake database init`.
- Add an admin user with `bin/cake user add admin admin admin admin`.
- Clear the cache with `bin/cake cache clear_all`.

To install, build, and configure the application the following command line tools are used:
- **Composer**: `composer install` installs the application dependencies, initialises missing configurations, and compiles all frontend assets using webpack.
  See `composer.json` for dependencies and `src/Console/Installer.php` for what happens behind the scenes after the dependencies are installed.
- **Webpack**: `npm run build` compiles the frontend assets, including minimized JavaScript and CSS files, images, and translation files.
  See `package.json` for details about the available commands and `webpack.config.js` for the configuration.
- **CakePHP**: `bin/cake` is the command line interface for CakePHP. Epigraf implements custom commands and actions for managing the application.
  Call `bin/cake` without arguments to display a list of available commands.
  Call a command with the `--help` option to display usage information, for example `bin/cake database --help`.

Specific command actions are:
- `bin/cake database init` initializes the main and, optionally, project databases. Project databases can be created from presets.
- `bin/cake database import` imports a SQL database dump, for example to restore a backup.
- `bin/cake user add` adds a user to the application database.
- `bin/cake user remove` deletes a user from the application database.
- `bin/cake permission add` grants permissions to a user.
- `bin/cake cache clear_all` clears the cache.

## Configuration

Database and cache server connections are configured in the `config/app.php` file.
A blueprint is provided in the `config/app.default.php` file.

See the docker folder for examples how to configure the servers for different environments:
- Development: `docker/php/app.php`.
- Test: `docker/test/app.php`.
- Production: `docker/deploy/app.php`.

## Logs
Logs of the Epigraf application are
configured in the `config.php` file and
stored in the `logs` directory within the application root folder:

- `logs/error.log` logs error messages and exceptions.
- `logs/debug.log` logs debug messages. When the debug mode is enabled in the app.php file,
  debug messages are displayed in the browser and not written to the log.
  Further, the cache is disabled in debug mode.
  Therefore, disable the debug mode in production environments.
- `logs/queries.log` logs SQL queries. By default, queries are not logged.
  To enable query logging, set the `log` key in the `Datasources` to true in the `app.php` file.

The error log can also be accessed from the Epigraf main menu.
Click the settings icon and choose the log menu item.
