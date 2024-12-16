<?php

namespace Epi\Test\TestCase;

use Cake\Core\Plugin;
use App\Test\TestCase\AppTestCase;

/**
 * Epi\EpiTestCase\EpiTestCase
 *
 */
class EpiTestCase extends AppTestCase
{
    /**
     * setup method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $reflect = new \ReflectionClass($this);
        $this->_compareBasePath = Plugin::path('Epi') . 'tests' . DS . 'Comparisons' . DS . $reflect->getShortName() . DS;
        $this->comparisonFile = $this->_compareBasePath . $this->getName() .'.php';
        $this->testdataFile = Plugin::path('Epi') .'tests' . DS . 'Testdata' . DS . $reflect->getShortName() . DS .$this->getName() .'.php';
    }
}
