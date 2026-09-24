<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">

    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans2-commons.xsl"/>
    
    <!-- mit diesem stylesheet werden die 
            querverweise, 
            die elemente <lemma> und 
            die elemente <name> in den register behandelt
        
        es wird aufgerufen in:
         di-trans2-index-brands.xsl
         di-trans2-index-heraldry.xsl
         di-trans2-index-locations.xsl
         di-trans2-indices.xsl
        
    -->
    
    
    <!-- querverweise in den registern sammeln-->
    <xsl:template name="crossRefs">
        <xsl:param name="p_basisstandort" />
        <xsl:param name="index" select="ancestor::index" />
        <xsl:param name="crossRefs">
            <xsl:for-each select="crossRef">
                <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                <xsl:variable name="v_crossRef_id" select="@id" />
                <!-- es wird geprüft, ob das verweisziel oder dessen unterlemmata referenzen auf artikelnummern (sections) 
                    enthält, anderenfalls wird der verweis eliminiert -->                
                <xsl:if test="$index//item[@id=$v_crossRef_id]//sections/section">
                    <xsl:call-template name="crossRef">
                        <xsl:with-param name="p_basisstandort" select="$p_basisstandort"/>
                    </xsl:call-template>
                </xsl:if>
                
            </xsl:for-each>
        </xsl:param>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

        <xsl:if test="$crossRefs/crossRef">
            <crossRefs log2="rcr1"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:copy-of select="$crossRefs" />
            </crossRefs><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>
    </xsl:template>
    
    <!-- verweisumkehr -->
    <xsl:template name="crossRef">
        <xsl:param name="p_basisstandort" />
        <xsl:choose>
            <xsl:when test="crossRef">
                <xsl:for-each select=".//crossRef[not(crossRef)]">
                    <xsl:choose>
                        <xsl:when test="$p_basisstandort = lemma">
                            <xsl:call-template name="verweisumkehr" />    
                        </xsl:when>
                        <xsl:otherwise>                            
                            <crossRef log2="rcr2">
                                <xsl:copy-of select="@*" />
                                <xsl:copy-of select="lemma" />
                                <xsl:call-template name="verweisumkehr" />                                    
                                
                            </crossRef>                            
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:when>
            <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
        </xsl:choose>
    </xsl:template> 
    
    <!-- Dreht die Verschachtelung um: innerstes crossRef wird zum äußersten crossRef --> 
    <xsl:template name="verweisumkehr">
        <xsl:for-each select="parent::crossRef">            
            <crossRef log2="rcr3"><xsl:copy-of select="@*"/>
                <xsl:copy-of select="lemma"/>
                <xsl:call-template name="verweisumkehr" />                
            </crossRef>
        </xsl:for-each>
    </xsl:template>
    
    <!-- die elemente <lemma> in den registern formatieren -->
    <xsl:template match="lemma[ancestor::index]">
        <!-- in diesem template werden ordnungsnummern in geschweiften klammern und ergänzungen nach einer raute transformiert -->
        
        <!-- hinsichtlich der ordnungsnummern wird diese transformation obsolet, 
         sobald im epigraf-editor das neue eingabefeld "sortierschlüssel" korrekt verwendet wird-->
        
        <!-- wenn lateinische oder arabische ordnungsnummern in geschweiften klammern vorkommen, 
    werden sie zum korrekten sortieren in einem attribut numerisch aufgelöst:
    die angabe in geschweiften klammern wird in zwei schritten isoliert, 
    dann in einem dritten schritt in eine zahl mit führenden nullen umgewandelt;
    jeder schritt erfolgt in einem parameter, der das transformationsergebnis festhält, 
    der dritte parameter wird bei der nachfolgenden transformation des elements <lemma> 
    als attributwert übernommen-->
        <!-- TODO: Funktion implementieren -->
        <!-- schritt 1 -->
        <xsl:param name="p_string1"><xsl:value-of select="substring-before(.,'}')"/></xsl:param>
        <!-- schritt 2 -->
        <xsl:param name="p_string2"><xsl:value-of select="substring-after($p_string1,'{')"/></xsl:param>
        <!-- schritt 3 -->
        <xsl:param name="p_string3">
            <xsl:if test="$p_string2='I'">001</xsl:if>
            <xsl:if test="$p_string2='II'">002</xsl:if>
            <xsl:if test="$p_string2='III'">003</xsl:if>
            <xsl:if test="$p_string2='IV'">004</xsl:if>
            <xsl:if test="$p_string2='V'">005</xsl:if>
            <xsl:if test="$p_string2='VI'">006</xsl:if>
            <xsl:if test="$p_string2='VII'">007</xsl:if>
            <xsl:if test="$p_string2='VIII'">008</xsl:if>
            <xsl:if test="$p_string2='VIIII'">009</xsl:if>
            <xsl:if test="$p_string2='IX'">009</xsl:if>
            <xsl:if test="$p_string2='X'">010</xsl:if>
            <xsl:if test="$p_string2='XI'">011</xsl:if>
            <xsl:if test="$p_string2='XII'">012</xsl:if>
            <xsl:if test="$p_string2='XIII'">013</xsl:if>
            <xsl:if test="$p_string2='XIV'">014</xsl:if>
            <xsl:if test="$p_string2='XV'">015</xsl:if>
            <xsl:if test="$p_string2='XVI'">016</xsl:if>
            <xsl:if test="$p_string2='XVII'">017</xsl:if>
            <xsl:if test="$p_string2='XVIII'">018</xsl:if>
            <xsl:if test="$p_string2='XVIIII'">019</xsl:if>
            <xsl:if test="$p_string2='XIX'">019</xsl:if>
            <xsl:if test="$p_string2='XX'">020</xsl:if>
            <xsl:if test="$p_string2='XXI'">021</xsl:if>
            <xsl:if test="$p_string2='XXII'">022</xsl:if>
            <xsl:if test="$p_string2='XXIII'">023</xsl:if>
            <xsl:if test="$p_string2='XXIV'">024</xsl:if>
            <xsl:if test="$p_string2='XXV'">025</xsl:if>
            <xsl:if test="$p_string2='XXVI'">026</xsl:if>
            <xsl:if test="$p_string2='XXVII'">027</xsl:if>
            <xsl:if test="$p_string2='XXVIII'">028</xsl:if>
            <xsl:if test="$p_string2='XXVIIII'">029</xsl:if>
            <xsl:if test="$p_string2='XXIX'">029</xsl:if>
            <xsl:if test="$p_string2='XXX'">030</xsl:if>
            <xsl:if test="contains($p_string2,'1') or
                contains($p_string2,'2') or
                contains($p_string2,'3') or
                contains($p_string2,'4') or
                contains($p_string2,'5') or
                contains($p_string2,'6') or 
                contains($p_string2,'7') or 
                contains($p_string2,'8') or
                contains($p_string2,'9')"><xsl:number value="$p_string2" format="0001"></xsl:number></xsl:if>
        </xsl:param>
        <xsl:choose>
            <!-- lemmata mit ordnungszahlen in geschweiften klammern -->
            <xsl:when test="contains(.,'{')">
                <!-- das element wird neu angelegt, die attribute werden übernommen -->
                <lemma log2="rle1">
                    <xsl:copy-of select="@*"/>
                    <!-- das attribut @sortstring wird angelegt;
                     um die alfabetische sortierbarkeit herzustellen, wird die ordnungszahl 
                     mit den geschweiften klammern durch die entsprechende zahl mit führenden nullen ersetzt-->
                    <xsl:attribute name="sortstring"><xsl:value-of select="substring-before(.,'{')"/><xsl:value-of select="$p_string3"/><xsl:value-of select="substring-after(.,'}')"/></xsl:attribute>
                    <!-- aus dem string im element <lemma> werden die geschweiften klammern herausgefiltert -->
                    <xsl:value-of select="translate(.,'{}','')"/>
                </lemma>
            </xsl:when>
            <!-- lemmata mit (vorläufigen) ergänzungen nach einer raute #;
             der teil-string ab der raute wird abgeworfen-->
            <xsl:when test="contains(.,'#')">
                <!-- das element wird neu angelegt, die attribute werden übernommen -->
                <lemma log2="rle1a">
                    <xsl:copy-of select="@*"/>
                    <!-- das attribut @sortstring bilden und als wert den string vor der raute einfügen -->
                    <xsl:attribute name="sortstring"><xsl:value-of select="substring-before(.,'#')"/></xsl:attribute>
                    <!-- dasselbe mit dem textknoten im element <lemma> -->
                    <xsl:value-of select="substring-before(.,'#')"/>
                </lemma>
            </xsl:when>
            <!-- lemmata ohne geschweifte klammern oder raute -->
            <xsl:otherwise>
                <lemma log2="le2">
                    <xsl:copy-of select="@*"/>
                    <!--<xsl:attribute name="sortstring"><xsl:value-of select="."/></xsl:attribute>-->
                    <xsl:value-of select="."/>
                </lemma>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <!-- im wappenregister aus dem element <name> das element <lemma> bilden -->    
    <xsl:template match="name">
        <xsl:choose>
            <xsl:when test="ancestor::index[@propertytype='heraldry']">
                <lemma>
                    <xsl:copy-of select="@*"/>
                    <xsl:value-of select="."/>
                </lemma>
            </xsl:when>
            <xsl:otherwise>
                <name log2="rname">
                    <xsl:copy-of select="@*"/>
                    <xsl:value-of select="."/>
                </name>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
</xsl:stylesheet>