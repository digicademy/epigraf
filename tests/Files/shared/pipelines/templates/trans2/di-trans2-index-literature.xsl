<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans2-commons.xsl"/>
    <xsl:import href="di-trans2-indices-commons.xsl"/>
    
    <!-- 
        in diesem stylesheet wird das literatur-und quellenverzeichnis 
        für den inschriftenband zusammengestellt,
        
        es wird aufgerufen im sylesheet di-trans2-book.xsl
    -->
    
    <!-- literaturausgabe für die pipeline band -->    
    <xsl:template name="biblio">

        <!-- literatur-abschnitt im band aufsuchen -->
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- abschnitt anlegen, vorhandene attribute kopieren, attribut @indexing="1" einfügen, 
                damit der abschnitt im inhaltsverzeichnis erscheint -->
        <literatures>
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="indexing">1</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- titel auslesen -->
            <xsl:call-template name="section-title" />
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- literaturregister aufsuchen -->
            <xsl:for-each select="ancestor::book/indices/index[@propertytype='literature']">
                <xsl:for-each select="item[not(@ishidden='1')]">
                    <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                    <section>
                        <xsl:copy-of select="@*"/>
                        <xsl:attribute name="indexing">1</xsl:attribute>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:copy-of select="lemma" />
                        <xsl:call-template name="titel_sortieren"></xsl:call-template>
                    </section><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    
                    <!--    <xsl:copy-of select="."/>-->
                </xsl:for-each>
            </xsl:for-each>
        </literatures><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- literaturtitel sortieren -->
    <xsl:template name="titel_sortieren">
        <xsl:for-each select="item[not(@ishidden='1')]">
            <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <item log2="lit-item1">
                <xsl:copy-of select="@*"/>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:copy-of select="lemma"/>
                <xsl:call-template name="titel_sortieren"></xsl:call-template>
<!--                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:copy-of select="sections" copy-namespaces="no"></xsl:copy-of>-->
            </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template> 
</xsl:stylesheet>