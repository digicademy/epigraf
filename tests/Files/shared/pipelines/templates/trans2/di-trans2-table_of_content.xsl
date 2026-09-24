<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="di-trans2-commons.xsl"/>
    
    <xsl:template name="table_of_content">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <table_of_content>
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="indexing">0</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- überschrift des inhaltsverzeichnises -->
            <xsl:call-template name="section-title"></xsl:call-template>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:apply-templates select="items/item[@itemtype='chapter']/content"></xsl:apply-templates>
        </table_of_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
</xsl:stylesheet>