<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:epi="http://epigraf.inschriften.net#xslt-functions"    
    exclude-result-prefixes="xs epi"
    version="2.0">

    <xsl:param name="di-type">di</xsl:param><!-- di oder dio; ist hier manuell anzugeben, perspektivisch sollte die angabe aus den kopfdaten des bandartikels auszulesen sein  -->

    <!-- benanntes template für das gesamtregister, wird aufgerufen in di-trans-dio.xsl -->
    <xsl:template name="indices">
        <xsl:param name="p_iri"><xsl:value-of select="$p_volume_iri"/>-page-indices</xsl:param>

        <!-- seite für das gesamtregister anlegen -->    
        <page type="indices" iri="{$p_iri}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>      
            <title>Register</title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>6</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
            <doktype>1</doktype><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <layout>1</layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text> 
            <!-- gesamtregister aufrufen -->
            <xsl:apply-templates select="indices">
                <xsl:with-param name="p_iri" select="$p_iri" />
            </xsl:apply-templates>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:call-template name="citation">
                <xsl:with-param name="p_iri"><xsl:value-of select="$p_iri"/></xsl:with-param>
            </xsl:call-template>
        </page>  
    </xsl:template>        
 
    <!-- 
        die registereinträge erhalten eine universelle ID, 
        die zum späteren kumulieren der regiser aller di-bände benötigt wird
    -->
    
   

<!-- STRUKTURIERUNG DER REGISTER -->
    
    <xsl:template match="indices">
        <xsl:param name="p_iri" />
        <xsl:param name="p_page_id"><xsl:value-of select="ancestor::book/project/dio_page-id_indexes"/></xsl:param>
        
        <!-- Überschrift Register -->
        <tt_content iri="{$p_iri}-title"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <type>header</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header>Register</header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout>2</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

        <!-- inhaltsübersicht und einzelregister-->
        <tt_content iri="{$p_iri}-content">
            <type>html</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <p>
                    <!-- wenn vorhanden, vorbemerkung zum gesamtregister einfügen -->
                    <xsl:if test="note">
                        <xsl:apply-templates select="note/p"/><xsl:text> </xsl:text>                    
                    </xsl:if>                
                    <xsl:text>Sofern neue Erkenntnisse vorliegen, werden sie in die Online-Ausgabe aufgenommen.</xsl:text>
                </p><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <h3>Inhaltsübersicht</h3><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <!-- inhaltsverzeichnis anlegen, als sprungmarken dienen die IDs der register-sections -->
                <ul class="synopsis-inhalt"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- jede register-überschrift ansteuern und ein :li anlegen-->
                    <xsl:for-each select="index">
                        <xsl:variable name="v_index_id" select="@section-id"/>
                        <xsl:if test="title and $v_index_id">
                        <li>
                            <a>
                                <xsl:attribute name="href">
                                    <xsl:value-of select="concat($p_page_id, '#', $v_index_id)"/>
                                </xsl:attribute>                                
                                <xsl:value-of select="nr"/>
                                <xsl:text>. </xsl:text>
                                <xsl:value-of select="title"/>
                            </a>
                        </li>
                        </xsl:if>
                    </xsl:for-each>
                </ul><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <!-- einzelregister ansteuern und an template verweisen -->
                <xsl:for-each select="index">
                    <xsl:if test="title and @section-id">
                        <xsl:apply-templates select="."></xsl:apply-templates>
                    </xsl:if>
                </xsl:for-each>

            </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- einzelregister -->
    <xsl:template match="index">
        <xsl:param name="p_index_id" select="@section-id"></xsl:param>
        <!-- sprungmarke -->
        <span id="{$p_index_id}"></span><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- überschrift -->
        <h3 class="reg-header"><xsl:value-of select="concat(nr,'. ',title)"/></h3><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- vorbemerkung -->
        <xsl:for-each select="note/p">
            <p class="reg-single-note"><xsl:apply-templates></xsl:apply-templates></p>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
        
        <!-- registereinträge aufrufen -->
        <ul class="reg-items"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:call-template name="items">
                <!-- Neu gemischte Register neu sortieren -->
                <!-- Das muss für personnames hier passieren, weil sonst die Verweise nicht mit einsortiert werden -->
                <xsl:with-param name="reorder" select="@type='personnames'"/>
            </xsl:call-template>
        </ul>
    </xsl:template>
    
    <xsl:template name="items">
        <!-- Set to true() for reordering all lemmata in alphabetical order -->
        <xsl:param name="reorder" select="false()" />

        <xsl:choose>
            <xsl:when test="$reorder = true()">
                <xsl:for-each select="item|item_italic|group">
                    <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                    <xsl:call-template name="subitem"><xsl:with-param name="reorder" select="$reorder" /></xsl:call-template>
                </xsl:for-each>
            </xsl:when>
            <xsl:otherwise>
                <xsl:for-each select="item|item_italic|group">
                    <xsl:call-template name="subitem"><xsl:with-param name="reorder" select="$reorder" /></xsl:call-template>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="subitem">
        <xsl:param name="reorder" select="false()" />
        <li>
            <xsl:call-template name="item" />
            <xsl:choose>
                <!-- einzeln stehende (solitäre) untereinträge werden an den übergeordneten eintrag herangezogen -->
                <xsl:when test="count(.//*[self::item or self::item_italic])=1 and not(nrn) and not(crossRefs) and not(group) and not(self::group)">
                    <xsl:choose>
                        <!-- der anschluss erfolgt registerspezifisch mit oder ohne komma -->
                        <!-- TODO: keine hart kodierten Lemmabestandteile im Stylesheet -->
                        <xsl:when test="ancestor::index[@type='epithets'] and ancestor::group[starts-with(@groupname,'lat')]">
                            <xsl:text> </xsl:text>
                        </xsl:when>
                        <xsl:otherwise><xsl:text>, </xsl:text></xsl:otherwise>
                    </xsl:choose>
                    <!-- untereintrag ansteuern -->
                    <xsl:for-each select="item|item_italic">
                        <span><xsl:call-template name="item" /></span>
                    </xsl:for-each>
                </xsl:when>
                
                <xsl:otherwise>
                    <xsl:if test="item|item_italic|group">
                        <ul>
                            <xsl:call-template name="items"><xsl:with-param name="reorder" select="$reorder" /></xsl:call-template>
                        </ul>
                    </xsl:if>                    
                </xsl:otherwise>
            </xsl:choose>
        </li>
    </xsl:template>

    <xsl:template name="item">
        <xsl:variable name="v_level" select="@level + 4"/>
        <xsl:copy-of select="@id"/>
        <xsl:attribute name="class">
            <xsl:choose>
                <xsl:when test="self::item or self::item_italic">
                    <xsl:choose>
                        <xsl:when test="ancestor::index[@group='initials'] and not(parent::item or parent::item_italic) and not(lemma/@sortchart = following-sibling::*[name()='item' or name()='item_italic'][1]/lemma/@sortchart)">item initialende</xsl:when>
                        <xsl:otherwise>item</xsl:otherwise>
                    </xsl:choose>                            
                </xsl:when>
                <!-- item initialende -->
                <xsl:when test="self::group">
                    <xsl:choose>
                        <xsl:when test="lemma">gruppe level<xsl:value-of select="$v_level"/></xsl:when>
                        <xsl:otherwise>gruppe</xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
            </xsl:choose>
        </xsl:attribute>
        
        <xsl:apply-templates select="lemma" />
        <xsl:if test="ancestor::index[@type='blazons']"><xsl:call-template name="blazons" /></xsl:if>
        <xsl:if test="nrn"><xsl:text> </xsl:text></xsl:if>
        
        <xsl:call-template name="links" />
        <xsl:if test="nrn and crossRefs"><xsl:text>;</xsl:text></xsl:if>
        
        <xsl:apply-templates select="crossRefs" />
    </xsl:template>
    
    <xsl:template name="links">
        <xsl:for-each select="nrn/nr">
            <a>
                <xsl:attribute name="href">/<xsl:value-of select="$p_volume_identifier"/>/<xsl:value-of select="epi:pad-with-suffix(., 4)"/></xsl:attribute>
                <xsl:if test="@lost='1'"><xsl:text disable-output-escaping="yes">&lt;em&gt;</xsl:text></xsl:if>
                <xsl:apply-templates select="."></xsl:apply-templates>
                <xsl:if test="@lost='1'"><xsl:text disable-output-escaping="yes">&lt;/em&gt;</xsl:text></xsl:if>
            </a><xsl:if test="following-sibling::nr"><xsl:text>, </xsl:text></xsl:if>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="lemma">
        <xsl:choose>
        <xsl:when test="parent::item_italic"><em> <xsl:value-of select="translate(normalize-space(.), '{}', '')"/></em></xsl:when>
        <xsl:otherwise> <xsl:value-of select="translate(normalize-space(.), '{}', '')"/></xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="crossRefs[*]">
        <xsl:choose>
            <xsl:when test="preceding-sibling::nrn or preceding-sibling::item or following-sibling::nrn or following-sibling::item">
                <xsl:if test="not(preceding-sibling::crossRefs)"> s. a. </xsl:if>
            </xsl:when>
            <xsl:otherwise>
                <xsl:if test="not(preceding-sibling::crossRefs)"> s. </xsl:if>
            </xsl:otherwise>
        </xsl:choose>
        <xsl:for-each select="see|see_also"><xsl:apply-templates select="."/></xsl:for-each>
        <xsl:if test="following-sibling::crossRefs">, </xsl:if>
    </xsl:template>
    
    <xsl:template match="see|see_also">
            
            <!-- Find the leaves, i.e. the deepest visible see/see_also items: those with no visible descendants -->
            <xsl:variable name="thread" select=".[not(@hidden='1')] | .//see[not(@hidden='1')] | .//see_also[not(@hidden='1')]"/>
            <xsl:variable name="deepest" select="$thread[not(.//see[not(@hidden='1')] or .//see_also[not(@hidden='1')])]" />
            
            <xsl:for-each select="$deepest">
                <a>        
                    <xsl:attribute name="href">
                        <xsl:text>#</xsl:text>
                        <xsl:value-of select="@id"/>
                    </xsl:attribute>
                    
                    <!-- Collect lemma values in this element and its descendants excluding hidden see/see_also and ancestors in the same item -->            
                    <xsl:variable name="lemmaNodes" as="element()*" select="ancestor-or-self::see[not(@hidden='1')] | ancestor-or-self::see_also[not(@hidden='1')]" />
                                
                    <!-- Exclude last lemma node if its string value equals $itemLemma's string value -->
                    <xsl:variable name="itemLemma" select="ancestor::item[1]/lemma"/>
                    <xsl:variable name="filteredLemmaNodes" as="element()*">
                        <xsl:sequence select="
                            if ((string($lemmaNodes[last()]/lemma) = string($itemLemma)) and (count($lemmaNodes) gt 1))
                            then $lemmaNodes[position() lt last()]
                            else $lemmaNodes
                        "/>
                    </xsl:variable>
                    
                    <!-- Join path -->
                    <xsl:value-of select="string-join($filteredLemmaNodes/lemma, '&#xA0;› ')"/>
                </a>
                <xsl:if test="position() lt last()">, </xsl:if>
            </xsl:for-each>       
        <xsl:if test="following-sibling::see or following-sibling::see_also">, </xsl:if>
    </xsl:template>
    
    <xsl:template name="blazons">: <xsl:apply-templates select="shield/p"/><xsl:if test="crest">;  <xsl:apply-templates select="crest/p"/></xsl:if><xsl:if test="biblio">; <xsl:apply-templates select="biblio/p"/></xsl:if><xsl:if test="not(ends-with(biblio/p, '.'))"><xsl:text>.</xsl:text></xsl:if></xsl:template>


</xsl:stylesheet>

