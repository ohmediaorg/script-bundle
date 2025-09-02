<?php

namespace OHMedia\ScriptBundle\Service;

use OHMedia\ScriptBundle\Entity\Script;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

class ScriptEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'Scripts';
    }

    public function getEntities(): array
    {
        return [Script::class];
    }
}
