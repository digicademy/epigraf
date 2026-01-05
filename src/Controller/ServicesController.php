<?php
/**
 * Epigraf 5.0
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

namespace App\Controller;

use App\Datasource\Services\ServiceFactory;
use App\Model\Table\PermissionsTable;

/**
 * Services Controller
 *
 * Manages requests to external services and APIs
 *
 */
class ServicesController extends AppController
{

    /**
     * Access permissions
     *
     * @var array[] $authorized
     */
    public $authorized = [
        'web' => [
            'reader' => [],
            'coder' => [],
            'desktop' => [],
            'author' => [],
            'editor' => []
        ]
    ];

    public $help = 'configuration';

    /**
     * Retrieve the result of a service
     *
     * @param string $service The service to be called
     * @param string|null $path The endpoint or task id passed to the service
     * @return void
     * @throws \Exception
     */
    public function get(string $service, $path = null): void
    {
        $apiService = ServiceFactory::create($service);

        // Merge get and post data
        $data = $apiService->sanitizeParameters(
            array_merge(
                $this->request->getQueryParams(),
                $this->request->getData() ?? []
            )
        );

        if ($this->request->is('post')) {
            $task = $apiService->query($path, $data);

            // Redirect to the status endpoint
            // For the LLM service, parameters are added by the postProcess() method.
            if (!empty($task['task_id'])) {
                $getUrl = [
                    'action' => 'get', $service, $task['task_id'],
                    '?' => $task['params'] ?? []
                ];
                $this->Answer->redirect($getUrl);
            }
        }
        else {
            $task = $apiService->query($path, $data);
        }

        // In proxy mode, pass the response as is
        if ($apiService->proxyMode === 'file') {
            $this->response = $this->response
                ->withType($task['type'])
                ->withStringBody($task['response'])
                ->withStatus($task['status'] ?? 500);

            $this->autoRender = false;
        }
        elseif ($apiService->proxyMode === 'json') {
            $this->response = $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($task['response'] ?? []))
                ->withStatus($task['status'] ?? 500);

            $this->autoRender = false;
        } else {
            $this->Answer->addAnswer(['service' => $service, 'task' => $task]);
            $this->Answer->addOptions(['params' => $data]);
            $this->render('/Services/get_' . $service);
        }
    }


    /**
     * Check access to specific services
     *
     * To allow access to a service, add a permission with the entity name set to the service name.
     *
     * @param array|\ArrayAccess|null $user
     * @return bool
     */
    public function isAuthorized($user)
    {
        $action = $this->request->getParam('action');
        if (!in_array($user['role'] ?? '', ['admin', 'devel']) && ($action === 'get')) {
            $passedParams = $this->request->getParam('pass', []);
            $service = $passedParams[0] ?? null;

            if (empty($service)) {
                return false;
            }

            return PermissionsTable::hasGrantedPermission(
                $user,
                null,
                $this->request->getParam('controller'),
                $this->request->getParam('action'),
                $this->_getRequestScope(),
                ['entity_name' => $service]
            );
        }

        return parent::isAuthorized($user);
    }
}
