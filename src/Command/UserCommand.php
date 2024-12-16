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

use App\Model\Table\UsersTable;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;

/**
 * Manage user accounts
 *
 * Example to add a devel user:
 * ```
 * bin/cake user add devel devel devel devel
 * ```
 *
 * @property UsersTable $Users
 */
class UserCommand extends Command
{

    /**
     * IO variable
     *
     * @var null
     */
    public $io = null;

    /**
     *  Models
     */
    public $Users = null;

    /**
     * Initialization hook method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        $this->Users = $this->fetchTable('Users');
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
            'help' => 'Action.',
            'choices' => ['add', 'remove'],
            'required' => true
        ]);

        $parser->addArgument('username', [
            'help' => 'Username',
            'required' => true
        ]);

        $parser->addArgument('password', [
            'help' => 'Password'
        ]);

        $parser->addArgument('role', [
            'help' => 'Role'
        ]);

        $parser->addArgument('accesstoken', [
            'help' => 'Access token for API access'
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
            $this->addUser(
                $args->getArgument('username'),
                $args->getArgument('role'),
                $args->getArgument('password'),
                $args->getArgument('accesstoken')
            );
        }
        elseif ($action == 'remove') {
            $this->removeUser(
                $args->getArgument('username')
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
    public function addUser($username = false, $role = false, $password = false, $accesstoken = null)
    {
        if (empty($username)) {
            return $this->io->abort('Username is missing', 1);
        }

        if (empty($role)) {
            return $this->io->abort('Role is missing', 2);
        }

        if (empty($password)) {
            return $this->io->abort('Password is missing', 3);
        }

        $user = $this->Users->newEntity([]);
        $user->setAccess(['role', 'username', 'norm_iri', 'accesstoken'], true);
        $user = $this->Users->patchEntity($user, [
            'username' => $username,
            'password' => $password,
            'role' => $role,
            'accesstoken' => $accesstoken,
            'contact' => 'Automatically created user'
        ]);

        if ($this->Users->save($user)) {
            $this->io->out(__("The user {0} has been created.", $username));
        }
        else {
            $this->io->out(__("The user {0} could not be created. Maybe the user is already present in the database.",
                $username));
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

