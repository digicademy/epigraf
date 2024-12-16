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

use App\Model\Table\DatabanksTable;
use App\Model\Table\PermissionsTable;
use App\Model\Table\UsersTable;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Manage user permissions.
 *
 * Example to grant all api permissions to the devel user:
 * ```
 * bin/cake permission add devel api "*" "*"
 * ```
 *
 * @property PermissionsTable $Permissions
 * @property UsersTable $Users
 * @property DatabanksTable $Databanks
 */
class PermissionCommand extends Command
{

    /**
     * IO variable
     *
     * @var null
     */
    public $io = null;

    /**
     * Models
     */
    protected $Permissions = null;
    protected $Users = null;
    protected $Databanks = null;

    /**
     * Initialization hook method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Permissions = $this->fetchTable('Permissions');
        $this->Users = $this->fetchTable('Users');
        $this->Databanks = $this->fetchTable('Databanks');
    }

    /**
     * Build option parser
     *
     * @param ConsoleOptionParser $parser
     *
     * @return ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser->addArgument('action', [
            'help' => 'Action',
            'choices' => ['add'],
            'required' => true
        ]);

        $parser->addArgument('username', [
            'help' => 'Username',
            'required' => true
        ]);

        $parser->addArgument('request', [
            'help' => 'Request type (api or web)'
        ]);

        $parser->addArgument('name', [
            'help' => 'Endpoint name or * for all endpoints'
        ]);

        $parser->addArgument('database', [
            'help' => 'Database name or * for all databases'
        ]);

        return $parser;
    }

    /**
     * Initialize action-depending data and directories
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

        if ($action == 'add') {
            $this->addUserPermission(
                $args->getArgument('username'),
                $args->getArgument('request'),
                $args->getArgument('name'),
                $args->getArgument('database')
            );
        }
    }

    /**
     * Add a new user
     *
     * @param $username
     * @param $password
     * @param $role
     *
     * @return void
     */
    public function addUserPermission($username = false, $request = false, $name = false, $database = null)
    {
        if (empty($username)) {
            return $this->io->abort('Username is missing', 1);
        }

        if (empty($request)) {
            return $this->io->abort('Request type is missing', 2);
        }

        if (empty($name)) {
            return $this->io->abort('Endpoint name is missing', 3);
        }

        if (empty($database)) {
            return $this->io->abort('Database name is missing', 4);
        }

        $userid = $this->Users->findByUsername($username)->first()->id;


        $permission = $this->Permissions->newEntity([]);
        $permission = $this->Permissions->patchEntity($permission, [
            'user_id' => $userid,
            'user_request' => $request,
            'permission_type' => 'access',
            'permission_name' => $name,
            'entity_type' => 'databank',
            'entity_name' => $database
        ]);

        if ($this->Permissions->save($permission)) {
            $this->io->out(__("The permission {0} has been granted to {1}.", $name, $username));
        }
        else {
            $this->io->out(__("The permission {0} could not granted to {1}.", $name, $username));
        }
    }

    /**
     * Remove a user
     *
     * @param $username
     *
     * @return void
     */
    public function removeUser($username = false)
    {
        if (empty($username)) {
            return $this->io->abort('Username is missing', 1);
        }

        $query = $this->Users->findByUsername($username);
        $user = $query->first();

        if (!$user) {
            $this->io->out(__('The user does not exist.'));
        }
        elseif ($this->Users->delete($user)) {
            $this->io->out(__('The user has been removed.'));
        }
        else {
            $this->io->out(__('The user could not be removed. Please, try again.'));
        }
    }

}

