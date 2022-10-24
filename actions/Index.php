<?php

namespace AppCatalog\actions;
use function \MLWF\app, \MLFW\_dbg;

class Index implements \MLFW\IAction {
  public function exec($params=null):\MLFW\Layouts\Basic {
    $l = new \MLFW\layouts\Basic;
    $l->wrap('Hello world!'.PHP_EOL);
    $l->wrap('Memory used: '.memory_get_peak_usage().' bytes');
    return $l;
  }
}