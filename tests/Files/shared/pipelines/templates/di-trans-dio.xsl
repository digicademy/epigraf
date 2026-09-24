<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:xs="http://www.w3.org/2001/XMLSchema">

<!-- Aufbau des DIO-Exports:
  <body>
    <page iri="dio-book-{108}-page-intro">
      <title>{Einleitung}</title>
      <sorting>1</sorting>
    </page>
      
    ==> Das Vorwort wird als <page> angelegt:
    <page iri="dio-book-{108}-page-intro-{sections-0000}" parent_iri="dio-book-{108}-page-intro">
      <title>{Vorwort}</title>
      <sorting>1</sorting>     
      <tt_content iri="dio-book-{108}-page-intro-{sections-0000}-text1">
        <type>text</type>
      </tt_content>
    </page>

    ==> Hauptkapitel der einleitung werden als <page> angelegt:
    <page iri="dio-di-{108}-page-intro-{sections-0000}" parent_iri="dio-book-{108}-page-intro">
      <title>{Überschrift 1. Abschnitt}</title>
      <sorting>{2}</sorting>
      
      ==> Unterkapitel werden innerhalb der hauptkapitel (<page>) als <tt_content> angelegt:
      <tt_content iri="dio-book-{108}-page-intro-{sections-0000}-text">
        <type>text|table|header</type>
      </tt_content>
    </page>
     

    <page type=articles>
      ...
      ==> Inschriftenartikel werden innerhalb der Artikelseite als tx_hisodat_sources angelegt:
      <tx_hisodat_sources>...</tx_hisodat_sources>
    </page>
    
    <page type="images">
      ...
    </page>
    
    <page type=literatures>
      <tt_content type="abbreviatons">...</tt_content>
      <tt_content type="literature">...</tt_content>
    </page>
    
    <page type=materials>  
      <tt_content type="drawings">...</tt_content>
      <tt_content type="brands">...</tt_content>
    </page>
    
    <page type=indexes>  
      ...
    </page>    
  -->
  
  <!-- Struktur der Elemente <tt_content>:
    
        <tt_content iri="">
            <type>header</type>
            <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout></header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content>
        <tt_content iri="">
            <type>text</type>
            <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout></header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
            </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content>
        <tt_content iri="">
            <type>table</type>
            <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <header_layout></header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
            </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </tt_content>
        
        in <tt_content type=header> bleibt <bodytext> leer
        
        wenn keine abschnittsnummerierung erfolgt, enthält <header_layout> den wert 100,
        anderenfalls muss ein zahl angegeben werden, die dem level der überschrift entspricht.
        
        in <tt_content type=text|table> bleibt <header> leer, wenn die betreffende überschrift 
        bereits darüber in einem <tt_content type=header> ausgegeben wurde.
    -->


  <xsl:import href="commons/di-switch.xsl"/>
  <xsl:import href="commons/epi-functions.xsl"/>
  
  <xsl:import href="trans-dio/di-dio-cover.xsl"/>
  <xsl:import href="trans-dio/di-dio-prefaces.xsl"/>
  <xsl:import href="trans-dio/di-dio-introduction.xsl"/>
  <xsl:import href="trans-dio/di-dio-articles.xsl"/>
  <xsl:import href="trans-dio/di-dio-indices.xsl"/>
  <xsl:import href="trans-dio/di-dio-heraldry.xsl"/>
  <xsl:import href="trans-dio/di-dio-brands.xsl"/>
  <xsl:import href="trans-dio/di-dio-floorplans.xsl"/>
  <xsl:import href="trans-dio/di-dio-literature.xsl"/>
  <xsl:import href="trans-dio/di-dio-abbreviations.xsl"/>
  <xsl:import href="trans-dio/di-dio-images.xsl"/>
  <xsl:import href="trans-dio/di-dio-materials.xsl"/>


  <!-- urn-fragment des inschriftenbandes -->
  <xsl:param name="p_dio_urn_volume"><xsl:value-of select="book/project/dio_urn_volume"/></xsl:param>
  <!-- Bandsignatur (zum Beispiel DI 114) -->
  <xsl:param name="p_volume_signature" select="book/project/description/di_signature"/>
  <!-- bandnummer -->
  <xsl:param name="p_volume_number"><xsl:value-of select="book/project/di_number"/></xsl:param>

  <!-- Band-Typ: di oder dio  -->
  <xsl:param name="p_volume_prefix">
    <xsl:choose>
      <xsl:when test="book/project/description/di_prefix/text()">
        <xsl:value-of select="book/project/description/di_prefix" />
      </xsl:when>
      <xsl:otherwise>di</xsl:otherwise>
    </xsl:choose>
  </xsl:param>

  <!--xsl:param name="p_volume_title"><xsl:apply-templates select="book/preliminaries/prelim_volume/prelim_volume_title//p"></xsl:apply-templates></xsl:param-->
  <xsl:param name="p_volume_title"><xsl:value-of select="book/preliminaries/prelim_volume/prelim_volume_title//p"></xsl:value-of></xsl:param>
  <xsl:param name="p_volume_name"><xsl:value-of select="book/project/name"/></xsl:param>
  <xsl:param name="p_volume_shorttitle"><xsl:value-of select="$p_volume_signature"/>, <xsl:value-of select="$p_volume_name"/></xsl:param>
  <xsl:param name="p_volume_author" select="book/project/description/author" />
  <xsl:param name="p_volume_path"><xsl:value-of select="book/project/description/di_path"/></xsl:param>
  <xsl:param name="p_urn"><xsl:value-of select="book/project/book_urn"/></xsl:param>
  <xsl:param name="p_date"><xsl:value-of select="format-dateTime(xs:dateTime(book/job/created), '[D01].[M01].[Y]')"/></xsl:param>

  <xsl:param name="p_volume_identifier">
    <xsl:value-of select="$p_volume_prefix" />
    <xsl:choose>
      <xsl:when test="book/project/di_number/text()">
        <xsl:number value="book/project/di_number" format="001"></xsl:number>
      </xsl:when>
      <xsl:otherwise><xsl:text>000</xsl:text></xsl:otherwise>
    </xsl:choose>
  </xsl:param>
  
  <!-- IRI prefix for volumes -->
  <!-- TODO: Use p_volume_identifier instead of di_number --> 
  <xsl:param name="p_volume_iri">dio-book-<xsl:value-of select="book/project/di_number"/></xsl:param>
    

  <xsl:template match="book">
    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text> 
    <xsl:choose>
      <!-- Wenn der gesamte Band auszugeben ist... -->
      <xsl:when test="*[not(name()='articles') and not(name()='options') and not(name()='project')  and not(name()='drawings')]">
        <book><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <!-- <parametertest-di-nr><xsl:value-of select="$volume_number_dreistellig"/></parametertest-di-nr>-->
          <project><xsl:value-of select="project/di_number"/></project><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          
          <xsl:call-template name="cover">              
          </xsl:call-template><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          
          <xsl:apply-templates select="prefaces">
            <xsl:with-param name="p_parent_iri"><xsl:value-of select="$p_volume_iri"/>-page-cover</xsl:with-param>
          </xsl:apply-templates><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          
          <xsl:apply-templates select="introduction">
            <xsl:with-param name="p_parent_iri"><xsl:value-of select="$p_volume_iri"/>-page-cover</xsl:with-param>
          </xsl:apply-templates><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          
          <xsl:apply-templates select="articles" mode="page">
          </xsl:apply-templates><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    
          <xsl:call-template name="images">              
          </xsl:call-template><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>            

          <xsl:call-template name="materials">
          </xsl:call-template><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    

          <xsl:call-template name="literature">              
          </xsl:call-template><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
          
          <xsl:call-template name="indices">       
          </xsl:call-template><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </book>
        
      </xsl:when>
      
      <!-- ... wenn nur der Katalog auszugeben ist. -->
      <xsl:otherwise>
          <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <xsl:apply-templates select="articles" mode="sources">
          </xsl:apply-templates>
          <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      </xsl:otherwise>
    </xsl:choose>
    
    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>  
  </xsl:template>

</xsl:stylesheet>

