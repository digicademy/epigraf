# Pipelines

Jede Datei im Hauptordner gehört zu einem Pipeline-Schritt. 
Eine Pipeline setzt sich aus einem oder mehreren dieser Schritte zusammen. 

Wenn in einem einzelnen Schritt weitere Stylesheets eingebunden werden, 
dann liegen diese in einem jeweils nach dem Hauptstylesheet benannten Ordner. 

Im Ordner `common` liegen Dateien, die in mehrere Stylesheets eingebunden werden (können).
Aktuell handelt es sich nur um die Datei epi44-switch.xsl, die von den Stylesheets trans0.xsl, trans1.xsl, trans2.xsl und word1.xsl aufgerufen wird.

## Pipeline für Word:

- trans0.xsl: Vorbereitung
- trans1.xsl: Vorbereitung
- trans2.xsl: Vorbereitung
- word1.xsl:  Ausgabe in Word ohne Markenbilder (Saxon-Parser)
- word2.xls: Ergänzung der Markenbilder (PHP-Standardparser)

## Pipeline für DIO

- trans0.xsl: Vorbereitung 
- trans1.xsl: Vorbereitung 
- trans2.xsl: Vorbereitung 
- trans-dio (*muss noch ergänzt werden*)


## Weitere Pipelines

- artikeltabelle.xsl; Ausgabe einer Artikeltabelle
- signaturenliste.xsl: Liste der Signaturen zur Vorbereitung des Abbildungsteils
- image-header.xsl: Erzeugen der Metadaten für Bilder (zur Weiterverarbeitung mit Adobe Bridge)

# Testdateien

Im Ordner tests liegen Dateien für das Testsystem.

- tests/source XML-Dateien aus Epigraf (Rohdaten)
- tests/target Transformierte XML-Dateien (Testdateien)

# Private

Der private-Ordner steht nicht unter Versionsverwaltung und eignet sich zur Ablage 
von eigenen Entwicklungsdateien
