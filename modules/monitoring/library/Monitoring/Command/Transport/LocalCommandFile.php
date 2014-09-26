<?php
// {{{ICINGA_LICENSE_HEADER}}}
// {{{ICINGA_LICENSE_HEADER}}}

namespace Icinga\Module\Monitoring\Command\Transport;

use Exception;
use LogicException;
use Icinga\Logger\Logger;
use Icinga\Module\Monitoring\Command\Exception\TransportException;
use Icinga\Module\Monitoring\Command\IcingaCommand;
use Icinga\Module\Monitoring\Command\Renderer\IcingaCommandFileCommandRenderer;
use Icinga\Util\File;

/**
 * A local Icinga command file
 */
class LocalCommandFile implements CommandTransportInterface
{
    /**
     * Path to the icinga command file
     *
     * @var String
     */
    protected $path;

    /**
     * Mode used to open the icinga command file
     *
     * @var string
     */
    protected $openMode = 'wn';

    /**
     * Command renderer
     *
     * @var IcingaCommandFileCommandRenderer
     */
    protected $renderer;

    /**
     * Create a new local command file command transport
     */
    public function __construct()
    {
        $this->renderer = new IcingaCommandFileCommandRenderer();
    }

    /**
     * Set the path to the local Icinga command file
     *
     * @param   string $path
     *
     * @return  self
     */
    public function setPath($path)
    {
        $this->path = (string) $path;
        return $this;
    }

    /**
     * Get the path to the local Icinga command file
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the mode used to open the icinga command file
     *
     * @param   string $openMode
     *
     * @return  self
     */
    public function setOpenMode($openMode)
    {
        $this->openMode = (string) $openMode;
        return $this;
    }

    /**
     * Get the mode used to open the icinga command file
     *
     * @return string
     */
    public function getOpenMode()
    {
        return $this->openMode;
    }

    /**
     * Write the command to the local Icinga command file
     *
     * @param   IcingaCommand   $command
     * @param   int|null        $now
     *
     * @throws  LogicException
     * @throws  TransportException
     */
    public function send(IcingaCommand $command, $now = null)
    {
        if (! isset($this->path)) {
            throw new LogicException;
        }
        $commandString = $this->renderer->render($command, $now);
        Logger::debug(
            sprintf(
                mt('monitoring', 'Sending external Icinga command "%s" to the local command file "%s"'),
                $commandString,
                $this->path
            )
        );
        try {
            $file = new File($this->path, $this->openMode);
            $file->fwrite($commandString . "\n");
            $file->fflush();
        } catch (Exception $e) {
            throw new TransportException(
                mt(
                    'monitoring',
                    'Can\'t send external Icinga command "%s" to the local command file "%s": %s'
                ),
                $commandString,
                $this->path,
                $e
            );
        }
    }
}