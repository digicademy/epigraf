<?php
namespace Snapshot;

use AcceptanceTester;
use App\Utilities\Converters\Arrays;

/**
 * Used to compare data
 *
 * The snapshot class is passed to the test method. Inside you can
 * assert the data conforms to the snapshot by calling:
 *
 * $snapshot->assert();
 *
 * To update snapshots, run in --debug mode.
 */
class Notes extends \Codeception\Snapshot
{
    protected AcceptanceTester $I;

    public function __construct(AcceptanceTester $I)
    {
        $this->I = $I;
        $this->shouldShowDiffOnFail();
    }

    /**
     * Get the snapshot filename
     *
     * @return string
     */
    protected function getFileName(): string
    {
        if (!$this->fileName) {
            $this->fileName = preg_replace('/\W/', '.', get_class($this)) . '.' . $this->extension;
        }
        return codecept_data_dir('snapshots') . DS . $this->fileName;
    }

    public function fetchData($conditions=[]): array|string|false
    {
        $I = $this->I;

        $I->amConnectedToDatabase('test_projects');
        $data = [
            'notes' => $I->grabEntriesFromDatabase('notes',['deleted'=>0] + $conditions),
        ];

        $data = Arrays::array_remove_keys($data, ['created', 'modified']);

        // Make it compatible with codeceptions loading procedure
        // which does not use associative arrays
        if ($this->saveAsJson) {
            $data = json_decode(json_encode($data), true);
        } else {
            $data = json_encode($data, JSON_PRETTY_PRINT);
        }

        return $data;
    }
}
