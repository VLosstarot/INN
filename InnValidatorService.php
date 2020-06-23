<?php

declare(strict_types=1);

require_once __DIR__."/InnValidatorInterface.php";

class InnValidatorService
{
    private InnValidatorInterface $validator;

    public function __construct(InnValidatorInterface $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param InnValidatorInterface $validator
     */
    public function setValidator(InnValidatorInterface $validator): void
    {
        $this->validator = $validator;
    }

    /**
     * Валидация ИНН
     * @param string|array $inn
     * @return bool
     */
    public function validate($inn): bool
    {
        return $this->validator->validate($inn);
    }
}
