<?php

namespace PCatalog\Actions;
use function \MLFW\app, \MLFW\_dbg;

class Index implements \MLFW\IAction, \MLFW\IEventHandler {
  public function exec($params=null):\MLFW\Layouts\Basic {
    $page = \MLFW\Models\Entity::getById(1);

    $related = \MLFW\Models\Entity::getRelated($page,1);

    $test_obj = new \MLFW\Models\Entity;
    $test_obj->title = 'Пробная запись';
    $test_obj->type = 3;
    //$test_obj->save();

    // print "Test link: ".app()->router->route('user33',['id'=>666]);

    $l = new \MLFW\Layouts\JSON;
    // $l->setTitle('Пробная страница');
    $l->putText('Hello world!'.PHP_EOL);
    $l->wrap($page,'PCatalog\\Actions\\IndexTemplate');
    $l->wrapAll($related,'PCatalog\\Actions\\IndexTemplate');


    app()->events->trigger('Test',$l);

    $l->putText('Memory used: '.memory_get_peak_usage().' bytes'.PHP_EOL);
    $l->putText('All memory: '.memory_get_peak_usage(true).' bytes');

    _dbg("TEST!!!");

    return $l;
  }

  public static function handleEvent(string $name,object $data):void {
    $data->putText('Called from event processor!');
  }
}

class IndexTemplate extends \MLFW\Template {
  private $obj;

  function __construct($obj) {
    $this->obj = $obj;
  }

  public function getTemplate():string {
    if (\is_object($this->obj) && \method_exists($this->obj,'__toString')) return (string)$this->obj;
    else return print_r($this->obj,true);
  }
}