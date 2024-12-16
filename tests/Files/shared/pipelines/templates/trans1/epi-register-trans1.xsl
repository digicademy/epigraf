<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>
    <!--<xsl:import href="epi44-switch.xsl"/>-->

<!-- wenn in den textbausteinen des bandes die nummern und titel der register angelegt sind, werden sie von dort ausgelesen,
    anderenfalls werden diese im template index pauschal vergeben-->

<!-- parameter mit den bezeichnungen der register -->
<!-- für nichtdeutsche bezeichnungen muss für jedes <item propertytype="*">
        ein item mit dem zutreffenden sprachkürzel im attribut @lang erzeugt
        und die andersprachige bezeichnung als textknoten eingefügt werden   -->
<xsl:param name="p_index-terms">
<item propertytype="locations" lang="de" initialgroups="1">Standorte</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="personnames" lang="de" initialgroups="1">Personen</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="manufacturers" lang="de" initialgroups="1">Künstler, Meister Werkstätten</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="placenames" lang="de" initialgroups="1">Ortsnamen und andere geographische Einheiten</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="heraldry" lang="de" initialgroups="1">Wappen und Marken</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="epithets" lang="de" initialgroups="1">Berufe, Stände, Titel und Attribute</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="incipits" lang="de" initialgroups="1">Initien</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="formulas" lang="de" initialgroups="0">Formeln und besondere Wendungen</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="texttypes" lang="de" initialgroups="0">Texttypen und Inschriftenarten</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="sources" lang="de" initialgroups="0">Nachweise von Zitaten und Paraphrasen</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="objecttypes" lang="de" initialgroups="1">Inschriftenträger</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="fonttypes" lang="de" initialgroups="1">Schriftarten</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="subjects" lang="de" initialgroups="1">Sachregister</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="materials" lang="de" initialgroups="0">Materialien</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="techniques" lang="de" initialgroups="0">Herstellungstechniken</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="wordseparators" lang="de" initialgroups="0">Worttrenner und Satzzeichen</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="metres" lang="de" initialgroups="0">Versmaße</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="icons" lang="de" initialgroups="1">Heilige, biblische und historische Personen, Allegorie, Mythologie, Ikonographie</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="initials" lang="de" initialgroups="1">Initialen</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="literature" lang="de" initialgroups="1">Quellen und Literatur</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="languages" lang="de" initialgroups="0">Sprachen</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<item propertytype="brands" lang="de" initialgroups="0">Marken</item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:param>



    <xsl:template name="bandregister">
        <xsl:param name="p_basisstandort"><xsl:value-of select="ancestor::book/project/name"/></xsl:param>
        <!-- der register-abschnitt in den textbausteinen des bandes wird aufgesucht -->
        <xsl:for-each select="ancestor::book/volume/section[@data_key='indices']">
        <xsl:comment>ausgabe über template bandregister</xsl:comment><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <!-- die überschrift des gesamtregisters wird ausgelesen -->
            <titel><xsl:value-of select="@name"/></titel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <!-- die allgemeinen anmerkungen zu den registern werden, wenn vorhanden, ausgelesen -->
            <notiz><xsl:apply-templates select="items/item[@itemtype='chapter']/content"/></notiz><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <!-- die einzelnen register-abschnitte werden angesteuert -->

            <xsl:for-each select="section">
                            <!-- für die zuordnung des entsprechenden registers wird der erforderliche key in eine variable gegeben -->
                            <xsl:variable name="key1"><xsl:value-of select="@data_key"/></xsl:variable>
                            <xsl:variable name="caption"><xsl:value-of select="@name"/></xsl:variable>
            <xsl:choose>
            <!-- registergruppen -->
                <xsl:when test="$key1='indexgroup'">
                    <index>
                                <!-- das type-attribut wird via $key1 aus dem attribut @section gebildet -->

                                <xsl:attribute name="type"><xsl:value-of select="$key1"/> </xsl:attribute>
                                <xsl:attribute name="nr">
                                    <xsl:value-of select="substring-before(@name, '. ')"/>
                                </xsl:attribute>
                                <xsl:attribute name="titel">
                                    <xsl:value-of select="substring-after(@name, '. ')"/>
                                </xsl:attribute>
                                <xsl:copy-of select="@level"/>
                                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                <!-- überschrift und anmerkungen zum register werden ausgelesen-->
                                <nr><xsl:value-of select="substring-before($caption, '. ')"/></nr><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                <titel><xsl:value-of select="substring-after($caption, '. ')"/></titel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                <notiz><xsl:apply-templates select="items/item[@itemtype='chapter']/content"/></notiz><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </index>
            <!-- die untergeordneten einzelregister einer registergruppe werden danach ausgelesen -->
                <xsl:for-each select="section">
                    <xsl:call-template name="einzelregister"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
                </xsl:for-each>
                </xsl:when>
            <!-- die einzelregister außerhalb von registergruppen -->
            <xsl:otherwise>
                <xsl:call-template name="einzelregister"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
            </xsl:otherwise>
            </xsl:choose>
            </xsl:for-each>
        </xsl:for-each>
        <!-- hier werden noch die nicht im band angelegten register
            1. hausmarken, 2. meisterzeichen 3. abmessungen, 4. literatur und 5. archivalien eingefügt  -->

       <!-- marken  -->
        <xsl:for-each select="ancestor::book/indices/index[@propertytype='brands']">
            <index type="{@propertytype}" titel="Marken" initialgruppen="1">
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:call-template name="register-marken"></xsl:call-template>
            </index><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>

        <!-- abmessungen
        <xsl:copy-of select="ancestor::book/manage_lists/index[@propertytype='measures']"/>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>-->
    </xsl:template>

    <xsl:template name="einzelregister">
                <!-- für die zuordnung des entsprechenden registers wird der erforderliche key in eine variable gegeben -->
                <xsl:param name="key1"><xsl:value-of select="@data_key"/></xsl:param>
                <xsl:param name="p_basisstandort"></xsl:param>

                <!-- der titel des register wird in einer variable vorformatiert: bindestrich zwischen leerzeichen wird durch halbgeviertstrich ersetzt -->
                <xsl:variable name="caption">
                    <xsl:choose>
                        <xsl:when test="contains(@name, ' - ')">
                            <xsl:value-of select="substring-before(@name, ' - ')"/><xsl:text> – </xsl:text><xsl:value-of select="substring-after(@name, ' - ')"/>
                        </xsl:when>
                        <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>

                <!-- das einzelne register wird angelegt-->
                <index>
                    <!-- das type-attribut wird via $key1 aus dem attribut @section gebildet -->
                    <xsl:attribute name="type"><xsl:value-of select="$key1"/> </xsl:attribute>
                    <xsl:attribute name="initialgruppen">
                            <xsl:for-each select="$p_index-terms/item[@propertytype=$key1]"><xsl:value-of select="@initialgroups"/></xsl:for-each>
                     </xsl:attribute>
                    <xsl:attribute name="nr">
                        <xsl:value-of select="substring-before(@name, '. ')"/>
                    </xsl:attribute>
                    <xsl:attribute name="titel">
                        <xsl:value-of select="substring-after(@name, '. ')"/>
                    </xsl:attribute>
                    <xsl:copy-of select="@level"/>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

                    <!-- überschrift und anmerkungen zum register werden ausgelesen-->
                    <nr><xsl:value-of select="substring-before($caption, '. ')"/></nr><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <titel><xsl:value-of select="substring-after($caption, '. ')"/></titel><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <notiz><xsl:apply-templates select="items/item[@itemtype='chapter']/content"/></notiz><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <!-- der jeweils zugehörige registerbaum wird angesteuert -->

                    <!-- für die wappenbeschreibungen existiert kein eigenes register,
                        vielmehr werden sie aus dem wappenregister generiert, das hierzu angesteuert wird-->
                    <xsl:if test="$key1='blazons'">
                        <xsl:for-each select="ancestor::book/indices/index[@propertytype='heraldry']">
                            <xsl:call-template name="register-blasonierungen"></xsl:call-template>
                            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </xsl:for-each>
                    </xsl:if>
                    <!-- das register Besonderheiten der Datierung muss aus dem Sachregister herausgezogen werden -->

                    <xsl:if test="$key1='timing'">
                        <xsl:for-each select="ancestor::book/indices/index[@propertytype='subjects']/item[starts-with(lemma,'Dat')]">
                            <xsl:call-template name="register_hierarchisieren"></xsl:call-template>
                            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </xsl:for-each>
                    </xsl:if>

                    <!-- alle anderen register werden durch den key angesteuert und gegebenenfalls speziellen templates zugewiesen-->
                    <xsl:for-each select="ancestor::book/indices/index[@propertytype=$key1]">

                        <xsl:choose>
                            <!-- wappen -->
                            <xsl:when test="$key1='heraldry'">
                                <xsl:call-template name="register-wappen-2"></xsl:call-template>
                                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:when>
                            <!-- zitate -->
                            <xsl:when test="$key1='sources'">
                                <xsl:call-template name="register_hierarchisieren_3"></xsl:call-template>
                                 <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:when>
                            <xsl:when test="$key1='locations'">
                                <xsl:call-template name="register-standorte">
                                    <xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param>
                                </xsl:call-template>
                                 <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:call-template name="register_hierarchisieren"/>
                                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:otherwise>
                </xsl:choose>
              </xsl:for-each>
                    <!-- wenn andere register als untereinträge incorporiert werden sollen, werden sie über den key ausgelesen  -->
                    <!-- zu incorporierende register werden angesteuert-->
                    <xsl:for-each select="section">
                        <xsl:variable name="key2"><xsl:value-of select="@data_key"/></xsl:variable>
                        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        <item>
                            <!-- die attribute des abschnitts werden übernommen -->
                            <xsl:copy-of select="@*"/>
                            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <!-- als lemma wird das attribut caption des jeweiligen abschnitts eingetragen;
                                daraus werden auch die attribute sortchart und sortstring generiert -->
                            <lemma log="sl1">
                                <xsl:attribute name="sortchart"><xsl:value-of select="translate(substring(@name,1,1),'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
                                <xsl:attribute name="sortstring"><xsl:value-of select="translate(@name,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
                                <xsl:value-of select="@name"/>
                            </lemma>
                            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <!-- die einträge des incorporierten registers werden als untereinträge aufgenommen -->
                            <xsl:for-each select="ancestor::book/indices/index[@propertytype=$key2]">
                                <xsl:call-template name="register_hierarchisieren"></xsl:call-template>
                                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:for-each>
                        </item>
                    </xsl:for-each>
             </index><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>



</xsl:template>

<!-- ENDE: bandregister -->

    <xsl:template match="wunsch[@tagname='key']"><!-- das key-element wird für die ausgabe unterdrückt --></xsl:template>


<xsl:template match="index">
    <xsl:param name="p_basisstandort"><xsl:value-of select="ancestor::book/project/name"/></xsl:param>
    <xsl:param name="p_propertytype"><xsl:value-of select="@propertytype"/></xsl:param>
    <index>
        <xsl:copy-of select="@*"/>
        <xsl:attribute name="titel"><xsl:value-of select="$p_index-terms/item[@propertytype=$p_propertytype]"/></xsl:attribute>
        <xsl:attribute name="als_register_ausgeben">1</xsl:attribute>
        <xsl:attribute name="initialgruppen"><xsl:value-of select="$p_index-terms/item[@propertytype=$p_propertytype]/@initialgroups"/></xsl:attribute>
        <xsl:choose>
            <xsl:when test="@propertytype='languages'"></xsl:when>
            <xsl:when test="@propertytype='locations'">
                <xsl:call-template name="register-standorte"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
            </xsl:when>
            <xsl:when test="@propertytype='brands'">
                <xsl:call-template name="register-marken"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            </xsl:when>
            <xsl:when test="@propertytype='heraldry'">
                <xsl:call-template name="register-wappen-2"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            </xsl:when>
              <xsl:otherwise><xsl:call-template name="register_hierarchisieren"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text></xsl:otherwise>
        </xsl:choose>
    </index>
</xsl:template>

<!--<xsl:template name="item">
    <xsl:for-each select="item[.//sections]">
        <item logr1="item1">
            <xsl:copy-of select="@*"/>
            <xsl:copy-of select="lemma"/>
            <xsl:copy-of select="sections"/>
            <xsl:call-template name="crossRefs"></xsl:call-template>
            <xsl:call-template name="item"></xsl:call-template>
        </item>
    </xsl:for-each>
</xsl:template>-->

    <xsl:template name="subitem-standorte-schleife">
        <!-- die untereinträge im standorte-register werden hierarchisiert -->
        <xsl:for-each select="item">
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <item log="2">
                <xsl:copy-of select="@*"/>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:call-template name="crossRefs"></xsl:call-template>
                <xsl:copy-of select="sections"/>
                <xsl:call-template name="subitem-standorte-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>


    <xsl:template name="links-textsorten">
        <!-- nach sprachen differenzieren -->
        <xsl:param name="links_textsorten1">
            <xsl:for-each select="section[not(@lang_alias=preceding-sibling::section/@lang_alias)]">
                 <xsl:sort select="@lang_sort" data-type="text"  order="ascending"></xsl:sort>
               <xsl:variable name="sprache"><xsl:value-of select="@lang_alias"/></xsl:variable>
                <sprache>
                    <xsl:attribute name="sprache"><xsl:value-of select="$sprache"/></xsl:attribute>
                    <xsl:copy-of select="."/>
                    <xsl:for-each select="following-sibling::section[@lang_alias=$sprache]">
                        <xsl:copy-of select="."/>
                    </xsl:for-each>
                </sprache>
            </xsl:for-each>
        </xsl:param>
        <!-- nach vers differenzieren und doubletten entfernen-->
        <xsl:param name="links_textsorten2">
            <xsl:for-each select="$links_textsorten1/sprache">
                <sprache>
                    <xsl:copy-of select="@*"/>
                    <xsl:for-each select="section[@metres='0'][not(@articles_id=preceding-sibling::section/@articles_id)]">
                        <xsl:copy-of select="."/>
                    </xsl:for-each>
                    <xsl:if test="section[@metres='1']">
                        <vers>
                            <xsl:for-each select="section[@metres='1'][not(@articles_id=preceding-sibling::section/@articles_id)]">
                            <xsl:copy-of select="."/>
                            </xsl:for-each>
                        </vers>
                    </xsl:if>
                </sprache>
            </xsl:for-each>
        </xsl:param>
        <!-- neu strukturieren: untereinträge aus sprache und vers generieren -->
        <xsl:param name="links_textsorten3">
            <xsl:for-each select="$links_textsorten2">
                <xsl:if test="sprache[string-length(@sprache)=0]">
                    <sections log="secs1"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="sprache[string-length(@sprache)=0]/section">
                        <xsl:sort select="sort"/>
                        <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                    </sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:if>
                <xsl:if test="sprache[string-length(@sprache)!=0]">
                    <xsl:for-each select="sprache[string-length(@sprache)!=0]">
                        <item log="3"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <lemma><xsl:value-of select="@sprache"/></lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:if test="section">
                                <sections log="secs2"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                   <xsl:for-each select="section">
                                       <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                   </xsl:for-each>
                                </sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:if>
                            <xsl:if test="vers">
                                <item log="4"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                    <lemma>Vers</lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                    <sections log="secs3"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                        <xsl:for-each select="vers/section">
                                            <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                        </xsl:for-each>
                                    </sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:if>
                        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                </xsl:if>
            </xsl:for-each>
        </xsl:param>
<!--  parametertest
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <textsorten1><xsl:copy-of select="$links_textsorten1"/></textsorten1><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <textsorten2><xsl:copy-of select="$links_textsorten2"/></textsorten2><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
 </parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
 -->
        <!-- ergebnis einfügen -->
        <xsl:copy-of select="$links_textsorten3"/>

    </xsl:template>

    <xsl:template name="register-sachregister">
        <xsl:param name="sachregister1">
            <xsl:call-template name="register_hierarchisieren"></xsl:call-template>

            <xsl:if test="ancestor::indices/index[@propertytype='wordseparators']">
            <item id="properties-0" log="5"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <lemma sortchart="w" sortstring="worttrenner und satzzeichen">Worttrenner und Satzzeichen</lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="ancestor::indices/index[@propertytype='wordseparators']">
                    <xsl:call-template name="register_hierarchisieren"></xsl:call-template>
                </xsl:for-each>
            </item>
            </xsl:if>

            <xsl:if test="ancestor::indices/index[@propertytype='materials']">
            <item id="properties-0" log="6"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <lemma sortchart="m" sortstring="material">Material</lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="ancestor::indices/index[@propertytype='materials']">
                    <xsl:call-template name="register_hierarchisieren"></xsl:call-template>
                </xsl:for-each>
            </item>
            </xsl:if>

            <xsl:if test="ancestor::indices/index[@propertytype='techniques']">
            <item id="properties-0" log="7"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <lemma sortchart="a" sortstring="ausführungstechnik der inschriften">Ausführungstechnik der Inschriften</lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="ancestor::indices/index[@propertytype='techniques']">
                    <xsl:call-template name="register_hierarchisieren"></xsl:call-template>
                </xsl:for-each>
            </item>
            </xsl:if>

            <!-- das herstellerregister wird optionsweise in das sachregister eingebunden oder als selbständiges register ausgegeben -->
            <xsl:if test="$herstellerregister!=1 and ancestor::indices/index[@propertytype='manufacturers']">
                <item id="properties-0" log="8"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <lemma sortchart="m" sortstring="meister und werkstätten">Meister und Werkstätten</lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="ancestor::indices/index[@propertytype='manufacturers']">
                        <xsl:call-template name="register_hierarchisieren"></xsl:call-template>
                    </xsl:for-each>
                </item>
            </xsl:if>
            <!-- das versregister wird optionsweise in das sachregister eingebunden oder als selbständiges register ausgegeben -->
            <xsl:if test="$versregister!=1 and ancestor::index/index[@propertytype='metres']">
                <item id="properties-0" log="9"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <lemma sortchart="v" sortstring="versmaße">Versmaße</lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="ancestor::index/index[@propertytype='metres']">
                        <xsl:call-template name="register_hierarchisieren"></xsl:call-template>
                    </xsl:for-each>
                </item>
            </xsl:if>
        </xsl:param>
       <!-- <test_sachregister><xsl:copy-of select="$sachregister1"/></test_sachregister>-->
        <xsl:for-each select="$sachregister1/item[not(@ishidden='1')]">
            <xsl:sort select="lemma/@sortstring" order="ascending" data-type="text" lang="de"/>
            <xsl:copy-of select="."/>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="register-standorte">
        <xsl:param name="p_basisstandort"></xsl:param>
        <xsl:param name="standorte1">
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
                <xsl:variable name="v_lemma"><xsl:value-of select="lemma"/></xsl:variable>
                <!-- im standortregister wird der basisstandort (der der projektbezeichnung entspricht) ignoriert
                    und gleich auf die untereinträge weitergeleitet-->
                <!-- für städte -->

              <xsl:choose>
                    <xsl:when test="$v_lemma=$p_basisstandort and not($basisstandort_ausgeben=1)">
                        <xsl:call-template name="subitem-standorte-schleife"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
                    </xsl:when>
                    <!-- für landkreise -->
                    <xsl:when test="substring(lemma,1,5)= 'Ldkr.'">
                        <xsl:call-template name="ldkr"/>
                    </xsl:when>
                    <xsl:otherwise>
                         <item log="10">
                           <xsl:copy-of select="@*"/>
                            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:apply-templates select="lemma"/>
                            <xsl:for-each select="sections">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                            <xsl:call-template name="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="subitem-standorte-schleife"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
                        </item>
                     </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:param>

        <xsl:param name="standorte2">
            <!-- 2. transformationschritt: neusortieren der items auf der obersten ebene -->
        <xsl:for-each select="$standorte1/item">
            <xsl:sort select="lemma/@sortstring" lang="de"/>
            <xsl:copy-of select="."/>
        </xsl:for-each>
        </xsl:param>

        <xsl:param name="standorte3">
            <!-- 3. transformationsschritt: unterscheiden zwischen items mit referenzen auf ehemalige und aktuelle
                standorte,
            items mit gemischten referenzen werden dupliziert -->
            <xsl:for-each select="$standorte2">
                <xsl:call-template name="standorte-trennen"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
            </xsl:for-each>
        </xsl:param>

<xsl:param name="standorte4">
            <xsl:for-each select="$standorte3/item">
                <xsl:call-template name="standorte-trennen2"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
            </xsl:for-each>
</xsl:param>


<!--    parametertest
<xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
<parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <standorte1><xsl:copy-of select="$standorte1"/></standorte1><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <standorte2><xsl:copy-of select="$standorte2"/></standorte2><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <standorte3><xsl:copy-of select="$standorte3"/></standorte3><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <standorte4><xsl:copy-of select="$standorte4"/></standorte4><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <basisstandort><xsl:value-of select="$p_basisstandort"/></basisstandort><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</parametertest><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
-->

        <xsl:for-each select="$standorte4/item">
            <xsl:call-template name="standorte-bereinigen"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:template>

    <xsl:template name="standorte-bereinigen">
          <xsl:param name="p_basisstandort"></xsl:param>
        <xsl:param name="standort_status"><xsl:value-of select="@standort-status"/></xsl:param>
        <!-- die registerbäume werden dahingenden bereinigt, dass unter den aktuellen standorten
            nur die links auf den aktuellen standort ausgewählt werden sowie vice vers mit den ehemaligen-->
        <item>
            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="crossRefs">
                <xsl:call-template name="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
            <xsl:if test="sections/section[@kursiv=$standort_status]">
                <sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="sections/section[@kursiv=$standort_status]">
                        <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                </sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:if>

<!-- altes muster: führt dazu, dass kursive standorte unterhalb von nicht kursiven standorten nicht ausgelesen werden, hwi siehe Zurow (Lkr. Nordwestmecklenburg), Kirche
erst wenn die bedingung [.//section[@kursiv=$standort_status]] zu item entfällt, wird Kirche ausgelesen
-->
<!-- <xsl:for-each select="item[.//section[@kursiv=$standort_status]]"> -->
            <xsl:for-each select="item">
            <xsl:call-template name="standorte-bereinigen"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
            </xsl:for-each>
        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:template>

    <xsl:template name="ldkr">
        <xsl:param name="ldkr"><xsl:text> (</xsl:text><xsl:value-of select="lemma"/><xsl:text>)</xsl:text></xsl:param>
        <xsl:for-each select="item">
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <item log="11">
                    <xsl:copy-of select="@*"/>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <lemma log="ldkr">
                        <xsl:copy-of select="lemma/@*"/>
                        <xsl:value-of select="lemma"/><xsl:value-of select="$ldkr"/>
                    </lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="sections">
                        <xsl:copy-of select="."/>
                    </xsl:for-each>
            <xsl:for-each select="crossRefs">
                <xsl:call-template name="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
                    <xsl:call-template name="subitem-standorte-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>

    <xsl:template name="standorte-trennen">
        <xsl:param name="p_basisstandort"></xsl:param>
        <!-- trennung der standort-einträge nach aktuellen und ehemaligen standorten -->
        <xsl:for-each select="item">
            <xsl:choose>
                <!-- zuerst werden die einträge angesteuert, die nicht ausschließlich verweise enthalten;
                diese werden unterschieden in einträge, die
                (1) auf artikel/objekte verweisen in denen der angebene standort der aktuelle oder letzt bekannte ist und
                (2) auf artikel/objekte in denen der angegebene standort ein früherer/ehemaliger standort ist-->
                <xsl:when test="sections/section">
                    <!-- 1. aktuelle standorte (@kursiv='0')-->
                    <xsl:choose>
                        <!-- wenn zum betreffenden eintrag oder in untereinträgen verweise auf aktuelle standorte vorkommen,
                        wird der eintrag angelegt, aber nur die verweise auf aktuelle standorte auf derselben ebene,
                        wenn vorhanden, werden hier gesammelt; danach werden die untereinträge aufgerufen und analog behandelt -->
                        <xsl:when test="sections/section[@kursiv='0']">
                        <item log3="12">
                            <xsl:copy-of select="@*"/>
                            <xsl:attribute name="standort-status">0</xsl:attribute>
                            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:if test="sections/section">
                                <sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                    <xsl:for-each select="sections/section[@kursiv='0']">
                                        <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                    </xsl:for-each>
                                </sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:if>
                            <xsl:call-template name="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="standorte-trennen"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
                        </item> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <!-- wenn auf derselben ebene auch verweise auf ehemalige standorte vorkommen,
                                wird der eintrag verdoppelt und die verweise auf ehemalige standorte werden hier gesammelt -->
                            <xsl:if test="sections/section[@kursiv='1']">
                                <item log3="13">
                                    <xsl:copy-of select="@*"/>
                                    <xsl:attribute name="standort-status">1</xsl:attribute>
                                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                    <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                    <xsl:if test="sections/section">
                                        <sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                            <xsl:for-each select="sections/section[@kursiv='1']">
                                                <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                            </xsl:for-each>
                                        </sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                    </xsl:if>
                                    <xsl:for-each select="crossRefs">
                                        <xsl:call-template name="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                    </xsl:for-each>
                                    <xsl:call-template name="standorte-trennen"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
                                </item> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:if>
                        </xsl:when>

                        <!-- 2. ehemalige standorte (@kursiv='1') -->
                        <xsl:otherwise>
                        <item log3="14">
                            <xsl:copy-of select="@*"/>
                            <xsl:attribute name="standort-status">1</xsl:attribute>
                            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:copy-of select="lemma" /><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:if test="sections/section">
                             <sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                 <xsl:for-each select="sections/section[@kursiv='1']">
                                     <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                 </xsl:for-each>
                             </sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:if>
                            <!-- verweise werden nur ausgelesen, wenn sie nicht schon vorher
                                unter @kursiv='0' erfasst wurden, d. h.
                            wenn der standort nicht auch als aktueller standort erfasst ist-->
                            <xsl:if test="not(sections/section[@kursiv='0'])">
            <xsl:for-each select="crossRefs">
                <xsl:call-template name="crossRefs"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
                            </xsl:if>
                            <xsl:call-template name="standorte-trennen"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
                        </item> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <!-- wenn keine referenzen auf artikelnummern aber verweise vorkommen -->
                <xsl:otherwise>
                    <item log3="15">
                        <xsl:copy-of select="@*"/>
                        <xsl:attribute name="standort-status">0</xsl:attribute>
                        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        <xsl:copy-of select="lemma" /><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="crossRefs">
                <xsl:call-template name="crossRefs"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
                        <xsl:call-template name="standorte-trennen"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
                    </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>

<xsl:template name="standorte-trennen2">
        <xsl:param name="p_basisstandort"></xsl:param>
        <xsl:variable name="p_standort-status"><xsl:value-of select="@standort-status"/></xsl:variable>
<xsl:choose>
<!-- aktuelle standorte -->
    <xsl:when test="$p_standort-status='0'">
        <xsl:choose>
        <!-- wenn es denselben standort auch als ehemaligen gibt -->
            <xsl:when test="preceding-sibling::item[@id=current()/@id and @standort-status='1'] or following-sibling::item[@id=current()/@id and @standort-status='1']">
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy><xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy-of select="sections"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="item[@standort-status='0']"><xsl:call-template name="standorte-trennen2"></xsl:call-template></xsl:for-each>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:copy><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:when>
            <xsl:otherwise><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy><xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy-of select="sections"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="item"><xsl:call-template name="standorte-trennen2"></xsl:call-template></xsl:for-each>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:copy><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:when>
<!-- ehemalige standorte -->
    <xsl:when test="$p_standort-status='1'">
        <xsl:choose>
        <!-- wenn es denselben standort auch als aktuellen gibt -->
            <xsl:when test="preceding-sibling::item[@id=current()/@id and @standort-status='0'] or following-sibling::item[@id=current()/@id and @standort-status='0']">
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy><xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy-of select="sections"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="item[@standort-status='1']"><xsl:call-template name="standorte-trennen2"></xsl:call-template></xsl:for-each>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:copy><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:when>
            <xsl:otherwise>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy><xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy-of select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:copy-of select="sections"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="item"><xsl:call-template name="standorte-trennen2"></xsl:call-template></xsl:for-each>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </xsl:copy><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:when>
</xsl:choose>




</xsl:template>

    <xsl:template name="register_hierarchisieren">
        <xsl:choose>
            <xsl:when test="item[@iscategory!='']"><!-- gruppeneinträge -->
                <xsl:call-template name="register_hierarchisieren_2"></xsl:call-template>
            </xsl:when>
            <xsl:otherwise> <!-- einfache einträge -->
                <xsl:call-template name="register_hierarchisieren_1"></xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>

    </xsl:template>

<xsl:template name="register_hierarchisieren_3">

<!-- dieses template wird aufgerufen aus dem template index und aus dem template bandregister
zur strukturierung des register quellen -->

    <!-- zuerst die einfachen einträge sammeln, danach die gruppierungseinträge hinten anstellen -->
    <xsl:for-each select="item[not(@iscategory='1')]">
        <item log="16a">
            <xsl:copy-of select="@*"/>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:if test="lemma"><xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
<!--            <xsl:if test="name"><xsl:apply-templates select="name"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
            <xsl:if test="regeintrag"><xsl:apply-templates select="regeintrag"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>-->
            <xsl:for-each select="sections">
                <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
            <xsl:call-template name="crossRefs"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:call-template name="register_hierarchisieren_3"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:for-each>
    <xsl:for-each select="item[@iscategory='1']">
        <xsl:variable name="lemma_ausblenden"><xsl:value-of select="@ishidden"/></xsl:variable>
        <item log="17a">
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="type">group</xsl:attribute>
            <xsl:attribute name="groupname"><xsl:value-of select="lemma"/></xsl:attribute>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:apply-templates select="lemma[not($lemma_ausblenden=1)]"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:call-template name="crossRefs"></xsl:call-template>
            <xsl:copy-of select="sections"/> <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
             <xsl:call-template name="register_hierarchisieren_3"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:for-each>
</xsl:template>


<xsl:template name="register_hierarchisieren_2">
    <xsl:param name="reg-type"><xsl:value-of select="@propertytype"/></xsl:param>
    <!-- zuerst die einfachen einträge sammeln, danach die gruppierungseinträge hinten anstellen -->
    <!-- auswahl einschränken auf items mit referenzen auf artikelnummern (sections) oder mit querverweisen (crossRef) -->
            <xsl:for-each select="item[not(@iscategory='1')][not(@ishidden='1')][.//sections or .//crossRef]">
                <xsl:sort lang="de" select="lemma/@sortstring" case-order="upper-first" order="ascending" data-type="text"/>
                <item logr1="item16">
                    <xsl:copy-of select="@*"/>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                   <!-- <parametertest><reg-type><xsl:value-of select="$reg-type"/></reg-type></parametertest>-->
                    <xsl:if test="lemma"><xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                    <xsl:if test="name and not(lemma)"><xsl:apply-templates select="name"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text></xsl:if>
                    <xsl:choose>
                        <!-- im textsortenregister werden die links besonders behandelt -->
                        <!-- Sonderregelung interim für Wittenberg: <xsl:when test="contains($reg-type,'textsorten') and not(ancestor::book/project[1]/datenbank[text()='inschriften_sanh'])"> -->
                        <xsl:when test="$reg-type='texttypes'">
                            <xsl:for-each select="sections">
                                <xsl:call-template name="links-textsorten"></xsl:call-template>
                                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:for-each>
                        <xsl:call-template name="crossRefs"></xsl:call-template>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:for-each select="sections">
                                <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:for-each>
                        <xsl:call-template name="crossRefs"></xsl:call-template>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:call-template name="register_hierarchisieren_2"><xsl:with-param name="reg-type"><xsl:value-of select="$reg-type"/></xsl:with-param></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>

    <!-- gruppeneinträge -->

    <xsl:for-each select="item[@iscategory='1']">
        <xsl:variable name="lemma_ausblenden"><xsl:value-of select="@ishidden"/></xsl:variable>
        <item log="17">
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="type">group</xsl:attribute>
            <xsl:attribute name="groupname"><xsl:value-of select="lemma"/></xsl:attribute>


            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:apply-templates select="lemma[not($lemma_ausblenden=1)]"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:choose>
                <!-- im textsortenregister werden die links besonders behandelt -->
                <xsl:when test="$reg-type='texttypes'">
                    <xsl:for-each select="sections">
                        <xsl:call-template name="links-textsorten"></xsl:call-template>
                        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                   <xsl:call-template name="crossRefs"></xsl:call-template>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:copy-of select="sections"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:call-template name="crossRefs"></xsl:call-template>
                </xsl:otherwise>
            </xsl:choose>
             <xsl:call-template name="register_hierarchisieren_2"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:for-each>
</xsl:template>

    <xsl:template name="register_hierarchisieren_1">
        <!-- dieses template dient dazu, ein register zu hierarchisieren und gruppen zu bilden;
            das ergebnis kann in einem parameter zur weiterbearbeitung abgelegt werden
        -->
            <xsl:choose>
                <!-- im personenregister werden nicht aufgelöste initialen zur gruppe (ohne gruppenlemma) transformiert
                und an das ende gestellt-->
                <xsl:when test="@propertytype='personnames'">
                    <xsl:for-each select="item[not(contains(lemma,'nitialen'))]">
                        <xsl:sort select="lemma/@sortstring" data-type="text" lang="de"/>
                        <item log="18">
                            <xsl:copy-of select="@*"/>
                            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:for-each select="sections">
                                <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:for-each>
                            <xsl:call-template name="crossRefs"></xsl:call-template>
                            <xsl:call-template name="subitem-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                    <!-- nicht aufgelöste initialen -->
                    <xsl:for-each select="item[@level='0'][contains(lemma,'nitialen')]">
                        <item log="19">
                            <xsl:copy-of select="@*"/>
                            <xsl:attribute name="type">group</xsl:attribute>
                            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="subitem-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
               </xsl:when>

                <!-- wappen -->
                <xsl:when test="@propertytype='heraldry'">
                    <xsl:for-each select="item">
                        <item log="20">
                            <xsl:copy-of select="@*"/>
                            <xsl:apply-templates select="name"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:copy-of select="sections"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="crossRefs"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="subitem-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                </xsl:when>

                <!-- marken -->
                <xsl:when test="@propertytype='brands'">
                    <xsl:for-each select="item[@level='0']">
                        <item log="22">
                            <xsl:copy-of select="@*"/>
                            <xsl:apply-templates select="name"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:copy-of select="sections"></xsl:copy-of><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="crossRefs"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="subitem-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                </xsl:when>

                <!-- im epitheta-register werden die einträge der ersten ebene als gruppen (ohne gruppenlemma) transformiert -->
                <xsl:when test="@propertytype='epithets'">
                    <xsl:for-each select="item">
                        <!-- sortierung wird übernommen -->
                        <!--<xsl:sort select="@sort"/>-->
                        <item log="23">
                             <xsl:attribute name="groupname"><xsl:apply-templates select="lemma"/></xsl:attribute>
                            <xsl:attribute name="type">group</xsl:attribute>
                            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="subitem-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                 </xsl:when>

                <!-- im formelregister werden die einträge der ersten und zweiten ebene als gruppen markiert;
                die zweite ebene ohne gruppenlemma-->
                <xsl:when test="@propertytype='formulas'">
                    <xsl:for-each select="item">
                        <!-- sortierung wird übernommen -->
                        <!--<xsl:sort select="@sort"/>-->
                        <item log="24">
                            <xsl:attribute name="type">group</xsl:attribute>
                            <xsl:copy-of select="@*"/>
                            <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                           <xsl:copy-of select="sections"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

                            <xsl:for-each select="item">
                                <!-- sortierung wird übernommen -->
                                <!--<xsl:sort select="@sort"/>-->
                                <item log="25">
                                    <xsl:attribute name="groupname"><xsl:value-of select="lemma"/></xsl:attribute>
                                    <xsl:attribute name="type">group</xsl:attribute>


                                    <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                    <xsl:call-template name="subitem-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:for-each>
                        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                 </xsl:when>

                <!-- im textsortenregister werden keine gruppen markiert;
                die links auf artikelnummern werden auf inschriften weitergeführt
                und um angaben zur sprache und zur versform ergänzt -->
                <xsl:when test="@propertytype='texttypes'">
                    <xsl:for-each select="item">
                        <item log="26">
                            <xsl:copy-of select="@*"/>
                            <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:for-each select="sections">
                                <xsl:call-template name="links-textsorten"></xsl:call-template>
                                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:for-each>
                            <xsl:call-template name="crossRefs"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="subitem-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                </xsl:when>

                <!-- im initienregister werden die einträge der ersten ebene als gruppen (ohne gruppenlemma) markiert -->
                <xsl:when test="@propertytype='initials'">
                    <xsl:for-each select="item">
                        <xsl:sort select="@sort"/>
                        <item log="27">
                            <xsl:attribute name="groupname"><xsl:apply-templates select="lemma"/></xsl:attribute>
                            <xsl:attribute name="type">group</xsl:attribute>
                            <xsl:copy-of select="@*"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="subitem-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                </xsl:when>

                <!-- im quellenregister (zitate) werden die einträge der ersten und zweiten ebene
                    als gruppen (mit grupppenlemmata) markiert;-->
                <xsl:when test="@propertytype='sources'">
                    <xsl:for-each select="item">
                        <!--<xsl:sort select="lemma/@sortstring"/>-->
                      <item  log="28">
                        <xsl:if test="item">
                            <xsl:attribute name="type">group</xsl:attribute>
                        </xsl:if>
                          <xsl:copy-of select="@*"/>


                          <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                          <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                          <xsl:for-each select="sections">
                              <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                          </xsl:for-each>
                            <xsl:call-template name="crossRefs"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        <xsl:for-each select="item">
                            <!--<xsl:sort select="lemma/@sortstring"/>-->
                            <item  log="29">
                                <xsl:if test="lemma='lateinisch' or lemma='deutsch' or lemma='hebräisch' or lemma='griechisch' or lemma='dänisch'">
                                    <xsl:attribute name="type">group</xsl:attribute>
                                </xsl:if>
                                <xsl:copy-of select="@*"/>


                                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                <xsl:for-each select="sections">
                                    <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                </xsl:for-each>
                                <xsl:call-template name="crossRefs"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                                <xsl:call-template name="subitem-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </xsl:for-each>
                      </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                     </xsl:for-each>
                 </xsl:when>

                <!-- die übrigen register werden nur hierarchisiert -->
                <xsl:otherwise>
                    <xsl:for-each select="item[not(@ishidden='1')]">
                        <item logg="30">
                            <xsl:copy-of select="@*"/>
                            <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:copy-of select="name"/>
                            <xsl:for-each select="sections">
                                <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </xsl:for-each>
                            <xsl:call-template name="crossRefs"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="subitem-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                 </xsl:otherwise>
            </xsl:choose>

    </xsl:template>

    <xsl:template name="subitem-schleife">
      <xsl:for-each select="item[not(@ishidden='1')]">
          <item logr1="31">
              <xsl:copy-of select="@*"/>


              <xsl:apply-templates select="lemma"/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <xsl:for-each select="sections">
                  <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              </xsl:for-each>
              <xsl:call-template name="crossRefs"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
              <xsl:call-template name="subitem-schleife"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
          </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
      </xsl:for-each>
    </xsl:template>

    <xsl:template match="lemma[ancestor::index]">
        <!-- wenn lateinische oder Ordnungsnummern in geschweiften klammern vorkommen,
      werden sie zum korrekten sortieren in einem attribut numerisch aufgelöst -->
        <xsl:param name="string1"><xsl:value-of select="substring-before(.,'}')"/></xsl:param>
        <xsl:param name="string2"><xsl:value-of select="substring-after($string1,'{')"/></xsl:param>
        <xsl:param name="string3">
            <xsl:if test="$string2='I'">001</xsl:if>
            <xsl:if test="$string2='II'">002</xsl:if>
            <xsl:if test="$string2='III'">003</xsl:if>
            <xsl:if test="$string2='IV'">004</xsl:if>
            <xsl:if test="$string2='V'">005</xsl:if>
            <xsl:if test="$string2='VI'">006</xsl:if>
            <xsl:if test="$string2='VII'">007</xsl:if>
            <xsl:if test="$string2='VIII'">008</xsl:if>
            <xsl:if test="$string2='VIIII'">009</xsl:if>
            <xsl:if test="$string2='IX'">009</xsl:if>
            <xsl:if test="$string2='X'">010</xsl:if>
            <xsl:if test="$string2='XI'">011</xsl:if>
            <xsl:if test="$string2='XII'">012</xsl:if>
            <xsl:if test="$string2='XIII'">013</xsl:if>
            <xsl:if test="$string2='XIV'">014</xsl:if>
            <xsl:if test="$string2='XV'">015</xsl:if>
            <xsl:if test="$string2='XVI'">016</xsl:if>
            <xsl:if test="$string2='XVII'">017</xsl:if>
            <xsl:if test="$string2='XVIII'">018</xsl:if>
            <xsl:if test="$string2='XVIIII'">019</xsl:if>
            <xsl:if test="$string2='XIX'">019</xsl:if>
            <xsl:if test="$string2='XX'">020</xsl:if>
            <xsl:if test="$string2='XXI'">021</xsl:if>
            <xsl:if test="$string2='XXII'">022</xsl:if>
            <xsl:if test="$string2='XXIII'">023</xsl:if>
            <xsl:if test="$string2='XXIV'">024</xsl:if>
            <xsl:if test="$string2='XXV'">025</xsl:if>
            <xsl:if test="$string2='XXVI'">026</xsl:if>
            <xsl:if test="$string2='XXVII'">027</xsl:if>
            <xsl:if test="$string2='XXVIII'">028</xsl:if>
            <xsl:if test="$string2='XXVIIII'">029</xsl:if>
            <xsl:if test="$string2='XXIX'">029</xsl:if>
            <xsl:if test="$string2='XXX'">030</xsl:if>
            <xsl:if test="contains($string2,'1') or
                contains($string2,'2') or
                contains($string2,'3') or
                contains($string2,'4') or
                contains($string2,'5') or
                contains($string2,'6') or
                contains($string2,'7') or
                contains($string2,'8') or
                contains($string2,'9')"><xsl:number value="$string2" format="0001"></xsl:number></xsl:if>
        </xsl:param>
        <xsl:choose>
            <xsl:when test="contains(.,'{')">
                <lemma log="le1">
                    <xsl:copy-of select="@*"/>
                    <xsl:attribute name="sortstring"><xsl:value-of select="substring-before(.,'{')"/><xsl:value-of select="$string3"/><xsl:value-of select="substring-after(.,'}')"/></xsl:attribute>
                    <xsl:value-of select="translate(.,'{}','')"/>
                </lemma>
            </xsl:when>
            <xsl:when test="contains(.,'#')">
                <lemma log="le1">
                    <xsl:copy-of select="@*"/>
                    <xsl:attribute name="sortstring"><xsl:value-of select="substring-before(.,'#')"/></xsl:attribute>
                    <xsl:value-of select="substring-before(.,'#')"/>
                </lemma>
            </xsl:when>
            <xsl:otherwise>
                <lemma log="le2">
                    <xsl:copy-of select="@*"/>
                    <!--<xsl:attribute name="sortstring"><xsl:value-of select="."/></xsl:attribute>-->
                    <xsl:value-of select="."/>
                </lemma>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>



    <xsl:template match="name">
        <xsl:choose>
            <xsl:when test="ancestor::index[@propertytype='heraldry']">
                <lemma>
                    <xsl:copy-of select="@*"/>
                    <xsl:value-of select="."/>
                </lemma>
            </xsl:when>
            <xsl:otherwise>
                    <name log1="name">
                        <xsl:copy-of select="@*"/>
                        <xsl:value-of select="."/>
                    </name>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>


    <xsl:template name="register-wappen-1">
        <!-- dieses template wurde aufgerufen. wenn die register nicht aus dem band heraus angelegt werden
==> wird nicht mehr benötigt - kann irgendwann gelöscht werden
 -->

        <!-- das wappenregister wird in zwei parametern vorformatiert -->
        <xsl:param name="wappen1">
            <xsl:for-each select="item[substring(name,1,1)!='?']">
                <xsl:variable name="lemmax"><xsl:value-of select="substring-before(name,' {')"/></xsl:variable>
                <xsl:choose>
                    <!-- mit ordnungsnummern in geschweiften klammern versehen wappen desselben wappenträgers
                        werden als gruppe erfasst und im nächsten paramter neu nummeriert -->
                    <xsl:when test="contains(name,'{')">
                        <xsl:if test="not(preceding-sibling::item[substring-before(name,' {')=$lemmax])">
                            <item type="group" log1="32">
                                <xsl:for-each select="ancestor::index/item[name[substring-before(.,' {')=$lemmax]]">
                                    <item log1="33">
                                        <xsl:copy-of select="@*"/>
                                        <lemma>
<xsl:attribute name="sortchart"><xsl:value-of select="translate(substring(name,1,1),'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
<xsl:attribute name="sortstring"><xsl:value-of select="translate(name,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
                                            <xsl:value-of select="substring-before(name,' {')"/></lemma>
                                        <xsl:for-each select="sections">
                                            <xsl:copy-of select="."/>
                                        </xsl:for-each>
                                        <xsl:call-template name="crossRefs"></xsl:call-template>
                                    </item>
                                </xsl:for-each>
                            </item>
                        </xsl:if>
                    </xsl:when>
                    <xsl:otherwise>
                        <item log1="33a"><xsl:copy-of select="@*"/>
                            <xsl:for-each select="*">
                                <xsl:choose>
                                    <xsl:when test="self::name"><lemma><xsl:value-of select="name"/></lemma></xsl:when>
                                    <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
                                </xsl:choose>
                            </xsl:for-each>
<xsl:call-template name="crossRefs"></xsl:call-template>
                        </item>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:param>
        <xsl:param name="wappen2">
            <xsl:for-each select="$wappen1/*">
                <xsl:choose>
                    <!-- bei im vorigen parameter gruppierten wappen werden die ordungsnummern neu erzeugt -->
                    <xsl:when test="@type='group'">
                        <xsl:for-each select="*">
                            <item log1="34">
                                <xsl:attribute name="position"><xsl:value-of select="position()"/></xsl:attribute>
                                <xsl:copy-of select="@*"/>
                                <!-- die wappenbezeichnungen erhalten neue römische ziffern ensprechend ihrer position -->


                                <lemma>
                                    <xsl:copy-of select="lemma/@sortchart"/>
                                    <xsl:attribute name="sortstring"><xsl:value-of select="translate(lemma,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/><xsl:number value="position()" format="01"/></xsl:attribute>
                                    <xsl:value-of select="lemma"/><xsl:text> </xsl:text><xsl:number value="position()" format="I"/></lemma>
                                <xsl:for-each select="sections">
                                    <xsl:copy-of select="."/>
                                </xsl:for-each>
                                <xsl:call-template name="crossRefs"></xsl:call-template>
                            </item>
                        </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:param>

        <!-- wappenregister -->
        <index log="wapen2" propertytype="heraldry" titel="Wappen und Marken" name="wappen" nr="3" sort="6">
            <xsl:copy-of select="@*"/>
            <xsl:attribute name="als_register_ausgeben">1</xsl:attribute>
            <xsl:attribute name="initialgruppen">1</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="item[substring(name,1,1)!='?']">
                <xsl:sort select="name" order="ascending" lang="de"  case-order="upper-first"/>
                <xsl:variable name="lemmax"><xsl:value-of select="substring-before(name,' {')"/></xsl:variable>
                <item log1="36">
                    <xsl:copy-of select="@*"/>


                    <!-- hier die wappen-varianten neu nummerieren -->
                    <xsl:choose>
                        <xsl:when test="substring-before(name,' {')">
                            <xsl:choose>
                                <xsl:when test="following-sibling::item[substring-before(name,' {')=$lemmax] or preceding-sibling::item[substring-before(name,' {')=$lemmax]">
                                    <xsl:copy-of select="$wappen2//item[@id=current()/@id]/lemma"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <lemma>
<xsl:attribute name="sortchart"><xsl:value-of select="translate(substring(name,1,1),'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
<xsl:attribute name="sortstring"><xsl:value-of select="translate(name,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
                                        <xsl:value-of select="$lemmax"/>
                                    </lemma>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise><xsl:apply-templates select="name"/></xsl:otherwise>
                    </xsl:choose>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="sections">
                        <xsl:copy-of select="."/>
                    </xsl:for-each>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </item>
            </xsl:for-each>
            <item type="group" log1="37"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <lemma>nicht identifizierte Wappen</lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="item[substring(name,1,1)='?']/sections/section">
                        <xsl:copy-of select="."/>
                        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                </sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </index><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

        <!-- wappenbeschreibungen -->
        <index propertytype="blazons" initialgruppen="1" name="blasonierungen" titel="Wappenbeschreibungen" nr="3a" sort="7" als_register_ausgeben="1">
            <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="item[not(substring(name,1,1)='?')]">
                <xsl:sort select="name" data-type="text" lang="de" order="ascending" case-order="upper-first"/>
                <xsl:variable name="lemmax"><xsl:value-of select="substring-before(name,' {')"/></xsl:variable>
                <item log1="38">
                    <xsl:copy-of select="@*"/>


                    <xsl:choose>
                        <xsl:when test="substring-before(name,' {')">
                            <xsl:choose>
                                <xsl:when test="following-sibling::item[substring-before(name,' {')=$lemmax] or preceding-sibling::item[substring-before(name,' {')=$lemmax]">
                                    <xsl:copy-of select="$wappen2//item[@id=current()/@id]/lemma"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <lemma>
<xsl:attribute name="sortchart"><xsl:value-of select="translate(substring(name,1,1),'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
<xsl:attribute name="sortstring"><xsl:value-of select="translate(name,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
                                        <xsl:value-of select="$lemmax"/>
                                    </lemma>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise><xsl:apply-templates select="name"/></xsl:otherwise>
                    </xsl:choose><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <schild><xsl:apply-templates select="content" /></schild><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:if test="elements[node()]">
                        <oberwappen><xsl:apply-templates select="elements" /></oberwappen><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:if>
                    <xsl:if test="source_from[node()]">
                        <nachweis><xsl:apply-templates select="source_from" /></nachweis><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:if>
                </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
        </index><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
   </xsl:template>

    <xsl:template name="register-wappen-2">
        <!-- dieses template wurde aufgerufen wenn die register aus dem band heraus zusammengestellt werden
gilt jetzt auch für die artikelpipeline
-->

        <!-- das wappenregister wird in zwei parametern vorformatiert -->
        <xsl:param name="wappen1">
            <xsl:for-each select="item[substring(name,1,1)!='?']">
                <xsl:variable name="lemmax"><xsl:value-of select="substring-before(name,' {')"/></xsl:variable>
                <xsl:choose>
                    <!-- mit ordnungsnummern in geschweiften klammern versehen wappen desselben wappenträgers
                        werden als gruppe erfasst und im nächsten paramter neu nummeriert -->
                    <xsl:when test="contains(name,'{')">
                        <xsl:if test="not(preceding-sibling::item[substring-before(name,' {')=$lemmax])">
                            <item type="group" log1="39">
                                <xsl:for-each select="ancestor::index/item[name[substring-before(.,' {')=$lemmax]]">
                                    <item log1="40">
                                        <xsl:copy-of select="@*"/>
                                        <lemma>
<xsl:attribute name="sortchart"><xsl:value-of select="translate(substring(name,1,1),'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
<xsl:attribute name="sortstring"><xsl:value-of select="translate(name,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
                                            <xsl:value-of select="substring-before(name,' {')"/></lemma>
                                        <xsl:for-each select="sections">
                                            <xsl:copy-of select="."/>
                                        </xsl:for-each>
                                        <xsl:call-template name="crossRefs"></xsl:call-template>
                                    </item>
                                </xsl:for-each>
                            </item>
                        </xsl:if>
                    </xsl:when>
                    <xsl:otherwise>
                        <item log1="40a"><xsl:copy-of select="@*"/>
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
        <xsl:param name="wappen2">
            <xsl:for-each select="$wappen1/*">
                <xsl:choose>
                    <!-- bei im vorigen parameter gruppierten wappen werden die ordungsnummern neu erzeugt -->
                    <xsl:when test="@type='group'">
                        <xsl:for-each select="*">
                            <item log1="41">
                                <xsl:attribute name="position"><xsl:value-of select="position()"/></xsl:attribute>
                                <xsl:copy-of select="@*"/>
                                <!-- die wappenbezeichnungen erhalten neue römische ziffern ensprechend ihrer position -->
                                <lemma>
                                    <xsl:copy-of select="lemma/@sortchart"/>
                                    <xsl:attribute name="sortstring"><xsl:value-of select="translate(lemma,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/><xsl:number value="position()" format="01"/></xsl:attribute>
                                    <xsl:value-of select="lemma"/><xsl:text> </xsl:text><xsl:number value="position()" format="I"/></lemma>
                                <xsl:for-each select="sections">
                                    <xsl:copy-of select="."/>
                                </xsl:for-each>
                                <xsl:call-template name="crossRefs"></xsl:call-template>
                            </item>
                        </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise>
                        <item log1="41a"><xsl:copy-of select="@*"/>
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

        <!-- wappenregister -->
<!-- identfizierte wappen -->
<!--         <item type="group">-->
            <xsl:for-each select="item[substring(name,1,1)!='?']">
                <xsl:sort select="name" order="ascending" lang="de"  case-order="upper-first"/>
                <xsl:variable name="lemmax"><xsl:value-of select="substring-before(name,' {')"/></xsl:variable>
                <item log1="42">
                    <xsl:copy-of select="@*"/>
                    <!-- hier die wappen-varianten neu nummerieren -->
                    <xsl:choose>
                        <xsl:when test="substring-before(name,' {')">
                            <xsl:choose>
                                <xsl:when test="following-sibling::item[substring-before(name,' {')=$lemmax] or preceding-sibling::item[substring-before(name,' {')=$lemmax]">
                                 <xsl:copy-of select="$wappen2//item[@id=current()/@id]/lemma"/>
                                </xsl:when>
                                <xsl:otherwise>
                                  <lemma>
<xsl:attribute name="sortchart"><xsl:value-of select="translate(substring(name,1,1),'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
<xsl:attribute name="sortstring"><xsl:value-of select="translate(name,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>

                                    <xsl:value-of select="$lemmax"/>
                                </lemma>
                              </xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:for-each select="name">
                                  <lemma>
<xsl:attribute name="sortchart"><xsl:value-of select="translate(substring(.,1,1),'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
<xsl:attribute name="sortstring"><xsl:value-of select="translate(.,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
                                    <xsl:value-of select="."/>
                                </lemma>
                            </xsl:for-each>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="sections">
                        <xsl:copy-of select="."/>
                    </xsl:for-each>
                    <xsl:call-template name="crossRefs"></xsl:call-template>
                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                </item>
            </xsl:for-each>
        <!--</item>-->
<xsl:choose>
<xsl:when test="$unidentifizierte_wappen_beschreiben=1">
<!-- nicht identifizierte wappen mit angabe der blasonierung -->
        <item type="group" log1="43a"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <lemma>nicht identifizierte Wappen</lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <!-- zuerst diejenigen wappen, deren schild erkennbar ist -->
                <item type="group"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="item[substring(name,1,1)='?'][content[node()]]">
                        <xsl:sort select="content/@sortstring" order="ascending" lang="de"  case-order="upper-first"/>
                        <item><xsl:copy-of select="@*"/>
                            <xsl:for-each select="content">
                                <lemma><xsl:copy-of select="@*"/><xsl:apply-templates></xsl:apply-templates></lemma>
                            </xsl:for-each>
                            <xsl:for-each select="sections"><xsl:copy-of select="."/></xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            <xsl:call-template name="crossRefs"></xsl:call-template>
                       </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <!-- zum Schluss die wappen, bei denen nur die Helzier erkennbar ist  -->
        <item type="group" log1="43b"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                     <xsl:for-each select="item[substring(name,1,1)='?'][not(content[node()])]">
                        <xsl:sort select="elements/@sortstring" order="ascending" lang="de"  case-order="upper-first"/>
                        <item><xsl:copy-of select="@*"/>
                            <xsl:for-each select="elements">
                                <lemma><xsl:copy-of select="@*"/><xsl:apply-templates></xsl:apply-templates></lemma>
                            </xsl:for-each>
                            <xsl:for-each select="sections">
                                <xsl:copy-of select="."/>
                            </xsl:for-each>
                            <xsl:call-template name="crossRefs"></xsl:call-template>
                        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:when>
<xsl:otherwise>
<!-- nicht identifizierte wappen ohne angabe der blasonierung, es werden nur die Artikelnummern summarisch aufgelistet -->
            <item type="group" log1="43"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <lemma>nicht identifizierte Wappen</lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="item[substring(name,1,1)='?']/sections/section">
                        <xsl:copy-of select="."/>
                        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                </sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
</xsl:otherwise>
</xsl:choose>
    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:template>

    <xsl:template name="register-blasonierungen">
        <!-- dieses template wird vom template des blasonierungsregisters aufgerufen
datengrundlage ist das wappenregister
 -->

        <!-- zuerst wird das wappenregister  in zwei parametern vorformatiert -->
        <xsl:param name="wappen1">
            <xsl:for-each select="item[substring(name,1,1)!='?']">
                <xsl:variable name="lemmax"><xsl:value-of select="substring-before(name,' {')"/></xsl:variable>
                <xsl:choose>
                    <!-- mit ordnungsnummern in geschweiften klammern versehen wappen desselben wappenträgers
                        werden als gruppe erfasst und im nächsten paramter neu nummeriert -->
                    <xsl:when test="contains(name,'{')">
                        <xsl:if test="not(preceding-sibling::item[substring-before(name,' {')=$lemmax])">
                            <item type="group" log="44">
                                <xsl:for-each select="ancestor::index/item[name[substring-before(.,' {')=$lemmax]]">
                                    <item log="46">
                                        <xsl:copy-of select="@*"/>
                                        <lemma>
<xsl:attribute name="sortchart"><xsl:value-of select="translate(substring(name,1,1),'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
<xsl:attribute name="sortstring"><xsl:value-of select="translate(name,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
                                            <xsl:value-of select="substring-before(name,' {')"/>
                                        </lemma>
                                        <xsl:for-each select="sections">
                                            <xsl:copy-of select="."/>
                                        </xsl:for-each>
                                        <xsl:call-template name="crossRefs"></xsl:call-template>
                                    </item>
                                </xsl:for-each>
                            </item>
                        </xsl:if>
                    </xsl:when>
                    <xsl:otherwise>
                        <item log1="40aa"><xsl:copy-of select="@*"/>
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
        <xsl:param name="wappen2">
            <xsl:for-each select="$wappen1/*">
                <xsl:choose>
                    <!-- bei im vorigen parameter gruppierten wappen werden die ordungsnummern neu erzeugt -->
                    <xsl:when test="@type='group'">
                        <xsl:for-each select="*">
                            <item log="45">
                                <xsl:attribute name="position"><xsl:value-of select="position()"/></xsl:attribute>
                                <xsl:copy-of select="@*"/>
                                <!-- die wappenbezeichnungen erhalten neue römische ziffern ensprechend ihrer position -->
                                <lemma>
                                    <xsl:copy-of select="lemma/@sortchart"/>
                                    <xsl:attribute name="sortstring"><xsl:value-of select="translate(lemma,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/><xsl:number value="position()" format="01"/></xsl:attribute>
                                    <xsl:value-of select="lemma"/><xsl:text> </xsl:text><xsl:number value="position()" format="I"/>
                                </lemma>
                                <xsl:for-each select="sections">
                                    <xsl:copy-of select="."/>
                                </xsl:for-each>
                                <xsl:call-template name="crossRefs"></xsl:call-template><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                            </item>
                        </xsl:for-each>
                    </xsl:when>
                    <xsl:otherwise>
                        <item log1="40ab"><xsl:copy-of select="@*"/>
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

        <!-- wappenbeschreibungen -->
            <xsl:for-each select="item[not(substring(name,1,1)='?')]">
                <xsl:sort select="name" data-type="text" lang="de" order="ascending" case-order="upper-first"/>
                <xsl:variable name="lemmax"><xsl:value-of select="substring-before(name,' {')"/></xsl:variable>
                <item log="46a">
                    <xsl:copy-of select="@*"/>


                    <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:choose>
                        <xsl:when test="substring-before(name,' {')">
                            <xsl:choose>
                                <xsl:when test="following-sibling::item[substring-before(name,' {')=$lemmax] or preceding-sibling::item[substring-before(name,' {')=$lemmax]">
                                        <xsl:copy-of select="$wappen2//item[@id=current()/@id]/lemma"/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <lemma>
<xsl:attribute name="sortchart"><xsl:value-of select="translate(substring(name,1,1),'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
<xsl:attribute name="sortstring"><xsl:value-of select="translate(name,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
                                        <xsl:value-of select="$lemmax"/>
                                    </lemma></xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>

                        <xsl:otherwise>
                            <xsl:for-each select="name">
                                    <lemma>
                                        <xsl:attribute name="sortchart"><xsl:value-of select="translate(substring(.,1,1),'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
                                        <xsl:attribute name="sortstring"><xsl:value-of select="translate(.,'ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÜÚäöüÅ' , 'abcdefghijklmnopqrstuvwxyzaouuaoua')"/></xsl:attribute>
                                        <xsl:value-of select="."/>
                                    </lemma>
                            </xsl:for-each>
                        </xsl:otherwise>
                    </xsl:choose><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <schild><p><xsl:apply-templates select="content" /></p></schild><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:if test="elements[node()]">
                        <oberwappen><p><xsl:apply-templates select="elements" /></p></oberwappen><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:if>
                    <xsl:if test="source_from[node()]">
                        <nachweis><p><xsl:apply-templates select="source_from" /></p></nachweis><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:if>
                </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>
        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:template>


<!-- register marken -->
    <xsl:template name="register-marken">
        <!-- die nicht identifizierten marken in einem paramter sammeln -->
        <xsl:param name="marken_undefine">
            <xsl:for-each select="item[lemma[not(text())] or not(lemma)]">
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </xsl:param>
        <!-- die identifizierten marken auslesen -->
        <xsl:for-each select="item[lemma[text()]]">
            <xsl:sort select="lemma"  order="ascending" lang="de"  case-order="upper-first"/>
            <item log="47">
                <xsl:copy-of select="@*"/>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>

                <lemma>
                    <xsl:copy-of select="lemma/@*"/>
                    <xsl:value-of select="lemma"/><xsl:text> (M)</xsl:text>
                </lemma>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <xsl:for-each select="sections">
                    <xsl:copy-of select="."/>
                </xsl:for-each>
                <xsl:call-template name="crossRefs"></xsl:call-template>
                <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                 <!-- subeinträge -->
                <!--in hgw keine untereinträge und keine verweise-->
            </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>
 <!--
<parametertest_marken_undefine><xsl:copy-of select="$marken_undefine"/></parametertest_marken_undefine>
-->
        <!-- für die nichtidentifizierten marken zuerst aus dem register der markentypen
            die verschiedenen typen auslesen und je eine gruppe bilden-->
        <xsl:for-each select="ancestor::book/manage_lists/index[@propertytype='brandtypes']/item">
            <xsl:variable name="markentyp"><xsl:value-of select="lemma"/></xsl:variable>
            <item type="group" logr1="48" brandtype="{$markentyp}">
                <lemma>nicht identifizierte <xsl:value-of select="lemma"/></lemma><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                <!-- die links aus dem parameter der nicht identifizierten marken markentypisch auslesen-->
                <sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    <xsl:for-each select="$marken_undefine/item[@brandtype=$markentyp]//section">
                        <xsl:copy-of select="."/>
                        <xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
                    </xsl:for-each>
                </sections><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </item><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        </xsl:for-each>

    </xsl:template>

    <!-- für das register blasonierungen werden verweise auf literatur und marken kopiert-->
    <xsl:template match="rec_lit"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="rec_ma"><xsl:copy-of select="."/></xsl:template>
    <xsl:template match="rec_mz"><xsl:copy-of select="."/></xsl:template>

    <xsl:template name="register_datierung">

    </xsl:template>

<!-- querverweise in den registern sammeln-->
<xsl:template name="crossRefs">
<xsl:param name="p_basisstandort"></xsl:param>
    <xsl:if test="crossRef"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
        <crossRefs log1="cr1"><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            <xsl:for-each select="crossRef">
                 <xsl:sort lang="de" select="lemma/@sortstring" case-order="upper-first" order="ascending" data-type="text"/>
                 <xsl:variable name="p_crossRef_id"><xsl:value-of select="@id"/></xsl:variable>
<!-- es wird geprüft, ob das verweisziel referenzen auf artikelnummern (sections) oder untereinträge (item) enthält
anderenfalls wir der verweis eliminiert -->

                <xsl:if test="ancestor::index//item[@id=$p_crossRef_id][sections or item]">
                      <xsl:call-template name="crossRef"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
                </xsl:if>
           </xsl:for-each>

<!--             <xsl:for-each select="crossRef">
                <xsl:sort lang="de" select="lemma/@sortstring" case-order="upper-first" order="ascending" data-type="text"/>
                <xsl:copy-of select="."/><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
            </xsl:for-each>-->
        </crossRefs><xsl:text disable-output-escaping="yes">&#x000D;&#x000A;</xsl:text>
    </xsl:if>
</xsl:template>

<!-- verweisumkehr -->
<xsl:template name="crossRef">
<xsl:param name="p_basisstandort"></xsl:param>
<xsl:choose>
    <xsl:when test="crossRef">
        <xsl:for-each select=".//crossRef[not(crossRef)]">
            <crossRef log1="cr2"><xsl:copy-of select="@*"/>
                <xsl:copy-of select="lemma"/>
                <xsl:call-template name="verweisumkehr"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
            </crossRef>
        </xsl:for-each>
    </xsl:when>
    <xsl:otherwise><xsl:copy-of select="."/></xsl:otherwise>
</xsl:choose>
</xsl:template>

    <xsl:template name="verweisumkehr">
    <xsl:param name="p_basisstandort"></xsl:param>
        <xsl:for-each select="parent::crossRef">
                <crossRef log1="cr3"><xsl:copy-of select="@*"/>
                    <xsl:copy-of select="lemma"/>
                    <xsl:call-template name="verweisumkehr"><xsl:with-param name="p_basisstandort"><xsl:value-of select="$p_basisstandort"/></xsl:with-param></xsl:call-template>
                </crossRef>
        </xsl:for-each>
    </xsl:template>


</xsl:stylesheet>













