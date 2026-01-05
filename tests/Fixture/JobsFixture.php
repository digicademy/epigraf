<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * JobsFixture
 */
class JobsFixture extends TestFixture
{
    /**
     * Database connection
     *
     * @var string
     */
    public $connection = 'test';


    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'created' => '2021-07-09 11:29:39',
                'modified' => '2021-07-09 11:29:44',
                'jobtype' => 'export',
                'status' => 'download',
                'progress' => 5,
                'progressmax' => 4,
                'config' => array (
  'database' => 'epi_playground',
  'project_id' => '20',
  'pipeline_id' => '18',
  'articles_ids' => '7953',
  'tasks' =>
  array (
    'data' =>
    array (
      'objects' => false,
      'index' => 0,
      'text' => 0,
    ),
    'options' =>
    array (
      0 =>
      array (
        'number' => '1',
        'output' => 1,
        'category' => 'Allgemeines',
        'label' => 'Signatur anzeigen',
        'key' => 'signature',
        'radio' => '0',
        'value' => '',
      ),
      1 =>
      array (
        'number' => '2',
        'output' => 0,
        'category' => 'Allgemeines',
        'label' => 'Notizen',
        'key' => 'notes',
        'radio' => '0',
        'value' => '',
      ),
      2 =>
      array (
        'number' => '3',
        'output' => 0,
        'category' => 'Allgemeines',
        'label' => 'Letzte Änderung',
        'key' => 'modified',
        'radio' => '0',
        'value' => '',
      ),
      3 =>
      array (
        'number' => '4',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Titelei',
        'key' => 'preliminaries',
        'radio' => '0',
        'value' => '',
      ),
      4 =>
      array (
        'number' => '5',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Vorwort',
        'key' => 'prefaces',
        'radio' => '0',
        'value' => '',
      ),
      5 =>
      array (
        'number' => '6',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Einleitung',
        'key' => 'introduction',
        'radio' => '0',
        'value' => '',
      ),
      6 =>
      array (
        'number' => '7',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Chronologische Liste der Inschriften',
        'key' => 'table_of_inscriptions',
        'radio' => '0',
        'value' => '',
      ),
      7 =>
      array (
        'number' => '8',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Register',
        'key' => 'indices',
        'radio' => '0',
        'value' => '',
      ),
      8 =>
      array (
        'number' => '9',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Abkürzungen',
        'key' => 'abbreviations',
        'radio' => '0',
        'value' => '',
      ),
      9 =>
      array (
        'number' => '10',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Quellen und Literatur',
        'key' => 'biblio',
        'radio' => '0',
        'value' => '',
      ),
      10 =>
      array (
        'number' => '11',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Liste der bisher erschienenen DI-Bände',
        'key' => 'di-volumes',
        'radio' => '0',
        'value' => '',
      ),
      11 =>
      array (
        'number' => '12',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Zeichnungen',
        'key' => 'drawings',
        'radio' => '0',
        'value' => '',
      ),
      12 =>
      array (
        'number' => '13',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Grundrisse',
        'key' => 'maps',
        'radio' => '0',
        'value' => '',
      ),
      13 =>
      array (
        'number' => '14',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Bildtafeln',
        'key' => 'plates',
        'radio' => '0',
        'value' => '',
      ),
      14 =>
      array (
        'number' => '15',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Inhaltsverzeichnis',
        'key' => 'table_of_content',
        'radio' => '0',
        'value' => '',
      ),
      15 =>
      array (
        'number' => '16',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Katalogtitel',
        'key' => 'catalog_title',
        'radio' => '0',
        'value' => '',
      ),
      16 =>
      array (
        'number' => '17',
        'output' => 1,
        'category' => 'Band',
        'label' => 'Katalog der Inschriften',
        'key' => 'articles',
        'radio' => '0',
        'value' => '',
      ),
      17 =>
      array (
        'number' => '18',
        'output' => 0,
        'category' => 'Marken',
        'label' => 'Marken ausgeben',
        'key' => 'marks',
        'radio' => '0',
        'value' => '',
      ),
      18 =>
      array (
        'number' => '19',
        'output' => 0,
        'category' => 'Marken',
        'label' => 'zu einer Kategorie zusammenfassen',
        'key' => 'marks_together',
        'radio' => '0',
        'value' => '',
      ),
      19 =>
      array (
        'number' => '20',
        'output' => 0,
        'category' => 'Sonstiges',
        'label' => 'Verweise auf Abbildungen in den Artikeln',
        'key' => 'image_references',
        'radio' => '0',
        'value' => '',
      ),
      20 =>
      array (
        'number' => '21',
        'output' => 0,
        'category' => 'Sonstiges',
        'label' => 'bei Fußnoten Nummern rechtsbündig',
        'key' => 'footnotes',
        'radio' => '0',
        'value' => '',
      ),
      21 =>
      array (
        'number' => '22',
        'output' => 0,
        'category' => 'Sonstiges',
        'label' => 'Versalien',
        'key' => 'caps',
        'radio' => '0',
        'value' => '',
      ),
      22 =>
      array (
        'number' => '23',
        'output' => 0,
        'category' => 'Sonstiges',
        'label' => 'Sortierung nach Signatur',
        'key' => 'sort_signatures',
        'radio' => '0',
        'value' => '',
      ),
      23 =>
      array (
        'number' => '24',
        'output' => 0,
        'category' => 'Register',
        'label' => 'separates Versregister',
        'key' => 'index_verses',
        'radio' => '0',
        'value' => '',
      ),
      24 =>
      array (
        'number' => '25',
        'output' => 0,
        'category' => 'Register',
        'label' => 'separates Register der Meister und Werkstätten',
        'key' => 'index_producers',
        'radio' => '0',
        'value' => '',
      ),
      25 =>
      array (
        'number' => '26',
        'output' => 0,
        'category' => 'Register',
        'label' => 'nicht identifizierte Wappen mit Beschreibung ausgeben',
        'key' => 'reg_unident_descript',
        'radio' => '0',
        'value' => '',
      ),
      26 =>
      array (
        'number' => '27',
        'output' => 0,
        'category' => 'Standorte-Register',
        'label' => 'Basis-Standort anzeigen',
        'key' => 'base_location',
        'radio' => '0',
        'value' => '',
      ),
      27 =>
      array (
        'number' => '28',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Artikel',
        'key' => 'links_articles',
        'radio' => '0',
        'value' => '',
      ),
      28 =>
      array (
        'number' => '29',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Marken',
        'key' => 'links_marks',
        'radio' => '0',
        'value' => '',
      ),
      29 =>
      array (
        'number' => '30',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Grafikobjekte',
        'key' => 'links_maps',
        'radio' => '0',
        'value' => '',
      ),
      30 =>
      array (
        'number' => '31',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Inschriften',
        'key' => 'links_inscriptions',
        'radio' => '0',
        'value' => '',
      ),
      31 =>
      array (
        'number' => '32',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Fußnoten',
        'key' => 'links_footnotes',
        'radio' => '0',
        'value' => '',
      ),
      32 =>
      array (
        'number' => '33',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Literatur',
        'key' => 'links_lit',
        'radio' => '0',
        'value' => '',
      ),
      33 =>
      array (
        'number' => '34',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Wappen',
        'key' => 'links_coa',
        'radio' => '0',
        'value' => '',
      ),
      34 =>
      array (
        'number' => '35',
        'output' => 0,
        'category' => 'Ausgabemodus',
        'label' => 'Münchner Reihe',
        'key' => 'modus',
        'radio' => '1',
        'value' => 'projects_bay',
      ),
      35 =>
      array (
        'number' => '36',
        'output' => 1,
        'category' => 'Ausgabemodus',
        'label' => 'die anderen Reihen',
        'key' => 'modus',
        'radio' => '1',
        'value' => 'projects_all',
      ),
      36 =>
      array (
        'number' => '37',
        'output' => 1,
        'category' => 'Ligaturen',
        'label' => 'unterstreichen',
        'key' => 'ligature_arcs',
        'radio' => '1',
        'value' => '',
      ),
      37 =>
      array (
        'number' => '38',
        'output' => 0,
        'category' => 'Ligaturen',
        'label' => 'Ligaturbögen',
        'key' => 'ligature_arcs',
        'radio' => '1',
        'value' => '1',
      ),
    ),
  ),
  'pipeline_name' => 'Entwicklung4.4',
  'pipeline_tasks' =>
  array (
    0 =>
    array (
      'number' => '1',
      'type' => 'export',
      'data' =>
      array (
        'objects' => '1',
        'index' => '1',
        'text' => '1',
      ),
      'options' =>
      array (
        0 =>
        array (
          'number' => '1',
          'output' => '1',
          'category' => 'Allgemeines',
          'label' => 'Signatur anzeigen',
          'key' => 'signature',
          'radio' => '0',
          'value' => '',
        ),
        1 =>
        array (
          'number' => '2',
          'output' => '0',
          'category' => 'Allgemeines',
          'label' => 'Notizen',
          'key' => 'notes',
          'radio' => '0',
          'value' => '',
        ),
        2 =>
        array (
          'number' => '3',
          'output' => '0',
          'category' => 'Allgemeines',
          'label' => 'Letzte Änderung',
          'key' => 'modified',
          'radio' => '0',
          'value' => '',
        ),
        3 =>
        array (
          'number' => '4',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Titelei',
          'key' => 'preliminaries',
          'radio' => '0',
          'value' => '',
        ),
        4 =>
        array (
          'number' => '5',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Vorwort',
          'key' => 'prefaces',
          'radio' => '0',
          'value' => '',
        ),
        5 =>
        array (
          'number' => '6',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Einleitung',
          'key' => 'introduction',
          'radio' => '0',
          'value' => '',
        ),
        6 =>
        array (
          'number' => '7',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Chronologische Liste der Inschriften',
          'key' => 'table_of_inscriptions',
          'radio' => '0',
          'value' => '',
        ),
        7 =>
        array (
          'number' => '8',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Register',
          'key' => 'indices',
          'radio' => '0',
          'value' => '',
        ),
        8 =>
        array (
          'number' => '9',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Abkürzungen',
          'key' => 'abbreviations',
          'radio' => '0',
          'value' => '',
        ),
        9 =>
        array (
          'number' => '10',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Quellen und Literatur',
          'key' => 'biblio',
          'radio' => '0',
          'value' => '',
        ),
        10 =>
        array (
          'number' => '11',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Liste der bisher erschienenen DI-Bände',
          'key' => 'di-volumes',
          'radio' => '0',
          'value' => '',
        ),
        11 =>
        array (
          'number' => '12',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Zeichnungen',
          'key' => 'drawings',
          'radio' => '0',
          'value' => '',
        ),
        12 =>
        array (
          'number' => '13',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Grundrisse',
          'key' => 'maps',
          'radio' => '0',
          'value' => '',
        ),
        13 =>
        array (
          'number' => '14',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Bildtafeln',
          'key' => 'plates',
          'radio' => '0',
          'value' => '',
        ),
        14 =>
        array (
          'number' => '15',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Inhaltsverzeichnis',
          'key' => 'table_of_content',
          'radio' => '0',
          'value' => '',
        ),
        15 =>
        array (
          'number' => '16',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Katalogtitel',
          'key' => 'catalog_title',
          'radio' => '0',
          'value' => '',
        ),
        16 =>
        array (
          'number' => '17',
          'output' => '1',
          'category' => 'Band',
          'label' => 'Katalog der Inschriften',
          'key' => 'articles',
          'radio' => '0',
          'value' => '',
        ),
        17 =>
        array (
          'number' => '18',
          'output' => '0',
          'category' => 'Marken',
          'label' => 'Marken ausgeben',
          'key' => 'marks',
          'radio' => '0',
          'value' => '',
        ),
        18 =>
        array (
          'number' => '19',
          'output' => '0',
          'category' => 'Marken',
          'label' => 'zu einer Kategorie zusammenfassen',
          'key' => 'marks_together',
          'radio' => '0',
          'value' => '',
        ),
        19 =>
        array (
          'number' => '20',
          'output' => '0',
          'category' => 'Sonstiges',
          'label' => 'Verweise auf Abbildungen in den Artikeln',
          'key' => 'image_references',
          'radio' => '0',
          'value' => '',
        ),
        20 =>
        array (
          'number' => '21',
          'output' => '0',
          'category' => 'Sonstiges',
          'label' => 'bei Fußnoten Nummern rechtsbündig',
          'key' => 'footnotes',
          'radio' => '0',
          'value' => '',
        ),
        21 =>
        array (
          'number' => '22',
          'output' => '0',
          'category' => 'Sonstiges',
          'label' => 'Versalien',
          'key' => 'caps',
          'radio' => '0',
          'value' => '',
        ),
        22 =>
        array (
          'number' => '23',
          'output' => '0',
          'category' => 'Sonstiges',
          'label' => 'Sortierung nach Signatur',
          'key' => 'sort_signatures',
          'radio' => '0',
          'value' => '',
        ),
        23 =>
        array (
          'number' => '24',
          'output' => '0',
          'category' => 'Register',
          'label' => 'separates Versregister',
          'key' => 'index_verses',
          'radio' => '0',
          'value' => '',
        ),
        24 =>
        array (
          'number' => '25',
          'output' => '0',
          'category' => 'Register',
          'label' => 'separates Register der Meister und Werkstätten',
          'key' => 'index_producers',
          'radio' => '0',
          'value' => '',
        ),
        25 =>
        array (
          'number' => '26',
          'output' => '0',
          'category' => 'Register',
          'label' => 'nicht identifizierte Wappen mit Beschreibung ausgeben',
          'key' => 'reg_unident_descript',
          'radio' => '0',
          'value' => '',
        ),
        26 =>
        array (
          'number' => '27',
          'output' => '0',
          'category' => 'Standorte-Register',
          'label' => 'Basis-Standort anzeigen',
          'key' => 'base_location',
          'radio' => '0',
          'value' => '',
        ),
        27 =>
        array (
          'number' => '28',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Artikel',
          'key' => 'links_articles',
          'radio' => '0',
          'value' => '',
        ),
        28 =>
        array (
          'number' => '29',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Marken',
          'key' => 'links_marks',
          'radio' => '0',
          'value' => '',
        ),
        29 =>
        array (
          'number' => '30',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Grafikobjekte',
          'key' => 'links_maps',
          'radio' => '0',
          'value' => '',
        ),
        30 =>
        array (
          'number' => '31',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Inschriften',
          'key' => 'links_inscriptions',
          'radio' => '0',
          'value' => '',
        ),
        31 =>
        array (
          'number' => '32',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Fußnoten',
          'key' => 'links_footnotes',
          'radio' => '0',
          'value' => '',
        ),
        32 =>
        array (
          'number' => '33',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Literatur',
          'key' => 'links_lit',
          'radio' => '0',
          'value' => '',
        ),
        33 =>
        array (
          'number' => '34',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Wappen',
          'key' => 'links_coa',
          'radio' => '0',
          'value' => '',
        ),
        34 =>
        array (
          'number' => '35',
          'output' => '0',
          'category' => 'Ausgabemodus',
          'label' => 'Münchner Reihe',
          'key' => 'modus',
          'radio' => '1',
          'value' => 'projects_bay',
        ),
        35 =>
        array (
          'number' => '36',
          'output' => '1',
          'category' => 'Ausgabemodus',
          'label' => 'die anderen Reihen',
          'key' => 'modus',
          'radio' => '1',
          'value' => 'projects_all',
        ),
        36 =>
        array (
          'number' => '37',
          'output' => '1',
          'category' => 'Ligaturen',
          'label' => 'unterstreichen',
          'key' => 'ligature_arcs',
          'radio' => '1',
          'value' => '',
        ),
        37 =>
        array (
          'number' => '38',
          'output' => '0',
          'category' => 'Ligaturen',
          'label' => 'Ligaturbögen',
          'key' => 'ligature_arcs',
          'radio' => '1',
          'value' => '1',
        ),
      ),
      'offset' => 30,
      'start' => true,
    ),
    1 =>
    array (
      'number' => '2',
      'type' => 'transformxsl',
      'xslfile' => 'templates4_4/epi44-trans0.xsl',
    ),
    2 =>
    array (
      'number' => '3',
      'type' => 'transformxsl',
      'xslfile' => 'templates4_4/epi44-trans1.xsl',
    ),
    3 =>
    array (
      'number' => '4',
      'type' => 'transformxsl',
      'xslfile' => 'templates4_4/epi44-trans2.xsl',
    ),
    4 =>
    array (
      'number' => '5',
      'type' => 'save',
      'extension' => 'xml',
    ),
  ),
  'pipeline_progress' => 5,
  'project_name' => 'Wismar',
  'filename' => 'project_20-job_27552.xml',
  'filepath' => 'epi_playground/jobs/project_20-job_27552.xml',
),
            ],
            [
                'id' => 2,
                'created' => '2021-07-09 11:34:16',
                'modified' => '2021-07-09 11:34:18',
                'jobtype' => 'export',
                'status' => 'work',
                'progress' => 1,
                'progressmax' => 4,
                'config' => array (
  'database' => 'epi_playground',
  'project_id' => '20',
  'pipeline_id' => '18',
  'articles_ids' => '7953',
  'tasks' =>
  array (
    'data' =>
    array (
      'objects' => false,
      'index' => 0,
      'text' => 0,
    ),
    'options' =>
    array (
      0 =>
      array (
        'number' => '1',
        'output' => 1,
        'category' => 'Allgemeines',
        'label' => 'Signatur anzeigen',
        'key' => 'signature',
        'radio' => '0',
        'value' => '',
      ),
      1 =>
      array (
        'number' => '2',
        'output' => 0,
        'category' => 'Allgemeines',
        'label' => 'Notizen',
        'key' => 'notes',
        'radio' => '0',
        'value' => '',
      ),
      2 =>
      array (
        'number' => '3',
        'output' => 0,
        'category' => 'Allgemeines',
        'label' => 'Letzte Änderung',
        'key' => 'modified',
        'radio' => '0',
        'value' => '',
      ),
      3 =>
      array (
        'number' => '4',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Titelei',
        'key' => 'preliminaries',
        'radio' => '0',
        'value' => '',
      ),
      4 =>
      array (
        'number' => '5',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Vorwort',
        'key' => 'prefaces',
        'radio' => '0',
        'value' => '',
      ),
      5 =>
      array (
        'number' => '6',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Einleitung',
        'key' => 'introduction',
        'radio' => '0',
        'value' => '',
      ),
      6 =>
      array (
        'number' => '7',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Chronologische Liste der Inschriften',
        'key' => 'table_of_inscriptions',
        'radio' => '0',
        'value' => '',
      ),
      7 =>
      array (
        'number' => '8',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Register',
        'key' => 'indices',
        'radio' => '0',
        'value' => '',
      ),
      8 =>
      array (
        'number' => '9',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Abkürzungen',
        'key' => 'abbreviations',
        'radio' => '0',
        'value' => '',
      ),
      9 =>
      array (
        'number' => '10',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Quellen und Literatur',
        'key' => 'biblio',
        'radio' => '0',
        'value' => '',
      ),
      10 =>
      array (
        'number' => '11',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Liste der bisher erschienenen DI-Bände',
        'key' => 'di-volumes',
        'radio' => '0',
        'value' => '',
      ),
      11 =>
      array (
        'number' => '12',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Zeichnungen',
        'key' => 'drawings',
        'radio' => '0',
        'value' => '',
      ),
      12 =>
      array (
        'number' => '13',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Grundrisse',
        'key' => 'maps',
        'radio' => '0',
        'value' => '',
      ),
      13 =>
      array (
        'number' => '14',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Bildtafeln',
        'key' => 'plates',
        'radio' => '0',
        'value' => '',
      ),
      14 =>
      array (
        'number' => '15',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Inhaltsverzeichnis',
        'key' => 'table_of_content',
        'radio' => '0',
        'value' => '',
      ),
      15 =>
      array (
        'number' => '16',
        'output' => 0,
        'category' => 'Band',
        'label' => 'Katalogtitel',
        'key' => 'catalog_title',
        'radio' => '0',
        'value' => '',
      ),
      16 =>
      array (
        'number' => '17',
        'output' => 1,
        'category' => 'Band',
        'label' => 'Katalog der Inschriften',
        'key' => 'articles',
        'radio' => '0',
        'value' => '',
      ),
      17 =>
      array (
        'number' => '18',
        'output' => 0,
        'category' => 'Marken',
        'label' => 'Marken ausgeben',
        'key' => 'marks',
        'radio' => '0',
        'value' => '',
      ),
      18 =>
      array (
        'number' => '19',
        'output' => 0,
        'category' => 'Marken',
        'label' => 'zu einer Kategorie zusammenfassen',
        'key' => 'marks_together',
        'radio' => '0',
        'value' => '',
      ),
      19 =>
      array (
        'number' => '20',
        'output' => 0,
        'category' => 'Sonstiges',
        'label' => 'Verweise auf Abbildungen in den Artikeln',
        'key' => 'image_references',
        'radio' => '0',
        'value' => '',
      ),
      20 =>
      array (
        'number' => '21',
        'output' => 0,
        'category' => 'Sonstiges',
        'label' => 'bei Fußnoten Nummern rechtsbündig',
        'key' => 'footnotes',
        'radio' => '0',
        'value' => '',
      ),
      21 =>
      array (
        'number' => '22',
        'output' => 0,
        'category' => 'Sonstiges',
        'label' => 'Versalien',
        'key' => 'caps',
        'radio' => '0',
        'value' => '',
      ),
      22 =>
      array (
        'number' => '23',
        'output' => 0,
        'category' => 'Sonstiges',
        'label' => 'Sortierung nach Signatur',
        'key' => 'sort_signatures',
        'radio' => '0',
        'value' => '',
      ),
      23 =>
      array (
        'number' => '24',
        'output' => 0,
        'category' => 'Register',
        'label' => 'separates Versregister',
        'key' => 'index_verses',
        'radio' => '0',
        'value' => '',
      ),
      24 =>
      array (
        'number' => '25',
        'output' => 0,
        'category' => 'Register',
        'label' => 'separates Register der Meister und Werkstätten',
        'key' => 'index_producers',
        'radio' => '0',
        'value' => '',
      ),
      25 =>
      array (
        'number' => '26',
        'output' => 0,
        'category' => 'Register',
        'label' => 'nicht identifizierte Wappen mit Beschreibung ausgeben',
        'key' => 'reg_unident_descript',
        'radio' => '0',
        'value' => '',
      ),
      26 =>
      array (
        'number' => '27',
        'output' => 0,
        'category' => 'Standorte-Register',
        'label' => 'Basis-Standort anzeigen',
        'key' => 'base_location',
        'radio' => '0',
        'value' => '',
      ),
      27 =>
      array (
        'number' => '28',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Artikel',
        'key' => 'links_articles',
        'radio' => '0',
        'value' => '',
      ),
      28 =>
      array (
        'number' => '29',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Marken',
        'key' => 'links_marks',
        'radio' => '0',
        'value' => '',
      ),
      29 =>
      array (
        'number' => '30',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Grafikobjekte',
        'key' => 'links_maps',
        'radio' => '0',
        'value' => '',
      ),
      30 =>
      array (
        'number' => '31',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Inschriften',
        'key' => 'links_inscriptions',
        'radio' => '0',
        'value' => '',
      ),
      31 =>
      array (
        'number' => '32',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Fußnoten',
        'key' => 'links_footnotes',
        'radio' => '0',
        'value' => '',
      ),
      32 =>
      array (
        'number' => '33',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Literatur',
        'key' => 'links_lit',
        'radio' => '0',
        'value' => '',
      ),
      33 =>
      array (
        'number' => '34',
        'output' => 0,
        'category' => 'Links erzeugen auf',
        'label' => 'Wappen',
        'key' => 'links_coa',
        'radio' => '0',
        'value' => '',
      ),
      34 =>
      array (
        'number' => '35',
        'output' => 0,
        'category' => 'Ausgabemodus',
        'label' => 'Münchner Reihe',
        'key' => 'modus',
        'radio' => '1',
        'value' => 'projects_bay',
      ),
      35 =>
      array (
        'number' => '36',
        'output' => 1,
        'category' => 'Ausgabemodus',
        'label' => 'die anderen Reihen',
        'key' => 'modus',
        'radio' => '1',
        'value' => 'projects_all',
      ),
      36 =>
      array (
        'number' => '37',
        'output' => 1,
        'category' => 'Ligaturen',
        'label' => 'unterstreichen',
        'key' => 'ligature_arcs',
        'radio' => '1',
        'value' => '',
      ),
      37 =>
      array (
        'number' => '38',
        'output' => 0,
        'category' => 'Ligaturen',
        'label' => 'Ligaturbögen',
        'key' => 'ligature_arcs',
        'radio' => '1',
        'value' => '1',
      ),
    ),
  ),
  'pipeline_name' => 'Entwicklung4.4',
  'pipeline_tasks' =>
  array (
    0 =>
    array (
      'number' => '1',
      'type' => 'export',
      'data' =>
      array (
        'objects' => '1',
        'index' => '1',
        'text' => '1',
      ),
      'options' =>
      array (
        0 =>
        array (
          'number' => '1',
          'output' => '1',
          'category' => 'Allgemeines',
          'label' => 'Signatur anzeigen',
          'key' => 'signature',
          'radio' => '0',
          'value' => '',
        ),
        1 =>
        array (
          'number' => '2',
          'output' => '0',
          'category' => 'Allgemeines',
          'label' => 'Notizen',
          'key' => 'notes',
          'radio' => '0',
          'value' => '',
        ),
        2 =>
        array (
          'number' => '3',
          'output' => '0',
          'category' => 'Allgemeines',
          'label' => 'Letzte Änderung',
          'key' => 'modified',
          'radio' => '0',
          'value' => '',
        ),
        3 =>
        array (
          'number' => '4',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Titelei',
          'key' => 'preliminaries',
          'radio' => '0',
          'value' => '',
        ),
        4 =>
        array (
          'number' => '5',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Vorwort',
          'key' => 'prefaces',
          'radio' => '0',
          'value' => '',
        ),
        5 =>
        array (
          'number' => '6',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Einleitung',
          'key' => 'introduction',
          'radio' => '0',
          'value' => '',
        ),
        6 =>
        array (
          'number' => '7',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Chronologische Liste der Inschriften',
          'key' => 'table_of_inscriptions',
          'radio' => '0',
          'value' => '',
        ),
        7 =>
        array (
          'number' => '8',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Register',
          'key' => 'indices',
          'radio' => '0',
          'value' => '',
        ),
        8 =>
        array (
          'number' => '9',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Abkürzungen',
          'key' => 'abbreviations',
          'radio' => '0',
          'value' => '',
        ),
        9 =>
        array (
          'number' => '10',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Quellen und Literatur',
          'key' => 'biblio',
          'radio' => '0',
          'value' => '',
        ),
        10 =>
        array (
          'number' => '11',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Liste der bisher erschienenen DI-Bände',
          'key' => 'di-volumes',
          'radio' => '0',
          'value' => '',
        ),
        11 =>
        array (
          'number' => '12',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Zeichnungen',
          'key' => 'drawings',
          'radio' => '0',
          'value' => '',
        ),
        12 =>
        array (
          'number' => '13',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Grundrisse',
          'key' => 'maps',
          'radio' => '0',
          'value' => '',
        ),
        13 =>
        array (
          'number' => '14',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Bildtafeln',
          'key' => 'plates',
          'radio' => '0',
          'value' => '',
        ),
        14 =>
        array (
          'number' => '15',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Inhaltsverzeichnis',
          'key' => 'table_of_content',
          'radio' => '0',
          'value' => '',
        ),
        15 =>
        array (
          'number' => '16',
          'output' => '0',
          'category' => 'Band',
          'label' => 'Katalogtitel',
          'key' => 'catalog_title',
          'radio' => '0',
          'value' => '',
        ),
        16 =>
        array (
          'number' => '17',
          'output' => '1',
          'category' => 'Band',
          'label' => 'Katalog der Inschriften',
          'key' => 'articles',
          'radio' => '0',
          'value' => '',
        ),
        17 =>
        array (
          'number' => '18',
          'output' => '0',
          'category' => 'Marken',
          'label' => 'Marken ausgeben',
          'key' => 'marks',
          'radio' => '0',
          'value' => '',
        ),
        18 =>
        array (
          'number' => '19',
          'output' => '0',
          'category' => 'Marken',
          'label' => 'zu einer Kategorie zusammenfassen',
          'key' => 'marks_together',
          'radio' => '0',
          'value' => '',
        ),
        19 =>
        array (
          'number' => '20',
          'output' => '0',
          'category' => 'Sonstiges',
          'label' => 'Verweise auf Abbildungen in den Artikeln',
          'key' => 'image_references',
          'radio' => '0',
          'value' => '',
        ),
        20 =>
        array (
          'number' => '21',
          'output' => '0',
          'category' => 'Sonstiges',
          'label' => 'bei Fußnoten Nummern rechtsbündig',
          'key' => 'footnotes',
          'radio' => '0',
          'value' => '',
        ),
        21 =>
        array (
          'number' => '22',
          'output' => '0',
          'category' => 'Sonstiges',
          'label' => 'Versalien',
          'key' => 'caps',
          'radio' => '0',
          'value' => '',
        ),
        22 =>
        array (
          'number' => '23',
          'output' => '0',
          'category' => 'Sonstiges',
          'label' => 'Sortierung nach Signatur',
          'key' => 'sort_signatures',
          'radio' => '0',
          'value' => '',
        ),
        23 =>
        array (
          'number' => '24',
          'output' => '0',
          'category' => 'Register',
          'label' => 'separates Versregister',
          'key' => 'index_verses',
          'radio' => '0',
          'value' => '',
        ),
        24 =>
        array (
          'number' => '25',
          'output' => '0',
          'category' => 'Register',
          'label' => 'separates Register der Meister und Werkstätten',
          'key' => 'index_producers',
          'radio' => '0',
          'value' => '',
        ),
        25 =>
        array (
          'number' => '26',
          'output' => '0',
          'category' => 'Register',
          'label' => 'nicht identifizierte Wappen mit Beschreibung ausgeben',
          'key' => 'reg_unident_descript',
          'radio' => '0',
          'value' => '',
        ),
        26 =>
        array (
          'number' => '27',
          'output' => '0',
          'category' => 'Standorte-Register',
          'label' => 'Basis-Standort anzeigen',
          'key' => 'base_location',
          'radio' => '0',
          'value' => '',
        ),
        27 =>
        array (
          'number' => '28',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Artikel',
          'key' => 'links_articles',
          'radio' => '0',
          'value' => '',
        ),
        28 =>
        array (
          'number' => '29',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Marken',
          'key' => 'links_marks',
          'radio' => '0',
          'value' => '',
        ),
        29 =>
        array (
          'number' => '30',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Grafikobjekte',
          'key' => 'links_maps',
          'radio' => '0',
          'value' => '',
        ),
        30 =>
        array (
          'number' => '31',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Inschriften',
          'key' => 'links_inscriptions',
          'radio' => '0',
          'value' => '',
        ),
        31 =>
        array (
          'number' => '32',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Fußnoten',
          'key' => 'links_footnotes',
          'radio' => '0',
          'value' => '',
        ),
        32 =>
        array (
          'number' => '33',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Literatur',
          'key' => 'links_lit',
          'radio' => '0',
          'value' => '',
        ),
        33 =>
        array (
          'number' => '34',
          'output' => '0',
          'category' => 'Links erzeugen auf',
          'label' => 'Wappen',
          'key' => 'links_coa',
          'radio' => '0',
          'value' => '',
        ),
        34 =>
        array (
          'number' => '35',
          'output' => '0',
          'category' => 'Ausgabemodus',
          'label' => 'Münchner Reihe',
          'key' => 'modus',
          'radio' => '1',
          'value' => 'projects_bay',
        ),
        35 =>
        array (
          'number' => '36',
          'output' => '1',
          'category' => 'Ausgabemodus',
          'label' => 'die anderen Reihen',
          'key' => 'modus',
          'radio' => '1',
          'value' => 'projects_all',
        ),
        36 =>
        array (
          'number' => '37',
          'output' => '1',
          'category' => 'Ligaturen',
          'label' => 'unterstreichen',
          'key' => 'ligature_arcs',
          'radio' => '1',
          'value' => '',
        ),
        37 =>
        array (
          'number' => '38',
          'output' => '0',
          'category' => 'Ligaturen',
          'label' => 'Ligaturbögen',
          'key' => 'ligature_arcs',
          'radio' => '1',
          'value' => '1',
        ),
      ),
      'offset' => 30,
      'start' => true,
    ),
    1 =>
    array (
      'number' => '2',
      'type' => 'transformxsl',
      'xslfile' => 'templates4_4/epi44-trans0.xsl',
    ),
    2 =>
    array (
      'number' => '3',
      'type' => 'transformxsl',
      'xslfile' => 'templates4_4/epi44-trans1.xsl',
    ),
    3 =>
    array (
      'number' => '4',
      'type' => 'transformxsl',
      'xslfile' => 'templates4_4/epi44-trans2.xsl',
    ),
    4 =>
    array (
      'number' => '5',
      'type' => 'save',
      'extension' => 'xml',
    ),
  ),
  'pipeline_progress' => 1,
  'project_name' => 'Wismar',
  'filename' => 'project_20-job_27553.xml',
  'filepath' => 'epi_playground/jobs/project_20-job_27553.xml',
),
            ],
        ];
        parent::init();
    }
}
