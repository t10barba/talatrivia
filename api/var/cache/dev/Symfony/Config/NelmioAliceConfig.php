<?php

namespace Symfony\Config;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class NelmioAliceConfig implements \Symfony\Component\Config\Builder\ConfigBuilderInterface
{
    private $locale;
    private $seed;
    private $functionsBlacklist;
    private $loadingLimit;
    private $maxUniqueValuesRetry;
    private $_usedProperties = [];
    private $_hasDeprecatedCalls = false;

    /**
     * Default locale for the Faker Generator
     * @default 'en_US'
     * @param ParamConfigurator|mixed $value
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function locale($value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['locale'] = true;
        $this->locale = $value;

        return $this;
    }

    /**
     * Value used make sure Faker generates data consistently across runs, set to null to disable.
     * @default 1
     * @param ParamConfigurator|mixed $value
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function seed($value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['seed'] = true;
        $this->seed = $value;

        return $this;
    }

    /**
     * @param ParamConfigurator|list<ParamConfigurator|mixed> $value
     *
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function functionsBlacklist(ParamConfigurator|array $value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['functionsBlacklist'] = true;
        $this->functionsBlacklist = $value;

        return $this;
    }

    /**
     * Alice may do some recursion to resolve certain values. This parameter defines a limit which will stop the resolution once reached.
     * @default 5
     * @param ParamConfigurator|int $value
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function loadingLimit($value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['loadingLimit'] = true;
        $this->loadingLimit = $value;

        return $this;
    }

    /**
     * Maximum number of time Alice can try to generate a unique value before stopping and failing.
     * @default 150
     * @param ParamConfigurator|int $value
     * @return $this
     * @deprecated since Symfony 7.4
     */
    public function maxUniqueValuesRetry($value): static
    {
        $this->_hasDeprecatedCalls = true;
        $this->_usedProperties['maxUniqueValuesRetry'] = true;
        $this->maxUniqueValuesRetry = $value;

        return $this;
    }

    public function getExtensionAlias(): string
    {
        return 'nelmio_alice';
    }

    public function __construct(array $config = [])
    {
        if (array_key_exists('locale', $config)) {
            $this->_usedProperties['locale'] = true;
            $this->locale = $config['locale'];
            unset($config['locale']);
        }

        if (array_key_exists('seed', $config)) {
            $this->_usedProperties['seed'] = true;
            $this->seed = $config['seed'];
            unset($config['seed']);
        }

        if (array_key_exists('functions_blacklist', $config)) {
            $this->_usedProperties['functionsBlacklist'] = true;
            $this->functionsBlacklist = $config['functions_blacklist'];
            unset($config['functions_blacklist']);
        }

        if (array_key_exists('loading_limit', $config)) {
            $this->_usedProperties['loadingLimit'] = true;
            $this->loadingLimit = $config['loading_limit'];
            unset($config['loading_limit']);
        }

        if (array_key_exists('max_unique_values_retry', $config)) {
            $this->_usedProperties['maxUniqueValuesRetry'] = true;
            $this->maxUniqueValuesRetry = $config['max_unique_values_retry'];
            unset($config['max_unique_values_retry']);
        }

        if ($config) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($config)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['locale'])) {
            $output['locale'] = $this->locale;
        }
        if (isset($this->_usedProperties['seed'])) {
            $output['seed'] = $this->seed;
        }
        if (isset($this->_usedProperties['functionsBlacklist'])) {
            $output['functions_blacklist'] = $this->functionsBlacklist;
        }
        if (isset($this->_usedProperties['loadingLimit'])) {
            $output['loading_limit'] = $this->loadingLimit;
        }
        if (isset($this->_usedProperties['maxUniqueValuesRetry'])) {
            $output['max_unique_values_retry'] = $this->maxUniqueValuesRetry;
        }
        if ($this->_hasDeprecatedCalls) {
            trigger_deprecation('symfony/config', '7.4', 'Calling any fluent method on "%s" is deprecated; pass the configuration to the constructor instead.', $this::class);
        }

        return $output;
    }

}
