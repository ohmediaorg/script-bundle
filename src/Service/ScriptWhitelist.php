<?php

namespace OHMedia\ScriptBundle\Service;

use OHMedia\ScriptBundle\Entity\Script;
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

    public function isScriptWhitelisted(Script $script): bool
    {
        $token = $this->tokenStorage->getToken();

        $user = $token ? $token->getUser() : null;

        if (!$user) {
            return false;
        }

        if ($user->isTypeDeveloper()) {
            // return true;
        }

        foreach ($this->iframeSrcPrefixes as $iframeSrcPrefix) {
            $prefix = preg_quote($iframeSrcPrefix, '/');
            $regex = '/^<iframe[^src]*src="'.$prefix.'[^"]*"[^>]*>\s*<\/iframe>$/';
            if (preg_match($regex, $script->getContent())) {
                return true;
            }
        }

        foreach ($this->scriptSrcPrefixes as $scriptSrcPrefix) {
            $prefix = preg_quote($scriptSrcPrefix, '/');
            $regex = '/^<script[^src]*src="'.$prefix.'[^"]*"[^>]*>\s*<\/script>$/';
            if (preg_match($regex, $script->getContent())) {
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
