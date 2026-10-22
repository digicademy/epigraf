<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" 
xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml" 
xmlns:v="urn:schemas-microsoft-com:vml" 
xmlns:w10="urn:schemas-microsoft-com:office:word" 
xmlns:sl="http://schemas.microsoft.com/schemaLibrary/2003/core" 
xmlns:aml="http://schemas.microsoft.com/aml/2001/core" 
xmlns:o="urn:schemas-microsoft-com:office:office" 
xmlns:dt="uuid:C2F41010-65B3-11d1-A29F-00AA00C14882" 
xmlns:wsp="http://schemas.microsoft.com/office/word/2003/wordml/sp2" 
>


<xsl:output method="xml" version="1.0" indent="yes" encoding="UTF-8"/>

  <!-- als einführung und übersicht zu WordML siehe 
    http://officeopenxml.com/WPcontentOverview.php (englisch)
    https://www.data2type.de/xml-xslt-xslfo/wordml/ (deutsch)
  
  -->
  
  <!-- zusamenstellung der fonts für die package-datei /word/fontTable.xml -->

  <xsl:template name="fonts">
  <!--Fonts: Standartfont ist Times New Roman, zusaetzlich eingebunden ist Courier New-->
    <w:fonts xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"
      xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">
      <w:font w:name="Times New Roman">
        <w:panose1 w:val="02020603050405020304"/>
        <w:charset w:val="00"/>
        <w:family w:val="roman"/>
        <w:pitch w:val="variable"/>
        <w:sig w:usb0="E0002EFF" w:usb1="C000785B" w:usb2="00000009" w:usb3="00000000"
          w:csb0="000001FF" w:csb1="00000000"/>
      </w:font>
      <w:font w:name="Courier New">
        <w:panose1 w:val="02070309020205020404"/>
        <w:charset w:val="00"/>
        <w:family w:val="modern"/>
        <w:pitch w:val="fixed"/>
        <w:sig w:usb0="E0002EFF" w:usb1="C0007843" w:usb2="00000009" w:usb3="00000000"
          w:csb0="000001FF" w:csb1="00000000"/>
      </w:font>
      <w:font w:name="Cambria">
        <w:panose1 w:val="02040503050406030204"/>
        <w:charset w:val="00"/>
        <w:family w:val="roman"/>
        <w:pitch w:val="variable"/>
        <w:sig w:usb0="E00006FF" w:usb1="420024FF" w:usb2="02000000" w:usb3="00000000"
          w:csb0="0000019F" w:csb1="00000000"/>
      </w:font>
      <w:font w:name="Araldo">
        <w:panose1 w:val="02020404030301010803"/>
        <w:charset w:val="00"/>
        <w:family w:val="roman"/>
        <w:pitch w:val="variable"/>
        <w:sig w:usb0="00000003" w:usb1="00000000" w:usb2="00000000" w:usb3="00000000"
          w:csb0="00000001" w:csb1="00000000"/>
      </w:font>
    </w:fonts>
  </xsl:template>
</xsl:stylesheet>
