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
    <xsl:import href="di-doc-indexes.xsl"/>

<!-- in diesem stylesheet wird das inhaltsverzeichnis eine inschriftenbandes erzeugt -->
    
    <!-- inhaltsverzeichnis formatieren -->
    <xsl:template match="table_of_content">
    <xsl:comment>inhaltsverzeichnis anfang</xsl:comment> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <!-- gegebenenfalls linke leerseite einfügen -->
      <xsl:if test="preceding-sibling::*">
        <xsl:copy-of select="$p_odd-page"/>
      </xsl:if>
      <!-- überschrift des inhaltsverzeichnisses  -->
      <w:p>
        <w:pPr><w:pStyle w:val="epi-ueberschrift-1"/></w:pPr>
        <w:r><w:t><xsl:value-of select="title"/></w:t></w:r>
      </w:p>
        <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
      
      <!-- liste der abschnittsüberschriften erzeugen -->
      <!-- jeden bandabschnitt erster ebene ansteuern-->
      <xsl:for-each select="ancestor::book/*[@indexing='1']">
        <!-- mit dem attribut @indexing=1 wurde festgelegt, 
            dass der abschnitt ins inhaltsverzeichnis aufzunehmen ist -->

        <!-- den titel in einer variable speichern -->
        <xsl:variable name="v_title">
          <!-- der titel eines abschnitts kann im element <titel>, im element <lemma> oder im attribut @name stehen.
          daher alle drei instancen checken-->
          <xsl:choose>
             <xsl:when test="title">
               <xsl:value-of select="title"/>
             </xsl:when>
             <xsl:when test="lemma">
               <xsl:value-of select="lemma"/>
             </xsl:when>
             <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
           </xsl:choose>
        </xsl:variable>
        
        <!-- nach abschnittstypen differenzieren -->
        <xsl:choose>
          
          <!-- einleitung mit allen kapiteln -->
          <xsl:when test="@data_key='introduction'">
            <!-- zwischenzeile -->
            <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
            <w:p>
             <w:pPr>
                 <w:pStyle w:val="epi-inhalt-1"/>
             </w:pPr>
             
               <w:hlink>
                   <xsl:attribute name="w:bookmark"><xsl:value-of select="@id"/></xsl:attribute>
                    <w:r>
                      <w:t><xsl:value-of select="$v_title"/>&#x00A0;</w:t>
                   </w:r>
                   <!-- tab mit führenden punkten setzen, dann rechtsbündig -->
                   <w:r>
                       <w:rPr>
                           <w:spacing w:val="40"/>
                           <w:sz w:val="18"/>
                       </w:rPr>
                       <w:tab/>
                   </w:r>
                   <w:r>
                       <w:tab/>
                   </w:r>
                   <!-- feldfunktion seitenzahl und ? als platzhalter setzen -->
                   <w:r>
                       <w:fldChar w:fldCharType="begin">
                           <w:fldData xml:space="preserve">CNDJ6nn5us4RjIIAqgBLqQsCAAAACAAAAA4AAABfAFQAbwBjADUAMAAzADEANgA4ADgAMQA5AAAA</w:fldData>
                       </w:fldChar>
                   </w:r>
                   <w:r>
                       <w:instrText>PAGEREF <xsl:value-of select="@id"/> \h</w:instrText>
                   </w:r>
                   <w:r>
                       <w:fldChar w:fldCharType="separate"/>
                   </w:r>
                   <w:r>
                       <w:t>?</w:t>
                   </w:r>
                   <w:r>
                       <w:fldChar w:fldCharType="end"/>
                   </w:r>
               </w:hlink>            
             
           </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
           <!-- leerzeile -->
           <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-5pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- unterabschnitte der einleitung -->
            <xsl:for-each select=".//section">
                <!-- erstes zeichen des abschnittnamens speichern -->
                <xsl:variable name="v_nbr"><xsl:value-of select="substring(@name,1,1)"/></xsl:variable>
                <!-- abschnittstitel ermitteln und speichern -->
                <xsl:variable name="v_title2">
                <xsl:choose>
                  <xsl:when test="title">
                    <xsl:value-of select="title"/>
                  </xsl:when>
                  <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
                </xsl:choose>               
              </xsl:variable>
                <!-- nummer des abschnitts extrahieren -->
              <xsl:variable name="v_lfnr"><xsl:value-of select="substring-before($v_title2,' ')"/></xsl:variable>
                <!-- bezeichnung (titel) des abschnitts ohne nummerierung extrahieren -->
              <xsl:variable name="v_title3"><xsl:value-of select="substring-after($v_title2,' ')"/></xsl:variable>

                <!-- wenn es sich bei dem ersten zeichen des abschnittnamens 
                  um eine ziffer handelt, wird der abschnitt im inhaltsverzeichnis aufgeführt -->
                <xsl:if test="contains('123456789', $v_nbr)">
               <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <w:p>
             <w:pPr>
               <w:pStyle w:val="epi-inhalt-2"/>
             </w:pPr>
             
               <w:hlink>
                   <xsl:attribute name="w:bookmark"><xsl:value-of select="@id"/></xsl:attribute>
                   <!-- abschnittsnummer auslesen, mit punkt abschließen -->
                   <w:r>
                     <w:t><xsl:value-of select="$v_lfnr"/>  </w:t>
                   </w:r>
                   <!-- tab linksbündig setzen -->
                   <w:r>
                       <w:tab/>
                   </w:r>
                   <!-- titel auslesen -->
                   <w:r>
                     <w:t><xsl:value-of select="$v_title3"/>&#x00A0;</w:t>
                   </w:r>
                   <!-- tab mit führenden punkten setzen, dann rechtsbündig -->
                   <w:r>
                       <w:rPr>
                           <w:spacing w:val="40"/>
                           <w:sz w:val="18"/>
                       </w:rPr>
                       <w:tab/>
                   </w:r>
                   <w:r>
                       <w:tab/>
                   </w:r>                 
                   <!-- feldfunktion seitenzahl und ? als platzhalter setzen -->
                   <w:r>
                       <w:fldChar w:fldCharType="begin">
                           <w:fldData xml:space="preserve">CNDJ6nn5us4RjIIAqgBLqQsCAAAACAAAAA4AAABfAFQAbwBjADUAMAAzADEANgA4ADgAMQA5AAAA</w:fldData>
                       </w:fldChar>
                   </w:r>
                   <w:r>
                       <w:instrText>PAGEREF <xsl:value-of select="@id"/> \h</w:instrText>
                   </w:r>
                   <w:r>
                       <w:fldChar w:fldCharType="separate"/>
                   </w:r>
                   <w:r>
                       <w:t>?</w:t>
                   </w:r>
                   <w:r>
                       <w:fldChar w:fldCharType="end"/>
                   </w:r>
               </w:hlink>            
             
           </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              </xsl:if>
              </xsl:for-each>
          </xsl:when>
          
          <!-- register mit allen einzelregistern -->
          <xsl:when test="index or name()='indices'">
            <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
            <w:p>
             <w:pPr>
                 <w:pStyle w:val="epi-inhalt-1"/>
             </w:pPr>
             
               <w:hlink>
                   <xsl:attribute name="w:bookmark"><xsl:value-of select="@id"/></xsl:attribute>
                   <!-- abschnittsbezeichnung auslesen, mit punkt abschließen -->
                   <w:r>
                     <w:t><xsl:value-of select="$v_title"/>&#x00A0;</w:t>
                   </w:r>
                   <!-- tab mit führenden punkten setzen, dann rechtsbündig -->
                   <w:r>
                       <w:rPr>
                           <w:spacing w:val="40"/>
                           <w:sz w:val="18"/>
                       </w:rPr>
                       <w:tab/>
                   </w:r>
                   <w:r>
                       <w:tab/>
                   </w:r>                 
                   <!-- feldfunktion seitenzahl und ? als platzhalter setzen -->
                   <w:r>
                       <w:fldChar w:fldCharType="begin">
                           <w:fldData xml:space="preserve">CNDJ6nn5us4RjIIAqgBLqQsCAAAACAAAAA4AAABfAFQAbwBjADUAMAAzADEANgA4ADgAMQA5AAAA</w:fldData>
                       </w:fldChar>
                   </w:r>
                   <w:r>
                       <w:instrText>PAGEREF <xsl:value-of select="@id"/> \h</w:instrText>
                   </w:r>
                   <w:r>
                       <w:fldChar w:fldCharType="separate"/>
                   </w:r>
                   <w:r>
                       <w:t>?</w:t>
                   </w:r>
                   <w:r>
                       <w:fldChar w:fldCharType="end"/>
                   </w:r>
               </w:hlink>            
             
           </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- zwischenzeile -->
           <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-5pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>

            <!-- für die einzelnen register wird das register-template 
                im stylesheet di-doc-indexes.xsl aufgerufen -->
            <xsl:call-template name="register-uebersicht"></xsl:call-template>
          </xsl:when>
          
         
          <!-- die übrigen abschnitte -->
          <xsl:otherwise>
              <!-- leerzeile -->
            <w:p><w:pPr><w:pStyle w:val="epi-leerzeile-10pt"/></w:pPr></w:p>
            <w:p>
             <w:pPr>
                 <w:pStyle w:val="epi-inhalt-1"/>
             </w:pPr>
             
               <w:hlink>
                   <xsl:attribute name="w:bookmark"><xsl:value-of select="@id"/></xsl:attribute>
                   <!-- abschnittsbezeichnung auslesen, mit punkt abschließen -->
                   <w:r>
                     <w:t><xsl:value-of select="$v_title"/>&#x00A0;</w:t>
                   </w:r>
                   <!-- tab mit führenden punkten setzen, dann rechtsbündig -->
                   <w:r>
                       <w:rPr>
                           <w:spacing w:val="40"/>
                           <w:sz w:val="18"/>
                       </w:rPr>
                       <w:tab/>
                   </w:r>
                   <w:r>
                       <w:tab/>
                   </w:r>
                   <!-- feldfunktion seitenzahl und ? als platzhalter setzen -->
                   <w:r>
                       <w:fldChar w:fldCharType="begin">
                           <w:fldData xml:space="preserve">CNDJ6nn5us4RjIIAqgBLqQsCAAAACAAAAA4AAABfAFQAbwBjADUAMAAzADEANgA4ADgAMQA5AAAA</w:fldData>
                       </w:fldChar>
                   </w:r>
                   <w:r>
                       <w:instrText>PAGEREF <xsl:value-of select="@id"/> \h</w:instrText>
                   </w:r>
                   <w:r>
                       <w:fldChar w:fldCharType="separate"/>
                   </w:r>
                   <w:r>
                       <w:t>?</w:t>
                   </w:r>
                   <w:r>
                       <w:fldChar w:fldCharType="end"/>
                   </w:r>
               </w:hlink>            
           </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <!-- zwischenzeile -->
            <xsl:if test="*[@indexing='1']"><w:p><w:pPr><w:pStyle w:val="epi-leerzeile-5pt"/></w:pPr></w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text></xsl:if>
            
            <!-- unterabschnitte -->
            <xsl:for-each select="*[@indexing='1']">
              <xsl:variable name="v_title3">
                <xsl:choose>
                <xsl:when test="title">
                  <xsl:value-of select="title"/>
                </xsl:when>
                <xsl:when test="lemma">
                  <xsl:value-of select="lemma"/>
                </xsl:when>
                <xsl:otherwise><xsl:value-of select="@name"/></xsl:otherwise>
              </xsl:choose>
              </xsl:variable>
            <w:p>
             <w:pPr>
                 <w:pStyle w:val="epi-inhalt-2"/>
             </w:pPr>
             
               <w:hlink>
                   <xsl:attribute name="w:bookmark"><xsl:value-of select="@id"/></xsl:attribute>
                   <!-- abschnittsbezeichnung auslesen, mit punkt abschließen -->
                   <w:r>
                     <w:t><xsl:value-of select="$v_title3"/>&#x00A0;</w:t>
                   </w:r>
                   <!-- tab mit führenden punkten setzen, dann rechtsbündig -->
                   <w:r>
                       <w:rPr>
                           <w:spacing w:val="40"/>
                           <w:sz w:val="18"/>
                       </w:rPr>
                       <w:tab/>
                   </w:r>                
                   <w:r>
                       <w:tab/>
                   </w:r>
                   <!-- feldfunktion seitenzahl und ? als platzhalter setzen -->
                   <w:r>
                       <w:fldChar w:fldCharType="begin">
                           <w:fldData xml:space="preserve">CNDJ6nn5us4RjIIAqgBLqQsCAAAACAAAAA4AAABfAFQAbwBjADUAMAAzADEANgA4ADgAMQA5AAAA</w:fldData>
                       </w:fldChar>
                   </w:r>
                   <w:r>
                       <w:instrText>PAGEREF <xsl:value-of select="@id"/> \h</w:instrText>
                   </w:r>
                   <w:r>
                       <w:fldChar w:fldCharType="separate"/>
                   </w:r>
                   <w:r>
                       <w:t>?</w:t>
                   </w:r>
                   <w:r>
                       <w:fldChar w:fldCharType="end"/>
                   </w:r>
               </w:hlink>            
             </w:p> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each>
          </xsl:otherwise>
        </xsl:choose>
      </xsl:for-each>
    </wx:sect> <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    <xsl:comment>inhaltsverzeichnis ende</xsl:comment> 
  </xsl:template>    
</xsl:stylesheet>