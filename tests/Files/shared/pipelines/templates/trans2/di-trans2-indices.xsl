<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
>
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans2-commons.xsl"/>
    <xsl:import href="di-trans2-indices-commons.xsl"/>
    <xsl:import href="di-trans2-index-heraldry.xsl"/>
    <xsl:import href="di-trans2-index-literature.xsl"/>
    <xsl:import href="di-trans2-index-brands.xsl"/>
    <xsl:import href="di-trans2-index-texttypes.xsl"/>
    <xsl:import href="di-trans2-index-locations.xsl"/>


<!-- in diesem stylesheet werden die register und das literaturverzeichnis weiter strukturiert,
     es wird im stylesheet di-trans2-book.xsl di-trans2-articles.xsl aufgerufen-->
    
    <!-- 
        der weitere transformationsweg der register verzweigt sich hier:
            a) stichwort BANDREGISTER: wenn in den textbausteinen des bandes die nummern und titel der register, 
                vorbemerkungen und die registergruppen und das implementieren/incorporieren 
                von teilregistern angelegt sind, werden sie von dort ausgelesen;
                die dort angelegte reihenfolge wird beibehalten.
            b) stichwort KATALOGREGISTER: anderenfalls werden sie den mitgegebenen attributen @snr und @title entnommen.
                (diese attribute wurden in der ersten transformation aus den type-angaben der register erzeugt),
                die reihenfolge richtet sich nach dem attribut @snr, 
                dessen attributwert zu diesem zweck in einen numerischen und eine alphabetischen 
                teil gesplittet werden muss.
    -->
<!-- ============================================================================================= -->  
    <!-- template zur transformation der register, wird aufgerufen in:
            di-trans2-book.xsl
            di-trans2-articles.xsl
    -->
    <xsl:template name="indices">


        <!-- ================================================ -->        
 
        <!-- abschnitt (element <indices>) neu anlegen, vorhandene attribute kopieren,
                das attribut @indexing="1" hinzufügen, damit der abschnitt im 
                inhaltsverzeichnis des band gelistet wird -->
        <indices log2="ind1">
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="indexing">1</xsl:attribute>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- der transformationsweg verzweigt sich: -->
            <xsl:choose>
                <!--a)  wenn die registerfolge und -struktur im band angelegt ist, 
                wird sie dort angesteuert und in einem eigenen template abgearbeitet -->
                <xsl:when test="ancestor::book/volume/section[@data_key='indices']">
                    <xsl:call-template name="bandregister"></xsl:call-template>
                </xsl:when>
                
                <!-- b) wenn die registerfolge und -struktur nur mit dem katalog ausgegeben werden soll, 
                    wird sie über das hier aufgerufene template aus dem parameter p_index-sort2 erzeugt -->
                <xsl:otherwise>
                    <xsl:call-template name="katalogregister"/>
                </xsl:otherwise>
            </xsl:choose>
        </indices>
    </xsl:template> 

    <!-- =========================================================================================================== -->
    <!-- stichwort KATALOGREGISTER: 
            zusammenstellung der register nur für den inschriftenkatalog (nicht für den gesamten band) -->

    <xsl:template name="katalogregister">
        <!-- wird aufgerufen in name=indices,
                der fokus liegt auf book/indices -->

        <!-- mit den folgenden beiden paramtern werden die register sortiert-->
        <xsl:param name="p_index-sort1">
            <!-- 
            in diesem parameter wird die laufnummer des registers (@snr) aufgeteilt 
            in einen numerischen (@sort1) und einen alfabetischen (@sort2) teil,
            zu diesem zweck wird das benannte template "index-sort" aufgerufen.
        -->
            
            <!-- jeden <index> ansteuern -->
            <xsl:for-each select="index">
                <!-- element neu anlegen, attribute kopieren -->
                <index><xsl:copy-of select="@*"></xsl:copy-of>
                    <!-- template zum einfügen der attribute @sort1 und @sort2 aufrufen -->
                    <xsl:call-template name="index-sort"></xsl:call-template>
                    <!-- den weiteren inhalt ansteuern und kopieren -->
                    <xsl:for-each select="*"><xsl:copy-of select="." copy-namespaces="no"></xsl:copy-of></xsl:for-each>
                </index><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </xsl:param>
        
        <!-- der inhalt von p_index-sort1 wird sortiert-->        
        <xsl:param name="p_index-sort2">
            <xsl:perform-sort select="$p_index-sort1/index">
                <!-- erstes sortierkriterium: der numerische teil der laufnummer -->
                <xsl:sort select="@sort1" order="ascending" data-type="number"/>
                <!-- zweites sortierkriterium: der alfabetische teil der laufnummer -->
                <xsl:sort select="@sort2" order="ascending" lang="de" case-order="upper-first" />
            </xsl:perform-sort>
        </xsl:param> 
        
        <!-- parametertest
        <test-para-sort1><xsl:copy-of select="$p_index-sort1"></xsl:copy-of></test-para-sort1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        -->
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:choose>
            <!-- in den optionen prüfen, ob die register ausgegeben werden sollen -->
            <xsl:when test="$sw_register=1">
                <!-- die register einzeln aufrufen und an das template match=index verweisen -->
                <!-- 1. zuerst die nummerierten register ohne literaturverzeichnis -->
                <xsl:for-each select="$p_index-sort2/index[@snr[string()]]">
                    <xsl:if test="not(@propertytype='literature')">
                        <xsl:apply-templates select="."></xsl:apply-templates><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:if>                </xsl:for-each>
                <!-- 2. dann die nicht nummerierten register ohne literaturverzeichnis -->
                <xsl:for-each select="$p_index-sort2/index[not(@snr[string()])]">
                    <xsl:if test="not(@propertytype='literature')">
                        <xsl:apply-templates select="."></xsl:apply-templates><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:if>
                </xsl:for-each>
                <!-- 3. zum schluss das literaturverzeichnis -->
                <xsl:apply-templates select="$p_index-sort2/index[@propertytype='literature']"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:when>
            <!-- wenn gemäß den optionen die register nicht ausgegeben werden sollen, werden dennoch 
                            drei für die nachfolgenden transformationsschritte notwendigen indices mitgegeben -->
            <xsl:otherwise>
                <xsl:apply-templates select="index[@propertytype='locations']" />
                <xsl:apply-templates select="index[@propertytype='fonttypes']" />
                <xsl:apply-templates select="index[@propertytype='heraldry']" />
            </xsl:otherwise>
        </xsl:choose>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    
   
   <!-- vorbereitung der sortierung für das katalogregister
        wird aufgerufe in p_index-sort1 -->
    <xsl:template name="index-sort">
        <!-- aus dem attribut @snr (laufnummer) eines jeden <index> wird der numerische und der alfabetische teil 
         extrahiert und jeweils einem eigenen attribut (@sort1, @sort2) angewiesen; 
         @sort1 bekommt den numerischen wert,
         @sort2 den alfabetischen wert zugewiesen
         
         bei dem alfabetischen wert handelt es sich jeweis nur um einen einzigen buchstaben,
         die differenzierung erfolgt buchstabenweise
    -->
        <xsl:choose>
            <xsl:when test="@snr[string()]">
                <xsl:choose>
                    <xsl:when test="contains(@snr,'a')">
                        <xsl:attribute name="sort1"><xsl:value-of select="substring-before(@snr,'a')"/></xsl:attribute>
                        <xsl:attribute name="sort2">a</xsl:attribute>
                    </xsl:when>
                    <xsl:when test="contains(@snr,'b')">
                        <xsl:attribute name="sort1"><xsl:value-of select="substring-before(@snr,'b')"/></xsl:attribute>
                        <xsl:attribute name="sort2">b</xsl:attribute>
                    </xsl:when>
                    <xsl:when test="contains(@snr,'c')">
                        <xsl:attribute name="sort1"><xsl:value-of select="substring-before(@snr,'c')"/></xsl:attribute>
                        <xsl:attribute name="sort2">c</xsl:attribute>
                    </xsl:when>
                    <xsl:when test="contains(@snr,'d')">
                        <xsl:attribute name="sort1"><xsl:value-of select="substring-before(@snr,'d')"/></xsl:attribute>
                        <xsl:attribute name="sort2">d</xsl:attribute>
                    </xsl:when>
                    <xsl:when test="contains(@snr,'e')">
                        <xsl:attribute name="sort1"><xsl:value-of select="substring-before(@snr,'e')"/></xsl:attribute>
                        <xsl:attribute name="sort2">e</xsl:attribute>
                    </xsl:when>
                    <xsl:when test="contains(@snr,'f')">
                        <xsl:attribute name="sort1"><xsl:value-of select="substring-before(@snr,'f')"/></xsl:attribute>
                        <xsl:attribute name="sort2">f</xsl:attribute>
                    </xsl:when>
                    <!-- wenn die laufnummer (@snr) keinen buchstaben enthält, 
                     wird der gesamte attributwert dem attribut @sort1 zugewiesen,
                     @sort2 bleibt leer-->
                    <xsl:otherwise>
                        <xsl:attribute name="sort1"><xsl:value-of select="@snr"/></xsl:attribute>
                        <xsl:attribute name="sort2"></xsl:attribute>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
        </xsl:choose>
    </xsl:template>

    <!-- ==================================================================================================== -->
    <!-- stichwort BANDREGISTER: das folgende template bildet zunächst die im  
        bandartikel angelegte reihenfolge, die zuordnungen 
        und die bezeichnungen der register nach; 
        anschließend werden die betreffenden daten aus den <index>-abschnitten zugeführt.  -->
    <xsl:template name="bandregister">
        <!-- wird aufgerufen in name=indices, der fokus liegt auf book/volume/section[@data_key=indices] -->
        
        <!-- für das standorte-register wird der basisstandort des editionsprojekts aus dem projektnamen ausgelesen 
            und zur späteren verwendung in einem parameter abgelegt; 
            der basisstandort ist die bezeichnung des erfassungsgebiets eines inschriftenprojekts;
            er ist nur relevant, wenn das erfassungsgebiet eine stadt ist und 
            wird für die kumulierung der register mehrerer inschriftenbände benötigt -->
        <xsl:param name="p_basisstandort"><xsl:value-of select="ancestor::book/project/name"/></xsl:param>

            <xsl:comment>ausgabe über template bandregister</xsl:comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- die überschrift des gesamtregisters wird ausgegeben -->
            <xsl:call-template name="section-title"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- die allgemeinen anmerkungen zu den registern werden, wenn vorhanden, ausgelesen -->
            <note><xsl:apply-templates select="items/item[@itemtype='chapter']/content"/></note><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- die einzelnen register-abschnitte ansteuern -->
            <xsl:for-each select="section">
                <!-- für die zuordnung des registerbaums aus dem entsprechenden registers wird der 
                    erforderliche key in eine variable gegeben; 
                    in  weiteren variablen wird die bezeichnung des registers gespeichert;
                    aus der bezeichnung werden die laufnummer und der titel des registers selektiert
                -->
                <xsl:variable name="v_data_key1"><xsl:value-of select="@data_key"/></xsl:variable>
                <xsl:variable name="v_caption"><xsl:value-of select="@name"/></xsl:variable>
                <xsl:variable name="v_nr"><xsl:value-of select="substring-before(@name, '. ')"/></xsl:variable>
                <xsl:variable name="v_title"><xsl:value-of select="substring-after(@name, '. ')"/></xsl:variable>
                <xsl:choose>
                    <!-- es wird unterschieden zwischen einzelregistern und registergruppen;
                    bei registergruppen handelt es sich lediglich um sammelüberschriften 
                    für mehrere einzelregister, die zu einer gruppe zusammengefasst 
                    sind (ohne eigenen registerbaum)-->
                <!-- registergruppen -->
                    <xsl:when test="$v_data_key1='indexgroup'">
                        <!-- das element <index> bilden und attribute zuführen -->
                        <index>
                            <!-- die attribute @type, @nr, @title aus den vorher angelegten variablen bilden -->
                            <xsl:attribute name="type">
                                <xsl:value-of select="$v_data_key1"/>
                            </xsl:attribute>
                            <xsl:attribute name="nr">
                                <xsl:value-of select="$v_nr"/>
                            </xsl:attribute>
                            <xsl:attribute name="title">
                                <xsl:value-of select="$v_title"/>
                            </xsl:attribute>
                            <!-- ein attribut @section-id wird gebildet -->
                            <xsl:attribute name="section-id">
                                <xsl:value-of select="@id"/>
                            </xsl:attribute>
                            <!-- das attribut @level kopieren -->
                            <xsl:copy-of select="@level"/>
                            <!-- das attribut @group kopieren -->
                            <xsl:copy-of select="@group"></xsl:copy-of>
                            
                            <xsl:attribute name="log2">br1</xsl:attribute>
        
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <!-- für die laufnummer und den titel der registergruppe elemente bilden 
                                und inhalte aus den entsprechenden variablen einfügen -->
                            <nr><xsl:value-of select="$v_nr"/></nr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <title><xsl:value-of select="$v_title"/></title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <!-- für die anmerkungen zur registergruppe eine element bilden, 
                                die betreffenden daten ansteuern und zur formatierung an ein template verweisen -->
                            <note><xsl:apply-templates select="items/item[@itemtype='chapter']/content"/></note><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </index>
                        <!-- die untergeordneten einzelregister einer registergruppe werden nun angesteuert 
                                und an ein template verwiesen -->
                      <xsl:for-each select="section">
                          <xsl:call-template name="einzelregister">
                              <!-- für das standorte-register wird der basisstandort als parameter mitgegeben -->
                              <xsl:with-param name="p_basisstandort">
                                  <xsl:value-of select="$p_basisstandort"/>
                              </xsl:with-param>
                          </xsl:call-template>           
                      </xsl:for-each>
                    </xsl:when>
                <!-- die einzelregister außerhalb von registergruppen ansteuern und an ein template verweisen -->
                <xsl:otherwise>
                    <xsl:call-template name="einzelregister">
                        <!-- für das standorte-register den zuvor ermittelten basisstandort als parameter mitgeben -->
                        <xsl:with-param name="p_basisstandort">
                            <xsl:value-of select="$p_basisstandort"/>
                        </xsl:with-param>
                    </xsl:call-template>
                </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        
    
        <!-- zum schluss das nicht im bandartikel angelegte marken-register hinzufügen  -->
        <!-- TODO: Warum nicht im Bandartikel angelegt? -->
        <!-- register ansteuern -->
        <xsl:for-each select="ancestor::book/indices/index[@propertytype='brands']">
            <!-- element <index> mit den erforderlichen attributen anlegen -->
            <index type="{@propertytype}">
                <xsl:copy-of select="@title"/>
                <xsl:copy-of select="@group"/>
                <xsl:copy-of select="@role"/>
                <xsl:copy-of select="@snr"/>
                <xsl:attribute name="log2">br1brands</xsl:attribute>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- template zur formatierung des markenregisters aufrufen -->
                <xsl:call-template name="register-marken" />
            </index><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>

<!-- _____________________________________________________________________________ -->
    
    <!-- die einzelnen register für den inschriftenband (nicht nur für den katalog) -->
    <!-- wird wirksam im template name=bandregister, 
            der fokus liegt auf book/volume/section[@data_key=indices]/section[@datakey=*] -->
    <xsl:template name="einzelregister">
        <!-- für die zuordnung des entsprechenden registers wird der 
            erforderliche key in einen parameter gegeben -->
        <xsl:param name="p_data_key1"><xsl:value-of select="@data_key"/></xsl:param>
        <!-- für das register standorte wird ein parameter mit der angabe des basisstandorts gebildet;
        der inhalt des parameters ergibt sich aus dem befehl with-param, 
        der dem befehl zum aufrufen dieses templates mitgegeben wurde-->
        <xsl:param name="p_basisstandort"></xsl:param>
        
        <!-- die bezeichnung des registers wird in einem parameter vorformatiert: 
            bindestrich zwischen leerzeichen wird durch halbgeviertstrich ersetzt -->
        <xsl:param name="p_caption">
            <xsl:choose>
                <xsl:when test="contains(@name, ' - ')">
                    <xsl:value-of select="substring-before(@name, ' - ')"/><xsl:text> – </xsl:text><xsl:value-of select="substring-after(@name, ' - ')"/>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
            </xsl:choose>
        </xsl:param>
        <!-- aus der bezeichnung werden laufnummer und titel in parametern selektiert -->
        <xsl:param name="p_nr"><xsl:value-of select="substring-before($p_caption, '. ')"/></xsl:param>
        <xsl:param name="p_title"><xsl:value-of select="substring-after($p_caption, '. ')"/></xsl:param>
        
        <!-- jedes einzelne register wird neu angelegt-->
        <index log2="index1">
            <!-- das type-attribut wird aus dem parameter $key1 gebildet -->
            <xsl:attribute name="type" select="$p_data_key1" />
            <xsl:attribute name="nr" select="substring-before(@name, '. ')" />
            <xsl:attribute name="title" select="substring-after(@name, '. ')" />
            <xsl:attribute name="section-id" select="@id" />
            <xsl:copy-of select="@level" />
            <xsl:for-each select="ancestor::book/indices/index[@propertytype=$p_data_key1]">
                <xsl:copy-of select="@group" /> 
            </xsl:for-each>
            <xsl:if test="$p_data_key1='blazons'">
                <xsl:copy-of select="ancestor::book/indices/index[@propertytype='heraldry']/@group" />
            </xsl:if>
            
            <!-- @group="initials" bewirkt die gruppierung von registereinträgen nach anfangsbuchstaben  -->
            <xsl:attribute name="log2">br2</xsl:attribute>
            <xsl:if test="$p_data_key1='locations'">
                <xsl:attribute name="base-location" select="$p_basisstandort" />
            </xsl:if>
            
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    
            <!-- überschrift und anmerkungen zum register werden ausgelesen-->
            <nr><xsl:value-of select="$p_nr"/></nr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <title><xsl:value-of select="$p_title"/></title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <note><xsl:apply-templates select="items/item[@itemtype='chapter']/content"/></note><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- der jeweils zugehörige registerbaum wird angesteuert -->
            
            <!-- Wappenbeschreibungen als Register (siehe auch di-trans2-book, dort Wappenbeschreibungen als Anhang) -->
            <xsl:if test="$p_data_key1='blazons'">
                <xsl:for-each select="ancestor::book/indices/index[@propertytype='heraldry']">
                    <xsl:call-template name="register-blasonierungen" />
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </xsl:if>
            
            <!-- das register "Besonderheiten der Datierung" muss aus dem "Sachregister" herausgezogen 
                    und als eigenes register abgelegt werden -->
            <xsl:if test="$p_data_key1='timing'">
                <xsl:for-each select="ancestor::book/indices/index[@propertytype='subjects']/item[starts-with(lemma,'Dat')]">
                    <xsl:call-template name="register_hierarchisieren" />
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </xsl:if>                    
            
            <!-- alle anderen register werden durch den data_key angesteuert und gegebenenfalls 
                    speziellen templates zugewiesen-->
            <xsl:for-each select="ancestor::book/indices/index[@propertytype=$p_data_key1]">
                <xsl:choose>
                    <!-- wappen -->
                    <xsl:when test="$p_data_key1='heraldry'">
                        <xsl:call-template name="register-wappen" />
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:when>
                    <!-- standorte -->
                    <xsl:when test="$p_data_key1='locations'">
                        <xsl:call-template name="register-standorte">
                            <xsl:with-param name="p_basisstandort" select="$p_basisstandort" />
                            <xsl:with-param name="index" select="." />
                        </xsl:call-template>
                         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:when>
                    <!-- andere -->
                    <xsl:otherwise>
                        <xsl:call-template name="register_hierarchisieren" />
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:otherwise>
              </xsl:choose>                               
            </xsl:for-each>

            <!-- wenn andere register als untereinträge incorporiert werden sollen, werden sie über den data_key ausgelesen  -->
            <!-- zu incorporierende register ansteuern-->
            <xsl:for-each select="section">
                <!-- data_key zur identifikation des abschnitts als variable speichern -->
                <xsl:variable name="v_data_key2"><xsl:value-of select="@data_key"/></xsl:variable>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- registereintrag anlegen -->
                <item>
                    <!-- die attribute des abschnitts werden übernommen -->
                    <xsl:copy-of select="@*"/>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- als lemma wird das attribut @name des jeweiligen abschnitts eingetragen; 
                        daraus werden auch die attribute sortchart (anfangsbuchstabe normalisiert) und sortstring generiert -->
                    <lemma log2="rsl1">
                        <xsl:attribute name="sortchart"><xsl:value-of select="translate(lower-case(substring(@name,1,1)),'äáàâåüúùûöóòôéèêž' , 'aaaaauuuuooooeeez')"/></xsl:attribute>
                        <xsl:attribute name="sortstring"><xsl:value-of select="@name"/></xsl:attribute>
                        <xsl:value-of select="@name"/>
                    </lemma>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- die einträge des incorporierten registers werden angesteuert und als untereinträge aufgenommen
                            und zur fomatierung an ein template verwiesen-->
                    <xsl:for-each select="ancestor::book/indices/index[@propertytype=$v_data_key2]">
                        <xsl:call-template name="register_hierarchisieren"></xsl:call-template>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </item>
            </xsl:for-each>
     </index><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
<!-- ENDE: bandregister (aus dem bandartikel (element <volume>) generiert) -->


<!-- ========================================================================= -->
<!-- einzelregister aufrufen -->  
    <!-- wird wirksam im template name=katalogregister -->
    <xsl:template match="index">
        <!-- basisstandort und bezeichnung/spezifikation des registers in parametern ablegen -->
        <xsl:param name="p_basisstandort"><xsl:value-of select="ancestor::book/project/name"/></xsl:param>
        <xsl:param name="p_propertytype"><xsl:value-of select="@propertytype"/></xsl:param>
        <!-- register-element neu bilden, vorhandene attribute übernehmen, weitere attribute erzeugen -->
        <index>
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="log2">kr1</xsl:attribute>
            <!-- register zur weiteren verarbeitung (hierarchisierung) an templates verweisen -->
            <xsl:choose>
                <!-- standorte-register speziellem template zuweisen, basisstandort als parameter mitgeben -->
                <xsl:when test="@propertytype='locations'">
                    <xsl:attribute name="base-location"><xsl:value-of select="$p_basisstandort"/></xsl:attribute>
                    <xsl:call-template name="register-standorte">
                        <xsl:with-param name="p_basisstandort" select="$p_basisstandort" />
                        <xsl:with-param name="index" select="." />
                    </xsl:call-template>
                </xsl:when>
                <!-- markenregister speziellem template zuweisen -->
                <xsl:when test="@propertytype='brands'">
                    <xsl:call-template name="register-marken" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:when>
                <!-- wappenregister speziellem template zuweisen -->
                <xsl:when test="@propertytype='heraldry'">
                    <xsl:call-template name="register-wappen" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:when>
                <!-- die übrigen register demselben template zwecks hierarchisierung zuweisen -->  
                <xsl:otherwise>
                    <xsl:call-template name="register_hierarchisieren"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:otherwise>
            </xsl:choose>      
        </index>
    </xsl:template>

    <xsl:template name="register_hierarchisieren">
        <!-- dieses template dient dazu, ein register zu hierarchisieren und 
            registereinträge (element <item>) zu gruppieren;
            das ergebnis kann in einem parameter zur weiterbearbeitung abgelegt werden.
        -->
        <xsl:choose>
            <!-- im personenregister werden nicht aufgelöste initialen zur gruppe (ohne gruppenlemma) transformiert
                und an das ende gestellt-->
            <xsl:when test="@propertytype='personnames'">
                <xsl:for-each select="item[not(contains(lemma,'nitialen'))]">
                    <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                    <item log2="r18">
                        <xsl:copy-of select="@*"/>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:for-each select="sections">
                            <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:for-each>
                        <xsl:call-template name="crossRefs" />
                        <xsl:call-template name="item-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
                <!-- nicht aufgelöste initialen -->
                <xsl:for-each select="item[@level='0'][contains(lemma,'nitialen')]">
                    <item log2="r19">
                        <xsl:copy-of select="@*"/>
                        <xsl:attribute name="type">group</xsl:attribute>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:call-template name="item-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </xsl:when>

            <!-- die übrigen register werden nur hierarchisiert -->
            <xsl:otherwise>
                    <xsl:call-template name="item-schleife"></xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    
    <!-- registereinträge -->
    <!-- nur im texttyeneregister wird für die <sections> eine eigenes template aufgerufen -->
    <xsl:template name="item-schleife">
        <xsl:for-each select="item">
            <xsl:variable name="v_lemma" select="lemma" />
            <xsl:variable name="v_sortchart" select="lemma/@sortchart" />
            <xsl:variable name="v_sortstring" select="lemma/@sortstring" />

            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:choose>
                <!-- gruppierungen -->
                <xsl:when test="@iscategory='1'">
                    <xsl:choose>
                        <!-- ohne gruppenbezeichnung -->
                        <xsl:when test="@ishidden='1'">
                            <item log2="r30a">
                                <xsl:copy-of select="@*"/>
                                <xsl:attribute name="sortchart" select="$v_sortchart" />
                                <xsl:attribute name="sortstring" select="$v_sortstring" />
                                <xsl:attribute name="type">group</xsl:attribute>
                                <xsl:attribute name="groupname" select="$v_lemma" />
                                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <xsl:for-each select="sections">
                                    <xsl:choose>
                                    <xsl:when test="ancestor::index[@propertytype='texttypes']">
                                        <xsl:call-template name="links-textsorten" />
                                    </xsl:when>
                                        <xsl:otherwise><xsl:copy-of select="." /></xsl:otherwise>
                                </xsl:choose>
                                </xsl:for-each>
                                
                                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <xsl:call-template name="crossRefs" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                                                
                                <xsl:call-template name="item-schleife" />
                                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                
                            </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:when>
                        
                        <!-- mit gruppenbezeichnung -->
                        <xsl:otherwise>
                            <item log2="r30b">
                                <xsl:copy-of select="@*"/>
                                <xsl:attribute name="sortchart" select="$v_sortchart" />
                                <xsl:attribute name="sortstring" select="$v_sortstring" />
                                <xsl:attribute name="type">group</xsl:attribute>
                                <xsl:attribute name="groupname" select="$v_lemma" />
                                
                                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <xsl:copy-of select="lemma" />
                                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                
                                <xsl:for-each select="sections">
                                    <xsl:choose>
                                        <xsl:when test="ancestor::index[@propertytype='texttypes']">
                                            <xsl:call-template name="links-textsorten" />
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <xsl:copy-of select="." />
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </xsl:for-each>                                
                                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                
                                <xsl:call-template name="crossRefs" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <xsl:call-template name="item-schleife" />
                                
                            </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                
                <!-- keine gruppierung -->
                <xsl:otherwise>
                    <item log2="r30c">
                        <xsl:copy-of select="@*"/>
                        <xsl:attribute name="sortchart"><xsl:value-of select="$v_sortchart"/></xsl:attribute>
                        <xsl:attribute name="sortstring"><xsl:value-of select="$v_sortstring"/></xsl:attribute>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:copy-of select="lemma"></xsl:copy-of>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:for-each select="sections">
                            <xsl:choose>
                                <xsl:when test="ancestor::index[@propertytype='texttypes']">
                                    <xsl:call-template name="links-textsorten" />
                                </xsl:when>
                                <xsl:otherwise><xsl:copy-of select="." /></xsl:otherwise>
                            </xsl:choose>
                        </xsl:for-each>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:call-template name="crossRefs" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:call-template name="item-schleife" />
                    </item>                            
                </xsl:otherwise>
            </xsl:choose>
         </xsl:for-each>
    </xsl:template>
     
</xsl:stylesheet>