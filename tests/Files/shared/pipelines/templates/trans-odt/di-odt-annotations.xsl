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

    <xsl:import href="../cil-commons/cil-switch.xsl"/>

    <xsl:template match="t">
        <xsl:value-of select="." xml:space="preserve"/>
    </xsl:template>

    <xsl:template match="nl">
        <xsl:if test="not(ancestor::inscriptions)">
            <text:line-break/>
        </xsl:if>
    </xsl:template>

    <!-- verweise -->
    <xsl:template match="cil-a">
        <text:a text:style-name="Hyperlink">
            <xsl:attribute name="xlink:href">
                <xsl:value-of select="@href"/>
            </xsl:attribute>
            <xsl:value-of select="@value"/>
        </text:a>
    </xsl:template>

    <xsl:template match="cil-rec_lit">
        <text:span text:style-name="">
            <xsl:value-of select="@data-link-value"/>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-rec_archiv">
        <text:span text:style-name="">
            <xsl:value-of select="@data-link-value"/>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-rec_db">
        <text:span text:style-name="">
            <xsl:value-of select="@data-link-value"/>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-rec_intern">
        <text:span text:style-name="">
            <xsl:value-of select="@data-link-value"/>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-rec_extern">
        <text:span text:style-name="">
            <xsl:value-of select="@data-link-value"/>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-link-sequnc">
        <text:span text:style-name="">
            <xsl:apply-templates/>
        </text:span>
    </xsl:template>

    <!-- office textauszeichnungen -->
    <xsl:template match="cil-undrln">
        <text:span text:style-name="cil-underline">
            <xsl:apply-templates/>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-term">
        <text:span text:style-name="cil-term">
            <xsl:apply-templates/>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-quot">
        <text:span text:style-name="cil-zitat">
            <xsl:apply-templates/>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-bold">
        <text:span text:style-name="cil-bold">
            <xsl:apply-templates/>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-italic">
        <text:span text:style-name="cil-italic">
            <xsl:apply-templates/>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-sup">
        <text:span text:style-name="cil-sup">
            <xsl:apply-templates/>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-extdt">
        <text:span text:style-name="cil-extdt">
            <xsl:apply-templates/>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-smallcaps">
        <text:span text:style-name="cil-smallcaps">
            <xsl:apply-templates/>
        </text:span>
    </xsl:template>

    <!-- überschriften -->
    <xsl:template match="cil-h0">
        <xsl:text disable-output-escaping="yes">&lt;/text:p&gt;</xsl:text>
        <xsl:text disable-output-escaping="yes">&lt;text:p text:style-name="cil-h0"&gt;</xsl:text>
        <xsl:apply-templates/>
        <xsl:text disable-output-escaping="yes">&lt;/text:p&gt;</xsl:text>
        <xsl:text disable-output-escaping="yes">&lt;text:p text:style-name="cil-section"&gt;</xsl:text>
    </xsl:template>

    <xsl:template match="cil-h1">
        <xsl:text disable-output-escaping="yes">&lt;/text:p&gt;</xsl:text>
        <xsl:text disable-output-escaping="yes">&lt;text:p text:style-name="cil-h1"&gt;</xsl:text>
        <text:span text:style-name="cil-h1">
            <xsl:apply-templates/>
        </text:span>
        <xsl:text disable-output-escaping="yes">&lt;/text:p&gt;</xsl:text>
        <xsl:text disable-output-escaping="yes">&lt;text:p text:style-name="cil-section"&gt;</xsl:text>
    </xsl:template>

    <xsl:template match="cil-h2">
        <xsl:text disable-output-escaping="yes">&lt;/text:p&gt;</xsl:text>
        <xsl:text disable-output-escaping="yes">&lt;text:p text:style-name="cil-h2"&gt;</xsl:text>
        <text:span text:style-name="cil-h02">
            <xsl:apply-templates/>
        </text:span>
        <xsl:text disable-output-escaping="yes">&lt;/text:p&gt;</xsl:text>
        <xsl:text disable-output-escaping="yes">&lt;text:p text:style-name="cil-section"&gt;</xsl:text>
    </xsl:template>

    <xsl:template match="cil-h3">
        <xsl:text disable-output-escaping="yes">&lt;/text:p&gt;</xsl:text>
        <xsl:text disable-output-escaping="yes">&lt;text:p text:style-name="cil-h3"&gt;</xsl:text>
        <text:span text:style-name="cil-h3">
            <xsl:apply-templates/>
        </text:span>
        <xsl:text disable-output-escaping="yes">&lt;/text:p&gt;</xsl:text>
        <xsl:text disable-output-escaping="yes">&lt;text:p text:style-name="cil-section"&gt;</xsl:text>
    </xsl:template>

    <xsl:template match="cil-h4">
        <xsl:text disable-output-escaping="yes">&lt;/text:p&gt;</xsl:text>
        <xsl:text disable-output-escaping="yes">&lt;text:p text:style-name="cil-h4"&gt;</xsl:text>
        <text:span text:style-name="cil-h3">
            <xsl:apply-templates/>
        </text:span>
        <xsl:text disable-output-escaping="yes">&lt;/text:p&gt;</xsl:text>
        <xsl:text disable-output-escaping="yes">&lt;text:p text:style-name="cil-section"&gt;</xsl:text>
    </xsl:template>

    <xsl:template match="cil-h5">
        <xsl:text disable-output-escaping="yes">&lt;/text:p&gt;</xsl:text>
        <xsl:text disable-output-escaping="yes">&lt;text:p text:style-name="cil-h5"&gt;</xsl:text>
        <text:span text:style-name="cil-h0">
            <xsl:apply-templates/>
        </text:span>
        <xsl:text disable-output-escaping="yes">&lt;/text:p&gt;</xsl:text>
        <xsl:text disable-output-escaping="yes">&lt;text:p text:style-name="cil-section"&gt;</xsl:text>
    </xsl:template>

<!-- anmerkungsapparate -->
    <xsl:template match="cil-app-things">
        <text:span text:style-name="cil-footnotesign">
            <xsl:number count="cil-app-things" from="article"/>
            <xsl:text>)</xsl:text>
        </text:span>
    </xsl:template>

    <xsl:template match="cil-app-text">
        <xsl:apply-templates/>
    </xsl:template>
    
    <xsl:template match="cil-app-variants">
        <xsl:apply-templates/>
    </xsl:template>

    <!-- verweisfolge -->
    <xsl:template match="cil-lnk-sequnc">
        <xsl:apply-templates/>
    </xsl:template>

<!-- ANNOTATIONEN in der edition -->

<!-- abkürzung -->
    <xsl:template match="cil-abbr">
        <xsl:text>(- - -)</xsl:text>
    </xsl:template>

    <xsl:template match="cil-add">
        <xsl:text>`</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>´</xsl:text>
    </xsl:template>
    <xsl:template match="cil-add-po">
        <xsl:text>«</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>»</xsl:text>
    </xsl:template>
    <xsl:template match="cil-area">
        <xsl:if test="preceding-sibling::cil-area">
            <xsl:text> || </xsl:text>
        </xsl:if>
        <xsl:apply-templates/>
    </xsl:template>
    <xsl:template match="cil-del">
        <xsl:text>[[</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>]]</xsl:text>
    </xsl:template>

<!-- abkürzungen -->
    <xsl:template match="cil-ex-a"> <!-- Abkürzung auflösen (Ergänzung markieren) -->
        <xsl:param name="p_parent"></xsl:param>
        <xsl:text>(</xsl:text>
        <xsl:apply-templates/>
        <!-- wenn die auflösung in einer ligatur erfolgt -->
        <xsl:if test="$p_parent='lig'"><xsl:text>&#x0361;</xsl:text></xsl:if>
        <xsl:text>)</xsl:text>
    </xsl:template>
    <xsl:template match="cil-ex-b"> <!-- separat stehendes Abkürzungssymbol sicher auflösen -->
        <xsl:text>(</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>)</xsl:text>
    </xsl:template>
    <xsl:template match="cil-ex-c"> <!-- unsichere Auflösung markieren -->
        <xsl:text>(</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>?)</xsl:text>
    </xsl:template>
    <xsl:template match="cil-ex-d"> <!-- separat stehendes Abkürzungssymbol unsicher auflösen -->
        <xsl:text>(</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>?)</xsl:text>
    </xsl:template>
    <xsl:template match="cil-ex-e"> <!-- unvollständige Auflösung markieren -->
        <xsl:text>(</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>-)</xsl:text>
    </xsl:template>


    <xsl:template match="cil-expan">
        <xsl:apply-templates/>
    </xsl:template>
    <xsl:template match="cil-fragment">
        <xsl:text> || </xsl:text>
        <xsl:apply-templates/>
    </xsl:template>
    <xsl:template match="cil-g-ref">
        <!-- grafische Symbole -->
        <!-- liegende u-klammern -->
        <xsl:text>⸦</xsl:text>
        <xsl:value-of select="@data-link-value"/>
        <xsl:text>⸧</xsl:text>
    </xsl:template>
    <xsl:template match="cil-gap-a">
        <xsl:text>[-]</xsl:text>
    </xsl:template>
    <xsl:template match="cil-gap-b">
        <xsl:text>. . .</xsl:text>
    </xsl:template>
    <xsl:template match="cil-gap-c">
        <xsl:text>[- - -]</xsl:text>
    </xsl:template>
    <xsl:template match="cil-gap-d">
        <xsl:call-template name="loop1">
            <xsl:with-param name="counter" select="@num_sign"/>
            <xsl:with-param name="sign">+</xsl:with-param>
        </xsl:call-template>
    </xsl:template>
    <xsl:template match="cil-gap-e">
        <xsl:text>[- - - - - -]</xsl:text>
    </xsl:template>
    <xsl:template match="cil-gap-f">
        <xsl:text>[</xsl:text>
        <xsl:call-template name="loop1">
            <xsl:with-param name="counter" select="@quantity"/>
            <xsl:with-param name="sign">.</xsl:with-param>
        </xsl:call-template>
        <xsl:text>]</xsl:text>
    </xsl:template>
    <xsl:template match="cil-gap-g">
        <xsl:text>[-</xsl:text>
        <xsl:value-of select="@quantity"/>
        <xsl:text>?-]</xsl:text>
    </xsl:template>
    <xsl:template match="cil-gap-h">
        <xsl:text>[-c.</xsl:text>
        <xsl:value-of select="@quantity"/>
        <xsl:text>-]</xsl:text>
    </xsl:template>
    <xsl:template match="cil-gap-i">
        <xsl:text>- - - - - -</xsl:text>
    </xsl:template>
    <xsl:template match="cil-gap-j">
        <xsl:text>‹- - -›</xsl:text>
    </xsl:template>
    <xsl:template match="cil-gap-k">
        <xsl:text>‹- - - - - -›</xsl:text>
    </xsl:template>
    <xsl:template match="cil-hi-a">
        <!-- liegende u-klammern -->
        <xsl:text>⸦</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>⸧</xsl:text>
    </xsl:template>
    <xsl:template match="cil-hi-b">
        <!-- vokal mit apex -->
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="cil-hi-c">
        <!-- ligatur -->
        <xsl:param name="p_lig1">
            <xsl:for-each select="*">
                <xsl:choose>
                    <xsl:when test="self::t">
                        <xsl:if test="string-length() &gt; 0">
                            <xsl:call-template name="loop3">
                                <xsl:with-param name="counter" select="1"/>
                                <xsl:with-param name="end" select="string-length()"/>
                                <xsl:with-param name="sign">&#x0361;</xsl:with-param>
                                <!-- &#x0361; ist bogen oberhalb ͡  -->
                            </xsl:call-template>
                        </xsl:if>
                    </xsl:when>
                    <!--<xsl:otherwise><xsl:apply-templates select="."></xsl:apply-templates></xsl:otherwise>-->
                    <xsl:otherwise>
                        <xsl:copy-of select="." copy-namespaces="no"/>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:param>
        <xsl:for-each select="$p_lig1/*">
            <xsl:choose>
                <xsl:when test="self::t">
                    <xsl:choose>
                        <xsl:when test="following-sibling::t">
                            <xsl:value-of select="."/>
                            <xsl:text>&#x0361;</xsl:text>
                        </xsl:when>
                        <xsl:when test="not(following-sibling::t)">
                            <xsl:choose>
                                <xsl:when test="preceding-sibling::*[1][not(self::t)]">
                                    <!--<xsl:text>&#x0361;</xsl:text>-->
                                    <xsl:value-of select="."/>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="."/>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="."/>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:apply-templates select="."><xsl:with-param name="p_parent">lig</xsl:with-param></xsl:apply-templates>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:for-each>
    </xsl:template>

    <xsl:template match="cil-hi-d">
        <!-- text über der zeile, mit überstrich markiert -->
        <text:span text:style-name="cil-supraline">
            <xsl:apply-templates/>
        </text:span>
    </xsl:template>
    <xsl:template match="cil-hi-e">
        <!-- i longa als i mit gravis -->
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="cil-lb">
        <!-- zeilenanfang / zeilenwechsel -->
        <xsl:param name="p_number">
            <xsl:number count="cil-lb" from="content" level="any"/>
        </xsl:param>
        <xsl:if test="not(ancestor::inscriptions-variants) and not(ancestor::inscriptions-comments)">
            <xsl:choose>
                <xsl:when test="$vz_behavior = 2">
                    <xsl:if test="not($p_number = 1)">
                        <text:line-break/>
                    </xsl:if>
                    <xsl:if test="$vz_num = 1">
                        <text:span text:style-name="cil-linenumber">(<xsl:value-of
                                select="$p_number"/><xsl:text>) </xsl:text><text:tab/></text:span>
                    </xsl:if>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:if test="not($p_number = 1)">
                        <xsl:choose>
                            <xsl:when test="@break = 'no'">
                                <xsl:text>|</xsl:text>
                                <text:span text:style-name="cil-sup">
                                    <xsl:value-of select="$p_number"/>
                                </text:span>
                            </xsl:when>
                            <xsl:otherwise>
                                <xsl:text> |</xsl:text>
                                <xsl:if test="$vz_num = 1">
                                    <text:span text:style-name="cil-sup">
                                        <xsl:value-of select="$p_number"/>
                                    </text:span>
                                </xsl:if>
                                <xsl:text> </xsl:text>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:if>
                </xsl:otherwise>
            </xsl:choose>
        </xsl:if>

    </xsl:template>

    <xsl:template match="cil-note">
        <xsl:text>(</xsl:text>
        <xsl:value-of select="@rend"/>
        <xsl:text>)</xsl:text>
    </xsl:template>
    <xsl:template match="cil-num">
        <text:span text:style-name="cil-smallcaps">
            <xsl:apply-templates/>
        </text:span>
    </xsl:template>
    <xsl:template match="cil-orig">
        <text:span text:style-name="cil-uppercase">
            <xsl:apply-templates/>
        </text:span>
    </xsl:template>
    <xsl:template match="cil-space-c">
        <xsl:text>(vac.</xsl:text>
        <xsl:value-of select="@quantity"/>
        <xsl:if test="@extend = 'unknown'">
            <xsl:text>?</xsl:text>
        </xsl:if>
        <xsl:text>)</xsl:text>
    </xsl:template>
    <xsl:template match="cil-srpls">
        <xsl:text>{</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>}</xsl:text>
    </xsl:template>

    <xsl:template match="cil-suppl-a">
        <!-- zerstörte Zeichen deren Form erschlossen werden kann [abc] -->
        <xsl:text>[</xsl:text>
        <xsl:apply-templates/>
        <!-- das ? kann hinzukommen [abc?] -->
        <xsl:if test="@uncert = 'yes'">
            <xsl:text>?</xsl:text>
        </xsl:if>
        <xsl:text>]</xsl:text>
    </xsl:template>

    <xsl:template match="cil-suppl-b">
        <!-- zerstörte Zeichen oder Zeichenfolgen, die nicht sicher erschlossen werden können [abc?] -->
        <xsl:text>[</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>?]</xsl:text>
    </xsl:template>

    <xsl:template match="cil-suppl-c">
        <!-- verlorene Zeichen, ergänzt nach einer früheren Edition; rend: underline -->
        <text:span text:style-name="cil-underline">><xsl:apply-templates/></text:span>
    </xsl:template>

    <xsl:template match="cil-suppl-d">
        <!-- zerstörtes Wort, das bis auf die Endung erschlossen werden kann [abc-] -->
        <xsl:text>[</xsl:text>
        <xsl:apply-templates/>
        <xsl:text>-]</xsl:text>
    </xsl:template>

    <xsl:template match="cil-suppl-e">
        <!-- ausgelassene und zu ergänzende  Zeichen <abc> -->
        <xsl:param name="p_parent"></xsl:param>
        <xsl:text>‹</xsl:text>
        <!--<xsl:text>&lt;</xsl:text>-->
        <xsl:apply-templates/>
        <!-- wenn die ergänzung in einer ligatur erfolgt -->
        <xsl:if test="$p_parent='lig'"><xsl:text>&#x0361;</xsl:text></xsl:if>
        <xsl:text>›</xsl:text>
        <!--<xsl:text>&gt;</xsl:text>-->
    </xsl:template>

    <xsl:template match="cil-suppl-f">
        <!-- subaudible Wörter  Zeichen (scil. abc) -->
        <xsl:text>(scil. </xsl:text>
        <xsl:apply-templates/>
        <xsl:text>)</xsl:text>
    </xsl:template>

    <xsl:template match="cil-unclr">
        <!-- Lesung nur im Kontext zu erschließen -->
        <xsl:if test="string-length() &gt; 0">
            <xsl:call-template name="loop2">
                <xsl:with-param name="counter" select="1"/>
                <xsl:with-param name="end" select="string-length()"/>
                <xsl:with-param name="sign">&#x0323;</xsl:with-param>
                <!-- unterpunkt (&#x0323;) aus dem unicode-bereich der combinierbaren 
          diakritischen zeichen  -->
            </xsl:call-template>
        </xsl:if>
    </xsl:template>

    <xsl:template match="cil-wtr">
        <!-- interpunktion -->
        <xsl:param name="p_data_link_target">
            <xsl:value-of select="@p_data_link_target"/>
        </xsl:param>
        <!-- das zeichen aus den links heraussuchen -->
        <xsl:value-of
            select="ancestor::article/links/link[@to_id = $p_data_link_target]/property/lemma"/>
    </xsl:template>




    <!-- loops -->
    <xsl:template name="loop1">
        <xsl:param name="counter"/>
        <xsl:param name="sign"/>
        <xsl:choose>
            <xsl:when test="$counter &gt; 0">
                <xsl:value-of select="$sign"/>
                <xsl:call-template name="loop1">
                    <xsl:with-param name="counter" select="$counter - 1"/>
                </xsl:call-template>
            </xsl:when>
            <xsl:otherwise/>
        </xsl:choose>
    </xsl:template>

    <xsl:template name="loop2">
        <xsl:param name="counter"/>
        <xsl:param name="end"/>
        <xsl:param name="sign"/>
<!--        <xsl:param name="p_string1"><xsl:value-of select="."/></xsl:param>
        <xsl:param name="p_string1"><xsl:value-of select="."/></xsl:param>  -->      
        
        <xsl:if test="$counter &lt;= $end">
            <xsl:value-of select="substring(.,$counter,1)"/><xsl:value-of select="$sign"/>
            <xsl:call-template name="loop2">
                <xsl:with-param name="counter" select="$counter + 1"/>
                <xsl:with-param name="end" select="$end"/>
                <xsl:with-param name="sign" select="$sign"/>
            </xsl:call-template>
        </xsl:if>
    </xsl:template>

    <xsl:template name="loop3">
        <xsl:param name="counter"/>
        <xsl:param name="end"/>
        <xsl:param name="sign"/>
        <xsl:if test="$counter &lt;= $end">
            <t>
                <xsl:value-of select="substring(., $counter, 1)"/>
            </t>
            <xsl:call-template name="loop3">
                <xsl:with-param name="counter" select="$counter + 1"/>
                <xsl:with-param name="end" select="$end"/>
            </xsl:call-template>
        </xsl:if>
    </xsl:template>

</xsl:stylesheet>
