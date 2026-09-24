<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
>

  <xsl:output method="xml" version="1.0"/>

<!-- 
    dieses stylesheet sammelt die datierungsangaben eines artikels, 
    entfernt dopubletten,  
    sortiert die angaben chronlogisch nach dem sortierschlüssel,
    fügt die datierungsangaben zwecks ausgabe in der kopfzeile des artikels kommasepariert zu einen string zusammen,
    erzeugt referenzpunkte für den beginn und das ende der datierungsspanne,
    gibt den referenzpunkt für die chronologische einsortierung des betreffenden artikels weiter
    
    es wird aufgerufen in di-trans3-articles.xsl
  
  -->
  
  <xsl:template name="dating">
 
  <!-- 1) alle datierungen des artikels sammeln -->
    <xsl:param name="p_dating1">
    <!-- a) die datierung des objekts -->
    <xsl:for-each select="sections/section[@sectiontype='conditions']/items/item[@itemtype='conditions']">
      <dating>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:copy-of select="date_sort"></xsl:copy-of>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:copy-of select="date_value"></xsl:copy-of>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:copy-of select="date_start"></xsl:copy-of>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:copy-of select="date_end"></xsl:copy-of>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </dating><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:for-each>
      <!-- b) die datierungen der inschriften -->
    <xsl:for-each select="sections/section[@sectiontype='inscription']/section/section/items/item[@itemtype='transcriptions']">
      <dating>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:copy-of select="date_sort"></xsl:copy-of>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:copy-of select="date_value"></xsl:copy-of>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:copy-of select="date_start"></xsl:copy-of>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:copy-of select="date_end"></xsl:copy-of>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </dating>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:for-each>
  </xsl:param>
  
  <!-- 2. doubletten entfernen -->
  <xsl:param name="p_dating2">
    <xsl:for-each select="$p_dating1/dating">
      <xsl:if test="not(date_value= preceding-sibling::dating/date_value)">
        <!-- leere <dating>-elemente übergehen -->
        <xsl:if test="*">
          <!-- die nicht leeren kopieren -->
          <xsl:copy-of select="."></xsl:copy-of>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>
      </xsl:if>
    </xsl:for-each>
  </xsl:param>
  
  <!-- 3. sortieren -->
  <xsl:param name="p_dating3">
    <xsl:for-each select="$p_dating2/dating">
      <xsl:sort select="date_sort" order="ascending" lang="de" case-order="upper-first" />
      <xsl:copy-of select="."></xsl:copy-of>  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:for-each>
  </xsl:param>
  
  <!-- 4. datierungs-strings aufreihen -->
  <xsl:param name="p_dating_value">
    <xsl:for-each select="$p_dating3/dating">
      <xsl:value-of select="date_value"/>
      <xsl:if test="following-sibling::dating"><xsl:text>, </xsl:text></xsl:if>
    </xsl:for-each>
  </xsl:param>
  
  <!-- parametertest  
  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  <parametertest>
  <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  <p_dating1><xsl:copy-of select="$p_dating1"></xsl:copy-of></p_dating1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  <p_dating2><xsl:copy-of select="$p_dating2"></xsl:copy-of></p_dating2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  <p_dating3><xsl:copy-of select="$p_dating3"></xsl:copy-of></p_dating3><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  <p_dating_value><xsl:copy-of select="$p_dating_value"></xsl:copy-of></p_dating_value><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  -->
  
  <!-- 5. datierungselemente ausgeben -->
  <xsl:choose>
    <!-- wenn ein eigener abschnitt für die datierungsangaben angelegt wurde: -->
    <xsl:when test="sections/section[@norm_iri='di_headline_date']">
      <date_value><xsl:value-of select="sections/section[@norm_iri='di_headline_date']/items/item/content"/></date_value>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:when>
    <!-- wenn kein eigener abschnitt angelegt wurde: die ermittelten datierungs-string aus dem parameter einfügen -->
    <xsl:otherwise>
      <date_value><xsl:value-of select="$p_dating_value"/></date_value>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:otherwise>
  </xsl:choose>
    <!-- elemente mit den in parametern ermittelten referenzpunkten einfügen:
          die beiden ersten gemäß der ältesten datierung, den dritten gemäß der jüngsten datierung  
    -->
  <xsl:for-each select="$p_dating3">
    <xsl:copy-of select="dating[1]/date_sort"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:copy-of select="dating[1]/date_start"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:copy-of select="dating[last()]/date_end"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</xsl:template>


<!-- 
    die folgenden templates dienen der übersetzung der datierungsausdrücke ins estnische;
    sie sind noch nicht (wieder) implementiert,
    gegebenenfalls können sie aber angepasst werden
  
-->
    
  <xsl:template name="dating-language">
    <xsl:param name="p_dating-string"><xsl:value-of select="."/></xsl:param>
    <dating>
      <xsl:choose>
        <!-- die fremdsprachige datenbank wird identifiziert, 
          die datierungs-strings werden zur übersetzung an die folgenden templates übergeben -->
        <xsl:when test="ancestor::book/project/database[text()='inscriptiones_estoniae']">
          <!-- es wird unterschieden nach einfachen und mit trennstrich zusammengesetzten datierungen -->
          <xsl:choose>
            <!-- zusammengesetzte datierungen: 
              der datierungsstring wird zur trennung der beiden teile an ein template übergeben;
            anschließend wird für beide teile jeweils einzeln das übersetzungs-template aufgerufen-->
            <xsl:when test="contains($p_dating-string,'–')">
              <xsl:call-template name="dating-split">
                <xsl:with-param name="p_dating-string"><xsl:value-of select="$p_dating-string"/></xsl:with-param>
              </xsl:call-template>
            </xsl:when>
            <!-- einfache datierungen: das übersetzungs-template wird aufgerufen -->
            <xsl:otherwise>
              <xsl:call-template name="dating-string-translate">
                <xsl:with-param name="p_dating-string"><xsl:value-of select="$p_dating-string"/></xsl:with-param>
              </xsl:call-template>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:when>
        <!-- wenn die datenbank nicht fremdspachig ist, werden die vorhandenen strings übernommen -->
        <xsl:otherwise>
          <xsl:value-of select="."/>
        </xsl:otherwise>
      </xsl:choose>
    </dating>
  </xsl:template>
  
  <xsl:template name="dating-split">
    <!-- bei zusammengesetzten datierunge wird der string in die beiden bestandteile zerlegt -->
    <xsl:param name="p_dating-string"></xsl:param>
    <xsl:param name="p_dating-string1"><xsl:value-of select="substring-before($p_dating-string,'–')"/></xsl:param>
    <xsl:param name="p_dating-string2"><xsl:value-of select="substring-after($p_dating-string,'–')"/></xsl:param>
    
    <!-- die beiden teile werden einzeln an ein template zur übersetzung übergeben und die ergebnisse hier wieder zusammengefügt -->
    <xsl:call-template name="dating-string-translate">
      <xsl:with-param name="p_dating-string"><xsl:value-of select="$p_dating-string1"/></xsl:with-param>
    </xsl:call-template>
    <xsl:text>-</xsl:text>
    <xsl:call-template name="dating-string-translate">
      <xsl:with-param name="p_dating-string"><xsl:value-of select="$p_dating-string2"/></xsl:with-param>
    </xsl:call-template>
   </xsl:template>

<xsl:template name="dating-string-translate">
    <!-- gesamter datierungsstring (wird übernommen) -->
    <xsl:param name="p_dating-string"></xsl:param>
    <!-- teilstring vor Jh. (wird hier erzeugt)-->
    <xsl:param name="p_dating-string-vor_jh"><xsl:value-of select="substring-before($p_dating-string,'Jh.')"/></xsl:param>

  <xsl:choose>
    <!-- bei approximativen/erschlossenen datierungsangaben werden die sprachlichen bestandteile ins Estnische übersetzt -->
    <xsl:when test="contains($p_dating-string,'Jh.')">
      <!-- die jahrhundert-angaben werden an ein weiteres template übergeben und dort transformiert;
      die string-parameter werden mit übergeben-->
      <xsl:call-template name="dating-jh">
        <xsl:with-param name="p_dating-string"><xsl:value-of select="$p_dating-string"/></xsl:with-param>
        <xsl:with-param name="p_dating-string-vor_jh"><xsl:value-of select="$p_dating-string-vor_jh"/></xsl:with-param>
      </xsl:call-template>      
    </xsl:when>
    <!-- alle anderen werden hier umgewandelt -->
    <xsl:when test="contains($p_dating-string,'um')">
      <xsl:text>ca</xsl:text><xsl:value-of select="substring-after($p_dating-string,'um')"/>
    </xsl:when>
    <xsl:when test="contains($p_dating-string,'nach')">
      <xsl:text>pärast</xsl:text><xsl:value-of select="substring-after($p_dating-string,'nach')"/>
    </xsl:when>
    <xsl:when test="contains($p_dating-string,'vor')">
      <xsl:text>enne</xsl:text><xsl:value-of select="substring-after($p_dating-string,'vor')"/>
    </xsl:when>
    <xsl:when test="contains($p_dating-string,'o. früher')">
      <xsl:value-of select="substring-before($p_dating-string,'o. früher')"/><xsl:text>või varem</xsl:text>
    </xsl:when>
    <xsl:when test="contains($p_dating-string,'o. später')">
      <xsl:value-of select="substring-before($p_dating-string,'o. später')"/><xsl:text>või hiljem</xsl:text>
    </xsl:when>
    <!-- bloße jahreszahlen werden übernommen -->
    <xsl:otherwise><xsl:value-of select="$p_dating-string"/></xsl:otherwise>
  </xsl:choose>
</xsl:template>
  
  
  <!-- datierungen mit jahrhundert-angabe -->
  <xsl:template name="dating-jh">
    <xsl:param name="p_dating-string-vor_jh"></xsl:param>
    <xsl:param name="p_dating-string"></xsl:param>
    <xsl:choose>
      <!-- jahrhundert hälften -->
      <xsl:when test="contains($p_dating-string-vor_jh,'H.')">
        <xsl:value-of select="substring-after($p_dating-string-vor_jh,'H.')"/><xsl:text>saj. </xsl:text>
        <xsl:value-of select="substring-before($p_dating-string-vor_jh,'H.')"/><xsl:text>pool</xsl:text>
        <xsl:if test="contains($p_dating-string,'?')"><xsl:text>?</xsl:text></xsl:if>
      </xsl:when>
      <!-- jahrhundert drittel -->
      <xsl:when test="contains($p_dating-string-vor_jh,'D.')">
        <xsl:value-of select="substring-after($p_dating-string-vor_jh,'D.')"/><xsl:text>saj. </xsl:text>
        <xsl:value-of select="substring-before($p_dating-string-vor_jh,'D.')"/><xsl:text>kolmandik</xsl:text>
        <xsl:if test="contains($p_dating-string,'?')"><xsl:text>?</xsl:text></xsl:if>
      </xsl:when>
      <!-- jahrhundert viertel -->
      <xsl:when test="contains($p_dating-string-vor_jh,'V.')">
        <xsl:value-of select="substring-after($p_dating-string-vor_jh,'V.')"/><xsl:text>saj. </xsl:text>
        <xsl:value-of select="substring-before($p_dating-string-vor_jh,'V.')"/><xsl:text>veerand</xsl:text>
        <xsl:if test="contains($p_dating-string,'?')"><xsl:text>?</xsl:text></xsl:if>
      </xsl:when>
      <!-- jahrhundert anfang -->
      <xsl:when test="contains($p_dating-string-vor_jh,'A.')">
        <xsl:value-of select="substring-after($p_dating-string-vor_jh,'A.')"/><xsl:text>saj. algus</xsl:text>
        <xsl:if test="contains($p_dating-string,'?')"><xsl:text>?</xsl:text></xsl:if>
      </xsl:when>
      <!-- jahrhundert mitte -->
      <xsl:when test="contains($p_dating-string-vor_jh,'M.')">
        <xsl:value-of select="substring-after($p_dating-string-vor_jh,'M.')"/><xsl:text>saj. keskp.</xsl:text>
        <xsl:if test="contains($p_dating-string,'?')"><xsl:text>?</xsl:text></xsl:if>
      </xsl:when>
      <!-- jahrhundert ende -->
      <xsl:when test="contains($p_dating-string-vor_jh,'E.')">
        <xsl:value-of select="substring-after($p_dating-string-vor_jh,'E.')"/><xsl:text>saj. lõpp</xsl:text>
        <xsl:if test="contains($p_dating-string,'?')"><xsl:text>?</xsl:text></xsl:if>
      </xsl:when>
      <xsl:otherwise><xsl:value-of select="$p_dating-string-vor_jh"/><xsl:text>saj.</xsl:text></xsl:otherwise>
    </xsl:choose>
    
  </xsl:template>

</xsl:stylesheet>
