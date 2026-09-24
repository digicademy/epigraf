<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
<!-- dieses stylesheet strukturiert das abkürzungsverzeichnis eines di-bandes
    auf der zweiten transformationsstufe;
    
    das attribut @indexing=1 bewirkt, dass diese abschnitt in das 
    inhaltsverzeichnis des di-bandes aufgenommen wird
    -->    
    
    <xsl:template name="abbreviations">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- abschnitt gemäß @data_key anlegen, attribut @indexing anfügen, 
                damit der abschnitt im inhaltsverzeichnis erscheint,
                vorhandene attribute kopieren -->
        <xsl:element name="{@data_key}">
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="indexing">1</xsl:attribute>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- abschnittsüberschrift -->
            <title>
                <xsl:value-of select="@name"/>
            </title>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- unterelemente an templates verweisen -->
            <xsl:apply-templates></xsl:apply-templates>
            <!-- die weitere verarbeitung erfolgt in di-trans2-commons.xsl 
                    im template match=content  -->
        </xsl:element>
    </xsl:template>
    
</xsl:stylesheet>