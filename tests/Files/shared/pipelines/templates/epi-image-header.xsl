<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output  method="xml" version="1.0" omit-xml-declaration="no" indent="yes" encoding="UTF-8"/>
  
  <xsl:template match="/">
    <xsl:apply-templates/>
  </xsl:template>
  
  <xsl:template match="book">
    <xsl:apply-templates select="catalog"/>
  </xsl:template>
  
  <xsl:template match="catalog">
    <xsl:apply-templates select="article"/>
  </xsl:template>
  
  
  <xsl:template match="article">
    <x:xmpmeta xmlns:x="adobe:ns:meta/" x:xmptk="Adobe XMP Core 4.2.2-c063 53.352624, 2008/07/30-18:12:18 ">
      <rdf:RDF xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
        <rdf:Description rdf:about="" xmlns:dc="http://purl.org/dc/elements/1.1/">
          <dc:title>
            <rdf:Alt>
              <rdf:li xml:lang="x-default">
                <xsl:text>DI </xsl:text><xsl:value-of select="ancestor::book/project/di_number"/><xsl:text>, Nr. </xsl:text> 
                <xsl:value-of select="number(kopfzeile/signatur[1])" />
                <xsl:text> - </xsl:text>
                    <xsl:for-each select="kopfzeile/standorte/standort">
                    <xsl:if test="@basesite"><xsl:value-of select="@basesite"/><xsl:text>, </xsl:text></xsl:if>
                    <xsl:value-of select="."/>
                    </xsl:for-each>
                <xsl:text> - </xsl:text>
                <xsl:value-of select="kopfzeile/datierung"/>
              </rdf:li>
            </rdf:Alt>
          </dc:title>
          <dc:creator>
            <rdf:Seq>
              <rdf:li><!--Autor des Bildes-->
                  <xsl:value-of select="ancestor::book/options/@copyright"/>
              </rdf:li>
            </rdf:Seq>
          </dc:creator>
          <dc:description>
            <rdf:Alt>
              <rdf:li xml:lang="x-default"><!--entfaellt--></rdf:li>
            </rdf:Alt>
          </dc:description>
          <dc:subject>
            <rdf:Bag>
              <rdf:li> 
                <!-- Schlagworte -->
                  <xsl:call-template name="keywords"></xsl:call-template>
              </rdf:li>
            </rdf:Bag>
          </dc:subject>
          <dc:rights>
            <rdf:Alt>
              <rdf:li xml:lang="x-default">
                <!--Rechteinhaber des Bildes-->
                  <xsl:value-of select="ancestor::book/options/@copyright"/>         
              </rdf:li>
            </rdf:Alt>
          </dc:rights>
        </rdf:Description>
        <rdf:Description rdf:about="" xmlns:photoshop="http://ns.adobe.com/photoshop/1.0/">
          <photoshop:CaptionWriter><xsl:value-of select="ancestor::book/options/@caption_writer"/></photoshop:CaptionWriter>
          
          <photoshop:DateCreated><xsl:value-of select="kopfzeile/modified"/></photoshop:DateCreated>          
          
          <photoshop:City><xsl:value-of select="ancestor::book/options/@city"/><!--Greifswald--></photoshop:City>
          <photoshop:State><xsl:value-of select="ancestor::book/options/@state"/><!--Mecklenburg-Vorpommern--></photoshop:State>
          <photoshop:Country><xsl:value-of select="ancestor::book/options/@country"/><!--Deutschland--></photoshop:Country>
          <photoshop:Credit><xsl:value-of select="ancestor::book/options/@credit"/><!--NADW Göttingen, Forschungsstelle Inschriften Greifswald--></photoshop:Credit>
          <photoshop:Source><xsl:value-of select="ancestor::book/options/@source"/><!--Bildarchiv NADW Göttingen, Forschungsstelle Inschriften Greifswald--></photoshop:Source>
          <photoshop:Headline><xsl:value-of select="kopfzeile/titel" /></photoshop:Headline>
        </rdf:Description>
        <rdf:Description rdf:about="" xmlns:xmpRights="http://ns.adobe.com/xap/1.0/rights/">
          <xmpRights:Marked>True</xmpRights:Marked>
          <xmpRights:WebStatement><xsl:value-of select="ancestor::book/options/@rights"/><!--Unerlaubte Vervielfältigung oder Weitergabe nicht gestattet. Die Herstellung von Kopien für den persönlichen, privaten und nicht kommerziellen Gebrauch ist erlaubt.--></xmpRights:WebStatement>
          <xmpRights:UsageTerms><xsl:value-of select="ancestor::book/options/@rights"/><!--Unerlaubte Vervielfältigung oder Weitergabe nicht gestattet. Die Herstellung von Kopien für den persönlichen, privaten und nicht kommerziellen Gebrauch ist erlaubt.--></xmpRights:UsageTerms>
        </rdf:Description>
        <rdf:Description rdf:about="" xmlns:Iptc4xmpCore="http://iptc.org/std/Iptc4xmpCore/1.0/xmlns/">
          <Iptc4xmpCore:CreatorContactInfo rdf:parseType="Resource">
            <Iptc4xmpCore:CountryCode>DE</Iptc4xmpCore:CountryCode>
          </Iptc4xmpCore:CreatorContactInfo>
        </rdf:Description>
      </rdf:RDF>
    </x:xmpmeta>
  </xsl:template>

<!-- haupteinträge in den registern ansteuern uns auslesen -->
<xsl:template name="keywords">
  <xsl:param name="items">    
    <xsl:for-each select="//index/*[name()='eintrag' or name()='gruppe']">
      <xsl:variable name="term1"><xsl:value-of select="normalize-space(lemma)"/><xsl:if test="lemma">, </xsl:if></xsl:variable>
      <xsl:if test="lemma">
        <item><xsl:value-of select="normalize-space(lemma)"/></item>
      </xsl:if>
      <xsl:if test="*[name()='eintrag' or name()='gruppe']">
<!-- untereinträge ansteuern -->
          <xsl:call-template name="keyword"><xsl:with-param name="term1"><xsl:value-of select="$term1"/></xsl:with-param></xsl:call-template>
      </xsl:if>
    </xsl:for-each>
  </xsl:param>
  <xsl:for-each select="$items/item"><xsl:value-of select="."/><xsl:if test="following-sibling::item">;</xsl:if></xsl:for-each>
</xsl:template>
  
<!-- untereinträge auslesen und deren untereinträge in einer schleife ansteuern und auslesen -->
  <xsl:template name="keyword">
    <xsl:param name="term1"></xsl:param>
    <xsl:for-each select="*[name()='eintrag' or name()='gruppe']">
      <xsl:variable name="term2"><xsl:value-of select="$term1"/><xsl:value-of select="normalize-space(lemma)"/><xsl:if test="lemma">, </xsl:if></xsl:variable>
      <xsl:if test="lemma">
        <xsl:if test="contains($term1,',')">
          <item><xsl:value-of select="$term1"/><xsl:value-of select="normalize-space(lemma)"/></item>
        </xsl:if>
        <item><xsl:value-of select="normalize-space(lemma)"/></item>
      </xsl:if>
      <xsl:if test="*[name()='eintrag' or name()='gruppe']">
          <xsl:call-template name="keyword"><xsl:with-param name="term1"><xsl:value-of select="$term2"/></xsl:with-param></xsl:call-template>
      </xsl:if>      
    </xsl:for-each>
  </xsl:template>
  
</xsl:stylesheet>


