<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:fn="http://www.w3.org/2005/xpath-functions"
     xmlns:php="http://php.net/xsl"
        xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
    >
<xsl:import href="commons/epi-switch.xsl"/>
<xsl:import href="trans1/epi-register-trans1.xsl"/>
<xsl:import href="trans1/epi-inschriftenliste.xsl"/>



<!-- die funktion dieses stylesheets besteht in der sortierung der katalogartikel nach drei kriterien -->

<!-- außerdem werden die abteilungen eines bandes neu angelegt und
        die inhalte aus den textbausteinen, dem katalog und den registern zusammengestellt -->

<!-- darüber hinaus können einzelne elemente modifiziert werden -->

    <!-- WICHTIG: der parameter "abbildungsliste_vorhanden" (c. zeile 43) muss deaktiviert werden, wenn das stylesheet offline verwendet wird
                  für den upload muss es aktiviert werden
    -->


    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="book">
        <xsl:choose>
            <xsl:when test="options/@text='1'">
<xsl:comment>book_band</xsl:comment>
                <xsl:call-template name="book_band"></xsl:call-template>
            </xsl:when>
            <xsl:otherwise>
<xsl:comment>book_katalog</xsl:comment>
                <xsl:call-template name="book_katalog"></xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="book_band">
        <!-- die folgenden parameter steuern die ausgabe der verweise auf abbildungen in den artikeln-->

      <xsl:param name="abbildungsliste_pfad">../../databases/<xsl:value-of select="project/@database"/>/lists/plates_<xsl:value-of select="project/signature"/>.xml</xsl:param>
      <xsl:param name="abbildungsliste_vorhanden">
            <xsl:choose>
                <xsl:when test="fn:doc-available($abbildungsliste_pfad) = true()">1</xsl:when>
                <xsl:otherwise>0</xsl:otherwise>
            </xsl:choose>
        </xsl:param>
        <xsl:param name="abbildungsliste">
            <xsl:call-template name="abbildungsliste">
                <xsl:with-param name="abbildungsliste_pfad"><xsl:copy-of select="$abbildungsliste_pfad"/></xsl:with-param>
                <xsl:with-param name="abbildungsliste_vorhanden"><xsl:copy-of select="$abbildungsliste_vorhanden"/></xsl:with-param>
            </xsl:call-template>
        </xsl:param>

<!-- book neu anlegen -->

        <xsl:copy>
        <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:copy-of select="options"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

<!-- parametertest abbildungen
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<parametertest-abbildungen><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<abbildungsliste-vorhanden><xsl:copy-of select="$abbildungsliste_vorhanden"></xsl:copy-of></abbildungsliste-vorhanden><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<abbildungsliste_pfad><xsl:copy-of select="$abbildungsliste_pfad"></xsl:copy-of></abbildungsliste_pfad><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<abbildungsliste><xsl:copy-of select="$abbildungsliste"/></abbildungsliste><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</parametertest-abbildungen><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->

<!-- projektangaben  -->
        <project><xsl:copy-of select="project/@*"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <database><xsl:value-of select="project/@database"/></database><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <xsl:copy-of select="project/signature"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <xsl:copy-of select="project/name"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <dio_signature data_key="dio_signature"><xsl:value-of select="volume/section[@data_key='dio_signature']/items/item[@itemtype='chapter']/content"/></dio_signature><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <di_number data_key="di_number"><xsl:value-of select="volume/section[@data_key='di_number']/items/item[@itemtype='chapter']/content"/></di_number><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
          </project>

<!-- die hauptabschnitte/sections des bandes werden in der gegebenen reihenfolge einzeln angesteuert -->
          <xsl:for-each select="volume/section">
            <!-- anhand der daten-schlüssel werden den einzelnen sections je eigene templates zugewiesen -->
            <xsl:variable name="data_key"><xsl:value-of select="@data_key"/></xsl:variable>
            <xsl:choose>
                <xsl:when test="@sectiontype='outputoptions'"><!--könnte benötigt werden, wenn das erste Notizfeld des Bandes ausgegeben werden soll--></xsl:when>
                <xsl:when test="$data_key='dio_signature'">
                    <!--<dio_signatur><xsl:value-of select="fields/beschreibung"/></dio_signatur>-->
                </xsl:when>
                <xsl:when test="$data_key='di_number'"><!-- entfällt --></xsl:when>
                <xsl:when test="$data_key='preliminaries'">
                    <xsl:if test="$titelei=1"><xsl:call-template name="titelei"></xsl:call-template></xsl:if>
                </xsl:when>

                <xsl:when test="$data_key='table_of_content'">
                    <xsl:if test="$inhalt=1"><xsl:call-template name="inhaltsverzeichnis"></xsl:call-template></xsl:if>
                </xsl:when>

                <xsl:when test="@data_key='prefaces'">
                    <xsl:if test="$vorwort=1"><xsl:call-template name="vorwort"></xsl:call-template></xsl:if>
                </xsl:when>

                <xsl:when test="@data_key='introduction'">
                    <xsl:if test="$einleitung=1"><xsl:call-template name="einleitung"></xsl:call-template></xsl:if>
                </xsl:when>

                <xsl:when test="$data_key='articles'">
                        <xsl:call-template name="articles">
                            <xsl:with-param name="abbildungsliste_pfad"><xsl:copy-of select="$abbildungsliste_pfad"/></xsl:with-param>
                            <xsl:with-param name="abbildungsliste_vorhanden"><xsl:copy-of select="$abbildungsliste_vorhanden"/></xsl:with-param>
                            <xsl:with-param name="abbildungsliste"><xsl:copy-of select="$abbildungsliste"/></xsl:with-param>
                        </xsl:call-template>
                </xsl:when>

                <xsl:when test="$data_key='table_of_inscriptions'">
                    <xsl:if test="$inschriftenliste=1"><xsl:call-template name="inschriftenliste"></xsl:call-template></xsl:if>
                </xsl:when>

                <xsl:when test="$data_key='abbreviations'">
                    <xsl:if test="$abkuerzungen=1"><xsl:call-template name="abkuerzungen"></xsl:call-template></xsl:if>
                </xsl:when>

                <xsl:when test="$data_key='indices'">
                    <xsl:call-template name="indices"></xsl:call-template>
                </xsl:when>
                <xsl:when test="$data_key='biblio'">
                    <xsl:if test="$quellen-literatur=1"><xsl:call-template name="quellen_literatur"></xsl:call-template></xsl:if>
                </xsl:when>

                <xsl:when test="$data_key='di_volumes'">
                    <xsl:if test="$di-bände=1"><xsl:call-template name="di_baende"></xsl:call-template></xsl:if>
                </xsl:when>

                <xsl:when test="$data_key='drawings'">
                    <xsl:if test="$zeichnungen='1' or
                                    $marken='1' or
                                    $grundrisse='1'">
                        <xsl:call-template name="zeichnungen"></xsl:call-template>
                    </xsl:if>
                </xsl:when>

                <xsl:when test="$data_key='plates'">
                    <xsl:if test="$bildtafeln=1"><xsl:call-template name="bildtafeln"></xsl:call-template></xsl:if>
                </xsl:when>
                <xsl:otherwise><error>Fehler: Dem Abschnitt &quot;<xsl:value-of select="@name"/>&quot; wurde kein gültiger Datenschlüssel zugewiesen. Wählen Sie einen Datenschlüssel aus oder wenden Sie sich an den Administrator!</error></xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>

          <!-- abbildungsliste: liste mit den referenzen von den artikeln auf abbildungen -->
<!--          <xsl:call-template name="abbildungsliste">
              <xsl:with-param name="abbildungsliste_vorhanden"><xsl:value-of select="$abbildungsliste_vorhanden"/></xsl:with-param>
              <xsl:with-param name="abbildungsliste_pfad"><xsl:value-of select="$abbildungsliste_pfad"/></xsl:with-param>
          </xsl:call-template>-->
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:copy-of select="volume/footnotes"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:copy-of select="volume/links"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:copy>
    </xsl:template>

    <xsl:template name="abbildungsliste">
        <xsl:param name="abbildungsliste_vorhanden"></xsl:param>
        <xsl:param name="abbildungsliste_pfad"></xsl:param>

<!--  parametertest
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <liste_vorhanden><xsl:copy-of select="$abbildungsliste_vorhanden"/></liste_vorhanden>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <listenpfad><xsl:copy-of select="$abbildungsliste_pfad"/></listenpfad>
     -->

        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

        <xsl:if test="$abbildungsliste_vorhanden='1'">
            <table>
                <xsl:for-each select="document($abbildungsliste_pfad)//*[name()='Row'][*/*[node()]]">
<!-- in der dritten zelle muss es ein data-element mit textknoten geben
und die dritte zelle (oder eine andere) darf nicht dass attribut ss:Index enthalten, weil es dadurch zur maskierten auslöschung der dritten zelle und zur verschiebung nach links der folgenden kommt
-->
                    <xsl:if test="*[3]/*[node()] and not(*[@ss:Index])">

                     <row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                         <cell><xsl:value-of select="*[2]/*"/></cell><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                         <cell><xsl:value-of select="*[3]/*"/></cell><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                     </row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:if>
              </xsl:for-each>
           </table>
        </xsl:if>
    </xsl:template>

    <xsl:template name="book_katalog">
       <!-- aufbau des bandes -->
        <xsl:copy>
            <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:copy-of select="options"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <xsl:copy-of select="project"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

            <!-- katalog -->
            <xsl:call-template name="katalog"></xsl:call-template>


            <!-- register -->
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <!--<xsl:copy-of select="volume/section[@name='Register']"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>-->
                 <xsl:for-each select="indices">
                     <xsl:call-template name="indices"></xsl:call-template>
                 </xsl:for-each>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

            <!--marken-->
            <xsl:call-template name="marken"></xsl:call-template>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

       </xsl:copy>
   </xsl:template>

    <xsl:template name="marken">
        <xsl:param name="marken">
            <xsl:copy-of select="indices/index[@propertytype='brands']/item"/>
        </xsl:param>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <marken>
            <!--<xsl:copy-of select="@*"/>-->
            <xsl:attribute name="indizieren">1</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="manage_lists/index[@propertytype='brandtypes']/item">
                <xsl:variable name="id"><xsl:value-of select="@id"/></xsl:variable>
                <xsl:variable name="typ"><xsl:value-of select="lemma"/></xsl:variable>
                <markentyp>
                    <xsl:attribute name="id"><xsl:value-of select="$id"/></xsl:attribute><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <titel><xsl:value-of select="lemma"/></titel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="$marken/item">
                        <!--<xsl:copy-of select="."/>-->
                        <xsl:if test="@brandtype-id=$id">
                            <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </xsl:if>
                    </xsl:for-each>
                </markentyp><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
        </marken><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:template>

    <xsl:template name="indices">
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <indices log1="ind1">
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="indizieren">1</xsl:attribute>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:choose>
            <!-- wenn die registerfolge und -struktur im band angelegt ist,
                wird diese angesteuert und in einem eigenen template abgearbeitet - in der datei epi44-register-trans1.xsl -->

             <xsl:when test="ancestor::book[options/@text='1']/volume/section[@data_key='indices']">
                <xsl:call-template name="bandregister"></xsl:call-template>
            </xsl:when>

            <!-- wenn die registerfolge und -struktur im band nicht angelegt ist, wird sie hier interimsweise erzeugt -->
            <xsl:otherwise>
        <!-- das standortregister ist für die kopfzeile erforderlich und wird daher immer mitgeführt -->
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:choose>
        <xsl:when test="$register=1">
                    <xsl:for-each select="index"><xsl:apply-templates select="."></xsl:apply-templates></xsl:for-each>
       </xsl:when>
        <xsl:otherwise>
            <xsl:apply-templates select="index[@propertytype='locations']"></xsl:apply-templates>
            <xsl:apply-templates select="index[@propertytype='fonttypes']"></xsl:apply-templates>
            <xsl:apply-templates select="index[@propertytype='heraldry']"></xsl:apply-templates>
        </xsl:otherwise>
</xsl:choose>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          </xsl:otherwise>
       </xsl:choose>
      </indices>
    </xsl:template>

    <xsl:template name="vorwort">

            <vorwort>
                <xsl:copy-of select="@*"/>
                <xsl:attribute name="indizieren">1</xsl:attribute>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <titel>
                    <xsl:choose>
                        <xsl:when test="items/item/content/wunsch[@tagname='Titel']">
                            <xsl:value-of select="items/item/content/wunsch[@tagname='Titel']"/>
                        </xsl:when>
                        <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                    </xsl:choose>
                </titel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:apply-templates select="items/item[@itemtype='chapter']/content"></xsl:apply-templates>
                <!-- die sections mit datum und unterschrift ansteuern -->
                <xsl:for-each select="section">
                    <xsl:variable name="caption"><xsl:value-of select="translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜ','abcdefghijklmnopqrstuvxyzäöü')"/></xsl:variable>
                    <xsl:element name="{$caption}">
                        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        <xsl:apply-templates select="items/item[@itemtype='chapter']/content"></xsl:apply-templates>
                    </xsl:element><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:for-each>
            </vorwort>

    </xsl:template>

    <xsl:template name="abkuerzungen">
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <abkuerzungen indizieren="1">
                <xsl:copy-of select="@*"/>
                <xsl:attribute name="indizieren">1</xsl:attribute>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <titel>
                    <xsl:choose>
                        <xsl:when test="items/item/content/wunsch[@tagname='Titel']">
                            <xsl:value-of select="items/item/content/wunsch[@tagname='Titel']"/>
                        </xsl:when>
                        <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                    </xsl:choose>
                </titel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:apply-templates></xsl:apply-templates>
            </abkuerzungen>
    </xsl:template>

    <!-- elemente in den tabellenfeldern werden kopiert -->
    <xsl:template match="section[@data_key='csv_table']//content/*">
        <xsl:copy-of select="."/>
    </xsl:template>
  <!-- tabellenformate ende -->

    <!-- zeilenumbrüche in den textfeldern der einleitung -->
<!--<xsl:template match="nl">
<xsl:choose>
    <xsl:when test="ancestor::property"><umbruch>testumbruch</umbruch></xsl:when>
<xsl:otherwise>
<xsl:choose>
       <!-\- csv-tabellen mit # als separator zur nachfolgenden tabellarischen formatierung-\->
            <xsl:when test="ancestor::section[@data_key='csv_table']">
                <xsl:text disable-output-escaping="yes">&#x003C;/t&#x003E;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x003C;t&#x003E;</xsl:text>
            </xsl:when>
           <!-\- einfache textfelder -\->
            <xsl:otherwise>
                <xsl:text disable-output-escaping="yes">&#x003C;/p&#x003E;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x003C;p&#x003E;</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
</xsl:otherwise>
</xsl:choose>

</xsl:template>-->

    <xsl:template match="nl[parent::content]">
        <xsl:choose>
       <!-- csv-tabellen mit # als separator zur nachfolgenden tabellarischen formatierung-->
            <xsl:when test="ancestor::section[@data_key='csv_table']">
                <xsl:text disable-output-escaping="yes">&#x003C;/t&#x003E;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x003C;t&#x003E;</xsl:text>
            </xsl:when>
           <!-- einfache textfelder -->
            <xsl:otherwise>
<xsl:if test="not(ancestor::property)">
                <xsl:text disable-output-escaping="yes">&#x003C;/p&#x003E;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x003C;p&#x003E;</xsl:text></xsl:if>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- zeilenumbrüche in den notizfelder der einleitung -->
    <xsl:template match="nl[parent::comment][ancestor::volume]">
                <xsl:text disable-output-escaping="yes">&#x003C;/p&#x003E;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x003C;p&#x003E;</xsl:text>
    </xsl:template>

    <!-- falls im beschreibung-element ein wunsch-tag existiert, wird es durch ein paragraf-zeichen ersetzt,
    damit das rahmende p-tag bei der nächsten transformation ausgesondert werden kann -->
    <xsl:template match="wunsch">§</xsl:template>



<xsl:template name="einleitung">
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  <einleitung>
      <xsl:copy-of select="@*"/>
      <xsl:attribute name="indizieren">1</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <!-- die einzelnen kapitel der einleitung werden aus de hierarchie herausgelöst und flach aneinander gereiht -->
        <titel>
            <xsl:choose>
                <xsl:when test="items/item/content/wunsch[@tagname='Titel']">
                    <xsl:value-of select="items/item/content/wunsch[@tagname='Titel']"/>
                </xsl:when>
<!--                <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>-->
                <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
            </xsl:choose>
        </titel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:for-each select=".//section">
          <xsl:variable name="caption"><xsl:value-of select="@name"/></xsl:variable>
          <xsl:variable name="kapitelnummer"><xsl:value-of select="substring-before($caption,'. ')"/>.</xsl:variable>
          <xsl:variable name="titel"><xsl:value-of select="substring-after($caption,'. ')"/></xsl:variable>
          <abschnitt>
              <xsl:copy-of select="@*"/>
              <!-- aus dem attribut caption werden die kapitelnummer und die kapitelüberschrift/titel selektiert -->
              <xsl:choose>
                <xsl:when test="starts-with($caption,'1') or
                                starts-with($caption,'2') or
                                starts-with($caption,'3') or
                                starts-with($caption,'4') or
                                starts-with($caption,'5') or
                                starts-with($caption,'6') or
                                starts-with($caption,'7') or
                                starts-with($caption,'8') or
                                starts-with($caption,'9')">
                    <xsl:attribute name="kapitelnummer"><xsl:value-of select="$kapitelnummer"/></xsl:attribute>
                    <xsl:attribute name="titel"><xsl:value-of select="$titel"/></xsl:attribute>
                </xsl:when>
                  <xsl:otherwise>
                      <xsl:choose>
                          <!-- für die abschnitte Fortsetzung und Tabelle werden keine Überschriften ausgelesen -->
                          <xsl:when test="@data_key='continuation'"></xsl:when>
                          <xsl:when test="@data_key='csv_table'"></xsl:when>
                          <!-- für abschnitte ohne kapitelnummer wird  das gesamte caption-attribut als titel ausgelesen -->
                          <xsl:otherwise><xsl:attribute name="titel"><xsl:value-of select="$caption"/></xsl:attribute></xsl:otherwise>
                      </xsl:choose>
                  </xsl:otherwise>
               </xsl:choose>
              <xsl:for-each select="items/item[@itemtype='chapter']">
                  <xsl:apply-templates select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              </xsl:for-each>
          </abschnitt><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:for-each>

  </einleitung>

</xsl:template>

<!-- elemente in den sections der einleitung -->
<xsl:template match="footnotes"><xsl:if test="node()"><xsl:copy-of select="."/></xsl:if></xsl:template>
<xsl:template match="links"><xsl:if test="node()"><xsl:copy-of select="."/></xsl:if></xsl:template>
<xsl:template match="meta"><!--<xsl:copy-of select="."/>--></xsl:template>
<xsl:template match="tables"><!--<xsl:copy-of select="."/>--></xsl:template>
<xsl:template match="property[ancestor::volume]"></xsl:template>
<xsl:template match="lemma[parent::property[ancestor::volume]]"></xsl:template>


<xsl:template match="content[ancestor::volume][parent::item[@itemtype='chapter']]">
        <content log1="cont1">
            <xsl:attribute name="type">
                <xsl:choose>
                    <xsl:when test="ancestor::section[@data_key='csv_table']">csv_table</xsl:when>
                    <xsl:when test="ancestor::section[@sectiontype='table']">table</xsl:when>
                    <xsl:otherwise>paragraphs</xsl:otherwise>
                </xsl:choose>
            </xsl:attribute>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:choose>
                <xsl:when test="ancestor::section[@data_key='csv_table']"><t><xsl:apply-templates/></t></xsl:when>
                <xsl:when test="ancestor::section[@sectiontype='table']"><xsl:apply-templates></xsl:apply-templates></xsl:when>
                <xsl:otherwise><p><xsl:apply-templates/></p></xsl:otherwise>
            </xsl:choose>
        </content><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

<!-- notizfelder in der einleitung -->
<xsl:template match="comment[ancestor::volume]">
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<comment><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<p><xsl:apply-templates/></p><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</comment><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

<!-- tabellenabschnitte in einleitung und artikel aus tr und td -->
<xsl:template match="tr"><xsl:copy-of select="."/></xsl:template>

<xsl:template match="lemma[ancestor::section]"></xsl:template>

<!-- elemente in den texten der einleitung -->
<xsl:template match="app1"><xsl:copy-of select="."/></xsl:template>
<xsl:template match="quot"><xsl:copy-of select="."/></xsl:template>
<xsl:template match="rec_lit"><xsl:copy-of select="."/></xsl:template>
<xsl:template match="rec_extern"><xsl:copy-of select="."/></xsl:template>
<xsl:template match="i"><xsl:copy-of select="."/></xsl:template>

<!--<xsl:template match="rec_extern">
    <xsl:choose>
        <xsl:when test="parent::folge">
            <folge><xsl:copy-of select="." /></folge>
        </xsl:when>
        <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
    </xsl:choose>
</xsl:template>-->

<xsl:template match="folge">
    <folge><xsl:copy-of select="@*"/><xsl:apply-templates></xsl:apply-templates></folge>
</xsl:template>
<!--<xsl:template match="folge[not(parent::folge)]">
    <folge><xsl:apply-templates/></folge>
</xsl:template>
<xsl:template match="folge[parent::folge]">
    <xsl:copy-of select="." />
</xsl:template>-->


<!--<xsl:template name="titelei">
    <titelei>
        <xsl:copy-of select="@*"/>
        <xsl:attribute name="indizieren">0</xsl:attribute>
            <!-\- die sections-elemente werden neu angelegt und nach dem caption-attribut benannt,
            anschließend alle überflüssigen element ausgesondert
            und nur der inhalt des beschreibungs-feldes eingefügt-\->
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="section">
            <xsl:variable name="element-name"><xsl:value-of select="translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','absdefghijklmnopqrstuvwxyz')"/></xsl:variable>
            <xsl:element name="{$element-name}"><xsl:copy-of select="@data_key"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="items/item[@itemtype='chapter']/content[node()]"><xsl:apply-templates select="."></xsl:apply-templates></xsl:for-each>
                <xsl:call-template name="titelei-sections"></xsl:call-template>
            </xsl:element><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
    </titelei>
</xsl:template>

    <xsl:template name="titelei-sections">
        <xsl:for-each select="section">
        <xsl:variable name="element-name"><xsl:value-of select="translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','absdefghijklmnopqrstuvwxyz')"/></xsl:variable>
        <xsl:element name="{$element-name}"><xsl:copy-of select="@data_key"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="items/item[@itemtype='chapter']/content[node()]"><xsl:apply-templates select="."></xsl:apply-templates></xsl:for-each>
            <xsl:call-template name="titelei-sections"></xsl:call-template>
        </xsl:element><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>
-->
<xsl:template name="titelei">
    <titelei>
        <xsl:copy-of select="@*"/>
        <xsl:attribute name="indizieren">0</xsl:attribute>
            <!-- die sections-elemente werden neu angelegt und nach dem caption-attribut benannt,
            anschließend alle überflüssigen element ausgesondert
            und nur der inhalt des beschreibungs-feldes eingefügt-->
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="section">
            <xsl:variable name="element-name"><xsl:value-of select="@data_key"/></xsl:variable>
            <xsl:element name="{$element-name}"><xsl:attribute name="log1">prelim0</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="items/item[@itemtype='chapter']/content[node()]"><xsl:apply-templates select="."></xsl:apply-templates></xsl:for-each>
                <xsl:call-template name="titelei-sections"></xsl:call-template>
            </xsl:element><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
    </titelei>
</xsl:template>


    <xsl:template name="titelei-sections">
        <xsl:for-each select="section">
        <xsl:variable name="element-name">
            <xsl:choose>
                <xsl:when test="@data_key and string-length(@data_key)!=0"><xsl:value-of select="@data_key"/></xsl:when>
                <xsl:otherwise><xsl:value-of select="translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZ','absdefghijklmnopqrstuvwxyz')"/></xsl:otherwise>
            </xsl:choose>
            </xsl:variable>
        <xsl:element name="{$element-name}"><xsl:attribute name="log1">prelim1</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="items/item[@itemtype='chapter']/content[node()]"><xsl:apply-templates select="."></xsl:apply-templates></xsl:for-each>
            <xsl:call-template name="titelei-sections"></xsl:call-template>
        </xsl:element><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>


<xsl:template name="quellen_literatur">
<!-- literaturausgabe für die pipeline band -->
  <!-- literatur-abschnitt im band aufsuchen -->
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <quellen_literatur>
          <xsl:copy-of select="@*"/>
          <xsl:attribute name="version">4.1</xsl:attribute>
          <xsl:attribute name="indizieren">1</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

          <!-- titel auslesen -->
<xsl:choose>
    <xsl:when test="ancestor::volume"><xsl:call-template name="section-titel"></xsl:call-template></xsl:when>
    <xsl:otherwise><titel>Quellen und Literatur</titel></xsl:otherwise>
</xsl:choose>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

          <!-- literaturregister aufsuchen -->
          <xsl:for-each select="ancestor::book/indices/index[@propertytype='literature']">
              <xsl:for-each select="item[not(@ishidden='1')]">
<xsl:sort select="lemma/@sortstring" lang="de" case-order="upper-first" order="ascending" data-type="text"/>
                  <abschnitt>
                      <xsl:copy-of select="@*"/>
                      <xsl:attribute name="indizieren">1</xsl:attribute>
                      <xsl:copy-of select="lemma"/>
                      <xsl:call-template name="titel_sortieren"></xsl:call-template>
                  </abschnitt>

              <!--    <xsl:copy-of select="."/>-->
              </xsl:for-each>
          </xsl:for-each>
       </quellen_literatur><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

    <xsl:template name="titel_sortieren">
        <xsl:for-each select="item">
            <xsl:sort select="lemma/@sortstring" lang="de" case-order="upper-first" order="ascending" data-type="text"/>
            <item>
                <xsl:copy-of select="@*"/>
                <xsl:copy-of select="lemma"/>
                <xsl:call-template name="titel_sortieren"></xsl:call-template>
            </item>
        </xsl:for-each>
    </xsl:template>


<xsl:template name="titel_suchen">
    <!-- die literatur- und quellentitel für das literaturverzeichnis werden in mehreren stufen zusammengestellt -->

    <!-- zunächst werden für die folgeoperationen erforderliche parameter angelegt -->
    <!-- key für den unterabschnitt (archivalien oder literatur) -->
    <xsl:param name="biblio-section"><xsl:value-of select="fields/beschreibung/*[@tagname='key']"/></xsl:param>

    <!-- pfad auf die textdatei mit den vollständigen literatur-titeln oder quellen-titeln;
    es ist ein relativer pfad erforderlich, beginnend vom verzeichnis mit den stylesheets (templates)
    über den nächst höheren ordner (export) zum obersten datei-ordner in epigrafweb,
    dann eins tiefer zum ordner databases,
    dann auf den ordner der arbeitsstelle (wird aus dem element projekt/datenbank automatisch generiert),
    schließlich zum ordner mit der entsprechenden datei - diese angabe wird aus den textbausteinen des bandes,
    entsprechend der dor vorgenommenen angabe generiert-->
    <xsl:param name="path"><xsl:text>../../databases/</xsl:text><xsl:value-of select="ancestor::book/project/datenbank"/><xsl:text>/</xsl:text><xsl:value-of select="section[@name='database-path']/fields/beschreibung"/></xsl:param>

    <!-- die aus citavi exportierte textdatei mit den formatierten langtiteln wird für die folgeoperationen in einen parameter geladen  -->
    <xsl:param name="langtitel">
        <!-- nur wenn in den textbausteinen das bandes eine pfadangabe existiert, wird das externe document geladen,
        anderenfalls käme es zu einem abbruch der transformation und einer entsprechenden fehlermeldung-->
        <xsl:if test="section[@name='database-path']/fields/beschreibung[text()]">
            <xsl:copy-of select="document($path)"/>
        </xsl:if>
    </xsl:param>

    <!-- die zusammenführung der kurz- und langtitel erfolgt zunächst in einem parameter;
    dazu werden zuerst alle titel des literatur-register aufgerufen und mit den langtiteln aus der literaturliste verkoppelt;
    anschließend wird der parameter erneut aufgerufen und die titel endformatiert-->
    <xsl:param name="biblio">
        <!-- in einem section-tag wird angegeben, ob es sich um literatur oder archivalien handelt  -->
        <section><xsl:value-of select="$biblio-section"/></section>
        <xsl:for-each select="section[@name='database-name']">
            <xsl:variable name="database"><xsl:value-of select="substring-before(fields/beschreibung, '.')"/></xsl:variable>

            <!-- die kurztitel im literatur-register werden angesteuert und nach dem darin angegebenen citavi-datenbanknamen ausgelesen  -->
        <xsl:for-each select="ancestor::book/indices/index[@type='properties_literatur']/item[starts-with(import_db, $database)]">
            <!-- die citavi-laufnummer wird als variable abgelegt, zur verwendung beim ansteuern der entsprechenden langtitel in der literaturliste  -->
            <xsl:variable name="import_id"><xsl:value-of select="import_id"/></xsl:variable>
            <item>
                <!-- in den items werden alle elemente außer den links mitgeführt  -->
                <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy-of select="kurztitel"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy-of select="import_db"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy-of select="import_id"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <langtitel>
                    <!-- mittels der variable import_id wird der zugehörige langtitel aus der literaturliste angesteuert -->
                    <xsl:for-each select="$langtitel//*[lnr|nr=$import_id]">
                        <xsl:choose>
                            <!-- beim einfügen der element wird zwischen literatur und archivalien unterschieden -->
                            <xsl:when test="$biblio-section='literatur'"><xsl:value-of select=".//biblio"/></xsl:when>
                            <xsl:otherwise>
                                <xsl:copy-of select=".//archiv"/>
                                <xsl:copy-of select=".//lt"/>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>
                </langtitel>
            </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
         </xsl:for-each>
        </xsl:for-each>
    </xsl:param>

    <!-- die titelliste wird aus dem parameter biblio aufgerufen, die liste der archivalien wird neu strukturiert -->
    <xsl:for-each select="$biblio">
        <xsl:choose>
            <!-- die literaturliste wird wie gegeben übernommen -->
            <xsl:when test="section='literatur'">
                <xsl:for-each select="item"><xsl:copy-of select="."/></xsl:for-each>
            </xsl:when>
            <!-- die archivalienliste wird umstrukturiert; hauptkriterium wird das archiv;
            items desselben archivs werden darunter gesammelt-->
            <xsl:otherwise>
                <!-- das jeweils erste item eines archivs wird angesteuert -->
                <xsl:for-each select="item[not(langtitel/archiv=preceding-sibling::item/langtitel/archiv)]">
                    <item>
                        <!-- bezeichnung des archivs auslesen -->
                        <xsl:copy-of select="langtitel/archiv"/>
                        <!-- das item neu anlegen und die erforderlichen element auswählen -->
                        <item>
                            <xsl:copy-of select="kurztitel"/>
                            <xsl:copy-of select="import_db"/>
                            <xsl:copy-of select="import_id"/>
                            <xsl:copy-of select="langtitel/lt/*"/>
                        </item>
                        <!-- die übrigen items desselben archivs ansteuern -->
                        <xsl:for-each select="following-sibling::item[langtitel/archiv=current()/langtitel/archiv]">
                            <!-- das item neu anlegen und die erforderlichen element auswählen -->
                            <item>
                                <xsl:copy-of select="kurztitel"/>
                                <xsl:copy-of select="import_db"/>
                                <xsl:copy-of select="import_id"/>
                                <xsl:copy-of select="langtitel/lt/*"/>
                            </item>
                        </xsl:for-each>
                    </item>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:for-each>


<!--
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <biblio><xsl:copy-of select="$biblio"/></biblio>
    <biblio-section><xsl:copy-of select="$biblio-section"/></biblio-section><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <path><xsl:copy-of select="$path"/></path><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <langtitel><xsl:copy-of select="$langtitel"/></langtitel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->

</xsl:template>

<xsl:template name="katalog">
    <!-- template für epigraf 4 -->
    <catalog indizieren="1">
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!-- katalogtitel aus den textbausteinen des bandes auswählen -->
    <xsl:for-each select="volume/section[@data_key='articles']">
        <xsl:call-template name="section-titel"></xsl:call-template>
   </xsl:for-each>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

    <!-- artikel auswählen -->
   <xsl:for-each select="catalog/article">
    <!-- artikel kopieren -->
    <xsl:copy>
        <!-- nummerierung einfügen -->
        <xsl:attribute name="nr"><xsl:number count="article" format="1" from="catalog"/></xsl:attribute>
        <xsl:copy-of select="@*"/>
           <!-- den artikelinhalt kopieren -->
               <xsl:for-each select="*"><xsl:copy-of select="."/></xsl:for-each>
    </xsl:copy>
       <!--<xsl:copy-of select="."/>-->
   </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</catalog><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

<xsl:template name="articles">
    <xsl:param name="abbildungsliste"></xsl:param>
    <xsl:param name="abbildungsliste_vorhanden"></xsl:param>
 <xsl:param name="abbildungsliste_pfad"></xsl:param>
    <!-- template für epigraf 4.1 -->
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <catalog indizieren="1">
        <xsl:copy-of select="@*"/>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!-- katalogtitel aus den textbausteinen des bandes auswählen -->
        <xsl:call-template name="section-titel"/>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!-- artikel auswählen und neu anlegen-->
   <xsl:for-each select="ancestor::book/catalog/article">
        <!-- für den fall, dass die signaturen durch die katalognummern ersetzt wurden, die ursprüngliche signatur mit hilfe des projektkürzels identifizieren und aufnehmen -->
        <xsl:variable name="p_project_name"><xsl:value-of select="ancestor::book/project/signature"/></xsl:variable>
       <xsl:variable name="p_signatur"><xsl:value-of select="sections/section[@sectiontype='signatures']/items/item[@itemtype='signatures']/value"/></xsl:variable>
       <xsl:copy>
<xsl:attribute name="nr"><xsl:number count="article" format="1" from="catalog"/></xsl:attribute>
           <xsl:copy-of select="@*"/>
           <!--<test><xsl:copy-of select="$signatur"/></test>-->
           <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

<!-- parametertest artikel
<parametertest-artikel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<projekt><xsl:copy-of select="$p_project_name"/></projekt><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<signatur><xsl:copy-of select="$p_signatur"/></signatur><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<abbildungsliste><xsl:copy-of select="$abbildungsliste"/></abbildungsliste><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</parametertest-artikel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->

     <!-- container für referenzen auf abbildungen im tafelteil hinzufügen -->
           <abbildungen>
               <xsl:for-each select="$abbildungsliste//row">
                   <xsl:if test="cell[1][text()=$p_signatur]">
                   <xsl:value-of select="translate(cell[2],'-','–')"/></xsl:if>
               </xsl:for-each>
           </abbildungen><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
       <!-- den artikelinhalt kopieren -->
           <xsl:for-each select="*"><xsl:copy-of select="."/></xsl:for-each>
       </xsl:copy>
   </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</catalog><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

<xsl:template name="inhaltsverzeichnis">
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <inhalt>
        <xsl:copy-of select="@*"/>
        <xsl:attribute name="indizieren">0</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!-- überschrift des inhaltsverzeichnises -->
        <xsl:call-template name="section-titel"></xsl:call-template>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:apply-templates select="items/item[@itemtype='chapter']/content"></xsl:apply-templates>
    </inhalt><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

</xsl:template>

<xsl:template name="zeichnungen">
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <zeichnungen log1="zei1">
        <xsl:attribute name="indizieren">1</xsl:attribute>
        <xsl:copy-of select="@*"/>
        <xsl:call-template name="section-titel"></xsl:call-template>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="section"><xsl:apply-templates select="."></xsl:apply-templates></xsl:for-each>
    </zeichnungen>
</xsl:template>

<xsl:template match="section[@data_key='marks']">
<!-- es fehlt noch die liste der markentypen im datenbank-output -->
   <marken>
       <xsl:attribute name="indizieren">1</xsl:attribute>
       <xsl:copy-of select="@*"/>
       <xsl:call-template name="section-titel"></xsl:call-template>
       <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:for-each select="ancestor::book/manage_lists/index[@propertytype='brandtypes']/item">
        <xsl:variable name="typ"><xsl:value-of select="lemma"/></xsl:variable>
        <markentyp>
            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <titel><xsl:value-of select="lemma"/></titel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="ancestor::book/indices/index[@propertytype='brands']/item[@brandtype=$typ]">
                <item>
                    <xsl:copy-of select="@*"/>
                    <xsl:for-each select="*"><xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:for-each>
                    <!--<xsl:apply-templates select="."></xsl:apply-templates>-->
                </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
        </markentyp><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:for-each>
   </marken><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

<xsl:template match="abbildung"><xsl:copy-of select="."/></xsl:template>
<xsl:template match="markentyp"></xsl:template>


<xsl:template match="section[@data_key='maps']">
    <maps log1="maps1"><xsl:copy-of select="@*"/>
        <titel><xsl:value-of select="@name"/></titel>
        <xsl:apply-templates></xsl:apply-templates><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </maps><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

<xsl:template match="section[@data_key='map']">
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <map><xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="items/item/content">
         <content><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <p><xsl:apply-templates select="."></xsl:apply-templates></p>
         </content><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
            <xsl:for-each select="section"><xsl:apply-templates select="."></xsl:apply-templates></xsl:for-each>
    </map>

</xsl:template>

<xsl:template match="section[@data_key='map_concordance']">
    <konkordanz><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:copy-of select="links"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:apply-templates select="items/item/content"></xsl:apply-templates></konkordanz><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>
<xsl:template match="section[@data_key='map_file']">
    <bild><xsl:apply-templates select="items/item/content"></xsl:apply-templates></bild><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

<xsl:template match="section[@data_key='map_title']">
    <bildlegende><xsl:apply-templates select="items/item/content"></xsl:apply-templates></bildlegende><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

<xsl:template name="bildtafeln">
    <!-- aus den textbausteinen des bandes werden die titelseite für den tafelteil
    und die seite mit den bildnachweisen ausgelesen-->
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <bildtafeln indizieren="1">
            <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:call-template name="section-titel"></xsl:call-template>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="section">
                <xsl:choose>
                    <xsl:when test="@data_key='plates_credits'">
                     <bildnachweis>
                         <xsl:copy-of select="@*"/>
                         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="section-titel"></xsl:call-template>
                        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:for-each select="items/item/content">
                                <content><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                    <p><xsl:apply-templates></xsl:apply-templates></p><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                </content><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:for-each>
                     </bildnachweis><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:when>
                    <xsl:otherwise><error>Fehler: Dem Abschnitt &quot;<xsl:value-of select="@name"/>&quot; wurde kein Datenschlüssel zugewiesen. Wählen Sie einen Datenschlüssel aus oder wenden Sie sich an den Administrator!</error></xsl:otherwise>

                </xsl:choose>
            </xsl:for-each>
        </bildtafeln><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

<xsl:template name="section-titel">
    <xsl:choose>
        <xsl:when test="items/item[@itemtype='chapter']/content/wunsch[@tagname='Titel']">
            <titel><xsl:value-of select="items/item[@itemtype='chapter']/content/wunsch[@tagname='Titel']"/></titel>
        </xsl:when>
        <xsl:otherwise><titel><xsl:value-of select="@name"/></titel></xsl:otherwise>
    </xsl:choose>
</xsl:template>


<xsl:template name="di_baende">
    <xsl:param name="baende">../files/di_baende.xls</xsl:param>
    <di_baende indizieren="1">
        <xsl:copy-of select="@*"/>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:call-template name="section-titel"></xsl:call-template>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <table><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="document($baende)//*[name()='Row'][*/*[node()]]">
                <row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <cell><xsl:value-of select="*[1]/*"/></cell><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <cell><xsl:value-of select="*[2]/*"/></cell><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </row><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
        </table><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <!-- hier noch die einzelnen titel einlesen, aus den textbausteinen oder aus einer separaten datei -->
    </di_baende><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

    <!-- zwischenüberschriften in einleitungskapiteln -->
    <xsl:template match="h"><h><xsl:copy-of select="@data-link-value"/><xsl:apply-templates/></h></xsl:template>

    <!-- gesperrt ausgezeichnete wörter in der einleitung -->
    <xsl:template match="g"><xsl:copy-of select="."/></xsl:template>

</xsl:stylesheet>
