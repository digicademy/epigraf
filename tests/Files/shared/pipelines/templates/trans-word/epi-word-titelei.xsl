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

<xsl:output method="xml" version="1.0"  indent="yes" encoding="UTF-8"/>

<xsl:template match="titelei">
  <xsl:if test="$titelei=1 and node()">
    <xsl:comment>titelei anfang</xsl:comment><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
    <wx:sect><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

      <xsl:comment>reihentitel anfang</xsl:comment><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <wx:sub-section><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <xsl:call-template name="titelei-reihe"/>
      </wx:sub-section>
      <xsl:comment>reihentitel ende</xsl:comment><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

      <xsl:comment>bandtitel anfang</xsl:comment><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <wx:sub-section><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <xsl:call-template name="titelei-band"/>
      </wx:sub-section>
      <xsl:comment>bandtitel ende</xsl:comment><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

      <xsl:comment>impressum anfang</xsl:comment><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <wx:sub-section><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
        <xsl:call-template name="impressum"/>
      </wx:sub-section>
      <xsl:comment>impressum ende</xsl:comment><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
      <xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>

  </wx:sect><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
  <xsl:comment>titelei ende</xsl:comment><xsl:text disable-output-escaping="yes">&#x000D;</xsl:text>
  </xsl:if>
</xsl:template>

  <xsl:template name="titelei-reihe">
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

        <w:tr>
          <w:trPr>
            <w:trHeight w:val="10915"/>
          </w:trPr>
          <w:tc>
            <w:tcPr>
              <w:tcW w:w="9212" w:type="dxa"/>
            </w:tcPr>
            <xsl:for-each select="prelim_series/prelim_series_title">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-titelei-1"/>
                </w:pPr>
                <w:r>
                  <w:t><xsl:value-of select=".//p"/></w:t>
                </w:r>
              </w:p>
            </xsl:for-each>
            <!-- reihentitel -->
<!--            <xsl:for-each select="reihe/titel">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-titelei-1"/>
                </w:pPr>
                <w:r>
                  <w:t><xsl:value-of select=".//p"/></w:t>
                </w:r>
              </w:p>
            </xsl:for-each>-->

            <!-- herausgeber -->
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
<!--            <xsl:for-each select="reihe/herausgeber//p">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-titelei-2"/>
                </w:pPr>
                <w:r>
                  <w:t><xsl:value-of select="."/></w:t>
                </w:r>
              </w:p>
            </xsl:for-each>-->

            <!-- zwischenzeile -->
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-titelei-2"/>
              </w:pPr>
            </w:p>

            <!-- reihennummer -->
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
<!--            <xsl:for-each select="reihe/nummer">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-titelei-2"/>
              </w:pPr>
              <w:r>
                <w:t><xsl:value-of select=".//p"/></w:t>
              </w:r>
            </w:p>
            </xsl:for-each>-->

            <!-- unterreihe -->
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
<!--            <xsl:for-each select="unterreihe">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-titelei-2"/>
                </w:pPr>
                <w:r>
                  <w:t><xsl:value-of select=".//p"/></w:t>
                </w:r>
              </w:p>
            </xsl:for-each>-->

            <w:p>
              <w:pPr>
                <w:jc w:val="center"/>
              </w:pPr>
            </w:p>

            <!-- bandtitel -->
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
<!--            <xsl:for-each select="band/titel">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-titelei-4"/>
              </w:pPr>
              <w:r>
                <w:t><xsl:value-of select="./p"/></w:t>
              </w:r>
            </w:p>
            </xsl:for-each>-->
          </w:tc>
        </w:tr>
        <w:tr>
          <w:trPr>
            <w:trHeight w:val="1615"/>
          </w:trPr>
          <w:tc>
            <w:tcPr>
              <w:tcW w:w="9212" w:type="dxa"/>
              <w:vAlign w:val="bottom"/>
            </w:tcPr>

            <!-- erscheinungsjahr -->
            <xsl:for-each select="prelim_volume/prelim_year">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-titelei-2"/>
                </w:pPr>
                <w:r>
                  <w:t><xsl:value-of select="./p"/></w:t>
                </w:r>
              </w:p>
            </xsl:for-each>
<!--            <xsl:for-each select="band/jahr">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-titelei-2"/>
                </w:pPr>
                <w:r>
                  <w:t><xsl:value-of select="./p"/></w:t>
                </w:r>
              </w:p>
            </xsl:for-each>-->

            <!-- verlag -->
            <xsl:for-each select="prelim_volume/prelim_publisher">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-titelei-2"/>
                </w:pPr>
                <w:r>
                  <w:t><xsl:value-of select="./p"/></w:t>
                </w:r>
              </w:p>
            </xsl:for-each>
<!--            <xsl:for-each select="band/verlag">
              <w:p>
                <w:pPr>
                  <w:pStyle w:val="epi-titelei-2"/>
                </w:pPr>
                <w:r>
                  <w:t><xsl:value-of select="./p"/></w:t>
                </w:r>
              </w:p>
            </xsl:for-each>-->

          </w:tc>
        </w:tr>
      </w:tbl>
  </xsl:template>

  <xsl:template name="titelei-band">
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
      <!-- oberes feld -->
      <w:tr>
        <w:trPr>
<!--          <w:trHeight w:val="11482"/>-->
          <w:trHeight w:val="10915"/>
        </w:trPr>
        <w:tc>
          <w:tcPr>
            <w:tcW w:w="9212" w:type="dxa"/>
          </w:tcPr>
          <!-- bandtitel -->
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
<!--          <xsl:for-each select="band/titel">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-titelei-1"/>
              </w:pPr>
              <w:r>
                <w:t><xsl:value-of select=".//p"/></w:t>
              </w:r>
            </w:p>
          </xsl:for-each>-->

          <!-- herausgeber -->
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
<!--          <xsl:for-each select="band/untertitel//p">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-titelei-2"/>
              </w:pPr>
              <w:r>
                <w:t><xsl:value-of select="."/></w:t>
              </w:r>
            </w:p>
          </xsl:for-each>-->


        </w:tc>
      </w:tr>
      <!--unteres feld-->
      <w:tr>
        <w:trPr>
          <w:trHeight w:val="1615"/>
        </w:trPr>
        <w:tc>
          <w:tcPr>
            <w:tcW w:w="9212" w:type="dxa"/>
            <w:vAlign w:val="bottom"/>
          </w:tcPr>

          <!-- erscheinungsjahr -->
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
<!--          <xsl:for-each select="band/jahr">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-titelei-2"/>
              </w:pPr>
              <w:r>
                <w:t><xsl:value-of select=".//p"/></w:t>
              </w:r>
            </w:p>
          </xsl:for-each>-->

          <!-- verlag -->
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
<!--          <xsl:for-each select="band/verlag">
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-titelei-2"/>
              </w:pPr>
              <w:r>
                <w:t><xsl:value-of select=".//p"/></w:t>
              </w:r>
            </w:p>
          </xsl:for-each>-->

        </w:tc>
      </w:tr>
    </w:tbl>
  </xsl:template>

  <xsl:template name="impressum">
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
    <xsl:if test="prelim_volume/prelim_sponsor">  <!--band/förderung  -->
    <w:tr>
        <w:trPr>
          <w:trHeight w:val="5817"/>
        </w:trPr>
        <w:tc>
          <w:tcPr>
            <w:tcW w:w="9212" w:type="dxa"/>
          </w:tcPr>
          <!-- reihentitel -->
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
      </w:tr></xsl:if>

<xsl:if test="prelim_volume/prelim_biblio_note"><!-- band/biblio -->
      <w:tr>
        <w:trPr>
          <w:trHeight w:val="4833"/>
        </w:trPr>
        <w:tc>
          <w:tcPr>
            <w:tcW w:w="9212" w:type="dxa"/>
          </w:tcPr>
          <!-- reihentitel -->
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

<xsl:if test="prelim_volume/prelim_year or prelim_volume/prelim_publisher or prelim_volume/prelim_isbn or prelim_volume/prelim_rights or prelim_volume/prelim_print_note"><!-- band/jahr or band/verlag or band/isbn or band/rechte or band/druck -->
<w:tr>
        <w:trPr>
          <w:trHeight w:val="1843"/>
        </w:trPr>
        <w:tc>
          <w:tcPr>
            <w:tcW w:w="9212" w:type="dxa"/>
            <w:vAlign w:val="bottom"/>
          </w:tcPr>

          <!-- erscheinungsjahr -->
            <w:p>
              <w:pPr>
                <w:pStyle w:val="epi-impressum-2"/>
              </w:pPr>
              <w:r>
                <w:t>© </w:t>
                <w:t> <xsl:value-of select="prelim_volume/prelim_year"/></w:t><!-- band/jahr -->
                <w:t> <xsl:value-of select="prelim_volume/prelim_publisher"/></w:t><!-- band/verlag -->
              </w:r>
            </w:p>
           <!-- isbn -->
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
          <!-- rechte -->
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
          <!-- printed in germany -->
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
  </xsl:template>
</xsl:stylesheet>
