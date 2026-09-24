<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:epi="http://epigraf.inschriften.net#xslt-functions"    
    exclude-result-prefixes="xs epi"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/>
    
    <!-- Hier werden die spitzen Klammern, die nicht als Escape-Zeichenfolgen ausgegeben werden sollen, deklariert. -->
    <xsl:output method="xml" use-character-maps="myMap"/>
    <xsl:character-map name="myMap">
        <xsl:output-character character="≤" string="&lt;"/>
        <xsl:output-character character=">" string="&gt;"/>
    </xsl:character-map>
    
<!-- in diesem stylesheet werden alle arten von verweisen verarbeitet, 
        die in anderen stylsheets der dritten transformationsstufe 
        aufgerufen werden -->

    <!-- folgen von links in den elemente von <di_generals> -->
    <xsl:template match="links">
        <xsl:call-template name="linkSerie" />
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
        <xsl:variable name="article" select="ancestor::article" />
        <xsl:variable name="count" select="count($p_links_doubletten_entfernen2/link)" />
        <xsl:for-each select="$p_links_doubletten_entfernen2/link">
            <xsl:choose>
                <xsl:when test="@target_id or @data-link-target"><xsl:call-template name="link"><xsl:with-param name="article" select="$article" /></xsl:call-template></xsl:when>
                <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
            </xsl:choose>
            <xsl:if test="following-sibling::link[1][not(text()='–')] and not(text()='–')">
                <xsl:choose>
                    <xsl:when test="$count = 2"><xsl:text> und </xsl:text></xsl:when>
                    <xsl:otherwise><xsl:text>, </xsl:text></xsl:otherwise>
                </xsl:choose>                
            </xsl:if>
        </xsl:for-each>
    </xsl:template>
    
    <!-- literaturverweise -->    
    <xsl:template match="link[@type='literatur']">
        <xsl:param name="id" select="if(string-length(@data-link-target) &gt; 0) then @data-link-target else @target_id" />
        <xsl:param name="p_target_url">/<xsl:value-of select="$p_volume_path"/>/literatur.html#<xsl:value-of select="$id"/></xsl:param>        
        <xsl:choose>
            <xsl:when test="@ishidden = '1'"><xsl:value-of select="."/></xsl:when>
            <xsl:when test="($sw_links_lit=1) and not(starts-with(.,'DI '))">
                <a href="{$p_target_url}"><xsl:value-of select="."/></a>
            </xsl:when>
            <xsl:when test="($sw_links_lit=1) and starts-with(.,'DI ')">
                <a class="link_di" data-volume="{epi:extract-volume-number(.)}" href="{$p_target_url}"><xsl:value-of select="."/></a>
            </xsl:when>            
            <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    
    <!--Verweise auf Inschriften-->
    <xsl:template match="link[@type='inscription' or @type='inscriptionpart']">
        <xsl:call-template name="link" />
    </xsl:template>
    
    <xsl:template name="link">
        <xsl:param name="article" select="ancestor::article" />
        
        <!-- auslesen der @id der ersten bearbeitung einer inschrift oder eines inschriftenteils, 
            um sie im folgenden als letztes glied der verweisadresse zu verwenden -->
        <xsl:param name="bearbeitungID">
            <!-- es wird unterschieden zwischen verweis im selben artikel oder in einen anderen artikel -->
            <xsl:choose>
                <!-- in anderem artikel -->
                <xsl:when test="@target_article">
                    <xsl:for-each select="ancestor::articles/article[@nr=current()/@target_article]//*[@id=current()/@target_id or @id=current()/@data-link-target]">
                        <xsl:choose>
                            <xsl:when test="inscriptionpart"><xsl:value-of select="inscriptionpart[1]/version[1]/nr/@id"/></xsl:when>
                            <xsl:otherwise><xsl:value-of select="version[1]/nr/@id"/></xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>
                </xsl:when>
                <!-- im selben artikel -->
                <xsl:otherwise>
                    <xsl:for-each select="$article//*[@id=current()/@target_id  or @id=current()/@data-link-target]">
                        <!-- wenn der link auf eine inschrift zielt, wird auf deren ersten inschriftenteil 
                            und dann weiter auf die erste bearbeitung umgeleitet;
                            wenn er bereits auf eine bearbeitung gerichtet ist, wird deren @id direkt aufgenommen -->
                        <xsl:choose>
                            <xsl:when test="inscriptionpart"><xsl:value-of select="inscriptionpart[1]/version[1]/nr/@id"/></xsl:when>
                            <xsl:when test="version"><xsl:value-of select="version[1]/nr/@id"/></xsl:when>
                            <xsl:otherwise><xsl:value-of select="@id"/></xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:param>
        
        <!-- beim bilden der linkadresse wird unterschieden zwischen artikelintern und artikelextern; 
                dem entsprechend werden die adressen aus den zuvor angelegten parametern erstellt -->
        <!-- noch nicht berücksichtigt ist, dass bei verweis auf eine inschrift in einem artikel 
            der nur eine einzige inschrift enthält, der inschriftbezeichner ausgeblendet wird; 
            dieser umstand müsste bereits bei der vorformatierung in epigraf-server.trans2.xsl 
            analysiert und gekennzeichnet werden -->
        <xsl:choose>
            <xsl:when test="$sw_links_inscriptions=1">
                
                <xsl:choose>
                    <!-- für externe links auf inschriften in anderen artikeln 
                    wird die adresse innerhalb des zielartikels gebildet 
                    aus bandnummer, artikelnummer und @id der ersten bearbeitung -->                    
                    <xsl:when test="@target_article">
                        <a>
                            <xsl:attribute name="href">/<xsl:value-of select="$p_volume_identifier" />/<xsl:value-of select="epi:pad-with-suffix(@target_article,4)" />#<xsl:value-of select="$bearbeitungID"/></xsl:attribute>
                            <xsl:apply-templates></xsl:apply-templates>
                        </a>
                    </xsl:when>
                    
                    <!-- für interne links auf inschriften oder inschriftenteile 
                    wird die adresse gebildet aus @id der ersten bearbeitung -->
                    <xsl:otherwise>
                        <a>
                            <xsl:attribute name="href">#<xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="epi:pad-with-suffix($article/@nr,4)" />-<xsl:value-of select="$bearbeitungID" /></xsl:attribute>
                            <xsl:apply-templates />
                        </a>                    
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates/>                
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    
    <!-- erste variante mit subsections -->
    <!--<xsl:template match="link[@type='inschrift']">
<xsl:param name="record-link">record:di<xsl:value-of select="$p_volume_number"/>-<xsl:number value="@target_article" format="0001"></xsl:number></xsl:param>
<xsl:param name="inschrift-link-extern"><xsl:value-of select="$p_volume_identifier"/>-<xsl:number value="@target_article" format="0001"/>-<xsl:value-of select="@target_id"/></xsl:param>
<xsl:param name="inschrift-link-intern"><xsl:value-of select="$p_volume_identifier"/>-<xsl:number value="ancestor::article/@nr" format="0001"/>-<xsl:value-of select="@target_id"/></xsl:param>
<xsl:choose>
    <xsl:when test="@target_article">
        <ref>
            <xsl:attribute name="target"><xsl:value-of select="$record-link"/>#<xsl:value-of select="$inschrift-link-extern"/></xsl:attribute>
            <xsl:value-of select="."/>
        </ref>
    </xsl:when>
    <xsl:otherwise>
        <ref>
            <xsl:attribute name="target">#<xsl:value-of select="$inschrift-link-intern"/></xsl:attribute>
            <xsl:value-of select="."/>
        </ref>
    </xsl:otherwise>
</xsl:choose>
<!-\-    <ref>
    <xsl:copy-of select="@type"/>
    <xsl:attribute name="target"><xsl:value-of select="@target_id"/></xsl:attribute>
    <xsl:value-of select="."/>
  </ref>-\->
</xsl:template>-->
    
    <!--Verweise auf Marken-->
    <xsl:template match="link[@type='marke']">
        
        <!-- die ziel-id der marke in der markenliste speichern -->
         <xsl:param name="p_target" select="@data-link-target" /> 
          
        <!-- mittels der ziel-id die marke in der markenliste ansteuern -->
        <xsl:param name="bezeichnung">
            <xsl:for-each select="ancestor::book//brands//brand[@id=$p_target]">
                    <!-- das kürzel für die bezeichnung des markentyps in einer variablen speichern-->
                    <xsl:variable name="signum">
                        <xsl:for-each select="ancestor::brand-type">
                                <xsl:choose>
                                    <!-- wenn das kürzel beim markentyp angegeben ist, dieses auslesen -->
                                    <xsl:when test="@sign"><xsl:value-of select="@sign"/></xsl:when>
                                    <!-- anderenfalls den anfangsbuchstaben des markentyps auswählen -->
                                    <xsl:otherwise><xsl:value-of select="substring(title,1,1)"/></xsl:otherwise>
                                </xsl:choose>                     
                            </xsl:for-each>
                    </xsl:variable>
                    <!-- die nummer der marke (bezogen auf den markentyp) speichern -->
                    <xsl:variable name="nr"><xsl:value-of select="nr"/></xsl:variable>
                <!-- den verweis auf die marke erzeugen -->
                <xsl:choose>
                    <!-- wenn die marken zu einem einzigen typ zusammengefasst werden sollen -->
                    <xsl:when test="$sw_markentypen_zusammenfassen=1">
                        <xsl:text>M</xsl:text><xsl:value-of select="$nr"/>
                    </xsl:when>
                    <!-- anderenfalls: wenn die marken nach typen getrennt angegeben werden sollen -->
                    <xsl:otherwise><xsl:value-of select="$signum"/><xsl:value-of select="$nr"/></xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:param> 
        
        
        <!-- muster: <ref target="mark:di077-m013">H13</ref> -->
        <xsl:choose>
            <xsl:when test="$sw_links_brands=1">
                <a>
                    <xsl:attribute name="href">/<xsl:value-of select="$p_volume_path"/>/materialien.html#<xsl:value-of select="$p_target"/></xsl:attribute>
                    <xsl:value-of select="$bezeichnung"/>
                </a>
            </xsl:when>
            <xsl:otherwise><xsl:value-of select="$bezeichnung"/></xsl:otherwise>
        </xsl:choose>
                
    </xsl:template>
    
    
    <xsl:template match="link[@type='map']">
        <!-- Altes Muster: <ref target="object:map077-1_di077-0031">Nr. 31</ref> -->       
        <xsl:param name="p_target_url">/<xsl:value-of select="$p_volume_path"/>/materialien.html#a<xsl:value-of select="."/></xsl:param>        
        
        <xsl:choose>
            <!-- TODO: a-Nummern berücksichtigen -->
            <xsl:when test="$sw_links_maps=1">
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="$p_target_url"/></xsl:attribute>
                    <xsl:value-of select="."/>
                </a>
            </xsl:when>
            <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
        </xsl:choose>
    </xsl:template>





    <!--Verweise auf andere Datensaetze/Artikel-->
    <!-- Muster: <ref target="record:di077-0010">Kat.-Nr. 10</ref> 
                    bei a-nummern:
                 <ref target="record:di077-0010a">Kat.-Nr. 10a</ref>
    -->
    
    <xsl:template match="link[@type='articles']">
                       
        <!-- id des zielartikels speichern -->
        <xsl:param name="p_data-link-target"><xsl:value-of select="@data-link-target"/></xsl:param>
        
        <!-- bezeichner (signatur) des ziel-artikels speichern -->
        <xsl:param name="p_headline_signature">
            <xsl:for-each select="ancestor::book/articles/article[@id=$p_data-link-target]">
                <xsl:value-of select="headline/signature"></xsl:value-of>
            </xsl:for-each>
        </xsl:param>
        <!-- laufnummer des ziel-artikels speichern-->
        <xsl:param name="p_headline_articlenumber">
            <xsl:for-each select="ancestor::book/articles/article[@id=$p_data-link-target]">
                <xsl:value-of select="headline/articlenumber"></xsl:value-of>
            </xsl:for-each>
        </xsl:param>
        
        <!-- optionsweise signatur oder laufnummer des zieleartikels zur weiteren verwendung auswählen -->
        <xsl:param name="p_choose_target_signature">
            <!-- wenn nach signatur sortiert und nummeriert werden soll, 
                werden die signaturen, die gegebenenfalls a-nummern enthalten können,
                ausgelsesen,
                anderenfalls die aus dem bestand ermittelten laufnummern
            -->
            <xsl:choose>
                <!-- signatur = artikelnummer im gedruckten band -->
                <xsl:when test="$sw_sortieren_nach_signatur=1">
                    <xsl:value-of select="$p_headline_signature"/>
                </xsl:when>
                <!-- neu ermittelte laufnummer -->
                <xsl:otherwise>
                    <xsl:value-of select="$p_headline_articlenumber"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:param>
        
        <!-- die beiden folgenden parameter setzen bei a-nummer, die ziffernfolge vor dem buchstaben 
                auf vier stellen, gegebenfalls mit führenden nullen   -->
        <xsl:param name="p_split_target_signature">
            <!-- die signatur in ziffernfolge und buchstaben zerlegen -->
            <xsl:for-each select="$p_choose_target_signature">
                <xsl:analyze-string select="." regex="([0-9]+)([a-zA-Z])">
                    <xsl:matching-substring>
                        <digits><xsl:value-of select="regex-group(1)"/></digits>
                        <letter><xsl:value-of select="regex-group(2)"/></letter>
                    </xsl:matching-substring>
                    <xsl:non-matching-substring><digits><xsl:value-of select="."/></digits></xsl:non-matching-substring>
                </xsl:analyze-string>
            </xsl:for-each>
        </xsl:param>
        <xsl:param name="p_target_signature1">
            <!-- die ziffernfolge auf vier stellen hochrechnen und den buchstaben anfügen -->
            <!-- TODO: Funktion epi:pad-with-suffix() verwenden? -->
            <xsl:for-each select="$p_split_target_signature">
                <xsl:number value="digits" format="0001"></xsl:number><xsl:value-of select="lower-case(letter)"/>
            </xsl:for-each>
        </xsl:param>
        <xsl:param name="p_target_signature2">
            <!-- die ziffernfolge ohne führende nullen ausgeben und den buchstaben anfügen -->
            <xsl:for-each select="$p_split_target_signature">
                <xsl:number value="digits" format="1"></xsl:number><xsl:value-of select="lower-case(letter)"/>
            </xsl:for-each>
        </xsl:param>
        
        <!-- verweise konstruieren -->
        <xsl:choose>
            <!-- wenn links gebildet werden sollen -->
            <xsl:when test="$sw_links_articles=1">
                <xsl:choose>
                    <xsl:when test="ancestor::article">
                        <a>
                            <xsl:attribute name="href">/<xsl:value-of select="$p_volume_identifier"/>/<xsl:value-of select="epi:pad-with-suffix($p_choose_target_signature, 4)"/></xsl:attribute>
                            <xsl:value-of select="$p_target_signature2"/>
                        </a>
                    </xsl:when>
                    <xsl:otherwise>                
                        <ref>
                            <xsl:attribute name="target">record:<xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="$p_target_signature1"/></xsl:attribute>
                            <xsl:choose>
                                <!-- wenn der zielartikel in der exportauswahl enthalten ist -->
                                <xsl:when test="$p_choose_target_signature[string()]">
                                    <xsl:value-of select="$p_target_signature2"/>
                                </xsl:when>
                                <!-- wenn der zielartikel nicht in der exportauswahl enthalten ist -->
                                <xsl:otherwise><xsl:text>n. v.</xsl:text></xsl:otherwise>
                            </xsl:choose>
                        </ref>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <!-- wenn keine links gebildet werden sollen -->
            <xsl:otherwise>
                <xsl:value-of select="$p_choose_target_signature"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <!-- Verweise auf Inschriftenabschnitte in anderen Artikeln -->
    <!-- Muster: <ref target="record:di077-0010">Kat.-Nr. 10A</ref> 
                    bei a-nummern:
                 <ref target="record:di077-0010a">Kat.-Nr. 10aB</ref>
    -->
    
    <xsl:template match="link[@type='sections']">
        
        <!-- id des Abschnitts speichern -->
        <xsl:param name="p_data-link-section-target"><xsl:value-of select="@data-link-target"/></xsl:param>
        
        <!-- Bezeichner des Abschnitts speichern -->
        <xsl:param name="p_target_signature3"><xsl:value-of select="inscriptlink"/></xsl:param>
        
        <!-- id des zielartikels speichern -->
        <xsl:param name="p_data-link-target"><xsl:value-of select="ancestor::book/articles/article[.//(inscription|inscriptionpart)[@id=$p_data-link-section-target]]/@id"/></xsl:param>
        
        <!-- bezeichner (signatur) des ziel-artikels speichern -->
        <xsl:param name="p_headline_signature">
            <xsl:for-each select="ancestor::book/articles/article[@id=$p_data-link-target]">
                <xsl:value-of select="headline/signature"></xsl:value-of>
            </xsl:for-each>
        </xsl:param>
        <!-- laufnummer des ziel-artikels speichern-->
        <xsl:param name="p_headline_articlenumber">
            <xsl:for-each select="ancestor::book/articles/article[@id=$p_data-link-target]">
                <xsl:value-of select="headline/articlenumber"></xsl:value-of>
            </xsl:for-each>
        </xsl:param>
        
        <!-- optionsweise signatur oder laufnummer des zieleartikels zur weiteren verwendung auswählen -->
        <xsl:param name="p_choose_target_signature">
            <!-- wenn nach signatur sortiert und nummeriert werden soll, 
                werden die signaturen, die gegebenenfalls a-nummern enthalten können,
                ausgelsesen,
                anderenfalls die aus dem bestand ermittelten laufnummern
            -->
            <xsl:choose>
                <!-- signatur = artikelnummer im gedruckten band -->
                <xsl:when test="$sw_sortieren_nach_signatur=1">
                    <xsl:value-of select="$p_headline_signature"/>
                </xsl:when>
                <!-- neu ermittelte laufnummer -->
                <xsl:otherwise>
                    <xsl:value-of select="$p_headline_articlenumber"/>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:param>
        
        <!-- die beiden folgenden parameter setzen bei a-nummer, die ziffernfolge vor dem buchstaben 
                auf vier stellen, gegebenfalls mit führenden nullen   -->
        <xsl:param name="p_split_target_signature">
            <!-- die signatur in ziffernfolge und buchstaben zerlegen -->
            <xsl:for-each select="$p_choose_target_signature">
                <xsl:analyze-string select="." regex="([0-9]+)([a-zA-Z])">
                    <xsl:matching-substring>
                        <digits><xsl:value-of select="regex-group(1)"/></digits>
                        <letter><xsl:value-of select="regex-group(2)"/></letter>
                    </xsl:matching-substring>
                    <xsl:non-matching-substring><digits><xsl:value-of select="."/></digits></xsl:non-matching-substring>
                </xsl:analyze-string>
            </xsl:for-each>
        </xsl:param>
        <xsl:param name="p_target_signature1">
            <!-- die ziffernfolge auf vier stellen hochrechnen und den buchstaben anfügen -->
            <!-- todo: die Funktion epi:pad-with-suffix() verwenden? -->
            <xsl:for-each select="$p_split_target_signature">
                <xsl:number value="digits" format="0001"></xsl:number><xsl:value-of select="lower-case(letter)"/>
            </xsl:for-each>
        </xsl:param>
        <xsl:param name="p_target_signature2">
            <!-- die ziffernfolge ohne führende nullen ausgeben und den buchstaben anfügen -->
            <xsl:for-each select="$p_split_target_signature">
                <xsl:number value="digits" format="1"></xsl:number><xsl:value-of select="lower-case(letter)"/>
            </xsl:for-each>
        </xsl:param>
        <!-- ziel-link zusammensetzen -->
        <!-- TODO: Bei Verweisen auf Inschriften, muss auf den ersten Inschriftenteil umgelenkt werden  -->
        <xsl:param name="p_link-target"><xsl:value-of select="$p_volume_identifier"/>/<xsl:value-of select="epi:pad-with-suffix($p_choose_target_signature, 4)"/>#<xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="epi:pad-with-suffix($p_choose_target_signature, 4)"/>-<xsl:value-of select="$p_data-link-section-target"/></xsl:param>
        
        
        <!-- verweise konstruieren -->
        <xsl:choose>
            <!-- wenn links gebildet werden sollen -->
            <xsl:when test="$sw_links_articles=1">  
                <a href="/{$p_link-target}">
                    <!--<test><xsl:copy-of select="$p_choose_target_signature"></xsl:copy-of></test>-->
                    <xsl:choose>
                        <!-- wenn der zielartikel in der exportauswahl enthalten ist -->
                        <xsl:when test="$p_choose_target_signature[string()]">
                            <xsl:value-of select="$p_target_signature2"/><xsl:value-of select="$p_target_signature3"/>
                        </xsl:when>
                        <!-- wenn der zielartikel nicht in der exportauswahl enthalten ist -->
                        <xsl:otherwise><xsl:text>n. v.</xsl:text></xsl:otherwise>
                    </xsl:choose>
                </a>
            </xsl:when>
            <!-- wenn keine links gebildet werden sollen -->
            <xsl:otherwise>
                <xsl:value-of select="$p_choose_target_signature"/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    
    <xsl:template match="link[@type='footnotes']">
        
        <xsl:param name="targetID"><xsl:value-of select="ancestor::article/footnotes//item[@id=current()/@data-link-target]/@itemID"/></xsl:param>
        <xsl:param name="p_data-link-target"><xsl:value-of select="@data-link-target"/></xsl:param>        
        
        <xsl:choose>
            <!-- Verweise auf Fußnoten in der Einleitung -->
            <xsl:when test="$targetID = ''">
                <xsl:value-of select="index-of(//introduction//app1/@id, $p_data-link-target)"/>
            </xsl:when>
            <!-- Verweise auf Fußnoten in einem Katalogartikel -->
            <xsl:otherwise>
                <xsl:for-each select="ancestor::article/footnotes//item[@id=$p_data-link-target]">
                    <xsl:choose>
                        <xsl:when test="$sw_links_footnotes=1">
                            <!-- für interaktive links muss das folgende muster überprüft werden -->
                            <a>
                                <xsl:variable name="p_article_number"><xsl:value-of select="epi:pad-with-suffix(ancestor::article/@nr, 4)" /></xsl:variable>
                                <xsl:variable name="p_target-pref"><xsl:value-of select="$p_volume_identifier" />-<xsl:value-of select="$p_article_number"/>-</xsl:variable>
                                <xsl:attribute name="href">#<xsl:value-of select="$p_target-pref"/><xsl:value-of select="$targetID"/></xsl:attribute>      
                                <xsl:choose>
                                    <xsl:when test="parent::letter_footnotes">
                                        <xsl:number level="any" count="item" from="letter_footnotes" format="a"/>
                                    </xsl:when>
                                    <xsl:when test="parent::digit_footnotes">
                                        <xsl:number level="any" count="item" from="digit_footnotes" format="1"/>
                                    </xsl:when>
                                </xsl:choose>
                            </a>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:choose>
                                <xsl:when test="parent::letter_footnotes">
                                    <xsl:number level="any" count="item" from="letter_footnotes" format="a"/>
                                </xsl:when>
                                <xsl:when test="parent::digit_footnotes">
                                    <xsl:number level="any" count="item" from="digit_footnotes" format="1"/>
                                </xsl:when>
                            </xsl:choose>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
        
    </xsl:template>
    
    
    <!-- verweis auf eine fußnote in einem anderen artikel -->
    <xsl:template match="link[@type='footnotes_extern']">
        <xsl:param name="p_target_footnote"><xsl:value-of select="@data-link-target"/></xsl:param>
        <xsl:param name="p_target_article"><xsl:value-of select="@article-number"/></xsl:param>
        <xsl:param name="p_article_number"><xsl:value-of select="epi:pad-with-suffix(@article-number, 4)"/></xsl:param>
        <xsl:variable name="v_target-pref"><xsl:value-of select="$p_volume_identifier"/>-<xsl:value-of select="$p_article_number"/></xsl:variable>
        <xsl:variable name="v_target-article">/<xsl:value-of select="$p_volume_identifier"/>/<xsl:value-of select="$p_article_number"/></xsl:variable>
        
        <xsl:choose>
            <!-- Verweis aus der Einleitung auf eine Fußnote in einem Katalogartikel -->
            <xsl:when test="ancestor::introduction">
                <a>
                    <xsl:attribute name="href"><xsl:value-of select="$v_target-article"/></xsl:attribute>
                    <xsl:value-of select="$p_target_article"/>
                </a>
                <xsl:for-each select="ancestor::book//articles/article/footnotes//item[@id=$p_target_footnote]">
                    <xsl:choose>
                        <xsl:when test="parent::letter_footnotes">
                            <xsl:text>, Anm. </xsl:text><xsl:number level="any" count="item" from="letter_footnotes" format="a"/>
                        </xsl:when>
                        <xsl:when test="parent::digit_footnotes">
                            <xsl:text>, Anm. </xsl:text><xsl:number level="any" count="item" from="digit_footnotes" format="1"/>
                        </xsl:when>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:when>
            <!-- Verweis aus einem Katalogartikel auf eine Fußnote in einem anderen Katalogartikel -->
            <xsl:otherwise>
                <xsl:for-each select="ancestor::articles/article/footnotes//item[@id=$p_target_footnote]">                    
                    <xsl:choose>
                        <xsl:when test="$sw_links_footnotes=1">
                            <a>
                                <xsl:attribute name="href"><xsl:value-of select="$v_target-article"/>#<xsl:value-of select="$v_target-pref"/>-<xsl:value-of select="@itemID"/></xsl:attribute>      
                                <xsl:choose>
                                    <xsl:when test="parent::letter_footnotes">
                                        <xsl:value-of select="$p_target_article"/><xsl:text>, Anm. </xsl:text><xsl:number level="any" count="item" from="letter_footnotes" format="a"/>
                                    </xsl:when>
                                    <xsl:when test="parent::digit_footnotes">
                                        <xsl:value-of select="$p_target_article"/><xsl:text>, Anm. </xsl:text><xsl:number level="any" count="item" from="digit_footnotes" format="1"/>
                                    </xsl:when>
                                </xsl:choose>
                            </a>
                        </xsl:when>
                        <xsl:otherwise>
                             <xsl:choose>
                                <xsl:when test="parent::letter_footnotes">
                                    <xsl:text>, Anm. </xsl:text><xsl:number level="any" count="item" from="letter_footnotes" format="a"/>
                                </xsl:when>
                                <xsl:when test="parent::digit_footnotes">
                                    <xsl:text>, Anm. </xsl:text><xsl:number level="any" count="item" from="digit_footnotes" format="1"/>
                                </xsl:when>
                            </xsl:choose>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
        
    </xsl:template>   
    
    <xsl:template name="linkhandler">
        <xsl:param name="p_volume-nr"><xsl:value-of select="ancestor::book/project/di_number"/></xsl:param>
        <xsl:for-each select=".//knr">
            <xsl:sort select="number(.)" data-type="number" order="ascending"/>
            <ref>
                <xsl:attribute name="target">record:di<xsl:value-of select="$p_volume-nr"/>-<xsl:value-of select="epi:pad-with-suffix(.,4)"/></xsl:attribute>
                <xsl:value-of select="."/>
            </ref>            
            <xsl:if test="not(position()=last())">, </xsl:if></xsl:for-each>
        <!-- marken -->
        <xsl:for-each select=".//section">
            <xsl:sort select="number(.)" data-type="number" order="ascending"/>
            <ref>
                <xsl:attribute name="target">record:di<xsl:value-of select="$p_volume-nr"/>-<xsl:value-of select="epi:pad-with-suffix(.,4)"/></xsl:attribute>
                <xsl:value-of select="."/>
            </ref>
            <xsl:if test="not(position()=last())">, </xsl:if></xsl:for-each>        
    </xsl:template>
    
</xsl:stylesheet>