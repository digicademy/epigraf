<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans1-commons.xsl"/>
    <xsl:output indent="no"/>  

    <!-- dieses stylesheet fügt den einträgen im texttypenregister die kategorien 'language' und 'metres' hinzu,
         es wird aufgerufen im template index-restrukturieren im stylesheet trans0-indices-commons.xsl-->
    
    <!-- vorgehensweise: 
         1. im <index> textypes zu jedem eintrag die verweisziele (artikel/inschrift) aufrufen,
         2. vom verweisziel ausgehend den <index> languages aufsuchen und den die inschrift betreffenden 
            spracheintrag aussuchen und festhalten,
         3. dasselbe bezüglich des <index> metres
         4. wenn möglich verkreuzung der kategorien languages und metres klären (siehe parameter p_texttypes-collect)
         5. aufgrund der ergänzungen eine neusortierung der einträge durchführen 
         
         ergebnis:
         1. im element <section> wird ein zusätzliches attribut @metres="0|1" angelegt
         2. desgleichen werden angelegt die 
            attribute @language, @lang_id und @lang_alias (letzteres für die abkürzung der sprachbezeichnung)
            z. b. @language="lateinisch" lang_id="properties-10434" lang_alias="lat."
         3. dementsprechend wurden die registereinträge unter berücksichtigung der neuen kategorien neu sortiert, 
            eine erneute sortierung ist in den folgenden transformationen nicht mehr erforderlich

        folgen:
        aus den ergänzungen um sprache und metrik wird in den nachfolgenden transformationsschritten (trans1, trans2) 
        ein neuer registerbaum erzeugt
    -->

    <xsl:template name="texttypes-target-sections-expand1">
        <!-- der fokus wurde vor dem aufrufen dieses templates auf 
            index[propertytype="texttypes"]/properties/property geführt, 
            liegt also auf dem einzelnen registereintrag -->
        
        <!-- muster für die ergänzung der referenzen auf artikelnummern im texttypenregister:
                die artikelnnummern werden um angaben zu sprache und metrik ergänzt -->

        <!-- der container für die referenzen (element <section>) wird in der ursprünglichen form 
                in dem folgenden parameter wieder angelegt -->
        <xsl:param name="p_sections-doubles-reduct">
            <!-- alle referenzen auf artikelnummern  (element <section>) aufsuchen -->
            <xsl:for-each select="sections/section">
              <!-- variable mit der @id des registereintrags (element <property>) -->
                <xsl:variable name="v_property_id"><xsl:value-of select="ancestor::property/@id"/></xsl:variable>
              <!-- variable mit der @id des ziel-artikels -->
                <xsl:variable name="v_article_id"><xsl:value-of select="@articles_id"/></xsl:variable>
              <!-- variable mit der @id der ziel-section -->
                <xsl:variable name="v_section_id"><xsl:value-of select="@id"/></xsl:variable>
                <!-- zur erweiterung des texttypenregisters um angaben zu metrik und sprache 
                        jede <section> an das entsprechende template verweisen -->
                <xsl:call-template name="texttypes-target-sections-expand2">
                    <xsl:with-param name="p_article_id"><xsl:value-of select="$v_article_id"/></xsl:with-param>
                    <xsl:with-param name="p_section_id"><xsl:value-of select="$v_section_id"/></xsl:with-param>
                    <xsl:with-param name="p_property_id"><xsl:value-of select="$v_property_id"/></xsl:with-param>
                </xsl:call-template>
            </xsl:for-each>
        </xsl:param>
        

        
        <!-- für die sortierung wird aus dem ersten parameter ein neuer 
             parameter, ergänzt um einen sortierschlüssel, erstellt -->
        <xsl:param name="p_sections-doubles-reduct1">
            <xsl:for-each select="$p_sections-doubles-reduct/section">
                <xsl:copy>
                    <xsl:attribute name="lang_sortkey">
                        <xsl:choose>
                            <xsl:when test="@lang_alias='hebr.'">1</xsl:when>
                            <xsl:when test="@lang_alias='gr.'">2</xsl:when>
                            <xsl:when test="@lang_alias='lat.'">3</xsl:when>
                            <xsl:when test="@lang_alias='dt.'">4</xsl:when>
                            <xsl:otherwise>5</xsl:otherwise>
                        </xsl:choose>
                    </xsl:attribute>
                    <xsl:copy-of select="@*"/>
                </xsl:copy>
            </xsl:for-each>
        </xsl:param>
        
        <!-- ein weiterer sortier-parameter wird erstellt -->
        <xsl:param name="p_sections-doubles-reduct2">
            <xsl:for-each select="$p_sections-doubles-reduct/section">
                <xsl:sort select="@lang_sort" order="ascending" lang="de" case-order="upper-first" />
                <xsl:sort select="@metres" data-type="number" order="ascending"/>
                <xsl:sort select="@articles_id" data-type="number" order="ascending"/>
                <xsl:copy>
                    <xsl:copy-of select="@*"/>
                </xsl:copy>
            </xsl:for-each>
        </xsl:param>
        
        <!-- parametertest 
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sections-doubles-reduct><xsl:copy-of select="$p_sections-doubles-reduct"></xsl:copy-of></sections-doubles-reduct><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        -->
        
        <!-- ein container für die sections wird angelegt -->
        <sections log1="secttt"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- der zweite parameter mit den vorformatieren elementen <section> wird aufgerufen, 
                die doubletten darin werden entfernt -->
            <xsl:for-each select="$p_sections-doubles-reduct2/section">
                <xsl:variable name="v_language"><xsl:value-of select="@language"/></xsl:variable>
                <xsl:variable name="v_metres"><xsl:value-of select="@metres"/></xsl:variable>
                <xsl:choose>
                    <!--doubletten entfernen-->
                    <xsl:when test="@articles_id = preceding-sibling::section[1]/@articles_id and 
                        @lang_alias = preceding-sibling::section[1]/@lang_alias and
                        @metres = preceding-sibling::section[1]/@metres"></xsl:when>
                    <xsl:otherwise><xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </sections>
    </xsl:template>
    
    <xsl:template name="texttypes-target-sections-expand2">
        <!--erweiterung des texttypenregisters um angaben zu metrik und sprache-->
        <!-- drei parameter aus der anweisung zum aufrufen dieses templates übernehmen  -->
        <xsl:param name="p_property_id"></xsl:param>
        <xsl:param name="p_article_id"></xsl:param>
        <xsl:param name="p_section_id"></xsl:param>
        
        <!-- parameter für das ermitteln der kombinationen bzw. überkreuzungen von 
                texttyp und sprache in derselben inschrift -->
        <xsl:param name="p_texttypes-collect">
            <!-- vom registereintrag des texttypes zum item des textypes im jeweiligen artikel navigieren -->
            <xsl:for-each select="ancestor::book/article[@id=$p_article_id]/sections/section[@id=$p_section_id]/items/item[@itemtype='texttypes'][property[@id=$p_property_id]]">
                <!-- interner verweis auf inschrift-teil im feld ergänzung: 
                        wenn vorhanden, verweis-id in variable speichern   -->
                <xsl:variable name="v_rec"><xsl:value-of select="content/rec_intern/@data-link-target"/></xsl:variable>
                <!-- wenn die norm_iri der betreffenden sprache in das ergänzungsfeld eingetragen wurde, diese auslesen -->
                <xsl:variable name="v_iri"><xsl:value-of select="content/text()"/></xsl:variable>
                <!-- anzahl der spracheinträge ermitteln -->
                <xsl:variable name="v_languages"><xsl:value-of select="count(ancestor::items/item[@itemtype='languages'])"/></xsl:variable>
                <!-- anzahl der texttypeneinträge ermitteln -->
                <xsl:variable name="v_texttypes"><xsl:value-of select="count(ancestor::items/item[@itemtype='texttypes'])"/></xsl:variable>
                <!-- zuordnung = @itemgroup zu sprache und metrum speichern -->
                <xsl:variable name="v_itemgroup"><xsl:value-of select="@itemgroup"/></xsl:variable>

                <!-- mögliche kombinationen  bzw. überkreuzungen der kategorien sprache und texttyp je inschrift-->
                <xsl:choose>
                    <!-- wenn sprache und metrik über @itemgroup explizit zugewiesen wurden  -->
                    <xsl:when test="@itemgroup[string()]">

                        <section id="{$p_section_id}" articles_id="{$p_article_id}" log1="textt-group">
                            <xsl:attribute name="metres">
                                <xsl:choose>
                                    <xsl:when test="parent::items/item[@itemtype='metres'][@itemgroup=$v_itemgroup]">1</xsl:when>
                                    <xsl:otherwise>0</xsl:otherwise>
                                </xsl:choose>
                            </xsl:attribute>
                            <xsl:attribute name="language">
                                <xsl:for-each select="parent::items/item[@itemtype='languages'][@itemgroup=$v_itemgroup]">
                                    <xsl:value-of select="property/lemma"/>
                                </xsl:for-each>
                            </xsl:attribute>
                            <xsl:attribute name="lang_id">
                                <xsl:for-each select="parent::items/item[@itemtype='languages'][@itemgroup=$v_itemgroup]">
                                    <xsl:value-of select="property/@id"/>
                                </xsl:for-each>
                            </xsl:attribute>
                            <xsl:attribute name="lang_alias">
                                <xsl:for-each select="parent::items/item[@itemtype='languages'][@itemgroup=$v_itemgroup]">
                                    <xsl:value-of select="property/unit"/>
                                </xsl:for-each>
                            </xsl:attribute>
                            <xsl:attribute name="lang_sort">
                                <xsl:for-each select="parent::items/item[@itemtype='languages'][@itemgroup=$v_itemgroup]">
                                    <xsl:value-of select="property/@sortkey"/>
                                </xsl:for-each>
                            </xsl:attribute>
                       </section>
                    </xsl:when>
                    
                    <!-- wenn sprache und metrik in den unterlemmata stecken -->
                    <xsl:when test="property/lemma =  ('lat.', 'dt.', 'hebr.', 'griech.', 'franz.', 'Vers')">
                        <section id="{$p_section_id}" articles_id="{$p_article_id}" log1="textt-expl">
                            <xsl:attribute name="metres">0</xsl:attribute>
                            <xsl:attribute name="language"></xsl:attribute>
                            <xsl:attribute name="lang_id"></xsl:attribute>
                            <xsl:attribute name="lang_alias"></xsl:attribute>
                            <xsl:attribute name="lang_sort"></xsl:attribute>                            
                        </section>                        
                    </xsl:when>
                    
                    <!-- wenn sprache und metrik nicht über @itemgroup explizit zugewiesen wurden
                            gelten folgende unterscheidungen:                    
                    -->
                    <xsl:otherwise>
                        <xsl:choose>
                            <!-- eine texttyp oder mehrere texttypen, aber nur eine sprache -->
                            <xsl:when test="$v_texttypes &gt; 0 and  $v_languages =1">
                                <!-- template aufrufen, parameter mitgeben -->
                                <xsl:call-template name="texttypes-section">
                                    <xsl:with-param name="p_property_id"><xsl:value-of select="$p_property_id"/></xsl:with-param>
                                    <xsl:with-param name="p_rec"><xsl:value-of select="$v_rec"/></xsl:with-param>
                                    <xsl:with-param name="p_section_id"><xsl:value-of select="$p_section_id"/></xsl:with-param>
                                    <xsl:with-param name="p_article_id"><xsl:value-of select="$p_article_id"/></xsl:with-param>
                                    <xsl:with-param name="p_languages"><xsl:value-of select="$v_languages"/></xsl:with-param>
                                    <xsl:with-param name="p_texttypes"><xsl:value-of select="$v_texttypes"/></xsl:with-param>
                                </xsl:call-template>       
                            </xsl:when>
                            <!-- ein texttyp oder mehrere texttypen, aber keine sprache -->
                            <xsl:when test="$v_texttypes &gt; 0 and  $v_languages = 0">
                                <!-- template aufrufen, parameter mitgeben -->
                                <xsl:call-template name="texttypes-section">
                                    <xsl:with-param name="p_property_id"><xsl:value-of select="$p_property_id"/></xsl:with-param>
                                    <xsl:with-param name="p_rec"><xsl:value-of select="$v_rec"/></xsl:with-param>
                                    <xsl:with-param name="p_iri"><xsl:value-of select="$v_iri"/></xsl:with-param>
                                    <xsl:with-param name="p_section_id"><xsl:value-of select="$p_section_id"/></xsl:with-param>
                                    <xsl:with-param name="p_article_id"><xsl:value-of select="$p_article_id"/></xsl:with-param>
                                    <xsl:with-param name="p_languages"><xsl:value-of select="$v_languages"/></xsl:with-param>
                                    <xsl:with-param name="p_texttypes"><xsl:value-of select="$v_texttypes"/></xsl:with-param>
                                </xsl:call-template>       
                            </xsl:when>
                            <!-- ein texttyp und mehrere sprachen -->
                            <xsl:when test="$v_texttypes = 1 and  $v_languages &gt; 1">
                                <!-- erst zu zu jeder sprache navigieren -->
                                <xsl:for-each select="ancestor::items/item[@itemtype='languages']">
                                    <!-- dann template aufrufen, parameter mitgeben -->
                                    <xsl:call-template name="texttypes-section">
                                        <xsl:with-param name="p_property_id"><xsl:value-of select="$p_property_id"/></xsl:with-param>
                                        <xsl:with-param name="p_rec"><xsl:value-of select="$v_rec"/></xsl:with-param>
                                        <xsl:with-param name="p_iri"><xsl:value-of select="$v_iri"/></xsl:with-param>
                                        <xsl:with-param name="p_section_id"><xsl:value-of select="$p_section_id"/></xsl:with-param>
                                        <xsl:with-param name="p_article_id"><xsl:value-of select="$p_article_id"/></xsl:with-param>
                                        <xsl:with-param name="p_languages"><xsl:value-of select="$v_languages"/></xsl:with-param>
                                        <xsl:with-param name="p_texttypes"><xsl:value-of select="$v_texttypes"/></xsl:with-param>
                                    </xsl:call-template>  
                                </xsl:for-each>
                            </xsl:when>
                            <!-- mehrere texttypen und mehrere sprachen -->
                            <xsl:when test="$v_texttypes &gt; 1 and  $v_languages &gt;1">
                                <!-- template aufrufen, parameter mitgeben -->
                                <xsl:call-template name="texttypes-section">
                                    <xsl:with-param name="p_property_id"><xsl:value-of select="$p_property_id"/></xsl:with-param>
                                    <xsl:with-param name="p_rec"><xsl:value-of select="$v_rec"/></xsl:with-param>
                                    <xsl:with-param name="p_iri"><xsl:value-of select="$v_iri"/></xsl:with-param>
                                    <xsl:with-param name="p_section_id"><xsl:value-of select="$p_section_id"/></xsl:with-param>
                                    <xsl:with-param name="p_article_id"><xsl:value-of select="$p_article_id"/></xsl:with-param>
                                    <xsl:with-param name="p_languages"><xsl:value-of select="$v_languages"/></xsl:with-param>
                                    <xsl:with-param name="p_texttypes"><xsl:value-of select="$v_texttypes"/></xsl:with-param>
                                </xsl:call-template>  
                            </xsl:when>
                        </xsl:choose>
                    </xsl:otherwise>
                 </xsl:choose>
            </xsl:for-each>
        </xsl:param>
        
        <!-- parametertest
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <texttypes-collect><xsl:copy-of select="$p_texttypes-collect"></xsl:copy-of></texttypes-collect><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text> -->

        
        <!-- den zuvor erzeugten parameter aufrufen und subsections in sections überführen-->
        <xsl:for-each select="$p_texttypes-collect">
            <xsl:variable name="v_language"><xsl:value-of select="@language"/></xsl:variable>
            <xsl:variable name="v_metres"><xsl:value-of select="@metres"/></xsl:variable>

            <xsl:for-each select="section">
                <xsl:choose>
                    <xsl:when test="subsection">
                        <xsl:for-each select="subsection">
                            <section log1="ss1">
                                <xsl:copy-of select="parent::section/@id"/>
                                <xsl:copy-of select="parent::section/@articles_id"/>
                                <xsl:copy-of select="parent::section/@metres"/>
                                <xsl:copy-of select="@lang_id"/>
                                <xsl:copy-of select="@lang_alias"/>
                                <xsl:copy-of select="@lang_sort"/>
                                <xsl:attribute name="language"><xsl:value-of select="lemma[last()]"/><!--<xsl:if test="not(name)">kein Lemma</xsl:if>--></xsl:attribute>
                            </section><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
                </xsl:choose></xsl:for-each>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template name="texttypes-section">
        <xsl:param name="p_property_id"/>
        <xsl:param name="p_rec"/>
        <xsl:param name="p_iri"/>
        <xsl:param name="p_section_id"/>
        <xsl:param name="p_article_id"/>
        <xsl:param name="p_languages"/>
        <xsl:param name="p_texttypes"/>
        
        <section id="{$p_section_id}" articles_id="{$p_article_id}" log1="textt1">
            <xsl:attribute name="metres">
                <!-- wenn (1) zu einem texttypeneintrag ein verweis <rec_intern> 
                        auf einen inschriftenteil vorliegt, muss (2) geprüft werden, 
                        ob auch der metrikeintrag einen solchen verweis enthält  -->
                <xsl:choose>
                    <!-- auf  (1) prüfen -->
                    <xsl:when test="$p_rec != ''">
                        <!-- wenn (1) vorhanden -->
                        <xsl:choose>
                            <!-- auf (2) prüfen -->
                            <!-- wenn (1) und (2) vorhanden: wert 1 angeben -->
                            <xsl:when test="parent::items/item[@itemtype='metres'][content/rec_intern[@data-link-target=$p_rec]]">1</xsl:when>
                            <!-- wenn (1) vorhanden und  (2) nicht vorhanden: 
                                    das einzig vorhandene sprachlemma direkt auswählen -->
                            <xsl:otherwise>0</xsl:otherwise>
                        </xsl:choose>
                    </xsl:when>
                    <!-- wenn (1) nicht vorhanden: das einzig vorhandene metriklemma direkt auswählen -->
                    <xsl:otherwise>
                        <xsl:choose>
                            <xsl:when test="parent::items/item[@itemtype='metres']/property/lemma">1</xsl:when>
                            <xsl:otherwise>0</xsl:otherwise>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>
            <!-- für das einzufügende attribut @language wird geprüft, 
                    ob bei der textypenangabe ein verweis auf einen inschriftteil vorliegt-->
            <xsl:choose>
                <xsl:when test="content/rec_intern and parent::items/item[@itemtype='languages'][.//rec_intern]">
                    <!-- wenn ein solcher verweis vorliegt, wird ein spezielles template aufgerufen -->   
                    <xsl:call-template name="texttypes-languages">
                        <xsl:with-param name="p_article_id"><xsl:value-of select="$p_article_id"/></xsl:with-param>
                        <xsl:with-param name="p_section_id"><xsl:value-of select="$p_section_id"/></xsl:with-param>
                        <xsl:with-param name="p_property_id"><xsl:value-of select="$p_property_id"/></xsl:with-param>
                    </xsl:call-template>
                </xsl:when>
                <!-- wenn die verknüpfung zwischen texttyp und sprache über die angabe 
                    der norm_iri (legacy: oder des unterlemmas) der sprache im feld anmerkung hergestellt wurde -->
                <xsl:when test="$p_iri and (content=$p_iri)">
                    <xsl:for-each select="ancestor::article//section[@id=$p_section_id]//item[@itemtype='languages']/property[@propertytype='languages'][norm_iri=$p_iri or lemma=$p_iri]">
                        <xsl:attribute name="language"><xsl:value-of select="lemma"/></xsl:attribute>
                        <xsl:attribute name="lang_id"><xsl:value-of select="@id"/></xsl:attribute>
                        <xsl:attribute name="lang_alias"><xsl:value-of select="unit"/></xsl:attribute>
                        <xsl:attribute name="lang_sort"><xsl:value-of select="@sortkey"/></xsl:attribute>
                        
                    </xsl:for-each>
                    
                </xsl:when>
                <xsl:otherwise>
                    <!-- wenn kein verweis auf einen inschriftenteil vorliegt, 
                            wird das attribut hier erzeugt und der wert ermittelt und zugewiesen -->
                    <xsl:attribute name="language">
                        <!-- prüfen ob unter sprachen mehr als eine sprache angegeben ist -->
                        <xsl:choose>
                            <!-- wenn mehr als eine sprache angegeben ist -->
                            <xsl:when test="$p_languages &gt; 1 and not($p_texttypes = 1)">
                                <!-- prüfen ob zur textsorte im feld Ergänzung ein 
                                        texteintrag (mit einer sprachangabe) vorliegt -->
                                <xsl:choose>
                                    <!-- wenn ein texteintrag (mit einer sprachangabe) vorliegt diesen aufrufen -->
                                    <xsl:when test="content[text()]"><xsl:value-of select="content"/></xsl:when>
                                    <!-- anderenfalls eine fehlermeldung ausgeben -->
                                    <xsl:otherwise>Eingabefehler: Sprach(en)angabe zu dieser Inschrift bzw. zum Inschriftteil im Artikel präzisieren!</xsl:otherwise>
                                </xsl:choose>
                            </xsl:when>
                            <xsl:when test="$p_languages &gt; 1 and $p_texttypes = 1">
                                <!-- in diesem fall befindet sich der fokus schon im language-item -->
                                <xsl:value-of select="property/lemma"/>
                            </xsl:when>
                            <!-- wenn nur eine sprache angebeben ist diese aufrufen, 
                                    dazu muss der fokus in das language-item geführt werden -->
                            <xsl:otherwise><xsl:value-of select="ancestor::items/item[@itemtype='languages'][not(content/rec_intern)]/property/lemma"/></xsl:otherwise>
                        </xsl:choose>
                    </xsl:attribute>
                    
                    <xsl:attribute name="lang_id">
                        <!-- prüfen ob unter sprachen mehr als eine sprache angegeben ist -->
                        <xsl:choose>
                            <!-- wenn mehr als eine sprache angegeben ist -->
                            <xsl:when test="$p_languages &gt; 1 and not($p_texttypes = 1)">
                                <!-- prüfen ob zur textsorte im feld Ergänzung ein 
                                        texteintrag (mit einer sprachangabe) vorliegt -->
                                <xsl:choose>
                                    <!-- wenn ein texteintrag (mit einer sprachangabe) vorliegt diesen aufrufen -->
                                    <xsl:when test="content[text()]">
                                        <xsl:for-each select="ancestor::items/item[@itemtype='languages']/property[lemma=current()]"><xsl:value-of select="@id"/></xsl:for-each>
                                    </xsl:when>
                                    <!-- anderenfalls eine fehlermeldung ausgeben -->
                                    <xsl:otherwise>Eingabefehler: Sprach(en)angabe zu dieser Inschrift bzw. zum Inschriftteil im Artikel präzisieren!</xsl:otherwise>
                                </xsl:choose>
                            </xsl:when>
                            <xsl:when test="$p_languages &gt; 1 and $p_texttypes = 1">
                                <!-- in diesem fall befindet sich der fokus schon im language-item -->
                                <xsl:value-of select="property/@id"/>
                            </xsl:when>
                            <!-- wenn nur eine sprache angebeben ist diese aufrufen, 
                                    dazu muss der fokus in das language-item geführt werden -->
                            <xsl:otherwise><xsl:value-of select="ancestor::items/item[@itemtype='languages'][not(content/rec_intern)]/property/@id"/></xsl:otherwise>
                        </xsl:choose>
                    </xsl:attribute>
                    <xsl:attribute name="lang_alias">
                        <!-- prüfen ob unter sprachen mehr als eine sprache angegeben ist -->
                        <xsl:choose>
                            <!-- wenn mehr als eine sprache angegeben ist -->
                            <xsl:when test="$p_languages &gt; 1 and not($p_texttypes = 1)">
                                <!-- prüfen ob zur textsorte im feld Ergänzung ein 
                                        texteintrag (mit einer sprachangabe) vorliegt -->
                                <xsl:choose>
                                    <!-- wenn ein texteintrag (mit einer sprachangabe) vorliegt diesen aufrufen -->
                                    <xsl:when test="content[text()]">
                                        <xsl:for-each select="ancestor::items/item[@itemtype='languages']/property[lemma=current()]"><xsl:value-of select="unit"/></xsl:for-each>
                                    </xsl:when>
                                    <!-- anderenfalls eine fehlermeldung ausgeben -->
                                    <xsl:otherwise>Eingabefehler: Sprach(en)angabe zu dieser Inschrift bzw. zum Inschriftteil im Artikel präzisieren!</xsl:otherwise>
                                </xsl:choose>
                            </xsl:when>
                            <xsl:when test="$p_languages &gt; 1 and $p_texttypes = 1">
                                <!-- in diesem fall befindet sich der fokus schon im language-item -->
                                <xsl:value-of select="property/unit"/>
                            </xsl:when>
                            <!-- wenn nur eine sprache angebeben ist diese aufrufen, dazu muss der fikus in das language-item geführt werden -->
                            <xsl:otherwise><xsl:value-of select="ancestor::items/item[@itemtype='languages'][not(content/rec_intern)]/property/unit"/></xsl:otherwise>
                        </xsl:choose>
                    </xsl:attribute>
                    <xsl:attribute name="lang_sort">
                        <!-- prüfen ob unter sprachen mehr als eine sprache angegeben ist -->
                        <xsl:choose>
                            <!-- wenn mehr als eine sprache angegeben ist -->
                            <xsl:when test="$p_languages &gt; 1 and not($p_texttypes = 1)">
                                <!-- prüfen ob zur textsorte im feld Ergänzung ein 
                                        texteintrag (mit einer sprachangabe) vorliegt -->
                                <xsl:choose>
                                    <!-- wenn ein texteintrag (mit einer sprachangabe) vorliegt diesen aufrufen -->
                                    <xsl:when test="content[text()]">
                                        <xsl:for-each select="ancestor::items/item[@itemtype='languages']/property[lemma=current()]"><xsl:value-of select="@sortkey"/></xsl:for-each>
                                    </xsl:when>
                                    <!-- anderenfalls eine fehlermeldung ausgeben -->
                                    <xsl:otherwise>Eingabefehler: Sprach(en)angabe zu dieser Inschrift bzw. zum Inschriftteil im Artikel präzisieren!</xsl:otherwise>
                                </xsl:choose>
                            </xsl:when>
                            <xsl:when test="$p_languages &gt; 1 and $p_texttypes = 1">
                                <!-- in diesem fall befindet sich der fokus schon im language-item -->
                                <xsl:value-of select="property/@sortkey"/>
                            </xsl:when>
                            <!-- wenn nur eine sprache angebeben ist diese aufrufen, dazu muss der fikus in das language-item geführt werden -->
                            <xsl:otherwise><xsl:value-of select="ancestor::items/item[@itemtype='languages'][not(content/rec_intern)]/property/@sortkey"/></xsl:otherwise>
                        </xsl:choose>
                    </xsl:attribute>
                    
                </xsl:otherwise>
            </xsl:choose>
            
            <!-- parametertest 
<xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<v_property_id><xsl:value-of select="$v_property_id"/></v_property_id><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<v_rec><xsl:value-of select="$v_rec"/></v_rec><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<v_section_id><xsl:value-of select="$v_section_id"/></v_section_id><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<v_article_id><xsl:value-of select="$v_article_id"/></v_article_id><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<v_languages><xsl:value-of select="$v_languages"/></v_languages><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<v_texttypes><xsl:value-of select="$v_texttypes"/></v_texttypes><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->
        </section>
        
        
    </xsl:template>
    
    <xsl:template name="texttypes-languages">
        <!--im texttypenregister bei den referenzen auf artikelnummern (sections)  
                interimsweise subsections für die sprachangaben anlegen-->
        <xsl:param name="p_property_id"><xsl:value-of select="ancestor::property/@id"/></xsl:param>
        <xsl:param name="p_article_id"><xsl:value-of select="@articles_id"/></xsl:param>
        <xsl:param name="p_section_id"><xsl:value-of select="@id"/></xsl:param>
        
        <!-- jeden verweis auf einen inschriftteil ansteuern und daraus eine subsection bilden-->
        <xsl:for-each select="content/rec_intern">
            <xsl:variable name="v_rec"><xsl:value-of select="@data-link-target"/></xsl:variable>
            <!-- für jeden verweis auf einen inschriftenteil eine subsection anlegen 
                    und die attribute aus der section übernehmen -->
            <subsection> <xsl:copy-of select="ancestor::section/@id"/><xsl:copy-of select="ancestor::section/@articles_id"/>
                <xsl:choose>
                    <!-- wenn es bei der inschrift einen spracheintrag mit demselben 
                            verweis auf einen inschriftteil gibt, diesen anteuern und auslesen-->
                    <xsl:when test="ancestor::items/item[@itemtype='languages'][content/rec_intern[@data-link-target=$v_rec]]">
                        <xsl:attribute name="log1ss">subsec1</xsl:attribute>
                        <xsl:for-each select="ancestor::items/item[@itemtype='languages'][content/rec_intern[@data-link-target=$v_rec]]">
                            <xsl:attribute name="lang_alias"><xsl:value-of select="property/unit"/></xsl:attribute>
                            <xsl:attribute name="lang_id"><xsl:value-of select="property/@id"/></xsl:attribute>
                            <xsl:attribute name="lang_sort"><xsl:value-of select="property/@sortkey"/></xsl:attribute>
                            <xsl:attribute name="sort">lang_4</xsl:attribute>
                            <xsl:copy-of select="property/lemma"/>
                        </xsl:for-each>
                        <xsl:copy-of select="ancestor::items/item[@itemtype='languages'][content/rec_intern[@data-link-target=$v_rec]]/property/lemma"/>
                    </xsl:when>
                    <!--  anderenfalls die sprachangabe direkt ansteuern-->
                    <xsl:otherwise>
                        
                        <xsl:choose>
                            <xsl:when test="ancestor::items/item[@itemtype='languages']">
                                <xsl:attribute name="log1ss">subsec2</xsl:attribute>
                                <xsl:for-each select="ancestor::items/item[@itemtype='languages'][1]">
                                    <!-- wenn es mehrere sprachangaben (ohne verweis auf inschriftenteile) gibt, 
                                            ein element <lemma> erzeugen und eine fehlermeldung ausgeben -->
                                    <xsl:choose>
                                        <xsl:when test="following-sibling::item[@itemtype='languages']"><lemma log1="lem1">Eingabefehler: Sprach(en)angabe zu dieser Inschrift bzw. zum Inschriftteil im Artikel präzisieren!</lemma></xsl:when>
                                        <!--wenn nur eine sprachangabe vorhanden ist, diese auswählen  -->
                                        <xsl:otherwise>
                                            <xsl:attribute name="lang_alias"><xsl:value-of select="property/unit"/></xsl:attribute>
                                            <xsl:attribute name="lang_id"><xsl:value-of select="property/@id"/></xsl:attribute>
                                            <xsl:attribute name="lang_sort"><xsl:value-of select="property/@sortkey"/></xsl:attribute>
                                            <xsl:attribute name="sort">lang_5</xsl:attribute>
                                            <xsl:copy-of select="property/lemma"/>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </xsl:for-each>
                            </xsl:when>
                            <xsl:otherwise>
                                <!-- es folgt hier nur die prüfung ob alle fälle erfasst wurden: 
                                    log1ss="subsec3" dürfte nicht erscheinen -->
                                <xsl:attribute name="log1ss">subsec3</xsl:attribute>
                                <xsl:attribute name="lang_alias"><xsl:value-of select="property/unit"/></xsl:attribute>
                                <xsl:attribute name="lang_id"><xsl:value-of select="property/@id"/></xsl:attribute>
                                <xsl:attribute name="lang_sort"><xsl:value-of select="property/@sortkey"/></xsl:attribute>
                                <xsl:attribute name="sort">lang_6</xsl:attribute>
                                <xsl:copy-of select="property/lemma"/>                    
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
            </subsection>
        </xsl:for-each>
    </xsl:template>

</xsl:stylesheet>