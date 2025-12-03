<?php

namespace SimplyPoly\Views;

if (!defined('ABSPATH')) exit;

abstract class AbstractView
{

    public function __construct()
    {
        if (!defined('ABSPATH')) exit;
    }

    abstract public function render($attrs): mixed;

}

?>