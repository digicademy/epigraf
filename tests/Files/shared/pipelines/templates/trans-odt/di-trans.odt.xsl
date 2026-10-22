<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
    xmlns:ooo="http://openoffice.org/2004/office"
    xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"
    xmlns:xlink="http://www.w3.org/1999/xlink"
    xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0"
    xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"
    xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
    xmlns:rpt="http://openoffice.org/2005/report"
    xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0"
    xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0"
    xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0"
    xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0"
    xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"
    xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"
    xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc"
    xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2"
    xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:tableooo="http://openoffice.org/2009/table"
    xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0"
    xmlns:drawooo="http://openoffice.org/2010/draw"
    xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0"
    xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0"
    xmlns:math="http://www.w3.org/1998/Math/MathML"
    xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0"
    xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0"
    xmlns:formx="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0"
    xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xhtml="http://www.w3.org/1999/xhtml"
    xmlns:grddl="http://www.w3.org/2003/g/data-view#" xmlns:css3t="http://www.w3.org/TR/css3-text/"
    xmlns:officeooo="http://openoffice.org/2009/office" office:version="1.3"
    office:mimetype="application/vnd.oasis.opendocument.text" exclude-result-prefixes="xs"
    version="2.0">

    <xsl:output method="xml" indent="yes"/>
    <xsl:strip-space elements="*"/>
    <xsl:preserve-space elements="t"/>


    <xsl:import href="../cil-commons/cil-switch.xsl"/>
    <xsl:import href="cil-odt-styles.xsl"/>
    <xsl:import href="cil-odt-settings.xsl"/>
    <xsl:import href="cil-odt-meta.xsl"/>
    <xsl:import href="cil-odt-annotations.xsl"/>

    <!--    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>-->


    <!--==KATALOG / Catalogue===============================================================-->

    <!--Hauptmuster / master pattern-->
    <xsl:template match="book">
        <office:document-content xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
            xmlns:ooo="http://openoffice.org/2004/office"
            xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"
            xmlns:xlink="http://www.w3.org/1999/xlink"
            xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0"
            xmlns:dc="http://purl.org/dc/elements/1.1/"
            xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0"
            xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"
            xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
            xmlns:rpt="http://openoffice.org/2005/report"
            xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0"
            xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0"
            xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0"
            xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0"
            xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"
            xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"
            xmlns:ooow="http://openoffice.org/2004/writer"
            xmlns:oooc="http://openoffice.org/2004/calc"
            xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2"
            xmlns:xforms="http://www.w3.org/2002/xforms"
            xmlns:tableooo="http://openoffice.org/2009/table"
            xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0"
            xmlns:drawooo="http://openoffice.org/2010/draw"
            xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0"
            xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0"
            xmlns:math="http://www.w3.org/1998/Math/MathML"
            xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0"
            xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0"
            xmlns:formx="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0"
            xmlns:dom="http://www.w3.org/2001/xml-events"
            xmlns:xsd="http://www.w3.org/2001/XMLSchema"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xmlns:xhtml="http://www.w3.org/1999/xhtml"
            xmlns:grddl="http://www.w3.org/2003/g/data-view#"
            xmlns:css3t="http://www.w3.org/TR/css3-text/"
            xmlns:officeooo="http://openoffice.org/2009/office" office:version="1.3"
            office:mimetype="application/vnd.oasis.opendocument.text">

<!--            <xsl:call-template name="meta"/>
            <xsl:call-template name="settings"/>-->
            <office:scripts>
                <office:script script:language="ooo:Basic">
                    <ooo:libraries xmlns:ooo="http://openoffice.org/2004/office"
                        xmlns:xlink="http://www.w3.org/1999/xlink"/>
                </office:script>
            </office:scripts>

<!--            <xsl:call-template name="font-face-decls"/>
            <xsl:call-template name="master-styles"/>-->
            <!--<xsl:call-template name="styles"/>-->
            <!--<xsl:call-template name="automatic-styles"></xsl:call-template>-->
            <!--<xsl:call-template name="master-styles"></xsl:call-template>-->

            <office:body>
                <office:text text:use-soft-page-breaks="true">

                    <xsl:apply-templates/>
                </office:text>
            </office:body>
        </office:document-content>



    </xsl:template>

    <xsl:template match="catalog">
        <xsl:comment>katalog anfang</xsl:comment>
        <text:section text:style-name="catalog" text:name="catalog">
            <xsl:apply-templates/>
        </text:section>
        <xsl:comment>katalog ende</xsl:comment>
    </xsl:template>

    <xsl:template match="article">
        <xsl:param name="p_article_name">
            <xsl:text>article</xsl:text>
            <xsl:number format="001"/>
        </xsl:param>
        <xsl:comment>artikel anfang</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <text:section text:style-name="article" text:name="{$p_article_name}">
            <xsl:apply-templates/>
            <xsl:if test="following-sibling::article">

                <text:p/>
            </xsl:if>
        </text:section>
        <xsl:comment>artikel ende</xsl:comment>
    </xsl:template>

    <xsl:template match="header">
        <xsl:comment>kopfzeile anfang</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <text:p text:style-name="cil-kopfzeile-balken-oben"/>
        <text:p text:style-name="cil-kopfzeile">
            <text:span>
                <xsl:number count="article" from="catalog"/>
                <xsl:text> </xsl:text>
                <text:s text:c="4"/>
            </text:span>
            <text:span>
                <text:tab/>
            </text:span>
            <text:span>
                <xsl:value-of select="title"/>
            </text:span>
            <text:span>
                <text:tab/>
            </text:span>
            <text:span>
                <xsl:for-each select="dating/item[not(text() = preceding-sibling::item/text())]">
                    <xsl:value-of select="."/>
                    <xsl:if
                        test="following-sibling::item[not(text() = preceding-sibling::item/text())]">
                        <xsl:text>, </xsl:text>
                    </xsl:if>
                </xsl:for-each>
            </text:span>
        </text:p>
        <text:p text:style-name="cil-kopfzeile-balken-unten"/>
        <xsl:choose>
            <xsl:when test="$v_signature = 1">
                <text:p text:style-name="cil-signature">
                    <xsl:value-of select="signature"/>
                </text:p>
            </xsl:when>
            <xsl:otherwise>
                <text:p text:style-name="cil-signature-hidden">
                    <xsl:value-of select="signature"/>
                </text:p>
            </xsl:otherwise>
        </xsl:choose>
        <!--        <xsl:if test="$v_signature = 1">
            <text:p text:style-name="cil-signature">
                <xsl:value-of select="signature"/>
            </text:p>
        </xsl:if>-->
        <xsl:comment>kopfzeile ende</xsl:comment>
    </xsl:template>

    <xsl:template match="editions_main">
        <xsl:comment>anfang leiteditionen</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende leiteditionen</xsl:comment>
    </xsl:template>

    <xsl:template match="editions_first">
        <xsl:comment>anfang ersteditionen</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende ersteditionen</xsl:comment>
    </xsl:template>

    <xsl:template match="locations_first">
        <xsl:comment>anfang fundort</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende fundort</xsl:comment>
    </xsl:template>

    <xsl:template match="locations_last">
        <xsl:comment>anfang standort</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende standort</xsl:comment>
    </xsl:template>

    <xsl:template match="dating">
        <xsl:comment>anfang datierung</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende datierung</xsl:comment>
    </xsl:template>

    <xsl:template match="lecturebase">
        <xsl:comment>anfang ediert nach</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende ediert nach</xsl:comment>
    </xsl:template>

    <xsl:template match="description">
        <xsl:comment>anfang beschreibung</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="textfield">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende beschreibung</xsl:comment>
    </xsl:template>

    <xsl:template match="measures">
        <xsl:comment>anfang objektmaße</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende objektmaße</xsl:comment>
    </xsl:template>

    <xsl:template match="materials">
        <xsl:comment>anfang materieal</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende material</xsl:comment>
    </xsl:template>

    <xsl:template match="form_inscriptionarea">
        <xsl:comment>anfang schriftfeld form</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende schriftfeld form</xsl:comment>
    </xsl:template>

    <xsl:template match="measures_inscriptionarea">
        <xsl:comment>anfang schriftfeld maße</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende schriftfeld maße</xsl:comment>
    </xsl:template>

    <xsl:template match="fontsizes">
        <xsl:comment>anfang schrifthöhe</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende schrifthöhe</xsl:comment>
    </xsl:template>

    <xsl:template match="linedistances">
        <xsl:comment>anfang zeilenabstand</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende zeilenabstand</xsl:comment>
    </xsl:template>

    <xsl:template match="images">
        <xsl:comment>anfang abbildungen</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende abbildungen</xsl:comment>
    </xsl:template>

    <xsl:template match="inscriptions">
        <xsl:comment>anfang inschriften</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="inscriptions">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende inschriften</xsl:comment>
    </xsl:template>

    <xsl:template match="inscriptions_comments">
        <xsl:comment>anfang textkritischer apparat</xsl:comment>
        <!-- abschnittsüberschrift einfügen -->
        <text:p text:style-name="cil-caption">
            <xsl:value-of select="@caption"/>
        </text:p>
        <!-- tabelle in zwei spalten, jede zeile für eine anmerkung  -->
        <table:table table:name="cil-comments" table:style-name="Tabelle2">
            <table:table-column table:style-name="Tabelle2.A"/>
            <table:table-column table:style-name="Tabelle2.B"/>
            <xsl:for-each select="items/item">
                <table:table-row>
                    <!-- linke spalte bezeichner der inschrift -->
                    <table:table-cell table:style-name="Tabelle2.A1" office:value-type="string">
                        <text:p text:style-name="cil-normal-2">
                            <xsl:if
                                test="
                                    following-sibling::item[not(inscription/@name = current()/inscription/@name)]
                                    or
                                    preceding-sibling::item[not(inscription/@name = current()/inscription/@name)]
                                    ">
                                <xsl:value-of select="inscription/@name"/>
                            </xsl:if>
                        </text:p>
                    </table:table-cell>
                    <!-- rechte spalte inhalt der anmerkung -->
                    <table:table-cell table:style-name="Tabelle2.A2" office:value-type="string">
                        <text:p text:style-name="cil-normal-2">
                            <!-- zeilennummern ausgeben -->
                            <xsl:if test="lines/line/node()">
                                <xsl:text>Z. </xsl:text>
                                <xsl:for-each select="lines/line">
                                    <xsl:value-of select="."/>
                                    <xsl:if test="following-sibling::line">-</xsl:if>
                                </xsl:for-each>
                                <xsl:text>: </xsl:text>
                            </xsl:if>
                            <!-- wenn vorhanden subtext ansteuern -->
                            <xsl:if test="segment">
                                <xsl:choose>
                                    <!-- rasur als subtext unter einem ersatztext -->
                                    <xsl:when test="@type = 'cil-add-plov'">
                                        <text:span text:style-name="cil-app-segment">
                                            <xsl:text> Rasur:</xsl:text>
                                        </text:span>
                                        <text:span>
                                            <xsl:text> ¦ </xsl:text>
                                            <xsl:apply-templates select="segment/*"/>
                                            <xsl:text> ¦ ‒ </xsl:text>
                                        </text:span>
                                    </xsl:when>
                                    <!-- fehlerhafter befund als subtext unter einer korrektur -->
                                    <xsl:when test="@type = 'cil-choice'">
                                        <text:span text:style-name="cil-app-segment">
                                            <xsl:text> Befund:</xsl:text>
                                        </text:span>
                                        <text:span>
                                            <xsl:text> ¦ </xsl:text>
                                            <xsl:apply-templates select="segment/*"/>
                                            <xsl:if test="comment">
                                                <xsl:text> ¦ ‒ </xsl:text>
                                            </xsl:if>
                                        </text:span>
                                    </xsl:when>
                                </xsl:choose>
                            </xsl:if>
                            <!-- anmerkung auslesen -->
                            <xsl:apply-templates select="comment/*"/>
                        </text:p>
                    </table:table-cell>
                </table:table-row>
            </xsl:for-each>
        </table:table>
        <xsl:comment>ende textkritischer apparat</xsl:comment>
    </xsl:template>

    <xsl:template match="inscriptions_variants">
        <xsl:comment>anfang textkritischer apparat</xsl:comment>
        <text:p text:style-name="cil-caption">
            <xsl:value-of select="@caption"/>
        </text:p>

        <table:table table:name="cil-variants" table:style-name="Tabelle2">
            <table:table-column table:style-name="Tabelle2.A"/>
            <table:table-column table:style-name="Tabelle2.B"/>
            <xsl:for-each select="items">
                <xsl:for-each select="item">
                    <table:table-row>
                        <table:table-cell table:style-name="Tabelle2.A1" office:value-type="string">
                            <text:p text:style-name="cil-normal-2">
                                <xsl:if
                                    test="
                                        following-sibling::item[not(inscription/@name = current()/inscription/@name)]
                                        or
                                        preceding-sibling::item[not(inscription/@name = current()/inscription/@name)]
                                        ">
                                    <xsl:value-of select="inscription/@name"/>
                                </xsl:if>
                            </text:p>
                        </table:table-cell>
                        <table:table-cell table:style-name="Tabelle2.A2" office:value-type="string">
                            <text:p text:style-name="cil-normal-2">
                                <xsl:if test="lines/line/node()">
                                    <xsl:text>Z. </xsl:text>
                                    <xsl:for-each select="lines/line">
                                        <xsl:value-of select="."/>
                                        <xsl:if test="following-sibling::line">-</xsl:if>
                                    </xsl:for-each>
                                    <xsl:text>: </xsl:text>
                                </xsl:if>
                                <text:span text:style-name="cil-zitat">
                                    <xsl:apply-templates select="content/*"/>
                                </text:span>
                                <xsl:if test="content/node()">
                                    <xsl:text> ‒ </xsl:text>
                                </xsl:if>
                                <xsl:apply-templates select="comment/*"/>
                            </text:p>
                        </table:table-cell>
                    </table:table-row>
                </xsl:for-each>
            </xsl:for-each>
        </table:table>
        <xsl:comment>ende textkritischer apparat</xsl:comment>
    </xsl:template>

    <xsl:template match="comment">
        <xsl:comment>anfang kommentar</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="textfield">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende kommentar</xsl:comment>
    </xsl:template>

    <xsl:template match="databases">
        <xsl:comment>anfang datenbanken</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende datenbanken</xsl:comment>
    </xsl:template>

    <xsl:template match="editions_others">
        <xsl:comment>anfang andere editionen</xsl:comment>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="sec_items">
            <xsl:with-param name="p_parent_name">
                <xsl:value-of select="name()"/>
            </xsl:with-param>
        </xsl:call-template>
        <xsl:comment>ende andere editionen</xsl:comment>
    </xsl:template>

    <xsl:template match="notes">
        <xsl:comment>anfang anmerkungen</xsl:comment>
        <text:p text:style-name="cil-caption">
            <xsl:value-of select="@caption"/>
        </text:p>
        <table:table table:name="cil-notes" table:style-name="Tabelle2">
            <table:table-column table:style-name="Tabelle2.A"/>
            <table:table-column table:style-name="Tabelle2.B"/>
            <xsl:for-each select="group[@groupname = 'cil-app-things']">
                <xsl:for-each select="item">
                    <table:table-row>
                        <table:table-cell table:style-name="Tabelle2.A1" office:value-type="string">
                            <text:p text:style-name="cil-normal-2">
                                <xsl:number count="item" from="group"/>
                                <xsl:text>)</xsl:text>
                            </text:p>
                        </table:table-cell>
                        <table:table-cell table:style-name="Tabelle2.A2" office:value-type="string">
                            <text:p text:style-name="cil-normal-2">
                                <xsl:apply-templates select="footnote/content"/>
                            </text:p>
                        </table:table-cell>
                    </table:table-row>
                </xsl:for-each>
            </xsl:for-each>
        </table:table>
        <xsl:comment>ende anmerkungen</xsl:comment>
    </xsl:template>

    <xsl:template name="sec_items">
        <xsl:param name="p_parent_name"/>
        <text:p text:style-name="cil-caption">
            <xsl:value-of select="@caption"/>
        </text:p>
        <text:p text:style-name="cil-section">
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:call-template name="sec_item">
                <xsl:with-param name="p_parent_name">
                    <xsl:value-of select="$p_parent_name"/>
                </xsl:with-param>
            </xsl:call-template>
        </text:p>
    </xsl:template>

    <xsl:template name="sec_item">
        <xsl:param name="p_parent_name"/>
        <xsl:choose>
            <xsl:when test="$p_parent_name = 'measures'">
                <text:span>
                    <xsl:for-each select="item">
                        <xsl:value-of select="lemma"/>
                        <xsl:if test="lemma-facet">
                            <xsl:text> (</xsl:text>
                            <xsl:value-of select="lemma-facet"/>
                            <xsl:text>)</xsl:text>
                        </xsl:if>
                        <xsl:text> </xsl:text>
                        <xsl:value-of select="value"/>
                        <xsl:text> </xsl:text>
                        <xsl:value-of select="unit"/>
                        <xsl:if test="following-sibling::item">
                            <xsl:text>, </xsl:text>
                        </xsl:if>
                    </xsl:for-each>
                    <xsl:text>.</xsl:text>
                </text:span>
            </xsl:when>

            <xsl:when test="$p_parent_name = 'measures_inscriptionarea'">
                <!-- bei mehreren inschriften werden die maße derselben inschrift jeweils nacheinander ausgelesen -->
                <xsl:for-each select="item">
                    <xsl:if test="item">
                        <!-- der bezeichner der inschrift wird nur ausgegeben, wenn es mehrere inschriften gibt-->
                        <xsl:if
                            test="
                                preceding-sibling::item or
                                following-sibling::item
                                ">
                            <xsl:value-of select="inscription/@name"/>
                            <xsl:text>: </xsl:text>
                        </xsl:if>
                        <xsl:for-each select="item">
                            <xsl:value-of select="lemma"/>
                            <xsl:if test="lemma-facet">
                                <xsl:text> (</xsl:text>
                                <xsl:value-of select="lemma-facet"/>
                                <xsl:text>)</xsl:text>
                            </xsl:if>
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="value"/>
                            <xsl:text> </xsl:text>
                            <xsl:value-of select="unit"/>
                            <xsl:if test="following-sibling::item">
                                <xsl:text>, </xsl:text>
                            </xsl:if>
                        </xsl:for-each>
                    </xsl:if>
                </xsl:for-each>
                <xsl:text>.</xsl:text>
                <xsl:if test="following-sibling::item[item]">
                    <text:line-break/>
                </xsl:if>
            </xsl:when>

            <xsl:when test="$p_parent_name = 'form_inscriptionarea'">
                <xsl:variable name="v_count_inscriptions">
                    <xsl:value-of
                        select="count(item[not(inscription/@name = preceding-sibling::item/inscription/@name)])"
                    />
                </xsl:variable>
                <xsl:for-each select="item[content]">
                    <xsl:if test="$v_count_inscriptions != 1">
                        <xsl:value-of select="inscription/@name"/>
                    </xsl:if>
                    <xsl:apply-templates/>
                </xsl:for-each>
            </xsl:when>

            <xsl:when test="$p_parent_name = 'dating'">
                <xsl:for-each select="item">
                    <xsl:value-of select="date"/>
                    <xsl:if test="inscription">
                        <xsl:text> (</xsl:text>
                        <xsl:value-of select="inscription/@name"/>
                        <xsl:text>)</xsl:text>
                    </xsl:if>
                    <xsl:if test="following-sibling::item">
                        <xsl:text>, </xsl:text>
                    </xsl:if>
                </xsl:for-each>
                <xsl:text>.</xsl:text>
            </xsl:when>

            <xsl:when test="$p_parent_name = 'lecturebase'">
                <xsl:for-each select="item[not(source_from = preceding-sibling::item/source_from)]">
                    <xsl:value-of select="source_from"/>
                    <!-- wenn es mehrere inschriften gibt, wird die nummer der inschrift zur lecturebase (element source_from) mit ausgegeben,
aber nur für den fall, dass die lecturebase jeweils verschieden ist;
beruhen alle auf derselben lecturebase (element source_from), unterbleibt die angeba der inschriftnummern
-->
                    <xsl:if test="following-sibling::item[source_from = current()/source_from]">
                        <xsl:if
                            test="
                                preceding-sibling::item[not(source_from = current()/source_from)] or
                                following-sibling::item[not(source_from = current()/source_from)]
                                ">
                            <xsl:text> (</xsl:text>
                            <xsl:value-of select="inscription/@name"/>
                            <xsl:text>, </xsl:text>
                            <xsl:for-each
                                select="following-sibling::item[source_from = current()/source_from]">
                                <xsl:value-of select="inscription/@name"/>
                            </xsl:for-each>
                            <xsl:text>)</xsl:text>
                        </xsl:if>
                    </xsl:if>
                </xsl:for-each>
                <xsl:if test="item">
                    <xsl:text>.</xsl:text>
                </xsl:if>
            </xsl:when>

            <xsl:when test="$p_parent_name = 'fontsizes' or $p_parent_name = 'linedistances'">
                <!-- es werden nur die items-elemente aufgesucht, 
        (1) von dessen item-element mindestens einer text enthält oder
        (2) die group-elemente enthalten
-->
                <xsl:for-each select="items[item[text()] or group]">
                    <text:span>
                        <!-- wenn es mehrere items-elemente gibt, wird der bezeichner der betreffenden inschrift ausgelesen -->
                        <xsl:if test="@inscript_number">
                            <xsl:value-of select="@inscript_number"/>
                            <xsl:text>: </xsl:text>
                        </xsl:if>
                        <xsl:choose>
                            <!-- wenn die zeilen in gruppen aufgeteilt sind -->
                            <xsl:when test="group">
                                <xsl:for-each select="group">
                                    <xsl:for-each select="item">
                                        <!-- enthält des item keinen text, wird ein bindestrich ausgegeben -->
                                        <xsl:value-of select="."/>
                                        <xsl:if test="not(text())">
                                            <xsl:text>-</xsl:text>
                                        </xsl:if>
                                        <!-- die einzelangaben werden durch pipes geetrennt -->
                                        <xsl:if test="following-sibling::item">
                                            <xsl:text> | </xsl:text>
                                        </xsl:if>
                                    </xsl:for-each>
                                    <!-- den gruppenbezeichner nach der gruppe in klammern ausgeben -->
                                    <xsl:text> (</xsl:text>
                                    <xsl:value-of select="@group-name"/>
                                    <xsl:text>)</xsl:text>
                                    <!-- wenn ein weiteres group-element folgt, wird  ein komma gesetzt-->
                                    <xsl:if test="following-sibling::group">
                                        <xsl:text>; </xsl:text>
                                    </xsl:if>
                                </xsl:for-each>
                            </xsl:when>
                            <!-- wenn die zeilen nicht in gruppen aufgeteilt sind -->
                            <xsl:otherwise>
                                <xsl:for-each select="item">
                                    <xsl:value-of select="."/>
                                    <xsl:if test="not(text())">
                                        <xsl:text>-</xsl:text>
                                    </xsl:if>
                                    <xsl:if test="following-sibling::item">
                                        <xsl:text> | </xsl:text>
                                    </xsl:if>
                                </xsl:for-each>
                            </xsl:otherwise>
                        </xsl:choose>
                    </text:span>
                    <xsl:if test="following-sibling::items[item[text()] or group]">
                        <text:line-break/>
                    </xsl:if>
                </xsl:for-each>
            </xsl:when>

            <xsl:when test="$p_parent_name = 'images'">
                <xsl:for-each select="items[item]">
                    <text:span>
                        <xsl:for-each select="item">
                            <xsl:if test="parent::*[@type = 'owns']">
                                <xsl:if test="following-sibling::item or preceding-sibling::item"/>
                                <xsl:value-of select="@sortno"/>
                                <xsl:text>. </xsl:text>
                            </xsl:if>
                            <xsl:apply-templates select="content"/>
                            <xsl:if test="following-sibling::item">
                                <xsl:text>, </xsl:text>
                            </xsl:if>
                        </xsl:for-each>
                        <xsl:if test="following-sibling::items">
                            <xsl:text>.</xsl:text>
                            <text:line-break/>
                        </xsl:if>
                    </text:span>
                </xsl:for-each>
            </xsl:when>

            <xsl:otherwise>
                <text:span>
                    <xsl:for-each select="item">
                        <xsl:value-of select="name"/>
                        <xsl:if test="content">
                            <xsl:text> </xsl:text>
                        </xsl:if>
                        <xsl:apply-templates select="content"/>
                        <xsl:if test="following-sibling::item">
                            <xsl:text>; </xsl:text>
                        </xsl:if>
                    </xsl:for-each>
                    <xsl:if test="item">
                        <xsl:text>.</xsl:text>
                    </xsl:if>
                </text:span>
            </xsl:otherwise>
        </xsl:choose>

        <!--<xsl:for-each select="item">
<xsl:value-of select="name"/>
<xsl:if test="content"><xsl:text> </xsl:text></xsl:if>
<xsl:apply-templates select="content"></xsl:apply-templates>
<xsl:if test="following-sibling::item">
<xsl:text>; </xsl:text></xsl:if>
</xsl:for-each>
<xsl:if test="item"><xsl:text>.</xsl:text></xsl:if>-->
    </xsl:template>



    <xsl:template match="project"/>
    <xsl:template match="indices"/>

    <xsl:template name="textfield">
        <xsl:param name="p_parent_name"/>
        <text:p text:style-name="cil-caption">
            <xsl:value-of select="@caption"/>
        </text:p>
        <text:p text:style-name="cil-section">
            <xsl:for-each select="content">
                <xsl:apply-templates/>
            </xsl:for-each>
        </text:p>

    </xsl:template>

    <!-- inschriften -->
    <xsl:template name="inscriptions">
        <xsl:param name="p_parent_name"/>
        <text:p text:style-name="cil-caption">
            <xsl:value-of select="@caption"/>
        </text:p>
        <xsl:for-each select="item">
            <table:table table:name="cil-inscript" table:style-name="Tabelle1">
                <table:table-column table:style-name="Tabelle1.A"/>
                <table:table-column table:style-name="Tabelle1.B"/>
                <table:table-row>
                    <table:table-cell table:style-name="Tabelle1.A1" office:value-type="string">
                        <text:p text:style-name="cil-transcription">
                            <xsl:if test="preceding-sibling::item or following-sibling::item">
                                <xsl:value-of select="inscription/@name"/>
                            </xsl:if>
                        </text:p>
                    </table:table-cell>
                    <table:table-cell table:style-name="Tabelle1.A2" office:value-type="string">
                        <text:p text:style-name="cil-transcription">
                            <xsl:apply-templates select="content"/>
                        </text:p>
                    </table:table-cell>
                </table:table-row>
            </table:table>
        </xsl:for-each>
    </xsl:template>


</xsl:stylesheet>
