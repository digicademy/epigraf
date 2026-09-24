<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
     xmlns:php="http://php.net/xsl"
    >
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans2-commons.xsl"/>

<!-- mit diesem stylesheet wir eine liste der inschriften eines 
        inschriftenbands in chronologischer folge erstellt.
        
        diese liste ist relevant für inschriftenbestände 
        mit zahlreichen inschriftenträgern, auf denen nacheinander 
        mehrfach neue inschriften angebracht wurde (z. b. grabplatten), 
        um neben der sortierung der artikel nach der jeweils ältesten inschrift
        auch eine auflistung sämtlicher inschriften in chronologischer folge anzubieten. 
        
        das erstkriterum der sortierung sind die datierungsangaben,
        zu jeder datierungsangabe, die im katalog der inschriften vorkommt,
        werden die entsprechenden inschriften aufgeführt, 
        sortiert nach der bezeichnung des inschriftenträgers (zweitkriterium),
        dann nach der artikelnummer (drittkriterium) und dem 
        bezeichner der inschrift bzw. des inschrift-teils (viertkriterium).  
        
        die erstellung der liste erfolgt schrittweise in parametern
    -->

<!-- ======================================================================================= -->

    <!-- inschriftenliste = chronologische liste aller inschriften = erstellen -->
    <xsl:template name="table_of_inscriptions">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- es wird die entsprechende titelseite in den textbausteinen des bandes angesteuert,
        der titel und gegebenenfalls die vorbemerkung ausgelesen-->
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <table_of_inscriptions>
                <xsl:copy-of select="@*"/>
                <xsl:attribute name="indexing">1</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- abschnittsüberschrift -->
                <xsl:call-template name="section-title"></xsl:call-template>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- vorbemerkungen -->
                <xsl:apply-templates select="items/item/content"></xsl:apply-templates>
                <xsl:call-template name="inschriften_sammeln"></xsl:call-template>
           </table_of_inscriptions>
    </xsl:template>
    
    <xsl:template name="inschriften_sammeln">
        <!-- inschriftenliste schrittweise in parametern zusammenstellen -->

        <!-- 1. Schritt: daten sammeln -->
        <xsl:param name="p_inschriftenliste1">
                 <!-- alle artikel ansteuern -->
                <xsl:for-each select="ancestor::book/articles/article">
                    <!-- objektbezeichnung und id der objektbezeichnung speichern
                         wenn der name des objekts mit dem lemma des übergeordneten eintrags beginnt, 
                         wird das lemma des übergeordneten eintrags ausgewählt,
                         anderenfalls der name des umnmittelbaren eintrags;
                         
                         dadurch wird z. b. "Grabplatte mit Wappen" als untereintrag von "Grabplatte" auf "Grabplatte" reduziert,
                         hingegen wird "Fußbodenfliese" als untereintrag von "Bauglied" als "Fußbodenfliese" ausgeworfen
                    
                    -->
                    <xsl:variable name="v_objecttype_ancestor">
                        <xsl:for-each select="sections/section[@sectiontype='objecttypes']/items/item[1]/property">
                            <xsl:value-of select="ancestors/property[last()]/lemma"/>
                        </xsl:for-each>
                    </xsl:variable>
                    
                    <xsl:variable name="v_objecttype_self">
                        <xsl:value-of select="normalize-space(sections/section[@sectiontype='objecttypes']/items/item[1]/property/name)"/>
                    </xsl:variable>
                    
                    <xsl:variable name="v_objecttype_final">
                        <xsl:choose>
                            <xsl:when test="$v_objecttype_ancestor[string()] and starts-with($v_objecttype_self, $v_objecttype_ancestor)"><xsl:value-of select="$v_objecttype_ancestor"/></xsl:when>
                            <xsl:otherwise><xsl:value-of select="$v_objecttype_self"/></xsl:otherwise>
                        </xsl:choose>                        
                    </xsl:variable>
                    
                        <xsl:variable name="v_objekttypeID"><xsl:value-of select="sections/section[@sectiontype='objecttypes']/items/item[1]/property/@id"/></xsl:variable>
              
                    <!-- artikelnummer und id des artikels speichern -->
                    <xsl:variable name="v_artikelnummer"><xsl:number count="article" from="articles" format="1" /></xsl:variable>
                    <xsl:variable name="v_artikelID"><xsl:value-of select="@id"/></xsl:variable>
              
                    <!-- anzahl der inschriften im artikel ernitteln und speichern -->
                    <xsl:variable name="v_inschriften_total" select="count(sections/section[@sectiontype='inscription'])"></xsl:variable>
              
                    <!-- innerhalb der artikel alle inschriften ansteuern -->
                    <xsl:for-each select="sections/section[@sectiontype='inscription']">
                        <!-- 
                            nummer der inschrift und 
                            bezeichner (buchstaben),
                            sowie id derselben speichern,
                            anzahl der inchrift-teile ermitteln und speichern
                        -->
                        <xsl:variable name="v_inschrift_number"><xsl:value-of select="@number"/></xsl:variable>
                        <xsl:variable name="v_inschrift_caption"><xsl:value-of select="@name"/></xsl:variable>
                        <xsl:variable name="v_inschriftID"><xsl:value-of select="@id"/></xsl:variable>
                        <xsl:variable name="v_inschrifteile_total" select="count(section)"></xsl:variable>
                        <xsl:choose>
                            <!-- inschriften, die nur nummerierungen z. b. von grabplatten darstellen übergehen -->
                            <!-- TODO: use IRI -->
                            <xsl:when test="items/item/property/lemma[text()='Nummerierung']"></xsl:when>
                            <xsl:otherwise>
                                <!-- inschrift-teil ansteuern -->
                                <xsl:for-each select="section">
                                    <!-- 
                                        bezeichnung des inschriftteils und dessen id speichern,
                                        anzahl der bearbeitungen (varianten) ernitteln und speichern
                                    -->
                                    <xsl:variable name="v_inschriftteil_caption"><xsl:value-of select="@name"/></xsl:variable>
                                    <xsl:variable name="v_inschriftteilID"><xsl:value-of select="@id"/></xsl:variable>
                                    <xsl:variable name="v_bearbeitungen_total" select="count(section)"></xsl:variable>
                                 <!-- bearbeitungen ansteuern -->
                                 <xsl:for-each select="section[1]">
                                     <!-- nach datierungsindex sortieren -->
                                     <!-- nicht datierte inschriften übergehen -->
                                     <xsl:if test="items/item/date_sort[node()]">
                                         <!-- 
                                             für die erste bearbeitung (variante 1) einen container <item> anlegen,
                                             darin weitere elemente anlegen und mit attributen versehen für:
                                             artikelnummer
                                             nummer der inschrift 
                                             nummer das inschriftteils
                                             objekttyp (inschriftenträger)
                                             datierung
                                             datierungsindex
                                         -->
                                         <item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                             <!-- artikelnummer-->
                                             <artikelnummer>
                                                 <xsl:attribute name="targetID"><xsl:value-of select="$v_artikelID"/></xsl:attribute>
                                                 <xsl:value-of select="$v_artikelnummer"/>
                                             </artikelnummer>
                                             <!-- nummer der inschrift -->
                                             <inschriftnummer>
                                                 <xsl:attribute name="targetID"><xsl:value-of select="$v_inschriftID"/></xsl:attribute>
                                                 <xsl:attribute name="inschriften_total"><xsl:value-of select="$v_inschriften_total"/></xsl:attribute>
                                                 <xsl:attribute name="number"><xsl:value-of select="$v_inschrift_number"/></xsl:attribute>
                                                 <xsl:value-of select="$v_inschrift_caption"/>
                                             </inschriftnummer>
                                             <!-- nummer des inschriftteils -->
                                             <inschriftteilnummer>
                                                 <xsl:attribute name="targetID"><xsl:value-of select="$v_inschriftteilID"/></xsl:attribute>
                                                 <xsl:attribute name="inschriftteile_total"><xsl:value-of select="$v_inschrifteile_total"/></xsl:attribute>
                                                 <xsl:value-of select="$v_inschriftteil_caption"/>
                                             </inschriftteilnummer>
                                             <!-- bezeichnung des objekts (objekttyp, inschriftenträger) -->
                                             <objekttyp><xsl:attribute name="targetID"><xsl:value-of select="$v_objekttypeID"/></xsl:attribute><xsl:value-of select="$v_objecttype_final"/></objekttyp>
                                             <!-- datierung -->
                                             <dating><xsl:value-of select="items/item/date_value"/></dating>
                                             <!-- datierungsindex -->
                                             <dating_index><xsl:value-of select="items/item/date_sort"/></dating_index>
                                         </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                     </xsl:if>
                                 </xsl:for-each>
                                </xsl:for-each>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>
     
     <!-- parametertest 
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <objecttype_ancestor><xsl:value-of select="$v_objecttype_ancestor"/></objecttype_ancestor><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <objecttype_self><xsl:value-of select="$v_objecttype_self"/></objecttype_self><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <objecttype_final><xsl:value-of select="$v_objecttype_final"/></objecttype_final><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
     -->
                </xsl:for-each>
         </xsl:param>
        
        <!-- 2. Schritt: die liste des ersten parameters chronologisch sortieren -->
        <xsl:param name="p_inschriftenliste2">
            <xsl:for-each select="$p_inschriftenliste1/item">
                <xsl:sort select="dating_index" order="ascending" lang="de" case-order="upper-first" />
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </xsl:param>
        
        <!-- 3. Schriftt: innerhalb der liste nach datierung gruppieren  -->
        <xsl:param name="p_inschriftenliste3">
            <xsl:for-each select="$p_inschriftenliste2/item">
                <!-- das erste vorkommen einer bestimmten datierung auswählen -->
                <xsl:if test="not(dating = preceding-sibling::item/dating)">
                    <!-- gruppen-element <dating> anlegen -->
                    <dating>
                        <!-- das erste vorkommen einfügen -->
                        <xsl:copy-of select="dating"/>
                        <objekt>
                            <xsl:copy-of select="artikelnummer"/>
                            <xsl:copy-of select="inschriftnummer"/>
                            <xsl:copy-of select="inschriftteilnummer"/>
                            <xsl:copy-of select="objekttyp"/>
                        </objekt>
                        <xsl:for-each select="following-sibling::item[dating = current()/dating]">
                        <objekt>
                            <xsl:copy-of select="artikelnummer"/>
                            <xsl:copy-of select="inschriftnummer"/>
                            <xsl:copy-of select="inschriftteilnummer"/>
                            <xsl:copy-of select="objekttyp"/>
                        </objekt>                            
                        </xsl:for-each>
                    </dating>
                </xsl:if>
            </xsl:for-each>
        </xsl:param>
        
        <!-- 4. Schritt innerhalb der datierungen nach objekttypen gruppieren -->
        <xsl:param name="p_inschriftenliste4">
            <xsl:for-each select="$p_inschriftenliste3/dating">
                <dating>
                    <xsl:copy-of select="dating"/>
                    <xsl:for-each select="objekt">
                        <xsl:if test="not(objekttyp = preceding-sibling::objekt/objekttyp)">
                        <objekt>
                            <xsl:copy-of select="objekttyp"/>
                            <article>
                                <xsl:copy-of select="artikelnummer"/>
                                <xsl:copy-of select="inschriftnummer"/>
                                <xsl:copy-of select="inschriftteilnummer"/> 
                            </article>
                            <xsl:for-each select="following-sibling::objekt[objekttyp = current()/objekttyp]">
                                <article>
                                    <xsl:copy-of select="artikelnummer"/>
                                    <xsl:copy-of select="inschriftnummer"/>
                                    <xsl:copy-of select="inschriftteilnummer"/> 
                                </article>
                            </xsl:for-each>
                        </objekt>
                        </xsl:if>
                    </xsl:for-each>
                </dating>
            </xsl:for-each>
        </xsl:param>

        <!-- 5. Schritt: nach artikelnummern gruppieren -->
        <xsl:param name="p_inschriftenliste5">
            <xsl:for-each select="$p_inschriftenliste4/dating">
               <dating>
                <xsl:copy-of select="dating"/>
                <xsl:for-each select="objekt">
                    <objekt>
                        <xsl:copy-of select="objekttyp"/>
                        <xsl:for-each select="article[not(artikelnummer = preceding-sibling::article/artikelnummer)]">
                            <article>
                                <xsl:copy-of select="artikelnummer"/>
                                <inschriften>
                                    <xsl:copy-of select="inschriftnummer"/>
                                    <inschriftteile>
                                        <xsl:copy-of select="inschriftteilnummer"/>
                                    </inschriftteile>
                                </inschriften>
                                <xsl:for-each select="following-sibling::article[artikelnummer = current()/artikelnummer]">
                                <inschriften>
                                    <xsl:copy-of select="inschriftnummer"/>
                                    <inschriftteile>
                                        <xsl:copy-of select="inschriftteilnummer"/>
                                    </inschriftteile>
                                </inschriften>                                        
                                </xsl:for-each>                                    
                            </article>
                        </xsl:for-each>
                    </objekt>
                </xsl:for-each>
               </dating>
            </xsl:for-each>
        </xsl:param>
        
        <!-- 6. Schritt: nach inschriften gruppieren -->
        <xsl:param name="p_inschriftenliste6">
            <xsl:for-each select="$p_inschriftenliste5/dating">
                <dating>
                    <xsl:copy-of select="dating"/>
                    <xsl:for-each select="objekt">
                        <objekt>
                            <xsl:copy-of select="objekttyp"/>
                           <xsl:for-each select="article">
                               <article>
                                   <xsl:copy-of select="artikelnummer"/>
                                   <xsl:for-each select="inschriften[not(inschriftnummer = preceding-sibling::inschriften/inschriftnummer)]">
                                       <inschrift>
                                           <xsl:copy-of select="inschriftnummer"/>
                                           <xsl:copy-of select="inschriftteile/*"/>
                                           <xsl:for-each select="following-sibling::inschriften[inschriftnummer = current()/inschriftnummer]">
                                                  <xsl:copy-of select="inschriftteile/*"/>
                                            </xsl:for-each>                                                         
                                       </inschrift>
                                    </xsl:for-each>
                               </article>
                           </xsl:for-each> 
                        </objekt>
                    </xsl:for-each>
                </dating>                
            </xsl:for-each>
        </xsl:param>
        
        
        <!-- 7. Schritt:  reduzieren auf die artikelnummer, wenn alle inschriften eines artikels unter derselben datierung 
            erfasst sind; inschriftenteile übergehen -->
        <xsl:param name="p_inschriftenliste7">
            <xsl:for-each select="$p_inschriftenliste6/dating">
                <dating>
                    <xsl:copy-of select="dating"/>
                    <xsl:for-each select="objekt">
                        <objekt>
                            <xsl:copy-of select="objekttyp"/>
                            <xsl:for-each select="article">
                                <xsl:variable name="v_inschriften_total"><xsl:value-of select="inschrift/inschriftnummer/@inschriften_total"/></xsl:variable>
                                <xsl:variable name="v_inschriften_ist"><xsl:value-of select="count(inschrift)"/></xsl:variable>
                                   <article>
                                       <xsl:copy-of select="artikelnummer"/>
                                       <xsl:if test="$v_inschriften_total != $v_inschriften_ist">
                                         <xsl:for-each select="inschrift">
                                             <xsl:copy>
                                                 <xsl:copy-of select="inschriftnummer"/>
                                             </xsl:copy>
                                         </xsl:for-each>  
                                       </xsl:if>
                                   </article>
                               </xsl:for-each> 
                        </objekt>
                    </xsl:for-each>
                </dating>                
            </xsl:for-each>
        </xsl:param>
        
        <!-- 8. Schritt: inschriftennummern in sequenzen gruppieren -->
        <xsl:param name="p_inschriftenliste8">
            <xsl:for-each select="$p_inschriftenliste7/dating">
                <dating>
                    <xsl:copy-of select="dating"/>
                    <xsl:for-each select="objekt">
                        <objekt>
                            <xsl:copy-of select="objekttyp"/>
                            <xsl:for-each select="article">
                                <xsl:variable name="v_inschriften_total"><xsl:value-of select="inschrift/inschriftnummer/@inschriften_total"/></xsl:variable>
                                <xsl:variable name="v_inschriften_ist"><xsl:value-of select="count(inschrift)"/></xsl:variable>
                                   <article>
                                       <xsl:copy-of select="artikelnummer"/>
                                       <xsl:if test="inschrift">
                                           <!-- template zum sequenzieren aufrufen -->
                                            <xsl:call-template name="inschriften_sequenzieren"></xsl:call-template>
                                       </xsl:if>
                                   </article>
                               </xsl:for-each> 
                        </objekt>
                    </xsl:for-each>
                </dating>                
            </xsl:for-each>            
        </xsl:param>
        
        <!-- 9. Schritt: fertige gruppierte und sortierte liste ausgeben -->
        <items><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
           <xsl:copy-of select="$p_inschriftenliste8"/>

            <!-- parametertest
            <param1><xsl:copy-of select="$p_inschriftenliste1"/></param1>     
            <param6><xsl:copy-of select="$p_inschriftenliste6"/></param6>     
            <param7><xsl:copy-of select="$p_inschriftenliste7"/></param7>
            -->

        </items><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

<!-- ============================================================================= -->

    <!-- ununterbrochen fortlaufenden folgen von inschriftennummern zu sequenzen zusammenfassen -->
    <xsl:template name="inschriften_sequenzieren">
        <!-- 1. ein element <sequenz> über alle nummern setzen -->
      <sequenz>
        <xsl:for-each select="inschrift">
            <xsl:variable name="v_self_number"><xsl:value-of select="inschriftnummer/@number"/></xsl:variable>
            <xsl:variable name="v_following_number"><xsl:value-of select="following-sibling::*[1]/inschriftnummer/@number"/></xsl:variable>
                <xsl:copy-of select="inschriftnummer"/>
                    <!-- 2. wenn die fortlaufende nummernfolge unterbrochen ist, die sequenz durch inverse tags splitten -->
                    <xsl:if test="not(position()=last())">
                        <xsl:if test="$v_self_number + 1 != $v_following_number">
                            <xsl:text disable-output-escaping="yes">&lt;/sequenz&gt;&lt;sequenz&gt;</xsl:text>
                        </xsl:if>
                    </xsl:if>
        </xsl:for-each>
      </sequenz>    
    </xsl:template>

</xsl:stylesheet>