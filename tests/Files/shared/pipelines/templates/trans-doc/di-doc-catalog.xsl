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
    <xsl:import href="di-doc-article.xsl"/>
    

<!-- dieses stylesheet erzeugt den katalog der inschriftenartikel  -->
   
  <!-- katalog der inschriftenartikel aufrufen und formatieren -->
    <xsl:template match="articles">
        <!-- prüfen ob laut option in der pipeline der katalog ausgegeben werden soll -->
        <xsl:if test="not($sw_katalog='0')">
            <xsl:comment>katalog anfang</xsl:comment> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- die titelseite des katalogs wird ausgegeben, wenn dies gemäß option vorgesehen ist, 
    aber auch unabhängig hiervon, wenn die einleitung oder die titelei oder das vorwort ebenfalls auszugeben ist -->
            <xsl:if test="$sw_katalogtitel='1' or $sw_einleitung='1' or $sw_titelei='1' or $sw_vorwort='1'">
                <wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- wenn es einen vorausgehenden abschnitt gibt, wird eine linke leerseite vorgeschaltet -->
                    <xsl:if test="preceding-sibling::*"><xsl:copy-of select="$p_odd-page"/></xsl:if>
                    <!-- katalogtitel formatieren -->
                    <w:p>
                        <w:pPr>
                            <w:pStyle w:val="epi-ueberschrift-1"/>
                            <w:outlineLvl w:val="0"/>
                        </w:pPr>
                        <!-- sprungmarke einfügen und titel auslesen -->
                        <aml:annotation w:type="Word.Bookmark.Start">
                            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                            <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                        </aml:annotation>
                        <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>
                        <!-- sprungmarke schließen -->
                        <aml:annotation w:type="Word.Bookmark.End">
                            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                        </aml:annotation> 
                    </w:p>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- seitenumbruch -->
                <wx:sect>
                    <w:p>
                        <w:pPr><w:pageBreakBefore/></w:pPr>
                    </w:p>
                    <!-- gegebenenfalls linke leerseite einfügen -->
                    <xsl:if test="preceding-sibling::*"><xsl:copy-of select="$p_odd-page"/></xsl:if>
                </wx:sect>
            </xsl:if>
            <!-- seitenumbruch -->
            <wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <w:p>
                    <w:pPr><w:pageBreakBefore/></w:pPr>
                </w:p>
                <xsl:if test="preceding-sibling::*"><xsl:copy-of select="$p_odd-page"/></xsl:if>
                
         <!--alle artikel ansteuern-->
                <xsl:for-each select="article">
                    <!-- 
                      jeden artikel an das betreffende template verweisen;
                      der artikelaufbau erfolgt in di-doc-article.xsl
                    -->
                    <xsl:apply-templates select="."/>
                </xsl:for-each> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <!-- als letzten absatz die angabe zur einspaltigen darstellung anfügen aller artikel-->
                <w:p wsp:rsidR="00533250" wsp:rsidRDefault="00533250">
                    <w:pPr>
                        <w:sectPr wsp:rsidR="00533250" wsp:rsidSect="00533250">
                            <w:type w:val="continuous"/>
                            <w:pgSz w:w="11906" w:h="16838"/>
                            <w:pgSz w:w="11906" w:h="16838" />
                            <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                            <w:cols w:num="1" w:space="708"/>
                            <w:docGrid w:line-pitch="360"/>
                        </w:sectPr>
                    </w:pPr>
                </w:p>
            </wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:comment>katalog ende</xsl:comment> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if></xsl:template>    
</xsl:stylesheet>