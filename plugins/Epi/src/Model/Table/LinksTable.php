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

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Links table
 */
class LinksTable extends BaseTable
{
    /**
     * Type field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $typeField = 'from_tagname';

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

        $this->setTable('links');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo(
            'Properties',
            [
                'foreignKey' => 'to_id',
                'conditions' => ['to_tab' => 'properties'],
                'className' => 'Epi.Properties',
                'propertyName' => 'property'
            ]
        );

        $this->belongsTo(
            'PropertiesWithAncestors',
            [
                'foreignKey' => 'to_id',
                'conditions' => ['to_tab' => 'properties'],
                'className' => 'Epi.Properties',
                'propertyName' => 'property',
                'finder' => 'containAncestors'
            ]
        );

        $this->belongsTo(
            'Articles',
            [
                'foreignKey' => 'to_id',
                'conditions' => ['Links.to_tab' => 'articles'],
                'className' => 'Epi.Articles',
                'propertyName' => 'article'
            ]
        );

        $this->belongsTo(
            'Sections',
            [
                'foreignKey' => 'to_id',
                'conditions' => ['Links.to_tab' => 'sections'],
                'className' => 'Epi.Sections',
                'propertyName' => 'section'
            ]
        );

        $this->belongsTo(
            'SectionsWithAncestors',
            [
                'foreignKey' => 'to_id',
                'conditions' => ['Links.to_tab' => 'sections'],
                'className' => 'Epi.Sections',
                'propertyName' => 'section',
                'finder' => 'containAncestors'
            ]
        );

        $this->belongsTo(
            'Footnotes',
            [
                'foreignKey' => 'to_id',
                'conditions' => ['Links.to_tab' => 'footnotes'],
                'className' => 'Epi.Footnotes',
                'propertyName' => 'footnote'
            ]
        );
    }

    /**
     * Default validation rules
     *
     * @param Validator $validator Validator instance
     * @return Validator
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
            ->scalar('root_tab')
            ->maxLength('root_tab', 500)
            ->allowEmptyString('root_tab');

        $validator
            ->scalar('from_tab')
            ->maxLength('from_tab', 500)
            ->allowEmptyString('from_tab');

        $validator
            ->scalar('from_field')
            ->maxLength('from_field', 500)
            ->allowEmptyString('from_field');

        $validator
            ->scalar('from_tagid')
            ->maxLength('from_tagid', 500)
            ->allowEmptyString('from_tagid');

        $validator
            ->scalar('from_tagname')
            ->maxLength('tagname', 1500)
            ->allowEmptyString('from_tagname');

        $validator
            ->scalar('to_tab')
            ->maxLength('to_tab', 500)
            ->allowEmptyString('to_tab');

//        $validator
//            ->integer('to_id');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating application integrity
     *
     * @param RulesChecker $rules
     *
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        return $rules;
    }

    /**
     * Prepare a new property if requested
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @return void
     */
    public function afterMarshal(
        EventInterface $event,
        EntityInterface $entity,
        ArrayObject $data,
        ArrayObject $options
    ) {
        $toValue = $data['to_value'] ?? '';
        if (empty($entity->to_id) && ($toValue !== '')) {
            $property = [];
            $property['propertytype'] = $data['to_type'] ?? '';
            $property['lemma'] = $data['to_value'] ?? '';
            $entity->newproperty = $this->Properties->newEntity($property);
        }

        parent::afterMarshal($event, $entity, $data, $options);
    }

    /**
     * Save a new property as prepared in beforeMarshal()
     *
     * @param EventInterface $event
     * @param $entity
     * @param $options
     */
    public function beforeSave(EventInterface $event, $entity, $options)
    {
        // Create new property
        if (!empty($entity->newproperty)) {
            $property = $entity->newproperty;
            $this->Properties->save($property);
            $entity->property = $property;
            $entity->to_id = $property->id;
        }
    }
}
