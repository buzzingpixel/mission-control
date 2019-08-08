<?php

declare(strict_types=1);

namespace src\app\users;

use corbomite\db\interfaces\QueryModelInterface;
use corbomite\user\interfaces\UserApiInterface;
use corbomite\user\interfaces\UserModelInterface;

class AdditionalUserActionsService
{
    /** @var UserApiInterface */
    private $userApi;

    public function __construct(UserApiInterface $userApi)
    {
        $this->userApi = $userApi;
    }

    /**
     * @return UserModelInterface[]
     */
    public function fetchAsSelectArray(?QueryModelInterface $params = null) : array
    {
        $users = $this->userApi->fetchAll($params);

        $items = [];

        foreach ($users as $user) {
            $items[$user->guid()] = $user->emailAddress();
        }

        return $items;
    }
}
