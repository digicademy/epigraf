<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
 
 <!-- 
     in diesem stylesheet wird die vorsatzseite zu den bildtafeln 
     mit den bildnachweisen konfiguriert,

     es wird aufgerufen in di-trans3.xsl 
 -->
    
    <xsl:template match="plates">
        <!-- element neu anlegen, attribute und titel kopieren -->
        <xsl:copy>
            <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="title" copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- template zur formatierung der bildnachweise aufrufen -->
            <xsl:apply-templates select="plates_credits"></xsl:apply-templates>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:copy>
    </xsl:template>
    
    <!-- bildnachweise formatieren -->
    <xsl:template match="plates_credits">
        <xsl:copy>
            <xsl:copy-of select="@*"/><xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- absätze, die nicht als leer gekennzeichnet sind, aufrufen und kopieren -->
            <xsl:for-each select="*[not(text()='§')]">
                <xsl:copy-of select="." copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </xsl:copy>
    </xsl:template>
</xsl:stylesheet>