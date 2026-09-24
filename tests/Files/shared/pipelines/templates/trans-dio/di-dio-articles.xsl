<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:epi="http://epigraf.inschriften.net#xslt-functions"
    xmlns:php="http://php.net/xsl"
    exclude-result-prefixes="xs epi php"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/>
    
    <xsl:import href="di-dio-tools.xsl"/>
    <xsl:import href="di-dio-links.xsl"/>
    
    <!-- Hier werden die spitzen Klammern, die nicht als Escape-Zeichenfolgen ausgegeben werden sollen, deklariert. -->
    <xsl:output method="xml" use-character-maps="myMap"/>
    <xsl:character-map name="myMap">
        <xsl:output-character character="≤" string="&lt;"/>
        <xsl:output-character character=">" string="&gt;"/>
    </xsl:character-map>
    
    
    <!-- 
        in diesem stylesheet wird der katalog der inschriftenartikel (element <articles>)
        auf der transformationsstufe 3 weiter strukturiert.
        
        es wird aufgerufen in di-dio.xsl
    -->

    <xsl:template match="articles" mode="page">
        <xsl:param name="p_iri"><xsl:value-of select="$p_volume_iri"/>-page-inscriptions</xsl:param>
        
        <page type="articles" iri="{$p_iri}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <title>Inschriften</title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>2</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
            <doktype>4</doktype><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <layout>1</layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <tx_realurl_pathsegment>inschriften-liste</tx_realurl_pathsegment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <tx_realurl_exclude>1</tx_realurl_exclude><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
            <tx_hisodat_sources>
                <xsl:for-each select="article">
                    <xsl:apply-templates select="." />                
                </xsl:for-each>
            </tx_hisodat_sources>
        </page><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <page type="articles" iri="{$p_iri}-sources-list" parent_iri="{$p_iri}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <title>Inschriften</title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
            <doktype>1</doktype><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <layout>1</layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <tt_content iri="{$p_iri}-sources-list"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>list</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header>Inschriftenkatalog: <xsl:value-of select="$p_volume_title"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>2</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <list_type>hisodat_sources</list_type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <pi_flexform>
                    <T3FlexForms>
                        <data>
                            <sheet index="sDEF">
                                <language index="lDEF">
                                    <field index="settings.recordPids.sources">
                                        <value index="vDEF">###UIDOFCOLLECTION:<xsl:value-of select="$p_volume_signature"/>###</value>
                                    </field>
                                    <field index="settings.FF.recursive">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.targetPid">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.originPid">
                                        <value index="vDEF"></value>
                                    </field>
                                </language>
                            </sheet>
                            <sheet index="sLIST">
                                <language index="lDEF">
                                    <field index="settings.orderBy">
                                        <value index="vDEF">10</value>
                                    </field>
                                    <field index="settings.ascDesc">
                                        <value index="vDEF">10</value>
                                    </field>
                                    <field index="settings.itemsPerPage">
                                        <value index="vDEF">10</value>
                                    </field>
                                </language>
                            </sheet>
                            <sheet index="sREGISTERS">
                                <language index="lDEF">
                                    <field index="settings.registerPids.FF.persons">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.registerPids.FF.localities">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.registerPids.FF.entities">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.registerPids.FF.events">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.registerPids.FF.keywords">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.registerPids.FF.archives">
                                        <value index="vDEF"></value>
                                    </field>
                                </language>
                            </sheet>
                        </data>
                    </T3FlexForms></pi_flexform><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </page><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
        <page type="articles" iri="{$p_iri}-sources-view" parent_iri="{$p_iri}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <title>Inschrift</title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>2</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
            <doktype>1</doktype><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <layout>1</layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <tt_content iri="{$p_iri}-sources-view"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>list</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <list_type>hisodat_sources</list_type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <pi_flexform>
                    <T3FlexForms>
                        <data>
                            <sheet index="sDEF">
                                <language index="lDEF">
                                    <field index="settings.recordPids.sources">
                                        <value index="vDEF">###UIDOFCOLLECTION:<xsl:value-of select="$p_volume_signature"/>###</value>                                        
                                    </field>
                                    <field index="settings.FF.recursive">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.targetPid">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.originPid">
                                        <value index="vDEF"></value>
                                    </field>
                                </language>
                            </sheet>
                            <sheet index="sLIST">
                                <language index="lDEF">
                                    <field index="settings.orderBy">
                                        <value index="vDEF">10</value>
                                    </field>
                                    <field index="settings.ascDesc">
                                        <value index="vDEF">10</value>
                                    </field>
                                    <field index="settings.itemsPerPage">
                                        <value index="vDEF">10</value>
                                    </field>
                                </language>
                            </sheet>
                            <sheet index="sREGISTERS">
                                <language index="lDEF">
                                    <field index="settings.registerPids.FF.persons">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.registerPids.FF.localities">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.registerPids.FF.entities">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.registerPids.FF.events">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.registerPids.FF.keywords">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="settings.registerPids.FF.archives">
                                        <value index="vDEF"></value>
                                    </field>
                                </language>
                            </sheet>
                        </data>
                    </T3FlexForms></pi_flexform>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </page><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
    </xsl:template>    
    
    
    <xsl:template match="articles" mode="sources">
        <tx_hisodat_sources>
            <xsl:for-each select="article">
                <xsl:apply-templates select=".">
            </xsl:apply-templates></xsl:for-each>
        </tx_hisodat_sources>
    </xsl:template>
    
    <xsl:template match="article">
        <!-- IRI-Fragment des Artikels -->
        <xsl:param name="p_iri"><xsl:value-of select="$p_volume_iri"/>-<xsl:value-of select="@id"/></xsl:param>

        <!-- laufnummer des artikels -->
        <xsl:param name="p_index"><xsl:value-of select="headline/articlenumber"/></xsl:param>
        
        <!-- bezeichner des artikels: laufnummer oder bezeichner -->
        <xsl:param name="p_signature">
            <!-- wenn nach signatur sortiert und nummeriert werden soll, 
                werden die signaturen, die gegebenenfalls a-nummern enthalten können,
                ausgelsesen,
                anderenfalls die aus dem bestand ermittelten laufnummern
            -->
            <xsl:choose>
                <!-- signatur = artikelnummer im gedruckten band -->
                <xsl:when test="$sw_sortieren_nach_signatur=1">
                    <xsl:value-of select="lower-case(headline/signature)"/>
                </xsl:when>
                <!-- neu ermittelte laufnummer -->
                <xsl:otherwise>
                    <xsl:value-of select="lower-case(headline/articlenumber)"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:param>
        
        <!-- die beiden folgenden parameter setzen bei a-nummer, die ziffernfolge vor dem buchstaben 
                auf vier stellen, gegebenfalls mit führenden nullen   -->
        
        <!-- TODO: Funktion epi:pad-with-suffix() aus epi-functions.xsl verwenden -->
        <xsl:param name="p_split_signature">
            <!-- die signatur in ziffernfolge und buchstaben zerlegen -->
            <xsl:for-each select="$p_signature">
                <xsl:analyze-string select="." regex="([0-9]+)([a-zA-Z])">
                    <xsl:matching-substring>
                        <digits><xsl:value-of select="regex-group(1)"/></digits>
                        <letter><xsl:value-of select="regex-group(2)"/></letter>
                    </xsl:matching-substring>
                    <xsl:non-matching-substring><digits><xsl:value-of select="."/></digits></xsl:non-matching-substring>
                </xsl:analyze-string>
            </xsl:for-each>
        </xsl:param>

        <xsl:param name="p_article-number">
            <!-- die ziffernfolge auf vier stellen hochrechnen und den buchstaben anfügen -->
            <xsl:for-each select="$p_split_signature">
                <xsl:number value="digits" format="0001" /><xsl:value-of select="letter"/>
            </xsl:for-each>
        </xsl:param>

        <xsl:param name="p_article_dio_identifier">
            <xsl:value-of select="$p_volume_identifier"/>
            <xsl:text>-</xsl:text>
            <xsl:value-of select="$p_article-number"/>
        </xsl:param>

        <xsl:param name="p_article_number_reduct">
            <!-- aus  ziffernfolge führende nullen entfernen und den buchstaben anfügen -->
            <xsl:for-each select="$p_split_signature">
                <xsl:number value="digits" format="1"></xsl:number><xsl:value-of select="letter"/>
            </xsl:for-each>
        </xsl:param>

        <!--<xsl:param name="p_article-number"><xsl:number value="@nr" format="0001"></xsl:number></xsl:param>-->
        <xsl:param name="p_preID"><xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="$p_article-number"/></xsl:param>
        
        <!-- Ermittlung der Anzahl der descriptions zur Identifizierung komplexer Artikel -->
        
        <xsl:param name="p_count_descriptions" select="count(./di_description)"/>
        
        <!-- Sind alle Inschriftenteile verloren? -->
        
        <xsl:param name="p_all_lost">
            <xsl:choose>
                <xsl:when test="not(./inscription/inscriptionpart[@lost != '1']) and ./headline/trad_sigle = '†'">1</xsl:when>
                <xsl:otherwise>0</xsl:otherwise>
            </xsl:choose>
        </xsl:param>
        
        
        <!-- parametertest 
        <parametertest1><xsl:copy-of select="$p_index"></xsl:copy-of></parametertest1> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <parametertest2><xsl:copy-of select="$p_signature"></xsl:copy-of></parametertest2> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        -->
        
        <!-- artikel -->
        <dio_record>
            <!-- bezeichner des artikels, gegbenenfalls mit a-nummern  -->
            <xsl:attribute name="signatur"><xsl:value-of select="$p_article_dio_identifier"/></xsl:attribute>
            <!-- laufnummer des artikels, wenn a-nummern vorkommen, kann weicht die laufnummer vom bezeichner ab -->
            <xsl:attribute name="index"><xsl:value-of select="$p_index"/></xsl:attribute>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- kopfzeile -->
            <record_header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- bezeichner des artikels, gegbenenfalls mit a-nummern  -->
                <tx_hisodat_identifier><xsl:value-of select="$p_article_dio_identifier"/></tx_hisodat_identifier><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <tx_hisodat_signature><xsl:value-of select="$p_article_number_reduct"/></tx_hisodat_signature><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- urn -->
                <tx_hisodat_signature_add><xsl:value-of select="$p_dio_urn_volume"/></tx_hisodat_signature_add><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

                <tx_dio_cssclass><xsl:if test="outputoptions/outputoption[@norm_iri='di_synopsis']">di_synopsis</xsl:if></tx_dio_cssclass>

                <!-- überlieferungszustand -->
                <tx_dio_preservation>
                    <xsl:for-each select="headline/trad_sigle">
                        <xsl:choose>
                            <xsl:when test=". = ''">
                                <xsl:text>0</xsl:text>
                            </xsl:when>
                            <xsl:when test=". = '†'">
                                <xsl:text>1</xsl:text>
                            </xsl:when>
                            <xsl:when test=". = '(†)'">
                                <xsl:text>2</xsl:text>
                            </xsl:when>
                            <xsl:when test=". = '†?'">
                                <xsl:text>3</xsl:text>
                            </xsl:when>
                            <xsl:when test=". = '(†?)'">
                                <xsl:text>4</xsl:text>
                            </xsl:when>
                            <xsl:when test=". = '†(?)'">
                                <xsl:text>5</xsl:text>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:text>?</xsl:text>
                            </xsl:otherwise>
                        </xsl:choose>  
                    </xsl:for-each>    
                </tx_dio_preservation><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

                <!-- standort(e) -->
                <tx_dio_title>
                    <xsl:for-each select="headline/locations/location">
                        <xsl:value-of select="."/>
                        <xsl:if test="following-sibling::location"><xsl:text>, </xsl:text></xsl:if>
                    </xsl:for-each>
                </tx_dio_title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
               <!-- datierung -->
                <tx_hisodat_date_start>
                    <xsl:choose>
                        <xsl:when test="string(headline/dating/date_start) and not(headline/dating/date_start = '')">
                            <xsl:value-of select="format-number(headline/dating/date_start, '0000')" />
                        </xsl:when>
                        <xsl:otherwise></xsl:otherwise>
                    </xsl:choose>                                        
                </tx_hisodat_date_start><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <tx_hisodat_date_end>
                    <xsl:choose>
                        <xsl:when test="string(headline/dating/date_end) and not(headline/dating/date_end = '')">
                            <xsl:value-of select="format-number(headline/dating/date_end, '0000')" />
                        </xsl:when>
                        <xsl:otherwise></xsl:otherwise>
                    </xsl:choose>
                </tx_hisodat_date_end><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <tx_hisodat_date_sorting><xsl:value-of select="headline/dating/date_sort" /></tx_hisodat_date_sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <tx_hisodat_date_comment><xsl:value-of select="headline/dating/date_value" /></tx_hisodat_date_comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </record_header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <record_body><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- beschreibung -->
                <tx_hisodat_short>
                    <xsl:choose>
                        <xsl:when test="$p_count_descriptions = 1">
                            <xsl:apply-templates select="di_description" />                            
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:apply-templates select="di_description[1]" />
                        </xsl:otherwise>
                    </xsl:choose>
                </tx_hisodat_short><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <!-- allgemeine angaben -->
                <tx_dio_addinfos>
                    <!-- inschrift nach -->
                    <xsl:if test="di_generals/di_generals_source"><xsl:apply-templates select="di_generals/di_generals_source"/>
                        <xsl:if test="di_generals/di_generals_addition"><xsl:text> </xsl:text></xsl:if>
                    </xsl:if>
                    <!-- ergänzung nach -->
                    <xsl:if test="di_generals/di_generals_addition"><xsl:apply-templates select="di_generals/di_generals_addition"/></xsl:if>
                </tx_dio_addinfos><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <tx_dio_dimensions>
                    <!-- abmessungen des objekts -->
                    <xsl:if test="di_generals/di_generals_measure">
                        <xsl:apply-templates select="di_generals/di_generals_measure"/>
                        <xsl:variable name="measures" select="normalize-space(string(di_generals/di_generals_measure))"/>
                        <xsl:choose>
                            <xsl:when test="$measures != '' and ends-with($measures, '.')"/>
                            <xsl:otherwise>
                                <xsl:text>.</xsl:text>
                            </xsl:otherwise>
                        </xsl:choose>
                        <xsl:if test="di_generals/di_generals_fontsize">
                            <xsl:text> – </xsl:text>
                        </xsl:if>
                    </xsl:if>
                    <!-- schrifthöhe -->
                    <xsl:if test="di_generals/di_generals_fontsize"><xsl:apply-templates select="di_generals/di_generals_fontsize"/>
                        <xsl:if test="not(di_generals/di_generals_fontsize/p)">
                            <xsl:text>.</xsl:text>
                        </xsl:if>
                    </xsl:if>		  
                </tx_dio_dimensions><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <!-- schriftarten -->
                <tx_dio_typeface>
                    <xsl:if test="di_generals/di_generals_fonttype">
                        <xsl:apply-templates select="di_generals/di_generals_fonttype"/>
                    </xsl:if>		
                </tx_dio_typeface>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <!-- inschriften -->
                <tx_hisodat_sourcetext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:choose>
                        <!-- zur synoptischen darstellung der versionen einer inschrift
                            ein spezielles template aufrufen  -->
                        <xsl:when test="outputoptions/outputoption[@norm_iri='di_synopsis']">
                            <xsl:call-template name="synopsis">
                                <xsl:with-param name="p_preID" select="$p_preID"/>
                            </xsl:call-template>
                        </xsl:when>
                        <!-- wenn keine synoptische darstellung verlangt wird:  -->
                        <xsl:otherwise>
                            <xsl:if test="$p_count_descriptions = 1">
                                <xsl:variable name="v_inscription_block"
                                    select="
                                    let $all := di_generals/following-sibling::* 
                                    return
                                    if (exists($all[self::di_comment]))
                                    then $all[position() le index-of($all, $all[self::di_comment][1])]
                                    else $all
                                    "/>
                                
                                <xsl:for-each select="$v_inscription_block">
                                    <xsl:variable name="pos" select="position()" />
                                    <xsl:variable name="prev" select="$v_inscription_block[$pos - 1]" />
                                    <xsl:variable name="next" select="$v_inscription_block[$pos + 1]" />
                                    
                                    <xsl:choose>
                                        
                                        <xsl:when test="self::di_header1">
                                            <description class="di_header1">
                                                <xsl:apply-templates />
                                            </description>
                                            <xsl:text>&#x000A;</xsl:text>
                                        </xsl:when>
                                        
                                        <xsl:when test="self::di_header2">
                                            <xsl:for-each select="p">
                                                <description class="di_header2">
                                                    <xsl:apply-templates />
                                                </description>
                                                <xsl:text>&#x000A;</xsl:text>
                                            </xsl:for-each>
                                            <xsl:text>&#x000A;</xsl:text>
                                        </xsl:when>
                                        
                                        <xsl:when test="self::inscription">
                                            
                                            <!-- open <inscription><sco> if first in group -->
                                            <xsl:if test="not($prev/self::inscription)">
                                                <xsl:text disable-output-escaping="yes">&lt;inscription&gt;&#x000A;</xsl:text>
                                                <xsl:text disable-output-escaping="yes">&lt;sco&gt;&#x000A;</xsl:text>
                                            </xsl:if>
                                            
                                            <xsl:call-template name="inscriptionparts">
                                                <xsl:with-param name="p_preID" select="$p_preID"/>
                                                <xsl:with-param name="p_all_lost" select="$p_all_lost"/>
                                            </xsl:call-template>
                                            
                                            <!-- close </sco></inscription> if last in group -->
                                            <xsl:if test="not($next/self::inscription)">
                                                <xsl:text disable-output-escaping="yes">&lt;/sco&gt;&#x000A;</xsl:text>
                                                <xsl:text disable-output-escaping="yes">&lt;/inscription&gt;&#x000A;</xsl:text>
                                            </xsl:if>
                                            
                                        </xsl:when>
                                        
                                    </xsl:choose>
                                </xsl:for-each>
                            </xsl:if>                            
                        </xsl:otherwise>
                    </xsl:choose>
                    
                    
                </tx_hisodat_sourcetext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <tx_dio_orig_inscription>  
                    <xsl:for-each select=".//bl">
                        <xsl:for-each select=".//text()[not(ancestor::app1)][not(ancestor::app2)]"><xsl:value-of select="."/><xsl:text> </xsl:text></xsl:for-each>
                    </xsl:for-each>
                </tx_dio_orig_inscription><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <tx_dio_translation>
                    <xsl:if test="$p_count_descriptions = 1">
                        <xsl:for-each select="translations/translation">
                            <xsl:call-template name="translation">
                                <xsl:with-param name="p_article-number" select="$p_article-number"/>
                            </xsl:call-template>
                        </xsl:for-each>
                    </xsl:if>
                </tx_dio_translation><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <tx_dio_biblecite>
                    <!-- bibelzitate, nur bei der münchener arbeitsstelle
                    aus der section mit dem key: di_citation_source-->
                    <xsl:apply-templates select="di_citation_source"></xsl:apply-templates>
                </tx_dio_biblecite><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <tx_dio_dateoninscription>
                    <xsl:for-each select="di_date_on_inscription"><xsl:apply-templates></xsl:apply-templates></xsl:for-each>
                </tx_dio_dateoninscription><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <tx_dio_metre><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:if test="$p_count_descriptions = 1">
                        <xsl:call-template name="metre">
                            <xsl:with-param name="metresNode" select="di_metres"/>
                        </xsl:call-template>
                    </xsl:if>
                </tx_dio_metre><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <tx_dio_heraldry><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:if test="$p_count_descriptions = 1">
                        <xsl:apply-templates select="heraldry">
                        </xsl:apply-templates>
                    </xsl:if>
                </tx_dio_heraldry><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <tx_hisodat_description><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:choose>
                        <xsl:when test="$p_count_descriptions > 1">
                            
                            <!-- Verarbeiten und Einfügen komplexer Inschriften -->
                            
                            <xsl:variable name="v_comment_extended" select="let $all := di_generals/following-sibling::* return $all[position() le index-of($all, $all[self::di_comment][1])]"/>
                            <xsl:variable name="v_comment_extended1">
                                <xsl:for-each select="$v_comment_extended">
                                    <xsl:choose>
                                        <xsl:when test="self::di_description">
                                            <description>
                                                <xsl:for-each select="p">
                                                    <p><xsl:apply-templates select="."/></p>
                                                </xsl:for-each>
                                            </description><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        </xsl:when>
                                        <xsl:when test="self::di_header1">
                                            <description class="di_header1">
                                                <em><xsl:value-of select="."/></em>
                                            </description><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        </xsl:when>
                                        <xsl:when test="self::di_header2">
                                            <description class="di_header2">
                                                <em><xsl:value-of select="."/></em>
                                            </description><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        </xsl:when>
                                        <xsl:when test="self::inscription">
                                            <xsl:if test="not(name(preceding-sibling::*[1]) = name())">
                                                <xsl:text>≤inscription>&#x000A;≤sco>&#x000A;</xsl:text>
                                            </xsl:if>
                                            <xsl:call-template name="inscriptionparts">
                                                <xsl:with-param name="p_preID" select="$p_preID"/>
                                                <xsl:with-param name="p_all_lost" select="$p_all_lost"/>
                                            </xsl:call-template>
                                            <xsl:if test="not(following-sibling::*[1][name() = name(current())])">
                                                <xsl:text>≤/sco>&#x000A;</xsl:text>
                                                <xsl:if test="following-sibling::*[1][self::translations/translation]">
                                                    <translation>
                                                        <xsl:for-each select="following-sibling::*[1]/translation">
                                                            <xsl:call-template name="translation">
                                                                <xsl:with-param name="p_article-number" select="$p_article-number"/>
                                                            </xsl:call-template>
                                                        </xsl:for-each>
                                                    </translation>
                                                </xsl:if>
                                                <xsl:text>≤/inscription>&#x000A;</xsl:text>
                                            </xsl:if>
                                        </xsl:when>
                                        <xsl:when test="self::di_metres">
                                            <xsl:if test="normalize-space(.) != ''">
                                                <metre>
                                                    <xsl:call-template name="metre">
                                                        <xsl:with-param name="metresNode" select="."/>
                                                    </xsl:call-template>
                                                </metre><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                            </xsl:if>
                                        </xsl:when>
                                        <xsl:when test="self::heraldry">
                                            <heraldry><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                                <caption>
                                                    <xsl:apply-templates select="./title"/>
                                                </caption>
                                                <xsl:apply-templates select=".">
                                                </xsl:apply-templates>
                                            </heraldry><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        </xsl:when>
                                        <xsl:when test="self::di_comment">
                                            <commentary>
                                                <p><strong>Kommentar</strong></p>
                                                <xsl:for-each select="p">
                                                    <p><xsl:apply-templates select="."/></p>
                                                </xsl:for-each>
                                            </commentary>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <xsl:if test="not(self::translations) and not(self::di_metres)">
                                                <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                                
                                            </xsl:if>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </xsl:for-each>
                            </xsl:variable>
                            <xsl:copy-of select="$v_comment_extended1"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:for-each select="di_comment">
                                <xsl:apply-templates select="." />
                            </xsl:for-each>
                        </xsl:otherwise>
                    </xsl:choose>
                </tx_hisodat_description><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <tx_dio_annotations><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="footnotes/letter_footnotes/item">
                        <!-- die id des entry-elements wird aus dem vorsatz "ants" (für annotations) und der id des item gebildet  -->
                        <xsl:variable name="v_item_id"><xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="$p_article-number"/>-<xsl:value-of select="@itemID"/></xsl:variable>
                        <xsl:variable name="v_target_id"><xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="$p_article-number"/>-pk<xsl:value-of select="@pkID"/></xsl:variable>
                        <entry>
                            <xsl:if test="$sw_links_footnotes=1">
                                <xsl:attribute name="id" select="$v_item_id"/>
                                <xsl:attribute name="target">#<xsl:value-of select="$v_target_id"/></xsl:attribute>
                            </xsl:if>
                            <xsl:apply-templates/>
                        </entry><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </tx_dio_annotations><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <tx_dio_footnotes><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="footnotes/digit_footnotes/item">
                        <!-- die id des entry-elements wird aus dem vorsatz "fnts" (für footnotes) und der id des item gebildet  -->
                        <xsl:variable name="v_item_id"><xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="$p_article-number"/>-<xsl:value-of select="@itemID"/></xsl:variable>
                        <xsl:variable name="v_target_id"><xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="$p_article-number"/>-pk<xsl:value-of select="@pkID"/></xsl:variable>
                        <entry>
                            <xsl:if test="$sw_links_footnotes=1">
                                <xsl:attribute name="id" select="$v_item_id"/>
                                <xsl:attribute name="target">#<xsl:value-of select="$v_target_id"/></xsl:attribute>
                            </xsl:if>
                            <xsl:apply-templates/>
                        </entry><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>	
                </tx_dio_footnotes><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <tx_dio_literature><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="references/item">
                        <entry>
                            <xsl:apply-templates/>
                        </entry><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>       
                    </xsl:for-each>
                </tx_dio_literature><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <!--tx_dio_corrigenda></tx_dio_corrigenda--><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
            </record_body><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:if test="ancestor::book/options/@images = 1">
                <record_images><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="images/item">
                        <!-- Muss dem Dateinamen entsprechen, der in der Pipeline Dev:DIO-Bilder gebildet wird: 
                            {root.project.description|json:di_prefix}-{root.project.description|json:di_number}_{root.signature}_{sortno|padzero:2}.{file_type}" }
                         -->
                        <xsl:variable name="filename">
                            <xsl:value-of select="$p_volume_prefix"/>
                            <xsl:text>-</xsl:text>                                
                            <xsl:value-of select="epi:pad($p_volume_number, 2)"/>
                            <xsl:text>_</xsl:text>
                            <xsl:value-of select="lower-case(ancestor::article/headline/signature)"/>
                            <xsl:text>_</xsl:text>
                            <xsl:value-of select="epi:pad(@sortno,2)"/>
                            <xsl:text>.</xsl:text>
                            <xsl:value-of select="replace(file_type,'tif','jpg')" />
                        </xsl:variable>
                        
                        <xsl:variable name="filepath">
                            <xsl:text>/user_upload/abbildungen/</xsl:text>
                            <xsl:value-of select="$p_volume_prefix"/>
                            <xsl:text>-</xsl:text>                            
                            <xsl:value-of select="$p_volume_number"/>
                        </xsl:variable>                        
                        <filepath><xsl:value-of select="$filepath"/>/<xsl:value-of select="$filename"/></filepath><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </record_images><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:if>
            <xsl:if test="di_corrigenda/item">
                <record_corrigenda><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="di_corrigenda/item">
                        <item iri="{$p_iri}-corrigenda-{@id}" type="1"><xsl:apply-templates select="." /></item>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </record_corrigenda><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:if>
        </dio_record><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- artikelnummern -->
    <xsl:template match="signature">
        <!-- führende nullen entfernen -->
        <xsl:number select="." format="1"></xsl:number>
<!--        <xsl:choose>
            <xsl:when test="substring(.,1,1)='0'">
                <xsl:choose>
                    <xsl:when test="substring(.,2,1)='0'">
                        <xsl:value-of select="substring(.,3)" />
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:value-of select="substring(.,2)" />
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="." />
            </xsl:otherwise>
        </xsl:choose>-->
    </xsl:template>
    
    <!--Fussnotenzeichen im Text / footnote characters-->
    <!--1. der Zahlenapparat-->
    <xsl:template match="app1[ancestor::articles]">
        <xsl:param name="p_targetID"><xsl:value-of select="ancestor::article/footnotes/digit_footnotes/item[@pkID=current()/@id]/@itemID"/></xsl:param>
        <xsl:choose>
            <xsl:when test="$sw_links_footnotes=1">
                <a>
                    <xsl:attribute name="id"><xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="epi:pad-with-suffix(ancestor::article/@nr, 4)" />-pk-<xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="href">#<xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="epi:pad-with-suffix(ancestor::article/@nr, 4)"/>-<xsl:value-of select="$p_targetID"/></xsl:attribute>
                    <sup>
                        <xsl:number level="any" from="article" count="app1|loc_ten" format="1)"/>
                    </sup>
                </a>
            </xsl:when>
            <xsl:otherwise>
                <sup><xsl:number level="any" from="article" count="app1|loc_ten" format="1)"/></sup>
            </xsl:otherwise>
        </xsl:choose>
        
        
    </xsl:template>
    <!--2. der Buchstabenapparat-->
    <xsl:template match="app2">
        <xsl:param name="p_targetID"><xsl:value-of select="ancestor::article/footnotes/letter_footnotes/item[@pkID=current()/@id]/@itemID"/></xsl:param>
        <xsl:choose>
            <!-- TODO: a-Nummern berücksichtigen -->
            <xsl:when test="$sw_links_footnotes=1">
                <a>
                    <xsl:attribute name="id"><xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="epi:pad-with-suffix(ancestor::article/@nr, 4)" />-pk-<xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="href">#<xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="epi:pad-with-suffix(ancestor::article/@nr, 4)" />-<xsl:value-of select="$p_targetID"/></xsl:attribute>
                    <sup>
                        <xsl:number level="any" from="article" count="app2" format="a)"/>
                    </sup>
                </a>
            </xsl:when>
            <xsl:otherwise>
                <sup><xsl:number level="any" from="article" count="app2" format="a)"/></sup>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    

    <!--Zitate in Fussnoten / quotations in footnotes-->
    <xsl:template match="quot[*|text()]"><em><xsl:apply-templates/></em></xsl:template>
    
    <!--Formate in den Transkriptionen-->
    <!--Versalien / ornamental initials-->
    <xsl:template match="vsl[not(ancestor::app2)]">
        <xsl:apply-templates/>
    </xsl:template>
    
    <!--Initialen / initials-->
    <xsl:template match="ini">
        <xsl:apply-templates/>
    </xsl:template>
    
    <!--hochgestellte Buchstaben / elevated letters-->
    <xsl:template match="sup[*|text()]">
        <xsl:choose>
            <xsl:when test="(current()='o' or current()='c') and not(@value='keine Jahreszahl')">
                <sup><xsl:apply-templates/></sup>
            </xsl:when>
            <xsl:otherwise><xsl:apply-templates/></xsl:otherwise> 
        </xsl:choose>
    </xsl:template>
    
    <!-- In the introduction -->
    <xsl:template match="vz[ancestor::introduction or ancestor::prefaces]">
        <xsl:if test="@indent='0' and ./node()">
            <span class="line">
                <xsl:call-template name="zeilennummer"/>
                <xsl:apply-templates/>
            </span>
        </xsl:if>
        <xsl:if test="@indent='1'">
            <span class="line indent">
                <xsl:call-template name="zeilennummer"/>
                <xsl:apply-templates/>
            </span>
        </xsl:if>  
    </xsl:template>
    
    <xsl:template match="vz">
        <xsl:if test="@indent='0' and ./node()">
            <lno>
                <xsl:call-template name="zeilennummer"/>
                <xsl:apply-templates/>
            </lno><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>
        <xsl:if test="@indent='1'">
            <lin>
                <xsl:call-template name="zeilennummer"/>
                <xsl:apply-templates/>
            </lin><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>  
    </xsl:template>
    
    <xsl:template match="vz" mode="synopse">
        <xsl:apply-templates></xsl:apply-templates>
    </xsl:template>
    
    <xsl:template name="zeilennummer">
        <xsl:param name="p_zeilennummer">
            <xsl:number level="single" count="vz[normalize-space(.)]" from="bl" format="1"/>
        </xsl:param>
        <xsl:param name="p_zeilenanzahl">
            <xsl:value-of select="count(ancestor::bl/vz[normalize-space(.)])"/>
        </xsl:param>         
        <xsl:if test="$p_zeilenanzahl &gt; 10">
            <xsl:if test="$p_zeilennummer mod 5 = 0">
                <cnt><xsl:value-of select="$p_zeilennummer" /></cnt>
            </xsl:if>
        </xsl:if>
    </xsl:template>
    
    <!--Feste Leerstellen und Zwischenraeume / fixed blanks and gaps-->
    <xsl:template match="spatium_v[@width=1]">
        <xsl:choose>
            <xsl:when test="ancestor::inschrift">
                <xsl:text>&#x0020;</xsl:text>
            </xsl:when>
            <xsl:otherwise>
                <span style="margin-left:1pt"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="spatium_v[@width &gt; 1]">
        <xsl:for-each select=".">
            <span>
                <xsl:attribute name="padding-right">
                    <xsl:call-template name="multiplikator">
                        <xsl:with-param name="faktor" select="@width" />
                    </xsl:call-template>
                </xsl:attribute>
            </span>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template name="multiplikator">
        <xsl:param name="faktor"/>
        <xsl:value-of select="$faktor * 4"/>pt</xsl:template>
    <!--Ende: Transkriptionsformate / transcriptions=============================-->
    <!--Verweise auf andere Datensaetze/Artikel / references to inscriptions in other articles-->
    <!--<rec type="extern"/> / rec[@type="extern"]ausgelagert in druck.volagen.xsl-->
    
    <!--Spalten im Transkriptionsfeld / columns in transcription area-->
    <!-- match="bl[@align='properties/alignments/di_columns']/bl[1]" -->
    <xsl:template name="spalten">
        <table><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:for-each select="bl">
                    <cell>
                        <entry>
                            <xsl:apply-templates>
                                <xsl:with-param name="caller" select="'spalten'" />
                            </xsl:apply-templates>
                        </entry>
                    </cell>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </row>
        </table><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- Marginalien im Transkriptionsfeld -->    
    <xsl:template name="marginalien">
        <xsl:choose>
            <xsl:when test="not(./line)">
                <lce><xsl:apply-templates/></lce><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:when>
            <xsl:otherwise>
                <table><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:for-each select="./line">
                    <xsl:variable name="v_zeilennr"><xsl:number count="line" from="bl" format="1"/></xsl:variable>
                    <tr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <td  style="text-align: right;">
                            <xsl:if test="di_left_margin">
                              <xsl:apply-templates select="di_left_margin"/>
                            </xsl:if>
                        </td>
                        <td  style="text-align: right;">
                            <xsl:if test="vz and $v_zeilennr mod 5 = 0">
                                <em style="font-size: 0.8em;"><xsl:value-of select="$v_zeilennr"/></em>                                    
                            </xsl:if>
                        </td>
                        <td>
                            <xsl:if test="vz/@data-link-value='eingerückt'">
                                <xsl:attribute name="style">padding-left: 1em;</xsl:attribute>                                
                            </xsl:if>
                            <xsl:choose>
                                <xsl:when test="di_left_margin">
                                    <xsl:apply-templates select="node()[not(self::di_left_margin)]"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:apply-templates/>                                            
                                </xsl:otherwise>
                            </xsl:choose>
                        </td>
                    </tr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
                </table><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="z[parent::bl[parent::bl[@align='properties/alignments/di_columns']]]"><br/></xsl:template>
    
    <xsl:template match="bl/line">
        <xsl:param name="p_position" select="position()"/>
        <lno>
            <xsl:apply-templates/>
            <tab><xsl:for-each select="ancestor::bl[@align='properties/alignments/di_columns']/bl[2]/line[@position=current()/@position]"><xsl:apply-templates/></xsl:for-each></tab>
        </lno>  
    </xsl:template>
    
    <xsl:template name="inscriptionparts">
        <xsl:param name="p_preID"/>
        <xsl:param name="p_all_lost"/>
        <!-- inschrift-teile ansteuern -->
        <xsl:for-each select="inscriptionpart">

            <!-- synoptische darstellung der versionen -->

            <xsl:for-each select="version"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <sec>
                    <xsl:if test="$sw_links_inscriptions=1">
                        <xsl:attribute name="id">
                          <xsl:value-of select="$p_preID"/>-<xsl:value-of select="nr/@id"/>
                        </xsl:attribute>
                    </xsl:if><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:if test="nr[text()]">
                        <snr>
                            <xsl:choose>
                                <xsl:when test="$p_all_lost = '1'">
                                    <xsl:apply-templates select="replace(nr, '†', '')"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:apply-templates select="nr"/>
                                </xsl:otherwise>
                            </xsl:choose>
                            <xsl:apply-templates select="version-nr" />
                        </snr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:if>
                    <par>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:for-each select="content/bl">
                            <xsl:choose>
                                <xsl:when test="@align='properties/alignments/di_columns'">
                                    <xsl:call-template name="spalten"/>
                                </xsl:when>
                                <xsl:when test="@value='with_marg_left' or following-sibling::bl/@value='with_marg_left'">
                                    <xsl:call-template name="marginalien"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:choose>
                                        <xsl:when test="vz">
                                            <xsl:for-each select="vz[node()]">
                                                <xsl:apply-templates select="."/>
                                            </xsl:for-each>
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <lno><xsl:apply-templates/></lno><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        </xsl:otherwise>
                                    </xsl:choose>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:for-each>
                    </par><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </sec><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
            
        </xsl:for-each>
    </xsl:template>
    
    <!-- die versionen eine inschritenteils nebeneinander - snoptisch - darstellen -->
    <xsl:template name="synopsis">
        <xsl:param name="p_preID"/>
        <inscription><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- tabelle anlegen -->
            <sco class="synopse-table"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- alle inschriftenteile des artikels ansteuern
                    und an ein template verweisen, das jeweils eine tabellenzeile erzeugt  -->
                <xsl:for-each select=".//inscriptionpart">
                    <xsl:call-template name="synopse-row">
                        <xsl:with-param name="p_preID" select="$p_preID"/>
                    </xsl:call-template>
                </xsl:for-each>
            </sco>
        </inscription><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- tabellenzeile für synoptische darstellung von inschrift-versionen -->
    <xsl:template name="synopse-row">
        <xsl:param name="p_preID"/>
        <!-- zeile anlegen und spalten erzeugen-->
        <sec>
            <xsl:if test="$sw_links_inscriptions=1">
                <xsl:attribute name="id">
                    <xsl:value-of select="$p_preID"/>-<xsl:value-of select="version[1]/nr/@id"/>
                </xsl:attribute>
            </xsl:if>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- erste spalte: inschrift-bezeichner -->
            <snr><xsl:value-of select="version[1]/nr"/></snr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- die versionen ansteuern und für jede eine weitere spalte anlegen -->
            <xsl:for-each select="version">
                <par><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- bereiche <bl> im transkriptionsfeld ansteuern -->
                    <xsl:for-each select="content/bl">
                        <xsl:choose>
                            <!-- wenn es sich bei dem bereich um eine überschrift handelt,
                            ein <div> mit class-attribut anlegen-->
                            <xsl:when test="contains(@align, 'di_title')">
                                <lnh>
                                    <!-- überschrift (wenn text vorhanden) ausgeben -->
                                    <xsl:apply-templates select="."></xsl:apply-templates>
                                </lnh><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:when>
                            <!-- anderenfalls <div> ohne attribut anlegen -->
                            <xsl:otherwise>
                                <xsl:choose>
                                    <!-- wenn verszeilen getagt sind,
                                        jede verszeile ansteuern und in ein <lno> geben -->
                                    <xsl:when test="vz">
                                        <!-- zeilen  -->
                                        <xsl:for-each select="vz">
                                            <lno>
                                                <xsl:apply-templates select="." mode="synopse"></xsl:apply-templates>
                                            </lno><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        </xsl:for-each> 
                                    </xsl:when>
                                    <!-- wenn es keine verszeilen gibt, den inhalt des bereichs
                                        insgesamt in ein einzelnes <lno> geben -->
                                    <xsl:otherwise>
                                        <lno>
                                            <xsl:apply-templates select="."></xsl:apply-templates>
                                        </lno><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>
                </par><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </sec>
    </xsl:template>
    
    <xsl:template name="metre">
        <xsl:param name="metresNode"/>
        <xsl:choose>
            
            <xsl:when test="$metresNode/item">
                <xsl:for-each select="$metresNode/item">
                    <entry>
                        <xsl:value-of select="content" />
                        <xsl:if test="links/link">
                            <xsl:text> (</xsl:text>
                            <xsl:apply-templates select="links" />
                            <xsl:text>)</xsl:text>
                        </xsl:if>
                        <xsl:text>.</xsl:text>
                    </entry>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>		  
            </xsl:when>
            
            <xsl:when test="$metresNode/p">
                <xsl:for-each select="$metresNode/p">
                    <entry>
                        <xsl:apply-templates select="."/>
                    </entry>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </xsl:when>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template name="translation">
        <xsl:param name="p_article-number"/>
        <entry>
            <xsl:if test="nr[node()]"> 
                <xsl:text>(</xsl:text>
                <xsl:choose>
                    <xsl:when test="$sw_links_inscriptions=1">
                        <ref><xsl:attribute name="target">#<xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="$p_article-number"/>-<xsl:value-of select="nr/@section-id"/></xsl:attribute>
                            <xsl:apply-templates select="nr"></xsl:apply-templates>
                            <xsl:apply-templates select="version"></xsl:apply-templates>
                        </ref>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:apply-templates select="nr"></xsl:apply-templates>
                        <xsl:apply-templates select="version"></xsl:apply-templates>
                    </xsl:otherwise>
                </xsl:choose>
                <xsl:text>) </xsl:text>
            </xsl:if>
            <xsl:apply-templates select="content"/>
        </entry>
        <xsl:if test="position() != last()">
            <br />
        </xsl:if>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
</xsl:stylesheet>