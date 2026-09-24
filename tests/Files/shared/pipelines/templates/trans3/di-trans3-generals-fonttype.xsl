<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    
    <xsl:import href="../commons/di-switch.xsl"/>

    <!-- 
        in diesem stylesheet werden angaben zu den im artikel 
        vorkommenden schriftarten zusammengetragen und konfiguriert 
    -->
    
    <xsl:template name="generals_fonttype">
        <!-- zuerst wird zur späteren verwendung eine reihe von angaben in parametern erfasst -->
        
        <!-- ID des artikels -->
        <xsl:param name="p_article-id" select="@id" />
    
        <!-- anzahl der inschriften des artikels-->
        <xsl:param name="p_anzahl_inschriften_total" select="count(sections/section[@sectiontype='inscription'])" />
        
        <!-- anzahl der inschriftenteile des artikels-->
        <xsl:param name="p_anzahl_teile_total" select="count(.//section[@sectiontype='inscriptionpart'])" />
        
        <!-- anzahl aller bearbeitungen des artikels-->
        <xsl:param name="p_anzahl_bearbeitungen_total" select="count(.//section[@sectiontype='inscriptiontext'])" />
     
        <!-- auf den artikel zutreffende einträge aus dem schriftartenregister extrahieren -->
        <xsl:param name="p_schriftarten1">
            
            <!-- es werden im register schriftarten diejenigen einträge der obersten ebene aufgerufen, die 
                (a) selbst mindestens einen link (verknüpfung, verweis auf) auf den aktuellen artikel oder 
                (b) von denen mindestens ein untereintrag solch einen link (verknüpfung, verweis auf) enthält
            -->
            <xsl:for-each select="ancestor::book/indices/index[@type='fonttypes' or @propertytype='fonttypes']/item[sections/section[@articles_id=$p_article-id] or .//item[sections/section[@articles_id=$p_article-id]]]">
                <!-- registereintrag nachbilden -->
                <item>
                    <!-- element <lemma> einfügen  -->
                    <xsl:copy-of select="lemma" />
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    
                    <!-- verknüpfungen mit / verweise auf inschriften (sections) im aktuellen artikel ermitteln -->
                    <links>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:for-each select="sections/section[@articles_id=$p_article-id]">
                            
                            <!-- verweis nachbilden, attribute kopieren -->
                            <link log3="link-1">
                                <xsl:copy-of select="@*"/>
                                <xsl:attribute name="type">inscription</xsl:attribute>
                                <xsl:attribute name="number" select="ancestor::book/articles[1]/article[@id=$p_article-id]/sections//section[@id=current()/@id][not(ancestor::links)]/@number" />
                                
                                <!-- die betreffende inschrift (section) im artikel ansteuern -->
                                <xsl:for-each select="ancestor::book/articles[1]/article[@id=$p_article-id]/sections//section[@id=current()/@id][not(ancestor::links)]">
    
                                    <xsl:choose>
                                        <!-- für die münchener reihe als bezeichner der inschrift die nummer als römische zahl einfügen -->
                                        <xsl:when test="$sw_modus='projects_bay'">
                                            <xsl:choose>
                                                <xsl:when test="@number[string()]">
                                                    <xsl:number value="@number" format="I"></xsl:number>
                                                </xsl:when>
                                                <xsl:otherwise>NaN</xsl:otherwise>
                                            </xsl:choose>
                                        </xsl:when>
                                        
                                        <!-- für die anderen reihen den inschrift-buchstaben (attribut @name) als bezeichner einfügen -->
                                        <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                                     
                                    </xsl:choose>
                                 </xsl:for-each>
                            </link>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            
                       </xsl:for-each>
                    </links>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    
                    <!-- untereinträge ermitteln -->
                    <xsl:call-template name="angaben-schriftarten-schleife">
                        <xsl:with-param name="p_article-id" select="$p_article-id" />
                    </xsl:call-template>
                    
                </item>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
            </xsl:for-each>
        </xsl:param>
        
        <!-- die untereinträge werden über das hier aufgerufene template konsolidiert -->
        <xsl:param name="p_schriftarten2">
            <xsl:for-each select="$p_schriftarten1/item">
                <item>
                    <xsl:copy-of select="@*"/>
                    <xsl:copy-of select="lemma"/>
                    <xsl:copy-of select="links"/>
                    <xsl:copy-of select="sections"/>
                    
                    <!-- 
                        einträge die auf "... mit Versal" und "... mit Versalien" enden,
                        werden zusammengeführt
                    -->
                    <xsl:call-template name="angaben-subschriftarten" />                
                </item>
            </xsl:for-each>
        </xsl:param>
        
        <!-- links sortieren -->
        <xsl:param name="p_schriftarten3">
            <!-- ziffern und zahlzeichen werden von der weiteren verarbeitung ausgeschlossen -->
            <!-- TODO: Nicht hier hart kodieren, konfigurierbar machen -->
            <xsl:for-each select="$p_schriftarten2/item[not(contains(lemma,'Ziffern'))][not(contains(lemma,'Zahlzeichen'))]">
                <item>
                    <xsl:copy-of select="@*"/>
                    <xsl:for-each select="lemma">
                        <lemma><xsl:copy-of select="@*"/><xsl:value-of select="."/></lemma>
                    </xsl:for-each>
                    <!-- verweise auf inschriften sortieren -->
                    <xsl:call-template name="links-sortieren"></xsl:call-template>
                    <!-- bei untereinträgen zu schriftarten links sortieren -->
                    <xsl:call-template name="angaben-subschriftarten-sortieren"></xsl:call-template>                
                </item>
            </xsl:for-each>
       </xsl:param>
        
        <!-- anzahl der registereinträge (schriftarten) einschließlich aller untereinträge ermitteln  -->
        <xsl:param name="p_anzahl-items">
            <xsl:for-each select="$p_schriftarten3"><xsl:value-of select="count(.//item[links/link])"/></xsl:for-each>
        </xsl:param>
        
        <!-- anzahl aller verweise auf inschriften ermitteln -->
        <xsl:param name="p_anzahl_links">
            <xsl:for-each select="$p_schriftarten3"><xsl:value-of select="count(.//link)"/></xsl:for-each>
        </xsl:param>
         
        <!-- Ausgabe für DIO: die einzelnen angaben werden fortlaufend zusammengeführt 
             und durch klammern und satzzeichen formatiert, die links bleiben erhalten 
        -->
        <xsl:param name="p_schriftarten4">
            <!-- zuerst werden die haupteinträge angesteuert (ziffern und zahlzeichen werden erneut ausgeschlossen) -->
            <!-- TODO: Zahlzeichen nicht hart kodieren -->
            <xsl:for-each select="$p_schriftarten3/item[not(contains(lemma,'Ziffern'))][not(contains(lemma,'Zahlzeichen'))]">
                <xsl:choose>
                    <!-- wenn der erste buchstabe des ersten lemmas ein kleinbuchstabe ist, wird er in einen großbuchstaben umgewandelt -->
                    <!-- TODO: Funktion schreiben -->
                    <xsl:when test="position()=1">
                        <xsl:value-of select="translate(substring(lemma,1,1),'abcdefghijklmnopqrstuvwxüzäöü','ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜ')"/><xsl:value-of select="substring(lemma,2)"/>
                    </xsl:when>
                    <xsl:otherwise><xsl:value-of select="lemma"/></xsl:otherwise>
                </xsl:choose>
    
                <xsl:choose>
                    <!-- bei nur einer inschrift wird der inschrift-bezeichner (A, B, C) nicht ausgegeben -->
                    <xsl:when test="$p_anzahl_inschriften_total=1">
                        <xsl:if test="item[not(substring(lemma,1,3)='und')] [not(substring(lemma,1,3)='mit')]"><xsl:text>,</xsl:text></xsl:if>
                        <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:choose>
                            <!-- wenn alle inschriften dieselbe schriftart aufweisen, werden die bezeichner nicht ausgegeben -->
                            <xsl:when test="$p_anzahl-items=1 and $p_anzahl_inschriften_total = $p_anzahl_links">
                                <xsl:if test="item[not(substring(lemma,1,3)='und')] [not(substring(lemma,1,3)='mit')]"><xsl:text>,</xsl:text></xsl:if>
                                <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:choose>
                                    <xsl:when test="links/link">
                                        <xsl:text> (</xsl:text><xsl:call-template name="links_sort2"></xsl:call-template><xsl:text>)</xsl:text>
                                        <xsl:if test="item[not(substring(lemma,1,3)='und')] [not(substring(lemma,1,3)='mit')]"><xsl:text>,</xsl:text></xsl:if>
                                        <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>                                    
                                    </xsl:when>
                                    <!-- wenn bei dem lemma oder unterlemma keine referenz auf eine inschrift vorliegt, 
                                        werden die klammern für die bezeichner ausgelassen -->
                                    <xsl:otherwise>
                                        <xsl:if test="item[not(substring(lemma,1,3)='und')] [not(substring(lemma,1,3)='mit')]"><xsl:text>,</xsl:text></xsl:if>
                                        <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>  
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
    
                <!-- ansteuern der untereinträge in flacher folge (d. h. ohne berücksichtigung der hierarchie) -->
                <xsl:for-each select=".//item">
                    <!-- lemma ausgeben -->
                    <xsl:value-of select="lemma" />
                    <xsl:choose>
                        <!-- artikel mit nur einer inschrift -->
                        <xsl:when test="$p_anzahl_inschriften_total = 1">
                            <xsl:call-template name="subschriftarten"></xsl:call-template>                       
                        </xsl:when>
                        <!-- artikel mit mehreren inschriften -->
                        <xsl:otherwise>
                            <xsl:choose>
                                <!-- wenn im artikel nur eine schriftart angegeben ist und die anzahl der inschriften 
                                    mit der anzahl der verweise auf schriftarten übereinstimmt  -->
                                <xsl:when test="$p_anzahl-items=1 and $p_anzahl_inschriften_total = $p_anzahl_links">
                                    <xsl:call-template name="subschriftarten" />  
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:choose>
                                        <xsl:when test="links/link">
                                            <xsl:text> (</xsl:text><xsl:call-template name="links_sort2"></xsl:call-template><xsl:text>)</xsl:text>
                                            <xsl:call-template name="subschriftarten"></xsl:call-template>                                           
                                        </xsl:when>
                                        <xsl:otherwise>
                                            <xsl:call-template name="subschriftarten"></xsl:call-template>  
                                        </xsl:otherwise>
                                    </xsl:choose>
    
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:otherwise>
                    </xsl:choose>
                    
                </xsl:for-each>
                <xsl:if test="following-sibling::item[not(contains(lemma,'Ziffern'))][not(contains(lemma,'Zahlzeichen'))]"><xsl:text>; </xsl:text></xsl:if>
             </xsl:for-each>           
        </xsl:param>
    
         <!--hier unterhalb können die inhalte der parameter zur kontrolle der zwichenergebnisse angezeigt werden 
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>   
            <test><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <article-id><xsl:copy-of select="$article-id"/></article-id><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <bearbeitungen><xsl:copy-of select="$anzahl_bearbeitungen_total"/></bearbeitungen><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sa1><xsl:copy-of select="$schriftarten1"/></sa1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sa2><xsl:copy-of select="$schriftarten2"/></sa2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sa3><xsl:copy-of select="$schriftarten3"/></sa3><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <sa4><xsl:copy-of select="$schriftarten4"/></sa4><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
           <anzahl-items><xsl:copy-of select="$anzahl-items"/></anzahl-items><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
           <anzahl-links><xsl:copy-of select="$anzahl_links"/></anzahl-links><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </test><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    -->
    
        <xsl:choose>
            <!-- wenn dafür ein eigener abschnitt angelegt wurde, wird dieser angesteuert -->
            <xsl:when test="sections/section[@norm_iri='di_generals_fonttype']">
                <di_generals_fonttype log3="fonttypes2">
                    <xsl:apply-templates select="sections/section[@norm_iri='di_generals_fonttype']/items/item[@itemtype='text']/content" />
                </di_generals_fonttype>
            </xsl:when>
            
            <!-- anderenfalls wird der betreffende parameter aufgerufen -->
            <xsl:otherwise>
                <di_generals_fonttype>
                    <xsl:copy-of select="$p_schriftarten4"/><xsl:if test="$p_schriftarten4/node()"><xsl:text>.</xsl:text></xsl:if>
                </di_generals_fonttype>
            </xsl:otherwise>
        </xsl:choose>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <xsl:template name="subschriftarten">
        <!-- TODO: Nicht hart kodieren, konfigurierbar machen -->
        
        <!-- wenn es ein child-item gibt, dessen lemma nicht mit 'mit' oder 'und' beginnt, wird ein komma gesetzt -->
        <xsl:if test="item[not(starts-with(lemma,'und'))] [not(starts-with(lemma,'mit'))] [not(starts-with(lemma,'der'))]"><xsl:text>,</xsl:text></xsl:if>
        <!-- wenn ein child-item existiert, wird ein leerzeichen gesetzt -->
        <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>
        <!-- wenn ein geschwister-item folgt, wird ein komma mit leerzeichen gesetzt -->
        <xsl:if test="following-sibling::item"><xsl:text>, </xsl:text></xsl:if>   
    </xsl:template>
    
    <xsl:template name="angaben-schriftarten-schleife">
        <!-- ermittlung der untereinträge im register schriftarten -->
        <!-- wird im parameter schriftarten1 aufgerufen -->
        <xsl:param name="p_article-id"></xsl:param>
        
        <!-- nur diejenigen untereinträge werden angesteuert, die referenzen auf den aktuellen artikel enthalten -->
        <xsl:for-each select="item[sections/section[@articles_id=$p_article-id] or .//item[sections/section[@articles_id=$p_article-id]]]">
            <!-- eintrag nachbilden, element <lemma> einfügen -->
            <item>
                <xsl:copy-of select="lemma" />
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <!-- verweise auf inschriften (sections) im betreffenden artikel -->
                <links>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    
                    <xsl:for-each select="sections/section[@articles_id=$p_article-id]">
                        <!-- verweis nachbilden, attribute kopieren -->
                        <link log3="link-2">
                            <xsl:copy-of select="@*"/>
                            <xsl:attribute name="type">inscription</xsl:attribute>
                            <xsl:attribute name="number" select="ancestor::book/articles[1]/article[@id=$p_article-id]//section[@id=current()/@id][not(ancestor::links)]/@number" />
    
                            <!-- inschrift (section) im betreffenden artikel ansteuern -->
                            <xsl:for-each select="ancestor::book/articles[1]/article[@id=$p_article-id]//section[@id=current()/@id][not(ancestor::links)]">
                                
                                    <xsl:choose>
                                        <!-- münchener reihe: aus der nummer eine römische zahl als bezeichner der inschrift bilden -->
                                        <xsl:when test="$sw_modus='projects_bay'">
                                            <xsl:choose>
                                                <xsl:when test="@number[string()]">
                                                    <xsl:number value="@number" format="I"></xsl:number>
                                                </xsl:when>
                                                <xsl:otherwise>NaN</xsl:otherwise>
                                            </xsl:choose>
                                        </xsl:when>
                                        <!-- anderen reihen: den inschrift-buchstaben (@name) als bezeichner angeben -->
                                        <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                                     </xsl:choose>
                                    </xsl:for-each>
                        
                        </link>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </links>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <!-- unter-untereinträge ermitteln -->
                 <xsl:call-template name="angaben-schriftarten-schleife">
                     <xsl:with-param name="p_article-id" select="$p_article-id" />
                </xsl:call-template>
            
            </item>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template name="angaben-subschriftarten">
        <!-- benachbarte untereinträge 'mit Versal' und 'mit Versalien' und dgl. werden zu 'mit Versalien' und dgl. zusammengeführt  -->        
        <!-- TODO: Generalisieren, siehe die Handhabung für das Register -->
        
        <xsl:for-each select="item">
            
            <!-- die lemmata 'mit versal' und 'mit versalien' sowie die entsprechenden varianten werden zusammengezogen -->
            <xsl:choose>
                <!-- versal/versalien -->
                <xsl:when test="lemma='mit Versal' and following-sibling::item/lemma='mit Versalien'">
                    <item>
                        <xsl:copy-of select="@*"/>
                        <lemma>mit Versalien</lemma>
                        <links>
                            <xsl:for-each select="links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                            <xsl:for-each select="following-sibling::item[lemma[text()='mit Versalien']]/links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                        </links>
                        <xsl:call-template name="angaben-subschriftarten" />
                    </item>
                </xsl:when>
                <xsl:when test="lemma='mit Versalien' and preceding-sibling::item/lemma='mit Versal'"></xsl:when>
                
                <!-- versal und minuskel/versalien ... -->
                <xsl:when test="lemma='mit Versal und Minuskel' and following-sibling::item/lemma='mit Versalien und Minuskel'">
                    <item>
                        <xsl:copy-of select="@*"/>
                        <lemma>mit Versalien und Minuskel</lemma>
                        <links>
                            <xsl:for-each select="links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                            <xsl:for-each select="following-sibling::item[lemma[text()='mit Versalien und Minuskel']]/links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                        </links>
                        <xsl:call-template name="angaben-subschriftarten"/>
                    </item>
                </xsl:when>
                <xsl:when test="lemma='mit Versalien und Minuskel' and preceding-sibling::item/lemma='mit Versal und Minuskel'"></xsl:when>
                
                <!-- frakturversal/frakturversalien -->
                <xsl:when test="lemma='mit Frakturversal' and following-sibling::item/lemma='mit Frakturversalien'">
                    <item>
                        <xsl:copy-of select="@*"/>
                        <lemma>mit Frakturversalien</lemma>
                        <links>
                            <xsl:for-each select="links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                            <xsl:for-each select="following-sibling::item[lemma[text()='mit Frakturversalien']]/links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                        </links>
                        <xsl:call-template name="angaben-subschriftarten"/>
                    </item>
                </xsl:when>
                
                <xsl:when test="lemma='mit Frakturversalien' and preceding-sibling::item/lemma='mit Frakturversal'"></xsl:when>
                
                <!-- versal in gotischer majuskel/versalien etc -->
                <xsl:when test="lemma='mit Versal in gotischer Majuskel' and following-sibling::item/lemma='mit Versalien in gotischer Majuskel'">
                    <item>
                        <xsl:copy-of select="@*"/>
                        <lemma>mit Versalien in gotischer Majuskel</lemma>
                        <links>
                            <xsl:for-each select="links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                            <xsl:for-each select="following-sibling::item[lemma[text()='mit Versalien in gotischer Majuskel']]/links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                        </links>

                        <xsl:call-template name="angaben-subschriftarten"/>
                    </item>
                </xsl:when>
                
                <xsl:when test="lemma='mit Versalien in gotischer Majuskel' and preceding-sibling::item/lemma='mit Versal in gotischer Majuskel'"></xsl:when>
                
                <!-- andere einträge -->
                <xsl:otherwise>
                    <item>
                        <xsl:copy-of select="@*"/>
                        <xsl:copy-of select="lemma"/>
                        <xsl:copy-of select="links"/>
                        <xsl:call-template name="angaben-subschriftarten"/>
                    </item>
                </xsl:otherwise>
                
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>


    
    <xsl:template name="angaben-subschriftarten-sortieren">
        <!-- zum sortieren der links wird der elemente-baum abgearbeitet
            die sortierunng ist erforderlich, weil beim zusammenführen der versal(ien)-items 
            gegebenenfalls höhere nummern nach vorn gelangt sein können
        -->
        <xsl:for-each select="item">
           <item>
             <xsl:copy-of select="@*"/>
             <xsl:copy-of select="lemma"/>
             <xsl:call-template name="links-sortieren" />
             <xsl:call-template name="angaben-subschriftarten-sortieren" />
           </item>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template name="links-sortieren">
        <!-- verweise aud inschriften sortieren -->
         <links>
            <xsl:for-each select="links/link">
                <xsl:sort select="@number" data-type="number" />
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </links>
        
        <!-- hier kann man über eine weitere schleife die links verdichten: A, B, C, D zu A-D;
             die ansprache kann dabei über das attribut inscript-number erfolgen -->
        
    </xsl:template>

    <xsl:template name="links_sort2">
        <xsl:param name="p_linsk_sort2">
            <xsl:for-each select="links/link">
                <xsl:sort select="@number"  data-type="number"/>
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </xsl:param>
        
        <links>
            <xsl:for-each select="$p_linsk_sort2/link">
                <link type="inscription">
                   <xsl:attribute name="target_id" select="@id" />
                   <xsl:copy-of select="@number" />
                   <xsl:value-of select="." />
                </link>
            </xsl:for-each>
        </links>
    </xsl:template>

</xsl:stylesheet>