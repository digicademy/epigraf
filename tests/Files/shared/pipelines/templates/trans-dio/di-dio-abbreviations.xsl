<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/>
    
    <xsl:import href="di-dio-links.xsl"/>
    <xsl:import href="di-dio-tools.xsl"/>
    
    <xsl:template match="abbreviations">
        <xsl:param name="p_iri" />

            <tt_content iri="{$p_iri}-abbr-header"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>header</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header><xsl:value-of select="title"/></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>3</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext></bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <tt_content iri="{$p_iri}-abbr-text"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>text</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext>
                    <xsl:apply-templates select="p"></xsl:apply-templates>
                </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <tt_content iri="{$p_iri}-abbr-table"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <type>table</type><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header></header><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <header_layout>100</header_layout><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                    <xsl:apply-templates select="table"></xsl:apply-templates>
                </bodytext><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
                <pi_flexform>                        
                    <T3FlexForms>
                        <data>
                            <sheet index="sDEF">
                                <language index="lDEF">
                                    <field index="acctables_caption">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="acctables_summary">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="acctables_tfoot">
                                        <value index="vDEF">0</value>
                                    </field>
                                    <field index="acctables_headerpos">
                                        <value index="vDEF">left</value>
                                    </field>
                                    <field index="acctables_nostyles">
                                        <value index="vDEF">0</value>
                                    </field>
                                    <field index="acctables_tableclass">
                                        <value index="vDEF">literature</value>
                                    </field>
                                </language>
                            </sheet>
                            <sheet index="s_parsing">
                                <language index="lDEF">
                                    <field index="tableparsing_quote">
                                        <value index="vDEF"></value>
                                    </field>
                                    <field index="tableparsing_delimiter">
                                        <value index="vDEF">124</value>
                                    </field>
                                </language>
                            </sheet>
                        </data>
                    </T3FlexForms>
                </pi_flexform>
            </tt_content><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    </xsl:template>
    
</xsl:stylesheet>