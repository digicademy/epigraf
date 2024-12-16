<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml"
  xmlns:v="urn:schemas-microsoft-com:vml"
  xmlns:w10="urn:schemas-microsoft-com:office:word"
  xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core"
  xmlns:aml="http://schemas.microsoft.com/aml/2001/core"
  xmlns:wx="http://schemas.microsoft.com/office/word/2003/auxHint"
  xmlns:o="urn:schemas-microsoft-com:office:office"
  xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882"
  xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2"
  xmlns:msxsl="urn:schemas-microsoft-com:xslt"
  xmlns:exsl="http://exslt.org/common"
  xmlns:php="http://php.net/xsl"
>
<xsl:template match="*">
<xsl:choose>
    <xsl:when test="name()='marke'">
        <xsl:call-template name="markenbilder"></xsl:call-template>
    </xsl:when>
    <xsl:when test="name()='file_name'"></xsl:when>
    <xsl:otherwise>
        <xsl:element name="{name()}">
            <xsl:copy-of select="@*"/>
            <xsl:apply-templates/>
        </xsl:element>
    </xsl:otherwise>
</xsl:choose>

</xsl:template>

<xsl:template name="markenbilder">
<xsl:comment>markentest</xsl:comment>
            <w:binData>
              <xsl:attribute name="w:name">
        			  <xsl:value-of select="concat('wordml://',file_name,'.png')" />
        			</xsl:attribute>
              <xsl:value-of select="php:function('Images::base64Image',concat('properties/brands/',string(file_name)),500)" />
            </w:binData>
  </xsl:template>

</xsl:stylesheet>
