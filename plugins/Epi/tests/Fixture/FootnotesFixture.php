<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 * Command: bin/cake bake fixture -c projects --table footnotes --conditions "root_tab='articles' AND root_id > 0 AND root_id < 10" --records Epi.Footnotes --count 500
 */
declare(strict_types=1);

namespace Epi\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * FootnotesFixture
 */
class FootnotesFixture extends TestFixture
{
    /**
     * Database connection
     *
     * @var string
     */
    public $connection = 'test_projects';

    /**
     * Table name
     *
     * @var string
     */
    public $table = 'footnotes';
    /**
     * Fields
     *
     * @var array
     */
    // phpcs:disable
    public $fields = [
        'id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'deleted' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'published' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'timestamp', 'length' => null, 'precision' => null, 'null' => true, 'default' => '0000-00-00 00:00:00', 'comment' => ''],
        'modified' => ['type' => 'timestamp', 'length' => null, 'precision' => null, 'null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => ''],
        'modified_by' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created_by' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'sortno' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'fntype' => ['type' => 'boolean', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'name' => ['type' => 'string', 'length' => 200, 'null' => false, 'default' => '', 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'content' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'root_id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'root_tab' => ['type' => 'string', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'from_id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'from_tab' => ['type' => 'string', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'from_field' => ['type' => 'string', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'from_tagname' => ['type' => 'string', 'length' => 10, 'null' => false, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'from_tagid' => ['type' => 'string', 'length' => 500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'from_sort' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        '_indexes' => [
            'state' => ['type' => 'index', 'columns' => ['deleted'], 'length' => []],
            'modified_by' => ['type' => 'index', 'columns' => ['modified_by'], 'length' => []],
            'created_by' => ['type' => 'index', 'columns' => ['created_by'], 'length' => []],
            'rootrecord_id' => ['type' => 'index', 'columns' => ['root_id'], 'length' => []],
            'rootrecord_tab' => ['type' => 'index', 'columns' => ['root_tab'], 'length' => ['root_tab' => '255']],
            'linkrec_id' => ['type' => 'index', 'columns' => ['from_id'], 'length' => []],
            'linkrec_tab' => ['type' => 'index', 'columns' => ['from_tab'], 'length' => ['from_tab' => '255']],
            'linkrec_feld' => ['type' => 'index', 'columns' => ['from_field'], 'length' => ['from_field' => '255']],
            'linkrec_subid' => ['type' => 'index', 'columns' => ['from_tagid'], 'length' => ['from_tagid' => '255']],
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
                'deleted' => 0,
                'published' => null,
                'created' => '2008-05-05 18:50:24',
                'modified' => '2021-05-01 18:59:55',
                'modified_by' => 34,
                'created_by' => null,
                'sortno' => 16,
                'fntype' => false,
                'name' => 'b',
                'content' => '<quot id="000003957386834363425925957201">vicke</quot>] Davor ein liegendes Rechteck. <nl id="000003957386834363425925942758"></nl>',
                'root_id' => 5,
                'root_tab' => 'articles',
                'from_id' => 285720,
                'from_tab' => 'items',
                'from_field' => 'content',
                'from_tagname' => 'app2',
                'from_tagid' => '000003957386834363425925913760',
                'from_sort' => 2,
            ],
            [
                'id' => 2,
                'deleted' => 0,
                'published' => null,
                'created' => '2008-05-05 18:50:24',
                'modified' => '2021-05-01 18:59:55',
                'modified_by' => 34,
                'created_by' => null,
                'sortno' => 16,
                'fntype' => false,
                'name' => 'c',
                'content' => '<quot id="000003957386834399305555645672">sy</quot>] Steht verkleinert unter der Zeile.<nl id="000003957386834399305555653195"></nl>',
                'root_id' => 5,
                'root_tab' => 'articles',
                'from_id' => 285720,
                'from_tab' => 'items',
                'from_field' => 'content',
                'from_tagname' => 'app2',
                'from_tagid' => '000003957386834381944444542609',
                'from_sort' => 3,
            ],
            [
                'id' => 5926,
                'deleted' => 0,
                'published' => null,
                'created' => '2012-02-07 17:13:47',
                'modified' => '2021-05-01 18:59:55',
                'modified_by' => 34,
                'created_by' => 4,
                'sortno' => 16,
                'fntype' => false,
                'name' => 'a',
                'content' => '<quot id="000004094675934203703703715784">-ghebaren</quot> Schlie.',
                'root_id' => 5,
                'root_tab' => 'articles',
                'from_id' => 285720,
                'from_tab' => 'items',
                'from_field' => 'content',
                'from_tagname' => 'app2',
                'from_tagid' => '000004094675921652777777847286',
                'from_sort' => 1,
            ],
            [
                'id' => 6106,
                'deleted' => 0,
                'published' => null,
                'created' => '2012-04-03 07:50:58',
                'modified' => '2021-05-01 18:59:54',
                'modified_by' => 4,
                'created_by' => 4,
                'sortno' => 14001,
                'fntype' => true,
                'name' => '1',
                'content' => 'Nach Minneker, Kloster, S. 55.',
                'root_id' => 6,
                'root_tab' => 'articles',
                'from_id' => 345510,
                'from_tab' => 'items',
                'from_field' => 'content',
                'from_tagname' => 'app1',
                'from_tagid' => '000004100241023362268518546444',
                'from_sort' => 1,
            ],
            [
                'id' => 3182,
                'deleted' => 0,
                'published' => null,
                'created' => '2008-07-21 09:46:55',
                'modified' => '2021-05-01 18:59:55',
                'modified_by' => null,
                'created_by' => 1,
                'sortno' => 17001,
                'fntype' => false,
                'name' => 'a',
                'content' => '<quot id="000003965049066098379629641759">Söne</quot>] Schlie gibt an: "Statt \'Söne\' muss es \'Sone\' heissen, denn Balthasar ist der Bruder und nur Erich der Sohn des Herzogs Magnus. ',
                'root_id' => 9,
                'root_tab' => 'articles',
                'from_id' => 285724,
                'from_tab' => 'items',
                'from_field' => 'content',
                'from_tagname' => 'app2',
                'from_tagid' => '000003965048989709490740746574',
                'from_sort' => 1,
            ],
        ];
        parent::init();
    }
}
