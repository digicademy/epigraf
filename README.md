<img align="right" width="48" height="48" src="/docs/assets/img/epigraf-icon.png" alt="Epigraf logo">

# Epigraf

Epigraf is a research platform for collecting, annotating, linking and publishing textual and multimodal data.
The data model supports research databases ranging from epistolary editions to social media corpora.
Epigraf is currently used primarily for editing epigraphic data - inscriptions in connection with the objects to which they are attached.
It includes a publication system for various document formats such as Word or TEI, structured JSON, XML and CSV data, and triples in TTL, JSON-LD and RDF/XML.

You want to try it out? Contact us for a test account on one of our machines.
See the [user documentation](https://epigraf.inschriften.net/help)
and the [developer documentation](/docs/index.md) for further information.

![Epigraf use cases](/docs/assets/img/epigraf-use-cases.png)

![Edit with Epigraf](/docs/assets/img/edit-with-epigraf_playground~394.png)

## How to run Epigraf

1. Fire up the Apache Webserver, the PHP container, and the database container
   ```
   docker compose up -d
   ```

2. Install Epigraf
   ```
   docker exec epi_php composer install
   docker exec epi_php bin/cake cache clear_all
   ```

3. Init the database and add an admin user
   (with role, password, and access token set to `admin`)
   ```
   docker exec epi_php bin/cake database init --drop
   docker exec epi_php bin/cake user add admin admin admin admin
   ```

4. Create an example project database from a preset
   ```
   docker exec epi_php bin/cake database init --database epi_example --preset movies
   ```

5. Login to Epigraf at http://localhost with username and password `admin`.


See the [docker](docker) folder for more options.

## Authors and Citation
Epigraf is developed by the
[Digital Academy of Sciences and Literature | Mainz](https://www.adwmainz.de/digitalitaet/digitale-akademie.html)
and the research group
[Digital Media & Computational Methods at the University of Münster](https://www.uni-muenster.de/Kowi/institut/arbeitsbereiche/digital-media-computational-methods.shtml), Germany.

**Citation**
Jünger, J., Hertkorn, G., Gärtner, C.,
Herold, J., Knispel, J., Kopp, M.,Kotthoff, H., Lentge, F., Michel, M., Syring, W-D. (2024).
Epigraf: a research platform for collecting, annotating, linking and publishing multimodal text data data.
Version 5.0. https://github.com/digicademy/epigraf
