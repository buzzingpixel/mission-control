<?php

declare(strict_types=1);

namespace src\app\servers\interfaces;

use src\app\support\interfaces\StandardModelInterface;

interface ServerModelInterface extends StandardModelInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     *
     * @param string|null $val
     */
    public function remoteServiceAdapter(
        ?RemoteServiceAdapterInterface $val = null
    ) : ?RemoteServiceAdapterInterface;

    /**
     * Clears the remote service adapter
     *
     * @return mixed
     */
    public function clearRemoteServiceAdapter();

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function remoteId(?string $val = null) : ?string;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function address(?string $val = null) : ?string;

    /**
     * Returns the value. Sets value if incoming argument is set
     *
     * @param string|null $val
     *
     * @return string
     */
    public function sshPort(?int $val = null) : ?int;

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function sshKeyModel(
        ?SSHKeyModelInterface $val = null
    ) : ?SSHKeyModelInterface;

    /**
     * Clears the SSH Key Model
     *
     * @return mixed
     */
    public function clearSSHKeyModel();

    /**
     * Returns the value. Sets value if incoming argument is set
     */
    public function sshUserName(?string $val = null) : ?string;
}
