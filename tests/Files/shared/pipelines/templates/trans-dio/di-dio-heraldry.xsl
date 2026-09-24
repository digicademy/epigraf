<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    
    <xsl:import href="../commons/di-switch.xsl"/>

    <xsl:import href="di-dio-links.xsl"/>
    <xsl:import href="di-dio-tools.xsl"/>
    
    <!-- wichtiger hinweis: der test mit dem operator &lt;= o.ä. funktioniert in xsl version 2.0 nur mit umwandlung in number() 
    Falsch:  select="row[@lfnr &lt;= @max_zeilen]" 
    Richtig: select="row[number(@lfnr) &lt;= number(@max_zeilen)]" -->
    
    
    <!-- in diesem stylesheet wird der wappen-abschnitt des artikels aufgebaut  -->
    
    
    <!--Wappentabelle-->
    <xsl:template match="heraldry">
        <!--<xsl:choose>
            <xsl:when test="count(table/row[1]/cell) = 6">
                <caption>Wappen Herzogszepter:</caption><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:for-each select=".//row[position()&lt;4]">
                    <row>
                        <xsl:for-each select="cell">
                            <cell>
                                <xsl:for-each select="coa">
                                    <entry><xsl:apply-templates select=".">
                                    </xsl:apply-templates></entry>
                                </xsl:for-each>
                            </cell>
                        </xsl:for-each>
                    </row>
                </xsl:for-each>
                <caption>Wappen Bischofszepter:</caption><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:for-each select=".//row[position()&gt;4]">
                    <row>
                        <xsl:for-each select="cell">
                            <cell>
                                <xsl:for-each select="coa">
                                    <entry><xsl:apply-templates select="."/></entry>
                                </xsl:for-each>
                            </cell>
                        </xsl:for-each>
                    </row>
                </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>     
            </xsl:when>   
            <xsl:otherwise>
                <caption><xsl:value-of select="title"/></caption><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <xsl:apply-templates select="table"/>
            </xsl:otherwise>
        </xsl:choose>-->
        <xsl:apply-templates select="table"/>
    </xsl:template>
    
    <xsl:template match="heraldry/table">
        <xsl:for-each select="row[number(@lfnr) &lt;= number(@max_zeilen)]"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <row><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:for-each select="cell">
                        <cell><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                            <xsl:for-each select="entry">
                                <entry>
                                    <xsl:apply-templates />
                                </entry>
                                <!-- komma wird im t3 erzeugt, inhalt zwischen entry sollte nicht erfolgen --> 
                                <!--xsl:if test="./following-sibling::node()">
                                    <xsl:text>, </xsl:text>
                                </xsl:if-->
                            </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                        </cell><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    </xsl:for-each>
                </row>
            
            <xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="entry[ancestor::heraldry]">
        <xsl:param name="p_wappen_id">coa:<xsl:value-of select="$p_volume_identifier"/>-coa<xsl:value-of select="tokenize(@target_id, '-')[last()]"/></xsl:param>
        <entry>
            <xsl:choose>
                <xsl:when test="starts-with(.,'?')">
                    <xsl:apply-templates/>
                </xsl:when>
                <xsl:when test="$sw_links_coa=1">
                    <ref target="{$p_wappen_id}"><xsl:apply-templates/></ref>
                </xsl:when>
                <xsl:otherwise>
                    <xsl:apply-templates/>
                </xsl:otherwise>
            </xsl:choose>
        </entry>
    </xsl:template>
   
</xsl:stylesheet>