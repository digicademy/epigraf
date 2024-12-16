# Docker files for testing, building and deployment

## Docker files
The docker files differ in the way Epigraf comes to life:

- **apache/Dockerfile**: An Apache webserver configured to run Epigraf.
  The image is used in the docker-compose.yml in combination with a PHP FPM container.
- **php/Dockerfile**: A PHP FPM image containing everything needed to run Epigraf.
  The image is used in the docker-compose.yml in combination with an Apache webserver.
- **deploy/Dockerfile**: This docker file is used to build the image for deployment.
  It includes the Epigraf source code, Epigraf is directly installed into the image.
  Based on the Apache/PHP 8.3 FPM image, a webserver and a database server
  are expected to be provided by the hosting environment.
- **test/Dockerfile**: This is the docker file for the GitLab Runner in the test system.
  The container does't include the Epigraf source code.
  Instead, the application code is cloned in the GitLab pipeline.
  The test environment needs to provide a database server.

What's included in the images:

| Dockerfile | Epigraf | Apache | PHP     | MariaDB | Redis |
|------------|---------|:-------|:--------|:--------|:------|
| **deploy** | Yes     | No     | 8.3 FPM | No      | No    |
| **php**    | No      | No     | 8.3 FPM | No      | No    |
| **apache** | No      | Yes    | No      | No      | No    |
| **test**   | No      | Yes    | 8.3     | No      | No    |

Images including the Epigraf application code can be run without further installation steps.
Images without the application code require installation steps:

1. Mount or clone the source code into `var/www/html`.
   The following commands start in this folder.
2. Create the configuration file `config/app.php` to configure the database connection.
3. Run `composer install`
4. Init the database with the command `bin/cake database init`
5. Create an admin user account with the command `bin/cake user add admin admin admin admin`

See the Docker files that contain the full Epigraf application for inspiration.

## Server infrastructure

You need the following server environment:

- Webserver: Apache, passing requests to PHP FPM (or with mod_rewrite enabled)
- PHP server: PHP 8.3 with
  Saxon/C (depends on Java),
  exiftool,
  mysqldump,
  node.js and npm,
  and GraphicsMagick (or ImageMagick)
- Database server: MariaDB 10.11
- Cache server: Redis (optional)

In case you separate Apache and PHP, for example to use PHP FPM, make sure:

a) Apache delivers static files from the `htdocs` folder, and
b) Apache passes other requests to the `htdocs/index.php`.

## Installation
### First time installation

1. Clone the repository.

2. Use `composer install --no-dev` to install the app.

3. Edit `config/app.php` and set up the data sources, data folders and check that the security salt is present (see below).

4. Run `bin/cake database init` to create the databases. **Caution:** existing database tables may be altered.


### Update installation

1. Pull repository.

2. Use `composer install` to update the app.

3. Clear all caches by running `bin/cake cache clear_all`.


## Configuration

All configuration settings are to be found in `config/app.php`.
If `config/app.php`  is not present at time of installation
(`composer install` command) it will be created by copying
`config/app.default.php`.

You need to set the database connections, paths of the data folders,
the securty salt, and, optionally, a Redis connection.

You can search and replace the placeholders described below to automate this task.

### 1. Security salt
At installation time, a security salt will be created and the following placeholder is replaced with the salt:

* `__SALT__`

Leave it as is. **Caution**: Make sure not to overwrite the salt value when upgrading the app.
It is used to encrypt user passwords. Login may no longer work for existing users if the salt is changed.

### 2. Databases

Epigraf manages several databases, make sure it has its own database server.
Check the datasources configuration and replace the following placeholders, they occur multiple times in the config.

* `__DBHOST__`
* `__DBPORT__`
* `__DBUSERNAME__`
* `__DBPASSWORD__`

In production mode, remove the config for test databases.
Don't forget to disable debug mode in the app.php for production environments.

### 3. Data folders

Epigraf manages several data folders, some for the general application and each project database gets its own folder.
Create a root folder that contains all other folders and replace the following placeholder by the path:

* `__DIRDATAROOT__`

In the root folder, create the subfolders `shared` and `databases`.
The shared folder will contain data for the wiki, help and frontend pages.
The databases folder will host images and documents for each database.
You might consider to mount external storage in this place.

Replace the following placeholders with the absolute paths:

* `__DIRSHARED__`
* `__DIRDATABASES__`

### 4. Cache configuration
By default, a file cache is used. If you want to use Redis, see the app.default.php
and replace the following placeholders:

* `__REDISHOST__`


## CI/CD

GitLab pipelines can be used to build, test and deploy the Epigraf application.

The deployment image can be automatically built in a GitLab pipeline,
pushed to a GitLab registry and deployed on a Kubernetes cluster.
See the `.gitlab-ci.yml` for an example pipeline
and `docker/deploy/app.php` for an example configuration file.

The image for the test system should be built locally and pushed to the GitLab registry.

### Build the test image

Example to build and push the test image:

```
cd docker/test

docker build -t registry.gitlab.rlp.net/adwmainz/digicademy/di/epigraf/epigraf/test:php8 .
docker login registry.gitlab.rlp.net
docker push registry.gitlab.rlp.net/adwmainz/digicademy/di/epigraf/epigraf/test:php8

```

### Build the deploy image locally

Since the source code is included in the image,
building the deploy image starts in the repository root.

Before building the image, create the file `config/app.deploy.php` and configure the
database connection, data folders, and the cache. You can use `config/app.default.php`
as a blueprint.

```
cp docker/deploy/Dockerfile Dockerfile

docker build -t registry.gitlab.rlp.net/adwmainz/digicademy/di/epigraf/epigraf/production .
docker login registry.gitlab.rlp.net
docker push registry.gitlab.rlp.net/adwmainz/digicademy/di/epigraf/epigraf/production

rm Dockerfile
```

In your production environment, you need an Apache webserver, a MariaDB server, and optionally
a Redis server.
Configure the Apache webserver to pass PHP FPM requests to the index.php in the htdocs folder,
but make sure it also delivers static files from the htdocs folder.

# Server configuration

Example settings for deployment:

- FPM-Pool-Settings: [php/www.conf](php/www.conf) (to be placed in `/usr/local/etc/php-fpm.d/www.conf`)
- MariaDB-Settings: [mariadb/my.cnf](mariadb/my.cnf)


# Dependencies

*Installed in Docker files:*
- Saxon/C (depends on Java)
- exiftool
- GraphicsMagick or ImageMagick

*Third party frameworks included in the repository or installed using composer:*
- Ckeditor (depends on node.js and npm)
- jQuery and jQuery UI
