<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:epi="http://epigraf.inschriften.net#xslt-functions"
    exclude-result-prefixes="xs epi"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/>
    <xsl:import href="di-dio-links.xsl"/>
    <xsl:import href="di-dio-tools.xsl"/>
   
    <!--Meisterzeichen und Hausmarken-->
    <xsl:template match="brands">
        <xsl:param name="p_iri"></xsl:param>
        <tt_content iri="{$p_iri}-section-header"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header><xsl:value-of select="title"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout>2</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        

        <xsl:for-each select="brand-type">
            <tt_content iri="{$p_iri}-section-brand-type-{@id}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                
                <header><xsl:value-of select="title"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>3</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext>
                    <xsl:apply-templates select="."/>
                </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                        
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="brand-type">
        <xsl:param name="p_brand-sign">
            <xsl:choose>
                <xsl:when test="$sw_markentypen_zusammenfassen=1">
                    <xsl:text>M</xsl:text>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:choose>
                        <xsl:when test="@sign"><xsl:value-of select="@sign"/></xsl:when>
                        <xsl:otherwise><xsl:value-of select="substring(title,1,1)"/></xsl:otherwise>
                    </xsl:choose>                    
                </xsl:otherwise>
            </xsl:choose>            
        </xsl:param>
        <div class="brands-gallery">
            <xsl:for-each select="brand-group/brand">
                <xsl:variable name="brandName"><xsl:value-of select="$p_brand-sign"/><xsl:value-of select="epi:pad(nr, 3)"/></xsl:variable>
                <xsl:variable name="brandFilename">/fileadmin/user_upload/materialien/di-<xsl:value-of select="$p_volume_number"/>/marken/<xsl:value-of select="translate(tokenize(file_name, '/')[last()],'+','_')"/></xsl:variable>
                <div class="brands-item" id="{@id}">
                    <div class="brands-box">
                        <img src="{$brandFilename}" alt="{$brandName}" />
                    </div>
                    <div class="brands-number"><xsl:value-of select="$brandName"/> </div>
                    <div class="brands-links">Nr. <xsl:call-template name="linkhandler"/></div>
                </div>                

            </xsl:for-each>
        </div>
    </xsl:template>
    
</xsl:stylesheet>