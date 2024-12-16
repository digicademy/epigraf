<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 */

declare(strict_types=1);

namespace Epi\Test\TestCase\Model\Table;

use Epi\Model\Table\FootnotesTable;
use Cake\ORM\TableRegistry;
use Epi\Test\TestCase\EpiTestCase;

/**
 * App\Model\Table\FussnotenTable Test Case
 */
class FootnotesTableTest extends EpiTestCase
{
    /**
     * Test subject
     *
     * @var Footnotes
     */
    protected $Footnotes;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Footnotes') ? [] : ['className' => FootnotesTable::class];
        $this->Footnotes = $this->fetchTable('Footnotes', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Footnotes);

        parent::tearDown();
    }

}
