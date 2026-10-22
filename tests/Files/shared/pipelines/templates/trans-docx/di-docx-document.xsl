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
    
    <xsl:import href="../commons/di-switch.xsl"/> <!-- "Schalter" zum Aus- und Einblenden bestimmter Abschnitte des Layouts -->
    <xsl:import href="di-docx-styles.xsl"/>
    <xsl:import href="di-docx-fonts.xsl"/>
    <xsl:import href="di-docx-commons.xsl"/>
    
    <xsl:import href="di-docx-preliminaries.xsl"/>  
    <xsl:import href="di-docx-prefaces.xsl"/> 
    <xsl:import href="di-docx-list_content.xsl"/>
    <xsl:import href="di-docx-introduction.xsl"/>
    <xsl:import href="di-docx-articles.xsl"/>
    <xsl:import href="di-docx-abbreviations.xsl"/>
    
    <xsl:import href="di-docx-indexes.xsl"/>
    
    <xsl:import href="di-docx-literature.xsl"/>
    <xsl:import href="di-docx-di_volumes.xsl"/>
    <xsl:import href="di-docx-plates.xsl"/>
    <xsl:import href="di-docx-drawings.xsl"/>
    <xsl:import href="di-docx-maps.xsl"/>
    
    
    <xsl:import href="di-docx-brands.xsl"/>
    <xsl:import href="di-docx-heraldry.xsl"/>
    
    
    <xsl:template name="document">
        <w:document xmlns:ve="http://schemas.openxmlformats.org/markup-compatibility/2006"
            xmlns:o="urn:schemas-microsoft-com:office:office"
            xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"
            xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math"
            xmlns:v="urn:schemas-microsoft-com:vml"
            xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"
            xmlns:w10="urn:schemas-microsoft-com:office:word"
            xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
            xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml">
            
            <w:body>
            <!-- der wurzelknoten wird an ein template verwiesen und 
    dadurch auf das wurzelelement (<book>) gelenkt-->
            <xsl:apply-templates/>
            
            <!-- eigenschaften der seiten: 
    die seiteneigenschaften eines dokument-abschnitts werden immer am 
    ende des betreffenden abschnitts eingefügt und gelten bis dorthin, wo sie eingefügt sind;
    -->    
            <w:sectPr>
                <w:hdr w:type="even">
                    <w:p>
                        <w:pPr>
                            <w:pStyle w:val="epi-seitenkopf"/>
                        </w:pPr>
                    </w:p>
                </w:hdr>
                <w:hdr w:type="odd">
                    <w:p>
                        <w:pPr>
                            <w:pStyle w:val="epi-normal-1"/>
                        </w:pPr>
                    </w:p>
                </w:hdr>
                <w:ftr w:type="even">
                    <w:p>
                        <w:pPr>
                            <w:pStyle w:val="epi-seitenfuss"/>
                        </w:pPr>
                        <w:fldSimple w:instr=" PAGE \* MERGEFORMAT ">
                            <w:r>
                                <w:rPr>
                                    <w:noProof/>
                                </w:rPr>
                                <w:t>2</w:t>
                            </w:r>
                        </w:fldSimple>
                    </w:p>
                    <w:p>
                        <w:pPr>
                            <w:pStyle w:val="epi-seitenfuss"/>
                        </w:pPr>
                    </w:p>
                </w:ftr>
                <w:ftr w:type="odd">
                    <w:p>
                        <w:pPr>
                            <w:pStyle w:val="epi-seitenfuss"/>
                            <w:jc w:val="right"/>
                        </w:pPr>
                        <w:fldSimple w:instr=" PAGE \* MERGEFORMAT ">
                            <w:r>
                                <w:rPr>
                                    <w:noProof/>
                                </w:rPr>
                                <w:t>22</w:t>
                            </w:r>
                        </w:fldSimple>
                    </w:p>
                    <w:p>
                        <w:pPr>
                            <w:pStyle w:val="epi-seitenfuss"/>
                        </w:pPr>
                    </w:p>
                </w:ftr>
                <w:type w:val="continuous"/>
                <w:pgSz w:w="11906" w:h="16838"/>
                <!-- satzspiegel breite 8107 twips (14,3 cm) , höhe 13124 twips (23,15 cm)
        22,15 cm = 12559 twips
      1 cm 0 567 twips
      -->
                <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800" w:gutter="0"/>
                <xsl:if test="$sw_register=1 and .//indices[not(following-sibling::*)]"><w:cols w:num="3" w:space="708"/></xsl:if>
                <xsl:if test="$sw_register!=1"><w:cols w:space="708"/></xsl:if>
                <w:docGrid w:linePitch="360"/>
            </w:sectPr>
            
            
        </w:body>
    </w:document>
    </xsl:template>
    
</xsl:stylesheet>