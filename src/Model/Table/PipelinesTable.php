<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Table;

use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\FactoryLocator;
use Cake\ORM\Query;
use Cake\Validation\Validator;

/**
 * Pipelines table
 *
 */
class PipelinesTable extends BaseTable
{

    /**
     * The field containing a default caption
     *
     * @var string
     */
    public $captionField = 'name';

    /**
     * Request parameter config
     *
     * @var string[]
     */
    public $parameters = [
        'id' => 'list',
        'published' => 'list-integer',
        'name' => 'string',
        'term' => 'string',
        'selected' => 'list',
        'load' => 'list',
        'save' => 'list'
    ];


    /**
     * Initialize hook
     *
     * @param array $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('pipelines');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
    }

    /**
     * Returns the schema table object describing this table's properties.
     *
     * @return \Cake\Database\Schema\TableSchemaInterface
     */
    public function getSchema(): TableSchemaInterface
    {
        $schema = parent::getSchema();
        $schema->setColumnType('tasks', 'json');
        return $schema;
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance
     * @return Validator
     */
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->allowEmptyString('name');

        return $validator;
    }

    /**
     * Get columns to be rendered in table views
     *
     * @param array $selected The selected columns
     * @param array $default The default columns
     * @param string|null $type Filter by type
     *
     * @return array
     */
    public function getColumns($selected = [], $default = [], $type = null)
    {

        $default = [
            'id' => [
                'caption' => __('ID'),
                'sort' => true,
                'default' => $this::$requestFormat !== 'html',
                'width' => 50
            ],

            'name' => [
                'caption' => __('Name'),
                'sort' => true,
                'default' => true,
                'filter' => 'text',
                'width' => 150
            ],
            'description' => [
                'caption' => __('Description'),
                'sort' => true,
                'width' => 200,
                'default' => true
            ],

            'norm_iri' => [
                'caption' => __('IRI fragment'),
                'sort' => 'norm_iri',
                'default' => true,
                'width' => 50
            ],

            'created' => [
                'caption' => __('Created'),
                'sort' => true,
                'default' => $this::$requestFormat !== 'html',
                'width' => 50
            ],

            'modified' => [
                'caption' => __('Modified'),
                'sort' => true,
                'default' => $this::$requestFormat !== 'html',
                'width' => 50
            ]

        ];

        return parent::getColumns($selected, $default, $type);
    }

    /**
     * Find pipelines for specific article types
     *
     * The query returns a list of pipeline names indexed by their IDs
     *
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findForArticles(Query $query, array $options)
    {
        $query = $query->find('list');

        if (!empty($options['articles'])) {
            $articlesTable = FactoryLocator::get('Table')->get('Epi.Articles');
            $exportOptions = $articlesTable->getExportOptions($options);

            if (!empty($exportOptions['pipelines'])) {
                $query->where(['Pipelines.norm_iri IN' => $exportOptions['pipelines']]);
            }
        }

        return $query;
    }
}
