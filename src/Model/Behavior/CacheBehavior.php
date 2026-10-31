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

use App\Cache\Cache;
use App\Model\Entity\Databank;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Behavior;

/**
 * Cache behavior
 *
 * Clear the cache after saving new data
 */
class CacheBehavior extends Behavior
{
    /**
     * Default configuration.*
     * @param array $_defaultConfig
     */
    protected $_defaultConfig = [];

    /**
     * Initialize hook
     *
     * @param array $config
     *
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->_config['implementedMethods']['initResultCache'] = 'initResultCache';
        $this->_config['implementedMethods']['clearCache'] = 'clearCache';

        parent::initialize($config);
    }

    /**
     * Clear cache after saving
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param array $options
     * @return void
     */
    public function afterSave(EventInterface $event, EntityInterface $entity, $options = [])
    {
        $this->clearCache();
    }

    /**
     * Initialize the cache config and return its name.
     *
     * @param string $conn The database connection name, default is 'projects'.
     * @return string Name of the cache configuration
     */
    public function initResultCache($conn = 'projects')
    {
        $table = $this->table();
        if (empty($table->_cacheConfigName)) {
            $configName = 'epi_results_' . Databank::addPrefix($table->getDatabaseName($conn)) . '_' . $table->getTable();
            $table->_cacheConfigName = $configName;
            Cache::initCache($configName, 'results');
        }

        if (Configure::read('debug')) {
            Cache::clear($table->_cacheConfigName);
        }

        return $table->_cacheConfigName;
    }

    /**
     * Clear cache
     *
     * @param string $conn
     * @return void
     */
    public function clearCache($conn = 'projects')
    {
        // Result cache
        $cacheConfig = $this->initResultCache($conn);
        Cache::clear($cacheConfig);

        // View cache
        // @var string $configName The configuration name used in the controller ($this->plugin . '_' . $this->name);
        $configName = 'epi_views_Epi_Articles';
        Cache::initCache($configName, 'views');
        Cache::clear($configName);
    }

}
