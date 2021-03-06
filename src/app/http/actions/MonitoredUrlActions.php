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
use src\app\monitoredurls\interfaces\MonitoredUrlsApiInterface;
use function count;

class MonitoredUrlActions
{
    /** @var UserApiInterface */
    private $userApi;
    /** @var ResponseInterface */
    private $response;
    /** @var FlashDataApiInterface */
    private $flashDataApi;
    /** @var RequestHelperInterface */
    private $requestHelper;
    /** @var MonitoredUrlsApiInterface */
    private $monitredUrlsApi;

    /** @var array */
    private $guids = [];

    public function __construct(
        UserApiInterface $userApi,
        ResponseInterface $response,
        FlashDataApiInterface $flashDataApi,
        RequestHelperInterface $requestHelper,
        MonitoredUrlsApiInterface $monitredUrlsApi
    ) {
        $this->userApi         = $userApi;
        $this->response        = $response;
        $this->flashDataApi    = $flashDataApi;
        $this->requestHelper   = $requestHelper;
        $this->monitredUrlsApi = $monitredUrlsApi;

        $guids = $this->requestHelper->post('guids');

        if (! $guids) {
            return;
        }

        foreach ($guids as $guid) {
            $this->guids[] = $this->monitredUrlsApi->uuidToBytes($guid);
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

        if (! $this->guids) {
            throw new Http500Exception('No monitored URLs specified');
        }

        $fetchParams = $this->monitredUrlsApi->makeQueryModel();
        $fetchParams->addWhere('guid', $this->guids);
        $models = $this->monitredUrlsApi->fetchAll($fetchParams);

        $verb = '';

        foreach ($models as $model) {
            switch ($this->requestHelper->post('bulk_action')) {
                case 'archive':
                    $verb = 'archived';
                    $this->monitredUrlsApi->archive($model);
                    break;
                case 'delete':
                    $verb = 'deleted';
                    $this->monitredUrlsApi->delete($model);
                    break;
                case 'unArchive':
                    $verb = 'un-archived';
                    $this->monitredUrlsApi->unArchive($model);
                    break;
                default:
                    throw new Http500Exception('Invalid bulk action');
            }
        }

        $flashDataModel = $this->flashDataApi->makeFlashDataModel(['name' => 'Message']);

        $flashDataModel->dataItem('type', 'Success');

        $singularPlural = count($models) > 1 ?
            'Monitored URLs' :
            'Monitored URL';

        $flashDataModel->dataItem(
            'content',
            $singularPlural . ' ' . $verb . ' successfully'
        );

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->flashDataApi->setFlashData($flashDataModel);

        $response = $this->response->withHeader('Location', '/monitored-urls');

        $response = $response->withStatus(303);

        return $response;
    }
}
