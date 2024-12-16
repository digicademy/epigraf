<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:fo="http://www.w3.org/1999/XSL/Format">

<!--unsichere Lesung von Buchstaben-->

	<xsl:template match="insec|wunsch[@tagname='insec']">
	 <xsl:for-each select=".">
	   <xsl:if test="string-length() &gt; 0">	       	    
	     <xsl:call-template name="InsecSchleife">
	       <xsl:with-param name="Zaehler" select="1" />
	       <xsl:with-param name="Ende" select="string-length()" />	      
	     </xsl:call-template>
	   </xsl:if>
	  </xsl:for-each>
	</xsl:template>

<xsl:template name="InsecSchleife">
   <xsl:param name="Zaehler" />
	<xsl:param name="Ende" />
   <xsl:if test="$Zaehler &lt;= $Ende">
    <xsl:value-of select="substring(.,$Zaehler,1)"/><xsl:text>&#x0323;</xsl:text>
    <xsl:call-template name="InsecSchleife">
      <xsl:with-param name="Zaehler" select="$Zaehler + 1" />
      <xsl:with-param name="Ende" select="$Ende" />
    </xsl:call-template>
  </xsl:if>
</xsl:template>
</xsl:stylesheet>


