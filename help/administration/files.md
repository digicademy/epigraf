---
title: Files and Folders
permalink: '/administration/files/'
---

You can add a description to each folder that explains the role of the folder to users.
Further, you can use the folder configuration to set up automatic deployment of external content
to the server.

# Synchronization with external sources

Folders can be synchronized with external repositories via WebHook as follows, provided the external repositories are available as zip files:

**1. Configure the folder**

In the folder configuration, set the `origin` key to the source zip file.
In the `unzip` key profice a list of subfolders that are to be extracted from the file.
Example:

``` plaintext
{
  "origin": "https://gitlab.rlp.net/adwmainz/digicademy/di/epigraf/epipresets/-/archive/devel/epipresets-devel.zip",
  "unzip": "epipresets-devel"
}
```

When editing, the folder ID is displayed in the URL. Note the ID for the following step.

**2. Authorize a bot user**

After setting up the folder configuration, a pull button is displayed in the folder's footer.
The pull function can only be used with explicit authorization.
To trigger synchronization you can use a bot account with the following permissions:

<figure class="table">
<table>
<thead>
<tr>
<th>Setting</th>
<th>Value</th>
</tr>
</thead>
<tbody>
<tr>
<td>User</td>
<td>A custom user name, e.g. `pullbot`.</td>
</tr>
<tr>
<td>Role</td>
<td><em>leave blank</em></td>
</tr>
<tr>
<td>Requested by</td>
<td>API access</td>
</tr>
<tr>
<td>Permission type</td>
<td>access</td>
</tr>
<tr>
<td>Endpoint name</td>
<td>app/files/pull</td>
</tr>
<tr>
<td>Entity type</td>
<td>record</td>
</tr>
<tr>
<td>Entity name</td>
<td>*</td>
</tr>
<tr>
<td>Entity ID</td>
<td><em>ID of the folder, for example 76601</em></td>
</tr>
</tbody>
</table>
</figure>

**3. Set up a webhook**

To trigger synchronization, you can use Webhooks in a GitLab repository.
Issue a POST request to the following endpoint:

`https://epigraf.inschriften.net/files/pull/{FOLDERID}.json?token={ACCESSTOKEN}`

The ACCESSTOKEN can be created in the bot's user profile. FOLDERID corresponds to the folder ID.
