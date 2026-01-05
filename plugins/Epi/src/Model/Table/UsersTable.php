<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */
declare(strict_types=1);

namespace Epi\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Users table
 */
class UsersTable extends BaseTable
{

    /**
     * Type field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $typeField = 'usertype';

    /**
     * The field containing a default caption
     *
     * @var string
     */
    public $captionField = 'name';

    /**
     * Predefined user roles
     *
     * @var string[]
     */
    static $userRoles = [
        USER_AUTHOR => "Current Author",
        USER_HIDDEN => "Former author",
        USER_ADMIN => "Admin"
    ];

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

        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('GlobalUsers', [
            'className' => 'Users',
            'bindingKey' => 'norm_iri',
            'foreignKey' => 'iri'
        ]);
    }

    /**
     * Default validation rules
     *
     * @param \Cake\Validation\Validator $validator Validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('deleted')
            ->allowEmptyString('deleted');

        $validator
            ->integer('modified_by')
            ->allowEmptyString('modified_by');

        $validator
            ->integer('created_by')
            ->allowEmptyString('created_by');

        $validator
            ->scalar('name')
            ->maxLength('name', 1500)
            ->allowEmptyString('name');

        $validator
            ->scalar('acronym')
            ->maxLength('acronym', 1500)
            ->allowEmptyString('acronym');

        $validator
            ->integer('userrole')
            ->allowEmptyString('userrole')
            ->add('role', 'inList', [
                'rule' => ['inList', array_keys(UsersTable::$userRoles)],
                'message' => 'Please enter a valid role'
            ]);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param RulesChecker $rules The rules object to be modified.
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        // TODO: not possible because versioning duplicates the IRIs
        // $rules->add($rules->isUnique(['norm_iri']));
        return $rules;
    }

    /**
     * Get columns
     *
     * TODO: keep all definitions, indexed by: articletype, default, query parameter
     *
     *  ### Options
     *  - type (string) Filter by type
     *  - join (boolean) Join the columns to the query
     *
     * @param array $selected The selected columns
     * @param array $default The default columns
     * @param array $options
     *
     * @return array
     */
    public function getColumns($selected = [], $default = [], $options = [])
    {
        $default = [
            'name' => ['caption' => __('Name'), 'width' => 200, 'default' => true],
            'acronym' => ['caption' => __('Acronym'), 'width' => 100, 'default' => true],
            'userrole' => ['caption' => __('Rolle'), 'width' => 100, 'default' => true],
            'norm_iri' => ['caption' => __('IRI fragment'), 'width' => 200, 'default' => true],
            'id' => ['caption' => 'ID', 'width' => 100, 'default' => true]
        ];

        return parent::getColumns($selected, $default, $options);
    }
}
