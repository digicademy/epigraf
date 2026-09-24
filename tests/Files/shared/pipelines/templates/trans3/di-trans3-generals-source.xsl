<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
    
    <!-- in diesem stylesheet wird zusammengetragen und formatiert, 
        ob und welche nicht mehr erhaltene inschriften 
        nach welcher vorlage aufgenommen wurden
    
    es wird aufgerufen in di-trans3-articles.xsl
    
    
    --> 

    <xsl:import href="../commons/di-switch.xsl"/> 

    <xsl:template name="generals_source">
        <xsl:param name="p_db"></xsl:param>
        <xsl:param name="p_projectlanguage"></xsl:param>
        <!-- anzahl der inschriften des artikels-->
        <xsl:param name="p_anzahl_inschriften_total"><xsl:value-of select="count(sections/section[@sectiontype='inscription'])" /></xsl:param>
        
        <!-- anzahl der inschriftenteile des artikels-->
        <xsl:param name="p_anzahl_teile_total"><xsl:value-of select="count(.//section[@sectiontype='inscriptionpart'])" /></xsl:param>
        
        <!-- anzahl aller bearbeitungen des artikels-->
        <xsl:param name="p_anzahl_bearbeitungen_total"><xsl:value-of select="count(.//section[@sectiontype='inscriptiontext'])" /></xsl:param>
        
        <!-- anzahl der bearbeitungen nach kopial vorlage  -->
        <xsl:param name="p_anzahl_bearbeitungen_kopial"><xsl:value-of select="count(.//section[@sectiontype='inscriptiontext'][.//source_from[node()]])"/></xsl:param>
        
         <!-- anzahl der autopsierten bearbeitungen -->
        <xsl:param name="p_anzahl_bearbeitungen_autopsiert">
        <xsl:value-of select="$p_anzahl_bearbeitungen_total - $p_anzahl_bearbeitungen_kopial" />
        </xsl:param>
 
        
        <xsl:param name="p_vorlagen1">
            <!-- alle nicht autopsierten bearbeitungen auswählen:
        bearbeitungen, bei denen das feld "autopsie" nicht angeklickt ist und das feld "inschrift nach" nicht leer ist -->
            <xsl:for-each select=".//section[@sectiontype='inscriptiontext'][not(items/item/source_autopsy)][items/item/source_from[node()]]">
            <!-- vorlage in variable speichern -->
            <xsl:variable name="v_source" select="items/item/source_from" />
                <vorlage>
                    <!-- vorlage auslesen -->
                    <quelle>
                        <xsl:apply-templates select=".//source_from"/>
                    </quelle>
                    <!-- nummer auslesen, differenziert nach bearbeitung, inschriftteil, inschrift -->
                    <nr>
                        <!-- die id der bearbeitung wird als target_id für den späteren link auf die inschrift/bearbeitung mitgeführt -->
                        <xsl:attribute name="target_id" select="@id" />
                        <xsl:copy-of select="ancestor::section[@sectiontype='inscription']/@number"/>
                        <xsl:attribute name="subsectionnumber" select="ancestor::section[@sectiontype='inscriptionpart']/@number" />
                        <xsl:if test="preceding-sibling::section or following-sibling::section">
                            <nr_bearbeitung>
                                <!-- laufende nummern der bearbeitung -->
                                <xsl:attribute name="nr" select="@number" />
                                <!-- anzahl der bearbeitungen des inschriftenteils -->
                                <xsl:attribute name="von">
                                    <xsl:for-each select="parent::section">
                                        <xsl:value-of select="count(section)"/>
                                    </xsl:for-each>
                                </xsl:attribute>
                                <!-- bezeichner der bearbeitung -->
                                <xsl:number format="a" value="@number" />
                            </nr_bearbeitung>
                        </xsl:if>
                        <!-- zum übergeordneten inschriftenteil gehen -->
                        <xsl:for-each select="parent::section">
                            <!-- anzahl aller inschriftenteile ermitteln -->
                            <xsl:variable name="v_anzahl_sections_total"><xsl:for-each select="parent::section"><xsl:value-of select="count(section)"/></xsl:for-each></xsl:variable>
                            <!-- anzahl aller inschriftenteile mit ergänzung ermitteln -->
                            <xsl:variable name="v_anzahl_sections_kopial">
                                <xsl:for-each select="parent::section"><xsl:value-of select="count(section[.//source_from=$v_source])"/></xsl:for-each>
                            </xsl:variable>
<!--
<xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<sections_total><xsl:value-of select="$v_anzahl_sections_total"/></sections_total><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<sections_ergaenzt><xsl:value-of select="$v_anzahl_sections_ergaenzt"/></sections_ergaenzt><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->
                            <xsl:if test="$v_anzahl_sections_total != $v_anzahl_sections_kopial">
                                <nr_teil>
                                    <!-- laufende nummer des inschriftenteils -->
                                    <xsl:attribute name="nr">
                                        <xsl:value-of select="@number"/>
                                    </xsl:attribute>
                                    <!-- anzahl der inschriftenteile dieser inschrift -->
                                    <xsl:attribute name="von">
                                        <xsl:for-each select="parent::section">
                                            <xsl:value-of select="count(section)"/>
                                        </xsl:for-each>
                                    </xsl:attribute>
                                    <!-- bezeichner des inschriftenteils -->
                                    <xsl:value-of select="@name"/>
                                </nr_teil>
                            </xsl:if>
                            <!-- zur übergeordneten inschrift gehen -->
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
                                            <xsl:when test="$sw_modus='projects_bay'">
                                                <xsl:choose>
                                                    <xsl:when test="@number[string()]">
                                                        <xsl:number value="@number" format="I"></xsl:number>
                                                    </xsl:when>
                                                    <xsl:otherwise>NaN</xsl:otherwise>
                                                </xsl:choose>
                                            </xsl:when>
                                            <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                                        </xsl:choose>
                                    </nr_inschrift></xsl:if>
                            </xsl:for-each>
                        </xsl:for-each>
                    </nr>
                </vorlage>
            </xsl:for-each>
        </xsl:param>
        
        <!-- doubletten entfernen -->
        <xsl:param name="p_vorlagen2">
            <xsl:for-each select="$p_vorlagen1/vorlage[not(quelle=preceding-sibling::vorlage/quelle)][quelle/node()]">
                <vorlage><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:copy-of select="quelle"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- nummern in umgekehrter folge auslesen (A inschrift, 1 Teil, a bearbeitung) -->
                    <nrn><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <!-- nummernfolge des ersten treffers -->
                        <nr>
                            <!-- die target_id für den späteren link wird mitgeführt -->
                            <xsl:attribute name="target_id"><xsl:value-of select="nr/@target_id"/></xsl:attribute>
                            <xsl:copy-of select="nr/@number"/> 
                            <xsl:copy-of select="nr/@subsectionnumber"/>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="nr/*[3]"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="nr/*[2]"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="nr/*[1]"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </nr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <!-- nummern der doubletten -->
                        <xsl:for-each select="following-sibling::vorlage[quelle=current()/quelle]">
                            <nr>
                                <!-- die target_id für den späteren link wird mitgeführt -->
                                <xsl:attribute name="target_id"><xsl:value-of select="nr/@target_id"/></xsl:attribute>
                                <xsl:copy-of select="nr/@number"/>
                                <xsl:copy-of select="nr/@subsectionnumber"/>
                                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <xsl:copy-of select="nr/*[3]"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <xsl:copy-of select="nr/*[2]"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <xsl:copy-of select="nr/*[1]"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </nr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:for-each>
                    </nrn><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </vorlage><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </xsl:param>
        
        <xsl:param name="p_vorlagen3">
            <xsl:for-each select="$p_vorlagen2/vorlage[quelle[node()]]">
                <xsl:variable name="v_anzahl_vorlagen"><xsl:value-of select="count(../vorlage)"/></xsl:variable>
                <xsl:variable name="v_anzahl_nrn"><xsl:value-of select="count(nrn/nr)"/></xsl:variable>
                <vorlage>
                    <xsl:copy-of select="quelle"/>
                    <xsl:for-each select="nrn">
                        <nrn>
                            <xsl:for-each select="nr">
                                <nr>
                                    <xsl:attribute name="target_id"><xsl:value-of select="@target_id"/></xsl:attribute>
                                    <xsl:copy-of select="@number"/>
                                    <xsl:attribute name="nr_string"><xsl:for-each select="*"><xsl:value-of select="."/></xsl:for-each></xsl:attribute>
                                    <xsl:if test="nr_teil"><xsl:attribute name="is_part"><xsl:value-of select="@subsectionnumber"/></xsl:attribute></xsl:if>
                                    <!-- die target_id auf die inschrift wird übernommen -->
                                    <xsl:for-each select="*">          
                                        <xsl:copy-of select="."/>
                                    </xsl:for-each>
                                </nr>
                            </xsl:for-each>
                        </nrn>
                    </xsl:for-each>
                </vorlage>
            </xsl:for-each>
        </xsl:param>

        <xsl:param name="p_vorlagen4">
            <xsl:for-each select="$p_vorlagen3/vorlage">
                <xsl:variable name="v_anzahl_vorlagen"><xsl:value-of select="count(../vorlage)"/></xsl:variable>
                <xsl:variable name="v_anzahl_nrn"><xsl:value-of select="count(nrn/nr)"/></xsl:variable>
                <vorlage>
                    <xsl:copy-of select="quelle"/>
                    <xsl:for-each select="nrn">
                        <nrn>
                            <xsl:for-each select="nr[not(@nr_string=preceding-sibling::nr/@nr_string)]">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                        </nrn>
                     </xsl:for-each>
                </vorlage>
            </xsl:for-each>
        </xsl:param>
        
        <!-- zur kontrolle des ergebnisses wird der inhalt der paramter ausgegeben
        <vorlagentest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <inschriften><xsl:value-of select="$p_anzahl_inschriften_total"/></inschriften><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <teile><xsl:value-of select="$p_anzahl_teile_total"/></teile><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bearbeitungen><xsl:value-of select="$p_anzahl_bearbeitungen_total"/></bearbeitungen><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bearbeitungen_autopsie><xsl:value-of select="$p_anzahl_bearbeitungen_autopsiert"/></bearbeitungen_autopsie>
            <bearbeitungen_kopial><xsl:value-of select="$p_anzahl_bearbeitungen_kopial"/></bearbeitungen_kopial><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <vorlagen1><xsl:copy-of select="$p_vorlagen1"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></vorlagen1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <vorlagen2><xsl:copy-of select="$p_vorlagen2"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></vorlagen2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <vorlagen3><xsl:copy-of select="$p_vorlagen3"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></vorlagen3><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <vorlagen4><xsl:copy-of select="$p_vorlagen4"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></vorlagen4><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </vorlagentest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text> -->

        <xsl:choose>
            <!-- wenn dafür ein eigener abschnitt angelegt wurde, wird dieser angesteuert -->
            <xsl:when test="sections/section[@norm_iri='di_generals_source']">
                <di_generals_source><xsl:apply-templates select="sections/section[@norm_iri='di_generals_source']/items/item[@itemtype='text']/content"/></di_generals_source>
            </xsl:when>
            <!-- anderenfalls wird ein template aufgerufen -->
            <xsl:otherwise>
                <di_generals_source>
                    <xsl:for-each select="$p_vorlagen4/vorlage[quelle[node()]]">
                        <xsl:variable name="v_anzahl_vorlagen"><xsl:value-of select="count(../vorlage)"/></xsl:variable>
                        <xsl:variable name="v_anzahl_nrn"><xsl:value-of select="count(nrn/nr)"/></xsl:variable>
                        <xsl:choose>
                            
                            <!-- wenn alle bearbeitungen nach kopialer vorlage erfolgen und es nur eine vorlage gibt: ohne links -->
                            <xsl:when test="$p_anzahl_bearbeitungen_total=$p_anzahl_bearbeitungen_kopial and 
                                $v_anzahl_vorlagen = 1 ">
                                <xsl:choose>
                                    <!-- estnisch -->
                                    <xsl:when test="$p_projectlanguage='et'">
                                        <xsl:text>Pealiskirja</xsl:text><xsl:if test="$v_anzahl_nrn &gt; 1">de</xsl:if> allikas <xsl:value-of select="quelle"/><xsl:text>.</xsl:text>                                
                                    </xsl:when>
                                    <!-- deutsch -->
                                    <xsl:otherwise>
                                        <xsl:text>Inschrift</xsl:text><xsl:if test="$v_anzahl_nrn &gt; 1">en</xsl:if> nach <xsl:value-of select="quelle"/><xsl:text>.</xsl:text>                             
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:when>
                            <!-- wenn nur einzelne bearbeitungen nach vorlage erfolgen oder wenn es mehrere vorlagen gibt: mit links  -->
                            <xsl:otherwise>
                                <xsl:if test="not($v_anzahl_nrn = 1 and $p_anzahl_teile_total = 1)">
                                    <xsl:choose>
                                        <!-- estnisch -->
                                        <xsl:when test="$p_projectlanguage='et'">
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
                                            <link type="inscription">
                                                <!-- die target_id auf die inschrift wird übernommen -->
                                                <xsl:attribute name="target_id" select="@target_id" />
                                                <xsl:copy-of select="@number"/>
                                                <xsl:copy-of select="@is_part"/>
                                                <!--<xsl:if test="nr_teil"><xsl:attribute name="is_part"><xsl:value-of select="@subsectionnumber"/></xsl:attribute></xsl:if>-->
                                                
                                                <xsl:for-each select="*">
                                                    <xsl:choose>
                                                         <!-- die nummer der bearbeitung wird extra getagt damit sie bei der ausgabe hochgestellt und unterstrichen werden kann  -->
                                                         <xsl:when test="name()='nr_bearbeitung'"><version-nr><xsl:value-of select="."/></version-nr></xsl:when>
                                                        <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
                                                     </xsl:choose>
                                                </xsl:for-each>
                                                
                                            </link>
                                        </xsl:for-each>
                                    </links>
                                </xsl:for-each>
                                
                                <xsl:choose>
                                    <xsl:when test="$p_projectlanguage='et'">
                                        <xsl:text> allikas </xsl:text> 
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:text> nach </xsl:text> 
                                    </xsl:otherwise>
                                </xsl:choose>                        
                                <xsl:value-of select="quelle"/><xsl:if test="following-sibling::vorlage"><xsl:text>, </xsl:text></xsl:if><xsl:if test="not(following-sibling::vorlage)"><xsl:text>.</xsl:text></xsl:if>
                            </xsl:otherwise>
                        </xsl:choose>
                        <!--                <variablentest>
                    <anzahl_vorlagen><xsl:value-of select="$p_anzahl_vorlagen"/></anzahl_vorlagen>
                    <anzahl_nrn><xsl:value-of select="$p_anzahl_nrn"/></anzahl_nrn>
                </variablentest>-->
                    </xsl:for-each>
                </di_generals_source>                
            </xsl:otherwise>
        </xsl:choose>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
</xsl:stylesheet>