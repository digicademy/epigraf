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

        $params = $apiService->sanitizeParameters($this->request->getQueryParams());

        if ($this->request->is('post')) {
            $postData = $this->request->getData();
            $params = array_merge($params, $postData);
            $task = $apiService->query($path, $params);

            // Redirect to the status endpoint
            if (!empty($task['task_id'])) {
                $getUrl = [
                    'action' => 'get', $service, $task['task_id'],
                    '?' => $params
                ];
                $this->Answer->redirect($getUrl);
            }
//            else {
//                $error = $task['message'] ?? 'Could not create task.';
//                $this->Answer->error($error);
//            }
        }
        else {
            $task = $apiService->query($path, $params);
        }

        // In proxy mode, pass the response as is
        if ($apiService->proxyMode === 'json') {
            $this->response = $this->response
                ->withType('application/json')
                ->withStringBody(json_encode($task['response'] ?? []))
                ->withStatus($task['status'] ?? 500);

            $this->autoRender = false;
        } else {
            $this->Answer->addAnswer(['service' => $service, 'task' => $task]);
            $this->Answer->addOptions(['params' => $params]);
            $this->render('/Services/get_' . $service);
        }
    }

}
