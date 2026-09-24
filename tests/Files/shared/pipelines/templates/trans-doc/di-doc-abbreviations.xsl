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

<!-- in diesem stylesheet wird der abschnitt abkürzungen eines inschriftenbandes formatiert -->

    <xsl:template match="abbreviations">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
        <xsl:comment>abkuerzungen anfang</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- seitenumbruch -->
        <wx:sect>
            <w:p>
                <w:pPr><w:pageBreakBefore/></w:pPr>
            </w:p>
        </wx:sect>
        <wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- linke leerseite einfügen -->
            <xsl:copy-of select="$p_odd-page"/>
            <!-- die child-elemente (titel, absätze und tabellen) aufrufen und über je eigene templates einfügen,
                 die entsprechenden templates stehen in di-doc-commons.xsl
            -->
            <xsl:apply-templates></xsl:apply-templates>
            
        </wx:sect>
        <xsl:comment>abkuerzungen ende</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
   
</xsl:stylesheet>