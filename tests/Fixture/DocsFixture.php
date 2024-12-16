<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * DocsFixture
 */
class DocsFixture extends TestFixture
{
    /**
     * Database connection
     *
     * @var string
     */
    public $connection = 'test';


    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 47,
                'deleted' => 0,
                'version_id' => null,
                'sortkey' => '',
                'norm_iri' => 'window-uebersicht',
                'created' => '2017-11-26 21:20:09',
                'modified' => '2021-08-04 11:21:19',
                'published' => 0, 'menu'=>1,
                'segment' => 'help',
                'name' => 'Startfenster',
                'category' => 'Handbuch',
                'content' => '[zur Übersicht ...](/docs/view/73)



__Gliederung:__

* [Aufbau des Startfensters](#seitenaufbau)
* [Menüleiste](#menue) (1)
* [Statusanzeige: Datenbank](#statusDb) (2)
* [Dropdown-Felder](#dropdown)
     * ["Ebene"](#dropdown_ebene) (3)
     * ["Projekt"](#dropdown_projekt)(4)
* [Tabelle der Artikel oder Projekte](#liste)
    * [Artikel und Projekte](#artikel_projekte)
    * [Tabelle formatieren](#formatieren)
     * [Spalten wählen](#spalten_waehlen)
     * [Spaltenbreite](#spaltenbreite)
     * [Spalten anordnen](#spalten_anordnen)
    * [Tabelle sortieren](#sortieren)
    * [Tabelle aktualisieren](#aktualisieren)
* [Werkzeugleiste](#werkzeug) (5)
* [Statuszeile](#statusZ) (6)
    * [Artikel durch Eintippen einer Zeichenfolge `Finden`](#finden)


#Aufbau des Startfensters {#seitenaufbau}
[zurück ...](/docs/view/47)
![Startseite](/files/download?root=shared&path=help%2Fscreenshots&filename=startseite.png)

Beim Programmstart erscheint das Startfenster von Epigraf.

Die Hauptfläche nimmt eine Tabelle ein, die – entsprechend der Auswahl im [Dropdown-Feld "Ebene"](#dropdown) – [Artikel oder Projekte](/docs/view/90) einer Datenbank aufführt. Davon oben, links und unten sind eine Reihe von Anzeigen und Funktionen angeordnet.

(1) Am oberen Rand erscheint die Menüleiste.

(2) Darunter wird der Name der aktiven Datenbank angezeigt.

Hiervon rechts sind zwei Dropdown-Fenster platziert:
(3) Unter `Ebene` kann man wählen, ob in der Tabelle Artikel oder Projekte angezeigt werden.
(4) Unter `Projekt` kann man die Auswahl der Artikel auf eines der aufgelisteten Projekte einschränken oder die Artikel aller Projekte anzeigen lassen.

(5) Links neben der Tabelle befindet sich die Werkzeugleiste.

(6) Unter der Tabelle sieht man eine Statusanzeige (Name der Datenbank, Bearbeiter, Anzahl der Datensätze/Artikel, zum `Finden` eingegebene Zeichenfolge)


#Menüleiste {#menue}
<a href="/docs/view/47">zurück ...</a>
<br/>
![Menüleiste](/files/download?root=shared&path=help%2Fscreenshots&filename=Menleiste.PNG)


Die Menüleiste am oberen Rand des Startfensters bietet in acht Dropout-Menüs den Zugriff auf zentrale Bereiche und Funktionen des Programms.

##__Menü "Datenbank"__ {#menue_datenbank}

<table>
 <colgroup> <col width="300"> <col > </colgroup>
<tr>
<td>
<img src="/files/download?root=shared&path=help%2Fscreenshots&filename=Datenbank.png" />
</td>
<td>
<ol>
<li>Im oberen Bereich werden (ensprechend den in der <a href="/docs/view/70">Konfiguration</a> vorgenommen Einträgen) die auf dem PC mit EpigrafDesktop verknüpften <b>Datenbanken</b> angezeigt. Die Anzeige bietet die Möglichkeit, durch Anklicken eine der aufgeführten Datenbanken aufzurufen bzw. zwischen den Datenbanken zu wechseln. Außer bei System-Administratoren wird in der Regel nur eine Datenbank, die der jeweiligen Arbeitsstelle, angezeigt. Wenn ein Bearbeiter über die nötigen Zugangsdaten verfügt, kann er auch auf mehrere Datenbanken zugreifen</li>
<li><b>Aktualisieren</b> (F5): die Artikel-Tabelle wird neu aufgebaut.</li>
<li><a href="/docs/view/48"><b>Bearbeiter wechseln</b></a></li>
<li><a href="/docs/view/74"><b>Backup anlegen</b></a></li>
<li><a href="/docs/view/75"><b>Sperren aufheben</b></a></li>
<li><a href="/docs/view/70"><b>Konfiguration</b></a></li>
</ol>
</td>
</tr>
</table>

##__Menü "Globale Daten"__ {#globaledaten}
Dieses Menü bietet Zugriff auf zentrale Listen und Ansichten.

<table>
 <colgroup> <col width="300"> <col > </colgroup>
<tr>
<td>
<img src="/files/download?root=shared&path=help%2Fscreenshots&filename=GlobaleDaten.png">
</td>
<td>
<ol>
<li><strong>Band</strong>. Beim Anklicken öffnet sich das <a href="/docs/view/81">Band-Fenster</a>, in dem (mit Ausnahme von Katalog und Registern) die Bestandteile eines Inschriftenbandes (Titelei, Vorwort, Einleitung usw.) angelegt werden.
</li>
<li><strong>Nachschlagelisten</strong>. Das Untermenü bietet Zugriff auf die dort angeführten <a href="/docs/view/85">Register und ähnliche Listen</a>.
</li>
<li><strong>Formate</strong>. Das Untermenü erlaubt den Zugriff auf sonstige, insbesondere mit dem Datenexport verknüpften <a href="/docs/view/85">Listen</a>.
</li>
<li><strong>Datenbanken</strong>. Über das Untermenü können die dort aufgeführten Datenbestände bearbeitet werden:
<ul>
<li><a href="/docs/view/82">Literatur</a></li>
<li><a href="/docs/view/83">Marken</a></li>
<li><a href="/docs/view/84">Wappen</a></li>
</ul>
</li>
<li><strong>Dateien</strong>. Öffnet die in der <a href="/docs/view/70">Datenbank-Konfiguration</a> angegebenen und im Untermenü angezeigten Abbildungs-Verzeichnisse:
<ul>
<li>Bilderverzeichnis</li>
<li>Wappenverzeichnis</li>
<li>Markenverzeichnis</li>
</ul>
</li>
</ol>
</td>
</tr>
</table>


##__Menü "Bearbeiten"__ {#bearbeiten}
Dieses Menü korrespondiert mit den oberen drei Buttons der [Werkzeugleiste](#werkzeug). Entsprechend der im [Dropdown-Feld "Ebene"](#dropdown) vorgenommen Auswahl können Projekte oder Artikel angelegt, bearbeitet oder gelöscht werden.


<table>
 <colgroup> <col width="300"> <col > </colgroup>
<tr>
<td>
<img src="/files/download?root=shared&path=help%2Fscreenshots&filename=Bearbeiten.png" />
</td>
<td>
<ol>
<li><strong>Neuer Artikel</strong> oder <strong>Neues Projekt</strong> (Strg + N)</li>
<li><strong>Artikel bearbeiten</strong> oder <strong>Projekt bearbeiten</strong>. Der markierte Artikel oder das markierte Projekt wird geöffnet. Dasselbe bewirken die Betätigung der ENTER-Taste oder ein Doppelklick mit der Maus.</li>
<li><strong>Artikel löschen</strong> oder <strong>Projekt löschen</strong></li>
<li><strong>Datensatz nach ID öffnen</strong>. Diese Funktion dient Systementwicklern bei SQL-Abfragen.</li>
</ol>
</td>
</tr>
</table>


##__Menü "Export"__ {#menue_export}
Hier stehen zwei [Exportpipelines](/docs/view/87) für die  Datenausgabe in Textdokumente zur Verfügung. Alternativ können die angegebenen Tastenkürzel ([Shortcuts](/docs/view/93)) benutzt werden:

* __Artikel exportieren__ (F6): Export einzelner oder mehrerer Artikel als Text-Dokument über [EpigrafWeb](/docs/view/87), optionsweise auch mit Ausgabe der Register.
* __Band exportieren__ (F7): Export ganzer Inschriftenbände als Text-Dokument über [EpigrafWeb](/docs/view/87), optionsweise beschränkbar auf einzelne Bandabschnitte.

##__Menü "Ansicht"__  {#menue_ansicht}

<table>
 <colgroup> <col width="300"> <col > </colgroup>
<tr>
<td>
<img src="/files/download?root=shared&path=help%2Fscreenshots&filename=Ansicht.png" />
</td>
<td>
<ol>
<li><a href="/docs/view/80"><strong>Register</strong></a> und</li>
<li><a href="/docs/view/79"><strong>Filter</strong></a>: korrspondieren mit dem vierten und fünften Button der <a href="#werkzeug">Werkzeugleiste</a></li>
<li><strong>Projekte</strong>: korrspondiert mit der Dropdown-Liste "Projekte" und erlaubt die Auswahl einzelner Projekt.</li>
<li><strong>Letzte Änderung</strong>: schränkt die Artikel-Auswahl optionsweise auf die zuletzt bearbeiteten zehn, fünfzig oder hundert Artikel ein und erlaubt es, diese Einschränkung wieder rückgängig zu machen.</li>
<li><strong>Bearbeiter</strong>: schränkt die Artikel-Auswahl auf diejenigen ein, die von einem bestimmten Bearbeiter angelegt oder zuletzt bearbeitet wurden.</li>
<li><strong>Alles anzeigen</strong>: lässt die Artikel aller in der Datenbank vorhandenen Projekte auflisten.</li>
<li><strong>Nächstes Finden</strong> (F3): Diese Funktion bewirkt, dass nach dem Eintippen einer bestimmten Zeichenfolge (die in der <a href="#statusZ">Statuszeile</a> am Fuß des Startfensters angezeigt wird) der Fokus zum jeweils nächsten Treffer in der Artikel-Tabelle springt.</li>
<li><strong>Vorheriges Finden</strong> (Shift+F3) bewirkt dasselbe jedoch in umgekehrter Reihenfolge.</li>
</ol>
</td>
</tr>
</table>


##__Menü "Spalten"__  {#menue_spalten}
Zeigt alle für die [Artikel-Tabelle](#liste) verfügbaren Spalten an und erlaubt, sie ein- oder auszublenden.

##__Menü "Dateiablage"__  {#menue_dateiablag}
<table> <colgroup> <col> </colgroup> <tbody> <tr> <td style="padding:0"> <p>Zeigt die auf <a href="/docs/view/86">EpigrafWeb</a> vorhandenen Verzeichnisse zur Ablage von Dateien (<a href="/docs/view/78">Hilfsmittel</a>) an und leitet dorthin weiter. </p></td> </tr> </tbody> </table>

##__Menü "Notizen"__  {#menue_notizen}
<table> <colgroup> <col> </colgroup> <tbody> <tr> <p> Zeigt die auf <a href="/docs/view/86">EpigrafWeb</a> abgelegten <a href="/docs/view/77">Notizen</a> an und leitet dorthin weiter. </p> </tr> </tbody> </table>


#Statusanzeige {#statusDb}
<a href="/docs/view/47">zurück ...</a>
Hier wird die Bezeichnung der aktuell aufgerufenen Datenbank angezeigt.

#Dropdown-Felder {#dropdown}
<a href="/docs/view/47">zurück ...</a>

![Dropdown](/files/download?root=shared&path=help%2Fscreenshots&filename=Ebene.png)

Mit den beiden Dropdown-Feldern rechts oberhalb der Tabelle erfolgt die Auswahl der Datenbestände, die in der Tabelle angezeigt werden soll.


* __Ebene__: <a id="dropdown_ebene"></a> Hier kann gewählt weden, ob in der Tabelle Artikel, Projekte oder Inschriften angezeigt werden. Wählt man bspw. "Inschriften" aus, erscheint dann in der Tabelle ein Auflistung sämtlicher Inschriften des gewählten Projekts.
* __Projekt__: <a id="dropdwon_projekt"></a> Wenn in der Tabelle Artikel angezeigt werden, kann die Auswahl auf die Artikel eines einzelnen Projekts eingeschränkt werden oder es können die Artikel aller Projekte angezeigt werden.

#Tabelle der Artikel oder Projekte {#liste}
[zurück ...](/docs/view/47)


##Artikel und Projekte {#artikel_projekte}
Die Grundeinheiten einer Inschriften-Datenbank sind der "Artikel" und das "Projekt". Der Artikel bildet einen Inschriftenträger ab, das Projekt entspricht der übergeordneten Editionseinheit, in der Regel dem geografischen Raum eines Inschriftenbandes oder einer Online-Edition. Dem gemäß wird das Arbeitsgebiet und der sich aufbauende Datenbestand einer Inschriften-Forschungsstelle in Projekte unterteilt, denen Artikel zugeordnet werden. Artikel außerhalb von Projekten sollten nicht vorkommen. Um in der Datenbank ein Projekt anzulegen oder zu bearbeiten, wählt man im [Dropdown-Fenster "Ebene"](#dropdown) oberhalb der Tabelle den Eintrag `Projekte`. Anschließend kann man über die [Werkzeugleiste](#werkzeug) oder das Menü ["Bearbeiten"](#bearbeiten) Projekte [anlegen, bearbeiten oder löschen](/docs/view/90).

##Tabelle formatieren {#formatieren}

* <a id="spalten_waehlen"></a> __Spalten wählen__: Die in der Tabelle anzuzeigenden Spalten können im Menü "Spalten" ausgewählt oder wieder abgewählt werden. Da die Anzahl der gewählten Spalten sich auf die Dauer des Aufbaus der Tabelle auswirkt, sollten vor allem bei umfangreichen Projekten oder wenn die Artikel aller Projekte ausgewählt wurden, nur die tatsächlich erforderlichen Spalten aufgerufen werden.
    * __Spalte "Nr"__: Am linke Rand der Tabelle sollte die Spalte "Nr" plaziert werden. Sie dient der Orientierung in der aktuellen Tabelle. Die angezeigten Nummern sind nicht mit den Artikelnummern identisch. Sie zeigen lediglich die der gewählten Sortierung entsprechende Reihenfolge an.
    * __Spalte "Datensatzstatus"__: Diese Spalte sollte an zweiter Stelle plaziert werden. Sie zeigt durch eine Lampe an, ob der ebtsprechende Artikel geöfffnet bzw. [gesperrt](/docs/view/75) ist.
* <a id="spaltenbreite"></a> __Spaltenbreite__: Die Breite jeder Spalte kann im Spaltenkopf angepasst werden. Dazu richtet man im Spaltenkopf den Mausfokus auf die Trennlinie zur rechts folgenden Spalte. Wenn sich der Mauszeiger in ein Kreuz verwandelt, kann man mit einem Rechtsklick die Linie ergreifen und die Spalte auf- oder zuziehen.
* <a id="spalten_anordnen"></a> __Spalten anordnen__: Um die Anordnung der Spalten zu ändern, klickt man mit der rechten Maustaste auf einen Spaltenkopf, hält die Taste und zieht den Spaltenkopf nach rechts oder links.

##Tabelle sortieren {#sortieren}

* Die Tabelle lässt sich nach jeder Spalte auf oder absteigend sortieren. Dazu klickt man mit der rechten Maustaste auf den entsprechenden Spaltenkopf. Um die Sortierfolge umzukehren, muss man erneut klicken. Im Spaltenkopf zeigt ein nach oben gerichtetes Dreieck aufsteigende Sortierfolge an, ein nach unten gerichtetes Dreieck absteigende.
* Die Datierungsspalten sind mit einer speziellen Funktion ausgestattet, die eine nach den DI-Richtlinien korrekte chronolgisch Reihenfolge erzeugt. Durch die Sortierung nach der Spalte "Objektdatierung" kann in der Tabelle eine chronologisch aufsteigende Reihenfolge der Artikel bewirkt werden.

##Tabelle aktualisieren {#aktualisieren}

* Mittels der Funktionstaste F5, über das [Menü "Datenbank"](#menue_datenbank), oder mit dem entsprechenden Button der [Werkzeugleiste](#werkzeug) kann man die Tabelle der Artikel oder Projekte aktualisieren.
* Nach  erfolgtem Befehl wird die Tabelle aus den aktuellen Daten neu aufgebaut. Solange die Tabelle im Aufbau ist, erscheint in der [Statuszeile](#statusZ) unterhalb der Tabelle am rechten Rand die Anzeige "Vorladen ...". Erst wenn diese Anzeige verschwunden ist, kann die Tabelle korrekt sortiert und mit der Funktion [`Finden`](#finden) durchsucht werden.

#Werkzeugleiste {#werkzeug}
[zurück ...](/docs/view/47)
<img src="/files/download?root=shared&path=help%2Fscreenshots&filename=werkzeugleiste.png" align="left" style="padding-right:100px">

1. `Neuer Artikel`  (Strg + N): ein neuer Artikel wird angelegt
2. `Artikel öffnen`: der in der Tabelle markierte Artikel wird geöffnet
3. `Artikel löschen`: die in der Tabelle markierten Artikel werden gelöscht
4. `Aktualisiseren`  (F5): die Tabelle der Artikel wird mit den aktuellen Daten neu aufgebaut
5. `Filter`: Tabelle der Artikel nach Inhalten spaltengemäß [filtern](/docs/view/79)
6. `Register`: ermöglicht eine kombinierte Recherche in den [Registern](/docs/view/80)
7. `Notizen`: leitet weiter zu den [Notizen](/docs/view/77) auf EpigrafWeb
8. `Hilfsmittel`: leitet weiter zu den [Hilfsmitteln](/docs/view/78) auf EpigrafWeb

<br/><br/><br/>

#Statuszeile {#statusZ}
[zurück ...](/docs/view/47)

In der Statuszeile unterhalb der Tabelle erscheinen verschiedene Anzeigen:

* __Datenbank__: Name der gewählten [Datenbank](#menue_datenbank).
* __Bearbeiter__: Name des aktuellen [Bearbeiters](/docs/view/48).
* __Datensätze__: Anzahl der in der Tabelle angezeigten Artikel oder Projekte.
* __Finden__: <a id="finden"></a>Wenn man auf der Startseite mit der Tastatur eine Zeichenfolge eintippt, springt der Fokus innerhalb der Tabelle auf die nächste Artikel-Zeile, die in der Spalte "Signatur" oder "Titel" die entsprechende Zeichenfolge aufweist.
    * Die Zeichenfolge wird in der Statuszeile unterhalb der Tabelle hinter "Finden" angezeigt.
    * Mit der Funktionstaste F3 springt der Fokus auf den jeweils nächsten Treffer, mit Shift+F3 zum jeweils vorhergehenden.
* __Vorladen__: Solange die Liste aus der Datenbank geladen wird, erscheint am rechten Rand der Statuszeile "Vorladen ...". Erst wenn diese Anzeige verschwunden ist, sind alle Artikel geladen und die Tabelle kann [sortiert](#sortieren) und mit der Funktion `Finden` durchsucht werden.

[zurück ...](/docs/view/47)',
                'format' => 'markdown',
            ],
            [
                'id' => 50,
                'deleted' => 0,
                'version_id' => null,
                'sortkey' => '',
                'norm_iri' => 'section-datensatz',
                'created' => '2017-11-26 21:23:56',
                'modified' => '2018-04-16 13:08:07',
                'published' => 0, 'menu'=>1,
                'segment' => 'help',
                'name' => 'Artikel » Abschnitt Datensatz',
                'category' => 'Kontext-Hilfe',
                'content' => '[zur Übersicht ...](https://epigraf.inschriften.net/docs/view/73)

![Datensatz](https://epigraf.inschriften.net/files/download?root=shared&path=help%2Fscreenshots&filename=DatensatzFenster.png)',
                'format' => 'markdown',
            ],
            [
                'id' => 73,
                'deleted' => 0,
                'version_id' => null,
                'sortkey' => '',
                'norm_iri' => 'start',
                'created' => '2018-01-16 17:42:05',
                'modified' => '2021-11-26 07:29:33',
                'published' => 0, 'menu'=>1,
                'segment' => 'help',
                'name' => 'Epigraf-Handbuch',
                'category' => 'Handbuch',
                'content' => '<!--
<a class="button tiny" href="/epigraf_desktop">EpigrafDesktop &#8663;</a>
<a class="button tiny" href="#epigraf_web">EpigrafWeb &#8663;</a>
<a class="button tiny" href="/docs/view/110">FAQs &#8663;</a>
-->

## Übersicht
* [EpigrafDesktop](#epigraf_desktop)
* [EpigrafWeb](#epigraf_web)
* [Häufig gestellte Fragen (FAQs)](/docs/view/110)

----

* [Epigraf User Support via Mattermost](https://mattermost.gitlab.rlp.net/di/channels/epigraf-user-support)
* [Epigraf-Server: Verantwortliche und Hotline](/docs/view/141)

----


# EpigrafDesktop {#epigraf_desktop}

[Installation](/docs/view/1)
[Konfiguration](/docs/view/70)
[Dokumentation](/docs/view/131)

## Programmaufbau

* [Startfenster](/docs/view/47)
    * [Aufbau und Funktionen](/docs/view/47)
    * [Bearbeiter wählen, hinzufügen, löschen](/docs/view/48)
    * [Artikel entsperren](/docs/view/75)
    * [Register und andere Listen (Literatur, Wappen, Marken usw.) entsperren](/docs/view/75)
    * [Backup anlegen](/docs/view/74)
* [Artikel-Fenster](/docs/view/72)
* Band-Fenster
* [Listenfenster](/docs/view/85)
    * Register
    * [Literatur](/docs/view/99)
    * Wappen
    * [Marken](/docs/view/83)
    * Sonstige Listen

## Themen

* [Ausgabeoptionen für einzelne Artikel](/docs/view/113)
* [Auswahlliste einschränken](/docs/view/85#einschraenken)
* [Bugreports - Fehlermeldungen per Email senden](/docs/view/130)
* [Dateinamen für EpigrafWeb](/docs/view/123)
* [Datierung](/docs/view/95)
* [Doubletten in Registern zusammenführen](/docs/view/85#doubletten)
* [Drucksatz erstellen aus Epigraf in Word](/files/download?root=shared&path=wiki%2FHilfsmittel&filename=Leitfaden-Satzherstellung-aus-Epigraf-in-Word.docx)
* [Importfunktion](/docs/view/127)
* [Ligaturen optional durch Unterstreichung oder Unterbögen darstellen](/docs/view/114)
* [Literaturverwaltung mit Citavi](/docs/view/99)
* [Marken zeichnen mit Inkscape](/docs/view/106)
* [Shortcuts](/docs/view/93)
* [Silbentrennung](/docs/view/105)
* [Verknüpfungen](/docs/view/94)
* [Verszeilen in Anmerkungen/Fußnoten](/docs/view/142)
* [Volltextsuche](/docs/view/132)
* [Weblinks](/docs/view/128#weblinks)
* [Word-Export](/docs/view/138)



# EpigrafWeb {#epigraf_web}

EpigrafWeb bildet den Online-Bereich von Epigraf 4 mit Elementen einer virtuellen Forschungsumgebung, die auch genutzt werden können, wenn EpigrafDesktop nicht als Redaktionssystem verwendet wird.
Allgemein und forschungsstellenübergreifend bietet EpigrafWeb Zugriff auf das Epigraf-Handbuch und das [Epigraf-Wiki](/wiki/view/117).
Daneben wird jeder Forschungsstelle ein eigener Bereich für die Projektorganisation zur Verfügung gestellt.
[Weiter ...](/docs/view/86)

<br/>

',
                'format' => 'markdown',
            ],
            [
                'id' => 146,
                'deleted' => 0,
                'version_id' => null,
                'sortkey' => '',
                'norm_iri' => '',
                'created' => '2020-11-03 18:18:46',
                'modified' => '2021-12-18 00:55:51',
                'published' => 0, 'menu'=>1,
                'segment' => 'help',
                'name' => 'Neue Datenbanken anlegen',
                'category' => 'Administration',
                'content' => 'Über die Weboberfläche:

1. Datenbankverbindung erstellen
2. Datenbank erstellen und initialisieren

Um eine andere Datenbank zu duplizieren oder zu importieren, kann über die Funktion "Script importieren" ein Dump eingelesen werden, zum Beispiel ein Backup.

',
                'format' => 'markdown',
            ],
            [
                'id' => 148,
                'deleted' => 0,
                'version_id' => null,
                'sortkey' => '1',
                'norm_iri' => 'start',
                'created' => '2020-12-28 12:12:46',
                'modified' => '2022-01-27 12:59:27',
                'published' => 1, 'menu' => 1,
                'segment' => 'pages',
                'name' => 'Startseite',
                'category' => 'Frontend',
                'content' => '<div class="content-tight">

	<p>
		EPIGRAF ist eine Plattform zur Erfassung, Annotation, Vernetzung und Publikation von Kommunikationsdaten. Die
		geisteswissenschaftliche Ausrichtung des Projekts wird in den kommenden Jahren auf datenwissenschaftliche und
		sozialwissenschaftliche Anwendungsfelder erweitert.
	</p>

	<p>
		EPIGRAF dient aktuell vor allem der Aufnahme epigrafischer Daten – von Inschriften im Zusammenhang mit den
		Objekten, auf denen sie angebracht sind – und deren Publikation sowohl in gedruckter Form als auch in digitalen
		Medien nach den <a href="https://www.nature.com/articles/sdata201618">FAIR-Prinzipien</a> für das Semantic Web.
	</p>

	<p>
		EPIGRAF umfasst zwei Komponenten:
	</p>

	<ul>
		<li>EpigrafDesktop: Redaktionssystem zur Erfassung und Bearbeitung epigrafischer Daten</li>
		<li>EpigrafWeb: Kollaborative Funktionen, Support, Recherche und Datenexport</li>
	</ul>

	<p>
		EPIGRAF wurde für das <a href="https://www.akademienunion.de/">interakademische Editionsunternehmen</a> „Die
		Deutschen Inschriften des Mittelalters und der Frühen Neuzeit“ entworfen und wird in den neun
		Inschriften-Forschungsstellen der sechs beteiligten Akademien der Wissenschaften eingesetzt.
		Die Entwicklung erfolgt in einer Kooperation der <a href="http://www.adwmainz.de/digitalitaet/digitale-akademie.html">Digitalen
			Akademie der Wissenschaften und der Literatur | Mainz</a> und des <a href="https://www.uni-muenster.de/Kowi/institut/arbeitsbereiche/digital-media-computational-methods.html">Arbeitsbereichs Digital Media & Computational Methods an der Universität Münster</a>.
		Die Datenbestände des Projekts werden in gedruckter Form in der Reihe „Die Deutschen Inschriften“ und digital
		auf <a href="http://www.inschriften.net/">Deutsche Inschriften Online (DIO)</a> publiziert.
	</p>


	<div class="figure-list-logos">

	       <figure class="figure-nocaption">
			<a href="https://www.uni-muenster.de/Kowi/institut/arbeitsbereiche/digital-media-computational-methods.html">
				<img src="/files/download/2702" alt="Westfälische Wilhelms-Universität Münster">
			</a>
		</figure>

		<figure class="figure-nocaption">
			<a href="https://www.adwmainz.de/digitalitaet/digitale-akademie.html">
				<img src="/files/download/2701" alt="Digitale Akademie Mainz">
			</a>
		</figure>

		<figure>
			<a href="https://www.adwmainz.de">
				<img src="/files/download/898?format=thumb&size=150"
					 alt="Akademie der Wissenschaften und der Literatur Mainz">
			</a>
			<figcaption>
				Akademie der Wissenschaften und der Literatur Mainz
			</figcaption>
		</figure>


		<figure>
			<a href="https://adw-goe.de/">
				<img src="/files/download/896?format=thumb&size=150" alt="Akademie der Wissenschaften Göttingen">
			</a>
			<figcaption>
				Akademie der Wissenschaften Göttingen
			</figcaption>
		</figure>

		<figure>
			<a href="http://www.awk.nrw.de/">
				<img src="/files/download/897?format=thumb&size=150"
					 alt="Nordrhein-Westfälische Akademie der Wissenschaften und der Künste">
			</a>
			<figcaption>
				Nordrhein-Westfälische Akademie der Wissenschaften und der Künste
			</figcaption>
		</figure>

		<figure>
			<a href="http://www.saw-leipzig.de/">
				<img src="/files/download/899?format=thumb&size=150" alt="Sächsische Akademie der Wissenschaften">
			</a>
			<figcaption>
				Sächsische Akademie der Wissenschaften
			</figcaption>
		</figure>

		<figure>
			<a href="https://www.hadw-bw.de/">
				<img src="/files/download/900?format=thumb&size=150" alt="Heidelberger Akademie der Wissenschaften">
			</a>
			<figcaption>
				Heidelberger Akademie der Wissenschaften
			</figcaption>
		</figure>

		<figure>
			<a href="https://www.badw.de/">
				<img src="/files/download/382?format=thumb&size=150" alt="Bayerische Akademie der Wissenschaften">
			</a>
			<figcaption>
				Bayerische Akademie der Wissenschaften
			</figcaption>
		</figure>
	</div>
</div>
',
                'format' => 'markdown',
            ],
            [
                'id' => 151,
                'deleted' => 0,
                'version_id' => null,
                'sortkey' => '2',
                'norm_iri' => 'api',
                'created' => '2020-12-28 12:20:02',
                'modified' => '2021-10-02 16:58:09',
                'published' => 1, 'menu'=>1,
                'segment' => 'pages',
                'name' => 'API',
                'category' => 'Frontend',
                'content' => '<h1>Eine Schnittstelle in die Geschichte...</h1>
<p>
    Inschriften legen Zeugnis über die Geschichte ab und verweisen auf Ideen, Ereignisse, Personen, Orte und vieles mehr.
    Damit andere Projekte auf dem reichhaltigen Datenschatz, der sich aus der fortlaufenden Erschließung ergibt, aufbauen können,
    wird in Zukunft eine Programmierschnittstelle bereit gestellt.
<p>
</p>

    Wir hoffen, im Verlauf des Jahres 2021 erste Daten aus den Registern zugänglich machen zu können.
    Nehmen Sie gern Kontakt mit uns auf, wenn Sie daran interessiert sind.
</p>
<br>
*Das Datenmodell von Epigraf*
<br>
<br>
![Datenmodell von Epigraf](/files/display/2056)',
                'format' => 'markdown',
            ],
            [
                'id' => 96,
                'norm_iri' => '',
                'created' => '2018-03-10 15:39:48',
                'modified' => '2020-05-19 15:59:19',
                'published' => 0, 'menu'=>1,
                'segment' => 'wiki',
'format'=>'markdown',
'deleted'=>0,
                'name' => 'Hilfsmittel',
                'category' => 'B. Hilfsmittel',
                'content' => '#####Übersicht

A. Themenfelder:

* [Wörterbücher](#wb)
* [Heraldik](#heraldik)
* [Chronologie und Chronographie](#chrono)
* [Theologie und Kirche](#theologie)
* [Universität](#uni)
* [Geschichtsquellen](#histo)
* [Prosopographie](#prosopo)
* [Historische Geographie](#geo)
* [Kunstgeschichte und Denkmalpflege](#kuge)
* [Glockenkunde](#glocken)
* [Musik und Liturgie](#musik)
* [Abbreviaturen und Siglen](#sigla)
* [Berufe, Titel, Stände, Verwandtschaften, Attribute](#attribute)


B. Techniken:

* [Tastaturbelegung für Altgriechisch und Hebräisch](#tastatur)
* [Verwendung "Regulärer Ausdrücke": Suchen und Ersetzen mit Mustervergleich/Platzhalter in Word](https://www.google.com/url?sa=t&rct=j&q=&esrc=s&source=web&cd=2&cad=rja&uact=8&ved=2ahUKEwjP9d-JlIfkAhUiIMUKHV8mALEQFjABegQIAhAC&url=http%3A%2F%2Fwww.provincia.bz.it%2Flandmaus%2Fde%2Flandmaus-online%2Ftipps-tricks.asp%3F%26somepubl_action%3D300%26somepubl_image_id%3D177668&usg=AOvVaw0baD5FjdxtI5Oubq8vsEiR)
* [Explicatio notarum quae diacriticae dicuntur - Leidener Klammersystem mit den Erweiterungen von Krummrey und Panciera (CIL VII.8.1)](https://epigraf.inschriften.net/files/download?root=shared&path=wiki%2FHilfsmittel&filename=Krummrey-Panciera_CIL_VI_8_1_1.pdf)

---
<a name="wb"></a>
####Wörterbücher
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* [DWDS – Digitales Wörterbuch der deutschen Sprache](https://www.dwds.de/)
* [Wörterbuch-Netz - Historische und regionalsprachliche Wörterbücher des Deutschen](http://www.woerterbuchnetz.de/cgi-bin/WBNetz/setupStartSeite.tcl)
* [Wörterbücher des Niederdeutschen](http://www.plattdeutsch-niederdeutsch.net/bibliographie/woerterbuecher.htm)
* [Mittelhochdeutsches Wörterbuch (MWB Online)](http://www.mhdwb-online.de/)
* [Schiller/Lübben, Mittelniederdeutsches Wörterbuch](https://de.wikisource.org/wiki/Mittelniederdeutsches_W%C3%B6rterbuch_(Schiller-L%C3%BCbben))
* [Frühneuhochdeutsches Wörterbuch (FWB online)](https://fwb-online.de/)

---

* [Du Cange et al., Glossarium mediæ et infimæ latinitatis](http://ducange.enc.sorbonne.fr/)
* [Forcellini (Totius Latinitatis Lexicon Aegidii Forcellini)](http://linguax.com/lexica/forc.php)
* [Enigma: Entziffern schwieriger lateinischer Lesarten in mittelalterlichen Handschriften](http://ciham-digital.huma-num.fr/enigma/)
* [Navigium - Latein-Wörterbuch](https://www.navigium.de/latein-woerterbuch.php?form)
* [CAMENA EVRECA: Lateinwörterbücher aus der Frühen Neuzeit](https://www2.uni-mannheim.de/mateo/camenahtdocs/eureca.html)

---

<a name="heraldik"></a>
#### Heraldik
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* Nachschlag- und Sammelwerke adeliger Wappen [GenWiki  &#8663;](http://wiki-de.genealogy.net/Nachschlag-_und_Sammelwerke_adeliger_Wappen)

---

* Siebmachers Wappenbücher [CerlOrg  &#8663;](http://data.cerl.org/siebmacher/_search)
* Bagmihl, Pommersches Wappenbuch [Wikipedia &#8663;](https://de.wikipedia.org/wiki/Pommersches_Wappenbuch) [GenWiki &#8663;](http://wiki-de.genealogy.net/Pommersches_Wappenbuch_(Bagmihl))
* Spießen, Wappenbuch des Westfälischen Adels [GenWiki  &#8663;](http://wiki-de.genealogy.net/Wappenbuch_des_westf%C3%A4lischen_Adels)

---

<a name="chrono"></a>
#### Chronologie und Chronographie
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* Grotefend, Zeitrechnung des deutschen Mittelalters und der Neuzeit, von Horst Ruth [ _manuscripta-mediaevalia_ &#8663;](http://www.manuscripta-mediaevalia.de/gaeste/grotefend/grotefend.htm)
* [Ecclesiastical Date Calculator](http://people.albion.edu/imacinnes/calendar/Ecclesiastical_dates_files/widget1_markup.html)

---

<a name="theologie"></a>
#### Theologie und Kirche
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

Bibelrecherche

* [BibelServer](https://www.bibleserver.com/)
* [BibelOnline](http://www.bibel-online.net/)
* [Biblegateway](https://www.biblegateway.com/passage/?language=Latin)
* [All-in-One Biblical Resources Search](http://markgoodacre.org/multibib/)

---

Lexika

* [Biographisch-bibliographisches Kirchenlexikon (BBKL)](http://www.bbkl.de/)
* [Ökumenisches Heiligenlexikon](https://www.heiligenlexikon.de/)
* [WiBiLex - Das wissenschaftliche Bibellexikon im Internet (im Aufbau)](https://www.bibelwissenschaft.de/wibilex/)

___

Siglen

* [Siglen für Bibelnachweise (pdf)](https://epigraf.inschriften.net/files/download?root=root&path=shared%2Fwiki%2FHilfsmittel&filename=Bibelnachweise.pdf)

-----

Bibel-Online-Ausgaben

1.	Moderne deutschsprachige Bibelausgaben:

* [Luther-Bibel (2017) ](https://www.bibleserver.com/)
* [Einheitsübersetzung (2016)](https://www.bibleserver.com)
* [Zürcher Bibel](https://www.bibleserver.com)

2.	Lateinische Bibelausgaben:

* [Vulgata Clementina](http://vulsearch.sourceforge.net/html/)
* [Vulgata (Weber/Gryson)](https://www.bibelwissenschaft.de/online-bibeln/biblia-sacra-vulgata/lesen-im-bibeltext/) Die Online-Ausgabe enthält im Unterschied zur gedruckten im Psalter nur das Psalterium iuxta Hebraeos, nicht aber das Gallicanum.

3.	Evangelische deutschsprachige Bibelübersetzungen des 16. Jahrhunderts:

* [Luther-Bibel (1545), Edition](http://www.zeno.org/Literatur/M/Luther,+Martin/Luther-Bibel+1545)
* [Niederdeutsche Bibelübersetzung (Bugenhagen-Bibel, 1533), Vollfaksimile](http://daten.digitale-sammlungen.de/~db/0005/bsb00051900/images/)
* [Niederdeutsche Bibelübersetzung (Barther Bibel, 1588)](http://www.digitale-bibliothek-mv.de/viewer/resolver?urn=urn%253Anbn%253Ade%253Agbv%253A9-g-1904456)
* [Zürcher Bibel (1531), Vollfaksimile](https://www.e-rara.ch/zuz/doi/10.3931/e-rara-17064)

4.	Katholische deutschsprachige Bibelübersetzungen des 16. Jahrhunderts (Katholische Korrekturbibeln):

* [Hieronymus Emser, Neues Testament (1527), Vollfaksimile](http://digital.onb.ac.at/OnbViewer/viewer.faces?doc=ABO_%2BZ137159405)
* [Johannes Dietenberger, Biblia Altes und Neues Testament (1534) Vollfaksimile](http://daten.digitale-sammlungen.de/~db/0008/bsb00085341/images/)
* [Johannes Eck, Bibel Altes und Neues Testament (1537), Vollfaksimile](http://digital.onb.ac.at/OnbViewer/viewer.faces?doc=ABO_%2BZ137117101)


5.	[Deutsche Bibelgesellschaft: Online-Bibeln](https://www.bibelwissenschaft.de/online-bibeln/ueber-die-online-bibeln/)
Das Portal bietet Online-Ausgaben der hebräischen, griechischen, lateinischen und einzelner moderner deutschsprachiger Ausgaben. Unter dem Stichwort „Bibelkunde“ erklären sehr instruktive Artikel Aufbau, Inhalt und Texttradition einzelner Bibelbücher ([WiBiLex](https://www.bibelwissenschaft.de/wibilex/)). Eine Zeitschrift „Die Bibel in der Kunst“ ist im Aufbau.

6. [VD 16](https://opacplus.bib-bvb.de/TouchPoint_touchpoint/start.do?SearchProfile=Altbestand&SearchType=2)
Vollfaksimiles historischer Bibelausgaben erreichbar über das Verzeichnis der Drucke des 16. Jahrhunderts.

---

<a name="uni"></a>
#### Universität
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* [RAG - Repertorium Academicum Germanicum - Die graduierten Gelehrten des Alten Reiches zwischen 1250 und 1550](http://rag-online.org/)
* [Universitätsmatrikel - GenWiki](http://genwiki.genealogy.net/Universit%C3%A4tsmatrikel)

---

<a name="histo"></a>
#### Geschichtsquellen
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* [Monumenta Germaniae Historica (MGH)](http://www.mgh.de/)
* [Regesta Imperii](http://www.regesta-imperii.de/startseite.html)

---

<a name="prosopo"></a>
#### Prosopographie
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* [Biographie-Portal  - ADB, NDB u. a.](http://www.biographie-portal.eu/)
* [Personalschriften](http://www.personalschriften.de/)
* [Germania Sacra: Personendatenbank](http://personendatenbank.germania-sacra.de/)
* [Repertorium Germanicum (RG)](http://194.242.233.132/denqRG/index.htm)

---

<a name="geo"></a>
#### Historische Geographie
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* [Orbis Latinus online](http://www.columbia.edu/acis/ets/Graesse/contents.html)
* [Datenbank frühneuzeitlicher Ortsnamen: Thesaurus Locorum (THELO)](http://www.personalschriften.de/datenbanken/thelo.html)
* [Repertorium Germanicum (RG)](http://194.242.233.132/denqRG/index.htm)
* [Geonames](https://www.geonames.org/) Website zur Ermittlung geographischer Normdaten

---

<a name="kuge"></a>
#### Kunstgeschichte und Denkmalpflege
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* [Bau- und Kunstdenkmäler in Deutschland und Europa - Inventarbände](https://de.wikisource.org/wiki/Kunstdenkm%C3%A4ler)


---

<a name="glocken"></a>
#### Glockenkunde / Campanologie
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* [Deutsches Glockenmuseum](https://glockenmuseum.de)
* [Grabinski Online](http://www.grabinski-online.de)
* Bibliographien
    * [Bibliothekskatalog des Deutschen Glockenmuseums](https://glockenmuseum.de/bibliothek/katalog/)
    * [Bibliographie zur Glockenkunde von Joachim Grabinski](http://www.grabinski-online.de/glocken/biblio.html)
    * [Bibliographie zur Glockenkunde = OPAC des Deutschen Glockenmuseums, von Andreas Philipp](http://pionlib.de/bells)

---

<a name="musik"></a>
#### Musik und Liturgie
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* Datenbanken zur Kirchenmusik des Mittelalters
   * [Gregorianisches Repertoire](https://gregorien.info/)
   * [Cantus: A Database for Latin Ecclesiastical Chant - Inventories of Chant Sources]( http://cantus.uwaterloo.ca/)
   * [Antiphonale Synopticum]( http://gregorianik.uni-regensburg.de/info)
* Nachschlagewerke
   * [Ulysse Chevalier, Repertorium Hymnologicum, Bd. 1: A-K](https://archive.org/details/repertoriumhymno01chev)
   * [Ulysse Chevalier, Repertorium Hymnologicum, Bd. 2: L-Z](https://archive.org/details/repertoriumhymn02chev)
   * [Ulysse Chevalier, Repertorium Hymnologicum, Bd. 3: Nachträge A-Z](https://archive.org/details/repertoriumhymn03chev)
   * [Ulysse Chevalier, Repertorium Hymnologicum, Bd. 4: Nachträge A-Z](https://archive.org/details/repertoriumhymno04chev_0)
   * [Ulysse Chevalier, Repertorium Hymnologicum, Bd. 5: Addenda et corrigenda](https://archive.org/details/repertoriumhymn05chev)
   * [Ulysse Chevalier, Repertorium Hymnologicum, Bd. 6: Préface - Tables](https://archive.org/details/repertoriumhymno06chev)
* Thematische Internetseiten
   * [Missa Medievalis](https://www.uni-muenster.de/Kultbild/missa/): Internetseite der Universität Münster, SFB 496, Forschungsgruppe KultBild, die einen Überblick über Text- und Bildquellen sowie Realien zur mittelalterlichen Messe bietet.


---

<a name="sigla"></a>
#### Abbreviaturen und Siglen
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* [Abkürzungen _per singulas litteras_ in den DI-Bänden (Jens Pickenhan)](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FHilfsmittel&filename=Abkuerzungen_per_singulas_litteras.xlsx)
* [Abkürzungen in den DI-Bänden allgemein (fürs Epgraphik-Lehrbuch zusammengestellt von Katharina Kagerer)](https://epigraf.inschriften.net/files/download?root=shared&path=wiki%2FHilfsmittel&filename=Abkuerzungen.xlsx)
* [Sigla Latina in Libris Impressis Occurrentia, von Marek Winiarczyk, Universität Mannheim](https://www2.uni-mannheim.de/mateo/camenaref/siglalatina.html)

---

<a name="attribute"></a>
#### Berufe, Titel, Stände, Verwandtschaften, Attribute
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* [Grundsätzliche Überlegungen zur Übersetzung von Titeln, Standes-, Rang- und Amtsbezeichnungen sowie von Epitheta und Prädikaten, von Rüdiger Fuchs](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FDI-Richtlinien&filename=Epitheta-Titel.pdf)
* [Berufsbezeichnungen](https://script.byu.edu/SiteAssets/German_Occupations.pdf) [(BYU\'s Script Tutorial)](https://script.byu.edu/Pages/German/de/common.aspx)
* [Datenbank frühneuzeitlicher Berufsbezeichnungen: Thesaurus Professionum (THEPRO)](http://www.personalschriften.de/datenbanken/thepro.html)
* [Übersetzungen lateinischer Titel, Standes-, Rang- und Amtsbezeichnungen (Harald Drös)](https://epigraf.inschriften.net/files/download?root=shared&path=wiki%2FHilfsmittel&filename=Titel_--mter_Auszug-aus-DI-2--12--36--48--50--54.pdf)

---

<a name="tastatur"></a>
#### Tastaturbelegung für Altgriechisch und Hebräisch
[_zurück_](https://epigraf.inschriften.net/wiki/view/96)

* Altgriechisch
    * [Tastatur auf Altgriechisch umstellen](https://www.kaththeol.uni-muenchen.de/studium/wissenschaftliches-arbeiten/materialien_seite/griechisch.pdf)
    * [Polytonische Orthographie](https://de.wikipedia.org/wiki/Polytonische_Orthographie)
    * [Griechisch Schreiben mit Open Office](https://biblegreek.jimdo.com/tastaturbelegung.php)
* Hebräisch
    * [Tastatur auf Hebräisch umstellen](https://www.kaththeol.uni-muenchen.de/studium/wissenschaftliches-arbeiten/materialien_seite/tastatur-hebraeisch.pdf)

---

</br></br></br></br></br>

',
            ],
            [
                'id' => 103,
                'norm_iri' => '',
                'created' => '2018-03-19 08:55:23',
                'modified' => '2020-06-23 11:30:49',
                'published' => 0, 'menu'=>1,
                'segment' => 'wiki',
'format'=>'markdown',
'deleted'=>0,
                'name' => 'Wiki » Repositorium',
                'category' => 'C. Repositorium',
                'content' => '<!--- Muster für die Links: [&#8663;]() -->

######Übersicht
* [Literatur - Sonderdrucke u. dgl.](#rep_lit)
* [Filesharing](#rep_share)

---



#### <a name="rep_lit"></a>Literatur - Sonderdrucke u. dgl.
Hermann, Sonja: Das Epitaph für Philipp Konrad von Viermund von 1596 im Paderborner Stadtmuseum, in: Westfälische Zeitschrift 169, 2019, S. 235-255. [&#8663;](https://epigraf.inschriften.net/files/download?root=root&path=Literatur&filename=Hermann--Epitaph-Viermund-1.pdf)

Baltolu, Ramona: Die Rolle der Fraktur in der Gotico-Antiqua, in: Archiv für Diplomatik 63, 2017, S. 337-366. [&#8663;](https://epigraf.inschriften.net/files/download?path=Literatur&filename=BOE_412-50980_AfD_63_Baltolu_eSD.pdf)

Diederich, Toni: Siegeldatierung nach der Schrift – Möglichkeiten und Grenzen, in: Herold-Jahrbuch, Neue Folge 20. Band, 2015, S. 33-72 [&#8663;](https://epigraf.inschriften.net/files/download?path=Literatur&filename=herold_jb_2015_diederich.pdf)

Hallof, Klaus: Antiken Inschriften auf der Spur. Die Epigraphiker der Akademie „im Felde“, in: Jahresmagazin 2017 der Berlin-Brandenburgischen Akademie der Wissenschaften, S. 66-71 [&#8663;](https://epigraf.inschriften.net/files/download?path=Literatur&filename=140HallofEpigraphikerimFeldeJahresmagazin20171.pdf)

Herbers, Birgit: „Referenzkorpus Deutsche Inschriften“ – Chancen und Grenzen der Auswertung, in: PerspektivWechsel oder: Die Wiederentdeckung der Philologie. Band 1: Sprachdaten und Grundlagenforschung in der Historischen Linguisitk. Hrsg. von Sarah Kwekkeboom u. Sandra Waldenberger. Berlin 2016, S. 27-41.[&#8663;](https://epigraf.inschriften.net/files/download?path=Literatur&filename=herbers_FSWegera.pdf)

Kagerer, Katharina: Höfische Repräsentation durch Inschriften – die Anfänge Bückeburgs als Residenzstadt, in: Schaumburgische Mitteilungen 2 (2019), S. 152-177 [&#8663;](https://epigraf.inschriften.net/epi4/inschriften_nsn/files/download?path=literatur&filename=Kagerer_Inschriften_in_Residenzstaedten.pdf)

Kagerer, Katharina: Das lateinisch‐deutsche Inschriftenprogramm in der evangelischen Dorfkirche von Hülsede (1577), in: Astrid Steiner-Weber/Franz Römer (Hg.), Acta Conventus Neo-Latini Vindobonensis. Proceedings of the Sixteenth International Congress of Neo-Latin Studies (Vienna 2015), Leiden 2018 (= Acta Conventus Neo-Latini 16), S. 379-391 [&#8663;](https://epigraf.inschriften.net/epi4/inschriften_nsn/files/download?path=literatur&filename=Kagerer_Huelsede.pdf)

Scholz, Sebastian: Inschriften als Medien des Glaubensstreits. Die _Antithesis Christi et Antichristi_ in der Schlosskirche von Schmalkalden, in: Gewinner und Verlierer in Medien der Selbstdarstellung. Bilder, Bauten, Inschriften, Leichenpredigten, Münzen und Medaillen in der Frühen Neuzeit, herausgegeben von Jörg H. Lampe, Wiesbaden 2017, S. 25-35 [&#8663;](https://epigraf.inschriften.net/files/download?path=Literatur&filename=2017GewinnerVerliererSonderdruckScholz.pdf)

---


#### <a name="rep_share"></a>Filesharing - Dokumente teilen

Abkürzungen _per singulas litteras_ in den DI-Bänden, zusammengestellt von Jens Pickenhan u. a.[&#8663;](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FHilfsmittel&filename=Abkuerzungen_per_singulas_litteras.xlsx)

Abkürzungen in den DI-Bänden, für das Epigraphik-Lehrbuch zusammengestellt von Katharina Kagerer [&#8663;](https://epigraf.inschriften.net/files/download?root=shared&path=wiki%2FHilfsmittel&filename=Abkuerzungen.xlsx)

Glockenbibliographie von Andreas Philipp: Benutzungshinweise[&#8663;](https://epigraf.inschriften.net/epi4/inschriften_nsn/files/download?path=hilfsmittel&filename=Glockenbibliographie_Benutzungshinweise.pdf)

<!--- Muster für die Links: [&#8663;]() -->

',
            ],
            [
                'id' => 104,
                'norm_iri' => '',
                'created' => '2018-03-20 18:02:07',
                'modified' => '2019-10-22 08:57:44',
                'published' => 0, 'menu'=>1,
                'segment' => 'wiki',
'format'=>'markdown',
'deleted'=>0,
                'name' => 'Epigraf Wiki >> Organisation',
                'category' => 'E. Organisation',
                'content' => '---

[Epigrafik-Lehrbuch](https://epigraf.inschriften.net/wiki/view/137)

---

[DI - Mitarbeitertagungen](https://epigraf.inschriften.net/wiki/view/116)

---

 [DI - Bearbeitungsrichtlinien](https://epigraf.inschriften.net/wiki/view/97)

---

 [DI - Evaluation 2018](https://epigraf.inschriften.net/wiki/view/101)

---

[DI - E-Mail-Verteiler](https://epigraf.inschriften.net/wiki/view/109)

---

[Entwicklung (DIO - Epigraf - VRE)](https://epigraf.inschriften.net/wiki/view/111)

---
[Nationale Forschungsdateninfrastruktur - NFDI](https://epigraf.inschriften.net/wiki/view/115)

---

[Open Data - Open Access - Lizenzierung](https://epigraf.inschriften.net/wiki/view/140)

---
',
            ],
            [
                'id' => 117,
                'norm_iri' => 'start',
                'created' => '2018-11-10 22:58:07',
                'modified' => '2020-06-10 08:35:07',
                'published' => 0, 'menu'=>1,
                'segment' => 'wiki',
'format'=>'markdown',
'deleted'=>0,
                'name' => 'Wiki',
                'category' => '',
                'content' => '<!---
Kollektive Intelligenz: Inschriften Online - Organisation - Repositorium - Hilfsmittel - Recherchen
<span style="padding-left:1em"></span>[Epigraf Wiki &#8663;](/docs/view/96)
<a class="button" href="/wiki/view/96">Epigraf Wiki:</a>-->

[<img src="https://epigraf.inschriften.net/files/download?root=shared&path=help%2Fscreenshots&filename=twitter_logo.png" />](https://twitter.com/inschriften)



##Kollektive Intelligenz: Epigraf Wiki </br></br>


[Inschriften Online](https://epigraf.inschriften.net/wiki/view/118)

[Deutsche Inschriften - Kumulierte Register](https://epigraf.inschriften.net/wiki/view/143)

[Hilfsmittel](https://epigraf.inschriften.net/wiki/view/96)

[Repositorium: Literatur (Sonderdrucke u. dgl.) u. Filesharing](https://epigraf.inschriften.net/wiki/view/103)

[Web-Recherchen](https://epigraf.inschriften.net/wiki/view/120)

[Organisation](https://epigraf.inschriften.net/wiki/view/104)

',
            ],
            [
                'id' => 118,
                'norm_iri' => '',
                'created' => '2018-11-11 09:47:39',
                'modified' => '2020-06-30 16:27:32',
                'published' => 0, 'menu'=>1,
                'segment' => 'wiki',
'format'=>'markdown',
'deleted'=>0,
                'name' => 'Inschriften Online',
                'category' => 'A. Inschriften Online',
                'content' => '---


### Mittelalter und Neuzeit

[Deutsche Inschriften Online (DIO)](http://www.inschriften.net/)

[Die Deutschen Inschriften - Wiener Reihe](http://hw.oeaw.ac.at/inschriften)

[Die Deutschen Inschriften - ältere Bände (4, 5, 13, 15, 17) als Digitalisate](https://digi.hadw-bw.de/view/di)

[Germania Sacra: Inschriften niedersächsischer Klöster und Stifte](http://inschriften.germania-sacra.de/)

[TITULUS - Corpus des inscriptions de la France médiévale](http://titulus.huma-num.fr/exist/apps/titulus/index.html)

---

### Altertum
#### Plattform

Eine offene kollaborative Plattform für die antike Epigraphik / A Collaborative Platform for Digital Epigraphy: [Epigraphy.info](http://epigraphy.info)

* Digital Epigraphy am Scheideweg? / Digital Epigraphy at a crossroads?[ &#8663;](http://archiv.ub.uni-heidelberg.de/volltextserver/22141/1/DigitalEpigraphy_FFG-FG_09112016.pdf)
* Empfehlungen für eine offene kollaborative Plattform für die antike
Epigraphik[ &#8663;](http://archiv.ub.uni-heidelberg.de/volltextserver/24674/1/epigraphy-info_Empfehlungen_Feraudi_Grieshaber_062018_de-en.pdf)

#### Datenbanken

Corpus Inscriptionum Latinarum (CIL) - Online via [Arachne](https://arachne.uni-koeln.de/drupal/?q=de_DE/node/369)

-----
</br>



Der größte Teil der epigraphischen Hinterlassenschaft der Antike liegt mittlerweile – was den Textbestand angeht – in digitaler Form vor, meist jedoch nur in der Originalsprache.
Die Übersicht ist übernommen von [http://www.gnomon.ku-eichstaett.de/LAG/proseminar/Seminarreader/frame_38691_maincontent.html](http://www.gnomon.ku-eichstaett.de/LAG/proseminar/Seminarreader/frame_38691_maincontent.html).

1. PHI #7 (CD-ROM).
Enthält eine große Zahl griechischer Inschriften aus klassischer Zeit, daneben auch viele christliche Inschriften aus der Spätantike. Diese und neuere Texte sind seit geraumer Zeit unter [http://epigraphy.packhum.org](http://epigraphy.packhum.org/) auch direkt und kostenfrei im Internet zugänglich.
2. Electronic Archive of Greek and Latin Epigraphy (EAGLE): [http://www.eagle-eagle.it](http://www.eagle-eagle.it/).
Als umfassende Datenbank lateinischer und griechischer Inschriften geplant. Von einem zentralen Portal aus soll über eine Metasuche Zugriff auf möglichst viele der vorhandenen epigraphischen Datenbanken geboten werden. Noch in der Entwicklungsphase, aber bereits benutzbar.
3. Epigraphische Datenbank Heidelberg (EDH): [http://www.epigraphische-datenbank-heidelberg.de](http://www.epigraphische-datenbank-heidelberg.de/).
Gewissermaßen der Mercedes unter den epigraphischen Datenbanken. Bietet nicht nur publizierte Texte, sondern auch Neulesungen und teilweise sogar exzellente Bilder. Zudem werden alle epigraphisch relevanten Daten, wie z. B. Maße, Schrifthöhe usw., erfasst. Im Gegensatz zu anderen vergleichbaren Projekten handelt es sich hier also eigentlich nicht nur um eine Datenbank, sondern um eine eigenständige Publikation. Darüber hinaus werden die aufgenommenen Texte thematisch und prosopographisch weiter erschlossen. Aus Eichstätt werden griechische Inschriften der römischen Kaiserzeit zugeliefert.
4. Epigraphic Database Roma (EDR): [http://www.edr-edr.it](http://www.edr-edr.it/).
Ableger des EAGLE-Projektes, der vor allem für die Bearbeitung der nichtchristlichen Inschriften (für die christlichen Inschriften s. EDB) aus Rom selbst zuständig ist. Das entsprechende Textmaterial der EDH wurde zur weiteren Pflege abgegeben.
5. Epigraphic Database Bari (EDB): [http://www.edb.uniba.it](http://www.edb.uniba.it/).
Ableger des EAGLE-Projektes, der für den Bereich der christlichen Epigraphik zuständig ist. Momentan sind bereits knapp 240 000 Inschriften aus verschiedenen ICVR-Bänden verfügbar.
6. Epigraphische Datenbank Clauss/Slaby (EDCS): [http://www.manfredclauss.de](http://www.manfredclauss.de/).
Die momentan umfänglichste Datenbank mit lateinischen Inschriften. Damit ist der besondere Vorzug dieses Projektes schon benannt. In Zusammenarbeit mit dem Rechenzentrum der KU wurde die Suchmaske vor einiger Zeit wesentlich verbessert. Auch werden mittlerweile Links zu Paralleleinträgen in anderen Datenbanken und Bildmaterial gegeben. Die über die Texterfassung hinaus geleistete Erschließung kann sich aber nicht mit dem von der EDH Gebotenen messen.
7. Eichstätter Konkordanzprogramm zur griechischen und lateinischen Epigraphik
(ConcEyst): [http://www.ku-eichstaett.de/Fakultaeten/GGF/fachgebiete/Geschichte/Alte%20Geschichte/Projekte/conceyst](http://www.ku-eichstaett.de/Fakultaeten/GGF/fachgebiete/Geschichte/Alte%20Geschichte/Projekte/conceyst).
Das Programm ist über den CD-ROM-Server bzw. das DBIS zu benutzen, steht aber neuerdings auch zum freien Download zur Verfügung. Die Datenbestände umfassen u. a. mehrere CIL-Bände, die gesamten ILS und große Teile der SIG3. Besonders hilfreich sind die Möglichkeiten, auf einen rückläufigen Index zuzugreifen, sowie die Ausgabe der Suchergebnisse im Konkordanzformat.
8. Epigraphische Datenbank zum antiken Kleinasien: [http://www.epigraphik.uni-](http://www.epigraphik.uni-hamburg.de/)[hamburg.de](http://www.epigraphik.uni-hamburg.de/).
Erschließt momentan vor allem Texte aus Galatien.
9. Hispania Epigraphica Online (HEpOl): [http://www.eda-bea.es](http://www.eda-bea.es/).
Erschließt die Editionen der gleichnamigen Zeitschrift digital.
10. Ubi erat lupa: [http://www.ubi-erat-lupa.org/platform.shtml](http://www.ubi-erat-lupa.org/platform.shtml).
Beherbergt eine Reihe von epigraphischen Datenbanken. Besonders interessant ist eine Datenbank römischer Steindenkmäler, die besonderen Wert auf die gleichrangige Dokumentation von Bildschmuck und Text legt.
11. Poinikastas – Epigraphic sources for early Greek writing: [http://poinikastas.csad.ox.ac.uk](http://poinikastas.csad.ox.ac.uk/).
Erschließt die archaischen Inschriften Griechenlands, vor allem auch unter schriftgeschichtlichen Aspekten. Referenzpunkt ist dabei Lilian H. Jefferys Standardwerk: The local scripts of archaic Greece. A study of the origin of the Greek alphabet and its development from the eighth to the fifth centuries B. C. (= Oxford monographs on classical archaeology), Oxford 1961. Zu jeder Inschrift werden Hinweise auf weitere Editionen gegeben, sowie ein Digitalisat der handschriftlichen Notizen Jefferys gegeben.
12. The Inscriptions of Roman Tripolitania (IRT): [http://rubens.anu.edu.au/htdocs/bycountry/libya](http://rubens.anu.edu.au/htdocs/bycountry/libya).
Elektronische Version der gedruckten Publikation von 1952. Interessant v. a. wegen der eingescannten Abbildungen.
13. The Roman Inscriptions of Britain I (RIB I): [http://www.romanbritain.freeserve.co.uk/INSCRIPTIONS.HTM](http://www.romanbritain.freeserve.co.uk/INSCRIPTIONS.HTM).
14. Testimonia Epigraphica Norica (T.E.NOR): [http://www.kfunigraz.ac.at/agawww/Instrumenta/oberoesterreich](http://www.kfunigraz.ac.at/agawww/Instrumenta/oberoesterreich).
Datenbank, die in recht kryptischem Format Zugriff auf römische Kleininschriften (instrumentum domesticum) aus Oberösterreich bietet.
15. Online library of the ancient Greek inscriptions from Bulgaria: [http://telamon.proclassics.org](http://telamon.proclassics.org/).
Das Projekt konzentriert sich momentan auf die Inschriften von Philippopolis und Augusta Traiana.
16. Vindolanda Tablets Online/Tabulae Vindolandenses: [http://vindolanda.csad.ox.ac.uk](http://vindolanda.csad.ox.ac.uk/).
In dem römischen Fort Vindolanda, im Hinterland des Hadrianswalles gelegen, wurde der bislang größte Fund von beschrifteten Holztafeln gemacht. Das komplette Corpus inklusive der Übersetzungen, hochauflösenden Bilder, Kommentare und Einleitungstexte der Editoren steht im Internet zur Verfügung. Die Aufbereitung des Materials auf der Website ist exzellent (s. etwa die Ausführungen zum römischen Namenwesen, zur Struktur der Auxiliareinheiten, militärischen Dienstgraden usw. in der Rubrik „Reference&quot;). An example how such things ought to be done!!
17. Aphrodisias in Late Antiquity: [http://insaph.kcl.ac.uk/ala2004](http://insaph.kcl.ac.uk/ala2004).
Erweiterte elektronische Version von Roueché, Charlotte: Aphrodisias in Late Antiquity: the Late Roman and Byzantine inscriptions, London 1989.
18. Bilddatenbank attischer Inschriften: [http://epigraphy.osu.edu/resources/attic](http://epigraphy.osu.edu/resources/attic).
19. Inscriptions from the land of Israel: [http://www.stg.brown.edu/projects/Inscriptions](http://www.stg.brown.edu/projects/Inscriptions).
20. Greek manumission inscriptions: [http://www3.iath.virginia.edu/meyer](http://www3.iath.virginia.edu/meyer/).


----
außerdem:

Inscriptiones Siciliae / I.Sicily: [http://sicily.classics.ox.ac.uk/](http://sicily.classics.ox.ac.uk/)


</br></br></br></br>
',
            ],
            [
                'id' => 120,
                'norm_iri' => '',
                'created' => '2018-11-11 10:16:46',
                'modified' => '2019-05-17 08:37:58',
                'published' => 0, 'menu'=>1,
                'segment' => 'wiki',
'format'=>'markdown',
'deleted'=>0,
                'name' => 'Web-Recherchen',
                'category' => 'D. Recherche',
                'content' => '_Der beste Weg im Internet die richtige Antwort zu bekommen, ist nicht eine Frage zu stellen, sondern die falsche Antwort zu verbreiten._ [(Cunningham\'s Law)](https://de.wikipedia.org/wiki/Ward_Cunningham)

* [Wikipedia]()
* [Internet-Archive (archive.org)](https://archive.org)[ ... _siehe hierzu auch_](https://de.wikipedia.org/wiki/Internet_Archive)
* [Google Books - Erweiterte Buchsuche](https://books.google.de/advanced_book_search?hl=de)

---

</br></br></br>',
            ],
            [
                'id' => 125,
                'norm_iri' => '',
                'created' => '2019-01-12 10:42:00',
                'modified' => '2019-09-20 16:12:09',
                'published' => 0, 'menu'=>1,
                'segment' => 'wiki',
'format'=>'markdown',
'deleted'=>0,
                'name' => 'Abgeschlossen',
                'category' => 'F. Entwicklung',
                'content' => '# Abgeschlossene Entwicklungen und Korrekturen
## Geprüft - in Ordnung
* der Fehler auch in den anderen Registen; Einträge lassen sich verschieben, neu angelegte werden nicht gespeichert, stattdessen die Fehlermeldung, desgleichen beim Umbenennen von Einträgen; Löschen ist möglich
* im Register Formeln Zugriffsverletzung nach dem Neuanlegen und Speichern eines Eintrags; der Eintrag springt beim Einfügen in den Datensatz nicht in die vorgegebene, sondern in die nächste Zeile; nach dem Anklicken verschwindet er wieder; nach dem xten Versuch ha sich die ganze Anwendung aufgehängt.
Neuer Versuch: es stellt sich heraus, dass neu angelegte Einträge nicht gespeichert werden; Fehlermeldung:
Ausnahmefehler: Zugriffsverletzung bei Adresse 00404E08 in Modul \'Epigraf4.exe\'. Lesen von Adresse 190064DC;
Fehlertyp: EAccessViolation;
Sender: Form_HierachieList[TForm_HierarchieListe]
* bei Beschaffenheit-->Zustand und bei Maßangaben-->Bezeichnung wird die falsche Spalte angezeigt, bei Maßangaben-->Bezeichnung springt es beim Anklicken zur richtigen Spalte um, geht dann aber wieder auf die falsche zurück
* `Objektbeschreibung` und `Objektkommentare` sollten in `Beschreibung` und `Kommentar` umbenannt werden
* bei den Textabschnitten sollte das Feld `Name` in `Typ` umbenannt werden
* im Abschnitt `Beschaffenheit` soll `Materialien` in `Material` umbenannt werden
* im Abschnitt Datensatz sollte das Feld `Darstellung` in `Ausgabeoptionen für den Artikel` umbenannt werden
* im Abschnitt Signaturen verschwindet in der zweiten Spalte (Autor/Quelle) der Eintrag beim Anklicken; die dahinterliegende Liste ist nicht angebunden; beim Versuch, sie anzubinden, kommt eine Fehlermeldung
* dasselbe passiert bei Nachweise-->Referenz
* im Transkriptionsfeld soll in `bereich` die Möglichkeit vorgesehen werden, die Typ-Angabe zu entfernen, also wie bei den Registern durch Eintippen des Minus-Zeichens (Bindestrich) `leer` auszuwählen
* der Baustein `Wünschdirwas` bzw. `Wunschtag` sollte in `Abschnitt` umbenannt werden
* im Abschnitt Datensatz sollten die Felder Artikeltyp und Artikelnummer ausgeblendet werden
* in der Navigationsspalte sollte `Materialien` in `Material` umgenannt werden
* die Abschnitte `Material` und `Inschriftenträger` besser untereinander aber in doppelter Breite anzeigen, damit auch bei längeren Einträgen alles sichtbar wird
* beim Import aus Citavi sollten beim Auswählen der Datenbank standardmäßig alle Dateien *.* angezeigt werden
* Wenn man im Bereich Nachweise den Cursor in ein Feld der rechten Spalte (Ergänzungen) setzt, flackert der Cursor manchmal nur kurz, manchmal minutenlang, hört erst auf, wenn man nochmal klickt; in anderen Tabellel geschieht dasselbe
* Programmabsturz beim Versuch die Rolle eines Bearbeiters zu ändern. Zuerst erscheint ein Fenster mit oben Epigraf 4, darunter nur ein OK-Button; dann kommt ein Fenster mit folgenden Fehlermeldungen: <br/>
Ausnahmefehler: SQL Error: Lost connection to MySQL server during quera <br/>
Fehlertyp: EZDatabaseError <br/>
Sender: Form_EditBearbieter [TForm_EditBearbeiter]<br/>
danach Programmabsturz
* das Ausführen der Exportfunktion führt zum sofortigen Programmabbruch
    * beim Export aus der Liste immer noch Programmabruch oder man wird nur auf die Datenbanken-Seite geführt__ </br>
folgende Fehlermeldungen treten auf: Zugriffsverletzung bei Adresse 54C8FB8A in Modul \'libmysql.dll\'. Lesen von Adresse .... <br/>
SQL Error: MySQL client ran out of memory
    * Erneut bearbeitet, bitte prüfen.
* Wird eigentlich die Reihenfolge der flexiblen Abschnitte eim Export garantiert?
    * Ja
* bei den Textabschnitten sollte im Feld `Name` bzw. `Typ` der Eintrag als Kategorie aus einer Liste gewählt werden können
    * Es ist nun eine Liste hinterlegt, die allerdings noch nicht dynamisch ist. Welche Einträge sollen in die Liste aufgenommen werden?
* im Abschnitt Beschaffenheit ist das Feld `Farbe` verschwunden
    * Das ist wie besprochen bei den Abmessungen gelandet. Evtl. müsste die Bezeichnung noch angepasst werden (`Eigenschaften`?)
* der tag mit dem Text des Kommentars heiß nicht `kommentar` sondern `beschreibung`
    * das würde ich aus Konsistenzgründen auch so lassen: das Feld heißt in allen Textabschnitten beschreibung. Der Unterschied zwischen Kommentar und Beschreibung und anderen Textabschnitten ergibt sich aus dem Namen des Abschnitts.
* im Attribut @section des Elements `link` fehlt das Präefix `sections-`
* das Attribut @parent_section wird leer ausgegeben
* Artikel>Abschnitt Datensatz: die `Ausgabeoptionen für den Artikel` werden nicht exportiert
* im Abschnitt Datensatz sollte beim Feld `Darstellung` bzw. `Auswahloptionen` eine Mehrfachauswahl (analog zu `Materialien`) möglich sein
* Bei der Migration der Register:Fehlende Verweise nur mit Lemmata auf oberster Ebene abgleichen
* Das Navigationsfenster müsste in zwei Bereiche unterteilt werden, einen für die festen und einen für die beweglichen Abschnitte;
die Standardfolge im beweglichen Bereich sollte Beschreibung - Inschriften - Kommentar sein; ein neuer Abschnitt sollte unterhalb des markierten Abschnitts, anderenfalls am unteren Ende des Bereichs mit den beweglichen Abschnitten eingefügt werden
* Verschieben von Abschnitten: Buttons wie ehemals bei Textbausteinen wären gut
* Textbausteine zu Band umbenennen, aus der Artikelliste rausnehmen
* Bei der Migration der Register:Fehlende Verweise werden mehrfach angelegt
* die alfabetischen Fußnoten app2 werden aus der Datenbank je siebemal vollkommen identisch ausgegeben anstatt nur einmal
* Verweise auf Fußnoten auch intern
* Exportieren von Datensätzen, die nicht in der Standarddatenbank enthalten sind, funktioniert nicht
* NAme (siehe oben auf der Edit-Seite dieser Seite) zu Name korrigieren
* Fehlermeldung beim Speichern und Schließen des Standortregisters: <br/>
Ausnahmefehler: Zugriffversetzung bei Adresse 00404E08 in Modul \'Epigraf4.exe\'. Lesen von Adresse 650050DC. <br/>
Fehlertyp:EAccessViolation. <br/>
Sender: Form_Hierarchieliste[TForm_HierarchieListe]
  * Vermutlich mit alter Epigraf-Version, bitte neue Version prüfen
* wenn man auf der Startseite unter Projket von Alle auf ein einzelnes Projekt gehen will, geschieht gar nichts
  * Vermutlich mit alter Epigraf-Version, bitte neue Version prüfen
* nach dem Import aus Citavi kommt es nach dem Schließen des Dialogfeldes zu einer Fehlermeldung:
`Fehler beim Speichern: SQL Error: Lost connection to MySQL server during query`;
danach stürzt Epigraf ab
* es wäre vorteilhaft, wenn man in den Registern mehrere Einträge markieren und dann zugleich verschieben (nach oben, nach unten, ausrücken, einrücken) könnte
* der Abschnitt "Maßangaben" könnte umbenannt werden in "Maßangaben und andere Eigenschaften"
* Register: einen Eintrag kann man versehentlich mit einem Verweis überschreiben; das lässt sich nicht rückgängig machen; in der Übersicht erscheinen dann statt Lemma und Bezeichnung siehe und der Verweistext, im Bereich Detail werden die Angaben in in den Feldern Lemma und Bezeichnung ausgegraut.
    * Vorläufig funktioniert das wie folgt: den Text im Verweisfeld löschen. Ggf. muss vorher ein Lemma ausgewählt und dann gelöscht werden - das passiert, wenn der Verweis auf einen Eintrag mit leerer Bezeichnung verweist.
* Fehler beim Backup: mysqldump: Got error: 2026: SSL connection error: ASN: bad other signature confirmation when trying to connect
Backup nicht erfolgreich: Abbruch durch mysqldump.exe
* für die Backup-Funktion müssten die Komponenten noch bereitgestellt werden und ein Hinweis in der Installations-Hilfe erfolgen
    * Nicht mehr nötig, ist alles in den Installer integriert
* Speichern ohne Schließen in Listen
    * Das geht anscheinend schon lange, siehe Menü / Taste F10
* Bezeichnung der Textabschnitte in der Liste: "Text" in "Zwischentext" umbenennen
* CRW und CR2 -Bilder lassen sich nicht importieren
    * Evtl. dauert Upload lange?
* wo ist der Button für den Band geblieben?
    * Siehe Menü "Globale Daten"
* wenn man einen Textabschnitt löscht, kommt die Frage, ob man die darüber stehende Inschrift löschen will; wenn man das bestätigt, wird entweder darauf verwisen, dass die Inschrift nicht gelöscht werden kann, weil auf sie noch verwiesen wird, oder es wird eine Fehlermeldung angezeigt; wenn man verneint, wird der Löschvorgang korrekt abgebrochen
* https://epigraf.inschriften.net/epi3/inschriften_mv/files?path=articles%2FHWI%2Fhwi.heilgeist.ct001&token=KR4428L2ZNNJ8KM9PTD "Order erstellen" korrigieren zu "Ordner erstellen"
* das Sortieren der Abbildungen durch Anklicken der Spaltenköpfe funktioniert nicht
    * Funktioniert bei mir - allerdings nur aufsteigend und das war anscheinend schon immer so?
* in der Liste der Personennamen wird das Feld `Bemerkung` nicht angezeigt; die Felder `Externe Datenbank` und `Index in externer Datenbank` werden nicht mehr benötigt, `Bemerkung` aber schon.
    * Nachfrage: Das heißt, auch die Importfunktionen werden nicht mehr benötigt? Gilt das sicher auch für die anderen Arbeitsstellen?
* in hwi.heilgeist.gestuehl Abschnitt Zwischentext eingefügt, beim Schließen (ohne speichern) Fehlermeldung: Fehler beim Freigeben des Fensters: Zugriffsverletzung bei Adresse 00404E08 in Modul Epigraf4.exe. Lesen von Adresse FFFFFDE
* In der Pipline für den Datenexport aus der DB umbenennen: Objekte in Artikel, Index in Register, Text in Band
* in der globalen Liste Epitheta werden neu angelegte Einträge nicht am vorgesehenen Ort sondern am Ende gespeichert, in der Liste Sprachen funktioniert es aber korrekt
* backup nicht erfolgreich, abbruch durch mysqldump.exe
    * Das ist bei Umlauten im Backup-Pfad aufgetreten, sollte nun funktionieren
* Personenregister zweimal Klinkow - lässt durch Ein- und Ausrücken sich nicht zusammenführen bzw. nicht speichern
    * Vermutlich ein Fehler der internen Cache-Mechanismen. Ich habe diese vorläufig abgeschaltet. Dadurch könnte es zur Verlangsamerung beim Speichern (nicht nur im Register, auch an anderen Stellen) kommen - bitte berichten, falls etwas zu langsam wird.
* die Funktionen `Einstellung Hauptebene` und `einfache Liste` lassen sich nicht auf einzelne Listen, sondern immer nur auf alle anwenden (die in einer Liste vorgenommene Einstellung überträgt sich auf alle Listen)
    * Einstellungen müssten einmal für alle Listen überprüft und ggf. neu gesetzt werden
* im Abschnitt Marken fehlt die Möglichkeit zu scrollen - siehe hwi.heilgeist.gestühl
* im Bereich Fußnoten ist die Spalte für die Fußnotennummern bei dreistelligen Zahlen nicht breit genug (siehe Band/Textbausteine Stralsund)
* in den globalen Listen wird das Feld `Bemerkung` nicht gespeichert
* der Font Adobe Garamond Pro wird bei der Auswahl der Fonts nicht aufgelistet
* Systemabsturz (keine Rückmeldung) beim Bereinigen der globalen Listen: kurz hintereinander zweimal Absturz beim Versuch eine Liste zu öffnen (Material, Massangaben); Projektauswahl: Alle
    * Was ist mit `Bereinigen` gemeint?
    * Ich habe die Funktion zum Senden von Fehlerberichten (bugreports) wieder aktiviert. Bitte bei weiteren Fehlern einen Fehlerbericht senden, damit ich das nachvollziehen kann
* online-Abbildungen lassen sich aus EpigrafDesktop nicht öffnen
* Bugreport von Christine: "Entfernen eines leeren Inschriftenfeldes, in das keine Transkription eingetragen war."  Zugriffsverletzung nach Entfernen von Inschrift und Auswählen einer verbleibenden Inschrift.

* Ist es möglich, zu den beweglichen Textabschnitten noch vier Kategorien aufzunehmen:
    * `allgemeine Angaben: Inschrift(en) nach`
    * `allgemeine Angaben: Ergänzung(en) nach`
    * `allgemeine Angaben: Buchstabenhöhe(n)`
    * `allgemeine Angaben: Schriftart(en)`
    * Außerdem hätte ich `Zwischentext` gern umbenannt in `Gliederungstext`
* noch nicht behoben: im Datensatz Stralsund 021 kann man die Inschrift B (leer) nicht löschen
* gibt es die Möglichkeit, das (automatische) Sortieren in den Listen so einzurichten, dass die Verweise vorn stehen?
    * Verweise werden nun nach oben sortiert, als zweites Kriterium wird wie bei regulären Einträgen der Wert im Lemma-Feld verwendet. Das Lemma-Feld und das Bezeichnungs-Feld sind zu diesem (und vielleicht auch anderen Zwecken?) nun auch im Verweis-Modus beschreibbar.
     * funktioniert nur bedingt, siehe z. B. im Personenregister "Balthasar (von)" oder "Battus" oder "Bavemann" oder "Behr"
* in der Liste Maßangaben gibt es einen Eintrag ####Leer der nicht gelöschte werden kann (weil man ihn nicht herausfiltern kann; man müsste erst herausfinden, in welchem Artikel auf ihn verlinkt wird)
    * Siehe die Registerfunktion (oder den Menüpunkt Daten -> Abhängigkeiten finden, das ist aber langsam)
    * die Registerfunktion listet diese Liste nicht auf (weil es kein Register ist)
    * bitte noch einmal prüfen: sollte aufgelistet sein wie alle anderen Tabellen
* Im Register Sprache ist der Eintrag "keine Angabe/sprachlich unspezifisch" als ###UNDELETED markiert; er ist mit mehr als 400 Artikeln verknüpft. Kann man die Verbindungen via MySql automatisch lösen und den Eintrag löschen?
* Ausgabefehler bei der Verwendung von search-and-replace-Dateien: `Fehler: DOMDocument::load(): Input is not proper UTF-8, indicate encoding ! Bytes: 0x96 0x46 0x29 0x2E in /mnt/zdv.data/epigraf.inschriften.net/databases/inschriften_mv/export/project_20-job_367.xml, line: 4`
    * Beispiel: hwi.nikolai.ct206
     * hat sich (hoffentlich) erledigt
* Daensätze lassen sich nicht löschen: pch.dütschow.glocke1, pch.dütschow.glocke2,
`Ausnahmefehler:
SQLError: Lost connection to MYSQLserver during query
Fehlertyp: EZDatabase Error
Sender: JvToolBar [TJvToolBar]`
* für die Zwischentextabschnitte habe ich vergessen die Kategorie "Allgemeine Angaben: Maße" zu beantragen
* Datensätze lassen sich nicht entsperren: dbr.klosterkirche.statuen-magnus; dbr.klosterkirche.altar-tugenden; die Sperren traten auf, nachdem versucht wurde, zu große Bilddateien hochzuladen und einzubinden (das Hochladen wurde vom System zurückgewiesen, wegen der Größe)
* Das Ansteuern von Dateien im Pipline-Dialog von EpigrafWeb funktioniert nicht (man muss den Pfad händisch eintragen)
* Im Register Literatur sind die Titel nicht alfabetisch sortiert;
* Im Register Literatur erscheinen nur die im Abschnitt Nachweise eingefügten Titel, die über die Verweisfunktion verknüpften aber nicht
    * Gibt es noch weitere Register, wo Verweise eine Rolle spielen? Welche?
*  `Angelegt` `von` und  `Letzte Bearbeitung` `von` werden nicht ausgegeben:  <br/>
   `<created_by empty="true"></created_by>`
   `<modified_by empty="true"></modified_by>`
* in `section: beschaffenheit / fields / datierung` werden die Sonderzeichen falsch kodiert ausgegeben

* bei den internen Verweisen sollte A1, B2 usw. zur Auswahl nur angeboten werden, wenn es auch A2, B2 gibt
    * ein Beispiel wäre gut: wo ist das nicht der Fall?
    * alle Artikel, in denen Inschriften mit nur einem Inschriftenteil vorkommen (also die meisten), z. B. Artikel Stralsund Nr. 019

* wie gelange ich als Aministrator aber nur assoziierter Nutzer von inschriften_sanh auf die zugehörige Notizen-Seite?
    * Sollte nun funktionieren wie bei der Standarddatenbank (über das Menü). Was immer geht: von der Weboberfläche aus über Datenbanken -> [Datenbankname] -> Anzeigen -> Notizen
* Datenbank inschriften_mv, Register Literatur, 14 x #Fehler2 - nirgendwo eingebunden, kann gelöscht werden
* in der Liste Absatzformat verursacht der Versuch, einen Eintrag anzulegen, eine Fehlermeldung
* Register Personen: keine Untereinträge möglich, desgleichen Inschriftenträger, Sachregister usw.
* bei den Registern funktioniert die Funktion "Auswahlliste einschränken" nicht mehr
    * genau genommen funktioniert die Einbeziehung der Unterlemmata nicht
* bei copy & past wird der lange Gedankenstrich in einen kurzen umgewandelt

* wenn man einen Artikel geöffnet hat und auf die Startseite wechselt und F1 wählt, wird die Hilfgeseite zu dem entsprechenden Abschnitt des Artikels und nicht die Hilfeseite für die Starseite geöffnet
* Im Artikel-Fenster sollte im Abschnitt "Inschrift" die linke Spalte der Tabelle anstatt mit "Betreff" mit "Register" überschrieben werden.
* Sonderzeichen in der Signatur beim Anlegen von Ordner für Bilder entfernen, auch in EpigrafDesktop

* Menü "Ansicht": (1) "Projekte" umbenennen in "einzelne Projekte", (2)  "Alles anzeigen" in "alle Projekte" und direkt unter (1) platzieren
    * So ähnlich ("Alles anzeigen" hat eine andere Funktion als nur alle Projekte anzuzeugen, hiermit werden alle Filter zurückgesetzt)
* Artikel-Fenster >> Menu "Dateiablage": "Hilfsmittel verwalten" umbenennen ind "Dateien verwalten"
* Artikel-Fenster >> Menu "Datensatz" umbenennen in "Artikel"
* es gibt noch einige Inkonsistenzen mit den Bezeichnungen "Datensatz", "Objekt", "Artikel"; ich hätte das alles gern als "Artikel" benannt; die Vereinheitlichung ist wichtig für das Handbuch, das ich gerade konzipiere und schreibe; es betrifft folgende Bereiche
    * im Startfenster: Menü >> Bearbeiten:
     * Neues Objekt ==> Neuer Artikel
     * Datensatz bearbeiten ===> Artikel __öffnen__
     * Datensatz löschen ==> Artikel löschen
     * Datensatz nach ID öffnen ===> Artikel nach ID öffnen
    * Werkzeugleiste im Startfenster >> Tooltips der drei obersten Buttons
     * Neues Objekt ==> neuer Artikel
     * Datensatz öffnen ==> Artikel öffnen
     * Datensatz löschen ==> Artikel löschen
    * im Artikel-Fenster: Menü >> Datensatz ==> Artikel
* Menü "Ansicht": Letze 10 zu Letzte 10 korrigieren
* wenn man im Menü > Ansicht > Bearbeiter einen Berabeiter auswählt, kann man die Auswahl nicht rückgängig machen
* kann man das Feld "Wert" bei den Maßanghaben auf xml umstellen?  bei der Angabe des Schlagtons benötigt man mitunter eine hochgestellt Ziffer, die ich mit dem Werkzeug "Hochgestellt" (Strg+H) erzeuge
    * Ja, mit Nebenwirkungen: auch Schrifthohen.Wert wird beim Exportieren als XML-Feld behandelt
* Menü "Export": Artikel Exportieren, Band Exportieren ==> exportieren am Anfang klein schreiben!
* kann man den Namen der Datenbank ausgeben?
* Frage: wie lange bleiben die Export-Dateien auf dem Server liegen, wann werden sie gelöscht?
    * Bei jedem Exportvorgang werden im Exportverzeichnis der jeweiligen Datenbank alle Dateien gelöscht, die älter als 24 Stunden sind.
* kann man den Namen der Datenbank ausgeben?
* in der db inschriften_sanh, artikel band, sind die ersten 18 Fußnoten mit keiner textstelle  (app1) verknüpft und bis auf die erste leer
  * Habe ich gelöscht. Inhalt der ersten Fußnote habe ich in das Notizfeld kopiert.
* bei den Pipelines werden die in der Spalte "Output" unterhalb der Category "Daten" voreingestellten Markierungen nicht mehr ausgegeben/angezeigt

# eingefügt am 20. September 2019:
## April 2019
* der grüne "siehe"-Link ist nicht mehr interaktiv
* es werden so viele blaue "Verweis unter"-Links mit demselben Sprungziel angezeigt, wie "siehe"-Links unter dem Sprungziel vorhanden sind; dies geschieht, sobald man im Register eine Veränderung vornimmt; nach dem erneuten Öffnen des Registers ist alles wieder in Ordnung, d. h. es werden keine überflüssigen "Verweis unter"-Links angezeigt, nach der ersten Veränderung geht das Spiel aber von vorn los
* beim Öffnen einer Liste oder eines Registers werden auch alle anderen gesperrt
* mit der aktuellsten Version von Epigraf4.1 kann ich mit meinem Administrator-Account nicht mehr nach dem Wechsel in eine andere Datenbanken exportieren
* wenn man einen neuen Artikel anlegt und ihn speichert ohne zu schließen oder wenn man den Band-Artikel öffnet und speichert ohne zu schließen, wird in der Tabelle des Startfentensters eine Zeile angelegt, die eine Fehlermeldung zeigt: "ERROR: Zugriffsverletzung bei Adresse ..... in Modul \'Epigraf4.exe\'. Lesen von Adresse  ....." ; wenn man dann auch F5 drückt, hängt sich das Programm auf
* die  Dropdown-Liste Datenbereich (im Band-Fenster) wird nicht sortiert  angezeigt
  JJ: Die Sortierung richtet sich nach dem Lemma

## März 2019
* Übersicht: Spalte Artikelnummer entfernen
* Sonderzeichen  >> ■ [=Blockade] ==> Shortcut Shift+Strg+B zuordnen

## März 2019
* Band: "Datenbereich"-Label wegnehmen

## Februar 2019
* doppelte markenliste (marken, meisterzeichen), siehe hwi 2443
* Speichern von Wappen fehlerhaft? Siehe Testprotokoll
* Marken: Die Zuordnung zu Wappen ist im Wappen-Abschnitt unwirksam.
  JJ: konnte ich leider nicht nachvollziehen...was wäre denn erwartet, was im Wappen-Abschnitt passieren soll?
* Abbildungen: Sortierung der Tabelle funktioniert nur bedingt und je Spalte nur in eine Richtung
  JJ: was heißt bedingt? -> Überarbeitung der Sortierfunktion ggf. in Erweiterungsliste aufnehmen
* Abbildungen: Spalte Abbildungsteil ist unwirksam
* Abbildungen: Button Neues lokales Bild ist unwirksam
* Abbildungen: Button [...] ist unwirksam für lokale Bilder
* Abbildungen: Button Ordner öffnen öffnet den Ordner des Benutzerkontos
* Abbildungen: Button Bild öffnen mit öffnet das Dialogfenster für die Auswahl der Anwendung, es passiert aber nichts
* Übersicht, Menü Notizen und Toolbar: Notizseiten mit Sonderzeichen im Titel werden als leere Zeilen angezeigt
## Januar 2019

* EpigrafDesktop >>> Ebene Inschriften >>> Spalte Datierung der Bearbeitungen: ohne Anzeige

* test_mv41: hwi.apotheke.mörser-1592 in den Links zum Kommentar ist ein verwaister Link auf den Literaturtitel "StA Greifswald – Stadtarchiv Greifswald", der dort nicht eingetragen war (oder nur versehentlich kurz angeklickt wurde).
  JJ: habe ich gelöscht, es waren sogar zwei Links

* Literatur >> Abhängigkeiten finden: verweise 122034 - wird kein Artikel geöffnet (betrifft hwi.museum.wappentafel, Fußnote 2, "löschen - AH Wismar, Fotosammlung")
  JJ: Öffnen von Verweisen war bislang nicht vorgesehen, habe ich jetzt nachgerüstet

* desgleichen verweise 132962 ("###LEER")
    JJ: Öffnen von Verweisen war bislang nicht vorgesehen, habe ich jetzt nachgerüstet

* Listenfenster Wappen: das Aufziehen der Spalten sollte wie im Starfenster mögliche sein (der momentane Modus ist nicht intuitiv)

* Listenfenster: das Feld "Lemma" ist zu groß, "Bemerkung" dadurch zu klein

* EpigrafDesktop >> Register-Fenster: das Feld "Lemma" um ca. ein Viertel verkleinern und den freiwerdenden Raum dem Feld "Bemerkung" zuschlagen
  JJ: Habe ich so angepasst, dass der Platz zwischen Lemma und Bemerkung aufgeteilt wird.

* EpigrafDesktop,>> Artikelfenster>> Abschnitt Marken: Überschrift nicht "Marken und Meisterzeichen", sondern nur "Marken"

* Artikelfenster >> Abschnitt Marken:
    * das Entfernen einer Marke führt zu Fehlermeldung; die Marke verschwindet nur temporär aus der Anzeige im Markenfenster, wird aber beim nächsten Aufsuchen des Markenfensters wieder angezeigt (wird also nicht entfernt)
- EpigrafWeb: Marken sind pixelig

* Frage: wenn man in der Konfiguration für die Marken keinen Pfad angibt, kommen sie dann aus EpigrafWeb?
  JJ: Ja, genau!

* Startfenster >> Ebene Inschriften: Filter funktioniert nicht
- In Artikeln die Zuordnungen im Register immer auf Teile
- Dialog zur Auswahl von Online-Dateien: Filtermöglichkeit

- Cachen von Marken verbessern
- Marken lassen sich nicht scrollen

- Fehlende Markenbilder: abgleichen, unzulässige Zeichen in Dateinamen ersetzen (Fehlermeldung beim Export in EpigrafWeb)

  JJ: In der Datenbank `test_mv41` habe ich alle Dateinamen von Marken in Datenbank und im Dateisystem auf Kleinschreibung umgewandelt und alle laut Konvention unerlaubten Zeichen entfernt. Punkte habe ich entgegen der Konvention überall gelassen. Der Abgleich zwischen Dateisystem und Datenbank ergibt 48 fehlende, 64 ungenutzte und 604 korrekt eingebundene Markenbilder, siehe `test_mv41/marken/marken.files.abgleich.xlsx`.

* Marken einfügen: Shortcut Strg+M


- Literatur: für die Umstellung eine Liste aller Literaturtitel bereitstellen

  JJ: siehe `databases/inschriften_mv/biblio/inschriften_mv.properties_literatur.xlsx`
  Diese Datei enthält die Literaturtabelle von inschriften_mv (Stand 13.1.2019)

- Normdaten-Feld in Listen vergrößern
- Textabschnitte: Freitextfeld im Artikel ausblenden

- Marken im Artikel schmaler darstellen
- Beim Ändern von Marken: vorherige Marke auswählen
- Neue Versionsnummer

- Literatur: Index wurde bei Migration nicht übernommen, ergänzen

* Piplines >> Radiobuttons: eine Buttonfolge sollte nur ein Attribut und darin für jeden Button einen spezifischen Wert setzen; bei zwei Buttons zwei Werte (z. B. 0|1), bei drei Buttons drei Werte (0|1|2) usw.

* Artikelfenster: Zwischentexte: das Feld "Name" soll entfallen

Ebene Inschriften - es fehlen Spalten für:

* Inschrift (A, B; A1, A2 usw.)
* Signatur
* Zustand (Überlieferungszustand)

- Die Spalte Abschnittsnummer ist nicht relevant.

Ebene Inschriften in Übersicht:

* es gibt drei fest eingestellte Spalten: Titel, Status Zustand; alle drei bleiben leer
* wenn  man aus der Liste die Spalten Titel und Status wählt, werden sie nochmal angelegt und füllen sich auch
* die Spalte Zustand lässt sich (obwohl angezeigt) nicht abwählen

Bandfenster:

* Die Liste in der Auswahl "Kategorie" sollte nicht dieselbe sein, wie diejenige im Artikelfenster für die Zwischentexte. Mein Anliegen war hier eine Key-Liste abzulegen, mittels derer man andere Datenbereiche ansteuern und einbinden kann, z. B. die Register, das Literaturverzeichnis, Marken usw. - als Ersatz für das Tag "key", das ich bisher dafür benutze. Schau dir mal den "Band" zum Projekt Wismar an. Die key-Liste ist eindeutig technisch angelegt und referenziert in den Bezeichnugen auf die Terms die aus der Datenbank kommen. Die im Artikelfenster mit den Kategorein der Zwischentexte zu vermischen, wäre für die Nutzer/Bearbeiter doch sehr irritierend. Statt "key" könnte es sicher noch einen bessern Ausdruck geben, ich denke darüber nach.

## Dezember 2018
* in der Bildverwaltung werden im Fenster "Öffnen" (zu öffnen mit dem Button mit den drei Punkten) CRW- und CR2-Bilder nicht angezeigt, ebensowenig PDFs.
* Kann man es einrichten, dass beim Einfügen einer neuen Inschrift gleich ein Inschriftenteil und eine Bearbeitung angelegt werden, und beim Anlegen eines neuen Inschriftenteils zugleich eine Bearbeitung?
* Bildanzeigefenster nicht mehr standardmäßig als immer im Vordergrund öffnen (führt manchmal zu Konflikten mit geöffneten Befehlsfenstern, die eine Eingabe verlangen, aber von der Bildanzeige verdeckt werden).
* Die Funktion "Abhängigkeiten finden" fehlt im Dialogfenster Literatur

## Jahr 2017
* dieses Problem ist nur teilweise gelöst: Literaturverweise  die nur in der Wappendatenbank enthalten sind, können nicht aufgelöst werden, weil es keine Links-Liste dafür gibt und sie auch nicht im Literaturregister aufgeführt werden; siehe `<rec_lit id="000004205352725342592592602797" value="Siebmachers Wappenbuch"></rec_lit>` oder hgw 056
    * diese Verweise können im Artikel und auch im Wappenregister aufgelöst werden, erscheinen aber weder im Literaturregister des Epigrafen noch im Literaturregister der Textausgabe (Rohdaten, properties_literatur)
    * Für das Literaturregister von EpigrafDesktop gibt es keine schnelle Lösung. Das ließe sich aber im Zusammenhang mit Paket 3 einbauen (Erweiterung der Recherchefunktionen auf Worttrenner, Buchstabenverbindungen und Satzzeichen). Im Export sollte es jetzt enthalten sein, bitte prüfen. Im angegebenen Datensatz (hgw 056) war m.E. schon vorher alles vollständig.
* wie schon in Epigraf 3 tritt auch jetzt noch das Problem auf, dass bei Einfügen von Wappen dies nicht immer gespeichert wird, insbesondere gibt es das Proböem, dass das Speicher bzw. Aktualisieren nicht erfolgt, wenn man ein Wappen verändert (in der Auswahlliste ein anderes wählt)
* auch mit Epigraf4 sind wieder Fälle aufgetreten, bei denen das Anlegen von Fußnoten nicht korrekt verläuft, siehe inschriften_sanh, wb.friedhof.gm-heiligengeists; hier waren zwei Fußnoten nach dem Anlegen zwar noch sichtbar, konnten aber inhaltlich nicht mehr bearbeitet werden; nach dem Löschen sind sie zwar in Epigraf unsichtbar, werden aber als leere Fußnoten ohne Referenzzeichen im Text ausgegeben
  * Ich kann das nicht nachvollziehen: es sind drei Fußnoten in EpigrafDesktop zu sehen und genau die gleichen drei Fußnoten sind auch in EpigrafWeb und im Export zu sehen
* bei internen Verweisen auf Fußnoten kann die Fußnote, auf die verwiesen wird, nicht gefunden werden; man gelangt über die id des verweises auf links/item aber von dort nicht weiter
  * Bitte prüfen. In diesem Zug habe ich die Verweise auf Artikel verändert, weil das nicht konsistent war. Voher stand die ID des Artikels im Attribut "link_sections". Nun gibt es je nach Art des Verweises drei Attribute: link_articles, link_sections,link_fussnoten. Die Mischung aus Deutsch und Englisch ist hier zwar unschön, das habe ich aber dummerweise bereits in der Datenbank so gemischt.
*  __WICHTIG__: im Wappenfenster kann man nicht sortieren und es sortiert sich auch nicht mehr automatisch
*  __WICHTIG__: Artikelfenster >> Abschnitt Wappen: in der Tabelle werden die Wappen nicht korrekt sortiert: W1, W10, W11 usw. bis W19; dann W2, W20, W21 usw. [hwi.heilgeist.gestuehl]
*  __WICHTIG__: wenn man in einem Textfeld eine Ziffernfußnote anbrigen will, kann es passieren, dass die Fußnote nicht an der Stelle bleibt, an die man den Kursor zuvor gesetzt hat, sondern an den Anfang des Felds springt; erst bei der zweitenFußnote bleibt diese dort, wo man sie haben will - dass passiert nur in einigen Artikeln, ich kann nicht erkennen, wovon es abhängt oder ausgelöst wird - siehe hwi.georgen.ct041/kommentar; hwi.georgen.ct043/kommentar; hwi.georgen.ct121/kommentar

* Öffnen von Menüs mit Alt+: Die Unterstreichung der entsprechenden Buchstaben, mit denen die Menüs geöffnet werden können, erscheint erst nach Drücken der Alt-Taste ==> folgt der jeweiligen Windows-Version
* Artikelfenster >> Abschnitt Inschrift >> Schrifthöhe. wenn man in der Spalte "Eintrag" eine Eingabe vornimmt und dann in das Feld darunter klickt, um eine weitere Eingabe vorzunehmen, versucht Epigraf ein Dropdown-Listenfeld darzustellen und gibt dann eine Fehlermeldung aus; wenn man in das Feld "Ergänzung" klickt, wird die neue Zeile korrekt angelegt .
* __WICHTIG:__ Die Wappentabelle lässt sich aus dem Artikelfenster heraus nicht mehr über den blauen Pfeilbutton öffnen
* gleich noch sowas: es wurde der Wunsch geäußert, dass, wenn man zwecks Export auf EpigrafWeb geleitet wird, der Fokus automatisch auf der Schaltfläche "Start" liegt, damit man ohne Mausnutzung gleich starten kann


* Volltextsuche >> Button "Desktop" ==> Verbindungsfehler',
            ],
        ];
        parent::init();
    }
}
