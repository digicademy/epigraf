<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- auslesen des platzhalters <loc_ten/> für verweise auf nummern in grundrissen
    der platzhalter selbst ist leer, er markiert nur die stelle, an der eine fußnote mit einem textbaustein referenziert werden soll,
    der inhalt des textbausteins wird aus dem abschnitt signaturen des artikels generiert,
    die signatur muss in EpigrafDesktop im feld Autor/Quelle mit einer Referenz auf einen eintrag im der literaturliste versehen sein
    dieser eintrag enthält in der linken spalte den kompletten text für den baustein, in der rechten einen kurztitel
    solche einträge sollten in der literaturliste in einem übergeordneten gruppeneintrag zusammengefasst werden, bei dem das kontrollkästchen "Bezeichnung ausblenden" markiert ist

    von dem textbaustein ausgehend wird zuerst in den abschnitte "signaturen" des artikels zum ersten item navigiert, dessen lemma den string "Grundriss" enthält
    das lemma wird ausgelesen,
    dann werden alle Grundriss-items angesteuert und aus den signaturen die nummern entnommen
    -->

    <xsl:template name="loc_ten">
        <!-- aus dem ersten eintrag wird der text entnommen -->
        <xsl:param name="loc_ten_text">
        <xsl:value-of select="normalize-space(ancestor::article/sections/section[@sectiontype='signatures']/items/item[contains(property/lemma, 'Grundriss')]/property/lemma)"/>
        </xsl:param>

        <!-- id des textbausteins in der literaturliste zur (mittelbaren) identifikation des grundrisses
            (falls es mehrere grundrisse gibt) -->
        <xsl:param name="id_textbaustein"><xsl:value-of select="ancestor::article/sections/section[@sectiontype='signatures']/items/item[contains(property/lemma, 'Grundriss')]/property/@id"/></xsl:param>

        <!-- die laufende nummer des grundrisses (map) wird ermittelt -->
        <xsl:param name="map_number">
            <xsl:for-each select="ancestor::book/zeichnungen//map[konkordanz//rec_lit[@data-link-target=$id_textbaustein]]"><xsl:value-of select="count(.)"/></xsl:for-each>
        </xsl:param>

        <!-- aus allen zutreffenden einträgen werden die nummern entnommen -->
        <xsl:param name="loc_ten_nrn">
        <xsl:for-each select="ancestor::article/sections/section[@sectiontype='signatures']//item[contains(property/lemma, 'Grundriss')]">
         <xsl:variable name="loc_ten_nr">
             <xsl:choose>
                <xsl:when test="contains(value, '.gp')"><xsl:number format="1" value="substring-after(value, '.gp')"/></xsl:when>
                <xsl:otherwise><xsl:number format="1" value="value"/></xsl:otherwise>
            </xsl:choose>
        </xsl:variable>

        <!-- der link wird aus den zuvor erstellten parametern und variablen modelliert -->
            <link type="map"  map_number="{$map_number}" mapID="{$id_textbaustein}" objectID="{$id_textbaustein}#{$loc_ten_nr}">
                <xsl:copy-of select="$loc_ten_nr"/>
            </link><xsl:if test="following-sibling::item[contains(property/lemma, 'Grundriss')]"><xsl:text>, </xsl:text></xsl:if>
        </xsl:for-each>
        </xsl:param>

        <!-- verweistext und nummer(n) mit dem/denn link/s  werden zusammengesetzt und ausgegeben-->
        <xsl:value-of select="$loc_ten_text"/><xsl:text> </xsl:text><xsl:copy-of select="$loc_ten_nrn"/><xsl:text>.</xsl:text>
    </xsl:template>


<!-- hier folgt das alte muster zum erstellen des textbausteins für den greifswald-band
    es kann entfernt werden, wenn der greifswald-bestand auf die neue form umgestellt wurde
    -->
    <xsl:template name="loc_ten_hgw">
        <xsl:choose>
            <!--fuer Greifswald-->
            <xsl:when test="//projekt/kuerzel[text()='hgw']">
                <!-- Verweise auf aktuelle Pläne -->
                <xsl:for-each select="ancestor::objekt/signaturen/signatur[@quelle='Bearbeiter' or @quelle='']">
                    <xsl:if test="contains(.,'hgw')">
                        <xsl:if test="contains(. , 'nikolai') or contains(. , 'marien')">
                            <xsl:text>Siehe Kirchengrundrisse mit Grabplatten, S. </xsl:text><xsl:call-template name="sn"/><xsl:text>, Grundriss&#x20;</xsl:text>
                            <xsl:if test = "contains(. , 'marien')">
                                <xsl:text>St. Marien, Nr. &#x20;</xsl:text><signatur_grundriss><xsl:value-of select="." /></signatur_grundriss><xsl:text>. </xsl:text>
                            </xsl:if>
                            <xsl:if test = "contains(. , 'nikolai')">
                                <xsl:text>St. Nikolai, Nr.&#x20;</xsl:text><signatur_grundriss><xsl:value-of select="." /></signatur_grundriss><xsl:text>. </xsl:text>
                            </xsl:if>
                        </xsl:if>
                    </xsl:if>
                </xsl:for-each>
                <!--Verweise auf Lageplaene bei Pyl-->
                <xsl:for-each select="ancestor::objekt/signaturen/signatur[contains(@quelle, 'biblio77') or contains(@quelle, 'Pyl') or contains(., 'pyl')]">
                    <xsl:if test="ancestor::objekt[@tradindex!='traditio2']">
                        <xsl:text>Zur früheren Lage siehe&#x20;</xsl:text>
                    </xsl:if>
                    <xsl:if test="ancestor::objekt[@tradindex='traditio2']">
                        <xsl:text>Siehe&#x20;</xsl:text>
                    </xsl:if>
                    <xsl:text>Pyl, Greifswalder Kirchen, nach S.&#x00A0;248, Grundriss&#x20;</xsl:text>
                    <xsl:if test = "contains(. , 'marien')">
                        <xsl:text>St. Marien,&#x20;</xsl:text>
                    </xsl:if>
                    <xsl:if test = "contains(. , 'nikolai')">
                        <xsl:text>St. Nikolai,&#x20;</xsl:text>
                    </xsl:if>
                    <xsl:if test = "contains(. , 'jacobi') or contains(. , 'jakobi')">
                        <xsl:text>St. Jacobi,&#x20;</xsl:text>
                    </xsl:if>
                    <xsl:text>Nr.&#x20;</xsl:text>
                    <!--die Nummern werden ausgelesen, fuehrende Nullen entfernt-->
                    <xsl:choose>
                        <xsl:when test="contains(., 'pyl0')">
                            <xsl:choose>
                                <xsl:when test="contains(., 'pyl00')">
                                    <xsl:value-of select="substring-after(. , 'pyl00')"/>
                                    <xsl:text>.&#x20;</xsl:text>
                                </xsl:when>
                                <xsl:otherwise>
                                    <xsl:value-of select="substring-after(. , 'pyl0')"/>
                                    <xsl:text>.&#x20;</xsl:text>
                                </xsl:otherwise>
                            </xsl:choose>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:value-of select="substring-after(. , 'pyl')"/>
                            <xsl:text>.&#x20;</xsl:text>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:when>
            <!-- Ende: loc_ten für Greifswald -->

            <xsl:otherwise>
                <xsl:choose>
                    <xsl:when test="//section[@caption='Platzhalter']//*[@tagname='Platzhalter']">
                        <xsl:call-template name="platzhalter"></xsl:call-template>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:for-each select="ancestor::article/section[@type='signaturen']//item[contains(referenz/lemma, 'Grundriss')]">
                            <xsl:variable name="loc_ten_text"><xsl:value-of select="referenz/lemma"/></xsl:variable>
                            <xsl:variable name="loc_ten_nr">
                                <xsl:choose>
                                    <xsl:when test="contains(bezeichnung, '.gp')"><xsl:value-of select="subtring-after(bezeichnung, '.gp')"/></xsl:when>
                                    <xsl:otherwise><xsl:value-of select="bezeichnung"/></xsl:otherwise>
                                </xsl:choose>
                            </xsl:variable>
                            <xsl:text>Siehe </xsl:text><xsl:value-of select="$loc_ten_text"/><xsl:text>, Nr.&#x20;</xsl:text><xsl:value-of select="$loc_ten_nr"/>
                        </xsl:for-each>
                        <xsl:text>Siehe </xsl:text><xsl:value-of select="ancestor::article/section[@type='signaturen']//item[contains(referenz/lemma, 'Grundriss')]/referenz/lemma"/><xsl:text>, Nr.&#x20;</xsl:text><xsl:for-each select="ancestor::article/section[@type='signaturen']//item[contains(referenz/kurztitel, 'Grundriss')]"><xsl:value-of select="number(substring-after(bezeichnung,'.gp'))"/><xsl:if test="following-sibling::item[contains(referenz/kurztitel, 'Grundriss')]"><xsl:text>, </xsl:text></xsl:if></xsl:for-each><xsl:text>.</xsl:text>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>



    <xsl:template name="sn"></xsl:template>

    <xsl:template name="platzhalter"><!-- wird aufgerufen im template loc_ten_hgw -->

<!-- überarbeitung begonnen JH 2021.06.28 -->
        <xsl:param name="platzhalter1">
            <platzhalter>
            <!-- alle signaturen ansteuern, die als Autor/Quelle den string 'grundriss' enthalten -->
        <xsl:for-each select="ancestor::article/sections/section[@sectiontype='signatures']/items/item[contains(property/lemma, 'Grundriss')]">
            <!-- die referenz auf den kurztitel der quellenangabe als variable ablegen -->
            <xsl:variable name="link-item"><xsl:value-of select="property/@id"/></xsl:variable>
            <!-- den eintrag neu anlegen -->
            <loc_ten>
                <xsl:copy-of select="@*"/>
                <!-- die bezeichnung der signatur kopieren -->
                <bezeichnung>
                    <xsl:copy-of select="@id"/>
                    <xsl:value-of select="bezeichnung"/>
                </bezeichnung>
                <!-- den kurztitel der quellenangabe kopieren -->
                <xsl:for-each select="referenz">
                    <kurztitel>
                        <xsl:copy-of select="@link_item"/>
                        <xsl:value-of select="kurztitel"/>
                    </kurztitel>
                </xsl:for-each>

                <!-- anhand der signatur des kurztitels
                    den ersetzungtext in den textbausteinen des bandes suchen -->
                <xsl:for-each select="//section[@caption='Platzhalter']/links/item[@link_item=$link-item]">
                    <xsl:variable name="tag-id"><xsl:value-of select="@tag_id"/></xsl:variable>
                    <xsl:for-each select="ancestor::section/fields/beschreibung/*[@tagname='Platzhalter'][rec_lit[@id=$tag-id]]">
                    <ersetzen_durch><xsl:value-of select="substring-after(.,'|')"/></ersetzen_durch>
                    <trenner><xsl:value-of select="following-sibling::*[@tagname='Trennzeichen']"/></trenner>
                    </xsl:for-each>+
                </xsl:for-each>
            </loc_ten>
        </xsl:for-each>
            </platzhalter>
        </xsl:param>

        <xsl:param name="platzhalter2">
        <!-- zu jedem ersten verweis auf denselben grundriss gehen -->
        <xsl:for-each select="$platzhalter1//loc_ten[not(kurztitel/@link_item=preceding-sibling::loc_ten/kurztitel/@link_item)]">
            <xsl:variable name="link-item"><xsl:value-of select="kurztitel/@link_item"/></xsl:variable>
            <xsl:variable name="trenner"><xsl:value-of select="trenner"/></xsl:variable>
            <loc_ten>
                <xsl:copy-of select="@*"/>
                <xsl:copy-of select="ersetzen_durch"/>
                <nrn>
                <xsl:for-each select="ancestor::platzhalter/loc_ten[kurztitel/@link_item=$link-item]">
                    <nr>
                        <xsl:copy-of select="bezeichnung/@id"/>
                        <xsl:value-of select="number(substring-after(bezeichnung,$trenner))"/>
                    </nr>
                </xsl:for-each>
                </nrn>
            </loc_ten>
        </xsl:for-each>
        </xsl:param>
   <test1><xsl:copy-of select="$platzhalter1"/></test1>
       <test2><xsl:copy-of select="$platzhalter2"/></test2>

        <xsl:for-each select="$platzhalter2/loc_ten">
            <xsl:value-of select="ersetzen_durch"/><xsl:text> </xsl:text>
            <xsl:for-each select="nrn/nr"><xsl:value-of select="."/><xsl:if test="following-sibling::nr"><xsl:text>, </xsl:text></xsl:if></xsl:for-each>
        </xsl:for-each>
        <xsl:text>.</xsl:text><xsl:if test="following-sibling::loc_ten"><xsl:text> </xsl:text></xsl:if>

    </xsl:template>

</xsl:stylesheet>
