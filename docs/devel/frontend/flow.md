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

## The link and button flow

When an action requires to display content in a frame,
the target depends on the attributes of the source that triggers the action.

If links (`a` elements) are clicked from the **main frame**,
their `class` attribute  determines the target.
Buttons may have a `data-target` attribute that determines the target.
The following classes or attribute values are supported:

- main: Open in the current window.
- tab: Open in a new browser window.
- popup: Open in a popup frame.
- frame: Open in a tab frame in the right sidebar.

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

Table rows contain an action column, marked with the `actions` class and hidden from the user.
The action column, for each entity, contains links with roles for viewing or editing the entity.

When a table row is clicked, the first action is performed and
the entity is openend in the right sidebar either in view or in edit mode, depending on the linked URL.

On mouse over events on the table content,
the table cells are wrapped in links determined from the action column.
This allows for opening entities in a new browser tab by right click.

In the top right corner of the sidebar, a button for opening the content in a new browser tab is added.
In the sidebar, an open button may be added below the content (usually highlighted)
that opens the entity in a new browser window.

When a table row is double-clicked, the entity is opened in the main frame.

## Form submission flow

The base form classes hijack form buttons to override their default behavior.
What happens after buttons in a frame (popup, sidebar tab, main frame) are clicked depends on the data-role attribute of the button:

- submit or save: The form is submitted using the action of the form. Form attributes, response URL and content determine the next action.
- cancel: The entity is unlocked and the frame is updated using the data-cancel-url of the form. If the data-cancel-url is not set, the frame is closed.
- delete: The entity is unlocked and the frame is updated using the data-delete-url of the form. If the data-delete-url is not set, the frame is closed.

Other roles such as `import` (=add files to an article) do not result in form operations.

Buttons created by link elements result in loading the content using the href of the `a` element.

### Form processing

Form actions are (usually) processed by the ActionsComponent.
If saving entity data is not successful, the default view is returned in the same frame, so that the user can correct the input.
If saving was successful, the ActionsComponent that processes the submitted form, responds as follows:

- By default, the controller redirects to the view action.
- The default action can be overriden by adding a different action to the `redirect` query parameter of the form action URL.
  (used for articles). Note: the redirect query parameter is also used for user login, entity unlock and file operations.
- When calling the ActionsComponent, you can pass a `redirect` option to override both the default redirect action
  and the `redirect` query parameter.

When calling the ActionsComponent, you can further pass an `open` option with a URL
that is added to the response URL as a query parameter (used for articles and docs).

How those options are used depends on the context of the form submission.

**Add action**
If saving a new entity was not successful, the default view is returned in the same frame, so that the user can correct the input.
If saving was successful, the controller redirects to the view action.

For articles and wiki docs, the entity is opened in a new tab by leveraging the `open` option of the ActionsComponent.
This makes sure the entity is opened even if it is not included in the current table (articles) or if the action
was called from view mode (docs).

**Edit action**
By default, successful saving redirects to the view action, thus, the editing mode is always closed after saving.

Saving articles redirects to the edit action levaraging the `redirect=edit` query parameter.
Thus, articles must be explicitly closed by the user.

### After the form submission

After form submission, the base form class parses the response.
It emits `app:show:message` if it finds a `[data-snippet=message]` element in the response.

It extracts the entity ID, the entity's delete and move flags, and the list of merged ids.
Then it emits corresponding events (epi:delete:row, epi:move:row, epi:merge:row, epi:update:row, epi:create:row).
This allows tables to update their rows and open a new or updated entity in the sidebar.

What follows depends on the specific frame class (which is always inherited from the base form class).

**Main frames**

If the response content indicates an error (the message snippet has the class `error`), nothing happens.
Otherwise, the main frame class parses also the response URL to determine the next action in the following order:

- If the response URL contains an `open` query parameter,
  the window is reloaded with the given URL.
- If the original form element has a `data-proceed-url` attribute,
  the window is reloaded with the given URL.
- Otherwise, the window is reloaded with the response URL.

**Sidebars tabs**

If the response content indicates an error (the message snippet has the class `error`), nothing happens.
Otherwise, the tab frame class parses also the response URL to determine the next action in the following order:

- If the response URL contains an `open` query parameter,
  the frame is updated with the response content and a new browser tabsheet with the given URL is opened.
- If the original form element has a `data-proceed-url` attribute,
  the frame is reloaded with the given URL.
- Otherwise, the frame is updated with the response content.

When updating the frame with the response content, the button roles matter.
The frame is always closed if the role of the button that triggered the action is not `save`
(in contrast to `submit` and all other roles).

**Popups**

If the response content indicates an error (the message snippet has the class `error`),
the popup is updated with the response content, that's it.

Otherwise, the popup class parses also the response URL to determine the next action in the following order:

- If the response content contains `a[data-role="download"]`,
  the popup is closed and the download is started
  (used in the context of the `jobs/execute` action).
- If the response URL contains an `open` query parameter,
  the popup is closed and a new browser tabsheet with the given URL is opened.
- If the response content contains `a[data-role="proceed"][data-target="main"]`,
  the popup is closed and the main frame is redirected to the given URL
  (used in the context of the `jobs/execute` action).
- If the response URL does contain a `close=1` query parameter,
  the popup is closed
  (used in the context of `jobs/execute`, `mutate`, `transfer`, `export`, and `import` actions).
- If the response content does not contain a submit button (indicates there are no follow-up actions),
  the popup is closed.
- Otherwise, the popup is updated with the response content.

When updating the popup with the response content, the button roles matter.
The popup is always closed if the role of the button that triggered the action is not `save`
(in contrast to `submit` and all other roles).

