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

namespace Epi\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Epi\Model\Table\TokenTable;
use Epi\Test\TestCase\EpiTestCase;

/**
 * Epi\Model\Table\TokenTable Test Case
 */
class TokenTableTest extends EpiTestCase
{
    /**
     * Test subject
     *
     * @var \Epi\Model\Table\TokenTable
     */
    public $Token;

    /**
     * Fixtures
     *
     * @var array
     */
	public $fixtures = [
		'plugin.Epi.Token'
	];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = TableRegistry::getTableLocator()->exists('Token') ? [] : ['className' => TokenTable::class];
        $this->Token = $this->fetchTable('Token', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->TokenTable);
        parent::tearDown();
    }


	/**
	 * Test hasSessionToken method
	 *
	 * @return void
	 */
	public function testHasSessionTokenTrue()
	{
		$hasSessionToken = $this->Token->hasSessionToken('TESTTOKENAUTHOR');
		$this->assertequals(true, $hasSessionToken);
	}


	/**
	 * Test hasSessionToken method (if usertoken is empty)
	 *
	 * @return void
	 */
	public function testHasSessionTokenFalse()
	{
		$token = $this->Token->get(1);
		$data = ['usertoken' => null];
		$this->Token->patchEntity($token, $data);
		$this->Token->save($token);

		$hasSessionToken = $this->Token->hasSessionToken('TESTTOKENAUTHOR');
		$this->assertEquals(false, $hasSessionToken);
	}

	/**
	 * Test hasSessionToken method (if sessiontoken is expired)
	 *
	 * @return void
	 */
	public function testHasSessionTokenFalseExpired()
	{
		$this->markTestIncomplete('Not implemented yet.');
	}

	/**
	 * Test updateSessionToken method
	 *
	 * @MM: Nochmal überprüfen, warum ist die Zeit die gleiche?? (Nachricht an selbst)
	 * (Vielleicht weil der Test zu schnell ausgeführt wird)
	 * -> UPDATE: Da beim Abfragen der Zeit immer ein neues Time-Object erzeugt wird,
	 * einfach eine Sekunde davon abziehen, dann vergleichen (Problem war, dass die kleinste Zeiteinheit
	 * darin Sekunden sind, aber die Unterschiede vmtl. im Millisekundenbereich liegen)
	 *
	 * @return void
	 */
	public function testUpdateSessionToken()
	{
//		Get time of last modification (= Time-Object of current time)
//		and subtract 1 second from object
		$token = $this->Token->get(1);
		$timeBeforeModified = $token['modified']->subSeconds(1);
//		Update token
		$this->Token->updateSessionToken(1);
//		Get token again
		$modifiedToken = $this->Token->get(1);
		$timeAfterModify = $modifiedToken['modified'];
//		Check if not equal -> update has worked
		$this->assertNotEquals($timeBeforeModified, $timeAfterModify);
	}


	/**
	 * Test deleteSessionToken method (if token exists and is deleted)
	 *
	 * @return void
	 */
	public function testDeleteSessionTokenTrue()
	{
		$deleteSessionToken = $this->Token->deleteSessionToken('TESTTOKENAUTHOR');
		$this->assertequals(true, $deleteSessionToken);
		$isDeleted = !($this->Token->exists(['id' => 1]));
		$this->assertEquals(true, $isDeleted);
	}


	/**
	 * Test deleteSessionToken method (if token does not exist)
	 *
	 * @return void
	 */
	public function testDeleteSessionTokenFalse()
	{
		$deleteSessionToken = $this->Token->deleteSessionToken('SECONDTOKEN');
		$this->assertequals(false, $deleteSessionToken);
		$isExistent = $this->Token->exists(['usertoken' => 'SECONDTOKEN']);
		$this->assertEquals(false, $isExistent);
	}

}
