<?php

namespace OHMedia\ScriptBundle\Twig;

use OHMedia\ScriptBundle\Repository\ScriptRepository;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ScriptBarExtension extends AbstractExtension
{
    public function __construct(private ScriptRepository $scriptRepository)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('script_bar', [$this, 'scriptBar'], [
                'is_safe' => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function scriptBar(Environment $twig): string
    {
        $script = $this->scriptRepository->getActive();

        if (!$script) {
            return '';
        }

        if ($script->isDismissible() && isset($_COOKIE[$script->getCookieName()])) {
            return '';
        }

        return $twig->render('@OHMediaScript/script_bar.html.twig', [
            'script' => $script,
        ]);
    }
}
