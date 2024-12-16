<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Command;

use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

use Cake\ORM\TableRegistry;
use App\Model\Table\BaseTable;

/**
 * Tree command class
 */
class TreeCommand extends Command
{

    /**
     * Initialization hook method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
    }

    /**
     * Add parser options
     *
     * @param ConsoleOptionParser $parser
     *
     * @return ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->addArgument('db', [
            'help' => 'Select the project database, e.g. projects_playground.',
            'required' => true
        ]);

        $parser->addArgument('model', [
            'help' => 'Select the model name, e.g. Epi.Properties.',
            'required' => true
        ]);

        return $parser;
    }

    /**
     * Recover trees
     *
     * Example call: bin/cake epi.tree projects_playground Epi.Properties
     * The model must implement getScopes and setScope method and have the TreeBehavior attached.
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     * @return int|void|null
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->out('Recovering trees. Please take a cup of coffee and keep waiting.');

        // Set database
        $db = $args->getArgument('db');
        BaseTable::setDatabase($db);

        // Load model
        $modelname = $args->getArgument('model');
        $table = TableRegistry::getTableLocator()->get($modelname);

        // Recover
        $scopes = $table->getScopes();

        foreach ($scopes as $scope) {
            $io->out('Recovering ' . $scope);
            $table->setScope($scope);
            $table->recover();
        }

        $io->out('Finish your coffee! The trees were recovered.');
    }

}
