<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
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
  xmlns:msxsl="urn:schemas-microsoft-com:xslt" 
  xmlns:exsl="http://exslt.org/common" 
  xmlns:php="http://php.net/xsl" 
>

<!-- 
    dieses stylesheet stößt die vierte transformationsstufe der di-daten an;
    es ist anzuwenden auf das transformationsergebnis der dritten stufe, 
    die mit di-trans3.xsl in gang gesetzt wurde.
    
    in diesm stylesheet wird der gesamte inschriftenband zur ausgabe als word-dokument angelegt;
    
    die drei vorangegangenen transformationsstufen müssen nacheinander, 
    ausgehend vom export der rohdaten aus epigraf,
    durch die stylesheets 
      di-trans1.xsl
      di trans1.xsl
      di-trans3.xsl 
    ausgelöst worden sein.
  -->
 
  <!-- 
    das ausgabeformat ist office open xml (OOXML), auch als 
    WordML bezeichnet;
    
    die formatierung erfolgt nicht für die package-variante (zip-archiv),
    sondern als single file (word 2003)
    
    der innere code ist für beide varianten derselbe
    
    als einführung und übersicht zu WordML siehe 
    http://officeopenxml.com/WPcontentOverview.php (englisch)
    https://www.data2type.de/xml-xslt-xslfo/wordml/ (deutsch)
  
  -->
  
  <!-- 
  zu jedem abschnitt des bandes gibt es ein eigenes stylesheet im ordner trans-doc:
  titelei                 (preliminaries)
  vorwort                 (prefaces)
  inhaltsverzeichnis      (list_content)
  einleitung              (introduction)
  katalog der inschriften (catalog)
  inschriftenliste        (list_inscriptions)
  abkürzungen             (abbreviations)
  di-bände                (di_volumes)
  register                (indexes)
  zeichnungen             (drawings)
  grundrisse              (maps)
  bildtafeln              (plates)
  
   
  templates die in mehreren stylesheets aufgerufen werden können,
  befinden sich in di-trans-doc-commons.xsl,
  
  die ausgabeoptionen für alle transformationsstufen enthält commons/di-switch.xsl,
  
  stilanweisungen sind in di-doc-styles.xsl notiert
   
  -->  

<xsl:import href="commons/di-switch.xsl"/> <!-- "Schalter" zum Aus- und Einblenden bestimmter Abschnitte des Layouts -->
  <xsl:import href="trans-doc/di-doc-styles.xsl"/>
  <xsl:import href="trans-doc/di-doc-fonts.xsl"/>
  <xsl:import href="trans-doc/di-doc-commons.xsl"/>

  <xsl:import href="trans-doc/di-doc-preliminaries.xsl"/>  
  <xsl:import href="trans-doc/di-doc-prefaces.xsl"/> 
  <xsl:import href="trans-doc/di-doc-list_content.xsl"/>
  <xsl:import href="trans-doc/di-doc-introduction.xsl"/>
  <xsl:import href="trans-doc/di-doc-catalog.xsl"/>
  <xsl:import href="trans-doc/di-doc-abbreviations.xsl"/>
  <xsl:import href="trans-doc/di-doc-blazons.xsl"/>
  
  <xsl:import href="trans-doc/di-doc-list_inscriptions.xsl"/>

  <xsl:import href="trans-doc/di-doc-indexes.xsl"/>
  
  <xsl:import href="trans-doc/di-doc-literature.xsl"/>
  <xsl:import href="trans-doc/di-doc-di_volumes.xsl"/>
  <xsl:import href="trans-doc/di-doc-plates.xsl"/>
  <xsl:import href="trans-doc/di-doc-drawings.xsl"/>
  <xsl:import href="trans-doc/di-doc-maps.xsl"/>


  <xsl:import href="trans-doc/di-doc-brands.xsl"/>
  <xsl:import href="trans-doc/di-doc-heraldry.xsl"/>
 

<xsl:output method="xml" version="1.0" indent="yes" encoding="UTF-8"/>

<!-- wurzelknoten ansteuern -->
<xsl:template match="/">

  <!--Verarbeitungsanweisung zum Erzeugen von <?mso-application progid="Word.Document"?> -->
<xsl:processing-instruction name="mso-application">
 <xsl:text>progid="Word.Document"</xsl:text>
</xsl:processing-instruction>

<!-- document anlegen -->
<w:wordDocument xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w10="urn:schemas-microsoft-com:office:word" xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core" xmlns:aml="http://schemas.microsoft.com/aml/2001/core" xmlns:wx="http://schemas.microsoft.com/office/word/2003/auxHint" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2" xmlns:fo="http://www.w3.org/1999/XSL/Format" w:macrosPresent="no" w:embeddedObjPresent="no" w:ocxPresent="no" xml:space="preserve">
<xsl:call-template name="fonts" />
<xsl:call-template name="styles" />

<!-- eigenschaften dokument -->
<w:docPr>
  <!-- settings für word beim öffnen der datei -->
  <w:view w:val="print"/>
  <w:zoom w:percent="150"/>
  <w:attachedTemplate w:val=""/>
  
  <!-- standard-tabstop -->
  <w:defaultTabStop w:val="708"/>

  <!-- automatische silbentrennung -->
  <!-- die funktion kann für einzelne absätze in w:pPr durch <w:suppressAutoHyphens/> ausgeschaltet werden  -->
    <w:autoHyphenation/>
    <!-- aufeinanderfolgende trennstriche: 1 -->
    <w:consecutiveHyphenLimit w:val="2"/>
    <!-- trennzone 0,5 cm -->
    <w:hyphenationZone w:val="284"/>
    <!-- wörter in grossbuchstaben werden nicht getrennt -->
    <w:doNotHyphenateCaps/>
  <w:characterSpacingControl w:val="DontCompress"/>

  <!-- seiten spiegeln: left wird zu innen, right zu außen -->
  <w:mirrorMargins/> 

  <!-- xml-behavior -->
  <w:validateAgainstSchema/>
  <w:saveInvalidXML w:val="off"/>
  <w:ignoreMixedContent w:val="off"/>
  <w:alwaysShowPlaceholderText w:val="off"/>
  <w:compat/>
  
  <!-- eigenschaften fußnoten -->
  <w:footnotePr>
    <w:footnote w:type="separator">
      <w:p>
        <w:pPr>
          <w:spacing w:line="240" w:line-rule="auto"/>
        </w:pPr>
        <w:r>
          <w:separator/>
        </w:r>
      </w:p>
    </w:footnote>
    <w:footnote w:type="continuation-separator">
      <w:p>
        <w:pPr>
          <w:spacing w:line="240" w:line-rule="auto"/>
        </w:pPr>
        <w:r>
          <w:continuationSeparator/>
        </w:r>
      </w:p>
    </w:footnote>
  </w:footnotePr>
</w:docPr>

<!-- dokumentinhalt -->
  <w:body>
    <!-- der wurzelknoten wird an ein template verwiesen und 
    dadurch auf das wurzelelement (<book>) gelenkt-->
    <xsl:apply-templates/>

<!-- eigenschaften der seiten: 
    die seiteneigenschaften eines dokument-abschnitts werden immer am 
    ende des betreffenden abschnitts eingefügt und gelten bis dorthin, wo sie eingefügt sind;
    -->    
 <w:sectPr>
  <w:hdr w:type="even">
    <w:p>
      <w:pPr>
        <w:pStyle w:val="epi-seitenkopf"/>
      </w:pPr>
    </w:p>
  </w:hdr>
  <w:hdr w:type="odd">
    <w:p>
      <w:pPr>
        <w:pStyle w:val="epi-normal-1"/>
      </w:pPr>
    </w:p>
  </w:hdr>
  <w:ftr w:type="even">
    <w:p>
      <w:pPr>
        <w:pStyle w:val="epi-seitenfuss"/>
      </w:pPr>
      <w:fldSimple w:instr=" PAGE \* MERGEFORMAT ">
        <w:r wsp:rsidR="000C002F">
          <w:rPr>
            <w:noProof/>
          </w:rPr>
          <w:t>2</w:t>
        </w:r>
      </w:fldSimple>
    </w:p>
    <w:p>
      <w:pPr>
        <w:pStyle w:val="epi-seitenfuss"/>
      </w:pPr>
    </w:p>
  </w:ftr>
  <w:ftr w:type="odd">
    <w:p>
      <w:pPr>
        <w:pStyle w:val="epi-seitenfuss"/>
        <w:jc w:val="right"/>
      </w:pPr>
      <w:fldSimple w:instr=" PAGE \* MERGEFORMAT ">
        <w:r wsp:rsidR="00544CA7">
          <w:rPr>
            <w:noProof/>
          </w:rPr>
          <w:t>22</w:t>
        </w:r>
      </w:fldSimple>
    </w:p>
    <w:p>
      <w:pPr>
        <w:pStyle w:val="epi-seitenfuss"/>
      </w:pPr>
    </w:p>
  </w:ftr>
  <w:type w:val="continuous"/>
   <w:pgSz w:w="11906" w:h="16838"/>
      <!-- satzspiegel breite 8107 twips (14,3 cm) , höhe 13124 twips (23,15 cm)
        22,15 cm = 12559 twips
      1 cm 0 567 twips
      -->
  <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800" w:gutter="0"/>
      <xsl:if test="$sw_register=1 and .//indices[not(following-sibling::*)]"><w:cols w:num="3" w:space="708"/></xsl:if>
      <xsl:if test="$sw_register!=1"><w:cols w:space="708"/></xsl:if>
  <w:docGrid w:line-pitch="360"/>
</w:sectPr>

  
</w:body>
</w:wordDocument>
</xsl:template>

<!--wurzelelement aufrufen und an templates verweisen-->
<xsl:template match="book"><xsl:apply-templates/></xsl:template>

<xsl:template match="options"></xsl:template>

</xsl:stylesheet>
