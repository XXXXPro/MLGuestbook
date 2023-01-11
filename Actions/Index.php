<?php

namespace AppCatalog\actions;
use function \MLFW\app, \MLFW\_dbg;

class Index implements \MLFW\IAction {
  public function exec($params=null):\MLFW\Layouts\Basic {
    $page = \MLFW\Models\Entity::getById(1);

    $related = \MLFW\Models\Entity::getRelated($page,1);
    print_r($related);

    echo "<br /><br />\n\n";

    $l = new \MLFW\Layouts\Basic;
    $l->wrap('Hello world!'.PHP_EOL);
    $l->wrap(print_r($page,true));
    $l->wrap('Memory used: '.memory_get_peak_usage().' bytes');
    return $l;
  }
}