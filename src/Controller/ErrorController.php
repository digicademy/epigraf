<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         3.3.4
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

namespace App\Controller;

/**
 * Error Handling Controller
 *
 * Controller used by WebExceptionRenderer to render error responses.
 */
class ErrorController extends AppController
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
     * beforeFilter callback
     *
     * @param \Cake\Event\EventInterface $event
     *
     * @return \Cake\Http\Response|void|null
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        // Set menu
        parent::beforeFilter($event);
        $this->layout = 'default';
    }

    /**
     * beforeRender callback
     *
     * @param \Cake\Event\EventInterface $event
     *
     * @return \Cake\Http\Response|void|null
     */
    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        if ($this->request->is('api')) {
            $errorVars = array_intersect_key($this->viewBuilder()->getVars(), ['message' => true, 'code' => true]);
            if ($this->request->getParam('_ext') === 'csv') {
                $errorVars = [$errorVars];
            }
            $this->set(['error' => $errorVars]);

            $this->viewBuilder()->setOption('serialize', ['error']);
        }

        parent::beforeRender($event);
        $this->viewBuilder()->setTemplatePath('Error');
    }

    /**
     * Callback afterFilter
     *
     * @param \Cake\Event\EventInterface $event
     *
     * @return \Cake\Http\Response|void|null
     */
    public function afterFilter(\Cake\Event\EventInterface $event)
    {
        parent::afterFilter($event);
    }
}
