<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:sodipodi="http://sodipodi.sourceforge.net/DTD/sodipodi-0.dtd"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    exclude-result-prefixes="xs"
    version="2.0">
<xsl:output indent="yes"/>

<!-- funktion dieses stylesheets: von den betreffenden zeichnungsobjekten (hier: auswahl an grabplatten) 
links auf  die entsprechenen katalognummern erzeugen sowie
id's als sprungziele für die back-links von den katalogartikeln auf die zeichnungsobjekte gernerieren

diese stylesheet ist anzuwenden auf eine xml.datei mit dem wurzelement svg
enthaltend (1) das g-element mit den zeichnungsobjekten sowie 
(2) ein element "catalog" mit den elementen "article" des betrefenden inschriftenbandes, 
erzeugt mit der pipeline "Rohdaten" oder mit der ersten transformationsstufe (epi-trans0.xsl)

beispiele:
hgw.nikolai.map.daten.xml
hgw.narien.map.daten.xml

die zeichnungsobjekte (rect, path, poligon u. dgl.) müssen mit der zugehörigen laufnummer (die i, d. R. nicht  der artikelnummer entspricht) 
zu einem g-element zusammengefasst werden, dessen id als sprungziel für den backlink aus dem katalogartikel dienen soll

gehören mehrere zeichnungsobjekte mit ihren nummern zum selben artikel, 
werden sie alle vom einem weiteren g-tag umschlossen; 
dessen id übernimmt die funktion des sprungziels, sie wird aus den ids der darin enthaltenen g-elemente kumuliert  
hier muss sichergestellt werden, dass die unterelemente in ihrer genauen zugehörigkeit verschachtelt sind
-->
<!--
die unten folgenden wurzelparameter dienen der anpassung an die jeweilige imagemap

über den ersten parameter werden die betreffenden artikel angesteuert, um die jeweilige artikelnummer zu ermitteln; 
die angabe entspricht dem namen des textbausteins, mit den die grundriss-signaturen im artikel spezifiziert wurden

der zweite parameter gibt den anfang des pfads zu den dio.artikeln an

der dritte parameter gibt den anfang der signatur, die als sprungziel für den back-link diensn soll, an

der vierte paramter gibt die id des obersten g-elements an, auf das die transformation anzuwenden ist
 -->

<!-- parameter - zum anpassen an die jeweilige imagemap-->
<xsl:param name="p_map_name">Grundriss St. Marien Greifswald</xsl:param>    
<xsl:param name="p_volume_startLink">https://www.inschriften.net/di077/</xsl:param>    
<xsl:param name="p_object_startID">hgw.marien.gp</xsl:param>
<xsl:param name="p_layer_linked">Grabplatten_verlinkt</xsl:param>    

<xsl:template match="g[@id=$p_layer_linked]">
<g>
<xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
<xsl:for-each select="g">

<xsl:choose>
    <xsl:when test="g">
<xsl:call-template name="object-group">
<xsl:with-param name="p_map_name"><xsl:value-of select="$p_map_name"/></xsl:with-param>
<xsl:with-param name="p_volume_startLink"><xsl:value-of select="$p_volume_startLink"/></xsl:with-param>
<xsl:with-param name="p_object_startID"><xsl:value-of select="$p_object_startID"/></xsl:with-param>
</xsl:call-template>
</xsl:when>
    <xsl:otherwise>
<xsl:call-template name="object-single">
<xsl:with-param name="p_map_name"><xsl:value-of select="$p_map_name"/></xsl:with-param>
<xsl:with-param name="p_volume_startLink"><xsl:value-of select="$p_volume_startLink"/></xsl:with-param>
<xsl:with-param name="p_object_startID"><xsl:value-of select="$p_object_startID"/></xsl:with-param>
</xsl:call-template>
</xsl:otherwise>
</xsl:choose>
</xsl:for-each>
</g>
</xsl:template>

<!-- muster für die einfache platten -->
<xsl:template name="object-single">
    <xsl:param name="p_map_name"></xsl:param>
    <xsl:param name="p_volume_startLink"></xsl:param>
    <xsl:param name="p_object_startID"></xsl:param>

    <xsl:param name="p_object_number">
        <xsl:value-of select="text"/>
    </xsl:param>
    <xsl:param name="p_article_number">
        <xsl:for-each select="ancestor::*/catalog/article/sections/section/items/item[property/name=$p_map_name][value=$p_object_number]">
            <!-- TODO: a-Nummern berücksichtigen -->
            <xsl:number value="parent::items/item[1]/value" format="0001"></xsl:number> 
        </xsl:for-each>
    </xsl:param>
    <xsl:param name="p_href"><xsl:value-of select="$p_volume_startLink"/><xsl:value-of select="$p_article_number"/>/</xsl:param>
    <xsl:param name="p_a_id"><xsl:value-of select="$p_object_startID"/><xsl:value-of select="$p_object_number"/></xsl:param>

<a>
<xsl:attribute name="id">a<xsl:value-of select="$p_a_id"/></xsl:attribute>
<xsl:attribute name="xlink:href"><xsl:value-of select="$p_href"/></xsl:attribute>
<xsl:attribute name="target">_top</xsl:attribute>
<!--  
<parametertest>
<p_object_number><xsl:value-of select="$p_object_number"/></p_object_number>
<p_article_number><xsl:value-of select="$p_article_number"/></p_article_number>
<p_href><xsl:value-of select="$p_href"/></p_href>
<p_a_id><xsl:value-of select="$p_a_id"/></p_a_id>
</parametertest>
-->

<g><xsl:copy-of select="@transform"></xsl:copy-of>
<xsl:attribute name="id"><xsl:value-of select="$p_object_startID"/><xsl:value-of select="$p_object_number"/>a</xsl:attribute>
<xsl:for-each select="*"><xsl:copy-of select="."></xsl:copy-of></xsl:for-each>
</g>
</a>
</xsl:template>

<!-- muster für gruppen von plattenfragmenten -->
<xsl:template name="object-group">
    <xsl:param name="p_map_name"></xsl:param>
    <xsl:param name="p_volume_startLink"></xsl:param>
    <xsl:param name="p_object_startID"></xsl:param>

    <xsl:param name="p_object_number">
        <xsl:choose>
            <xsl:when test="g/g/text"><xsl:value-of select="g[1]/g/text"/></xsl:when>
            <xsl:otherwise><xsl:value-of select="g[1]/text"/></xsl:otherwise>
        </xsl:choose>                                                        
    </xsl:param>

    <xsl:param name="p_object_numbers-unsort">
        <xsl:for-each select="g">
            <xsl:sort data-type="number"></xsl:sort>
            <number><xsl:value-of select=".//text"/></number>
        </xsl:for-each>
    </xsl:param>

    <xsl:param name="p_object_numbers-sort">
        <xsl:for-each select="$p_object_numbers-unsort/number">
            <xsl:value-of select="."/><xsl:if test="following-sibling::*">_</xsl:if>
        </xsl:for-each>
    </xsl:param>
    
    <!-- TODO: a-Nummern berücksichtigen -->
    <xsl:param name="p_article_number">
        <xsl:for-each select="ancestor::*/catalog/article/sections/section/items/item[property/name=$p_map_name][contains(value,$p_object_number)]">
        <xsl:number value="parent::items/item[1]/value" format="0001"></xsl:number> 
        </xsl:for-each>
    </xsl:param>
    <xsl:param name="p_href"><xsl:value-of select="$p_volume_startLink"/><xsl:value-of select="$p_article_number"/>/</xsl:param>
    <xsl:param name="p_a_id"><xsl:value-of select="$p_object_startID"/><xsl:value-of select="$p_object_numbers-sort"/></xsl:param>
    <a>
        <xsl:attribute name="id">a<xsl:value-of select="$p_a_id"/></xsl:attribute>
        <xsl:attribute name="xlink:href"><xsl:value-of select="$p_href"/></xsl:attribute>
        <xsl:attribute name="target">_top</xsl:attribute>

<g>
<xsl:copy-of select="@transform"></xsl:copy-of>
<xsl:attribute name="id"><xsl:value-of select="$p_object_startID"/><xsl:value-of select="$p_object_numbers-sort"/></xsl:attribute>

        <xsl:for-each select="g">
            <xsl:variable name="p_object_sortnumber"><xsl:number  format="a"/></xsl:variable>
<!-- 
<parametertest>
<p_map_name><xsl:value-of select="$p_map_name"/></p_map_name>
<p_object_number><xsl:value-of select="$p_object_number"/></p_object_number>
<p_article_number><xsl:value-of select="$p_article_number"/></p_article_number>
<p_object_sortnumber><xsl:value-of select="$p_object_sortnumber"/></p_object_sortnumber>
<p_object_numbers-sort><xsl:value-of select="$p_object_numbers-sort"/></p_object_numbers-sort>
</parametertest>
-->

            <xsl:call-template name="object-group-object">
                <xsl:with-param name="p_map_name"><xsl:value-of select="$p_map_name"/></xsl:with-param>
                <xsl:with-param name="p_volume_startLink"><xsl:value-of select="$p_volume_startLink"/></xsl:with-param>
                <xsl:with-param name="p_object_startID"><xsl:value-of select="$p_object_startID"/></xsl:with-param>
                <xsl:with-param name="p_object_sortnumber"><xsl:value-of select="$p_object_sortnumber"/></xsl:with-param>
                <xsl:with-param name="p_object_numbers-sort"><xsl:value-of select="$p_object_numbers-sort"/></xsl:with-param>
            </xsl:call-template>

        </xsl:for-each>
</g>
    </a>
</xsl:template>

<!-- muster für grabplattenfragmente in gruppen -->
<xsl:template name="object-group-object">
    <xsl:param name="p_map_name"></xsl:param>
    <xsl:param name="p_volume_startLink"></xsl:param>
    <xsl:param name="p_object_startID"></xsl:param>
    <xsl:param name="p_object_sortnumber"></xsl:param>
    <xsl:param name="p_object_number"><xsl:value-of select="text"/></xsl:param>
    <xsl:param name="p_object_numbers-sort"></xsl:param>

<g>
<xsl:copy-of select="@transform"></xsl:copy-of>
<xsl:attribute name="id"><xsl:value-of select="$p_object_startID"/><xsl:value-of select="$p_object_numbers-sort"/><xsl:value-of select="$p_object_sortnumber"/></xsl:attribute>
<xsl:for-each select="*"><xsl:copy-of select="."/></xsl:for-each>
</g>

</xsl:template>

<xsl:template match="catalog"></xsl:template>

</xsl:stylesheet>






