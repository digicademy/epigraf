<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Table;

use App\Utilities\Converters\Arrays;
use App\Utilities\Converters\Attributes;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Query;
use Epi\Model\Entity\FileRecord;
use Files\Model\Behavior\FileSystemBehavior;
use Files\Model\Table\FilesTableTrait;

/**
 * Files table
 *
 * @mixin FileSystemBehavior
 */
class FilesTable extends BaseTable
{

    use FilesTableTrait;

    public $captionField = 'name';

    /**
     * Request parameter config
     *
     * @var string[]
     */
    public $parameters = [
        'id' => 'raw',
        'root' => 'raw',
        'path' => 'raw',
        'filename' => 'raw',
        'basepath' => 'raw',
        'folder_id' => 'raw',
        'list' => 'raw'
    ];

    /**
     * The mounts used in this table
     *
     * @var string[] Array of mount names (e.g. 'root', 'shared')
     */
    public array $mounts = [];

    public string $defaultMount = 'root';

    /**
     * Initialize hook
     *
     * @param array $config
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('files');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->setEntityClass('Epi.FileRecord');

        $this->belongsTo(
            'Items',
            [
                'foreignKey' => ['name'],
                'bindingKey' => ['file_name'],
                'className' => 'Epi.Items',
                'propertyName' => 'item',
                'joinType' => Query::JOIN_TYPE_LEFT,
                'conditions' => ['Files.path = CONCAT("articles/", Items.file_path)']
            ]
        );

        $this->belongsTo(
            'Articles',
            [
                'foreignKey' => 'Items.articles_id',
                'className' => 'Epi.Articles',
                'propertyName' => 'article'
            ]
        );

    }

    /**
     * Clear the caches after saving
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param array $options
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, $options = [])
    {
        parent::afterSave($event, $entity, $options);

        $this->Articles->clearResultCache();
        $this->clearViewCache('epi_views_Epi_Articles');
    }

    /**
     * Checks how many files in the files-table are not available in the items-table
     * @return array|Query
     */
    public function findCompleteness()
    {
        $query = $this->find();

        $files = $query
            ->select([
                'type',
                #'folder' => $query->newExpr("path REGEXP '^(.*[/])'"),
                'n_missing' => $query->expr('SUM(case when Items.file_name IS NULL THEN 1 ELSE 0 END)'),
                'wanted' => $query->expr("case when type NOT IN ('cr2', 'crw', 'mrw', 'nef', 'xmp', 'thm') THEN 1 ELSE 0 END")
            ])
            ->contain(['Items'])
            ->where([
                'isfolder <>' => 1,
                'path LIKE' => 'articles/%',
                'Items.file_name IS' => null
            ])
            ->group(['type', 'file_online'])
            ->order([
                'wanted' => 'DESC',
                'n_missing' => 'DESC'
            ]);

        return $files;
    }

    /**
     * Get list of missing items (see function checkCompleteness)
     * @return Query $query for further processing (to iterate over object to create list)
     */
    public function findIncomplete(Query $query, array $options)
    {

        $conditions = [
            'isfolder <>' => 1,
            'path LIKE' => 'articles/%',
            'Items.file_name IS' => null
        ];


        if (isset($options['type'])) {
            $conditions[] = ['type' => $options['type']];
        }

        if (isset($options['missing'])) {
            if ($options['missing']) {
                $conditions[] = ['Items.id IS' => null];
            }
            else {
                $conditions[] = ['Items.id IS NOT' => null];
            }
        }

        return $query
            ->contain(['Items'])
            ->where($conditions);
    }
}
