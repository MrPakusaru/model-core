<?php

namespace ModelCore\Configuration\Sections;

use ModelCore\Classes\Checker;
use ModelCore\Exceptions\CheckerException;

final class SectionField
{
    private string $alias;
    private string $column;
    private string $cast;
    private bool $nullable;

    /**
     * Формирует набор данных поля модели
     *
     * @param Checker $checker
     * @param string $alias
     * @param array $fieldRawData
     * @return self
     * @throws CheckerException
     */
    public static function make(Checker $checker, string $alias, array $fieldRawData)
    {
        $field = new self();

        $field->alias = $checker->validateData('alias', $alias, 'required|string');

        $field->column = $checker->validateData('column', $fieldRawData['column'], 'required|string');
        $field->cast = $checker->validateData('cast', $fieldRawData['cast'], 'required|string'); //TODO разрешить отсутствие поля

        $requirements = $checker->validateData('column', $fieldRawData['requirements'], 'required|array');
        $field->nullable = in_array('nullable', $requirements); //TODO дописать применение

        return $field;
    }

    /**
     * Возвращает алиас поля
     * @return string
     */
    public function getAlias(): string
    {
        return $this->alias;
    }

    /**
     * Возвращает название колонки в таблице
     * @return string
     */
    public function getColumn(): string
    {
        return $this->column;
    }

    /**
     * TODO
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Возвращает спаренное значение (alias) -> (COL_NAME)
     * @param bool $inverted Нужно ли инвертировать ключ-значение (COL_NAME) -> (alias)
     * @return array<string,string>
     */
    public function getAliasColumn(bool $inverted = false): array
    {
        if ($inverted) {
            /* [COL_NAME => alias] */
            return [$this->column => $this->alias];
        }

        /* [alias => COL_NAME] */
        return [$this->alias => $this->column];
    }

    /**
     * Возвращает спаренное значение (alias) -> (cast)
     * @return string[]
     */
    public function getAliasCast(): array
    {
        return [$this->alias => $this->cast];
    }
}
