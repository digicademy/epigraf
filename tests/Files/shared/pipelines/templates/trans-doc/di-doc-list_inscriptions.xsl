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
    <xsl:import href="di-doc-commons.xsl"/>

<!-- für bände mit vielen objekten, auf denen sich inschriften 
        verschiedener zeitstellung befinden, kann eine 
        chronologisch sortierte liste aller inschriften erstellt werden -->

    <xsl:template match="table_of_inscriptions">
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
        <xsl:comment>inschriftenliste anfang</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- gegebenfalls linke leerseite einschieben -->
            <xsl:if test="preceding-sibling::*">
                <xsl:copy-of select="$p_odd-page"/>
            </xsl:if>
            <!-- überschrift  -->
            <w:p>
                <w:pPr>
                    <w:pStyle w:val="epi-ueberschrift-1"/>
                </w:pPr>
                <!-- sprungmarke öffnen und titel auslesen -->
                <aml:annotation w:type="Word.Bookmark.Start">
                    <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                    <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                </aml:annotation>
                <!-- titel einfügen -->
                <w:r><w:t><xsl:value-of select="normalize-space(title)"/></w:t></w:r>
                <!-- sprungmarke schließen -->
                <aml:annotation w:type="Word.Bookmark.End">
                    <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                </aml:annotation>        
            </w:p>
            <!-- vorbemerkung einfügen -->
            <xsl:for-each select="p[not(text()='§')]">
                <w:p>
                    <w:pPr><w:pStyle w:val="epi-normal-1"/>
                        <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
                    </w:pPr>
                </w:p>
            </xsl:for-each>
            <!-- leerezeile -->
            <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
            <!-- abschnitt mit der überschrift einspaltig formatieren -->
            <w:p wsp:rsidR="00533250" wsp:rsidRDefault="00533250">
                <w:pPr>
                    <w:sectPr wsp:rsidR="00533250" wsp:rsidSect="00533250">
                        <w:type w:val="continuous"/>
                        <w:pgSz w:w="11906" w:h="16838"/>
                        <w:pgSz w:w="11906" w:h="16838" />
                        <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                        <!-- einspaltig -->
                        <w:cols w:num="1" w:space="708"/>
                        <w:docGrid w:line-pitch="360"/>
                    </w:sectPr>
                </w:pPr>
            </w:p>
        </wx:sect>
        <wx:sect>
            
        <!-- liste erstellen -->
            
            <!-- 1. die verschiedenen zeitstellungen (datierungen) ansteuern -->
        <xsl:for-each select="items/dating">
            <!-- atierung ausgeben -->
            <w:p>
                <w:pPr>
                    <w:pStyle w:val="epi-register-eintrag"/>
                    <xsl:if test="not(position()=1)"><!--<w:spacing w:before="40"/>--></xsl:if>
                </w:pPr>
                <w:r><w:t><xsl:value-of select="dating"/></w:t></w:r>
            </w:p>
            <!-- 2. objekte der betreffenden zeitstellung ansteuern -->
            <xsl:for-each select="objekt">
                <w:p>
                    <w:pPr>
                        <w:pStyle w:val="epi-register-eintrag"/>
                        <w:ind w:left="200"/>
                    </w:pPr>
                    <!-- objektbezeichnung ausgeben (inschriftenträger) -->
                    <w:r><w:t><xsl:value-of select="objekttyp"/>: </w:t></w:r>
                    <!-- referenzen auf artikelnummern ansteuern -->
                    <xsl:for-each select="article">
                        <!-- artikelnummer ausgeben -->
                        <w:r><w:t><xsl:value-of select="artikelnummer"/></w:t></w:r>
                        <!-- die zu sequenzen zusammengefassten inschriftennumern ansteuern -->
                        <xsl:for-each select="sequenz">
                            <!-- anzahl der in der sequenz enthaltenen nummern ermitteln -->
                            <xsl:variable name="v_numbers" select="count(inschriftnummer)"></xsl:variable>
                            <xsl:choose>
                                <!-- bei mehreren nummern die erste und die letzte auswählen 
                                    und mit spiegelstrich verbinden -->
                                <xsl:when test="$v_numbers != 1">
                                    <!-- erster inschrift-buchstabe -->
                                    <w:r>
                                        <w:rPr><w:sz w:val="16"/></w:rPr>
                                        <w:t><xsl:value-of select="inschriftnummer[1]"/></w:t>
                                    </w:r>
                                    <!-- spiegelstrich -->
                                    <w:r>
                                        <w:rPr><w:sz w:val="16"/></w:rPr>
                                        <w:t>&#x2013;</w:t>
                                    </w:r>
                                    <!-- letzter inschriftbuchstabe -->
                                    <w:r>
                                        <w:rPr><w:sz w:val="16"/></w:rPr>
                                        <w:t><xsl:value-of select="inschriftnummer[last()]"/></w:t>
                                    </w:r>
                                </xsl:when>
                                <!-- bei nur einer nummer,dies auswählen -->
                                <xsl:otherwise>
                                    <w:r>
                                        <w:rPr><w:sz w:val="16"/></w:rPr>
                                        <w:t><xsl:value-of select="inschriftnummer"/></w:t>
                                    </w:r>
                                </xsl:otherwise>
                            </xsl:choose>
                            <!-- wenn weitere inschrift-nummern-sequenzen folgen, ein komma setzten -->
                            <xsl:if test="following-sibling::sequenz">
                                <w:r><w:t>, </w:t></w:r>
                            </xsl:if>
                        </xsl:for-each>
                        <!-- wenn weitere artikelnummern folgen, ein komma setzten -->
                        <xsl:if test="following-sibling::article">
                            <w:r><w:t>, </w:t></w:r>
                        </xsl:if>
                    </xsl:for-each>
                </w:p>
            </xsl:for-each>
        </xsl:for-each>
            
            
            <!-- der letzte absatz enthält die angabe zur dreispaltigen darstellung der liste -->
            <w:p wsp:rsidR="00533250" wsp:rsidRDefault="00533250">
                <w:pPr>
                    <w:sectPr wsp:rsidR="00533250" wsp:rsidSect="00533250">
                        <w:type w:val="continuous"/>
                        <w:pgSz w:w="11906" w:h="16838"/>
                        <w:pgSz w:w="11906" w:h="16838" />
                        <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                        <!-- drei spalten -->
                        <w:cols w:num="3" w:space="708"/>
                        <w:docGrid w:line-pitch="360"/>
                    </w:sectPr>
                </w:pPr>
            </w:p>
        </wx:sect>
        <!-- danach wieder einspaltig
                (kann wahrscheinlich entfallen)
        -->
        <wx:sect>
            <w:p wsp:rsidR="00533250" wsp:rsidRDefault="00533250">
                <w:pPr>
                    <w:sectPr wsp:rsidR="00533250" wsp:rsidSect="00533250">
                        <w:type w:val="continuous"/>
                        <w:pgSz w:w="11906" w:h="16838"/>
                        <w:pgSz w:w="11906" w:h="16838" />
                        <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                        <w:cols w:num="1" w:space="708"/>
                        <w:docGrid w:line-pitch="360"/>
                    </w:sectPr>
                </w:pPr>
            </w:p>
        </wx:sect>
        <xsl:comment>inschriftenliste ende</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

</xsl:stylesheet>