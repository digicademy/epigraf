# SQL dumps for the test system

The dumps were created with HeidiSQL from the local databases
(which were updated with the production system databases first).

- test_epigraf_schema.sql, test_projects_schema.sql, and test_public_schema.sql create the tables
  to be filled with fixture data.

- test_epigraf_tree.sql generates the table for the tree test.

- test_epigraf_versions.sql generates the table for the versions test.

- test_projects.sql is a complete dump of epi_test. It can be produced from within the
  php container using mysqldump:
  ```
  mysqldump --routines --quick -h mysql -u root -proot epi_test > "/var/www/html/tests/Testdata/Databases/test_projects.sql"
  ```
  Before creating the dump, it is advised to delete deleted records from the test database:
  ```
  DELETE FROM articles WHERE deleted <> 0;
  DELETE FROM files WHERE deleted <> 0;
  DELETE FROM footnotes WHERE deleted <> 0;
  DELETE FROM items WHERE deleted <> 0;
  DELETE FROM links WHERE deleted <> 0;
  DELETE FROM notes WHERE deleted <> 0;
  DELETE FROM projects WHERE deleted <> 0;
  DELETE FROM properties WHERE deleted <> 0;
  DELETE FROM sections WHERE deleted <> 0;
  DELETE FROM `types` WHERE deleted <> 0;
  DELETE FROM users WHERE deleted <> 0;
  ```

- test_epigraf.sql was assembled with specific content.
  Docs, for example, were dumped using the following criteria:
  ```
  (segment = 'help' AND deleted=0 AND (category LIKE 'A%' OR category LIKE 'B%')) OR
  (segment='wiki' AND deleted=0 AND (name='Wiki') OR (category='G. Dokumentation')) OR
  (segment='pages' AND deleted=0)
  ```

Snippet to create content test_epigraf.sql from within the container (adjust as necessary)
```
cd /var/www/html/tests/Testdata/Databases
mysqldump --host=mysql --user=root --password=root --extended-insert --no-data --compact epigraf > test_epigraf.sql
mysqldump --host=mysql --user=root --password=root --extended-insert --no-create-info --compact --where="name='epi_test'" epigraf databanks  >> test_epigraf.sql
mysqldump --host=mysql --user=root --password=root --extended-insert --no-create-info --compact --where="norm_iri='start' AND segment='pages' AND deleted=0" epigraf docs  >> test_epigraf.sql

mysqldump --host=mysql --user=root --password=root --extended-insert --no-create-info --compact --where="username='devel'" epigraf users  >> test_epigraf.sql
mysqldump --host=mysql --user=root --password=root --extended-insert --no-create-info --compact --where="username='author'" epigraf users  >> test_epigraf.sql

mysqldump --host=mysql --user=root --password=root --extended-insert --no-create-info --compact --where="entity_name='epi_test'" epigraf permissions  >> test_epigraf.sql
```

