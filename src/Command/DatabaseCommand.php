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

use App\Model\Entity\Databank;
use App\Model\Table\DatabanksTable;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;
use Cake\Http\Exception\NotFoundException;
use DirectoryIterator;
use Epi\Model\Table\BaseTable;
use PDOException;

class DatabaseCommand extends Command
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
                "init = Create blank databases.\n" .
                "import = Import sql files using filename or folder and pattern.\n" .
                "fixfolders = fix database folders.\n"
            ,
            'choices' => ['init', 'import', 'fixfolders'],
            'required' => true
        ]);

        $parser->addOption('database', [
            'help' => 'Database name'
        ]);
        $parser->addOption('connection', [
            'help' => 'Connection name (default for main database, projects for project databases)'
        ]);
        $parser->addOption('drop', [
            'help' => 'Whether to drop existing databases. Defaults to false.',
            'boolean' => true
        ]);
        $parser->addOption('preset', [
            'help' => 'Create a database from the preset folder: The preset name.'
        ]);

        $parser->addOption('filename', [
            'help' => 'SQL file. The filename should match the database name.'
        ]);
        $parser->addOption('folder', [
            'help' => 'Folder containing SQL dumps. Filenames should match the database name.'
        ]);
        $parser->addOption('pattern', [
            'help' => 'Regex matching files in the folder. Defaults to "/\.sql\.gz$/".'
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

        if ($action == 'init') {

            $databasename = $args->getOption('database');
            $connectionname = $args->getOption('connection') ?? 'default';

            // TODO: check whether the database already exists and stop if drop is false
            $drop = $args->getOption('drop') ?? false;

            $preset = $args->getOption('preset');
            if (empty($preset)) {
                $filename = $args->getOption('filename') ?? (ROOT . DS . 'config/schema/epigraf.sql');

                $this->createDatabase($connectionname, $databasename, $drop);
                $this->importSql($connectionname, $databasename, $filename);

                $this->createDatabase('test');
                $this->createDatabase('test_projects');
                $this->createDatabase('test_public');

                $this->createFolders();

            }
            else {
                $this->createDatabase($connectionname, $databasename, $drop);
                $this->importPreset($databasename, $preset);
            }
        }

        elseif ($action == 'import') {

            $databasename = $args->getOption('database');
            $connectionname = $args->getOption('connection') ?? 'default';
            $filename = $args->getOption('filename');

            $folder = $args->getOption('folder');
            $pattern = $args->getOption('pattern');
            $filenames = [];

            // Get list of files
            if (!empty($folder)) {
                $directory = new DirectoryIterator($folder);
                foreach ($directory as $fileInfo) {
                    if ($fileInfo->isFile() && preg_match($pattern ?? '/\.sql\.gz$/', $fileInfo->getFilename())) {
                        $filenames[] = $fileInfo->getPathname();
                    }
                }
            }
            else {
                $filenames[] = $filename;
            }

            if (empty($filenames)) {
                $this->io->abort('No files found.', 1);
            }

            // Process files
            foreach ($filenames as $file) {
                $db = $databasename ?? preg_replace("/\..*$/", "", basename($file));

                // TODO: check whether the database already exists and stop if drop is false
                $drop = $args->getOption('drop') ?? false;

                $this->createDatabase($connectionname, $db, $drop);
                $this->importSql($connectionname, $db, $file);
            }

            $this->createFolders();

            $roles = $args->getOption('roles') ?? ['devel'];
            $password = $args->getOption('password') ?? null;
        }

        // Create folders
        elseif ($action == 'fixfolders') {
            $this->createFolders();

        }
        else {
            $io->out("Unknown action {$action}.");
        }

    }

    /**
     * Create default directories for all databases
     *
     * @return void
     */
    protected function createFolders()
    {
        // Create default folders
        $mounts = Configure::read('Data') ?? [];
        foreach ($mounts as $mount) {
            if (!is_dir($mount)) {
                $this->io->out("Created missing folder {$mount}.");
                mkdir($mount, 0700, true);
            }
        }

        /** @var Databank[] $databanks */
        $databanks = $this->fetchTable('Databanks')->find('all');
        foreach ($databanks as $databank) {
            $result = $databank->createFolders();
            if (!empty($result['created'])) {
                $this->io->out("{$result['created']} folders of {$result['missing']} missing folders created for database {$databank->name}.");
            }
        }
    }

    /**
     * Create a database
     *
     * Creates a SQL database.
     * Additionally, for connections to project databases or database names prefixed by "epi_",
     * a databank record will be created.
     *
     * @param string $connectionname For the main database, use 'default'.
     *                               For project databases use 'projects'.
     * @param string $databasename Leave empty to use the database name from the configuration in app.php.
     * @param boolean $drop
     *
     * @return void
     */
    protected function createDatabase($connectionname, $databasename = null, $drop = false)
    {

        $config = ConnectionManager::getConfig($connectionname);
        if (empty($config)) {
            $this->io->out("Skipping the connection {$connectionname}.");
            return;
        }

        $databasename = $databasename ?? $config['database'];

        try {
            $conn = BaseTable::setDatabase(false, $connectionname, false);

            if ($drop) {
                $this->io->out("Drop database {$databasename}.");
                $conn->execute('DROP DATABASE IF EXISTS ' . $databasename);
            }
            $this->io->out("Create database {$databasename}.");

            $conn->execute('CREATE DATABASE IF NOT EXISTS ' . $databasename . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

            if (($connectionname == 'projects') || str_starts_with($databasename, 'epi_')) {
                BaseTable::setDatabase($config['database'], $connectionname, false);
                $this->createDatabaseRecord($databasename);
            }
        } catch (PDOException $e) {
            $this->io->out($e->getMessage());
        }

        BaseTable::setDatabase($config['database'], $connectionname, false);
    }

    /**
     * Create a project database record
     *
     * @param string $databasename
     * @return void
     */
    protected function createDatabaseRecord($databasename)
    {
        /** @var DatabanksTable $databanks */
        $databanks = $this->fetchTable('Databanks');
        /** @var  Databank $databank */
        $databank = $databanks->find('all')->where(['name' => $databasename])->first();

        if (empty($databank)) {
            $databank = $databanks->newEntity(['name' => $databasename, 'version' => DATABASE_CURRENT_VERSION]);

            if ($databanks->save($databank)) {
                $this->io->out("Project database {$databasename} added.");
            }
            else {
                $this->io->out("Project database {$databasename} could not be added.");
                $databank = null;
            }
        }

        if (!empty($databank)) {
            $databank->createFolders();
        }
    }

    /**
     * Populate a database from a preset
     *
     * @param string $databasename
     * @param string $preset
     * @return void
     */
    protected function importPreset($databasename, $preset)
    {
        /** @var DatabanksTable $databanks */
        $databanks = $this->fetchTable('Databanks');
        /** @var  Databank $databank */
        $databank = $databanks->find('all')->where(['name' => $databasename])->first();

        if (!empty($databank)) {
            $databank->preset = $preset;

            if ($databank->initDatabase()) {
                $this->io->out("The preset {$preset} was imported into {$databasename}.");
            }
            else {
                $this->io->out("The preset {$preset} could not be imported into {$databasename}.");
            }
        }
        else {
            $this->io->out("The database record  for {$databasename} does not exist.");
        }
    }


    /**
     * Import an sql dump
     *
     * @param string $connectionname
     * @param string $databasename
     * @param string $filename
     *
     * @return false|void
     */
    protected function importSql($connectionname, $databasename = null, $filename = null)
    {

        try {
            if (!file_exists($filename)) {
                throw new NotFoundException('The file ' . $filename . 'could not be found.');
            }

            // Get database connection
            $config = ConnectionManager::getConfig($connectionname);
            $databasename = $databasename ?? $config['database'];
            $conn = BaseTable::setDatabase($databasename, $connectionname, false);

            $this->io->out("Import sql file {$filename} into {$databasename}.");

            $lineCount = 0;
            try {
                $conn->getDriver()->connect();

                $filehandle = gzopen($filename, 'rb');
                try {
                    $query = '';

                    // Loop through each line
                    while (!gzeof($filehandle)) //foreach ($lines as $line)
                    {
                        $line = gzgets($filehandle);

                        // Skip it if it's a comment
                        if (substr($line, 0, 2) == '--' || $line == '') {
                            continue;
                        }

                        // Add this line to the current segment
                        $query .= $line;

                        // If it has a semicolon at the end, it's the end of the query
                        if (substr(trim($line), -1, 1) == ';') {
                            $query = trim($query);

                            // Skip DATABASE commands
                            if (
                                (substr(strtoupper($query), 0, 16) !== 'CREATE DATABASE ') &&
                                (substr(strtoupper($query), 0, 4) !== 'USE ')
                            ) {
                                // Perform the query
                                $status = $conn->execute($query);
                                if (!$status) {
                                    return false;
                                }
                                $lineCount++;
                            }

                            $query = '';
                        }

                    }
                } finally {
                    gzclose($filehandle);
                }

            } catch (PDOException $e) {
                return false;
            }

            $this->io->out("Import finished with {$lineCount} queries into {$databasename}.");

        } catch (PDOException $e) {
            $this->io->out($e->getMessage());
        }

        // Restore database connection
        BaseTable::setDatabase($config['database'], $connectionname, false);
    }
}
