<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"
    xmlns:v="urn:schemas-microsoft-com:vml" 
    xmlns:w10="urn:schemas-microsoft-com:office:word" 
    xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core" 

    xmlns:o="urn:schemas-microsoft-com:office:office" 
    xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" 
    xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2" 
    xmlns:msxsl="urn:schemas-microsoft-com:xslt" 
    xmlns:php="http://php.net/xsl" 
    >
    
    <xsl:import href="../commons/di-switch.xsl"/>
    <xsl:import href="di-docx-styles.xsl"/>
    <xsl:import href="di-docx-commons.xsl"/>

<!-- dieses stylesheet transformiert und formatiert die register -->
    
    <xsl:template match="indices">
        <xsl:comment>indices anfang</xsl:comment> 
        <!-- ausabeoptionen abfragen -->
        <xsl:if test="$sw_register=1">

                <!-- gegebenenfalls linke leerseite einfügen -->
            <xsl:if test="preceding-sibling::*"><xsl:copy-of select="$p_oddPage"/></xsl:if>

                
                    <!-- linke leerseite einfügen -->
                    <xsl:copy-of select="$p_oddPage"/>
                    <!-- titelseite -->
                 <w:p>
                    <w:pPr>
                        <w:pStyle w:val="epi-ueberschrift-1"/>
                    </w:pPr>
                    <!-- sprungmarke öffnen und titel auslesen -->
                     <w:bookmarkStart>
                         <xsl:attribute name="w:id">15<xsl:value-of select="tokenize(@id, '-')[last()]"/></xsl:attribute>
                        <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                    </w:bookmarkStart>
                        <!-- titel einfügen -->
                    <w:r>
                        <xsl:choose>
                            <xsl:when test="title"><w:t><xsl:value-of select="title"/></w:t></xsl:when>
                            <xsl:otherwise><w:t>Register</w:t></xsl:otherwise>
                        </xsl:choose>
                    </w:r>
                    <!-- sprungmarke schließen -->
                     <w:bookmarkEnd>
                         <xsl:attribute name="w:id">15<xsl:value-of select="tokenize(@id, '-')[last()]"/></xsl:attribute>
                    </w:bookmarkEnd> 
                 </w:p>
                
                <!-- anmerkung zu den registern -->
                
                    <!-- auf anmerkung prüfen -->
                    <xsl:if test="note[p]">
                    <!-- anmerkung ansteuern und auslesen-->
                       <xsl:for-each select="note/p">
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi-normal-1"/>
                            </w:pPr>
                            <w:r>
                                <w:t><xsl:value-of select="."/></w:t>
                            </w:r>
                        </w:p>
                       </xsl:for-each>
                        <!-- leerzeile einfügen -->
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi-leerzeile-10pt"/>
                            </w:pPr>
                        </w:p>
                    </xsl:if>
                    <!-- inhaltsverzeichnis zu den registern aufrufen -->
                    <xsl:call-template name="register-uebersicht"></xsl:call-template>
                    <!-- </xsl:if> -->
                
                
                <!-- einzelregister ansteuern und an templates verweisen;
                        register der marken auslassen
                -->
                <xsl:for-each select="index[not(@type='brands')]">
                    <xsl:apply-templates select="."/>
                </xsl:for-each>
            
            <!-- seitenformat auf einspaltig zurücksetzen -->
              
                   <w:p>
                       <w:pPr>
                           <w:sectPr>
                               <w:type w:val="continuous"/>
                               <w:pgSz w:w="11906" w:h="16838"/>
                               <w:pgSz w:w="11906" w:h="16838" />
                               <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                               <!-- einspaltig -->
                               <w:cols w:num="1" w:space="708"/>
                               <w:docGrid w:linePitch="360"/>
                           </w:sectPr>
                       </w:pPr>
                   </w:p>                  
              
        </xsl:if>
        <xsl:comment>indices ende</xsl:comment> 
    </xsl:template>
    
    <xsl:template name="register-uebersicht">
        <!-- inhaltsverzeichnis des registers;
        mit links auf die sprungmarken der einzelregister und feldfunktion für die ermittlung der seitenzahlen-->
        <xsl:for-each select="index[not(@type='brands')]">
           <w:p>
               <w:pPr>
                   <w:pStyle w:val="epi-inhalt-2"/>
               </w:pPr>

               <!-- verweise auf einzelregister werden als links formatiert;
               das attribut w:bookmark enthält als sprungmarke die @section-id des zielregisters aus dem bandartikel-->
               <w:hlink>
                   <xsl:attribute name="w:bookmark"><xsl:value-of select="@section-id"/></xsl:attribute>
                   <!-- registernummer auslesen, mit punkt abschließen -->
                   <w:r>
                       <!-- nummer des registers auslesen -->
                       <xsl:choose>
                           <xsl:when test="nr/node()"><w:t><xsl:value-of select="nr"/>. </w:t></xsl:when>
                           <xsl:otherwise><w:t><xsl:value-of select="@nr"/>. </w:t></xsl:otherwise>
                       </xsl:choose>
                       
                   </w:r>
                   <!-- tab linksbündig setzen -->
                   <w:r>
                       <w:tab/>
                   </w:r>
                   <!-- titel auslesen -->
                   <w:r>
                       <xsl:choose>
                           <xsl:when test="title/node()"><w:t><xsl:value-of select="title"/>&#x00A0;</w:t></xsl:when>
                           <xsl:otherwise><w:t><xsl:value-of select="concat(@snr,'. ',@title)"/>&#x00A0;</w:t></xsl:otherwise>
                       </xsl:choose>
                   </w:r>
                   <!-- tab mit führenden punkten setzen, dann  rechtsbündig -->
                   <w:r>
                       <w:rPr>
                           <w:spacing w:val="40"/>
                           <w:sz w:val="18"/>
                       </w:rPr>
                       <w:tab/>
                   </w:r>
                   <w:r>
                       <w:tab/>
                   </w:r>                   
                   <!-- feldfunktion seitenzahl und ? als platzhalter setzen -->
                   <w:r>
                       <w:fldChar w:fldCharType="begin">
                           <w:fldData xml:space="preserve">CNDJ6nn5us4RjIIAqgBLqQsCAAAACAAAAA4AAABfAFQAbwBjADUAMAAzADEANgA4ADgAMQA5AAAA</w:fldData>
                       </w:fldChar>
                   </w:r>
                   <w:r>
                       <!-- seitenzahl der registerüberschrift ermitteln; als referenz dient komplementär zu den Sprungmarken die @section-id  -->
                       <w:instrText>PAGEREF <xsl:value-of select="@section-id"/> \h</w:instrText>
                   </w:r>
                   <!-- führende punkte -->
                   <w:r>
                       <w:fldChar w:fldCharType="separate"/>
                   </w:r>
                   <!-- platzhalter für die seitenzahl; 
                       die zahl wird im erzeugten dokument mit Strg+A und F9 generiert bzw. aktualisiert -->
                   <w:r>
                       <w:t>?</w:t>
                   </w:r>
                   <w:r>
                       <w:fldChar w:fldCharType="end"/>
                   </w:r>
               </w:hlink>
           </w:p> 
        </xsl:for-each>
    </xsl:template>

    <!-- einzelregister -->
    <xsl:template  match="index">
        <xsl:param name="p_root" select="/">
        <!-- den gesamten band zur späteren verwendung einzelner instanzen 
            (in den hierauf folgenden parametern) zwischenspeichern -->            
        </xsl:param> 
       
        <!-- weitere parameter:
            in dem zuerst folgenden parameter werden einige register umstrukturiert, 
            im zweiten parameter werden einzeln stehende unterlemmata an das hauptlemma herangezogen -->


        <xsl:param name="p_index-0">
                <xsl:choose>
                    <!-- im personenregister müssen die einträge der zweiten ebene neu sortiert werden -->
                    <!-- TODO: Das passiert bereits in di-trans3-indices. Warum hier nochmal? -->
                    <xsl:when test="@propertytype='personnames' or @type='personnames'">
                        <index>
                            <xsl:copy-of select="@*"/>
                            <xsl:for-each select="*">
                                <xsl:choose>
                                    <xsl:when test="name()='item'">
                                        <item>

                                            <xsl:copy-of select="@*"/>
                                            <xsl:copy-of select="lemma"/>
                                            <xsl:copy-of select="nrn"/>
                                            <xsl:copy-of select="crossRefs"/>
                                            <xsl:for-each select="item">
                                                <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                                                <xsl:copy-of select="."/>
                                            </xsl:for-each>                                
                                        </item>                                        
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:copy-of select="."/>
                                    </xsl:otherwise>
                                </xsl:choose>

                            </xsl:for-each>
                        </index>
                    </xsl:when>
                    <!-- im sachregister müssen die einträge der ersten ebene neu sortiert werden,
                         desgleichen im register schriftausführung
                    -->
                    <xsl:when test="@propertytype='subjects' or @type='subjects' or @propertytype='scriptfeatures' or @type='scriptfeatures'">
                            <index>
                                <xsl:copy-of select="@*"/>
                                <xsl:copy-of select="nr"/>
                                <xsl:copy-of select="title"/>
                                <xsl:copy-of select="note"/>  
                                <xsl:for-each select="item">
                                    <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                                    <xsl:copy-of select="."/>
                                </xsl:for-each>
                            </index>                        
                    </xsl:when>
                    <xsl:when test="@propertytype='blazons'">
                        <!-- register wappenblasonierungen
                                neu anlegen und den band aus dem parameter mitgeben
                        -->
                            <index>
                                <xsl:copy-of select="@*"/>
                                <xsl:copy-of select="nr"/>
                                <xsl:copy-of select="title"/>
                                <xsl:copy-of select="note"/>  
                                <xsl:for-each select="item">
                                    <xsl:sort select="name/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                                    
                                    <xsl:copy>
                                        <xsl:copy-of select="@*"/>
                                        <xsl:for-each select="*">
                                            <xsl:copy>
                                                <xsl:copy-of select="@*"/>
                                                <!-- band aus dem parameter mitgeben -->
                                                <xsl:apply-templates><xsl:with-param name="p_root" select="$p_root" /></xsl:apply-templates>
                                            </xsl:copy>
                                        </xsl:for-each>
                                    </xsl:copy>
                                </xsl:for-each>
                            </index>                        
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:copy-of select="."/>
                    </xsl:otherwise>
                </xsl:choose>
        </xsl:param>

        <!-- in folgendem parameter werden einzel stehende unterlemmmata an das hauptlemma herangezogen; 
               die ansprache der einzelnen templates erfolgt über mode="index-param"
               
               je nachdem, ob der ganze band oder nur der katalog erzeugt werden soll,
               werden die elemente für laufnummer und titel des registers kopiert 
               oder aus den entsprechenden attributen erzeugt
        -->
        <xsl:param name="p_index">
            <xsl:for-each select="$p_index-0/index">
            <index>
                <xsl:copy-of select="@*"/>
                <xsl:choose>
                    <!-- element <nr> kopieren oder erzeugen -->
                    <xsl:when test="nr"><xsl:copy-of select="nr"/></xsl:when>
                    <xsl:otherwise><nr><xsl:value-of select="@snr"/></nr></xsl:otherwise>
                </xsl:choose>
                <xsl:choose>
                    <!-- element <titel> kopieren oder erzeugen -->
                    <xsl:when test="title"><xsl:copy-of select="title"/></xsl:when>
                    <xsl:otherwise><nr><xsl:value-of select="@title"/></nr></xsl:otherwise>
                </xsl:choose>
                <xsl:copy-of select="note"/>
                <xsl:apply-templates mode="index-param">
                    <xsl:with-param name="p_root" select="$p_root" />
                </xsl:apply-templates>
            </index></xsl:for-each>
        </xsl:param>
        

<!--         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <test-root><xsl:copy-of select="$p_root"></xsl:copy-of></test-root>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <test-reg1><xsl:copy-of select="$p_index-0"/></test-reg1>
         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <test-reg2><xsl:copy-of select="$p_index"/></test-reg2>
         <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>-->

        
        <!-- anschließend erfolgt die endgültige formatierung aus dem paramter heraus -->
        
            <xsl:for-each select="$p_index/*">
                <xsl:apply-templates><xsl:with-param name="p_root" select="$p_root" /></xsl:apply-templates>
            </xsl:for-each>
            
            <!-- der letzte absatz ethält die angabe zur dreispaltigen darstellung -->
            <xsl:choose>
                <!-- die register wappenbeschreibungen (blasonierungen) und literatur (gibt es nur in der artikelpipeline) bleiben einspaltig -->
                <xsl:when test="@type='blazons' or @propertytype='blazons' or @type='indexgroup'"></xsl:when>
                <xsl:when test="@propertytype='literature'"></xsl:when>
                <!-- alle anderen dreispaltig -->
                <xsl:otherwise>
                    <w:p>
                        <w:pPr>
                            <w:sectPr>
                                <w:type w:val="continuous"/>
                                <w:pgSz w:w="11906" w:h="16838"/>
                                <w:pgSz w:w="11906" w:h="16838" />
                                <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                                <w:cols w:num="3" w:space="708"/>
                                <w:docGrid w:linePitch="360"/>
                            </w:sectPr>
                        </w:pPr>
                    </w:p>                    
                </xsl:otherwise>
            </xsl:choose>
        
    </xsl:template>

<!-- ==================================================== -->
    <!-- ANFANG: vorformatierung -->
    <!-- es folgen die muster für die vorformatierung der register-elemente im mode="index-param", 
         sie werden im parameter p_index aufgerufen 
    -->

    <!-- gruppen mit mehreren einträgen -->
    <xsl:template match="group" mode="index-param">
        <group>
            <xsl:copy-of select="@*"/>
            <xsl:apply-templates mode="index-param"/>
        </group>
    </xsl:template>
    
    <!-- einzeleinträge - nicht kursiv darzustellen -->
    <xsl:template mode="index-param" match="item">
        <xsl:choose>
            <!-- bei einzeln stehenden unterlemmata wird das rahmen-tag item entfernt -->
            <xsl:when test="parent::item[not(crossRefs) and not(nrn/nr)] and 
                not(preceding-sibling::item_italic) and 
                not(following-sibling::item_italic) and
                not(preceding-sibling::item) and
                not(following-sibling::item)
                ">                
                <xsl:apply-templates mode="index-param"/>
            </xsl:when>
            <!-- alle anderen lemmata verbleiben im item-rahmen -->
            <xsl:otherwise>
                <item>
                    <xsl:copy-of select="@*"/>
                    <xsl:apply-templates mode="index-param"/>
                </item>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    <!-- einzeleinträge - kursiv darzustellen -->
    <xsl:template mode="index-param" match="item_italic">
    <!-- bei einzeln stehenden unterlemmata wird das rahmen-tag item oder item_italic entfernt -->
        <xsl:choose>
        <!-- wenn das rahmen-tag item_italic lautet -->
            <xsl:when test="parent::item_italic[not(crossRefs) and not(nrn/nr)] and 
                not(preceding-sibling::item_italic) and 
                not(following-sibling::item_italic) and
                not(preceding-sibling::item) and
                not(following-sibling::item)
                ">
                <xsl:apply-templates mode="index-param"/>
            </xsl:when>
        <!-- wenn das rahmen-tag item lautet -->
            <xsl:when test="parent::item[not(crossRefs) and not(nrn/nr)] and 
                not(preceding-sibling::item_italic) and 
                not(following-sibling::item_italic) and
                not(preceding-sibling::item) and
                not(following-sibling::item)
                ">
                <xsl:apply-templates mode="index-param"/>
            </xsl:when>
            <xsl:otherwise>
                <item_italic>
                    <xsl:copy-of select="@*"/>
                    <xsl:apply-templates mode="index-param"/>
                </item_italic>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>

    <!-- lemmata -->
    <xsl:template match="lemma" mode="index-param">
        <!-- die lemmata werden hier um die id des übergeordneten items ergänzt, 
            um auch für herangezogene lemmata sprungmarken erzeugen zu können -->
        <!-- bei kursiv auszugebenden einträgen wird die kennzeichnung als kursiv auch auf das lemma übertragen, 
                damit bei herangezogenen untereinträgen diese kennzeichnung erhalten bleibt 
                und in der abschließenden transformation berücksichtigt werden kann -->
        <lemma>
            <xsl:copy-of select="@*"/>
            <xsl:copy-of select="parent::*/@id"/>
            <xsl:if test="parent::item_italic"><xsl:attribute name="before">1</xsl:attribute></xsl:if>
            <xsl:value-of select="."/>
        </lemma>
    </xsl:template>
    
    <!-- element <schild> im wappenregister -->
    <xsl:template match="shield" mode="index-param">
        <xsl:copy-of select="."/>
    </xsl:template>
    <xsl:template match="biblio" mode="index-param"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="crest" mode="index-param"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="crossRefs" mode="index-param"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="nrn" mode="index-param"><xsl:copy-of select="."/></xsl:template>
<!-- ENDE: vorformatierung -->

<!-- =========================================================== -->
<!-- muster für die endgültige formatierung -->

    <xsl:template match="index/nr"><!-- noch nicht eingebunden --></xsl:template>
    <xsl:template match="index/nr" mode="index-param"><!-- muss unterdrückt werden --></xsl:template>
    <xsl:template match="index/title" mode="index-param"><!-- muss unterdrückt werden --></xsl:template>
    <!-- register überschrift -->
    <xsl:template match="index/title">
        <!-- sprungmarke für das inhaltsverzeichnis aus dem attribut @section-id erzeugen  -->
        <xsl:param name="p_bookmark">
            <xsl:for-each select="ancestor::index">
                <xsl:value-of select="@section-id"/>
            </xsl:for-each>
        </xsl:param>
        <w:p></w:p>
        <!-- registerüberschrift -->
        <w:p>
            <w:pPr>
                <!--<w:jc w:val="center"/>-->
                <w:pStyle w:val="epi-ueberschrift-2"/>
                <!--<w:outlineLvl w:val="1"/>-->
            </w:pPr>
            <!-- anfang sprungmarke für inhaltsverzeichnis -->
            <w:bookmarkStart>
                <xsl:attribute name="w:id">16<xsl:value-of select="tokenize($p_bookmark, '-')[last()]"/></xsl:attribute>
                <xsl:attribute name="w:name"><xsl:value-of select="$p_bookmark"/></xsl:attribute>                
            </w:bookmarkStart>
            <!-- nummer des registers -->
                <xsl:if test="preceding-sibling::nr[text()]">
                    <xsl:for-each select="preceding-sibling::nr">
                        <w:r>
                            <w:rPr><w:smallCaps w:val="off"/></w:rPr>
                            <w:t><xsl:value-of select="."/></w:t><w:t>. </w:t>
                        </w:r>
                    </xsl:for-each>                    
                </xsl:if>
            <!-- titel des registers -->
            <w:r>
                <!-- formatierung -->
                <w:rPr>
                    <w:smallCaps/>
                </w:rPr>
                <xsl:choose>
                    <!-- wenn der titel aus dem bandartikel genommen wurde, kann er hier einfach ausgelesen werden  -->
                    <xsl:when test="node()">
                        <w:t><xsl:value-of select="."/></w:t>
                    </xsl:when>
                    <!-- wenn der titel nicht aus dem bandartikel kommt, wird er hier direkt erzeugt -->
                    <xsl:otherwise>
                        <w:t><xsl:for-each select="parent::index">
                            <xsl:value-of select="concat(@snr, '. ',@title)"/>
                        </xsl:for-each></w:t>
                    </xsl:otherwise>
                </xsl:choose>
            </w:r>
            <!-- ende der sprungmarke für das inhaltsverzeichnis -->
            <w:bookmarkEnd>
                <xsl:attribute name="w:id">16<xsl:value-of select="tokenize($p_bookmark, '-')[last()]"/></xsl:attribute>
            </w:bookmarkEnd>
        </w:p>
        <xsl:if test="not(parent::*[@type='indexgroup'])">
            <!-- abschnittsformatierung einspaltig -->
            <w:p></w:p>
            <w:p>
                <w:pPr>
                    <w:sectPr>
                        <w:type w:val="continuous"/>
                        <w:pgSz w:w="11906" w:h="16838" />
                        <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                        <!-- der bereich der registerüberschrift erscheint einspaltig -->
                        <w:cols w:num="1" w:space="708"/>
                        <w:docGrid w:linePitch="360"/>
                    </w:sectPr>
                </w:pPr>
            </w:p>
        </xsl:if>
    </xsl:template>

<!-- vorbemerkungen zum register -->
    <xsl:template match="index/note">
        <!-- wenn es eine vorbemerkung gibt, wird sie hier eingefügt -->
        <xsl:if test="node()">
            <xsl:for-each select="p">
                <w:p><xsl:apply-templates></xsl:apply-templates></w:p>
            </xsl:for-each>
            <w:p></w:p>
            <!-- abschnittsformatierung einspaltig -->
            <w:p>
                <w:pPr>
                    <w:sectPr>
                        <w:type w:val="continuous"/>
                        <w:pgSz w:w="11906" w:h="16838" />
                        <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                        <!-- der bereich der vorbemerkung erscheint einspaltig -->
                        <w:cols w:num="1" w:space="708"/>
                        <w:docGrid w:linePitch="360"/>
                    </w:sectPr>
                </w:pPr>
            </w:p>
        </xsl:if>
    </xsl:template>

    <xsl:template match="note" mode="index-param"><!-- wird ignoriert --></xsl:template>

    <!-- gruppe von registereinträgen -->
    <xsl:template match="group" >
        <!-- gruppen die nicht am anfang eines registers stehen, werden durch leerzeile abgesetzt -->
          <xsl:if test="preceding-sibling::group or preceding-sibling::item"><w:p></w:p>/></xsl:if>
          
          <!-- bei gruppen mit eigenem lemma wird für das lemma ein absatz angelegt -->
        <xsl:if test="lemma">
           <w:p>
               <w:pPr><w:pStyle w:val="epi-register-eintrag"/></w:pPr>
              <xsl:apply-templates select="lemma" />
              <xsl:apply-templates select="crossRefs" />
              <xsl:apply-templates select="nrn" />
            </w:p>
         </xsl:if>
        <!-- leerzeile -->
        <xsl:if test="@level='0' and following-sibling::group or following-sibling::item"><w:p><w:pPr><w:pStyle w:val="epi-leerzeile-3pt"/></w:pPr></w:p>/></xsl:if>
        <xsl:apply-templates select="group|item" />       
  </xsl:template>

    <!-- registereinträge -->
    <xsl:template match="item|item_italic" >
        <xsl:param name="p_root" select="/" />
        <!-- die anzahl der spiegelstriche und des einzugs wird über eine 
            hierarchische nummerierung (level=multiple) der einträge und untereinträge ermittelt -->
        <xsl:param name="p_levela"><xsl:number count="item|item_italic" from="index" level="multiple"/></xsl:param>
        <!-- die ziffern werden aus der nummerierung eliminiert, sodass nur noch die punkte übrigbleiben -->
        <xsl:param name="p_levelb"><xsl:value-of select="translate($p_levela, '.1234567890', '.')"/></xsl:param>
        <!-- die anzahl der punkte entspricht der anzahl der spiegelstriche bzw. dem level -->
        <xsl:param name="p_levelc"><xsl:value-of select="string-length($p_levelb)"/></xsl:param>
        <!-- die spiegelstriche werden ensprechend dem level vorformatiert -->
        <xsl:param name="p_leveld">
            <xsl:if test="$p_levelc=1">&#x2013; </xsl:if>
            <xsl:if test="$p_levelc=2">&#x2013; &#x2013; </xsl:if>
            <xsl:if test="$p_levelc=3">&#x2013; &#x2013; &#x2013; </xsl:if>
            <xsl:if test="$p_levelc=4">&#x2013; &#x2013; &#x2013; &#x2013; </xsl:if>
        </xsl:param>
     
      <!-- um die schachtelung der einträge zu beseitigen, werden nur das lemma, nrn und verweise in einen absatz eingefügt;
            untereinträge werden in nachfolgenden absätzen in flacher folge ausgegeben--> 
      
      <!-- leerzeile vor einer gruppe oder nach wechsel des initialbuchstabens -->
        <xsl:if test="preceding-sibling::*[1][name()='group']">
          <w:p>
            <w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr>
          </w:p>
        </xsl:if>
        <xsl:if test="
            ancestor::index[@group='initials'] and 
            preceding-sibling::*[name()='item' or name()='item_italic'] and
            not(parent::item) and 
            not(parent::item_italic) and 
            not(parent::group[parent::index[@type='personnames' or @propertytype='personnames']]) and 
            not(lemma[1]/@sortchart=preceding-sibling::*[1]/lemma[1]/@sortchart)
            ">
          <w:p>
            <w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr>
          </w:p>
        </xsl:if>
        <!-- absatz anlegen -->
        <w:p>
          <w:pPr><w:pStyle w:val="epi-register-eintrag"/></w:pPr>
         <!-- entsprechend der anzahl der spiegelstriche bzw. des levels wird der hängende einzug festgelegt -->
         <xsl:choose>
         <xsl:when test="$p_levelc=1"><w:pPr><w:ind w:left="170" w:hanging="170"/></w:pPr></xsl:when>
         <xsl:when test="$p_levelc=2"><w:pPr><w:ind w:left="312" w:hanging="312"/></w:pPr></xsl:when>
         <xsl:when test="$p_levelc=3"><w:pPr><w:ind w:left="454" w:hanging="454"/></w:pPr></xsl:when>
         <xsl:when test="$p_levelc=3"><w:pPr><w:ind w:left="596" w:hanging="596"/></w:pPr></xsl:when>
             <xsl:otherwise><w:pPr><w:ind w:left="170" w:hanging="170"/></w:pPr></xsl:otherwise>
         </xsl:choose>
         <!-- die spiegelstriche werden vor dem lemma eingefügt,
              danach lemma, artikelnummern und verweise zum einfügen an templates verwiesen
         -->
         <w:r>
             <w:t><xsl:value-of select="$p_leveld"/></w:t>
         </w:r><xsl:apply-templates select="lemma"><xsl:with-param name="p_root" select="$p_root" /></xsl:apply-templates>
         <xsl:apply-templates select="nrn" />
         <xsl:apply-templates select="crossRefs" />
    </w:p>
        <!-- die unterlemmata bekommen separate absätze -->
        <xsl:apply-templates select="item_italic|item" />
  </xsl:template>
    
    <!-- lemma des registereintrags -->
    <xsl:template match="lemma">
       <xsl:param name="p_root" select="/"><!-- der band wird zwischengespeichert --></xsl:param> 
       <!-- die folgenden parameter werden für das register wappenbeschreibungen benötigt,
            sie dienen der feststellung, ob im feld <nachweise> ein abschließender punkt vorhanden ist
       -->
        <!-- 1. lemma mit schild und nachweis zusammenführen, whitespace entfernen -->
        <xsl:param name="p_lemma_string"><xsl:value-of select="normalize-space(concat(.,ancestor::item/shield,ancestor::item/biblio))"/></xsl:param>
        <!-- 2. zeichenzahl ermitteln -->
        <xsl:param name="p_string_laenge_lemma"><xsl:value-of select="string-length($p_lemma_string)"/></xsl:param>
        <!-- 3. das letzte zeichen ermitteln; wenn es sich um den punkt handelt, wird später kein punkt gesetzt  -->
        <xsl:param name="p_lemma_string_last"><xsl:value-of select="substring($p_lemma_string,$p_string_laenge_lemma)"/></xsl:param>
        
        <!-- lemma im element <schild> ignorieren -->
        <xsl:if test="ancestor::item/shield">
        <!--<test_blasonierung><xsl:value-of select="$p_lemma_string_last"/></test_blasonierung>-->            
        </xsl:if>

        <!-- jedes lemma bekommt eine sprungmarke als verweisziel -->
        <!-- anfang: sprungmarke -->
        <w:bookmarkStart>
            <xsl:attribute name="w:id">17<xsl:value-of select="tokenize(@id, '-')[last()]"/></xsl:attribute>
            <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
        </w:bookmarkStart>        
        <w:r>
            <!-- die gruppen-lemmata des formelregisters werden fett formatiert-->
            <xsl:if test="parent::group[@level='0'] and ancestor::index[@type='formulas' or @propertytype='formulas']"><w:rPr><w:smallCaps/></w:rPr></xsl:if>
            <!-- die gruppen-lemmata des quelle-zitate-registers werden fett formatiert-->
            <xsl:if test="parent::group[@level='0'] and ancestor::index[@type='sources' or @propertytype='sources']"><w:rPr><w:smallCaps/></w:rPr></xsl:if>
            <!-- die gruppen-lemmata des literaturregisters werden fett formatiert-->
            <xsl:if test="parent::group and ancestor::index[@type='literature' or @propertytype='literature']"><w:rPr><w:b/></w:rPr></xsl:if>
            <!-- die ehemaligen standorte im standortregister werden kursiv ausgegeben -->
            <xsl:if test="parent::item_italic or @before='1'"><w:rPr><w:i/></w:rPr></xsl:if>
            <!-- bei einem herangezogenen kursiven untereintrag wird auch der hauteintrag kursiv ausgegeben -->
            <xsl:if test="parent::item[lemma[@before='1']]"><w:rPr><w:i/></w:rPr></xsl:if>
            <!-- eingabefehler rot kennzeichnen -->
            <xsl:if test="starts-with(.,'Eingabefehler')"><w:rPr><w:color w:val="#990033"/></w:rPr></xsl:if>
            
            <!-- lemma ausgeben, eventuell vorhandene formatierungen werden über entsprechende templates verarbeitet -->
            <w:t><xsl:apply-templates/></w:t>
            <!-- 
                an das hauptlemma herangezogene unterlemmata werden durch kommata separiert;
                hier besteht die möglichkeit, für bestimmte register auch anders vorzugehen, 
                etwa mittels doppelpunkt oder ohne trennzeichen
            -->            
            <xsl:choose>
            <!-- bei den lateinischen epitheta werden die untereinträge ohne komma herangezogen, bei allen übrigen registern und einträgen mit komma -->
                <xsl:when test="ancestor::index[@type='epithets'] and ancestor::group[starts-with(@groupname,'lat')]"><xsl:if test="following-sibling::lemma"><w:t><xsl:text> </xsl:text></w:t></xsl:if></xsl:when>
                <xsl:otherwise><xsl:if test="following-sibling::lemma"><w:t>, </w:t></xsl:if></xsl:otherwise>
            </xsl:choose>
            
            <!-- für das register wappenbeschreibungen werden die elemente schild und nachweis in das lemma hineingezogen -->
            <!-- parameter p_plaintext=1 bedeutet: textknoten sind schon als runs <w:r> getagt -->
            <xsl:if test="parent::item/shield">
                <w:t>: <xsl:apply-templates select="parent::item/shield"><xsl:with-param name="p_root" select="$p_root" /><xsl:with-param name="p_plaintext" select="1" /></xsl:apply-templates></w:t>
            </xsl:if>
            <xsl:if test="parent::item/biblio">
                <w:t>; <xsl:apply-templates select="parent::item/biblio"><xsl:with-param name="p_root" select="$p_root" /><xsl:with-param name="p_plaintext" select="1" /></xsl:apply-templates></w:t>
            </xsl:if>
            <!-- bei den wappenbeschreibungen wird gegebenenfalls 
                (d. h. wenn nicht schon vorhanden) ein abschließender punkt gesetzt -->
            <xsl:if test="parent::item/shield or parent::item/biblio">
                <xsl:if test="not($p_lemma_string_last='.')">
                    <w:t>.</w:t>
                </xsl:if>
            </xsl:if>
        </w:r>
        <!-- sprungmarke schließen -->
        <w:bookmarkEnd>
            <xsl:attribute name="w:id">17<xsl:value-of select="tokenize(@id, '-')[last()]"/></xsl:attribute>
        </w:bookmarkEnd>
        <!-- ende: sprungmarke -->        
    </xsl:template>
    
    <!-- referenzen auf artikelnummern -->
    <xsl:template match="nrn">
        <!-- nummern auslesen -->
        <w:r><w:t><xsl:text> <!-- breiteres leerzeichen en-space U2002 --></xsl:text></w:t></w:r>
        <!-- die einzelnen nummern ansteuen -->
        <xsl:for-each select="nr">
            <!-- link auf die artikelnummer: anfang -->
            <w:r>
                <w:fldChar w:fldCharType="begin">
                    <!--<w:fldData xml:space="preserve"><xsl:value-of select="generate-id()"/></w:fldData>-->
                </w:fldChar>
            </w:r>
            <w:r>
                <w:instrText>Hyperlink \l "<xsl:value-of select="@articles_id"/>" </w:instrText>
            </w:r>
            <w:r>
                <w:fldChar w:fldCharType="separate"/>
            </w:r>
            <!-- artikelnummer -->
            <w:r>
                <!-- kursivierung von nummern im standorteregister -->
                <xsl:if test="@lost='1'"><w:rPr><w:i/></w:rPr></xsl:if>
                <!-- nummer ausgeben -->
                <w:t><xsl:value-of select="."/></w:t></w:r>
            <w:r>
                <w:fldChar w:fldCharType="end"/>
            </w:r>
            <!-- link auf die artikelnummer: ende -->
            
            <!-- gegebenenfalls kommata setzen -->
            <xsl:if test="following-sibling::nr"><w:r><w:t>, </w:t></w:r></xsl:if>
        </xsl:for-each>
    </xsl:template>

   
    <!-- verweise formatieren -->
    <xsl:template match="crossRefs[*]">
        <!-- zur sortierung werden die verweise zunächst in einen paramter geladen
        zur späteren unterscheidung wird der register-typ als attribut mitgeführt-->
          
        <!-- die verweise werden hierarchisch abgearbeitet -->  
       <xsl:param name="p_verweise1">
         <xsl:call-template name="verweisschleife"></xsl:call-template>
       </xsl:param>

        <!-- doubletten entfernen und sortieren -->
        <xsl:param name="p_verweise2">
            <xsl:for-each select="$p_verweise1/crossRef[not(lemma=preceding-sibling::crossRef/lemma)]">
                <xsl:sort select="lemma/@sortstring" order="ascending" lang="de" case-order="upper-first" />
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </xsl:param>
        
        <!-- verweisausdruck (s. oder s. a.) setzen -->
        <xsl:choose>
            <xsl:when test="preceding-sibling::nrn or preceding-sibling::item or following-sibling::nrn or following-sibling::item">
                <xsl:if test="not(preceding-sibling::crossRefs)">
                <w:r><w:t>, s.&#x00A0;a. </w:t></w:r>
                </xsl:if>
            </xsl:when>
            <xsl:otherwise>
                <xsl:if test="not(preceding-sibling::crossRefs)">
                    <w:r><w:t> s. </w:t></w:r>
                </xsl:if>
            </xsl:otherwise>
        </xsl:choose>
 
<!-- parametertest verweise
<xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<verweise1><xsl:copy-of select="$p_verweise1"/></verweise1><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
<verweise2><xsl:copy-of select="$p_verweise2"/></verweise2><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
-->
  
        <!-- die im parameter vorformatierten verweise ansteuern -->
        <xsl:for-each select="$p_verweise2/crossRef">

            <!-- link auf verweisziel: anfang -->
            <w:r>
                <w:fldChar w:fldCharType="begin">
                    <!--<w:fldData xml:space="preserve"><xsl:value-of select="generate-id()"/></w:fldData>-->
                </w:fldChar>
            </w:r>
            <w:r><w:instrText>Hyperlink \l "<xsl:value-of select="@id"/>" </w:instrText></w:r>
            <w:r>
                <w:fldChar w:fldCharType="separate"/>
            </w:r>
            <!-- lemma = verweisquelle -->            
            <w:r><w:t><xsl:value-of select="lemma"/></w:t></w:r>
            <w:r>
                <w:fldChar w:fldCharType="end"/>
            </w:r>
            <!-- link auf verweisziel: ende -->
            <xsl:if test="following-sibling::*"><w:r><w:t>, </w:t></w:r></xsl:if>
        </xsl:for-each>
        <!-- wenn weitere verweise folgen, ein komma setzen -->
        <xsl:if test="following-sibling::crossRefs"><w:r><w:t>, </w:t></w:r></xsl:if>
    </xsl:template>

    <xsl:template name="verweisschleife">
        <!-- verweise ansteuern -->
        <xsl:for-each select="*[name()='siehe' or name()='siehe_auch']">
            <!-- verweistyp feststellen und speichern -->
            <xsl:variable name="v_verweistyp"><xsl:value-of select="name()"/></xsl:variable>
            
            <xsl:choose>
            <!-- zu verbergende gruppeneinträge im formelregister werden übersprungen-->
                <xsl:when test="@ishidden='1' and ancestor::index[@type='formulas' or @propertytype='formulas']">
                    <!-- die untergeordneten verweise zur verarbeitung in der schleife aufrufen -->
                    <xsl:call-template name="verweisschleife"></xsl:call-template>
                </xsl:when>
                <xsl:otherwise>
                    <!-- element <verweis> mit einem attribut @type anlegen,
                         die anderen attribute und das lemma kopieren
                    -->
                    <verweis type="{$v_verweistyp}">
                        <xsl:copy-of select="@*"/>
                        <xsl:copy-of select="lemma"/>
                        <!-- schleife erneut aufrufen, um weitere unterverweise zu verarbeiten -->
                        <xsl:call-template name="verweisschleife"></xsl:call-template>
                    </verweis>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template> 

    <!-- vorbemerkungen zu den einzelregistern -->
    <xsl:template match="index/note/p/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>

</xsl:stylesheet>