<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns:fn="http://www.w3.org/2005/xpath-functions"
    xmlns:php="http://php.net/xsl"
    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
    >

    <xsl:import href="di-trans2-indices.xsl"/>
    <xsl:import href="di-trans2-commons.xsl"/>

<!-- mit diesem stylesheet wird der katalog der inschriftenartikel erzeugt,
     es wird in trans2.xsl und in trans2-book.xsl aufgerufen.
    
     die artikel selbst werden lediglich kopiert und um attribute 
     sowie das element <abbildungen> mit den betreffenden abbildungsnummern im tafelteil ergänzt,
     der artikelaufbau erfolgt erst auf der folgenden transformationsstufe,  
     die durch di-trans3.xsl angestoßen wird.
     
     die artikel werden in diesem stylesheet sortiert und durchnummeriert.
    -->
    <!-- AUSGANGSSITUATION und ZIEL:
    
    aus der ersten transformation ist folgende gliederung der hauptelemente hervorgegangen:
    <book>
        <options/>
        <project/>
        <volume/>
        <articles/>
        <indices/>
        <managa_lists/>
    </book>
    
    daraus soll folgende gliederung der hauptelemente hervorgebracht werden:
    <book>
        <options/>
        <project/>
        <volume/>
        <articles/>
        <indices/>
        <marken/>
    </book>    
    das element <manga_list> entfällt, ein element <marken> zur ergänzung der register wird hinzugefügt.

   ____________________________________________________
   einzelne elemente werden wie folgt erzeugt oder weiter behandelt:
    <options> wird kopiert
    <project> wird kopiert
    <articles> wird mittels templates erzeugt
    <indices> wird mittels templates erzeugt
    <brands> wird mittels template erzeugt

    -->

<!-- ==BEGINN TRANSFORMATION========================================================================= -->  
    <!-- aufbau des bandes --> 
    <xsl:template name="book_articles">
        <!-- dieses template wird in trans2-book.xsl aufgerufen -->

        <!-- element <book> neu anlegen, attribute kopieren -->
        <xsl:copy>
            <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- Job kopieren, damit wir das Datum am Ende haben -->
            <xsl:copy-of select="job"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- abschnitt <options> kopieren -->
            <xsl:copy-of select="options"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- abschnitt <project> kopieren -->
            <project><xsl:copy-of select="project/@*" copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <database><xsl:value-of select="project/@database"/></database><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:copy-of select="project/signature"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:copy-of select="project/name"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <dio_urn_volume><xsl:value-of select="project/description/urn"/></dio_urn_volume><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <di_number data_key="di_number"><xsl:value-of select="project/description/di_number"/></di_number><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!--                <dio_page-id_indexes data_key="dio_page-id_indexes"><xsl:value-of select="project/description/page-id-indexes"/></dio_page-id_indexes>
-->                <xsl:copy-of select="project/description" copy-namespaces="no"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>   
            </project>
<!--            <xsl:copy-of select="project"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>-->
            <!-- abschnitt <types> kopieren -->
            <xsl:copy-of select="types"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- abschnitt <articles> aus template einfügen-->
            <xsl:call-template name="articles-catalog"></xsl:call-template>
             
            <!-- abschnitt <indices> aus template einfügen-->
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="indices">
                <xsl:call-template name="indices"></xsl:call-template>
            </xsl:for-each>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!--abschnitt <marken> aus template einfügen-->
            <xsl:call-template name="brands"></xsl:call-template>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- steuerungslisten kopieren -->
            <xsl:copy-of select="manage_lists"></xsl:copy-of>
        </xsl:copy>  
    </xsl:template>
    
    <!-- katalog der inschriftenartikel erzeugen für die export-pipeline DI-Artikel-->
    <xsl:template name="articles-catalog">
        <!-- dieses template wird nur im aktuellen stylesheet 
             im template book-catalog aufgerufe,
             das attribut @indexing=1 bewirkt, dass der katalogtitel in das 
                inhaltsverzeichnis des inschrifenbandes aufgenommen wird.
        -->
        <articles>
            <xsl:attribute name="indexing">1</xsl:attribute>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- katalogtitel aus den textbausteinen des bandes auswählen -->
            <xsl:for-each select="volume/section[@data_key='articles']">
                <xsl:call-template name="section-title"></xsl:call-template>
            </xsl:for-each>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- artikel ansteuern -->
            <xsl:for-each select="articles/article">
                <!-- artikel kopieren -->
                <xsl:copy>
                    <!-- nummerierung einfügen -->                    
                    <xsl:attribute name="nr"><xsl:choose>
                        <xsl:when test="$sw_sortieren_nach_signatur=1"><xsl:value-of select="replace(@article_signature,'^0+', '')"/></xsl:when>
                        <xsl:otherwise><xsl:number count="article" format="1" from="articles"/></xsl:otherwise>
                    </xsl:choose></xsl:attribute>
                    <!-- vorhandene attribute kopieren -->
                    <xsl:copy-of select="@*"/>
                    <!-- den artikelinhalt kopieren -->
                    <xsl:for-each select="*"><xsl:copy-of select="."/></xsl:for-each>
                </xsl:copy>
            </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>              
        </articles><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- erzeugen des katalogs für die pipeline DI-Band -->
    <xsl:template name="articles"> 
        <!-- wird aufgerufen in trans2-book.xsl -->
        <!-- die folgenden parameter steuern die ausgabe der verweise auf abbildungen in den artikeln-->     
        <xsl:param name="p_abbildungsliste_pfad"><xsl:value-of select="ancestor-or-self::book/job/@folder"/>../../projects/<xsl:value-of select="lower-case(ancestor-or-self::book/project/signature)"/>/plates.xml</xsl:param>
        <xsl:param name="p_abbildungsliste_vorhanden">
            <xsl:choose>
                <xsl:when test="fn:doc-available($p_abbildungsliste_pfad) = true()">1</xsl:when>
                <xsl:otherwise>0</xsl:otherwise>
            </xsl:choose>
        </xsl:param>  
        <xsl:param name="p_abbildungsliste">
            <xsl:call-template name="abbildungsliste">
                <xsl:with-param name="p_abbildungsliste_pfad"><xsl:copy-of select="$p_abbildungsliste_pfad"/></xsl:with-param>
                <xsl:with-param name="p_abbildungsliste_vorhanden"><xsl:copy-of select="$p_abbildungsliste_vorhanden"/></xsl:with-param>
            </xsl:call-template>
        </xsl:param>

        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
        <!-- katalog anlegen, vorhandene attribute kopieren,
             das attribut @indexing=1 bewirkt, dass der katalogtitel in das 
                inhaltsverzeichnis des inschriftenbandes aufgenommen wird -->
        <xsl:element name="{@data_key}">
            <xsl:attribute name="indexing">1</xsl:attribute>
            <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- katalogtitel aus den textbausteinen des bandes erzeugen,
                das template dafür befindet sich in di-trans2-commons.xsl-->
            <xsl:call-template name="section-title"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- artikel auswählen und neu anlegen-->
            <xsl:for-each select="ancestor::book/articles/article">
                <!-- für den fall, dass die signaturen durch die katalognummern ersetzt wurden, 
                die ursprüngliche signatur mit hilfe des projektkürzels identifizieren und aufnehmen -->
                <xsl:variable name="v_project_name"><xsl:value-of select="ancestor::book/project/signature"/></xsl:variable>
                <xsl:variable name="v_signatur"><xsl:value-of select="sections/section[@sectiontype='signatures']/items/item[@itemtype='signatures'][1]/value"/></xsl:variable>
                
                <!-- artikel neu anlegen, vorhandene attribute kopieren, attribut @nr hinzufügen -->
                <xsl:copy>
                    <xsl:attribute name="nr"><xsl:choose>
                        <xsl:when test="$sw_sortieren_nach_signatur=1"><xsl:value-of select="replace(@article_signature,'^0+', '')"/></xsl:when>
                        <xsl:otherwise><xsl:number count="article" format="1" from="articles"/></xsl:otherwise>
                    </xsl:choose></xsl:attribute>                    
                    <xsl:copy-of select="@*"/>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    
                    <!-- parametertest artikel 
<parametertest-artikel><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<projekt><xsl:copy-of select="$p_project_name"/></projekt><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<signatur><xsl:copy-of select="$p_signatur"/></signatur><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<abbildungsliste><xsl:copy-of select="$abbildungsliste"/></abbildungsliste><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</parametertest-artikel><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->
                    
                    <!-- container für referenzen auf abbildungen im tafelteil hinzufügen
                     die abbildungsliste wird im parameter p_abbildungsliste 
                     durch das template name=abbildungsliste (s. u.) aus einer externen tabelle erstellt 
                -->
                    <plates_list>
                        <!--xsl:attribute name="path"><xsl:value-of select="$p_abbildungsliste_pfad"/></xsl:attribute-->
                        <!--xsl:attribute name="exists"><xsl:value-of select="$p_abbildungsliste_vorhanden"/></xsl:attribute-->
                        <!--xsl:attribute name="uri"><xsl:value-of select="replace(translate(document-uri(/), '\', '/'), '[^/]+$','')"/></xsl:attribute-->
                        <xsl:for-each select="$p_abbildungsliste//row">
                            <xsl:if test="cell[1][text()=$v_signatur]">
                                <!-- bindestriche werden durch spiegelstriche ersetzt -->
                                <xsl:value-of select="translate(cell[2],'-','–')"/></xsl:if>
                        </xsl:for-each>
                        <!--  parametertest      
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <liste_vorhanden><xsl:copy-of select="$p_abbildungsliste_vorhanden"/></liste_vorhanden>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <listenpfad><xsl:copy-of select="$p_abbildungsliste_pfad"/></listenpfad>-->
                    </plates_list><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    
                    <!-- den artikelinhalt kopieren -->
                    <!-- Reihenfolge der Abschnitte muss leicht verändert werden: Wappen von oben nach unten direkt über den ersten Kommentar holen. 
                        Damit Markenzählung für Marken in Wappenfußnoten funktioniert, d.h. <rec_ma> in der richtigen Reihenfolge auftritt. 
                    -->
                    <!--xsl:for-each select="*"><xsl:copy-of select="."/></xsl:for-each-->
                
                    <!-- process child elements of article -->
                    <xsl:for-each select="*">
                        <xsl:choose>
                            <!-- special handling for <sections> -->
                            <xsl:when test="self::sections">
                                <xsl:copy>
                                    <!-- copy attributes of <sections> -->
                                    <xsl:copy-of select="@*"/>
                                    
                                    <!-- get the first heraldry and comment sections -->
                                    <xsl:variable name="heraldry" select="(section[@sectiontype='heraldry'])[1]"/>                                   
                                    <xsl:variable name="di_comment" select="(section[@sectiontype='text'][@norm_iri='di_comment'])[1]"/>
                                                                        
                                    <!-- iterate through all sections -->
                                    <xsl:for-each select="section">
                                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        <xsl:choose>
                                            <!-- when we reach the first di_comment, insert heraldry BEFORE it (if heraldry exists) -->
                                            <xsl:when test="$di_comment and $heraldry and (. is $di_comment)">
                                                    <xsl:copy-of select="$heraldry"/>
                                                    <xsl:copy-of select="."/>
                                            </xsl:when>
                                                                                       
                                            <!-- skip the first heraldry to avoid duplicate output -->
                                            <!-- copy the rest -->
                                            <xsl:when test="$di_comment and $heraldry and not(. is $heraldry)"><xsl:copy-of select="."/></xsl:when>                                            
                                            <xsl:when test="not($di_comment) or not($heraldry)"><xsl:copy-of select="."/></xsl:when>

                                        </xsl:choose>
                                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    </xsl:for-each>
                                </xsl:copy>
                            </xsl:when>
                            
                            <!-- all other elements copied as-is -->
                            <xsl:otherwise>
                                <xsl:copy-of select="."/>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>
                </xsl:copy>
            </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>              
                    
            
        </xsl:element>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- für den parameter p_abbildungsliste: 
            tabelle der abbildungen des tafelteils, die in den artikeln referenziert werden  -->
    <xsl:template name="abbildungsliste">
        <xsl:param name="p_abbildungsliste_vorhanden"></xsl:param>
        <xsl:param name="p_abbildungsliste_pfad"></xsl:param>
        
        <!--  parametertest      
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <liste_vorhanden><xsl:copy-of select="$p_abbildungsliste_vorhanden"/></liste_vorhanden>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <listenpfad><xsl:copy-of select="$p_abbildungsliste_pfad"/></listenpfad>-->

        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
        <xsl:if test="$p_abbildungsliste_vorhanden='1'">
            <!-- tabelle anlegen -->
            <table>
                <!-- externe tabelle ansteuern-->
                <xsl:for-each select="document($p_abbildungsliste_pfad)//*[name()='Row'][*/*[node()]]">
                    <!-- in der dritten zelle muss es ein data-element mit textknoten geben
                         und die dritte zelle (oder eine andere) darf nicht dass attribut ss:Index enthalten, 
                         weil es dadurch zur maskierten auslöschung der dritten zelle und zur verschiebung nach links der folgenden kommt
                    -->
                    <xsl:if test="*[3]/*[node()] and not(*[@ss:Index])">
                        <!-- tabellenzeile anlegen, aus der zweiten und dritten spalte der externen tabelle
                        neue spalten (zellen, element <cell>) erzeugen-->
                        <row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <cell><xsl:value-of select="*[2]/*"/></cell><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <cell><xsl:value-of select="*[3]/*"/></cell><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:if>
                </xsl:for-each>
            </table>   
        </xsl:if>
    </xsl:template>
</xsl:stylesheet>
