<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>
<xsl:import href="../commons/epi-switch.xsl"/>

    <xsl:template name="ergaenzung_nach">
        <xsl:param name="db"></xsl:param>
        <xsl:param name="projectlanguage"></xsl:param>
        <!-- anzahl der inschriften des artikels-->
        <xsl:param name="anzahl_inschriften_total"><xsl:value-of select="count(sections/section[@sectiontype='inscription'])" /></xsl:param>

        <!-- anzahl der inschriftenteile des artikels-->
        <xsl:param name="anzahl_teile_total"><xsl:value-of select="count(.//section[@sectiontype='inscriptionpart'])" /></xsl:param>

        <!-- anzahl der bearbeitungen des artikels-->
        <xsl:param name="anzahl_bearbeitungen_total"><xsl:value-of select="count(.//section[@sectiontype='inscriptiontext'])" /></xsl:param>

        <!-- anzahl bearbeitungen mit ergänzung -->
        <xsl:param name="anzahl_bearbeitungen_ergaenzt"><xsl:value-of select="count(.//section[@sectiontype='inscriptiontext'][.//source_addition[node()]])" /></xsl:param>

        <xsl:param name="ergaenzungen1">
            <!-- jede ergänzte bearbeitungen ansteuern -->
            <xsl:for-each select=".//section[@sectiontype='inscriptiontext'][items/item/source_addition[node()]]">
            <!-- vorlage in variable speichern -->
            <xsl:variable name="v_source"><xsl:value-of select="items/item/source_addition"/></xsl:variable>
                <ergaenzung>
                    <!-- die vorlage für die ergänzung auslesen -->
                    <quelle>
                        <xsl:apply-templates select=".//source_addition"/>
                    </quelle>
                    <!-- nummer auslesen, differenziert nach bearbeitung, inschriftteil, inschrift -->
                    <nr>
                        <!-- die id der bearbeitung wird als targetID für den späteren link auf die inschrift/bearbeitung mitgeführt -->
                        <xsl:attribute name="targetID"><xsl:value-of select="@id"/></xsl:attribute>
                        <xsl:copy-of select="ancestor::section[@sectiontype='inscription']/@number"/>
                        <xsl:if test="preceding-sibling::section or following-sibling::section">
                            <nr_bearbeitung>
                                <!-- laufende nummer der bearbeitung -->
                                <xsl:attribute name="nr">
                                    <xsl:value-of select="@number"/>
                                </xsl:attribute>
                                <!-- anzahl der bearbeitungen -->
                                <xsl:attribute name="von">
                                    <xsl:for-each select="parent::section">
                                        <xsl:value-of select="count(section)"/>
                                    </xsl:for-each>
                                </xsl:attribute>
                                <xsl:number format="a" value="@number"/>
                            </nr_bearbeitung>
                        </xsl:if>
                        <!-- zum übergeordneten inschriftenteil gehen -->
                        <xsl:for-each select="parent::section">
                            <!-- anzahl aller inschriftenteile ermitteln -->
                            <xsl:variable name="v_anzahl_sections_total"><xsl:for-each select="parent::section"><xsl:value-of select="count(section)"/></xsl:for-each></xsl:variable>
                            <!-- anzahl aller inschriftenteile mit ergänzung ermitteln -->
                            <xsl:variable name="v_anzahl_sections_ergaenzt">
                                <xsl:for-each select="parent::section"><xsl:value-of select="count(section[.//source_addition=$v_source])"/></xsl:for-each>
                            </xsl:variable>

                            <xsl:if test="$v_anzahl_sections_total != $v_anzahl_sections_ergaenzt">
                                <nr_teil>
                                    <!-- laufende nummer des inschriftenteils -->
                                    <xsl:attribute name="nr">
                                        <xsl:value-of select="@number"/>
                                    </xsl:attribute>
                                    <!-- anzahl der inschriftenteile -->
                                    <xsl:attribute name="von">
                                        <xsl:for-each select="parent::section">
                                            <xsl:value-of select="count(section)"/>
                                        </xsl:for-each>
                                    </xsl:attribute>
                                    <!-- bezeichner des inschriftenteils -->
                                    <xsl:value-of select="@name"/>
                                </nr_teil>
                            </xsl:if>
                            <!-- zur übergeordneten inschriften gehen -->
                            <xsl:for-each select="parent::section">
                                <xsl:if test="preceding-sibling::section or following-sibling::section">
                                    <nr_inschrift>
                                        <!-- laufende nummer der inschrift -->
                                        <xsl:attribute name="nr">
                                            <xsl:value-of select="@number"/>
                                        </xsl:attribute>
                                        <!-- anzahl der inschriften im artikel -->
                                        <xsl:attribute name="von">
                                            <xsl:for-each select="ancestor::article">
                                                <xsl:value-of select="count(section[@sectiontype='inscription'])"/>
                                            </xsl:for-each>
                                        </xsl:attribute>
                                        <!-- bezeichner der inschrift -->
                                        <xsl:choose>
                                            <xsl:when test="$modus='projects_bay'"><xsl:number value="@number" format="I"/></xsl:when>
                                            <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                                        </xsl:choose>
                                    </nr_inschrift></xsl:if>
                            </xsl:for-each>
                        </xsl:for-each>
                    </nr>
                </ergaenzung>
            </xsl:for-each>
        </xsl:param>
        <!-- doubletten entfernen -->
        <xsl:param name="ergaenzungen2">
            <xsl:for-each select="$ergaenzungen1/ergaenzung[not(quelle=preceding-sibling::ergaenzung/quelle)][quelle/node()]">
                <ergaenzung><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                    <xsl:copy-of select="quelle"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                    <!-- nummern in umgekehrter folge auslesen (muster): Inschrift (A), Teil (1), Bearbeitung (a) -->
                    <nrn><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                        <!-- nummernfolge des ersten treffers -->
                        <nr>
                            <!-- die targetID für den späteren link wird mitgeführt -->
                            <xsl:attribute name="targetID"><xsl:value-of select="nr/@targetID"/></xsl:attribute>
                            <xsl:copy-of select="nr/@number"/>
                            <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                            <xsl:copy-of select="nr/*[3]"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                            <xsl:copy-of select="nr/*[2]"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                            <xsl:copy-of select="nr/*[1]"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                        </nr><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                        <!-- nummern der doubletten -->
                        <xsl:for-each select="following-sibling::ergaenzung[quelle=current()/quelle]">
                            <nr>
                                <!-- die targetID für den späteren link wird mitgeführt -->
                                <xsl:attribute name="targetID"><xsl:value-of select="nr/@targetID"/></xsl:attribute>
                                <xsl:copy-of select="nr/@number"/>
                                <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                                <xsl:copy-of select="nr/*[3]"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                                <xsl:copy-of select="nr/*[2]"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                                <xsl:copy-of select="nr/*[1]"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                            </nr><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                        </xsl:for-each>
                    </nrn><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                </ergaenzung><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            </xsl:for-each>
        </xsl:param>

<xsl:param name="ergaenzungen3">
            <xsl:for-each select="$ergaenzungen2/ergaenzung[quelle[node()]]">
                <xsl:variable name="anzahl_vorlagen"><xsl:value-of select="count(../ergaenzung)"/></xsl:variable>
                <xsl:variable name="anzahl_nrn"><xsl:value-of select="count(nrn/nr)"/></xsl:variable>
                <ergaenzung>
                <xsl:copy-of select="quelle"/>
                <xsl:for-each select="nrn">
                        <nrn>
                                <xsl:for-each select="nr">
                                    <nr>
                                        <xsl:attribute name="target_id"><xsl:value-of select="@targetID"/></xsl:attribute>
                                        <xsl:copy-of select="@number"/>
                                        <xsl:attribute name="nr_string"><xsl:for-each select="*"><xsl:value-of select="."/></xsl:for-each></xsl:attribute>
                                        <xsl:if test="nr_teil"><xsl:attribute name="is_part"><xsl:value-of select="@subsectionnumber"/></xsl:attribute></xsl:if>
                                        <!-- die targetID auf die inschrift wird übernommen -->
                                        <xsl:for-each select="*">
                                            <xsl:choose>
                                                <!-- die nummer der bearbeitung wird extra getagt damit sie bei der ausgabe hochgestellt und unterstrichen werden kann  -->
                                                <xsl:when test="name()='nr_bearbeitung'"><su><xsl:value-of select="."/></su></xsl:when>
                                                <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
                                            </xsl:choose>
                                        </xsl:for-each>
                                    </nr>
                                </xsl:for-each>
                            </nrn>
                        </xsl:for-each>
                    </ergaenzung>
            </xsl:for-each>
        </xsl:param>

        <xsl:param name="ergaenzungen4">
            <xsl:for-each select="$ergaenzungen3/ergaenzung">
                <xsl:variable name="anzahl_vorlagen"><xsl:value-of select="count(../ergaenzung)"/></xsl:variable>
                <xsl:variable name="anzahl_nrn"><xsl:value-of select="count(nrn/nr)"/></xsl:variable>
                <ergaenzung>
                <xsl:copy-of select="quelle"/>
                <xsl:for-each select="nrn">
                        <nrn>
                                <xsl:for-each select="nr[not(@nr_string=preceding-sibling::nr/@nr_string)]">
                                    <xsl:copy-of select="."/>
                                </xsl:for-each>
                            </nrn>
                        </xsl:for-each>
                </ergaenzung>
            </xsl:for-each>
        </xsl:param>

        <!-- zur kontrolle des ergebnisses wird der inhalt des zweiten parameters ausgegeben
        <ergaenzungen_test2><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <inschriften><xsl:value-of select="$anzahl_inschriften_total"/></inschriften><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <teile><xsl:value-of select="$anzahl_teile_total"/></teile><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <bearbeitungen><xsl:value-of select="$anzahl_bearbeitungen_total"/></bearbeitungen><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <ergaenzungen><xsl:value-of select="$anzahl_bearbeitungen_ergaenzt"/></ergaenzungen><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <erg1><xsl:copy-of select="$ergaenzungen1"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text></erg1>
            <erg2><xsl:copy-of select="$ergaenzungen2"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text></erg2>
            <erg3><xsl:copy-of select="$ergaenzungen3"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text></erg3>
            <erg4><xsl:copy-of select="$ergaenzungen4"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text></erg4>
        </ergaenzungen_test2><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text> -->

        <!-- vorlagentext zusammenstellen für den druck-->
        <ergaenzung_nach>
            <xsl:for-each select="$ergaenzungen4/ergaenzung[quelle[node()]]">
                <xsl:variable name="anzahl_vorlagen"><xsl:value-of select="count(../ergaenzung)"/></xsl:variable>
                <xsl:variable name="anzahl_nrn"><xsl:value-of select="count(nrn/nr)"/></xsl:variable>
<!--
<variablentest>
<vorlagen><xsl:value-of select="$anzahl_vorlagen"/></vorlagen>
<nrn><xsl:value-of select="$anzahl_nrn"/></nrn>
</variablentest>
-->
                <xsl:choose>
                    <!-- wenn es nur eine inschrift mit einem inschriftenteil und einer bearbeitung gibt,
                    werden die bezeichner der inschrift (A), des teils (1) und der bearbeitung (a) unterdrückt -->
                    <xsl:when test="$anzahl_bearbeitungen_total=1">
                        <xsl:choose>
                            <!-- estnisch -->
                            <xsl:when test="$projectlanguage='et'">
                                <xsl:text>Pealiskirja täiendatud allikas </xsl:text><xsl:value-of select="quelle"/><xsl:text>.</xsl:text>
                            </xsl:when>
                            <!-- deutsch -->
                            <xsl:otherwise>
                                <xsl:text>Inschrift ergänzt nach </xsl:text><xsl:value-of select="quelle"/><xsl:text>.</xsl:text>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:when>
                    <!-- wenn alle bearbeitungen nach kopialer vorlage erfolgen und es nur eine vorlage gibt: ohne links -->
                    <xsl:when test="$anzahl_bearbeitungen_total=$anzahl_bearbeitungen_ergaenzt and
                                    $anzahl_vorlagen = 1 ">
                        <xsl:choose>
                            <!-- estnisch -->
                            <xsl:when test="$projectlanguage='et'">
                                <xsl:text>Pealiskirja</xsl:text><xsl:if test="$anzahl_nrn &gt; 1">de</xsl:if><xsl:text> täiendatud allikas </xsl:text><xsl:value-of select="quelle"/><xsl:text>.</xsl:text>
                            </xsl:when>
                            <!-- deutsch -->
                            <xsl:otherwise>
                                <xsl:text>Inschrift</xsl:text><xsl:if test="$anzahl_nrn &gt; 1">en</xsl:if><xsl:text> ergänzt nach </xsl:text><xsl:value-of select="quelle"/><xsl:text>.</xsl:text>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:when>
                    <!-- wenn nur einzelne bearbeitungen nach vorlage erfolgen oder wenn es mehrere vorlagen gibt: mit links  -->
                    <xsl:otherwise>
                        <xsl:if test="not($anzahl_nrn = 1 and $anzahl_teile_total = 1)">
                        <xsl:choose>
                            <!-- estnisch -->
                            <xsl:when test="$projectlanguage='et'">
                                <xsl:text>Pealiskirja</xsl:text><xsl:if test="nrn/nr[following-sibling::nr]">de</xsl:if><xsl:text> </xsl:text>
                            </xsl:when>
                            <!-- deutsch -->
                            <xsl:otherwise>
                                <xsl:text>Inschrift</xsl:text><xsl:if test="nrn/nr[following-sibling::nr]">en</xsl:if><xsl:text> </xsl:text>
                            </xsl:otherwise>
                        </xsl:choose>
                        </xsl:if>

                        <xsl:for-each select="nrn">
                        <links>
                                <xsl:for-each select="nr">
                                    <link type="inschrift">
                                        <xsl:attribute name="target_id"><xsl:value-of select="@targetID"/></xsl:attribute>
                                        <xsl:copy-of select="@number"/>
                                        <xsl:copy-of select="@is_part"/>
                                            <xsl:choose>
                                                <!-- die nummer der bearbeitung wird extra getagt damit sie bei der ausgabe hochgestellt und unterstrichen werden kann  -->
                                                <xsl:when test="name()='nr_bearbeitung'"><su><xsl:value-of select="."/></su></xsl:when>
                                                <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
                                            </xsl:choose>

                                    </link>
                                </xsl:for-each>
                            </links>
                        </xsl:for-each>

                        <xsl:choose>
                            <!-- estnisch -->
                            <xsl:when test="$projectlanguage='et'">
                                <xsl:text> täiendatud allikas </xsl:text><xsl:value-of select="quelle"/><xsl:if test="following-sibling::ergaenzung"><xsl:text>, </xsl:text></xsl:if><xsl:if test="not(following-sibling::ergaenzung)"><xsl:text>.</xsl:text></xsl:if>
                            </xsl:when>
                            <!-- deutsch -->
                            <xsl:otherwise>
                                <xsl:text> ergänzt nach </xsl:text><xsl:value-of select="quelle"/><xsl:if test="following-sibling::ergaenzung"><xsl:text>, </xsl:text></xsl:if><xsl:if test="not(following-sibling::ergaenzung)"><xsl:text>.</xsl:text></xsl:if>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </ergaenzung_nach>
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    </xsl:template>

</xsl:stylesheet>
