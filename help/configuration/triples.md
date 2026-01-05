---
title: Triples Configuration
permalink: '/configuration/triples/'
---

Epigraf implements a generator for triples in accordance with the Resource Description Framework.
You can generate triples by adding an extension such as `.ttl` to the path of an entity, for example to an article URL.
Table views can also be used to generate triples (i.e., the output of index endpoints) by adding an extension to the path.
Table views always generate a Hydra collection.

The generated triples are configured in the article, section and item types.
Both, templates for subject-predicate-object statements and namespaces are defined in the `triples` key.
The templates can contain combinations of literals with placeholders.
Placeholders are replaced by the entity contents.
For example, the placeholder `{iri}` in the article configuration is replaced by the IRI of the article.

Each triple template consists of the keys `subject`, `predicate` and `object`. An optional `comment` key
can be used to document the purpose of the triple.

Namespaces are configured as a key-value list with namespaces as keys and the URL prefixes as values.

## Article configuration

Example for the triple configuration in an article type:

``` plaintext
"triples" : {
  "base": "https://epigraf.inschriften.net/iri/",
  "namespaces": {
    "epi": "https://epigraf.inschriften.net/iri/",
    "schema":"https://schema.org/"
  },
  "templates": [
    {
      "comment": "The article has a title.",
      "subject": "epi:{iri}#",
      "predicate": "schema:title",
      "object": "{name}"
    },
    {
      "comment": "The article is about inscriptions.",
      "subject": "epi:{iri}#",
      "predicate": "schema:about",
      "object": "epi:{iri}#inscriptions"
    }
  ]
}
```

Example output in JSON-LD for an inscription located in the Schwerin Cathedral of St. Marien and St. Johannis:

``` plaintext
{
  "@context": {
    "epi": "http:\/\/epigraf.inschriften.net\/iri\/",
    "schema": "https:\/\/schema.org\/"
  },
  "@set": [
    {
      "@id": "epi:articles\/epi-article\/mv~1623#",
      "schema:title": "Sarg f\u00fcr Anna Sophia, Herzogin zu Mecklenburg",
      "schema:about": "epi:articles\/epi-article\/mv~1623#inscriptions"
    }
  ]
}
```

## Sections and items configuration

Triples can also be generated at the section and item levels.
Sections are organised in a hierarchy. For example, you can have an inscription section
that contains inscription parts, which in turn contain transcription sections.
To get data from upper levels in the section hierarchy, use the `parent` property.
In the following example, the subject refers to the IRI of an inscription section two levels above
and thus links the item of type `transcriptions` contained in a transcriptions section to the ancestor `inscription` section.

``` plaintext
"templates": [
  {
    "comment": "An inscription has text",
    "subject": "epi:{parent.parent.iri}",
    "predicate": "schema:text",
    "object": "{items.*[itemtype=transcriptions].content}"
  }
]
```

# Jobs configuration

Data generated from jobs, i.e. when exporting data via pipelines, may refer to the job. Example:

``` plaintext
{
  "triples": {
    "templates": [
      {
        "comment": "To which data feed does the article belong?",
        "subject": "epi:{iri}#dfi",
        "predicate": "schema:isPartOf",
        "object": "epi:{job.iri}"
      }
   ]
}
```

## Data types

The type of a value is determined when rendering the triples. The result
of an evaluated placeholder string may have one of the following types:

- Prefixed names: Prefixed names are values that start with a prefix defined in the
  namespace configuration. Example:
  `schema:title`.
- IRIs: If IRIs contain characters not allowed in prefixed names, the need to be expanded.
  For example, if the value starts with a namespace prefix but contains a slash, it is expanded to a full IRI.
  Expanded IRIs starting with the base path are converted to relative IRIs.
- Literals: Literal values are text values. They are always trimmed.
- Typed literals: To use other types than strings, in the template, add `^^` followed by a type
  such as `schema:Date`.

## Supported triple formats

The triple generator supports JSON-LD, XML/RDF and TTL serialisation by adding one of the extensions
`jsonld`, `rdf` or `ttl` to the URL path. Note the following pecularities of the target formats :

- In TTL format, the predicate `rdf:type` is replaced by `a`.
  As a prerequisite, you need to configure the namespace `rdf:http://www.w3.org/1999/02/22-rdf-syntax-ns#`.
- In RDF/XML format, `rdf:Description` elements are generated for all statements, with the subject in the `rdf:about` attribute.

