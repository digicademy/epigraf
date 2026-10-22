<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xs="http://www.w3.org/2001/XMLSchema"
 xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
 xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml"
 xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w10="urn:schemas-microsoft-com:office:word"
 xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core"
 xmlns:aml="http://schemas.microsoft.com/aml/2001/core"
 xmlns:wx="http://schemas.microsoft.com/office/word/2003/auxHint"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882"
 xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2"
 xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0"
 xmlns:ooo="http://openoffice.org/2004/office"
 xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0"
 xmlns:xlink="http://www.w3.org/1999/xlink"
 xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0"
 xmlns:dc="http://purl.org/dc/elements/1.1/"
 xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0"
 xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0"
 xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0"
 xmlns:rpt="http://openoffice.org/2005/report"
 xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0"
 xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0"
 xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0"
 xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0"
 xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0"
 xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0"
 xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc"
 xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2"
 xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:tableooo="http://openoffice.org/2009/table"
 xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0"
 xmlns:drawooo="http://openoffice.org/2010/draw"
 xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0"
 xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0"
 xmlns:math="http://www.w3.org/1998/Math/MathML"
 xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0"
 xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0"
 xmlns:formx="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0"
 xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xsd="http://www.w3.org/2001/XMLSchema"
 xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xhtml="http://www.w3.org/1999/xhtml"
 xmlns:grddl="http://www.w3.org/2003/g/data-view#" xmlns:css3t="http://www.w3.org/TR/css3-text/"
 xmlns:officeooo="http://openoffice.org/2009/office" office:version="1.3"
 office:mimetype="application/vnd.oasis.opendocument.text">


 <xsl:output method="xml" version="1.0" indent="yes" encoding="UTF-8"/>

 <xsl:template name="font-face-decls">
  <office:font-face-decls>
   <style:font-face style:name="Courier New" svg:font-family="&apos;Courier New&apos;"
    style:font-family-generic="modern" style:font-pitch="fixed"/>
   <style:font-face style:name="Times New Roman" svg:font-family="&apos;Times New Roman&apos;"
    style:font-family-generic="roman" style:font-pitch="variable"/>
  </office:font-face-decls>
 </xsl:template>

 <xsl:template name="master-styles">
  <office:master-styles>
   <style:master-page style:name="Standard" style:page-layout-name="pm1" draw:style-name="dp1"/>
   <style:master-page style:name="MP0" style:page-layout-name="pm2" draw:style-name="dp1">
    <style:header>
     <text:p text:style-name="epi-normal-1"/>
    </style:header>
    <style:footer>
     <text:p text:style-name="P1">
      <text:page-number text:select-page="current">2</text:page-number>
     </text:p>
     <text:p text:style-name="epi-normal-1"/>
    </style:footer>
   </style:master-page>
  </office:master-styles>
 </xsl:template>

 <xsl:template name="styles">
    <office:styles>
   <style:default-style style:family="graphic">
    <style:graphic-properties draw:stroke="solid" svg:stroke-width="0.071cm"
     svg:stroke-color="#385d8a" draw:fill-color="#4f81bd" fo:wrap-option="wrap"
     draw:shadow-offset-x="0.3cm" draw:shadow-offset-y="0.3cm"
     draw:start-line-spacing-horizontal="0.283cm" draw:start-line-spacing-vertical="0.283cm"
     draw:end-line-spacing-horizontal="0.283cm" draw:end-line-spacing-vertical="0.283cm"
     style:writing-mode="lr-tb" style:flow-with-text="false"/>
    <style:paragraph-properties fo:line-height="100%" fo:text-align="start"
     style:text-autospace="ideograph-alpha" style:line-break="strict" loext:tab-stop-distance="0cm"
     style:font-independent-line-spacing="false">
     <style:tab-stops/>
    </style:paragraph-properties>
    <style:text-properties fo:font-variant="normal" fo:text-transform="none"
     style:use-window-font-color="true" loext:opacity="0%" style:text-outline="false"
     style:text-line-through-style="none" style:text-line-through-type="none"
     style:text-position="0% 100%" style:font-name="Times New Roman" fo:font-size="10pt"
     fo:letter-spacing="normal" fo:language="de" fo:country="DE" fo:font-style="normal"
     fo:text-shadow="none" style:text-underline-style="none" fo:font-weight="normal"
     style:letter-kerning="false" style:font-name-asian="Times New Roman"
     style:font-size-asian="10pt" style:language-asian="de" style:country-asian="DE"
     style:font-style-asian="normal" style:font-weight-asian="normal"
     style:font-name-complex="Times New Roman" style:font-size-complex="10pt"
     style:language-complex="he" style:country-complex="IL" style:font-style-complex="normal"
     style:font-weight-complex="normal" style:text-emphasize="none" style:text-scale="100%"
     style:font-relief="none"/>
   </style:default-style>
   <style:default-style style:family="paragraph">
    <style:paragraph-properties fo:line-height="100%" fo:text-align="start"
     style:justify-single-word="false" fo:keep-together="auto" fo:orphans="2" fo:widows="2"
     fo:hyphenation-ladder-count="no-limit" fo:break-before="auto" fo:break-after="auto"
     fo:background-color="transparent" fo:padding="0cm" fo:border="none" style:shadow="none"
     fo:keep-with-next="auto" text:number-lines="true" text:line-number="0"
     style:text-autospace="ideograph-alpha" style:punctuation-wrap="hanging"
     style:line-break="strict" style:tab-stop-distance="1.249cm" style:snap-to-layout-grid="true"
     style:writing-mode="lr-tb">
     <style:background-image/>
    </style:paragraph-properties>
    <style:text-properties fo:font-variant="normal" fo:text-transform="none"
     style:use-window-font-color="true" loext:opacity="0%" style:text-outline="false"
     style:text-line-through-style="none" style:text-line-through-type="none"
     style:text-position="0% 100%" style:font-name="Times New Roman" fo:font-size="10pt"
     fo:letter-spacing="normal" fo:language="de" fo:country="DE" fo:font-style="normal"
     fo:text-shadow="none" style:text-underline-style="none" fo:font-weight="normal"
     style:letter-kerning="false" fo:background-color="transparent"
     style:font-name-asian="Times New Roman" style:font-size-asian="10pt" style:language-asian="de"
     style:country-asian="DE" style:font-style-asian="normal" style:font-weight-asian="normal"
     style:font-name-complex="Times New Roman" style:font-size-complex="10pt"
     style:language-complex="he" style:country-complex="IL" style:font-style-complex="normal"
     style:font-weight-complex="normal" style:text-emphasize="none" style:text-combine="none"
     style:text-scale="100%" style:font-relief="none" fo:hyphenate="true"
     fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:default-style>
   <style:default-style style:family="table">
    <style:table-properties table:border-model="collapsing"/>
   </style:default-style>
   <style:default-style style:family="table-row">
    <style:table-row-properties fo:keep-together="auto"/>
   </style:default-style>


   <style:style style:name="cil-table-inscript" style:family="table">
    <style:table-properties style:width="13.353cm" fo:margin-left="0cm" table:align="left"/>
   </style:style> 
   
   <style:style style:name="cil-table_left_row-inscript" style:family="table-column">
    <style:table-column-properties style:column-width="0.653cm"/>
   </style:style>
   <style:style style:name="cil-table_right_row-inscript" style:family="table-column">
    <style:table-column-properties style:column-width="12.7cm"/>
   </style:style>
   
   <style:style style:name="cil-table_cell-inscript" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.018cm" fo:padding-right="0.018cm" fo:padding-top="0cm" fo:padding-bottom="0.035cm" fo:border="none"/>
   </style:style>  



   <style:style style:name="Standard" style:family="paragraph" style:class="text">
    <style:paragraph-properties fo:line-height="0.388cm" fo:text-align="justify"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties style:letter-kerning="true" style:language-complex="ar"
     style:country-complex="SA" fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="Heading" style:family="paragraph" style:parent-style-name="Standard"
    style:next-style-name="cil-normal-1" style:class="text">
    <style:paragraph-properties fo:margin-top="0.423cm" fo:margin-bottom="0.212cm"
     style:contextual-spacing="false" fo:keep-with-next="always"/>
    <style:text-properties style:font-name="Times New Roman"
     fo:font-family="&apos;Times New Roman&apos;" style:font-family-generic="swiss"
     style:font-pitch="variable" fo:font-size="14pt" style:font-name-asian="Microsoft YaHei"
     style:font-family-asian="&apos;Microsoft YaHei&apos;" style:font-family-generic-asian="system"
     style:font-pitch-asian="variable" style:font-size-asian="14pt"
     style:font-name-complex="Lucida Sans1" style:font-family-complex="&apos;Lucida Sans&apos;"
     style:font-family-generic-complex="system" style:font-pitch-complex="variable"
     style:font-size-complex="14pt"/>
   </style:style>
   <style:style style:name="cil-normal-1" style:display-name="Text body" style:family="paragraph"
    style:parent-style-name="Standard" style:class="text">
    <style:paragraph-properties fo:margin-top="0cm" fo:margin-bottom="0.247cm"
     style:contextual-spacing="false" fo:line-height="115%"/>
   </style:style>
   
   <style:style style:name="cil-section" style:family="paragraph" style:parent-style-name="Standard" style:master-page-name="">
    <style:paragraph-properties fo:margin-left="0.499cm" fo:margin-right="0cm" fo:line-height="0.388cm" fo:text-align="start" style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit" fo:text-indent="0cm" style:auto-text-indent="false" style:page-number="auto" fo:background-color="transparent"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>   
   
   <style:style style:name="List" style:family="paragraph" style:parent-style-name="cil-normal-1"
    style:class="list">
    <style:text-properties style:font-size-asian="12pt" style:font-name-complex="Times New Roman"
     style:font-family-complex="&apos;Times New Roman&apos;"
     style:font-family-generic-complex="swiss"/>
   </style:style>
   <style:style style:name="cil-caption" style:family="paragraph" style:parent-style-name="Standard"
    style:class="extra">
    <style:paragraph-properties fo:margin-top="0.4cm" fo:margin-bottom="0.2cm"
     style:contextual-spacing="false" text:number-lines="false" text:line-number="0"/>
    <style:text-properties fo:color="#006699" loext:opacity="100%" fo:font-size="11pt" style:font-size-asian="11pt"
     style:font-name-complex="Times New Roman"
     style:font-family-complex="&apos;Times New Roman&apos;"
     style:font-family-generic-complex="swiss" style:font-size-complex="11pt"
     />
    <!-- style:text-underline-style="solid" style:text-underline-width="auto"
     style:text-underline-color="#000000" style:text-underline-mode="continuous" -->
   </style:style>
   <style:style style:name="Index" style:family="paragraph" style:parent-style-name="Standard"
    style:class="index">
    <style:paragraph-properties text:number-lines="false" text:line-number="0"/>
    <style:text-properties style:font-size-asian="12pt" style:font-name-complex="Times New Roman"
     style:font-family-complex="&apos;Times New Roman&apos;"
     style:font-family-generic-complex="swiss"/>
   </style:style>
   <style:style style:name="cil-normal-1" style:family="paragraph">
    <style:paragraph-properties fo:line-height="0.388cm" fo:text-align="justify"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties style:letter-kerning="true" style:language-complex="ar"
     style:country-complex="SA" fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-notiz" style:family="paragraph" style:parent-style-name="Standard">
    <style:paragraph-properties fo:text-align="start" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties style:font-name="Courier New" fo:font-family="&apos;Courier New&apos;"
     style:font-family-generic="modern" style:font-pitch="fixed" fo:hyphenate="true"
     fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>

   <style:style style:name="cil-signature" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-top="0.1cm" fo:line-height="0.25cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:font-size="8pt"/>
   </style:style>

   <style:style style:name="cil-signature-hidden" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-top="0.1cm" fo:line-height="0.25cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties text:display="none" fo:font-size="8pt"/>
   </style:style>

   <style:style style:name="cil-kopfzeile" style:family="paragraph"
    style:parent-style-name="cil-normal-1" style:default-outline-level="2">
    <style:paragraph-properties fo:text-align="start" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit" fo:keep-with-next="always">
     <style:tab-stops>
      <style:tab-stop style:position="7.5cm" style:type="center"/>
      <style:tab-stop style:position="17.0cm" style:type="right"/>
     </style:tab-stops>
    </style:paragraph-properties>

    <style:text-properties fo:font-size="11pt" fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>

   <style:style style:name="cil-kopfzeile-balken-oben" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:line-height="0.176cm" fo:hyphenation-ladder-count="no-limit"
     fo:padding-left="0cm" fo:padding-right="0cm" fo:padding-top="0.035cm" fo:padding-bottom="0cm"
     fo:border-left="none" fo:border-right="none" fo:border-top="0.74pt solid #000000"
     fo:border-bottom="none" style:shadow="none" fo:keep-with-next="always"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-kopfzeile-balken-unten" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:line-height="0.176cm" fo:hyphenation-ladder-count="no-limit"
     fo:padding-left="0cm" fo:padding-right="0cm" fo:padding-top="0cm" fo:padding-bottom="0.035cm"
     fo:border-left="none" fo:border-right="none" fo:border-top="none"
     fo:border-bottom="0.74pt solid #000000" style:shadow="none" fo:keep-with-next="always"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-seitenkopf" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:hyphenation-ladder-count="no-limit">
     <style:tab-stops>
      <style:tab-stop style:position="8.001cm" style:type="center"/>
      <style:tab-stop style:position="16.002cm" style:type="right"/>
     </style:tab-stops>
    </style:paragraph-properties>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-seitenfuss" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:hyphenation-ladder-count="no-limit">
     <style:tab-stops>
      <style:tab-stop style:position="8.001cm" style:type="center"/>
      <style:tab-stop style:position="16.002cm" style:type="right"/>
     </style:tab-stops>
    </style:paragraph-properties>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-marginalie" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:text-align="start" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-beschreibung" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-abschnitt" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:text-align="start" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties style:text-underline-style="solid" style:text-underline-width="auto"
     style:text-underline-color="#000000" style:text-underline-mode="continuous"
     style:text-overline-mode="continuous" style:text-line-through-mode="continuous"
     fo:hyphenate="true" fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-wappen" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:text-align="start" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-verse" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:text-align="start" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-gliederung-1" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-left="1.251cm" fo:margin-top="0cm"
     fo:margin-bottom="0.106cm" style:contextual-spacing="false" fo:text-align="start"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit"
     fo:text-indent="-1.251cm" style:auto-text-indent="false">
     <style:tab-stops/>
    </style:paragraph-properties>
    <style:text-properties fo:font-style="italic" style:font-style-asian="italic"
     fo:hyphenate="true" fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-gliederung-2" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-left="1.251cm" fo:margin-top="0cm"
     fo:margin-bottom="0.106cm" style:contextual-spacing="false" fo:text-align="start"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit">
     <style:tab-stops/>
    </style:paragraph-properties>
    <style:text-properties fo:font-style="italic" style:font-style-asian="italic"
     fo:hyphenate="true" fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-gliederung-3" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-left="1.251cm" fo:margin-top="0cm"
     fo:margin-bottom="0.106cm" style:contextual-spacing="false" fo:text-align="start"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit">
     <style:tab-stops/>
    </style:paragraph-properties>
    <style:text-properties fo:font-style="italic" style:font-style-asian="italic"
     fo:hyphenate="true" fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-transcription" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:line-height="0.459cm" fo:text-align="start"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit">
    <style:tab-stops>
     <style:tab-stop style:position="0.8cm" style:type="left"/>
    </style:tab-stops>
    </style:paragraph-properties>
    <style:text-properties fo:hyphenate="false" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-pentameter" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-left="0.501cm" fo:line-height="0.459cm"
     fo:text-align="start" style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit">
     <style:tab-stops/>
    </style:paragraph-properties>
    <style:text-properties fo:hyphenate="false" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-normal-2" style:family="paragraph">
    <style:paragraph-properties fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:font-size="9pt" style:font-size-asian="9pt" fo:hyphenate="true"
     fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-uebersetzung" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:text-align="justify" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-apparat" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:margin-left="0.501cm" fo:line-height="0.388cm"
     fo:text-align="justify" style:justify-single-word="false" fo:orphans="0" fo:widows="0"
     fo:hyphenation-ladder-count="no-limit" fo:text-indent="-0.501cm" style:auto-text-indent="false">
     <style:tab-stops>
      <style:tab-stop style:position="0cm"/>
     </style:tab-stops>
    </style:paragraph-properties>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-nachweis" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.353cm" fo:text-align="justify"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties style:letter-kerning="true" fo:hyphenate="true"
     fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>
   <!--<style:style style:name="cil-leerzeile-1pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.035cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-2pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.071cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-3pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.106cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-4pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.141cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-5pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.176cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-6pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.212cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-7pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.247cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-8pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.282cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-9pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.318cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-10pt" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:line-height="0.353cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-10.5pt" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:line-height="0.37cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-11pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.388cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-12pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.423cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-13pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.459cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-14pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.494cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-15pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.529cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-16pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.564cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-17pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.6cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-18pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.635cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-19pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.67cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-20pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.706cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-21pt" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.741cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <!--<style:style style:name="cil-leerzeile-22pt" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:line-height="0.776cm" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>-->
   <style:style style:name="cil-titelei-1" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-top="0cm" fo:margin-bottom="1.693cm"
     style:contextual-spacing="false" fo:line-height="0.847cm" fo:text-align="center"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit" fo:break-before="page"
     fo:keep-with-next="always"/>
    <style:text-properties fo:font-variant="small-caps" fo:font-size="24pt"
     style:font-size-asian="24pt" fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-titelei-2" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:line-height="0.847cm" fo:text-align="center"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit"
     fo:keep-with-next="always"/>
    <style:text-properties fo:text-transform="uppercase" fo:font-size="12pt"
     fo:letter-spacing="0.018cm" style:font-size-asian="12pt" fo:hyphenate="true"
     fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-titelei-3" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-top="0cm" fo:margin-bottom="0.353cm"
     style:contextual-spacing="false" fo:line-height="0.353cm" fo:text-align="center"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit"
     fo:keep-with-next="always"/>
    <style:text-properties fo:text-transform="uppercase" fo:hyphenate="true"
     fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-titelei-4" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-top="0cm" fo:margin-bottom="0.388cm"
     style:contextual-spacing="false" fo:line-height="0.423cm" fo:text-align="center"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit"
     fo:keep-with-next="always"/>
    <style:text-properties fo:font-size="12pt" style:font-size-asian="12pt" fo:hyphenate="true"
     fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-impressum-1" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-top="0cm" fo:margin-bottom="0.388cm"
     style:contextual-spacing="false" fo:text-align="center" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit" fo:keep-with-next="always"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-impressum-2" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:text-align="center" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit" fo:keep-with-next="always"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>

   <style:style style:name="cil-h0" style:family="paragraph"
    style:parent-style-name="cil-normal-1" style:default-outline-level="1">
    <style:paragraph-properties fo:margin-left="1cm" fo:margin-top="0.2cm" fo:margin-bottom="0.1cm"
     style:contextual-spacing="false" fo:line-height="0.423cm" fo:text-align="start"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit"
     fo:keep-with-next="always"/>
    <style:text-properties  fo:font-weight="600" fo:letter-spacing="0.071cm"
     fo:font-size="10pt" style:font-size-asian="10pt"  fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>

   <style:style style:name="cil-h1" style:family="paragraph"
    style:parent-style-name="cil-normal-1" style:default-outline-level="1">
    <style:paragraph-properties fo:margin-top="0.8cm" fo:margin-bottom="0.4cm"
     style:contextual-spacing="false" fo:line-height="0.423cm" fo:text-align="start"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit"
     fo:keep-with-next="always"/>
    <style:text-properties fo:text-transform="uppercase" fo:font-size="12pt"
     style:font-size-asian="12pt" fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   
   <style:style style:name="cil-h2" style:family="paragraph"
    style:parent-style-name="cil-normal-1" style:default-outline-level="2" style:list-style-name="">
    <style:paragraph-properties fo:font-size="11pt"
     style:font-size-asian="11pt" fo:margin-left="0.2cm" fo:margin-top="0.4cm" fo:margin-bottom="0.4cm"
     style:contextual-spacing="false" fo:line-height="0.423cm" fo:text-align="start"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit"
     fo:keep-with-next="always"/>
    <style:text-properties fo:font-variant="small-caps" fo:hyphenate="true"
     fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-h3" style:family="paragraph"
    style:parent-style-name="cil-normal-1" style:default-outline-level="3">
    <style:paragraph-properties  fo:margin-left="0.4cm" fo:margin-top="0.353cm" fo:margin-bottom="0.353cm"
     style:contextual-spacing="false" fo:text-align="start" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit" fo:keep-with-next="always"/>
    <style:text-properties fo:font-weight="600" fo:font-size="10pt"
     style:font-size-asian="10pt" fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>

   <style:style style:name="cil-h4" style:family="paragraph"
    style:parent-style-name="cil-normal-1" style:default-outline-level="4">
    <style:paragraph-properties  fo:margin-left="0.6cm" fo:margin-top="0.353cm" fo:margin-bottom="0.353cm"
     style:contextual-spacing="false" fo:text-align="start" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit" fo:keep-with-next="always"/>
    <style:text-properties fo:font-style="italic" fo:font-size="10pt"
     style:font-size-asian="10pt" fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-h5" style:family="paragraph"
    style:parent-style-name="cil-normal-1" style:default-outline-level="3" style:list-style-name="">
    <style:paragraph-properties  fo:margin-left="0.4cm" fo:margin-top="0.353cm" fo:margin-bottom="0.353cm"
     style:contextual-spacing="false" fo:text-align="start" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit" fo:keep-with-next="always"/>
    <style:text-properties fo:letter-spacing="0.071cm" fo:font-size="10pt"
     style:font-size-asian="10pt"  fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>   


<!--   <style:style style:name="cil-h0" style:family="text">
    <style:text-properties fo:font-weight="600" fo:letter-spacing="0.071cm"/>
   </style:style>-->  
   
   <style:style style:name="cil-register-eintrag" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:text-align="start" style:justify-single-word="false"
     fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-marken" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-left="0.827cm" fo:margin-right="1.348cm"
     fo:margin-top="0.088cm" fo:margin-bottom="0cm" style:contextual-spacing="false"
     fo:text-align="start" style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit"
     fo:text-indent="-0.827cm" style:auto-text-indent="false">
     <style:tab-stops/>
    </style:paragraph-properties>
    <style:text-properties fo:font-size="9pt" style:font-size-asian="9pt" fo:hyphenate="true"
     fo:hyphenation-remain-char-count="2" fo:hyphenation-push-char-count="2"
     loext:hyphenation-no-caps="false" loext:hyphenation-no-last-word="false"
     loext:hyphenation-word-char-count="no-limit" loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-zeilennummer" style:family="paragraph"
    style:parent-style-name="cil-normal-2">
    <style:paragraph-properties fo:line-height="0.459cm" fo:text-align="end"
     style:justify-single-word="false" fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:font-size="8pt" fo:font-style="italic" style:font-size-asian="8pt"
     style:font-style-asian="italic" fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-tabellenzeile" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-top="0cm" fo:margin-bottom="0.141cm"
     style:contextual-spacing="false" fo:line-height="0.353cm" fo:text-align="start"
     style:justify-single-word="false" fo:keep-together="always"
     fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   
   


   
   <style:style style:name="cil-inhalt-1" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:hyphenation-ladder-count="no-limit">
     <style:tab-stops>
      <style:tab-stop style:position="13.67cm" style:leader-style="dotted" style:leader-text="."/>
      <style:tab-stop style:position="14.305cm" style:type="right"/>
     </style:tab-stops>
    </style:paragraph-properties>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="cil-inhalt-2" style:family="paragraph"
    style:parent-style-name="cil-normal-1">
    <style:paragraph-properties fo:margin-left="1.499cm" fo:margin-right="1.005cm"
     fo:hyphenation-ladder-count="no-limit" fo:text-indent="-1.005cm" style:auto-text-indent="false">
     <style:tab-stops>
      <style:tab-stop style:position="0cm"/>
      <style:tab-stop style:position="12.171cm" style:leader-style="dotted" style:leader-text="."/>
      <style:tab-stop style:position="12.806cm" style:type="right"/>
     </style:tab-stops>
    </style:paragraph-properties>
    <style:text-properties fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="Dokumentstruktur" style:family="paragraph"
    style:parent-style-name="Standard">
    <style:paragraph-properties fo:hyphenation-ladder-count="no-limit"/>
    <style:text-properties style:font-name="Tahoma" fo:font-family="Tahoma"
     style:font-family-generic="swiss" style:font-pitch="variable" fo:font-size="8pt"
     style:font-size-asian="8pt" style:font-name-complex="Tahoma" style:font-family-complex="Tahoma"
     style:font-family-generic-complex="swiss" style:font-pitch-complex="variable"
     style:font-size-complex="8pt" fo:hyphenate="true" fo:hyphenation-remain-char-count="2"
     fo:hyphenation-push-char-count="2" loext:hyphenation-no-caps="false"
     loext:hyphenation-no-last-word="false" loext:hyphenation-word-char-count="no-limit"
     loext:hyphenation-zone="no-limit"/>
   </style:style>
   <style:style style:name="Header_20_and_20_Footer" style:display-name="Header and Footer"
    style:family="paragraph" style:parent-style-name="Standard" style:class="extra">
    <style:paragraph-properties text:number-lines="false" text:line-number="0">
     <style:tab-stops>
      <style:tab-stop style:position="8.5cm" style:type="center"/>
      <style:tab-stop style:position="17cm" style:type="right"/>
     </style:tab-stops>
    </style:paragraph-properties>
   </style:style>
   <style:style style:name="Header" style:family="paragraph"
    style:parent-style-name="Header_20_and_20_Footer" style:class="extra">
    <style:paragraph-properties text:number-lines="false" text:line-number="0">
     <style:tab-stops>
      <style:tab-stop style:position="8.5cm" style:type="center"/>
      <style:tab-stop style:position="17cm" style:type="right"/>
     </style:tab-stops>
    </style:paragraph-properties>
   </style:style>
   <style:style style:name="Footer" style:family="paragraph"
    style:parent-style-name="Header_20_and_20_Footer" style:class="extra">
    <style:paragraph-properties text:number-lines="false" text:line-number="0">
     <style:tab-stops>
      <style:tab-stop style:position="8.5cm" style:type="center"/>
      <style:tab-stop style:position="17cm" style:type="right"/>
     </style:tab-stops>
    </style:paragraph-properties>
   </style:style>
   <style:style style:name="Table_20_Contents" style:display-name="Table Contents"
    style:family="paragraph" style:parent-style-name="Standard" style:class="extra">
    <style:paragraph-properties fo:orphans="0" fo:widows="0" text:number-lines="false"
     text:line-number="0"/>
   </style:style>
   <style:style style:name="Absatz-Standardschriftart" style:family="text"/>
   <style:style style:name="cil-seitenzahl" style:family="text"/>
   <style:style style:name="cil-footnotesign" style:family="text">
    <style:text-properties style:text-position="super 65%"/>
   </style:style>
   <style:style style:name="cil-folium" style:family="text">
    <style:text-properties style:text-position="super 65%"/>
   </style:style>
<!-- zeilennummer vor der umgebrochenen transkriptionszeile -->
   <style:style style:name="cil-linenumber" style:family="text">
    <style:text-properties fo:font-size="8pt" fo:font-style="italic" style:font-size-asian="8pt"
     style:font-style-asian="italic" />
   </style:style>

   <style:style style:name="cil-app-segment" style:family="text">
    <style:text-properties fo:font-size="8pt" fo:font-style="italic" style:font-size-asian="8pt"
     style:font-style-asian="italic" />
   </style:style>

   <style:style style:name="cil-version" style:family="text">
    <style:text-properties style:text-position="super 65%" style:text-underline-style="solid"
     style:text-underline-width="auto" style:text-underline-color="#000000"
     style:text-underline-mode="continuous" style:text-overline-mode="continuous"
     style:text-line-through-mode="continuous"/>
   </style:style>
   <style:style style:name="cil-term" style:family="text">
    <style:text-properties fo:font-style="italic" style:font-style-asian="italic"/>
   </style:style>
   <style:style style:name="cil-zitat" style:family="text">
    <style:text-properties fo:font-style="italic" style:font-style-asian="italic"/>
   </style:style>
   <style:style style:name="cil-chronogramm" style:family="text">
    <style:text-properties fo:font-weight="bold" style:font-weight-asian="bold"/>
   </style:style>
   <style:style style:name="cil-initialen" style:family="text">
    <style:text-properties fo:font-size="14pt" style:font-size-asian="14pt"/>
   </style:style>
   <style:style style:name="cil-versalien" style:family="text">
    <style:text-properties fo:font-size="14pt" style:font-size-asian="14pt"/>
   </style:style>
   <style:style style:name="cil-sup" style:family="text">
    <style:text-properties style:text-position="super 65%" fo:font-size="8pt"
     style:font-size-asian="8pt"/>
   </style:style>

   <style:style style:name="cil-bold" style:family="text">
    <style:text-properties fo:font-weight="bold" style:font-weight-asian="bold"/>
   </style:style>
   <style:style style:name="cil-smallcaps" style:family="text">
    <style:text-properties fo:font-variant="small-caps"/>
   </style:style>
   <style:style style:name="cil-uppercase" style:family="text">
    <style:text-properties fo:text-transform="uppercase"/>
   </style:style>
   <style:style style:name="cil-underline" style:family="text">
    <style:text-properties style:text-underline-style="solid" style:text-underline-width="auto"
     style:text-underline-color="#000000" style:text-underline-mode="continuous"
     style:text-overline-mode="continuous" style:text-line-through-mode="continuous"/>
   </style:style>
   <style:style style:name="cil-supraline" style:family="text">
    <style:text-properties style:text-overline-style="solid" style:text-overline-width="auto"
     style:text-overline-color="#000000" style:text-overline-mode="continuous"/>
   </style:style>   
   
   
   <style:style style:name="cil-ligatur" style:family="text">
    <style:text-properties style:text-underline-style="solid" style:text-underline-width="auto"
     style:text-underline-color="#000000" style:text-underline-mode="continuous"
     style:text-overline-mode="continuous" style:text-line-through-mode="continuous"/>
   </style:style>
   <style:style style:name="cil-extdt" style:family="text">
    <style:text-properties fo:letter-spacing="0.071cm"/>
   </style:style>
   <style:style style:name="Hyperlink" style:family="text"
    style:parent-style-name="Absatz-Standardschriftart">
    <style:text-properties fo:color="#000099" loext:opacity="100%"/>
   </style:style>
   <style:style style:name="Dokumentstruktur_20_Zchn" style:display-name="Dokumentstruktur Zchn"
    style:family="text" style:parent-style-name="Absatz-Standardschriftart">
    <style:text-properties style:font-name="Tahoma" fo:font-family="Tahoma"
     style:font-family-generic="swiss" style:font-pitch="variable" fo:font-size="8pt"
     style:letter-kerning="true" style:font-size-asian="8pt" style:font-name-complex="Tahoma"
     style:font-family-complex="Tahoma" style:font-family-generic-complex="swiss"
     style:font-pitch-complex="variable" style:font-size-complex="8pt" style:language-complex="ar"
     style:country-complex="SA"/>
   </style:style>
   <text:outline-style style:name="Outline">
    <text:outline-level-style text:level="1" loext:num-list-format="%1%" style:num-format="">
     <style:list-level-properties text:list-level-position-and-space-mode="label-alignment">
      <style:list-level-label-alignment text:label-followed-by="listtab"/>
     </style:list-level-properties>
    </text:outline-level-style>
    <text:outline-level-style text:level="2" loext:num-list-format="%2%" style:num-format="">
     <style:list-level-properties text:list-level-position-and-space-mode="label-alignment">
      <style:list-level-label-alignment text:label-followed-by="listtab"/>
     </style:list-level-properties>
    </text:outline-level-style>
    <text:outline-level-style text:level="3" loext:num-list-format="%3%" style:num-format="">
     <style:list-level-properties text:list-level-position-and-space-mode="label-alignment">
      <style:list-level-label-alignment text:label-followed-by="listtab"/>
     </style:list-level-properties>
    </text:outline-level-style>
    <text:outline-level-style text:level="4" loext:num-list-format="%4%" style:num-format="">
     <style:list-level-properties text:list-level-position-and-space-mode="label-alignment">
      <style:list-level-label-alignment text:label-followed-by="listtab"/>
     </style:list-level-properties>
    </text:outline-level-style>
    <text:outline-level-style text:level="5" loext:num-list-format="%5%" style:num-format="">
     <style:list-level-properties text:list-level-position-and-space-mode="label-alignment">
      <style:list-level-label-alignment text:label-followed-by="listtab"/>
     </style:list-level-properties>
    </text:outline-level-style>
    <text:outline-level-style text:level="6" loext:num-list-format="%6%" style:num-format="">
     <style:list-level-properties text:list-level-position-and-space-mode="label-alignment">
      <style:list-level-label-alignment text:label-followed-by="listtab"/>
     </style:list-level-properties>
    </text:outline-level-style>
    <text:outline-level-style text:level="7" loext:num-list-format="%7%" style:num-format="">
     <style:list-level-properties text:list-level-position-and-space-mode="label-alignment">
      <style:list-level-label-alignment text:label-followed-by="listtab"/>
     </style:list-level-properties>
    </text:outline-level-style>
    <text:outline-level-style text:level="8" loext:num-list-format="%8%" style:num-format="">
     <style:list-level-properties text:list-level-position-and-space-mode="label-alignment">
      <style:list-level-label-alignment text:label-followed-by="listtab"/>
     </style:list-level-properties>
    </text:outline-level-style>
    <text:outline-level-style text:level="9" loext:num-list-format="%9%" style:num-format="">
     <style:list-level-properties text:list-level-position-and-space-mode="label-alignment">
      <style:list-level-label-alignment text:label-followed-by="listtab"/>
     </style:list-level-properties>
    </text:outline-level-style>
    <text:outline-level-style text:level="10" loext:num-list-format="%10%" style:num-format="">
     <style:list-level-properties text:list-level-position-and-space-mode="label-alignment">
      <style:list-level-label-alignment text:label-followed-by="listtab"/>
     </style:list-level-properties>
    </text:outline-level-style>
   </text:outline-style>
   <text:notes-configuration text:note-class="footnote" style:num-format="1" text:start-value="0"
    text:footnotes-position="page" text:start-numbering-at="document"/>
   <text:notes-configuration text:note-class="endnote" style:num-format="i" text:start-value="0"/>
   <text:linenumbering-configuration text:number-lines="false" text:offset="0.499cm"
    style:num-format="1" text:number-position="left" text:increment="5"/>
   <loext:theme loext:name="Office Theme">
    <loext:theme-colors loext:name="LibreOffice">
     <loext:color loext:name="dark1" loext:color="#000000"/>
     <loext:color loext:name="light1" loext:color="#ffffff"/>
     <loext:color loext:name="dark2" loext:color="#000000"/>
     <loext:color loext:name="light2" loext:color="#ffffff"/>
     <loext:color loext:name="accent1" loext:color="#18a303"/>
     <loext:color loext:name="accent2" loext:color="#0369a3"/>
     <loext:color loext:name="accent3" loext:color="#a33e03"/>
     <loext:color loext:name="accent4" loext:color="#8e03a3"/>
     <loext:color loext:name="accent5" loext:color="#c99c00"/>
     <loext:color loext:name="accent6" loext:color="#c9211e"/>
     <loext:color loext:name="hyperlink" loext:color="#0000ee"/>
     <loext:color loext:name="followed-hyperlink" loext:color="#551a8b"/>
    </loext:theme-colors>
   </loext:theme>
   
   
  <!-- aus automatic-styles --> 
     

      
      <!-- inschriftentabelle -->
      <style:style style:name="Tabelle1" style:family="table">
       <style:table-properties style:width="13.353cm" fo:margin-left="0cm" table:align="left"/>
      </style:style>
      <style:style style:name="Tabelle1.A" style:family="table-column">
       <style:table-column-properties style:column-width="0.653cm"/>
      </style:style>
      <style:style style:name="Tabelle1.B" style:family="table-column">
       <style:table-column-properties style:column-width="12.7cm"/>
      </style:style>
      <style:style style:name="Tabelle1.A1" style:family="table-cell">
       <style:table-cell-properties fo:padding-left="0.018cm" fo:padding-right="0.018cm"
        fo:padding-top="0cm" fo:padding-bottom="0.035cm" fo:border="none"/>
      </style:style>
      
      <!-- anmerkungen -->
      <style:style style:name="Tabelle2" style:family="table">
       <style:table-properties style:width="14.288cm" fo:margin-left="0cm" table:align="left"/>
      </style:style>
      <style:style style:name="Tabelle2.A" style:family="table-column">
       <style:table-column-properties style:column-width="0.635cm"/>
      </style:style>
      <style:style style:name="Tabelle2.B" style:family="table-column">
       <style:table-column-properties style:column-width="13.653cm"/>
      </style:style>
      <style:style style:name="Tabelle2.C" style:family="table-column">
       <style:table-column-properties style:column-width="1.5cm"/>
      </style:style>   
      <style:style style:name="Tabelle2.A1" style:family="table-cell">
       <style:table-cell-properties fo:padding-left="0.018cm" fo:padding-right="0.018cm"
        fo:padding-top="0cm" fo:padding-bottom="0.053cm" fo:border="none"/>
      </style:style>
      
      <!-- varianten und textkritik -->
      <style:style style:name="Tabelle3" style:family="table">
       <style:table-properties style:width="17.0cm" fo:margin-left="0cm" table:align="left"/>
      </style:style>
      <style:style style:name="Tabelle3.A" style:family="table-column">
       <style:table-column-properties style:column-width="0.3cm"/>
      </style:style>
      <style:style style:name="Tabelle3.B" style:family="table-column">
       <style:table-column-properties style:column-width="0.3cm"/>
      </style:style>
      <style:style style:name="Tabelle3.C" style:family="table-column">
       <style:table-column-properties style:column-width="12.0cm"/>
      </style:style>
      <style:style style:name="Tabelle3.A1" style:family="table-cell">
       <style:table-cell-properties fo:padding-left="0.018cm" fo:padding-right="0.018cm"
        fo:padding-top="0cm" fo:padding-bottom="0.053cm" fo:border="none"/>
      </style:style>
      
      
      
      <style:style style:name="cil-textcomments" style:family="table">
       <style:table-properties style:width="17cm" fo:margin-left="0cm" table:align="left"/>
      </style:style>
      <style:style style:name="cil-textcomments.A" style:family="table-column">
       <style:table-column-properties style:column-width="0.896cm"/>
      </style:style>
      <style:style style:name="cil-textcomments.B" style:family="table-column">
       <style:table-column-properties style:column-width="1.102cm"/>
      </style:style>
      <style:style style:name="cil-textcomments.C" style:family="table-column">
       <style:table-column-properties style:column-width="15.002cm"/>
      </style:style>
      <style:style style:name="cil-textcomments.A1" style:family="table-cell">
       <style:table-cell-properties fo:padding-left="0cm" fo:padding-right="0.199cm" fo:padding-top="0cm" fo:padding-bottom="0.499cm" fo:border="none"/>
      </style:style>
      <style:style style:name="cil-textcomments.C1" style:family="table-cell">
       <style:table-cell-properties fo:padding-left="0cm" fo:padding-right="0cm" fo:padding-top="0cm" fo:padding-bottom="0.499cm" fo:border="none"/>
      </style:style>
      
     
     
     <!--<office:automatic-styles>
   <!-\- tabelle inschriften -\->
   <style:style style:name="Tabelle1" style:family="table">
    <style:table-properties style:width="17.0cm" fo:margin-left="0cm" table:align="left"/>
   </style:style>
   <style:style style:name="Tabelle1.A" style:family="table-column">
    <style:table-column-properties style:column-width="0.653cm"/>
   </style:style>
   <style:style style:name="Tabelle1.B" style:family="table-column">
    <style:table-column-properties style:column-width="16.7cm"/>
   </style:style>
   <style:style style:name="Tabelle1.A1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.0cm" fo:padding-right="0.2cm" fo:padding-top="0cm" fo:padding-bottom="0.2cm" fo:border="none"/>
   </style:style>
   <style:style style:name="Tabelle1.A2" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.0cm" fo:padding-right="0.0cm" fo:padding-top="0cm" fo:padding-bottom="0.2cm" fo:border="none"/>
   </style:style>
   
   <!-\- tabelle sachanmerkungen -\->
   <style:style style:name="Tabelle2" style:family="table">
    <style:table-properties style:width="17.0cm" fo:margin-left="0cm" table:align="left"/>
   </style:style>
   <style:style style:name="Tabelle2.A" style:family="table-column">
    <style:table-column-properties style:column-width="0.653cm"/>
   </style:style>
   <style:style style:name="Tabelle2.B" style:family="table-column">
    <style:table-column-properties style:column-width="16.7cm"/>
   </style:style>
   <style:style style:name="Tabelle2.A1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.0cm" fo:padding-right="0.2cm" fo:padding-top="0cm" fo:padding-bottom="0.0cm" fo:border="none"/>
   </style:style>
   <style:style style:name="Tabelle2.A2" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.0cm" fo:padding-right="0.0cm" fo:padding-top="0cm" fo:padding-bottom="0.0cm" fo:border="none"/>
   </style:style>   
   
   <!-\- tabelle kommentare -\->
   <style:style style:name="Tabelle3" style:family="table">
    <style:table-properties style:width="17.0cm" fo:margin-left="0cm" table:align="left"/>
   </style:style>
   <style:style style:name="Tabelle3.A" style:family="table-column">
    <style:table-column-properties style:column-width="2cm"/>
   </style:style>
   <style:style style:name="Tabelle3.B" style:family="table-column">
    <style:table-column-properties style:column-width="15cm"/>
   </style:style>
   <style:style style:name="Tabelle3.A1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.0cm" fo:padding-right="0.2cm" fo:padding-top="0cm" fo:padding-bottom="0.5cm" fo:border="none"/>
   </style:style>
   <style:style style:name="Tabelle3.A2" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.0cm" fo:padding-right="0.0cm" fo:padding-top="0cm" fo:padding-bottom="0.5cm" fo:border="none"/>
   </style:style>   
   
   <style:style style:name="cil-textcomments" style:family="table">
    <style:table-properties style:width="17cm" fo:margin-left="0cm" table:align="left"/>
   </style:style>
   <style:style style:name="cil-textcomments.A" style:family="table-column">
    <style:table-column-properties style:column-width="0.896cm"/>
   </style:style>
   <style:style style:name="cil-textcomments.B" style:family="table-column">
    <style:table-column-properties style:column-width="1.102cm"/>
   </style:style>
   <style:style style:name="cil-textcomments.C" style:family="table-column">
    <style:table-column-properties style:column-width="15.002cm"/>
   </style:style>
   <style:style style:name="cil-textcomments.A1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0cm" fo:padding-right="0.199cm" fo:padding-top="0cm" fo:padding-bottom="0.499cm" fo:border="none"/>
   </style:style>
   <style:style style:name="cil-textcomments.C1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0cm" fo:padding-right="0cm" fo:padding-top="0cm" fo:padding-bottom="0.499cm" fo:border="none"/>
   </style:style>
   
   
  </office:automatic-styles>-->
     
     
     
     
     
   
   
  </office:styles>
 </xsl:template>

 <xsl:template name="automatic-styles">
  <office:automatic-styles>

   <!-- inschriftentabelle -->
   <style:style style:name="Tabelle1" style:family="table">
    <style:table-properties style:width="13.353cm" fo:margin-left="0cm" table:align="left"/>
   </style:style>
   <style:style style:name="Tabelle1.A" style:family="table-column">
    <style:table-column-properties style:column-width="0.653cm"/>
   </style:style>
   <style:style style:name="Tabelle1.B" style:family="table-column">
    <style:table-column-properties style:column-width="12.7cm"/>
   </style:style>
   <style:style style:name="Tabelle1.A1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.018cm" fo:padding-right="0.018cm"
     fo:padding-top="0cm" fo:padding-bottom="0.035cm" fo:border="none"/>
   </style:style>

   <!-- anmerkungen -->
   <style:style style:name="Tabelle2" style:family="table">
    <style:table-properties style:width="14.288cm" fo:margin-left="0cm" table:align="left"/>
   </style:style>
   <style:style style:name="Tabelle2.A" style:family="table-column">
    <style:table-column-properties style:column-width="0.635cm"/>
   </style:style>
   <style:style style:name="Tabelle2.B" style:family="table-column">
    <style:table-column-properties style:column-width="13.653cm"/>
   </style:style>
   <style:style style:name="Tabelle2.C" style:family="table-column">
    <style:table-column-properties style:column-width="1.5cm"/>
   </style:style>   
   <style:style style:name="Tabelle2.A1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.018cm" fo:padding-right="0.018cm"
     fo:padding-top="0cm" fo:padding-bottom="0.053cm" fo:border="none"/>
   </style:style>

   <!-- varianten und textkritik -->
   <style:style style:name="Tabelle3" style:family="table">
    <style:table-properties style:width="17.0cm" fo:margin-left="0cm" table:align="left"/>
   </style:style>
   <style:style style:name="Tabelle3.A" style:family="table-column">
    <style:table-column-properties style:column-width="0.3cm"/>
   </style:style>
   <style:style style:name="Tabelle3.B" style:family="table-column">
    <style:table-column-properties style:column-width="0.3cm"/>
   </style:style>
   <style:style style:name="Tabelle3.C" style:family="table-column">
    <style:table-column-properties style:column-width="12.0cm"/>
   </style:style>
   <style:style style:name="Tabelle3.A1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.018cm" fo:padding-right="0.018cm"
     fo:padding-top="0cm" fo:padding-bottom="0.053cm" fo:border="none"/>
   </style:style>
   
   
   
   <style:style style:name="cil-textcomments" style:family="table">
    <style:table-properties style:width="17cm" fo:margin-left="0cm" table:align="left"/>
   </style:style>
   <style:style style:name="cil-textcomments.A" style:family="table-column">
    <style:table-column-properties style:column-width="0.896cm"/>
   </style:style>
   <style:style style:name="cil-textcomments.B" style:family="table-column">
    <style:table-column-properties style:column-width="1.102cm"/>
   </style:style>
   <style:style style:name="cil-textcomments.C" style:family="table-column">
    <style:table-column-properties style:column-width="15.002cm"/>
   </style:style>
   <style:style style:name="cil-textcomments.A1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0cm" fo:padding-right="0.199cm" fo:padding-top="0cm" fo:padding-bottom="0.499cm" fo:border="none"/>
   </style:style>
   <style:style style:name="cil-textcomments.C1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0cm" fo:padding-right="0cm" fo:padding-top="0cm" fo:padding-bottom="0.499cm" fo:border="none"/>
   </style:style>
 
  </office:automatic-styles>
  
  <!--<office:automatic-styles>
   <!-\- tabelle inschriften -\->
   <style:style style:name="Tabelle1" style:family="table">
    <style:table-properties style:width="17.0cm" fo:margin-left="0cm" table:align="left"/>
   </style:style>
   <style:style style:name="Tabelle1.A" style:family="table-column">
    <style:table-column-properties style:column-width="0.653cm"/>
   </style:style>
   <style:style style:name="Tabelle1.B" style:family="table-column">
    <style:table-column-properties style:column-width="16.7cm"/>
   </style:style>
   <style:style style:name="Tabelle1.A1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.0cm" fo:padding-right="0.2cm" fo:padding-top="0cm" fo:padding-bottom="0.2cm" fo:border="none"/>
   </style:style>
   <style:style style:name="Tabelle1.A2" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.0cm" fo:padding-right="0.0cm" fo:padding-top="0cm" fo:padding-bottom="0.2cm" fo:border="none"/>
   </style:style>
   
   <!-\- tabelle sachanmerkungen -\->
   <style:style style:name="Tabelle2" style:family="table">
    <style:table-properties style:width="17.0cm" fo:margin-left="0cm" table:align="left"/>
   </style:style>
   <style:style style:name="Tabelle2.A" style:family="table-column">
    <style:table-column-properties style:column-width="0.653cm"/>
   </style:style>
   <style:style style:name="Tabelle2.B" style:family="table-column">
    <style:table-column-properties style:column-width="16.7cm"/>
   </style:style>
   <style:style style:name="Tabelle2.A1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.0cm" fo:padding-right="0.2cm" fo:padding-top="0cm" fo:padding-bottom="0.0cm" fo:border="none"/>
   </style:style>
   <style:style style:name="Tabelle2.A2" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.0cm" fo:padding-right="0.0cm" fo:padding-top="0cm" fo:padding-bottom="0.0cm" fo:border="none"/>
   </style:style>   
   
   <!-\- tabelle kommentare -\->
   <style:style style:name="Tabelle3" style:family="table">
    <style:table-properties style:width="17.0cm" fo:margin-left="0cm" table:align="left"/>
   </style:style>
   <style:style style:name="Tabelle3.A" style:family="table-column">
    <style:table-column-properties style:column-width="2cm"/>
   </style:style>
   <style:style style:name="Tabelle3.B" style:family="table-column">
    <style:table-column-properties style:column-width="15cm"/>
   </style:style>
   <style:style style:name="Tabelle3.A1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.0cm" fo:padding-right="0.2cm" fo:padding-top="0cm" fo:padding-bottom="0.5cm" fo:border="none"/>
   </style:style>
   <style:style style:name="Tabelle3.A2" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0.0cm" fo:padding-right="0.0cm" fo:padding-top="0cm" fo:padding-bottom="0.5cm" fo:border="none"/>
   </style:style>   
   
   <style:style style:name="cil-textcomments" style:family="table">
    <style:table-properties style:width="17cm" fo:margin-left="0cm" table:align="left"/>
   </style:style>
   <style:style style:name="cil-textcomments.A" style:family="table-column">
    <style:table-column-properties style:column-width="0.896cm"/>
   </style:style>
   <style:style style:name="cil-textcomments.B" style:family="table-column">
    <style:table-column-properties style:column-width="1.102cm"/>
   </style:style>
   <style:style style:name="cil-textcomments.C" style:family="table-column">
    <style:table-column-properties style:column-width="15.002cm"/>
   </style:style>
   <style:style style:name="cil-textcomments.A1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0cm" fo:padding-right="0.199cm" fo:padding-top="0cm" fo:padding-bottom="0.499cm" fo:border="none"/>
   </style:style>
   <style:style style:name="cil-textcomments.C1" style:family="table-cell">
    <style:table-cell-properties fo:padding-left="0cm" fo:padding-right="0cm" fo:padding-top="0cm" fo:padding-bottom="0.499cm" fo:border="none"/>
   </style:style>
   
   
  </office:automatic-styles>-->
  
  
  
  
 </xsl:template>

</xsl:stylesheet>
