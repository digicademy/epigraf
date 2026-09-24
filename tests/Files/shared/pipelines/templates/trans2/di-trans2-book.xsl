<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns:fn="http://www.w3.org/2005/xpath-functions"
    xmlns:php="http://php.net/xsl"
    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
    >
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans2-commons.xsl"/>

    <xsl:import href="di-trans2-preliminaries.xsl"/>
    <xsl:import href="di-trans2-table_of_content.xsl"/>
    <xsl:import href="di-trans2-prefaces.xsl"/>
    <xsl:import href="di-trans2-introduction.xsl"/>
    <xsl:import href="di-trans2-articles.xsl"/>
    <xsl:import href="di-trans2-table_of_inscriptions.xsl"/> 
    <xsl:import href="di-trans2-abbreviations.xsl"/>
    <xsl:import href="di-trans2-indices.xsl"/>
    <xsl:import href="di-trans2-di_volumes.xsl"/>
    <xsl:import href="di-trans2-plates.xsl"/>
    <xsl:import href="di-trans2-drawings.xsl"/>
    <xsl:import href="di-trans2-brands.xsl"/>
    <xsl:import href="di-trans2-maps.xsl"/>


    <!-- mit diesem stylesheet wird auf der zweiten transformationsstufe 
         der komplette di-inschriftenband für die pipeline DI-Band erzeugt;
        
         es wird aufgerufen in di-trans2.xsl.
    -->
    
    <!-- ================================================================== -->
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
        <preliminaries/>
        <prefaces/>
        <tabel_of_content/>
        <introduction/>
        <articles/>
        <table_of_inscriptions/>
        <indices/>
        <abbreviations/>
        <di-baende/>
        <zeichnungen/>
        <bildtafeln/>
        <footnotes/>
        <links/>
    </book>
   ____________________________________________________
   einzelne elemente werden wie folgt erzeugt oder weiter behandelt:
   
    <options> wird kopiert
    <project> wird um weitere angaben ergänzt
    
    zur bildung der folgenden elemente werden die elemente 
    <section> des elements <volume> angesteuert und an
    templates verwiesen, die sich in je einem 
    eigenen stylesheet befinden
    
    zum schluss werden aus <volume> die elemente
    <footnotes> und 
    <links> kopiert
    -->

<!-- ==BEGINN TRANSFORMATION========================================================================= -->      

    <!-- aufbau des inschriftenbandes / anlegen der grundstruktur -->
    <xsl:template name="book_volume">
        <!-- element <book> neu anlegen -->
        <xsl:copy>
            <!-- attribute übernehmen -->
            <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  
            <!-- Job kopieren, damit wir das Datum am Ende haben -->
            <xsl:copy-of select="job"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- liste der optionen für die bandausgabe kopieren -->
            <xsl:copy-of select="options"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- abschnitt <types> kopieren -->
            <xsl:copy-of select="types"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="properties"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- parametertest abbildungen 
<xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<parametertest-abbildungen><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<abbildungsliste-vorhanden><xsl:copy-of select="$abbildungsliste_vorhanden"></xsl:copy-of></abbildungsliste-vorhanden><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<abbildungsliste_pfad><xsl:copy-of select="$abbildungsliste_pfad"></xsl:copy-of></abbildungsliste_pfad><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<abbildungsliste><xsl:copy-of select="$abbildungsliste"/></abbildungsliste><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</parametertest-abbildungen><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->
            
            <!-- container für projektangaben neu anlegen,
            attribute aus der vorigen transformation (quelldokument) übernehmen,
            element <database> erzeugen und den wert des entsprechend attributs einfügen,
            projektsignatur als element einfügen,
            projektname als element einfügen,
            dio-signatur als element einfügen,
            di-nummer des bandes als element einfügen, das element mit dem attribut @data_key versehen
            -->
            <project><xsl:copy-of select="project/@*" copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <database><xsl:value-of select="project/@database"/></database><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:copy-of select="project/signature"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:copy-of select="project/name"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <dio_urn_volume><xsl:value-of select="project/description/urn"/></dio_urn_volume><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <di_number data_key="di_number"><xsl:value-of select="project/description/di_number"/></di_number><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<!--                <dio_page-id_indexes data_key="dio_page-id_indexes"><xsl:value-of select="project/description/page-id-indexes"/></dio_page-id_indexes> -->
                <xsl:copy-of select="project/description" copy-namespaces="no"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <book_urn><xsl:value-of select="volume/norm_data"/></book_urn>                
            </project> 
            
            
            
            <!-- die hauptabschnitte/<section> des bandes werden in der gegebenen reihenfolge einzeln angesteuert -->
            <xsl:for-each select="volume/section">
                <!-- das attribut @data_key beim aufrufen jeder <section> in einer variablen ablegen -->
                <xsl:variable name="v_data_key"><xsl:value-of select="@data_key"/></xsl:variable>
                <xsl:comment>book_section</xsl:comment>

                <!-- anhand der daten-schlüssel (data_key) werden den einzelnen <section> je eigene templates zugewiesen,
                        gegebenenfalls nur wenn die ausgabe in den optionen beim aufrufen der pipeline aktiviert wurde-->
                <xsl:choose>
                    <xsl:when test="@sectiontype='outputoptions'"><!--könnte benötigt werden, wenn das erste Notizfeld des Bandes ausgegeben werden soll--></xsl:when>
                    <xsl:when test="$v_data_key='dio_signature'"><!-- entfällt --></xsl:when>
                    <xsl:when test="$v_data_key='di_number'"><!-- entfällt --></xsl:when>  
                    
                    <!-- die einzelnen abteilungen des bandes werden angesteuert 
                         und an templates verwiesen -->

                    <!-- titelei -->
                    <xsl:when test="$v_data_key='preliminaries'">
                        <xsl:if test="$sw_titelei=1"><xsl:call-template name="preliminaries"></xsl:call-template></xsl:if>
                    </xsl:when>
                    
                    <!-- inhaltsverzeichnis -->
                    <xsl:when test="$v_data_key='table_of_content'">
                        <xsl:if test="$sw_inhalt=1"><xsl:call-template name="table_of_content"></xsl:call-template></xsl:if>
                    </xsl:when>
                    
                    <!-- vorworte -->
                    <xsl:when test="@data_key='prefaces'">
                        <xsl:if test="$sw_vorwort='1'"><xsl:call-template name="prefaces"></xsl:call-template></xsl:if>
                    </xsl:when>
                    
                    <!-- einleitung -->
                    <xsl:when test="@data_key='introduction'">
                        <xsl:if test="$sw_einleitung='1'"><xsl:call-template name="introduction"></xsl:call-template></xsl:if>
                    </xsl:when>
                    
                    <!-- katalog der inschriftenartikel -->
                    <xsl:when test="$v_data_key='articles'">
                        <xsl:call-template name="articles">
                            <!-- das hier aufgerufene template befindet sich in der datei trans2-articles.xsl -->
                        </xsl:call-template>
                    </xsl:when>
                    
                    <!-- chronologisch sortierte liste der inschriften -->
                    <xsl:when test="$v_data_key='table_of_inscriptions'">
                        <xsl:if test="$sw_inschriftenliste='1'"><xsl:call-template name="table_of_inscriptions"></xsl:call-template></xsl:if>
                    </xsl:when>
                    
                    <!-- abkuerzungen -->
                    <xsl:when test="$v_data_key='abbreviations'">
                        <xsl:if test="$sw_abkuerzungen='1'"><xsl:call-template name="abbreviations"></xsl:call-template></xsl:if>
                    </xsl:when>
                    
                    <!-- register -->
                    <xsl:when test="$v_data_key='indices'">
                        <!-- das hier aufgerufene template befindet sich in der datei trans2-indices.xsl -->
                        <xsl:call-template name="indices"></xsl:call-template>
                    </xsl:when>
                    
                    <!-- literatur und quellen -->
                    <xsl:when test="$v_data_key='biblio'">
                        <!-- das hier aufgerufene template befindet sich in der datei trans2-index-literature.xsl -->
                        <xsl:if test="$sw_quellen-literatur='1'"><xsl:call-template name="biblio"></xsl:call-template></xsl:if>
                    </xsl:when>
                    
                    <!-- liste der bisher erschienenen DI-Bände -->
                    <xsl:when test="$v_data_key='di_volumes'">
                        <xsl:if test="$sw_di-bände='1'"><xsl:call-template name="di_volumes"></xsl:call-template></xsl:if>
                    </xsl:when>
                    
                    <!-- zeichnungen -->
                    <xsl:when test="$v_data_key='drawings'">
                        <xsl:call-template name="drawings"></xsl:call-template>
<!--                        <xsl:if test="$sw_zeichnungen='1' or
                            $sw_marken='1' or
                            $sw_grundrisse='1'">
                            <xsl:call-template name="drawings"></xsl:call-template>
                        </xsl:if>-->
                    </xsl:when>
                    
                    <!-- bildtafeln -->
                    <xsl:when test="$v_data_key='plates'">
                        <xsl:if test="$sw_bildtafeln='1'"><xsl:call-template name="plates"></xsl:call-template></xsl:if>
                    </xsl:when>
                    
                    <!-- münchener reihe: tabellarische übersicht der standorte -->
                    <xsl:when test="$v_data_key='di_locations_table'">
                        <xsl:call-template name="index_locations_table"/>
                    </xsl:when>
                    
                    <!-- Wappenbeschreibungen als Anhang (siehe auch di-trans2-indices, dort Wappenbeschreibungen als Register) -->
                    <xsl:when test="$v_data_key='blazons'">
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <blazons>
                            <xsl:copy-of select="@*" copy-namespaces="no"/>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:for-each select="ancestor::book/indices/index[@propertytype='heraldry']">
                                <xsl:call-template name="register-blasonierungen" />
                            </xsl:for-each>
                        </blazons>
                    </xsl:when>                    

                    <!-- prüfroutine auf sections ohne gültigen datenschlüssel -->
                    <xsl:otherwise>
                        <error>Fehler: Dem Abschnitt &quot;<xsl:value-of select="@name"/>&quot; wurde kein gültiger Datenschlüssel zugewiesen. Wählen Sie einen Datenschlüssel aus oder wenden Sie sich an den Administrator!</error>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- fußnoten der einleitung kopieren (zur bearbeitung in späteren transformationen) -->
            <xsl:copy-of select="volume/footnotes"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- links kopieren (zur verwendung in späteren transformationen)-->
            <xsl:copy-of select="volume/links"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- steuerungslisten kopieren -->
            <xsl:copy-of select="manage_lists"></xsl:copy-of>
        </xsl:copy>
    </xsl:template>

</xsl:stylesheet>