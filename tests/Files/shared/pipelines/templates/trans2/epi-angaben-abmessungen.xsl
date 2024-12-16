<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
>

<xsl:template name="abmessungen">

    <!-- erster schritt: die einzelnen massangaben werden ausgelesen, nach der im datensatz angezeigten reihenfolge sortiert
         und in einem komprimierten elemente-baum abgelegt -->
    <xsl:param name="abmessungen1">
        <xsl:for-each select="sections/section[@sectiontype='measures']/items/item">
         <xsl:variable name="v_target_unit_id"><xsl:value-of select="property[@segment='measures']/@id"/></xsl:variable>
        <!-- sortierung nach dem attribut @sortno -->
<!--        <xsl:sort select="@sortno"/>-->
            <!-- mit einer blockade ■ markierte angaben werden von der ausgabe ausgeschlossen -->
            <xsl:if test="not(contains(property/lemma,'■'))">
        <abmessung>
            <mass><xsl:value-of select="property/lemma"/><xsl:text>&#x20;</xsl:text></mass>
            <wert><xsl:apply-templates select="value"/></wert>
            <masseinheit><xsl:text>&#x20;</xsl:text><xsl:value-of select="property/unit"/></masseinheit>
            <xsl:for-each select="content">
                <ergaenzung><xsl:apply-templates></xsl:apply-templates></ergaenzung>
            </xsl:for-each>
        </abmessung>
            </xsl:if>
    </xsl:for-each>
    </xsl:param>

    <!-- zweiter schritt: gleichartige massangaben werden zusammengezogen -->
    <xsl:param name="abmessungen2">
    <xsl:for-each select="$abmessungen1/abmessung[not(mass=preceding-sibling::abmessung/mass)]">
        <item>
           <xsl:copy-of select="mass"/>
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

<!--<abmessungen1><xsl:copy-of select="$abmessungen1"/></abmessungen1>
<abmessungen2><xsl:copy-of select="$abmessungen2"/></abmessungen2>-->

    <xsl:if test="$abmessungen2/item[*]">
        <abmessungen>
            <xsl:for-each select="$abmessungen2/item">
                <xsl:value-of select="mass"/>
                <xsl:for-each select="werte/wert">
                    <xsl:apply-templates/>
                    <xsl:if test="following-sibling::wert"><xsl:text>,&#x20;</xsl:text></xsl:if>
                </xsl:for-each>
                <xsl:if test="following-sibling::item"><xsl:text>,&#x20;</xsl:text></xsl:if>
            </xsl:for-each>
            <xsl:text>.</xsl:text>
        </abmessungen>
    </xsl:if>
</xsl:template>

<xsl:template name="ergaenzung">
    <xsl:choose>
        <xsl:when test="ergaenzung[node()]">
            <xsl:text>&#x20;(</xsl:text><xsl:apply-templates select="ergaenzung"/><xsl:text>)</xsl:text>
        </xsl:when>
        <xsl:otherwise><xsl:apply-templates select="ergaenzung"/></xsl:otherwise>
    </xsl:choose>
<!--    <xsl:if test="ergaenzung[*|text()]">
        <xsl:text>&#x20;(</xsl:text><xsl:value-of select="ergaenzung/text()" /><xsl:text>)</xsl:text><xsl:apply-templates select="ergaenzung/app1"/>
    </xsl:if>   -->
</xsl:template>
</xsl:stylesheet>








