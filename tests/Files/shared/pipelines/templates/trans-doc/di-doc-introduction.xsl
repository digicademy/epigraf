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

    <!-- mit diesem stylesheet wird die einleitung eines 
        inschriftenbandes zur endausgabe als textdokument für word verarbeitet -->


    <xsl:template match="introduction">
        <xsl:comment>einleitung anfang</xsl:comment> 
        <wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- gegebenenfalls linke leerseite vorschalten -->
            <xsl:if test="preceding-sibling::*"><xsl:copy-of select="$p_odd-page"/></xsl:if>
            <!-- titel der einleitung -->
            <w:p>
                <w:pPr><w:pStyle w:val="epi-ueberschrift-1"/></w:pPr>
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
            </w:p>
            <!-- kapitel der einleitung ansteuern -->
            <xsl:for-each select="section">
                <!-- kapitelüberschrift (titel), 
                     tabellen-abschnitte und fortsetzungs-abschnitte erhalten keinen titel
                 -->
                <xsl:if test="@titel and not(@data_key='csv_table') and not(@data_key='csv_table_horizontal') and not(@data_key='csv_table_vertical') and not(@data_key='continuation') and not(@sectiontype='table')">
                    <!-- ein titel wird ausgegeben, wenn es ein titel-attribut gibt -->
                    <w:p>
                        <w:pPr>
                            <!-- das format der überschrift folgt dem level-attribut des abschnitts -->
                            <xsl:if test="@level='1'"><w:pStyle w:val="epi-ueberschrift-2"/></xsl:if>
                            <xsl:if test="@level='2'"><w:pStyle w:val="epi-ueberschrift-3"/></xsl:if>
                            <xsl:if test="@level='3'"><w:pStyle w:val="epi-ueberschrift-4"/></xsl:if>
                            <xsl:if test="@level='4'"><w:pStyle w:val="epi-ueberschrift-5"/></xsl:if>
                            <xsl:if test="@level='5'"><w:pStyle w:val="epi-ueberschrift-6"/></xsl:if>
                            <xsl:if test="@level='6'"><w:pStyle w:val="epi-ueberschrift-7"/></xsl:if>
                            <xsl:if test="@level='7'"><w:pStyle w:val="epi-ueberschrift-8"/></xsl:if>
                        </w:pPr>
                        <!-- sprungmarke öffnen und titel auslesen -->
                        <aml:annotation w:type="Word.Bookmark.Start">
                            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                            <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                        </aml:annotation>
                        <!-- titel einfügen -->
                        <w:r><w:t><xsl:value-of select="@name"/></w:t></w:r>
                        <!-- sprungmarke schließen -->
                        <aml:annotation w:type="Word.Bookmark.End">
                            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                        </aml:annotation>
                    </w:p>
                </xsl:if>
                <!-- überschrift (titel) ende -->
                
                <xsl:choose>
                    <!-- abschnitt csv-tabelle -->
                    <xsl:when test="(@data_key='csv_table') or (@data_key='csv_table_horizontal') or (@data_key='csv_table_vertical')">
                        <!-- gegebenenfalls leerzeile einschalten -->
                        <xsl:if test="preceding-sibling::section[1][node()]"><w:pStyle w:val="epi-leerzeile-9pt"/></xsl:if>
                        <!-- tabelle erzeugen -->
                        <w:tbl>
                            <!-- tabellenzeilen ansteuern -->
                            <xsl:for-each select="row">
                                <!-- anzahl spalten (zellen) ermitteln -->
                                <xsl:variable name="v_anzahl_zellen"><xsl:value-of select="count(cell)"/></xsl:variable>
                                <!-- tabellenzeile anlegen -->
                                <w:tr>
                                    <!-- tabellenzellen ansteuern -->
                                    <xsl:for-each select="cell">
                                        <!-- spalte (zelle) anlegen -->
                                        <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                            <w:tcPr>
                                                <!-- zellenbreite je nach anzahl der zellen festlegen -->
                                                <xsl:choose>
                                                    <xsl:when test="$v_anzahl_zellen = 2">
                                                        <xsl:if test="position() = 1"><w:tcW w:w="851" w:type="dxa"/></xsl:if>
                                                    </xsl:when>
                                                    <xsl:otherwise>
                                                        <xsl:if test="position() != 1"><w:tcMar><w:left w:w="100" w:type="dxa"/><w:bottom w:w="20" w:type="dxa"/></w:tcMar></xsl:if>
                                                    </xsl:otherwise>
                                                </xsl:choose>
                                            </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                            <!-- zelleninhalt mittels template einfügen -->
                                            <w:p><xsl:apply-templates select="."></xsl:apply-templates></w:p>
                                        </w:tc>
                                    </xsl:for-each>
                                </w:tr>
                            </xsl:for-each>
                        </w:tbl>
                        <!-- leerzeile an tabelle anschließen -->
                        <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-9pt"/></w:pPr></w:p>
                    </xsl:when>
                    <!-- die formatierung eines abschnitts vom @sectiontype='table'
                            erfolgt analog zum abschnit mit @data_key='csv_table'-->
                    <xsl:when test="@sectiontype='table'">
                        <xsl:if test="preceding-sibling::section[1][node()]"><w:pStyle w:val="epi-leerzeile-9pt"/></xsl:if>
                        <w:tbl>
                            <xsl:for-each select="tr">
                                <xsl:variable name="v_anzahl_zellen"><xsl:value-of select="count(td)"/></xsl:variable>
                                <w:tr>
                                    <xsl:for-each select="td">
                                        <w:tc> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                            <w:tcPr>
                                                <xsl:choose>
                                                    <xsl:when test="$v_anzahl_zellen = 2">
                                                        <xsl:if test="position() = 1"><w:tcW w:w="851" w:type="dxa"/></xsl:if>
                                                    </xsl:when>
                                                    <xsl:otherwise>
                                                        <xsl:if test="position() != 1"><w:tcMar><w:left w:w="100" w:type="dxa"/><w:bottom w:w="20" w:type="dxa"/></w:tcMar></xsl:if>
                                                    </xsl:otherwise>
                                                </xsl:choose>
                                            </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                            <w:p><xsl:apply-templates select="."></xsl:apply-templates></w:p>
                                        </w:tc>
                                    </xsl:for-each>
                                </w:tr>
                            </xsl:for-each>
                        </w:tbl>
                        <w:pStyle w:val="epi-leerzeile-9pt"/>
                    </xsl:when>          
                    <!-- Zitierhinweis wird übersprungen, nur für DIO relevant -->
                    <xsl:when test="@data_key='citationnote'"></xsl:when>               
                    <!-- die anderen abschnitte (kapitel) -->
                    <xsl:otherwise>
                        <xsl:for-each select="p|h|bl">
                            <!-- überschriften und absätze werden an die 
                                 entsprechenden templates verwiesen,
                                 sie befinden sich in di-doc-commons.xsl
                            -->
                            <xsl:apply-templates select="."></xsl:apply-templates>
                        </xsl:for-each>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </wx:sect>
        <xsl:comment>einleitung  ende</xsl:comment> 
    </xsl:template>

</xsl:stylesheet>