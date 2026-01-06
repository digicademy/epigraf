---
title: Model
permalink: 'devel/backend/model/'
---


The purpose of the model layer is to retrieve, modify, and store data in the database.
Epigraf uses the CakePHP ORM to map database records to entities.

The model always combines a table class and an entity class:
- **Table** classes implement finders for database queries and functions for data validation and manipulation.
  Tables are linked by belongsTo and hasMany associations, which are defined in the table classes.
  Pay attention to the CakePHP callbacks in table classes such as `beforeSave()` and `afterSave()`.
- **Entities** contain the content of database records (or form data posted from the browser to add or update entities).
  Each entity corresponds to one row in the database.
  The database fields are available in the entity properties, each field corresponds to a property.
  Epigraf widely makes use of magic methods to access entity properties.
  Getters and setters may manipulate existing field content.
  Further, virtual fields, not directly stored in the database, are generated on the fly.

In summary, query results retrieved by table classes are a collection of entities.
Fields are available in entity properties.

## Table and Entity Classes

### Application Level Models
Application level data is managed by models in the `src/Model` folder.
All Tables are derived from `src/Model/Table/BaseTable.php` and all Entities from `src/Model/Entity/BaseEntity.php`.

Common methods in the table classes are `findHasParams()` and `findContainFields()`
which are used by the controllers to filter and retrieve data from the database based on query parameters.

![Model class hierarchy (App)](../assets/img/classes-model-app.png)

There are some pecularities in the models. You may need to read the section about project database models
to understand the full picture:

-  **Docs**: Epigraf makes use of scoped database tables. That means: One database table can be shared by different classes.
   A scope field is used to determine, which class is responsible for which data segment.
   On the application level, the database table `docs` is scoped in such a way using the `segment` field
   with the possible values `wiki`, `help` and `page`.
   It holds data for the wiki (managed by the Wiki entity class)
   and the help and the public pages (both managed by the Doc entity class).

-  **Files**: The database mirrors the file system. Because this is needed for application and for project databases,
   in different folders, the FileRecord entity is implemented in the shared FilesRequest plugin.
   The application model's FileRecord entity derives from the plugin's FileRecord entity.

-  **DefaultType**: The DefaultType entity is a virtual fallback entity,
   without a corresponding database table in the application database.
   It serves to make the application entities compatible with the project entities.
   On the project database level, functions for retrieving field values use the type information for rendering.
   An entity's type entity is accessed by its `type` property, for example, to determine the label when rendering a document.
   Records in project database tables such as `articles` or `properties` have a field `articletype` or `propertytype` respectively.
   The type determines the meaning, labeling and data format of the record's fields.
   For each type, a configuration record exists in the project databases `types` table.
   The type entity is loaded along with the entities from the database and is available in the entity's `type` property.
   On the application level, the `type` property accesses an instance of the DefaultType entity class .

### Project Level Models
Project databases are managed by models in the `plugins/Epi/src/Model` folder.

Tables classes are usually derived from `plugins/Epi/src/Model/Table/BaseTable.php`
and entity classes from `plugins/Epi/src/Model/Entity/BaseEntity.php`.
Both project database level base classes extend the corresponding application's base classes.

![Model class hierarchy (Epi)](../assets/img/classes-model-epi.png)

Some special cases have to be considered:

-  As on the application level, the FileRecord entity is implemented in the shared FilesRequest plugin.
   The project model's FileRecord entity derives from the plugin's FileRecord entity.
-  The note entity directly inherits from the application's doc entity.
   Thus, it shares the same code with the application level.
-  Top level container entities inherit from RootEntity,
   which is an abstract class without a corresponding database table.
   Sections, items, links and footnotes exist within the containers.
-  The types table holds configurations for projects, articles, sections, items, links and footnotes records.
-  Sections are organised hierarchically. To simplify index generation in export pipelines,
   virtual SectionPath entities are used to represent the ancestor path in sections and items.
-  For generating indexes (e.g. in the export pipelines) entities without database tables are used:
   IndexSegment, IndexProperty, IndexSection, IndexLink. They mirror the underlying database entities.
-  The token and lock classes are legacy code for compatibility with EpiDesktop.
   They will be removed in future versions.
-  The tag class is used to hold content found within other data objects that is tagged with XML tags,
   such as references in footnotes or citations in items.
-  The meta table managed by MetaTable holds version information about the project databases.
   Single entities are loaded into the default CakePHP Entity class.


### Common Behaviours

The table and entity classes share functionality by inheritance as described above.
Other mechanisms for shared functionality include:

- Behaviours extend table classes with additional methods.
  For example, tree structures are managed by the TreeBehaviour class.
- Traits: Traits are used to share methods between classes and to separate general functionality.
  For example, the TreeTrait is used in the PropertyEntity to provide function for navigating the tree structure.
- Interfaces are used to ensure that classes implement methods required by other classes.
  For example, the ExportTableInterface defines the functions getExportCount() and getExportData()
  that are called from the Job classes on different models.

#### Behaviors
- **ImportBehavior**: Import csv or xml files and transfer data between databases.
  Records are mapped using identifiers, allowing patch operations in addition to adding and deleting data.
  Links between the records are resolved.
- **ModifierBehavior**: Save the current user's ID to the created_by and modified_by fields.
- **VersionBehavior**: Implement versioning and soft-deletion for database tables.
  Soft deletion is achieved by using a `deleted` field, where a value of 0 denotes active records,
  1 marks a record as deleted without actually removing it. Versioning is implemented using a `version_id` field;
  when a record is updated, a copy is created with `deleted = 2` and `version_id` linking to the original.
- **VersionedTreeBehavior**: Override the callbacks of CakePHP's tree behaviour
  to disable tree modifications for versioned records and for records that are soft-deleted.
- **IndexBehavior**: Collect properties including their links to add them to an index, e.g. for articles exports.
- **PositionBehavior**: Add tree properties (e.h. number of children) after loading hierarchical data from the database.
- **XmlStylesBehavior**: Parse XML fields and render them to different output formats
  such as XML, HTML, Markdown or plain text, based on the types configuration.

#### Interfaces
- **ExportEntityInterface, ExportTableInterface**: Functions for data export.
- **MutateEntityInterface, MutateTableInterface**: Functions for batch operations.
- **ScopedTableInterface**: Scope function, which allow a database table to be used for different entities.

## Domain Model Configuration

The Epigraf user interface is highly configurable to support domains such as letter editions or social media corpora.
The configuration defines which fields from a database record are used with which labels in the specific domain.
The configuration is stored in the types table. Each typed entity has access to its type record by the type property.

See the user documentation for information about how to configure the domain model.

## Retrieving Field Values

Once you have retrieved entities from the database,
you can use extraction keys to access the data.

Epigraf extraction keys support renaming columns, using placeholder strings,
getting nested data using dot notation, filtering data,
and piping the data through postprocessing steps.

Placeholder strings are handled by `BaseEntity::getValuePlaceholder()`,
which calls `BaseEntity::getValueNested()` to extract target fields,
which in turn calls `BaseEntity::getValueFormatted()` to format the field value.

### Placeholder Strings

Placeholder strings are the most versatile extraction method as they combine literal
strings and placeholders. They are used for triple generation.

For example, if you want to generate a list of IRIs prefixed with a namespace, you
can use the following placeholder string on an article entity:

```
$article->getValuePlaceholder('epi:{sections.*.iri}')
```

Placeholders in curly brackets consists of an extraction path,
optionally followed by processing steps, each seperated by a pipe.

See `BaseEntity::getValuePlaceholder()` and `Objects::processValues()` for more information.


### Extraction Paths

Extraction paths consist of dot separated path elements.
For example, if you want to extract the lemmata of all properties used in article items
of type 'personnames', you can dive into the article structure by an extraction path:

```
$article->getValueNested('sections.*.items.*[itemtype=personnames].property.lemma')
```

A path element is a field name or an asterisk.
Asterisks match all keys of an array or object,
they are particularly used to match items in a list.
Path elements may contain conditions in square brackets.

All but the last path elements are used to extract the target field using `Objects::extract()`.
The last path element is the target field, handled by `BaseEntity::getValueFormatted()` to output rendered data.

See `BaseEntity::getValueNested()` for more information.

### Rendered Field Values

Once a target field has been extracted, the value can be formatted according to the
field configuration and the output format.

The following example renders XML data as plain text
(if the content field is configured as XML format):

```
$item->getValueFormatted('content', ['format' => 'txt'])
```

The same method can be used to extract values from JSON fields:
```
$item->getValueFormatted('value.lat', ['format' => 'txt'])
```

See `BaseEntity::getValueFormatted()` for more information.

In case the extraction key is prefixed with a format key, separated by a colon,
this overrides the format option of `getValueFormatted()`.
