<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans1-commons.xsl"/>
    <xsl:output indent="no"/>  

<!-- mit diesem stylesheet wird das standorteregister erzeugt,
            es wird aufgerufen in di-trans1-indices-commons.xsl im template index-restrukturieren-->
    
    
    


<xsl:template name="locations-target-sections-expand">
    <!-- muster für die ergänzung der referenzen auf artikelnummern im standorteregister: 
    a) für ehemalige standorte sollen die standortbezeichnungen derart gekennzeichnet werden, 
       dass man sie am ende kursiv ausgegeben kann,
    b) für aktuelle oder zuletzt bekannte standorte erfolgt die ausgabe recte
    c) aktuelle oder zuletzt bekannte standorte sind solche, die im artikel in der 
       standorteliste an oberster stelle stehen oder, wenn sie nicht an oberster stelle stehen, 
       mit dem textbaustein primesite markierte wurden
    d) ist der inschriftenträger verloren, soll die artikelnummer derart gekennzeichnet werden, 
       dass sie kursiv ausgegeben werden kann

    für diese beiden eigenschaften werden im folgenden die attribute @before und @lost angelegt
    -->
    <!-- für die münchener reihe werden die referenzen auf artikelnummern ergänzt um die attribute:
        •	Laufnummer (mit Status)
        •	Datierung
        •	Inschriftenträger 
        •	Sprache
        •	Schriftart

        dafür werden als attribute erzeugt: 
        @state, @date, @object, @language, @fonttype
    -->
    
    <!-- der container für die referenzen (element <section>) wird 
         in der ursprünglichen form wieder angelegt -->
    <sections log1="sectLoc"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <!-- alle referenzen (element <section>) aufsuchen -->
        <xsl:for-each select="sections/section">
            <!-- variable mit der @id des registereintrags (element <property>) -->
            <xsl:variable name="v_property_id"><xsl:value-of select="ancestor::property/@id"/></xsl:variable>
            <!-- variable mit der @id des ziel-artikels -->
            <xsl:variable name="v_article_id"><xsl:value-of select="@articles_id"/></xsl:variable>
            <!-- variable mit der @id der ziel-section -->
            <xsl:variable name="v_section_id"><xsl:value-of select="@id"/></xsl:variable>
            
            <!-- variable für das schlüsselwort primeSite (wenn vorhanden, d. h. wenn es einen weiteren aktuellen 
                    oder zuletzt bekannten standort gibt) -->
            <xsl:variable name="v_location_primesite">
                <!-- vom standort im register zum zugehörigen artikel und weiter bis zur section mit den standorten -->
                <xsl:for-each select="ancestor::book/article[@id=$v_article_id]/sections/section[@id=$v_section_id]">
                    <!-- in der section weiter bis zum item mit der property des betreffenden standorts  -->
                    <xsl:for-each select="items/item[@itemtype='locations']/property[@id=$v_property_id]">
                        <!-- von hier wieder zurück auf das parent::item und dort hinab zum content-element 
                                mit dem literaturverweis, der auf des schlüsselwort primesite führt -->
                        <xsl:for-each select="parent::item/content/rec_lit">
                            <!-- das attribut data_link_target in einer variablen speichern -->
                            <xsl:variable name="v_data_link_target"><xsl:value-of select="@data-link-target"/></xsl:variable>
                            <!-- wieder hoch zum artikel, dann in die links und zum link auf den 
                                    literaturverweis mit dem schlüsselwort -->
                            <xsl:for-each select="ancestor::article/links/link[@to_id=$v_data_link_target]">
                                <!-- nun property/lemma mit dem schlüsselwort primeSite (wennn vorhanden) auslesen -->
                                <xsl:value-of select="property/lemma"/>
                            </xsl:for-each>
                        </xsl:for-each>
                    </xsl:for-each>
                </xsl:for-each>
            </xsl:variable>
            
            <!-- wenn in EpiWeb das kontrollkästchen "Letztbekannter Standort" markiert wurde,
                    enthält das element <item/value> die ziffer 1, 
                    was im folgenden als variable gespeichert wird
            -->
            <xsl:variable name="v_location_lastsite">
                <!-- vom standort im register zum zugehörigen artikel und weiter bis zur section mit den standorten -->
                <xsl:for-each select="ancestor::book/article[@id=$v_article_id]/sections/section[@id=$v_section_id]">
                    <!-- in der section weiter bis zum item mit der property des betreffenden standorts  -->
                    <xsl:for-each select="items/item[@itemtype='locations'][property[@id=$v_property_id]]">
                        <!-- im item ist das feld value auf 1 gesetzt, wenn "letztbekannter standort" ausgewählt wurde -->
                        <xsl:value-of select="value"/>
                    </xsl:for-each>
                </xsl:for-each>
            </xsl:variable>
            
            <!-- variablentest
<parametertest>
    <t1><xsl:value-of select="$v_property_id"/></t1>
    <t2><xsl:value-of select="$v_article_id"/></t2>
    <t3><xsl:value-of select="$v_section_id"/></t3>
    <t4><xsl:value-of select="$v_location_primesite"/></t4>
    <t5><xsl:value-of select="$v_location_lastsite"/></t5>
</parametertest>
-->
            
            
            <!-- referenz neu anlegen und vorhandene attribute kopieren, 
                    die attribute @before und @lost erzeugen -->
            <section log1="s2">
                <xsl:copy-of select="@*"/>
                <xsl:attribute name="before">
                    <xsl:choose>
                        <!-- wenn der betreffende standort in der standorteliste  des artikels 
                                an erster stelle steht: @before=0 -->
                        <xsl:when test="ancestor::book/article[@id=$v_article_id]/sections/section[@id=$v_section_id]/items/item[@itemtype='locations'][1]/property[@id=$v_property_id]">0</xsl:when>
                        <!-- wenn zum standort im feld Ergänzung der textbaustein "zweitstandort/primesite" 
                                aus dem literaturverzeichnis eingefügt wurde @before=0  -->
                        <xsl:when test="$v_location_primesite='primeSite'">0</xsl:when>
                        <!-- wenn der standort als letztbekannt (value=1) markiert wurde @before=0-->
                        <xsl:when test="$v_location_lastsite='1'">0</xsl:when>
                        <!-- in allen anderen fällen: @before=1 -->   
                        <xsl:otherwise>1</xsl:otherwise>
                    </xsl:choose>
                </xsl:attribute>
                <xsl:attribute name="lost">
                    <!-- zum artikel und weiter zum abschnitt beschaffenheit (section conditions) navigieren -->
                    <xsl:for-each select="ancestor::book/article[@id=$v_article_id]/sections/section[@sectiontype='conditions']/items/item/property">
                        <!-- die angaben im lemma auf zwei zustände reduzieren: 0=nicht verloren, 1=verloren -->
                        <xsl:choose>
                            <xsl:when test="norm_iri='di_traditio1'">0</xsl:when>
                            <xsl:when test="norm_iri='di_traditio2'">1</xsl:when>
                            <xsl:when test="norm_iri='di_traditio3'">0</xsl:when>
                            <xsl:when test="norm_iri='di_traditio4'">0</xsl:when>
                            <xsl:when test="norm_iri='di_traditio5'">0</xsl:when>
                            <xsl:when test="norm_iri='di_traditio6'">0</xsl:when>
                        </xsl:choose>
                    </xsl:for-each>
                </xsl:attribute>
                <xsl:attribute name="state">
                    <!-- zum artikel und weiter zum abschnitt beschaffenheit (section conditions) navigieren -->
                    <xsl:for-each select="ancestor::book/article[@id=$v_article_id]/sections/section[@sectiontype='conditions']/items/item/property">
                        <!-- die sigle des entsprechenden überlieferungszustands gemäß dem lemma einfügen -->
                        <xsl:choose>
                            <xsl:when test="norm_iri='di_traditio1'"></xsl:when>
                            <xsl:when test="norm_iri='di_traditio2'">†</xsl:when>
                            <xsl:when test="norm_iri='di_traditio3'">(†)</xsl:when>
                            <xsl:when test="norm_iri='di_traditio4'">(†)</xsl:when>
                            <xsl:when test="norm_iri='di_traditio5'">(†)</xsl:when>
                            <xsl:when test="norm_iri='di_traditio6'">†?</xsl:when>
                        </xsl:choose>
                    </xsl:for-each>
                </xsl:attribute>
                <xsl:attribute name="date">
                    <!-- zum artikel navigieren und die datierung des objekts auslesen -->
                    <xsl:for-each select="ancestor::book/article[@id=$v_article_id]/sections/section[@sectiontype='conditions']">
                        <xsl:value-of select="items/item[@itemtype='conditions']/date_value"/>
                    </xsl:for-each>
                </xsl:attribute>
                <xsl:attribute name="objecttype">
                    <!-- zum artikel navigieren und die bezeichnugn des inschriftenträgers auslesen -->
                    <xsl:for-each select="ancestor::book/article[@id=$v_article_id]/sections/section[@sectiontype='objecttypes']">
                        <xsl:for-each select="items/item">
                            <xsl:value-of select="normalize-space(property/name)"/>
                            <xsl:if test="following-sibling::item"><xsl:text>, </xsl:text></xsl:if>
                        </xsl:for-each>
                        
                        <!--<xsl:value-of select="normalize-space(items/item[1]/property/name)"/>-->
                    </xsl:for-each>
                </xsl:attribute>
                <xsl:attribute name="language">
                    <!-- sprachen auslesen-->
                    <xsl:call-template name="collect-languages">
                        <xsl:with-param name="v_article_id"><xsl:value-of select="$v_article_id"/></xsl:with-param>
                    </xsl:call-template>
                </xsl:attribute>
                <xsl:attribute name="fonttype">
                    <!-- schriftarten auslesen-->                    
                        <xsl:call-template name="collect-fonttypes">
                            <xsl:with-param name="v_article_id"><xsl:value-of select="$v_article_id"/></xsl:with-param>
                        </xsl:call-template>
                </xsl:attribute>
                
            </section><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
    </sections>
</xsl:template>
    
    <xsl:template name="collect-fonttypes">
        <xsl:param name="v_article_id"></xsl:param>

        <!-- items sammeln -->
        <xsl:param name="p_collect1">
            <!-- zum artikel und weiter zu den darin enthaltenen inschriften navigieren,
                            die in den inschriften vorkommenden schriftarten auslesen--> 
            <xsl:for-each select="ancestor::book/article[@id=$v_article_id]/sections/section[@sectiontype='inscription']">
                <xsl:for-each select="items/item[@itemtype='fonttypes']">
                    <item><t><xsl:value-of select="normalize-space(property[@propertytype='fonttypes']/name)"/></t></item>
                </xsl:for-each>
            </xsl:for-each>
            
        </xsl:param>
        <!-- doubletten entfernen -->
        <xsl:param name="p_collect2">
            <xsl:for-each select="$p_collect1/item[not(t=preceding-sibling::item/t)]">
                <xsl:copy-of select="."></xsl:copy-of>
            </xsl:for-each>
        </xsl:param>
        
        <!-- items ausgeben, gegebenenfalls kommata setzten -->
        <xsl:for-each select="$p_collect2/item">
            <xsl:value-of select="t"/><xsl:if test="following-sibling::item"><xsl:text>, </xsl:text></xsl:if>
        </xsl:for-each>
        
    </xsl:template>
    <xsl:template name="collect-languages">
        <xsl:param name="v_article_id"></xsl:param>

        <!-- items sammeln -->
        <xsl:param name="p_collect1">
        <!-- zum artikel und weiter zu den darin enthaltenen inschriften navigieren,
                            die in den inschriften vorkommenden sprachen auslesen-->            
         <xsl:for-each select="ancestor::book/article[@id=$v_article_id]/sections/section[@sectiontype='inscription']">
            <xsl:for-each select="items/item[@itemtype='languages']">
                <item><t><xsl:value-of select="normalize-space(property[@propertytype='languages']/unit)"/></t></item>
            </xsl:for-each>
        </xsl:for-each>           
        </xsl:param>
        <!-- doubletten entfernen -->        
        <xsl:param name="p_collect2">
            <xsl:for-each select="$p_collect1/item[not(t=preceding-sibling::item/t)]">
                <xsl:copy-of select="."></xsl:copy-of>
            </xsl:for-each>
        </xsl:param>
        <!-- items ausgeben, gegebenenfalls kommata setzten -->
        <xsl:for-each select="$p_collect2/item">
            <xsl:value-of select="t"/><xsl:if test="following-sibling::item"><xsl:text>, </xsl:text></xsl:if>
        </xsl:for-each>

    </xsl:template>
</xsl:stylesheet>








