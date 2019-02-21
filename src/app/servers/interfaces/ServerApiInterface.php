<?php
declare(strict_types=1);

namespace src\app\servers\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\servers\exceptions\TitleNotUniqueException;
use src\app\servers\exceptions\InvalidServerModelException;
use src\app\servers\exceptions\InvalidSSHKeyModelException;

interface ServerApiInterface
{
    /**
     * Creates a Server Model
     * @return ServerModelInterface
     */
    public function createModel(): ServerModelInterface;

    /**
     * Creates an SSH Key Model
     * @return SSHKeyModelInterface
     */
    public function createSShKeyModel(): SSHKeyModelInterface;

    /**
     * Converts a UUID to bytes for database queries
     * @param string $string
     * @return string
     */
    public function uuidToBytes(string $string): string;

    /**
     * Creates a Fetch Data Params instance
     * @return QueryModelInterface
     */
    public function makeQueryModel(): QueryModelInterface;

    /**
     * Saves a Server Model
     * @param ServerModelInterface $serverModel
     * @throws InvalidServerModelException
     * @throws TitleNotUniqueException
     */
    public function save(ServerModelInterface $model);

    /**
     * Saves an SSH Key Model
     * @param SSHKeyModelInterface $serverModel
     * @throws InvalidSSHKeyModelException
     * @throws TitleNotUniqueException
     */
    public function saveSSHKey(SSHKeyModelInterface $model);

    /**
     * Archives a server
     * @param ServerModelInterface $model
     */
    public function archive(ServerModelInterface $model);

    /**
     * Archives a server
     * @param SSHKeyModelInterface $model
     */
    public function archiveSSHKey(SSHKeyModelInterface $model);

    /**
     * Un-archives a server
     * @param ServerModelInterface $model
     */
    public function unArchive(ServerModelInterface $model);

    /**
     * Un-archives an ssh key
     * @param SSHKeyModelInterface $model
     */
    public function unArchiveSSHKey(SSHKeyModelInterface $model);

    /**
     * Deletes a server
     * @param ServerModelInterface $model
     */
    public function delete(ServerModelInterface $model);

    /**
     * Deletes an ssh key
     * @param SSHKeyModelInterface $model
     */
    public function deleteSSHKey(SSHKeyModelInterface $model);

    /**
     * Fetches one server model result based on params
     * @param QueryModelInterface $params
     * @return ServerModelInterface|null
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ): ?ServerModelInterface;

    /**
     * Fetches one ssh key model result based on params
     * @param QueryModelInterface $params
     * @return SSHKeyModelInterface|null
     */
    public function fetchOneSSHKey(
        ?QueryModelInterface $params = null
    ): ?SSHKeyModelInterface;

    /**
     * Fetches all server model results based on params
     * @param QueryModelInterface $params
     * @return ServerModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null): array;

    /**
     * Fetches all ssh key model results based on params
     * @param QueryModelInterface $params
     * @return SSHKeyModelInterface[]
     */
    public function fetchAllSSHKeys(?QueryModelInterface $params = null): array;

    /**
     * Fetch servers as select array
     * @param QueryModelInterface|null $params
     * @param bool $keyIsSlug Set true to use slugs instead of GUIDs as keys
     * @return array [
     *     'server-slug' => 'Server Name',
     *     'another-server-slug' => 'Another Server Name',
     * ]
     */
    public function fetchAsSelectArray(
        ?QueryModelInterface $params = null,
        $keyIsSlug = false
    ): array;

    /**
     * Fetch SSH Keys as select array
     * @param QueryModelInterface|null $params
     * @param bool $keyIsSlug Set true to use slugs instead of GUIDs as keys
     * @return array [
     *     'ssh-key-slug' => 'SSH Key Name',
     *     'another-ssh-key-slug' => 'Another SSH Key Name',
     * ]
     */
    public function fetchSSHKeysAsSelectArray(
        ?QueryModelInterface $params = null,
        $keyIsSlug = false
    ): array;
}
