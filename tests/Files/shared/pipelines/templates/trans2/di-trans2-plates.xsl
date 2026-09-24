<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="di-trans2-commons.xsl"/>

    <!-- 
        dieses stylesheet erzeugt den abschnitt für die 
        titelseite des tafelteils und die bildnachweise.
        
        es wird aufgerufen in di-trans2-volume.xsl
    -->   


    <xsl:template name="plates">
        <!-- aus den textbausteinen des bandes werden die titelseite für den tafelteil 
    und die seite mit den bildnachweisen ausgelesen-->
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- abschnitt anlegen, das attribut indexing=1 anfügen, 
                damit der abschnitt im inhaltsverzeichnis des bandes gelistet wird,
                vorhandene attribute kopieren-->
        <plates indexing="1">
            <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- abschnittsüberschrift -->
            <xsl:call-template name="section-title"></xsl:call-template>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- unterabschnitt mit den bildnachweisen ansteuern -->
            <xsl:for-each select="section">
                <!-- auf vorhandensein des gültigen datenschlüssels testen, 
                        wenn der test negativ ausfällt, eine fehlermeldung ausgeben-->
                <xsl:choose>
                    <xsl:when test="@data_key='plates_credits'">
                        <!-- container für die bildnachweise anlegen, vorhandene attribute kopieren -->
                        <plates_credits>
                            <xsl:copy-of select="@*"/>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <!-- überschrift -->
                            <xsl:call-template name="section-title"></xsl:call-template>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <!-- zum inhalts-element des abschnitts navigieren -->
                            <xsl:for-each select="items/item/content">
                                <!-- element neu anlegen, ein element <p> als inneren rahmen einfügen,
                                        den weiteren inhalt zum einfügen an templates verweisen-->
                                <content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <p><xsl:apply-templates></xsl:apply-templates></p><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <!-- nota bene: wenn zeilenumbrüche <nl/> vorhanden sind, 
                                            wird das element <p> an den betreffendnen stellen gesplittet
                                            (siehe template nl in di-trans2-commons.xsl)-->
                                </content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:for-each>
                        </plates_credits><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:when>
                    <xsl:otherwise><error>Fehler: Dem Abschnitt &quot;<xsl:value-of select="@name"/>&quot; wurde kein Datenschlüssel zugewiesen. Wählen Sie einen Datenschlüssel aus oder wenden Sie sich an den Administrator!</error></xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </plates><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
</xsl:stylesheet>