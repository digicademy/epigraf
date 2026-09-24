<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">

<!-- 
    dieses stylesheet enthält templates für drei abteilungen des inschriftenbandes,
    die nicht in einem einzelnen, speziellen stylesheet verarbeitet werden:
    * vorwort
    * einleitung
    * abkürzungen
    
    hierin enthalten sind auch templates zur auflösung von csv-tabellen
    
    es wird aufgerufen in di-trans3.xsl
-->


    <xsl:import href="../commons/di-switch.xsl"/>
    <xsl:import href="di-trans3-commons.xsl"/>
    
    <!-- vorwort -->
    <xsl:template match="prefaces">
        <!-- der inhalt der <p>-tags wird aufgelöst -->
        <prefaces>
            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="title" copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <content><xsl:copy-of select="content/@*"/>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:for-each select="content/p">
                    <xsl:apply-templates select="." />
                </xsl:for-each>
            </content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="datum" copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="unterschrift" copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <citationnote data_key="citationnote">
               <xsl:copy-of select="*[@data_key='citationnote']/content/p" copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </citationnote>
        </prefaces><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- einleitung -->
    <xsl:template match="introduction">
        <introduction>
            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="title" copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- <section>s ansteuern und an template verweisen -->
            <xsl:for-each select="section">
                <xsl:apply-templates select="." />
            </xsl:for-each>
        </introduction>
    </xsl:template>
    
    <!-- abkürzungen -->
    <xsl:template match="abbreviations">
        <abbreviations indexing="1">
            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="title" copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="content">
                <xsl:choose>
                    <!-- wenn es sich um eine tabelle handelt -->
                    <xsl:when test="@type='table'">
                        <table><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:for-each select="p">
                                <row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <cell><xsl:apply-templates></xsl:apply-templates></cell>
                                </row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:for-each>
                        </table><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:when>
                    <!-- wenn es sich um eine csv-tabelle (mit rauten # als trennzeichen) handelt -->
                    <xsl:when test="(@type='csv_table') or (@type='csv_table_horizontal') or (@type='csv_table_vertical')">
                        <table>
                            <!-- die tabellenzeilen wurde in der vorangegangenen transformation 
                                    als <t>-elemente angelegt,
                            diese nun ansteuern, mit einem <cell>-element gewissermaßen auskleiden 
                            und den inhalt an ein template verweisen, das die rauten # 
                            durch inverse <cell>-tags ersetzt-->
                            <xsl:for-each select="t">
                                <row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <cell><xsl:apply-templates select="."></xsl:apply-templates></cell>
                                </row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:for-each>
                        </table>
                    </xsl:when>
                    <xsl:otherwise><xsl:apply-templates></xsl:apply-templates></xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </abbreviations>
    </xsl:template>

    <!-- abschnitte der einleitung 
    die abschnitte sind nicht hierarchisch verschachtelt,
    die hierarchie kommt nur in der nummerierung der überschriften zum ausdruck
    -->
    <xsl:template match="section">
        <section>
            <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <xsl:choose>
                <!-- csv-tabellen in der einleitung-->
                <xsl:when test="(@data_key='csv_table') or (@data_key='csv_table_horizontal') or (@data_key='csv_table_vertical')">
                    <!-- die tabellenzeilen wurde in der vorangegangenen transformation 
                            als <t>-elemente angelegt,
                            diese nun ansteuern, mit einem <cell>-element gewissermaßen auskleiden 
                            und den inhalt an ein template verweisen, das die rauten # 
                            durch inverse <cell>-tags ersetzt-->
                    <xsl:for-each select="content/t">
                        <row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <cell><xsl:apply-templates select="."></xsl:apply-templates></cell>
                        </row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </xsl:when>
                <!-- abschnitte vom typ tabelle -->
                <xsl:when test="@sectiontype='table'"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="content/tr">
                        <xsl:copy><xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:for-each select="td">
                                <xsl:copy><xsl:copy-of select="@*"  /><xsl:apply-templates /></xsl:copy><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:for-each>
                        </xsl:copy><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </xsl:when>
                <!-- andere abschnitte in der einleitung-->
                <xsl:otherwise><xsl:apply-templates select="content" /></xsl:otherwise>
            </xsl:choose>
        </section><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- tabellen-abschnitte der einleitung ansteuern und alle zutreffenden templates aufrufen -->
    <xsl:template match="t">
        <xsl:apply-templates></xsl:apply-templates>
    </xsl:template>
    
    <!-- textknoten in den tabellen-abschnitten der einleitung -->
    <xsl:template match="t/text()">
        <xsl:choose>
            <!-- wenn der textknoten rauten # enthält, werden diese durch ein schließendes und wieder öffnendes (inverses> <cell>-tag ersetzt -->
            <xsl:when test="contains(.,'#')">
                <!-- bis zur ersten # -->
                <xsl:value-of select="substring-before(.,'#')"/>
                <!-- tags einfügen -->
                <xsl:text disable-output-escaping="yes">&lt;/cell&gt;&#x000A;&lt;cell&gt;</xsl:text>
                <xsl:choose>
                    <!-- wenn danach noch weitere # folgen, werden sie suckzessive über eine template-schleife angesteuert und ersetzt -->
                    <xsl:when test="contains(substring-after(.,'#'), '#')">
                        <!-- schleife aufrufen -->
                        <xsl:call-template name="loop-cells">
                            <!-- parameter mit dem string nach der ersetzten # mitgeben -->
                            <xsl:with-param name="p_string"><xsl:value-of select="substring-after(.,'#')"/></xsl:with-param>
                        </xsl:call-template>
                    </xsl:when>
                    <!-- wen der reststring keine # mehr enthält, wird er direkt ausgegeben -->
                    <xsl:otherwise><xsl:value-of select="substring-after(.,'#')"/></xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <!-- wenn der gesamte textknoten keine # enthält wird er direkt ausgegeben -->
            <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <!-- schleife zur ersetzung von # durch inverse cell-tags in csv-tabellen -->
    <xsl:template name="loop-cells">
        <xsl:param name="p_string"></xsl:param>
        <xsl:choose>
            <xsl:when test="contains($p_string, '#')">
                <xsl:value-of select="substring-before($p_string, '#')"/>
                <xsl:text disable-output-escaping="yes">&lt;/cell&gt;&#x000A;&lt;cell&gt;</xsl:text>
                <xsl:call-template name="loop-cells">
                    <xsl:with-param name="p_string"><xsl:value-of select="substring-after($p_string, '#')"/></xsl:with-param>
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise><xsl:copy-of select="$p_string"/></xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>