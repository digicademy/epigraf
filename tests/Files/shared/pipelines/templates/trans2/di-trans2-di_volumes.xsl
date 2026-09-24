<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="di-trans2-commons.xsl"/>

<!-- in diesem stylesheet wird die externe excel-tabelle der 
        bereits erschienenen di-bände in einen abschnitt mit interner tabelle überführt
    
        wird aufgerufen in di-trans2-book.xsl
    -->


    <xsl:template name="di_volumes">
        <!-- pfad auf die externe datei in einem parameter speichern: wird nicht mehr benötigt
        <xsl:param name="p_baende">../../../../../../pipelines/files/di_baende.xls</xsl:param>
        -->


        <!-- abschnitt neu anlegen, attribut @indexing=1 anfügen, 
            damit der abschnitt in das inhaltsverzeichnis des bandes aufgenommen wird,
            schon vorhandene attribute kopieren.
        -->
        <di_volumes indexing="1">
            <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- abschnittsüberschrift einfügen -->
            <xsl:call-template name="section-title"></xsl:call-template>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- tabelle anlegen und aus dem abschnitt book/properties füllen -->
            <table><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- tabellenzeilen ansteuern -->
                
                <xsl:for-each select="ancestor-or-self::book/properties/property">
                    <!-- tabellenzeile neu anlegen -->
                    <row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <!-- zwei spalten (<cell>) anlegen und füllen -->
                        <cell><xsl:value-of select="signature"/></cell><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <cell><xsl:value-of select="lemma"/></cell><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
                
                
                <!-- altes muster über externe excel-datei:
                    
                    <xsl:for-each select="document($p_baende)//*[name()='Row'][*/*[node()]]">
                    <!-\- tabellenzeile neu anlegen -\->
                    <row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <!-\- zwei spalten (<cell>) anlegen und füllen -\->
                        <cell><xsl:value-of select="*[1]/*"/></cell><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <cell><xsl:value-of select="*[2]/*"/></cell><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>-->
            </table><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </di_volumes><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
</xsl:stylesheet>