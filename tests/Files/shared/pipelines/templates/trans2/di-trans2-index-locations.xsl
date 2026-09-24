<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans2-commons.xsl"/>
    <xsl:import href="di-trans2-indices-commons.xsl"/>
    
    <!-- 
        in diesem stylesheet wird das standorteregister neu aufgebaut.
         es wird eine interen differenzierung zwischen 
         a) ehemaligen standorten und 
         b) aktuellen bzw. (bei verlorenen objekten) letztbekannten standorten
         
         bei städtischen beständen wird der basisstandort, i. e. die bezeichnung der stadt,
         die im standorteregister nicht erscheinen soll,
         als <item> eliminiert und dem nächst niedrigen <item> als attribut angefügt.
    -->
    
    <!-- für die münchener reihe wird das standorteregister 
        als eigener abschnitt außerhalb der register (template name=index_locations_table,
        für alle anderen reihen innerhalb der register (template name=register standorte)
        ausgegeben  -->
    

    <xsl:template name="register-standorte">
        <xsl:param name="p_basisstandort" />
        <xsl:param name="index" select="ancestor::index" />
       <!-- der basisstandort ist die bezeichnung des erfassungsgebiets eines inschriftenprojekts;
            er ist nicht relevant, wenn das erfassungsgebiet eine stadt ist, wird 
            für die kumulierung der register mehrerer inschriftenbände benötigt -->
        <xsl:param name="p_standorte1">
        <!-- 1. transformationsschritt: 
            1.1. eliminieren der basisstandort-items, 
            1.2. markieren der basisstandorte als attribut in den sub-items,
            1.3. hierarchisierung des registers, 
            1.4.auslesen der artikelnummern, 
            1.5. erweitern der referenzen auf artikel durch angaben zum objektzustand und
            zur position der standortangabe in der standortliste, 
            um in der folgenden transformation zwischen ehemaligen und aktuellen standorten differenzieren zu können
        -->
       
            <xsl:for-each select="item">
                <xsl:variable name="v_lemma" select="lemma" />
                <!-- im standortregister wird der basisstandort (der der projektbezeichnung entspricht) ignoriert
                    und gleich auf die untereinträge weitergeleitet-->
                <!-- für städte -->
                <xsl:choose>
                    <xsl:when test="$v_lemma=$p_basisstandort and not($sw_basisstandort_ausgeben=1)">
                        <xsl:call-template name="subitem-standorte-schleife">
                            <xsl:with-param name="p_basisstandort" select="$p_basisstandort" />
                        </xsl:call-template>
                    </xsl:when>
                    <!-- für landkreise -->
                    <!-- TODO: Keine Daten im Template, Ldkr entfernen -->
                    <xsl:when test="substring(lemma,1,5)= 'Ldkr.'">
                      <xsl:call-template name="ldkr"/> 
                    </xsl:when>
                    <xsl:otherwise>
                       <item log2="r10">
                         <xsl:copy-of select="@*"/>
                          <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                          <xsl:apply-templates select="lemma"/>
                          <xsl:for-each select="sections">
                              <xsl:copy-of select="."/>
                          </xsl:for-each>
                          <xsl:call-template name="crossRefs">
                              <xsl:with-param name="p_basisstandort" select="$p_basisstandort" />
                          </xsl:call-template><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                          <xsl:call-template name="subitem-standorte-schleife">
                              <xsl:with-param name="p_basisstandort" select="$p_basisstandort"/>
                          </xsl:call-template>
                      </item>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:param>
        
        <!-- 2. transformationschritt: neusortieren der items auf der obersten ebene -->
        <xsl:param name="p_standorte2">
            <xsl:for-each select="$p_standorte1/item">
                <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </xsl:param>
    
        <!-- 3. transformationsschritt: unterscheiden zwischen <item>s mit referenzen auf ehemalige und aktuelle standorte,
              <item>s mit gemischten referenzen werden dupliziert 
        -->
        <xsl:param name="p_standorte3">        
          <xsl:for-each select="$p_standorte2">
              <xsl:call-template name="standorte-trennen">
                  <xsl:with-param name="p_basisstandort"  select="$p_basisstandort"/>
                  <xsl:with-param name="index" select="$index"/>
              </xsl:call-template>
          </xsl:for-each> 
        </xsl:param>
        
        <!-- 4. dasselbe auf der nächst unteren ebene -->
        <xsl:param name="p_standorte4">
            <xsl:for-each select="$p_standorte3/item">
                <xsl:call-template name="standorte-trennen2">
                    <xsl:with-param name="p_basisstandort" select="$p_basisstandort" />
                    <xsl:with-param name="index" select="$index"/>
                </xsl:call-template>
            </xsl:for-each> 
        </xsl:param>

        
        <!--    parametertest
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <standorte1><xsl:copy-of select="$p_standorte1"/></standorte1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <standorte2><xsl:copy-of select="$p_standorte2"/></standorte2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <standorte3><xsl:copy-of select="$p_standorte3"/></standorte3><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <standorte4><xsl:copy-of select="$p_standorte4"/></standorte4><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <basisstandort><xsl:value-of select="$p_basisstandort"/></basisstandort><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        -->



        <!-- 5. die beiden kategorien aktuelle und ehemalige standorte bereinigen, 
              d. h. nicht zutreffendeuntereinträge entfernen -->
        <xsl:for-each select="$p_standorte4/item">
          <!-- die registerbäume werden dahingenden bereinigt, dass unter den aktuellen standorten 
          nur die links auf den aktuellen standort ausgewählt werden sowie vice versa mit den ehemaligen-->
          <xsl:call-template name="standorte-bereinigen">
              <xsl:with-param name="p_basisstandort" select="$p_basisstandort" />
              <xsl:with-param name="index" select="$index"/>            
          </xsl:call-template>
          <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <xsl:template name="subitem-standorte-schleife">
        <xsl:param name="p_basisstandort" />
        <!-- die untereinträge im standorte-register werden hierarchisiert -->
        <xsl:for-each select="item">
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <item log2="r2">
                <xsl:copy-of select="@*"/>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:call-template name="crossRefs">
                    <xsl:with-param name="p_basisstandort" select="$p_basisstandort" />
                </xsl:call-template>
                <xsl:copy-of select="sections"/>
                <xsl:call-template name="subitem-standorte-schleife" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>           
        </xsl:for-each>
    </xsl:template>
    
    
        <!-- die registerbäume werden dahingenden bereinigt, dass unter den aktuellen standorten 
            nur die links auf den aktuellen standort ausgewählt werden sowie vice versa mit den ehemaligen-->
    <xsl:template name="standorte-bereinigen">
        <xsl:param name="p_basisstandort" />
        <xsl:param name="index" select="ancestor::index" />
        <!-- @before=1 bedeutet ehemaliger standort (wird kursiv dargestellt)
                 @before=0 bedeutet aktueller standort
            -->    
        <xsl:param name="p_standort_status" select="@before" />
    
        <item>
            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:copy-of select="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:if test="sections/section[@before=$p_standort_status]">
                <sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="sections/section[@before=$p_standort_status]">
                        <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:if>
    
            <xsl:for-each select="item">
                <xsl:call-template name="standorte-bereinigen">
                    <xsl:with-param name="p_basisstandort" select="$p_basisstandort" />
                    <xsl:with-param name="index" select="$index" />
                </xsl:call-template>
            </xsl:for-each>
        </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
<xsl:template name="ldkr">
    <xsl:param name="p_ldkr"><xsl:text> (</xsl:text><xsl:value-of select="lemma"/><xsl:text>)</xsl:text></xsl:param>
    <xsl:for-each select="item">
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <item log2="r11">
                <xsl:copy-of select="@*"/>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <lemma log2="rldkr">
                    <xsl:copy-of select="lemma/@*"/>
                    <xsl:value-of select="lemma"/><xsl:value-of select="$p_ldkr"/>
                </lemma><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:for-each select="sections">
                    <xsl:copy-of select="."/>
                </xsl:for-each>
        <xsl:for-each select="crossRefs">
            <xsl:call-template name="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
                <xsl:call-template name="subitem-standorte-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>           
    </xsl:for-each>
</xsl:template>

    <xsl:template name="standorte-trennen">
        <xsl:param name="p_basisstandort" />
        <xsl:param name="index" select="ancestor::index" />
        <!-- trennung der standort-einträge nach aktuellen und ehemaligen standorten -->
        <xsl:for-each select="item">
            <xsl:choose>
                <!-- zuerst werden die einträge angesteuert, die nicht ausschließlich verweise enthalten;
                diese werden unterschieden in einträge, die 
                (1) auf artikel/objekte verweisen in denen der angebene standort der aktuelle oder letzt bekannte ist und 
                (2) auf artikel/objekte in denen der angegebene standort ein früherer/ehemaliger standort ist-->
                <xsl:when test="sections/section">
                    <!-- 1. aktuelle standorte (@before='0')-->
                    <xsl:choose>
                        <!-- wenn zum betreffenden eintrag oder in untereinträgen verweise auf aktuelle standorte vorkommen,
                        wird der eintrag angelegt, aber nur die verweise auf aktuelle standorte auf derselben ebene,
                        wenn vorhanden, werden hier gesammelt; danach werden die untereinträge aufgerufen und analog behandelt -->
                        <xsl:when test="sections/section[@before='0']">
                        <item log2="r12">
                            <xsl:copy-of select="@*"/>
                            <!-- das attribut @before aus den elementen <section> wird auf das element <item> übertragen:
                                    @before=0 bedeutet aktueller standort
                                    @before=1 bedeutet ehemaliger standort (wird kursiv dargestellt)
                            -->
                            <xsl:attribute name="before">0</xsl:attribute>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:if test="sections/section">
                                <sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <xsl:for-each select="sections/section[@before='0']">
                                        <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    </xsl:for-each>
                                </sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:if>
                            <xsl:copy-of select="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                            
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:call-template name="standorte-trennen">
                                <xsl:with-param name="p_basisstandort" select="$p_basisstandort"/>
                            </xsl:call-template>    
                        </item> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <!-- wenn auf derselben ebene auch verweise auf ehemalige standorte vorkommen, 
                                wird der eintrag verdoppelt und die verweise auf ehemalige standorte werden hier gesammelt -->
                            <xsl:if test="sections/section[@before='1']">
                                <item log2="r13">
                                    <xsl:copy-of select="@*"/>
                                    <xsl:attribute name="before">1</xsl:attribute>
                                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <xsl:if test="sections/section">
                                        <sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                            <xsl:for-each select="sections/section[@before='1']">
                                                <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                            </xsl:for-each>
                                        </sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    </xsl:if>
                                    <xsl:copy-of select="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <xsl:call-template name="standorte-trennen">
                                        <xsl:with-param name="p_basisstandort" select="$p_basisstandort" />
                                    </xsl:call-template>    
                                </item> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:if>
                        </xsl:when>
                        
                        <!-- 2. ehemalige standorte (@before='1') -->
                        <xsl:otherwise>
                        <item log2="r14">
                            <xsl:copy-of select="@*"/>
                            <xsl:attribute name="before">1</xsl:attribute>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="lemma" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:if test="sections/section">
                             <sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                 <xsl:for-each select="sections/section[@before='1']">
                                     <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                 </xsl:for-each>
                             </sections><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:if>
                            <!-- verweise werden nur ausgelesen, wenn sie nicht schon vorher 
                                unter @before='0' erfasst wurden, d. h.
                            wenn der standort nicht auch als aktueller standort erfasst ist-->
                            <xsl:if test="not(sections/section[@before='0'])">
                                <xsl:copy-of select="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:if>
                            <xsl:call-template name="standorte-trennen">
                                <xsl:with-param name="p_basisstandort" select="$p_basisstandort"/>
                            </xsl:call-template>   
                        </item> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <!-- wenn keine referenzen auf artikelnummern aber verweise vorkommen -->
                <xsl:otherwise>
                    <item log2="r15">
                        <xsl:copy-of select="@*"/>
                        <xsl:attribute name="before">0</xsl:attribute>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:copy-of select="lemma" /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:copy-of select="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:call-template name="standorte-trennen">
                            <xsl:with-param name="p_basisstandort" select="$p_basisstandort" />
                        </xsl:call-template>    
                    </item><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="standorte-trennen2">
        <xsl:param name="p_basisstandort"></xsl:param>
        <xsl:param name="index" select="ancestor::index" />
        <xsl:param name="p_standort_status" select="@before" />
        
        <xsl:choose>
            <!-- aktuelle standorte -->
            <xsl:when test="$p_standort_status='0'">
                <xsl:choose>
                    
                <!-- wenn es denselben standort auch als ehemaligen gibt: doublizieren -->
                    <xsl:when test="preceding-sibling::item[@id=current()/@id and @before='1'] or following-sibling::item[@id=current()/@id and @before='1']">
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:copy>
                            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="sections"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>           
                            <xsl:for-each select="item[@before='0']">
                                <xsl:call-template name="standorte-trennen2"><xsl:with-param name="index" select="$index" /></xsl:call-template>
                            </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:copy><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:when>
                    
                    <xsl:otherwise><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:copy>
                            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="sections"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>       
                            <xsl:for-each select="item">
                                <xsl:call-template name="standorte-trennen2">
                                    <xsl:with-param name="index" select="$index" />
                                </xsl:call-template>
                            </xsl:for-each>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:copy><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>     
            </xsl:when>
            
            <!-- ehemalige standorte -->
            <xsl:when test="$p_standort_status='1'">
                <xsl:choose>
                    
                <!-- wenn es denselben standort auch als aktuellen gibt: doublizieren -->
                    <xsl:when test="preceding-sibling::item[@id=current()/@id and @before='0'] or following-sibling::item[@id=current()/@id and @before='0']">
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:copy>
                            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="sections"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                          
                            <xsl:for-each select="item[@before='1']">
                              <xsl:call-template name="standorte-trennen2">
                                  <xsl:with-param name="index" select="$index" />
                              </xsl:call-template>
                            </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:copy><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:when>
                    
                    <xsl:otherwise>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:copy><xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:copy-of select="sections"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <!-- verweise werden nur ausgelesen, wenn sie nicht schon vorher 
                            unter @before='0' erfasst wurden, d. h.
                        wenn der standort nicht auch als aktueller standort erfasst ist-->
                            <xsl:if test="not(sections/section[@before='0'])">
                                <xsl:copy-of select="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:if>                         
                        <xsl:for-each select="item">
                            <xsl:call-template name="standorte-trennen2">
                                <xsl:with-param name="index" select="$index" />
                            </xsl:call-template>
                        </xsl:for-each>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:copy><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:otherwise>
                    
                </xsl:choose>     
            </xsl:when>
        </xsl:choose>
</xsl:template>
    
    <!-- münchener reihe: taballarische übersicht der standorte,
            wird aufgerufen in di-trans2-book.xsl-->
    <xsl:template name="index_locations_table">
        <!-- basisstandort feststellen -->
        <xsl:param name="p_basisstandort"><xsl:value-of select="ancestor::book/project/name"/></xsl:param>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- 1. abschnitt anlegen, attribute kopieren-->
        <locations>
            <xsl:copy-of select="@*"></xsl:copy-of>
            <!-- 2. zum standorteregister navigieren, auch diese attribute kopieren -->
            <xsl:for-each select="/book/indices/index[@propertytype='locations']">
                <xsl:copy-of select="@*"></xsl:copy-of>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <!-- 3. template zum strukturieren der registereinträge aufrufen, parameter basisstandort mitgeben -->
                <xsl:call-template name="register-standorte">                    
                    <xsl:with-param name="p_basisstandort" select="$p_basisstandort" />
                    <xsl:with-param name="index" select="." />
                </xsl:call-template>
                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
        </locations>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
</xsl:stylesheet>