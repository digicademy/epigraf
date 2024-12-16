<?php
declare(strict_types=1);

namespace Epi\Test\Fixture;
//bin/cake bake fixture -c projects --table notes --records Epi.Notes --conditions "id IN (2,4,5,6,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22)" --count 100
use Cake\TestSuite\Fixture\TestFixture;

/**
 * NotesFixture
 */
class NotesFixture extends TestFixture
{
    /**
     * Database connection
     *
     * @var string
     */
    public $connection = 'test_projects';

    /**
     * Fields
     *
     * @var array
     */
    // phpcs:disable
    public $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'version_id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'deleted' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'published' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'menu' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => true, 'default' => null, 'comment' => ''],
        'modified' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => true, 'default' => null, 'comment' => ''],
        'name' => ['type' => 'char', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'category' => ['type' => 'string', 'length' => 300, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'content' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'format' => ['type' => 'string', 'length' => 15, 'null' => false, 'default' => 'markdown', 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        '_indexes' => [
            'published' => ['type' => 'index', 'columns' => ['published'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'MyISAM',
            'collation' => 'utf8mb4_unicode_ci'
        ],
    ];
    // phpcs:enable
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 2,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2017-10-30 11:34:43',
                'modified' => '2021-08-19 06:40:43',
                'name' => 'Wismar',
                'category' => 'Wismar',
                'content' => '



####Chat
1. hwi.marien.ct249
    + Literaturangabe in Anm. 3 präzisieren (in AUB vorhanden)
1. marien.ct+65
    * MD: Maße sind jetzt in Notizfeld
    + wann und wo wurden die Fragemente eingelagert ?
    * zugewiesen an: JH
1. heilgeist.steintafel
    * MD, CM: Ausgabefehler, derselbe Fehler auch in hwi.marien.ct303, hwi.marien.glocke03, hwi.georgen.glocke1 (MD)
    * zugewiesen an: JH
1. dominikaner.tafel
    * 	CM: Schriftart einfach eine frühe(!) Got. Min. mit wenigen Brechungen, die Crull nicht kannte und die er deswegen als "Cursive" bezeichnet hat?
    * zugewiesen an: JH
1. georgen.ct289
    * CM: A (1461) und B (1464) von demselben Steinmetz,  gleichzeitig entstanden --> beide von 1464
    * zugewiesen an: JH (zu A1 und A2 ändern)
1. Nachgetragene Sterbedaten sollen in der Kopfzeile mit einer eigenen JZ berücksichtigt werden. Vgl. m.ct158, n.ct021, n.ctSK16... --> prüft/vereinheitlicht Jürgen
1. Bei Nachweisen von Crull/Techen Seitenzahlen mit Nr. (in Klammern) angeben. --> Prüfen und vereinheitlichen.
    * zugewiesen an Philipp
1. Bei Nachweisen von Crull/Techen Seitenzahlen mit Nr. (in Klammern) angeben, ggf. ergänzen (s.o.); dazu: Der Klammergebrauch gilt auch für alle andere Lit. mit Seiten- UND Nr.-angaben --> vereinheitlichen, Zweifelsfälle notieren (Name, Titel, Datensatz)
    * zugewiesen an Philipp

### Besprochen und entschieden:
__Editionsteil__
1. Zusammenschreibung nach modernem Gebrauch, wenn aber dann Großbuchstaben mitten im Wort stehen würden, wird Worttrennung wie in der Überlieferung gelassen; Bsp.: marien.tafel-sperling: Tausent Sechs Hundert Siebenzehn
2. y mit Trema aus kopialer Überlieferung wird ohne Trema wiedergegeben, bei Überlieferung im Original wird das Trema beibehalten
3. (Namens-)Ergänzungen von CT werden in der Edition wiedergegeben, wenn die Ergänzung nachvollziehbar ist. Wenn die Ergänzung nicht nachvollziehbar ist, wird sie in Anmerkungen wiedergegeben. Wenn die komplette Inschrift nach CT, weil nicht mehr vorhanden, im Editionsteil; es gibt einen Unterschied zwischen Ergänzung nach(!) Crull und Techen und Ergänzung von(!) Crull und Techen (d.h. schon bei C/T in eckigen Klammern).
4. Interpunktion von Crull/Techen bei neuzeitlichen Inschriften wird nicht übernommen, bei mittelalterlichen schon (sofern überhaupt vorhanden); das gilt für Grabplatten, für Epitaphien evtl. abweichendes Verfahren
5. V. S. E. soll immer in der Edition aufgelöst werden, nicht in einer Bu.-anmerkung: V(ND) S(EINEN) E(RBEN). --> Wenn aus der I. niederdt. Sprache ersichtlich, dann auch Auflösung in niederdt. Formen; sonst neuhochdt.-modern - z.B. (H(ERRN)) - auflösen. --> erl.
6. Jahreszahlen: nur nach m für tausend und nach den Jahrhunderten eine Leerstelle setzen.
7. In der Edition von sehr schlecht erhaltenen Inschriften werden entweder a) Unterpunkte gesetzt oder b) Wortlaut nach C/T in [...] wiedergegeben. Das bleibt so, wie es ist!
1. Worttrenner werden generell(!) als hochgestellte Punkte wiedergegeben; z.B. g.ct157, ct118. Anders: Punkte auf Grundlinie, die Funktion eines Satzzeichens haben.
9. Archivalien, nach denen Inschriften wiedergegeben/ergänzt wurden (vgl. m.ct269, Begräbnisbuch St. Marien; n.ct174, Begräbnisbuch ...), sind keine Inschriftenüberlieferung und werden daher NICHT in die Nachweisezeile aufgenommen (Jürgen).
1. Namensinitialen, deren Bedeutung/Auflösung sich erschließen lässt (aus anderen I. oder aus Grabbüchern), einheitlich in der Edition auflösen (nicht in Anm.); vgl. m.ct158, m.ct199 A, g.ct263 B, g.ct067 C, m.ct247 B u. C, m.ct+39 B, g.ct174 ... Es ist ok, wenn die Nachweise dann in Form von Grabbuch-Zitaten im Komm. stehen.
1. Lat. Eigentumsvermerke als Namen im Genitiv werden übersetzt als "Des N. N. und ...". - Pronomen "seinen" bei folgendem "heredum" wird mit übersetzt, auch wenn es im lat. Text nicht dasteht. --> Erledigt CM 26.1.2021: m.ct263, g.ct031, m.ct062, m.ct+57, g.ct007, m.ct145 ... [NB: Dt. Eigentumsvermerke, wenn ausgeschrieben, haben "und seinen Erben", also Dativ; so auch in Editionen vereinheitlicht.]
10. Zur Info: Crull/Techen geben Inschriften in got. Min. in Frakturtype wieder, Inschriften in Kapitalis in Antiqua (aber Kleinbuchstaben)
8. Jürgen: Prüfen, ob in allen Inschriftenbeschreibungen die Worttrenner besprochen werden und ggf., ob sie mit den Tags im Editionsteil übereinstimmen. Das scheint je nach Standort unterschiedlich gehandhabt worden zu sein.

__Namenskonventionen für historische Personen__
1. Bei Personennamen wird zunächst die Namensform verwendet, die in der Inschrift vorkommt. Kommen zu derselben Personen mehrere Namensformen vor, muss man sich für eine entscheiden und darf in allen Artikeln nur diese eine verwenden. Bei Familiennamen können mehrere Formen bzw. Varianten im selben Registereintrag verzeichnet werden. Im selben Artikel sollte aber nur eine einzige Form verwendet werden, gegebenfalls kann man eine Variante in Klammern dazu geben. Vor- und Nachnamen, die auf _-man_ enden, werden zu _-mann_ normalisiert.
2. Namen zu Anfang der Beschreibung, "Grabplatte für ..." - Namen stimmen nicht immer denen im Editionsteil übereinstimmen (n.ct173). Anderer Fall: Nachname in Klammern, weil er nicht in der Inschrift steht (m.ct219). --> In der Inschrift abgekürzte und für die Ed. aufgelöste Namen werden in der Beschreibung vollständig genannt.
3. Erschlossene Nachnamen, die nicht in der Inschrift stehen, können in der Beschreibung in Klammern gesetzt werden.
4. Referenznamen (Hauptform, vgl. Register): Wenn der Name in den Inschriften von der Form des Referenznamens abweicht, wird in der Beschreibung / bei der ersten Nennung im Artikel die Referenzform UND die Variante in Klammern angegeben; sonst nur der Referenzname; Beispiel "Ahasver (Asschweries) Meincke".
5. Namen von noch existierenden Adelsfamilien werden nach heutigem Gebrauch angesetzt; vgl. Winterfeldt (nicht -feld), Königsmarck (nicht -mark), vgl. m.ct095 ... [evtl. nochmal prüfen]
1. Unsichere Namen: nach unsicheren Namensformen steht erst ein Spatium, dann "(?)" --> erl. CM 11.02.21

__Terminologie__
1. hl. Johannes: Als Namensformen gelten Johannes Bapt. und Johannes Ev.
2. Terminologie Genien (eigentl. antike Schutzgötter) vs. Putten (Barock: nackte kleine Engel oder Engelsköpfe): "Genien" nur verwenden, wenn es einen größeren antiken (stilistischen, mytholog.) Kontext gibt.
7. Grabbuch vs. Begräbnisbuch: es heißt Grabbuch (nicht Begräbnisbuch) --> vereinheitlichen (erl. CM)
1. "Wangenstein" oder "Beischlagwange"? --> Beischlagwange (erl. CM)
1. Quadrangel: Nominativ Sgl.: das Quadrangel, Nominativ Pl. die Quadrangel, ABER Dativ Pl. Quadrangeln (vgl. Deklination v. Engel) --> vereinheitlichen (erl. CM 8.2.21)

__Übersetzungen__
1. Übersetzung von "beatus" bei Tagesangaben in "heilig" - statt selig - ändern.
2. Übersetzungen von Quellenzitaten werden zwischen einfache Anführungsstriche \'...\' gesetzt.
3. Bei Datierung nach Heiligentagen darf statt z. B. "am Tag des heiligen Martin" auch "am Martinstag" übersetzt sein, beide Übersetzungsmöglichkeiten sind ok
4. Gemäß Mail Frau Schröder (17.9.2020) Übersetzungen der ndt. Fürbittformel ändern: "Dass Gott ihm gnädig sei." --> "Gott sei ihm gnädig." --> Erledigt CM für alle Stellen, die durch automatische Suche zu finden waren.

__Zitate__
1. Betr. Zitate aus älterer Literatur: Vor Erscheinungsjahr 1800 wie Quellenzitate behandeln, also kursiv setzen; nach 1800 wie Zitate aus der modernen Forschungsliteratur in  "..." setzen. --> Bsp. marien.gewölbe-1607, franziskaner.gp-beatrix, marien.ct243
2. Werktitel kursiv, ebenso Zitate aus Primärtexten (Quellen) --> m.glocke09
3. Zitate a.d. Lutherbibel "diplomatisch" wiedergeben, d.h. wie in der TB-Ausgabe nach dem Druck 1545 (dtv-Ausgabe), d.h. mit Virgeln, Versalien, v für u usw.
__Parameterzeile__
1. Maßangaben sollen in der Form "11 cm" (nicht "11,0 cm") ausgegeben werden

__Abkürzungen__
1. Jahrhundert wird im Fließtext eher ausgeschrieben, in Stichpunktartigem eher abgekürzt, muss aber nicht ganz konsequent sein
2. zwischen Abkürzungen aus zwei Wörtern steht ein Leerzeichen: z. B., u. a.

__Standorte__
1. Betr. Standorte OFM-Kloster und OP-Kloster: Aus beiden Orten stammen Inschriften, die nach der Klosterzeit angefertigt wurden, dennoch soll als Standort "Dominikanerkloster" bzw. "-kirche" und "Franziskanerkloster" bzw. "-kirche" angegeben werden, weil das auch in späterer Zeit die ortsübliche Bezeichnung ist. Das Patrozinium soll nur in der Einleitung angeführt werden.

__anderes__
1. "s." und "s. a." für "siehe" und "siehe auch" soll ausgeschrieben werden. --> erl. Philipp 28.01.21
2. Bei Nachweisen von Crull/Techen Seitenzahlen mit Nr. (in Klammern) angeben, der Klammergebrauch gilt auch für alle andere Lit. mit Seiten- UND Nr.-angaben --> für C/T erledigt CM - prüfen für andere Titel
3. "AHW, Zeugenbuch 2 (1430–1490), p. 60" - das "p." soll NICHT durch "S." ersetzt werden.
4. Interpunktion in Fällen wie "Nach Schlie, Geschichts-Denkmäler 2, S. 98 wurde 1462 ..." --> kein Komma nach der S.-zahl.
5. Tagesdaten zu Quellennachweisen in Anm. werden nach dem Schema "13. Juli 1615" (nicht: "1615 Juli 13") wiedergegeben.
6. Anführen von einzelnen Inschriften v. a. im Komm.: Wenn der Buchstabe der Inschrift syntaktisch nicht notwendig ist, steht der Buchstabe in Klammern, wenn er aber notwendig ist, ohne Klammern im fortlaufenden Satz.
8. Quellenzeile:  wird chronologisch sortiert --> prüfen, Philipp (ab 28.1.2021)
10. Bei Verweisen auf Lexikonartikel wird der Kurztitel des Lexikonartikels in die Literaturliste aufgenommen (nicht der Lexikontitel), beim Nachweis des Artikels  steht nicht "Art.", sondern Autorname und Titel des Artikels. In einer Fußnote NICHT auf Online-Ausgabe verweisen, z. B. bei ADB, NDB  -- Beispiele (20.1.2021, müssen noch korrigiert werden, dafür Literaturliste mit ausgeben): 1. franziskaner.gp-beatrix, dort "Wolfgang Huschner, Heinrich II. (der Löwe), in: Pettke (Hg.), Biographisches Lexikon 6, S. 156–162, hier S. 156" -  "Art. Latomus, Bernhard (Karl Ernst Hermann Krause), in: ADB 19 (1884), S. 753, auch https://de.wikisource.org/wiki/ADB:Latomus,_Bernhard (31.7.2018)" - 2. m.ct154, dort "Art. Gronovius, Johann Friedrich in: ADB und NDB, online http://www.deutsche-biographie.de/sfz23943.html" - 3. wandmal.tidekap1, dort "Liselotte Stauch, Walther Föhl, Art. Baum in: Reallexikon zur Deutschen Kunstgeschichte (RDK) 2, Sp. 63–90, hier Sp. 67" - 4. g.altar-thomas, dort "Ulrike Mörschel, Art. A[lbert] v[on] Brescia, in: Lexikon des Mittelalters 1, Sp. 288" - 5. m.epitaph-schwartzkopff, dort "Joachim Kramer, Art. Phönix, in: LCI 3, Sp. 430–432"

### Offene Fragen u. Korrekturen
1. Welche Recherchen müssen unbedingt noch in HWI erfolgen? Lässt sich das durch eine Fahrt abarbeiten??? - Einzelne Angaben (Maße, Ausführungsdetails) könnten im Vorwort des DI-Bandes durch einen Satz zu pandemiebedingten Einschränkungen ab März 2020 erklärt werden.
1. Städt. Beschauzeichen ergänzen (Mona): kirchner1634, schenkkanne-1637
1. Schreibung von Zahlen (Geldbeträge usw.): jetzt noch uneinheitlich, mal als Wort, mal als Zahl. Üblich wäre: bis 12 ausschreiben, danach nur runde Zahlen (zehn, zwanzig etc.).
1. Bef.-Nr.: Sollen Bef.-Nr. einheitlich mit vierstelligen JZ wiedergegeben werden? jetzt mal in der Form 7/07 (m.ct229, m.ct324), mal 9/2007 (m.ct232, m.ct227), mal nur Nummer (Bef.-Nr. 77, so m.ct+71, g.ct040 ...).
1. Ist es korrekt, dass Inv.-Nr. des Stadtgeschichtlichen Museums mal nach dem Muster "Inv.-Nr. 5316 KE", mal in der Form "Inv.-Nr. EB 57/01" wiedergegeben werden?
1. Wiedergabe von Kürzungspunkten aus der kopialen Überlieferung, z. B. für Bibelstellen: Punkte beibehalten oder tilgen? vgl. marien.taufe (A), altarleuchter -1630-2 (S oder S.?)
1. Ausgabe "Reimverse, dt." oder "Deutsche Reimverse"?
1. Formatierung von Kirchenliedern und Archivalientiteln - kursiv, in einem oder zwei Anführungsstrichen? noch uneinheitlich, siehe epitaph-1615, Anm. 2, 3 u. 4; m.ct249 (...); s. dazu auch oben, Zitate: Werktitel sollen kursiv sein ...
1. Artikel Jürgen: zu Kapellen evtl. Hinweise auf Grundrisse bei Grewolls, Kapellen nachtragen
1. Glocken: für die Bearbeitung auch Eichler, Handbuch der Stück- und Glockengießer (Zi. 2.12) benutzen, Titel kommt bislang nicht vor.
1. Textkrit. Anmerkungen: Wie wird "Zu ergänzen ist ..." benutzt? Aktuell: auch - natürlich nicht nur - für verkürzte Formeln, die aber nie in ganzer Länge  ausgeführt waren, wo also gar kein Schriftverlust vorliegt; siehe z. B. dominikanerglocke1 (Anm. b), n.ct130, dominikaner.chorinschrift (Anm. w: "Zu ergänzen ist dem Sinn nach ..."; g.ct001+, hwi.fürstenhof (C). Vorschlag: statt "zu ergänzen ist ..." ggf. "Für ..." verwenden?
1. Sonderfall dazu: orate [pro eo]: Es gibt viele Fälle, in denen als kürzestmögliche Form "orate" für ausreichend gehalten wurde, "pro eo" war offenbar entbehrlich. Trotzdem Anm. in der Form "Zu ergänzen ist pro eo." machen???
1. Schriftbezeichnungen "Gotische Minuskel mit Versalien - Gotische Minuskel mit Versalien in Versalschrift - Gotische Minuskel mit Fraktur-Versalien - Gotische Minuskel mit frakturartigem Versal" ...: Mir scheint, dass das nicht sauber getrennt ist, weil es sich nicht trennen lässt. Dasselbe wird nicht immer mit demselben Begriff bezeichnet; außerdem sind Versalien ja relativ frei gestaltbar. Es sei denn, es handelt sich eindeutig um Kapitalis-Versalien, aber auch hier gibt es Übergänge zur Frühhum. Kap. Vgl. n.ct243 (B), museum.gp-kladow (B), heilgeist.ct001 (B), g.ct203 (C), n.ct014+, n.ct016+, n.ct194 (A) ... Vgl. zum Thema Kohn, in: Epigraphik 2000, S. 65ff.
1. Worttrenner, Gestaltung der: Stichproben(!) haben Diskrepanzen zwischen Beschreibung und Tags im Editionsteil ergeben. Vgl. g.ctSK07: Worttrenner als "unbestimmt" getagged, in der Beschr nicht erwähnt - ich sehe viele Doppelpunkte; m.ct064, A: "Rauten" laut Beschreibung, Tags "Quadrangel"; m.ct+03: ein Quadrat und eine Raute in der Beschr., tags: Quadrat, Quadrangel, Raute; n.ct257 (C) Quadrangel vs. Hochpunkt ...
1. Namensregister, Überprüfung: Formen der Familiennamen prüfen. Welche Formen stehen in den Inschriften, was in den einzelnen Artikeln (bei häufigen Namen), was im Register als Haupt- bzw. Nebenform? Auch Wappenreg. berücksichtigen!
1. Jürgen: In prosopograf. Teilen der Gp.-kommentare fehlende Verweise auf einzelne Inschriften ergänzen bzw. überprüfen und ggf. korrigieren? Jonas hat sich das erste Viertel des Kat. angeschaut und Korrekturbedarf festgestellt (Jan. 2021).
1. Jürgen: Unterpunktete Worttrenner werden nicht ausgegeben; s. museum.gp-tancke. --> Jürgen
1. Jürgen: betr. Datierungen. Es gibt "vor 1462" neben "1462 oder früher" bzw. "nach 1477" neben "1477 oder später" --> noch nicht entschieden, dafür Fälle zusammen ansehen u. vereinheitlichen.


##### Chat erledigt

1. drei Zierschnallen
    * Standort LAKD, nicht Fundort
    * zugewiesen an: MD - erledigt
1. pilgerzeichen
    * CM, MD: raus aus Katalog, weil seriell gegossen
    * zugewiesen an: CM - erledigt
1. museum.sühnestein-steenvord
    * MD: raus, Standort ist in NWM; ist auch in NWM schon angelegt: nwm.saunsdorf.gedenkstein; Fotos in den Artikel bei NWM
    * zugewiesen an: MD - erledigt
1. marien.ct219 u. a.
    * CM: bei Oeynhausen nur Inschrift A ==> Nachweise zu Oeynhausen auf die Inschrift(en) hin konkretisieren
    * zugewiesen an: Jonas u. CM - erledigt
1. im AHW existiert ein Foto zum Leuchter hwi.marien.kronleuchter [... link](https://ariadne-portal.uni-greifswald.de/?arc=15&type=mps&id=101489)
    + zugewiesen an: MD - auf dem Foto die Plättchen mit den Inschriften nicht vorhanden - erledigt
1. CM: Kurztitel Mecklenburgisches Urkundenbuch/MUB vereinheitlichen zu MUB - erledigt Jonas/CM
1. CM: hwi.heilgeist.bildfenster: Sind hier irgendwelche Maße ermittelbar? - nein MD
1. CM: Verwendung von "Reimverse" und "Reimverse deutsch" vereinheitlichen; in den Ed. oft auch noch nicht mit entsprechenden Zeilenumbrüchen formatiert - am besten zu finden über ein Gesamt-Dokument - Literaturwiss. handelt es sich um Verse unterschiedl. Silbenzahl mit Paarreimen (Wagenknecht, Deutsche Metrik: "Knittelvers") - erledigt MD
1. MUB-Nachweise umstellen von S.-zahlen auf Nr.
    * zugewiesen an: Jonas u. CM - erledigt Jonas
1. CM (15.9.): Verständnis des "Chorumgangs": Es gibt nur EINEN Ch., daher Bezeichnungen wie "Übergang vom südlichen zum südöstlichen Chorumgang" ergänzen durch Ausdrücke wie "Teil" oder "Bereich". Vgl. m.ct254 ... - erledigt CM
1. Zitierweise von Blattangaben in handschriftl. Quellen: f. -> fol., Blatt/Bl. -> fol. Die Ausnahme: Scheffers, Bl. xx - das bleibt so! - erledigt PB, CM
1. (1.10.) Für C/T-Nachweise werden keine Buchstaben für die einzelnen Inschriften in der Nachweiszeile ergänzt. --> In einzelnen Artikeln, wo sie noch stehen, rausnehmen! --> Erledigt CM.
1. Nachweisen von Crull/Techen in Form von Seitenzahlen mit Nr. (in Klammern), also "S. 12 (Nr. 13)" --> für C/T erledigt CM - noch nicht für andere Werke, aus denen Angaben mit Nr. angeführt werden
1. Betr. Standorte OFM-Kloster und OP-Kloster
    * Manchmal Standort Kirche, manchmal Kloster allgemein -> prüfen/vereinheitlichen
    * zugewiesen an: MD - erledigt
1. Vornamen ohne Nachnamen, Nachnamen ohne Vornamen: Vorne in Beschreibungen zurückhaltend "N. N." ergänzen, wenn sonst keine Angaben zum Namen vorliegen; vgl. n.ct196 und öfter. Bei späteren Namensnennungen im Artikel darf(!) N. N. auch fehlen. --> erledigt CM 13.11.2020
1. Maßangaben sollen in der Form "11 cm" (nicht "11,0 cm") ausgegeben werden --> vereinheitlichen, d.h. wo im PDF vorhanden aus den Maßangaben tilgen
    * erledigt Jonas
1. Tagesdaten zu Quellennachweisen in Anm. werden nach dem Schema "13. Juli 1615" (nicht: "1615 Juli 13") wiedergegeben --> vereinheitlichen
    * erledigt Jonas
1. Ausgabe Maßezeile: Wenn in einer Inschrift sowohl Zi. als auch Bu. vorhanden sind, muss die Schriftform an erster Stelle stehen, damit "Bu." ausgegeben wird
    * erledigt Jonas

####Aufgabenbereich für Mona:

* Altarretabel
* Wand- und Deckenmalerei
* Leuchter - fertig
* Vasa sacra - fertig
* Epitaphien
* evtl. Wasserkunst
* Glasgemälde und -fenster - fertig
* Kanzeln
* Gemälde - fertig
* Ofensockel - fertig
* Uhr und Zifferblatt - fertig
* Papagoyenektte - ausgesondert
* Großschmiedestraße 18 - fertig
* Tafelbilder - fertig
* Triumphkreuze - fertig

####Aufgabenbereich für Christine:

* Zunft u.ä. - fertig

####Aufgabenbereich für Jürgen:
* Kanzeln
* Glocken
* evtl. Wasserkunst



####Aufgaben für Jonas

Noch nicht gezeichnete Marken sind:
1. mkg-hamburg.stop-1911.278 (Abb. Kunzel, Zinngießerhandwerk, S. 422, Nr. 44-78) **verschlagen**

2. sm-schwerin.stop-KH370 (Abb. Kunzel, Zinngießerhandwerk, S. 422, Nr. 44-79) **verschlagen**

3. museum.stop-2950 (Abb. Kunzel, Zinngießerhandwerk, S. 422, Nr. 44-76) **verschlagen**

4. museum.stop-12869 (Abb. Kunzel, Zinngießerhandwerk, S. 422, Nr. 44-80) **gezeichnet; liegt im Ordner von Jonas S** - in Artikel eingefügt MD

5. museum.willkomm-kirchner1634 (Abb. Kunzel, Zinngießerhandwerk, S. 402, Nr. 44-32) **gezeichnet; liegt im Ordner von Jonas S**  - in Artikel eingefügt MD

6. georgen.kelch6 (Fotos: hwi.georgen.kelch6.DIHGW_3860 und hwi.georgen.kelch6.DIHGW_3863) **gezeichnet; liegt im Ordner von Jonas S** - in Artikel eingefügt MD

außerdem:

7. nikolai.kelch4 (nur eine Zeichnung bei Schlie 2, S. 153, bei der ich mir nicht sicher bin, ob sie zum Zeichnen reicht) **nicht vollständig abgebildet, daher keine Zeichnung mgl.**

8. marien.kelch5 (keine Abb,: Crull und Schlie sagen, das Meisterzeichen sei ein H; reicht eher nicht zum Zeichnen, oder?) **reicht nicht zum Zeichnen**

9. marien.kanzel (oberes Foto in der pdf-Datei "Kanzel Wismar..." im Ordner Mona->Wismar, auf S. 7) **gezeichnet; liegt im Ordner von Jonas S**  - in Artikel eingefügt MD

-----------


[To-do-Liste](https://epigraf.inschriften.net/epi4/inschriften_mv/notes/view/16)

[Adressen](https://epigraf.inschriften.net/epi4/inschriften_mv/notes/view/14)


####Hilfsmittel (Linkliste)
* [Techen, Friedrich, Geschichte der Seestadt Wismar](http://www.torrido.de/index.htm)
* [HWI-Personennachweise](\\\\141.53.14.11\\inschriftenmv)
* [von Gamm, Ausgestorbene Geschlechter Mecklenburgs](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00000845)
* [Crull, Ratslinie](http://daten.digitale-sammlungen.de/~db/bsb00006577/images/index.html?id=00006577&fip=141.53.196.42&no=12&seite=1)
* [Crull, Wappen der Mannschaft](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00002957)
* [Crull, Wappen Wismarscher Geschlechter](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00003164)
* [Grotefend, Mecklenburger in Danzig](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00003382)
* [Mecklenburgische Jahrbücher](http://mvdok.lbmv.de/sammlung/mjb)
* [Mecklenburgisches Urkundenbuch](http://www.archive.org/search.php?query=mecklenburgisches%20Urkundenbuch)
* [Schlie, Bau- und Kunstdenkmäler, Bd. 2](http://www.archive.org/details/diekunstundgesch02schl)
* [Fries, St. Georgen (Diss.)](https://opus4.kobv.de/opus4-bamberg/frontdoor/index/index/docId/26016)
* Schröder, Kurze Beschreibung der Stadt und Herrschaft Wismar [Lexikus](http://www.lexikus.de/bibliothek/Kurze-Beschreibung-der-Stadt-und-Herrschaft-Wismar) u. [BSB](http://mdz-nbn-resolving.de/urn:nbn:de:bvb:12-bsb10003310-5)
    - Stadtsekretäre: S. 51
    - Syndici S. 32

Archiv der Hansestadt Wismar - Stadtbücher [1: pdf](https://www.stadtbuecher.de/de/archive/stadtarchiv-wismar/pdf)
[2: stadtbuecher.de](https://www.stadtbuecher.de/de/archive/stadtarchiv-wismar/)

[Joachim Brügmann, Das Zunftwesen der Seestadt Wismar bis zum Beginn des 17. Jahrhunderts](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00003730)

Friedrich Crull, Friedrich Techen: Die Grabsteine der Wismarer Kirchen<br/>
[Teil 1: Einleitung, St. Marien](http://mvdok.lbmv.de/resolve/id/mvdok_document_00002988/fulltext#page119)<br/>
[Teil 2: St. Nikolai](http://mvdok.lbmv.de/resolve/id/mvdok_document_00003004/fulltext#page241)<br/>
[Teil 3: St. Georgen, Schwarzes Kloster, In der Stadt verstreute Steine; Epitaphien; Register](http://mvdok.lbmv.de/resolve/id/mvdok_document_00003019/fulltext#page103)<br/>

Wismars Straßennamen<br/>
[Kindler, Straßennamen Wismars](https://epigraf.inschriften.net/epi4/inschriften_mv/files?path=hilfsmittel%2FProjekte-in-MV%2FWismar)<br/>
[Schmidt, Straßennamen Wismars](https://epigraf.inschriften.net/epi4/inschriften_mv/files?path=hilfsmittel%2FProjekte-in-MV%2FWismar)<br/>
[Straßennamen Lexikon](http://www.wismar.de/media/custom/125_659_1.PDF)<br/>
[Techen, Straßennamen Wismars](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00003299)<br/>

Gustav Willgeroth, Die Mecklenburgisch-Schwerinschen Pfarren seit dem dreißigjährigen Kriege. Mit Anmerkungen über die früheren Pastoren seit der Reformation, 4 Bde., Wismar 1924–1937

* Präpositur Wismar [Bd. 3](http://purl.uni-rostock.de/rosdok/ppn717436977), S. 1337ff.
    * Wismar St. Marien: 1347 [222]
    * Wismar St. Georgen: 1366
    * Wismar St. Nikolai: 1377

[Gustav Willgeroth, Die Lehrer der Großen Stadtschule zu Wismar](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00003723)

[Gustav Willgeroth, Beiträge zur Wismarschen Familienkunde, Wismar 1932](http://rosdok.uni-rostock.de/mcrviewer/recordIdentifier/rosdok_ppn859930912/iview2/phys_0007.iview2?logicalDiv=log_0002)

',
                'format' => 'markdown',
            ],
            [
                'id' => 4,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2017-10-30 11:54:55',
                'modified' => '2021-05-05 08:36:12',
                'name' => 'Mecklenburg, Literatur allgemein',
                'category' => 'Mecklenburg',
                'content' => ' [Landesbibliothek MV - Sonderbestände (Personalschriften, Ditmarsche Sammlungen und Schmidtsche Bibliothek)](http://www-db.lbmv.de/)



#### Wiki-Farm [(Fandom-Wikis)](https://de.wikipedia.org/wiki/Fandom_(Webseite))
+ [Datenbank zum mecklenburgischen Niederadel des Spätmittelalters](https://adel-mecklenburgs.fandom.com/de/wiki/Adel_Mecklenburgs_Wikia)

==> auch unter Auswertung der Regestenkartei Mecklenburgischer Urkunden (RMU)

+ [Prosopographie zur Güterwegnahme und Gewalt auf See im Hanseraum (1375-1435)](https://prosopographie.fandom.com/de/wiki/Prosopographie_Wiki)

#### MUB - Mecklenburgisches Urkundenbuch
* [archive.org](http://www.archive.org/search.php?query=mecklenburgisches%20Urkundenbuch)
* [Übersicht (Wikipedia)](https://de.wikipedia.org/wiki/Mecklenburgisches_Urkundenbuch)

##### „Erste Abtheilung“

[Band I (1863): Nr. 1–666 (786–1250)](https://archive.org/details/mecklenburgisch02unkngoog)
[Band II (1864): Nr. 667–1557 (1251–1280)](https://archive.org/details/bub_gb_mEQ8AAAAMAAJ)
[Band III (1865): Nr. 1558–2425 (1281–1296)](https://archive.org/details/bub_gb_WXYKAAAAIAAJ)
[Band IV (1867): Nr. 2426–2723 (1297–1300), Nachträge und Register zu I–IV, Siegel (Tafel 1–34)](https://archive.org/details/bub_gb_20w8AAAAMAAJ)

* Ortsregister S. 1-100
    * Nachträge S. 505-506
* Personenregister S. 101-403
    * Nachträge S. 506-509
* Wort- und Sachregister S. 405-502
    * Nachträge S. 510-518
* Siegel S. 519-554

##### „Zweite Abtheilung“

[Band V (1869): Nr. 2724–3581 (1301–1312)](https://archive.org/details/mecklenburgisch07unkngoog)
[Band VI (1870): Nr. 3582–4318 (1313–1321)](https://archive.org/details/bub_gb_H3UKAAAAIAAJ)
[Band VII (1872): Nr. 4319–5008 (1322–1328)](https://archive.org/details/mecklenburgisch13altegoog)
[Band VIII (1873): Nr. 5009–5727 (1329–1336)](https://archive.org/details/mecklenburgisch05altegoog) [... und](https://archive.org/details/bub_gb_PXQKAAAAIAAJ)
[Band IX (1875): Nr. 5728–6602 (1337–1345)](https://archive.org/details/mecklenburgisch07altegoog)
[Band X (1877): Nr. 6603–7399 (1346–1350), Nachträge zu I–X, Siegel (Tafel 35–70)](https://archive.org/details/mecklenburgisch13unkngoog)
[Band XI (1878): Orts- und Personenregister zu V-X](https://archive.org/details/mecklenburgisch09altegoog)

* Ortsregister S. 1
    * Berichtigungen und Nachträge S. 697
* Personenregister S. 109
    * Berichtigungen und Nachträge S. 698

[Band XII (1882): Wort- und Sachregister zu V-X](https://archive.org/details/mecklenburgisch11altegoog)

##### „Dritte Abtheilung“

[Band XIII (1884): Nr. 7400–8174 (1351–1355)](https://archive.org/details/mecklenburgisch15unkngoog)
[Band XIV (1886): Nr. 8175–8817 (1356–1360)](https://archive.org/details/mecklenburgisch14unkngoog)
[Band XV (1890): Nr. 8818–9430 (1360–1365)](https://archive.org/details/mecklenburgisch16unkngoog)
[Band XVI (1893): Nr. 9431–10141 (1366–1370)](https://archive.org/details/mecklenburgisch11unkngoog)
[Band XVII (1897): Register zu XIII–XVI](https://archive.org/details/mecklenburgisch04altegoog)

* Ortsregister S. 1
* Personenregister S. 55
* Standesregister S. 260
* Wort- und Sachregister S. 309
* Berichtigungenund Nachträge S. 663

[Band XVIII (1897): Nr. 10142–10819 (1371–1375), Register](https://archive.org/details/mecklenburgisch03altegoog)
[Band XIX (1899): Nr. 10820–11299 (1376–1380), Register](https://archive.org/details/mecklenburgisch06unkngoog)
[Band XX (1900): Nr. 11300–11741 (1381–1385), Register](https://archive.org/details/mecklenburgisch06altegoog)
[Band XXI (1903): Nr. 11742–12251 (1386–1390), Register](https://archive.org/details/mecklenburgisch12unkngoog)  [... und](https://archive.org/details/bub_gb_MXgKAAAAIAAJ)
[Band XXII (1907): Nr. 12252–12880 (1391–1395), Register](https://archive.org/details/mecklenburgisch05unkngoog/page/n6/mode/2up)
Band XXIII (1911): Nr. 12881–13561 (1396–1399), Register
[Band XXIV (1913): Nr. 13562–13739 (1400), Register, Siegel (Tafel 71–114)](https://archive.org/details/MecklenburgischesUrkundenbuchV.241913)

##### Nachträge

Band XXV A (1936): Nachträge 1166–1400
Band XXV B (1977): Nachträge 1235–1400

Die Bände 18 bis 24 haben jeweils ein eigenes Register.

----

Mecklenburgische Urkunden, ges. und neu bearb. und mit Unterstützung des Vereins für Meklenburgische Geschichte und Alterthumskunde hrsg. von G. C. F. Lisch, Schwerin 1837

Drei Bände in einem:[ ==> online](https://books.google.de/books?id=wPUDAAAAYAAJ&dq=johannes+achym&hl=de&source=gbs_navlinks_s)
Bd. 1 Urkunden des Klosters Dargun
Bd. 2 Urkunden des Klosters Neukloster
Bd. 3 Urkunden des Bistums Schwerin

Am Ende des dritten Bandes S. 117ff.: Register zu dem Mecklenburgischen Urkunden, Bde. I, II, III



-------

* [Grotefend, Mecklenburger in Danzig](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00003382)

* [Mecklenburgische Jahrbücher](http://mvdok.lbmv.de/sammlung/mjb)


-----

* [Crull, Wappen der Mannschaft](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00002957)

--------------
* Dietrich Schröder, Mecklenburgische Kirchen-Historie des Papistischen Mecklenburgs, Erster Band, Wismar 1741 [UB Rostock](http://purl.uni-rostock.de/rosdok/ppn1040752020)
    * [Erstes Alphabeth ... Darinnen enthalten, Wie, durch sonderbahre Göttliche Fügung, das Christenthum dem Lande Mecklenburg sich nach und nach genähert, und endlich ein Räumlein darinnen gefunden ... online](https://books.google.de/books?id=H8RWAAAAcAAJ&printsec=frontcover&hl=de&source=gbs_ge_summary_r&cad=0#v=onepage&q&f=false)
   * [Zweites Alphabeth ... 1025-1157]()
   * [Drittes Alphabeth ... 1157-1225]()
   * [Viertes Alphabeth ... 1226-1270]()
   * [Fünftes Alphabeth ... 1271-1309 ... online](https://books.google.de/books?id=3cRWAAAAcAAJ&printsec=frontcover&hl=de&source=gbs_ge_summary_r&cad=0#v=onepage&q&f=false)
   * [Sechstes Alphabeth ... 1309-1329 ... online](https://books.google.de/books?id=acRWAAAAcAAJ&printsec=frontcover&hl=de&source=gbs_ge_summary_r&cad=0#v=onepage&q&f=false)
   * [Siebendes Alphabeth ... 1329-1345]()
   * [Achtes Alphabeth ... 1345-1372]()
   * [Neuntes Alphabeth ... 1372-1397]()
* Dietrich Schröder, Mecklenburgische Kirchen-Historie des Papistischen Mecklenburgs, Andrer Band, Wismar 1741 [UB Rostock](http://purl.uni-rostock.de/rosdok/ppn1040752101)
   * [Zehntes Alphabeth ... 1397-1419 ... online](https://books.google.de/books?id=7nNbAAAAcAAJ&printsec=frontcover&hl=de&source=gbs_ge_summary_r&cad=0#v=onepage&q&f=false)
   * [Eilfftes Alphabeth ... 1319-1343 ... online](https://books.google.de/books?id=f8RWAAAAcAAJ&printsec=frontcover&hl=de&source=gbs_ge_summary_r&cad=0#v=onepage&q&f=false)
   * [Zwölfftes Alphabeth ... 1343-1367]()
   * [Dreyzehendes Alphabeth ... 1467-1485]()
   * [Viertzehendes Alphabeth ... 1485-1495]()
   * [Funffzehendes Alphabeth ... 1495-1504 ... online](https://books.google.de/books?id=3cRWAAAAcAAJ&printsec=frontcover&hl=de&source=gbs_ge_summary_r&cad=0#v=onepage&q&f=false)
   * [Sechzehendes Alphabeth ... 1504-1517]()
   * [Siebenzehendes Alphabeth ... Der erste Anhang von allerhand Nachrichten und Dokumenten die dem vorhergehenden einzuschalten]()
   * [Achtzehendes Alphabeth ... Darin enthalten noch einige Documenta nebst Registern]()




* Dieterich Schröders Kirchen-Historie des Evangelischen Mecklenburgs vom Jahr 1518 bis 1742
    * [Teil 1: 1518-1551, Rostock 1788](http://www.mdz-nbn-resolving.de/urn/resolver.pl?urn=urn:nbn:de:bvb:12-bsb10360988-1)
    * [Teil 2: 1552-1568, Rostock 1788](http://www.mdz-nbn-resolving.de/urn/resolver.pl?urn=urn:nbn:de:bvb:12-bsb10360989-1)
    * [Teil 3: 1569-1581, Rostock 1789](http://www.mdz-nbn-resolving.de/urn/resolver.pl?urn=urn:nbn:de:bvb:12-bsb10360990-8)

-----------

[Springinsgut/Schröder, Wismarische Prediger-Historie](http://digital.slub-dresden.de/werkansicht/dlf/17672/1/0/)

Chronologisches Register der Prediger nach den Kirchen und jeweiligen Ämtern S. 277.

----------

Gustav Willgeroth, Die Mecklenburgisch-Schwerinschen Pfarren seit dem dreißigjährigen Kriege. Mit Anmerkungen über die früheren Pastoren seit der Reformation, 4 Bde., Wismar 1924–1937

* [Bd. 1](http://purl.uni-rostock.de/rosdok/ppn717436322)
* [Bd. 2](http://purl.uni-rostock.de/rosdok/ppn717436632)
* [Bd. 3](http://purl.uni-rostock.de/rosdok/ppn717436977)
* [Berichtigungen und Zusätze; Register zu Bd. 1-3](http://purl.uni-rostock.de/rosdok/ppn717437302)
* [Nachtrag 1933](http://purl.uni-rostock.de/rosdok/ppn717437612)
* [Ergänzungsband 1937](http://purl.uni-rostock.de/rosdok/ppn71743785X)

------

Gustav Willgeroth (Hg.), [Die  mecklenburgischen Aerzte von den ältesten Zeiten bis zur Gegenwart](http://rosdok.uni-rostock.de/mcrviewer/recordIdentifier/rosdok_ppn690255713/iview2/phys_0001.iview2?x=-1749.0540827147406&y=-2.2737367544323206e-13&scale=0.2688141391106043&rotation=0&layout=singlePageLayout&logicalDiv=log_0000)

------

### Universitätsmatrikel

* Balck, Mecklenburger auf auswärtigen Universitäten
    * [Teil 1](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00002895)
    * [Teil 2](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00002919)
    * [Teil 3](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00002932)

* [Grotefend, Mecklenburger auf der Universität Bologna](http://mvdok.lbmv.de/mjbrenderer?id=mvdok_document_00002972)

* [Knod, Bologna (Deutsche Studenten in Bologna (1289-1562) Biographischer Index zu den Acta nationis germanicae Universitatis bononienses. Im Auftrag der K. Preussichen Akademie der Wissenschaften bearb. von Gustav C. Knod)](https://archive.org/details/deutschestudent00knod)

* [RAG - Repertorium Academicum Germanicum - Die graduierten Gelehrten des Alten Reiches zwischen 1250 und 1550](http://rag-online.org/)

* [Universitätsmatrikel - GenWiki](http://genwiki.genealogy.net/Universit%C3%A4tsmatrikel)',
                'format' => 'markdown',
            ],
            [
                'id' => 5,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2017-11-22 09:58:57',
                'modified' => '2020-07-10 09:16:39',
                'name' => 'Rostock, Grabplatten',
                'category' => 'Rostock',
                'content' => '__Bestandsübersicht Grabplatten__

**St. Marien (ca. 250)**
+ a) im Kircheninneren: 220 davon liegend: 205, stehend: 14, aus dem Boden aufgenommen: 1 b) außen im Mauersockel: ca. 26 c) Zählung Gp. bei Skottki: 258 (Nov. 2017, Theo Jelen)
+ NB: Zahlreiche zersägte Gp. mit Inschriften in gotischer Majuskel wurden 1398ff. verbaut als Sohlbänke der Obergadenfenster; schwer zugänglich, nur von oberhalb des Gewölbes der Seitenschiffe. - Anzahl nicht abschätzbar, jeweils nur einzelne Streifen älterer Gp. verbaut, Inschriften nur von unterhalb der Obergadenfenster einsehbar!
+ NB: In einem Ende 19. Jh. erbauten Heizungsschacht vom "Kirchencafe" zur SW-Ecke der Kirche sind ma. Grabplatten verbaut. Dieser Schacht ist nicht zugänglich!

**St. Nikolai (ca. 40; ca. 220 incl. Nachlass Krause)**
+ a) Gp. im Altarraum 2 (incl. Mensa), stehend im südl. Seitenschiff 2, nördl. Seitenschiff 2 vgl. dazu das Panorama im Internet http://www.deutschland-panorama.de/kirchen/rostock_nikolaikirche/ps/9_949_01.php.
+ b) außen an der Südseite im Pflaster verlegt etwa 15 außen an der Nordseite in einer Grünanlage u. in den Chormauern etwa 15, überwiegend jünger, stark überwachsen In den Hauseingängen um die Kirche sieht man etliche Platten als Treppenstufen und Schwellen verlegt.
--> Stand Juli 2020: insgesamt etwa 40 Platten heute noch vorh., überwiegend nach 1650

**Hl. Kreuz/Museum (50) 50 Platten (nach Wagner)**

**St. Petri (ca. 30)**
+ in der Turmhalle 4 oder 5 Platten
+ außen um die Kirche wurden vor einigen Jahren etwa zwei Dutzend Platten im Pflaster verlegt, mit denen zuvor der unterirdische Heizkanal zwischen Heizhaus und Kirche abgedeckt war, nach meiner Einschätzung alles jüngere Platten (nach 1650)

**St. Katharinen/HMT (ca. 50)**
+ knapp 50 bei Ausgrabungen in den 1990er Jahren zutage getreten -> s. Mecklenburg. Klosterbuch, Fotos von Heiko Schäfer bekommen

**Magazin der Denkmalpflege im Wasserturm (5)**
+ 5 Platten --> noch aktuell???

**St. Jakobi**
+ "etwa 170 Fragm. von mind. 40 Gp." (MKB 2, S. 914); dazu der Verf. dieses Kapitels Torsten Rütz [Sept. 2017, mdl.]: Gp. sind hochkant eingelagert und schwer zugänglich


__Nachlass Ludwig Krause im Stadtarchiv__
(email von Ralf Mulsow vom 13. Juli 2009)

Lieber Herr Herold, vielen Dank für die mail, das sind ja schon schöne Ergebnisse. Ich traf gerade Prof. Münch und habe ihm auch die mail weitergeleitet. Er hatte ja im 14. Jh. vermehrt Vermächtnisse der Kröpelins für St. Katharinen gefunden und schaut nach, ob etwas passendes dabei ist. Ich antworte erst jetzt, da ich im Archiv und zu Hause erst den "Ludwig Krause Nachlaß" nach Grabplatten durchgesehen habe.
Zu den Platten auf der Südseite von St. Nikolai: Ob die Steine schon nach der Aufnahme des Fußbodens (nach Krause 1890) herausgeschafft worden sind, kann ich nicht sagen. Zeitlich würde das passen, da diese Abtreppung nach den Karten vor 1902 angelegt wurde. Es käme natürlich auch noch die Renovierung der Kirche in den 1980er Jahren in Frage. Da könnte man Kirchenbaurat Gisbert Wolf befragen oder die Landschaftsarchitekten, die den Platz in der Mitte der 1990er Jahre umgestaltet haben. Ich erkundige mich.

Nun zu Ludwig Krauses Nachlaß im Stadtarchiv (AHR 1.4.17)
- in der Mappe "Stadt Rostock allg." erwähnt er einiges an Grabsteinen in div. Häusern, Höfen,Trottoirs usw. aber nur kurz ohne Zeichnung; zwei Rostocker Grabsteine, sek. im Schweriner Schloß sind nach ihm in den Meckl. Jahrbüchern, XV, S. 164 und XXVII, S. 238/239 beschrieben
- die Tagebücher sind nicht sehr ergiebig, nur eine Grabsteinzeichnung aus St. Jakobi (1902, S. 22)

Es gibt verschiedene Notizbücher und auch lose Sammelmappen mit Material zu einzelnen Kirchen/Klöstern, interessant in Bezug auf Grabsteine:
- Nr. 207 (Rost. Altertümer III, Johanniskloster, Heiligkreuz): Nachrichten zu den beiden fürstlichen Grabsteinen in St. Johannis (Nikolaus das Kind gest. 1314)
- Nr. 208 (St. Petri-Kirche, Mat.-sammlung): nur Wiedergabe der Inschriften der beiden studentischen Erbbegräbnisse von 1499
- Nr. 210 (St. Marien-Kirche Bd. 1, Mat.-sammlung): Skizzen von 3 Grabsteinen 1576/1614, 1723, 1730 sowie Inschriftskizzen weiterer Platten
- Nr. 211 (St. Jacobi-Kirche Bd. 2, Mat.-sammlung): Skizze einer Platte von 1823

- Nr. 199-204 (Nikolaikirche): Das lohnt sich !!!, Krause hat den ganzen Fußboden aufgenommen, was er nicht sehen konnte, hat er später, als das Gestühl "verauctioniert" worden ist (um 1890), nachgetragen. In den Heften gibt es ca. 160 Zeichnungen in Tusche bis 10x20cm Größe, entweder die gesamt Platte oder nur die Inschrift bei den jüngeren Steinen oder auch Bruchstücke von Steinen; wohl der gesamte Grabplattenbestand. Dazu oft noch hist. Notizen zu den erwähnten Personen. Es sind etliche ma. Platten dabei. Die ma. Inschriften hat Krause, soweit lesbar, dann auch noch ausgeschrieben. Dazu gibt es noch ein Hausmarkenregister (299 Stk.!), ein Register der Handwerkerzeichen, der Wappen usw.
Besondere Grabsteine:
- Hermann Becker, Pfarrer gest. 1479 (davon soll ein guter Rostocker Photograph nach Krause Platten gemacht haben, vielleicht sind die noch zu finden)
- prächtige Barockplatte von 1621/1674 Samuel von Voss
- die Bernhard Kopman-Platte von 1332, 2,77 x 1,5 Meter, Lisch, Meckl. Jb. XXVII, S. 139 und S. 116, nach Krause eine falsch wiedergegebene Inschrift !!

Übrigens hat Krause auch sehr viel Material zum Glockenbestand, den Tauffässern usw. mit Recherchen, Inschriften, Pausen usw. abgelegt.

Das wäre, denke ich, alles zu Ludwig Krause. Im Anhang noch der erwähnte Engel mit Schriftband, Altbestand des Museums, ohne Herkunft, eingemauert in eine Hofmauer im ehem. Schiffahrtsmuseum, August-Bebel-Str. 1. Können sie dazu etwas sagen?
Viele Grüße aus Rostock
Ralf Mulsow

Ralf Mulsow
Stadtarchäologe
Amt für Kultur und Denkmalpflege
Friedhofsweg 28
18057 Rostock
email: ralf.mulsow@rostock.de
Tel.: 0381/25 219 18
Mobil: 0170/63 61 254

',
                'format' => 'markdown',
            ],
            [
                'id' => 11,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-03-01 09:16:35',
                'modified' => '2019-05-19 15:37:13',
                'name' => 'Mecklenburgs Maße',
                'category' => 'Mecklenburg',
                'content' => '1 Fuß = 12 Zoll

1 Rostocker Fuß  (ab 18. Jh.)  = 29 cm

1 Zoll = 2,4 cm


[Quelle](https://de.wikipedia.org/wiki/Alte_Ma%C3%9Fe_und_Gewichte_(Mecklenburg))',
                'format' => 'markdown',
            ],
            [
                'id' => 8,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-01-22 14:01:42',
                'modified' => '2021-02-20 08:08:11',
                'name' => 'Rostock: Linkliste Lit.',
                'category' => 'Rostock',
                'content' => '* [Schröder, Etwas von gelehrten Rostockschen Sachen](http://rosdok.uni-rostock.de/resolve?id=rosdok_series_000000000005)
* Familiennachrichten, gesammelt von Joachim Christian Friedrich Baeder
    * [Erster Theil, Rostock 1866](http://purl.uni-rostock.de/rosdok/ppn896971228)
    * [Zweiter Theil, Rostock 1868](http://purl.uni-rostock.de/rosdok/ppn896971252)',
                'format' => 'markdown',
            ],
            [
                'id' => 9,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-02-28 15:16:02',
                'modified' => '2019-05-19 15:41:54',
                'name' => 'Allgemeine Hilfsmittel',
                'category' => 'Hilfsmittel',
                'content' => '---
[Deutsche Inschriften Online](http://www.inschriften.net/)

* [Startseite](http://www.inschriften.net/)
* [Suche](http://www.inschriften.net/suche.html)

---

* [Siebmacher online](http://data.cerl.org/siebmacher/_search)

* [Bagmihl, Pommersches Wappenbuch 1-5](https://de.wikipedia.org/wiki/Pommersches_Wappenbuch)

* [Enigma](http://ciham-digital.huma-num.fr/enigma/)

* [Ariadne Archivverbund](http://ariadne.uni-greifswald.de/)

* [Digitale Bibliothek MV](http://ub-goobi-pr2.ub.uni-greifswald.de/viewer/)

* [Grotefend online](http://www.manuscripta-mediaevalia.de/gaeste/grotefend/grotefend.htm)

* [RAG - Repertorium Academicum Germanicum - Die graduierten Gelehrten des Alten Reiches zwischen 1250 und 1550](http://rag-online.org/)

* [Knod, Bologna (Deutsche Studenten in Bologna (1289-1562) Biographischer Index zu den Acta nationis germanicae Universitatis bononienses. Im Auftrag der K. Preussichen Akademie der Wissenschaften bearb. von Gustav C. Knod)](https://archive.org/details/deutschestudent00knod)

* [Universitätsmatrikel - GenWiki](http://genwiki.genealogy.net/Universit%C3%A4tsmatrikel)',
                'format' => 'markdown',
            ],
            [
                'id' => 10,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-02-28 15:36:30',
                'modified' => '2019-05-19 15:39:54',
                'name' => 'Bearbeitungsrichtlinien',
                'category' => 'Richtlinien',
                'content' => '[Bearbeitungsrichtlinien 2005](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FDI-Richtlinien&filename=Bearbeitungsrichtlinien05.pdf)

[Ergänzungen 2009](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FDI-Richtlinien&filename=Bearbeitungsrichtlinien-Ergnzung2009GttingenundGreifswald.pdf)

[Kopiale Überlieferung](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FDI-Richtlinien&filename=BearbeitungsrichtlinienKopien.pdf)

[Bibelnachweise](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FDI-Richtlinien&filename=Bibelnachweise.pdf)

[Register-Manuale 2009](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FDI-Richtlinien&filename=RegisterManuale20093.pdf)

[Register-Manuale 2015](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FDI-Richtlinien&filename=RegisterManuale20093.pdf)

[Grundsätzliche Überlegungen zur Übersetzung von Titeln, Standes-, Rang- und Amtsbezeichnungen sowie von Epitheta und Prädikaten](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FDI-Richtlinien&filename=Epitheta-Titel.pdf)

[Textsorten auf Glocken](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FDI-Richtlinien&filename=TextsortenaufGlocken.pdf)

[Textsorten in Bauinschriften](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FDI-Richtlinien&filename=TextsorteninBauinschriften.pdf)',
                'format' => 'markdown',
            ],
            [
                'id' => 12,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-08-01 14:10:44',
                'modified' => '2019-05-19 15:37:27',
                'name' => 'Pommern',
                'category' => 'Pommern',
                'content' => '[Baltische Studien in DigiBib MV - 1832 bis 2002](http://www.digitale-bibliothek-mv.de/viewer/toc/PPN559838239/1/LOG_0000/)',
                'format' => 'markdown',
            ],
            [
                'id' => 6,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2017-11-22 10:00:57',
                'modified' => '2020-10-13 10:17:50',
                'name' => 'Rostock, St. Nikolai',
                'category' => 'Rostock',
                'content' => '**St. Nikolai**
- Turmhalle: Relief Gottvater (s. dazu Mail u. Foto Sakowski)
- Kirchenschiff: 1 steinernes Ep. Georg Jacobi (1597), 6 Wandleuchter, 4 Gp. an der Wand, 1 Gp. als Altarmensa, 1 stark beschädigte Predella (zugehöriges Retabel der Nikolaikirche heute in St. Marien, äußere Flügel in St. Petri [so Dehio MV])
- Moltke-Gruft: 5 steinerne Bauteile/Relieftafeln mit I. vor 1650, Bauinschrift St. Jakobi 1465
(Sichtung CM 14.8.2020)
--> 19 Objekte

- Grabplatten bei Krause: Abkürzung D. A. D. G. entspricht D(EM) A(MT) D(ER) G(ERBER). Begründung: Die betreffenden Platten liegen lt. Krause vor der "Gerbertür" -> dabei handelt es sich wahrscheinlich um die Tür zum Gestühl des Gerberamtes (Jonas Simon, 13.10.2020). Vgl. hro.nicolai.gp-maneke.

**Kunstgutdepot**
- siehe separate Liste (Word, PC Magin)',
                'format' => 'markdown',
            ],
            [
                'id' => 13,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-08-13 09:02:50',
                'modified' => '2019-05-19 15:36:22',
                'name' => 'Archiv der Hansestadt Wismar - AHW',
                'category' => 'Wismar',
                'content' => '__hwi.georgen.ct017:__
Ingeborch van Plessen, gestorben 1563
Nachlässe: Collectaneen - H. P. W. Anders und Dr. Crull,
Signatur: 099
Titel: Überzählige Abschriften von Urkunden
Enthält: Abschrift einer Urkunde betreffs Ingeborg v. Plessen schenkt zu den von M. Konrad Boddeker zu St. Jürgen gestifteten Almissen hundert Gulden vom 04. Januar 1559.
==> über Ariadne, Suchwort Plesse


__hwi.heilgeist.gestuehl:__
Gestühl des Scharfrichters C. H.: • C • H • / D • W • V • R • M / 1589
Gewerke:

* Schlosser
* Nagelschmiede
* Hufschmiede
* Ankerschmiede
* Bäcjer
* Barbiere
* Böttcher
* Träger
* Seiler ?

__hwi.georgen.ct025__
Gödert (Gädert) von der Fehrschen Legat siehe Stadtarchiv Wismar (Abt. VIII. Rep. 4 Nachlässe) 097; Nachlässe - 01. Collectaneen - H. P. W. Anders und Dr. CrullM alte Signatur: Bd. 97

Signatur: 5835
Titel: Verwaltung, Einnahme- und Ausgaberechnungen und Belege des __"Gödert von der Fehrschen Legats" zur Aussteuer armer Mädchen__
Enthält auch: Enthält auch: Notizen aus dem Nachlaß des Advokaten __G. C. Lemke zur Geschichte der Familie Gödert von der Fehr.__
Laufzeit: 1825-1952
Alte Signaturen: XXIII.11.8; Private Stiftungen Nr. 128
Bestellnummer: Stadtarchiv Wismar (Abt. III. Rep. 1. Aa Ratsakten 14. Jh. - 1945) 5835
Standort: Stadtarchiv Wismar - Ratsakten 14. Jh. - 1945 - 21. (XXIII) Geistliche Sachen - 21.4. Legate, Stiftungen und Stipendien',
                'format' => 'markdown',
            ],
            [
                'id' => 14,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-09-20 07:58:25',
                'modified' => '2022-01-17 00:05:56',
                'name' => 'Adressen Wismar',
                'category' => 'Wismar',
                'content' => 'Test2 __Museum__

Corinna Schubert, Leiterin des Stadtgeschichtlichen Museums, Beguinenstraße 4
03841 2243-111
03841 2243-112
cschubert@wismar.de

Dr. Karen E. Hammer, Sachbearbeiterin Sammlung + Ausstellung
03841 / 2243-111
KHammer@wismar.de

__Denkmalpflege__

Christiane Bens
03841 251 6033
0151 10715277
cbens@wismar.de

Ulrike Willert
Dipl.- Ing. Architektin
Sachbearbeitung Denkmalpflege/ Denkmalschutz
Bauamt
Abt. Sanierung und Denkmalschutz
Kopenhagener Straße 1
23966 Wismar
03841/ 251 6037
UWillert@wismar.de

__Stadtkirchenamt__

Dipl.-Ing. Thomas Junggebauer
Sachgebiet Kirchenbau,
Hinter dem Rathaus 6
Tel. 03841 251-9040
TJunggebauer@wismar.de

Marienkirchturm
Frau Nöhring
03841 251 3029


zuständig für den Zugang zur Wasserkunst:
Gebäudemanagement, Herr Wiechmann, Herr Rode

Matthias Zahn

__Stadtarchiv__

Altwismarstraße 7 - 17
23966 Wismar
Telefon: 03841 251-4080
FAX: 03841 251-4082

Benutzereingang über den Hof Gerberstraße 9a

http://www.wismar.de/index.phtml?sNavID=1800.197&La=1

Lesesaal:
Montag geschlossen
Dienstag und Mittwoch 9.00 - 15.30 Uhr
Donnerstag 9.00 - 17.30 Uhr
Freitag geschlossen

Martina Pyl, Sachbearbeiterin, Bibliothekarin der Ratsbibliothek
Tel. 03841 251-4083
MPyl@wismar.de

Nils Jörn
Telefon 03841 251-4080
 NJoern@wismar.de /nilsjoern@aol.com


__St. Nikolai__

Kirche St. Nikolai: 23966 Wismar, Nikolaikirchhof
Sprechzeiten Gemeindebüro: montags 8 - 12 Uhr, dienstags 8 - 14 Uhr
Pastor: Roger Thomas
Spiegelberg 14, 23966 Wismar, Tel. / Fax: (03841) 21 36 24
mobil: 0175 / 73 82 46 40
nikolai-wismar@kirchenkreis-wismar.de
wismar-nikolai[at]elkm.de

Martin Poley, Küster in St. Nikolai, St.-Nikolai.Kirchhof 11
Küster / Team Offene Kirche: mobil 01520 366 94 69

__Heiligen Geist__

Ev.-luth. Kirchengemeinde Heiligen Geist Wismar
Thomas Cremer, Pastor
Lübsche Str. 31, 23966 Wismar, Tel. / Fax: (03841) 28 35 28
mobil  0176  78 41 73 68
E-mail: wismar-heiligen-geist[at]elkm.de
Hospitalkirche Heiligen Geist: 23966 Wismar, Lübsche Str. 31
Sprechzeiten Gemeindebüro: donnerstags und freitags  8 - 13  Uhr

Küsterin: Ulrike Rachholz Tel.: (0157) 77 85 87 20

__Kirchgemeinde St. Marien und Georgen__

Pastor Thorsten Markert
Büro: Baustr. 27, 23966 Wismar
Tel.: 03841 / 282549
mobil: 0152 27 23 69 05.

Neue Kirche: 23966 Wismar, Marienkirchplatz
Öffnungszeiten der Neuen Kirche: dienstags bis freitags 10–14 Uhr, samstags 10–16 Uhr

Caroline Blank, Gemeindesekretärin,

Sprechzeiten:
mittwochs bis freitags 9 bis 12 Uhr
sowie mittwochs von 14 bis 16.30 Uhr
Tel.: 03841/ 282549


Fotoklub Wismar e. V.
Gerd Falk
g.falk@tracon-wismar.de
mobil1: 0151 649 36 928
mobil2: 0172 860 63 98
privat:     03841-641728
dienstlich:     03841-40130



Planungsbüro Angelis und Partner, zuständig für St. Georgen und St. Nikolai
Kristian Fleischhack
03841 40293

Bauunion Wismar, Herr Uhle
03841 450124
0170 6340835
Telefonat am 11. April 2016 ca. 10.30 h
betreffs Auslegen der Grabplatte in St. Nikolai
6 größere, 2 kleinere Platten
4 bis 5 Arbeiter für 8 Stunden, 35 €/h
und Zuschlag für Technik
Gesamtkosten etwa 2000 €

Stefan Wigbrand (Grabungshelfer)
Juri Gagarin-Ring 58
23966 Wismar
stefanwigbrand1@gmail.com

Günther Faust  (ehem. Denkmalpflege Wismar)
Zur Mühle 3
23996 Dambeck
038424 20700

',
                'format' => 'markdown',
            ],
            [
                'id' => 15,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-10-18 12:36:05',
                'modified' => '2019-05-19 15:39:02',
                'name' => 'Markdown',
                'category' => 'Entwicklung',
                'content' => 'In-Browser-Editor: [StackEdit](https://stackedit.io/)

[Handbuch in Markdown](https://digitale-methodik.adwmainz.net/home/) <!-- username: herold-->

',
                'format' => 'markdown',
            ],
            [
                'id' => 16,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-11-08 10:00:34',
                'modified' => '2021-08-24 12:33:43',
                'name' => 'To-Do-Liste Wismar',
                'category' => 'Wismar',
                'content' => '#####Wismarfahrt August 2021
######Mona
- Archiv: Stadtarchiv Wismar (Abt. III. Rep. 1. Db Suppliken an den Wismarer Rat) (1) 1782 und Antwort darauf für die Kanzel in Neustadt-Glewe
- Archiv: alteschule.predella: Signatur 3563, alte Signatur: XV.24.II, Verwaltung des Museums für Kunst und Altertum Wismar, Bestellnummer: Stadtarchiv Wismar (Abt. III. Rep. 1. Aa Ratsakten 14. Jh. – 1945) 3563, Laufzeit 1863–1925 => steht da was zur Provenienz? Und zur Zughörigkeit zu einem Retabel (bspw. das, das auch auf dem Foto ist)
- Archiv: dominikaner.chorinschrift: fragen, ob das Archiv noch die von Crull (Inschrift im Dominikaner-Kloster, S. 26) erwähnte Schrift Schröders „Ausführliche Beschreibung der Stadt Wismar“ haben; Original verloren und Auszüge als Typoskript in Abt. VIII Rep. 2, Kastenarchiv Techen, Kasten V?
- Archiv: marien.epi-kalen: Ratsakten 14. Jh.–1945, Nr. 5520; in Ariadne steht „Katharina Kalen, geborene Wenck, Witwe Jost Kalens“; laut Schröder ist sie aber vor ihm gestorben -> Kann man herausfinden, was davon stimmt
- Archiv unwichtig: nikolai.altarleuchter-kladow: Signatur (1) 0821, Abt. II. Rep. 1. B. Testamente 1441-1865; Testament Agneta Martens zu Datierung
- Archiv unwichtig: georgen.kronleuchter2: Namen recherchierbar?
- Museum unwichtig: museum.kachelmodel.aurani: Maße (auch Bu.) und publizierbares Foto
- Museum unwichtig: museum.kachelmodel.melger-grise: Maße (auch Bu.) und publizierbares Foto
- Heilig-Geist wahrscheinlich nicht möglich, weil noch in der Retaurierung?: georgen.altar-martin_georg: Malerei der geschlossenen Ansicht größtenteils verloren: stimmt das? kein Foto da
- Nikolai unwichtig: georgen.kronleuchter1: fotografieren
- Nikolai unwichtig: nikolai.epitaph-schabbelius: Maße und teilweise Bu.
- Nikolai: georgen.hochaltar: Autopsie D2 mater pulchre (fehlt auf Foto)
- Nikolai: georgen.hochaltar: H1 manibus überprüfen; Schriftbänder in Medaillons lesen (B2, B4, B6, B12); H12 am Original überprüfen, v. a. nach Resten vom O am Anfang suchen; einige Bu. fehlen (A1, D, E, F, G, H, R)
- Georgen unwichtig: georgen.wandmal.tidekap1: gucken, ob bei der Kreuzigungsgruppe auf der Südseite das INRI noch da ist
- draußen unwichtig: schabbelhaus: Foto/Maße
- draußen unwichtig: ratsapotheke: Foto/Maße


#####Wismarfahrt 14. Oktober
######Mona
- Archiv: Schreiben an den Rat zur Übernahme von Objekten aus dem Dominikanerkloster ins Museum: AHW, Signatur: 3562, alte Signatur: XV.24.I, Förderung und Übernahme des Museums für Kunst und Altertum durch die Stadt, Bestellnr. nach Ariadne: Stadtarchiv Wismar (Abt. III. Rep. 1. Aa Ratsakten 14. Jh. - 1945) 3562, Laufzeit 1863-1935
- Archiv: Inventarverzeichnis Museum Alte Schule: AHW, Signatur: 3563, alte Signatur: XV.24.II, Verwaltung des Museums für Kunst und Altertum Wismar, Bestellnr. nach Ariadne: Stadtarchiv Wismar (Abt. III. Rep. 1. Aa Ratsakten 14. Jh. – 1945) 3563, Laufzeit 1863–1925
- Archiv: fragen, ob sie Schröders "Ausführliche Beschreibung der Stadt Wismar" noch haben; möglicherweise Original verloren und Auszüge als Typoskript in Abt. VIII Rep. 2, Kastenarchiv Techen, Kasten V
- Archiv: Erbschaftssachen Katharina Wenck Witwe von Jost Kale 1566; Bestellnr. nach Ariadne Stadtarchiv Wismar (Abt. III. Rep. 1. Aa Ratsakten 14. Jh. - 1945) 5520; Standort nach Ariadne: Stadtarchiv Wismar - Ratsakten 14. Jh. - 1945 - 20. (XX.) Erbschafts- und Nachlaßsachen, Nächstzeugnisse, Geburtsbriefe, Echtbriefe, Bestätigungen, Zehntsachen, Abschoß - 20.3. Inventare, Erbschaftssachen, Testamente, Vormundschaften; Laufzeit 1551–1579; alte Signatur: XX.P; Signatur: 5520 (für marien.epi-kalen)
- Archiv: Echtbriefe des Pfarrers zu Schenefeld und der Stadt Itzehoe für die Geschwister Hadenfeld zum Nachlaß Katharina Kathens, geborene Wenck, Jost Katens Frau; 1566; alte Signatur XX.D.2; Bestellnr. nach Ariadne: Stadtarchiv Wismar (Abt. III. Rep. 1. Aa Ratsakten 14. Jh. - 1945) 5432/15; Standort nach Ariadne: Stadtarchiv Wismar - Ratsakten 14. Jh. - 1945 - 20. (XX.) Erbschafts- und Nachlaßsachen, Nächstzeugnisse, Geburtsbriefe, Echtbriefe, Bestätigungen, Zehntsachen, Abschoß - 20.2. Geburtsbriefe, Echt- und Nächstzeugnisse; Signatur: 5432/15 (für marien.epi-kalen)
- Georgen: für georgen.wandmal-tidekap1: gucken, ob bei der Kreuzigungsgruppe auf der Südseite das INRI noch da ist

######Jürgen
- AHW
    - Bestellnummer 097 (Abt. VIII. Rep. 4 Nachlässe - 01. Collectaneen - H. P. W. Anders und Dr. Crull)
    - Bestellnummer 5833 (Abt. III. Rep. 1. Aa Ratsakten 14. Jh. - 1945 - 21. (XXIII) Geistliche Sachen - 21.4. Legate, Stiftungen und Stipendien)
    - Abt. VI, Rep. 1, B Zeugenbuch (Kleines Stadtbuch, Liber parvus civitatis), Bde. 1-11
- AHW Fotosammlung (bestellen)
    - Grabplatte für Dorti Dargun: Fotosammlung A-C - Glasplatten und Negative - Alte Signaturen: B, Grabsteine und -kreuze, 2 - Bestellnummer: (1) 1039
- Denkmalpflege
    - kürzlich auf dem Georgenkirchhof aufgefundene Platte für Markus Burmeister und Gertrud Kröger hwi.georgen.ct027 autopsieren
    - hwi.marien.ct+64, Wappen-GP für Balthasar von Schöneich etc., ehemals Trägerstein einer Messingplatte ==> Verbleib der Fragmente auf dem Georgenkirchhof klären
    - nach dem Verbleib der ehemaligen Altarplatte St. Marien (hwi.marien.ct+65 ) fragen und dieselbe autopsieren, Maße ergänzen
- St. Nikolai
    - hwi.nikolai.ct059, Grabplatte für Nikolaus van der Molen, für Tymme und Claus Dargun sowie für Michel Baumann (die eingehauene Nummer ist 57) ==> aus den beiden Bruchstücke in der Südvorhalle die Gesamtlänge ermitteln
    - hwi.nikolai.ct107, Grabplatte für Brinker Mane, Marquart Mane und seine Ehefrau Telzeke, für Johann Kroger, Johann Ruge und Jürgen Juhl - im nordwestlichen Joch der Nordvorhalle, direkt vor dem Portal ==> auf allen Fotos fehlt der Rand an einer Schmalseite, darauf vielleicht die Nummer?


---

#####Wismarfahrt 23. Juli 2020
+ Archiv JH
    + AHW Ratsakten: Auseinandersetzung mit Angehörigen adliger Familien A-Z, darunter auch Stralendorf, Averburg und Raven [... link](https://ariadne-portal.uni-greifswald.de/?arc=15&type=obj&id=5287117)
    * hwi.georgen.kronleuchter2: Amt der Zimmerleute >>> Namen im Stadtarchiv recherchieren, VI, Rep. 7, Zunftbücher etc./ Cord Hovier, Borchart van der Luthe, Jacob Harbrech, Marten Schomaker
    * Testament Anna Kladow - AHW, Abt. II. Rep. 1. B Testamente 1441-1865, Nr. 0500, 1630-1637, betrifft hwi.nikolai.altarleuchter-kladow
    * Ludolf Zwinagel im zweiten Zeugenbuch p./f. 60 (1430-1490) => hwi.georgen.ct280
    * Wurzel Jesse: Stadtarchiv - Crull-Collectaneen / Fotosammlung: 1523, 1527
    + Schröder, Ausführliche Beschreibung (verloren) - event. noch was vorhanden/bekannt?
    + Abt. VIII. Rep. 3. A3 Fotosammlung E - Sakralbauten // Glockenfotos
     + 7 (Schwarzes Kloster)
     + 8 (St. Georgen)
     + 9 (St. Georgen)
     + 13 (St. Nikolai)
     + 14/00 - 14/12 (St. Marien)
    + Henrich von Stammer, 1635 bei Dömitz in schwedische Gefangenschaft, 1637 in Wismar hingerichtet wegen Verrats - hwi.marien.ct+05 (kopial)
    + Zeugebücher
     + Buch 1 (1430-1490)
              + Ludof Zwinagel (hwi.georgen.ct280)
     + Buch 2 (1490–1518)
            + Timme Bohnsack (hwi.georgen.ct284)
            + Bertholomeus Netke (Grabplatte hwi.nikolai.ct188)
     + Buch 3 (1518-1531)
            + Dirick Lost (hwi.nikolai.ct214)
     + Buch 5 (1541-1550)
            + Goldschmiedegeselle Hans Wiggeland (Grabfliese hwi.heilgeist.ct005)
     + Buch 6 (1550-1562)
            + Berent Rantze im Zeugenbuch 6, 7, 8  (Grabplatte hwi.marien.ct336)
            + Hinrick Warckman (hwi.nikolai.ct061, hwi.marien.ct322)
     + Buch 7 (1569-1586) und 8 (1586-1595)
            + Berent Rantze im Zeugenbuch  6, 7, 8 (Grabplatte hwi.marien.ct336)


+ Archiv MD
    + Crull, Collectaneen Bd. 45 und 54
    + Inventarverzeichnis Museum Alte Schule: AHW, Signatur: 3563, alte Signatur: XV.24.II, Verwaltung des Museums für Kunst und Altertum Wismar, Bestellnr nach Ariadne: Stadtarchiv Wismar (Abt. III. Rep. 1. Aa Ratsakten 14. Jh. – 1945) 3563, Laufzeit 1863–1925
    + Fotos
      + Foto der Chorinschrift im Dominikanerkloster in den Crull-Collectaneen: AHW, Abt. VIII. Rep. 4 Nachlässe – 01. Collectaneen – H. P. W. Anders und Dr. Crull, Bd. 48 (Bestellnr. nach Ariadne: Stadtarchiv Wismar (Abt. VIII. Rep. 4 Nachlässe) 048)
      + Wurzel Jesse
         + Fotosammlung B 18.2. St. Nikolai (Bestellnr. nach Ariadne: Stadtarchiv Wismar (Abt. VIII. Rep. 3. A1 Fotosammlung A-C - Glasplatten und Negative) (1) 1527); alte Signatur (1) 1527
         + Fotosammlung B 18.2. St. Nikolai (Bestellnr. nach Ariadne: Stadtarchiv Wismar (Abt. VIII. Rep. 3. A1 Fotosammlung A-C - Glasplatten und Negative) (1) 1523); alte Signatur (1) 1523
      + Georgen Tidekapelle
         + Fotosammlung A , St. Georgen (Bestellnr. nach Ariadne: Stadtarchiv Wismar (Abt. VIII. Rep. 3. A1 Fotosammlung A-C - Glasplatten und Negative) (1) 0375); alte Signatur: A VIII, 43
         + Fotosammlung A , St. Georgen (Bestellnr. nach Ariadne: Stadtarchiv Wismar (Abt. VIII. Rep. 3. A1 Fotosammlung A-C - Glasplatten und Negative) (1) 0377); alte Signatur: A VIII, 43
         + Abt. VIII. Rep. 1. B1 Crull-Sammlung, 06.: VI Einzelne Kunstwerke und Altertümer, 06.04.: 06. D. Malerei 0966 (VI. D. 7)
         + Abt. VIII. Rep. 1. B1 Crull-Sammlung, 06.: VI Einzelne Kunstwerke und Altertümer, 06.04.: 06. D. Malerei 0967 (VI. D. 8)
+ Museum
    + Tafelbild im Magazin AHW
    + Kachelmodel
    + Glasscheibe
    + Teppich
    + Maueranker 1571
+ Hegede 1 - Maueranker
+ Papenstraße 16 - Tafel D. B.
+ Wassertor
+ Maueranker Schabbelhaus
+ Fürstenhof
+ Georgen Wandmalerei Buchstabenhöhe
+ Nikolai
    + Triumphkreuz
    + Wurzel Jesse
    + Gewölbeinschrift


##### Datenbankfehler:
* verwaiste Fußnoten:
    * hwi.nikolai.totentanz2
    * hwi.nikolai.ct177

##### Register Texttypen
"Sterbevermerk" ergänzen um die Untereinträge "mit nachgetragenem Sterbedatum" und "mit offenem Sterbedatum"
==> Hiwis: Artikel durchsehen und nachtragen


##### Archivrecherchen



#####Kanzel der Heiliggeistkirche in Neustadt-Glewe
Anruf Sakowski am 10. Sept. 2019
hat das Meisterzeichen von Tönnnies Ewers auf dem Unterhang entdeckt: TE, darunter die Meistermarke, geschnitzt
die alten gemalten Inschriften scheinen auf dem Kanzeldeckel unter den jüngeren hervor
der Schalldeckel ist nach Lübecker Vorbild gefertigt
zwei Restauratorinnen aus Schwerin haben sich mit der Kanzel befasst
Sakowski hat Expose mit Fotos geschickt

##### Marken

bei folgenden Marken konnten die Probleme, dass sie im Epigraf nicht angezeigt werden oder doppelt vorhanden sind, noch nicht gelöst werden:

- HM.hwi.n.ct206b.1133
- HM.hwi.n.ct246.1033
- HM.hwi.nct1+.2112
- HM075.3202
- HM184.3111

##### St. Nikolai

* hwi.nikolai.gp248 in der Turmluke, besonders Inschrift A autopsieren (JZ?, Name?)



#####Kostprobe
hwi.stadtmauer
Denkstein Werkmann hwi.museum.denkstein-werkmann
Thomasaltar hwi.georgen.altar-thomas
hwi.nikolai.kelch5
Oblatendose Stralendorf hwi.marien.oblatendose
Rathaus Wandinschrift Trunkenheit hwi.rathaus.wandinschrift
Glocke
Gruppe aus Grabplatten, Epitaph, event. Leuchter o.ä. (Stralendorf)

Gruppe aus 9 miteinander in Beziehung stehender Objekte ausgehend von einer Glocke  (1 Glocke, 6 Grabplatten, 1 Epitaph, 1 Set aus drei Wandleuchtern)

* hwi.marien.glocke07 (Stifter: Plesse/Wopersnow/Warnstedt - Pastor Joachim Hertzberg ließ sie nach Wismar bringen)
    + hwi.marien.ct219 (Grabplate für Markus Tanke, dessen Tochter Magdalena den Pastor Anton Hertzberg (Joachims Vater) heiratete; die Grabstelle kam 1655 an Dr. med. Anton Hertzberg, Bruder von Joachim; Magdalena hatte eine Schwester Engel Tancke, s. u.)
     + hwi.marien.ct090 (Engel Tancke, verheiratet mit 1. Nikolaus Karow, 2. Elias Judelius)
      + hwi.marien.epitaph-karow
      + hwi.marien.armleuchter-ej (drei Armleuchter gestiftet von Elias Judelius)
    + hwi.marien.ct018 (Grabplatte Pastor Joachim Hertzberg)
    + hwi.nikolai.ct024 (Grabplatte Pastor Anton Herztberg, verheiratet mit Magdalena Tanke, Eltern von Joachim)
     + hwi.nikolai.ct173 (Grabplatte für u. a. Daniel Borchwardt, Urenkel von Pastor Anton Hertzberg)
    + hwi.georgen.ct035 (Grabplatte Daniel Hertzberg, Ratsherr, Bruder von Joachim)

Gruppe von 10 Objekten um Balthasar von Schöneich und Catharina Stralendorf   (2 Oblatendosen, 1 Kronleuchter, 2 Kanzeln, 1 Epitaph, 1 Gemälde, 3 Grabplatten)
==> via Personenregister können alle über Catharina Stralendorf aufgerufen werden, außer hwi.marien.kanzel (noch nicht fertig)

+ hwi.marien.oblatendose
+ hwi.georgen.oblatendose2
+ hwi.marien.ct+64
    + hwi.marien.kanzel
+ hwi.marien.epitaph-1595
+ hwi.marien.kronleuchter
    + hwi.marien.kanzel
+ hwi.marien.ct+64s
+ hwi.georgen.kanzel
+ hwi.marien.gemälde-schöneich
+ hwi.marien.ct305',
                'format' => 'markdown',
            ],
            [
                'id' => 17,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-11-26 12:22:57',
                'modified' => '2019-12-17 09:20:26',
                'name' => 'Förderantrag',
                'category' => 'Organisation',
                'content' => 'Niedersächsisches Ministerium für Wissenschaft und Kultur / Volkswagenstiftung
Förderantrag Geistes- und Kulturwissenschaften - digital

[Pressemitteilung](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=documente&filename=PMGeistes-undKulturwissenschaftendigital.pdf)

[Ausschreibung](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=documente&filename=180928_Ausschreibung_Geisteswissenschaften_Digital.pdf)

[__FAIR__ Data Prinzipien für Forschungsdaten](https://blogs.tib.eu/wp/tib/2017/09/12/die-fair-data-prinzipien-fuer-forschungsdaten/)

__F__indable
__A__ccessible
__I__nteroperable
__R__eusable

### Semantic Web Konzepte

[BEACON](https://de.wikipedia.org/wiki/Wikipedia:BEACON)
[Typo3-App: BEACONIZER](https://prezi.com/nx6zjrh_4lal/der-gnd-beaconizer-personennormdaten-in-online-applikation/)

### Thesauri

[Wikipedia: Thesaurus](https://de.wikipedia.org/wiki/Thesaurus)

[Objektdatenbanken in Museen weltweit](http://www.kunst-und-kultur.de/index.php?Action=showMuseumDatabases)

[Joachim Schmid: Textsorten](http://www.joachimschmid.ch/docs/DMtTextsort.pdf)

[Getty Thesaurus of Geographic Names](https://de.wikipedia.org/wiki/Getty_Thesaurus_of_Geographic_Names)

### Temporal Tagging

* [HeidelTime](https://heideltime.ifi.uni-heidelberg.de/heideltime/)
* [timeML](www.timeml.org/publications/timeMLdocs/timeml_1.2.1.html)
* [histHub](https://histhub.ch/)
* [OpenRefine](https://en.wikipedia.org/wiki/OpenRefine)
* [perio.do](https://perio.do/en/)
* [chronotology](https://chronontology.dainst.org/)
* [Godot](http://www.lust-auf-rom.de/epigraphy_info/godot.pdf)
',
                'format' => 'markdown',
            ],
            [
                'id' => 18,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-11-27 10:08:27',
                'modified' => '2020-08-29 08:52:46',
                'name' => 'XML u. Co.',
                'category' => 'Entwicklung',
                'content' => '__Übersicht__

[A. Register](#register)

* [Aufgaben](#aufgaben)
* [Anleitung für das Suchen mit Regulären Ausdrücken in Word](#anleitung)
* [Offene Fragen](#offenefragen)
* [Korrigenda](#korrigenda)
* [Workflow der Registerdigitalisierung](#workflow)
    * [Schematische Übersicht](#organigramm)
    * [x.register.doc](#register_doc)
    * [x.register_proto.xml](#register_proto)
    * [x.register_base.xml](#register_base)
    * [x.register_trans1.xml](#register_trans1)
    * [x.register_trans2.xml](#register_trans2)
    * [x.register_view.htm](#register_view)
    * [x.register_typo3.xml](#register_typo3)
* [Tips](#tips)

[B. Oxygen](#oxygen)

[C. Epidoc](#epidoc)

[D. Reguläre Ausdrücke](#regex)

----

<a name="register"></a>
## Register

<a name="aufgaben"></a>
### Aufgaben

* __Jonas__
    * Lüneburg
    * Regensburger Dom DI95
* __Mona__
    * Northeim
    * di74 Regensburg
* __Oskar__
    * Ingolstadt
* __Jürgen__
    * Zeitz

<a name="anleitung"></a>
### Anleitung für das Suchen mit Regulären Ausdrücken in Word:

[1. PDF](https://www.google.com/url?sa=t&rct=j&q=&esrc=s&source=web&cd=2&cad=rja&uact=8&ved=2ahUKEwjP9d-JlIfkAhUiIMUKHV8mALEQFjABegQIAhAC&url=http%3A%2F%2Fwww.provincia.bz.it%2Flandmaus%2Fde%2Flandmaus-online%2Ftipps-tricks.asp%3F%26somepubl_action%3D300%26somepubl_image_id%3D177668&usg=AOvVaw0baD5FjdxtI5Oubq8vsEiR)

[2. PDF](https://www.google.com/url?sa=t&rct=j&q=&esrc=s&source=web&cd=1&cad=rja&uact=8&ved=2ahUKEwjP9d-JlIfkAhUiIMUKHV8mALEQFjAAegQIBRAC&url=https%3A%2F%2Fwww.lubasch.ch%2Foffice%2Fword%2Fword_tipps%2Fword_platzhaltersuche.pdf&usg=AOvVaw0Q1ozObat4iI33jKUp5SFi)




<a name="offenefragen"></a>
### Offene Fragen

Oskar: Beim Vorgang "Items taggen 1-4" werden weder die `<nrn>` tags gesetzt, noch die Kursivierung getaggt.

<a name="korrigenda"></a>
### DIO - Korrigenda

* di29 worms Register Zitate 2. dt. "(L) = Lutherbibel" ist sehr groß dargestellt; lat. nach "Rm" fehlt 2x der Punkt

<a name="workflow"></a>
### Workflow Registertransformation

<a name="organigramm"></a>
#### Schematische Übersicht
![Registertransformation](https://epigraf.inschriften.net/files/download?path=Notizen&filename=XML-Transformation.png)

<a name="register_doc"></a>
#### x.register.doc

Das Word-Dokument des Registers wird geöffnet.

Kursivierungen:

* Kursive Wörter oder Wortgruppen, die weder Gruppenüberschriften darstellen noch ehemalige Standorte, werden mit je einem Unterstrich direkt vor und nach dem kursiven Wort oder der Wortgruppe versehen.
* Wenn außerhalb des Standorte-Register Artikelnnummer kursiv auszugeben sind, werden sie am Ende mit dem Zeichen § markiert (siehe DI 84, Weilheim-Schongau, Reg. 5).

Die hier folgend aufgeführten Markierungen bestimmter Register-Elemente am Beginn der Absätze und die weiteren Schritte können auch in x.register.proto.xml vorgenommen werde. Im Word-Dokument müssen sie immer am Zeilenanfang stehen, in x.register.proto.xml im p-Element ganz am Anfang.

* Notizen: `=`
* Register-Überschriften: `##`
* Gruppen erste Ebene: `###`
* Gruppen zweite Ebene: `####`
* Gruppen dritte Ebene: ` #####` usw.
* Klammern: `~` Hiermit werden Gruppen innerhalb von Einträgen markiert, die Untereinträge mit Anstrich enthalten, welche ihrerseits (zugleich) Untereinträge des über der Gruppe/Klammer stehenden Eintrags sind, siehe DI 95 (Regensburger Dom 2), Register 3. Stände - Berufe - Titel usw.
* im Standortregister ehemalige (kursive) Standorte: §; externe Standorte: !; ehemalige externe Standorte: $!
* Registereinträge der ersten Ebene werden nicht markiert, Untereinträge, Unter-Untereinträge usw. mit Bindestrich (einfacher Bindestrich, kein Halbgeviertstrich o. ä.) und Leerzeichen in der entsprechenden Anzahl
* innerhalb einer Zeile werden Lemma, Verweise, Referenzen auf Artikel und Referenzen auf Seiten in der Einleitung durch Semikola von einander getrennt: Wollust;233;s. a. Laster
* Wörter in Großbuchstaben oder Kapitälchen müssen normalisiert werden

<a name="register_proto"></a>
#### x.register.proto.xml

Es wird eine .xml-Datei erstellt, in der der markierte Text aus dem .doc-Dokument eingefügt wird.

Jetzt müssen alle Zeilen in ein `<p>-tag` via RegEx gesetzt werden:

* suchen: ((.)*)
* ersetzen: &lt;p&gt;$1&lt;/p&gt;

Wenn nicht schon in x.register.doc geschehen, werden die oben erläuterten Markierungen unmittelbar nach `<p>` gesetzt.

Weitere Hinweise:

Gruppen mit Gruppen-Lemma (explizite Gruppen) bekommen die Rauten in das p-tag in dem auch das Lemma steht
für Gruppen ohne Gruppenlemma (implizite Gruppen) muss ein p-tag dort angelegt werden, wo die Gruppe beginnt, d. h. direkt über dem ersten Eintrag zu dieser Gruppe; in das zu setzende p-tag werden nur die Rauten (in der der Ebene entsprechenden Anzahl) eingetragen.

Beispiel explizite Gruppe:
`<p>###deutsch</p>`
`<p>Alls hernach es muas einmall gestorben sein;537</p>`
`<p>Als nach M. d. das Zwelft iar lag;102</p>`
`<p>Aus dem Feuer bin ich geflossen;580</p>`

Beispiel implizite Gruppe:
`<p>###</p>`
`<p>Alls hernach es muas einmall gestorben sein;537</p>`
`<p>Als nach M. d. das Zwelft iar lag;102</p>`
`<p>Aus dem Feuer bin ich geflossen;580</p>`

Wenn auf eine Gruppe nicht zu gruppierende Items folgen ( d. h. wenn die Gruppe vor dem Registerende oder dem Ende der übergeordneten Gruppe endet) muss vor dem ersten dieser Items ein p-tag mit drei Rauten (in x.register.word.doc nur drei Rauten) gesetzt sein, um zu markieren, wo die vorausgehende Gruppe endet. Wenn es sich um Untergruppen handelt, muss die Anzahl der Rauten dem Level gemäß erhöht werden. Hinter die Rauten wird ein Blockade-Zeichen ■ gesetzt:
`###■` bzw.  `<p>###■</p>`
Das Blockadezeichen erlaubt es, in einem der nachfolgenden Transformationsprozesse, den Gruppenkontainer wieder zu entfernen.
Das Zeichen ■ kann auf dem Nummernblock mit ALT+254 erzeugt werden.


In den öffnenden indices-tag müssen die Attribute band-nr="" und band-titel="" gesetzt werden, der Bandtitel ist der volle Titel des Bandes; handelt es sich um einen Münchener Band, muss zusätzlich das Attribut modus="bay" gesetzt werden, das bewirkt, dass die Standorttabelle richtig verarbeitet wird. Beispiel: DI 74

`<indices band-nr="52" band-titel="Die Inschriften der Stadt Zeitz" band-typ="di" basis-standort="Zeitz">`

Der Band Typ lautet bei retrodigitalisierten Bänden immer "di" (Deutsche Inschriften). Bei Bänden, die nicht gedruckt werden lautet der Band Typ "dio" (Deutsche Inschriften Online).

Stehen zwischen dem öffnenden p-tag und einer Raute noch Leerstellen, müssen diese entfernt werden.

Sollten in der Ursprungsdatei des Registers (x.register.doc) Semikola verwendet worden sein, die bei der Transformation Fehler verursachen (z. B. DI 74, Reg. 2a Wappen), müssen diese durch das Zeichen ^ ersetzt werden, dies wird später automatisch wieder in ein Semikolon umgewandelt. Das betrifft alle Semikola, die nicht der Trennung zwischen Lemma, Artikelnummern, Verweisen u. dgl. dienen.
Vorgehensweise (für DI 74, Reg. 2a Wappen):

* das Teilregister isolieren
* suchen nach: `(\\([^)]+)`
* ersetzen durch: `$1^`

Die Anstriche vor den Untereinträgen müssen nicht händisch entfernt werden, dies geschieht später automatisch.

Muster in: Z:\\DIO\\Gesamtregister DI\\Bandregister\\di52 zeitz\\di52.register.xml

Transformation mittels `items_taggen.xsl`

Wenn das Dokument in dieser Weise vorbereitet wurde, kann es durch xsl-Transformation weiterverarbeitet werden.
Dazu werden nacheinander die Stylesheets `items_taggen1.xsl` bis `items_taggen_4. xsl` aufgerufen.
In Oxygen können alle vier in ein einziges Transformation-Szenario eingebunden werden.

##### Transformationsszenario mit Oxygen - Szenario anlegen

* die zu transformierende Datei in den Editor- Vordergrund holen
* Werkzeug Transformations_Szenario aufrufen ![Werkzeug Transformation](https://epigraf.inschriften.net/files/download?path=Notizen&filename=WerkzeugTransformation.PNG) (Button)
* auf Neu klicken
* XSL-TRansformation mit XSLT wählen
* Namen angeben
* Transformater auswählen: Saxon6.5.5
* XSL-URL: erstes Stylesheet auswählen: file:/Z:/DIO/Gesamtregister%20DI/Register%20Schemata%20und%20Stylesheets/items%20taggen1.xsl
* gegebenenfalls über Button  "Zusätzliche XSLT-STylesheets"  weitere Stylsheets einbinden (wenn nacheinander mehrere TRansformationsschritte erfolgen sollen)
* >> `Ausgabedatei` >> `Im Editor öffnen`anklicken
* OK
* Button `Zugeordnete anwenden`

Zum Überprüfen in das Ergebnis von `items_taggen` das Schema `register_base.xsd` einbinden.


#####Umbruch bei Vornamen durch RegEx einfügen

In manchen Registern ist der Vorname nicht als Untereintrag in einen separaten Absatz mit Bindestrich und Leerzeichen am Anfang gesetzt (z.B. DI99 Ingolstadt). Dies lässt sich durch RegEx korrigieren:

* Vorher:

Plümel, Johann;73
- Leonhard;73

* Nachher:

Plümel
- Johann;73
- Leonhard;73

*__Vorgehensweise__ - Suchen: (\\n[^,^\\n^;^-^–-]+)(,), Ersetzen durch: $1\\n-

Das Suchmuster zeigt, dass als Anstriche mitunter verschiedene Strich-Zeichen (Bindestrich, Halbgeviertstrich usw.) vorkommen. Für die Weiterverarbeitung per XSLT-Transformation dürfen nur Bindestriche U+002D vorkommen, andere müssen durch diese ersetzt werden.
Zwischen Bindestrich und Lemma muss ein einfaches Leerzeichen U+0020 stehen, wenn mehrere Anstriche aufeinander folgen, müssen sie durch einfache Leerzeichen U+0020 getrennt sein.


Zeilen in p-tags setzen durch suchen und ersetzen: suchen: `(.+)` ersetzten: `<p>$1</p>`

<a name="register_base"></a>
#### Bearbeitungsschritte  in x.register_base.xml

1. Nach den Erzeugen der Datei `di~register_base.xml` ist im Editor das Schema `register_base.xsd.` einzubinden. Anschließend kann die Datei gegen das Schema auf Gültigkeit validiert werden.

2. Bei der Transformation durch das Szenario "items_taggen" wird in jedem Register das Attribut `initialgruppen="0"` eingefügt. Das Attribut kennzeichnet, ob in dem betreffenden Register nach dem Wechsel des Initialbuchstabens der Lemmata ein Durchschuss erfolgen soll. Dadurch werden im Register Blöcke mit Einträgen desselben Initialbuchstabens gebildet. Soll das der Fall sein, muss der Wert auf `1` gesetzt werden.

3. Desgleichen wird das Attribut `sortModus="abc"` eingefügt. Damit wird gekennzeichnet, ob die Einsortierung der Einträge bzw. Lemmata streng alfabetisch vorgenommen wurde oder ob p unter b aufgenommen wurde, k unter c, t unter d, v unter f, wie es in den Münchener Bänden die Regel ist. Wenn letzteres der Fall ist, muss im Personenregister der Wert auf `bpckdtfv` gesetzt werden. Das xml-Schema beschränkt die Eingabe auf diese beiden möglichen Werte.

4. In `<indices>` muss das Attribut `band-typ=""` eingefügt und als Wert `di` oder `dio` eingesetzt werden.

5. Bei städtischen Beständen muss in `<indices>` das Attribut `basis-standort=""`  und der Name der Stadt als Wert eingefügt werden.

6. Die Auszeichnung für Kursivierungen durch Unterstriche muss via RegEx als `<kursiv>` getaggt .werden:

* suchen: _([^_]+)_
* ersetzen: <kursiv>$1</kursiv>

<a name="register_trans1"></a>
#### Bearbeitungsschritte  in x.register_trans1.xml

<a name="register_trans2"></a>
#### Bearbeitungsschritte  in x.register_trans2.xml

<a name="register_view"></a>
#### Bearbeitungsschritte  in x.register_view.htm

<a name="register_typo3"></a>
#### Bearbeitungsschritte  in x.register_typo3.xml

<a name="tips"></a>
#### Tips
Das Blockade-Zeichen ■ kann mit dem eingeschaltetem Nummernblock der Tastatur durch ALT+254 erzeugt werden.

## Oxygen {#oxygen}
Arbeitsspeicher erhöhen:
im Programmverzeichnis in der Datei  oxygen<20.1>.vmoptions den ersten Paramter -Xmx4000m erhöhen, z. B. auf 6000m
<20.1>: die spitzen Klammern markieren hier die Programmversion

## Epidoc {#epidoc}

* [Epidoc: Enführung und Referenz](http://www.stoa.org/epidoc/gl/5/)
* [EpiDoc Guidelines](http://www.stoa.org/epidoc/gl/REF2014/)
* [List of all supporting data guidelines](http://www.stoa.org/epidoc/gl/latest/app-allsupp.html)
* [Einführung deutsch](http://www.stoa.org/epidoc/gl/REF2014/intro-eps-de.html)
* [Physical Description](http://www.stoa.org/epidoc/gl/REF2014/supp-description.html)
* [Transkriptionswerkzeuge](http://www.stoa.org/epidoc/gl/REF2014/app-alltrans.html)


[Epidoc - Schema (auf sourceforge.net)](https://sourceforge.net/p/epidoc/wiki/Schema/)

[Schema-Sprache: RELAX NG (rng)](https://de.wikipedia.org/wiki/RELAX_NG)

Literatur:

* [Information Technologies for Epigraphy and Cultural Heritage. Proceedings of the First EAGLE International Conference, 2014](http://archiv.ub.uni-heidelberg.de/propylaeumdok/2337/1/Proceedings%20of%20the%20First%20EAGLE%20International.pdf)
* [Digital and Traditional Epigraphy in Context: Proceedings of the EAGLE 2016 International Conference](https://books.google.de/books?id=kDpvDwAAQBAJ&printsec=frontcover&hl=de&source=gbs_ge_summary_r&cad=0#v=onepage&q&f=false)
* [Crossing Experiences in Digital Epigraphy: From Practice to Discipline](https://www.zenodo.org/record/2593547)



## Reguläre Ausrücke {#regex}

Reguläre Ausrücke testen: [https://regex101.com/](https://regex101.com/)

[Suchen und Ersetzen mit RegEx (Platzhaltern) in Word](https://www.lubasch.ch/office/word/word_tipps/word_platzhaltersuche.pdf)',
                'format' => 'markdown',
            ],
            [
                'id' => 19,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-12-18 14:29:58',
                'modified' => '2019-05-19 15:41:16',
                'name' => 'Inschriftenkommission',
                'category' => 'Organisation',
                'content' => '[Protokoll der Sitzung vom 19. November 2018](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=documente&filename=Protokoll_Sitzung_Inschriften_19_11_2018.docx)',
                'format' => 'markdown',
            ],
            [
                'id' => 20,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-12-19 10:16:45',
                'modified' => '2020-03-24 09:07:19',
                'name' => 'Epigraf-Workshops',
                'category' => 'Organisation',
                'content' => 'Themen für den nächsten Workshop:

* Dateiablage - wird nur in Greifswald genutzt
* Notizen, genutzt in HGW, __GÖ__, Dresden, Halle
* Bildverwaltung
* Marken - Workflow (Zeichnen mit Inkscape, Einbinden der Zeichnungen in Epigraf)
* Ebene Inschriften - Recherchemöglichkeiten
* Volltextsuche über EpigrafWeb
* Verwendung der Kategorien bei Buchstabenverbindungen und Worttrennern/Satzzeichen
* Hochstellung von Buchstaben und Diakritika
* Arbeiten mit regulären Ausdrücken in Word

April 2020

* Marken zeichnen mit Inkskape, Markenverwaltung
* neue Funktionalitäten: Transkriptionsspalten, mehrere Standorte in der Kopfzeile
* Zeilen- und Verszeilenanfänge bzw. -umbrüche
* Klammerregeln bei Auslassungen: eckige Klammern in spitzen Klammern u. dgl.',
                'format' => 'markdown',
            ],
            [
                'id' => 21,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2019-01-15 14:43:58',
                'modified' => '2019-05-19 15:36:41',
                'name' => 'HWI Meisterzeichen',
                'category' => 'Wismar',
                'content' => '1. Goldschmiede (MD)

* Meisterzeichen Andreas Reimers (Abb. Schlie 2, S. 113) :
    * hwi.georgen.kelch4
    * hwi.georgen.kelch5

2. Zinngießer (CM)
Willkommpokale (5):

* hwi.willkomm-böttchergesellen (BZ und 2 MZ, Harmen Claussen)
* hwi.museum.willkomm-kirchner1634 (BZ und 2 MZ)
* hwi.museum.willkomm-kirchner1620 (BZ und 2 MZ, Jochim Schulte)

Stops (11)

* hwi.sm-schwerin.stop-KH370 (MZ nach Abb. Kunzel)
* hwi.museum.stop-3316 (2 HM oder MZ)
* hwi.museum.stop-2952 (BZ und 2 MZ, Paul Auerdiek)
* hwi.museum.stop-2951 (2 Marken)
* hwi.mkg-hamburg.stop-1911.279 (BZ und 2 MZ, Hinrich Ficke)
* hwi.mkg-hamburg.stop-1911.278 (2 MZ, verschlagen, evtl. nach Abb. Kunzel)
* hwi.khm-rostock.stop-z333 (BZ und 2 MZ, Jochim Schulte)

Rörken

* hwi.museum.rörken-1613 (BZ und 2 MZ, Jochim Schulte)

Hausmarke

*  hwi.georgen.kelch7 (Abb. Schlie 2, S. 115)

Meisterzeichen Conrad Willers

* hwi.georgen.oblatendose1 (Foto vorhanden)


',
                'format' => 'markdown',
            ],
            [
                'id' => 22,
                'version_id' => 0,
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2019-01-15 15:14:17',
                'modified' => '2020-10-15 13:20:49',
                'name' => 'Texttypen',
                'category' => 'Richtlinien',
                'content' => 'Stifterinschrift vs Stiftungsinschrift:

*  __Stifter__inschrift: wenn man davon ausgehen kann, dass der in der Inschrift genannte Stifter auch der Initiator der Inschrift ist (der Stifter hat die Inschrift anfertigen lassen (Stifterinschrift = "Inschrift des Stifters")
*  __Stiftungs__inschrift: der Stifter wird nicht namentlich genannt oder der als Stifter genannte ist nicht der Initiator der Inschrift (nicht der Stifter, sonder ein anderer hat die Inschrift anfertigen lassen)
* Stiftername: wenn in der Inschrift nur der Name des Stifters genannt ist (ohne Verb oder Erwähnung des gestifteten Objekts',
                'format' => 'markdown',
            ],
        ];
        parent::init();
    }
}
