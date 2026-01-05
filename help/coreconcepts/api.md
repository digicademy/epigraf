---
title: Core Concepts - API
permalink: '/coreconcepts/api/'
---

The entire Epigraf application is an Application Programming Interface (API). Epigraf allows data retrieval and modification by external programs via defined endpoints. In principle, Epigraf can also be used as a backend for independently hosted front ends. Data can be exchanged in a structured format (JSON). Further, rendered HTML components, such as the article list (without the layout), can be embedded in a front end via API access.

The endpoints correspond to the URLs that are visible when navigating the pages in the browser. To output **structured formats** via these endpoints instead of HTML web pages, append the desired format extension to the path:

- The **XML** format includes all content on a page and is structured hierarchically. For example, article elements contain all sections. It is also the output format for [exports via pipelines](/help?category=D.+Data+exchange+%2F+1.+Export).
- The **JSON** format has a hierarchical structure, but is generally more compact than XML and easier to process for other applications.
- The **CSV** format contains tabular data and is compatible with many statistical programs. Epigraf outputs entities from different hierarchical levels (e.g. articles and their sections) in rows below each other. The hierarchy of entities is stored in id columns.

You will find buttons for different formats at the bottom of the pages. Alternatively, the URL displayed in the browser can be modified so that the path ends with the file format (.xml, .json, .csv).

Example: The article list of a database can be accessed in the browser via [/epi/epi_public/articles](https://epigraf.inschriften.net/epi/epi_public/articles) as a webpage.

- The same list can be retrieved in JSON format via [/epi/epi_public/articles.json](https://epigraf.inschriften.net/epi/epi_public/articles.json).
- The data is served in XML format via [/epi/epi_public/articles.xml](https://epigraf.inschriften.net/epi/epi_public/articles.xml).
- A CSV table is generated via [/epi/epi_public/articles.csv](https://epigraf.inschriften.net/epi/epi_public/articles.csv).

Table pages such as the projects table or the articles table are served in a **Hydra-compatible** format, enabling harvesting of collections. If the appropriate [configuration](../configuration/triples) is set up, articles can be output in RDF-compatible **triple formats**.

- **JSON-LD** (jsonld extension)
- **Turtle** (ttl extension)
- **RDF/XML** (rdf extension)

In addition to retrieving structured data, the following **rendered views** can be generated:

- **HTML** (without extension): Interactive webpage to be displayed in browsers. To embedd components into another webpage, the show parameter can be used to select specific elements. The theme parameter determines the CSS styling.
- **Markdown** (md extension): Presentation of article content in rendered markdown.

All endpoints return UTF-8 encoded data. Non-printing characters such as the unit separator control character are filtered out.

# Access to endpoints

An access token is required to access non-public data and for write access. This token is appended to the URL or sent as a bearer token, for example: `/epi/epi_all/articles.csv?token=ABCDEFG`. The token is generated in Epigraf's [user account](/users).

To use the access token, [API permissions](../administration) must be granted both for accessing the relevant database and for accessing the endpoint. For example, to use the articles/index endpoint with a token, the following permissions are required:

- User: \<username\>
- Role: *remains empty*
- Requested by: "api"
- Permission type = "access"
- Entity Type: "databank"
- Entity name: \<databank name\>
- Entity ID: *can remain empty, optionally the ID of the databank*
- Permission name = "epi/articles/index"

# General parameters of all endpoints

## Common parameters

In all endpoints, the path parameter `<db>` must be replaced by the selected project database, for example by "epi_public".

For index and view endpoints, the optional `idents` query parameter determines how IDs are composed in the ID fields:

<ul>
<li><em>id</em>: By default, the IDs are generated from the table name and entity ID, for example <code>articles-551</code>.</li>
<li><em>tmp</em>: Temporary IDs are created. When used in subsequent imports, new entities will be created for temporary IDs. Example: <code>articles-tmp551</code>.</li>
<li><em>iri</em>: Replace IDs by IRI paths, for example <code>articles/epi-article/dresden~551</code>.</li>
</ul>

## Pagination and order

Each query returns as many entities as requested in the `limit` parameter. Additional entities can be retrieved using the `page` parameter.

<figure class="table">
<table>
<thead>
<tr>
<th>Query Parameter</th>
<th>Explanation</th>
</tr>
</thead>
<tbody>
<tr>
<td>limit</td>
<td>Optional. Default: Number of entities per page. The default varies depending on the endpoint; typically, 100 entities are returned.</td>
</tr>
<tr>
<td>page</td>
<td>Optional. Default: 1. Page number.</td>
</tr>
<tr>
<td>total</td>
<td>Optional. Maximum number of entities returned (even if more are available)</td>
</tr>
<tr>
<td>sort</td>
<td>Optional. Specifies the field by which the data is sorted. The first entities are returned; further entities can be retrieved by pagination. Multiple fields are separated by commas.</td>
</tr>
<tr>
<td>direction</td>
<td>Optional. In conjunction with the sort parameter, defines the sort direction.
<ul><li><em>asc</em>: Ascending order</li>
<li><em>desc</em>: Descending order</li>
</ul>
If sorting is performed on multiple fields, the sort order for each field can be defined in a comma-separated list.</td>
</tr>
<tr>
<td>id</td>
<td>Optional: Restricts the result to a single entity.</td>
</tr>
</tbody>
</table>
</figure>

Some endpoints support cursor-based pagination. With cursor-based pagination, a reference entity is defined as a reference point for the result. Cursor-based pagination can be used to narrow down a category tree (`epi/<db>/properties/index`) to specific segments. The `seek` parameter can be used to jump to specific nodes within the result list. For the properties/index endpoint, the entity hierarchy is expanded: ancestors of the filtered entities are always included in the result. In addition, when seeking a specific entity, the previous entities in the result set are returned.

<figure class="table">
<table>
<thead>
<tr>
<th>Query Parameter</th>
<th>Explanation</th>
</tr>
</thead>
<tbody>
<tr>
<td>cursor</td>
<td>ID of a reference entity. The entity that follow in the sorted result set are returned. For the <code>properties/index</code> endpoint, the sorting is fixed to the tree structure.</td>
</tr>
<tr>
<td>direction</td>
<td>Sorting direction: ascending (default value <em>asc)</em> or descending (<em>desc)</em>.</td>
</tr>
<tr>
<td>limit</td>
<td>Number of entities returned by a query. In the <code>properties/index</code> endpoint, the number of entities may be larger, as ancestor nodes are always returned.</td>
</tr>
<tr>
<td>children</td>
<td>In combination with <code>cursor</code>, the <code>properties/index</code> endpoint determines whether the returned entities start at the next sibling node (default value <em>0</em>) or at the next child node (value <em>1</em>). This parameter is only taken into account for ascending sort order (direction=<em>asc</em>).</td>
</tr>
<tr>
<td>collapsed</td>
<td><p>In combination with <code>cursor</code>, the <code>properties/index</code>-endpoint determines whether the result set should be restricted to nodes on the same level (value <em>1</em>) or whether all descendants should be returned (default value <em>0</em>).</p>
This parameter depends on the <code>children</code> and <code>cursor</code> parameters:  if child nodes are requested, the result set is restricted to the child node level. Otherwise, the result set is restricted to the cursor node level. If no cursor parameter is provided, the result set is restricted to the first level.</td>
</tr>
<tr>
<td>seek</td>
<td><p>ID of a reference entity. Unlike the <code>cursor</code> parameter, the number of entities does not refer directly to the reference entity. Instead, additional preceding (sort direction <em>desc</em>) or following (sort direction <em>asc</em>) entities are returned, with the number depending on the <code>limit</code> parameter. This allows to query the previous and next nodes of an entity.</p>
This parameter excludes using the parameters <code>cursor</code>, <code>collapsed</code> and <code>children</code>.</td>
</tr>
</tbody>
</table>
</figure>

## Fields, columns and blocks

The **index endpoints** support selecting columns to be returned by using the `columns` parameter. Multiple columns are selected as a comma-separated list.

- If the columns parameter is empty, the default fields will be returned.
- Additional columns are configured in the [types configuration](../configuration/articles) by the `columns` key.
- Registered users can also request ad hoc columns using extraction keys. An ad hoc column consists of the caption, followed by an equal sign and the extraction key. The parameter must be URL-encoded because it contains an equal sign.

The extraction key can adress simple fields of the article object (e.g. `title`) or nested fields using dot notation (e.g. `project.shortname`). This follows the conventions of the types configuration. To access lists, an asterisk placeholder can be used (e.g. `items.{\*}.value`). Lists can be filtered with conditions in square brackets (e.g. `items.{\*}[itemtype=conditions].date`). For further details, see the [CakePHP documentation on hash syntax](https://book.cakephp.org/4/en/core-libraries/hash.html#Cake\Utility\Hash::extract).

By default, all ID fields contain IRI paths. To request IDs, use the idents parameter.

For HTML output, specific components can be explicitly requested using the `show` parameter. This allows, for example, the main content to be embedded in external pages without sidebars. The parameter supports a comma-separated list of the following elements:

- mainmenu: The main menu.
- searchbar: The search bar in table views.
- content: The main content area.
- footer: The footer area.
- leftsidebar: The left sidebar.
- rightsidebar: The right sidebar.

Styling of the rendered view depends on the `theme` parameter. The `minimal` theme creates a compact view with reduced outer margins, which is particularly useful when embedding content in iframes.

The `flow` parameter determines the click target of entities in a collection (e.g. table views). The options include `frame`, `popup` or `tab`.

## Available endpoints

All endpoints are structured according to the same pattern. There are two different endpoint routes:

- **Global endpoints** access application level actions. They consist of a controller, which usually corresponds to a database table, the action to be executed and, if necessary, additional parameters: `/<controller>/<action>?<parameter>`
- **Project endpoints** additionally contain the path segment `epi` and a database name: `<db>`:`/epi/<db>/<controller>/<action>?<parameter>`

To find out the available endpoints, simply browse Epigraf in a webbrowser and pay attention to the adress bar.
Each URL is an endpoint, consisting of a controller path (e.g. projects, articles, properties) and an action path.
Typically, a controller provides the following actions

- index
- view
- show
- edit
- add
- delete
- import
- transfer
- mutate

# List of endpoints

> Note: The following documentation is not complete yet. But most endpoints can be inferred from the URLs in the browser address bar.

## Projects

### GET /epi/\<db\>/projects/index

A list of all projects. Example: `/epi/epi_all/projects?term=greifswald`.

<figure class="table">
<table>
<thead>
<tr>
<th>Query Parameter</th>
<th>Explanation</th>
</tr>
</thead>
<tbody>
<tr>
<td>term</td>
<td>Optional. Only projects whose name, description or IRI contain the search term are returned.</td>
</tr>
<tr>
<td>projecttypes</td>
<td>Optional. A comma-separated list, restricts the result to projects with the specific types.</td>
</tr>
</tbody>
</table>
</figure>

## Articles

### GET /epi/\<db\>/articles/index

A paginated list of articles. Identical to the short form `GET /epi/<db>/articles`.

<figure class="table">
<table>
<thead>
<tr>
<th>Query Parameter</th>
<th>Description</th>
</tr>
</thead>
<tbody>
<tr>
<td>term</td>
<td>Optional. In conjunction with the field parameter, restricts the articles to articles matching the search term.</td>
</tr>
<tr>
<td>field</td>
<td>Optional. In conjunction with the term parameter, specifies which fields to search.
<ul><li>captions: Search by article number and title.</li>
<li>articlenumber:  Search by article number.</li>
<li>title: Search by article title.</li>
<li>status: Search by the status field of the article.</li>
<li>text: Searches the full text index of articles. The searchable full text must be indexed, which means that items of type `search` must be created that contain the indexed text in their content field.</li>
</ul></td>
</tr>
<tr>
<td>articletypes</td>
<td>Optional. Restricts the results by article types. A comma-separated list of article types as defined in the configuration. </td>
</tr>
<tr>
<td>projects</td>
<td>Optional. Restricts the results to articles belonging to the given projects. A comma-separated list of project IDs.</td>
</tr>
<tr>
<td>properties.&lt;propertytype&gt;.selected</td>
<td>Optional. Restricts the results to articles with the given categories assigned via links or items. A comma-separated list of category IDs. The `&lt;propertytype&gt;` placeholder has to be replaced by the name of the property configuration. Example: `/epi/epi_public/articles.json?properties.objecttypes.selected=975`</td>
</tr>
<tr>
<td>properties.&lt;propertytype&gt;.flags</td>
<td>Optional. A comma-separated list of options related to the category filter. Available options:
<ul><li>all: Select all categories.</li>
<li>des: Select descendants, i.e. sub categories.</li>
<li>inv: Reverse the selection.</li>
<li>grp: Outputs grouped or enriched results. This generates pie charts in maps, groups the tile view into lanes, and displays annotated text passages below the articles in the table.</li>
</ul></td>
</tr>
<tr>
<td>lat</td>
<td>Optional. Used in combination with the parameters lng and zoom.
When a map is displayed, it is centered on the given point (at and lng parameters) and zoom level (zoom parameter).
In addition, items can be sorted according to their distance from this point by setting the sort parameter to `distance`.</td>
</tr>
<tr>
<td>lng</td>
<td>Optional. Used in combination with the parameters lat and zoom.
</td>
</tr>
<tr>
<td>zoom</td>
<td>Optional. Used in combination with the parameters lat and lng.</td>
</tr>
<tr>
<td>tile</td>
<td>Optional. Returns only articles containing coordinates in the given tile. Tiles are formed according to the pattern &lt;zoom&gt;/&lt;x&gt;/&lt;y&gt;, see <a href="https://wiki.openstreetmap.org/wiki/Slippy_map_tilenames.">https://wiki.openstreetmap.org/wiki/Slippy_map_tilenames.</a> The slash is URL-encoded as %2F. Example: `/epi/epi_public/articles.json?tile=11%2F676%2F1066`</td>
</tr>
<tr>
<td>quality</td>
<td>Optional. Used in conjunction with the `tiles` parameter. When querying geocoded data, the quality level or validity can be restricted:
<ul>
<li>0 = Unchecked: All coordinates are taken into account.</li>
<li>1 = Uncertain: The coordinates must at least have been automatically checked for plausibility.</li>
<li>2 = Verified: Only manually verified coordinates are taken into account.</li>
</ul>
Depending on the accuracy of the data, points may be approximate and only represent the area most likely to be related to an article.</td>
</tr>
<tr>
<td>template</td>
<td>Optional. Layout of the HTML output.
<ul>
<li>table: The basic tabular template.</li>
<li>map: A map with geocoded articles.</li>
<li>tiles: A tile view with article previews.</li>
<li>lanes: Lanes grouping articles by categories.</li>
<li>coding: A view optimized for quick editing. In the sidebar, articles are always opened in edit mode.</li>
</ul></td>
</tr>
<tr>
<td>columns</td>
<td>Optional. Comma-separated list of fields to be displayed in the table.
<ul><li>By default the fields articlenumber, title and norm_iri are displayed.</li>
<li>Available fields are configured in the columns key of article types.</li>
<li>Authenticated users can define ad hoc fields. An ad hoc field consists of a column name followed by an equal sign and an extraction key. The field must be URL-encoded because it contains an equal sign.
</li></ul>
Extraction keys are article fields (e.g., `title`) or nested fields with dot notation (e.g., `project.shortname`). To access lists, an asterisk placeholder can be used (e.g., `items.{*}.value`). Lists can be filtered with conditions in square brackets (e.g., <code>items.{*}[itemtype=conditions].date</code>). For further details, see the <a href="https://book.cakephp.org/4/en/core-libraries/hash.html#Cake\Utility\Hash::extract">CakePHP hash syntax documentation</a>.</td>
</tr>
<tr>
<td>published</td>
<td>Optional. Only articles that meet or exceed the given publication level will be returned:
<ul><li>0 = Drafted</li>
<li>1 = In progress</li>
<li>2 = Completed</li>
<li>3 = Published</li>
<li>4 = Searchable</li>
</ul></td>
</tr>
<tr>
<td>snippets</td>
<td>Optional. A comma-separated list of snippets to be included in the result.
A snippet is a predefined compilation of content:
<ul><li><em>paths</em>: The result contains detailed information on the
hierarchical structure of sections and lemmas.</li>
<li><em>search</em>: Return the full text index content.</li>
<li><em>indexes</em>:  Category indexes are generated.</li>
<li><em>iris</em>: Return IRIs.</li>
<li><em>published</em>: Include the publication status field.</li>
<li><em>tags</em>: Extract tags from XML fields.</li>
</ul></td>
</tr>
<tr>
<td>details</td>
<td>Optional (experimental). A comma-separated list of extraction keys. The extracted content is displayed in the table view as search results. Used to show segments annotated by categories. Example: <code>/articles/index?details=items.*.tags.*</code>.</td>
</tr>
<tr>
<td>idents</td>
<td>Optional. Determines how IDs are composed in the ID fields:
<ul><li><em>id</em>: By default, the IDs are generated from the table name and entity ID, for example <code>articles-551</code>.</li>
<li><em>tmp</em>: Temporary IDs are created. When used in subsequent imports, new entities will be created for temporary IDs. Example: <code>articles-tmp551</code>.</li>
<li><em>iri</em>: Replace IDs by IRI paths, for example <code>articles/epi-article/dresden~551</code>.</li>
</ul></td>
</tr>
</tbody>
</table>
</figure>

### POST /epi/\<db\>/articles/import

Import articles including all associated data. The data structure must correspond to the Relational Article Model. Three variants are supported: 1. Provide a file to be uploaded and imported. 2. Provide the name of a file available on the server. 3. Provide JSON data.

Example: `/epi/epi_movies/articles/import?filename=newmovies.csv`

<figure class="table">
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Explanation</th>
</tr>
</thead>
<tbody>
<tr>
<td>filename</td>
<td><p>When importing files or folders already present on the server: file name or folder name on the server, relative to the import folder of the database.</p>
The files must contain XML or CSV. Folders can contain multiple XML files.</td>
</tr>
<tr>
<td>file</td>
<td>When uploading a file: The file content following the Relational Article Model in CSV or XML format.</td>
</tr>
<tr>
<td>data</td>
<td>When posting JSON data: The data array according to the Relational Article Model.</td>
</tr>
<tr>
<td>pipeline_id</td>
<td>In case the data should be transformed using a pipeline, provide the pipeline ID. Only available in combination with the filename parameter.</td>
</tr>
<tr>
<td>tree</td>
<td>By default (value '1'), the tree structure of sections is regenerated after each import step. Disable with '0'.</td>
</tr>
<tr>
<td>solved</td>
<td>Set to '1' to return a mapping between input IDs and resulting database IDs. Default is '0'. Handle with care, the result ist stored in the job's result field in the database and may grow large.</td>
</tr>
</tbody>
</table>
</figure>

### DELETE /epi/\<db\>/articles/delete/<id>

Delete an article, including its sections, items, footnotes and links.

<figure class="table">
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Explanation</th>
</tr>
</thead>
<tbody>
<tr>
<td>&lt;id&gt;</td>
<td>Numeric article ID (e.g. 1).</td>
</tr>
</tbody>
</table>
</figure>

## Sections

### POST /epi/\<db\>/sections/move/\<id\>

Move sections within an article after or before a reference section.

<figure class="table">
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Explanation</th>
</tr>
</thead>
<tbody>
<tr>
<td>&lt;id&gt;</td>
<td>ID of the section to move.</td>
</tr>
<tr>
<td>reference_id</td>
<td>ID of a reference section.</td>
</tr>
<tr>
<td>reference_pos</td>
<td>Desired relation of the reference section to the moved section: <code>parent</code> or <code>preceding</code>.</td>
</tr>
</tbody>
</table>
</figure>

## Properties

### GET /epi/\<db\>/properties/\<propertytype\>/index

List of categories of a category type.

<figure class="table">
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Explanation</th>
</tr>
</thead>
<tbody>
<tr>
<td>&lt;propertytype&gt;</td>
<td>Name of the category system.</td>
</tr>
</tbody>
</table>
</figure>

### GET /epi/\<db\>/properties/merge


A GET request results in a preview of how the target category would look after merging with source categories.

The endpoint can be used with query or with path parameters.
Example: `GET /epi/<db>/properties/merge/<source>/<target>`.

<figure class="table">
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Explanation</th>
</tr>
</thead>
<tbody>
<tr>
<td>source</td>
<td>A comma separated list of IDs determining the source categories to be merged and deleted.</td>
</tr>
<tr>
<td>target</td>
<td>ID of the target category into which source categories will be merged.</td>
</tr>
<tr>
<td>concat</td>
<td>
If set to '1', content from text fields (lemma, sortkey) is concatenated, if the source entities differ.By default ('0') only the references are rewired.</td>
</tr>
</tbody>
</table>
</figure>

### POST /epi/\<db\>/properties/merge

Merges multiple categories by transferring the contents of one or more source categories to a target category. If the `concat` parameter is set to '1', contents from text fields is merged. All references to the source categories (e.g. from articles) are rewired to the target category. The source categories are then deleted.


Example:
`POST /epi/epi_all/properties/merge?source=2,3,4&target=1`

The endpoint can alternatively be used with path parameters:
`POST /epi/<db>/properties/merge/<source>/<target>`

If the tree structure is affected by the merge, a follow-up POST request should be executed on the endpoint `/epi/<db>/properties/mutate/<propertytype>` to recover the tree by a sort operation on the lft field.

<figure class="table">
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Explanation</th>
</tr>
</thead>
<tbody>
<tr>
<td>source</td>
<td>A comma separated list of IDs determining the source categories to be merged and deleted.</td>
</tr>
<tr>
<td>target</td>
<td>ID of the target category into which source categories will be merged.</td>
</tr>
<tr>
<td>concat</td>
<td>
If set to '1', content from text fields (lemma, sortkey) is concatenated, if the source entities differ.By default ('0') only the references are rewired.</td>
</tr>
</tbody>
</table>
</figure>

### POST /epi/\<db\>/properties/mutate/\<propertytype\>

The mutate endpoint can be used to perform batch processing tasks.

For categories, the sort task can be used  to reorder the category tree. It is further used to recover the tree structure in case it gets invalid. Tree structures are invalid if the lft/rght values are no longer correct, for example after import processes or after merging categories.

<figure class="table">
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Explanation</th>
</tr>
</thead>
<tbody>
<tr>
<td>task</td>
<td>The task:
<ul>
<li>`sort`: Reorder or recover the tree.</li>
</ul>
</td>
</tr>
<tr>
<td>&lt;propertytype&gt;</td>
<td>The property type, for example `fonttypes`.</td>
</tr>
</tbody>
</table>
</figure>

<figure class="table">
<table>
<thead>
<tr>
<th>Payload</th>
<th>Explanation</th>
</tr>
</thead>
<tbody>
<tr>
<td>config.params.sortby</td>
<td>Task option are provided in the payload of the POST request:
<ul><li><code>lemma</code> Sort by lemma field.</li>
<li><code>sortkey</code> Sort by sortkey field.</li>
<li><code>lft</code> Order by existing lft values to repair the tree. All lft and rght values are regenerated.</li>
<li><code>sortno</code> Order by sort numbers used by EpiDesktop to repair the tree. All lft/rght values are regenerated.</li>
</ul></td>
</tr>
</tbody>
</table>
</figure>

## Types

### GET /epi/\<db\>/types/\<scope\>/index

The types endpoint returns the database configuration.
Each type defines what kind of projects, articles, sections, items, annotations (links and footnotes) and categories occur in a database.

<figure class="table">
<table>
<thead>
<tr>
<th>Parameter</th>
<th>Explanation</th>
</tr>
</thead>
<tbody>
<tr>
<td>scope</td>
<td>Optional. Limit the returned configuration entities. A comma separated list containinge one or more of `projects`, `articles`, `sections`, `items`, `links`, `footnotes` or `properties`.</td>
</tr>
<tr>
<td>term</td>
<td>Optional. Filter the result by entities contain the search term in their name, caption, description or IRI.</td>
</tr>
</tbody>
</table>
</figure>
