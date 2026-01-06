---
title: User Management and Permissions
permalink: 'user/administration/users/'
---

By default, all data stored in Epigraf is only accessible after logging in with a user account.
Each user needs to be registered in the general user management. In addition, basic user data
is stored in the project databases. This makes sure, that a project database is self-contained.
Editor names, for example, are stored in the project database even if user accounts are deleted
from the application database. IRIs are used to match user accounts in a project database to
application-level user accounts. Therefore, every user must have a unique IRI fragment.

To make content accessible, it has to be explicitly published for guest users
and permissions have to be granted to registered users.
Each user account is assigned a role that restricts the actions that can be taken.

<figure class="table">
    <table>
        <thead>
        <tr>
            <th>Role</th>
            <th>Permissions</th>
            <th>Write Access</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>Guest</td>
            <td>Can access published data without logging in. Usually, you should publish some pages for the main menu and one public database. After editors finished their work in own project databases, you publish data by transferring it into the public database.</td>
            <td>No</td>
        </tr>
        <tr>
            <td>Reader</td>
            <td>Can access all data in project databases shared with the user, but cannot modify any data. Reader accounts are used to give access to external researchers without publishing a database.</td>
            <td>No</td>
        </tr>
        <tr>
            <td>Bot</td>
            <td>Bot accounts are used for automated deployment of data, for example to sync folders with external repositories.</td>
            <td>Partial</td>
        </tr>
        <tr>
            <td>Coder</td>
            <td>Coder accounts are used to assign specific data editing tasks to research assistants without granting permssion to the whole database.</td>
            <td>Partial</td>
        </tr>
        <tr>
            <td>Desktop</td>
            <td>EpiDesktop is a legacy application that is no longer supported. EpiDesktop users can read and modify the data in their own project databases. They can edit the wiki.</td>
            <td>Partial</td>
        </tr>
        <tr>
            <td>Author</td>
            <td>Can read and modify the data in their own project databases. They can edit the wiki. Author accounts are the usual accounts for researchers working on the data.</td>
            <td>Partial</td>
        </tr>
        <tr>
            <td>Editor</td>
            <td>Can access and modify all project databases, including the types configuration of the databases. An editor can modify pipelines, edit global pages and the wiki. Editor accounts are used for managing the databases on the server.</td>
            <td>Partial</td>
        </tr>
        <tr>
            <td>Admin</td>
            <td>Can access all data and all functions of the application and has the permission to manage user accounts.</td>
            <td>Yes</td>
        </tr>
        <tr>
            <td>Devel</td>
            <td>Has all permissions of administrators. In addition, developers can access experimental functions.</td>
            <td>Yes</td>
        </tr>
        </tbody>
    </table>
</figure>

Epigraf has hard-wired permissions for the user roles (in the footer of the user management page, click the endpoints button).
Thus, each user has a primary role that determines the base permissions.
While admins and devels can access every database, other users need explicit access permissions for a project database.
You either use the Grant and Revoke buttons in the user profile, or open the permissions page that gives you more fine-grained control.
You will find a button to open the permissions page in the footer of the users page.

You can grant each user specific permissions going beyond the role.
Moreover, you can assign different roles for different databases.
For example, you can grant selected users on the server readonly access to a staging or a workshop database.

# Creating user accounts

User accounts are maintained by administrators. Note the following rules when managing user accounts:

- **User name**: User accounts are assigned on a person-specific basis.
  Avoid accounts shared by different people.
  Users' names should, therefore, contain the initials and the surname of the person.
- **User role**: The most common user role is "author".
- **Primary database**: For each user, set a primary database in the user profile. This is where a user will be redirected after the logging in. In addition, grant access to the database using the button "Grant access to database". Usually you should restrict access to the web scope. You can grant access to multiple databases or revoke access in the user management page. Users switch between database by clicking on the button in the top left menu.
- **Activation**: Although administrators can set a password in the user profile, usually, you should leave the password blank when creating a new user account. Instead, generate an invitation link using the "Generate Invitation" button and send it to the user by email. The link is valid for 18 hours. In the course of activation, users set a password themselves. The invitation mechanism can also be used later by administrators as a password reset link to be sent to a user.
- **IRI fragment**: Each user must have a unique identifier. The IRI fragment is used to match application-level user accounts
  to user accounts in the project databases.

# EpiDesktop (deprecated)

To work with EpiDesktop, the following additional settings are required:

- An article and a book pipeline must be defined in the user account; these serve as the default export pipelines.
- Grant SQL database access via the "Grant permission" button. The password is identical to the password of the user account, and it is set automatically during activation.
- Users can find the settings for EpiDesktop after activation in their user account at <https://epigraf.inschriften.net/users/view/me>

# Permissions

Permissions going beyond the hard-wired roles can be granted to specific users or to specific roles.
Each user account has a primary user role. You can also grant a user all hard-wired permissions of other roles
by adding a permission record that has both set, a role and a user.
In this case, usually, you restrict the additional role permissions to a specific database. For example,
you can grant an author additional readonly access to a second database.

A permission record consists of the following fields:

<figure class="table">
    <table>
        <thead>
        <tr>
            <th>Field</th>
            <th>Explanation</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>User</td>
            <td>User name. Leave blank to assign the permission to a role instead of a specific user.</td>
        </tr>
        <tr>
            <td>Role</td>
            <td>Role. Leave blank to assign the permission to a specific user or select the user's primary role. If you set both, a user and a role, the user gains all hard-wired permissions of that role.</td>
        </tr>
        <tr>
            <td>Requested By</td>
            <td>Web access means that a user needs to login with user name and password. API access means a user logs in using an access token.</td>
        </tr>
        <tr>
            <td>Permission Type</td>
            <td>Use `access` to grant permissions to a user. Locks are used by Epigraf to lock specific records or tables so that user's don't interfere when working in the same database. Data locking should only be managed by the application.</td>
        </tr>
        <tr>
            <td>Entity Type</td>
            <td>Use `databank` to grant permissions to a user. Permissions for specific records are used by Epigraf for locking entities so that user's don't interfere when working in the same database. For global permissions, e.g. to access service endpoints, leave the entity type blank.</td>
        </tr>
        <tr>
            <td>Entity Name</td>
            <td>Name of the project database, including the `epi_` prefix. Names and IDs of databases can be found in the database management page.  The asterisk can be used to grant access to all databases. For  global endpoints that are not bound to a specific database, use the asterisk. To grant access to a service endpoint, enter the service name (e.g. http, llm, reconcile, geo).</td>
        </tr>
        <tr>
            <td>Entity ID</td>
            <td>The entity ID field is used to assign permissions to specific data records, for example to lock entities so that user's don't interfere when working in the same database. Usually you leave the ID field empty, or you set it to the database ID. Names and IDs of databases can be found in the database management page.</td>
        </tr>
        <tr>
            <td>Permission Name</td>
            <td>To grant the default, hard-wired permissions to a user, you can leave the permission name empty. For example, authors need at least one permission to a database specified in the entity name field to use their hard-wired permissions on the project database. This is what happens if you grant access to a database from the user's profile page. For permissions going beyond the role, select the endpoint name. Global endpoints are prefixed with "app", database-specific endpoints are prefixed with "epi". You can use the asterisk to grant access to all endpoints, but use it wisely. From a security perspective, you should restrict access to what is really necessary. </td>
        </tr>
        </tbody>
    </table>
</figure>

Endpoints usually align with the URLs in the address bar of the browser. Each URL and, thus, each endpoint consists of a controller (e.g. `users`) and an action (e.g. `view`). Project-related URLs start with the `epi` route followed by the database (e.g. `playground`, the epi prefix of the database name is omitted in the URL). In the URLs, the `index` action can be omitted as it is the default action of a controller.

# Publishing data for guest users

In principle, all data is only accessible after logging in, unless it has been explicitly published. Sharing data from a project database with users who are not logged in, requires a permission for the guest role that grants access to at least one endpoint for a specific database. It is not advised to grant guests access to global endpoints or to work-in-progress data in project databases. Instead, create a **public database** (named `epi_public`), grant access to guest users and transfer data to the public database once it is ready.

Access to entities is controlled by their **publication status** and by their **types configuration**. Guest users can only access projects, articles, sections, items and also types (!) that are published. Make sure to publish the necessary types entities - otherwise you might see rendered content, but with limited interactivity, since some JavaScript widgets rely on requesting types from the server. Moreover, check your sections and items configuration, you need to set their `public` key to true. Then transfer all types to the public database.

The publication status of sections and items may remain empty - in this case, they inherit the status of their container (items inherit from sections, sections inherit from articles). You can use the publication status field to hide specific content. For example, you can publish an images section, but unpublish selected image items within the section.

Within project databases, you will find a distinction between "published" and "searchable". While published entities can be accessed by guests if they know how to find them, such entities are only listed in the article table (and thus findable) if their status is set to "searchable", which includes that an entity is published. It is advised to update the publication status of the entities before transferring them to a public database. This keeps the databases in sync and avoids manually updating data in the public database.

Visibility of some **fields** is restricted. Guest users can't see the names of editors (modified_by, created_by), the creation date (created),the article'S editing status field (status) and the section notes.
