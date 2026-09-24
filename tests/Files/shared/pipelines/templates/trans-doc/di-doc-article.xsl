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
    
    <xsl:import href="../commons/di-switch.xsl"/> <!-- "Schalter" zum Aus- und Einblenden bestimmter Abschnitte des Layouts -->
    <xsl:import href="di-doc-styles.xsl"/>
    <xsl:import href="di-doc-commons.xsl"/>
    <xsl:import href="di-doc-ligatures.xsl"/>
    <xsl:import href="di-doc-tools.xsl"/>
    <xsl:import href="di-doc-links.xsl"/>    
    
    <!--
    in diesem stylesheet wird der artikel wird gemäß der vorgefundenen reihenfolge seiner abschnitte zusammengestellt
    -->
    
<!--aufbau des artikels / composition of article-->
  <xsl:template match="article">
    <xsl:comment>artikelanfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <wx:sub-section> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <!-- vor jedem außer dem ersten artikel wird eine hohe leerzeile eingefügt -->
      <xsl:if test="preceding-sibling::article">
        <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-22pt"/></w:pPr></w:p>
      </xsl:if>
      <!-- elemente des artikels ansteuern -->
      <xsl:for-each select="*">
        <!-- elemente an entsprechende templates verweisen -->
        <xsl:apply-templates select="."/>
      </xsl:for-each>
      <xsl:comment>artikelende</xsl:comment>
       <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </wx:sub-section> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <!-- kopfzeile und signatur -->
  <xsl:template match="headline">
    <!-- 
        die position des mittleren tabulators (standortangabe) ungefähr in der mitte 
        zwischen dem ende der laufnummer und dem anfang der datierung ermitteln:
        
        die kopzeile hat die breite von 85 x den buchstaben n (=mittelbreiter buchstabe)
      die kopfzeile ist 8107 twips breit, das geteilt durch 85 ergibt pro n 95,3 twips ;
      um die für den standort verfügbare länge zu berechnen, muss man von der breite der kopfzeile 
      die länge des datierungsstrings und die länge der artikelnummer mit sigle abziehen;
      um die position des mittleren tabulators zu errechnen, muss man die verfügbare standortbreite halbieren
      und dazu die länge des nummerierungsstrings addieren;
      die so ermittelte zahl muss mit den twips je n (=95) multipliziert werden
    -->
    <xsl:param name="p_usedstring"><xsl:value-of select="string-length(dating/date_value) + string-length(articlenumber) + string-length(trad_sigle)"/></xsl:param>
    <xsl:param name="p_nrstring"><xsl:value-of select="string-length(articlenumber) + string-length(trad_sigle)"/></xsl:param>
    <xsl:param name="p_diffstring"><xsl:value-of select="85 - $p_usedstring"/></xsl:param>
    <xsl:param name="p_pos1"><xsl:value-of select="$p_diffstring * 0.5"/></xsl:param>
    <xsl:param name="p_pos2"><xsl:value-of select="$p_pos1 + $p_nrstring"/></xsl:param>
    <xsl:param name="p_pos3"><xsl:value-of select="$p_pos2 * 95"/></xsl:param>

     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<!--
    <parametertest>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <xsl:value-of select="string-length(dating/date_value)"/>-<xsl:value-of select="string-length(articlenumber)"/>-<xsl:value-of select="string-length(trad_sigle)"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:copy-of select="$p_usedstring"/> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:copy-of select="$p_nrstring"/> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:copy-of select="$p_diffstring"/> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:copy-of select="$p_pos1"/> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:copy-of select="$p_pos2"/> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:copy-of select="$p_pos3"/> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </parametertest>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->
    <xsl:comment>kopfzeile anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <!-- oberer balken -->
    <wx:pBdrGroup>
      <wx:borders>
        <wx:top wx:val="solid" wx:bdrwidth="14" wx:space="1" wx:color="auto"/>
      </wx:borders>
      <w:p>
        <w:pPr><w:pStyle w:val="epi-kopfzeile-balken-oben"/></w:pPr>
      </w:p>
    </wx:pBdrGroup>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <!-- zeile anlegen -->
    <w:p>
      <w:pPr>
        <w:pStyle w:val="epi-kopfzeile"/>
          <!-- tabulatoren, in der mitte (zentrierend) und am ende (rechtsbündig), setzen -->
        <w:tabs>
           <w:tab w:val="center" w:pos="3686">
            <xsl:attribute name="w:pos">
              <xsl:value-of select="$p_pos3"/>
            </xsl:attribute>
          </w:tab>
          <w:tab w:val="right" w:pos="8110"/>
        </w:tabs>
      </w:pPr>
      <!-- um artikelnummer und sigle sprungmarke für die 
          links aus den registern und den querverweisen setzten -->
      <!-- anfang: sprungmarke -->
      <aml:annotation w:type="Word.Bookmark.Start">
        <xsl:attribute name="aml:id"><xsl:value-of select="ancestor::article/@id"/></xsl:attribute>
        <xsl:attribute name="w:name"><xsl:value-of select="ancestor::article/@id"/></xsl:attribute>
      </aml:annotation>
      <!-- artikelnummer und sigle einfügen -->
      <w:r><w:t><xsl:value-of select="concat(articlenumber,' ',trad_sigle)"/></w:t></w:r>
      <aml:annotation w:type="Word.Bookmark.End">
        <xsl:attribute name="aml:id"><xsl:value-of select="ancestor::article/@id"/></xsl:attribute>
      </aml:annotation>
      <!-- ende: sprungmarke -->
     
      <!-- standort -->
      <w:r>
        <!-- tabulator setzen -->
        <w:tab/>
        <!-- elemente <standort> ansteuern und auslesen -->
        <xsl:for-each select="locations/location">
            <!-- geschweifte klammen aus standortangaben entfernen -->
          <w:t><xsl:value-of select="translate(.,'{}','')"/><xsl:if test="following-sibling::location"><xsl:text>, </xsl:text></xsl:if></w:t>
        </xsl:for-each>
      </w:r>
      
      <!-- datierung -->
      <w:r>
        <!-- tabulator setzten und element <dating> auslesen -->
        <w:tab/><w:t><xsl:value-of select="dating/date_value"/></w:t>
      </w:r>
    </w:p>
    
    <!-- unterer balken -->
    <wx:pBdrGroup>
      <wx:borders>
        <wx:bottom wx:val="solid" wx:bdrwidth="15" wx:space="1" wx:color="auto"/>
      </wx:borders>
      <w:p>
        <w:pPr><w:pStyle w:val="epi-kopfzeile-balken-unten"/></w:pPr>
      </w:p>
    </wx:pBdrGroup>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>kopfzeile ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <!--Ende Abschnitt 1: Kopfzeile zwischen Balken / header between leaders-->
    
    <!-- signatur -->
    <!-- die signatur wird links unter dem unteren balken der kopfzeile ausgegeben -->
    <xsl:comment>signatur anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <w:p>
      <w:pPr>
        <w:pStyle w:val="epi-leerzeile-10pt"/>
        <w:keepNext w:val="on"/>
      </w:pPr>
      <!--signatur einfuegen, wenn in den ausgabeoptionen angegeben-->
      <xsl:if test="$sw_signatur=1">
        <w:r><w:rPr><w:vertAlign w:val="superscript"/></w:rPr><w:t><xsl:value-of select="signature" /></w:t></w:r>
      </xsl:if>
    </w:p>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>signatur ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>

<!-- beschreibung -->
  <xsl:template match="di_description">
    <xsl:comment>beschreibung anfang</xsl:comment>
    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:apply-templates/>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <!-- leerzeile -->
    <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>beschreibung ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <!-- gliederungsabschnitte 
        
        zur strukturierung umfangreicher artikel können
        gliederungsabschnitte in epigraf als 
        zwischenabsätze zwischen (=flach) oder als 
        container (=hierarchisch) für inschriften verwendet werden.
        sie unterscheiden sich in der darstellung des direkt damit 
        verbundenen textfeldes durch die zuweisung unterschiedlicher 
        formatvorlagen
  -->
  <xsl:template match="di_header1">
    <xsl:comment>gliederung-1 anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <w:p><w:pPr><w:pStyle w:val="epi-gliederung-1"/></w:pPr><xsl:apply-templates/></w:p>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>gliederung-1 ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <xsl:template match="di_header2">
    <xsl:comment>gliederung-2 anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:for-each select="p">
      <w:p><w:pPr><w:pStyle w:val="epi-gliederung-2"/></w:pPr><xsl:apply-templates/></w:p>
    </xsl:for-each>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>gliederung-2 ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <xsl:template match="di_header3">
    <xsl:comment>gliederung-3 anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:for-each select="p">
      <w:p><w:pPr><w:pStyle w:val="epi-gliederung-2"/></w:pPr><xsl:apply-templates/></w:p>
    </xsl:for-each>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>gliederung-3 ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="numbering">
    <!-- bislang nicht vorhanden -->
    <xsl:comment>numbering1 anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:for-each select="p">
      <w:p><w:pPr><w:pStyle w:val="epi-gliederung-2"/></w:pPr><xsl:apply-templates/></w:p>
    </xsl:for-each>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>numbering1 ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  

   <!--abschnitt für verschiedene angaben: Vorlagen, Objekteigenschaften, Schrifthoehen und Schriftarten / various characteristics-->
   <xsl:template match="di_generals">
    <xsl:comment>angaben anfang</xsl:comment>
    <!-- wenn angaben oder verweise auf abbildungsnummern vorhanden sind -->
    <xsl:if test="*[node()]">
    <!-- marginalie am äußeren rand für die abbildungsnummern -->
    <wx:pBdrGroup>
      <wx:apo>
        <wx:width wx:val="849"/>
        <wx:jc wx:val="right"/> <!-- durch die angabe <w:mirrorMargins/> in <w:docPr> in <w:wordDocument> wird daraus 'außen'-->
        <wx:vertFromText wx:val="850"/>
        <wx:horizFromText wx:val="200"/>
      </wx:apo>
      <w:p>
        <w:pPr>
          <w:pStyle w:val="epi-marginalie"/>
          <!--<wx:jc wx:val="inside"/>-->
          <!--<w:framePr w:w="1190" w:hspace="200" w:vspace="850" w:wrap="not-beside" w:vanchor="text" w:hanchor="page" w:x-align="outside" w:y="1"/>-->
        </w:pPr>
        <w:r>
          <!-- wird ausgeblendet wenn keine abbildungen referenziert sind -->
          <xsl:if test="not(plates_list[node()])">
          <w:rPr><w:vanish/></w:rPr>
          </xsl:if>
          <w:t>Abb. <xsl:value-of select="plates_list"/></w:t>
        </w:r>
      </w:p>
    </wx:pBdrGroup>
    <!-- nun die angaben -->
    <xsl:if test="node()">
        <xsl:choose>
          <!-- münchener reihe -->
          <xsl:when test="$sw_modus='projects_bay'">
            <!-- erster absatz -->
              <w:p>
                <w:pPr><w:pStyle w:val="epi-beschreibung"/></w:pPr>
                <!-- bestimmte elemente mit den angaben ansteuern und an templates verweisen -->
                <xsl:for-each select="di_generals_measure|di_generals_fontsize|di_generals_fonttype">
                  <!-- bei mehreren angaben trennzeichen dazwischen setzen -->
                  <xsl:if test="preceding-sibling::di_generals_fontsize or
                    preceding-sibling::di_generals_measure or
                    preceding-sibling::di_generals_fonttype
                    ">
                    <xsl:choose>
                      <xsl:when test="self::di_generals_fontsize"><w:r><w:t><xsl:text>,&#x0020;</xsl:text></w:t></w:r></xsl:when>
                      <xsl:otherwise><w:r><w:t><xsl:text>.&#x00A0;&#x2013;&#x0020;</xsl:text></w:t></w:r></xsl:otherwise>
                    </xsl:choose>
                  </xsl:if>
                  <xsl:apply-templates/>
                </xsl:for-each>
              </w:p>
              <!-- wenn weitere angaben vorhanden, leerzeile einfügen -->
            <xsl:if test="di_generals_measure or di_generals_fontsize or di_generals_fonttype">
              <xsl:if test="di_generals_source or di_generals_addition or di_generals_other">
                <!-- leerzeile-->
                          <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:if>
                </xsl:if>
            <!-- zweiter absatz  -->
              <w:p>
                <w:pPr><w:pStyle w:val="epi-beschreibung"/></w:pPr>
                <!-- die übrigen elemente ansteuern und an tenplates verweisen -->
                <xsl:for-each select="di_generals_source|di_generals_addition|di_generals_other">
                  <xsl:apply-templates/>
                  <!-- trennzeichen setzen: festes leerzeichen, halbgeviertstrich, einfaches leerzeichen -->
                  <xsl:if test="following-sibling::di_generals_source or
                                following-sibling::di_generals_addition or
                                following-sibling::di_generals_other
                                ">
                    <w:r><w:t><xsl:text>&#x00A0;&#x2013;&#x0020;</xsl:text></w:t></w:r>
                  </xsl:if>
                </xsl:for-each>
              </w:p>
        </xsl:when>
          <!-- die anderen reihen: alle angaben in einem absatz -->
        <xsl:otherwise>
          <!-- absatz anlegen -->
              <w:p>
                <w:pPr><w:pStyle w:val="epi-beschreibung"/></w:pPr>
                <!-- die verschiedenen angaben ansteuern und an templates verweisen -->
                <xsl:for-each select="*[not(name()='plates_list')][not(contains(name(),'dio'))][string-length() &gt; 1][not(starts-with(name(),'x'))]">
                  <xsl:apply-templates/>
                  <!-- zwischen den verschiedenen angaben leerzeichen und bindestrich einfügen; 
                    bei angaben die nicht mit einem Punkt enden, wird ein solcher eingefügt -->
                  <xsl:choose>
                    <xsl:when test="following-sibling::*[not(name()='plates_list')][not(contains(name(),'dio'))][string-length() &gt; 1]"><w:r><w:t><xsl:if test="not(ends-with(.,'.'))"><xsl:text>.</xsl:text></xsl:if><xsl:text>&#x00A0;&#x2013;&#x0020;</xsl:text></w:t></w:r></xsl:when>
                    <xsl:when test="not(ends-with(.,'.'))"><w:r><w:t><xsl:text>.</xsl:text></w:t></w:r></xsl:when>
                  </xsl:choose>
                </xsl:for-each>
              </w:p>
        </xsl:otherwise>
        </xsl:choose>
       <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <!-- leerzeile-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:if>
    </xsl:if>
    <xsl:comment>angaben ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <!-- die verschiedenen angaben im einzelnen -->
  <!-- verweise auf inschriften werden durch das template linkSerie zusammengefasst,
       d. h. innerhalb einer serie werden die unmittelbar aufeinander folgenden durch einen spiegelstrich ersetzt -->
  <xsl:template match="di_generals_measure"><w:r><w:t><xsl:value-of select="translate(.,'-','–')"/></w:t></w:r></xsl:template>
    <!-- abmessungen nur provisorische lösung - müsste wie das feld beschreibung formatiert werden
    Bindestriche werden durch bis-Striche (Halbgeviertstriche) ersetzt-->
  <xsl:template match="di_generals_fonttype/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
  <xsl:template match="di_generals_fonttype/links"><xsl:call-template name="linkSerie" /></xsl:template>
    
  <xsl:template match="di_generals_fontsize/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
  <xsl:template match="di_generals_fontsize/links"><xsl:call-template name="linkSerie" /></xsl:template>
    
  <xsl:template match="di_generals_source/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
  <xsl:template match="di_generals_source/links"><xsl:call-template name="linkSerie" /></xsl:template>
    
  <xsl:template match="di_generals_other/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
    
  <xsl:template match="di_generals_addition/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
  <xsl:template match="di_generals_addition/links"><xsl:call-template name="linkSerie" /></xsl:template>
  
  

  <!-- abschnitt übersetzungen -->
  <xsl:template match="translations">
    <xsl:comment>übersetzungen anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <xsl:choose>
          <!-- wenn die übersetzungen gemäß ausgabeoptionen für den artikel nach jeder inschrift auszugeben sind, 
          wird kein eigener abschnitt mit übersetzungen gebildet-->
        <xsl:when test="ancestor::article/outputoptions/outputoption[@norm_iri='di_translations_sectionwise']"></xsl:when>
      <xsl:otherwise>
            <!--1. pruefen, ob Uebersetzungen vorliegen-->
          <xsl:if test="translation">
            <!--2. Template fuer die Uebersetzungen einfuegen-->
            <xsl:for-each select="translation">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-uebersetzung1"/></w:pPr>
                <xsl:apply-templates  select="."/></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
            <!--leerzeile einfuegen-->
            <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          </xsl:if>
      </xsl:otherwise>
      </xsl:choose>
      <xsl:comment>übersetzungen ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <!-- übersetzungen inschriftenweise ausgeben -->
  <xsl:template match="translation" mode="inscriptionwise"><xsl:apply-templates></xsl:apply-templates></xsl:template>
  
  <!-- die einzelne übersetzung -->
  <xsl:template match="translation">
    <xsl:if test="$sw_modus='projects_bay'">
      <xsl:apply-templates select="content"/><w:r><w:t><xsl:text disable-output-escaping="yes">&#x0020;</xsl:text></w:t></w:r>
    </xsl:if>
    <xsl:if test="nr[text()] or version-nr[text()]">
      <!-- münchener bände: erst die übersetzung, dann die nummer der inschrift -->
<!--      <xsl:if test="$sw_modus='projects_bay'">
        <xsl:apply-templates select="content"/><w:r><w:t><xsl:text disable-output-escaping="yes">&#x0020;</xsl:text></w:t></w:r>
      </xsl:if>-->
      <w:r><w:t>(</w:t></w:r>
      <xsl:apply-templates select="nr"/>
      <xsl:apply-templates select="version"/>
      <w:r><w:t>) </w:t></w:r>
    </xsl:if>
    <!-- alle anderen bände: erst die nummer der inschrift, dann die übersetzung -->
    <xsl:if test="not($sw_modus='projects_bay')">
      <xsl:apply-templates select="content"/>
    </xsl:if>
  </xsl:template>
    
  <xsl:template match="translation/nr">
    <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
  </xsl:template>
  
  <xsl:template match="translation/version-nr">
    <xsl:choose>
      <xsl:when test="preceding-sibling::nr"><w:r><w:rPr><w:rStyle w:val="epi-version"/></w:rPr><w:t><xsl:value-of select="."/></w:t></w:r></xsl:when>
      <xsl:otherwise><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  <xsl:template match="translation/content">
    <xsl:apply-templates/>
  </xsl:template>

<!-- abschnitt versmaße -->
  <xsl:template match="di_metres">
    <xsl:comment>versmaße anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <!-- 1. testen ob versangaben vorliegen-->
    <xsl:if test="item">
      <!-- 2. versangaben ansteuern-->
      <xsl:for-each select="item">
        <w:p>
          <w:pPr>
            <xsl:choose>
              <xsl:when test="$sw_modus='projects_bay'"><w:pStyle w:val="epi-apparat"/></xsl:when>
                <xsl:otherwise><w:pStyle w:val="epi-verse"/></xsl:otherwise>
            </xsl:choose>
            </w:pPr>
            <!-- angabe zum versmaß und gegebenfalls verweis auf die betreffende inschrift ausgeben,
            mehrere verweise gegebenfalls zusammenziehen-->
          <w:r><w:t><xsl:apply-templates select="content"/></w:t></w:r><xsl:if test="links/link"><w:r><w:t><xsl:text> (</xsl:text></w:t></w:r><xsl:for-each select="links"><xsl:call-template name="linkSerie" /></xsl:for-each><w:r><w:t><xsl:text>)</xsl:text></w:t></w:r></xsl:if><w:r><w:t><xsl:text>.</xsl:text></w:t></w:r>

        </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </xsl:for-each>
      <!-- 3. Leerzeile einschieben-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:if>
    <xsl:if test="p">
      <xsl:for-each select="p">
        <w:p>
          <w:pPr>
            <xsl:choose>
              <xsl:when test="$sw_modus='projects_bay'"><w:pStyle w:val="epi-apparat"/></xsl:when>
              <xsl:otherwise><w:pStyle w:val="epi-verse"/></xsl:otherwise>
            </xsl:choose>
          </w:pPr>
          <xsl:apply-templates select="."></xsl:apply-templates>
        </w:p>
      </xsl:for-each>
      <!-- 3. Leerzeile einschieben-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      
    </xsl:if>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>versmaße ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>

  <!-- abschnitt zitatquellen 
      (wenn inschriften zitate enhalten, kann hier der nachweis angegeben sein) -->
  <xsl:template match="di_citation_source">
         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>zitatquellen - anfang</xsl:comment>
         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:if test="node()">
      <!--template zur ausgabe und formatierung aufrufen-->
      <w:p><w:pPr><w:pStyle w:val="epi-apparat"/></w:pPr><xsl:apply-templates/></w:p>
      <!--leerzeile einschieben-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
    </xsl:if> 
         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>zitatquellen - ende</xsl:comment>
         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <!-- abschnitt: datum in der inschrift -->
  <xsl:template match="di_date_on_inscription">
         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>datum in der inschrift - anfang</xsl:comment>
         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <!-- testen ob ein inhalt vorhanden ist -->
    <xsl:if test="node()">
      <!--template aufrufen-->
      <w:p><w:pPr><w:pStyle w:val="epi-beschreibung"/></w:pPr>
        <xsl:choose>
          <xsl:when test="starts-with(.,'Datum')"><xsl:apply-templates/></xsl:when>
          <xsl:otherwise><w:r><w:t>Datum: </w:t></w:r><xsl:apply-templates/></xsl:otherwise>
        </xsl:choose>
        <xsl:if test="not(ends-with(.,'.'))"><w:r><w:t>.</w:t></w:r></xsl:if>
      </w:p>
      <!--3. Leerzeile einschieben-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
    </xsl:if> 
         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>datum in der inschrift - ende</xsl:comment>
         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <!-- abschnitt kommentar -->
  <xsl:template match="di_comment">
    <xsl:comment>kommentar anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <!--1. nach Kommentaren testen-->
    <xsl:if test="node()">
      <!--2. Template fuer Kommentare einfuegen-->
      <xsl:apply-templates/>
      <!--3. Leerzeile einschieben-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
    </xsl:if>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>kommentar ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <!-- abschnitt fußnoten -->
  <xsl:template match="footnotes">
    <!-- anzahl der fußnoten ermitteln;
          die zahl wird benötigt, um festzustelien, wie breit die spalte 
          für die fußnotenbuchstaben bzw. -ziffern sein muss,
          d. h. ob für ein- oder zweistellige angaben -->
    <xsl:param name="p_count_ziffern"><xsl:value-of select="count(digit_footnotes/item)"/></xsl:param>
    <xsl:param name="p_count_buchstaben"><xsl:value-of select="count(letter_footnotes/item)"/></xsl:param>
    <xsl:comment>fußnoten anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <!--1. nach Fussnoten testen-->
    <xsl:if test="*/*">
        <!-- buchstabenapparat aufrufen -->
      <xsl:apply-templates select="letter_footnotes">
      <xsl:with-param name="p_count_ziffern"><xsl:value-of select="$p_count_ziffern"/></xsl:with-param>
      <xsl:with-param name="p_count_buchstaben"><xsl:value-of select="$p_count_buchstaben"/></xsl:with-param>
    </xsl:apply-templates>
        <!-- ziffernapparat aufrufen -->
      <xsl:apply-templates select="digit_footnotes">
      <xsl:with-param name="p_count_ziffern"><xsl:value-of select="$p_count_ziffern"/></xsl:with-param>
      <xsl:with-param name="p_count_buchstaben"><xsl:value-of select="$p_count_buchstaben"/></xsl:with-param>
    </xsl:apply-templates>
    <!--2. Leerzeile einschieben-->
    <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
    <xsl:comment>fußnoten ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <!-- unterabschnitt buchstabenfußnoten -->
  <xsl:template match="letter_footnotes">
    <xsl:param name="p_count_ziffern"></xsl:param>
    <xsl:param name="p_count_buchstaben"></xsl:param>
    <xsl:comment>buchstabenapparat anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <!--auf Buchstabenapparat testen-->
    <xsl:if test="item"> 
      <!--fußnoten einfuegen als tabelle-->
      <w:tbl>
        <w:tblPr>
          <w:tblStyle w:val="Tabellengitternetz"/>
          <w:tblW w:w="0" w:type="auto"/>
          <w:tblInd w:w="0" w:type="dxa"/>
          <w:tblBorders>
            <w:top w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:left w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:bottom w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:right w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:insideH w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:insideV w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
          </w:tblBorders><w:tblLook w:val="01E0"/>
        </w:tblPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:for-each select="item">
          <w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- linke spalte: nummerierung -->          
            <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:tcPr>
                <w:tcMar>
                  <w:bottom w:w="30" w:type="dxa"/>
                  
                  <!-- wenn die fußnotenbuchstaben rechtsbündig gesetzt werden sollen,
                    wird der rechte rand der vorderen spalte entsprechend angepasst -->
  
                  <xsl:if test="$sw_fußnoten = 1">
                  <!-- anpassung des rechten zellenrandes an die breite der zahlen -->
                  <xsl:choose>
                    <!-- einstellig schmal , d.h. unter m -->
                    <xsl:when test="$p_count_buchstaben &lt; 13 and $p_count_ziffern &lt; 10">
                      <w:right w:w="200" w:type="dxa"/>
                    </xsl:when>
                    <!-- einstellig breit -->
                    <xsl:when test="$p_count_buchstaben &lt; 27 and $p_count_buchstaben &gt; 12">
                      <w:right w:w="150" w:type="dxa"/>
                    </xsl:when>
                    <xsl:when test="$p_count_ziffern &gt; 99">
                      <w:right w:w="30" w:type="dxa"/>
                    </xsl:when>
                    <!-- zweistellig -->
                    <xsl:otherwise>
                      <w:right w:w="100" w:type="dxa"/>
                    </xsl:otherwise>
                  </xsl:choose>
                </xsl:if>
                </w:tcMar>
                <w:tcW w:w="360" w:type="dxa"/>
              </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-apparat"/>
                  <w:ind w:left="0" w:first-line="0"/>
                  <!-- buchstaben wahlweise rechtsbündig setzen -->
                  <xsl:if test="$sw_fußnoten = 1">
                    <w:jc w:val="right"/>
                  </xsl:if>
                </w:pPr>
                <w:r>
                  <w:t><xsl:number level="any" count="item" from="letter_footnotes" format="a) "/></w:t>
                </w:r>
              </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          
            <!-- rechte spalte: inhalt -->
            <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:tcPr>
                <w:tcMar>
                  <w:bottom w:w="30" w:type="dxa"/>
                </w:tcMar>
                <w:tcW w:w="7740" w:type="dxa"/>
              </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-apparat"/>
                   <w:ind w:left="0" w:first-line="0"/>
                </w:pPr>
                <!-- inhalt zum einfügen an templates verweisen -->
                <xsl:apply-templates/>
              </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
      </w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

      <!--wenn ein ziffernapparat folgt niedrige zwischenzeile einschieben-->
      <xsl:if test="following-sibling::digit_footnotes[item]">
        <w:p>
          <w:pPr>
            <w:pStyle w:val="epi-leerzeile-9pt"/>
          </w:pPr>
        </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>        
      </xsl:if>
      <xsl:comment>buchstabenapparat ende</xsl:comment>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <!-- unterabschnitt ziffernfußnoten -->
  <xsl:template match="digit_footnotes">
    <xsl:param name="p_count_ziffern"></xsl:param>
    <xsl:param name="p_count_buchstaben"></xsl:param>
    <xsl:comment>ziffernapparat anfang</xsl:comment>
    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <!--nach Ziffernapparat testen-->
    <xsl:if test="item"> 
      <!--Fussnoten einfuegen als tabelle-->
      <w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <w:tblPr>
          <w:tblStyle w:val="Tabellengitternetz"/>
          <w:tblW w:w="0" w:type="auto"/>
          <w:tblInd w:w="0" w:type="dxa"/>
          <w:tblBorders>
            <w:top w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:left w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:bottom w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:right w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:insideH w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:insideV w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
          </w:tblBorders>
          <w:tblLook w:val="01E0"/>
        </w:tblPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:for-each select="item">
          <w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- linke spalte für die nummerierung -->
            <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:tcPr>
                <w:tcMar>
                  <w:bottom w:w="30" w:type="dxa"/> 
                  
                  <!-- wenn die fußnotennummern rechtsbündig gesetzt werden sollen,
                  wird der rechte rand der vorderen spalte entsprechend angepasst -->
                  <xsl:if test="$sw_fußnoten = 1">                  
                    <!-- anpassung des rechten zellenrandes an die breite der zahlen -->
                    <xsl:choose>
                      <!-- einstellig, d.h. unter zehn -->
                      <xsl:when test="$p_count_ziffern &lt; 10">
                        <w:right w:w="200" w:type="dxa"/>
                      </xsl:when>
                      <!-- zweistellig -->
                      <xsl:when test="$p_count_ziffern &lt; 100 and $p_count_ziffern &gt; 9">
                        <w:right w:w="100" w:type="dxa"/>
                      </xsl:when>
                      <!-- dreistellig -->
                      <xsl:otherwise>
                        <w:right w:w="30" w:type="dxa"/>
                      </xsl:otherwise>
                    </xsl:choose>
                  </xsl:if>
                </w:tcMar>
                <w:tcW w:w="360" w:type="dxa"/>
              </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-apparat"/>
                  <w:ind w:left="0" w:first-line="0"/>
                  <!-- ziffern wahlweise rechtsbündig setzen -->
                  <xsl:if test="$sw_fußnoten = 1">
                    <w:jc w:val="right"/>
                  </xsl:if>
                </w:pPr>
                <w:r>
                    <!-- nummerierung einfügen-->
                  <w:t><xsl:number level="any" count="item" from="digit_footnotes" format="1) "/></w:t>
                </w:r>
              </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- rechte spalte: inhalt -->
            <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:tcPr>
                <w:tcMar>
                  <w:bottom w:w="30" w:type="dxa"/>
                </w:tcMar>
                <w:tcW w:w="7740" w:type="dxa"/>
              </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <xsl:for-each select="p">
              <w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <w:pPr>
                  <w:pStyle w:val="epi-apparat"/>
                  <w:ind w:left="0" w:first-line="0"/>
                </w:pPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- inhalt zm einfügen an templates verweisen -->
                <xsl:apply-templates /></w:p> 
              </xsl:for-each>
              <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          </w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
      </w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:if>
    <xsl:comment>ziffernapparat ende</xsl:comment>
    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  
  
  <!-- abschnitt nachweise -->
  <xsl:template match="references">
      <!-- auf nachweise testen -->
    <xsl:if test="item">
    <xsl:comment>nachweise anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <w:pPr>
          <w:pStyle w:val="epi-nachweis"/>
        </w:pPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- nachweise einzeln ansteuern und ausgeben  -->
        <xsl:for-each select="item">
          <xsl:apply-templates/>
          <!-- gegebenenfalls trennzeichen setzen -->
          <xsl:if test="following-sibling::item">
           <xsl:choose>
             <!-- münchener reihe als trennzeichen ein semikolon -->
             <xsl:when test="$sw_modus='projects_bay'"><w:r><w:t><xsl:text>;&#x0020;</xsl:text></w:t></w:r></xsl:when>
             <!-- andere reihen als trennzeichen ein halbgeviertstrich -->
             <xsl:otherwise>
               <w:r><w:t><xsl:text>&#x00A0;&#x2013;&#x0020;</xsl:text></w:t></w:r>
             </xsl:otherwise>
           </xsl:choose>
          </xsl:if>  
        </xsl:for-each>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <!--Leerzeile einschieben-->
      <w:p>
        <w:pPr>
          <w:pStyle w:val="epi-leerzeile-10pt"/>
        </w:pPr>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:if>
    <xsl:comment>nachweise ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>

  <!-- notizen zu den abschnitten ausgeben;
        die notizen erscheinen am ende des artikels, 
        sie werden durch einen balken vom eigentlichen artikel abgetrennt,
        die notizen werden in der reihenfolge der abschnitte, 
        zu denen sie gehören, ausgegeben,
        über jeder notiz wird angegeben, zu welchem abschnitt sie gehört
  -->
  <xsl:template match="notes">
    <xsl:comment>notizen anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- ausgabeoptionen abfragen -->
    <xsl:if test="$sw_datum=1 or $sw_notizen=1">
        <!-- leerzeile -->
        <w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <w:pPr>
          <w:pStyle w:val="epi-leerzeile-10pt"/>
        </w:pPr>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <!-- balken einfügen-->
        <wx:pBdrGroup>
        <wx:borders>
          <wx:bottom wx:val="solid" wx:bdrwidth="15" wx:space="1" wx:color="auto"/>
        </wx:borders>
        <w:p>
          <w:pPr>
            <w:pBdr>
              <w:bottom w:val="dotted" w:sz="6" wx:bdrwidth="15" w:space="1" w:color="auto"/>
            </w:pBdr>
            <w:spacing w:line="100" w:line-rule="exact"/>
          </w:pPr>
        </w:p>
      </wx:pBdrGroup> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:if>
    
    <!--letzte Änderung-->
      <!-- ausgabeoptionen nach datum der letzten änderung abfragen -->
    <xsl:if test="$sw_datum=1">
      <!--Zwischenzeilen-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <w:p><w:pPr><w:pStyle w:val="epi-notiz"/></w:pPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <w:r>
          <w:rPr><w:u w:val="single"/></w:rPr>
          <w:t>Letzte Änderung: </w:t>
        </w:r>
        <w:r>
          <w:t><xsl:value-of select="ancestor::article/headline/modified"/>&#x0020;(<xsl:value-of select="ancestor::article/headline/modifier/@acronym"/>)</w:t>
        </w:r>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:if>
    
    <!--Notizen / notes-->
      <!-- ausgaboptionen nach notizen abfragen -->
    <xsl:if test="$sw_notizen=1">
      <!--Zwischenzeile-->
      <w:p>
        <w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <!--ueberschrift-->
      <w:p>
        <w:pPr><w:pStyle w:val="epi-notiz"/></w:pPr>
        <w:r>
          <w:rPr><w:u w:val="single"/></w:rPr>
          <w:t>Notizen:</w:t>
        </w:r>>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <!-- notizen ansteuern -->
      <xsl:for-each select="note">
        <!-- zuordnung zum betreffenden abschnitt ausgeben -->
        <w:p>
          <w:pPr><w:pStyle w:val="epi-notiz"/></w:pPr> 
          <xsl:apply-templates select="allocation"/></w:p>
          <!-- notiztext ausgeben -->
        <xsl:for-each select="note_text">
            <w:p>
              <w:pPr><w:pStyle w:val="epi-notiz"/></w:pPr>
              <xsl:apply-templates/></w:p>
          </xsl:for-each>
        <!--Zwischenzeile-->
        <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </xsl:for-each>
       <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <xsl:comment>notizen ende</xsl:comment>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:if>
  </xsl:template>
 
<!--===Ende: artikel============================================-->


<!--Transkriptionenspalten / transcription collumns-->
    <!-- der abschnitt transkriptionsspalten enthält mindestens 
        zwei abschnitte <inschrift> die bei der ausgabe nebeneinander dargestellt werden sollen;
    die nummerierung soll für beide inschriften davor links nach dem Muster A-B erfolgen
    -->
  <xsl:template match="di_edit_columns">
  <!-- anzahl der inschriften, die spaltenweise auszugeben sind, ermitteln -->
  <xsl:param name="p_anzahl_spalten"><xsl:value-of select="count(inscription)"/></xsl:param>
  <xsl:comment>transkription anfang</xsl:comment>
    <!-- für jedes element <di_edit_columns> wird eine tabelle angelegt;
    die erste spalte der tabelle enthält die nummerierungen nach dem muster A-B oder A-C,
    für jedes element <inschrift> resp. <bearbeitung> wird eine weitere spalte angelegt,
    die inschriftenteile gehen jeweils in <bearbeitung> auf  -->
  <w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

      <!-- erste spalte für die nummerierung -->
      <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <w:tcPr>
          <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
          <w:tcW w:w="750" w:type="dxa"/>
        </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <w:p>
          <w:pPr>
            <xsl:choose>
              <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
              <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
            </xsl:choose>
          </w:pPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- nummerierungen ermitteln -->
          <w:r><w:t><xsl:value-of select="inscription[1]//nr"/>–<xsl:value-of select="inscription[last()]//nr"/></w:t></w:r>
        </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

      <!-- für jede inschrift eine weitere spalte anlegen -->
      <xsl:for-each select="inscription//version">
          <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <w:tcPr>
              <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
              <xsl:choose>
                <xsl:when test="$p_anzahl_spalten=2">
                  <!--<w:tcW w:w="3400" w:type="dxa"/>-->
                  <w:tcW w:w="3583" w:type="dxa"/>
                </xsl:when>
                  <xsl:when test="$p_anzahl_spalten=3">
                  <!--<w:tcW w:w="2266" w:type="dxa"/>-->
                    <w:tcW w:w="2390" w:type="dxa"/>
                </xsl:when>
                <!-- Note bene: für mehr als drei inschriften nebeneinander wird wohl der raum nicht ausreichen -->
              </xsl:choose>
            </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- transkription der inschrift einfügen -->
            <xsl:apply-templates select="content"/>
          </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </xsl:for-each>
    </w:tr>
  </w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  
  <!--zwischenzeile-->
  <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  <xsl:comment>transkription ende</xsl:comment>
   <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</xsl:template>



<!--Transkriptionen / transcriptions-->
  <xsl:template match="inscription">
  <xsl:comment>transkription anfang</xsl:comment>
  <!-- für jede inschrift wird eine tabelle mit zwei spalten angelegt;
  für jede bearbeitung eine zeile, die inschriftenteile werden übersprungen,
  d. h. sie leiten auf die bearbeitung weiter-->
  <w:tbl>
    <w:tblPr>
      <w:tblW w:w="7920" w:type="dxa"/>
      <w:tblCellMar>
          <w:left w:w="10" w:type="dxa"/>
          <w:right w:w="10" w:type="dxa"/>
      </w:tblCellMar>
    </w:tblPr>
    <w:tblGrid>
        <w:gridCol w:w="370"/>
        <w:gridCol w:w="7358"/>
    </w:tblGrid>
    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:if test="@alias[string()] or 
      following-sibling::inscription[@alias[string()]] or 
      preceding-sibling::inscription[@alias[string()]]">
      <!-- bei den münchener bänden wird anstelle der nummerierung der text aus 
           dem feld alias ausgelesen und als eigene zeile über der inschrift eingefügt -->
      <w:tr>
        <w:tc>
          <w:tcPr>
            <w:tcW w:w="370" w:type="dxa"/>
            <w:gridSpan w:val="2"/>
          </w:tcPr>
          <w:p>
            <w:pPr>
              <xsl:choose>
                <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
                <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
              </xsl:choose>
            </w:pPr> 
            <w:r>
              <xsl:choose>
                <xsl:when test="$sw_modus='projects_bay'">
                  <w:t><xsl:number value="@number" format="I"></xsl:number><xsl:text>. </xsl:text></w:t>
                </xsl:when>
                <xsl:otherwise>
                  <w:t><xsl:value-of select="@name"/><xsl:text>. </xsl:text></w:t>
                </xsl:otherwise>
              </xsl:choose>
              <w:t><xsl:value-of select="@alias"/></w:t>
            </w:r>
          </w:p>
          <xsl:if test="@alias[string()]">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-leerzeile-6pt"/>
              </w:pPr>
            </w:p>
          </xsl:if>
        </w:tc>
      </w:tr>
    </xsl:if>
    <xsl:for-each select="inscriptionpart">
      <xsl:if test="@alias[string()]">
      <!-- bei den münchener bänden wird anstelle der nummerierung der text aus 
           dem feld alias ausgelesen und als eigene zeile über der inschrift eingefügt -->
        <w:tr>
          <w:tc>
            <w:tcPr>
                <w:tcW w:w="370" w:type="dxa"/>
                <w:gridSpan w:val="2"/>
             </w:tcPr>
              <w:p>
                <w:pPr>
                  <xsl:choose>
                    <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
                    <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
                  </xsl:choose>
                </w:pPr> 
                <w:r><w:t><xsl:value-of select="@alias"/></w:t></w:r>
              </w:p>
          </w:tc>
        </w:tr>
      </xsl:if>
        <!-- wenn mehrere bearbeitungen (versionen) einer inschrift angelegt wurden, 
            können diese gemäß der ausgabeoption für den artikel
            1. nebeneinander (synoptisch) oder 
            2. untereinander ausgegeben werden -->

      <xsl:choose>
            <!-- 1. synoptische darstellung von zwei bearbeitungen nebeneinander -->
            <!-- die ausgabeoptionen des artikels abfragen 
                 (bezieht sich auf den ganzen artikel) und prüfen, 
                 ob für die betreffende inschrift zwei bearbeitungen 
                 vorliegen (bezieht sich auf jede einzelne inschrift) -->
        <xsl:when test="ancestor::article/outputoptions/outputoption[@norm_iri='di_synopsis'] and version[following-sibling::version]">

          <!-- tabellenzeile für die bearbeitung anlegen -->
          <w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- linke tabellenspalte für die nummerierung -->
            <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:tcPr>
                <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
                <w:tcW w:w="370" w:type="dxa"/>
              </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:p>
                <w:pPr>
                  <xsl:choose>
                    <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
                    <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
                  </xsl:choose>
                </w:pPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <w:r>
                  <!-- bezeichner der inschrift auslesen -->
                  <w:t><xsl:value-of select="ancestor::inscription/@name"/></w:t>
                  <!-- bei mehreren inschriftteilen den bezeichner des teils auslesen -->
                  <xsl:if test="preceding-sibling::inscriptionpart or following-sibling::inscriptionpart">
                    <w:t><xsl:value-of select="@name"/></w:t>  
                  </xsl:if>
                </w:r>
              </w:p>
            </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!--  je eine weitere tabellenspalte für jede bearbeitungen -->
            <xsl:for-each select="version">
            <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:tcPr>
                <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
                <w:tcW w:w="3600" w:type="dxa"/>
              </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <!-- inhalt an template verweisen -->
              <xsl:apply-templates select="content"/></w:tc>
            </xsl:for-each>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          </w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:when>

       <!-- 2. fortlaufende darstellung der bearbeitungen untereinander -->
       <xsl:otherwise>
        <!-- alle bearbeitungen ansteuern und untereinander ausgeben -->
         <xsl:for-each select="version">
          <!-- tabellenzeile für die bearbeitung anlegen -->
          <w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- linke spalte für die nummerierung -->
            <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:tcPr>
                <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
                <w:tcW w:w="370" w:type="dxa"/>
              </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:p>
                <w:pPr>
                  <xsl:choose>
                    <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
                    <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
                  </xsl:choose>
                </w:pPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <w:r>
                  <w:t><xsl:if test="not(ancestor::article/inscription/@alias[string()])">
                      <xsl:choose>
                        <!-- münchener reihe -->
                        <xsl:when test="$sw_modus='projects_bay'"><xsl:value-of select="nr/@nr_bay"/><xsl:if test="string-length(nr/@nr_bay) !=0"><xsl:text>.</xsl:text></xsl:if></xsl:when>
                         <!-- die anderen reihen --> 
                        <xsl:otherwise><xsl:value-of select="nr"/></xsl:otherwise>
                      </xsl:choose>
                    </xsl:if></w:t></w:r>
                  
                <!-- wenn mehrere bearbeitungen vorliegen, die untereinander auszugeben sind, 
                     versionsnummern erzeugen -->
                <xsl:if test="preceding-sibling::version or following-sibling::version">
                  <w:r>
                    <w:rPr>
                      <w:vertAlign w:val="superscript"/>
                      <w:u w:val="single" w:color="black"/>
                    </w:rPr>
                    <w:t><xsl:value-of select="version-nr"/></w:t>
                  </w:r>
                </xsl:if>
              </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- rechte spalte für den text -->
            <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <w:tcPr>
                <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
                <w:tcW w:w="7200" w:type="dxa"/>
              </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- transkription einfügen -->
              <xsl:apply-templates select="content"/></w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          </w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- leerzeile zwischen zwei bearbeitungen / versionen -->
           <xsl:if test="following-sibling::version">
            <w:tr><w:tc>
              <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </w:tc></w:tr>
          </xsl:if>
        </xsl:for-each>
      </xsl:otherwise>
     </xsl:choose>
      <!-- wenn es in der ausgabeoption des artikels vorgegeben ist, 
          die übersetzung der inschrift direkt auf die transkription folgen lassen -->
      <xsl:if test="ancestor::article/outputoptions/outputoption[@norm_iri='di_translations_sectionwise']">
        

        <!-- tabellenzeile erzeugen -->
      <w:tr>
        <!-- linke tabellenspalte erzeugen (bleibt leer) -->
      <w:tc>
        <w:tcPr>
          <w:tcMar><w:bottom w:w="100" w:type="dxa"/></w:tcMar>
          <w:tcW w:w="500" w:type="dxa"/>
        </w:tcPr>
      <w:p></w:p>
      </w:tc>
        <!-- rechte tabellenspalte für den text der übersetzung -->
      <w:tc>
        <w:tcPr>
          <w:tcMar>
          <!--<w:bottom w:w="120" w:type="dxa"/>
          <w:left w:w="380" w:type="dxa"/>
          <w:top w:w="60" w:type="dxa"/>-->
          </w:tcMar>
        </w:tcPr>
        <!-- absatz erzeugen und übersetzung einfügen -->
        <w:p>
          <w:pPr>
            <w:pStyle w:val="epi-uebersetzung2"/>
          </w:pPr>
          <w:r><w:t><xsl:apply-templates select="translation" mode="inscriptionwise"></xsl:apply-templates></w:t></w:r>
        </w:p>
      </w:tc>
    </w:tr>
   </xsl:if>
    </xsl:for-each>
  </w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  
  <!--2. Zwischenzeile-->
  <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  <xsl:comment>transkription ende</xsl:comment>
   <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</xsl:template>

<!-- bearbeitung / version einer inschrift -->
<xsl:template match="version">
  <xsl:choose>
    <!-- wenn ein bereich-tag gesetzt wurde (element <bl>)
         das template dafür aufrufen-->
    <xsl:when test="bl"><xsl:apply-templates/></xsl:when>
    <!-- wenn kein bereich-tag gesetzt wurde (element <bl>)
         absatz erzeugen und templates für weitere elemente aufrufen-->
    <xsl:otherwise>
      <w:p>
        <w:pPr>
          <xsl:choose>
            <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
            <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
          </xsl:choose>
        </w:pPr>
        <xsl:apply-templates/>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<!--Bereiche in den Transkriptionen / sub-areas in transcription area-->
<xsl:template match="bl">
  <!-- transkriptions-bereichen in fussnoten wird ein schließendes p-tag vorangesetzt;
       (und am ende ein öffnendes nachgestellt)-->
  <xsl:if test="ancestor::footnotes and not(starts-with(@align,'properties/alignments/di_lang_'))">
    <xsl:text disable-output-escaping="yes">&#x003C;/w:p&#x003E;</xsl:text>
  </xsl:if>
  
  <!-- Vor Transkriptionsfeldern in der Einleitung eine Leerzeile einfügen -->
  <xsl:if test="preceding-sibling::p"><w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p></xsl:if>
  
<!-- es wird unterschieden zwischen zeilengerechter ausgabe (bei versen und fortlaufender ausgabe -->
  <xsl:choose>
    <!-- bei verszeilen wird eine weitere, innere tabelle angelegt;
    sie besteht aus zwei spalten, die linke für die zeilennummerierung, die rechte für den text-->

    <!-- auf verszeilen testen, wenn keine margialien auftreten -->
    <xsl:when test="vz and not(@value='with_marg_left')">  
     <w:tbl>
     <xsl:for-each select="vz[node()]">
        <!-- vorbereitung der zeilennummerierung in zwei variablen -->
       <xsl:variable name="v_zeilenanzahl">
         <xsl:for-each select="parent::bl">
           <xsl:value-of select="count(vz[text()])"/>
         </xsl:for-each>
       </xsl:variable>
       <xsl:variable name="v_zeilennummer">
         <xsl:number level="single" count="vz[text()]" from="bl" format="1"/>
       </xsl:variable>

      <w:tr>
        <!-- linke spalte für zeilennummern; 
          wenn es mehr als 10 zeilen gibt, wird jede fünfte nummeriert -->
        <w:tc>
          <w:tcPr>
            <w:tcMar>
              <!--<w:top w:w="40" w:type="dxa"/>-->
              <w:right w:w="150" w:type="dxa"/>
            </w:tcMar>
            <w:tcW w:w="350" w:type="dxa"/>
          </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <w:p>
            <w:pPr>
              <w:pStyle w:val="epi-zeilennummer"/>
            </w:pPr>
            <!-- wenn mehr als 10 zeilen vorhanden -->
            <xsl:if test="$v_zeilenanzahl &gt; 10">
              <!-- jede fünfte nummerieren -->
              <xsl:if test="number($v_zeilennummer) mod 5 =0">
            <w:r><w:t><xsl:number count="vz[text()]" from="bl" format="1"/></w:t></w:r>
                </xsl:if>
            </xsl:if>        
          </w:p>
        </w:tc>
        
         <!-- rechte spalte für den text;
              bei distichen wird die pentameterzeile eingezogen,
              dazu wird das attribut @indent ausgwertet -->
        <!-- eingezogene zeile für pentameter -->  
        <xsl:if test="@indent='1'">
           <w:tc>
            <w:p>
              <w:pPr>
                <xsl:choose>
                  <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-pentameter-fn"/></xsl:when>
                  <xsl:otherwise><w:pStyle w:val="epi-pentameter"/></xsl:otherwise>
                </xsl:choose>
                <!-- wenn der vorhergehende bl eine Überschrift oder einen properties/alignments/di_title enthält,
                  folgt auf die letzte Zeile ein Durchschuss -->
                <xsl:if test="parent::bl[preceding-sibling::bl[@align='Überschrift' or @align='properties/alignments/di_title']] and not(following-sibling::vz)">
                  <w:spacing w:after="60"/>
                </xsl:if>
                <xsl:if test="ancestor::footnotes">
                  <w:rPr>
                    <w:sz w:val="18"/>
                  </w:rPr>
                </xsl:if>
              </w:pPr>
              <!-- inhalte an templates verweisen -->
              <xsl:apply-templates/>
            </w:p>
           </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
         </xsl:if>
          <!-- nicht eingezogene zeile -->
         <xsl:if test="@indent='0'">
          <w:tc>
               <w:p>
                <w:pPr>
                  <xsl:choose>
                    <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
                    <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
                  </xsl:choose>
                  <w:spacing w:left="-60"/>
                  <!-- wenn der vorhergehende bl eine Überschrift oder einen properties/alignments/di_title enthält, folgt auf die letzte Zeile ein Durchschuss -->
                  <xsl:if test="parent::bl[preceding-sibling::bl[@align='Überschrift' or @align='properties/alignments/di_title']] and not(following-sibling::vz)">
                    <w:spacing w:after="60"/>
                  </xsl:if>
                </w:pPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                 <xsl:apply-templates/>
               </w:p>
          </w:tc>
           <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
         </xsl:if>
      </w:tr>
     </xsl:for-each>
    </w:tbl>
      <!-- zwischenzeile -->
      <xsl:if test="not(ancestor::footnotes) and not(following-sibling::bl)">
        <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </xsl:if>
    </xsl:when>

    <!-- marginalie am linken rand des transkriptionsfeldes -->
    <!-- auf marginalien testen -->
    <xsl:when test="@value='with_marg_left'">
       <xsl:variable name="v_zeilenanzahl">
           <xsl:value-of select="count(line)"/>
       </xsl:variable>
       <!-- tabelle bilden -->
        <w:tbl>
          <!-- zeilen ansteuern;
               für jede zeile drei spalten bilden:
               1. für die zeilennummern
               2. für marginalien
               3. für den zeilentext
               
          -->
            <xsl:for-each select="line">
                   <xsl:variable name="v_zeilennummer">
                     <xsl:number count="line" from="bl" format="1"/>
                   </xsl:variable>
              <xsl:variable name="v_laenge"><xsl:value-of select="string-length(di_left_margin)"/></xsl:variable>
                <!-- spalte für die zeilennummern -->
                <w:tr>
                  <!-- 1. spalte: zeiöennummern -->
                  <w:tc>
                    <w:tcPr>
                      <w:tcMar>
                        <!--<w:top w:w="40" w:type="dxa"/>-->
                        <w:right w:w="150" w:type="dxa"/>
                      </w:tcMar>
                      <w:tcW w:w="350" w:type="dxa"/>
                    </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <w:p>
                      <w:pPr>
                        <w:pStyle w:val="epi-zeilennummer"/>
                      </w:pPr>
                       <xsl:if test=".//vz">
                         <xsl:if test="$v_zeilenanzahl &gt; 10">
                           <xsl:if test="$v_zeilennummer mod 5 =0">
                              <w:r><w:t><xsl:number count="line" from="bl" format="1"/></w:t></w:r>
                          </xsl:if>
                      </xsl:if>        
                      </xsl:if>
                    </w:p>
                  </w:tc>
                    <!-- 2. spalte: für die marginalie -->
                    <w:tc>
                      <w:tcPr><xsl:if test="string-length(di_left_margin) &gt; 15"><w:vmerge w:val="restart"/></xsl:if>
                        <xsl:if test="string-length(preceding-sibling::line[1]/di_left_margin) &gt; 15 and not(marg)"><w:vmerge/></xsl:if>
                                    <w:tcW w:w="1400" w:type="dxa"/>
                      </w:tcPr>
                      <!-- absatz für die marginalie -->
                      <w:p>
                        <w:pPr>
                          <xsl:choose>
                            <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
                            <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
                          </xsl:choose>
                          <w:ind w:right="113"/></w:pPr>
                        <!-- marginalie an template verweisen -->
                        <xsl:apply-templates select="di_left_margin" mode="marg1"></xsl:apply-templates>
                      </w:p>
                    </w:tc>
                    <!-- 3. spalte: für den zeilentext -->
                    <w:tc>
                      <!-- absatz bilden -->
                       <w:p>
                         <w:pPr>
                           <xsl:choose>
                             <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
                             <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
                           </xsl:choose>
                           <xsl:if test="vz[@value='einger&#xFC;ckt' or @data-link-iri='properties/indentations/di_indent']"> <w:ind w:left="240"/></xsl:if>
                         </w:pPr>
                         <!-- zeileninhalt zur ausgabe und formatierung an templates verweisen -->
                         <xsl:apply-templates></xsl:apply-templates>
                       </w:p>
                    </w:tc>
                </w:tr>
            </xsl:for-each>
        </w:tbl>
        <w:p></w:p>
    </xsl:when>

    <!-- absätze ohne verse erhalten keine zeilennummern und 
      werden um die breite der spalte für zeilennummern nach links gerückt;
    außer wenn ganze inschriften nebeneinander in spalten stehen (transkriptionsspalten)-->
    <xsl:otherwise>
      <!-- absatz anlegen -->
      <w:p>
        <w:pPr>
          <xsl:choose>
            <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
            <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
          </xsl:choose>
          <xsl:if test="not(ancestor::di_edit_columns)"><w:ind w:left="350"/></xsl:if>
          <xsl:if test="ancestor::di_edit_columns"><w:ind w:right="350"/></xsl:if>
        </w:pPr>
        <!-- inhalte an templates verweisen -->
        <xsl:apply-templates/></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:otherwise>
  </xsl:choose>
  <!-- transkriptions-bereichen in fussnoten wird ein öffnendes p-tag nachgeseztz -->
  <xsl:if test="ancestor::footnotes">
    <xsl:text disable-output-escaping="yes">&#x003C;w:p&#x003E;</xsl:text>
      <w:pPr><w:pStyle w:val="epi-apparat"/><w:ind w:left="0" w:first-line="0"/></w:pPr> 
  </xsl:if>    
</xsl:template>
<!-- Ende transkriptionsbereich / element <bl> /  -->


<!-- marginalen formatieren -->
  <xsl:template match="di_left_margin"><!-- entfällt --></xsl:template>
  <xsl:template match="di_left_margin" mode="marg1"><xsl:apply-templates></xsl:apply-templates></xsl:template>

<!--Ende: Transkriptionsformate / transcriptions=============================-->  
  
  <!--Spalten im Transkriptionsfeld <bl> / columns in transcription area-->
  <!-- das die spalten umklammernde element <bl> muss das attribut 
       @align="properties/alignments/di_columns" oder
       @data-link-value="Spalten" aufweisen,
       zusätzlich müssen auch die spalten bildenden <bl> als kindknoten vorhanden sein -->
  <xsl:template match="content/bl[@align='properties/alignments/di_columns' or @data-link-iri='properties/alignments/di_columns'][bl]">
    <!-- tabelle anlegen -->
    <w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <!-- tabellenzeile bilden -->
      <w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- die als spalten infrage kommenden elemente <br> ansteuern -->
        <xsl:for-each select="bl">
          <!-- spalten bilden  -->
          <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:choose>
              <!-- auf verszeilen prüfen -->
              <xsl:when test="vz">
                <xsl:for-each select="vz[position()!=1]">
                  <w:p>
                    <w:pPr>
                      <xsl:choose>
                        <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
                        <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
                      </xsl:choose>
                      <xsl:if test="position()!=last()">
                        <w:ind w:right="400"/>
                      </xsl:if>
                      <w:ind w:left="350"/>
                      <xsl:choose>
                        <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
                        <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
                      </xsl:choose>
                    </w:pPr>
                    <!-- transkription am templates verweisen -->
                    <xsl:apply-templates/>
                  </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
              </xsl:when>
              <!-- wenn keine verszeilen vorkommen -->
              <xsl:otherwise>
                <w:p>
                  <w:pPr>
                    <xsl:choose>
                      <xsl:when test="ancestor::footnotes"><w:pStyle w:val="epi-transkription-fn"/></xsl:when>
                      <xsl:otherwise><w:pStyle w:val="epi-transkription"/></xsl:otherwise>
                    </xsl:choose>
                   <xsl:if test="position()!=last()">
                      <w:ind w:right="400"/>
                    </xsl:if>
                    <xsl:if test="position()= 1">
                      <w:ind w:left="350"/>
                    </xsl:if>
                  </w:pPr>
                  <!-- transkription an templates verweisen -->
                  <xsl:apply-templates/>
                </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              </xsl:otherwise>
            </xsl:choose>
          </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
      </w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <w:p/> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <!--Überschriften im Transkriptionsfeld / headers in transcription area-->
  <xsl:template match="bl[@align='Überschrift' or @align='properties/alignments/di_title']">
    <!-- absatz bilden -->
    <w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <w:pPr>
        <w:ind w:left="200"/>
      </w:pPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <!-- überschrift an templates verweisen -->
      <xsl:apply-templates/>
    </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
  <!--Ende: Spalten im Trankriptionsfeld / columns in the transcription area-->
 
  <!-- zeilenumbrüche in transkriptionsfeldern mit spalten -->
  <xsl:template match="bl[@data-link-iri='properties/alignments/di_columns' or @align='properties/alignments/di_columns']/bl/z[@connex='nz' or @data-link-iri='properties/linebindings/di_newline']">
    <w:r><w:br/></w:r>
  </xsl:template>
  <!-- die schrägstriche werden unterdrückt -->
  <xsl:template match="bl[@data-link-iri='properties/alignments/di_columns' or align or @align='properties/alignments/di_columns']/bl/z[@connex='nz' or @data-link-iri='properties/linebindings/di_newline']/text()"></xsl:template>
  
 <!-- textknoten in beschreibung, kommentar und fußnoten-->
<!--  <xsl:template match="di_description/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
  <xsl:template match="di_comment/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>-->
  <xsl:template match="footnotes//item/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>


</xsl:stylesheet>