<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
 
  <xsl:import href="../commons/di-switch.xsl"/>

<!-- 
      dieses stylesheet konfiguriert den abschnitt mit den hausmarken, meisterzeichen usw. im inschriftenband,
      
      es wird aufgerufen in di-trans3-articles.xsl
  -->

<xsl:template match="brands">
  <!-- zuerst werden für jeden markentyp kursorisch alle verweise auf marken in den artikeln angesteuert 
        und in einen parameter kopiert -->
  <!-- danach werden die doubletten ausgesondert und die so verkürzte liste in einen weiteren parameter kopiert -->
  <!-- als drittes werden die marken nummeriert und in vierergruppen geordnet -->

    <xsl:param name="p_brands_1">
    <!-- funktion dieses parameters: alle verweise auf marken in den textfeldern der artikel sowie 
          alle referenzen auf fußnoten in der reihenfolge ihres auftretens auslesen,
          kopieren und um das attribut @brandtype-name aus dem markenregister ergänzen -->
      <!-- Verweise auf Marken in Artikelinhalten (Felder in item oder auch item/property) sowie alle Fußnoten in der Reihenfolge ihres Auftretens ansteuern -->
      
     <!-- geändert JH 2025.11.13: es werden artikelweise
          zuerst alle sections außer heraldry und brands auf <rec_ma> durchsucht
          danach die footnotes-->
     <!-- nochmal geändert JJ 2025.11.16: Das bringt noch mehr durcheinander.
          Stattdessen in vorheriger Stufe den Wappenabschnitt hinter die Inschriften verschoben. -->
      <xsl:for-each select="ancestor::book/articles/article">
          <xsl:for-each select=".//rec_ma">
             <xsl:variable name="property" select="(ancestor::article//property[@id=current()/@data-link-target])[1]" />
              <xsl:copy>
                  <xsl:attribute name="signature"><xsl:value-of select="$property[1]/signature[1]" /></xsl:attribute>                
                  <xsl:copy-of select="@*"/>
              </xsl:copy>
          </xsl:for-each>
        <!--xsl:for-each select="sections/section[not(@sectiontype='heraldry') and not(@sectiontype='brands')]//rec_ma">
          <xsl:copy-of select="."></xsl:copy-of>
        </xsl:for-each>
        <xsl:for-each select="footnotes//rec_ma">
          <xsl:copy-of select="."></xsl:copy-of>
        </xsl:for-each-->
      </xsl:for-each>
      
      <!--
        altes muster:
        <xsl:for-each select="ancestor::book/articles/article//section/items/item//*[name()='rec_ma' or name()='app1' or name()='app2']">
          <!-\- für die weitere verarbeitung differenzieren  -\->
          <xsl:choose>
          <!-\- im fall des verweises auf eine marke (in den textfeldern)  die entsprechende spezifikation in den links aufsuchen -\->
          <xsl:when test="name()='rec_ma'">
                <xsl:copy-of select="."/>
          </xsl:when>
          <!-\- verweise auf marken in fußnoten -\->
          <xsl:otherwise>
            <!-\- jede referenz auf eine fußnote prüfen, ob in der zugehörigen fußnote ein verweis auf eine marke enhalten ist -\->
            <!-\- die @id der referenz matcht auf das attribut @from_id der fußnote-\->
            <xsl:if test="ancestor::article/footnotes/footnote[@from_id=current()/@id]//rec_ma">
                <!-\- ist dies gegeben, den verweis auf die marke in der fußnote ansteuern -\->
                <xsl:for-each select="ancestor::article/footnotes/footnote[@from_id=current()/@id]//rec_ma">
                <!-\- nun wie oben bei den markenverweisen in den textfeldern verfahren -\->
                <xsl:copy-of select="."/>
                </xsl:for-each>
            </xsl:if>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:for-each>-->
    </xsl:param>

  <!-- doubletten entfernen aus p_brands_1-->
   <xsl:param name="p_brands_2">
    <xsl:for-each select="$p_brands_1/*">
        <xsl:choose>
            <xsl:when test="@data-link-value=preceding-sibling::*/@data-link-value"></xsl:when>
            <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
        </xsl:choose>
    </xsl:for-each>
   </xsl:param> 

  <!-- die elemente aus p_brands_2 mit laufnummern versehen -->
   <xsl:param name="p_brands_3">
    <xsl:for-each select="$p_brands_2/*">
    <xsl:copy>
      <xsl:copy-of select="@*"/>
      <xsl:attribute name="current_number">
        <xsl:choose>
          <xsl:when test="@signature and string-length(normalize-space(@signature)) > 0">
            <xsl:value-of select="@signature" />
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="position()" />
          </xsl:otherwise>
        </xsl:choose>
      </xsl:attribute>
    </xsl:copy>
    </xsl:for-each>
   </xsl:param>

<!-- die markenabteilungen vorstrukturieren -->
    <xsl:param name="p_brandtypes_1">
    <xsl:choose>
    <!-- wenn die marken nicht nach dem typ unterteilt werden sollen, werden die markentyp-abteilungen zusammengezogen -->
          <xsl:when test="$sw_markentypen_zusammenfassen = 1">
            <brandtype>
                <xsl:for-each select="brandtype/item">
                    <xsl:variable name="v_id"><xsl:value-of select="@id"/></xsl:variable>
    <!-- die laufnumer des markenverweises durch abgleich mit dem parameter p_brands_3 ermitteln -->
                   <xsl:variable name="v_currentnumber">
                        <xsl:for-each select="$p_brands_3/*[@data-link-target=$v_id][1]"><xsl:value-of select="@current_number"/></xsl:for-each>
                    </xsl:variable>
    <!-- die laufnummer zwecks sortierung nach der reihenfolge des auftretens im katalog zum marken-<item> hinzufügen -->
                    <item current_number="{$v_currentnumber}"><xsl:copy-of select="@*"/>
                      <xsl:copy-of select="name"></xsl:copy-of>
                      <xsl:copy-of select="file_name"></xsl:copy-of>
                      <xsl:copy-of select="signature"></xsl:copy-of>
                      <xsl:call-template name="resolve_sections"></xsl:call-template>
                    </item>
                </xsl:for-each>
            </brandtype>
          </xsl:when>
    <!-- anderenfall die markentyp-abteilungen einzeln behandeln, details wie oben -->
          <xsl:otherwise>
             <xsl:for-each select="brandtype">
                <brandtype><xsl:copy-of select="@*"/>
                    <xsl:copy-of select="title"/>
                    <xsl:for-each select="item">
                        <xsl:variable name="v_id"><xsl:value-of select="@id"/></xsl:variable>
                        <xsl:variable name="v_currentnumber">
                            <xsl:for-each select="$p_brands_3/*[@data-link-target=$v_id][1]"><xsl:value-of select="@current_number"/></xsl:for-each>
                        </xsl:variable>
                        <item current_number="{$v_currentnumber}"><xsl:copy-of select="@*"/>
                          <xsl:copy-of select="name"></xsl:copy-of>
                          <xsl:copy-of select="file_name"></xsl:copy-of>
                          <xsl:copy-of select="signature"></xsl:copy-of>
                          <xsl:call-template name="resolve_sections"></xsl:call-template>
                        </item>
                    </xsl:for-each>
                </brandtype>
            </xsl:for-each>        
         </xsl:otherwise>
        </xsl:choose>
    </xsl:param>

<!-- markeneinträge sortieren -->
    <xsl:param name="p_brandtypes_2">
        <xsl:for-each select="$p_brandtypes_1">
            <xsl:for-each select="brandtype">
              <brandtype><xsl:copy-of select="@*"/>
                      <xsl:copy-of select="title"/>
                        <xsl:for-each select="item">
                           <!-- First: Put items without @current_number to the end --> 
                           <xsl:sort select="number(string-length(@current_number) = 0)" data-type="number"/>
                           <!-- Second: Sort by @current_number -->
                           <xsl:sort select="@current_number" data-type="number"/>
                        <xsl:copy-of select="."/>
                        </xsl:for-each>
                </brandtype>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:param>

<!-- markenabschnitt des bandes neu anlegen -->
  <brands indexing="1" log3="m1">
    <xsl:copy-of select="@*"/>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

<!--parametertest
<p_brandtypes_1><xsl:copy-of select="$p_brandtypes_1"/></p_brandtypes_1> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<p_brandtypes_2><xsl:copy-of select="$p_brandtypes_2"/></p_brandtypes_2> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
 <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

<p_brands1><xsl:copy-of select="$p_brands_1"/></p_brands1> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<p_brands2><xsl:copy-of select="$p_brands_2"/></p_brands2> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<p_brands3><xsl:copy-of select="$p_brands_3"/></p_brands3> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
 <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->

    
    <!--gesamttitel einfügen-->
    <title><xsl:value-of select="title"/></title>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<!--unterabschnitte für den markentyp anlegen-->
    <xsl:for-each select="$p_brandtypes_2/brandtype">
            <brand-type><xsl:copy-of select="@*"/> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<!--titel übernehmen-->
            <title><xsl:value-of select="title"/></title>
<!--alle items ansteuern und an das nächste template übergeben-->
              <xsl:for-each select="item">
                      <xsl:call-template name="insert_brands"/>
                    </xsl:for-each>
            </brand-type>
        </xsl:for-each>
  </brands> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
 </xsl:template>

<!--marken-items strukturieren  -->
  <xsl:template name="insert_brands">
    <xsl:if test="(position() mod 4) = 1">
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <brand-group> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <brand>
          <xsl:copy-of select="@*"/>
           <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <nr>
            <xsl:choose>
              <xsl:when test="signature and (string-length(normalize-space(signature)) > 0)">
                <xsl:value-of select="signature" />
              </xsl:when>
              <xsl:otherwise>
                <xsl:number from="brandtype" count="item"/>
              </xsl:otherwise>
            </xsl:choose>            
          </nr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <formkey>
            <xsl:call-template name="formkey">
              <xsl:with-param name="p_formkey1"><xsl:value-of select="name"/></xsl:with-param>
            </xsl:call-template>
          </formkey> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>         
          <xsl:copy-of select="file_name"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <xsl:copy-of select="sections"></xsl:copy-of>

        </brand> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:for-each select="following-sibling::item[position() &lt;= 3]">
          <brand>
            <xsl:copy-of select="@*"/>
             <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <nr>
              <xsl:choose>
                <xsl:when test="signature and (string-length(normalize-space(signature)) > 0)">
                  <xsl:value-of select="signature" />
                </xsl:when>
                <xsl:otherwise>
                  <xsl:number from="brandtype" count="item"/>
                </xsl:otherwise>
              </xsl:choose>            
            </nr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>          
             <formkey>
              <xsl:call-template name="formkey">
                <xsl:with-param name="p_formkey1"><xsl:value-of select="name"/></xsl:with-param>
              </xsl:call-template>
            </formkey> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>           
            <xsl:copy-of select="file_name"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="sections"></xsl:copy-of>
          </brand> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
      </brand-group> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:if>
  </xsl:template>
  
  <!-- verweise auf artikelnummern: doubletten entfernen und laufnummern auslesen -->
  <xsl:template name="resolve_sections">
    <sections>
      <xsl:for-each select="sections/section[not(@articles_id=preceding-sibling::section/@articles_id)]">
        <xsl:variable name="v_articles_id"><xsl:value-of select="@articles_id"/></xsl:variable>
        <xsl:variable name="v_articlenumber">
          <xsl:value-of select="ancestor::book/articles/article[@id=$v_articles_id]/@nr"/>
        </xsl:variable>
        <xsl:copy><xsl:copy-of select="@*"/><xsl:value-of select="$v_articlenumber"/></xsl:copy>
      </xsl:for-each>
    </sections>
  </xsl:template>
  
  <!-- formschlüssel zum filtern der markenbilder nach anzahl der spezifischen formelemente -->
  <xsl:template name="formkey">
    <xsl:param name="p_formkey1"></xsl:param>
    <xsl:param name="p_formkey2"><xsl:value-of select="substring-after($p_formkey1,'.')"/></xsl:param>
    <xsl:choose>
      <xsl:when test="contains($p_formkey2, '.')">
        <xsl:call-template name="formkey">
          <xsl:with-param name="p_formkey1"><xsl:value-of select="$p_formkey2"/></xsl:with-param>
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise><xsl:value-of select="$p_formkey2"/></xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
</xsl:stylesheet>

