<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="di-trans2-commons.xsl"/>
    
    <!-- 
        in diesen stylesheet wird der abschnitt für das vorwort
        eines inschriftenbands erzeugt,
        als unterelemente werden container für datum und unterschrift eingefügt,
        
        es wird aufgerufen in di-trans2-book.xsl
        
        NOTA BENE: für mehrere vorworte ist noch eine anpasssung erforderlich,
        es wäre auch ein weiterer datenschlüssel di_preface erforderlich 
    -->
    
    <xsl:template name="prefaces">
        
        <!-- abschnitt anlegen, attribut @indexing=1 anfügen, 
                damit der abschnitt im inhaltsverzeichnis gelistet wird,
                vorhandene attribute kopieren
        -->
        <xsl:element name="{@data_key}">
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="indexing">1</xsl:attribute>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- abschnittsüberschrift -->
            <xsl:call-template name="section-title" />
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- text des vorworts ansteuern und an templates verweisen -->
            <xsl:apply-templates select="items/item[@itemtype='chapter']/content" />

            <!-- die unterabschnitte, datum und unterschrift enthaltend, ansteuern -->
            <xsl:for-each select="section">
                <xsl:variable name="v_caption" select="lower-case(@name)" />
                <!-- element (datum oder unterschrift) anlegen, elementnamen aus dem attribut @name erzeugen -->
                <xsl:element name="{$v_caption}">
                    <xsl:attribute name="data_key" select="replace(items/item/property[@propertytype='datakeys']/norm_iri,'di_','')" />
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                    
                    <!-- inhalt einfügen -->
                    <xsl:apply-templates select="items/item[@itemtype='chapter']/content" />
                </xsl:element><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </xsl:element>
    </xsl:template>
</xsl:stylesheet>