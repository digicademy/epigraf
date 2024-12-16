<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
    xmlns="urn:schemas-microsoft-com:office:spreadsheet" 
    xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" 
    xmlns:dc="http://purl.org/dc/elements/1.1/" 
    xmlns:dom="http://www.w3.org/2001/xml-events" 
    xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" 
    xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" 
    xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" 
    xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" 
    xmlns:html="http://www.w3.org/TR/REC-html40"
    xmlns:math="http://www.w3.org/1998/Math/MathML" 
    xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" 
    xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" 
    xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2" 
    xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" 
    xmlns:ooo="http://openoffice.org/2004/office" 
    xmlns:oooc="http://openoffice.org/2004/calc" 
    xmlns:ooow="http://openoffice.org/2004/writer" 
    xmlns:rdfa="http://docs.oasis-open.org/opendocument/meta/rdfa#" 
    xmlns:rpt="http://openoffice.org/2005/report" 
    xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" 
    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" 
    xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" 
    xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" 
    xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" 
    xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns:xforms="http://www.w3.org/2002/xforms" 
    xmlns:xlink="http://www.w3.org/1999/xlink" 
    xmlns:xsd="http://www.w3.org/2001/XMLSchema" 
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    >
    
    <xsl:output method="xml" version="1.0" omit-xml-declaration="no" indent="yes" encoding="UTF-8"/>

<xsl:template match="book">
    <xsl:param name="articles"><xsl:value-of select="count(article)"/></xsl:param>
    <xsl:param name="expendet_rows"><xsl:value-of select="$articles + 5"/></xsl:param>
    <xsl:processing-instruction name="mso-application">
            <xsl:text>progid="Excel.Sheet"</xsl:text>
        </xsl:processing-instruction>
    <Workbook>
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>Jürgen Herold</Author>
  <LastAuthor>Jürgen Herold</LastAuthor>
  <Created>2019-04-05T09:37:16Z</Created>
  <Version>12.00</Version>
 </DocumentProperties>
 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
  <WindowHeight>10005</WindowHeight>
  <WindowWidth>10005</WindowWidth>
  <WindowTopX>120</WindowTopX>
  <WindowTopY>135</WindowTopY>
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
   <Alignment ss:Vertical="Top"/>
  </Style>
  <Style ss:ID="s63">
   <Alignment ss:Vertical="Top" ss:WrapText="1"/>
  </Style>
  <Style ss:ID="s64">
   <Alignment ss:Vertical="Top"/>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"
    ss:Bold="1"/>
  </Style>
  <Style ss:ID="s76">
   <Alignment ss:Vertical="Top"/>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#376091"
    ss:Bold="1"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Abbildungen">
  <Table ss:ExpandedColumnCount="5" x:FullColumns="1"
   x:FullRows="1" ss:StyleID="s62" ss:DefaultColumnWidth="60"
   ss:DefaultRowHeight="15">
      <xsl:attribute name="ss:ExpandedRowCount"><xsl:value-of select="$expendet_rows"/></xsl:attribute>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="39.75"/>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="249.75"/>
   <Column ss:StyleID="s63" ss:AutoFitWidth="0" ss:Width="300"/>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="150" ss:Span="1"/>
   <Row ss:AutoFitHeight="0">
    <Cell ss:StyleID="s64"><Data ss:Type="String">Lfnr.</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Signatur</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Abbildungen</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Datierung</Data></Cell>
    <Cell ss:StyleID="s64"><Data ss:Type="String">Titel</Data></Cell>
   </Row>
   <Row ss:AutoFitHeight="0">
    <Cell ss:Index="3" ss:StyleID="s76"><Data ss:Type="String">1. in dieser Spalte zu den Artikeln die betreffenden Abbildungsnummern eintragen</Data></Cell>
   </Row>
   <Row ss:AutoFitHeight="0">
    <Cell ss:Index="3" ss:StyleID="s76"><Data ss:Type="String">2. die Datei als &quot;XML-Kalkulationstabelle 2003&quot; unter diesem Namen Speichern: &quot;plates_<xsl:value-of select="project/signature"/>.xml&quot;</Data></Cell>
   </Row>
   <Row ss:AutoFitHeight="0">
    <Cell ss:Index="3" ss:StyleID="s76"><Data ss:Type="String">3. die gespeicherte Datei auf EpigrafWeb in den Ordner &quot;Dateien/lists&quot; hochladen</Data></Cell>
   </Row>
      
      
        <!--
                    <Cell ss:StyleID="s64"><Data ss:Type="String">No..</Data></Cell>
                    <Cell ss:StyleID="s64"><Data ss:Type="String">Signature</Data></Cell>                    
                    <Cell ss:StyleID="s64"><Data ss:Type="String">Images:&#10;1. in this column for the articles, enter the relevant figure numbers &#10;&#10;2. Save the file as &quot;XML-Kalkulationstabelle 2003&quot; under this name: plates_<xsl:value-of select="project/kuerzel"/>.xml&#10;&#10;3. upload the saved file to EpigrafWeb in the folder &quot;Dateien/lists&quot;</Data></Cell>
                    <Cell ss:StyleID="s64"><Data ss:Type="String">Titel</Data></Cell>  
      -->
     
                <xsl:for-each select="article[@articletype='epi-article']">
<!-- die auskommentierten zellen dienten der erstellung einer konkordanz der grabplatten im bestand wismar -->
                    <Row>
                        <Cell><Data ss:Type="Number"><xsl:number level="single" count="article"/></Data></Cell>
                        <Cell><Data ss:Type="String"><xsl:value-of select="sections/section[@sectiontype='signatures']/items/item[1]/value"/></Data></Cell>
<!--                  <Cell><Data ss:Type="String"><xsl:value-of select="sections/section[@sectiontype='signatures']/items/item[contains(value,'.ct')]/value"/></Data></Cell>-->
                        <Cell><Data ss:Type="String"> </Data></Cell>
<!--                  <Cell><Data ss:Type="String"> <xsl:value-of select="sections/section[@sectiontype='locations']/items/item/property[1]/lemma[1]"/></Data></Cell>-->                        
                        <Cell><Data ss:Type="String"><xsl:value-of select="sections/section[@sectiontype='conditions']/items/item/date_value"/></Data></Cell>
                        <Cell><Data ss:Type="String"><xsl:value-of select="name"/></Data></Cell>
                    </Row>
                </xsl:for-each>
            </Table>
<WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.4921259845"/>
    <Footer x:Margin="0.4921259845"/>
    <PageMargins x:Bottom="0.984251969" x:Left="0.78740157499999996"
     x:Right="0.78740157499999996" x:Top="0.984251969"/>
   </PageSetup>
   <Unsynced/>
   <Print>
    <ValidPrinterInfo/>
    <PaperSizeIndex>9</PaperSizeIndex>
    <HorizontalResolution>-3</HorizontalResolution>
    <VerticalResolution>0</VerticalResolution>
   </Print>
   <Selected/>
   <Panes>
    <Pane>
     <Number>3</Number>
     <ActiveRow><xsl:value-of select="$expendet_rows"/></ActiveRow>
     <ActiveCol>2</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>               
        </Worksheet>
    </Workbook>        
    
    
    
    
<!--<xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <objektliste>
        <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <xsl:for-each select="//article[@type='objekt']">	
            <xsl:number level="any" count="article"/>
            <xsl:text disable-output-escaping="yes">&#x0009;</xsl:text>
            <xsl:value-of select="section[@type='signaturen']/tables/table/item[1]/bezeichnung"/>
            <xsl:text disable-output-escaping="yes">&#x0009;</xsl:text>
            <xsl:value-of select="section[@type='standorte']/tables/table/item[1]/referenz/name"/>
            <xsl:text disable-output-escaping="yes">&#x0009;</xsl:text>
            <xsl:value-of select="section[@type='datensatz']/fields/titel"/>        
            <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        </xsl:for-each>
    </objektliste>-->
</xsl:template>   
</xsl:stylesheet>