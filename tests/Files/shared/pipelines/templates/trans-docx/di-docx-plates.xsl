<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
    xmlns:v="urn:schemas-microsoft-com:vml" 
    xmlns:w10="urn:schemas-microsoft-com:office:word" 
    xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core" 
    xmlns:o="urn:schemas-microsoft-com:office:office" 
    xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" 
    xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2" 
    xmlns:msxsl="urn:schemas-microsoft-com:xslt" 
    xmlns:exsl="http://exslt.org/common" 
    xmlns:php="http://php.net/xsl" 
    >
    
    <xsl:import href="../commons/di-switch.xsl"/>
    <xsl:import href="di-docx-styles.xsl"/>
    <xsl:import href="di-docx-commons.xsl"/>

<!-- mit diesem stylesheet wird die seite mit den bildnchweisen
     für den tafelteil eine inschriftenbandes erzeugt 
     
    -->

    <xsl:template match="plates">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
        <xsl:comment>bildtafeln anfang</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
            <w:p><w:r><w:br w:type="page"/></w:r></w:p>
             <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!--<w:suppressLineNumbers/>-->
                
                <!-- linke leerseite vorschalten -->
                <xsl:copy-of select="$p_oddPage"/>
                <!-- überschrift  -->
                <w:p>
                    <w:pPr>
                        <w:pStyle w:val="epi-ueberschrift-1"/>
                        <w:spacing w:before="4800"/>
                        <w:suppressLineNumbers/>
                    </w:pPr>
                    <!-- sprungmarke öffnen und titel ausgeben -->
                    <w:bookmarkStart>
                        <xsl:attribute name="w:id">25<xsl:value-of select="tokenize(@id, '-')[last()]"/></xsl:attribute>
                        <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                    </w:bookmarkStart>
                    <!-- titel auslesen -->
                    <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>
                    <!-- sprungmarke schließen -->
                    <w:bookmarkEnd>
                        <xsl:attribute name="w:id">25<xsl:value-of select="tokenize(@id, '-')[last()]"/></xsl:attribute>
                    </w:bookmarkEnd>
                </w:p>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:comment>bildnachweise anfang</xsl:comment>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

                <!-- bildnachweise ansteuern -->
                <xsl:for-each select="plates_credits">
                    <!-- seitenumbruch -->
                    <w:p><w:r><w:br w:type="page"/></w:r></w:p>
                    <!-- titel auslesen -->
                    <w:p>
                        <w:pPr><w:pStyle w:val="epi-ueberschrift-2"/></w:pPr>
                        <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>
                    </w:p>  
                    <!-- absätze ansteuern -->
                    <xsl:for-each select="content/p">
                        <w:p>
                            <w:pPr>
                                <xsl:choose>
                                    <xsl:when test="node()"><w:pStyle w:val="epi-normal-1"/></xsl:when>
                                    <xsl:otherwise><w:pStyle w:val="epi-leerzeile-10pt1"/></xsl:otherwise>
                                </xsl:choose>
                                <xsl:if test="@indent='1'">
                                    <w:ind w:firstLine="240"/>
                                </xsl:if>
                            </w:pPr>
                            <!-- text auslesen -->
                            <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
                        </w:p>
                    </xsl:for-each>
                </xsl:for-each>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:comment>bildnachweise ende</xsl:comment>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
             <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:comment>bildtafeln ende</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
</xsl:stylesheet>