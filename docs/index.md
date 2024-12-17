---
# Epigraf 5.0
#
# @author     Epigraf Team
# @contact    jakob.juenger@adwmainz.de
# @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
#
# To modify the layout, see https://jekyllrb.com/docs/themes/#overriding-theme-defaults

permalink: /
---

# Introduction

Epigraf is a research platform for collecting, annotating, linking and publishing multimodal text data.

The data model supports research databases ranging from epistolary editions to social media corpora.
Epigraf is currently used primarily for editing epigraphic data - inscriptions in connection with the objects to which they are attached.
It includes a publication system for various document formats such as Word or TEI, structured JSON, XML and CSV data, and triples in TTL, JSON-LD and RDF/XML.

*Note about the documentation:* You are currently reading the [developer documentation](https://digicademy.github.io/epigraf/).
For information about how to use Epigraf, see the [user documentation](https://epigraf.inschriften.net/help).

*Note about the development status:* Epigraf has a history.
You will find archeological layers and legacy code from former development epochs
when visiting the code base and documentation.
Some of them are well preserved, others are deteriorating.
In any case, they provide fertile soil for future development.
Look out for the flowers that are in bloom and help nurture the garden.

![Epigraf use cases](/assets/img/epigraf-use-cases.png)

# Getting Started

Epigraf is a classical web application.
The [server infrastructure](/docs/servers) to run Epigraf consists of an Apache web server,
a MariaDB database server, file storage, and optionally a Redis cache server.
You can use the prepared docker compose setup to run the application:

1. Fire up the servers:
   ```
   docker compose up -d
   ```

2. Install Epigraf:
   ```
   docker exec epi_php composer install
   docker exec epi_php bin/cake cache clear_all
   ```

3. Init the application database and add an admin user:
   ```
   docker exec epi_php bin/cake database init
   docker exec epi_php bin/cake user add admin admin admin admin
   ```
   *Note*: The database init command is called without parameters
   to create the application database with the default name "epigraf".
   The user add command is called with all the parameters username, role, password and access token set to "admin".

4. Create an example project database:
   ```
   docker exec epi_php bin/cake database init --database epi_example --preset movies
   ```
   *Note*: Project databases are prefixed with "epi_".
   Presets for project databases are located in the folder `plugins/Epi/config/presets`.

5. Login to Epigraf at [http://localhost](http://localhost)
   with the username `admin` and the password `admin`.

What's next? Get familiar with the [Epigraf configuration](https://epigraf.inschriften.net/help) to adapt it to your use case.

# Architecture

The [frontend](/docs/frontend) is rendered in the browser using HTML, CSS, and JavaScript. Frontend logic is based on the EpiWidJs framework.
The [backend](/docs/backend) is implemented using the CakePHP framework and contains the application logic.
MariaDB and the file system is used to store [data](/docs/database). There is one application database for managing
user accounts and application-wide data. For the research data, multiple project databases are created.
Frontend and database content is cached using Redis. If no Redis server is available, the cache is stored in the file system.

![Epigraf architecture](/assets/img/epigraf-architecture.png){:width="90%"}

# Directory structure

The source code folder contains the standard directories of a CakePHP
application.

-  *config*: The application configuration, such as the database settings.
-  *htdocs*: The web root from which static CSS and Javascript files are served.
-  *src*: The application source code.
-  *templates*: Templates to render HTML files.
-  *tests*: Unit and acceptance tests.
-  *resources*: Assets such as translation files and toolbar icons.
-  *bin*: Scripts for running CakePHP from the console.
-  *tmp*: Temporary files, such as thumbnails of images, and the cache folder if no Redis cache is used.
-  *vendor*: Third-party code installed by composer. Do not touch this directory.
             The vendor directory also contains the CakePHP core.
-  *logs*: Error and debug logs.
-  *docker*: Docker configurations for development, test, and deployment.


Plugins encapsulate functions used by the application.

- *plugins/Epi*: Handles the project databases.
- *plugins/Files*: Handles the file system.
- *plugins/Rest*: Implementation of the API functions.
- *plugins/Widgets*: JavaScript frontend widgets.

Each plugin folder, in general, can contain the same folders as a CakePHP application.
Depending on the plugin, not all folders are always used.
In contrast to the application, plugins which deliver frontend files
use `webroot` instead of `htdocs` as the web root folder.


