<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" 
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
    xmlns:fn="http://www.w3.org/2005/xpath-functions"
     xmlns:php="http://php.net/xsl"
        xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
    >
    <xsl:import href="commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="trans2/di-trans2-book.xsl"/>
    <xsl:import href="trans2/di-trans2-articles.xsl"/>

<!-- 
    dieses stylesheet stößt die zweite transformationsstufe beim export der di-daten an;
    es ist anzuwenden auf das exportergebnis der ersten transformationsstufe, 
    die mit di-trans1.xsl in gang gesetzt wurde. 
    
    es eröffnet wahlweise die transformation für zwei verschiedene exportergebnisse, für:
    A) den gesamten inschriftenband (book-band),
    B) nur den katalog der inschriftenartikel (book-katalog), optionsweise zuzüglich der register 
    
    ad A) der inschriftenband wird über das template book_band 
          in der datei di-trans1-book.xsl weiter verarbeitet,
    ad B) der katalog der inschriftenartikel wird über das template book_katalog 
          in der datei di-trans1-articles.xsl weiter verarbeitet
    
    -->
    <!-- ================================================================== -->
    <!-- AUSGANGSSITUATION und ZIEL:
    
    aus der ersten transformation ist folgende gliederung der hauptelemente hervorgegangen:
    <book>
        <options/>
        <project/>
        <volume/>
        <articles/>
        <indices/>
        <managa_lists/>
    </book>
    
    die zweite transformation kann daraus zwei ergebnisse hervorbringen:
    A) den gesamten band, angestoßen durch das template book_volume in trans2/di-trans2-book.xsl
    <book>
        <options/> kopiert
        <project/> kopiert
        <preliminaries/>
        <prefaces/>
        <table_of_content/>
        <inroduction/>
        <articles/>
        <table_of_inscriptions/>
        <indices/>
        <abbreviations/>
        <di-volumes/>
        <drawings/>
        <plates/>
        <footnotes/> fußnoten der einleitung
        <links/> verknüpfungen
        <managelists> kopiert
    </book>

    B) eine artikelserie zuzüglich register, angestoßen durch das template book-articles in trans2/di-trans2-articles.xsl
    <book>
        <options/> kopiert
        <project/> kopiert
        <volume/>
        <articles/>
        <indices/>
        <brands/>
        <managelists> kopiert
    </book>    
    das element <manga_list> entfällt, ein element <marken> zur ergänzung der register wird hinzugefügt
    -->

<!-- ==BEGINN TRANSFORMATION========================================================================= -->    

<!-- wurzelknoten ansteuern und an templates verweisen -->
    <xsl:template match="/">
        <xsl:apply-templates/>
    </xsl:template>

    <!-- wurzelelement <book> ansteuern;
            je nach dem, ob der bandartikel (element <volume>) 
            von dem vorherigen stylesheet (trans0-book.xsl) 
            mitgegeben wurde oder nicht, wird es an jeweils ein anderes template verwiesen-->    
    <xsl:template match="book">
        <xsl:choose>
            <!-- wenn das element <volume> einschließlich unterelementen vorhanden ist,
                    wird das template zum erzeugen eines vollständigen inschriftenbandes aufgerufen-->
            <xsl:when test="volume/section[@data_key[string()]]">
                <xsl:comment>book_band</xsl:comment>
                <xsl:call-template name="book_volume">
                    <!-- dieses template befindet sich in di-trans2-book.xsl -->
                </xsl:call-template>
            </xsl:when>
            <!-- ist das element <volume> nicht vorhanden, wir nur der 
                inschriftenkatalog, optionsweise mit registern, erzeugt -->
            <xsl:otherwise>
                <xsl:comment>book_katalog</xsl:comment>
                <xsl:call-template name="book_articles">
                    <!-- dieses template befindet sich in di-trans2-articles.xsl -->
                </xsl:call-template>
            </xsl:otherwise>
        </xsl:choose>
    </xsl:template>
    
    
    <!-- zur besseren übersichtlichkeit sind die beiden templates book_band und  book_katalog 
         jeweils in eine eigene datei ausgelagert, zusammen mit den für das eine oder andere 
         zutreffenden weiteren (darin aufgerufenen) templates;
         templates die auf beide zutreffen, sind in trans1-commons.xsl enthalten            
    -->
 
</xsl:stylesheet>