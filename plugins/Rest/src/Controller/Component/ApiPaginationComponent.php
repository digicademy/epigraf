<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */
declare(strict_types=1);

namespace Rest\Controller\Component;

use Cake\Event\Event;

/**
 * Extends the ApiPaginationComponent from the bcrowe/cakephp-api-pagination plugin
 * to support all types of Epigraf API requests.
 *
 */
class ApiPaginationComponent extends \BryanCrowe\ApiPagination\Controller\Component\ApiPaginationComponent
{
   /**
     * Checks whether the current request is an API request with pagination.
     *
     * @return bool True if API request with paging, otherwise false.
     */
    protected function isPaginatedApiRequest()
    {
        if (
            $this->getController()->getRequest()->getAttribute('paging')
            && $this->getController()->getRequest()->is('api')
        ) {
            return true;
        }

        return false;
    }


    /**
     * Injects the pagination info into the response if the current request is a
     * JSON or XML request with pagination.
     *
     * @param  \Cake\Event\Event $event The Controller.beforeRender event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        if (!$this->isPaginatedApiRequest()) {
            return;
        }

        $subject = $event->getSubject();

        $paging = $this->getController()->getRequest()->getAttribute('paging');

        $modelName = ucfirst($this->getConfig('model', $subject->getName()));
        $this->pagingInfo = $paging[$modelName] ?? $paging[$subject->modelClass] ?? $paging[0];

        $config = $this->getConfig();

        if (!empty($config['aliases'])) {
            $this->setAliases();
        }

        if (!empty($config['visible'])) {
            $this->setVisibility();
        }

        $subject->set($config['key'], $this->pagingInfo);
        $data = $subject->viewBuilder()->getOption('serialize') ?? [];

        if (is_array($data)) {
            $data[] = $config['key'];
            $subject->viewBuilder()->setOption('serialize', $data);
        }
    }

}
