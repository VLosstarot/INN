<?php

declare(strict_types=1);

/**
 * Валидатор INN
 */
interface Validator
{
    /**
     * Валидация ИНН
     * @param string|array $inn
     * @return bool
     */
    public function validate($inn): bool;
}
