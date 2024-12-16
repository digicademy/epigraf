<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

<xsl:template name="angaben_schriftarten">
    <xsl:param name="db"></xsl:param>
    <xsl:param name="article-id"><xsl:value-of select="@id"/></xsl:param>
    <!-- anzahl der inschriften des artikels-->
    <xsl:param name="anzahl_inschriften_total"><xsl:value-of select="count(sections/section[@sectiontype='inscription'])" /></xsl:param>

    <!-- anzahl der inschriftenteile des artikels-->
    <xsl:param name="anzahl_teile_total"><xsl:value-of select="count(.//section[@sectiontype='inscriptionpart'])" /></xsl:param>

    <!-- anzahl aller bearbeitungen des artikels-->
    <xsl:param name="anzahl_bearbeitungen_total"><xsl:value-of select="count(.//section[@sectiontype='inscriptiontext'])" /></xsl:param>

 <!-- das auf den artikel zutreffende teilregister aus dem schriftartenregister extrahieren -->
    <xsl:param name="schriftarten1">
        <!-- es werden im register schriftarten die einträge der obersten ebene aufgerufen, die
            (a) selbst mindestens einen link auf den aktuellen artikel oder
            (b) von denen mindestens ein untereintrag solch einen link enhält-->
        <xsl:for-each select="ancestor::book/indices/index[@type='fonttypes' or @propertytype='fonttypes']/item[sections/section[@articles_id=$article-id] or .//item[sections/section[@articles_id=$article-id]]]">
            <item>
                <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                <links><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                    <xsl:for-each select="sections/section[@articles_id=$article-id]">
                        <link log="link-1">
                            <xsl:copy-of select="@*"/>
                                    <xsl:for-each select="ancestor::book/catalog/article[@id=$article-id]/sections//section[@id=current()/@id][not(ancestor::links)]">
                                    <xsl:copy-of select="@number"/>
                                        <xsl:choose>
                                            <xsl:when test="$modus='projects_bay'"><xsl:number value="@number" format="I"/></xsl:when>
                                            <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                                         </xsl:choose>
                                    </xsl:for-each>
                        </link><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                   </xsl:for-each>
                </links><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
             <xsl:call-template name="angaben-schriftarten-schleife">
                    <xsl:with-param name="article-id"><xsl:value-of select="$article-id"/></xsl:with-param>
                    <xsl:with-param name="db"><xsl:value-of select="$db"/></xsl:with-param>
            </xsl:call-template>
            </item><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        </xsl:for-each>
    </xsl:param>

    <xsl:param name="schriftarten2">
        <!-- die untereinträge werden über das hier aufgerufene template konsolidiert -->
        <xsl:for-each select="$schriftarten1/item">
            <item>
                <xsl:copy-of select="@*"/>
                <xsl:copy-of select="lemma"/>
                <xsl:copy-of select="links"/>
            <xsl:call-template name="angaben-subschriftarten"></xsl:call-template>
            </item>
        </xsl:for-each>
    </xsl:param>

    <!-- links sortieren -->
    <xsl:param name="schriftarten3">
        <xsl:for-each select="$schriftarten2/item[not(contains(lemma,'Ziffern'))][not(contains(lemma,'Zahlzeichen'))]">
            <item>
                <xsl:copy-of select="@*"/>
                <xsl:for-each select="lemma">
                    <lemma><xsl:copy-of select="@*"/><xsl:value-of select="."/></lemma>
                </xsl:for-each>
                <xsl:call-template name="links-sortieren"></xsl:call-template>
                <xsl:call-template name="angaben-subschriftarten-sortieren"></xsl:call-template>
            </item>
        </xsl:for-each>
   </xsl:param>

    <xsl:param name="anzahl-items"><xsl:for-each select="$schriftarten3"><xsl:value-of select="count(.//item[links/link])"/></xsl:for-each></xsl:param>
    <xsl:param name="anzahl_links"><xsl:for-each select="$schriftarten3"><xsl:value-of select="count(.//link)"/></xsl:for-each></xsl:param>

    <!-- ausgabe für DIO: die einzelnen angaben werden fortlaufend zusammengeführt und durch klammern und satzzeichen formatiert, die links bleiben erhalten -->
    <xsl:param name="schriftarten4">
        <!-- zuerst werden die haupteinträge angesteuert -->
        <xsl:for-each select="$schriftarten3/item[not(contains(lemma,'Ziffern'))][not(contains(lemma,'Zahlzeichen'))]">
            <xsl:choose>
                <!-- wenn der erste buchstabe des ersten lemmas ein kleinbuchstabe ist wird er in einen großbuchstaben umgewandelt -->
                <xsl:when test="position()=1">
                    <xsl:value-of select="translate(substring(lemma,1,1),'abcdefghijklmnopqrstuvwxüzäöü','ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜ')"/><xsl:value-of select="substring(lemma,2)"/>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="lemma"/></xsl:otherwise>
            </xsl:choose>

            <xsl:choose>
                <!-- bei nur einer inschrift wird der inschrift-bezeichner (A, B, C) nicht ausgegeben -->
                <xsl:when test="$anzahl_inschriften_total=1">
                    <xsl:if test="item[not(substring(lemma,1,3)='und')] [not(substring(lemma,1,3)='mit')]"><xsl:text>,</xsl:text></xsl:if>
                    <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:choose>
                        <!-- wenn alle inschriften dieselbe schriftart aufweisen, werden die bezeichner nicht ausgegeben -->
                        <xsl:when test="$anzahl-items=1 and $anzahl_inschriften_total = $anzahl_links">
                            <xsl:if test="item[not(substring(lemma,1,3)='und')] [not(substring(lemma,1,3)='mit')]"><xsl:text>,</xsl:text></xsl:if>
                            <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:choose>
                                <xsl:when test="links/link">
                                    <xsl:text> (</xsl:text><xsl:call-template name="links_sort2"></xsl:call-template><xsl:text>)</xsl:text>
                                    <xsl:if test="item[not(substring(lemma,1,3)='und')] [not(substring(lemma,1,3)='mit')]"><xsl:text>,</xsl:text></xsl:if>
                                    <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>
                                </xsl:when>
                                <!-- wenn bei dem lemma oder unterlema keine referenzen auf eine inschrift vorliegt.
                                    werden die Klammern für die bezeichner ausgelassen -->
                                <xsl:otherwise>
                                    <xsl:if test="item[not(substring(lemma,1,3)='und')] [not(substring(lemma,1,3)='mit')]"><xsl:text>,</xsl:text></xsl:if>
                                    <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:otherwise>
            </xsl:choose>

            <!-- ansteuern der untereinträge in flacher folge (d. h. ohne berücksichtigung der hierarchie) -->
            <xsl:for-each select=".//item">
                <xsl:value-of select="lemma"/>
                <xsl:choose>
                    <xsl:when test="$anzahl_inschriften_total = 1">
                        <xsl:if test="item[not(substring(lemma,1,3)='und')] [not(substring(lemma,1,3)='mit')]"><xsl:text>,</xsl:text></xsl:if>
                        <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>
                        <xsl:if test="following-sibling::item"><xsl:text>, </xsl:text></xsl:if>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:choose>
                            <xsl:when test="$anzahl-items=1 and $anzahl_inschriften_total = $anzahl_links">
                                <xsl:if test="item[not(substring(lemma,1,3)='und')] [not(substring(lemma,1,3)='mit')]"><xsl:text>,</xsl:text></xsl:if>
                                <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>
                                <xsl:if test="following-sibling::item"><xsl:text>, </xsl:text></xsl:if>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:choose>
                                    <xsl:when test="links/link">
                                        <xsl:text> (</xsl:text><xsl:call-template name="links_sort2"></xsl:call-template><xsl:text>)</xsl:text>
                                        <xsl:if test="item[not(substring(lemma,1,3)='und')] [not(substring(lemma,1,3)='mit')]"><xsl:text>,</xsl:text></xsl:if>
                                        <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>
                                        <xsl:if test="following-sibling::item"><xsl:text>, </xsl:text></xsl:if>
                                    </xsl:when>
                                    <xsl:otherwise>
                                        <xsl:if test="item[not(substring(lemma,1,3)='und')] [not(substring(lemma,1,3)='mit')]"><xsl:text>,</xsl:text></xsl:if>
                                        <xsl:if test="item"><xsl:text> </xsl:text></xsl:if>
                                        <xsl:if test="following-sibling::item"><xsl:text>, </xsl:text></xsl:if>
                                    </xsl:otherwise>
                                </xsl:choose>

                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>

            </xsl:for-each>
            <xsl:if test="following-sibling::item[not(contains(lemma,'Ziffern'))][not(contains(lemma,'Zahlzeichen'))]"><xsl:text>; </xsl:text></xsl:if>
         </xsl:for-each>
    </xsl:param>

     <!--hier unterhalb können die inhalte der parameter zur kontrolle der zwichenergebnisse angezeigt werden
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <test><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <article-id><xsl:copy-of select="$article-id"/></article-id><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <bearbeitungen><xsl:copy-of select="$anzahl_bearbeitungen_total"/></bearbeitungen><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <sa1><xsl:copy-of select="$schriftarten1"/></sa1><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <sa2><xsl:copy-of select="$schriftarten2"/></sa2><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <sa3><xsl:copy-of select="$schriftarten3"/></sa3><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <sa4><xsl:copy-of select="$schriftarten4"/></sa4><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

       <anzahl-items><xsl:copy-of select="$anzahl-items"/></anzahl-items><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
       <anzahl-links><xsl:copy-of select="$anzahl_links"/></anzahl-links><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        </test><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
-->



    <schriftarten>
        <xsl:copy-of select="$schriftarten4"/><xsl:if test="$schriftarten4/node()"><xsl:text>.</xsl:text></xsl:if>
    </schriftarten><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:template>


<xsl:template name="angaben-schriftarten-schleife">
    <!-- wird im parameter schriftarten1 aufgerufen -->
    <xsl:param name="article-id"></xsl:param>
    <xsl:param name="db"></xsl:param>
    <!-- nur die untereinträge werden angesteuert, die referenzen auf den aktuellen artikel enthalten -->
    <xsl:for-each select="item[sections/section[@articles_id=$article-id] or .//item[sections/section[@articles_id=$article-id]]]">
        <item>
        <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <links><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                    <xsl:for-each select="sections/section[@articles_id=$article-id]">
                        <link log="link-2">
                            <xsl:copy-of select="@*"/>
                                    <xsl:for-each select="ancestor::book/catalog/article[@id=$article-id]//section[@id=current()/@id][not(ancestor::links)]">
<xsl:copy-of select="@number"/>
                                        <xsl:choose>
                                            <xsl:when test="$modus='projects_bay'"><xsl:number value="@number" format="I"/></xsl:when>
                                            <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                                         </xsl:choose>
                                    </xsl:for-each>
                        </link><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                    </xsl:for-each>
            </links><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
             <xsl:call-template name="angaben-schriftarten-schleife">
                    <xsl:with-param name="article-id"><xsl:value-of select="$article-id"/></xsl:with-param>
                    <xsl:with-param name="db"><xsl:value-of select="$db"/></xsl:with-param>
            </xsl:call-template>        </item><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    </xsl:for-each>
</xsl:template>


    <!-- ansteuern der übereinträge -->
    <xsl:template name="item_schleife">
        <xsl:for-each select="preceding-sibling::item[@id=current()/@parent_id]">
            <xsl:call-template name="item_schleife"></xsl:call-template>
            <item>
                <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            </item><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        </xsl:for-each>
    </xsl:template>


    <xsl:template name="angaben-subschriftarten">
        <!-- benachbarte untereinträge 'mit Versal' und 'mit Veralien' und dgl. werden zu 'mit Versalien' und dgl. zusammegeführt  -->
        <xsl:for-each select="item">
            <!-- die lemmata 'mit versal' und 'mit versalien' sowie die entsprechenden varianten werden zusammengezogen -->
            <xsl:choose>
                <!-- versal/versalien -->
                <xsl:when test="lemma='mit Versal' and following-sibling::item/lemma='mit Versalien'">
                    <item>
                        <xsl:copy-of select="@*"/>
                        <lemma>mit Versalien</lemma>
                        <links>
                            <xsl:for-each select="links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                            <xsl:for-each select="following-sibling::item[lemma[text()='mit Versalien']]/links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                        </links>
                        <xsl:call-template name="angaben-subschriftarten"/>
                    </item>
                </xsl:when>
                <xsl:when test="lemma='mit Versalien' and preceding-sibling::item/lemma='mit Versal'"></xsl:when>

                <!-- versal und minuskel/versalien ... -->
                <xsl:when test="lemma='mit Versal und Minuskel' and following-sibling::item/lemma='mit Versalien und Minuskel'">
                    <item>
                        <xsl:copy-of select="@*"/>
                        <lemma>mit Versalien und Minuskel</lemma>
                        <links>
                            <xsl:for-each select="links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                            <xsl:for-each select="following-sibling::item[lemma[text()='mit Versalien und Minuskel']]/links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                        </links>
                        <xsl:call-template name="angaben-subschriftarten"/>
                    </item>
                </xsl:when>
                <xsl:when test="lemma='mit Versalien und Minuskel' and preceding-sibling::item/lemma='mit Versal und Minuskel'"></xsl:when>

                <!-- frakturversal/frakturversalien -->
                <xsl:when test="lemma='mit Frakturversal' and following-sibling::item/lemma='mit Frakturversalien'">
                    <item>
                        <xsl:copy-of select="@*"/>
                        <lemma>mit Frakturversalien</lemma>
                        <links>
                            <xsl:for-each select="links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                            <xsl:for-each select="following-sibling::item[lemma[text()='mit Frakturversalien']]/links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                        </links>
                        <xsl:call-template name="angaben-subschriftarten"/>
                    </item>
                </xsl:when>
                <xsl:when test="lemma='mit Frakturversalien' and preceding-sibling::item/lemma='mit Frakturversal'"></xsl:when>
                <!-- versal in gotischer majuskel/versalien etc -->
                <xsl:when test="lemma='mit Versal in gotischer Majuskel' and following-sibling::item/lemma='mit Versalien in gotischer Majuskel'">
                    <item>
                        <xsl:copy-of select="@*"/>
                        <lemma>mit Versalien in gotischer Majuskel</lemma>
                        <links>
                            <xsl:for-each select="links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                            <xsl:for-each select="following-sibling::item[lemma[text()='mit Versalien in gotischer Majuskel']]/links/link">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                        </links>

                        <xsl:call-template name="angaben-subschriftarten"/>
                    </item>
                </xsl:when>
                <xsl:when test="lemma='mit Versalien in gotischer Majuskel' and preceding-sibling::item/lemma='mit Versal in gotischer Majuskel'"></xsl:when>

                <!-- andere einträge -->
                <xsl:otherwise>
                    <item>
                        <xsl:copy-of select="@*"/>
                        <xsl:copy-of select="lemma"/>
                        <xsl:copy-of select="links"/>
                        <xsl:call-template name="angaben-subschriftarten"/>
                    </item>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>



    <xsl:template name="angaben-subschriftarten-sortieren">
        <!-- zum sortieren der links wird der elemente-baum abgearbeitet
            die sortierunng ist erforderlich, weil beim zusammenführen der versal(ien)-items
            gegebenenfalls höhere nummern nach vorn gelangt sein können
        -->
        <xsl:for-each select="item">
           <item>
            <xsl:copy-of select="@*"/>
            <xsl:copy-of select="lemma"/>
            <xsl:call-template name="links-sortieren"></xsl:call-template>
            <xsl:call-template name="angaben-subschriftarten-sortieren"></xsl:call-template>
           </item>
        </xsl:for-each>
    </xsl:template>
    <xsl:template name="links-sortieren">
         <links>
            <xsl:for-each select="links/link">
                <!--<xsl:sort select="@inscript-number" data-type="number"/>-->
                <xsl:sort select="@number" data-type="number"/>
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </links>
        <!-- hier kann man über eine weitere schleife die links verdichten: A, B, C, D zu A-D;
             die ansprache kann dabei über das attribut inscript-number erfolgen -->
    </xsl:template>

<xsl:template name="links_sort2">
    <xsl:param name="p_linsk_sort2">
        <xsl:for-each select="links/link"><xsl:sort select="@number"  data-type="number"/><xsl:copy-of select="."/></xsl:for-each>
    </xsl:param>
<links>
    <xsl:for-each select="$p_linsk_sort2/link">
        <link type="inschrift">
           <xsl:attribute name="target_id"><xsl:value-of select="@id"/></xsl:attribute>
           <xsl:copy-of select="@number"/>
            <xsl:value-of select="."/>
        </link><!--<xsl:if test="following-sibling::link"><xsl:text>, </xsl:text></xsl:if>-->
    </xsl:for-each>
</links>

</xsl:template>

</xsl:stylesheet>
