<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="di-trans2-commons.xsl"/>

<!-- 
    dieses stylesheet bereitet den abschnitt für zeichnungen vor,
    es wird aufgerufen in di-trans2-book.xsl
-->

    <xsl:template name="drawings">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- abschnitt neu anlegen, attribut @indexing=1 anfügen, 
                damit der abschnitt im inhaltsverzeichnis des bands gelistet wird,
                schon vorhandene attribute kopieren
        -->
        <drawings log2="zei1">
            <xsl:attribute name="indexing">1</xsl:attribute>
            <xsl:copy-of select="@*"/>
            <!-- abschnittsüberschrift anweisen -->
            <xsl:call-template name="section-title" />
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="section"><xsl:apply-templates select="." /></xsl:for-each>
        </drawings>
    </xsl:template>
    
    <!-- Abschnitt für eine einzelne Abbildung -->
    <xsl:template match="section[@data_key='drawing']">        
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <image>
            <xsl:attribute name="indexing">1</xsl:attribute>
            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <title><xsl:value-of select="@name"/></title>
            <xsl:for-each select="items/item/content"><xsl:apply-templates select="." /></xsl:for-each>
            <xsl:for-each select="section"><xsl:apply-templates select="." /></xsl:for-each>
        </image>
    </xsl:template>
    
    <!-- Abschnitt für eine einzelne Abbildung: Dateiname -->
    <xsl:template match="section[@data_key='drawing_file']">
        <filename><xsl:value-of select="items/item/content/text()" /></filename><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>    
    
</xsl:stylesheet>