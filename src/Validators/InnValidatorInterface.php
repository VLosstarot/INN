<?php

declare(strict_types=1);

namespace App\Validators;

/**
 * Валидатор INN
 */
interface InnValidatorInterface
{
    /**
     * Валидация ИНН
     * @param string|array $inn
     * @return bool
     */
    public function validate($inn): bool;
}
