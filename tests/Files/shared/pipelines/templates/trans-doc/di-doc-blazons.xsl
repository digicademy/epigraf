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

    <xsl:template match="blazons">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
        <xsl:comment>blasonierungen anfang</xsl:comment>
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

            <w:p>
                <w:pPr>
                    <w:pStyle w:val="epi-ueberschrift-1"/>
                </w:pPr>
                <!-- sprungmarke öffnen und titel auslesen -->
                <aml:annotation w:type="Word.Bookmark.Start">
                    <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                </aml:annotation>
                <!-- titel einfügen -->
                <w:r>
                    <w:t><xsl:value-of select="@name"/></w:t>
                </w:r>
                <!-- sprungmarke schließen -->
                <aml:annotation w:type="Word.Bookmark.End">
                    <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                </aml:annotation> 
            </w:p>
            <xsl:for-each select="item[lemma]">
                <xsl:variable name="v_sort_self"><xsl:value-of select="lemma/@sortchart"/></xsl:variable>
                <xsl:variable name="v_sort_next"><xsl:value-of select="following-sibling::item[1]/lemma/@sortchart"/></xsl:variable>

                <w:p>
                    <w:pPr>
                        <w:pStyle w:val="epi-blasonierung-eintrag"/>
                    </w:pPr>
                    
                    <xsl:for-each select="*">
                        <xsl:choose>
                            <xsl:when test="self::lemma">
                                <w:r><w:t><xsl:apply-templates></xsl:apply-templates></w:t></w:r>
                                <xsl:if test="following-sibling::*"><w:r><w:t><xsl:text>: </xsl:text></w:t></w:r></xsl:if>
                            </xsl:when>
                            <xsl:otherwise>
                                <w:r><w:t><xsl:apply-templates select="p"></xsl:apply-templates></w:t></w:r>
                                <xsl:if test="following-sibling::*"><w:r><w:t><xsl:text>; </xsl:text></w:t></w:r></xsl:if>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>
                    <xsl:for-each select="*[last()]">
                        <xsl:variable name="v_last"><xsl:value-of select="."/></xsl:variable>
                        <xsl:if test="not(ends-with($v_last,'.'))"><w:r><w:t><xsl:text>.</xsl:text></w:t></w:r></xsl:if>
                    </xsl:for-each>
                    
                </w:p>
                <!-- leerzeile beim wechsel des anfangbuchstabens -->
                <xsl:if test="not($v_sort_self=$v_sort_next)">
                    <w:p>
                        <w:pPr>
                            <w:pStyle w:val="epi-leerzeile-10pt"/>
                        </w:pPr>
                    </w:p>
                </xsl:if>
             </xsl:for-each>
          
        </wx:sect>
        <xsl:comment>blasonierungen ende</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

   
</xsl:stylesheet>