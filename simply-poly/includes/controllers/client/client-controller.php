<?php

namespace SimplyPoly\Controllers;

use SimplyPoly\Helper;
use SimplyPoly\Controllers\TranslationController;

if (!defined('ABSPATH')) exit;

class ClientController extends AbstractController
{
    private $translationController;

    public function __construct()
    {
        parent::__construct();

        $this->translationController = new TranslationController();
        if (!is_admin()) add_action('template_redirect', [$this, 'get'], 0);
    }

    public function get($attrs = null): mixed
    {
        if (!Helper::isFrontendRequest()) return null;
        if (!is_singular()) return null;
        if (is_preview()) return null;
        
        ob_start([$this, 'post']);
        return null;
    }

    public function post($html, $phase = null): string
    {
        if (!is_singular()) return (string) $html;

        $post_id = get_the_ID();
        if (!$post_id) return (string) $html;

        $translations = $this->translationController->get(['post_id' => $post_id]);
        if (empty($translations)) return (string) $html;

        $current_lang = Helper::getCurrentLang();
        if (!$current_lang) return (string) $html;

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<?xml encoding="UTF-8">' . (string) $html);

        $xpath = new \DOMXPath($dom);

        foreach ($translations as $cssPath => $langs) {
            if (empty($langs[$current_lang])) continue;

            $xpathQuery = Helper::cssToXpath($cssPath);
            if (!$xpathQuery) continue;

            $nodes = $xpath->query($xpathQuery);
            if (!$nodes) continue;

            foreach ($nodes as $node) $node->nodeValue = $langs[$current_lang];
        }

        return $dom->saveHTML() ?: (string) $html;
    }
}

?>