<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans1-commons.xsl"/>
    <xsl:import href="di-trans1-indices-commons.xsl"/>
    <xsl:output indent="no"/>  

    <!-- mit diesem stylesheet wird das literatur- und quellenverzeichnis erzeugt;
        es wird aufgerufen in di-trans1-indices.xsl im template indices
    -->

    <xsl:template name="litRefs">
        <!-- literaturliste aus den literaturverweisen neu erstellen: 
            diese verfahren ist notwendig, weil sich in der Literaturliste (index[@propertytype='literature'])
            titel befinden können, die verwaist oder überflüssig sind;
            das erstellen einer neues liste aus den referenzen/verweisen auf literaturtitel 
            im bandartikel und in den katalogartikeln stellt sicher, dass nur die tatsächlich 
            verwendeten titel im literaturverzeichnis erscheinen;
            (der letzte parameter in dieser serie liefert eine liste der verwaisten titel; 
            sie kann über die parameterausgabe im transformationsergebnis sichtbar gemacht werden,
            wird aber im weiteren nicht mehr verwendet)
        -->
        <!-- die schritte 1 bis 7 erfolgen in einer serie von parametern
        
         schritt 1: alle literaturreferenzen aus einleitung und katalog sammeln
         schritt 2: daraus die doubletten entfernen
         schritt 3: von dieser liste ausgehend die komplementären einträge im literaturindex ansteuern und auslesen
         schritt 4: zu den titel dieser liste die obereinträge aus dem literaturindex hinzufügen
         schritt 5: die obereinträge der nächst höheren (obersten) stufe hinzufügen
         schritt 6: doubletten aus dem ergebnis von schritt 5 entfernen
         schritt 7: überflüssige und verwaiste titel zusammenstellen (nur zur information, wird im weiteren nicht benötigt)
         schritt 8: literaturliste ausgeben
         schritt 9: verwaiste titel ausgeben (nur informativ, wird im weiteren nicht verwendet)
        -->
        <!-- attribute des elements <index> in einem parameter ablegen -->
        <xsl:param name="p_literatur-attributs"><index><xsl:copy-of select="@*"></xsl:copy-of></index></xsl:param>

        <!-- schritt 1: die einzelnen titel aus dem gesamten band auslesen -->
        <xsl:param name="p_litRefs1">            
            <!-- Zuerst aus den freien literaturverweisen in textabschnittten und fußnoten -->
            <xsl:for-each select="..//rec_lit[not(ancestor::links)]">
                <litRef log1="lr1">
                    <xsl:attribute name="id" select="@data-link-target"/>
                    <xsl:value-of select="@data-link-value"/>
                </litRef><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            
            <!-- Dann aus den abschnitten der literaturnachweise in den bandartikeln -->            
            <xsl:for-each select="../article/sections/section[@sectiontype='references']/items/item/property">
                <litRef log1="lr2">
                    <xsl:copy-of select="@id"/>
                    <xsl:value-of select="name"/>
                </litRef><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
            
            <!-- Sofern Wappenbeschreibungen ausgegeben werden: Aus Nachweisfeldern der identifizierten, nicht-ausgeblendeten Wappen -->
            <xsl:if test="../article[@articletype='epi-book']/sections/section[@sectiontype='chapter']/items/item/property[@propertytype='datakeys'][norm_iri/text()='di_blazons']">
                <xsl:for-each select="../index[@propertytype='heraldry']/properties/property[not(starts-with(name,'?')) and (not(@ishidden) or @ishidden='0')]/source_from//rec_lit">
                    <litRef log1="lr13">
                        <xsl:attribute name="id" select="@data-link-target" />
                        <xsl:value-of select="@data-link-value"/>
                    </litRef><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:for-each>
            </xsl:if>
            
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:param>
        
        <!-- schritt 2: aus der ersten abfrage die doubletten entfernen -->
        <xsl:param name="p_litRefs2">           
            <xsl:for-each select="$p_litRefs1/litRef[not(@id=preceding-sibling::litRef/@id)]">
                <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- literaturindex mitgeben -->
            <xsl:copy-of select="properties" />
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:param>
        
        <!-- schritt 3: alle einträge der referenzliste im literaturindex ansteuern und auslesen-->
        <xsl:param name="p_litRefs3">           
            <xsl:for-each select="$p_litRefs2">
                <xsl:for-each select="litRef">
                    <xsl:variable name="p_litRef_id" select="@id"/>
                    <!-- für alle einträge aus der referenzliste die komplementären einträge im literaturindex ansteuern -->
                    <xsl:for-each select="../properties/property[@id=$p_litRef_id]">
                        <!--  den eintrag ausgeben -->
                        <xsl:copy-of select="." />
                        <!-- verweisziele aufsuchen und ausgeben -->
                        <xsl:for-each select="../property[@parent_id=current()/@id]">
                            <xsl:for-each select="../property[@id=current()/@related_id]">
                                <xsl:copy-of select="." />
                            </xsl:for-each>
                        </xsl:for-each>
                    </xsl:for-each>
                </xsl:for-each>
                <!-- literaturindex wieder einfügen -->
                <xsl:copy-of select="properties" />
            </xsl:for-each>
        </xsl:param>
        
        <!-- schritt 4: obereinträge aus dem literaturindex hinzufügen -->
        <xsl:param name="p_litRefs4">           
            <xsl:for-each select="$p_litRefs3">
                <xsl:for-each select="property">
                    <!-- template zum aulesen der obereinträge aus dem literaturindex aufrufen -->
                    <xsl:call-template name="litRefWithParents" />
                </xsl:for-each>
                <!-- literaturindex wieder einfügen -->
                <xsl:copy-of select="properties" />
            </xsl:for-each>
        </xsl:param>
        
        <!-- schritt 6: doubletten aus dem vorigen parameter entfernen -->
        <xsl:param name="p_litRefs5">           
            <xsl:for-each select="$p_litRefs4">
                <xsl:for-each select="property">
                    <xsl:sort select="@sortkey" order="ascending" lang="de" case-order="upper-first" />
                    <xsl:choose>
                        <xsl:when test="@id=preceding-sibling::property/@id"></xsl:when>
                        <xsl:otherwise>
                            <xsl:copy-of select="." /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>       
                <!-- literaturindex wieder einfügen -->
                <xsl:copy-of select="properties" />          
            </xsl:for-each>
        </xsl:param>        
        
        <!-- schritt 7: überflüssige und verwaiste titel suchen und ausgeben -->
        <xsl:param name="p_litRefs6">           
            <!-- alle einträge der refernzliste im literaturindex ansteuern-->
            <xsl:for-each select="$p_litRefs5">
                <xsl:for-each select="properties/property">
                    <xsl:variable name="p_litRef_id" select="@id" />
                    <!-- nur die auslesen die keine entsprechung in katalog oder einleitung haben -->
                    <xsl:choose>
                        <xsl:when test="lemma='siehe'"></xsl:when>
                        <xsl:when test="../../property[@id=$p_litRef_id]"></xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="lemma"/>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:for-each>
        </xsl:param>
        
        <!-- parametertest
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <parametertests><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>      
            <test1><xsl:copy-of select="$p_litRefs1"></xsl:copy-of></test1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <test2><xsl:copy-of select="$p_litRefs2"></xsl:copy-of></test2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <test3><xsl:copy-of select="$p_litRefs3"></xsl:copy-of></test3><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <test4><xsl:copy-of select="$p_litRefs4"></xsl:copy-of></test4><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <test5><xsl:copy-of select="$p_litRefs5"></xsl:copy-of></test5><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </parametertests><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>          
        -->
        <xsl:comment>alle literaturverweise aus katalog und einleitung</xsl:comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
        <!-- schritt 8: literaturliste ausgeben -->
        <xsl:for-each select="$p_litRefs5">
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:for-each select="property[@level='0']">
                    <item>
                        <xsl:copy-of select="@*" />
                        <xsl:for-each select="*"><xsl:copy-of select="." /></xsl:for-each>
                        <xsl:call-template name="items-hierarchisieren" />
                    </item>
                </xsl:for-each>            
        </xsl:for-each>        
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        
        <!-- schritt 9: verwaiste titel ausgeben -->
        <xsl:comment>überflüssige literaturtitel<xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="$p_litRefs6" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

    <!-- Entry point: outputs the entry itself, then walks up all parents -->
    <xsl:template name="litRefWithParents">
        <!-- capture the literaturindex from the calling context -->
        <xsl:param name="p_index" select="../properties" />
        
        <!-- the entry itself -->
        <xsl:copy-of select="." />
        <level><xsl:value-of select="@level"/></level>
        
        <!-- then all parents -->
        <xsl:call-template name="parentLitRef">
            <xsl:with-param name="p_parent-id" select="@parent_id" />
            <xsl:with-param name="p_index" select="$p_index" />
        </xsl:call-template>
    </xsl:template>
    
    
    <!-- Recursion: outputs one parent and calls itself for the next -->
    <xsl:template name="parentLitRef">
        <xsl:param name="p_parent-id" />
        <xsl:param name="p_index" />
        
        <!-- look up the parent in the passed-in index, NOT via ../ -->
        <xsl:for-each select="$p_index/property[@id=$p_parent-id]">
            <xsl:copy-of select="." />
            <level><xsl:value-of select="@level"/></level>
            
            <!-- recurse with THIS parent's own parent_id -->
            <xsl:if test="@parent_id and @parent_id != ''">
                <xsl:call-template name="parentLitRef">
                    <xsl:with-param name="p_parent-id" select="@parent_id" />
                    <xsl:with-param name="p_index" select="$p_index" />
                </xsl:call-template>
            </xsl:if>
        </xsl:for-each>
    </xsl:template>

</xsl:stylesheet>