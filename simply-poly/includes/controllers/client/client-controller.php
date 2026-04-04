<?php

namespace SimplyPoly\Controllers;

use SimplyPoly\Helper;
use SimplyPoly\Views\SwitchLanguagesView;
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

        new SwitchLanguagesView();
    }

    public function get($attrs = null): mixed
    {
        if (!Helper::isFrontendRequest()) return null;
        if (!is_singular()) return null;
        if (is_preview()) return null;

        ob_start([$this, 'post']);
        return null;
    }

    public function post($html): string
    {
        $post_id = get_the_ID();
        if (!$post_id) return $html;

        $translations = $this->translationController->get(['post_id' => $post_id]);
        if (empty($translations)) return $html;

        $current_lang = Helper::getCurrentLang();
        if (!$current_lang) return $html;

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new \DOMXPath($dom);

        $adminBar = $dom->getElementById('wpadminbar');
        $placeholder = null;
        $parent = null;

        if ($adminBar && $adminBar->parentNode) {
            $parent = $adminBar->parentNode;
            $placeholder = $dom->createComment('simplypoly-adminbar');
            $parent->replaceChild($placeholder, $adminBar);
        }

        foreach ($translations as $cssPath => $langs) {
            if (empty($langs[$current_lang])) continue;

            $xpathQuery = Helper::cssToXpath($cssPath);
            if (!$xpathQuery) continue;

            $nodes = $xpath->query('/html' . $xpathQuery);
            if (!$nodes || $nodes->length === 0) continue;

            foreach ($nodes as $node) $node->nodeValue = $langs[$current_lang];
        }

        if ($placeholder && $parent && $adminBar) $parent->replaceChild($adminBar, $placeholder);

        return $dom->saveHTML() ?: $html;
    }
}
