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

<!-- dieses stylesheet erzeugt die seiten 
     des literatur- und quellenverzeichnisses 
     
     die hierarchie der einträge wurde in der 
     vorangegangenen transformation flach ausgelegt (d. h. entschachtelt)
     und ist nur noch durch das attribut @level gekennzeichnet;
     
     zuerst werden die abschnitte des verzeichnisses angelegt,
     dann innerhalb der abschnitte die items ausgespielt
     
     die hierarchie wird durch unterschiedlich weite einzüge 
     kenntlich gemacht
-->


    <!-- verzeichnis literatur und quellen -->
    <xsl:template match="literatures">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
        <xsl:comment>biblio anfang</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- linke leerseite einfügen -->
            <xsl:copy-of select="$p_odd-page"/>
            <!-- überschrift einfügen -->
            <w:p>
                <w:pPr>
                    <w:pStyle w:val="epi-ueberschrift-1"/>
                    <w:pageBreakBefore/>
                </w:pPr>
                <!-- sprungmarke öffnen und überschrift auslesen -->
                <aml:annotation w:type="Word.Bookmark.Start">
                    <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                </aml:annotation>
                <!-- überschrift einfügen -->
                <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>
                <!-- sprungmarke schließen -->
                <aml:annotation w:type="Word.Bookmark.End">
                    <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                </aml:annotation> 
            </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- die verschiedenen abschnitte (literatur, quellen, online-resourcen u.s.w.) 
                 ansteuer -->
            <xsl:for-each select="section">
                <!-- überschrift für den abschnitt erzeugen -->
                <w:p>
                    <w:pPr>
                        <w:pStyle w:val="epi-ueberschrift-2"/>
                    </w:pPr>
                    <!-- sprungmarke öffnen und überschrift einfügen -->
                    <aml:annotation w:type="Word.Bookmark.Start">
                        <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                        <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                    </aml:annotation>
                    <!-- überschrift (lemma) auslesen-->
                    <w:r><w:t><xsl:value-of select="lemma"/></w:t></w:r>
                    <!-- sprungmarke schließen -->
                    <aml:annotation w:type="Word.Bookmark.End">
                        <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                    </aml:annotation>
                </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- zum einfügen der einzeltitel (items) template aufrufen -->
                <xsl:call-template name="biblio"></xsl:call-template>
                <!-- gegebenenfalss leerzeile einfügen -->
                <xsl:if test="following-sibling::section">
                    <w:p>
                        <w:pPr>
                            <w:pStyle w:val="epi-leerzeile-18pt"/>
                        </w:pPr>
                    </w:p>
                </xsl:if>      
            </xsl:for-each>
        </wx:sect>
        <xsl:comment>biblio ende</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- einzeltitel (items) formatieren -->
    <xsl:template name="biblio">
        <!-- einzeltitel ansteuern -->
        <xsl:for-each select=".//item[not(@ishidden='1')]">
            <xsl:choose>
                <!-- di-bände bei gewählter ausgabeoption ausblenden -->
                <xsl:when test="starts-with(lemma,'DI ') and $sw_quellen-literatur-di='1'"></xsl:when>
                
                <xsl:otherwise>
                    <!-- zwischen den items eine leerzeile als durchschuss-->
                    <w:p>
                        <xsl:choose>
                            <!-- nach wechsel des anfangsbuchstabens einen größeren durchschuss anbringen -->
                            <xsl:when test="@level='1' and lemma/@sortchart!=preceding-sibling::item[1]/lemma/@sortchart">
                                <w:pPr>
                                    <w:pStyle w:val="epi-leerzeile-18pt"/>
                                    <!--<w:keepNext w:val="on"/>-->
                                </w:pPr>
                            </xsl:when>
                            <!-- anderenfalls nur einen kleinen durchschuss -->
                            <xsl:otherwise><w:pPr><w:pStyle w:val="epi-leerzeile-5pt"/></w:pPr></xsl:otherwise>
                        </xsl:choose>
                    </w:p>
                    <!-- einzeltitel ausgeben -->
                    <w:p>
                        <w:pPr>
                            <!-- einzug gemäß der hierarschichen ebene erzeugen -->
                            <xsl:if test="@level='1'"><w:ind w:left="400" w:hanging="400"/></xsl:if>
                            <xsl:if test="@level='2'"><w:ind w:left="400"/></xsl:if>
                            <xsl:if test="@level='3'"><w:ind w:left="800"/></xsl:if>
                            <xsl:if test="@level='4'"><w:ind w:left="1200"/></xsl:if>
                        </w:pPr>
                        <!-- titel auwählen, wenn er nicht mit einem punkt endet, einen punkt anhängen -->
                        <w:r>
                            <w:t><xsl:value-of select="normalize-space(lemma)"/><xsl:if test="not(item)"><xsl:if test="not(ends-with(lemma,'.'))">.</xsl:if></xsl:if></w:t>
                        </w:r>
                    </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>

</xsl:stylesheet>