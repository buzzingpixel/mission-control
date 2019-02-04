<?php
declare(strict_types=1);

namespace src\app\servers\interfaces;

use src\app\support\interfaces\StandardModelInterface;

interface ServerModelInterface extends StandardModelInterface
{
    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return RemoteServiceAdapterInterface
     */
    public function remoteServiceAdapter(
        ?RemoteServiceAdapterInterface $val = null
    ): RemoteServiceAdapterInterface;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function remoteId(string $val): string;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function address(string $val): string;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function sshPort(?string $val = null): string;

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param SSHKeyModelInterface|null $val
     * @return string
     */
    public function sshKeyModel(?string $val = null): ?SSHKeyModelInterface;

    /**
     * Clears the SSH Key Model
     */
    public function clearSSHKeyModel();

    /**
     * Returns the value. Sets value if incoming argument is set
     * @param string|null $val
     * @return string
     */
    public function sshUserName(?string $val = null): string;
}
