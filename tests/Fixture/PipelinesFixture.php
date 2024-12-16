<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PipelinesFixture
 *
 *  bin/cake bake fixture Pipelines --records --conditions "id IN (16,19,21)" --count 10
 *
 */
class PipelinesFixture extends TestFixture
{

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 16,
                'deleted' => 0,
                'published' => null,
                'version_id' => null,
                'created' => '2019-11-29 09:11:26',
                'modified' => '2024-02-11 18:17:37',
                'created_by' => null,
                'modified_by' => 132,
                'name' => 'Rohdaten',
                'norm_iri' => 'raw',
                'description' => 'Ausgabe der Daten im XML-Format ohne zusätzliche Transformation',
                'tasks' => array(
                    0 =>
                        array(
                            'number' => '1',
                            'type' => 'options',
                            'outputfile' => '',
                        ),
                    1 =>
                        array(
                            'number' => '2',
                            'type' => 'data_types',
                            'canskip' => '0',
                            'caption' => 'Typen',
                            'scopes' => 'links',
                            'categories' => 'cil-transcription',
                            'outputfile' => '',
                        ),
                    2 =>
                        array(
                            'number' => '3',
                            'type' => 'data_job',
                            'canskip' => '0',
                            'caption' => 'Job data',
                            'outputfile' => '',
                        ),
                    3 =>
                        array(
                            'number' => '4',
                            'type' => 'data_projects',
                            'canskip' => '0',
                            'caption' => 'Project data',
                            'outputfile' => '',
                        ),
                    4 =>
                        array(
                            'number' => '5',
                            'type' => 'data_articles',
                            'canskip' => '1',
                            'caption' => 'Bandartikel',
                            'articletypes' => 'epi-book',
                            'matchprojects' => '1',
                            'outputfile' => '',
                        ),
                    5 =>
                        array(
                            'number' => '6',
                            'type' => 'data_articles',
                            'canskip' => '1',
                            'caption' => 'Katalogartikel',
                            'articletypes' => '',
                            'matchprojects' => '0',
                            'outputfile' => '',
                        ),
                    6 =>
                        array(
                            'number' => '7',
                            'type' => 'data_index',
                            'canskip' => '1',
                            'caption' => 'Register',
                            'outputfile' => '',
                        ),
                    7 =>
                        array(
                            'number' => '8',
                            'type' => 'bundle',
                            'canskip' => '0',
                            'caption' => 'Bundle files',
                            'source' => '',
                            'prefix' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<book>',
                            'postfix' => '
</book>',
                            'outputfile' => '',
                        ),
                    8 =>
                        array(
                            'number' => '9',
                            'type' => 'save',
                            'inputfile' => '',
                            'extension' => 'xml',
                            'download' => '1',
                        ),
                ),
            ],
            [
                'id' => 19,
                'deleted' => 0,
                'published' => null,
                'version_id' => null,
                'created' => '2021-07-09 07:14:23',
                'modified' => '2024-02-11 16:42:37',
                'created_by' => null,
                'modified_by' => 132,
                'name' => 'DI: Artikel',
                'norm_iri' => 'di-articles-doc',
                'description' => 'Ausgabe des Inschriftenkatalogs, wahlweise mit Registern',
                'tasks' => array(
                    0 =>
                        array(
                            'number' => '1',
                            'type' => 'options',
                            'options' =>
                                array(
                                    2 =>
                                        array(
                                            'number' => '2',
                                            'output' => '1',
                                            'category' => 'Allgemeines',
                                            'label' => 'Signatur anzeigen',
                                            'key' => 'signature',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    3 =>
                                        array(
                                            'number' => '3',
                                            'output' => '1',
                                            'category' => 'Allgemeines',
                                            'label' => 'Notizen',
                                            'key' => 'notes',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    4 =>
                                        array(
                                            'number' => '4',
                                            'output' => '0',
                                            'category' => 'Allgemeines',
                                            'label' => 'Letzte Änderung',
                                            'key' => 'modified',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    5 =>
                                        array(
                                            'number' => '5',
                                            'output' => '0',
                                            'category' => 'Register',
                                            'label' => 'Register',
                                            'key' => 'indices',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    6 =>
                                        array(
                                            'number' => '6',
                                            'output' => '0',
                                            'category' => 'Register',
                                            'label' => 'Literatur und Quellen',
                                            'key' => 'biblio',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    7 =>
                                        array(
                                            'number' => '7',
                                            'output' => '0',
                                            'category' => 'Standorte-Register',
                                            'label' => 'Basis-Standort anzeigen',
                                            'key' => 'base_location',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    8 =>
                                        array(
                                            'number' => '8',
                                            'output' => '1',
                                            'category' => 'Ligaturen',
                                            'label' => 'unterstreichen',
                                            'key' => 'ligature_arcs',
                                            'radio' => '1',
                                            'value' => '',
                                        ),
                                    9 =>
                                        array(
                                            'number' => '9',
                                            'output' => '0',
                                            'category' => 'Ligaturen',
                                            'label' => 'Ligaturbögen',
                                            'key' => 'ligature_arcs',
                                            'radio' => '1',
                                            'value' => '1',
                                        ),
                                    10 =>
                                        array(
                                            'number' => '10',
                                            'output' => '0',
                                            'category' => 'Marken',
                                            'label' => 'zu einer Kategorie zusammenfassen',
                                            'key' => 'marks_together',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    11 =>
                                        array(
                                            'number' => '11',
                                            'output' => '0',
                                            'category' => 'Ausgabemodus',
                                            'label' => 'Münchener Reihe',
                                            'key' => 'modus',
                                            'radio' => '1',
                                            'value' => 'projects_bay',
                                        ),
                                    12 =>
                                        array(
                                            'number' => '12',
                                            'output' => '1',
                                            'category' => 'Ausgabemodus',
                                            'label' => 'alle anderen Reihen',
                                            'key' => 'modus',
                                            'radio' => '1',
                                            'value' => 'projects_all',
                                        ),
                                ),
                            'outputfile' => '',
                        ),
                    1 =>
                        array(
                            'number' => '2',
                            'type' => 'data_projects',
                            'canskip' => '0',
                            'caption' => 'Project data',
                            'outputfile' => '',
                        ),
                    2 =>
                        array(
                            'number' => '3',
                            'type' => 'data_articles',
                            'canskip' => '0',
                            'caption' => 'Article data',
                            'articletypes' => '',
                            'matchprojects' => '0',
                            'outputfile' => '',
                        ),
                    3 =>
                        array(
                            'number' => '4',
                            'type' => 'data_index',
                            'canskip' => '1',
                            'caption' => 'Index data',
                            'outputfile' => '',
                        ),
                    4 =>
                        array(
                            'number' => '5',
                            'type' => 'bundle',
                            'canskip' => '0',
                            'caption' => 'Bundle files',
                            'source' => '',
                            'prefix' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<book>',
                            'postfix' => '
</book>',
                            'outputfile' => '',
                        ),
                    5 =>
                        array(
                            'number' => '6',
                            'type' => 'transformxsl',
                            'canskip' => '0',
                            'caption' => 'Transform with XSL',
                            'inputfile' => '',
                            'xslfile' => 'pipelines/templates/epi-trans0.xsl',
                            'processor' => 'saxon',
                            'outputfile' => '',
                        ),
                    6 =>
                        array(
                            'number' => '7',
                            'type' => 'transformxsl',
                            'canskip' => '0',
                            'caption' => 'Transform with XSL',
                            'inputfile' => '',
                            'xslfile' => 'pipelines/templates/epi-trans1.xsl',
                            'processor' => 'saxon',
                            'outputfile' => '',
                        ),
                    7 =>
                        array(
                            'number' => '8',
                            'type' => 'transformxsl',
                            'canskip' => '0',
                            'caption' => 'Transform with XSL',
                            'inputfile' => '',
                            'xslfile' => 'pipelines/templates/epi-trans2.xsl',
                            'processor' => 'saxon',
                            'outputfile' => '',
                        ),
                    8 =>
                        array(
                            'number' => '9',
                            'type' => 'replace',
                            'canskip' => '0',
                            'caption' => 'Search and replace',
                            'inputfile' => '',
                            'replacefile' => 'pipelines/templates/epi-replace-whitespace.txt',
                            'outputfile' => '',
                        ),
                    9 =>
                        array(
                            'number' => '10',
                            'type' => 'transformxsl',
                            'canskip' => '0',
                            'caption' => 'Transform with XSL',
                            'inputfile' => '',
                            'xslfile' => 'pipelines/templates/epi-trans-word1.xsl',
                            'processor' => 'saxon',
                            'outputfile' => '',
                        ),
                    10 =>
                        array(
                            'number' => '11',
                            'type' => 'save',
                            'inputfile' => '',
                            'extension' => 'doc',
                            'download' => '1',
                        ),
                ),
            ],
            [
                'id' => 21,
                'deleted' => 0,
                'published' => null,
                'version_id' => null,
                'created' => '2021-12-18 11:51:34',
                'modified' => '2024-02-11 18:56:12',
                'created_by' => null,
                'modified_by' => 132,
                'name' => 'DI: Band',
                'norm_iri' => 'di-book-doc',
                'description' => 'Ausgabe eines Inschriftenbandes in Word',
                'tasks' => array(
                    0 =>
                        array(
                            'number' => '1',
                            'type' => 'options',
                            'options' =>
                                array(
                                    5 =>
                                        array(
                                            'number' => '5',
                                            'output' => '1',
                                            'category' => 'Allgemeines',
                                            'label' => 'Signatur',
                                            'key' => 'signature',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    6 =>
                                        array(
                                            'number' => '6',
                                            'output' => '0',
                                            'category' => 'Allgemeines',
                                            'label' => 'Notizen',
                                            'key' => 'notes',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    7 =>
                                        array(
                                            'number' => '7',
                                            'output' => '0',
                                            'category' => 'Allgemeines',
                                            'label' => 'Letzte Änderung',
                                            'key' => 'modified',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    8 =>
                                        array(
                                            'number' => '8',
                                            'output' => '0',
                                            'category' => 'Band',
                                            'label' => 'Titelei',
                                            'key' => 'preliminaries',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    9 =>
                                        array(
                                            'number' => '9',
                                            'output' => '0',
                                            'category' => 'Band',
                                            'label' => 'Inhaltsverzeichnis',
                                            'key' => 'table_of_content',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    10 =>
                                        array(
                                            'number' => '10',
                                            'output' => '0',
                                            'category' => 'Band',
                                            'label' => 'Vorwort',
                                            'key' => 'prefaces',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    11 =>
                                        array(
                                            'number' => '11',
                                            'output' => '0',
                                            'category' => 'Band',
                                            'label' => 'Einleitung',
                                            'key' => 'introduction',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    12 =>
                                        array(
                                            'number' => '12',
                                            'output' => '1',
                                            'category' => 'Band',
                                            'label' => 'Katalog der Inschriften',
                                            'key' => 'articles',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    13 =>
                                        array(
                                            'number' => '13',
                                            'output' => '0',
                                            'category' => 'Band',
                                            'label' => 'Chronologische Liste der Inschriften',
                                            'key' => 'table_of_inscriptions',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    14 =>
                                        array(
                                            'number' => '14',
                                            'output' => '0',
                                            'category' => 'Band',
                                            'label' => 'Register',
                                            'key' => 'indices',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    15 =>
                                        array(
                                            'number' => '15',
                                            'output' => '0',
                                            'category' => 'Band',
                                            'label' => 'Abkürzungen',
                                            'key' => 'abbreviations',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    16 =>
                                        array(
                                            'number' => '16',
                                            'output' => '0',
                                            'category' => 'Band',
                                            'label' => 'Quellen und Literatur',
                                            'key' => 'biblio',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    17 =>
                                        array(
                                            'number' => '17',
                                            'output' => '0',
                                            'category' => 'Band',
                                            'label' => 'Liste der bisher erschienenen DI-Bände',
                                            'key' => 'di-volumes',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    18 =>
                                        array(
                                            'number' => '18',
                                            'output' => '0',
                                            'category' => 'Band',
                                            'label' => 'Zeichnungen',
                                            'key' => 'drawings',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    19 =>
                                        array(
                                            'number' => '19',
                                            'output' => '0',
                                            'category' => 'Band',
                                            'label' => 'Marken',
                                            'key' => 'marks',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    20 =>
                                        array(
                                            'number' => '20',
                                            'output' => '0',
                                            'category' => 'Band',
                                            'label' => 'Bildtafeln',
                                            'key' => 'plates',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    21 =>
                                        array(
                                            'number' => '21',
                                            'output' => '0',
                                            'category' => 'Standorte-Register',
                                            'label' => 'Basis-Standort anzeigen',
                                            'key' => 'base_location',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    22 =>
                                        array(
                                            'number' => '22',
                                            'output' => '0',
                                            'category' => 'Fußnoten',
                                            'label' => 'Nummern rechtsbündig',
                                            'key' => 'footnotes',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    23 =>
                                        array(
                                            'number' => '23',
                                            'output' => '1',
                                            'category' => 'Ligaturen',
                                            'label' => 'unterstreichen',
                                            'key' => 'ligature_arcs',
                                            'radio' => '1',
                                            'value' => '',
                                        ),
                                    24 =>
                                        array(
                                            'number' => '24',
                                            'output' => '0',
                                            'category' => 'Ligaturen',
                                            'label' => 'Ligaturbögen',
                                            'key' => 'ligature_arcs',
                                            'radio' => '1',
                                            'value' => '1',
                                        ),
                                    25 =>
                                        array(
                                            'number' => '25',
                                            'output' => '0',
                                            'category' => 'Marken',
                                            'label' => 'zu einer Kategorie zusammenfassen ',
                                            'key' => 'marks_together ',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    26 =>
                                        array(
                                            'number' => '26',
                                            'output' => '0',
                                            'category' => 'Sonstiges',
                                            'label' => 'Sortierung nach Signatur',
                                            'key' => 'sort_signatures',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                    27 =>
                                        array(
                                            'number' => '27',
                                            'output' => '0',
                                            'category' => 'Quellen und Literatur',
                                            'label' => 'DI-Bände ausblenden',
                                            'key' => 'di_volumes_hide',
                                            'radio' => '0',
                                            'value' => '',
                                        ),
                                ),
                            'outputfile' => '',
                        ),
                    1 =>
                        array(
                            'number' => '2',
                            'type' => 'data_projects',
                            'canskip' => '0',
                            'caption' => 'Project data',
                            'outputfile' => '',
                        ),
                    2 =>
                        array(
                            'number' => '3',
                            'type' => 'data_articles',
                            'canskip' => '0',
                            'caption' => 'Article data',
                            'articletypes' => 'epi-book',
                            'matchprojects' => '1',
                            'outputfile' => '',
                        ),
                    3 =>
                        array(
                            'number' => '4',
                            'type' => 'data_articles',
                            'canskip' => '0',
                            'caption' => 'Article data',
                            'articletypes' => 'epi-article',
                            'matchprojects' => '0',
                            'outputfile' => '',
                        ),
                    4 =>
                        array(
                            'number' => '5',
                            'type' => 'data_index',
                            'canskip' => '0',
                            'caption' => 'Index data',
                            'outputfile' => '',
                        ),
                    5 =>
                        array(
                            'number' => '6',
                            'type' => 'bundle',
                            'canskip' => '0',
                            'caption' => 'Bundle files',
                            'source' => '',
                            'prefix' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<book>',
                            'postfix' => '
</book>',
                            'outputfile' => '',
                        ),
                    6 =>
                        array(
                            'number' => '7',
                            'type' => 'transformxsl',
                            'canskip' => '0',
                            'caption' => 'Transform with XSL',
                            'inputfile' => '',
                            'xslfile' => 'pipelines/templates/epi-trans0.xsl',
                            'processor' => 'saxon',
                            'outputfile' => '',
                        ),
                    7 =>
                        array(
                            'number' => '8',
                            'type' => 'transformxsl',
                            'canskip' => '0',
                            'caption' => 'Transform with XSL',
                            'inputfile' => '',
                            'xslfile' => 'pipelines/templates/epi-trans1.xsl',
                            'processor' => 'saxon',
                            'outputfile' => '',
                        ),
                    8 =>
                        array(
                            'number' => '9',
                            'type' => 'transformxsl',
                            'canskip' => '0',
                            'caption' => 'Transform with XSL',
                            'inputfile' => '',
                            'xslfile' => 'pipelines/templates/epi-trans2.xsl',
                            'processor' => 'saxon',
                            'outputfile' => '',
                        ),
                    9 =>
                        array(
                            'number' => '10',
                            'type' => 'replace',
                            'canskip' => '0',
                            'caption' => 'Search and replace',
                            'inputfile' => '',
                            'replacefile' => 'pipelines/templates/epi-replace-whitespace.txt',
                            'outputfile' => '',
                        ),
                    10 =>
                        array(
                            'number' => '11',
                            'type' => 'transformxsl',
                            'canskip' => '0',
                            'caption' => 'Transform with XSL',
                            'inputfile' => '',
                            'xslfile' => 'pipelines/templates/epi-trans-word1.xsl',
                            'processor' => 'saxon',
                            'outputfile' => '',
                        ),
                    11 =>
                        array(
                            'number' => '12',
                            'type' => 'replace',
                            'canskip' => '0',
                            'caption' => 'Search and replace',
                            'inputfile' => '',
                            'replacefile' => 'pipelines/templates/epi-silbentrennung.txt',
                            'outputfile' => '',
                        ),
                    12 =>
                        array(
                            'number' => '13',
                            'type' => 'transformxsl',
                            'canskip' => '0',
                            'caption' => 'Transform with XSL',
                            'inputfile' => '',
                            'xslfile' => 'pipelines/templates/epi-trans-word2.xsl',
                            'processor' => 'php',
                            'outputfile' => '',
                        ),
                    13 =>
                        array(
                            'number' => '14',
                            'type' => 'replace',
                            'canskip' => '0',
                            'caption' => 'Search and replace',
                            'inputfile' => '',
                            'replacefile' => 'pipelines/templates/epi-contract-numbers.txt',
                            'outputfile' => '',
                        ),
                    14 =>
                        array(
                            'number' => '15',
                            'type' => 'save',
                            'inputfile' => '',
                            'extension' => 'doc',
                            'download' => '1',
                        ),
                ),
            ],
        ];
        parent::init();
    }
}
