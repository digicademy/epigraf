---
title: Database
has_children: true
nav_order: 40
---

# {{ page.title }}

Epigraf stores data using a relational database management system (MariaDB) and in the file system.
There is one application database `epigraf` for user management, pipelines, help pages, and other application-wide content.
This goes along with a file system area for application-wide images and other files.

For the research data, Epigraf manages multiple project databases, each prefixed with `epi_`.
Project databases have their own subfolders for images and other files.
Each project database can be configured for specific domains, such as inscriptions, letters, or social media posts.
