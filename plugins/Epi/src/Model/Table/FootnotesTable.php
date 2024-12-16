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
 * Footnotes table
 *
 */
class FootnotesTable extends BaseTable
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

        $this->setTable('footnotes');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Epi.XmlStyles', ['fields' => ['content', 'segment']]);

        $this->belongsTo(
            'FootnoteArticles',
            [
                'className' => 'Epi.Articles',
                'foreignKey' => 'root_id',
                //'strategy' => BelongsTo::STRATEGY_SELECT,
                //'joinType' => Query::JOIN_TYPE_LEFT,
                'propertyName' => 'article',
                'conditions' => ['Footnotes.root_tab' => 'articles', 'FootnoteArticles.deleted' => 0]
            ]
        );

        $this->hasMany('ToLinks', [
            'className' => 'Epi.Links',
            'foreignKey' => 'to_id',
            'conditions' => ['Links.to_tab' => 'footnotes', 'Links.deleted' => 0]
        ]);

//        $this->belongsTo(
//            'Types',
//            [
//                'className' => 'Epi.Types',
//                'strategy' => BelongsTo::STRATEGY_SELECT,
//                'joinType' => Query::JOIN_TYPE_LEFT,
//                'foreignKey' => 'from_tagname',
//                'bindingKey' => 'name',
//                'conditions' => ['Types.scope' => 'links']
//            ]
//        );

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
            ->scalar('name')
            ->maxLength('name', 200)
            ->allowEmptyString('name');

        $validator
            ->integer('sortno')
            ->allowEmptyString('sortno');

        $validator
            ->integer('from_sort')
            ->allowEmptyString('from_sort');

        $validator
            ->scalar('content')
            ->allowEmptyString('content');

        $validator
            ->boolean('fntype')
            ->allowEmptyString('fntype');

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

}
