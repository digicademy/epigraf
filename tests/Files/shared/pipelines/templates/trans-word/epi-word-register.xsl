<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml"
    xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:w10="urn:schemas-microsoft-com:office:word"
    xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core"
    xmlns:aml="http://schemas.microsoft.com/aml/2001/core"
    xmlns:wx="http://schemas.microsoft.com/office/word/2003/auxHint"
    xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882"
    xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2"
    xmlns:exsl="http://exslt.org/common"
>

    <xsl:param name="odd-page">
        <w:p>
            <w:pPr>
                <w:sectPr>
                    <w:type w:val="odd-page"/>
                    <w:pgSz w:w="11906" w:h="16838" />
                    <!--<w:pgMar w:top="1701" w:right="1985" w:bottom="2013" w:left="1814" w:header="709" w:footer="709"  w:gutter="0"/>-->
                    <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                    <w:cols w:space="708"/>
                    <w:docGrid w:line-pitch="360"/>
                    <w:hdr w:type="even">
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi-seitenkopf"/>
                            </w:pPr>
                        </w:p>
                    </w:hdr>
                    <w:hdr w:type="odd">
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi-normal-1"/>
                            </w:pPr>
                        </w:p>
                    </w:hdr>
                    <w:ftr w:type="even">
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi-seitenfuss"/>
                            </w:pPr>
                            <w:fldSimple w:instr=" PAGE \* MERGEFORMAT ">
                                <w:r wsp:rsidR="000C002F">
                                    <w:rPr>
                                        <w:noProof/>
                                    </w:rPr>
                                    <w:t>2</w:t>
                                </w:r>
                            </w:fldSimple>
                        </w:p>
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi-seitenfuss"/>
                            </w:pPr>
                        </w:p>
                    </w:ftr>
                    <w:ftr w:type="odd">
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi-seitenfuss"/>
                                <w:jc w:val="right"/>
                            </w:pPr>
                            <w:fldSimple w:instr=" PAGE \* MERGEFORMAT ">
                                <w:r wsp:rsidR="00544CA7">
                                    <w:rPr>
                                        <w:noProof/>
                                    </w:rPr>
                                    <w:t>22</w:t>
                                </w:r>
                            </w:fldSimple>
                        </w:p>
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi-seitenfuss"/>
                            </w:pPr>
                        </w:p>
                    </w:ftr>
                </w:sectPr>
            </w:pPr>
        </w:p>
    </xsl:param>
    <xsl:template match="indices">
        <xsl:comment>indices anfang</xsl:comment>
          <xsl:if test="$register=1">
            <wx:sect>
            <xsl:if test="preceding-sibling::*"><xsl:copy-of select="$odd-page"/></xsl:if>

                <wx:sub-section>
                    <xsl:copy-of select="$odd-page"/>
                <w:p>
                    <w:pPr>
                        <w:pStyle w:val="epi-ueberschrift-1"/>
                    </w:pPr>
            <!-- sprungmarke einfügen und titel auslesen -->
                  <aml:annotation w:type="Word.Bookmark.Start">
                      <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                      <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
                  </aml:annotation>
                    <w:r>
                        <xsl:choose>
                            <xsl:when test="titel"><w:t><xsl:value-of select="titel"/></w:t></xsl:when>
                            <xsl:otherwise><w:t>Register</w:t></xsl:otherwise>
                        </xsl:choose>
                    </w:r>
                  <aml:annotation w:type="Word.Bookmark.End">
                      <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                  </aml:annotation>
               </w:p>
                </wx:sub-section>
                <wx:sub-section>
                    <xsl:if test="notiz[p]">
                    <xsl:for-each select="notiz/p">
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi-normal-1"/>
                            </w:pPr>
                            <w:r>
                                <w:t><xsl:value-of select="."/></w:t>
                            </w:r>
                        </w:p>
                       </xsl:for-each>
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi-leerzeile-10pt"/>
                            </w:pPr>
                        </w:p>
                    </xsl:if>
<!--                    <xsl:if test="notiz[text()]">
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi-normal-1"/>
                            </w:pPr>
                            <w:r>
                                <w:t><xsl:value-of select="notiz"/></w:t>
                            </w:r>
                        </w:p>
                        <w:p>
                            <w:pPr>
                                <w:pStyle w:val="epi-leerzeile-10pt"/>
                            </w:pPr>
                        </w:p>
                    </xsl:if>-->
                <xsl:if test="ancestor::book/options/@text='1'"><xsl:call-template name="register-uebersicht"></xsl:call-template></xsl:if>
                </wx:sub-section>
                <xsl:for-each select="index[not(@type='brands')]">
                    <xsl:apply-templates select="."/>
                </xsl:for-each>
            </wx:sect>
              <wx:sect>
                   <w:p wsp:rsidR="00533250" wsp:rsidRDefault="00533250">
                       <w:pPr>
                           <w:sectPr wsp:rsidR="00533250" wsp:rsidSect="00533250">
                               <w:type w:val="continuous"/>
                               <w:pgSz w:w="11906" w:h="16838"/>
                               <w:pgSz w:w="11906" w:h="16838" />
                               <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                               <w:cols w:num="1" w:space="708"/>
                               <w:docGrid w:line-pitch="360"/>
                           </w:sectPr>
                       </w:pPr>
                   </w:p>
              </wx:sect>
        </xsl:if>
        <xsl:comment>indices ende</xsl:comment>
    </xsl:template>

    <xsl:template name="register-uebersicht">
        <!-- inhaltsverzeichnis des registers;
        mit links auf die einzelregister und feldfunktion für die ermittlung der seitenzahlen-->
        <xsl:for-each select="index[not(@type='brands')]">
           <w:p>
               <w:pPr>
                   <w:pStyle w:val="epi-inhalt-2"/>
               </w:pPr>

               <!-- verweise auf einzelregister werden als links formatiert;
               das attribut w:bookmark enthält als sprungmarke die das type-attribut bzw. die id des zielregisters -->
               <w:hlink>
                   <xsl:attribute name="w:bookmark"><xsl:value-of select="@type"/></xsl:attribute>
                   <!-- registernummer auslesen, mit punkt abschließen -->
                   <w:r>
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
                           <xsl:when test="titel/node()"><w:t><xsl:value-of select="titel"/>&#x00A0;</w:t></xsl:when>
                           <xsl:otherwise><w:t><xsl:value-of select="@titel"/>&#x00A0;</w:t></xsl:otherwise>
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
                       <w:instrText>PAGEREF <xsl:value-of select="@type"/> \h</w:instrText>
                   </w:r>
                   <w:r>
                       <w:fldChar w:fldCharType="separate"/>
                   </w:r>
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

    <xsl:template  match="index">
        <!-- in dem ersten parameter werden einige register umstrukturiert -->
        <!-- im zweiten parameter werden einzeln stehende unterlemmata an das hauptlemma herangezogen -->

        <!-- im personenregister müssen die einträge der zweiten ebene neu sortiert werden -->
        <xsl:param name="index-0">
                <xsl:choose>
                    <xsl:when test="@propertytype='personnames' or @type='personnames'">

                        <index>
                            <xsl:copy-of select="@*"/>
                            <xsl:for-each select="*">
                                <xsl:choose>
                                    <xsl:when test="name()='eintrag'">
                                        <eintrag>

                                            <xsl:copy-of select="@*"/>
                                            <xsl:copy-of select="lemma"/>
                                            <xsl:copy-of select="nrn"/>
                                            <xsl:copy-of select="verweise"/>
                                            <xsl:for-each select="eintrag">
                                                <xsl:sort select="lemma" data-type="text" order="ascending" lang="de"/>
                                                <xsl:copy-of select="."/>
                                            </xsl:for-each>
                                        </eintrag>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:copy-of select="."/>
                                    </xsl:otherwise>
                                </xsl:choose>

                            </xsl:for-each>
                        </index>
                    </xsl:when>
                    <!-- im sachregister müssen die einträge der ersten ebene neu sortiert werden -->
                    <xsl:when test="@propertytype='subjects' or @type='subjects'">
                            <index>
                                <xsl:copy-of select="@*"/>
                                <xsl:copy-of select="nr"/>
                                <xsl:copy-of select="titel"/>
                                <xsl:copy-of select="notiz"/>
                                <xsl:for-each select="eintrag">
                                    <xsl:sort select="lemma/@sortstring" data-type="text" lang="de" order="ascending"/>
                                    <xsl:copy-of select="."/>
                                </xsl:for-each>
                            </index>
                    </xsl:when>
                    <xsl:when test="@propertytype='blazons'">
                            <index>
                                <xsl:copy-of select="@*"/>
                                <xsl:copy-of select="nr"/>
                                <xsl:copy-of select="titel"/>
                                <xsl:copy-of select="notiz"/>
                                <xsl:for-each select="eintrag">
                                    <xsl:copy>
                                        <xsl:copy-of select="@*"/>
                                        <xsl:for-each select="*">
                                            <xsl:copy>
                                                <xsl:copy-of select="@*"/>
                                                <xsl:apply-templates></xsl:apply-templates>
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
        -->
        <xsl:param name="index">
            <xsl:for-each select="exsl:node-set($index-0)/index">
            <index>
                <xsl:copy-of select="@*"/>
                <xsl:choose>
                    <xsl:when test="nr"><xsl:copy-of select="nr"/></xsl:when>
                    <xsl:otherwise><nr><xsl:value-of select="@nr"/></nr></xsl:otherwise>
                </xsl:choose>
                <xsl:choose>
                    <xsl:when test="titel"><xsl:copy-of select="titel"/></xsl:when>
                    <xsl:otherwise><nr><xsl:value-of select="@titel"/></nr></xsl:otherwise>
                </xsl:choose>
                <xsl:copy-of select="notiz"/>
                <xsl:apply-templates mode="index-param"/>
            </index></xsl:for-each>
        </xsl:param>


<!--         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <test-reg1><xsl:copy-of select="$index-0"/></test-reg1>
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <test-reg2><xsl:copy-of select="$index"/></test-reg2>
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->

        <!-- anschließend erfolgt die endgültige formatierung aus dem paramter heraus -->
        <wx:sub-section>
            <xsl:for-each select="exsl:node-set($index)/*">
                <xsl:apply-templates/>
            </xsl:for-each>

            <!-- der letzte absatz ethält die angabe zur dreispaltigen darstellung -->
            <xsl:choose>
                <!-- die register wappenbeschreibungen (blasonierungen) und literatur (gibt es nur in der artikelpipeline) bleiben einspaltig -->
                <xsl:when test="@type='blazons' or @propertytype='blazons' or @type='indexgroup'"></xsl:when>
                <xsl:when test="@propertytype='literature'"></xsl:when>
                <!-- alle anderen dreispaltig -->
                <xsl:otherwise>
                    <w:p wsp:rsidR="00533250" wsp:rsidRDefault="00533250">
                        <w:pPr>
                            <w:sectPr wsp:rsidR="00533250" wsp:rsidSect="00533250">
                                <w:type w:val="continuous"/>
                                <w:pgSz w:w="11906" w:h="16838"/>
                                <w:pgSz w:w="11906" w:h="16838" />
                                <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                                <w:cols w:num="3" w:space="708"/>
                                <w:docGrid w:line-pitch="360"/>
                            </w:sectPr>
                        </w:pPr>
                    </w:p>
                </xsl:otherwise>
            </xsl:choose>

        </wx:sub-section>
    </xsl:template>

    <!-- muster für die vorformatierung im mode="index-param" -->
    <xsl:template match="gruppe" mode="index-param">
        <gruppe>
            <xsl:copy-of select="@*"/>
            <xsl:apply-templates mode="index-param"/>
        </gruppe>
    </xsl:template>

    <xsl:template mode="index-param" match="eintrag">
        <xsl:choose>
            <!-- bei einzeln stehenden unterlemmata wird das rahmen-tag eintrag entfernt -->
            <xsl:when test="parent::eintrag[not(verweise) and not(nrn/nr)] and
                not(preceding-sibling::eintrag_kursiv) and
                not(following-sibling::eintrag_kursiv) and
                not(preceding-sibling::eintrag) and
                not(following-sibling::eintrag)
                ">
                <xsl:apply-templates mode="index-param"/>
            </xsl:when>
            <!-- alle anderen lemmata verbleiben im eintrag-rahmen -->
            <xsl:otherwise>
                <eintrag>
                    <xsl:copy-of select="@*"/>
                    <xsl:apply-templates mode="index-param"/>
                </eintrag>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template mode="index-param" match="eintrag_kursiv">
    <!-- bei einzeln stehenden unterlemmata wird das rahmen-tag eintrag oder eintrag_kursiv entfernt -->
        <xsl:choose>
        <!-- wenn das rahmen-tag eintrag_kursiv lautet -->
            <xsl:when test="parent::eintrag_kursiv[not(verweise) and not(nrn/nr)] and
                not(preceding-sibling::eintrag_kursiv) and
                not(following-sibling::eintrag_kursiv) and
                not(preceding-sibling::eintrag) and
                not(following-sibling::eintrag)
                ">
                <xsl:apply-templates mode="index-param"/>
            </xsl:when>
        <!-- wenn das rahmen-tag eintrag lautet -->
            <xsl:when test="parent::eintrag[not(verweise) and not(nrn/nr)] and
                not(preceding-sibling::eintrag_kursiv) and
                not(following-sibling::eintrag_kursiv) and
                not(preceding-sibling::eintrag) and
                not(following-sibling::eintrag)
                ">
                <xsl:apply-templates mode="index-param"/>
            </xsl:when>
            <xsl:otherwise>
                <eintrag_kursiv>
                    <xsl:copy-of select="@*"/>
                    <xsl:apply-templates mode="index-param"/>
                </eintrag_kursiv>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    <xsl:template match="lemma" mode="index-param">
        <!-- die lemmata werden hier um die id des übergeordneten eintrags ergänzt,
            um auch für herangezogene lemmata sprungmarken erzeugen zu können -->
        <!-- bei kursiv auszugebenden einträgen wird die kennzeichnung als kursiv auch auf das lemma übertragen,
                damit bei herangezogenen untereinträgen diese kennzeichnung erhalten bleibt
                und in der abschließenden transformation berücksichtigt werden kann -->
        <lemma>
            <xsl:copy-of select="@*"/>
            <xsl:copy-of select="parent::*/@id"/>
            <xsl:if test="parent::eintrag_kursiv"><xsl:attribute name="kursiv">1</xsl:attribute></xsl:if>
            <xsl:value-of select="."/>
        </lemma>
    </xsl:template>

    <xsl:template match="schild" mode="index-param">
        <xsl:copy-of select="."/>
    </xsl:template>
    <xsl:template match="nachweis" mode="index-param"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="oberwappen" mode="index-param"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="verweise" mode="index-param"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="nrn" mode="index-param"><xsl:copy-of select="."/></xsl:template>
<!-- ENDE: vorformatierung -->

<!-- muster für die endgültige formatierung -->

    <xsl:template match="index/nr"><!-- noch nicht eingebunden --></xsl:template>
    <xsl:template match="index/nr" mode="index-param"><!-- muss unterdrückt werden --></xsl:template>
    <xsl:template match="index/titel" mode="index-param"><!-- muss unterdrückt werden --></xsl:template>
    <xsl:template match="index/titel">
        <w:p></w:p>
        <w:p>
            <w:pPr>
                <!--<w:jc w:val="center"/>-->
                <w:pStyle w:val="epi-ueberschrift-2"/>
                <!--<w:outlineLvl w:val="1"/>-->
            </w:pPr>
            <aml:annotation w:type="Word.Bookmark.Start">
                <xsl:attribute name="aml:id"><xsl:value-of select="ancestor::index/@type"/></xsl:attribute>
                <xsl:attribute name="w:name"><xsl:value-of select="ancestor::index/@type"/></xsl:attribute>
            </aml:annotation>
                <xsl:if test="preceding-sibling::nr[text()]">
                    <xsl:for-each select="preceding-sibling::nr">
                        <w:r>
                            <w:rPr><w:smallCaps w:val="off"/></w:rPr>
                            <w:t><xsl:value-of select="."/></w:t><w:t>. </w:t>
                        </w:r>
                    </xsl:for-each>
                </xsl:if>
            <w:r>
                <w:rPr>
                    <w:smallCaps/>
                </w:rPr>
                <xsl:choose>
                    <xsl:when test="node()">
                        <w:t><xsl:value-of select="."/></w:t>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:if test="parent::index[@name='standorte']"><w:t>Standorte</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='personen']"><w:t>Personennamen</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='hersteller']"><w:t>Künstler, Meister, Werkstätten</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='ortsnamen']"><w:t>Ortsnamen und andere geographische Bezeichnungen</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='wappen']"><w:t>Wappen und Marken</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='properties_blasonierungen']"><w:t>Wappenbeschreibungen</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='epitheta']"><w:t>Berufe, Stände, Titel, Verwandtschaften, Attribute</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='initien']"><w:t>Initien</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='formeln']"><w:t>Formeln und besondere Wendungen</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='texttypen']"><w:t>Texttypen und Inschriftenarten</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='quellen']"><w:t>Zitate und Paraphrasen, Textquellen</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='objekte']"><w:t>Inschriftenträger</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='schriftarten']"><w:t>Schriftarten</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='sachen']"><w:t>Sachregister</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='ikonografie']"><w:t>Heilige, biblische Personen, Allegorien, Mythologie in Text und Bild – Ikonografie</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='initialen']"><w:t>Initialen</w:t></xsl:if>
                        <xsl:if test="parent::index[@name='versmaße']"><w:t>Versmaße</w:t></xsl:if>
                    </xsl:otherwise>
                </xsl:choose>
            </w:r>
            <aml:annotation w:type="Word.Bookmark.End">
                <xsl:attribute name="aml:id"><xsl:value-of select="ancestor::index/@type"/></xsl:attribute>
            </aml:annotation>
        </w:p>
<xsl:if test="not(parent::*[@type='indexgroup'])">
        <w:p></w:p>
        <w:p wsp:rsidR="00533250" wsp:rsidRDefault="00533250">
            <w:pPr>
                <w:sectPr wsp:rsidR="00533250" wsp:rsidSect="009E7823">
                    <w:type w:val="continuous"/>
                    <w:pgSz w:w="11906" w:h="16838" />
                    <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                    <w:cols w:space="708"/>
                    <w:docGrid w:line-pitch="360"/>
                </w:sectPr>
            </w:pPr>
        </w:p>
</xsl:if>
    </xsl:template>

    <xsl:template match="index/notiz">
        <xsl:if test="node()">
            <xsl:for-each select="p">
                <w:p><xsl:apply-templates></xsl:apply-templates></w:p>
            </xsl:for-each>
            <w:p></w:p>
            <w:p wsp:rsidR="00533250" wsp:rsidRDefault="00533250">
                <w:pPr>
                    <w:sectPr wsp:rsidR="00533250" wsp:rsidSect="009E7823">
                        <w:type w:val="continuous"/>
                        <w:pgSz w:w="11906" w:h="16838" />
                        <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
                        <w:cols w:space="708"/>
                        <w:docGrid w:line-pitch="360"/>
                    </w:sectPr>
                </w:pPr>
            </w:p>
        </xsl:if>
    </xsl:template>

<xsl:template match="notiz" mode="index-param"></xsl:template>


    <xsl:template match="gruppe" >
        <!-- gruppen die nicht am anfang eines registers stehen, werden durch leerzeile abgesetzt -->
          <xsl:if test="preceding-sibling::gruppe or preceding-sibling::eintrag"><w:p></w:p>/></xsl:if>

          <!-- bei gruppen mit eigenem lemma wird für das lemma ein absatz angelegt -->
        <xsl:if test="lemma">
           <w:p>
               <w:pPr><w:pStyle w:val="epi-register-eintrag"/></w:pPr>
              <xsl:apply-templates select="lemma" />
              <xsl:apply-templates select="verweise" />
              <xsl:apply-templates select="nrn" />
            </w:p>
         </xsl:if>
        <xsl:if test="@level='0' and following-sibling::gruppe or following-sibling::eintrag"><w:p><w:pPr><w:pStyle w:val="epi-leerzeile-3pt"/></w:pPr></w:p>/></xsl:if>
        <xsl:apply-templates select="gruppe|eintrag" />
  </xsl:template>

    <xsl:template match="eintrag|eintrag_kursiv" >
        <!-- die anzahl der spiegelstriche und des einzugs wird über eine hierarchische nummerierung der einträge und untereinträge ermittelt -->
        <xsl:param name="levela"><xsl:number count="eintrag|eintrag_kursiv" from="index" level="multiple"/></xsl:param>
        <!-- die ziffern werden aus der nummerierung eliminiert, sodass nur noch die punkte übrigbleiben -->
        <xsl:param name="levelb"><xsl:value-of select="translate($levela, '.1234567890', '.')"/></xsl:param>
        <!-- die anzahl der punkte entspricht der anzahl der spiegelstriche bzw. des levels -->
        <xsl:param name="levelc"><xsl:value-of select="string-length($levelb)"/></xsl:param>
        <!-- die spiegelstriche werden ensprechend des levels vorformatiert -->
        <xsl:param name="leveld">
            <xsl:if test="$levelc=1">&#x2013; </xsl:if>
            <xsl:if test="$levelc=2">&#x2013; &#x2013; </xsl:if>
            <xsl:if test="$levelc=3">&#x2013; &#x2013; &#x2013; </xsl:if>
            <xsl:if test="$levelc=4">&#x2013; &#x2013; &#x2013; &#x2013; </xsl:if>
        </xsl:param>

      <!-- um die schachtelung der einträge zu beseitigen, werden nur das lemma, nrn und verweise in einen absatz eingefügt;
            untereinträge werden in nachfolgenden absätzen ausgegeben-->

      <!-- leerzeile vor einer gruppe oder nach wechsel des initialbuchstabens -->
        <xsl:if test="preceding-sibling::*[1][name()='gruppe']">
          <w:p>
            <w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr>
          </w:p>
        </xsl:if>
        <xsl:if test="
            ancestor::index[@initialgruppen='1'] and
            preceding-sibling::*[name()='eintrag' or name()='eintrag_kursiv'] and
            not(parent::eintrag) and
            not(parent::eintrag_kursiv) and
            not(parent::gruppe[parent::index[@type='personnames' or @propertytype='personnames']]) and
            not(lemma[1]/@sortchart=preceding-sibling::*[1]/lemma[1]/@sortchart)
            ">
          <w:p>
            <w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr>
          </w:p>
        </xsl:if>
        <w:p>
          <w:pPr><w:pStyle w:val="epi-register-eintrag"/></w:pPr>
         <!-- entsprechend der anzahl der spiegelstriche bzw. des levels wird der hängende einzug festgelegt -->
         <xsl:choose>
         <xsl:when test="$levelc=1"><w:pPr><w:ind w:left="170" w:hanging="170"/></w:pPr></xsl:when>
         <xsl:when test="$levelc=2"><w:pPr><w:ind w:left="312" w:hanging="312"/></w:pPr></xsl:when>
         <xsl:when test="$levelc=3"><w:pPr><w:ind w:left="454" w:hanging="454"/></w:pPr></xsl:when>
         <xsl:when test="$levelc=3"><w:pPr><w:ind w:left="596" w:hanging="596"/></w:pPr></xsl:when>
             <xsl:otherwise><w:pPr><w:ind w:left="170" w:hanging="170"/></w:pPr></xsl:otherwise>
         </xsl:choose>
         <!-- die spiegelstriche werden vor dem lemma eingefügt -->
         <w:r><w:t><xsl:value-of select="$leveld"/></w:t></w:r><xsl:apply-templates select="lemma" />
         <xsl:apply-templates select="nrn" />
         <xsl:apply-templates select="verweise" />
    </w:p>
        <!-- die unterlemmata bekommen separate absätze -->
        <xsl:apply-templates select="eintrag_kursiv|eintrag" />
  </xsl:template>

    <xsl:template match="lemma">
       <xsl:param name="lemma_string"><xsl:value-of select="normalize-space(concat(.,ancestor::eintrag/schild,ancestor::eintrag/nachweis))"/></xsl:param>
        <xsl:param name="string_laenge_lemma"><xsl:value-of select="string-length($lemma_string)"/></xsl:param>
       <xsl:param name="lemma_string_last"><xsl:value-of select="substring($lemma_string,$string_laenge_lemma)"/></xsl:param>
        <xsl:if test="ancestor::eintrag/schild">
        <!--<test_blasonierung><xsl:value-of select="$lemma_string_last"/></test_blasonierung>-->
        </xsl:if>

        <!-- jedes lemma bekommt eine sprungmarke als verweisziel -->
        <!-- anfang: sprungmarke -->
        <aml:annotation w:type="Word.Bookmark.Start">
            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
            <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
        </aml:annotation>
        <w:r>
            <!-- die gruppen-lemmata des formelregisters werden fett formatiert-->
            <xsl:if test="parent::gruppe[@level='0'] and ancestor::index[@type='formulas' or @propertytype='formulas']"><w:rPr><w:smallCaps/></w:rPr></xsl:if>
            <!-- die gruppen-lemmata des quelle-zitate-registers werden fett formatiert-->
            <xsl:if test="parent::gruppe[@level='0'] and ancestor::index[@type='sources' or @propertytype='sources']"><w:rPr><w:smallCaps/></w:rPr></xsl:if>
            <!-- die gruppen-lemmata des literaturregisters werden fett formatiert-->
            <xsl:if test="parent::gruppe and ancestor::index[@type='literature' or @propertytype='literature']"><w:rPr><w:b/></w:rPr></xsl:if>
            <!-- die ehemaligen standorte im standortregister werden kursiv ausgegeben -->
            <xsl:if test="parent::eintrag_kursiv or @kursiv='1'"><w:rPr><w:i/></w:rPr></xsl:if>
            <!-- bei einem herangezogenen kursiven untereintrag wird auch der hauteintrag kursiv ausgegeben -->
            <xsl:if test="parent::eintrag[lemma[@kursiv='1']]"><w:rPr><w:i/></w:rPr></xsl:if>
            <xsl:if test="starts-with(.,'Eingabefehler')"><w:rPr><w:color w:val="#990033"/></w:rPr></xsl:if>
            <!--
                an das hauptlemma herangezogene unterlemmata werden durch kommata separiert;
                hier besteht die möglichkeit, für bestimmte register auch anders vorzugehen,
                etwa mittels doppelpunkt oder ohne trennzeichen
            -->
            <w:t><xsl:apply-templates/></w:t>

<xsl:choose>
<!-- bei den lateinischen epitheta werden die untereinträge ohne komma herangezogen, bei allen übrigen registern und einträgen mit komma -->
    <xsl:when test="ancestor::index[@type='epithets'] and ancestor::gruppe[starts-with(@groupname,'lat')]"><xsl:if test="following-sibling::lemma"><w:t><xsl:text> </xsl:text></w:t></xsl:if></xsl:when>
    <xsl:otherwise><xsl:if test="following-sibling::lemma"><w:t>, </w:t></xsl:if></xsl:otherwise>
</xsl:choose>

            <!-- für das register wappenbeschreibungen werden die elemente schild und nachweis in das lemma hineingezogen -->
            <xsl:if test="parent::eintrag/schild">
                <w:t>: <xsl:value-of select="parent::eintrag/schild"/></w:t>
            </xsl:if>
            <xsl:if test="parent::eintrag/nachweis">
                <w:t>; <xsl:value-of select="parent::eintrag/nachweis"/></w:t>
            </xsl:if>
            <xsl:if test="parent::eintrag/schild or parent::eintrag/nachweis">
                <xsl:if test="not($lemma_string_last='.')">
                    <w:t test="nachweis">.</w:t>
                </xsl:if>
            </xsl:if>
        </w:r>
        <aml:annotation w:type="Word.Bookmark.End">
            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
        </aml:annotation>
        <!-- ende: sprungmarke -->

    </xsl:template>

    <xsl:template match="nrn">
        <!-- nummern auslesen -->
        <w:r><w:t><xsl:text> <!-- breiteres leerzeichen en-space U2002 --></xsl:text></w:t></w:r>
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
                <xsl:if test="@verloren='1'"><w:rPr><w:i/></w:rPr></xsl:if>
                <!-- nummer ausgeben -->
                <w:t><xsl:value-of select="."/></w:t></w:r>
            <w:r>
                <w:fldChar w:fldCharType="end"/>
            </w:r>
            <!-- link auf die artikelnummer: ende -->

            <!-- gegebenenfalls kommata setzen -->
            <xsl:if test="following-sibling::nr"><w:r><w:t>, </w:t></w:r></xsl:if>
        </xsl:for-each></xsl:template>



    <xsl:template match="verweise[*]">
        <!-- zur sortierung werden die verweise zunächst in einen paramter geladen
        zur späteren unterscheidung wird der register-typ als attribut mitgeführt-->

       <xsl:param name="verweise1">
         <xsl:call-template name="verweisschleife"></xsl:call-template>
        </xsl:param>

<!-- doubletten entfernen und sortieren -->
<xsl:param name="verweise2">
<xsl:for-each select="exsl:node-set($verweise1)/verweis[not(lemma=preceding-sibling::verweis/lemma)]">
<xsl:sort select="lemma/@sortstring" order="ascending" lang="de"  case-order="upper-first"/>
<xsl:copy-of select="."/>
</xsl:for-each>
</xsl:param>

        <xsl:choose>
            <xsl:when test="preceding-sibling::nrn or preceding-sibling::eintrag or following-sibling::nrn or following-sibling::eintrag">
                <xsl:if test="not(preceding-sibling::verweise)">
                <w:r><w:t>, s.&#x00A0;a. </w:t></w:r>
                </xsl:if>
            </xsl:when>
            <xsl:otherwise>
                <xsl:if test="not(preceding-sibling::verweise)">
                    <w:r><w:t> s. </w:t></w:r>
                </xsl:if>
            </xsl:otherwise>
        </xsl:choose>

<!-- parametertest verweise
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<verweise1><xsl:copy-of select="$verweise1"/></verweise1><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<verweise2><xsl:copy-of select="$verweise2"/></verweise2><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->



        <xsl:for-each select="exsl:node-set($verweise2)/verweis">

            <!-- link auf verweisziel: anfang -->
            <w:r>
                <w:fldChar w:fldCharType="begin">
                    <!--<w:fldData xml:space="preserve"><xsl:value-of select="generate-id()"/></w:fldData>-->
                </w:fldChar>
            </w:r>
            <w:r>
                       <xsl:choose>
                            <!-- variante (1) personenregister -->
                            <xsl:when test="ancestor::index[@type='personnames' or @propertytype='personnames']">
                                <!--<w:instrText>Hyperlink \l "<xsl:value-of select=".//*/@articles_id"/>" </w:instrText>-->
                                <w:instrText>Hyperlink \l "<xsl:value-of select="@id"/>" </w:instrText>
                            </xsl:when>
                            <!-- variante (2) andere register-->
                            <xsl:otherwise>
                                  <w:instrText>Hyperlink \l "<xsl:value-of select="@id"/>" </w:instrText>
                            </xsl:otherwise>
                        </xsl:choose>

            </w:r>
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
           <xsl:if test="following-sibling::verweise"><w:r><w:t>, </w:t></w:r></xsl:if>

    </xsl:template>

<xsl:template name="verweisschleife">
<xsl:for-each select="*[name()='siehe' or name()='siehe_auch']">
<xsl:variable name="p_verweistyp"><xsl:value-of select="name()"/></xsl:variable>
<xsl:choose>
<!-- verborgene gruppeneinträge werden übersprungen, aber eingeschränkt auf das formelregister -->
    <xsl:when test="@ishidden='1' and ancestor::index[@type='formulas' or @propertytype='formulas']">
<xsl:call-template name="verweisschleife"></xsl:call-template>
</xsl:when>
<xsl:otherwise>
    <verweis type="{$p_verweistyp}">
<xsl:copy-of select="@*"/><xsl:copy-of select="lemma"/>
<xsl:call-template name="verweisschleife"></xsl:call-template>
</verweis>
</xsl:otherwise>
</xsl:choose>



<!--    <verweis type="{$p_verweistyp}">
<xsl:copy-of select="@*"/><xsl:copy-of select="lemma"/>
<xsl:call-template name="verweisschleife"></xsl:call-template>
</verweis>-->
</xsl:for-each>





<!--    <xsl:for-each select="parent::*[name()='siehe' or name()='siehe_auch']">
        <verweis><xsl:copy-of select="@*"/><xsl:copy-of select="lemma"/>
            <xsl:call-template name="verweisumkehr"></xsl:call-template>
        </verweis>
    </xsl:for-each>-->
</xsl:template>

<xsl:template match="index/notiz/p/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>

</xsl:stylesheet>
