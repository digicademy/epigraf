<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
     xmlns:php="http://php.net/xsl"
    >


    <!-- inschriftenliste = chronologische liste aller inschriften -->
    <xsl:template name="inschriftenliste">
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <!-- es wird die entsprechende titelseite in den textbausteinen des bandes angesteuert,
        der titel und gegebenenfalls die vorbemerkung ausgelesen-->
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <inschriftenliste>
                <xsl:copy-of select="@*"/>
                <xsl:attribute name="indizieren">1</xsl:attribute><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                    <xsl:choose>
                        <xsl:when test="items/item/content/wunsch[@tagname='Titel']">
                            <titel><xsl:value-of select="items/item/content/wunsch[@tagname='Titel']"/></titel>
                            
                        </xsl:when>
                        <xsl:otherwise><titel><xsl:value-of select="@name"/></titel></xsl:otherwise>
                    </xsl:choose>
                <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                <xsl:apply-templates select="items/item/content"></xsl:apply-templates>
                <xsl:call-template name="inschriften_sammeln"></xsl:call-template>
           </inschriftenliste>
    </xsl:template>
    
    <xsl:template name="inschriften_sammeln">
        <!-- inschriftenliste zusammenstellen -->
        <xsl:param name="inschriftenliste1">
                 <!-- alle articles ansteuern<test>inschriftenliste1</test> -->
                <xsl:for-each select="ancestor::book/catalog/article/sections">
                    <xsl:variable name="objekttyp"><xsl:value-of select="section[@sectiontype='objecttypes']/items/item/property/name"/></xsl:variable>
                    <xsl:variable name="objekttypID"><xsl:value-of select="section[@sectiontype='objecttypes']/items/item/property/@id"/></xsl:variable>
                    <xsl:variable name="artikelnummer"><xsl:number count="article" from="catalog" format="1" /></xsl:variable>
                    <xsl:variable name="artikelID"><xsl:value-of select="@id"/></xsl:variable>
                    <xsl:variable name="inschriften_total" select="count(section[@sectiontype='inscription'])"></xsl:variable>
                    <!-- alle inschriften ansteuern -->
                    <xsl:for-each select="section[@sectiontype='inscription']">
                        <xsl:variable name="inschrift_number"><xsl:value-of select="@number"/></xsl:variable>
                        <xsl:variable name="inschrift_caption"><xsl:value-of select="@name"/></xsl:variable>
                        <xsl:variable name="inschriftID"><xsl:value-of select="@id"/></xsl:variable>
                        <xsl:variable name="inschrifteile_total" select="count(section)"></xsl:variable>
                        <xsl:choose>
                            <!-- nummerierungen übergehen -->
                            <xsl:when test="items/item/property/lemma[text()='Nummerierung']"></xsl:when>
                            <xsl:otherwise>
                                <!-- inschriftteil ansteuern -->
                                <xsl:for-each select="section">
                                    <xsl:variable name="inschriftteil_caption"><xsl:value-of select="@name"/></xsl:variable>
                                    <xsl:variable name="inschriftteilID"><xsl:value-of select="@id"/></xsl:variable>
                                    <xsl:variable name="bearbeitungen_total" select="count(section)"></xsl:variable>
                                 <!-- bearbeitungen ansteuern -->
                                 <xsl:for-each select="section[1]">
                                     <!-- nach datierungsindex sortieren -->
                                     <!-- nicht datierte inschriften übergehen -->
                                     <xsl:if test="items/item/date_sort[node()]">
                                         <item><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                                             <artikelnummer>
                                                 <xsl:attribute name="targetID"><xsl:value-of select="$artikelID"/></xsl:attribute>
                                                 <xsl:value-of select="$artikelnummer"/>
                                             </artikelnummer>
                                             <inschriftnummer>
                                                 <xsl:attribute name="targetID"><xsl:value-of select="$inschriftID"/></xsl:attribute>
                                                 <xsl:attribute name="inschriften_total"><xsl:value-of select="$inschriften_total"/></xsl:attribute>
                                                 <xsl:attribute name="number"><xsl:value-of select="$inschrift_number"/></xsl:attribute>
                                                 <xsl:value-of select="$inschrift_caption"/>
                                             </inschriftnummer>
                                             <inschriftteilnummer>
                                                 <xsl:attribute name="targetID"><xsl:value-of select="$inschriftteilID"/></xsl:attribute>
                                                 <xsl:attribute name="inschriftteile_total"><xsl:value-of select="$inschrifteile_total"/></xsl:attribute>
                                                 <xsl:value-of select="$inschriftteil_caption"/>
                                             </inschriftteilnummer>
                                             <objekttyp>
                                                 <xsl:attribute name="targetID"><xsl:value-of select="$objekttypID"/></xsl:attribute>
<xsl:value-of select="ancestor::article/sections/section[@sectiontype='objecttypes']/items/item[1]/property[1]/name[1]"/>
                                               <!--  <xsl:value-of select="ancestor::book/indices/index[@propertytype='objecttypes']/item[.//@id=$objekttypID]/lemma"/>-->
                                                 <!--<xsl:value-of select="$objekttyp"/>-->
                                             </objekttyp>
                                             <datierung><xsl:value-of select="items/item/date_value"/></datierung>
                                             <datierungsindex><xsl:value-of select="items/item/date_sort"/></datierungsindex>
                                         </item><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                                     </xsl:if>
                                 </xsl:for-each>
                                </xsl:for-each>
                            </xsl:otherwise>
                        </xsl:choose>
                    </xsl:for-each>
                </xsl:for-each>
         </xsl:param>
        
        <!-- die liste chronologisch sortieren -->
        <xsl:param name="inschriftenliste2">
            <xsl:for-each select="$inschriftenliste1/item">
                <xsl:sort select="datierungsindex"/>
                <xsl:copy-of select="."/>
            </xsl:for-each>
        </xsl:param>
        
        <!-- innerhalb der liste nach datierung gruppieren  -->
        <xsl:param name="inschriftenliste3">
            <xsl:for-each select="$inschriftenliste2/item">
                <xsl:if test="not(datierung = preceding-sibling::item/datierung)">
                    <datierung>
                        <xsl:copy-of select="datierung"/>
                        <objekt>
                            <xsl:copy-of select="artikelnummer"/>
                            <xsl:copy-of select="inschriftnummer"/>
                            <xsl:copy-of select="inschriftteilnummer"/>
                            <xsl:copy-of select="objekttyp"/>
                        </objekt>
                        <xsl:for-each select="following-sibling::item[datierung = current()/datierung]">
                        <objekt>
                            <xsl:copy-of select="artikelnummer"/>
                            <xsl:copy-of select="inschriftnummer"/>
                            <xsl:copy-of select="inschriftteilnummer"/>
                            <xsl:copy-of select="objekttyp"/>
                        </objekt>                            
                        </xsl:for-each>
                    </datierung>
                </xsl:if>
            </xsl:for-each>
        </xsl:param>
        
        <!-- innerhalb der liste nach objekttypen gruppieren -->
        <xsl:param name="inschriftenliste4">
            <xsl:for-each select="$inschriftenliste3/datierung">
                <datierung>
                    <xsl:copy-of select="datierung"/>
                    <xsl:for-each select="objekt">
                        <xsl:if test="not(objekttyp = preceding-sibling::objekt/objekttyp)">
                        <objekt>
                            <xsl:copy-of select="objekttyp"/>
                            <article>
                            <xsl:copy-of select="artikelnummer"/>
                            <xsl:copy-of select="inschriftnummer"/>
                            <xsl:copy-of select="inschriftteilnummer"/> 
                            </article>
                            <xsl:for-each select="following-sibling::objekt[objekttyp = current()/objekttyp]">
                            <article>
                            <xsl:copy-of select="artikelnummer"/>
                            <xsl:copy-of select="inschriftnummer"/>
                            <xsl:copy-of select="inschriftteilnummer"/> 
                            </article>
                            </xsl:for-each>
                        </objekt>
                        </xsl:if>
                    </xsl:for-each>
                </datierung>
            </xsl:for-each>
        </xsl:param>
        
        <xsl:param name="inschriftenliste5">
            <xsl:for-each select="$inschriftenliste4/datierung">
                <datierung>
                <xsl:copy-of select="datierung"/>
                <xsl:for-each select="objekt">
                    <objekt>
                        <xsl:copy-of select="objekttyp"/>
                        
                            <xsl:for-each select="article[not(artikelnummer = preceding-sibling::article/artikelnummer)]">
                                <article>
                                    <xsl:copy-of select="artikelnummer"/>
                                    <inschriften>
                                        <xsl:copy-of select="inschriftnummer"/>
                                        <inschriftteile>
                                            <xsl:copy-of select="inschriftteilnummer"/>
                                        </inschriftteile>
                                    </inschriften>
                                    <xsl:for-each select="following-sibling::article[artikelnummer = current()/artikelnummer]">
                                    <inschriften>
                                        <xsl:copy-of select="inschriftnummer"/>
                                        <inschriftteile>
                                            <xsl:copy-of select="inschriftteilnummer"/>
                                        </inschriftteile>
                                    </inschriften>                                        
                                    </xsl:for-each>                                    
                                </article>
                                
                            </xsl:for-each>
                        
                    </objekt>
                </xsl:for-each>
                </datierung>
            </xsl:for-each>
        </xsl:param>
        
        <xsl:param name="inschriftenliste6">
            <xsl:for-each select="$inschriftenliste5/datierung">
                <datierung>
                    <xsl:copy-of select="datierung"/>
                    <xsl:for-each select="objekt">
                        <objekt>
                            <xsl:copy-of select="objekttyp"/>

                               <xsl:for-each select="article">
                                   <article>
                                       <xsl:copy-of select="artikelnummer"/>
                                       
                                           <xsl:for-each select="inschriften[not(inschriftnummer = preceding-sibling::inschriften/inschriftnummer)]">
                                               <inschrift>
                                                   <xsl:copy-of select="inschriftnummer"/>
                                                  
                                                   <xsl:copy-of select="inschriftteile/*"/>
                                                   <xsl:for-each select="following-sibling::inschriften[inschriftnummer = current()/inschriftnummer]">
                                                          <xsl:copy-of select="inschriftteile/*"/>
                                                    </xsl:for-each>                                                         
                                                   
 
                                               </inschrift>
                                            </xsl:for-each>
                                       
                                   </article>
                               </xsl:for-each> 
                            
                        </objekt>
                    </xsl:for-each>
                </datierung>                
            </xsl:for-each>
        </xsl:param>
        
        
        <!-- reduzieren auf artikelnummer wenn alle inschriften eines artikels unter derselben datierung erfasst sind, inschriftenteile weglassen -->
        <xsl:param name="inschriftenliste7">
            <xsl:for-each select="$inschriftenliste6/datierung">
                <datierung>
                    <xsl:copy-of select="datierung"/>
                    <xsl:for-each select="objekt">
                        <objekt>
                            <xsl:copy-of select="objekttyp"/>
                            <xsl:for-each select="article">
                                <xsl:variable name="inschriften_total"><xsl:value-of select="inschrift/inschriftnummer/@inschriften_total"/></xsl:variable>
                                <xsl:variable name="inschriften_ist"><xsl:value-of select="count(inschrift)"/></xsl:variable>
                                   <article>
                                       <xsl:copy-of select="artikelnummer"/>
                                       <xsl:if test="$inschriften_total != $inschriften_ist">
                                         <xsl:for-each select="inschrift">
                                             <xsl:copy>
                                                 <xsl:copy-of select="inschriftnummer"/>
                                             </xsl:copy>
                                         </xsl:for-each>  
                                       </xsl:if>
                                   </article>
                               </xsl:for-each> 
                        </objekt>
                    </xsl:for-each>
                </datierung>                
            </xsl:for-each>
        </xsl:param>
        
        <!-- aufeinanderfolgende inschriftennummern in sequenzen zusammenfassen -->
        <xsl:param name="inschriftenliste8">
            <xsl:for-each select="$inschriftenliste7/datierung">
                <datierung>
                    <xsl:copy-of select="datierung"/>
                    <xsl:for-each select="objekt">
                        <objekt>
                            <xsl:copy-of select="objekttyp"/>
                            <xsl:for-each select="article">
                                <xsl:variable name="inschriften_total"><xsl:value-of select="inschrift/inschriftnummer/@inschriften_total"/></xsl:variable>
                                <xsl:variable name="inschriften_ist"><xsl:value-of select="count(inschrift)"/></xsl:variable>
                                   <article>
                                       <xsl:copy-of select="artikelnummer"/>
                                       <xsl:if test="inschrift">
                                            <xsl:call-template name="inschriften_sequenzieren"></xsl:call-template>
                                       </xsl:if>
                                   </article>
                               </xsl:for-each> 
                        </objekt>
                    </xsl:for-each>
                </datierung>                
            </xsl:for-each>            
        </xsl:param>
        
        <items><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<!--<param1><xsl:copy-of select="$inschriftenliste1"/></param1>     
<param6><xsl:copy-of select="$inschriftenliste6"/></param6>     
<param7><xsl:copy-of select="$inschriftenliste7"/></param7>-->

           <xsl:copy-of select="$inschriftenliste8"/>
        </items><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    </xsl:template>


<xsl:template name="inschriften_sequenzieren">
    <sequenz>
    <xsl:for-each select="inschrift">
        <xsl:variable name="self_number"><xsl:value-of select="inschriftnummer/@number"/></xsl:variable>
        <xsl:variable name="following_number"><xsl:value-of select="following-sibling::*[1]/inschriftnummer/@number"/></xsl:variable>
            <xsl:copy-of select="inschriftnummer"/>
<xsl:if test="not(position()=last())">
            <xsl:if test="$self_number + 1 != $following_number">
                <xsl:text disable-output-escaping="yes">&lt;/sequenz&gt;&lt;sequenz&gt;</xsl:text>
            </xsl:if>
</xsl:if>



    </xsl:for-each>
    </sequenz>    
</xsl:template>







</xsl:stylesheet>
