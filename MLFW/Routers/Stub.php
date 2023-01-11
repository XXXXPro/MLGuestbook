<?php
/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork router stub class for single-page apps. 
 *  Always return controller class name specified in constructor
 *  ================================ **/

namespace MLFW\Routers;

class Stub implements \MLFW\IRouter {
  private $action_name;

  public function __construct($params) {
    $this->action_name = $params;
  }

  public function getAction($url): string {
    return $this->action_name;
  }

  public function route($name,$params):string {
    return './';
  }
}