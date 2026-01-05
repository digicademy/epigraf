---
title: Fields configuration
permalink: '/configuration/fields/'
---

For all entity types – articles, sections, items, properties, links, and footnotes – the `fields` key is used along a field configuration object to specify which fields are to be used and how they are labelled.

Epigraf provides four kinds of fields:

- *Internal* data management fields should be treated as read-only. You can change their visibility in the frontend, but you must not change their interpretation. Examples include the ID field or the last modified field.
- Fields *reserved* for the Relational Article Model should also not be reinterpreted. For example, the norm_iri field holds unique identifiers that are used throughout the Epigraf universe to link, transfer, and publish entities. You can configure their labels and their visibility. But you should not reinterpret the fields, and you must not change their data types.
- *Customizable* fields can be configured to match your use case. They cover different data types and, as long as it is compatible with the underlying database type, the data type of such a field can be further specified. For example, you can use the content field of items to store an annotated transcription as XML, a location as GeoJSON or an image description as plain text. All of those specific types are based on text data which is the data type of the content field.
- *Virtual fields*  are calculated on the fly, thus, they can't be modified. For example, properties provide a virtual field `path` that displays the lemma path including all ancestor properties.

See the configuration pages for articles and categories or the database model page to find out what fields can be used.

In the simplest case, the field configuration consists of a list with field names as keys and labels as values.
If additional properties are to be configured, an object with the following keys can be specified as the value:

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
            <td>Field label.</td>
        </tr>
        <tr>
            <td>showcaption</td>
            <td>Whether the label should be displayed (`true` or `false`).</td>
        </tr>
        <tr>
            <td>display</td>
            <td>Display options:
                <ul>
                    <li>false: The field is hidden, except in edit mode.</li>
                    <li>hide: The field is also hidden in edit mode.</li>
                    <li>highlight: The field is highlighted.</li>
                    <li>addendum: The field contains additional information that should be displayed in a smaller font
                        size.
                    </li>
                    <li>more: The field is hidden in the tabular view and can be accessed via a More button.</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>format</td>
            <td>The field format, see below.</td>
        </tr>
        <tr>
            <td>searchfield</td>
            <td>true|false: Specifies whether the field should be used as a search field. The field then appears in the
                selection list of search fields. Certain fields are always used as search fields, regardless of the
                configuration.
            </td>
        </tr>
        <tr>
            <td>targets</td>
            <td>For fields with the formats record or relation, it is specified what can be referenced:
                <ul>
                    <li>articles: Permitted values are "internal" for internal references and "external" for references
                        to other articles.
                    </li>
                    <li>sections: The permitted section types are specified in a list.</li>
                    <li>items: The permitted content types are specified in a list.</li>
                    <li>properties: The permitted category types are specified in a list.</li>
                    <li>footnotes: The permitted types of apparatus are specified in a list.</li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>required</td>
            <td>For fields with the formats record or relation, a value must be selected by default. To also allow empty
                values (a selection option is then created for this in the drop-down selector), required can be set to
                false.
            </td>
        </tr>
        <tr>
            <td>append</td>
            <td>In the default setting, only existing categories can be selected for fields with the format record. To
                be able to create new categories ad hoc, append is set to true.
            </td>
        </tr>
        <tr>
            <td>baseurl</td>
            <td>For fields of type <code>imageurl</code>, the file name of the image is formed from the fields file_path
                and file_name and appended to the baseurl. Example: for a baseurl: <code>https://www.inschriften.net/fileadmin/</code>.
            </td>
        </tr>
        <tr>
            <td>template</td>
            <td>For JSON fields, a table is output by default. A compact list is generated using the "list" template.
            </td>
        </tr>
        <tr>
            <td>types</td>
            <td><p>In XML fields: Which annotations are permitted? A list of link names (name field of the annotation)
                or link groups (config.group field of the annotation) is specified.</p>
                In property fields: What type of properties are linked? The name of the property type is specified as a
                string.
            </td>
        </tr>
        <tr>
            <td>options</td>
            <td>For selection fields, an object with keys and labels. Example:
                <pre class="plaintext">
"file_meta": {
  "caption": "Lizenz",
  "format": "select",
  "options": {
    "": "Unklar",
    "ccby40": "CC By 4.0",
    "other": "Andere"
  }
}
</pre>
            </td>
        </tr>
        <tr>
            <td>edit</td>
            <td>Is the field editable or not (true|false)?</td>
        </tr>
        <tr>
            <td>fulltext</td>
            <td>The name of an index to which the text is to be added, e.g. "Transcription" or "Notes". For full-text
                indexing, the fulltext key must also be set in the item.
            </td>
        </tr>
        <tr>
            <td>autofill</td>
            <td>Field contents can be generated automatically from other fields during entry. To do this, the data
                source is specified as the extraction key in the source key. The extraction key contains the table name,
                the row type in square brackets, and finally the field name separated by a period.
                <p>Content is normally only overwritten if the field is empty and has not been edited manually. The
                    optional force key ensures that the field is locked for manual entry and is overwritten in all
                    cases.</p>
                <p>Example:</p>
                <pre class="plaintext">
"signature": {
  "caption": "Signatur",
    "help": "Wird automatisch aus der ersten Signatur übernommen",
    "autofill": {
      "source": "items[signatures].value",
      "force": true
  }
}
</pre>
                The values can be transformed during transfer, for example to clean them up. To do this, a list of
                transformation functions is specified in the process key. Example:
                <pre class="plaintext">
"norm_iri": {
  "caption": "IRI-Fragment",
  "autofill": {
    "source": "properties[imagetypes].name",
    "process": [
      "irifragment"
    ]
  }
}
</pre>
                The following transformations are implemented:
                <ul>
                    <li>irifragment: Converts the value to lowercase, replaces spaces with hyphens, and removes all
                        characters not allowed in IRI fragments (a-z, 0-9, hyphen, underscore, and tilde are retained).
                    </li>
                    <li>sortkey: Converts the value to lowercase, removes all special characters (a-z, 0-9, hyphen,
                        underscore, tilde and space characters are retained) and adds five leading zeros to all numbers.
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>keys</td>
            <td>Subfields can be defined for JSON fields. Each subfield is configured like a normal field.
                <p>Example:</p>
                <pre class="plaintext">
"file_meta": {
  "format": "json",
  "keys": {
    "licence": {
      "caption": "Lizenz",
      "format": "select",
      "options": {
        "": "Unklar",
        "ccby40": "CC BY 4.0",
        "other": "Andere"
      }
    }
  }
}
</pre>
            </td>
        </tr>
        <tr>
            <td>published</td>
            <td>Status value, e.g. for geocoordinates</td>
        </tr>
        <tr>
            <td>widgets.map</td>
            <td>Should a map view be displayed (true|false)?
                <p>Example:</p>
                <pre class="plaintext">
"content": {
  "caption": "Geokoordinaten",
    "showcaption": false,
    "format": "geodata",
    "widgets": {
      "map": true
    },
    "keys": {
      "lat": "Latitude",
      "lng": "Longitude",
      "radius": "Radius"
    },
    "template": "list"
},
"published": "Veröffentlicht"</pre>
            </td>
        </tr>
        <tr>
            <td>services</td>
            <td><a href="../configuration/services">Service configurations</a> used to update the field, e.g. a
                reconciliation service configuration.
            </td>
        </tr>
        </tbody>
    </table>
</figure>


## Field formats

The database determines the field format, whether it is a text field or a numeric field.
You can refine the behavior by overriding the format key, particularly for text fields:

<ul>
<li>xml: XML content. The available annotations are defined in the `types` key.</li>
<li>json: JSON content. Such a field can either be populated using a JSON editor, or inputs for specific subkeys
can be configured using the `keys` key.
</li>
<li>select: A selection list, see the `options` key.</li>
<li>check: A checkbox.</li>
<li>date: A field containing a natural-language date.</li>
<li>file: A file name input.</li>
<li>image: The field contains a file name for rendering an image. The file must be present on the server.</li>
<li>imageurl: The field contains a URL for rendering an image. Used for images not present on the server.</li>
<li>link: The field contains a URL that should be linked.</li>
<li>geodata: The field contains coordinates and, optionally, a radius in a JSON object with the
keys lat, lon and radius.</li>
<li>normdata: The field contains authority data. Multiple identifiers are separated by line
breaks. You can use the namespaces configured in the property or article type. This way, the identifiers are linked.
</li>
</ul>

Further fields are used for relating different entities to each other:

<li>record: References to entities outside the root entity, e.g. to link from one article to another. Only available for polymophic ID fields that store a target table and a target ID. See the `targets` key.</li>
<li>relation: References within the root entity, e.g. to link from an item to a section. Only available for polymophic ID fields that store a target table and a target ID. See the `targets` key.</li>
<li>property: Link to a property. Only available for property ID fields. See the `types` key.</li>
<li>sectionname: Within articles, to use values from the properties as section names, first, set the key `sectionname.option` in the section type configuration to a property type. Second, set the format of the property field of an item to `sectionname`. Note that this establishes a permanent relation between the item and the property, but (by now) the section name is not automatically updated once the property content changes.
</li>
