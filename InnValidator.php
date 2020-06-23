<?php

declare(strict_types=1);

namespace App;

use App\InnValidatorInterface;

require_once __DIR__."/InnValidatorInterface.php";

class InnValidator implements InnValidatorInterface
{
    /**
     * Коэффициенты для вычисления контрольной суммы 10-значного ИНН
     */
    private const COEFFICIENTS_10 = [2, 4, 10, 3, 5, 9, 4, 6, 8, 0];
    /**
     * Коэффициенты для вычисления первой цифры контрольной суммы 12-значного ИНН
     */
    private const COEFFICIENTS_12_1 = [7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0];
    /**
     * Коэффициенты для вычисления второй цифры контрольной суммы 12-значного ИНН
     */
    private const COEFFICIENTS_12_2 = [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0];
    /**
     * Делитель контрольного числа ИНН
     */
    private const CONTROL_NUMBER_DIVIDER = 11;
    /**
     * Добавочный делитель контрольного числа ИНН
     */
    private const ADDITIONAL_CONTROL_NUMBER_DIVIDER = 10;

    /**
     * {@inheritDoc}
     */
    public function validate($inn): bool
    {
        if (!is_array($inn)) {
            $inn = array_map('intval', str_split($inn));
        }

        switch (count($inn)) {
            case 10:
                return $this->validate10($inn);
            case 12:
                return $this->validate12($inn);
            default:
                return false;
        }
    }

    /**
     * Валидация 10-значного ИНН
     * Алгоритм:
     * 1) Вычисляется контрольная сумма как сумма произведений соответствующего числа
     *    в номере на коэффициенты [2, 4, 10, 3, 5, 9, 4, 6, 8, 0]
     * 2) Вычисляется контрольное число как остаток от деления контрольной суммы на 11
     * 3) Если контрольное число больше 9,
     *    то контрольное число вычисляется как остаток от деления контрольного числа на 10
     * 4) Контрольное число проверяется с десятым знаком ИНН. В случае их равенства ИНН считается правильным
     * @param array $inn
     * @return bool
     */
    private function validate10(array $inn): bool
    {
        /** 1) */
        foreach (self::COEFFICIENTS_10 as $key => $number) {
            $sum[] = $inn[$key] * $number;
        }

        $sum = array_sum($sum);

        /** 2) */
        $sum %= self::CONTROL_NUMBER_DIVIDER;

        /** 3) */
        [$sum] = $this->mod([$sum]);

        /** 4) */
        return $sum === $inn[9];
    }

    /**
     * Валидация 12-значного ИНН
     * Алгоритм:
     * 1) Вычисляется контрольная сумма как сумма произведений соответствующего числа в номере
     *    на коэффициенты [7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0] по первым 11 знакам
     * 2) Вычисляется контрольное число(1) как остаток от деления контрольной суммы на 11
     * 3) Вычисляется контрольная сумма как сумма произведений соответствующего числа в номере
     *    на коэффициенты  [3, 7, 2, 4, 10, 3, 5, 9, 4, 6, 8, 0] по 12 знакам
     * 4) Вычисляется контрольное число(2) как остаток от деления контрольной суммы на 11
     * 5) Если контрольное число(2) больше 9,
     *    то контрольное число(2) вычисляется как остаток от деления контрольного числа(2) на 10
     * 6) Контрольное число(1) проверяется с одиннадцатым знаком ИНН
     *    и контрольное число(2) проверяется с двенадцатым знаком ИНН
     *    В случае их равенства ИНН считается правильным
     * @param array $inn
     * @return bool
     */
    private function validate12(array $inn): bool
    {
        /** 1) */
        foreach (self::COEFFICIENTS_12_1 as $key => $number) {
            $sum_1[] = $inn[$key] * $number;
        }
        $sum_1 = array_sum($sum_1);

        /** 2) */
        $sum_1 %= self::CONTROL_NUMBER_DIVIDER;

        /** 3) */
        foreach (self::COEFFICIENTS_12_2 as $key => $number) {
            $sum_2[] = $inn[$key] * $number;
        }
        $sum_2 = array_sum($sum_2);

        /** 4) */
        $sum_2 %= self::CONTROL_NUMBER_DIVIDER;

        /** 5) */
        [$sum_1, $sum_2] = $this->mod([$sum_1, $sum_2]);

        /** 6) */
        return $sum_1 === $inn[10] && $sum_2 === $inn[11];
    }

    /**
     * Выполнение пунтка 3) валидации 10-значного ИНН для нескольких значений
     * @param array $numbers
     * @return array
     */
    private function mod(array $numbers): array
    {
        $result = [];
        foreach ($numbers as $number) {
            if ($number > 9) {
                $result[] = $number % self::ADDITIONAL_CONTROL_NUMBER_DIVIDER;
            } else {
                $result[] = $number;
            }
        }
        return $result;
    }
}
