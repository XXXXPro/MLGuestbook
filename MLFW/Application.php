<?php
/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork basic application class
 *  ================================ **/

namespace MLFW;

require __DIR__."/interfaces.php";
require __DIR__."/functions.php";

class Application {
  private $_params;
  public $db;
  public $router;

  function __construct($params) {
    $this->_params = $params;
    if (empty($this->_params['router'])) $this->_params['router']='MLFW\\Routers\\Stub';
    if (empty($this->_params['router_settings'])) $this->_params['router_settings']=null;
  }

  function init() {

  }

  function main() {
    $this->router = new $this->_params['router']($this->_params['router_settings']);
    $controller_class = $this->router->getAction('/');
    $controller = new $controller_class;
    $layout = $controller->exec(null);
  }
}