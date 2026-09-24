<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/>
    
    <xsl:import href="di-dio-links.xsl"/>
    <xsl:import href="di-dio-tools.xsl"/>
    <xsl:import href="di-dio-common.xsl"/>
        
    <xsl:template name="cover">
        <xsl:param name="p_iri"><xsl:value-of select="$p_volume_iri"/>-page-cover</xsl:param>
    
        <page type="cover" iri="{$p_iri}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <title><xsl:value-of select="introduction/title"/></title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
          <doktype>21</doktype><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <layout>0</layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          
          <xsl:call-template name="title">
              <xsl:with-param name="p_volume_title" select="$p_volume_title"></xsl:with-param>
              <xsl:with-param name="p_iri"><xsl:value-of select="$p_volume_iri"/>-page-cover</xsl:with-param>
          </xsl:call-template>

          <xsl:for-each select="preliminaries/prelim_volume/prelim_volume_subtitle">
              <xsl:variable name="t_iri">
                  <xsl:value-of select="$p_iri"/>
                  <xsl:text>-subtitle</xsl:text>
                  <xsl:if test="position() > 1"><xsl:text>-</xsl:text><xsl:value-of select="position()"/></xsl:if>
              </xsl:variable>
              <tt_content iri="{$t_iri}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                  <type>header</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                  <sorting>2</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                  <header><xsl:apply-templates select=".//p" /></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                  <header_layout>
                      <xsl:choose>
                          <xsl:when test="position() = 1">3</xsl:when>
                          <xsl:otherwise>4</xsl:otherwise>
                      </xsl:choose>
                  </header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                  <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          </xsl:for-each>
            
          <tt_content iri="{$p_iri}-menu"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <type>menu</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <sorting>3</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <menu_type>1</menu_type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>            
              <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          
          <tt_content iri="{$p_iri}-covertext"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <sorting>4</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <bodytext><xsl:apply-templates select="preliminaries/prelim_volume/di_cover//p"></xsl:apply-templates></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  
          <tt_content iri="{$p_iri}-citationnote"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <sorting>5</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <section_frame>247</section_frame><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <header>Hinweis</header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              <bodytext>
                  <p><strong>Hinweis: </strong>Die Einleitung und die Inschriftenartikel sind jeweils mittels eines persistenten Identifikators (URN) zitierfähig. Den Zitationshinweis und das Datum der letzten Änderung finden Sie am Ende einer Seite: <xsl:call-template name="citation-text" /></p>
              </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            
        </page><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
</xsl:stylesheet>