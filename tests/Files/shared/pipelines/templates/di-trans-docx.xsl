<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:fn="http://www.w3.org/2005/xpath-functions"
    xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
    exclude-result-prefixes="xs" version="2.0">
    
    <xsl:output method="xml" version="1.0" indent="yes" encoding="UTF-8"/>

    <xsl:import href="trans-docx/di-docx-styles.xsl"/>
    <xsl:import href="trans-docx/di-docx-fonts.xsl"/>
    <xsl:import href="trans-docx/di-docx-document.xsl"/>

    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="book">
        
        <!--ERSTENS: 
            in einem parameter die ordnerstruktur des docx-package und 
            die darin vorgesehenen datein nachbilden und die dateiinhalte erzeugen-->
        <xsl:param name="p_book_collect">
        <!-- package-ordner "job0000.docx" -->
        <d_docx>
        <!-- ordner "_rels" 
                verknüfungen zwischen dateien auf der root-ebene-->
            <d_rels>
                <!-- datei ".rels" -->
                <f.rels>
                    <!-- es werden die dateien 
                            docProps/app.xml und 
                            docProps/core.xml mit dem hautptdokument 
                            word/document.xml verknüpft -->
                    <Relationships
                        xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                        <Relationship Id="rId3"
                            Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties"
                            Target="docProps/app.xml"/>
                        <Relationship Id="rId2"
                            Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties"
                            Target="docProps/core.xml"/>
                        <Relationship Id="rId1"
                            Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument"
                            Target="word/document.xml"/>
                    </Relationships>
                </f.rels>
            </d_rels>
         <!-- ordner "docProps" -->
            <d_docProps>
                <!-- datei "app.xml" -->
                <f_app.xml>
                    <Properties
                        xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties"
                        xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
                        <Template>Normal.dotm</Template>
                        <TotalTime/>
                        <Pages/>
                        <Words/>
                        <Characters/>
                        <Application>Microsoft Office Word</Application>
                        <DocSecurity>0</DocSecurity>
                        <Lines/>
                        <Paragraphs/>
                        <ScaleCrop>false</ScaleCrop>
                        <Company>Die Deutschen Inschriften</Company>/>
                        <LinksUpToDate>false</LinksUpToDate>
                        <CharactersWithSpaces/>
                        <SharedDoc/>
                        <HyperlinksChanged/>
                        <AppVersion/>
                    </Properties>
                </f_app.xml>
                <!-- datei "core.xml" -->
                <f_core.xml>
                    <cp:coreProperties
                        xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties"
                        xmlns:dc="http://purl.org/dc/elements/1.1/"
                        xmlns:dcterms="http://purl.org/dc/terms/"
                        xmlns:dcmitype="http://purl.org/dc/dcmitype/"
                        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                        <dc:creator>EpigrafWeb</dc:creator>
                        <cp:lastModifiedBy>EpigrafWeb</cp:lastModifiedBy>
                        <cp:revision>1</cp:revision>
                        <dcterms:created xsi:type="dcterms:W3CDTF">
                            <xsl:value-of select="fn:current-dateTime()"/>
                        </dcterms:created>
                        <dcterms:modified xsi:type="dcterms:W3CDTF">
                            <xsl:value-of select="fn:current-dateTime()"/>
                        </dcterms:modified>
                    </cp:coreProperties>
                </f_core.xml>
            </d_docProps>
    <!-- ordner "word" - textdokument -->
            <d_word>
            <!-- ordner "_rels" - verknüpfungen zwischen dokumenten -->
                <d_rels>
                    <!-- datei "document.xml.rls" mit den verknüpfungen: 
                        verknüpft das hauptdokument (document.xml) 
                        mit den stilanweisungen (styles.xml), 
                        den fonts (fontTable.xml) und den 
                        abbildungen (media/image1.png)
                        
                        ==> beim einfügen von abbildungen in word werden die bilddateien umbenannt in image{laufnummer}.png
                        
                        ==> für den ordner media müssen diese bilder einzeln aufgelistet sein
                    
                    -->
                    <f_document.xml.rls>
                        <Relationships
                            xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
                            <Relationship Id="rId1"
                                Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/styles"
                                Target="styles.xml"/>
                        </Relationships>
                        <Relationship Id="rId2"
                            Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/fontTable"
                            Target="fontTable.xml"/>
                        
                        <!-- jedes einzelne image muss nach dem folgenden muster erfasst werden.
                             die einbindung in das hauptdokunet erfolgt über das attribut@Id,
                             die ansteuerung des image geht über das attribut @Target
                        -->
                        <Relationship Id="rId4" 
                            Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/image" 
                            Target="media/image1.png"/>
                    </f_document.xml.rls>
                </d_rels>
         <!-- ordner media -->
                <d_media>
                    <!-- hier werden die bilddateien eingefügt -->
                </d_media>
         <!-- hauptdokument: datei "document.xml"  -->
                <f_document.xml> 
                    <xsl:call-template name="document"></xsl:call-template>
                </f_document.xml>
          
         <!-- fonts: datei"fontTable.xml" -->
                <f_fontTable.xml>
                    <xsl:call-template name="fonts"></xsl:call-template>
                </f_fontTable.xml>

          <!-- stilanweisungen: datei "styles.xml"  -->
                <f_styles.xml>
                    <xsl:call-template name="styles"></xsl:call-template>
                </f_styles.xml>
                
          <!-- weitere eventuell einzustellende dateien sind:
                    "settings.xml" (einstellungen des word-programms)
                    "webSettings.xml" (einstellung für browserdarstellung, <w:optimizeForBrowser/>)
          -->      

            </d_word>

            <f_content_Types.xml>
                <!-- Jedes package muss eine Datei [Content_Types].xml haben, die sich im root-Ordner des packages befindet. 
                    Diese Datei enthält eine Liste aller Inhaltstypen der Teile des packages. 
                    Jeder Teil und sein Typ muss in [Content_Types].xml aufgeführt sein.  -->
                <Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
                    <Override PartName="/word/footnotes.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footnotes+xml"/>
                    <Default Extension="png" ContentType="image/png"/>
                    <Default Extension="rels"
                        ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
                    <Default Extension="xml" ContentType="application/xml"/>
                    <Override PartName="/word/document.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>
                    <Override PartName="/word/styles.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.styles+xml"/>
                    <Override PartName="/word/endnotes.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.endnotes+xml"/>
                    <Override PartName="/word/footer4.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"/>
                    <Override PartName="/docProps/app.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
                    <Override PartName="/word/settings.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.settings+xml"/>
                    <Override PartName="/word/footer2.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"/>
                    <Override PartName="/word/footer3.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"/>
                    <Override PartName="/word/footer1.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.footer+xml"/>
                    <Override PartName="/word/header4.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.header+xml"/>
                    <Override PartName="/word/theme/theme1.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.theme+xml"/>
                    <Override PartName="/word/header2.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.header+xml"/>
                    <Override PartName="/word/header3.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.header+xml"/>
                    <Override PartName="/word/fontTable.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.fontTable+xml"/>
                    <Override PartName="/word/webSettings.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.webSettings+xml"/>
                    <Override PartName="/word/header1.xml"
                        ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.header+xml"/>
                    <Override PartName="/docProps/core.xml"
                        ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
                </Types>
            </f_content_Types.xml>

        </d_docx>
        </xsl:param>
        
        <!-- ZWEITENS:        
                aus dem parameter die für das erzeugen des docx-pacckage erforderlichen inhalte auslesen
        -->
        
        <!-- den inhalt für die datei docx/word/document.xml auslesen -->
        <xsl:for-each select="$p_book_collect">
            <xsl:for-each select="d_docx/d_word/f_document.xml/*[name()='w:document']">
                <xsl:copy-of select="."></xsl:copy-of>
            </xsl:for-each>
        </xsl:for-each>

    </xsl:template>

</xsl:stylesheet>
