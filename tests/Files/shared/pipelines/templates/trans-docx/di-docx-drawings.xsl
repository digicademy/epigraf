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

    <!-- 
     dieses stylesheet erzeugt die vorsatz-seite(n) für die 
     ausgabe von zeichnungen verschiedener kategorien (im/als anhang des inschriftenbandes),
     
     bei den kategorien handelt es sich um:
     hausmarken (brands)
     schemazeichnungen (drawing, noch nicht angelegt)
     grundrisse (maps,
     
     die in jeweils eigenen stylesheet verarbeitet werden
    -->
    
    <xsl:template match="drawings">
        <!-- ausgabeoptionen abfragen -->
        <xsl:if test="$sw_zeichnungen=1">
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
            <xsl:comment>zeichnungen anfang</xsl:comment>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
             <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <w:p>
                    <w:pPr><w:pStyle w:val="epi-ueberschrift-1"/></w:pPr>
                    <!-- neue seite -->
                    <w:r><w:br w:type="page"/></w:r>
                    <!-- sprungmarke öffnen und titel auslesen -->
                    <w:bookmarkStart>
                        <xsl:attribute name="w:id">14<xsl:value-of select="tokenize(@id, '-')[last()]"/></xsl:attribute>
                        <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                    </w:bookmarkStart>
                    <!-- titel des abschnittszeichnungen einfügen -->
                    <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>
                    <!-- sprungmartke schließen -->
                    <w:bookmarkEnd>
                        <xsl:attribute name="w:id">14<xsl:value-of select="tokenize(@id, '-')[last()]"/></xsl:attribute>
                    </w:bookmarkEnd>
                </w:p>
                <!-- unterabschnitte ansteuern und an templates verweisen -->
                <xsl:apply-templates select="*[not(name()='titel')]"></xsl:apply-templates>
            
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
            <xsl:comment>zeichnungen ende</xsl:comment>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>
    </xsl:template>
    
</xsl:stylesheet>