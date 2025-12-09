<?php

namespace Symfony\Config\FidryAliceDataFixtures;

use Symfony\Component\Config\Loader\ParamConfigurator;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * This class is automatically generated to help in creating a config.
 */
class DbDriversConfig 
{
    private $doctrineOrm;
    private $doctrineMongodbOdm;
    private $doctrinePhpcrOdm;
    private $eloquentOrm;
    private $_usedProperties = [];

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function doctrineOrm($value): static
    {
        $this->_usedProperties['doctrineOrm'] = true;
        $this->doctrineOrm = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function doctrineMongodbOdm($value): static
    {
        $this->_usedProperties['doctrineMongodbOdm'] = true;
        $this->doctrineMongodbOdm = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function doctrinePhpcrOdm($value): static
    {
        $this->_usedProperties['doctrinePhpcrOdm'] = true;
        $this->doctrinePhpcrOdm = $value;

        return $this;
    }

    /**
     * @default null
     * @param ParamConfigurator|bool $value
     * @return $this
     */
    public function eloquentOrm($value): static
    {
        $this->_usedProperties['eloquentOrm'] = true;
        $this->eloquentOrm = $value;

        return $this;
    }

    public function __construct(array $config = [])
    {
        if (array_key_exists('doctrine_orm', $config)) {
            $this->_usedProperties['doctrineOrm'] = true;
            $this->doctrineOrm = $config['doctrine_orm'];
            unset($config['doctrine_orm']);
        }

        if (array_key_exists('doctrine_mongodb_odm', $config)) {
            $this->_usedProperties['doctrineMongodbOdm'] = true;
            $this->doctrineMongodbOdm = $config['doctrine_mongodb_odm'];
            unset($config['doctrine_mongodb_odm']);
        }

        if (array_key_exists('doctrine_phpcr_odm', $config)) {
            $this->_usedProperties['doctrinePhpcrOdm'] = true;
            $this->doctrinePhpcrOdm = $config['doctrine_phpcr_odm'];
            unset($config['doctrine_phpcr_odm']);
        }

        if (array_key_exists('eloquent_orm', $config)) {
            $this->_usedProperties['eloquentOrm'] = true;
            $this->eloquentOrm = $config['eloquent_orm'];
            unset($config['eloquent_orm']);
        }

        if ($config) {
            throw new InvalidConfigurationException(sprintf('The following keys are not supported by "%s": ', __CLASS__).implode(', ', array_keys($config)));
        }
    }

    public function toArray(): array
    {
        $output = [];
        if (isset($this->_usedProperties['doctrineOrm'])) {
            $output['doctrine_orm'] = $this->doctrineOrm;
        }
        if (isset($this->_usedProperties['doctrineMongodbOdm'])) {
            $output['doctrine_mongodb_odm'] = $this->doctrineMongodbOdm;
        }
        if (isset($this->_usedProperties['doctrinePhpcrOdm'])) {
            $output['doctrine_phpcr_odm'] = $this->doctrinePhpcrOdm;
        }
        if (isset($this->_usedProperties['eloquentOrm'])) {
            $output['eloquent_orm'] = $this->eloquentOrm;
        }

        return $output;
    }

}
