<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
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
>


<xsl:output method="xml" version="1.0" indent="yes" encoding="UTF-8"/>

  <!-- als einführung und übersicht zu WordML siehe 
    http://officeopenxml.com/WPcontentOverview.php (englisch)
    https://www.data2type.de/xml-xslt-xslfo/wordml/ (deutsch)
  
  -->

<xsl:template name="fonts">
<!--Fonts: Standartfont ist Times New Roman, zusaetzlich eingebunden ist Courier New-->
  <w:fonts>
  <w:defaultFonts w:ascii="Times New Roman" w:fareast="Times New Roman" w:h-ansi="Times New Roman" w:cs="Times New Roman"/>
  <w:font w:name="Times New Roman">
    <w:panose-1 w:val="02020404030301010803"/>
    <w:charset w:val="00"/>
    <w:family w:val="Roman"/>
    <w:pitch w:val="variable"/>
    <w:sig w:usb-0="00000287" w:usb-1="00000000" w:usb-2="00000000" w:usb-3="00000000" w:csb-0="0000009F" w:csb-1="00000000"/>
  </w:font>
  <w:font w:name="Courier New">
    <w:panose-1 w:val="02070409020205020404"/>
    <w:charset w:val="00"/>
    <w:family w:val="Modern"/>
    <w:notTrueType/>
    <w:pitch w:val="fixed"/>
    <w:sig w:usb-0="00000003" w:usb-1="00000000" w:usb-2="00000000" w:usb-3="00000000" w:csb-0="00000001" w:csb-1="00000000"/>
  </w:font>
    <!-- ligaturbögen -->
  <w:font w:name="Araldo">
    <w:panose-1 w:val="02020404030301010803"/>
    <w:charset w:val="00"/>
    <w:family w:val="Roman"/>
    <w:pitch w:val="variable"/>
    <w:sig w:usb-0="00000003" w:usb-1="00000000" w:usb-2="00000000" w:usb-3="00000000" w:csb-0="00000001" w:csb-1="00000000"/>
  </w:font>
    <!-- spitze klammern -->
    <w:font w:name="Cambria">
      <w:panose-1 w:val="02040503050406030204"/>
      <w:charset w:val="00"/>
      <w:family w:val="Roman"/>
      <w:pitch w:val="variable"/>
      <w:sig w:usb-0="E00002FF" w:usb-1="400004FF" w:usb-2="00000000" w:usb-3="00000000" w:csb-0="0000019F" w:csb-1="00000000"/>
    </w:font>
  </w:fonts>
</xsl:template>








</xsl:stylesheet>
