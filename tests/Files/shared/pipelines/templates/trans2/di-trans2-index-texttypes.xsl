<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    
    <!-- in diesem stylesheet wird das register "textsorten" 
        mit den unterkategorien "sprache" und "vers" neu aufgebaut;
        
        in der vorangegangene transformation (trans1) wurden die 
        referenzen auf inschriften (element <sections/section>) um die attribute: 
            @metres     (gibt an ob die inschrift in versen verfasst, d. h.  metrische bzw. gereimt ist)
            @language   (bezeichnung der sprache)
            @lang_id    (@id im sprachenindex> 
            @lang_alias (abkürzung der sprachbezeichnung)
            @lang_sort  (Sortierschlüssel der sprachbezeichnung)
        erweitert.
        
        aus diesen angaben werden sprache und vers als untereinträge (element <item>) 
        zu den textsorten gebildet:
        <item>
            <lemma>textsorte</item>
            <item>
                <lemma>sprache</lemma>
                <item>
                    <lemma>vers</lemma>
                </item>
            </item>
        </item>
        
    -->

    <xsl:template name="links-textsorten">
        
        <!-- 1. schritt: nach sprachen differenzieren -->
        <xsl:param name="p_links_textsorten1">
            <xsl:for-each select="section[not(@lang_alias=preceding-sibling::section/@lang_alias)]">
                <xsl:sort select="@lang_sort" order="ascending" lang="de" case-order="upper-first" />
                <xsl:variable name="v_language"><xsl:value-of select="@lang_alias"/></xsl:variable>
                <language>
                    <xsl:attribute name="language" select="$v_language" />
                    <xsl:copy-of select="."/>
                    <xsl:for-each select="following-sibling::section[@lang_alias=$v_language]">
                        <xsl:copy-of select="."/>
                    </xsl:for-each>
                </language>
            </xsl:for-each>
        </xsl:param>
        
        <!-- 2. schritt nach vers differenzieren und doubletten entfernen-->
        <xsl:param name="p_links_textsorten2">
            <xsl:for-each select="$p_links_textsorten1/language">
                <language>
                    <xsl:copy-of select="@*"/>
                    <xsl:for-each select="section[@metres='0'][not(@articles_id=preceding-sibling::section[1]/@articles_id)]">
                        <xsl:copy-of select="."/>
                    </xsl:for-each>
                    <xsl:if test="section[@metres='1']">
                        <vers>
                            <xsl:for-each select="section[@metres='1'][not(@articles_id=preceding-sibling::section[1]/@articles_id) or (@articles_id=preceding-sibling::section[1]/@articles_id and preceding-sibling::section[1]/@metres = 0)]">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                        </vers>                   
                    </xsl:if>
                </language>
            </xsl:for-each>            
        </xsl:param>
        
        <!-- 3. schritt: neu strukturieren = untereinträge nach sprache und vers generieren -->
        <xsl:param name="p_links_textsorten3">
            <xsl:for-each select="$p_links_textsorten2">
                
                <!-- a) keine sprachangabe vorhanden -->
                <xsl:if test="language[string-length(@language)=0]">
                    <sections log2="rsecs1"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:for-each select="language[string-length(@language)=0]//section">
                            <xsl:sort select="sort" order="ascending" lang="de" case-order="upper-first"/>
                            <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:for-each>
                    </sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:if>
                
                <!-- b) sprachangabe vorhanden -->
                <xsl:if test="language[string-length(@language)!=0]">
                    <!-- die einzelnen sprachangaben ansteuern um für jede ein <item> anzulegen  -->
                    <xsl:for-each select="language[string-length(@language)!=0]">
                        <!-- <item> anlegen -->
                        <item log2="r3"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <!-- element <lemma> erzeugen -->
                            <lemma><xsl:value-of select="@language"/></lemma><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <!-- wenn keine zusätzliche vers-angabe existiert, 
                                    die referenzen auf artikelnummern (element <section>) hier einfügen-->
                            <xsl:if test="section">
                                <sections log2="rsecs2"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <xsl:for-each select="section">
                                        <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    </xsl:for-each>
                                </sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:if>
                            <!-- wenn innerhalb des elments <language> zusätzlich vers-angaben vorkommen, 
                                    ein <item> anlegen -->
                            <xsl:if test="vers">
                                <item log2="r4"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <!-- element <lemma> erzeugen -->
                                    <lemma>Vers</lemma><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <!-- referenzen auf artikelnummern einfügen -->
                                    <sections log2="rsecs3"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        <xsl:for-each select="vers/section">
                                            <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        </xsl:for-each>
                                    </sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:if>
                        </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </xsl:if>
            </xsl:for-each>
        </xsl:param>
        
        <!--  parametertest    
    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <textsorten1><xsl:copy-of select="$p_links_textsorten1"/></textsorten1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <textsorten2><xsl:copy-of select="$p_links_textsorten2"/></textsorten2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
     -->
        <!-- ergebnis des letzten parameters einfügen -->
        <xsl:copy-of select="$p_links_textsorten3"/>
        
    </xsl:template>
    
</xsl:stylesheet>