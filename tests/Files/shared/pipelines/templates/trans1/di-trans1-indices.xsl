<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:import href="../commons/di-switch.xsl"/><!-- die variablen in der switch-datei beginnen alle mit sw_ -->
    <xsl:import href="di-trans1-index-locations.xsl"/>
    <xsl:import href="di-trans1-index-texttypes.xsl"/>
    <xsl:import href="di-trans1-index-persons.xsl"/>
    <xsl:import href="di-trans1-index-literature.xsl"/>
    <xsl:import href="di-trans1-indices-commons.xsl"/>
    <xsl:import href="di-trans1-commons.xsl"/>
    <xsl:output indent="no"/>  

    <!-- 
        mit diesem stylesheet werden die register vorstrukturiert;
        das register der standorte, der texttypen und das literaturverzeichnis 
        erfordern eine gesonderte behandlung, die in drei 
        ausgelagerten stylesheets erfolgt (siehe <xsl:import>).
    
    -->
    
    <!-- die elemente <index> werden auf zwei gruppen aufgeteilt:
    die gruppe <indices> enthält diejenigen kategorien, die für die erstellung der register benötigt werden,
    die gruppe <manage-lists> enthält steuerungslisten, auf die bei der datentransformation zurückzugreifen ist;
    
    die elemente <index> werden zudem um die attribute 
      @role="index|false"
      @group="initial|false"
      @snr="[0-9]{1,2}[a-z]?"
      @title="/d*"
    erweitert, die für die ausgabe/darstellung in anwendung kommen,
    diese attribute werden den angaben in book/types/type entnommen.
    
    die elemente <property> werden in <item> umgewidnet, die attribute auf @id und @level reduziert
    
    bei den elementen <lemma> wird das attribut @sortkey in @sortstring umgewidmet, 
    der attribuwert wird normalisiert und in kleinbuchstaben ausgegeben,
    zusätzlich wird aus dem anfangsbuchstaben des attributs @sortstring ein attribut @sortchart erzeugt,
    das attribut @level wird aus dem parent-element <property> herangezogen
    
    das element <name> wird kopiert
    
    ======================
    
    das literaturverzeichnis wird aus den tatsächlich referenzierten titeln neu erstellt,
    um verwaiste titel, die an manchen stellen im code versteckt noch vorkommen und 
    dadurch im <index> aufgeführt sind, auszuschließen.
    
    im standorteregister werden den referenzen auf artikelnummern/inschriften (element <section>)
    die attribute @before="0|1" und @lost="0|1" hinzugefügt
    
    im texttypenregister werden den referenzen auf artikelnummern/inschriften (element <section>)
    folgende attribute hinzugefügt:
     @language       (sprache, z. b. "lateinisch")
     @lang_id        (id des spracheintrags im sprachen-register) 
     @lang_alias     (sprachkürzel, z. b. "lat.")
     @lang_sortkey   (eine ziffer)
     @metres="0|1"   (ob die entsprechende inschrift ein metrum enthält)
    -->

    <!-- transformationsmuster anlegen -->
    <xsl:template name="indices">
        <!-- hauptparameter aus dem übergeordneten stylesheet übernehmen -->
        <xsl:param name="p_project-name"></xsl:param>
        <xsl:param name="p_project-signature"></xsl:param>
        <xsl:param name="p_project-database"></xsl:param>

        <!-- in einem weiteren parameter die indices strukturieren -->
        <xsl:param name="p_indices_restruct">
          <!-- zuerst die type-spezifikationen der register aufsuchen,
          bestimmte attribute und elemente in variablen speichern-->
          <xsl:for-each select="types/type">
  
               <!-- attribut @norm_iri in der spezifikation entspricht dem attribut @propertytype des registerbaums -->
              <xsl:variable name="v_norm-iri"><xsl:value-of select="@norm_iri"/></xsl:variable>
  
               <!-- <role>index</role> gibt an, dass das register in der publikation ausgegeben wird;
                    ist das element leer, handelt es sich um eine steuerungsliste (manage_list>-->
              <xsl:variable name="v_role"><xsl:value-of select="config/role"/></xsl:variable>
  
              <!-- <group>initials</group> gibt an, dass die registereinträge in blöcke mit 
                   demselben initialbuchstaben zusammengefasst werden;
                   d. h. nach dem wechsel des initialbuchstabens soll ein durchschuss erfolgen -->
              <xsl:variable name="v_group"><xsl:value-of select="config/export/group"/></xsl:variable>
              
              <!-- laufnummern des registers, wenn vorhanden -->
              <xsl:variable name="v_snr"><xsl:value-of select="config/export/snr"/></xsl:variable>
  
              <!-- bezeichnung des registers (klarname) -->
              <xsl:variable name="v_title"><xsl:value-of select="config/export/title"/></xsl:variable>
  
              <!-- von jeder type-spezifikation zu dem jeweils betreffenden registerbaum steuern -->
              <xsl:for-each select="ancestor::book/index[@propertytype=$v_norm-iri]">
                  <!-- element <index> neu anlegen und mit den attributen gemäß der type-spezifikation versehen -->
                  <index propertytype="{$v_norm-iri}" role="{$v_role}" group="{$v_group}" snr="{$v_snr}" title="{$v_title}" log1="indType">
                      <!-- templates zur transformation der der registerbäume aufrufen -->
                      <xsl:choose>
                          <!-- Das Literaturregister erfordert eine Sonderbehandlung: 
                               nicht im aktuell exportierten Bestand verwendete Verweisziele/Titel aussortieren. 
                               TODO: Prüfen, inwiefern das in index-restruct nun zum Aussortieren verwendete Muster generalisiert werden kann,
                               sodass es auch Unterebenen erfasst, um die Sonderbehandlung von Literatur zu entfernen.
                          -->
                          <xsl:when test="$v_norm-iri='literature'"><xsl:call-template name="litRefs"/></xsl:when>
                          
                          <!-- Das Personenregister erfordert eine Sonderbehandlung:
                               Ordnungszahlen in geschweiften Klammern neu vergeben.
                          -->
                          <xsl:when test="@propertytype='personnames'"><xsl:call-template name="index-persons" /></xsl:when>
                          
                          <!-- Alle anderen indices weden gleich behandelt -->
                          <xsl:otherwise><xsl:call-template name="index-restruct" /></xsl:otherwise>
                      </xsl:choose>
                  </index><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
              </xsl:for-each>
          </xsl:for-each>    
        </xsl:param>
        
        <!-- parametertest         
            <indextest><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>       
                <xsl:copy-of select="$p_indices_restruct"></xsl:copy-of>
            </indextest>  -->
           
        <!-- die im parameter formatierten indices werden in 
            zwei abteilungen - register und steuerungslisten - getrennt; 
            die differenzierung erfolgt nach dem attribut @role-->
    
        <!-- die abteilung register wird angelegt  -->
        <indices log1="ind1"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>     
           <xsl:for-each select="$p_indices_restruct/index[@role='index']">
               <xsl:copy-of select="." /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
           </xsl:for-each>
        </indices><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    
        <!-- die abteilung manage_lists (steuerungslisten) wird angelegt  -->
        <manage_lists log1="mL1"><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            <xsl:for-each select="$p_indices_restruct/index[not(@role='index')]">
                <xsl:copy-of select="." /><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
            </xsl:for-each><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>            
        </manage_lists><xsl:text disable-output-escaping="yes">&#x000A;</xsl:text>
    
    </xsl:template>

</xsl:stylesheet>