<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:epi="http://epigraf.inschriften.net#xslt-functions"
    exclude-result-prefixes="xs epi"
    version="2.0">

    <xsl:import href="../commons/di-switch.xsl"/>

    <xsl:import href="di-trans3-commons.xsl"/>

    <xsl:import href="di-trans3-generals-source.xsl"/>
    <xsl:import href="di-trans3-generals-addition.xsl"/>
    <xsl:import href="di-trans3-generals-fonttype.xsl"/>
    <xsl:import href="di-trans3-generals-fontsize.xsl"/>
    <xsl:import href="di-trans3-generals-measure.xsl"/>
    <xsl:import href="di-trans3-inscriptions.xsl"/>
    <xsl:import href="di-trans3-loc-ten.xsl"/>
    <xsl:import href="di-trans3-dating.xsl"/>

    <!-- wichtiger hinweis: der test mit dem operator &lt;= o.ä. funktioniert in xsl version 2.0 nur mit Umwandlung in number()
    Falsch:  <xsl:if test="$p_columnnumber &lt;= $p_max_columns">
    Richtig: <xsl:if test="number($p_columnnumber) &lt;= number($p_max_columns)">-->

<!-- nota bene: <folge> muss ab trans1 noch umbenannt werden -->

    <!--
        in diesem stylesheet wird der katalog der inschriftenartikel (element <articles>)
        auf der transformationsstufe 3 weiter strukturiert.

        es wird aufgerufen in di-trans3.xsl
    -->

    <!-- norm_iri der über dropdown benannten sections in den articles:

        Allgemeine Angaben: Buchstabenhöhe      = di_generals_fontsize
        Allgemeine Angaben: Ergänzungen nach    = di_generals_addition
        Allgemeine Angaben: Inschriften nach    = di_generals_source
        Allgemeine Angaben: Maße                = di_generals_measure
        Allgemeine Angaben: Schriftarten        = di_generals_fonttype
        Allgemeine Angaben: Sonstiges           = di_generals_other
        Beschreibung                            = di_description
        Datum in der Inschrift                  = di_date_on_inscription
        Gliederung: Ebene 1                     = di_header1
        Gliederung: Ebene 2                     = di_header2
        Gliederung: Ebene 3                     = di_header3
        Kommentar                               = di_comment
        Kopfzeile: Datierung                    = di_headline_date
        Kopfzeile: Standorte                    = di_headline_site
        Transkriptionsspalten                   = di_edit_columns
        Versmaße                                = di_metres
        Segment                                 = di_segment

        Zitatquellen                            = di_citation_source
        Inschriften nummerieren                 = di_inscription_numbering
    -->


    <!-- allgemeine parameter -->
    <!-- a) bezeichnung der datenbank -->
    <xsl:param name="p_db"><xsl:value-of select="book/project/database"/></xsl:param>
    <!-- b) sprache des projekts (für die estnischen kolleginnen)-->
    <xsl:param name="p_projectlanguage">
        <!-- Sprachenkürzel nach ISO 639-1; siehe https://wiki.selfhtml.org/wiki/Sprachk%C3%BCrzel -->
        <xsl:choose>
            <xsl:when test="$p_db='inscriptiones_estoniae'">et</xsl:when>
            <xsl:otherwise>de</xsl:otherwise>
        </xsl:choose>
    </xsl:param>
    <!-- c) basistandort des project (bei städtischen beständen: name der stadt -->
    <xsl:param name="p_basesite"><xsl:value-of select="book/project/name"/></xsl:param>

<!-- katalog der inschriftenartikel aufbauen -->
    <xsl:template name="articles">
        <xsl:param name="p_projectlanguage"></xsl:param>
        <!-- container für den katalog (element <articles>) neu anlegen -->
        <articles>
            <!-- attribute kopieren -->
            <xsl:copy-of select="articles/@*"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- katalogtitel erstellen -->
            <xsl:for-each select="articles/title">
                <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>

            <!-- artikel ansteuern und zur weiteren transformation an templates verweisen-->
            <xsl:for-each select="articles/article">
                <xsl:apply-templates select=".">
                    <xsl:with-param name="p_projectlanguage"><xsl:value-of select="$p_projectlanguage"/></xsl:with-param>
                </xsl:apply-templates>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </articles><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

<!-- ANFANG: artikel -->
    <!-- den einzelnen katalogartikel erstellen -->
    <xsl:template match="article[@articletype='epi-article']">
        <xsl:param name="p_projectlanguage"></xsl:param>
        <xsl:param name="p_number_of_inscriptions" select="count(.//section[@sectiontype='inscription'][not(parent::link)])" />
        <xsl:param name="p_number_of_inscriptionparts" select="count(.//section[@sectiontype='inscriptionpart'])" />
        <xsl:param name="p_number_of_versions" select="count(.//section[@sectiontype='inscriptiontext'])" />
        <xsl:param name="p_number_of_translations" select="count(.//translation[node()])" />
        <xsl:param name="p_anzahl_kommentare" select="count(sections/section[@kategorie='Kommentar' or @norm_iri='di_comment'])" />
        <xsl:param name="option_blockwise" select=".//section[@sectiontype='outputoptions']//property/norm_iri[text()='di_translations_blockwise']" />

        <!-- artikel neu anlegen, attibute hinzufügen bzw. kopieren -->
        <article>
            <xsl:attribute name="number_of_inscriptions" select="$p_number_of_inscriptions" />
            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!--
<test-anzahl-kommentare><xsl:copy-of select="$anzahl_kommentare"/></test-anzahl-kommentare>
<xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->
            <!-- ausgabeoptionen neu anlegen und konfigurieren -->
            <outputoptions><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- die einzelnen optionen ansteuern und neu konfigurieren -->
                <xsl:for-each select="sections/section[@sectiontype='outputoptions']/items/item/property">
                    <outputoption>
                        <!-- elemente in attribute umwandeln -->
                        <xsl:attribute name="lemma"><xsl:value-of select="lemma"/></xsl:attribute>
                        <xsl:attribute name="name"><xsl:value-of select="name"/></xsl:attribute>
                        <xsl:attribute name="norm_iri"><xsl:value-of select="norm_iri"/></xsl:attribute>
                    </outputoption><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </outputoptions><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- kopfzeile -->
            <headline><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- titel des artikels -->
                <title><xsl:value-of  select="name"/></title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- metadaten der bearbeitung des artikels -->
                <created><xsl:value-of select="created"/></created><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <modified><xsl:value-of select="modified"/></modified><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <creator><xsl:value-of select="creator/name"/></creator><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <modifier>
                    <xsl:attribute name="acronym"><xsl:value-of select="modifier/acronym"/></xsl:attribute>
                    <xsl:value-of select="modifier/name"/>
                </modifier><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

                <!-- signatur -->
                <signature><xsl:value-of select="signature"/></signature><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>


                <!-- artikelnummer -->
                <articlenumber><xsl:number count="article" from="articles" format="1"/></articlenumber><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

                <!-- überlieferungszustand -->
                    <!-- a) erläuterungstext -->
                <trad_index><xsl:value-of select="sections/section[@sectiontype='conditions']/items/item/property/@id"/></trad_index><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- b) symbol -->
                <trad_sigle>
                    <xsl:for-each select="sections/section[@sectiontype='conditions']/items/item/property">
                        <!-- symbole gemäß überlieferungszusand einspielen:
                                sterbekreuz (&#x2020;), sterbekreuz in klammern, sterbekreuz mit fragezeichen -->
                        <xsl:choose>
                            <xsl:when test="norm_iri='di_traditio1'"></xsl:when>
                            <xsl:when test="norm_iri='di_traditio2'"><xsl:text>&#x2020;</xsl:text></xsl:when>
                            <xsl:when test="norm_iri='di_traditio3'"><xsl:text>(&#x2020;)</xsl:text></xsl:when>
                            <xsl:when test="norm_iri='di_traditio4'"><xsl:text>(&#x2020;)</xsl:text></xsl:when>
                            <xsl:when test="norm_iri='di_traditio5'"><xsl:text>(&#x2020;)</xsl:text></xsl:when>
                            <xsl:when test="norm_iri='di_traditio6'"><xsl:text>&#x2020;?</xsl:text></xsl:when>
                            <xsl:otherwise></xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>
                </trad_sigle><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

                <!-- standorte -->
                <xsl:choose>
                    <!-- wenn ein eigener abschnitt für die standortangaben angelegt wurde: -->
                    <xsl:when test="sections/section[@norm_iri='di_headline_site']">
                        <!-- container für standorte anlegen -->
                        <locations>
                            <!-- die eizelnen standorte einfügen -->
                            <location><xsl:value-of select="sections/section[@norm_iri='di_headline_site']/items/item/content"/></location>
                        </locations>
                    </xsl:when>
                    <!-- wenn kein eigener abschnitt angelegt wurde: template zum ermitteln der standortangaben aufrufen -->
                    <xsl:otherwise><xsl:call-template name="locations"/></xsl:otherwise>
                </xsl:choose>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

                <!-- datierung -->
                <dating>
                    <xsl:call-template name="dating"></xsl:call-template>
                </dating>
            </headline><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- beschreibung: template aufrufen -->
            <xsl:call-template name="description"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- corrigenda: template aufrufen -->
            <xsl:call-template name="corrigenda"/>

            <!-- verschieden angaben zu inschriften
                 (schriftart, schrifthöhe, quelle der wiedergabe und der ergänzungen):
                 template aufrufen
            -->
            <xsl:call-template name="generals">
                <xsl:with-param name="p_projectlanguage" select="$p_projectlanguage" />
                <xsl:with-param name="p_number_of_inscriptions" select="$p_number_of_inscriptions" />
            </xsl:call-template>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- inschriften und zwischentexte -->
            <xsl:variable name="article" select="."/>

            <xsl:for-each-group select="sections/section[@sectiontype='text' or @sectiontype='inscription']"
                group-adjacent="@sectiontype">

                <!-- Inschriften blockweise -->
                <xsl:choose>

                    <!-- Case 1: This is a block of inscription sections -->
                    <xsl:when test="current-grouping-key() = 'inscription'">

                        <!-- Ausgabe der Inschriften -->
                        <xsl:for-each select="current-group()">
                            <xsl:call-template name="inscriptioncontent">
                                <xsl:with-param name="p_number_of_inscriptions" select="$p_number_of_inscriptions" />
                                <xsl:with-param name="p_number_of_inscriptionparts" select="$p_number_of_inscriptionparts" />
                                <xsl:with-param name="p_number_of_versions" select="$p_number_of_versions" />
                                <xsl:with-param name="p_number_of_translations" select="$p_number_of_translations" />
                                <xsl:with-param name="p_anzahl_kommentare" select="$p_anzahl_kommentare" />
                            </xsl:call-template>
                        </xsl:for-each>


                        <!-- Blockweise Ausgabe der Übersetzungen und Versmaße -->
                        <xsl:if test="$option_blockwise">
                            <xsl:call-template name="inscriptiondata">
                                <xsl:with-param name="article" select="$article" />
                                <xsl:with-param name="sections" select="current-group()" />
                                <xsl:with-param name="p_number_of_inscriptions" select="$p_number_of_inscriptions"/>
                            </xsl:call-template>
                        </xsl:if>
                    </xsl:when>

                    <!--  Case 2: This is a block of NON-inscription sections -->
                    <xsl:otherwise>
                        <xsl:for-each select="current-group()">
                            <xsl:call-template name="inscriptioncontent">
                                <xsl:with-param name="p_number_of_inscriptions" select="$p_number_of_inscriptions" />
                                <xsl:with-param name="p_number_of_inscriptionparts" select="$p_number_of_inscriptionparts" />
                                <xsl:with-param name="p_number_of_versions" select="$p_number_of_versions" />
                                <xsl:with-param name="p_number_of_translations" select="$p_number_of_translations" />
                                <xsl:with-param name="p_anzahl_kommentare" select="$p_anzahl_kommentare" />
                            </xsl:call-template>
                        </xsl:for-each>
                    </xsl:otherwise>
                </xsl:choose>


                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each-group>

            <!-- buchstabenapparat münchener reihe (projects_bay) -->
            <xsl:if test="$sw_modus='projects_bay'">
                <footnotes><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:call-template name="letter_footnotes"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </footnotes><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:if>

            <!-- zitatquellen: abschnitt mit der angabe von der herkunft von zitaten
                    in der/den inschrift/en (wenn vorhanden) -->
            <xsl:if test="sections/section[@norm_iri='di_citation_source']">
                <xsl:for-each select="sections/section[@norm_iri='di_citation_source']">
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- abschnitt anlegen, das element <content>
                            ansteuern und an templates verweisen -->
                    <di_citation_source><xsl:copy-of select="@*"/><xsl:apply-templates select="items/item/content"></xsl:apply-templates></di_citation_source>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </xsl:if>

            <xsl:if test="not($option_blockwise)">
                <xsl:call-template name="inscriptiondata">
                    <xsl:with-param name="p_number_of_inscriptions" select="$p_number_of_inscriptions"></xsl:with-param>
                    <xsl:with-param name="article" select="." />
                    <xsl:with-param name="sections" select="./sections/section" />
                </xsl:call-template>
            </xsl:if>

            <!-- wenn ein zwischentext für die angabe der versformen angelegt wurde,
                    diesen ansteuern und wie eine textfeld (z. b. beschreibung) verarbeiten -->
            <xsl:if test="./sections/section[@norm_iri='di_metres']">
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <di_metres>
                    <xsl:for-each select="./sections/section[@norm_iri='di_metres']">
                        <xsl:apply-templates select="items/item/content"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </di_metres>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:if>

            <!-- abschnitt für datumsangaben aus/in der inschrift -->
            <xsl:if test="sections/section[@norm_iri='di_date_on_inscription']">
                <xsl:for-each select="sections/section[@norm_iri='di_date_on_inscription']">
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <di_date_on_inscription><xsl:copy-of select="@*"/><xsl:apply-templates select="items/item/content"></xsl:apply-templates></di_date_on_inscription>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </xsl:if>

            <!-- abschnitt für angaben zu wappen: template aufrufen -->
            <xsl:call-template name="heraldry"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- kommentar: template aufrufen -->
            <xsl:call-template name="comment"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- apparate anlegen-->
            <footnotes><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- buchstabenfußnoten: template aufrufen (außer münchener reihe) -->
                <xsl:if test="not($sw_modus='projects_bay')">
                    <xsl:call-template name="letter_footnotes"></xsl:call-template>
                </xsl:if>
                <!-- ziffernfußnoten: template aufrufen -->
                <xsl:call-template name="collect_digit_footnotes"/>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </footnotes><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- nachweise: template aufrufen -->
            <xsl:call-template name="references"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- bilder: template aufrufen -->
            <xsl:call-template name="images"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- notizen (wenn laut ausgabeoptionen vorgesehen) -->
            <xsl:call-template name="notes"></xsl:call-template>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

        </article>
    </xsl:template>
<!-- ENDE: artikel -->

  <!-- standorte in der kopfzeile -->
  <xsl:template name="locations">
    <!-- level umtauschen bei landkreisen zwecks nachfolgender umsortierung -->
    <xsl:param name="p_basesite" select="ancestor::book/project/name"></xsl:param>

      <!-- die folgenden beiden parameter sind obsolet geworden und wurden durch den dritten ersetzt -->
 <!--    <xsl:param name="p_locations_sort1">
        <xsl:for-each select="locations/location">
            <location>
               <xsl:for-each select="lemma[.=$p_basesite][1]">
                   <xsl:attribute name="basesite"><xsl:value-of select="$p_basesite"/></xsl:attribute>
               </xsl:for-each>
                <xsl:for-each select="lemma[not(text()=$p_basesite)]">
                    <xsl:choose>
                        <!-\- wenn es sich um einen landkreis handelt -\->
                        <xsl:when test="starts-with(.,'Ldkr.') or starts-with(.,'Landkreis') or starts-with(.,'Lkr.')">
                            <!-\- und wenn das editions-projekt der landkreis ist -\->
                            <xsl:if test=".=$p_basesite">
                            <lemma level="1.1">
                                <xsl:value-of select="$p_basesite"/><xsl:text>(</xsl:text><xsl:value-of select="."/> <xsl:text>)</xsl:text>
                            </lemma></xsl:if>
                        </xsl:when>
                        <!-\- wenn es sich um keinen landkreis handelt oder der landkreis nicht das projekt ist
                            (der standort also in einem landkreis außerhalb des projekt-landkreises liegt -\->
                        <xsl:otherwise><xsl:copy-of select="." copy-namespaces="no"/></xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
            </location>


        </xsl:for-each>
    </xsl:param>
    <!-\- umsortieren -\->
    <xsl:param name="p_locations_sort2">
        <xsl:for-each select="$p_locations_sort1/location">
            <location><xsl:copy-of select="@*"/>
                <xsl:for-each select="lemma">
                    <xsl:sort order="ascending" select="@level" data-type="number"/>
                  <xsl:copy-of select="." copy-namespaces="no"/>
                </xsl:for-each>
            </location>
        </xsl:for-each>
    </xsl:param>-->

      <xsl:param name="p_locations">
          <!-- in diesem parameter werden die obereinträge der standorte,
              weil sie dem erfassungsgebiet des projekts entsprechen, ausgeblendet
          bei standorten außerhalb des erfassungsgebietes wird die
          lankreisbezeichnung (level 1) an die ortsbezeichnung (level 2) in klammern angehängt
          Beispiele:
          aus "Lkr. Nienburg, Nienburg, Museum Nienburg" wird "Nienburg (Lkr. Nienburg), Museum Nienburg"
          aus "Lkr. Helmstedt, Helmstedt, Kloster St. Marienberg" wird "Helmstedt (Lkr. Helmstedt), Kloster St. Marienberg"
          -->
          <xsl:for-each select="locations/location">
              <location>
                  <!-- basisstandort (wenn vorhanden) als attribut anfügen -->
                  <xsl:for-each select="lemma[.=$p_basesite][1]">
                      <xsl:attribute name="basesite"><xsl:value-of select="$p_basesite"/></xsl:attribute>
                  </xsl:for-each>
                  <xsl:for-each select="lemma[not(.=$p_basesite)]">
                      <xsl:choose>
                          <!-- erstes lemma -->
                          <xsl:when test="position()=1">
                              <!-- wenn es sich bei dem ersten lemma um einen landkreis handelt, wird es ausgeblendet -->
                              <xsl:choose>
                                  <xsl:when test="starts-with(.,'Ldkr.') or starts-with(.,'Landkreis') or starts-with(.,'Lkr.')">
                                  </xsl:when>
                                  <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
                              </xsl:choose>
                          </xsl:when>
                          <xsl:when test="position()=2">
                              <lemma><xsl:value-of select="."/>
                                  <!-- wenn der standort in einem landkreis liegt, der nicht der projektbezeichnung entspricht,
                          wird die landkreisbezeichnung in klammern angehängt-->
                                  <xsl:if test="starts-with(preceding-sibling::lemma[1],'Ldkr.') or starts-with(preceding-sibling::lemma[1],'Landkreis') or starts-with(preceding-sibling::lemma[1],'Lkr.')">
                                      <xsl:if test="preceding-sibling::lemma[1]!=$p_basesite">
                                          <xsl:text> (</xsl:text><xsl:value-of select="preceding-sibling::lemma[1]"/><xsl:text>)</xsl:text>
                                      </xsl:if>
                                  </xsl:if>
                              </lemma>
                          </xsl:when>
                          <xsl:otherwise><xsl:copy-of select="." copy-namespaces="no"/></xsl:otherwise>
                      </xsl:choose>
                  </xsl:for-each>
              </location>
          </xsl:for-each>
      </xsl:param>

      <!-- früheres, nun obsoletes muster: -->
      <!-- einfügen, bei mehreren standorten kommasepariert -->
<!--    <locations log3="sto1">
        <xsl:for-each select="$p_locations_sort2/location">
          <location><xsl:copy-of select="@*"/>
            <xsl:for-each select="lemma">
                <xsl:value-of select="."/><xsl:if test="following-sibling::lemma[1][not(starts-with(.,'('))]"><xsl:text>,</xsl:text></xsl:if><xsl:if test="following-sibling::lemma"><xsl:text> </xsl:text></xsl:if>
             </xsl:for-each>
            </location>
        </xsl:for-each>
      </locations>-->


      <!-- standorte ausgeben  -->
      <locations log3="sto1">
          <!-- parameter ansteuern -->
          <xsl:for-each select="$p_locations/location">
              <location><xsl:copy-of select="@*"/>
                  <!-- lemmata kommasepariert aufreihen -->
                  <xsl:for-each select="lemma">
                      <xsl:value-of select="."/><xsl:if test="following-sibling::lemma"><xsl:text>, </xsl:text></xsl:if>
                  </xsl:for-each>
              </location>
          </xsl:for-each>
      </locations>

      <!-- alternativ: -->

<!--
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <p_standorte_sort1><xsl:copy-of select="$p_locations_sort1"/></p_standorte_sort1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <p_standorte_sort2><xsl:copy-of select="$p_locations_sort2"/></p_standorte_sort2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->
  </xsl:template>

    <!-- anmerkungen zu corrigenda -->
    <xsl:template name="corrigenda">
        <xsl:if test="sections/section[@sectiontype='corrigenda']">
            <di_corrigenda>
                <xsl:for-each select="sections/section[@sectiontype='corrigenda']/items/item[@itemtype='corrigenda']">
                    <item>
                        <xsl:attribute name="id" select="@id" />
                        <xsl:apply-templates select="./content"/>
                    </item>
                </xsl:for-each>
            </di_corrigenda>
        </xsl:if>
    </xsl:template>


    <!-- beschreibung des objekts -->
  <xsl:template name="description">
    <!-- wenn mehrere beschreibungen vorhanden sind, wird hier die erste eingefügt -->
      <di_description><xsl:apply-templates select="sections/section[@norm_iri='di_description'][1]/items/item[@itemtype='text']/content"/></di_description>
  </xsl:template>

    <!-- Inschriften und Zwischenüberschriften -->
    <xsl:template name="inscriptioncontent">
        <xsl:param name="p_number_of_inscriptions" />
        <xsl:param name="p_number_of_inscriptionparts" />
        <xsl:param name="p_number_of_versions" />
        <xsl:param name="p_number_of_translations" />
        <xsl:param name="p_anzahl_kommentare" />

        <xsl:choose>

            <xsl:when test="starts-with(@norm_iri, 'di_generals')"><!-- schon weiter oben (generals) behandelt --></xsl:when>
            <xsl:when test="@norm_iri='di_comment' and $p_anzahl_kommentare=1"><!-- schon weiter oben behandelt --></xsl:when>
            <xsl:when test="@norm_iri='di_description'">
                <xsl:choose>
                    <xsl:when test="preceding-sibling::section[@norm_iri='di_description']">
                        <xsl:apply-templates select=".">
                            <xsl:with-param name="p_number_of_inscriptions" select="$p_number_of_inscriptions" />
                            <xsl:with-param name="p_number_of_inscriptionparts" select="$p_number_of_inscriptionparts" />
                            <xsl:with-param name="p_number_of_versions" select="$p_number_of_versions" />
                            <xsl:with-param name="p_number_of_translations" select="$p_number_of_translations" />
                        </xsl:apply-templates>
                    </xsl:when>
                    <xsl:otherwise><!-- die erste bechreibung wurde schon eingefügt --></xsl:otherwise>
                </xsl:choose>
            </xsl:when>

            <!-- transkriptionsspalten: inschriften werden spaltenweise nebeneinander dargestellt,
                                     ihre bezeichner (A, B, usw.) davor zusammengefasst (z. b. A-B) -->
            <xsl:when test="@norm_iri='di_edit_columns'">
                <di_edit_columns>
                    <xsl:for-each select="section">
                        <xsl:apply-templates select=".">
                            <xsl:with-param name="p_number_of_inscriptions" select="$p_number_of_inscriptions" />
                            <xsl:with-param name="p_number_of_inscriptionparts" select="$p_number_of_inscriptionparts" />
                            <xsl:with-param name="p_number_of_versions" select="$p_number_of_versions" />
                            <xsl:with-param name="p_number_of_translations" select="$p_number_of_translations" />
                        </xsl:apply-templates>
                    </xsl:for-each>
                </di_edit_columns>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:when>

            <!-- sonstige abschnitte -->
            <xsl:otherwise>
                <xsl:apply-templates select=".">
                    <xsl:with-param name="p_number_of_inscriptions" select="$p_number_of_inscriptions" />
                    <xsl:with-param name="p_number_of_inscriptionparts" select="$p_number_of_inscriptionparts" />
                    <xsl:with-param name="p_number_of_versions" select="$p_number_of_versions" />
                    <xsl:with-param name="p_number_of_translations" select="$p_number_of_translations" />
                </xsl:apply-templates>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- Übersetzungen und Versmaße -->
    <xsl:template name="inscriptiondata">
        <xsl:param name="p_number_of_inscriptions" />
        <xsl:param name="article" select="." />
        <xsl:param name="sections" select="./sections/section" />

        <!-- übersetzungen: template aufrufen (in di-trans3-inscriptions.xsl) -->
        <xsl:call-template name="translations">
            <xsl:with-param name="p_db"><xsl:value-of select="$p_db"/></xsl:with-param>
            <xsl:with-param name="sections" select="$sections" />
        </xsl:call-template>


        <!-- abschnitt mit den angaben zu versformen -->
        <xsl:choose>
            <!-- wenn ein zwischentext für die angabe der versformen angelegt wurde,
                    diesen ansteuern und wie eine textfeld (z. b. beschreibung) verarbeiten -->
            <xsl:when test="$article/sections/section[@norm_iri='di_metres']"><!-- Wird einmal insgesamt ausgegeben --></xsl:when>
            <!-- anderenfalls die angaben aus dem register zusammentragen -->
            <xsl:otherwise>
                <xsl:call-template name="verses">
                    <xsl:with-param name="sections" select="$sections" />
                    <xsl:with-param name="p_number_of_inscriptions" select="$p_number_of_inscriptions" />
                </xsl:call-template>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- kommentar -->
  <xsl:template name="comment">
    <xsl:for-each select="sections/section[@norm_iri='di_comment']">
      <xsl:choose>
          <xsl:when test="preceding-sibling::section[@norm_iri='di_comment']"></xsl:when>
          <xsl:when test="following-sibling::section[@norm_iri='di_comment']"></xsl:when>
        <xsl:otherwise>
          <xsl:for-each select="items/item[@itemtype='text']/content">
            <di_comment log="k1"><xsl:apply-templates select="."/></di_comment>
          </xsl:for-each>
        </xsl:otherwise>
      </xsl:choose>
     </xsl:for-each>
  </xsl:template>

    <xsl:template match="content[parent::item[@itemtype='text']]">
        <!-- Wrap nl chunks in p -->
        <xsl:choose>
            <xsl:when test="nl">
                <xsl:for-each-group select="node()" group-ending-with="nl">
                    <p><xsl:apply-templates select="current-group()[not(self::nl)]" /></p><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each-group>
                <!-- if last child is nl, output an empty p -->
                <!-- TODO: This mimics the old behaviour. Is it really necessary? -->
                <xsl:if test="node()[last()][self::nl]">
                    <xsl:element name="p" />
                    <!--xsl:text disable-output-escaping="yes">&#x000A;</xsl:text-->
                </xsl:if>
            </xsl:when>
            <xsl:otherwise><p><xsl:apply-templates /></p></xsl:otherwise>
        </xsl:choose>

    </xsl:template>

  <!--xsl:template match="content[parent::item[@itemtype='text']]"><p><xsl:apply-templates /></p></xsl:template-->

    <!-- verschiedene angaben zu den inschriften -->
    <xsl:template name="generals">
      <xsl:param name="p_projectlanguage" />
      <xsl:param name="p_number_of_inscriptions" />

      <!-- 1) die angaben in einem parameter zusammenstellen -->
      <xsl:param name="p_generals">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

        <!-- inschrift nach -->
        <xsl:call-template name="generals_source">
              <xsl:with-param name="p_projectlanguage"><xsl:value-of select="$p_projectlanguage"/></xsl:with-param>
              <xsl:with-param name="p_db"><xsl:value-of select="$p_db"/></xsl:with-param>
        </xsl:call-template>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

        <!-- ergänzung nach -->
        <xsl:call-template name="generals_addition">
           <xsl:with-param name="p_projectlanguage"><xsl:value-of select="$p_projectlanguage"/></xsl:with-param>
        </xsl:call-template>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

        <!-- sonstige angaben (münchener reihe) -->
        <xsl:if test="sections/section[@norm_iri='di_generals_other']">
               <di_generals_other><xsl:apply-templates select="sections/section[@norm_iri='di_generals_other']/items/item[@itemtype='text']/content"/></di_generals_other>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>

        <!-- massangaben -->
        <xsl:call-template name="generals_measure"></xsl:call-template>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

        <!-- schrifthöhen -->
        <xsl:call-template name="generals_fontsize">
           <xsl:with-param name="p_number_of_inscriptions" select="$p_number_of_inscriptions" />
        </xsl:call-template>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

        <!-- schriftarten -->
        <xsl:call-template name="generals_fonttype" />
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:param>

    <!-- 2. allgemeine angaben aus dem parameter einfügen -->
    <di_generals><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

        <xsl:for-each select="$p_generals/*[*|text()]">
            <xsl:copy-of select="." copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>

        <!-- liste der abbildungen im tafelteil hinzufügen-->
        <xsl:for-each select="plates_list">
            <xsl:copy><xsl:value-of select="."/></xsl:copy><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>

   </di_generals><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
 </xsl:template>


  <!-- nachweisfeld: literatur-und quellenangaben am ende des artikels -->
  <xsl:template name="references">
      <!-- container für die nachweise anlegen -->
    <references><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- die einzelnen nachweise ansteuern -->
    <xsl:for-each select="sections/section[@sectiontype='references']/items/item[*]">
        <!-- für jeden nachweis ein <item> anlegen -->
      <item>
        <!-- um eine verknüpfung mit dem literaturverzeichnis zu ermöglichen,
                wird die id der literaturangabe kopiert -->
        <xsl:copy-of select="property/@id"/>
          <!-- template zur formatierung des einzelnachweises aufrufen -->
        <xsl:call-template name="reference"/>
      </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:for-each>
    </references>
  </xsl:template>

  <!-- einzelnachweise -->
  <xsl:template name="reference">
    <!-- es wird ermittelt, ob die ergänzung zum literaturtitel mit einem punkt endet -->
      <xsl:param name="p_string-length_addition"><xsl:value-of select="string-length(content)"/></xsl:param>
      <xsl:param name="p_end_of_addition"><xsl:value-of select="substring(content, $p_string-length_addition)"/></xsl:param>

      <!-- hier wird festgelegt, ob die verknüpfung des titels mit der ergänzung durch ein komma (mit leerschritt
      oder nur durch leerschritt) erfolgt -->
    <xsl:param name="p_junctor">
        <xsl:variable name="v_content"><xsl:value-of select="content"/></xsl:variable>
        <xsl:for-each select="ancestor::book/types/type[@norm_iri='literature']//comma">
            <xsl:variable name="v_comma" select="."/>
                <xsl:if test="starts-with($v_content,$v_comma)"><xsl:text>,</xsl:text></xsl:if>
        </xsl:for-each>
    </xsl:param>

    <xsl:param name="p_shorttitle">
      <!-- kurztitel auslesen und zwischenspeichern -->
      <xsl:for-each select="property">
        <link type="literatur">
            <xsl:choose>
                <xsl:when test="@ishidden = '1'">
                    <xsl:variable name="target" select="ancestors/property[@ishidden='0'][1]"/>
                    <xsl:choose>
                        <xsl:when test="$target">
                            <xsl:attribute name="target_id"><xsl:value-of select="$target/@id"/></xsl:attribute>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:attribute name="ishidden"><xsl:value-of select="@ishidden"/></xsl:attribute>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:attribute name="target_id"><xsl:value-of select="@id"/></xsl:attribute>
                </xsl:otherwise>
            </xsl:choose>
          <xsl:value-of select="name"/>
        </link>
      </xsl:for-each>
    </xsl:param>
    <!-- der einzelne nachweis wird erzeugt. es gelten folgende bedingungen:
    1) wenn eine ergänzung zum titel vorliegt, wird die entsprechende verknüpfung (junktor) eingefügt,
    2) wenn die ergänzung nicht mit einem punkt endet, wird ein punkt angefügt, außer bei der münchener reihe.
    -->
      <xsl:copy-of select="$p_shorttitle"/><xsl:if test="content[node()]"><xsl:value-of select="$p_junctor"/><xsl:text>&#x20;</xsl:text></xsl:if><xsl:apply-templates select="content"/><xsl:if test="not($p_end_of_addition='.') and not($sw_modus='projects_bay')">.</xsl:if>
  </xsl:template>


  <!-- Bilder -->
    <xsl:template name="images">
        <!-- container anlegen -->
        <images><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- die einzelnen bilder ansteuern -->
            <xsl:for-each select="sections/section[(@sectiontype='images') and ((number(@published) > 2) or (@published=''))]/items/item[number(@published) > 2]">
                <!-- für jedes bild ein <item> anlegen -->
                <item>
                    <xsl:copy-of select="@id"/>
                    <xsl:copy-of select="@sortno"/>
                    <xsl:copy-of select="file_name"/>
                    <xsl:copy-of select="file_path"/>
                    <xsl:copy-of select="file_type"/>
                </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </images>
    </xsl:template>


  <!-- buchstabenfußnoten -->
  <xsl:template name="letter_footnotes">
      <xsl:param name="sections" select="." />

      <!-- container anlegen -->
    <letter_footnotes><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- fußnotenzeichen in den texten aufsuchen -->
        <xsl:for-each select="$sections//app2">
            <xsl:variable name="v_app2" select="@id" />
            <!-- zur liste der fußnoten navigieren und die gemäß @id entsprechende fußnote ansteuern -->
            <xsl:for-each select="ancestor::article/footnotes/footnote[@from_tagid=$v_app2]">
                <!-- listeneinttrag (<item>) anlegen, mit attributen versehen und
                    fußnotentext mittels templates einfügen -->
                <item><xsl:copy-of select="@id"/>
                        <xsl:attribute name="itemID"><xsl:value-of select="$v_app2"/></xsl:attribute>
                        <xsl:attribute name="pkID"><xsl:value-of select="@from_tagid"/></xsl:attribute><!-- pk bedeutet place keeper = platzhalter -->
                        <xsl:apply-templates select="content" />
                </item>
            </xsl:for-each>
          <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
    </letter_footnotes>
  </xsl:template>

  <!-- ziffernfußnoten abschnittsweise ansteuern und an templates verweisen-->
  <xsl:template name="collect_digit_footnotes">
    <!-- container für ziffernfußnoten anlegen -->
    <digit_footnotes>
        <!-- die einzelnen abschnitte des artikels in der
            finalen reihenfolge ansteuern und an das
            tenmplate zum auslesen er fußnotenverweisen -->
    <!-- beschreibung -->
    <xsl:for-each select="sections/section[@norm_iri='di_description'][1]">
      <xsl:call-template name="digit_footnotes"/>
    </xsl:for-each>
        <!-- maßangaben allgemein -->
    <xsl:for-each select="sections/section[@sectiontype='measures']">
        <xsl:call-template name="digit_footnotes"/>
    </xsl:for-each>
      <!-- optional eingefügte abschnitte mit allgemeinen angaben -->
        <!-- inschrift nach -->
        <xsl:for-each select="sections/section[@norm_iri='di_generals_source']">
          <xsl:call-template name="digit_footnotes"/>
      </xsl:for-each>
        <!-- ergänzung nach -->
        <xsl:for-each select="sections/section[@norm_iri='di_generals_addition']">
          <xsl:call-template name="digit_footnotes"/>
      </xsl:for-each>
        <!-- maße -->
        <xsl:for-each select="sections/section[@norm_iri='di_generals_measure']">
          <xsl:call-template name="digit_footnotes"/>
      </xsl:for-each>
        <!-- buchstabenhöhen -->
        <xsl:for-each select="sections/section[@norm_iri='di_generals_fontsize']">
          <xsl:call-template name="digit_footnotes"/>
      </xsl:for-each>
        <!-- schriftarten -->
        <xsl:for-each select="sections/section[@norm_iri='di_generals_fonttype']">
          <xsl:call-template name="digit_footnotes"/>
      </xsl:for-each>

    <!-- inschriften und zwischentexte -->
    <xsl:for-each select=".//section[@sectiontype='inscriptiontext' or @sectiontype='text']">
      <xsl:choose>
        <!-- wenn es sich nicht um die Haupt-Beschreibung vor den Inschriften,
          sondern um einen Zwischen-Beschreibung handelt (die hauptbeschreibung wurde bereits angesteuert) -->
          <xsl:when test="@norm_iri='di_description'">
          <xsl:choose>
              <xsl:when test="preceding-sibling::section[@norm_iri='di_description']">
                <xsl:call-template name="digit_footnotes"/>
            </xsl:when>
            <xsl:otherwise></xsl:otherwise>
          </xsl:choose>
        </xsl:when>
        <!-- wenn es sich nicht um den hauptkommentar nach den inschriften,
          sondern um einen zwischenkommentar handelt (der hautpkommentar wurde bereits angesteuert) -->
        <xsl:when test="@kategorie='Kommentar'">
          <xsl:choose>
              <xsl:when test="following-sibling::section[@norm_iri='di_comment']">
                <xsl:call-template name="digit_footnotes"/>
            </xsl:when>
            <xsl:otherwise></xsl:otherwise>
          </xsl:choose>
        </xsl:when>

        <!-- allgemeine angaben ... und versmaße werden an anderer stelle ausgelesen -->
          <xsl:when test="starts-with(@norm_iri, 'di_generals')"><!-- entfällt hier --></xsl:when>
          <xsl:when test="@norm_iri='di_metres'"><!-- entfällt hier --></xsl:when>
        <xsl:otherwise>
          <!-- in den inschriften nur die transkriptionen ansteuern,
              die übersetzungen werden anschließend separat erfasst -->
            <xsl:for-each select="items/item/content">
                <xsl:call-template name="digit_footnotes"/>
          </xsl:for-each>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:for-each>

      <!-- übersetzungen-->
      <xsl:for-each select=".//section[@sectiontype='inscriptiontext']/items/item/translation">
          <xsl:call-template name="digit_footnotes"/>
      </xsl:for-each>

    <!-- versformen -->
    <xsl:for-each select="sections/section[@norm_iri='di_metres']">
        <xsl:call-template name="digit_footnotes"/>
    </xsl:for-each>

      <!-- wappen -->
    <xsl:for-each select="sections/section[@sectiontype='heraldry']">
        <xsl:call-template name="digit_footnotes_heraldry"/>
    </xsl:for-each>
      <!-- kommentar -->
        <xsl:for-each select="sections/section[@kategorie='Kommentar' or @norm_iri='di_comment'][last()]">
        <xsl:call-template name="digit_footnotes"/>
    </xsl:for-each>
    </digit_footnotes>
  </xsl:template>

  <!-- ziffernfußnoten in den abschnitten aufsuchen und formatieren -->
  <xsl:template name="digit_footnotes">
    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <xsl:for-each select=".//*[self::app1 or self::loc_ten]">
          <!-- loc_ten ist ein platzhalter für einen verweise auf eine objektnummer
                auf einer grundrisszeichnung; wird hier in eine fußnote umgewidmet
          -->
            <xsl:variable name="v_app1_id" select="@id" />
          
            <xsl:choose>
                <!-- fußnoten <app1> -->
              <xsl:when test="self::app1">
                <xsl:for-each select="ancestor::article/footnotes/footnote[@from_tagid=$v_app1_id]">
                    <!-- listeneintrag anlegen -->
                    <item><xsl:copy-of select="@id"/>
                        <xsl:attribute name="itemID"><xsl:value-of select="$v_app1_id"/></xsl:attribute>
                        <!-- pk bedeutet place keeper = platzhalter -->
                        <xsl:attribute name="pkID"><xsl:value-of select="@from_tagid"/></xsl:attribute>
                        <xsl:apply-templates select="content" />
                  </item>
                </xsl:for-each>
              </xsl:when>
                <!-- platzhalter <loc_ten> -->
              <xsl:otherwise>
                  <!-- fußnoten-listeneintrag anlegen (aus <loc_ten> wird <item>) -->
                <item><xsl:copy-of select="@id"/>
                    <!-- da das item erst erzeugt wird, muss auch die ID generiert werden, hier aus der ID des platzhalters -->
                    <xsl:attribute name="itemID">item-<xsl:value-of select="@id"/></xsl:attribute>
                    <!-- pk bedeutet place keeper = platzhalter -->
                    <xsl:attribute name="pkID"><xsl:value-of select="@id"/></xsl:attribute>
                    <!-- absatz anlegen und template aufrufen -->
                    <p><xsl:call-template name="loc_ten"></xsl:call-template></p>
                </item>
                </xsl:otherwise>
            </xsl:choose>
       <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </xsl:for-each>
  </xsl:template>

  <!-- ziffernfu0noten im abschnitt wappen -->
    <xsl:template name="digit_footnotes_heraldry">
    <xsl:for-each select="items/item">
      <xsl:sort select="pos_y" data-type="number" order="ascending"/>
      <xsl:sort select="pos_x" data-type="number" order="ascending"/>
      <xsl:sort select="pos_z" data-type="number" order="ascending"/>
      <xsl:choose>
        <!--variante 1: fußnote aus dem anmerkungsfeld nehmen-->
        <xsl:when test="content">
        <item>
            <xsl:attribute name="id"> <xsl:value-of select="@id"/></xsl:attribute>
            <xsl:attribute name="itemID"><xsl:value-of select="@id"/></xsl:attribute>
            <xsl:attribute name="pkID"><xsl:value-of select="@id"/></xsl:attribute>
            <p><xsl:apply-templates select="content"></xsl:apply-templates></p>
        </item>
        </xsl:when>
        <!-- variante 2: fußnote aus der wappenliste -->
        <xsl:otherwise>
            <xsl:choose>
                <xsl:when test="starts-with(property/name , '?') and (property/content[node()] or property/elements[node()])">
                <item>
                    <xsl:attribute name="id"> <xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="itemID"><xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="pkID"><xsl:value-of select="@id"/></xsl:attribute>
                    <p>
                        <xsl:for-each select="property/content/node()">
                            <xsl:choose>
                                <xsl:when test="position() = 1 and self::text()">
                                    <xsl:value-of select="concat(
                                        upper-case(substring(., 1, 1)),
                                        substring(., 2)
                                        )"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:apply-templates select="."/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:for-each>

                        <xsl:if test="property/elements[node()]">, </xsl:if>
                        <xsl:apply-templates select="property/elements" />
                        <xsl:text>.</xsl:text>
                    </p>
                </item>
                </xsl:when>
                <!-- münchner reihe -->
                <xsl:when test="contains(property/name , 'unbekannt') and (property/content[node()] or property/elements[node()])">
                <item>
                    <xsl:attribute name="id"> <xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="itemID"><xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="pkID"><xsl:value-of select="@id"/></xsl:attribute>
                    <p>Wappen unbekannt (<xsl:apply-templates select="property/content"></xsl:apply-templates><xsl:if test="property/elements[node()]">, </xsl:if><xsl:apply-templates select="property/elements"></xsl:apply-templates><xsl:text>).</xsl:text></p>
                </item>
                </xsl:when>
            </xsl:choose>
        </xsl:otherwise>
        </xsl:choose>
    </xsl:for-each>
  </xsl:template>

  <!-- abschnitt mit den tabellarisch angeordneten wappen-angaben  -->
  <xsl:template name="heraldry">
      <!-- tabelle in parametern vorbereiten -->
      
    <!-- Get maximum x -->
    <xsl:param name="p_columns1">
        <xsl:for-each select="sections/section[@sectiontype='heraldry']/items/item/pos_x">
            <xsl:sort select="." data-type="number"/>
            <xsl:copy-of select="."/>
        </xsl:for-each>
    </xsl:param>
      
    <!-- Get maximum y -->
    <xsl:param name="p_rows1">
        <xsl:for-each select="sections/section[@sectiontype='heraldry']/items/item/pos_y">
            <xsl:sort select="." data-type="number"/>
            <xsl:copy-of select="."/>
        </xsl:for-each>
    </xsl:param>   

      <!-- auf das vorkommen von wappen-angaben testen-->
    <xsl:if test="sections/section[@sectiontype='heraldry']/items/item">
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- container für die wappentabelle anlegen -->
      <heraldry><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!--
        <parametertest_wappentabelle>x:<xsl:value-of select="$p_columns3"/>; y:<xsl:value-of select="$p_rows3"/></parametertest_wappentabelle>
        -->

        <!-- überschrift -->
        <title>Wappen:</title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <!-- tabelle anlegen -->
        <table><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <xsl:call-template name="looprows">
            <xsl:with-param name="p_rownumber" select="1" />
              <xsl:with-param name="p_max_rows" select="number($p_rows1/pos_y[last()])"/>
              <xsl:with-param name="p_max_columns" select="number($p_columns1/pos_x[last()])"/>
          </xsl:call-template>
        </table><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </heraldry><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:if>
  </xsl:template>

  <!-- zeilenschleife -->
  <xsl:template name="looprows">
    <xsl:param name="p_rownumber" />
    <xsl:param name="p_max_rows" />
    <xsl:param name="p_max_columns" />
    <xsl:if test="number($p_rownumber) &lt;= number($p_max_rows)" >
      <row lfnr="{$p_rownumber}" max_zeilen="{$p_max_rows}" max_spalten="{$p_max_columns}">
        <xsl:call-template name="loopcolumns">
          <xsl:with-param name="p_columnnumber" select="1" />
          <xsl:with-param name="p_max_columns" select="number($p_max_columns)" />
            <xsl:with-param name="p_rownumber" select="number($p_rownumber)" />
            <xsl:with-param name="p_max_rows" select="number($p_max_rows)"/>
        </xsl:call-template>
      </row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- gegebenefalls weiter tabellenzeilen einfügen -->
      <xsl:call-template name="looprows">
        <xsl:with-param name="p_rownumber" select="$p_rownumber + 1" />
        <xsl:with-param name="p_max_rows" select="$p_max_rows" />
        <xsl:with-param name="p_max_columns" select="$p_max_columns" />
      </xsl:call-template>
    </xsl:if>
  </xsl:template>

  <!-- spalten-schleife -->
  <xsl:template name="loopcolumns">
    <xsl:param name="p_columnnumber" />
    <xsl:param name="p_max_rows" />
    <xsl:param name="p_max_columns" />
    <xsl:param name="p_rownumber" />

    <xsl:if test="number($p_columnnumber) &lt;= number($p_max_columns)">
      
      <!-- tabellenzelle anlegen -->
      <cell rownumber="{$p_columnnumber}" max_max_rows="{$p_max_columns}">
          <xsl:for-each-group
              select="sections/section[@sectiontype='heraldry']/items/item[pos_x=$p_columnnumber and pos_y=$p_rownumber]"
              group-by="if (normalize-space(@itemgroup)) then normalize-space(@itemgroup) else generate-id()"
          >
              
            <!-- zelleneintrag -->
          <entry>
              <!-- iterate over all items in this group -->
              <xsl:for-each select="current-group()">
                  <xsl:variable name="v_coa-id" select="property/@id" />
                  
                  <coa>
                      <xsl:attribute name="target_id" select="$v_coa-id" />
                      <xsl:if test="normalize-space(@itemgroup)">
                          <xsl:attribute name="itemgroup" select="@itemgroup" />
                      </xsl:if>
                      
                     <xsl:choose>
                       <!-- Grouped items (Allianzwappen) -->
                       <xsl:when test="position() &gt; 1"></xsl:when>

                       <!-- Alias -->
                       <xsl:when test="value/text()">
                           <xsl:value-of select="value" />
                       </xsl:when>

                       <!-- Alias -->
                       <xsl:when test="@itemgroup">
                           <xsl:value-of select="@itemgroup" />
                       </xsl:when>
                       
                       <!-- Unbekannt -->
                       <xsl:when test="starts-with(property/name, '?') or contains(property/name, 'unbekannt')">
                           <xsl:text>unbekannt</xsl:text>
                       </xsl:when>
    
                       <!-- Name mit Platzhalternummern -->
                       <xsl:when test="contains(property/name,'{')">
                         <!-- zun wappenregister navigieren -->
                         <!-- die lemmata werden wegen möglicher umbenennungen
                              (im zuge der anpassung an den bestand des projekts)
                              aus dem wappenregister genommen-->
                          <xsl:for-each select="ancestor::book/indices/index[@propertytype='heraldry' or @type='heraldry']/item[@id=$v_coa-id]">
                              <xsl:value-of select="lemma"/>
                          </xsl:for-each>
                      </xsl:when>
                         
                      <!-- Name -->
                      <xsl:otherwise>
                          <xsl:value-of select="property/name"/>
                      </xsl:otherwise>
                  </xsl:choose>
                </coa>
              </xsl:for-each>
              <!-- fussnote anfügen -->
              <xsl:choose>
                  <xsl:when test="content">
                      <app1><xsl:attribute name="id" select="@id" /></app1>
                  </xsl:when>
                  <xsl:when test="(starts-with(property/name , '?') or contains(property/name , 'unbekannt')) and (property/content[node()] or property/elements[node()])">
                      <app1><xsl:attribute name="id" select="@id" /></app1>
                  </xsl:when>
                  <xsl:otherwise />
              </xsl:choose>
          </entry>
        </xsl:for-each-group>
      </cell>
        
        <!-- gegebenenfalls weitere tabellen-zellen einfügen -->
      <xsl:call-template name="loopcolumns">
        <xsl:with-param name="p_columnnumber" select="number($p_columnnumber) + 1" />
        <xsl:with-param name="p_max_rows" select="number($p_max_rows)"/>
        <xsl:with-param name="p_max_columns" select="number($p_max_columns)" />
        <xsl:with-param name="p_rownumber" select="number($p_rownumber)" />
      </xsl:call-template>
    </xsl:if>
  </xsl:template>

  <!-- abschnitt für versangaben -->
    <xsl:template name="verses">
        <xsl:param name="sections" select="./sections/section" />
        <xsl:param name="p_number_of_inscriptions" />

        <!-- die versangaben in einem parameter sammeln und vorformatieren -->
        <xsl:param name="p_verses-collect">

        <!-- versangaben in den <items> aufsuchen (wenn vorhanden)-->
        <xsl:if test="$sections[@sectiontype='inscription']/items/item[@itemtype='metres']">
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="$sections[@sectiontype='inscription'][items/item[@itemtype='metres']]">
                <xsl:variable name="v_inschriftID"><xsl:value-of select="@id"/></xsl:variable>
                <xsl:variable name="v_sectionnumber"><xsl:value-of select="@number"/></xsl:variable>

                <xsl:for-each select="items/item[@itemtype='metres']">
                   <item>
                     <xsl:copy-of select="@id"/>
                     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

                       <!-- Nummern:
                           a) Kommen aus den Links direkt im Artikel
                           b) Oder es wird die Inschriftennummer übernommen
                       -->
                       <xsl:if test="number($p_number_of_inscriptions) != 1">
                         <xsl:choose>
                           <xsl:when test="content/rec_intern">
                             <xsl:for-each select="content/rec_intern">
                               <nr target_id="{$v_inschriftID}" sectionnumber="{$v_sectionnumber}">
                                   <xsl:apply-templates select="." />
                               </nr>
                             </xsl:for-each>
                           </xsl:when>
                           <xsl:otherwise>
                             <nr target_id="{$v_inschriftID}" sectionnumber="{$v_sectionnumber}">
                                 <xsl:copy-of select="ancestor::section[@sectiontype='inscription']/@number"/>
                                 <xsl:for-each select="ancestor::section[@sectiontype='inscription']">
                                     <xsl:choose>
                                         <xsl:when test="$sw_modus='projects_bay'">
                                             <xsl:choose>
                                                 <xsl:when test="@number[string()]">
                                                     <xsl:number value="@number" format="I"/>
                                                 </xsl:when>
                                                 <xsl:otherwise>NaN</xsl:otherwise>
                                             </xsl:choose>
                                         </xsl:when>
                                         <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                                     </xsl:choose>
                                </xsl:for-each>
                               </nr>
                           </xsl:otherwise>
                         </xsl:choose>
                       <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                     </xsl:if>

                     <!-- Inhalte bzw. Lemma
                         a) Aus dem Ergänzungsfeld, wobei Ausdrücke in Klammern entfernt werden,
                            um ggf. zusätzliche Links zu entfernen.
                         b) Aus dem Namen (=Langeform) der Property
                     -->
                     <content>
                        <xsl:choose>
                          <xsl:when test="content[text()]">
                              <xsl:value-of select="normalize-space(epi:remove-brackets(content))"/>
                          </xsl:when>
                          <xsl:otherwise>
                              <xsl:value-of select="normalize-space(property/name)"/>
                          </xsl:otherwise>
                        </xsl:choose>
                     </content>
                     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                   </item>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </xsl:for-each>
        </xsl:if>
    </xsl:param>

    <!-- parameter anzeigen -->
    <!--
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <param_verse_sammeln><xsl:copy-of select="$p_verses-collect"/></param_verse_sammeln>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <param_number_of_inscriptions><xsl:copy-of select="$p_number_of_inscriptions"/></param_number_of_inscriptions>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    -->

    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <di_metres><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <!-- die verschiedenen versformen separieren und referenzen auf inschriften zusammenführen -->
      <xsl:for-each select="$p_verses-collect/item[not(content=preceding-sibling::item/content)]">
        <item>
          <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <xsl:copy-of select="content" copy-namespaces="no"/>
            <links>
              <xsl:for-each select="nr">
              <link  type="inscription"><xsl:copy-of select="@*"/>
                  <xsl:value-of select="."/>
              </link>
              </xsl:for-each>
              <xsl:for-each select="following-sibling::item[content=current()/content]">
                <xsl:for-each select="nr">
                   <link type="inscription"><xsl:copy-of select="@*"/><xsl:value-of select="."/></link>
                </xsl:for-each>
              </xsl:for-each>
            </links>
        </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </xsl:for-each>
    </di_metres>
  </xsl:template>
  <!-- ende: verse -->

  <!-- im feld ergänzung zur angabe der vermaße werden zusätze in klammern [außer (?)] entfernt  -->
    <xsl:template match="item[@itemtype='metres']/content">
    <xsl:param name="p_versline"><xsl:apply-templates/></xsl:param>
      <xsl:choose>
        <xsl:when test="contains($p_versline, ' (') and not(contains($p_versline, ' (?)'))">
          <xsl:value-of select="substring-before($p_versline,' (')"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="$p_versline"/>
        </xsl:otherwise>
      </xsl:choose>
  </xsl:template>

<!-- unterdrückte <section> (werden auf andere weise, durch benannte templates, ausgelesen -->
    <xsl:template match="section[@sectiontype='text'][@norm_iri='di_metres']"></xsl:template>
    <xsl:template match="section[@norm_iri='di_headline_date']"></xsl:template>
    <xsl:template match="section[@norm_iri='di_headline_site']"></xsl:template>

    <!-- Gliederung: Ebene 1 -->
    <xsl:template match="section[@sectiontype='text'][@norm_iri='di_header1']">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <di_header1><xsl:apply-templates select=".//content"/></di_header1>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

    <!-- Gliederung: Ebene 2 -->
    <xsl:template match="section[@sectiontype='text'][@norm_iri='di_header2']">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <di_header2><xsl:apply-templates select=".//content"/></di_header2>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

    <!-- Gliederung: Ebene 3 -->
    <xsl:template match="section[@sectiontype='text'][@norm_iri='di_header3']">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <di_header3><xsl:apply-templates select=".//content"/></di_header3>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

    <!-- münchener reihe: inschriften anders nummerieren -->
    <xsl:template match="section[@sectiontype='text'][@norm_irie='di_inscription_numbering']">
        <!-- bislang nicht vorhanden -->
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <di_inscription_numbering><xsl:apply-templates select=".//content"/></di_inscription_numbering>
        <xsl:for-each select="section">
            <xsl:apply-templates select="."></xsl:apply-templates>
        </xsl:for-each>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

    <!-- kommentare -->
    <xsl:template match="section[@sectiontype='text'][@norm_iri='di_comment']">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:if test="preceding-sibling::section[@norm_iri='di_comment']"></xsl:if>
        <di_comment log="k2"><xsl:apply-templates select="items/item[@itemtype='text']/content"></xsl:apply-templates></di_comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

    <!-- beschreibungen -->
    <xsl:template match="section[@sectiontype='text'][@norm_iri='di_description']">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <di_description><xsl:apply-templates select="items/item[@itemtype='text']/content"></xsl:apply-templates></di_description>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>


    <!-- zu unterdrückende elemente  -->
    <xsl:template match="section[starts-with(@kategorie, 'Datum')]"></xsl:template>
    <xsl:template match="section[@kategorie='Zitatquellen']"></xsl:template>
    <xsl:template match="caption"></xsl:template>
    
</xsl:stylesheet>
