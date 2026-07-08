<?php

namespace App\Core\Exceptions;

use Exception;

class ConfigException extends Exception
{
    /**
     * Конвертирует входящее исключение в текущее
     * @param Exception $e
     * @return $this
     */
    public static function from(Exception $e): static
    {
        return new static($e->getMessage());
    }
}
