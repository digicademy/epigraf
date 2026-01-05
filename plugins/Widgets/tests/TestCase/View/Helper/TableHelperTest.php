<?php
declare(strict_types=1);

namespace Widgets\Test\TestCase\View\Helper;

use Widgets\View\Helper\ElementHelper;
use Widgets\View\Helper\TableHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use App\Utilities\Converters\Arrays;

/**
 * Widgets\View\Helper\TableHelper Test Case
 */
class TableHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Widgets\View\Helper\TableHelper
     */
    protected $Table;

    /**
     * Setup method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $view = new View();
        $this->Table = new TableHelper($view);
        $this->Table->Element = new ElementHelper($view);
    }

    /**
     * Teardown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Table);

        parent::tearDown();
    }

    public function testCheckboxList(): void {
        // set up comparison data
        $divOne = Arrays::nestedToHtml([
            'div' => [
                'attrs' => ['class' => 'input checkbox'],
                'content' => [
                    'input' => [
                        'attrs' => [
                            'type' => 'hidden',
                            'name' => 'Spicy',
                            'value' => '0'
                        ],
                    ],
                    'label' => [
                        'attrs' => ['for' => 'spicy'],
                        'content' => [
                            'input' => [
                                'attrs' => ['type' => 'checkbox', 'name' => 'Spicy', 'value' => '1', 'checked' => 'checked', 'id' => 'spicy',],
                            ],
                            'Spicy'
                        ],
                        'close' => true
                    ]
                ],
                'close' => true
            ]
        ]);
        $divTwo = Arrays::nestedToHtml([
                'div' => [
                    'attrs' => ['class' => 'input checkbox'],
                    'content' => [
                        'input' => [
                            'attrs' => [
                                'type' => 'hidden',
                                'name' => 'Cooked',
                                'value' => '0'
                            ],
                        ],
                        'label' => [
                            'attrs' => ['for' => 'cooked'],
                            'content' => [
                                'input' => [
                                    'attrs' => ['type' => 'checkbox', 'name' => 'Cooked', 'value' => '1', 'id' => 'cooked'],
                                ],
                                'Cooked'
                            ],
                            'close' => true
                        ]
                    ],
                    'close' => true
                ]
            ]
        );


        $expected = Arrays::nestedToHtml([
                'ul' => [
                    'attrs' => ['class' => ''],
                    'content' => [
                        0 => [
                            'li' => [
                                'attrs' => ['data-value' => 'base.level'],
                                'content' => $divOne,
                                'close' => true
                            ]
                        ],
                        1 => [
                            'li' => [
                                'attrs' => ['data-value' => 'base.method'],
                                'content' => $divTwo,
                                'close' => true
                            ]
                        ]
                    ],
                    'close' => true
                ]
            ]
        );

        // arguments
        $items = [
            [
                'title' => 'Spicy',
                'name' => 'Spicy',
                'value' => 'base.level',
                'checked' => true
            ],
            [
                'title' => 'Cooked',
                'name' => 'Cooked',
                'value' => 'base.method',
                'checked' => false
            ]
        ];

        $this->assertEquals(
            $expected,
            $this->Table->checkboxList($items)
        );
    }
}
