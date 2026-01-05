---
title: Trouble shooting
permalink: /administration/troubleshooting/
---


## An article can no longer be opened, a red error message appears. What should I do?

This error can occur if the section order is broken.
This may happen if a parent section has been deleted without deleting the child sections.
In the [error Log](/pages/show/logs), you should see the message
"The tree contains items with invalid parent IDs or with an invalid order".

To fix the structure:

- Search for the article in the article list using the ID
- Click the mutate button in the footer
- Execute the task "Rebuild section tree"

## When exporting, red error messages such as "Error while executing the task of type transformxsl" appear. How do I find the error?

This message indicates that either an article does not contain content intended for a specific pipeline
or that the export stylesheets cannot handle the content.
To narrow down the error, proceed as follows:

1. Provoke the error
2. Lookup the files in the jobs folder of the affected database.
   The log file provides information on the error source.
   Other xml files contain the status before the error occurred.
3. Open the xml file from the job folder with Oxygen. Execute the affected stylesheets locally and fix the error.
4. Update the stylesheets on the server.
