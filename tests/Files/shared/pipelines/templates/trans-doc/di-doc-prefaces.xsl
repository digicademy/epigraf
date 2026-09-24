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

    <!-- 
        mit diesem stylesheet werden die seiten 
        für das vorwort eines inschriftenbandes erzeugt
    -->

    <xsl:template match="prefaces">
        <xsl:comment>vorwort anfang</xsl:comment> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- gegebenenfalls linke leerseite einfügen -->
            <xsl:if test="preceding-sibling::*">
                <xsl:copy-of select="$p_odd-page"/>
            </xsl:if>
            <!-- vorwort-überschrift anlegen -->
            <w:p>
                <w:pPr><w:pStyle w:val="epi-ueberschrift-1"/></w:pPr>
                <!-- sprungmarke öffenen und titel ausgeben -->
                <aml:annotation w:type="Word.Bookmark.Start">
                    <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                </aml:annotation>
                <!-- titel auslesen -->
                <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>
                <!-- sprungmarke schließen -->
                <aml:annotation w:type="Word.Bookmark.End">
                    <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                </aml:annotation>        
            </w:p>
            
            <!-- absätze des vorworts ansteuern -->
            <xsl:for-each select="content/p">
                <w:p>
                    <w:pPr>
                        <w:pStyle w:val="epi-normal-1"/>
                        <xsl:if test="@indent='1'">
                            <w:ind w:first-line="240"/>
                        </xsl:if>
                    </w:pPr>
                    <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
                </w:p>
            </xsl:for-each>
            <!-- datum und unterschrift ausgeben-->
            <w:p>
                <w:pPr>
                    <w:pStyle w:val="epi-normal-1"/>
                    <!-- rechtsbündiger tabulator am rechten rand -->
                    <w:tabs>
                        <w:tab w:val="right" w:pos="8080"/>
                    </w:tabs>
                    <w:ind w:right="27"/>
                    <w:spacing w:before="240"/>
                </w:pPr>
                <!-- datum einfügen -->
                <w:r>
                    <w:t><xsl:value-of select="datum/content/p"/></w:t>
                </w:r>
                <!-- tabulator setzen und unterschrift einfügen -->
                <w:r>
                    <w:tab/>
                    <w:t><xsl:value-of select="unterschrift/content/p"/></w:t>
                </w:r>
            </w:p>
        </wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:comment>vorwort ende</xsl:comment>
    </xsl:template>

</xsl:stylesheet>