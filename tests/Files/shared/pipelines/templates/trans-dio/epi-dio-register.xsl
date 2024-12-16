<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" >

    <xsl:param name="dio_signatur"><xsl:value-of select="book/project/dio_signature"/></xsl:param>
    <xsl:param name="volume_number"><xsl:value-of select="book/project/di_number"/></xsl:param>
    <xsl:param name="volume_number_dreistellig"><xsl:number value="book/project/di_number" format="001"></xsl:number></xsl:param>
    <xsl:param name="di-type">di</xsl:param><!-- di oder dio; ist hier manuell anzugeben, perspektivisch sollte die angabe aus den kopfdaten des bandartikels auszulesen sein  -->

<xsl:template name="add_id">
<!-- berechnung des timestamps  -->	
	<xsl:param name="timestamp1"><xsl:value-of select="current-dateTime()"/></xsl:param>
	<xsl:param name="timestamp2"><xsl:value-of select="translate($timestamp1,'.:-,+T','')"/></xsl:param>
	<xsl:param name="timestamp3"><xsl:value-of select="substring($timestamp2,1,18)"/></xsl:param>
<!-- berechnung der id -->
	<xsl:param name="id_1"><xsl:value-of select="generate-id()"/></xsl:param>
	<xsl:param name="id_2">
		<xsl:choose>
			<xsl:when test="starts-with($id_1,'d5e')"><xsl:value-of select="substring($id_1,4)"/></xsl:when>
                                  <xsl:when test="starts-with($id_1,'d6e')"><xsl:value-of select="substring($id_1,4)"/></xsl:when>
			<xsl:otherwise><xsl:value-of select="$id_1"/></xsl:otherwise>
		</xsl:choose>
	</xsl:param>
           <xsl:param name="index-lfnr"><xsl:number count="index[not(@name='gliederung')]" format="001"></xsl:number><xsl:if test="@name='gliederung'">000</xsl:if></xsl:param>
<!-- id-attribut erzeugen -->
        <xsl:attribute name="id">
        	<xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number_dreistellig"/>
        	<xsl:text>_</xsl:text>
            <xsl:if test="not(self::indices)"> <!-- im indices-element wird die lfnr als bestandteil der id weggelassen -->           
                    <xsl:value-of select="$index-lfnr"/>
        	       <xsl:text>_</xsl:text>
            </xsl:if>
    	<xsl:value-of select="$id_2"/>
    	<xsl:value-of select="$timestamp3"/>
        </xsl:attribute>
</xsl:template>

<xsl:template match="indices">
    <indices>
        <xsl:attribute name="band-nr" select="$volume_number"/>
        <xsl:attribute name="band-titel"><xsl:value-of select="ancestor::book/titelei/band/titel/p"/></xsl:attribute>
        <xsl:attribute name="band-typ" select="$di-type"/>
        <xsl:attribute name="modus">all</xsl:attribute>
        <xsl:attribute name="basisstandort"><xsl:value-of select="ancestor::book/project/bezeichnung"/></xsl:attribute>
        <xsl:attribute name="epi-id"><xsl:value-of select="@id"/></xsl:attribute>
        <xsl:call-template name="add_id"></xsl:call-template>
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
       <xsl:apply-templates select="notiz"></xsl:apply-templates>
        <xsl:for-each select="index"><xsl:apply-templates select="."></xsl:apply-templates></xsl:for-each>
    </indices>

<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:template>

<xsl:template match="notiz[ancestor::indices]"><xsl:if test="node()"><notiz><xsl:apply-templates></xsl:apply-templates></notiz><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text></xsl:if></xsl:template>


<xsl:template match="index">
<xsl:param name="index-lfnr"><xsl:number count="index[not(@name='gliederung')]" format="001"></xsl:number><xsl:if test="@name='gliederung'">000</xsl:if></xsl:param>
    <register>
        <xsl:attribute name="band-nr" select="$volume_number"/>
        <xsl:attribute name="register-name"><xsl:value-of select="@titel"/></xsl:attribute>
        <xsl:attribute name="register-nr"><xsl:value-of select="@nr"/></xsl:attribute>
        <xsl:copy-of select="@initialgruppen"></xsl:copy-of>
        <xsl:call-template name="add_id"></xsl:call-template>
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>    
        <xsl:apply-templates><xsl:with-param name="index-lfnr"><xsl:value-of select="$index-lfnr"/></xsl:with-param></xsl:apply-templates>
    </register><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text> 
</xsl:template>

<xsl:template match="eintrag">
<xsl:param name="index-lfnr"></xsl:param>
    <eintrag>
        <xsl:attribute name="epi-id"><xsl:value-of select="@id"/></xsl:attribute>
        <xsl:if test="@basisstandort"><xsl:attribute name="loc"><xsl:value-of select="@basisstandort"/></xsl:attribute></xsl:if>
        <xsl:call-template name="add_id"></xsl:call-template>
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <xsl:apply-templates><xsl:with-param name="index-lfnr"><xsl:value-of select="$index-lfnr"/></xsl:with-param></xsl:apply-templates>
    </eintrag><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:template>

<xsl:template match="eintrag_kursiv">
<xsl:param name="index-lfnr"></xsl:param>
    <eintrag_kursiv>
        <xsl:attribute name="epi-id"><xsl:value-of select="@id"/></xsl:attribute>
        <xsl:if test="@basis_standort"><xsl:attribute name="loc"><xsl:value-of select="@basis_standort"/></xsl:attribute></xsl:if>
        <xsl:call-template name="add_id"></xsl:call-template>
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <xsl:apply-templates><xsl:with-param name="index-lfnr"><xsl:value-of select="$index-lfnr"/></xsl:with-param></xsl:apply-templates>
    </eintrag_kursiv><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:template>

<xsl:template match="gruppe">
<xsl:param name="index-lfnr"></xsl:param>
    <gruppe>
        <xsl:attribute name="epi-id"><xsl:value-of select="@id"/></xsl:attribute>
        <xsl:call-template name="add_id"></xsl:call-template>
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <xsl:apply-templates><xsl:with-param name="index-lfnr"><xsl:value-of select="$index-lfnr"/></xsl:with-param></xsl:apply-templates>
    </gruppe><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:template>

<xsl:template match="lemma">
    <lemma><xsl:if test="not(parent::siehe) and not(parent::siehe_auch)"><xsl:copy-of select="@sortchart"/><xsl:copy-of select="@sortstring"/></xsl:if>
        <xsl:apply-templates></xsl:apply-templates>

<xsl:if test="following-sibling::schild|oberwappen">: </xsl:if>
<xsl:for-each select="following-sibling::schild"><xsl:apply-templates/><xsl:if test="following-sibling::oberwappen[text()]">, </xsl:if></xsl:for-each>
<xsl:for-each select="following-sibling::oberwappen"><xsl:if test="not(starts-with(.,'auf dem Helm'))">auf dem Helm </xsl:if><xsl:value-of select="."/></xsl:for-each>
<xsl:if test="following-sibling::nachweis"> – </xsl:if>
<xsl:for-each select="following-sibling::nachweis"><xsl:apply-templates></xsl:apply-templates></xsl:for-each><xsl:if test="ancestor::index[@name='blasonierungen']"><xsl:text>.</xsl:text></xsl:if>

    </lemma><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:template>

<xsl:template match="nrn[node()]">
    <nrn><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <xsl:apply-templates></xsl:apply-templates>
    </nrn>
</xsl:template>

<xsl:template match="schild"></xsl:template>
<xsl:template match="oberwappen"></xsl:template>
<xsl:template match="nachweis"><xsl:apply-templates></xsl:apply-templates></xsl:template>

<xsl:template match="nr[parent::nrn]">
    <nr>
        <xsl:copy-of select="@target-section"/>
        <xsl:copy-of select="@target-article"/>
        <xsl:apply-templates></xsl:apply-templates>
    </nr><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:template>
<xsl:template match="nr[parent::index]"></xsl:template>

<xsl:template match="titel[parent::index]">
<titel><xsl:value-of select="parent::index/nr"/>. <xsl:value-of select="."/></titel>
</xsl:template>

<xsl:template match="verweise">
<xsl:apply-templates></xsl:apply-templates>
</xsl:template>

<xsl:template match="siehe">
<siehe>
<xsl:choose>
    <xsl:when test="@target_id">
        <xsl:attribute name="epi-id"><xsl:value-of select="@id"/></xsl:attribute>
        <xsl:attribute name="epi-target-id"><xsl:value-of select="@target_id"/></xsl:attribute>
    </xsl:when>
<!-- im personenregister ist für den verweis auf den familiennamen die target-id mit der id des verweiselements identisch; 
die id muss mit einem präfix von der target-id unterschieden werden  -->
    <xsl:otherwise>
        <xsl:attribute name="epi-id">s<xsl:value-of select="@id"/></xsl:attribute>
        <xsl:attribute name="epi-target-id"><xsl:value-of select="@id"/></xsl:attribute>
    </xsl:otherwise>
</xsl:choose>

<xsl:apply-templates></xsl:apply-templates></siehe>
</xsl:template>

<xsl:template match="siehe_auch">
<siehe_auch>
<xsl:choose>
    <xsl:when test="@target_id">
        <xsl:attribute name="epi-id"><xsl:value-of select="@id"/></xsl:attribute>
        <xsl:attribute name="epi-target-id"><xsl:value-of select="@target_id"/></xsl:attribute>
    </xsl:when>
<!-- im personenregister ist für den verweis auf den familiennamen die target-id mit der id des verweiselements identisch; 
die id muss mit einem präfix von der target-id unterschieden werden  -->
    <xsl:otherwise>
        <xsl:attribute name="epi-id">s<xsl:value-of select="@id"/></xsl:attribute>
        <xsl:attribute name="epi-target-id"><xsl:value-of select="@id"/></xsl:attribute>
    </xsl:otherwise>
</xsl:choose>

<xsl:apply-templates></xsl:apply-templates></siehe_auch>
</xsl:template>

</xsl:stylesheet>

