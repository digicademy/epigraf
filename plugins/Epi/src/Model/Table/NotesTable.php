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

use App\Model\Table\DocsTable;

/**
 * Notes table
 */
class NotesTable extends DocsTable
{
    /**
     * Database connection
     *
     * @var null
     */
    public static $defaultConnection = 'projects';

    /**
     * Type field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $typeField = 'notetype';

    /**
     * The field containing a default caption
     *
     * @var string
     */
    public $captionField = 'name';

    /**
     * Scope field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $scopeField = null;

    /**
     * Current scope
     *
     * @var null
     */
    public $scopeValue = null;

    /**
     * Model table configuration
     *
     * @var array
     */
    public $config = [
        'table' => 'notes',
        'norm_iri' => true
    ];

    /**
     * Initialize notes table
     *
     * @param array $config
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setEntityClass('Epi.Note');
    }

    /**
     * Get pagination parameters
     *
     * @param array $params Parsed request parameters
     * @param array $columns
     * @return array
     */
    public function getPaginationParams(array $params = [], array $columns = [])
    {
        $pagination = parent::getPaginationParams($params, $columns);

        return [
                'order' => ['Notes.name' => 'asc'],
                'limit' => 100
            ] + $pagination;
    }

}
