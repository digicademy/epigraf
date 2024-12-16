---
title: Themes
layout: default
parent: Frontend
nav_order: 30
---
# {{ page.title }}

The visual appearance of the frontend is defined using themes. The primary color palette, accent colors, neutral colors, and functional colors
as well as the font face are defined in the file `src/htdocs/app.css`. Those variables are used in the CSS files of the frontend to style the HTML elements.
Theme files in the `src/htdocs` folder overwrite the default values.

Themes can be switched by the `theme` query parameter or in the user settings. Allowed values are listed in `UsersTable::$themes`.

| Scope     | Variable                 | Default                   | Description                                                                                                                                                                                                                        |
|-----------|--------------------------|---------------------------|------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| primary   | --primary-200            | hsl(185, 68%, 91%)        | Sidebar resizer hover color. Dirty dropdown background color.                                                                                                                                                                      |
| primary   | --primary-300            | hsl(185, 43%, 80%)        | Background of selected unfocused table rows, column resizer color, message box background.                                                                                                                                         |
| primary   | --primary-400            | hsl(185, 64%, 40%)        | Table supplement font color, animation color for targeted headings.                                                                                                                                                                |
| primary   | --primary-500            | hsl(185, 75%, 36%)        | Background of selected table rows, active tabsheet buttons' background color, help button background color, active note heading font color, selected tree items and table items in dropdown panes, hovered annotations background. |
| primary   | --primary-600            | hsl(185, 74%, 32%)        | Hovered selected table row background, highlight font color for search results in tables.                                                                                                                                          |
| primary   | --primary-700            | hsl(185, 75%, 29%)        | Database main menu background (left), font color of links, table headers, background of hovered navigational buttons, hovered logout & profile button.                                                                             |
| primary   | --primary-800            | hsl(185, 74%, 26%)        | App main menu background (middle), Heading 2+, border of table headers, footer border, font color of sidebar&frame title, font color of navigational links and buttons (including settings button, legends).                       |
| primary   | --primary-900            | hsl(185, 97%, 18%)        | User main menu background (right), Heading 1, font color of hovered accordion toggle, image border in trees, menu font color.                                                                                                      |
| accent    | --accent-button-normal   | hsl(195, 100%, 36%)       | Button background color. Active (main) menu item background. Finished status font color.                                                                                                                                           |
| accent    | --accent-button-hover    | hsl(195, 100%, 29%)       | Hovered buttons' background. Border color of highlighted fields and boxes.                                                                                                                                                         |
| accent    | --accent-open-normal     | hsl(93, 64%, 39%)         | Open and add button background, success message background. Published, searchable & checked status font color, metadata font color.                                                                                                |
| accent    | --accent-open-hover      | hsl(93, 62%, 32%)         | Hovered open buttons' background.                                                                                                                                                                                                  |
| accent    | --accent-submit-normal   | hsl(40, 100%, 29%)        | Submit buttons' background.                                                                                                                                                                                                        |
| accent    | --accent-submit-hover    | hsl(40, 89%, 39%)         | Hovered submit buttons' background.                                                                                                                                                                                                |
| functions | --function-highlight     | hsl(93, 100%, 60%)        | Highlighted text background color.                                                                                                                                                                                                 |
| functions | --function-warning       | hsl(40, 100%, 50%)        | Background of warnings, link color in messages.                                                                                                                                                                                    |
| functions | --function-remove        | hsl(3, 89%, 39%)          | Error message background, remove button background.                                                                                                                                                                                |
| functions | --function-title         | hsl(3, 89%, 35%)          | Breadcrumbs.                                                                                                                                                                                                                       |
| functions | --function-home          | hsl(195, 100%, 31%)       | Background of top left home and the database button.                                                                                                                                                                               |
| neutral   | --neutral-100            | #FFFFFF                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-150            | #FEFEFE                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-200            | #FAFAFA                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-250            | #F6F6F6                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-300            | #F3F3F3                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-350            | #F1F1F1                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-400            | #EFEFEF                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-450            | #E8E8E8                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-500            | #E6E6E6                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-550            | #DDDDDD                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-600            | #D1D1D1                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-650            | #CACACA                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-700            | #B3B3B3                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-750            | #8D8D8D                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-800            | #303030                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-850            | #0A0A0A                   |                                                                                                                                                                                                                                    |
| neutral   | --neutral-900            | #000000                   |                                                                                                                                                                                                                                    |
| neutral   | --content-font-family    | sans-serif                | Used for field content.                                                                                                                                                                                                         |

Additional styles are provided by the Widget plugin in the `plugins/Widget/webroot/css/widget.css` file.
Each widget may contribute a css file in `plugins/Widget/webroot/css` which is imported into the `widget.css` file.

# Toolbar
The XML editor toolbar buttons make use of icons as SVG files.
The icons are dynamically imported to Epigraf from the file `htdocs/img/icons.min.js`.
This file is created with webpack from the `resource/icons` folder.
To add icons, the SVG files must be stored in this folder and imported into the icons.js file.
The minimized version is generated by calling `npm run build`.

If you want to create your own icons as SVG files, you can do this with a graphics program such as Inkscape.
Note the following when creating icons:
- The icons should have a height and width of 20x20 pixels.
- For shapes such as circles and rectangles paths should be used.
- Assign fonts to the entire text element. If external fonts are to be integrated,
  they must be preferably stored as woff files in the `htdocs/webfonts` folder.
  Also, integrate the external font in the file `htdocs/css/app.css`.
- Create SVGs without overlapping elements, otherwise shifts may occur in the displaying of the icons.
- When working with git, pay attention during commits to what is text and what is binary data.
