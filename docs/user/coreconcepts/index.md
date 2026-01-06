---
title: Core Concepts
permalink: 'user/coreconcepts/'
---

The core concepts of Epigraf evolved over several decades along experience with heterogeneous research databases.
We advise to get familiar with the concepts before diving into administration, configuration, data management and development.

**Relational Article Model**: Epigraf stores complex documents in a simple relational database.
For import and export operations you should understand how arbitrary data is mapped to the fixed set of tables and fields.
The mapping is based on a types configuration that allows for adapting Epigraf to a wide range of use cases.

**International Resource Identifiers**: Every entity in the database is uniquely identified by an IRI.
Understanding the IRIs scheme of Epigraf helps you with data import, export, transfer and publication.

**Extraction Keys**: Epigraf uses object-relational mapping to load data from databases into objects.
Extraction keys are then used to retrieve the data for display in table columns, for example.

**Application Programming Interface**: Epigraf is based on an API that uses the Model-View-Controller (MVC) architecture.
The MVC paradigm is one of the most widely adopted application architectures as it separates data management and rendering outputs.
Consequently, every URL you see in the browser address bar represents a call to a controller and an action. Further options are passed in path and query parameters.
You can easily change the output format of the calls by adding a format extension, such as .json or .xml, to the path.
