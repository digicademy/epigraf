<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */
?>
<?php
/**
 * @var array $task
 * @var array $options
 */
?>
<?php $entityHelper =($options['edit'] ?? false) ? $this->EntityInput : $this->EntityHtml; ?>
<?=
    $entityHelper->taskTable($task, [

        'format' => [
            'type' => 'select',
            'label' => __('Output format'),
            'options' => [
                'xml' => __('XML'),
                'json' => __('JSON'),
                'csv' => __('CSV'),
                'md' => __('Markdown'),
                'plain' => __('HTML'),
                'ttl' => __('Turtle'),
                'rdf' => __('RDF/XML'),
                'jsonld' => __('JsonLd'),
                'geojson' => __('GeoJSON')
            ],
            'value' => $task['format'] ?? 'xml'
        ],

        'iris' => [
            'type' => 'checkbox',
            'label' => __('Output IRIs instead of IDs'),
            'checked' => $task['iris'] ?? false
        ]
    ], $options)
?>
