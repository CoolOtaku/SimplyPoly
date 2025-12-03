<?php

namespace SimplyPoly\Controllers;

if (!defined('ABSPATH')) exit;

abstract class AbstractController
{

    public function __construct()
    {
        if (!defined('ABSPATH')) exit;
    }

    abstract public function get($attrs): mixed;

    abstract public function post($attrs): mixed;

}

?>