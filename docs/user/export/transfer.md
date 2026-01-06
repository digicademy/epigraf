---
title: Transfer Entities between Databases
permalink: '/user/export/transfer/'
---


Entities can be transferred between different databases on a server using the **Transfer** button.

IRIs are used to decide whether an existing entity is updated or a new entity is created in the target database.
For example, if a category (=property) with the same IRI exists both on the source and the target database,
the lemma, name and other fields of the category are updated in the existing entity.

IRIs are based on the source table (e.g. articles, sections, properties ...),
the entity type (configured in the types, stored in fields such as articletype, sectiontype, propertytype...)
and an IRI fragment (stored in the norm_iri field). In case the IRI fragment is not defined yet,
a virtual IRI fragment is generated. The virtual IRI consists of the database name and the entity ID separated by a tilde (e.g. mv~123).
The virtual IRI is stored in the target database in the norm_iri field. This makes sure that a record transferred to a new database
is identified and will be updated in following transfer operations, as long as the source IRI did not change.




