<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
  xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml" 
  xmlns:v="urn:schemas-microsoft-com:vml" 
  xmlns:w10="urn:schemas-microsoft-com:office:word" 
  xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core" 
  xmlns:aml="http://schemas.microsoft.com/aml/2001/core" 
  xmlns:wx="http://schemas.microsoft.com/office/word/2003/auxHint" 
  xmlns:o="urn:schemas-microsoft-com:office:office" 
  xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" 
  xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2" 
  xmlns:msxsl="urn:schemas-microsoft-com:xslt" 
  xmlns:exsl="http://exslt.org/common" 
  xmlns:php="http://php.net/xsl" 
  >
  
  <xsl:import href="../commons/di-switch.xsl"/>
  <xsl:import href="di-doc-styles.xsl"/>
  <xsl:import href="di-doc-commons.xsl"/>
  
  <xsl:output method="xml" version="1.0"  indent="yes" encoding="UTF-8"/>

<!-- 
    mit diesem stylesheet wird die titelei 
    eines inschriftenbandes erzeugt:
    1. titelseite für die reihe
    2. titelseite für den band
    3. seite für sponsoring, bibliografische angaben, impressum
  -->


<xsl:template match="preliminaries">
  <!-- die einzelnen seiten der titelei werden nacheinander aufgerufen, 
       die inhalte über benannte templates eingefügt-->

  <!-- ausgabeoptionen abfragen -->
  <xsl:if test="$sw_titelei=1 and node()">
    <xsl:comment>titelei anfang</xsl:comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <wx:sect><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    
    <!-- erste titelseite -->
      <xsl:comment>reihentitel anfang</xsl:comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <wx:sub-section><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="prelim_series"/>
      </wx:sub-section>
      <xsl:comment>reihentitel ende</xsl:comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    
    <!-- zweite titelseite -->
      <xsl:comment>bandtitel anfang</xsl:comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <wx:sub-section><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="prelim_volume"/>
      </wx:sub-section>
      <xsl:comment>bandtitel ende</xsl:comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    
    <!-- seite für sponsoring, bibliographische angaben und impressum -->
      <xsl:comment>impressum anfang</xsl:comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <wx:sub-section><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        <xsl:call-template name="impressum"/>
      </wx:sub-section>
      <xsl:comment>impressum ende</xsl:comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    
  </wx:sect><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  <xsl:comment>titelei ende</xsl:comment><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
  </xsl:if>
</xsl:template>
  
  <!-- erste titelseite -->
  <xsl:template name="prelim_series">
    <!-- um erscheinungsjahr und verlag an den unteren rand setzen zu können, 
      wird die seite als einspaltige tabelle formatiert -->
      <w:tbl>
        <w:tblPr>
          <w:tblW w:w="0" w:type="auto"/>
          <w:tblBorders>
            <w:top w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:left w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:bottom w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:right w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:insideH w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
            <w:insideV w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
          </w:tblBorders>
          <w:tblLook w:val="04A0"/>
        </w:tblPr>
        <w:tblGrid>
          <w:gridCol w:w="9212"/>
        </w:tblGrid>
        <!-- tabellenzeile für den oberen teil der seite -->
        <w:tr>
          <w:trPr>
            <w:trHeight w:val="10915"/>
          </w:trPr>
          <w:tc>
            <w:tcPr>
              <w:tcW w:w="9212" w:type="dxa"/>
            </w:tcPr>
            <!-- reihentitel ansteuern -->
            <xsl:for-each select="prelim_series/prelim_series_title">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-titelei-1"/>
                </w:pPr>
                <!-- reihentitel auslesen -->
                <w:r>
                  <w:t><xsl:value-of select=".//p"/></w:t>
                </w:r>
              </w:p>
            </xsl:for-each>
            
            <!-- absatz mit den herausgebern der reihe -->
            <xsl:for-each select="prelim_series/prelim_series_editor//p">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-titelei-2"/>
                </w:pPr>
                <w:r>
                  <w:t><xsl:value-of select="."/></w:t>
                </w:r>
              </w:p>
            </xsl:for-each>
            
            <!-- zwischenabsatz -->
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-titelei-2"/>
              </w:pPr>
            </w:p>
            
            <!-- absatz für die reihennummer -->
            <xsl:for-each select="prelim_series/prelim_series_number">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-titelei-2"/>
              </w:pPr>
              <w:r>
                <w:t><xsl:value-of select=".//p"/></w:t>
              </w:r>
            </w:p>
            </xsl:for-each>
            
            <!-- absatz für die unterreihe -->
            <xsl:for-each select="prelim_sub_series">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-titelei-2"/>
                </w:pPr>
                <w:r>
                  <w:t><xsl:value-of select=".//p"/></w:t>
                </w:r>
              </w:p>
            </xsl:for-each>
            
            <!-- zwischenabsatz -->
            <w:p>
              <w:pPr>
                <w:jc w:val="center"/>
              </w:pPr>
            </w:p>
            
            <!-- absatz für den bandtitel -->
            <xsl:for-each select="prelim_volume/prelim_volume_title">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-titelei-4"/>
              </w:pPr>
              <w:r>
                <w:t><xsl:value-of select="./p"/></w:t>
              </w:r>
            </w:p>
            </xsl:for-each>
          </w:tc>
        </w:tr>
        
        <!-- tabellenzeile für den unteren teil der seite bis zum seitenfuß -->
        <xsl:call-template name="prelim-bottom"></xsl:call-template>
      </w:tbl>
  </xsl:template>


<!-- zweite titelseite -->
  <xsl:template name="prelim_volume">
    <!-- um erscheinungsjahr und verlag an den unteren rand setzen zu können, 
      wird die seite als einspaltige tabelle formatiert -->
     <w:tbl>
       <w:tblPr>
         <w:tblW w:w="0" w:type="auto"/>
         <w:tblBorders>
           <w:top w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
           <w:left w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
           <w:bottom w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
           <w:right w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
           <w:insideH w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
           <w:insideV w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
         </w:tblBorders>
         <w:tblLook w:val="04A0"/>
       </w:tblPr>
       <w:tblGrid>
         <w:gridCol w:w="9212"/>
       </w:tblGrid>
       <!-- tabellenzeile für den oberen teil der seite -->
       <w:tr>
         <w:trPr>
           <w:trHeight w:val="10915"/>
         </w:trPr>
         <w:tc>
           <w:tcPr>
             <w:tcW w:w="9212" w:type="dxa"/>
           </w:tcPr>
           <!-- leerer Absatz, weil Tabellenzellen nicht leer sein dürfen -->
           <xsl:if test="not(prelim_volume/prelim_volume_title) and not(prelim_volume/prelim_volume_subtitle//p)">
             <w:p>
               <w:pPr>
                 <w:pStyle w:val="epi-titelei-1"/>
               </w:pPr>
               <w:r>
                 <w:t></w:t>
               </w:r>
             </w:p>               
           </xsl:if>
           
           <!-- absatz für den bandtitel -->
           <xsl:for-each select="prelim_volume/prelim_volume_title">
             <w:p>
               <w:pPr>
                 <w:pStyle w:val="epi-titelei-1"/>
               </w:pPr>
               <w:r>
                 <w:t><xsl:value-of select=".//p"/></w:t>
               </w:r>
             </w:p>
           </xsl:for-each>
           
           <!-- absatz zur angabe der bandbearbeiter-->
           <xsl:for-each select="prelim_volume/prelim_volume_subtitle//p">
             <w:p>
               <w:pPr>
                 <w:pStyle w:val="epi-titelei-2"/>
               </w:pPr>
               <w:r>
                 <w:t><xsl:value-of select="."/></w:t>
               </w:r>
             </w:p>
           </xsl:for-each>
         </w:tc>
       </w:tr>
       <!-- tabellenzeile für den unteren teil der seite -->
       <xsl:call-template name="prelim-bottom"></xsl:call-template>
     </w:tbl>
  </xsl:template>

<!-- unterer teil der beiden titelseiten -->
  <xsl:template name="prelim-bottom">

    <!-- tabellenzeile für den unteren teil der seite -->
       <w:tr>
         <w:trPr>
           <w:trHeight w:val="1615"/>
         </w:trPr>
         <w:tc>
           <w:tcPr>
             <w:tcW w:w="9212" w:type="dxa"/>
             <!-- ausrichtung der tabellenzelle am unteren rand -->
             <w:vAlign w:val="bottom"/>
           </w:tcPr>
           
           <!-- leerer Absatz, weil Tabellenzellen nicht leer sein dürfen -->
           <xsl:if test="not(prelim_volume/prelim_year) and not(prelim_volume/prelim_publisher)">
             <w:p>
               <w:pPr>
                 <w:pStyle w:val="epi-titelei-1"/>
               </w:pPr>
               <w:r>
                 <w:t></w:t>
               </w:r>
             </w:p>               
           </xsl:if>           
           
           <!-- absatz für das erscheinungsjahr -->
           <xsl:for-each select="prelim_volume/prelim_year">
             <w:p>
               <w:pPr>
                 <w:pStyle w:val="epi-titelei-2"/>
               </w:pPr>
               <w:r>
                 <w:t><xsl:value-of select=".//p"/></w:t>
               </w:r>
             </w:p>
           </xsl:for-each>
           
           <!-- absatz zur angabe des verlags -->
           <xsl:for-each select="prelim_volume/prelim_publisher">
             <w:p>
               <w:pPr>
                 <w:pStyle w:val="epi-titelei-2"/>
               </w:pPr>
               <w:r>
                 <w:t><xsl:value-of select=".//p"/></w:t>
               </w:r>
             </w:p>
           </xsl:for-each>
         </w:tc>
       </w:tr>
  </xsl:template>

<!-- seite füt sponsoring, impressu usw. -->
  <xsl:template name="impressum">
    <xsl:if test="prelim_volume/prelim_year or 
      prelim_volume/prelim_publisher or 
      prelim_volume/prelim_isbn or 
      prelim_volume/prelim_rights or 
      prelim_volume/prelim_print_note or prelim_sponsor or prelim_biblio_note">
     <!-- um den gesamten satzspiegel auszufüllen und die 
       letzte angabe am unteen seitenrand zu platzieren,
       wird eine tabelle mit nur einer spalte angelegt
     -->
     <w:tbl>
       <w:tblPr>
         <w:tblW w:w="0" w:type="auto"/>
         <w:tblBorders>
           <w:top w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
           <w:left w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
           <w:bottom w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
           <w:right w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
           <w:insideH w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
           <w:insideV w:val="none" w:sz="0" wx:bdrwidth="0" w:space="0" w:color="auto"/>
         </w:tblBorders>
         <w:tblLook w:val="04A0"/>
       </w:tblPr>
       <w:tblGrid>
         <w:gridCol w:w="9212"/>
       </w:tblGrid>
       
       <!-- auf sponsoring prüfen -->
     <xsl:if test="prelim_volume/prelim_sponsor">  <!--band/förderung  -->    
     <!-- tabellenzeile für sponsoring -->
       <w:tr>
         <w:trPr>
           <w:trHeight w:val="5817"/>
         </w:trPr>
         <w:tc>
           <w:tcPr>
             <w:tcW w:w="9212" w:type="dxa"/>
           </w:tcPr>
           <!-- absatz für sponsoring -->
           <xsl:for-each select="prelim_volume/prelim_sponsor"><!--band/förderung  -->
             <w:p>
               <w:pPr>
                 <w:pStyle w:val="epi-impressum-1"/>
               </w:pPr>
               <w:r>
                 <w:t><xsl:value-of select=".//p"/></w:t>
               </w:r>
             </w:p>
           </xsl:for-each>
         </w:tc>
       </w:tr>
     </xsl:if>
 
     <!-- auf bibliografische angaben prpfen -->
     <xsl:if test="prelim_volume/prelim_biblio_note"><!-- band/biblio -->   
       <!-- tabellenzeile für bibliogrtafische angaben -->
       <w:tr>
         <w:trPr>
           <w:trHeight w:val="4833"/>
         </w:trPr>
         <w:tc>
           <w:tcPr>
             <w:tcW w:w="9212" w:type="dxa"/>
           </w:tcPr>
           <!-- absatz für bibliografische angaben -->
           <xsl:for-each select="prelim_volume/prelim_biblio_note"><!-- band/biblio -->
             <w:p>
               <w:pPr>
                 <w:pStyle w:val="epi-impressum-2"/>
               </w:pPr>
               <w:r>
                 <w:t><xsl:value-of select=".//p"/></w:t>
               </w:r>
             </w:p>
           </xsl:for-each>
         </w:tc>
       </w:tr>
   </xsl:if>  
 
   <!-- prüfen auf vorkommen bestimmter elemente/angaben  -->
   <xsl:if test="prelim_volume/prelim_year or 
     prelim_volume/prelim_publisher or 
     prelim_volume/prelim_isbn or 
     prelim_volume/prelim_rights or 
     prelim_volume/prelim_print_note"><!-- band/jahr or band/verlag or band/isbn or band/rechte or band/druck -->
     <!-- wenn mindestens eine dieser angaben vorhanden ist, 
       wird eine weitere tabellenzeile für das copyright angelegt -->
     <w:tr>
           <w:trPr>
             <w:trHeight w:val="1843"/>
           </w:trPr>
           <w:tc>
             <w:tcPr>
               <w:tcW w:w="9212" w:type="dxa"/>
               <!-- ausrichtung am unteren rand -->
               <w:vAlign w:val="bottom"/>
             </w:tcPr>
             
             <!-- absatz für erscheinungsjahr und verlag -->
               <!--w:p>
                 <w:pPr>
                   <w:pStyle w:val="epi-impressum-2"/>
                 </w:pPr>
                 <w:r>
                   <w:t>© </w:t>
                   <w:t> <xsl:value-of select="prelim_volume/prelim_year"/></w:t--><!-- band/jahr -->
                   <!--w:t> <xsl:value-of select="prelim_volume/prelim_publisher"/></w:t--><!-- band/verlag -->
                 <!--/w:r>
               </w:p-->
              <!-- absatz für isbn -->
             <xsl:for-each select="prelim_volume/prelim_isbn"><!-- band/isbn -->
               <w:p>
                 <w:pPr>
                   <w:pStyle w:val="epi-impressum-2"/>
                 </w:pPr>
                 <w:r>
                   <w:t><xsl:value-of select=".//p"/></w:t>
                 </w:r>
               </w:p>
             </xsl:for-each>
             <!-- absatz für copyright-->
             <xsl:for-each select="prelim_volume/prelim_rights"><!-- band/rechte -->
               <w:p>
                 <w:pPr>
                   <w:pStyle w:val="epi-impressum-2"/>
                 </w:pPr>
                 <w:r>
                   <w:t><xsl:value-of select=".//p"/></w:t>
                 </w:r>
               </w:p>
             </xsl:for-each>
             <!-- absatz für printed in germany -->
             <xsl:for-each select="prelim_volume/prelim_print_note"><!-- band/druck -->
               <w:p>
                 <w:pPr>
                   <w:pStyle w:val="epi-impressum-2"/>
                 </w:pPr>
                 <w:r>
                   <w:t><xsl:value-of select=".//p"/></w:t>
                 </w:r>
               </w:p>
             </xsl:for-each>
           </w:tc>
         </w:tr></xsl:if>
     </w:tbl>
    </xsl:if>      
  </xsl:template>
</xsl:stylesheet>
