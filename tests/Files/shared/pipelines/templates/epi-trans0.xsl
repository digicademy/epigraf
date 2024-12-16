<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
 
<xsl:import href="commons/epi-switch.xsl"/>
<xsl:output indent="no"/>  


<!--    funktionen dieses stylesheets:
   die  hauptelemente projekt (project), band (volume), katalog (catalog), register (indices) und steuerungslisten (manage_lists) werden angelegt
   die sections in volume werden hierarchisch verschachtelt
        die elemente <section @sectiontype="chapter"> in volume werden um die attribute @kategorie und @data_key ergänzt    

    der katalog wird nach vier kriterien (datierung, standort, träger, signatur) sortiert

    die elemente <index> werden in die beiden gruppen <indices> und <manage_lists> aufgeteilt    
 
noch nicht:
    die elemente <section @sectiontype="chapter"> (nicht feste abschnitte in den artikeln) werden um die attribute @kategorie und @data_key ergänzt

    in den registern und steuerungslisten werden items erzeugt und hierarchich verschachtelt, 
        die items werden mit den attributen @gruppierung und @ausblenden versehen
        die verweise werden in element <see> transformiert und die entsprechenden lemmata in hierarchischer Folge eingefügt

        im standorteregister werden die verweisziele (sections) um die attribute @kursiv und @verloren ergänzt
        im texttypenregister werden die verweisziele (sections) um die attribute @metres und @language ergänzt

        die elemente <lemma> werden mit den attributen @sortchart und @sortstring versehen, die geschweiften klammer werden in den text()-knoten  herausgefiltert

-->

<!-- generelle parameter des projects zur verwendung an jeder belieben stelle des codes -->
<xsl:param name="p_project-name">
<xsl:value-of select="/book/project/name"/>
</xsl:param>
<xsl:param name="p_project-signature">
<xsl:value-of select="/book/project/signature"/>
</xsl:param>
<xsl:param name="p_project-database">
<xsl:value-of select="/book/project/database"/>
</xsl:param>
<xsl:param name="p_literaturindex">
<xsl:for-each select="/book/index[@propertytype='literature']">
<xsl:copy-of select="properties"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>
</xsl:param>
<xsl:param name="p_literatur-attributs">
<xsl:for-each select="/book/index[@propertytype='literature']">
<index><xsl:copy-of select="@*"></xsl:copy-of></index>
</xsl:for-each>
</xsl:param>

<xsl:attribute-set name="attributs-sections">
    <xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
    <xsl:attribute name="parent_id"><xsl:value-of select="@parent_id"/></xsl:attribute>    
    <xsl:attribute name="articles_id"><xsl:value-of select="@articles_id"/></xsl:attribute>   
    <xsl:attribute name="sectiontype"><xsl:value-of select="@sectiontype"/></xsl:attribute>
    <xsl:attribute name="name"><xsl:value-of select="@name"/></xsl:attribute>
    <xsl:attribute name="number"><xsl:value-of select="@number"/></xsl:attribute>
    <xsl:attribute name="level"><xsl:value-of select="@level"/></xsl:attribute>
    <xsl:attribute name="kategorie"><xsl:value-of select="items/item/property[@propertytype='captions']/lemma"/></xsl:attribute>
    <xsl:attribute name="data_key"><xsl:value-of select="items/item[@itemtype='chapter']/property[@propertytype='datakeys']/lemma"/></xsl:attribute>
</xsl:attribute-set> 

<xsl:template match="/">
    <xsl:apply-templates/>
</xsl:template>

<!-- die haupteinheiten project, catalog, indices, mangage_lists werden angelegt -->
<xsl:template match="book">


<!-- START: parameter für die sortierung des katalogs -->
<!-- 
der katalog wird ausgelesen, 
die artikel werden neu angelegt, attribute und elemente kopiert und um sortierkriterien erweitert:
die artikelsignatur wird als attribut mitgegeben
der datierungsindex wird ermittelt und als attribut mitgegeben
die standorte werden ermittelt und als elemente mitgegeben
der objekttyp wird ermittelt und als element mitgegeben
 -->
    <xsl:param name="p_katalog0">
        <xsl:for-each select="article[@articletype='epi-article']">
            <!-- datierungsschlüssel für die nachfolgende sortierung ermitteln -->
              <xsl:variable name="v_datierung1">
                   <xsl:for-each select="sections/section//items[1]/item[1]/date_sort[node()]">
                       <xsl:sort/>
                       <xsl:copy-of select="."/>
                   </xsl:for-each>
               </xsl:variable>
               <!-- der erst = älteste datierungsindex wird ausgelesen-->
               <xsl:variable name="v_datierung2"><xsl:value-of select="$v_datierung1/*[1]"/></xsl:variable>
            <article>
                <xsl:copy-of select="@*"/>
                <!-- die signatur des artikels für die anschließende weitersortierung als attribut mitgeben-->
                <xsl:attribute name="article_signature"><xsl:value-of select="sections/section[@sectiontype='signatures']/items[1]/item[1]/value"/></xsl:attribute>

                <!-- datierungsschlüssel als attribut mitgeben -->
                <xsl:attribute name="datierung_index"><xsl:value-of select="$v_datierung2"/></xsl:attribute>

                <!-- den standort  für die anschließende weitersortierung und für die kopfzeile ermitteln -->
                <standorte>
                    <xsl:for-each select="sections/section[@sectiontype='locations']/items/item[@itemtype='locations']">
                        <!-- mit den folgenden variablen wird festgestellt, ob der standort als zusätzlicher (mit dem textbaustein primesite) markiert ist -->
                        <xsl:variable name="zweitstandort1"><xsl:value-of select="content/rec_lit/@data-link-target"/></xsl:variable>
                        <xsl:variable name="zweitstandort2"><xsl:value-of select="ancestor::article/links/link[@to_id=$zweitstandort1]/property/lemma"/></xsl:variable>
                        <!-- die ausgabe wird eingeschränkt auf den jeweils obersten standort und darunter stehende die als primesite markiert sind -->
                        <xsl:if test="not(preceding-sibling::item[@itemtype='locations']) or $zweitstandort2='primeSite'">
                                <standort log0="sto2">
                                    <xsl:for-each select="property">
                                        <!-- die obereinträge werden rekursiv hinzugefügt  -->
                                        <xsl:for-each select="ancestors/property">
                                        <xsl:sort select="@level" data-type="number" order="ascending"></xsl:sort>
                                        <xsl:apply-templates select="lemma"/>
                                        </xsl:for-each>
                                        <!-- dann wird das lemma ausgegeben -->
                                        <xsl:apply-templates select="lemma"/>
                                    </xsl:for-each>
                                 </standort>
                                <standort-sort log0="sto3">
                                    <xsl:for-each select="property">
                                        <!-- die obereinträge werden rekursiv hinzugefügt  -->
                                        <xsl:for-each select="ancestors/property">
                                        <xsl:sort select="@level" data-type="number" order="ascending"></xsl:sort>
<xsl:value-of select="translate(lemma,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/>
                                        </xsl:for-each>
                                        <!-- dann wird das lemma ausgegeben -->
<xsl:value-of select="translate(lemma,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/>
                                    </xsl:for-each>
                                 </standort-sort>
                        </xsl:if>
                    </xsl:for-each>
                </standorte>
                <!-- für die anschließende weitersortierung muss die objektbezeichnung ausgelesen werden-->
                <objekt>
                    <xsl:for-each select="sections/section[@sectiontype='objecttypes']/items[1]/item[1]/property">
                                        <!-- die obereinträge werden rekursiv hinzugefügt  -->
                                        <xsl:for-each select="ancestors/property">
                                        <xsl:sort select="@level" data-type="number" order="ascending"></xsl:sort>
                                        <xsl:apply-templates select="lemma"/>
                                        </xsl:for-each>
                                        <!-- dann wird das lemma ausgegeben -->
                            <xsl:apply-templates select="lemma"/>
                    </xsl:for-each>
                </objekt>
                <xsl:for-each select="*"><xsl:copy-of select="."/></xsl:for-each>
            </article>
        </xsl:for-each>
    </xsl:param>
<xsl:param name="p_katalog1">
<xsl:choose>
                <xsl:when test="$sortieren_nach_signatur=1">
<!-- nur nach der signatur sortieren -->
                    <xsl:perform-sort select="$p_katalog0/article">
                         <xsl:sort select="@article_signature"  lang="de" order="ascending"/>
                    </xsl:perform-sort>
                </xsl:when>
                <xsl:otherwise>
<!-- nach datierung, standort, objekttyp und signatur sortieren -->
                    <xsl:perform-sort select="$p_katalog0/article">
                        <xsl:sort select="@datierung_index"/>
                        <xsl:sort select="standorte/standort-sort[1]" lang="de" order="ascending"/>
                        <xsl:sort select="objekt" lang="de"/>
                        <xsl:sort select="@article_signature"  lang="de" order="ascending"/>
                    </xsl:perform-sort>
<!--
<xsl:perform-sort select="$p_katalog0/article">
                      <xsl:sort select="@article_signature"  lang="de"/>
                      <xsl:sort select="objekt" lang="de"/>
                      <xsl:sort select="standort" lang="de"/>
                     <xsl:sort select="@datierung_index"/>
</xsl:perform-sort>
-->
                </xsl:otherwise>
            </xsl:choose>
</xsl:param>



    <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <book>
        <xsl:copy-of select="@*"/>
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <xsl:copy-of select="options"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <xsl:copy-of select="project"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>


<!-- die abteilung volume wird angelegt und die abschnitte werden hierarchisiert  -->

<!-- parametertest 
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<param_katalog0><xsl:copy-of select="$p_katalog0"></xsl:copy-of></param_katalog0><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<param_katalog1><xsl:copy-of select="$p_katalog1"></xsl:copy-of></param_katalog1><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->

        <volume>
        <xsl:for-each select="article[@articletype='epi-book']">
            <xsl:copy-of select="@id"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <!-- allgemeine angaben kopieren -->
            <xsl:for-each select="*[not(self::sections)]"><xsl:copy-of select="."/></xsl:for-each>
        <!-- die section zum hierarchisieren anweisen -->
            <xsl:for-each select="sections/section[@level='1']">
                <xsl:call-template name="sections-hierarchisieren"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
        </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </volume><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

<!-- der katalog wird angelegt, die artikel darin nach den im katalog-parameter vorformatierten kriterien sortiert -->
        <catalog>
            <xsl:for-each select="$p_katalog1/article">
                <article><xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
               <!-- allgemeine angaben kopieren -->
                   <xsl:for-each select="*[not(self::sections or self::footnotes or self::links)]"><xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:for-each>
               <!-- die section zum hierarchisieren anweisen -->
                           <sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                   <xsl:for-each select="sections/section[@level='1']">
                                       <xsl:call-template name="sections-hierarchisieren"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                       <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                   </xsl:for-each>
                           </sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                           <xsl:copy-of select="footnotes"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                           <xsl:copy-of select="links"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </article><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
             </xsl:for-each>
        </catalog>
<indices log0="ind1"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='locations']"><xsl:call-template name="index-restrukturieren">

</xsl:call-template></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='personnames']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='manufacturers']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='placenames']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='heraldry']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='brands']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='epithets']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='incipits']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='formulas']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='texttypes']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='sources']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='objecttypes']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='fonttypes']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='subjects']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='materials']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='techniques']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='wordseparators']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='metres']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='icons']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='initials']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!--<xsl:for-each select="index[@propertytype='literature']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->
<xsl:for-each select="index[@propertytype='literature']"><xsl:call-template name="litRefs"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>


<xsl:for-each select="index[@propertytype='languages']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</indices><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

<manage_lists><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='alignments']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='brandtypes']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='captions']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='conditions']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='dataformats']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='headers']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='imagetypes']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='indentations']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='linebindings']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='measures']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='outputoptions']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='paragraphs']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="index[@propertytype='verticalalignments']"><xsl:call-template name="index-restrukturieren"/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

</manage_lists><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

<!-- liste der literaturtitel aus dem band ermitteln 
    <xsl:call-template name="litRefs"></xsl:call-template>
-->
    </book>
</xsl:template>


<xsl:template name="sections-hierarchisieren">
    <!-- sections der obersten ebene neu anlegen, vorhandene attribute kopieren
         weitere attribute erzeugen: @kategorie (in den artikeln) und @data_key (im band)
    -->
    <section log0="s1" xsl:use-attribute-sets="attributs-sections">
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <!-- alle benötigten untergeordneten element mit inhalt durch spezielle templates einfügen-->
            <xsl:apply-templates></xsl:apply-templates>
        <!-- untergeordnete sections recursiv ansteuern und an dasselbe template verweisen -->
        <xsl:for-each select="following-sibling::section[@parent_id=current()/@id]"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:call-template name="sections-hierarchisieren"/>
        </xsl:for-each>
    </section>
</xsl:template>

<!-- BEGINN: elemente in <section> -->
<xsl:template match="path"></xsl:template>
<xsl:template match="status"></xsl:template>
<xsl:template match="comment"><xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:template>
<xsl:template match="items">
    <items><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="item"><xsl:apply-templates select="."></xsl:apply-templates></xsl:for-each>
    </items>
</xsl:template>
<xsl:template match="item">
    <item><xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="*">
            <xsl:choose>
                <!-- nicht benötigte element werden ausgeschieden -->
                <xsl:when test="not(node()) and not(@*)"></xsl:when>
                <xsl:when test="text()='0'"></xsl:when>
                <xsl:when test="self::sectionpath"></xsl:when>
                <!-- weiterhin benötigte elemente werden kopiert -->
                <xsl:otherwise><xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>
<!-- ENDE: element in <section> -->

<!--<xsl:template name="index-restrukturieren"><xsl:copy-of select="."></xsl:copy-of></xsl:template>-->

<!-- REGISTER umstrukturieren -->
<xsl:template name="index-restrukturieren">
<!-- element <property> wird zu <item> oder <see> (verweis)-->

<!-- der unterschied zwischen item und verweis in den rohdaten liegt im attribut @related_id 
        bei items ist der attributwert leer (@related_id=''), bei verweisen besteht er aus der id des ziel-items
-->
        <xsl:param name="p_index-name"><xsl:value-of select="@propertytype"/></xsl:param>
        <index>
            <xsl:copy-of select="@*"/><xsl:attribute name="log0">ind2</xsl:attribute>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!-- die registereinträge (property) der obersten ebenen werden angesteuert -->
            <xsl:for-each select="properties/property[@level='0']">
<!-- der wert des attributs @related_id wird in einer variablen gespeichert -->
                <xsl:variable name="v_related_id"><xsl:value-of select="@related_id"/></xsl:variable>
                <xsl:variable name="v_property_id"><xsl:value-of select="@id"/></xsl:variable>
                <xsl:variable name="v_brandtype_linkID"><xsl:value-of select="sections/section/@id"/></xsl:variable>
<!--<test><xsl:value-of select="$v_brandtype_linkID"/></test>-->
                <xsl:choose>
               <!-- items werden angelegt-->
                    <xsl:when test="@related_id='' or not(@related_id)">
                        <item log0="it1">
                            <xsl:copy-of select="@id"/>
                            <xsl:copy-of select="@level"/>
                            <xsl:copy-of select="@ishidden"/>
                            <xsl:copy-of select="@iscategory"/>
                        <xsl:if test="ancestor::index[@propertytype='brands']">
                            <xsl:attribute name="brandtype">
                            <xsl:value-of select="property/lemma"/>
                            </xsl:attribute>
                            <xsl:attribute name="brandtype-id">
                            <xsl:value-of select="property/@id"/>
                            </xsl:attribute>
                            <xsl:attribute name="brandtype-name">
                            <xsl:value-of select="property/name"/>
                            </xsl:attribute>
                          </xsl:if>
                           <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <!-- das vorformatierte lemma wird eingefügt --> 
                           <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                 <!-- weitere elemente werden kopiert -->
                            <xsl:apply-templates select="name"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:if test="unit[node()]"><xsl:copy-of select="unit"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                            <xsl:if test="norm_data[node()]"><xsl:copy-of select="norm_data"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                            <xsl:if test="number[node()]"><xsl:copy-of select="number"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                            <xsl:if test="file_name[node()]"><xsl:copy-of select="file_name"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                            <!-- spezielle elemente aus dem wappenregister -->
                            <xsl:if test="content[node()]"><xsl:copy-of select="content"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                            <xsl:if test="elements[node()]"><xsl:copy-of select="elements"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                            <xsl:if test="source_from[node()]"><xsl:copy-of select="source_from"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                <!-- die register standorte und texttypen werden für später vorzunehmende formatierungen um zusätzliche angaben ergänzt  -->
                            <xsl:choose>
                                <xsl:when test="$p_index-name='locations'">
                <!-- für das register standorte wird ein spezielles template aufgerufen -->
                                    <xsl:call-template name="locations-target-sections-expand"/>
                                </xsl:when>
                                <xsl:when test="$p_index-name='texttypes'">
                <!-- für das register texttypen wird ein spezielles template aufgerufen -->
                                    <xsl:call-template name="texttypes-target-sections-expand1"/>
                                </xsl:when>
                                <xsl:otherwise><xsl:copy-of select="sections"/></xsl:otherwise>
                            </xsl:choose>
                            <!-- zeilenumbruch -->
                            <xsl:if test="sections"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                   <!-- das template zur hierarchisierung der registereinträge wird aufgerufen -->
                            <xsl:call-template name="items-hierarchisieren"><xsl:with-param name="p_index-name"><xsl:value-of select="$p_index-name"/></xsl:with-param></xsl:call-template>
                         </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:when>
               <!-- verweise -->
                    <xsl:otherwise>
               <!-- verweis-template aufrufen -->
                        <xsl:call-template name="verweis_anlegen"><xsl:with-param name="v_related_id"><xsl:value-of select="$v_related_id"/></xsl:with-param></xsl:call-template>
                    </xsl:otherwise>
                </xsl:choose>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
        </index><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

<xsl:template name="items-hierarchisieren">
    <!-- den namen des registers in einem paramter speichern -->
    <xsl:param name="p_index-name"><xsl:value-of select="ancestor::index/@segment"/></xsl:param>
    <!-- aus den geschwister-items die jeweils untergeordneten ansteuern -->
    <xsl:for-each select="../property[@parent_id=current()/@id]">
        <!-- den wert des attributs @relatet_id in einer variablen speichern -->
        <xsl:variable name="v_related_id"><xsl:value-of select="@related_id"/></xsl:variable>

        <!-- mittels der variablen zwischen items und verweisen unterscheiden -->
        <xsl:choose>
<!-- items -->
            <xsl:when test="@related_id='' or not(@related_id)">
            <!-- item anlegen -->
                 <item>
                     <!-- einige attribute kopieren, andere hinzufügen -->
                        <xsl:copy-of select="@id"/>
                        <xsl:copy-of select="@level"/>
                            <xsl:copy-of select="@ishidden"/>
                            <xsl:copy-of select="@iscategory"/>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                     <!-- template für das element <lemma> aurufen -->
                        <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                 <!-- weitere elemente weden kopiert -->
                            <xsl:apply-templates select="name"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:if test="unit[node()]"><xsl:copy-of select="unit"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                            <xsl:if test="norm_data[node()]"><xsl:copy-of select="norm_data"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                            <xsl:if test="number[node()]"><xsl:copy-of select="number"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                            <xsl:if test="file_name[node()]"><xsl:copy-of select="file_name"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                            <!-- spezielle elemente aus dem wappenregister -->
                            <xsl:if test="content[node()]"><xsl:copy-of select="content"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                            <xsl:if test="elements[node()]"><xsl:copy-of select="elements"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                            <xsl:if test="source_from[node()]"><xsl:copy-of select="source_from"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                    <!-- die register standorte und texttypen werden für später vorzunehmende formatierungen um zusätzliche angaben ergänzt  -->

                            <xsl:choose>
                                <xsl:when test="$p_index-name='locations'">
                <!-- für das register standorte wird ein spezielles template aufgerufen -->
                                    <xsl:call-template name="locations-target-sections-expand"/>
                                </xsl:when>
                                <xsl:when test="$p_index-name='texttypes'">
                <!-- für das register texttypen wird ein spezielles template aufgerufen -->
                                    <xsl:call-template name="texttypes-target-sections-expand1"/>
                                </xsl:when>
                                <xsl:otherwise><xsl:copy-of select="sections"/></xsl:otherwise>
                            </xsl:choose>
                        <!-- zeilenumbruch -->
                        <xsl:if test="sections"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                <!-- template rekursiv aufrufen -->
                       <xsl:call-template name="items-hierarchisieren">
                                <xsl:with-param name="p_index-name"><xsl:value-of select="$p_index-name"/></xsl:with-param>
                        </xsl:call-template>
                     </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:when>
<!-- see (verweise)   -->    
                <xsl:otherwise>
                    <!-- verweis-template aufrufen und parameter mitgeben-->
                    <xsl:call-template name="verweis_anlegen"><xsl:with-param name="v_related_id"><xsl:value-of select="$v_related_id"/></xsl:with-param></xsl:call-template>
                </xsl:otherwise>
        </xsl:choose>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:for-each>
</xsl:template>

<xsl:template name="verweis_anlegen">
     <!-- parameter aus dem übergeordneten template übernehmen -->
      <xsl:param name="v_related_id"></xsl:param>
    <!-- verweisziele aufsuchen -->
              <xsl:for-each select="ancestor::index//property[@id=$v_related_id]">
            <!-- verweis anlegen -->
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <crossRef>
                    <xsl:copy-of select="@id"/>
                    <xsl:copy-of select="@parent_id"/>
                    <xsl:copy-of select="@level"/>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <!-- template zum auslesen der verweise aufrufen -->
                           <xsl:call-template name="verweise_auslesen"/>
                 </crossRef><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
               </xsl:for-each>
</xsl:template>

    <xsl:template name="verweise_auslesen"> 
<!-- den wert des attributs @parent_id in einer variablen speichern -->
        <xsl:variable name="v_parent_id"><xsl:value-of select="@parent_id"/></xsl:variable>
<!-- template für das element <lemma> aufrufen -->
      <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!-- den übergeordneten eintrag des primären verweisziels ansteuern -->
        <xsl:for-each select="preceding-sibling::property[@id=$v_parent_id]">
        <!-- ein sub-element <crossRef> anlegen -->
                     <crossRef>
              <!-- einige attribute übernehmen, andere neu erzeugen -->
                        <xsl:copy-of select="@id"/>
                        <xsl:copy-of select="@parent_id"/>
                        <xsl:copy-of select="@level"/>
                            <xsl:copy-of select="@ishidden"/>
                            <xsl:copy-of select="@iscategory"/>
                        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>   
                <!-- das template erneut in sich selbst aufrufen, um den nächst übergeordneten eintrag zu bearbeiten -->
                           <xsl:call-template name="verweise_auslesen"/>
                    </crossRef><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>

<xsl:template name="locations-target-sections-expand">
<!-- muster für die ergänzung der referenzen auf artikelnummern im standorteregister -->

<!-- der container für die referenzen (element <section>) wird in der ursprünglichen form wieder angelegt -->
    <sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!-- alle referenzen (element <section>) aufsuchen -->
        <xsl:for-each select="sections/section">
        <!-- variabele mit der @id des registereintrags (element <property>) -->
            <xsl:variable name="v_property_id"><xsl:value-of select="ancestor::property/@id"/></xsl:variable>
        <!-- variable mit der @id des ziel-artikels -->
             <xsl:variable name="v_article_id"><xsl:value-of select="@articles_id"/></xsl:variable>
        <!-- variable mit der @id der ziel-section -->
            <xsl:variable name="v_section_id"><xsl:value-of select="@id"/></xsl:variable>

        <!-- variable für das schlüsselwort primeSite (wenn vorhanden) -->
            <xsl:variable name="v_location_primesite">
            <!-- vom standort im register zum zugehörigen artikel und weiter bis zur section mit den standorten -->
            <xsl:for-each select="ancestor::book/article[@id=$v_article_id]/sections/section[@id=$v_section_id]">
                <!-- in der section weiter bis zum item mit der property des betreffenden standorts  -->
                <xsl:for-each select="items/item/property[@id=$v_property_id]">
                    <!-- von hier wieder zurück auf das parent::item und dort hinab zum content-element mit dem literaturverweis, der auf des schlüsselwort primesite führt -->
                    <xsl:for-each select="parent::item/content/rec_lit">
                        <!-- das attribut data_link_target in einer variablen speichern -->
                        <xsl:variable name="v_data_link_target"><xsl:value-of select="@data-link-target"/></xsl:variable>
                         <!-- wieder hoch zum artikel, dann in die links und zum link auf den literaturverweis mit dem schlüsselwort -->
                        <xsl:for-each select="ancestor::article/links/link[@to_id=$v_data_link_target]">
                             <!-- nun property/lemma mit dem schlüsselwort primeSite (wennn vorhanden) auslesen -->
                            <xsl:value-of select="property/lemma"/>
                         </xsl:for-each>
                      </xsl:for-each>
                </xsl:for-each>
            </xsl:for-each>
            </xsl:variable>
        
        <!-- variablentest
<parametertest>
        <t1><xsl:value-of select="$v_property_id"/></t1>
        <t2><xsl:value-of select="$v_article_id"/></t2>
        <t3><xsl:value-of select="$v_section_id"/></t3>
        <t4><xsl:value-of select="$v_location_primesite"/></t4>
</parametertest>
-->
        

<!-- referenz neu anlegen und vorhandene attribute kopieren, die attribute @kursiv und @verloren erzeugen -->
        <section log0="s2">
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="kursiv">
                <xsl:choose>
                  <!-- wenn der betreffende standort in der standorteliste  des artikels an erster stelle steht: @kursiv=0 -->
                    <xsl:when test="ancestor::book/article[@id=$v_article_id]/sections/section[@id=$v_section_id]/items/item[1]/property[@id=$v_property_id]">0</xsl:when>
                 <!-- wenn zum standort im feld Ergänzung der textbaustein "zweitstandort/primesite" aus dem literaturverzeichnis eingefügt wurde @kursiv=0  -->
                    <xsl:when test="$v_location_primesite='primeSite'">0</xsl:when>
                 <!-- in allen anderen fällen: @kursiv=1 -->   
                    <xsl:otherwise>1</xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>
            <xsl:attribute name="verloren">
                <!-- die attributwerte werden aus dem abschnitt Beschaffenheit (section conditions) des ziel-artikels und dort aus den register-fragmenten (items) entnommen -->
                <xsl:for-each select="ancestor::book/article[@id=$v_article_id]/sections/section[@sectiontype='conditions']/items/item/property">
                    <xsl:choose>
                        <xsl:when test="lemma='traditio1'">0</xsl:when>
                        <xsl:when test="lemma='traditio2'">1</xsl:when>
                        <xsl:when test="lemma='traditio3'">1</xsl:when>
                        <xsl:when test="lemma='traditio4'">1</xsl:when>
                        <xsl:when test="lemma='traditio5'">0</xsl:when>
                        <xsl:when test="lemma='traditio6'">0</xsl:when>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:attribute>
        </section><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
    </sections>
</xsl:template>

<xsl:template name="texttypes-target-sections-expand1">
<!-- muster für die ergänzung der referenzen auf artikelnummern im texttypenregister -->
<!-- der container für die referenzen (element <section>) wird in der ursprünglichen form in dem folgenden  parameter wieder angelegt -->
    <xsl:param name="sections-doubles-reduct">
        <!-- alle referenzen (element <section>) aufsuchen -->
        <xsl:for-each select="sections/section">
        <!-- variabele mit der @id des registereintrags (element <property>) -->
            <xsl:variable name="v_property_id"><xsl:value-of select="ancestor::property/@id"/></xsl:variable>
        <!-- variable mit der @id des ziel-artikels -->
             <xsl:variable name="v_article_id"><xsl:value-of select="@articles_id"/></xsl:variable>
        <!-- variable mit der @id der ziel-section -->
            <xsl:variable name="v_section_id"><xsl:value-of select="@id"/></xsl:variable>
            <xsl:call-template name="texttypes-target-sections-expand2">
                        <xsl:with-param name="v_article_id"><xsl:value-of select="$v_article_id"/></xsl:with-param>
                        <xsl:with-param name="v_section_id"><xsl:value-of select="$v_section_id"/></xsl:with-param>
                        <xsl:with-param name="v_property_id"><xsl:value-of select="$v_property_id"/></xsl:with-param>
            </xsl:call-template>
        </xsl:for-each>
    </xsl:param>

<!-- parametertest 
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<sections-doubles-reduct><xsl:copy-of select="$sections-doubles-reduct"></xsl:copy-of></sections-doubles-reduct><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->

<!-- ein container für die sections wird angelegt -->
    <sections log0="secttt"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!-- der parameter wird aufgerufen, die doubleten werden darin entfernt -->
        <xsl:for-each select="$sections-doubles-reduct/section">
            <xsl:variable name="v_language"><xsl:value-of select="@language"/></xsl:variable>
            <xsl:variable name="v_metres"><xsl:value-of select="@metres"/></xsl:variable>
                <xsl:choose>
                    <!--doubletten entfernen-->
                    <xsl:when test="@articles_id = preceding-sibling::section/@articles_id and 
                                              @language = preceding-sibling::section/@language and
                                              @metres = preceding-sibling::section/@metres"></xsl:when>
                    <xsl:otherwise><xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:otherwise>
                </xsl:choose>
        </xsl:for-each>
    </sections>

</xsl:template>

<xsl:template name="texttypes-target-sections-expand2">
<!--erweiterung des texttypenregisters um angaben zu metrik und sprache-->
            <xsl:param name="v_property_id"></xsl:param>
             <xsl:param name="v_article_id"></xsl:param>
            <xsl:param name="v_section_id"></xsl:param>
<xsl:param name="texttypes-collect">
    <xsl:for-each select="ancestor::book/article[@id=$v_article_id]/sections/section[@id=$v_section_id]/items/item[@itemtype='texttypes'][property[@id=$v_property_id]]">
        <xsl:variable name="v_rec"><xsl:value-of select="content/rec_intern/@data-link-target"/></xsl:variable>
        <xsl:variable name="v_languages"><xsl:value-of select="count(ancestor::items/item[@itemtype='languages'])"/></xsl:variable>
        <xsl:variable name="v_texttypes"><xsl:value-of select="count(ancestor::items/item[@itemtype='texttypes'])"/></xsl:variable>
<!-- mögliche kombinatiouen  bzw. überkreuzungen der kategorien sprache  und texttyp je inschrift-->
<xsl:choose>
    <!-- eine texttyp oder mehrere texttypen, aber eine sprache -->
    <xsl:when test="$v_texttypes &gt; 0 and  $v_languages =1">
        <xsl:call-template name="texttypes-section">
            <xsl:with-param name="v_property_id"><xsl:value-of select="$v_property_id"/></xsl:with-param>
            <xsl:with-param name="v_rec"><xsl:value-of select="$v_rec"/></xsl:with-param>
            <xsl:with-param name="v_section_id"><xsl:value-of select="$v_section_id"/></xsl:with-param>
            <xsl:with-param name="v_article_id"><xsl:value-of select="$v_article_id"/></xsl:with-param>
            <xsl:with-param name="v_languages"><xsl:value-of select="$v_languages"/></xsl:with-param>
            <xsl:with-param name="v_texttypes"><xsl:value-of select="$v_texttypes"/></xsl:with-param>
        </xsl:call-template>       
    </xsl:when>
    <!-- eine texttyp oder mehrere texttypen, aber keine sprache -->
    <xsl:when test="$v_texttypes &gt; 0 and  $v_languages = 0">
        <xsl:call-template name="texttypes-section">
            <xsl:with-param name="v_property_id"><xsl:value-of select="$v_property_id"/></xsl:with-param>
            <xsl:with-param name="v_rec"><xsl:value-of select="$v_rec"/></xsl:with-param>
            <xsl:with-param name="v_section_id"><xsl:value-of select="$v_section_id"/></xsl:with-param>
            <xsl:with-param name="v_article_id"><xsl:value-of select="$v_article_id"/></xsl:with-param>
            <xsl:with-param name="v_languages"><xsl:value-of select="$v_languages"/></xsl:with-param>
            <xsl:with-param name="v_texttypes"><xsl:value-of select="$v_texttypes"/></xsl:with-param>
        </xsl:call-template>       
    </xsl:when>
    <!-- ein texttyp und mehrere sprachen -->
    <xsl:when test="$v_texttypes = 1 and  $v_languages &gt; 1">
        <!-- zu jeder sprache navigieren -->
        <xsl:for-each select="ancestor::items/item[@itemtype='languages']">
        <xsl:call-template name="texttypes-section">
            <xsl:with-param name="v_property_id"><xsl:value-of select="$v_property_id"/></xsl:with-param>
            <xsl:with-param name="v_rec"><xsl:value-of select="$v_rec"/></xsl:with-param>
            <xsl:with-param name="v_section_id"><xsl:value-of select="$v_section_id"/></xsl:with-param>
            <xsl:with-param name="v_article_id"><xsl:value-of select="$v_article_id"/></xsl:with-param>
            <xsl:with-param name="v_languages"><xsl:value-of select="$v_languages"/></xsl:with-param>
            <xsl:with-param name="v_texttypes"><xsl:value-of select="$v_texttypes"/></xsl:with-param>
        </xsl:call-template>  
        </xsl:for-each>
    </xsl:when>

    <!-- mehrere texttypen und mehrere sprachen -->
    <xsl:when test="$v_texttypes &gt; 1 and  $v_languages &gt;1">
        <xsl:call-template name="texttypes-section">
            <xsl:with-param name="v_property_id"><xsl:value-of select="$v_property_id"/></xsl:with-param>
            <xsl:with-param name="v_rec"><xsl:value-of select="$v_rec"/></xsl:with-param>
            <xsl:with-param name="v_section_id"><xsl:value-of select="$v_section_id"/></xsl:with-param>
            <xsl:with-param name="v_article_id"><xsl:value-of select="$v_article_id"/></xsl:with-param>
            <xsl:with-param name="v_languages"><xsl:value-of select="$v_languages"/></xsl:with-param>
            <xsl:with-param name="v_texttypes"><xsl:value-of select="$v_texttypes"/></xsl:with-param>
        </xsl:call-template>  
    </xsl:when>

</xsl:choose>
        


        
    </xsl:for-each>
</xsl:param>

<!-- parametertest 
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<texttypes-collect><xsl:copy-of select="$texttypes-collect"></xsl:copy-of></texttypes-collect><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->

<!-- den parameter aufrufen und subsections in sections überführen-->
    <xsl:for-each select="$texttypes-collect">
        <xsl:variable name="v_language"><xsl:value-of select="@language"/></xsl:variable>
        <xsl:variable name="v_metres"><xsl:value-of select="@metres"/></xsl:variable>

<xsl:for-each select="section">
        <xsl:choose>
            <xsl:when test="subsection">
                <xsl:for-each select="subsection">
                    <section log0="ss1">
                            <xsl:copy-of select="parent::section/@id"/>
                            <xsl:copy-of select="parent::section/@articles_id"/>
                            <xsl:copy-of select="parent::section/@metres"/>
                            <xsl:copy-of select="@lang_id"/>
                           <xsl:copy-of select="@lang_alias"/>
                    <xsl:attribute name="language"><xsl:value-of select="name"/><!--<xsl:if test="not(name)">kein Lemma</xsl:if>--></xsl:attribute>
                    </section><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:for-each>
            </xsl:when>
            <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
        </xsl:choose></xsl:for-each>
    </xsl:for-each>
</xsl:template>

<xsl:template name="texttypes-section">
    <xsl:param name="v_property_id"/>
    <xsl:param name="v_rec"/>
    <xsl:param name="v_section_id"/>
    <xsl:param name="v_article_id"/>
    <xsl:param name="v_languages"/>
    <xsl:param name="v_texttypes"/>

    <section id="{$v_section_id}" articles_id="{$v_article_id}" log0="textt1">
                <xsl:attribute name="metres">
                <!-- wenn (1) zu einem texttypeneintrag ein verweis <rec_intern> auf einen inschriftenteil vorliegt, muss (2) geprüft werden, 
                        ob auch der metrikeintrag einen solchen verweis enthält  -->
                                <xsl:choose>
                <!-- auf  (1) prüfen -->
                                    <xsl:when test="content/rec_intern">
                    <!-- wenn (1) vohanden -->
                                        <xsl:choose>
                        <!-- auf (2) prüfen -->
                             <!-- wenn (1) und (2) vorhanden: wert 1 angeben -->
                                           <xsl:when test="parent::items/item[@itemtype='metres'][content/rec_intern[@data-link-target=$v_rec]]">1</xsl:when>
                            <!-- wenn (1) vorhanden und  (2) nicht vorhanden: das einzig vorhandene sprachlemma direkt auswählen -->
                                            <xsl:otherwise>0</xsl:otherwise>
                                        </xsl:choose>
                                    </xsl:when>
                     <!-- wenn (1) nicht vorhanden: das einzig vorhandene sprachlemma direkt auswählen -->
                                <xsl:otherwise>
                                    <xsl:choose>
                                        <xsl:when test="parent::items/item[@itemtype='metres']/property/lemma">1</xsl:when>
                                        <xsl:otherwise>0</xsl:otherwise>
                                    </xsl:choose>
                                    </xsl:otherwise>
                                </xsl:choose>
                </xsl:attribute>
    <!-- für das einzufügende attribut @language wird geprüft, ob bei der textypenangabe ein verweis auf einen inschriftteil vorliegt-->
                <xsl:choose>
                    <xsl:when test="content/rec_intern and parent::items/item[@itemtype='languages'][.//rec_intern]">
                     <!-- wenn ein solcher verweis vorliegt, wird ein spezielles template aufgerufen -->   
                            <xsl:call-template name="texttypes-languages">
                                                    <xsl:with-param name="v_article_id"><xsl:value-of select="$v_article_id"/></xsl:with-param>
                                                    <xsl:with-param name="v_section_id"><xsl:value-of select="$v_section_id"/></xsl:with-param>
                                                    <xsl:with-param name="v_property_id"><xsl:value-of select="$v_property_id"/></xsl:with-param>
                            </xsl:call-template>
                    </xsl:when>
                    <xsl:otherwise>
                    <!-- wenn kein verweis auf einen inschriftenteil vorliegt, wird das attribut hier erzeugt und der wert ermittelt und zugewiesen -->
                        <xsl:attribute name="language">
                        <!-- prüfen ob unter sprachen mehr als eine sprache angegeben ist -->
                           <xsl:choose>
                                 <!-- wenn mehr als eine sprache angegeben ist -->
                                 <xsl:when test="$v_languages &gt; 1 and not($v_texttypes = 1)">
                                    <!-- prüfen ob zur textsorte im feld Ergänzung ein texteintrag (mit einer sprachangabe) vorliegt -->
                                    <xsl:choose>
                                        <!-- wenn ein texteintrag (mit einer sprachangabe) vorliegt diesen aufrufen -->
                                        <xsl:when test="content[text()]"><xsl:value-of select="content"/></xsl:when>
                                        <!-- anderenfalls eine fehlermeldung ausgeben -->
                                        <xsl:otherwise>Eingabefehler: Sprach(en)angabe zu dieser Inschrift bzw. zum Inschriftteil im Artikel präzisieren!</xsl:otherwise>
                                    </xsl:choose>
                                </xsl:when>
                                <xsl:when test="$v_languages &gt; 1 and $v_texttypes = 1">
                                    <!-- in diesem fall befindet sich der fokus schon im language-item -->
                                    <xsl:value-of select="property/lemma"/>
                                </xsl:when>
                                <!-- wenn nur eine sprache angebeben ist diese aufrufen, dazu muss der fikus in das language-item geführt werden -->
                                <xsl:otherwise><xsl:value-of select="ancestor::items/item[@itemtype='languages']/property/lemma"/></xsl:otherwise>
                            </xsl:choose>
                        </xsl:attribute>

                        <xsl:attribute name="lang_id">
                        <!-- prüfen ob unter sprachen mehr als eine sprache angegeben ist -->
                           <xsl:choose>
                                 <!-- wenn mehr als eine sprache angegeben ist -->
                                 <xsl:when test="$v_languages &gt; 1 and not($v_texttypes = 1)">
                                    <!-- prüfen ob zur textsorte im feld Ergänzung ein texteintrag (mit einer sprachangabe) vorliegt -->
                                    <xsl:choose>
                                        <!-- wenn ein texteintrag (mit einer sprachangabe) vorliegt diesen aufrufen -->
                                        <xsl:when test="content[text()]">
                                            <xsl:for-each select="ancestor::items/item[@itemtype='languages']/property[lemma=current()/content]"><xsl:value-of select="@id"/></xsl:for-each>
                                       </xsl:when>
                                        <!-- anderenfalls eine fehlermeldung ausgeben -->
                                        <xsl:otherwise>Eingabefehler: Sprach(en)angabe zu dieser Inschrift bzw. zum Inschriftteil im Artikel präzisieren!</xsl:otherwise>
                                    </xsl:choose>
                                </xsl:when>
                                <xsl:when test="$v_languages &gt; 1 and $v_texttypes = 1">
                                    <!-- in diesem fall befindet sich der fokus schon im language-item -->
                                    <xsl:value-of select="property/@id"/>
                                </xsl:when>
                                <!-- wenn nur eine sprache angebeben ist diese aufrufen, dazu muss der fikus in das language-item geführt werden -->
                                <xsl:otherwise><xsl:value-of select="ancestor::items/item[@itemtype='languages']/property/@id"/></xsl:otherwise>
                            </xsl:choose>
                        </xsl:attribute>
                        <xsl:attribute name="lang_alias">
                        <!-- prüfen ob unter sprachen mehr als eine sprache angegeben ist -->
                           <xsl:choose>
                                 <!-- wenn mehr als eine sprache angegeben ist -->
                                 <xsl:when test="$v_languages &gt; 1 and not($v_texttypes = 1)">
                                    <!-- prüfen ob zur textsorte im feld Ergänzung ein texteintrag (mit einer sprachangabe) vorliegt -->
                                    <xsl:choose>
                                        <!-- wenn ein texteintrag (mit einer sprachangabe) vorliegt diesen aufrufen -->
                                        <xsl:when test="content[text()]">
                                            <xsl:for-each select="ancestor::items/item[@itemtype='languages']/property[lemma=current()/content]"><xsl:value-of select="unit"/></xsl:for-each>
                                       </xsl:when>
                                        <!-- anderenfalls eine fehlermeldung ausgeben -->
                                        <xsl:otherwise>Eingabefehler: Sprach(en)angabe zu dieser Inschrift bzw. zum Inschriftteil im Artikel präzisieren!</xsl:otherwise>
                                    </xsl:choose>
                                </xsl:when>
                                <xsl:when test="$v_languages &gt; 1 and $v_texttypes = 1">
                                    <!-- in diesem fall befindet sich der fokus schon im language-item -->
                                    <xsl:value-of select="property/unit"/>
                                </xsl:when>
                                <!-- wenn nur eine sprache angebeben ist diese aufrufen, dazu muss der fikus in das language-item geführt werden -->
                                <xsl:otherwise><xsl:value-of select="ancestor::items/item[@itemtype='languages']/property/unit"/></xsl:otherwise>
                            </xsl:choose>
                        </xsl:attribute>

                     </xsl:otherwise>
                </xsl:choose>

<!-- parametertest 
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<v_property_id><xsl:value-of select="$v_property_id"/></v_property_id><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<v_rec><xsl:value-of select="$v_rec"/></v_rec><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<v_section_id><xsl:value-of select="$v_section_id"/></v_section_id><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<v_article_id><xsl:value-of select="$v_article_id"/></v_article_id><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<v_languages><xsl:value-of select="$v_languages"/></v_languages><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<v_texttypes><xsl:value-of select="$v_texttypes"/></v_texttypes><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->
            </section>


</xsl:template>

<xsl:template name="texttypes-languages">
<!--im texttypenregister bei den referenzen auf artikelnummern (sections)  interimsweise subsections für die sprachangaben anlegen-->
    <xsl:param name="v_property_id"><xsl:value-of select="ancestor::property/@id"/></xsl:param>
     <xsl:param name="v_article_id"><xsl:value-of select="@articles_id"/></xsl:param>
    <xsl:param name="v_section_id"><xsl:value-of select="@id"/></xsl:param>

       <!-- jeden verweis auf einen inschriftteil ansteuern und daraus eine subsection bilden-->
           <xsl:for-each select="content/rec_intern">
               <xsl:variable name="v_rec"><xsl:value-of select="@data-link-target"/></xsl:variable>
            <!-- für jeden verweis auf einen inschriftenteil eine subsection anlegen und die attribute aus der section übernehmen -->
               <subsection> <xsl:copy-of select="ancestor::section/@id"/><xsl:copy-of select="ancestor::section/@articles_id"/>
                   <xsl:choose>
                    <!-- wenn es bei der inschrift einen spracheintrag mit demselben verweis auf einen inschriftteil gibt, diesen anteuern und auslesen-->
                       <xsl:when test="ancestor::items/item[@itemtype='languages'][content/rec_intern[@data-link-target=$v_rec]]">
<xsl:attribute name="log0ss">subsec1</xsl:attribute>
                            <xsl:for-each select="ancestor::items/item[@itemtype='languages'][content/rec_intern[@data-link-target=$v_rec]]">
                                <xsl:attribute name="lang_alias"><xsl:value-of select="property/unit"/></xsl:attribute>
                                <xsl:attribute name="lang_id"><xsl:value-of select="property/@id"/></xsl:attribute>
                                <xsl:copy-of select="property/name"/>
                            </xsl:for-each>
                           <xsl:copy-of select="ancestor::items/item[@itemtype='languages'][content/rec_intern[@data-link-target=$v_rec]]/property/name"/>
                       </xsl:when>
                    <!--  anderenfalls die sprachangabe direkt ansteuern-->
                       <xsl:otherwise>

                            <xsl:choose>
                                <xsl:when test="ancestor::items/item[@itemtype='languages']">
<xsl:attribute name="log0ss">subsec2</xsl:attribute>
                                   <xsl:for-each select="ancestor::items/item[@itemtype='languages'][1]">
                                        <!-- wenn es mehrere sprachangaben (ohne verweis auf inschriftenteile)  gibt, ein element <lemma> erzeugen und eine fehlermeldung ausgeben -->
                                        <xsl:choose>
                                            <xsl:when test="following-sibling::item[@itemtype='languages']"><lemma log0="lem1">Eingabefehler: Sprach(en)angabe zu dieser Inschrift bzw. zum Inschriftteil im Artikel präzisieren!</lemma></xsl:when>
                                            <!--wenn nur eine sprachangabe vorhanden ist, diese auswählen  -->
                                            <xsl:otherwise>
                                                <xsl:attribute name="lang_alias"><xsl:value-of select="property/unit"/></xsl:attribute>
                                                <xsl:attribute name="lang_id"><xsl:value-of select="property/@id"/></xsl:attribute>
                                                <xsl:copy-of select="property/name"/>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </xsl:for-each>
                                </xsl:when>
                                <xsl:otherwise>
<!-- es folgt hier nur die prüfung ob alle fälle erfasst wurden: log0ss="subsec3" dürfte nicht erscheinen -->
<xsl:attribute name="log0ss">subsec3</xsl:attribute>
                                        <xsl:attribute name="lang_alias"><xsl:value-of select="property/unit"/></xsl:attribute>
                                        <xsl:attribute name="lang_id"><xsl:value-of select="property/@id"/></xsl:attribute>
                                        <xsl:copy-of select="property/name"/>                    
                                </xsl:otherwise>
                            </xsl:choose>
                           
                            
                        </xsl:otherwise>
                   </xsl:choose>
               </subsection>
           </xsl:for-each>
</xsl:template>

<xsl:template match="lemma">
<!-- der wert des attributs sortkey wird aus dem item ausgelesen und normalisiert -->
        <xsl:param name="p_sortstring1">
        <!-- schreibweise normalisieren -->
<xsl:choose>
    <xsl:when test="parent::*/@sortkey[string()]">
            <xsl:value-of select="translate(parent::*/@sortkey,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/>
    </xsl:when>
<xsl:otherwise>
 <xsl:value-of select="translate(.,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/>
</xsl:otherwise>
</xsl:choose>
        </xsl:param>

        <xsl:param name="p_sortstring2">
        <!-- die zeichenfolge "Leerstelle=Leerstelle" in den lemmata der literaturliste  wird entfernt-->
            <xsl:choose>
                <xsl:when test="contains($p_sortstring1,' = ') ">
                     <xsl:value-of select="substring-before($p_sortstring1,' = ')"/><xsl:value-of select="substring-after($p_sortstring1,' = ')"/>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="$p_sortstring1"/></xsl:otherwise>
            </xsl:choose>
        </xsl:param>

<!-- parametertest 
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<p_sortstring1><xsl:copy-of select="$p_sortstring1"></xsl:copy-of></p_sortstring1><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<p_sortstring2><xsl:copy-of select="$p_sortstring2"></xsl:copy-of></p_sortstring2><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>-->

<!-- das element <lemma> wird neu angelegt und mit den atributen @sortchart und @sortstring versehen -->
        <lemma log0="lem2"><xsl:copy-of select="parent::property/@level"></xsl:copy-of>
            <xsl:attribute name="sortchart"><xsl:value-of select="substring($p_sortstring2,1,1)"/></xsl:attribute>
            <xsl:attribute name="sortstring"><xsl:value-of select="$p_sortstring2"/></xsl:attribute>
            <xsl:value-of select="."/>
        </lemma>
</xsl:template>

<xsl:template match="name">
<!-- der wert des attributs sortkey wird aus dem item ausgelesen und normalisiert -->
        <xsl:param name="p_sortstring1">
        <!-- schreibweise normalisieren -->
            <xsl:value-of select="translate(.,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/>
        </xsl:param>



<!-- das element <lemma> wird neu angelegt und mit den atributen @sortchart und @sortstring versehen -->
        <name>
            <xsl:if test="ancestor::index[@propertytype='heraldry']">
                <xsl:attribute name="sortchart"><xsl:value-of select="substring($p_sortstring1,1,1)"/></xsl:attribute>
                <xsl:attribute name="sortstring"><xsl:value-of select="$p_sortstring1"/></xsl:attribute>
            </xsl:if> 
            <xsl:value-of select="."/>
        </name>
</xsl:template>

<!-- ENDE:  REGISTER umstrukturieren -->

<xsl:template name="parentLitRef">
            <xsl:param name="p_parent-id"><xsl:value-of select="@parent_id"/></xsl:param>
            <xsl:copy-of select="."></xsl:copy-of>
            <xsl:for-each select="../properties/property[@id=$p_parent-id]">
            <xsl:copy-of select="."></xsl:copy-of><level><xsl:value-of select="@level"/></level>/>
<!--<xsl:call-template name="parentLitRef"><xsl:with-param name="p_parent-id"><xsl:value-of select="$p_parent-id"/></xsl:with-param></xsl:call-template>-->

              <xsl:for-each select="../properties/property[@id=$p_parent-id]">
                  <xsl:variable name="p_parent_id"><xsl:value-of select="@parent_id"/></xsl:variable>
                  <xsl:copy-of select="."></xsl:copy-of><level><xsl:value-of select="@level"/></level>/>
                  <xsl:for-each select="../properties/property[@id=$p_parent-id]">
                      <xsl:variable name="p_parent_id"><xsl:value-of select="@parent_id"/></xsl:variable>
                      <xsl:copy-of select="."></xsl:copy-of><level><xsl:value-of select="@level"/></level>/>
                      <xsl:for-each select="../properties/property[@id=$p_parent-id]">
                          <xsl:variable name="p_parent_id"><xsl:value-of select="@parent_id"/></xsl:variable>
                          <xsl:copy-of select="."></xsl:copy-of><level><xsl:value-of select="@level"/></level>/>                
                      </xsl:for-each>
                   </xsl:for-each>
                 </xsl:for-each>
            </xsl:for-each>
</xsl:template>

<!-- literaturliste aus den literaturverweisen neu erstellen -->
<xsl:template name="litRefs">
<xsl:param name="p_litRefs1">
<!-- die einzelnen titel aus dem gesamten band auslesen -->
<!-- zuerst aus den freien literaturverweisen in textabschnittten und fußnoten -->
    <xsl:for-each select="..//rec_lit[not(ancestor::links)]">
        <litRef log="lr1"><xsl:attribute name="id"><xsl:value-of select="@data-link-target"/></xsl:attribute>
           <xsl:value-of select="@data-link-value"/>
        </litRef><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!-- dann aus den abschnitten der literaturnachweise in den bandartikeln -->

    <xsl:for-each select="../article/sections/section[@sectiontype='references']/items/item/property">
        <litRef log="lr2"><xsl:copy-of select="@id"/>
            <xsl:value-of select="name"/>
        </litRef><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:for-each>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:param>

<!-- aus der ersten abfrage die doubletten entfernen -->
<xsl:param name="p_litRefs2">
<!-- aus der ersten abfrage die doubletten entfernen -->
        <xsl:for-each select="$p_litRefs1/litRef[not(@id=preceding-sibling::litRef/@id)]">
        <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!-- literaturindex mitgeben -->
    <xsl:copy-of select="$p_literaturindex"></xsl:copy-of>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:param>

<!-- einträge der refernzliste im literaturindex ansteuern und auslesen -->
<xsl:param name="p_litRefs3">
<!-- alle einträge der refernzliste im literaturindex ansteuern und auslesen-->
    <xsl:for-each select="$p_litRefs2">
        <xsl:for-each select="litRef">
        <xsl:variable name="p_litRef_id"><xsl:value-of select="@id"/></xsl:variable>
<!--  für alle einträge aus der referenzliste die complementären einträge im literaturindex ansteuern -->
            <xsl:for-each select="../properties/property[@id=$p_litRef_id]">
                <!--  den eintrag ausgeben -->
                <xsl:copy-of select="."></xsl:copy-of>
                <!-- verweisziele aufsuchen und ausgeben -->
                <xsl:for-each select="../property[@parent_id=current()/@id]">
                    <xsl:for-each select="../property[@id=current()/@related_id]">
                                    <xsl:copy-of select="."></xsl:copy-of>
                    </xsl:for-each>
                </xsl:for-each>
            </xsl:for-each>
        </xsl:for-each>
<!-- literaturindex wieder einfügen -->
        <xsl:copy-of select="properties"></xsl:copy-of>
    </xsl:for-each>
</xsl:param>

<!-- obereinträge aus dem literaturindex hinzufügen -->
<xsl:param name="p_litRefs4">
    <xsl:for-each select="$p_litRefs3">
        <xsl:for-each select="property">
<!-- schleife zum aulesen der obereinträge aus dem literaturindex aufrufen -->
            <xsl:call-template name="parentLitRef"></xsl:call-template>
        </xsl:for-each>
<!-- literaturindex wieder einfügen -->
        <xsl:copy-of select="properties"></xsl:copy-of>
   </xsl:for-each>
</xsl:param>


<!-- obereinträge aus dem literaturindex erneut hinzufügen -->
<xsl:param name="p_litRefs4a">
    <xsl:for-each select="$p_litRefs4">
        <xsl:for-each select="property">
<!-- schleife zum aulesen der obereinträge aus dem literaturindex aufrufen -->
            <xsl:call-template name="parentLitRef"></xsl:call-template>
        </xsl:for-each>
<!-- literaturindex wieder einfügen -->
        <xsl:copy-of select="properties"></xsl:copy-of>
   </xsl:for-each>
</xsl:param>

<!-- doubletten aus dem vorigen parameter entfernen -->
<xsl:param name="p_litRefs5">
    <xsl:for-each select="$p_litRefs4a">
    <xsl:for-each select="property">
        <xsl:sort select="@sortkey"></xsl:sort>
        <xsl:choose>
            <xsl:when test="@id=preceding-sibling::property/@id"></xsl:when>
            <xsl:otherwise>
                <xsl:copy-of select="."></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
     </xsl:for-each>       
 <!-- literaturindex wieder einfügen -->
        <xsl:copy-of select="properties"></xsl:copy-of>          
    </xsl:for-each>
</xsl:param>


<!-- überflüssige und verwaiste titel suchen und ausgeben -->
<xsl:param name="p_litRefs6">
<!-- alle einträge der refernzliste im literaturindex ansteuern-->
    <xsl:for-each select="$p_litRefs5">
<xsl:for-each select="properties/property">
<xsl:variable name="p_litRef_id"><xsl:value-of select="@id"/></xsl:variable>
<!-- nur die auslesen die keine entsprechung in katalog oder einleitung haben -->
<xsl:choose>
    <xsl:when test="lemma='siehe'"></xsl:when>
    <xsl:when test="../../property[@id=$p_litRef_id]"></xsl:when>
    <xsl:otherwise>
        <xsl:value-of select="lemma"/>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:otherwise>
</xsl:choose>
</xsl:for-each>
</xsl:for-each>
</xsl:param>

<!-- parametertest -->
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!--<test0><xsl:copy-of select="$p_literaturindex"></xsl:copy-of></test0><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<test1><xsl:copy-of select="$p_litRefs1"></xsl:copy-of></test1><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<test2><xsl:copy-of select="$p_litRefs2"></xsl:copy-of></test2><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<test3><xsl:copy-of select="$p_litRefs3"></xsl:copy-of></test3><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<test4><xsl:copy-of select="$p_litRefs4"></xsl:copy-of></test4><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<test5><xsl:copy-of select="$p_litRefs5"></xsl:copy-of></test5><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>-->

<xsl:comment>alle literaturverweise aus katalog und einleitung</xsl:comment><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:for-each select="$p_litRefs5">
        <index><xsl:copy-of select="$p_literatur-attributs/index/@*"></xsl:copy-of>
            <xsl:for-each select="property[@level='0']">
                <item><xsl:copy-of select="@*"></xsl:copy-of>
                    <xsl:for-each select="*"><xsl:copy-of select="."></xsl:copy-of></xsl:for-each>
                    <xsl:call-template name="items-hierarchisieren"></xsl:call-template>
                </item>
            </xsl:for-each>
       </index>
    </xsl:for-each>
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:comment>überflüssige literaturtitel<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:copy-of select="$p_litRefs6"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</xsl:comment>
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

</xsl:stylesheet>









