<?php

namespace OHMedia\ScriptBundle\Service;

use OHMedia\ScriptBundle\Repository\ScriptRepository;
use OHMedia\WysiwygBundle\Shortcodes\AbstractShortcodeProvider;
use OHMedia\WysiwygBundle\Shortcodes\Shortcode;

class ScriptShortcodeProvider extends AbstractShortcodeProvider
{
    public function __construct(private ScriptRepository $scriptRepository)
    {
    }

    public function getTitle(): string
    {
        return 'Scripts';
    }

    public function buildShortcodes(): void
    {
        $scripts = $this->scriptRepository->createQueryBuilder('s')
            ->orderBy('s.name', 'asc')
            ->getQuery()
            ->getResult();

        foreach ($scripts as $script) {
            $id = $script->getId();

            $this->addShortcode(new Shortcode(
                sprintf('%s (ID:%s)', $script, $id),
                'script('.$id.')'
            ));
        }
    }
}
