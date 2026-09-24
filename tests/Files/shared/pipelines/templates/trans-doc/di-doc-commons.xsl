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
    xmlns:php="http://php.net/xsl" 
    >
    
    <xsl:import href="../commons/di-switch.xsl"/>
    <xsl:import href="di-doc-styles.xsl"/>


<!-- in diesem stylesheet werden verschiedene elemente formatiert, 
        die in anderen stylesheets aufgerufen werden -->


    <!-- bezeichnung der datenbank -->
    <xsl:param name="p_db"><xsl:value-of select="book/project/@database"/></xsl:param>
    
<!-- 
        leere linke seite zum einfügen als vorsatzseite vor dem beginn bestimmter abschnitte;
        der parameter wird im folgenden aufgerufen, wenn eine solche seite einzufügen ist
-->
    <!-- höhe des satzspiegels 14879 twips -->
    <xsl:param name="p_odd-page">
        <w:p>
            <w:pPr>
                <w:sectPr>
                    <w:type w:val="odd-page"/>
                    <w:pgSz w:w="11906" w:h="16838" />
                    <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                    <w:cols w:space="708"/>
                    <w:docGrid w:line-pitch="360"/>
                    <w:hdr w:type="even">
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi.seitenkopf"/>
                            </w:pPr>
                        </w:p>
                    </w:hdr>
                    <w:hdr w:type="odd">
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi.normal-1"/>
                            </w:pPr>
                        </w:p>
                    </w:hdr>
                    <w:ftr w:type="even">
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi.seitenfuss"/>
                            </w:pPr>
                            <w:fldSimple w:instr=" PAGE \* MERGEFORMAT ">
                                <w:r wsp:rsidR="000C002F">
                                    <w:rPr>
                                        <w:noProof/>
                                    </w:rPr>
                                    <w:t>2</w:t>
                                </w:r>
                            </w:fldSimple>
                        </w:p>
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi.seitenfuss"/>
                            </w:pPr>
                        </w:p>
                    </w:ftr>
                    <w:ftr w:type="odd">
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi.seitenfuss"/>
                                <w:jc w:val="right"/>
                            </w:pPr>
                            <w:fldSimple w:instr=" PAGE \* MERGEFORMAT ">
                                <w:r wsp:rsidR="00544CA7">
                                    <w:rPr>
                                        <w:noProof/>
                                    </w:rPr>
                                    <w:t>22</w:t>
                                </w:r>
                            </w:fldSimple>
                        </w:p>
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi.seitenfuss"/>
                            </w:pPr>
                        </w:p>
                    </w:ftr>
                </w:sectPr>
            </w:pPr>
        </w:p>
    </xsl:param>
    
<!-- projektangaben werden nicht mehr benötigt -->
    <xsl:template match="project"><!-- k. w. --></xsl:template>


    <!-- absätze -->
    <xsl:template match="p[ancestor::di_comment or ancestor::di_description]">
        <w:p>
            <w:pPr>
                <w:pStyle w:val="epi-beschreibung"/>
            </w:pPr><xsl:apply-templates/></w:p>
    </xsl:template>
    
    <xsl:template match="p[ancestor::introduction or ancestor::maps or parent::abbreviations]">
        <w:p>
            <w:pPr>
                <w:pStyle w:val="epi-beschreibung"/>
                <xsl:if test="@indent='1'">
                    <w:ind w:first-line="240"/>
                </xsl:if>
            </w:pPr>
            <xsl:apply-templates></xsl:apply-templates>
        </w:p>
    </xsl:template>
    
    <!-- überschriften in der einleitung -->
    <xsl:template match="h[ancestor::introduction]">
        <w:p>
            <w:pPr>
                <xsl:choose>
                    <xsl:when test="@data-link-value='H1'"><w:pStyle w:val="epi-ueberschrift-2"/></xsl:when>
                    <xsl:when test="@data-link-value='H2'"><w:pStyle w:val="epi-ueberschrift-3"/></xsl:when>
                    <xsl:when test="@data-link-value='H3'"><w:pStyle w:val="epi-ueberschrift-4"/></xsl:when>
                    <xsl:when test="@data-link-value='H4'"><w:pStyle w:val="epi-ueberschrift-5"/></xsl:when>
                    <xsl:when test="@data-link-value='H5'"><w:pStyle w:val="epi-ueberschrift-6"/></xsl:when>
                    <xsl:when test="@data-link-value='H6'"><w:pStyle w:val="epi-ueberschrift-7"/></xsl:when>
                    <xsl:when test="@data-link-value='H7'"><w:pStyle w:val="epi-ueberschrift-8"/></xsl:when>
                    <xsl:when test="@data-link-value='H8'"><w:pStyle w:val="epi-ueberschrift-9"/></xsl:when>
                </xsl:choose>
            </w:pPr>
            <xsl:apply-templates></xsl:apply-templates>
        </w:p>
    </xsl:template>
    
    <!-- titel für den abschnitt abkuezungen -->
    <xsl:template match="title[parent::abbreviations]">
        <w:p>
            <w:pPr><w:pStyle w:val="epi-ueberschrift-1"/></w:pPr>
            <!-- sprungmarke einfügen und titel auslesen -->
            <aml:annotation w:type="Word.Bookmark.Start">
                <xsl:attribute name="aml:id"><xsl:value-of select="parent::*/@id"/></xsl:attribute>
                <xsl:attribute name="w:name"><xsl:value-of select="parent::*/@id"/></xsl:attribute>
            </aml:annotation>
            <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
            <!-- sprungmarke schließen -->
            <aml:annotation w:type="Word.Bookmark.End">
                <xsl:attribute name="aml:id"><xsl:value-of select="parent::*/@id"/></xsl:attribute>
            </aml:annotation> 
        </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

    <!-- inhalte von sections und items an templates verweisen -->
    <xsl:template match="content"><xsl:apply-templates/></xsl:template>
    
    <!-- tabelle zweispaltig -->
    <xsl:template match="table">
        <xsl:if test="preceding-sibling::p"><w:p><w:pPr><w:pStyle w:val="epi-leerzeile-9pt"/></w:pPr></w:p></xsl:if>
        <w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="row">
                <w:tr>
                    <w:tc>
                        <w:p>
                            <xsl:apply-templates select="cell[1]"/>
                        </w:p>
                    </w:tc>
                    <w:tc>
                        <w:p>
                            <xsl:apply-templates select="cell[2]"/>
                        </w:p>
                    </w:tc>
                </w:tr> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </w:tbl> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!--===== allgemeine Formate ======-->
    <!--in bestimmten elementen werden alle textknoten in 'runs' getagt, 
        außer wenn das template mit dem parameter plaintext=1 aufgerufen wird-->
    <xsl:template match="text()">
        <xsl:param name="p_plaintext" select="0" />
        <xsl:choose>
            <xsl:when test="
                ($p_plaintext=0) and
                
                (ancestor::di_header1 or 
                ancestor::di_header2 or 
                ancestor::di_header3 or 
                ancestor::di_inscription_numbering or 
                ancestor::allocation or 
                ancestor::references or
                ancestor::bl or 
                ancestor::note_text or
                ancestor::di_date_on_inscription or
                ancestor::di_citation_source or

                ancestor::digt_footnotes or 
                ancestor::letter_footnotes or 
                ancestor::content[parent::translation] or 
                ancestor::heraldry or 
                ancestor::di_generals or
                ancestor::p or 
                ancestor::h or 
                ancestor::cell or
                ancestor::td or
                ancestor::sup or
                ancestor::app1 or
                ancestor::app2
                )">
                <w:r>
                    <!-- formatanweisungen hinzufügen -->
                    <w:rPr>
                        <!-- versionsbuchstaben in der nummerierung der uebersetzungen -->
                        <xsl:if test="parent::version">
                            <w:rStyle w:val="epi-version"/>
                        </xsl:if>
                        <!--Kursivierung-->
                        <xsl:if test="ancestor::quot or parent::quot">
                            <w:rStyle w:val="epi-zitat"/>
                        </xsl:if>
                        <xsl:if test="ancestor::i or parent::i">
                            <w:rStyle w:val="epi-zitat"/>
                        </xsl:if>
                        <xsl:if test="parent::allocation"> <!-- überschrift in notizen -->
                            <w:i/>
                        </xsl:if>
                        <xsl:if test="ancestor::bl[@align='Überschrift' or @align='properties/alignments/di_title']">
                            <w:i/>
                        </xsl:if>

                        <!--Initialen-->
                        <xsl:if test="parent::ini">
                            <w:rStyle w:val="epi-initialen"/>
                        </xsl:if>
                        <!--hochgestellte Buchstaben-->
                        <xsl:if test="parent::sup or ancestor::sup">
                            <xsl:choose>
                                <xsl:when test="ancestor::references or ancestor::app1 or ancestor::letter_footnotes">
                                    <w:vertAlign w:val="superscript"/> 
                                </xsl:when>
                                <xsl:otherwise>                                    
                                    <w:position w:val="8"/>
                                    <w:sz w:val="16"/> 
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:if>
                        <xsl:if test="parent::sups or ancestor::sups">
                            <w:vertAlign w:val="superscript"/>
                        </xsl:if>
                        <!--Kapitalis in der Transkription-->
                        <xsl:if test="parent::kap">
                            <w:rStyle w:val="epi-kapitaelchen"/>
                            <xsl:if test="ancestor::quot">
                                <w:i/>
                            </xsl:if>
                        </xsl:if>
                        <!--Kapitaelchen-->
                        <xsl:if test="parent::k">
                            <w:smallCaps/>
                        </xsl:if>

                        <!-- Verweise auf Inschriften nach Verweisen auf Artikelnummern -->
                        <xsl:if test="parent::inscriptlink">
                            <w:rStyle w:val="epi-inscriptlink"/>
                        </xsl:if>
                        <!--Buchstabenverbindungen-->
                        <xsl:if test="ancestor::all">
                            <w:rStyle w:val="epi-ligatur"/>
                            <xsl:if test="ancestor::quot">
                                <w:i/>
                            </xsl:if>
                        </xsl:if>
                        <!--Chronogrammbuchstaben-->
                        <xsl:if test="parent::chr">
                            <w:rStyle w:val="epi-chronogramm"/>
                            <xsl:if test="ancestor::quot">
                                <w:i/>
                            </xsl:if>
                        </xsl:if>
                        <!-- gesperrt -->
                        <xsl:if test="parent::g">
                            <w:rStyle w:val="epi-gesperrt"/>
                        </xsl:if>
                        <!-- fett -->
                        <xsl:if test="parent::b">
                            <w:b/>
                        </xsl:if>
                        <xsl:if test="ancestor::u">
                            <w:u w:val="single"/>
                        </xsl:if>
                        <!--Versalien-->
                        <xsl:if test="parent::vsl and $sw_versalien='1'">
                            <w:rStyle w:val="epi-versalien"/>
                            <xsl:if test="ancestor::quot">
                                <w:i/>
                            </xsl:if>
                        </xsl:if>
                    </w:rPr>
                    <!-- textknoten einfügen -->
                    <w:t>
                        <xsl:value-of select="." />
                        <!-- bei inmittelbar aufeinander folgenden Ligaturen 
          wird in die unterstreichung ein kleiner senkrechtet Strich U+0329 gesetzt;
          um die  horizontale ausrichtung über w:spacing zu steuern, 
          wird für das zeichen mittels inverser tags ein eigener run eingefügt 
          (d. h. der schon gesetzte run wird vor dem einzusetztenden geschlossen 
          und danach wieder geöffnet)
        -->
                        <xsl:if test="parent::all and following::node()[1][self::all]">
                            <xsl:text disable-output-escaping="yes">&lt;/w:t&gt;&lt;/w:r&gt;</xsl:text>
                            <w:r>
                                <w:rPr><w:spacing w:val="-15"/></w:rPr>
                                <w:t>&#x0329;</w:t>
                            </w:r>
                            <xsl:text disable-output-escaping="yes">&lt;w:r&gt;&lt;w:t&gt;</xsl:text>
                        </xsl:if>
                    </w:t>
                </w:r>
            </xsl:when>
            <xsl:otherwise><xsl:value-of select="." /></xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <!--Zeilenumbrueche und Absaetze in Kommentaren, Beschreibungen, Vorwort, Einleitung usw.: 
        ausser in Transkriptionen / various line breaks-->
    <!-- zeilenumbrüche, die nicht in einem element <bl> (transkriptionbereich) stehen -->
    <xsl:template match="nl[not(parent::bl)]
                           [
                            ancestor::di_description or 
                            ancestor::di_comment or 
                            ancestor::di_header1 or 
                            ancestor::di_header2 or 
                            ancestor::di_header3 or 
                            ancestor::notes or 
                            ancestor::footnotes or 
                            ancestor::translations
                            ]">
        <!-- den unmittelbar vorausgehenden geschwisterknoten ermitteln -->
        <xsl:param name="p_test1">
            <xsl:for-each select="preceding-sibling::*[name()='bl' or name()='nl'][1]">
                <xsl:value-of select="name()"/>
            </xsl:for-each>
        </xsl:param>
        <!-- den unmittelbar folgenden geschwisterknoten ermitteln -->
        <xsl:param name="p_test2">
            <xsl:for-each select="following-sibling::*[name()='bl' or name()='nl'][1]">
                <xsl:value-of select="name()"/>
            </xsl:for-each>
        </xsl:param>
        
        <xsl:choose>
            <!-- zeilenumbrüche direkt vor oder nach einen element <bl> werden ignoriert -->
            <xsl:when test="$p_test1='bl'"><xsl:comment>test nl 1</xsl:comment></xsl:when>
            <xsl:when test="$p_test2='bl'"><xsl:comment>test nl 2</xsl:comment></xsl:when>
            <!-- bei allen anderen wird der schon bestehende absatz durch inverse absatz-tags gesplittet -->
            <xsl:otherwise>
                 <xsl:text disable-output-escaping="yes">&#x003C;/w:p&#x003E;&#x003C;w:p&#x003E;</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
        <!-- formatvorlagen gemäß dem anestor-element einfügen -->
        <xsl:if test="ancestor::footnotes">
            <w:pPr><w:pStyle w:val="epi-apparat"/><w:ind w:left="0" w:first-line="0"/></w:pPr> 
        </xsl:if>
        <xsl:if test="ancestor::di_description or ancestor::di_comment">
            <w:pPr><w:pStyle w:val="epi-beschreibung"/></w:pPr> 
        </xsl:if>  
        <xsl:if test="ancestor::translations">
            <w:pPr><w:pStyle w:val="epi-uebersetzung1"/></w:pPr> 
        </xsl:if>
        <xsl:if test="ancestor::di_header1">
            <w:pPr><w:pStyle w:val="epi-gliederung-1"/></w:pPr> 
        </xsl:if> 
        <xsl:if test="ancestor::di_header2">
            <w:pPr><w:pStyle w:val="epi-gliederung-2"/></w:pPr> 
        </xsl:if> 
        <xsl:if test="ancestor::di_header3">
            <w:pPr><w:pStyle w:val="epi-gliederung-3"/></w:pPr> 
        </xsl:if> 
        <xsl:if test="ancestor::notes">
            <w:pPr><w:pStyle w:val="epi-notiz"/></w:pPr> 
        </xsl:if>
        
        <!-- parametertest 
     <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <parametertest1><xsl:copy-of select="$p_test1"/></parametertest1> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <parametertest2><xsl:copy-of select="$p_test2"/></parametertest2> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->
    </xsl:template>

    <!-- zeilenumbrüche im feld verse -->
    <xsl:template match="nl[ancestor::di_metres]">
        <!-- der schon mit dem element <verse> gesetze absatz wird durch inverse tags gesplittet -->
        <xsl:text disable-output-escaping="yes">&lt;/w:t&gt;&lt;/w:r&gt;&lt;/w:p&gt;&lt;w:p&gt;</xsl:text>
        <!-- formatvorlagen setzten -->
        <w:pPr><w:pStyle w:val="epi-verse"/></w:pPr>
        <xsl:text disable-output-escaping="yes">&lt;w:r&gt;&lt;w:t&gt;</xsl:text>
        
    </xsl:template>    
    
    <xsl:template name="linkSerie">
        <!-- dieses template kann aufgerufen werden, um fortlaufende folgen von links auf inschriften zu verdichten;
             fortlaufende zwischennummern werden durch den spiegelstrich ersetzt -->

        <!-- doubletten aus der serie entfernen -->
        <xsl:param name="p_links_doubletten_enfernen1">
            <xsl:for-each select="link[not(text()=preceding-sibling::link/text())]">
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </xsl:param>
        
        <!-- die vorhandenen links werden darauf prüft, ob und wie weit 
            sie ununterbrochen fortlaufen,  
            der erste und der letzte link einer solchen ununterbrochenen serie werden kopiert, 
            die dazwischenstehenden durch <link>–</link> ersetzt    
        -->
        <xsl:param name="p_links_verdichten">
            <xsl:for-each select="$p_links_doubletten_enfernen1/link">
                <xsl:variable name="v_preceding_nr"><xsl:value-of select="preceding-sibling::link[1]/@number"/></xsl:variable>
                <xsl:variable name="v_following_nr"><xsl:value-of select="following-sibling::link[1]/@number"/></xsl:variable>
                <xsl:choose>
                    <xsl:when test="
                        $v_preceding_nr != '' and
                        $v_following_nr != '' and
                        @number=$v_preceding_nr +1 and 
                        @number=$v_following_nr -1 and 
                        not(@is_part) and
                        not(preceding-sibling::link[1][@is_part]) and
                        not(following-sibling::link[1][@is_part])
                        "><link>–</link></xsl:when>
                    <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:param>
        
        <!-- folge von spiegelstrichen auf einen reduzieren -->
        <xsl:param name="p_links_doubletten_entfernen2">
            <xsl:for-each select="$p_links_verdichten/link[not(text()=preceding-sibling::link[1]/text())]">
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </xsl:param>       
        
        <!--parametertest
             <xsl:comment></xsl:comment>          
             <p_links_doubletten_enfernen1><xsl:copy-of select="$p_links_doubletten_enfernen1"/></p_links_doubletten_enfernen1> 
             <p_links_verdichten><xsl:copy-of select="$p_links_verdichten"></xsl:copy-of></p_links_verdichten>
             <p_links_doubletten_entfernen2><xsl:copy-of select="$p_links_doubletten_entfernen2"/></p_links_doubletten_entfernen2>
   -->
        
        
        <!-- kommata setzen -->
        <xsl:variable name="count" select="count($p_links_doubletten_entfernen2/link)" />
        <xsl:for-each select="$p_links_doubletten_entfernen2/link">
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
            <xsl:if test="following-sibling::link[1][not(text()='–')] and not(text()='–')">
                <w:r><w:t>
                <xsl:choose>
                    <xsl:when test="$count = 2"><xsl:text> und </xsl:text></xsl:when>
                    <xsl:otherwise><xsl:text>, </xsl:text></xsl:otherwise>
                </xsl:choose>
                </w:t></w:r>
            </xsl:if>
        </xsl:for-each>
    </xsl:template>
    
</xsl:stylesheet>