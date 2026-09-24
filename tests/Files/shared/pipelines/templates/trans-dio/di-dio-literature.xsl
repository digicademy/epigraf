<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  
  <xsl:import href="../commons/di-switch.xsl"/>

  <!--==QUELLEN- UND LITERATURVERZEICHNIS / Sources and Literature=====================-->
  
  
<!--  
    <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  <header_layout></header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  <bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

  </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  
  -->
  
  <xsl:template name="literature">
    <xsl:param name="p_iri"><xsl:value-of select="$p_volume_iri"/>-page-literature</xsl:param>
    
    <page type="literature" iri="{$p_iri}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>      
      <title>Literatur</title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <sorting>5</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
      <doktype>1</doktype><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <layout>1</layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
      <!-- Überschrift Quellen- und Literaturverzeichnis -->
      <tt_content iri="{$p_iri}-title"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <type>header</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <sorting>1</sorting><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <header>Quellen- und Literaturverzeichnis</header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <header_layout>2</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      
      <xsl:apply-templates select="abbreviations">
        <xsl:with-param name="p_iri" select="$p_iri" />
      </xsl:apply-templates>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <xsl:apply-templates select="literatures">
        <xsl:with-param name="p_iri" select="$p_iri" />
      </xsl:apply-templates>

      <xsl:call-template name="citation">
        <xsl:with-param name="p_iri"><xsl:value-of select="$p_iri"/></xsl:with-param>
      </xsl:call-template>
    </page><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>        
  </xsl:template>
  
  <xsl:template match="literatures">
    <xsl:param name="p_iri" />
    
    <!-- abteilungen ansteuern -->
    <xsl:for-each select="section">
      <xsl:variable name="v_section-id" select="@id"/>
      
      <!-- überschrift der abteilung -->
      <tt_content iri="{$p_iri}-{$v_section-id}-header"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <type>header</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <header><xsl:value-of select="lemma" /></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <header_layout>3</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <!-- templates für die verschiedenen abteilungen aufrufen -->
      <!-- TODO: Nicht hart kodieren, ggf. Metaproperty anlegen -->
        <xsl:choose>
          <xsl:when test="contains(lemma, 'Ungedruckte')">
            <xsl:call-template name="loop-items-sources">
              <xsl:with-param name="p_iri" select="$p_iri" />
            </xsl:call-template>
          </xsl:when>
          <xsl:when test="contains(lemma, 'Gedruckte')">
            <xsl:call-template name="loop-items-literature">
              <xsl:with-param name="p_iri" select="$p_iri" />
            </xsl:call-template>
          </xsl:when>
          <xsl:when test="contains(lemma, 'Online')">
            <xsl:call-template name="loop-items-online-resources">
              <xsl:with-param name="p_iri" select="$p_iri" />
            </xsl:call-template>
          </xsl:when>
        </xsl:choose>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:for-each>
  </xsl:template>
  
  <!-- <item>s rekursiv ansteuern und formatieren -->
  
  <xsl:template name="loop-items-sources">
    <xsl:param name="p_iri" />
    <xsl:for-each select="item">
      <xsl:variable name="v_item-id" select="@id"/>
      
      <!-- format der überschrift gemäß dem attribut @level bestimmen -->
      <xsl:variable name="v_level" select="@level"/>
      <xsl:variable name="v_h" select="$v_level + 4"/>
      <xsl:variable name="v_lemma" select="normalize-space(lemma)" />
  
      <tt_content iri="{$p_iri}-{$v_item-id}-text"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <header><xsl:value-of select="$v_lemma"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <header_layout><xsl:value-of select="$v_h"/></header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <bodytext>
          <xsl:call-template name="loop-items-sources-ul" />                  
        </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>          

    </xsl:for-each>
  </xsl:template>

  <xsl:template name="loop-items-sources-ul">
    <xsl:if test="item">
    <ul>
      <xsl:for-each select="item">
        <li><xsl:attribute name="id" select="@id" /><xsl:value-of select="normalize-space(lemma)"/><xsl:if test="not(item)"><xsl:if test="not(ends-with(lemma,'.'))">.</xsl:if></xsl:if><xsl:call-template name="loop-items-sources-ul" /></li>
      </xsl:for-each>
    </ul>  
    </xsl:if>
  </xsl:template>
  
  <xsl:template name="loop-items-literature">
    <xsl:param name="p_iri" />
    <xsl:for-each select="item[not(lemma/@sortchart=preceding-sibling::item/lemma/@sortchart)]">
      <xsl:variable name="v_itemid" select="@id"/>
      <xsl:variable name="v_sortchart"><xsl:value-of select="lemma/@sortchart"/></xsl:variable>
      <tt_content iri="{$p_iri}-{$v_itemid}-lit-letter-{$v_sortchart}"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:choose>
          <xsl:when test="matches($v_sortchart, '^[0-9]$')">
            <header/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          </xsl:when>
          <xsl:otherwise>
            <header><xsl:value-of select="upper-case(lemma/@sortchart)"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout>4</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          </xsl:otherwise>
        </xsl:choose>
        <bodytext>
          <ul><li><xsl:attribute name="id" select="@id" /><xsl:value-of select="lemma"/><xsl:if test="not(ends-with(lemma,'.'))">.</xsl:if></li>
            <xsl:for-each select="following-sibling::item[lemma[@sortchart=$v_sortchart]]">
              <xsl:choose>
                <xsl:when test="starts-with(lemma,'DI ') and $sw_quellen-literatur-di='1'"></xsl:when>
                <xsl:otherwise>
                  <!-- literaturtitel einfügen und mit punkt abschließen -->
                  <li><xsl:attribute name="id" select="@id" /><xsl:value-of select="normalize-space(lemma)"/><xsl:if test="not(ends-with(lemma,'.'))">.</xsl:if></li>                  
                </xsl:otherwise>
              </xsl:choose>
            </xsl:for-each>
          </ul>
        </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:for-each>
  </xsl:template>
  
  <xsl:template name="loop-items-online-resources">
    <xsl:param name="p_iri" />
    <xsl:variable name="v_itemid" select="./item[1]/@id"/>
    <tt_content iri="{$p_iri}-{$v_itemid}-text"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <bodytext>
        <ul>
          <xsl:for-each select="./item">
            <!-- titel einfügen und mit punkt abschließen -->
            <li><xsl:attribute name="id" select="@id" /><xsl:value-of select="normalize-space(lemma)"/><xsl:if test="not(ends-with(lemma,'.'))">.</xsl:if></li>                  
          </xsl:for-each>
        </ul>
      </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:template>
  
</xsl:stylesheet>
