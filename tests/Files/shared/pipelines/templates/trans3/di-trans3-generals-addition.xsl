<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
>
<xsl:import href="../commons/di-switch.xsl"/> 
    
<!-- in diesem stylesheet wird zusammengetragen und formatiert, 
        ob und welche unvollständig erhaltene inschriften 
        nach welcher vorlage ergänzt wurden
    
    es wird aufgerufen in di-trans3-articles.xsl
    
    
    -->    

    <xsl:template name="generals_addition">
        <xsl:param name="p_projectlanguage" />
        
        <!-- anzahl der inschriften des artikels-->
        <xsl:param name="p_anzahl_inschriften_total" select="count(sections/section[@sectiontype='inscription'])" />
        
        <!-- anzahl der inschriftenteile des artikels-->
        <xsl:param name="p_anzahl_teile_total" select="count(.//section[@sectiontype='inscriptionpart'])" />
        
        <!-- anzahl der bearbeitungen des artikels-->
        <xsl:param name="p_anzahl_bearbeitungen_total" select="count(.//section[@sectiontype='inscriptiontext'])" />
        
        <!-- anzahl bearbeitungen mit ergänzung -->
        <xsl:param name="p_anzahl_bearbeitungen_ergaenzt" select="count(.//section[@sectiontype='inscriptiontext'][.//source_addition[node()]])" />
        
        <xsl:param name="p_ergaenzungen1">
            
            <!-- jede bearbeitungen die ein element ergänzung (<source_addition>)enthält ansteuern -->
            <xsl:for-each select=".//section[@sectiontype='inscriptiontext'][items/item/source_addition[node()]]">
            
                <!-- vorlage in variable speichern -->
                <xsl:variable name="v_source" select="items/item/source_addition" />
                
                <ergaenzung>
                    <!-- die vorlage für die ergänzung auslesen -->
                    <quelle>
                        <xsl:apply-templates select=".//source_addition" />
                    </quelle>
                    <!-- nummer auslesen, differenziert nach bearbeitung, inschriftteil, inschrift -->
                    <nr>
                        <!-- die id der bearbeitung wird als target_id für den späteren link auf die inschrift/bearbeitung mitgeführt -->
                        <xsl:attribute name="target_id" select="@id" />
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
                            <xsl:variable name="v_anzahl_sections_total">
                                <xsl:for-each select="parent::section">
                                    <xsl:value-of select="count(section)"/></xsl:for-each>
                            </xsl:variable>
                                
                            <!-- anzahl aller inschriftenteile mit ergänzung ermitteln -->
                            <xsl:variable name="v_anzahl_sections_ergaenzt">
                                <xsl:for-each select="parent::section">
                                    <xsl:value-of select="count(section[.//source_addition=$v_source])"/>
                                </xsl:for-each>
                            </xsl:variable>

                            <xsl:if test="$v_anzahl_sections_total != $v_anzahl_sections_ergaenzt">
                                <nr_teil>
                                    <!-- laufende nummer des inschriftenteils -->
                                    <xsl:attribute name="nr" select="@number" />

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
                </ergaenzung>
            </xsl:for-each>
        </xsl:param>
        
        <!-- doubletten entfernen -->
        <xsl:param name="p_ergaenzungen2">
            <xsl:for-each select="$p_ergaenzungen1/ergaenzung[not(quelle=preceding-sibling::ergaenzung/quelle)][quelle/node()]">
                <ergaenzung>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:copy-of select="quelle"/>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    
                    <!-- nummern in umgekehrter folge auslesen (muster): Inschrift (A), Teil (1), Bearbeitung (a) -->
                    <nrn><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <!-- nummernfolge des ersten treffers -->
                        <nr>
                            <!-- die target_id für den späteren link wird mitgeführt -->
                            <xsl:attribute name="target_id"><xsl:value-of select="nr/@target_id"/></xsl:attribute>
                            <xsl:copy-of select="nr/@number"/>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="nr/*[3]"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="nr/*[2]"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="nr/*[1]"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </nr>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        
                        <!-- nummern der doubletten -->
                        <xsl:for-each select="following-sibling::ergaenzung[quelle=current()/quelle]">
                            <nr>
                                <!-- die target_id für den späteren link wird mitgeführt -->
                                <xsl:attribute name="target_id"><xsl:value-of select="nr/@target_id"/></xsl:attribute>
                                <xsl:copy-of select="nr/@number"/>
                                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <xsl:copy-of select="nr/*[3]"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <xsl:copy-of select="nr/*[2]"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <xsl:copy-of select="nr/*[1]"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </nr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:for-each>
                    </nrn>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </ergaenzung>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </xsl:param>

        <xsl:param name="p_ergaenzungen3">
            <xsl:for-each select="$p_ergaenzungen2/ergaenzung[quelle[node()]]">
                <xsl:variable name="v_anzahl_vorlagen"><xsl:value-of select="count(../ergaenzung)"/></xsl:variable>
                <xsl:variable name="v_anzahl_nrn"><xsl:value-of select="count(nrn/nr)"/></xsl:variable>
                <ergaenzung>
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

        <xsl:param name="p_ergaenzungen4">
            <xsl:for-each select="$p_ergaenzungen3/ergaenzung">
                <xsl:variable name="v_anzahl_vorlagen"><xsl:value-of select="count(../ergaenzung)"/></xsl:variable>
                <xsl:variable name="v_anzahl_nrn"><xsl:value-of select="count(nrn/nr)"/></xsl:variable>
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
        <ergaenzungen_test2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <inschriften><xsl:value-of select="$p_anzahl_inschriften_total"/></inschriften><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <teile><xsl:value-of select="$p_anzahl_teile_total"/></teile><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bearbeitungen><xsl:value-of select="$p_anzahl_bearbeitungen_total"/></bearbeitungen><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <ergaenzungen><xsl:value-of select="$p_anzahl_bearbeitungen_ergaenzt"/></ergaenzungen><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <erg1><xsl:copy-of select="$p_ergaenzungen1"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></erg1>           
            <erg2><xsl:copy-of select="$p_ergaenzungen2"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></erg2>           
            <erg3><xsl:copy-of select="$p_ergaenzungen3"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></erg3>
            <erg4><xsl:copy-of select="$p_ergaenzungen4"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></erg4>
        </ergaenzungen_test2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text> -->

        <!-- vorlagentext zusammenstellen für den druck-->
        <di_generals_addition>
            <xsl:for-each select="$p_ergaenzungen4/ergaenzung[quelle[node()]]">
                <xsl:variable name="v_anzahl_vorlagen"><xsl:value-of select="count(../ergaenzung)"/></xsl:variable>
                <xsl:variable name="v_anzahl_nrn"><xsl:value-of select="count(nrn/nr)"/></xsl:variable>
                
                <!--
                <variablentest>
                <vorlagen><xsl:value-of select="$p_anzahl_vorlagen"/></vorlagen>
                <nrn><xsl:value-of select="$p_anzahl_nrn"/></nrn>
                </variablentest>
                -->

                <xsl:choose>
                    
                    <!-- wenn dafür ein eigener abschnitt angelegt wurde, wird dieser angesteuert -->
                    <xsl:when test="sections/section[@norm_iri='di_generals_addition']">
                        <di_generals_addition><xsl:apply-templates select="sections/section[@norm_iri='di_generals_addition']/items/item[@itemtype='chapter']/content"/></di_generals_addition>
                    </xsl:when>
                    
                    <!-- anderenfalls wird ein template aufgerufen -->
                    <xsl:otherwise>
                        <xsl:choose>
                            <!-- 
                                wenn es nur eine inschrift mit einem inschriftenteil und einer bearbeitung gibt,
                                werden die bezeichner der inschrift (A), des teils (1) und der bearbeitung (a) unterdrückt 
                            -->
                            <xsl:when test="$p_anzahl_bearbeitungen_total=1">
                                <xsl:choose>
                                    <!-- TODO: Nicht hart kodieren -->
                                    <!-- estnisch -->
                                    <xsl:when test="$p_projectlanguage='et'">
                                        <xsl:text>Pealiskirja täiendatud allikas </xsl:text><xsl:value-of select="quelle"/><xsl:text>.</xsl:text>                               
                                    </xsl:when>
                                    <!-- deutsch -->
                                    <xsl:otherwise>
                                        <xsl:text>Inschrift ergänzt nach </xsl:text><xsl:value-of select="quelle"/><xsl:text>.</xsl:text>                             
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:when>
                            
                            <!-- wenn alle bearbeitungen nach kopialer vorlage erfolgen und es nur eine vorlage gibt: ohne links -->
                            <xsl:when test="$p_anzahl_bearbeitungen_total=$p_anzahl_bearbeitungen_ergaenzt and 
                                $v_anzahl_vorlagen = 1 ">
                                <xsl:choose>
                                    <!-- TODO: Nicht hart kodieren -->
                                    <!-- estnisch -->
                                    <xsl:when test="$p_projectlanguage='et'">
                                        <xsl:text>Pealiskirja</xsl:text><xsl:if test="$v_anzahl_nrn &gt; 1">de</xsl:if><xsl:text> täiendatud allikas </xsl:text><xsl:value-of select="quelle"/><xsl:text>.</xsl:text>                                
                                    </xsl:when>
                                    <!-- deutsch -->
                                    <xsl:otherwise>
                                        <xsl:text>Inschrift</xsl:text><xsl:if test="$v_anzahl_nrn &gt; 1">en</xsl:if><xsl:text> ergänzt nach </xsl:text><xsl:value-of select="quelle"/><xsl:text>.</xsl:text>                             
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:when>
                            
                            <!-- wenn nur einzelne bearbeitungen nach vorlage erfolgen oder wenn es mehrere vorlagen gibt: mit links  -->
                            <xsl:otherwise>
                                <xsl:if test="not($v_anzahl_nrn = 1 and $p_anzahl_teile_total = 1)">
                                    <xsl:choose>
                                        <!-- TODO: Nicht hart kodieren -->
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
                                                <xsl:attribute name="target_id"><xsl:value-of select="@target_id"/></xsl:attribute>
                                                <xsl:copy-of select="@number"/>
                                                <xsl:copy-of select="@is_part"/>
                                                <xsl:choose>
                                                    <!-- die nummer der bearbeitung/version wird extra getagt damit sie bei der ausgabe hochgestellt und unterstrichen werden kann  -->
                                                    <xsl:when test="name()='nr_bearbeitung'"><version-nr><xsl:value-of select="."/></version-nr></xsl:when>
                                                    <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
                                                </xsl:choose>
                                                
                                            </link>
                                        </xsl:for-each>
                                    </links>
                                </xsl:for-each>
                                
                                <xsl:choose>
                                    <!-- estnisch -->
                                    <xsl:when test="$p_projectlanguage='et'">
                                        <xsl:text> täiendatud allikas </xsl:text><xsl:value-of select="quelle"/><xsl:if test="following-sibling::ergaenzung"><xsl:text>, </xsl:text></xsl:if><xsl:if test="not(following-sibling::ergaenzung)"><xsl:text>.</xsl:text></xsl:if>                             
                                    </xsl:when>
                                    <!-- deutsch -->
                                    <xsl:otherwise>
                                        <xsl:text> ergänzt nach </xsl:text><xsl:value-of select="quelle"/><xsl:if test="following-sibling::ergaenzung"><xsl:text>, </xsl:text></xsl:if><xsl:if test="not(following-sibling::ergaenzung)"><xsl:text>.</xsl:text></xsl:if>                             
                                    </xsl:otherwise>
                                </xsl:choose>                      
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </di_generals_addition>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

</xsl:stylesheet>