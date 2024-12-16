<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

  <xsl:output method="xml" version="1.0"/>

  <xsl:template name="datierung">
    <xsl:param name="p_datierung_index"></xsl:param>
    <!-- ausgabeoptionen aufnehmen -->
<!--    <xsl:param name="p_datkat"><xsl:value-of select="sections/section[@sectiontype='outputoptions']/items/item/property/lemma"/></xsl:param>-->
    <xsl:param name="p_datOpt"><xsl:if test="sections/section[@sectiontype='outputoptions']/items/item/property/lemma[text()='1']">1</xsl:if></xsl:param>
    <!--<datOpt><xsl:copy-of select="$p_datOpt"/></datOpt>-->
    <xsl:choose>
      <xsl:when test="$p_datOpt=1">
        <xsl:for-each select="sections/section[@sectiontype='conditions']/items/item/date_value">
          <xsl:call-template name="datierung-sprache"></xsl:call-template>
        </xsl:for-each>
      </xsl:when>
      <xsl:otherwise><xsl:call-template name="datierungssuche"></xsl:call-template></xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <xsl:template name="datierungssuche">
    <xsl:param name="p_datierung1">
      <xsl:for-each select="sections//section[@sectiontype='inscriptiontext' or @sectiontype='conditions']/items/item[@itemtype='conditions' or @itemtype='transcriptions']">
        <xsl:sort select="date_sort"/>
        <xsl:for-each select="date_value[text()]">
          <xsl:call-template name="datierung-sprache"></xsl:call-template>
        </xsl:for-each>
      </xsl:for-each>
    </xsl:param>
    <datierung>
      <xsl:for-each select="$p_datierung1/datierung[not(text()=preceding-sibling::datierung/text())]">
        <xsl:value-of select="."/><xsl:if test="following-sibling::datierung[not(text()=preceding-sibling::datierung/text())]"><xsl:text>, </xsl:text></xsl:if>
      </xsl:for-each>
    </datierung><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <datierung_start>
      <xsl:value-of select="$p_datierung1/datierung[1]"/>
    </datierung_start><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <datierung_end>
      <xsl:value-of select="$p_datierung1/datierung[last()]"/>
    </datierung_end><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <datierung_sort>
     <xsl:value-of select="@datierung_index"/>
    </datierung_sort><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
  </xsl:template>

  <xsl:template name="datierung-sprache">
    <xsl:param name="p_datierung-string"><xsl:value-of select="."/></xsl:param>
    <datierung>
      <xsl:choose>
        <!-- die fremdsprachige datenbank wird identifiziert,
          die datierungs-strings werden zur übersetzung an die folgenden templates übergeben -->
        <xsl:when test="ancestor::book/project/database[text()='inscriptiones_estoniae']">
          <!-- es wird unterschieden nach einfachen und mit trennstrich zusammengesetzten datierungen -->
          <xsl:choose>
            <!-- zusammengesetzte datierungen:
              der datierungsstring wird zur trennung der beiden teile an ein template übergeben;
            anschließend wird für beide teile jeweils einzeln das übersetzungs-template aufgerufen-->
            <xsl:when test="contains($p_datierung-string,'–')">
              <xsl:call-template name="datierung-split">
                <xsl:with-param name="p_datierung-string"><xsl:value-of select="$p_datierung-string"/></xsl:with-param>
              </xsl:call-template>
            </xsl:when>
            <!-- einfache datierungen: das übersetzungs-template wird aufgerufen -->
            <xsl:otherwise>
              <xsl:call-template name="datierung-string-translate">
                <xsl:with-param name="p_datierung-string"><xsl:value-of select="$p_datierung-string"/></xsl:with-param>
              </xsl:call-template>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:when>
        <!-- wenn die datenbank nicht fremdspachig ist, werden die vorhandenen strings übernommen -->
        <xsl:otherwise>
          <xsl:value-of select="."/>
        </xsl:otherwise>
      </xsl:choose>
    </datierung>
  </xsl:template>

  <xsl:template name="datierung-split">
    <!-- bei zusammengesetzten datierunge wird der string in die beiden bestandteile zerlegt -->
    <xsl:param name="p_datierung-string"></xsl:param>
    <xsl:param name="p_datierung-string1"><xsl:value-of select="substring-before($p_datierung-string,'–')"/></xsl:param>
    <xsl:param name="p_datierung-string2"><xsl:value-of select="substring-after($p_datierung-string,'–')"/></xsl:param>

    <!-- die beiden teile werden einzeln an ein template zur übersetzung übergeben und die ergebnisse hier wieder zusammengefügt -->
    <xsl:call-template name="datierung-string-translate">
      <xsl:with-param name="p_datierung-string"><xsl:value-of select="$p_datierung-string1"/></xsl:with-param>
    </xsl:call-template>
    <xsl:text>-</xsl:text>
    <xsl:call-template name="datierung-string-translate">
      <xsl:with-param name="p_datierung-string"><xsl:value-of select="$p_datierung-string2"/></xsl:with-param>
    </xsl:call-template>
   </xsl:template>

<xsl:template name="datierung-string-translate">
    <!-- gesamter datierungsstring (wird übernommen) -->
    <xsl:param name="p_datierung-string"></xsl:param>
    <!-- teilstring vor Jh. (wird hier erzeugt)-->
    <xsl:param name="p_datierung-string-vor_jh"><xsl:value-of select="substring-before($p_datierung-string,'Jh.')"/></xsl:param>

  <xsl:choose>
    <!-- bei approximativen/erschlossenen datierungsangaben werden die sprachlichen bestandteile ins Estnische übersetzt -->
    <xsl:when test="contains($p_datierung-string,'Jh.')">
      <!-- die jahrhundert-angaben werden an ein weiteres template übergeben und dort transformiert;
      die string-parameter werden mit übergeben-->
      <xsl:call-template name="datierung-jh">
        <xsl:with-param name="p_datierung-string"><xsl:value-of select="$p_datierung-string"/></xsl:with-param>
        <xsl:with-param name="p_datierung-string-vor_jh"><xsl:value-of select="$p_datierung-string-vor_jh"/></xsl:with-param>
      </xsl:call-template>
    </xsl:when>
    <!-- alle anderen werden hier umgewandelt -->
    <xsl:when test="contains($p_datierung-string,'um')">
      <xsl:text>ca</xsl:text><xsl:value-of select="substring-after($p_datierung-string,'um')"/>
    </xsl:when>
    <xsl:when test="contains($p_datierung-string,'nach')">
      <xsl:text>pärast</xsl:text><xsl:value-of select="substring-after($p_datierung-string,'nach')"/>
    </xsl:when>
    <xsl:when test="contains($p_datierung-string,'vor')">
      <xsl:text>enne</xsl:text><xsl:value-of select="substring-after($p_datierung-string,'vor')"/>
    </xsl:when>
    <xsl:when test="contains($p_datierung-string,'o. früher')">
      <xsl:value-of select="substring-before($p_datierung-string,'o. früher')"/><xsl:text>või varem</xsl:text>
    </xsl:when>
    <xsl:when test="contains($p_datierung-string,'o. später')">
      <xsl:value-of select="substring-before($p_datierung-string,'o. später')"/><xsl:text>või hiljem</xsl:text>
    </xsl:when>
    <!-- bloße jahreszahlen werden übernommen -->
    <xsl:otherwise><xsl:value-of select="$p_datierung-string"/></xsl:otherwise>
  </xsl:choose>
</xsl:template>


  <!-- datierungen mit jahrhundert-angabe -->
  <xsl:template name="datierung-jh">
    <xsl:param name="p_datierung-string-vor_jh"></xsl:param>
    <xsl:param name="p_datierung-string"></xsl:param>
    <xsl:choose>
      <!-- jahrhundert hälften -->
      <xsl:when test="contains($p_datierung-string-vor_jh,'H.')">
        <xsl:value-of select="substring-after($p_datierung-string-vor_jh,'H.')"/><xsl:text>saj. </xsl:text>
        <xsl:value-of select="substring-before($p_datierung-string-vor_jh,'H.')"/><xsl:text>pool</xsl:text>
        <xsl:if test="contains($p_datierung-string,'?')"><xsl:text>?</xsl:text></xsl:if>
      </xsl:when>
      <!-- jahrhundert drittel -->
      <xsl:when test="contains($p_datierung-string-vor_jh,'D.')">
        <xsl:value-of select="substring-after($p_datierung-string-vor_jh,'D.')"/><xsl:text>saj. </xsl:text>
        <xsl:value-of select="substring-before($p_datierung-string-vor_jh,'D.')"/><xsl:text>kolmandik</xsl:text>
        <xsl:if test="contains($p_datierung-string,'?')"><xsl:text>?</xsl:text></xsl:if>
      </xsl:when>
      <!-- jahrhundert viertel -->
      <xsl:when test="contains($p_datierung-string-vor_jh,'V.')">
        <xsl:value-of select="substring-after($p_datierung-string-vor_jh,'V.')"/><xsl:text>saj. </xsl:text>
        <xsl:value-of select="substring-before($p_datierung-string-vor_jh,'V.')"/><xsl:text>veerand</xsl:text>
        <xsl:if test="contains($p_datierung-string,'?')"><xsl:text>?</xsl:text></xsl:if>
      </xsl:when>
      <!-- jahrhundert anfang -->
      <xsl:when test="contains($p_datierung-string-vor_jh,'A.')">
        <xsl:value-of select="substring-after($p_datierung-string-vor_jh,'A.')"/><xsl:text>saj. algus</xsl:text>
        <xsl:if test="contains($p_datierung-string,'?')"><xsl:text>?</xsl:text></xsl:if>
      </xsl:when>
      <!-- jahrhundert mitte -->
      <xsl:when test="contains($p_datierung-string-vor_jh,'M.')">
        <xsl:value-of select="substring-after($p_datierung-string-vor_jh,'M.')"/><xsl:text>saj. keskp.</xsl:text>
        <xsl:if test="contains($p_datierung-string,'?')"><xsl:text>?</xsl:text></xsl:if>
      </xsl:when>
      <!-- jahrhundert ende -->
      <xsl:when test="contains($p_datierung-string-vor_jh,'E.')">
        <xsl:value-of select="substring-after($p_datierung-string-vor_jh,'E.')"/><xsl:text>saj. lõpp</xsl:text>
        <xsl:if test="contains($p_datierung-string,'?')"><xsl:text>?</xsl:text></xsl:if>
      </xsl:when>
      <xsl:otherwise><xsl:value-of select="$p_datierung-string-vor_jh"/><xsl:text>saj.</xsl:text></xsl:otherwise>
    </xsl:choose>

  </xsl:template>

</xsl:stylesheet>
