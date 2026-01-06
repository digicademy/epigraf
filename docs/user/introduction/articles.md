---
title: Introduction to Articles
permalink: '/epigraf/user/introduction/articles/'
---

Articles are the starting point for data collection and analysis with Epigraf.
The definition of an article depends on the use case.
In epigraphy, for example, an article contains a description of an object and its inscriptions.
In the case of a letter edition, each letter constitutes an article.
For social media analysis, it makes sense to think of an article as a post or a thread consisting of a post and its associated comments.
Thus, an article contains a description of the research object.

![Epigraf Editing](/epigraf/user/assets/img/articles_composition.png)

Each article is made up of sections that can be flexibly combined.
Sections contain the text and all relevant metadata and associated files in their items.
Items include text, images and references to categories.
In text fields, annotations can be used to format text and to text segments
to categories. Footnotes are a special type of annotation containing comments on a text segment.

Articles and all associated data can either be [imported](/epigraf/user/import) from external files,
transferred from other databases, or created and edited directly in the user interface.

# Navigating articles

## Text search

The search field above the article list searches identifiers, the articles' title and status fields, and the fulltext index.
Select the scope in the dropdown menu next to the search input.

Multiple search terms are separated by space. By default, the result contains all articles that match each of the terms (boolean AND condition). Use a vertical bar (the pipe `|`) to find all articles that match at least one of the terms (boolean OR condition).
If you combine these two patterns, the OR conditions are evaluated first, followed by the AND conditions (AND binds stronger).

Example: The search pattern "gratia plena | gracia plena" finds all articles that contain both *gratia* and *plena* (first AND condition) or both *gracia* and *plena* (second AND condition).

To search text containing whitespace, use quotation marks around the search term.

## Filter options

In the left sidebar, the list of articles can be filtered by project, article type, or publication status.
You can combine it with the text search or facet search.

## Facets

The plus button in the left sidebar adds a category system as a facet.
Facets only contain the categories that match the other search and filter criteria.
Therefore, facets play a double role: They help to find specific articles and at the same time
they provide an overview about the categories prevalent in selected articles. This feature is particularly
handy when multiple category systems are used as facets. As an example, you could select all thumb stones in one facet
and then inspect the font types of the thumb stones in a second facet.

Next to each category you see the number of articles that match the category. Note that multiple categories may appear in an article.  Therefore, the number of results in the article list does not always correspond to the sum in the category tree.

Category systems may grow large and contain, for example, thousands of persons.
Below each facet you find an input field to search categories.

![Epigraf Research](/epigraf/user/assets/img/articles_facets.png)

For the visible facet, you can select further options:
- *Select all* results in all articles that have any of the categories. In this case, selecting single categories as well the *Include descendants* checkbox have no effect.
- *Invert selection* results in all articles not matching the selected categories. If no category is selected, the result list will always be empty. In combination with *Select all* this results in all articles that have none of the categories.
- *Include descendants* extends the selection to all child categories of checked categories.

Examples:

- Show all articles that refer to the material *Wood*: Activate the *Wood* checkbox.
- Show all articles that refer to the material *Wood* or any of its child categories: Activate the checkbox *Wood*; activate the checkbox *Include descendants*.
- Show all articles that refer to any material: Activate the *Select all* checkbox.
- Show all items that do not refer to any material: Select the *Select all* checkbox and select the *Invert selection* checkbox.
- Show all items that do not refer to the material *Wood*: Activate the *Wood* checkbox and activate the *Invert selection* checkbox.
- Show all articles that do not refer to the material *Wood* or any of its children: Select the checkbox *Wood*, select the checkbox *Include descendants* and select the checkbox *Invert selection*.

# Edit articles

See the following images for an overview about the basic editing features.

![Epigraf Articles](/epigraf/user/assets/img/articles_editing.png)

Text is annotated using a toolbar or corresponding keyboard shortcuts.
Based on the database configuration, annotations can be linked to categories.

![Epigraf Articles](/epigraf/user/assets/img/annotation.png)


# Date values

Dating fields are used to store natural language dating of objects in a structured manner.

Dates must be entered according to defined patterns, see the examples below.
When entering a date, Epigraf parses and normalises upper and lower case, spaces and selected abbreviations.
Dates can appear as simple dates (e.g. 1520) or as ranges (e.g. 1520-1522).
Multiple dates are separated by a comma (e.g. 1520, 1522) or by "or" (e.g. 1524 or 1525).

From the value, a range consisting of start and end year is derived from the most recent and the oldest single date.
The range is used for search functions.

Further, a sort key are generated from the oldest date found in the value.
The following rules apply to the sort key:

1.  More precise dates come before less precise dates
2.  Older dates come before more recent dates

When entering fuzzy dates, the order cannot always be clearly determined by the two rules.
In these cases the following list can be used as a guide.
*Please note*: The table shows sortkeys generated by version 4 of the dating algorithm.
We refined the algorithm, the table will be updated soon.

<figure class="table">
    <table>
        <thead>
        <tr>
            <th>Order</th>
            <th>Dating</th>
            <th>Normalised dating</th>
            <th>Start year</th>
            <th>End year</th>
            <th>Sortkey</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>1</td>
            <td>3 v. Chr.</td>
            <td>3 v.Chr.</td>
            <td>-3</td>
            <td>-3</td>
            <td>-0003AAA00000000A4</td>
        </tr>
        <tr>
            <td>2</td>
            <td>1200 v. Chr. - 1. Jh. v.Chr.</td>
            <td>1200 v.Chr.–1.Jh. v.Chr.</td>
            <td>-1200</td>
            <td>-1</td>
            <td>-0100BBE-8800AABA4</td>
        </tr>
        <tr>
            <td>3</td>
            <td>3 n. Chr.</td>
            <td>3</td>
            <td>3</td>
            <td>3</td>
            <td>00003AAA00000000A4</td>
        </tr>
        <tr>
            <td>4</td>
            <td>800 v. Chr. - 1. Jh.</td>
            <td>800 v.Chr.–1.Jh.</td>
            <td>-800</td>
            <td>100</td>
            <td>00100BBE-9200AABA4</td>
        </tr>
        <tr>
            <td>5</td>
            <td>1200</td>
            <td>1200</td>
            <td>1200</td>
            <td>1200</td>
            <td>01200AAA00000000A4</td>
        </tr>
        <tr>
            <td>6</td>
            <td>vor 1200</td>
            <td>vor 1200</td>
            <td>1175</td>
            <td>1200</td>
            <td>01200AAB00000000A4</td>
        </tr>
        <tr>
            <td>7</td>
            <td>um 1200</td>
            <td>um 1200</td>
            <td>1190</td>
            <td>1210</td>
            <td>01200AAD00000000A4</td>
        </tr>
        <tr>
            <td>8</td>
            <td>nach 1200</td>
            <td>nach 1200</td>
            <td>1200</td>
            <td>1225</td>
            <td>01200AAF00000000A4</td>
        </tr>
        <tr>
            <td>9</td>
            <td>Anfang 16. Jh</td>
            <td>A.16.Jh.</td>
            <td>1501</td>
            <td>1524</td>
            <td>01501ABF00000000A4</td>
        </tr>
        <tr>
            <td>10</td>
            <td>1410 - Anfang 16. Jh</td>
            <td>1410–A.16.Jh.</td>
            <td>1410</td>
            <td>1525</td>
            <td>01501BBF08590AABA4</td>
        </tr>
        <tr>
            <td>11</td>
            <td>1520</td>
            <td>1520</td>
            <td>1520</td>
            <td>1520</td>
            <td>01520AAA00000000A4</td>
        </tr>
        <tr>
            <td>12</td>
            <td>1520 ?</td>
            <td>1520?</td>
            <td>1520</td>
            <td>1520</td>
            <td>01520AAA00000000B4</td>
        </tr>
        <tr>
            <td>13</td>
            <td>1520 - 22</td>
            <td>1520–1522</td>
            <td>1520</td>
            <td>1522</td>
            <td>01522BAA08480AABA4</td>
        </tr>
        <tr>
            <td>13</td>
            <td>1520 - 1522</td>
            <td>1520–1522</td>
            <td>1520</td>
            <td>1522</td>
            <td>01522BAA08480AABA4</td>
        </tr>
        <tr>
            <td>14</td>
            <td>1525 oder 1524</td>
            <td>1525 oder 1524</td>
            <td>1524</td>
            <td>1525</td>
            <td>01524BAA08475AAAA4</td>
        </tr>
        <tr>
            <td>15</td>
            <td>1525</td>
            <td>1525</td>
            <td>1525</td>
            <td>1525</td>
            <td>01525AAA00000000A4</td>
        </tr>
        <tr>
            <td>16</td>
            <td>1.V. 16. Jh</td>
            <td>1.V.16.Jh.</td>
            <td>1501</td>
            <td>1525</td>
            <td>01525ABA00000000A4</td>
        </tr>
        <tr>
            <td>18</td>
            <td>1.D. 16. Jh</td>
            <td>1.D.16.Jh.</td>
            <td>1501</td>
            <td>1533</td>
            <td>01533ABA00000000A4</td>
        </tr>
        <tr>
            <td>19</td>
            <td>2.V. 16. Jh</td>
            <td>2.V.16.Jh.</td>
            <td>1525</td>
            <td>1550</td>
            <td>01550ABA00000000A4</td>
        </tr>
        <tr>
            <td>20</td>
            <td>1.H. 16. Jh</td>
            <td>1.H.16.Jh.</td>
            <td>1501</td>
            <td>1550</td>
            <td>01550ABB00000000A4</td>
        </tr>
        <tr>
            <td>21</td>
            <td>Mitte 16. Jh</td>
            <td>M.16.Jh.</td>
            <td>1525</td>
            <td>1575</td>
            <td>01550ABC00000000A4</td>
        </tr>
        <tr>
            <td>22</td>
            <td>2.D. 16. Jh</td>
            <td>2.D.16.Jh.</td>
            <td>1533</td>
            <td>1566</td>
            <td>01566ABA00000000A4</td>
        </tr>
        <tr>
            <td>23</td>
            <td>3.V. 16. Jh</td>
            <td>3.V.16.Jh.</td>
            <td>1550</td>
            <td>1575</td>
            <td>01575ABA00000000A4</td>
        </tr>
        <tr>
            <td>24</td>
            <td>Ende 16. Jh</td>
            <td>E.16.Jh.</td>
            <td>1575</td>
            <td>1600</td>
            <td>01600ABA00000000A4</td>
        </tr>
        <tr>
            <td>25</td>
            <td>4.V. 16. Jh</td>
            <td>4.V.16.Jh.</td>
            <td>1575</td>
            <td>1600</td>
            <td>01600ABB00000000A4</td>
        </tr>
        <tr>
            <td>26</td>
            <td>3.D. 16. Jh</td>
            <td>3.D.16.Jh.</td>
            <td>1566</td>
            <td>1600</td>
            <td>01600ABC00000000A4</td>
        </tr>
        <tr>
            <td>27</td>
            <td>2.H. 16. Jh</td>
            <td>2.H.16.Jh.</td>
            <td>1550</td>
            <td>1600</td>
            <td>01600ABD00000000A4</td>
        </tr>
        <tr>
            <td>28</td>
            <td>16. Jh</td>
            <td>16.Jh.</td>
            <td>1501</td>
            <td>1600</td>
            <td>01600ABE00000000A4</td>
        </tr>
        <tr>
            <td>29</td>
            <td>15.-16. Jh</td>
            <td>15.–16.Jh.</td>
            <td>1401</td>
            <td>1600</td>
            <td>01600BBE08500BEBA4</td>
        </tr>
        <tr>
            <td>30</td>
            <td>Anfang 17. Jh</td>
            <td>A.17.Jh.</td>
            <td>1601</td>
            <td>1625</td>
            <td>01601ABF00000000A4</td>
        </tr>
        </tbody>
    </table>
</figure>

# Geographic coordinates

Items and properties containing geographic coordinates can be displayed on a map.
You can either store coordinates directly in an article or choose a property with coordinates, or combine both options.
The fields must be configured as geodata fields by an administrator.

For example, you can use a category system to manage places and locations.
If you geolocate a place in the categories, you can refer to the category in an article item.
But what if the place is a complex building consisting of several parts, such as a monastery,
and the coordinates are not specific enough? Additionally, to locate the specific position within the building complex,
you may refine or overwrite the coordinates directly in a geodata field of an article item.

![Epigraf geolocation editing](/epigraf/user/assets/img/geolocations_edit.png)

Once configured, you use the map view to change the coordinates.
The **location marker** can be moved to the appropriate position on the map using drag and drop with the mouse, and the corresponding **latitude and longitude values** are determined automatically. Alternatively, known latitude and longitude values can be entered directly into the respective fields. The size of the location marker can be adjusted using the **radius field**. Note: The radius of the markers is only visible if the radius view has been activated beforehand using the button on the left side of the map.

The **search field** can be used to search for locations or buildings. In category systems, the search field is pre-filled with the category lemma. Press Enter to start the search. If a matching result is selected from the search suggestions, it is marked on the map with a blue box. As soon as you confirm the box by clicking on it, the marker moves to the corresponding position and the values for latitude, longitude, and radius are automatically adjusted.

The **publication status** of the item or category is used to indicate the reliability of coordinates:
- 0 = drafted = unvalidated coordinates.
- 1 = in progress = assumed coordinates, for example, from an automatic geocoding service
- 2 = complete = validated coordinates, either manually entered or derived from other reliable data such as Geonames
- 3 = published = validated coordinates that should be displayed on public maps
- 4 = searchable = validated coordinates that should be displayed on public maps

Note: By choosing the publication status, the whole item or category is set to this status. In consequence, the property or item
will be published or unpublished when it is transferred to a public database.

![Epigraf map](/epigraf/user/assets/img/geolocations_map.png)

# Special characters

You can use the standard ways of your operating system to enter special characters.
Windows systems provide the [Windows Character Map](https://support.microsoft.com/de-de/office/einf%C3%BCgen-von-ascii-und-unicode-symbolen-oder-zeichen-westliche-sprachen-d13f58d3-7bcb-44a7-a4d5-972ee12e50e0#bmcharactermap).
For example, you can locate the Greek alphabet in the extended view by grouping by "Unicode subrange".
For Windows users, an alternative is [BabelMap](https://www.babelstone.co.uk/Software/BabelMap.html).

Particularly frequently used special characters can be [added to](/epigraf/user/configuration/annotations) the Epigraf toolbar by administrators.

# Batch Processing

Batch processing enables you to process multiple items automatically. It is primarily used for maintenance work and can be accessed via the Mutate button. After selecting the items, choose a task.

-   **Move to Project**: The selected articles are moved to another project.
-   **Delete articles**: The selected articles will be deleted.
-   **Copy articles**: The selected articles are duplicated.
-   **Assign collection**: All selected articles are assigned to a category.
     You can use this feature to group articles and use the faceted search to select articles from different groups.
     Within each article, a "Collections" section (sectiontype=collections) is created if it does not already exist.
     Within the collection section, a collection item (itemtype=collections) is created and linked to the selected property.
-   **Assign article number**: A new signature entity is inserted at the first position in the "Signatures" section (sectiontype=signatures).
    The feature is used to number articles for publication.
     The new signature is a consecutive number, according to the order of the selected articles.
     At the same time, a reference to a selected literature title (propertytype=literature) is created.
-   **Summarize Text:** An automatic summary is generated or an existing summary is updated in the summary section (sectiontype=summary).
     This features requires to configure an LLM service (usually [https://databoard.uni-muenster.de](https://databoard.uni-muenster.de/)).
-   **Rebuild section order**: In case the section hierarchy is corrupt, you can repair it by rebulding the section order.
-   **Rebuild fulltext index**: The full text index for the selected articles is updated.
     You should update the index after the configuration for the full-text search has been changed.
-   **Rebuild dates index**: The date key is updated for the selected articles.
     The date key is used to sort articles according to natural language and fuzzy dating (e.g. 15th century, 1433).
-   **Assign IRIs**: Each Epigraf entity is identified by a unique IRI.
    Epigraf IRIs contain a table name, the entity type and an IRI fragment identifying the entity.
    For articles, the IRI fragment should contain the database name and the article ID.
    The IRI fragment should always remain stable, even when an article is transferred to another database and gets a new ID.
    By default, after new articles have been created, the IRI fragment field (`norm_iri`) is empty.
    When accessing (or transferring) an article without an IRI fragment, a virtual IRI is generated from the database name and the article ID.
    In the rare case that you need fixed IRI fragments in the database, you can permanently store them by the "Assign IRIs" task.

