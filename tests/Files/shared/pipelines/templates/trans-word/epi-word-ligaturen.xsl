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

  <!--buchstabenverbindungen-->
  <xsl:template match="all">

    <xsl:choose>
      <xsl:when test="$ligaturboegen=1"><xsl:call-template name="ligaturen"></xsl:call-template></xsl:when>
      <xsl:otherwise><xsl:apply-templates></xsl:apply-templates></xsl:otherwise>
    </xsl:choose>
   </xsl:template>

<xsl:template name="ligaturen">
  <xsl:param name="rPr">
    <w:rFonts w:ascii="Araldo" w:h-ansi="Araldo"/>
    <wx:font wx:val="Araldo"/>
    <!-- die bögen müssen höhergestellt werden, damit sie sichtbar sind -->
    <w:position w:val="2"/>
  </xsl:param>
  <xsl:choose>
    <!--Ligatur und Bogenverschmelzung und Nexus litterarum-->
    <xsl:when test="
@lemma='fsn' or
@value='Bogenverschmelzung' or
@data-link-value='Bogenverschmelzung' or
 @lemma='lig' or
@data-link-value='Ligatur' or
@value='Ligatur' or
@value='Verschränkung' or
@data-link-value='Verschränkung' or
@lemma='nex' or
contains(@value,'Nexus') or
contains(@data-link-value,'Nexus') or
@value='' or
@data-link-value=''
">
      <!--<xsl:text disable-output-escaping="yes">&lt;/w:t&gt;&lt;/w:r&gt;</xsl:text>-->
      <!--erstes zeichen mit bogen-->

      <w:r>
        <w:rPr>
          <xsl:if test="ancestor::quot">
            <w:rStyle w:val="epi-zitat"/>
          </xsl:if>
        </w:rPr>
        <w:t><xsl:value-of select="substring(.,1,1)" /></w:t></w:r>
      <!-- es gibt vier linke bögen von unterschiedlicher länge;
          sie werden hinter den ersten buchstaben gesetzt und schieben sich rechtsbündig nach links:
          kurz 0041 = A
          länger 0044 = D
          noch länger 0047 = G
          am längsten 004A = J
          -->

      <!-- es gibt vier verschieden lange sequenzen aus bogen nach links, strich nach recht, bogen nach rechts
        kurz: AB C
        mittellang: DE F
        lang: GH I
        sehr lang: JK L
        -->

      <xsl:choose>
        <xsl:when test="substring(.,1,1)= 'i' or
          substring(.,1,1)= 'j' or
          substring(.,1,1)= 'l' or
          substring(.,1,1)= 'p' or
          substring(.,1,1)= 'I' or
          substring(.,1,1)= 'J'
          ">
          <!--<fo:character font-family="Araldo" character="&#x0041;" padding-left="0.07em" padding-right="-0.07em" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0041;</w:t>
          </w:r>
        </xsl:when>
        <xsl:when test="substring(.,1,1)= 's'">
          <!--<fo:character font-family="Araldo" character="&#x0041;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0041;</w:t>
          </w:r>
        </xsl:when>
        <xsl:when test="substring(.,1,1)= 'd' or
          substring(.,1,1)= 'g' or
          substring(.,1,1)= 'm' or
          substring(.,1,1)= 'q' or
          substring(.,1,1)= 'w' or
          substring(.,1,1)= 'o'">
          <!--<fo:character font-family="Araldo" character="&#x0041;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0041;</w:t>
          </w:r>
        </xsl:when>
        <xsl:when test="substring(.,1,1)= 'I' or
          substring(.,1,1)= 'J'
          ">
          <!--<fo:character font-family="Araldo" character="&#x0041;" padding-right="-0.5pt" padding-left="0.5pt" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0041;</w:t>
          </w:r>
        </xsl:when>
        <xsl:when test="substring(.,1,1)= 'S'">
          <!--<fo:character font-family="Araldo" character="&#x0044;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0044;</w:t>
          </w:r>
        </xsl:when>
        <xsl:when test="substring(.,1,1)= 'A' or
          substring(.,1,1)= 'B' or
          substring(.,1,1)= 'C' or
          substring(.,1,1)= 'D' or
          substring(.,1,1)= 'E' or
          substring(.,1,1)= 'F' or
          substring(.,1,1)= 'H' or
          substring(.,1,1)= 'K' or
          substring(.,1,1)= 'L' or
          substring(.,1,1)= 'N' or
          substring(.,1,1)= 'O' or
          substring(.,1,1)= 'P' or
          substring(.,1,1)= 'R' or
          substring(.,1,1)= 'T' or
          substring(.,1,1)= 'U' or
          substring(.,1,1)= 'V' or
          substring(.,1,1)= 'X' or
          substring(.,1,1)= 'Y' or
          substring(.,1,1)= 'Z'
          ">
          <!--<fo:character font-family="Araldo" character="&#x0044;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0044;</w:t>
          </w:r>
        </xsl:when>
        <xsl:when test="substring(.,1,1)= 'G' or
          substring(.,1,1)= 'M' or
          substring(.,1,1)= 'Q' or
          substring(.,1,1)= 'W'
          ">
         <!--<fo:character font-family="Araldo" character="&#x0047;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0047;</w:t>
          </w:r>
        </xsl:when>

        <xsl:otherwise>
          <!--<fo:character font-family="Araldo" character="&#x0044;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0044;</w:t>
          </w:r>
        </xsl:otherwise>
      </xsl:choose>

      <!--die folgenden zeichen mit unterstrichen-->
      <xsl:call-template name="LigaturSchleife">
        <xsl:with-param name="Zaehler" select="2" />
        <xsl:with-param name="Ende" select="string-length()-1" />
      </xsl:call-template>

      <!--letztes zeichen mit bogen-->
      <xsl:choose>
        <!-- es gibt vier rechte bögen von unterschiedlicher länge;
          sie werden vor den letzten buchstaben gesetzt und schieben sich linksbündig nach rechts:
          kurz 0043 = C
          länger 0046 = F
          noch länger 0049 = I
          am längsten 004C = L
          -->

        <xsl:when test="substring(.,string-length())= 'p'
          ">
          <!--<fo:character font-family="Araldo" character="&#x0043;" padding-left="0.07em" padding-right="-0.07em" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0043;</w:t>
          </w:r>
        </xsl:when>
        <xsl:when test="substring(.,string-length())= 'i' or
          substring(.,string-length())= 'j' or
          substring(.,string-length())= ')' or
          substring(.,string-length())= 'l'
          ">
          <!--<fo:character font-family="Araldo" character="&#x0043;" padding-left="-0.07em" padding-right="0.07em" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0043;</w:t>
          </w:r>
        </xsl:when>
        <xsl:when test="substring(.,string-length())= 's'">
          <!--<fo:character font-family="Araldo" character="&#x0043;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0043;</w:t>
          </w:r>
        </xsl:when>
        <xsl:when test="substring(.,string-length())= 'd' or
          substring(.,string-length())= 'g' or
          substring(.,string-length())= 'm' or
          substring(.,string-length())= 'q' or
          substring(.,string-length())= 'w' or
          substring(.,string-length())= 'o'
          ">
          <!--<fo:character font-family="Araldo" character="&#x0043;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0043;</w:t>
          </w:r>
        </xsl:when>
        <xsl:when test="substring(.,string-length())= 'I' or
          substring(.,string-length())= 'R' or
          substring(.,string-length())= 'J'
          ">
          <!--<fo:character font-family="Araldo" character="&#x0043;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0043;</w:t>
          </w:r>
        </xsl:when>

        <xsl:when test="substring(.,string-length())= 'S'">
          <!--<fo:character font-family="Araldo" character="&#x0046;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0046;</w:t>
          </w:r>
        </xsl:when>
        <xsl:when test="substring(.,string-length())= 'A' or
          substring(.,string-length())= 'B' or
          substring(.,string-length())= 'C' or
          substring(.,string-length())= 'D' or
          substring(.,string-length())= 'E' or
          substring(.,string-length())= 'F' or
          substring(.,string-length())= 'H' or
          substring(.,string-length())= 'K' or
          substring(.,string-length())= 'L' or
          substring(.,string-length())= 'N' or
          substring(.,string-length())= 'O' or
          substring(.,string-length())= 'P' or
          substring(.,string-length())= 'T' or
          substring(.,string-length())= 'U' or
          substring(.,string-length())= 'V' or
          substring(.,string-length())= 'X' or
          substring(.,string-length())= 'Y' or
          substring(.,string-length())= 'Z'
          ">
          <!--<fo:character font-family="Araldo" character="&#x0046;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0046;</w:t>
          </w:r>
        </xsl:when>
        <xsl:when test="substring(.,string-length())= 'G' or
          substring(.,string-length())= 'M' or
          substring(.,string-length())= 'Q' or
          substring(.,string-length())= 'W'
          ">
          <!--<fo:character font-family="Araldo" character="&#x0049;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0049;</w:t>
          </w:r>
        </xsl:when>
        <xsl:otherwise>
          <!--<fo:character font-family="Araldo" character="&#x0046;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
          <w:r>
            <w:rPr>
              <xsl:copy-of select="$rPr"/>
            </w:rPr>
            <w:t>&#x0046;</w:t>
          </w:r>
        </xsl:otherwise>

      </xsl:choose>
      <w:r>
        <w:rPr>
          <xsl:if test="ancestor::quot">
            <w:rStyle w:val="epi-zitat"/>
          </xsl:if>
        </w:rPr>
        <w:t><xsl:value-of select="substring(.,string-length())" /></w:t></w:r>
      <!--<xsl:text disable-output-escaping="yes">&lt;w:t&gt;&lt;w:r&gt;</xsl:text>-->
    </xsl:when>
    <!--sonstige buchstabenverbindungen-->
    <xsl:otherwise>
      <xsl:value-of select="."/>
    </xsl:otherwise>
  </xsl:choose>
</xsl:template>

<xsl:template name="LigaturSchleife">
  <xsl:param name="rPr">
    <w:rFonts w:ascii="Araldo" w:h-ansi="Araldo"/>
    <wx:font wx:val="Araldo"/>
    <!-- die bögen müssen höhergestellt werden, damit sie sichtbar sind -->
    <w:position w:val="2"/>
  </xsl:param>
  <xsl:param name="Zaehler" />
 	  <xsl:param name="Ende" />
  	  <xsl:if test="$Zaehler &lt;= $Ende">
        <xsl:choose>
          <!--
          die striche unter den zwischenbuchstaben werden vor die buchstaben gesetzt und schieben sich linksbündig nach recht unter dieselben;
          es gibt vier längen:
          kurz 0042 = B
          länger 0045 = E
          noch länger 0048 = H
          am längsten 004B = K
          -->
          <xsl:when test="substring(.,$Zaehler,1)= 'i' or
                          substring(.,$Zaehler,1)= 'j' or
                          substring(.,$Zaehler,1)= ']' or
                          substring(.,$Zaehler,1)= '[' or
                          substring(.,$Zaehler,1)= '(' or
                          substring(.,$Zaehler,1)= ')'
                                ">
            <!--<fo:character font-family="Araldo" character="c" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
            <w:r>
              <w:rPr>
                <xsl:copy-of select="$rPr"/>
              </w:rPr>
              <xsl:if test="not(noAll)"><w:t>&#x0042;</w:t></xsl:if>
            </w:r>
          </xsl:when>
          <xsl:when test="substring(.,$Zaehler,1)= ' ' ">
            <!--<fo:character font-family="Araldo" character="&#x0045;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
            <w:r>
              <w:rPr>
                <xsl:copy-of select="$rPr"/>
              </w:rPr>
              <w:t>&#x0045;</w:t>
            </w:r>
          </xsl:when>
          <xsl:when test="substring(.,$Zaehler,1)= 'a' or
                          substring(.,$Zaehler,1)= 'c' or
                          substring(.,$Zaehler,1)= 's' or
                          substring(.,$Zaehler,1)= 'S'
                          ">
            <!--<fo:character font-family="Araldo" character="&#x0045;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
            <w:r>
              <w:rPr>
                <xsl:copy-of select="$rPr"/>
              </w:rPr>
              <xsl:if test="not(noAll)"><w:t>&#x0045;</w:t></xsl:if>
            </w:r>
          </xsl:when>
          <xsl:when test="substring(.,$Zaehler,1)= 'd' or
                          substring(.,$Zaehler,1)= 'g' or
                          substring(.,$Zaehler,1)= 'm' or
                          substring(.,$Zaehler,1)= 'q' or
                          substring(.,$Zaehler,1)= 'w' or
                          substring(.,$Zaehler,1)= 'o'">
            <!--<fo:character font-family="Araldo" character="&#x004B;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
            <w:r>
              <w:rPr>
                <xsl:copy-of select="$rPr"/>
              </w:rPr>
              <xsl:if test="not(noAll)"><w:t>&#x004B;</w:t></xsl:if>
            </w:r>
          </xsl:when>
          <xsl:when test="substring(.,$Zaehler,1)= 'A' or
                           substring(.,$Zaehler,1)= 'D' or
                           substring(.,$Zaehler,1)= 'G' or
                           substring(.,$Zaehler,1)= 'H' or
                           substring(.,$Zaehler,1)= 'K' or
                           substring(.,$Zaehler,1)= 'M' or
                           substring(.,$Zaehler,1)= 'N' or
                           substring(.,$Zaehler,1)= 'O' or
                           substring(.,$Zaehler,1)= 'Q' or
                           substring(.,$Zaehler,1)= 'U' or
                           substring(.,$Zaehler,1)= 'W'
                          ">
            <!--<fo:character font-family="Araldo" character="&#x004B;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
            <w:r>
              <w:rPr>
                <xsl:copy-of select="$rPr"/>
              </w:rPr>
              <xsl:if test="not(noAll)"><w:t>&#x004B;</w:t></xsl:if>
            </w:r>
          </xsl:when>
          <xsl:when test="substring(.,$Zaehler,1)= 'B' or
                           substring(.,$Zaehler,1)= 'C' or
                           substring(.,$Zaehler,1)= 'E' or
                           substring(.,$Zaehler,1)= 'F' or
                           substring(.,$Zaehler,1)= 'L' or
                           substring(.,$Zaehler,1)= 'P' or
                           substring(.,$Zaehler,1)= 'R' or
                           substring(.,$Zaehler,1)= 'T' or
                           substring(.,$Zaehler,1)= 'V' or
                           substring(.,$Zaehler,1)= 'X' or
                           substring(.,$Zaehler,1)= 'Y' or
                           substring(.,$Zaehler,1)= 'Z'
                           ">
            <!--<fo:character font-family="Araldo" character="&#x0048;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
            <w:r>
              <w:rPr>
                <xsl:copy-of select="$rPr"/>
              </w:rPr>
              <xsl:if test="not(noAll)"><w:t>&#x0048;</w:t></xsl:if>
            </w:r>
          </xsl:when>

          <xsl:when test="substring(.,$Zaehler,1)= 'I' or
                          substring(.,$Zaehler,1)= 'J'
                          ">
            <!--<fo:character font-family="Araldo" character="&#x0045;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
            <w:r>
              <w:rPr>
                <xsl:copy-of select="$rPr"/>
              </w:rPr>
                <xsl:if test="not(noAll)"><w:t>&#x0045;</w:t></xsl:if>
           </w:r>
          </xsl:when>
          <xsl:otherwise>
     <!-- <fo:character font-family="Araldo" character="&#x0048;" keep-with-next.within-line="always" keep-with-previous.within-line="always"/>-->
            <w:r>
              <w:rPr>
                <xsl:copy-of select="$rPr"/>
              </w:rPr>
              <xsl:if test="not(noAll)"><w:t>&#x0048;</w:t></xsl:if>
            </w:r>
          </xsl:otherwise>
        </xsl:choose>
  	    <w:r>
  	      <w:rPr>
  	        <xsl:if test="ancestor::quot">
  	          <w:rStyle w:val="epi-zitat"/>
  	        </xsl:if>
  	      </w:rPr>
  	      <w:t><xsl:value-of select="substring(.,$Zaehler,1)"/></w:t></w:r>
        <xsl:call-template name="LigaturSchleife">
          <xsl:with-param name="Zaehler" select="$Zaehler + 1" />
          <xsl:with-param name="Ende" select="$Ende" />
        </xsl:call-template>
      </xsl:if>
</xsl:template>



</xsl:stylesheet>
