<?php

namespace Apollo16\Core\Module;
use Apollo16\Core\Module\Exceptions\InvalidJsonFormat;

/**
 * Module Json Parser.
 *
 * @author      mohammad.anang  <m.anangnur@gmail.com>
 */

class Json
{
    /**
     * Original JSON File.
     *
     * @var string
     */
    protected $file;

    /**
     * Configuration.
     *
     * @var array
     */
    protected $config = [];

    /**
     * Create new JSON parser.
     *
     * @param $jsonFile
     * @throws Exceptions\InvalidJsonFormat
     */
    public function __construct($jsonFile)
    {
        $this->file = $jsonFile;
        $this->isValidJson();
    }

    /**
     * Get module name.
     * 
     * @return string
     */
    public function getModuleName()
    {
        return isset($this->config['name']) ? $this->config['name'] : '';
    }

    /**
     * Get module description.
     *
     * @return string
     */
    public function getModuleDescription()
    {
        return isset($this->config['description']) ? $this->config['description'] : '';
    }

    /**
     * Get module autoload.
     *
     * @return array
     */
    public function getModuleAutoload()
    {
        return isset($this->config['module']['autoloader']) ? $this->config['module']['autoload'] : [];
    }

    /**
     * Get all service providers registered by this module.
     *
     * @return array
     */
    public function getModuleServiceProvider()
    {
        return isset($this->config['module']['providers']) ? $this->config['module']['providers'] : [];
    }

    /**
     * Get all routes model binder registered with this module.
     *
     * @return array
     */
    public function getModuleRoutes()
    {
        return isset($this->config['module']['routes']) ? $this->config['modules']['routes'] : [];
    }

    /**
     * Get module author.
     *
     * @return string
     */
    public function getModuleAuthor()
    {
        return isset($this->config['author']) ? $this->config['author'] : '';
    }

    /**
     * Get module author url.
     *
     * @return string
     */
    public function getModuleAuthorUrl()
    {
        return isset($this->config['author_url']) ? $this->config['author_url'] : '';
    }

    /**
     * Get module base path.
     *
     * @return string
     */
    public function getBasePath()
    {
        return dirname($this->file);
    }

    /**
     * Determine if this a valid json object.
     *
     * @throws \Apollo16\Core\Module\Exceptions\InvalidJsonFormat
     */
    public function isValidJson()
    {
        $config = json_encode(file_get_contents($this->file), true);

        if(json_last_error() == JSON_ERROR_NONE) {
            $this->config = $config;
        } else {
            throw new InvalidJsonFormat($this->file);
        }
    }
}