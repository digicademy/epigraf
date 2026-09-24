<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">  
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:output indent="no"/>  

    <!-- 
        dieses stylesheet enthält templates und weitere instanzen, 
        die in verschiedenen anderen stylsheets 
        derselben transformationsstufe aufgerufen werden
    -->


    <!-- für die section-elemente wird ein attribute-set angelegt -->
    <xsl:attribute-set name="attributs-sections">
        <xsl:attribute name="id" select="@id" />
        <xsl:attribute name="parent_id" select="@parent_id" />    
        <xsl:attribute name="articles_id" select="@articles_id" />   
        <xsl:attribute name="sectiontype" select="@sectiontype" />
        <xsl:attribute name="published" select="@published" />
        <xsl:attribute name="name" select="@name" />
        <xsl:attribute name="number" select="@number" />
        <xsl:attribute name="level" select="@level" />
        <xsl:attribute name="alias" select="@alias" />
        <xsl:attribute name="kategorie" select="items/item/property[@propertytype='captions']/lemma" />
        <xsl:attribute name="data_key" select="substring-after(items/item[@itemtype='chapter']/property[@propertytype='datakeys']/norm_iri,'di_')" />
        <xsl:attribute name="norm_iri" select="items/item/property[@propertytype='captions']/norm_iri" />       
    </xsl:attribute-set> 


<!-- in den rohdaten flach aus der datenbank ausgespielte 
        abschnitte werden verschachtelt -->
    <xsl:template name="sections-hierarchisieren">
        <!-- sections der obersten ebene neu anlegen, vorhandene attribute kopieren
         weitere attribute erzeugen: @kategorie (in den artikeln) und @data_key (im band)
    -->
        <section xsl:use-attribute-sets="attributs-sections">
            <!-- alle benötigten untergeordneten element mit inhalt durch spezielle templates einfügen-->
            <xsl:apply-templates></xsl:apply-templates>
            <!-- untergeordnete sections recursiv ansteuern und an dasselbe template verweisen -->
            <xsl:for-each select="following-sibling::section[@parent_id=current()/@id]"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:call-template name="sections-hierarchisieren"/>
            </xsl:for-each>
        </section>
    </xsl:template>
    
    <!-- BEGINN: elemente in <section> verarbeiten-->
    <xsl:template match="path"><!-- wird eliminiert --></xsl:template>
    <xsl:template match="status"><!-- wird eliminiert --></xsl:template>
    
    <!-- elemente <comment> kopieren wo sie auftreten -->
    <xsl:template match="comment"><xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:template>
    
    <!-- den container <items> wieder anlegen, die darin enthaltenen 
        elemente <item> an die zutreffenden templates verweisen -->
    <xsl:template match="items">
        <items><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="item"><xsl:apply-templates select="."></xsl:apply-templates></xsl:for-each>
        </items>
    </xsl:template>
    
    <!-- elemente <item> restrukturieren -->
    <xsl:template match="item">
        <!-- element neu anlegen, attribute kopieren -->
        <item><xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- child-elemente ansteuern -->
            <xsl:for-each select="*">
                <xsl:choose>
                    <!-- nicht benötigte elemente werden ausgeschieden -->
                    <xsl:when test="not(node()) and not(@*)"><!-- leer und ohne attribut --></xsl:when>
                    <xsl:when test="text()='0'"><!-- entfällt --></xsl:when>
                    <xsl:when test="self::sectionpath"><!-- entfällt --></xsl:when>
                    <xsl:when test="self::created"><!-- entfällt --></xsl:when>
                    <xsl:when test="self::modified"><!-- entfällt --></xsl:when>
                    <!-- weiterhin benötigte elemente werden kopiert -->
                    <xsl:otherwise><xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    <!-- ENDE: element in <section> -->    

    <xsl:template match="created"><!-- k. w. --></xsl:template>
    <xsl:template match="modified"><!-- k. w. --></xsl:template>

</xsl:stylesheet>