<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">

    <!--MARKEN, MEISTERZEICHEN, Grundrisse -->
    <xsl:template name="materials">
        <xsl:param name="p_iri"><xsl:value-of select="$p_volume_iri"/>-page-materials</xsl:param>
        
        <page type="materials" iri="{$p_iri}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>      
            <title>Materialien</title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>4</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
            <doktype>1</doktype><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <layout>1</layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        
            <xsl:apply-templates select="drawings">
                <xsl:with-param name="p_iri" select="$p_iri"></xsl:with-param>
            </xsl:apply-templates>                    
        </page><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
    </xsl:template>

    <xsl:template match="drawings">        
        <xsl:param name="p_iri"></xsl:param>

        <xsl:if test="map">
            <tt_content iri="{$p_iri}-floorplans-header"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>header</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header>Lagepläne</header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>2</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <tt_content iri="{$p_iri}-floorplan-scripts"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>html</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header>Lagepläne</header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>2</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext>
                    <script src="/fileadmin/user_upload/scripts/svg-pan-zoom.min.js"></script>
                </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>

        <xsl:for-each select="map">
            <xsl:apply-templates select=".">
                <xsl:with-param name="p_iri" select="$p_iri"></xsl:with-param>
            </xsl:apply-templates>
        </xsl:for-each>

        <xsl:for-each select="image">
            <xsl:apply-templates select=".">
                <xsl:with-param name="p_iri" select="$p_iri"></xsl:with-param>
            </xsl:apply-templates>
        </xsl:for-each>
        
        <xsl:for-each select="brands">
            <xsl:apply-templates select=".">
                <xsl:with-param name="p_iri" select="$p_iri"></xsl:with-param>
            </xsl:apply-templates>
        </xsl:for-each>

        <tt_content iri="{$p_iri}-materials-scripts"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <type>html</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout></header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext>
                <script src="/typo3conf/ext/dio/Resources/Public/JS/materials.js"></script>
            </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
        <xsl:call-template name="citation">
            <xsl:with-param name="p_iri"><xsl:value-of select="$p_iri"/></xsl:with-param>
        </xsl:call-template>
    </xsl:template>

    <!-- Titles are handled elsewhere -->
    <xsl:template match="drawings/title" />
    
    <!-- Image -->
    <xsl:template match="image">
        <xsl:param name="p_iri"></xsl:param>
        <xsl:variable name="imagenr" select="position()"/>        
        <xsl:variable name="imageFilename">/fileadmin/user_upload/materialien/di-<xsl:value-of select="$p_volume_number"/>/abbildungen/<xsl:value-of select="filename"/></xsl:variable>
        <xsl:variable name="imageTitle" select="title"/>
        
        <tt_content iri="{$p_iri}-image-{$imagenr}-header"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <type>header</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header><xsl:value-of select="$imageTitle"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout>3</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext>          
            </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
        <tt_content iri="{$p_iri}-image-{$imagenr}-content"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <type>html</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header><xsl:value-of select="$imageTitle"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout>3</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext>

                <img src="{$imageFilename}" class="materials-image" alt="{$imageTitle}" />   
            </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
    </xsl:template>    


</xsl:stylesheet>