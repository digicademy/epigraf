---
title: Core Concepts - International Resource Identifiers
permalink: '/coreconcepts/iris/'
---


In Epigraf, most entities are identified using IRIs, which are internationally unique identifiers that remain stable even if a data record is copied or exported to other databases. In Epigraf, **full IRIs** contain the following data:

-   Domain: Namespace of all entities, for example `http://epigraf.inschriften.net/iri/`.
-   Table: The name of the table in the database, for example `items`.
-   Type: Each table is divided into different segments by the types configuration, for example `text` or `image`.
-   Fragment: The IRI fragment is a custom value that identifies the entitiy in the table, for example `di-103-17`.

This results in the example IRI `http://epigraf.inschriften.net/iri/items/text/di-103-17` as an identifier of the text content in article number 17 from the volume number 103 in the project "Die Deutschen Inschriften". By using such IRIs when importing data into Epigraf, new data can be not only be created, existing entities can be updated at any time.

The **IRI fragment** sometimes has a more complex structure. In principle, it is an arbitrary value, as long as it remains unique. We recommend to build IRIs using the following components, separated by a tilde:

-   Data source: Unique identifier of a database, data source or corpus, for example `di` for the series "Die Deutschen Inschriften". The data source is usually omitted if the entity was digitally born in the current database. Nevertheless, adding at least the database name is particularly helpful when entities later leave the original database.
-   Article signature: Unique identifier of an article, a number or name within a corpus, for example ‘di-103-17’ for article 17 in volume 103 of the inscription series.
-   Section name: Unique identifier of a section within an article, for example `description`. IRIs always contain the section type, thus, it is not crucial to repeat the type in the IRI fragment. But a section name is always necessary if there are several sections of the same type.
-   Item number: Multiple items within a section are numbered consecutively, for example with `1`, `2`, `3` and so on. This is necessary if an article or section contains several items of the same type.

In the example, the IRI `http://epigraf.inschriften.net/iri/items/text/di~di-103-1~description~1` is used to designate a specific item.
As a rule, full IRIs are not used for data synchronization. The domain usually is omitted
because the **IRI path** consisting of table, type and fragment is sufficient to identify the entity.
In the example, the IRI path is `items/text/di~di-103-1~description~1`.

To make sure that IRI fragments from different databases do not conflict, it is advised to include the **database name** in the IRI fragment.
The [transfer function of Epigraf](../export/transfer) creates IRI fragments automatically if the corresponding field is empty.
For example, the section with the ID 18 in the epi_nrw database automatically receives the IRI fragment \“nrw\~18\”.
Assuming it is a section of type "locations", the full IRI would be `http://epigraf.inschriften.net/iri/sections/locations/nrw~18` and it can no longer be confused with IRIs from other databases on the same server.

When importing from **sources** other than epigraph databases, a unique source identifier and a unique document identifier should be included analogous to the database name. For example, if an article with the signature "ani576mc" is imported from a corpus with the name "Anima", then an appropriate IRI fragment would be `anima~ani576mc`.

The IRI fragment is stored in the `norm_iri` field of the database tables.
Any alphanumeric value may be entered, consisting of the characters a-z, 0-9, - and _. Only lower case is allowed. Other spaces, special characters and umlauts are not permitted. An exception is the tilde to identify data sources and components of the IRI fragment schemes (see below).
Examples of valid values are numbers such as "1" or words in lower case such as "kreuz". English identifiers such as "cross" or identifiers from standardized vocabularies such as "deu" (ISO 639-3 code for the German language) are to be preferred.

It is important that IRI fragments and entity types are not changed once they have been established.
Each IRI must be unique in the Epigraf universe, i.e. also unique across databases so that databases can be synchronized.

## IRI schemes

While articles can be identified simply by a signature or number, the contained sections and items require more attention. All sections and items within a database share the tables and types. Therefore, unique identifiers binding sections and items to their container entities are should be included in the IRI fragment. We recommend separating the components with a tilde, starting with the IRI fragment of the article.

**a) Recommended scheme for articles**

``` plaintext
articles/<type>/<datasource>~<articlesignature>
```

**b) Recommended scheme for sections**

``` plaintext
sections/<type>/<datasource>~<articlesignature>~<sectionname>
```

If an article contains only one section of the type, for example only a single section with images, the article signature is sufficient. The type clearly identifies the section within the article.

However, if several sections of the same type appear in an article, they must be differentiated using a postfix. Disambiguation is necessary because all sections of a type begin with the same IRI path (e.g. `sections/text/`). For this reason, a **section identifier** is appended with a tilde. For example, if an article contains a description and a comment each with the same section type "text", then an IRI fragment can be created as follows for two different sections within an article (the data source is "anima", the article signature is "ani576mc"): `anima~ani576mc~description` and `anima~ani576mc~comment`.

**c) Recommended scheme for items**

``` plaintext
items/<type>/<datasource>~<articlesignature>~<sectionname>~<itemnumber>
```

As with sections, the article signature is sufficient if only a single item of the type occurs in an article, for example only a single image. Otherwise, the item must be uniquely identified within the database via the postfix.

In case each section only contains one single item, the section identifier is sufficient. For example, you could use the same IRI fragment as in the sections: `anima~ani576mc~description` and `anima~ani576mc~comment`. Items are numbered in the database. In case more than one item per section may occur, an item number can be included in the IRI fragment. Use for example `anima~ani576mc~description~1` and `anima~ani576mc~description~2` to denote two consecutive items in the same section.

Please note that the IRI paths for items must be unique regardless of their parent article or section. Therefore, always include an identifier for the article and the section or make sure to use a unique numbering scheme (you can use the entity ID) throughout a database.

**d) Recommended scheme for properties**

Some of the categories specifically tailored to the respective projects, such as the literature used. Others are shared between different projects, for example languages or the classification of object types. Make sure to use the same IRI fragment for the same properties in different databases. The implementation is simple: either use an identifier from authority data or controlled vocabularies (e.g. an ISO language code), invent your own identifier or derive an identifier from the property lemma:

``` plaintext
properties/<type>/<datasource>~<lemma>
```

## Resolving IRIs

An IRI is used to uniquely identify an entity worldwide. In consequence...

- ...categories such as font types, word separators or object types are identified, even if the labels change, for example when translating labels between languages.
- ...categories can be merged and compared between the databases of different project teams.
- ... categories can be imported from external data sources (CSV, XML). Categories that already exist in the database with the same IRI are updated and not newly created.
- ... fixed categories such as measurement units can be used in export stylesheets to implement conditions for rendering the content.
- ... documents including the content can be transferred and updated between databases and servers.

IRIs are structured like the URLs of a webpage. An example of a simple IRI is
`<https://epigraf.inschriften.net/iri/articles/epi-article/1>`. The IRI redirects to an article that describes an epigraphically relevant object. This IRI is made up of the following parts:

- The **IRI path** `articles/epi-article/1` follows the pattern `<table>/<type>/<irifragment>` as described above. The first part corresponds to the database table (e.g. articles, sections, properties), followed by the [entity type](../configuration) (e.g. for the properties table fonttypes or locations) and finally the IRI fragment.
-   The **IRI endpoint** `https://epigraf.inschriften.net/iri/` ensures that the IRI can be resolved. This IRI endpoint resolves to the public database on the server. Therefore, to make entities accessible by its IRI, it should be tranferred to the public database. As an alternative, each database has its own IRI endpoint according to the scheme `https://epigraf.inschriften.net/epi/<db>/iri/`, with a placeholder for the database name. The Epigraf IRI controller has a fall back mechanism for IRIs not found in the public database. If the IRI fragment contains the database name in the source component (the part before the first tilde), the public endpoint redirects to the database specific endpoint. The controller also resolves virtual IRI fragments consisting of the database name followed by a tilde an the entity ID. Keep in mind, that only published databases are accessible without authorisation.

## Authority data and namespaces

In addition to the IRIs of the Relational Article Model, IRIs from **external vocabularies** (so-called authority files) can be used in Epigraf. An example is the IRI "https://sws.geonames.org/2917788/" to identify the city of Greifswald. Such IRIs are entered into the `norm_data` field  of articles and categories. Examples of controlled vocabularies include Geonames for locations, GND numbers for persons and Wikidata IDs for all kinds of entities. Multiple IRIs can be separated by line breaks.

Epigraf supports full IRIs and IRIs with namespace prefixes such as "geonames:2917788" (so-called QNames) . Namespace prefixes must be configured in the respective type configuration. Using a prefix ensure that IRIs do not become cumbersome and long.  Within Epigraf, the prefix "epi:" is used for `https://epigraf.inschriften.net/iri/`. Examples of IRIs with an epi-prefix are "epi:properties/wordseparators/1" or "epi:articles/epi-article/1".

