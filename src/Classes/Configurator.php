<?php

namespace ModelCore\Classes;

use ModelCore\Configuration\ModelConfiguration;
use ModelCore\Exceptions\ConfigException;
use Closure;
use Exception;

class Configurator
{
    private ModelConfiguration $configuration;

    /**
     * @param Model $model
     * @throws Exception
     */
    public function __construct(
        private readonly Model $model
    ) {
        $this->resolveConfigData();
    }

    /**
     * @throws Exception
     */
    public static function new(Model $model): static
    {
        return new static($model);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function configure(): void
    {
        $this->resolveCoreParams();
        $this->resolveFieldsParams();
    }

    /**
     * @return void
     * @throws Exception
     */
    private function resolveConfigData(): void
    {
        if ($this->model::$config === '') {
            throw new ConfigException('Отсутствует привязка к конфигурации модели');
        }
        $this->configuration = new ModelConfiguration($this->model::$config);
    }

    /**
     * Устанавливает корневые параметры
     * @return void
     */
    private function resolveCoreParams(): void
    {
        $coreData = $this->configuration->getCoreData();

        $this->model->setTable($coreData->getTable());

        $connection = $coreData->getConnection();
        if (!empty($connection)) {
            $this->model->setConnection($connection);
        }

        $this->model->timestamps = in_array('timestamps', $coreData->getAvailableParams()); // TODO вынести в CoreData
    }

    /**
     * Устанавливает параметры для полей
     * @return void
     */
    private function resolveFieldsParams(): void
    {
        $fields = $this->configuration->getFields();
        $castsData = $fields->getCastsMap();
        $this->model->mergeCasts($castsData);
        $this->model->casts = [];
    }

    private function resolveRelationsParams()
    {
        //TODO
    }

    /**
     * Преобразует названия колонок из БД в алиасы полей из конфигурации
     * @param array $attributes
     * @param bool $sync
     * @param Closure $setRawAttributes
     * @return mixed
     */
    public function setRawAttributes(array $attributes, bool $sync, Closure $setRawAttributes): mixed
    {
        $map = $this->configuration->getFieldsAliasesMap(true);

        /**
         * Возвращает алиас для соответствующей колонки
         * @param string $colName
         * @return string
         */
        $alias = function (string $colName) use ($map) {
            return $map[$colName] ?? $colName;
        };

        $altributes = [];
        foreach ($attributes as $colName => $value) {
            $altributes[$alias($colName)] = $value;
        }

        return $setRawAttributes($altributes, $sync);
    }

    /**
     * Преобразует алиасы полей из конфигурации в названия колонок из БД
     * @param array $attributes
     * @return array<string, mixed>
     */
    public function prepareFieldsToDB(array $attributes): array
    {
        $map = $this->configuration->getFieldsAliasesMap();
        $preparedFields = [];
        foreach ($attributes as $alias => $value) {
            if (isset($map[$alias])) {
                $preparedFields[$map[$alias]] = $value;
            }
        }
        return $preparedFields;
    }
}
