<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans1-index-locations.xsl"/>
    <xsl:import href="di-trans1-index-texttypes.xsl"/>
    <xsl:import href="di-trans1-commons.xsl"/>
    
<!-- dieses stylesheet enthält templates, die in verschiedenen anderen stylesheet zwecks
    erzeugung bzw. transformation von registern aufgerufen werden:
    - jeden index neu anlegen
    - die darin enthaltenen items hierarchisieren
    - verweis-start-elemente erzeugen
    - verweis-ziele darin hinzufügen
    
    außerdem muster für die elemente:
    - <lemma>
    - <name> (betrifft nur das wappenregister)
    -->

<!-- allemeines muster zur restrukturierung von registern, der fokus wurde zuvor auf <index> geführt -->
    <xsl:template name="index-restruct">
        <!-- die registereinträge einsschließlich der querverweise 
            werden in den rohdaten durch das element <property> repräsentiert.
            
            mit diesem template werden die elemente <property> zu 
            <item> für die eigentlichen registereinträge und 
            zu <crossRef> für verweise umgeformt.
            
            die in den rohdaten nur flach angeordneten elemente 
            werden dem attribut @level folgend hierarchisch verschachtelt
        -->
        
        <!-- 
            der unterschied zwischen item und verweis in den rohdaten liegt im attribut @related_id 
            bei items ist der attributwert leer (@related_id=''), 
            bei verweisen besteht er aus der id des ziel-items
        -->
        
        <!-- den namen des registers aus dem attribut @propertytype ermitteln und einem parameter übergeben  -->    
        <xsl:param name="p_index-name"><xsl:value-of select="@propertytype"/></xsl:param>

        <!-- register neu anlegen -->
        
        <!-- die registereinträge (property) der obersten ebenen werden angesteuert -->
        <!-- im register worttrenner wird der eintrag 'unbestimmt' - in EpiWeb markiert 
            mit 'im Register ignorieren' (@ishidden='1') - zurückgehalten -->
        <!-- TODO: Kann @ishidden generalisiert werden? Sonderfall: Bei Wappen im Register keine Wirkung, aber für Wappenbeschreibungen -->
        <xsl:for-each select="properties/property[@level='0']
            [not(@propertytype='wordseparators' and @ishidden='1')]
        ">
            
            <!-- der wert des attributs @related_id wird in einer variablen gespeichert -->
            <!-- der wert des attributs @id wird in einer variablen gespeichert -->
            <!-- für die verwendung im markenregister wird die @id des markentyps in einer variablen gespeichert -->            
            <xsl:variable name="v_related_id" select="@related_id" />                           
            <xsl:variable name="v_property_id" select="@id" />                            
            <xsl:variable name="v_brandtype_linkID" select="sections/section/@id" />
            
            <!-- parametertest
                <test-markentyp-id><xsl:value-of select="$v_brandtype_linkID"/></test>
            -->

            <xsl:variable name="isNotCrossRef" select="@related_id='' or not(@related_id)" />
            <xsl:variable name="isEmpty" select="not(sections or ancestor::properties/property[@parent_id = $v_property_id])" />

            <!-- elemente <item> und <crossRef> werden angelegt-->
            <xsl:choose>
                <!-- Skip unused (without children, without sections) properties -->
                <xsl:when test="$isNotCrossRef and $isEmpty"></xsl:when>
                
                <!-- registereinträge (property> die keine verweise sind, werden zu <item> -->
                <xsl:when test="$isNotCrossRef">
                    <!-- element anlegen, ausgewählte attribute übernehmen, 
                            weitere, wenn erforderlich, neu erzeugen  -->
                    <item log1="it1">
                        <xsl:copy-of select="@id"/>
                        <xsl:copy-of select="@level"/>
                        <xsl:copy-of select="@ishidden"/>
                        <xsl:copy-of select="@iscategory"/>
                        
                        <!-- für das markenregister weitere attribute generieren -->
                        <xsl:if test="ancestor::index[@propertytype='brands']">
                            <xsl:attribute name="brandtype">
                                <xsl:value-of select="property/lemma"/>
                            </xsl:attribute>
                            <xsl:attribute name="brandtype-id">
                                <xsl:value-of select="property/@id"/>
                            </xsl:attribute>
                            <xsl:attribute name="brandtype-name">
                                <xsl:value-of select="property/name"/>
                            </xsl:attribute>
                            <xsl:attribute name="brandtype-sign">
                                <xsl:value-of select="property/unit"/>
                            </xsl:attribute>
                        </xsl:if>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        
                        <!-- das vorformatierte element <lemma> wird eingefügt --> 
                        <xsl:apply-templates select="lemma" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        
                        <!-- das vorformatierte element <name> wird eingefügt -->
                        <xsl:apply-templates select="name" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        
                        <!-- weitere elemente werden kopiert -->
                        <xsl:if test="unit[node()]"><xsl:copy-of select="unit"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <xsl:if test="norm_data[node()]"><xsl:copy-of select="norm_data"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <xsl:if test="number[node()]"><xsl:copy-of select="number"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <xsl:if test="signature[node()]"><xsl:copy-of select="signature"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <xsl:if test="file_name[node()]"><xsl:copy-of select="file_name"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <!-- spezielle elemente aus dem wappenregister -->
                        <xsl:if test="content[node()]"><xsl:copy-of select="content"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <xsl:if test="elements[node()]"><xsl:copy-of select="elements"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <xsl:if test="source_from[node()]"><xsl:copy-of select="source_from"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        
                        <!-- die register standorte und texttypen werden für zusätzlich 
                                vorzunehmende formatierungen um weitere angaben ergänzt  -->
                        <xsl:choose>
                            <xsl:when test="$p_index-name='locations'">
                                <!-- für das register standorte wird ein 
                                    spezielles template in di-trans1-index-locations.xsl aufgerufen -->
                                <xsl:call-template name="locations-target-sections-expand"/>
                            </xsl:when>
                            <xsl:when test="$p_index-name='texttypes'">
                                <!-- für das register texttypen wird ein 
                                    spezielles template in di-trans1-index-texttypes.xsl aufgerufen -->
                                <xsl:call-template name="texttypes-target-sections-expand1"/>
                            </xsl:when>
                            <!-- in den anderen registern die unterelemente kopieren -->
                            <xsl:otherwise><xsl:copy-of select="sections"/></xsl:otherwise>
                        </xsl:choose>
                        
                        <!-- zeilenumbruch (nur für die bessere lesbarkeit des code relevant) -->
                        <xsl:if test="sections"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        
                        <!-- das template zur hierarchisierung der registereinträge wird aufgerufen -->
                        <xsl:call-template name="items-hierarchisieren">
                            <!-- den registernamen als parameter mitgeben -->
                            <xsl:with-param name="p_index-name" select="$p_index-name" />
                        </xsl:call-template>
                        
                    </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:when>

                <!-- nun die register-internen verweise bearbeiten -->
                <xsl:otherwise>
                    <!-- für einträge, die verweise sind, ein eigenes template aufrufen,
                            die @id auf das verwiesene <item> als parameter mitgeben-->
                    <xsl:call-template name="crossRef_create">
                        <xsl:with-param name="p_related_id"><xsl:value-of select="$v_related_id"/></xsl:with-param>
                    </xsl:call-template>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <xsl:template name="items-hierarchisieren">
        <!-- funktion: registereinträge hierarchisiert verschachteln -->
        <!-- den namen des registers in einem paramter speichern -->
        <xsl:param name="p_index-name"><xsl:value-of select="ancestor::index/@segment"/></xsl:param>
        
        <!-- aus den geschwister-items die auf der 
                nächst unteren ebene logisch untergeordneten 
                durch dea attributs @parent_id identifizieren und ansteuern -->
        <xsl:for-each select="../property[@parent_id=current()/@id]">
            <!-- den wert des attributs @related_id in einer variablen speichern -->
            <xsl:variable name="v_related_id"><xsl:value-of select="@related_id"/></xsl:variable>
            
            <!-- mittels der variablen zwischen items und verweisen unterscheiden -->
            <xsl:choose>
                <!-- einträge (<property>) die keine verweise sind-->
                <xsl:when test="@related_id='' or not(@related_id)">
                    <!-- item anlegen -->
                    <item>
                        <!-- einige attribute kopieren, andere hinzufügen -->
                        <xsl:copy-of select="@id"/>
                        <xsl:copy-of select="@level"/>
                        <xsl:copy-of select="@ishidden"/>
                        <xsl:copy-of select="@iscategory"/>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <!-- template für das element <lemma> aurufen -->
                        <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <!-- template für das element <name> aurufen -->
                        <xsl:apply-templates select="name"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <!-- weitere elemente weden kopiert -->
                        <xsl:if test="unit[node()]"><xsl:copy-of select="unit"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <xsl:if test="norm_data[node()]"><xsl:copy-of select="norm_data"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <xsl:if test="number[node()]"><xsl:copy-of select="number"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <xsl:if test="signature[node()]"><xsl:copy-of select="signature"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <xsl:if test="file_name[node()]"><xsl:copy-of select="file_name"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <!-- spezielle elemente aus dem wappenregister -->
                        <xsl:if test="content[node()]"><xsl:copy-of select="content"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <xsl:if test="elements[node()]"><xsl:copy-of select="elements"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <xsl:if test="source_from[node()]"><xsl:copy-of select="source_from"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>

                        <!-- die register standorte und texttypen werden 
                            für später vorzunehmende formatierungen um zusätzliche angaben ergänzt  -->
                        <xsl:choose>
                            <xsl:when test="$p_index-name='locations'">
                                <!-- für das register standorte wird ein spezielles template aufgerufen -->
                                <xsl:call-template name="locations-target-sections-expand"/>
                            </xsl:when>
                            <xsl:when test="$p_index-name='texttypes'">
                                <!-- für das register texttypen wird ein spezielles template aufgerufen -->
                                <xsl:call-template name="texttypes-target-sections-expand1"/>
                            </xsl:when>
                            <xsl:otherwise><xsl:copy-of select="sections"/></xsl:otherwise>
                        </xsl:choose>
                        <!-- zeilenumbruch -->
                        <xsl:if test="sections"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
                        <!-- das aktuelle template zur verarbeitung der tiefer liegenden einträge 
                                rekursiv aufrufen, parameter mitgeben -->
                        <xsl:call-template name="items-hierarchisieren">
                            <xsl:with-param name="p_index-name"><xsl:value-of select="$p_index-name"/></xsl:with-param>
                        </xsl:call-template>
                    </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:when>
                <!-- einträge (<property>) die verweise sind   -->    
                <xsl:otherwise>
                    <!-- verweis-template aufrufen und parameter mitgeben-->
                    <xsl:call-template name="crossRef_create"><xsl:with-param name="p_related_id"><xsl:value-of select="$v_related_id"/></xsl:with-param></xsl:call-template>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>
    
    <!-- anlegen der verweis-verknüfungen -->
    <xsl:template name="crossRef_create">
        <!-- parameter aus dem übergeordneten template übernehmen -->
        <xsl:param name="p_related_id"></xsl:param>
        <!-- verweisziele aufsuchen -->
        <xsl:for-each select="ancestor::properties//property[@id=$p_related_id]">
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- verweis anlegen -->
            <!--xsl:if test="sections/section or @propertytype='personnames' and //property[@parent_id=$p_related_id][@level='1']/sections"-->
            <crossRef>
                <xsl:copy-of select="@id"/>
                <xsl:copy-of select="@parent_id"/>
                <xsl:copy-of select="@level"/>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- template zum auslesen der verweise aufrufen -->
                <xsl:call-template name="crossRef_targets"/>
            </crossRef><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!--/xsl:if-->
        </xsl:for-each>
    </xsl:template>
    
    <!-- anlegen der verweisziele> -->
    <xsl:template name="crossRef_targets"> 
        <!-- den wert des attributs @parent_id in einer variablen speichern -->
        <xsl:variable name="v_parent_id"><xsl:value-of select="@parent_id"/></xsl:variable>
        <!-- template für das element <lemma> aufrufen -->
        <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- den übergeordneten eintrag des primären verweisziels ansteuern -->
        <xsl:for-each select="preceding-sibling::property[@id=$v_parent_id]">
            <!-- ein sub-element <crossRef> anlegen -->
            <crossRef>
                <!-- einige attribute übernehmen, andere neu erzeugen -->
                <xsl:copy-of select="@id"/>
                <xsl:copy-of select="@parent_id"/>
                <xsl:copy-of select="@level"/>
                <xsl:copy-of select="@ishidden"/>
                <xsl:copy-of select="@iscategory"/>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>   
                <!-- das template erneut in sich selbst aufrufen, um den nächst übergeordneten eintrag zu bearbeiten -->
                <xsl:call-template name="crossRef_targets"/>
            </crossRef><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>
    
    <!-- lemmata formatieren -->
    <xsl:template match="lemma">
        <!-- der wert des attributs sortkey wird aus dem parent-element <property> 
             ausgelesen und normalisiert,
             wenn das attribut @sortkey  nicht vorhanden oder leer ist, 
             wird das lemma selbst dafür herangezogen
             
             @sortstring (und @sortkey) bildet die grundlage für die 
             alphabetische sortierung der registereinträge
             
             @sortchart dient dazu, nach dem wechsel des anfangsbuchstabens 
             einer folge von einträgen einen durchschuss zu erzeugen
        -->
        <xsl:param name="p_sortstring1">
            <!-- schreibweise normalisieren, whitespace entfernen -->
            <xsl:choose>
                <xsl:when test="parent::property/@sortkey[string()]">
                    <xsl:value-of select="lower-case(normalize-space(parent::*/@sortkey))"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:value-of select="lower-case(normalize-space(.))"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:param>
        
        <xsl:param name="p_sortstring2">
            <!-- die zeichenfolge "Leerstelle=Leerstelle" in den lemmata der literaturliste  wird entfernt-->
            <xsl:choose>
                <xsl:when test="contains($p_sortstring1,' = ') ">
                    <xsl:value-of select="substring-before($p_sortstring1,' = ')"/><xsl:value-of select="substring-after($p_sortstring1,' = ')"/>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="$p_sortstring1"/></xsl:otherwise>
            </xsl:choose>
        </xsl:param>
        
        <!-- parametertest 
<xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<p_sortstring1><xsl:copy-of select="$p_sortstring1"></xsl:copy-of></p_sortstring1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<p_sortstring2><xsl:copy-of select="$p_sortstring2"></xsl:copy-of></p_sortstring2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>-->
        
        <!-- das element <lemma> wird neu angelegt und mit den 
            attributen @sortchart (anfangsbuchstabe des sortierstrings) und 
            @sortstring (sortierstring) versehen -->
        <lemma log1="lem2"><xsl:copy-of select="parent::property/@level"></xsl:copy-of>
            <xsl:attribute name="sortchart">
                <xsl:choose>
                    <xsl:when test="starts-with($p_sortstring2, 'zz')">
                        <xsl:value-of select="translate(lower-case(substring($p_sortstring2,3,1)),'äáàâåüúùûöóòôéèêž' , 'aaaaauuuuooooeeez')"/>
                     </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="translate(lower-case(substring($p_sortstring2,1,1)),'äáàâåüúùûöóòôéèêž' , 'aaaaauuuuooooeeez')"/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>
            <xsl:attribute name="sortstring"><xsl:value-of select="lower-case($p_sortstring2)"/></xsl:attribute>
            <xsl:choose>
                <xsl:when test="contains(.,'#')">
                    <xsl:value-of select="normalize-space(substring-before(.,'#'))"/>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
            </xsl:choose>
        </lemma>
    </xsl:template>
    
    <!-- element <name> für das wappenregister formatieren -->
    <xsl:template match="name">
        <!-- der wert des attributs @sortkey aus dem element ausgelesen und normalisieren -->
        <xsl:param name="p_sortstring1">
            <xsl:choose>
                    <!-- sortstring aus dem sortkey, wenn vorhanden, erzeugen -->
                <xsl:when test="parent::property/@sortkey">
                    <xsl:value-of select="parent::property/@sortkey"/>
                </xsl:when>
                <xsl:otherwise>
                     <!-- alternativ sortstring aus der bezeichung generieren -->
                    <xsl:value-of select="."/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:param>

        <!-- das element <name> nur im wappenregister neu anlegen und 
            mit den attributen @sortchart (anfangsbuchstabe des sortierstrings) 
            und @sortstring (sortierstring) versehen -->
        <name log1="her-nam1">
            <xsl:if test="ancestor::index[@propertytype='heraldry']">
                <xsl:attribute name="sortchart"><xsl:value-of select="translate(lower-case(substring($p_sortstring1,1,1)),'äáàâåüúùûöóòôéèêž' , 'aaaaauuuuooooeeez')"/></xsl:attribute>
                <xsl:attribute name="sortstring"><xsl:value-of select="$p_sortstring1"/></xsl:attribute>
            </xsl:if> 
            <xsl:value-of select="."/>
        </name>
    </xsl:template>
</xsl:stylesheet>