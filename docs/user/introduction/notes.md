---
title: Introduction to Notes
permalink: '/user/introduction/notes/'
toc: false
---

Notes and the wiki are used for the coordination of research efforts.
For example, you can store common editing guidelines or a codebook for content analyses in the notes.
While notes are specific to a project database, the wiki is accessible to all users on a server.


# Elements of a page in the notes or wiki

A page consists of the following elements:

- Name: The caption of the note
- Content: The content of the note, it can contain text, images, and links to other notes or wiki pages.
- Category: The category serves two purposes.
  First, it groups pages when you browse the full list of pages.
  Second, the menu is generated from the categories of the pages.
  In the category name, separate each level of the hierarchy with a forward slash `/`.
- Menu: The menu is generated from the categories of the pages.
  Only pages where "Add to menu" is checked are included in the menu.
- Speaking URL (IRI): By default, each page has an internal ID.
  If you add an IRI fragment, you can link to the page with a more meaningful URL,
  for example, `/wiki/notes/introduction` instead of `/wiki/notes/1234`.
- Sort key: The sort key is used to determine the order of pages in the menu.
- Published: By default, all pages are only visible to logged in users.
  You can make it available to all users by checking the "Published" checkbox.

# Links

Make sure to remove the domain from internal links and when including images in the wiki, public pages or notes.
In addition, always remove any superfluous parameters, especially the database parameter.
For example, use `/wiki` instead of `https://epigraf.inschriften.net/wiki?database=public`.
