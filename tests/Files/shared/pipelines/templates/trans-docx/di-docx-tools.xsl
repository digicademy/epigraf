<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
    xmlns:v="urn:schemas-microsoft-com:vml" 
    xmlns:w10="urn:schemas-microsoft-com:office:word" 
    xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core" 
    xmlns:o="urn:schemas-microsoft-com:office:office" 
    xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" 
    xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2" 
    xmlns:msxsl="urn:schemas-microsoft-com:xslt" 
    xmlns:exsl="http://exslt.org/common" 
    xmlns:php="http://php.net/xsl" 
    >
    
    <xsl:import href="../commons/di-switch.xsl"/>
    <xsl:import href="di-docx-styles.xsl"/>

    <!-- 
        in diesem stylesheet werden die in der werkzeugleiste von epigraf angebotenen 
        trankriptions- und textauszeichnungen formatiert;
        
        nota bene: auszeichnungen die nur als textformate (kursiv, gesperrt, fett usw.) wiederzugeben sind, 
        werden im stylesheet di-doc-commons.xsl im template match=text() formatiert
    -->

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

    <!-- kennzeichnung vorerst ausgelassener und später hinzugefügter zeichen in spitzen klammern -->
    <xsl:template match="add">
        <w:r>
            <w:rPr>
                <w:rFonts w:ascii="Cambria"/>
                <xsl:if test="ancestor::quot"><w:i/><w:spacing w:val="-40"/></xsl:if>
            </w:rPr>
            <w:t>〈</w:t>
        </w:r>
        <xsl:apply-templates></xsl:apply-templates>
        <w:r>
            <w:rPr>
                <w:rFonts w:ascii="Cambria"/>
                <xsl:if test="ancestor::quot"><w:i/><w:spacing w:val="-40"/></xsl:if>
            </w:rPr>
            <w:t>〉</w:t>
        </w:r>  
    </xsl:template>
    
    <!-- zwischenräume / lacunae -->
    <xsl:template match="spatium_v">
        <xsl:param name="p_width"><xsl:value-of select="@width"/></xsl:param>
        <w:r><w:t>
            <xsl:call-template name="spatium_auslesen">
                <xsl:with-param name="p_zaehler1"><xsl:value-of select="$p_width"/></xsl:with-param>
            </xsl:call-template>
        </w:t></w:r>
    </xsl:template>
    <xsl:template name="spatium_auslesen">
        <xsl:param name="p_zaehler1"></xsl:param>
        <xsl:param name="p_zaehler2" select="$p_zaehler1 - 1"></xsl:param>
        <xsl:if test="$p_zaehler1 &gt; 0"><xsl:text>&#x00A0;</xsl:text>
            <xsl:call-template name="spatium_auslesen">
                <xsl:with-param name="p_zaehler1"><xsl:value-of select="$p_zaehler2"/></xsl:with-param>
            </xsl:call-template>
        </xsl:if>
    </xsl:template>
    
    <!-- kennzeichnung von unsicherer lesung durch unterpunkte -->
    <xsl:template match="insec[not(wtr)]">
        <xsl:for-each select=".">
            <xsl:if test="string-length() &gt; 0">	       	    
                <xsl:call-template name="InsecSchleife">
                    <xsl:with-param name="p_Zaehler" select="1" />
                    <xsl:with-param name="p_Ende" select="string-length()" />	      
                </xsl:call-template>
            </xsl:if>
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="InsecSchleife">
        <xsl:param name="p_Zaehler" />
        <xsl:param name="p_Ende" />
        <xsl:if test="$p_Zaehler &lt;= $p_Ende">
            <w:r>
                <!-- wenn unsichere lesung in einem zitatat auftritt, wird kursiviert -->
                <xsl:if test="ancestor::quot">
                    <w:rPr><w:rStyle w:val="epi-zitat"/></w:rPr>
                </xsl:if>
                <!-- erhöhung der laufweite bei ziffern, damit der punkt unter der ziffer und nicht rechts daneben steht -->
                <xsl:if test="substring(.,$p_Zaehler,1)='1' or
                    substring(.,$p_Zaehler,1)='2' or
                    substring(.,$p_Zaehler,1)='3' or
                    substring(.,$p_Zaehler,1)='4' or
                    substring(.,$p_Zaehler,1)='5' or
                    substring(.,$p_Zaehler,1)='6' or
                    substring(.,$p_Zaehler,1)='7' or
                    substring(.,$p_Zaehler,1)='8' or
                    substring(.,$p_Zaehler,1)='9' or
                    substring(.,$p_Zaehler,1)='0' 
                    ">
                    <xsl:choose>
                        <!-- in fußnoten -->
                        <xsl:when test="ancestor::item"><w:rPr><w:spacing w:val="25"/></w:rPr></xsl:when>
                        <!-- im trankriptionsfeld -->
                        <xsl:otherwise><w:rPr><w:spacing w:val="30"/></w:rPr></xsl:otherwise>
                    </xsl:choose>
                    
                </xsl:if>
                <!-- die zeichen innerhalb des bereichs unsicherer lesung werden einzeln angesteuert 
          und jeweils danach ein unterpunkt (&#x0323;) aus dem unicode-bereich der combinierbaren 
          diakritischen zeichen gesetzt -->
                <w:t><xsl:value-of select="substring(.,$p_Zaehler,1)"/></w:t></w:r><w:r><w:t><xsl:text>&#x0323;</xsl:text></w:t></w:r>
            <xsl:call-template name="InsecSchleife">
                <xsl:with-param name="p_Zaehler" select="$p_Zaehler + 1" />
                <xsl:with-param name="p_Ende" select="$p_Ende" />
            </xsl:call-template>
        </xsl:if>
    </xsl:template>    

    <!--Worttrenner-->
    <xsl:template match="wtr[ancestor::bl or ancestor::item]">
        <!-- worttrenner in zitat (element <quot>): das zeichen wird kursiviert, 
             in einem bereich unsicherer lesung (element <insec>): das zeichen wird mit unterpunkt versehen -->
        <w:r><xsl:if test="ancestor::quot"><w:rPr><w:rStyle w:val="epi-zitat"/></w:rPr></xsl:if><w:t><xsl:value-of select="."/></w:t></w:r><xsl:if test="parent::insec"><w:r><w:t>&#x0323;</w:t></w:r></xsl:if></xsl:template>
    
    <!--Fussnotenzeichen im Text / footnote characters-->
    <!--1. der Zahlenapparat font-size="7pt"-->
    <xsl:template match="app1">
        <xsl:choose>
            <!-- im artikel -->
            <xsl:when test="ancestor::article">
                <w:r>
                    <w:rPr>
                        <w:rStyle w:val="epi-fussnotenzeichen"/>
                        <!-- in kursiven absätzen wird das fußnotenzeichen recte ausgegeben -->
                        <w:i w:val="off"/>
                    </w:rPr>
                    <!-- es wird artikelweise eine fortlaufende nummerierung erzeugt -->
                    <w:t><xsl:number level="any" from="article" count="app1" format="1)"/></w:t>
                </w:r>      
            </xsl:when>
            <!-- in der einleitung -->
            <xsl:otherwise>
                <w:r>
                    <w:rPr>
                        <w:rStyle w:val="epi-fussnotenzeichen"/>
                    </w:rPr>
                    <!-- der word-automatismus zur erzeugug der fußnoten 
                        wird aufgerufen (<w:footnote> und <w:footnoteRef/>);
                        
                        der fußnotentext wird an der stelle eingefügt, 
                        an der das fußnotenzeichen erscheinen soll,
                        die plazierung der fußnoten am unteren seitenende 
                        wird von word automatisiert vorgenommen
                    -->
                    <w:footnote>
                        <!-- absätze in der fußnote ansteuern -->
                        <xsl:for-each select="p">          
                            <w:p>
                                <w:pPr>
                                    <w:pStyle w:val="epi-apparat"/>
                                </w:pPr>
                                <!-- vor dem ersten absatz wird die nummer erzeugt -->
                                <xsl:if test="not(preceding-sibling::p)">
                                    <w:r>
                                        <w:rPr>
                                            <w:rStyle w:val="epi-fussnotenzeichen"/>
                                        </w:rPr>
                                        <w:footnoteRef/>
                                    </w:r>
                                    <w:r><w:tab/></w:r>
                                </xsl:if>
                                <!-- inhalt der absätze einer fußnote an templates verweisen  -->
                                <xsl:apply-templates></xsl:apply-templates>
                            </w:p>
                        </xsl:for-each> 
                    </w:footnote>
                </w:r>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!--2. der Buchstabenapparat font-size="7pt"-->
    <xsl:template match="app2"><w:r><w:rPr><w:vertAlign w:val="superscript"/></w:rPr><w:t><xsl:number level="any" from="article" count="app2" format="a)"/></w:t></w:r></xsl:template>
    
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
    <xsl:template match="link[@type='inscription']">
        <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
    </xsl:template>
    <!-- versionsbuchstaben (barbeitungen) in verweisen auf inschriften hochstellen -->  
    <xsl:template match="su">
        <w:r><w:rPr><w:rStyle w:val="epi-version"/></w:rPr><w:t><xsl:value-of select="."/></w:t></w:r>
    </xsl:template>
    
    <!-- verweis auf eine inschrift in einem anderen artikel -->
    <!-- 1. artikel ansteuern -->
    <xsl:template match="link[@type='sections']">
        <!--<w:r><w:t><xsl:value-of select="."/></w:t></w:r>-->
        <xsl:apply-templates></xsl:apply-templates>
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
        <xsl:param name="p_plaintext" select="0"/> <!-- bedeutet: textknoten sind nicht schon als runs <w:r> getagt -->
        <xsl:variable name="v_data-link-value">
            <xsl:value-of select="@data-link-target"/>
        </xsl:variable>
        <xsl:for-each select="ancestor::book//brands//brand[@id=$v_data-link-value]">
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