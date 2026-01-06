---
title: Import CSV files
permalink: '/epigraf/user/import/csv/'
---

# Structure of CSV import files

Articles including project data, categories, and all other related data can be imported from CSV files.
For orientation purposes, we recommend downloading an existing article via the "CSV" button
or a set of articles with the "Export" button using the default data pipeline.
Adapt the file for your own import. Alternatively, the R and Python packages support the transformation of any data into the import format.

Understanding the [Relational Article Model](/epigraf/user/import) is crucial for preparing the CSV files.
The default database table into which a CSV file is imported depends on the page where the import is started.
For example, on the categories page, entities are imported into the `properties` table. The selected category system is used as default `propertytype`. On the article page, articles are imported along with their associated sections and items.

The CSV file contains column names in the first line.
A semicolon is expected as a separator between the fields, entities are separated by a line break.
Fields that contain a semicolon or a line break must be enclosed in double quotation marks.
Quotation marks are masked by doubling them. Example for importing categories:

``` plaintext
lemma;comment
Stein;"A so-called ""Lemma"" with quotation marks in the comment field"
Holz;"A second lemma"
```

You can check whether the file is valid in the import preview.

# Linking entities to each other

In order to import entities for several tables at the same time, you explicitly provide the target table of an entity.
This allows to import full articles including all necessary categories, editor and project data entities.
The target table for each row is either derived from the `id` of the entity or from a `table` column:

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>table</th>
        <th>id</th>
        <th>articles_id</th>
        <th>sections_id</th>
        <th>name</th>
        <th>content</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><span class="highlight">articles</span></td>
        <td><span class="highlight">articles</span>-tmp1</td>
        <td></td>
        <td></td>
        <td>An article</td>
        <td></td>
      </tr>
      <tr>
        <td><span class="highlight">sections</span></td>
        <td><span class="highlight">sections</span>-tmp1</td>
        <td>articles-tmp1</td>
        <td></td>
        <td>A section</td>
        <td>Comment on the section</td>
      </tr>
      <tr>
        <td><span class="highlight">items</span></td>
        <td></td>
        <td>articles-tmp1</td>
        <td>sections-tmp1</td>
        <td></td>
        <td>The content of the section</td>
      </tr>
    </tbody>
  </table>
</figure>

In the example, the target table for the article and the section entity is defined twice for each entity.
It is contained both in the temporary ID and, additionally, in the `table` column.
In contrast to the example, we recommend to always work with [IRI paths](/epigraf/user/import), that also contain the table.
IRI paths have the advantage that entities can be updated and not only created.
