---
title: Working with Files
permalink: '/user/introduction/files/'
---

Images, PDF files or other documents can be integrated into the project databases.
Across projects, uploading files is relevant for import and export pipelines, the wiki and public pages.


# The folder structure of Epigraf

The **database-specific files** are managed via the menu item "Files". Make sure to use the appropriate folders:

-   notes: Files that are included in notes. It is recommended to set up a separate subfolder for each note category.
-   articles: is the right place for all article-related files such as images.
    Use a subfolder named by the project and within the project folder create article folders named by article number or signature.
-   properties: Files that are used in categories. Create separate subfolders for different category systems. Use the property type as folder name.

Management of all other folders should be left to Epigraf:

-   backup: This is where the [database backups](/user/administration/databases) are located.
-   jobs: contains the result of [pipeline](/user/export/pipelines) jobs.
-   import: [Imported CSV files](/user/import/csv) are temporarily stored in this location.

**Cross-database files** are managed via the "Repository" menu item.
The files are grouped into mounts. The shared mount contains at least the following folders:

- wiki: Files used in the wiki are located in a subfolder of the shared mount.
- public: Files used for public pages, for example images used on the Epigraf homepage.
- pipelines: Files used in pipelines, for example XSL-T stylesheets.

The **root mount** is reserved for administrators.
It allows access to all folders, including the shared folder and the project-specific folders.

# Uploading files

The following conventions apply to the naming of files and folders in Epigraf:

-   *Allowed:* Letters a-z, lower case only
-   *Allowed:* Numbers 0-9, hyphen, underscore, plus sign
-   *Not allowed:* No umlauts and no ß, use ue, ae, oe, ss instead
-   *Not allowed:* No spaces and no commas, use underscore or hyphen instead
-   *Not allowed:* No dots, except before the file extension

If new files are uploaded, Epigraf automatically cleans up the file names.

# Using files

All files, whether images or documents, are accessed in Epigraf either via their file path or via an ID.

- **Download links** offer the file for download, for example `/files/download/76459`.
- **Display links** display a file directly in the browser, if possible, for example `/files/display/76459`.

In download and display links, the path and file name can also be specified as parameters as an alternative to the ID of a file.
Example: `/files/display?root=shared&path=pages%2F01-start&filename=logo_mainz.png`.

For images, a scaled-down version can be retrieved using the thumbs and size parameters,
for example: `/files/display/76459?format=thumb&size=600`.

HTML files containing interactive documents can also be adressed using a display link.
This way, HTML files are displayed directly in the browser instead of being downloaded.
