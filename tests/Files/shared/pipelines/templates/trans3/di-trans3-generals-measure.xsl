<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
>

<!-- in diesem stylesheet werden die maßangaben zur 
        größe des inschriftenträgers zusammengetragen und formatiert,
    
    es wird aufgerufen in di_trans3-articles.xsl
    -->

<xsl:template name="generals_measure">
    
    <!-- erster schritt: die einzelnen massangaben werden ausgelesen, nach der im datensatz angezeigten reihenfolge sortiert 
         und in einem komprimierten elemente-baum abgelegt -->
    <xsl:param name="p_abmessungen1">
        <!-- maßangabe aufsuchen -->
        <xsl:for-each select="sections/section[@sectiontype='measures']/items/item">
            <!-- Nur Kategorien, die nicht als "ausblenden" markiert sind -->
            <xsl:if test="not(property/@ishidden='1')">
                <!-- container anlegen, elemente für massbezeichnung, wert und masseinheit sowie für ergänzungen einfügen -->
                <abmessung>
                    <mass><xsl:value-of select="property/name"/><xsl:text>&#x20;</xsl:text></mass>
                    <wert><xsl:value-of select="translate(value,'-', '‒')"/></wert>
                    <masseinheit><xsl:text>&#x20;</xsl:text><xsl:value-of select="property/unit"/></masseinheit>
                    <xsl:for-each select="content">
                        <ergaenzung><xsl:apply-templates></xsl:apply-templates></ergaenzung>
                    </xsl:for-each>
                </abmessung>
            </xsl:if>
    </xsl:for-each>
    </xsl:param>
    
    <!-- zweiter schritt: gleichartige massangaben (angaben mit derselben maß-bezeichnung/kategorie) werden zusammengezogen -->
    <xsl:param name="p_abmessungen2">
        <!-- das erste maß derselben kategorie wird aufgesucht, ein element <item> wird angelegt-->
        <xsl:for-each select="$p_abmessungen1/abmessung[not(mass=preceding-sibling::abmessung/mass)]">
        <item>
            <!-- die maßbezeichnung/kategorie wird eingefügt -->
           <xsl:copy-of select="mass"/>
            <!-- ein container für die verschiedenen werte wird angelegt -->
           <werte>
               <!-- die werte werden ausgelesen, gegebenenfalls ergänzungen in einem benannten template verarbeitet -->
               <!-- für den ersten wert -->
               <wert>
                   <xsl:value-of select="wert/text()"/>
                   <xsl:value-of select="masseinheit"/>
                   <!-- wenn der wert mit einer fußnote versehen ist, wird die fußnote nach der maßeinheit angezeigt -->
                   <xsl:apply-templates select="wert/app1"/>
                   <xsl:call-template name="ergaenzung"/>
               </wert>
               <!-- für alle weiteren werte -->
               <xsl:for-each select="following-sibling::abmessung[mass=current()/mass]">
                   <wert>
                       <xsl:value-of select="wert/text()"/>
                       <xsl:value-of select="masseinheit"/>
                       <!-- wenn der wert mit einer fußnote versehen ist, wird die fußnote nach der maßeinheit angezeigt -->
                       <xsl:apply-templates select="wert/app1"/>
                       <xsl:call-template name="ergaenzung"/>
                   </wert>
               </xsl:for-each>
           </werte>
        </item>
    </xsl:for-each>
   </xsl:param>
    
    <!-- parametertest
    <abmessungen1><xsl:copy-of select="$abmessungen1"/></abmessungen1>
    <abmessungen2><xsl:copy-of select="$abmessungen2"/></abmessungen2>
    -->
    <xsl:choose>
        <!-- wenn dafür ein eigener abschnitt angelegt wurde, wird dieser angesteuert -->
        <xsl:when test="sections/section[@norm_iri='di_generals_measure']">
            <xsl:for-each select="sections/section[@norm_iri='di_generals_measure']/items/item[@itemtype='text']/content">
                <di_generals_measure><xsl:apply-templates select="."/></di_generals_measure>
            </xsl:for-each>
        </xsl:when>
        <!-- anderenfalls werden sie hier ermittelt -->
        <xsl:otherwise>
            <!-- wenn maßangaben vorhanden sind, wird ein element <di_generals_measure> angelegt -->
            <xsl:if test="$p_abmessungen2/item[*]">
                <di_generals_measure>
                    <!-- wenn maßangaben mit einer maßbzeichnung/kategorie vorhanden sind, werden sie angesteuert -->
                    <xsl:if test="$p_abmessungen2/item/mass[normalize-space()!='']">
                        <xsl:for-each select="$p_abmessungen2/item">
                            <!-- die maßbezeichnung wird eingelesen -->
                            <xsl:value-of select="mass"/>
                            <!-- jeder wert wird einzeln angesteuert, formatiert und kommasepariert von den folgenden werten eingefügt -->
                            <xsl:for-each select="werte/wert">
                                <xsl:apply-templates/>
                                <xsl:if test="following-sibling::wert"><xsl:text>,&#x20;</xsl:text></xsl:if>
                            </xsl:for-each>
                            <!-- wenn es mehrere massangaben (kategorien) gibt, werden sie durch kommata getrennt -->
                            <xsl:if test="following-sibling::item"><xsl:text>,&#x20;</xsl:text></xsl:if>
                        </xsl:for-each>
                        <!-- entfällt künftig: zum schluss wird ein punkt gesetzt 
                        <xsl:text>.</xsl:text>-->
                    </xsl:if>
                </di_generals_measure>
            </xsl:if>            
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>

<xsl:template name="ergaenzung">
    <!-- formatierung von ergänzungen -->
    <!-- wenn eine ergänzung vorliegt, wird sie in runden klammern formatiert ausgegeben -->
    <xsl:if test="ergaenzung[node()]">
        <xsl:text>&#x20;(</xsl:text><xsl:apply-templates select="ergaenzung"/><xsl:text>)</xsl:text> 
    </xsl:if>    
</xsl:template>

</xsl:stylesheet>








