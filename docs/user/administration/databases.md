---
title: Database Administration
permalink: '/user/administration/databases/'
toc: false
---

Epigraf manages separate databases for different projects.
Click the top left button to open the database management page.

New databases are created by administrators in the database management page.
Epigraf ships with some example databases that can be used as a playground
for learning how to configure Epigraf. To create a database from one of the examples:

1. Click `Create database` in the footer
3. Name your database, e.g. `movies`
4. Select a preset, we recommend the `Example movie database` for getting started.
5. Save

The example movies database is now available in the database list.
Select it and click the green *Show articles* button.
While administrators can access all databases, other users need to be granted access to a database
by an administrator. To grant access, open the user management page and click the *Grant* button
in a specific user profile.

A project database comprises an SQL database and a folder for additional files.

After creating a new database, it needs to be configured.
This involves creating a types configuration to adapt the database to a use case and to import
data such as the properties used to annotate content.
Ony admins can configure the types of a database.

Instead of starting from scratch, you can transfer the configuration of an existing database,
import it from CSV or XML files or generate it using the API.

On the database management page, you will find buttons to backup a database or
to restore backups. To duplicate a database, first, create a backup. Second,
create a new database and import the backup.
