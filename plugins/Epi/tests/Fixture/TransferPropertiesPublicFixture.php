<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 * First, find relevant IDs from items and links:
 *    SELECT DISTINCT tree.id FROM properties AS leaves INNER JOIN items AS items
 *    ON items.properties_id = leaves.id AND items.articles_id > 0 AND items.articles_id < 10
 *    LEFT JOIN properties AS tree ON tree.propertytype = leaves.propertytype
 *    AND tree.lft <= leaves.lft AND tree.rght >= leaves.rght
 *
 *    SELECT DISTINCT tree.id FROM properties AS leaves INNER JOIN links AS links
 *    ON links.to_id = leaves.id AND links.to_tab = "properties"
 *	  AND links.root_id > 0 AND links.root_id < 10 AND links.root_tab = "articles"
 *    LEFT JOIN properties AS tree ON tree.propertytype = leaves.propertytype
 *    AND tree.lft <= leaves.lft AND tree.rght >= leaves.rght
 *
 * Second, e.g. using a text editor create a comma separated string containing all ids:
 *    350,10434,7629,8124,8125,67,21083,22915,20890,21910,20852,20853,329,341,10426,10437,7631,264,266,458,10494,23918,7691,8126,78,21081,21625,20884,21624,21288,21204,368,460,461,8127,347,8128,346,7743,8129,10174,10255,21008,253,23,105,133,254,137,29,125,107,54
 *
 * Third, create fixture
 *     bin/cake bake fixture -c projects --table properties --records Epi.Properties --conditions "id IN (350,10434,7629,8124,8125,67,21083,22915,20890,21910,20852,20853,329,341,10426,10437,7631,264,266,458,10494,23918,7691,8126,78,21081,21625,20884,21624,21288,21204,368,460,461,8127,347,8128,346,7743,8129,10174,10255,21008,253,23,105,133,254,137,29,125,107,54)" --count 500
 *
 */
namespace Epi\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;


/**
 * PropertiesFixture
 */
class TransferPropertiesPublicFixture extends TestFixture
{
    /**
     * Database connection
     *
     * @var string
     */
    public $connection = 'test_public';

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'properties';

    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'deleted' => 0,
                'published' => null,
                'created' => '2008-05-05 18:51:24',
                'modified' => '2021-05-02 23:13:36',
                'created_by' => null,
                'modified_by' => 5,
                'sortno' => 26,
                'sortkey' => 'HolzPublic',
                'propertytype' => 'materials',
                'signature' => null,
                'file_name' => null,
                'properties_id' => null,
                'lemma' => 'HolzPublic',
                'name' => 'HolzPublic',
                'unit' => null,
                'comment' => 'Do some wooden comments arrive from the public database?',
                'content' => null,
                'elements' => null,
                'keywords' => '',
                'source_from' => null,
                'ishidden' => null,
                'iscategory' => null,
                'norm_type' => null,
                'norm_data' => null,
                'norm_iri' => 'holz',
                'import_db' => null,
                'import_id' => null,
                'related_id' => null,
                'mergedto_id' => null,
                'splitfrom_id' => null,
                'parent_id' => null,
                'level' => 0,
                'lft' => 1,
                'rght' => 2,

            ],
            [
                'id' => 2,
                'deleted' => 0,
                'published' => null,
                'created' => '2008-05-05 18:51:24',
                'modified' => '2021-05-02 23:13:36',
                'created_by' => null,
                'modified_by' => null,
                'sortno' => 1,
                'sortkey' => 'PublicSortUnknownMaterial',
                'propertytype' => 'materials',
                'signature' => null,
                'file_name' => null,
                'properties_id' => null,
                'lemma' => 'PublicLemmaUnknownMaterial',
                'name' => 'PublicNameUnknownMaterial',
                'unit' => null,
                'comment' => null,
                'content' => null,
                'elements' => null,
                'keywords' => null,
                'source_from' => null,
                'ishidden' => null,
                'iscategory' => null,
                'norm_type' => null,
                'norm_data' => null,
                'norm_iri' => null,
                'import_db' => null,
                'import_id' => null,
                'related_id' => null,
                'mergedto_id' => null,
                'splitfrom_id' => null,
                'parent_id' => null,
                'level' => 0,
                'lft' => 3,
                'rght' => 4,

            ],
            [
                'id' => 3,
                'deleted' => 0,
                'published' => null,
                'created' => '2008-05-05 18:51:24',
                'modified' => '2021-05-02 23:13:36',
                'created_by' => null,
                'modified_by' => null,
                'sortno' => 2,
                'sortkey' => 'PublicSortKnownMaterial',
                'propertytype' => 'materials',
                'signature' => null,
                'file_name' => null,
                'properties_id' => null,
                'lemma' => 'PublicLemmaKnownMaterial',
                'name' => 'PublicNameKnownMaterial',
                'unit' => null,
                'comment' => null,
                'content' => null,
                'elements' => null,
                'keywords' => null,
                'source_from' => null,
                'ishidden' => null,
                'iscategory' => null,
                'norm_type' => null,
                'norm_data' => null,
                'norm_iri' => 'knownmaterial',
                'import_db' => null,
                'import_id' => null,
                'related_id' => null,
                'mergedto_id' => null,
                'splitfrom_id' => null,
                'parent_id' => null,
                'level' => 0,
                'lft' => 5,
                'rght' => 6,

            ]
        ];
        parent::init();
    }


}
