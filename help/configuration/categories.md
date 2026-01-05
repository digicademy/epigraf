---
title: Categories
permalink: '/configuration/categories/'
---

# The composition of category systems

Categories are used in items and annotations to add structured data to articles.
Categories are stored in the properties table and each category system is configured as a separate property type.

There are four types of properties:

- Properties used in articles and annotations.
- Meta-properties used to categorize other properties in their `properties_id` field.
- Properties used as categories to structure the tree, they are flagged in the `iscategory` field.
- See-references establishing an edge between a source and a target property across the tree by using the `related_id` field.

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
     <tr>
        <td>columns</td>
        <td>Optional. A list of column configuration objects, keyed by column identifiers.
            Used for the table view. See the columns configuration documentation for further details.</td>
      </tr>
      <tr>
        <td>type</td>
        <td>A tree structure is used by default (value ´tree`), alternatively a flat list can be used (`flat`).</td>
      </tr>
      <tr>
        <td>displayfield</td>
        <td>There are two fields that can be used for category labels. The `lemma` field should be used by default
            and it should be set as displayfield, too.
            If a category systems is organisized hierarchically, instead, the full lemma `path` can be used as displayfield value.
            Sometimes you have short and long titles for a property. For example, for manageing literature,
            you store the full bibliographic reference in the name and a short title in the lemma.
            Then you can decide to use the `name` instead of the `lemma` field as your displayfield.</td>
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
        <td>Optional, experimental. All properties have a fixed position in the tree.
            In future versions, we may implement automatic ordering or validation of the tree structure.
            Set the alphasort key to `true` if you are sure that your properties should follow an alphabetic order.
            The default value is `false`.
        </td>
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

## Available fields

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
        <td>parent_id</td>
        <td>Input field for the parent property.</td>
      </tr>
      <tr>
        <td>lemma</td>
        <td>The ususal property label.</td>
      </tr>
      <tr>
        <td>name</td>
        <td>Alternative field for property labels. For example, you can store long bibliographic references in the lemma field and short references in the name field.</td>
      </tr>
      <tr>
        <td>sortkey</td>
        <td>A string used to sort properties with the mutate function or in export stylesheets.</td>
      </tr>
      <tr>
        <td>related_id</td>
        <td>Reference to another property, for example to identify relationships between persons.
            The type of reference can be categorized by the lemma or a meta-property (see the `properties_id` field).</td>
      </tr>
      <tr>
        <td>properties_id</td>
        <td>A meta-property for classifying a property. For example, used to assign a brand type to a brand.
            Can also be used to provide a relationship type (e.g. "mother of") of references (see the `related_id` field).</td>
      </tr>
      <tr>
        <td>unit</td>
        <td>Use the field for whatever you want. Example: For properties such as measures, a unit such as "cm". </td>
      </tr>
      <tr>
        <td>content</td>
        <td>Use the field for whatever you want. Example: Geolocation of places in JSON format.</td>
      </tr>
      <tr>
        <td>elements</td>
        <td>Use the field for whatever you want. Example: Describe the composition of a property, i.e. if properties denote coat of arms.</td>
      </tr>
      <tr>
        <td>source_from</td>
        <td>Use the field for whatever you want. Example: A literature reference with further details about the property.</td>
      </tr>
      <tr>
        <td>iscategory</td>
        <td>Use the field for whatever you want. Usually used to mark a property as structural element in the tree.</td>
      </tr>
      <tr>
        <td>ishidden</td>
        <td>Use the field for whatever you want. Usually used to mark a property that should be excluded in published documents.</td>
      </tr>
      <tr>
        <td>comment</td>
        <td>Use the field for whatever you want. Example: Your personal notes.</td>
      </tr>
      <tr>
        <td>signature</td>
        <td>Use the field for whatever you want. Usually an identifier within your project, for example the number of a brand.</td>
      </tr>
      <tr>
        <td>published</td>
        <td>Publication state of the property. 0 = drafted, 1 = in progress, 2 = completed, 3 = published, 4 = searchable.</td>
      </tr>
      <tr>
        <td>norm_data</td>
        <td>Authority data, each identifier on one line. You can use namespaces configured in the property type.</td>
      </tr>
      <tr>
        <td>norm_iri</td>
        <td>IRI fragment of the property.</td>
      </tr>
      <tr>
        <td>file_name</td>
        <td>A file name, for example, to attach an image to a property.</td>
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
        <td>The lemma path. Ususally used together with parent_id. Both can be configured with the same label "Parent lemma", ancestors is used in view mode and parent_id in edit mode to select a parent property.</td>
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
        <td>path</td>
        <td>The lemma path</td>
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


