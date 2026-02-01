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
  'scheduled' => NULL,
  'created_by' => 1,
  'modified_by' => 1,
  'name' => NULL,
  'jobtype' => 'export',
  'norm_iri' => NULL,
  'delay' => 0,
  'schedule' => NULL,
  'nextrun' => NULL,
  'status' => 'init',
  'progress' => 0,
  'progressmax' => 0,
  'config' => 
  array (
    'server' => 'http://localhost/',
    'database' => 'test_projects',
    'table' => 'articles',
    'scope' => NULL,
    'params' => 
    array (
      'projects' => '1',
      'articles' => '1',
      'sort' => 'location',
    ),
    'selection' => 'selected',
    'pipeline_id' => '19',
    'pipeline_name' => 'DI: Artikel',
    'options' => 
    array (
      'enabled' => 
      array (
        3 => 
        array (
          'caption' => 'Index data',
          'enabled' => 1,
        ),
      ),
      'custom' => 
      array (
        'signature' => 1,
        'notes' => 1,
        'modified' => 0,
        'indices' => 0,
        'biblio' => 0,
        'base_location' => 0,
        'ligature_arcs' => '1',
        'marks_together' => 0,
        'modus' => 'projects_bay',
      ),
      'index' => 1,
    ),
    'user_role' => 'author',
    'user_id' => 1,
  ),
  'result' => NULL,
); 
?>