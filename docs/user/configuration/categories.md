---
title: Categories
permalink: '/user/configuration/categories/'
---

Categories are used in items and annotations to add structured data to articles.
They are stored in the properties table and each category system is configured as a separate property type.

There are four types of properties:

- Properties used in articles and annotations.
- Meta-properties used to categorize other properties in their `properties_id` field.
- Properties used as categories to structure the tree, they are flagged in the `iscategory` field.
- See-references establishing an edge between a source and a target property across the tree by using the `related_id` field.


# Configuration options

The configuration for each property type includes the following keys:

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
            <td>A list of field configuration objects. See the field configuration documentation for further details.</td>
        </tr>
     <tr>
        <td>columns</td>
        <td>Optional. A list of column configuration objects, keyed by column identifiers.
            Used for the table view. See the columns configuration documentation for further details.</td>
      </tr>
      <tr>
        <td>type</td>
        <td>Indicates whether the categories form a hierarchy.
            A tree structure is assumed by default (value ´tree`),
            you can collapse it (value `collapsed`) or
            configure a flat list ( value `flat`).
            For tree structures, in addition, you must configure the parent_id field.
        </td>
      </tr>
      <tr>
        <td>displayfield</td>
        <td>By default, the `lemma` field should be used as displayfield value. This determines the
            value, for example, displayed in select lists.
            Sometimes you have short and long titles for a property. For example, for manageing literature,
            you store the full bibliographic reference in the `name` field and a short title in the `lemma`.
            In this case, you can decide to use `name` instead of the `lemma` as your `displayfield`.
            Alternatively, if a category system is organisized hierarchically, you can use the
            full path including ancestor lemma values by using `path` as `displayfield` value.
        </td>
      </tr>
      <tr>
        <td>caption</td>
        <td>The label displayed, for example, in select lists is determined by the displayfield options.
            You can output more complex labels by using <a href="../coreconcepts/keys">placeholder keys</a> in the caption value.
            For example, you can use the following caption value to display the path and the unit of a property:
            <code>"caption": "{path} ({unit})"</code>.
        </td>
      </tr>
      <tr>
        <td>level</td>
        <td>Optional, experimental. Not all properties in a hierarchical property tree denote the entities used in articles.
             Some can be used as categories to group its descendants.
             For example, literature titles could be divided into the categories "Sources", "Archives" and "Literature".
             You can use the `iscategory` field for each property to clarify the role.
             In addition, set the `level` key to the first tree level that does not only contain categories.
             This information may be used in the web interface to style properties by their level.</td>
      </tr>
      <tr>
        <td>alphasort</td>
        <td>Deprecated. Use the sort key instead.</td>
      </tr>
        <tr>
        <td>edge</td>
        <td>Optional. A reference is a subcategory using the `related_id` field to refer to a target property.
          This creates a relationship between the parent property and the reference target.
          You can use meta-properties to classify different types of relationships, for example,
          to capture families using father, mother, daughter, son etc. relationships.
          The `edge` configuration object is used to define a displayfield for the reference.
          Example to display the lemma of the reference:
          <code>"edge": { "displayfield": "lemma" }</code>
          Example to display the meta property of the reference:
          <code> "edge": { "displayfield": "property.path" } </code>
</td>
</tr>
      <tr>
        <td>role</td>
        <td>By default, guest users don't see any category systems in the faceted article search.
            Set the role to `search` to make it available. Alternatively, you can use `index` to indicate
            that the category system is not only to be used for faceted search, but for indexes in printed publications,
             if an export stylesheet implements this feature.</td>
      </tr>
      <tr>
        <td>sort</td>
        <td>Although the order is always fixed in the database, properties can be rearranged using
            mutate operations or in export pipelines. The `sort` value determines the field used
            for sorting in mutate operations.
        </td>
      </tr>
      <tr>
        <td>export</td>
        <td>Since the types configuration can be included in exports, you can store additional
            data to be used in export stylesheets. Examples:
          <ul>
            <li>group: Whether lemmas should be grouped by their initials in the index.</li>
            <li>snr: A number for sorting indexes.</li>
            <li>title: A heading for the index.</li>
          </ul>
       </td>
      </tr>
      <tr>
        <td>namespaces</td>
        <td>Namespaces used in `norm_data` fields. An object with the namespace prefix as key (e.g. <code>aat</code>) and a namespace configuration object as value.
             The namespace configuration object provides the base URL in the baseurl key (e.g. <code>{"baseurl": "http://vocab.getty.edu/page/aat/"}</code>).</td>
      </tr>
    </tbody>
  </table>
</figure>

# Available fields

Properties include both fields reserved for the [Relational Article Model (RAM)](/user/coreconcepts/model) and fields that can be customized.
While you can change the labels for all fields, you can only change the data types for customizable fields.
Refer to the `format` column in the table below to determine whether a field is reserved or customizable.

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Field Key</th>
        <th>Formats</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>lemma</td>
        <td>Customizable: text, xml. Max. 1,500 characters.</td>
        <td>The ususal property label.
            Although you can change the format, it is recommended to use plain text because the value is used
            widely in the user interface, for example, in select lists.
        </td>
      </tr>
      <tr>
        <td>name</td>
        <td>Customizable: text, xml. Max. 1,500 characters.</td>
        <td>Alternative field for property labels.
            For example, you can store long bibliographic references in the lemma field and short references in the name field.
            Although you can change the format, it is recommended to use plain text because the value is used
            widely in the user interface, for example, in select lists.
        </td>
      </tr>
      <tr>
        <td>sortkey</td>
        <td>Reserved: text. Max. 1,500 characters.</td>
        <td>A string used to sort properties with the mutate function or in export stylesheets.</td>
      </tr>
      <tr>
        <td>signature</td>
        <td>Reserved: text. Max. 1,500 characters.</td>
        <td>Usually an identifier within your project, for example the number of a brand.
            You can reinterpret the field by changing its label, but the type is fixed and cannot be changed.
        </td>
      </tr>
      <tr>
        <td>unit</td>
        <td>Customizable: text, xml, json. Max. 500 characters.</td>
        <td>Usually stores the unit of a value.
            Example: For properties such as measures, a unit such as "cm".
            You can customize the field and change the field format for other use cases.
        </td>
      </tr>
      <tr>
        <td>content</td>
        <td>Customizable: text, json xml. Max. 16,777,215 characters.</td>
        <td>Usually contains additional data for a property.
            You can customize the field and change the field format for other use cases.
            For example, you could store geolocations of places in JSON format.
        </td>
      </tr>
      <tr>
        <td>elements</td>
        <td>Customizable: text, json xml. Max. 16,777,215 characters.</td>
        <td>
            Usually contains descriptions about the composition of a property,
            i.e. if properties denote coat of arms.
            You can customize the field and change the field format for other use cases.
        </td>
      </tr>
      <tr>
        <td>source_from</td>
        <td>Customizable: text, json xml. Max. 65,535 characters.</td>
        <td>
            Usually contains information about the the data source
            Example: A literature reference with further details about the property.
            You can customize the field and change the field format for other use cases.
        </td>
      </tr>
      <tr>
        <td>comment</td>
        <td>Customizable: text, json xml. Max. 16,777,215 characters.</td>
        <td>Usually contains personal notes about a property.
            You can customize the field and change the field format for other use cases.
        </td>
      </tr>
      <tr>
        <td>keywords</td>
        <td>Customizable: text, json xml. Max. 1,500 characters.</td>
        <td>Usually contains a short comma-separated list of keywords.
            Although you can change the format, it is recommended to use plain text because the value is used
            for keyword filters in the user interface.
        </td>
      </tr>
      <tr>
        <td>file_name</td>
        <td>Reserved: filename. Max. 1,500 characters.</td>
        <td>A file name, for example, to attach an image to a property.</td>
      </tr>
      <tr>
        <td>parent_id</td>
        <td>Reserved: id</td>
        <td>Input field for the parent property.</td>
      </tr>
      <tr>
        <td>related_id</td>
        <td>Reserved: id</td>
        <td>Reference to another property, for example to identify relationships between persons.
            The type of reference can be categorized by the lemma or a meta-property (see the `properties_id` field).</td>
      </tr>
      <tr>
        <td>properties_id</td>
        <td>Reserved: id</td>
        <td>A meta-property for classifying a property. For example, used to assign a brand type to a brand.
            Can also be used to provide a relationship type (e.g. "mother of") of references (see the `related_id` field).</td>
      </tr>
      <tr>
        <td>iscategory</td>
        <td>Reserved: check</td>
        <td>Usually used to mark a property as structural element in the tree.
            You can reinterpret the field by changing its label, but the type is fixed and cannot be changed.
        </td>
      </tr>
      <tr>
        <td>ishidden</td>
        <td>Reserved: check</td>
        <td>
            Usually used to mark a property that should be excluded in published documents.
            You can reinterpret the field by changing its label, but the type is fixed and cannot be changed.
        </td>
      </tr>
      <tr>
        <td>published</td>
        <td>Reserved: select</td>
        <td>The publication state of the property. 0 = drafted, 1 = in progress, 2 = completed, 3 = published, 4 = searchable.
            You can reinterpret the field by changing its label, but the type is fixed and cannot be changed.
        </td>
      </tr>
      <tr>
        <td>norm_data</td>
        <td>Reserved: textM. ax. 65,535 characters.</td>
        <td>Authority data, each identifier on one line. You can use namespaces configured in the property type.</td>
      </tr>
      <tr>
        <td>norm_iri</td>
        <td>Reserved: text. Max. 1,500 characters.</td>
        <td>IRI fragment of the property.</td>
      </tr>
      <tr>
        <td>created</td>
        <td>Reserved: timestamp.</td>
        <td>Timestamp of row creation.</td>
      </tr>
      <tr>
        <td>modified</td>
        <td>Reserved: timestamp.</td>
        <td>Timestamp of the last modification.</td>
      </tr>
      <tr>
        <td>created_by</td>
        <td>Reserved: id.</td>
        <td>ID of the user who created the row.</td>
      </tr>
      <tr>
        <td>modified_by</td>
        <td>Reserved: id.</td>
        <td>ID of the user who last modified the row.</td>
      </tr>
      <tr>
        <td>id</td>
        <td>Reserved: id.</td>
        <td>ID of the row.</td>
      </tr>
    </tbody>
  </table>
</figure>


Properties implement some virtual fields not present in the database.
They are calculated on the fly and can be used in view modes or table columns:


<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Field Key</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>ancestors</td>
        <td>The list of ancestor nodes. Ususally used together with parent_id. Both can be configured with the same label "Parent lemma", ancestors is used in view mode and parent_id in edit mode to select a parent property.</td>
      </tr>
        <tr>
            <td>path</td>
            <td>The lemma path, including ancestors.</td>
        </tr>
        <tr>
            <td>iri</td>
            <td>The IRI path, consisting of table name, property type and IRI fragment. Usually, used together with norm_iri. While iri generates the full path, norm_iri is used in edit mode to generate an input field for the IRI fragment.</td>
        </tr>
        <tr>
            <td>image</td>
            <td>Displays the file of a property as image. Usually, used together with file_name. While image shows the image, file_name generates an input field in edit mode.</td>
        </tr>
        <tr>
            <td>articles_count</td>
            <td>Number of articles referring to the property.</td>
        </tr>
        <tr>
            <td>items_count</td>
            <td>Number of items referring to the property.</td>
        </tr>
        <tr>
            <td>links_count</td>
            <td>Number of links annotations referring to the property.</td>
        </tr>
    </tbody>
  </table>
</figure>


