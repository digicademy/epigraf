<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans2-commons.xsl"/>
    <xsl:import href="di-trans2-indices-commons.xsl"/>
    
    <xsl:template name="brands">
        <xsl:param name="p_marken">
            <xsl:copy-of select="indices/index[@propertytype='brands']/item"/>
        </xsl:param>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <brands>
            <!--<xsl:copy-of select="@*"/>-->
            <xsl:attribute name="indexing">1</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="manage_lists/index[@propertytype='brandtypes']/item">
                <xsl:variable name="v_id"><xsl:value-of select="@id"/></xsl:variable>
                <xsl:variable name="v_typ"><xsl:value-of select="lemma"/></xsl:variable>
                <brandtype>
                    <xsl:attribute name="id" select="$v_id" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <title><xsl:value-of select="lemma"/></title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="$p_marken/item">
                        <!--<xsl:copy-of select="."/>-->
                        <xsl:if test="@brandtype-id=$v_id">
                            <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:if>
                    </xsl:for-each>
                </brandtype><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </brands><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    
    <!-- register marken -->
    <xsl:template name="register-marken">
        <!-- die nicht identifizierten marken in einem paramter sammeln -->
        <xsl:param name="p_marken_undefine">
            <xsl:for-each select="item[lemma[not(text())] or not(lemma)][@brandtype='Hausmarken']">
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </xsl:param>
        <!-- die identifizierten hausmarken auslesen -->
        <xsl:for-each select="item[lemma[text()]][@brandtype='Hausmarken']">
            <xsl:sort select="lemma" order="ascending" lang="de" case-order="upper-first" />
            <item log2="r47">
                <xsl:copy-of select="@*"/>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <lemma>
                    <xsl:copy-of select="lemma/@*"/>
                    <xsl:value-of select="lemma"/><xsl:text> (M)</xsl:text>
                </lemma>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:for-each select="sections">
                    <xsl:copy-of select="."/>
                </xsl:for-each>
                <xsl:call-template name="crossRefs"></xsl:call-template>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- subeinträge -->
                <!--in hgw keine untereinträge und keine verweise-->
            </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>          
        </xsl:for-each>
        <!--       
<parametertest_marken_undefine><xsl:copy-of select="$marken_undefine"/></parametertest_marken_undefine>
-->
        <!-- für die nichtidentifizierten marken zuerst aus dem register der markentypen 
            die verschiedenen typen auslesen und je eine gruppe bilden-->
<!--        <xsl:for-each select="ancestor::book/manage_lists/index[@propertytype='brandtypes']/item">
            <xsl:variable name="v_brandtype"><xsl:value-of select="lemma"/></xsl:variable>
            <item type="group" log2="r48" brandtype="{$v_brandtype}">
                <lemma>nicht identifizierte <xsl:value-of select="lemma"/></lemma><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-\- die links aus dem parameter der nicht identifizierten marken markentypisch auslesen-\->
                <sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="$p_marken_undefine/item[@brandtype=$v_brandtype]//section">
                        <xsl:copy-of select="."/>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                
            </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>   
        </xsl:for-each>-->
        
        
        <item type="group" log2="r48" brandtype="Hausmarken">
            <lemma>nicht identifizierte Hausmarken</lemma><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- die links aus dem parameter der nicht identifizierten marken markentypisch auslesen-->
            <sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:for-each select="$p_marken_undefine/item//section">
                    <xsl:copy-of select="."/>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                
        </item>
    </xsl:template>
    
</xsl:stylesheet>