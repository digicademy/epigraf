<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="di-trans2-commons.xsl"/>
    
    <!-- 
        dieses stylesheet stößt die transformation der 
        einleitung und der einleitungskapitel eines inschriftenbands an;
        
        es wird in di-trans2-book.xsl aufgerufen.
    -->
    
    <!-- abschnitt einleitung erzeugen -->
    <xsl:template name="introduction">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- 
            einleitung aus dem entsprechenden element <section> der rohdaten 
            und der vorangegangenen transformationen anlegen,
            vorhandene attribute kopieren, atribut @indidizieren=1 hinzufügen, 
            damit der abschnitt im inhaltsverzeichnis des inschriftenbands erscheint.
        -->
        <introduction>
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="indexing">1</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- abschnittsüberschrift erzeugen -->
            <xsl:call-template name="section-title"></xsl:call-template>
            
            <!-- die einzelnen kapitel und unterkapitel der einleitung werden aus der 
                hierarchie herausgelöst und flach aneinander gereiht -->
            <xsl:for-each select=".//section">
                <!-- aus dem kapitelnamen die kapitelnummer und die 
                    bezeichnung des kapitels herauslosen und speichern  -->
                <xsl:variable name="v_caption"><xsl:value-of select="@name"/></xsl:variable>
                <xsl:variable name="v_kapitelnummer"><xsl:value-of select="substring-before($v_caption,' ')"/></xsl:variable>
                <xsl:variable name="v_titel"><xsl:value-of select="substring-after($v_caption,' ')"/></xsl:variable>
                <!-- kapitel (<abschnitt>)neu anlegen, vorhandene attribute kopieren -->
                <section>
                    <xsl:copy-of select="@*"/>
                    <!-- die kapitelnummer und die kapitelüberschrift
                            als attribute mitgegeben-->
                    <xsl:choose>
                        <!-- für nummerierte kapitel -->
                        <xsl:when test="starts-with($v_caption,'1') or
                            starts-with($v_caption,'2') or 
                            starts-with($v_caption,'3') or 
                            starts-with($v_caption,'4') or 
                            starts-with($v_caption,'5') or 
                            starts-with($v_caption,'6') or 
                            starts-with($v_caption,'7') or 
                            starts-with($v_caption,'8') or 
                            starts-with($v_caption,'9')">
                            <xsl:attribute name="kapitelnummer"><xsl:value-of select="$v_kapitelnummer"/></xsl:attribute>
                            <xsl:attribute name="titel"><xsl:value-of select="$v_titel"/></xsl:attribute>
                        </xsl:when>
                        <!-- nicht nummerierte kapitel -->
                        <xsl:otherwise>
                            <xsl:choose>
                                <!-- für die abschnitte Fortsetzung und Tabelle werden 
                                        keine überschriften ausgelesen -->
                                <xsl:when test="@data_key='continuation'"></xsl:when>
                                <xsl:when test="(@data_key='csv_table') or (@data_key='csv_table_horizontal') or (@data_key='csv_table_vertical')"></xsl:when>
                                <!-- für abschnitte ohne kapitelnummer wird das gesamte 
                                        @caption-attribut als titel ausgelesen -->
                                <xsl:otherwise><xsl:attribute name="titel"><xsl:value-of select="$v_caption"/></xsl:attribute></xsl:otherwise>
                            </xsl:choose>
                        </xsl:otherwise>
                    </xsl:choose>
                    <!-- kapitelinhalt ansteuern und an das betreffende 
                            template (in di-trans2-commons.xsl)verweisen -->
                    <xsl:for-each select="items/item[@itemtype='chapter']">
                        <xsl:apply-templates select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </section><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </introduction>                
    </xsl:template>

</xsl:stylesheet>