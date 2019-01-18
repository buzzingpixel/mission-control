<?php
declare(strict_types=1);

namespace src\app\http\actions;

use LogicException;
use Psr\Http\Message\ResponseInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\http\exceptions\Http500Exception;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\http\interfaces\RequestHelperInterface;
use src\app\projects\interfaces\ProjectsApiInterface;
use corbomite\flashdata\interfaces\FlashDataApiInterface;

class ProjectListActions
{
    private $userApi;
    private $response;
    private $projectsApi;
    private $flashDataApi;
    private $requestHelper;

    private $projectGuids = [];

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        ProjectsApiInterface $projectsApi,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi = $userApi;
        $this->response = $response;
        $this->projectsApi = $projectsApi;
        $this->flashDataApi = $flashDataApi;
        $this->requestHelper = $requestHelper;

        $this->projectGuids = $this->requestHelper->post('projects');
    }

    /**
     * @throws Http404Exception
     * @throws Http500Exception
     */
    public function __invoke(): ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Project List Actions requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || ! $user->userDataItem('admin')) {
            throw new Http404Exception();
        }

        if (! $this->projectGuids) {
            throw new Http500Exception('No projects specified');
        }

        $fetchParams = $this->projectsApi->createFetchDataParams();
        $fetchParams->addWhere('guid', $this->projectGuids);
        $projects = $this->projectsApi->fetchProjects($fetchParams);

        $verb = '';

        foreach ($projects as $project) {
            switch ($this->requestHelper->post('bulk_action')) {
                case 'archive':
                    $verb = 'archived';
                    $this->projectsApi->archiveProject($project);
                    break;
                case 'delete':
                    $verb = 'deleted';
                    $this->projectsApi->deleteProject($project);
                    break;
                default:
                    throw new Http500Exception('Invalid bulk action');
            }
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel([
            'name' => 'Message'
        ]);

        $flashDataModel->dataItem('type', 'Success');

        $flashDataModel->dataItem(
            'content',
            'Projects "' . $verb . '" successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/projects');

        $response = $response->withStatus(303);

        return $response;
    }
}
