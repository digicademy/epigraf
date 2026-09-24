<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
>
<xsl:import href="../commons/di-switch.xsl" /> 
    
    
    <!-- in diesem stylesheet wird zusammengetragen und formatiert, 
        welche schrifthöhen in den inschriften eines artikels vorkommen
    
    es wird aufgerufen in di-trans3-articles.xsl
    
    
    -->     
    

    <xsl:template name="generals_fontsize">
        
        <!-- der name der datenbank wird eingefügt, um später bei nichtdeutschen projekten 
                sprachspezifische bezeichnungen zu ermöglichen -->
        
        <xsl:param name="p_number_of_inscriptions" />    
        <xsl:param name="p_db" />  
        
        <!-- erste transformation: ausgewählte angaben unterdrücken, 
             unterscheiden nach ziffern und buchstaben, 
             nummerierung der inschriftenverweise
        -->
        <xsl:param name="p_schrifthoehen1">

            <xsl:for-each select="sections/section[@sectiontype='inscription']/items/item[@itemtype='fontheights']">
            <xsl:choose>
              
                <!-- schrifthöhen von versalien werden unterdrückt -->
                <!-- TODO: Nicht hart kodieren, Lösung in den Daten finden -->
                <xsl:when test="contains(content,'Versal')"></xsl:when>
                <xsl:otherwise>
                    <schrifthoehe><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                       <zeichentyp>
                           
                           <!-- es wird nach buchtstaben und ziffern unterschieden -->
                           <xsl:choose>
                            <!-- ACHTUNG: nach der umstellung auf XSLT 2.0 dem pfad in contains() an <item> ein [1] angehängt, 
                                 weil es anderenfalls mitunter zu einem zielkonflikt kommen kann:
                                 PRÜFEN ob im, Ergebnis nichts unterschlagen wird!
                            -->                          
                               <!-- TODO: Nicht 'arabisch' oder 'iffer' hart kodieren, Lösung in den Daten finden -->
                               <xsl:when test="contains(ancestor::items/item[@itemtype='fonttypes'][1]/property/lemma,'arabisch') and
                                   contains(ancestor::items/item[@itemtype='fonttypes'][1]/property/lemma,'iffer')">ziffern</xsl:when>
                               <xsl:otherwise>buchstaben</xsl:otherwise>
                               
                           </xsl:choose>
                           
                       </zeichentyp><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        
                        <wert><xsl:value-of select="translate(value,'-', '‒')"/></wert><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                       
                        <ergaenzung>
                            <!-- TODO: Was macht das? -->
                            <xsl:if test="not(matches(replace(content, '\s+', ''), '^[,]+$'))">
                                <xsl:value-of select="content"/>
                            </xsl:if>
                        </ergaenzung><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        
                        <links>
                           <xsl:choose>
                               <!-- wenn im ergänzungsfeld ein verweis auf einen inschriftenteil steht, wird dieser ausgelesen -->
                               <xsl:when test="content/rec_intern">
                                   <xsl:for-each select="content/rec_intern">
                                       <xsl:apply-templates select="." /><xsl:if test="following-sibling::rec_intern">, </xsl:if>
                                   </xsl:for-each>                                   
                               </xsl:when>
                               
                               <!-- anderenfalls wird der inschrift-bezeichner (inschrift-nummer) ausgelesen-->
                               <xsl:otherwise>
                                   <link type="inscription">
                                       <xsl:attribute name="target_id" select="ancestor::section[@sectiontype='inscription']/@id" />
                                       <xsl:copy-of select="ancestor::section[@sectiontype='inscription']/@number" />
                                       <xsl:for-each select="ancestor::section[@sectiontype='inscription']">
                                           <xsl:choose>
                                               <xsl:when test="$sw_modus='projects_bay'">
                                                   <xsl:choose>
                                                       <xsl:when test="@number[string()]">
                                                           <xsl:number value="@number" format="I"></xsl:number>
                                                       </xsl:when>
                                                       <xsl:otherwise>NaN</xsl:otherwise>
                                                   </xsl:choose>
                                               </xsl:when>
                                               <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                                           </xsl:choose>
                                       </xsl:for-each>
                                   </link>
                               </xsl:otherwise>
                               
                           </xsl:choose>
                            
                       </links><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </schrifthoehe><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    
                 </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
        </xsl:param>
        
        <!-- zweite transformation: gleiche buchstabenhöhen werden zusammengezogen, dasselbe bei ziffernhöhen -->
        <xsl:param name="p_schrifthoehen2">
        
            <!-- buchstaben --> 
            <xsl:for-each select="$p_schrifthoehen1/schrifthoehe[zeichentyp[text()='buchstaben']][not(wert=preceding-sibling::schrifthoehe[zeichentyp[text()='buchstaben']]/wert)]">
                <schrifthoehe_buchstaben><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                   <wert><xsl:value-of select="wert"/></wert><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                   
                    <!-- Eine Ergänzung wird hier nur dann übernommen, wenn sich die Schrifthöhe nicht auf mehrere Inschriftteile bezieht -->
                   <ergaenzung>
                       <xsl:choose>
                           <xsl:when test="not(following-sibling::schrifthoehe[zeichentyp[text()='buchstaben']][wert=current()/wert])">
                               <xsl:value-of select="ergaenzung"/>                    
                           </xsl:when>
                       </xsl:choose>
                   </ergaenzung><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    
                   <links><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                       <xsl:for-each select="links/link">
                           <link>
                               <xsl:copy-of select="./@*"/>
                               <xsl:attribute name="ergaenz">
                                   <!-- Eine Ergänzung wird in den Link übernommen, wenn noch weitere Links folgen -->
                                   <xsl:if test="position() != last()">
                                       <xsl:value-of select="ergaenzung"/>
                                   </xsl:if>
                               </xsl:attribute>
                               <xsl:copy-of select="./text()"/>
                           </link>
                       </xsl:for-each>
                       
                       <xsl:for-each select="following-sibling::schrifthoehe[zeichentyp[text()='buchstaben']][wert=current()/wert]">
                           <xsl:for-each select="links/link">
                               <link>
                                   <xsl:copy-of select="@*"/>
                                   <xsl:attribute name="ergaenz" select="ergaenzung" />
                                   <xsl:value-of select="." />
                               </link>
                               <xsl:if test="./following-sibling::node()">
                                   <xsl:text>, </xsl:text>                                    
                               </xsl:if>
                           </xsl:for-each>
                           <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                       </xsl:for-each>
                       
                   </links><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
               </schrifthoehe_buchstaben><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>               
           </xsl:for-each>
        
            <!-- ziffern -->
            <xsl:for-each select="$p_schrifthoehen1/schrifthoehe[zeichentyp[text()='ziffern']][not(wert=preceding-sibling::schrifthoehe[zeichentyp[text()='ziffern']]/wert)]">
                <schrifthoehe_ziffern><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <wert><xsl:value-of select="wert"/></wert><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:copy-of select="ergaenzung"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    
                    <links><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:copy-of select="links/link" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                        
                        <xsl:for-each select="following-sibling::schrifthoehe[zeichentyp[text()='ziffern']][wert=current()/wert]">
                            <xsl:copy-of select="links/link"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:for-each>
                    </links><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    
                </schrifthoehe_ziffern><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>               
            </xsl:for-each>     
            
        </xsl:param> 

        <xsl:param name="p_anzahl_hoehen">
            <xsl:for-each select="$p_schrifthoehen2"><xsl:value-of select="count(*)"/></xsl:for-each>
        </xsl:param>

        <xsl:param name="p_anzahl_links">
            <xsl:for-each select="$p_schrifthoehen2"><xsl:value-of select="count(.//link)"/></xsl:for-each>
        </xsl:param>

        <!-- parameter ausgeben 
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <schrifthoehen1><xsl:copy-of select="$p_schrifthoehen1"/></schrifthoehen1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <schrifthoehen2><xsl:copy-of select="$p_schrifthoehen2"/></schrifthoehen2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <hoehen><xsl:copy-of select="$p_anzahl_hoehen"/></hoehen><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <links><xsl:copy-of select="$p_anzahl_links"/></links><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <inschriften><xsl:copy-of select="$p_number_of_inscriptions"/></inschriften>
        </parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        -->

        <!-- formatierung der schrifthöhenzeile für den Druck -->        
        <xsl:choose>
            
            <!-- wenn dafür ein eigener abschnitt angelegt wurde, wird dieser angesteuert -->
            <xsl:when test="sections/section[@norm_iri='di_generals_fontsize']">
                <di_generals_fontsize><xsl:apply-templates select="sections/section[@norm_iri='di_generals_fontsize']/items/item[@itemtype='text']/content"/></di_generals_fontsize>
            </xsl:when>
            
            <!-- anderenfalls wird ein template aufgerufen -->
            <xsl:otherwise>
                <di_generals_fontsize>
                    <!-- <xsl:attribute name="anzahl_inschriften"><xsl:value-of select="$p_anzahl_inschriften"/></xsl:attribute>-->
                    <!-- buchstaben -->
                    <xsl:if test="$p_schrifthoehen2/schrifthoehe_buchstaben">
                        
                        <xsl:choose>
                            <xsl:when test="$p_db='inscriptiones_estoniae'">
                                <xsl:text>Tä </xsl:text> 
                            </xsl:when>
                            <xsl:otherwise><xsl:text>Bu. </xsl:text></xsl:otherwise>
                        </xsl:choose>
                        
                        <xsl:for-each select="$p_schrifthoehen2/schrifthoehe_buchstaben">
                            <xsl:value-of select="wert"/><xsl:text> cm</xsl:text>
                            
                            <!-- Ist eine Ergänzung vorhanden, wird sie nach einem Komma ausgegeben -->
                            <xsl:choose>
                                <xsl:when test="ergaenzung!=''">
                                    <xsl:text>, </xsl:text><xsl:value-of select="ergaenzung"/>
                                </xsl:when>
                            </xsl:choose>
                            
                            <!-- bei mehr als einer inschrift werden die nummern in klammern ausgegeben -->
                            <!-- TODO: Aber nicht, wenn alle Links die gleichen sind -->
                            
                            <xsl:if test="($p_anzahl_hoehen != 1) or ($p_number_of_inscriptions != $p_anzahl_links)">
                                <xsl:text> (</xsl:text>
                                <links log3="lfs1">
                                    <xsl:for-each select="links/link">
                                        <xsl:choose>
                                            <xsl:when test="@ergaenz!=''">
                                                <!-- Ist eine Ergänzung vorhanden, wird sie in runden Klammern nach dem Inschriftenbezeichner ausgegeben -->
                                                <link log3="lfs2">
                                                    <xsl:copy-of select="@*"/>
                                                    <xsl:attribute name="type">inscription</xsl:attribute>
                                                    <xsl:value-of select="."/>
                                                    <xsl:text> (</xsl:text>
                                                    <xsl:value-of select="@ergaenz"/>
                                                    <xsl:text>)</xsl:text>
                                                </link>
                                            </xsl:when>
                                            <xsl:otherwise>
                                                <link log3="lfs3">
                                                    <xsl:copy-of select="@*"/>
                                                    <xsl:attribute name="type">inscription</xsl:attribute>
                                                    <xsl:value-of select="."/>
                                                </link>
                                            </xsl:otherwise>
                                        </xsl:choose>
                                    </xsl:for-each>
                                </links>
                                <xsl:text>)</xsl:text>
                            </xsl:if>
                            
                            <xsl:if test="following-sibling::schrifthoehe_buchstaben"><xsl:text>, </xsl:text></xsl:if>
                        </xsl:for-each>
                        
                    </xsl:if>
                    
                    <!-- ziffern -->
                    <xsl:if test="$p_schrifthoehen2/schrifthoehe_ziffern">
                        
                        <!-- wenn buchstabenhöhen vorangehen, wird ein semikolon gesetzt-->
                        <xsl:if test="$p_schrifthoehen2/schrifthoehe_buchstaben"><xsl:text>; </xsl:text></xsl:if>    
                        <xsl:choose>
                            <xsl:when test="$p_db='inscriptiones_estoniae'">
                                <xsl:text>Tä </xsl:text> 
                            </xsl:when>
                            <xsl:otherwise><xsl:text>Zi. </xsl:text></xsl:otherwise>
                        </xsl:choose>
                        
                        <xsl:for-each select="$p_schrifthoehen2/schrifthoehe_ziffern">
                            <xsl:value-of select="wert"/><xsl:text> cm</xsl:text>
                            <!-- Ist eine Ergänzung vorhanden, wird sie nach einem Komma ausgegeben -->
                            <xsl:choose>
                                <xsl:when test="ergaenzung!=''">
                                    <xsl:text>, </xsl:text><xsl:value-of select="ergaenzung"/>
                                </xsl:when>
                            </xsl:choose>
                            
                            <!-- bei mehr als einer inschrift werden die nummern in klammern ausgegeben -->
                            <xsl:if test="$p_anzahl_hoehen != 1 or $p_number_of_inscriptions != $p_anzahl_links">
                                <!--<xsl:text> (</xsl:text><xsl:copy-of select=".//links"/><xsl:text>)</xsl:text>-->
                                <xsl:if test="$p_anzahl_hoehen != 1 or $p_number_of_inscriptions != $p_anzahl_links">
                                    <xsl:text> (</xsl:text>
                                    <links log3="lfs4">
                                        <xsl:for-each select="links/link">
                                            <xsl:choose>
                                                <xsl:when test="@ergaenz!=''">
                                                    <!-- Ist eine Ergänzung vorhanden, wird sie in runden Klammern nach dem Inschriftenbezeichner ausgegeben -->
                                                    <link log3="lfs2">
                                                        <xsl:copy-of select="@*"/>
                                                        <xsl:attribute name="type">inscription</xsl:attribute>
                                                        <xsl:value-of select="."/>
                                                        <xsl:text> (</xsl:text>
                                                        <xsl:value-of select="@ergaenz"/>
                                                        <xsl:text>)</xsl:text>
                                                    </link>
                                                </xsl:when>
                                                <xsl:otherwise>
                                                    <link log3="lfs5">
                                                        <xsl:copy-of select="@*"/>
                                                        <xsl:attribute name="type">inscription</xsl:attribute>
                                                        <xsl:value-of select="."/>
                                                    </link>
                                                </xsl:otherwise>
                                            </xsl:choose>
                                        </xsl:for-each>
                                    </links>
                                    <xsl:text>)</xsl:text>
                                </xsl:if>
                            </xsl:if>
                            
                            <xsl:if test="following-sibling::schrifthoehe_ziffern"><xsl:text>, </xsl:text></xsl:if>
                            
                        </xsl:for-each>
                    </xsl:if>
                    
                    <!-- entfällt künftig: am schluss wird ein punkt gesetzt, wenn schrifthöhen vorliegen
                    <xsl:if test="$p_schrifthoehen2/*"><xsl:text>.</xsl:text></xsl:if> -->
                    
                </di_generals_fontsize>               
            </xsl:otherwise>
        </xsl:choose>        
    </xsl:template>

</xsl:stylesheet>