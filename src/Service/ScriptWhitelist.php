<?php

namespace OHMedia\ScriptBundle\Service;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ScriptWhitelist
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        #[Autowire('%oh_media_script.iframe_src_prefixes%')]
        private array $iframeSrcPrefixes,
        #[Autowire('%oh_media_script.script_src_prefixes%')]
        private array $scriptSrcPrefixes,
    ) {
    }

    public function isWhitelisted(string $content): bool
    {
        $token = $this->tokenStorage->getToken();

        $user = $token ? $token->getUser() : null;

        if (!$user) {
            return false;
        }

        if ($user->isTypeDeveloper()) {
            return true;
        }

        if ($this->isWhitelistedIframeTag($content)) {
            return true;
        }

        if ($this->isWhitelistedScriptTag($content)) {
            return true;
        }

        return false;
    }

    private function isWhitelistedIframeTag(string $content): bool
    {
        $isIframeTag = preg_match(
            '/^<iframe([^>]*)>\s*<\/iframe>$/',
            $content,
            $iframeTagMatches,
        );

        if (!$isIframeTag) {
            return false;
        }

        $attributes = $iframeTagMatches[1];

        preg_match_all('/ src="([^"]*)"/', $attributes, $srcMatches);

        if (1 !== count($srcMatches[0])) {
            // only valid with a single src attribute
            return false;
        }

        $src = $srcMatches[1][0];

        foreach ($this->iframeSrcPrefixes as $iframeSrcPrefix) {
            if (str_starts_with($src, $iframeSrcPrefix)) {
                return true;
            }
        }

        return false;
    }

    private function isWhitelistedScriptTag(string $content): bool
    {
        $isScriptTag = preg_match(
            '/^<script([^>]*)>\s*<\/script>$/',
            $content,
            $scriptTagMatches,
        );

        if (!$isScriptTag) {
            return false;
        }

        $attributes = $scriptTagMatches[1];

        preg_match_all('/ src="([^"]*)"/', $attributes, $srcMatches);

        if (1 !== count($srcMatches[0])) {
            // only valid with a single src attribute
            return false;
        }

        $src = $srcMatches[1][0];

        foreach ($this->scriptSrcPrefixes as $scriptSrcPrefix) {
            if (str_starts_with($src, $scriptSrcPrefix)) {
                return true;
            }
        }

        return false;
    }

    public function getIframeSrcPrefixes(): array
    {
        return $this->iframeSrcPrefixes;
    }

    public function getScriptSrcPrefixes(): array
    {
        return $this->scriptSrcPrefixes;
    }
}
