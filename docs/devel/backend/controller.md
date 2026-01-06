---
title: Controller
permalink: 'devel/backend/controller/'
---

Epigraf maps URLs to action methods in controller classes.
These methods perform three primary functions:

1. Parameter parsing: Extract and validate URL parameters that control the data retrieval and view rendering.
2. Data Handling: Retrieve and store data using the [model](/devel/backend/model) based on URL parameters and posted payloads.
3. View Rendering: Pass data to [views](/devel/backend/view) for the browser (HTML) or API responses (e.g. JSON).

## URL Mapping and Routing

Routes map URLs to specific controllers and actions.
They are defined in `config/routes.php`.
By default, routes follow the convention:

```
/<controller>/<action>/<path parameters><extension>?<query parameters>
```

Routes for project databases are configured in `plugin/Epi/config/routes.php` and follow the convention:

```
/epi/<database>/<controller>/<action>/<path parameters><extension>?<query parameters>
```

- **Controller**: Determines which controller handles the request.
  Controller classes are located in `src/Controller` and `plugins/Epi/src/Controller`.
- **Action**: Specifies the method to invoke in the controller. The most common actions include:
  - index: List entities. CakePHP conventions allow omitting `index` in the URL. For example, `/users` is equivalent to `/users/index`.
  - view: Display a single entity. The entity ID is passed as a path parameter.
  - add: Create a new entity. Get requests render a form, while post requests create the entity.
  - edit: Update an existing entity. Get requests render a form, while post requests update the entity.
  - delete: Remove an entity. Get requests render a confirmation page, while post requests delete the entity.
  - transfer: Transfer entities between databases.
  - mutate: Perform batch operations on entities.
  - import: Import data from external CSV or XML files.
- **Parameters**: Path segments are passed as parameters to the action methods.
  Query parameters can be retrieved using `$this->request->getQuery()`.
- **Extension**: Specifies the output format. By default, if the extension is ommited, HTML pages are rendered.
  Change the output format by adding one of the following extension: `json`, `xml`, `csv`, `md`, `rdf`, `jsonld`.
  Alternatively, Epigraf supports content negotiation via passing a mime type to the `Accept` header.

For example, the following URL maps to the `index` method in the `UsersController`,
which retrieves all user records from the database with the role `author` and renders the response in JSON format:

```
/users/index.json?role=author
```

Common query parameters that affect the rendering in Epigraf include:

- template: The page layout for rendering entity collections. Options include table, map, tiles, lanes.
- theme: Select colors and layouts for rendering HTML pages.
- show: Select specific blocks to be rendered in HTML pages.
  A comma separated list, options include content, leftsidebar, rigthsidebar, mainmenu, footer, search.
- mode: The mode is used to optimize the layout for specific tasks. Options include default, view and code.
  This affects, for example, the sidebar widths, order of sections within articles and which fields are editable.
- columns: A comma separated list of colum names for table rendering.
- shape: Format of API outputs, examples for rendering CSV files include long, wide, triples.
- snippets: In API outputs, several fields are hidden by default and can be requested by passing a comma separated list of
  field groups. Options include paths, search, problems, editors.

Additional parameters are specific to the different endpoints and
control data selection and pagination. To find out which parameters are implemented,
see the model table classes that correspond to the controller.
For example, query parameters for the URL `epi/<database>/articles/index`
are defined in the property `ArticlesTable::$parameters` located in the file `plugins/Epi/Models/Table/ArticlesTable.php`.

## Assets

Static files such as CSS and JavaScript are stored in the `htdocs` directory and served directly by the web server.
The Epi plugin and the Widgets plugin provide additional assets in the `plugins/Epi/webroot` and `plugins/Widgets/webroot` directories.
All other requests are routed to the `htdocs/index.php` which is the CakePHP entry point. See the `.htaccess` configuration.

Thinking about static asset delivery is particular important when running Epigraf in a PHP FPM setup.
In this case, the `.htaccess` is not enough. Static files should be served by the web server directly,
not by the PHP FPM process.

## Authentication and Authorization

Epigraf uses the CakePHP authentication and authorization components to manage user access to the application.
It implements the following concepts based on CakePHP's AuthComponent:

- **Authentication**: Users can authenticate either via the users/login endpoint by providing their username and
  password, or by sending an access token in the query parameter or the header of the request. Note: There are no specific API
  endpoints in Epigraf, all endpoints are considered API endpoints. The distinction between API and web requests is
  solely based on the authentication procedure. All requests that are authenticated by a token are considered API
  requests, all other requests are considered web requests.
- **Authorization**: Users are assigned roles in their user record that determine which actions they are allowed to
  perform on which controllers using API or web requests.
  There are three types of authorization procedures in Epigraf: hardwired permissions, granted permissions and data permissions.
  Hardwired permissions are defined in the `$authorized` property in the controller classes,
  while granted permissions are managed in the permission table. Both control access to endpoints.
  Data permissions are used to control access to specific entities and fields in the database.
  The permissions are based on the entities' published fields, the types configuration, and the permissions table.

In addition, Epigraf only allows Cross-Origin-Resource-Sharing (cors) if explicitly configured.
CORs headers are set in the `AppController::beforeFilter()` callback based on the configuration in `app.php`.

### Hardwired endpoint permissions
Hardwired permissions are defined in the controllers' `$authorized` property for api and web access for the different user roles:
  - Unauthenticated users are assigned the 'guest' role. At an early stage in the request lifecycle, after loading authentication plugins,
    the method `AppController::_initAuthorization()` grants web access using `$this->Auth->allow()` for actions listed for guest users  in the `$authorized` property.
  - Other roles comprise 'reader', 'coder', 'desktop', 'author' and 'editor' with ascending access rights.
    The `isAuthorized()` method in the `AppController` class checks whether the user is allowed to access
    the requested action by calling `AppController::hasWiredPermission()` on the global and the Epi plugin controller.
  - The roles 'devel' and 'admin' have global access when logged in via the web interface and not otherwise restricted in `$authorized`.
    As with all roles of authenticated users, their permissions are checked in `isAuthorized()`.

Authorization is handled slightly different for app level endpoints and project level endpoints.
Project level endpoints are all controllers and actions in the Epi plugin. They allow access to a specific project database.
The currently selected database is determined by the database parameter in `AppController::_selectDatabase()`.
All users have a default user role defined in their user record. Additionally, users may be assigned different roles on different databases
in the permissions table. When accessing project level endpoints, the roles in the permission table take precedence over the default user role.

The app level users controller has its own `isAuthorized()` method to allow users to manage their own user record,
even if the necessary actions are not listed in `$authorized`.

### Granted endpoint permissions
The permissions table stores permissions according to the ARO-ACO concept:

- User ID, role and request scope determine who is requesting access via API or web reqeuests.
- Entity type, name and id as well as the endpoint name determine the target of the request.

Access to endpoints and specific database entities is granted or denied by comparing
a permission mask to the permission records by calling `PermissionsTable::hasPermission()`.

### Data permissions
Access to project databases is given on the basis of granted permissions.
For an example see the `getAllowedDatabases()` method.

The `published` field of each entity plays a crucial role in determining whether guest users are allowed to access an entity.
All data extraction and display methods use `BaseEntity::getEntityIsVisible()` to check whether the entity is published.
Specific fields are filtered out on the basis of the types configuration in `BaseEntity::getFieldIsVisible()`.

Further, access to specific entities can be granted by the permissions table. By the time of this writing, only
the FilesController uses this method in `BaseEntity::isPermitted()` to check access.

### Handling permissions
Authentication and authorization data is prepared for passing it to the backend layers (model, view, controller)
and to the frontend (JavaScript) in `AppController::_initUser()`.

- Models: In the model layer, relevant authorization parameters are attached to the BaseTable class,
  e.g. to `BaseTable::$userRole` property.
- Views: In the view layer, the LinkHelper class provides methods to check permissions for rendering links,
  e.g. `Link->hasPermission()`.
- Controller: The AppController has properties with relevant authorization data,
  e.g. `AppController->userRole`.
- Frontend: User data is passed to the frontend in the `AppController->getAppJs()` method.
  A JavaSCript user model class is instantiated in the property `App.user` (see `src/htdocs/js/models.js`).

## Rate limiting

Rate limiting is implemented in the RateLimitMiddleware.
It is configured in the `config/app.php` file
which reads the following environment variables:

- APP_RATE_LIMIT_REQUESTS
- APP_RATE_LIMIT_INTERVAL


## User Settings
User settings in Epigraf are either stored in the user record or in the session. They include:

- Preferences such as the selected theme are stored in the config field of the user record.
- Query parameters of the endpoints determine filter criteria for the rows and column selections.
  They are stored in the user record when the query parameter `save` is set to 1,
  and loaded when the query parameter `load` is set to 1.
- User interface settings such as the width of sidebars are stored in the session by
  sending post or patch requests to the server. After interactions with the frontend,
  the settings are posted using AJAX requests.

## Classes and Components

### Application level

All controllers extend the `AppController` class (`src/Controller/AppController.php`)
which is derived from the base `Controller` class provided by the CakePHP framework.

![Application controller class hierarchy](/devel/assets/img/classes-controller-app.png)

On the application level, controllers serve the following purposes:

- **Display information pages**: PagesController, WikiController, HelpController.
- **Manage user accounts**: UsersController, SettingsController, PermissionsController.
- **Manage content**: DatabanksController, FilesController.
- **Batch operations**: PipelinesController, JobsController, ArticlesController.
  _Note_: The ArticlesController is solely used for backwards compatibility with EpiDesktop and will be removed in future versions.
  The same applies to some functions of the JobsController that will be moved to the TransferComponent in future versions.
- **Error rendering**: ErrorController.


### Project level

The Epi plugin implements an own `Epi\AppController` class (`plugins/Epi/Controller/AppController.php`)
derived from the global AppController, adding functionality for project database handling.
All project database controllers within the Epi plugin extend this class.

![Epi plugin controller class hierarchy](/devel/assets/img/classes-controller-epi.png)

They Epi plugin controllers serve the following purposes:

- **Manage research data**:  ProjectsController, ArticlesController, SectionsController, ItemsController, PropertiesController
- **Manage project database users**: UsersController
- **Configure the domain model**: TypesController
- **IRI resolving**: IrisController
- **Display information pages**: NotesController
- **File management**: FilesController
- **Integrity checks**: AnalyticsController

### Common components and traits
Common functions are implemented in components (which reside in subfolders of the controller folders)
that are attached to the controllers:

- **ActionsComponent** (Rest plugin): Provides basic CRUD operations for entities. Particularly used for NotesController, PagesController, HelpController, and WikiController.
- **AnswerComponent** (Rest plugin): Prepares data for rendering in views.
- **ApiPaginationComponent** (Rest plugin): Injects pagination information into serialized API responses.
- **LockComponent** (Rest plugin): Manages the locking of entities for editing so that users don't interfere.
- **FilesRequestComponent** (Files plugin): Handles file uploads, downloads, and displaying files.
- **TransferComponent** (Epi plugin): Import entities, transfer entities between databases, and mutate entities by batch operations.

Note that UserControllers and FilesControllers are implemented on both levels,
as they are used for managing different data in the application and in the project databases.
The FilesController classes on both levels use the same actions implemented in `FilesRequestTrait` and `FilesRequestComponent`.
for handling file uploads and downloads.

Multiple controllers use the `LockTrait` and the `LockComponent` for implementing lock and unlock actions.
Locking avoids conflicts when multiple users try to edit the same entity.

In addition, the RequestComponent, PaginatorComponent, FlashComponent and AuthComponent of the CakePHP framework are used widely
throughout Epigraf for handling requests, responses, pagination, messages and authentication.
All controllers inherit traits from the CakePHP base Controller class: EventDispatcherTrait, LocatorAwareTrait, LogTrait, ModelAwareTrait,
ViewVarsTrait.

## Code Examples

In the most simple case, the Actions component can be used to handle standard CRUD operations.
As an example, calling `epi/public/projects/index` connects to the epi_public database and
maps to the following `index` method in the `ProjectsController` of the `Epi`-plugin:

```php
/**
* Retrieve list of projects
*
* @return void
*/
public function index()
{
  $this->Actions->index();
}
```

For more complex operations, parameter parsing, data management and view rendering are directly handled in the controller methods.
The basic structure of a controller method for index operations is as follows:

```php
/**
 * Retrieve a list of users.
 *
 * @return void
 */
public function index()
{
    [$params, $columns, $paging, $filter] = $this->Actions->prepareParameters();

    $entities = $this->Users
        ->find('hasParams', $params)
        ->find('containFields', $params);

    $this->paginate = $paging;
    $entities = $this->paginate($entities);

    $this->Answer->addOptions(compact('params', 'columns', 'filter'));
    $this->Answer->addAnswer(compact('entities'));
}
```
**Explanation:**

- Routing: In the example, the URL `/users/index` resolves to `UsersController::index()`.
- Parameter handling: The Actions component provided by the Rest plugin extracts parameters and paging information
  for data retrieval (`$params` and `$paging`), the column configuration for the rendered table (`$columns`)
  and settings for the search bar and facets (`$filter`) from path and query parameters using `prepareParameters()`.
- Data retrieval is managed through finders of the model class implemented in `src/Model/Table/UsersTable.php`
  and instantiated in the `Users` property of the controller.
  By convention, model classes are instantiated according to the controller name.
  The pagination component attached to the controller is used to paginate the results.
- The Answer component prepares variables for rendering and passes them to the view.
  The CakePHP framework automatically renders the view after the controller method has finished executing.
  By default, the view is rendered in HTML format using the AppView class.
  The layout is located in `templates/layout/default.php`,
  specific action templates are located in `templates/<Controller>/<action>.php` (e.g., `templates/Users/index.php`).
  API responses are rendered by view classes derived from `src/View/ApiView` (e.g., `src/View/JsonView.php`).

