---
title: Columns Configuration
permalink: '/user/configuration/columns/'
---

For the overview tables for projects, articles, and categories, the `columns` key specifies which columns are available.
In the simplest case, the configuration contains a list of column names as keys and labels as values, for example:

``` plaintext
{
  "columns": {
    "lemma": "Lemma",
    "sortkey": "Sorting key",
    "articles_count": "Articles",
    "norm_data": "Authority data",
    "norm_iri": "IRI fragment",
    "id": "ID"
  }
}
```

The key can contain any database field, including virtual fields, and dot separated extraction keys.
See the following example on how to display the number of annotations and the editor name using extraction keys:

``` plaintext
{
  "links.*|count": "Annotations",
  "modifier.name": "Editor"
}

```

For further options, use an arbitrary name as key and a column configuration object as value.
In the following example, you see the full column configuration object for the number of annotations and the editor name:

``` plaintext
{
  "annotations": {
    "caption": "Annotations",
    "key": "links.*",
    "aggregate": "count"
  },
  "editor": {
    "caption": "Editor",
    "key" : "modifier.name"
  }
}
```


Column configuration objects support the following keys:

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
                <td>caption</td>
                <td>Column caption in the table.</td>
            </tr>
            <tr>
                <td>name</td>
                <td>Unique name for the field. The name is used in the URL query parameters, for example, when sorting by a field. By default, the key of a column configuration object is used.</td>
            </tr>
            <tr>
            <td>default</td>
            <td>By default (value `true`), all configured columns are visible. If you hide a column (value `false`) it will be visible if the user selects it using the column settings button.</td>
            </tr>
            <tr>
                <td>key</td>
                <td>Path key to extract the data from the entity.
                 <a href="../coreconcepts/model">Path extraction keys</a> consist of simple entity fields (e.g., `signature`) or nested fields with dot notation (e.g., `project.signature`). To multiple values in a list, use the asterisk placeholder (e.g., `items.*.value`). Lists can be filtered with conditions in square brackets (e.g. `items.*[itemtype=conditions].date`).
                <br>
                By default, path extraction keys result in the raw field value. For example, if the content field of a property contains JSON, a JSON string is returned. To format such nested data, use an array with at least two elements. The first elements contain the extraction key, and the last element contains the field name to be formatted, for example: `["project","description"]`.</td>
            </tr>
            <tr>
                <td>value</td>
                <td>Alternatively, a <a href="../coreconcepts/model">placeholder extraction key</a> instead of a path extraction key. Placeholder extraction keys support constructing a value from multiple fields and fixed strings.</td>
            </tr>
            <tr>
                <td>aggregate</td>
                <td>If the extraction key results in multiple values, use an aggregation function to summarize it (min, max, count, collapse, false). Single values can be postprocessed using a transformation function (strip). Multiple steps can be separated with a pipe. Example: `strip|collapse` would remove all tags from the values and then collapse multiple values (if present) to a comma-separated list.</td>
            </tr>
            <tr>
                <td>sort</td>
                <td>By default, sorting is automatically derived from the extraction key. To disable sorting, set the `sort` key to `false`. Alternatively, provide a sort configuration object that is used to construct SQL joins:
                    <ul>
                        <li>table: Table that is joined just for sorting.</li>
                        <li>conditions: Additional filters, for example `{"itemtype":"heraldry"}`.</li>
                        <li>field: Sort field in the table.</li>
                        <li>aggregate: Agggregation function if the join results in multiple rows: `min`, `max`, or `count`.</li>
                        <li>cast: Type casting using an SQL type, e.g.  `INTEGER`.</li>
                    </ul>
                </td>
            </tr>
            <tr>
                <td>link</td>
                <td>URL configuration object to generate a link. Placeholders in curly brackets are replaced by the entity contents. Example:<br />
            <pre class="plaintext">
              "link": {
                "controller": "articles",
                "action": "index",
                "?": {
                  "properties.{propertytype}": "{id}",
                  "load": "true"
                }
              }
            </pre></td>
            </tr>
            <tr>
            <td>icon</td>
            <td>A Unicode character that is displayed in tiles as an icon in front of the summary line.</td>
            </tr>
            <tr>
            <td>width</td>
            <td>Column width in pixels.</td>
            </tr>
        </tbody>
    </table>
</figure>


