<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
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
    xmlns:ooow="http://openoffice.org/2004/writer" 
    xmlns:oooc="http://openoffice.org/2004/calc"
    xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2"
    xmlns:xforms="http://www.w3.org/2002/xforms" 
    xmlns:tableooo="http://openoffice.org/2009/table"
    xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0"
    xmlns:drawooo="http://openoffice.org/2010/draw"
    xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0"
    xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0"
    xmlns:math="http://www.w3.org/1998/Math/MathML"
    xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0"
    xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0"
    xmlns:formx="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0"
    xmlns:dom="http://www.w3.org/2001/xml-events" 
    xmlns:xsd="http://www.w3.org/2001/XMLSchema"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
    xmlns:xhtml="http://www.w3.org/1999/xhtml"
    xmlns:grddl="http://www.w3.org/2003/g/data-view#" 
    xmlns:css3t="http://www.w3.org/TR/css3-text/"
    xmlns:officeooo="http://openoffice.org/2009/office" office:version="1.3"
    exclude-result-prefixes="xs" version="2.0">


    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>

    <xsl:template match="book">
        <!-- package-ordner "job0000.odt" -->
        <d_odt>
        <!-- ordner "META-INF" 
                verknüfungen zwischen dateien-->
            <d_META-INF>
                <!-- datei "manifest.xml" -->
                <f_manifest.xml>
                    <!-- es werden die dateien 
                            meta.xml.
                            settings.xml und
                            styles.xml mit dem hautptdokument 
                            content.xml verknüpft -->
                    <manifest:manifest xmlns:manifest="urn:oasis:names:tc:opendocument:xmlns:manifest:1.0">
                        <manifest:file-entry manifest:full-path="/"
                            manifest:media-type="application/vnd.oasis.opendocument.text"/>
                        <manifest:file-entry manifest:full-path="content.xml" manifest:media-type="text/xml"/>
                        <manifest:file-entry manifest:full-path="styles.xml" manifest:media-type="text/xml"/>
                        <manifest:file-entry manifest:full-path="settings.xml" manifest:media-type="text/xml"/>
                        <manifest:file-entry manifest:full-path="meta.xml" manifest:media-type="text/xml"/>
                    </manifest:manifest>
                </f_manifest.xml>
            </d_META-INF>
         <!-- datei "content.xml" -->
            <f_content.xml>
                
            </f_content.xml>
          <!-- datei "styles.xml" -->
            <f_styles.xml>
                
            </f_styles.xml>
            
            <!-- datei "settings.xml" -->
            <f_settings.xml>
                
            </f_settings.xml>
            
            <!-- datei "meta.xml" -->
            <f_meta.xml>
                <!--  <?xml version="1.0" encoding="UTF-8"?> -->
                <xsl:stylesheet 
                    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                    xmlns:xs="http://www.w3.org/2001/XMLSchema"
                    xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" 
                    xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" 
                    xmlns:rpt="http://openoffice.org/2005/report" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:tableooo="http://openoffice.org/2009/table" xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0" xmlns:drawooo="http://openoffice.org/2010/draw" xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0" xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:formx="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:grddl="http://www.w3.org/2003/g/data-view#" xmlns:css3t="http://www.w3.org/TR/css3-text/" xmlns:officeooo="http://openoffice.org/2009/office" office:version="1.3" office:mimetype="application/vnd.oasis.opendocument.text"
                    
                    exclude-result-prefixes="xs"
                    version="2.0">
                    
                    
                    <xsl:template name="meta">
                        <office:meta><meta:generator>Epigraf</meta:generator><meta:initial-creator>Jürgen Herold</meta:initial-creator><meta:creation-date>2023-11-19T04:19:00Z</meta:creation-date><dc:date>2023-11-20T10:54:10.216000000</dc:date><meta:template xlink:type="simple" xlink:actuate="onRequest" xlink:title="" xlink:href="a.galluskapelle.pfeilergrab-iulius.odt/Normal.dotm"/></office:meta>
                        
                    </xsl:template>
                </xsl:stylesheet>
                <xsl:stylesheet 
                    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                    xmlns:xs="http://www.w3.org/2001/XMLSchema"
                    xmlns:office="urn:oasis:names:tc:opendocument:xmlns:office:1.0" xmlns:ooo="http://openoffice.org/2004/office" xmlns:fo="urn:oasis:names:tc:opendocument:xmlns:xsl-fo-compatible:1.0" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:config="urn:oasis:names:tc:opendocument:xmlns:config:1.0" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:meta="urn:oasis:names:tc:opendocument:xmlns:meta:1.0" xmlns:style="urn:oasis:names:tc:opendocument:xmlns:style:1.0" 
                    xmlns:text="urn:oasis:names:tc:opendocument:xmlns:text:1.0" 
                    xmlns:rpt="http://openoffice.org/2005/report" xmlns:draw="urn:oasis:names:tc:opendocument:xmlns:drawing:1.0" xmlns:dr3d="urn:oasis:names:tc:opendocument:xmlns:dr3d:1.0" xmlns:svg="urn:oasis:names:tc:opendocument:xmlns:svg-compatible:1.0" xmlns:chart="urn:oasis:names:tc:opendocument:xmlns:chart:1.0" xmlns:table="urn:oasis:names:tc:opendocument:xmlns:table:1.0" xmlns:number="urn:oasis:names:tc:opendocument:xmlns:datastyle:1.0" xmlns:ooow="http://openoffice.org/2004/writer" xmlns:oooc="http://openoffice.org/2004/calc" xmlns:of="urn:oasis:names:tc:opendocument:xmlns:of:1.2" xmlns:xforms="http://www.w3.org/2002/xforms" xmlns:tableooo="http://openoffice.org/2009/table" xmlns:calcext="urn:org:documentfoundation:names:experimental:calc:xmlns:calcext:1.0" xmlns:drawooo="http://openoffice.org/2010/draw" xmlns:loext="urn:org:documentfoundation:names:experimental:office:xmlns:loext:1.0" xmlns:field="urn:openoffice:names:experimental:ooo-ms-interop:xmlns:field:1.0" xmlns:math="http://www.w3.org/1998/Math/MathML" xmlns:form="urn:oasis:names:tc:opendocument:xmlns:form:1.0" xmlns:script="urn:oasis:names:tc:opendocument:xmlns:script:1.0" xmlns:formx="urn:openoffice:names:experimental:ooxml-odf-interop:xmlns:form:1.0" xmlns:dom="http://www.w3.org/2001/xml-events" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:grddl="http://www.w3.org/2003/g/data-view#" xmlns:css3t="http://www.w3.org/TR/css3-text/" xmlns:officeooo="http://openoffice.org/2009/office" office:version="1.3" office:mimetype="application/vnd.oasis.opendocument.text"
                    
                    exclude-result-prefixes="xs"
                    version="2.0">
                    
                    
                    <xsl:template name="meta">
                        <office:meta><meta:generator>Epigraf</meta:generator><meta:initial-creator>Jürgen Herold</meta:initial-creator><meta:creation-date>2023-11-19T04:19:00Z</meta:creation-date><dc:date>2023-11-20T10:54:10.216000000</dc:date><meta:template xlink:type="simple" xlink:actuate="onRequest" xlink:title="" xlink:href="a.galluskapelle.pfeilergrab-iulius.odt/Normal.dotm"/></office:meta>
                        
                    </xsl:template>
                </xsl:stylesheet>
            </f_meta.xml>
            
            
            <!-- datei "mimetype" -->
            <f_mimetype>application/vnd.oasis.opendocument.text</f_mimetype>
            

            

        </d_odt>

    </xsl:template>

</xsl:stylesheet>
