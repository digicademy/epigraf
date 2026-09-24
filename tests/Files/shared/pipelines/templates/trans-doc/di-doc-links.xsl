<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
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
    
    <xsl:import href="../commons/di-switch.xsl"/>
    <xsl:import href="di-doc-styles.xsl"/>

    <!-- in diesem stylesheet werden alle arten von verweisen verarbeitet, 
        die in anderen stylsheets der dritten transformationsstufe 
        aufgerufen werden -->
    

    <!-- weblinks -->
    <xsl:template match="a">
        <w:r><w:fldChar w:fldCharType="begin"/></w:r>
        <w:r>
            <w:instrText> HYPERLINK "<xsl:value-of select="@href"/>" </w:instrText>
        </w:r>
        <w:r><w:fldChar w:fldCharType="separate"/></w:r>
        <w:r><w:rPr><w:rStyle w:val="Hyperlink"/></w:rPr><w:t><xsl:value-of select="@value"/></w:t></w:r>
        <w:r><w:fldChar w:fldCharType="end"/></w:r>
    </xsl:template>

    <!-- VERWEISE -->
    <!-- verweis von einer beliebigen stelle auf eine fußnote im selben artikel -->
    <xsl:template match="link[@type='footnotes']">
        <xsl:variable name="v_target_fussnote"><xsl:value-of select="@data-link-target"/></xsl:variable>
        <xsl:choose>
            <!-- von der einleitung aus-->
            <xsl:when test="ancestor::introduction">
                <xsl:for-each select="ancestor::introduction//app1[@id=$v_target_fussnote]">
                    <w:r><w:t><xsl:number level="any" count="app1" from="introduction" format="1"/></w:t></w:r>
                </xsl:for-each>    
            </xsl:when>
            <!-- von fußnoten aus -->
            <xsl:otherwise>
                <xsl:for-each select="ancestor::article/footnotes//item[@id=$v_target_fussnote]">
                    <xsl:choose>
                        <xsl:when test="parent::letter_footnotes">
                            <w:r><w:t><xsl:number level="any" count="item" from="letter_footnotes" format="a"/></w:t></w:r>
                        </xsl:when>
                        <xsl:when test="parent::digit_footnotes">
                            <w:r><w:t><xsl:number level="any" count="item" from="digit_footnotes" format="1"/></w:t></w:r>
                        </xsl:when>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <!-- verweis auf eine fußnote in einem anderen artikel -->
    <xsl:template match="link[@type='footnotes_extern']">
        <xsl:variable name="v_target_fussnote"><xsl:value-of select="@data-link-target"/></xsl:variable>
        <!-- artikelnummer auslesen -->
        <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
        <!-- fußnotenbezeichner ermitteln -->
        <xsl:for-each select="ancestor::book/articles/article/footnotes//item[@id=$v_target_fussnote]">
            <xsl:choose>
                <xsl:when test="parent::letter_footnotes">
                    <w:r><w:t>, Anm. <xsl:number level="any" count="item" from="letter_footnotes" format="a"/></w:t></w:r>
                </xsl:when>
                <xsl:when test="parent::digit_footnotes">
                    <w:r><w:t>, Anm. <xsl:number level="any" count="item" from="digit_footnotes" format="1"/></w:t></w:r>
                </xsl:when>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>    

    <!-- verweis auf eine inschrift im selben artikel -->
    <xsl:template match="link[@type='inscription' or @type='inschrift']">
        <xsl:for-each select="node()">
            <xsl:choose>
                <xsl:when test="name() = 'version-nr'">
                    <w:r>
                        <w:rPr>
                            <w:vertAlign w:val="superscript"/>
                            <w:u w:val="single" w:color="black"/>
                        </w:rPr>
                        <w:t><xsl:value-of select="."/></w:t>
                    </w:r>                    
                </xsl:when>
                <xsl:otherwise><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:otherwise>
            </xsl:choose>                
        </xsl:for-each>
    </xsl:template>

    <!-- verweis auf eine inschrift in einem anderen artikel -->
    <!-- 1. artikel ansteuern -->
    <xsl:template match="link[@type='sections']">
        <!--<w:r><w:t><xsl:value-of select="."/></w:t></w:r>-->
        <xsl:apply-templates />
    </xsl:template>
    <!-- 2. artikelnummer auslesen -->
    <xsl:template match="link[@type='sections']/text()">
        <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
    </xsl:template>
    <!-- 3. verlängerung des verweises auf die inschrift auslesen -->    
    <xsl:template match="inscriptlink">
        <xsl:choose>
            <xsl:when test="ancestor::footnotes"><w:r><w:rPr><w:sz w:val="15"/></w:rPr><w:t><xsl:value-of select="."/></w:t></w:r></xsl:when>
            <xsl:otherwise><w:r><w:rPr><w:sz w:val="16"/></w:rPr><w:t><xsl:value-of select="."/></w:t></w:r></xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- literaturverweis -->
    <xsl:template match="link[@type='literatur']">
        <xsl:param name="p_plaintext" select="0" />
        <xsl:choose>
            <xsl:when test="$p_plaintext=1"><xsl:value-of select="."/></xsl:when>
            <xsl:otherwise><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <!--verweise auf marken-->
    <xsl:template match="link[@type='marke']">  
        <xsl:param name="p_root" select="ancestor::book" /><!-- der band wird zwischengespeichert -->
        <xsl:param name="p_plaintext" select="0"/> <!-- bedeutet: textknoten sind nicht schon als runs <w:r> getagt -->
        <xsl:variable name="v_data-link-value" select="@data-link-target" />
        
        <xsl:for-each select="$p_root//brands//brand[@id=$v_data-link-value]">
            <!-- die marke wird im markenverzeichnis angesteuert -->
            <!-- bei zusammengefassten markenkategorien wird für die 
                bezeichnung der marke als sigle vor der laufnummer ein M eingefügt;
                
                bei getrennten kategorien wird der erste buchstabe der bezeichnung 
                des markentyps eingefügt-->
            <xsl:choose>
                <xsl:when test="$p_plaintext=1"> <!-- bedeutet: textknoten sind bereits als runs <w:r> getagt -->
                    <xsl:choose>
                        <xsl:when test="$sw_markentypen_zusammenfassen = 1">M<xsl:value-of select="nr"/></xsl:when>
                        <xsl:otherwise><xsl:value-of select="substring(@brandtype-name,1,1)"/><xsl:value-of select="nr"/></xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise> <!-- bedeutet: textknoten sind nicht schon als runs <w:r> getagt -->
                    <w:r><w:t><xsl:choose>
                        <xsl:when test="$sw_markentypen_zusammenfassen = 1">M<xsl:value-of select="nr"/></xsl:when>
                        <xsl:otherwise><xsl:value-of select="substring(@brandtype-name,1,1)"/><xsl:value-of select="nr"/></xsl:otherwise>
                    </xsl:choose></w:t></w:r>        
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>
    
</xsl:stylesheet>