---
title: Import Data
permalink: '/epigraf/user/import/'
---

# The Relational Article Model (RAM)

CSV files and XML files can be imported into the databases using the "Import" button in the footer of the pages.
Alternatively, the API can be used to import data. An R package and a Python package simplify API operations.

The import function allows both, new articles, sections and categories to be created and existing entities to be updated.
To prepare data imports, a basic understanding of the [data model](/epigraf/user/coreconcepts/model) will be helpful.
Epigraf implements the Relational Article Model (RAM) to store documents in tables for projects, articles, sections, items, links, footnotes and properties.

When it comes to data import, the most crucial aspect of the RAM are IRIs. IRIs are globally unique identifiers. In Epigraf databases,
each entity has an IRI and you use them to prepare an import table. For example, to import categories, the import table can be structured as follows:

<figure class="table">
    <table>
        <thead>
        <tr>
            <th>Id</th>
            <th>Lemma</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>properties/categories/scifi</td>
            <td>Science fiction</td>
        </tr>
        <tr>
            <td>properties/categories/musical</td>
            <td>Musical</td>
        </tr>
        <tr>
            <td>properties/categories/drama</td>
            <td>Drama</td>
        </tr>
        </tbody>
    </table>
</figure>

In the example, the ID field is populated with IRI paths. Each IRI path consists of the target table `properties`, the property type `categories` and a unique IRI fragment for the entity. The table is fixed. Which types are available within a table is defined in the types configuration of a database. The type used here refers to the movies sample database. The IRI fragment is an arbitrary identifier, it can contain numbers or letters.

Using IRI paths, first, ensures that no new entities are created when the same data is imported twice. Instead, the import function compares the IRIs to the database to determine whether an entity already exist. Entities with an existing IRI path are overwritten, otherwise new entities are created.

Second, IRI paths are used to link records to each other. For example, to import text into an article, the import table can be structured as follows:

<figure class="table">
    <table>
        <thead>
        <tr>
            <th>id</th>
            <th>name</th>
            <th>content</th>
            <th>projects_id</th>
            <th>articles_id</th>
            <th>sections_id</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><span class="highlight">projects/default/movies</span></td>
            <td>Movies</td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><span class="highlight">articles/default/0001</span></td>
            <td>Chronicles of Narnia</td>
            <td></td>
            <td>projects/default/movies</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><span class="highlight">sections/text/0001</span></td>
            <td>Abstract</td>
            <td></td>
            <td>projects/default/movies</td>
            <td>articles/default/0001</td>
            <td></td>
        </tr>
        <tr>
            <td>items/text/0001</td>
            <td></td>
            <td>The Chronicles of Narnia is a series of films based on the novels by C.S. Lewis.</td>
            <td><span class="highlight">projects/default/movies</span></td>
            <td><span class="highlight">articles/default/0001</span></td>
            <td><span class="highlight">sections/text/0001</span></td>
        </tr>
        </tbody>
    </table>
</figure>

In the example, an item entity with the description of a movie is created in the last line. The description ends up in an item of type `text` with the IRI fragment `0001`. This item is inserted into the section with the IRI path `sections/text/0001`, which in turn is created in the article with the IRI path `articles/default/0001`.

Such import tables following the RAM can be imported directly as CSV files. Alternatively, XML files mapping the very same table structure can be imported. The R and Python packages support the conversion of data into the RAM format and upload import tables directly from R or Python scripts with a single command.

# Which fields are available?

In principle, all fields that are included in the entity export or that are documented in the [development documentation](https://digicademy.github.io/epigraf/database/datamodel/) are available for the import. All target tables share their fields when importing, for example for the name of an article and the name of a section. Fields that are irrelevant for an entity – such as the content field for articles – simply remain empty.

For some fields, aliases can be used to keep the file clearer:

-   In the respective tables, the field `type` can be used instead of projecttype, articletype, sectiontype, itemtype, propertytype, usertype, scope or from_tagname.
-   In the items table, the field `to_id` can be used instead of links_tab and links_id. Usually it contains an IRI path of the target entity.
-   In the links and footnotes tables, the field `root_id` is sufficient instead of root_tab and root_id, provided that the table can be derived from the provided IRI path. The same applies to `from_id` and `to_id`.

See the aliases and their corresponding database fields as listed below.

## Project import fields

<figure class="table">
    <table>
        <thead>
        <tr>
            <th>Alias</th>
            <th>Explanation</th>
            <th>Field in the data model</th>
            <th>Example</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>id</td>
            <td>IRI path</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>published</td>
            <td>Publication state 0 to 4.</td>
            <td>published</td>
            <td></td>
        </tr>
        <tr>
            <td>type</td>
            <td>Entity type</td>
            <td>projecttype</td>
            <td></td>
        </tr>
        <tr>
            <td>iri</td>
            <td>IRI fragment</td>
            <td>norm_iri</td>
            <td></td>
        </tr>
        <tr>
            <td>sortno</td>
            <td>sort number</td>
            <td>sortno</td>
            <td></td>
        </tr>
        <tr>
            <td>signature</td>
            <td>Short title</td>
            <td>signature</td>
            <td></td>
        </tr>
        <tr>
            <td>name</td>
            <td>Long title</td>
            <td>name</td>
            <td></td>
        </tr>
        <tr>
            <td>content</td>
            <td>Project metadata in JSON format</td>
            <td>description</td>
            <td></td>
        </tr>
        <tr>
            <td>norm_data</td>
            <td>Authority data</td>
            <td>norm_data</td>
            <td></td>
        </tr>
        </tbody>
    </table>
</figure>

## Article import fields

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Alias</th>
        <th>Explanation</th>
        <th>Field in the data model</th>
        <th>Example</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>id</td>
        <td>IRI path</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>published</td>
        <td>Publication state 0-4</td>
        <td>published</td>
        <td></td>
      </tr>
      <tr>
        <td>type</td>
        <td>Entity type</td>
        <td>articletype</td>
        <td></td>
      </tr>
      <tr>
        <td>iri</td>
        <td>IRI fragment</td>
        <td>norm_iri</td>
        <td></td>
      </tr>
      <tr>
        <td>sortno</td>
        <td>sort number</td>
        <td>sortno</td>
        <td></td>
      </tr>
      <tr>
        <td>signature</td>
        <td>Article dentifier (text)</td>
        <td>signature</td>
        <td></td>
      </tr>
      <tr>
        <td>name</td>
        <td>Article title (text)</td>
        <td>name</td>
        <td></td>
      </tr>
      <tr>
        <td>status</td>
        <td>Article status (text)</td>
        <td>status</td>
        <td></td>
      </tr>
      <tr>
        <td>norm_data</td>
        <td>Authority data (text)</td>
        <td>norm_data</td>
        <td></td>
      </tr>
      <tr>
        <td>creator</td>
        <td>Article author (IRI path)</td>
        <td>created_by</td>
        <td></td>
      </tr>
      <tr>
        <td>modifier</td>
        <td>Article editor (IRI path)</td>
        <td>modified_by</td>
        <td></td>
      </tr>
      <tr>
        <td>project</td>
        <td>Project (IRI path)</td>
        <td>projects_id</td>
        <td></td>
      </tr>
    </tbody>
  </table>
</figure>

## Section import fields

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Alias</th>
        <th>Explanation</th>
        <th>Field in the data model</th>
        <th>Example</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>id</td>
        <td>IRI path</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>published</td>
        <td>Publication state 0-4</td>
        <td>published</td>
        <td></td>
      </tr>
      <tr>
        <td>type</td>
        <td>Entity type</td>
        <td>sectiontype</td>
        <td></td>
      </tr>
      <tr>
        <td>iri</td>
        <td>IRI fragment</td>
        <td>norm_iri</td>
        <td></td>
      </tr>
      <tr>
        <td>sortno</td>
        <td>Sort number</td>
        <td>sortno</td>
        <td></td>
      </tr>
      <tr>
        <td>number</td>
        <td>Section number (number)</td>
        <td>number</td>
        <td></td>
      </tr>
      <tr>
        <td>name</td>
        <td>Section name (text)</td>
        <td>name</td>
        <td></td>
      </tr>
      <tr>
        <td>signature</td>
        <td>Alternative secion name (text)</td>
        <td>alias</td>
        <td></td>
      </tr>
      <tr>
        <td>content</td>
        <td>Section notes (text)</td>
        <td>comment</td>
        <td></td>
      </tr>
      <tr>
        <td>layout_cols</td>
        <td>Number of columns in a grid (number)</td>
        <td>layout_cols</td>
        <td></td>
      </tr>
      <tr>
        <td>layout_rows</td>
        <td>Number of rows in a grid (number)</td>
        <td>layout_rows</td>
        <td></td>
      </tr>
      <tr>
        <td>articles_id</td>
        <td>Article (IRI path)</td>
        <td>articles_id</td>
        <td></td>
      </tr>
      <tr>
        <td>parent_id</td>
        <td>Parent section (IRI path)</td>
        <td>parent_id</td>
        <td></td>
      </tr>
    </tbody>
  </table>
</figure>

## Item import fields

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Alias</th>
        <th>Explanation</th>
        <th>Field in the data model</th>
        <th>Example</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>id</td>
        <td>IRI path</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>published</td>
        <td>Publication state 0-4</td>
        <td>published</td>
        <td></td>
      </tr>
      <tr>
        <td>type</td>
        <td>Entity type</td>
        <td>itemtype</td>
        <td></td>
      </tr>
      <tr>
        <td>iri</td>
        <td>IRI fragment</td>
        <td>norm_iri</td>
        <td></td>
      </tr>
      <tr>
        <td>sortno</td>
        <td>Sort number</td>
        <td>sortno</td>
        <td></td>
      </tr>
      <tr>
        <td>value</td>
        <td>A single value (text)</td>
        <td>value</td>
        <td></td>
      </tr>
      <tr>
        <td>content</td>
        <td>Text content</td>
        <td>content</td>
        <td></td>
      </tr>
      <tr>
        <td>translation</td>
        <td>Translation text</td>
        <td>translation</td>
        <td></td>
      </tr>
      <tr>
        <td>property</td>
        <td>Linked category (IRI path)</td>
        <td>properties_id</td>
        <td></td>
      </tr>
      <tr>
        <td>pos_x</td>
        <td>Position in the grid (number)</td>
        <td>pos_x</td>
        <td></td>
      </tr>
      <tr>
        <td>pos_y</td>
        <td>Position in the grid (number)</td>
        <td>pos_y</td>
        <td></td>
      </tr>
      <tr>
        <td>pos_z</td>
        <td>Position in the grid (number)</td>
        <td>pos_z</td>
        <td></td>
      </tr>
      <tr>
        <td>sections_id</td>
        <td>Section (IRI path)</td>
        <td>sections_id</td>
        <td></td>
      </tr>
      <tr>
        <td>articles_id</td>
        <td>Article (IRI path)</td>
        <td>articles_id</td>
        <td></td>
      </tr>
    </tbody>
  </table>
</figure>

*To be added: to_id, flagged, file_\*, date_\*, source_\**

## Types import fields

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Alias</th>
        <th>Explanation</th>
        <th>Field in the data model</th>
        <th>Example</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>id</td>
        <td>IRI path</td>
        <td></td>
        <td></td>
      </tr>
      <tr>
        <td>type</td>
        <td>Scope of the type configuration</td>
        <td>scope</td>
        <td></td>
      </tr>
      <tr>
        <td>iri</td>
        <td>IRI fragment</td>
        <td>norm_iri</td>
        <td></td>
      </tr>
      <tr>
        <td>sortno</td>
        <td>Sort number</td>
        <td>sortno</td>
        <td></td>
      </tr>
      <tr>
        <td>name</td>
        <td>Type name</td>
        <td>name</td>
        <td></td>
      </tr>
      <tr>
        <td>caption</td>
        <td>Type label (text)</td>
        <td>caption</td>
        <td></td>
      </tr>
      <tr>
        <td>mode</td>
        <td>Mode (text)</td>
        <td>mode</td>
        <td></td>
      </tr>
      <tr>
        <td>category</td>
        <td>Type category (text)</td>
        <td>category</td>
        <td></td>
      </tr>
      <tr>
        <td>description</td>
        <td>Type decription (text)</td>
        <td>description</td>
        <td></td>
      </tr>
      <tr>
        <td>config</td>
        <td>Type configuration in JSON format</td>
        <td>config</td>
        <td></td>
      </tr>
    </tbody>
  </table>
</figure>

# How are entities linked to each other?

Projects contain articles, articles consist of sections and sections contain items. The items in turn refer to properties.
The link between all these entities is established during import via IDs. IDs can be created in three different ways:

-   **IRI paths** (Internationalized Resource Identifiers) are particularly flexible and recommended, as they allow [data transfer](/epigraf/user/export/transfer) between different databases without knowing the internal database IDs. They are formed according to the scheme `<table>/<type>/<irifragment>`. Example: `properties/languages/iso-de-de`.
-   **Database IDs** must correspond to an existing entity. They are used to overwrite existing data or to refer to existing data. These IDs are formed according to the scheme `<table>-<id>`, where the placeholder `<id>` contains an existing numeric ID. Example: `articles-1`.
-   **Temporary IDs** are not imported into the database, but are only used for linking within a CSV file. They are formed according to the scheme `<table>-tmp<id>`, i.e. the table name is followed by the prefix "tmp" after a hyphen and then a custom name, which can be composed of any letters and numbers. When importing entities using temporary IDs, database-specific IDs are automatically created and used for all fields with the same temporary ID. Example: `articles-tmp123`.

In general, IRI paths are suitable for both, importing new data and updating existing data. Database IDs only work for updating existing data. Temporary IDs are rarely useful; they are only useful for one-time initial imports because new entities are created each time the import process is repeated. Here is an example of an import table with temporary IDs instead of IRI paths:

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>id</th>
        <th>articles_id</th>
        <th>sections_id</th>
        <th>name</th>
        <th>content</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><span class="highlight">articles-tmp1</span></td>
        <td></td>
        <td></td>
        <td>An article</td>
        <td></td>
      </tr>
      <tr>
        <td>sections-tmp1</td>
        <td><span class="highlight">articles-tmp1</span></td>
        <td></td>
        <td>A section</td>
        <td>Comment on the section</td>
      </tr>
      <tr>
        <td>items-tmp1</td>
        <td>articles-tmp1</td>
        <td>sections-tmp1</td>
        <td></td>
        <td>The content of the section</td>
      </tr>
    </tbody>
  </table>
</figure>

In the example, not only a separate ID is specified for a section, but also the ID of the associated item; the same applies to the item entity. The entities are therefore linked to each other during import.

In the import preview, you can see whether IDs in an import can be resolved to existing data.
All entities found in the database, based on IRI paths or IDs, are highlighted in green. Unmarked entities are new to the database and will be created.

# How are entities updated instead of newly created?

If IRIs or database IDs are used (see above) and an entity with the same IRI or ID already exists, it is not created again, but overwritten. Two variants for noting the IRI paths are supported:

-   A **complete IRI path** is given in the `id` column, for example "properties/languages/iso-de-de".
-   The **components of the IRI path** are given in the respective columns. The table name "properties" results from the `table` column (or from a temporary ID). In addition, the entity type "languages" must be specified in the `types` column so that the IRI path can be derived. The IRI fragment is given in the `iri` column, for example "iso-de-de".

Further behavior can optionally be controlled via the `_action` and `_fields` columns:

- **\_action=clear**: Entities contained in the current entity are deleted to make room for the following entities. For example, a section can be cleared before new items are imported in the following steps.
- **\_action=skip**: The entity is not imported, i.e. it is not overwritten and not created. The entity is only included in the import data to serve as intermediary link target or link source.
- **\_action=link**: The entity is only created if it does not exist yet. You can use this option, for example, to create non-existing properties without overwriting content of existing properties.
- **\_fields**: All tables in an import file share the columns. This is how the comment of a section as well as the text of an item both are imported from the `content` field. If the field is empty, it is cleared. Alternatively, the `_fields` column specifies which fields should be taken into account. List all fields to be considered, separated with commas (note: do not forget the ID fields). For example, this ensures that a comment for a section is not overwritten, but a transcription in an item entity is updated, although the import file contains a shared content column and, thus, an empty comment field would clear the comment of a section by default.  If the `_fields` column is missing or empty, all fields are considered.
