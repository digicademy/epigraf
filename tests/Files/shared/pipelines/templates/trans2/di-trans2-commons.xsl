<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">


<!-- dieses stylesheet enthält templates der transformationsstufe 2, die in anderen stylesheets 
        der transformationsstufe 2 aufgerufen werden  -->

    <!-- überschriften zu den abschnitten des inschriftenbands-->
    <xsl:template name="section-title">
        <title><xsl:value-of select="@name"/></title>
    </xsl:template>  
  
    <!-- überschriften der einleitungskapitel -->
    <xsl:template match="h"><h><xsl:copy-of select="@data-link-value"/><xsl:apply-templates/></h></xsl:template>


    <!-- gesperrt ausgezeichnete wörter (in der einleitung) -->
    <xsl:template match="g"><xsl:copy-of select="."/></xsl:template>
    
    <!-- tabellenabschnitte in einleitung und artikeln aus tr und td -->    
    <xsl:template match="tr"><xsl:copy-of select="."/></xsl:template>
    
    <!-- element <lemma> außerhalb der register unterdrücken -->
    <xsl:template match="lemma[ancestor::section]"></xsl:template>
    
    <!-- elemente in den textfeldern (<content>) der einleitung -->
    <xsl:template match="app1"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="quot"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="rec_lit"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="rec_extern"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="i"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="b"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="u"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="sup"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="sups"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="k"><xsl:copy-of select="."/></xsl:template>
    
    <!-- transkriptionswerkzeuge in der einleitungstabelle-->
    <xsl:template match="bl">
        <xsl:text disable-output-escaping="yes">&#x003C;/p&#x003E;</xsl:text>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:copy-of select="."/>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
        <xsl:text disable-output-escaping="yes">&#x003C;p&#x003E;</xsl:text>
    </xsl:template>
    <xsl:template match="cpl"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="abr"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="add"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="vz"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="z"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="all"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="del"><xsl:copy-of select="."/></xsl:template>
    
    
    
    <xsl:template match="folge">
        <folge><xsl:copy-of select="@*"/><xsl:apply-templates></xsl:apply-templates></folge>
    </xsl:template>
    
    <!-- elemente in den sections der einleitung -->
    <xsl:template match="footnotes"><xsl:if test="node()"><xsl:copy-of select="."/></xsl:if></xsl:template>
    <xsl:template match="links"><xsl:if test="node()"><xsl:copy-of select="."/></xsl:if></xsl:template>
    <xsl:template match="meta"><!--<xsl:copy-of select="."/>--></xsl:template>
    <xsl:template match="tables"><!--<xsl:copy-of select="."/>--></xsl:template>    
    <xsl:template match="property[ancestor::volume]"></xsl:template>
    <xsl:template match="lemma[parent::property[ancestor::volume]]"></xsl:template>
    

<!-- =====ELEMENTE <content> und <comment>========== -->

    <!-- inhaltselemente (<content>) in den kapiteln der einleitung -->
    <xsl:template match="content[ancestor::volume][parent::item[@itemtype='chapter']]">
        <content log2="cont1">
            <xsl:attribute name="type">
                <xsl:choose>
                    <xsl:when test="ancestor::section[@data_key='csv_table']">csv_table</xsl:when>
                    <xsl:when test="ancestor::section[@data_key='csv_table_horizontal']">csv_table_horizontal</xsl:when>
                    <xsl:when test="ancestor::section[@data_key='csv_table_vertical']">csv_table_vertical</xsl:when>
                    <xsl:when test="ancestor::section[@sectiontype='table']">table</xsl:when>
                    <xsl:otherwise>paragraphs</xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:choose>
                <xsl:when test="ancestor::section[@data_key='csv_table']"><t><xsl:apply-templates/></t></xsl:when>
                <xsl:when test="ancestor::section[@data_key='csv_table_horizontal']"><t><xsl:apply-templates/></t></xsl:when>
                <xsl:when test="ancestor::section[@data_key='csv_table_vertical']"><t><xsl:apply-templates/></t></xsl:when>
                <xsl:when test="ancestor::section[@sectiontype='table']"><xsl:apply-templates></xsl:apply-templates></xsl:when>
                <xsl:otherwise><p><xsl:apply-templates/></p></xsl:otherwise>
            </xsl:choose>
        </content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>  
    
    <!-- notizfelder in der einleitung -->
    <xsl:template match="comment[ancestor::volume]">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <p><xsl:apply-templates/></p><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
<!-- ======ZEILENUMBRÜCHE <nl> anfang========== -->   
    
    <!-- zeilenumbrüche in textfeldern und notizfeldern -->
    <xsl:template match="nl[parent::content]">
        <xsl:choose>
            <!-- 1. in csv-tabellen mit # als separator für eine tabellarische formatierung:
                der gesamte text wurde in dem vorangegeangen 
                template match=content in ein element <t> eingefügt,
                das nun an den elementen <nl> durch inverse tags gesplittet wird

                vorher:
                <t>text<nl/>text<nl/>text<nl/></t>
                
                nachher:
                <t>text</t>
                <t>text</t>
                <t>text</t>
            -->
            <xsl:when test="ancestor::section[(@data_key='csv_table') or (@data_key='csv_table_horizontal') or (@data_key='csv_table_vertical')]">
                <xsl:text disable-output-escaping="yes">&#x003C;/t&#x003E;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x003C;t&#x003E;</xsl:text>
            </xsl:when>
            
            <!-- 2. in einfachen textfeldern:
                der gesamte text wurde in dem vorangegeangen 
                template match=content in ein element <p> eingefügt,
                das nun an den elementen <nl> durch inverse tags gesplittet wird
                
                vorher:
                <p>text<nl/>text<nl/>text<nl/></p>
                
                nachher:
                <p>text</p>
                <p>text</p>
                <p>text</p>
            -->
            <xsl:otherwise> 
                <!-- testfelder der register bleiben ausgenommen -->
                <xsl:if test="not(ancestor::property)">
                    <xsl:text disable-output-escaping="yes">&#x003C;/p&#x003E;</xsl:text>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:text disable-output-escaping="yes">&#x003C;p&#x003E;</xsl:text>
                </xsl:if>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <!-- zeilenumbrüche in den notizfelder der einleitung:
        der gesamte text wurde durch das vorangegangene 
        template match=comment in ein element <p> eingefügt,
        das nun an den elementen <nl> durch inverse tags gesplittet wird
        
        vorher:
        <p>text<nl/>text<nl/>text<nl/></p>
        
        nachher:
        <p>text</p>
        <p>text</p>
        <p>text</p>
    -->
    <xsl:template match="nl[parent::comment][ancestor::volume]">
        <xsl:text disable-output-escaping="yes">&#x003C;/p&#x003E;</xsl:text>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:text disable-output-escaping="yes">&#x003C;p&#x003E;</xsl:text>
    </xsl:template>
    
<!-- ======ZEILENUMBRÜCHE <nl> ende========== -->
    
    
    <xsl:template match="anchor"><xsl:copy-of select="."></xsl:copy-of></xsl:template>
</xsl:stylesheet>