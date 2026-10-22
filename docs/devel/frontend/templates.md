---
title: Templates
permalink: 'devel/frontend/templates/'
---

## Page Layout

Epigraf uses flex layouts. The main window is divided into the main menu, content area and the footer.
The content area consists of two sidebars and the main content.
Within the two sidebars, content is arranged in tabsheets.

The main menu is generated in the AppController based on the currently logged in user.
The action buttons in the footer are generated from within collection views and entity views
using the Link-Helper. See `templates/layout/default.php` for how the layout is assembled.

Additionally, popup windows are used to display messages and show input fields.
Popup content is generated using JavaScript or requested from the server via AJAX.
An image viewer is implemented as an overlay that covers the entire page.

For small screens (mobile devices) the sidebars are hidden using CSS media queries.
Buttons are provided to show and hide the sidebars as overlays.
Menu items are rearranged in a dropdown menu if space is limited.

Most pages fall into one of the following categories:
- Collection views: Display a list of entities as table or tree with actions to view or edit them.
- Entity views: Display a single entity with its properties and actions to edit it.

## Collection Views

Collections views are usually generated in `index.php` templates.
They consist of the following elements:

- Flash messages showing feedback, for example after entities were saved (optional).
- Search bar in the main content area (optional).
- Table or tree view in the main content area.
- Facets or sub menus in the left sidebar (optional).
- The right sidebar is used to display single entities selected in the table.

The collections are rendered as tables or trees using one of the following helper functions:

- `TableHelpber::simpleTable()` outputs a table.
- `TreeHelper::selectTree()` outputs a tree.
- `TableHelper::filterTable()` integrates table and trees with filter options.


The collection views open up ways to access entity views by providing links in the last table column.
They are hidden on the pages, instead JavaScript click handlers use those links
to open entity views on the same page (double click) in the sidebar (single-click),
or in new tabsheets (ctrl+click) or in popups.

Further, some collections lead to other collections. In the following example,
the database collection view opens the article collection view of the selected database:

```php
  $this->Table->filterTable(
          'databanks',
          $entities,
          [
              'actions' => [
                  'view' => true,
                  'open' => [
                      'plugin'=>'epi',
                      'controller' => 'articles',
                      'database' =>'{name}',
                      'action' => 'index',
                      '?'=>['load'=>true]
                  ]
              ]
          ]
      )
```


## Entity Views

Entity views are generated in view.php, edit.php, add.php and many more templates.
Typically they consist of the following elements:

- Flash messages showing feedback, for example after entities were saved (optional).
- Bread crumbs in the main content area.
- Entity content in the main area.
- Entity specific actions in the footer within the main content area.
- Navigation in the left sidebar.
- Detail views in the right sidebar.

The entity content is usually rendered by one of the following helper functions:
- `EntityHtmlHelper::entityTable()` renders content in view actions.
- `EntityInputHelper::entityTable()` renders inputs in add and edit actions.

Complex documents such as articles, containing multiple sections and annotations, are assembled using
the functions `docContent()`, `sectionsList()` and `footnoteList()` in EntityHtmlHelper
for display purposes or EntityInputHelper for editing purposes.

## Dynamic Content

In production mode, on each page load, the following JavaScript files are loaded:

- app.min.js: The main application bundle. It imports JavaScript files from the htdocs folder.
- widgets.min.js: The EpiWidJs framework provides widgets such as dropdowns,
  a model for editing documents, and frame handling.
  It imports JavaScript files from the widget plugin folder.
- epieditor.min.js: The EpiEditorJs framework for wysiwyg editors based on the CKEditor.
  It imports JavaScript files from the widget plugin folder.
- jquery.min.js and jquery-ui.min.js: The jQuery library is used to create popups.
- leaflet.js and additional plugins: The Leaflet library for interactive maps.
- mark.js: The Mark.js library for highlighting text after full text search.

The main application bundle instantiates the class `EpiApp`
and assigns it to the global `App` object.
The application object implements methods...

- to show and hide loaders and messages.
- to load dynamic content using an ajax queue and update the page with `replaceDataSnippets()`.
- to instantiate a user model in `App.user` for managing user settings.
- to send error logs to the server.
- to observe click events on the page which open popups and activate tab sheets
  based on classes such as `popup`, `frame` and `noframe`.

The `App` object is expanded by functions in widgets.js
that attach JavaScript classes to HTML elements.
See the [widget documentation](/epigraf/devel/frontend/widgets).
