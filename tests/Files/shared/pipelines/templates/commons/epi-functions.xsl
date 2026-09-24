<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:epi="http://epigraf.inschriften.net#xslt-functions"    
    exclude-result-prefixes="xs epi"
    version="2.0">

    <!-- In diesem Stylesheet werden allgemeine Funktionen definiert -->
    
    <!-- Pad number with zeros -->
    <xsl:function name="epi:pad" as="xs:string">
        <xsl:param name="value" as="xs:string"/>
        <xsl:param name="width" as="xs:integer"/>
        <xsl:variable name="num" select="replace($value, '[^0-9]', '')"/>
        <xsl:sequence select="format-number(number($num), string-join(for $i in 1 to $width return '0', ''))"/>
    </xsl:function>
    
    <!-- Pad number with zeros but keep prefix -->
    <xsl:function name="epi:pad-with-suffix" as="xs:string?">
        <xsl:param name="value" as="xs:string?"/>
        <xsl:param name="width" as="xs:integer"/>
        <xsl:variable name="num" select="replace($value, '[^0-9]', '')"/>
        <xsl:variable name="suffix" select="replace($value, '^[0-9]+', '')"/>
        <xsl:sequence select="concat(format-number(number($num), string-join(for $i in 1 to $width return '0', '')), $suffix)"/>
    </xsl:function>
    
    <!-- Extract volume ID from literature lemma for DI volumes -->
    <xsl:function name="epi:extract-volume-number" as="xs:string">
        <xsl:param name="value" as="xs:string"/>
        <xsl:variable name="shorttitle" select="replace($value, '.*(DIO?\s*\d+[a-z]*).*', '$1')"/>
        <xsl:variable name="identifier" select="lower-case(translate($shorttitle, ' ', ''))"/>        
        <xsl:value-of select="$identifier"/>
    </xsl:function>
    
    <!-- Pad number with zeros -->
    <xsl:function name="epi:remove-brackets" as="xs:string">
        <xsl:param name="value" as="xs:string"/>
        <xsl:value-of select="normalize-space(replace($value, '\(\s*\)', ''))"/>
    </xsl:function>
    
</xsl:stylesheet>