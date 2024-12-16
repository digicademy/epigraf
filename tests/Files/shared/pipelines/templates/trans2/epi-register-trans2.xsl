<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>
<xsl:import href="../commons/epi-switch.xsl"/>

 <!-- variablen und parameter können, anders als in xsl-1.1, nur mit exsl:node-set ausgelesen werden -->

  <!-- FUNKTION DIESES STYLESHEETS: -->
  <!-- die struktur aus der ersten transformation (register-trans1.xsl) wird übernommen -->
  <!-- den einzelnene registern werden überschriften und gegebenenfalls vorbemerkungen aus den textbausteinen zugewiesen  -->
  <!-- die doubletten bei den referenzen auf artikelnummern werden entfernt -->
  <!-- einträge, die weder referenzen auf artikel, verweise oder untereinträge aufweisen, werden entfernt -->

<!-- das abschneiden der verweise nach leerstelle erfolgt im template verweise_reduzieren unter  log="vl1" und  log="vl2" -->


 <xsl:template match="indices">
    <!-- zuerst wird geprüft, ob im projekt ein eigener titel vorgesehen ist,
    wenn ja wird er von dort ausgelesen,
    wenn nicht wird er hier erzeugt-->
   <indices>
     <xsl:copy-of select="@*"/>
   <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
     <!-- gesamttitel zum register -->
    <titel>
      <xsl:choose>
        <xsl:when test="titel">
          <xsl:value-of select="titel"/>
        </xsl:when>
        <xsl:otherwise>
          <xsl:text>Register</xsl:text>
        </xsl:otherwise>
      </xsl:choose>
    </titel>
   <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:for-each select="notiz/content">
        <notiz log="note1"><xsl:copy-of select="@*"/><xsl:apply-templates/></notiz>
    </xsl:for-each>

   <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

     <!-- die register werden aufgerufen und sortiert -->
    <!-- das register mit den maßangaben wird nicht mehr benötigt -->
    <xsl:for-each select="index">
        <!--<xsl:sort select="@sort" data-type="number"/>-->
      <xsl:apply-templates select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:for-each>
   </indices>
</xsl:template>

<xsl:template match="p[text()='§'][ancestor::notiz]"><!-- wird unterdrückt --></xsl:template>

    <!--  index[contains(@type,'')]  -->
<xsl:template match="index[@propertytype='locations']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='personnames']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='manufacturers']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='placenames']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='heraldry']"><xsl:call-template name="wappen_marken"/></xsl:template>
<xsl:template match="index[@propertytype='brands']"><!-- geht in wappen auf --></xsl:template>
<xsl:template match="index[@propertytype='blazons']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='epithets']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='incipits']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='formulas']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='texttypes']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='sources']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='objecttypes']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='fonttypes']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='techniques']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='subjects']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[contains(@propertytype,'datierung')]"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='icons']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='metres']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='initials']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='literature']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='measures']"><!-- wird nicht mehr benötigt --></xsl:template>
<xsl:template match="index[string-length(@propertytype)=0]"><xsl:call-template name="index"/></xsl:template> <!-- für zwischenabschnitte zwischen registern die der gliederung dienen -->
<xsl:template match="index[@propertytype='wordseparators']"><xsl:call-template name="index"/></xsl:template>
<xsl:template match="index[@propertytype='materials']"><xsl:call-template name="index"/></xsl:template>

  <xsl:template name="index">
    <index>
        <!-- die attribute werden übernommen -->
        <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <xsl:choose>
        <xsl:when test="nr">
          <nr><xsl:value-of select="nr"/></nr><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:when>
        <xsl:otherwise>
          <nr><xsl:value-of select="@nr"/></nr><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:otherwise>
      </xsl:choose>

      <xsl:choose>
        <xsl:when test="caption">
          <titel><xsl:value-of select="caption"/></titel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:when>
        <xsl:otherwise>
          <titel><xsl:value-of select="@titel"/></titel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:otherwise>
      </xsl:choose>
    <xsl:for-each select="notiz/content">
        <notiz log="note2"><xsl:copy-of select="@*"/><xsl:apply-templates/></notiz>
    </xsl:for-each>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

      <xsl:choose>
            <xsl:when test="@type='fonttypes' or @propertytype='fonttypes'">
                <xsl:call-template name="schriftarten"/>
            </xsl:when>
            <xsl:when test="@type='heraldry' or @propertytype='heraldry'">
                <xsl:call-template name="wappen_marken"/>
            </xsl:when>
            <xsl:when test="@type='blazons'">
                <xsl:call-template name="blasonierungen"/>
            </xsl:when>
            <xsl:when test="@type='subjects'">
            <!-- das sachregister muss erneut alphabetisch sortiert werden, weil einträge aus anderen register zugespielt wurden -->
            <!-- der eintrag "Schrift"  wird hier unterdrückt weil er an anderer stelle in das schriftregister 9b übertragen wird -->
                <xsl:for-each select="item[not(@groupname='Schrift') or lemma='Schrift']">
                <xsl:sort select="lemma/@sortstring" order="ascending" lang="de"  case-order="upper-first"/>
                <eintrag><xsl:copy-of select="@*"/><xsl:apply-templates/></eintrag>
                </xsl:for-each>
            </xsl:when>
            <xsl:when test="@type='writing'">
                <!-- die formatierung des schriftregisters (9b schriftausführung) wird an ein eigenes template verwiesen -->
                <xsl:call-template name="schrift"/>
            </xsl:when>
            <xsl:otherwise>
                <xsl:apply-templates/>
            </xsl:otherwise>
      </xsl:choose>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </index>
</xsl:template>

<!-- das schriftregister (9b schriftausführung) wird aus den registern schrifttechnik und worttrenner sowie dem eintrag schrift im sachregister gebildet -->
<!--
die register schrittechnik und worttrenner werden bereits im vorfeld im bandartikel aufgerufen,
der eintrag schrift muss noch aus dem sachregister übernommen werden
 -->
<xsl:template name="schrift">
    <!-- die komponenten werden zunächst in einem parameter gesammelt, damit man sie in einem zweiten schritt sortieren kann -->
    <xsl:param name="p_schrift1">
                    <!-- schrifttechnik und worttrenner sind bereits vorhanden und werden an die zutreffenden templates verwiesen -->
                    <xsl:apply-templates/>
                    <!-- die schriftausführung wird aus dem sachregister geholt und dan ebenfalls an die templates verwiesen-->
                    <xsl:for-each select="../index[@type='subjects']/item[@groupname='Schrift' or lemma='Schrift']"> <xsl:apply-templates/></xsl:for-each>
    </xsl:param>

    <!--<parametertest_schrift><xsl:copy-of select="$p_schrift1"/></parametertest_schrift>-->

    <!-- der parameter wird aufgerufen, die einträge werden sortiert und kopiert -->
    <xsl:for-each select="$p_schrift1/eintrag">
        <xsl:sort select="lemma/@sortstring"/>
        <xsl:copy-of select="."/>
    </xsl:for-each>
</xsl:template>

<xsl:template name="blasonierungen">
<!-- die einträge müssen neu sortiert werden -->
    <xsl:for-each select="item">
            <xsl:sort select="lemma/@sortstring" order="ascending" lang="de"  case-order="upper-first"/>
            <eintrag logg="e1">
              <xsl:copy-of select="@*"/>
              <xsl:apply-templates/>
            </eintrag>
    </xsl:for-each>
</xsl:template>

<xsl:template name="schriftarten">
  <xsl:for-each select="item">
    <xsl:choose>
      <!-- verweise kopieren-->
      <xsl:when test="not(item)">
        <eintrag logg="e2">
          <xsl:copy-of select="@*"/>
          <xsl:apply-templates/>
        </eintrag>
        </xsl:when>
      <!-- einträge spezifizieren -->
      <xsl:otherwise>
        <eintrag logg="e3">
          <xsl:copy-of select="@*"/>
          <xsl:apply-templates select="lemma"/>
          <xsl:apply-templates select="sections"/>
          <xsl:call-template name="subschriftarten"/>
        </eintrag>
      </xsl:otherwise>
    </xsl:choose>
  </xsl:for-each>
 </xsl:template>

  <xsl:template name="subschriftarten">
    <xsl:for-each select="item">
      <!-- die lemmata 'mit versal' und 'mit versalien' sowie die entsprechenden varianten werden zusammengezogen -->
      <xsl:choose>
        <!-- versal/versalien -->
        <xsl:when test="lemma='mit Versal' and following-sibling::item/lemma='mit Versalien'">
          <eintrag logg="e4">
            <xsl:copy-of select="@*"/>
            <lemma>mit Versalien</lemma>
            <nrn>
              <xsl:call-template name="nrn-versalien">
                <xsl:with-param name="lemma">mit Versalien</xsl:with-param>
              </xsl:call-template>
            </nrn>
            <xsl:call-template name="subschriftarten"/>
          </eintrag>
        </xsl:when>
        <xsl:when test="lemma='mit Versalien' and preceding-sibling::item/lemma='mit Versal'"></xsl:when>

        <!-- versal und minuskel/versalien ... -->
        <xsl:when test="lemma='mit Versal und Minuskel' and following-sibling::item/lemma='mit Versalien und Minuskel'">
          <eintrag logg="e5">
            <xsl:copy-of select="@*"/>
            <lemma>mit Versalien und Minuskel</lemma>
            <nrn>
              <xsl:call-template name="nrn-versalien">
                <xsl:with-param name="lemma">mit Versalien und Minuskel</xsl:with-param>
              </xsl:call-template>
            </nrn>
            <xsl:call-template name="subschriftarten"/>
          </eintrag>
        </xsl:when>
        <xsl:when test="lemma='mit Versalien und Minuskel' and preceding-sibling::item/lemma='mit Versal und Minuskel'"></xsl:when>

        <!-- frakturversal/frakturversalien -->
        <xsl:when test="lemma='mit Frakturversal' and following-sibling::item/lemma='mit Frakturversalien'">
          <eintrag logg="e6">
            <xsl:copy-of select="@*"/>
            <lemma>mit Frakturversalien</lemma>
            <nrn>
              <xsl:call-template name="nrn-versalien">
                <xsl:with-param name="lemma">mit Frakturversalien</xsl:with-param>
              </xsl:call-template>
            </nrn>
            <xsl:call-template name="subschriftarten"/>
          </eintrag>
        </xsl:when>
        <xsl:when test="lemma='mit Frakturversalien' and preceding-sibling::item/lemma='mit Frakturversal'"></xsl:when>
        <!-- versal in gotischer majuskel/versalien etc -->
        <xsl:when test="lemma='mit Versal in gotischer Majuskel' and following-sibling::item/lemma='mit Versalien in gotischer Majuskel'">
          <eintrag logg="e7">
            <xsl:copy-of select="@*"/>
            <lemma>mit Versalien in gotischer Majuskel</lemma>
            <nrn>
              <xsl:call-template name="nrn-versalien">
                <xsl:with-param name="lemma">mit Versalien in gotischer Majuskel</xsl:with-param>
              </xsl:call-template>
            </nrn>
            <xsl:call-template name="subschriftarten"/>
          </eintrag>
        </xsl:when>
        <xsl:when test="lemma='mit Versalien in gotischer Majuskel' and preceding-sibling::item/lemma='mit Versal in gotischer Majuskel'"></xsl:when>

        <!-- andere einträge -->
        <xsl:otherwise>
          <eintrag logg="e8">
            <xsl:copy-of select="@*"/>
            <xsl:copy-of select="lemma"/>
            <xsl:apply-templates select="sections"/>
            <xsl:call-template name="subschriftarten"/>
          </eintrag>
        </xsl:otherwise>
      </xsl:choose>
    </xsl:for-each>
   </xsl:template>



  <!-- falls titel und notiz schon angelegt wurden, werden sie ignoriert -->
<xsl:template match="titel[parent::index]"></xsl:template>
<xsl:template match="notiz[parent::index]"></xsl:template>

<xsl:template match="item[ancestor::index]">
    <xsl:choose>
        <xsl:when test="@standort-status='1'">
          <eintrag_kursiv>
           <xsl:copy-of select="@*"/>
           <xsl:apply-templates/>
          </eintrag_kursiv>
        </xsl:when>
        <xsl:when test="@type='group'">
          <gruppe>
              <xsl:copy-of select="@*"/>
              <xsl:apply-templates/>
          </gruppe>
        </xsl:when>
        <xsl:otherwise>
            <eintrag log="e9">
                <xsl:copy-of select="@*"/>
                <xsl:apply-templates/>
            </eintrag>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>



<xsl:template match="lemma">
<lemma><xsl:copy-of select="@*"/><xsl:apply-templates></xsl:apply-templates></lemma>
</xsl:template>

<!-- referenzen auf artikelnummer -->
<xsl:template match="section[ancestor::item]">
    <nr>
        <xsl:copy-of select="@*"/>
      <xsl:for-each select="ancestor::book/catalog/article[@id=current()/@articles_id]">
            <!--<xsl:value-of select="@nr"/>--><xsl:number count="article" from="book"/>
        </xsl:for-each>
    </nr>
</xsl:template>

<!-- referenzen auf artikelnummern -->
<xsl:template match="sections[parent::item]">
  <xsl:param name="links-nrn">
    <xsl:for-each select="section[not(@articles_id=preceding-sibling::section/@articles_id)]">
      <xsl:apply-templates select="."/>
    </xsl:for-each>
  </xsl:param>
  <nrn>
    <xsl:for-each select="$links-nrn/nr">
      <xsl:sort select="node()" data-type="number"/>
            <xsl:copy-of select="."/>
    </xsl:for-each>
  </nrn>
</xsl:template>

<xsl:template match="sections[parent::item]" mode="schriftarten">
  <xsl:param name="links-nrn-schriftarten">
    <xsl:for-each select="section[not(@articles_id=preceding-sibling::section/@articles_id)]">
      <xsl:apply-templates select="."/>
    </xsl:for-each>
  </xsl:param>
  <xsl:for-each select="$links-nrn-schriftarten/nr">
    <xsl:sort select="node()" data-type="number"/>
    <xsl:copy-of select="."/>
  </xsl:for-each>
</xsl:template>

<!-- verweise -->
<!-- das abschneiden der verweise nach leerstelle erfolgt im template verweise_reduzieren unter  log="vl1" und  log="vl2" -->
<xsl:template match="crossRefs">
  <xsl:param name="p_basisstandort"><xsl:value-of select="ancestor::book/project/lemma"/></xsl:param>
  <xsl:param name="verweise">
    <xsl:choose>
    <!-- personenregister -->
    <xsl:when test="ancestor::index[ @propertytype='personnames' or @type='personnames']">
    <xsl:for-each select="crossRef">
    <xsl:choose>
    <!-- bei verweisen auf ehefrauen wird zuerst der vorname als eintrag/lemma angelegt -->
        <xsl:when test="crossRef">
            <xsl:for-each select="crossRef">
                <eintrag log2="e10"><xsl:copy-of select="@id"/>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <xsl:choose>
                    <!-- ergänzungen in klammern zum namen werden unterdrückt -->
                    <xsl:when test="contains(lemma,' (')">
                      <lemma log2="lcr1">
                        <xsl:copy-of select="lemma/@*"/>
                        <xsl:value-of select="substring-before(lemma,' (')"/>
                      </lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:when>
                    <xsl:when test="contains(lemma,', ')">
                      <lemma log2="lcr2">
                        <xsl:copy-of select="lemma/@*"/>
                        <xsl:value-of select="substring-before(lemma,', ')"/>
                      </lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:when>
                    <xsl:when test="contains(lemma,'#')">
                      <lemma log2="lcr3">
                        <xsl:copy-of select="lemma/@*"/>
                        <xsl:value-of select="substring-before(lemma,'#')"/>
                      </lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:when>
                    <xsl:otherwise><xsl:copy-of select="lemma"/></xsl:otherwise>
                  </xsl:choose><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <!-- nun wird der verweis auf den nachnamen erzeugt -->
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <verweise><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                   <xsl:for-each select="parent::crossRef">
                    <siehe log2="siehe1">
                      <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                      <xsl:choose>
                        <!-- ergänzungen in klammern zum namen werden unterdrückt -->
                       <xsl:when test="contains(lemma,' (')">
                         <lemma log2="lcr4">
                           <xsl:copy-of select="lemma/@*"/>
                           <xsl:value-of select="substring-before(lemma,' (')"/>
                         </lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                       </xsl:when>
                        <xsl:when test="contains(lemma,', ')">
                          <lemma log2="lcr5">
                            <xsl:copy-of select="lemma/@*"/>
                            <xsl:value-of select="substring-before(lemma,', ')"/>
                          </lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </xsl:when>
                        <xsl:when test="contains(lemma,'#')">
                          <lemma log2="lcr6">
                            <xsl:copy-of select="lemma/@*"/>
                            <xsl:value-of select="substring-before(lemma,'#')"/>
                          </lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </xsl:when>
                        <xsl:otherwise><xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:otherwise>
                     </xsl:choose><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </siehe><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                   </xsl:for-each>
                </verweise>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              </eintrag>
            </xsl:for-each>
       </xsl:when>
        <!-- wenn es keine unterverweise gibt -->
       <xsl:otherwise>
        <verweise log="v1"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          <siehe log2="siehe2">
              <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <xsl:choose>
                <!-- ergänzungen in klammern zum namen werden unterdrückt -->
<!--                <xsl:when test="contains(lemma,'|')">
                  <lemma log2="lcr7">
                    <xsl:copy-of select="lemma/@*"/>
                    <xsl:value-of select="substring-before(lemma,'|')"/>
                  </lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:when>-->
                <xsl:when test="contains(lemma,' (')">
                  <lemma log2="lcr7">
                    <xsl:copy-of select="lemma/@*"/>
                    <xsl:value-of select="substring-before(lemma,' (')"/>
                  </lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:when>
                <xsl:when test="contains(lemma,', ')">
                  <lemma log2="lcr8">
                    <xsl:copy-of select="lemma/@*"/>
                    <xsl:value-of select="substring-before(lemma,', ')"/>
                  </lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:when>
                <xsl:when test="contains(lemma,'#')">
                  <lemma log2="lcr9">
                    <xsl:copy-of select="lemma/@*"/>
                    <xsl:value-of select="substring-before(lemma,'#')"/>
                  </lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:when>
                <xsl:otherwise><xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:otherwise>
              </xsl:choose>
            </siehe>
          </verweise>
        </xsl:otherwise>
       </xsl:choose>
      </xsl:for-each>
    </xsl:when>
    <!-- ende register personen -->

<xsl:when test="ancestor::index[@propertytype='locations' or @type='locations']">
<xsl:for-each select="crossRef">
<xsl:choose>
    <xsl:when test="lemma=$p_basisstandort">
<xsl:for-each select="crossRef">
      <verweise><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:call-template name="verweise-reduzieren"></xsl:call-template>
      </verweise><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:for-each>

    </xsl:when>
<xsl:otherwise>
      <verweise><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:call-template name="verweise-reduzieren"></xsl:call-template>
      </verweise><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:otherwise>
</xsl:choose>

</xsl:for-each>

</xsl:when>

    <!-- register epitheta -->
    <xsl:when test="ancestor::index[@propertytype='epithets' or @type='epithets']">
      <!-- die verweise der obersten ebene (auf sprachen) werden unterdrückt -->
      <verweise><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <xsl:call-template name="verweise-reduzieren"></xsl:call-template>
      </verweise><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:when>

<!--    <!-\- register formeln -\->
    <xsl:when test="ancestor::index[@name='formeln' or @type='properties_formeln']">
      <!-\- die zwischenverweise auf übergeordnete gruppen-einträge - sprachen und wortfeld -  werden übersprungen -\->
      <xsl:call-template name="verweise-reduzieren"></xsl:call-template>
    </xsl:when>-->

    <!-- die übrigen register -->
    <xsl:otherwise>
      <verweise><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <xsl:call-template name="verweise-reduzieren"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
       <!--<xsl:apply-templates/>-->
      </verweise><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:otherwise>
  </xsl:choose>
  </xsl:param>

<!--
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<parametertest_verweise><xsl:copy-of select="$verweise"/></parametertest_verweise>
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->

<!-- doubletten aus parameter entfernen -->
  <xsl:for-each select="$verweise">
    <xsl:choose>
      <!-- für alle register außer personennamen -->
      <xsl:when test="verweise">
        <xsl:for-each select="verweise">
          <verweise> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="*[not(@id=preceding-sibling::*/@id)]">
              <xsl:variable name="verweis-id"><xsl:value-of select="@id"/></xsl:variable>
              <xsl:element name="{name()}">
                <xsl:copy-of select="@*"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy-of select="lemma"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="siehe|siehe_auch"> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  <xsl:copy-of select="."/>
                </xsl:for-each>
                <xsl:for-each select="following-sibling::*[@id=$verweis-id]">
                  <xsl:copy-of select="siehe|siehe_auch"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:for-each>
              </xsl:element>
            </xsl:for-each>
          </verweise>
        </xsl:for-each>
      </xsl:when>
      <!-- für das personenregister -->
      <xsl:otherwise>
         <xsl:for-each select="eintrag">
          <eintrag log="e11"><xsl:copy-of select="@id"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:copy-of select="lemma"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="verweise"> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <verweise> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="*[not(@id=preceding-sibling::*/@id)]">
                  <xsl:variable name="verweis-id"><xsl:value-of select="@id"/></xsl:variable>
                  <xsl:element name="{name()}">
                    <xsl:copy-of select="@*"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:copy-of select="lemma"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="siehe|siehe_auch">
                      <xsl:copy-of select="."/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                    <xsl:for-each select="following-sibling::*[@id=$verweis-id]">
                      <xsl:copy-of select="siehe|siehe_auch"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                  </xsl:element>
                </xsl:for-each>
              </verweise>
            </xsl:for-each>
          </eintrag>
        </xsl:for-each>

      </xsl:otherwise>
    </xsl:choose>
  </xsl:for-each>
</xsl:template>


<!-- den string der verweisziele in einigen registern nach bestimmten zeichen abschneiden -->
  <xsl:template name="verweise-reduzieren">
    <xsl:for-each select="crossRef">
      <xsl:choose>
        <xsl:when test="@id=ancestor::index/item[@type='group']/@id">
          <xsl:call-template name="verweise-reduzieren"></xsl:call-template>
        </xsl:when>
        <xsl:otherwise>
          <xsl:choose>
            <xsl:when test="ancestor::item[1]/sections">
              <siehe_auch>
                <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:choose>
<!-- personen -->
                  <xsl:when test="ancestor::index[@type='personnames']">
                    <lemma log="vl1">
                      <xsl:copy-of select="@*"/>
                        <xsl:choose>
                            <xsl:when test="contains(lemma,' (')"><xsl:value-of select="substring-before(lemma,' (')"/></xsl:when>
                            <xsl:otherwise><xsl:value-of select="lemma"/></xsl:otherwise>
                        </xsl:choose>
                    </lemma>
                  </xsl:when>
<!-- orte -->
                  <xsl:when test="ancestor::index[@type='placenames']">
                    <lemma log="vl1">
                      <xsl:copy-of select="@*"/>
                        <xsl:choose>
                            <xsl:when test="contains(lemma,' (')"><xsl:value-of select="substring-before(lemma,' (')"/></xsl:when>
                            <xsl:otherwise><xsl:value-of select="lemma"/></xsl:otherwise>
                        </xsl:choose>
                    </lemma>
                  </xsl:when>
<!-- standorte -->
                  <xsl:when test="ancestor::index[@type='locations']">
                    <lemma log="vl1">
                      <xsl:copy-of select="@*"/>
                        <xsl:choose>
                            <xsl:when test="contains(lemma,' (')"><xsl:value-of select="substring-before(lemma,' (')"/></xsl:when>
                            <xsl:otherwise><xsl:value-of select="lemma"/></xsl:otherwise>
                        </xsl:choose>
                    </lemma>
                  </xsl:when>
<!-- wappen,  initien, formeln, texttypen, inschriftenträger, schriftarten, sachregister, ikonographie, initialen, versformen, marken. -->
                  <xsl:otherwise>
                    <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  </xsl:otherwise>
                </xsl:choose>
                 <xsl:call-template name="verweise-reduzieren"></xsl:call-template>
              </siehe_auch><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:when>

            <xsl:otherwise>
              <siehe>
                <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:choose>
<!-- personen -->
                  <xsl:when test="ancestor::index[@type='personnames']">
                    <lemma log="vl1">
                      <xsl:copy-of select="@*"/>
                        <xsl:choose>
                            <xsl:when test="contains(lemma,' (')"><xsl:value-of select="substring-before(lemma,' (')"/></xsl:when>
                            <xsl:otherwise><xsl:value-of select="lemma"/></xsl:otherwise>
                        </xsl:choose>
                    </lemma>
                  </xsl:when>
<!-- orte -->
                  <xsl:when test="ancestor::index[@type='placenames']">
                    <lemma log="vl1">
                      <xsl:copy-of select="@*"/>
                        <xsl:choose>
                            <xsl:when test="contains(lemma,' (')"><xsl:value-of select="substring-before(lemma,' (')"/></xsl:when>
                            <xsl:otherwise><xsl:value-of select="lemma"/></xsl:otherwise>
                        </xsl:choose>
                    </lemma>
                  </xsl:when>
<!-- standorte -->
                  <xsl:when test="ancestor::index[@type='locations']">
                    <lemma log="vl1">
                      <xsl:copy-of select="@*"/>
                        <xsl:choose>
                            <xsl:when test="contains(lemma,' (')"><xsl:value-of select="substring-before(lemma,' (')"/></xsl:when>
                            <xsl:otherwise><xsl:value-of select="lemma"/></xsl:otherwise>
                        </xsl:choose>
                    </lemma>
                  </xsl:when>
<!-- wappen,  initien, formeln, texttypen, inschriftenträger, schriftarten, sachregister, ikonographie, initialen, versformen, marken. -->
                  <xsl:otherwise>
                    <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                  </xsl:otherwise>
                </xsl:choose>
                 <xsl:call-template name="verweise-reduzieren"></xsl:call-template>
              </siehe><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:otherwise>
          </xsl:choose>

        </xsl:otherwise>
      </xsl:choose>
    </xsl:for-each>
  </xsl:template>

  <xsl:template name="wappen_marken">
    <xsl:param name="wappen1">
      <!-- wappen, hausmarken und meisterzeichen zusammenführen und referenzen auf artikelnummern erzeugen -->
      <!-- 1. wappen, hausmarken und meisterzeichen zusammenführen -->
      <xsl:for-each select="ancestor::indices/index[@type='heraldry' or  @type='brands' or @propertytype='heraldry' or  @propertytype='brands']/item[not(@type='group')][not(lemma=preceding-sibling::item/lemma)]">
        <xsl:sort select="lemma" order="ascending" lang="de"  case-order="upper-first"/>
        <eintrag log1="e12">
          <xsl:copy-of select="@*"/>
          <xsl:apply-templates select="lemma"/>
            <xsl:if test="ancestor::index[@propertytype='heraldry' or @type='heraldry']"><xsl:apply-templates select="name"/></xsl:if>
          <nrn>
            <!-- 2. referenzen auf artikelnummern suchen -->
            <xsl:for-each select="ancestor::index[@type='heraldry' or @type='brands' or @propertytype='heraldry' or  @propertytype='brands']/item[lemma=current()/lemma]//section">
              <nr>
                <xsl:copy-of select="@*"/>
                <xsl:for-each select="ancestor::book/catalog/article[@id=current()/@articles_id]">
                  <!--<xsl:value-of select="@nr"/>--><xsl:number count="article" from="book"/>
                </xsl:for-each>
              </nr>
            </xsl:for-each>
          </nrn>
        </eintrag>
      </xsl:for-each>
    </xsl:param>
    <xsl:param name="wappen2">
      <!-- doubletten bei den lemmata, die sich aus dem zusammenführen von marken und wappen ergeben haben, entfernen -->
      <xsl:for-each select="$wappen1/eintrag[not(lemma=preceding-sibling::eintrag/lemma)]">
        <eintrag log2="e13">
          <xsl:copy-of select="@*"/>
          <xsl:copy-of select="lemma"/>
          <nrn>
            <xsl:for-each select="nrn/nr[not(text()=preceding-sibling::nr/text())]">
              <xsl:copy-of select="."/>
            </xsl:for-each>
            <xsl:for-each select="following-sibling::eintrag[lemma=current()/lemma]/nrn/nr">
              <xsl:copy-of select="."/>
            </xsl:for-each>
          </nrn>
        </eintrag>
       </xsl:for-each>
    </xsl:param>
    <xsl:param name="wappen3">
      <!-- doubletten bei den referenzen auf artikelnummern entfernen -->
      <xsl:for-each select="$wappen2/eintrag">
        <eintrag log3="e14">
          <xsl:copy-of select="@*"/>
          <xsl:copy-of select="lemma"/>
          <nrn>
            <xsl:for-each select="nrn/nr[not(text()=preceding-sibling::nr/text())]">
              <xsl:sort data-type="number"/>
              <xsl:copy-of select="."/>
            </xsl:for-each>
          </nrn>
        </eintrag>
      </xsl:for-each>
    </xsl:param>


<!--    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
   <wappen1><xsl:copy-of select="$wappen1"/></wappen1><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wappen2><xsl:copy-of select="$wappen2"/></wappen2><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <wappen3><xsl:copy-of select="$wappen3"/></wappen3><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>-->


    <index>
      <xsl:copy-of select="@*"/>
      <nr><xsl:value-of select="@nr"/></nr>
      <titel> <xsl:value-of select="@titel"/></titel>
   <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    <xsl:for-each select="notiz/content">
        <notiz log="note3"><xsl:copy-of select="@*"/><xsl:apply-templates/></notiz>
    </xsl:for-each>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      <!-- zuerst einzel-einträge auswählen -->
      <xsl:for-each select="$wappen3/eintrag">
        <xsl:copy-of select="."/>
      </xsl:for-each>

      <!-- danach gruppen (=sammel-einträge für nicht identifiziert wappen, marken, meisterzeichen) anhängen -->
      <xsl:for-each select="ancestor::indices/index[@propertytype='heraldry' or @propertytype='brands' or @type='heraldry' or @type='brands']/item[@type='group']">
        <!-- es werden nur die gruppen angesteuert, die referenzen auf kataöogartikel enthalten -->
        <xsl:if test="sections/section">
            <xsl:choose>
                <xsl:when test="item[@type='group']">
                    <gruppe>
                    <xsl:copy-of select="lemma"/>
                    <xsl:for-each select="item[@type='group'][item]">
                        <xsl:for-each select="item"><eintrag logg="e15"><xsl:copy-of select="@*"/><xsl:apply-templates></xsl:apply-templates></eintrag></xsl:for-each>
                    </xsl:for-each>
                    </gruppe>
                </xsl:when>
                <xsl:otherwise><xsl:call-template name="nrn-gruppe-wappen"></xsl:call-template></xsl:otherwise>
            </xsl:choose>
        </xsl:if>
      </xsl:for-each>
    </index>
  </xsl:template>


<xsl:template name="nrn-gruppe-wappen">
  <xsl:param name="nrn-gruppe-wappen1">
    <!-- referenzen auf artikelnummern erzeugen -->
    <xsl:for-each select="sections/section">
      <nr>
        <xsl:copy-of select="@*"/>
        <xsl:for-each select="ancestor::book/catalog/article[@id=current()/@articles_id]">
          <!--<xsl:value-of select="@nr"/>-->
            <xsl:number count="article" from="book"/>
        </xsl:for-each>
      </nr>
    </xsl:for-each>
  </xsl:param>
  <xsl:param name="nrn-gruppe-wappen2">
    <!-- doubletten entfernen und sortieren -->
    <xsl:for-each select="$nrn-gruppe-wappen1/nr[not(text()=preceding-sibling::nr/text())]">
      <xsl:sort data-type="number"/>
      <xsl:copy-of select="."/>
    </xsl:for-each>
  </xsl:param>

  <gruppe log="g1">
    <xsl:copy-of select="lemma"/>
    <nrn>
      <xsl:for-each select="$nrn-gruppe-wappen2/nr">
        <xsl:copy-of select="."/>
      </xsl:for-each>
    </nrn>
  </gruppe>
</xsl:template>

<!-- referenzen auf artikelnummern im schriftartenregister -->
<xsl:template name="nrn-versalien">
  <xsl:param name="lemma"></xsl:param>
  <xsl:param name="nrn-versalien">
<!--  <xsl:apply-templates select="links" mode="schriftarten"/> -->
  <xsl:apply-templates select="sections" mode="schriftarten"/>
  <xsl:for-each select="following-sibling::item[lemma=$lemma]/sections"><xsl:apply-templates select="." mode="schriftarten"/></xsl:for-each>
  </xsl:param>
  <!-- doubletten entfernen und sortieren -->
  <xsl:for-each select="$nrn-versalien/nr[not(text()=preceding-sibling::nr/text())]">
    <xsl:sort data-type="number"/>
    <xsl:copy-of select="."/>
  </xsl:for-each>
</xsl:template>

<xsl:template match="schild"><schild><xsl:apply-templates></xsl:apply-templates></schild></xsl:template>
<xsl:template match="oberwappen"><oberwappen><xsl:apply-templates></xsl:apply-templates></oberwappen></xsl:template>
<xsl:template match="nachweis"><nachweis><xsl:apply-templates></xsl:apply-templates></nachweis></xsl:template>

<xsl:template match="nr"></xsl:template>

</xsl:stylesheet>
