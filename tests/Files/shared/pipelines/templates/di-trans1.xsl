<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
 
<xsl:import href="commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="trans1/di-trans1-volume.xsl"/>
    <xsl:import href="trans1/di-trans1-articles.xsl"/>
    <xsl:import href="trans1/di-trans1-indices.xsl"/>
    <xsl:import href="trans1/di-trans1-commons.xsl"/>
<xsl:output indent="no"/>  

<!-- dieses stylesheet stößt die erste transformationsstufe der di-daten an.
        es ist auf die xml-datei anzuwenden, die den rohdatenexport 
        einer di-datenbank aus epigraf enthält.
        
    die rohdaten sind mehr oder minder in der tabellenstruktur der datenbank gehalten;
    ziel der transformation über alle stufen hinweg ist es, daraus ein layout 
    für den druck und für die anzeige am bildschirm zu formen.
    
    auf das transformationsergebnis dieses stylesheets sind nacheinander zwei weitere
    transformationen (transformationsstufen) anzuwenden:
    stufe 2: di-trans2.xsl
    stufe 3: di-trans3.csl
    
    nach der dritten transformation sind die daten so angeordnet und strukturiert,
    dass sie in das für di-bände vorgegebene layout ohne weitere umstellungen 
    der grundstruktur überführt werden können.
    
    aus dem ergebnis der dritten transformationsstufe können in einer vierten stufe
    dateien unterschiedlicher formate für je spezifische publikationsformen 
    erzeugt werden:
    .docx (word-dokument)
    .odt  (libre-office-dokument)
    .xml für dio
    
    um ein workdokument zu erzeugen, ist auf der vierten transformationsstufe 
    das stylesheet 
        di-trans-doc1.xsl 
    zu verwenden,
    damit auch abbildungen z. b. von hausmarken angezeigt werden, ist das ergebnis 
    anschließend noch mit 
        di-trans-doc2.xsl 
    zu transformieren.
    
    zum erzeugen einer xml-datei für den export in die dio-datenbank,
    wird für die vierte transformationsstufe 
        di-trans-dio.xsl 
    benötigt.
    
    für die ausgabe der daten in ein libreoffic-dokument ist 
        di-trans-odt.xsl 
    zu verwenden.
    -->

    <!-- ===================================================================================
        AUSGANGSSITUATION:
        über den rohdatenexport erhält man eine xml-datei mit dem wurzelelement <book>.
        es enthält die child-elemente: 
            <options>
            <types>
            <job>
            <project>
            <article>
            <index>
        
        <options> enthält die ausgabeoptionen der export-pipeline.
        
        <types> enthält die child-elemente <type>. 
        jedes element <type> repräsentiert eines der anderen in epigraf verwendeten
        arten von elementen (artikelabschnitte, text- und transkriptionswerkzeuge, register usw.)
        jeweils mit allen spezifikationen.
        
        <job> enhält angaben zum gerade angestoßenen datenexport
        
        <projekt> enhält den namen der datenbank sowie die bezeichnung und das kürzel des projekts, 
        zu dem die exportierten daten resp. katalogartikel gehören.
        
        <article> kann mehrfach vorkommen, je nachdem, wie viele 
        inschriftenartikel für den export ausgewählt wurden.
        
        das erste element <article> besitzt das attribut @type="epi-book". es gibt den sogenannten 
        bandartikel wieder, der die daten für all das, was nicht zu den kataolgartikeln 
        und zu den register gehört, enthält: titelei, vorwort, einleitung usw.
        
        alle weiteren elemente <article> haben dass attribut @type="epi-article".
        jedes einzelen repräsentiert einen inschriftenartikel für den katalog der inschriften
        
       <index> kommt ebenfalls mehrfach vor. jedes einzelne enthält die daten für eines 
        der im projekt verwendeten listen, die auf der benutzeroberfläche von Epigraf als 
        kategorien bezeichnet werden. Bei diesen listen handelt es sich zum einen um solche, 
        die als register ausgegeben werden, zum anderen um listen mit steuerungsbefehlen (steuerungslisten).
        
    -->

    <!-- norm_iri der über dropdown benannten sections in den articles:
        (name                                   = norm_iri)
        Allgemeine Angaben: Buchstabenhöhe      = di_generals_fontsize
        Allgemeine Angaben: Ergänzungen nach    = di_generals_addition
        Allgemeine Angaben: Inschriften nach    = di_generals_source
        Allgemeine Angaben: Maße                = di_generals_measure
        Allgemeine Angaben: Schriftarten        = di_generals_fonttype
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
    -->

<!--    
   ====================================================================
   funktionen dieses und der damit assozierten stylesheets:
   
   aus den elementen des rohdatenexports werden im wurzelelement <book> neue hauptelemente gebildet:
   <book>
        <options/> wird kopiert
        <project/> wird kopiert
        <volume/> wird aus dem element <article type="epi-book"> gebildet
        <articles/> sammelt die elemente <article type="epi-article">
        
             die elemente <index> werden aufgeteilt und gruppiert in: 
             
        <indices/> register
        <managa_lists/> steuerungslisten
   </book>
   ____________________________________________________
   einzelne elemente werden wie folgt weiter behandelt:
        die <section> in <volume> werden hierarchisch verschachtelt
        
        die elemente <section sectiontype="chapter"> in <volume> werden um die 
        attribute @kategorie und @data_key ergänzt.    

        in <articles> werden die elemente <article> nach vier kriterien (datierung, standort, träger, signatur) sortiert.
  
        die register und steuerungslisten werden in trans1-indices.xsl transformiert: 
            aus den elementen <property> werden elemente
            <item> erzeugt und hierarchich verschachtelt (==> trans1-indices-commons.xsl), 
            jedes <item> wird mit den attributen @gruppierung und @ausblenden versehen,
            querverweise werden in ein element <crossRef> transformiert und entsprechende lemmata 
            in hierarchischer folge eingefügt.
    
            im standorteregister werden die verweisziele <section> in <sections> um die 
            attribute @before und @lost ergänzt (==> trans1-index-locations.xsl).
            
            im texttypenregister werden die verweisziele <section> in <sections> um die 
            attribute @metres und @language ergänzt (==> trans1-index-texttypes.xsl).
            
            die elemente <lemma> werden mit den attributen @sortchart und @sortstring versehen,  
            die geschweiften klammern werden aus den text()-knoten  herausgefiltert (trans1-indices-commons.xsl).

-->
    
    <!-- 
        GILT FÜR ALLE STYLESHEETS: das attribut @log1 dient dazu, 
        transformationen bei gegebenenfalls erforderlicher fehlersuche 
        im transformationsergebnis nachzuvollziehen,
        man kann damit erkennen, welcher befehl an welcher stelle wirksam wurde
        auf den folgenden transformationsstufen lautet es:
         @log2
         @log3.
    -->

<!-- ==BEGINN TRANSFORMATION========================================================================= -->    

<!-- generelle parameter des projects zur verwendung an jeder belieben stelle des codes -->
    <xsl:param name="p_project-name">
        <xsl:value-of select="/book/project/name"/>
    </xsl:param>
    <xsl:param name="p_project-signature">
        <xsl:value-of select="/book/project/signature"/>
    </xsl:param>
    <xsl:param name="p_project-database">
        <xsl:value-of select="/book/project/@database"/>
    </xsl:param>


<!-- der wurzelknoten wird angesteuert und an das nachfolgende template verwiesen -->
    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>
    
    <!-- das gesamte document wird in dem parameter p_book neu angelegt und allen elementen 
            wird aus den types das attribut norm_iri zugespielt 
    außerden:
                einzelnen elementen werden spezifikationen aus den 
                links/properties oder den indexes/properties
    -->
    <xsl:template name="takeall">
        <xsl:for-each select="node()">
            <xsl:variable name="v_name"><xsl:value-of select="name()"/></xsl:variable>
            <xsl:choose>
                <xsl:when test="self::text()">
                    <!-- textkonten einfügen -->
                    <xsl:value-of select="."/>
                </xsl:when>
                <xsl:when test="self::z">
                    <!-- das elenetn z für zeilenanchluss wird um das attribut unit ergänzt;
                    das attribut @unit enthält das zeichen resp. symbol für die 
                    wiedergabe des zeilenschluss, als slasch, doppelslach o. ä.-->
                    
                    <!-- element z mit attributen kopieren -->
                    <xsl:copy>
                        <xsl:copy-of select="@*" />
                        <!-- attribut @unit anlegen -->
                        <xsl:attribute name="unit">
                            <!-- links ansteuern und den attributwert auslesen  -->
                            <xsl:value-of select="ancestor::book/index[@propertytype='linebindings']//property[@id=current()/@data-link-target]/unit"/>
                        </xsl:attribute>
                    </xsl:copy>
                </xsl:when>
                <xsl:otherwise>
                    <!-- elemente mit den attributen kopieren -->
                    <xsl:copy>
                        <xsl:copy-of select="@*"></xsl:copy-of>
                        <!-- attribut @norm_iri anfügen -->
                        <xsl:copy-of select="ancestor::book/types/type[name=$v_name]/@norm_iri"></xsl:copy-of>
                        <!-- unterknotenten ansteuern und rekursiv an dasselbe template verweisen -->
                        <xsl:call-template name="takeall"></xsl:call-template>
                    </xsl:copy>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>    


<!-- das wurzelelement wird aufgerufen, 
     die haupteinheiten project, articles, indices werden angelegt -->
    

    <xsl:template match="book">
       <xsl:param name="p_book">
           <book>
                <!-- dazu das template takeall aufrufen -->
                <xsl:call-template name="takeall"></xsl:call-template>
           </book>
       </xsl:param>

        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
        
    <!-- das element book wird neu angelegt, 
            die vorhandenen attribute werden übernommen,
            -->
        <book>

            <!-- vorhandene attribute kopieren -->
            <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
<!--<parametertest>
    <xsl:copy-of select="$p_book"></xsl:copy-of>
</parametertest>-->
            
            <xsl:for-each select="$p_book/book">
            <!-- die drei folgenden elemente (im weitern für steuerung und filterung benötigt) kopieren -->
            <xsl:copy-of select="options"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="job"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="article[1]/project"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="types[type]">
                <xsl:copy-of select="."></xsl:copy-of>
            </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- typ-spezifikationen für die spätere verwendung /übernehmen/speichern -->
            <!--<types><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-\- aus dem <type>-element der literaturliste die element <comma> auslesen -\->   
                <xsl:for-each select="types/type[@norm_iri='literature']">
                    <xsl:copy><xsl:copy-of select="@*"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:for-each select=".//comma">
                            <xsl:copy-of select="."></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:for-each>
                    </xsl:copy>
                </xsl:for-each>
                
                <!-\- in diesem types-container können noch weitere typ-spezifikationen 
                     für andere elemente (items) und listen (properties)
                     eingefügt/übernommen werden
                
                -\->
            </types>--><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="properties"></xsl:copy-of>
    
    <!-- parametertest 
    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    
    <param_projectname><xsl:copy-of select="$p_project-name"></xsl:copy-of></param_projectname>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <param_signature><xsl:copy-of select="$p_project-signature"></xsl:copy-of></param_signature>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <param_db><xsl:copy-of select="$p_project-database"></xsl:copy-of></param_db>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    -->
    
        <!-- die abteilung volume (bandartikel) einfügen  -->
        <xsl:call-template name="volume">
            <xsl:with-param name="p_project-name"><xsl:value-of select="$p_project-name"/></xsl:with-param>
            <xsl:with-param name="p_project-signature"><xsl:value-of select="$p_project-signature"/></xsl:with-param>
            <xsl:with-param name="p_project-database"><xsl:value-of select="$p_project-database"/></xsl:with-param>
        </xsl:call-template>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    
        <!-- die abteilung katalog einfügen -->
        <xsl:call-template name="articles">
            <xsl:with-param name="p_project-name"><xsl:value-of select="$p_project-name"/></xsl:with-param>
            <xsl:with-param name="p_project-signature"><xsl:value-of select="$p_project-signature"/></xsl:with-param>
            <xsl:with-param name="p_project-database"><xsl:value-of select="$p_project-database"/></xsl:with-param>
        </xsl:call-template>        
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
        <!-- die abteilung indices einfügen und in register und steuerungslisten aufteilen -->
        <xsl:call-template name="indices">
            <xsl:with-param name="p_project-name"><xsl:value-of select="$p_project-name"/></xsl:with-param>
            <xsl:with-param name="p_project-signature"><xsl:value-of select="$p_project-signature"/></xsl:with-param>
            <xsl:with-param name="p_project-database"><xsl:value-of select="$p_project-database"/></xsl:with-param>
        </xsl:call-template>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text> 
            </xsl:for-each>
        </book>
    </xsl:template>

</xsl:stylesheet>