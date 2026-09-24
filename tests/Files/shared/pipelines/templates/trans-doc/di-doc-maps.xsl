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

<!-- in diesem stylesheet werden die seiten für die darstellung 
     von grundrissen erzeugt
     
     zuerst wird die titelseite des abschnitts angelegt,
     sie enthält die überschrift des abschnitts;
     
     wenn es nur einen grundriss gibt, 
     werden auf derselben seite unter der überschrift     
     anmerkungen ausgegeben,
     darunter eine konkordanz der nummern auf dem grundriss mit den artikelnummern
     
     wenn es mehrere grundrisse gibt, wird für jeden eine eigene vorsatzseite mit der 
     konkordanz erzeugt
     
     die grundrisse selbst werden nach erstellung des typoskripts manuell eingefügt
     die konkordanzen müssen ebenfalls manuell auf vierspaltigen seitensatz umgestellt werden
     
    -->

    <!-- grundrisse -->
    <xsl:template match="maps">
        <xsl:if test="$sw_zeichnungen=1">
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
            <xsl:comment>grundrisse anfang</xsl:comment>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <w:p>
                    <w:pPr><w:pStyle w:val="epi-ueberschrift-2"/></w:pPr>
                    <!-- neue seite -->
                    <w:r><w:br w:type="page"/></w:r>
                    <!-- sprungmarke öffnen und titel auslesen -->
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
                <!-- vorbemerkungen, grundrisse und konkordanz(en) 
                     zum einfügen an templates verweisen -->
                <xsl:apply-templates select="*[not(name()='titel')]"></xsl:apply-templates>
            </wx:sect>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
            <xsl:comment>grundrisse ende</xsl:comment>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>
    </xsl:template>
    
    <!-- vorbemerkungen ausgeben -->
    <xsl:template match="map/note/p">
        <w:p><xsl:apply-templates></xsl:apply-templates></w:p>
    </xsl:template>
    
    <!-- der einzelne grundriss -->
    <xsl:template match="map">
        <xsl:if test="$sw_zeichnungen=1">
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
            <xsl:comment>grundriss anfang</xsl:comment>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <w:p>
                    <w:pPr><w:pStyle w:val="epi-ueberschrift-2"/></w:pPr>
                    <!-- neue seite erzeugen -->
                    <w:r><w:br w:type="page"/></w:r>
                    <!-- sprungmarke öffnen und titel auslesen -->
                    <aml:annotation w:type="Word.Bookmark.Start">
                        <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                        <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                    </aml:annotation>
                    <!-- titel einfügen -->
                    <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>
                    <!-- sprungmarke schießen -->
                    <aml:annotation w:type="Word.Bookmark.End">
                        <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                    </aml:annotation>
                </w:p>
                <xsl:apply-templates select="*[not(name()='titel')]"></xsl:apply-templates>
            </wx:sect>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
            <xsl:comment>grundriss ende</xsl:comment>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>
    </xsl:template>
    
    <xsl:template match="map/title"></xsl:template>
    
    <!-- konkordanz der nummern auf dem grundriss mit den artikelnummern
         
         die konkordanz wurde in der vorangegengenen 
         transformation als tabelle erzeugt und wird hier 
         ebenfalls als tabelle ausgegeben
    -->
    <xsl:template match="map-concordance">
        <xsl:param name="p_concord_sort">
            <xsl:for-each select="row">
                <xsl:sort select="map_object_number/@sortstring" data-type="text" order="ascending" />
                <xsl:copy-of select="." copy-namespaces="no"></xsl:copy-of>
            </xsl:for-each>
        </xsl:param>
        
        <w:tbl>
            <xsl:for-each select="$p_concord_sort/row">
                <!--<xsl:sort select="map_object_number/@sortstring" data-type="number"/>-->
                <w:tr>
                    <!-- spalte für die nummern auf dem grundriss -->
                    <w:tc>
                        <w:tcPr><w:tcW w:w="500" w:type="dxa"/></w:tcPr>
                        <w:p><w:r><w:t><xsl:value-of select="map_object_number"/></w:t></w:r></w:p>
                    </w:tc>
                    <!-- spalte für die komplementären artikelnummern -->
                    <w:tc>
                        <w:tcPr><w:tcW w:w="1200" w:type="dxa"/></w:tcPr>
                        <w:p><w:r><w:t><xsl:value-of select="article_number"/></w:t></w:r></w:p>
                    </w:tc>
                </w:tr>
            </xsl:for-each>
        </w:tbl>
    </xsl:template>
    
</xsl:stylesheet>