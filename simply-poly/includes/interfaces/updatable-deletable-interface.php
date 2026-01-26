<?php

namespace SimplyPoly\Controllers\Contracts;

if (!defined('ABSPATH')) exit;

interface UpdatableDeletableInterface
{

    public function update($attrs): mixed;

    public function delete($attrs): mixed;

}

?>