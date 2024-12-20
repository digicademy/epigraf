<?xml version="1.0" encoding="UTF-16"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

  
  <!--============================================================================-->
  <!-- "Schalter" zum Aus- und Einblenden bestimmter Abschnitte des Layouts: -->
  <!--export document type-->
  <!--<xsl:variable name="ausgabeformat" select="normalize-space(//einstellungen/Ausgabeformat)"/>-->
  <!--export Datum letzte Änderung-->
  <xsl:variable name="datum" select="book/options/@modified"/>
  <!--Ausgabe der Signatur im Kopf des Artikels-->
  <xsl:variable name="signatur"  select="book/options/@signature"/>
  <!--Notizfeld/annotations-->
  <xsl:variable name="notizen"  select="book/options/@notes"/>
   <!--title pages-->
  <xsl:variable name="titelei" select="book/options/@preliminaries"/>
  <!--table of contents-->
  <xsl:variable name="inhalt" select="book/options/@table_of_content"/>
  <!--preface-->
  <xsl:variable name="vorwort" select="book/options/@prefaces"/>
  <!--introduction-->
  <xsl:variable name="einleitung" select="book/options/@introduction"/>
  <!--title page of the catalogue-->
  <xsl:variable name="katalogtitel" select="book/options/@catalog_title"/>
  <!--catalogue-->
  <xsl:variable name="katalog" select="book/options/@articles"/>
  <!--chronologische Liste der Inschriften-->
  <xsl:variable name="inschriftenliste" select="book/options/@table_of_inscriptions"/>
  <!--index allgemein-->
  <xsl:variable name="register" select="book/options/@indices"/>
  <!-- versregister -->
  <xsl:variable name="versregister" select="book/options/@index_verses"/>
  <!-- herstellerregister -->
  <xsl:variable name="herstellerregister" select="book/options/@index_producers"></xsl:variable>
    <!-- standorte-register -->
  <xsl:variable name="basisstandort_ausgeben" select="book/options/@base_location"></xsl:variable>
  
  <!--abbreviations-->
  <xsl:variable name="abkuerzungen" select="book/options/@abbreviations"/>
  <!--bibliography-->
  <xsl:variable name="quellen-literatur" select="book/options/@biblio"/>
  <xsl:variable name="quellen-literatur-di" select="book/options/@di_volumes_hide"/>

  
  <xsl:variable name="di-bände" select="book/options/@di-volumes"/>
  <!--Meisterzeichen und Hausmarken-->
  <xsl:variable name="marken" select="book/options/@marks"/>
  
  <xsl:variable name="markentypen_zusammenfassen" select="book/options/@marks_together"/>
  <!--Zeichnungen-->
  <xsl:variable name="zeichnungen" select="book/options/@drawings"/>
  <!--Grundrisse-->
  <xsl:variable name="grundrisse" select="book/options/@maps"/>
  <!--Bildtafeln (Titelseite und Bildnachweise-->
  <xsl:variable name="bildtafeln"  select="book/options/@plates"/>
  <!--Ausgabe der Platzhalter fuer Verweise auf Abbildungen-->
  <xsl:variable name="abbildungsverweise"  select="book/options/@image_references"/>
  <!--Ausgabe der Versalien in groesser Schrift-->
  <xsl:variable name="versalien"  select="book/options/@caps"/>
  <!-- Nummern bei Fußnoten rechtsbündig setzen -->
  <xsl:variable name="fußnoten"  select="book/options/@footnotes"/>
  <!-- Ligaturbögen anstatt unterstreichung -->
  <xsl:variable name="ligaturboegen"  select="book/options/@ligature_arcs"/>
 

 <!-- Katalog nach Signatur sortieren -->
  <xsl:variable name="sortieren_nach_signatur"  select="book/options/@sort_signatures"/>
  <!-- Wappenregister: nicht identifizierte Wappen  mit Beschreibung ausgeben-->
  <xsl:variable name="unidentifizierte_wappen_beschreiben"  select="book/options/@reg_unident_descript"/>  

 <!-- Links ausgeben -->
    <xsl:variable name="links_articles"  select="book/options/@links_articles"/>
    <xsl:variable name="links_marks"  select="book/options/@links_marks"/>
    <xsl:variable name="links_maps"  select="book/options/@links_maps"/>
    <xsl:variable name="links_inscriptions"  select="book/options/@links_inscriptions"/>
    <xsl:variable name="links_footnotes"  select="book/options/@links_footnotes"/>
    <xsl:variable name="links_lit"  select="book/options/@links_lit"/>
    <xsl:variable name="links_coa"  select="book/options/@links_coa"/>

<!-- reihespezifischer Ausgabemodus -->
  <xsl:variable name="modus"  select="book/options/@modus"/>

  <!--====Ende: Schalter/Switch===================================================-->


</xsl:stylesheet>
