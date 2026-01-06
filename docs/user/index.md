---
# Epigraf 5.0
#
# @author     Epigraf Team
# @contact    jakob.juenger@adwmainz.de
# @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
#
title: Welcome to Epigraf!
permalink: '/user/'
toc: false
---

Epigraf is a research platform for collecting, annotating, linking and publishing multimodal text data.
The data model supports research databases ranging from epistolary editions to social media corpora.
Epigraf is currently used primarily for editing epigraphic data - inscriptions in connection with the objects to which they are attached.
It includes a publication system for various document formats such as Word or TEI, structured JSON, XML and CSV data,
and triples in TTL, JSON-LD and RDF/XML.

Epigraf's modules support the entire research data lifecycle ([Higgins 2008](https://doi.org/10.2218/ijdc.v3i1.48)):

![Epigraf Modules](user/assets/img/modules.png)

- **Collection**: Data sets can be both edited in the application as well as imported from files. The core concepts of Epigraf are articles and properties used in articles.
- **Annotation**: Every article is composed of sections, that can be flexibly combined, containing text and all relevant metadata (descriptions, comments, categorizations via vocabularies) as well as embedded files or images. A configurable toolbar is available for the annotation of texts.
- **Linking**: In order to publish data as Linked Open Data according to the FAIR principles([Wilkinson et al. 2016](https://doi.org/10.1038/sdata.2016.18)), authority data identifiers (IRIs; [W3C 2014](https://www.w3.org/TR/rdf11-concepts/)) can be created for each article and category. This allows data sets to be reconciled between different databases. The data model is compatible with the Resource Description Framework ([W3C 2014](https://www.w3.org/TR/rdf11-concepts/))  so that the relationships between data points can be modeled in the form of statements.
- **Analysis**: A faceted full-text search is available for the entire database. The vocabularies used for indexing can be used to dive into the data.
- **Publication**: Camera-ready documents, for example in Word format or in standardized document formats such as ([TEI 2022](https://tei-c.org/); [Elliott et al. 2020](https://epidoc.stoa.org/gl/latest/)), can be generated using a pipeline system and XSL stylesheets. If required, data sets can be made publicly available in the web interface. A programming interface makes the data available in CSV, JSON, or XML format. For interacting with the data, R and Python packages are under developement.
- **Collaboration**: Epigraf supports working collaboratively on a database. For the coordination of multiple workplaces, wikis and a file repository are used.

Epigraf emerged from the inter-academic edition project "The German Inscriptions of the Middle Ages and the Early Modern Period".
It is used in the nine inscription research centers of the six participating academies of sciences.
The application is being developed by the Digital Academy of Sciences and Literature \| Mainz and the Digital Media & Computational Methods Research Group at the University Münster.

Epigraf is an open-source project that is under active development.
The [developer documentation](https://digicademy.github.io/epigraf/devel/) on GitHub
provides an overview of the tech stack and guides you through the installation process.
The source code is released on GitHub following major updates.
We welcome collaborative development and look forward to hearing from you.

