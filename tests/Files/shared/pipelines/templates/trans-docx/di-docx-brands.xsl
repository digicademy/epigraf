<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
    xmlns:v="urn:schemas-microsoft-com:vml" 
    xmlns:w10="urn:schemas-microsoft-com:office:word" 
    xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core" 
    xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"
    xmlns:o="urn:schemas-microsoft-com:office:office" 
    xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" 
    xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2" 
    xmlns:msxsl="urn:schemas-microsoft-com:xslt" 
    xmlns:exsl="http://exslt.org/common" 
    xmlns:php="http://php.net/xsl" 
    xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"
    xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture"
    xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"
    >
    
    <xsl:import href="../commons/di-switch.xsl"/>
    <xsl:import href="di-docx-styles.xsl"/>
    <xsl:import href="di-docx-commons.xsl"/>


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
             <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- titelseite für den abschnitt marken -->
                <w:p>
                    <w:pPr><w:pStyle w:val="epi-ueberschrift-2"/></w:pPr>
                    <!-- neue seite -->
                    <w:r><w:br w:type="page"/></w:r>
                    <!-- sprungmarke um den titel erzeugen -->
                    <bookmarkStart>
                        <xsl:attribute name="w:id"><xsl:value-of select="@id"/></xsl:attribute>
                        <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                    </bookmarkStart>
                    <!-- titel auslesen -->
                    <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>
                    <!-- schließen der sprungmarke -->
                    <w:bookmarkEnd>
                        <xsl:attribute name="w:id"><xsl:value-of select="@id"/></xsl:attribute>
                    </w:bookmarkEnd>
                </w:p>
                <!-- die abschnitte zu den verschiedneen markentypen ansteuern
                    (meisterzeichen, hausmarken u. dgl.)-->
                <xsl:for-each select="section">
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
                
            
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>    
            <xsl:comment>marken ende</xsl:comment>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>
    </xsl:template>
    
    <!-- die einzelne marke -->    
    <xsl:template match="brand">
        <!-- bezeichnung der marke zwischenspeichern -->
        <xsl:param name="p_bezeichnung"><xsl:value-of select="name"/></xsl:param>  
        <!-- liste der catalognummern derjenigen artikel erzeugen, in denen die marke auftritt -->
        <xsl:param name="p_artikelnummern_list">
            <xsl:for-each select="./nrn/nr">
                <xsl:variable name="v_articles_id"><xsl:value-of select="@articles_id"/></xsl:variable>       
                <article-nr><xsl:value-of select="ancestor::book/articles/article[@id=$v_articles_id]/@nr" /></article-nr>
            </xsl:for-each>
        </xsl:param>
        <!-- doubletten aus der liste entfernen, nummern sortieren -->
        <xsl:param name="p_artikelnummern_distinct">
            <xsl:for-each select="distinct-values($p_artikelnummern_list/article-nr)">
                <xsl:sort data-type="number" order="ascending"/>
                <article-nr><xsl:value-of select="." /></article-nr>
            </xsl:for-each>
        </xsl:param>
        <!-- kommata als trennzeichen einfügen -->
        <xsl:param name="p_artikelnummern_joined">
            <xsl:value-of select="string-join($p_artikelnummern_distinct/article-nr,', ')" />
        </xsl:param>
        
        <!-- parameter zu prüfzwecken ausgeben -->
        <!--   
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>marken-id: <xsl:copy-of select="$p_bezeichnung"/></xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>artikelnummern1: <xsl:copy-of select="$p_artikelnummern1"/></xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>artikelnummern2: <xsl:copy-of select="$p_artikelnummern2"/></xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
 -->
        <!-- tabellenzelle anlegen -->
        <w:tc>
            <w:tcPr>
                <w:tcW w:w="3000" w:type="dxa"/>
            </w:tcPr>
            <!-- absatz anlegen -->
            <w:p>
                <w:pPr>
                    <w:pStyle w:val="epi-normal-1"/>
                    <w:spacing w:line="240" w:lineRule="auto"/>
                    <w:jc w:val="left"/>
                    <w:rPr>
                        <w:noProof/>
                        <w:sz w:val="24"/>
                        <w:szCs w:val="24"/>
                    </w:rPr>
                </w:pPr>
                <!-- pfad auf das bild einfügen, 
                     die binären bilddateien werden erst auf der nächsten transformationsstufe
                     eingefügt,
                     weil der für dieses stylesheet anzuwendende parser 
                     keine funktion dafür bereit hält 
                -->
                <w:r>
                    <w:rPr>
                        <w:noProof/>
                        <w:sz w:val="24"/>
                        <w:szCs w:val="24"/>
                        <w:lang w:bidi="he-IL"/>
                    </w:rPr>
                    
                    <!-- auf epigrafWeb gespeicherte markenbilder -->
                    
                    <!-- das alte muster für wordML2003 aufrufen -->
                    <xsl:call-template name="w_pict"></xsl:call-template>
                    
                    <!-- beispile für das neue muster für DOCX:  -->
                    <xsl:call-template name="w_drawing"></xsl:call-template>
                    
                </w:r>
            </w:p>
            <!-- absatz für die markenlegende hinzufügen,
            die markenlegende besteht aus der bezeichnung der marke und 
            den nummern der katalogartikel, in der auf die marke referenziert wird, -->
            <w:p>
                <w:pPr><w:pStyle w:val="epi-marken"/></w:pPr>
                <w:r>
                    <w:rPr>
                        <w:bdr w:val="single" w:sz="4" w:space="1" w:color="auto"/>
                    </w:rPr>
                    <!-- bei zusammengefassten markenkategorien wird für die bezeichnung der marke als sigle vor der laufnummer ein M eingefügt;
   bei getrennten kategorien wird der erste buchstabe der bezeichnung des markentyps eingefügt-->
                    <xsl:choose>
                        <xsl:when test="$sw_markentypen_zusammenfassen = 1">
                            <w:t>M<xsl:value-of select="nr"/></w:t>
                        </xsl:when>
                        <xsl:otherwise><w:t><xsl:value-of select="substring(@brandtype_name,1,1)"/><xsl:value-of select="nr"/></w:t> </xsl:otherwise>
                    </xsl:choose>
               </w:r>
                <!-- katalognummern -->
                <xsl:if test="$p_artikelnummern_joined != ''">
                    <w:r><w:t> Kat.-Nr. <xsl:value-of select="$p_artikelnummern_joined"/></w:t></w:r>
                </xsl:if>
            </w:p>
        </w:tc>
    </xsl:template>  
    
    <!-- das alte muster für worML2003 -->
    <xsl:template name="w_pict">
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
    </xsl:template>
    
    <!-- beispiel für das neue bildmuster für docx -->
    <xsl:template name="w_drawing">
        <w:drawing>
            <wp:inline distT="0" distB="0" distL="0" distR="0">
                <wp:extent cx="899160" cy="899160"/>
                <wp:effectExtent l="19050" t="19050" r="15240" b="15240"/>
                <wp:docPr id="43" name="Bild 43" descr="marke"/>
                <wp:cNvGraphicFramePr>
                    <a:graphicFrameLocks
                        xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main"
                    />
                </wp:cNvGraphicFramePr>
                <a:graphic
                    xmlns:a="http://schemas.openxmlformats.org/drawingml/2006/main">
                    <a:graphicData
                        uri="http://schemas.openxmlformats.org/drawingml/2006/picture">
                        <pic:pic
                            xmlns:pic="http://schemas.openxmlformats.org/drawingml/2006/picture">
                            <pic:nvPicPr>
                                <pic:cNvPr id="0" name="Picture 43" descr="marke"/>
                                <pic:cNvPicPr>
                                    <a:picLocks noChangeArrowheads="1"/>
                                </pic:cNvPicPr>
                            </pic:nvPicPr>
                            <pic:blipFill>
                                <a:blip r:embed="rId137" cstate="print"/>
                                <a:srcRect/>
                                <a:stretch>
                                    <a:fillRect/>
                                </a:stretch>
                            </pic:blipFill>
                            <pic:spPr bwMode="auto">
                                <a:xfrm>
                                    <a:off x="0" y="0"/>
                                    <a:ext cx="899160" cy="899160"/>
                                </a:xfrm>
                                <a:prstGeom prst="rect">
                                    <a:avLst/>
                                </a:prstGeom>
                                <a:noFill/>
                                <a:ln w="6350" cmpd="sng">
                                    <a:solidFill>
                                        <a:srgbClr val="000000"/>
                                    </a:solidFill>
                                    <a:miter lim="800000"/>
                                    <a:headEnd/>
                                    <a:tailEnd/>
                                </a:ln>
                                <a:effectLst/>
                            </pic:spPr>
                        </pic:pic>
                    </a:graphicData>
                </a:graphic>
            </wp:inline>
        </w:drawing>
        
    </xsl:template>
    
</xsl:stylesheet>