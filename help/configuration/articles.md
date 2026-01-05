---
title: Articles
permalink: '/configuration/articles/'
---


# The composition of articles


An article describes a research object, such as an object carrying inscriptions, a letter, or a social media post. The configuration specifies how the data model is used to represent your case. You start with article fields and then define the section types contained in an article. For each section type, in turn, you define its fields and the available item types.
For XML fields, you define the allowed annotations, i.e. links and footnotes. All of those components are called entities. The root entity is the article which contains section, item, links and footnotes entities.

Note, in the documentation below, you can fully customize all fields not marked as reserved or internal.
For reserved and interal fields you can customize their visiblity and labels. See the <a href="../configuration/fields">field configuration documentation</a> for further details.

Based on the configuration, an article can be displayed in different modes. The view mode is a readonly representation of the article and the edit mode is used to modify an article. Collections of articles can be displayed in the table view where each article is a row,
in lanes and tiles with short previews of an article. Further, maps, timelines and network graphs can be used to place articles in contexts. Those view options are configured in the article type. For example, in the columns key you configure how the columns in the overview table are filled with article fields or with data from the contained sections and items (as well as connected project entities and user entities). See the <a href="../configuration/columns">columns configuration documentation</a> for further details.

## Article type configuration

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Key</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>fields</td>
        <td>Setup of the entity view. A list of field configuration objects, keyed by the field name. The following fields are available for article entities:
         <ul>
            <li>signature: A short identifier for the article (cutomizable, text type).</li>
            <li>name: A title of the article (cutomizable, text type).</li>
            <li>sortno: A number used for sorting articles (experimental, number type).</li>
            <li>norm_data: Authority data field (reserved, text type).</li>
            <li>norm_iri: IRI-fragment of an article (reserved, text type).</li>
            <li>created: Date and time when the entity was created (internal, datetime type).</li>
            <li>modified: Date and time of the last modification (internal, datetime type).</li>
            <li>created_by: User that created the entity (internal, ID type).</li>
            <li>modified_by: User that last modified the entity (internal, ID type).</li>
        </ul>
        </td>
      </tr>
      <tr>
        <td>columns</td>
        <td>Setup of the entity table. A list of column configuration objects defining the columns available in the article overview table.</td>
      </tr>
      <tr>
        <td>preview</td>
        <td>Setup of the tiles view. The tiles view includes snippets and images from items contained in the article. See the details below.</td>
      </tr>
      <tr>
        <td>header</td>
        <td>Setup of the headline in the entity view. A list of header component configuration objects. See the examples below.</td>
      </tr>
      <tr>
        <td>sections</td>
        <td>Setup of sections contained in an article. A named list of section configuration objects. This list is used to create an empty article and to arrange the sections in the entity view. See below for further details.</td>
      </tr>
      <tr>
        <td>namespaces</td>
        <td>Namespaces used in the norm_data fields of the article and its contained sections and items. Norm data fields hold authority data by storing a list of identifiers, each separated by a new line. Each identifier can be abbreviated with a namespace prefix instead of storing the full identifier. The namespaces configuration containsa an object keyed by the namespace prefix (e.g. "urn") and a namespace configuration object. The namespace configuration object consist of the keys `baseurl` and `button` for each namespace. The baseurl value contains the full namespace URL (e.g. `https://nbn-resolving.de/urn:`). The button value, if not empty, is used to create a labeled button that leads to the URL derived from the identifier in the norm_data field, with the namespace prefix replaced by the full namespace URL.</td>
      </tr>
      <tr>
        <td>pipelines</td>
        <td>Pipelines are used to generate documents such as TEI-XML,  Word files or Zip-Archives.
           Not all pipelines are relevant for all article types. The pipelines list configuration object restricts the pipelines
           visible for authors and provides buttons in the footer area. The keys of the object contain IRI fragments of the pipelines, the values are used as button labels. The first button is always assigned the keyboard shortcut F6.<br>
            To make a pipeline available without a button, provide a full configuration object instead of simply a button label. The bject consists of a `caption` key for the button label and a 'button' key with a value of either <i>true</i> or <i>false</i> to show or hide the button.<br>
             In EpiDesktop, exports are triggered using F6 for single articles or F7 for a volume. A scope parameter with the values `article` or `book` is transferred to EpiWeb, the respective pipelines are defined in the user profiles.
             The scope key can also be set in the full pipeline configuration object.<br>
Example configuration:
<pre>
{
  "di-articles-doc": {
    "caption": "Word",
    "scope": "article"
  },
  "di-epidoc": {
    "caption": "EpiDoc"
  },
  "di-book-doc": {
    "caption": "Band",
    "scope": "book",
    "button": false
  },
  "di-images": {
    "button": false
  },
  "dio-export-book": {
    "button": false
  },
  "dio-export-img": {
    "button": false
  }
}
</pre>
        </td>
      </tr>
      <tr>
        <td>footnotes</td>
        <td>A list of footnote types used in the article and its contained sections and items. The list is necessary to generate the footnote tabsheets in the sidebar.</td>
      </tr>
      <tr>
        <td>toolbar</td>
        <td>The annotation toolbar by default is initialised when focusing an input field (value `false`). Set the toolbar key to `true`,
            if it should be initialized at startup.</td>
      </tr>
      <tr>
        <td>geodata</td>
        <td>In order for items to be displayed on article overview maps, the source of geolocations must be configured. The geodata key contains an object with item types as keys and extraction keys in the values. Example for retrieving geodata both directly from items in an article and indirectly from linked properties:
<pre>
"geodata": {
  "geolocations": "value",
  "locations": "property.content"
}
</pre>
        </td>
      </tr>
    </tbody>
  </table>
</figure>

### Sections contained in an article

The initial order and hierarchy of sections when **creating** an article is determined by the configuration in the sections key of an article type. You first need to configure the relevant section types (see below) before you can use them in the article configuration. The sections are then listed with keys that correspond to their type, and further settings are given in the value.

In the following example, an inscription section and a subordinate inscription part section (see the `parent` key) are created
when an article is created. In addition, any number of inscription sections and subordinate inscription part sections later be added manually (see the `count` key):

``` plaintext
{
  "sections": {
    "inscription": {
      "type": "inscription",
      "caption": "Inschrift",
      "count": "*"
    },
    "inscriptionpart": {
      "type": "inscriptionpart",
      "caption": "Inschriftenteil",
      "parent": "inscription",
      "count": "*"
    }
  }
}
```

To prevent automatic section creation when an article is created, set the default key to `false`.
The sections can be added manually later. Example:

``` plaintext
{
  "sections": {
    "geolocations": {
      "type": "geolocations",
      "caption": "Standorte",
      "count": "*",
      "default": false
    }
  }
}
```

A section type can be used multiple times if it is differentiated by name. For this purpose the key is formed according to the scheme `<sectiontype>[<sectionname>]`, i.e. it contains the section caption in square brackets after the section type.
Such keys can also be used in the `parent` key of subordinate sections. In the following example, an article is configured that consists of text sections. When creating an article, a section with the caption "Description" and two subsections labeled  "Object Description" and "Writing Description" will be created. In addition, you can later add text sections manually (see the `default` key).

``` plaintext
{
  "sections": {
    "text[Description]": {
      "type": "text",
      "caption": "Description"
    },
    "text[Object Description]": {
      "type": "text",
      "caption": "Object Description",
      "parent": "text[Description]"
    },
    "text[Writing Description]": {
      "type": "text",
      "caption": "Writing Description",
      "parent": "text[Description]"
    },
    "text": {
      "type": "text",
      "caption": "Additional Text",
      "count": "*",
      "default": false
    }
  }
}
```

The order of the sections when creating articles is determined by the order in the configuration. Sections can be moved manually in the editing view. It is also possible to move sections using a weighting. For example, you can move a section to the top when not in editing mode, to make it immediately present for readers. Negative weights move a section up, positive weights move it down. In the following example, when creating an article, the first section will be the locations section, followed by the description section.
This order will be used in edit mode. Based on the weights, the sections will be displayed in reverse order in view mode.

``` plaintext
{
  "sections": {
    "geolocations" : {
      "type": "geolocations",
      "caption": "Locations"
    }
    "text[Description]": {
      "type": "text",
      "weight": -10
    }
  }
}
```

The following table provides an overview of the keys used in section configuration objects:

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Key</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>type</td>
        <td>Section type</td>
      </tr>
      <tr>
        <td>caption</td>
        <td>Optional. Name of the section. By default, the caption results from the section type configuration.</td>
      </tr>
      <tr>
        <td>parent</td>
        <td>Optional. Key of the parent section to create a hierarchy.</td>
      </tr>
      <tr>
        <td>default</td>
        <td>Optional. By default, all sections are created when an article is created. If this is not desired, the default key can be set to `false`.</td>
      </tr>
      <tr>
        <td>weight</td>
        <td>Optional. Weighting of the section in the article view. Positive weights move the section downwards, negative weights move it upwards (optional).</td>
      </tr>
      <tr>
        <td>hide</td>
        <td>Optional. Usually all sections are visible in an article (`true`). You can hide a section completely by setting the hide property to `false`.</td>
      </tr>
      <tr>
        <td>collapse</td>
        <td>All sections are expanded by default. The default setting can be overwritten with the collapse key, taking on of `true` or `false`.</td>
      </tr>
    </tbody>
  </table>
</figure>

### Configuration of the article tiles

In addition to the entity view and the entities tables, articles can be displayed in tiles or lanes.
A tile contains an image or a text excerpt supplemented with summary information.
The data source is configured in the tiles key of an article type using a tile configuration object with the following keys:

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Key</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>images</td>
        <td>A list of item types containing images. The first image is selected from the list of items in an article. If there are published images among them, these are preferred.</td>
      </tr>
      <tr>
        <td>text</td>
        <td>A list of item types. If no image is available, the text (content field) from the first matching item is displayed in the tile.</td>
      </tr>
      <tr>
        <td>summary</td>
        <td>A list of columns that must be defined in the column configuration of an article. The contents of the columns are displayed as details in the preview.</td>
      </tr>
    </tbody>
  </table>
</figure>

The following example defines a tile with an image from the item type "images", which is replaced by the text from the item type "transcriptions" if not available. The tile also displays the content configured in the source, location and date columns. You need to configure the columns of the article accordingly:

``` plaintext
{
  "preview": {
    "images": ["images"],
    "text": ["transcriptions"],
    "summary": ["source", "location", "date"]
  }
}
```

## Section type configuration

A section groups the items of an article. Each section has a name and contains a comment field, but no other fields with content. The content is stored in the items. Sections are arranged hierarchically. Thus, the sections work much like sections in a research paper that provide structure to the propositions within an article. In fact, the Relational Article Model of Epigraf is inspired of how scientist arrange their thinking when presenting insights to other persons.

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Key</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
            <td>fields</td>
            <td>A list of field configuration objects, keyed by the field name. The following fields can be configured:
            <ul>
                <li>published: You can change the title of the published field</li>
                <li>comment: You can define the annotations that are allowed in the comment field.</li>
                <li>layout_cols and layout_rows: Used for grid widgets (see below).</li>
            </ul>
        </td>
      </tr>
      <tr>
        <td>sections</td>
        <td>Optional. A list of section types that defines which subsections are allowed.</td>
      </tr>
      <tr>
        <td>items</td>
        <td>Setup of items contained in an article. A named list of item configuration objects. This list is used to create an empty section. See below for further details. </td>
      </tr>
      <tr>
        <td>name</td>
        <td>Optional. Sections can have a fixed name, a name selected from a properties list or a name based on counting the sections in an article. Provide an object with the following keys:
          <ul>
            <li><code>prefix</code>: Prefix of the section name.</li>
            <li><code>path</code>: To show the full path of ancestor sections in the section label, set the path to `true`. The default value is `false`..</li>
            <li><code>number</code>: By default, sections have a fixed name (value `false`). Alternatively, you can automatically add a number (value `numeric`) after the prefix or count by letters (value `alphabetic`).</li>
            <li><code>scoped</code>: By default, the number is based on all sections in the article (value `false`). Alternatively, you can limit counting to the sectiontype (value `true`).</li>
            <li><code>options</code>: You can provide a list of names to select from by setting the `options` key to a propertytype. This will not link the section to the property. In consequence, the section name stays as is even if the property is updated later. To explicitly link a property to the section, configure an item within the section. To use the value of such a property as section name, set the format of the item's property field to `sectionname`.
            </li>
          </ul>
        </td>
      </tr>
      <tr>
        <td>view</td>
        <td>A section template that defines how the section is displayed, see below.</td>
      </tr>
      <tr>
        <td>display</td>
        <td>Optional. Display options for a section:
          <ul><li>Use `false` to hide a section.</li>
            <li>Set to `empty`to hide the section content, but keep the section in the visible hierarchy.</li>
            <li>Put emphasis on a section by setting the display key to `highlight`.</li>
            <li>Dim the section by setting the display key to `addendum`.</li>
          </ul>
        </td>
      </tr>
      <tr>
        <td>caption</td>
        <td>Optional. By default, a section is always displayed with a header bar (value `true`). For simplifying view modes, you can hide the section header (value `false`).</td>
      </tr>
      <tr>
        <td>public</td>
        <td>Optional. By default (value `true`), all sections are visible for guests once the database is published. You can hide a section for guests (value `false`) to conceal data that is only valuable for managing the research process but not for presentation purposes.</td>
      </tr>
      <tr>
        <td>position</td>
        <td>Optional. When you click a row in the article overview table, an article preview appears in the sidebar. As article author, you can switch to revise mode to always open articles in edit mode. By default, the section order is as usual (value `false`). To speed up going through the articles, you can fix a section (value `fixed`) so authors don't need to always scroll to the section they want to revise. This feature is intended for content analysis scenarios, where you don't change the original content of a section, rather you assign categories in a section dedicated for the coding step.</td>
      </tr>
      <tr>
        <td>help</td>
        <td>By default, a help key is made up of the article type and the section type. It is generated for each section according to the following pattern: <code>articletype-&lt;type&gt;-sectiontype-&lt;type&gt;</code>. Example: <code>articletype-epi-article-sectiontype-images</code>. The help key is used to look up pages in the wiki by the IRI fragment of wiki pages. To use a specific wiki page with another IRI or the same page for different section types, provide the wiki page IRI fragment in the `help` key.</td>
      </tr>
    </tbody>
  </table>
</figure>

### Items contained in a section

The items key of a section determines what items are available in a section. It contains a list of item configuration objects.
A full item configuration object includes the following keys:

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Key</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>type</td>
        <td>Item type</td>
      </tr>
      <tr>
        <td>default</td>
        <td> By default, when a new section is created, one instance of the configured item type is automatically generated. You can disable this behavior by setting the `default` key to false
</td>
      </tr>
      <tr>
        <td>count</td>
        <td>Allow an arbitrary number of items in the section by setting the `count` key to an asterisk `*`. You can fix the number to exactly one item by setting the key to `1`.</td>
      </tr>
    </tbody>
  </table>
</figure>

In the following example, items of type "heraldry" are allowed in the section zero or more items.
Since the `default` key is set to `false`, the items are not generated automatically on section creation.
Instead you add them manually using the respective buttons in the interface.

``` plaintext
"items": [
  {
    "type": "heraldry",
    "count": "*",
    "default": false
  }
]
```

### Section templates and widgets

The following values are possible in the view key of a section type:

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Value</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>table</td>
        <td>The items within a section are listed in a table, with the column names resulting from the field definition of the items. The table view is best used to list linked properties with additional comments. Example: a list of cited literature with titles selected from the properties (first column) and optionally complemented by the specific page numbers (in another column).</td>
      </tr>
      <tr>
        <td>stack</td>
        <td>The fields of the items are listed one below the other so that as much space as possible is available for each field. This template is suitable for larger text fields. Example: show a transcription, its translation, the source reference and a dating field one below the other.</td>
      </tr>
      <tr>
        <td>list</td>
        <td>The items are listed one after the other, for example to create a list of categories linked to an article.</td>
      </tr>
    </tbody>
  </table>
</figure>

The view key can either contain one of the values or a section view configuration object. Using a section view configuration object allows further options such as displaying a map or a grid widget in addition to a table. Widgets such as `map`, `thumbs`, `grid`, or `upload` summarize the content or provide simplified user interfaces for interacting with the content.

In a section view configuration object, the view template is given in the `name` key. See the following example for a full-blown image section with thumbnails and upload widget:

```
{
  "fields": {
    "published": "Published",
    "comment": {
      "types": [
        "links",
        "transcription",
        "references",
        "text",
        "character"
      ]
    }
  },
  "items": [
    {
      "type": "images",
      "count": "*"
    }
  ],
  "view": {
      "name": "table",
      "widgets": {
        "upload": true,
        "thumbs": {
          "itemtype": "images",
          "link": true,
          "fields": [
            "sortno",
            "file"
          ]
        }
    }
  }
}
```



**Settings for the table template**

By default, a separate table is created for each item type. The columns of the table result from the fields in the item type configuration. If there are items with images (and the images are available on the server), a thumbnail view of all images is generated above the table. You can tweak the default behaviour by the following keys:

-   Set `captions` to `true` to show the item names as captions above the tables. This does not work in combination with the grouped key set to `true`.
-   Set `grouped` to `true` to generate one table for all items. The item names appear in the first column. The other colums are shared by the fields of all item types in the section.


**Map widget**

Given you have configured item types containing latitude and longitude values, you can display a map within a single article.
Example configuration (the `widgets` key is part of the section view configuration object, see above):

``` plaintext
"widgets": {
  "map": {
    "itemtypes": {
      "geolocations": {
        "segment": "Artikel",
        "field": "value",
        "edit": true
      },
      "locations": {
        "segment": "Kategorie",
        "field": "property.content"
      }
    }
  }
}
```

The `itemtypes` key contains a keyed list of item type configuration objects. You can show multiple markers on the map, for example the positions stored in the section's items and positions stored in properties used in the items. Use the `segment` key for providing legend labels, the `field` key to extract the location (the used item field or property field must be configured as a JSON field with lat and lng properties). For item positions, set `edit` to `true` if you want to allow authors to move the marker on the map.

**Thumbnail widget**

Given you have configured item types containing images, you can display a thumbnail list preceding the item table.
Example configuration (the `widgets` key is part of the section view configuration object, see above):


``` plaintext
"widgets": {
  "thumbs": {
    "itemtype": "images",
    "link": true,
    "fields": [
      "sortno",
      "file"
    ]
  }
}
```

The `link` key defines whether clicking on the image opens the image viewer. The `fields` key contains a list of item field names that provide data to be displayed along the thumbnail.

**File upload widget**

Images, by default, are uploaded in the files menu and then used in the article. In addition, you can enable an upload button to be displayed right below the item table:

``` plaintext
"widgets": {
  "upload": true
}
```

The button either opens the image folder in the sidebar or offers the option of creating the folder if it does not exist. Since the default image folder is based on the project and article signature, this is only possible if the article has been assigned to a project, has a signature and has been saved at least once.

**Grid widget**

The items are displayed in a grid. The size of the grid is stored in the section fields `layout_cols` and `layout_rows`.
Each item is positioned in this grid, multiple items may also be stacked in one cell together.
The item position is stored in the item fields `pos_x`, `pos_y`, and `pos_z`, counting starts with 1.
A grid, for example, can be used to store the position of coats of arms on an object.

## Item type configuration

Items contain the contents of an article.
Each single item represents a research proposition within an article.
The item configuration defines which fields are used, how they are labeled
and whether additional functionality (file metadata transfer, geocoding, external services) should be used for an item:

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Key</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>fields</td>
        <td>A list of field configuration objects using the fields listed below.</td>
      </tr>
      <tr>
        <td>display</td>
        <td>Optional. Display options for the item:
          <ul><li>false: Always hide the item on the frontend.</li>
            <li>highlight: Highlight the item (i.e. with a background color).</li>
            <li>addendum: Dim the item (i.e. using a smaller font size).</li>
            <li>more: Tables can grow wide when using the section table template. A more button provides decoupling the item to show all fields vertically in the sidebar or a popup.</li>
          </ul>
        </td>
      </tr>
      <tr>
        <td>edit</td>
        <td>Optional. By default, all items are editable (value `true`), you can make items readonly (value `false`).</td>
      </tr>
      <tr>
        <td>public</td>
        <td>Optional. By default (value `true`), all items are visible for guests once the database is published. You can hide an item for guests (value `false`) to conceal data that is only valuable for managing the research process but not for presentation purposes.</td>
      </tr>
      <tr>
        <td>fulltext</td>
        <td>Optional. To use fulltext search for item content, you first set the `fulltext` key of an item to `true`.
          Second, you provide the name of an index in the `fulltext` key of a field configuration object.</td>
      </tr>
      <tr>
        <td>metadata</td>
        <td>Optional. If the item contains an image file (currently only jpg and tif are supported), metadata can be automatically written into the image file during export. There are two places where mapping entity data to file metadata fields is defined: in the pipeline and in the item type configuration. In the item type configuration, include an object mapping file metadata fields (keys of the config object) to entity extraction keys (values of the config object). In the following example, data from the file_meta.copyright field is transferred to the xmp:Rights metadata field. Note that there is no dedicated copyright field in the database. Instead, in the item type's field configuration, the file_meta field should be configured to store JSON data with copyright information in the copyright subkey.
<pre>
{
  "xmp:Rights": "file_meta.copyright"
}
</pre>
          During export, image files can be renamed using placeholder keys. The target filename can be configured in the `filename` key of the metadata config object. See the extraction key help on how to use placeholders. Example:
<pre>
{
  "filename": "di-{root.project.description|json:bandnummer}-{root.signature}-{sortno|padzero:2}.{file_type}"
}
</pre>
        </td>
      </tr>
      <tr>
        <td>geodata</td>
        <td>
            Optional. If the item refers to a geolocation that should be displayed on a map, you can either store the location in a property or in the item. For both options you use JSON to store latitude, longitude, and optionally a marker radius.
            In the `geodata` key of the item type configuration you define which field to use. <br>
            Example for using the item's value field (containing JSON): <pre>"geodata": "value"</pre><br>
            Example for using the content field (containing JSON) of the property linked in the item:
            <pre>"geodata": "property.content"</pre><br>
        </td>
      </tr>
      <tr>
        <td>services</td>
        <td>Optional. In the services key you specify external services to use with an item, for example, to provide a button for automated summaries or automated annotation using an LLM. See the <a href="../configuration/services">services configuration documentation </a> for further details.</td>
      </tr>
    </tbody>
  </table>
</figure>


### Fields available in an item

Each item contains one data snippet of an article. The items provide several fields, for example, to represent images, annotated text or references to structured properties. The fields typically cover the following use cases:

- Text and translations including annotations
- Source information
- Short single character values or numbers
- Reference to structured properties or categories
- References to other sections or items within an article
- Markers for flagging content and for indicating editing progress
- Files and images
- Positions in x,y, and z dimensions
- Geographic locations in latitude using longitude values
- Natural-language datings

Although you can use all the different fields in a single item at the same time to represent complex data, it is advised to restrict each item to one proposition that is as simple as possible. For example, store the text to be analysed in one item type and use further items to link the case to properties in a category system. See below for options to generate more complex compound data representations.

Some of the field keys used in the item type field configuration do not directly correspond to the fields in the database. Rather they identify a field bundle. For example, the `date` key bundles the database fields `date_value`, `date_sort`, `date_start` and `date_end`. While a human readable date string (e.g. "13.Jh.") is stored int the `date_value` field when editing an article, the date string is automatically parsed by Epigraf to generate a sort string and to extract a range of years for filter function.

The following field keys are available in item fields configuration objects:

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Key</th>
        <th>Description</th>
        <th>Database Fields</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>sortno</td>
        <td>Number field. Used for defining a fixed item order within a section.</td>
        <td>sortno</td>
      </tr>
      <tr>
        <td>itemgroup</td>
        <td>Text field. Used to group items that belong together (see below).</td>
        <td>itemgroup</td>
      </tr>
      <tr>
        <td>property</td>
        <td>ID field. Used to refer to a property in the categories, for example to a location property. In addition, the property type (i.e. the used category system) must be specified in the `types` key of the field object.</td>
        <td>properties_id</td>
      </tr>
      <tr>
        <td>flagged</td>
        <td>0/1 flag. Used to flag content, for example, to indicate that images are intended for publication.</td>
        <td>flagged</td>
      </tr>
      <tr>
        <td>value</td>
        <td>Text field. Typically, a single number or a single text value, for example the height of an object. Can contain JSON or XML.</td>
        <td>value</td>
      </tr>
      <tr>
        <td>content</td>
        <td>Text field. Typically, the main content. Can contain JSON or XML.</td>
        <td>content</td>
      </tr>
      <tr>
        <td>translation</td>
        <td>Text field. Typically, the translation of data the content field. Can contain JSON or XML.</td>
        <td>translation</td>
      </tr>
      <tr>
        <td>file</td>
        <td>File reference. In the database, the fields file_path (folder of the file), file_name (name of the file), file_online (0/1 flag, whether the file is located on the server or only locally) and file_source (original directory of the file before uploading to Epigraf) are used. You should define a base directory where the file path starts in the `fileroot` key of the field configuration object.</td>
        <td>file_name, file_path, file_online, file_source</td>
      </tr>
      <tr>
        <td>file_meta</td>
        <td>Text field. Used for storing metadata, typically configured as a JSON field with keys for license, usage rights, creator name etc.</td>
        <td>file_meta</td>
      </tr>
      <tr>
        <td>file_copyright</td>
        <td>Deprecated.</td>
        <td>file_copyright</td>
      </tr>
      <tr>
        <td>pos</td>
        <td>Number field. Items can be arranged in two- or three-dimensional space. The horizontal and vertical extent of space is stored in the item's section in the `layout_cols` and `layout_rows` fields. The x- and y-axes represent the positions in a flat  grid. The z-axis either indicates a third dimension or simply the order of items within a cell.</td>
        <td>pos_x, pos_y, pos_z</td>
      </tr>
      <tr>
        <td>date</td>
        <td>Dating field. In the database, the fields date_value stores the natural language phrase, e.g. "E. 15.Jh.". From this string, a key for chronological sorting and the range in years covering the period are derived. You can use the derived values, for example, in the article column setup or for later analyses.</td>
        <td>date_value, date_sort, date_start, date_end</td>
      </tr>
      <tr>
        <td>date_add</td>
        <td>Text field. Additional information on the dating, for example the method used for dating.</td>
        <td>date_add</td>
      </tr>
      <tr>
        <td>source_autopsy</td>
        <td>0/1 flag. Typically used to indicate whether the content was produced by the researcher. In the case of transcriptions, for example, it is used to indicate whether the transcription was written by the article author or is copied from another source.</td>
        <td>source_autopsy</td>
      </tr>
      <tr>
        <td>source_from</td>
        <td>Text field. Typically, contains a source reference for the content. In the case of copied transcriptions, for example, a reference to the source of the transcription.</td>
        <td>source_from</td>
      </tr>
      <tr>
        <td>source_addition</td>
        <td>Text field. Typically, contains further information on the source, such as the page numbers within a book.</td>
        <td>source_addition</td>
      </tr>
      <tr>
        <td>links</td>
        <td>Polymorphic ID field. Links an item to another section or another item within the article. In the database, the fields links_tab and links_id are used to hold the target table and entity ID. (The additional fields links_field and links_tagid are not currently used; they may be used in the future for referring to specific target fields and target tags within an entity).</td>
        <td>links_tab, links_to, links_field, links_tagid</td>
      </tr>
      <tr>
        <td>norm_iri</td>
        <td>Text field. Contains the IRI fragment of the item.</td>
        <td>norm_iri</td>
      </tr>
      <tr>
        <td>published</td>
        <td>Publication status. 0 = drafted, 1= in progress, 2 = complete, 3= published,  4 = searchable.</td>
        <td>published</td>
      </tr>
    </tbody>
  </table>
</figure>

### Compound items

In case the fields are not sufficient, there are four mechanisms for more modeling complex representations:

- Deepen the content fields: Text fields can hold JSON data to extend the available fields. You can use dot separated keys to identify nested data and to generate input fields for the single JSON components.
- Use annotation markup: Text fields can hold XML with arbitrary structures. The user interface in XML fields is configured by the annotations (links and footnotes types).
- Group by sections: Sections are a means to structure the data within an article. All items within a section are considered to belong together.
- Group items: Multiple items within a section can be grouped together. Instead of thinking about all items in a section
as a unit, or you use the `itemgroup` field of an item with a common value for all items forming a logical unit.

Deepening the content fields using JSON or XML or building item groups allows for representing all data structures you can think of. In theory, there are no limits of what kind of data can be stored in Epigraf. Nevertheless, when using a database system such as Epigraf, the aim is to manage structured data in a way that is future-proof. The Relational Article Model of Epigraf has been thouroughly developed and tested in research projects.  It is advised to follow the concepts and start as simple as possible. Try to think of your data as a list of items where items belonging together are grouped by a section. This makes sure you come up with a versatile and easy to process data structure that can be used for both, all kinds of data analyses and all kinds of published documents and archives.

