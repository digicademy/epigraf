<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <!--buchstabenverbindungen-->
  <xsl:template match="all|wunsch[@tagname='all']">
    <xsl:choose>
      <!--uebergestellte buchstaben-->
      <xsl:when test="@type='lsup' or contains(@value,'bergestellte Buchstaben')">
        <xsl:choose>
          <!--ae-->
          <xsl:when test="contains(.,'ae')">
            <xsl:text>a&#x0364;</xsl:text>
          </xsl:when>
          <!--oe-->
          <xsl:when test="contains(.,'oe')">
            <xsl:text>o&#x0364;</xsl:text>          </xsl:when>
          <!--ue-->
          <xsl:when test="contains(.,'ue')">
            <xsl:text>u&#x0364;</xsl:text>
          </xsl:when>
          <!--uo-->
          <xsl:when test="contains(.,'uo')">
            <xsl:text>u&#x0366;</xsl:text>          </xsl:when>
          <!--gross V mit zwei Punkten oder Strichen-->
          <xsl:when test="contains(.,'V')">
            <xsl:text>V&#x0308;</xsl:text>
          </xsl:when>
          <!--gross N mit akzent-->
          <xsl:when test="contains(.,'N')">
            <xsl:text>N&#x0301;</xsl:text>
          </xsl:when>
          <xsl:otherwise>
            <inline>
              <xsl:value-of select="."/>
            </inline>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:when>

     <xsl:otherwise>
        <lig>
          <xsl:value-of select="."/>
        </lig>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:template>
  
  
  

  
</xsl:stylesheet>
