<?php

namespace App\Core;

use App\Core\Classes\Configurator;
use Exception;
use Illuminate\Database\Eloquent\Model as EloquentModel;

/**
 * Базовая модель
 */
abstract class Model extends EloquentModel
{
    /**
     * @var string Имя конфигурации модели
     */
    public static string $config = '';
    /**
     * Создаёт новый экземпляр модели
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        static::whenBooted(function () {
            Configurator::new($this)->configure();
        });
        parent::__construct($attributes);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function setRawAttributes(array $attributes, $sync = false): mixed
    {
        return Configurator::new($this)->setRawAttributes($attributes, $sync, function ($a, $s) {
            return parent::setRawAttributes($a, $s);
        });
    }

    /**
     * @return array<string, mixed>
     * @throws Exception
     */
    protected function getAttributesForInsert(): array
    {
        return Configurator::new($this)->prepareFieldsToDB(parent::getAttributesForInsert());
    }

    /**
     * @return array<string, mixed>
     * @throws Exception
     */
    public function getDirty(): array
    {
        return Configurator::new($this)->prepareFieldsToDB(parent::getDirty());
    }

    public function hasCast($key, $types = null): bool
    {
        return parent::hasCast($key, $types);
    }
}
