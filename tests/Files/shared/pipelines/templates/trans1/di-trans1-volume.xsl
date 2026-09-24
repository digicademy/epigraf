<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans1-commons.xsl"/>
    <xsl:output indent="no"/>  

<!-- mit diesem stylesheet wird der abschnitt volume (band) angelegt;
     es wird aufgerufen in di-trans1.xsl.
     
     der abschnitt volume umfasst alles, was nicht 
     zum katalog der inschriftenartikel oder 
     zu den registern gehört.
    -->

    <xsl:template name="volume">
        <!-- parameter aus dem übergeordneten stylesheet  -->
        <xsl:param name="p_project-name"></xsl:param>
        <xsl:param name="p_project-signature"></xsl:param>
        <xsl:param name="p_project-database"></xsl:param>
        
        <!-- abschnitt anlegen -->
        
            <!-- den bandartikel ansteuern und transformieren-->
            <xsl:for-each select="article[@articletype='epi-book']">
              <volume>
                <xsl:copy-of select="@id"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- allgemeine angaben kopieren -->
                <xsl:for-each select="*[not(self::sections)]"><xsl:copy-of select="."/></xsl:for-each>
                <!-- die sections zum hierarchisieren anweisen -->
                <xsl:for-each select="sections/section[@level='1']">
                    <xsl:call-template name="sections-hierarchisieren"/>
                    <!-- die weitere transformation erfolgt von diesem template ausgehend -->
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
              </volume>
            </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
    </xsl:template>

</xsl:stylesheet>


