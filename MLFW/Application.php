<?php
/** ================================
 *  @package MLFW
 *  @author 4X_Pro <me@4xpro.ru>
 *  @version 0.90
 *  @url 
 *  MindLife FrameWork basic application class
 *  ================================ **/

namespace MLFW;

use Exception;

require __DIR__."/interfaces.php";
require __DIR__."/functions.php";

class Application {
  private $_params;
  public $db;
  public $router;

  function __construct($params) {
    $this->_params = $params;
    $default_values = [
      'router'=>'MLFW\\Routers\\Stub',
      'router_settings'=>null,
      'ob_handler'=>null,
      'error_reporting'=>0
    ];
    foreach ($default_values as $key=>$def_value) if (!isset($this->_params[$key])) $this->_params[$key]=$def_value;    
  }

  function init() {
    // starting output buffering
    ob_start($this->_params['ob_handler']);
    // enabling errors display
    ini_set("error_reporting",(string)$this->_params['error_reporing']);
    // creating router class
    $this->router = new $this->_params['router']($this->_params['router_settings']);
  }

  function main() {
    try {
      $this->init();

      $controller_class = $this->router->getAction('/');
      if (!class_exists($controller_class)) throw new Exception404("Class $controller_class not found!");
      $controller = new $controller_class;
      $result = $controller->exec(null);
      // TODO: Add headers output if $result is instance of Layout, else output status 200
      print $result;
    }
    catch (ExceptionConfig $e) {
      $this->show_error(500,'Configuration error: '.$e->getMessage(),$e);
    }
    catch (ExceptionSecurity $e) {    
      $this->show_error(500,'Security error: '.$e->getMessage(),$e);
    }
    catch (Exception404 $e) {
      $this->show_error(404,$e->getMessage(),$e);
    }
    catch (\Exception $e) {
      $this->show_error(500,'General error: '.$e->getMessage(),$e);
    }
  }

  function show_error(int $code,string $text,\Exception $e=null) {
    if (class_exists('Layouts\\ErrorPage')) {
      $errpage = new Layouts\ErrorPage(500);
      $errpage->wrap($e);
    }
    else {
      // $error_strings = [4]
      // header(); // TODO: error code output
      print $text;
    }
  }
}