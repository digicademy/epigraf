<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
     
    <xsl:template name="images">
        <xsl:param name="p_iri"><xsl:value-of select="$p_volume_iri"/>-page-images</xsl:param>
        
        <page type="images" iri="{$p_iri}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>      
            <title>Abbildungen</title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>3</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
            <doktype>1</doktype><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <layout>1</layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <tt_content iri="{$p_iri}-gallery"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>list</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header><xsl:value-of select="$p_volume_signature"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <list_type>genericgallery_pi1</list_type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <tx_generic_gallery><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <title><xsl:value-of select="$p_volume_signature"/></title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <type>folder</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <folder>/user_upload/abbildungen/<xsl:value-of select="$p_volume_prefix"/>-<xsl:value-of select="$p_volume_number"/>/</folder><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                    
                </tx_generic_gallery><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <xsl:call-template name="citation">
                <xsl:with-param name="p_iri"><xsl:value-of select="$p_iri"/></xsl:with-param>
            </xsl:call-template>

        </page><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
    </xsl:template>
    
</xsl:stylesheet>