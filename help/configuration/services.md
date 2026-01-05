---
title: Services Configuration
permalink: '/configuration/services/'
---

Services pass entity data to a service and save the returned value in a target field of existing or newly generated items.
They are used to get authority data from reconciliation services, coordinates from geocoding services or codings and summaries from LLM services.

Services for properties are configured inside the target field configuration, e.g. in the norm_data field configuration. For articles, the configuration should be added to the source item configuration. The resulting service buttons below a field or item can either be manually clicked by users or triggered by batch operations using the mutate button.

# How to configure a service for properties?

## Field configuration

The services configuration for properties is added to the target field. It is an object with an arbitrary key for each service and a service configuration object as value. The configuration object for property fields contains the following keys:

<figure class="table">
    <table>
        <thead>
        <tr>
            <th>Key</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>caption</td>
            <td>Button caption</td>
        </tr>
        <tr>
            <td>service</td>
            <td>Service name. At the moment only <code>reconcile</code> is supported.</td>
        </tr>
        <tr>
            <td>provider</td>
            <td>An identifier for the reconciliation provider. The provider has to be configured in the app settings of Epigraf by an administrator. Examples: <code>aat</code>, <code>wd</code>.</td>
        </tr>
        <tr>
            <td>input</td>
            <td>The field that is used to query the service, e.g. <code>lemma</code>. In properties, you can use <code>path</code> to send the full lemma path to the service.</td>
        </tr>
        <tr>
            <td>type</td>
            <td>An additional type send to the reconciliation service, e.g. <code>/aat</code> or <span><code>Q18783400</code></span>.</td>
        </tr>
        <tr>
            <td>score</td>
            <td>For batch reconciliation, the score threshold to be passed. From the result list, the first item either marked as match by the service or passing the threshold is inserted into the norm_data field.</td>
        </tr>
        </tbody>
    </table>
</figure>

## Example: Geocoding of properties

In the example, geo data is stored in the content field of a property:

``` plaintext
 "content": {
     "caption": "Geokoordinaten",
     "showcaption": false,
     "format": "geodata",
     "widgets": {
       "map": true
     },
     "keys": {
       "lat": "Latitude",
       "lng": "Longitude",
       "radius": "Radius"
     },
     "template": "list",
     "services": {
       "geo": {
         "caption": "Geocoding",
         "service": "geo",
         "provider": "nominatim",
         "input": "lemma"
       }
     }
   }
```

Make sure to configure the articles and items to show geo data. For example, if the geo data is contained in properties that are used in places items:

- Set the geodata key of the articles type to `{"places": "property.content"}` to show locations on the articles overview map.
- Set the geodata key of the items type to `property.content` to show locations within an article.

## Example: Reconciliation of authority data for properties

``` plaintext
 "services": {
        "aat": {
          "caption": "Reconcile AAT",
          "service": "reconcile",
          "provider": "aat",
          "type": "/aat",
          "input": "lemma",
          "score": 20
        },
        "wd": {
          "caption": "Reconcile WikiData",
          "service": "reconcile",
          "provider": "wd",
          "type": "Q18783400",
          "input": "lemma",
          "score": 20
        }
      }
```

Make sure to configure the namespaces within the property type configuration to generate links.

# How to configure a service for article data?

## Item configuration

The services configuration for articles is added to the source item. It is an object with an arbitrary key for each service and a service configuration object as value. The configuration object for items contains the following keys:

<figure class="table">
    <table>
        <thead>
        <tr>
            <th>Key</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>caption</td>
            <td>Button caption</td>
        </tr>
        <tr>
            <td>service</td>
            <td>Service name. At the moment only <code>llm</code> is supported. This uses the [Databoard Service](https://databoard.uni-muenster.de/) at the University of Münster.</td>
        </tr>
        <tr>
            <td>task</td>
            <td>The task to be performed by the LLM service, one of <code>summarize</code>, <code>coding</code> or <code>annotate</code>. See the [databoard documentation](https://databoard.uni-muenster.de/) for details.</td>
        </tr>
        <tr>
            <td>prompts</td>
            <td>Optionally, a prompt template supported by the service. Defaults to an empty string for default prompts. See the [databoard documentation](https://databoard.uni-muenster.de/) for details.</td>
        </tr>
        <tr>
            <td>multinomial</td>
            <td>Whether to perform the task in single or in multi mode. Coding tasks return the best matching property in single mode and a value for each property in multi mode. See the [databoard documentation](https://databoard.uni-muenster.de/) for details.</td>
        </tr>
        <tr>
            <td>input</td>
            <td>Either the whole article is sent to the service in Markdown format (<code>article</code>) or the content of the item with the service configuration (<code>item</code>). Artikel data is taken from the last saved state while item content ist taken directly from the field in an open article.</td>
        </tr>
        <tr>
            <td>target</td>
            <td>An object with the keys container, itemtype, sectiontype and fields.
                <p>The container value determines whether to update the source item (<code>item</code>), add or update other items in the same section (<code>section</code>) or other items other sections of the article (<code>article</code>). Item type and section type determine which items and sections are affected inside the container.</p>
                <p>The item fields where the result should be stored.</p>
                <p>For summarize tasks, defaults to <code>{"content": "llm_result", "value": "state"}</code>.</p>
                For other tasks such as coding it defaults to <code>{"properties_id": "properties_id", "properties_label": "properties_label", 'value': "value"}</code>.</td>
        </tr>
        <tr>
            <td>tagname</td>
            <td>For annotation tasks the name of the links configuration that will be used to add intext annotation. The property type is derived from the links configuration.</td>
        </tr>
        </tbody>
    </table>
</figure>

## Example: Generate summaries with a Large Language Model

``` plaintext
"services": {
  "summary": {
    "caption": "Automated summary",
    "service":"llm",
    "task": "summarize",
    "input": "article",
    "target": {
      "container": "item",
      "fields": {
        "content": "llm_result",
        "value": "state"
      }
    }
  }
}
```

## Example: Automated coding with a Large Language Model

Add the following configuration to the item that contains the source
text:

``` plaintext
"services": {
  "coding": {
    "caption": "Genre detection",
    "service": "llm",
    "task": "coding",
    "input": "item",
    "target": {
      "container": "article",
      "itemtype": "categories",
      "sectiontype": "categories"
    },
    "fields": {
      "properties_id": "properties_id",
      "properties_label": "properties_label",
      "value": "value"
    }
  }
}
```

In the item type used in the configuration ("categories") configure the property field and link it to a property type. The properties are used to create the rule book.

The result is inserted into the article section defined in the sectiontype value. If a section can hold multiple items (count is set to *), one item for each answer is added. Otherwise the first matching item is updated.

# How to make a service available in Epigraf?

## Application settings

Service URLs and access tokens need to be configured in the app.php. See the developer documentation.

## Permissions

Access to a services needs to be explicitly allowed. Add the following permission record for a user or role:

<figure class="table">
    <table>
        <thead>
        <tr>
            <th>Field</th>
            <th>Setting</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>permission type</td>
            <td>Allow access.</td>
        </tr>
        <tr>
            <td>entity name</td>
            <td>The service name (http, llm, reconcile, geo) or an asterisk as wildcard.</td>
        </tr>
        <tr>
            <td>endpoint</td>
            <td>Select the services endpoint <code>app/services/get</code>.</td>
        </tr>
        </tbody>
    </table>
</figure>

All other permission fields remain empty.
