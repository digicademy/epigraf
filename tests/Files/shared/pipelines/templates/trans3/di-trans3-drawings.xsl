<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
 <!-- 
     in diesem stylesheet wird im inschriftenband der abschnitt mit den 
     zeichnungen (marken, grundrisse, schemazeichnungen) angelegt,
     
     es wird aufgerufen in di-trans3.xsl
 
 -->   
    <!-- element <drawings> aufrufen -->
    <xsl:template match="drawings">
        <!-- element neu anlegen, attribute kopieren, titel kopieren -->
        <drawings log3="zei1">
            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="title" copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- templates für marken und abbildungen aufrufen -->
            <xsl:apply-templates select="brands"></xsl:apply-templates>
            <xsl:apply-templates select="image"></xsl:apply-templates>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- gundrisse -->
            <!-- wenn es mehrere grundrisse gibt -->
            <xsl:if test="maps">
                <!-- den container mit den grundrissen ansteuern -->
                <xsl:for-each select="maps">
                    <!-- container neu anlegen, attribute und titel kopieren -->
                    <maps>
                        <xsl:copy-of select="@*"/>
                        <xsl:copy-of select="title" copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:choose>
                            <xsl:when test="map">
                                <xsl:for-each select="map">
                                    <map>
                                        <xsl:copy-of select="@*"/>
                                        <xsl:attribute name="map_iri"><xsl:value-of select="map_concordance/content/p/rec_lit/@data-link-iri"/></xsl:attribute>
                                        <xsl:apply-templates select="." />
                                    </map>
                                </xsl:for-each>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:apply-templates></xsl:apply-templates>
                            </xsl:otherwise>
                        </xsl:choose>
                    </maps>
                </xsl:for-each>
            </xsl:if>
            <!-- wenn es nur einen grundriss gibt -->
            <xsl:if test="map">
                <xsl:for-each select="map">
                    <map>
                        <xsl:copy-of select="@*"/>
                        <xsl:attribute name="map_iri"><xsl:value-of select="map_concordance/content/p/rec_lit/@data-link-iri"/></xsl:attribute>
                        <xsl:apply-templates></xsl:apply-templates>
                    </map>
                </xsl:for-each>
            </xsl:if>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </drawings>
    </xsl:template>
    
    <xsl:template match="map/title"><xsl:copy-of select="." /></xsl:template>
    
    <!-- vorbemerkungen zu den grundrissen -->
    <xsl:template match="map/content"><note><xsl:apply-templates /></note></xsl:template>
    
    <!-- 
        konkordanz der grabplattennummern auf dem grundriss mit den artikelnummern,
        
        die nummern auf den grundrisszeichnungen sind im zugehörigen artikel
        unter den signaturen aufgenommen, 
        im Feld Autor/Quelle (element <content>) ist der zugehörige grundriss als textbaustein referenziert,
        die textbausteine liegen im literatur-index
    -->
    <xsl:template match="map_concordance">
        <!-- textbaustein zur signatur in einen parameter speichern -->
        <xsl:param name="p_textbaustein"><xsl:value-of select="content/p/rec_lit/@data-link-value"/></xsl:param>
        <map-concordance><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- jeden artikel ansteuern, der eine signatur mit dem textbaustein aufweist -->
            <xsl:for-each select="ancestor::book/articles/article[sections/section[@sectiontype='signatures']/items/item[@itemtype='signatures']/property/name=$p_textbaustein]">
                <!-- artikelnummer in einer variablen speichern -->
                <xsl:variable name="v_article_number"><xsl:value-of select="@nr"/></xsl:variable>
                <!-- artikel-id in einer variablen speichern -->
                <xsl:variable name="v_article_id"><xsl:value-of select="tokenize(@id, '-')[last()]"/></xsl:variable>
                <!-- jedes signatur-item  mit dem betreffenden textbaustein ansteuern -->
                <xsl:for-each select="sections/section[@sectiontype='signatures']/items/item[@itemtype='signatures'][property/name=$p_textbaustein]">
                    <!-- nummer auf dem grundriss ohne führende nullen in einer variablen speichern -->
                    <xsl:variable name="v_object_number">
                        <xsl:choose>
                            <xsl:when test="contains(value, 'gp')"><xsl:number value="substring-after(value, 'gp')" format="1"/></xsl:when>
                            <xsl:otherwise>
                                <xsl:analyze-string select="value" regex="^[0-9]*$">
                                    <xsl:matching-substring>
                                        <xsl:number format="1" value="."/>
                                    </xsl:matching-substring>
                                    <xsl:non-matching-substring>
                                        <xsl:choose>
                                            <xsl:when test="starts-with(.,'00')">
                                                <xsl:value-of select="substring(.,3)"/>
                                            </xsl:when>
                                            <xsl:when test="starts-with(.,'0') and not(starts-with(.,'00'))">
                                                <xsl:value-of select="substring(.,2)"/>
                                            </xsl:when>
                                            <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
                                        </xsl:choose>
                                    </xsl:non-matching-substring>
                                </xsl:analyze-string>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:variable>     
                   
                    <!-- tabellenzeile anlegen-->
                    <row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <!-- nummer auf dem grundriss -->
                        <map_object_number>
                            <xsl:attribute name="sortstring"><xsl:value-of select="value"/></xsl:attribute>
                            <xsl:value-of select="$v_object_number"/></map_object_number><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <!--artikelnummer-->
                        <article_number article-id="{$v_article_id}">Kat.-Nr. <xsl:value-of select="$v_article_number"/></article_number><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </xsl:for-each>
        </map-concordance><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- dateiname der grundrisszeichnung -->
    <xsl:template match="map_file"><map_path><xsl:value-of select="content/p"/></map_path></xsl:template>
    
    <!-- titel der grundrisszeichnung -->
    <xsl:template match="map_title"><map_title><xsl:value-of select="content/p"/></map_title></xsl:template>
    
    <!-- Abbildung -->
    <xsl:template match="image"><xsl:copy-of select="." /></xsl:template>
    

    
</xsl:stylesheet>