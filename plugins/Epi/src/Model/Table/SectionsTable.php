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

use App\Model\Behavior\VersionedTreeBehavior;
use App\Model\Interfaces\ScopedTableInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Epi\Model\Entity\Section;

/**
 * Sections table
 *
 * # Behaviors
 * @mixin \Epi\Model\Behavior\PositionBehavior
 * @mixin VersionedTreeBehavior
 */
class SectionsTable extends BaseTable implements ScopedTableInterface
{

    /**
     * Type field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $typeField = 'sectiontype';

    /**
     * Scope field for scoped queries and IRI paths
     *
     * @var null|string
     */
    public $scopeField = 'articles_id';

    /**
     * Current scope
     *
     * @var null
     */
    public $scopeValue = null;

    /**
     * Store recover operations for the VersionedTreeBehavior
     *
     * @var array
     */
    public $_recoverQueue = [];

    /**
     * Store move operations for the VersionedTreeBehavior
     *
     * @var array
     */
    public $_moveQueue = [];

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

        $this->setTable('sections');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        //$this->removeBehavior('Version');
        $this->addBehavior('VersionedTree', ['level' => 'level', 'recoverOrder' => 'sortno']);
        $this->addBehavior('Epi.Position');
        $this->addBehavior('Epi.XmlStyles', ['fields' => ['comment']]);

        $this->belongsTo(
            'SectionArticles',
            [
                'className' => 'Epi.Articles',
                'foreignKey' => 'articles_id',
//                'strategy' => BelongsTo::STRATEGY_SELECT,
//				'joinType' => Query::JOIN_TYPE_LEFT,
                'propertyName' => 'article'
            ]
        );

        $this->hasMany(
            'Items',
            [
                'className' => 'Epi.Items',
                'foreignKey' => 'sections_id',
                'joinType' => Query::JOIN_TYPE_LEFT,
                'sort' => ['Items.itemtype', 'Items.sortno'],
                'propertyName' => 'items',
                'conditions' => ['Items.deleted' => 0],
                'dependent' => true,
                'cascadeCallbacks' => true
            ]
        );

        $this->hasMany(
            'ToLinks',
            [
                'className' => 'Epi.Links',
                'foreignKey' => 'to_id',
                'conditions' => ['Links.to_tab' => 'sections']
            ]
        );

        $this->hasMany(
            'FromLinks',
            [
                'className' => 'Epi.Links',
                'foreignKey' => 'from_id',
                'conditions' => ['FromLinks.from_tab' => 'sections'],
                'dependent' => true,
                'cascadeCallbacks' => true
            ]
        );

        $this->hasMany('Footnotes', [
            'className' => 'Epi.Footnotes',
            'foreignKey' => 'from_id',
            'conditions' => ['Footnotes.from_tab' => 'sections', 'Footnotes.deleted' => 0],
            'dependent' => true,
            'cascadeCallbacks' => true
        ]);

        $this->belongsTo(
            'Types',
            [
                'className' => 'Epi.Types',
                'strategy' => BelongsTo::STRATEGY_SELECT,
                'joinType' => Query::JOIN_TYPE_LEFT,
                'foreignKey' => 'sectiontype',
                'bindingKey' => 'name',
                'conditions' => ['Types.scope' => 'sections', 'Types.mode' => 'default', 'Types.preset' => 'default']
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
            ->scalar('sectiontype')
            ->maxLength('sectiontype', 100);

        $validator
            ->scalar('norm_iri')
            ->maxLength('norm_iri', 500)
            ->add('norm_iri', 'validFormat', [
                'rule' => ['custom', '/^[a-z0-9_~-]+$/'],
                'message' => 'Only lowercase alphanumeric characters, underscore, hyphen and tilde are allowed.'
            ])
            ->allowEmptyString('norm_iri');

        return $validator;
    }

    /**
     * Set root and container property of the items,
     * trigger fulltext indexer
     *
     * TODO: normalize sortno field by itemtype (EpiDesktop produces gaps by ignoring itemtype)
     *
     * @param EventInterface $event
     * @param Section $entity
     * @param array $options
 */
    public function beforeSave(EventInterface $event, $entity, $options = [])
    {
        if (!empty($entity->items)) {
            foreach ($entity->items as $item) {
                $item->container = $entity;
                $item->root = $entity->root;
            }
        }

        if (($options['fulltext'] ?? false) && ($entity->type['merged']['fulltext'] ?? true)) {
            $entity->updateSearchItems();
        }
    }

    /**
     * Get all scopes (= article ids)
     *
     * implements ScopedTableInterface
     *
     * @param array $options
     * @return array
     */
    public function getScopes(array $options = []): array
    {
        $this->removeScope();
        $scopes = $this
            ->SectionArticles
            ->find('list', ['valueField' => 'id'])
            ->toArray();

        return $scopes;
    }

    /**
     * Get current article ID
     *
     * @return string
     */
    public function getScope(): string
    {
        return $this->scopeValue ?? '';
    }

    /**
     * Enable tree behavior and set scope (=article id)
     *
     * @implements ScopedTableInterface
     * @param string $scope
     * @return Table
     */
    public function setScope($scope = null): Table
    {
        if ($scope instanceof EntityInterface) {
            $scope = $scope->{$this->scopeField};
        }

        if ($this->behaviors()->has('VersionedTree')) {
            if (is_null($scope)) {
                $scopeCondition = [$this->getAlias() . '.articles_id IS' => null, $this->getAlias() . '.deleted' => 0];
            }
            else {
                $scopeCondition = [$this->getAlias() . '.articles_id' => $scope, $this->getAlias() . '.deleted' => 0];
            }

            $this->behaviors()->VersionedTree->setConfig('scope', $scopeCondition, false);
        }

        $this->scopeValue = $scope;
        return $this;
    }


    /**
     * Disable tree behavior and, thus, remove scope
     *
     * @implements ScopedTableInterface
     * @return Table
     */
    public function removeScope(): Table
    {
        if ($this->behaviors()->has('VersionedTree')) {
            $this->behaviors()->VersionedTree->setConfig('scope', null);
        }

        $this->scopeValue = null;
        return $this;
    }

}
