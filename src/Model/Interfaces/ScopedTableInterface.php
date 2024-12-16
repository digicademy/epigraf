<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 */

namespace App\Model\Interfaces;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Table;

/**
 * Interface that scoped tables must implement
 *
 */
interface ScopedTableInterface
{

    /**
     * Get all scopes
     *
     * @return array|false
     */
    public function getScopes(): array;

    /**
     * Set current scope
     *
     * @param string|EntityInterface $scope Scope value or an entity from which the scope can be derived
     * @return Table The table itself
     */
    public function setScope($scope): Table;

    /**
     * Get current scope
     *
     * @param string $scope
     */
    public function getScope(): string;

    /**
     * Remove current scope
     *
     * @return Table The table itself
     */
    public function removeScope(): Table;

}
