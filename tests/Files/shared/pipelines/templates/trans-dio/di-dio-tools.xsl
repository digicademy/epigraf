<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:epi="http://epigraf.inschriften.net#xslt-functions"    
    exclude-result-prefixes="xs epi"
    version="2.0">

<!-- in diesem stylesheet werden verschiedene elemente zur inline-auszeichnung verarbeitet, 
        die in anderen stylsheets der dritten transformationsstufe 
        aufgerufen werden -->
    
    <!-- weblinks -->
    <xsl:template match="a">
        <a>
            <xsl:attribute name="href"><xsl:value-of select="@href"/></xsl:attribute>
            <xsl:value-of select="@value"/>
        </a>
    </xsl:template>
    
    <!--
        Verluste in den Transkriptionen: die eckigen klammern sind schon gesetzt und 
        werden theoretisch mit den strichen in der zeile zusammengehalten.
        Allerdings bricht Browser nach figure dash trotzdem um.
        Deshalb del-span eingeführt.
    -->
    <xsl:template match="del">
        <span class="dio_del"><xsl:apply-templates/></span>
    </xsl:template>
    
    <!--Nachtraege: spitze klammern setzen -->
    <xsl:template match="add">&#x27E8;<xsl:apply-templates/>&#x27E9;</xsl:template>
    
    <!-- zeilenwechsel: die slashs sind schon gesetzt. Innerhalb von Spalten im Transkriptionsfeld wird ein slash durch <br /> ersetzt. -->
    <xsl:template match="z">
        <xsl:param name="caller" />
        <xsl:choose>
            <xsl:when test="$caller = 'spalten'">
                <xsl:choose>
                    <xsl:when test="./@data-link-iri = 'properties/linebindings/di_newline'">
                        <br />
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:apply-templates/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates/>                
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <!-- Innerhalb von Spalten im Transkriptionsfeld wird ein slash durch <br /> ersetzt -->
    <xsl:template match="z" mode="from-spalten">
        <xsl:choose>
            <xsl:when test="./@data-link-iri = 'properties/linebindings/di_newline'">
                <br />
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <!-- Unterüberschriften in der Einleitung und in Katalogartikeln -->
    <xsl:template match="h">
        <xsl:choose>
            <!--Ueberschriften in Katalogartikeln-->
            <xsl:when test="ancestor::article">
                <h><em><xsl:apply-templates/></em></h>                
            </xsl:when>
            <xsl:when test="ancestor::section">
                <xsl:variable name="v_header_class" select="@data-link-value"/>
                <xsl:variable name="v_header_level_1" select="substring($v_header_class, 2)"/>
                <xsl:variable name="v_header_level_2" select="number($v_header_level_1) + 1"/>
                <h5 class="subheader subheader-h{$v_header_level_2}">
                    <xsl:apply-templates/>
                </h5>
            </xsl:when>
        </xsl:choose>

    </xsl:template>
    
    <!--Kursivierungen in den Textbausteinen, z. B. in den Registervorbemerkungen
            dieses werkzeug wurde mitunter versehentlich mit dem zitat-werkzeug <quot> verschachtelt
    -->
    <xsl:template match="i">
        <xsl:choose>
            <!-- wenn es ein <quot> enthält -->
            <xsl:when test="quot"><xsl:apply-templates/></xsl:when>
            <!-- wenn es in einem <quot> enhalten ist -->
            <xsl:when test="parent::quot"><xsl:apply-templates/></xsl:when>
            <!-- wenn es allein (ohne <quot>) steht -->
            <xsl:otherwise><em><xsl:apply-templates/></em></xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <!--Fett-->
    <xsl:template match="b">
        <b><xsl:apply-templates/></b>
    </xsl:template>
    
    <xsl:template match="chr">
        <strong><xsl:apply-templates/></strong>
    </xsl:template>
    
    <!--Unterstrichen-->
    <xsl:template match="u">
        <u><xsl:apply-templates/></u>
    </xsl:template>
    
    
    <!--zitate-->
    <xsl:template match="quot">
        <em><xsl:apply-templates/></em>
    </xsl:template>
    
    <!--Sperrung-->
    <xsl:template match="g">
        <span>
            <xsl:attribute name="class">letterspacing</xsl:attribute>
            <xsl:apply-templates/>
        </span>
    </xsl:template>
    
    <!--Kapitaelchen-->
    <xsl:template match="k">
        <smallcaps><xsl:apply-templates/></smallcaps>
    </xsl:template>
    
    <!--Worttrenner-->
    <xsl:template match="wtr">
        <xsl:value-of select="."/>
<!--        <xsl:choose>
            <xsl:when test="@data-link-value='Dreieck auf der Grundlinie' or 
                @data-link-value='Dreispitz auf der Grundlinie' or 
                @data-link-value='Punkt auf der Grundlinie' or 
                @data-link-value='Quadrangel auf der Grundlinie'">.</xsl:when>
            <xsl:when test="@data-link-value='Doppelpunkt' or 
                @data-link-value='Dreieck als Doppelpunkt' or 
                @data-link-value='Doppelpunkt › aus Dreiecken' or
                @data-link-value='Quadrangel als Doppelpunkt' or 
                @data-link-value='Quadrat › Doppelquadrat' or 
                @data-link-value='Raute › Doppelraute'">:</xsl:when>
            <xsl:when test="@data-link-value='Gleichheitszeichen'">=</xsl:when>
            <xsl:when test="@data-link-value='Komma'">,</xsl:when>
            <xsl:when test="@data-link-value='Kreuz'">+</xsl:when>
            <xsl:when test="@data-link-value='Semikolon'">;</xsl:when>
            <xsl:when test="@data-link-value='Apostroph'">'</xsl:when>
            <!-\-<xsl:when test="@type='kreuz'">+</xsl:when>-\->
            <xsl:otherwise>·</xsl:otherwise>
        </xsl:choose>
-->    </xsl:template>
    
    
    <!--Ersetzen der Signatur (=alte Nummer)) durch die aktuelle Nummer auf dem Kirchengrundriss 
        nach der entsprechenden Konkordanz
    
        ==> vermutlich obsolet
    -->
    <xsl:template match="signatur_grundriss">
        <xsl:param name="konkordanz"><xsl:value-of select="document('../Daten/konkordanz.xml')//*[name()='Worksheet']//*[name()='Row' and descendant::*[name()='Data']][*[name()='Cell'][1]/*[name()='Data']=current()/text()]/*[name()='Cell'][2]/*[name()='Data']" /></xsl:param>
        <ref>
            <xsl:attribute name="map-object"><xsl:value-of select="@link"/><xsl:number format="001" value="$konkordanz"/></xsl:attribute>
            <xsl:text>Nr.&#x20;</xsl:text>
            <xsl:value-of select="$konkordanz" />
        </ref>
    </xsl:template>
    
    <!-- kennzeichnung einer inschrift-versionsnummer bei den inschriften und hochstellung-->
    <xsl:template match="version-nr[text()]">
        <sup><span style="text-decoration:underline"><xsl:value-of select="."/></span></sup>
    </xsl:template>
    
    <!-- kennzeichnung und hochstellung einer inschrift-versionsnummer in den allgemeinen angaben 
        (generals_addition und generals_source) 
        wurde zu version-nr umgewidmet
    <xsl:template match="su"><sup><span style="text-decoration:underline"><xsl:value-of select="."/></span></sup></xsl:template>
-->
    
    <!--Zeilenumbrueche und Absaetze in Kommentaren, Beschreibungen, Vorwort, Einleitung usw.: ausser in Transkriptionen / various line breaks-->
    <xsl:template match="nl[parent::di_description or parent::di_comment or parent::notes]">
        <xsl:if test="not(preceding-sibling::node()[1][self::nl])"><!--<br/>-->
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>
    </xsl:template>
    
    <!-- Mehrere Absätze mit Leerzeichen trennen -->
    <xsl:template match="p">
        <!--xsl:if test="preceding-sibling::p[text() != '']"><xsl:text> </xsl:text><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if-->
        <xsl:if test="preceding-sibling::p"><xsl:text> </xsl:text></xsl:if>
        <xsl:choose>
            <xsl:when test="node()">
                <xsl:apply-templates/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:value-of select="."/>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>   

    <!--unsichere Lesung von Buchstaben:
        bei einer so markierten zeichenfolge wird 
        jedem zeichen ein unterpunkt (&#x0323;) hinzugefügt
    -->
    <xsl:template match="insec">
        <xsl:for-each select=".">
            <xsl:if test="string-length() &gt; 0">	       	    
                <xsl:call-template name="InsecSchleife">
                    <xsl:with-param name="Zaehler" select="1" />
                    <xsl:with-param name="Ende" select="string-length()" />	      
                </xsl:call-template>
            </xsl:if>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template name="InsecSchleife">
        <xsl:param name="Zaehler" />
        <xsl:param name="Ende" />
        <xsl:if test="number($Zaehler) &lt;= number($Ende)">
            <xsl:value-of select="substring(.,$Zaehler,1)"/><xsl:text>&#x0323;</xsl:text>
            <xsl:call-template name="InsecSchleife">
                <xsl:with-param name="Zaehler" select="$Zaehler + 1" />
                <xsl:with-param name="Ende" select="$Ende" />
            </xsl:call-template>
        </xsl:if>
    </xsl:template>
    
    <!--buchstabenverbindungen-->
    <xsl:template match="all">
        <xsl:choose>
            <!--uebergestellte buchstaben-->
            <xsl:when test="@type='lsup' or contains(@data-link-iri,'di_overpositioned')">
                <xsl:choose>
                    <!--ae-->
                    <xsl:when test="contains(.,'ae')">
                        <xsl:text>a&#x0364;</xsl:text>
                    </xsl:when>
                    <!--oe-->
                    <xsl:when test="contains(.,'oe')">
                        <xsl:text>o&#x0364;</xsl:text>
                    </xsl:when>
                    <!--ue-->
                    <xsl:when test="contains(.,'ue')">
                        <xsl:text>u&#x0364;</xsl:text>
                    </xsl:when>
                    <!--uo-->
                    <xsl:when test="contains(.,'uo')">
                        <xsl:text>u&#x0366;</xsl:text>
                    </xsl:when>
                    <!--gross V mit zwei Punkten oder Strichen-->
                    <xsl:when test="contains(.,'V')">
                        <xsl:text>V&#x0308;</xsl:text>
                    </xsl:when>
                    <!--gross N mit akzent-->
                    <xsl:when test="contains(.,'N')">
                        <xsl:text>N&#x0301;</xsl:text>
                    </xsl:when>
                    <xsl:otherwise>
                        <inline>
                            <xsl:value-of select="."/>
                        </inline>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:when>
            <!-- nexus litterarum, bogenverbindung u. dgl. 
                    werden in ein element <lig> eingefügt  -->
            <xsl:otherwise>
                <xsl:choose>
                    <xsl:when test="ancestor::inscription">
                        <lig>
                            <xsl:value-of select="."/>
                        </lig>
                    </xsl:when>
                    <xsl:otherwise>
                        <span class="ligature">
                            <xsl:value-of select="."/>
                        </span>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <xsl:template match="sup">
        <sup><xsl:apply-templates></xsl:apply-templates></sup>
    </xsl:template>
    <xsl:template match="sups">
        <sup><xsl:apply-templates></xsl:apply-templates></sup>
    </xsl:template>
    
    <!-- seitenzahlenanker -->
    <!--
        Die id ist immer römisch, nur die Anzeige durch das Format bestimmt.
        Das type-Attribut ist immer 1.
        
        ausgangscode:
        <anchor id="7d35c6318c90405ed12ba1548653df5a" format="roman" value="12" data-link-target="" data-link-value="Seitenzahlenanker" />
        zielcode:
        <anchor id="DI103-XII" type="1">XII</anchor>

        ausgangscode:
        <anchor id="7d35c6318c90405ed12ba1548653df5a" format="numeric" value="12" data-link-target="" data-link-value="Seitenzahlenanker" />
        zielcode:
        <anchor id="DI103-XII" type="1">12</anchor>

    

    -->
    <xsl:template match="anchor">
        <xsl:param name="p_di_number"><xsl:value-of select="ancestor::book/project/di_number"/></xsl:param>
        <xsl:param name="p_page_number">
            <xsl:choose>
                <xsl:when test="@format='roman'"><xsl:number value="@value" format="I"></xsl:number></xsl:when>
                <xsl:otherwise><xsl:value-of select="@value" /></xsl:otherwise>
            </xsl:choose>
        </xsl:param>

        <xsl:param name="p_page_id">DI<xsl:value-of select="$p_di_number"/>-<xsl:number value="@value" format="I" /></xsl:param>
     
        <anchor id="{$p_page_id}" type="1"><xsl:value-of select="$p_page_number"/></anchor>
    </xsl:template>
    
    <!--Fussnoten in der Einleitung-->
    <xsl:template match="app1[ancestor::introduction or ancestor::prefaces][not(ancestor::bl)]">
        <anm><xsl:apply-templates/></anm>  
    </xsl:template> 
    
    <xsl:template match="app1[ancestor::introduction or ancestor::prefaces][ancestor::bl]">
        <anm><xsl:apply-templates/></anm>  
    </xsl:template> 
    
    
    <!-- Transkriptionen in der Einleitung -->
    <xsl:template match="bl[ancestor::introduction or ancestor::prefaces]">
        <p class="transcription"><xsl:apply-templates /></p>
    </xsl:template>

    <!-- Remove space between lines to prevent p tags generated by Typo3 -->
    <xsl:template match="bl[ancestor::introduction or ancestor::prefaces]/text()"><xsl:value-of select="translate(., '&#xA;&#xD;', '')"/></xsl:template>

</xsl:stylesheet>