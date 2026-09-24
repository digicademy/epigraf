<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="di-trans2-commons.xsl"/>
    
    <!-- 
        mit diesem stylesheet werden sie abschnitte für grundrisszeichnungen angelegt;
        es wird in di-trans2-book.xsl aufgerufen
    -->
    
    <!-- abschnitt grundrisse (wenn mehrere grundrisse vorhanden sind) -->
    <xsl:template match="section[@data_key='maps']">
        <!-- abschnitt für mehrere grundrisse anlegen -->
        <maps log2="maps1">
            <xsl:attribute name="indexing">1</xsl:attribute>
            <xsl:copy-of select="@*"/>
            <!-- abschnittsüberschrift -->
            <xsl:call-template name="section-title" />
            <!-- abschnitte für einzelne grundrisse an templates verweisen -->
            <xsl:apply-templates /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </maps><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template> 
    
    <!-- abschnitt für einen einzelnen grundriss -->
    <xsl:template match="section[@data_key='map']">        
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- abschnitt neu anlegen, vorhandene attribute kopieren -->
        <map>
            <xsl:if test="not(ancestor::maps)"><xsl:attribute name="indexing">1</xsl:attribute></xsl:if>
            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <title><xsl:value-of select="@name"/></title>
            <!-- inhalt ansteuern -->
            <xsl:for-each select="items/item/content">
                <!-- inhalts-element neu anlegen, 
                        absatz (element <p>) erzeugen und in diesen 
                        die unter-elemente aufrufen und an tenplates verweisen-->
                
                    <xsl:apply-templates select="." />

            </xsl:for-each>
            <!-- unterabschnitte (konkordanz, zeichnung, bildlegende) 
                ansteuern und an templates verweisen -->
            <xsl:for-each select="section"><xsl:apply-templates select="." /></xsl:for-each>
        </map>
    </xsl:template>
    
    <!-- konkordanz zum grundriss -->
    <xsl:template match="section[@data_key='map_concordance']">
        <map_concordance><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="links"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:apply-templates select="items/item/content" /></map_concordance><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- grundrisszeichnung (filename) -->
    <xsl:template match="section[@data_key='map_file']">
        <map_file><xsl:apply-templates select="items/item/content" /></map_file><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- bildlegende zum grundriss -->
    <xsl:template match="section[@data_key='map_title']">
        <map_title><xsl:apply-templates select="items/item/content" /></map_title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    
</xsl:stylesheet>