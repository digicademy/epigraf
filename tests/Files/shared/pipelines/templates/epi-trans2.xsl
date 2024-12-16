<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
        xmlns:php="http://php.net/xsl"
>

<!-- als muster für das zusammenziehen der referenzen auf inschriften siehe das template nrn-verdichten, eingebunden im template verse  -->

<xsl:import href="trans2/epi-inschrift-nach.xsl"/>
<xsl:import href="trans2/epi-ergaenzung-nach.xsl"/>
<xsl:import href="trans2/epi-angaben-schriftarten.xsl"/>
<xsl:import href="trans2/epi-angaben-schrifthoehen.xsl"/>
<xsl:import href="trans2/epi-angaben-abmessungen.xsl"/>
<xsl:import href="trans2/epi-inschriften.xsl"/>
  <xsl:import href="trans2/epi-register-trans2.xsl"/>
  <xsl:import href="trans2/epi-loc-ten.xsl"/>
  <xsl:import href="trans2/epi-marken.xsl"/>
  <xsl:import href="trans2/epi-datierung.xsl"/>
  <xsl:import href="commons/epi-switch.xsl"/>

<xsl:param name="p_db"><xsl:value-of select="book/project/database"/></xsl:param>
    <!-- Sprachenkürzel nach ISO 639-1; siehe https://wiki.selfhtml.org/wiki/Sprachk%C3%BCrzel -->
<xsl:param name="p_projectlanguage">
  <xsl:choose>
    <xsl:when test="$p_db='inscriptiones_estoniae'">et</xsl:when>
    <xsl:otherwise>de</xsl:otherwise>
  </xsl:choose>
</xsl:param>
<xsl:param name="p_basesite"><xsl:value-of select="book/project/name"/></xsl:param>


  <xsl:template match="/">
    <xsl:apply-templates/>
  </xsl:template>

  <xsl:template match="book">
    <book>
      <xsl:copy-of select="@*"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:copy-of select="options"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

      <!-- projektangaben -->
      <xsl:copy-of select="project"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!--titelei-->
      <xsl:copy-of select="titelei"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!--inhaltsverzeichnis  -->
      <xsl:copy-of select="inhalt"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>


      <!--vorwort-->
      <xsl:apply-templates select="vorwort"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>


      <!--einleitung-->
      <xsl:apply-templates select="einleitung"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- katalog -->
      <catalog>
        <xsl:copy-of select="catalog/@*"/>

        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="catalog/titel">
          <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
        <xsl:for-each select="catalog/article">
          <xsl:apply-templates select=".">
            <xsl:with-param name="p_projectlanguage"><xsl:value-of select="$p_projectlanguage"/></xsl:with-param>
          </xsl:apply-templates>
          <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
      </catalog><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!--inschriftenliste-->
      <xsl:copy-of select="inschriftenliste"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

           <!-- abkürzungen -->
      <xsl:apply-templates select="abkuerzungen"></xsl:apply-templates>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- quellen und literatur -->
      <xsl:copy-of select="quellen_literatur"/>

      <!-- di-liste -->
      <xsl:copy-of select="di_baende"/>

        <!-- register -->
      <xsl:apply-templates select="indices"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>


      <!-- meisterzeichen und hausmarken-->
     <!--  das template im externen stylesheet marken.xsl-->
      <xsl:apply-templates select="marken"></xsl:apply-templates>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- zeichnungen -->
      <xsl:apply-templates select="zeichnungen"></xsl:apply-templates>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- bildtafeln-->
      <xsl:apply-templates select="bildtafeln"></xsl:apply-templates>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

    </book>
  </xsl:template>

  <xsl:template match="bildtafeln">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:copy-of select="titel"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:apply-templates select="bildnachweis"></xsl:apply-templates>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:copy>
  </xsl:template>

  <xsl:template match="bildnachweis">
    <xsl:copy>
      <xsl:copy-of select="@*"/><xsl:copy-of select="@*"/>
      <xsl:for-each select="*[not(text()='§')]">
        <xsl:copy-of select="."/>
      </xsl:for-each>
    </xsl:copy>
  </xsl:template>

  <xsl:template match="zeichnungen">
    <zeichnungen log2="zei1">
      <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:copy-of select="titel"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:apply-templates select="marken"></xsl:apply-templates>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <xsl:if test="maps">
        <xsl:for-each select="maps">
                 <maps>
                    <xsl:copy-of select="@*"/>
                    <xsl:copy-of select="titel"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:choose>
    <xsl:when test="map">
        <xsl:for-each select="map">
                        <map>
                            <xsl:copy-of select="@*"/>
                            <xsl:apply-templates select="."></xsl:apply-templates>
                        </map>
                        </xsl:for-each>
</xsl:when>
<xsl:otherwise>
                    <xsl:apply-templates></xsl:apply-templates>
</xsl:otherwise>
</xsl:choose>

                 </maps>
        </xsl:for-each>
        </xsl:if>

         <xsl:if test="map">
            <xsl:for-each select="map">
              <map>
                    <xsl:copy-of select="@*"/>
                    <xsl:apply-templates></xsl:apply-templates>
                </map></xsl:for-each>
        </xsl:if>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </zeichnungen>
  </xsl:template>

<!-- vorbemerkungen zu den grundrissen -->
<xsl:template match="map/content"><note><xsl:apply-templates></xsl:apply-templates></note></xsl:template>

<!-- konkordanz der grabplattennummern auf dem grundriss mit den artikelnummern -->
<xsl:template match="konkordanz">
    <!-- textbaustein zur signatur in einen parameter speichern -->
    <xsl:param name="p_textbaustein"><xsl:value-of select="content/p/rec_lit/@data-link-value"/></xsl:param>
    <concordance><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <!-- jeden artikel ansteuern, der eine signatur mit dem textbaustein aufweist -->
            <xsl:for-each select="ancestor::book/catalog/article[sections/section[@sectiontype='signatures']/items/item[@itemtype='signatures']/property/name=$p_textbaustein]">
                <!-- artikelnummer in einer variablen speichern -->
                <xsl:variable name="p_article_number"><xsl:value-of select="@nr"/></xsl:variable>
                <!-- artikel-id in einer variablen speichern -->
                <xsl:variable name="p_article_id"><xsl:value-of select="substring-after(@id,'-')"/></xsl:variable>
                <!-- jedes signatur-item  mit dem betreffenden textbaustein ansteuern -->
                <xsl:for-each select="sections/section[@sectiontype='signatures']/items/item[@itemtype='signatures'][property/name=$p_textbaustein]">
                <!-- nummer auf dem grundriss ohne führende nullen in einer variablen speichern -->
                <xsl:variable name="p_object_number"><xsl:number value="value" format="1"/></xsl:variable>


                <!-- tabellenzeile anlegen-->
                    <row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                           <!-- nummer auf dem grundriss -->
                          <map_object_number><xsl:value-of select="$p_object_number"/></map_object_number><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <!--artikelnummer-->
                          <article_number article-id="{$p_article_id}">Kat.-Nr. <xsl:value-of select="$p_article_number"/></article_number><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:for-each>
    </xsl:for-each>
    </concordance><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

<xsl:template match="bild"><map_path><xsl:value-of select="content/p"/></map_path></xsl:template>
<xsl:template match="bildlegende"><map_title><xsl:value-of select="content/p"/></map_title></xsl:template>


  <xsl:template match="article[@articletype='epi-article']">
    <xsl:param name="p_projectlanguage"></xsl:param>
    <xsl:param name="anzahl_inschriften"><xsl:value-of select="count(.//section[@sectiontype='inscription'][not(parent::link)])"/></xsl:param>
    <xsl:param name="anzahl_inschriftenteile"><xsl:value-of select="count(.//section[@sectiontype='inscriptionpart'])"/></xsl:param>
    <xsl:param name="anzahl_bearbeitungen"><xsl:value-of select="count(.//section[@sectiontype='inscriptiontext'])"/></xsl:param>
    <xsl:param name="anzahl_uebersetzungen"><xsl:value-of select="count(.//translation[node()])"/></xsl:param>
    <xsl:param name="anzahl_kommentare"><xsl:value-of select="count(sections/section[@kategorie='Kommentar' or @name='Kommentar'])"/></xsl:param>

    <article>
      <xsl:attribute name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:attribute>
      <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

<!--
<test-anzahl-kommentare><xsl:copy-of select="$anzahl_kommentare"/></test-anzahl-kommentare>
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->
      <optionen><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="sections/section[@sectiontype='outputoptions']/items/item/property">
          <option>
            <xsl:attribute name="lemma"><xsl:value-of select="lemma"/></xsl:attribute>
            <xsl:attribute name="name"><xsl:value-of select="name"/></xsl:attribute>
            <xsl:attribute name="iri"><xsl:value-of select="norm_iri"/></xsl:attribute>
          </option><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
       </optionen><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <kopfzeile><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <titel><xsl:value-of  select="name"/></titel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <created><xsl:value-of select="created"/></created><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <modified><xsl:value-of select="modified"/></modified><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <creator><xsl:value-of select="creator/name"/></creator><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
         <modifier><xsl:value-of select="modifier/name"/></modifier><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

       <!-- signatur -->
      <signatur><xsl:value-of select="signature"/></signatur><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- datierungsindex -->
     <datierung_index> <xsl:value-of select="sections/section[@sectiontype='conditions']/items/item/date_sort"/></datierung_index>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- artikelnummer -->
      <artikelnummer><xsl:number count="article" from="catalog" format="1"/></artikelnummer><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- überlieferungform -->
      <trad_index><xsl:value-of select="sections/section[@sectiontype='conditions']/items/item/property/lemma"/></trad_index><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <trad_sigle>
        <xsl:for-each select="sections/section[@sectiontype='conditions']/items/item/property">
          <xsl:choose>
            <xsl:when test="lemma[text()='traditio1']"></xsl:when>
            <xsl:when test="lemma[text()='traditio2']"><xsl:text>&#x2020;</xsl:text></xsl:when>
            <xsl:when test="lemma[text()='traditio3']"><xsl:text>(&#x2020;)</xsl:text></xsl:when>
            <xsl:when test="lemma[text()='traditio4']"><xsl:text>(&#x2020;)</xsl:text></xsl:when>
            <xsl:when test="lemma[text()='traditio5']"><xsl:text>(&#x2020;)</xsl:text></xsl:when>
            <xsl:when test="lemma[text()='traditio6']"><xsl:text>&#x2020;?</xsl:text></xsl:when>
            <xsl:otherwise></xsl:otherwise>
          </xsl:choose>
        </xsl:for-each>
      </trad_sigle><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- standort -->
        <xsl:choose>
            <xsl:when test="sections/section[@kategorie='Kopfzeile: Standorte']">
                <standorte><standort><xsl:value-of select="sections/section[@kategorie='Kopfzeile: Standorte']/items/item/content"/></standort></standorte>
            </xsl:when>
            <xsl:otherwise><xsl:call-template name="standorte"/></xsl:otherwise>
        </xsl:choose>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- datierung -->
    <xsl:choose>
        <xsl:when test="sections/section[@kategorie='Kopfzeile: Datierung']">
            <datierung><xsl:value-of select="sections/section[@kategorie='Kopfzeile: Datierung']/items/item/content"/></datierung>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:when>
        <xsl:otherwise>
              <xsl:call-template name="datierung">
                <xsl:with-param name="p_datierung_index"><xsl:value-of select="@datierung_index"/></xsl:with-param>
              </xsl:call-template>
              <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:otherwise>
    </xsl:choose>
      </kopfzeile><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- beschreibung -->
      <xsl:call-template name="beschreibung"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- objekt-angaben -->
      <xsl:call-template name="angaben">
        <xsl:with-param name="p_projectlanguage"><xsl:value-of select="$p_projectlanguage"/></xsl:with-param>
        <xsl:with-param name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:with-param>
      </xsl:call-template>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- inschriften und zwischentexte -->
     <xsl:for-each select="sections/section[@sectiontype='text' or @sectiontype='inscription']">
        <xsl:choose>
          <xsl:when test="contains(@kategorie,'Allgemeine Angaben')"><!-- schon weiter oben behandelt --></xsl:when>
          <xsl:when test="@kategorie='Kommentar' and $anzahl_kommentare=1"><!-- schon weiter oben behandelt --></xsl:when>
          <xsl:when test="contains(@kategorie,'Beschreibung')">
            <xsl:choose>
              <xsl:when test="preceding-sibling::section[contains(@kategorie,'Beschreibung')]">
                <xsl:apply-templates select=".">
                  <xsl:with-param name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:with-param>
                  <xsl:with-param name="anzahl_inschriftenteile"><xsl:value-of select="$anzahl_inschriftenteile"/></xsl:with-param>
                  <xsl:with-param name="anzahl_bearbeitungen"><xsl:value-of select="$anzahl_bearbeitungen"/></xsl:with-param>
                  <xsl:with-param name="anzahl_uebersetzungen"><xsl:value-of select="$anzahl_uebersetzungen"/></xsl:with-param>
                </xsl:apply-templates>
              </xsl:when>
              <xsl:otherwise><!-- die erste bechreibung wurde schon eingefügt --></xsl:otherwise>
            </xsl:choose>
          </xsl:when>
          <xsl:when test="@kategorie='Transkriptionsspalten'">
            <transkriptionsspalten>
              <xsl:copy-of select="@*"/>
              <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <!-- hier das muster für transkription mehrerer inschriften in spalten nebeneinander aufrufen -->
              <xsl:for-each select="section">
                <xsl:apply-templates select=".">
                  <xsl:with-param name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:with-param>
                  <xsl:with-param name="anzahl_inschriftenteile"><xsl:value-of select="$anzahl_inschriftenteile"/></xsl:with-param>
                  <xsl:with-param name="anzahl_bearbeitungen"><xsl:value-of select="$anzahl_bearbeitungen"/></xsl:with-param>
                  <xsl:with-param name="anzahl_uebersetzungen"><xsl:value-of select="$anzahl_uebersetzungen"/></xsl:with-param>
                </xsl:apply-templates>
              </xsl:for-each>
            </transkriptionsspalten><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          </xsl:when>
          <xsl:otherwise>
            <xsl:apply-templates select=".">
              <xsl:with-param name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:with-param>
              <xsl:with-param name="anzahl_inschriftenteile"><xsl:value-of select="$anzahl_inschriftenteile"/></xsl:with-param>
              <xsl:with-param name="anzahl_bearbeitungen"><xsl:value-of select="$anzahl_bearbeitungen"/></xsl:with-param>
              <xsl:with-param name="anzahl_uebersetzungen"><xsl:value-of select="$anzahl_uebersetzungen"/></xsl:with-param>
            </xsl:apply-templates>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:for-each>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

        <!-- buchstabenapparat münchener reihe (projects_bay) -->
        <xsl:if test="$modus='projects_bay'">
            <apparate><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:call-template name="buchstabenapparat"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </apparate><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:if>

      <xsl:if test="sections/section[@kategorie='Zitatquellen']">
        <xsl:for-each select="sections/section[@kategorie='Zitatquellen']">
          <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <section><xsl:copy-of select="@*"/><xsl:apply-templates select="items/item/content"></xsl:apply-templates></section>
          <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
      </xsl:if>

      <!-- übersetzungen -->
      <xsl:call-template name="uebersetzungen">
        <xsl:with-param name="p_db"><xsl:value-of select="$p_db"/></xsl:with-param>
        <xsl:with-param name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:with-param>
        <xsl:with-param name="anzahl_inschriftenteile"><xsl:value-of select="$anzahl_inschriftenteile"/></xsl:with-param>
        <xsl:with-param name="anzahl_bearbeitungen"><xsl:value-of select="$anzahl_bearbeitungen"/></xsl:with-param>
        <xsl:with-param name="anzahl_uebersetzungen"><xsl:value-of select="$anzahl_uebersetzungen"/></xsl:with-param>
      </xsl:call-template>

      <!-- verse -->
      <xsl:choose>
       <!-- wenn ein zwischentext für die angabe der Versmaße angelegt wurde, diesen ansteuern und wie eine textfeld (beschreibung) verarbeiten -->
        <xsl:when test="sections/section[@kategorie='Versmaße']">
          <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!--          <verse><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="section[@caption='Versmaße']">
              <item><content><xsl:apply-templates select="fields/beschreibung"/></content></item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
          </verse>-->
          <beschreibung mode="verse">
            <xsl:for-each select="sections/section[@name='Versmaße']">
             <xsl:apply-templates select="items/item/content"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
          </beschreibung>
          <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:when>
        <!-- anderenfalls die angaben aus dem register zusammentragen -->
        <xsl:otherwise>
      <xsl:call-template name="verse"><xsl:with-param name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:with-param></xsl:call-template>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:otherwise>
      </xsl:choose>

      <xsl:if test="sections/section[starts-with(@kategorie, 'Datum')]">
        <xsl:for-each select="sections/section[starts-with(@kategorie, 'Datum')]">
          <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <section><xsl:copy-of select="@*"/><xsl:apply-templates select="items/item/content"></xsl:apply-templates></section>
          <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
      </xsl:if>

      <!-- wappen -->
      <xsl:call-template name="wappen"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!--      <xsl:choose>
          <xsl:when test="section[@caption='Wappen']">
            <heraldik><xsl:apply-templates select="section[@caption='Wappen']/fields/beschreibung"/></heraldik>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          </xsl:when>
        <xsl:otherwise>
           <xsl:call-template name="wappen"/>
           <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:otherwise>
      </xsl:choose>-->



      <!-- kommentar -->
      <xsl:call-template name="kommentar"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- apparate -->
        <apparate><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:if test="not($modus='projects_bay')">
                <xsl:call-template name="buchstabenapparat"></xsl:call-template>
                </xsl:if>
              <xsl:call-template name="ziffernapparate"/>
              <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </apparate><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- nachweise -->
      <xsl:call-template name="nachweise"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- notizen -->
      <xsl:call-template name="notizen"></xsl:call-template>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

    </article>
  </xsl:template>

  <xsl:template match="transkriptionsspalten">
    <xsl:param name="anzahl_bearbeitungen"></xsl:param>
    <xsl:param name="anzahl_inschriften"></xsl:param>
    <xsl:param name="anzahl_inschriftenteile"></xsl:param>
    <xsl:param name="anzahl_uebersetzungen"></xsl:param>
    <transkriptionsspalten>
      <xsl:for-each select="section">
        <xsl:apply-templates select=".">
          <xsl:with-param name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:with-param>
          <xsl:with-param name="anzahl_inschriftenteile"><xsl:value-of select="$anzahl_inschriftenteile"/></xsl:with-param>
          <xsl:with-param name="anzahl_bearbeitungen"><xsl:value-of select="$anzahl_bearbeitungen"/></xsl:with-param>
          <xsl:with-param name="anzahl_uebersetzungen"><xsl:value-of select="$anzahl_uebersetzungen"/></xsl:with-param>
        </xsl:apply-templates>
      </xsl:for-each>
    </transkriptionsspalten>
  </xsl:template>



  <xsl:template name="standorte">
<!-- level umtauschen bei landkreisen zwecks nachfolgender umsortierung -->
        <xsl:param name="p_standorte_sort1">
            <xsl:for-each select="standorte/standort">
                <standort>
<xsl:for-each select="lemma">
<xsl:choose>
    <xsl:when test=".=$p_basesite"><xsl:attribute name="basesite"><xsl:value-of select="$p_basesite"/></xsl:attribute></xsl:when>
<xsl:otherwise>
                        <xsl:choose>
                            <xsl:when test="starts-with(.,'Ldkr.') or starts-with(.,'Landkreis') or starts-with(.,'Lkr.')">
                                <lemma level="1.1">
                                   <xsl:text>(</xsl:text><xsl:value-of select="."/> <xsl:text>)</xsl:text>
                                </lemma>
                        </xsl:when>
                        <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
                        </xsl:choose>
</xsl:otherwise>
</xsl:choose>
</xsl:for-each>

<!--                    <xsl:for-each select="lemma[not(.=$p_basesite)]">
                        <xsl:choose>
                            <xsl:when test="starts-with(.,'Ldkr.') or starts-with(.,'Landkreis') or starts-with(.,'Lkr.')">
                                <lemma level="1.1">
                                   <xsl:text>(</xsl:text><xsl:value-of select="."/> <xsl:text>)</xsl:text>
                                </lemma>
                        </xsl:when>
                        <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>-->
                </standort>
            </xsl:for-each>
        </xsl:param>
<!-- umsortiern -->
    <xsl:param name="p_standorte_sort2">
        <xsl:for-each select="$p_standorte_sort1/standort">
            <standort><xsl:copy-of select="@*"/>
                <xsl:for-each select="lemma">
                    <xsl:sort order="ascending" select="@level" data-type="number"/>
                    <xsl:copy-of select="."/>
                </xsl:for-each>
            </standort>
        </xsl:for-each>
    </xsl:param>

    <standorte log2="sto1">
        <xsl:for-each select="$p_standorte_sort2/standort">
          <standort><xsl:copy-of select="@*"/>
            <xsl:for-each select="lemma">
                <xsl:value-of select="."/><xsl:if test="following-sibling::lemma[1][not(starts-with(.,'('))]"><xsl:text>,</xsl:text></xsl:if><xsl:if test="following-sibling::lemma"><xsl:text> </xsl:text></xsl:if>
             </xsl:for-each>
            </standort>
        </xsl:for-each>
      </standorte>
<!--
<parametertest>
<p_standorte_sort1><xsl:copy-of select="$p_standorte_sort1"/></p_standorte_sort1>
<p_standorte_sort2><xsl:copy-of select="$p_standorte_sort2"/></p_standorte_sort2>
</parametertest>
-->
  </xsl:template>




  <xsl:template name="beschreibung">
    <!-- hier wird die erste Beschreibung eingefügt -->
    <beschreibung><xsl:apply-templates select="sections/section[@name='Beschreibung'][1]/items/item[@itemtype='text']/content"/></beschreibung>
  </xsl:template>

  <xsl:template name="kommentar">
    <!--<kommentar><xsl:apply-templates select="section[@caption='Kommentar'][last()]/fields/beschreibung"/></kommentar>-->
    <xsl:for-each select="sections/section[@name='Kommentar']">
      <xsl:choose>
        <xsl:when test="preceding-sibling::section[@name='Kommentar']"></xsl:when>
        <xsl:when test="following-sibling::section[@name='Kommentar']"></xsl:when>
        <xsl:otherwise>
          <xsl:for-each select="items/item[@itemtype='text']/content">
            <kommentar log="k1"><xsl:apply-templates select="."/></kommentar>
          </xsl:for-each>
        </xsl:otherwise>
      </xsl:choose>
     </xsl:for-each>
  </xsl:template>

 <xsl:template name="angaben">
   <xsl:param name="p_projectlanguage"></xsl:param>
   <xsl:param name="anzahl_inschriften"></xsl:param>
   <xsl:param name="angaben">
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
     <!-- inschrift nach -->
     <xsl:choose>
       <xsl:when test="sections/section[@kategorie='Allgemeine Angaben: Inschrift(en) nach']">
         <inschrift_nach><xsl:apply-templates select="sections/section[@kategorie='Allgemeine Angaben: Inschrift(en) nach']/items/item[@itemtype='text']/content"/></inschrift_nach>
       </xsl:when>
       <xsl:otherwise>
         <xsl:call-template name="inschrift_nach">
           <xsl:with-param name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:with-param>
           <xsl:with-param name="p_projectlanguage"><xsl:value-of select="$p_projectlanguage"/></xsl:with-param>
            <xsl:with-param name="p_db"><xsl:value-of select="$p_db"/></xsl:with-param>
         </xsl:call-template>
       </xsl:otherwise>
     </xsl:choose>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

     <!-- ergänzung nach -->
     <xsl:choose>
       <xsl:when test="sections/section[@caption='Allgemeine Angaben: Ergänzung(en) nach']">
         <ergaenzung_nach><xsl:apply-templates select="sections/section[@kategorie='Allgemeine Angaben: Ergänzung(en) nach']/items/item[@itemtype='chapter']/content"/></ergaenzung_nach>
       </xsl:when>
       <xsl:otherwise>
         <xsl:call-template name="ergaenzung_nach">
           <xsl:with-param name="p_projectlanguage"><xsl:value-of select="$p_projectlanguage"/></xsl:with-param>
           <xsl:with-param name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:with-param>
            <xsl:with-param name="p_db"><xsl:value-of select="$p_db"/></xsl:with-param>
         </xsl:call-template>
       </xsl:otherwise>
     </xsl:choose>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

<!-- sonstige angaben -->
    <xsl:if test="sections/section[@caption='Allgemeine Angaben: Sonstiges']">
        <angaben_sonstiges><xsl:apply-templates select="sections/section[@kategorie='Allgemeine Angaben: Sonstiges']/items/item[@itemtype='text']/content"/></angaben_sonstiges>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>

 <!-- massangaben -->
     <xsl:choose>
       <xsl:when test="sections/section[@kategorie='Allgemeine Angaben: Maße']">
         <xsl:for-each select="sections/section[@kategorie='Allgemeine Angaben: Maße']/items/item[@itemtype='text']/content">
            <abmessungen><xsl:apply-templates select="."/></abmessungen>
         </xsl:for-each>
       </xsl:when>
        <xsl:otherwise>
         <xsl:call-template name="abmessungen"></xsl:call-template>
        </xsl:otherwise>
     </xsl:choose>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

     <!-- schrifthöhen -->
     <xsl:choose>
       <xsl:when test="sections/section[@kategorie='Allgemeine Angaben: Buchstabenhöhe(n)']">
         <schrifthoehen><xsl:apply-templates select="sections/section[@kategorie='Allgemeine Angaben: Buchstabenhöhe(n)']/items/item[@itemtype='text']/content"/></schrifthoehen>
       </xsl:when>
       <xsl:otherwise>
         <xsl:call-template name="schrifthoehen">
            <xsl:with-param name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:with-param>
            <xsl:with-param name="p_db"><xsl:value-of select="$p_db"/></xsl:with-param>
        </xsl:call-template>
       </xsl:otherwise>
     </xsl:choose>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

     <!-- schriftarten -->
     <xsl:choose>
       <xsl:when test="sections/section[@kategorie='Allgemeine Angaben: Schriftart(en)']">
         <schriftarten log="fonttypes2"><xsl:apply-templates select="sections/section[@kategorie='Allgemeine Angaben: Schriftart(en)']/items/item[@itemtype='text']/content"/></schriftarten>
       </xsl:when>

<!--       <xsl:when test="section[@kategorie='Kopfzeile: Datierung']">
         <datierung_kopfzeile><xsl:apply-templates select="section[@kategorie='Kopfzeile: Datierung']/fields/beschreibung"/></datierung_kopfzeile>
       </xsl:when>-->

       <xsl:otherwise>
         <xsl:call-template name="angaben_schriftarten">
         <xsl:with-param name="anzahl_inschriften"><xsl:value-of select="$anzahl_inschriften"/></xsl:with-param>
         <xsl:with-param name="p_db"><xsl:value-of select="$p_db"/></xsl:with-param>
        </xsl:call-template>
       </xsl:otherwise>
     </xsl:choose>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
   </xsl:param>

   <angaben><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
     <xsl:for-each select="$angaben/*[*|text()]"><xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:for-each>
     <xsl:for-each select="abbildungen">
       <xsl:copy><xsl:value-of select="."/></xsl:copy><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
     </xsl:for-each>
   </angaben><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
   <!-- [*|text()] -->
 </xsl:template>


  <!-- nachweisfeld -->
  <xsl:template name="nachweise">
    <nachweise><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:for-each select="sections/section[@sectiontype='references']/items/item">
      <item>
        <!-- um eine verknüpfung mit dem literaturverzeichnis zu ermöglichen, wird die id der literaturangabe kopiert -->
        <xsl:copy-of select="property/@id"/>
        <xsl:call-template name="nachweis"/>
      </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:for-each>
    </nachweise>
  </xsl:template>

  <!-- einzelnachweise -->
  <xsl:template name="nachweis">
    <!-- es wird ermittelt, ob die ergänzung zum literaturtitel mit einem punkt endet -->
    <xsl:param name="laenge_ergaenzung"><xsl:value-of select="string-length(content)"/></xsl:param>
    <xsl:param name="ende_ergaenzung"><xsl:value-of select="substring(content, $laenge_ergaenzung)"/></xsl:param>
    <!-- hier wird festgelegt, ob die verknüpfung des titels mit der ergänzung durch ein komma mit leerschritt
      oder nur durch leerschritt erfolgt -->
    <xsl:param name="junktor">
      <xsl:choose>
        <xsl:when test="
          starts-with(content, 'Abb.') or
          starts-with(content, 'Abbildung') or
         starts-with(content, 'Bd.') or
         starts-with(content, 'Bl.') or
         starts-with(content, 'Fig.') or
         starts-with(content, 'Figur') or
          starts-with(content, 'fol.') or
          starts-with(content, 'Jg.') or
          starts-with(content, 'Nr.') or
          starts-with(content, 'S.') or
          starts-with(content, 'Seite') or
          starts-with(content, 'Sp.') or
          starts-with(content, 'Spalte') or
          starts-with(content, 'T.') or
          starts-with(content, 'Taf.') or
          starts-with(content, 'Tafel') or
          starts-with(content, 'Kap.') or
          starts-with(content, 'Kapitel') or
          starts-with(content, 'Kat.-Nr') or
          starts-with(content, 'Teil') or
          starts-with(content, 'Tf.')
          ">
          <xsl:text>,&#x20;</xsl:text>
        </xsl:when>
        <xsl:otherwise>
          <xsl:text>&#x20;</xsl:text>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:param>
    <xsl:param name="kurztitel">
      <!-- kurztitel auslesen und zwischenspeichern, dabei unterscheiden zwischen epigraf 4 und epigraf4.1 -->
      <xsl:for-each select="property">
        <link type="literatur">
          <xsl:attribute name="target_id"><xsl:value-of select="@id"/></xsl:attribute>
          <xsl:value-of select="name"/>
        </link>
      </xsl:for-each>
    </xsl:param>
    <!-- die nachweiszeile wird erzeugt. es gelten folgende bedingungen:
    1) wenn eine ergänzung zum titel vorliegt, wird die entsprechende verknüpfung (junktor) eingefügt
    2) wenn die ergänzung nicht mit einem punkt endet, wird ein punkt angefügt, außer bei der münchener reihe
    -->
    <xsl:copy-of select="$kurztitel"/><xsl:if test="content[node()]"><xsl:value-of select="$junktor"/></xsl:if><xsl:apply-templates select="content"/><xsl:if test="not($ende_ergaenzung='.') and not($modus='projects_bay')">.</xsl:if>
  </xsl:template>



  <xsl:template name="buchstabenapparat">
    <buchstabenapparat><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:for-each select=".//app2">
    <xsl:variable name="v_app2"><xsl:value-of select="@id"/></xsl:variable>
        <xsl:for-each select="ancestor::article/footnotes/footnote[@from_tagid=$v_app2]">
            <item><xsl:copy-of select="@id"/>
                    <xsl:attribute name="itemID"><xsl:value-of select="$v_app2"/></xsl:attribute>
                    <xsl:attribute name="pkID"><xsl:value-of select="@from_tagid"/></xsl:attribute><!-- pk bedeutet place keeper = platzhalter -->
                    <p><xsl:apply-templates select="content"></xsl:apply-templates></p>
            </item>
        </xsl:for-each>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:for-each>
    </buchstabenapparat>
  </xsl:template>


  <xsl:template name="ziffernapparate">
    <ziffernapparat>
    <xsl:for-each select="sections/section[@kategorie='Beschreibung'][1]">
      <xsl:call-template name="ziffernapparat"/>
    </xsl:for-each>
    <xsl:for-each select="sections/section[@sectiontype='measures']">
      <xsl:call-template name="ziffernapparat"/>
    </xsl:for-each>

      <xsl:for-each select="sections/section[@kategorie='Allgemeine Angaben: Inschrift(en) nach']">
        <xsl:call-template name="ziffernapparat"/>
      </xsl:for-each>

      <xsl:for-each select="sections/section[@kategorie='Allgemeine Angaben: Ergänzung(en) nach']">
        <xsl:call-template name="ziffernapparat"/>
      </xsl:for-each>

      <xsl:for-each select="sections/section[@kategorie='Allgemeine Angaben: Maße']">
        <xsl:call-template name="ziffernapparat"/>
      </xsl:for-each>

      <xsl:for-each select="sections/section[@kategorie='Allgemeine Angaben: Buchstabenhöhe(n)']">
        <xsl:call-template name="ziffernapparat"/>
      </xsl:for-each>

      <xsl:for-each select="sections/section[@kategorie='Allgemeine Angaben: Schriftart(en)']">
        <xsl:call-template name="ziffernapparat"/>
      </xsl:for-each>

    <!-- inschriften und zwischentexte -->
    <xsl:for-each select=".//section[@sectiontype='inscriptiontext' or @sectiontype='text']">
      <xsl:choose>
        <!-- wenn es sich nicht um die Haupt-Beschreibung vor den Inschriften,
          sondern um einen Zwischen-Beschreibung handelt (die hauptbeschreibung wurde bereits angesteuert) -->
        <xsl:when test="@kategorie='Beschreibung'">
          <xsl:choose>
            <xsl:when test="preceding-sibling::section[@kategorie='Beschreibung']">
              <xsl:call-template name="ziffernapparat"/>
            </xsl:when>
            <xsl:otherwise></xsl:otherwise>
          </xsl:choose>
        </xsl:when>
        <!-- wenn es sich nicht um den hauptkommentar nach den inschriften,
          sondern um einen zwischenkommentar handelt (der hautpkommentar wurde bereits angesteuert) -->
        <xsl:when test="@kategorie='Kommentar'">
          <xsl:choose>
            <xsl:when test="following-sibling::section[@kategorie='Kommentar']">
              <xsl:call-template name="ziffernapparat"/>
            </xsl:when>
            <xsl:otherwise></xsl:otherwise>
          </xsl:choose>
        </xsl:when>

        <!-- allgemeine angaben ... und versmaße werden an anderer stelle ausgelesen -->
        <xsl:when test="starts-with(@kategorie,'Allgemeine Angaben')"></xsl:when>
        <xsl:when test="starts-with(@kategorie,'Versmaße')"></xsl:when>
        <xsl:otherwise>
          <!-- nur die transkriptionen ansteuern, die übersetzungen werden anschließend separat erfasst -->
            <xsl:for-each select="items/item/content">
            <xsl:call-template name="ziffernapparat"/>
          </xsl:for-each>
<!--          <xsl:for-each select="fields/transkription">
            <xsl:call-template name="ziffernapparat"/>
          </xsl:for-each>
          <xsl:for-each select="fields/beschreibung">
            <xsl:call-template name="ziffernapparat"/>
          </xsl:for-each>-->

        </xsl:otherwise>
      </xsl:choose>
    </xsl:for-each>

      <!-- übersetzungen-->
      <xsl:for-each select=".//section[@sectiontype='inscriptiontext']/items/item/translation">
        <xsl:call-template name="ziffernapparat"/>
      </xsl:for-each>

    <xsl:for-each select="sections/section[@kategorie='Versmaße']">
      <xsl:call-template name="ziffernapparat"/>
    </xsl:for-each>

    <xsl:for-each select="sections/section[@sectiontype='heraldry']">
<!--      <xsl:sort select="@position_vertikal"/>
      <xsl:sort select="@position_horizontal"/>-->
      <xsl:call-template name="ziffernapparat_wappen"/>
    </xsl:for-each>

    <xsl:for-each select="sections/section[@kategorie='Kommentar' or @name='Kommentar'][last()]">
      <xsl:call-template name="ziffernapparat"/>
    </xsl:for-each>
    </ziffernapparat>
  </xsl:template>

  <xsl:template name="ziffernapparat">
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:for-each select=".//*[self::app1 or self::loc_ten]">
        <xsl:variable name="v_app1_id"><xsl:value-of select="@id"/></xsl:variable>
            <xsl:choose>
              <xsl:when test="self::app1">
                <xsl:for-each select="ancestor::article/footnotes/footnote[@from_tagid=$v_app1_id]">
                 <item><xsl:copy-of select="@id"/>
                    <xsl:attribute name="itemID"><xsl:value-of select="$v_app1_id"/></xsl:attribute>
                    <xsl:attribute name="pkID"><xsl:value-of select="@from_tagid"/></xsl:attribute><!-- pk bedeutet place keeper = platzhalter -->
                    <p><xsl:apply-templates select="content"></xsl:apply-templates></p>
                  </item>
                </xsl:for-each>
              </xsl:when>
              <xsl:otherwise>
                <item><xsl:copy-of select="@id"/>
                        <xsl:attribute name="itemID">item-<xsl:value-of select="@id"/></xsl:attribute><!-- da das item erst erzeugt wird, muss auch die ID generiert werden, hier aus der ID des platzhalters -->
                        <xsl:attribute name="pkID"><xsl:value-of select="@id"/></xsl:attribute><!-- pk bedeutet place keeper = platzhalter -->
                        <p><xsl:call-template name="loc_ten"></xsl:call-template></p>
                </item>
                </xsl:otherwise>
            </xsl:choose>
       <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:for-each>
  </xsl:template>

  <xsl:template name="ziffernapparat_wappen">
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
                <xsl:when test="starts-with(property/name , '?')">
                <item>
                    <xsl:attribute name="id"> <xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="itemID"><xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="pkID"><xsl:value-of select="@id"/></xsl:attribute>
                    <p>Wappen ? (<xsl:apply-templates select="property/content"></xsl:apply-templates><xsl:if test="property/elements[node()]">, </xsl:if><xsl:apply-templates select="property/elements"></xsl:apply-templates><xsl:text>).</xsl:text></p>
                </item>
                </xsl:when>

                <xsl:when test="contains(property/name , 'unbekannt')">
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

  <xsl:template name="wappen">
    <xsl:param name="wappenbreite1">
        <xsl:for-each select="sections/section[@sectiontype='heraldry']">
            <xsl:copy-of select="items/item/pos_x"/>
        </xsl:for-each>
    </xsl:param>
    <xsl:param name="wappenbreite2">
        <xsl:for-each select="$wappenbreite1/pos_x">
            <xsl:sort data-type="number"/>
            <xsl:copy-of select="."/>
        </xsl:for-each>
    </xsl:param>
    <xsl:param name="wappenbreite3">
        <xsl:value-of select="$wappenbreite2/pos_x[last()]"/>
    </xsl:param>

    <xsl:param name="wappenhoehe1">
        <xsl:for-each select="sections/section[@sectiontype='heraldry']">
            <xsl:copy-of select="items/item/pos_y"/>
        </xsl:for-each>
    </xsl:param>
    <xsl:param name="wappenhoehe2">
        <xsl:for-each select="$wappenhoehe1/pos_y">
            <xsl:sort data-type="number"/>
            <xsl:copy-of select="."/>
        </xsl:for-each>
    </xsl:param>
    <xsl:param name="wappenhoehe3">
        <xsl:value-of select="$wappenhoehe2/pos_y[last()]"/>
    </xsl:param>
    <xsl:if test="sections/section[@sectiontype='heraldry']/items/item">
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <heraldik><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<parametertest_wappentabelle>x:<xsl:value-of select="$wappenbreite3"/>; y:<xsl:value-of select="$wappenhoehe3"/></parametertest_wappentabelle>

        <bezeichnung>Wappen:</bezeichnung><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <tabelle><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <xsl:call-template name="loopzeilen">
            <xsl:with-param name="zeilennummer">1</xsl:with-param>
            <xsl:with-param name="max_zeilen">
              <xsl:value-of select="$wappenhoehe3"/>
            </xsl:with-param>
            <xsl:with-param name="max_spalten">
              <xsl:value-of select="$wappenbreite3"/>
            </xsl:with-param>
          </xsl:call-template>
        </tabelle><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </heraldik><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
  </xsl:template>

  <xsl:template name="loopzeilen">
    <xsl:param name="zeilennummer"></xsl:param>
    <xsl:param name="max_zeilen"></xsl:param>
    <xsl:param name="max_spalten"></xsl:param>
    <xsl:if test="$zeilennummer &lt;= $max_zeilen">
      <zeile lfnr="{$zeilennummer}" max_zeilen="{$max_zeilen}" max_spalten="{$max_spalten}">
        <xsl:call-template name="loopspalten">
          <xsl:with-param name="spaltennummer">1</xsl:with-param>
          <xsl:with-param name="max_spalten">
            <xsl:value-of select="$max_spalten"/>
          </xsl:with-param>
        <xsl:with-param name="zeilennummer">
          <xsl:value-of select="$zeilennummer"/>
        </xsl:with-param>
        </xsl:call-template>
      </zeile><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:call-template name="loopzeilen">
        <xsl:with-param name="zeilennummer">
          <xsl:value-of select="$zeilennummer + 1"/>
        </xsl:with-param>
        <xsl:with-param name="max_zeilen">
          <xsl:value-of select="$max_zeilen"/>
        </xsl:with-param>
        <xsl:with-param name="max_spalten">
          <xsl:value-of select="$max_spalten"/>
        </xsl:with-param>

      </xsl:call-template>
    </xsl:if>
  </xsl:template>

  <xsl:template name="loopspalten">
    <xsl:param name="spaltennummer"></xsl:param>
    <xsl:param name="max_zeilen"></xsl:param>
    <xsl:param name="max_spalten"></xsl:param>
    <xsl:param name="zeilennummer"></xsl:param>
    <xsl:if test="$spaltennummer &lt;= $max_spalten">
      <zelle spaltennummer="{$spaltennummer}" max_spalten="{$max_spalten}">
        <xsl:for-each select="sections/section[@sectiontype='heraldry']/items/item[pos_x=$spaltennummer and pos_y=$zeilennummer]">
          <xsl:variable name="wappen-id"><xsl:value-of select="property/@id"/></xsl:variable>
<entry>
          <wappen target_id="{$wappen-id}">
               <xsl:choose>
                  <xsl:when test="starts-with(property/name , '?') or contains(property/name , 'unbekannt')">
                    <xsl:choose>
                        <!-- münchener reihe -->
                        <xsl:when test="$modus='projects_bay'"> <xsl:text>unbekannt</xsl:text></xsl:when>
                        <!-- andere reihen -->
                        <xsl:otherwise> <xsl:text>?</xsl:text></xsl:otherwise>
                    </xsl:choose>
                  </xsl:when>
                 <xsl:when test="contains(property/name,'{')">
                <!-- in das wappenregister gehen -->
                   <!-- die lemmata werden wegen möglicher umbenennungen (im zuge der anpassung an den bestand des projekts)
                aus dem wappenregister genommen-->
                    <xsl:for-each select="ancestor::book/indices/index[@propertytype='heraldry' or @type='heraldry']/item[@id=$wappen-id]">
                        <xsl:value-of select="lemma"/>
                    </xsl:for-each>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="property/name"/></xsl:otherwise>
                </xsl:choose>
         </wappen>
        <!-- fussnote anfügen -->
            <xsl:choose>
                <xsl:when test="content"><app1><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute></app1></xsl:when>
            <xsl:otherwise>
                    <xsl:if test="starts-with(property/name , '?') or contains(property/name , 'unbekannt')">
                     <app1><xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute></app1>
                    </xsl:if></xsl:otherwise>
            </xsl:choose>
          </entry>
        </xsl:for-each>
      </zelle>
      <xsl:call-template name="loopspalten">
        <xsl:with-param name="spaltennummer">
          <xsl:value-of select="$spaltennummer + 1"/>
        </xsl:with-param>
        <xsl:with-param name="max_zeilen">
          <xsl:value-of select="$max_zeilen"/>
        </xsl:with-param>
        <xsl:with-param name="max_spalten">
          <xsl:value-of select="$max_spalten"/>
        </xsl:with-param>
        <xsl:with-param name="zeilennummer">
          <xsl:value-of select="$zeilennummer"/>
        </xsl:with-param>
      </xsl:call-template>
    </xsl:if>
  </xsl:template>

  <xsl:template name="verse">
    <xsl:param name="anzahl_inschriften"></xsl:param>
    <xsl:param name="verse-sammeln">
    <xsl:if test="sections//section[@sectiontype='inscription']/items/item[@itemtype='metres']">
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <xsl:for-each select="sections//section[@sectiontype='inscription'][items/item[@itemtype='metres']]">
        <xsl:variable name="inschriftID"><xsl:value-of select="@id"/></xsl:variable>
        <xsl:variable name="v_sectionnumber"><xsl:value-of select="@number"/></xsl:variable>
        <xsl:for-each select="items/item[@itemtype='metres']">
        <item>
          <xsl:copy-of select="@id"/>
          <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <xsl:if test="$anzahl_inschriften != 1">
              <xsl:choose>
                <xsl:when test="content/rec_intern">
                  <xsl:for-each select="content/rec_intern">
                    <nr target_id="{$inschriftID}" sectonnumber="{$v_sectionnumber}"><xsl:apply-templates select="."></xsl:apply-templates></nr>
                  </xsl:for-each>
                </xsl:when>
                <xsl:otherwise>
                  <nr target_id="{$inschriftID}" sectonnumber="{$v_sectionnumber}"><xsl:copy-of select="ancestor::section[@sectiontype='inscription']/@number"/>
                    <xsl:choose>
                        <!-- nummerierung münchener reihe-->
                        <xsl:when test="$modus='projects_bay'"><xsl:number value="ancestor::section[@sectiontype='inscription']/@number" format="I"/></xsl:when>
                        <!-- nummerierung andere reiehen -->
                        <xsl:otherwise><xsl:value-of select="ancestor::section[@sectiontype='inscription']/@name"/></xsl:otherwise>
                    </xsl:choose>
                  </nr>
                </xsl:otherwise>
              </xsl:choose>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          </xsl:if>
          <content>
          <xsl:choose>
            <xsl:when test="content[text()]"><xsl:value-of select="normalize-space(content)"/></xsl:when>
            <xsl:otherwise><xsl:value-of select="normalize-space(property/name)"/></xsl:otherwise>
          </xsl:choose>
          </content><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:for-each>
    </xsl:for-each>
    </xsl:if>
    </xsl:param>



<!-- parameter anzeigen -->
<!--    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <param_verse_sammeln><xsl:copy-of select="$verse-sammeln"/></param_verse_sammeln>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <param_anzahl_inschriften><xsl:copy-of select="$anzahl_inschriften"/></param_anzahl_inschriften>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->

    <verse><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!-- die verschiedenen versformen separieren und referenzen auf inschriften zusammenführen -->
      <xsl:for-each select="$verse-sammeln/item[not(content=preceding-sibling::item/content)]">
        <item>
          <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <xsl:copy-of select="content"/>
            <links>
            <xsl:for-each select="nr">
            <link  type="inschrift"><xsl:copy-of select="@*"/>
                <xsl:value-of select="."/>
            </link>
            </xsl:for-each>
            <xsl:for-each select="following-sibling::item[content=current()/content]">
                            <xsl:for-each select="nr">
                                  <link type="inschrift"><xsl:copy-of select="@*"/><xsl:value-of select="."/></link>
                            </xsl:for-each>
                          </xsl:for-each>
            </links>

            <!--<nrn type="inscriptRef">
               <xsl:for-each select="nr">
                    <nr><xsl:copy-of select="@*"/>
                      <link type="inschrift"><xsl:attribute name="target_id"><xsl:value-of select="@target_id"/></xsl:attribute><xsl:value-of select="."/></link>
                    </nr>
                  </xsl:for-each>
              <xsl:for-each select="following-sibling::item[content=current()/content]">
                <xsl:for-each select="nr">
                    <nr><xsl:copy-of select="@*"/>
                      <link type="inschrift"><xsl:attribute name="target_id"><xsl:value-of select="@target_id"/></xsl:attribute><xsl:value-of select="."/></link>
                    </nr>
                </xsl:for-each>
              </xsl:for-each>
            </nrn>-->
        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:for-each>
    </verse>
  </xsl:template>
  <!-- ende: verse -->

  <xsl:template match="item[@itemtype='metres']/content">
    <xsl:param name="verszeile"><xsl:apply-templates/></xsl:param>
      <xsl:choose>
        <xsl:when test="contains($verszeile, ' (') and not(contains($verszeile, ' (?)'))">
          <xsl:value-of select="substring-before($verszeile,' (')"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:value-of select="$verszeile"/>
        </xsl:otherwise>
      </xsl:choose>
  </xsl:template>

  <!-- verweise auf inschriften -->
<!--
<rec_intern id="000004355650010064814814840819" value="G" data-link-target="sections-319911" data-link-value="H"/>
<rec_intern id="000004409439279473379629634292" value="Fu&#xDF;note a" data-link-target="footnotes-12749" data-link-value="~NA~"/>

<section log="s1" id="sections-319911" parent_id="" articles_id="articles-3556" sectiontype="inscription" sectionname="H" sectionnumber="8" level="1" kategorie="" data_key="">
 -->
<xsl:template match="rec_intern">
    <xsl:param name="p_rec_intern_type"><xsl:value-of select="substring-before(@data-link-target,'-')"/></xsl:param>
    <xsl:param name="p_data_link_target"><xsl:value-of select="@data-link-target"/></xsl:param>
    <xsl:param name="p_target_type"><xsl:value-of select="ancestor::article//section[@id=$p_data_link_target]/@sectiontype"/></xsl:param>
    <xsl:param name="p_target_number"><xsl:value-of select="ancestor::article//section[@id=$p_data_link_target]/@number"/></xsl:param>
    <xsl:param name="p_target_name">
        <xsl:value-of select="ancestor::article//section[@id=$p_data_link_target]/@name"/>
    </xsl:param>
    <xsl:param name="p_target_parent_name">
        <xsl:value-of select="ancestor::article//section[@id=$p_data_link_target]/parent::section/@name"/>
    </xsl:param>
    <xsl:param name="p_target_parent_number">
        <xsl:value-of select="ancestor::article//section[@id=$p_data_link_target]/parent::section/@number"/>
    </xsl:param>
<!--
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<para1><xsl:value-of select="$p_rec_intern_type"/></para1><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<para2><xsl:value-of select="$p_data_link_target"/></para2><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<para2><xsl:value-of select="$p_target_type"/></para2><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<para4><xsl:value-of select="$p_target_number"/></para4><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<para5><xsl:value-of select="$p_target_name"/></para5><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<para6><xsl:value-of select="$p_target_parent_name"/></para6><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<para7><xsl:value-of select="$p_target_parent_number"/></para7><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->

    <xsl:choose>
        <xsl:when test="$p_rec_intern_type='footnotes'">
                  <link type="{$p_rec_intern_type}"><xsl:copy-of select="@data-link-target"/>
                    <!-- weil die fußnoten noch nicht nummeriert sind, kann der bezeichner (zahl oder buchstaben) der fußnote
                      erst im nächsten transformationsschritt anhand der target_id ausgelesen werden -->
                  </link>
        </xsl:when>
        <xsl:otherwise>
            <link type="{$p_rec_intern_type}"><xsl:copy-of select="@data-link-target"/>
                        <xsl:choose>
                            <xsl:when test="$modus='projects_bay'">
                            <xsl:if test="$p_target_type='inscriptionpart'"><xsl:number value="$p_target_parent_number" format="I"/><xsl:text>.</xsl:text></xsl:if><xsl:number value="$p_target_number" format="I"/>
                            </xsl:when>
                            <xsl:otherwise><xsl:if test="$p_target_type='inscriptionpart'"><xsl:value-of select="$p_target_parent_name"/></xsl:if><xsl:value-of select="$p_target_name"/></xsl:otherwise>
                        </xsl:choose>
            </link>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>


  <!-- REC EXTERN NEU-->

<!--
Beispiele für alle Arten möglicher Verweise
Verweis auf Website: <a id="000004364945313722222222224960" href="http://www.inschriften.net" value="DIO"/>

externe Verweise auf:
anderen Artikel: <rec_extern id="000004409436642755787037083174" value="hwi.zaschendorf.retabel[Artikel]" data-link-target="articles-3556" data-link-value="hwi.zaschendorf.retabel"/>

Inschrift in einem anderen Artikel : <rec_extern id="000004409436659200231481519504" value="hwi.zaschendorf.retabel[Inschrift A]" data-link-target="sections-272944" data-link-value="A"/>

Inschriftteil in einem anderen Artikel: <rec_extern id="000004409436679006944444418015" value="hwi.zaschendorf.retabel[Inschrift D2]" data-link-target="sections-320944" data-link-value="2"/>

Ziffernfußnote in einem anderen Artikel: <rec_extern id="000004409436696782407407478395" value="hwi.zaschendorf.retabel[Fu&#xDF;note 16]" data-link-target="footnotes-12485" data-link-value="~NA~"/>

Buchstabenfußnote in einem anderen Artikel: <rec_extern id="000004409436730376157407465196" value="hwi.zaschendorf.retabel[Fu&#xDF;note c]" data-link-target="footnotes-12697" data-link-value="~NA~"/>

interne Verweise:
auf Inschrift: <rec_intern id="000004411270206751157407427243" value="E" data-link-target="sections-320555" data-link-value="E"/>

auf Inschriftteil: <rec_intern id="000004411271208787037037073652" value="M2" data-link-target="sections-329686" data-link-value="2"/>

auf Fußnote: <rec_intern id="000004409439279473379629634292" value="Fu&#xDF;note a" data-link-target="footnotes-12749" data-link-value="~NA~"/>

auf Marke: <rec_ma id="000004409439306443287037053925" value="HM.gct118.1122" data-link-target="properties-25152" data-link-value="HM.gct118.1122"/>


Literaturverweis: <rec_lit id="000004409439532908564814855594" value="Adler, Stralsund" data-link-target="properties-20865" data-link-value="Adler, Stralsund"/>

Verweis auf Grundrissobjekt: <loc_ten id="000004409439625269675925933078"/></content>

die attribute sind immer:
@id
@value
@data-link-target
@data-link-value

-->

  <xsl:template match="rec_extern">
    <!-- id  des verweises: -->
    <xsl:param name="p_id"><xsl:value-of select="@id"/></xsl:param>
<!-- signatur des artikels, auf den verwiesen wird, ergänzt um die weiterleitung auf den betreffenden abschnitt (artikel, inschrift, inschriftteil, fußnote)  : -->
    <xsl:param name="p_value"><xsl:value-of select="@data-link-value"/></xsl:param>
<!-- id des verweisziels:-->
    <xsl:param name="p_data-link-target"><xsl:value-of select="@data-link-target"/></xsl:param>
<!--  bezeichner des verweisziels -->
    <xsl:param name="p_data-link-value"><xsl:value-of select="@data-link-value"/></xsl:param>
<!-- target-typ (articles | sections | footnotes) -->
    <xsl:param name="p_target-type"><xsl:value-of select="substring-before(@data-link-target,'-')"/></xsl:param>
<!-- es wird geprüft, ob der verweis in einer verweisfolge steht -->
    <xsl:param name="p_folge"><xsl:if test="ancestor::folge">1</xsl:if></xsl:param>

<xsl:param name="p_rec-extern_links">
    <xsl:choose>

<!-- verweis auf einen artikel -->
        <xsl:when test="$p_target-type='articles'">
        <!-- bei verweisen auf artikel muss nur die artikelnummer ermittelt werden-->
          <link type="articles" folge="{$p_folge}"><xsl:copy-of select="@*"/>
                <xsl:choose>
                <!-- wenn der artikel in der auswahl enthalten ist, ihn über die @id ansteuern und für die laufnummer attribute erzeugen -->
                    <xsl:when test="ancestor::book/catalog/article[@id=$p_data-link-target]">
                      <xsl:for-each select="ancestor::book/catalog/article[@id=$p_data-link-target]">
                            <xsl:attribute name="article-sortstring"><xsl:number count="article" from="catalog" format="001"/></xsl:attribute>
                            <xsl:number count="article" from="catalog" format="1"/>
                        </xsl:for-each>
                    </xsl:when>
                    <!-- wenn der artikel nicht in der auswahl vorhanden ist, die bezeichnung des verweiszieles einfügen -->
                    <xsl:otherwise><xsl:value-of select="$p_value"/></xsl:otherwise>
                </xsl:choose>
          </link>
        </xsl:when>

<!-- verweis auf einen artikel mit verlängerung auf eine inschrift -->
        <xsl:when test="$p_target-type='sections'">
        <!-- bei verweisen áuf inschriften in anderen artikeln müssen artikelnummer und bezeichnung der inschrift ermittelt werden -->
            <link type="sections" folge="{$p_folge}"><xsl:copy-of select="@*"/>
                <xsl:choose>
                    <!-- wenn der artikel mit der angewiesenen inschrift in der auswahl enthalten ist, ihn über die @id ansteuern und für die laufnummer attribute erzeugen -->
                    <xsl:when test="ancestor::book/catalog/article/sections//section[@id=$p_data-link-target]">
                    <!-- zuerst die inschrift-section ansteuern-->
                        <xsl:for-each select="ancestor::book/catalog/article/sections//section[@id=$p_data-link-target]">
                            <xsl:variable name="v_sectionnumber"><xsl:value-of select="parent::section/@name"/><xsl:value-of select="@name"/></xsl:variable>
                            <!-- für den inschriftbezeichner das attribute @name ausgelesen, bei einem inschriftteil davor dasselbe attribut der übergeordneten inschrift-section -->
                            <xsl:attribute name="section-number"><xsl:value-of select="$v_sectionnumber"/></xsl:attribute>
                    <!-- dann den übergeordneten artikel ansteuern und die laufnummer des artikels in attribute geben -->
                            <xsl:for-each select="ancestor::article">
                                    <xsl:attribute name="article-sortstring"><xsl:number count="article" from="catalog" format="001"/></xsl:attribute>
<!--                                    <xsl:number count="article" from="catalog" format="1"/><xsl:text> (</xsl:text><xsl:value-of select="$v_sectionnumber"/><xsl:text>)</xsl:text>-->
                                    <xsl:number count="article" from="catalog" format="1"/><inscriptlink log="i1"><xsl:value-of select="$v_sectionnumber"/></inscriptlink>
                            </xsl:for-each>
                        </xsl:for-each>
                    </xsl:when>
                    <!-- wenn der artikel nicht in der auswahl vorhanden ist, die bezeichnung des verweiszieles einfügen -->
                    <xsl:otherwise><xsl:value-of select="$p_value"/></xsl:otherwise>
                </xsl:choose>
            </link>
        </xsl:when>


<!-- verweis auf einen artikel mit verlängerung auf eine fußnote -->
        <xsl:when test="$p_target-type='footnotes'">
            <link type="footnotes_extern" folge="{$p_folge}"><xsl:copy-of select="@*"/>
                <xsl:choose>
                    <!-- wenn der artikel mit der angewiesenen fußnote in der auswahl enthalten ist, ihn über die @id ansteuern und für die laufnummer attribute erzeugen -->
                    <xsl:when test="ancestor::book/catalog/article[footnotes/footnote[@id=$p_data-link-target]]">
                        <xsl:for-each select="ancestor::book/catalog/article[footnotes/footnote[@id=$p_data-link-target]]">
                                    <xsl:attribute name="article-number"><xsl:number count="article" from="catalog" format="1"/></xsl:attribute>
                                    <xsl:attribute name="article-sortstring"><xsl:number count="article" from="catalog" format="001"/></xsl:attribute>
                                    <!-- artikelnummer eingeben -->
                                    <xsl:value-of select="@nr"/>
                        </xsl:for-each>
                        <!-- der  bezeichner für die fußnote  kann über @data_link_target erst in der nächsten transformation ermittelt weden -->
                    </xsl:when>
                    <!-- wenn der artikel nicht in der auswahl vorhanden ist, die bezeichnung des verweiszieles einfügen -->
                    <xsl:otherwise><xsl:value-of select="$p_value"/></xsl:otherwise>
                </xsl:choose>
            </link>
        </xsl:when>


    </xsl:choose>
</xsl:param>

<!-- parametertest
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<p_rec-extern_links><xsl:copy-of select="$p_rec-extern_links"/></p_rec-extern_links><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
 -->

<!-- parameter auslesen und die links mit Kat-Nr. verbinden, wen sie nicht in einer verweisfolge stehen -->
    <xsl:for-each select="$p_rec-extern_links/link">
        <xsl:if test="not(@folge='1')"><xsl:text>Kat.-Nr. </xsl:text></xsl:if> <xsl:copy-of select="."/>
    </xsl:for-each>
</xsl:template>

  <!-- ende: REC EXTERN NEU-->

<xsl:template match="rec_lit">
<!-- <rec_lit id="000004409439532908564814855594" value="Adler, Stralsund" data-link-target="properties-20865" data-link-value="Adler, Stralsund"/> -->
<link type="literatur"><xsl:copy-of select="@id"/><xsl:copy-of select="@data-link-target"/><xsl:copy-of select="@data-link-value"/><xsl:value-of select="@data-link-value"/><xsl:if test="not(@data-link-value)"><xsl:value-of select="@value"/></xsl:if></link>
</xsl:template>

  <!--<xsl:template match="x-rec_lit">
    <xsl:param name="id"><xsl:value-of select="@id"/></xsl:param>

    <xsl:if test="ancestor::article">
      <xsl:for-each select="ancestor::section/links/item[@p_tag_id=$id]">
        <link type="literatur">
          <xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute>
          <xsl:attribute name="target_id"><xsl:value-of select="@link_item"/></xsl:attribute>
          <xsl:value-of select="name"/>
        </link>

        <!-\-<xsl:if test="name"><xsl:value-of select="name"/></xsl:if>-\->
      </xsl:for-each>
      <!-\-<xsl:value-of select="ancestor::section/links/item[@p_tag_id=current()/@id]/kurztitel"/>-\->
    </xsl:if>

    <xsl:if test="ancestor::einleitung">
      <xsl:for-each select="ancestor::abschnitt/links/item[@p_tag_id=$id]">
      <link type="literatur">
        <xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute>
        <xsl:attribute name="target_id"><xsl:value-of select="@link_item"/></xsl:attribute>
        <xsl:if test="name"><xsl:value-of select="name"/></xsl:if>
      </link>
      </xsl:for-each>
    </xsl:if>

    <xsl:if test="ancestor::konkordanz">
      <xsl:for-each select="ancestor::konkordanz/links/item[@p_tag_id=$id]">
      <link type="literatur">
        <xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute>
        <xsl:attribute name="target_id"><xsl:value-of select="@link_item"/></xsl:attribute>
        <xsl:if test="name"><xsl:value-of select="name"/></xsl:if>
      </link>
      </xsl:for-each>
    </xsl:if>

    <xsl:if test="ancestor::index">
      <xsl:for-each select="ancestor::book/catalog/article[section[@sectiontype='heraldry']/links/item[@p_tag_id=$id]][1]">
        <xsl:for-each select="section[@sectiontype='heraldry']/links/item[@p_tag_id=$id][1]">
        <link type="literatur">
          <xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute>
          <xsl:attribute name="target_id"><xsl:value-of select="@link_item"/></xsl:attribute>
          <xsl:value-of select="name"/>
        </link>
        </xsl:for-each>
      </xsl:for-each>
    </xsl:if>
  </xsl:template>-->

  <!-- verweise auf marken -->
  <xsl:template match="rec_ma">
       <xsl:param name="p_data_link_target"><xsl:value-of select="@data-link-target"/></xsl:param>
<!-- rec_ma in link umwidmen -->
    <link type="marke">
      <xsl:copy-of select="@*"/>
    <!-- markentyp ermitteln -->
    <xsl:attribute name="brandtype">
        <xsl:value-of select="ancestor::sections/section[@sectiontype='brands']/items/item/property[@id=$p_data_link_target]/property[@propertytype='brandtypes']/lemma"/>
    </xsl:attribute>
    <!-- markenbesitzer ermitteln -->
    <xsl:attribute name="brandowner">
        <xsl:value-of select="ancestor::sections/section[@sectiontype='brands']/items/item/property[@id=$p_data_link_target]/lemma"/>
    </xsl:attribute>
    </link>
  </xsl:template>

  <xsl:template match="app1">
    <xsl:variable name="v_id"><xsl:value-of select="@id"/></xsl:variable>
    <xsl:choose>
<!-- in der einleitung werden die anmerkungen in den text gezogen, damit sie in word automatisch an das jeweilige seitenende gesetzt werden können -->
      <xsl:when test="ancestor::einleitung">
        <xsl:for-each select="ancestor::book/footnotes/footnote[@from_tagid=$v_id]">
        <!-- für interne verweise auf fußnoten wird die id der fußnote auf das app1-tag übertragen -->
        <app1><xsl:copy-of select="@id"/>
        <p><xsl:apply-templates select="content"></xsl:apply-templates></p>
        </app1>
        </xsl:for-each>
      </xsl:when>
<!-- in den artikeln wird das anmerkungszeichen unverändert übernommen -->
      <xsl:otherwise><app1 id="{$v_id}"/></xsl:otherwise>
    </xsl:choose>
  </xsl:template>

  <!-- zeilenumbrüche -->
  <xsl:template match="nl">
        <xsl:choose>
            <xsl:when test="ancestor::bl"></xsl:when>
        <xsl:when test="ancestor::footnote">
            <xsl:text disable-output-escaping="yes">&#x003C;/p&#x003E;</xsl:text>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:text disable-output-escaping="yes">&#x003C;p&#x003E;</xsl:text>
        </xsl:when>
        <xsl:otherwise><xsl:copy></xsl:copy></xsl:otherwise>
        </xsl:choose>
  </xsl:template>


  <xsl:template match="app2"><xsl:copy><xsl:copy-of select="@*"/></xsl:copy></xsl:template>
  <xsl:template match="loc_ten"><app1 source="loc_ten"><xsl:copy-of select="@id"/></app1></xsl:template>

  <xsl:template match="quot"><quot><xsl:apply-templates/></quot></xsl:template>

  <xsl:template name="notizen">
  <notizen><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:for-each select="sections//section/comment[text()]">
      <xsl:variable name="parent_section1"><xsl:value-of select="ancestor-or-self::section/@parent_id"/></xsl:variable>
      <notiz><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <zuordnung>
          <xsl:choose>
            <xsl:when test="ancestor-or-self::section[@sectiontype='inscriptiontext']"><xsl:for-each select="ancestor::article//section[@id=$parent_section1]"><xsl:variable name="parent_section2"><xsl:value-of select="ancestor-or-self::section/@parent_id"/></xsl:variable><xsl:for-each select="ancestor::article//section[@id=$parent_section2]"><xsl:text>Inschrift </xsl:text><xsl:value-of select="@name"/></xsl:for-each><xsl:text> Teil </xsl:text><xsl:value-of select="@name"/></xsl:for-each><xsl:text> Bearbeitung </xsl:text><xsl:value-of select="ancestor-or-self::section/@name"/></xsl:when>
            <xsl:when test="ancestor-or-self::section[@sectiontype='inscriptionpart']"><xsl:for-each select="ancestor::article//section[@id=$parent_section1]"><xsl:variable name="parent_section2"><xsl:value-of select="ancestor-or-self::section/@parent_id"/></xsl:variable><xsl:text>Inschrift </xsl:text><xsl:value-of select="@name"/></xsl:for-each><xsl:text> Teil </xsl:text><xsl:value-of select="ancestor-or-self::section/@name"/></xsl:when>
            <xsl:when test="ancestor-or-self::section[@sectiontype='inscription']"><xsl:text>Inschrift </xsl:text><xsl:value-of select="ancestor-or-self::section/@name"/></xsl:when>
            <xsl:otherwise><xsl:value-of select="ancestor-or-self::section/@name"/></xsl:otherwise>
          </xsl:choose>
        </zuordnung><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <notiztext><xsl:for-each select="node()"><xsl:apply-templates select="."/></xsl:for-each></notiztext>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </notiz><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:for-each>
  </notizen><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>



  <xsl:template match="h[@value='Schriftarten' or @data-link-value='Schriftarten']"><xsl:copy-of select="."/></xsl:template>

  <!--<xsl:template name="loc_ten">Siehe Grundriss ...</xsl:template>-->

  <xsl:template match="section[@sectiontype='text'][@name='Versma&#xDF;e']"></xsl:template>

  <xsl:template match="section[@kategorie='Kopfzeile: Datierung']"></xsl:template>
<xsl:template match="section[@kategorie='Kopfzeile: Standorte']"></xsl:template>

  <xsl:template match="section[@sectiontype='text'][@name='Gliederung: Ebene 1']">
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <gliederung1><xsl:apply-templates select=".//content"></xsl:apply-templates></gliederung1>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="section[@sectiontype='text'][@name='Gliederung: Ebene 2']">
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <gliederung2><xsl:apply-templates select=".//content"></xsl:apply-templates></gliederung2>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="section[@sectiontype='text'][@name='Gliederung: Ebene 3']">
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <gliederung3><xsl:apply-templates select=".//content"></xsl:apply-templates></gliederung3>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="section[@sectiontype='text'][@name='Inschriften nummerieren']">
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <numbering><xsl:apply-templates select=".//content"></xsl:apply-templates></numbering>
    <xsl:for-each select="section">
            <xsl:apply-templates select="."></xsl:apply-templates>
</xsl:for-each>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="section[@sectiontype='text'][@name='Kommentar']">
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:if test="preceding-sibling::section[@name='Kommentar']"></xsl:if>
    <kommentar log="k2"><xsl:apply-templates select="items/item[@itemtype='text']/content"></xsl:apply-templates></kommentar>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

  </xsl:template>


  <xsl:template match="section[@sectiontype='text'][@name='Beschreibung']">
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <beschreibung><xsl:apply-templates select="items/item[@itemtype='text']/content"></xsl:apply-templates></beschreibung>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>


  <xsl:template match="vorwort">
    <vorwort>
      <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:copy-of select="titel"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <content><xsl:copy-of select="content/@*"/>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="content/p">
              <xsl:apply-templates select="."></xsl:apply-templates>
            </xsl:for-each>
      </content><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:copy-of select="datum"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:copy-of select="unterschrift"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </vorwort><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>



  <xsl:template match="einleitung">
    <einleitung>
      <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:copy-of select="titel"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:for-each select="abschnitt">
      <xsl:apply-templates select="."></xsl:apply-templates>
    </xsl:for-each>
    </einleitung>
  </xsl:template>



  <xsl:template match="abkuerzungen">
    <abkuerzungen indizieren="1">
      <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:copy-of select="titel"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:for-each select="content">
        <xsl:choose>
          <xsl:when test="@type='table'">
            <table><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <xsl:for-each select="p">
                <row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <cell><xsl:apply-templates></xsl:apply-templates></cell>
                </row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              </xsl:for-each>
            </table><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          </xsl:when>
        <xsl:when test="@type='csv_table'">
            <table>
                  <xsl:for-each select="t">
                    <row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                      <cell><xsl:apply-templates select="."></xsl:apply-templates></cell>
                    </row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  </xsl:for-each>
            </table>
        </xsl:when>
          <xsl:otherwise><xsl:apply-templates></xsl:apply-templates></xsl:otherwise>
        </xsl:choose>
      </xsl:for-each>
<!--      <xsl:for-each select="content/p">
        <row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <cell><xsl:apply-templates></xsl:apply-templates></cell>
        </row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:for-each>-->
    </abkuerzungen>
  </xsl:template>

  <!-- absätze in vorwort, einleitung usw. -->
  <xsl:template match="p">
<xsl:choose>
    <xsl:when test="text()='§'"></xsl:when>
    <xsl:when test="h"><xsl:copy-of select="h"/></xsl:when>
    <xsl:otherwise>
    <p>
            <xsl:attribute name="indent">
              <xsl:choose>
                <xsl:when test="not(preceding-sibling::p)">0</xsl:when>
                <xsl:when test="preceding-sibling::p[1][text()='§']">0</xsl:when>
                <xsl:when test="preceding-sibling::p[1][not(node())]">0</xsl:when>
                <xsl:when test="preceding-sibling::p[1][h]">0</xsl:when>
                <xsl:otherwise>1</xsl:otherwise>
              </xsl:choose>
            </xsl:attribute>
            <xsl:apply-templates></xsl:apply-templates></p>
</xsl:otherwise>
</xsl:choose>
  </xsl:template>


  <xsl:template match="abschnitt">
    <abschnitt>
      <xsl:copy-of select="@*"/>
      <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <xsl:choose>
        <!-- tabellen-abschnitte in der einleitung-->
        <xsl:when test="@data_key='csv_table'">
          <xsl:for-each select="content/t">
            <row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <cell><xsl:apply-templates select="."></xsl:apply-templates></cell>
            </row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          </xsl:for-each>
        </xsl:when>
        <xsl:when test="@sectiontype='table'"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <xsl:for-each select="content/tr">
            <xsl:copy><xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="td">
                    <xsl:copy><xsl:copy-of select="@*"/><xsl:apply-templates></xsl:apply-templates></xsl:copy><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:for-each>
            </xsl:copy><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          </xsl:for-each>
        </xsl:when>
        <!-- andere abschnitte in der einleitung-->
        <xsl:otherwise><xsl:apply-templates select="content"></xsl:apply-templates></xsl:otherwise>
      </xsl:choose>
    </abschnitt><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <!-- tabellen-abschnitte der einleitung ansteuern und alle zutreffenden templates aufrufen -->
  <xsl:template match="t">
    <xsl:apply-templates></xsl:apply-templates>
  </xsl:template>

<!-- textknoten in den tabellen-abschnitten der einleitung -->
    <xsl:template match="t/text()">
        <xsl:choose>
<!-- wenn der textknoten eine raute # enthält, wird diese durch ein schließendes und wieder öffnendes cell-tag ersetzt -->
          <xsl:when test="contains(.,'#')">
            <xsl:value-of select="substring-before(.,'#')"/>
            <xsl:text disable-output-escaping="yes">&lt;/cell&gt;&#x000D;&lt;cell&gt;</xsl:text>
            <xsl:value-of select="substring-after(.,'#')"/>
          </xsl:when>
          <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
        </xsl:choose>
    </xsl:template>


  <xsl:template match="folge[not(parent::folge)]">
  <xsl:param name="folge1">
    <folge>
      <xsl:for-each select="*">
        <xsl:choose>
          <xsl:when test="name()='folge'">
            <folge><xsl:apply-templates></xsl:apply-templates></folge>
          </xsl:when>
          <xsl:otherwise><folge><xsl:apply-templates select="."></xsl:apply-templates></folge></xsl:otherwise>
        </xsl:choose>
      </xsl:for-each>
    </folge>
  </xsl:param>

  <xsl:param name="folge2">
    <xsl:for-each select="$folge1/folge">
      <xsl:for-each select="folge">
        <xsl:sort select="link/@article-sortstring" data-type="text"/>
        <xsl:copy-of select="."/>
      </xsl:for-each>
    </xsl:for-each>
  </xsl:param>


<!--<folge1><xsl:copy-of select="$folge1"/></folge1>
 <folge2><xsl:copy-of select="$folge2"/></folge2>-->


    <xsl:for-each select="$folge2">
    <folge>
      <xsl:text>Kat.-Nr. </xsl:text>
      <xsl:for-each select="folge">
        <xsl:copy-of select="."/><xsl:if test="following-sibling::folge"><xsl:text>, </xsl:text></xsl:if>
      </xsl:for-each>
    </folge>
  </xsl:for-each>
<!--  <folge><xsl:if test="not(parent::folge)"><katnr>Kat.-Nr. </katnr></xsl:if><xsl:apply-templates></xsl:apply-templates></folge>-->
</xsl:template>

  <!-- gesperrt ausgezeichnete wörter in der einleitung -->
  <xsl:template match="g"><xsl:copy-of select="."/></xsl:template>
    <!-- fett ausgezeichnete wörter in der einleitung -->
    <xsl:template match="b"><xsl:copy-of select="."/></xsl:template>

<!-- kursivierungen die keine zitate sind -->
<xsl:template match="i"><i><xsl:copy-of select="@*"/><xsl:apply-templates/></i></xsl:template>

<xsl:template match="section[starts-with(@kategorie, 'Datum')]"></xsl:template>
<xsl:template match="section[@kategorie='Zitatquellen']"></xsl:template>

 <xsl:template match="a">
   <xsl:copy-of select="."/>
 </xsl:template>

<!-- zwischenüberschriften in einleitungskapiteln -->
<xsl:template match="h"><h><xsl:copy-of select="@value"/><xsl:copy-of select="@data-link-value"/><xsl:apply-templates/></h></xsl:template>

<xsl:template match="caption"></xsl:template>



</xsl:stylesheet>

