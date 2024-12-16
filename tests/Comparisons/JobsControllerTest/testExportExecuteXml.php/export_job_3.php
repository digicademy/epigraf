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
  'progressmax' => 10,
  'config' => 
  array (
    'pipeline_id' => '16',
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
        4 => 
        array (
          'caption' => 'Bandartikel',
          'enabled' => 1,
        ),
        5 => 
        array (
          'caption' => 'Katalogartikel',
          'enabled' => 1,
        ),
        6 => 
        array (
          'caption' => 'Register',
          'enabled' => 1,
        ),
      ),
      'index' => 1,
      'options' => 
      array (
      ),
    ),
    'user_role' => 'author',
    'user_id' => 1,
    'pipeline_name' => 'Rohdaten',
    'pipeline_tasks' => 
    array (
      0 => 
      array (
        'number' => '1',
        'type' => 'options',
        'outputfile' => '',
      ),
      1 => 
      array (
        'number' => '2',
        'type' => 'data_types',
        'canskip' => '0',
        'caption' => 'Typen',
        'scopes' => 'links',
        'categories' => 'cil-transcription',
        'outputfile' => '',
      ),
      2 => 
      array (
        'number' => '3',
        'type' => 'data_job',
        'canskip' => '0',
        'caption' => 'Job data',
        'outputfile' => '',
      ),
      3 => 
      array (
        'number' => '4',
        'type' => 'data_projects',
        'canskip' => '0',
        'caption' => 'Project data',
        'outputfile' => '',
      ),
      4 => 
      array (
        'number' => '5',
        'type' => 'data_articles',
        'canskip' => '1',
        'caption' => 'Bandartikel',
        'articletypes' => 'epi-book',
        'matchprojects' => '1',
        'outputfile' => '',
      ),
      5 => 
      array (
        'number' => '6',
        'type' => 'data_articles',
        'canskip' => '1',
        'caption' => 'Katalogartikel',
        'articletypes' => '',
        'matchprojects' => '0',
        'outputfile' => '',
      ),
      6 => 
      array (
        'number' => '7',
        'type' => 'data_index',
        'canskip' => '1',
        'caption' => 'Register',
        'outputfile' => '',
      ),
      7 => 
      array (
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
      array (
        'number' => '9',
        'type' => 'save',
        'inputfile' => '',
        'extension' => 'xml',
        'download' => '1',
      ),
    ),
    'pipeline_progress' => 0,
  ),
); 
?>