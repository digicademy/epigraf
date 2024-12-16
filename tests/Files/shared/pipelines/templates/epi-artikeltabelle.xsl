<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
xmlns:axf="http://www.antennahouse.com/names/XSL/Extensions" 
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
xmlns:msxsl="urn:schemas-microsoft-com:xslt" 
xmlns:exsl="http://exslt.org/common"
>

  <!-- variablen und parameter müssen mit exsl:node-set(object) ausgelsesen werden-->
  

  <xsl:template match="/">
    <!--Verarbeitungsanweisung zum Erzeugen von <?mso-application progid="Word.Document"?> -->
    <xsl:processing-instruction name="mso-application">
    <xsl:text>progid="Excel.Sheet"</xsl:text>
    </xsl:processing-instruction>
    
    <Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
      xmlns:o="urn:schemas-microsoft-com:office:office"
      xmlns:x="urn:schemas-microsoft-com:office:excel"
      xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
      xmlns:html="http://www.w3.org/TR/REC-html40">
      <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
        <Author>epigraf-export</Author>
        <LastAuthor>epigraf-export</LastAuthor>
        <Created></Created>
        <LastSaved></LastSaved>
        <Version></Version>
      </DocumentProperties>
      <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
        <AllowPNG/>
      </OfficeDocumentSettings>
      <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
        <WindowHeight>12585</WindowHeight>
        <WindowWidth>24915</WindowWidth>
        <WindowTopX>120</WindowTopX>
        <WindowTopY>30</WindowTopY>
        <ProtectStructure>False</ProtectStructure>
        <ProtectWindows>False</ProtectWindows>
      </ExcelWorkbook>
      <Styles>
        <Style ss:ID="Default" ss:Name="Normal">
          <Alignment ss:Vertical="Bottom"/>
          <Borders/>
          <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
          <Interior/>
          <NumberFormat/>
          <Protection/>
        </Style>
        <Style ss:ID="s62">
          <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
            ss:Bold="1"/>
        </Style>
      </Styles>
      <Worksheet ss:Name="Artikelliste">
        <Table>
<!--          <Column ss:Index="4" ss:AutoFitWidth="0" ss:Width="102"/>
          <Column ss:Index="14" ss:AutoFitWidth="0" ss:Width="111.75"/>
          <Column ss:AutoFitWidth="0" ss:Width="117.75"/>
          <Column ss:Index="17" ss:AutoFitWidth="0" ss:Width="102"/>-->
          <Row>
            <Cell ss:StyleID="s62"><Data ss:Type="String">Signatur</Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">Projekt</Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">Projektkürzel</Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">Bearbeitungsstatus</Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">erstellt wann</Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">erstellt durch</Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">letzte Änderung wann</Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">letzte Änderung durch</Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">Überlieferungszustand</Data>
              <Comment ss:Author="Epigraf 4">
                <ss:Data xmlns="http://www.w3.org/TR/REC-html40">
                  <B>
                    <Font html:Face="Tahoma" x:Family="Swiss" html:Size="9" html:Color="#000000">Hinweis:</Font>
                  </B>
                  <Font html:Face="Tahoma" x:Family="Swiss" html:Size="9" html:Color="#000000">&#10;Das Fehlen des Eintrags wird durch das Zeichen ■ angezeigt.</Font>
                </ss:Data>
              </Comment>
            </Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">Titel</Data>
              <Comment ss:Author="Epigraf 4">
                <ss:Data xmlns="http://www.w3.org/TR/REC-html40">
                  <B>
                    <Font html:Face="Tahoma" x:Family="Swiss" html:Size="9" html:Color="#000000">Hinweis:</Font>
                  </B>
                  <Font html:Face="Tahoma" x:Family="Swiss" html:Size="9" html:Color="#000000">&#10;Der Titel wird als Bildunterschrift in die Metadaten der Abbildungen eingespielt. Das Fehlen des Titels wird durch das Zeichen ■ angezeigt.</Font>
                </ss:Data>
              </Comment>            
            
            </Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">Standort</Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">Inschriftenträger</Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">Material</Data></Cell>
            <Cell ss:StyleID="s62">
              <Data ss:Type="String">Datierung des Objekts</Data>
              <Comment ss:Author="Epigraf 4">
                <ss:Data xmlns="http://www.w3.org/TR/REC-html40">
                  <B>
                    <Font html:Face="Tahoma" x:Family="Swiss" html:Size="9" html:Color="#000000">Hinweis:</Font>
                  </B>
                  <Font html:Face="Tahoma" x:Family="Swiss" html:Size="9" html:Color="#000000">&#10;Das Fehlen einer Datierung wird durch das Zeichen ■ angezeigt.</Font>
                </ss:Data>
              </Comment>            
            </Cell>
            <Cell ss:StyleID="s62">
              <Data ss:Type="String">Datierungsindex</Data>
              <Comment ss:Author="Epigraf 4">
                <ss:Data xmlns="http://www.w3.org/TR/REC-html40">
                  <B>
                    <Font html:Face="Tahoma" x:Family="Swiss" html:Size="9" html:Color="#000000">Hinweis:</Font>
                  </B>
                  <Font html:Face="Tahoma" x:Family="Swiss" html:Size="9" html:Color="#000000">&#10;Nach dieser Spalte kann man die Tabelle chronologisch sortieren.</Font>
                </ss:Data>
              </Comment>
            </Cell>
            <Cell ss:StyleID="s62">
              <Data ss:Type="String">Datierung der Inschriften</Data>
              <Comment ss:Author="Epigraf 4">
                <ss:Data xmlns="http://www.w3.org/TR/REC-html40">
                  <B>
                    <Font html:Face="Tahoma" x:Family="Swiss" html:Size="9" html:Color="#000000">Hinweis:</Font>
                  </B>
                  <Font html:Face="Tahoma" x:Family="Swiss" html:Size="9" html:Color="#000000">&#10;Das Fehlen einer Datierung wird durch das Zeichen ■ angezeigt.</Font>
                </ss:Data>
              </Comment>
            </Cell>
<!--            <Cell ss:StyleID="s62"><Data ss:Type="String">anzahl Abbildungen</Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">Anzahl Inschriften</Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">Anzahl Inschriftenteile </Data></Cell>
            <Cell ss:StyleID="s62"><Data ss:Type="String">Anzahl Bearbeitungen (Varianten)</Data></Cell>-->
            <Cell ss:StyleID="s62"><Data ss:Type="String">ID</Data></Cell>
<!--            <Cell ss:StyleID="s62"><Data ss:Type="String">Artikelnummer</Data></Cell>-->
          </Row>
          
          <xsl:for-each select="/book/catalog/article">
            <Row>
              <!-- artikel-signatur -->
              <Cell>
                <Data ss:Type="String"><xsl:value-of select="signature"/></Data>
              </Cell>
              <!-- projektname -->
              <Cell>
                <Data ss:Type="String"><xsl:value-of select="ancestor::book/project/name"/></Data>
              </Cell>
              <!-- projektkuerzel -->
              <Cell><Data ss:Type="String"><xsl:value-of select="ancestor::book/project/signature"/></Data></Cell>
              
              <!-- Bearbeitungsstatus -->
              <Cell>
                <Data ss:Type="String"><xsl:value-of select="section[@type='datensatz']/fields/status"/></Data>
              </Cell>
              
              <!-- erstellt -->
              <Cell>
                <Data ss:Type="String"><xsl:value-of select="created"/></Data>
              </Cell>
              
              <!-- erstellt von -->
              <Cell>
                <Data ss:Type="String"><xsl:value-of select="creator/name"/></Data>
              </Cell>
              
              <!-- letzte Änderung -->
              <Cell>
                <Data ss:Type="String"><xsl:value-of select="modified"/></Data>
              </Cell>
              
              <!-- letzte Änderung von -->
              <Cell>
                <Data ss:Type="String"><xsl:value-of select="modifier/name"/></Data>
              </Cell>
              
              <!-- Überlieferungszustand -->
              <Cell>
                <Data ss:Type="String">
                  <xsl:value-of select="sections/section[@sectiontype='conditions']/items/item[@itemtype='conditions']/property[@propertytype='conditions']/name"/>
                  <xsl:if test="not(sections/section[@sectiontype='conditions']/items/item[@itemtype='conditions']/property[@propertytype='conditions']/name[text()])">■</xsl:if>
                </Data>
                  
              </Cell>
              
              <!-- Titel -->
              <Cell>
                <Data ss:Type="String">
                  <xsl:value-of select="name"/>
                  <xsl:if test="not(name[text()])">■</xsl:if>
                </Data>
              </Cell>
              
              <!-- Standort -->
              <Cell>
                <Data ss:Type="String">
                            <xsl:for-each select="standorte/standort">
                            <xsl:for-each select="lemma"><xsl:value-of select="."/><xsl:if test="following-sibling::lemma">, </xsl:if></xsl:for-each>
                            <xsl:if test="following-sibling::standort">; </xsl:if>
                            </xsl:for-each>
                    </Data>
              </Cell>
              
              <!-- Inschriftenträger -->
              <Cell>
                <Data ss:Type="String"><xsl:value-of select="objekt"/></Data>
              </Cell>
              
              <!-- Material -->
              <Cell>
                <Data ss:Type="String"><xsl:value-of select="sections/section[@sectiontype='materials']/items/item[@itemtype='materials']/property[@propertytype='materials']/name"/></Data>
              </Cell>
              
              <!-- Datierung  des Objekts -->
              <Cell>
                <Data ss:Type="String">
                  <xsl:for-each select="sections/section[@sectiontype='conditions']/items/item[@itemtype='conditions']/date_value"><xsl:value-of select="."/><xsl:if test="not(text())">■</xsl:if></xsl:for-each>
                  </Data>
              </Cell>
              
              <!-- Datierungsindex -->
              <Cell>
                <Data ss:Type="String"><xsl:value-of select="sections/section[@sectiontype='conditions']/items/item[@itemtype='conditions']/date_sort"/></Data>
              </Cell>
              
              <!-- Datierung der Inschriften -->
              <Cell>
                <Data ss:Type="String">
                    <xsl:call-template name="datierungen"></xsl:call-template>
                </Data>
              </Cell>
              
<!--              <!-\-  -\->
              <Cell><Data ss:Type="String">anzahl Abbildungen</Data></Cell>
              
              <!-\-  -\->
              <Cell><Data ss:Type="String">Anzahl Inschriften</Data></Cell>
              
              <!-\-  -\->
              <Cell><Data ss:Type="String">Anzahl Inschriftenteile </Data></Cell>
              
              <!-\-  -\->
              <Cell><Data ss:Type="String">Anzahl Bearbeitungen (Varianten)</Data></Cell>-->
              
              <!-- ID -->
              <Cell>
                <Data ss:Type="String"><xsl:value-of select="@table_id"/></Data>
              </Cell>
              
              <!--  
              <Cell><Data ss:Type="String">Artikelnummer</Data></Cell>-->
            </Row>
          </xsl:for-each>

        </Table>
        <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
          <PageSetup>
            <Header x:Margin="0.3"/>
            <Footer x:Margin="0.3"/>
            <PageMargins x:Bottom="0.78740157499999996" x:Left="0.7" x:Right="0.7"
              x:Top="0.78740157499999996"/>
          </PageSetup>
          <Print>
            <ValidPrinterInfo/>
            <PaperSizeIndex>9</PaperSizeIndex>
            <HorizontalResolution>600</HorizontalResolution>
            <VerticalResolution>600</VerticalResolution>
          </Print>
          <Selected/>
          <Panes>
            <Pane>
              <Number>3</Number>
              <ActiveRow>1</ActiveRow>
              <ActiveCol>10</ActiveCol>
            </Pane>
          </Panes>
          <ProtectObjects>False</ProtectObjects>
          <ProtectScenarios>False</ProtectScenarios>
        </WorksheetOptions>
      </Worksheet>
    </Workbook>
  </xsl:template>
  
<xsl:template name="datierungen">
  <xsl:param name="datierung1">
  <xsl:for-each select=".//section[@sectiontype='inscriptiontext']/items/item[@itemtype='transcriptions']/date_value">
    <datierung><xsl:value-of select="."/><xsl:if test="not(text())">■</xsl:if></datierung>
  </xsl:for-each>
  </xsl:param>
  <xsl:for-each select="$datierung1/datierung">
    <xsl:value-of select="."/><xsl:if test="following-sibling::datierung"><xsl:text> | </xsl:text></xsl:if>
  </xsl:for-each>
</xsl:template>

</xsl:stylesheet>

