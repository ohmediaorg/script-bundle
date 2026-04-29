<?php

namespace OHMedia\ScriptBundle\Validator;

use OHMedia\ScriptBundle\Service\ScriptWhitelist as ScriptWhitelistService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ScriptWhitelistValidator extends ConstraintValidator
{
    public function __construct(private ScriptWhitelistService $scriptWhitelist)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ScriptWhitelist) {
            throw new UnexpectedTypeException($constraint, ScriptWhitelist::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!$this->scriptWhitelist->isWhitelisted($value)) {
            $this->context->buildViolation('The script content did not meet the whitelist requirements. Please ensure you copied it correctly.')
                ->setParameter('{{ string }}', $value)
                ->addViolation();
        }
    }
}
