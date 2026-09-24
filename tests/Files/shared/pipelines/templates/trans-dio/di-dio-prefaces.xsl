<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/>
    
    <xsl:import href="di-dio-links.xsl"/>
    <xsl:import href="di-dio-tools.xsl"/>
    <xsl:import href="di-dio-common.xsl"/>

    
    <xsl:template match="prefaces">
        <xsl:param name="p_section-id" select="@id"/>
        <xsl:param name="p_parent_iri"><xsl:value-of select="$p_volume_iri"/>-page-intro</xsl:param>
        <xsl:param name="p_iri"><xsl:value-of select="$p_volume_iri"/>-page-intro-<xsl:value-of select="$p_section-id"/></xsl:param>

        <xsl:variable name="v_level" select="@level"/>
        <xsl:variable name="v_level_end"><xsl:value-of select="$v_level + 2"/></xsl:variable>

        <page iri="{$p_iri}" parent_iri="{$p_parent_iri}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <title><xsl:value-of select="title"/></title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <doktype>1</doktype><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <layout>1</layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <xsl:call-template name="title">
                <xsl:with-param name="p_volume_title" select="$p_volume_title"></xsl:with-param>
                <xsl:with-param name="p_iri"><xsl:value-of select="$p_iri"/></xsl:with-param>
            </xsl:call-template>

            <xsl:call-template name="pagebrowser">
                <xsl:with-param name="p_iri"><xsl:value-of select="$p_iri"/></xsl:with-param>
                <xsl:with-param name="p_iri_postfix">top</xsl:with-param>
            </xsl:call-template>
            
            <tt_content iri="{$p_iri}-header"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>header</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header><xsl:value-of select="title"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout><xsl:value-of select="$v_level_end"/></header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <tt_content iri="{$p_iri}-text1"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <sorting>2</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="content/p">
                        <p><xsl:apply-templates></xsl:apply-templates></p>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <tt_content iri="{$p_iri}-text2"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <sorting>3</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:apply-templates select="datum//p"></xsl:apply-templates>
                    <xsl:text>&#8195;</xsl:text>
                    <xsl:apply-templates select="unterschrift//p"></xsl:apply-templates>
                </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <xsl:call-template name="citation">
                <xsl:with-param name="p_iri"><xsl:value-of select="$p_iri"/></xsl:with-param>
                <xsl:with-param name="p_section_title">Einleitung</xsl:with-param>
                <xsl:with-param name="p_page_title"><xsl:value-of select="title"/></xsl:with-param>
                <xsl:with-param name="p_custom_value">
                    <xsl:if test="*[@data_key='citationnote']//p"><xsl:value-of select="*[@data_key='citationnote']//p"/></xsl:if>
                </xsl:with-param>                
            </xsl:call-template>
            
            <xsl:call-template name="pagebrowser">
                <xsl:with-param name="p_iri"><xsl:value-of select="$p_iri"/></xsl:with-param>
                <xsl:with-param name="p_iri_postfix">bottom</xsl:with-param>
            </xsl:call-template>            
          </page>
        </xsl:template>
    
</xsl:stylesheet>