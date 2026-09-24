<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="di-trans2-commons.xsl"/>

    <!-- 
        in diesem stylesheet wird der abschnitt für
        die titelseiten des inschriftenbands angelegt,
    
        es wird aufgerufen in di-trans2-book.xsl.
    -->

    <xsl:template name="preliminaries">
        <!-- abschnitt preliminaries gemäß @data_key erzeugen, attribut @indexing=0 anfügen, 
                damit der abschnitt nicht in inhaltsverzeichnis des bands aufgeführt wird,
                vorhandene attribute kopieren
        -->
        <xsl:element name="{@data_key}">
            <xsl:attribute name="indexing">0</xsl:attribute>
            <xsl:copy-of select="@*"/>

            <!-- die sections-elemente (unterabschnitte) werden neu angelegt und nach dem @caption-attribut benannt,
            anschließend alle überflüssigen elemente ausgesondert 
            und nur der inhalt des beschreibungs-feldes eingefügt-->
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="section">
                <xsl:variable name="v_element-name"><xsl:value-of select="@data_key"/></xsl:variable>
                <!-- abschnitt erzeugen und nach dem datenschlüssel benennen -->
                <xsl:element name="{$v_element-name}"><xsl:attribute name="log1">prelim0</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="items/item[@itemtype='chapter']/content[node()]"><xsl:apply-templates select="."></xsl:apply-templates></xsl:for-each>
                    <!-- template zur erzeugung der tieferen unterabschnitte aufrufen  -->
                    <xsl:call-template name="prelim-sections"></xsl:call-template>
                </xsl:element><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
    
    <!-- unterabschnitte -->
    <xsl:template name="prelim-sections">
        <xsl:for-each select="section">
            <xsl:variable name="v_element-name">
                <xsl:choose>
                    <xsl:when test="@data_key and string-length(@data_key)!=0"><xsl:value-of select="@data_key"/></xsl:when>
                    <xsl:otherwise><xsl:value-of select="lower-case(@name)"/></xsl:otherwise>
                </xsl:choose>
            </xsl:variable>
            <!-- abschnitt erzeugen und nach dem datenschlüssel oder dem abschnittsnamen benennen -->
            <xsl:element name="{$v_element-name}"><xsl:attribute name="log1">prelim1</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- inhalt ansteuern und an template verweisen -->
                <xsl:for-each select="items/item[@itemtype='chapter']/content[node()]"><xsl:apply-templates select="."></xsl:apply-templates></xsl:for-each>
                <!-- für tiefer liegende unterabschnitte das template erneut aufrufen -->
                <xsl:call-template name="prelim-sections"></xsl:call-template>
            </xsl:element><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>
    
</xsl:stylesheet>