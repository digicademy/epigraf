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
        'articletypes' => [
            'type' => 'text',
            'label' => __('Article types'),
            'value' => $task['articletypes'] ?? $task['articletype']  ?? '',
            'placeholder'=> __('A comma separated list of article types, e.g. "object" or "text"')
        ],
        'matchprojects' => [
            'type' => 'checkbox',
            'label' => __('All articles in selected projects'),
            'checked' => $task['matchprojects'] ?? false
        ],

        'copyimages' => [
            'type' => 'checkbox',
            'label' => __('Copy images '),
            'help' => __('Add metadata to the image files and copy them to the target folder'),
            'checked' => $task['copyimages'] ?? false
        ],
        'imagetypes' => [
            'type' => 'text',
            'label' => __('Image item types'),
            'value' => $task['imagetypes'] ?? '',
            'placeholder'=> __('A comma separated list of item types, e.g. "images"')
        ],
        'metadata' => [
            'type' => 'textarea',
            'escape' => true,
            'label' => __('Image metadata configuration'),
            'placeholder' => __(
                'Add the JSON configuration for metadata extraction. '
                . 'Each key is a metadata field in the file, each value is a placeholder extraction key from the perspective of an image item. '
                . 'The configuration will be merged with the types configuration for the image item. '
                . 'Example: {"licence":"{file_meta.licence}","{copyright":"file_copyright}"}'
            ),
            'value' => $task['metadata'] ?? ''
        ],
        'imagefolder' => [
            'type' => 'text',
            'label' => __('Image folder in the current job folder'),
            'placeholder' => __('For example, "images"'),
            'value' => $task['imagefolder'] ?? ''
        ],
        'format' => [
            'type' => 'select',
            'label' => __('Output format'),
            'options' => [
                'xml' => __('XML'),
                'json' => __('JSON'),
                'csv' => __('CSV'),
                'md' => __('Markdown'),
                'ttl' => __('Turtle'),
                'rdf' => __('RDF/XML'),
                'jsonld' => __('JsonLd'),
                'geojson' => __('GeoJSON')
            ],
            'value' => $task['format'] ?? 'xml'
        ],
        'snippets' => [
            'type' => 'text',
            'label' => __('Data snippets'),
            'value' => $task['snippets'] ?? '',
            'placeholder'=> __('A comma separated list of snippets to retrieve, e.g. "published,comments"')
        ],
        'iris' => [
            'type' => 'checkbox',
            'label' => __('Output IRIs instead of IDs'),
            'checked' => $task['iris'] ?? false
        ],
        'wrap' => [
            'type' => 'checkbox',
            'label' => __('Wrap content (add prolog and epilog)'),
            'help' => __('Add prolog and epilog to the output, e.g. the XML declaration and XML root tags.'),
            'checked' => $task['wrap'] ?? false
        ],
    ], $options)
?>
