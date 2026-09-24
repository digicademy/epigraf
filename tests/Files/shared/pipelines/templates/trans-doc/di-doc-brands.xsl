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


    <!-- mit diesem stylesheet werden die seiten 
         für die abbildungen der marken erzeugt;
            als grundraster werden tabellen mit tabellenzeilen 
            zu je vier tabellenzellen (spalten) angelegt, 
            die abbildungen der einzelnen marken werden 
            mit ihren bildunterschriften in die tabellenzellen eingefügt
            
            in der vorangegangenen transformation wurde bereits gruppen von je 
            vier marken gebildet (element <m-group>)
    
    
    
    -->
    
    <xsl:template match="brands">
        <xsl:if test="$sw_marken=1">
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
            <xsl:comment>marken anfang</xsl:comment>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- titelseite für den abschnitt marken -->
                <w:p>
                    <w:pPr><w:pStyle w:val="epi-ueberschrift-2"/></w:pPr>
                    <!-- neue seite -->
                    <w:r><w:br w:type="page"/></w:r>
                    <!-- sprungmarke um den titel erzeugen -->
                    <aml:annotation w:type="Word.Bookmark.Start">
                        <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                        <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                    </aml:annotation>
                    <!-- titel auslesen -->
                    <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>
                    <!-- schließen der sprungmarke -->
                    <aml:annotation w:type="Word.Bookmark.End">
                        <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                    </aml:annotation>
                </w:p>
                <!-- die abschnitte zu den verschiedneen markentypen ansteuern
                    (meisterzeichen, hausmarken u. dgl.)-->
                <xsl:for-each select="brand-type">
                    <xsl:variable name="v_pfad"><xsl:value-of select="file_name"/></xsl:variable>
                    <!-- abschnittsüberschrift erzeugen -->
                    <w:p>
                        <xsl:choose>
                            <xsl:when test="title">
                                <w:pPr><w:pStyle w:val="epi-ueberschrift-3z"/></w:pPr>
                                <!-- titel auslesen -->
                                <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>              
                            </xsl:when>
                            <xsl:otherwise>
                                <w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr>
                            </xsl:otherwise>
                        </xsl:choose>
                        
                    </w:p>
                    <!-- tabelle anlegen -->
                    <w:tbl>
                        <w:tblPr>
                            <w:tblW w:w="8900" w:type="dxa"/>
                            <w:tblInd w:w="0" w:type="dxa" />
                            <w:tblCellMar>
                                <w:left w:w="0" w:type="dxa"/>
                                <w:right w:w="0" w:type="dxa"/>
                            </w:tblCellMar>
                            <w:tblLook w:val="04A0"/>
                        </w:tblPr>
                        <!-- markengruppen ansteuern und für jede eine tabellenzeile erzeugen -->
                        <xsl:for-each select="brand-group">
                            <w:tr>
                                <w:trPr>
                                    <!-- 1 mm = 56,6928 twip; 46 mm = 2620 twip; 44 mm = 2495; 44,3 = 2511,496062992 -->
                                    <!-- höhe des satzspiegels 297mm - 30mm - 45,5mm = 221,5mm 
                  auf fünf zeilen je zeile 44,3 mm = = 2511,496062992 twips-->
                                    <w:trHeight w:val="2511"/>
                                </w:trPr>
                                <!-- marken in der gruppe ansteuern und an das entsprechende template verweisen
                                    (dort wird für jede marke eine tabellenzelle erzeugt) -->
                                <xsl:for-each select="brand">
                                    <xsl:apply-templates select=".">
                                        <xsl:with-param name="p_pfad"><xsl:value-of select="$v_pfad"/></xsl:with-param>
                                    </xsl:apply-templates>
                                </xsl:for-each>
                            </w:tr>
                        </xsl:for-each>
                        
                    </w:tbl>
                </xsl:for-each>
                
            </wx:sect>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
            <xsl:comment>marken ende</xsl:comment>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>
    </xsl:template>
    
    <!-- die einzelne marke -->    
    <xsl:template match="brand">
        <!-- bezeichnung der marke zwischenspeichern -->
        <xsl:param name="p_brandtype_sign"><xsl:value-of select="@brandtype-sign"/></xsl:param>  
        <!-- liste der catalognummern derjenigen artikel erzeugen, in denen die marke auftritt -->



        
        <!-- parameter zu prüfzwecken ausgeben -->
        <!--    
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:comment>marken-typ: <xsl:copy-of select="$p_brandtype_sign"/></xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->

        <!-- tabellenzelle anlegen -->
        <w:tc>
            <w:tcPr>
                <w:tcW w:w="3000" w:type="dxa"/>
            </w:tcPr>
            <!-- absatz anlegen -->
            <w:p wsp:rsidR="00841147" wsp:rsidRPr="000D3B78" wsp:rsidRDefault="009713D8" wsp:rsidP="000D3B78">
                <w:pPr>
                    <w:spacing w:line="240" w:line-rule="auto"/>
                    <w:jc w:val="left"/>
                    <w:rPr>
                        <w:noProof/>
                        <w:sz w:val="24"/>
                        <w:sz-cs w:val="24"/>
                    </w:rPr>
                </w:pPr>
                <!-- pfad auf das bild einfügen, 
                     die binären bilddateien werden erst auf der nächsten transformationsstufe
                     eingefügt,
                     weil der für dieses stylesheet anzuwendende parser 
                     keine funktion dafür bereit hält 
                -->
                <w:r wsp:rsidRPr="000D3B78">
                    <w:rPr>
                        <w:noProof/>
                        <w:sz w:val="24"/>
                        <w:sz-cs w:val="24"/>
                    </w:rPr>
                    
                    <!-- auf epigrafWeb gespeicherte markenbilder -->
                    <w:pict>
                        <v:shape id="_x0000_i1037" type="#_x0000_t75" style="width:71pt;height:71pt;visibility:visible;mso-wrap-style:square;mso-position-horizontal:absolute">
                            <v:imagedata o:title="marke">
                                <xsl:attribute name="src">
                                    <xsl:value-of select="concat('wordml://',file_name,'.png')" />
                                </xsl:attribute>
                            </v:imagedata>
                            <o:lock v:ext="edit" aspectratio="f"/>
                            <w10:bordertop type="single" width="4"/>
                            <w10:borderleft type="single" width="4"/>
                            <w10:borderbottom type="single" width="4"/>
                            <w10:borderright type="single" width="4"/>
                        </v:shape>
                        <!--<xsl:call-template name="markenbilder"></xsl:call-template>-->
                        
                        <!-- das element <marke/> für lokale transformation auskommentieren -->
                        <marke>
                            <file_name><xsl:value-of select="file_name"/></file_name>
                        </marke>
                    </w:pict>
                </w:r>
            </w:p>
            <!-- absatz für die markenlegende hinzufügen,
            die markenlegende besteht aus der bezeichnung der marke und 
            den nummern der katalogartikel, in der auf die marke referenziert wird, -->
            <w:p>
                <w:pPr><w:pStyle w:val="epi-marken"/></w:pPr>
                <w:r>
                    <w:rPr>
                        <w:bdr w:val="single" w:sz="4" wx:bdrwidth="10" w:space="1" w:color="auto"/>
                    </w:rPr>
                    <!-- bei zusammengefassten markenkategorien wird für die bezeichnung der marke als sigle vor der laufnummer ein M eingefügt;
   bei getrennten kategorien wird der erste buchstabe der bezeichnung des markentyps eingefügt-->
                    <xsl:choose>
                        <xsl:when test="$sw_markentypen_zusammenfassen = 1">
                            <w:t>M<xsl:value-of select="nr"/></w:t>
                        </xsl:when>
                        <xsl:otherwise><w:t><xsl:value-of select="$p_brandtype_sign"/><xsl:value-of select="nr"/></w:t> </xsl:otherwise>
                    </xsl:choose>
               </w:r>
                <!-- katalognummern -->
                    <w:r>
                        <w:t> Kat.-Nr. </w:t>
                <w:t><xsl:for-each select="sections/section">
                    <xsl:sort select="number(.)" data-type="number" order="ascending"/>
                    <xsl:value-of select="."/>
                    <xsl:if test="not(position()=last())">, </xsl:if>
                </xsl:for-each></w:t>
                </w:r>
            </w:p>
        </w:tc>
    </xsl:template>  
    
</xsl:stylesheet>