<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="di-trans2-commons.xsl"/>
 
 <!-- 
     dieses stylesheet erzeugt die zusammenstellung der marken (hausmarken, meisterzeichen u. dgl.),
     es wird aufgerufen in di-trans2-volume.xsl.
     
     zuerst wird im abschnitt volume der unterabschnitt marken angesteuert,
     daraus wird der rahmen (element <marken>) für den neuen markenabschnitt erstellt,
     von dort wird zum index der markentypen in den manage_lists navigiert 
     und zu jedem typ ein element <brandtype> angelegt, 
     von dort aus wird zum index der marken navigiert um jeweils alle zum typ passenden
     marken anzusteuern und mit einem element <item> zu erfassen.
     
     das ergebnis ist eine nach typen gruppierte liste der marken.
 -->
 
 
    <!-- abschnitt des inschriftentbandes zu den marken aufrufen  -->
    <xsl:template match="section[@data_key='marks' or @data_key='brands']">
        
        <!-- der alte datenschlüssel lautete marks, 2025.02.06 ersetzt durch brands -->
        <!-- 
            abschnitt für die marken erzeugen, attribut @indexing=1 mitgeben, 
            damit der abschnitt im inhaltsverzeichnis des bands angezeigt wird,
            vorhandene attribute kopieren
        -->
        <brands>
            <xsl:attribute name="indexing">1</xsl:attribute>
            <xsl:copy-of select="@*"/>
            <!-- abschnittsüberschrift -->
            <xsl:call-template name="section-title"></xsl:call-template>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- zum index der markentypen navigieren, jeden typ ansteuern und 
                 die typ-bezeichnung in einer variablen speichern   -->
            <xsl:for-each select="ancestor::book/manage_lists/index[@propertytype='brandtypes']/item">
                <xsl:variable name="v_typ"><xsl:value-of select="lemma"/></xsl:variable>
                <!-- element <brandtype> anlegen, vorhandene atribute kopieren -->
                <brandtype>
                    <xsl:copy-of select="@*"/>
                    <xsl:copy-of select="lemma/@sortstring"></xsl:copy-of>
                    <xsl:attribute name="name"><xsl:value-of select="lemma"/></xsl:attribute>
                    <xsl:attribute name="sign"><xsl:value-of select="unit"/></xsl:attribute>
                    
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- bezeichnung des markentyps -->
                    <title><xsl:value-of select="lemma"/></title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- zum index der marken navigieren und die zum typ 
                            passenden marken (element <item>) kopieren -->
                    <xsl:for-each select="ancestor::book/indices/index[@propertytype='brands']/item[@brandtype=$v_typ]">
                        <xsl:copy-of select="."/>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </brandtype><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </brands><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
    </xsl:template>
        
</xsl:stylesheet>