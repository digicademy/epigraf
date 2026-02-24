<?php
declare(strict_types=1);

namespace Epi\Test\Fixture;
//bin/cake bake fixture -c projects --table notes --records Epi.Notes --conditions "id IN (2,4,5,6,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22)" --count 100
use Cake\TestSuite\Fixture\TestFixture;

/**
 * NotesFixture
 */
class NotesFixture extends TestFixture
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
        'version_id' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'deleted' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'published' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'menu' => ['type' => 'integer', 'length' => null, 'unsigned' => false, 'null' => true, 'default' => '1', 'comment' => '', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => true, 'default' => null, 'comment' => ''],
        'modified' => ['type' => 'datetime', 'length' => null, 'precision' => null, 'null' => true, 'default' => null, 'comment' => ''],
        'name' => ['type' => 'char', 'length' => 200, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'category' => ['type' => 'string', 'length' => 300, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'content' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'format' => ['type' => 'string', 'length' => 15, 'null' => false, 'default' => 'markdown', 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        'norm_iri' => ['type' => 'string', 'length' => 1500, 'null' => true, 'default' => null, 'collate' => 'utf8mb4_unicode_ci', 'comment' => '', 'precision' => null],
        '_indexes' => [
            'published' => ['type' => 'index', 'columns' => ['published'], 'length' => []],
        ],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'MyISAM',
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
                'id' => 2,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2017-10-30 11:34:43',
                'modified' => '2021-08-19 06:40:43',
                'name' => 'Wismar',
                'category' => 'Wismar',
                'content' => '



####Chat
1. hwi.marien.ct249
    + Literaturangabe in Anm. 3 präzisieren (in AUB vorhanden)
1. marien.ct+65
    * MD: Maße sind jetzt in Notizfeld
    + wann und wo wurden die Fragemente eingelagert ?
',
                'format' => 'markdown',
            ],
            [
                'id' => 4,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2017-10-30 11:54:55',
                'modified' => '2021-05-05 08:36:12',
                'name' => 'Mecklenburg, Literatur allgemein',
                'category' => 'Mecklenburg',
                'content' => ' [Landesbibliothek MV - Sonderbestände (Personalschriften, Ditmarsche Sammlungen und Schmidtsche Bibliothek)](http://www-db.lbmv.de/)',
                'format' => 'markdown',
            ],
            [
                'id' => 5,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2017-11-22 09:58:57',
                'modified' => '2020-07-10 09:16:39',
                'name' => 'Rostock, Grabplatten',
                'category' => 'Rostock',
                'content' => '__Bestandsübersicht Grabplatten__',
                'format' => 'markdown',
            ],
            [
                'id' => 11,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-03-01 09:16:35',
                'modified' => '2019-05-19 15:37:13',
                'name' => 'Mecklenburgs Maße',
                'category' => 'Mecklenburg',
                'content' => '1 Fuß = 12 Zoll',
                'format' => 'markdown',
            ],
            [
                'id' => 8,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-01-22 14:01:42',
                'modified' => '2021-02-20 08:08:11',
                'name' => 'Rostock: Linkliste Lit.',
                'category' => 'Rostock',
                'content' => '* [Schröder, Etwas von gelehrten Rostockschen Sachen](http://rosdok.uni-rostock.de/resolve?id=rosdok_series_000000000005)',
                'format' => 'markdown',
            ],
            [
                'id' => 9,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-02-28 15:16:02',
                'modified' => '2019-05-19 15:41:54',
                'name' => 'Allgemeine Hilfsmittel',
                'category' => 'Hilfsmittel',
                'content' => '---
[Deutsche Inschriften Online](http://www.inschriften.net/)

* [Startseite](http://www.inschriften.net/)
* [Suche](http://www.inschriften.net/suche.html)
',
                'format' => 'markdown',
            ],
            [
                'id' => 10,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-02-28 15:36:30',
                'modified' => '2019-05-19 15:39:54',
                'name' => 'Bearbeitungsrichtlinien',
                'category' => 'Richtlinien',
                'content' => '[Bearbeitungsrichtlinien 2005](https://epigraf.inschriften.net/epi4/inschriften_mv/files/download?path=hilfsmittel%2FDI-Allgemein%2FDI-Richtlinien&filename=Bearbeitungsrichtlinien05.pdf)',
                'format' => 'markdown',
            ],
            [
                'id' => 12,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-08-01 14:10:44',
                'modified' => '2019-05-19 15:37:27',
                'name' => 'Pommern',
                'category' => 'Pommern',
                'content' => '[Baltische Studien in DigiBib MV - 1832 bis 2002](http://www.digitale-bibliothek-mv.de/viewer/toc/PPN559838239/1/LOG_0000/)',
                'format' => 'markdown',
            ],
            [
                'id' => 6,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2017-11-22 10:00:57',
                'modified' => '2020-10-13 10:17:50',
                'name' => 'Rostock, St. Nikolai',
                'category' => 'Rostock',
                'content' => '**St. Nikolai**
- Turmhalle: Relief Gottvater (s. dazu Mail u. Foto Sakowski)
- Kirchenschiff: 1 steinernes Ep. Georg Jacobi (1597), 6 Wandleuchter, 4 Gp. an der Wand, 1 Gp. als Altarmensa, 1 stark beschädigte Predella (zugehöriges Retabel der Nikolaikirche heute in St. Marien, äußere Flügel in St. Petri [so Dehio MV])',
                'format' => 'markdown',
            ],
            [
                'id' => 13,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-08-13 09:02:50',
                'modified' => '2019-05-19 15:36:22',
                'name' => 'Archiv der Hansestadt Wismar - AHW',
                'category' => 'Wismar',
                'content' => '__hwi.georgen.ct017:__
',
                'format' => 'markdown',
            ],
            [
                'id' => 14,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-09-20 07:58:25',
                'modified' => '2022-01-17 00:05:56',
                'name' => 'Adressen Wismar',
                'category' => 'Wismar',
                'content' => 'Test2 __Museum__',
                'format' => 'markdown',
            ],
            [
                'id' => 15,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-10-18 12:36:05',
                'modified' => '2019-05-19 15:39:02',
                'name' => 'Markdown',
                'category' => 'Entwicklung',
                'content' => 'In-Browser-Editor: [StackEdit](https://stackedit.io/)

[Handbuch in Markdown](https://digitale-methodik.adwmainz.net/home/) <!-- username: herold-->

',
                'format' => 'markdown',
            ],
            [
                'id' => 16,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-11-08 10:00:34',
                'modified' => '2021-08-24 12:33:43',
                'name' => 'To-Do-Liste Wismar',
                'category' => 'Wismar',
                'content' => '#####Wismarfahrt August 2021',
                'format' => 'markdown',
            ],
            [
                'id' => 17,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-11-26 12:22:57',
                'modified' => '2019-12-17 09:20:26',
                'name' => 'Förderantrag',
                'category' => 'Organisation',
                'content' => 'Niedersächsisches Ministerium für Wissenschaft und Kultur / Volkswagenstiftung',
                'format' => 'markdown',
            ],
            [
                'id' => 18,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-11-27 10:08:27',
                'modified' => '2020-08-29 08:52:46',
                'name' => 'XML u. Co.',
                'category' => 'Entwicklung',
                'content' => '__Übersicht__',
                'format' => 'markdown',
            ],
            [
                'id' => 19,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-12-18 14:29:58',
                'modified' => '2019-05-19 15:41:16',
                'name' => 'Inschriftenkommission',
                'category' => 'Organisation',
                'content' => 'Protokoll der Sitzung vom 19. November 2018',
                'format' => 'markdown',
            ],
            [
                'id' => 20,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2018-12-19 10:16:45',
                'modified' => '2020-03-24 09:07:19',
                'name' => 'Epigraf-Workshops',
                'category' => 'Organisation',
                'content' => 'Themen für den nächsten Workshop',
                'format' => 'markdown',
            ],
            [
                'id' => 21,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2019-01-15 14:43:58',
                'modified' => '2019-05-19 15:36:41',
                'name' => 'HWI Meisterzeichen',
                'category' => 'Wismar',
                'content' => '1. Goldschmiede (MD)',
                'format' => 'markdown',
            ],
            [
                'id' => 22,
                'version_id' => 0,
                'norm_iri' => '',
                'deleted' => 0,
                'published' => null, 'menu'=>1,
                'created' => '2019-01-15 15:14:17',
                'modified' => '2020-10-15 13:20:49',
                'name' => 'Texttypen',
                'category' => 'Richtlinien',
                'content' => 'Stifterinschrift vs Stiftungsinschrift:

*  __Stifter__inschrift: wenn man davon ausgehen kann, dass der in der Inschrift genannte Stifter auch der Initiator der Inschrift ist (der Stifter hat die Inschrift anfertigen lassen (Stifterinschrift = "Inschrift des Stifters")
*  __Stiftungs__inschrift: der Stifter wird nicht namentlich genannt oder der als Stifter genannte ist nicht der Initiator der Inschrift (nicht der Stifter, sonder ein anderer hat die Inschrift anfertigen lassen)
* Stiftername: wenn in der Inschrift nur der Name des Stifters genannt ist (ohne Verb oder Erwähnung des gestifteten Objekts',
                'format' => 'markdown',
            ],
        ];
        parent::init();
    }
}
