<?php

namespace OHMedia\ScriptBundle\Security\Voter;

use OHMedia\ScriptBundle\Entity\Script;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;

class ScriptVoter extends AbstractEntityVoter
{
    public const INDEX = 'index';
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    protected function getAttributes(): array
    {
        return [
            self::INDEX,
            self::CREATE,
            self::EDIT,
            self::DELETE,
        ];
    }

    protected function getEntityClass(): string
    {
        return Script::class;
    }

    protected function canIndex(Script $script, User $loggedIn): bool
    {
        return true;
    }

    protected function canCreate(Script $script, User $loggedIn): bool
    {
        return true;
    }

    protected function canEdit(Script $script, User $loggedIn): bool
    {
        return true;
    }

    protected function canDelete(Script $script, User $loggedIn): bool
    {
        return true;
    }
}
