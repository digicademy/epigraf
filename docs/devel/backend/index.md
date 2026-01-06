---
title: Backend and Framework
permalink: 'devel/backend/'
---

Epigraf is a PHP application that uses the CakePHP framework and adheres to the MVC paradigm.

We strongly recommend reading the CakePHP documentation to understand how Epigraf works:

- Controllers: [Introduction to controllers](https://book.cakephp.org/4/en/controllers.html), [request and response handling](https://book.cakephp.org/4/en/controllers/request-response.html) and [routing](https://book.cakephp.org/4/en/development/routing.html).
- Models: [Introduction to the ORM](https://book.cakephp.org/4/en/orm.html) and [pagination](https://book.cakephp.org/4/en/controllers/pagination.html).
- Views: [Introduction to view rendering](https://book.cakephp.org/4/en/views.html) and [view helpers](https://book.cakephp.org/4/en/views/helpers.html).


## Application Logic vs. Project Logic

Epigraf separates application-wide logic and database-specific logic:

1. **Application Logic**: Handles features available across the application (right side of the main menu)
   such as user management and export pipelines. The controller, model and view classes are located in subfolders of the application source folder `src`.
   The default database used in the controllers is `epigraf`. The templates for the views are located in the `templates` folder.
2. **Project Logic**: Handles actions for project databases (left side of the main menu)
   such as creating articles and categories. The controller and model classes are located in subfolder of the Epi-plugin folder `plugins/Epi`.
   Templates for view rendering are located in the `plugins/Epi/templates` folder.
   Project database names always start with `epi_`. URLs are prefixed with `epi/<database_name>`, the `epi_` prefix from the database name can be omitted in the URL.
   For each request, the database connection is switched to the appropriate project database by calling `BaseTable::setDatabase()` in
   the `AppController::beforeFilter()` callback.

## Collections and Entities

The content captured and published with Epigraf is stored in different entities that are
organized hierarchically, for example to create the structure of an article.
In addition to the hierarchical structure, some entities are also grouped and made accessible
in collections. For example, Epigraf can display all articles of a project or an entire
database as a list. The projects themselves form a collection within a database.
Other examples are the collection of categories, the collection of notes,
or the collection of databases that are only accessible to users with special permissions.

The controllers implement actions that work on individual entities, such as view(),
edit(), delete(). Actions that can be applied to collections are index(), mutate(), transfer().
Also in the view layer, templates can be distinguished according to whether they represent
collections or the content of individual entities.
The templates for entities structure the content to make it easily distinguishable
or accessible for editing. The templates for collections provide an overview
and the individual entities, e.g. by means of identifiers, a title or images.
The selection of templates and their special features are described in the view layer documentation.

The export of data is organized by pipelines. A pipeline consists of several steps
that are necessary, for example, to output the content of one or more articles
as a Word file and to provide the result as a download. Pipelines can usually be triggered
for individual entities or for entire collections.
The data to be output and the individual process steps are defined in the configuration
of a pipeline, thereby determining the output format.

## The MVC Paradigm

To understand how Epigraf implements the MVC paradigm, consider the following example:

- Log on and visit <http://localhost/users/index>.
  You can omit the last */index* part of the URL:
  If no action is specified the index method will be called automatically.
  This is a CakePHP convention.
- Open `src/Controller/UsersController.php` and locate the `index()` method.
  This is where one page of user records is retrieved from the database:
  ```php
  $entities = $this->Users
    ->find('hasParams', $params)
    ->find('containFields', $params);
  $entities = $this->paginate($entities);
  ```
- The property `$this->Users` refers to the model table.
  Look at the file `src/Model/Table/UsersTable.php`. Table classes provide access to a database table
  using finders and pagination options are added by the controller. This is a CakePHP convention.
  The variable `$entities` refers to the query result containing a collection of entities.
  Each entity represents a row in the table. Open `src/Model/Entity/User.php` to see the entity class.
- At the end of the index method, the Answer component is used to pass data to the view:
  ```php
  $this->Answer->addAnswer(compact('entities', 'connected', 'summary'));
  ```
- The `templates/Users/index.php` file contains the template used to render the HTML file in the browser.
  In a template you have access to all variables that are passed to the view layer by the controller.
  In the template, the entities are rendered using the TableHelper:
  ```php
  <?= $this->Table->filterTable('users', $entities, ['select'=>true, 'actions'=>['view'=>true]]) ?>
  ```
  Open `plugins/Widgets/src/View/Helper/TableHelper.php` to understand how the TableHelper works.
  Helpers are useful for packaging rendering code into reusable components.
