<?php

namespace OHMedia\ScriptBundle\Security\Voter;

use OHMedia\ScriptBundle\Entity\Script;
use OHMedia\SecurityBundle\Entity\User;
use OHMedia\SecurityBundle\Security\Voter\AbstractEntityVoter;
use OHMedia\WysiwygBundle\Service\Wysiwyg;

class ScriptVoter extends AbstractEntityVoter
{
    public const INDEX = 'index';
    public const CREATE = 'create';
    public const EDIT = 'edit';
    public const DELETE = 'delete';

    public function __construct(private Wysiwyg $wysiwyg)
    {
    }

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
        return $loggedIn->isTypeDeveloper();
    }

    protected function canEdit(Script $script, User $loggedIn): bool
    {
        return $loggedIn->isTypeDeveloper();
    }

    protected function canDelete(Script $script, User $loggedIn): bool
    {
        if (!$loggedIn->isTypeDeveloper()) {
            return false;
        }

        $shortcode = sprintf('script(%d)', $script->getId());

        return !$this->wysiwyg->shortcodesInUse($shortcode);
    }
}
