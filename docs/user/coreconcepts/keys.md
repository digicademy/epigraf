---
title: Core Concepts - Path Extraction Language
permalink: '/user/coreconcepts/keys/'
---

The Path Extraction Language (PEL) was developed to extract flat tables from nested data structures.
In Epigraf, documents are represented using the Relational Article Model,
which organizes content as a hierarchy of nested objects.

Put simply, an article’s content is structured into sections and items,
and items as well as annotation in text fields are linked to properties.
Epigraf uses object-relational mapping (ORM) to load database records
into root objects that provide access to nested child objects and their data fields.
Extraction keys are then used to address specific fields within this structure.

The entities of the Relational Database Model include different types of data fields:

- **Database fields**: Fields that exist in the database.
  The fields are documented in the [development documentation](https://digicademy.github.io/epigraf/database/datamodel/) and include names,
  annotated texts, dates, items or references to properties.
  For example, an article object contains the field `signature`.
- **Virtual fields:** Some objects expose computed properties that return formatted values.
  For instance, each article object provides the virtual field `iri_path`.
  Although this field is not stored in the database, it generates an IRI path
  composed of the table name, article type, and IRI fragment.
- **Relations**: Entities can be connected through two types of relationships.
  A belongsTo relationship refers to a single related object.
  For example, an article object contains the `project` relation,  which links to a project entity.
  In contrast, hasMany relationships represent collections of related objects.
  For example, an article object contains a list of sections in the `sections` relation.
- **Ancestors** Articles contain sections, which in turn contain items that may reference properties.
  An article serves as the root entity for all nested objects.
  From any contained object (e.g., an item), the root article can be accessed via the `root` property.
  The immediate parent object is available through the `container` relation.
  In hierarchical structures such as sections, the parent section can additionally be accessed via the `parent` relation.

Data extraction in Epigraf operates across this entire object hierarchy.
For example, the columns in the articles collection table are populated
by extracting values from sections and items nested within each article object.

## Path keys

Path keys dive into an object by chaining the fields separated by a dot.

-  A simple path key contains just the name of the data field or relation. Example: `signature`.
-  A compound path key extracts nested data separated by a dot.
   Example, starting from an article object: `project.signature`.
-  A compound path key can also go up the object hierarchy. Example to get the article IRI starting from an item: `root.iri`.
-  The path can contain an asterisk * as a placeholder to address a list of objects. This allows values to be extracted from hasMany relations. Example, starting from a section: `items.*.content`.
-  You can also filter when using placeholders. The filter condition is specified in square brackets. Example: `items.*[itemtype=images].file_name`.
-  A path key can also include a format prefix, followed by a colon. Example: `txt:items.*[itemtype=transcriptions].content`.
   This is useful to render annotated XML content as plain text.

## Aggregation keys

If the values need post-processing, several processing steps can be appended to a path key with pipes `|` .
Additional processing parameters are added after a colon, multiple parameters are separated by commas.
Example:  `items.*[itemtype=images].file_name|collapse:;`

The following path functions are currently available in aggregation keys:

- collapse: Combines several values into one string.
- first: Returns the first element of a list.
- min: Returns the smallest element of a list.
- max: Returns the largest element of a list.
- count: Returns the number of elements in a list.
- distinct: Returns a list of distinct values.
- add: Extract another value from the root item using the extraction key in the first parameter.
  Adds the new value to the list of current values.
- next: Coalesce, i.e. add the value using another extraction key if the current value is empty.
- split: Split a string at new lines and return the result as an array
  (that can be processed in further steps, e.g. to select the first value).
- filter: Return elements of an array matching a given pattern. The pattern is provided as regular expression.
  The regular expression is specified without any delimiters. Example for dropping empty strings: `filter:^.+$`.
- strip: Remove all HTML tags from a string or an array of string.
- trim: Remove whitespace from both ends of a string.  You can provide other characters to be trimmed as parameter.
- ltrunc: Remove a prefix from a string.
- json: Extract a json value or a value from a nested array by the extraction key provided as first parameter.
- padzero: Pad a number with zeros. The number of digits should be passed as parameter.

## Placeholder keys

Placeholder keys are character strings that contain aggregation keys in curly brackets.
This allows complex values to be composed of literals and database content.
Example: `Source {project.description.source} - {article.created}`.
Multiple aggregation keys can be placed in square brackets separated by commas.
In this case, the first value that is not empty is returned. Example: `{[project.name,project.signature]}`.

## Named keys

Named keys are aggregation keys prefixed with a name and an equal sign.
For example, columns in a table can be named: `Signature=article.signature`.
