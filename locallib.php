<?php
require_once(dirname(__FILE__).'/../../config.php');

function local_fliplearning_new_menu_item($name, $url){
    $item = new stdClass();
    $item->name = $name;
    $item->url = $url;
    return $item;
}
