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
    <xsl:import href="di-doc-tools.xsl"/>
    <xsl:import href="di-doc-links.xsl"/>
    
    <!-- dieses stylesheet erzeugt seiten mit der 
        auflistung der schon erschienen di-bände;
        
        die liste wurde in der vorhergehenden transformation 
        aus einer externen datei importiert und in ein schema
        table/row/cell eingefügt;
        
        das folgende template erzeugt daraus analog eine tabelle für word
    
    -->
    
    <xsl:template match="di_volumes">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
        <xsl:comment>di-bände anfang</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- linke leerseite einfügen -->
            <xsl:copy-of select="$p_odd-page"/>
            
            <!-- titel anlegen -->
            <w:p>
                <w:pPr><w:pStyle w:val="epi-ueberschrift-2"/></w:pPr>
                <!-- sprungmarke öffnen und titel auslesen -->
                <aml:annotation w:type="Word.Bookmark.Start">
                    <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                </aml:annotation>
                <!-- titel einfügen -->
                <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>
                <!-- sprungmarke schließen -->
                <aml:annotation w:type="Word.Bookmark.End">
                    <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                </aml:annotation> 
            </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- word-tabelle erzeugen -->
            <w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <w:tblGrid>
                    <w:gridCol w:w="861"/>
                    <w:gridCol w:w="7266"/>
                </w:tblGrid>
                <!-- tabellenzeilen ansteuern -->
                <xsl:for-each select="table/row">
                    <!--neue tabellenzeile anlegen-->
                    <w:tr>
                        <w:trPr>
                            <w:cantSplit w:w="on"/>
                        </w:trPr>
                        <!-- erste spalte -->
                        <w:tc>
                            <w:tcPr><w:tcW w:w="861" w:type="dxa"/></w:tcPr>
                            <w:p>
                                <w:pPr>
                                    <w:pStyle w:val="epi-tabellenzeile"/>
                                </w:pPr>
                                <!-- inhalt an template verweisen (auslesen) -->
                                <xsl:apply-templates select="cell[1]"/>
                            </w:p>
                        </w:tc>
                        <!-- zweite spalte -->
                        <w:tc>
                            <w:tcPr><w:tcW w:w="7266" w:type="dxa"/></w:tcPr>
                            <w:p>
                                <w:pPr>
                                    <w:pStyle w:val="epi-tabellenzeile"/>
                                </w:pPr>
                                <!-- inhalt an template verweisen (auslesen) -->
                                <xsl:apply-templates select="cell[2]"/>
                            </w:p>
                        </w:tc>
                    </w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </wx:sect>
        <xsl:comment>di-bände ende</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
</xsl:stylesheet>