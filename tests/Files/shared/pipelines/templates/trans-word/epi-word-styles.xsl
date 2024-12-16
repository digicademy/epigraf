<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml"
xmlns:v="urn:schemas-microsoft-com:vml"
xmlns:w10="urn:schemas-microsoft-com:office:word"
xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core"
xmlns:aml="http://schemas.microsoft.com/aml/2001/core"
xmlns:wx="http://schemas.microsoft.com/office/word/2003/auxHint"
xmlns:o="urn:schemas-microsoft-com:office:office"
xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882"
xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2"
>


<xsl:output method="xml" version="1.0" indent="yes" encoding="UTF-8"/>

  <!-- als einführung und übersicht zu WordML siehe
    http://officeopenxml.com/WPcontentOverview.php (englisch)
    https://www.data2type.de/xml-xslt-xslfo/wordml/ (deutsch)

  -->

<xsl:template name="fonts">
<!--Fonts: Standartfont ist Times New Roman, zusaetzlich eingebunden ist Courier New-->
  <w:fonts>
  <w:defaultFonts w:ascii="Times New Roman" w:fareast="Times New Roman" w:h-ansi="Times New Roman" w:cs="Times New Roman"/>
  <w:font w:name="Times New Roman">
    <w:panose-1 w:val="02020404030301010803"/>
    <w:charset w:val="00"/>
    <w:family w:val="Roman"/>
    <w:pitch w:val="variable"/>
    <w:sig w:usb-0="00000287" w:usb-1="00000000" w:usb-2="00000000" w:usb-3="00000000" w:csb-0="0000009F" w:csb-1="00000000"/>
  </w:font>
  <w:font w:name="Courier New">
    <w:panose-1 w:val="02070409020205020404"/>
    <w:charset w:val="00"/>
    <w:family w:val="Modern"/>
    <w:notTrueType/>
    <w:pitch w:val="fixed"/>
    <w:sig w:usb-0="00000003" w:usb-1="00000000" w:usb-2="00000000" w:usb-3="00000000" w:csb-0="00000001" w:csb-1="00000000"/>
  </w:font>
    <!-- ligaturbögen -->
  <w:font w:name="Araldo">
    <w:panose-1 w:val="02020404030301010803"/>
    <w:charset w:val="00"/>
    <w:family w:val="Roman"/>
    <w:pitch w:val="variable"/>
    <w:sig w:usb-0="00000003" w:usb-1="00000000" w:usb-2="00000000" w:usb-3="00000000" w:csb-0="00000001" w:csb-1="00000000"/>
  </w:font>
    <!-- spitze klammern -->
    <w:font w:name="Cambria">
      <w:panose-1 w:val="02040503050406030204"/>
      <w:charset w:val="00"/>
      <w:family w:val="Roman"/>
      <w:pitch w:val="variable"/>
      <w:sig w:usb-0="E00002FF" w:usb-1="400004FF" w:usb-2="00000000" w:usb-3="00000000" w:csb-0="0000019F" w:csb-1="00000000"/>
    </w:font>
  </w:fonts>
</xsl:template>




<xsl:template name="styles">
<!--Formatvorlagen-->
<!--Absatzformate-->
  <!-- für die absatzkontrolle stehen in w:pPr folgende funktionen zur verfügung:
(1) Das Element <w:keepNext> verhindert einen Seitenumbruch zwischen diesem und dem folgenden Absatz oder Objekt. Es kann auch das Attribut w:val enthalten, das die Ausprägungen on und off besitzen kann und das Zusammenhalten mit dem nächsten Objekt an- oder ausschaltet.

(2) <w:keepLines> verhindert einen Seitenumbruch innerhalb des Absatzes. Es kann ebenso wie <w:keepNext> das Attribut w:val enthalten, das die Ausprägungen on und off haben kann, mit denen ein möglicher Seitenumbruch für den betreffenden Absatz zugelassen oder unterbunden werden kann.

(3) <w:pageBreakBefore> erzwingt einen Seitenumbruch vor dem Absatz. Auch hier ist ein Attribut w:val mit den Ausprägungen on und off erlaubt, mit denen ein Seitenumbruch vor dem betreffenden Absatz erzwungen oder gegebenenfalls gebilligt werden kann.

(4) <w:suppressLineNumbers> kann die in einem Dokument eingestellte Zeilennummerierung wieder ausschalten. Hat das Dokument keine Zeilennummerierung, wird es ignoriert. Es kann das Attribut w:val enthalten, das mit den Ausprägungen on und off die Zeilennummerierung an- bzw. ausschaltet.

(5) <w:suppressAutoHyphens> schaltet die automatische Silbentrennung in einem Absatz aus. Es kann das Attribut w:val enthalten, das mit den Ausprägungen on und off die Silbentrennung an- bzw. ausschaltet.

(6) <w:spacing> behandelt die Abstände vor und nach Absätzen sowie zwischen den Zeilen eines Absatzes. Das Attribut w:before definiert den Abstand vor einem Absatz in Twips (twentieths of a point). 360 Twips entsprechen folglich 18pt. w:after gibt den Abstand nach einem Absatz in Twips an. Das Attribut w:line definiert den Zeilenabstand in Twips.  600 Twips sind eine Umrechnung der Schriftgröße (12pt) multipliziert mit dem Zeilenabstand (2,5-fach) mal 20 (Twips). Das Attribut w:line-rule rechnet beim Wert auto die Abstände abhängig von den Schriftgrößen um. Wäre der Wert exact, so würden die in w:line angegebenen Maße in jedem Fall eingehalten.

(7) <w:ind> bestimmt die Einzüge eines Absatzes. Mit dem Attribut w:left kann man einen linken Einzug in Twips festlegen, mit dem Attribut w:right einen rechten;  mit w:left und w:hanging kann man (mit jeweils gleichen werten) für einen hängenden Einzug sorgen.

(8) Mit <w:jc> lässt sich die Ausrichtung eines Absatzes festlegen. Die erlaubten Werte sind: left (links), center (zentriert), right (rechts) und both (Blocksatz).

(9) Das Element <w:shd> erlaubt eine Hintergrundfarbe, das Attribut w:fill legt diese als RGB-Hexadezimalwert fest. Das Attribut w:val erzeugt je nach Wert gepunktete Hintergründe. Beim Wert clear ist diese Eigenschaft ausgeschaltet. Die hier verwendeten Hintergrundfarbeinstellungen lassen sich über das Formular Format -> Rahmen und Schattierung ändern.
 -->
<w:styles>
  <w:versionOfBuiltInStylenames w:val="4"/>
  <w:latentStyles w:defLockedState="off" w:latentStyleCount="156"/>

  <!--Standardabsatz: 10,5 pt, für times new roman herabgesetzt auf 10 pt-->
  <w:style w:type="paragraph" w:default="on" w:styleId="epi-standard">
    <w:name w:val="Normal"/>
    <w:aliases w:val="A1"/>
    <wx:uiName wx:val="epi-standard"/>
    <w:rPr>
      <w:rFonts w:ascii="Times New Roman" w:h-ansi="Times New Roman"/>
      <wx:font wx:val="Times New Roman"/>
      <!--<w:sz w:val="21"/>-->
      <w:sz w:val="20"/>
      <w:lang w:val="DE" w:fareast="DE" w:bidi="AR-SA"/>
      <w:kern w:val="20"/>
    </w:rPr>
    <w:pPr>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact"/>
      <w:jc w:val="both"/>
    </w:pPr>
  </w:style>

  <w:style w:type="paragraph" w:default="on" w:styleId="epi-normal-1">
    <w:name w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-normal-1"/>
    <w:rPr>
      <w:rFonts w:ascii="Times New Roman" w:h-ansi="Times New Roman"/>
      <wx:font wx:val="Times New Roman"/>
      <!--<w:sz w:val="21"/>-->
      <w:sz w:val="20"/>
      <w:lang w:val="DE" w:fareast="DE" w:bidi="AR-SA"/>
      <w:kern w:val="20"/>
    </w:rPr>
    <w:pPr>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact"/>
      <w:jc w:val="both"/>
    </w:pPr>
  </w:style>

  <!--Notizfeld-->
  <w:style w:type="paragraph" w:styleId="epi-notiz">
    <w:name w:val="epi-notiz"/>
    <w:basedOn w:val="epi-standard"/>
    <wx:uiName wx:val="epi-notiz"/>
    <w:pPr>
      <w:pStyle w:val="epi-notiz"/>
      <w:spacing w:line="220" w:line-rule="exact"/>
      <w:jc w:val="left"/>
    </w:pPr>
    <w:rPr>
      <w:rFonts w:ascii="Courier New" w:h-ansi="Courier New"/>
      <wx:font wx:val="Courier New"/>
    </w:rPr>
  </w:style>

  <!--Kopfzeile-->
  <w:style w:type="paragraph" w:styleId="epi-kopfzeile">
    <w:name w:val="epi-kopfzeile"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-kopfzeile"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact"/>
      <w:jc w:val="left"/>
<!--      <w:tabs>
        <w:tab w:val="center" w:pos="3686"/>
        <w:tab w:val="right" w:pos="8110"/>
      </w:tabs>-->
      <w:outlineLvl w:val="1"/>
      <w:keepNext w:val="on"/>
    </w:pPr>
    <w:rPr><w:kern w:val="20"/></w:rPr>
  </w:style>

  <!--Kopfzeile: Abstand zum Balken oben-->
  <w:style w:type="paragraph" w:styleId="epi-kopfzeile-balken-oben">
    <w:name w:val="epi-kopfzeile-balken-oben"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-kopfzeile-balken-oben"/>
      <w:widowControl w:val="on"/>
      <w:pBdr>
        <w:top w:val="single" w:sz="6" wx:bdrwidth="14" w:space="1" w:color="auto"/>
      </w:pBdr>
      <w:spacing w:line="100" w:line-rule="exact"/>
      <w:keepNext w:val="on"/>
    </w:pPr>
  </w:style>

  <!--Kopfzeile: Abstand zum Balken unten-->
  <w:style w:type="paragraph" w:styleId="epi-kopfzeile-balken-unten">
    <w:name w:val="epi-kopfzeile-balken-unten"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-kopfzeile-balken-unten"/>
      <w:widowControl w:val="on"/>
      <w:pBdr>
        <w:bottom w:val="single" w:sz="6" wx:bdrwidth="15" w:space="1" w:color="auto"/>
      </w:pBdr>
      <w:spacing w:line="100" w:line-rule="exact"/>
      <w:keepNext w:val="on"/>
    </w:pPr>
  </w:style>

  <w:style w:type="paragraph" w:styleId="epi-seitenkopf">
    <w:name w:val="epi-seitenkopf"/>
    <wx:uiName wx:val="epi-seitenkopf"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:tabs>
        <w:tab w:val="center" w:pos="4536"/>
        <w:tab w:val="right" w:pos="9072"/>
      </w:tabs>
    </w:pPr>
  </w:style>

  <w:style w:type="paragraph" w:styleId="epi-seitenfuss">
    <w:name w:val="epi-seitenfuss"/>
    <wx:uiName wx:val="epi-seitenfuß"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:link w:val="seitenzahl"/>
    <w:pPr>
      <w:tabs>
        <w:tab w:val="center" w:pos="4536"/>
        <w:tab w:val="right" w:pos="9072"/>
      </w:tabs>
    </w:pPr>
  </w:style>

  <w:style w:type="character" w:styleId="epi-seitenzahl">
    <w:name w:val="epi-seitenzahl"/>
<!--    <w:basedOn w:val="Absatz-Standardschriftart"/>-->
    <w:link w:val="seitenfuss"/>
  </w:style>

  <!--marginalie für die Abbildungsnummern-->
  <w:style w:type="paragraph" w:styleId="epi-marginalie">
    <w:name w:val="epi-marginalie"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-marginalie"/>
    <w:pPr>
      <w:pStyle w:val="epi-marginalie"/>
      <w:spacing w:line="220" w:line-rule="exact"/>
      <w:jc w:val="left"/>
      <w:framePr w:w="1190" w:hspace="200" w:vspace="850" w:wrap="not-beside" w:vanchor="text" w:hanchor="page" w:x-align="outside" w:y="1"/>
    </w:pPr>
    <w:rPr><w:kern w:val="20"/></w:rPr>
  </w:style>

  <!--Beschreibung-->
  <w:style w:type="paragraph" w:styleId="epi-beschreibung">
    <w:name w:val="epi-beschreibung"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-beschreibung"/>
    <w:pPr>
      <w:pStyle w:val="epi-beschreibung"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact"/>
      <w:jc w:val="both"/>
    </w:pPr>
    <w:rPr>
      <wx:font wx:val="Times New Roman"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

    <!--Wappen-->
  <w:style w:type="paragraph" w:styleId="epi-wappen">
    <w:name w:val="epi-wappen"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-wappen"/>
    <w:pPr>
      <w:pStyle w:val="epi-wappen"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact"/>
      <w:jc w:val="left"/>
    </w:pPr>
    <w:rPr><w:kern w:val="20"/></w:rPr>
  </w:style>

  <!--Verse-->
  <w:style w:type="paragraph" w:styleId="epi-verse">
    <w:name w:val="epi-verse"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-verse"/>
    <w:pPr>
      <w:pStyle w:val="epi-verse"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact"/>
      <w:jc w:val="left"/>
    </w:pPr>
    <w:rPr><w:kern w:val="20"/></w:rPr>
  </w:style>

    <!--Gliederung-1-->
  <w:style w:type="paragraph" w:styleId="epi-gliederung-1">
    <w:name w:val="epi-gliederung-1"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-gliederung-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-gliederung-1"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact"  w:after="60"/>
      <w:jc w:val="left"/>
      <w:ind w:left="709" w:hanging="709"/>
      <w:i/>
    </w:pPr>
    <w:rPr>
      <w:i/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

      <!-- Gliederung-2 -->
  <w:style w:type="paragraph" w:styleId="epi-gliederung-2">
    <w:name w:val="epi-gliederung-2"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-gliederung-2"/>
    <w:pPr>
      <w:pStyle w:val="epi-gliederung-2"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact" w:after="60"/>
      <w:jc w:val="left"/>
      <w:ind w:left="709"/>
    </w:pPr>
    <w:rPr>
      <w:i/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <!-- Gliederung-3 ==>> noch identisch mit gliederung 2 -->
  <w:style w:type="paragraph" w:styleId="epi-gliederung-3">
    <w:name w:val="epi-gliederung-3"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-gliederung-3"/>
    <w:pPr>
      <w:pStyle w:val="epi-gliederung-3"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact" w:after="60"/>
      <w:jc w:val="left"/>
      <w:ind w:left="709"/>
    </w:pPr>
    <w:rPr>
      <w:i/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <!-- einzüge mit w:ind w:val="left|right|before|after"  -->

  <!--Transkription-->
  <w:style w:type="paragraph" w:styleId="epi-transkription">
    <w:name w:val="epi-transkription"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-transkription"/>
    <w:pPr>
      <w:pStyle w:val="epi-transkription"/>
      <w:suppressAutoHyphens/>
      <w:widowControl/>
      <w:spacing w:line="260" w:line-rule="exact"/>
      <w:jc w:val="left"/>
    </w:pPr>
    <w:rPr><w:kern w:val="20"/></w:rPr>
  </w:style>

<!--Pentameterzeile-->
  <w:style w:type="paragraph" w:styleId="epi-pentameter">
    <w:name w:val="epi-pentameter"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-pentameter"/>
    <w:pPr>
      <w:pStyle w:val="epi-pentameter"/>
      <w:suppressAutoHyphens/>
      <w:widowControl/>
      <w:spacing w:line="260" w:line-rule="exact"/>
      <w:ind w:left="284"/>
      <w:jc w:val="left"/>
    </w:pPr>
    <w:rPr><w:kern w:val="20"/></w:rPr>
  </w:style>


  <!-- absätze in 9 pt basieren auf epi-normal-2; die schriftart wird von epi-normal-1 vererbt -->
  <w:style w:type="paragraph" w:styleId="epi-normal-2">
    <w:name w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-normal-2"/>
    <w:pPr>
      <w:pStyle w:val="epi-normal-2"/>
      <w:spacing w:line="240" w:line-rule="auto"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="18"/>
    </w:rPr>
  </w:style>


 <!--Ubersetzung nach allen Inschriften-->
  <w:style w:type="paragraph" w:styleId="epi-uebersetzung1">
    <w:name w:val="epi-uebersetzung1"/>
    <wx:uiName wx:val="epi-übersetzung1"/>
    <w:basedOn w:val="epi-normal-2"/>
    <w:pPr>
      <w:pStyle w:val="epi-uebersetzung"/>
      <w:jc w:val="both"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="18"/>
    </w:rPr>
  </w:style>

 <!--Ubersetzung am Ende jedes Inschriftenteils-->
  <w:style w:type="paragraph" w:styleId="epi-uebersetzung2">
    <w:name w:val="epi-uebersetzung2"/>
    <wx:uiName wx:val="epi-übersetzung2"/>
    <w:basedOn w:val="epi-normal-2"/>
    <w:pPr>
      <w:pStyle w:val="epi-uebersetzung2"/>
        <w:spacing w:before="120" w:after="240"/><w:ind w:left="323"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="18"/>
    </w:rPr>
  </w:style>


  <!--Fussnotenabsatz-->
  <!-- gilt für die Fußnotn in den Artikeln und in der Einleitung -->
  <w:style w:type="paragraph" w:styleId="epi-apparat">
    <w:name w:val="epi-apparat"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-apparat"/>
    <w:pPr>
      <w:pStyle w:val="epi-apparat"/>
      <w:widowControl w:val="off"/>
      <w:spacing w:line="220" w:line-rule="exact"/>
      <w:jc w:val="both"/>
      <!-- einzug und tab sind für die fußnoten der einleitung erforderlich -->
      <w:ind w:left="284" w:hanging="284"/>
      <w:tabs>
        <w:tab w:val="left" w:pos="284"/>
      </w:tabs>
    </w:pPr>
  </w:style>


  <!--Nachweisfeld-->
  <w:style w:type="paragraph" w:styleId="epi-nachweis">
    <w:name w:val="epi-nachweis"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-nachweis"/>
    <w:pPr>
      <w:pStyle w:val="epi-nachweis"/>
      <w:spacing w:line="200" w:line-rule="exact"/>
      <w:jc w:val="both"/>
    </w:pPr>
    <w:rPr><w:kern w:val="20"/></w:rPr>
  </w:style>

  <!--Leerzeile 1pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-1pt">
    <w:name w:val="epi-leerzeile-1pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-1pt"/>
    <w:pPr>
      <w:spacing w:line="20" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 2pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-2pt">
    <w:name w:val="epi-leerzeile-2pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-2pt"/>
    <w:pPr>
      <w:spacing w:line="40" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 3pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-3pt">
    <w:name w:val="epi-leerzeile-3pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-3pt"/>
    <w:pPr>
      <w:spacing w:line="60" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 4pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-4pt">
    <w:name w:val="epi-leerzeile-4pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-4pt"/>
    <w:pPr>
      <w:spacing w:line="80" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 5pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-5pt">
    <w:name w:val="epi-leerzeile-5pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-5pt"/>
    <w:pPr>
      <w:spacing w:line="100" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 6pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-6pt">
    <w:name w:val="epi-leerzeile-6pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-6pt"/>
    <w:pPr>
      <w:spacing w:line="120" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 7pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-7pt">
    <w:name w:val="epi-leerzeile-7pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-7pt"/>
    <w:pPr>
      <w:spacing w:line="140" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 8pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-8pt">
    <w:name w:val="epi-leerzeile-8pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-8pt"/>
    <w:pPr>
      <w:spacing w:line="160" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 9pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-9pt">
    <w:name w:val="epi-leerzeile-9pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-9pt"/>
    <w:pPr>
      <w:spacing w:line="180" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 10pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-10pt">
    <w:name w:val="epi-leerzeile-10pt"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-leerzeile-10pt"/>
    <w:pPr>
      <w:spacing w:line="200" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 10.5pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-10.5pt">
    <w:name w:val="epi-leerzeile-10.5pt"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-leerzeile-10.5pt"/>
    <w:pPr>
      <w:spacing w:line="210" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 11pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-11pt">
    <w:name w:val="epi-leerzeile-11pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-11pt"/>
    <w:pPr>
      <w:spacing w:line="220" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 12pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-12pt">
    <w:name w:val="epi-leerzeile-12pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-12pt"/>
    <w:pPr>
      <w:spacing w:line="240" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 13pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-13pt">
    <w:name w:val="epi-leerzeile-13pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-13pt"/>
    <w:pPr>
      <w:spacing w:line="260" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 14pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-14pt">
    <w:name w:val="epi-leerzeile-14pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-14pt"/>
    <w:pPr>
      <w:spacing w:line="280" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 15pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-15pt">
    <w:name w:val="epi-leerzeile-15pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-15pt"/>
    <w:pPr>
      <w:spacing w:line="300" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 16pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-16pt">
    <w:name w:val="epi-leerzeile-16pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-16pt"/>
    <w:pPr>
      <w:spacing w:line="320" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 17pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-17pt">
    <w:name w:val="epi-leerzeile-17pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-17pt"/>
    <w:pPr>
      <w:spacing w:line="340" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 18pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-18pt">
    <w:name w:val="epi-leerzeile-18pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-18pt"/>
    <w:pPr>
      <w:spacing w:line="360" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 19pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-19pt">
    <w:name w:val="epi-leerzeile-19pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-19pt"/>
    <w:pPr>
      <w:spacing w:line="380" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 20pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-20pt">
    <w:name w:val="epi-leerzeile-20pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-20pt"/>
    <w:pPr>
      <w:spacing w:line="400" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile 21pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-21pt">
    <w:name w:val="epi-leerzeile-21pt"/>
    <w:basedOn w:val="epi-normal-2"/>
    <wx:uiName wx:val="epi-leerzeile-21pt"/>
    <w:pPr>
      <w:spacing w:line="420" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!--Leerzeile am Artikelende 22pt-->
  <w:style w:type="paragraph" w:styleId="epi-leerzeile-22pt">
    <w:name w:val="epi-leerzeile-22pt"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-leerzeile-22pt"/>
    <w:pPr>
      <w:spacing w:line="440" w:line-rule="exact"/>
    </w:pPr>
  </w:style>

  <!-- formate der titelseiten -->
  <!-- haupttitel -->
  <w:style w:type="paragraph" w:styleId="epi-titelei-1">
    <w:name w:val="epi-titelei-1"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-titelei-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-titelei-1"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="480" w:line-rule="exact" w:after="960" />
      <w:jc w:val="center"/>
      <w:keepNext w:val="on"/>
      <w:pageBreakBefore/>
    </w:pPr>
    <w:rPr>
      <w:smallCaps/>
      <w:sz w:val="48"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <!-- untertitel -->
  <w:style w:type="paragraph" w:styleId="epi-titelei-2">
    <w:name w:val="epi-titelei-2"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-titelei-2"/>
    <w:pPr>
      <w:pStyle w:val="epi-titelei-2"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="480" w:line-rule="exact"/>
      <w:spacing w:val="40"/>
      <w:jc w:val="center"/>
      <w:keepNext w:val="on"/>
    </w:pPr>
    <w:rPr>
      <w:caps/>
      <w:sz w:val="24"/>
      <w:spacing w:val="10"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <!-- unterreihe -->
  <w:style w:type="paragraph" w:styleId="epi-titelei-3">
    <w:name w:val="epi-titelei-3"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-titelei-3"/>
    <w:pPr>
      <w:pStyle w:val="epi-titelei-3"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="200" w:line-rule="exact" w:after="200"/>
      <w:jc w:val="center"/>
      <w:keepNext w:val="on"/>
    </w:pPr>
    <w:rPr>
      <w:caps/>
      <w:sz w:val="20"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <!-- bandtitel auf der reihenseite -->
  <w:style w:type="paragraph" w:styleId="epi-titelei-4">
    <w:name w:val="epi-titelei-4"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-titelei-4"/>
    <w:pPr>
      <w:pStyle w:val="epi-titelei-4"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="240" w:line-rule="exact" w:after="220"/>
      <w:jc w:val="center"/>
      <w:keepNext w:val="on"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="24"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <!-- impressum - förderer -->
  <w:style w:type="paragraph" w:styleId="epi-impressum-1">
    <w:name w:val="epi-impressum-1"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-impressum-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-impressum-1"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact" w:after="220"/>
      <w:jc w:val="center"/>
      <w:keepNext w:val="on"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="20"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <!-- impressum - sonstiges -->
  <w:style w:type="paragraph" w:styleId="epi-impressum-2">
    <w:name w:val="epi-impressum-2"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-impressum-2"/>
    <w:pPr>
      <w:pStyle w:val="epi-impressum-2"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact"/>
      <w:jc w:val="center"/>
      <w:keepNext w:val="on"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="20"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>


  <!-- überschriften -->
  <w:style w:type="paragraph" w:styleId="epi-ueberschrift-1">
    <w:name w:val="epi-ueberschrift-1"/>
    <wx:uiName wx:val="epi-überschrift-1"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-ueberschrift-1"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="240" w:line-rule="exact" w:after="220" w:before="480"/>
      <w:jc w:val="center"/>
      <w:keepNext w:val="on"/>
      <w:outlineLvl w:val="0"/>
    </w:pPr>
    <w:rPr>
      <w:caps/>
      <w:sz w:val="24"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <w:style w:type="paragraph" w:styleId="epi-ueberschrift-2">
    <w:name w:val="epi-ueberschrift-2"/>
    <wx:uiName wx:val="epi-überschrift-2"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-ueberschrift-2"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="240" w:line-rule="exact" w:after="220" w:before="220"/>
      <w:jc w:val="center"/>
      <w:keepNext w:val="on"/>
      <w:outlineLvl w:val="1"/>
    </w:pPr>
    <w:rPr>
      <w:smallCaps/>
      <w:sz w:val="20"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <w:style w:type="paragraph" w:styleId="epi-ueberschrift-3">
    <w:name w:val="epi-ueberschrift-3"/>
    <wx:uiName wx:val="epi-überschrift-3"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-ueberschrift-3"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact" w:after="200" w:before="200"/>
      <w:jc w:val="left"/>
      <w:keepNext w:val="on"/>
      <w:outlineLvl w:val="2"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="20"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <w:style w:type="paragraph" w:styleId="epi-ueberschrift-3z">
    <w:name w:val="epi-ueberschrift-3z"/>
    <wx:uiName wx:val="epi-überschrift-3z"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact" w:after="200" w:before="200"/>
      <w:jc w:val="center"/>
      <w:keepNext w:val="on"/>
      <w:outlineLvl w:val="2"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="20"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <w:style w:type="paragraph" w:styleId="epi-ueberschrift-4">
    <w:name w:val="epi-ueberschrift-4"/>
    <wx:uiName wx:val="epi-überschrift-4"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-ueberschrift-4"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact" w:after="200" w:before="200"/>
      <w:jc w:val="left"/>
      <w:keepNext w:val="on"/>
      <w:outlineLvl w:val="3"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="20"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <w:style w:type="paragraph" w:styleId="epi-ueberschrift-5">
    <w:name w:val="epi-ueberschrift-5"/>
    <wx:uiName wx:val="epi-überschrift-5"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-ueberschrift-5"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact" w:after="160" w:before="200"/>
      <w:jc w:val="left"/>
      <w:keepNext w:val="on"/>
      <w:outlineLvl w:val="4"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="20"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <w:style w:type="paragraph" w:styleId="epi-ueberschrift-6">
    <w:name w:val="epi-ueberschrift-6"/>
    <wx:uiName wx:val="epi-überschrift-6"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-ueberschrift-6"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact" w:after="160" w:before="160"/>
      <w:jc w:val="left"/>
      <w:keepNext w:val="on"/>
      <w:outlineLvl w:val="5"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="20"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <w:style w:type="paragraph" w:styleId="epi-ueberschrift-7">
    <w:name w:val="epi-ueberschrift-7"/>
    <wx:uiName wx:val="epi-überschrift-7"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-ueberschrift-7"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact" w:after="120" w:before="160"/>
      <w:jc w:val="left"/>
      <w:keepNext w:val="on"/>
      <w:outlineLvl w:val="6"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="20"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <w:style w:type="paragraph" w:styleId="epi-ueberschrift-8">
    <w:name w:val="epi-ueberschrift-8"/>
    <wx:uiName wx:val="epi-überschrift-8"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:pStyle w:val="epi-ueberschrift-8"/>
      <w:widowControl w:val="on"/>
      <w:spacing w:line="220" w:line-rule="exact" w:after="120" w:before="120"/>
      <w:jc w:val="left"/>
      <w:keepNext w:val="on"/>
      <w:outlineLvl w:val="7"/>
       <w:ind w:first-line="240"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="20"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <!-- lemmata in den registern -->
  <w:style w:type="paragraph" w:styleId="epi-register-eintrag">
    <w:name w:val="epi-register-eintrag"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-register-eintrag"/>
    <w:pPr>
      <w:pStyle w:val="epi-register-eintrag"/>
      <w:widowControl w:val="on"/>
      <w:jc w:val="left"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="20"/>
      <w:kern w:val="20"/>
    </w:rPr>
  </w:style>

  <!-- unterschriften zu den marken -->
  <w:style w:type="paragraph" w:styleId="epi-marken">
    <w:name w:val="epi-marken"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-marken"/>
    <w:pPr>
      <w:jc w:val="left"/>
      <w:spacing w:before="50"/>
      <w:ind w:left="469" w:right="764" w:hanging="469"/>
    </w:pPr>
    <w:rPr>
      <w:sz w:val="18"/>
    </w:rPr>
  </w:style>

  <!--Zeilennummern-->
  <w:style w:type="paragraph" w:styleId="epi-zeilennummer">
    <w:name w:val="epi-zeilennummer"/>
    <wx:uiName wx:val="epi-zeilennummer"/>
    <w:basedOn w:val="epi-normal-2"/>
    <w:pPr>
      <w:jc w:val="right"/>
      <w:spacing w:line="260" w:line-rule="exact"/>
    </w:pPr>
    <w:rPr>
      <w:i/>
      <w:sz w:val="16"/>
    </w:rPr>
  </w:style>

    <!--Tabellenabsätze für Liste der DI Bände und Abkürzungen-->
  <w:style w:type="paragraph" w:styleId="epi-tabellenzeile">
    <w:name w:val="epi-tabellenzeile"/>
    <wx:uiName wx:val="epi-tabellenzeile"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:pPr>
      <w:jc w:val="left"/>
      <w:spacing w:line="200" w:line-rule="exact" w:after="80"/>
      <w:keepLines w:val="on"/>
    </w:pPr>
    <w:rPr><w:kern w:val="20"/></w:rPr>
  </w:style>


  <!-- inhaltsverzeichnis -->
  <w:style w:type="paragraph" w:styleId="epi-inhalt-1">
    <w:name w:val="epi-inhalt-1"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-inhalt-1"/>
    <w:pPr>
      <w:tabs>
        <!-- der erste tab gibt an wie weit die punktierte Linie reichen soll,
                     der zweite richtet die seitennumer rechtsbündig am rand des satzspeigels aus -->
        <w:tab w:val="left" w:leader="dot"  w:pos="7750"/>
        <w:tab w:val="right" w:pos="8110"/>
      </w:tabs>
    </w:pPr>
    <w:rPr><w:kern w:val="20"/></w:rPr>
  </w:style>


  <w:style w:type="paragraph" w:styleId="epi-inhalt-2">
    <w:name w:val="epi-inhalt-2"/>
    <w:basedOn w:val="epi-normal-1"/>
    <wx:uiName wx:val="epi-inhalt-2"/>
    <w:pPr>
      <w:tabs>
        <!-- der erste tab gibt die Position des titels an, der zweite wie weit die punktierte Linie reichen soll,
                     der dritte richtet die seitennumer rechtsbündig am rand des satzspeigels aus -->
        <w:tab w:val="left" w:pos="850"/>
        <w:tab w:val="left" w:leader="dot"  w:pos="7750"/>
        <w:tab w:val="right" w:pos="8110"/>
      </w:tabs>
      <w:ind w:left="850" w:hanging="570" w:right="570"/>
    </w:pPr>
    <w:rPr><w:kern w:val="20"/></w:rPr>
  </w:style>
  <!-- ende inhaltsverzeichnis -->


  <!-- inzeilige formate -->

  <!--Fussnotenzeichen-->

  <w:style w:type="character" w:styleId="epi-fussnotenzeichen">
    <w:name w:val="epi-fussnotenzeichen"/>
    <wx:uiName wx:val="epi-fußnotenzeichen"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:semiHidden/> <!-- semiHidden specifies that the style should be hidden from the main user interface when the document is loaded -->
    <w:rPr>
      <w:vertAlign w:val="superscript"/>
    </w:rPr>
  </w:style>

  <w:style w:type="character" w:styleId="epi-folium">
    <w:name w:val="epi-folium"/>
    <wx:uiName wx:val="epi-folium"/>
    <w:basedOn w:val="epi-normal-1"/>
    <w:semiHidden/> <!-- semiHidden specifies that the style should be hidden from the main user interface when the document is loaded -->
    <w:rPr>
      <w:vertAlign w:val="superscript"/>
    </w:rPr>
  </w:style>


  <!-- versionsnummer bei inschrift-bezeichnern -->
  <w:style w:type="character" w:styleId="epi-version">
    <w:name w:val="epi-version"/>
    <wx:uiName wx:val="epi-version"/>
    <w:rPr>
      <w:vertAlign w:val="superscript"/>
      <w:u w:val="single" w:color="black"/>
    </w:rPr>
  </w:style>

  <!--Kursivierung als Zitat-->
  <w:style w:type="character" w:styleId="epi-zitat">
    <w:name w:val="epi-zitat"/>
    <wx:uiName wx:val="epi-zitat"/>
    <w:rPr>
      <w:i/>
    </w:rPr>
  </w:style>

  <!--Chronogramme-->
  <w:style w:type="character" w:styleId="epi-chronogramm">
    <w:name w:val="epi-chronogramm"/>
    <wx:uiName wx:val="epi-chronogramm"/>
    <w:rPr>
      <w:b/>
    </w:rPr>
  </w:style>

  <!--Initialen-->
  <w:style w:type="character" w:styleId="epi-initialen">
    <w:name w:val="epi-initialen"/>
    <wx:uiName wx:val="epi-initialen"/>
    <w:rPr>
      <w:sz w:val="28"/>
    </w:rPr>
  </w:style>

  <!--Versalien-->
  <w:style w:type="character" w:styleId="epi-versalien">
    <w:name w:val="epi-versalien"/>
    <wx:uiName wx:val="epi-versalien"/>
    <w:rPr>
      <w:sz w:val="28"/>
    </w:rPr>
  </w:style>

  <!--hochgestellte Buchstaben-->
  <w:style w:type="character" w:styleId="epi-hochgestellt">
    <w:name w:val="epi-hochgestellt"/>
    <wx:uiName wx:val="epi-hochgestellt"/>
    <w:rPr>
       <w:position w:val="10"/>
        <w:sz w:val="16"/>
    </w:rPr>
  </w:style>

  <!--Kapitälchen-->
  <w:style w:type="character" w:styleId="epi-kapitaelchen">
    <w:name w:val="epi-kapitaelchen"/>
    <wx:uiName wx:val="epi-kapitaelchen"/>
    <w:rPr>
      <w:smallCaps/>
    </w:rPr>
  </w:style>

  <!--Ligaturen-->
  <w:style w:type="character" w:styleId="epi-ligatur">
    <w:name w:val="epi-ligatur"/>
    <wx:uiName wx:val="epi-ligatur"/>
    <w:rPr>
      <w:u w:val="single" w:color="black"/>
    </w:rPr>
  </w:style>

  <!--gesperrter Text-->
  <w:style w:type="character" w:styleId="epi-gesperrt">
    <w:name w:val="epi-gesperrt"/>
    <wx:uiName wx:val="epi-gesperrt"/>
    <w:rPr>
      <w:spacing w:val="40"/>
    </w:rPr>
  </w:style>

  <w:style w:type="character" w:styleId="Hyperlink">
    <w:name w:val="Hyperlink"/>
    <w:rsid w:val="001158DE"/>
    <w:rPr>
      <w:color w:val="000099"/>
      <!--<w:color w:val="0000FF"/>-->
      <!--<w:u w:val="single"/>-->
    </w:rPr>
  </w:style>
</w:styles>
</xsl:template>



</xsl:stylesheet>
