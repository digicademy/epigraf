---
title: Export Data using Pipelines
permalink: '/user/export/pipelines/'
---


# Export pipelines

Pipelines are used to transform data from the relational article model into other data formats
For example, they are used to create Word files, TEI documents or HTML content for a website.

After constructing a pipeline, you apply it to an article selection by clicking the export button.
The export button is available in the footer of each article and also in the footer of the articles table.
To export an entire project, select the respective articles using filters in the article table, i.e. narrow down the article list to a specific project.

Pipelines usually start with RAM data in XML format that is transformed by XSLT stylesheets.
For complex output formats - such as DOC or ODT files - several transformation steps can be processed in succession.
To inspect the initial RAM data, a) open a single article as XML, b) use the default data pipline with the full entity export option, or c) construct a simple pipeline that generates an XML document without any transformation.
XSLT stylesheets required to transform the raw data are usually stored in the pipelines folder.

# Construction of pipelines

Pipelines contain a sequence of tasks that are processed one after another.
A pipeline consists of three types of tasks:

-   Retrieval tasks: A pipeline begins by querying article data and writing it to one or multiple intermediary files.
-   Preparation tasks: Intermediary files are merged into a common file or folder.
-   Transformation tasks: Intermediary files are transformed into the target format using XSLT stylesheets.
-   Output tasks: The resulting files are prepared for download.

UTF-8 is used as encoding in all tasks. Non-printable characters such as the Unit Separator control character are filtered out.

## 1. Retrieval tasks

- Options task: Adds an options object to the output file. Options can be selected by a user when starting the export job.
- Job parameters task: Adds the job entity to the output file. The job, for example, contains information about the current date and time and the server.
- Project data task: Adds the project entity to the output file, if a project was selected when starting the export job.
- Article data task: Adds the selected article entities to the output file.
- Index data task: Creates an index from all categories (=properties) used in the exported articles and adds the index to the output file.
- Property data task: Adds all categories of a selected category system (=property type). The task, for example, is used to export a complete list of literature stored as categories, regardless of whether articles include the literature.
- Types data task: Adds the types configuration to the output file. This way, for example, you can use settings for specific property types to process exported indexes or links settings for rendering annotations.

Exports are based on the selected articles when starting a job. Some data tasks allow for additional settings:

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Option</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Article types / scopes</td>
        <td>The article types can be restricted using a comma-separated list, for example "epi-article". In the types data task, the scope can be restricted, for example to "properties".</td>
      </tr>
      <tr>
        <td>All articles in selected projects</td>
        <td>Export articles that were not selected directly but are contained in the selected project. It is advised to restrict the article type as well, otherwise all articles in a project will be exported. The option is intended to be used for including generic articles for a project. For example, a specific article type can be used to store the introduction for a printed volume that should be exported along with other articles in a project.</td>
      </tr>
      <tr>
        <td>Copy images</td>
        <td>Copy images of the selected articles to the output folder.</td>
      </tr>
      <tr>
        <td>Image item types</td>
        <td>If article images are to be copied, you must provide a comma-separated list of item types containing the image file names.</td>
      </tr>
      <tr>
        <td>Image metadata configuration</td>
        <td>Based on the item type configuration, metadata is written into the image files. In addition, you can add metadata keys in the pipeline that complement or overwrite the item type settings.</td>
      </tr>
      <tr>
        <td>Image folder in the current job folder</td>
        <td>The target folder for images. By default, images are copied to the "images" folder within the job's folder.</td>
      </tr>
      <tr>
        <td>Output file</td>
        <td>By default, the article data is written to the default job file (e.g. job_1234.xml). You can explicitly define a filename and refer to the file in following pipeline tasks. By default, all data is written to one single output file. You can split the output into separate files by using placeholders in curly brackets in the output filename. Placeholders are replaced by entity data. Example: "article-{signature}.xml"</td>
      </tr>
    </tbody>
  </table>
</figure>

## 2. Preparation tasks

- Bundle files: Concatenate all files in a folder into a single output file.
- Copy files: Copy files to the job folder. Can be used, for example, to copy a template for ODT-files.

Depending on the task, further options are available:

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Option</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Source root folder</td>
        <td>The folder that contains files. Bundling is restricted to files in the job folder.</td>
      </tr>
      <tr>
        <td>Source folder or file within the root folder</td>
        <td>Either a file path or a folder path relative to the root folder. The bundle task, by default, and if not specified otherwise, concatenates all files in the job folder.</td>
      </tr>
      <tr>
        <td>File with list of filenames</td>
        <td>Optional. If the source is a folder, the file list can be filtered by providing a file with one file name per row. You could generate such a file list in an earlier stage of the pipeline.</td>
      </tr>
        <tr>
        <td>Prefix and postfix</td>
        <td>For file bundling, text that will be added to the top or bottom of the output file. For example, open a root xml-tag in the prefix setting:
        ```
        &lt;?xml version="1.0" encoding="UTF-8" standalone="yes"?&gt;
        &lt;book&gt;
        ```

        Close the root tag in the postfix setting:
        ```
        <pre>&lt;/book&gt;</pre>
        ```
        In the prefix and postfix, you can use placeholder strings to access job data. Example for the prefix in a TTL export pipeline:
        ```
        <jobs/export/{id}>
         a schema:Dataset ;
         a schema:DataFeed ;
         schema:DateModified "{created}^^schema:Date"
        ```
        </td>
      </tr>

      <tr>
        <td>Target folder / output file</td>
        <td>The target folder (for copy tasks) or output file (for bundle tasks) relative to the job folder.</td>
      </tr>
    </tbody>
  </table>
</figure>

## 3. Transformation tasks

- Transform with XSL: Use an XSLT stylesheet to transform XML files generated in preceding tasks.
- Search and replace: Use regular expressions to replace values in a file generated by preceding tasks.

## 4. Output tasks

- Zip a file or folder tasks: Zip a single file or a folder to prepare it for download.
- Save to file task: Send a file to the browser

Depending on the task, the following options are available:

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>Option</th>
        <th>Description</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Folder or file to be zipped</td>
        <td>Leave empty to zip the default job file. Enter a file name to zip a single file. Enter the name of a folder within the current job folder to zip its content. Make sure you create the folder or file in preceding tasks.</td>
      </tr>
      <tr>
        <td>Outputfile</td>
        <td>The name of the resulting file. Leave empty to use the default job file.</td>
      </tr>
    </tbody>
  </table>
</figure>

# Image export including metadata

Epigraf can be used to export images and automatically update their metadata.
To store image metadata, usually the content field of an image item type is configured
to hold several metadata values as JSON.

The following table shows typical image metadata fields.
The *attribute* column refers to the [Extensible Metadata Platform](https://en.wikipedia.org/wiki/Extensible_Metadata_Platform)
(XMP) standard.

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>What</th>
        <th>Attribute</th>
        <th>Example</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Image title</td>
        <td>xmp:Title</td>
        <td>DI 34, No. 13 - Niederhausen, Protestant parish church - 1st half of 13th century</td>
      </tr>
      <tr>
        <td>Image caption</td>
        <td>xmp:Headline</td>
        <td>Epitaph of Johann von Schmidtburg</td>
      </tr>
      <tr>
        <td>Creator</td>
        <td>xmp:Creator</td>
        <td>Thomas G. Tempel</td>
      </tr>
      <tr>
        <td>Source</td>
        <td>xmp:Source</td>
        <td>Image archive ADW Mainz</td>
      </tr>
      <tr>
        <td>Copyright information</td>
        <td>xmp:Rights</td>
        <td>Heidelberger Akademie</td>
      </tr>
      <tr>
        <td>Terms of use</td>
        <td>xmp:UsageTerms</td>
        <td>CC BY 4.0</td>
      </tr>
      <tr>
        <td>Credit / Provider</td>
        <td>xmp:Credit</td>
        <td>ADW Mainz, Inscription commission</td>
      </tr>
      <tr>
        <td>Copyright status</td>
        <td>xmp:Marked</td>
        <td>True: Protected by copyright. False: Public domain.</td>
      </tr>
    </tbody>
  </table>
</figure>

To transfer metadata into image files, you need to create an export pipeline
that puts all images into a ZIP-archive.
In the item type configuration for images you can explicitly define metadata input fields.
In the export pipeline you use placeholder strings to map article data (including image items)
and project data to specific metadata attributes.
to images when exporting.
To store image metadata, usually the content field of an image item type is configured
to hold several metadata values as JSON.

The following table shows typical image metadata fields.
The *attribute* column refers to the [Extensible Metadata Platform](https://en.wikipedia.org/wiki/Extensible_Metadata_Platform)
(XMP) standard.

<figure class="table">
  <table>
    <thead>
      <tr>
        <th>What</th>
        <th>Attribute</th>
        <th>Example</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>Image title</td>
        <td>xmp:Title</td>
        <td>DI 34, No. 13 - Niederhausen, Protestant parish church - 1st half of 13th century</td>
      </tr>
      <tr>
        <td>Image caption</td>
        <td>xmp:Headline</td>
        <td>Epitaph of Johann von Schmidtburg</td>
      </tr>
      <tr>
        <td>Creator</td>
        <td>xmp:Creator</td>
        <td>Thomas G. Tempel</td>
      </tr>
      <tr>
        <td>Source</td>
        <td>xmp:Source</td>
        <td>Image archive ADW Mainz</td>
      </tr>
      <tr>
        <td>Copyright information</td>
        <td>xmp:Rights</td>
        <td>Heidelberger Akademie</td>
      </tr>
      <tr>
        <td>Terms of use</td>
        <td>xmp:UsageTerms</td>
        <td>CC BY 4.0</td>
      </tr>
      <tr>
        <td>Credit / Provider</td>
        <td>xmp:Credit</td>
        <td>ADW Mainz, Inscription commission</td>
      </tr>
      <tr>
        <td>Copyright status</td>
        <td>xmp:Marked</td>
        <td>True: Protected by copyright. False: Public domain.</td>
      </tr>
    </tbody>
  </table>
</figure>

To transfer metadata into image files, you need to create an export pipeline
that puts all images into a ZIP-archive.
In the [item type configuration](/user/configuration/articles) for images you can explicitly define metadata input fields.
In the article export task, contained in a pipeline, you use placeholder strings to map article data (including image items
and project data) to specific metadata attributes.
