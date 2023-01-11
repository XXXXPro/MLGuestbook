<?php

/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork basic layout class (defaults to text/plain )
 *  ================================ **/

namespace MLFW\layouts;

class Basic {
  private $mime = 'text/plain';
  private $modified =  null;
  private $objects = [];
  private $headers = [];

  public function wrap($obj):void {
    $this->objects[]=$obj;
  }

  public function __toString():string  {
    $result = '';
    foreach ($this->objects as $obj) $result.=$obj;
    return $result;
  }
}