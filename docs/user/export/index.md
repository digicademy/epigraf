---
title: Export Data
permalink: '/user/export/'
---

Epigraf supports to retrieve raw data from the Relational Article Model and to generate polished publication formats.


**Pipelines**: Use the pipeline system to generate camera-ready word documents, TEI-files, RDF-triples, Websites
or other documents for archiving and publication purposes. In a pipeline, data is assebled and transformed to arbitrary target formats.
You can also export images using a pipeline.

**API access**: You can directly access all data visible on the web interface in structured formats such as JSON.
Distilling data from the API is simplified using the [rpigraf package](https://github.com/datavana/rpigraf).

**Transfer**: Use the transfer functions in the web interface or the API to merge data from one or multiple Epigraf database
into other databases. The transfer functions, by evaluating IRIs, make sure that entities keep in sync.

**Database access**: If you own the server and are able to establish SQL connections to the database,
you can directly work with the live database tables. Nevertheless, when working with raw data,
it is recommended to ingest it into a local database. You can generate a backup from the database management page.
The backup dumps all data into an SQL file that can be imported with common SQL management tools such as DBeaver.
You can also import a backup into another (local or productive) Epgraf server using the database management page.
The [rpigraf package](https://github.com/datavana/rpigraf) implements an interface that can be used both,
on direct SQL connections or the API.
