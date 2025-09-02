<?php

namespace OHMedia\ScriptBundle\Service;

use OHMedia\BackendBundle\Service\AbstractNavItemProvider;
use OHMedia\BootstrapBundle\Component\Nav\NavItemInterface;
use OHMedia\BootstrapBundle\Component\Nav\NavLink;
use OHMedia\ScriptBundle\Entity\Script;
use OHMedia\ScriptBundle\Security\Voter\ScriptVoter;

class ScriptNavItemProvider extends AbstractNavItemProvider
{
    public function getNavItem(): ?NavItemInterface
    {
        if ($this->isGranted(ScriptVoter::INDEX, new Script())) {
            return (new NavLink('Scripts', 'script_index'))
                ->setIcon('code-slash');
        }

        return null;
    }
}
