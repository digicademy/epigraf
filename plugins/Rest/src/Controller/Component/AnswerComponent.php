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

use App\Model\Entity\BaseEntity;
use Cake\Controller\Component;
use Cake\Http\Exception\RedirectException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Routing\Router;
use Cake\Utility\Inflector;
use Laminas\Diactoros\Response\RedirectResponse;
use Rest\Error\Middleware\RestAnswerException;

/**
 * Answer component
 */
class AnswerComponent extends Component
{
    /**
     * Default configuration.
     *
     * @var array
     *
     */
    protected $_defaultConfig = [];

    /**
     * Create a success message
     *
     * Show a flash message and redirect to url in web requests.
     * In API requests, return the message.
     *
     * @param string|bool $message The message
     * @param array|null $url redirect URL (leave empty to only show the message)
     * @param array $data Additional data in API responses
     *
     * @return void
     * @throws RestAnswerException
     */
    public function success($message = false, $url = null, $data = [])
    {
        $request = $this->getController()->getRequest();
        if (($message !== false) && !$request->is('api')) {
            $this->getController()->Flash->success($message);
        }

        // Redirect or API response
        if (!empty($url)) {
            throw new RestAnswerException($url, true, $message, $data, false);
        }
        else {
            $data['success'] = true;
            $data['message'] = $message;

            $this->addAnswer(['status' => $data]);
        }
    }

    /**
     * Create an error message
     *
     * Show a flash message and redirect to url in web requests.
     * In API requests, return the message.
     *
     * @param string|bool $message The message
     * @param array $url redirect URL (leave empty to only show the message)
     * @param array $data Additional data in API responses
     *
     * @return void
     * @throws RestAnswerException
     */
    public function error($message = false, $url = [], $data = [])
    {
        $request = $this->getController()->getRequest();
        if (($message !== false) && !$request->is('api')) {
            $this->getController()->Flash->error($message);
        }

        // Redirect or API response
        if (!empty($url)) {
            throw new RestAnswerException($url, false, $message, $data);
        }
        else {
            $data['success'] = false;
            $data['message'] = $message;

            //TODO: use 'error' instead of 'status'?
            //TODO: set HTTP status code?
            $this->addAnswer(['status' => $data]);
        }
    }

    /**
     * Create a redirect without message
     *
     * @param $url
     *
     * @return RedirectResponse|mixed redirect without message, regardless whether web or api request
     */
    public function redirect($url = [])
    {
        throw new RedirectException(
            Router::url($url, true)
        );
    }

    /**
     * Redirect to login
     *
     * @param $msg
     * @return void
     */
    public function redirectToLogin($msg = null)
    {
        if ($this->getController()->getRequest()->is('api')) {
            throw new UnauthorizedException($msg);
        }

        if (!empty($this->getController()->Auth->user())) {
            $msg = $msg ?? __('You have no access to the requested page.');
        } else {
            $msg = $msg ?? __('Please log in to access this location.');
        }
        $this->error($msg, $this->getController()->getLoginUrl());
    }

    /**
     * Add response data that will be rendered in view templates and view classes
     *
     * @param array $data
     * @return void
     */
    public function addAnswer($data)
    {
        $serialize = $this->getController()->viewBuilder()->getOption('serialize') ?? [];

        if (is_array($serialize)) {

            // Rename entity key (based on table name of the entity)
            $keys = array_keys($data);
            if (isset($data['entity']) && ($data['entity'] instanceof BaseEntity)) {
                $aliases = $keys;
                $index = array_search('entity', $aliases);
                $aliases[$index] = $data['entity']->getEntityName();
                $keys = array_combine($aliases, $keys);
            }

            // Rename entities key (based on modelClass of the controller)
            if (isset($data['entities'])) {
                $aliases = $keys;
                $index = array_search('entities', $aliases);
                $alias = explode('.', $this->getController()->modelClass);
                $aliases[$index] = Inflector::tableize(end($alias));
                $keys = array_combine($aliases, $keys);
            }

            $serialize = array_merge($serialize, $keys);
            $this->getController()->viewBuilder()->setOption('serialize', $serialize);
        }

        $this->getController()->set($data);
    }

    /**
     * Set all options
     *
     * In the templates, options can be retrieved using
     * $this->getConfig('options').
     *
     * @param array $data List of data objects
     * @return void
     */
    public function setOptions($data)
    {
        $this->getController()->viewBuilder()->setOption('options', $data);
    }

    /**
     * Add options to the existing options
     *
     * In the templates, options can be retrieved using
     * $this->getConfig('options').
     *
     * @param array $data List of data objects
     * @return void
     */
    public function addOptions($data)
    {
        $options = $this->getController()->viewBuilder()->getOption('options') ?? [];
        $options = array_merge($options, $data);
        $this->getController()->viewBuilder()->setOption('options', $options);
    }

}
