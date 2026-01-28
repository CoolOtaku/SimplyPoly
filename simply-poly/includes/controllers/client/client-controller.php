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

        add_filter('query_vars', function ($vars) {
            $vars[] = 'lang';
            return $vars;
        });

        //if (!is_admin()) add_action('template_redirect', [$this, 'get'], 0);
    }

    public function get($content): bool
    {
        if (!is_singular()) return false;
        ob_start([$this, 'post']);
        return true;
    }

    public function post($html): string|array
    {
        $post_id = get_the_ID();
        if (!$post_id) return $html;

        $translations = $this->translationController->get(['post_id' => $post_id]);
        if (empty($translations)) return $html;

        $current_lang = Helper::getCurrentLang();
        return $current_lang;
        if (!$current_lang) return $html;

        libxml_use_internal_errors(true);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->loadHTML('<?xml encoding="UTF-8">' . $html);

        $xpath = new \DOMXPath($dom);

        foreach ($translations as $cssPath => $langs) {

            if (empty($langs[$current_lang])) continue;

            $xpathQuery = Helper::cssToXpath($cssPath);
            if (!$xpathQuery) continue;

            $nodes = $xpath->query($xpathQuery);
            if (!$nodes) continue;

            foreach ($nodes as $node) {
                $node->nodeValue = $langs[$current_lang];
            }
        }

        return $dom->saveHTML();
    }
}

?>