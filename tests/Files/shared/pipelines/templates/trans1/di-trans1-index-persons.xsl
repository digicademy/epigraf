<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:math="http://www.w3.org/2005/xpath-functions/math"
    exclude-result-prefixes="xs math"
    version="2.0">
    
    <xsl:import href="di-trans1-indices-commons.xsl"/>
    <xsl:output indent="no"/>
    
    <xsl:template name="index-persons">
        <xsl:param name="p_persons1">
             <!-- einträge bei denen das lemma eine ordnungszahl in geschweiften klammern enthält, 
                 werden gruppiert und im folgenden parameter neu nummeriert  -->
             <xsl:for-each select="properties/property">                 
                 <xsl:choose>
                     <!-- wenn das lemma eine ordnungszahl in geschweiften klammern enthält -->
                     <xsl:when test="contains(lemma,'{')">
                         
                         <!-- normalisiertes lemma ohne geschweifte klammern erzeugen und parent_id merken -->
                         <xsl:variable name="v_lemma_plain" select="replace(lemma,'\{[0-9]+\}', '{}')" />
                         <xsl:variable name="v_parent_id" select="@parent_id"/>
                         
                         <!-- 
                             das erste gleichlautende lemma ohne ordungszahl mit gleichem parent ansteuern, 
                             eine gruppe bilden  
                         -->
                         <xsl:if test="not(preceding-sibling::property
                             [(replace(lemma,'\{[0-9]+\}', '{}')=$v_lemma_plain) and ((@parent_id=$v_parent_id) or (not(@parent_id) and not($v_parent_id)))]
                         )">                    
                             <group>
                                 <xsl:copy-of select="@*" />
                                 
                                 <!-- 
                                     alle einträge mit gleichlautendem lemma ohne ordnungszahl aufsuchen
                                     und neu anlegen
                                 -->
                                 <xsl:for-each select="ancestor::index//property
                                     [replace(lemma,'\{[0-9]+\}', '{}')=$v_lemma_plain]
                                     [(@parent_id=$v_parent_id) or (not(@parent_id) and not($v_parent_id))]
                                     ">
                                     <property log1="p40">
                                         <xsl:copy-of select="@*"/>
                                         <xsl:for-each select="*">
                                             <xsl:choose>
                                                 <xsl:when test="self::lemma">
                                                     <lemma>
                                                         <xsl:copy-of select="@*" />
                                                         <xsl:value-of select="$v_lemma_plain"/>
                                                     </lemma>
                                                   </xsl:when>
                                                 <xsl:otherwise>
                                                     <xsl:copy-of select="." />
                                                 </xsl:otherwise>
                                             </xsl:choose>
                                         </xsl:for-each>
                                     </property>
                                 </xsl:for-each>                            
                             </group>
                         </xsl:if>
                     </xsl:when>
                     <!-- wenn das lemma keine ordnungszahl in geschweiften klammern enthält: kopieren -->
                     <xsl:otherwise>
                         <xsl:copy-of select="."></xsl:copy-of>
                     </xsl:otherwise>
                 </xsl:choose>
             </xsl:for-each>
        </xsl:param> 
        
        <xsl:param name="p_persons2">
              <properties>
                <xsl:for-each select="$p_persons1/*">

                <xsl:choose>
                    <!-- bei im vorigen parameter gruppierten wappen werden die ordungsnummern neu erzeugt -->
                    <xsl:when test="self::group">
                        <!-- gruppen ansteuern -->
                        <xsl:for-each select="*">
                            <xsl:variable name="newlemma">
                                <xsl:choose>
                                    <!-- wenn die gruppe mehr als einen eintrag enthält, erhält jeder eintrag eine positionsnummer -->
                                    <xsl:when test="following-sibling::* or preceding-sibling::*">                                        
                                        <xsl:value-of select="normalize-space(replace(lemma,'\{\}',concat('(',position(),')')))" />
                                    </xsl:when>
                                    <!-- sonst werden die geschweiften klammern entfernt -->
                                    <xsl:otherwise>
                                        <xsl:value-of select="normalize-space(replace(lemma,'\{\}',''))" />
                                    </xsl:otherwise>
                                </xsl:choose>
                            </xsl:variable>
                            
                            <property log1="p41">
                                <xsl:attribute name="position" select="position()"/>
                                <xsl:copy-of select="@*"/>
                                <lemma>
                                    <xsl:copy-of select="@*"></xsl:copy-of>
                                    <xsl:value-of select="$newlemma"/>
                                </lemma>
                                <xsl:for-each select="*[not(self::lemma)]"><xsl:copy-of select="." /></xsl:for-each>
                            </property>                                  
                                
                        </xsl:for-each>
                    </xsl:when>
                    <!-- <property>s außerhalb von gruppen werden kopiert -->
                    <xsl:otherwise>
                        <xsl:copy-of select="."></xsl:copy-of>
                    </xsl:otherwise>
                </xsl:choose>
                
            </xsl:for-each>
            </properties>
        </xsl:param> 
        
        <xsl:for-each select="$p_persons2">
            <xsl:call-template name="index-restruct" />
        </xsl:for-each>
        
<!--        <parametertest1><xsl:copy-of select="$p_persons1"></xsl:copy-of></parametertest1>
        <parametertest2><xsl:copy-of select="$p_persons2"></xsl:copy-of></parametertest2>-->
        
    </xsl:template>
</xsl:stylesheet>