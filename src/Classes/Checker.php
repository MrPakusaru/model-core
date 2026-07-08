<?php

namespace App\Core\Classes;

use App\Core\Exceptions\CheckerException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Factory as Validators;

/**
 * Проверяет данные на соответствие правилам
 */
final class Checker
{
    /**
     * Расположение конфигурации моделей
     */
    private const string VALIDATION_CONFIG_LOCATION = 'core.validation.model';

    private readonly Validators $validators;

    /**
     * Возвращает новый экземпляр
     * @return self
     * @throws BindingResolutionException
     */
    public static function new(): self
    {
        $instance = new self();
        $instance->validators = App::make(Validators::class);

        return $instance;
    }

    /**
     * Проводит валидацию конфигурации модели
     *
     * @throws CheckerException
     */
    public function configDataValidate(array $configData): void
    {
        $rules = config(self::VALIDATION_CONFIG_LOCATION, []);
        if (empty($rules)) {
            return;
        }

        $validator = $this->validators->make($configData, $rules);
        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $text = implode(', ' . PHP_EOL, $errors);
            throw new CheckerException($text);
        }
    }

    /**
     * Проводит валидацию параметра на соответствие параметрам
     *
     * @param string $fieldName
     * @param mixed $data Значение
     * @param string $rules Правила валидации
     * @return mixed
     * @throws CheckerException
     */
    public function validateData(string $fieldName, mixed $data, string $rules): mixed
    {
        $validator = $this->validators->make(
            ["fieldname" => $data],
            ["fieldname" => $rules]
        );

        if ($validator->fails()) {
            $msg = str_replace("fieldname", "'{$fieldName}'", $validator->errors()->first());
            throw new CheckerException($msg);
        }

        return $data;
    }
}
