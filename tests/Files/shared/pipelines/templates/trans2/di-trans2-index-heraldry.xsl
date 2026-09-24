<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans2-commons.xsl"/>
    <xsl:import href="di-trans2-indices-commons.xsl"/>

    <!-- 
        dieses stylesheet strukturiert 
        1. das wappenregister (register-wappen)       
            die nicht identifizierten werden am ende des registers als gruppe ausgegeben
        2. die liste der wappenbeschreibungen für den anhang (register-blasonierungen)
        
        beides wird aufgerufen in di-trans2-indices.xsl
    
    -->    
    
    
    <xsl:template name="register-wappen">
      <!-- wird aufgerufen in 
            template name="einzelregister" und in 
            template match="index"
      -->
        
        <!-- wappenregister in einem parameter sortieren -->
        <xsl:param name="p_wappen-sort">
            <index>
            <xsl:for-each select="item">
                <xsl:sort select="name/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                <xsl:copy-of select="."></xsl:copy-of>
            </xsl:for-each>
            </index>
        </xsl:param>
        
        <!-- das wappenregister wird in zwei weiteren parametern vorformatiert -->
        
        <xsl:param name="p_wappen1">
            <!-- identifizierte wappen ansteuern -->
            <xsl:for-each select="$p_wappen-sort/index/item[not(starts-with(name,'?'))]">
                <!-- bei bezeichnungen mit ordnungsnummern in geschweiften klammern 
                    den string vor der klammer speichern -->
                <xsl:variable name="v_lemmax"><xsl:value-of select="substring-before(name,' {')"/></xsl:variable>
                <xsl:choose>
                    <!-- mit ordnungsnummern in geschweiften klammern versehene wappen desselben wappenträgers 
                        werden als gruppe erfasst und im nächsten paramter neu nummeriert -->
                    <xsl:when test="contains(name,'{')">
                        <xsl:if test="not(preceding-sibling::item[substring-before(name,' {')=$v_lemmax])">
                            <item type="group" log2="r39">
                                <xsl:copy-of select="@*"></xsl:copy-of>
                                <xsl:for-each select="ancestor::index/item[name[substring-before(.,' {')=$v_lemmax]]">
                                    <item log2="r40">
                                        <xsl:copy-of select="@*"/>
                                        <lemma>
                                            <xsl:copy-of select="name/@*"></xsl:copy-of>
                                            <xsl:value-of select="substring-before(name,' {')"/></lemma>   
                                        <xsl:for-each select="sections">
                                            <xsl:copy-of select="."/>
                                        </xsl:for-each>
                                        <xsl:call-template name="crossRefs" />
                                    </item>
                                </xsl:for-each>
                            </item>
                        </xsl:if>
                    </xsl:when>
                    <!-- ohne ordnungsnummern in geschweiften klammern -->
                    <xsl:otherwise>
                        <item log2="r40a"><xsl:copy-of select="@*"/>
                            <xsl:for-each select="*">
                                <xsl:choose>
                                    <xsl:when test="self::name"><lemma><xsl:value-of select="name"/></lemma></xsl:when>
                                    <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
                                </xsl:choose>
                            </xsl:for-each>
                        </item>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:param> 
        <xsl:param name="p_wappen2">
            <xsl:for-each select="$p_wappen1/*">
                <xsl:choose>
                    <!-- bei im vorigen parameter gruppierten wappen werden die ordungsnummern neu erzeugt -->
                    <xsl:when test="@type='group'">
                        <!-- gruppen ansteuern -->
                        <xsl:for-each select="*">
                            <item log2="r41">
                                <xsl:attribute name="position"><xsl:value-of select="position()"/></xsl:attribute>
                                <xsl:copy-of select="@*"/>                              
                                <!-- die wappenbezeichnungen erhalten neue römische ziffern ensprechend ihrer position -->
                                <lemma>
                                    <xsl:copy-of select="lemma/@*"/>
                                    <xsl:value-of select="lemma"/><xsl:text> </xsl:text><xsl:number from="item[@type='group']" format="I"/></lemma>
                                <xsl:for-each select="sections">
                                    <xsl:copy-of select="."/>
                                </xsl:for-each>
                                <xsl:call-template name="crossRefs" />
                            </item>
                        </xsl:for-each>
                    </xsl:when>
                    <!-- items außerhalb von gruppen ansteuern -->
                    <xsl:otherwise>
                        <item log2="r41a"><xsl:copy-of select="@*"/>
                            <xsl:for-each select="*">
                                <xsl:choose>
                                    <xsl:when test="self::name"><lemma><xsl:value-of select="name"/></lemma></xsl:when>
                                    <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
                                </xsl:choose>
                            </xsl:for-each>
                        </item>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:param>
        
<!--        <wappen_sort><xsl:copy-of select="$p_wappen-sort"></xsl:copy-of></wappen_sort>
        <wappen1><xsl:copy-of select="$p_wappen1"></xsl:copy-of></wappen1>-->
        
        <!-- wappenregister -->
        <!-- identfizierte wappen -->
        <xsl:for-each select="item[not(starts-with(name,'?'))]">
                <xsl:sort select="name/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                <xsl:variable name="v_lemmax" select="substring-before(name,' {')" />
                <item log2="r42">
                    <xsl:copy-of select="@*"/>
                    <!-- hier die wappen-varianten neu nummerieren -->
                    <xsl:choose>
                        <xsl:when test="substring-before(name,' {')">
                            <xsl:choose>
                                <xsl:when test="following-sibling::item[substring-before(name,' {')=$v_lemmax] or preceding-sibling::item[substring-before(name,' {')=$v_lemmax]">
                                  <xsl:copy-of select="$p_wappen2//item[@id=current()/@id]/lemma" />
                                </xsl:when>
                                <xsl:otherwise>
                                  <lemma>
                                    <xsl:copy-of select="name/@*" />
                                    <xsl:value-of select="$v_lemmax" />
                                </lemma>
                              </xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:for-each select="name">
                                  <lemma>
                                      <xsl:copy-of select="@*" />
                                      <xsl:value-of select="." />
                                </lemma>
                            </xsl:for-each>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="sections">
                        <xsl:copy-of select="." />
                    </xsl:for-each>
                    <xsl:call-template name="crossRefs" />
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </item>
            </xsl:for-each>
            <!-- nicht identifizierte wappen -->
            <xsl:choose>
                <!-- nicht identifizierte wappen mit angabe der blasonierung -->
                <xsl:when test="$sw_unidentifizierte_wappen_beschreiben=1">
                    <item type="group" log2="r43a"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <lemma>nicht identifizierte Wappen:</lemma><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        
                        <!-- zuerst diejenigen wappen, deren schild erkennbar ist -->
                        <item type="group" log2="r43aa"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:for-each select="item[substring(name,1,1)='?'][content[node()]]">
                                <xsl:sort select="name/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                                <item><xsl:copy-of select="@*"/>
                                    <xsl:for-each select="content">
                                        <lemma><xsl:copy-of select="../name/@*"/><xsl:apply-templates /></lemma>
                                    </xsl:for-each>
                                    <xsl:for-each select="sections"><xsl:copy-of select="." /></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <xsl:call-template name="crossRefs" />
                               </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:for-each>
                        </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        
                        <!-- zum Schluss die wappen, bei denen nur die Helmzier erkennbar ist  -->
                        <item type="group" log2="r43ab"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:for-each select="item[substring(name,1,1)='?'][not(content[node()])]">
                              <xsl:sort select="name/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                              <item><xsl:copy-of select="@*"/>
                                  <xsl:for-each select="elements">
                                      <lemma><xsl:copy-of select="../name/@*"/><xsl:apply-templates /></lemma>
                                  </xsl:for-each>
                                  <xsl:for-each select="sections">
                                      <xsl:copy-of select="."/>
                                  </xsl:for-each>
                                  <xsl:call-template name="crossRefs" />
                              </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:for-each>       
                        </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        
                    </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:when>
                
                <!-- nicht identifizierte wappen ohne angabe der blasonierung, es werden nur die Artikelnummern summarisch aufgelistet -->
                <xsl:otherwise>
                    <item type="group" log2="r43"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <lemma>nicht identifizierte Wappen</lemma><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:for-each select="item[substring(name,1,1)='?']/sections/section">
                                <xsl:copy-of select="."/>
                                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:for-each>
                        </sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:otherwise>
            </xsl:choose>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>

    <xsl:template name="register-blasonierungen">
        <!-- Dieses template wird in di-trans2-indices.xsl vom Template des Blasonierungsregisters (name="einzelregister") aufgerufen.
             Es wird auch in di-trans2-book aufgerufen, falls Wappen als Wappenbeschreibungsanhang ausgegeben werden soll. 
             Datengrundlage ist in beiden Fällen das Wappenregister.             
        -->
        
        <!-- zuerst wird das wappenregister  in zwei parametern vorformatiert -->
        <xsl:param name="p_wappen1">
            <xsl:for-each select="item[not(starts-with(name,'?'))]">
                <xsl:variable name="v_lemmax"><xsl:value-of select="substring-before(name,' {')"/></xsl:variable>
                <xsl:choose>
                    
                    <!-- betrifft wappenvarianten desselben wappenträgers:
                        mit ordnungsnummern in geschweiften klammern versehene wappen desselben wappenträgers 
                        werden als gruppe erfasst und im nächsten paramter neu nummeriert;
                    
                        das umnumerieren ist erforderlich, weil in der datenbank mehr varianten vorkommen können, 
                        als im aktuellen projekt davon referenziert werden; die nummerierung erfolgt dann gemäß
                        der anzahl der tatsächlich aufgerufenen varianten.
                        
                        beispiel:
                        in der datenbank kommen vor 
                        Völschow {1}
                        Völschow {2}
                        Völschow {3}
                        Völschow {4}
                        Völschow {5}
                        
                        im bestand des projekts (also im band) kommen davon aber nur 1, 3 und 5 vor;
                        die umnummerierung ergibt dann:
                        Völschow {1} zu Völschow {1}  
                        Völschow {3} zu Völschow {2}
                        Völschow {5} zu Völschow {3}
                    -->
                    <xsl:when test="contains(name,'{')">
                        <!-- das erste von mehreren einträgen mit gleichlautendem <name> mit ordnungsnummer ansteuern -->
                        <xsl:if test="not(preceding-sibling::item[substring-before(name,' {')=$v_lemmax])">
                            <!-- gruppen-item bilden -->
                            <item type="group" log2="r44">
                                <!-- alle einträge mit gleichlautendem <name> ansteuern und in die gruppe einfügen, 
                                    element <name> in element <lemma> umwandeln  -->
                                <xsl:for-each select="ancestor::index/item[name[substring-before(.,' {')=$v_lemmax]]">
                                    <item log2="r46">
                                        <xsl:copy-of select="@*"/>
                                        <lemma>
                                            <xsl:copy-of select="name/@*"></xsl:copy-of>
                                            <xsl:value-of select="substring-before(name,' {')"/>
                                        </lemma>   
                                        <xsl:for-each select="sections">
                                            <xsl:copy-of select="."/>
                                        </xsl:for-each>
                                        <xsl:call-template name="crossRefs" />
                                    </item>
                                </xsl:for-each>
                            </item>
                        </xsl:if>
                    </xsl:when>
                    
                    <!-- ohne ordnungsnummern in geschweiften klammern: element <name> in <lemma> umwandeln -->
                    <xsl:otherwise>
                        <item log2="r40aa">
                            <xsl:copy-of select="@*"/>
                            <xsl:for-each select="*">
                                <xsl:choose>
                                    <xsl:when test="self::name">
                                        <lemma>
                                            <xsl:copy-of select="name/@*"></xsl:copy-of>
                                            <xsl:value-of select="name"/>
                                        </lemma></xsl:when>
                                    <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
                                </xsl:choose>
                            </xsl:for-each>
                        </item>
                    </xsl:otherwise>
                    
                </xsl:choose>
            </xsl:for-each>
        </xsl:param> 
        
        <xsl:param name="p_wappen2">
            <xsl:for-each select="$p_wappen1/*">
                <xsl:choose>
                    
                    <!-- bei im vorigen parameter gruppierten wappen werden die 
                        ordungsnummern hinter den wappenträgerbezeichnungen neu erzeugt -->
                    <xsl:when test="@type='group'">
                        <xsl:for-each select="*">
                            <item log2="r45">
                                <xsl:attribute name="position"><xsl:value-of select="position()"/></xsl:attribute>
                                <xsl:copy-of select="@*"/>
                                <!-- die wappenbezeichnungen erhalten neue römische ziffern ensprechend ihrer position -->
                                <lemma>
                                    <xsl:copy-of select="lemma/@*"/>
                                    <xsl:value-of select="lemma"/><xsl:text> </xsl:text><xsl:number value="position()" format="I"/>
                                </lemma>
                                <xsl:for-each select="sections">
                                    <xsl:copy-of select="."/>
                                </xsl:for-each>
                                <xsl:call-template name="crossRefs" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </item>
                        </xsl:for-each>
                    </xsl:when>
                    
                    <!-- wappen außerhalb von gruppen -->
                    <xsl:otherwise>
                        <item log2="r40ab"><xsl:copy-of select="@*"/>
                            <xsl:for-each select="*">
                                <xsl:choose>
                                    <xsl:when test="self::name">
                                        <lemma>
                                            <xsl:copy-of select="name/@*"></xsl:copy-of>
                                            <xsl:value-of select="name"/>
                                        </lemma></xsl:when>
                                    <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
                                </xsl:choose>
                            </xsl:for-each>
                        </item>
                    </xsl:otherwise>
                    
                </xsl:choose>
            </xsl:for-each>
        </xsl:param>
        
        <!-- wappenbeschreibungen -->         
        <xsl:for-each select="item[not(starts-with(name,'?')) and (not(@ishidden) or @ishidden='0') and (content/node() or source_from/node())]">
            
            <xsl:sort select="name/@sortstring" order="ascending" lang="de" case-order="upper-first" />
            
            <!-- in <name> wird der string vor ergänzungen in geschweiften klammern herausgelöst -->
            <xsl:variable name="v_lemmax" select="substring-before(name,' {')" />
            
            <item log2="r46a">
                <xsl:copy-of select="@*"/>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:choose>
                    
                    <xsl:when test="substring-before(name,' {')">
                        <xsl:choose>
                            <xsl:when test="following-sibling::item[substring-before(name,' {')=$v_lemmax] or preceding-sibling::item[substring-before(name,' {')=$v_lemmax]">
                                    <xsl:copy-of select="$p_wappen2//item[@id=current()/@id]/lemma"/>
                            </xsl:when>
                            <xsl:otherwise>
                                <!-- <name> wird zu <lemma> -->
                                <lemma>
                                    <xsl:copy-of select="name/@*"></xsl:copy-of>
                                    <xsl:value-of select="$v_lemmax"/>
                                </lemma></xsl:otherwise>
                        </xsl:choose>
                    </xsl:when>
                    
                    <xsl:otherwise>
                        <!-- <name> wird zu <lemma> -->
                        <xsl:for-each select="name">
                                <lemma>
                                    <xsl:copy-of select="@*"></xsl:copy-of>
                                    <xsl:value-of select="."/>
                                </lemma>
                        </xsl:for-each>
                    </xsl:otherwise>
                    
                </xsl:choose><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                
                <!-- elemente umwidmen:
                     <content> zu <shield>
                     <elements> zu <crest>
                     <sourde_from> zu <biblio> 
                -->
                <shield><p><xsl:apply-templates select="content" /></p></shield><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!--xsl:if test="elements[node()]">
                    <crest><p><xsl:apply-templates select="elements" /></p></crest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:if-->
                <xsl:if test="source_from[node()]">
                    <biblio><p><xsl:apply-templates select="source_from" /></p></biblio><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:if>
            </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
        
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
 
    
    <!-- für das register blasonierungen werden verweise auf literatur und marken kopiert-->
    <xsl:template match="rec_lit"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="rec_ma"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="rec_mz"><xsl:copy-of select="."/></xsl:template>
    


</xsl:stylesheet>