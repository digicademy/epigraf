---
title: Backups
parent: Database
nav_order: 20
---

# {{ page.title }}

## Versioning and Deletion

In the project databases, when modifying data with the user interface (web and api),
records are versioned by two mechanisms:

- Soft deletion: Records are not deleted in the database but marked as deleted setting
  the `deleted` field to 1. The content is no longer displayed in the user interface,
  but available in the SQL database.
- Versioning: When a record is modified, a copy of the modified record is created with
  the `deleted` field set to 2. The `version_id` field contains the ID of the original record.

## Backup and Restore

The user interface provides a backup function that creates SQL dumps in the database management overview.
In the same place, you can restore a database or import sql files.

Additionally, the command line interface can be used to import databases.
The following command imports a gzipped SQL file into the database `epi_dresden`:

```
bin/cake database import --drop --database epi_dresden --filename epi_dresden.sql.gz
```
