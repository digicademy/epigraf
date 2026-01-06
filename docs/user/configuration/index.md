---
title: Configuration
permalink: 'user/configuration/'
---


Epigraf can be adapted to suit a wide range of use cases. The building blocks of projects, articles, sections, items and annotations (including links and footnotes) are predefined by the data model. However, how this structure is filled with meaning can be freely configured. For example, you can define different section types (e.g. description sections, transcription sections, lists of locations) and configure which fields are displayed, how they are labelled and the data formats they contain.

The configuration is accessible for administrators via the `Types` menu. This is where you define how articles are composed, which category systems are available and which annotations, footnotes or other apparatuses are possible. A complete configuration consists of types for the following components:

-   **Projects** that group articles
-   **Articles** within a project
-   **Sections** within an article
-   **Items** within a section
-   **Links** within text fields
-   **Footnotes** within text fields
-   **Properties** used in items and links.

One JSON configuration containing keys and values is created for each type. The configuration, for example, defines the labels of fields and how different types relate to each other.

In addition to the standard configuration, further display modes can be configured. For this purpose, a type configurations is created for the mode. The mode specific configuration can be minimalistic and just override specific settings. It is merged into the default configuration, and thus, inherits all those settings.

- **default**: The standard configuration, which must always be present. Additional modes override individual keys in the standard configuration.
- **preview**: Settings for the view mode of entities. This mode always applies to guest users. Use it to polish the entities view if not in edit mode.
- **code**: The code mode is activated by clicking the revise button in the articles overview. This always opens the articles in the sidebar in edit mode and, thus, allows for going through a bunch of articles quickly. In addition, by using a specific configuration, you can fix the relevant sections and hide unnecessary fields.
- **stage**: *To be added*

Some settings can be found in several types:

-   **Columns** in table views are defined via the 'columns' key.
-   **Fields** of entities are configured in the 'fields' key.
-   **Hierarchy** and linking of types:
    -   For projects, the possible article types of the project are defined in the 'articles' key.
    -   For articles, the subordinate sections are defined in the 'sections' key.
    -   For sections, the key 'items' defines the subordinate items.
-   For **property fields**, the 'types' key determines which property type is used.
-   For **text fields** configured as XML, the 'types' key defines which annotations (links, footnotes) are available for the field.

## Scope of the type configuration

-   **scope**: Determines what kind of entities is configured, for example "sections".
-   **name**: A name for the configured type, for example "locations".
-   **mode**: A default configuration must always be created. It can be overwritten for the revise mode (code) and the view mode (preview).
-   **preset**: The default configuration can be overwritten for specific use cases. Create a type configuration with a preset value and add the preset value to the preset query parameter of a URL when accessing a page. For example, you can configure a brief article overview using a preset. Presets are also used to provide different triple configurations for specific triple stores. You must not use presets with the default mode, use the preview mode.
-   **iri:** A globally unique identifier, used for transferring configuration between different databases.
-   **published:** Only published type configurations are used when displaying content to guest users.
-   **number**: Order of the type entities. If there are multiple configurations for the same type (scope, name, and mode), they are overwritten in the given order.

## Content of the type configuration

-   **caption**: A label for the type. It is used, for example, as section header and as button label in the annotation toolbar.
-   **description:** Just for your convenience, take notes about the type's purpose.
-   **category**: When a project matures, the configuration may grow to several hundred type entities. Use categories to organise the types.
-   **config**: Contains the configuration in JSON format.


