<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0"> 
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans1-commons.xsl"/>
    
    <xsl:output indent="no"/>  

<!-- der katalog mit den inschriftenartikeln wird erzeugt -->    
    <xsl:template name="articles">
        <xsl:param name="p_project-name"></xsl:param>
        <xsl:param name="p_project-signature"></xsl:param>
        <xsl:param name="p_project-database"></xsl:param>
        
<!-- der katalog wird in zwei paramtern vorformatiert und sortiert -->
        <!-- START: parameter für die sortierung des katalogs -->
       
        <xsl:param name="p_katalog0">
        <!-- 
            der katalog wird ausgelesen, 
            die artikel werden neu angelegt, attribute und elemente kopiert und um sortierkriterien erweitert:
            die artikelsignatur wird als attribut mitgegeben
            der datierungsindex wird ermittelt und als attribut mitgegeben
            die standorte werden ermittelt und als elemente mitgegeben
            der objekttyp wird ermittelt und als element mitgegeben
         -->             
            <xsl:for-each select="article[@articletype='epi-article']">
                <!-- datierungsschlüssel für die nachfolgende sortierung ermitteln -->
                <xsl:variable name="v_dates_all">
                    <!-- alle datierungsschlüssel innerhalb des artikels aufsuchen, 
                            außer demjenigen für die zusätzliche tagesdatierung -->
                    <xsl:for-each select="sections/section//items/item[not(@itemtype='dates')]/date_sort[node()]">
                        <!-- datierungsschlüssel sortieren -->
                        <xsl:sort />
                        <!-- datierungsschlüssel kopieren -->
                        <xsl:copy-of select="."/>
                    </xsl:for-each>
                </xsl:variable>
                
                <!-- der erste = älteste datierungsschlüssel wird ausgelesen-->
                <xsl:variable name="v_dates_old"><xsl:value-of select="$v_dates_all/*[1]"/></xsl:variable>
 
                <!-- wenn bei der datierung des objekts zusätzlich eine 
                        tagesdatierung angegeben ist, wird diese ausgelesen -->
                <xsl:variable name="v_dates_day">
                    <xsl:value-of select="sections/section[@sectiontype='conditions']/items/item[@itemtype='dates']/date_value"/>
                </xsl:variable>
                
                <article>
                    <xsl:copy-of select="@*"/>
                    <!-- die signatur des artikels für die anschließende weitersortierung als attribut mitgeben-->
                    <xsl:attribute name="article_signature"><xsl:value-of select="sections/section[@sectiontype='signatures']/items[1]/item[1]/value"/></xsl:attribute>
                    
                    <!-- datierungsschlüssel als attribut mitgeben -->
                    <xsl:attribute name="dating_index" select="$v_dates_old" />
                    <!-- optionales tagesdatum als zweitkriterium der datierung mitgeben-->
                    <xsl:attribute name="dating_index_add" select="$v_dates_day" />
                    
                    <!-- den standort für die anschließende weitersortierung und für die kopfzeile ermitteln -->
                    <locations>
                        <xsl:for-each select="sections/section[@sectiontype='locations']/items/item[@itemtype='locations']">
                            <!-- (a) mit den folgenden variablen wird festgestellt, ob der standort als 
                                    zusätzlicher (mit dem textbaustein primesite) markiert ist -->
                            <xsl:variable name="v_zweitstandort1"><xsl:value-of select="content/rec_lit/@data-link-target"/></xsl:variable>
                            <xsl:variable name="v_zweitstandort2"><xsl:value-of select="ancestor::article/links/link[@to_id=$v_zweitstandort1]/property/lemma"/></xsl:variable>
                            
                            <!-- die nun folgende variable stellt fest, ob der standort
                                 alternativ zu (a) mit dem kontrollkästchen "Letztbekannter Standort" markiert wurde, 
                                 in diesem fall enthält das element <value> die ziffer 1 -->
                            <xsl:variable name="v_lastlocation" select="value"/>
           
                            <!-- die ausgabe wird eingeschränkt auf den jeweils obersten standort 
                                    und darunter stehende, die als 
                                    primesite (alte methode) oder 
                                    letztbekannt (neue methode) markiert sind -->
                            <xsl:if test="not(preceding-sibling::item[@itemtype='locations']) or $v_zweitstandort2='primeSite' or $v_lastlocation='1'" >
                                <location log1="sto2">
                                    <xsl:attribute name="sortstring">
                                        <!-- ausgabe für die sortierung: normalisierung der gross/kleinschreibung und der sonderzeichen -->
                                        <xsl:for-each select="property">
                                            <!-- die obereinträge werden rekursiv hinzugefügt  -->
                                            <xsl:for-each select="ancestors/property">
                                                <xsl:sort select="@level" data-type="number" order="ascending"></xsl:sort>
                                                <xsl:value-of select="lemma"/>
                                            </xsl:for-each>
                                            <!-- dann wird das lemma ausgegeben -->
                                            <xsl:value-of select="lemma"/>
                                        </xsl:for-each>
                                    </xsl:attribute>
                                    <xsl:for-each select="property">
                                        <!-- die obereinträge werden rekursiv hinzugefügt  -->
                                        <xsl:for-each select="ancestors/property">
                                            <xsl:sort select="@level" data-type="number" order="ascending"></xsl:sort>
                                            <xsl:apply-templates select="lemma"/>
                                        </xsl:for-each>
                                        <!-- dann wird das lemma ausgegeben -->
                                        <xsl:apply-templates select="lemma"/>
                                    </xsl:for-each>
                                </location>
                            </xsl:if>
                        </xsl:for-each>
                    </locations>
                    
                    <!-- für die anschließende weitersortierung muss die objektbezeichnung ausgelesen werden-->
                    <objecttypes>                        
                        <xsl:for-each select="sections/section[@sectiontype='objecttypes']/items[1]/item[1]/property">
                            <objecttype>
                                <xsl:attribute name="sortstring">
                                    <!-- ausgabe für die sortierung: normalisierung der gross/kleinschreibung und der sonderzeichen -->
                                    <!-- die obereinträge werden rekursiv hinzugefügt  -->
                                    <xsl:for-each select="ancestors/property">
                                        <xsl:sort select="@level" data-type="number" order="ascending"></xsl:sort>
                                        <xsl:value-of select="lemma"/>
                                    </xsl:for-each>
                                    <!-- dann wird das lemma ausgegeben -->
                                    <xsl:value-of select="lemma"/>
                                </xsl:attribute>
                                
                                <!-- die obereinträge werden rekursiv hinzugefügt  -->
                                <xsl:for-each select="ancestors/property">
                                    <xsl:sort select="@level" data-type="number" order="ascending"></xsl:sort>
                                    <xsl:apply-templates select="lemma"/>
                                </xsl:for-each>
                                <!-- dann wird das lemma ausgegeben -->
                                <xsl:apply-templates select="lemma"/>
                            </objecttype>
                        </xsl:for-each>                        
                      </objecttypes>   
                    <xsl:for-each select="*"><xsl:copy-of select="."/></xsl:for-each>
                </article>
            </xsl:for-each>
        </xsl:param>
        
        <!-- Sortierung der Artikel herstellen -->
        <xsl:param name="p_katalog1">
            <xsl:choose>
                
                <!-- nur nach der signatur sortieren -->
                <xsl:when test="$sw_sortieren_nach_signatur=1">                    
                    <xsl:perform-sort select="$p_katalog0/article">
                        <xsl:sort select="@article_signature" order="ascending" lang="de" case-order="upper-first" />
                    </xsl:perform-sort>
                </xsl:when>
                
                <!-- nach datierung, standort, objekttyp und signatur sortieren -->
                <xsl:otherwise>
                    <xsl:perform-sort select="$p_katalog0/article">
                        <xsl:sort select="@dating_index" order="ascending" lang="de" case-order="upper-first"/>
                        <xsl:sort select="@dating_index_add" order="ascending" lang="de" case-order="upper-first"/>
                        <xsl:sort select="locations/location[1]/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                        <xsl:sort select="objecttypes/objecttype[1]/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                        <xsl:sort select="@article_signature"  order="ascending" lang="de" case-order="upper-first" />
                    </xsl:perform-sort>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:param>
        
        <!-- Ende: parameter für die sortierung des katalogs -->
        
<!-- parametertest 
<param_katalog0><xsl:copy-of select="$p_katalog0"></xsl:copy-of></param_katalog0><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<param_katalog1><xsl:copy-of select="$p_katalog1"></xsl:copy-of></param_katalog1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->        
        
        
    <!-- der katalog wird aus dem parameter p_katalog1 mit den darin bereits sortierten artikeln neuangelegt -->
    <articles log1="kat1">

        <!-- die artikel aus dem parameter einzeln aufrufen -->
        <xsl:for-each select="$p_katalog1/article">
            <!-- artikel neu anlegen, attribute kopieren -->
            <article>
                <xsl:copy-of select="@*"/>
                <xsl:attribute name="article_sort" select="position()" />
                
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <!-- allgemeine angaben kopieren -->
                <xsl:for-each select="*[not(self::sections or self::footnotes or self::links)]"><xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:for-each>
                
                <!-- ein element sections bilden -->
                <sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- die einzelnen sections aufrufen und zum hierarchisieren anweisen -->
                    <xsl:for-each select="sections/section[@level='1']">
                        <xsl:call-template name="sections-hierarchisieren"/>
                        <!-- die weitere transformation erfolgt von diesem template ausgehend -->
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <!-- fussnoten und links des artikels kopieren -->
                <xsl:copy-of select="footnotes"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:copy-of select="links"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            </article><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
    </articles>    
    
    </xsl:template>

</xsl:stylesheet>