<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">

<!-- in diesem stylesheet werden verschiedene elemente verarbeitet, 
        die in anderen stylsheets der dritten transformationsstufe 
        aufgerufen werden -->

    <!-- absätze in vorwort, einleitung usw. -->
    <xsl:template match="p">
        <xsl:choose>
            <xsl:when test="text()='§'" />
            <xsl:when test="h"><xsl:copy-of select="h" copy-namespaces="no"/></xsl:when>
            <xsl:otherwise>
                <p>
                    <xsl:attribute name="indent">
                        <xsl:choose>
                            <xsl:when test="preceding-sibling::bl">0</xsl:when>
                            <xsl:when test="not(preceding-sibling::p)">0</xsl:when>
                            <xsl:when test="preceding-sibling::p[1][text()='§']">0</xsl:when>
                            <xsl:when test="preceding-sibling::p[1][not(node())]">0</xsl:when>
                            <xsl:when test="preceding-sibling::p[1][h]">0</xsl:when>
                            <xsl:otherwise>1</xsl:otherwise>
                        </xsl:choose>
                    </xsl:attribute>
                    <xsl:apply-templates /></p>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- verweisfolgen (folgen von verweisen auf artikel) 
        
     das entsprechend werkzeug in epigraf wird dort durch 
     geschweifte rote klammern sichtbar gemacht
    
    ziel: wenn mehrere verweise auf artikelnummern direkt hintereinander erfolgen, 
    wird nur einmal das präfix "Kat.-Nr." gesetzt, 
    die nummern werden, durch kommata getrennt,angefügt
    
    wenn innerhalb einer verweisfolge zu einzelnen artikelnummern ergänzungen (in klammern) 
    hinzugefügt sind, müssen diese ergänzungen zusammen mit der nummer noch einmal (d. h. verschachtelt) 
    als folge markiert worden sein (d. h. geschweifte rote klammern innerhalb solcher klammern), 
    damit sie mit der nummer zusammen bleiben
    -->
    <xsl:template match="folge[not(parent::folge)]">
        <xsl:param name="p_folge1">
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
        
        <xsl:param name="p_folge2">
            <xsl:for-each select="$p_folge1/folge">
                <xsl:for-each select="folge">
                    <xsl:sort select="link[1]/@article-sortstring" order="ascending" lang="de" case-order="upper-first" />
                    <xsl:copy-of select="." copy-namespaces="no"/>
                </xsl:for-each>
            </xsl:for-each>
        </xsl:param>
        
        <!-- parametertest      
      <folge1><xsl:copy-of select="$folge1"/></folge1>
      <folge2><xsl:copy-of select="$folge2"/></folge2>
    -->
        
        <xsl:for-each select="$p_folge2">
            <folge>
                <xsl:text>Kat.-Nr. </xsl:text>
                <xsl:for-each select="folge">
                    <xsl:copy-of select="." copy-namespaces="no"/><xsl:if test="following-sibling::folge"><xsl:text>, </xsl:text></xsl:if>
                </xsl:for-each>
            </folge>
        </xsl:for-each>
    </xsl:template>
    
    <!-- gesperrt ausgezeichnete wörter in der einleitung -->
    <xsl:template match="g"><xsl:copy-of select="." copy-namespaces="no"/></xsl:template>
    
    <!-- fett ausgezeichnete wörter in der einleitung -->
    <xsl:template match="b"><xsl:copy-of select="." copy-namespaces="no"/></xsl:template>
    
    <!-- unterstreichungen in der einleitung -->
    <xsl:template match="u"><xsl:copy-of select="." copy-namespaces="no"/></xsl:template>
    
    <!-- kapitälchen in der einleitung -->
    <xsl:template match="k"><xsl:copy-of select="." copy-namespaces="no"/></xsl:template>
    
    <!-- kursivierungen die keine zitate sind -->
    <xsl:template match="i"><i><xsl:copy-of select="@*"/><xsl:apply-templates/></i></xsl:template>
    
    <!-- hochstellungen außerhalb der transkriptionen -->
    <xsl:template match="sups"><sups><xsl:apply-imports></xsl:apply-imports></sups></xsl:template>
    
    <!-- weblinks -->
    <xsl:template match="a">
        <xsl:copy-of select="." copy-namespaces="no"/>
    </xsl:template> 
    

  <!-- interne verweise auf inschriften und fußnoten im selben artikel-->
<xsl:template match="rec_intern">
    <!-- verweistyp - fußnote oder inschrift (footnotes vs sections) - ermitteln -->
    <xsl:param name="p_rec_intern_type"><xsl:value-of select="tokenize(@data-link-target,'-')[1]"/></xsl:param>
    <!-- id des verweisziels speichern -->
    <xsl:param name="p_data_link_target"><xsl:value-of select="@data-link-target"/></xsl:param>
    <!-- section-typ ermitteln -->
    <xsl:param name="p_target_type"><xsl:value-of select="ancestor::article/sections//section[@id=$p_data_link_target]/@sectiontype"/></xsl:param>
    <!-- inschrift-nummer speichern -->
    <xsl:param name="p_target_number"><xsl:value-of select="ancestor::article/sections//section[@id=$p_data_link_target]/@number"/></xsl:param>
    <!-- bezeichner des inschriftenteils speichern -->
    <xsl:param name="p_target_name">
        <xsl:value-of select="ancestor::article/sections//section[@id=$p_data_link_target]/@name"/>
    </xsl:param>
    <!-- bezeichner der übergordneten Inschrift speichern -->
    <xsl:param name="p_target_parent_name">
        <xsl:value-of select="ancestor::article/sections//section[@id=$p_data_link_target][1]/parent::section/@name" />
    </xsl:param>
    <!-- nummer der übergordneten inschrift speichern -->
    <xsl:param name="p_target_parent_number">
        <xsl:value-of select="ancestor::article/sections//section[@id=$p_data_link_target]/parent::section/@number"/>
    </xsl:param>
<!-- parametertest
<xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<para1><xsl:value-of select="$p_rec_intern_type"/></para1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<para2><xsl:value-of select="$p_data_link_target"/></para2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<para3><xsl:value-of select="$p_target_type"/></para3><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<para4><xsl:value-of select="$p_target_number"/></para4><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<para5><xsl:value-of select="$p_target_name"/></para5><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<para6><xsl:value-of select="$p_target_parent_name"/></para6><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<para7><xsl:value-of select="$p_target_parent_number"/></para7><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->

    <xsl:choose>
        <!-- verweise auf fußnoten -->
        <xsl:when test="$p_rec_intern_type='footnotes'">
            <link type="{$p_rec_intern_type}"><xsl:copy-of select="@data-link-target"/>
              <!-- weil die fußnoten noch nicht nummeriert sind, kann der bezeichner (zahl oder buchstaben) der fußnote
                erst im nächsten transformationsschritt anhand der target_id ausgelesen werden -->
            </link>
        </xsl:when>
        <!-- verweise auf inschriften -->
        <xsl:otherwise>
            <link log3="recInt1" type="{$p_target_type}"><xsl:copy-of select="@data-link-value"/><xsl:copy-of select="@data-link-target"/>
                <!-- inschrift -->
                <xsl:if test="$p_target_type='inscription'">
                    <xsl:choose>
                        <xsl:when test="$sw_modus='projects_bay'">
                            <xsl:choose>
                                <xsl:when test="$p_target_number[string()]">
                                    <xsl:number value="$p_target_number" format="I"/>
                                </xsl:when>
                                <xsl:otherwise>NaN</xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>

                        <xsl:otherwise><xsl:value-of select="$p_target_name"/>
                            </xsl:otherwise>
                    </xsl:choose>
                </xsl:if>
                <!-- inschriftteil -->
                <xsl:if test="$p_target_type='inscriptionpart'">
                    <xsl:choose>
                        <xsl:when test="$sw_modus='projects_bay'">
                            <xsl:choose>
                                <xsl:when test="$p_target_parent_number/string() and $p_target_number/string()">
                                    <xsl:number value="$p_target_parent_number" format="I"/><xsl:text>.</xsl:text><xsl:number value="$p_target_number" format="I"/>
                                </xsl:when>
                                <xsl:otherwise>NaN</xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise><xsl:value-of select="$p_target_parent_name"/><xsl:value-of select="$p_target_name"/></xsl:otherwise>
                    </xsl:choose>
                </xsl:if>

            </link>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>



<!-- REC EXTERN NEU: vwerweise auf (andere) artikel, sowie auf inschriften und fußnoten in anderen artikeln-->

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
        <xsl:param name="p_target-type"><xsl:value-of select="tokenize(@data-link-target,'-')[1]"/></xsl:param>
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
                            <xsl:when test="ancestor::book/articles/article[@id=$p_data-link-target]">
                                <xsl:for-each select="ancestor::book/articles/article[@id=$p_data-link-target]">
                                    <xsl:attribute name="article-sortstring"><xsl:number count="article" from="articles" format="001"/></xsl:attribute>
                                    <xsl:number count="article" from="articles" format="1"/>
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
                            <!-- wenn der artikel mit der angewiesenen inschrift in der export-auswahl enthalten ist, ihn über die @id ansteuern und für die laufnummer attribute erzeugen -->
                            <xsl:when test="ancestor::book/articles/article/sections//section[@id=$p_data-link-target]">
                                <!-- zuerst die inschrift-section ansteuern-->
                                <xsl:for-each select="ancestor::book/articles/article/sections//section[@id=$p_data-link-target]">
                                    <xsl:variable name="v_sectionnumber"><xsl:value-of select="parent::section/@name"/><xsl:value-of select="@name"/></xsl:variable>
                                    <!-- für den inschriftbezeichner das attribute @name ausgelesen, bei einem inschriftteil davor dasselbe attribut der übergeordneten inschrift-section -->
                                    <xsl:attribute name="section-number"><xsl:value-of select="$v_sectionnumber"/></xsl:attribute>
                                    <!-- dann den übergeordneten artikel ansteuern und die laufnummer des artikels in attribute geben -->
                                    <xsl:for-each select="ancestor::article">
                                        <xsl:attribute name="article-sortstring"><xsl:number count="article" from="articles" format="001"/></xsl:attribute>
                                        <xsl:number count="article" from="articles" format="1"/><inscriptlink log="i1"><xsl:value-of select="$v_sectionnumber"/></inscriptlink>
                                    </xsl:for-each>
                                </xsl:for-each>
                            </xsl:when>
                            <!-- wenn der artikel nicht in der export-auswahl vorhanden ist, die bezeichnung des verweiszieles einfügen -->
                            <xsl:otherwise><xsl:value-of select="$p_value"/></xsl:otherwise>
                        </xsl:choose>
                    </link>
                </xsl:when>


                <!-- verweis auf einen artikel mit verlängerung auf eine fußnote -->
                <xsl:when test="$p_target-type='footnotes'">
                    <link type="footnotes_extern" folge="{$p_folge}"><xsl:copy-of select="@*"/>
                        <xsl:choose>
                            <!-- wenn der artikel mit der angewiesenen fußnote in der auswahl enthalten ist, ihn über die @id ansteuern und für die laufnummer attribute erzeugen -->
                            <xsl:when test="ancestor::book/articles/article[footnotes/footnote[@id=$p_data-link-target]]">
                                <xsl:for-each select="ancestor::book/articles/article[footnotes/footnote[@id=$p_data-link-target]]">
                                    <xsl:attribute name="article-number"><xsl:number count="article" from="articles" format="1"/></xsl:attribute>
                                    <xsl:attribute name="article-sortstring"><xsl:number count="article" from="articles" format="001"/></xsl:attribute>
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
<xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<p_rec-extern_links><xsl:copy-of select="$p_rec-extern_links"/></p_rec-extern_links><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
 -->

        <!-- parameter auslesen und die links mit Kat-Nr. verbinden, wen sie nicht in einer verweisfolge stehen -->
        <xsl:for-each select="$p_rec-extern_links/link">
            <xsl:if test="not(@folge='1')"><xsl:text>Kat.-Nr. </xsl:text></xsl:if> <xsl:copy-of select="." copy-namespaces="no"/>
        </xsl:for-each>

    </xsl:template>

    <!-- ende: REC EXTERN NEU-->

    <!-- verweise auf einen literaturtitel -->
    <xsl:template match="rec_lit">
        <link type="literatur">
            <xsl:copy-of select="@id" />
            <!--xsl:copy-of select="@data-link-target" /-->

            <!-- In case the target literature entry is hidden, link to the next non-nidden ancestor -->
            <xsl:variable name="tagid" select="@id" />
            <xsl:variable name="link" select="ancestor::article/links/link[@from_tagid=$tagid]" />
            <xsl:choose>
                <xsl:when test="$link/property/@ishidden = '1'">
                    <xsl:variable name="target" select="$link/property/ancestors/property[@ishidden='0'][1]" />
                    <xsl:choose>
                        <xsl:when test="$target">
                            <xsl:attribute name="data-link-target" select="$target/@id" />
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:attribute name="ishidden" select="@ishidden" />
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:attribute name="data-link-target" select="@data-link-target" />
                </xsl:otherwise>
            </xsl:choose>

            <xsl:copy-of select="@data-link-value" />
            <xsl:value-of select="@data-link-value" />
            <xsl:if test="not(@data-link-value)">
                <xsl:value-of select="@value" />
            </xsl:if>
        </link>
    </xsl:template>

    <!-- verweise auf eine marke -->
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

    <!-- ziffernfußnoten -->
    <xsl:template match="app1">
        <xsl:variable name="v_id" select="@id" />
        <xsl:choose>
            <!-- in der einleitung werden die anmerkungen in den text gezogen, damit sie in word automatisch an das jeweilige seitenende gesetzt werden können -->
            <xsl:when test="ancestor::introduction or ancestor::prefaces">
                <xsl:for-each select="ancestor::book/footnotes/footnote[@from_tagid=$v_id]">
                    <!-- für interne verweise auf fußnoten wird die id der fußnote auf das app1-tag übertragen -->
                    <app1><xsl:copy-of select="@id"/>
                        <xsl:apply-templates select="content" />
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
            <!-- im transkriptionsfeld werden sie unterdrückt -->
            <xsl:when test="ancestor::bl"></xsl:when>
            <!-- in Fußnoten wurden sie in Absätze umgeformt -->
            <xsl:when test="ancestor::footnote/content"></xsl:when>            
            <xsl:otherwise><xsl:copy /></xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template match="footnote/content">
        <xsl:for-each-group select="node()" group-starting-with="nl">
            <p>
                <xsl:apply-templates select="current-group()[not(self::nl)]"/>
            </p>
        </xsl:for-each-group>
    </xsl:template>
    
    <!-- buchstabenfußnoten und platzhalter für verweise auf objektnummer von grundrisszeichnungen -->
    <xsl:template match="app2"><xsl:copy><xsl:copy-of select="@*"/></xsl:copy></xsl:template>
    <xsl:template match="loc_ten"><app1 source="loc_ten"><xsl:copy-of select="@id"/></app1></xsl:template>

    <!-- zitate -->
    <xsl:template match="quot"><quot><xsl:apply-templates/></quot></xsl:template>

    <!-- notizen (auszugeben am ende eines artikels) -->
    <xsl:template name="notes">
        <notes><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="sections//section/comment[text()]">
                <xsl:variable name="v_section_type"><xsl:value-of select="parent::section/@sectiontype"/></xsl:variable>
                <xsl:variable name="v_section_name1"><xsl:value-of select="@name"/></xsl:variable>
                <xsl:variable name="v_section_number"><xsl:value-of select="@number"/></xsl:variable>
                <!-- für die müncherner bände die nummer der inschrift römisch erzeugen -->
                <xsl:variable name="v_section-name2">
                    <xsl:choose>
                        <xsl:when test="$v_section_type='inscription' and $sw_modus='projects_bay'">
                            <xsl:number value="$v_section_number" format="I"></xsl:number>
                        </xsl:when>
                        <xsl:otherwise><xsl:value-of select="$v_section_name1"/></xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>

                <xsl:variable name="v_section-name3">
                    <xsl:choose>
                        <xsl:when test="ancestor::section[@sectiontype='inscription']">
                            <xsl:for-each select="ancestor::section[@sectiontype='inscription']">
                                <xsl:choose>
                                    <xsl:when test="$sw_modus='projects_bay'">
                                        <xsl:text>Inschrift </xsl:text>
                                            <xsl:choose>
                                                <xsl:when test="@number[string()]">
                                                    <xsl:number value="@number" format="I"></xsl:number>
                                                </xsl:when>
                                                <xsl:otherwise>NaN</xsl:otherwise>
                                            </xsl:choose>
                                    </xsl:when>
                                    <xsl:otherwise><xsl:text>Inschrift </xsl:text><xsl:value-of select="@name"/></xsl:otherwise>
                                </xsl:choose>
                            </xsl:for-each>
                            <xsl:if test="$sw_modus='projects_all'">
                            <xsl:for-each select="ancestor::section[@sectiontype='inscriptionpart']">
                                <xsl:text>.</xsl:text><xsl:value-of select="@name"/>
                            </xsl:for-each>
                                <xsl:for-each select="ancestor::section[@sectiontype='inscriptiontext']">
                                    <xsl:text>.</xsl:text><xsl:value-of select="@name"/>
                                </xsl:for-each>
                            </xsl:if>
                        </xsl:when>
                        <xsl:otherwise><xsl:value-of select="parent::section/@name"/></xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>


                <xsl:variable name="v_parent_section1"><xsl:value-of select="ancestor-or-self::section/@parent_id"/></xsl:variable>


                <note><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- zuordnung zum entsprechenden abschnitt -->
                    <allocation><xsl:value-of select="$v_section-name3"/></allocation>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <note_text><xsl:for-each select="node()"><xsl:apply-templates select="."/></xsl:for-each></note_text>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </note><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </notes><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>



    <xsl:template match="h[@value='Schriftarten' or @data-link-value='Schriftarten']"><xsl:copy-of select="." copy-namespaces="no"/></xsl:template>
    
    <!-- zwischenüberschriften in einleitungskapiteln -->
    <xsl:template match="h"><h><xsl:copy-of select="@value"/><xsl:copy-of select="@data-link-value"/><xsl:apply-templates/></h></xsl:template>
    
    <!-- seitenzahlenanker in der einleitung -->
    <xsl:template match="anchor"><xsl:copy-of select="."></xsl:copy-of></xsl:template>

    
</xsl:stylesheet>