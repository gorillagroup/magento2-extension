<?php
declare(strict_types=1);

namespace Bazaarvoice\Connector\Logger;

use Bazaarvoice\Connector\Api\ConfigProviderInterface;
use Exception;
use Magento\Framework\App\State;

/**
 * Class Logger
 *
 * @package Bazaarvoice\Connector\Logger
 */
class Logger extends \Monolog\Logger
{
    /**
     * @var bool
     */
    protected $admin = false;
    /**
     * @var ConfigProviderInterface
     */
    private $configProvider;

    /**
     * Logger constructor.
     *
     * @param string                                    $name
     * @param ConfigProviderInterface                   $configProvider
     * @param State                                     $state
     *
     * @param array|\Monolog\Handler\HandlerInterface[] $handlers
     *
     * @codingStandardsIgnoreStart
     */
    public function __construct(
        $name,
        ConfigProviderInterface $configProvider,
        State $state,
        array $handlers = array()
    ) {
        try {
            $this->admin = $state->getAreaCode() === 'adminhtml';
        } catch (Exception $e) {
        }
        parent::__construct($name, $handlers);
        $this->configProvider = $configProvider;
        /** @codingStandardsIgnoreEnd */
    }

    /**
     * @param string|array $message
     * @param array        $context
     *
     * @return bool
     */
    public function debug($message, array $context = [])
    {
        if ($this->configProvider->isDebugEnabled()) {
            return $this->addRecord(static::DEBUG, $message, $context);
        }

        return true;
    }

    /**
     * @param int    $level
     * @param string $message
     * @param array  $context
     *
     * @return bool
     */
    public function addRecord($level, $message, array $context = [])
    {
        if (is_array($message)) {
            // phpcs:ignore
            $message = print_r($message, $return = true);
        }

        if (php_sapi_name() == "cli" || $this->admin) {
            // phpcs:ignore
            echo $message."\n";
        }

        return parent::addRecord($level, $message, $context);
    }
}
