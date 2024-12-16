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
  xmlns:msxsl="urn:schemas-microsoft-com:xslt"
  xmlns:exsl="http://exslt.org/common"
  xmlns:php="http://php.net/xsl"
>

<xsl:import href="commons/epi-switch.xsl"/> <!-- "Schalter" zum Aus- und Einblenden bestimmter Abschnitte des Layouts -->
<xsl:import href="trans-word/epi-word-register.xsl"/>
<xsl:import href="trans-word/epi-word-styles.xsl"/>
<xsl:import href="trans-word/epi-word-ligaturen.xsl"/>
<xsl:import href="trans-word/epi-word-titelei.xsl"/>

<xsl:output method="xml" version="1.0" indent="yes" encoding="UTF-8"/>


<xsl:param name="p_db"><xsl:value-of select="book/project/database"/></xsl:param>



<!-- leere linke seite zum einfügen als vorsatzseite vor dem beginn bestimmter abschnitte-->
  <!-- höhe des satzspiegels 14879 twips -->
<xsl:param name="odd-page">
    <w:p>
      <w:pPr>
        <w:sectPr>
          <w:type w:val="odd-page"/>
          <w:pgSz w:w="11906" w:h="16838" />
          <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800"  w:gutter="0" />
          <w:cols w:space="708"/>
          <w:docGrid w:line-pitch="360"/>
          <w:hdr w:type="even">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi.seitenkopf"/>
              </w:pPr>
            </w:p>
          </w:hdr>
          <w:hdr w:type="odd">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi.normal-1"/>
              </w:pPr>
            </w:p>
          </w:hdr>
          <w:ftr w:type="even">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi.seitenfuss"/>
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
                <w:pStyle w:val="epi.seitenfuss"/>
              </w:pPr>
            </w:p>
          </w:ftr>
          <w:ftr w:type="odd">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi.seitenfuss"/>
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
                <w:pStyle w:val="epi.seitenfuss"/>
              </w:pPr>
            </w:p>
          </w:ftr>
        </w:sectPr>
      </w:pPr>
    </w:p>
</xsl:param>


<xsl:template match="/">
<!--Verarbeitungsanweisung zum Erzeugen von <?mso-application progid="Word.Document"?> -->
<xsl:processing-instruction name="mso-application">
 <xsl:text>progid="Word.Document"</xsl:text>
</xsl:processing-instruction>


<w:wordDocument xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w10="urn:schemas-microsoft-com:office:word" xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core" xmlns:aml="http://schemas.microsoft.com/aml/2001/core" xmlns:wx="http://schemas.microsoft.com/office/word/2003/auxHint" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2" xmlns:fo="http://www.w3.org/1999/XSL/Format" w:macrosPresent="no" w:embeddedObjPresent="no" w:ocxPresent="no" xml:space="preserve">
<xsl:call-template name="fonts"></xsl:call-template>
<xsl:call-template name="styles"></xsl:call-template>

<w:docPr>
  <w:view w:val="print"/>
  <w:zoom w:percent="150"/>
  <w:attachedTemplate w:val=""/>
  <w:defaultTabStop w:val="708"/>
  <!-- automatische silbentrennung -->
  <!-- die funktion kann für einzelne absätze in w:pPr durch <w:suppressAutoHyphens/> ausgeschaltet werden  -->
    <w:autoHyphenation/>
    <!-- aufeinanderfolgende trennstriche: 1 -->
    <w:consecutiveHyphenLimit w:val="2"/>
    <!-- trennzone 0,5 cm -->
    <w:hyphenationZone w:val="284"/>
    <!-- wörter in grossbuchstaben werden nicht getrennt -->
    <w:doNotHyphenateCaps/>
  <w:characterSpacingControl w:val="DontCompress"/>
  <!-- seiten spiegeln: left wird zu innen, right zu außen -->
  <w:mirrorMargins/>
  <w:validateAgainstSchema/>
  <w:saveInvalidXML w:val="off"/>
  <w:ignoreMixedContent w:val="off"/>
  <w:alwaysShowPlaceholderText w:val="off"/>
  <w:compat/>

  <w:footnotePr>
    <w:footnote w:type="separator">
      <w:p>
        <w:pPr>
          <w:spacing w:line="240" w:line-rule="auto"/>
        </w:pPr>
        <w:r>
          <w:separator/>
        </w:r>
      </w:p>
    </w:footnote>
    <w:footnote w:type="continuation-separator">
      <w:p>
        <w:pPr>
          <w:spacing w:line="240" w:line-rule="auto"/>
        </w:pPr>
        <w:r>
          <w:continuationSeparator/>
        </w:r>
      </w:p>
    </w:footnote>
  </w:footnotePr>
</w:docPr>
<w:body>
    <xsl:apply-templates/>

 <w:sectPr>
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
  <w:type w:val="continuous"/>
   <w:pgSz w:w="11906" w:h="16838"/>
      <!-- satzspiegel breite 8107 twips (14,3 cm) , höhe 13124 twips (23,15 cm)
        22,15 cm = 12559 twips
      1 cm 0 567 twips
      -->
  <w:pgMar w:top="1701" w:right="1985" w:bottom="2580" w:left="1814" w:header="709" w:footer="1800" w:gutter="0"/>
      <xsl:if test="$register=1 and .//indices[not(following-sibling::*)]"><w:cols w:num="3" w:space="708"/></xsl:if>
      <xsl:if test="$register!=1"><w:cols w:space="708"/></xsl:if>
  <w:docGrid w:line-pitch="360"/>
</w:sectPr>


</w:body>
</w:wordDocument>
</xsl:template>

<!--==KATALOG / Catalogue===============================================================-->

<!--Hauptmuster / master pattern-->
<xsl:template match="book"><xsl:apply-templates/></xsl:template>

<xsl:template match="catalog">
<xsl:if test="not($katalog='0')">
  <xsl:comment>katalog anfang</xsl:comment> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  <xsl:if test="$katalogtitel='1' or $einleitung='1' or $titelei='1' or $vorwort='1'">
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:if test="preceding-sibling::*"><xsl:copy-of select="$odd-page"/></xsl:if>
      <w:p>
        <w:pPr>
          <w:pStyle w:val="epi-ueberschrift-1"/>
          <w:outlineLvl w:val="0"/>
        </w:pPr>
        <!-- sprungmarke einfügen und titel auslesen -->
          <aml:annotation w:type="Word.Bookmark.Start">
              <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
              <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
          </aml:annotation>
                  <w:r><w:t><xsl:value-of select="titel"/></w:t></w:r>
          <aml:annotation w:type="Word.Bookmark.End">
              <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
          </aml:annotation>
      </w:p>
       <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wx:sect>
     <w:p>
      <w:pPr><w:pageBreakBefore/></w:pPr>
    </w:p>
      <xsl:if test="preceding-sibling::*"><xsl:copy-of select="$odd-page"/></xsl:if>
    </wx:sect>
  </xsl:if>

  <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <w:p>
      <w:pPr><w:pageBreakBefore/></w:pPr>
    </w:p>
    <xsl:if test="preceding-sibling::*"><xsl:copy-of select="$odd-page"/></xsl:if>

  <!--Datensaetze/Artikel / articles-->
  <xsl:for-each select="article">
    <xsl:apply-templates select="."/>
  </xsl:for-each> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

                <!-- der letzte absatz ethält die angabe zur einspaltigen darstellung -->
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
  </wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  <xsl:comment>katalog ende</xsl:comment> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:if></xsl:template>

  <xsl:template match="project"></xsl:template>


  <xsl:template match="vorwort">
    <xsl:comment>vorwort anfang</xsl:comment> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:if test="preceding-sibling::*">
        <xsl:copy-of select="$odd-page"/>
      </xsl:if>
      <!-- überschrift  -->
      <w:p>
        <w:pPr><w:pStyle w:val="epi-ueberschrift-1"/></w:pPr>
        <!-- sprungmarke einfügen und titel auslesen -->
          <aml:annotation w:type="Word.Bookmark.Start">
              <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
              <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
          </aml:annotation>
                  <w:r><w:t><xsl:value-of select="titel"/></w:t></w:r>
          <aml:annotation w:type="Word.Bookmark.End">
              <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
          </aml:annotation>
      </w:p>

      <!-- content -->
      <xsl:for-each select="content/p">
        <w:p>
          <w:pPr>
            <w:pStyle w:val="epi-normal-1"/>
            <xsl:if test="@indent='1'">
              <w:ind w:first-line="240"/>
            </xsl:if>
          </w:pPr>
          <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
        </w:p>
      </xsl:for-each>
      <!-- unterschrift -->
      <w:p>
        <w:pPr>
          <w:pStyle w:val="epi-normal-1"/>
          <w:tabs>
            <w:tab w:val="right" w:pos="8080"/>
          </w:tabs>
          <w:ind w:right="27"/>
          <w:spacing w:before="240"/>
        </w:pPr>
        <w:r>
          <w:t><xsl:value-of select="datum/p"/></w:t>
        </w:r>
        <w:r>
          <w:tab/>
          <w:t><xsl:value-of select="unterschrift/p"/></w:t>
        </w:r>
      </w:p>
    </wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>vorwort ende</xsl:comment>
  </xsl:template>

  <xsl:template match="inhalt">
    <xsl:comment>inhaltsverzeichnis anfang</xsl:comment> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:if test="preceding-sibling::*">
        <xsl:copy-of select="$odd-page"/>
      </xsl:if>
      <!-- überschrift des inhaltsverzeichnisses  -->
      <w:p>
        <w:pPr><w:pStyle w:val="epi-ueberschrift-1"/></w:pPr>
        <w:r><w:t><xsl:value-of select="titel"/></w:t></w:r>
      </w:p>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <!-- jeden bandabschnitt erster ebene ansteuern -->
      <xsl:for-each select="ancestor::book/*[@indizieren='1']">
        <!-- den titel in einer variable speichern -->
        <xsl:variable name="titel">
          <xsl:choose>
             <xsl:when test="titel">
               <xsl:value-of select="titel"/>
             </xsl:when>
             <xsl:when test="lemma">
               <xsl:value-of select="lemma"/>
             </xsl:when>
             <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
           </xsl:choose>
        </xsl:variable>

        <!-- nach abschnittstypen differenzieren -->
        <xsl:choose>

          <!-- einleitung -->
          <xsl:when test="@data_key='introduction'">
            <!-- zwischenzeile -->
            <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
            <w:p>
             <w:pPr>
                 <w:pStyle w:val="epi-inhalt-1"/>
             </w:pPr>

               <w:hlink>
                   <xsl:attribute name="w:bookmark"><xsl:value-of select="@id"/></xsl:attribute>
                    <w:r>
                       <w:t><xsl:value-of select="$titel"/>&#x00A0;</w:t>
                   </w:r>
                   <!-- tab mit führenden punkten setzen, dann rechtsbündig -->
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
                       <w:instrText>PAGEREF <xsl:value-of select="@id"/> \h</w:instrText>
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

           </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-5pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <!-- unterabschnitte der einleitung -->
            <xsl:for-each select=".//abschnitt">
              <xsl:variable name="nbr"><xsl:value-of select="substring(@name,1,1)"/></xsl:variable>
              <xsl:variable name="titel2">
                <xsl:choose>
                  <xsl:when test="titel">
                    <xsl:value-of select="titel"/>
                  </xsl:when>
                  <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                </xsl:choose>
              </xsl:variable>
              <xsl:variable name="lfnr"><xsl:value-of select="substring-before($titel2,' ')"/></xsl:variable>
              <xsl:variable name="titel3"><xsl:value-of select="substring-after($titel2,' ')"/></xsl:variable>
              <xsl:if test="$nbr=1 or $nbr=2 or $nbr=3 or $nbr=4 or $nbr=5 or $nbr=6 or $nbr=7 or $nbr=8 or $nbr=9">
               <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <w:p>
             <w:pPr>
               <w:pStyle w:val="epi-inhalt-2"/>
             </w:pPr>

               <w:hlink>
                   <xsl:attribute name="w:bookmark"><xsl:value-of select="@id"/></xsl:attribute>
                   <!-- registernummer auslesen, mit punkt abschließen -->
                   <w:r>
                       <w:t><xsl:value-of select="$lfnr"/>  </w:t>
                   </w:r>
                   <!-- tab linksbündig setzen -->
                   <w:r>
                       <w:tab/>
                   </w:r>
                   <!-- titel auslesen -->
                   <w:r>
                       <w:t><xsl:value-of select="$titel3"/>&#x00A0;</w:t>
                   </w:r>
                   <!-- tab mit führenden punkten setzen, dann rechtsbündig -->
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
                       <w:instrText>PAGEREF <xsl:value-of select="@id"/> \h</w:instrText>
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

           </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              </xsl:if>
              </xsl:for-each>
          </xsl:when>

          <!-- register -->
          <xsl:when test="index or name()='indices'">
            <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
            <w:p>
             <w:pPr>
                 <w:pStyle w:val="epi-inhalt-1"/>
             </w:pPr>

               <w:hlink>
                   <xsl:attribute name="w:bookmark"><xsl:value-of select="@id"/></xsl:attribute>
                   <!-- registernummer auslesen, mit punkt abschließen -->
                   <w:r>
                       <w:t><xsl:value-of select="$titel"/>&#x00A0;</w:t>
                   </w:r>
                   <!-- tab mit führenden punkten setzen, dann rechtsbündig -->
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
                       <w:instrText>PAGEREF <xsl:value-of select="@id"/> \h</w:instrText>
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

           </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <!-- zwischenzeile -->
           <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-5pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

            <!-- für die einzelnen register wird das register-template aufgerufen -->
            <xsl:call-template name="register-uebersicht"></xsl:call-template>
          </xsl:when>


          <!-- die übrigen abschnitte -->
          <xsl:otherwise>
            <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
            <w:p>
             <w:pPr>
                 <w:pStyle w:val="epi-inhalt-1"/>
             </w:pPr>

               <w:hlink>
                   <xsl:attribute name="w:bookmark"><xsl:value-of select="@id"/></xsl:attribute>
                   <!-- registernummer auslesen, mit punkt abschließen -->
                   <w:r>
                       <w:t><xsl:value-of select="$titel"/>&#x00A0;</w:t>
                   </w:r>
                   <!-- tab mit führenden punkten setzen, dann rechtsbündig -->
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
                       <w:instrText>PAGEREF <xsl:value-of select="@id"/> \h</w:instrText>
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
           </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <!-- zwischenzeile -->
            <xsl:if test="*[@indizieren='1']"><w:p><w:pPr><w:pStyle w:val="epi-leerzeile-5pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>

            <!-- unterabschnitte -->
            <xsl:for-each select="*[@indizieren='1']">
              <xsl:variable name="titel3">
             <xsl:choose>
             <xsl:when test="titel">
               <xsl:value-of select="titel"/>
             </xsl:when>
             <xsl:when test="lemma">
               <xsl:value-of select="lemma"/>
             </xsl:when>
             <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
           </xsl:choose>
              </xsl:variable>
            <w:p>
             <w:pPr>
                 <w:pStyle w:val="epi-inhalt-2"/>
             </w:pPr>

               <w:hlink>
                   <xsl:attribute name="w:bookmark"><xsl:value-of select="@id"/></xsl:attribute>
                   <!-- registernummer auslesen, mit punkt abschließen -->
                   <w:r>
                       <w:t><xsl:value-of select="$titel3"/>&#x00A0;</w:t>
                   </w:r>
                   <!-- tab mit führenden punkten setzen, dann rechtsbündig -->
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
                       <w:instrText>PAGEREF <xsl:value-of select="@id"/> \h</w:instrText>
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

           </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

            </xsl:for-each>
          </xsl:otherwise>
        </xsl:choose>


      </xsl:for-each>


    </wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>inhaltsverzeichnis ende</xsl:comment>
  </xsl:template>

  <xsl:template match="einleitung">
    <xsl:comment>einleitung anfang</xsl:comment>
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:if test="preceding-sibling::*"><xsl:copy-of select="$odd-page"/></xsl:if>
      <w:p>
        <w:pPr><w:pStyle w:val="epi-ueberschrift-1"/></w:pPr>
        <!-- sprungmarke einfügen und titel auslesen -->
          <aml:annotation w:type="Word.Bookmark.Start">
              <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
              <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
          </aml:annotation>
                  <w:r><w:t><xsl:value-of select="titel"/></w:t></w:r>
          <aml:annotation w:type="Word.Bookmark.End">
              <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
          </aml:annotation>
      </w:p>
      <xsl:for-each select="abschnitt">
        <!-- überschrift -->
        <xsl:if test="@titel and not(@data_key='csv_table') and not(@data_key='continuation') and not(@sectiontype='table')">
          <!-- eine überschrift wird ausegeben, wenn es ein titel-attribut gibt -->
        <w:p>
          <w:pPr>
            <!-- das format der überschrift folgt dem level-attribut des abschnitts -->
            <xsl:if test="@level='1'"><w:pStyle w:val="epi-ueberschrift-2"/></xsl:if>
            <xsl:if test="@level='2'"><w:pStyle w:val="epi-ueberschrift-3"/></xsl:if>
            <xsl:if test="@level='3'"><w:pStyle w:val="epi-ueberschrift-4"/></xsl:if>
          </w:pPr>

        <!-- sprungmarke einfügen und titel auslesen -->

              <aml:annotation w:type="Word.Bookmark.Start">
                  <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                  <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
              </aml:annotation>
                <w:r><w:t><xsl:value-of select="@name"/></w:t></w:r>
              <aml:annotation w:type="Word.Bookmark.End">
                  <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
              </aml:annotation>

        </w:p>
        </xsl:if>
        <!-- überschrift ende -->

        <xsl:choose>
          <xsl:when test="@data_key='csv_table'">
            <xsl:if test="preceding-sibling::abschnitt[1][node()]"><w:pStyle w:val="epi-leerzeile-9pt"/></xsl:if>
            <w:tbl>
              <xsl:for-each select="row">
                <xsl:variable name="anzahl_zellen"><xsl:value-of select="count(cell)"/></xsl:variable>
                <w:tr>
                  <xsl:for-each select="cell">
                    <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                      <w:tcPr>
                        <xsl:choose>
                          <xsl:when test="$anzahl_zellen = 2">
                            <xsl:if test="position() = 1"><w:tcW w:w="851" w:type="dxa"/></xsl:if>
                          </xsl:when>
                          <xsl:otherwise>
                            <xsl:if test="position() != 1"><w:tcMar><w:left w:w="100" w:type="dxa"/><w:bottom w:w="20" w:type="dxa"/></w:tcMar></xsl:if>
                          </xsl:otherwise>
                        </xsl:choose>
                      </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                      <w:p><xsl:apply-templates select="."></xsl:apply-templates></w:p>
                    </w:tc>
                  </xsl:for-each>
                </w:tr>
              </xsl:for-each>
            </w:tbl>
            <w:pStyle w:val="epi-leerzeile-9pt"/>
          </xsl:when>

          <xsl:when test="@sectiontype='table'">
            <xsl:if test="preceding-sibling::abschnitt[1][node()]"><w:pStyle w:val="epi-leerzeile-9pt"/></xsl:if>
            <w:tbl>
              <xsl:for-each select="tr">
                <xsl:variable name="anzahl_zellen"><xsl:value-of select="count(td)"/></xsl:variable>
                <w:tr>
                  <xsl:for-each select="td">
                    <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                      <w:tcPr>
                        <xsl:choose>
                          <xsl:when test="$anzahl_zellen = 2">
                            <xsl:if test="position() = 1"><w:tcW w:w="851" w:type="dxa"/></xsl:if>
                          </xsl:when>
                          <xsl:otherwise>
                            <xsl:if test="position() != 1"><w:tcMar><w:left w:w="100" w:type="dxa"/><w:bottom w:w="20" w:type="dxa"/></w:tcMar></xsl:if>
                          </xsl:otherwise>
                        </xsl:choose>
                      </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                      <w:p><xsl:apply-templates select="."></xsl:apply-templates></w:p>
                    </w:tc>
                  </xsl:for-each>
                </w:tr>
              </xsl:for-each>
            </w:tbl>
            <w:pStyle w:val="epi-leerzeile-9pt"/>
          </xsl:when>

          <xsl:otherwise>
            <xsl:for-each select="p|h">
<!--                <xsl:if test="@value='H1'"><w:p><w:pPr><w:pStyle w:val="epi-leerzeile-20pt"/></w:pPr></w:p></xsl:if>
                <xsl:if test="@value='H2'"><w:p><w:pPr><w:pStyle w:val="epi-leerzeile-16pt"/></w:pPr></w:p></xsl:if>
                <xsl:if test="@value='H3'"><w:p><w:pPr><w:pStyle w:val="epi-leerzeile-12pt"/></w:pPr></w:p></xsl:if>
                <xsl:if test="@value='H4'"><w:p><w:pPr><w:pStyle w:val="epi-leerzeile-8pt"/></w:pPr></w:p></xsl:if>
                <xsl:if test="@value='H5'"><w:p><w:pPr><w:pStyle w:val="epi-leerzeile-4pt"/></w:pPr></w:p></xsl:if>
              <w:p>
                <w:pPr>
                  <xsl:if test="@indent='1'">
                    <w:ind w:first-line="240"/>
                  </xsl:if>
                </w:pPr>
                <xsl:apply-templates></xsl:apply-templates>
              </w:p>-->
                <xsl:apply-templates select="."></xsl:apply-templates>
            </xsl:for-each>
          </xsl:otherwise>
        </xsl:choose>

      </xsl:for-each>
    </wx:sect>
    <xsl:comment>einleitung  ende</xsl:comment>
  </xsl:template>

<xsl:template match="p[ancestor::einleitung or ancestor::maps]">
              <w:p>
                <w:pPr>
                  <xsl:if test="@indent='1'">
                    <w:ind w:first-line="240"/>
                  </xsl:if>
                </w:pPr>
                <xsl:apply-templates></xsl:apply-templates>
              </w:p>
</xsl:template>
<xsl:template match="h[ancestor::einleitung]">
              <w:p>
                <w:pPr>
                <xsl:choose>
                    <xsl:when test="@data-link-value='H1'"><w:pStyle w:val="epi-ueberschrift-1"/></xsl:when>
                    <xsl:when test="@data-link-value='H2'"><w:pStyle w:val="epi-ueberschrift-2"/></xsl:when>
                    <xsl:when test="@data-link-value='H3'"><w:pStyle w:val="epi-ueberschrift-3"/></xsl:when>
                    <xsl:when test="@data-link-value='H4'"><w:pStyle w:val="epi-ueberschrift-4"/></xsl:when>
                    <xsl:when test="@data-link-value='H5'"><w:pStyle w:val="epi-ueberschrift-5"/></xsl:when>
                    <xsl:when test="@data-link-value='H6'"><w:pStyle w:val="epi-ueberschrift-6"/></xsl:when>
                    <xsl:when test="@data-link-value='H7'"><w:pStyle w:val="epi-ueberschrift-7"/></xsl:when>
                    <xsl:when test="@data-link-value='H8'"><w:pStyle w:val="epi-ueberschrift-8"/></xsl:when>
                </xsl:choose>
                </w:pPr>
                <xsl:apply-templates></xsl:apply-templates>
              </w:p>
</xsl:template>


  <xsl:template match="inschriftenliste">
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>inschriftenliste anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <xsl:if test="preceding-sibling::*">
            <xsl:copy-of select="$odd-page"/>
          </xsl:if>
          <!-- überschrift  -->

          <w:p>
            <w:pPr>
              <w:pStyle w:val="epi-ueberschrift-1"/>
            </w:pPr>
            <!-- sprungmarke einfügen und titel auslesen -->
              <aml:annotation w:type="Word.Bookmark.Start">
                  <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                  <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
              </aml:annotation>
                      <w:r><w:t><xsl:value-of select="normalize-space(titel)"/></w:t></w:r>
              <aml:annotation w:type="Word.Bookmark.End">
                  <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
              </aml:annotation>
          </w:p>

          <xsl:for-each select="p[not(text()='§')]">
            <w:p>
              <w:pPr><w:pStyle w:val="epi-normal-1"/>
              <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
              </w:pPr>
            </w:p>
           </xsl:for-each>

          <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>

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
        <wx:sect>



            <!-- hier die liste einfügen -->
          <xsl:for-each select="items/datierung">
          <w:p>
            <w:pPr>
              <w:pStyle w:val="epi-register-eintrag"/>
              <xsl:if test="not(position()=1)"><!--<w:spacing w:before="40"/>--></xsl:if>
            </w:pPr>
            <w:r><w:t><xsl:value-of select="datierung"/></w:t></w:r>
          </w:p>
            <xsl:for-each select="objekt">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-register-eintrag"/>
                  <w:ind w:left="200"/>
                </w:pPr>
                <w:r><w:t><xsl:value-of select="objekttyp"/>: </w:t></w:r>
                <xsl:for-each select="article">
                  <w:r><w:t><xsl:value-of select="artikelnummer"/></w:t></w:r>
                  <xsl:for-each select="sequenz">
                    <xsl:variable name="numbers" select="count(inschriftnummer)"></xsl:variable>
                    <xsl:choose>
                      <xsl:when test="$numbers != 1">
                        <!-- erster inschrift-buchstabe -->
                        <w:r>
                          <w:rPr><w:sz w:val="16"/></w:rPr>
                          <w:t><xsl:value-of select="inschriftnummer[1]"/></w:t>
                        </w:r>
                        <!-- spiegelstrich -->
                        <w:r>
                          <w:rPr><w:sz w:val="16"/></w:rPr>
                          <w:t>&#x2013;</w:t>
                        </w:r>
                        <!-- letzter inschriftbuchstabe -->
                        <w:r>
                          <w:rPr><w:sz w:val="16"/></w:rPr>
                          <w:t><xsl:value-of select="inschriftnummer[last()]"/></w:t>
                        </w:r>
                      </xsl:when>
                      <xsl:otherwise>
                        <w:r>
                          <w:rPr><w:sz w:val="16"/></w:rPr>
                          <w:t><xsl:value-of select="inschriftnummer"/></w:t>
                        </w:r>
                      </xsl:otherwise>
                    </xsl:choose>
                    <xsl:if test="following-sibling::sequenz">
                      <w:r><w:t>, </w:t></w:r>
                    </xsl:if>
                  </xsl:for-each>
                    <xsl:if test="following-sibling::article">
                      <w:r><w:t>, </w:t></w:r>
                    </xsl:if>
                </xsl:for-each>
              </w:p>
            </xsl:for-each>

          </xsl:for-each>


            <!-- der letzte absatz ethält die angabe zur dreispaltigen darstellung -->
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

    <xsl:comment>inschriftenliste ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="abkuerzungen">
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>abkuerzungen anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wx:sect>
      <w:p>
        <w:pPr><w:pageBreakBefore/></w:pPr>
      </w:p>
    </wx:sect>
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:copy-of select="$odd-page"/>
    <!-- titel, absätze und tabellen ansteuern und über je eigen templates einfügen -->
      <xsl:apply-templates></xsl:apply-templates>

    </wx:sect>
    <xsl:comment>abkuerzungen ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="titel[parent::abkuerzungen]">
      <w:p>
        <w:pPr><w:pStyle w:val="epi-ueberschrift-1"/></w:pPr>
    <!-- sprungmarke einfügen und titel auslesen -->
          <aml:annotation w:type="Word.Bookmark.Start">
              <xsl:attribute name="aml:id"><xsl:value-of select="parent::*/@id"/></xsl:attribute>
              <xsl:attribute name="w:name"><xsl:value-of select="parent::*/@id"/></xsl:attribute>
          </aml:annotation>
            <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
          <aml:annotation w:type="Word.Bookmark.End">
              <xsl:attribute name="aml:id"><xsl:value-of select="parent::*/@id"/></xsl:attribute>
          </aml:annotation>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="p[parent::abkuerzungen]">
   <w:p>
    <w:pPr>
      <xsl:if test="@indent='1'">
        <w:ind w:first-line="240"/>
      </xsl:if>
    </w:pPr>
    <xsl:apply-templates></xsl:apply-templates>
  </w:p>
  </xsl:template>
  <xsl:template match="table">
    <xsl:if test="preceding-sibling::p"><w:p><w:pPr><w:pStyle w:val="epi-leerzeile-9pt"/></w:pPr></w:p></xsl:if>
      <w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="row">
          <w:tr>
            <w:tc>
              <w:p>
                <xsl:apply-templates select="cell[1]"/>
              </w:p>
            </w:tc>
            <w:tc>
              <w:p>
                <xsl:apply-templates select="cell[2]"/>
              </w:p>
            </w:tc>
          </w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
      </w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

<!-- verzeichnis literatur und quellen -->
  <xsl:template match="quellen_literatur">
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>biblio anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <xsl:copy-of select="$odd-page"/>
      <!-- titel einfügen -->
      <w:p>
        <w:pPr>
          <w:pStyle w:val="epi-ueberschrift-1"/>
          <w:pageBreakBefore/>
        </w:pPr>
        <!-- sprungmarke einfügen und titel auslesen -->
              <aml:annotation w:type="Word.Bookmark.Start">
                  <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                  <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
              </aml:annotation>
                <w:r><w:t><xsl:value-of select="titel"/></w:t></w:r>
              <aml:annotation w:type="Word.Bookmark.End">
                  <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
              </aml:annotation>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:for-each select="abschnitt">
      <w:p>
        <w:pPr>
            <w:pStyle w:val="epi-ueberschrift-2"/>
        </w:pPr>
        <!-- sprungmarke einfügen und titel auslesen -->
              <aml:annotation w:type="Word.Bookmark.Start">
                  <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
                  <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
              </aml:annotation>
                <w:r><w:t><xsl:value-of select="lemma"/></w:t></w:r>
              <aml:annotation w:type="Word.Bookmark.End">
                  <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
              </aml:annotation>
        </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:call-template name="biblio"></xsl:call-template>
      <xsl:if test="following-sibling::abschnitt">
        <w:p>
          <w:pPr>
            <w:pStyle w:val="epi-leerzeile-18pt"/>
          </w:pPr>
        </w:p>
      </xsl:if>
      </xsl:for-each>

    </wx:sect>
    <xsl:comment>biblio ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template name="biblio">
    <xsl:for-each select=".//item[not(@ishidden='1')]">
    <xsl:choose>
    <!-- di-bände bei gewählter option ausblenden -->
        <xsl:when test="starts-with(lemma,'DI ') and $quellen-literatur-di='1'"></xsl:when>

         <xsl:otherwise>
    <!-- vor jedem item eine leerzeile als durchschuss-->
              <w:p>
                <xsl:choose>
    <!-- nach wechsel des anfangsbuchstabens einen größeren durchschuss anbringen -->
                 <xsl:when test="@level='1' and lemma/@sortchart!=preceding-sibling::item[1]/lemma/@sortchart">
                   <w:pPr>
                     <w:pStyle w:val="epi-leerzeile-18pt"/>
                     <!--<w:keepNext w:val="on"/>-->
                   </w:pPr>
                 </xsl:when>
    <!-- anderenfalls nur einen kleinen durchschuss -->
                  <xsl:otherwise><w:pPr><w:pStyle w:val="epi-leerzeile-5pt"/></w:pPr></xsl:otherwise>
               </xsl:choose>
              </w:p>

        <w:p>
          <w:pPr>
            <xsl:if test="@level='1'"><w:ind w:left="400" w:hanging="400"/></xsl:if>
            <xsl:if test="@level='2'"><w:ind w:left="400"/></xsl:if>
            <xsl:if test="@level='3'"><w:ind w:left="800"/></xsl:if>
            <xsl:if test="@level='4'"><w:ind w:left="1200"/></xsl:if>
          </w:pPr>
          <!-- titel auwählen, wenn er nicht mit einem punkt endet, einen punkt anhängen -->
          <w:r>
             <w:t><xsl:value-of select="lemma"/><xsl:if test="not(item)"><xsl:if test="not(substring(lemma,last())='.')">.</xsl:if></xsl:if></w:t>
          </w:r>
        </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!--      <xsl:if test="@level='1' and following-sibling::item">
            <w:p>
              <w:pPr><w:pStyle w:val="epi-leerzeile-5pt"/></w:pPr>
            </w:p>
          </xsl:if> -->
    </xsl:otherwise>
    </xsl:choose>
    </xsl:for-each>
  </xsl:template>


  <xsl:template match="di_baende">
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>di-bände anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:copy-of select="$odd-page"/>

      <!-- titel einfügen -->
      <w:p>
        <w:pPr><w:pStyle w:val="epi-ueberschrift-2"/></w:pPr>
    <!-- sprungmarke einfügen und titel auslesen -->
          <aml:annotation w:type="Word.Bookmark.Start">
              <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
              <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
          </aml:annotation>
            <w:r><w:t><xsl:value-of select="titel"/></w:t></w:r>
          <aml:annotation w:type="Word.Bookmark.End">
              <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
          </aml:annotation>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <!-- tabelle transformieren -->
      <w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <w:tblGrid>
          <w:gridCol w:w="861"/>
          <w:gridCol w:w="7266"/>
        </w:tblGrid>
        <xsl:for-each select="table/row">
          <w:tr>
            <w:trPr>
              <w:cantSplit w:w="on"/>
            </w:trPr>
            <w:tc>
              <w:tcPr><w:tcW w:w="861" w:type="dxa"/></w:tcPr>
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-tabellenzeile"/>
                </w:pPr>
                <xsl:apply-templates select="cell[1]"/>
              </w:p>
            </w:tc>
            <w:tc>
              <w:tcPr><w:tcW w:w="7266" w:type="dxa"/></w:tcPr>
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-tabellenzeile"/>
                </w:pPr>
                <xsl:apply-templates select="cell[2]"/>
              </w:p>
            </w:tc>
          </w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
      </w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </wx:sect>
    <xsl:comment>di-bände ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="bildtafeln">
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>bildtafeln anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wx:sect>
    <w:p><w:r><w:br w:type="page"/></w:r></w:p>
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!--<w:suppressLineNumbers/>-->
        <xsl:copy-of select="$odd-page"/>
      <!-- überschrift  -->
      <w:p>
        <w:pPr>
          <w:pStyle w:val="epi-ueberschrift-1"/>
          <w:spacing w:before="4800"/>
          <w:suppressLineNumbers/>
        </w:pPr>
  <!-- sprungmarke einfügen und titel auslesen -->
        <aml:annotation w:type="Word.Bookmark.Start">
            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
            <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
        </aml:annotation>
          <w:r><w:t><xsl:value-of select="titel"/></w:t></w:r>
        <aml:annotation w:type="Word.Bookmark.End">
            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
        </aml:annotation>
      </w:p>
       <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:comment>bildnachweise anfang</xsl:comment>
       <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:for-each select="bildnachweis">

      <w:p><w:r><w:br w:type="page"/></w:r></w:p>
      <w:p>
        <w:pPr><w:pStyle w:val="epi-ueberschrift-2"/></w:pPr>
        <w:r><w:t><xsl:value-of select="titel"/></w:t></w:r>
      </w:p>
      <xsl:for-each select="content/p">
        <w:p>
          <w:pPr>
            <xsl:choose>
              <xsl:when test="node()"><w:pStyle w:val="epi-normal-1"/></xsl:when>
              <xsl:otherwise><w:pStyle w:val="epi-leerzeile-10pt1"/></xsl:otherwise>
            </xsl:choose>
            <xsl:if test="@indent='1'">
              <w:ind w:first-line="240"/>
            </xsl:if>
          </w:pPr>
          <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
        </w:p>
      </xsl:for-each>
      </xsl:for-each>
       <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:comment>bildnachweise ende</xsl:comment>
       <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>bildtafeln ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="zeichnungen">
    <xsl:if test="$zeichnungen=1">
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>zeichnungen anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <w:p>
        <w:pPr><w:pStyle w:val="epi-ueberschrift-1"/></w:pPr>
        <!-- neue seite -->
        <w:r><w:br w:type="page"/></w:r>
  <!-- sprungmarke einfügen und titel auslesen -->
        <aml:annotation w:type="Word.Bookmark.Start">
            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
            <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
        </aml:annotation>
          <w:r><w:t><xsl:value-of select="titel"/></w:t></w:r>
        <aml:annotation w:type="Word.Bookmark.End">
            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
        </aml:annotation>
      </w:p>
      <xsl:apply-templates select="*[not(name()='titel')]"></xsl:apply-templates>
    </wx:sect>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>zeichnungen ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
  </xsl:template>

<xsl:template match="maps">
        <xsl:if test="$zeichnungen=1">
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>grundrisse anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <w:p>
        <w:pPr><w:pStyle w:val="epi-ueberschrift-2"/></w:pPr>
        <!-- neue seite -->
        <w:r><w:br w:type="page"/></w:r>
  <!-- sprungmarke einfügen und titel auslesen -->
        <aml:annotation w:type="Word.Bookmark.Start">
            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
            <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
        </aml:annotation>
          <w:r><w:t><xsl:value-of select="titel"/></w:t></w:r>
        <aml:annotation w:type="Word.Bookmark.End">
            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
        </aml:annotation>
      </w:p>
      <xsl:apply-templates select="*[not(name()='titel')]"></xsl:apply-templates>
    </wx:sect>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>grundrisse ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
</xsl:template>

<xsl:template match="map/note/p">
<w:p><xsl:apply-templates></xsl:apply-templates></w:p>
</xsl:template>

<xsl:template match="map">
        <xsl:if test="$zeichnungen=1">
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>grundrisse anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <w:p>
        <w:pPr><w:pStyle w:val="epi-ueberschrift-2"/></w:pPr>
        <!-- neue seite -->
        <w:r><w:br w:type="page"/></w:r>
  <!-- sprungmarke einfügen und titel auslesen -->
        <aml:annotation w:type="Word.Bookmark.Start">
            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
            <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
        </aml:annotation>
          <w:r><w:t><xsl:value-of select="map_title"/></w:t></w:r>
        <aml:annotation w:type="Word.Bookmark.End">
            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
        </aml:annotation>
      </w:p>
      <xsl:apply-templates select="*[not(name()='titel')]"></xsl:apply-templates>
    </wx:sect>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>grundrisse ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
</xsl:template>

<xsl:template match="concordance">
    <w:tbl>
        <xsl:for-each select="row">
<xsl:sort select="map_object_number" data-type="number"/>
            <w:tr>
                <w:tc>
                    <w:tcPr><w:tcW w:w="500" w:type="dxa"/></w:tcPr>
                    <w:p><w:r><w:t><xsl:value-of select="map_object_number"/></w:t></w:r></w:p>
                </w:tc>
                <w:tc>
                    <w:tcPr><w:tcW w:w="1200" w:type="dxa"/></w:tcPr>
                    <w:p><w:r><w:t><xsl:value-of select="article_number"/></w:t></w:r></w:p>
                </w:tc>
            </w:tr>
        </xsl:for-each>
    </w:tbl>


</xsl:template>


  <xsl:template match="marken">
    <xsl:if test="$marken=1">
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>marken anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <w:p>
        <w:pPr><w:pStyle w:val="epi-ueberschrift-2"/></w:pPr>
        <!-- neue seite -->
        <w:r><w:br w:type="page"/></w:r>
  <!-- sprungmarke einfügen und titel auslesen -->
        <aml:annotation w:type="Word.Bookmark.Start">
            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
            <xsl:attribute name="w:name"><xsl:value-of select="@id"/></xsl:attribute>
        </aml:annotation>
          <w:r><w:t><xsl:value-of select="titel"/></w:t></w:r>
        <aml:annotation w:type="Word.Bookmark.End">
            <xsl:attribute name="aml:id"><xsl:value-of select="@id"/></xsl:attribute>
        </aml:annotation>
      </w:p>
      <!-- die abschnitte 1. meisterzeichen und 2. hausmarken werden jeweils angesteuert-->
      <xsl:for-each select="abschnitt">
        <xsl:variable name="pfad"><xsl:value-of select="pfad"/></xsl:variable>
        <w:p>
          <xsl:choose>
            <xsl:when test="titel">
              <w:pPr><w:pStyle w:val="epi-ueberschrift-3z"/></w:pPr>
              <!-- titel auslesen -->
              <w:r><w:t><xsl:value-of select="titel"/></w:t></w:r>
            </xsl:when>
            <xsl:otherwise>
              <w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr>
            </xsl:otherwise>
          </xsl:choose>

        </w:p>
        <!-- tabelle anlegen -->
        <w:tbl>
          <w:tblPr>
            <w:tblW w:w="8900" w:type="dxa"/>
            <w:tblInd w:w="0" w:type="dxa" />
            <w:tblCellMar>
              <w:left w:w="0" w:type="dxa"/>
              <w:right w:w="0" w:type="dxa"/>
            </w:tblCellMar>
            <w:tblLook w:val="04A0"/>
          </w:tblPr>

          <xsl:for-each select="m-group">
            <w:tr>
              <w:trPr>
                <!-- 1 mm = 56,6928 twip; 46 mm = 2620 twip; 44 mm = 2495; 44,3 = 2511,496062992 -->
                <!-- höhe des satzspiegels 297mm - 30mm - 45,5mm = 221,5mm
                  auf fünf zeilen je zeile 44,3 mm = = 2511,496062992 twips-->
                <w:trHeight w:val="2511"/>
              </w:trPr>
              <xsl:for-each select="marke">
                <xsl:apply-templates select=".">
                  <xsl:with-param name="pfad"><xsl:value-of select="$pfad"/></xsl:with-param>
                </xsl:apply-templates>
              </xsl:for-each>
            </w:tr>
          </xsl:for-each>

        </w:tbl>
      </xsl:for-each>

    </wx:sect>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>marken ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
  </xsl:template>

  <xsl:template match="marke">
    <xsl:param name="bezeichnung"><xsl:value-of select="name"/></xsl:param>
    <xsl:param name="artikelnummern1">
      <xsl:for-each select="ancestor::book/catalog/article[.//link[@type='marke'][@data-link-value=$bezeichnung]]">
        <artikelnummer><xsl:number from="catalog" count="article" format="1"/></artikelnummer>
      </xsl:for-each>
    </xsl:param>
    <xsl:param name="artikelnummern2">
      <xsl:for-each select="exsl:node-set($artikelnummern1)/artikelnummer">
        <xsl:value-of select="."/><xsl:if test="following-sibling::artikelnummer"><xsl:text>, </xsl:text></xsl:if>
      </xsl:for-each>
    </xsl:param>

<!-- parameter zu prüfzwecken ausgeben -->
<!--
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>marken-id: <xsl:copy-of select="$bezeichnung"/></xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>artikelnummern1: <xsl:copy-of select="$artikelnummern1"/></xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>artikelnummern2: <xsl:copy-of select="$artikelnummern2"/></xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
 -->
    <w:tc>
      <w:tcPr>
        <w:tcW w:w="3000" w:type="dxa"/>
      </w:tcPr>
      <w:p wsp:rsidR="00841147" wsp:rsidRPr="000D3B78" wsp:rsidRDefault="009713D8" wsp:rsidP="000D3B78">
        <w:pPr>
          <w:spacing w:line="240" w:line-rule="auto"/>
          <w:jc w:val="left"/>
          <w:rPr>
            <w:noProof/>
            <w:sz w:val="24"/>
            <w:sz-cs w:val="24"/>
          </w:rPr>
        </w:pPr>
        <w:r wsp:rsidRPr="000D3B78">
          <w:rPr>
            <w:noProof/>
            <w:sz w:val="24"/>
            <w:sz-cs w:val="24"/>
          </w:rPr>

<!-- auf epigrafWeb gespeicherte markenbilder -->
          <w:pict>
            <v:shape id="_x0000_i1037" type="#_x0000_t75" style="width:71pt;height:71pt;visibility:visible;mso-wrap-style:square;mso-position-horizontal:absolute">
              <v:imagedata o:title="marke">
                <xsl:attribute name="src">
                  <xsl:value-of select="concat('wordml://',file_name,'.png')" />
                </xsl:attribute>
              </v:imagedata>
              <o:lock v:ext="edit" aspectratio="f"/>
              <w10:bordertop type="single" width="4"/>
              <w10:borderleft type="single" width="4"/>
              <w10:borderbottom type="single" width="4"/>
              <w10:borderright type="single" width="4"/>
            </v:shape>
            <!--<xsl:call-template name="markenbilder"></xsl:call-template>-->

<!-- das element <marke/> für lokale transformation auskommentieren -->
<marke>
<file_name><xsl:value-of select="file_name"/></file_name>
</marke>

          </w:pict>


        </w:r>
      </w:p>
      <!-- markenlegende -->
      <w:p>
        <w:pPr><w:pStyle w:val="epi-marken"/></w:pPr>
        <w:r>
          <w:rPr>
            <w:bdr w:val="single" w:sz="4" wx:bdrwidth="10" w:space="1" w:color="auto"/>
          </w:rPr>
             <!-- bei zusammengefassten markenkategorien wird für die bezeichnung der marke als sigle vor der laufnummer ein M eingefügt;
   bei getrennten kategorien wird der erse buchstabe der bezeichnung des markentyps eingefügt-->
          <xsl:choose>
            <xsl:when test="$markentypen_zusammenfassen = 1">
              <w:t>M<xsl:value-of select="nr"/></w:t>
            </xsl:when>
            <xsl:otherwise><w:t><xsl:value-of select="substring(properties_markentyp_id/lemma,1,1)"/><xsl:value-of select="nr"/></w:t> </xsl:otherwise>
          </xsl:choose>

        </w:r>
        <!--<xsl:choose>
          <xsl:when test="contains(ancestor::abschnitt/@name, 'Haus')">
            <w:r>
              <w:rPr>
                <w:bdr w:val="single" w:sz="4" wx:bdrwidth="10" w:space="1" w:color="auto"/>
              </w:rPr>
              <w:t>H </w:t>
              <w:t><xsl:value-of select="nr"/></w:t>
            </w:r>
          </xsl:when>
          <xsl:when test="contains(ancestor::abschnitt/@name, 'Meister')">
            <w:r>
              <w:rPr>
                <w:bdr w:val="single" w:sz="4" wx:bdrwidth="10" w:space="0" w:color="auto"/>
              </w:rPr>
              <w:t>M </w:t>
              <w:t><xsl:value-of select="nr"/></w:t>
            </w:r>
          </xsl:when>
        </xsl:choose>-->

        <!-- katalognummern -->
        <w:r><w:t> Kat.-Nr. <xsl:value-of select="$artikelnummern2"/></w:t>
        </w:r>
      </w:p>
    </w:tc>

  </xsl:template>

  <!--Aufbau des Artikels / composition of article-->
  <xsl:template match="article">
    <xsl:comment>artikelanfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wx:sub-section> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <!-- vor jedem außer dem ersten artikel wird eine hohe leerzeile eingefügt -->
      <xsl:if test="preceding-sibling::article">
        <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-22pt"/></w:pPr></w:p>
      </xsl:if>
      <xsl:for-each select="*">
        <xsl:apply-templates select="."/>
      </xsl:for-each>
      <xsl:comment>artikelende</xsl:comment>
       <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </wx:sub-section> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

    <!-- die kopzeile hat die breite von 85 x den buchstaben n (=mittelbreiter buchstabe)
      die kopfzeile ist 8107 twips breit, das geteilt durch 85 ergibt pro n 95,3 twips ;
      um die für den standort verfügbare länge zu berechnen, muss man von der breite der kopfzeile
      die länge des datierungsstrings und die länge der artikelnummer mit sigle abziehen;
      um die position des mittleren tabulators zu errechnenm nuss man die verfügbare standortbreite halbieren
      und dazu die länge des nummerierungsstrings addieren;
      die so ermittelte zahl muss mit den twips je n (=95) multipliziert werden

    -->
  <xsl:template match="kopfzeile">
    <xsl:param name="usedstring"><xsl:value-of select="string-length(datierung) + string-length(artikelnummer) + string-length(trad_sigle)"/></xsl:param>
    <xsl:param name="nrstring"><xsl:value-of select="string-length(artikelnummer) + string-length(trad_sigle)"/></xsl:param>
    <xsl:param name="diffstring"><xsl:value-of select="85 - $usedstring"/></xsl:param>
    <xsl:param name="pos1"><xsl:value-of select="$diffstring * 0.5"/></xsl:param>
    <xsl:param name="pos2"><xsl:value-of select="$pos1 + $nrstring"/></xsl:param>
    <xsl:param name="pos3"><xsl:value-of select="$pos2 * 95"/></xsl:param>

     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<!--<test>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:copy-of select="$usedstring"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:copy-of select="$nrstring"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:copy-of select="$diffstring"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:copy-of select="$pos1"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:copy-of select="$pos2"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:copy-of select="$pos3"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</test>-->
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!-- kopfzeile und signatur-->
    <xsl:comment>kopfzeile anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!-- oberer balken -->
    <wx:pBdrGroup>
      <wx:borders>
        <wx:top wx:val="solid" wx:bdrwidth="14" wx:space="1" wx:color="auto"/>
      </wx:borders>
      <w:p>
        <w:pPr><w:pStyle w:val="epi-kopfzeile-balken-oben"/></w:pPr>
      </w:p>
    </wx:pBdrGroup>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

    <w:p>
      <w:pPr>
        <w:pStyle w:val="epi-kopfzeile"/>
        <w:tabs>
           <w:tab w:val="center" w:pos="3686">
            <xsl:attribute name="w:pos">
              <xsl:value-of select="$pos3"/>
            </xsl:attribute>
          </w:tab>
          <w:tab w:val="right" w:pos="8110"/>
        </w:tabs>
      </w:pPr>
      <!-- sprungmarke für die links aus den registern und den querverweisen -->
      <!-- anfang: sprungmarke -->
      <aml:annotation w:type="Word.Bookmark.Start">
        <xsl:attribute name="aml:id"><xsl:value-of select="ancestor::article/@id"/></xsl:attribute>
        <xsl:attribute name="w:name"><xsl:value-of select="ancestor::article/@id"/></xsl:attribute>
      </aml:annotation>
      <!-- artikelnummer und sigle -->
      <w:r><w:t><xsl:value-of select="concat(artikelnummer,' ',trad_sigle)"/></w:t></w:r>
      <aml:annotation w:type="Word.Bookmark.End">
        <xsl:attribute name="aml:id"><xsl:value-of select="ancestor::article/@id"/></xsl:attribute>
      </aml:annotation>
      <!-- ende: sprungmarke -->

      <!-- standort -->
      <w:r>
        <w:tab/>
        <xsl:for-each select="standorte/standort">
        <w:t><xsl:value-of select="translate(.,'{}','')"/><xsl:if test="following-sibling::standort"><xsl:text>, </xsl:text></xsl:if></w:t>
        </xsl:for-each>
      </w:r>

      <!-- datierung -->
      <w:r>
        <w:tab/><w:t><xsl:value-of select="datierung"/></w:t>
      </w:r>
    </w:p>

    <!-- unterer balken -->
    <wx:pBdrGroup>
      <wx:borders>
        <wx:bottom wx:val="solid" wx:bdrwidth="15" wx:space="1" wx:color="auto"/>
      </wx:borders>
      <w:p>
        <w:pPr><w:pStyle w:val="epi-kopfzeile-balken-unten"/></w:pPr>
      </w:p>
    </wx:pBdrGroup>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>kopfzeile ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!--Ende Abschnitt 1: Kopfzeile zwischen Balken / header between leaders-->

    <!-- signatur -->
    <xsl:comment>signatur anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <w:p>
      <w:pPr>
        <w:pStyle w:val="epi-leerzeile-10pt"/>
        <w:keepNext w:val="on"/>
      </w:pPr>
      <!--Signatur einfuegen-->
      <xsl:if test="$signatur=1">
        <w:r><w:rPr><w:vertAlign w:val="superscript"/></w:rPr><w:t><xsl:value-of select="signatur" /></w:t></w:r>
      </xsl:if>
    </w:p>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>signatur ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>



  <xsl:template match="beschreibung">
    <xsl:comment>beschreibung anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <w:p><w:pPr>
        <xsl:choose>
            <xsl:when test="@mode='verse' and $modus='projects_bay'"><w:pStyle w:val="epi-apparat"/></xsl:when>
            <xsl:otherwise><w:pStyle w:val="epi-beschreibung"/></xsl:otherwise>
        </xsl:choose>
        </w:pPr><xsl:apply-templates/></w:p>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!-- leerzeile -->
    <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>beschreibung ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="gliederung1">
    <xsl:comment>gliederung-1 anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <w:p><w:pPr><w:pStyle w:val="epi-gliederung-1"/></w:pPr><xsl:apply-templates/></w:p>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>gliederung-1 ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="gliederung2">
    <xsl:comment>gliederung-2 anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <w:p><w:pPr><w:pStyle w:val="epi-gliederung-2"/></w:pPr><xsl:apply-templates/></w:p>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>gliederung-2 ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="gliederung3">
    <xsl:comment>gliederung-3 anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <w:p><w:pPr><w:pStyle w:val="epi-gliederung-3"/></w:pPr><xsl:apply-templates/></w:p>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>gliederung-3 ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="numbering">
    <xsl:comment>numbering1 anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <w:p><w:pPr><w:pStyle w:val="epi-beschreibung"/></w:pPr><xsl:apply-templates/></w:p>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>numbering1 ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="angaben">
    <!--Abschnitt 3: Vorlagen, Objekteigenschaften, Schrifthoehen und Schriftarten / various characteristics-->
<!--    <xsl:param name="angaben">
      <xsl:for-each select="*[node()]">
        <xsl:apply-templates select="."/>
        <xsl:if test="following-sibling::*[text()]"><xsl:text>&#x20;&#x2013;&#x20;</xsl:text></xsl:if>
      </xsl:for-each>
    </xsl:param>-->


    <xsl:comment>angaben anfang</xsl:comment>
    <!-- wenn angaben oder verweise auf abbildungsnummern vorhanden sind -->
    <xsl:if test="*[node()]">
    <!-- marginalie am äußeren rand für die abbildungsnummern -->
    <wx:pBdrGroup>
      <wx:apo>
        <wx:width wx:val="849"/>
        <wx:jc wx:val="right"/>
        <wx:vertFromText wx:val="850"/>
        <wx:horizFromText wx:val="200"/>
      </wx:apo>
      <w:p>
        <w:pPr>
          <w:pStyle w:val="epi-marginalie"/>
          <!--<wx:jc wx:val="inside"/>-->
          <!--<w:framePr w:w="1190" w:hspace="200" w:vspace="850" w:wrap="not-beside" w:vanchor="text" w:hanchor="page" w:x-align="outside" w:y="1"/>-->
        </w:pPr>
        <w:r>
          <!-- wird ausgeblendet wenn keine abbildungen referenziert sind -->
          <xsl:if test="not(abbildungen[node()])">
          <w:rPr><w:vanish/></w:rPr>
          </xsl:if>
          <w:t>Abb. <xsl:value-of select="abbildungen"/></w:t>
        </w:r>
      </w:p>
    </wx:pBdrGroup>
    <!-- nun die angaben -->
    <xsl:if test="node()">
        <xsl:choose>
            <xsl:when test="$modus='projects_bay'">
              <w:p>
                <w:pPr><w:pStyle w:val="epi-beschreibung"/></w:pPr>
                <xsl:for-each select="*[name()='abmessungen' or name()='schrifthoehen' or name()='schriftarten']">
                  <xsl:apply-templates/>
                  <xsl:if test="following-sibling::*[name()='abmessungen' or name()='schrifthoehen' or name()='schriftarten']"><w:r><w:t><xsl:text>&#x00A0;&#x2013;&#x0020;</xsl:text></w:t></w:r></xsl:if>
                </xsl:for-each>
              </w:p>
                <xsl:if test="*[name()='abmessungen' or name()='schrifthoehen' or name()='schriftarten']">
                    <xsl:if test="*[name()='inschrift_nach' or name()='ergaenzung_nach' or name()='angaben_sonstiges']">
                          <!-- leerzeile-->
                          <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:if>
                </xsl:if>
              <w:p>
                <w:pPr><w:pStyle w:val="epi-beschreibung"/></w:pPr>
                <xsl:for-each select="*[name()='inschrift_nach' or name()='ergaenzung_nach' or name()='angaben_sonstiges']">
                  <xsl:apply-templates/>
                  <xsl:if test="following-sibling::*[name()='inschrift_nach' or name()='ergaenzung_nach' or name()='angaben_sonstiges']"><w:r><w:t><xsl:text>&#x00A0;&#x2013;&#x0020;</xsl:text></w:t></w:r></xsl:if>
                </xsl:for-each>
              </w:p>
        </xsl:when>
        <xsl:otherwise>
              <w:p>
                <w:pPr><w:pStyle w:val="epi-beschreibung"/></w:pPr>
                <xsl:for-each select="*[not(name()='abbildungen')][not(contains(name(),'dio'))][string-length() &gt; 1][not(starts-with(name(),'x'))]">
                  <xsl:apply-templates/>
                  <xsl:if test="following-sibling::*[not(name()='abbildungen')][not(contains(name(),'dio'))][string-length() &gt; 1]"><w:r><w:t><xsl:text>&#x00A0;&#x2013;&#x0020;</xsl:text></w:t></w:r></xsl:if>
                </xsl:for-each>
              </w:p>
        </xsl:otherwise>
        </xsl:choose>
       <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <!-- leerzeile-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
    </xsl:if>
    <xsl:comment>angaben ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

    <!-- abmessungen nur provisorische lösung - müsste wie das feld beschreibung formatiert werden
    Bindestriche werden durch bis-Striche (Halbgeviertstriche) ersetzt-->
    <xsl:template match="abmessungen/text()"><w:r><w:t><xsl:value-of select="translate(.,'-','–')"/></w:t></w:r></xsl:template>

    <xsl:template match="schriftarten/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
    <xsl:template match="schriftarten/links"><w:r><w:t><xsl:call-template name="linkSerie"></xsl:call-template></w:t></w:r></xsl:template>

    <xsl:template match="schrifthoehen/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
    <xsl:template match="schrifthoehen/links"><w:r><w:t><xsl:call-template name="linkSerie"></xsl:call-template></w:t></w:r></xsl:template>

    <xsl:template match="inschrift_nach/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
    <xsl:template match="inschrift_nach/links"><w:r><w:t><xsl:call-template name="linkSerie"></xsl:call-template></w:t></w:r></xsl:template>

    <xsl:template match="angaben_sonstiges/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>

    <xsl:template match="ergaenzung_nach/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
    <xsl:template match="ergaenzung_nach/links"><w:r><w:t><xsl:call-template name="linkSerie"></xsl:call-template></w:t></w:r></xsl:template>


  <xsl:template match="uebersetzungen">
    <xsl:comment>übersetzungen anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:choose>
    <xsl:when test="ancestor::article/optionen/option[@iri='di_translations_sectionwise']"></xsl:when>
<xsl:otherwise>
      <!--1. pruefen, ob Uebersetzungen vorliegen-->
    <xsl:if test="uebersetzung">
      <!--2. Template fuer die Uebersetzungen einfuegen-->
      <xsl:for-each select="uebersetzung"><w:p><w:pPr><w:pStyle w:val="epi-uebersetzung1"/></w:pPr><xsl:apply-templates select="."/></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:for-each>
      <!--leerzeile einfuegen-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
</xsl:otherwise>
</xsl:choose>

    <xsl:comment>übersetzungen ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="verse">
    <xsl:comment>versmaße anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!-- 1. testen ob Verse vorliegen-->
    <xsl:if test="item">
      <!-- 2. Template fuer die Versschemata einfuegen-->
      <xsl:for-each select="item">
        <w:p>
          <w:pPr>
            <xsl:choose>
                <xsl:when test="$modus='projects_bay'"><w:pStyle w:val="epi-apparat"/></xsl:when>
                <xsl:otherwise><w:pStyle w:val="epi-verse"/></xsl:otherwise>
            </xsl:choose>
            </w:pPr>
          <w:r><w:t><xsl:apply-templates select="content"/><xsl:if test="links/link"><xsl:text> (</xsl:text><xsl:for-each select="links"><xsl:call-template name="linkSerie"></xsl:call-template></xsl:for-each><xsl:text>)</xsl:text></xsl:if><xsl:text>.</xsl:text></w:t></w:r>

        </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:for-each>
      <!-- 3. Leerzeile einschieben-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>versmaße ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="section[@kategorie='Zitatquellen']">
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>zitatquellen - anfang</xsl:comment>
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:if test="node()">
      <!--2. Template fuer Kommentare einfuegen-->
      <w:p><w:pPr><w:pStyle w:val="epi-apparat"/></w:pPr><xsl:apply-templates/></w:p>
      <!--3. Leerzeile einschieben-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
    </xsl:if>
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>zitatquellen - ende</xsl:comment>
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="section[starts-with(@kategorie, 'Datum')]">
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>datum in der inschrift - anfang</xsl:comment>
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:if test="node()">
      <!--2. Template fuer Kommentare einfuegen-->
      <w:p><w:pPr><w:pStyle w:val="epi-beschreibung"/></w:pPr><xsl:apply-templates/></w:p>
      <!--3. Leerzeile einschieben-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
    </xsl:if>
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>datum in der inschrift - ende</xsl:comment>
         <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>



  <xsl:template match="kommentar">
    <xsl:comment>kommentar anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!--Abschnitt 8: Kommentare / comments-->
    <!--1. nach Kommentaren testen-->
    <xsl:if test="node()">
      <!--2. Template fuer Kommentare einfuegen-->
      <w:p><w:pPr><w:pStyle w:val="epi-beschreibung"/></w:pPr><xsl:apply-templates/></w:p>
      <!--3. Leerzeile einschieben-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
    </xsl:if>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>kommentar ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="apparate">
    <xsl:param name="count_ziffern"><xsl:value-of select="count(ziffernapparat/item)"/></xsl:param>
    <xsl:param name="count_buchstaben"><xsl:value-of select="count(buchstabenapparat/item)"/></xsl:param>
    <xsl:comment>fußnoten anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!--Abschnitt 9: Fussnoten / footnotes-->
    <!--1. nach Fussnoten testen-->
    <xsl:if test="*/*">
    <xsl:apply-templates select="buchstabenapparat">
      <xsl:with-param name="count_ziffern"><xsl:value-of select="$count_ziffern"/></xsl:with-param>
      <xsl:with-param name="count_buchstaben"><xsl:value-of select="$count_buchstaben"/></xsl:with-param>
    </xsl:apply-templates>
    <xsl:apply-templates select="ziffernapparat">
      <xsl:with-param name="count_ziffern"><xsl:value-of select="$count_ziffern"/></xsl:with-param>
      <xsl:with-param name="count_buchstaben"><xsl:value-of select="$count_buchstaben"/></xsl:with-param>
    </xsl:apply-templates>
    <!--2. Leerzeile einschieben-->
    <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
    <xsl:comment>fußnoten ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="buchstabenapparat">
    <xsl:param name="count_ziffern"></xsl:param>
    <xsl:param name="count_buchstaben"></xsl:param>
    <xsl:comment>buchstabenapparat anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!--Teil 1: Buchstabenapparat-->
    <!--a) nach Buchstabenapparat testen-->
    <xsl:if test="item">
      <!--c) Fussnoten einfuegen als Tabelle-->
      <w:tbl><w:tblPr><w:tblStyle w:val="Tabellengitternetz"/><w:tblW w:w="0" w:type="auto"/><w:tblInd w:w="0" w:type="dxa"/><w:tblBorders>
        <w:top w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/><w:left w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/><w:bottom w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/><w:right w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/><w:insideH w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/><w:insideV w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/></w:tblBorders><w:tblLook w:val="01E0"/></w:tblPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="item"><w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

          <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <w:tcPr>
              <w:tcMar>
                <w:bottom w:w="30" w:type="dxa"/>

          <!-- wenn die fußnotenbuchstaben rechtsbündig gesetzt werden sollen,
            wird der rechte rand der vorderne spalte entsprechend angepasst -->

              <xsl:if test="$fußnoten = 1">
                <!-- anpassung des rechten zellenrandes an die breite der zahlen -->
                <xsl:choose>
                  <!-- einstellig schmal , d.h. unter m -->
                  <xsl:when test="$count_buchstaben &lt; 13 and $count_ziffern &lt; 10">
                    <w:right w:w="200" w:type="dxa"/>
                  </xsl:when>
                  <!-- einstellig breit -->
                  <xsl:when test="$count_buchstaben &lt; 27 and $count_buchstaben &gt; 12">
                    <w:right w:w="150" w:type="dxa"/>
                  </xsl:when>
                  <xsl:when test="$count_ziffern &gt; 99">
                    <w:right w:w="30" w:type="dxa"/>
                  </xsl:when>
                  <!-- zweistellig -->
                  <xsl:otherwise>
                    <w:right w:w="100" w:type="dxa"/>
                  </xsl:otherwise>
                </xsl:choose>
              </xsl:if>

              </w:tcMar>
              <w:tcW w:w="360" w:type="dxa"/>
            </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-apparat"/>
                <w:ind w:left="0" w:first-line="0"/>
                <!-- buchstaben wahlweise rechtsbündig setzen -->
                <xsl:if test="$fußnoten = 1">
                  <w:jc w:val="right"/>
                </xsl:if>
            </w:pPr>
              <w:r><w:t><xsl:number level="any" count="item" from="buchstabenapparat" format="a) "/></w:t></w:r></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

          <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <w:tcPr><w:tcMar><w:bottom w:w="30" w:type="dxa"/></w:tcMar><w:tcW w:w="7740" w:type="dxa"/></w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <w:p><w:pPr><w:pStyle w:val="epi-apparat"/><w:ind w:left="0" w:first-line="0"/></w:pPr><xsl:apply-templates/></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          </w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
      </w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:if test="following-sibling::ziffernapparat[item]">
      <!--d) wenn ein ziffernapparat folgt niedrige zwischenzeile einschieben-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-9pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
      </xsl:if>
    <xsl:comment>buchstabenapparat ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>


  <xsl:template match="link[@type='footnotes']">
    <xsl:variable name="target_fussnote"><xsl:value-of select="@data-link-target"/></xsl:variable>
    <xsl:choose>
        <xsl:when test="ancestor::einleitung">
    <xsl:for-each select="ancestor::einleitung//app1[@id=$target_fussnote]">
          <w:r><w:t><xsl:number level="any" count="app1" from="einleitung" format="1"/></w:t></w:r>
        </xsl:for-each>
    </xsl:when>
    <xsl:otherwise>
    <xsl:for-each select="ancestor::article/apparate//item[@id=$target_fussnote]">
          <xsl:choose>
            <xsl:when test="parent::buchstabenapparat">
              <w:r><w:t><xsl:number level="any" count="item" from="buchstabenapparat" format="a"/></w:t></w:r>
            </xsl:when>
            <xsl:when test="parent::ziffernapparat">
              <w:r><w:t><xsl:number level="any" count="item" from="ziffernapparat" format="1"/></w:t></w:r>
            </xsl:when>
          </xsl:choose>
        </xsl:for-each>
    </xsl:otherwise>
    </xsl:choose>

  </xsl:template>

  <xsl:template match="link[@type='inschrift']">
    <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
  </xsl:template>
  <xsl:template match="link[@type='sections']">
    <!--<w:r><w:t><xsl:value-of select="."/></w:t></w:r>-->
        <xsl:apply-templates></xsl:apply-templates>
  </xsl:template>

  <xsl:template match="link[@type='sections']/text()">
    <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
  </xsl:template>

  <xsl:template match="inscriptlink">
<xsl:choose>
    <xsl:when test="ancestor::apparate"><w:r><w:rPr><w:sz w:val="15"/></w:rPr><w:t><xsl:value-of select="."/></w:t></w:r></xsl:when>
    <xsl:otherwise><w:r><w:rPr><w:sz w:val="16"/></w:rPr><w:t><xsl:value-of select="."/></w:t></w:r></xsl:otherwise>
</xsl:choose>

  </xsl:template>

  <xsl:template match="link[@type='literatur']">
    <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
  </xsl:template>




  <xsl:template match="link[@type='footnotes_extern']">
    <xsl:variable name="v_target_fussnote"><xsl:value-of select="@data-link-target"/></xsl:variable>
    <!-- artikelnummer auslesen -->
    <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
    <!-- fußnotenbezeichner ermitteln -->
    <xsl:for-each select="ancestor::book/catalog/article/apparate//item[@id=$v_target_fussnote]">
      <xsl:choose>
        <xsl:when test="parent::buchstabenapparat">
          <w:r><w:t>, Anm. <xsl:number level="any" count="item" from="buchstabenapparat" format="a"/></w:t></w:r>
        </xsl:when>
        <xsl:when test="parent::ziffernapparat"><test>test</test>
          <w:r><w:t>, Anm. <xsl:number level="any" count="item" from="ziffernapparat" format="1"/></w:t></w:r>
        </xsl:when>
      </xsl:choose>
    </xsl:for-each>
  </xsl:template>

  <xsl:template match="ziffernapparat">
    <xsl:param name="count_ziffern"></xsl:param>
    <xsl:param name="count_buchstaben"></xsl:param>
    <xsl:comment>ziffernapparat anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <!--a) nach Ziffernapparat testen-->
    <xsl:if test="item">
      <!--c) Fussnoten einfuegen als tabelle-->
      <w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <w:tblPr>
          <w:tblStyle w:val="Tabellengitternetz"/>
          <w:tblW w:w="0" w:type="auto"/>
          <w:tblInd w:w="0" w:type="dxa"/>
          <w:tblBorders>
            <w:top w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:left w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:bottom w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:right w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:insideH w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:insideV w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
          </w:tblBorders>
          <w:tblLook w:val="01E0"/>
        </w:tblPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="item">
          <w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <w:tcPr>
                <w:tcMar>
                  <w:bottom w:w="30" w:type="dxa"/>

                  <!-- wenn die fußnotennummern rechtsbündig gesetzt werden sollen,
                  wird der rechte rand der vorderen spalte entsprechend angepasst -->
                  <xsl:if test="$fußnoten = 1">
                  <!-- anpassung des rechten zellenrandes an die breite der zahlen -->
                  <xsl:choose>
                    <!-- einstellig, d.h. unter zehn -->
                    <xsl:when test="$count_ziffern &lt; 10">
                      <w:right w:w="200" w:type="dxa"/>
                    </xsl:when>
                    <!-- zweistellig -->
                    <xsl:when test="$count_ziffern &lt; 100 and $count_ziffern &gt; 9">
                      <w:right w:w="100" w:type="dxa"/>
                    </xsl:when>
                    <!-- dreistellig -->
                    <xsl:otherwise>
                      <w:right w:w="30" w:type="dxa"/>
                    </xsl:otherwise>
                  </xsl:choose>
                </xsl:if>

                </w:tcMar>
                <w:tcW w:w="360" w:type="dxa"/>
              </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-apparat"/>
                  <w:ind w:left="0" w:first-line="0"/>
                  <!-- ziffern wahlweise rechtsbündig setzen -->
                  <xsl:if test="$fußnoten = 1">
                    <w:jc w:val="right"/>
                  </xsl:if>
                </w:pPr>
                <!--<xsl:comment>test fussnote</xsl:comment>-->
                <w:r>
                  <w:t><xsl:number level="any" count="item" from="ziffernapparat" format="1) "/></w:t>
                </w:r>
              </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

            <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <w:tcPr><w:tcMar><w:bottom w:w="30" w:type="dxa"/></w:tcMar><w:tcW w:w="7740" w:type="dxa"/></w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <xsl:for-each select="p">
<w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <w:pPr><w:pStyle w:val="epi-apparat"/><w:ind w:left="0" w:first-line="0"/></w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:apply-templates/></w:p> </xsl:for-each>

<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          </w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
      </w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
    <xsl:comment>ziffernapparat ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="nachweise">
    <xsl:if test="item">
    <xsl:comment>nachweise anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <w:pPr><w:pStyle w:val="epi-nachweis"/></w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="item"><xsl:apply-templates select="."/>
            <xsl:if test="following-sibling::item">
             <xsl:choose>
<!-- münchener reihe als trennzeichen ei semikolon -->
                 <xsl:when test="$modus='projects_bay'"><w:r><w:t><xsl:text>;&#x0020;</xsl:text></w:t></w:r></xsl:when>
<!-- andere reihen als trennzeichen ein halbeviertstrich -->
                 <xsl:otherwise><w:r><w:t><xsl:text>&#x00A0;&#x2013;&#x0020;</xsl:text></w:t></w:r></xsl:otherwise>
             </xsl:choose>
            </xsl:if>
        </xsl:for-each>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <!--2. Leerzeile einschieben-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
    <xsl:comment>nachweise ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>

  <xsl:template match="notizen">
    <xsl:comment>notizen anfang</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:if test="$datum=1 or $notizen=1">
      <w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <w:pPr>
          <w:pStyle w:val="epi-leerzeile-10pt"/>
        </w:pPr>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <wx:pBdrGroup>
        <wx:borders>
          <wx:bottom wx:val="solid" wx:bdrwidth="15" wx:space="1" wx:color="auto"/>
        </wx:borders>
        <w:p>
          <w:pPr>
            <w:pBdr>
              <w:bottom w:val="dotted" w:sz="6" wx:bdrwidth="15" w:space="1" w:color="auto"/>
            </w:pBdr>
            <w:spacing w:line="100" w:line-rule="exact"/>
          </w:pPr>
        </w:p>
      </wx:pBdrGroup> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>

    <!--letzte Änderung-->
    <xsl:if test="$datum=1">
      <!--Zwischenzeile-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <w:p><w:pPr><w:pStyle w:val="epi-notiz"/></w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <w:r><w:rPr><w:u w:val="single"/></w:rPr><w:t>Letzte Änderung: </w:t></w:r><w:r><w:t><xsl:value-of select="ancestor::article/kopfzeile/modified"/>&#x0020;(<xsl:value-of select="ancestor::article/kopfzeile/modified_by/@displayvalue"/>)</w:t></w:r></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>

    <!--Notizen / notes-->
    <xsl:if test="$notizen=1">
      <!--Zwischenzeile-->
      <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <!--Ueberschrift-->
      <w:p><w:pPr><w:pStyle w:val="epi-notiz"/></w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <w:r><w:rPr><w:u w:val="single"/></w:rPr><w:t>Notizen:</w:t></w:r>></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:for-each select="notiz">
        <w:p><w:pPr><w:pStyle w:val="epi-notiz"/></w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <xsl:apply-templates select="zuordnung"/></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:for-each select="notiztext">
          <w:p><w:pPr><w:pStyle w:val="epi-notiz"/></w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:apply-templates/></w:p>
        </xsl:for-each> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <!--Zwischenzeile-->
        <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:for-each>
       <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:comment>notizen ende</xsl:comment>
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
  </xsl:template>


<!--===Ende: KATALOG / Catalogue============================================-->


<!--===== allgemeine Formate ======-->
<!--alle Textknoten werden in 'runs' getagt-->
<xsl:template match="text()">
  <xsl:choose>
    <xsl:when test="ancestor::vermerke or
      ancestor::notiz or
      ancestor::gliederung1 or
      ancestor::gliederung2 or
      ancestor::gliederung3 or
      ancestor::numbering or
      ancestor::zuordnung or
      ancestor::nachweise or
      ancestor::nachweisliste or
      ancestor::bl or
      ancestor::beschreibung or
      ancestor::section[starts-with(@kategorie, 'Datum')] or
      ancestor::section[@kategorie='Zitatquellen'] or
      ancestor::kommentar or
      ancestor::ziffernapparat or
      ancestor::buchstabenapparat or
      ancestor::uebersetzung or
      ancestor::wappen or
      ancestor::schriftarten or
      ancestor::p or
      ancestor::h or
      ancestor::cell or
      ancestor::td or
      ancestor::sup or
      ancestor::app1">
    <w:r>
      <w:rPr>
        <!-- versionsbuchstaben in der nummerierung der uebersetzungen -->
        <xsl:if test="parent::version">
          <w:rStyle w:val="epi-version"/>
        </xsl:if>
        <!--Kursivierung-->
        <xsl:if test="ancestor::quot or parent::quot">
          <w:rStyle w:val="epi-zitat"/>
        </xsl:if>
        <xsl:if test="ancestor::i or parent::i">
          <w:rStyle w:val="epi-zitat"/>
        </xsl:if>
        <xsl:if test="parent::zuordnung"> <!-- überschrift in notizen -->
          <w:i/>
        </xsl:if>
        <xsl:if test="ancestor::bl[@align='Überschrift' or @align='Bildtitel']">
          <w:i/>
        </xsl:if>
        <!--Versalien-->
        <xsl:if test="parent::vsl and $versalien='1'">
          <w:rStyle w:val="epi-versalien"/>
        </xsl:if>
        <!--Initialen-->
        <xsl:if test="parent::ini">
          <w:rStyle w:val="epi-initialen"/>
        </xsl:if>
        <!--hochgestellte Buchstaben-->
        <xsl:if test="parent::sup">
          <xsl:choose>
            <xsl:when test="ancestor::nachweise or ancestor::app1 or ancestor::ziffernapparat">
              <w:rStyle w:val="epi-folium"/>
            </xsl:when>
            <xsl:otherwise>
              <!--<w:rStyle w:val="epi-hochgestellt"/>-->
              <w:position w:val="8"/>
              <w:sz w:val="16"/>
            </xsl:otherwise>
          </xsl:choose>
        </xsl:if>
        <!--Kapitaelchen-->
        <xsl:if test="parent::kap">
          <w:rStyle w:val="epi-kapitaelchen"/>
          <xsl:if test="ancestor::quot">
            <w:i/>
          </xsl:if>
        </xsl:if>
        <!-- Verweise auf Inschriften nach Verweisen auf Artikelnummern -->
        <xsl:if test="parent::inscriptlink">
          <w:rStyle w:val="epi-inscriptlink"/>
        </xsl:if>
        <!--Buchstabenverbindungen-->
        <xsl:if test="parent::all">
          <w:rStyle w:val="epi-ligatur"/>
          <xsl:if test="ancestor::quot">
            <w:i/>
          </xsl:if>
        </xsl:if>
        <!--Chronogrammbuchstaben-->
        <xsl:if test="parent::chr">
          <w:rStyle w:val="epi-chronogramm"/>
          <xsl:if test="ancestor::quot">
            <w:i/>
          </xsl:if>
        </xsl:if>
        <!-- gesperrt -->
        <xsl:if test="parent::g">
          <w:rStyle w:val="epi-gesperrt"/>
        </xsl:if>
        <!-- fett -->
        <xsl:if test="parent::b">
          <w:b/>
        </xsl:if>
        <!-- versalien -->
        <xsl:if test="parent::vsl and ancestor::article/optionen/option[@name='Versalien darstellen' or @lemma='2']">
          <w:rStyle w:val="epi-versalien"/>
        </xsl:if>
      </w:rPr>
      <w:t>
        <xsl:choose>
          <xsl:when test="string-length != 1 and starts-with(.,' ')">
            <xsl:value-of select="substring-after(.,' ')" />
          </xsl:when>
          <xsl:otherwise>
            <xsl:value-of select="." />
          </xsl:otherwise>
        </xsl:choose>
        <!-- bei inmittelbar aufeinander folgenden Ligaturen
          wird in die unterstrichung ein kleiner senkrechtet Strich U+0329 gesetzt;
          um die  horizontale ausrichtung über w:spacing zu steuern,
          wird für das zeichen ein eigener run eingefügt
        -->
        <xsl:if test="parent::all and following::node()[1][self::all]">
          <xsl:text disable-output-escaping="yes">&lt;/w:t&gt;&lt;/w:r&gt;</xsl:text>
          <w:r>
            <w:rPr><w:spacing w:val="-15"/></w:rPr>
            <w:t>&#x0329;</w:t>
           </w:r>
          <xsl:text disable-output-escaping="yes">&lt;w:r&gt;&lt;w:t&gt;</xsl:text>
        </xsl:if>
      </w:t>
    </w:r>
  </xsl:when>
  <xsl:otherwise>
   <xsl:value-of select="." />
  </xsl:otherwise>
 </xsl:choose>
</xsl:template>

  <xsl:template match="insec[not(wtr)]|wunsch[@tagname='insec']">
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
    <xsl:if test="$Zaehler &lt;= $Ende">
      <w:r>
        <!-- wenn unsichere lesung in einem zitatat auftritt, wird kursiviert -->
        <xsl:if test="ancestor::quot">
          <w:rStyle w:val="epi-zitat"/>
        </xsl:if>
<!-- erhöhung der laufweite bei ziffern, damit der punkt unter der ziffer und nicht rechts daneben steht -->
<xsl:if test="substring(.,$Zaehler,1)='1' or
                    substring(.,$Zaehler,1)='2' or
                    substring(.,$Zaehler,1)='3' or
                    substring(.,$Zaehler,1)='4' or
                    substring(.,$Zaehler,1)='5' or
                    substring(.,$Zaehler,1)='6' or
                    substring(.,$Zaehler,1)='7' or
                    substring(.,$Zaehler,1)='8' or
                    substring(.,$Zaehler,1)='9' or
                    substring(.,$Zaehler,1)='0'
                    ">
<xsl:choose>
<!-- in fußnoten -->
    <xsl:when test="ancestor::item"><w:rPr><w:spacing w:val="25"/></w:rPr></xsl:when>
<!-- im trankriptionsfeld -->
    <xsl:otherwise><w:rPr><w:spacing w:val="30"/></w:rPr></xsl:otherwise>
</xsl:choose>

</xsl:if>
        <!-- die zeichen innerhalb des bereichs unsicherer lesung werden einzeln angesteuert
          und jeweils danach ein unterpunkt (&#x0323;) aus dem unicode-bereich der combinierbaren
          diakritischen zeichen gesetzt -->
        <w:t><xsl:value-of select="substring(.,$Zaehler,1)"/></w:t></w:r><w:r><w:t><xsl:text>&#x0323;</xsl:text></w:t></w:r>
      <xsl:call-template name="InsecSchleife">
        <xsl:with-param name="Zaehler" select="$Zaehler + 1" />
        <xsl:with-param name="Ende" select="$Ende" />
      </xsl:call-template>
    </xsl:if>
  </xsl:template>


<!--==Ausgelagerte Stylesheets / imported stylesheets=======================-->

<!--Unsichere Lesung-->
<!--<xsl:template match="insec[not(wtr)]"><xsl:apply-templates/></xsl:template>-->
<!--Worttrenner-->






  <!--==ENDE: ausgelagerte Stylesheets / imported stylesheets=================-->

<!--==UNTERGEORDNETE TEMPLATES / sub-templates==============================-->

<!--benanntes Muster fuer die Datierungsspalte / datings-->
<xsl:template name="spalte_links"><xsl:param name="breite"/><xsl:value-of select="$breite * 8"/>pt</xsl:template>
<xsl:template name="spalte_rechts"><xsl:param name="breite"/><xsl:value-of select="$breite * 5"/>pt</xsl:template>



<!--Transkriptionenspalten / transcription collumns-->
<xsl:template match="transkriptionsspalten">
  <xsl:param name="anzahl_spalten"><xsl:value-of select="count(inschrift)"/></xsl:param>
  <xsl:comment>transkription anfang</xsl:comment>
<!--1. Transkriptionszeile-->
  <!-- für jedes element transkriptionsspalten wird eine tabelle angelegt;
    die erste spalte enthält die nummerierungen,
    für jede inschrift resp. bearbeitung wird eine weitere spalte angelegt,
    die inschriftenteile werden übersprungen -->
  <w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <!-- erste spalte für die nummerierung -->
      <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <w:tcPr>
          <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
          <w:tcW w:w="750" w:type="dxa"/>
        </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <w:p><w:pPr><w:pStyle w:val="epi-transkription"/></w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

          <w:r><w:t><xsl:value-of select="inschrift[1]//nr"/>–<xsl:value-of select="inschrift[last()]//nr"/></w:t></w:r>

        </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <!-- für jede inschrift eine spalte anlegen -->
      <xsl:for-each select="inschrift//bearbeitung">
           <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <w:tcPr>
              <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
              <xsl:choose>
                <xsl:when test="$anzahl_spalten=2">
                  <!--<w:tcW w:w="3400" w:type="dxa"/>-->
                  <w:tcW w:w="3583" w:type="dxa"/>
                </xsl:when>
                  <xsl:when test="$anzahl_spalten=3">
                  <!--<w:tcW w:w="2266" w:type="dxa"/>-->
                    <w:tcW w:w="2390" w:type="dxa"/>
                </xsl:when>
              </xsl:choose>

            </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <xsl:apply-templates select="content"/>
           </w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:for-each>
    </w:tr>
  </w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

  <!--2. Zwischenzeile-->
  <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  <xsl:comment>transkription ende</xsl:comment>
   <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>



<!--Transkriptionen / transcriptions-->
<xsl:template match="inschrift">
  <xsl:comment>transkription anfang</xsl:comment>
<!--1. Transkriptionszeile-->
  <!-- für jede inschrift wird eine tabelle mit zwei spalten angelegt;
  für jede bearbeitung eine zeile, die inschriftenteile werden übersprungen -->
  <w:tbl>
          <w:tblPr>
            <w:tblW w:w="7920" w:type="dxa"/>
            <w:tblCellMar>
                <w:left w:w="10" w:type="dxa"/>
                <w:right w:w="10" w:type="dxa"/>
            </w:tblCellMar>
          </w:tblPr>
        <w:tblGrid>
            <w:gridCol w:w="370"/>
            <w:gridCol w:w="7358"/>
        </w:tblGrid>
 <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:for-each select="inschriftenteil">
            <xsl:if test="ergaenzung[node()]">
<!-- anstelle der nummerierung bei den münchener bänden wird der text aus dem feld ergaenzung ausgelesen und als eingen zeile über der inschrift eingefügt -->
            <w:tr>
            <w:tc>
            <w:tcPr>
                <w:tcW w:w="370" w:type="dxa"/>
                <w:gridSpan w:val="2"/>
                </w:tcPr>
            <w:p><w:pPr><w:pStyle w:val="epi-transkription"/></w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                <w:r><w:t><xsl:value-of select="ergaenzung"/></w:t></w:r></w:p></w:tc></w:tr>
            </xsl:if>
        <xsl:choose>
            <!-- synoptische darstellung von zwei bearbeitungen nebeneinander -->
            <xsl:when test="ancestor::article/optionen/option[@iri='di_synopsis'] and bearbeitung[following-sibling::bearbeitung]">
             <!-- zeile für die bearbeitung anlegen -->

              <w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <!-- linke spalte für die nummerierung -->
                <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <w:tcPr>
                    <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
                    <w:tcW w:w="370" w:type="dxa"/>
                  </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <w:p><w:pPr><w:pStyle w:val="epi-transkription"/></w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <w:r><w:t><xsl:value-of select="bearbeitung/nr"/></w:t></w:r>
<!--                    <xsl:if test="preceding-sibling::bearbeitung or following-sibling::bearbeitung">
                      <w:r>
                        <w:rPr>
                          <w:vertAlign w:val="superscript"/>
                          <w:u w:val="single" w:color="black"/>
                        </w:rPr>
                        <w:t><xsl:value-of select="version"/></w:t>
                      </w:r>
                    </xsl:if>-->
                  </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

                <!--  eine spalte für jede bearbeitungen -->
                <xsl:for-each select="bearbeitung">
                <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <w:tcPr>
                    <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
                    <w:tcW w:w="3600" w:type="dxa"/>
                  </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <xsl:apply-templates select="content"/></w:tc>
</xsl:for-each>


 <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              </w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:when>


            <!-- fortlaufende darstellung der bearbeitungen untereinander -->
           <xsl:otherwise>

            <xsl:for-each select="bearbeitung">
              <!-- zeile für die bearbeitung anlegen -->
              <w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <!-- linke spalte für die nummerierung -->
                <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <w:tcPr>
                    <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
                    <w:tcW w:w="370" w:type="dxa"/>
                  </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <w:p><w:pPr><w:pStyle w:val="epi-transkription"/></w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <w:r><w:t><xsl:if test="not(parent::inschriftenteil/ergaenzung)">
<xsl:choose>
    <xsl:when test="$modus='projects_bay'"><xsl:value-of select="nr/@nr_bay"/><xsl:if test="string-length(nr/@nr_bay) !=0"><xsl:text>.</xsl:text></xsl:if></xsl:when>
    <xsl:otherwise><xsl:value-of select="nr"/></xsl:otherwise>
</xsl:choose>

</xsl:if></w:t></w:r>
                    <xsl:if test="preceding-sibling::bearbeitung or following-sibling::bearbeitung">
                      <w:r>
                        <w:rPr>
                          <w:vertAlign w:val="superscript"/>
                          <w:u w:val="single" w:color="black"/>
                        </w:rPr>
                        <w:t><xsl:value-of select="version"/></w:t>
                      </w:r>
                    </xsl:if>
                  </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <!-- rechte spalte für den text -->
                <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <w:tcPr>
                    <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
                    <w:tcW w:w="7200" w:type="dxa"/>
                  </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <xsl:apply-templates select="content"/></w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              </w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>




              <xsl:if test="following-sibling::bearbeitung">
                <w:tr><w:tc>
                  <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </w:tc></w:tr>
              </xsl:if>
            </xsl:for-each>
          </xsl:otherwise>
        </xsl:choose>

<xsl:if test="ancestor::article/optionen/option[@iri='di_translations_sectionwise']">
<w:tr>
<w:tc>
<w:tcPr>
                    <w:tcMar><w:bottom w:w="100" w:type="dxa"/></w:tcMar>
                    <w:tcW w:w="500" w:type="dxa"/>
                  </w:tcPr>
<w:p></w:p>
</w:tc>
<w:tc>
<w:tcPr>
                    <w:tcMar>
<!--<w:bottom w:w="120" w:type="dxa"/>
<w:left w:w="380" w:type="dxa"/>
<w:top w:w="60" w:type="dxa"/>-->
</w:tcMar>

                  </w:tcPr>
<w:p>
<w:pPr>
<w:pStyle w:val="epi-uebersetzung2"/>
</w:pPr>
<w:r><w:t><xsl:apply-templates select="translation"></xsl:apply-templates></w:t></w:r>
</w:p></w:tc>
</w:tr>
</xsl:if>


    </xsl:for-each>
  </w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

  <!--2. Zwischenzeile-->
  <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  <xsl:comment>transkription ende</xsl:comment>
   <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

  <xsl:template match="content"><xsl:apply-templates/></xsl:template>

<xsl:template match="bearbeitung">
  <xsl:choose>
    <xsl:when test="bl"><xsl:apply-templates/></xsl:when>
    <xsl:otherwise>
      <w:p>
        <w:pPr>
          <w:pStyle w:val="epi-transkription"/>
        </w:pPr>
        <xsl:apply-templates/>
      </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:otherwise>
  </xsl:choose>

</xsl:template>

<!--Bereiche in den Transkriptionen / sub-areas in transcription area-->
<xsl:template match="bl">
  <!-- transkriptions-bereichen in fussnoten wird ein schließendes p-tag vorangesetzt -->
  <xsl:if test="ancestor::apparate and not(starts-with(@align,'Sprache'))">
    <xsl:text disable-output-escaping="yes">&#x003C;/w:p&#x003E;</xsl:text>
  </xsl:if>
<!-- es wird unterschieden zwischen zeilengerechter ausgabe bei versen und fortlaufender ausgabe -->
  <xsl:choose>
    <!-- bei verszeilen wird eine weitere, innere tabelle angelegt;
    sie besteht aus zwei spalten, die linke für die zeilennummerierung, die rechte für den text-->
    <xsl:when test="vz and not(@value='with_marg_left')">
     <w:tbl>
     <xsl:for-each select="vz[node()]">
        <!-- vorbereitung der zeilennummerierung in zwei variablen -->
       <xsl:variable name="zeilenanzahl">
         <xsl:for-each select="parent::bl">
           <xsl:value-of select="count(vz[text()])"/>
         </xsl:for-each>
       </xsl:variable>
       <xsl:variable name="zeilennummer">
         <xsl:number level="single" count="vz[text()]" from="bl" format="1"/>
       </xsl:variable>

      <w:tr>
        <!-- linke spalte für zeilennummern (wenn mehr als 5 zeilen) -->
        <w:tc>
          <w:tcPr>
            <w:tcMar>
              <!--<w:top w:w="40" w:type="dxa"/>-->
              <w:right w:w="150" w:type="dxa"/>
            </w:tcMar>
            <w:tcW w:w="350" w:type="dxa"/>
          </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <w:p>
            <w:pPr>
              <w:pStyle w:val="epi-zeilennummer"/>
            </w:pPr>
            <xsl:if test="$zeilenanzahl &gt; 10">
              <xsl:if test="$zeilennummer mod 5 =0">
            <w:r><w:t><xsl:number count="vz[text()]" from="bl" format="1"/></w:t></w:r>
                </xsl:if>
            </xsl:if>
          </w:p>
        </w:tc>

      <!-- rechte spalte für den text -->
<xsl:if test="@indent='1'">
      <w:tc>
       <w:p>
         <w:pPr>
           <w:pStyle w:val="epi-pentameter"/>
           <!-- wenn der vorhergehende bl eine Überschrift oder einen Bildtitel enthält, folgt auf die letzte Zeile ein Durchschuss -->
           <xsl:if test="parent::bl[preceding-sibling::bl[@align='Überschrift' or @align='Bildtitel']] and not(following-sibling::vz)">
             <w:spacing w:after="60"/>
           </xsl:if>
         </w:pPr>
         <xsl:apply-templates/>
       </w:p>
      </w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:if>
<xsl:if test="@indent='0'">
  <w:tc>
       <w:p>
        <w:pPr>
          <w:pStyle w:val="epi-transkription"/>
          <w:spacing w:left="-60"/>
          <!-- wenn der vorhergehende bl eine Überschrift oder einen Bildtitel enthält, folgt auf die letzte Zeile ein Durchschuss -->
          <xsl:if test="parent::bl[preceding-sibling::bl[@align='Überschrift' or @align='Bildtitel']] and not(following-sibling::vz)">
            <w:spacing w:after="60"/>
          </xsl:if>
        </w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
         <xsl:apply-templates/>
       </w:p>
  </w:tc>
   <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:if>

      </w:tr>

     </xsl:for-each>

    </w:tbl>
      <!-- zwischenzeile -->
      <xsl:if test="not(ancestor::apparate)">
        <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:if>

    </xsl:when>

    <xsl:when test="@value='with_marg_left'">
       <xsl:variable name="zeilenanzahl">
           <xsl:value-of select="count(zeile)"/>
       </xsl:variable>
        <w:tbl>
            <xsl:for-each select="zeile">
                   <xsl:variable name="zeilennummer">
                     <xsl:number count="zeile" from="bl" format="1"/>
                   </xsl:variable>
                    <xsl:variable name="laenge"><xsl:value-of select="string-length(marg)"/></xsl:variable>
<!--test laenge: <xsl:copy-of select="$laenge"/>-->
                <!-- spalte für die zeilennummern -->
                <w:tr>
                            <w:tc>
                              <w:tcPr>
                                <w:tcMar>
                                  <!--<w:top w:w="40" w:type="dxa"/>-->
                                  <w:right w:w="150" w:type="dxa"/>
                                </w:tcMar>
                                <w:tcW w:w="350" w:type="dxa"/>
                              </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                              <w:p>
                                <w:pPr>
                                  <w:pStyle w:val="epi-zeilennummer"/>
                                </w:pPr>
                                 <xsl:if test=".//vz">
                                   <xsl:if test="$zeilenanzahl &gt; 10">
                                     <xsl:if test="$zeilennummer mod 5 =0">
                                        <w:r><w:t><xsl:number count="zeile" from="bl" format="1"/></w:t></w:r>
                                    </xsl:if>
                                </xsl:if>
                                </xsl:if>
                              </w:p>
                            </w:tc>
                    <!-- spalte für die marginalien -->
                    <w:tc>
                    <w:tcPr><xsl:if test="string-length(marg) &gt; 15"><w:vmerge w:val="restart"/></xsl:if>
                                    <xsl:if test="string-length(preceding-sibling::zeile[1]/marg) &gt; 15 and not(marg)"><w:vmerge/></xsl:if>
                                    <w:tcW w:w="1400" w:type="dxa"/>
                </w:tcPr>
                        <w:p>
                                <w:pPr><w:pStyle w:val="epi-transkription"/> <w:ind w:right="113"/></w:pPr>
                                <xsl:apply-templates select="marg" mode="marg1"></xsl:apply-templates>
                        </w:p>
                    </w:tc>
                    <!-- spalte für die textzeilen -->
                    <w:tc>
                           <w:p>
                                <w:pPr><w:pStyle w:val="epi-transkription"/><xsl:if test="vz[@value='einger&#xFC;ckt' or @data-link-value='einger&#xFC;ckt']"> <w:ind w:left="240"/></xsl:if></w:pPr>
                               <xsl:apply-templates></xsl:apply-templates>
                           </w:p>
                    </w:tc>
                </w:tr>
            </xsl:for-each>
        </w:tbl>
        <w:p></w:p>
    </xsl:when>

    <xsl:when test="contains(@align, 'Sprache')"><xsl:apply-templates/></xsl:when>

    <!-- absätze ohne verse erhalten keine zeilennummern und
      werden um die breite der spalte für zeilennummern nach links gerückt;
    außer wenn ganze inschriften nebeneinander in spalten stehen (transkriptionsspalten)-->
    <xsl:otherwise>
      <w:p>
        <w:pPr>
          <w:pStyle w:val="epi-transkription"/>
          <xsl:if test="not(ancestor::transkriptionsspalten)"><w:ind w:left="350"/></xsl:if>
          <xsl:if test="ancestor::transkriptionsspalten"><w:ind w:right="350"/></xsl:if>
        </w:pPr>
        <xsl:apply-templates/></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:otherwise>
  </xsl:choose>
  <!-- transkriptions-bereichen in fussnoten wird ein öffnendes p-tag nachgeseztz -->
  <xsl:if test="ancestor::apparate and not(starts-with(@align,'Sprache'))">
    <xsl:text disable-output-escaping="yes">&#x003C;w:p&#x003E;</xsl:text>
      <w:pPr><w:pStyle w:val="epi-apparat"/><w:ind w:left="0" w:first-line="0"/></w:pPr>
  </xsl:if>

</xsl:template>

<xsl:template match="marg"></xsl:template>
 <xsl:template match="marg" mode="marg1"><xsl:apply-templates></xsl:apply-templates></xsl:template>


<!--Wappentabelle-->
<xsl:template match="heraldik">
  <xsl:if test="node()">
  <xsl:comment>wappen anfang</xsl:comment>
   <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  <w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <w:tblPr>
      <w:tblStyle w:val="Tabellengitternetz"/>
      <w:tblW w:w="0" w:type="auto"/>
      <w:tblBorders>
        <w:top w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
        <w:left w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
        <w:bottom w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
        <w:right w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
        <w:insideH w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
        <w:insideV w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
      </w:tblBorders>
      <w:tblLook w:val="01E0"/>
    </w:tblPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <w:tcPr>
          <w:tcMar>
            <w:bottom w:w="20" w:type="dxa"/>
          </w:tcMar>
          <w:tcW w:w="980" w:type="dxa"/>
        </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <w:p>
          <w:pPr><w:pStyle w:val="epi-wappen"/></w:pPr>
          <w:r><w:t><xsl:value-of select="bezeichnung"/></w:t></w:r>
        </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:apply-templates select="tabelle"/>
        <w:p><w:pPr><w:pStyle w:val="epi-wappen"/></w:pPr></w:p>/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
   <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  <!--leerzeile einschieben-->
  <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  <xsl:comment>wappen ende</xsl:comment>
   <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:if>
</xsl:template>

<xsl:template match="heraldik/tabelle">
  <w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:for-each select="zeile">
      <w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:apply-templates select="."/>
      </w:tr>
    </xsl:for-each>
  </w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

<xsl:template match="heraldik/tabelle/zeile">
  <xsl:for-each select="zelle">
    <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <w:tcPr>
        <w:tcMar><w:bottom w:w="20" w:type="dxa"/></w:tcMar>
    </w:tcPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:apply-templates select="."/>
    </w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:for-each>
</xsl:template>

<xsl:template match="heraldik/tabelle/zeile/zelle">
  <w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <w:pPr>
      <w:pPr><w:pStyle w:val="epi-wappen"/></w:pPr>
      <xsl:if test="preceding-sibling::zelle"><w:ind w:left="100"/></xsl:if>
      <w:ind w:right="100"/>
    </w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<xsl:for-each select="entry">
<w:r><w:t><xsl:value-of select="wappen" /></w:t></w:r><xsl:apply-templates select="app1"/>
<xsl:if test="position() != last()"><w:r><w:t>,<xsl:text>&#x0020;</xsl:text></w:t></w:r></xsl:if>
</xsl:for-each>
 <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>


<!--    <xsl:for-each select="wappen"><w:r><w:t><xsl:value-of select="text()" /></w:t></w:r>
    <xsl:apply-templates select="app1"/>
    <xsl:if test="position() != last()"><w:r><w:t>,<xsl:text>&#x0020;</xsl:text></w:t></w:r></xsl:if>
    </xsl:for-each><xsl:apply-templates select="app1"/></w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>-->
</xsl:template>


<!--Fussnotenzeichen im Text / footnote characters-->
  <!--1. der Zahlenapparat font-size="7pt"-->
<xsl:template match="app1">
  <xsl:choose>
    <xsl:when test="ancestor::article">
      <w:r>
        <w:rPr>
          <w:rStyle w:val="epi-fussnotenzeichen"/>
          <!-- in kursiven absätzen wird das fußnotenzeichen recte ausgegeben -->
          <w:i w:val="off"/>
        </w:rPr>
        <w:t><xsl:number level="any" from="article" count="app1" format="1)"/></w:t>
      </w:r>
    </xsl:when>
    <xsl:otherwise>
      <w:r>
        <w:rPr>
          <w:rStyle w:val="epi-fussnotenzeichen"/>
        </w:rPr>
        <w:footnote>
          <xsl:for-each select="p">
          <w:p>
            <w:pPr>
              <w:pStyle w:val="epi-apparat"/>
            </w:pPr>
           <xsl:if test="not(preceding-sibling::p)">
              <w:r>
              <w:rPr>
                <w:rStyle w:val="epi-fussnotenzeichen"/>
              </w:rPr>
                <w:footnoteRef/><!--<w:t>) </w:t>-->
            </w:r>
             <w:r><w:tab/></w:r>
            </xsl:if>
            <xsl:apply-templates></xsl:apply-templates>
          </w:p>
          </xsl:for-each>
        </w:footnote>
        <!--<w:t>) </w:t>-->
      </w:r>
    </xsl:otherwise>
  </xsl:choose>

</xsl:template>
  <!--2. der Buchstabenapparat font-size="7pt"-->
<xsl:template match="app2"><w:r><w:rPr><w:vertAlign w:val="superscript"/></w:rPr><w:t><xsl:number level="any" from="article" count="app2" format="a)"/></w:t></w:r></xsl:template>


<!--Ende: Transkriptionsformate / transcriptions=============================-->

<!-- verweise auf literatur
  <xsl:template match="rec[@type='biblio']">
    <w:r><w:t><xsl:value-of select="@text"/></w:t></w:r>
  </xsl:template>  -->

<!--Verweise auf andere Datensaetze/Artikel / references to inscriptions in other articles
<xsl:template match="rec[@type='extern']"><xsl:param name="signatur_referenz"><xsl:value-of select="@text"/></xsl:param><xsl:choose><xsl:when test="//datensatz[@signatur=$signatur_referenz]"><w:r><w:t>Nr.&#x20;<xsl:value-of select="//datensatz[@signatur=$signatur_referenz]//nr"/></w:t></w:r></xsl:when><xsl:otherwise><w:r><w:t><xsl:value-of select="@text"/></w:t></w:r></xsl:otherwise></xsl:choose></xsl:template>-->


<!--Verweise auf Hausmarken-->
<xsl:template match="link[@type='marke']">
  <xsl:variable name="marke-bezeichnung">
    <xsl:value-of select="@data-link-value"/>
  </xsl:variable>
  <xsl:for-each select="ancestor::book//marken//marke[name[text()=$marke-bezeichnung]]">
   <!-- bei zusammengefassten markenkategorien wird für die bezeichnung der marke als sigle  vor der laufnummer ein M eingefügt;
   bei getrennten kategorien wird der erse buchstabe der bezeichnung des markentyps eingefügt-->
    <xsl:choose>
    <xsl:when test="$markentypen_zusammenfassen = 1">
      <w:r><w:t>M<xsl:value-of select="nr"/></w:t></w:r>
    </xsl:when>
    <xsl:otherwise><w:r><w:t><xsl:value-of select="substring(@brandtype-name,1,1)"/><xsl:value-of select="nr"/></w:t></w:r></xsl:otherwise>
  </xsl:choose>
  </xsl:for-each>
</xsl:template>



<!--Verweise auf Meisterzeichen-->
<!--<xsl:template match="rec_mz">
  <xsl:variable name="marke-bezeichnung"><xsl:value-of select="."/></xsl:variable>
  <w:r><w:t><xsl:text>M</xsl:text><xsl:value-of select="ancestor::book//marken//marke[bezeichnung[text()=$marke-bezeichnung]]/nr" /></w:t></w:r>
</xsl:template>-->

<!--Verweise auf Inschriften-->
<xsl:template match="rec[@type='intern']">
	<xsl:choose>
		<xsl:when test="ancestor::nachweise"><xsl:value-of select="@text"/></xsl:when>
		<xsl:when test="ancestor::vers"></xsl:when>
		<xsl:otherwise><w:r><w:t><xsl:value-of select="@text"/></w:t></w:r></xsl:otherwise>
	</xsl:choose>
</xsl:template>


<!--Spalten im Transkriptionsfeld / columns in transcription area-->
<!-- neben den zutreffenden Attribute müssen auch bl als Kindknoten vorhanden sein -->
<xsl:template match="content/bl[@align='Spalten' or @data-link-value='Spalten'][bl]">
  <w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

    <w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:for-each select="bl">

        <w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

<xsl:choose>
    <xsl:when test="vz">
<xsl:for-each select="vz[position()!=1]">
<w:p>
               <w:pPr>
                 <w:pStyle w:val="epi-transkription"/>

                <xsl:if test="position()!=last()">
                <w:ind w:right="400"/>
                </xsl:if>

                   <w:ind w:left="350"/>

              </w:pPr>
            <xsl:apply-templates/>
          </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:for-each>
</xsl:when>
<xsl:otherwise>
<w:p>
               <w:pPr>
                 <w:pStyle w:val="epi-transkription"/>

                <xsl:if test="position()!=last()">
                <w:ind w:right="400"/>
                </xsl:if>
                 <xsl:if test="position()= 1">
                   <w:ind w:left="350"/>
                 </xsl:if>
              </w:pPr>
            <xsl:apply-templates/>
          </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:otherwise>
</xsl:choose>



        </w:tc> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:for-each>
    </w:tr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </w:tbl> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  <w:p/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:template>

  <!--Überschriften im Transkriptionsfeld / headers in transcription area-->
  <xsl:template match="transkription/bl[@align='Überschrift' or @align='Bildtitel']">
    <w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <w:pPr>
                <w:ind w:left="200"/>
                <!--<w:spacing w:before="240" w:after="60"/>-->
              </w:pPr> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <xsl:apply-templates/>
    </w:p> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
  </xsl:template>


<!--Ende: Spalten im Trankriptionsfeld / columns in the transcription area-->

  <!--Zeilenumbrueche und Absaetze in Kommentaren, Beschreibungen, Vorwort, Einleitung usw.: ausser in Transkriptionen / various line breaks-->
  <xsl:template match="nl[not(parent::bl)][ancestor::beschreibung or ancestor::kommentar or ancestor::gliederung1 or ancestor::gliederung2 or ancestor::gliederung3 or ancestor::notizen or ancestor::apparate or ancestor::uebersetzung]">
    <xsl:param name="test1">
      <xsl:for-each select="preceding-sibling::*[name()='bl' or name()='nl'][1]">
        <xsl:value-of select="name()"/>
      </xsl:for-each>
    </xsl:param>
    <xsl:param name="test2">
      <xsl:for-each select="following-sibling::*[name()='bl' or name()='nl'][1]">
        <xsl:value-of select="name()"/>
      </xsl:for-each>
    </xsl:param>
    <xsl:choose>
      <xsl:when test="$test1='bl'"><xsl:comment>test nl 1</xsl:comment></xsl:when>
      <xsl:when test="$test2='bl'"><xsl:comment>test nl 2</xsl:comment></xsl:when>
      <xsl:otherwise>
        <!--<xsl:comment>test nl 3</xsl:comment>-->
        <xsl:text disable-output-escaping="yes">&#x003C;/w:p&#x003E;&#x003C;w:p&#x003E;</xsl:text>
        <!--<xsl:comment>test nl 4</xsl:comment>-->
      </xsl:otherwise>
    </xsl:choose>
<!--    <xsl:if test="not(following-sibling::bl[1]) and not(preceding-sibling::bl[1])">
    <xsl:text disable-output-escaping="yes">&#x003C;/w:p&#x003E;&#x003C;w:p&#x003E;</xsl:text>
    </xsl:if>-->
    <xsl:if test="ancestor::apparate">
      <w:pPr><w:pStyle w:val="epi-apparat"/><w:ind w:left="0" w:first-line="0"/></w:pPr>
    </xsl:if>
    <xsl:if test="ancestor::beschreibung or ancestor::kommentar">
      <w:pPr><w:pStyle w:val="epi-beschreibung"/></w:pPr>
    </xsl:if>
    <xsl:if test="ancestor::uebersetzung">
      <w:pPr><w:pStyle w:val="epi-uebersetzung1"/></w:pPr>
    </xsl:if>
    <xsl:if test="ancestor::gliederung1">
      <w:pPr><w:pStyle w:val="epi-gliederung-1"/></w:pPr>
    </xsl:if>
    <xsl:if test="ancestor::gliederung2">
      <w:pPr><w:pStyle w:val="epi-gliederung-2"/></w:pPr>
    </xsl:if>
    <xsl:if test="ancestor::gliederung3">
      <w:pPr><w:pStyle w:val="epi-gliederung-3"/></w:pPr>
    </xsl:if>
    <xsl:if test="ancestor::notizen">
      <w:pPr><w:pStyle w:val="epi-notiz"/></w:pPr>
    </xsl:if>

<!-- parametertest
     <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <parametertest1><xsl:copy-of select="$test1"/></parametertest1> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <parametertest2><xsl:copy-of select="$test2"/></parametertest2> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->


  </xsl:template>
<xsl:template match="nl[ancestor::verse]">
  <xsl:text disable-output-escaping="yes">&lt;/w:t&gt;&lt;/w:r&gt;&lt;/w:p&gt;&lt;w:p&gt;</xsl:text>
  <w:pPr><w:pStyle w:val="epi-verse"/></w:pPr>
  <xsl:text disable-output-escaping="yes">&lt;w:r&gt;&lt;w:t&gt;</xsl:text>

</xsl:template>


<!--Literaturverweis / bibliographical reference -->
<xsl:template match="apparat_B//rec[@type='biblio']"><w:r><w:t><xsl:value-of select="@text"/></w:t></w:r></xsl:template>
<xsl:template match="apparat_Z//rec[@type='biblio']"><w:r><w:t><xsl:value-of select="@text"/></w:t></w:r></xsl:template>

 <!---->
<xsl:template match="ls"><xsl:text></xsl:text></xsl:template>


<!--Worttrenner-->
 <xsl:template match="wtr[ancestor::bl or ancestor::item]">
<w:r><xsl:if test="ancestor::quot"><w:rPr><w:rStyle w:val="epi-zitat"/></w:rPr></xsl:if><w:t><xsl:value-of select="."/></w:t></w:r><xsl:if test="parent::insec"><w:r><w:t>&#x0323;</w:t></w:r></xsl:if></xsl:template>



  <xsl:template match="einstellungen"><!--die Einstellungsparameter werden unterdrueckt--></xsl:template>

  <xsl:template match="h[@type='Schriftarten']"></xsl:template>
  <xsl:template match="h[@value='Schriftarten']"></xsl:template>


  <xsl:template match="objektliste"></xsl:template>

  <!--<xsl:template match="quot"><w:r><w:rPr><w:rStyle w:val="epi-zitat"/></w:rPr><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>  -->




<xsl:template match="beschreibung/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
 <xsl:template match="kommentar/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
  <xsl:template match="apparate//item/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>
<!--  <xsl:template match="bl/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>-->

  <xsl:template match="uebersetzung">
    <xsl:if test="nr[text()] or version[text()]">
      <w:r><w:t>(</w:t></w:r>
      <xsl:apply-templates select="nr"/>
      <xsl:apply-templates select="version"/>
      <w:r><w:t>) </w:t></w:r>
    </xsl:if>
    <xsl:apply-templates select="content"/>
  </xsl:template>
<xsl:template match="uebersetzung/nr">
  <w:r><w:t><xsl:value-of select="."/></w:t></w:r>
</xsl:template>
<xsl:template match="uebersetzung/version">
  <xsl:choose>
    <xsl:when test="preceding-sibling::nr"><w:r><w:rPr><w:rStyle w:val="epi-version"/></w:rPr><w:t><xsl:value-of select="."/></w:t></w:r></xsl:when>
    <xsl:otherwise><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:otherwise>
  </xsl:choose>
</xsl:template>
  <xsl:template match="uebersetzung/content">
    <xsl:apply-templates/>
  </xsl:template>


 <!-- <xsl:template match="content/text()"><w:r><w:t><xsl:value-of select="."/></w:t></w:r></xsl:template>-->

<!-- versionsbuchstaben in internen verweisen hochstellen -->
<xsl:template match="su">
<!--  <xsl:text disable-output-escaping="yes">&lt;/w:t&gt;&lt;/w:r&gt;</xsl:text><w:r><w:rPr><w:rStyle w:val="epi-inschrift-version"/></w:rPr><w:t><xsl:value-of select="."/></w:t></w:r><xsl:text disable-output-escaping="yes">&lt;w:r&gt;&lt;w:t&gt;</xsl:text>
-->
  <w:r><w:rPr><w:rStyle w:val="epi-version"/></w:rPr><w:t><xsl:value-of select="."/></w:t></w:r>
</xsl:template>

<!-- spitze Klammern für auslassungen -->
<!--<xsl:template match="add">
  <w:r>
    <w:rPr>
      <w:rFonts w:ascii="MS Gothik" w:h-ansi="MS Gothik"/>
      <wx:font wx:val="MS Gothik"/>
      <xsl:if test="ancestor::quot"><w:i/></xsl:if>
    </w:rPr>
    <w:t>&lt;</w:t>
  </w:r>
  <xsl:apply-templates></xsl:apply-templates>
  <w:r>
    <w:rPr>
      <w:rFonts w:ascii="MS Gothik" w:h-ansi="MS Gothik"/>
      <wx:font wx:val="MS Gothik"/>
      <xsl:if test="ancestor::quot"><w:i/></xsl:if>
    </w:rPr>
    <w:t>&gt;</w:t>
  </w:r>
</xsl:template>-->
<xsl:template match="add">
  <w:r>
    <w:rPr>
      <w:rFonts w:ascii="Cambria" w:h-ansi="Cambria"/>
<!--      <wx:font wx:val="Cambria"/>-->
      <xsl:if test="ancestor::quot"><w:i/><w:spacing w:val="-40"/></xsl:if>
    </w:rPr>
    <w:t>〈</w:t>
  </w:r>
  <xsl:apply-templates></xsl:apply-templates>
  <w:r>
    <w:rPr>
      <w:rFonts w:ascii="Cambria" w:h-ansi="Cambria"/>
      <!--<wx:font wx:val="Cambria"/>-->
      <xsl:if test="ancestor::quot"><w:i/><w:spacing w:val="-40"/></xsl:if>
    </w:rPr>
    <w:t>〉</w:t>
  </w:r>
</xsl:template>

  <xsl:template match="spatium_v">
        <xsl:param name="width"><xsl:value-of select="@width"/></xsl:param>
        <w:r><w:t>
    <xsl:call-template name="spatium_auslesen">
      <xsl:with-param name="zaehler1"><xsl:value-of select="$width"/></xsl:with-param>
    </xsl:call-template>
    </w:t></w:r>
  </xsl:template>
  <xsl:template name="spatium_auslesen">
    <xsl:param name="zaehler1"></xsl:param>
    <xsl:param name="zaehler2" select="$zaehler1 - 1"></xsl:param>
    <xsl:if test="$zaehler1 &gt; 0"><xsl:text>&#x00A0;</xsl:text>
    <xsl:call-template name="spatium_auslesen">
      <xsl:with-param name="zaehler1"><xsl:value-of select="$zaehler2"/></xsl:with-param>
    </xsl:call-template>
    </xsl:if>
  </xsl:template>

  <!-- zeilenumbrüche in transkriptionsfeldern mit spalten -->
  <xsl:template match="bl[@data-link-value='Spalten' or @align='Spalten']/bl/z[@connex='nz' or starts-with(@data-link-value,'neue Zeile')]">
    <w:r><w:br/></w:r>
  </xsl:template>
  <!-- die schrägstriche werden unterdrückt -->
  <xsl:template match="bl[@data-link-value='Spalten' or align or @align='Spalten']/bl/z[@connex='nz' or starts-with(@data-link-value,'neue Zeile')]/text()"></xsl:template>

  <xsl:template match="a">
    <!--<w:r><w:t><xsl:value-of select="@value"/></w:t></w:r>-->

    <w:r><w:fldChar w:fldCharType="begin"/></w:r>
<w:r>
    <w:instrText> HYPERLINK "<xsl:value-of select="@href"/>" </w:instrText>
</w:r>
<w:r><w:fldChar w:fldCharType="separate"/></w:r>
<w:r><w:rPr><w:rStyle w:val="Hyperlink"/></w:rPr><w:t><xsl:value-of select="@value"/></w:t></w:r>
<w:r><w:fldChar w:fldCharType="end"/></w:r>
  </xsl:template>

<xsl:template name="linkSerie">
<!-- dieses template kann aufgerufen werden, um fortlaufende folgen von links auf inschriften zu verdichten
fortlaufende zwischennummern werden eliminiert und durch spiegelstrich ersetzt -->
<!-- fortlaufende zwischennummern werden durch den spiegelstrich ersetzt -->
    <xsl:param name="p_links_doubletten_enfernen1">
        <xsl:for-each select="link[not(text()=preceding-sibling::link[1]/text())]">
                <xsl:copy-of select="."/>
        </xsl:for-each>
    </xsl:param>

    <xsl:param name="p_links_verdichten">
        <xsl:for-each select="exsl:node-set($p_links_doubletten_enfernen1)/link">
            <xsl:variable name="preceding_nr"><xsl:value-of select="preceding-sibling::link[1]/@number"/></xsl:variable>
            <xsl:variable name="following_nr"><xsl:value-of select="following-sibling::link[1]/@number"/></xsl:variable>
            <xsl:choose>
            <xsl:when test="@number=$preceding_nr +1 and
                                        @number=$following_nr -1 and
                                        not(@is_part) and
                                        not(preceding-sibling::link[1][@is_part]) and
                                        not(following-sibling::link[1][@is_part])"><link>–</link></xsl:when>
            <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:param>

<!-- folge von spiegelstrichen auf einen reduzieren -->
    <xsl:param name="p_links_doubletten_entfernen2">
        <xsl:for-each select="exsl:node-set($p_links_verdichten)/link[not(text()=preceding-sibling::link[1]/text())]">
            <xsl:copy-of select="."/>
        </xsl:for-each>
    </xsl:param>

<!--<xsl:comment>
parameter1: <xsl:copy-of select="$nrn-verdichten"/>
parameter2: <xsl:copy-of select="$nrn-doubletten-entfernen"/>
</xsl:comment>-->

<!-- kommata setzen -->
    <xsl:for-each select="exsl:node-set($p_links_doubletten_entfernen2)/link">
       <xsl:value-of select="."/><xsl:if test="following-sibling::link[1][not(text()='–')] and not(text()='–')"><xsl:text>, </xsl:text></xsl:if>
    </xsl:for-each>
</xsl:template>

</xsl:stylesheet>
