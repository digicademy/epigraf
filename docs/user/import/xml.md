---
title: Import XML files
permalink: '/user/import/xml/'
---

# Structure of XMl import files

Articles including project data, categories, and all other related data can be imported from XML files.
For orientation purposes, we recommend downloading an existing article via the "XML" button
or a set of articles with the "Export" button using the default data pipeline.
To import data from TEI, ODT, or DOCX files, you should transform them to XML files following the Relational Article Model (RAM) as implemented by Epigraf using XSLT stylesheets.

Each article is contained in an `<article>` element. Child elements such as `<name>`correspond to the fields in the database.

``` plaintext
<?xml version="1.0" encoding="UTF-8"?>
<epi>
  <article id="articles/object/anima~ani562ag">
     <name>Tomb of the papal consultant</name>
     <signature>ani562ag</signature>
  </article>
</epi>
```

This pattern applies to all entities in the [Relational Article Model (RAM)](../coreconcepts/model). The import file can simply consist of multiple `<project>`, `<article>`, `<section>`, `<item>`, `<footnote>`, `<link>` and `<property>` elements one after another. The element order is irrelevant.

# Linking entities to each other

Each entity should have an IRI path, a database ID or a temporary ID in its id attribute. To link entities, those IRIs or IDs can be used in the corresponding fields. For example, to link a section to an article, use the `articles_id` attribute of the `<section>` element with the same value as in the `id` attribute of the `<article>` element.

Alternatively, you can nest elements. For eaxample, an `<article>` element can contain a `<project>` element and multiple `<section>` elements.Nesting should follow the [Relational Article Model (RAM)](../coreconcepts/model). Articles are assigned to a project and contain sections. Sections contain items. Items can refer to properties. Entities in child elements  are always linked to the entity represented by the parent element. You should add an `id` attribute containing an IRI path to each entity element, but you do not need to use IDs for binding nested entitites to their parents. Multiple children may be nested in the container elements `<sections>`, `<items>`, `<footnotes>`, and `<links>`. In nested structures it is important to first provide the elements corresponding to the entity's fields and add nested data to the end of a parent element.

The following example contains a full article with all associated entities. In the example, a project entity and a location property are also created (or updated if they exist). If you omit the `<property>` element and just use the `<properties_id>` element, the category referenced by the property ID must already exist before the import; it will not be created or updated automatically.

In the example, a hierarchical structure is created between the sections. The hierarchical structure does not result from the elements being nested within each other, but from the attributes `id` and `parent_id`.

Text can be annotated with XML elements if configured accordingly. In the example, the annotations `<z>` for line breaks and `<abr>` for abbreviations are used in the transcription field. The example lacks footnotes or links from an annotation to a property. This would require an `id` attribute for the elements in the transcription field and corresponding `<link>` or `<footnote>` elements within the article.

``` plaintext
<?xml version="1.0" encoding="UTF-8"?>
<epi>
  <article id="articles/object/anima~ani562ag">
     <name>Tomb of the papal consultant</name>
     <signature>ani562ag</signature>

     <project id="projects/epi/anima">
        <name>S. Maria dell'Anima</name>
        <signature>anima</shortname>
     </project>

     <sections>
        <section id="sections/locations/anima~ani562ag~locations">
           <name>Locations</sectionname>
           <items>
              <item id="items/locations/anima~ani562ag~locations~1">
                 <properties_id>properties/locations/santamariadellanima</properties_id>
                 <property id="properties/locations/santamariadellanima">
                    <lemma>Santa Maria dell'Anima</lemma>
                 </property>
              </item>
           </items>
        </section>
        <section id="sections/text/anima~ani562ag~description">
           <name>Description</name>
           <items>
              <item id="items/text/anima~ani562ag~description~1">
                 <content>Tomb of Agustin Gyntzer, papal consultant and advisor to the Duke of Bavaria.</content>
              </item>
           </items>
        </section>
        <section id="sections/inscription/anima~ani562ag~inscription~1">
           <name>A</name>
           <number>1</number>
        </section>
        <section id="sections/inscriptionpart/anima~ani562ag~inscriptionpart~1"
                 parent_id="sections/inscription/anima~ani562ag~inscription~1">
           <name>1</name>
           <number>1</number>
        </section>
        <section id="sections/inscriptiontext/anima~ani562ag~inscriptiontext~1"
                 parent_id="sections/inscriptionpart/anima~ani562ag~inscriptionpart~1">
           <name>1</name>
           <number>1</number>
           <items>
              <item id="items/transcriptions/anima~ani562ag~transcriptions~1">
                 <content>IESV CHRISTO <z/> RESVRRECTIONIS <z/> ET SALVTIS AVCTORI <z/> AVGVSTINO GYNTZERO <z/> COLMARIENSI IVRIS <z/> CIVILIS PONTIFICIIQ<abr>VE</abr>
                    <z/> CONSVLTO ALBERTI <z/> BAVARIAE DVCIS CONSI<z/>LIARIO RELIGIONIS <z/> INTEGRITATE MORVM <z/> PROBITATE <z/> AC SVAVITATE PRAESTANTI <z/> OBIIT DECIMO CALEN<abr>DAE</abr>
                    <z/> OCTOB<abr>RIS</abr> MDLXII</content>
                 <source_from>According to Galletti.</source_from>
              </item>
           </items>
        </section>
     </sections>
  </article>
</epi>
```

Note that all types used in the example have to be configured in the database. Otherwise the imported data will not be rendered correctly:

- Project types: epi
- Article types: object
- Section types: locations, text, inscription, inscriptionpart, inscriptiontext
- Item types: locations, text, transcriptions
- Property types: locations
- Links types: z, abr


# Data formats

Field values are imported directly from the XML file, with protected characters being masked. This means that a quotation mark is imported as `&quot;`. To import JSON data, you need literal quotes. To import unmasked quotation marks, set the format parameter of a field element to "json". Example:

``` plaintext
<content format="json">
  {
    "lat": 52.236477,
    "lng": 12.97412
  }
</content>
```
