<?php

namespace Symfony\Config;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class HautelookAliceConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $fixturesPath;
    private $rootDirs;
    private $_usedProperties = [];
    private $_hasDeprecatedCalls = false;

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed>|mixed $value
     *
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function fixturesPath(mixed $value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['fixturesPath'] = true;
        $this->fixturesPath = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function rootDirs(ParamConfigurator|array $value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['rootDirs'] = true;
        $this->rootDirs = $value;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'hautelook_alice';
    }

    public function __construct(array $config = [])
    {
        if (array_key_exists('fixtures_path', $config)) {
            $this->_usedProperties['fixturesPath'] = true;
            $this->fixturesPath = $config['fixtures_path'];
            unset($config['fixtures_path']);
        }

        if (array_key_exists('root_dirs', $config)) {
            $this->_usedProperties['rootDirs'] = true;
            $this->rootDirs = $config['root_dirs'];
            unset($config['root_dirs']);
        }

        if ($config) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($config)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['fixturesPath'])) {
            $output['fixtures_path'] = $this->fixturesPath;
        }
        if (isset($this->_usedProperties['rootDirs'])) {
            $output['root_dirs'] = $this->rootDirs;
        }
        if ($this->_hasDeprecatedCalls) {
            trigger_deprecation('symfony/config', '7.4', 'Calling any fluent method on "%s" is deprecated; pass the configuration to the constructor instead.', $this::class);
        }

        return $output;
    }

}
