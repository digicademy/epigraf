<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 * Command: bin/cake bake fixture -c projects --table articles --conditions "id > 0 AND id < 10" --records Epi.Articles --count 100
 */
namespace Epi\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ArticlesFixture
 */
class ArticlesFixture extends TestFixture
{
    /**
     * Database connection
     *
     * @var string
     */
	public $connection = 'test_projects';

    /**
     * Fields
     *
     * @var array
     */
    // phpcs:disable
    public $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'projects_id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'deleted' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'published' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'timestamp', 'length' => null, 'precision' => null, 'null' => true, 'default' => '0000-00-00 00:00:00', 'comment' => ''],
        'modified' => ['type' => 'timestamp', 'length' => null, 'precision' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => ''],
        'modified_by' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created_by' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'articletype' => ['type' => 'string', 'length' => 50, 'null' => false, 'default' => 'Objekt', 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'signature' => ['type' => 'string', 'length' => 1500, 'null' => false, 'default' => '', 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'name' => ['type' => 'string', 'length' => 1500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'status' => ['type' => 'string', 'length' => 1500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'norm_data' => ['type' => 'string', 'length' => 1500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'norm_iri' => ['type' => 'string', 'length' => 1500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'norm_type' => ['type' => 'string', 'length' => 1500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'lastopen_id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'lastopen_tab' => ['type' => 'string', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'lastopen_field' => ['type' => 'string', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'lastopen_tagid' => ['type' => 'string', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        '_indexes' => [
            'projects_id' => ['type' => 'index', 'columns' => ['projects_id'], 'length' => []],
            'state' => ['type' => 'index', 'columns' => ['deleted'], 'length' => []],
            'modified_by' => ['type' => 'index', 'columns' => ['modified_by'], 'length' => []],
            'created_by' => ['type' => 'index', 'columns' => ['created_by'], 'length' => []],
            'lastopen_id' => ['type' => 'index', 'columns' => ['lastopen_id'], 'length' => []],
            'lastopen_tab' => ['type' => 'index', 'columns' => ['lastopen_tab'], 'length' => ['lastopen_tab' => '255']],
            'lastopen_feld' => ['type' => 'index', 'columns' => ['lastopen_field'], 'length' => ['lastopen_field' => '255']],
            'lastopen_subid' => ['type' => 'index', 'columns' => ['lastopen_tagid'], 'length' => ['lastopen_tagid' => '255']],
            'published' => ['type' => 'index', 'columns' => ['published'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8mb4_unicode_ci'
        ],
    ];
    // phpcs:enable
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
                'projects_id' => 5,
                'deleted' => 0,
                'published' => null,
                'created' => '2007-08-20 22:00:00',
                'modified' => '2021-05-01 18:59:54',
                'modified_by' => 36,
                'created_by' => 1,
                'articletype' => 'epi-article',
                'signature' => 'dbr.rerik.alt-gaarz.glocke1',
                'name' => '',
                'status' => 'neu angelegt',
                'norm_data' => null,
                'norm_iri' => null,
                'norm_type' => null,
                'lastopen_id' => 1,
                'lastopen_tab' => 'articles',
                'lastopen_field' => 'projekt',
                'lastopen_tagid' => '',
            ],
            [
                'id' => 2,
                'projects_id' => 5,
                'deleted' => 0,
                'published' => null,
                'created' => '2007-08-20 22:00:00',
                'modified' => '2021-05-01 18:59:54',
                'modified_by' => 36,
                'created_by' => 1,
                'articletype' => 'epi-article',
                'signature' => 'dbr.rerik.alt-gaarz.glocke2',
                'name' => '',
                'status' => 'neu angelegt',
                'norm_data' => null,
                'norm_iri' => null,
                'norm_type' => null,
                'lastopen_id' => 2,
                'lastopen_tab' => 'articles',
                'lastopen_field' => 'projekt',
                'lastopen_tagid' => '',
            ],
            [
                'id' => 3,
                'projects_id' => 5,
                'deleted' => 0,
                'published' => null,
                'created' => '2007-08-20 22:00:00',
                'modified' => '2021-05-01 18:59:54',
                'modified_by' => 36,
                'created_by' => 1,
                'articletype' => 'epi-article',
                'signature' => 'dbr.rerik.alt-gaarz.glocke3',
                'name' => '',
                'status' => 'neu angelegt',
                'norm_data' => null,
                'norm_iri' => null,
                'norm_type' => null,
                'lastopen_id' => 3,
                'lastopen_tab' => 'articles',
                'lastopen_field' => 'projekt',
                'lastopen_tagid' => '',
            ],
            [
                'id' => 4,
                'projects_id' => 5,
                'deleted' => 0,
                'published' => null,
                'created' => '2007-08-20 22:00:00',
                'modified' => '2021-05-01 18:59:54',
                'modified_by' => 36,
                'created_by' => 1,
                'articletype' => 'epi-article',
                'signature' => 'dbr.rerik.alt-gaarz.gp-oertzen1',
                'name' => '',
                'status' => 'neu angelegt',
                'norm_data' => null,
                'norm_iri' => null,
                'norm_type' => null,
                'lastopen_id' => 4,
                'lastopen_tab' => 'articles',
                'lastopen_field' => 'projekt',
                'lastopen_tagid' => '',
            ],
            [
                'id' => 5,
                'projects_id' => 5,
                'deleted' => 0,
                'published' => null,
                'created' => '2007-08-20 22:00:00',
                'modified' => '2021-05-01 18:59:54',
                'modified_by' => 36,
                'created_by' => 1,
                'articletype' => 'epi-article',
                'signature' => 'dbr.rerik.alt-gaarz.gp-oertzen2',
                'name' => 'Oertzen, Vicke von; Stralendorff, Adelheid von',
                'status' => '',
                'norm_data' => null,
                'norm_iri' => null,
                'norm_type' => null,
                'lastopen_id' => 5,
                'lastopen_tab' => 'articles',
                'lastopen_field' => 'projekt',
                'lastopen_tagid' => '',
            ],
            [
                'id' => 6,
                'projects_id' => 5,
                'deleted' => 0,
                'published' => null,
                'created' => '2007-08-20 22:00:00',
                'modified' => '2021-05-01 18:59:54',
                'modified_by' => 34,
                'created_by' => 1,
                'articletype' => 'epi-article',
                'signature' => 'dbr.althof.woizlawa',
                'name' => 'Woizlawa',
                'status' => '',
                'norm_data' => null,
                'norm_iri' => null,
                'norm_type' => null,
                'lastopen_id' => null,
                'lastopen_tab' => null,
                'lastopen_field' => null,
                'lastopen_tagid' => null,
            ],
            [
                'id' => 7,
                'projects_id' => 5,
                'deleted' => 0,
                'published' => null,
                'created' => '2007-08-14 22:00:00',
                'modified' => '2021-05-01 18:59:54',
                'modified_by' => 4,
                'created_by' => 2,
                'articletype' => 'epi-article',
                'signature' => 'dbr.hornstorf.gp-johann',
                'name' => 'Johann, Pfarrer',
                'status' => '',
                'norm_data' => null,
                'norm_iri' => null,
                'norm_type' => null,
                'lastopen_id' => 7,
                'lastopen_tab' => 'articles',
                'lastopen_field' => 'status',
                'lastopen_tagid' => '',
            ],
            [
                'id' => 8,
                'projects_id' => 5,
                'deleted' => 0,
                'published' => null,
                'created' => '2007-06-27 22:00:00',
                'modified' => '2021-05-01 18:59:54',
                'modified_by' => 4,
                'created_by' => 2,
                'articletype' => 'epi-article',
                'signature' => 'dbr.kavelstorf.gp-rüze',
                'name' => 'Rüze, Werner + Bertha',
                'status' => '',
                'norm_data' => null,
                'norm_iri' => null,
                'norm_type' => null,
                'lastopen_id' => 8,
                'lastopen_tab' => 'items_bearbeitungen',
                'lastopen_field' => 'uebersetzung',
                'lastopen_tagid' => '',
            ],
            [
                'id' => 9,
                'projects_id' => 5,
                'deleted' => 0,
                'published' => null,
                'created' => '2006-10-04 22:00:00',
                'modified' => '2021-05-01 18:59:54',
                'modified_by' => 36,
                'created_by' => 3,
                'articletype' => 'epi-article',
                'signature' => 'dbr.klosterkirche.holztafel-biddet',
                'name' => 'Balthasar, Herzog; Erich, Herzog; Ursula, Herzogin',
                'status' => '',
                'norm_data' => null,
                'norm_iri' => null,
                'norm_type' => null,
                'lastopen_id' => 9,
                'lastopen_tab' => 'articles',
                'lastopen_field' => 'projekt',
                'lastopen_tagid' => '',
            ],
        ];
        parent::init();
    }
}
