<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

    <xsl:import href="../commons/di-switch.xsl"/>


    <!-- FUNKTION DIESES STYLESHEETS: -->
    <!-- die struktur aus der vorheringe transformation (register-trans2.xsl) wird übernommen -->
    <!-- den einzelnen registern werden überschriften und gegebenenfalls vorbemerkungen aus den textbausteinen zugewiesen  -->
    <!-- die doubletten bei den referenzen auf artikelnummern werden entfernt -->
    <!-- einträge, die weder referenzen auf artikel, verweise oder untereinträge aufweisen, werden entfernt -->

    <!-- achtung: in den verweisen erfolgt das abschneiden der ergänzungen in klammern
          im template verweise_reduzieren unter log3="vl1" -->


    <xsl:template match="indices">
        <!-- zuerst wird geprüft, ob im projekt ein eigener titel vorgesehen ist,
        wenn ja wird er von dort ausgelesen,
        wenn nicht wird er hier erzeugt-->
        <indices>
            <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- gesamttitel zum register -->
            <title>
                <xsl:choose>
                    <xsl:when test="title">
                        <xsl:value-of select="title"/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:text>Register</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </title>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- vorbemerkung kopieren -->
            <xsl:for-each select="note/content">
                <note log3="note1"><xsl:copy-of select="@*"/><xsl:apply-templates/></note>
            </xsl:for-each>

            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- die register werden aufgerufen und sortiert -->
            <!-- das register mit den maßangaben wird nicht mehr benötigt -->
            <xsl:for-each select="index[not(@propertytype='literature')]">
                <xsl:apply-templates select="."/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
            <xsl:if test="$sw_quellen-literatur=1">
                <xsl:apply-templates select="index[@propertytype='literature']"></xsl:apply-templates>
            </xsl:if>
        </indices>
    </xsl:template>

    <!-- münchener reihe: tabellarische übersicht der standorte -->
    <xsl:template match="locations">
        <xsl:copy-of select="."></xsl:copy-of>
    </xsl:template>

    <!-- leere, nur mit § gekennzeichnete absätze werden ausgesondert -->
    <xsl:template match="p[text()='§'][ancestor::note]"><!-- wird unterdrückt --></xsl:template>


    <!-- das einzelne register -->
    <!--  <xsl:template match="index">
        <xsl:choose>
          <!-\- wappen- und markenregister: an spezielles template verweisen -\->
          <xsl:when test="@propertytype='heraldry'"><xsl:call-template name="heraldry-brands"/></xsl:when>
          <!-\- die anderen register: alle an dasselbe template verweisen -\->
          <xsl:otherwise><xsl:call-template name="indexes"/></xsl:otherwise>
        </xsl:choose>
      </xsl:template>-->


    <!-- register formatieren -->
    <xsl:template match="index">
        <xsl:choose>
            <!-- wappen- und markenregister: an spezielles template verweisen -->
            <xsl:when test="@type='heraldry'"><xsl:call-template name="heraldry-brands" /></xsl:when>

            <!-- die anderen register: alle an dasselbe template verweisen -->
            <xsl:otherwise>
                <index>
                    <!-- die attribute werden übernommen -->
                    <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <!-- register-nummer -->
                    <xsl:choose>
                        <xsl:when test="nr">
                            <nr><xsl:value-of select="nr"/></nr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:when>
                        <xsl:otherwise>
                            <nr><xsl:value-of select="@nr"/></nr><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>

                    <!-- register-name -->
                    <xsl:choose>
                        <xsl:when test="title">
                            <title><xsl:value-of select="title"/></title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:when>
                        <xsl:otherwise>
                            <title><xsl:value-of select="@titel"/></title><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>

                    <!-- vorbemerkung -->
                    <xsl:for-each select="note/content">
                        <note log3="note2"><xsl:copy-of select="@*"/><xsl:apply-templates/></note>
                    </xsl:for-each>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

                    <!-- nach register-typen verarbeiten  -->
                    <xsl:choose>

                        <xsl:when test="@type='fonttypes' or @propertytype='fonttypes'">
                            <xsl:call-template name="fonttypes"/>
                        </xsl:when>
                        <xsl:when test="@type='heraldry' or @propertytype='heraldry'">
                            <xsl:call-template name="heraldry-brands"/>
                        </xsl:when>
                        <xsl:when test="@type='blazons'">
                            <xsl:call-template name="blazons"/>
                        </xsl:when>
                        <xsl:when test="@type='writing'">
                            <xsl:call-template name="writing"/>
                        </xsl:when>

                        <xsl:when test="@type='scriptfeatures'">
                            <xsl:for-each select="item">
                                <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                                <xsl:apply-templates select="." />
                            </xsl:for-each>
                        </xsl:when>

                        <xsl:when test="@type='subjects'">
                            <!-- das sachregister muss erneut alphabetisch sortiert werden, weil einträge aus anderen register zugespielt wurden -->
                            <!-- der eintrag "Schrift"  wird hier unterdrückt weil er im template writing in das schriftregister 9b übertragen wird -->
                            <xsl:for-each select="item[not(@groupname='Schrift') or lemma='Schrift']">
                                <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                                <item><xsl:copy-of select="@*"/><xsl:apply-templates /></item>
                            </xsl:for-each>
                        </xsl:when>

                        <xsl:otherwise>
                            <xsl:apply-templates />
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </index>
            </xsl:otherwise>
        </xsl:choose>

    </xsl:template>

    <!-- das schriftregister (9b schriftausführung) wird aus den registern schrifttechnik und worttrenner
          sowie dem eintrag schrift im sachregister gebildet -->
    <!--
    die register schrifttechnik und worttrenner werden bereits im vorfeld im bandartikel aufgerufen,
    der eintrag schrift muss noch aus dem sachregister übernommen werden
     -->
    <xsl:template name="writing">
        <!-- die komponenten werden zunächst in einem parameter gesammelt, damit man sie in einem zweiten schritt sortieren kann -->
        <xsl:param name="p_schrift1">
            <!-- schrifttechnik und worttrenner sind bereits vorhanden und werden an die zutreffenden templates verwiesen -->
            <xsl:apply-templates/>
            <!-- die schriftausführung wird aus dem sachregister geholt und dann ebenfalls an die templates verwiesen-->
            <xsl:for-each select="../index[@type='subjects']/item[@groupname='Schrift' or lemma='Schrift']">
                <xsl:apply-templates/>
            </xsl:for-each>
        </xsl:param>

        <!--<parametertest_schrift><xsl:copy-of select="$p_schrift1"/></parametertest_schrift>-->

        <!-- der parameter wird aufgerufen, die einträge werden sortiert und kopiert -->
        <xsl:for-each select="$p_schrift1/item">
            <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
            <xsl:copy-of select="."/>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="blazons">
        <!-- die einträge müssen neu sortiert werden -->
        <xsl:for-each select="item">
            <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
            <item log3="e1">
                <xsl:copy-of select="@*"/>
                <xsl:apply-templates />
            </item>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="fonttypes">
        <xsl:for-each select="item">
            <xsl:choose>
                <!-- verweise kopieren-->
                <xsl:when test="not(item)">
                    <item log3="e2">
                        <xsl:copy-of select="@*"/>
                        <xsl:apply-templates/>
                    </item>
                </xsl:when>
                <!-- einträge spezifizieren -->
                <xsl:otherwise>
                    <item log3="e3">
                        <xsl:copy-of select="@*"/>
                        <xsl:apply-templates select="lemma"/>
                        <xsl:apply-templates select="sections"/>
                        <xsl:call-template name="sub-fonttypes">
                            <xsl:with-param name="articles" select="ancestor::book/articles" />
                        </xsl:call-template>
                    </item>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>

    <!-- Versalien und Versal zusammenziehen -->
    <xsl:template name="sub-fonttypes">
        <xsl:param name="articles" />
        
        <!-- Step 1: add normalized lemma attribute -->
        <xsl:variable name="items" as="element()*">
            <xsl:for-each select="item">
                <xsl:copy>
                    <xsl:copy-of select="@*" />
                    <xsl:attribute name="normalized" select="replace(replace(string(lemma), 'ersalien', 'ersal'), 'ersal', 'ersalien')" />
                    <xsl:copy-of select="node()" />
                </xsl:copy>
            </xsl:for-each>
        </xsl:variable>
       
       
        <!-- Step 2: output only the last (because plural should be sorted later) normalized lemma-->        
        <xsl:for-each select="$items">
            <xsl:variable name="currentItem" select="@normalized" />            
            <xsl:variable name="sameItems" select="$items[@normalized = $currentItem]" />

            <!-- Keep items with children, because the template does not merge recursively yet -->
            <xsl:if test="item or (index-of($sameItems, .)[1] = count($sameItems))">                                
                <item log3="e-merged">
                    <!-- Output lemma -->
                    <!--xsl:copy-of select="@*[name() != 'normalized']" /-->
                    <xsl:copy-of select="@*" />
                    <xsl:if test="lemma">
                        <lemma>
                            <xsl:copy-of select="lemma/@*" />
                            <xsl:value-of select="lemma" />
                        </lemma>
                    </xsl:if>
                
                    <!-- Collect all sections from items that normalize to the same lemma -->                                
                    <nrn>
                        <xsl:choose>
                            <!-- Keep items with children, because the template does not merge recursively yet -->
                            <xsl:when test="item">
                                <xsl:call-template name="nrn-versalien">
                                    <xsl:with-param name="articles" select="$articles" />
                                    <xsl:with-param name="sections" select="$items[@normalized = $currentItem]/sections/section | sections/section" />
                                </xsl:call-template>                                
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:call-template name="nrn-versalien">
                                    <xsl:with-param name="articles" select="$articles" />
                                    <xsl:with-param name="sections" select="$items[@normalized = $currentItem]/sections/section" />
                                </xsl:call-template>
                            </xsl:otherwise>
                        </xsl:choose>
                    </nrn>
                    
                    <!-- Recursively process remaining items -->
                    <xsl:call-template name="sub-fonttypes">
                        <xsl:with-param name="articles" select="$articles" />
                    </xsl:call-template>
                </item>
            </xsl:if>
        </xsl:for-each>
    </xsl:template>

    <!-- falls titel und notiz schon angelegt wurden, werden sie ignoriert -->
    <xsl:template match="title[parent::index]"></xsl:template>
    <xsl:template match="note[parent::index]"></xsl:template>

    <xsl:template match="item[ancestor::index]">
        <xsl:choose>

            <!-- kursive einträge im standorteregister -->
            <xsl:when test="@before='1'">
                <item_italic>
                    <xsl:copy-of select="@*"/>
                    <xsl:apply-templates/>
                </item_italic>
            </xsl:when>

            <!-- gruppeneinträge (ohne referenzen auf artikelnummern) -->
            <xsl:when test="@type='group'">
                <group>
                    <xsl:copy-of select="@*"/>
                    <xsl:apply-templates/>
                </group>
            </xsl:when>

            <!-- gewöhnliche einträge -->
            <xsl:otherwise>
                <xsl:if test=".//section or .//crossRef">
                    <item log3="e9">
                        <xsl:copy-of select="@*"/>
                        <xsl:apply-templates/>
                    </item>
                </xsl:if>
            </xsl:otherwise>

        </xsl:choose>
    </xsl:template>


    <!-- lemmata der einträge -->
    <xsl:template match="lemma">
        <lemma><xsl:copy-of select="@*"/><xsl:apply-templates /></lemma>
    </xsl:template>

    <!-- die folgenden templates sammeln die referenzen auf katalogartikel,
          spielen die laufnummer des jeweils referenzierten artikels ein
          und sortieren die referenzen nummerisch gemäß den laufnummern -->

    <!-- sammel-referenzen (element <sections>) auf artikelnummern -->
    <xsl:template match="sections[parent::item]">
        <!-- in einem parameter die einzelnen referenzen sammeln und
              zum nummerieren gemäß der laufnummer im katalog an template verweisen-->
        <xsl:param name="p_links-nrn">
            <xsl:for-each select="section[not(@articles_id=preceding-sibling::section/@articles_id)]">
                <xsl:variable name="v_articles_id" select="@articles_id" />
                <xsl:variable name="v_section_id" select="@id" />
                <nr target-article="{$v_articles_id}" target-section="{$v_section_id}">
                    <xsl:copy-of select="@lost" />
                    <xsl:attribute name="sort" select="ancestor::book/articles/article[@id=$v_articles_id]/@article_sort" />
                    <xsl:value-of select="ancestor::book/articles/article[@id=$v_articles_id]/@nr"/>
                </nr>
            </xsl:for-each>
        </xsl:param>
        
        <!-- container für die referenzen anlegen -->
        <nrn log3="nr1">
            <!-- einzelne referenzen aus dem parameter auslesen und sortieren -->
            <xsl:for-each select="$p_links-nrn/nr">
                <xsl:sort select="@sort" data-type="number"/>
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </nrn>
    </xsl:template>

    <!-- einzel-referenzen (element <section>) auf artikelnummer -->
    <xsl:template match="section[ancestor::item]">
        <nr log3="nr1">
            <xsl:copy-of select="@*"/>
            <!-- den betreffenden artikel im katalog ansteuern und die laufnummer auslesen -->
            <xsl:for-each select="ancestor::book/articles/article[@id=current()/@articles_id]">
                <!--<xsl:value-of select="@nr"/>--><xsl:number count="article" from="articles"/>
            </xsl:for-each>
        </nr>
    </xsl:template>

    <!-- verweise -->
    <!-- das abschneiden der verweise nach leerstelle erfolgt im template reduct-crossRefs unter log3="vl1" -->
    <xsl:template match="crossRefs">
        <xsl:param name="p_base-location" select="ancestor::book/project/lemma" />
        <xsl:param name="source_item_level" select="number(parent::item/@level)"/>

        <xsl:param name="p_crossRefs">
            <xsl:choose>
                <!-- personenregister -->
                <xsl:when test="ancestor::index[@propertytype='personnames' or @type='personnames']">
                    <xsl:for-each select="crossRef">
                        <xsl:choose>
                            <!-- Verweis von Level 0 auf tiefere Level (Level 1 des Ziels wird als Unterlemma ergänzt, auf Level 0 des Ziels wird verwiesen) -->
                            <!-- bei verweisen auf ehefrauen wird zuerst der vorname als eintrag/lemma angelegt -->
                            <!-- bzw. allgemeiner: wenn das Ziel auf tieferer Ebene ist als die Quelle, wird ein untereintrag mit inhalt des zieleintrags erzeugt -->
                            <xsl:when test="crossRef[not(@parent_id=ancestor::item/@id)] and ($source_item_level = 0)">
                                <xsl:for-each select="crossRef">

                                    <item log3="e10">
                                        <xsl:copy-of select="@id"/>
                                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        <xsl:call-template name="shortenlemma"><xsl:with-param name="lemma" select="lemma" /></xsl:call-template>                                      
                                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        
                                        <!-- nun wird der verweis auf den nachnamen erzeugt -->
                                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        <crossRef>
                                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                            <xsl:for-each select="parent::crossRef">
                                                <see log3="see1">
                                                    <xsl:copy-of select="@*"/>
                                                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                                    <xsl:call-template name="shortenlemma" >
                                                        <xsl:with-param name="lemma" select="lemma" />
                                                    </xsl:call-template>                                                  
                                                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                                </see>
                                                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                            </xsl:for-each>
                                        </crossRef>
                                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    </item>
                                </xsl:for-each>
                            </xsl:when>

                            <!-- verweise bei namensvarianten einer person innerhalb derselben familie, d. h. von level 1 auf level 1 -->
                            <xsl:when test="crossRef[@parent_id=ancestor::item/@id] and ($source_item_level = 1)">
                                <crossRefs>
                                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <see>
                                        <xsl:copy-of select="crossRef/@*"/>
                                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        <xsl:copy-of select="crossRef/lemma"/>
                                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    </see>
                                </crossRefs>
                            </xsl:when>

                            <!-- von level 1 auf level 0 und weiter auf level 1 -->
                            <xsl:when test="crossRef[not(@parent_id=ancestor::item/@id)] and ($source_item_level = 1)">
                                <crossRefs>
                                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <see>
                                        <xsl:copy-of select="crossRef/@*"/>
                                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        <xsl:call-template name="shortenlemma">
                                            <xsl:with-param name="lemma" select="lemma" />
                                            <xsl:with-param name="crossRef" select="crossRef" />
                                        </xsl:call-template>
                                    </see>
                                </crossRefs>
                            </xsl:when>

                            <!-- Level 0 zu Level 0 oder Level 1 zu Level 1 oder Level 1 zu Level 0 (auf Level 0 des Ziellemmas wird verwiesen) -->
                            <!-- wenn es keine unterverweise gibt -->
                            <xsl:otherwise>
                                <crossRefs log3="v1">
                                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <see log3="see2">
                                        <xsl:copy-of select="@*"/>
                                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                        
                                        <xsl:call-template name="shortenlemma">
                                            <xsl:with-param name="lemma" select="lemma" />
                                            <xsl:with-param name="allAttributes" select="true()" />
                                        </xsl:call-template>
                                       
                                    </see>
                                </crossRefs>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>
                </xsl:when>
                <!-- ende personenregister -->

                <!-- standorteregister-->
                <xsl:when test="ancestor::index[@propertytype='locations' or @type='locations']">
                    <crossRefs>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:call-template name="reduct-crossRefs">
                            <xsl:with-param name="p_base-location" select="$p_base-location" />
                        </xsl:call-template>
                    </crossRefs>                                      
                </xsl:when>
                <!-- ende standorteregister-->
                
                <!-- die übrigen register -->
                <xsl:otherwise>
                    <crossRefs>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <xsl:call-template name="reduct-crossRefs" />
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </crossRefs>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:param>

        <!-- 
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
          <parametertest_verweise><xsl:copy-of select="$p_crossRefs"/></parametertest_verweise>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        -->

        <!-- doubletten aus parameter entfernen -->
        <xsl:for-each select="$p_crossRefs">

            <xsl:for-each select="crossRefs">
                <crossRefs>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="*[not(@id=preceding-sibling::*/@id)]">
                        <!-- Verweise neu sortieren, weil Oberlemmata zum Beispiel bei Epithetata rausgenommen wurden -->
                        <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                        <xsl:variable name="v_verweis-id" select="@id" />
                        
                        <xsl:element name="{name()}">
                            <xsl:copy-of select="@*"/>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:copy-of select="lemma"/>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:for-each select="see|see_also">
                                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                            <xsl:for-each select="following-sibling::*[@id=$v_verweis-id]">
                                <xsl:copy-of select="see|see_also"/>
                                <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:for-each>
                        </xsl:element>
                        
                    </xsl:for-each>
                </crossRefs>
            </xsl:for-each>

            <xsl:for-each select="item">
                <item log3="e11">
                    <xsl:attribute name="sortstring" select="lemma/@sortstring" />
                    <xsl:copy-of select="@id"/>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:copy-of select="lemma"/>
                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="crossRef">
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        <crossRefs>
                            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:for-each select="*[not(@id=preceding-sibling::*/@id)]">
                                <!-- Verweise neu sortieren, weil Oberlemmata zum Beispiel bei Verweisen von Nachnamen auf Vornamen rausgenommen wurden -->
                                <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />

                                <xsl:variable name="v_verweis-id" select="@id"/>
                                <xsl:element name="{name()}">
                                    <xsl:copy-of select="@*"/>
                                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <xsl:copy-of select="lemma"/>
                                    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    <xsl:for-each select="see|see_also">
                                        <xsl:copy-of select="."/>
                                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    </xsl:for-each>
                                    <xsl:for-each select="following-sibling::*[@id=$v_verweis-id]">
                                        <xsl:copy-of select="see|see_also"/>
                                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                    </xsl:for-each>
                                </xsl:element>
                            </xsl:for-each>
                        </crossRefs>
                    </xsl:for-each>
                </item>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:template>


    <!-- Remove additions of lemma -->
    <xsl:template name="shortenlemma">
        <xsl:param name="lemma" />
        <xsl:param name="allAttributes" select="false()" />
        <xsl:param name="crossRef" />

        <lemma log3="lcr1">
            <xsl:choose>
                <xsl:when test="$allAttributes = true()">
                    <xsl:copy-of select="lemma/@*"/>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:copy-of select="$lemma/@level" />
                    <xsl:copy-of select="$lemma/@sortchart" />
                    <xsl:attribute name="sortstring" select="$lemma/@sortstring" />        
                </xsl:otherwise>
            </xsl:choose>            
            
             <xsl:choose>                 
                 <!-- ergänzungen in klammern zum namen werden unterdrückt -->
                 <xsl:when test="contains($lemma,' (')"><xsl:value-of select="substring-before($lemma,' (')"/></xsl:when>                 
                 <!-- TODO: Es stört in DI 113 Verweise wie "s. Meißen, Bischöfe, Thimo von Colditz". Bislang nur durch geschützte Leerzeichen umgehbar. -->
                 <!--xsl:when test="contains($lemma,', ')"><xsl:value-of select="substring-before($lemma,', ')"/></xsl:when-->
                 <xsl:when test="contains($lemma,'#')"><xsl:value-of select="substring-before($lemma,'#')"/></xsl:when>
                 <xsl:otherwise><xsl:value-of select="$lemma"/></xsl:otherwise>
             </xsl:choose>      
            
            <xsl:if test="$crossRef">
                <xsl:text>, </xsl:text>
                <xsl:apply-templates select="$crossRef/lemma" />               
            </xsl:if>
        </lemma>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
    <!-- den string der verweisziele in einigen registern nach bestimmten zeichen abschneiden -->
    <xsl:template name="reduct-crossRefs">
        <xsl:param name="p_base-location" />
        <xsl:for-each select="crossRef">
            <xsl:choose>
                
                <!-- if the target is a group, skip it -->
                <xsl:when test="@id=ancestor::item[@type='group']/@id">
                    <xsl:call-template name="reduct-crossRefs" />
                </xsl:when>
                
                <xsl:otherwise>
                    <!-- see_also for items linking to articles, see for items that are pure reference items -->
                    <xsl:element name="{if (ancestor::item[1]/sections) then 'see_also' else 'see'}">
                        
                        <xsl:copy-of select="@*"/>
                        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                                                
                        <!-- shorten: personen, orte, standorte -->
                        <xsl:variable name="shorten" select="ancestor::index[(@type='personnames') or (@type='placenames') or (@type='locations')]" />
                        
                        <xsl:choose>
                            
                            <xsl:when test="$shorten">                                
                                <lemma log3="vl1">
                                    <xsl:copy-of select="@*"/>                                
                                    <xsl:choose>
                                        <xsl:when test="contains(lemma,' (')"><xsl:value-of select="substring-before(lemma,' (')"/></xsl:when>
                                        <xsl:otherwise><xsl:value-of select="lemma"/></xsl:otherwise>
                                    </xsl:choose>
                                </lemma><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>                                
                            </xsl:when>
                            
                            <xsl:otherwise>
                                <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            </xsl:otherwise>
                            
                        </xsl:choose>
                     
                        <xsl:call-template name="reduct-crossRefs"></xsl:call-template>
                    </xsl:element><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="heraldry-brands">
        <xsl:param name="p_coa1">
            <!-- wappen, hausmarken und meisterzeichen zusammenführen und referenzen auf artikelnummern erzeugen -->
            <!-- 1. wappen und hausmarken zusammenführen
                  ohne die gruppe der nicht identifizierten wappen-->
            <xsl:for-each select="ancestor::indices/index[@type='heraldry' or  @type='brands' or @propertytype='heraldry' or  @propertytype='brands']/item[not(@type='group')][not(lemma=preceding-sibling::item/lemma)]">
                <item log3="e12">
                    <xsl:copy-of select="@*"/>
                    <xsl:apply-templates select="lemma"/>
                    <xsl:if test="ancestor::index[@propertytype='heraldry' or @type='heraldry']"><xsl:apply-templates select="name"/></xsl:if>
                    <nrn>
                        <!-- 2. referenzen auf artikelnummern suchen -->
                        <xsl:for-each select="ancestor::index[@type='heraldry' or @type='brands' or @propertytype='heraldry' or  @propertytype='brands']/item[lemma=current()/lemma]//section">
                            <nr>
                                <xsl:copy-of select="@*"/>
                                <xsl:attribute name="sort" select="ancestor::book/articles/article[@id=current()/@articles_id]/@article_sort" />
                                <xsl:value-of select="ancestor::book/articles/article[@id=current()/@articles_id]/@nr" />
                            </nr>
                        </xsl:for-each>
                    </nrn>
                </item>
            </xsl:for-each>
        </xsl:param>

        <xsl:param name="p_coa2">
            <!-- doubletten bei den lemmata, die sich aus dem zusammenführen von marken und wappen ergeben haben, entfernen -->
            <xsl:for-each select="$p_coa1/item[not(lemma=preceding-sibling::item/lemma)]">
                <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                <item log3="e13">
                    <xsl:copy-of select="@*"/>
                    <xsl:copy-of select="lemma"/>
                    <nrn>
                        <xsl:for-each select="nrn/nr[not(text()=preceding-sibling::nr/text())]">
                            <xsl:copy-of select="."/>
                        </xsl:for-each>
                        <xsl:for-each select="following-sibling::item[lemma=current()/lemma]/nrn/nr">
                            <xsl:copy-of select="."/>
                        </xsl:for-each>
                    </nrn>
                </item>
            </xsl:for-each>
        </xsl:param>
        
        <xsl:param name="p_coa3">
            <!-- doubletten bei den referenzen auf artikelnummern entfernen -->
            <xsl:for-each select="$p_coa2/item">
                <item log3="e14">
                    <xsl:copy-of select="@*"/>
                    <xsl:copy-of select="lemma"/>
                    <nrn>
                        <xsl:for-each select="nrn/nr[not(text()=preceding-sibling::nr/text())]">
                            <xsl:sort select="@sort" data-type="number"/>
                            <xsl:copy-of select="."/>
                        </xsl:for-each>
                    </nrn>
                </item>
            </xsl:for-each>
        </xsl:param>


        <!--    <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
           <wappen1><xsl:copy-of select="$p_coa1"/></wappen1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <wappen2><xsl:copy-of select="$p_coa2"/></wappen2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <wappen3><xsl:copy-of select="$p_coa3"/></wappen3><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
         -->

        <index>
            <xsl:copy-of select="@*"/>
            <nr><xsl:value-of select="@nr"/></nr>
            <title> <xsl:value-of select="@title"/></title>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="note/content">
                <note log3="note3"><xsl:copy-of select="@*"/><xsl:apply-templates/></note>
            </xsl:for-each>
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- zuerst einzel-einträge auswählen -->
            <xsl:for-each select="$p_coa3/item">
                <xsl:copy-of select="."/>
            </xsl:for-each>

            <!-- danach gruppen (=sammel-einträge für nicht identifiziert wappen, marken, meisterzeichen) anhängen -->
            <xsl:for-each select="ancestor::indices/index[@propertytype='heraldry' or @propertytype='brands' or @type='heraldry' or @type='brands']/item[@type='group']">
                <!-- es werden nur die gruppen angesteuert, die referenzen auf katalogartikel enthalten -->
                <xsl:if test=".//sections/section">
                    <xsl:choose>
                        <xsl:when test="item[@type='group']">
                            <group>
                                <xsl:copy-of select="lemma"/>
                                <xsl:for-each select="item[@type='group'][item]">
                                    <xsl:for-each select="item"><item log3="e15"><xsl:copy-of select="@*"/><xsl:apply-templates></xsl:apply-templates></item></xsl:for-each>
                                </xsl:for-each>
                            </group>
                        </xsl:when>
                        <xsl:otherwise><xsl:call-template name="nrn-group-coa"></xsl:call-template></xsl:otherwise>
                    </xsl:choose>
                </xsl:if>
            </xsl:for-each>
        </index>
    </xsl:template>


    <xsl:template name="nrn-group-coa">
        
        <!-- referenzen auf artikelnummern erzeugen -->
        <xsl:param name="p_nrn-group-coa1">        
            <xsl:for-each select="sections/section">
                <nr>
                    <xsl:copy-of select="@*"/>
                    <xsl:attribute name="sort" select="ancestor::book/articles/article[@id=current()/@articles_id]/@article_sort" />
                    <xsl:value-of select="ancestor::book/articles/article[@id=current()/@articles_id]/@nr" />
                </nr>
            </xsl:for-each>
        </xsl:param>
        
        <!-- doubletten entfernen und sortieren -->
        <xsl:param name="p_nrn-group-coa2">
            <xsl:for-each select="$p_nrn-group-coa1/nr[not(text()=preceding-sibling::nr/text())]">
                <xsl:sort select="@sort" data-type="number" />
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </xsl:param>

        <group log3="g1">
            <xsl:copy-of select="lemma"/>
            <nrn>
                <xsl:for-each select="$p_nrn-group-coa2/nr">
                    <xsl:sort select="@sort" data-type="number" />
                    <xsl:copy-of select="."/>
                </xsl:for-each>
            </nrn>
        </group>
    </xsl:template>

    <!-- referenzen auf artikelnummern im schriftartenregister -->
    <xsl:template name="nrn-versalien">
        <xsl:param name="sections" />
        <xsl:param name="articles" />

        <!-- den betreffenden artikel im katalog ansteuern und die laufnummer auslesen -->
        <xsl:param name="p_nrn-versalien">
            <xsl:for-each select="$sections">
                <xsl:variable name="v_articles_id" select="@articles_id" />
                <xsl:variable name="v_section_id" select="@id" />
                <nr target-article="{$v_articles_id}" target-section="{$v_section_id}">
                    <xsl:attribute name="sort" select="$articles/article[@id=current()/@articles_id]/@article_sort" />
                    <xsl:value-of select="$articles/article[@id=current()/@articles_id]/@nr" />
                </nr>
            </xsl:for-each>
        </xsl:param>

        <!-- doubletten entfernen und sortieren -->
        <xsl:for-each select="$p_nrn-versalien/nr[not(text()=preceding-sibling::nr/text())]">
            <xsl:sort select="@sort" data-type="number" />
            <xsl:copy-of select="."/>
        </xsl:for-each>
    </xsl:template>

    <xsl:template match="shield"><shield><xsl:apply-templates /></shield></xsl:template>
    <!-- oberwappen wird unterdrückt -->
    <xsl:template match="crest"><!--<crest><xsl:apply-templates></xsl:apply-templates></crest>--></xsl:template>
    <xsl:template match="biblio"><biblio><xsl:apply-templates></xsl:apply-templates></biblio></xsl:template>
    <xsl:template match="nr"></xsl:template>

</xsl:stylesheet>