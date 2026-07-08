<?php

namespace App\Core\Configuration\Sections;

use App\Core\Classes\Checker;
use App\Core\Exceptions\CheckerException;
use App\Core\Exceptions\ConfigException;

final class SectionFields
{
    /**
     * Ключ, по которому расположены данные в конфигурации модели
     */
    private const string CONFIG_DATA_KEY = 'fields';

    /**
     * @var SectionField[]
     */
    private array $fields;
    private array $aliases;

    /**
     * Формирует набор с данными полей модели
     *
     * @param Checker $checker
     * @param array $configRawData
     * @return self
     * @throws CheckerException
     * @throws ConfigException
     */
    public static function makeArray(Checker $checker, array $configRawData): self
    {
        $fieldsData = $configRawData[self::CONFIG_DATA_KEY];

        $sectionFields = new self();

        foreach ($fieldsData as $fieldName => $rawData) {
            $name = $checker->validateData('fieldName', $fieldName, 'required|string');

            if (isset($sectionFields->fields[$name])) {
                throw new ConfigException("Поле с названием {$fieldName} указано в конфигурации более 1 раза");
            }

            $sectionFields->fields[$name] = SectionField::make($checker, $fieldName, $rawData);
            $sectionFields->aliases[] = $name;
        }

        return $sectionFields;
    }

    /**
     * Возвращает набор полей
     * @return array<SectionField>
     */
    public function all(): array
    {
        return $this->fields;
    }

    /**
     * Возвращает класс конфигурации поля по его алиасу
     * @param string $alias
     * @return SectionField
     * @throws ConfigException
     */
    public function getField(string $alias): SectionField
    {
        if (!isset($this->fields[$alias])) {
            throw new ConfigException("Поле {$alias} отсутствует в текущей конфигурации");
        }

        return $this->fields[$alias];
    }

    /**
     * Возвращает массив алиасов полей
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
    }

    /**
     * Возвращает маппинг колонок в БД на алиасы полей в конфигурации
     *
     * По умолчанию возвращает [alias => COL_NAME]. При инверсии [COL_NAME => alias]
     * @param bool $isInverted
     * @return array
     */
    public function getAliasesMap(bool $isInverted = false): array
    {
        $columnAliasesMap = [];
        foreach ($this->fields as $field) {
            $columnAliasesMap += $field->getAliasColumn($isInverted);
        }

        return $columnAliasesMap;
    }

    /**
     * Возвращает маппинг cast на алиасы полей в конфигурации
     *
     * @return array Формат [alias => cast]
     */
    public function getCastsMap(): array
    {
        $castsMap = [];
        foreach ($this->fields as $field) {
            $castsMap += $field->getAliasCast();
        }

        return $castsMap;
    }
}
