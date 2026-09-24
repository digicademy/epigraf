# Pipelines

Jede Datei im Hauptordner gehört zu einem Pipeline-Schritt. 
Eine Pipeline setzt sich aus einem oder mehreren dieser Schritte zusammen. 

Wenn in einem einzelnen Schritt weitere Stylesheets eingebunden werden, 
dann liegen diese in einem jeweils nach dem Hauptstylesheet benannten Ordner. 

Im Ordner `common` liegen Dateien, die in mehrere Stylesheets eingebunden werden (können).
Aktuell handelt es sich nur um die Datei epi44-switch.xsl, 
die von den Stylesheets trans0.xsl, trans1.xsl, trans2.xsl und word1.xsl aufgerufen wird.

## Bandexport

Der Bandexport führt ausgehend vom Rohdatenexport zunächst
über die Transformationsstufen 1 bis 3 zu einer kanonischen Form
des Bandes. Diese kanonische Form wird dann zu Word- und DIO-Formaten
weitergeführt.

- trans1.xsl: Strukturierung
  - Hauptelemente (project, volume, catalog, indices und manage_lists) werden angelegt.
  - Abschnitte (sections) werden hierarchisch verschachtelt.
  - In den Abschnitten des Bandartikels werden Attribute ergänzt.
  - Der Katalog wird nach vier Kriterien (datierung, standort, träger, signatur) sortiert.
  - Die Indizes (index) werden in Register (indices) und Steuerlisten (manage_lists) aufgeteilt
  - Der Literaturindex wird neu erstellt (nur tatsächlich verwendete Titel) 
   
- trans2.xsl: Vorbereitung.
  - Der Katalogartikel wird nach drei Kriterien sortiert.
  - Die Abteilungen eines Bandes werden neu angelegt.
  - Die Inhalte aus den Textbausteinen, dem Katalog und den Registern werden zusammengestellt.
    
- trans3.xsl: Vorbereitung.


### Das Literaturverzeichnis

Schritt 1:  
- Wird in di-trans1-index-literature.xsl auf verwendete Titel (rec_lit, property) eingeschränkt.
  Berücksichtigt werden auch Nachweise zu Wappen.
- Wird in di-trans1-indices-commons.xsl verschachtelt.
  
Schritt 2:  
- Wird in di-trans2-index-literature.xsl sortiert, ishidden-Titel werden ausgeblendet, 
  das Ergebnis wird ohne Fundstellen (also nur die Titel) in ein `<literatures>`-Element 
  mit Überschrift eingebettet.

Schritt 3:  
- Wird in di-trans3-indices.xslx im Wesentlichen einfach übernommen.

### Die Wappen (Wappenregister, Wappenbeschreibungsanhang)

Schritt 1:  
- Wird in di-trans1-indices weitgehend unverändert übernommen

Schritt 2:  
- In di-trans2-index-heraldry.xls werden 
  die Platzhalternummern in geschweiften Klammern durch fortlaufende Nummern ersetzt,
  die identifizierten Wappen (Name beginnt nicht mit Fragezeichen) in das Register übernommen, 
  die Namen werden zu Lemmata, die Fundstellen werden übernommen.
- Wenn die Exportoption zur Beschreibung nicht identifizierter Wappen ausgewählt ist,
  werden das content-Feld bzw. falls leer das elements-Feld zu Lemmata.
  In jeden Fall werden die Fundstellen übernommen.  
- Es wird in di-trans2-index-heraldry.xls ein Wappenbeschreibungsanhang gebildet, 
  der je nach Aufbau des Bandartikels im Register oder als Extranhang ausgegeben werden kann.
  Übernommen werden nur identifizierte Wappen, die nicht mit "Ausblenden" gekennzeichnet sind
  und die Inhalte in content oder source_from haben.
  Der Wappenbeschreibungsanhang enthält je Wappen die Inhalte der 
  Felder name (wird zu lemma), content (wird zu shield) und source_from (wird zu biblio).
  Das Feld elements (wird zu crest) wird übersprungen. 

Schritt 3:
- Marken und Wappen werden in di-trans3-indices.xls zu einem Register zusammengeführt.
- Wappenbeschreibungsanhang wird sortiert und übernommen.
  Der Inhalt des Feldes elements (wird zu crest), das heißt Beschreibung des Oberwappens, 
  wird ausgeblendet.

### Die Marken (Wappenregister, Markenseite)

Schritt 1:  
Es werden weitere Attribute gebildet, die für das Wappenregister wichtig sind,
insbesondere das Attribut brandtype (darin das Lemma des Markentyps).

Schritt 2:  
Es wird ein Register mit identifizierten Marken und @brandtype='Hausmarken' gebildet,
das in Schritt 3 in das Wappenregister eingespielt wird.

Die Marken werden nach Kategorie gruppiert. 

Schritt 3:  
Das Markenregister wird in di-trans3-indices.xls das Wappenregister eingespielt.


## Pipeline für Word:

- trans1.xsl: Vorbereitung.
- trans2.xsl: Vorbereitung.
- trans3.xsl: Vorbereitung
- word1.xsl:  Ausgabe in Word ohne Markenbilder (Saxon-Parser)
- word2.xls: Ergänzung der Markenbilder (PHP-Standardparser)

## Pipeline für DIO

- trans1.xsl: Vorbereitung 
- trans2.xsl: Vorbereitung 
- trans3.xsl: Vorbereitung 
- trans-dio: Ergebnis ist die Importdatei für das DIO-Typo3

## Weitere Pipelines

- artikeltabelle.xsl; Ausgabe einer Artikeltabelle
- signaturenliste.xsl: Liste der Signaturen zur Vorbereitung des Abbildungsteils
- image-header.xsl: Erzeugen der Metadaten für Bilder (zur Weiterverarbeitung mit Adobe Bridge)

# Testdateien

Im Ordner tests liegen Dateien und Szenarien zum Testen der Transformationen 
Die Tests werden in der GitLab CI/CD-Pipeline ausgeführt.

Jeder Ordner enthält einen Rohdatenexport (book.xml), 
eine readme.md mit Beschreibung des Falls 
und das Ergebnis der einzelnen Transformationsstufen.
Diese bilden die Referenz für die Tests.

Werden die Stylesheets verändert, sollten alle Transformation ausgeführt,
das Ergebnis geprüft und somit die Referenzen aktualisiert werden.

# Private

Der private-Ordner steht nicht unter Versionsverwaltung und eignet sich zur Ablage 
von eigenen Entwicklungsdateien
