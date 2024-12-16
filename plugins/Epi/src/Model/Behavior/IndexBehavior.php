<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Behavior;

use App\Cache\Cache;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;

use Cake\Utility\Hash;
use Epi\Model\Entity\IndexProperty;
use Epi\Model\Entity\IndexSection;
use Epi\Model\Entity\IndexSegment;
use Epi\Model\Entity\SectionPath;

/**
 * Index behavior
 *
 * Prepares properties for index generation
 *
 * The index can be loaded from and saved to cache to collect items over multiple requests
 * The properties are obtained from the model tables index property.
 * Structure of the index property:
 * - key: property name; value: IndexSegment
 * - key in the properties value of IndexSegment objects: property id; value: IndexProperty.
 * - key in the sections value of IndexProperty objects: section id; value: IndexSection
 *
 * TODO: Rethink where to put which index:
 *       - for import, export, headers in view classes, lookup
 *       - in the job, in the table, in the behaviour, in the entities
 */
class IndexBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
    ];


    /**
     * addToIndex
     *
     * Used in TableArticles to collect properties including their links
     *
     * @param IndexProperty $indexProperty Derived from a property entity
     * @param SectionPath $sectionPath Derived from a section entity
     */
    public function addToIndex(IndexProperty $indexProperty, IndexSection $sectionPath = null)
    {
        $index = &$this->table()->index;

        // First level: property type
        if (!isset($index[$indexProperty['propertytype']])) {
            $index[$indexProperty['propertytype']] = new IndexSegment(['propertytype' => $indexProperty['propertytype']]);
        }
        $targetSegment = $index[$indexProperty['propertytype']];
        $targetProperty = $targetSegment->addProperty($indexProperty);

        // Link
        if (!empty($sectionPath)) {
            $targetProperty->addSectionPath($sectionPath);
        }

        //Ancestors
        if (!empty($indexProperty['ancestors'])) {
            $indexProperty->_added_ancestors = true;
            foreach ($indexProperty['ancestors'] as $ancestor) {
                $ancestorIndexProperty = new IndexProperty($ancestor);
                $ancestorIndexProperty->_added_ancestors = true;
                $this->addToIndex($ancestorIndexProperty);
            }
        }

        // Links in properties
        if (!empty($indexProperty['links'])) {
            //$property->_added_ancestors = true;
            foreach ($indexProperty['links'] as $link) {
                if (!empty($link['property'])) {
                    $linkIndexProperty = new IndexProperty($link['property']);
                    $this->addToIndex($linkIndexProperty, $sectionPath);
                }
            }
        }

        //Property of property
        if (!empty($indexProperty['property'])) {
            $metaIndexProperty = new IndexProperty($indexProperty['property']);
            $this->addToIndex($metaIndexProperty, $sectionPath);
        }
    }

    /**
     * loadIndex
     *
     * Loads index items from cache
     *
     * TODO: Rethink where to put which index: in the job, in the table, in the behaviour, in the entities
     *
     */
    public function loadIndex($cachekey = false)
    {
        if (!empty($cachekey)) {
            $json = Cache::read($cachekey, 'index');
            $index = [];
            foreach ($json ?? [] as $segKey => $segVal) {
                $index[$segKey] = new IndexSegment($segVal);
            }

//            $index = Cache::read($cachekey, 'index');
            $this->table()->index = $index;
        }
    }

    /**
     * saveIndex
     *
     * Persist the index between requests.
     *
     * @param false $cachekey
     */
    public function saveIndex($cachekey = false)
    {
        if (!empty($cachekey)) {
            $index = $this->table()->index;

            $cacheData = [];
            foreach ($index as $segKey => $segVal) {
                $cacheData[$segKey] = $segVal->getDataNested(['snippets' => ['index'], 'format' => 'json']);
            }
            Cache::write($cachekey, $cacheData, 'index');
        }
    }

    /**
     *  clearIndex
     *
     * @param $cachekey
     */
    public function clearIndex($cachekey, $deletecache = true)
    {
        $this->table()->index = [];
        if ($deletecache) {
            Cache::delete($cachekey, 'index');
        }
    }

    /**
     * Returns the augmented index,
     * prepared for serialization
     *
     * @return array
     */
    public function getIndexes($cachekey = false)
    {
        // Load from cache
        if (!empty($cachekey) && empty($this->table()->index)) {
            $this->loadIndex($cachekey);
        }

        // Augment indexes
        //$this->fetchAncestors();
        $this->fetchRelated();

        $index = collection($this->table()->index);
        $index = $index->map(
            function ($propertyType, $key) {


                //Sort tree and convert to array
                $propertyType['properties'] = collection($propertyType['properties'])
                    ->sortBy('lft', SORT_ASC)
                    ->map(
                        function ($property, $key) {
                            $property['sections'] = array_values($property['sections'] ?? []);
                            return $property;
                        }
                    );

                // Convert to array
                $propertyType['properties'] = array_values($propertyType['properties']->toArray());

                return $propertyType;
            }
        );

        // Convert to array
        $index = array_values($index->toArray());

        return $index;
    }

    /**
     * fetchAncestors
     *
     * Retrieve ancestor nodes for all entities in the indexes
     *
     * @return array
     */
    public function fetchAncestors()
    {
        $indexes = &$this->table()->index;

        $propertiesTable = TableRegistry::getTableLocator()->get('Epi.Properties');

        foreach ($indexes as $propertyType => &$indexSegment) {
            $properties = &$indexSegment['properties'];

            // Noch nicht bearbeitete Properties auswählen
            $nodes = collection($properties)->filter(
                function ($value) {
                    $doFetch = empty($value->_added_acestors);
                    $value->_added_ancestors = true;
                    return $doFetch;
                }
            );

            // Liste vorhandener Properties erstellen, damit sie nicht neu abgerufen werden
            $ignore = collection($properties)->extract('id')->toList();

            // Ancestors finden und hinzufügen
            $parents = $propertiesTable
                ->find('ancestorsFor', ['nodes' => $nodes])
                ->find('without', ['ids' => $ignore]);

            foreach ($parents as $ancestor) {
                $indexProperty =  new IndexProperty($ancestor);
                $indexProperty->container = $indexSegment;
                $this->addToIndex($indexProperty);
//                $indexProperty->root = $indexProperty;
//                $indexProperty->_added_ancestors = true;
//                $properties[] = $indexProperty;
            }
        }

        return $indexes;
    }

    /**
     * fetchRelated
     *
     * Retrieve related (lookup) properties including their ancestors
     *
     * @return array
     */
    public function fetchRelated()
    {
        $indexes = &$this->table()->index;
        $propertiesTable = TableRegistry::getTableLocator()->get('Epi.Properties');

        $nodes = [];

        foreach ($indexes as $propertyType => &$index) {
            $properties = &$index['properties'];

            // Noch nicht bearbeitete Properties auswählen
            foreach ($properties as &$property) {
                if (empty($value->_added_references)) {
                    $nodes[] = $property;
                }
                $property->_added_references = true;
            }
        }

        // Liste vorhandener Properties erstellen, damit sie nicht neu geholt werden
        //$ignore = collection($properties)->extract('id')->toList();

        if (!empty($nodes)) {
            $ids = Hash::extract($nodes, '{*}.id');

            // Add "Verweis unter"
            $references = $propertiesTable
                ->find('referencesFrom', ['nodes' => $ids]);
            $this->_addProperties($references);

            // Add "Siehe unter"
            $references = $propertiesTable
                ->find('referencesTo', ['nodes' => $ids]);
            $this->_addProperties($references);
        }

        return $indexes;
    }

    /**
     * Add all properties in a query result
     *
     * @param $properties
     * @return void
     */
    protected function _addProperties($properties)
    {
        foreach ($properties as $property) {
            // Add reference node
            $indexproperty = new IndexProperty($property);
            $this->addToIndex($indexproperty);

            // Add lemma node (including ancestors)
            if (!empty($property['lookup_from'])) {
                $indexproperty = new IndexProperty($property['lookup_from']);
                $this->addToIndex($indexproperty);
            }

            // Add lemma node (including ancestors)
            if (!empty($property['lookup_to'])) {
                $indexproperty = new IndexProperty($property['lookup_to']);
                $this->addToIndex($indexproperty);
            }
        }
    }

}
