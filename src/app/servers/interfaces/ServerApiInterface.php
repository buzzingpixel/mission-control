<?php

declare(strict_types=1);

namespace src\app\servers\interfaces;

use corbomite\db\interfaces\QueryModelInterface;
use src\app\servers\exceptions\InvalidServerModelException;
use src\app\servers\exceptions\InvalidSSHKeyModelException;
use src\app\servers\exceptions\TitleNotUniqueException;

interface ServerApiInterface
{
    /**
     * Creates a Server Model
     */
    public function createModel() : ServerModelInterface;

    /**
     * Creates an SSH Key Model
     */
    public function createSShKeyModel() : SSHKeyModelInterface;

    /**
     * Converts a UUID to bytes for database queries
     */
    public function uuidToBytes(string $string) : string;

    /**
     * Creates a Fetch Data Params instance
     */
    public function makeQueryModel() : QueryModelInterface;

    /**
     * Saves a Server Model
     *
     * @param ServerModelInterface $serverModel
     *
     * @return mixed
     *
     * @throws InvalidServerModelException
     * @throws TitleNotUniqueException
     */
    public function save(ServerModelInterface $model);

    /**
     * Saves an SSH Key Model
     *
     * @param SSHKeyModelInterface $serverModel
     *
     * @return mixed
     *
     * @throws InvalidSSHKeyModelException
     * @throws TitleNotUniqueException
     */
    public function saveSSHKey(SSHKeyModelInterface $model);

    /**
     * Archives a server
     *
     * @return mixed
     */
    public function archive(ServerModelInterface $model);

    /**
     * Archives a server
     *
     * @return mixed
     */
    public function archiveSSHKey(SSHKeyModelInterface $model);

    /**
     * Un-archives a server
     *
     * @return mixed
     */
    public function unArchive(ServerModelInterface $model);

    /**
     * Un-archives an ssh key
     *
     * @return mixed
     */
    public function unArchiveSSHKey(SSHKeyModelInterface $model);

    /**
     * Deletes a server
     *
     * @return mixed
     */
    public function delete(ServerModelInterface $model);

    /**
     * Deletes an ssh key
     *
     * @return mixed
     */
    public function deleteSSHKey(SSHKeyModelInterface $model);

    /**
     * Fetches one server model result based on params
     */
    public function fetchOne(
        ?QueryModelInterface $params = null
    ) : ?ServerModelInterface;

    /**
     * Fetches one ssh key model result based on params
     */
    public function fetchOneSSHKey(
        ?QueryModelInterface $params = null
    ) : ?SSHKeyModelInterface;

    /**
     * Fetches all server model results based on params
     *
     * @return ServerModelInterface[]
     */
    public function fetchAll(?QueryModelInterface $params = null) : array;

    /**
     * Fetches all ssh key model results based on params
     *
     * @return SSHKeyModelInterface[]
     */
    public function fetchAllSSHKeys(?QueryModelInterface $params = null) : array;

    /**
     * Fetch servers as select array
     *
     * @param bool $keyIsSlug Set true to use slugs instead of GUIDs as keys
     *
     * @return string[] [
     *     'server-slug' => 'Server Name',
     *     'another-server-slug' => 'Another Server Name',
     * ]
     */
    public function fetchAsSelectArray(
        ?QueryModelInterface $params = null,
        bool $keyIsSlug = false
    ) : array;

    /**
     * Fetch SSH Keys as select array
     *
     * @param bool $keyIsSlug Set true to use slugs instead of GUIDs as keys
     *
     * @return string[] [
     *     'ssh-key-slug' => 'SSH Key Name',
     *     'another-ssh-key-slug' => 'Another SSH Key Name',
     * ]
     */
    public function fetchSSHKeysAsSelectArray(
        ?QueryModelInterface $params = null,
        bool $keyIsSlug = false
    ) : array;

    /**
     * Generates an SSH key and returns an array with two keys:
     *     - publickey
     *     - privatekey
     *
     * @return string[]
     */
    public function generateSSHKey() : array;

    /**
     * Lists the servers authorized keys
     *
     * @return string[]
     */
    public function listServerAuthorizedKeys(ServerModelInterface $model) : array;

    /**
     * Adds an authorized key to a server
     *
     * @return mixed
     */
    public function addServerAuthorizedKey(string $key, ServerModelInterface $model);

    /**
     * Removes an authorized key from a server
     *
     * @return mixed
     */
    public function removeServerAuthorizedKey(string $key, ServerModelInterface $model);
}
