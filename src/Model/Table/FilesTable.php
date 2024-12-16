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
use Files\Model\Behavior\FileSystemBehavior;

/**
 * Files table
 *
 * # Behaviors
 * @mixin FileSystemBehavior
 */
class FilesTable extends BaseTable
{

    public $captionField = 'name';

    /**
     * Initialize hook
     *
     * @param array $config
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('files');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->setEntityClass('FileRecord');
        $this->addBehavior('Files.FileSystem');
    }

    /**
     * Returns the schema table object describing this table's properties.
     *
     * @return \Cake\Database\Schema\TableSchemaInterface
     */
    public function getSchema(): TableSchemaInterface
    {
        $schema = parent::getSchema();
        $schema->setColumnType('config', 'json');
        return $schema;
    }
}
