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

    <!-- 
        in diesem stylesheet werden die in der werkzeugleiste von epigraf angebotenen 
        trankriptions- und textauszeichnungen formatiert;
        
        nota bene: auszeichnungen die nur als textformate (kursiv, gesperrt, fett usw.) wiederzugeben sind, 
        werden im stylesheet di-doc-commons.xsl im template match=text() formatiert
    -->

    <!-- kennzeichnung vorerst ausgelassener und später hinzugefügter zeichen in spitzen klammern -->
    <xsl:template match="add">
        <w:r>
            <w:rPr>
                <w:rFonts w:ascii="Cambria" w:h-ansi="Cambria"/>
                <xsl:if test="ancestor::quot"><w:i/><w:spacing w:val="-40"/></xsl:if>
            </w:rPr>
            <w:t>〈</w:t>
        </w:r>
        <xsl:apply-templates></xsl:apply-templates>
        <w:r>
            <w:rPr>
                <w:rFonts w:ascii="Cambria" w:h-ansi="Cambria"/>
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
                    <w:rStyle w:val="epi-zitat"/>
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
    
    <!-- versionsbuchstaben (bearbeitungen) in verweisen auf inschriften hochstellen -->  
    <xsl:template match="su">
        <w:r><w:rPr><w:rStyle w:val="epi-version"/></w:rPr><w:t><xsl:value-of select="."/></w:t></w:r>
    </xsl:template>
    
</xsl:stylesheet>