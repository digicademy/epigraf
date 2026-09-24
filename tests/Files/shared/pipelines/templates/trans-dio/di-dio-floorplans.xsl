<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:fn="http://www.w3.org/2005/xpath-functions"
    xmlns:epi="http://epigraf.inschriften.net#xslt-functions"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:svg="http://www.w3.org/2000/svg"
    exclude-result-prefixes="xs fn svg epi"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/>
    <xsl:import href="di-dio-links.xsl"/>
   
    <!-- Grundrisse -->
    <!--    Dazu muss im Bandartikel der Abschnitt für Grundrisse eingerichtet sein (siehe Wismar-Band). -->
    <!--    In den Stylesheets der vorherigen Stufen werden daraus map-Elemente inkl. Konkordanz generiert.-->
    <!--    Die Konkordanz ordnet die Objektnummern der Flächen im Grundriss den Artikelnummern zu.-->
    <!--    Im Kategoriensystem Literatur finden sich Einträge für Grundrisse. -->
    <!--    Diese müssen eine IRI bekommen, die der SVG-Datei des Grundrisses entspricht (z.B. map-hwi-nikolai). -->
    <!--    Die SVG-Datei wird im Datenbankordner unter projects/<bandkuerzel>/<dateiname>.svg abgelegt. -->   
    <!--    Für die lokale Entwicklung kann die SVG-Datei auch im selben Ordner wie die XML-Datei des Bandartikels liegen.-->
    <!--    Die Stylesheets prozessieren diese SVG-Datei.-->
    <!--    In den Artikel wird die Objektnummer als eigene Signatur mit Verweis auf den Literaturtitel erfasst. -->
    <!--    Im Fließtext sind an die entsprechenden Stellen pos-Tags gesetzt.-->
    <!--    Damit die Verlinkung funktioniert, muss die Form und Nummer eines Objekts in der SVG-Datei zu einer Gruppe zusammengefasst sein -->
    <!--   Die ID der Gruppe muss mit dem Präfix 'a' beginnen, gefolgt von der Objektnummer (ohne führende Nullen). -->
    
    <xsl:template match="map">
        <xsl:param name="p_iri"></xsl:param>
        <xsl:param name="mapname" select="tokenize(@map_iri, '/')[last()]" />

        <!-- Search map file in production folder first, then in local folder -->
        <xsl:variable name="projectfolder_prod"
                      select="concat(ancestor-or-self::book/job/@folder,
                             '../../projects/',
                             lower-case(ancestor-or-self::book/project/signature),
                             '/')"/>

        <xsl:variable name="projectfolder_local"
                      select="replace(translate(document-uri(/), '\', '/'), '[^/]+$','')"/>

        <xsl:variable name="mapfile" select="concat($mapname, '.svg')"/>

        <xsl:variable name="p_floorplan_path"
                   select="if (doc-available(concat($projectfolder_prod,$mapfile)))
                   then concat($projectfolder_prod,$mapfile)
                   else concat($projectfolder_local,$mapfile)"/>

        <xsl:if test="fn:doc-available($p_floorplan_path)">
            <tt_content iri="{$p_iri}-floorplan-{$mapname}-header"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>header</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header><xsl:value-of select="title"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>3</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    
    
            <tt_content iri="{$p_iri}-floorplan-{$mapname}-content"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>html</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header><xsl:value-of select="title"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>3</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext>
                    <xsl:variable name="svg_concordance" select="map-concordance"/>
                    <xsl:variable name="svg_map" select="document($p_floorplan_path)"/>
                    <div id="floorplan-{$mapname}" class="floorplan">
                        <xsl:for-each select="$svg_map/*[local-name() = 'svg']">
                            <xsl:copy copy-namespaces="no">
                                <xsl:namespace name="xlink" select="'http://www.w3.org/1999/xlink'"/>
                                <xsl:copy-of select="@*[name() != ('width','height')]"/>
                                <xsl:attribute name="width">100%</xsl:attribute>
                                <xsl:attribute name="height">100%</xsl:attribute>
                                <xsl:apply-templates select="node()"  mode="svg-copy">
                                    <xsl:with-param name="concordance" select="$svg_concordance"/>
                                </xsl:apply-templates>
                            </xsl:copy>
                        </xsl:for-each>
                    </div>               
                </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>

    </xsl:template>

    <!-- Template matching SVG <g> elements -->
    <xsl:template match="svg:g" mode="svg-copy">
        <xsl:param name="concordance"/>
        <xsl:variable name="id" select="@id"/>
        <xsl:variable name="row" select="$concordance/row[concat('a', replace(map_object_number/@sortstring, '^0+', '')) = $id]"/>

        <xsl:choose>
            <xsl:when test="$row">
                <xsl:variable name="article-num" select="epi:pad-with-suffix(replace($row/article_number/text(), '.*?(\d+)', '$1'), 4)" />
                <a xlink:href="{concat('/', $p_volume_identifier, '/', $article-num)}">
                    <xsl:copy copy-namespaces="no">
                        <xsl:apply-templates select="@*|node()"  mode="svg-copy">
                            <xsl:with-param name="concordance" select="$concordance"/>
                        </xsl:apply-templates>
                    </xsl:copy>
                </a>
            </xsl:when>
            <xsl:otherwise>
                <xsl:copy copy-namespaces="no">
                    <xsl:apply-templates select="@*|node()"  mode="svg-copy">
                        <xsl:with-param name="concordance" select="$concordance"/>
                    </xsl:apply-templates>
                </xsl:copy>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- Identity template to copy other nodes/attributes -->
    <xsl:template match="@*|node()" mode="svg-copy">
        <xsl:param name="concordance" as="element(map-concordance)?" tunnel="yes"/>
        <xsl:copy copy-namespaces="no">
            <xsl:apply-templates select="@*|node()" mode="svg-copy">
                <xsl:with-param name="concordance" select="$concordance" tunnel="yes"/>
            </xsl:apply-templates>
        </xsl:copy>
    </xsl:template>
    
</xsl:stylesheet>