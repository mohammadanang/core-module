<?php

namespace Apollo16\Core\Module\Exceptions;

use Exception;

/**
 * Invalid Json Format Exception.
 *
 * @author      mohammad.anang  <m.anangnur@gmail.com>
 */

class InvalidJsonFormat extends Exception
{
    public function __construct($filePath)
    {
        parent::__construct("JSON FIle: `$filePath` is incorrectly formed.");
    }
}