<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">

<!-- 
    dieses stylesheet enthält templates für die einleitung,
      
    hierin enthalten sind auch templates zur auflösung von csv-tabellen,
    
    es wird aufgerufen in di-trans-dio.xsl
-->


    <xsl:import href="../commons/di-switch.xsl"/>
    <xsl:import href="di-dio-links.xsl"/>
    <xsl:import href="di-dio-tools.xsl"/>
    <xsl:import href="di-dio-common.xsl"/>

    <!--einleitung-->
    <xsl:template match="introduction">
        <xsl:param name="p_section-id" select="@id"/>
        
        <!-- Hauptüberschrift -->
<!--        <tt_content type="header">
            <header><xsl:value-of select="title"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout>2</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->
        <!-- im parameter: abschnitte ansteuern, nach typ differenzieren -->
        <xsl:param name="p_page">
            <xsl:for-each select="section[@level='2']">
                
                <xsl:variable name="v_level" select="@level"/>
                <xsl:variable name="v_level_end"><xsl:value-of select="$v_level + 1"/></xsl:variable>
                <xsl:variable name="v_section-id" select="@id"/>
                <xsl:variable name="v_parent_iri"><xsl:value-of select="$p_volume_iri"/>-page-cover</xsl:variable>
                <xsl:variable name="v_iri"><xsl:value-of select="$p_volume_iri"/>-page-intro-<xsl:value-of select="$v_section-id"/></xsl:variable>
                
                <page iri="{$v_iri}" parent_iri="{$v_parent_iri}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <title><xsl:value-of select="@name"/></title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <sorting><xsl:value-of select="@number+1"/></sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <doktype>1</doktype><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <layout>1</layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

                    <xsl:call-template name="title">
                        <xsl:with-param name="p_volume_title" select="$p_volume_title"></xsl:with-param>
                        <xsl:with-param name="p_iri"><xsl:value-of select="$v_iri"/></xsl:with-param>
                    </xsl:call-template>

                    <xsl:call-template name="pagebrowser">
                        <xsl:with-param name="p_iri"><xsl:value-of select="$v_iri"/></xsl:with-param>
                        <xsl:with-param name="p_iri_postfix">top</xsl:with-param>
                    </xsl:call-template>
                    
                    <xsl:call-template name="footnotestart">
                        <xsl:with-param name="p_iri"><xsl:value-of select="$v_iri"/></xsl:with-param>
                        <xsl:with-param name="p_startvalue"></xsl:with-param>
                    </xsl:call-template>
                    
                    <tt_content iri="{$v_iri}-header"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <type>header</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <header><xsl:value-of select="@name"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <header_layout><xsl:value-of select="$v_level_end"/></header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </tt_content>


                    <xsl:if test="p | bl">
                        <tt_content iri="{$v_iri}-text"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                           
                            <bodytext><xsl:for-each select="p | bl">
                                <xsl:if test="self::p">
                                    <p><xsl:apply-templates /></p>
                                </xsl:if>
                                <xsl:if test="self::bl">
                                    <p class="transcription"><xsl:apply-templates /></p>
                                </xsl:if>
                            </xsl:for-each></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:if>                
                    <xsl:call-template name="loop-sections">
                        <xsl:with-param name="p_iri-intro" select="$v_parent_iri"></xsl:with-param>
                        <xsl:with-param name="p_section-id" select="$v_section-id"></xsl:with-param>
                    </xsl:call-template>
                    
                    <xsl:call-template name="citation">
                        <xsl:with-param name="p_iri" select="$v_iri" />
                        <xsl:with-param name="p_section_title">Einleitung</xsl:with-param>
                        <xsl:with-param name="p_page_title" select="@name" />
                        <xsl:with-param name="p_custom_value">
                            <xsl:if test="following-sibling::section[@parent_id = current()/@id][@data_key='citationnote']">
                                <xsl:value-of select="following-sibling::section[@parent_id = current()/@id][@data_key='citationnote']/p"/>
                            </xsl:if>
                        </xsl:with-param>
                    </xsl:call-template>
                    
                    <xsl:call-template name="pagebrowser">
                        <xsl:with-param name="p_iri"><xsl:value-of select="$v_iri"/></xsl:with-param>
                        <xsl:with-param name="p_iri_postfix">bottom</xsl:with-param>
                    </xsl:call-template>
                </page><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </xsl:param>
        
        <!-- Im Element <sorting> fortlaufend nummerieren 
             und im Element <tt_content type="foootnotestart"> die Anzahl der vorangegangenen Fußnoten eintragen.
             Dazu die vorher im Paramter $p_page aufgefangenen <page>-Elemente mit allen Unterelementen neu anlegen
             und die nötigen Werte überschreiben.             
        -->
        <xsl:for-each select="$p_page/page">
            <!-- Count all <anm> elements in previous pages -->
            <xsl:variable name="anmStartCount">
                <xsl:value-of select="count(preceding-sibling::page//anm) + 1"/>
            </xsl:variable>            
            <page>
                <xsl:copy-of select="@*"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:copy-of select="title"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:copy-of select="sorting"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:copy-of select="doktype"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:copy-of select="layout"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:for-each select="tt_content">
                    <tt_content>
                        <xsl:copy-of select="@*"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <sorting><xsl:number count="tt_content" level="single"></xsl:number></sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        
                        <xsl:for-each select="*">
                            <!-- Update bodytext only if tt_content's type is 'footnotestart' and the element is 'bodytext' -->
                            <xsl:choose>
                                <xsl:when test="../@type='footnotestart' and name()='bodytext'">
                                    <bodytext><anmstart><xsl:value-of select="$anmStartCount"/></anmstart></bodytext>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:copy-of select="."/>
                                </xsl:otherwise>
                            </xsl:choose><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                 
                        </xsl:for-each>
                    </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </page><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template name="loop-sections">
        <xsl:param name="p_iri-intro"/>
        <xsl:param name="p_section-id"></xsl:param>

        <xsl:for-each select="parent::*/section[@parent_id=$p_section-id]">
            <xsl:variable name="v_level" select="@level"></xsl:variable>
            <xsl:variable name="v_level_end"><xsl:value-of select="$v_level + 1"/></xsl:variable>
            <xsl:variable name="v_section-id" select="@id"/>
            <!-- abschnitte nach typ differenzieren -->
            <xsl:choose>
                <!-- csv-tabellen -->
                <xsl:when test="@data_key='csv_table'">
                    <tt_content iri="{$p_iri-intro}-{$v_section-id}-table"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <type>table</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <bodytext>
                            <xsl:apply-templates></xsl:apply-templates>
                        </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:when>
                <xsl:when test="@data_key='csv_table_horizontal'">
                    <tt_content iri="{$p_iri-intro}-{$v_section-id}-table"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <type>table</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <bodytext>
                            <xsl:apply-templates></xsl:apply-templates>
                        </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <pi_flexform>                        
                            <T3FlexForms>
                            <data>
                                <sheet index="sDEF">
                                    <language index="lDEF">
                                        <field index="acctables_caption">
                                            <value index="vDEF"></value>
                                        </field>
                                        <field index="acctables_summary">
                                            <value index="vDEF"></value>
                                        </field>
                                        <field index="acctables_tfoot">
                                            <value index="vDEF">0</value>
                                        </field>
                                        <field index="acctables_headerpos">
                                            <value index="vDEF">top</value>
                                        </field>
                                        <field index="acctables_nostyles">
                                            <value index="vDEF">0</value>
                                        </field>
                                        <field index="acctables_tableclass">
                                            <value index="vDEF"></value>
                                        </field>
                                    </language>
                                </sheet>
                                <sheet index="s_parsing">
                                    <language index="lDEF">
                                        <field index="tableparsing_quote">
                                            <value index="vDEF"></value>
                                        </field>
                                        <field index="tableparsing_delimiter">
                                            <value index="vDEF">124</value>
                                        </field>
                                    </language>
                                </sheet>
                            </data>
                        </T3FlexForms>
                    </pi_flexform>
                        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:when>
                <xsl:when test="@data_key='csv_table_vertical'">
                    <tt_content iri="{$p_iri-intro}-{$v_section-id}-table"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <type>table</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <header_layout>200</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <bodytext>
                            <xsl:apply-templates></xsl:apply-templates>
                        </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <pi_flexform>                        
                            <T3FlexForms>
                                <data>
                                    <sheet index="sDEF">
                                        <language index="lDEF">
                                            <field index="acctables_caption">
                                                <value index="vDEF"></value>
                                            </field>
                                            <field index="acctables_summary">
                                                <value index="vDEF"></value>
                                            </field>
                                            <field index="acctables_tfoot">
                                                <value index="vDEF">0</value>
                                            </field>
                                            <field index="acctables_headerpos">
                                                <value index="vDEF">left</value>
                                            </field>
                                            <field index="acctables_nostyles">
                                                <value index="vDEF">0</value>
                                            </field>
                                            <field index="acctables_tableclass">
                                                <value index="vDEF"></value>
                                            </field>
                                        </language>
                                    </sheet>
                                    <sheet index="s_parsing">
                                        <language index="lDEF">
                                            <field index="tableparsing_quote">
                                                <value index="vDEF"></value>
                                            </field>
                                            <field index="tableparsing_delimiter">
                                                <value index="vDEF">124</value>
                                            </field>
                                        </language>
                                    </sheet>
                                </data>
                            </T3FlexForms>
                        </pi_flexform>
                    </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:when>
                <!-- fortsetzung von kapiteln nach csv-tabellen -->
                <xsl:when test="@data_key='continuation'">
                    <tt_content iri="{$p_iri-intro}-{$v_section-id}-text"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <bodytext><xsl:apply-templates /></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                
                </xsl:when>
                <!-- Zitierhinweis wird nicht hier, sondern in der Seite übernommen -->
                <xsl:when test="@data_key='citationnote'"></xsl:when>
                <!-- kapitel -->
                <xsl:otherwise>
                    <xsl:choose>
                        <xsl:when test="$v_level_end > 5">
                            <tt_content iri="{$p_iri-intro}-{$v_section-id}-header"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <header_layout>5</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <bodytext>
                                    <h5 class="subheader subheader-h{$v_level_end}">                                    
                                        <xsl:value-of select="@name"/>
                                    </h5>
                                </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                            
                        </xsl:when>
                        <xsl:otherwise>
                            <tt_content iri="{$p_iri-intro}-{$v_section-id}-header"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <type>header</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <header><xsl:value-of select="@name"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <header_layout><xsl:value-of select="$v_level_end"/></header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                            
                        </xsl:otherwise>                        
                    </xsl:choose>
                    <xsl:if test="p | bl">
                        <tt_content iri="{$p_iri-intro}-{$v_section-id}-text"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <!-- absätze ansteuern und an templates verweisen -->
                            <bodytext>
                                <xsl:for-each select="*">
                                    <xsl:choose>
                                        <xsl:when test="self::p">
                                            <p><xsl:apply-templates/></p>
                                        </xsl:when>
                                        <xsl:when test="self::bl">
                                            <xsl:apply-templates select="self::bl"/>
                                        </xsl:when>
                                        <xsl:when test="self::h">
                                            <xsl:variable name="v_header_class" select="@data-link-value"/>
                                            <xsl:variable name="v_header_level_1" select="substring($v_header_class, 2)"/>
                                            <xsl:variable name="v_header_level_2" select="number($v_header_level_1) + 1"/>
                                            <h5 class="subheader subheader-h{$v_header_level_2}">
                                                <xsl:apply-templates/>
                                            </h5>
                                        </xsl:when>
                                    </xsl:choose>
                                </xsl:for-each>
                            </bodytext>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:if>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:if test="following-sibling::section[@parent_id=$v_section-id]">
            <xsl:call-template name="loop-sections">
                <xsl:with-param name="p_iri-intro" select="$p_iri-intro"></xsl:with-param>
                <xsl:with-param name="p_section-id" select="$v_section-id"></xsl:with-param>
            </xsl:call-template>
            </xsl:if>
        </xsl:for-each>
        
<!--        
        <parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="$p_page"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
        
-->
        
    </xsl:template>
    
    <xsl:template match="p[ancestor::introduction  or ancestor::prefaces]">
        <p><xsl:apply-templates /></p>
    </xsl:template>
    
    <!-- tabellenzeilen -->
    <xsl:template match="row">
        <xsl:choose>
            <!-- es werden nur zeilen verarbeitet, die zellen mit inhalt aufweisen -->
            <xsl:when test="cell/node()">
                <xsl:for-each select="cell"><xsl:apply-templates></xsl:apply-templates><xsl:if test="following-sibling::cell"><xsl:text>|</xsl:text></xsl:if></xsl:for-each>
            </xsl:when>
            <xsl:otherwise></xsl:otherwise>
        </xsl:choose>
    </xsl:template>
</xsl:stylesheet>