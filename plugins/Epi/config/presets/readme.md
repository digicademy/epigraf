# Example data

## How to prepare example data?

1. Prepare a local database with the content and configuration
2. Then clean the database:
    ````
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
    ````
3. Create a zipped dump with the Epigraf backup routine
