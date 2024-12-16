<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>
<xsl:import href="../commons/epi-switch.xsl"/>

    <xsl:template name="schrifthoehen">
    <xsl:param name="anzahl_inschriften"></xsl:param>
    <xsl:param name="db"></xsl:param>
    <!-- erste transformation: ausgewählte angaben unterdrücken, unterscheiden nach ziffern und buchstaben,
        nummerierung der inschriftenverweise-->
    <xsl:param name="schrifthoehen1">
        <!-- der name der datenbank wird eingefügt, um später bei nichtdeutschen projekten
            sprachspezifische bezeichnungen zu ermöglichen -->
    <!--<xsl:for-each select=".//table[@type='items_schrifthoehen']/item">-->
    <xsl:for-each select="sections/section[@sectiontype='inscription']/items/item[@itemtype='fontheights']">
        <xsl:choose>

            <!-- schrifthöhen von versalien werden unterdrückt -->
            <xsl:when test="contains(content,'Versal')"></xsl:when>
            <xsl:otherwise>
                <schrifthoehe><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                   <zeichentyp>
                       <!-- es wird nach buchtstaben und ziffern unterschieden -->
                       <xsl:choose>
                           <xsl:when test="contains(ancestor::items/item[@itemtype='fonttypes']/property/lemma,'arabisch') and
                               contains(ancestor::items/item[@itemtype='fonttypes']/property/lemma,'iffer')">ziffern</xsl:when>
                           <xsl:otherwise>buchstaben</xsl:otherwise>
                       </xsl:choose>
                   </zeichentyp><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                    <wert><xsl:value-of select="value"/></wert><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                    <ergaenzung><xsl:value-of select="content"/></ergaenzung><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                   <links>
                       <xsl:choose>
                           <!-- wenn im ergänzungsfeld ein verweis auf einen inschriftenteil steht, wird dieser ausgelesen -->
                           <xsl:when test="content/rec_intern">
                               <xsl:for-each select="content/rec_intern">
                                   <xsl:apply-templates select="." /><xsl:if test="following-sibling::rec_intern">, </xsl:if>
                               </xsl:for-each>

                           </xsl:when>
                           <!-- anderenfalls wird der inschrift-bezeichner (inschrift-nummer) ausgelesen-->
                           <xsl:otherwise>
                               <link type="inschrift">
                                   <xsl:attribute name="target_id"><xsl:value-of select="ancestor::section[@sectiontype='inscription']/@id" /></xsl:attribute>
                                    <xsl:copy-of select="ancestor::section[@sectiontype='inscription']/@number"/>
                                    <xsl:choose>
                                        <xsl:when test="$modus='projects_bay'"><xsl:number value="ancestor::section[@sectiontype='inscription']/@number" format="I"/></xsl:when>
                                        <xsl:otherwise><xsl:value-of select="ancestor::section[@sectiontype='inscription']/@name" /></xsl:otherwise>
                                    </xsl:choose>
                               </link>
                           </xsl:otherwise>
                       </xsl:choose>
                   </links><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                </schrifthoehe><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
             </xsl:otherwise>
        </xsl:choose>
    </xsl:for-each>
    </xsl:param>

    <!-- zweite transformation: gleiche buchstabenhöhen werden zusammengezogen, dasselbe bei ziffernhöhen -->
    <xsl:param name="schrifthoehen2">
      <!-- buchstaben -->
      <xsl:for-each select="$schrifthoehen1/schrifthoehe[zeichentyp[text()='buchstaben']][not(wert=preceding-sibling::schrifthoehe[zeichentyp[text()='buchstaben']]/wert)]">
        <schrifthoehe_buchstaben><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <wert><xsl:value-of select="wert"/></wert><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <xsl:copy-of select="ergaenzung"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <links><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                <xsl:copy-of select="links/link"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

                <xsl:for-each select="following-sibling::schrifthoehe[zeichentyp[text()='buchstaben']][wert=current()/wert]">
                    <xsl:copy-of select="links/link"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                </xsl:for-each>
            </links><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        </schrifthoehe_buchstaben><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    </xsl:for-each>

    <!-- ziffern -->
    <xsl:for-each select="$schrifthoehen1/schrifthoehe[zeichentyp[text()='ziffern']][not(wert=preceding-sibling::schrifthoehe[zeichentyp[text()='ziffern']]/wert)]">
        <schrifthoehe_ziffern><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <wert><xsl:value-of select="wert"/></wert><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <xsl:copy-of select="ergaenzung"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <links><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                <xsl:copy-of select="links/link"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

                <xsl:for-each select="following-sibling::schrifthoehe[zeichentyp[text()='ziffern']][wert=current()/wert]">
                    <xsl:copy-of select="links/link"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                </xsl:for-each>
            </links><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        </schrifthoehe_ziffern><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    </xsl:for-each>
    </xsl:param>

<xsl:param name="p_anzahl_hoehen">
<xsl:for-each select="$schrifthoehen2"><xsl:value-of select="count(*)"/></xsl:for-each>
</xsl:param>
<xsl:param name="p_anzahl_links"><xsl:for-each select="$schrifthoehen2"><xsl:value-of select="count(.//link)"/></xsl:for-each></xsl:param>

<!-- parameter ausgeben
<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<schrifthoehen1><xsl:copy-of select="$schrifthoehen1"/></schrifthoehen1><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<schrifthoehen2><xsl:copy-of select="$schrifthoehen2"/></schrifthoehen2><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<hoehen><xsl:copy-of select="$p_anzahl_hoehen"/></hoehen><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<links><xsl:copy-of select="$p_anzahl_links"/></links><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<inschriften><xsl:copy-of select="$anzahl_inschriften"/></inschriften>
</parametertest><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
-->

    <!-- formatierung der schrifthöhenzeile für den Druck -->
    <schrifthoehen>
       <!-- <xsl:attribute name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:attribute>-->
        <!-- buchstaben -->
        <xsl:if test="$schrifthoehen2/schrifthoehe_buchstaben">
            <xsl:choose>
                <xsl:when test="$db='inscriptiones_estoniae'">
                    <xsl:text>Tä </xsl:text>
                </xsl:when>
                <xsl:otherwise><xsl:text>Bu. </xsl:text></xsl:otherwise>
            </xsl:choose>
          <xsl:for-each select="$schrifthoehen2/schrifthoehe_buchstaben">
              <xsl:value-of select="wert"/><xsl:text> cm</xsl:text>
              <!-- bei mehr als einer inschrift werden die nummern in klammern ausgegeben -->
              <xsl:if test="$p_anzahl_hoehen != 1 or $anzahl_inschriften != $p_anzahl_links">
              <xsl:text> (</xsl:text><xsl:copy-of select=".//links"/><xsl:text>)</xsl:text>
              </xsl:if>
              <xsl:if test="following-sibling::schrifthoehe_buchstaben"><xsl:text>, </xsl:text></xsl:if>
        </xsl:for-each>
      </xsl:if>

        <!-- ziffern -->
        <xsl:if test="$schrifthoehen2/schrifthoehe_ziffern">
            <!-- wenn buchstabenhöhen vorangehen, wird ein semikolon gesetzt-->
            <xsl:if test="$schrifthoehen2/schrifthoehe_buchstaben"><xsl:text>; </xsl:text></xsl:if>
            <xsl:choose>
                <xsl:when test="$db='inscriptiones_estoniae'">
                    <xsl:text>Tä </xsl:text>
                </xsl:when>
                <xsl:otherwise><xsl:text>Zi. </xsl:text></xsl:otherwise>
            </xsl:choose>
            <xsl:for-each select="$schrifthoehen2/schrifthoehe_ziffern">
                <xsl:value-of select="wert"/><xsl:text> cm</xsl:text>
                <!-- bei mehr als einer inschrift werden die nummern in klammern ausgegeben -->
                <xsl:if test="$p_anzahl_hoehen != 1 or $anzahl_inschriften != $p_anzahl_links">
                    <xsl:text> (</xsl:text><xsl:copy-of select=".//links"/><xsl:text>)</xsl:text>
                </xsl:if>
                <xsl:if test="following-sibling::schrifthoehe_ziffern"><xsl:text>, </xsl:text></xsl:if>
            </xsl:for-each>
        </xsl:if>
        <!-- am schluss wird ein punkt gesetzt, wenn schrifthöhen vorliegen -->
        <xsl:if test="$schrifthoehen2/*"><xsl:text>.</xsl:text></xsl:if>
    </schrifthoehen>
</xsl:template>

</xsl:stylesheet>
