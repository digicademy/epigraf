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

/**
 * Wiki table
 *
 * The wiki table is a segment of the docs table.
 *
 * @method \App\Model\Entity\Doc get($primaryKey, $options = [])
 * @method \App\Model\Entity\Doc newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Doc[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Doc|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Doc patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Doc[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Doc findOrCreate($search, callable $callback = null)
 */
class WikiTable extends DocsTable
{

    //TODO: can we use DocsTable without WikiTable?

    /**
     * Model table configuration
     *
     * @var array
     */
    public $config = [
        'segment' => 'wiki',
        'table' => 'docs'
    ];

}
