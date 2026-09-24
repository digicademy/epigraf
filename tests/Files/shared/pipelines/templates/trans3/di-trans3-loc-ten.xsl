<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<!-- funktion dieses stylesheets:
        
    auslesen des platzhalters <loc_ten/> für verweise auf nummern in grundrissen
    
    es wird aufgerufen in di-trans3-articles.xsl
    
    vorgehensweise:
    der platzhalter selbst ist leer, er markiert nur die stelle, an der eine ziffern-fußnote (element <item>)
    mit einem textbaustein erzeut werden soll,
    der inhalt des textbausteins wird aus dem abschnitt signaturen des artikels generiert,
    die signatur muss in EpigrafDesktop im feld Autor/Quelle mit einer Referenz auf 
    einen eintrag im der literaturliste versehen sein,
    dieser eintrag enthält in der linken spalte den kompletten text für den baustein, 
    in der rechten einen kurztitel,
    solche einträge sollten in der literaturliste in einem übergeordneten gruppeneintrag "Textbausteine"
    zusammengefasst werden, bei dem das kontrollkästchen "Bezeichnung ausblenden" markiert ist
    
    von dem textbaustein ausgehend wird zuerst in den abschnitte "signaturen" des artikels 
    zum ersten item navigiert, dessen ancestor-property ein lemma enthält, das auf "Textbausteine" lautet,
    dieses lemma wird ausgelesen,
    dann werden alle Grundriss-items angesteuert und aus den signaturen die nummern entnommen.
    -->

    <xsl:template name="loc_ten">
        <!-- aus dem ersten eintrag desselben textbaustein wird der text entnommen -->
        <xsl:param name="p_loc_ten_text">
            <xsl:for-each select="ancestor::article/sections/section[@sectiontype='signatures']/items/item[contains(property//property/lemma, 'Textbausteine')]">
                <xsl:if test="not(property/lemma=preceding-sibling::item/property/lemma)">
                    <xsl:value-of select="normalize-space(property/lemma)"/>
                </xsl:if>
            </xsl:for-each>
<!--        <xsl:value-of select="normalize-space(ancestor::article/sections/section[@sectiontype='signatures']/items/item[contains(property/lemma, 'Grundriss')][1]/property/lemma)"/>
-->
        </xsl:param>
        
        <!-- id des textbausteins in der literaturliste zur (mittelbaren) identifikation des grundrisses 
            (falls es mehrere grundrisse gibt) -->
        <xsl:param name="p_id_textbaustein">
            <xsl:for-each select="ancestor::article/sections/section[@sectiontype='signatures']/items/item[contains(property//property/lemma, 'Textbausteine')]">
                <xsl:if test="not(property/@id=preceding-sibling::item/property/@id)">
                    <xsl:value-of select="property/@id"/>
                </xsl:if>
            </xsl:for-each>
            
<!--            <xsl:value-of select="ancestor::article/sections/section[@sectiontype='signatures']/items/item[contains(property/lemma, 'Grundriss')]/property/@id"/>
-->
        </xsl:param>

        <!-- die laufende nummer des grundrisses (map) wird ermittelt -->
        <xsl:param name="p_map_number">
            <xsl:for-each select="ancestor::book/drawings//map[map_concordance//rec_lit[@data-link-target=$p_id_textbaustein]]"><xsl:value-of select="count(.)"/></xsl:for-each>
        </xsl:param>
        
        <!-- aus allen zutreffenden einträgen werden die nummern entnommen -->
        <xsl:param name="p_loc_ten_nrn">
            <xsl:for-each select="ancestor::article/sections/section[@sectiontype='signatures']//item[contains(property//property/lemma, 'Textbausteine')]">
         <xsl:variable name="v_loc_ten_nr">
             <!-- wenn die angabe nur aus ziffern besteht, bei diesen führende nullen entfernen,
             andernfalls die angabe kopieren-->
             <xsl:analyze-string select="value" regex="^[0-9]*$">
                 <xsl:matching-substring>
                     <xsl:number format="1" value="."/>
                 </xsl:matching-substring>
                 <xsl:non-matching-substring>
                     <xsl:choose>
                         <xsl:when test="starts-with(.,'00')">
                             <xsl:value-of select="substring(.,3)"/>
                         </xsl:when>
                         <xsl:when test="starts-with(.,'0') and not(starts-with(.,'00'))">
                             <xsl:value-of select="substring(.,2)"/>
                         </xsl:when>
                         <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
                     </xsl:choose>
                 </xsl:non-matching-substring>
             </xsl:analyze-string>
         </xsl:variable>

        <!-- der link wird aus den zuvor erstellten parametern und variablen modelliert -->
            <link type="map"  map_number="{$p_map_number}" mapID="{$p_id_textbaustein}" objectID="{$p_id_textbaustein}#{$v_loc_ten_nr}">
                <xsl:copy-of select="$v_loc_ten_nr"/>
            </link><xsl:if test="following-sibling::item[contains(property/lemma, 'Grundriss')]"><xsl:text>, </xsl:text></xsl:if>
        </xsl:for-each>
        </xsl:param>
        
       <!-- <parametertest>
            <p_loc_ten_text><xsl:copy-of select="$p_loc_ten_text"></xsl:copy-of></p_loc_ten_text>
            <p_id_textbaustein><xsl:copy-of select="$p_id_textbaustein"></xsl:copy-of></p_id_textbaustein>
            <p_map_number><xsl:copy-of select="$p_map_number"></xsl:copy-of></p_map_number>
            <p_loc_ten_nrn><xsl:copy-of select="$p_loc_ten_nrn"></xsl:copy-of></p_loc_ten_nrn>
        </parametertest>-->
        
        <!-- verweistext und nummer(n) mit dem/denn link/s  werden zusammengesetzt und ausgegeben-->
        <xsl:value-of select="$p_loc_ten_text"/><xsl:text> </xsl:text><xsl:copy-of select="$p_loc_ten_nrn"/><xsl:text>.</xsl:text>
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
                <!-- TODO: No data in stylesheets! -->
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
                            <xsl:variable name="v_loc_ten_text"><xsl:value-of select="referenz/lemma"/></xsl:variable>
                            <xsl:variable name="v_loc_ten_nr">
                                <xsl:choose>
                                    <xsl:when test="contains(bezeichnung, '.gp')"><xsl:value-of select="substring-after(bezeichnung, '.gp')"/></xsl:when>
                                    <xsl:otherwise><xsl:value-of select="bezeichnung"/></xsl:otherwise>
                                </xsl:choose>
                            </xsl:variable>
                            <xsl:text>Siehe </xsl:text><xsl:value-of select="$v_loc_ten_text"/><xsl:text>, Nr.&#x20;</xsl:text><xsl:value-of select="$v_loc_ten_nr"/>
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
        <xsl:param name="p_platzhalter1">
            <platzhalter>
            <!-- alle signaturen ansteuern, die als Autor/Quelle den string 'grundriss' enthalten -->
        <xsl:for-each select="ancestor::article/sections/section[@sectiontype='signatures']/items/item[contains(property/lemma, 'Grundriss')]">
            <!-- die referenz auf den kurztitel der quellenangabe als variable ablegen -->
            <xsl:variable name="v_link-item"><xsl:value-of select="property/@id"/></xsl:variable>
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
                <xsl:for-each select="//section[@caption='Platzhalter']/links/item[@link_item=$v_link-item]">
                    <xsl:variable name="v_tag-id"><xsl:value-of select="@tag_id"/></xsl:variable>
                    <xsl:for-each select="ancestor::section/fields/beschreibung/*[@tagname='Platzhalter'][rec_lit[@id=$v_tag-id]]">
                    <ersetzen_durch><xsl:value-of select="substring-after(.,'|')"/></ersetzen_durch>
                    <trenner><xsl:value-of select="following-sibling::*[@tagname='Trennzeichen']"/></trenner>
                    </xsl:for-each>+
                </xsl:for-each>
            </loc_ten>
        </xsl:for-each>
            </platzhalter>
        </xsl:param>
        
        <xsl:param name="p_platzhalter2">
        <!-- zu jedem ersten verweis auf denselben grundriss gehen -->
        <xsl:for-each select="$p_platzhalter1//loc_ten[not(kurztitel/@link_item=preceding-sibling::loc_ten/kurztitel/@link_item)]">
            <xsl:variable name="v_link-item"><xsl:value-of select="kurztitel/@link_item"/></xsl:variable>
            <xsl:variable name="v_trenner"><xsl:value-of select="trenner"/></xsl:variable>
            <loc_ten>
                <xsl:copy-of select="@*"/>
                <xsl:copy-of select="ersetzen_durch"/>
                <nrn>
                    <xsl:for-each select="ancestor::platzhalter/loc_ten[kurztitel/@link_item=$v_link-item]">
                    <nr>
                        <xsl:copy-of select="bezeichnung/@id"/>
                        <xsl:value-of select="number(substring-after(bezeichnung,$v_trenner))"/>
                    </nr>
                </xsl:for-each>
                </nrn>
            </loc_ten>
        </xsl:for-each>
        </xsl:param>
        
        <!-- parametertest -->
<!--        <test1><xsl:copy-of select="$p_platzhalter1"/></test1>
        <test2><xsl:copy-of select="$p_platzhalter2"/></test2>-->
        
        <xsl:for-each select="$p_platzhalter2/loc_ten">
            <xsl:value-of select="ersetzen_durch"/><xsl:text> </xsl:text>
            <xsl:for-each select="nrn/nr"><xsl:value-of select="."/><xsl:if test="following-sibling::nr"><xsl:text>, </xsl:text></xsl:if></xsl:for-each>
        </xsl:for-each>
        <xsl:text>.</xsl:text><xsl:if test="following-sibling::loc_ten"><xsl:text> </xsl:text></xsl:if>
        
    </xsl:template>
    
</xsl:stylesheet>
