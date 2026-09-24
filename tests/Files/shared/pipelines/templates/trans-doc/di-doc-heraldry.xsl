<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml" 
    xmlns:v="urn:schemas-microsoft-com:vml" 
    xmlns:w10="urn:schemas-microsoft-com:office:word" 
    xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core" 
    xmlns:aml="http://schemas.microsoft.com/aml/2001/core" 
    xmlns:wx="http://schemas.microsoft.com/office/word/2003/auxHint" 
    xmlns:o="urn:schemas-microsoft-com:office:office" 
    xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" 
    xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2" 
    xmlns:msxsl="urn:schemas-microsoft-com:xslt" 
    xmlns:exsl="http://exslt.org/common" 
    xmlns:php="http://php.net/xsl" 
    >
    
    <xsl:import href="../commons/di-switch.xsl"/>
    <xsl:import href="di-doc-styles.xsl"/>
    <xsl:import href="di-doc-commons.xsl"/>

<!-- dieses stylesheet formatiert die schematiche darstellung und anordnung 
        von wappen in einem katalogartikel -->
    
    <!--äußere wappentabelle-->
    <xsl:template match="heraldry">
        <!-- auf vorhandensein von wappeneinträgen prüfen -->
        <xsl:if test="node()">
            <xsl:comment>wappen anfang</xsl:comment>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- haupt-tabelle anlegen:
                 eine zeile mit zwei spalten, 
                 erste spalte: titel ("Wappen:")
                 zweite spalte: innere tabelle mit den einzelnen wappen
            -->
            <w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- tabelleneingenschaften -->
                <w:tblPr>
                    <w:tblW w:w="0" w:type="auto"/>
                    <w:tblCellMar>
                        <w:left w:w="10" w:type="dxa"/>
                        <w:right w:w="10" w:type="dxa"/>
                    </w:tblCellMar>
                    <w:tblLook w:val="01E0"/>
                </w:tblPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <w:tblGrid>
                    <w:gridCol w:w="980"/>
                    <w:gridCol w:w="774"/>
                </w:tblGrid> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- tabellenzeile -->
                <w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- spalte für den titel -->
                    <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <w:tcPr>
                            <w:tcMar>
                                <w:bottom w:w="20" w:type="dxa"/>
                            </w:tcMar>
                            <w:tcW w:w="980" w:type="dxa"/>
                        </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <w:p>
                            <w:pPr><w:pStyle w:val="epi-wappen"/></w:pPr>
                            <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>
                        </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- spalte zur aufnahme der inneren tabelle -->
                    <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <!-- innere tabelle aufrufen und an templat verweisen -->
                        <xsl:apply-templates select="table"/>
                        <w:p><w:pPr><w:pStyle w:val="epi-wappen"/></w:pPr></w:p>/> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!--leerzeile einschieben-->
            <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:comment>wappen ende</xsl:comment>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>
    </xsl:template>
    
    <!-- innere wappentabelle -->
    <xsl:template match="heraldry/table">
        <!-- tabelle anlegen -->
        <w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- zeilen ansteuern, neue anlegen und an das betreffende teplate verweisen -->
            <xsl:for-each select="row">
                <w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:apply-templates select="."/>
                </w:tr>
            </xsl:for-each>
        </w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- innere wappentabelle: tabellenzeile -->
    <xsl:template match="heraldry/table/row">
        <!-- zellen (spalten) ansteuern -->
        <xsl:for-each select="cell">
            <!-- neue spalte anlegen und an das entsprechend template verweisen -->
            <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <w:tcPr>
                    <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
                </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:apply-templates select="."/>
            </w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>
    
    <!-- innere wappentabelle: tabellenspalte -->
    <xsl:template match="heraldry/table/row/cell">
        <!-- absatz anlegen -->
        <w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <w:pPr>
                <w:pPr><w:pStyle w:val="epi-wappen"/></w:pPr>
                <xsl:if test="preceding-sibling::cell"><w:ind w:left="100"/></xsl:if>
                <w:ind w:right="100"/>
            </w:pPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- wappeneinträge ansteuern und auslesen, fußnote anhängen -->
            <xsl:for-each select="entry">
                <w:r><w:t><xsl:value-of select="coa[1]" /></w:t></w:r><xsl:apply-templates select="app1"/>
                <xsl:if test="position() != last()"><w:r><w:t>,<xsl:text>&#x0020;</xsl:text></w:t></w:r></xsl:if>
            </xsl:for-each>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
</xsl:stylesheet>