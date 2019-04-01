<?php

declare(strict_types=1);

namespace src\app\http\actions;

use corbomite\flashdata\interfaces\FlashDataApiInterface;
use corbomite\http\exceptions\Http404Exception;
use corbomite\http\exceptions\Http500Exception;
use corbomite\http\interfaces\RequestHelperInterface;
use corbomite\user\interfaces\UserApiInterface;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use src\app\projects\interfaces\ProjectsApiInterface;
use function count;

class ProjectListActions
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var ProjectsApiInterface */
    private $projectsApi;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var RequestHelperInterface */
    private $requestHelper;

    /** @var array */
    private $projectGuids = [];

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        ProjectsApiInterface $projectsApi,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper
    ) {
        $this->userApi       = $userApi;
        $this->response      = $response;
        $this->projectsApi   = $projectsApi;
        $this->flashDataApi  = $flashDataApi;
        $this->requestHelper = $requestHelper;

        $guids = $this->requestHelper->post('projects');

        if (! $guids) {
            return;
        }

        foreach ($guids as $guid) {
            $this->projectGuids[] = $this->projectsApi->uuidToBytes($guid);
        }
    }

    /**
     * @throws Http404Exception
     * @throws Http500Exception
     */
    public function __invoke() : ?ResponseInterface
    {
        if ($this->requestHelper->method() !== 'post') {
            throw new LogicException(
                'Project List Actions requires post request'
            );
        }

        $user = $this->userApi->fetchCurrentUser();

        if (! $user || $user->getExtendedProperty('is_admin') !== 1) {
            throw new Http404Exception();
        }

        if (! $this->projectGuids) {
            throw new Http500Exception('No projects specified');
        }

        $fetchParams = $this->projectsApi->makeQueryModel();
        $fetchParams->addWhere('guid', $this->projectGuids);
        $projects = $this->projectsApi->fetchAll($fetchParams);

        $verb = '';

        foreach ($projects as $project) {
            switch ($this->requestHelper->post('bulk_action')) {
                case 'archive':
                    $verb = 'archived';
                    $this->projectsApi->archive($project);
                    break;
                case 'delete':
                    $verb = 'deleted';
                    $this->projectsApi->delete($project);
                    break;
                case 'unArchive':
                    $verb = 'un-archived';
                    $this->projectsApi->unArchive($project);
                    break;
                default:
                    throw new Http500Exception('Invalid bulk action');
            }
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $singularPlural = count($projects) > 1 ? 'Projects' : 'Project';

        $flashDataModel->dataItem(
            'content',
            $singularPlural . ' ' . $verb . ' successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/projects');

        $response = $response->withStatus(303);

        return $response;
    }
}
