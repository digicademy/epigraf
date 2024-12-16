<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="marken">
  <!-- zuerst werden für jeden markentyp kursorisch alle verweise auf marken in den artikeln angesteuert und in einen parameter kopiert -->
  <!-- danach werden die doubletten ausgesondert und die so verkürzte liste in einen weiteren parameter kopiert -->
  <!-- als drittes werden die marken nummeriert und in vierergruppen geordnet -->

    <xsl:param name="p_marken_1">
    <!-- funktion dieses parameter: alle verweise auf marken in den textfeldern der artikel sowie alle referenzen auf fußnoten in der reihenfolge ihres auftretens auslesen
    kopieren und um das attribut @brandtype-name aus dem markenregister ergänzen -->
    <!-- verweise auf marken in den textfeldern sowie alle fußnoten in der reihenfolge ihres auftretens ansteuern -->
    <xsl:for-each select="ancestor::book/catalog/article//section/items/item/content//*[name()='rec_ma' or name()='app1']">
    <!-- für die weitere verarbeitung differenzieren  -->
          <xsl:choose>
          <!-- im fall des verweises auf eine marke (in den textfeldern)  die entsprechende spezifikation in den links aufsuchen -->
          <xsl:when test="name()='rec_ma'">
                <xsl:copy-of select="."/>
          </xsl:when>
    <!-- verweise auf marken in fußnoten -->
          <xsl:otherwise>
          <!-- jede referenz auf eine fußnote prüfen, ob in der zugehörigen fußnote ein verweis auf eine marke enhalten ist -->
            <!-- die @id der referenz matcht auf das attribut @from_id der fußnote-->
            <xsl:if test="ancestor::article/footnotes/footnote[@from_id=current()/@id]//rec_ma">
            <!-- ist dies gegeben, den verweis auf die marke in der fußnote ansteuern -->
                <xsl:for-each select="ancestor::article/footnotes/footnote[@from_id=current()/@id]//rec_ma">
                <!-- nun wie oben bei den markenverweisen in den textfeldern verfahren -->
                <xsl:copy-of select="."/>
                </xsl:for-each>
            </xsl:if>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:for-each>
    </xsl:param>
    <!-- doubletten entfernen aus p_marken_1-->
   <xsl:param name="p_marken_2">
    <xsl:for-each select="$p_marken_1/*">
        <xsl:choose>
            <xsl:when test="@data-link-value=preceding-sibling::*/@data-link-value"></xsl:when>
            <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
        </xsl:choose>
    </xsl:for-each>
   </xsl:param>
    <!-- die elemente aus p_marken_2 mit laufnummern versehen -->
   <xsl:param name="p_marken_3">
    <xsl:for-each select="$p_marken_2/*">
    <xsl:copy><xsl:copy-of select="@*"/><xsl:attribute name="current_number"><xsl:value-of select="position()"/></xsl:attribute></xsl:copy>
    </xsl:for-each>
   </xsl:param>

<!-- die markenabteilungen vorstrukturieren -->
    <xsl:param name="p_markentypen_1">
    <xsl:choose>
    <!-- wenn die marken nicht nach dem typ unterteilt werden sollen, werden die markentyp-abteilungen zusammengezogen -->
          <xsl:when test="$markentypen_zusammenfassen = 1">
            <markentyp>
                <xsl:for-each select="markentyp/item">
                    <xsl:variable name="v_id"><xsl:value-of select="@id"/></xsl:variable>
    <!-- die laufnumer des markenverweises durch abgleich mit dem  parameter p_marken_3 ermitteln -->
                   <xsl:variable name="v_currentnumber">
                        <xsl:for-each select="$p_marken_3/*[@data-link-target=$v_id]"><xsl:value-of select="@current_number"/></xsl:for-each>
                    </xsl:variable>
    <!-- die laufnummer zwecks sortierung nach der reiehnfolge des auftretens im katalog zum marken.item hinzufügen -->
                    <item current_number="{$v_currentnumber}"><xsl:copy-of select="@*"/>
                        <xsl:for-each select="*"><xsl:copy-of select="."/></xsl:for-each>
                    </item>
                </xsl:for-each>
            </markentyp>
          </xsl:when>
    <!-- anderenfall die markentyp-abteilungen einzeln behandeln, details wie oben -->
          <xsl:otherwise>
             <xsl:for-each select="markentyp">
                <markentyp><xsl:copy-of select="@*"/>
                    <xsl:copy-of select="titel"/>
                    <xsl:for-each select="item">
                        <xsl:variable name="v_id"><xsl:value-of select="@id"/></xsl:variable>
                        <xsl:variable name="v_currentnumber">
                            <xsl:for-each select="$p_marken_3/*[@data-link-target=$v_id]"><xsl:value-of select="@current_number"/></xsl:for-each>
                        </xsl:variable>
                        <item current_number="{$v_currentnumber}"><xsl:copy-of select="@*"/>
                            <xsl:for-each select="*"><xsl:copy-of select="."/></xsl:for-each>
                        </item>
                    </xsl:for-each>
                </markentyp>
            </xsl:for-each>
         </xsl:otherwise>
        </xsl:choose>
    </xsl:param>

<!-- markeneinträge sortieren -->
    <xsl:param name="p_markentypen_2">
        <xsl:for-each select="$p_markentypen_1">
            <xsl:for-each select="markentyp">
                <markentyp><xsl:copy-of select="@*"/>
                      <xsl:copy-of select="titel"/>
                        <xsl:for-each select="item">
                        <xsl:sort select="@current_number" data-type="number"/>
                        <xsl:copy-of select="."/>
                        </xsl:for-each>
                </markentyp>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:param>

<!-- markenabschnitt des bandes neu anlegen -->
  <marken indizieren="1" logg="m1">
    <xsl:copy-of select="@*"/>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

<!--parametertest
<p_markentypen_1><xsl:copy-of select="$p_markentypen_1"/></p_markentypen_1> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<p_markentypen_2><xsl:copy-of select="$p_markentypen_2"/></p_markentypen_2> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
 <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

<p_marken1><xsl:copy-of select="$p_marken_1"/></p_marken1> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<p_marken2><xsl:copy-of select="$p_marken_2"/></p_marken2> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<p_marken3><xsl:copy-of select="$p_marken_3"/></p_marken3> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
 <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->
<!--gesamttitel einfügen-->
    <titel><xsl:value-of select="titel"/></titel>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!--unterabschnitte für den markentyp anlegen-->
        <xsl:for-each select="$p_markentypen_2/markentyp">
            <abschnitt><xsl:copy-of select="@*"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!--titel übernehmen-->
            <titel><xsl:value-of select="titel"/></titel>
<!--alle items ansteuern und an das nächste template übergeben-->
                    <xsl:for-each select="item">
                      <xsl:call-template name="insert_marken"/>
                    </xsl:for-each>
            </abschnitt>
        </xsl:for-each>
  </marken> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
 </xsl:template>

<!--marken-items strukturieren  -->
  <xsl:template name="insert_marken">
    <xsl:if test="(position() mod 4) = 1">
      <m-group> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <marke>
          <xsl:copy-of select="@*"/>
           <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <nr>
            <xsl:number from="markentyp" count="item"/>
          </nr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <formkey>
            <xsl:call-template name="formkey">
              <xsl:with-param name="formkey1"><xsl:value-of select="name"/></xsl:with-param>
            </xsl:call-template>
          </formkey> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <xsl:for-each select="*">
            <xsl:copy-of select="."/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          </xsl:for-each>

        </marke> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="following-sibling::item[position() &lt;= 3]">
          <marke>
            <xsl:copy-of select="@*"/>
             <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <nr>
              <xsl:number from="markentyp" count="item"/>
            </nr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
             <formkey>
              <xsl:call-template name="formkey">
                <xsl:with-param name="formkey1"><xsl:value-of select="name"/></xsl:with-param>
              </xsl:call-template>
            </formkey> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="*">
              <xsl:copy-of select="."/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
          </marke> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
      </m-group> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
  </xsl:template>

  <xsl:template name="formkey">
    <xsl:param name="formkey1"></xsl:param>
    <xsl:param name="formkey2"><xsl:value-of select="substring-after($formkey1,'.')"/></xsl:param>
    <xsl:choose>
      <xsl:when test="contains($formkey2, '.')">
        <xsl:call-template name="formkey">
          <xsl:with-param name="formkey1"><xsl:value-of select="$formkey2"/></xsl:with-param>
        </xsl:call-template>
      </xsl:when>
      <xsl:otherwise><xsl:value-of select="$formkey2"/></xsl:otherwise>
    </xsl:choose>
  </xsl:template>

</xsl:stylesheet>

