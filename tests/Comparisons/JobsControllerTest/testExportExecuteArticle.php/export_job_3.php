<?php 
 return array (
  'id' => 3,
  'created' => 
  \Cake\I18n\FrozenTime::__set_state(array(
     'date' => '2020-10-29 13:00:58.000000',
     'timezone_type' => 3,
     'timezone' => 'Europe/Berlin',
  )),
  'modified' => 
  \Cake\I18n\FrozenTime::__set_state(array(
     'date' => '2020-10-29 13:00:58.000000',
     'timezone_type' => 3,
     'timezone' => 'Europe/Berlin',
  )),
  'created_by' => 1,
  'modified_by' => 1,
  'typ' => 'export',
  'status' => 'work',
  'progress' => 1,
  'progressmax' => 12,
  'config' => 
  array (
    'pipeline_id' => '19',
    'database' => 'projects',
    'model' => 'articles',
    'table' => 'articles',
    'params' => 
    array (
      'articles' => '1',
      'projects' => '1',
    ),
    'selection' => 'selected',
    'tasks' => 
    array (
      'enabled' => 
      array (
        3 => 
        array (
          'caption' => 'Index data',
          'enabled' => 1,
        ),
      ),
      'index' => 1,
      'options' => 
      array (
        2 => 
        array (
          'number' => '2',
          'output' => 1,
          'category' => 'Allgemeines',
          'label' => 'Signatur anzeigen',
          'key' => 'signature',
          'radio' => '0',
          'value' => '',
          'type' => 'check',
        ),
        3 => 
        array (
          'number' => '3',
          'output' => 1,
          'category' => 'Allgemeines',
          'label' => 'Notizen',
          'key' => 'notes',
          'radio' => '0',
          'value' => '',
          'type' => 'check',
        ),
        4 => 
        array (
          'number' => '4',
          'output' => 0,
          'category' => 'Allgemeines',
          'label' => 'Letzte Änderung',
          'key' => 'modified',
          'radio' => '0',
          'value' => '',
          'type' => 'check',
        ),
        5 => 
        array (
          'number' => '5',
          'output' => 0,
          'category' => 'Register',
          'label' => 'Register',
          'key' => 'indices',
          'radio' => '0',
          'value' => '',
          'type' => 'check',
        ),
        6 => 
        array (
          'number' => '6',
          'output' => 0,
          'category' => 'Register',
          'label' => 'Literatur und Quellen',
          'key' => 'biblio',
          'radio' => '0',
          'value' => '',
          'type' => 'check',
        ),
        7 => 
        array (
          'number' => '7',
          'output' => 0,
          'category' => 'Standorte-Register',
          'label' => 'Basis-Standort anzeigen',
          'key' => 'base_location',
          'radio' => '0',
          'value' => '',
          'type' => 'check',
        ),
        8 => 
        array (
          'number' => '8',
          'output' => 0,
          'category' => 'Ligaturen',
          'label' => 'unterstreichen',
          'key' => 'ligature_arcs',
          'radio' => '1',
          'value' => '',
          'type' => 'radio',
        ),
        9 => 
        array (
          'number' => '9',
          'output' => 1,
          'category' => 'Ligaturen',
          'label' => 'Ligaturbögen',
          'key' => 'ligature_arcs',
          'radio' => '1',
          'value' => '1',
          'type' => 'radio',
        ),
        10 => 
        array (
          'number' => '10',
          'output' => 0,
          'category' => 'Marken',
          'label' => 'zu einer Kategorie zusammenfassen',
          'key' => 'marks_together',
          'radio' => '0',
          'value' => '',
          'type' => 'check',
        ),
        11 => 
        array (
          'number' => '11',
          'output' => 1,
          'category' => 'Ausgabemodus',
          'label' => 'Münchener Reihe',
          'key' => 'modus',
          'radio' => '1',
          'value' => 'projects_bay',
          'type' => 'radio',
        ),
        12 => 
        array (
          'number' => '12',
          'output' => 0,
          'category' => 'Ausgabemodus',
          'label' => 'alle anderen Reihen',
          'key' => 'modus',
          'radio' => '1',
          'value' => 'projects_all',
          'type' => 'radio',
        ),
      ),
    ),
    'user_role' => 'author',
    'user_id' => 1,
    'pipeline_name' => 'DI: Artikel',
    'pipeline_tasks' => 
    array (
      0 => 
      array (
        'number' => '1',
        'type' => 'options',
        'options' => 
        array (
          2 => 
          array (
            'number' => '2',
            'output' => '1',
            'category' => 'Allgemeines',
            'label' => 'Signatur anzeigen',
            'key' => 'signature',
            'radio' => '0',
            'value' => '',
          ),
          3 => 
          array (
            'number' => '3',
            'output' => '1',
            'category' => 'Allgemeines',
            'label' => 'Notizen',
            'key' => 'notes',
            'radio' => '0',
            'value' => '',
          ),
          4 => 
          array (
            'number' => '4',
            'output' => '0',
            'category' => 'Allgemeines',
            'label' => 'Letzte Änderung',
            'key' => 'modified',
            'radio' => '0',
            'value' => '',
          ),
          5 => 
          array (
            'number' => '5',
            'output' => '0',
            'category' => 'Register',
            'label' => 'Register',
            'key' => 'indices',
            'radio' => '0',
            'value' => '',
          ),
          6 => 
          array (
            'number' => '6',
            'output' => '0',
            'category' => 'Register',
            'label' => 'Literatur und Quellen',
            'key' => 'biblio',
            'radio' => '0',
            'value' => '',
          ),
          7 => 
          array (
            'number' => '7',
            'output' => '0',
            'category' => 'Standorte-Register',
            'label' => 'Basis-Standort anzeigen',
            'key' => 'base_location',
            'radio' => '0',
            'value' => '',
          ),
          8 => 
          array (
            'number' => '8',
            'output' => '1',
            'category' => 'Ligaturen',
            'label' => 'unterstreichen',
            'key' => 'ligature_arcs',
            'radio' => '1',
            'value' => '',
          ),
          9 => 
          array (
            'number' => '9',
            'output' => '0',
            'category' => 'Ligaturen',
            'label' => 'Ligaturbögen',
            'key' => 'ligature_arcs',
            'radio' => '1',
            'value' => '1',
          ),
          10 => 
          array (
            'number' => '10',
            'output' => '0',
            'category' => 'Marken',
            'label' => 'zu einer Kategorie zusammenfassen',
            'key' => 'marks_together',
            'radio' => '0',
            'value' => '',
          ),
          11 => 
          array (
            'number' => '11',
            'output' => '0',
            'category' => 'Ausgabemodus',
            'label' => 'Münchener Reihe',
            'key' => 'modus',
            'radio' => '1',
            'value' => 'projects_bay',
          ),
          12 => 
          array (
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
      array (
        'number' => '2',
        'type' => 'data_projects',
        'canskip' => '0',
        'caption' => 'Project data',
        'outputfile' => '',
      ),
      2 => 
      array (
        'number' => '3',
        'type' => 'data_articles',
        'canskip' => '0',
        'caption' => 'Article data',
        'articletypes' => '',
        'matchprojects' => '0',
        'outputfile' => '',
      ),
      3 => 
      array (
        'number' => '4',
        'type' => 'data_index',
        'canskip' => '1',
        'caption' => 'Index data',
        'outputfile' => '',
      ),
      4 => 
      array (
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
      array (
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
      array (
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
      array (
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
      array (
        'number' => '9',
        'type' => 'replace',
        'canskip' => '0',
        'caption' => 'Search and replace',
        'inputfile' => '',
        'replacefile' => 'pipelines/templates/epi-replace-whitespace.txt',
        'outputfile' => '',
      ),
      9 => 
      array (
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
      array (
        'number' => '11',
        'type' => 'save',
        'inputfile' => '',
        'extension' => 'doc',
        'download' => '1',
      ),
    ),
    'pipeline_progress' => 0,
  ),
); 
?>