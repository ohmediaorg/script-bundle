<?php

namespace OHMedia\ScriptBundle\Twig;

use OHMedia\ScriptBundle\Service\ScriptWhitelist;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ScriptWhitelistExtension extends AbstractExtension
{
    public function __construct(
        private ScriptWhitelist $scriptWhitelist,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('script_whitelist', [$this, 'whitelist'], [
                'needs_environment' => true,
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function whitelist(Environment $twig, int $limit = 3): string
    {
        $iframeSrcPrefixes = $this->scriptWhitelist->getIframeSrcPrefixes();
        $scriptSrcPrefixes = $this->scriptWhitelist->getScriptSrcPrefixes();

        if (!$iframeSrcPrefixes && !$scriptSrcPrefixes) {
            return '';
        }

        return $twig->render('@OHMediaScript/script_whitelist.html.twig', [
            'iframe_src_prefixes' => $iframeSrcPrefixes,
            'script_src_prefixes' => $scriptSrcPrefixes,
        ]);
    }
}
