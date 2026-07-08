<?php

namespace ModelCore\Configuration\Sections;

use ModelCore\Classes\Checker;
use ModelCore\Exceptions\CheckerException;
use ModelCore\Exceptions\ConfigException;

/**
 * Корневые данные модели
 */
final class SectionCore
{
    /**
     * Ключ, по которому расположены данные в конфигурации модели
     */
    private const string CONFIG_DATA_KEY = 'core';

    /**
     * @var string Связанная таблица
     */
    private readonly string $table;

    /**
     * @var string Соединение к БД
     */
    private readonly string $connection;

    /**
     * @var array Особенности модели
     */
    private readonly array $availableParams;

    /**
     * Формирует набор корневых данных модели
     *
     * @param Checker $checker
     * @param array $configRawData Данные раздела 'core' конфигурации модели
     * @return SectionCore
     * @throws ConfigException
     */
    public static function make(Checker $checker, array $configRawData): self
    {
        $coreData = $configRawData[self::CONFIG_DATA_KEY];

        $core = new self();
        try {
            $core->table = $checker->validateData('table', $coreData['table'], 'required|string');
            $core->connection = $checker->validateData('connection', $coreData['connection'], 'nullable|string');
            $core->availableParams = $checker->validateData('available_params', $coreData['available_params'], 'required|array');
        } catch (CheckerException $e) {
            throw ConfigException::from($e);
        }

        return $core;
    }

    /**
     * Возвращает название связанной таблицы
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * Возвращает название установленного соединения к БД
     * @return string
     */
    public function getConnection(): string
    {
        return $this->connection;
    }

    /**
     * Возвращает параметры особенностей модели
     * @return array
     */
    public function getAvailableParams(): array
    {
        return $this->availableParams;
    }
}
