---
title: Batch Operations
permalink: '/database/batch-operations/'
---

## The Export Pipeline System

Exporting data usually involves a number of tasks: Data needs to be collected,
converted and organised into a target structure such as XML files or folders containing
images and text data.

Epigraf allows you to configure the output process in pipelines.
A pipeline is a collection of tasks.
In a pipeline, the type of tasks and their sequence are defined with the necessary parameters.
Common tasks include:

- Collecting data from the database in XML, JSON or CSV format
- Perform search and replace operations using regular expressions
- Transforming data with XSLT
- Copying files from the file system
- Bundling multiple output files into a single file
- Creating a ZIP archive

Running a pipeline generates a job.
In the job, the defined tasks are processed one after the other.
Each task may contribute content or transformations to an output file.
Finally, the resulting file is stored in the file system or made available for download.

## Batch Manipulation

Epigraf's job system allows multiple records to be imported, transfered, exported or modified in a batch process.
Import, export and mutate operations are implemented in job classes to be found in the
`src/Model/Entity/Jobs` directory. Transfer operations are an import into a target database from a source database.

Each job contains tasks. The available task classes can be found in the `src/Model/Entity/Tasks` directory.

The possible mutate operations, i.e. batch operations for modifying records,
are listed in the table classes. For example, the fulltext index can be regenerated,
articles can be moved to another project or be deleted in batches
(see `ArticlesTable.php`).

## API Packages

The experimental [Epigraf package (R)](https://github.com/strohne/datavana/blob/main/epigraf)
and the [Epygraf package (Python)](https://github.com/strohne/datavana/blob/main/epygraf)
are currently developed to facilitate data work with Epigraf.
They provide functions for data transfer using the Epigraf APIs:
Preparing data imports, e.g. from social media datasets, and preparing data analyses.

