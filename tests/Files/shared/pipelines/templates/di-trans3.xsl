<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"  
        xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
        xmlns:php="http://php.net/xsl"
>

  <!-- <folge> muss ab trans1 noch umbenannt werden -->

<!-- 
  dieses stylesheet stößt die dritte transformationsstufe der di-daten an;
    es ist anzuwenden auf das transformationsergebnis der zweiten stufe, 
    die mit di-trans2.xsl in gang gesetzt wurde.
  
  in diesem stylesheet wird der inschriftenband erneut zusammengestellt,
  die einzelnen abschnitte werden an templates verwiesen, 
  die sich in ausgelagerten stylesheets befinden.
-->

  <xsl:import href="commons/di-switch.xsl"/>
  <xsl:import href="commons/epi-functions.xsl"/>
 
  <xsl:import href="trans3/di-trans3-indices.xsl"/>
  <xsl:import href="trans3/di-trans3-brands.xsl"/> 
  <xsl:import href="trans3/di-trans3-plates.xsl"/>
  <xsl:import href="trans3/di-trans3-drawings.xsl"/>
  <xsl:import href="trans3/di-trans3-volume.xsl"/>
  <xsl:import href="trans3/di-trans3-articles.xsl"/>

<!-- GENERELLE PARAMETER: -->
  <!-- bezeichnung der datenbank -->
  <xsl:param name="p_db"><xsl:value-of select="book/project/database"/></xsl:param>

  <!-- sprache des projekts (für die estnischen kolleginnen)-->
  <xsl:param name="p_projectlanguage">

    <!-- Sprachenkürzel nach ISO 639-1; siehe https://wiki.selfhtml.org/wiki/Sprachk%C3%BCrzel -->
  <xsl:choose>
    <xsl:when test="$p_db='inscriptiones_estoniae'">et</xsl:when>
    <xsl:otherwise>de</xsl:otherwise>
  </xsl:choose>
  </xsl:param>

  <!-- basistandort des project (bei städtischen beständen: name der stadt -->
  <xsl:param name="p_basesite"><xsl:value-of select="book/project/name"/></xsl:param>
   


  <!-- wurzelkonten ansteuern und an templates verweisen -->
  <xsl:template match="/">
    <xsl:apply-templates/>
  </xsl:template>
  
  <!-- wurzelelement aufrufen -->
  <xsl:template match="book">
    <!-- die struktur des inschriftenbandes wird angelegt -->
    <book>
      <xsl:copy-of select="@*"/>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <!-- die elemente der ersten ebene der reihe nach ansteuern, 
        sie wahlweise kopieren oder an templates verweisen -->
        <xsl:for-each select="*">
          <xsl:choose>
            <!-- elemente kopieren -->
            <xsl:when test="
              self::job or 
              self::options or
              self::project or
              self::preliminaries or
              self::table_of_content or
              self::literatures or
              self::di_volumes
              ">
              <xsl:copy-of select="." copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:when>
            <!-- an templates verweisen -->
            <xsl:when test="
              self::prefaces or
              self::introduction or
              self::abbreviations or
              self::indices or
              self::locations or
              self::brands or
              self::drawings or
              self::plates
              ">
              <xsl:apply-templates select="."/>
              <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:when>
            <!-- an benanntes templates verweisen -->
            <xsl:when test="self::articles">
              <xsl:for-each select="ancestor::book">
                <xsl:call-template name="articles">
                  <xsl:with-param name="p_projectlanguage"><xsl:value-of select="$p_projectlanguage"/></xsl:with-param>
                </xsl:call-template></xsl:for-each>
            </xsl:when>
            <!-- an benanntes templates verweisen -->
            <xsl:when test="self::blazons">
              <xsl:copy>
                <xsl:copy-of select="@*" />
                <xsl:attribute name="indexing">1</xsl:attribute>
                <xsl:call-template name="blazons" /></xsl:copy>
            </xsl:when>
          </xsl:choose>
          
        </xsl:for-each>

      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

<!--      <xsl:copy-of select="job"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <!-\- optionen aus der pipeline -\->
      <xsl:copy-of select="options" copy-namespaces="no"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

      <!-\- projektangaben -\->
      <xsl:copy-of select="project" copy-namespaces="no"/>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      
      <!-\-titelei-\->
      <xsl:copy-of select="preliminaries" copy-namespaces="no"/>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      
      <!-\-inhaltsverzeichnis  -\->
      <xsl:copy-of select="table_of_content" copy-namespaces="no"/>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
      
      <!-\-vorwort-\->
      <xsl:apply-templates select="prefaces"/>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      
      <!-\-einleitung-\->
      <xsl:apply-templates select="introduction"/>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      
      <!-\- katalog erneut anlegen (in trans3/di-trans3-catalog.xsl)-\->
      <xsl:call-template name="articles">
        <xsl:with-param name="p_projectlanguage"><xsl:value-of select="$p_projectlanguage"/></xsl:with-param>
      </xsl:call-template>
      
      <!-\-inschriftenliste kopieren-\->
      <xsl:copy-of select="table_of_inscriptions" copy-namespaces="no"/>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>      

      <!-\- abkürzungen an templates verweisen-\->
      <xsl:apply-templates select="abbreviations"></xsl:apply-templates>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
     
      <!-\- quellen und literatur kopieren -\->
      <xsl:copy-of select="literatures" copy-namespaces="no"/>
      
      <!-\- liste der di-baende kopieren -\->
      <xsl:copy-of select="di_volumes" copy-namespaces="no"/>

      <!-\- register zur weiteren transformation an templates verweisen-\->
      <!-\-  das template im externen stylesheet di-trans3-indices.xsl-\->
      <xsl:apply-templates select="indices"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
     
      <!-\- tabellarische übersicht der standorte zur weiteren transformation an templates verweisen-\->
      <!-\-  das template im externen stylesheet di-trans3-indices.xsl-\->
      <xsl:apply-templates select="locations"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
     
      <!-\- meisterzeichen und hausmarken-\->
      <!-\-  das template im externen stylesheet di-trans3-marken.xsl-\->
      <xsl:apply-templates select="brands"></xsl:apply-templates>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text> 
      
      <!-\- zeichnungen an templates verweisen-\->
      <!-\-  das template im externen stylesheet di-trans3-drawings.xsl-\->
      <xsl:apply-templates select="drawings"></xsl:apply-templates>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      
      <!-\- bildtafeln an templates verweisen-\->
      <!-\-  das template im externen stylesheet di-trans3-plates.xsl-\->
      <xsl:apply-templates select="plates"></xsl:apply-templates> 
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>-->
      
    </book>
  </xsl:template>
 </xsl:stylesheet>

