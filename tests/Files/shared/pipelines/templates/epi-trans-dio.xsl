<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
xmlns:fo="http://www.w3.org/1999/XSL/Format" 
xmlns:math="http://exslt.org/math"
xmlns:uuid="http://www.uuid.org" 
 exclude-result-prefixes="fo">
<xsl:import href="trans-dio/epi-dio-ligaturen.xsl"/>
<xsl:import href="trans-dio/epi-dio-insec.xsl"/> 
<xsl:import href="trans-dio/epi-dio-generate_uuid.xsl"><!-- das aufrufen erfolgt über  select="uuid:get-uuid(drinkName)"; statt drinkName muss ein relevanter pfad (oder eine variable) angegeben werden--></xsl:import> 
<xsl:import href="trans-dio/epi-dio-register.xsl"/> 
<xsl:import href="commons/epi-switch.xsl"/>


<!--<xsl:output  method="text" version="1.1" omit-xml-declaration="yes" indent="no" encoding="UTF-8"/>-->
  
    <xsl:param name="dio_signatur"><xsl:value-of select="book/project/dio_signature"/></xsl:param>
    <xsl:param name="volume_number"><xsl:value-of select="book/project/di_number"/></xsl:param>
    <xsl:param name="volume_number_dreistellig">
        <xsl:choose>
            <xsl:when test="book/project/di_number/text()">
                <xsl:number value="book/project/di_number" format="001"></xsl:number>
            </xsl:when>
            <xsl:otherwise><xsl:text>000</xsl:text></xsl:otherwise>
        </xsl:choose>
    </xsl:param>
    <xsl:param name="di-type">di</xsl:param><!-- di oder dio; ist hier manuell anzugeben, perspektivisch sollte die angabe aus den kopfdaten des bandartikels auszulesen sein  -->

<xsl:template match="book">
  <!--<est><xsl:for-each select="*"><xsl:copy-of select="."/></xsl:for-each></est>-->
  <xsl:param name="dio_signatur"><xsl:value-of select="project/dio_signatur"/></xsl:param>
  <xsl:param name="di_volume"><xsl:value-of select="substring-before($dio_signatur, 'g')"/></xsl:param>
<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text> 


 <dio_volume>
   <!-- <parametertest-di-nr><xsl:value-of select="$volume_number_dreistellig"/></parametertest-di-nr>-->
   <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
   <dio_katalog>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
   <dio_table>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
     <xsl:for-each select="catalog/article">
      <xsl:apply-templates select=".">
        <!--<xsl:with-param name="dio_signatur"><xsl:value-of select="$dio_signatur"/></xsl:with-param>-->
      </xsl:apply-templates>
     </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
   </dio_table>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
  </dio_katalog>
   <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
   <dio_vorwort>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <xsl:apply-templates select ="vorwort"/>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
  </dio_vorwort>   
   <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
   <dio_einleitung>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <xsl:apply-templates select ="einleitung"/>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>     
  </dio_einleitung>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
   <dio_abkuerzungen>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <xsl:apply-templates select ="abkuerzungen"/>
  </dio_abkuerzungen>
   <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
   <dio_literatur>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <xsl:apply-templates select ="quellen_literatur"/>
   </dio_literatur>
   <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
   <dio_wappen>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
     <xsl:call-template name="blasonierungen"></xsl:call-template>   
   </dio_wappen>
   <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
   <dio_register>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
     <xsl:apply-templates select ="indices"/>   
   </dio_register>
   <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
   <dio_zeichnungen>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
     <xsl:apply-templates select ="zeichnungen"/>
   </dio_zeichnungen>
<!--   <dio_marks>
     <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
     <xsl:apply-templates select ="//abschnitt[@name='marken']"/>
   </dio_marks>-->
   <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
 </dio_volume>
  <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>  
</xsl:template>


<xsl:template match="article">
    <xsl:param name="article-number"><xsl:number value="@nr" format="0001"></xsl:number></xsl:param>
    <xsl:param name="preID"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number_dreistellig"/>-<xsl:value-of select="$article-number"/></xsl:param>
  <dio_record>
  <xsl:attribute name="signatur"><xsl:value-of select="kopfzeile/artikelnummer"/></xsl:attribute>
  <xsl:attribute name="index"><xsl:value-of select="kopfzeile/artikelnummer"/></xsl:attribute>
  <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <record_header><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <tx_hisodat_signature><xsl:value-of select="kopfzeile/artikelnummer"/></tx_hisodat_signature><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <tx_hisodat_signature_add><xsl:value-of select="$dio_signatur"/></tx_hisodat_signature_add><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <tx_dio_preservation>
          <xsl:for-each select="kopfzeile/trad_index">
           <xsl:choose>
            <xsl:when test="contains(., 'traditio1')">
              <xsl:text>0</xsl:text>
            </xsl:when>
            <xsl:when test="contains(., 'traditio2')">
              <xsl:text>1</xsl:text>
            </xsl:when>
            <xsl:when test="contains(., 'traditio3') or contains(., 'traditio4') or contains(., 'traditio5')">
              <xsl:text>2</xsl:text>
            </xsl:when>
            <xsl:when test="contains(., 'traditio6')">
              <xsl:text>3</xsl:text>
            </xsl:when>
            <xsl:otherwise>
              <xsl:text>?</xsl:text>
            </xsl:otherwise>
          </xsl:choose>  
          </xsl:for-each>    
    		</tx_dio_preservation><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

        <tx_dio_title>
          <xsl:for-each select="kopfzeile/standorte/standort">
            <xsl:value-of select="."/>
          </xsl:for-each>
        </tx_dio_title><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

        <tx_hisodat_date_start>
    		  <datanfang><xsl:value-of select="kopfzeile/datierung_start" /></datanfang>
        </tx_hisodat_date_start><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		
        <tx_hisodat_date_end><datende><xsl:value-of select="kopfzeile/datierung_end" /></datende></tx_hisodat_date_end><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <tx_hisodat_date_sorting><xsl:value-of select="kopfzeile/datierung_index" /></tx_hisodat_date_sorting><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <tx_hisodat_date_comment><xsl:value-of select="kopfzeile/datierung" /></tx_hisodat_date_comment><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    </record_header><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
	
   <record_body><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		<tx_hisodat_short>
      <xsl:apply-templates select="beschreibung"></xsl:apply-templates>
		</tx_hisodat_short><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

    <tx_dio_addinfos>
      <xsl:if test="angaben/inschrift_nach"><xsl:apply-templates select="angaben/inschrift_nach"/>
      <xsl:if test="angaben/ergaenzung_nach"><xsl:text> </xsl:text></xsl:if>
      </xsl:if>
      <xsl:if test="angaben/ergaenzung_nach"><xsl:apply-templates select="angaben/ergaenzung_nach"/></xsl:if>
    </tx_dio_addinfos><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		
     <tx_dio_dimensions>
		  <xsl:if test="angaben/abmessungen">
		    <xsl:apply-templates select="angaben/abmessungen"/>
		    <xsl:if test="angaben/schrifthoehen"><xsl:text> </xsl:text></xsl:if>
		  </xsl:if>
		  <xsl:if test="angaben/schrifthoehen"><xsl:apply-templates select="angaben/schrifthoehen"/></xsl:if>		  
		</tx_dio_dimensions><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		<tx_dio_typeface>
      <xsl:if test="angaben/schriftarten"><xsl:apply-templates select="angaben/schriftarten"/></xsl:if>		
		</tx_dio_typeface><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		
	  <tx_hisodat_sourcetext><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
	    <inscription><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
	     <sco><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
 
<xsl:for-each select="inschrift"><xsl:comment>ID der Inschrift: <xsl:copy-of select="@id"/></xsl:comment>
<xsl:for-each select="inschriftenteil"><xsl:comment>ID des Inschriftenteils: <xsl:copy-of select="@id"/></xsl:comment>
<xsl:for-each select="bearbeitung"><xsl:comment>ID der Bearbeitung (Version): <xsl:copy-of select="nr/@id"/></xsl:comment><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<sec><xsl:if test="$links_inscriptions=1"><xsl:attribute name="id"><xsl:value-of select="$preID"/>-<xsl:value-of select="@id"/></xsl:attribute></xsl:if><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <xsl:if test="nr[text()]">
                <snr>
                   <xsl:apply-templates select="nr"></xsl:apply-templates><xsl:apply-templates select="version"></xsl:apply-templates>
                </snr><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            </xsl:if>
            <par>
                <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                <xsl:for-each select="content/bl">
                  <xsl:choose>
                   <xsl:when test="@align='Spalten'">
                     <xsl:call-template name="spalten"/>
                   </xsl:when> 
                   <xsl:otherwise> 
                     <xsl:choose>
                      <xsl:when test="vz">
                        <xsl:for-each select="vz[node()]">
                          <xsl:apply-templates select="."/>
                        </xsl:for-each>
                      </xsl:when>
                      <xsl:otherwise>
                        <lno><xsl:apply-templates/></lno><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                     </xsl:otherwise>
                     </xsl:choose>
                   </xsl:otherwise>
                  </xsl:choose>
                </xsl:for-each>
            </par><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</sec><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>
</xsl:for-each>
</xsl:for-each>

<!-- erste variante mit subsections -->       
<!--<xsl:for-each select="inschrift">
<sec><xsl:attribute name="id"><xsl:value-of select="$preID"/>-<xsl:value-of select="@id"/></xsl:attribute><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<xsl:for-each select="inschriftenteil">
<subsec><xsl:attribute name="id"><xsl:value-of select="$preID"/>-<xsl:value-of select="@id"/></xsl:attribute><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<xsl:for-each select="bearbeitung">
<subsec><xsl:attribute name="id"><xsl:value-of select="$preID"/>-<xsl:value-of select="@id"/></xsl:attribute><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
	<snr>
	   <xsl:apply-templates select="nr"></xsl:apply-templates><xsl:apply-templates select="version"></xsl:apply-templates>
	</snr><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <par>
                <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                <xsl:for-each select="content/bl">
                  <xsl:choose>
                   <xsl:when test="bl">
                     <xsl:apply-templates select="bl[1]"/>
                    <xsl:for-each select="bl[1]">
                     <!-\-<xsl:call-template name="trans_zeilen"/>-\->
                    </xsl:for-each>
                   </xsl:when> 
                   <xsl:otherwise> 
                     <xsl:choose>
                      <xsl:when test="vz">
                        <xsl:for-each select="vz[node()]">
                          <xsl:apply-templates select="."/>
                        </xsl:for-each>
                      </xsl:when>
                      <xsl:otherwise>
                        <lno><xsl:apply-templates/></lno><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                     </xsl:otherwise>
                     </xsl:choose>
                   </xsl:otherwise>
                  </xsl:choose>
                </xsl:for-each>
            </par><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</subsec><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>
</subsec><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>
</sec><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>-->



<!--<xsl:for-each select="inschrift/inschriftenteil/bearbeitung">
          <sec>
            <xsl:attribute name="id"><xsl:value-of select="ancestor::inschrift/@id"/></xsl:attribute>
            <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
					<snr>
					  <xsl:attribute name="id"><xsl:value-of select="ancestor::inschriftenteil/@id"/></xsl:attribute>
					  <xsl:apply-templates select="nr"></xsl:apply-templates><xsl:apply-templates select="version"></xsl:apply-templates>
					</snr><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
           <par>
             <xsl:attribute name="id"><xsl:value-of select="@id"/></xsl:attribute>
             <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <xsl:for-each select="content/bl">
              <xsl:choose>
               <xsl:when test="bl">
                 <xsl:apply-templates select="bl[1]"/>
                <xsl:for-each select="bl[1]">
                 <!-\-<xsl:call-template name="trans_zeilen"/>-\->
                </xsl:for-each>
               </xsl:when> 
               <xsl:otherwise> 
                 <xsl:choose>
                  <xsl:when test="vz">
                    <xsl:for-each select="vz[node()]">
                      <xsl:apply-templates select="."/>
                    </xsl:for-each>
                  </xsl:when>
                  <xsl:otherwise>
                    <lno><xsl:apply-templates/></lno><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
                 </xsl:otherwise>
                 </xsl:choose>
               </xsl:otherwise>
              </xsl:choose>
            </xsl:for-each>
					</par><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
 				 </sec><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        </xsl:for-each>-->
			 </sco><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>			
			</inscription><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
	  </tx_hisodat_sourcetext><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		
    <tx_dio_orig_inscription>  
        <xsl:for-each select=".//bl">
            <xsl:for-each select=".//text()[not(ancestor::app1)][not(ancestor::app2)]"><xsl:value-of select="."/><xsl:text> </xsl:text></xsl:for-each>
        </xsl:for-each>
    </tx_dio_orig_inscription><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		
    <tx_dio_translation>
      <xsl:for-each select="uebersetzungen/uebersetzung">
        <xsl:if test="nr[node()]"> 
            <xsl:text>(</xsl:text>
            <xsl:choose>
                <xsl:when test="$links_inscriptions=1">
                    <ref><xsl:attribute name="target">#<xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number"/>-<xsl:value-of select="$article-number"/>-<xsl:value-of select="nr/@section-id"/></xsl:attribute>
            <!--            <xsl:for-each select="link">
                                  <xsl:text>(</xsl:text><xsl:apply-templates select="."></xsl:apply-templates><xsl:text>) </xsl:text>
                        </xsl:for-each>-->
                     <xsl:apply-templates select="nr"></xsl:apply-templates>
                      <xsl:apply-templates select="version"></xsl:apply-templates>
                    </ref>
                </xsl:when>
                <xsl:otherwise>
                     <xsl:apply-templates select="nr"></xsl:apply-templates>
                      <xsl:apply-templates select="version"></xsl:apply-templates>
                </xsl:otherwise>
            </xsl:choose>
            <xsl:text>) </xsl:text>
        </xsl:if>
        <xsl:apply-templates select="content" />
        <xsl:if test="following-sibling::uebersetzung">
          <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:if>
      </xsl:for-each>
    </tx_dio_translation><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		
		<tx_dio_dateoninscription>
 		     <xsl:for-each select="section[@kategorie='Datum in der Inschrift']"><xsl:apply-templates></xsl:apply-templates></xsl:for-each>
		</tx_dio_dateoninscription><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    
		<tx_dio_metre>
		  <xsl:for-each select="verse/item">
		    <xsl:value-of select="content"/><xsl:if test="nr"><xsl:text> (</xsl:text>
		      <xsl:for-each select="nr"><xsl:apply-templates select="."></xsl:apply-templates>
		      <xsl:if test="following-sibling::nr"><xsl:text>, </xsl:text></xsl:if>
		      </xsl:for-each>
		      <xsl:text>)</xsl:text></xsl:if><xsl:text>.</xsl:text>
		    <xsl:if test="following-sibling::item"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
		  </xsl:for-each>		  
		</tx_dio_metre><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		
	  <tx_dio_heraldry><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
	    <xsl:apply-templates select="heraldik"/>
	  </tx_dio_heraldry><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		
	  <tx_hisodat_description><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		  <xsl:for-each select="kommentar">
		    <xsl:apply-templates select="." />
		  </xsl:for-each>
	  </tx_hisodat_description><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		
	  <tx_dio_annotations><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <xsl:for-each select="apparate/buchstabenapparat/item">
         <!-- die id des entry-elements wird aus dem vorsatz "ants" (für annotations) und der id des item gebildet  -->
        <xsl:variable name="item_id"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number"/>-<xsl:value-of select="$article-number"/>-<xsl:value-of select="@itemID"/></xsl:variable>
        <xsl:variable name="target_id"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number"/>-<xsl:value-of select="$article-number"/>-pk<xsl:value-of select="@pkID"/></xsl:variable>
        <entry>
          <xsl:if test="$links_footnotes=1">
                <xsl:attribute name="id" select="$item_id"/>
                <xsl:attribute name="target">#<xsl:value-of select="$target_id"/></xsl:attribute>
            </xsl:if>
          <xsl:apply-templates/></entry><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      </xsl:for-each>
	  </tx_dio_annotations><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

	  <tx_dio_footnotes><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <xsl:for-each select="apparate/ziffernapparat/item">
            <!-- die id des entry-elements wird aus dem vorsatz "fnts" (für footnotes) und der id des item gebildet  -->
            <xsl:variable name="item_id"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number"/>-<xsl:value-of select="$article-number"/>-<xsl:value-of select="@itemID"/></xsl:variable>
        <xsl:variable name="target_id"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number"/>-<xsl:value-of select="$article-number"/>-pk<xsl:value-of select="@pkID"/></xsl:variable>
        <entry>
            <xsl:if test="$links_footnotes=1">
                <xsl:attribute name="id" select="$item_id"/>
                <xsl:attribute name="target">#<xsl:value-of select="$target_id"/></xsl:attribute></xsl:if>
            <xsl:apply-templates/></entry><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      </xsl:for-each>	
	  </tx_dio_footnotes><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
		
	  <tx_dio_literature><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <xsl:for-each select="nachweise/item">
			 <entry>
			   <xsl:apply-templates/>
			 </entry><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>       
      </xsl:for-each>
	  </tx_dio_literature><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

	  <tx_dio_corrigenda></tx_dio_corrigenda><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

	</record_body><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

 </dio_record><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:template>

<xsl:template match="signatur">
<xsl:choose>
 <xsl:when test="substring(.,1,1)='0'">
  <xsl:choose>
    <xsl:when test="substring(.,2,1)='0'">
     <xsl:value-of select="substring(.,3)" />
    </xsl:when>
    <xsl:otherwise>
     <xsl:value-of select="substring(.,2)" />
    </xsl:otherwise>
  </xsl:choose>
 </xsl:when>
 <xsl:otherwise>
  <xsl:value-of select="." />
 </xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template name="inhalt.einleitung">
</xsl:template>

<!--=========VORWORT / preface========================================================-->
<xsl:template match="vorwort" >
  <xsl:apply-templates/>
</xsl:template>

<!--==EINLEITUNG / Introduction======================================================-->
<xsl:template match="einleitung">
   <xsl:apply-templates/>
</xsl:template>

<xsl:template match="abschnitt[ancestor::einleitung]">
<xsl:variable name="level"><xsl:value-of select="@level"/></xsl:variable>
<xsl:if test="@kapitelnummer">
<xsl:if test="$level=1"><h3><xsl:value-of select="@titel"/></h3></xsl:if>
<xsl:if test="$level=2"><h4><xsl:value-of select="@titel"/></h4></xsl:if>
<xsl:if test="$level=3"><h5><xsl:value-of select="@titel"/></h5></xsl:if>
<xsl:if test="$level=4"><h6><xsl:value-of select="@titel"/></h6></xsl:if>
<xsl:if test="$level=5"><h7><xsl:value-of select="@titel"/></h7></xsl:if>
</xsl:if>
<xsl:apply-templates/>
</xsl:template>

<!--Absaetze in der Einleitung-->
<xsl:template match="p[ancestor::einleitung  or ancestor::vorwort][node()]">
  <xsl:if test="not(parent::app1)"><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text></xsl:if>
<xsl:choose>
    <xsl:when test="h"><xsl:apply-templates/></xsl:when><!-- p-tags die ein h-tag umschließen werden eliminiert, sodass nur das h-tag bleibt -->
    <xsl:otherwise><p><xsl:apply-templates/></p></xsl:otherwise>
</xsl:choose>
</xsl:template>

<!-- zwischenüberschriften in der einleitung -->
<xsl:template match="h">
    <xsl:param name="value"><xsl:value-of select="translate(@data-link-value,'H','h')"/></xsl:param>
<xsl:choose>
<xsl:when test="string-length(@data-link-value)!=0">
    <xsl:element name="{$value}"><xsl:apply-templates/></xsl:element>
</xsl:when>
<xsl:otherwise>
<xsl:comment>Warnung: die Überschrift wurde nicht spezifiziert</xsl:comment>
<h><xsl:apply-templates/></h>
</xsl:otherwise>
</xsl:choose>

</xsl:template>


<!-- hauptüberschriften zu einleitung und vorwort usw. -->
<xsl:template match="titel[parent::*/parent::book]">
  <h2><xsl:value-of select="."/></h2>
</xsl:template>

<!--Einleitungstitel/Ueberschriften-->
<!--<xsl:template match="abschnitt[@name='einleitung']//h">
<xsl:if test="@value='H1'">
<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<h2><xsl:apply-templates/></h2>
</xsl:if>
<xsl:if test="@value='H2'">
<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<h3><xsl:number level="multiple" count="abschnitt/abschnitt" format="1.1."/> <xsl:apply-templates/></h3>
</xsl:if>
<xsl:if test="@value='H3'">
<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<h4><xsl:number level="multiple" count="abschnitt/abschnitt" format="1.1."/> <xsl:apply-templates/></h4>
</xsl:if>

<xsl:if test="@value='H4z'">
<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<h5><xsl:number level="multiple" count="abschnitt/abschnitt" format="1.1."/> <xsl:apply-templates/></h5>
</xsl:if>
<xsl:if test="@value='H4'">
<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text><h5><xsl:apply-templates/></h5>
</xsl:if>
<xsl:if test="@value='H5'">
<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text><h6><xsl:apply-templates/></h6>
</xsl:if>
<xsl:if test="@value='H6'">
<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text><h7><xsl:apply-templates/></h7>
</xsl:if>
<xsl:if test="@value='H7'">
<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text><h8><xsl:apply-templates/></h8>
</xsl:if>
<xsl:if test="@value='H8'">
<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text><h9><xsl:apply-templates/></h9>
</xsl:if>

  <!-\-ausgeblendete Ueberschriften-\->
<xsl:if test="@value='H7x' or @value='H8x'"></xsl:if>

</xsl:template>-->

<!--die Bezeichnungen der Kapitel werden nicht ausgegeben-->
<xsl:template match="name[ancestor::abschnitt[@name='einleitung']]"></xsl:template>

<xsl:template match="abschnitt[@name='einleitung']//abschnitt">
<xsl:apply-templates/>
</xsl:template>



<xsl:template match="row">
<xsl:choose>
<!-- es werden nur zeilen verarbeitet, die zellen mit inhalt aufweisen -->
    <xsl:when test="cell/node()">
      <row><xsl:for-each select="cell"><cell><xsl:apply-templates/></cell></xsl:for-each></row>
    </xsl:when>
    <xsl:otherwise></xsl:otherwise>
</xsl:choose>
</xsl:template>


<!--Tabellen in den Einfuehrungstexten /  table in Introduction-->
<xsl:template match="wunsch[@tagname='Tabelle']"> 
<xsl:choose>
<!--drei Spalten-->
<xsl:when test="@Typ='Wappen'">
  <table>
      <xsl:for-each select="wunsch[@tagname='Zeile']">
       <xsl:choose>
         <xsl:when test="position()=last()">
             <xsl:if test="wunsch[string-length() != 1]">
               <row>
                <xsl:for-each select="wunsch[@tagname='Spalte']">
                  <cell>
                      <xsl:apply-templates select="."/>
                  </cell>
                </xsl:for-each>
              </row>
             </xsl:if>
         </xsl:when>
         <xsl:otherwise>
             <xsl:if test="wunsch[string-length() != 1]">
               <row>
                <xsl:for-each select="wunsch[@tagname='Spalte']">
                  <cell>
                      <xsl:apply-templates select="."/>
                  </cell>
                </xsl:for-each>
              </row>
             </xsl:if>
         </xsl:otherwise>
       </xsl:choose>    
      </xsl:for-each>
  </table>
</xsl:when> 

<!--zwei Spalten-->
<xsl:otherwise>
  <table>
     <xsl:for-each select="wunsch[@tagname='Zeile']">
        <xsl:choose>
          <xsl:when test="position()=last()">
           <xsl:if test="wunsch[string-length() != 1]">
             <row>
              <xsl:for-each select="wunsch[@tagname='Spalte'][not(position()&gt;2)]">
                <cell>
                    <xsl:apply-templates select="."/>
                </cell>
              </xsl:for-each>
            </row>
           </xsl:if>  
          </xsl:when>
          <xsl:otherwise>
           <xsl:if test="wunsch[string-length() != 1]">
             <row>
              <xsl:for-each select="wunsch[@tagname='Spalte'][not(position()&gt;2)]">
                <cell>
                    <xsl:apply-templates select="."/>
                </cell>
              </xsl:for-each>
            </row>
           </xsl:if>
          </xsl:otherwise>
        </xsl:choose>
     </xsl:for-each>
  </table>    
 </xsl:otherwise>
</xsl:choose>
</xsl:template>

 

<!--==ABKUERZUNGSVERZEICHNIS / List of Abbreviations===============================-->
<xsl:template match="abschnitt[@name='abkuerzungen']">
  <xsl:for-each select=".//*[@tagname='Zeile']">
    <xsl:value-of select="*[1]"/>|<xsl:value-of select="*[2]"/>
    <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
  </xsl:for-each>
</xsl:template>


<!--==QUELLEN- UND LITERATURVERZEICHNIS / Sources and Literature=====================-->
<xsl:template match="quellen_literatur">
<h2><xsl:value-of select="titel"/></h2>
<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<xsl:for-each select="abschnitt">
<group><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<!--<h3><xsl:apply-templates select="lemma"></xsl:apply-templates></h3><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
-->
<h3><xsl:for-each select="lemma"><xsl:apply-templates></xsl:apply-templates></xsl:for-each></h3><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<!--<xsl:for-each select="item">
<item>
<xsl:attribute name="id">di<xsl:value-of select="$volume_number"/>-lit<xsl:value-of select="substring-after(@id,'-')"/></xsl:attribute>
<xsl:apply-templates></xsl:apply-templates>
</item><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>-->
<ul><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<xsl:for-each select="item">
<li>
<xsl:choose>
    <xsl:when test="item">
        <xsl:for-each select="lemma"><h4><xsl:apply-templates ></xsl:apply-templates></h4><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text></xsl:for-each>
    </xsl:when>
    <xsl:otherwise><xsl:for-each select="lemma"><xsl:apply-templates ></xsl:apply-templates></xsl:for-each></xsl:otherwise>
</xsl:choose>
<xsl:if test="item">
<ul><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<xsl:for-each select="item">
<li>
<xsl:for-each select="lemma"><xsl:apply-templates ></xsl:apply-templates></xsl:for-each>
<xsl:if test="item">
<ul><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<xsl:for-each select="item">
<li><xsl:for-each select="lemma"><xsl:apply-templates ></xsl:apply-templates></xsl:for-each></li><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>
</ul><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:if>
</li><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>
</ul><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:if>
</li><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>
</ul><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<!--<xsl:for-each select="item">
<item>
<xsl:apply-templates></xsl:apply-templates>
</item><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>-->

</group><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>

</xsl:template>

  <!--Quellenliste-->
<xsl:template match="abschnitt[@name='quellen']">
<h3><xsl:value-of select="titel"/></h3><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
  
<!--1. die jeweils erste Archivalie eines Archivs ansteuern und das Archiv ausgeben-->
<xsl:for-each select=".//archivalie[not(archiv=preceding-sibling::archivalie/archiv)]">
<xsl:sort select="archiv" />

<h4><xsl:value-of select="archiv" /></h4><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>      
<xsl:if test="lt/*[not(self::urheber)][text()]">
<!--2. alle Archivalien des unter 1. ausgelesenen Archivs ansteuern und auslesen--> 
<ul><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<xsl:for-each select="//ancestor::archivalien/archivalie[(current()/archiv=archiv)]">
<xsl:sort select="lt/*[text()]" />
  <li><xsl:apply-templates select="lt" />.</li><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>
</ul><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:if>
</xsl:for-each>
    
</xsl:template>
  <!--Langtitel der Archivalie-->
<xsl:template match="archivalie/lt">
<xsl:for-each select="*[text()][not(self::urheber)]"><xsl:value-of select="." /><xsl:if test="not(position()=last())">,&#x0020;</xsl:if></xsl:for-each>
</xsl:template>

  <!--Literaturliste-->
<xsl:template match="abschnitt[@name='literatur']">
  <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
 <h3><xsl:value-of select="titel"/></h3>
  <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
 <h5><xsl:value-of select="literatur/titel/initial"/></h5>
  <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
  <ul><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
   <xsl:for-each select="literatur/titel">
     <xsl:sort select="kt" order="ascending" data-type="text" lang="de"/>
     <xsl:for-each select=".">
       <xsl:choose>
         <!-- wenn Wechsel des Initialbuchstabens -->
         <xsl:when test="initial!=preceding-sibling::titel[1]/initial">
         <xsl:text disable-output-escaping="yes">&lt;/ul&gt;&#x000D;&lt;H5&gt;</xsl:text><xsl:value-of select="initial"/><xsl:text disable-output-escaping="yes">&lt;/H5&gt;&#x000D;&lt;ul&gt;&#x000D;</xsl:text>
         <li><xsl:value-of select="kt"/><xsl:text> = </xsl:text>          
           <xsl:choose>
            <!--bei der Personenangabe ", u. a." wird das Komma eliminiert-->
            <xsl:when test="contains(child::lt,', u. a.')">
            <xsl:value-of select="substring-before(child::lt,', u. a.')" />
            <xsl:text>&#x0020;u.&#x0020;a.</xsl:text>
            <xsl:value-of select="substring-after(child::lt,', u. a.')" />
            </xsl:when>
            <xsl:otherwise>
            <xsl:value-of select="lt" />
            </xsl:otherwise>
           </xsl:choose>.</li><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
         </xsl:when>
 <!-- wenn kein Wechsel des Initialbuchstabens -->
         <xsl:otherwise>
           <li>
             <xsl:value-of select="kt"/><xsl:text> = </xsl:text>          
             <xsl:choose>
             <!--bei der Personenangabe ", u. a." wird das Komma eliminiert-->
               <xsl:when test="contains(child::lt,', u. a.')">
                 <xsl:value-of select="substring-before(child::lt,', u. a.')" />
                 <xsl:text>&#x0020;u.&#x0020;a.</xsl:text>
                 <xsl:value-of select="substring-after(child::lt,', u. a.')" />
               </xsl:when>
               <xsl:otherwise>
                 <xsl:value-of select="lt" />
               </xsl:otherwise>
             </xsl:choose>.</li><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
           </xsl:otherwise>
          </xsl:choose>
        </xsl:for-each>
    </xsl:for-each>
 </ul><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:template>

<!--  WAPPEN -->

<xsl:template name="blasonierungen">
<xsl:for-each select="indices/index[@type='properties_blasonierungen']">
<h2><xsl:value-of select="titel"/></h2><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<xsl:for-each select="eintrag">
<xsl:variable name="wappen_id">di<xsl:value-of select="$volume_number"/>-coa<xsl:value-of select="substring-after(@id,'-')"/></xsl:variable>
<item id="{$wappen_id}">
<xsl:for-each select="lemma"><xsl:value-of select="."/><xsl:if test="following-sibling::*[node()]">: </xsl:if></xsl:for-each>
<xsl:for-each select="schild"><xsl:apply-templates select="."/><xsl:if test="following-sibling::oberwappen[text()]">, </xsl:if></xsl:for-each>
<xsl:for-each select="oberwappen"><xsl:if test="not(starts-with(.,'auf dem Helm'))">auf dem Helm </xsl:if><xsl:value-of select="."/></xsl:for-each>
<xsl:if test="nachweis"> – </xsl:if>
<xsl:apply-templates select="nachweis"></xsl:apply-templates><xsl:text>.</xsl:text>
</item><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>

</xsl:for-each>

</xsl:template>



<!--MARKEN, MEISTERZEICHEN, SCHemazeichnungen-->
  <!--Vortitel-->
<xsl:template match="zeichnungen">
<!--<h2>
  <xsl:value-of select="titel"/>
</h2><xsl:text>&#x000D;</xsl:text>-->
<xsl:apply-templates></xsl:apply-templates>
</xsl:template>

  <!--Meisterzeichen und Hausmarken-->
<!-- <xsl:template match="abschnitt[@name='marken']"> -->
<xsl:template match="marken">
  <h2><xsl:value-of select="titel"/></h2><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<xsl:for-each select="abschnitt">
    <h3><xsl:value-of select="titel"/></h3><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <marks_sequence><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
            <xsl:apply-templates select="."/>
        </marks_sequence><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    </xsl:for-each>
</xsl:template>

<xsl:template match="abschnitt[parent::marken]">
<xsl:param name="p_mark-sign"><xsl:value-of select="substring(titel,1,1)"/></xsl:param>
  <xsl:for-each select="m-group/marke">
    <mark><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <dateiname>di<xsl:value-of select="ancestor::book/project/di_number"/>-<xsl:value-of select="$p_mark-sign"/><xsl:number format="001" value="nr"/>.00000</dateiname>
      <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <documenttitel>DI <xsl:value-of select="ancestor::book/project/di_number"/> - <xsl:value-of select="$p_mark-sign"/> <xsl:value-of select="nr"/> - Kat.-Nr. <xsl:call-template name="linkhandler"/></documenttitel>    
      <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    </mark><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
  </xsl:for-each>
<!--  <marks_concordance><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <xsl:for-each select=".//*">
      <xsl:value-of select="bezeichnung"/> | di<xsl:if test="string-length(ancestor::book/project/di_number)&lt;3">0</xsl:if><xsl:value-of select="ancestor::book/project/di_number"/>-<xsl:value-of select="$p_mark-sign"/><xsl:number format="001" value="nr"/>
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
     </xsl:for-each>    
  </marks_concordance>-->
</xsl:template>

<!--Tabelle Meisterzeichen
<xsl:template match="abschnitt[@name='meisterzeichens']">
  <xsl:apply-templates select="titel"/>
  <xsl:for-each select=".//meisterzeichen">
    <mark><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <dateiname>di<xsl:if test="string-length(//projekt/di)&lt;3">0</xsl:if><xsl:value-of select="//projekt/di"/>-m<xsl:number format="001" value="nr"/>.00000</dateiname>
      <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <documenttitel>DI <xsl:value-of select="//projekt/di"/> - M <xsl:value-of select="nr"/> - Kat.-Nr. <xsl:call-template name="linkhandler"/></documenttitel>    


      <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    </mark><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
  </xsl:for-each>
  <marks_concordance><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <xsl:for-each select=".//*[name()='meisterzeichen']">
      <xsl:value-of select="bezeichnung"/> | di<xsl:if test="string-length(//projekt/di)&lt;3">0</xsl:if><xsl:value-of select="//projekt/di"/>-m<xsl:number count="*[name()='meisterzeichen']" level="any" from="abschnitt[@name='marken']" format="001"/>
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
     </xsl:for-each>    
  </marks_concordance>
</xsl:template>
-->
  <!--Tabelle Hausmarken
  <xsl:template match="abschnitt[@name='hausmarken']">
    <xsl:apply-templates select="titel"/>
    <xsl:for-each select=".//hausmarke">
      <mark><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <dateiname>di<xsl:if test="string-length(//projekt/di)&lt;3">0</xsl:if><xsl:value-of select="//projekt/di"/>-m<xsl:number format="001" value="nr+12"/>.<xsl:value-of select="substring-after(bezeichnung,'.')"/>0</dateiname>
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <documenttitel>DI <xsl:value-of select="//projekt/di"/> - H <xsl:value-of select="nr"/> - Kat.-Nr. <xsl:call-template name="linkhandler"/>
        </documenttitel>    
        
        
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      </mark><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    </xsl:for-each>
    <marks_concordance><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <xsl:for-each select=".//*[name()='hausmarke']">
        <xsl:value-of select="bezeichnung"/> | di<xsl:if test="string-length(//projekt/di)&lt;3">0</xsl:if><xsl:value-of select="//projekt/di"/>-m<xsl:number count="*[name()='hausmarke']" level="any" from="abschnitt[@name='marken']" format="001"/>
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      </xsl:for-each>    
    </marks_concordance>
  </xsl:template>
-->
<xsl:template name="linkhandler">
<xsl:param name="p_volume-nr"><xsl:value-of select="ancestor::book/project[1]/di_number"/></xsl:param>
  <xsl:for-each select=".//knr">
    <xsl:text disable-output-escaping="yes">&lt;link record:di</xsl:text><xsl:value-of select="$p_volume-nr"/><xsl:text>-</xsl:text><xsl:number format="0001" value="."/><xsl:text disable-output-escaping="yes">&gt;</xsl:text><xsl:value-of select="."/><xsl:text disable-output-escaping="yes">&lt;/link&gt;</xsl:text>
    <xsl:if test="not(position()=last())">, </xsl:if></xsl:for-each>  
</xsl:template>




<!--==Ausgelagerte Stylesheets / imported stylesheets=======================-->
<!--Unsichere Lesung
<xsl:template match="insec[not(wtr)]">
<xsl:apply-imports/>
</xsl:template>-->
<!--Worttrenner-->
<xsl:template match="wtr">
  <xsl:choose>
    <xsl:when test="@type='punkt' or @type='dreieck_basline' or @type='quadrangel_baseline'">.</xsl:when>
    <xsl:when test="@type='dpunkt' or @type='dquadrangel'">:</xsl:when>
    <xsl:when test="@type='komma'">,</xsl:when>
    <!--<xsl:when test="@type='kreuz'">+</xsl:when>-->
    <xsl:otherwise>ˑ</xsl:otherwise>
  </xsl:choose>
</xsl:template>
<!--==ENDE: ausgelagerte Stylesheets / imported stylesheets=================-->

<!--==UNTERGEORDNETE TEMPLATES / sub-templates==============================-->
<!--Transkriptionen / transcriptions-->

<!--Wappentabelle-->
<xsl:template match="heraldik">
  <xsl:choose>
    <xsl:when test="count(tabelle/zeile[1]/zelle) = 6">
      <caption>Wappen Herzogszepter:</caption><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <xsl:for-each select=".//zeile[position()&lt;4]">
        <row>
          <xsl:for-each select="zelle">
            <cell>
              <xsl:for-each select="wappen">
                <entry><xsl:apply-templates select="."/></entry>
              </xsl:for-each>
            </cell>
          </xsl:for-each>
        </row>
      </xsl:for-each>
      <caption>Wappen Bischofszepter:</caption><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <xsl:for-each select=".//zeile[position()&gt;4]">
        <row>
          <xsl:for-each select="zelle">
            <cell>
              <xsl:for-each select="wappen">
                <entry><xsl:apply-templates select="."/></entry>
              </xsl:for-each>
            </cell>
          </xsl:for-each>
        </row>
      </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>     
    </xsl:when>   
    <xsl:otherwise>
       <caption><xsl:value-of select="bezeichnung"/></caption><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
       <xsl:apply-templates select="tabelle"/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template match="heraldik/tabelle">
  <xsl:for-each select="zeile">
    <row>
      <xsl:for-each select="zelle">
        <cell>
            <xsl:apply-templates></xsl:apply-templates>
<!--          <xsl:for-each select="wappen">
            <xsl:variable name="wappen_id">coa:di<xsl:value-of select="$volume_number"/>-coa<xsl:value-of select="substring-after(@target_id,'-')"/></xsl:variable>
            <entry><ref target="{$wappen_id}"><xsl:apply-templates select="."/></ref></entry>-->
          <!--</xsl:for-each>-->
        </cell>
      </xsl:for-each>
    </row>
  </xsl:for-each>
</xsl:template>
<xsl:template match="zelle/wappen">
            <xsl:variable name="wappen_id">coa:di<xsl:value-of select="$volume_number"/>-coa<xsl:value-of select="substring-after(@target_id,'-')"/></xsl:variable>
            <entry>
                <xsl:choose>
                    <xsl:when test="starts-with(.,'?')">
                        <xsl:apply-templates/>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:choose>
                            <xsl:when test="$links_coa=1"><ref target="{$wappen_id}"><xsl:apply-templates/></ref></xsl:when>
                            <xsl:otherwise><xsl:apply-templates/></xsl:otherwise>
                        </xsl:choose>
                    </xsl:otherwise>
                </xsl:choose>
            </entry>
</xsl:template>

<!--Fussnotenzeichen im Text / footnote characters-->
<!--1. der Zahlenapparat-->
<xsl:template match="app1[ancestor::catalog]">
<xsl:param name="targetID"><xsl:value-of select="ancestor::article/apparate/ziffernapparat/item[@pkID=current()/@id]/@itemID"/></xsl:param>
<xsl:choose>
    <xsl:when test="$links_footnotes=1">
        <ref>
        <xsl:attribute name="id"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number_dreistellig"/>-<xsl:number value="ancestor::article/@nr" format="0001"></xsl:number>-pk<xsl:value-of select="@id"/></xsl:attribute>
        <xsl:attribute name="target">#<xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number_dreistellig"/>-<xsl:number value="ancestor::article/@nr" format="0001"></xsl:number>-<xsl:value-of select="$targetID"/></xsl:attribute>
        <sup>
        <xsl:number level="any" from="article" count="app1|loc_ten" format="1)"/>
        </sup>
        </ref>
    </xsl:when>
    <xsl:otherwise>
        <sup><xsl:number level="any" from="article" count="app1|loc_ten" format="1)"/></sup>
    </xsl:otherwise>
</xsl:choose>


</xsl:template>
<!--2. der Buchstabenapparat-->
<xsl:template match="app2">
<xsl:param name="targetID"><xsl:value-of select="ancestor::article/apparate/buchstabenapparat/item[@pkID=current()/@id]/@itemID"/></xsl:param>
<xsl:choose>
    <xsl:when test="$links_footnotes=1">
        <ref>
        <xsl:attribute name="id"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number_dreistellig"/>-<xsl:number value="ancestor::article/@nr" format="0001"></xsl:number>-pk<xsl:value-of select="@id"/></xsl:attribute>
        <xsl:attribute name="target">#<xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number_dreistellig"/>-<xsl:number value="ancestor::article/@nr" format="0001"></xsl:number>-<xsl:value-of select="$targetID"/></xsl:attribute>
        <sup>
        <xsl:number level="any" from="article" count="app2" format="a)"/>
        </sup>
        </ref>
    </xsl:when>
    <xsl:otherwise>
        <sup><xsl:number level="any" from="article" count="app2" format="a)"/></sup>
    </xsl:otherwise>
</xsl:choose>


</xsl:template>
  
<!--Fussnoten in der Einleitung-->
<xsl:template match="app1[ancestor::einleitung or ancestor::vorwort]">
<anm><xsl:apply-templates/></anm>  
</xsl:template>  
  
  
<!--Zitate in Fussnoten / quotations in footnotes-->
<xsl:template match="quot[*|text()]"><em><xsl:apply-templates/></em></xsl:template>

<!--Formate in den Transkriptionen-->
<!--Versalien / ornamental initials-->
<xsl:template match="vsl[not(ancestor::app2)]">
  <xsl:apply-templates/>
</xsl:template>
<!--Initialen / initials-->
<xsl:template match="ini">
  <xsl:apply-templates/>
</xsl:template>
<!--hochgestellte Buchstaben / elevated letters-->
<xsl:template match="sup[*|text()]">
   <xsl:choose>
    <xsl:when test="(current()='o' or current()='c') and not(@value='keine Jahreszahl')">
      <sup><xsl:apply-templates/></sup>
    </xsl:when>
    <xsl:otherwise><xsl:apply-templates/></xsl:otherwise> 
   </xsl:choose>
</xsl:template>


<xsl:template match="vz">
  <xsl:if test="@indent='0'">
    <lno>
      <xsl:call-template name="zeilennummer"/>
      <xsl:apply-templates/>
    </lno><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text></xsl:if>
  <xsl:if test="@indent='1'">
    <lin>
      <xsl:call-template name="zeilennummer"/>
      <xsl:apply-templates/>
    </lin><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text></xsl:if>  
</xsl:template>

<xsl:template name="zeilennummer">
  <xsl:param name="zeilennummer">
    <xsl:number level="single" count="vz[text()]" from="bl" format="1"/>
  </xsl:param>
  <xsl:param name="zeilenanzahl">
    <xsl:value-of select="count(ancestor::bl/vz[text()])"/>
  </xsl:param> 
  <xsl:if test="$zeilenanzahl &gt; 10">
    <xsl:if test="$zeilennummer mod 5 =0">
      <cnt><xsl:value-of select="$zeilennummer" /></cnt>
    </xsl:if>
  </xsl:if>
</xsl:template>

<!--Feste Leerstellen und Zwischenraeume / fixed blanks and gaps-->
<xsl:template match="spatium_v[@width=1]">
<xsl:choose>
 <xsl:when test="ancestor::inschrift">
  <xsl:text>&#x0020;</xsl:text>
 </xsl:when>
 <xsl:otherwise>
  <span style="margin-left:1pt"/>
 </xsl:otherwise>
</xsl:choose>
</xsl:template>

<xsl:template match="spatium_v[@width &gt; 1]">
<xsl:for-each select=".">
<span>
  <xsl:attribute name="padding-right">
    <xsl:call-template name="multiplikator">
      <xsl:with-param name="faktor" select="@width" />
    </xsl:call-template>
  </xsl:attribute>
</span>
</xsl:for-each>
</xsl:template>
<xsl:template name="multiplikator">
<xsl:param name="faktor"/>
<xsl:value-of select="$faktor * 4"/>pt</xsl:template>
<!--Ende: Transkriptionsformate / transcriptions=============================-->
<!--Verweise auf andere Datensaetze/Artikel / references to inscriptions in other articles-->
<!--<rec type="extern"/> / rec[@type="extern"]ausgelagert in druck.volagen.xsl-->

<!--Spalten im Transkriptionsfeld / columns in transcription area-->
<!-- match="bl[@align='Spalten']/bl[1]" -->
<xsl:template name="spalten">
<table><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<row><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
<xsl:for-each select="bl">
<cell><xsl:apply-templates></xsl:apply-templates></cell><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
</xsl:for-each>
</row>
</table><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>



<!--<xsl:for-each select="zeile">
  <xsl:apply-templates select="."/>
</xsl:for-each>-->
</xsl:template>

<xsl:template match="z[parent::bl[parent::bl[@align='Spalten']]]"><br/></xsl:template>

<xsl:template match="bl/zeile">
  <xsl:param name="position" select="position()"/>
  <lno>
    <xsl:apply-templates/>
    <tab><xsl:for-each select="ancestor::bl[@align='Spalten']/bl[2]/zeile[@position=current()/@position]"><xsl:apply-templates/></xsl:for-each></tab>
  </lno>  
</xsl:template>


<!--Zeilenumbrueche und Absaetze in Kommentaren, Beschreibungen, Vorwort, Einleitung usw.: ausser in Transkriptionen / various line breaks-->
<xsl:template match="nl[parent::beschreibung or parent::kommentar or parent::notizen]">
  <xsl:if test="not(preceding-sibling::node()[1][self::nl])"><!--<br/>-->
    <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
  </xsl:if>
</xsl:template>

 


<!--Literaturverweis / bibliographical reference -->
<!--<xsl:template match="apparate//link[@type='literatur']">
  <ref>
    <xsl:copy-of select="@type"/>
    <xsl:attribute name="target"><xsl:value-of select="@target_id"/></xsl:attribute>
    <xsl:value-of select="."/>
  </ref>
</xsl:template>-->

<xsl:template match="link[@type='literatur']">
<xsl:param name="target_id">lit:di<xsl:value-of select="$volume_number"/>-lit<xsl:value-of select="substring-after(@target_id,'-')"/></xsl:param>

    
<xsl:choose>
    <xsl:when test="$links_lit=1"><ref target="{$target_id}"><xsl:value-of select="."/></ref></xsl:when>
    <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
</xsl:choose>
</xsl:template>

<!-- 
<xsl:choose>
    <xsl:when test="">

    </xsl:when>
    <xsl:otherwise>

    </xsl:otherwise>
</xsl:choose>
 -->


<!--<xsl:template match="link[@type='literatur']">
<xsl:param name="target_id">lit:di<xsl:value-of select="$volume_number"/>-lit<xsl:value-of select="substring-after(@target_id,'-')"/></xsl:param>
<xsl:choose>
    <xsl:when test="ancestor::apparate">
      <ref>
        <xsl:copy-of select="@type"/>
        <xsl:attribute name="target"><xsl:value-of select="@target_id"/></xsl:attribute>
        <xsl:value-of select="."/>
      </ref>
    </xsl:when>
    <xsl:otherwise>
        <ref target="{$target_id}"><xsl:value-of select="."/></ref>
    </xsl:otherwise>
</xsl:choose>
</xsl:template>-->



<!--<xsl:template match="link[@type='literatur'][ancestor::index[@name='index.blasonierung']]">
  <ref>
    <xsl:copy-of select="@type"/>
    <xsl:attribute name="target"><xsl:value-of select="@target_id"/></xsl:attribute>
    <xsl:value-of select="."/>
  </ref>
</xsl:template>

<xsl:template match="link[@type='literatur'][ancestor::abschnitt[@name='einleitung']]">
  <ref>
    <xsl:copy-of select="@type"/>
    <xsl:attribute name="target"><xsl:value-of select="@target_id"/></xsl:attribute>
    <xsl:value-of select="."/>
  </ref>
</xsl:template>-->


 


<!--Verweise auf Hausmarken-->
  <xsl:variable name="count_mz" select="count(//meisterzeichen)"></xsl:variable>  
  <xsl:template match="rec[@type='marken'][contains(@text,'HM')]">
    <xsl:param name="marke_nr"><xsl:value-of select="substring-after(document('../Daten/marken.xml')//zeile[zelle[1][text()=current()/@text]]/zelle[2], '_m')"/></xsl:param>
    <xsl:choose>
        <xsl:when test="$links_marks=1">
           <ref>
           <xsl:attribute name="mark">
             <xsl:value-of select="document('../Daten/marken.xml')//zeile[zelle[1][text()=current()/@text]]/zelle[2]"/>
           </xsl:attribute>
           <xsl:text>H</xsl:text><xsl:number value="$marke_nr"/></ref>    
        </xsl:when>
        <xsl:otherwise>
            <xsl:text>H</xsl:text><xsl:number value="$marke_nr"/>
        </xsl:otherwise>
    </xsl:choose>
</xsl:template>


<!--Verweise auf Meisterzeichen-->
  <xsl:template match="rec[@type='marken'][contains(@text,'MZ')]">
    <xsl:param name="marke_nr"><xsl:value-of select="substring-after(document('../Daten/marken.xml')//zeile[zelle[1][text()=current()/@text]]/zelle[2], '_m')"/></xsl:param>
    <xsl:choose>
        <xsl:when test="$links_marks=1">
          <ref>
            <xsl:attribute name="mark">
              <xsl:value-of select="document('../Daten/marken.xml')//zeile[zelle[1][text()=current()/@text]]/zelle[2]"/>
            </xsl:attribute>
            <xsl:text>M</xsl:text><xsl:number value="$marke_nr"/>
          </ref>
        </xsl:when>
        <xsl:otherwise>
            <xsl:text>M</xsl:text><xsl:number value="$marke_nr"/>
        </xsl:otherwise>
    </xsl:choose>

</xsl:template>

<!--Verweise auf Inschriften-->
<xsl:template match="link[@type='inschrift']">
    <!-- auslesen der @id der ersten bearbeitung einer inschrift oder eines inschriftenteils, um sie im folgenden als letztes glied der verweisadresse zu verwenden -->
        <xsl:param name="bearbeitungID">
            <!-- es wird unterschieden zwischen verweis im selben artikel oder in einen anderen artikel -->
        <xsl:choose>
            <!-- in anderem artikel -->
            <xsl:when test="@target_article">
                <xsl:for-each select="ancestor::catalog/article[@nr=current()/@target_article]//*[@id=current()/@target_id]">
                    <xsl:choose>
                        <xsl:when test="inschriftenteil"><xsl:value-of select="inschriftenteil[1]/bearbeitung[1]/@id"/></xsl:when>
                        <xsl:otherwise><xsl:value-of select="bearbeitung[1]/@id"/></xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:when>
            <!-- im selben artikel -->
            <xsl:otherwise>
                <xsl:for-each select="ancestor::article//*[@id=current()/@target_id]">
                <!-- wenn der links auf eine inschrift zielt, wird auf deren ersten inschriftenteil und dann weiter auf die erste bearbeitung umgeleitet;
                        wenn er bereits auf eine bearbeitung gerichtet ist, wird deren @id direkt aufgenommen -->
                    <xsl:choose>
                        <xsl:when test="inschriftenteil"><xsl:value-of select="inschriftenteil[1]/bearbeitung[1]/@id"/></xsl:when>
                        <xsl:otherwise><xsl:value-of select="@id"/></xsl:otherwise>
                    </xsl:choose>
                </xsl:for-each>
            </xsl:otherwise>
        </xsl:choose>
        </xsl:param>

    <!-- für verweise auf inschriften in anderen artikeln wird der anfang der verweisadresse aus namespace, bandnummer und artikelnummer geformt -->
        <xsl:param name="record-link">record:di<xsl:value-of select="$volume_number"/>-<xsl:number value="@target_article" format="0001"></xsl:number></xsl:param>

    <!-- für externe links auf inschriften in anderen artikeln wird die adresse innerhalb des zielartikels gebildet aus bandnummer, artikelnummer und @id der ersten bearbeitung -->
        <xsl:param name="inschrift-link-extern"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number"/>-<xsl:number value="@target_article" format="0001"/>-<xsl:value-of select="$bearbeitungID"/></xsl:param>

    <!-- für interne links auf inschriften oder inschriftenteile wird die adresse gebildet aus bandnumer, artikelnummer und @id der ersten bearbeitung -->
        <xsl:param name="inschrift-link-intern"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number"/>-<xsl:number value="ancestor::article/@nr" format="0001"/>-<xsl:value-of select="$bearbeitungID"></xsl:value-of></xsl:param>

<!-- beim bilden der linkadresse wird unterschieden zwischen artikelintern und artikelextern; dem entsprechend werden die adressen aus den zuvor angelegten parametern erstellt -->
<!-- noch nicht berücksichtigt ist, dass bei verweis auf eine inschrift in einem artikel der nur eine einzige inschrift enthält, der inschriftbezeichner ausgeblendet wird; 
dieser umstand müsste bereits bei der vorformatierung in epigraf-server.trans2.xsl analysiert und gekennzeichnet werden -->
<xsl:choose>
    <xsl:when test="@target_article">
        <xsl:choose>
            <xsl:when test="$links_inscriptions=1">
                <ref>
                    <xsl:attribute name="target"><xsl:value-of select="$record-link"/>#<xsl:value-of select="$inschrift-link-extern"/></xsl:attribute>
                    <xsl:apply-templates></xsl:apply-templates>
                </ref>
            </xsl:when>
            <xsl:otherwise><xsl:apply-templates/></xsl:otherwise>
        </xsl:choose>

    </xsl:when>
    <xsl:otherwise>
        <xsl:choose>
            <xsl:when test="$links_inscriptions=1">
                    <ref>
                        <xsl:attribute name="target">#<xsl:value-of select="$inschrift-link-intern"/></xsl:attribute>
                        <xsl:apply-templates></xsl:apply-templates>
                    </ref>
            </xsl:when>
            <xsl:otherwise><xsl:apply-templates/></xsl:otherwise>
        </xsl:choose>
    </xsl:otherwise>
</xsl:choose>
</xsl:template>


<!-- erste variante mit subsections -->
<!--<xsl:template match="link[@type='inschrift']">
<xsl:param name="record-link">record:di<xsl:value-of select="$volume_number"/>-<xsl:number value="@target_article" format="0001"></xsl:number></xsl:param>
<xsl:param name="inschrift-link-extern"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number"/>-<xsl:number value="@target_article" format="0001"/>-<xsl:value-of select="@target_id"/></xsl:param>
<xsl:param name="inschrift-link-intern"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number"/>-<xsl:number value="ancestor::article/@nr" format="0001"/>-<xsl:value-of select="@target_id"/></xsl:param>
<xsl:choose>
    <xsl:when test="@target_article">
        <ref>
            <xsl:attribute name="target"><xsl:value-of select="$record-link"/>#<xsl:value-of select="$inschrift-link-extern"/></xsl:attribute>
            <xsl:value-of select="."/>
        </ref>
    </xsl:when>
    <xsl:otherwise>
        <ref>
            <xsl:attribute name="target">#<xsl:value-of select="$inschrift-link-intern"/></xsl:attribute>
            <xsl:value-of select="."/>
        </ref>
    </xsl:otherwise>
</xsl:choose>
<!-\-    <ref>
    <xsl:copy-of select="@type"/>
    <xsl:attribute name="target"><xsl:value-of select="@target_id"/></xsl:attribute>
    <xsl:value-of select="."/>
  </ref>-\->
</xsl:template>-->

  <!--Verweise auf Marken-->
  <xsl:template match="link[@type='marke']">

<!-- die ziel-id der marke in der markenliste speichern  -->
<xsl:param name="target"><xsl:value-of select="@target"/></xsl:param>

<!-- den ziffern-string aus der id extrahieren -->
<xsl:param name="target_nr"><xsl:value-of select="substring-after($target,'-')"></xsl:value-of></xsl:param>

<!-- mittels der ziel-id die marke in der markenliste ansteuern -->
<xsl:param name="bezeichnung">
<xsl:for-each select="ancestor::book//marken//marke[@link_item=$target]">
<!-- den ersten Buchstaben der Bezeichnung des Markentyps in einer variablen speichern
/properties_markentyp_id[1]/lemma[1]-->
<xsl:variable name="signum"><xsl:value-of select="substring(properties_markentyp_id/lemma,1,1)"/></xsl:variable>
<!-- die nummer der marke (bezogen auf den markentyp) speichern -->
<xsl:variable name="nr"><xsl:value-of select="nr"/></xsl:variable>
<xsl:value-of select="$signum"/><xsl:value-of select="$nr"/>
</xsl:for-each>
</xsl:param>


<!-- muster: <ref target="mark:di077-m013">H13</ref> -->
<xsl:choose>
    <xsl:when test="$links_marks=1">
        <ref>
        <xsl:attribute name="target">mark:<xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number"/>-m<xsl:value-of select="$target_nr"/></xsl:attribute>
            <xsl:value-of select="$bezeichnung"/>
        </ref>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="$bezeichnung"/></xsl:otherwise>
</xsl:choose>


  </xsl:template>


<xsl:template match="link[@type='map']">
<!-- Muster: <ref target="object:map077-1_di077-0031">Nr. 31</ref> -->
<!--
        <ref>
            <xsl:attribute name="map-object"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number_dreistellig"/>_map<xsl:value-of select="@map_number"/>_<xsl:value-of select="."/></xsl:attribute>
                <xsl:value-of select="."/>
        </ref>
-->
    <xsl:choose>
        <xsl:when test="$links_maps=1">
            <ref>
                <xsl:attribute name="target">object:map<xsl:value-of select="$volume_number"/>-<xsl:value-of select="@map_number"/>_di<xsl:value-of select="$volume_number"/>-<xsl:number value="." format="0001"/></xsl:attribute>
                    <xsl:value-of select="."/>
            </ref>
        </xsl:when>
        <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
    </xsl:choose>
</xsl:template>

<!--Verluste in den Transkriptionen: die Klammern werden mit den Strichen in der Zeile zusammengehalten-->
<xsl:template match="del">
    <xsl:apply-templates/>
</xsl:template>

<!--Nachtraege -->
<xsl:template match="add">&#x27E8;<xsl:apply-templates/>&#x27E9;</xsl:template>

<xsl:template match="z"><xsl:apply-templates/></xsl:template>

<!--Ueberschriften in Beschreibungen und Kommentaren-->
<xsl:template match="h[ancestor::objekt]">
  <h><em><xsl:apply-templates/></em></h>
</xsl:template>

<!--Kursivierungen in den Textbausteinen, z. B. in den Registervorbemerkungen-->
<xsl:template match="i"><em><xsl:apply-templates/></em>
</xsl:template>

<!--Sperrung-->
<xsl:template match="g">
 <letterspacing><xsl:apply-templates/></letterspacing>
</xsl:template>

<!--Kapitaelchen-->
<xsl:template match="k">
  <smallcaps><xsl:apply-templates/></smallcaps>
</xsl:template>

  <!--Verweise auf andere Datensaetze/Artikel-->
    <!-- Muster: <ref target="record:di077-0010">Kat.-Nr. 10</ref> -->
  <!--Einzelverweise-->
  <xsl:template match="link[@type='article'][not(ancestor::folge)]">
<xsl:choose>
    <xsl:when test="$links_articles=1">
        <ref>
         <xsl:attribute name="target">record:<xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number_dreistellig"/>-<xsl:value-of select="@sortstring"/></xsl:attribute>
         <xsl:value-of select="."/>
       </ref>   
    </xsl:when>
    <xsl:otherwise> <xsl:value-of select="."/></xsl:otherwise>
</xsl:choose>

  </xsl:template>
  <!--Verweise mit Ergaenzungen in Verweisserie-->
  <xsl:template match="link[@type='article'][parent::folge[parent::folge]]">
<xsl:choose>
    <xsl:when test="$links_articles=1">
    <ref>
     <xsl:attribute name="target">record:<xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number_dreistellig"/>-<xsl:value-of select="@sortstring"/></xsl:attribute>
      <xsl:value-of select="."/>
     </ref><xsl:apply-templates select="parent::folge/node()[not(self::link)]"/>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="."/><xsl:apply-templates select="parent::folge/node()[not(self::link)]"/></xsl:otherwise>
</xsl:choose>

  </xsl:template>
  <!--Verweise ohne Ergaenzungen in Verweisserie-->
  <xsl:template match="link[@type='article'][parent::folge[not(parent::folge)]]">
<xsl:choose>
    <xsl:when test="$links_articles=1">
        <ref>
         <xsl:attribute name="target">record:<xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number_dreistellig"/>-<xsl:value-of select="@sortstring"/></xsl:attribute>
          <xsl:value-of select="."/>
        </ref>
    </xsl:when>
    <xsl:otherwise><xsl:value-of select="."/></xsl:otherwise>
</xsl:choose>

  </xsl:template>
  
<xsl:template match="link[@type='fussnote']">
<xsl:param name="targetID"><xsl:value-of select="ancestor::article/apparate//item[@pkID=current()/@target_id]/@itemID"/></xsl:param>
<xsl:param name="article_number"><xsl:number value="ancestor::article/@nr" format="0001"></xsl:number></xsl:param>
<xsl:param name="target-pref"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number"/>-<xsl:value-of select="$article_number"/>-</xsl:param>

    <xsl:for-each select="ancestor::article/apparate//item[@pkID=current()/@target_id]">
    <xsl:choose>
        <xsl:when test="$links_footnotes=1">
          <ref >
            <xsl:attribute name="target"><xsl:text>#</xsl:text><xsl:value-of select="$target-pref"/><xsl:value-of select="$targetID"/></xsl:attribute>      
              <xsl:choose>
                <xsl:when test="parent::buchstabenapparat">
                  <xsl:number level="any" count="item" from="buchstabenapparat" format="a"/>
                </xsl:when>
                <xsl:when test="parent::ziffernapparat">
                  <xsl:number level="any" count="item" from="ziffernapparat" format="1"/>
                </xsl:when>
              </xsl:choose>
          </ref>
        </xsl:when>
        <xsl:otherwise>
              <xsl:choose>
                <xsl:when test="parent::buchstabenapparat">
                  <xsl:number level="any" count="item" from="buchstabenapparat" format="a"/>
                </xsl:when>
                <xsl:when test="parent::ziffernapparat">
                  <xsl:number level="any" count="item" from="ziffernapparat" format="1"/>
                </xsl:when>
              </xsl:choose>
        </xsl:otherwise>
    </xsl:choose>

    </xsl:for-each>
</xsl:template>


<!-- verweis auf eine fußnote in einem anderen artikel -->
  <xsl:template match="link[@type='fussnote_extern']">
    <xsl:variable name="target_fussnote"><xsl:value-of select="@target_id"/></xsl:variable>
    <xsl:variable name="target_article"><xsl:value-of select="@article_id"/></xsl:variable>
    <xsl:variable name="article_number"><xsl:number value="@target_article" format="0001"/></xsl:variable>

    <xsl:for-each select="ancestor::catalog/article/apparate//item[@id=$target_fussnote]">
        <xsl:variable name="apparat_typ"><xsl:if test="parent::buchstabenapparat">ants</xsl:if><xsl:if test="parent::ziffernapparat">fnts</xsl:if></xsl:variable>
        <xsl:variable name="target-pref"><xsl:value-of select="$di-type"/><xsl:value-of select="$volume_number"/>-<xsl:value-of select="$article_number"/>-<xsl:value-of select="$apparat_typ"/></xsl:variable>

    <xsl:choose>
        <xsl:when test="$links_footnotes=1">
          <ref >
            <xsl:attribute name="target">record:di<xsl:value-of select="$volume_number"/><xsl:text>-</xsl:text><xsl:value-of select="$article_number"/>
<xsl:text>#</xsl:text><xsl:value-of select="$target-pref"/><xsl:value-of select="$target_fussnote"/></xsl:attribute>      
              <xsl:choose>
                <xsl:when test="parent::buchstabenapparat">
                  <xsl:number level="any" count="item" from="buchstabenapparat" format="a"/>
                </xsl:when>
                <xsl:when test="parent::ziffernapparat">
                  <xsl:number level="any" count="item" from="ziffernapparat" format="1"/>
                </xsl:when>
              </xsl:choose>
          </ref>
        </xsl:when>
        <xsl:otherwise>
              <xsl:choose>
                <xsl:when test="parent::buchstabenapparat">
                  <xsl:number level="any" count="item" from="buchstabenapparat" format="a"/>
                </xsl:when>
                <xsl:when test="parent::ziffernapparat">
                  <xsl:number level="any" count="item" from="ziffernapparat" format="1"/>
                </xsl:when>
              </xsl:choose>
        </xsl:otherwise>
    </xsl:choose>

    </xsl:for-each>
  </xsl:template>

 <!-- Sortieren von Verweisen in Verweisserien-->
  <xsl:template match="folge[not(parent::folge)]">
  <xsl:text>Kat.-Nr.&#x20;</xsl:text>
   <xsl:for-each select=".//link">
    <xsl:sort data-type="number" select="@text" />
     <xsl:apply-templates select="."/>
       <xsl:if test="position()!=last() and position()!=last()-1"><xsl:text>, </xsl:text></xsl:if><xsl:if test="position()=last()-1"><xsl:text>, <!--hier kann man auch " und " einsetzen--></xsl:text></xsl:if>                  
   </xsl:for-each>
  </xsl:template>

<xsl:template match="schild"><xsl:apply-templates/></xsl:template>

<!--Ersetzen der Signatur (=alte Nummer)) durch die aktuelle Nummer auf dem Kirchengrundriss nach der entsprechenden Konkordanz-->
<xsl:template match="signatur_grundriss">
  <xsl:param name="konkordanz"><xsl:value-of select="document('../Daten/konkordanz.xml')//*[name()='Worksheet']//*[name()='Row' and descendant::*[name()='Data']][*[name()='Cell'][1]/*[name()='Data']=current()/text()]/*[name()='Cell'][2]/*[name()='Data']" /></xsl:param>
 <ref>
   <xsl:attribute name="map-object"><xsl:value-of select="@link"/><xsl:number format="001" value="$konkordanz"/></xsl:attribute>
   <xsl:text>Nr.&#x20;</xsl:text>
   <xsl:value-of select="$konkordanz" />
 </ref>
</xsl:template>

 <xsl:template match="version[node()]">
   <sup><span style="text-decoration:underline"><xsl:value-of select="."/></span></sup>
 </xsl:template>

<xsl:template match="su"><sup><span style="text-decoration:underline"><xsl:value-of select="."/></span></sup></xsl:template>

</xsl:stylesheet>

