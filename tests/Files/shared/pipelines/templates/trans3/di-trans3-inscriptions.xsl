<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >
    
<xsl:import href="../commons/di-switch.xsl"/> 

<!-- dieses stylesheet baut die inschriften-abschnitte eines artikels 
        auf der dritten transformationsstufe auf.
    
    es wird aufgerufen in di-trans3-articles.xsl-->


    <!-- die hierarchische struktur der inschriften und transkriptionen wird durchgearbeitet und neu getagt -->
    <xsl:template match="section[@sectiontype='inscription']">

       <xsl:param name="p_count_inscriptions"><xsl:for-each select="ancestor::article"><xsl:value-of select="count(sections//section[@sectiontype='inscription'])"/></xsl:for-each></xsl:param>
       <xsl:param name="p_count_inscriptionparts"><xsl:value-of select="count(section)"/></xsl:param>
       <xsl:param name="p_id1"><xsl:value-of select="@id"/></xsl:param>

        <xsl:param name="p_inscription">
            <inscription logg="i1">
                <xsl:copy-of select="@*"/>
                <!-- die attribute der inschrift-sections werden auf die container übertragen -->
                <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- zugehörige inschriftteile suchen -->
                <xsl:for-each select="section">
                    <xsl:variable name="v_count_versions"><xsl:number count="section"/></xsl:variable>
                    <xsl:variable name="v_id2"><xsl:value-of select="@id"/></xsl:variable>
                    <xsl:variable name="v_verloren"><xsl:value-of select="items/item[@itemtype='lost']/value"/></xsl:variable>
                    <inscriptionpart logg="i2">
                        <xsl:copy-of select="@*"/>
                        <xsl:attribute name="lost"><xsl:value-of select="$v_verloren"/></xsl:attribute>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <count_versions><xsl:value-of select="count(section)"/></count_versions>
<!-- ergänzung zur nummerierung für die münchener bände, aus dem feld datergaenzung der ersten version -->
                        <addition><xsl:value-of select="section[1]/fields[1]/datergaenzung[1]"/></addition>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <!-- zugehörige bearbeitungen/versionen suchen -->
                        <xsl:for-each select="section">
                           <xsl:call-template name="version"/> 
                        </xsl:for-each>
                    </inscriptionpart>
                </xsl:for-each>
            </inscription><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:param>
           
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>            
<!--
<test_anzahl_inschriften><xsl:copy-of select="$p_count_inscriptions"/></test_anzahl_inschriften><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>       
<test_anzahl_teile><xsl:copy-of select="$p_count_inscriptionparts"/></test_anzahl_teile><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<test_inschrift><xsl:copy-of select="$p_inschrift"/></test_inschrift><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
 -->     
        <xsl:for-each select="$p_inscription/inscription">
            <inscription>
                <xsl:copy-of select="@*"/>
<!--                <xsl:if test="$sw_modus='projects_bay'">
                    <xsl:attribute name="name">
                    <xsl:choose>
                        <xsl:when test="@number[string()]">
                            <xsl:number value="@number" format="I"/>
                        </xsl:when>
                        <xsl:otherwise>NaN</xsl:otherwise>
                    </xsl:choose>
                    </xsl:attribute>  
                </xsl:if>-->
                <xsl:for-each select="inscriptionpart">
                <xsl:variable name="v_count_versions"><xsl:value-of select="count_versions"/></xsl:variable>
                    <inscriptionpart log3="it1">
                        <xsl:copy-of select="@*"/>
                       <xsl:if test="addition[text()]"><xsl:copy-of select="addition" copy-namespaces="no"/></xsl:if>
                        <xsl:for-each select="version/content[.//bl]">
                            <xsl:call-template name="transkriptions">
                                <xsl:with-param name="p_count_inscriptions"><xsl:value-of select="$p_count_inscriptions"/></xsl:with-param>                              
                                <xsl:with-param name="p_count_inscriptionparts"><xsl:value-of select="$p_count_inscriptionparts"/></xsl:with-param>                              
                                <xsl:with-param name="p_count_versions"><xsl:value-of select="$v_count_versions"/></xsl:with-param>                              
                            </xsl:call-template> 
                        </xsl:for-each>     
                        <!-- wenn die übersetzungen inschriftenweise ausgegeben werden sollen, werden sie hier kopiert --> 
                        <xsl:if test="version/outputoption[text()='di_translations_sectionwise']">
                            <xsl:for-each select="version/translation">
                                <xsl:copy-of select="." copy-namespaces="no"/>
                            </xsl:for-each>
                        </xsl:if>
                    </inscriptionpart>
                </xsl:for-each> 
            </inscription>
        </xsl:for-each>
    </xsl:template>

<xsl:template name="version">
    <version log3="b1">
        <!-- die attribute der version-sections werden auf die container übertragen -->
        <xsl:copy-of select="@*"/>
        <xsl:attribute name="project"><xsl:value-of select="ancestor::book/project/name"/></xsl:attribute>
        <xsl:attribute name="ausgabe"></xsl:attribute>
         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:copy-of select="items/item/content" copy-namespaces="no"/>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:copy-of select="items/item/translation" copy-namespaces="no"/>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:copy-of select="ancestor::article/links" copy-namespaces="no"/>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
       <xsl:for-each select="ancestor::article/sections/section[@sectiontype='outputoptions']/items/item/property">
            <outputoption><xsl:value-of select="norm_iri"/></outputoption>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
       </xsl:for-each>
    </version>
</xsl:template>

<!--  -->
<xsl:template name="transkriptions">
   <xsl:param name="p_count_inscriptions"></xsl:param>
   <xsl:param name="p_count_inscriptionparts"/>
   <xsl:param name="p_count_versions"></xsl:param>

    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <version>
        <xsl:copy-of select="@*"/>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- die bezeichner der inschriften(teile) werden generiert und mit der id der version-section versehen -->
<!--
<parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
inschriften: <xsl:copy-of select="$p_count_inscriptions"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
teile: <xsl:copy-of select="$p_count_inscriptionparts"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
versionen: <xsl:copy-of select="$p_count_versions"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->
        <nr log="n1">
            <xsl:attribute name="id"><xsl:value-of select="parent::*/@id"/></xsl:attribute>
             <xsl:attribute name="nr_bay">
                        <xsl:if test="$p_count_inscriptions = 1">
                            <xsl:if test="$p_count_inscriptionparts = 1"></xsl:if>
                            <xsl:if test="$p_count_inscriptionparts != 1"><xsl:number value="parent::inscription/@number" format="1"/></xsl:if>
                        </xsl:if>
                        <xsl:if test="$p_count_inscriptions != 1">
                            <xsl:if test="$p_count_inscriptionparts = 1">
                            <xsl:number value="ancestor::inscription/@number" format="I"/><xsl:if test="ancestor::inscriptionpart[@lost='1']">&#x2020;</xsl:if>
                            </xsl:if>
                            <xsl:if test="$p_count_inscriptionparts != 1">
                                <xsl:number value="ancestor::inscription/@number" format="I"/><xsl:number value="ancestor::inscriptionpart/@number" format="1"/><xsl:if test="ancestor::inscriptionpart[@lost='1']">&#x2020;</xsl:if>
                            </xsl:if>
                        </xsl:if>
            </xsl:attribute>
            <xsl:if test="$p_count_inscriptions = 1">
                <xsl:if test="$p_count_inscriptionparts = 1"></xsl:if>
                <xsl:if test="$p_count_inscriptionparts != 1">
                    <xsl:value-of select="ancestor::inscriptionpart/@name"/>
                </xsl:if>
            </xsl:if>
            <xsl:if test="$p_count_inscriptions != 1">
                <xsl:if test="$p_count_inscriptionparts = 1">
                    <xsl:value-of select="ancestor::inscription/@name"/>
                    <xsl:if test="ancestor::inscriptionpart[@lost='1']">&#x2020;</xsl:if>
                </xsl:if>
                <xsl:if test="$p_count_inscriptionparts != 1">
                    <xsl:value-of select="ancestor::inscription/@name"/><xsl:value-of select="ancestor::inscriptionpart/@name"/>
                    <xsl:if test="ancestor::inscriptionpart[@lost='1']">&#x2020;</xsl:if>
                </xsl:if>
            </xsl:if>
        </nr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- der bezeichner der bearbeitung/version wird (als versionsnummer des inschriftenteils) generiert, 
            wenn mehr als ein bearbeitung/version existiert bzw. ausgegeben werden soll -->
        <version-nr>
            <xsl:if test="$p_count_versions !=1 ">
                <xsl:number level="single" count="version" from="inscriptionpart" format="a"/> 
            </xsl:if>
        </version-nr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
        <!-- die templates für die elemente der transkription werden aufgerufen -->
        <content>
            <xsl:apply-templates/>
        </content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </version><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</xsl:template>


    <xsl:template name="translations"><!-- aufgerufen aus article -->
        <xsl:param name="sections" select="./sections/section" />
        <xsl:param name="p_db"></xsl:param>
        <translations><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="$sections[@sectiontype='inscription']/section[@sectiontype='inscriptionpart']/section[@sectiontype='inscriptiontext']">
            <xsl:variable name="v_section-id"><xsl:value-of select="@id"/></xsl:variable>
                 <xsl:for-each select="items/item/translation[node()]">
                    <xsl:call-template name="translation">
                        <xsl:with-param name="p_db"><xsl:value-of select="$p_db"/></xsl:with-param>
                        <xsl:with-param name="p_section-id"><xsl:value-of select="$v_section-id"/></xsl:with-param>
                    </xsl:call-template> 
                </xsl:for-each>      
            </xsl:for-each>
        </translations>
    </xsl:template>
    
    <xsl:template name="translation">
        <xsl:param name="p_db"></xsl:param>
        <xsl:param name="p_section-id"></xsl:param>
        <xsl:param name="p_count_inscriptions">
            <xsl:for-each select="ancestor::article">
                <xsl:value-of select="count(sections//section[@sectiontype='inscription'])"/>
            </xsl:for-each>
        </xsl:param>
        <xsl:param name="p_count_inscriptionparts">
                <xsl:for-each select="ancestor::section[@sectiontype='inscription']">
                    <xsl:value-of select="count(section)"/>
                </xsl:for-each>
        </xsl:param>
        <xsl:param name="p_count_versions">
            <xsl:for-each select="ancestor::section[@sectiontype='inscriptionpart']">
                <xsl:value-of select="count(section)"/>
            </xsl:for-each>
        </xsl:param>
        
        <!-- bezeichner der bearbeitung/version -->
        <!-- todo: was ist mit zahlen größer als 10 ? -->
        <xsl:param name="p_caption-version">
            <xsl:value-of select="translate(ancestor::section[@sectiontype='inscriptiontext']/@number,'123456789','abcdefghi')"/>
        </xsl:param>
        <!-- bezeichner des inschriftteils -->
        <xsl:param name="p_caption-inscriptionpart">
            <xsl:for-each select="ancestor::section[@sectiontype='inscriptionpart']">
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

         </xsl:param>
        
        <!-- bezeichner der inschrift -->
        <xsl:param name="p_caption-inscription">
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

        </xsl:param>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- die bezeichner der inschriften(teile) werden generiert und mit der id der version-section versehen -->
        <translation><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<!-- 
<parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
inschriften: <xsl:copy-of select="$p_count_inscriptions"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
teile:  <xsl:copy-of select="$p_count_inscriptionparts"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
versions: <xsl:copy-of select="$p_count_versions"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
Nr. Inschrift: <xsl:copy-of select="$p_caption-inscription"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
Nr. Inschriftteil: <xsl:copy-of select="$p_caption-inscriptionpart"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
Nr. Version:<xsl:copy-of select="$p_caption-version"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->          
           <nr>
               <xsl:attribute name="section-id"><xsl:value-of select="$p_section-id"/></xsl:attribute>
                <xsl:if test="$p_count_inscriptions = 1">
                    <xsl:if test="$p_count_inscriptionparts = 1"></xsl:if>
                    <xsl:if test="$p_count_inscriptionparts &gt; 1">
                        <xsl:value-of select="$p_caption-inscriptionpart"/>
                    </xsl:if>
                </xsl:if>
                <xsl:if test="$p_count_inscriptions &gt; 1">
                    <xsl:if test="$p_count_inscriptionparts = 1">
                        <xsl:value-of select="$p_caption-inscription"/>
                    </xsl:if>
                    <xsl:if test="$p_count_inscriptionparts &gt; 1">
                        <xsl:value-of select="$p_caption-inscription"/><xsl:value-of select="$p_caption-inscriptionpart"/>
                    </xsl:if>
                </xsl:if>
            </nr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- der bezeichner der bearbeitung/version wird (als versionsnummer des inschriftenteils) generiert, 
            wenn mehr als ein bearbeitung/version existiert bzw. ausgegeben werden soll -->
            <version-nr>
                <xsl:if test="$p_count_versions !=1 ">
                    <xsl:value-of select="$p_caption-version"/>
                </xsl:if>
            </version-nr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- die templates für die elemente bzw. den text der übersetzung werden aufgerufen -->
            <content>
                <xsl:apply-templates/>
            </content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </translation><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text> 
    </xsl:template>
   
<!-- ================================================================== -->    
<!--Transkriptionswerkzeuge: Muster fuer Formatierungen in der Transkription-->
    
    <!-- um eine Ausgabe in Epidoc zu ermöglichen, dürfte das trankriptions-markup 
         nicht hier schon in ein klammersystem überführt werden,
         sondern erst in der letzten, ausgabeformat-spezifischen transformationsstufe, zu pdf, doc, dio-xml-->
   
    <!--Bloecke in den Transkriptionen-->
    <xsl:template match="bl">
        <!-- norm_iri des bl-types ermitteln -->
        <xsl:param name="p_id"><xsl:value-of select="@id"/></xsl:param>
        <xsl:param name="p_norm_iri" select="ancestor::article/links/link[@from_tagid=$p_id]/property/norm_iri" />

          <xsl:choose>
             <!-- inschriften mit marginalien am linken rand;
                  in epigraf wird zur kennzeichnung einer marginalie am linken rand 
                  innerhalb des elements <bl> eine weiteres 
                  element <bl @data-link-iri="properties/alignments/di_left_margin"> gesetzt,
                  und zwar an die stelle, auf die sich die marginalie bezieht
             -->
              
              <!-- prüfen ob innernhalb des <bl>-elements eine marginalie vorkommt-->
             <xsl:when test="bl[contains(@data-link-iri,'properties/alignments/di_left_margin')]">
                 <!-- das element neu anlegen, mit einem attribut dafür, 
                     dass darin eine marginalie vorkommt, kennzeichnen und 
                     ein template zur weiteren transformation aufrufen -->
                 <bl value="with_marg_left">
                     <xsl:call-template name="trans_marginal" />
                 </bl>
             </xsl:when>
              
              
              <!-- prüfen ob es sich bei dem <bl>-element um eine marginalie handelt;
                   gegebenenfalls ein element <di_left_margin> anlegen und den inhalt an templates verweisen
              -->
             <!-- altes muster:       
             <xsl:when test="contains(@data-link-iri,'properties/alignments/di_left_margin')"><marg><xsl:apply-templates></xsl:apply-templates></marg></xsl:when>
             -->                    
              <xsl:when test="@data-link-iri='properties/alignments/di_left_margin'">
                  <di_left_margin><xsl:apply-templates /></di_left_margin>
              </xsl:when>
              
              <!-- wenn es sich nicht um eine marginalie handelt und auch keine marginalie darin vorkommt -->
              <xsl:otherwise>
                 <bl>
                     <!-- das attribut @align gibt den key wieder, @value den listeneintrag;
                     nota bene: nicht alle bl haben ein attribut @align (kein key vergeben-->
                     <xsl:attribute name="align">
                         <xsl:value-of select="@data-link-iri" />
                         <xsl:if test="not(@data-link-iri)"><xsl:value-of select="@align" /></xsl:if>
                     </xsl:attribute>
                     
                     <!--vorbereitung der spalten fuer DIO-->
                     <!-- TODO: NICHT hart kodieren. Wozu? -->
                     <xsl:if test="$sw_pipeline='DIO-Band' and ancestor::bl">
                         <xsl:text disable-output-escaping="yes">&#x003C;line position="0" &#x003E;</xsl:text>
                     </xsl:if>
                     
                     <!-- wenn im block Verszeilen vorhanden sind, wird das taggen dieser Zeilen vorbereitet; 
                         zugleich wird mittels der Schriftart die Laufweite der Schrift abgeschaetzt -->
                     
                     <!-- schriftart-attribut setzen, wenn verszeilen vorhanden -->
                     <xsl:if test=".//vz and not(bl)">
                         <xsl:attribute name="fontwidth">
                             <xsl:choose>
                                 <xsl:when test="contains(ancestor::section/items/item[@propertytype='fonttypes']/property/name, 'Majuskel') or
                                     contains(ancestor::section/items/item[@propertytype='fonttypes']/property/name, 'Kapitalis')
                                     ">large</xsl:when>
                                 <xsl:otherwise>small</xsl:otherwise>
                             </xsl:choose>
                         </xsl:attribute>
                     </xsl:if>


                     <xsl:choose>
                         <!-- wenn im block Verszeilen vorhanden sind:
                         gruppierung der kindknoten an jedem vz-element;
                         jede gruppe wird in ein <vz>-element mit dem passenden indent gewrappt -->
                         <xsl:when test=".//vz and not(bl)">
                             <xsl:for-each-group select="node()" group-starting-with="vz">
                                 <vz>
                                     <xsl:attribute name="indent">
                                         <xsl:choose>
                                             <xsl:when test="self::vz and
                                                 (@indent='1' or @data-link-iri='properties/indentations/di_indent')">1</xsl:when>
                                             <xsl:otherwise>0</xsl:otherwise>
                                         </xsl:choose>
                                     </xsl:attribute>
                                     <!-- alle knoten der gruppe außer dem vz-trennzeichen selbst verarbeiten -->
                                     <xsl:apply-templates select="current-group()[not(self::vz)]"/>                                     
                                 </vz><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                             </xsl:for-each-group>
                         </xsl:when>
                         <!-- keine verszeilen: kinder normal verarbeiten -->
                         <xsl:otherwise>
                             <xsl:apply-templates/>
                         </xsl:otherwise>
                     </xsl:choose>                    
                     
                     <!--vorbereitung der spalten fuer DIO-->
                     <xsl:if test="$sw_pipeline='DIO-Band' and ancestor::bl">
                         <xsl:text disable-output-escaping="yes">&#x003C;/line&#x003E;</xsl:text>
                     </xsl:if>
                     
                 </bl>
             </xsl:otherwise>
         </xsl:choose>
    </xsl:template>    

<!-- marginalie im transkriptionsbereich (d. h. in element <bl>) -->
    <xsl:template name="trans_marginal">
           <!-- elemente der marginalie an templates verweisen -->
           <xsl:text disable-output-escaping="yes">&#x003C;line log3="marg2"&#x003E;</xsl:text>
           <xsl:apply-templates></xsl:apply-templates>
           <xsl:text disable-output-escaping="yes">&#x003C;/line&#x003E;</xsl:text>
   </xsl:template>   
    

  <!--Worttrenner-->
    <xsl:template match="wtr">
        <xsl:param name="p_wtr-id"><xsl:value-of select="@id"/></xsl:param>
        <xsl:param name="p_icon">
        <!-- wenn im feld Einheit (element unit) ein ausgabezeichen angegeben ist, 
             wird dieses ausgewählt, anderenfalls ein hochpunkt gesetzt -->
                    <xsl:choose>                        
                        <xsl:when test="$options/@simplewordseparators = '1'">
                            <xsl:text>&#x00B7;</xsl:text>
                        </xsl:when>
                        <xsl:when test="ancestor::version/links/link[@from_tagid=$p_wtr-id]/property/unit[text()]">
                            <xsl:value-of select="ancestor::version/links/link[@from_tagid=$p_wtr-id]/property/unit"/>
                        </xsl:when>
                        <xsl:when test="ancestor::article/links/link[@from_tagid=$p_wtr-id]/property/unit[text()]">
                            <xsl:value-of select="ancestor::article/links/link[@from_tagid=$p_wtr-id]/property/unit"/>
                        </xsl:when>
                        <xsl:otherwise><xsl:text>&#x00B7;</xsl:text></xsl:otherwise>
                    </xsl:choose>
        </xsl:param>
        <!-- element neu anlegen und trennzeichen ($p_icon) einfügen -->
        <wtr log="wtr1">
           <xsl:copy-of select="@*"/>
            <xsl:value-of select="$p_icon"/>
        </wtr>
    </xsl:template>

    <!--Ligaturen-->
    <xsl:template match="all">
        <xsl:variable name="v_all-id"><xsl:value-of select="@id"/></xsl:variable>
        <!-- element neu anlegen -->
            <all>
                <xsl:copy-of select="@*"/>
                <!-- die spezifikationen der buchstabenverbindung in den <links> aufsuchen
                und als attribute anfügen-->
                <xsl:for-each select="ancestor::version/links/link[@from_tagid=$v_all-id]">
                    <xsl:copy-of select="@from_id"/>
                    <xsl:attribute name="lemma">
                        <xsl:value-of select="property/lemma" />
                    </xsl:attribute>
                    <xsl:attribute name="name">
                        <xsl:value-of select="property/name" />
                    </xsl:attribute>
                </xsl:for-each>
                <!-- die ligierte zeichenfolge an templates verweisen -->
                <xsl:apply-templates/>
            </all>  
    </xsl:template>
    
    <!--versgerechte Zeilenumbrueche-->
    <xsl:template match="vz">
        
        <xsl:choose>
            <!-- in transkriptionen mit marginalien -->
            <xsl:when test="parent::bl/bl[contains(@data-link-iri,'properties/alignments/di_left_margin')]">
                <xsl:if test="preceding-sibling::vz">
                    <xsl:text disable-output-escaping="yes">&#x003C;/line&#x003E;&#x000A;&#x003C;line log3="marg1"&#x003E;</xsl:text>
                </xsl:if>
                <!-- das element vz wird kopiert um später die einrückung zu verarbeiten -->
                <xsl:copy-of select="." copy-namespaces="no" />
            </xsl:when>
            
            <!-- im transkriptionsfeld (nicht-marginalie):
             wird jetzt durch for-each-group im bl-template behandelt;
             dieses template wird für solche vz nicht mehr aufgerufen,
             da sie durch current-group()[not(self::vz)] ausgeschlossen sind.
             Sicherheitshalber: keine Ausgabe. -->
            <xsl:when test="ancestor::bl and
                not(parent::bl[contains(@data-link-iri,'properties/alignments/di_left_margin')])" />
            
            <!-- außerhalb des transkriptionsfeldes, etwa in fussnoten:
             bei einrückung drei feste leerzeichen einfügen -->
            <xsl:otherwise>
                <xsl:if test="@indent='1' or @data-link-iri='properties/indentations/di_indent'">
                    <xsl:text>&#x00A0;&#x00A0;&#x00A0;</xsl:text>
                </xsl:if>
            </xsl:otherwise>
        </xsl:choose>

   </xsl:template>

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
    <xsl:template match="add"><xsl:copy><xsl:copy-of select="@*"/><xsl:apply-templates/></xsl:copy></xsl:template>

    <!--Abbreviaturen / abbreviations-->
    <xsl:template match="abr">
        <xsl:choose>
            <!-- wenn die abkürzung in einer buchstabenverbindung enthalten ist,
            wird sie entsprechend gekennzeichnet-->
            <xsl:when test="ancestor::all">
                <!-- auf <noAll> wird in di-doc-ligatures.xsl zugegriffen -->
                <noAll><xsl:text>(</xsl:text><xsl:apply-templates/><xsl:text>)</xsl:text></noAll>
            </xsl:when>
            <!-- anderenfalls werden runde klammern gesetzt -->
            <xsl:otherwise>
                    <xsl:text>(</xsl:text><xsl:apply-templates/><xsl:text>)</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <!--Ergaenzungen--> 
    <xsl:template match="cpl">
        <!-- ergänzungen (das fehlen der zeichen beruht nicht auf späteren verlust) werden in eckige klammern gesetzt;
             innerhalb oder zwischen anderen ergänzungen oder verlust-tags (<del<) werden die klammern ausgelassen
        -->
        <xsl:if test="not(preceding-sibling::node()[1][self::del or self::cpl]) and not(parent::cpl or parent::del)">[</xsl:if>
        <xsl:apply-templates/>
        <xsl:if test="not(following-sibling::node()[1][self::del or self::cpl]) and not(parent::cpl or parent::del)">]</xsl:if>
    </xsl:template>
    
    <!--Verlust / lost letters 
        ist die Anzahl der verlorenen Zeichen unbekannt, werden drei Striche, ist sie bekannt, 
        wird die entsprechende Anzahl Punkte in eckigen Klammern gesetzt; die Klammern entfallen, 
        wenn die Verlustzeichen zwischen (ebenfalls eckigen) Ergaenzungsklammern gesetzt werden.-->

    <!-- &#x2011; geschützter bindestrich - in times new roman nicht verfügbar-->
    <!-- &#x202F; geschütztes schmales leerzeichen -->
    <!-- 
    &#x002D 	[- - -] hyphen-minus
    &#x02D7 	[˗ ˗ ˗] modifier letter minus sign
    &#x2012 	[‒ ‒ ‒] figure dash / ziffernbreiter gedankenstrich
    &#x2013 	[– – –] en dash / halbgeviertstrich
    -->
    <xsl:template match="del">
        <!-- das attribut @num_sing ansteuern und auf einen zahlenwert hin klären: 
                a) ist das attribut leer (num_sing='') wird der attributwert auf 0 gesetzt,
                b) besteht der attributwert aus dem fragezeichen oder anderen zeichen, 
                    die keine zahl sind (z. b. num_sing='?') wird er auf 0 gesetzt,
                c) besteht der attributwert aus einer zahl, wird sie übernommen
        -->
        <xsl:param name="p_num_sign">
            <xsl:choose>
                <xsl:when test="@num_sign[string()]">
                    <xsl:choose>
                        <xsl:when test="matches(@num_sign,'[^0-9]+')">0</xsl:when>
                        <xsl:otherwise><xsl:value-of select="@num_sign"/></xsl:otherwise>
                    </xsl:choose>
                    </xsl:when>
                <xsl:otherwise>0</xsl:otherwise>
            </xsl:choose>
        </xsl:param>
<!--        <parametertest-num-sign><xsl:value-of select="$p_num_sign"/></parametertest-num-sign>-->
        <xsl:choose>
            <xsl:when test="$p_num_sign='0'">
                <del><xsl:if test="not(preceding-sibling::node()[1][self::del or self::cpl]) and not(parent::cpl or parent::del or parent::abr or parent::add)">[</xsl:if>&#x2012;&#x202F;&#x2012;&#x202F;&#x2012;<xsl:if test="not(following-sibling::node()[1][self::del or self::cpl]) and not(parent::cpl or parent::del or parent::abr or parent::add)">]</xsl:if></del></xsl:when>
                <xsl:otherwise>
                    <del>
                        <xsl:if test="not(preceding-sibling::node()[1][self::del or self::cpl]) and not(parent::cpl or parent::del or parent::abr or parent::add)">[</xsl:if><xsl:call-template name="loop"><xsl:with-param name="p_num_sign" select="$p_num_sign"/></xsl:call-template><xsl:if test="not(following-sibling::node()[1][self::del or self::cpl]) and not(parent::cpl or parent::del or parent::abr or parent::add)">]</xsl:if>
                    </del>
                </xsl:otherwise></xsl:choose></xsl:template>
    
    
    <!--Schleife zum Auszaehlen der Punkte / find out number of lost letters-->
    <xsl:template name="loop">
<xsl:param name="p_num_sign"></xsl:param>
        <xsl:choose>
            <xsl:when test="$p_num_sign &gt; 0">
                <xsl:text>.</xsl:text>
                <xsl:call-template name="loop">
                    <xsl:with-param name="p_num_sign" select="$p_num_sign - 1"/>
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise></xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <!--Ende: Verlust / lost letters-->
    
    <!--Zeilenenden und Zeilenumbrueche und Zeilenunterbrechungen in den Transkriptionen / line breaks in transcriptions; 
                frühere Einschraenkung: z[preceding-sibling::text() or ancestor::app2]-->
    <xsl:template match="z">
        <xsl:choose>
            <!-- ausgabe für dio -->
            <xsl:when test="$sw_pipeline='DIO-Band' and ancestor::bl[@data-link-iri='properties/alignments/di_columns']">
                <xsl:text disable-output-escaping="yes">&#x003C;/line&#x003E; &#x003C;line position="</xsl:text> <xsl:number  count="z" from="bl"/> <xsl:text disable-output-escaping="yes">" &#x003E;</xsl:text>
            </xsl:when>
            
            <!-- inschriften mit marginalien: 
                 die zeilenumbrüche werden mittels invertierter tags in zeilencontainer umgewandelt -->
            <xsl:when test="parent::bl[not(vz)]/bl[contains(@data-link-iri,'properties/alignments/di_left_margin')]">
                <xsl:if test="following-sibling::z"><xsl:text disable-output-escaping="yes">&#x003C;/line&#x003E; &#x003C;line&#x003E;</xsl:text></xsl:if>
            </xsl:when>

            <!-- für alle anderen fälle: slashs setzen                 
                 damit die vor den slashs stehenden leerzeichen auch in hebräischen texten richtig positioniert werden, 
                 muss vor den slash das steuerzeichen LRM (left-to-right-mark, U+200E) gesetzt werden.
                 Außerdem wird ein Word Joiner (U+2060) eingefügt, um Umbrüche vor dem Slash zu verhindern.
                 TODO: Prüfen, ob hebräisch trotz Word Joiner noch korrekt läuft.
            -->
            <xsl:otherwise>
                <xsl:choose>
                    <!-- wenn im attribut @unit angegeben ist, welches zeichen gesetzt werden soll: -->
                    <xsl:when test="@unit[string()]">
                        <z><xsl:copy-of select="@*"/>&#x200E;&#x2060;<xsl:value-of select="@unit"/>&#x2060;</z>
                    </xsl:when>
                    <!-- neue zeile: einfachen slash setzen-->
                    <xsl:when test="@connex='nz' or @data-link-iri='properties/linebindings/di_newline'">
                        <z><xsl:copy-of select="@*"/>&#x200E;&#x2060;/&#x2060;</z>
                    </xsl:when>
                    <!-- andernfalls: zwei slashes -->
                    <xsl:otherwise>
                        <z><xsl:copy-of select="@*"/>&#x200E;&#x2060;//&#x2060;</z>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

<!--die folgenden elemente werden kopiert und erst im formatierer ausgabespezifisch 
        (je verschieden für doc, pdf oder dio-xml) verarbeitet-->
    
    <!--unsichere Lesung-->
    <xsl:template match="insec">
        <!--<xsl:copy-of select="."  copy-namespaces="no"/>-->
        <insec><xsl:apply-templates></xsl:apply-templates></insec>
    </xsl:template>
    <!--Versalien / ornamental initials-->
    <xsl:template match="vsl">
        <xsl:copy-of select="." copy-namespaces="no" />
    </xsl:template>
    <!--Initialen / initials-->
    <xsl:template match="ini">
        <xsl:copy-of select="." copy-namespaces="no" />
    </xsl:template>
    <!--hochgestellte Buchstaben / elevated letters-->
    <xsl:template match="sup[*|text()]">
        <xsl:copy-of select="." copy-namespaces="no" />
    </xsl:template>
    <!--Kapitaelchen / small caps-->
    <xsl:template match="kap">
        <xsl:copy-of select="." copy-namespaces="no" />
    </xsl:template>
    <!--Chronogrammbuchstaben-->
    <xsl:template match="chr">
        <xsl:copy-of select="." copy-namespaces="no" />
    </xsl:template>  
    <!--Feste Leerstellen und Zwischenraeume / fixed blanks and gaps-->
    <xsl:template match="spatium_v">
        <xsl:copy-of select="." copy-namespaces="no" />
    </xsl:template>
    
    <!--Ende: Formate in den Transkriptionen-->    

</xsl:stylesheet>