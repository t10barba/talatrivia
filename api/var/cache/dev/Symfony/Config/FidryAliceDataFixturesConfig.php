<?php

namespace Symfony\Config;

require_once __DIR__.\DIRECTORY_SEPARATOR.'FidryAliceDataFixtures'.\DIRECTORY_SEPARATOR.'DbDriversConfig.php';

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class FidryAliceDataFixturesConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $defaultPurgeMode;
    private $dbDrivers;
    private $_usedProperties = [];
    private $_hasDeprecatedCalls = false;

    /**
     * @default 'delete'
     * @param ParamConfigurator|mixed $value
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function defaultPurgeMode($value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['defaultPurgeMode'] = true;
        $this->defaultPurgeMode = $value;

        return $this;
    }

    /**
     * The list of enabled drivers.
     * @default {"doctrine_orm":null,"doctrine_mongodb_odm":null,"doctrine_phpcr_odm":null,"eloquent_orm":null}
     * @deprecated since Symfony 7.4
     */
    public function dbDrivers(array $value = []): \Symfony\Config\FidryAliceDataFixtures\DbDriversConfig
    {
        $this->_hasDeprecatedCalls = true;
        if (null === $this->dbDrivers) {
            $this->_usedProperties['dbDrivers'] = true;
            $this->dbDrivers = new \Symfony\Config\FidryAliceDataFixtures\DbDriversConfig($value);
        } elseif (0 < \func_num_args()) {
            throw new InvalidConfigurationException('The node created by "dbDrivers()" has already been initialized. You cannot pass values the second time you call dbDrivers().');
        }

        return $this->dbDrivers;
    }

    public function getExtensionAlias(): string
    {
        return 'fidry_alice_data_fixtures';
    }

    public function __construct(array $config = [])
    {
        if (array_key_exists('default_purge_mode', $config)) {
            $this->_usedProperties['defaultPurgeMode'] = true;
            $this->defaultPurgeMode = $config['default_purge_mode'];
            unset($config['default_purge_mode']);
        }

        if (array_key_exists('db_drivers', $config)) {
            $this->_usedProperties['dbDrivers'] = true;
            $this->dbDrivers = new \Symfony\Config\FidryAliceDataFixtures\DbDriversConfig($config['db_drivers']);
            unset($config['db_drivers']);
        }

        if ($config) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($config)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['defaultPurgeMode'])) {
            $output['default_purge_mode'] = $this->defaultPurgeMode;
        }
        if (isset($this->_usedProperties['dbDrivers'])) {
            $output['db_drivers'] = $this->dbDrivers->toArray();
        }
        if ($this->_hasDeprecatedCalls) {
            trigger_deprecation('symfony/config', '7.4', 'Calling any fluent method on "%s" is deprecated; pass the configuration to the constructor instead.', $this::class);
        }

        return $output;
    }

}
