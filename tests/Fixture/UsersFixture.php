<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 * Created with command: bin/cake bake fixture Users --records --conditions "username IN ('author', 'admin', 'reader', 'editor', 'devel')" --count 10
 * - pipeline_article_id and pipeline_book_id were manually changed to 19 and 21
 * - accesstokens were manually changed to match the role
 * - order was changed to author, admin, reader, editor, devel
 * - IDs were manually changed to 1,2,3,4,5
 * - databank_ids were changed to 1
 *
 *
 */

declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    /**
     * Database connection
     *
     * @var string
     */
    public $connection = 'test';

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
                'created' => '2022-04-21 11:34:15',
                'modified' => '2022-04-21 11:34:15',
                'lastaction' => '2022-04-30 11:12:35',
                'username' => 'author',
                'password' => '$2y$10$hSugdmzbHGPQj37iQ1glZOzX.5e8dxURbfRYjiG7UkynpBqqfhVii',
                'contact' => '',
                'accesstoken' => 'TESTTOKENAUTHOR',
                'role' => 'author',
                'databank_id' => 1,
                'pipeline_article_id' => 19,
                'pipeline_book_id' => 21,
                'settings' => null,
                'norm_iri' => 'author'
            ],

            [
                'id' => 2,
                'created' => '2022-04-21 11:34:15',
                'modified' => '2022-04-21 11:34:15',
                'lastaction' => null,
                'username' => 'admin',
                'password' => '$2y$10$EOBV.D6CNKkDYg5LglNBv.r/VgwsUhESYYu7TQo8YkbuSCI7TDhS6',
                'contact' => '',
                'accesstoken' => 'TESTTOKENADMIN',
                'role' => 'admin',
                'databank_id' => 1,
                'pipeline_article_id' => 19,
                'pipeline_book_id' => 21,
                'settings' => null,
                'norm_iri' => 'admin'
            ],

            [
                'id' => 3,
                'created' => '2022-04-21 11:34:15',
                'modified' => '2022-04-21 11:34:15',
                'lastaction' => null,
                'username' => 'reader',
                'password' => '$2y$10$DmBCBdAgXjlWvhZAJGWVK.ONiUst8Ui3eq4eQZFb2GS1F6AyR6qdq',
                'contact' => '',
                'accesstoken' => 'TESTTOKENREADER',
                'role' => 'reader',
                'databank_id' => 1,
                'pipeline_article_id' => 19,
                'pipeline_book_id' => 21,
                'settings' => null,
                'norm_iri' => 'reader'
            ],
            [
                'id' => 4,
                'created' => '2022-04-21 11:34:15',
                'modified' => '2022-04-21 11:34:15',
                'lastaction' => null,
                'username' => 'editor',
                'password' => '$2y$10$gIdaevj5Eeei8RMVKUWcy.4xG0ByGqZAIHBCrCZDlkSqe72aKmkv.',
                'contact' => '',
                'accesstoken' => 'TESTTOKENEDITOR',
                'role' => 'editor',
                'databank_id' => 1,
                'pipeline_article_id' => 19,
                'pipeline_book_id' => 21,
                'settings' => null,
                'norm_iri' => 'editor'
            ],
            [
                'id' => 5,
                'created' => '2022-04-21 11:34:15',
                'modified' => '2022-04-21 11:34:15',
                'lastaction' => '2022-04-30 11:24:43',
                'username' => 'devel',
                'password' => '$2y$10$f4/VnQoNJ6VUvXwWCh4FH.fKJLQRZYTsc4P9cxCmnRZiBWVJjj5rm',
                'contact' => '',
                'accesstoken' => 'TESTTOKENDEVEL',
                'role' => 'devel',
                'databank_id' => 1,
                'pipeline_article_id' => 19,
                'pipeline_book_id' => 21,
                'settings' => array(
                    'paths' =>
                        array(
                            '/epi/epi_playground/articles' =>
                                array(
                                    'articles_term' => '',
                                    'articles_field' => 'captions',
                                    'articles_articletype' => 'epi-article',
                                    'articles_sort' => 'Articles.signature',
                                    'articles_direction' => 'asc',
                                    'articles_projects' => '',
                                    'properties_objecttypes' => '',
                                    'fields' => 'project_signature,signature,title',
                                ),
                        ),
                    'columns' =>
                        array(
                            'pipelines' =>
                                array(
                                    'name' => '252.39200000000002',
                                    'descriptions' => '997.41',
                                ),
                        ),
                ),
                'norm_iri' => 'devel'
            ]
        ];
        parent::init();
    }
}
