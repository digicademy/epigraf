<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Model\Behavior;

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;

/**
 * Modifier behavior
 *
 * Saves the current user's ID to the created_by and modified_by fields
 */
class ModifierBehavior extends Behavior
{
    /**
     * Default configuration.*
     *
     * @var array Set the user key to the static table property that contains the user ID
     */
    protected $_defaultConfig = ['user' => null];

    /**
     * Initialize hook
     *
     * @param array $config
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
    }

    /**
     * Save user ID
     *
     * @param Event $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     */
    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        $userIdProperty = $this->getConfig('user');
        if (empty($userIdProperty)) {
            return;
        }

        $userId = $this->table()::$$userIdProperty;
        if (empty($userId)) {
            return;
        }

        if ($entity->isNew()) {
            $entity->created_by = $userId;
        }

        $entity->modified_by = $userId;
    }

}
