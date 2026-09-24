<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="di-dio-links.xsl"/>
    <xsl:import href="di-dio-tools.xsl"/>

    <!-- In diesem Stylesheet werden Templates für sich wiederholende Elemente definiert (z.B. Titel). -->
    
    <xsl:template name="title">
        <xsl:param name="p_volume_title" />
        <xsl:param name="p_iri" />
        
        <tt_content iri="{$p_iri}-title"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <type>header</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header><xsl:value-of select="$p_volume_title" /></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout>2</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    
    <xsl:template name="citation">
        <xsl:param name="p_urn" select="$p_urn"/>
        <xsl:param name="p_iri" />
        <xsl:param name="p_section_title" />
        <xsl:param name="p_page_title" />
        <xsl:param name="p_custom_value" />

        <tt_content iri="{$p_iri}-citation"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <section_frame>247</section_frame><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext>
                <p><strong>Zitierhinweis: </strong>
                    <xsl:call-template name="citation-text">
                        <xsl:with-param name="p_section_title" select="$p_section_title"></xsl:with-param>
                        <xsl:with-param name="p_page_title" select="$p_page_title"></xsl:with-param>
                        <xsl:with-param name="p_custom_value" select="$p_custom_value"></xsl:with-param>
                        <xsl:with-param name="p_urn" select="$p_urn"></xsl:with-param>
                    </xsl:call-template>
                </p>

            </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

    <xsl:template name="citation-text">
        <xsl:param name="p_section_title" />
        <xsl:param name="p_page_title" />
        <xsl:param name="p_custom_value" select="''" />
        <xsl:param name="p_urn" select="$p_urn"></xsl:param>

        <xsl:choose>
            <xsl:when test="$p_custom_value != ''">
                <xsl:value-of select="$p_custom_value"/>
            </xsl:when>
           <xsl:otherwise>
               <xsl:value-of select="$p_volume_shorttitle"/>
               <xsl:if test="$p_section_title != ''">
                   <xsl:text>, </xsl:text><xsl:value-of select="$p_section_title"/>
               </xsl:if>
               <xsl:if test="$p_page_title != ''">
                   <xsl:text>, </xsl:text><xsl:value-of select="$p_page_title"/>
               </xsl:if>
               <xsl:if test="$p_volume_author != ''">
                 <xsl:text> (</xsl:text><xsl:value-of select="$p_volume_author"/><xsl:text>)</xsl:text>
               </xsl:if>
           </xsl:otherwise>
        </xsl:choose>
        <xsl:text>, in: inschriften.net</xsl:text>
        <xsl:if test="string($p_urn)">
            <xsl:text>, </xsl:text>
            <a href="https://nbn-resolving.de/{$p_urn}"><xsl:value-of select="$p_urn"/></a>
        </xsl:if>
        <xsl:text> (</xsl:text><xsl:value-of select="$p_date"/><xsl:text>)</xsl:text>
        <xsl:text>.</xsl:text>
    </xsl:template>
    
    
    <xsl:template name="pagebrowser">
        <xsl:param name="p_iri_postfix" />
        <xsl:param name="p_iri" />
        
        <tt_content iri="{$p_iri}-pagebrowser-{$p_iri_postfix}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <type>list</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header>plugin.tx_cagpagebrowser</header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <list_type>tscobj_pi1</list_type>
            <pi_flexform>
                  <T3FlexForms>
                   <data>
                       <sheet index="sDEF">
                           <language index="lDEF">
                               <field index="object">
                                   <value index="vDEF">plugin.tx_cagpagebrowser</value>
                               </field>
                               <field index="htmlspecialchars">
                                   <value index="vDEF">0</value>
                               </field>
                           </language>
                       </sheet>
                   </data>
               </T3FlexForms>
            </pi_flexform>
        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <xsl:template name="footnotestart">
        <xsl:param name="p_iri" />
        <xsl:param name="p_startvalue"></xsl:param>
        
        <tt_content type="footnotestart" iri="{$p_iri}-footnotestart"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext><anmstart><xsl:value-of select="$p_startvalue"/></anmstart></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
    </xsl:template>    
</xsl:stylesheet>