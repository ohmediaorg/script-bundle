<?php

namespace OHMedia\ScriptBundle\Twig;

use OHMedia\ScriptBundle\Repository\ScriptRepository;
use OHMedia\WysiwygBundle\Twig\AbstractWysiwygExtension;
use Twig\TwigFunction;

class WysiwygExtension extends AbstractWysiwygExtension
{
    public function __construct(private ScriptRepository $scriptRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('script', [$this, 'script'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function script(?int $id = null): string
    {
        $script = $id ? $this->scriptRepository->find($id) : null;

        if (!$script) {
            return '';
        }

        return $script->getContent();
    }
}
