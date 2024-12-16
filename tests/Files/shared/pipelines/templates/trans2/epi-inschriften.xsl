<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
<xsl:import href="../commons/epi-switch.xsl"/>

  <xsl:template name="path_debug">
    <xsl:for-each select="parent::*">
      <xsl:call-template name="path_debug"></xsl:call-template>
    </xsl:for-each>
   <xsl:value-of select="name()"></xsl:value-of> -
  </xsl:template>

<!-- die hierarchische Struktur der inschriften und transkriptionen wird durchgearbeitet und neu getackt -->
    <xsl:template match="section[@sectiontype='inscription']">

       <xsl:param name="p_anzahl_inschriften"><xsl:for-each select="ancestor::article"><xsl:value-of select="count(sections//section[@sectiontype='inscription'])"/></xsl:for-each></xsl:param>
       <xsl:param name="p_anzahl_teile"><xsl:value-of select="count(section)"/></xsl:param>
       <xsl:param name="id1"><xsl:value-of select="@id"/></xsl:param>

        <xsl:param name="p_inschrift">
            <inschrift logg="i1">
                <xsl:copy-of select="@*"/>
                <!-- die antribute der inschrift-sections werden auf die container übertragen -->
                <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <!-- zugehörige inschriftteile suchen -->
                <xsl:for-each select="section">
                    <xsl:variable name="v_anzahl_bearbeitungen"><xsl:number count="section"/></xsl:variable>
                    <xsl:variable name="id2"><xsl:value-of select="@id"/></xsl:variable>
                    <xsl:variable name="verloren"><xsl:value-of select="items/item[@itemtype='lost']/value"/></xsl:variable>
                    <inschriftenteil logg="i2">
                        <xsl:copy-of select="@*"/>
                        <xsl:attribute name="verloren"><xsl:value-of select="$verloren"/></xsl:attribute>
                        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        <anzahl_bearbeitungen><xsl:value-of select="count(section)"/></anzahl_bearbeitungen>
<!-- ergänzung zur nummerierung für die münchener bände, aus dem feld datergaenzung der ersten bearbeitung -->
                        <ergaenzung><xsl:value-of select="section[1]/fields[1]/datergaenzung[1]"/></ergaenzung>
                        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        <!-- zugehörige bearbeitungen suchen -->
                        <xsl:for-each select="section">
                           <xsl:call-template name="bearbeitung"/>
                        </xsl:for-each>
                    </inschriftenteil>
                </xsl:for-each>
            </inschrift><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:param>

<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!--
<test_anzahl_inschriften><xsl:copy-of select="$p_anzahl_inschriften"/></test_anzahl_inschriften><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<test_anzahl_teile><xsl:copy-of select="$p_anzahl_teile"/></test_anzahl_teile><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<test_inschrift><xsl:copy-of select="$p_inschrift"/></test_inschrift><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
 -->
        <xsl:for-each select="$p_inschrift/inschrift">
            <inschrift>
                <xsl:copy-of select="@*"/>
                <xsl:for-each select="inschriftenteil">
                <xsl:variable name="v_anzahl_bearbeitungen"><xsl:value-of select="anzahl_bearbeitungen"/></xsl:variable>
                    <inschriftenteil log2="it1">
                        <xsl:copy-of select="@*"/>
                       <xsl:if test="ergaenzung[text()]"><xsl:copy-of select="ergaenzung"/></xsl:if>
                        <xsl:for-each select="bearbeitung/content[.//bl]">
                            <xsl:call-template name="transkriptionen">
                                <xsl:with-param name="p_anzahl_inschriften"><xsl:value-of select="$p_anzahl_inschriften"/></xsl:with-param>
                                <xsl:with-param name="p_anzahl_teile"><xsl:value-of select="$p_anzahl_teile"/></xsl:with-param>
                                <xsl:with-param name="p_anzahl_bearbeitungen"><xsl:value-of select="$v_anzahl_bearbeitungen"/></xsl:with-param>
                            </xsl:call-template>
                        </xsl:for-each>
                        <!-- wenn die übersetzungen inschriftenweise ausgegeben werden sollen, werden sie hier kopiert -->
                        <xsl:if test="bearbeitung/outputoption[text()='di_translations_sectionwise']">
                            <xsl:for-each select="bearbeitung/translation">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                        </xsl:if>
                    </inschriftenteil>
                </xsl:for-each>
            </inschrift>
        </xsl:for-each>
    </xsl:template>

<xsl:template name="bearbeitung">
    <bearbeitung log2="b1">
        <!-- die attribute der bearbeitung-sections werden auf die container übertragen -->
        <xsl:copy-of select="@*"/>
        <xsl:attribute name="projekt"><xsl:value-of select="ancestor::book/project/name"/></xsl:attribute>
        <xsl:attribute name="ausgabe"></xsl:attribute>
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
           <xsl:copy-of select="items/item/content"/>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
           <xsl:copy-of select="items/item/translation"/>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:copy-of select="ancestor::article/links"/>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="ancestor::article/sections/section[@sectiontype='outputoptions']/items/item/property">
            <outputoption><xsl:value-of select="norm_iri"/></outputoption>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
    </bearbeitung>
</xsl:template>

<!--  -->
<xsl:template name="transkriptionen">
   <xsl:param name="p_anzahl_inschriften"></xsl:param>
   <xsl:param name="p_anzahl_bearbeitungen"></xsl:param>
   <xsl:param name="p_anzahl_teile"/>

    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <bearbeitung>
        <xsl:copy-of select="@*"/>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <!-- die bezeichner der inschriften(teile) werden generiert und mit der id der bearbeitung-section versehen -->
<!--
<parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
inschriften: <xsl:copy-of select="$p_anzahl_inschriften"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
teile: <xsl:copy-of select="$p_anzahl_teile"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
bearbeitungen: <xsl:copy-of select="$p_anzahl_bearbeitungen"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->
        <nr log="n1">
            <xsl:attribute name="id"><xsl:value-of select="parent::*/@id"/></xsl:attribute>
             <xsl:attribute name="nr_bay">
                        <xsl:if test="$p_anzahl_inschriften = 1">
                            <xsl:if test="$p_anzahl_teile = 1"></xsl:if>
                            <xsl:if test="$p_anzahl_teile != 1"><xsl:number value="parent::inschrift/@number" format="1"/></xsl:if>
                        </xsl:if>
                        <xsl:if test="$p_anzahl_inschriften != 1">
                            <xsl:if test="$p_anzahl_teile = 1">
                            <xsl:number value="ancestor::inschrift/@number" format="I"/><xsl:if test="ancestor::inschriftenteil[@verloren='1']">&#x2020;</xsl:if>
                            </xsl:if>
                            <xsl:if test="$p_anzahl_teile != 1">
                                <xsl:number value="ancestor::inschrift/@number" format="I"/><xsl:number value="ancestor::inschriftenteil/@number" format="1"/><xsl:if test="ancestor::inschriftenteil[@verloren='1']">&#x2020;</xsl:if>
                            </xsl:if>
                        </xsl:if>
            </xsl:attribute>
            <xsl:if test="$p_anzahl_inschriften = 1">
                <xsl:if test="$p_anzahl_teile = 1"></xsl:if>
                <xsl:if test="$p_anzahl_teile != 1">
                    <xsl:value-of select="ancestor::inschriftenteil/@name"/>
                </xsl:if>
            </xsl:if>
            <xsl:if test="$p_anzahl_inschriften != 1">
                <xsl:if test="$p_anzahl_teile = 1">
                    <xsl:value-of select="ancestor::inschrift/@name"/>
                    <xsl:if test="ancestor::inschriftenteil[@verloren='1']">&#x2020;</xsl:if>
                </xsl:if>
                <xsl:if test="$p_anzahl_teile != 1">
                    <xsl:value-of select="ancestor::inschrift/@name"/><xsl:value-of select="ancestor::inschriftenteil/@name"/>
                    <xsl:if test="ancestor::inschriftenteil[@verloren='1']">&#x2020;</xsl:if>
                </xsl:if>
            </xsl:if>
        </nr><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <!-- der bezeichner der bearbeitung wird (als versionsnummer des inschriftenteils) generiert,
            wenn mehr als ein bearbeitung existiert bzw. ausgegeben werden soll -->
        <version>
            <xsl:if test="$p_anzahl_bearbeitungen !=1 ">
                <xsl:number level="single" count="bearbeitung" from="inschriftenteil" format="a"/>
            </xsl:if>
        </version><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

        <!-- die templates für die elemente der transkription werden aufgerufen -->
        <content>
            <xsl:apply-templates/>
        </content><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </bearbeitung><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>


    <xsl:template name="uebersetzungen"><!-- aufgerufen aus article -->
        <xsl:param name="db"></xsl:param>
        <uebersetzungen><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!--        <xsl:for-each select=".//uebersetzung[node()]">
            <xsl:call-template name="uebersetzung"/>
            </xsl:for-each>
            -->

            <xsl:for-each select="sections//section[@sectiontype='inscription']/section[@sectiontype='inscriptionpart']/section[@sectiontype='inscriptiontext']">
            <xsl:variable name="section-id"><xsl:value-of select="@id"/></xsl:variable>
                 <xsl:for-each select="items/item/translation[node()]">
                    <xsl:call-template name="uebersetzung">
                        <xsl:with-param name="db"><xsl:value-of select="$db"/></xsl:with-param>
                        <xsl:with-param name="section-id"><xsl:value-of select="$section-id"/></xsl:with-param>
                    </xsl:call-template>
                </xsl:for-each>
            </xsl:for-each>
        </uebersetzungen>
    </xsl:template>

    <xsl:template name="uebersetzung">

<!--        <xsl:param name="bearbeitung-parent-id"><xsl:value-of select="ancestor::section/@parent_section"/></xsl:param>
        <xsl:param name="inschriftteil-parent-id">
            <xsl:value-of select="//section[@id=$bearbeitung-parent-id]/@parent_section"/>
        </xsl:param>-->
        <xsl:param name="db"></xsl:param>
        <xsl:param name="section-id"></xsl:param>
        <xsl:param name="anzahl_inschriften">
            <xsl:for-each select="ancestor::article">
                <xsl:value-of select="count(sections//section[@sectiontype='inscription'])"/>
            </xsl:for-each>
        </xsl:param>
        <xsl:param name="anzahl_inschriftenteile">
                <xsl:for-each select="ancestor::section[@sectiontype='inscription']">
                    <xsl:value-of select="count(section)"/>
                </xsl:for-each>
        </xsl:param>
        <xsl:param name="anzahl_bearbeitungen">
            <xsl:for-each select="ancestor::section[@sectiontype='inscriptionpart']">
                <xsl:value-of select="count(section)"/>
            </xsl:for-each>
        </xsl:param>

        <!-- bezeichner der bearbeitung -->
        <xsl:param name="caption-bearbeitung">
            <xsl:value-of select="translate(ancestor::section[@sectiontype='inscriptiontext']/@number,'123456789','abcdefghi')"/>
        </xsl:param>
        <!-- bezeichner des inschriftteils -->
        <xsl:param name="caption-inschriftteil">
            <xsl:choose>
                <xsl:when test="$modus='projects_bay'"><xsl:number value="ancestor::section[@sectiontype='inscriptionpart']/@number" format="1"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="ancestor::section[@sectiontype='inscriptionpart']/@name"/></xsl:otherwise>
            </xsl:choose>
         </xsl:param>

        <!-- bezeichner der inschrift -->
        <xsl:param name="caption-inschrift">
             <xsl:choose>
                <xsl:when test="$modus='projects_bay'"><xsl:number value="ancestor::section[@sectiontype='inscription']/@number" format="I"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="ancestor::section[@sectiontype='inscription']/@name"/></xsl:otherwise>
            </xsl:choose>
        </xsl:param>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <!-- die bezeichner der inschriften(teile) werden generiert und mit der id der bearbeitung-section versehen -->
        <uebersetzung><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!--
<parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
inschriften: <xsl:copy-of select="$anzahl_inschriften"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
teile:  <xsl:copy-of select="$anzahl_inschriftenteile"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
bearbeitungen: <xsl:copy-of select="$anzahl_bearbeitungen"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
Nr. Inschrift: <xsl:copy-of select="$caption-inschrift"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
Nr. Inschriftteil: <xsl:copy-of select="$caption-inschriftteil"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
Nr. Version:<xsl:copy-of select="$caption-bearbeitung"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->
           <nr>
               <xsl:attribute name="section-id"><xsl:value-of select="$section-id"/></xsl:attribute>
                <xsl:if test="$anzahl_inschriften = 1">
                    <xsl:if test="$anzahl_inschriftenteile = 1"></xsl:if>
                    <xsl:if test="$anzahl_inschriftenteile &gt; 1">
                        <xsl:value-of select="$caption-inschriftteil"/>
                    </xsl:if>
                </xsl:if>
                <xsl:if test="$anzahl_inschriften &gt; 1">
                    <xsl:if test="$anzahl_inschriftenteile = 1">
                        <xsl:value-of select="$caption-inschrift"/>
                    </xsl:if>
                    <xsl:if test="$anzahl_inschriftenteile &gt; 1">
                        <xsl:value-of select="$caption-inschrift"/><xsl:value-of select="$caption-inschriftteil"/>
                    </xsl:if>
                </xsl:if>
            </nr><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <!-- der bezeichner der bearbeitung wird (als versionsnummer des inschriftenteils) generiert,
            wenn mehr als ein bearbeitung existiert bzw. ausgegeben werden soll -->
            <version>
                <xsl:if test="$anzahl_bearbeitungen !=1 ">
                    <xsl:value-of select="$caption-bearbeitung"/>
                </xsl:if>
            </version><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

            <!-- die templates für die elemente bzw. den text der übersetzung werden aufgerufen -->
            <content>
                <xsl:apply-templates/>
            </content><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </uebersetzung><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:template>


    <!--Muster fuer Formatierungen in der Transkription-->

    <!-- um eine Ausgabe in Epidoc zu ermöglichen, dürfte das trankriptions-markup nicht hier schon in ein klammersystem überfürt werden,
            sondern erst in der letzten, ausgabeformat-spezifischen transformationsstufe, zu pdf, doc, dio-xml-->

    <!--Bloecke in den Transkriptionen-->
    <xsl:template match="bl">
     <xsl:choose>
        <!-- inschriften mit marginalien am linken rand -->
        <xsl:when test="bl[contains(@data-link-value,'Marginalie links')]">
            <bl value="with_marg_left">
                <xsl:call-template name="trans_marginal"></xsl:call-template>
            </bl>
        </xsl:when>
        <xsl:when test="contains(@data-link-value,'Marginalie links')"><marg><xsl:apply-templates></xsl:apply-templates></marg></xsl:when>
        <xsl:otherwise>
            <bl>
                <!-- das attribut @align gibt den key wieder, @value den listeneintrag;
                nota bene: nicht alle bl haben ein attribut @align (kein key vergeben-->
                <xsl:attribute name="align">
                    <xsl:value-of select="@data-link-value" />
                    <xsl:if test="not(@data-link-value)"><xsl:value-of select="@align" /></xsl:if>
                </xsl:attribute>
                <!--vorbereitung der spalten fuer DIO-->
                <xsl:if test="contains(//einstellungen/Ausgabeformat,'dio') and ancestor::bl">
                    <xsl:text disable-output-escaping="yes">&#x003C;zeile position="0" &#x003E;</xsl:text>
                </xsl:if>
                <!-- wenn im block Verszeilen vorhanden sind, wird das taggen dieser Zeilen vorbereitet; zugleich wird mittels der Schriftart die Laufweite der Schrift abgeschaetzt -->
                <xsl:if test=".//vz and not(bl)">
                    <xsl:attribute name="schrifttyp">
                        <xsl:choose>
                            <xsl:when test="contains(ancestor::inschrift//schriftart//*, 'Majuskel') or
                                contains(ancestor::inschrift//schriftart//*, 'Kapitalis')
                                ">weit</xsl:when>
                            <xsl:otherwise>schmal</xsl:otherwise>
                        </xsl:choose>
                    </xsl:attribute>
                    <xsl:text disable-output-escaping="yes">&#x003C;vz indent='0'&#x003E;</xsl:text>
                </xsl:if>
                <xsl:apply-templates/>
                <xsl:if test=".//vz and not(bl)">
                    <xsl:text disable-output-escaping="yes">&#x003C;/vz&#x003E;</xsl:text>
                </xsl:if>
                <xsl:if test="contains(//einstellungen/Ausgabeformat,'dio') and ancestor::bl">
                    <xsl:text disable-output-escaping="yes">&#x003C;/zeile&#x003E;</xsl:text>
                </xsl:if>
            </bl>
        </xsl:otherwise>
    </xsl:choose>
    </xsl:template>

    <xsl:template name="trans_marginal">

           <xsl:text disable-output-escaping="yes">&#x003C;zeile log2="marg2"&#x003E;</xsl:text>
           <xsl:apply-templates></xsl:apply-templates>
           <xsl:text disable-output-escaping="yes">&#x003C;/zeile&#x003E;</xsl:text>


   </xsl:template>


    <!--die folgenden werden kopiert und erst im Formatierer ausgabespezifisch (je verschieden für doc, pdf oder dio-xml) ver
        arbeitet-->
    <!--unsichere Lesung-->
    <xsl:template match="insec">
        <!--<xsl:copy-of select="." />-->
        <insec><xsl:apply-templates></xsl:apply-templates></insec>
    </xsl:template>

    <!--Worttrenner-->
    <xsl:template match="wtr">
        <xsl:param name="wtr-id"><xsl:value-of select="@id"/></xsl:param>
        <xsl:param name="p_icon">
            <!-- wenn im feld Einheit (element unit) ein ausgabezeichen angegeben ist, wird dieses ausgewählt, anderenfalls ein hochpunkt gesetzt -->
                    <xsl:choose>
                        <xsl:when test="ancestor::bearbeitung/links/link[@from_tagid=$wtr-id]/property/unit[text()]">
                            <xsl:value-of select="ancestor::bearbeitung/links/link[@from_tagid=$wtr-id]/property/unit"/>
                        </xsl:when>
                        <xsl:when test="ancestor::article/links/link[@from_tagid=$wtr-id]/property/unit[text()]">
                            <xsl:value-of select="ancestor::article/links/link[@from_tagid=$wtr-id]/property/unit"/>
                        </xsl:when>
                        <xsl:otherwise><xsl:text>&#x00B7;</xsl:text></xsl:otherwise>
                    </xsl:choose>
        </xsl:param>
        <wtr log="wtr1">
           <xsl:copy-of select="@*"/>
            <xsl:value-of select="$p_icon"/>
        </wtr>
    </xsl:template>

    <!--Ligaturen-->
    <xsl:template match="all">
        <xsl:variable name="all-id"><xsl:value-of select="@id"/></xsl:variable>
<!--        <all>
            <xsl:attribute name="type">
                <xsl:value-of select="@type" />
            </xsl:attribute>
            <xsl:attribute name="value">
                <xsl:value-of select="@value" />
            </xsl:attribute>
            <xsl:apply-templates/>
        </all>-->

            <all>
                <xsl:copy-of select="@*"/>
               <!-- <xsl:for-each select="ancestor::bearbeitung/links/item[@tag_id=$all-id]">-->
<xsl:for-each select="ancestor::bearbeitung/links/link[@from_tagid=$all-id]">
                <xsl:copy-of select="@from_id"/>
                <xsl:attribute name="lemma">
                    <xsl:value-of select="property/lemma" />
                </xsl:attribute>
                <xsl:attribute name="name">
                    <xsl:value-of select="property/name" />
                </xsl:attribute>
                </xsl:for-each>
                <xsl:apply-templates/>
            </all>

    </xsl:template>
    <!--Versalien / ornamental initials-->
    <xsl:template match="vsl">
        <xsl:copy-of select="." />
    </xsl:template>
    <!--Initialen / initials-->
    <xsl:template match="ini">
        <xsl:copy-of select="." />
    </xsl:template>
    <!--hochgestellte Buchstaben / elevated letters-->
    <xsl:template match="sup[*|text()]">
        <xsl:copy-of select="." />
    </xsl:template>
    <!--Kapitaelchen / small caps-->
    <xsl:template match="kap">
        <xsl:copy-of select="." />
    </xsl:template>
    <!--Chronogrammbuchstaben-->
    <xsl:template match="chr">
        <xsl:copy-of select="." />
    </xsl:template>

    <!--versgerechte Zeilenumbrueche-->
    <xsl:template match="vz">
        <xsl:choose>
            <!-- in transkriptionen mit marginalien -->
            <xsl:when test="parent::bl/bl[contains(@data-link-value,'Marginalie')]">
            <xsl:choose>
                <xsl:when test="preceding-sibling::vz"><xsl:text disable-output-escaping="yes">&#x003C;/zeile&#x003E;&#x000D;&#x003C;zeile log2="marg1"&#x003E;</xsl:text></xsl:when>
            </xsl:choose>
            <!-- das element vz wird kopiert um später die einrückung zu verarbeiten-->
             <xsl:copy-of select="."/>
        </xsl:when>
            <!-- in sonstigen transkriptionen -->
        <xsl:otherwise>
                <xsl:choose>
                    <!-- im tranksriptionsfeld -->
                    <xsl:when test="ancestor::bl and not(parent::bl[contains(@data-link-value,'Marginalie')])">
                        <xsl:choose>
                            <xsl:when test="@indent='0' or @data-link-value='nicht einger&#x00FC;ckt'">
                                <xsl:text disable-output-escaping="yes">&#x003C;/vz&#x003E;&#x000D;&#x003C;vz indent="0"&#x003E;</xsl:text>
                            </xsl:when>
                            <xsl:when test="@indent='1' or @data-link-value='einger&#x00FC;ckt'">
                                <xsl:text disable-output-escaping="yes">&#x003C;/vz&#x003E;&#x000D;&#x003C;vz indent="1"&#x003E;</xsl:text>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:text disable-output-escaping="yes">&#x003C;/vz&#x003E;&#x000D;&#x003C;vz indent="0"&#x003E;</xsl:text>
                            </xsl:otherwise>
                        </xsl:choose>
                </xsl:when>
                    <!-- außerhalb des transkriptionsfeld, etwa in fussnoten, werden bei einrückung drei feste leerzeichen eingefügt -->
                    <xsl:otherwise>
                        <xsl:if test="@indent='1' or @data-link-value='einger&#x00FC;ckt'"><xsl:text>&#x00A0;&#x00A0;&#x00A0;</xsl:text></xsl:if>
                    </xsl:otherwise>
                </xsl:choose>
        </xsl:otherwise>
     </xsl:choose>
   </xsl:template>

    <!--Feste Leerstellen und Zwischenraeume / fixed blanks and gaps-->
    <xsl:template match="spatium_v">
        <xsl:copy-of select="." />
    </xsl:template>
    <!--die folgenden werden in diesem stylesheet verarbeitet-->
    <!--Zitate-->
    <xsl:template match="quot[*|text()]">
        <quot>
            <xsl:apply-templates/>
        </quot>
    </xsl:template>
    <!--Auslassungen / omissions-->
    <xsl:template match="oms">
        <xsl:for-each select=".">
            <xsl:text>[</xsl:text>
            <xsl:apply-templates/>
            <xsl:text>]</xsl:text>
        </xsl:for-each>
    </xsl:template>
    <!--nachgetragene Textbestandteile / text added later-->
    <!--<xsl:template match="add"><add>&lt;<xsl:apply-templates/>&gt;</add></xsl:template>-->
    <xsl:template match="add"><xsl:copy><xsl:copy-of select="@*"/><xsl:apply-templates/></xsl:copy></xsl:template>
    <!--Abbreviaturen / abbreviations-->
    <xsl:template match="abr">
        <xsl:choose>
            <xsl:when test="ancestor::all">
                <noAll><xsl:text>(</xsl:text><xsl:apply-templates/><xsl:text>)</xsl:text></noAll>
            </xsl:when>
            <xsl:otherwise>
                    <xsl:text>(</xsl:text><xsl:apply-templates/><xsl:text>)</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <!--Ergaenzungen-->
    <xsl:template match="cpl">
        <xsl:if test="not(preceding-sibling::node()[1][self::del or self::cpl]) and not(parent::cpl or parent::del)">[</xsl:if>
        <xsl:apply-templates/>
        <xsl:if test="not(following-sibling::node()[1][self::del or self::cpl]) and not(parent::cpl or parent::del)">]</xsl:if>
    </xsl:template>

    <!--Verlust / lost letters ist die Anzahl der verlorenen Zeichen unbekannt, werden drei Striche, ist sie bekannt, wird die entsprechende Anzahl Punkte in eckigen Klammern gesetzt; die Klammern entfallen, wenn die Verlustzeichen zwischen (ebenfalls eckigen) Ergaenzungsklammern gesetzt werden.-->
    <!-- &#x2011; geschützter bindestrich - in times new roman nicht verfügbar-->
    <!-- &#x202F; geschütztes schmales leerzeichen -->
    <!--
    &#x002D 	[- - -] hyphen-minus
    &#x02D7 	[˗ ˗ ˗] modifier letter minus sign
    &#x2012 	[‒ ‒ ‒] figure dash / ziffernbreiter gedankenstrich
    &#x2013 	[– – –] en dash / halbgeviertstrich
    -->
    <xsl:template match="del"><xsl:choose><xsl:when test="@num_sign='0'"><del><xsl:if test="not(preceding-sibling::node()[1][self::del or self::cpl]) and not(parent::cpl or parent::del or parent::abr or parent::add)">[</xsl:if>&#x2012;&#x202F;&#x2012;&#x202F;&#x2012;<!--&#x00A0;-&#x00A0;-&#x00A0;-&#x00A0;--><xsl:if test="not(following-sibling::node()[1][self::del or self::cpl]) and not(parent::cpl or parent::del or parent::abr or parent::add)">]</xsl:if></del></xsl:when><xsl:otherwise><del><xsl:if test="not(preceding-sibling::node()[1][self::del or self::cpl]) and not(parent::cpl or parent::del or parent::abr or parent::add)">[</xsl:if><xsl:call-template name="schleife"><xsl:with-param name="zaehler" select="@num_sign"/></xsl:call-template><xsl:if test="not(following-sibling::node()[1][self::del or self::cpl]) and not(parent::cpl or parent::del or parent::abr or parent::add)">]</xsl:if></del></xsl:otherwise></xsl:choose></xsl:template>
    <!--Schleife zum Auszaehlen der Punkte / find out number of lost letters-->
    <xsl:template name="schleife">
        <xsl:param name="zaehler"/>
        <xsl:choose>
            <xsl:when test="$zaehler &gt; 0">
                <xsl:text>.</xsl:text>
                <xsl:call-template name="schleife">
                    <xsl:with-param name="zaehler" select="$zaehler - 1"/>
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise></xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <!--Ende: Verlust / lost letters-->

    <!--Zeilenenden und Zeilenumbrueche und Zeilenunterbrechungen in den Transkriptionen / line breaks in transcriptions; alte Einschraenkung: z[preceding-sibling::text() or ancestor::app2]-->
    <xsl:template match="z">
        <xsl:choose>
            <xsl:when test="contains(//einstellungen/Ausgabeformat,'dio') and ancestor::bl[@data-link-value='Spalten']">
                <xsl:text disable-output-escaping="yes">&#x003C;/zeile&#x003E; &#x003C;zeile position="</xsl:text> <xsl:number  count="z" from="bl"/> <xsl:text disable-output-escaping="yes">" &#x003E;</xsl:text>
            </xsl:when>
            <!-- inschriften mit marginalien: die zeilenumbrüche werden in zeilencontainer umgewandelt -->
            <xsl:when test="parent::bl[not(vz)]/bl[contains(@data-link-value,'Marginalie links')]">
                <xsl:if test="following-sibling::z"><xsl:text disable-output-escaping="yes">&#x003C;/zeile&#x003E; &#x003C;zeile&#x003E;</xsl:text></xsl:if>
            </xsl:when>
            <xsl:otherwise>
                <xsl:choose>
                    <xsl:when test="@connex='nz' or starts-with(@data-link-value,'neue Zeile')">
<!-- damit die vor den slashs stehenden leerzeichen auch in hebräischen texten richtig positioniert werden, muss vor den slash das steuerzeichen LRM (left-to-right-mark, U+200E) gesetzt werden-->
                        <z><xsl:copy-of select="@*"/>&#x200E;/</z>
                    </xsl:when>
                    <xsl:when test="@connex='fw' or starts-with(@data-link-value,'Fragmentwechsel')">
                        <z><xsl:copy-of select="@*"/>&#x200E;//</z>
                    </xsl:when>
                    <xsl:otherwise>
                        <z><xsl:copy-of select="@*"/>&#x200E;//</z>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <!--Ende: Formate in den Transkriptionen-->

</xsl:stylesheet>
