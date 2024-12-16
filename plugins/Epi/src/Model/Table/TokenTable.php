<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace Epi\Model\Table;

use Cake\I18n\FrozenTime;

/**
 * Token table
 */
class TokenTable extends BaseTable
{

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

        $this->setTable('token');
        $this->setDisplayField('usertoken');
        $this->setPrimaryKey('id');
    }

    /**
     * Check existence of session token
     *
     * @param $usertoken
     * @return bool
     */
    public function hasSessionToken($usertoken)
    {
        if (empty($usertoken)) {
            return false;
        }

        $now = FrozenTime::now()->subMinutes(15);
        $this->deleteAll(['modified <' => $now]);

        return $this->find('all')->where(['usertoken' => $usertoken])->count() > 0;
    }

    /**
     * Update session token
     *
     * @param $usertoken
     * @return \Cake\Database\StatementInterface|false
     */
    public function updateSessionToken($usertoken)
    {
        if (empty($usertoken)) {
            return false;
        }

        $now = FrozenTime::now();
        return $this->updateQuery()
            ->update()
            ->set(['modified' => $now])
            ->where(['usertoken' => $usertoken])
            ->execute();
    }

    /**
     * Delete session token
     *
     * @param $usertoken
     * @return false|int
     */
    public function deleteSessionToken($usertoken)
    {
        if (empty($usertoken)) {
            return false;
        }

        return $this->deleteAll(['usertoken' => $usertoken]);
    }


}
