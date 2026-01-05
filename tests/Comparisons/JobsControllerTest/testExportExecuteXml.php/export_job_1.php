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
  'jobtype' => 'export',
  'status' => 'init',
  'progress' => 0,
  'progressmax' => 0,
  'config' => 
  array (
    'pipeline_id' => '16',
    'database' => 'projects',
    'server' => 'http://localhost/',
    'model' => 'articles',
    'table' => 'articles',
    'params' => 
    array (
      'articles' => '1',
      'projects' => '1',
    ),
    'selection' => 'selected',
    'options' => 
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
      'options' => 
      array (
      ),
      'index' => 1,
    ),
    'pipeline_name' => 'Rohdaten',
    'user_role' => 'author',
    'user_id' => 1,
  ),
  'result' => NULL,
); 
?>