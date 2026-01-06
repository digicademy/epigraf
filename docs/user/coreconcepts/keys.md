---
title: Core Concepts - Data Fields and Extraction Keys
permalink: 'user/coreconcepts/keys/'
---

Entities such as projects, articles, sections, items, or categories are represented within the application objects.
Epigraf uses object-relational mapping (ORM) as an established technique for accessing the data. Each object has:

- **Database fields**: Fields that exist in the database. The fields are listed in the [development documentation](https://digicademy.github.io/epigraf/database/datamodel/) and include names, annotated texts, dates, items or references to properties. For example, an article object contains the data field `signature`.
- **Virtual fields:** Some objects have properties that return formatted values. For example, an article object has the virtual field `iri_path`. Although this field does not exist in the database, it returns the IRI path consisting of table name, article type and IRI fragment.
- **Relations**: Two types of relations to other entities can occur. A belongsTo relationship refers to a single other object. For example, an article object contains the relation `project`, which contains the project entity object. In contrast, hasMany relationships contain a list of related objects. For example, an article object contains the list of sections in the `sections` relation.
- **Ancestors** Articles contain sections, which in turn contain items that can refer to properties. The article is the root entity object for all contained objects. From contained objects such as an item, it can be accessed via the `root´ property. The respective parent object can be accessed via the `container` property. In hierarchical tables such as sections, the parent section can be accessed via the `parent` property.

Data is extracted from these objects everywhere in the application. For example, the columns in the articles table get their values by extracting them from entity objects. For extracting data, different kinds of extraction keys are used, for example in the types configuration:

- **Path keys** dive into an object by chaining the fields separated by a dot.
    -   A simple path key contains just the name of the data field or relation. Example: `signature`.
    -   A compound path key extracts nested data separated by a dot.
        Example, starting from an article object: `project.signature`.
    -   A compound path key can also go up the object hierarchy. Example to get the article IRI starting from an item: `root.iri`.
    -   The path can contain an asterisk * as a placeholder to address a list of objects. This allows values to be extracted from hasMany relations. Example, starting from a section: `items.*.content`.
    -   You can also filter when using placeholders. The filter condition is specified in square brackets. Example: `items.*[itemtype=images].file_name`.
- **Aggregation keys**: If the values need post-processing, several processing steps can be appended to a path key with pipes `|` . Additional processing parameters are added after a colon, multiple parameters are separated by commas. Example:
    `items.*[itemtype=images].file_name|collapse:;`
- **Placeholder keys** are character strings that contain aggregation keys in curly brackets. This allows complex values to be composed of literals and database content. Example: `Source {project.description.source} - {article.created}`. Multiple aggregation keys can be placed in square brackets separated by commas. In this case, the first value that is not empty is returned. Example: `{[project.name,project.signature]}`.
- **Named keys** are aggregation keys prefixed with a name and an equal sign. For example, columns in a table can be named: `Signature=article.signature`.

The following path functions are currently available in aggregation keys:
- collapse: Combines several values into one string
- first: Returns the first element of a list
- min: Returns the smallest element of a list
- max: Returns the largest element of a list
- count: Returns the number of elements in a list
- split: Split a string at new lines and return the result as an array (that can be processed in further steps, e.g. to select the first value)
- filter: Return elements of an array matching a given pattern. The pattern is provided as regular expression.
- strip: Remove all HTML tags from a string or an array of string
- trim: Remove whitespace from both ends of a string.  You can provide other characters to be trimmed as parameter.
- ltrunc: Remove a prefix from a string.
- json: Extract a json value or a value from a nested array by the extraction key provided as first parameter
- padzero: Pad a number with zeros. The number of digits should be passed as parameter.
