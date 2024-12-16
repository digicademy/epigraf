<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Command;

use App\Console\Installer;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

class SetupCommand extends Command
{
    /**
     * IO
     *
     * @var null
     */
    public $io = null;

    /**
     * Initial setting of parser options
     *
     * @param ConsoleOptionParser $parser
     *
     * @return ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->addArgument('action', [
            'help' =>
                "tmp = Init temporary folders.\n"
            ,
            'choices' => ['tmp'],
            'required' => true
        ]);

        return $parser;
    }

    /**
     * Execute the command
     *
     * @param Arguments $args
     * @param ConsoleIo $io
     *
     * @return int|void|null
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->io = $io;
        $action = $args->getArgument('action');

        if ($action == 'tmp') {
            Installer::createWritableDirectories(TMP);
            $io->out("Created tmp folders in " . TMP);
        }
        else {
            $io->out("Unknown action {$action}.");
        }
    }

}
