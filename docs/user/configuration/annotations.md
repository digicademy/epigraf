---
title: Annotations
permalink: '/user/configuration/annotations/'
---

**Links** are used to link text segments to properties or other entities. The text segment is marked with a tag. Use links for structured annotations such as references to persons, places, content analysis categories or simple text highlighting.

**Footnotes** are standalone tags or tags around a text segment that refer to a footnotes entity containing a comment relating to the text passage. Use footnotes for critical apparatus, comments, or memos.

# Rendering options
Both annotation types can be rendered as formatting (e.g. italic text), as stand-alone tags (e.g. word separators or literature references), or as brackets (e.g. indicating text added in a transcription). Each tag can contain additional attributes. They are stored in the database fields using XML tags and converted to HTML tags for displaying.

Annotations do not necessarily need a links or footnotes entity and, thus, can also be used as simple markup tags. By default, each annotation has a button in the toolbar.

The tag_type key of the configuration determines the behavior (work in progress):

- **Tags**: Tags with standard tool buttons. Rendering is determined by the tag_type key: `text` (render a standalone value), `format` (format a text segment), `bracket` (the text segment is enclosed in brackets). Each tag can have attributes and link to one single entity, such as a property or an article.
- **Attributes**: Annotations that link to multiple categories are called molecules. The molecule itself is a standard tag configruation. It refers to other link types in their attributes key. The configuration entitities need to have the tag type `attribute` and their toolbutton must be disabled.
- **Dropdowns**: Annoations can be grouped to create drop-down buttons. To create such a tool button, configure a simple links annotation but omit the tag_type key. In the toolbutton configuration object, set the dropdown key to `true` and set a value in the group key. All other links types to be subsumed under this dropdown must have the same group key value in their toolbutton configuration and no other toolbutton keys.
- **Line breaks**: Line breaks need to be configured using a links annotation with tag type `break`.
- **Special characters**: Configuratiuon entities with the tag_type `character` are considered special characters and are inserted via the special characters button in the toolbar.

Each annotation, whether link or footnote, has a name and optionally can be assigned to a custom group by setting a common value in the group key. When editing, for each group, you can separately toggle the annotation boxes using the settings button next to the text input fields. Further, you can use the group names instead of annotation names to configure which annotations are allowed in which fields.

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
<td>group</td>
<td><em>Optional.</em> Name of the group to which this annotation belongs, for example “transcription”. This allows multiple annotations to be addressed collectively. This is particularly useful when configuring XML fields, as it means you don't have to list all allowed annotations individually. Instead, you can add the group name.</td>
</tr>
<tr>
<td>toolbutton</td>
<td>A toolbar configuration object (see below). Example for a <em>literature reference button</em>:
<pre class="plaintext">"toolbutton": { "group": "links", "icon": "literature"}</pre>
The configuration object can be abbreviated:
<br><strong>1. Boolean-value</strong> (<code>false|true</code>): By default, each annotation is assigned its own button in the toolbar (<code>true</code>). To prevent a button from being created, the key is set to <code>false</code>. Usually you should disable tool buttons for special characters, because they are placed in the special characters drop-down menu (see below). Corresponds to the <code>enable</code> key in the full configuration.<br><strong>2. String</strong>: Label of the tool button, for example <code>[abc]</code> or a Unicode symbol. Corresponds to the <code>symbol</code> key on the full configuration.</td>
</tr>
<tr>
<td>shortcut</td>
<td>Editor shortcut. Examples:
<ul><li>Alt+N</li>
<li>Ctrl+Shift+A</li>
<li>F9</li></ul>
The options are determined by the <a href="https://ckeditor.com/docs/ckeditor5/latest/api/module_utils_keyboard.html#function-parseKeystroke">CKEditor</a>.</td>
</tr>
<tr>
<td>tag_type</td>
<td>Defines how the annotation is rendered.
<ul>
<li><strong>text</strong>: A standalone tag, for example, a dedicated symbol for word separators in transcriptions. In the output, the values of the keys html_prefix, html_content, and html_postfix are concatenated.  Alternatively, the value of an attribute can be used, for example, to output URLs, see the attributes configuration below.</li>
<li><strong>format</strong>: The text is formatted, for example in italics or as bold text.</li>
<li><strong>bracket</strong>: The text is placed in brackets. The html_prefix value is used as opening bracket, the html_postfix value as closing bracket.</li>
<li><strong>attribute</strong>: A configuration used in the attributes key of molecular annotations, without a tag on its own.</li>
<li><strong>group</strong>: A configuration used exclusively for grouping (drop-down tool button, colours of annotation groups).</li>
<li><strong>break</strong>: A line break rendered as br tag.</li>
<li><strong>character</strong>: A special character added to the character toolbar (see below for special characters configuration).</li>
</ul></td>
</tr>
<tr>
<td>attributes</td>
<td>Optional. An attributes list object. Each key is an attribute name, and the value contains an attribute configuration object with the following keys:
<ul>
<li>caption: A label for input fields in edit mode.</li>
<li>default (optional): The default value in input fields and for rendering when the attribute is empty.</li>
<li>render (optional): Attribute values can be rendered as brackets or standalone elements, based on the following values:
    <ul>
        <li><code>text</code>: For standalone elements with the tag type `text`, the attribute value is used instead of html_content.</li>
        <li>With the specification <code>attribute</code>, the attribute is output as an attribute.</li>
        <li><code>prefix</code> or <code>postfix</code>: For brackets, the attribute value is used as opening or closing bracket.</li>
    </ul>
</li>
<li>repeat (optional): If the attribute contains a number, you can define a value in the repeat key. For rendering, the repeat value will be output the times given in the attribute. If the attribute is empty or contains the number 0, the value given in <code>default</code> is rendered one time.</li>
<li>values (optional): Either a regular expression or a list or an object, depending on the input key setting.
    <ul>
      <li>A regular expression is used to restrict the permitted values in text fields. For example, <code>[0-9]+</code> restricts the values to numbers.</li>
      <li>For select inputs, a list of available values can be used, for example <code>["lost", "omitted", "ellipsis"]</code>.</li>
      <li>For select inputs, an object with keys to be used as values in the XML attribute and values used as labels in the input, for example: <code>{"lost": "Lost","ellipsis": "omission"}</code></li>
      <li>For checkbox inputs, an object with two states. The first key is used when the checkbox is not activated, the second key when it is activated. The checkbox label is taken from the first value.</li>
    </ul>
</li>
<li>input (optional):
    <ul>
      <li><code>text</code> (default): A text input field.</li>
      <li><code>select</code>: A combobox input based on the values key.</li>
      <li><code>checkbox</code>: A checkbox input based on the values key.</li>
      <li><code>link</code>: For molecular annotations, a select input used to choose from property entities. Must be used in combination with the type key to determine the target property type.</li>
    </ul>
</li>
<li>type (optional): Used in conjunction with link inputs to create molecular annotations. Provide the name of the links configuration that defines a target property type in its `to` field configuration.</li>
<li>title (optional): A brief help text.</li>
</ul>
The attributes <code>id</code>, <code>value</code>, <code>class</code> and attributes with an <code>data</code> prefix are reserved and must not be used.</td>
</tr>
<tr>
<td>fields</td>
<td>Field configration list object. Keys represent field names, values contain a field configuration object including the rendering configuration. Available fields for footnotes:
<ul>
    <li>
        <strong>name</strong>: The name field of a footnote entity contains the footnote number. To render the footnote number, provide an object with the following keys:
        <ul>
            <li><code>format</code> must be set to the value `counter`.</li>
            <li><code>render</code>: Define one of `prefix`, `text` or `postfix` to push the number to the rendering of one of those components.</li>
            <li><code>counter</code>: The number type, one of the following values:
                <ul>
                    <li>numeric: A usual number.</li>
                    <li>alphabetic: A latin letter (variants: alphabetic-lower, alphabetic-upper)</li>
                    <li>roman: A roman numeral (variants: roman-lower, roman-upper)</li>
                    <li>greek: A greek number (variants: greek-lower, greek-upper)</li>
                </ul>
            </li>
        </ul>
    </li>
    <li><strong>segment</strong> and <strong>content</strong>: Field configuration objects for the fields holding footnote text and a reference text. The <code>format</code> subkey must be set to "xml". Configure the permitted annotations in the <code>types</code> subkey by providing a list containing annotation names and group names. The <code>caption</code> subkey can be used to set a heading. If the <code>autofill</code> subkey is set to true, the selected text is transferred to this field when a footnote is inserted. Alternatively, autofill can be set to an object with the keys `prefix` (the value is prefixed), `postfix` (the value is suffixed) and `wrap` (a link type name that is used to create an enclosing tag, e.g. `quot`).</li>
</ul>

Available fields for links:
<ul><li><strong>to</strong>: The target field configuration restricting what kind of links are allowed. Each link is part of a root entity and has a source. The source is either a tag in an XML field of the root entity (e.g. an article) or in one of its parts (e.g. an item in a section of an article). The target can be any other entity, in the database, the target reference is stored in a pair of `to_tab` and `to_id` columns. Thus, from the perspective of a root entity, there are external links (e.g. a link from article content to a property) and internal links (e.g. a link from one section to another section within the article). The <code>format</code> subkey must be set to "record" for external links and to "relation" for internal links. The <code>required</code> subkey can be set to "true" to force a selection in the editor. The <code>manage</code> subkey can be set to "true" to allow editors to quickly access and manage respective categories. The <code>limit</code> subkey can be set to either "article" opposed to the standard "project" to restrict the annotation selection to entities present in the current article (useful for working with categories such as person names). The <code>targets</code> subkey further specifies what can be referenced:
  <ul><li>articles: Permitted values are "internal" for internal references and "external" for references to other articles.</li>
  <li>sections: If the link target should be a section, provide a list of allowed section types.</li>
  <li>items: If the link target should be an item, provide a list of allowed item types.</li>
  <li>properties: If the link target should be a property, provide a list of allowed property types.</li>
  <li>footnotes: If the link target should be a footnote, provide a list of allowed footnote types.</li></ul></li></ul>
The target value can be rendered in standalone tags as well as opening or closing brackets. Set the <code>render</code> subkey to one of
`prefix` or `postfix` (for the brackets), or `text` (for tag values).
</td>
</tr>
<tr>
<td>prefix</td>
<td><p>For <strong>bracket tags</strong>, the value used for the opening bracket.</p>
<p>For <strong>text tags</strong>, this value is prefixed to the value given in the `html_content` key.</p>
For <strong>format tags</strong>, this value is inserted as text at the beginning of the selected range.</td>
</tr>
<tr>
<td>content</td>
<td>For <strong>text tags</strong> this value is rendered as the tag content. For example, word separators can be represented by a middle dot.</td>
</tr>
<tr>
<td>postfix</td>
<td><p>For <strong>bracket tags</strong>, the value used for the closing bracket.</p>
<p>For <strong>text tags</strong>, the value is appended to the `html_content` value.</p>
For <strong>format tags</strong>, this value is inserted as text at the end of the selected range.</td>
</tr>
<tr>
<td>escape</td>
<td>If the values in <code>prefix</code>, <code>content</code> and <code>postfix</code> need to be interpreted as raw HTML, disable escaping by setting it to <code>false</code>.</td>
</tr>
<tr>
<td>html<br>md<br>txt<br>rdf<br>ttl<br>jsonld<br></td>
<td><p>Depending on the output format, tags are rendered differently: HTML requires tags, whereas Markdown and full-text search should not contain tags.</p>
<p>In the html, md and txt keys, rendering objects with the keys `tag_type`, `tag`, `prefix`, `content` and `postfix` can be provided. These override the default settings for the respective formats. This way, for example, the rendering of a paragraph can be changed from tag_type "bracket" for the default HTML rendering to "format" for Markdown rendering in order to hide the markup.</p>
If possible, it is advised to set the default rendering to plain outputs without markup and then explicitly override the rendering in the `html` key - instead of first defining HTML rendering and then reverting it for all other possible output formats. For example, you can omit bracket rendering by default and only output bracket markup in HTML format.</td>
</tr>
<tr>
<td>html.tag</td>
<td>The tag name used for HTML rendering. If omitted, by default, all XML tags (which correspond to the annotation names in the types configuration) are converted to span tags with CSS classed that can be used for styling. Each converted tag is assigned the css class `xml_tag`. In addition, each tag is assigned a css class according to the pattern `xml_tag_&lt;name&gt;`. For example, a links annotation named `wtr` will be stored in the XML field as wtr tag and rendered as a span element with the class `xml_tag_wtr`. Format tags, in addition, are assigned the css class `xml_format`. Bracket tags are converted into three tags. The opening bracket is assigned the class `xml_bracket_open`, the closing bracket is assigned the class `xml_bracket_close`, and a tag wrapping the content is assigned the class `xml_bracket_content`.</td>
</tr>
<tr><td>css_style</td>
<td>CSS styling embedded in the web page when rendering entities as HTML. See the `html.tag` configuration to determine CSS selectors for styling the annotation.
<p>Example to create a break after a tag:</p>
<p><code>.xml_tag_vz:after {content: "\\A" ; white-space: pre;}</code></p>
<p>Annotation groups can be hidden with the settings button next to input fields. This will not really hide the content of all tags in the group. Instead it adds a css class following the pattern `.xml_group_&lt;groupname&gt;_unadorned` to the container which allows you to provide simplified unobstrusive styling. For example, you can change the color from blue to the default black color. Provide default rendering in the `default` subkey and rendering for the unselected state in the `unadorned` subkey.</p>
<p>Example: Small caps configured in a links configuration named `k` that appear as normal text when the text group has been unselected.</p>
<pre class="plaintext">
{
  "default": ".xml_tag_k {
    color: #1ccaaa;
    font-size: 1em;
    font-variant: small-caps }",
  "unadorned": ".xml_group_text_unadorned .xml_tag_k {
    color: inherit;
    font-size: inherit;
    font-variant: inherit; }"
}
</pre></td>
</tr>
<tr><td>color</td>
<td><em>Optional.</em> A colour used to style the annotation box next the input field.</td>
</tr>
</tbody>
</table>
</figure>

# Examples

**Example of a checkbox configuration:**

``` plaintext
{
  "caption": "Line begins within a word: ",
  "title": "If the new line begins within a word, select \"yes\".",
  "input": "checkbox",
  "values": {
    "yes": "yes",
    "no": "no"
  }
}
```

**Example of a molecular annotation configuration:**

If an annotation should link to multiple properties, a corresponding link configuration without a tool button is first created for each target property type. The molecular annotation itself uses them in its attributes configuration. In the following example defines a moleculear annotation with two input fields:

``` plaintext
{
  "tag_type": "format",
  "attributes": {
      "annotations": {
        "input": "link",
        "type": "annotations"
      },
      "categories": {
        "input": "link",
        "type": "categories"
      }
  }
}
```

The configuration refers to other link configurations in the type keys. The respective `annotations` and `categories` annotation
are configured without tool button. The `tag_type` key must be set to `attribute`:

``` plaintext
{
  "group": "annos",
  "toolbutton": false,
  "tag_type": "attribute",
  "fields": {
    "to": {
      "format": "record",
      "targets": {
        "properties": ["annotations"]
      }
    }
  }
}
```

# Toolbuttons

A button is created for each annotation. Their design is defined in the `toolbutton` key.
Icons (or short labels) for the buttons are configured using Uincode characters in the `symbol` key.
You can use icons from [Font Awesome](https://fontawesome.com/search?m=free) if you set the `font` subkey to `awesome`.

As an alternative, Epigraf ships with a set of SVG icons used for transcriptions. The file name of the SVG graphic can be provided in the `icon` key. For annotations that lack a symbol or icon key, the annotation's IRI fragment is used to refer to an SVG file (which then must be present on the server).

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
<td>symbol</td>
<td>A short label such as <code>[abc]</code> or a Unicode symbol such as ✅ for a check mark symbol. After saving, the character is automatically translated into its Unicode escape sequence (<code>"\u2705"</code> in the example).</td>
</tr>
<tr>
<td>font</td>
<td>Set the 'font' key to "awesome" if you want to use Unicode symbols from Font Awesome. Find an appropriate free icon on the Font-Awesome page, e.g. the <a href="https://fontawesome.com/icons/book?f=classic&amp;s=solid"><span>book icon</span></a>. Copy either the glyph or the Unicode codepoint and paste it into the `symbol` key. Code points are inserted with the prefix <code>\u</code>.
</td>
</tr>
<tr>
<td>style</td>
<td>CSS styles to change the appearance fo the button, for example: <code>color:#ff0000; font-weight:bold;</code> for a red bold button.</td>
</tr>
<tr>
<td>group</td>
<td>All toolbuttons with the same group value are combined into a drop-down menu. Example: If the group of link annotations for  reference to literature and for references, to other articles both contain <code>reference</code>), a drop-down menu with the corresponding toolbuttons is created. To bypass drop-down creating, set the group to <code>default</code>.</td>
</tr>
<tr>
<td>dropdown</td>
<td>To design dropdown buttons, a separate annotation is created just for the dropdown button. The <code>group</code> key must match the group of all assembeld buttons, and the <code>dropdown</code> key is set to <code>true</code>. The button can be designed using additional keys such as <code>icon</code>, <code>symbol</code> or <code>style</code>. Example :
<pre class="plaintext">
  "toolbutton": {
    "group": "links",
    "icon": "reference",
    "dropdown": true
  }
</pre>
</td>
</tr>
<tr>
<td>icon</td>
<td>The icon key is an alternative to thy symbols key for using SVG icons. The value refers to a predefined SVG file shipped with Epigraf. Currently, it is not possible to upload user-defined SVG files, they can only be integrated by developers.</td>
</tr>
</tbody>
</table>
</figure>

# Breaks

Line breaks are created in the editor with Shift+Enter. They are stored in XML as empty nl tags and rendered in HTML as br tags. You must always provide a links configuration named "nl" for line breaks, as follows:

``` plaintext
{
  "tag_type": "break",
  "toolbutton": false
}
```

If line breaks are allowed in an input field, the `nl` type must be listed in the types key of the XML field configuration.

# Special characters

The editor contains a standard set of special characters (punctuation marks, currency symbols, units, etc.) that can be inserted using the *Special Characters* button in the toolbar.
More special characters can be added by creating a links configuration with the following fields:

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
<td>name</td>
<td>English name of the special character, should follow the name in the Unicode standard. For example, the character "<span class="code char">■" is called "BLACK SQUARE". The name should be in lower case and without spaces or special characters. In the example, “black_square” would be a perfect name for the configuration entity.</span></td>
</tr>
<tr>
<td>scope</td>
<td>Must be set to `links`.</td>
</tr>
<tr>
<td>category</td>
<td>A custom categoy value makes it easier to navigate in the types configuration, for example "Characters", although it has no consequences for editing.</td>
</tr>
<tr>
<td>caption</td>
<td>Name of the special character displayed in the toolbar, for example "Black square" (in english).</td>
</tr>
<tr>
<td>IRI fragment</td>
<td>Derived from the name prefixed with "char-", for example "char-black_square".</td>
</tr>
<tr>
<td>config</td>
<td>Contains the extended configuration of the special character, see the next table.</td>
</tr>
</tbody>
</table>
</figure>

While the previous fields define the name, the *Config* field configures the behavior in the XML editor. The following keys are important:

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
<td>toolbutton</td>
<td>For special characters, always set to `false`.</td>
</tr>
<tr>
<td>group</td>
<td>The group name can be used when configuring XML fields to control which characters are allowed in the respective field. Usually you should set this to `character`.</td>
</tr>
<tr>
<td>tag_type</td>
<td>For special characters, always set tot `character`.</td>
</tr>
<tr>
<td>content</td>
<td>The character to be inserted. Please note: After saving, the character is automatically converted to its Unicode escape sequence  For example, "■" becomes `\u25A0`.</td>
</tr>
<tr>
<td>shortcut</td>
<td>The keyboard combination to insert the special character, (e.g., "Ctrl+K"), in English and without any spaces.</td>
</tr>
<tr>
<td>pane</td>
<td>Multiple special characters can be grouped together into a pane, for example to create a dedicated "Edition" pane. Special characters not assigned to a specific pane are listed in the "Custom" pane.</td>
</tr>
</tbody>
</table>
</figure>
