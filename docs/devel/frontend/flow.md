---
title: Flow
permalink: 'devel/frontend/flow/'
---

The Epigraf interface consists of nested frames, panes, and overlays:

- The main frame handles the full window content.
- Tab frames are contained in the sidebars.
  The detached tab frame is a derivate that temporary moves
  content from an arbitrary element into a sidebar tab.
- Popup frames show content on top of the main frame.
  Specific modal popup derivates are confirm popups, select popups, and message popups.
  The detached popup frame is a non-modal derivate that temporary moves content from an arbitrary element into a popup.
- Dropdown panes are used to display content on top of the main content,
  usually aligned to a toggle button or input.
- Overlays are used to display full-sized content on top of the entire page,
  blocking the interaction with the main frame.
  For example the image viewer or the sidebar content on small screens use overlays.

## The frame flow

When an action requires to display content in a frame,
the target depends on the attributes of the source that triggers the action.

If links (`a` elements) are clicked from the **main frame**,
their `class` attribute  determines the target.
Buttons may have a `data-target` attribute that determines the target.
The following classes or attribute values are supported:

- main: Open in current window.
- tab: Open in a new browser window.
- popup: Open a popup frame.
- frame: Open a tab frame in the right sidebar.

If none of the above classes is set, the default target is the main frame.

If a link is clicked from a **popup frame**, the target is changed,
because popups are considered of as self-contained frames
that should not affect the main window:

- main targets become tab targets
- frame targets become popup targets

## The table flow

Clicking table rows within the main frame usually triggers loading entity content into the right sidebar.
On small screens, there is no sidebar. Instead, the content is loaded in an overlay handled by the
accordion widget.

Table rows contain am action column, marked with the `actions` class and hidden from the user.
The actions column, for each entity, contains links with roles for viewing or editing the entity.

When a table row is clicked, the first action is performed and
the entity is openend in the right sidebar either in view or in edit mode.
In the top right corner, a button for opening the content in a new browser tab is added.

In the sidebar, an open button may be added below the content (usually highlighted)
that opens the entity in a new browser window.

When a table row is double-clicked, the entity is opened in the main frame.

On mouse over events on the table content,
the table cells are wrapped in links determined from the actions column.
This allows for opening entities in a new browser tab by right click.

